<?php
/**
* WPPIZZA_MODULE_OPENINGTIMES_STANDARD Class
*
* @package     WPPIZZA
* @subpackage  WPPIZZA_MODULE_OPENINGTIMES_STANDARD
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
class WPPIZZA_MODULE_OPENINGTIMES_STANDARD{

	private $settings_page = 'openingtimes';/* which admin subpage (identified there by this->class_key) are we adding this to */
	private $section_key = 'standard';/* must be unique */

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
			/**add text header*/
			add_action('wppizza_settings_sections_header_'.$this->settings_page.'', array( $this, 'sections_header'), 10, 2 );			
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
	/*********************************************************
			[more header info]
	*********************************************************/
	function sections_header($arg, $section_count){
		if($arg['id']==$this->section_key){
			
			$timezone_set = (get_option('timezone_string')!='') ? get_option('timezone_string') : get_option('gmt_offset') ;
			echo '<span style="font-size:90%">'.__('your currently set timezone: ', 'wppizza-admin').' '.$timezone_set.' - <a href="'.admin_url('options-general.php').'">'.__('click to change', 'wppizza-admin').'</a></span>';			
		}
	}
	/*------------------------------------------------------------------------------
	#	[settings section - setting page]
	#	@since 3.0
	#	@return array()
	------------------------------------------------------------------------------*/
	function admin_options_settings($settings, $sections, $fields, $inputs, $help){

		/*section*/
		if($sections){
			$settings['sections'][$this->section_key] =  __('Standard Opening Times', 'wppizza-admin');
		}
	
		/*help*/
		if($help){
			$settings['help'][$this->section_key][] = array(
				'label'=>__('Manage Standard Opening Times', 'wppizza-admin'),
				'description'=>array(
					__('Set your standard opening times. It will not be possible to place an order outside these times.', 'wppizza-admin'),
					'<b>'.__('USE 24 HOUR CLOCK.', 'wppizza-admin').'</b>',
					__('If you are closed on a given day set both times to be the same, if you are open 24 hours set times from 0:00 to 24:00', 'wppizza-admin'),
					'<span class="wppizza-highlight">'.__('Ensure that the Wordpress timezone setting in Settings->Timezone is correct', 'wppizza-admin').'</span>'
				)
			);
		}
		
		/*fields*/
		if($fields){
			$field = 'opening_times_standard';
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

		if($field=='opening_times_standard'){
			echo"<div id='wppizza_".$field."_options'  class='wppizza_admin_options'>";
			foreach(wppizza_days() as $k=>$v){
				echo"<div>";
				echo"<label class='wppizza_weekday'>".$v.":</label> ".__('open from', 'wppizza-admin').":";
				echo "<input name='".WPPIZZA_SLUG."[".$this->settings_page."][".$field."][".$k."][open]' size='3' type='text' class='wppizza-time-select' value='".$wppizza_options[$this->settings_page][$field][$k]['open']."' />";
				echo"".__('to', 'wppizza-admin').":";
				echo "<input name='".WPPIZZA_SLUG."[".$this->settings_page."][".$field."][".$k."][close]' size='3' type='text' class='wppizza-time-select' value='".$wppizza_options[$this->settings_page][$field][$k]['close']."' />";
				echo"</div>";
			}
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
		
		$options[$this->settings_page]['opening_times_standard']=array(
			0=>array('open'=>'14:30','close'=>'01:00'),
			1=>array('open'=>'09:30','close'=>'02:00'),
			2=>array('open'=>'09:30','close'=>'02:00'),
			3=>array('open'=>'09:30','close'=>'02:00'),
			4=>array('open'=>'09:30','close'=>'02:00'),
			5=>array('open'=>'09:30','close'=>'02:00'),
			6=>array('open'=>'09:30','close'=>'02:00')
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

			$options[$this->settings_page]['opening_times_standard'] = array();
			ksort($input[$this->settings_page]['opening_times_standard']);//just for consistency. not really necessary though
			foreach($input[$this->settings_page]['opening_times_standard'] as $k=>$v){
				foreach($v as $l=>$m){
				$options[$this->settings_page]['opening_times_standard'][$k][$l]=wppizza_validate_24hourtime($m);
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
$WPPIZZA_MODULE_OPENINGTIMES_STANDARD = new WPPIZZA_MODULE_OPENINGTIMES_STANDARD();
?>