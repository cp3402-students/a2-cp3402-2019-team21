jQuery(document).ready(function($){
	/*******************************
		[opening times - add new]
	*******************************/
	$(document).on('click', '#wppizza_add_opening_times_custom', function(e){
		e.preventDefault();
		jQuery.post(ajaxurl , {action :'wppizza_admin_openingtimes_ajax',vars:{'field':'opening_times_custom'}}, function(response) {
			$('#wppizza_opening_times_custom_options').append(response);
		},'html').error(function(jqXHR, textStatus, errorThrown) {alert("error : " + errorThrown);});
	});
	/*******************************
		[times closed - add new]
	*******************************/
	$(document).on('click', '#wppizza_add_times_closed_standard', function(e){
		e.preventDefault();
		jQuery.post(ajaxurl , {action :'wppizza_admin_openingtimes_ajax',vars:{'field':'times_closed_standard'}}, function(response) {
			$('#wppizza_times_closed_standard_options').append(response);
		},'html').error(function(jqXHR, textStatus, errorThrown) {alert("error : " + errorThrown);});
	});     
	/*****************************
	*	[remove an option]
	*****************************/
	$(document).on('click', '.wppizza-delete', function(e){
		e.preventDefault();
		var self=$(this);
		$(this).closest('div').empty().remove();
	});	
});