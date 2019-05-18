jQuery(document).ready(function($){

	/******************************
	*	[check smtp settings]
	******************************/
	$(document).on('click', '#wppizza_smtp_test', function(e){
		
		//alert('NOTE - TODO : SET AJAX ABS PATH FOR PHPMAILER AND SEND EMAILS CLASS - DELETE ME WHEN DONE');
		
		e.preventDefault();
		var formInputs=$(this).closest("form").serialize();
		/*make sure it's hidden and empty first*/
		$('#wppizza_smtp_test_results').fadeIn();
		$('#wppizza_smtp_test_results>pre').text('---one moment : testing smtp connection---');
		var parameters={};
		parameters.smtp_email=$('#wppizza_smtp_test_email').val();
		parameters.smtp_host=$('#wppizza_smtp_host').val();
		parameters.smtp_port=$('#wppizza_smtp_port').val();
		if($('#wppizza_smtp_authentication').is(':checked')){
		parameters.smtp_authentication=1;
		}
		parameters.smtp_encryption=$('#wppizza_smtp_encryption').val();
		parameters.smtp_username=$('#wppizza_smtp_username').val();
		parameters.smtp_password=$('#wppizza_smtp_password').val();
		/*we need an email*/
		if(parameters.smtp_email==''){
			$('#wppizza_smtp_test_results').hide();
			alert('please enter an email address');
			return;
		}
		jQuery.post(ajaxurl , {action :'wppizza_admin_settings_ajax',vars:{'field':'wppizza_smtp_test','smtp_parameters':parameters}}, function(response) {
			$('#wppizza_smtp_test_results>pre').html(response);
		},'html').error(function(jqXHR, textStatus, errorThrown) {alert("error : " + errorThrown);});
	});
});