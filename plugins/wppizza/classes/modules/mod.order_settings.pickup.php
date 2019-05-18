<?php
/**
* WPPIZZA_MODULE_ORDERSETTINGS_PICKUP Class
*
* @package     WPPIZZA
* @subpackage  WPPIZZA_MODULE_ORDERSETTINGS_PICKUP
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
class WPPIZZA_MODULE_ORDERSETTINGS_PICKUP{

	private $settings_page = 'order_settings';/* which admin subpage (identified there by this->class_key) are we adding this to */
	private $localization_page = 'localization';	
	private $section_key = 'pickup';/* must be unique */


	function __construct() {
		/**********************************************************
			[add settings to admin]
		***********************************************************/
		if(is_admin()){
			/* add admin options settings page*/
			add_filter('wppizza_filter_settings_sections_'.$this->settings_page.'', array($this, 'admin_options_settings'), 30, 5);
			/* add admin options settings page fields */
			add_action('wppizza_admin_settings_section_fields_'.$this->settings_page.'', array($this, 'admin_options_fields_settings'), 10, 5);
			/* add admin options localization page - does not need fields action*/
			add_filter('wppizza_filter_settings_sections_'.$this->localization_page.'', array($this, 'admin_options_localization'), 80, 5);
			
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
			$settings['sections'][$this->section_key] = __('Pickup', 'wppizza-admin');
		}
		/*help*/
		if($help){
			$settings['help'][$this->section_key][] = array(
				'label'=>__('Order Pickup', 'wppizza-admin'),
				'description'=>array(
					__('Please adjust settings as appropriate according to the information provided next to each individual option.', 'wppizza-admin')
				)
			);
		}
		
		/*fields*/
		if($fields){
			$field = 'order_pickup';
			$settings['fields'][$this->section_key][$field] = array( __('Allow order pickup by customer', 'wppizza-admin'), array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('Customer can choose to pickup the order him/herself.', 'wppizza-admin'),
				'description'=>array(
					__('No delivery charges will be applied if customer chooses to do so.', 'wppizza-admin')
				)
			));
			$field = 'order_pickup_preparation_time';
			$settings['fields'][$this->section_key][$field] = array( __('Preparation Time', 'wppizza-admin'), array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('Time set here will be used in various customer notices when pickup has been selected', 'wppizza-admin'),
				'description'=>array()
			));			
			
			$field = 'order_pickup_discount';
			$settings['fields'][$this->section_key][$field] = array( __('Discount for self-pickup', 'wppizza-admin'), array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('in % - 0 to disable [will not be applied to menu items and categories set to be excluded from discount calculation above]', 'wppizza-admin'),
				'description'=>array()
			));
			$field = 'order_pickup_alert';
			$settings['fields'][$this->section_key][$field] = array( __('javascript alert on pickup', 'wppizza-admin'), array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('enable javascript alert when user changes selection', 'wppizza-admin'),
				'description'=>array()
			));
			$field = 'order_pickup_alert_confirm';
			$settings['fields'][$this->section_key][$field] = array( __('Confirm pickup/delivery change', 'wppizza-admin'), array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('make user *confirm* change of pickup/delivery using a popup javascript alert', 'wppizza-admin'),
				'description'=>array()
			));
			$field = 'order_pickup_display_location';
			$settings['fields'][$this->section_key][$field] = array( __('Checkbox location', 'wppizza-admin'), array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>'',
				'description'=>array(
					__('Where would you like to display the checkbox to let customer select self pickup of order [if enabled above] ?', 'wppizza-admin')
				)
			));
			$field = 'order_pickup_as_default';
			$settings['fields'][$this->section_key][$field] = array( __('Pickup as default', 'wppizza-admin'), array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('set "pickup" to be the default selection (make sure to clear your browser cache to see the effect)', 'wppizza-admin'),
				'description'=>array()
			));
			$field = 'order_pickup_toggled';
			$settings['fields'][$this->section_key][$field] = array( __('Choices Toggle', 'wppizza-admin'), array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('by default an on/off checkbox will be used, enable to use a two button toggle', 'wppizza-admin'),
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

		if($field=='order_pickup'){
			echo "<label>";
				echo "<input id='".$field."' name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' type='checkbox'  ". checked($wppizza_options[$options_key][$field],true,false)." value='1' />";
				echo "".$label."";
			echo "</label>";			
			echo"".$description."";
		}
		if($field=='order_pickup_discount'){
			echo "<label>";
				echo "<input id='".$field."' name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' size='5' type='text' value='".wppizza_output_format_percent($wppizza_options[$options_key][$field])."' />";
				echo "".$label."";
			echo "</label>";			
			echo"".$description."";
		}		
		if($field=='order_pickup_preparation_time'){
			echo "<label>";
				echo "<input id='".$field."' name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' size='5' type='text' value='".($wppizza_options[$options_key][$field])."' />";
				echo "".$label."";
			echo "</label>";			
			echo"".$description."";
		}				
		if($field=='order_pickup_alert'){
			echo "<label>";
				echo "<input id='".$field."' name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' type='checkbox'  ". checked($wppizza_options[$options_key][$field],true,false)." value='1' />";
				echo "".$label."";
			echo "</label>";			
			echo"".$description."";
		}	
		if($field=='order_pickup_alert_confirm'){
			echo "<label>";
				echo "<input id='".$field."' name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' type='checkbox'  ". checked($wppizza_options[$options_key][$field],true,false)." value='1' />";
				echo "".$label."";
			echo "</label>";			
			echo"".$description."";
		}			
		if($field=='order_pickup_display_location'){
			echo "<label>";
				echo "".__('under cart only', 'wppizza-admin')."<input id='".$field."' name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' type='radio'  ".checked($wppizza_options[$options_key][$field],1,false)." value='1' /> ";
				echo "".__('on order page only', 'wppizza-admin')."<input id='".$field."' name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' type='radio'  ".checked($wppizza_options[$options_key][$field],2,false)." value='2' /> ";
				echo "".__('both', 'wppizza-admin')."<input id='".$field."' name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' type='radio'  ".checked($wppizza_options[$options_key][$field],3,false)." value='3' />";
				echo "".$label."";
			echo "</label>";			
			echo"".$description."";
		}
		if($field=='order_pickup_as_default'){
			echo "<label>";
				echo "<input id='".$field."' name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' type='checkbox'  ". checked($wppizza_options[$options_key][$field],true,false)." value='1' />";
				echo "".$label."";
			echo "</label>";			
			echo"".$description."";
		}
		if($field=='order_pickup_toggled'){
			echo "<label>";
				echo "<input id='".$field."' name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' type='checkbox'  ". checked($wppizza_options[$options_key][$field],true,false)." value='1' />";
				echo "".$label."";
			echo "</label>";			
			echo"".$description."";
		}
	}



	/*------------------------------------------------------------------------------
	#
	#
	#	[localization page]
	#
	#
	------------------------------------------------------------------------------*/
	/****************************************************************
	*	[settigs section  - localization page]
	*	@since 3.0
	*	@return array()
	****************************************************************/
	function admin_options_localization($settings, $sections, $fields, $inputs, $help){
		global $wppizza_options;
		/* skip if not enabled */
		//if(empty($wppizza_options[$this->settings_page]['repurchase'])){
		//	return $settings;
		//}
		/********************************
		*	[Labels]
		********************************/
		/*sections*/
		if($sections){
			$settings['sections'][$this->section_key] =  __('Pickup / Delivery', 'wppizza-admin');
		}
		/*fields*/
		if($fields){
			$field = 'pickup_toggle_delivery';
			$settings['fields'][$this->section_key][$field] = array( '' , array(
				'value_key'=>$field,
				'option_key'=>$this->localization_page,
				'label'=>__('Label Delivery Button [if Order Settings -> Order Pickup -> Choices Toggle enabled]', 'wppizza-admin')
			));				
			$field = 'pickup_toggle_pickup';
			$settings['fields'][$this->section_key][$field] = array( '' , array(
				'value_key'=>$field,
				'option_key'=>$this->localization_page,
				'label'=>__('Label Pickup Button [if Order Settings -> Order Pickup -> Choices Toggle enabled]', 'wppizza-admin')
			));	
			$field = 'order_page_selfpickup';
			$settings['fields'][$this->section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->localization_page,
				'label'=>__('Self Pickup: text on order page / email to highlight self pickup (if applicable)', 'wppizza-admin')
			));
			$field = 'generic_ready_for_pickup';
			$settings['fields'][$this->section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->localization_page,
				'label'=>__('Self Pickup: generic label that could be used (by 3rd party plugins) when order is ready for pickup', 'wppizza-admin')
			));
			$field = 'generic_ready_for_pickup_time';
			$settings['fields'][$this->section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->localization_page,
				'label'=>__('Self Pickup: generic text that could be used (by 3rd party plugins) to indicate when an order will be ready for collection [%s to be replaced with appropriate time]', 'wppizza-admin')
			));							
			$field = 'order_page_no_delivery';
			$settings['fields'][$this->section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->localization_page,
				'label'=>__('No Delivery Offered / Pickup Only: text on order page / email if delivery is not being offered (if applicable)', 'wppizza-admin')
			));
			$field = 'order_self_pickup';
			$settings['fields'][$this->section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->localization_page,
				'label'=>__('Self Pickup: text next to self pickup checkbox (if enabled)', 'wppizza-admin')
			));
			$field = 'order_self_pickup_cart';
			$settings['fields'][$this->section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->localization_page,
				'label'=>__('Self Pickup: text under total value (if selected by customer)', 'wppizza-admin')
			));
			$field = 'order_self_pickup_cartjs';
			$settings['fields'][$this->section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->localization_page,
				'label'=>__('Self Pickup: javascript alert when customer selects self pickup (if enabled)', 'wppizza-admin')
			));			
			$field = 'minimum_order';
			$settings['fields'][$this->section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->localization_page,
				'label'=>__('Label Info: required minimum order value for pickup (displayed if applicable)', 'wppizza-admin')
			));			
			
		}
	
		return $settings;
	}	



	/*------------------------------------------------------------------------------
	#	[insert default option on install]
	#	$parameter $options array() | filter passing on filtered options
	#	@since 3.0
	#	@return array()
	------------------------------------------------------------------------------*/
	function options_default($options){

		/* 
			order settings 
		*/			
		$options[$this->settings_page]['order_pickup'] = true;
		$options[$this->settings_page]['order_pickup_alert'] = false;
		$options[$this->settings_page]['order_pickup_alert_confirm'] = false;
		$options[$this->settings_page]['order_pickup_as_default'] = false;
		$options[$this->settings_page]['order_pickup_toggled'] = false;
		$options[$this->settings_page]['order_pickup_discount'] = 0;
		$options[$this->settings_page]['order_pickup_preparation_time'] = 30;
		$options[$this->settings_page]['order_pickup_display_location'] = 3;

		/* 
			localization 
		*/
		$options[$this->localization_page]['pickup_toggle_delivery']	= esc_html__('Delivery', 'wppizza');
		$options[$this->localization_page]['pickup_toggle_pickup']		= esc_html__('Pickup', 'wppizza');
		$options[$this->localization_page]['order_page_selfpickup']		= esc_html__('You have chosen to pickup the order yourself. This order will not be delivered. Please allow %s min. for us to prepare your order.', 'wppizza');
		$options[$this->localization_page]['generic_ready_for_pickup']	= esc_html__('Please collect your order from the store at your earliest convenience.', 'wppizza');
		$options[$this->localization_page]['generic_ready_for_pickup_time']	= esc_html__('Your order will be ready for collection at %s.', 'wppizza');
		$options[$this->localization_page]['order_page_no_delivery'] 	= esc_html__('Please collect your order at the store.', 'wppizza');
		$options[$this->localization_page]['order_self_pickup'] 		= esc_html__('I would like to pickup the order myself', 'wppizza');
		$options[$this->localization_page]['order_self_pickup_cart'] 	= esc_html__('Delivery: pickup', 'wppizza');
		$options[$this->localization_page]['order_self_pickup_cartjs'] 	= esc_html__('You have chosen to pickup the order yourself. This order will not be delivered. Please allow %s min. for us to prepare your order.', 'wppizza');				
		$options[$this->localization_page]['minimum_order'] 			= esc_html__('minimum order', 'wppizza');
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
			$options[$this->settings_page]['order_pickup']=!empty($input[$this->settings_page]['order_pickup']) ? true : false;
			$options[$this->settings_page]['order_pickup_alert']=!empty($input[$this->settings_page]['order_pickup_alert']) ? true : false;
			$options[$this->settings_page]['order_pickup_alert_confirm']=!empty($input[$this->settings_page]['order_pickup_alert_confirm']) ? true : false;
			$options[$this->settings_page]['order_pickup_as_default']=!empty($input[$this->settings_page]['order_pickup_as_default']) ? true : false;
			$options[$this->settings_page]['order_pickup_discount']=wppizza_validate_float_pc($input[$this->settings_page]['order_pickup_discount']);
			$options[$this->settings_page]['order_pickup_display_location'] = wppizza_validate_int_only($input[$this->settings_page]['order_pickup_display_location']);
			$options[$this->settings_page]['order_pickup_preparation_time'] = wppizza_validate_int_only($input[$this->settings_page]['order_pickup_preparation_time']);
			$options[$this->settings_page]['order_pickup_toggled']=!empty($input[$this->settings_page]['order_pickup_toggled']) ? true : false;
		}

		/* 
			localization strings are automatically validated 
		*/	


	return $options;
	}
}
/***************************************************************
*
*	[ini]
*
***************************************************************/
$WPPIZZA_MODULE_ORDERSETTINGS_PICKUP = new WPPIZZA_MODULE_ORDERSETTINGS_PICKUP();
?>