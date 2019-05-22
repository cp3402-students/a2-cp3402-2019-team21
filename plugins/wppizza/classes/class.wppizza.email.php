<?php
/**
* WPPIZZA_EMAIL Class
*
* @package     WPPIZZA
* @subpackage  WPPIZZA_EMAIL
* @copyright   Copyright (c) 2015, Oliver Bach
* @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
* @since       3.0
*
*/
if ( ! defined( 'ABSPATH' ) ) exit;/*Exit if accessed directly*/


/************************************************************************************************************************
*
*
*
*
*
************************************************************************************************************************/
class WPPIZZA_EMAIL{


	function __construct() {

			/*
				maybe use wppizza smtp settings on mails sent by wppizza
			*/
			add_filter( 'wp_mail', array($this, 'maybe_use_smtp_on_wppizza_mails' ));

			/*
				php mailer - log_mailer_errors
			*/
			add_action('wp_mail_failed', array($this, 'log_mailer_errors'), 10, 1);

	}

	/*************************************************
		conditionally use smtp on mails send by wppizza
	*************************************************/
	function maybe_use_smtp_on_wppizza_mails($mail){

		/*
			check for wppizza header in emails that are being sent
			and use smtp settings if enabled
		*/
		if(!empty($mail['headers']) && is_array($mail['headers']) && in_array(WPPIZZA_CUSTOM_HEADER_EMAIL, $mail['headers'])){

			/*
				php mailer - using smtp
			*/
			add_action('phpmailer_init', array($this, 'use_smtp'));
			/*
				php mailer - set alt body (if is html)
			*/
			add_action('phpmailer_init', array($this, 'set_html_mail_body'));
		}

	return $mail;
	}


	/*************************************************
		set to test mode to not init set_html_mail_body
	*************************************************/
	function isTest(){
		$this->isTest=true;
	}
	/*************************************************
		wrapper function
	*************************************************/
	function send($emails_to_send, $order_formatted){
		/*
			setup phpmailer and send emails one by one as required
		*/
		$results = $this->send_emails($emails_to_send, $order_formatted);
	return $results;
	}

	/*************************************************
		sending the emails returning error array
		(which wiil be empty if all goes well)
	*************************************************/
	function send_emails($emails_to_send, $order_formatted){
		global $wppizza_options;

		/***************************
		*	ini empty errors array
		****************************/
		$mail_errors = array();

		/*******************************************************************************************
		*
		*	loop through emails_to_send
		*
		********************************************************************************************/
		foreach($emails_to_send as $recipient_key => $email_settings){


			/*
				allow filtering of email_settings
				@since 3.9
			*/
			$email_settings = apply_filters('wppizza_filter_email_settings', $email_settings, $recipient_key, $order_formatted);


			/************
				ini headers
			*************/
			$wp_mail_headers=array();


			/**********************************
			*	set content type header
			**********************************/
			if(empty($email_settings['MsgHTML'])){
				$wp_mail_headers[] = 'Content-Type: text/plain; charset='.WPPIZZA_CHARSET.'';
			}else{
				$wp_mail_headers[] = 'Content-Type: text/html; charset='.WPPIZZA_CHARSET.'';
			}

			/************************************************
				from
			************************************************/
			$wp_mail_headers[] = 'From: '.$email_settings['SetFrom']['name'].' <'.$email_settings['SetFrom']['email'].'>';

			/************************************************
				to / recipient
			************************************************/
			if(!empty($email_settings['AddAddress'])){

				$recipients['email']  = array();
				//$recipients['name']  = array();/* currently unused */

				foreach($email_settings['AddAddress'] as $k=>$recipient){
					$recipients['email'][]  = $recipient['email'];
					//$recipients['name'][]  = $recipient['name'];
				}

			$wp_mail_recipients=implode(',',$recipients['email']);

			/* allow filtering of recipients */
			$wp_mail_recipients = apply_filters('wppizza_filter_mail_recipients', $wp_mail_recipients, $recipient_key, $email_settings, $order_formatted);
			}

			/************************************************
				reply to
			************************************************/
			if(!empty($email_settings['AddReplyTo'])){
				//$email_settings['AddReplyTo']['name'] //unused
				$wp_mail_headers[]= 'Reply-To: '.$email_settings['AddReplyTo']['email'].'';
			}

			/************************************************
				cc(s)
			*************************************************/
			if(!empty($email_settings['AddCC'])){

				$ccs['email']  = array();
				foreach($email_settings['AddCC'] as $k=>$cc){
					/** $cc might just be the email address */
					if(!empty($cc['email'])){
						$ccs['email'][]  = $cc['email'];
					}else{
						$ccs['email'][]  = $cc;
					}
				}

				$wp_mail_headers[]= 'Cc: '.implode(",",$ccs['email']).'';
			}

			/************************************************
				bcc(s)
			*************************************************/
			if(!empty($email_settings['AddBCC'])){

				$bccs['email']  = array();

				foreach($email_settings['AddBCC'] as $k=>$bcc){
					/** $bcc might just be the email address */
					if(!empty($bcc['email'])){
						$bccs['email'][]  = $bcc['email'];
					}else{
						$bccs['email'][]  = $bcc;
					}
				}

				$wp_mail_headers[]= 'Bcc: '.implode(",",$bccs['email']).'';
			}
			/* allow filtering of headers */
			$wp_mail_headers = apply_filters('wppizza_filter_mail_headers', $wp_mail_headers, $recipient_key, $email_settings, $order_formatted);

			/************************************************
				add custom header so we know it's an email send by wppizza
			************************************************/
			$wp_mail_headers[] = WPPIZZA_CUSTOM_HEADER_EMAIL ;


			/************************************************
				attachments
			*************************************************/
			$wp_mail_attachments = array();
			if(!empty($email_settings['AddAttachment'])){
			foreach($email_settings['AddAttachment'] as $attachment){
				$wp_mail_attachments[]=$attachment;
			}}
			/* allow filtering */
			$wp_mail_attachments = apply_filters('wppizza_filter_mail_attachments', $wp_mail_attachments, $recipient_key, $email_settings, $order_formatted);

			/************************************************
				subject - as set/filtered
			************************************************/
			$email_subject = $email_settings['Subject'];

			/***********************************************
				set the html body. use only appropriate html/plaintext
				part hereto be overridden by phpmailer_init action hook
				into body and altbody

				provided some other (mail/smtp) plugin has not
				plugged the wp_mail function omitting the
				phpmailer_init hook, in which case, at least we
				have somthing useable as message
			************************************************/
			if(empty($email_settings['MsgHTML'])){
				$message = $email_settings['AltBody'];
			}else{
				$message = $email_settings['MsgHTML'];
			}

			/***********************************************
				set plaintext and html markup we can  use
				in phpmailer_init action hook
			************************************************/
			$this->message_body['plaintext'] = $email_settings['AltBody'];
			if(!empty($email_settings['MsgHTML'])){
				$this->message_body['html'] = $email_settings['MsgHTML'];
			}

			/************************************************
				send mails
			*************************************************/
			/*disable actual sending if disable_emails set */
			if(!empty($wppizza_options['tools']['disable_emails'])){
				//$mail_errors['status']=true;
			}else{
				if(wp_mail($wp_mail_recipients, $email_subject, $message, $wp_mail_headers, $wp_mail_attachments)) {
					//$send_email_results['status']=true;
					/* do something more when email was sent */
					do_action('wppizza_on_email_sent', $recipient_key, $email_settings, $order_formatted);
				}else{
					//$send_email_results['status']=false;
					$mail_errors[$recipient_key] = $GLOBALS['phpmailer']->ErrorInfo;
					$error_get_last=error_get_last();
					if(!empty($error_get_last)){
						$mail_errors[$recipient_key].=' | '.print_r($error_get_last,true);/**sometimes there's somthing in tha variable too*/
					}
					// add full info
					$FullInfo = ' | Recipients: '.print_r($wp_mail_recipients,true). ' | Subject: '.print_r($email_subject,true).' | Headers: '.esc_html(print_r($wp_mail_headers,true)).'';
					$mail_errors[$recipient_key].= $FullInfo;
				}
			}

		}

	return $mail_errors;
	}
	/*********************************************************************************
	*
	*	[HTML EMAILS : set alt body too in html emails]
	*
	********************************************************************************/
	function set_html_mail_body($phpmailer){

		/*
			stop setting and unsetting vars when in test mode
		*/
		if(!empty($this->isTest)){

			// Always remove self at the end
    		remove_action( 'phpmailer_init', __function__ );
			return;
		}

		if( $phpmailer->ContentType == 'text/html' ) {
			if(!empty($this->message_body['html'])){
				/** if MsgHTML is set, phpmailer will automatically set this as phpmailer->Body */
				$phpmailer->MsgHTML($this->message_body['html']);
			}
			if(!empty($this->message_body['plaintext'])){
				$phpmailer->AltBody = $this->message_body['plaintext']; // optional - MsgHTML will create an alternate automatically, however this has been prettied up a little for this plugin. If you must, feel free to comment this line out though
			}

		}else{// plaintext emails

			// unset MsgHTML
			if(isset($phpmailer->MsgHTML)){
				unset($phpmailer->MsgHTML);
			}
			// unset any previous AltBody
			if(isset($phpmailer->AltBody)){
				unset($phpmailer->AltBody);
			}
			if(!empty($this->message_body['plaintext']) ){
				// make sure not to replace WP native plaintext registration notices ($phpmailer->Body) when user registers account at checkout!!
				$phpmailer->Body = !empty($phpmailer->Body) ? $phpmailer->Body :  $this->message_body['plaintext'];
				$phpmailer->AltBody = '';
			}
		}

		// Always remove self at the end
    	remove_action( 'phpmailer_init', __function__ );
	}

	/*********************************************************************************
	*
	*	[SMTP : send emails by smtp according to settings set]
	*
	********************************************************************************/
	function use_smtp($phpmailer){
		global $wppizza_options;

		/*
			testing smtp
		*/
		$testing_smtp = (!empty($_POST['vars']['field']) && $_POST['vars']['field']=='wppizza_smtp_test') ? true : false;

		/*
			skip if not enabled and we are not just testing
		*/
		if(empty($wppizza_options['settings']['smtp_enable']) && !$testing_smtp){
			// Always remove self at the end
    		remove_action( 'phpmailer_init', __function__ );
			return $phpmailer;
		}

		/*force smtp*/
		$phpmailer->isSMTP();

		/******************************************************************************************
			using admin test vars

			no doubt this could be done a lot more elegantly, but for the time being, this will do
			if we are testing smpt connections, add some settings
		*****************************************************************************************/
		if($testing_smtp){
		    $phpmailer->Timeout  	=   7;	/*test should really work with in 5-10 secs timeout*/
			$phpmailer->Host 		= $_POST['vars']['smtp_parameters']['smtp_host'];
			$phpmailer->Port 		= $_POST['vars']['smtp_parameters']['smtp_port'];
			$phpmailer->SMTPAuth 	= empty($_POST['vars']['smtp_parameters']['smtp_authentication']) ? false : true;
			$phpmailer->SMTPSecure 	= (!empty($_POST['vars']['smtp_parameters']['smtp_encryption'])) ? $_POST['vars']['smtp_parameters']['smtp_encryption'] : false;
			$phpmailer->Username 	= $_POST['vars']['smtp_parameters']['smtp_username'];
			$phpmailer->Password 	= (!empty($_POST['vars']['smtp_parameters']['smtp_password'])) ? $_POST['vars']['smtp_parameters']['smtp_password'] : wppizza_encrypt_decrypt($wppizza_options['settings']['smtp_password'],false);
			$phpmailer->SMTPDebug 	= 3; //Set SMTPDebug distincly for testing

		}else{
			//$phpmailer->Timeout  	=   5;
			$phpmailer->Host 		= $wppizza_options['settings']['smtp_host'];
			$phpmailer->Port 		= $wppizza_options['settings']['smtp_port'];
			$phpmailer->SMTPAuth 	= empty($wppizza_options['settings']['smtp_authentication']) ? false : true;
			$phpmailer->SMTPSecure  = (!empty($wppizza_options['settings']['smtp_encryption'])) ? $wppizza_options['settings']['smtp_encryption'] : false;
			$phpmailer->Username = $wppizza_options['settings']['smtp_username'];
			$phpmailer->Password  = wppizza_encrypt_decrypt($wppizza_options['settings']['smtp_password'],false);
			$phpmailer->SMTPDebug  = !empty($wppizza_options['settings']['smtp_debug']) ? 3 : false;
		}

		// Always remove self at the end
    	remove_action( 'phpmailer_init', __function__ );

	return $phpmailer;
	}
	/*********************************************************************************
	*
	*	[EMAILS : log mail errors]
	*
	********************************************************************************/
	function log_mailer_errors($wp_error){
  		$error_log_file = WPPIZZA_PATH_LOGS . 'mail-error-'.wp_hash('wppizzamail').'.log';
		file_put_contents($error_log_file,'['.date('Y-m-d H:i:s').']: ' . print_r($wp_error->errors,true).''.PHP_EOL, FILE_APPEND);
	}
}
?>