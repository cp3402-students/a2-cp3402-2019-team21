<?php
/**
* WPPIZZA_MODULE_ACCESSRIGHTS_ACCESS Class
*
* @package     WPPIZZA
* @subpackage  WPPIZZA_MODULE_ACCESSRIGHTS_ACCESS
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
class WPPIZZA_MODULE_ACCESSRIGHTS_ACCESS{

	private $settings_page = 'access_rights';/* which admin subpage (identified there by this->class_key) are we adding this to */
	private $section_key = 'access';/* must be unique */
	private $module_priority = 10;/* display order (priority) of settings in subpage */


	function __construct() {
		/**********************************************************
			[add settings to admin]
		***********************************************************/
		if(is_admin()){
			/* add admin options settings page*/
			add_filter('wppizza_filter_settings_sections_'.$this->settings_page.'', array($this, 'admin_options_settings'), $this->module_priority, 5);
			/* add admin options settings page fields */
			add_action('wppizza_admin_settings_section_fields_'.$this->settings_page.'', array($this, 'admin_options_fields_settings'), 10, 5);
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
			$settings['sections'][$this->section_key] =  __('Access Permissions', 'wppizza-admin');
		}
		/*help*/
		if($help){
			$settings['help'][$this->section_key][] = array(
				'label'=>__('Access Permissions', 'wppizza-admin'),
				'description'=>array(
					__('Enable user roles that are allowed to access the available plugin pages', 'wppizza-admin')
				)
			);
		}
		/*fields*/
		if($fields){
			$field = 'show_admin_access_capabilities';
			$settings['fields'][$this->section_key][$field] = array( '', array(
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
		if($field=='show_admin_access_capabilities'){
			/* echo access capabilities */
			WPPIZZA()-> user_caps -> user_echo_admin_caps($field, WPPIZZA_SLUG, false, $this->section_key);
		}
	}
	/*------------------------------------------------------------------------------
	#	[insert default option on install]
	#	$parameter $options array() | filter passing on filtered options
	#	@since 3.0
	#	@return array()
	------------------------------------------------------------------------------*/
//	function options_default($options){
//
//		//$options[$this->settings_page]['close_shop_now']=false;
//
//	return $options;
//	}

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
			/* update available page capabilties */
			$caps = WPPIZZA() -> user_caps -> user_caps_update($input[$this->section_key]); 
			$options[key($caps)] = $caps[key($caps)];
		}
	return $options;
	}
}
/***************************************************************
*
*	[ini]
*
***************************************************************/
$WPPIZZA_MODULE_ACCESSRIGHTS_ACCESS = new WPPIZZA_MODULE_ACCESSRIGHTS_ACCESS();
?>