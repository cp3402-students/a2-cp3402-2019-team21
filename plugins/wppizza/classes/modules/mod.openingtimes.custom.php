<?php
/**
* WPPIZZA_MODULE_OPENINGTIMES_CUSTOM Class
*
* @package     WPPIZZA
* @subpackage  WPPIZZA_MODULE_OPENINGTIMES_CUSTOM
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
class WPPIZZA_MODULE_OPENINGTIMES_CUSTOM{

	private $settings_page = 'openingtimes';/* which admin subpage (identified there by this->class_key) are we adding this to */
	private $section_key = 'custom';/* must be unique */


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
			[adding new custom opening time]
		*****************************************************/
		if($_POST['vars']['field']=='opening_times_custom'){
			$markup=$this->wppizza_admin_section_opening_times_custom($_POST['vars']['field']);
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
			$settings['sections'][$this->section_key] =  __('Custom Opening Times', 'wppizza-admin');
		}
	
		/*help*/
		if($help){
			$settings['help'][$this->section_key][] = array(
				'label'=>__('Manage Custom Opening Times', 'wppizza-admin'),
				'description'=>array(
					__('Set any dates / days here where opening times differ from the standard times (such as christmas, holidays etc).', 'wppizza-admin'),
					'<b>'.__('USE 24 HOUR CLOCK.', 'wppizza-admin').'</b>',
					__('If you are closed on a given day set both times to be the same, if you are open 24 hours set times from 0:00 to 24:00', 'wppizza-admin'),
					'<span class="wppizza-highlight">'.__('Ensure that the Wordpress timezone setting in Settings->Timezone is correct', 'wppizza-admin').'</span>'						
				)
			);
		}
		
		/*fields*/
		if($fields){
			$field = 'opening_times_custom';
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

		if($field=='opening_times_custom'){
			
			echo"<div id='wppizza_".$field."_options'  class='wppizza_admin_options'>";			
			if(isset($wppizza_options[$this->settings_page][$field])){
				/* sort by date */
				asort($wppizza_options[$this->settings_page][$field]);
				foreach($wppizza_options[$this->settings_page][$field] as $k=>$values){
					echo"".$this->wppizza_admin_section_opening_times_custom($field, $values);
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
		$options[$this->settings_page]['opening_times_custom'][]=array(
			'date'=>''.(date("Y")+1).'-01-01',
			'open'=>'17:00',
			'close'=>'01:00'
		);
		$options[$this->settings_page]['opening_times_custom'][]=array(
			'date'=>''.date("Y").'-12-25',
			'open'=>'17:00',
			'close'=>'01:00'
		);



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

			$options[$this->settings_page]['opening_times_custom'] = array();//initialize array
			if(isset($input[$this->settings_page]['opening_times_custom']['date'])){
			foreach($input[$this->settings_page]['opening_times_custom']['date'] as $key=>$date){
				$options[$this->settings_page]['opening_times_custom'][$key]['date']=wppizza_validate_date($date,'Y-m-d');
				$options[$this->settings_page]['opening_times_custom'][$key]['open']=wppizza_validate_24hourtime($input[$this->settings_page]['opening_times_custom']['open'][$key],'Y-m-d');
				$options[$this->settings_page]['opening_times_custom'][$key]['close']=wppizza_validate_24hourtime($input[$this->settings_page]['opening_times_custom']['close'][$key],'Y-m-d');
			}}
		}
	return $options;
	}
	/*********************************************************
			[helper - opening times custom also used when adding via ajax]
	*********************************************************/
	function wppizza_admin_section_opening_times_custom($field, $values = false){

		$date_formatted = !empty($values['date']) ? date_i18n("d M Y",strtotime($values['date'])) : '';
		$date = !empty($values['date']) ? $values['date'] : '';

		$str='';
		$str.="<div class='wppizza_option'>";
						
			$str.="<input name='".WPPIZZA_SLUG."[".$this->settings_page."][".$field."][date_formatted][]' size='10' type='text' class='wppizza-date-select' value='".$date_formatted."' />";
			$str.="<input name='".WPPIZZA_SLUG."[".$this->settings_page."][".$field."][date][]' type='hidden' class='wppizza-date' value='".$date."' />";
			
			$str.="".__('open from', 'wppizza-admin').":";
			
			$str.="<input name='".WPPIZZA_SLUG."[".$this->settings_page."][".$field."][open][]' size='3' type='text' class='wppizza-time-select' value='".$values['open']."' />";
			$str.="".__('to', 'wppizza-admin').":";
			
			$str.="<input name='".WPPIZZA_SLUG."[".$this->settings_page."][".$field."][close][]' size='3' type='text' class='wppizza-time-select' value='".$values['close']."' />";
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
$WPPIZZA_MODULE_OPENINGTIMES_CUSTOM = new WPPIZZA_MODULE_OPENINGTIMES_CUSTOM();
?>