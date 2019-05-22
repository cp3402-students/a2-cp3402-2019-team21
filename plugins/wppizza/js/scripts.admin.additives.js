jQuery(document).ready(function($){
	/******************************
	*	[additives - add new]
	******************************/
	$(document).on('click', '#wppizza_add_additives', function(e){
		e.preventDefault();
		var self=$(this);
		self.prop( "disabled", true );/* disable add button */
		var allKeys=$('.wppizza-getkey');
		if(allKeys.length>0){
			var setKeys=allKeys.serializeArray();
		}else{
			var setKeys='';
		}
		jQuery.post(ajaxurl , {action :'wppizza_admin_additives_ajax',vars:{'field':'additives', 'setKeys': setKeys }}, function(response) {		
			$('#wppizza_additives_options').append(response);
			self.prop( "disabled", false  );
		},'html').error(function(jqXHR, textStatus, errorThrown) {alert("error : " + errorThrown);});
	});
	/*****************************
	*	[remove an option]
	*****************************/
	$(document).on('click', '.wppizza-delete', function(e){
		e.preventDefault();
		$(this).closest('.wppizza_additives_option').empty().remove();
	});
});