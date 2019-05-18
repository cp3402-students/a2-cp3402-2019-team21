<?php
/**
* WPPIZZA_MODULE_SETTINGS_MISCELLANEOUS Class
*
* @package     WPPIZZA
* @subpackage  WPPIZZA_MODULE_SETTINGS_MISCELLANEOUS
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
class WPPIZZA_MODULE_SETTINGS_MISCELLANEOUS{

	private $settings_page = 'settings';/* which admin subpage (identified there by this->class_key) are we adding this to */


	private $section_key = 'miscellaneous';/* must be unique */


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
		/**********************************************************
			[filter/actions depending on settings]
		***********************************************************/
		if(!is_admin()){
			/*dequeue if set**/
			add_action('wp_print_scripts', array( $this, 'dequeue_scripts'),100);
			/*force inclusion of all scripts and styles on all pages **/
			add_filter('wppizza_filter_force_scripts_and_styles', array( $this, 'force_scripts_and_styles'));
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
	/*************************************
	
		force inclusion of all scripts and styles on all pages 
	
	**************************************/
	function force_scripts_and_styles($bool){
		global $wppizza_options;
		$bool = !empty($wppizza_options[$this->settings_page]['always_load_all_scripts_and_styles']) ? true : false;
	return $bool;
	}
	/****************************************************************************************************************
	*
	*	[dequeue scripts on demand]	
	*
	****************************************************************************************************************/
	function dequeue_scripts(){
		global $wppizza_options;
				
		if(!empty($wppizza_options[$this->settings_page]['dequeue_scripts'])){
			/*dequeue main*/
			if($wppizza_options[$this->settings_page]['dequeue_scripts']=='all'){
				wp_dequeue_script(WPPIZZA_SLUG);
			}
			/*dequeue jquery validate too or only*/
			if($wppizza_options[$this->settings_page]['dequeue_scripts']=='all' || $wppizza_options[$this->settings_page]['dequeue_scripts']=='validation'){
				wp_dequeue_script(WPPIZZA_SLUG.'-validate');
				wp_dequeue_script(WPPIZZA_SLUG.'-validate-methods');				
			}
		}
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
			$settings['sections'][$this->section_key] =  __('Miscellaneous', 'wppizza-admin');
		}
		/*help*/
		if($help){
			$settings['help'][$this->section_key][] = array(
				'label'=>__('Miscellaneous Options', 'wppizza-admin'),
				'description'=>array(
					__('Please adjust settings as appropriate according to the information provided next to each individual option.', 'wppizza-admin')
				)
			);
		}
		/*fields*/
		if($fields){
			$field = 'always_load_all_scripts_and_styles';
			$settings['fields'][$this->section_key][$field] = array( __('Load css and js on all pages', 'wppizza-admin'), array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('load *all* css and javascripts on all pages. Might be necessary for themes that hijack normal pagelinks', 'wppizza-admin'),
				'description'=>array()
			));	
			$field = 'dequeue_scripts';
			$settings['fields'][$this->section_key][$field] = array( __('Dequeue wppizza scripts', 'wppizza-admin'), array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('If you are *certain* you do not require the main wppizza javascript and jquery validation and nothing else depends on them, or another plugin is already including jquery validation elsewhere, use the settings above as required. If you do not know, just leave it as is', 'wppizza-admin'),
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
		
		if($field=='always_load_all_scripts_and_styles'){
			echo "<label>";
				echo "<input id='".$field."' name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' type='checkbox'  ". checked($wppizza_options[$options_key][$field],true,false)." value='1' />";
				echo "" . $label . "";
			echo "</label>";
			echo"".$description."";
		}
		if($field=='dequeue_scripts'){
			echo "<label>";
				echo "<select name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' />";
				echo "<option value='' ".selected($wppizza_options[$options_key][$field],"",false).">".__('leave as is', 'wppizza-admin')."</option>";
				echo "<option value='all' ".selected($wppizza_options[$options_key][$field],"all",false).">".__('dequeue both, main wppizza and jquery validation', 'wppizza-admin')."</option>";
				echo "<option value='validation' ".selected($wppizza_options[$options_key][$field],"validation",false).">".__('dequeue jquery validation only', 'wppizza-admin')."</option>";
				echo "</select>";
				echo "" . $label . "";
			echo "</label> ";
			echo "" . $description . "";
			echo"<br /><span class='wppizza-highlight'>".__('NOTE: if you dequeue any scripts other plugins rely on, you WILL break things', 'wppizza-admin')."</span>";
		}
	}
	/*------------------------------------------------------------------------------
	#	[insert default option on install]
	#	$parameter $options array() | filter passing on filtered options
	#	@since 3.0
	#	@return array()
	------------------------------------------------------------------------------*/
	function options_default($options){

		$options[$this->settings_page]['always_load_all_scripts_and_styles'] = false;
		$options[$this->settings_page]['dequeue_scripts'] = '';		
				
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
			$options[$this->settings_page]['dequeue_scripts'] = wppizza_validate_alpha_only($input[$this->settings_page]['dequeue_scripts']);
			$options[$this->settings_page]['always_load_all_scripts_and_styles'] = !empty($input[$this->settings_page]['always_load_all_scripts_and_styles']) ? true : false;			
		}
	return $options;
	}
}
/***************************************************************
*
*	[ini]
*
***************************************************************/
$WPPIZZA_MODULE_SETTINGS_MISCELLANEOUS = new WPPIZZA_MODULE_SETTINGS_MISCELLANEOUS();
?>