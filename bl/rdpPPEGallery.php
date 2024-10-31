<?php if ( ! defined('WP_CONTENT_DIR')) exit('No direct script access allowed'); ?>
<?php 

if ( ! class_exists('RDP_PEDIAPRESS_EMBED_GALLERY') ) :
class RDP_PEDIAPRESS_EMBED_GALLERY {
    public $_options; // GLOBAL Options 
    public $_version;  
    private $_atts;
    private $_content;
    
    public function __construct($version,$options){
        $this->_version = $version;
        $this->_options = $options;        
    }//__construct  

    public function shortcode($atts,$content = null){
        $nGUID = uniqid();
        $sHTML = '';        
        $atts = shortcode_atts( array(
            'col' => '2',
            'num' => '10',
            'cat' => '',
            'tag' => '',
            'open_new' => '0',
            'size' => 'small',
            'sort_col' => 'post_title',
            'sort_dir' => 'ASC',
            'full_title' => (isset($this->_options['full_title']))? intval($this->_options['full_title']): 0,
            'image_show' => (isset($this->_options['image_show']))? intval($this->_options['image_show']): 1,
            'title_show' => (isset($this->_options['title_show']))? intval($this->_options['title_show']): 1,
            'subtitle_show' => (isset($this->_options['subtitle_show']))? intval($this->_options['subtitle_show']): 1,
            'editor_show' => (isset($this->_options['editor_show']))? intval($this->_options['editor_show']): 1,
            'language_show' => (isset($this->_options['language_show']))? intval($this->_options['language_show']): 1,
            'book_size_show' => (isset($this->_options['book_size_show']))? intval($this->_options['book_size_show']): 1,
             ), $atts );    
        
        if(intval($atts['col']) == 0)return $sHTML;
        if(intval($atts['num']) == 0)return $sHTML;
        if($atts['full_title'] == 1){
            $atts['subtitle_show'] = 0;
        }
        
        $this->_atts = $atts;
        $this->_content = $content;        

        $termIDs = '';
        if(!empty($atts['cat'])){
            $termIDs = $atts['cat'];
        }
        if(!empty($atts['tag'])){
            if(!empty($termIDs))$termIDs .= ',';
            $termIDs .= $atts['tag'];
        }  

        global $wpdb;
        $sCountSQL = $this->buildCountSQL($termIDs);
        $nRecordCount = $wpdb->get_var( $sCountSQL );
        if(empty($nRecordCount))return $sHTML;
        $totalPages = ceil((int)$nRecordCount/(int)$atts['num']);        
        $paged = $this->getCurrentPage($totalPages);
        
        $start = ($paged - 1)*(int)$atts['num'];
        $sFetchSQL = $this->buildFetchSQL($termIDs, $start, $atts['num'],$atts['sort_col'],$atts['sort_dir']);
        $rows = $wpdb->get_results($sFetchSQL);
        
        $sHTML .= '<div class="rdp_ppe_book_gallery rdp_ppe_book_gallery_'. strtolower($atts['size']) .' rdp_ppe_book_gallery-'. $nGUID .'">';
        $sHTML .= $this->renderGallery($rows, (int)$atts['col'], $atts,$nGUID);
        $sHTML .= '<div id="rdp_ppe_gallery_footer">';
        $sHTML .= $this->renderPaging($paged, $totalPages);
        $sRSSLink = $this->buildRSSLink($atts);
        $sHTML .= '<a class="rdp-ppe-gallery-rss" target="_new" href="' . $sRSSLink . '">';
        $sHTML .= '<img class="rdp-ppe-gallery-rss" src="' . RDP_PEDIAPRESS_EMBED_PLUGIN_BASEURL . '/pl/style/images/rss-icon.png" />';
        $sHTML .= '</a>';
        $sHTML .= '</div><!-- #rdp_ppe_gallery_footer -->';
        $sHTML .= '</div><!-- .rdp_ppe_book_gallery -->';        

        add_action('wp_footer', array(&$this,'scriptsEnqueue'));

        return $sHTML;        
    }//shortcode  
    
    private function getCurrentPage($totalPages) {
        $currentPage = RDP_PEDIAPRESS_EMBED_UTILITIES::globalRequest('txtRDP_PPE_Gallery_Current_Page','1');
        $paged = $currentPage;

        if(isset($_POST['ddRDP_PPE_Gallery_Page_Select'])) $paged = intval($_POST['ddRDP_PPE_Gallery_Page_Select']);
        if(isset($_POST['btnRDP_PPE_Gallery_First'])) $paged = 1;
        if(isset($_POST['btnRDP_PPE_Gallery_Last'])) $paged = (int)$totalPages;   
        if(isset($_POST['btnRDP_PPE_Gallery_Previous'])) $paged = (int)$currentPage - 1;
        if(isset($_POST['btnRDP_PPE_Gallery_Next'])) $paged = (int)$currentPage + 1;
        if($paged < 1)$paged = 1;
        if($paged > $totalPages)$paged = $totalPages; 
        return  $paged;
    }//getCurrentPage
    
    public function scriptsEnqueue(){
        $this->handleScripts($this->_atts, $this->_content);
    }//scriptsEnqueue
    
    private function handleScripts($atts,$content = null){
        wp_register_style( 'rdp-ppe-style-common',RDP_PEDIAPRESS_EMBED_PLUGIN_BASEURL . '/pl/style/pediapress.common.css' );
	wp_enqueue_style( 'rdp-ppe-style-common' );

        $filename = get_stylesheet_directory() . '/rdp-ppe.custom.css';
        if (file_exists($filename)) {
            wp_register_style( 'rdp-ppe-style-custom',get_stylesheet_directory_uri() . '/rdp-ppe.custom.css',array('rdp-ppe-style-common' ) );
            wp_enqueue_style( 'rdp-ppe-style-custom' );
        } 

        do_action('rdp_ppe_gallery_scripts_enqueued',$atts, $content);        
    }//handleScripts    
    
    private function renderGallery($rows,$cols,$atts,$nGUID){
        $sHTML = '<form class="rdp-ppe-gallery-form" method="post" action="">';
        $nCols = (count($rows) < $cols)? count($rows) : $cols ;
        $nCols = max($nCols,1);
        $width = floor(100/$nCols)-1.5;
        $sClass = '';
        $nCounter = 0;

        foreach($rows as $row):
            $contentPieces = unserialize($row->option_value);
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
            
            $sHTML .= '<div id="rdp_ppe_gallery_box-' . $row->ID . '" class="rdp_ppe_gallery_box rdp_ppe_gallery_col-'. $nCounter % $cols .'">';
            $sGalleryItem = '<div class="rdp-ppe-gallery-item rdp-ppe-gallery-item-' . $row->ID . '">';
            
            if($atts['image_show'] == 1 && !empty($contentPieces['cover_img_src'])):
                $sGalleryItem .= '<p class="rdp-ppe-cover-image-container">';
                $sGalleryItem .= '<a id="rdp-ppe-cover-link-' . $row->ID . '" href="' . $sPostLink . '" class="rdp-ppe-cover-link" data-post-id="' . $row->ID . '"'; 
                if(!empty($atts['open_new']))$sGalleryItem .= ' target="_new"';
                $sGalleryItem .= '>'; 
                $sGalleryItem .= '<img class="rdp-ppe-cover-image" src="' . $sImgSrc . '" alt="' . $sTitle . '" border="0" onerror="this.style.display=\'none\'" />';            
                $sGalleryItem .= '</a>';            
                $sGalleryItem .= '</p>';                 
            endif;

            $sGalleryItem .= '<div class="rdp-ppe-gallery-metadata-container">'; 
            
            if($atts['title_show'] == 1){
                $sGalleryItem .= '<p class="rdp-ppe-title-container meta">';            
                $sGalleryItem .= '<span class="rdp-ppe-title">';            
                $sGalleryItem .= '<a id="rdp-ppe-title-link-' . $row->ID . '" href="' . $sPostLink . '" class="rdp-ppe-title-link" data-post-id="' . $row->ID . '"';            
                if(!empty($atts['open_new']))$sGalleryItem .= ' target="_new"';
                $sGalleryItem .= '>';                 
                $sGalleryItem .=  ($atts['full_title'] == 1)? $FullTitle : $sTitle;            
                $sGalleryItem .= '</a>';            
                $sGalleryItem .= '</span>';            
                $sGalleryItem .= '</p>';                 
            }

            if($atts['subtitle_show'] == 1 && !empty($contentPieces['subtitle'])) $sGalleryItem .= '<p class="rdp-ppe-subtitle-container meta"><b>Subtitle:</b><br><span class="rdp-ppe-subtitle">' . $sSubtitle . '</span></p> ';            
            if($atts['editor_show'] == 1 && !empty($contentPieces['editor'])) $sGalleryItem .= '<p class="rdp-ppe-editor-container meta"><b>Editor:</b><br><span class="rdp-ppe-editor">' . $sEditor . '</span></p>';            
            if($atts['language_show'] == 1 && !empty($contentPieces['language'])) $sGalleryItem .= '<p class="rdp-ppe-language-container meta"><b>Language:</b><br><span class="rdp-ppe-language">' . $sLanguage . '</span></p>';            
            if($atts['book_size_show'] == 1 && !empty($contentPieces['book_size'])) $sGalleryItem .= '<p class="rdp-ppe-book-size-container meta"><b>Book Size:</b><br><span class="rdp-ppe-book-size">' . $sBookSize . '</span></p>';            
            $sGalleryItem .= '</div>';            
            $sGalleryItem .= '</div>';            
            $sGalleryItem .= '<div class="clear"></div>';            
           
            
            $sHTML .= apply_filters('rdp_ppe_gallery_item', 
                    $sGalleryItem, 
                    array ( 
                    $contentPieces['link'], 
                    $sImgSrc, 
                    $sTitle, 
                    $sSubtitle, 
                    $sEditor,
                    $sLanguage,
                    $sPriceCurrency,
                    $sPriceAmount,
                    $row->ID,
                    $FullTitle,
                    $sPostLink,
                    $sBookSize));
            $sHTML .= '<input type="hidden" id="rdp-ppe-src-' . $row->ID . '" value="' . $contentPieces['link'] . '" />';
            $sHTML .= '<input type="hidden" class="rdp-ppe-shortcode" id="rdp-ppe-shortcode-' . $row->ID . '" value="' . $row->post_content . '" />';
            $sHTML .= '</div><!-- .rdp_ppe_gallery_box -->';
            $nCounter++;
            if($nCounter == count($rows))$sClass = ' last';
            if ($nCounter % $cols === 0) {
                $sHTML .= '<div class="clear rdp-ppe-gallery-row-sep'.$sClass.'"></div>';
            }
        endforeach;
        if ($nCounter % $cols !== 0)$sHTML .= '<div class="clear rdp-ppe-gallery-row-sep last"></div>';
        
        
        $sHTML .= '<style type="text/css">';
        $sHTML .= 'div.rdp_ppe_book_gallery-'.$nGUID.' div.rdp_ppe_gallery_box{width: '. $width . '%;}';
        $sHTML .= '</style>'; 
        $sHTML .= '</form>';
        return $sHTML;
    }//renderGallery
    
    private function buildRSSLink($atts){
        $sURL = trailingslashit( get_bloginfo('url') );
        $params = array();
        if(!empty($atts['cat']))$params['cat'] = $atts['cat'];
        if(!empty($atts['tag'])){
            $tagIDs = explode(',', $atts['tag']);
            $params['tag'] = '';
            foreach($tagIDs as $termID){
                $oTerm = get_term_by('id', $termID, 'post_tag');
                if(!empty($oTerm)){
                    if(strlen($params['tag']) > 0) $params['tag'] .= ',';
                    $params['tag'] .= $oTerm->slug;
                }
            }            
        }
        $params['feed'] = 'pediapress_rss';
        $sURL = add_query_arg($params,$sURL);
        return $sURL;
    }//buildRSSLink
    
    private function renderPaging($paged,$totalPages){
        if($totalPages < 2)return '';
        
        $sHTML = '<form class="rdp-ppe-paging-form" method="post" action="">'; 
        $sHTML .= '<div id="rdp-ppe-paging-controls" class="rdp-ppe-paging-controls"><div class="wrap">';
        $sHTML .= '<input type="submit"';
        if($paged == 1)$sHTML .= ' disabled="disabled" ';
        $sHTML .= 'name="btnRDP_PPE_Gallery_First" class="rdp-ppe-paging-link rdp-ppe-paging-first" pg="1" title="First Page" value="<<"/>';
        $sHTML .= '<input type="submit"';
        if($paged == 1)$sHTML .= ' disabled="disabled" ';        
        $sHTML .= 'name="btnRDP_PPE_Gallery_Previous" class="rdp-ppe-paging-link rdp-ppe-paging-previous" title="Previous Page" value="<" />';

        $sHTML .= ' <span class="rdp-ppe-page-select-wrap rdp-ppe-page-select-wrap"><span id="rdp-ppe-page-select-label"></span><select name="ddRDP_PPE_Gallery_Page_Select" class="rdp-ppe-gallery-page-select" onchange="this.form.submit()">';

        for ($x = 1; $x <= $totalPages; $x++) {
            $sHTML .= '<option value="'.$x.'" '. selected($x, $paged,FALSE) .'>'.$x.'</option>';
        } 

        $sHTML .= '</select> <span> of ' . $totalPages . '</span></span> ';
        $sHTML .= '<input type="submit"';
        if($paged == $totalPages)$sHTML .= ' disabled="disabled" ';
        $sHTML .= 'name="btnRDP_PPE_Gallery_Next" class="rdp-ppe-paging-link rdp-ppe-paging-next" title="Next Page" value=">" />';
        $sHTML .= '<input type="submit"';
        if($paged == $totalPages)$sHTML .= ' disabled="disabled" ';
        $sHTML .= 'name="btnRDP_PPE_Gallery_Last" class="rdp-ppe-paging-link rdp-ppe-paging-last" title="Last Page" value=">>">';
        
        $sHTML .= '</div><!-- wrap --></div><!-- .rdp-ppe-paging-controls -->';       
        $sHTML .= '<input type="hidden" name="txtRDP_PPE_Gallery_Current_Page" value="' . $paged . '" />';
        $sHTML .= '</form>';
        return $sHTML;      
    }//renderPaging    
    

    private function buildCountSQL($termIDs){
        global $wpdb;
        $sSQL = '';
        
        if(!empty($termIDs)):
        $sSQL = <<<EOS
SELECT COUNT(*) record_count
FROM (SELECT p.ID, pm.meta_value
FROM {$wpdb->posts} p, {$wpdb->postmeta} pm
WHERE p.ID = pm.post_id
AND pm.meta_key = '_rdp-ppe-cache-key'
AND p.post_status = 'publish'
AND p.post_type  In ('post','page')
AND p.ID IN
(SELECT DISTINCT object_id 
FROM {$wpdb->term_relationships}
WHERE term_taxonomy_id IN 
(SELECT term_taxonomy_id FROM {$wpdb->term_taxonomy} WHERE term_id IN ({$termIDs}))))p,
{$wpdb->options} o 
WHERE o.option_name = p.meta_value;   
   
EOS;
    else:
        $sSQL = <<<EOS
SELECT COUNT(*) record_count
FROM (SELECT p.ID, pm.meta_value
FROM {$wpdb->posts} p, {$wpdb->postmeta} pm
WHERE p.ID = pm.post_id
AND pm.meta_key = '_rdp-ppe-cache-key'
AND p.post_status = 'publish'
AND p.post_type  In ('post','page'))p,
{$wpdb->options} o 
WHERE o.option_name = p.meta_value;   
   
EOS;

    endif;
        
        return $sSQL;
    }//buildCountSQL    
    
    public static function buildFetchSQL($termIDs, $start, $rowCount, $orderCol = 'post_title', $orderAttr = 'ASC'){
        global $wpdb;
        $sSQL = '';
        
        if(!empty($termIDs)):        
        $sSQL = <<<EOS
SELECT p.*,o.*
FROM (SELECT p.ID, p.post_content, p.post_title, p.post_excerpt, p.post_date_gmt post_date, pm.meta_value
FROM {$wpdb->posts} p, {$wpdb->postmeta} pm
WHERE p.ID = pm.post_id
AND pm.meta_key = '_rdp-ppe-cache-key'
AND p.post_status = 'publish'
AND p.post_type In ('post','page')
AND p.ID IN
(SELECT DISTINCT object_id 
FROM {$wpdb->term_relationships}
WHERE term_taxonomy_id IN 
(SELECT term_taxonomy_id FROM {$wpdb->term_taxonomy} WHERE term_id IN ({$termIDs}))))p,
{$wpdb->options} o 
WHERE o.option_name = p.meta_value
ORDER BY p.{$orderCol} {$orderAttr} LIMIT {$start}, {$rowCount};   
EOS;
    else:
        $sSQL = <<<EOS
SELECT p.*,o.*
FROM (SELECT p.ID, p.post_content, p.post_title, p.post_excerpt, p.post_date_gmt post_date, pm.meta_value
FROM {$wpdb->posts} p, {$wpdb->postmeta} pm
WHERE p.ID = pm.post_id
AND pm.meta_key = '_rdp-ppe-cache-key'
AND p.post_status = 'publish'
AND p.post_type In ('post','page'))p,
{$wpdb->options} o 
WHERE o.option_name = p.meta_value   
ORDER BY p.{$orderCol} {$orderAttr} LIMIT {$start}, {$rowCount};   
EOS;

    endif;
        
        return $sSQL;        
    }//buildFetchSQL  
    
    
    public function syndicateShortcode($atts) {
        if(!is_array($atts))return '';
        $sURL = $atts['url'];
        $oURLPieces = parse_url($sURL);
        if(empty($oURLPieces['scheme']))$oURLPieces['scheme'] = 'http';        
        $sSourceDomain = $oURLPieces['scheme'].'://'.$oURLPieces['host']; 
        if(isset($oURLPieces['path']))$sSourceDomain .= $oURLPieces['path'];
        $queryPieces = array();
        
        if($oURLPieces && isset($oURLPieces['query'])){
            $query = $oURLPieces['query'];
            parse_str($query,$queryPieces);
        } 
        
        
        $src = $sSourceDomain . 'pediapress-gallery/syndicate/';        
        $params = array_merge($atts,$queryPieces);
        $params['url'] = false;
        $params['feed'] = false;

        $feed_url = add_query_arg($params,$src);

        $response = wp_remote_post( $feed_url, array(
                'method' => 'POST',
                'timeout' => 45,
                'redirection' => 5,
                'httpversion' => '1.0',
                'blocking' => true,
                'headers' => array(),
                'body' => $_POST,
                'cookies' => array()
            )
        );        
        if ( is_wp_error( $response ) ) {
           $error_message = $response->get_error_message();
           return "Something went wrong: $error_message";
        } else {
            $data = wp_remote_retrieve_body($response);
            return $data;
        }        
    }//syndicateShortcode
    
    public function syndicate(){
        $query = $_SERVER['QUERY_STRING'];
        parse_str($query,$queryPieces);
        $queryPieces['open_new'] = '1';
        
        $content = $this->shortcode($queryPieces);
        echo $content;
        die();
    }//syndicate
    
}//RDP_PEDIAPRESS_EMBED_GALLERY
endif;
/* EOF */