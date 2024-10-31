<?php


class RDP_PEDIAPRESS_EMBED_SHORTCODE_POPUP {
    public static function addMediaButton($page = null, $target = null){
        global $pagenow;
        if ( !in_array( $pagenow, array( "post.php", "post-new.php" ) ))return;        
        $rdp_ppe_button_src = plugins_url('/style/images/pediapress.ico', __FILE__);
        $output_link = '<a href="#TB_inline?width=400&inlineId=rdp-ppe-shortcode-popup" class="thickbox button" title="RDP PediaPress Embed" id="rdp-ppe-shortcode-button">';
        $output_link .= '<span class="wp-media-buttons-icon" style="background: url('. $rdp_ppe_button_src.'); background-repeat: no-repeat; background-position: 0 0;"/></span>';
        $output_link .= '</a>';
        echo $output_link;
    }//addMediaButton  
    
    public static function renderPopupForm(){
        global $pagenow;
        if ( !in_array( $pagenow, array( "post.php", "post-new.php" ) ))return;        
        echo '<div id="rdp-ppe-shortcode-popup" style="display:none;">';
        echo '<div id="rdp-pediapress-embed-tabs" style="position: static;">';
        echo '<ul>';
        echo '<li><a href="#tabs-1">';
        _e('PediaPress Book', 'rdp-pediapress-embed');
        echo '</a></li>';
        echo '<li><a href="#tabs-2">';
        _e('PediaPress Gallery', 'rdp-pediapress-embed');
        echo '</a></li>';
        echo '<li><a href="#tabs-3">';
        _e('PediaPress RSS Gallery', 'rdp-pediapress-embed');
        echo '</a></li>';
        echo '</ul>';
        
        $options = get_option( RDP_PEDIAPRESS_EMBED_PLUGIN::$options_name );
        $default_settings = RDP_PEDIAPRESS_EMBED_PLUGIN::default_settings();
        self::renderTab_1($options,$default_settings);
        self::renderTab_2($options,$default_settings);
        self::renderTab_3($options,$default_settings);

        echo '</div><!-- rdp-pediapress-embed-tabs -->';
        echo '</div><!-- rdp-ppe-shortcode-popup -->';        
    }//renderPopupForm
    
    private static function renderTab_1($options,$default_settings) {
        
        echo '<div id="tabs-1" class="rdp_ppe_form_wrap">';
        echo '<div class="media-item media-blank">';

        echo '<table class="describe">';
        echo '<tbody>';

        
        /*------------------------------------------------------------------------------
            Source URL
        ------------------------------------------------------------------------------*/   
        echo '<tr>';        
        echo '<th valign="top" class="label" scope="row">';
        echo '<span class="alignleft"><label for="rdp-ppe-embed-src">';
        _e('Source URL', 'rdp-pediapress-embed');
        echo '</label></span>';
        echo '<span class="alignright"><abbr class="required" title="required">*</abbr></span>';
        echo '</th>';
        echo '<td class="field"><input type="text" aria-required="true" value="http://" id="rdp_ppe_embed_src"></td>';
        echo '</tr> ';
        
        /*------------------------------------------------------------------------------
            Download URL
        ------------------------------------------------------------------------------*/ 
        echo '<tr>';          
        echo '<th valign="top" class="label" scope="row">';
        echo '<span class="alignleft"><label for="rdp-ppe-download-url">';
        _e('Download URL', 'rdp-pediapress-embed');
        echo '</label></span>';
        echo '</th>';
        echo '<td class="field"><input type="text" aria-required="true" value="http://"  id="rdp_ppe_download_url"></td>';
        echo '</tr> ';  
        
        
        /*------------------------------------------------------------------------------
            Use Default Settings
        ------------------------------------------------------------------------------*/ 
        echo '<tr>';
        $sLabel = esc_html__('Always use the default options from the settings page', 'rdp-pediapress-embed');        
        echo '<th valign="top" class="label" scope="row">';
        echo '<span class="alignleft"><label for="rdp_ppe_use_default_settings">';
        _e('Settings Mode', 'rdp-pediapress-embed');
        echo '</label></span>';
        echo '</th>';
        echo '<td class="field"><input type="checkbox" aria-required="true" value="1" id="rdp_ppe_use_default_settings" checked="checked" /> <span ><label for="rdp_ppe_use_default_settings"> '. $sLabel . '</label></span></td>';
        echo '</tr> ';        
        
        
        /*------------------------------------------------------------------------------
            Show TOC
        ------------------------------------------------------------------------------*/         
        echo '<tr>';
        $sLabel = esc_html__('Show TOC', 'rdp-pediapress-embed');
        echo '<th valign="top" class="label" scope="row">' . $sLabel . '</th>';
        $value = isset($options['toc_show'])? $options['toc_show'] : $default_settings['toc_show'];
        $value = intval($value);
        $sLabel = esc_html__('Display Table of Contents (TOC) for PediaPress book', 'rdp-pediapress-embed');
        echo '<td class="field"><input type="checkbox" aria-required="true" value="1" class="rdp_ppe_book_setting" id="rdp_ppe_toc_show" ' . checked($value,1 ,false) . '/> <span ><label for="rdp_ppe_toc_show"> '. $sLabel . '</label></span></td>';
        echo '</tr>';  
        
        /*------------------------------------------------------------------------------
            TOC Links
        ------------------------------------------------------------------------------*/         
        $value = isset($options['toc_links'])? $options['toc_links'] : $default_settings['toc_links'];
        echo '<tr>';
        $sLabel = esc_html__('TOC Links', 'rdp-pediapress-embed');
        echo '<th valign="top" class="label" scope="row">' . $sLabel . '</th>';
        echo '<td class="field">';
        $sLabel = esc_html__('Enabled &mdash; TOC links are enabled', 'rdp-pediapress-embed');
        echo '<label><input name="rdp-ppe-toc-links" class="rdp_ppe_book_setting" id="rdp_ppe_toc_links_enabled" type="radio" value="enabled"  ' . checked($value,"enabled",false) . '/> '. $sLabel . '</label>';
        echo '<br />';
        $sLabel = esc_html__('Logged-in &mdash; TOC links are active only when a user is logged in', 'rdp-pediapress-embed');
        echo '<label><input name="rdp-ppe-toc-links" class="rdp_ppe_book_setting" id="rdp_ppe_toc_links_logged_in" type="radio" value="logged-in" ' . checked($value,"logged-in",false) . '/> '. $sLabel . '</label>';
        echo '<br />';        
        $sLabel = esc_html__('Disabled &mdash; TOC links are completely disabled, all the time', 'rdp-pediapress-embed');
        echo '<label><input name="rdp-ppe-toc-links" class="rdp_ppe_book_setting" id="rdp_ppe_toc_links_disabled" type="radio" value="disabled" ' . checked($value,"disabled",false) . '/> '. $sLabel . '</label>  ';                                             
        echo '</td></tr> ';
        
        /*------------------------------------------------------------------------------
            Book Details
        ------------------------------------------------------------------------------*/         
        echo '<tr>';
        $sLabel = esc_html__('Book Details', 'rdp-pediapress-embed');        
        echo '<th valign="top" class="label" scope="row">' . $sLabel . '</th>';
        echo '<td class="field">';
        $value = isset($options['image_show'])? $options['image_show'] : $default_settings['image_show'];
        $value = intval($value);        
        echo '<input type="checkbox" aria-required="true" class="rdp_ppe_book_setting" value="1" id="rdp_ppe_image_show" ' . checked( $value , 1, false) . ' /> <span style="margin-right: 8px;"><label for="rdp_ppe_image_show"> ';
        _e('Display cover image', 'rdp-pediapress-embed');
        echo '</label></span>';
        $value = isset($options['title_show'])? $options['title_show'] : $default_settings['title_show'];
        $value = intval($value);
        echo '<input type="checkbox" aria-required="true" class="rdp_ppe_book_setting" value="1" id="rdp_ppe_title_show" ' . checked( $value , 1, false) . ' /> <span style="margin-right: 8px;"><label for="rdp_ppe_title_show"> ';
        _e('Display title', 'rdp-pediapress-embed');
        echo '</label></span>';
        $value = isset($options['subtitle_show'])? $options['subtitle_show'] : $default_settings['subtitle_show'];
        $value = intval($value);        
        echo '<input type="checkbox" aria-required="true" class="rdp_ppe_book_setting" value="1" id="rdp_ppe_subtitle_show" ' . checked( $value , 1, false) . ' /> <span><label for="rdp_ppe_subtitle_show"> ';
        _e('Display subtitle', 'rdp-pediapress-embed'); 
        echo '</label></span>';
        echo '<div></div>';
        $value = isset($options['full_title'])? $options['full_title'] : $default_settings['full_title'];
        $value = intval($value);        
        echo '<input type="checkbox" aria-required="true" class="rdp_ppe_book_setting" value="1" id="rdp_ppe_full_title" ' . checked( $value , 1, false) . ' /> <span  style="margin-right: 8px;"><label for="rdp_ppe_full_title"> ';
        _e('Use Full Titles', 'rdp-pediapress-embed'); 
        echo '</label></span>';
        $value = isset($options['editor_show'])? $options['editor_show'] : $default_settings['editor_show'];
        $value = intval($value);        
        echo '<input type="checkbox" aria-required="true" class="rdp_ppe_book_setting" value="1" id="rdp_ppe_editor_show" ' . checked( $value , 1, false) . ' /> <span style="margin-right: 8px;"><label for="rdp_ppe_editor_show"> ';
        _e('Display editor', 'rdp-pediapress-embed'); 
        echo '</label></span>';
        $value = isset($options['language_show'])? $options['language_show'] : $default_settings['language_show'];
        $value = intval($value);        
        echo '<input type="checkbox" aria-required="true" class="rdp_ppe_book_setting" value="1" id="rdp_ppe_language_show" ' . checked( $value , 1, false) . ' /> <span><label for="rdp_ppe_language_show"> ';
        _e('Display language', 'rdp-pediapress-embed'); 
        echo '</label></span>';
        echo '<div></div>';
        $value = isset($options['book_size_show'])? $options['book_size_show'] : $default_settings['book_size_show'];
        $value = intval($value);        
        echo '<input type="checkbox" aria-required="true" class="rdp_ppe_book_setting" value="1" id="rdp_ppe_book_size_show" ' . checked( $value , 1, false) . ' /> <span style="margin-right: 8px;"><label for="rdp_ppe_book_size_show"> ';
        _e('Display book size', 'rdp-pediapress-embed'); 
        echo '</label></span>';        
        echo '</td></tr> ';      
        
        /*------------------------------------------------------------------------------
            CTA Button
        ------------------------------------------------------------------------------*/        
        echo '<tr><th valign="top" class="label" scope="row"></th>';
        echo '<td class="field">';
        echo '<h3>' . esc_html__('Call-to-Action Button Settings', 'rdp-pediapress-embed') . '</h3>';
        
        // show button
        echo '<span style="width: 150px;display: inline-block;">';
        esc_html_e('Show','rdp-pediapress-embed');
        echo ':</span> ';
        $value = isset($options['cta_button_show'])? $options['cta_button_show'] : $default_settings['cta_button_show'];
        $value = intval($value);        
        echo '<input type="checkbox" class="rdp_ppe_book_setting" value="1" id="rdp_ppe_cta_button_show" ' . checked( $value , 1, false) . '/> ';        
        echo '<br />';
        
        // button size
        echo '<span style="width: 150px;display: inline-block;">';
        esc_html_e('Size','rdp-pediapress-embed');
        echo ':</span> ';        
        $value = ( isset( $options['cta_button_size'] ) ) ? $options['cta_button_size'] : $default_settings['cta_button_size'];
        echo '<select class="rdp_ppe_book_setting" id="rdp_ppe_cta_button_size">';
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
        echo '<select class="rdp_ppe_book_setting" id="rdp_ppe_cta_button_color">';
        foreach(RDP_PEDIAPRESS_EMBED_PLUGIN::button_colors() as $size){
            echo sprintf('<option value="%s" %s>%s</option>',$size,selected($value,$size,false), ucwords($size) );
        }
        echo '</select>';  
        echo '<br />';
        
        // button text
        echo '<span style="width: 150px;display: inline-block;">';
        esc_html_e('Text','rdp-pediapress-embed');
        echo ':</span> ';
        $sPPDownloadButtonText = empty($options['cta_button_text'])? $default_settings['cta_button_text'] : $options['cta_button_text'];
        $sPPDownloadButtonText = esc_attr($sPPDownloadButtonText);        
        echo '<input type="text" class="rdp_ppe_book_setting" id="rdp_ppe_cta_button_text" value="' . $sPPDownloadButtonText  . '" style="width: 250px;" />';
        echo '</td></tr> ';
        
        // popup content
        echo '<tr><th valign="top"  class="label" scope="row"></th>';
        echo '<td class="field">';
        echo '<label for="cta_button_content">';
        esc_html_e('Popup Content (shortcode/text/HTML)', 'rdp-pediapress-embed');
        echo '</label><br />';
        echo '<textarea class="rdp_ppe_book_setting" id="rdp_ppe_cta_button_content">';
        $value = empty($options['cta_button_content'])? '' : $options['cta_button_content'];
        $value = esc_textarea($value);
        echo $value;
        echo '</textarea>';
        echo '</td></tr> ';       

        
        /*------------------------------------------------------------------------------
            ATC Button
        ------------------------------------------------------------------------------*/         
        echo '<tr><th valign="top" class="label" scope="row"></th>';
        echo '<td class="field">';
        echo '<h3>' . esc_html__('Add-to-Cart Button Settings', 'rdp-pediapress-embed') . '</h3>';
        
        // show button
        echo '<span style="width: 150px;display: inline-block;">';
        esc_html_e('Show','rdp-pediapress-embed');
        echo ':</span> ';
        $value = isset($options['add_to_cart_show'])? $options['add_to_cart_show'] : $default_settings['add_to_cart_show'];
        $value = intval($value);        
        echo '<input type="checkbox" class="rdp_ppe_book_setting" value="1" id="rdp_ppe_add_to_cart_show" ' . checked( $value , 1, false) . '/> ';        
        echo '<br />';
        
        // button size
        echo '<span style="width: 150px;display: inline-block;">';
        esc_html_e('Size','rdp-pediapress-embed');
        echo ':</span> ';        
        $value = ( isset( $options['add_to_cart_size'] ) ) ? $options['add_to_cart_size'] : $default_settings['add_to_cart_size'];
        echo '<select class="rdp_ppe_book_setting" id="rdp_ppe_add_to_cart_size">';
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
        echo '<select class="rdp_ppe_book_setting" id="rdp_ppe_add_to_cart_color">';
        foreach(RDP_PEDIAPRESS_EMBED_PLUGIN::button_colors() as $size){
            echo sprintf('<option value="%s" %s>%s</option>',$size,selected($value,$size,false), ucwords($size) );
        }
        echo '</select>'; 
        echo '<br />';
        
        // button text
        echo '<span style="width: 150px;display: inline-block;">';
        esc_html_e('Text','rdp-pediapress-embed');
        echo ':</span> ';
        $value = empty($options['add_to_cart_text'])? $default_settings['add_to_cart_text'] : $options['add_to_cart_text'];
        $value = esc_attr($value);        
        echo '<input class="rdp_ppe_book_setting" type="text" id="rdp_ppe_add_to_cart_text" value="' . $value  . '" style="width: 250px;" />';
        echo '</td></tr> '; 
        
        
        /*------------------------------------------------------------------------------
            Insert Shortcode Button
        ------------------------------------------------------------------------------*/         
        echo '<tr>';
        echo '<td colspan="2">';
        echo '<input type="button" value="Insert Shortcode" id="btnInsertPPEBookShortcode" class="button-primary">';
        echo '</td></tr> ';     
        
        echo '</tbody>';
        echo '</table>';

        echo '</div>';
        echo '</div>';        
    }//renderTab_1
    
    
    private static function renderTab_2($options,$default_settings) {
        echo '<div id="tabs-2" class="rdp_ppe_form_wrap">';
        echo '<div class="media-item media-blank">';
        
        echo '<table class="describe">';
        echo '<tbody>';
        
        /*------------------------------------------------------------------------------
            Number of Columns
        ------------------------------------------------------------------------------*/        
        echo '<tr>';
        echo '<th valign="top" class="label" scope="row" style="width: 200px;">';
        echo '<span class="alignleft"><label for="rdp_ppe_gallery_col">';
        _e('Number of Columns', 'rdp-pediapress-embed');
        echo '</label></span>';
        echo '<span class="alignright"><abbr class="required" title="required" id="status_img">*</abbr></span>';
        echo '</th>';
        echo '<td class="field"><input type="text" aria-required="true" value="2" name="rdp_ppe_gallery_col" id="rdp_ppe_gallery_col" style="width: 20px;"></td>';
        echo '</tr> ';
        
        
        /*------------------------------------------------------------------------------
            Number of Results per Page
        ------------------------------------------------------------------------------*/         
        echo '<tr>';
        echo '<th valign="top" class="label" scope="row" style="width: 200px;">';
        echo '<span class="alignleft"><label for="rdp_ppe_gallery_num">';
        _e('Number of Results per Page', 'rdp-pediapress-embed'); 
        echo '</label></span>';
        echo '<span class="alignright"><abbr class="required" title="required" id="status_img">*</abbr></span>';
        echo '</th>';
        echo '<td class="field"><input type="text" aria-required="true" value="10" name="rdp_ppe_gallery_num" id="rdp_ppe_gallery_num" style="width: 30px;"></td>';
        echo '</tr>';    
        
        
        /*------------------------------------------------------------------------------
            Gallery Style
        ------------------------------------------------------------------------------*/           
        echo '<tr>'; 
        echo '<th valign="top" class="label" scope="row" style="width: 200px;">'; 
        echo '<span class="alignleft"><label for="rdp_ppe_gallery_size">'; 
        _e('Gallery Style', 'rdp-pediapress-embed');
        echo '</label></span>'; 
        echo '</th>'; 
        echo '<td class="field">'; 
        echo '<select id="rdp_ppe_gallery_size">'; 
        foreach(RDP_PEDIAPRESS_EMBED_PLUGIN::button_sizes() as $size){
            echo sprintf('<option value="%s">%s</option>',$size, ucwords($size) );
        }
        echo '</select>'; 
        echo '</td>'; 
        echo '</tr> '; 
        
        
        /*------------------------------------------------------------------------------
            Target Categories
        ------------------------------------------------------------------------------*/         
        echo '<tr>';
        echo '<th valign="top" class="label" scope="row" style="width: 200px;">';
        echo '<span class="alignleft"><label for="rdp_ppe_gallery_categories">';
        _e('Target Categories', 'rdp-pediapress-embed');
        echo '</label></span>';
        echo '<span class="alignright"><abbr class="required" title="required" id="status_img"></abbr></span>';
        echo '</th>';
        echo '<td class="field">';
        echo '<div id="rdp_ppe_gallery_categories" class="container">';

        $args = array(
          'orderby' => 'name',
          'hide_empty' => 0
          );
        $categories = get_categories( $args );  
            foreach ( $categories as $category ) {
                    echo '<input class="rdp_ppe_gallery_category" name="rdp_ppe_gallery_category" value="'. $category->term_id . '" type="checkbox" />' . $category->name . '<br/>';
            }

        echo '</div>';
        echo '</td>';
        echo '</tr>';  

        
        /*------------------------------------------------------------------------------
            Target Tags
        ------------------------------------------------------------------------------*/         
        echo '<tr>';
        echo '<th valign="top" class="label" scope="row" style="width: 200px;">';
        echo '<span class="alignleft"><label for="rdp_ppe_gallery_tags">';
        _e('Target Tags', 'rdp-pediapress-embed');
        echo '</label></span>';
        echo '<span class="alignright"><abbr class="required" title="required" id="status_img"></abbr></span>';
        echo '</th>';
        echo '<td class="field">';
        echo '<div id="rdp_ppe_gallery_tags" class="container">';
 
        $args = array(
          'orderby' => 'name',
          'hide_empty' => 0
          );
        $tags = get_tags( $args );  
            foreach ( $tags as $tag ) {
                    echo '<input class="rdp_ppe_gallery_tag" name="rdp_ppe_gallery_tag" value="'. $tag->term_id . '" type="checkbox" />' . $tag->name . '<br/>';
            }

        echo '</div>';
        echo '</td>';
        echo '</tr>'; 
        
        
        /*------------------------------------------------------------------------------
            Sort Field
        ------------------------------------------------------------------------------*/         
        echo '<tr>';
        echo '<th valign="top" class="label" scope="row" style="width: 200px;">';
        echo '<span class="alignleft"><label for="rdp_ppe_gallery_sort_col">';
        _e('Sort Field', 'rdp-pediapress-embed');
        echo '</label></span>';
        echo '</th>';
        echo '<td class="field">';
        echo '<input type="radio" name="rdp_ppe_gallery_sort_col" checked="checked" value="post_title">Post Title &nbsp;&nbsp;&nbsp;<input type="radio" name="rdp_ppe_gallery_sort_col" value="post_date">Post Date';
        echo '</td>';
        echo '</tr>'; 
        
        
        /*------------------------------------------------------------------------------
            Sort Order
        ------------------------------------------------------------------------------*/        
        echo '<tr>';
        echo '<th valign="top" class="label" scope="row" style="width: 200px;">';
        echo '<span class="alignleft"><label for="rdp_ppe_gallery_sort_dir">';
        _e('Sort Order', 'rdp-pediapress-embed');
        echo '</label></span>';
        echo '</th>';
        echo '<td class="field">';
        echo '<input type="radio" name="rdp_ppe_gallery_sort_dir" checked="checked" value="ASC">Ascending &nbsp;&nbsp;&nbsp;<input type="radio" name="rdp_ppe_gallery_sort_dir" value="DESC">Descending';
        echo '</td>';
        echo '</tr>'; 
        
        
        /*------------------------------------------------------------------------------
            Use Default Settings
        ------------------------------------------------------------------------------*/ 
        echo '<tr>';
        $sLabel = esc_html__('Always use the default options from the settings page', 'rdp-pediapress-embed');        
        echo '<th valign="top" class="label" scope="row">';
        echo '<span class="alignleft"><label for="rdp_ppe_gallery_use_default_settings">';
        _e('Settings Mode', 'rdp-pediapress-embed');
        echo '</label></span>';
        echo '<span class="alignright"></span>';
        echo '</th>';
        echo '<td class="field"><input type="checkbox" aria-required="true" value="1" id="rdp_ppe_gallery_use_default_settings" checked="checked" /> <span ><label for="rdp_ppe_gallery_use_default_settings"> '. $sLabel . '</label></span></td>';
        echo '</tr> ';          
        
        
        /*------------------------------------------------------------------------------
            Book Details
        ------------------------------------------------------------------------------*/         
        echo '<tr>';
        $sLabel = esc_html__('Book Details', 'rdp-pediapress-embed');        
        echo '<th valign="top" class="label" scope="row">' . $sLabel . '</th>';
        echo '<td class="field">';
        $value = isset($options['image_show'])? $options['image_show'] : $default_settings['image_show'];
        $value = intval($value);        
        echo '<input type="checkbox" aria-required="true" class="rdp_ppe_gallery_setting" value="1" id="rdp_ppe_gallery_image_show" ' . checked( $value , 1, false) . ' /> <span style="margin-right: 8px;"><label for="rdp_ppe_gallery_image_show"> ';
        _e('Display cover image', 'rdp-pediapress-embed');
        echo '</label></span>';
        $value = isset($options['title_show'])? $options['title_show'] : $default_settings['title_show'];
        $value = intval($value);
        echo '<input type="checkbox" aria-required="true" class="rdp_ppe_gallery_setting" value="1" id="rdp_ppe_gallery_title_show" ' . checked( $value , 1, false) . ' /> <span style="margin-right: 8px;"><label for="rdp_ppe_gallery_title_show"> ';
        _e('Display title', 'rdp-pediapress-embed');
        echo '</label></span>';
        $value = isset($options['subtitle_show'])? $options['subtitle_show'] : $default_settings['subtitle_show'];
        $value = intval($value);        
        echo '<input type="checkbox" aria-required="true" class="rdp_ppe_gallery_setting" value="1" id="rdp_ppe_gallery_subtitle_show" ' . checked( $value , 1, false) . ' /> <span><label for="rdp_ppe_gallery_subtitle_show"> ';
        _e('Display subtitle', 'rdp-pediapress-embed'); 
        echo '</label></span>';
        echo '<div></div>';
        $value = isset($options['full_title'])? $options['full_title'] : $default_settings['full_title'];
        $value = intval($value);        
        echo '<input type="checkbox" aria-required="true" class="rdp_ppe_gallery_setting" value="1" id="rdp_ppe_gallery_full_title" ' . checked( $value , 1, false) . ' /> <span  style="margin-right: 8px;"><label for="rdp_ppe_gallery_full_title"> ';
        _e('Use Full Titles', 'rdp-pediapress-embed'); 
        echo '</label></span>';
        $value = isset($options['editor_show'])? $options['editor_show'] : $default_settings['editor_show'];
        $value = intval($value);        
        echo '<input type="checkbox" aria-required="true" class="rdp_ppe_gallery_setting" value="1" id="rdp_ppe_gallery_editor_show" ' . checked( $value , 1, false) . ' /> <span style="margin-right: 8px;"><label for="rdp_ppe_gallery_editor_show"> ';
        _e('Display editor', 'rdp-pediapress-embed'); 
        echo '</label></span>';
        $value = isset($options['language_show'])? $options['language_show'] : $default_settings['language_show'];
        $value = intval($value);        
        echo '<input type="checkbox" aria-required="true" class="rdp_ppe_gallery_setting" value="1" id="rdp_ppe_gallery_language_show" ' . checked( $value , 1, false) . ' /> <span><label for="rdp_ppe_gallery_language_show"> ';
        _e('Display language', 'rdp-pediapress-embed'); 
        echo '</label></span>';
        echo '<div></div>';
        $value = isset($options['book_size_show'])? $options['book_size_show'] : $default_settings['book_size_show'];
        $value = intval($value);        
        echo '<input type="checkbox" aria-required="true" class="rdp_ppe_gallery_setting" value="1" id="rdp_ppe_gallery_book_size_show" ' . checked( $value , 1, false) . ' /> <span style="margin-right: 8px;"><label for="rdp_ppe_gallery_book_size_show"> ';
        _e('Display book size', 'rdp-pediapress-embed'); 
        echo '</label></span>';        
        echo '</td></tr> ';          
        
        
        
        /*------------------------------------------------------------------------------
            Insert Shortcode Button
        ------------------------------------------------------------------------------*/         
        echo '<tr>';
        echo '<td colspan="2">';
        echo '<input type="button" value="Insert Shortcode" id="btnInsertPPEGalleryShortcode" class="button-primary">';
        echo '</td></tr> ';     
        
        echo '</tbody>';
        echo '</table>';
        
        echo '</div>';
        echo '</div>';         
    }//renderTab_2
    
    private static function renderTab_3($options,$default_settings) {
        echo '<div id="tabs-3" class="rdp_ppe_form_wrap">';
        echo '<div class="media-item media-blank">';
        
        echo '<table class="describe">';
        echo '<tbody>';
        
        /*------------------------------------------------------------------------------
            Source URL
        ------------------------------------------------------------------------------*/   
        echo '<tr>';        
        echo '<th valign="top" class="label" scope="row" style="width: 200px;">';
        echo '<span class="alignleft"><label for="rdp_ppe_gallery_rss_feed">';
        _e('Feed URL', 'rdp-pediapress-embed');
        echo '</label></span>';
        echo '<span class="alignright"><abbr class="required" title="required">*</abbr></span>';
        echo '</th>';
        echo '<td class="field"><input type="text" aria-required="true" value="http://" id="rdp_ppe_gallery_rss_feed" style="width: 300px;"></td>';
        echo '</tr> ';        
        
        /*------------------------------------------------------------------------------
            Number of Columns
        ------------------------------------------------------------------------------*/        
        echo '<tr>';
        echo '<th valign="top" class="label" scope="row">';
        echo '<span class="alignleft"><label for="rdp_ppe_gallery_rss_col">';
        _e('Number of Columns', 'rdp-pediapress-embed');
        echo '</label></span>';
        echo '<span class="alignright"><abbr class="required" title="required" id="status_img">*</abbr></span>';
        echo '</th>';
        echo '<td class="field"><input type="text" aria-required="true" value="2" name="rdp_ppe_gallery_rss_col" id="rdp_ppe_gallery_rss_col" style="width: 20px;"></td>';
        echo '</tr> ';
        
        
        /*------------------------------------------------------------------------------
            Number of Results per Page
        ------------------------------------------------------------------------------*/         
        echo '<tr>';
        echo '<th valign="top" class="label" scope="row" style="width: 200px;">';
        echo '<span class="alignleft"><label for="rdp_ppe_gallery_rss_num">';
        _e('Number of Results per Page', 'rdp-pediapress-embed'); 
        echo '</label></span>';
        echo '<span class="alignright"><abbr class="required" title="required" id="status_img">*</abbr></span>';
        echo '</th>';
        echo '<td class="field"><input type="text" aria-required="true" value="10" name="rdp_ppe_gallery_rss_num" id="rdp_ppe_gallery_rss_num" style="width: 30px;"></td>';
        echo '</tr>';    
        
        
        /*------------------------------------------------------------------------------
            Gallery Style
        ------------------------------------------------------------------------------*/           
        echo '<tr>'; 
        echo '<th valign="top" class="label" scope="row" style="width: 200px;">'; 
        echo '<span class="alignleft"><label for="rdp_ppe_gallery_rss_size">'; 
        _e('Gallery Style', 'rdp-pediapress-embed');
        echo '</label></span>'; 
        echo '</th>'; 
        echo '<td class="field">'; 
        echo '<select id="rdp_ppe_gallery_rss_size">'; 
        foreach(RDP_PEDIAPRESS_EMBED_PLUGIN::button_sizes() as $size){
            echo sprintf('<option value="%s">%s</option>',$size, ucwords($size) );
        }
        echo '</select>'; 
        echo '</td>'; 
        echo '</tr> '; 

        
        
        /*------------------------------------------------------------------------------
            Sort Field
        ------------------------------------------------------------------------------*/         
        echo '<tr>';
        echo '<th valign="top" class="label" scope="row" style="width: 200px;">';
        echo '<span class="alignleft"><label for="rdp_ppe_gallery_rss_sort_col">';
        _e('Sort Field', 'rdp-pediapress-embed');
        echo '</label></span>';
        echo '</th>';
        echo '<td class="field">';
        echo '<input type="radio" name="rdp_ppe_gallery_rss_sort_col" checked="checked" value="post_title">Post Title &nbsp;&nbsp;&nbsp;<input type="radio" name="rdp_ppe_gallery_rss_sort_col" value="post_date">Post Date';
        echo '</td>';
        echo '</tr>'; 
        
        
        /*------------------------------------------------------------------------------
            Sort Order
        ------------------------------------------------------------------------------*/        
        echo '<tr>';
        echo '<th valign="top" class="label" scope="row" style="width: 200px;">';
        echo '<span class="alignleft"><label for="rdp_ppe_gallery_rss_sort_dir">';
        _e('Sort Order', 'rdp-pediapress-embed');
        echo '</label></span>';
        echo '</th>';
        echo '<td class="field">';
        echo '<input type="radio" name="rdp_ppe_gallery_rss_sort_dir" checked="checked" value="ASC">Ascending &nbsp;&nbsp;&nbsp;<input type="radio" name="rdp_ppe_gallery_rss_sort_dir" value="DESC">Descending';
        echo '</td>';
        echo '</tr>'; 
        
        
        /*------------------------------------------------------------------------------
            Use Default Settings
        ------------------------------------------------------------------------------*/ 
        echo '<tr>';
        $sLabel = esc_html__('Always use the default options from the settings page', 'rdp-pediapress-embed');        
        echo '<th valign="top" class="label" scope="row">';
        echo '<span class="alignleft"><label for="rdp_ppe_gallery_rss_use_default_settings">';
        _e('Settings Mode', 'rdp-pediapress-embed');
        echo '</label></span>';
        echo '<span class="alignright"></span>';
        echo '</th>';
        echo '<td class="field"><input type="checkbox" aria-required="true" value="1" id="rdp_ppe_gallery_rss_use_default_settings" checked="checked" /> <span ><label for="rdp_ppe_gallery_rss_use_default_settings"> '. $sLabel . '</label></span></td>';
        echo '</tr> ';          
        
        
        /*------------------------------------------------------------------------------
            Book Details
        ------------------------------------------------------------------------------*/         
        echo '<tr>';
        $sLabel = esc_html__('Book Details', 'rdp-pediapress-embed');        
        echo '<th valign="top" class="label" scope="row">' . $sLabel . '</th>';
        echo '<td class="field">';
        $value = isset($options['image_show'])? $options['image_show'] : $default_settings['image_show'];
        $value = intval($value);        
        echo '<input type="checkbox" aria-required="true" class="rdp_ppe_gallery_rss_setting" value="1" id="rdp_ppe_gallery_rss_image_show" ' . checked( $value , 1, false) . ' /> <span style="margin-right: 8px;"><label for="rdp_ppe_gallery_rss_image_show"> ';
        _e('Display cover image', 'rdp-pediapress-embed');
        echo '</label></span>';
        $value = isset($options['title_show'])? $options['title_show'] : $default_settings['title_show'];
        $value = intval($value);
        echo '<input type="checkbox" aria-required="true" class="rdp_ppe_gallery_rss_setting" value="1" id="rdp_ppe_gallery_rss_title_show" ' . checked( $value , 1, false) . ' /> <span style="margin-right: 8px;"><label for="rdp_ppe_gallery_rss_title_show"> ';
        _e('Display title', 'rdp-pediapress-embed');
        echo '</label></span>';
        $value = isset($options['subtitle_show'])? $options['subtitle_show'] : $default_settings['subtitle_show'];
        $value = intval($value);        
        echo '<input type="checkbox" aria-required="true" class="rdp_ppe_gallery_rss_setting" value="1" id="rdp_ppe_gallery_rss_subtitle_show" ' . checked( $value , 1, false) . ' /> <span><label for="rdp_ppe_gallery_rss_subtitle_show"> ';
        _e('Display subtitle', 'rdp-pediapress-embed'); 
        echo '</label></span>';
        echo '<div></div>';
        $value = isset($options['full_title'])? $options['full_title'] : $default_settings['full_title'];
        $value = intval($value);        
        echo '<input type="checkbox" aria-required="true" class="rdp_ppe_gallery_rss_setting" value="1" id="rdp_ppe_gallery_rss_full_title" ' . checked( $value , 1, false) . ' /> <span  style="margin-right: 8px;"><label for="rdp_ppe_gallery_rss_full_title"> ';
        _e('Use Full Titles', 'rdp-pediapress-embed'); 
        echo '</label></span>';
        $value = isset($options['editor_show'])? $options['editor_show'] : $default_settings['editor_show'];
        $value = intval($value);        
        echo '<input type="checkbox" aria-required="true" class="rdp_ppe_gallery_rss_setting" value="1" id="rdp_ppe_gallery_rss_editor_show" ' . checked( $value , 1, false) . ' /> <span style="margin-right: 8px;"><label for="rdp_ppe_gallery_rss_editor_show"> ';
        _e('Display editor', 'rdp-pediapress-embed'); 
        echo '</label></span>';
        $value = isset($options['language_show'])? $options['language_show'] : $default_settings['language_show'];
        $value = intval($value);        
        echo '<input type="checkbox" aria-required="true" class="rdp_ppe_gallery_rss_setting" value="1" id="rdp_ppe_gallery_rss_language_show" ' . checked( $value , 1, false) . ' /> <span><label for="rdp_ppe_gallery_rss_language_show"> ';
        _e('Display language', 'rdp-pediapress-embed'); 
        echo '</label></span>';
        echo '<div></div>';
        $value = isset($options['book_size_show'])? $options['book_size_show'] : $default_settings['book_size_show'];
        $value = intval($value);        
        echo '<input type="checkbox" aria-required="true" class="rdp_ppe_gallery_rss_setting" value="1" id="rdp_ppe_gallery_rss_book_size_show" ' . checked( $value , 1, false) . ' /> <span style="margin-right: 8px;"><label for="rdp_ppe_gallery_rss_book_size_show"> ';
        _e('Display book size', 'rdp-pediapress-embed'); 
        echo '</label></span>';        
        echo '</td></tr> ';          
        
        
        
        /*------------------------------------------------------------------------------
            Insert Shortcode Button
        ------------------------------------------------------------------------------*/         
        echo '<tr>';
        echo '<td colspan="2">';
        echo '<input type="button" value="Insert Shortcode" id="btnInsertPPEGalleryRSSShortcode" class="button-primary">';
        echo '</td></tr> ';     
        
        echo '</tbody>';
        echo '</table>';
        
        echo '</div>';
        echo '</div>'; 

        
    }//renderTab_3
    
}//RDP_PEDIAPRESS_EMBED_SHORTCODE_POPUP
