/**only loaded when post_type == wppizza**/
jQuery(document).ready(function($){
	/*********************************************************
	*	[de/activate a license]
	*********************************************************/
	$(document).on('click', '.wppizza_license_activate, .wppizza_license_deactivate', function(e){
		var self = $(this);
		var div=self.closest('.wppizza_license');
		var div_id=div.attr('id');
		var key_input=div.find('.wppizza_license_key');		
		var spinner_id='wppizza-load-'+div_id+'';	
		var data=div.find('input').serialize();
		var status=div.find('.wppizza_license_status');
		var action = self.hasClass('wppizza_license_activate') ? 'activate' : 'deactivate';
		
		/* add spinner */
		div.prepend('<span id="'+spinner_id+'" class="wppizza-load"></span>');
		jQuery.post(ajaxurl , {action :'wppizza_admin_tools_ajax',vars:{'field':'license_action', 'action' : action, 'data' : data}}, function(res) {
			
			/* remove spinner */
			$('#'+spinner_id+'').remove();
			
			
			/* some error somewhere*/
			if(typeof res.error !== 'undefined'){
				alert(res.error);
				return;
			}
			
			/* update class and label */
			if(typeof res.api_results !== 'undefined' && typeof res.api_results.success !== 'undefined'){


				
				/* only change lables and classes if api call succeeded */
				if(res.html.update_success){
					self.val(res.html.label);
					self.removeClass(res.html.class_remove);
					self.addClass(res.html.class_add);
					/* clear licence input if set */
					if(res.html.no_license_value){
						key_input.val('');
					}
				}	
				status.html(res.html.status);
			}
		},'json').error(function(jqXHR, textStatus, errorThrown) {alert("error : " + errorThrown);$('#'+spinner_id+'').remove();});
	});
});