<?php
/**
* WPPIZZA_MODULE_LAYOUT_CUSTOM_CSS Class
*
* @package     WPPIZZA
* @subpackage  WPPIZZA_MODULE_LAYOUT_CUSTOM_CSS
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
class WPPIZZA_MODULE_LAYOUT_CUSTOM_CSS{

	private $settings_page = 'layout';/* which admin subpage (identified there by this->class_key) are we adding this to */
	private $section_key = 'custom_css';/* must be unique */
	private $module_priority = 21;/* display order (priority) of settings in subpage */

	function __construct() {
		/**********************************************************
			[add settings to admin]
		***********************************************************/
		if(is_admin()){
			/* add admin options settings page*/
			add_filter('wppizza_filter_settings_sections_'.$this->settings_page.'', array($this, 'admin_options_settings'), $this->module_priority, 5);
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
			$settings['sections'][$this->section_key] =  __('Custom CSS', 'wppizza-admin');
		}
		/*fields*/
		if($fields){

			$field = 'custom_css';
			$settings['fields'][$this->section_key][$field] = array( __('Enter your Custom CSS', 'wppizza-admin') , array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>'',
				'description'=>array()
			));
		}

		/*help*/
		if($help){
			$settings['help'][$this->section_key][] = array(
				'label'=>__('Custom CSS', 'wppizza-admin'),
				'description'=>array(
					__('Enter any custom css declaration.  The created css file (or added &#60;style&#62; declarations if creation of the file fails due to server restrictions) will be loaded after any/all other wppizza css or omitted if empty', 'wppizza-admin'),
				)
			);
		}


		return $settings;
	}
	/*------------------------------------------------------------------------------
	#	[output option fields - setting page]
	#	@since 3.0
	#	@return array()
	------------------------------------------------------------------------------*/
	function admin_options_fields_settings($wppizza_options, $options_key, $field, $label, $description){


		if($field=='custom_css'){
			$custom_css = get_option(WPPIZZA_SLUG.'_custom_css','');
			print'<label>';
				print'<div>' . $label . '</div>';			
				print "<textarea id='".$field."' rows='10' cols='75' name='".WPPIZZA_SLUG."[".$options_key."][".$field."]'>". esc_textarea($custom_css)."</textarea>";
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

		/*
			current set custom css settings - if any 
		*/
		$current_custom_css = get_option(WPPIZZA_SLUG.'_custom_css', false);
		
		/*
			custom css option
		*/
		$user_css = empty($current_custom_css) ? '' : $current_custom_css ;
		$update_user_css = update_option(WPPIZZA_SLUG.'_custom_css', $user_css, false);
		
		/*
			last update and loading type
		*/
		$options[$this->settings_page]['custom_css_version'] = time();
		$options[$this->settings_page]['custom_css_type'] = 'none';
		
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
			

			$custom_css_file_path = WPPIZZA_PATH.'css/'.WPPIZZA_PREFIX.'.style.css';
			$saved_css = get_option(WPPIZZA_SLUG.'_custom_css', '');
			$update_css = trim(str_replace('}','}'.PHP_EOL,sanitize_text_field($input[$this->settings_page]['custom_css'])));
	
			
			/** only if old css != new css  */
			if($saved_css != $update_css){
		
				/** css not empty */
				if(!empty($update_css)){
					@file_put_contents($custom_css_file_path, $update_css);	
				}
				/** css set is empty and file exists - unlink file */
				if(empty($update_css)){
					@unlink($custom_css_file_path);	
				}
				/*
					set css type [none|file|inline]
				*/
				/** css set is empty == none,  css set is NOT empty check if file exists 'file' else 'inline' **/
				$custom_css_type = (empty($update_css)) ? 'none' : ((file_exists($custom_css_file_path)) ? 'file' : 'inline');

				/* update custom css option */
				
				$update_user_css = update_option(WPPIZZA_SLUG.'_custom_css', $update_css, false);

				/*
					last update and loading type
				*/
				$options[$this->settings_page]['custom_css_version'] = time();
				$options[$this->settings_page]['custom_css_type'] = $custom_css_type;
			}						
		}

	return $options;
	}
}
/***************************************************************
*
*	[ini]
*
***************************************************************/
$WPPIZZA_MODULE_LAYOUT_CUSTOM_CSS = new WPPIZZA_MODULE_LAYOUT_CUSTOM_CSS();
?>