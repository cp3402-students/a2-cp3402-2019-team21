<?php
/**
* WPPIZZA_MODULE_ADDITIVES Class
*
* @package     WPPIZZA
* @subpackage  WPPIZZA_MODULE_ADDITIVES
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
class WPPIZZA_MODULE_ADDITIVES{

	private $settings_page = 'additives';/* which admin subpage (identified there by this->class_key) are we adding this to */


	private $section_key = 'additives';/* must be unique */


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
			add_filter('wppizza_filter_admin_metaboxes', array( $this, 'wppizza_filter_admin_add_metaboxes'), 60, 4);
			add_filter('wppizza_filter_admin_save_metaboxes',array( $this, 'wppizza_filter_admin_save_metaboxes'), 10, 3);
			/** admin ajax **/
			add_action('wppizza_ajax_admin_'.$this->settings_page.'', array( $this, 'admin_ajax'));
		}
	}
	/*******************************************************************************************************************************************************
	*
	* 	[admin ajax]
	*
	********************************************************************************************************************************************************/
	function admin_ajax($wppizza_options){
		/*****************************************************
			[adding new additive]
		*****************************************************/
		if($_POST['vars']['field']=='additives' && isset($_POST['vars']['setKeys']) ){
		
			/**get next highest key available**/
			$nextKey=0;
			if(isset($_POST['vars']['setKeys']) && is_array($_POST['vars']['setKeys'])){
				$currentKeys=array();
				foreach($_POST['vars']['setKeys'] as $key_exists){
					$currentKeys[$key_exists['value']]=$key_exists['value'];
				}
				$highestKey=max($currentKeys);
				$nextKey=$highestKey+1;
			}
		
			/** cretae some (albeit empty) default values */
			$default_values['sort'] = '';
			$default_values['name'] = '';
		
		
			$output = $this->wppizza_admin_section_additives($_POST['vars']['field'], $nextKey, $default_values);		
		
			print"".$output."";
			exit();
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
	/*********************************************************
	*
	*	[add metaboxes]
	*	@since 3.0
	*
	*********************************************************/
	function wppizza_filter_admin_add_metaboxes($wppizza_meta_box, $meta_values, $meal_sizes, $wppizza_options){

		if(!empty($wppizza_options[$this->section_key])){
			/*->*** which additives in item ***/
			$wppizza_meta_box[$this->section_key]='';
			$wppizza_meta_box[$this->section_key].="<div class='".WPPIZZA_SLUG."_option_meta'>";

			$wppizza_meta_box[$this->section_key].="<label class='".WPPIZZA_SLUG."-meta-label'>".__('Additives', 'wppizza-admin').": </label>";
			asort($wppizza_options[$this->section_key]);//sort but keep index
			foreach($wppizza_options[$this->section_key]  as $key=>$value){
				$wppizza_meta_box[$this->section_key].="<label class='button'>";
				$wppizza_meta_box[$this->section_key].="<input name='".WPPIZZA_SLUG."[".$this->section_key."][".$key."]' size='5' type='checkbox' ". checked((is_array($meta_values[$this->section_key]) && in_array($key,$meta_values[$this->section_key])),true,false)." value='".$key."' /> ".$value['name']."";
				$wppizza_meta_box[$this->section_key].="</label>";
			}

			$wppizza_meta_box[$this->section_key].="</div>";
		}


		return $wppizza_meta_box;
	}

	/*********************************************************
	*
	*	[save metaboxes values]
	*	@since 3.0
	*
	*********************************************************/
	function wppizza_filter_admin_save_metaboxes($itemMeta, $item_id, $wppizza_options){

    	//**additives**//
    	$itemMeta[$this->section_key]=array();
    	if(isset($_POST[WPPIZZA_SLUG][$this->section_key])){
    	foreach($_POST[WPPIZZA_SLUG][$this->section_key] as $key=>$val){
    		$itemMeta[$this->section_key][$key]				= (int)$_POST[WPPIZZA_SLUG][$this->section_key][$key];
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
			$settings['sections'][$this->section_key] =  __('Additives Available', 'wppizza-admin');
		}
		/*help*/
		if($help){
			$settings['help'][$this->section_key][] = array(
				'label'=>__('Manage Additives', 'wppizza-admin'),
				'description'=>array(
					__('Some meals or beverages may contain additives.', 'wppizza-admin'),
					__('Add any possible additives (or any other notes for that matter) here and select them at any meal / beverage that contains these additives. This in turn will add a footnote to pages denoting which item contains what additives', 'wppizza-admin'),
					__('By default, additives will be sorted alphabetically.', 'wppizza-admin'),
					__('However, you can use the "sort" field to customise the sortorder. If you do, your choosen sort id will be used to identify your choosen additives in the frontend so you want to make sure to have unique identifiers/sort id\'s', 'wppizza-admin')
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
				asort($wppizza_options[$field]);//sort but keep index
				/* get additives that are in use */
				$additives_in_use = wppizza_options_in_use($field);
				foreach($wppizza_options[$field] as $key=>$values){
					echo"".$this->wppizza_admin_section_additives($field, $key, $values, $additives_in_use[$field]);
				}
			}
			echo"</div>";
			/** add new button **/
			echo"<div id='wppizza-".$field."-add' class='wppizza_admin_add'>";
				echo "<input type='button' id='wppizza_add_".$field."' class='button' value='".__('add additive', 'wppizza-admin')."' />";
			echo"</div>";
		}
	}


	/**
		[available sizes of meal items or add new via ajax]
	**/
	private function wppizza_admin_section_additives($field, $key, $values=null, $additives_in_use=null){

		$str='';

		$str.="<div class='wppizza_option wppizza_".$this->section_key."_option'>";

					/*for easy checking for existing keys when adding new**/
					$str.="<input id='wppizza_".$field."_".$key."' class='wppizza-getkey' name='wppizza-getkey[".$key."]' type='hidden' value='".$key."'>";
					
					$str.="<span class='wppizza_label_50'>";
						$str.="ID: ".$key."";
					$str.="</span>";

					$str.="<span>";
						$str.="".__('sort', 'wppizza-admin').":";
						$str.="<input name='".WPPIZZA_SLUG."[".$field."][".$key."][sort]' size='3' type='text' value='". $values['sort'] ."' placeholder=''/>";
					$str.="</span>";					
					
					$str.="<span>";
						$str.="".__('name', 'wppizza-admin').":";
						$str.="<input name='".WPPIZZA_SLUG."[".$field."][".$key."][name]' size='30' type='text' value='". $values['name'] ."' placeholder=''/>";
					$str.="</span>";					
					
					$str.="<span>";
						if(!isset($additives_in_use[$key])){
							$str.="<a href='#' class='".WPPIZZA_SLUG."-delete ".$field." ".WPPIZZA_SLUG."-dashicons dashicons-trash' title='".__('delete', 'wppizza-admin')."'></a>";
						}else{
							$str.="".__('in use', 'wppizza-admin')."";
						}
					$str.="</span>";										

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
			default additives are automatically creaeted  on install
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
					if(trim($values['name'])!=''){
						$sort= ($values['sort']!='' ) ? wppizza_validate_alpha_only($values['sort']) : '';
						$options[$this->section_key][$key]=array('sort'=>$sort,'name'=>wppizza_validate_string($values['name']));	
						
					}
				}
			}

			/*
				in case someone does something really daft (editing post pages and sizes at the same time in 2 different windows)....
				make sure we do not delete something that really should be there.
			*/
			global $wppizza_options;
			$additives_in_use = wppizza_options_in_use($this->section_key);
			if(!empty($additives_in_use[$this->section_key])){
				foreach($additives_in_use[$this->section_key] as $additive_key){
					if(!isset($options[$this->section_key][$additive_key])){
						$options[$this->section_key][$additive_key] = $wppizza_options[$this->section_key][$additive_key];
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
$WPPIZZA_MODULE_ADDITIVES = new WPPIZZA_MODULE_ADDITIVES();
?>