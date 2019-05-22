<?php
/**
* WPPIZZA_MODULE_ORDERSETTINGS_TAXES Class
*
* @package     WPPIZZA
* @subpackage  WPPIZZA_MODULE_ORDERSETTINGS_TAXES
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
class WPPIZZA_MODULE_ORDERSETTINGS_TAXES{

	private $settings_page = 'order_settings';/* which admin subpage (identified there by this->class_key) are we adding this to */

	private $section_key = 'taxes';/* must be unique */


	function __construct() {
		/**********************************************************
			[add settings to admin]
		***********************************************************/
		if(is_admin()){
			/* add admin options settings page*/
			add_filter('wppizza_filter_settings_sections_'.$this->settings_page.'', array($this, 'admin_options_settings'), 40, 5);
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
		/**metaboxes alt taxrate - priority same as submenu page **/
		add_filter('wppizza_filter_admin_metaboxes', array( $this, 'admin_add_metaboxes'), 20, 4);
		add_filter('wppizza_filter_admin_save_metaboxes',array( $this, 'admin_save_metaboxes'), 10, 3);
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
	*	[add metaboxes]
	*	@since 3.0
	*
	*********************************************************/
	function admin_add_metaboxes($wppizza_meta_box, $meta_values, $meal_sizes, $wppizza_options){

		/****  alternative taxrate ***/
		$wppizza_meta_box['alt_tax']='';
		$wppizza_meta_box['alt_tax'].="<div class='".WPPIZZA_SLUG."_option_meta'>";
		$wppizza_meta_box['alt_tax'].="<label class='wppizza-meta-label'>".sprintf( __( 'Alternative taxrate (%s%%)', 'wppizza-admin' ), wppizza_output_format_percent($wppizza_options[$this->settings_page]['item_tax_alt']) )." ? </label>";
		$wppizza_meta_box['alt_tax'].="<label class='button'>";
		$wppizza_meta_box['alt_tax'].="<input name='".WPPIZZA_SLUG."[item_tax_alt]' size='5' ". checked(!empty($meta_values['item_tax_alt']),true,false)." type='checkbox' value='1' /> ".__( 'yes/no', 'wppizza-admin' )."";
		$wppizza_meta_box['alt_tax'].="</label>";
		$wppizza_meta_box['alt_tax'].=" <span class='description'>[".__('set in wppizza->order settings', 'wppizza-admin')."]</span>";
		$wppizza_meta_box['alt_tax'].="</div>";


		return $wppizza_meta_box;		
	}	
	/*********************************************************
	*
	*	[save metaboxes values]
	*	@since 3.0
	*
	*********************************************************/	
	function admin_save_metaboxes($itemMeta, $item_id, $wppizza_options){

    	/**alt tax rate**/
    	$itemMeta['item_tax_alt'] = false;
    	if(isset($_POST[WPPIZZA_SLUG]['item_tax_alt'])){
	    	$itemMeta['item_tax_alt'] = true;
    	}
    	
		return $itemMeta;
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
			$settings['sections'][$this->section_key] = __('Taxes', 'wppizza-admin');
		}
		/*help*/
		if($help){
			$settings['help'][$this->section_key][] = array(
				'label'=>__('Taxes', 'wppizza-admin'),
				'description'=>array(
					__('Please adjust settings as appropriate according to the information provided next to each individual option.', 'wppizza-admin')
				)
			);
		}
		/*fields*/
		if($fields){
			$field = 'item_tax';
			$settings['fields'][$this->section_key][$field] = array( __('(Sales)Tax', 'wppizza-admin'), array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('applied to items in cart [in % - 0 to disable]', 'wppizza-admin'),
				'description'=>array()
			));			
			
			$field = 'item_tax_alt';
			$settings['fields'][$this->section_key][$field] = array( __('Alternative Taxrate', 'wppizza-admin'), array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('alternative taxrate [assign on a per menu item basis]', 'wppizza-admin'),
				'description'=>array()
			));	
			
			$field = 'shipping_tax';
			$settings['fields'][$this->section_key][$field] = array( __('Shipping Taxrate', 'wppizza-admin'), array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('apply tax to delivery/shipping too at', 'wppizza-admin'),
				'description'=>array()
			));	
			
			$field = 'taxes_included';
			$settings['fields'][$this->section_key][$field] = array( __('Prices include Tax', 'wppizza-admin'), array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('all prices are entered including tax, but I distinctly need to display the sum of taxes applied', 'wppizza-admin'),
				'description'=>array(
					__('if enabled, the sum of applicable taxes will be displayed separately without however adding it to the total (if taxrate > 0%).', 'wppizza-admin'),
					'<span class="wppizza-highlight">'.__('if you set different taxrates, make sure to set your text in wppizza->localization regarding taxes as appropriate', 'wppizza-admin').'</span>'
				)
			));			
			$field = 'taxes_round_natural';
			$settings['fields'][$this->section_key][$field] = array( __('Tax Rounding', 'wppizza-admin'), array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('Typically any decimal fractions of applicable taxes are rounded up. Tick this box if your tax laws allow for "natural" rounding (i.e rounding down if fractions are below .5)', 'wppizza-admin'),
				'description'=>array()
			));	
			
			$field = 'taxes_display';
			$settings['fields'][$this->section_key][$field] = array( __('Taxes Display', 'wppizza-admin'), array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>'',
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
		
		if($field=='item_tax'){
			echo "<label>";
				echo "<input id='".$field."' name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' size='5' type='text' value='".wppizza_output_format_percent($wppizza_options[$options_key][$field])."' />% ";
				echo "".$label."";
			echo "</label>";			
			echo"".$description."";
		}
		if($field=='item_tax_alt'){
			echo "<label>";
				echo "<input id='".$field."' name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' size='5' type='text' value='".wppizza_output_format_percent($wppizza_options[$options_key][$field])."' />% ";
				echo "".$label."";
			echo "</label>";			
			echo"".$description."";
		}
		if($field=='shipping_tax'){
			echo "<label>";
				echo"<input id='".$field."' name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' type='checkbox'  ". checked($wppizza_options[$options_key][$field],true,false)." value='1' />";
				echo "".$label."";
				echo "<input id='shipping_tax_rate'  name='".WPPIZZA_SLUG."[".$options_key."][shipping_tax_rate]' size='5' type='text' value='".wppizza_output_format_percent($wppizza_options[$options_key]['shipping_tax_rate'])."' />%";
			echo "</label>";			
			echo"".$description."";
		}
		if($field=='taxes_included'){
			echo "<label>";
				echo"<input name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' type='checkbox'  ". checked($wppizza_options[$options_key][$field],true,false)." value='1' />";
				echo "".$label."";
			echo "</label>";			
			echo"".$description."";
		}		
		if($field=='taxes_round_natural'){
			echo "<label>";
				echo"<input name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' type='checkbox'  ". checked($wppizza_options[$options_key][$field],true,false)." value='1' />";
				echo "".$label."";
			echo "</label>";			
			echo"".$description."";
		}	
		
	
		if($field=='taxes_display'){
			echo "<label>";
				/*
					set values for filter 
					wppizza_filter_combine_taxes
				*/
				//filter returns 1="false" [default] to show separate if there are separate taxrates
				echo "".__('auto', 'wppizza-admin')."<input id='".$field."' name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' type='radio'  ".checked($wppizza_options[$options_key][$field],1,false)." value='1' /> ";
				//filter returns 2= "true" to show combined tax only
				echo "".__('tax total only', 'wppizza-admin')."<input id='".$field."' name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' type='radio'  ".checked($wppizza_options[$options_key][$field],2,false)." value='2' /> ";
				
				/*
					if we ever enable the below 2 settings we should verify the output in class.wppizza.order.php  
					as these do not make too much sense yet
				*/
				//filter returns 3=  null to show both (alt and main tax as well as combined if one is empty)
				//echo "".__('both', 'wppizza-admin')."<input id='".$field."' name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' type='radio'  ".checked($wppizza_options[$options_key][$field],3,false)." value='3' />";
				// filter returns 4=  'force' to always show both (alt and main tax even if one is empty)
				//echo "".__('force both', 'wppizza-admin')."<input id='".$field."' name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' type='radio'  ".checked($wppizza_options[$options_key][$field],4,false)." value='4' />";
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

		$options[$this->settings_page]['item_tax'] = 0;
		$options[$this->settings_page]['item_tax_alt'] = 0;
		$options[$this->settings_page]['taxes_round_natural'] = false;
		$options[$this->settings_page]['taxes_display'] = 1;
		$options[$this->settings_page]['taxes_included'] = false;
		$options[$this->settings_page]['shipping_tax'] = false;
		$options[$this->settings_page]['shipping_tax_rate'] = 0; //to check perhaps only relevant for updates from very old version , in which case they can get lost really --> !empty($options[$this->settings_page]['item_tax']) ? $options[$this->settings_page]['item_tax'] : 0 ;/**on updates, use item_tax settings so as to not modify the initial behaviour in the frontend if enabled**/
		
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
			$options[$this->settings_page]['item_tax']=wppizza_validate_float_pc($input[$this->settings_page]['item_tax'],5);//5 decimals should really be enough i would have thought
			$options[$this->settings_page]['item_tax_alt']=wppizza_validate_float_pc($input[$this->settings_page]['item_tax_alt'],5);//5 decimals should really be enough i would have thought
			$options[$this->settings_page]['taxes_included'] = !empty($input[$this->settings_page]['taxes_included']) ? true : false;
			$options[$this->settings_page]['shipping_tax'] = !empty($input[$this->settings_page]['shipping_tax']) ? true : false;
			$options[$this->settings_page]['taxes_round_natural'] = !empty($input[$this->settings_page]['taxes_round_natural']) ? true : false;
			$options[$this->settings_page]['taxes_display'] = in_array($input[$this->settings_page]['taxes_display'], array(1,2,3,4)) ? $input[$this->settings_page]['taxes_display'] : 1;
			$options[$this->settings_page]['shipping_tax_rate']=wppizza_validate_float_pc($input[$this->settings_page]['shipping_tax_rate'],5);//5 decimals should really be enough i would have thought
		}
				
	return $options;
	}
}
/***************************************************************
*
*	[ini]
*
***************************************************************/
$WPPIZZA_MODULE_ORDERSETTINGS_TAXES = new WPPIZZA_MODULE_ORDERSETTINGS_TAXES();
?>