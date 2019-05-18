<?php
/**
* WPPIZZA_MODULE_ORDERSETTINGS_DISCOUNTS Class
*
* @package     WPPIZZA
* @subpackage  WPPIZZA_MODULE_ORDERSETTINGS_DISCOUNTS
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
class WPPIZZA_MODULE_ORDERSETTINGS_DISCOUNTS{

	private $settings_page = 'order_settings';/* which admin subpage (identified there by this->class_key) are we adding this to */

	private $section_key = 'discounts';/* must be unique */


	function __construct() {
		/**********************************************************
			[add settings to admin]
		***********************************************************/
		if(is_admin()){
			/* add admin options settings page*/
			add_filter('wppizza_filter_settings_sections_'.$this->settings_page.'', array($this, 'admin_options_settings'), 50, 5);
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
			$settings['sections'][$this->section_key] = __('Discounts', 'wppizza-admin');
		}
		/*help*/
		if($help){
			$settings['help'][$this->section_key][] = array(
				'label'=>__('Discounts', 'wppizza-admin'),
				'description'=>array(
					__('Please adjust settings as appropriate according to the information provided next to each individual option.', 'wppizza-admin')
				)
			);
		}
		/*fields*/
		if($fields){
			$field = 'discounts';
			$settings['fields'][$this->section_key][$field] = array( __('Discount Settings', 'wppizza-admin'), array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>'',
				'description'=>array()
			));					
			
			$field = 'discount_calculation_exclude_item';
			$settings['fields'][$this->section_key][$field] = array( __('Exclude menu items from discount calculation', 'wppizza-admin'), array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('<b>Exclude</b> following menu items when calculating discounts:', 'wppizza-admin'),
				'description'=>array()
			));	
			
			$field = 'discount_calculation_exclude_cat';
			$settings['fields'][$this->section_key][$field] = array( __('Exclude categories from discount calculation', 'wppizza-admin'), array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('<b>Exclude</b> all menu items belonging to following categories when calculating discounts:', 'wppizza-admin'),
				'description'=>array()
			));	
			
			$field = 'discount_calculate_delivery_before_discount';
			$settings['fields'][$this->section_key][$field] = array( __('Exclude discounts for delivery charges calculation (if applicable)', 'wppizza-admin'), array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('Enable to calculate delivery charges before taking any discounts into account', 'wppizza-admin'),
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
	
		if($field=='discounts'){
			
			echo "<table id='wppizza_admin_table_".$field."' class='wppizza_admin_table'>";
			
				foreach($wppizza_options[$options_key][$field] as $k=>$v){
				echo "<tr>";
					echo "<td>";
						echo "<input name='".WPPIZZA_SLUG."[".$options_key."][discount_selected]' type='radio' ". checked($wppizza_options[$options_key]['discount_selected']==$k,true,false)." value='".$k."' />";
						if($k=='none'){
							echo"".__('No Discounts', 'wppizza-admin')."";
						}
						if($k=='percentage'){
							echo"".__('Percentage Discount', 'wppizza-admin').":";
							echo"<br />";
							foreach($v['discounts'] as $l=>$m){
								echo"".__('If order total >=', 'wppizza-admin').":";
								echo"<input name='".WPPIZZA_SLUG."[".$options_key."][".$field."][".$k."][".$field."][".$l."][min_total]' size='3' type='text' value='".wppizza_output_format_price($wppizza_options[$options_key][$field][$k]['discounts'][$l]['min_total'])."' />";
								echo"".__('discount', 'wppizza-admin').":";
								echo"<input name='".WPPIZZA_SLUG."[".$options_key."][".$field."][".$k."][".$field."][".$l."][discount]' size='5' type='text' value='".wppizza_output_format_percent($wppizza_options[$options_key][$field][$k]['discounts'][$l]['discount'])."' />";
								echo"".__('percent', 'wppizza-admin')."";
								echo"<br />";
							}
						}
						if($k=='standard'){
							echo"".__('Standard Discount [money off]', 'wppizza-admin').":";
							echo"<br />";
							foreach($v['discounts'] as $l=>$m){
								echo"".__('If order total >=', 'wppizza-admin').":";
								echo"<input name='".WPPIZZA_SLUG."[".$options_key."][".$field."][".$k."][".$field."][".$l."][min_total]' size='3' type='text' value='".wppizza_output_format_price($wppizza_options[$options_key][$field][$k]['discounts'][$l]['min_total'])."' />";
								echo"".__('get', 'wppizza-admin').":";
								echo"<input name='".WPPIZZA_SLUG."[".$options_key."][".$field."][".$k."][".$field."][".$l."][discount]' size='3' type='text' value='".wppizza_output_format_price($wppizza_options[$options_key][$field][$k]['discounts'][$l]['discount'])."' />";
								echo"".__('off', 'wppizza-admin')."";
								echo"<br />";								
							}
						}
					echo "</td>";
				echo "</tr>";
				}
		
			echo "</table>";		
		
		}

		if($field=='discount_calculation_exclude_item'){
			/**Exclude following menu items when calculating  discounts**/
			echo "<label>";
				echo "".$label."<br />";
				echo"<select name='".WPPIZZA_SLUG."[".$options_key."][".$field."][]' multiple='multiple' data-placeholder='".__('N/A', 'wppizza-admin')."' class='wppizza_".$field."'>";
				foreach(wppizza_get_menu_items() as $pKey=>$pVal){
					echo"<option value='".$pVal->ID."' ";
						if(isset($wppizza_options[$options_key][$field]) && in_array($pVal->ID,$wppizza_options[$options_key][$field])){
							echo" selected='selected'";
						}
					echo">".$pVal->post_title."</option>";
				}
				echo"</select>";
			echo "</label>";			
			echo"".$description."";	
		}
		if($field=='discount_calculation_exclude_cat'){
			/**Exclude following categories  calculating  discounts**/
			if(is_array(wppizza_get_categories())){
				echo "<label>";
					echo "".$label."<br />";
					echo"<select name='".WPPIZZA_SLUG."[".$options_key."][".$field."][]' multiple='multiple'  data-placeholder='".__('N/A', 'wppizza-admin')."' class='wppizza_".$field."'>";
					foreach(wppizza_get_categories() as $cKey=>$cVal){
						echo"<option value='".$cVal->term_id."' ";
							if(isset($wppizza_options[$options_key][$field]) && isset($wppizza_options[$options_key][$field][$cVal->term_id])){
								echo" selected='selected'";
							}
						echo">".$cVal->name."</option>";
					}
					echo"</select>";
				echo "</label>";			
				echo"".$description."";					
			}
		}
				
		if($field=='discount_calculate_delivery_before_discount'){
			echo "<label>";
				echo"<input name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' type='checkbox'  ". checked($wppizza_options[$options_key][$field],true,false)." value='1' />";
				echo "".$label."";
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

		$options[$this->settings_page]['discount_selected'] = 'none';
		$options[$this->settings_page]['discounts'] = array(
			'none'=>array(),
			'percentage'=>array(
				'discounts'=>array(
					0=>array('min_total'=>'20','discount'=>'5'),
					1=>array('min_total'=>'50','discount'=>'10')
				)
			),
			'standard'=>array(
				'discounts'=>array(
					0=>array('min_total'=>'20','discount'=>'5'),
					1=>array('min_total'=>'50','discount'=>'10')
				)
			)
		);
		$options[$this->settings_page]['discount_calculation_exclude_item'] = array();
		$options[$this->settings_page]['discount_calculation_exclude_cat'] = array();
		$options[$this->settings_page]['discount_calculate_delivery_before_discount'] = false;
		
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
		/* 
			settings 
		*/
		if(isset($_POST[''.WPPIZZA_SLUG.'_'.$this->settings_page.''])){
		
			$options[$this->settings_page]['discount_selected'] = wppizza_validate_alpha_only($input[$this->settings_page]['discount_selected']);
			$options[$this->settings_page]['discounts'] = array();//initialize array
			$options[$this->settings_page]['discounts']['none'] = array();//add distinctly as it has no array associated with it			
			foreach($input[$this->settings_page]['discounts'] as $a=>$b){
				foreach($b as $c=>$d){
					foreach($d as $e=>$f){
						foreach($f as $g=>$h){
							if($a=='percentage' && $g=='discount'){
								$options[$this->settings_page]['discounts'][$a][$c][$e][$g]=wppizza_validate_float_pc($h);
							}else{
								$options[$this->settings_page]['discounts'][$a][$c][$e][$g]=wppizza_validate_float_only($h,2);
							}
						}
					}
				}
			}
			$options[$this->settings_page]['discount_calculation_exclude_item'] = !empty($input[$this->settings_page]['discount_calculation_exclude_item']) ? array_combine($input[$this->settings_page]['discount_calculation_exclude_item'],$input[$this->settings_page]['discount_calculation_exclude_item']) : array();/*makes keys == values*/
			$options[$this->settings_page]['discount_calculation_exclude_cat'] = !empty($input[$this->settings_page]['discount_calculation_exclude_cat']) ? array_combine($input[$this->settings_page]['discount_calculation_exclude_cat'],$input[$this->settings_page]['discount_calculation_exclude_cat']) : array();/*makes keys == values*/			
			$options[$this->settings_page]['discount_calculate_delivery_before_discount']=!empty($input[$this->settings_page]['discount_calculate_delivery_before_discount']) ? true : false;
		}
				
	return $options;
	}
}
/***************************************************************
*
*	[ini]
*
***************************************************************/
$WPPIZZA_MODULE_ORDERSETTINGS_DISCOUNTS = new WPPIZZA_MODULE_ORDERSETTINGS_DISCOUNTS();
?>