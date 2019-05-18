<?php
/**
* WPPIZZA_MODULE_SMTP Class
*
* @package     WPPIZZA
* @subpackage  WPPIZZA_MODULE_SMTP
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
*
************************************************************************************************************************/
class WPPIZZA_MODULE_SMTP{

	private $settings_page = 'settings';/* which admin subpage (identified there by this->class_key) are we adding this to */


//	private $layout_page = 'layout';/* which admin subpage (identified there by this->class_key) are we adding this to */
//	private $layout_position = 3;/* at which admin position (section) should this appear on above admin subpage, integer(zero indexed - for numeric position) or key ('layout-style' for example) after which it should appear*/

//	private $localization_page = 'localization';/* which admin subpage (identified there by this->class_key) are we adding this to */
//	private $localization_position = 'itemised-order';/* at which admin position (section) should this appear on above admin subpage, integer(zero indexed - for numeric position) or key ('layout-style' for example) after which it should appear*/


	private $section_key = 'smtp';/* must be unique */


	function __construct() {
		/**********************************************************
			[add settings to admin]
		***********************************************************/
		if(is_admin()){
			/* add admin options settings page*/
			add_filter('wppizza_filter_settings_sections_'.$this->settings_page.'', array($this, 'admin_options_settings'), 100, 5);
			/* add admin options settings page fields */
			add_action('wppizza_admin_settings_section_fields_'.$this->settings_page.'', array($this, 'admin_options_fields_settings'), 10, 5);

			/**add default options **/
			add_filter('wppizza_filter_setup_default_options', array( $this, 'options_default'));
			/**validate options**/
			add_filter('wppizza_filter_options_validate', array( $this, 'options_validate'), 10, 2 );

			/** admin ajax **/
			add_action('wppizza_ajax_admin_'.$this->settings_page.'', array( $this, 'admin_ajax'));
		}
		/**********************************************************
			[filter/actions depending on settings]
		***********************************************************/
		/* add smtp settings if renabled */
		//add_action('wppizza_phpmailer_settings', array($this, 'use_smtp'));
	}

	/*******************************************************************************************************************************************************
	*
	*
	*
	* 	[frontend filters]
	*
	*
	*
	********************************************************************************************************************************************************/
	/*************************************

		using smtp if enabled

	**************************************/
//	function use_smtp($phpmailer){
//		global $wppizza_options;
//		/************************************************
//			send with smtp if enabled
//		*************************************************/
//		if(!empty($wppizza_options[$this->section_key]['smtp_enable'])){
//			/*force smtp*/
//			$phpmailer->isSMTP();
//			$phpmailer->Host 		= $wppizza_options[$this->section_key]['smtp_host'];
//			$phpmailer->Port 		= $wppizza_options[$this->section_key]['smtp_port'];
//			$phpmailer->SMTPAuth 	= empty($wppizza_options[$this->section_key]['smtp_authentication']) ? false : true;
//			$phpmailer->SMTPSecure  = (!empty($wppizza_options[$this->section_key]['smtp_encryption'])) ? $wppizza_options[$this->section_key]['smtp_encryption'] : false;
//			$phpmailer->Username 	= $wppizza_options[$this->section_key]['smtp_username'];
//			$phpmailer->Password  	= wppizza_encrypt_decrypt($wppizza_options[$this->section_key]['smtp_password'],false);
//			$phpmailer->SMTPDebug  = !empty($wppizza_options[$this->section_key]['smtp_debug']) ? 3 : false;
//		}
//	}

	/*************************************

		testing smtp

	**************************************/
	function test_smtp($smtp_parameters){
		global $phpmailer; // define the global variable

		/* set smtp vars and test mode*/
		$email_results = WPPIZZA()->email->isTest();

		// Set up the mail variables
		$email = $smtp_parameters['smtp_email'];
		$subject = sprintf(__('%s SMTP Test: Test mail to ', 'wppizza-admin'),WPPIZZA_NAME) . $email;
		$message = sprintf(__('%s SMTP test email message from "%s" site', 'wppizza-admin'),WPPIZZA_NAME, get_bloginfo('name')).'';
		$headers = array(WPPIZZA_CUSTOM_HEADER_EMAIL);/* add custom header so we know it's an email send by wppizza */

		/**
			Start output buffering  for debugging output
		**/
		ob_start();

		/**Try sending test mail**/
		$mailResult = (wp_mail($email, $subject, $message, $headers)) ? true : false ;

		if(!$mailResult){

			$result['error'] = '<b style="color:red">FAIL - check details below</b>'.PHP_EOL;

			/*error info*/
			$result['error-info'] = array();

			/*last error*/
			$phpMailerLastError=error_get_last();
			if(!empty($phpMailerLastError)){
				$result['error-info']['last-error'] = $phpMailerLastError;
			}

			/*phpmailer errors*/
			$phpMailerError=$phpmailer->ErrorInfo;//. ' | '.$phpmailer->errorMessage;
			if(!empty($phpMailerError)){
				$result['error-info']['phpmailer']=''.print_r($phpMailerError,true).'';/**sometimes there's somthing in that variable too*/
			}

		}else{
			$result['success'] = '<b style="color:blue">SUCCESS - email sent. Check inbox of '.$email.' and do not forget to "Save Changes"</b>'.PHP_EOL;
		}

		/**smtp debugging output**/
		$result['debug'] = ob_get_clean();

	/**return array for display**/
	print"".print_r($result,true)."";
	exit();
	}

	/*******************************************************************************************************************************************************
	*
	*
	*
	* 	[add admin page options]
	*
	*
	*
	********************************************************************************************************************************************************/
	/*******************************************************************************************************************************************************
	*
	* 	[admin ajax]
	*
	********************************************************************************************************************************************************/
	function admin_ajax($wppizza_options){

		/*****************************************************
			[test smtp settings]
		*****************************************************/
		if($_POST['vars']['field']=='wppizza_smtp_test'){
			$smtp_test_results = $this->test_smtp($_POST['vars']['smtp_parameters']);

			/**return array for display**/
			print"".$smtp_test_results."";

		exit();
		}
	}
	/*------------------------------------------------------------------------------
	#
	#
	#	[settings page]
	#
	#
	------------------------------------------------------------------------------*/
	/****************************************************************
	*	[settigs section  - setting page]
	*	@since 3.0
	*	@return array()
	****************************************************************/
	function admin_options_settings($settings, $sections, $fields, $inputs, $help){

		/********************************
		*	[use smtp]
		********************************/
		/*sections*/
		if($sections){
			$settings['sections'][$this->section_key] =  __('use SMTP', 'wppizza-admin');
		}
		/*help*/
		if($help){
			$settings['help'][$this->section_key][] = array(
				'label'=>sprintf(__('use SMTP for sending %s related emails', 'wppizza-admin'),WPPIZZA_NAME),
				'description'=>array(
					sprintf(__('To use SMTP for any %s related emails, set the values as appropriate.', 'wppizza-admin'),WPPIZZA_NAME),
					__('Make sure you test your settings by using the SMTP test provided.', 'wppizza-admin'),
					'<span class="wppizza-highlight-important">'.sprintf(__('Please note, only %s emails will be affected', 'wppizza-admin'), WPPIZZA_NAME).'</span>'
				)
			);
		}
		/*fields*/
		if($fields){
			$field = 'smtp_enable';
			$settings['fields'][$this->section_key][$field] = array( __('Use SMTP', 'wppizza-admin'), array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>sprintf(__('check to use SMTP when sending %s related emails', 'wppizza-admin'), WPPIZZA_NAME),
				'description'=>array()
			));
			$field = 'smtp_host';
			$settings['fields'][$this->section_key][$field] = array( __('SMTP Host', 'wppizza-admin'), array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>'',
				'description'=>array()
			));
			$field = 'smtp_port';
			$settings['fields'][$this->section_key][$field] = array( __('SMTP Port', 'wppizza-admin'), array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>'',
				'description'=>array()
			));
			$field = 'smtp_encryption';
			$settings['fields'][$this->section_key][$field] = array( __('SMTP Encryption', 'wppizza-admin'), array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>'',
				'description'=>array()
			));
			$field = 'smtp_authentication';
			$settings['fields'][$this->section_key][$field] = array( __('SMTP Authentication', 'wppizza-admin'), array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('check to enable authentication', 'wppizza-admin'),
				'description'=>array()
			));
			$field = 'smtp_username';
			$settings['fields'][$this->section_key][$field] = array( __('SMTP Username', 'wppizza-admin'), array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>'',
				'description'=>array()
			));
			$field = 'smtp_password';
			$settings['fields'][$this->section_key][$field] = array( __('SMTP Password', 'wppizza-admin'), array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>'',
				'description'=>array()
			));
			$field = 'smtp_debug';
			$settings['fields'][$this->section_key][$field] = array( __('SMTP Debug', 'wppizza-admin'), array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('This must be OFF on production servers. Development/Testing only. (Will output full smtp connection log in your browsers console)', 'wppizza-admin'),
				'description'=>array()
			));
			$field = 'smtp_test';
			$settings['fields'][$this->section_key][$field] = array( __('SMTP Test', 'wppizza-admin'), array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('To test your smtp settings, enter your email address on the left and <a href="javascript:void(0)" id="wppizza_smtp_test">click here</a>. results will appear below', 'wppizza-admin'),
				'description'=>array()
			));
		}


		return $settings;
	}
	/****************************************************************
	*	[output option fields - setting page]
	*	@since 3.0
	*	@return array()
	****************************************************************/
	function admin_options_fields_settings($wppizza_options, $options_key, $field, $label, $description){

		/*************************************
			[smtp input fields]
		*************************************/
		if($field=='smtp_enable'){
			echo "<label>";
				echo "<input id='".$field."' name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' type='checkbox'  ". checked($wppizza_options[$options_key][$field],true,false)." value='1' />";
				echo "" . $label . "";
			echo "</label>";
			echo"".$description."";
		}
		if($field=='smtp_host'){
			echo "<label>";
				echo"<input  id='wppizza_".$field."' name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' type='text' value='".$wppizza_options[$options_key][$field]."' />";
				echo "" . $label . "";
			echo "</label>";
			echo"".$description."";
		}
		if($field=='smtp_port'){
			echo "<label>";
				echo"<input id='wppizza_".$field."' name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' size='3' type='text' value='".$wppizza_options[$options_key][$field]."' />";
				echo "" . $label . "";
			echo "</label>";
			echo"".$description."";
		}
		if($field=='smtp_encryption'){
			echo "<label>";
				echo"<select id='wppizza_".$field."' name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' />";
				echo"<option value='' ".selected($wppizza_options[$options_key][$field],"",false).">".__('No Encryption', 'wppizza-admin')."</option>";
				echo"<option value='ssl' ".selected($wppizza_options[$options_key][$field],"ssl",false).">".__('SSL', 'wppizza-admin')."</option>";
				echo"<option value='tls' ".selected($wppizza_options[$options_key][$field],"tls",false).">".__('TLS', 'wppizza-admin')."</option>";
			echo "</select>";
				echo "" . $label . "";
			echo "</label>";
		}

		if($field=='smtp_authentication'){
			echo "<label>";
				echo"<input id='wppizza_".$field."'  name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' type='checkbox'  ". checked($wppizza_options[$options_key][$field],true,false)." value='1' />";
				echo "" . $label . "";
			echo "</label>";
			echo"".$description."";
		}

		if($field=='smtp_username'){
			echo "<label>";
				echo"<input id='wppizza_".$field."' name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' type='text' value='".$wppizza_options[$options_key][$field]."' />";
				echo "" . $label . "";
			echo "</label>";
			echo"".$description."";
		}
		if($field=='smtp_password'){
			/*info string*/
			$SmtpPwPlaceholder=__('no password set','wppizza-admin');
			if(!empty($wppizza_options[$options_key][$field])){$SmtpPwPlaceholder=__('a password has been set. enter a new password to change the current password','wppizza-admin');}

			/*show password if defined*/
			$constant_dev_view_smtp_password = WPPIZZA_DEV_VIEW_SMTP_PASSWORD;/* cast to var for php 5.3 */
			$smtpPW = ( !empty($constant_dev_view_smtp_password) && !empty($wppizza_options[$options_key][$field]) ) ? wppizza_encrypt_decrypt($wppizza_options[$options_key][$field],false) : '';

			echo "<label>";
				echo"<input id='wppizza_".$field."' name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' type='text' value='".$smtpPW."' placeholder='********' />".$SmtpPwPlaceholder."</label>";
			echo"<br />";
			if(!function_exists('openssl_encrypt')){
				echo" <span class='wppizza-highlight'>".__('WARNING: you do not seem to have open ssl installed. your smtp password will be saved in plaintext in your database. this could pose a security risk', 'wppizza-admin')."</span>";
			}else{
				echo" <span class='wppizza-highlight'>".__('Note: if you move or clone this wppizza installation to another wordpress installation, you MUST re-enter and re-save your smtp password there unless your wp-config.php\'s are identical.', 'wppizza-admin')."</span>";
			}
		}
		if($field=='smtp_debug'){
			echo "<label>";
				echo "<input id='".$field."' name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' type='checkbox'  ". checked($wppizza_options[$options_key][$field],true,false)." value='1' />";
				echo "" . $label . "";
			echo "</label>";
		}
		if($field=='smtp_test'){
			echo "<label>";
				echo "To: <input id='wppizza_smtp_test_email' type='text' value='' placeholder='email@domain.com' />";
				echo "" . $label . "";
			echo "</label>";
			echo"".$description."";
			echo"<br />";
			echo"<div id='wppizza_smtp_test_results'><pre></pre></div>";
		}
	}
	/*------------------------------------------------------------------------------
	#	[insert default option on install]
	#	$parameter $options array() | filter passing on filtered options
	#	@since 3.0
	#	@return array()
	------------------------------------------------------------------------------*/
	function options_default($options){
		/********************************
		*	[smtp settings]
		********************************/
		$options[$this->settings_page]['smtp_enable'] = false;
		$options[$this->settings_page]['smtp_host'] = '';
		$options[$this->settings_page]['smtp_port'] = '25';
		$options[$this->settings_page]['smtp_encryption'] = '';
		$options[$this->settings_page]['smtp_authentication'] = false;
		$options[$this->settings_page]['smtp_username'] = '';
		$options[$this->settings_page]['smtp_password'] = '';
		$options[$this->settings_page]['smtp_debug'] =  false;
		/*SMTP Test does not need any option set**/

	return $options;
	}

	/*------------------------------------------------------------------------------
	#	[validate options on save/update]
	#
	#	@since 3.0
	#	@return array()
	------------------------------------------------------------------------------*/

	function options_validate($options, $input){
		/**make sure we get the full array on install/update**/
		if ( empty( $_POST['_wp_http_referer'] ) ) {
			return $input;
		}
		/********************************
		*	[smtp settings]
		********************************/
		if(isset($_POST[''.WPPIZZA_SLUG.'_'.$this->settings_page.''])){
			$options[$this->settings_page]['smtp_enable'] = !empty($input[$this->settings_page]['smtp_enable']) ? true : false;
			$options[$this->settings_page]['smtp_host']=wppizza_validate_string($input[$this->settings_page]['smtp_host']);
			$options[$this->settings_page]['smtp_port']=wppizza_validate_int_only($input[$this->settings_page]['smtp_port']);
			$options[$this->settings_page]['smtp_encryption']=wppizza_validate_string($input[$this->settings_page]['smtp_encryption']);
			$options[$this->settings_page]['smtp_authentication'] = !empty($input[$this->settings_page]['smtp_authentication']) ? true : false;
			$options[$this->settings_page]['smtp_username']=wppizza_validate_string($input[$this->settings_page]['smtp_username']);
			/*lets not override set ones as we will not display it in the input*/
			if(!empty($input[$this->settings_page]['smtp_password'])){
				$options[$this->settings_page]['smtp_password']=wppizza_encrypt_decrypt($input[$this->settings_page]['smtp_password']);
			}
			$options[$this->settings_page]['smtp_debug'] = !empty($input[$this->settings_page]['smtp_debug']) ? true : false;
		}
	return $options;
	}
}
/***************************************************************
*
*	[ini]
*
***************************************************************/
$WPPIZZA_MODULE_SMTP = new WPPIZZA_MODULE_SMTP();
?>