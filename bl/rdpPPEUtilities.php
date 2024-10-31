<?php if ( ! defined('WP_CONTENT_DIR')) exit('No direct script access allowed'); ?>
<?php

class RDP_PEDIAPRESS_EMBED_UTILITIES {
    static function isFeedRequest(){
        $rv = false;
        
        $slug = 'feed/pediapress_rss';
        $uri = $_SERVER['REQUEST_URI'];
        if(strpos($uri, $slug) !== false)$rv = true; 
        
        $sParam = self::globalRequest('feed');
        if($sParam === 'pediapress_rss')$rv = true;
        
        return $rv;
    }//isFeedRequest
    
    static function abortExecution(){
        $rv = false;
        $wp_action = RDP_PEDIAPRESS_EMBED_UTILITIES::globalRequest('action');
        if($wp_action == 'heartbeat')$rv = true;
        $isScriptStyleImg = RDP_PEDIAPRESS_EMBED_UTILITIES::isScriptStyleImgRequest();
        if($isScriptStyleImg)$rv = true;           
        return $rv;
    }//abortExecution
    
    static function isScriptStyleImgRequest(){
        $url = (isset($_SERVER['REQUEST_URI']))? $_SERVER['REQUEST_URI'] : '';        
        $imgExts = array("js", "css","gif", "jpg", "jpeg", "png", "tiff", "tif", "bmp");
        $url_parts = parse_url($url);        
        $path = (empty($url_parts["path"]))? '' : $url_parts["path"];
        $urlExt = pathinfo($path, PATHINFO_EXTENSION);
        return in_array($urlExt, $imgExts);
    }//isScriptStyleImgRequest 
    
    static function unXMLEntities($string) { 
       return str_replace (array ( '&amp;' , '&quot;', '&apos;' , '&lt;' , '&gt;' ) , array ( '&', '"', "'", '<', '>' ), $string ); 
    } 
    
    static function showMessage($message, $errormsg = false,$echo = true)
    {
        $sMSG = '';
        if( $errormsg )
        {
            $sMSG .= '<div id="rdp_ppe_message" class="alert error">';
        }
        else
        {
            $sMSG .= '<div id="rdp_ppe_message" class="alert success updated">';
        }

        $sMSG .= "<p><strong>$message</strong></p></div>";
        
        if($echo){
            echo $sMSG;
        }else{
            return $sMSG;
        }
    }//showMessage   
    
    static function getKey($url) {
        $sKEY = '_rdp_ppe_book_' . md5(esc_url( $url ));
        return $sKEY;
    }//getKey
    
    static function pluginIsActive($input){
        $active = get_option('active_plugins');
        $active = implode(",", $active);
        $rv = false;
        switch ($input){
            case "we": 
                if (strpos($active, "wiki-embed")) $rv = true;
                break;
        }//switch
        
       return $rv; 
    }//PluginIsActive  
    
    public static function globalRequest( $name, $default = '' ) {
        $RV = '';
        $array = $_GET;

        if ( isset( $array[ $name ] ) ) {
                $RV = $array[ $name ];
        }else{
            $array = $_POST;
            if ( isset( $array[ $name ] ) ) {
                    $RV = $array[ $name ];
            }                
        }
        
        if(empty($RV) && !empty($default)) return $default;
        return $RV;
    }  

    public static function rgempty( $name, $array = null ) {
        if ( is_array( $name ) ) {
                return empty( $name );
        }

        if ( ! $array ) {
                $array = $_POST;
        }

        $val = self::rgar( $array, $name );

        return empty( $val );
    }//rgempty
    
    public static function rgget( $name, $array = null ) {
        if ( ! isset( $array ) ) {
                $array = $_GET;
        }

        if ( isset( $array[ $name ] ) ) {
                return $array[ $name ];
        }

        return '';
    }    

    public static function rgpost( $name, $do_stripslashes = true ) {
        if ( isset( $_POST[ $name ] ) ) {
                return $do_stripslashes ? stripslashes_deep( $_POST[ $name ] ) : $_POST[ $name ];
        }

        return '';
    }    
    
    public static function rgars( $array, $name ) {
            $names = explode( '/', $name );
            $val   = $array;
            foreach ( $names as $current_name ) {
                    $val = rgar( $val, $current_name );
            }

            return $val;
    }

    public static function rgar( $array, $prop, $default = null ) {

            if ( isset( $array[ $prop ] ) ) {
                    $value = $array[ $prop ];
            } else {
                    $value = '';
            }

            return empty( $value ) && $default !== null ? $default : $value;
    }
}//RDP_PEDIAPRESS_EMBED_UTILITIES

/* EOF */
