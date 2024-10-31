<?php if ( ! defined('WP_CONTENT_DIR')) exit('No direct script access allowed'); ?>
<?php

class RDP_PEDIAPRESS_EMBED_CONTENT {
    
    public static function fetch($URL){
        $sKEY = RDP_PEDIAPRESS_EMBED_UTILITIES::getKey($URL);
        if (false !== ( $special_query_results = get_option( $sKEY ) ) ) return $special_query_results;        
        update_post_meta(get_the_ID(), RDP_PEDIAPRESS_EMBED::$postMetaKey, $sKEY);
        return self::contentPieces_Get($sKEY, $URL);
    }//grabContentFromPediaPress
    
    private static function contentPieces_Get($sKEY, $URL) {
        $curl = curl_init();
        // Make the request
        curl_setopt($curl, CURLOPT_URL, $URL );
        curl_setopt($curl, CURLOPT_POST, false);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_COOKIEFILE, "/tmp/cookie.txt");
        curl_setopt($curl, CURLOPT_COOKIEJAR, "/tmp/cookie.txt");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($curl);
        if (FALSE === $response) throw new Exception(curl_error($curl), curl_errno($curl));
        curl_close($curl);
        if(file_exists('/tmp/cookie.txt')) unlink('/tmp/cookie.txt');
        
        $html = new rdp_simple_html_dom(); // Create new parser instance
        $html->load($response);   

//        $html = rdp_file_get_html($URL);
        if(!$html)return array();      
        $body = $html->find('body',0);
        $bodyID = 0;
        
        if($body){
            $bodyID = $html->find('body',0)->id; 
        }
         
        if(!$bodyID)return array();
        $contentPieces = array(
            'body_id' => $bodyID,
            'link' => $URL,
            'cover_img_src' => '',
            'price_currency' => '',
            'price_amount' => '',
            'title' => '',
            'subtitle' => '',
            'editor' => '',
            'language' => '',
            'toc' => '',
            'book_size' => ''
        );
        
        switch ($bodyID) {
            case 'book_show':
                self::contentPieces_Parse($contentPieces,$html);
                update_option( $sKEY, $contentPieces, 'no' );                
                break;
           
            default:
                break;
        }
        
        $html->clear(); 
        unset($html);
        return $contentPieces;
    }//contentPieces_Get
    
    private static function contentPieces_Parse(&$contentPieces,&$html) {
        $mainContent = $html->find('div#mainContent',0);  
        if(!$mainContent)return;
        $baseURL = 'https://pediapress.com';
        
        $priceCurrency = $mainContent->find('#price-currency',0);
        $contentPieces['price_currency'] = ($priceCurrency)? $priceCurrency->innertext : '';

        $priceAmount = $mainContent->find('#price-amount',0);
        $contentPieces['price_amount'] = ($priceAmount)? $priceAmount->innertext : '';

        $title = $mainContent->find('#title',0);
        $contentPieces['title'] = ($title)? $title->innertext : '';  
        
        
        $metaData = $mainContent->find('#metadata',0);
        if($metaData){
            $metaParas = $metaData->find('p');
            for ($i = 0; $i<=count($metaParas)-1;$i++ ){
                $innerText = $metaParas[$i]->innertext;
                if(strpos($innerText, 'Subtitle:') !== false){
                    $contentPieces['subtitle'] = trim(substr($innerText, strpos($innerText, '>')+1)); 
                    continue;
                }

                if(strpos($innerText, 'Editor:') !== false){
                    $contentPieces['editor'] = trim(substr($innerText, strpos($innerText, '>')+1)); 
                    continue;
                }

                if(strpos($innerText, 'Language:') !== false){
                    $contentPieces['language'] = trim(substr($innerText, strpos($innerText, '>')+1)); 
                    continue;
                }                

            }                
        }        
        
        $coverImage = $mainContent->find('#coverImage',0);
        if($coverImage){
            $imgSrc = $baseURL . $coverImage->src;
            $imgName = '' ;
            $sExt = '';

            $rawImage = wp_remote_get($imgSrc);
            if( !is_wp_error( $rawImage ) ) {
                $sContentType = (isset($rawImage['headers']['content-type']))? $rawImage['headers']['content-type'] : '' ;
                $mimeType = explode(';',$sContentType);
                switch ($mimeType[0]) {
                    case 'image/jpeg':
                        $sExt = '.jpg';
                        break;
                    case 'image/gif':
                        $sExt = '.gif';
                        break;
                    case 'image/png':
                        $sExt = '.png';
                        break;
                    case 'image/tiff':
                        $sExt = '.tif';
                        break;
                    default:
                        break;
                }
            }

            if(!empty($sExt)){
                $upload_dir = wp_upload_dir();
                $imgName = sanitize_title($contentPieces['title'] . ' ' . $contentPieces['subtitle']) . $sExt;
                if(!file_exists(RDP_PEDIAPRESS_EMBED_IMG_DIR))mkdir(RDP_PEDIAPRESS_EMBED_IMG_DIR, 0755);
                $imgCacheSrc = RDP_PEDIAPRESS_EMBED_IMG_DIR . '/' . $imgName;
                if(!file_exists($imgCacheSrc)){
                    $fp = fopen($imgCacheSrc, 'x');
                    fwrite($fp, $rawImage['body']); // save the full image
                    fclose($fp);                    
                }   
                $contentPieces['cover_img_src'] =  RDP_PEDIAPRESS_EMBED_IMG_URL . $imgName;                    
            }else{
                $contentPieces['cover_img_src'] = $imgSrc;
            }
        }  
        
        $form = $mainContent->find('#bookopts-form',0);
        if($form){
            $formParas = $form->find('p');
            for ($i = 0; $i<=count($metaParas)-1;$i++ ){
                $innerText = $formParas[$i]->innertext;
                if(strpos($innerText, 'Book size:') !== false){
                    $contentPieces['book_size'] = trim(substr($innerText, strrpos($innerText, '>')+1)); 
                    continue;
                }                    
            }
        }        
        
        $toc = $mainContent->find('ul.outline',0);
        if($toc){
            foreach($toc->find('a') as $link){
                $link->title = $link->innertext;
                $link->rel="noindex, nofollow";
            }                
        }
        $contentPieces['toc'] = ($toc)? $toc->outertext : '';            
       
    }//contentPieces_Fetch    
}//RDP_PEDIAPRESS_EMBED_CONTENT
