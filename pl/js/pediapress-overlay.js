 $j=jQuery.noConflict();
// Use jQuery via $j(...)

$j(document).ready(rdp_ppe_overlay_onReady);

function rdp_ppe_overlay_onReady(){
    if(typeof rdp_ppe == 'undefined')return;
    if(rdp_ppe.has_content){
        $j(".rdp-ppe-cta-button").colorbox(
                                        {returnFocus:false,
                                        inline:true, 
                                        innerWidth: 960, 
                                        innerHeight:"80%",
                                        transition:"none"
                                        }) ;
        
       
        $j(document).bind('cbox_cleanup', function(){
          $j("#rdp_ppe_inline_content_wrapper").html($j("#cboxLoadedContent").html());
          
        }); 
    }
      

}//rdp_ppe_overlay_onReady
