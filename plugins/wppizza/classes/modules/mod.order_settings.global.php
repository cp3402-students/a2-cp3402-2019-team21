<?php
/**
* WPPIZZA_MODULE_ORDER_SETTINGS_GLOBAL Class
*
* @package     WPPIZZA
* @subpackage  WPPIZZA_MODULE_ORDER_SETTINGS_GLOBAL
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
class WPPIZZA_MODULE_ORDER_SETTINGS_GLOBAL{

	private $settings_page = 'order_settings';/* which admin subpage (identified there by this->class_key) are we adding this to */

	private $section_key = 'global';/* must be unique */


	function __construct() {
		/**********************************************************
			[add settings to admin]
		***********************************************************/
		if(is_admin()){
			/* add admin options settings page*/
			add_filter('wppizza_filter_settings_sections_'.$this->settings_page.'', array($this, 'admin_options_settings'), 10, 5);
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
		/***exclude selected order page from navigation */
		add_filter('get_pages', array($this,'exclude_order_page_from_navigation'));	
		/****filter transaction id's******/
		add_filter( 'wppizza_filter_transaction_id', array( $this, 'filter_transaction_id'),10,2);	
		/****show order on thank you page ******/
		add_filter('wppizza_filter_showorder_on_thankyou', array($this,'showorder_on_thankyou'));		
		
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
	/*********************************************************
	*
	*		[exclude_order_page_from_navigation - frontend only ]
	*
	*********************************************************/
	function exclude_order_page_from_navigation($pages) {
	if(is_admin()){return $pages;}
	
	global $wppizza_options;
		if($wppizza_options[$this->settings_page]['orderpage_exclude']){
			$pageCount = count($pages);
			for ( $i=0; $i<$pageCount; $i++ ) {
				$page = & $pages[$i];

				/**wpml select of order page**/
				if(function_exists('icl_object_id')) {
					$wppizza_options[$this->settings_page]['orderpage']=icl_object_id($wppizza_options[$this->settings_page]['orderpage'],'page');
				}

				if ($page->ID==$wppizza_options[$this->settings_page]['orderpage']) {
					unset( $pages[$i] );/*unset the order page*/
				}
			}
			if ( ! is_array( $pages ) ) $pages = (array) $pages;
			$pages = array_values( $pages );
		}
		return $pages;
	}	
	/*******************************************************************************
	*
	*
	*	filter transaction id
	*
	*
	*******************************************************************************/
	function filter_transaction_id($transaction_id, $order_id){
		global $wppizza_options;
		/**allow custom filter**/
		$transaction_id = apply_filters('wppizza_custom_transaction_id', $transaction_id, $order_id);
		/**add id to end**/
		if($wppizza_options[$this->settings_page]['append_internal_id_to_transaction_id']){
			$transaction_id.='/'.$order_id.'';
		}
		return $transaction_id;
	}	
	
	/*********************************************************
	*
	*		[showorder_on_thankyou]
	*
	*********************************************************/	
	function showorder_on_thankyou($bool){
		global $wppizza_options;
		$bool = !empty($wppizza_options[$this->settings_page]['gateway_showorder_on_thankyou']) ? true : false;	
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
			$settings['sections'][$this->section_key] = __('Global', 'wppizza-admin');
		}
		/*help*/
		if($help){
			$settings['help'][$this->section_key][] = array(
				'label'=>__('Currency', 'wppizza-admin'),
				'description'=>array(
					__('Set the currency you want to use throughout', 'wppizza-admin')
				)
			);
			$settings['help'][$this->section_key][] = array(
				'label'=>__('Order Page', 'wppizza-admin'),
				'description'=>array(
					__('You probably do NOT want to display the shopping cart on this page (although it won\'t break things if you do)', 'wppizza-admin')
				)
			);
			$settings['help'][$this->section_key][] = array(
				'label'=>__('Exclude Order Page', 'wppizza-admin'),
				'description'=>array(
					__('Excludes the set order page from any of your navigation menus (unless you specifically add it)', 'wppizza-admin')
				)
			);
			$settings['help'][$this->section_key][] = array(
				'label'=>__('Append internal ID', 'wppizza-admin'),
				'description'=>array(
					__('If you enable this option, the internal order ID of the database table will be appended to the transaction ID [e.g COD13966037358 will become COD13966037358/123 where 123 = internal id of order table]', 'wppizza-admin')
				)
			);
			$settings['help'][$this->section_key][] = array(
				'label'=>__('Show Order Details on "Thank You" page', 'wppizza-admin'),
				'description'=>array(
					__('Enable to show full order details on "thank you" page after payment', 'wppizza-admin')
				)
			);						
		}		
		/*fields*/
		if($fields){
			$field = 'currency';
			$settings['fields'][$this->section_key][$field] = array( __('Currency', 'wppizza-admin'), array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('set to --none-- to have no currency displayed anywhere', 'wppizza-admin') . ' <span style="color:red;">' . __('Note: A currency must be set if you want to accept credit card payments', 'wppizza-admin') .'</span>' ,
				'description'=>array()
			));	
			$field = 'orderpage';
			$settings['fields'][$this->section_key][$field] = array( __('Order Page', 'wppizza-admin'), array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('ensure the page includes [wppizza type="orderpage"] or the widget equivalent.', 'wppizza-admin'),
				'description'=>array()
			));	
			$field = 'orderpage_exclude';
			$settings['fields'][$this->section_key][$field] = array( __('Exclude "Order Page" from menu', 'wppizza-admin'), array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('To exclude this page from your navigation menu, check this option.', 'wppizza-admin'),
				'description'=>array()
			));	
			$field = 'append_internal_id_to_transaction_id';
			$settings['fields'][$this->section_key][$field] = array( __('Append internal ID', 'wppizza-admin'), array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('Append internal database ID to transaction ID', 'wppizza-admin'),
				'description'=>array()
			));	
			$field = 'gateway_showorder_on_thankyou';
			$settings['fields'][$this->section_key][$field] = array( __('Show Order Details on "Thank You" page', 'wppizza-admin'), array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('Will add any order details after your thank you text on successful order.', 'wppizza-admin'),
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
		*	[currency / order page / Add internal ID to transaction ID]
		********************************/
		if($field=='currency'){
			echo "<label>";
				echo "<select name='".WPPIZZA_SLUG."[".$options_key."][".$field."]'>";
				foreach(wppizza_currencies($wppizza_options[$options_key][$field]) as $l=>$m){
					echo "<option value='".$m['id']."' ".$m['selected'].">[".$m['id']."] - ".$m['value']."</option>";
				}
				echo "</select>";
				echo "".$label."";
			echo "</label>";			
			echo"".$description."";			
		}
		if($field=='orderpage'){
			echo "<label>";
				wp_dropdown_pages('name='.WPPIZZA_SLUG.'['.$options_key.']['.$field.']&selected='.$wppizza_options[$options_key][$field].'&show_option_none='.__('select your orderpage', 'wppizza-admin').'');
				echo "".$label."";
			echo "</label>";
			echo"".$description."";
		}
		
		if($field=='orderpage_exclude'){
			echo "<label>";
				echo "<input id='".$field."' name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' type='checkbox'  ". checked($wppizza_options[$options_key][$field],true,false)." value='1' />";
				echo "".$label."";
			echo "</label>";			
			echo"".$description."";
		}		
		
		if($field=='append_internal_id_to_transaction_id'){
			echo "<label>";
				echo "<input name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' type='checkbox'  ". checked($wppizza_options[$options_key][$field],true,false)." value='1' />";
				echo "".$label."";
			echo "</label>";				
			echo"".$description."";	
		}	

		if($field=='gateway_showorder_on_thankyou'){
			echo "<label>";
				echo "<input name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' type='checkbox'  ". checked($wppizza_options[$options_key][$field],true,false)." value='1' />";
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
		
		$options[$this->settings_page]['currency'] = 'GBP';
		$options[$this->settings_page]['currency_symbol'] = '£';
		$options[$this->settings_page]['orderpage'] = ''; /*set to empty initially, install class will overwrite if orderpage is installed/created*/
		$options[$this->settings_page]['orderpage_exclude'] = true;
		$options[$this->settings_page]['append_internal_id_to_transaction_id'] = false;
		$options[$this->settings_page]['gateway_showorder_on_thankyou'] = true;			
		
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
			$options[$this->settings_page]['currency'] = strtoupper($input[$this->settings_page]['currency']);
				$currency_symbol=wppizza_currencies($input[$this->settings_page]['currency'],true);
			$options[$this->settings_page]['currency_symbol'] = $currency_symbol['val'];
			$options[$this->settings_page]['orderpage'] = !empty($input[$this->settings_page]['orderpage']) ? (int)$input[$this->settings_page]['orderpage'] : false;
			$options[$this->settings_page]['orderpage_exclude']=!empty($input[$this->settings_page]['orderpage_exclude']) ? true : false;
			$options[$this->settings_page]['append_internal_id_to_transaction_id'] = !empty($input[$this->settings_page]['append_internal_id_to_transaction_id']) ? true : false;
			$options[$this->settings_page]['gateway_showorder_on_thankyou'] = !empty($input[$this->settings_page]['gateway_showorder_on_thankyou']) ? true : false;			
		
		}
	return $options;
	}
}
/***************************************************************
*
*	[ini]
*
***************************************************************/
$WPPIZZA_MODULE_ORDER_SETTINGS_GLOBAL = new WPPIZZA_MODULE_ORDER_SETTINGS_GLOBAL();
?>