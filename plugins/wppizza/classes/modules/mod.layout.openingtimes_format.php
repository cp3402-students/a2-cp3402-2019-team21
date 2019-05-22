<?php
/**
* WPPIZZA_MODULE_LAYOUT_OPENINGTIMES_FORMAT Class
*
* @package     WPPIZZA
* @subpackage  WPPIZZA_MODULE_LAYOUT_OPENINGTIMES_FORMAT
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
class WPPIZZA_MODULE_LAYOUT_OPENINGTIMES_FORMAT{

	private $settings_page = 'layout';/* which admin subpage (identified there by this->class_key) are we adding this to */

	private $section_key = 'opening_times_format';/* must be unique */


	function __construct() {
		/**********************************************************
			[add settings to admin]
		***********************************************************/
		if(is_admin()){
			/* add admin options settings page*/
			add_filter('wppizza_filter_settings_sections_'.$this->settings_page.'', array($this, 'admin_options_settings'), 30, 5);
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
			$settings['sections'][$this->section_key] =  __('Openingtimes Format', 'wppizza-admin');
		}
		/*fields*/
		if($fields){

			$field = 'hour';
			$settings['fields'][$this->section_key][$field] = array( __('Hours', 'wppizza-admin') , array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>'',
				'description'=>array()
			));

			$field = 'separator';
			$settings['fields'][$this->section_key][$field] = array( __('Separator', 'wppizza-admin'), array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>'',
				'description'=>array()
			));

			$field = 'minute';
			$settings['fields'][$this->section_key][$field] = array( __('Minutes', 'wppizza-admin') , array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>'',
				'description'=>array()
			));

			$field = 'ampm';
			$settings['fields'][$this->section_key][$field] = array( __('Show AM/PM ?', 'wppizza-admin') , array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>'',
				'description'=>array()
			));
			
			$field = 'dont_group_days';
			$settings['fields'][$this->section_key][$field] = array( __('Do not group days with equal times', 'wppizza-admin') , array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>'',
				'description'=>array()
			));			
			
		}

		/*help*/
		if($help){
			$settings['help'][$this->section_key][] = array(
				'label'=>__('Openingtimes', 'wppizza-admin'),
				'description'=>array(
					__('Openingtimes format will apply to the openingtimes displayed with a widget or shortcode', 'wppizza-admin')
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

		if($field=='hour'){
			print'<label>';
				echo "<select name='".WPPIZZA_SLUG."[".$this->section_key."][".$field."]' />";
						echo"<option value='G' ".selected($wppizza_options[$this->section_key][$field],"G",false).">".__('24-hour format without leading zeros', 'wppizza-admin')."</option>";
						echo"<option value='g' ".selected($wppizza_options[$this->section_key][$field],"g",false).">".__('12-hour format without leading zeros', 'wppizza-admin')."</option>";
						echo"<option value='H' ".selected($wppizza_options[$this->section_key][$field],"H",false).">".__('24-hour format with leading zeros', 'wppizza-admin')."</option>";
						echo"<option value='h' ".selected($wppizza_options[$this->section_key][$field],"h",false).">".__('12-hour format with leading zeros', 'wppizza-admin')."</option>";
				echo "</select>";
				print'' . $label . '';
			print'</label>';
			print'' . $description . '';
		}
		if($field=='separator'){
			print'<label>';
				echo "<select name='".WPPIZZA_SLUG."[".$this->section_key."][".$field."]' />";
						echo"<option value='' ".selected($wppizza_options[$this->section_key][$field],"",false).">".__('No separator', 'wppizza-admin')."</option>";
						echo"<option value='&nbsp;' ".selected($wppizza_options[$this->section_key][$field],"&nbsp;",false).">".__('Space', 'wppizza-admin')."</option>";
						echo"<option value=':' ".selected($wppizza_options[$this->section_key][$field],":",false).">:</option>";
						echo"<option value='.' ".selected($wppizza_options[$this->section_key][$field],".",false).">.</option>";
						echo"<option value='-' ".selected($wppizza_options[$this->section_key][$field],"-",false).">-</option>";
						echo"<option value=';' ".selected($wppizza_options[$this->section_key][$field],";",false).">;</option>";
				echo "</select>";
				print'' . $label . '';
			print'</label>';
			print'' . $description . '';
		}
		if($field=='minute'){
			print'<label>';
				echo "<select name='".WPPIZZA_SLUG."[".$this->section_key."][".$field."]' />";
						echo"<option value='' ".selected($wppizza_options[$this->section_key][$field],"",false).">".__('Hide minutes', 'wppizza-admin')."</option>";
						echo"<option value='i' ".selected($wppizza_options[$this->section_key][$field],"i",false).">".__('Show minutes', 'wppizza-admin')."</option>";
				echo "</select>";
				print'' . $label . '';
			print'</label>';
			print'' . $description . '';
		}
		if($field=='ampm'){
			print'<label>';
				echo "<select name='".WPPIZZA_SLUG."[".$this->section_key."][".$field."]' />";
						echo"<option value='' ".selected($wppizza_options[$this->section_key][$field],"",false).">".__('Do not show', 'wppizza-admin')."</option>";
						echo"<option value='a' ".selected($wppizza_options[$this->section_key][$field],"a",false).">".__('lowercase', 'wppizza-admin')."</option>";
						echo"<option value='A' ".selected($wppizza_options[$this->section_key][$field],"A",false).">".__('UPPERCASE', 'wppizza-admin')."</option>";
						echo"<option value=' a' ".selected($wppizza_options[$this->section_key][$field]," a",false).">".__('lowercase (with leading space)', 'wppizza-admin')."</option>";
						echo"<option value=' A' ".selected($wppizza_options[$this->section_key][$field]," A",false).">".__('UPPERCASE (width leading space)', 'wppizza-admin')."</option>";
				echo "</select>";
				print'' . $label . '';
			print'</label>';
			print'' . $description . '';
		}	
		if($field=='dont_group_days'){
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
		
		$options[$this->section_key]['hour']='G';
		$options[$this->section_key]['separator']=':';
		$options[$this->section_key]['minute']='i';
		$options[$this->section_key]['ampm']='';
		$options[$this->section_key]['dont_group_days']=false;

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
			$options[$this->section_key]['hour']=wppizza_validate_string($input[$this->section_key]['hour']);
			$options[$this->section_key]['separator']=wppizza_validate_string($input[$this->section_key]['separator']);
			$options[$this->section_key]['minute']=wppizza_validate_string($input[$this->section_key]['minute']);
			$options[$this->section_key]['ampm']=wppizza_validate_string($input[$this->section_key]['ampm']);
			$options[$this->section_key]['dont_group_days'] = !empty($input[$this->section_key]['dont_group_days']) ? true : false;
		}

	return $options;
	}
}
/***************************************************************
*
*	[ini]
*
***************************************************************/
$WPPIZZA_MODULE_LAYOUT_OPENINGTIMES_FORMAT = new WPPIZZA_MODULE_LAYOUT_OPENINGTIMES_FORMAT();
?>