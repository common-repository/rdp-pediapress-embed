var $j=jQuery.noConflict();
// Use jQuery via $j(...)

$j(document).ready(rdp_ppe_shortcode_popup_onReady);
var rdp_ppe_popupOpening = false;

function rdp_ppe_shortcode_popup_onReady(){
    $j('#rdp-pediapress-embed-tabs').tabs(); 
    rdp_ppe_toggleBookSettingInputs();
    rdp_ppe_toggleGallerySettingInputs();
    rdp_ppe_toggleGalleryRSSSettingInputs();
    $j('.wp-admin').on( "click", '#rdp-ppe-shortcode-button' , function(){
        rdp_ppe_popupOpening = true;
        $j('body').addClass('rdp-ppe-shortcode');
        setTimeout(function(){ rdp_ppe_popupOpening = false; }, 3000);
    });   
    $j('body').bind('DOMSubtreeModified', function(e) {
        if(rdp_ppe_popupOpening)return true;
        if(!$j('body').hasClass('modal-open') && $j('body').hasClass('rdp-ppe-shortcode'))rdp_ppe_removeBodyClass();
    });    
    $j('.wp-admin').on( "click", '#rdp_ppe_use_default_settings' , rdp_ppe_toggleBookSettingInputs);
    $j('.wp-admin').on( "click", '#rdp_ppe_gallery_use_default_settings' , rdp_ppe_toggleGallerySettingInputs);    
    $j('.wp-admin').on( "click", '#rdp_ppe_gallery_rss_use_default_settings' , rdp_ppe_toggleGalleryRSSSettingInputs);    
    $j('.wp-admin').on( "click", '#btnInsertPPEBookShortcode' , rdp_ppe_insertBookShortcode );     
    $j('.wp-admin').on( "click", '#btnInsertPPEGalleryShortcode' , rdp_ppe_insertGalleryShortcode );     
    $j('.wp-admin').on( "click", '#btnInsertPPEGalleryRSSShortcode' , rdp_ppe_insertGalleryRSSShortcode );     
}//rdp_ppe_shortcode_popup_onReady

function rdp_ppe_removeBodyClass(){
    $j('body').removeClass('rdp-ppe-shortcode');
}

function rdp_ppe_toggleBookSettingInputs(){
    $j('#rdp-pediapress-embed-tabs .rdp_ppe_book_setting').prop('disabled', function(i, v) { return !v; });
}//rdp_ppe_toggleBookSettingInputs

function rdp_ppe_toggleGallerySettingInputs(){
    $j('#rdp-pediapress-embed-tabs .rdp_ppe_gallery_setting').prop('disabled', function(i, v) { return !v; });
}//rdp_ppe_toggleBookSettingInputs

function rdp_ppe_toggleGalleryRSSSettingInputs(){
    $j('#rdp-pediapress-embed-tabs .rdp_ppe_gallery_rss_setting').prop('disabled', function(i, v) { return !v; });
}//rdp_ppe_toggleBookSettingInputs

function rdp_ppe_insertBookShortcode(){
    var srcURL = jQuery("#rdp_ppe_embed_src");
    if(rdp_ppe_admin_chk_blank(srcURL,"Please enter the PediaPress source URL.")){return false;}

    var sCode = "[rdp-pediapress-embed url='" + srcURL.val() + "' download_url='" + downloadURL.val() + "'";
    var ppButtonContent = jQuery("#rdp_ppe_cta_button_content").val();
    
    var fUseDefaults = ( jQuery("#rdp_ppe_use_default_settings").attr('checked') ? 1: 0 );    
    if(fUseDefaults != 1)sCode += rdp_ppe_buildBookShortcodeAttributes(ppButtonContent);

    if(ppButtonContent){
        sCode += "]" + ppButtonContent + "[/rdp-pediapress-embed]"
    }else{
       sCode += "]"; 
    } 
    rdp_ppe_removeBodyClass();
    var win = window.dialogArguments || opener || parent || top;    
    win.send_to_editor( sCode );    
}//rdp_ppe_insertBookShortcode

function rdp_ppe_buildBookShortcodeAttributes(ppButtonContent){
    var sCode = '';
    
    jQuery.fn.extend({
        groupVal: function() {
            return $j(this).filter(':checked').val();
        }
    });    
    
    var ppTOCShow = ( jQuery("#rdp_ppe_toc_show").attr('checked') ? 1: 0 );
    sCode += " toc_links='" + ppTOCShow + "'";
    
    if(ppTOCShow === 1){
        var ppTOCLinks = jQuery("[name='rdp-ppe-toc-links']").groupVal();
        sCode += " toc_links='" + ppTOCLinks + "'";        
    }

    var ppImageShow = ( jQuery("#rdp_ppe_image_show").attr('checked') ? 1: 0 );
    sCode += " image_show='" + ppImageShow + "'";    
    
    var ppTitleShow = ( jQuery("#rdp_ppe_title_show").attr('checked') ? 1: 0 );
    sCode += " title_show='" + ppTitleShow + "'";

    var ppFullTtitleShow = ( jQuery("#rdp_ppe_full_title").attr('checked') ? 1: 0 );
    sCode += " full_title='" + ppFullTtitleShow + "'";
    
    var ppSubtitleShow = ( jQuery("#rdp_ppe_subtitle_show").attr('checked') ? 1: 0 );    
    if(ppFullTtitleShow === 1)ppSubtitleShow = 0;
    sCode += " subtitle_show='" + ppSubtitleShow + "'";
    
    var ppEditorShow = ( jQuery("#rdp_ppe_editor_show").attr('checked') ? 1: 0 );
    sCode += " editor_show='" + ppEditorShow + "'";
    
    var ppLanguageShow = ( jQuery("#rdp_ppe_language_show").attr('checked') ? 1: 0 );
    sCode += " language_show='" + ppLanguageShow + "'";
    
    var ppBookSizeShow = ( jQuery("#rdp_ppe_book_size_show").attr('checked') ? 1: 0 );
    sCode += " book_size_show='" + ppBookSizeShow + "'";     

    var ppButtonShow = ( jQuery("#rdp_ppe_cta_button_show").attr('checked') ? 1: 0 );
    if(ppButtonContent) ppButtonShow = 1;
    sCode += " cta_button_show='"+ ppButtonShow +"'";

    if(ppButtonShow === 1){
        ppButtonSize = " cta_button_size='"+ jQuery("#rdp_ppe_cta_button_size").val() +"'";
        ppButtonColor = " cta_button_color='"+ jQuery("#rdp_ppe_cta_button_color").val() +"'";
        ppButtonText = " cta_button_text='"+ jQuery("#rdp_ppe_cta_button_text").val() +"'";
        sCode += ppButtonSize + ppButtonColor + ppButtonText;
    }   
    
    var ppATCShow = ( jQuery("#rdp_ppe_add_to_cart_show").attr('checked') ? 1: 0 );
    sCode += " add_to_cart_show='" + ppATCShow + "'";
    
    if(ppATCShow === 1){
        ppButtonSize = " add_to_cart_size='"+ jQuery("#rdp_ppe_add_to_cart_size").val() +"'";
        ppButtonColor = " add_to_cart_color='"+ jQuery("#rdp_ppe_add_to_cart_color").val() +"'";
        ppButtonText = " add_to_cart_text='"+ jQuery("#rdp_ppe_add_to_cart_text").val() +"'";
        sCode += ppButtonSize + ppButtonColor + ppButtonText;
    }
    
    return sCode;
}//rdp_ppe_buildBookShortcodeAttributes



function rdp_ppe_insertGalleryShortcode(){
    var ppGalleryCol = jQuery("#rdp_ppe_gallery_col");
    if(rdp_ppe_admin_chk_blank(ppGalleryCol,"Please enter number of columns")){return false;} 
    if(!rdp_ppe_admin_chk_numric(ppGalleryCol,"The number of columns should be numeric")) {return false;}
 
    var ppGalleryNum = jQuery("#rdp_ppe_gallery_num");
    if(rdp_ppe_admin_chk_blank(ppGalleryNum,"Please enter number of results to fetch")){return false;}    
    if(!rdp_ppe_admin_chk_numric(ppGalleryNum,"The number of results should be numeric")) {return false;} 
          
    var ppGalleryCats = '';
    jQuery(".rdp_ppe_gallery_category:checked").each(function( index ) {
        if(ppGalleryCats.length)ppGalleryCats += ',';
        ppGalleryCats += $j( this ).val();
      });

    var ppGalleryTags = '';
    jQuery(".rdp_ppe_gallery_tag:checked").each(function( index ) {
        if(ppGalleryTags.length)ppGalleryTags += ',';
        ppGalleryTags += $j( this ).val();
      });
      

    var ppGallerySize = jQuery("#rdp_ppe_gallery_size");    
    
    var sCode = "[rdp-pediapress-embed-gallery col='"+ppGalleryCol.val()+"' num='"+ppGalleryNum.val()+"' size='"+ppGallerySize.val()+"'";
    if(ppGalleryCats.length)sCode += " cat='"+ppGalleryCats+"'";
    if(ppGalleryTags.length)sCode += " tag='"+ppGalleryTags+"'";
    
    
    var sGallerySort = jQuery("input:radio[name=rdp_ppe_gallery_sort_col]:checked").val();
    sCode += " sort_col='"+ sGallerySort +"'";    
    
    var sGalleryAttr = jQuery("input:radio[name=rdp_ppe_gallery_sort_dir]:checked").val();
    sCode += " sort_dir='"+ sGalleryAttr +"'";
    
    var fUseDefaults = ( jQuery("#rdp_ppe_gallery_use_default_settings").attr('checked') ? 1: 0 );    
    if(fUseDefaults != 1){
        var ppImageShow = ( jQuery("#rdp_ppe_gallery_image_show").attr('checked') ? 1: 0 );
        sCode += " image_show='" + ppImageShow + "'";    

        var ppTitleShow = ( jQuery("#rdp_ppe_gallery_title_show").attr('checked') ? 1: 0 );
        sCode += " title_show='" + ppTitleShow + "'";

        var ppFullTtitleShow = ( jQuery("#rdp_ppe_gallery_full_title").attr('checked') ? 1: 0 );
        sCode += " full_title='" + ppFullTtitleShow + "'";

        var ppSubtitleShow = ( jQuery("#rdp_ppe_gallery_subtitle_show").attr('checked') ? 1: 0 );    
        if(ppFullTtitleShow === 1)ppSubtitleShow = 0;
        sCode += " subtitle_show='" + ppSubtitleShow + "'";

        var ppEditorShow = ( jQuery("#rdp_ppe_gallery_editor_show").attr('checked') ? 1: 0 );
        sCode += " editor_show='" + ppEditorShow + "'";

        var ppLanguageShow = ( jQuery("#rdp_ppe_gallery_language_show").attr('checked') ? 1: 0 );
        sCode += " language_show='" + ppLanguageShow + "'";

        var ppBookSizeShow = ( jQuery("#rdp_ppe_gallery_book_size_show").attr('checked') ? 1: 0 );
        sCode += " book_size_show='" + ppBookSizeShow + "'";          
    }  
    
    sCode += "]";
    rdp_ppe_removeBodyClass();
    var win = window.dialogArguments || opener || parent || top;    
    win.send_to_editor( sCode );    
}//rdp_ppe_insertGalleryShortcode


function rdp_ppe_insertGalleryRSSShortcode(){
    var srcURL = jQuery("#rdp_ppe_gallery_rss_feed");
    if(rdp_ppe_admin_chk_blank(srcURL,"Please enter the PediaPress feed URL.")){return false;}   
    
    var ppGalleryCol = jQuery("#rdp_ppe_gallery_rss_col");
    if(rdp_ppe_admin_chk_blank(ppGalleryCol,"Please enter number of columns")){return false;} 
    if(!rdp_ppe_admin_chk_numric(ppGalleryCol,"The number of columns should be numeric")) {return false;}
 
    var ppGalleryNum = jQuery("#rdp_ppe_gallery_rss_num");
    if(rdp_ppe_admin_chk_blank(ppGalleryNum,"Please enter number of results to fetch")){return false;}    
    if(!rdp_ppe_admin_chk_numric(ppGalleryNum,"The number of results should be numeric")) {return false;} 
          
    var ppGalleryCats = '';
    jQuery(".rdp_ppe_gallery_rss_category:checked").each(function( index ) {
        if(ppGalleryCats.length)ppGalleryCats += ',';
        ppGalleryCats += $j( this ).val();
      });

    var ppGalleryTags = '';
    jQuery(".rdp_ppe_gallery_rss_tag:checked").each(function( index ) {
        if(ppGalleryTags.length)ppGalleryTags += ',';
        ppGalleryTags += $j( this ).val();
      });
      

    var ppGallerySize = jQuery("#rdp_ppe_gallery_rss_size");   
    
    
    var sCode = "[rdp-pediapress-embed-gallery-rss url='" + srcURL.val() + "'  col='"+ppGalleryCol.val()+"' num='"+ppGalleryNum.val()+"' size='"+ppGallerySize.val()+"'";
    if(ppGalleryCats.length)sCode += " cat='"+ppGalleryCats+"'";
    if(ppGalleryTags.length)sCode += " tag='"+ppGalleryTags+"'";    
    var sGallerySort = jQuery("input:radio[name=rdp_ppe_gallery_sort_col]:checked").val();
    sCode += " sort_col='"+ sGallerySort +"'";    
    
    var sGalleryAttr = jQuery("input:radio[name=rdp_ppe_gallery_sort_dir]:checked").val();
    sCode += " sort_dir='"+ sGalleryAttr +"'";
    
    var fUseDefaults = ( jQuery("#rdp_ppe_gallery_use_default_settings").attr('checked') ? 1: 0 );    
    if(fUseDefaults != 1){
        var ppImageShow = ( jQuery("#rdp_ppe_gallery_image_show").attr('checked') ? 1: 0 );
        sCode += " image_show='" + ppImageShow + "'";    

        var ppTitleShow = ( jQuery("#rdp_ppe_gallery_title_show").attr('checked') ? 1: 0 );
        sCode += " title_show='" + ppTitleShow + "'";

        var ppFullTtitleShow = ( jQuery("#rdp_ppe_gallery_full_title").attr('checked') ? 1: 0 );
        sCode += " full_title='" + ppFullTtitleShow + "'";

        var ppSubtitleShow = ( jQuery("#rdp_ppe_gallery_subtitle_show").attr('checked') ? 1: 0 );    
        if(ppFullTtitleShow === 1)ppSubtitleShow = 0;
        sCode += " subtitle_show='" + ppSubtitleShow + "'";

        var ppEditorShow = ( jQuery("#rdp_ppe_gallery_editor_show").attr('checked') ? 1: 0 );
        sCode += " editor_show='" + ppEditorShow + "'";

        var ppLanguageShow = ( jQuery("#rdp_ppe_gallery_language_show").attr('checked') ? 1: 0 );
        sCode += " language_show='" + ppLanguageShow + "'";

        var ppBookSizeShow = ( jQuery("#rdp_ppe_gallery_book_size_show").attr('checked') ? 1: 0 );
        sCode += " book_size_show='" + ppBookSizeShow + "'";          
    }  
    
    sCode += "]";
    rdp_ppe_removeBodyClass();
    var win = window.dialogArguments || opener || parent || top;    
    win.send_to_editor( sCode );      
}//rdp_ppe_insertGalleryRSSShortcode


function rdp_ppe_admin_chk_blank(ctl,msg)
{
    if(typeof msg == 'undefined' || msg=="")
     {
      msg="This field cannot be blank";
     }
    if (ctl.val()=="")
     {
            alert(msg);
            ctl.val("");
            ctl.focus();
            return (true);
     }
    else
     return (false);
}  

function rdp_ppe_admin_chk_numric(ctl,msg)
 {
 	if(typeof msg == 'undefined' || msg=="")
	 {
	  msg="Please enter valid numeric data";
	 }

	if (isNaN(ctl.val()))
	 {
		alert(msg);
		ctl.val("");
		ctl.focus();
		return (false);
	 }
	else
	 return (true);
 }