<?php
/**
* WPPIZZA_MODULE_LAYOUT_STYLE Class
*
* @package     WPPIZZA
* @subpackage  WPPIZZA_MODULE_LAYOUT_STYLE
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
class WPPIZZA_MODULE_LAYOUT_STYLE{

	private $settings_page = 'layout';/* which admin subpage (identified there by this->class_key) are we adding this to */

	private $section_key = 'style';/* must be unique */


	function __construct() {
		/**********************************************************
			[add settings to admin]
		***********************************************************/
		if(is_admin()){
			/* add admin options settings page*/
			add_filter('wppizza_filter_settings_sections_'.$this->settings_page.'', array($this, 'admin_options_settings'), 20, 5);
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
			$settings['sections'][$this->section_key] =  __('Style', 'wppizza-admin');
		}
		/*fields*/
		if($fields){

			$field = 'include_css';
			$settings['fields'][$this->section_key][$field] = array( __('Include CSS / Stylesheet', 'wppizza-admin') , array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('Include frontend css that came with this plugin (untick if you want to provide your own styles somewhere else)', 'wppizza-admin'),
				'description'=>array()
			));

			$field = 'style';
			$settings['fields'][$this->section_key][$field] = array( __('Which style to use (if enabled above)', 'wppizza-admin'), array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>'',
				'description'=>array()
			));

			$field = 'load_additional_styles';
			$settings['fields'][$this->section_key][$field] = array( __('Load additional styles', 'wppizza-admin') , array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>'',
				'description'=>array(__('If you are using more than just the one style selected above in your shortcodes, enable the additional stylesheets here', 'wppizza-admin'))
			));

			$field = 'css_priority';
			$settings['fields'][$this->section_key][$field] = array( __('Stylesheet Priority', 'wppizza-admin') , array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('By default, the stylesheet will be loaded AFTER the main theme stylesheet (which should have a priority of "10"). If you experience strange behaviour or layout issues (in conjunction with other plugins for example), you can try adjusting this priority here (the bigger the number, the later it gets loaded).', 'wppizza-admin'),
				'description'=>array()
			));
		}

		/*help*/
		if($help){
			$settings['help'][$this->section_key][] = array(
				'label'=>__('Style', 'wppizza-admin'),
				'description'=>array(
					__('Choose from one of the styles for your layout of menu items in the frontend. Make sure "Include CSS / Stylesheet" is enabled.', 'wppizza-admin'),
					__('If you choose a grid layout, some more layout options will become available.', 'wppizza-admin')
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

		if($field=='style'){
			print'<label>';
				echo "<select id='".WPPIZZA_SLUG."_".$options_key."_".$field."' name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' />";
					foreach(wppizza_public_styles($wppizza_options[$options_key][$field]) as $k=>$v){
						echo"<option value='".$v['id']."' ".$v['selected'].">".$v['value']."</option>";
					}
				echo "</select>";
				print'' . $label . '';
			print'</label>';
			print'' . $description . '';

			/**grid options**/
			$gridOptionsShow=($wppizza_options[$options_key][$field]=='grid') ? 'block' : 'none' ;

			echo"<div id='".WPPIZZA_SLUG."-".$field."-grid' style='display:".$gridOptionsShow."'>";
			echo "<input id='' name='".WPPIZZA_SLUG."[".$options_key."][style_grid_columns]' size='4' type='text' value='".$wppizza_options[$options_key]['style_grid_columns']."' />";
			echo" ".__('How many columns per row [minimum 1]', 'wppizza-admin')."";
			echo"<br />";
			echo "<input id='' name='".WPPIZZA_SLUG."[".$options_key."][style_grid_margins]' size='4' type='text' value='".$wppizza_options[$options_key]['style_grid_margins']."' />";
			echo" ".__('margins between columns [in %]', 'wppizza-admin')."";
			echo"<br />";
			echo "<input id='' name='".WPPIZZA_SLUG."[".$options_key."][style_grid_full_width]' size='4' type='text' value='".$wppizza_options[$options_key]['style_grid_full_width']."' />";
			echo" ".__('maximum browser width for layout to revert to 1 column per row [in px] - (for mobile / small screen devices)', 'wppizza-admin')."";
			echo"<br /><br />";
			echo" <span class='description' >".__('you will probably have to tweak the above to work with your theme and/or add some custom css. make sure to check things with different browsers', 'wppizza-admin')."</span>";
			echo"</div>";
		}

		if($field=='include_css'){
			print'<label>';
				print "<input id='".$field."' name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' type='checkbox'  ". checked($wppizza_options[$options_key][$field],true,false)." value='1' />";
				print'' . $label . '';
			print'</label>';
			print'' . $description . '';
		}
		if($field=='load_additional_styles'){
			print'<label>';
				foreach(wppizza_public_styles() as $style){
					print "<label><input id='".$field."".$style['id']."' name='".WPPIZZA_SLUG."[".$options_key."][".$field."][".$style['id']."]' type='checkbox'  ". checked(isset($wppizza_options[$options_key][$field][$style['id']]),true,false)." value='1' />".$style['value']."</label>";
				}
				print'' . $label . '';
			print'</label>';
			print'' . $description . '';
		}
		if($field=='css_priority'){
			print'<label>';
				print "<input name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' size='2' type='text'  value='".$wppizza_options[$options_key][$field]."' />";
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

		$options[$this->settings_page]['style'] = 'default';
		$options[$this->settings_page]['style_grid_columns'] = 3;
		$options[$this->settings_page]['style_grid_margins'] = 1.5;
		$options[$this->settings_page]['style_grid_full_width'] = 480;
		$options[$this->settings_page]['include_css'] = true;
		$options[$this->settings_page]['load_additional_styles'] = array();
		$options[$this->settings_page]['css_priority'] = 11;

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
			$options[$this->settings_page]['style'] = wppizza_validate_alpha_only($input[$this->settings_page]['style']);
			$options[$this->settings_page]['style_grid_columns'] = ((int)$input[$this->settings_page]['style_grid_columns']>0) ? (int)$input[$this->settings_page]['style_grid_columns'] : 1;
			$options[$this->settings_page]['style_grid_margins'] = (float)$input[$this->settings_page]['style_grid_margins'];
			$options[$this->settings_page]['style_grid_full_width'] = ((int)$input[$this->settings_page]['style_grid_full_width']>0) ? (int)$input[$this->settings_page]['style_grid_full_width'] : 480;
			$options[$this->settings_page]['include_css'] = !empty($input[$this->settings_page]['include_css']) ? true : false;
			$options[$this->settings_page]['load_additional_styles'] = !empty($input[$this->settings_page]['load_additional_styles']) ? wppizza_validate_array($input[$this->settings_page]['load_additional_styles']) : array();
			$options[$this->settings_page]['css_priority'] = wppizza_validate_int_only($input[$this->settings_page]['css_priority']);
		}

	return $options;
	}
}
/***************************************************************
*
*	[ini]
*
***************************************************************/
$WPPIZZA_MODULE_LAYOUT_STYLE = new WPPIZZA_MODULE_LAYOUT_STYLE();
?>