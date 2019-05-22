<?php
/**
* WPPIZZA_MODULE_LAYOUT_IMAGES Class
*
* @package     WPPIZZA
* @subpackage  WPPIZZA_MODULE_LAYOUT_IMAGES
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
class WPPIZZA_MODULE_LAYOUT_IMAGES{

	private $settings_page = 'layout';/* which admin subpage (identified there by this->class_key) are we adding this to */

	private $section_key = 'images';/* must be unique */


	function __construct() {
		/**********************************************************
			[add settings to admin]
		***********************************************************/
		if(is_admin()){
			/* add admin options settings page*/
			add_filter('wppizza_filter_settings_sections_'.$this->settings_page.'', array($this, 'admin_options_settings'), 60, 5);
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
			$settings['sections'][$this->section_key] =  __('Menu Item Images', 'wppizza-admin');
		}

		/*help*/
		if($help){
			$settings['help'][$this->section_key][] = array(
				'label'=>__('Menu Item Images', 'wppizza-admin'),
				'description'=>array(
					__('Set menu item image options according to the settings available', 'wppizza-admin')
				)
			);
		}

		/*fields*/
		if($fields){

			$field = 'placeholder_img';
			$settings['fields'][$this->section_key][$field] = array( __('Display placeholder image', 'wppizza-admin') , array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('Display a placeholder image when no featured image has been associated with a meal item', 'wppizza-admin'),
				'description'=>array()
			));
			$field = 'prettyPhoto';
			$settings['fields'][$this->section_key][$field] = array( __('Enable prettyPhoto', 'wppizza-admin') , array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('Enable prettyPhoto (Lightbox Clone) on menu item images', 'wppizza-admin'),
				'description'=>array()
			));

			$field = 'prettyPhotoStyle';
			$settings['fields'][$this->section_key][$field] = array( __('Set prettyPhoto Style', 'wppizza-admin') , array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('see wppizza.prettyPhoto.custom.js.php if you would like to adjust prettyPhoto options', 'wppizza-admin'),
				'description'=>array()
			));

			$field = 'cart_image';
			$settings['fields'][$this->section_key][$field] = array( __('Thumbnail on checkout', 'wppizza-admin') , array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('Display a small thumbnail of any featured image set next to each item in cart on checkout pages', 'wppizza-admin'),
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


		if($field=='placeholder_img'){
			print'<label>';
				echo "<input id='".$field."' name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' type='checkbox'  ". checked($wppizza_options[$options_key][$field],true,false)." value='1' />";
				print'' . $label . '';
			print'</label>';
			print'' . $description . '';
		}
		if($field=='cart_image'){
			print'<label>';
				echo "<input id='".$field."' name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' type='checkbox'  ". checked($wppizza_options[$options_key][$field],true,false)." value='1' />";
				print'' . $label . '';
			print'</label>';
			print'' . $description . '';
		}
		if($field=='prettyPhoto' ){
			print'<label>';
				echo "<input id='".$field."' name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' type='checkbox'  ". checked($wppizza_options[$options_key][$field],true,false)." value='1' />";
				print'' . $label . '';
			print'</label>';
			print'' . $description . '';
		}
		if($field=='prettyPhotoStyle'){
			print'<label>';
				echo "<select id='".$field."' name='".WPPIZZA_SLUG."[".$options_key."][".$field."]'>";
					echo "<option value='pp_default' ".selected($wppizza_options[$options_key][$field],"pp_default",false).">Default</option>";
					echo "<option value='light_rounded' ".selected($wppizza_options[$options_key][$field],"light_rounded",false).">Light rounded</option>";
					echo "<option value='dark_rounded' ".selected($wppizza_options[$options_key][$field],"dark_rounded",false).">Dark rounded</option>";
					echo "<option value='light_square' ".selected($wppizza_options[$options_key][$field],"light_square",false).">Light square</option>";
					echo "<option value='dark_square' ".selected($wppizza_options[$options_key][$field],"dark_square",false).">Dark square</option>";
					echo "<option value='facebook' ".selected($wppizza_options[$options_key][$field],"facebook",false).">Facebook</option>";
				echo "</select>";
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

		$options[$this->settings_page]['placeholder_img'] = true;
		$options[$this->settings_page]['cart_image'] = false;
		$options[$this->settings_page]['prettyPhoto'] = false;
		$options[$this->settings_page]['prettyPhotoStyle'] = 'pp_default';

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
			$options[$this->settings_page]['placeholder_img'] = !empty($input[$this->settings_page]['placeholder_img']) ? true : false;
			$options[$this->settings_page]['cart_image'] = !empty($input[$this->settings_page]['cart_image']) ? true : false;
			$options[$this->settings_page]['prettyPhoto'] = !empty($input[$this->settings_page]['prettyPhoto']) ? true : false;
			$options[$this->settings_page]['prettyPhotoStyle']=wppizza_validate_string($input[$this->settings_page]['prettyPhotoStyle']);
		}

	return $options;
	}
}
/***************************************************************
*
*	[ini]
*
***************************************************************/
$WPPIZZA_MODULE_LAYOUT_IMAGES = new WPPIZZA_MODULE_LAYOUT_IMAGES();
?>