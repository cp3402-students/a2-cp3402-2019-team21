<?php
/**
* WPPIZZA_MODULE_OPENINGTIMES_CLOSE_SHOP_NOW Class
*
* @package     WPPIZZA
* @subpackage  WPPIZZA_MODULE_OPENINGTIMES_CLOSE_SHOP_NOW
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
class WPPIZZA_MODULE_OPENINGTIMES_CLOSE_SHOP_NOW{

	private $settings_page = 'openingtimes';/* which admin subpage (identified there by this->class_key) are we adding this to */


	private $section_key = 'close_shop_now';/* must be unique */


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
		/***alter search query if/when required to filter search to include wppizza etc**/
		add_filter( 'wppizza_shop_is_open', array( $this, 'close_shop_now' ), 1);
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
	/*******************************************************
		[force shop to be closed ]
	******************************************************/
	function close_shop_now( $is_open ) {
		global $wppizza_options;
		if(!empty($wppizza_options[$this->settings_page]['close_shop_now'])){
			$is_open = false;
		}
		return $is_open;
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
			$settings['sections'][$this->section_key] =  __('Close Shop Now', 'wppizza-admin');
		}
		/*help*/
		if($help){
			$settings['help'][$this->section_key][] = array(
				'label'=>__('Close Shop Now', 'wppizza-admin'),
				'description'=>array(
					__('Enabe to forcefully close the shop *right now* regardless of opening times or anything else set', 'wppizza-admin')
				)
			);
		}
		/*fields*/
		if($fields){
			$field = 'close_shop_now';
			$settings['fields'][$this->section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('enable and save to close the shop *right now* ignoring all set opening times', 'wppizza-admin'),
				'description'=>array(
					__('Do not forget to disable if you want to be open again !!', 'wppizza-admin')
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
		if($field=='close_shop_now'){
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

		$options[$this->settings_page]['close_shop_now']=false;

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
			$options[$this->settings_page]['close_shop_now'] = !empty($input[$this->settings_page]['close_shop_now']) ? true : false;
		}
	return $options;
	}
}
/***************************************************************
*
*	[ini]
*
***************************************************************/
$WPPIZZA_MODULE_OPENINGTIMES_CLOSE_SHOP_NOW = new WPPIZZA_MODULE_OPENINGTIMES_CLOSE_SHOP_NOW();
?>