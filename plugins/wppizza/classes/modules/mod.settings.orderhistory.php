<?php
/**
* WPPIZZA_MODULE_SETTINGS_ORDER_HISTORY Class
*
* @package     WPPIZZA
* @subpackage  WPPIZZA_MODULE_SETTINGS_ORDER_HISTORY
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
class WPPIZZA_MODULE_SETTINGS_ORDER_HISTORY{

	private $settings_page = 'settings';/* which admin subpage (identified there by this->class_key) are we adding this to */


	private $section_key = 'order_history';/* must be unique */


	function __construct() {
		/**********************************************************
			[add settings to admin]
		***********************************************************/
		if(is_admin()){
			/* add admin options settings page*/
			add_filter('wppizza_filter_settings_sections_'.$this->settings_page.'', array($this, 'admin_options_settings'), 20, 5);
			/* add admin options settings page fields */
			add_action('wppizza_admin_settings_section_fields_'.$this->settings_page.'', array($this, 'admin_options_fields_settings'), 10, 5);
			/**add default options **/
			add_filter('wppizza_filter_setup_default_options', array( $this, 'options_default'));
			/**validate options**/
			add_filter('wppizza_filter_options_validate', array( $this, 'options_validate'), 10, 2 );
		}
		/**********************************************************
			[filter/actions depending on settings]
		***********************************************************/
		/** set max results per page admin order history **/
		add_filter('wppizza_filter_order_history_max_results',array( $this, 'admin_order_history_max_results'));
		/** set polling time admin order history **/
		add_filter('wppizza_filter_order_history_polling_time', array( $this, 'admin_order_history_polling_time'));
		/** set auto poll admin order history **/
		add_filter('wppizza_filter_order_history_polling_auto', array( $this, 'admin_order_history_polling_auto'));
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

		admin_order_history_max_results

	**************************************/
	function admin_order_history_max_results($int){
		global $wppizza_options;
		if(!empty($wppizza_options[$this->settings_page]['admin_order_history_max_results'])){
			$int = $wppizza_options[$this->settings_page]['admin_order_history_max_results'];
		}
	
	return $int;
	}
	/*************************************

		admin_order_history_polling_time

	**************************************/	
	function admin_order_history_polling_time($int){
		global $wppizza_options;
		if(!empty($wppizza_options[$this->settings_page]['admin_order_history_polling_time'])){
			$int = $wppizza_options[$this->settings_page]['admin_order_history_polling_time'];
		}
	
	return $int;
	}
	/*************************************

		admin_order_history_polling_auto

	**************************************/	
	function admin_order_history_polling_auto($bool){
		global $wppizza_options;
		$bool = !empty($wppizza_options[$this->settings_page]['admin_order_history_polling_auto']) ? true : false;
	
	return $bool;
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
			$settings['sections'][$this->section_key] = __('Order History', 'wppizza-admin');
		}
		/*help*/
		if($help){
			$settings['help'][$this->section_key][] = array(
				'label'=>__('Order History', 'wppizza-admin'),
				'description'=>array(
					__('Please adjust settings as appropriate according to the information provided next to each individual option.', 'wppizza-admin')
				)
			);
		}
		/*fields*/
		if($fields){
			$field = 'admin_order_history_max_results';
			$settings['fields'][$this->section_key][$field] = array( __('Max Results Per Page', 'wppizza-admin'), array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('default number of results to show in admin order history','wppizza-admin'),
				'description'=>array()
			));
			$field = 'admin_order_history_polling_time';
			$settings['fields'][$this->section_key][$field] = array( __('Polling Time', 'wppizza-admin'), array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('default polling time [in seconds]. 15 seconds minimum', 'wppizza-admin'),
				'description'=>array()
			));
			$field = 'admin_order_history_polling_auto';
			$settings['fields'][$this->section_key][$field] = array( __('Auto Polling', 'wppizza-admin'), array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('automatically activate order polling on page load', 'wppizza-admin'),
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
		
		if($field=='admin_order_history_max_results'){
			echo "<label>";
				echo "<input id='".$field."' name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' size='2' type='text'  value='".$wppizza_options[$options_key][$field]."' />";
				echo "" . $label . "";
			echo "</label>";
			echo"".$description."";			
		}

		if($field=='admin_order_history_polling_time'){
			echo "<label>";
				echo "<input id='".$field."' name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' size='2' type='text'  value='".$wppizza_options[$options_key][$field]."' />";
				echo "" . $label . "";
			echo "</label>";
			echo"".$description."";				
		}

		if($field=='admin_order_history_polling_auto'){
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
		$options[$this->settings_page]['admin_order_history_max_results'] = 25;
		$options[$this->settings_page]['admin_order_history_polling_time'] = 60;		
		$options[$this->settings_page]['admin_order_history_polling_auto'] = true;		
		
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
			$options[$this->settings_page]['admin_order_history_max_results'] = wppizza_validate_int_only($input[$this->settings_page]['admin_order_history_max_results']);
			$options[$this->settings_page]['admin_order_history_polling_time'] = max(15,wppizza_validate_int_only($input[$this->settings_page]['admin_order_history_polling_time']));
			$options[$this->settings_page]['admin_order_history_polling_auto'] = !empty($input[$this->settings_page]['admin_order_history_polling_auto']) ? true : false;			
		}
	return $options;
	}
}
/***************************************************************
*
*	[ini]
*
***************************************************************/
$WPPIZZA_MODULE_SETTINGS_ORDER_HISTORY = new WPPIZZA_MODULE_SETTINGS_ORDER_HISTORY();
?>