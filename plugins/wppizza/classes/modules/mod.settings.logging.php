<?php
/**
* WPPIZZA_MODULE_SETTINGS_LOGGING Class
*
* @package     WPPIZZA
* @subpackage  WPPIZZA_MODULE_SETTINGS_LOGGING
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
class WPPIZZA_MODULE_SETTINGS_LOGGING{

	private $settings_page = 'settings';/* which admin subpage (identified there by this->class_key) are we adding this to */


	private $section_key = 'logging';/* must be unique */


	function __construct() {
		/**********************************************************
			[add settings to admin]
		***********************************************************/
		if(is_admin()){
			/* add admin options settings page*/
			add_filter('wppizza_filter_settings_sections_'.$this->settings_page.'', array($this, 'admin_options_settings'), 90, 5);
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
			$settings['sections'][$this->section_key] =  __('Logging', 'wppizza-admin');
		}
		/*help*/
		if($help){
			$settings['help'][$this->section_key][] = array(
				'label'=>__('Logging Options', 'wppizza-admin'),
				'description'=>array(
					__('Please adjust settings as appropriate according to the information provided next to each individual option.', 'wppizza-admin')
				)
			);
		}
		/*fields*/
		if($fields){
			$field = 'log_failed_orders';
			$settings['fields'][$this->section_key][$field] = array( __('Log failed orders', 'wppizza-admin'), array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('Do you wish to enable error logging ? [logfiles will be located in the /logs/ directory]', 'wppizza-admin'),
				'description'=>array()
			));	
			$field = 'send_failed_orders_to_admin';
			$settings['fields'][$this->section_key][$field] = array( __('Errors to admin email', 'wppizza-admin'), array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('It might be a good idea to have all non-verified transactions or other transaction errors sent to your administrators email address to investigate. You can turn this off if you are happy everything works. If you enable logging, all those occurences will be logged regardless.', 'wppizza-admin').'<br/><span class="wppizza-highlight">'.__('Note: Critical errors will always be sent to admin email, regardless of the settings here', 'wppizza-admin').'</span>',
				'description'=>array()
			));		
			$field = 'log_successful_orders';
			$settings['fields'][$this->section_key][$field] = array( __('Log successful orders', 'wppizza-admin'), array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('If you wish, enable this to log all successful orders too. [logfiles will be located in the /logs/ directory]', 'wppizza-admin'),
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
		
		if($field=='log_failed_orders'){
			echo "<label>";
				echo "<input id='".$field."' name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' type='checkbox'  ". checked($wppizza_options[$options_key][$field],true,false)." value='1' />";
				echo "" . $label . "";
			echo "</label>";
			echo"".$description."";
		}

		if($field=='send_failed_orders_to_admin'){
			echo "<label>";
				echo "<input id='".$field."' name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' type='checkbox'  ". checked($wppizza_options[$options_key][$field],true,false)." value='1' />";
				echo "" . $label . "";
			echo "</label>";
			echo"".$description."";
		}
		
		if($field=='log_successful_orders'){
			echo "<label>";
				echo "<input id='".$field."' name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' type='checkbox'  ". checked($wppizza_options[$options_key][$field],true,false)." value='1' />";
				echo "" . $label . "";
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

		$options[$this->settings_page]['log_failed_orders'] = true;
		$options[$this->settings_page]['send_failed_orders_to_admin'] = true;		
		$options[$this->settings_page]['log_successful_orders'] = false;				

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
		*	[validate]
		********************************/
		if(isset($_POST[''.WPPIZZA_SLUG.'_'.$this->settings_page.''])){			
			$options[$this->settings_page]['log_failed_orders'] = !empty($input[$this->settings_page]['log_failed_orders']) ? true : false;			
			$options[$this->settings_page]['send_failed_orders_to_admin'] = !empty($input[$this->settings_page]['send_failed_orders_to_admin']) ? true : false;			
			$options[$this->settings_page]['log_successful_orders'] = !empty($input[$this->settings_page]['log_successful_orders']) ? true : false;	
		}
	return $options;
	}
}
/***************************************************************
*
*	[ini]
*
***************************************************************/
$WPPIZZA_MODULE_SETTINGS_LOGGING = new WPPIZZA_MODULE_SETTINGS_LOGGING();
?>