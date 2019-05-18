/**only loaded when post_type == wppizza**/
jQuery(document).ready(function($){
	/*******************************
	*	[size option - add new]
	*******************************/
	$(document).on('click', '#wppizza_add_sizes', function(e){
		e.preventDefault();
			var self=$(this);
			self.prop( "disabled", true );/* disable add button */			
			var getKeys=$('.wppizza-getkey');
			if(getKeys.length>0){
				var allKeys=getKeys.serializeArray();
			}else{
				var allKeys='';
			}
			var newFields=parseInt($('#wppizza_add_sizes_fields').val());
			if(newFields>=1){
				jQuery.post(ajaxurl, {action :'wppizza_admin_meal_sizes_ajax',vars:{'field':'sizes','allKeys': allKeys, 'newFields':newFields}}, function(response) {
					var html=response;
					$('#wppizza_sizes_options').append(html);
					self.prop( "disabled", false  );
				},'html').error(function(jqXHR, textStatus, errorThrown) {alert("error : " + errorThrown);});
			}
	});
	/*****************************
	*	[remove an option]
	*****************************/
	$(document).on('click', '.wppizza-delete', function(e){
		e.preventDefault();
		var self=$(this);
		/**we must have at least one size option**/
		var noOfSizes=$('.wppizza_sizes_option').length;
		if(noOfSizes<=1){
			alert('Sorry, at least one size option must be defined');
			return;
		}
		$(this).closest('.wppizza_sizes_option').empty().remove();
	});
});