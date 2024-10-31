<?php if ( ! defined('WP_CONTENT_DIR')) exit('No direct script access allowed'); ?>
<?php 

class RDP_PEDIAPRESS_EMBED_RSS {
    private $_version = '';
    private $_options = array();

    function  __construct($version,$options){
        $this->_version = $version;
        $this->_options = $options; 
    }//__construct
    
    function customRSSFunc(){
        $termIDs = '';
        $catNames = array();
        $tagNames = array();
        global $wp_query;

        foreach($wp_query->tax_query->queries as $taxQuery){
            switch ($taxQuery['field']) {
                case 'term_id':
                    foreach($taxQuery['terms'] as $termID){
                        $oTerm = get_term_by('id', $termID, $taxQuery['taxonomy']);
                        if(!empty($oTerm)){
                            if(strlen($termIDs) > 0) $termIDs .= ',';
                            $termIDs.= $termID;
                            if($taxQuery['taxonomy'] == 'category')$catNames[] = $oTerm->name;
                            if($taxQuery['taxonomy'] == 'post_tag')$tagNames[] = $oTerm->name;
                        }
                    }

                    break;
                case 'slug':
                    foreach($taxQuery['terms'] as $termSlug){
                        $oTerm = get_term_by('slug', $termSlug, $taxQuery['taxonomy']);
                        if(!empty($oTerm)){
                            if(strlen($termIDs) > 0) $termIDs .= ',';
                            $termIDs .= $oTerm->term_id;
                            if($taxQuery['taxonomy'] == 'category')$catNames[] = $oTerm->name;
                            if($taxQuery['taxonomy'] == 'post_tag')$tagNames[] = $oTerm->name;                        
                        }
                    }
                    break;
                case 'name':
                    foreach($taxQuery['terms'] as $termName){
                        $oTerm = get_term_by('name', $termName, $taxQuery['taxonomy']);
                        if(!empty($oTerm)){
                            if(strlen($termIDs) > 0) $termIDs .= ',';
                            $termIDs .= $oTerm->term_id;
                            if($taxQuery['taxonomy'] == 'category')$catNames[] = $oTerm->name;
                            if($taxQuery['taxonomy'] == 'post_tag')$tagNames[] = $oTerm->name;                        
                        }
                    }
                    break;                

                default:
                    break;
            }

        }

        header('Content-Type: '.feed_content_type('rss-http').'; charset='.get_option('blog_charset'), true);
        $sRSS = '<?xml version="1.0" encoding="'.get_option('blog_charset').'"?'.'>'; 
        $sRSS .= '<rss version="2.0"><channel>';
        $sChannelTitle = get_bloginfo('name') . ' PediaPress Feed';
        $nCatNames = count($catNames);
        $sChannelDescription = 'PediaPress Feed';

        if($nCatNames):
            switch ($nCatNames) {
                case 1:
                    $sChannelDescription .= " for the " . $catNames[0] . " Category" ;
                    break;
                default:
                    $sChannelDescription .= " for the " . implode(', ', $catNames) . " Categories" ;
                    break;
            }    
        endif;

        $nTagNames = count($tagNames);
        if($nCatNames && $nTagNames)$sChannelDescription .= ' And/Or';

        if($nTagNames):
            switch ($nTagNames) {
                case 1:
                    $sChannelDescription .= " for the "  . $tagNames[0] ." Tag";
                    break;
                default:
                    $sChannelDescription .= " for the " . implode(', ', $tagNames) . " Tags" ;        
                    break;
            }    
        endif;


        $sRSS .= "<title><![CDATA[$sChannelTitle]]></title>";
        $Path=$_SERVER['REQUEST_URI'];
        $URI=site_url().$Path;
        $sRSS .= "<link><![CDATA[{$URI}]]></link>";
        $sRSS .= "<description>{$sChannelDescription}</description>";
        $ESTTZ = new DateTimeZone('America/New_York');
        $d1=new DateTime();
        $d1->setTimezone($ESTTZ);
        $pubDate = $d1->format(DateTime::RSS);
        $sRSS .= "<pubDate>{$pubDate}</pubDate>";

        $description = <<<EOD
    <div id="rdp-ppe-rss-%%PostID%%" class="rdp-ppe-rss-box">   
    <div>
        <p style="float: left;margin: 0 6px 6px 0" class="cover-image-container">
            <a id="rdp-ppe-cover-link-%%PostID%%" href="%%PostLink%%" class="rdp-ppe-cover-link" postid="%%PostID%%">
                <img class="coverImage" src="%%Image%%" alt="%%Title%%" border="0" width="118" height="174" onerror="this.style.display='none'" />
            </a>
        </p>
    <div class="rdp-ppe-rss-metadata-container" style="min-height: 180px;">
        <p class="rdp-ppe-title-container meta" style="font-size: 12px;line-height: normal;margin: 0px 3px 3px 0px;padding: 0px;"><span class="rdp-ppe-title">%%FullTitle%%</span></p>
        <p class="rdp-ppe-editor-container meta" style="font-size: 12px;line-height: normal;margin: 0px 3px 3px 0px;padding: 0px;"><b>Editor:</b><br><span class="rdp-ppe-editor">%%Editor%%</span></p>
        <p class="rdp-ppe-language-container meta" style="font-size: 12px;line-height: normal;margin: 0px 3px 3px 0px;padding: 0px;"><b>Language:</b><br><span class="rdp-ppe-language">%%Language%%</span></p>    
        <p class="rdp-ppe-book-size-container meta" style="font-size: 12px;line-height: normal;margin: 0px 3px 3px 0px;padding: 0px;"><b>Book Size:</b><br><span class="rdp-ppe-book-size">%%BookSize%%</span></p>
    </div>
    </div>
    <input type="hidden" class="rdp-ppe-shortcode" value="%%Content%%">
    </div><!-- .rdp-ppe-rss-box -->
    <div class="clear "rdp-ppe-rss-row-sep" style="height: 2px;background: none;"></div>
                
EOD;

        foreach($wp_query->posts as $row):
            $sKEY = get_post_meta($row->ID, '_rdp-ppe-cache-key',true);
            $contentPieces = get_option( $sKEY );
            if($contentPieces === false) continue;            
            $sRSS .= '<item>';
            $sImgSrc = (!empty($contentPieces['cover_img_src']))? $contentPieces['cover_img_src'] : '';
            $sTitle = (!empty($contentPieces['title']))? $contentPieces['title'] : '';
            $sSubtitle = (!empty($contentPieces['subtitle']))? $contentPieces['subtitle'] : '';
            $FullTitle = (!empty($contentPieces['subtitle']))? $sTitle . ': ' . $sSubtitle : $sTitle;
            $sEditor = (!empty($contentPieces['editor']))? $contentPieces['editor'] : '';
            $sLanguage = (!empty($contentPieces['language']))? $contentPieces['language'] : '';
            $sPriceCurrency = (!empty($contentPieces['price_currency']))? $contentPieces['price_currency'] : '';
            $sPriceAmount = (!empty($contentPieces['price_amount']))? $contentPieces['price_amount'] : '';
            $sBookSize = (!empty($contentPieces['book_size']))? $contentPieces['book_size'] : '';

            $sPostLink = get_permalink($row->ID);
            $sExcerpt = wp_trim_words( $row->post_excerpt, 40, '&hellip; <a href="'. $sPostLink .'">Read More</a>' );
            $title = self::entitiesPlain($row->post_title);

            $sRSS .= "<title><![CDATA[{$title}]]></title>";
            $sRSS .= "<link><![CDATA[{$sPostLink}]]></link>";
            $sRSS .= "<guid isPermaLink='true'><![CDATA[{$sPostLink}]]></guid>"; 

            $sGalleryItem = str_replace (array ( 
                '%%Image%%', 
                '%%Title%%' , 
                '%%Subtitle%%' , 
                '%%Editor%%',
                '%%Language%%',
                '%%PriceCurrency%%',
                '%%PriceAmount%%',
                '%%PostID%%',
                '%%Excerpt%%',
                '%%FullTitle%%',
                '%%PostLink%%',
                '%%BookSize%%',
                '%%Content%%') , 
                array ( 
                $sImgSrc, 
                $sTitle, 
                $sSubtitle, 
                $sEditor,
                $sLanguage,
                $sPriceCurrency,
                $sPriceAmount,
                $row->ID,
                $sExcerpt,
                $FullTitle,
                $sPostLink,
                $sBookSize,
                $row->post_content), 
                $description );
            $sGalleryItem = self::entitiesPlain($sGalleryItem);
            $sRSS .= "<description><![CDATA[{$sGalleryItem}]]></description>";

            $d1=new DateTime($row->post_date);
            $d1->setTimezone($ESTTZ);
            $pubDate = $d1->format(DateTime::RSS);
            $sRSS .= "<pubDate>{$pubDate}</pubDate>";
            $sRSS .= '</item>';        
        endforeach;
        $sRSS .= '</channel>';
        $sRSS .= '</rss>';
        echo $sRSS;
        exit;
    }//customRSSFunc

    static function entitiesPlain($string){
        return str_replace ( array ( '&amp;' , '&quot;', '&apos;' , '&lt;' , '&gt;', '&quest;',  '&#39;' ), array ( '&', '"', "'", '<', '>', '?', "'" ), $string ); 
    }    
}//RDP_PEDIAPRESS_EMBED__RSS
