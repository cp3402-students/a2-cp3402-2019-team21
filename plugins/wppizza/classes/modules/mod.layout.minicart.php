<?php
/**
* WPPIZZA_MODULE_LAYOUT_MINICART Class
*
* @package     WPPIZZA
* @subpackage  WPPIZZA_MODULE_LAYOUT_MINICART
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
class WPPIZZA_MODULE_LAYOUT_MINICART{

	private $settings_page = 'layout';/* which admin subpage (identified there by this->class_key) are we adding this to */

	private $section_key = 'minicart';/* must be unique */


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
			/**add text header*/
			add_action('wppizza_settings_sections_header_'.$this->settings_page.'', array( $this, 'sections_header'), 10, 2 );			
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

	/*********************************************************
			[more header info]
	*********************************************************/
	function sections_header($arg, $section_count){
		if($arg['id']==$this->section_key){
			echo '<div>'.__('[only applicable if enabled in widget or when using shortcodes]', 'wppizza-admin').'</div>';
			echo '<span class="wppizza-highlight">'.__('The "Minicart" could interfere with some layouts. If that is the case, using some of the options provided here with additional css declarations *might* let you get around this.', 'wppizza-admin').'</span>';
		}
	}
	/*------------------------------------------------------------------------------
	#	[settings section - setting page]
	#	@since 3.0
	#	@return array()
	------------------------------------------------------------------------------*/
	function admin_options_settings($settings, $sections, $fields, $inputs, $help){

		/*section*/
		if($sections){
			$settings['sections'][$this->section_key] =  __('Minicart', 'wppizza-admin');
		}

		/*help*/
		if($help){
			$settings['help'][$this->section_key][] = array(
				'label'=>__('Minicart Settings [if used in widget or shortcode]', 'wppizza-admin'),
				'description'=>array(
					__('Set options according to the settings available', 'wppizza-admin')
				)
			);
		}

		/*fields*/
		if($fields){

			$field = 'minicart_viewcart';
			$settings['fields'][$this->section_key][$field] = array( __('Display "view cart" button', 'wppizza-admin') , array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>'',
				'description'=>array()
			));
			$field = 'minicart_checkout';
			$settings['fields'][$this->section_key][$field] = array( __('Display "checkout" button', 'wppizza-admin') , array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('only shown if user can actually order', 'wppizza-admin'),
				'description'=>array()
			));	
			$field = 'minicart_itemcount';
			$settings['fields'][$this->section_key][$field] = array( __('Show count of items', 'wppizza-admin') , array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>'',
				'description'=>array()
			));				
			$field = 'minicart_position';
			$settings['fields'][$this->section_key][$field] = array( __('Position', 'wppizza-admin') , array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>'',
				'description'=>array()
			));						
			$field = 'minicart_always_shown';
			$settings['fields'][$this->section_key][$field] = array( __('Always show minicart', 'wppizza-admin') , array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('always show minicart, even if main cart is in view (if no main cart has been added anywhere, this will automatically be the case)', 'wppizza-admin'),
				'description'=>array()
			));
			$field = 'minicart_max_width_active';
			$settings['fields'][$this->section_key][$field] = array( __('Max browser width', 'wppizza-admin') , array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('max browser width up to which the minicart will be shown.', 'wppizza-admin'),
				'description'=>array(
					__('Useful for themes that, under a certain browser window width, change to a responsive design that moves elements to different places. [in px, 0 to ignore]', 'wppizza-admin')
				)
			));
			$field = 'minicart_elm_padding_top';
			$settings['fields'][$this->section_key][$field] = array( __('Top padding', 'wppizza-admin') , array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('add additional top padding to body element if small cart is displayed. [in px, 0 to ignore]', 'wppizza-admin'),
				'description'=>array()
			));
			$field = 'minicart_add_to_element';
			$settings['fields'][$this->section_key][$field] = array( __('Append to element', 'wppizza-admin') , array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('by default, the minicart will be added just before closing body tag with a css of position:fixed;top:0. if you want it appended elsewhere, set the relevant element here', 'wppizza-admin'),
				'description'=>array(
					__('use jQuery selectors, such as #my-elm-id or .my_elm_class etc. You might have to use additional css declarations for your theme.', 'wppizza-admin')
				)
			));										
			$field = 'minicart_elm_padding_selector';
			$settings['fields'][$this->section_key][$field] = array( __('Alt Element Padding', 'wppizza-admin') , array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__(' add above set padding to another element *instead* of the body tag', 'wppizza-admin'),
				'description'=>array(
					__('use jQuery selectors, such as #my-elm-id or .my_elm_class etc', 'wppizza-admin')
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

		if($field=='minicart_viewcart'){
			print'<label>';
				echo "<input name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' type='checkbox'  ". checked($wppizza_options[$options_key][$field],true,false)." value='1' />";
				print'' . $label . '';
			print'</label>';
			print'' . $description . '';
		}
		if($field=='minicart_checkout'){
			print'<label>';
				echo "<input name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' type='checkbox'  ". checked($wppizza_options[$options_key][$field],true,false)." value='1' />";
				print'' . $label . '';
			print'</label>';
			print'' . $description . '';
		}				
		
		if($field=='minicart_itemcount'){
			print'<label>';
				echo "<select name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' />";
					echo"<option value='' ".selected($wppizza_options[$options_key][$field],"",false).">".__('Do not show', 'wppizza-admin')."</option>";
					echo"<option value='left' ".selected($wppizza_options[$options_key][$field],"left",false).">".__('Left', 'wppizza-admin')."</option>";
					echo"<option value='right' ".selected($wppizza_options[$options_key][$field],"right",false).">".__('Right', 'wppizza-admin')."</option>";
				echo "</select>";
			print'</label>';
			print'' . $description . '';
		}			

		if($field=='minicart_position'){
			print'<label>';
				echo "<select name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' />";
					echo"<option value='top' ".selected($wppizza_options[$options_key][$field],"top",false).">".__('Top', 'wppizza-admin')."</option>";
					echo"<option value='bottom' ".selected($wppizza_options[$options_key][$field],"bottom",false).">".__('Bottom', 'wppizza-admin')."</option>";
				echo "</select>";
			print'</label>';
			print'' . $description . '';
		}
		
		if($field=='minicart_always_shown'){
			print'<label>';
				echo "<input name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' type='checkbox'  ". checked($wppizza_options[$options_key][$field],true,false)." value='1' />";
				print'' . $label . '';
			print'</label>';
			print'' . $description . '';
		}
		if($field=='minicart_max_width_active'){
			print'<label>';
				echo "<input name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' size='2' type='text'  value='".$wppizza_options[$options_key][$field]."' />";
				print'' . $label . '';
			print'</label>';
			print'' . $description . '';
		}
		if($field=='minicart_elm_padding_top'){
			print'<label>';
				echo "<input name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' size='2' type='text'  value='".$wppizza_options[$options_key][$field]."' />";
				print'' . $label . '';
			print'</label>';
			print'' . $description . '';
		}
		if($field=='minicart_add_to_element'){
			print'<label>';
				echo "<input name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' size='20' type='text'  value='".$wppizza_options[$options_key][$field]."' />";
				print'' . $label . '';
			print'</label>';
			print'' . $description . '';
		}
		if($field=='minicart_elm_padding_selector'){
			print'<label>';
			echo "<input name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' size='20' type='text'  value='".$wppizza_options[$options_key][$field]."' />";
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

		$options[$this->settings_page]['minicart_max_width_active'] = 0;
		$options[$this->settings_page]['minicart_elm_padding_top'] = 0;
		$options[$this->settings_page]['minicart_elm_padding_selector'] = '';
		$options[$this->settings_page]['minicart_add_to_element'] = '';
		$options[$this->settings_page]['minicart_always_shown'] = false;
		$options[$this->settings_page]['minicart_viewcart'] = true;
		$options[$this->settings_page]['minicart_checkout'] = true;
		$options[$this->settings_page]['minicart_itemcount'] = 'left';
		$options[$this->settings_page]['minicart_position'] = 'top';
		
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

			$options[$this->settings_page]['minicart_always_shown'] = !empty($input[$this->settings_page]['minicart_always_shown']) ? true : false;
			$options[$this->settings_page]['minicart_viewcart'] = !empty($input[$this->settings_page]['minicart_viewcart']) ? true : false;							
			$options[$this->settings_page]['minicart_checkout'] = !empty($input[$this->settings_page]['minicart_checkout']) ? true : false;	
			$options[$this->settings_page]['minicart_itemcount']=wppizza_validate_string($input[$this->settings_page]['minicart_itemcount']);
			$options[$this->settings_page]['minicart_position']=wppizza_validate_string($input[$this->settings_page]['minicart_position']);
			
			$options[$this->settings_page]['minicart_max_width_active']=wppizza_validate_int_only($input[$this->settings_page]['minicart_max_width_active']);
			$options[$this->settings_page]['minicart_elm_padding_top']=wppizza_validate_int_only($input[$this->settings_page]['minicart_elm_padding_top']);
			$options[$this->settings_page]['minicart_add_to_element']=preg_replace("/[^a-zA-Z0-9#>\-_\., ]/","",$input[$this->settings_page]['minicart_add_to_element']);
			$options[$this->settings_page]['minicart_elm_padding_selector']=preg_replace("/[^a-zA-Z0-9#>\-_\., ]/","",$input[$this->settings_page]['minicart_elm_padding_selector']);

						
		
		}

	return $options;
	}
}
/***************************************************************
*
*	[ini]
*
***************************************************************/
$WPPIZZA_MODULE_LAYOUT_MINICART = new WPPIZZA_MODULE_LAYOUT_MINICART();
?>