<?php if ( ! defined('WP_CONTENT_DIR')) exit('No direct script access allowed'); ?>
<?php

class RDP_PEDIAPRESS_EMBED_ADMIN_MENU {
    
    public $_options; // GLOBAL Options 
    public $_version;  

    
    public function __construct($version,$options){
        $this->_version = $version;
        $this->_options = $options;        
    }//__construct 
    
    
    public function enqueueStylesScripts() {
        if(wp_script_is('rdp-ppe-shortcode'))return;
        wp_enqueue_style( 'rdp-ppe-admin-style', plugins_url( 'style/pediapress.admin.css',__FILE__ ), array('rdp-ppe-admin-theme-style','thickbox'),$this->_version );         
        wp_enqueue_style( 'rdp-ppe-admin-core-style', plugins_url( 'style/jquery-ui.css',__FILE__ ),null ,'1.11.2' );            
        wp_enqueue_style( 'rdp-ppe-admin-theme-style', plugins_url( 'style/jquery-ui.theme.min.css',__FILE__ ), array('rdp-ppe-admin-core-style'),'1.11.2' ); 
        wp_enqueue_style( 'rdp-ppe-tabs-style', plugins_url( 'style/tabs.css',__FILE__ ), array('rdp-ppe-admin-theme-style'),$this->_version );         
        
        wp_enqueue_script('rdp-ppe-shortcode',plugins_url('js/script.shortcode-popup.js', __FILE__), array('jquery', "jquery-ui-tabs"), $this->_version, true ); 
        $params = array(
            'settings' => $this->_options
        );
        wp_localize_script( 'rdp-ppe-shortcode', 'rdp_ppe_shortcode', $params );

//        if(!wp_script_is('jquery-url'))wp_enqueue_script('jquery-url',plugins_url('js/url.min.js', __FILE__), array('jquery','rdp-ppe-shortcode'));          
        
    }//enqueueStylesScripts
    
    /*------------------------------------------------------------------------------
    Add admin menu
    ------------------------------------------------------------------------------*/
    static function add_menu_item(){
        if ( !current_user_can('activate_plugins') ) return;
        add_options_page( 'RDP PediaPress Embed', 'RDP PPE', 'manage_options', 'rdp-pediapress-embed', 'RDP_PEDIAPRESS_EMBED_ADMIN_MENU::generate_page' );
    } //add_menu_item    
    
    /*------------------------------------------------------------------------------
    Render settings page
    ------------------------------------------------------------------------------*/
    static function generate_page(){  
        $rv = self::handlePostback();
        
	echo '<div class="wrap">';
        echo '<h2>RDP PediaPress Embed</h2>';
        
        if($rv['message']){
            printf ('<div id="message" class="%s is-dismissible">', $rv['status']);
            printf('<p>%s</p>', $rv['message']);
            echo '<button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>';
            echo '</div>';               
        }
        
        echo '<form method="post">';
        $sLabel = esc_attr__('Clear PediaPress Cache', 'rdp-pediapress-embed');
        submit_button( $sLabel, 'secondary', 'btnClearPPECache', false );
        echo '</form>';

        echo '<form action="options.php" method="post">';
        settings_fields('rdp_pediapress_embed_options');
        do_settings_sections('rdp-pediapress-embed'); 
        submit_button();
        echo '</form>';
    }//generate_page
    
    private static function handlePostback() {
        global $wpdb; 
        $rv = array(
            'message' => '',
            'status' => 'notice-success notice'
        );       
        if(isset($_POST['btnClearPPECache'])){
            $sSQL = "DELETE FROM $wpdb->options WHERE option_name LIKE '%_rdp_ppe_book_%';";
            $wpdb->query($sSQL);
            $sSQL = sprintf("DELETE FROM %s WHERE meta_key LIKE '%s';",$wpdb->postmeta,RDP_PEDIAPRESS_EMBED::$postMetaKey);
            $wpdb->query($sSQL); 
            $sPath = RDP_PEDIAPRESS_EMBED_IMG_DIR . '/*';
            $files = glob($sPath); // get all file names
            foreach($files as $file){ // iterate files
              if(is_file($file))
                unlink($file); // delete file
            }
            $rv['message'] = 'PediaPress cache cleared.';
        } 
        
        return $rv;        
    }//handlePostback
    
    static function admin_page_init(){
        if ( !current_user_can('activate_plugins') ) return;
        //Add settings link to plugins page
        add_filter('plugin_action_links', array('RDP_PEDIAPRESS_EMBED_ADMIN_MENU', 'add_settings_link'), 10, 2);
        
        register_setting(
            'rdp_pediapress_embed_options',
            'rdp_pediapress_embed_options',
            'RDP_PEDIAPRESS_EMBED_ADMIN_MENU::options_validate'
        );    
        
        add_settings_section(
            'rdp_ppe_main',
            esc_html__('Default Shortcode Settings','rdp-pediapress-embed'),
            'RDP_PEDIAPRESS_EMBED_ADMIN_MENU::main_section_text',
            'rdp-pediapress-embed'
	);  
        add_settings_field(
            'image_show',
            esc_html__( 'Show Cover Image:','rdp-pediapress-embed'),
            array('RDP_PEDIAPRESS_EMBED_ADMIN_MENU', 'image_show_input'),
            'rdp-pediapress-embed',
            'rdp_ppe_main'
        );  
        add_settings_field(
            'title_show',
            esc_html__( 'Show Title:','rdp-pediapress-embed'),
            array('RDP_PEDIAPRESS_EMBED_ADMIN_MENU', 'title_show_input'),
            'rdp-pediapress-embed',
            'rdp_ppe_main'
        );  
        add_settings_field(
            'subtitle_show',
            esc_html__( 'Show Subtitle:','rdp-pediapress-embed'),
            array('RDP_PEDIAPRESS_EMBED_ADMIN_MENU', 'subtitle_show_input'),
            'rdp-pediapress-embed',
            'rdp_ppe_main'
        );     
        add_settings_field(
            'full_title',
            esc_html__( 'Use Full Title:','rdp-pediapress-embed'),
            array('RDP_PEDIAPRESS_EMBED_ADMIN_MENU', 'full_title_show_input'),
            'rdp-pediapress-embed',
            'rdp_ppe_main'
        );  
        add_settings_field(
            'editor_show',
            esc_html__( 'Show Editor:','rdp-pediapress-embed'),
            array('RDP_PEDIAPRESS_EMBED_ADMIN_MENU', 'editor_show_input'),
            'rdp-pediapress-embed',
            'rdp_ppe_main'
        );        
        add_settings_field(
            'language_show',
            esc_html__( 'Show Language:','rdp-pediapress-embed'),
            array('RDP_PEDIAPRESS_EMBED_ADMIN_MENU', 'language_show_input'),
            'rdp-pediapress-embed',
            'rdp_ppe_main'
        );        
        add_settings_field(
            'book_size',
            esc_html__( 'Show Book Size:','rdp-pediapress-embed'),
            array('RDP_PEDIAPRESS_EMBED_ADMIN_MENU', 'book_size_show_input'),
            'rdp-pediapress-embed',
            'rdp_ppe_main'
        );            
        add_settings_field(
            'toc_show',
            esc_html__( 'Show TOC:','rdp-pediapress-embed'),
            array('RDP_PEDIAPRESS_EMBED_ADMIN_MENU', 'toc_show_input'),
            'rdp-pediapress-embed',
            'rdp_ppe_main'
        );  
        add_settings_field(
            'toc_links',
            esc_html__( 'TOC Links:','rdp-pediapress-embed'),
            array('RDP_PEDIAPRESS_EMBED_ADMIN_MENU', 'toc_links_input'),
            'rdp-pediapress-embed',
            'rdp_ppe_main'
        );  
        add_settings_field(
            'cta_button_text',
            esc_html__( 'Call-to-Action Button:','rdp-pediapress-embed'),
            array('RDP_PEDIAPRESS_EMBED_ADMIN_MENU', 'cta_button_input'),
            'rdp-pediapress-embed',
            'rdp_ppe_main'
        );         
        add_settings_field(
            'atc_button_text',
            esc_html__( 'Add-to-Cart Button:','rdp-pediapress-embed'),
            array('RDP_PEDIAPRESS_EMBED_ADMIN_MENU', 'atc_button_input'),
            'rdp-pediapress-embed',
            'rdp_ppe_main'
        );   
        add_settings_field(
            'beneath_cover_content',
            esc_html__('Beneath Cover Content','rdp-pediapress-embed'),
            array('RDP_PEDIAPRESS_EMBED_ADMIN_MENU', 'beneath_cover_content_input'),
            'rdp-pediapress-embed',
            'rdp_ppe_main'
        );        
        
        add_settings_section(
            'rdp_ppe_rss',
            esc_html__('PediaPress RSS','rdp-pediapress-embed'),
            'RDP_PEDIAPRESS_EMBED_ADMIN_MENU::rss_section_text',
            'rdp-pediapress-embed'
	);     
        add_settings_field(
            'books_per_rss',
            esc_html__('Max number of RSS items','rdp-pediapress-embed'),
            array('RDP_PEDIAPRESS_EMBED_ADMIN_MENU', 'books_per_rss_input'),
            'rdp-pediapress-embed',
            'rdp_ppe_rss'
        );        
        
    }//admin_page_init
    
    static function books_per_rss_input() {
        $default_settings = RDP_PEDIAPRESS_EMBED_PLUGIN::default_settings();
        $options = get_option( 'rdp_pediapress_embed_options' );
        $sBooksPerRSS = ( isset( $options['books_per_rss'] ) && intval($options['books_per_rss']) > 0 ) ? $options['books_per_rss'] : $default_settings['books_per_rss'];        
        echo '<input name="rdp_pediapress_embed_options[books_per_rss]" type="number" step="1" min="1" value="' . esc_attr($sBooksPerRSS) . '" class="small-text"/>';
    }//books_per_rss_input


    static function atc_button_input() {
        $options = get_option( 'rdp_pediapress_embed_options' );
        $default_settings = RDP_PEDIAPRESS_EMBED_PLUGIN::default_settings();
        
        // show button
        echo '<span style="width: 150px;display: inline-block;">';
        esc_html_e('Show','rdp-pediapress-embed');
        echo ':</span> ';
        $value = isset($options['add_to_cart_show'])? $options['add_to_cart_show'] : $default_settings['add_to_cart_show'];
        $value = intval($value);        
        echo '<input type="checkbox" value="1" name="rdp_pediapress_embed_options[add_to_cart_show]" ' . checked( $value , 1, false) . '/> ';        
        echo '<br />';
        
        // button text
        echo '<span style="width: 150px;display: inline-block;">';
        esc_html_e('Text','rdp-pediapress-embed');
        echo ':</span> ';
        $value = empty($options['add_to_cart_text'])? $default_settings['add_to_cart_text'] : $options['add_to_cart_text'];
        $value = esc_attr($value);        
        echo '<input type="text" name="rdp_pediapress_embed_options[add_to_cart_text]" value="' . $value  . '" style="width: 250px;" />';
        echo '<br />';
        
        // button size
        echo '<span style="width: 150px;display: inline-block;">';
        esc_html_e('Size','rdp-pediapress-embed');
        echo ':</span> ';        
        $value = ( isset( $options['add_to_cart_size'] ) ) ? $options['add_to_cart_size'] : $default_settings['add_to_cart_size'];
        echo '<select name="rdp_pediapress_embed_options[add_to_cart_size]">';
        foreach(RDP_PEDIAPRESS_EMBED_PLUGIN::button_sizes() as $size){
            echo sprintf('<option value="%s" %s>%s</option>',$size,selected($value,$size,false), ucwords($size) );
        }
        echo '</select>';
        echo '<br />';
        
        // button color
        echo '<span style="width: 150px;display: inline-block;">';
        esc_html_e('Color','rdp-pediapress-embed');
        echo ':</span> ';  
        $value = ( isset( $options['add_to_cart_color'] ) ) ? $options['add_to_cart_color'] : $default_settings['add_to_cart_color'];
        echo '<select name="rdp_pediapress_embed_options[add_to_cart_color]">';
        foreach(RDP_PEDIAPRESS_EMBED_PLUGIN::button_colors() as $color){
            echo sprintf('<option value="%s" %s>%s</option>',$color,selected($value,$color,false), ucwords($color) );
        }
        echo '</select>';         
    }//atc_button_input  
    
    static function cta_button_input() {
        $options = get_option( 'rdp_pediapress_embed_options' );
        $default_settings = RDP_PEDIAPRESS_EMBED_PLUGIN::default_settings();
        
        // show button
        echo '<span style="width: 150px;display: inline-block;">';
        esc_html_e('Show','rdp-pediapress-embed');
        echo ':</span> ';
        $value = isset($options['cta_button_show'])? $options['cta_button_show'] : $default_settings['cta_button_show'];
        $value = intval($value);        
        echo '<input type="checkbox" value="1" name="rdp_pediapress_embed_options[cta_button_show]" ' . checked( $value , 1, false) . '/> ';        
        echo '<br />';
        
        // button text
        echo '<span style="width: 150px;display: inline-block;">';
        esc_html_e('Text','rdp-pediapress-embed');
        echo ':</span> ';
        $sPPDownloadButtonText = empty($options['cta_button_text'])? $default_settings['cta_button_text'] : $options['cta_button_text'];
        $sPPDownloadButtonText = esc_attr($sPPDownloadButtonText);        
        echo '<input type="text" name="rdp_pediapress_embed_options[cta_button_text]" value="' . $sPPDownloadButtonText  . '" style="width: 250px;" />';
        echo '<br />';
        
        // button size
        echo '<span style="width: 150px;display: inline-block;">';
        esc_html_e('Size','rdp-pediapress-embed');
        echo ':</span> ';        
        $value = ( isset( $options['cta_button_size'] ) ) ? $options['cta_button_size'] : $default_settings['cta_button_size'];
        echo '<select name="rdp_pediapress_embed_options[cta_button_size]">';
        foreach(RDP_PEDIAPRESS_EMBED_PLUGIN::button_sizes() as $size){
            echo sprintf('<option value="%s" %s>%s</option>',$size,selected($value,$size,false), ucwords($size) );
        }
        echo '</select>';
        echo '<br />';
        
        // button color
        echo '<span style="width: 150px;display: inline-block;">';
        esc_html_e('Color','rdp-pediapress-embed');
        echo ':</span> ';  
        $value = ( isset( $options['cta_button_color'] ) ) ? $options['cta_button_color'] : $default_settings['cta_button_color'];
        echo '<select name="rdp_pediapress_embed_options[cta_button_color]">';
        foreach(RDP_PEDIAPRESS_EMBED_PLUGIN::button_colors() as $color){
            echo sprintf('<option value="%s" %s>%s</option>',$color,selected($value,$color,false), ucwords($color) );
        }
        echo '</select>';  
        echo '<br />';

        // popup content
        echo '<span class="alignleft" style="width: 150px;display: inline-block;">';
        esc_html_e('Popup Content','rdp-pediapress-embed');
        echo ':</span> '; 
        echo '<span class="alignleft" style="max-width: 380px;margin: 2px 0 0 5px;">';
        esc_html_e('Text, HTML, and/or another shortcode to display in lightbox popup when the Call-to-action button is clicked','rdp-pediapress-embed');
        echo '</span>';
        $text_string = isset($options['cta_button_content'])? $options['cta_button_content'] : $default_settings['cta_button_content'];
        $text_string = esc_textarea($text_string);
        echo '<textarea name="rdp_pediapress_embed_options[cta_button_content]" rows="10" cols="50" style="margin: 2px 0 0 152px;">';
        echo $text_string;
        echo '</textarea>';
        
        
    }//cta_button_input
    
    static function toc_links_input() {
        $options = get_option( 'rdp_pediapress_embed_options' );
        $default_settings = RDP_PEDIAPRESS_EMBED_PLUGIN::default_settings();
        
        $value = isset($options['toc_links'])? $options['toc_links'] : $default_settings['toc_links'];
        echo '<label><input name="rdp_pediapress_embed_options[toc_links]" type="radio" value="enabled" ' . checked($value,"enabled",false) . ' /> ';
        esc_html_e('Enabled &mdash; TOC links are enabled','rdp-pediapress-embed');
        echo '</label>';
        echo '<br />';
        echo '<label><input name="rdp_pediapress_embed_options[toc_links]" type="radio" value="logged-in" ' . checked($value,"logged-in",false). ' /> ';
        esc_html_e('Logged-in &mdash; TOC links are active only when a user is logged in','rdp-pediapress-embed');
        echo '</label>';
        echo '<br />';  
        
        $sLabel = __('Text, HTML, and/or shortcode to display when a <b>non-logged-in person</b> clicks a TOC link.', 'rdp-pediapress-embed');
        $sLabel2 = __('An empty SPAN element will display a notification icon.', 'rdp-pediapress-embed');
        echo sprintf('<p>%s<br />%s</p>', $sLabel, $sLabel2);        
        $log_in_msg = isset($options['log_in_msg'])? $options['log_in_msg'] : $default_settings['log_in_msg'];
        $log_in_msg = esc_textarea($log_in_msg);
        echo '<textarea name="rdp_pediapress_embed_options[log_in_msg]"  rows="10" cols="50">' . $log_in_msg . '</textarea>';        
        
        
        echo '<br />';
        echo '<label><input name="rdp_pediapress_embed_options[toc_links]" type="radio" value="disabled" ' . checked($value,"disabled",false) . ' /> ';
        esc_html_e('Disabled &mdash; TOC links are completely disabled, all the time','rdp-pediapress-embed');
        echo '</label>'; 
    }//toc_links_input
    
    static function toc_show_input() {
        $options = get_option( 'rdp_pediapress_embed_options' );
        $default_settings = RDP_PEDIAPRESS_EMBED_PLUGIN::default_settings();
        $value = isset($options['toc_show'])? $options['toc_show'] : $default_settings['toc_show'];
        $value = intval($value);
        echo '<lable>';
        echo '<input type="checkbox" value="1" name="rdp_pediapress_embed_options[toc_show]" ' . checked( $value , 1, false) . '/> ';
        esc_html_e('Display Table of Contents for PediaPress books','rdp-pediapress-embed');
        echo '</lable>';
    }//toc_show_input
    
    static function image_show_input() {
        $options = get_option( 'rdp_pediapress_embed_options' );
        $default_settings = RDP_PEDIAPRESS_EMBED_PLUGIN::default_settings();
        $value = isset($options['image_show'])? $options['image_show'] : $default_settings['image_show'];
        $value = intval($value);
        echo '<lable>';
        echo '<input type="checkbox" value="1" name="rdp_pediapress_embed_options[image_show]" ' . checked( $value , 1, false) . '/> ';
        esc_html_e('Display cover photo for PediaPress books','rdp-pediapress-embed');
        echo '</lable>';
    }//image_show_input
    
    static function title_show_input() {
        $options = get_option( 'rdp_pediapress_embed_options' );
        $default_settings = RDP_PEDIAPRESS_EMBED_PLUGIN::default_settings();
        $value = isset($options['title_show'])? $options['title_show'] : $default_settings['title_show'];
        $value = intval($value);
        echo '<lable>';
        echo '<input type="checkbox" value="1" name="rdp_pediapress_embed_options[title_show]" ' . checked( $value , 1, false) . '/> ';
        esc_html_e('Display title for PediaPress books','rdp-pediapress-embed');
        echo '</lable>';
    }//title_show_input    
    
    static function subtitle_show_input() {
        $options = get_option( 'rdp_pediapress_embed_options' );
        $default_settings = RDP_PEDIAPRESS_EMBED_PLUGIN::default_settings();
        $value = isset($options['subtitle_show'])? $options['subtitle_show'] : $default_settings['subtitle_show'];
        $value = intval($value);
        echo '<lable>';
        echo '<input type="checkbox" value="1" name="rdp_pediapress_embed_options[subtitle_show]" ' . checked( $value , 1, false) . '/> ';
        esc_html_e('Display subtitle for PediaPress books','rdp-pediapress-embed');
        echo '</lable>';
    }//subtitle_show_input 
    
    static function full_title_show_input() {
        $options = get_option( 'rdp_pediapress_embed_options' );
        $default_settings = RDP_PEDIAPRESS_EMBED_PLUGIN::default_settings();
        $value = isset($options['full_title'])? $options['full_title'] : $default_settings['full_title'];
        $value = intval($value);
        echo '<lable>';
        echo '<input type="checkbox" value="1" name="rdp_pediapress_embed_options[full_title]" ' . checked( $value , 1, false) . '/> ';
        esc_html_e('Display book titles as combination of Title and Subtitle','rdp-pediapress-embed');
        echo '</lable>';
    }//subtitle_show_input     
    
    static function editor_show_input() {
        $options = get_option( 'rdp_pediapress_embed_options' );
        $default_settings = RDP_PEDIAPRESS_EMBED_PLUGIN::default_settings();
        $value = isset($options['editor_show'])? $options['editor_show'] : $default_settings['editor_show'];
        $value = intval($value);
        echo '<lable>';
        echo '<input type="checkbox" value="1" name="rdp_pediapress_embed_options[editor_show]" ' . checked( $value , 1, false) . '/> ';
        esc_html_e('Display editor for PediaPress books','rdp-pediapress-embed');
        echo '</lable>';
    }//editor_show_input  
    
    static function language_show_input() {
        $options = get_option( 'rdp_pediapress_embed_options' );
        $default_settings = RDP_PEDIAPRESS_EMBED_PLUGIN::default_settings();
        $value = isset($options['language_show'])? $options['language_show'] : $default_settings['language_show'];
        $value = intval($value);
        echo '<lable>';
        echo '<input type="checkbox" value="1" name="rdp_pediapress_embed_options[language_show]" ' . checked( $value , 1, false) . '/> ';
        esc_html_e('Display language of PediaPress books','rdp-pediapress-embed');
        echo '</lable>';
    }//language_show_input 
    
    static function book_size_show_input() {
        $options = get_option( 'rdp_pediapress_embed_options' );
        $default_settings = RDP_PEDIAPRESS_EMBED_PLUGIN::default_settings();
        $value = isset($options['book_size_show'])? $options['book_size_show'] : $default_settings['book_size_show'];
        $value = intval($value);
        echo '<lable>';
        echo '<input type="checkbox" value="1" name="rdp_pediapress_embed_options[book_size_show]" ' . checked( $value , 1, false) . '/> ';
        esc_html_e('Display page count of PediaPress books','rdp-pediapress-embed');
        echo '</lable>';
    }//book_size_show_input      
    
    static function beneath_cover_content_input(){
        $default_settings = RDP_PEDIAPRESS_EMBED_PLUGIN::default_settings();
        $sLabel = esc_attr__('Content to Insert Beneath PediaPress Book Cover Image', 'rdp-pediapress-embed');
        $options = get_option( 'rdp_pediapress_embed_options' );
        $text_string = isset($options['beneath_cover_content'])? $options['beneath_cover_content'] : $default_settings['beneath_cover_content'];
        $text_string = esc_textarea($text_string);
        echo '<span class="alignleft">' . $sLabel . '</span><br />';
        echo '<textarea name="rdp_pediapress_embed_options[beneath_cover_content]"  rows="10" cols="50">' . $text_string . '</textarea>';
    } //Clear_Cache_Input  
    
    
    
    /*------------------------------------------------------------------------------
    Validate incoming data
    ------------------------------------------------------------------------------*/
    static function options_validate($input){
        $default_settings = RDP_PEDIAPRESS_EMBED_PLUGIN::default_settings();
 	$options = array(
                'beneath_cover_content' => (isset($input['beneath_cover_content'])? $input['beneath_cover_content'] : $default_settings['beneath_cover_content'] ),
                'books_per_rss'         => (isset($input['books_per_rss']) && intval($input['books_per_rss']) > 0 ? $input['books_per_rss'] : $default_settings['books_per_rss'] ),
                'image_show'            => (isset( $input['image_show']) && $input['image_show'] == 1 ? 1 : 0 ),
                'title_show'            => (isset( $input['title_show']) && $input['title_show'] == 1 ? 1 : 0 ),
                'subtitle_show'         => (isset( $input['subtitle_show']) && $input['subtitle_show'] == 1 ? 1 : 0 ),
                'full_title'            => (isset( $input['full_title']) && $input['full_title'] == 1 ? 1 : 0 ),
                'editor_show'           => (isset( $input['editor_show']) && $input['editor_show'] == 1 ? 1 : 0 ),
                'language_show'         => (isset( $input['language_show']) && $input['language_show'] == 1 ? 1 : 0 ),
                'book_size_show'        => (isset( $input['book_size_show']) && $input['book_size_show'] == 1 ? 1 : 0 ),
                'toc_show'              => (isset( $input['toc_show']) && $input['toc_show'] == 1 ? 1 : 0 ),
                'toc_links'             => (isset( $input['toc_links'])? $input['toc_links'] : $default_settings['toc_links'] ),
                'log_in_msg'            => (isset( $input['log_in_msg'])? $input['log_in_msg'] : $default_settings['log_in_msg'] ),
                'cta_button_text'       => (isset( $input['cta_button_text'])? $input['cta_button_text'] : $default_settings['cta_button_text'] ),
                'cta_button_content'    => (isset( $input['cta_button_content'])? $input['cta_button_content'] : $default_settings['cta_button_content'] ),
                'cta_button_size'       => (isset( $input['cta_button_size'])? $input['cta_button_size'] : $default_settings['cta_button_size'] ),
                'cta_button_color'      => (isset( $input['cta_button_color'])? $input['cta_button_color'] : $default_settings['cta_button_color'] ),
                'cta_button_show'       => (isset( $input['cta_button_show']) && $input['cta_button_show'] == 1 ? 1 : 0 ),
                'add_to_cart_text'      => (isset( $input['add_to_cart_text'])? $input['add_to_cart_text'] : $default_settings['add_to_cart_text'] ),
                'add_to_cart_size'      => (isset( $input['add_to_cart_size'])? $input['add_to_cart_size'] : $default_settings['add_to_cart_size'] ),
                'add_to_cart_color'     => (isset( $input['add_to_cart_color'])? $input['add_to_cart_color'] : $default_settings['add_to_cart_color'] ),
                'add_to_cart_show'      => (isset( $input['add_to_cart_show']) && $input['add_to_cart_show'] == 1 ? 1 : 0 )
            );
        return $options;
    } //options_validate    

    static function main_section_text() {
        echo '';
    }

    static function rss_section_text() {
        echo '<p>';
        esc_html_e('Your site has an RSS feed for all books on the site','rdp-pediapress-embed');
        echo ': ' . '<a href="' . get_bloginfo('url' ) . '/feed/pediapress_rss" target="_new">' . get_bloginfo('url' ) . '/feed/pediapress_rss</a></p>';
        echo '<p>';
        esc_html_e('You can provide book feeds to specific categories or tags on your site by using the following formats','rdp-pediapress-embed');
        echo ':</p>';
        echo '<p><b>' . get_bloginfo('url' ) . '/category/category-slug/feed/pediapress_rss</b></p>';
        echo '<p>';
        esc_html_e('Or','rdp-pediapress-embed');
        echo '</p>';
        echo '<p><b>' . get_bloginfo('url' ) . '/tag/tag-slug/feed/pediapress_rss</b></p>';
        echo '<p>';
        esc_html_e('Modify the above URLs by replacing category-slug and tag-slug with the actual slug of the desired category or tag.','rdp-pediapress-embed');
        echo '</p>';
    }
    
    /**
     * Add Settings link to plugins page
     */
    static function add_settings_link($links, $file){
        if ($file == RDP_PEDIAPRESS_EMBED_PLUGIN_BASENAME){
            $settings_link = '<a href="options-general.php?page=' . RDP_PEDIAPRESS_EMBED_PLUGIN::$plugin_slug . '">'.esc_html__("Settings", 'rdp-pediapress-embed').'</a>';
             array_unshift($links, $settings_link);
        }
        return $links;
     }    
    
}//RDP_PEDIAPRESS_EMBED_ADMIN_MENU
