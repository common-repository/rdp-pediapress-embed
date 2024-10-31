var $j=jQuery.noConflict();
// Use jQuery via $j(...)

$j(document).ready(rdp_ppe_main_onReady);
function rdp_ppe_main_onReady(){
    rdp_ppe_handle_must_log_in();
}//rdp_ppe_main_onReady

function rdp_ppe_handle_must_log_in(){
    if(typeof rdp_ppe == 'undefined')return;
    if(rdp_ppe.logged_in === 'true')return;
    $j('.book_show').on( "click", 'a.rdp_ppe_must_log_in' , function(event){
        event.preventDefault();  
        $j('#rdp_ppe_message').remove();
        $j(this).parent().append(rdp_ppe.log_in_msg);
    });        
}//rdp_ppe_handle_links


