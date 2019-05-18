<?php
/**
* WPPIZZA_MODULE_LAYOUT_GATEWAYS Class
*
* @package     WPPIZZA
* @subpackage  WPPIZZA_MODULE_LAYOUT_GATEWAYS
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
class WPPIZZA_MODULE_LAYOUT_GATEWAYS{

	private $settings_page = 'layout'; /* which admin subpage (identified there by this->class_key) are we adding this to */

	private $section_key = 'gateway';/* must be unique */


	function __construct() {
		/**********************************************************
			[add settings to admin]
		***********************************************************/
		if(is_admin()){
			/* add admin options settings page*/
			add_filter('wppizza_filter_settings_sections_'.$this->settings_page.'', array($this, 'admin_options_settings'), 80, 5);
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
			$settings['sections'][$this->section_key] =  __('Gateways', 'wppizza-admin');
		}

		/*help*/
		if($help){
		}

		/*fields*/
		if($fields){

			$field = 'gateway_select_as_dropdown';
			$settings['fields'][$this->section_key][$field] = array( __('Gateway choices as dropdowns', 'wppizza-admin') , array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('Display Gateway choices as dropdowns instead of buttons. Only applicable if more than one gateway installed, activated and enabled', 'wppizza-admin'),
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

		if($field=='gateway_select_as_dropdown' ){
			print'<label>';
				echo "<input id='".$field."' name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' type='checkbox'  ". checked($wppizza_options['layout'][$field],true,false)." value='1' />";
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

		$options[$this->settings_page]['gateway_select_as_dropdown'] = false;

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
			
			$options[$this->settings_page]['gateway_select_as_dropdown'] = !empty($input[$this->settings_page]['gateway_select_as_dropdown']) ? true : false;							
		}

	return $options;
	}
}
/***************************************************************
*
*	[ini]
*
***************************************************************/
$WPPIZZA_MODULE_LAYOUT_GATEWAYS = new WPPIZZA_MODULE_LAYOUT_GATEWAYS();
?>