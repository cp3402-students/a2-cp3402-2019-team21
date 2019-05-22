<?php
/**
* WPPIZZA_MODULE_ORDERSETTINGS_EMAILS Class
*
* @package     WPPIZZA
* @subpackage  WPPIZZA_MODULE_ORDERSETTINGS_EMAILS
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
class WPPIZZA_MODULE_ORDERSETTINGS_EMAILS{

	private $settings_page = 'order_settings';/* which admin subpage (identified there by this->class_key) are we adding this to */

	private $section_key = 'emails';/* must be unique */


	function __construct() {
		/**********************************************************
			[add settings to admin]
		***********************************************************/
		if(is_admin()){
			/* add admin options settings page*/
			add_filter('wppizza_filter_settings_sections_'.$this->settings_page.'', array($this, 'admin_options_settings'), 80, 5);
			/* add admin options settings page fields */
			add_action('wppizza_admin_settings_section_fields_'.$this->settings_page.'', array($this, 'admin_options_fields_settings'), 10, 5);
			/**add default options **/
			add_filter('wppizza_filter_setup_default_options', array( $this, 'options_default'));
			/**validate options**/
			add_filter('wppizza_filter_options_validate', array( $this, 'options_validate'), 10, 2 );
		}

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
	/*******************************************************************************************************************************************************
	*
	*
	*
	* 	[add admin page options]
	*
	*
	*
	********************************************************************************************************************************************************/

	/*------------------------------------------------------------------------------
	#
	#
	#	[settings page]
	#
	#
	------------------------------------------------------------------------------*/

	/*------------------------------------------------------------------------------
	#	[settings section - setting page]
	#	@since 3.0
	#	@return array()
	------------------------------------------------------------------------------*/
	function admin_options_settings($settings, $sections, $fields, $inputs, $help){

		/*section*/
		if($sections){
			$settings['sections'][$this->section_key] = __('Emails', 'wppizza-admin');
		}
		/*help*/
		if($help){
			$settings['help'][$this->section_key][] = array(
				'label'=>__('"From" email address:', 'wppizza-admin'),
				'description'=>array(
					__('All emails will appear to have been sent from this address. (Some fax gateways for example require a distinct FROM email address). However, the customers email address will still be stored in the db/order history if entered', 'wppizza-admin')
				)
			);
			$settings['help'][$this->section_key][] = array(
				'label'=>__('Attachments', 'wppizza-admin'),
				'description'=>array(
					__('If you wish to add an attachment to the order emails add the FULL ABSOLUTE PATH to the file(s) you wish to attach to the emails', 'wppizza-admin')
				)
			);
			$settings['help'][$this->section_key][] = array(
				'label'=>__('DMARC', 'wppizza-admin'),
				'description'=>array(
					__('If a DMARC notice/warning is displayed at the top of the page and you are certain you are happy with your email settings as they are, you can enable and save this option to forcefully hide/disable this notice.', 'wppizza-admin')
				)
			);
		}
		/*fields*/
		if($fields){
			$field = 'order_email_to';
			$settings['fields'][$this->section_key][$field] = array( __('Order Email Recipient(s)', 'wppizza-admin'), array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=> __('Which email address should any orders be sent to [separated by comma if multiple]', 'wppizza-admin'),
				'description'=>array()
			));
			$field = 'order_email_bcc';
			$settings['fields'][$this->section_key][$field] = array( __('Order Email Bcc\'s', 'wppizza-admin'), array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('If you would like to BCC order emails add these here [separated by comma if multiple]', 'wppizza-admin'),
				'description'=>array()
			));
			$field = 'order_email_from';
			$settings['fields'][$this->section_key][$field] = array( __('"From" email address', 'wppizza-admin'), array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('All emails will appear to have been sent from this address.', 'wppizza-admin'),
				'description'=>array(
					'<span class="wppizza-highlight">'.__('You are strongly advised to set an email here that exists and corresponds to the domain of this server', 'wppizza-admin').'</span>'
				)
			));
			$field = 'order_email_from_name';
			$settings['fields'][$this->section_key][$field] = array( __('"From" email name', 'wppizza-admin'), array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('You can set a static "name" here (in conjunction with any "From" email address set above). If left empty, the email address will be used.', 'wppizza-admin').'',
				'description'=>array()
			));
			$field = 'order_email_attachments';
			$settings['fields'][$this->section_key][$field] = array( __('Email Attachments', 'wppizza-admin'), array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('Email Attachments [separated by comma if multiple]:', 'wppizza-admin').'',
				'description'=>array(
					''.__('Your absolute WP Path', 'wppizza-admin').': '. substr(ABSPATH,0,-1) .''
				)
			));
			$field = 'dmarc_nag_off';
			$settings['fields'][$this->section_key][$field] = array( __('Turn Off DMARC Notice', 'wppizza-admin'), array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('Disable DMARC notice', 'wppizza-admin').'',
				'description'=>array()
			));
		}


		return $settings;
	}
	/*------------------------------------------------------------------------------
	#	[output option fields - setting page]
	#	@since 3.0
	#	@return array()
	------------------------------------------------------------------------------*/
	function admin_options_fields_settings($wppizza_options, $options_key, $field, $label, $description){

		/********************************
		*	[Emails / Attachments]
		********************************/
		if($field=='order_email_to' || $field=='order_email_bcc' ){//$field==order_sms => not implemented
			echo "<label>";
				if(is_array($wppizza_options[$options_key][$field])){$val=implode(",",$wppizza_options[$options_key][$field]);}else{$val='';}
				echo "<input id='".$field."' name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' size='30' type='text' value='".$val."' />";
				echo "".$label."";
			echo "</label>";
			echo"".$description."";
		}
		if($field=='order_email_attachments'){
			echo "<label>";
				if(isset($wppizza_options[$options_key][$field]) && is_array($wppizza_options[$options_key][$field])){$val=implode(",",$wppizza_options[$options_key][$field]);}else{$val='';}
				echo "<input id='".$field."' name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' size='30' type='text' value='".$val."' placeholder='/absolute/path/to/your/file'/>";
				echo "".$label."";
			echo "</label>";
			echo"".$description."";
		}
		if($field=='order_email_from'){
			echo "<label>";
				echo "<input id='".$field."' name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' size='30' type='text' value='".$wppizza_options[$options_key][$field]."' />";
				echo "".$label."";
			echo "</label>";
			echo"".$description."";
		}
		if($field=='order_email_from_name'){
			echo "<label>";
				echo "<input id='".$field."' name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' size='30' type='text' value='".$wppizza_options[$options_key][$field]."' />";
				echo "".$label."";
			echo "</label>";
			echo"".$description."";
		}
		if($field=='dmarc_nag_off'){
			echo "<label>";
				echo" <input name='".WPPIZZA_SLUG."[".$options_key."][dmarc_nag_off]' type='checkbox'  ". checked($wppizza_options[$options_key]['dmarc_nag_off'],true,false)." value='1' /> ";
				echo "".$label."";
			echo "</label>";
			echo"".$description."";
		}

	}

	/*------------------------------------------------------------------------------
	#	[insert default option on install]
	#	$parameter $options array() | filter passing on filtered options
	#	@since 3.0
	#	@return array()
	------------------------------------------------------------------------------*/
	function options_default($options){

		$options[$this->settings_page]['order_email_to'] = array(''.get_option('admin_email').'');
		$options[$this->settings_page]['order_email_bcc'] = array();
		$options[$this->settings_page]['order_email_attachments'] = array();
		$options[$this->settings_page]['order_email_from'] = get_option('admin_email');
		$options[$this->settings_page]['order_email_from_name'] = get_option('blogname');
		$options[$this->settings_page]['dmarc_nag_off'] = false;


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
		/*
			settings
		*/
		if(isset($_POST[''.WPPIZZA_SLUG.'_'.$this->settings_page.''])){
			$options[$this->settings_page]['order_email_to'] = wppizza_validate_email_array($input[$this->settings_page]['order_email_to']);
			$options[$this->settings_page]['order_email_bcc'] = wppizza_validate_email_array($input[$this->settings_page]['order_email_bcc']);
			$options[$this->settings_page]['order_email_attachments'] = wppizza_strtoarray($input[$this->settings_page]['order_email_attachments']);
			$emailFrom=wppizza_validate_email_array($input[$this->settings_page]['order_email_from']);/*validated as array but we only store the first value as string*/
			$options[$this->settings_page]['order_email_from'] = !empty($emailFrom[0]) ? ''.$emailFrom[0].'' : '' ;
			$options[$this->settings_page]['order_email_from_name'] = wppizza_validate_string($input[$this->settings_page]['order_email_from_name']);
			/**dmarc nag**/
			$options[$this->settings_page]['dmarc_nag_off']= !empty($input[$this->settings_page]['dmarc_nag_off']) ? true : false;
		}

	return $options;
	}
}
/***************************************************************
*
*	[ini]
*
***************************************************************/
$WPPIZZA_MODULE_ORDERSETTINGS_EMAILS = new WPPIZZA_MODULE_ORDERSETTINGS_EMAILS();
?>