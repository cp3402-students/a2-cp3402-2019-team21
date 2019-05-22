<?php
/**
* WPPIZZA_MODULE_OPENINGTIMES_CLOSED Class
*
* @package     WPPIZZA
* @subpackage  WPPIZZA_MODULE_OPENINGTIMES_CLOSED
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
class WPPIZZA_MODULE_OPENINGTIMES_CLOSED{

	private $settings_page = 'openingtimes';/* which admin subpage (identified there by this->class_key) are we adding this to */
	private $section_key = 'closed';/* must be unique */


	function __construct() {
		/**********************************************************
			[add settings to admin]
		***********************************************************/
		if(is_admin()){
			/* add admin options settings page*/
			add_filter('wppizza_filter_settings_sections_'.$this->settings_page.'', array($this, 'admin_options_settings'), 40, 5);
			/* add admin options settings page fields */
			add_action('wppizza_admin_settings_section_fields_'.$this->settings_page.'', array($this, 'admin_options_fields_settings'), 10, 5);
			/**add default options **/
			add_filter('wppizza_filter_setup_default_options', array( $this, 'options_default'));
			/**validate options**/
			add_filter('wppizza_filter_options_validate', array( $this, 'options_validate'), 10, 2 );
			/** admin ajax **/
			add_action('wppizza_ajax_admin_'.$this->settings_page.'', array( $this, 'admin_ajax'));
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
	/*******************************************************************************************************************************************************
	*
	* 	[admin ajax]
	*
	********************************************************************************************************************************************************/
	function admin_ajax($wppizza_options){
		/*****************************************************
			[adding new custom closing time]
		*****************************************************/
		if($_POST['vars']['field']=='times_closed_standard'){
			$markup=$this->wppizza_admin_section_times_closed_standard($_POST['vars']['field']);
			print $markup ;
			exit();
		}		
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
			$settings['sections'][$this->section_key] =  __('Closed', 'wppizza-admin');
		}
	
		/*help*/
		if($help){
			$settings['help'][$this->section_key][] = array(
				'label'=>__('Manage Times Closed', 'wppizza-admin'),
				'description'=>array(
					__('If you are closed on certain days for a number of hours, enter them here.', 'wppizza-admin'),
					__('E.g: if you are generally open on Tuesdays - as set above - from 9:30 to 23:00, but close for lunch between 12:00 and 14:00, enter Tuesdays 12:00 - 14:00 here. If you are also closed on Tuesday between 17:30 and 18:00, set this as well and so on.', 'wppizza-admin'),
					__('Furthermore, do not enter times here that span midnight. If you are however closed from - let\'s say - 11:00 PM Mondays to 1:00 AM Tuesdays, enter "Mondays 23:00 to 23:59" as well as "Tuesdays 0:00 to 1:00".', 'wppizza-admin'),
					__('If you have setup any custom dates above (for example christmas or whatever), select "Custom Dates" instead of the day of week if you want to apply these closing times only to those dates.', 'wppizza-admin'),
					'<span class="wppizza-highlight">'.__('Note: if you set anything here, it will not be reflected when displaying openingtimes via shortcode or in the widget, so you might want to display your openingtimes manually somewhere. It DOES, however close the shoppingcart, the ability to order etc as required)', 'wppizza-admin').'</span>'
				)
			);
		}
		
		/*fields*/
		if($fields){
			$field = 'times_closed_standard';
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

		if($field=='times_closed_standard'){
			
			echo"<div id='wppizza_".$field."_options'  class='wppizza_admin_options'>";			
			if(isset($wppizza_options[$this->settings_page][$field])){
				/* sort by date */
				asort($wppizza_options[$this->settings_page][$field]);
				foreach($wppizza_options[$this->settings_page][$field] as $k=>$values){
					echo"".$this->wppizza_admin_section_times_closed_standard($field, $values);
				}}
			echo"</div>";
			
			/** add new button **/
			echo"<div id='wppizza-".$field."-add' class='wppizza_admin_add'>";
				echo "<a href='javascript:void(0)' id='wppizza_add_".$field."' class='button'>".__('add', 'wppizza-admin')."</a>";
			echo"</div>";
		}
	}
	/*------------------------------------------------------------------------------
	#	[insert default option on install]
	#	$parameter $options array() | filter passing on filtered options
	#	@since 3.0
	#	@return array()
	------------------------------------------------------------------------------*/
	function options_default($options){
		
		$options[$this->settings_page]['times_closed_standard']=array();

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

			$options[$this->settings_page]['times_closed_standard'] = array();//initialize array
			if(isset($input[$this->settings_page]['times_closed_standard']['day'])){
			foreach($input[$this->settings_page]['times_closed_standard']['day'] as $key=>$day){
				$options[$this->settings_page]['times_closed_standard'][$key]['day']=(int)$day;
				$options[$this->settings_page]['times_closed_standard'][$key]['close_start']=wppizza_validate_24hourtime($input[$this->settings_page]['times_closed_standard']['close_start'][$key],'Y-m-d');
				$options[$this->settings_page]['times_closed_standard'][$key]['close_end']=wppizza_validate_24hourtime($input[$this->settings_page]['times_closed_standard']['close_end'][$key],'Y-m-d');
			}}
		}
	return $options;
	}

	/*********************************************************
			[helper - opening times custom also used when adding via ajax]
	*********************************************************/
	function wppizza_admin_section_times_closed_standard($field, $values = false){

		$str='';
		$str.="<div class='wppizza_option'>";
						
			$str.="<select name='".WPPIZZA_SLUG."[".$this->settings_page."][".$field."][day][]'>";
				if(isset($values['day']) && $values['day'] =='-1'){$sel=" selected='selected'";}else{$sel="";}
				$str.="<option value='-1'".$sel.">--".__('Custom Dates Above (if any)', 'wppizza-admin')."--</option>";
				foreach(wppizza_days() as $k=>$v){
					if(isset($values['day']) && $values['day'] == $k){$sel=" selected='selected'";}else{$sel="";}
					$str.="<option value='".$k."'".$sel.">".$v."</option>";
				}
			$str.="</select>";
			
			$str.="".__('closed from', 'wppizza-admin').":";
			
			$str.="<input name='".WPPIZZA_SLUG."[".$this->settings_page."][".$field."][close_start][]' size='3' type='text' class='wppizza-time-select' value='".(empty($values['close_start']) ? '12:00' : $values['close_start'] )."' />";
			$str.="".__('to', 'wppizza-admin').":";
			
			$str.="<input name='".WPPIZZA_SLUG."[".$this->settings_page."][".$field."][close_end][]' size='3' type='text' class='wppizza-time-select' value='".(empty($values['close_end']) ? '13:00' : $values['close_end'] )."' />";
			$str.="<a href='javascript:void(0);' class='wppizza-delete ".$field." ".WPPIZZA_SLUG."-dashicons dashicons-trash' title='".__('delete', 'wppizza-admin')."'></a>";
			
		$str.="</div>";
		
		return $str;
	}
}
/***************************************************************
*
*	[ini]
*
***************************************************************/
$WPPIZZA_MODULE_OPENINGTIMES_CLOSED = new WPPIZZA_MODULE_OPENINGTIMES_CLOSED();
?>