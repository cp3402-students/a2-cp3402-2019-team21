jQuery(document).ready(function($){
	/*****************************
	*	[show gateway settings option]
	*****************************/
	$(document).on('click', '.wppizza-gateway-show-options', function(e){
		e.preventDefault();
		var key=$(this).attr('id').split("-").pop(-1);
		$('.wppizza-fields-gateways').slideUp();
		$('#wppizza-fields-gateways-'+key+'').slideDown();
	});
});