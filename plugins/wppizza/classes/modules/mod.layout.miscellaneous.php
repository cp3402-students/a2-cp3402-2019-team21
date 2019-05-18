<?php
/**
* WPPIZZA_MODULE_LAYOUT_MISCELLANEOUS Class
*
* @package     WPPIZZA
* @subpackage  WPPIZZA_MODULE_LAYOUT_MISCELLANEOUS
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
class WPPIZZA_MODULE_LAYOUT_MISCELLANEOUS{

	private $settings_page = 'layout';/* which admin subpage (identified there by this->class_key) are we adding this to */

	private $section_key = 'miscellaneous';/* must be unique */


	function __construct() {
		/**********************************************************
			[add settings to admin]
		***********************************************************/
		if(is_admin()){
			/* add admin options settings page*/
			add_filter('wppizza_filter_settings_sections_'.$this->settings_page.'', array($this, 'admin_options_settings'), 70, 5);
			/* add admin options settings page fields */
			add_action('wppizza_admin_settings_section_fields_'.$this->settings_page.'', array($this, 'admin_options_fields_settings'), 10, 5);
			/**add default options **/
			add_filter('wppizza_filter_setup_default_options', array( $this, 'options_default'));
			/**validate options**/
			add_filter('wppizza_filter_options_validate', array( $this, 'options_validate'), 10, 2 );
			/**add text header*/
			//add_action('wppizza_settings_sections_header_'.$this->settings_page.'', array( $this, 'sections_header'), 10, 2 );
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
			$settings['sections'][$this->section_key] =  __('Miscellaneous', 'wppizza-admin');
		}

		/*help*/
		if($help){
		}

		/*fields*/
		if($fields){

			$field = 'add_to_cart_on_title_click';
			$settings['fields'][$this->section_key][$field] = array( __('Click title to add to cart', 'wppizza-admin') , array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('Add item to cart on click of *item title* if there is only one pricetier for a menu item:', 'wppizza-admin'),
				'description'=>array()
			));
			$field = 'jquery_fb_add_to_cart';
			$settings['fields'][$this->section_key][$field] = array( __('Briefly display text in place of price when adding item to cart', 'wppizza-admin') , array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('Replace item price with customised text when adding an item to cart [set/edit text in localization]', 'wppizza-admin'),
				'description'=>array()
			));
			$field = 'suppress_loop_headers';
			$settings['fields'][$this->section_key][$field] = array( __('Suppress category header', 'wppizza-admin') , array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('Globally suppress category header and description above list of menu items (same as setting noheader=1 in each shortcode)', 'wppizza-admin'),
				'description'=>array()
			));
			$field = 'hide_cart_icon';
			$settings['fields'][$this->section_key][$field] = array( __('Hide cart icon next to prices', 'wppizza-admin') , array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>'',
				'description'=>array()
			));
			$field = 'hide_single_pricetier';
			$settings['fields'][$this->section_key][$field] = array( __('Hide size name and cart icon if item has only one size', 'wppizza-admin') , array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>'',
				'description'=>array()
			));
			$field = 'prettify_js_alerts';
			$settings['fields'][$this->section_key][$field] = array( __('Stylable javascript alerts', 'wppizza-admin') , array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('Convert browser native javascript popup alert boxes to css stylable modal windows (this will not work for javascript confirm popups)', 'wppizza-admin'),
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

		if($field=='add_to_cart_on_title_click' ){
			print'<label>';
				echo "<input id='".$field."' name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' type='checkbox'  ". checked($wppizza_options['layout'][$field],true,false)." value='1' />";
				print'' . $label . '';
			print'</label>';
			print'' . $description . '';
		}
		if($field=='jquery_fb_add_to_cart'){
			print'<label>';
				echo "<input id='' name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' type='checkbox'  ". checked($wppizza_options[$options_key][$field],true,false)." value='1' />";
				print'' . $label . '';
			print'</label>';
			print'' . $description . '';

			print'<br />';
			print'<label>';
			echo "<input id='' name='".WPPIZZA_SLUG."[".$options_key."][jquery_fb_add_to_cart_ms]' size='4' type='text'  value='".$wppizza_options[$options_key]['jquery_fb_add_to_cart_ms']."' />";
			echo" ".__('How long is it visible for before reverting back to displaying price [in ms]', 'wppizza-admin')."";
			print'</label>';
		}

		if($field=='suppress_loop_headers'){
			print'<label>';
				echo "<input id='".$field."' name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' type='checkbox'  ". checked($wppizza_options[$options_key][$field],true,false)." value='1' />";
				print'' . $label . '';
			print'</label>';
			print'' . $description . '';
		}
		if($field=='hide_cart_icon'){
			print'<label>';
				echo "<input id='".$field."' name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' type='checkbox'  ". checked($wppizza_options[$options_key][$field],true,false)." value='1' />";
				print'' . $label . '';
			print'</label>';
			print'' . $description . '';
		}
		if($field=='hide_single_pricetier'){
			print'<label>';
				echo "<input id='".$field."' name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' type='checkbox'  ". checked($wppizza_options[$options_key][$field],true,false)." value='1' />";
				print'' . $label . '';
			print'</label>';
			print'' . $description . '';
		}
		if($field=='prettify_js_alerts'){
			print'<label>';
				echo "<input id='".$field."' name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' type='checkbox'  ". checked($wppizza_options[$options_key][$field],true,false)." value='1' />";
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

		$options[$this->settings_page]['add_to_cart_on_title_click'] = false;
		$options[$this->settings_page]['jquery_fb_add_to_cart'] = true;
		$options[$this->settings_page]['jquery_fb_add_to_cart_ms'] = 1000;
		$options[$this->settings_page]['suppress_loop_headers'] = false;
		$options[$this->settings_page]['hide_cart_icon'] = false;
		$options[$this->settings_page]['hide_single_pricetier'] = false;
		$options[$this->settings_page]['prettify_js_alerts'] = false;

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

			$options[$this->settings_page]['add_to_cart_on_title_click'] = !empty($input[$this->settings_page]['add_to_cart_on_title_click']) ? true : false;
			$options[$this->settings_page]['jquery_fb_add_to_cart'] = !empty($input[$this->settings_page]['jquery_fb_add_to_cart']) ? true : false;
			$options[$this->settings_page]['jquery_fb_add_to_cart_ms']=absint($input[$this->settings_page]['jquery_fb_add_to_cart_ms']);
			$options[$this->settings_page]['suppress_loop_headers'] = !empty($input[$this->settings_page]['suppress_loop_headers']) ? true : false;
			$options[$this->settings_page]['hide_cart_icon'] = !empty($input[$this->settings_page]['hide_cart_icon']) ? true : false;
			$options[$this->settings_page]['hide_single_pricetier'] = !empty($input[$this->settings_page]['hide_single_pricetier']) ? true : false;
			$options[$this->settings_page]['prettify_js_alerts'] = !empty($input[$this->settings_page]['prettify_js_alerts']) ? true : false;
		}

	return $options;
	}
}
/***************************************************************
*
*	[ini]
*
***************************************************************/
$WPPIZZA_MODULE_LAYOUT_MISCELLANEOUS = new WPPIZZA_MODULE_LAYOUT_MISCELLANEOUS();
?>