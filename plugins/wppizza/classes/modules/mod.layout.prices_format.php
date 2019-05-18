<?php
/**
* WPPIZZA_MODULE_LAYOUT_PRICES_FORMAT Class
*
* @package     WPPIZZA
* @subpackage  WPPIZZA_MODULE_LAYOUT_PRICES_FORMAT
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
class WPPIZZA_MODULE_LAYOUT_PRICES_FORMAT{

	private $settings_page = 'layout';/* which admin subpage (identified there by this->class_key) are we adding this to */

	private $section_key = 'prices_format';/* must be unique */


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
			$settings['sections'][$this->section_key] =  __('Prices / Currency Symbols', 'wppizza-admin');
		}

		/*help*/
		if($help){
			$settings['help'][$this->section_key][] = array(
				'label'=>__('Prices / Currency Symbols', 'wppizza-admin'),
				'description'=>array(
					__('Format prices and currency symbols output according to the settings available', 'wppizza-admin')
				)
			);
		}

		/*fields*/
		if($fields){

			$field = 'hide_prices';
			$settings['fields'][$this->section_key][$field] = array( __('Hide prices', 'wppizza-admin') , array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>'<span class="wppizza-highlight">'.__('this will disable the adding of any item to the shoppingcart.', 'wppizza-admin'),'</span>',
				'description'=>array(
					__('Really only useful if you want to display your menu without offering online orders', 'wppizza-admin')
				)
			));
			$field = 'hide_decimals';
			$settings['fields'][$this->section_key][$field] = array( __('No decimals', 'wppizza-admin') , array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('Do no display any decimals', 'wppizza-admin'),
				'description'=>array(
					__('prices will be rounded if necessary', 'wppizza-admin')
				)
			));
			$field = 'show_currency_with_price';
			$settings['fields'][$this->section_key][$field] = array( __('Show a currency symbol directly next to each price', 'wppizza-admin') , array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>'',
				'description'=>array()
			));
			$field = 'hide_item_currency_symbol';
			$settings['fields'][$this->section_key][$field] = array( __('*Main* currency symbol', 'wppizza-admin') , array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('Hide *main* currency symbol next to each menu item', 'wppizza-admin'),
				'description'=>array(
					__('will not affect cart, summaries or emails', 'wppizza-admin')
				)
			));
			$field = 'currency_symbol_left';
			$settings['fields'][$this->section_key][$field] = array( __('*Main* currency symbol position', 'wppizza-admin') , array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('Show *main* currency symbol on the left - if not set to hidden', 'wppizza-admin'),
				'description'=>array(
					__('by default, the main currency symbol is displayed on the right of all prices / sizes when listing menu items', 'wppizza-admin')
				)
			));
			$field = 'currency_symbol_position';
			$settings['fields'][$this->section_key][$field] = array( __('All other [cart, order page, email] currency symbols', 'wppizza-admin') , array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>'',
				'description'=>array()
			));
			$field = 'currency_symbol_spacing';
			$settings['fields'][$this->section_key][$field] = array( __('Currency symbol spacing', 'wppizza-admin') , array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('add space between currency symbol and price', 'wppizza-admin'),
				'description'=>array()
			));
			$field = 'localize_zero_price';
			$settings['fields'][$this->section_key][$field] = array( __('Zero price as "Free"', 'wppizza-admin') , array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('Display price as "Free" if it equals zero (Set string in Localization->Miscellaneous))', 'wppizza-admin'),
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

		if($field=='hide_prices'){
			print'<label>';
				echo "<input id='".$field."' name='".WPPIZZA_SLUG."[".$this->section_key."][".$field."]' type='checkbox'  ". checked($wppizza_options[$this->section_key][$field],true,false)." value='1' />";
				print'' . $label . '';
			print'</label>';
			print'' . $description . '';
		}
		if($field=='hide_decimals' ){
			print'<label>';
				echo "<input id='".$field."' name='".WPPIZZA_SLUG."[".$this->section_key."][".$field."]' type='checkbox'  ". checked($wppizza_options[$this->section_key][$field],true,false)." value='1' />";
				print'' . $label . '';
			print'</label>';
			print'' . $description . '';
		}
		if($field=='show_currency_with_price'){
			print'<label>';
				echo "".__('do not show', 'wppizza-admin')." <input id='".$field."' name='".WPPIZZA_SLUG."[".$this->section_key."][".$field."]' type='radio'  ".checked($wppizza_options[$this->section_key][$field],0,false)." value='0' /> ";
				echo "".__('on left', 'wppizza-admin')." <input id='".$field."' name='".WPPIZZA_SLUG."[".$this->section_key."][".$field."]' type='radio'  ".checked($wppizza_options[$this->section_key][$field],1,false)." value='1' />";
				echo "".__('on right', 'wppizza-admin')." <input id='".$field."' name='".WPPIZZA_SLUG."[".$this->section_key."][".$field."]' type='radio'  ".checked($wppizza_options[$this->section_key][$field],2,false)." value='2' />";
				print'' . $label . '';
			print'</label>';
			print'' . $description . '';
		}
		if($field=='hide_item_currency_symbol'){
			print'<label>';
				echo "<input id='".$field."' name='".WPPIZZA_SLUG."[".$this->section_key."][".$field."]' type='checkbox'  ". checked($wppizza_options[$this->section_key][$field],true,false)." value='1' />";
				print'' . $label . '';
			print'</label>';
			print'' . $description . '';
		}
		if($field=='currency_symbol_left'){
			print'<label>';
				echo "<input id='".$field."' name='".WPPIZZA_SLUG."[".$this->section_key."][".$field."]' type='checkbox'  ". checked($wppizza_options[$this->section_key][$field],true,false)." value='1' />";
				print'' . $label . '';
			print'</label>';
			print'' . $description . '';
		}
		if($field=='currency_symbol_position'){
			print'<label>';
				echo "".__('on left', 'wppizza-admin')." <input id='".$field."' name='".WPPIZZA_SLUG."[".$this->section_key."][".$field."]' type='radio'  ".checked($wppizza_options[$this->section_key][$field],'left',false)." value='left' />";
				echo "".__('on right', 'wppizza-admin')." <input id='".$field."' name='".WPPIZZA_SLUG."[".$this->section_key."][".$field."]' type='radio'  ".checked($wppizza_options[$this->section_key][$field],'right',false)." value='right' />";
				print'' . $label . '';
			print'</label>';
			print'' . $description . '';
		}
		if($field=='currency_symbol_spacing'){
			print'<label>';
				echo "<input id='".$field."' name='".WPPIZZA_SLUG."[".$this->section_key."][".$field."]' type='checkbox'  ". checked($wppizza_options[$this->section_key][$field],true,false)." value='1' />";
				print'' . $label . '';
			print'</label>';
			print'' . $description . '';
		}
		if($field=='localize_zero_price'){
			print'<label>';
				echo "<input id='".$field."' name='".WPPIZZA_SLUG."[".$this->section_key."][".$field."]' type='checkbox'  ". checked($wppizza_options[$this->section_key][$field],true,false)." value='1' />";
				print'' . $label . '';
			print'</label>';
			print'' . $description . '';
		}

	}

	/****************************************************************
	*
	*	[insert default option on install]
	*	$parameter $options array() | filter passing on filtered options
	*	@since 3.0
	*	@return array()
	*
	****************************************************************/
	function options_default($options){


		$options[$this->section_key]['hide_prices'] = false;
		$options[$this->section_key]['hide_decimals'] = false;
		$options[$this->section_key]['show_currency_with_price'] = 0;
		$options[$this->section_key]['hide_item_currency_symbol'] = false;
		$options[$this->section_key]['currency_symbol_left'] = false;
		$options[$this->section_key]['currency_symbol_position'] = 'left';
		$options[$this->section_key]['currency_symbol_spacing'] = true;
		$options[$this->section_key]['localize_zero_price'] = false;


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
		if(isset($_POST[''.WPPIZZA_SLUG.'_'.$this->settings_page.''])){
			$options[$this->section_key]['hide_prices'] = !empty($input[$this->section_key]['hide_prices']) ? true : false;
			$options[$this->section_key]['hide_decimals'] = !empty($input[$this->section_key]['hide_decimals']) ? true : false;
			$options[$this->section_key]['show_currency_with_price'] = wppizza_validate_int_only($input[$this->section_key]['show_currency_with_price']);
			$options[$this->section_key]['hide_item_currency_symbol'] = !empty($input[$this->section_key]['hide_item_currency_symbol']) ? true : false;
			$options[$this->section_key]['currency_symbol_left'] = !empty($input[$this->section_key]['currency_symbol_left']) ? true : false;
			$options[$this->section_key]['currency_symbol_position'] = preg_replace("/[^a-z]/","",$input[$this->section_key]['currency_symbol_position']);
			$options[$this->section_key]['currency_symbol_spacing'] = !empty($input[$this->section_key]['currency_symbol_spacing']) ? true : false;
			$options[$this->section_key]['localize_zero_price'] = !empty($input[$this->section_key]['localize_zero_price']) ? true : false;
		}

	return $options;
	}
}
/***************************************************************
*
*	[ini]
*
***************************************************************/
$WPPIZZA_MODULE_LAYOUT_PRICES_FORMAT = new WPPIZZA_MODULE_LAYOUT_PRICES_FORMAT();
?>