<?php if ( ! defined('WP_CONTENT_DIR')) exit('No direct script access allowed'); ?>
<?php

class RDP_PEDIAPRESS_EMBED {
    public static $postMetaKey = '_rdp-ppe-cache-key';
    private $_version = '';
    private $_options = array();
    private $_default_settings = array();
    const baseURL = 'https://pediapress.com';

    function  __construct($version,$options){
        $this->_version = $version;
        $this->_options = $options; 
        $this->_default_settings = RDP_PEDIAPRESS_EMBED_PLUGIN::default_settings();
    }//__construct
    
    public function shortcode($atts,$content = null){
        $fDoShortcode = apply_filters('rdp_ppe_allow_shortcode', true, $atts, $content);
        if(!$fDoShortcode)return '';
        
        $url = (isset($atts['url']))? $atts['url'] : '' ;
        
        // Remove all illegal characters from a url
        $url = filter_var($url, FILTER_SANITIZE_URL);
        
        // Validate url
        if (filter_var($url, FILTER_VALIDATE_URL) === false){
            return RDP_PEDIAPRESS_EMBED_UTILITIES::showMessage('ERROR: Not a valid PediaPress URL.', true, false);
        }        
        
        if (strpos($url, 'pediapress.com') === false) {
            return RDP_PEDIAPRESS_EMBED_UTILITIES::showMessage('ERROR: Not a PediaPress URL.', true, false);
        }
        
        $downloadURL = (isset($atts['download_url']))? $atts['download_url'] : '' ;
        if(!empty($downloadURL)){
          $cachedDownloadURL = get_post_meta(get_the_ID(), 'limsbook_url', true);
          if($downloadURL && $downloadURL != $cachedDownloadURL){
              // Remove all illegal characters from a url
              $url = filter_var($downloadURL, FILTER_SANITIZE_URL);            
              // Validate url
              if (filter_var($downloadURL, FILTER_VALIDATE_URL) === false){
                  return RDP_PEDIAPRESS_EMBED_UTILITIES::showMessage('ERROR: Not a valid download URL.', true, false);
              }  
              update_post_meta(get_the_ID(), 'limsbook_url', $downloadURL);            
          }           
        }        
        
        
        $contentPieces = RDP_PEDIAPRESS_EMBED_CONTENT::fetch($url); 
       
        $atts = shortcode_atts( array(
        'url' => '',
        'download_url' => '',
        'image_show' => (isset($this->_options['image_show']))? intval($this->_options['image_show']): 1,
        'title_show' => (isset($this->_options['title_show']))? intval($this->_options['title_show']): 1,
        'subtitle_show' => (isset($this->_options['subtitle_show']))? intval($this->_options['subtitle_show']): 1,
        'full_title' => (isset($this->_options['full_title']))? intval($this->_options['full_title']): 0,            
        'editor_show' => (isset($this->_options['editor_show']))? intval($this->_options['editor_show']): 1,
        'language_show' => (isset($this->_options['language_show']))? intval($this->_options['language_show']): 1,
        'add_to_cart_show' => (isset($this->_options['add_to_cart_show']))? intval($this->_options['add_to_cart_show']): 1,
        'add_to_cart_text' => RDP_PEDIAPRESS_EMBED_UTILITIES::rgar($this->_options, 'add_to_cart_text', $this->_default_settings['add_to_cart_text']),
        'add_to_cart_size' => RDP_PEDIAPRESS_EMBED_UTILITIES::rgar($this->_options, 'add_to_cart_size',$this->_default_settings['add_to_cart_size']),
        'add_to_cart_color' => RDP_PEDIAPRESS_EMBED_UTILITIES::rgar($this->_options, 'add_to_cart_color',$this->_default_settings['add_to_cart_color']),
        'book_size_show' => (isset($this->_options['book_size_show']))? intval($this->_options['book_size_show']): 1,
        'beneath_cover_content' => RDP_PEDIAPRESS_EMBED_UTILITIES::rgar($this->_options, 'beneath_cover_content',$this->_default_settings['beneath_cover_content']),
        'toc_show' => (isset($this->_options['toc_show']))? intval($this->_options['toc_show']): 1,
        'toc_links' => RDP_PEDIAPRESS_EMBED_UTILITIES::rgar($this->_options, 'toc_links', $this->_default_settings['toc_links']),
        'cta_button_content' => empty($content)? empty($this->_options['cta_button_content'])? '' : $this->_options['cta_button_content'] : $content,
        'cta_button_show' => (isset($this->_options['cta_button_show']))? intval($this->_options['cta_button_show']): 1,
        'cta_button_text' => RDP_PEDIAPRESS_EMBED_UTILITIES::rgar($this->_options, 'cta_button_text', $this->_default_settings['cta_button_text']),
        'cta_button_size' => RDP_PEDIAPRESS_EMBED_UTILITIES::rgar($this->_options, 'cta_button_size', $this->_default_settings['cta_button_size']),
        'cta_button_color' => RDP_PEDIAPRESS_EMBED_UTILITIES::rgar($this->_options, 'cta_button_color', $this->_default_settings['cta_button_color']),
         ), $atts );  
        
        if(!is_numeric($atts['toc_show']))$atts['toc_show'] = $this->_default_settings['toc_show'];
        if(!in_array( $atts['toc_links'], RDP_PEDIAPRESS_EMBED_PLUGIN::toc_settings() ))$atts['toc_links'] = $this->_default_settings['toc_links'];          
        if($atts['full_title'] == 1){
            $atts['subtitle_show'] = 0;
        }
        if(empty($downloadURL)){
            $atts['cta_button_show'] = 0;
        }         
        
        $this->handleShortcodeScripts($atts,$content);            
        return $this->renderContent($url, $atts, $contentPieces);
    } //shortcode
    
    private function handleShortcodeScripts($atts,$content = null){
        if(wp_script_is('rdp-ppe-overlay'))return;
        if(!wp_script_is('jquery-colorbox'))wp_enqueue_script( 'jquery-colorbox', plugins_url( 'js/jquery.colorbox.min.js',__FILE__),array("jquery"), "1.3.20.2", true );        
        
        wp_enqueue_script( 'rdp-ppe-overlay', plugins_url( 'js/pediapress-overlay.js',__FILE__),array("jquery"), $this->_version, true );        
        $params = array(
            'has_content' => 0,
            'links_active' => 1,
            'log_in_msg' => '',
            'logged_in' => (is_user_logged_in())?'true':'false'
            );  
        
        switch ($atts['toc_links']) {
            case 'logged-in':
                if(!is_user_logged_in()){
                    $params['links_active'] = 0;
                    $log_in_msg = RDP_PEDIAPRESS_EMBED_UTILITIES::rgar($this->_options, 'log_in_msg', $this->_default_settings['log_in_msg']);
                    $msg = RDP_PEDIAPRESS_EMBED_UTILITIES::showMessage($log_in_msg, true, false);
                    $params['log_in_msg'] = do_shortcode($msg);
                }
                break;
            case 'disabled':
                $params['links_active'] = 0;
                break;
            default:
                break;
        }          
        
        if(!empty($content)){
            $params['has_content'] = 1;
            wp_enqueue_style( 'rdp-ppe-admin-core-style', plugins_url( 'style/jquery-ui.css',__FILE__ ), null,'1.11.2' );            
            wp_enqueue_style( 'rdp-ppe-admin-theme-style', plugins_url( 'style/jquery-ui.theme.min.css',__FILE__ ), array('rdp-ppe-admin-core-style'),'1.11.2' );             
        } 
        wp_localize_script( 'rdp-ppe-main', 'rdp_ppe', $params );

        wp_register_style( 'rdp-ppe-style-atc-'.$atts['add_to_cart_color'], plugins_url( 'style/atc-' . $atts['add_to_cart_color'] .  '.css' , __FILE__ ), 'rdp-ppe-style-common');
	wp_enqueue_style( 'rdp-ppe-style-atc-'.$atts['add_to_cart_color'] );  
        
        wp_register_style( 'rdp-ppe-style-cta-'.$atts['cta_button_color'], plugins_url( 'style/cta-' . $atts['cta_button_color'] .  '.css' , __FILE__ ), 'rdp-ppe-style-common' );
	wp_enqueue_style( 'rdp-ppe-style-cta-'.$atts['cta_button_color'] );  

        wp_enqueue_style( 'rdp-ppe-colorbox-style', plugins_url( 'style/colorbox.css',__FILE__),false, "1.3.20.2", 'screen');        
        do_action('rdp_ppe_book_scripts_enqueued',$atts, $content);        
    }//handleScripts
    
    public function enqueueStylesScripts() {
        if(wp_script_is('rdp-ppe-main'))return;
        wp_enqueue_script( 'rdp-ppe-main', plugins_url( 'js/script.rdp-ppe.js' , __FILE__ ), array( 'jquery','jquery-query' ), '1.0', TRUE);
        
        wp_register_style( 'rdp-ppe-style-common', plugins_url( 'style/pediapress.common.css' , __FILE__ ) );
	wp_enqueue_style( 'rdp-ppe-style-common' );
        
        $filename = get_stylesheet_directory() . 'style/rdp-ppe.custom.css';
        if (file_exists($filename)) {
            wp_register_style( 'rdp-ppe-style-custom', plugins_url( 'style/rdp-ppe.custom.css' , __FILE__ ),array('rdp-ppe-style-common' ) );
            wp_enqueue_style( 'rdp-ppe-style-custom' );
        }     
        do_action('rdp_ppe_scripts_enqueued');
    }//enqueueStylesScripts
    
    private function renderContent($URL,&$atts,&$contentPieces){
        if(empty($contentPieces)){
            return RDP_PEDIAPRESS_EMBED_UTILITIES::showMessage('Error: Unable to retieve content from PediaPress', true, false);
        }
        if ($contentPieces['body_id'] == 'errorpage') {
            return RDP_PEDIAPRESS_EMBED_UTILITIES::showMessage('Error: Unable to retieve content from PediaPress', true, false);
        }
        $sHTML = '';
        $bodyID = $contentPieces['body_id'];

        switch ($bodyID) {
            case 'book_show':
                $sHTML .= $this->renderBook($URL,$contentPieces,$atts);
                break;
            default:
                break;
        }//switch ($bodyID)  
        
        
        return $sHTML;        
    }//renderContent
    
    private function renderBook($URL,&$contentPieces,&$atts) {
        $html = null;
        $sDownloadButton = '';
        $sInlineHTML = '';         
        $sClasses = '';
        if($atts['image_show'] == 0)$sClasses .= ' no-cover';
        if($atts['title_show'] == 0)$sClasses .= ' no-title';
        if($atts['subtitle_show'] == 0)$sClasses .= ' no-subtitle';
        if($atts['editor_show'] == 0)$sClasses .= ' no-editor';
        if($atts['language_show'] == 0)$sClasses .= ' no-language';
        if($atts['add_to_cart_show'] == 0)$sClasses .= ' no-add-to-cart';

        $sMainContentClasses = apply_filters('rdp_ppe_book_main_content_classes', $sClasses ) ;
        $sHTML = '<div id="rdp-ppe-main" class="book_show' . $sMainContentClasses . '">';
        $sHTML .= '<div class="wrap" style="clear: right;"><div class="s1 w4"><div id="coverPreviewArea" class="nico_18">';
        
        if($atts['image_show'] == 1 && !empty($contentPieces['cover_img_src'])):
            $sHTML .= '<div class="ready">';
            $sHTML .= '<img id="coverImage" src="' . $contentPieces['cover_img_src'] . '" alt="" style="width: 100%;max-width: 201px;height: auto"/>';
            $sHTML .= '</div><!-- .ready -->';
            if(!empty($atts['beneath_cover_content']))$sHTML .= '<div id="contentBeneathCover">' . $atts['beneath_cover_content'] . '</div>';            
        endif;


        $sHTML .= '</div><!-- #coverPreviewArea --></div><!-- .s1 .w4 -->';                
        
        $fIncludeMeta = $atts['title_show'] == 1 || $atts['subtitle_show'] == 1 || $atts['editor_show'] == 1 || $atts['language_show'] == 1 ;
        if($fIncludeMeta):
            $sTitle = (!empty($contentPieces['title']))? $contentPieces['title'] : '';
            $sSubtitle = (!empty($contentPieces['subtitle']))? $contentPieces['subtitle'] : '';            
            $FullTitle = (!empty($contentPieces['subtitle']))? $sTitle . ': ' . $sSubtitle : $sTitle;
            $metaHTML = apply_filters('rdp_ppe_before_meta_open', '',$contentPieces,$atts);
            $metaHTML .= '<div id="metadata" class="s0l w4">';
            $metaHTML = apply_filters('rdp_ppe_after_meta_open', $metaHTML,$contentPieces,$atts);
            
            if($atts['title_show'] == 1){
                $metaHTML .= '<p><label id="title-label">Title:</label><span id="title">';
                $metaHTML .= ($atts['full_title'] == 1)? $FullTitle : $sTitle;
                $metaHTML .= '</span></p>';
            }
            if($atts['subtitle_show'] == 1 && !empty($sSubtitle)) $metaHTML .= '<p><label id="subtitle-label">Subtitle:</label><span id="subtitle">' . $contentPieces['subtitle'] . '</span></p>';
            if($atts['editor_show'] == 1 && !empty($contentPieces['editor'])) $metaHTML .= '<p><label id="editor-label">Editor:</label><span id="editor">' . $contentPieces['editor'] . '</span></p>';
            if($atts['language_show'] == 1 && !empty($contentPieces['language'])) $metaHTML .= '<p><label id="language-label">Language:</label><span id="language">' . $contentPieces['language'] . '</span></p>';
            if($atts['book_size_show'] == 1 && !empty($contentPieces['book_size'])) $metaHTML .= '<p><label id="book-size-label">Book size:</label><span id="book-size">' . $contentPieces['book_size'] . '</span></p>';

            $sPriceCurrency = (!empty($contentPieces['price_currency']))? $contentPieces['price_currency'] : '';
            $sPriceAmount = (!empty($contentPieces['price_amount']))? $contentPieces['price_amount'] : '';
            $sAddToCartHREF = $URL ;
            $sAddToCartHREF = apply_filters('rdp_ppe_book_atc_href', $sAddToCartHREF, $URL, $contentPieces, $atts ) ;
            $sAddToCartText = "{$atts['add_to_cart_text']} - {$sPriceCurrency} {$sPriceAmount}";

            if($atts['add_to_cart_show'] == 1 ):
                $sATC = '<div id="rdp_ppe_add_to_cart_box" class="' . $atts['add_to_cart_size'] .'">';
                $sATC .= '<a href="' . $sAddToCartHREF . '" class="rdp_ppe_add_to_cart '.  $atts['add_to_cart_color'] . '  ' . $atts['add_to_cart_size'] .'" target="_new">' . $sAddToCartText . '</a>';
                $sATC .= '</div><!-- #rdp_ppe_add_to_cart_box -->'; 
                $metaHTML .= apply_filters('rdp_ppe_atc_button', $sATC , $contentPieces, $atts ) ;
            endif; 

            if($atts['cta_button_show'] == 1){
                if(!empty($atts['cta_button_content']) || !empty($atts['download_url'])){
    //                if($atts['add_to_cart_show'] == 1 )$sDownloadButton .= "<div class='rdp-ppe-inline-content-sep'>OR</div>";

                    if(!empty($atts['cta_button_content'])){
                        $sDownloadButton .= "<div id='rdp-ppe-cta-button-box' class='{$atts['cta_button_size']}'><a class='rdp-ppe-cta-button {$atts['cta_button_color']} {$atts['cta_button_size']}' href='#rdp_ppe_inline_content'>{$atts['cta_button_text']}</a></div>";
                        $metaHTML .= apply_filters('rdp_ppe_cta_button', $sDownloadButton, $contentPieces, $atts) ;
                        $sInlineHTML .= "<div id='rdp_ppe_inline_content_wrapper' style='display:none'><div id='rdp_ppe_inline_content' class='$sClasses'>";
                        $sInlineHTML .= '<div class="rdp_ppe_cta_button_content">';
                        $sInlineHTML .= do_shortcode($atts['cta_button_content']);
                        $sInlineHTML .= "</div><!-- .rdp_ppe_cta_button_content -->";                 
                        $sInlineHTML .= "</div><!-- #rdp_ppe_inline_content --></div>";                     
                    }else{
                        $sDownloadButton .= "<div id='rdp-ppe-cta-button-box' class='{$atts['cta_button_size']}'><a class='rdp-ppe-cta-button {$atts['cta_button_color']} {$atts['cta_button_size']}' href='{$atts['download_url']}'>{$atts['cta_button_text']}</a></div>";
                        $metaHTML .= apply_filters('rdp_ppe_cta_button', $sDownloadButton, $contentPieces, $atts) ;                   
                    }
                }                
            }

            if($atts['image_show'] == 0 && !empty($atts['beneath_cover_content']))$metaHTML .= '<div id="contentBeneathCover">' . $atts['beneath_cover_content'] . '</div>';
            $metaHTML = apply_filters('rdp_ppe_before_meta_close', $metaHTML, $contentPieces, $atts);
            $metaHTML .= '</div><!-- #metadata -->';
            $sHTML .= apply_filters('rdp_ppe_after_meta_close', $metaHTML, $contentPieces, $atts);
        endif;
        $sHTML .= '<div></div>';
        $sHTML .= '</div><!-- .wrap -->';
        
        if($atts['toc_show'] == 1):
            $tocHTML = '';
            $mainContent = rdp_str_get_html('<html><body>'.$contentPieces['toc'].'</body></html>');
            if($mainContent){
                $tocHTML .= '<div></div><h2>Table of Contents:</h2>';
                switch (strtolower($atts['toc_links'])) {
                    case 'logged-in':
                        if(!is_user_logged_in()):
                            foreach($mainContent->find('ul.outline li a') as $link){
                                $link->href = null;
                                $link->class = trim($link->class . ' rdp_ppe_must_log_in');
                            }
                        endif;
                        break;
                    case 'disabled':
                        foreach($mainContent->find('ul.outline li a') as $link){
                            $link->outertext = $link->innertext;
                        }
                        break;
                    default:
                        break;
                }
                $tocHTML .= $mainContent->find('ul.outline',0)->outertext;
                $mainContent->clear();
            }
            $sHTML .= apply_filters('rdp_ppe_toc', $tocHTML, $contentPieces, $atts);
        endif;

        $sHTML .= '</div><!-- #rdp-ppe-main -->';
        $sHTML .= $sInlineHTML;
        return apply_filters('rdp_ppe_render_book', $sHTML, $contentPieces, $atts);
    }//renderBook
    
}//RDP_PEDIAPRESS_EMBED

/* EOF */
