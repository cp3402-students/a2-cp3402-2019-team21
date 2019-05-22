<?php
/**
* WPPIZZA_MODULE_ORDER_SETTINGS_DELIVERY Class
*
* @package     WPPIZZA
* @subpackage  WPPIZZA_MODULE_ORDER_SETTINGS_DELIVERY
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
class WPPIZZA_MODULE_ORDER_SETTINGS_DELIVERY{

	private $settings_page = 'order_settings';/* which admin subpage (identified there by this->class_key) are we adding this to */
	private $section_key = 'delivery';/* must be unique */
	private $localization_page = 'localization';
	private $localization_section_append = 'pickup';/* which localization section do we want to append the fileds to */	


	function __construct() {
		/**********************************************************
			[add settings to admin]
		***********************************************************/
		if(is_admin()){
			/* add admin options settings page*/
			add_filter('wppizza_filter_settings_sections_'.$this->settings_page.'', array($this, 'admin_options_settings'), 20, 5);
			/* add admin options settings page fields */
			add_action('wppizza_admin_settings_section_fields_'.$this->settings_page.'', array($this, 'admin_options_fields_settings'), 10, 5);
			/* add admin options localization page - does not need fields action*/
			add_filter('wppizza_filter_settings_sections_'.$this->localization_page.'', array($this, 'admin_options_localization'), 200, 5);/* highish priority to put it after all other miscellaneous localization fields (must be min 190)*/			
			/**add default options **/
			add_filter('wppizza_filter_setup_default_options', array( $this, 'options_default'));
			/**validate options**/
			add_filter('wppizza_filter_options_validate', array( $this, 'options_validate'), 10, 2 );
		}
		/**********************************************************
			[filter/actions depending on settings]
		***********************************************************/

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
			$settings['sections'][$this->section_key] = __('Delivery', 'wppizza-admin');
		}
		/*help*/
		if($help){
			$settings['help'][$this->section_key][] = array(
				'label'=>__('Delivery', 'wppizza-admin'),
				'description'=>array(
					__('Please adjust settings as appropriate according to the information provided next to each individual option.', 'wppizza-admin')
				)
			);
		}
		/*fields*/
		if($fields){
			$field = 'delivery';
			$settings['fields'][$this->section_key][$field] = array( __('Delivery Type', 'wppizza-admin'), array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>'',
				'description'=>array()
			));
			$field = 'order_min_for_delivery';
			$settings['fields'][$this->section_key][$field] = array( __('Minimum order value - delivery', 'wppizza-admin'), array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('minimum order value - on delivery [0 to disable]', 'wppizza-admin'),
				'description'=>array(
					__('disables "place order" button in cart and order page until set order value (before any discounts etc) has been reached.', 'wppizza-admin'),
					__('customer can still choose "self-pickup" (if enabled / applicable).', 'wppizza-admin')
				)
			));
		
			$field = 'order_min_for_pickup';
			$settings['fields'][$this->section_key][$field] = array( __('Minimum order value - pickup', 'wppizza-admin'), array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('minimum order value - on self pickup [0 to disable]', 'wppizza-admin'),
				'description'=>array(
					__('disables "place order" button in cart and order page until set order value (before any discounts etc) has been reached.', 'wppizza-admin')
				)
			));

			$field = 'order_min_on_totals';
			$settings['fields'][$this->section_key][$field] = array( __('Minimum order of totals', 'wppizza-admin'), array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('apply minimum order value on total sum of order (but before any tips and/or handling charges) [if left unchecked, minimum order uses only sum of items in cart before discounts etc]', 'wppizza-admin'),
				'description'=>array()
			));

			$field = 'order_min_exclude_delivery_charges';
			$settings['fields'][$this->section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>'',// set directly as its a suboption
				'description'=>array()
			));

			$field = 'order_delivery_time';
			$settings['fields'][$this->section_key][$field] = array( __('Delivery time', 'wppizza-admin'), array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('set delivery time that will be displayed in applicable alerts, emails, notices ', 'wppizza-admin'),
				'description'=>array(
					__('referenced in relevant localization strings', 'wppizza-admin')
				)
			));	


			$field = 'delivery_calculation_exclude_item';
			$settings['fields'][$this->section_key][$field] = array( __('Exclude menu items from calculation', 'wppizza-admin'), array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('<b>Exclude</b> following menu items when calculating if free delivery applies or minimum order value has been reached (if not using minimum order of totals)', 'wppizza-admin') . '<br/>(' . __('Consider updating your "minimum order..." localization strings too if you use this', 'wppizza-admin').')',
				'description'=>array()
			));
			
			$field = 'delivery_calculation_exclude_cat';
			$settings['fields'][$this->section_key][$field] = array( __('Exclude categories from calculation', 'wppizza-admin'), array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('<b>Exclude</b> all  menu items belonging to following *categories* when calculating if free delivery applies or minimum order value has been reached (if not using minimum order of totals)', 'wppizza-admin') . '<br/>(' . __('Consider updating your "minimum order..." localization strings too if you use this', 'wppizza-admin').')',
				'description'=>array(
					__('For example: you might want to offer free delivery only when total order of *meals* exceeds the set free delivery amount. In this case, exclude all your *drinks and non-meals* by selecting those above.', 'wppizza-admin')
				)
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

		if($field=='delivery'){
			/****sort in a more sensible manner**/
			$wppizza_options[$options_key][$field]=array(
				'no_delivery'=>$wppizza_options[$options_key][$field]['no_delivery'],
				'minimum_total'=>$wppizza_options[$options_key][$field]['minimum_total'],
				'standard'=>$wppizza_options[$options_key][$field]['standard'],
				'per_item'=>$wppizza_options[$options_key][$field]['per_item']
			);

			echo "<table id='wppizza_admin_table_".$field."' class='wppizza_admin_table'>";
			foreach($wppizza_options[$options_key][$field] as $k=>$v){
				echo "<tr>";
					echo "<td>";

						echo "<input name='".WPPIZZA_SLUG."[".$options_key."][delivery_selected]' type='radio' ". checked($wppizza_options[$options_key]['delivery_selected']==$k,true,false)." value='".$k."' />";

						if($k=='no_delivery'){
							echo" ".__('No delivery offered / pickup only', 'wppizza-admin')."";
							echo"<br /><span class='description'>".__('removes any labels, text, charges, checkboxes etc associated with delivery options. You can still set a minimum order value below.', 'wppizza-admin')."</span>";

						}
						if($k=='minimum_total'){
							echo" ".__('Free delivery when total order value reaches', 'wppizza-admin').":";
							echo"<input name='".WPPIZZA_SLUG."[".$options_key."][".$field."][".$k."][min_total]' size='3' type='text' value='".wppizza_output_format_price($wppizza_options[$options_key][$field][$k]['min_total'])."' />";
							echo"<div style='margin-left:20px'>";
							echo"<input name='".WPPIZZA_SLUG."[".$options_key."][".$field."][".$k."][deliver_below_total]' type='checkbox' ". checked($v['deliver_below_total'],true,false)." value='1' />";
							echo" ".__('Deliver even when total order value is below minimum (the difference between total and "Minimum Total" above will be added to the Total as "Delivery Charges")', 'wppizza-admin')."";
							echo"<br />";
							echo"<span class='description'>".__('(If this is not selected and the total order is below the set value above, the customer will not be able to submit the order to you)', 'wppizza-admin')."</span>";
							echo"<br />";
							echo"<input name='".WPPIZZA_SLUG."[".$options_key."][".$field."][".$k."][deliverycharges_below_total]' size='3' type='text' value='".wppizza_output_format_price($wppizza_options[$options_key][$field][$k]['deliverycharges_below_total'])."' />";
							echo" ".__('Fixed Delivery charges if order has not reached total for free delivery [0 to disable]', 'wppizza-admin')."";
							echo"<br />";
							echo" <span class='description' style='color:red'>(".__('if set (i.e. not 0) "Deliver even when total order value is below minimum" must be checked for this to have any effect', 'wppizza-admin').")</span>";

							echo"</div>";
						}
						if($k=='standard'){
							echo" ".__('Fixed Delivery Charges [added to order total]', 'wppizza-admin').":";
							echo "<input name='".WPPIZZA_SLUG."[".$this->settings_page."][".$field."][".$k."][delivery_charge]' size='3' type='text' value='".wppizza_output_format_price($wppizza_options[$this->settings_page][$field][$k]['delivery_charge'])."' />";

						}
						if($k=='per_item'){
							echo" ".__('Delivery Charges per item', 'wppizza-admin').":";
							echo"<div style='margin-left:20px'>";
							echo "<input name='".WPPIZZA_SLUG."[".$this->settings_page."][".$field."][".$k."][delivery_charge_per_item]' size='3' type='text' value='".wppizza_output_format_price($wppizza_options[$this->settings_page][$field][$k]['delivery_charge_per_item'])."' />";
							echo" ".__('Do not apply delivery charges when total order value reaches ', 'wppizza-admin').":";
							echo"<input name='".WPPIZZA_SLUG."[".$this->settings_page."][".$field."][".$k."][delivery_per_item_free]' size='3' type='text' value='".wppizza_output_format_price($wppizza_options[$this->settings_page][$field][$k]['delivery_per_item_free'])."' />";
							echo" ".__('[set to 0 to always apply charges per item]', 'wppizza-admin')."";
							echo"</div>";
						}
					echo "</td>";
				echo "</tr>";
			}
			echo "</table>";
		}

		if($field=='order_min_on_totals'){
			echo "<label>";
				echo "<input id='".$field."' name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' type='checkbox'  ". checked($wppizza_options[$options_key][$field],true,false)." value='1' />";
				echo "".$label."";
			echo "</label>";			
			echo"".$description."";
			
			/** add exclude delivery too here as suboption, setting label directly **/
			$field='order_min_exclude_delivery_charges';
			echo "<br><label>";
				echo "<input id='".$field."' name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' type='checkbox'  ". checked($wppizza_options[$options_key][$field],true,false)." value='1' />";
				echo "".__('do not include delivery charges to calculate if minimum order value has been reached (only applicable if above option is also enabled)', 'wppizza-admin')."";
			echo "</label>";			
			//echo"".$description."";			
		}	

		if($field=='order_min_for_delivery'){
			echo "<label>";
				echo "<input name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' size='3' type='text'  value='".wppizza_output_format_price($wppizza_options[$options_key][$field])."' />";
				echo "" . $label . "";
			echo "</label>";
			echo"".$description."";
		}
		if($field=='order_delivery_time'){
			echo "<label>";
				echo "<input name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' size='3' type='text'  value='".($wppizza_options[$options_key][$field])."' />";
				echo "" . $label . "";
			echo "</label>";
			echo"".$description."";
		}

		if($field=='order_min_for_pickup'){
			echo "<label>";
				echo "<input name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' size='3' type='text'  value='".wppizza_output_format_price($wppizza_options[$options_key][$field])."' />";
				echo "" . $label . "";
			echo "</label>";
			echo"".$description."";
		}


		if($field=='delivery_calculation_exclude_item'){
			if(is_array(wppizza_get_menu_items())){
				echo "<label>";
					echo "".$label."";
					echo "<br />";
					echo"<select name='".WPPIZZA_SLUG."[".$options_key."][".$field."][]' multiple='multiple' data-placeholder='".__('N/A', 'wppizza-admin')."' class='wppizza_".$field."'>";
					foreach(wppizza_get_menu_items() as $pKey=>$pVal){
						echo"<option value='".$pVal->ID."' ";
							if(isset($wppizza_options[$options_key][$field]) && in_array($pVal->ID,$wppizza_options[$options_key][$field])){
								echo" selected='selected'";
							}
						echo">".$pVal->post_title."</option>";
					}
					echo"</select>";
				echo "</label>";
				echo"".$description."";
			}
		}

		if($field=='delivery_calculation_exclude_cat'){
			if(is_array(wppizza_get_categories())){
				echo "<label>";
					echo "".$label."";
					echo "<br />";
					echo"<select name='".WPPIZZA_SLUG."[".$options_key."][".$field."][]' multiple='multiple'  data-placeholder='".__('N/A', 'wppizza-admin')."' class='wppizza_".$field."'>";
					foreach(wppizza_get_categories() as $cKey=>$cVal){
						echo"<option value='".$cVal->term_id."' ";
							if(isset($wppizza_options[$options_key][$field]) && isset($wppizza_options[$options_key][$field][$cVal->term_id])){
								echo" selected='selected'";
							}
						echo">".$cVal->name."</option>";
					}
					echo"</select>";
				echo "</label>";
				echo"".$description."";
			}
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
		//global $wppizza_options;
		/* skip if not enabled */
		//if(empty($wppizza_options[$this->settings_page]['repurchase'])){
		//	return $settings;
		//}
		/********************************
		*	[Labels]
		********************************/
		/*sections*/
		//if($sections){
		//	$add_settings['sections'][$this->section_key] =  __('User Purchase History - Repurchasing', 'wppizza-admin');
		//}
		/*fields*/
		if($fields){
			$field = 'free_delivery_for_orders_of';
			$settings['fields'][$this->localization_section_append][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->localization_page,
				'label'=>__('Label Info: i.e. "free delivery for orders over"...', 'wppizza-admin')
			));
			$field = 'minimum_order_delivery';
			$settings['fields'][$this->localization_section_append][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->localization_page,
				'label'=>__('Label Info: required minimum order value for delivery (displayed if applicable)', 'wppizza-admin')
			));			
			$field = 'order_page_delivery_time';
			$settings['fields'][$this->localization_section_append][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->localization_page,
				'label'=>__('Delivery Time: text on order page / email to indicate delivery times (if applicable) %s will be replaced by delivery times set in wppizza -> order settings', 'wppizza-admin')
			));	
			$field = 'generic_order_delivered_shortly';
			$settings['fields'][$this->localization_section_append][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->localization_page,
				'label'=>__('Delivery: generic label that could be used (by 3rd party plugins) when order is about to be delivered', 'wppizza-admin')
			));			
			$field = 'generic_order_delivery_time';
			$settings['fields'][$this->localization_section_append][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->localization_page,
				'label'=>__('Delivery: generic text that could be used (by 3rd party plugins) to indicate when an order will delivered [%s to be replaced with appropriate time]', 'wppizza-admin')
			));				
			$field = 'order_request_delivery';
			$settings['fields'][$this->localization_section_append][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->localization_page,
				'label'=>__('Shoppingcart - Delivery: text next to checkbox under cart / on order page (if pickup is set to be the default)', 'wppizza-admin')
			));

			$field = 'order_delivery_cartjs';
			$settings['fields'][$this->localization_section_append][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->localization_page,
				'label'=>__('Delivery: javascript alert when customer selects delivery (if enabled)', 'wppizza-admin')
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
			settings page 
		*/
		$options[$this->settings_page]['delivery_selected'] = 'minimum_total';
		$options[$this->settings_page]['order_min_for_delivery'] = 0;
		$options[$this->settings_page]['order_delivery_time'] = 45;
		$options[$this->settings_page]['order_min_for_pickup'] = 0; //to check. perhaps only relevant for updates from very old version  --> !empty($options[$this->settings_page]['order_min_for_delivery']) ? $options[$this->settings_page]['order_min_for_delivery'] : 0 ;/**on updates, use order_min_for_delivery settings so as to not modify th ebehaviousr in the frontend**/
		$options[$this->settings_page]['order_min_on_totals'] = false; 
		$options[$this->settings_page]['order_min_exclude_delivery_charges'] = false; 
				
		$options[$this->settings_page]['delivery'] = array(
			'no_delivery'=>'',
			'minimum_total'=>array('min_total'=>'12.5','deliver_below_total'=>true,'deliverycharges_below_total'=>'0'),
			'standard'=>array('delivery_charge'=>'12.5'),
			'per_item'=>array('delivery_charge_per_item'=>'1','delivery_per_item_free'=>'50')
		);
		$options[$this->settings_page]['delivery_calculation_exclude_item'] = array();
		$options[$this->settings_page]['delivery_calculation_exclude_cat'] = array();

		/*
			localization 
		*/
		$options[$this->localization_page]['free_delivery_for_orders_of'] = esc_html__('free delivery for orders over', 'wppizza');
		$options[$this->localization_page]['minimum_order_delivery'] = esc_html__('minimum order for delivery', 'wppizza');
		$options[$this->localization_page]['order_page_delivery_time'] = esc_html__('Please allow %s minutes for delivery.', 'wppizza');
		$options[$this->localization_page]['generic_order_delivered_shortly'] = esc_html__('Your order is out for delivery and will arrive with you shortly.', 'wppizza');
		$options[$this->localization_page]['generic_order_delivery_time'] = esc_html__('Your order will be delivered no later than %s.', 'wppizza');
		$options[$this->localization_page]['order_request_delivery'] = esc_html__('I would like my order to be delivered', 'wppizza');
		$options[$this->localization_page]['order_delivery_cartjs'] = esc_html__('Please allow %s min. for us to deliver your order.', 'wppizza');		



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

			$options[$this->settings_page]['delivery_selected'] = wppizza_validate_alpha_only($input[$this->settings_page]['delivery_selected']);
			$options[$this->settings_page]['order_min_for_delivery']=wppizza_validate_float_only($input[$this->settings_page]['order_min_for_delivery']);
			$options[$this->settings_page]['order_delivery_time']=wppizza_validate_int_only($input[$this->settings_page]['order_delivery_time']);
			$options[$this->settings_page]['order_min_for_pickup']=wppizza_validate_float_only($input[$this->settings_page]['order_min_for_pickup']);
			$options[$this->settings_page]['order_min_on_totals'] = !empty($input[$this->settings_page]['order_min_on_totals']) ? true :  false; 
			$options[$this->settings_page]['order_min_exclude_delivery_charges'] = !empty($input[$this->settings_page]['order_min_exclude_delivery_charges']) ? true :  false; 
						
			
			$options[$this->settings_page]['delivery'] = array();
				foreach($input[$this->settings_page]['delivery'] as $k=>$v){
					foreach($v as $l=>$m){
						if($l!='deliver_below_total'){
							$options[$this->settings_page]['delivery'][$k][$l]=wppizza_validate_float_only($m,2);
						}
					}
					if($k=='minimum_total'){
						$options[$this->settings_page]['delivery'][$k]['deliver_below_total']=!empty($input[$this->settings_page]['delivery'][$k]['deliver_below_total']) ? true : false;
						$options[$this->settings_page]['delivery'][$k]['deliverycharges_below_total']=wppizza_validate_float_only($input[$this->settings_page]['delivery'][$k]['deliverycharges_below_total']);
					}
				}
			/**hardcode no_delivery (as there are  no submitted input values)*/
			$options[$this->settings_page]['delivery']['no_delivery']='';
			$options[$this->settings_page]['delivery_calculation_exclude_item'] = !empty($input[$this->settings_page]['delivery_calculation_exclude_item']) ? array_combine($input[$this->settings_page]['delivery_calculation_exclude_item'], $input[$this->settings_page]['delivery_calculation_exclude_item']) : array();
			$options[$this->settings_page]['delivery_calculation_exclude_cat'] = !empty($input[$this->settings_page]['delivery_calculation_exclude_cat']) ? array_combine($input[$this->settings_page]['delivery_calculation_exclude_cat'],$input[$this->settings_page]['delivery_calculation_exclude_cat']) : array();/*makes keys == values*/

		}
	return $options;
	}
}
/***************************************************************
*
*	[ini]
*
***************************************************************/
$WPPIZZA_MODULE_ORDER_SETTINGS_DELIVERY = new WPPIZZA_MODULE_ORDER_SETTINGS_DELIVERY();
?>