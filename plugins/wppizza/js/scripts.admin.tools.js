/**only loaded when post_type == wppizza**/
jQuery(document).ready(function($){
	/*********************************************************
	*	[tools->get php settings]
	*********************************************************/
	$(document).on('click', '#wppizza_show_php_vars', function(e){
		var elm=$('#wppizza_php_info');
		jQuery.post(ajaxurl , {action :'wppizza_admin_tools_ajax',vars:{'field':'get-php-vars'}}, function(res) {
			elm.html(res);
		},'html').error(function(jqXHR, textStatus, errorThrown) {alert("error : " + errorThrown);});
	});
});