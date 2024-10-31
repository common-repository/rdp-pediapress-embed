<?php

/*
Plugin Name: RDP PediaPress Embed
Plugin URI: http://robert-d-payne.com/
Description: Enables the inclusion of PediaPress book pages into your own blog page or post through the use of shortcodes.
Version: 1.0.5
Author: Robert D Payne
Author URI: http://robert-d-payne.com/
License: GPLv2 or later
*/

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

if ( ! defined( 'WPINC' ) ) {
	die;
}

// Turn off all error reporting
//error_reporting(E_ALL^ E_WARNING);
$dir = plugin_dir_path( __FILE__ );
define('RDP_PEDIAPRESS_EMBED_PLUGIN_BASEDIR', $dir);
define('RDP_PEDIAPRESS_EMBED_PLUGIN_BASEURL',plugins_url( null, __FILE__ ) );
define('RDP_PEDIAPRESS_EMBED_PLUGIN_BASENAME', plugin_basename(__FILE__));

$upload_dir = wp_upload_dir();
$imgCacheDir = $upload_dir['basedir'] . '/rdp-pediapress-embed';
define('RDP_PEDIAPRESS_EMBED_IMG_DIR', $imgCacheDir);
$imgCacheURL = $upload_dir['baseurl'] . '/rdp-pediapress-embed/';
define('RDP_PEDIAPRESS_EMBED_IMG_URL',$imgCacheURL);

define('RDP_PEDIAPRESS_EMBED_INT_MAX', 9223372036854775808);
include_once 'bl/rdpPPEUtilities.php';
include_once 'bl/rdpPPEGallery.php';
include_once 'bl/rdpPPE_RSS.php';
include_once 'bl/simple_html_dom.php';

class RDP_PEDIAPRESS_EMBED_PLUGIN {
    public static $plugin_slug = 'rdp-pediapress-embed';
    public static $options_name = 'rdp_pediapress_embed_options';
    public static $version = '1.0.4';
    private $_options = array();
    private $_instance = null;
    private $_gallery_instance = null;

    function __construct() {
        $options = get_option( RDP_PEDIAPRESS_EMBED_PLUGIN::$options_name );
        if(is_array($options))$this->_options = $options;
        $this->load_dependencies();
    }//__construct


    static function default_settings() {
        return array(
            'books_per_rss' => '10',
            'beneath_cover_content' => '',
            'image_show' => '1',
            'title_show' => '1',
            'subtitle_show' => '0',
            'full_title' => '1',
            'editor_show' => '1',
            'language_show' => '1',
            'add_to_cart_show' => '1',
            'add_to_cart_text' => 'Add to Cart',
            'add_to_cart_size' => 'small',
            'add_to_cart_color' => 'blue',
            'book_size_show' => '1',
            'toc_show' => '1',
            'toc_links' => 'disabled',
            'cta_button_show' => '1',
            'cta_button_text' => 'Download FREE eBook Edition',
            'cta_button_size' => 'medium',
            'cta_button_color' => 'orange',
            'cta_button_content' => '',
            'log_in_msg' => '<span></span> Please log in to read online.',
            'gallery_style' => 'small'
        );
    }//default_settings

    static function toc_settings(){
        return array('enabled','disabled','logged-in');
    }

    static function button_sizes() {
       return array('small','medium','large');
    }

    static function button_colors() {
        return array('blue','creme','grey','orange','red');
    }

    private function load_dependencies() {
        if (is_admin()){
            include_once 'pl/rdpPPEAdminMenu.php' ;
            include_once 'pl/rdpPPEShortcodePopup.php' ;
        }

        include_once 'bl/rdpPPEContent.php';
        include_once 'pl/rdpPPE.php' ;
    }//load_dependencies

    private function define_front_hooks(){
        if(defined( 'DOING_AJAX' ))return;
        if(is_admin())return;
        $this->_instance =  new RDP_PEDIAPRESS_EMBED(self::$version,$this->_options);
        add_shortcode('rdp-pediapress-embed', array(&$this->_instance, 'shortcode'));
        add_action( 'wp_enqueue_scripts', array(&$this->_instance, 'enqueueStylesScripts') );
        $this->_gallery_instance = new RDP_PEDIAPRESS_EMBED_GALLERY(self::$version,$this->_options);
        add_shortcode( 'rdp-pediapress-embed-gallery', array(&$this->_gallery_instance, 'shortcode') );
        add_shortcode( 'rdp-pediapress-embed-gallery-rss', array(&$this->_gallery_instance, 'syndicateShortcode') );
        
    }//define_front_hooks

    private function define_admin_hooks() {
        if(!is_admin())return;
        if(defined( 'DOING_AJAX' ))return;
        $oRDP_PEDIAPRESS_EMBED_ADMIN_MENU = new RDP_PEDIAPRESS_EMBED_ADMIN_MENU(self::$version,$this->_options);
        $oRDP_PEDIAPRESS_EMBED_ADMIN_MENU->enqueueStylesScripts();
        add_action( 'admin_footer', 'RDP_PEDIAPRESS_EMBED_SHORTCODE_POPUP::renderPopupForm' );
        add_action('admin_menu', 'RDP_PEDIAPRESS_EMBED_ADMIN_MENU::add_menu_item');
        add_action('admin_init', 'RDP_PEDIAPRESS_EMBED_ADMIN_MENU::admin_page_init');
        add_action( 'media_buttons', 'RDP_PEDIAPRESS_EMBED_SHORTCODE_POPUP::addMediaButton',1 );
    }//define_admin_hooks

    public function run() {
        $this->define_front_hooks();
        $this->define_admin_hooks();
    }//run

    public static function install(){
        //Ensure the $wp_rewrite global is loaded
        global $wp_rewrite;
        //Call flush_rules() as a method of the $wp_rewrite object
        $wp_rewrite->flush_rules( false );
    }

}//RDP_PEDIAPRESS_EMBED_PLUGIN

register_activation_hook( __FILE__, array( 'RDP_PEDIAPRESS_EMBED_PLUGIN', 'install' ) );

function rdp_pediapress_embed_run(){
    // prevent running code unnecessarily
    if(RDP_PEDIAPRESS_EMBED_UTILITIES::abortExecution())return;
    if(RDP_PEDIAPRESS_EMBED_UTILITIES::isFeedRequest() !== false)return;
    
    /* handle syndication requests */
    $uri = $_SERVER['REQUEST_URI']; 
    $slug = '/pediapress-gallery/syndicate';    
    $pos = strpos($uri, $slug);    
    if($pos !== false){
        $oRDP_PEDIAPRESS_EMBED_GALLERY = new RDP_PEDIAPRESS_EMBED_GALLERY(RDP_PEDIAPRESS_EMBED_PLUGIN::$version, get_option( RDP_PEDIAPRESS_EMBED_PLUGIN::$options_name ));
        $oRDP_PEDIAPRESS_EMBED_GALLERY->syndicate();
    }    
    
    
    $oRDP_PEDIAPRESS_EMBED_PLUGIN = new RDP_PEDIAPRESS_EMBED_PLUGIN();
    $oRDP_PEDIAPRESS_EMBED_PLUGIN->run();
}
add_action('wp_loaded','rdp_pediapress_embed_run');


function rdp_pediapress_rss(){
    // prevent running code unnecessarily
    if(RDP_PEDIAPRESS_EMBED_UTILITIES::abortExecution())return;
    if(RDP_PEDIAPRESS_EMBED_UTILITIES::isFeedRequest() === false)return;
    $oRDP_PEDIAPRESS_EMBED_RSS = new RDP_PEDIAPRESS_EMBED_RSS(RDP_PEDIAPRESS_EMBED_PLUGIN::$version, get_option( RDP_PEDIAPRESS_EMBED_PLUGIN::$options_name ));
    add_feed('pediapress_rss', array($oRDP_PEDIAPRESS_EMBED_RSS,'customRSSFunc'));
}
add_action('init', 'rdp_pediapress_rss');


function rdp_pediapress_custom_feed_rewrite($wp_rewrite) {
    $feed_rules = array(
    'feed/pediapress_rss' => 'index.php?feed=pediapress_rss',
    'category/(.+)/feed/pediapress_rss' => 'index.php?feed=pediapress_rss&category_name=' . $wp_rewrite->preg_index(1),        
    'tag/(.+)/feed/pediapress_rss' => 'index.php?feed=pediapress_rss&tag=' . $wp_rewrite->preg_index(1),        
    );
    $wp_rewrite->rules = $feed_rules + $wp_rewrite->rules;
}//rdp_pediapress_custom_feed_rewrite
add_filter('generate_rewrite_rules', 'rdp_pediapress_custom_feed_rewrite');

/* EOF */