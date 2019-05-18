<?php
/**
* WPPIZZA_MODULE_TOOLS_SYSINFO_WPPIZZAVARS Class
*
* @package     WPPIZZA
* @subpackage  WPPIZZA_MODULE_TOOLS_SYSINFO_WPPIZZAVARS
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
class WPPIZZA_MODULE_TOOLS_SYSINFO_WPPIZZAVARS{

	private $settings_page = 'tools';/* which admin subpage (identified there by this->class_key) are we adding this to */
	private $tab_key = 'sysinfo';/* must be unique within this admin page*/
	private $section_key = 'wppizza_vars';

	function __construct() {
		/**********************************************************
			[add settings to admin]
		***********************************************************/
		if(is_admin()){
			/*** add to a specific tab ***/
			add_filter('wppizza_filter_admin_tabs_'.$this->settings_page.'', array($this, 'admin_tabs'), 20);			
			/* add admin options settings page*/
			add_filter('wppizza_filter_settings_sections_'.$this->settings_page.'', array($this, 'admin_options_settings'), 20, 5);
			/* add admin options settings page fields */
			add_action('wppizza_admin_settings_section_fields_'.$this->settings_page.'', array($this, 'admin_options_fields_settings'), 10, 5);
		}
	}
//
//	/*******************************************************************************************************************************************************
//	*
//	*
//	*
//	* 	[frontend filters]
//	*
//	*
//	*
//	********************************************************************************************************************************************************/
//
//
//
//	/*******************************************************************************************************************************************************
//	*
//	*
//	*
//	* 	[add admin page options]
//	*
//	*
//	*
//	********************************************************************************************************************************************************/
//
//	/*------------------------------------------------------------------------------
//	#
//	#
//	#	[settings page]
//	#
//	#
//	------------------------------------------------------------------------------*/


	/*********************************************************
			[add section to a particular tab]
	*********************************************************/
	function admin_tabs($tabs){
		$tabs['tab'][$this->tab_key]['sections'][] = $this->section_key;
		return $tabs;
	}
//	/*------------------------------------------------------------------------------
//	#	[settings section - setting page]
//	#	@since 3.0
//	#	@return array()
//	------------------------------------------------------------------------------*/
	function admin_options_settings($settings, $sections, $fields, $inputs, $help){

		/*section*/
		if($sections){
			$settings['sections'][$this->section_key] =  __('Plugin Parameters', 'wppizza-admin');
		}

		/*help*/
		if($help){
		}

		/*fields*/
		if($fields){
			$field = 'wppizza_vars';
			$settings['fields'][$this->section_key][$field] = array('' , array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>'',
				'description'=>array()
			));
		}
	return $settings;
	}
//	/*------------------------------------------------------------------------------
//	#	[output option fields - setting page]
//	#	@since 3.0
//	#	@return array()
//	------------------------------------------------------------------------------*/
	function admin_options_fields_settings($wppizza_options, $options_key, $field, $label, $description){

		if($field=='wppizza_vars'){
			echo"<textarea  readonly='readonly' onclick='this.focus();this.select();' style='width:100%;height:150px'>### ALL ".WPPIZZA_NAME." VARIABLES ###".PHP_EOL.print_r(maybe_serialize($wppizza_options),true)."</textarea>";		
		
		}
	}
//
//	/****************************************************************
//	*
//	*	[insert default option on install]
//	*	$parameter $options array() | filter passing on filtered options
//	*	@since 3.0
//	*	@return array()
//	*
//	****************************************************************/
//	function options_default($options){
//
////		$options[$this->settings_page]['add_to_cart_on_title_click'] = false;
//
//		return $options;
//	}
//
//	/*------------------------------------------------------------------------------
//	#	[validate options on save/update]
//	#
//	#	@since 3.0
//	#	@return array()
//	------------------------------------------------------------------------------*/
//	function options_validate($options, $input){
//		/**make sure we get the full array on install/update**/
//		if ( empty( $_POST['_wp_http_referer'] ) ) {
//			return $input;
//		}
//		if(isset($_POST[''.WPPIZZA_SLUG.'_'.$this->settings_page.'_'.$this->tab_key.''])){
//
////			$options[$this->settings_page]['add_to_cart_on_title_click'] = !empty($input[$this->settings_page]['add_to_cart_on_title_click']) ? true : false;
//
//		}
//
//	return $options;
//	}
}
/***************************************************************
*
*	[ini]
*
***************************************************************/
$WPPIZZA_MODULE_TOOLS_SYSINFO_WPPIZZAVARS = new WPPIZZA_MODULE_TOOLS_SYSINFO_WPPIZZAVARS();
?>