<?php
/**
* WPPIZZA_MODULE_MEAL_SIZES_SIZES Class
*
* @package     WPPIZZA
* @subpackage  WPPIZZA_MODULE_MEAL_SIZES_SIZES
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
class WPPIZZA_MODULE_MEAL_SIZES_SIZES{

	private $settings_page = 'meal_sizes';/* which admin subpage (identified there by this->class_key) are we adding this to */


	private $section_key = 'sizes';/* must be unique */


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
			/**metaboxes sizes/prices - priority same as submenu page **/
			add_filter('wppizza_filter_admin_metaboxes', array( $this, 'wppizza_filter_admin_add_metaboxes'), 50, 4);
			add_filter('wppizza_filter_admin_save_metaboxes',array( $this, 'wppizza_filter_admin_save_metaboxes'), 10, 3);
			/** admin ajax **/
			add_action('wppizza_ajax_admin_'.$this->settings_page.'', array( $this, 'admin_ajax'));

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


	/*******************************************************************************************************************************************************
	*
	* 	[admin ajax]
	*
	********************************************************************************************************************************************************/
	function admin_ajax($wppizza_options){

		/*****************************************************
			[adding new size selection options]
		*****************************************************/
		if($_POST['vars']['field']=='sizes' && isset($_POST['vars']['allKeys']) && isset($_POST['vars']['newFields']) && $_POST['vars']['newFields']>0){

			/**get next highest key available**/
			$nextKey=0;
			if(isset($_POST['vars']['allKeys']) && is_array($_POST['vars']['allKeys'])){
				$currentKeys=array();
				foreach($_POST['vars']['allKeys'] as $key_exists){
					$currentKeys[$key_exists['value']]=$key_exists['value'];
				}
				$highestKey=max($currentKeys);
				$nextKey=$highestKey+1;
			}

			/** cretae some (albeit empty) default values */
			$no_of_sizes = (int)$_POST['vars']['newFields'];
			$default_values = array();
			for($i=0; $i<$no_of_sizes; $i++){
				$default_values[$i]['lbl'] = '';
				$default_values[$i]['price'] = '';
			}


			$output=$this->wppizza_admin_section_sizes($_POST['vars']['field'], $nextKey, $default_values);
			print"".$output."";
			exit();
		}
	}


	/*********************************************************
	*
	*	[add metaboxes]
	*	@since 3.0
	*
	*********************************************************/
	function wppizza_filter_admin_add_metaboxes($wppizza_meta_box, $meta_values, $meal_sizes, $wppizza_options){

		/****  meal sizes with prices  ***/
		$wppizza_meta_box[$this->section_key]='';
		$wppizza_meta_box[$this->section_key].="<div class='".WPPIZZA_SLUG."_option_meta'>";

			$wppizza_meta_box[$this->section_key].="<label class='".WPPIZZA_SLUG."-meta-label'>".__('Size(s) and Price(s)', 'wppizza-admin')." (".$wppizza_options['order_settings']['currency_symbol']."): </label>";

			$wppizza_meta_box[$this->section_key].="<select name='".WPPIZZA_SLUG."[".$this->section_key."]' class='wppizza_pricetier_select wppizza_pricetier_select_meta'>";

			if(!isset($meta_values[$this->section_key])){
				$wppizza_meta_box[$this->section_key].="<option value=''>".esc_html__('--- Please Select ---', 'wppizza_admin')." ".$meta_values[$this->section_key]."</option>";
			}
			foreach($meal_sizes as $l=>$m){
				if(isset($meta_values[$this->section_key]) && $l == $meta_values[$this->section_key]){
					$sel=" selected='selected'";
				}else{
					$sel='';
				}

				$ident=!empty($wppizza_options[$this->section_key][$l][0]['lbladmin']) && $wppizza_options[$this->section_key][$l][0]['lbladmin']!='' ? $wppizza_options[$this->section_key][$l][0]['lbladmin'] :'ID:'.$l.'';

				$wppizza_meta_box[$this->section_key].="<option value='".$l."'".$sel.">".implode(", ",$m['lbl'])." [".$ident."]</option>";
			}
			$wppizza_meta_box[$this->section_key].="</select>";

			$wppizza_meta_box[$this->section_key].="<span class='".WPPIZZA_SLUG."_pricetiers'>";
				if(!empty($meta_values['prices'])){
				foreach($meta_values['prices'] as $k=>$v){
					$wppizza_meta_box[$this->section_key].="<input name='".WPPIZZA_SLUG."[prices][]' size='5' type='text' value='".wppizza_output_format_price($v)."' />";
				}}
			$wppizza_meta_box[$this->section_key].="</span>";

		$wppizza_meta_box[$this->section_key].="</div>";

		return $wppizza_meta_box;
	}
	/*********************************************************
	*
	*	[save metaboxes values]
	*	@since 3.0
	*
	*********************************************************/
	function wppizza_filter_admin_save_metaboxes($itemMeta, $item_id,  $wppizza_options){

    	/**set some default values (namely sizes and prices) when adding new page**/
    	if(!isset($_POST[WPPIZZA_SLUG]['sizes'])){
			$optionsSizes = wppizza_sizes_available();
			/**get no of price input fields of first available size option**/
			reset($optionsSizes);
			$first_key = key($optionsSizes);
			$_POST[WPPIZZA_SLUG]['sizes']=$first_key;
			if(isset($optionsSizes[$first_key]['price'])){
			$_POST[WPPIZZA_SLUG]['prices']=$optionsSizes[$first_key]['price'];
			}
    	}

		//**sizes**//
		$itemMeta['sizes']							= (int)$_POST[WPPIZZA_SLUG]['sizes'];

    	//**prices**//
    	$itemMeta['prices']=array();
    	if(isset($_POST[WPPIZZA_SLUG]['prices'])){
    	foreach($_POST[WPPIZZA_SLUG]['prices'] as $k=>$v){
    		$itemMeta['prices'][$k]					= wppizza_validate_float_only($_POST[WPPIZZA_SLUG]['prices'][$k],2);
    	}}

		return $itemMeta;
	}
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
			$settings['sections'][$this->section_key] =  __('Size Options Available', 'wppizza-admin');
		}
		/*help*/
		if($help){
			$settings['help'][$this->section_key][] = array(
				'label'=>__('Set Size Options', 'wppizza-admin'),
				'description'=>array(
					__('Define a selection of sizes that you want to make available for selection on a per item basis.', 'wppizza-admin'),
					'<br>',
					'<b>'.__('Example - adding a new size option:', 'wppizza-admin').'</b>',
					__('To add a new option for a menu item of "small, medium, large" enter "3" in the input formfield next to "how many size option fields" and click "add".', 'wppizza-admin'),
					__('In the resulting available options enter:', 'wppizza-admin'),
					__('- the "Admin Screen Label" (optional, only for your own identification purposes)', 'wppizza-admin'),
					__('- the "Label [Frontend]" (in this case this would be "small", "medium" and "large")', 'wppizza-admin'),
					__('- some default price per size (this can always be overwritten on a per item basis)', 'wppizza-admin'),
					'<br>',
					__('As meals and beverages can come in different sizes, please add/edit the options you want to offer your customers. You will then be able to offer these options on a per item basis:', 'wppizza-admin'),
					'<span style="color:red">'.__('For your own sanity and easier managability now and in the future, I would also suggest to define separate, distinct options for different types of dishes *even if they have the same sizes/labels*. (use the "Admin Screen Label" for easier identification)', 'wppizza-admin').'</span>'
				)
			);
		}
		/*fields*/
		if($fields){
			$field = $this->section_key;
			$settings['fields'][$this->section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=> '',
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

		if($field==$this->section_key){

			echo"<div id='wppizza_".$field."_options' class='wppizza_admin_options'>";/*new sizes will be appended to this div's id by ajax*/

			if(!empty($wppizza_options[$field])){
				/* get sizes that are in use */
				$sizes_in_use = wppizza_options_in_use($field);
				foreach($wppizza_options[$field] as $key=>$values){
					echo"".$this->wppizza_admin_section_sizes($field, $key, $values, $sizes_in_use[$field]);
				}
			}

			echo"</div>";
			/** add new button **/
			echo"<div id='wppizza-".$field."-add' class='wppizza_admin_add'>";
				echo "<input type='button' id='wppizza_add_".$field."' class='button' value='".__('add', 'wppizza-admin')."' />";
				echo "<input id='wppizza_add_".$field."_fields' size='1' type='text' value='1' />".__('how many size option fields ?', 'wppizza-admin')."";
			echo"</div>";
		}
	}


	/**
		[available sizes of meal items or add new via ajax]
	**/
	private function wppizza_admin_section_sizes($field, $key, $values=null, $sizes_in_use=null){

		$str='';

		$str.="<div class='wppizza_option wppizza_".$this->section_key."_option'>";
			$str.="<ul>";

				$str.="<li>";
					/*for easy checking for existing keys when adding new**/
					$str.="<input id='wppizza_".$field."_".$key."' class='wppizza-getkey' name='wppizza-getkey[".$key."]' type='hidden' value='".$key."'>";
					$str.="<span class='wppizza_label'>ID: ".$key."</span>";
				$str.="</li>";

				$str.="<li>";
					$str.="<span class='wppizza_label'>".__('Admin Screen Label', 'wppizza-admin').":</span>";
					$val=!empty($values[0]['lbladmin']) ? $values[0]['lbladmin'] : '';
					$str.="<input name='".WPPIZZA_SLUG."[".$field."][".$key."][0][lbladmin]' size='10' type='text' value='". $val ."' /><span class='description'> ".__('optional, use to identify groups with the same frontend labels. ', 'wppizza-admin')."</span>";
				$str.="</li>";


				$str.="<li>";
					$str.="<span class='wppizza_label'>".__('Label [Frontend]', 'wppizza-admin').":</span>";
					foreach($values as $c=>$obj){
						$str.="<input name='".WPPIZZA_SLUG."[".$field."][".$key."][".$c."][lbl]' size='10' type='text' value='".$obj['lbl']."' />";
					}
				$str.="</li>";

				$str.="<li>";
					$str.="<span class='".WPPIZZA_SLUG."_label'>".__('Default Prices', 'wppizza-admin').":</span>";

					foreach($values as $c=>$obj){
						$str.="<input name='".WPPIZZA_SLUG."[".$field."][".$key."][".$c."][price]' size='10' type='text' value='".$obj['price']."' />";
					}

					if(!isset($sizes_in_use[$key])){
						$str.="<a href='#' class='".WPPIZZA_SLUG."-delete ".$field." ".WPPIZZA_SLUG."-dashicons dashicons-trash' title='".__('delete', 'wppizza-admin')."'></a>";
					}else{
						$str.="".__('in use', 'wppizza-admin')."";
					}
				$str.="</li>";
			$str.="</ul>";
		$str.="</div>";

	return $str;
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
			note: dummy function
			default sizes are automatically creaeted  on install
			provided
			!defined('WPPIZZA_NO_DEFAULTS')
			OR
			!defined('WPPIZZA_NO_DEFAULT_ITEMS')
		*/
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

			$options[$this->section_key] = array();//initialize as empty array
			if(!empty($input[$this->section_key])){
			foreach($input[$this->section_key] as $key=>$values){
				foreach($values as $count=>$v){
					if($count==0){
					$options[$this->section_key][$key][$count]['lbladmin']=wppizza_validate_string($v['lbladmin']);
					}
					$options[$this->section_key][$key][$count]['lbl']=wppizza_validate_string($v['lbl']);
					$options[$this->section_key][$key][$count]['price']=wppizza_validate_float_only($v['price'],2);
				}
			}}

			/*
				in case someone does something really daft (editing post pages and sizes at the same time in 2 different windows)....
				make sure we do not delete something that really should be there.
				though there would still be a chance that a particular menu item has different sizes than before now, but at least there's somthing
				and it really would be a user error
			*/
			global $wppizza_options;
			$sizes_in_use = wppizza_options_in_use($this->section_key);
			if(!empty($sizes_in_use[$this->section_key])){
				foreach($sizes_in_use[$this->section_key] as $size_key){
					if(!isset($options[$this->section_key][$size_key])){
						$options[$this->section_key][$size_key] = $wppizza_options[$this->section_key][$size_key];
					}
				}
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
$WPPIZZA_MODULE_MEAL_SIZES_SIZES = new WPPIZZA_MODULE_MEAL_SIZES_SIZES();
?>