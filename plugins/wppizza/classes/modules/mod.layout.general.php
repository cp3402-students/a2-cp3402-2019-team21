<?php
/**
* WPPIZZA_MODULE_LAYOUT_GENERAL Class
*
* @package     WPPIZZA
* @subpackage  WPPIZZA_MODULE_LAYOUT_GENERAL
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
class WPPIZZA_MODULE_LAYOUT_GENERAL{

	private $settings_page = 'layout';/* which admin subpage (identified there by this->class_key) are we adding this to */

	private $section_key = 'general';/* must be unique */


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
			$settings['sections'][$this->section_key] =  __('General', 'wppizza-admin');
		}
		/*fields*/
		if($fields){
			$field = 'items_per_loop';
			$settings['fields'][$this->section_key][$field] = array( __('Menu Items per page', 'wppizza-admin') , array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=> __('how many menu items per category page (displays pagination, if there are more menu items for the selected category)[options: -1=all, >1=items per page]', 'wppizza-admin'),
				'description'=>array(
					'<span class="wppizza-highlight">'.__('if not set to -1, it must be >= wordpress settings->reading->Blog pages show at most', 'wppizza-admin').'</span>'
				)
			));
			$field = 'apply_menu_items_content_filter';
			$settings['fields'][$this->section_key][$field] = array( __('Shortodes in menu items content', 'wppizza-admin') , array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=> __('Allow shortcodes / content filters to be processed in menu item content fields', 'wppizza-admin'),
				'description'=>array(
					__('This will also change the wrapping "p" element to a "div" instead ', 'wppizza-admin')
				)
			));			
			$field = 'disable_online_order';
			$settings['fields'][$this->section_key][$field] = array( __('Completely disable online orders', 'wppizza-admin'), array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>'<span class="wppizza-highlight">'.__('this will still display prices (unless set to be hidden), but will disable shoppingcart and orderpage', 'wppizza-admin').'</span>',
				'description'=>array(
					__('Useful if you want to display your menu and prices but without offering online orders.', 'wppizza-admin')
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

		if($field=='items_per_loop'){
			print'<label>';
				print "<input name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' size='2' type='text'  value='".$wppizza_options[$options_key][$field]."' />";
				print'' . $label . '';
			print'</label>';
			print'' . $description . '';
		}

		if($field=='disable_online_order'){
			print'<label>';
				echo "<input id='".$field."' name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' type='checkbox'  ". checked($wppizza_options[$options_key][$field],true,false)." value='1' />";
				print'' . $label . '';
			print'</label>';
			print'' . $description . '';
		}
		
		if($field=='apply_menu_items_content_filter'){
			print'<label>';
				echo "<input id='".$field."' name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' type='checkbox'  ". checked($wppizza_options[$options_key][$field],true,false)." value='1' />";
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
		
		$options[$this->settings_page]['items_per_loop'] = '-1';
		$options[$this->settings_page]['disable_online_order'] = false;
		$options[$this->settings_page]['apply_menu_items_content_filter'] = false;
						
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

			/*set number of items per loop. must be >= get_option('posts_per_page ')*/
			/*if minus=>set to -1**/
			if(substr($input[$this->settings_page]['items_per_loop'],0,1)=='-'){
				$set='-1';
			}else{/*else mk int**/
				
				$ppp = get_option('posts_per_page ');
					
				if((int)$input[$this->settings_page]['items_per_loop'] >= $ppp){
					$set= (int)$input[$this->settings_page]['items_per_loop'];
				}else{
					$set= $ppp;
				}
			}
			$options[$this->settings_page]['items_per_loop'] = $set;


			$options[$this->settings_page]['disable_online_order'] = !empty($input[$this->settings_page]['disable_online_order']) ? true : false;
			
			$options[$this->settings_page]['apply_menu_items_content_filter'] = !empty($input[$this->settings_page]['apply_menu_items_content_filter']) ? true : false;
			
		}
		
	return $options;
	}
}
/***************************************************************
*
*	[ini]
*
***************************************************************/
$WPPIZZA_MODULE_LAYOUT_GENERAL = new WPPIZZA_MODULE_LAYOUT_GENERAL();
?>