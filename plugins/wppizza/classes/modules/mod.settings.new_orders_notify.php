<?php
/**
* WPPIZZA_MODULE_SETTINGS_NEW_ORDERS_NOTIFY Class
*
* @package     WPPIZZA
* @subpackage  WPPIZZA_MODULE_SETTINGS_NEW_ORDERS_NOTIFY
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
class WPPIZZA_MODULE_SETTINGS_NEW_ORDERS_NOTIFY{

	private $settings_page = 'settings';/* which admin subpage (identified there by this->class_key) are we adding this to */
	private $section_key = 'new_orders_notify';/* must be unique */

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
			
			/**localize admin js variables**/			
			add_filter('wppizza_filter_admin_js_localize', array( $this, 'localize_js_parameters'));
			
			/**notifications**/
			add_action('admin_footer', array( $this, 'notify_new_orders'));
			
		}
	}

	/*******************************************************************************************************************************************************
	*
	*
	*
	* 	[admin alerts]
	*
	*
	*
	********************************************************************************************************************************************************/
	function localize_js_parameters($parameters){
		global $wppizza_options, $current_screen;
		
		$alert_enabled = !empty($wppizza_options[$this->settings_page]['new_orders_notify']) ? true : false;
		$audio_set = !empty($wppizza_options[$this->settings_page]['new_orders_audio_file']) ? true : false;
		$adminbar_set = !empty($wppizza_options[$this->settings_page]['new_orders_notify_adminbar']) ? true : false;
		$pages_set = !empty($wppizza_options[$this->settings_page]['new_orders_notify_pages']) ? true : false;
		$in_page = false;
		/* dashboard */
		if($current_screen->id == 'dashboard' && !empty($wppizza_options[$this->settings_page]['new_orders_notify_pages']['dashboard'])){
			$in_page = true;	
		}
		/* orderhistory */
		if($current_screen->id == 'wppizza_page_orderhistory' && !empty($wppizza_options[$this->settings_page]['new_orders_notify_pages']['orderhistory'])){
			$in_page = true;	
		}
		/* all other admin pages */
		if(!in_array($current_screen->id,array('wppizza_page_orderhistory','dashboard')) && !empty($wppizza_options[$this->settings_page]['new_orders_notify_pages']['all_admin_pages'])){
			$in_page = true;	
		}				
		
				
		
		if($alert_enabled && $pages_set && $in_page && ($audio_set || $adminbar_set)){
			$parameters[$this->section_key] = array();
			
			/* adminbar */
			if($adminbar_set){
				$parameters[$this->section_key]['admin_bar'] =  1;
			}
			/* audio file */
			if($audio_set){
				$parameters[$this->section_key]['audio'] =  $wppizza_options[$this->settings_page]['new_orders_audio_file'];
			}
			/* polling time */
			$parameters[$this->section_key]['polling_time'] = ($wppizza_options[$this->settings_page]['admin_order_history_polling_time']*1000);
			
			/* admin bar new order label */
			$parameters[$this->section_key]['new_order_label'] = wppizza_decode_entities(sprintf($wppizza_options['localization']['admin_notify_new_order_label'], WPPIZZA_NAME));

			/* link to order history */
			$parameters[$this->section_key]['order_link'] =  admin_url( 'edit.php?post_type='.WPPIZZA_POST_TYPE.'&page=orderhistory');
			
		}
		return $parameters;
	}

   /*************************************************************************
        play sound and/or admin bar notificaton on new order
        repeats every "set polling time" seconds  until there's no
        new order anymore (just change the status)....
    ***************************************************************************/
    function notify_new_orders(){
        echo"<script type='text/javascript'>
            /* <![CDATA[ */
            jQuery(document).ready(function($){
            	if(typeof wppizza.new_orders_notify !== 'undefined'){
            		/* add admin bar notify div */
            		if(typeof wppizza.new_orders_notify.admin_bar !== 'undefined'){
            			var having_orders = '';
            			\$('#wpadminbar').append('<div id=\"wppizza_adminbar_new_orders_notify\"></div>');
            		}
            		/* audio */
            		if(typeof wppizza.new_orders_notify.audio !== 'undefined'){
            			var notifyNewOrdersAudio = new Audio(wppizza.new_orders_notify.audio);	
            		}
            		/*polling */
            		var poll_and_notify = function(){            			
						/* ajax get new orders */
						jQuery.post(ajaxurl , {action :'wppizza_admin_orderhistory_ajax',vars:{'field':'new_orders'}}, function(orders) {
							/* we have orders ! */
							if(typeof orders.new_orders !=='undefined' && orders.new_orders > 0 ){
								/* admin bar notifications */
								having_orders = '<a href=\"'+wppizza.new_orders_notify.order_link+'\"><span>' + orders.new_orders + '</span> ' + wppizza.new_orders_notify.new_order_label + '</a>';
								
								/* play audio */
								if(typeof wppizza.new_orders_notify.audio !== 'undefined'){
									notifyNewOrdersAudio.play();
								}						
							}else{
								having_orders = '';
							}
							
							/* admin bar notifications show or empty*/
							if(typeof wppizza.new_orders_notify.admin_bar !== 'undefined'){
								\$('#wppizza_adminbar_new_orders_notify').html(having_orders);
							}	
						},'json').error(function(jqXHR, textStatus, errorThrown) {alert('error : ' + errorThrown);});
            		}
            		
            		/* polling interval*/
					var notifyNewOrdersInterval=setInterval(function(){
						poll_and_notify();
					},(wppizza.new_orders_notify.polling_time));
					
					/* poll on load too */
						poll_and_notify();
            	}
            });
            /* ]]> */
        </script>";
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
			$settings['sections'][$this->section_key] = __('New Order Notifications', 'wppizza-admin');
		}
		/*help*/
		if($help){
			$settings['help'][$this->section_key][] = array(
				'label'=>__('Notifications', 'wppizza-admin'),
				'description'=>array(
					__('Enable Notifications: Enable to check periodically for new orders according to the "Polling Time" set.', 'wppizza-admin'),
					__('Audio Notifications: Play a sound when a new order arrives. Might not work with all browsers.', 'wppizza-admin'),
					__('Admin Bar Notifications: Shows visual notifications in "Admin Bar" if there are any new orders.', 'wppizza-admin'),
					__('Pages: Enable admin pages where notifications are to be enabled.', 'wppizza-admin')
				)
			);
		}
		/*fields*/
		if($fields){
			$field = 'new_orders_notify';
			$settings['fields'][$this->section_key][$field] = array( __('Enable Notifications', 'wppizza-admin'), array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('Checks for new orders according to the "Polling Time" set ', 'wppizza-admin'),
				'description'=>array()
			));	
			$field = 'new_orders_notify_adminbar';
			$settings['fields'][$this->section_key][$field] = array( __('Admin Bar Notifications', 'wppizza-admin'), array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('show visual notification in admin bar for new orders', 'wppizza-admin'),
				'description'=>array()
			));				
			$field = 'new_orders_audio_file';
			$settings['fields'][$this->section_key][$field] = array( __('Audio Notifications', 'wppizza-admin'), array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('fully qualified URL of audio file to use - depending on browser, you might have to use a .wav file. Empty to disable', 'wppizza-admin'),
				'description'=>array('<span class="wppizza-highlight">'.__('Note: this might not work with all browsers', 'wppizza-admin').'</span>')
			));	
			$field = 'new_orders_notify_pages';
			$settings['fields'][$this->section_key][$field] = array( __('Pages', 'wppizza-admin'), array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('select the page(s) on which notifications should be enabled', 'wppizza-admin'),
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
		if($field=='new_orders_notify'){
			echo "<label>";
				echo "<input id='".$field."' name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' type='checkbox'  ". checked($wppizza_options[$options_key][$field],true,false)." value='1' />";
				echo "" . $label . "";
			echo "</label>";
			echo"".$description."";
		}
		
		if($field=='new_orders_audio_file'){
			echo "<label>";
				echo "<input name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' size='90' type='text'  value='".$wppizza_options[$options_key][$field]."' placeholder='".WPPIZZA_URL."assets/audio/notify.mp3'/>";
				echo "" . $label . "";
			echo "</label>";
			echo"".$description."";
		}		
				
		if($field=='new_orders_notify_adminbar'){
			echo "<label>";
				echo "<input id='".$field."' name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' type='checkbox'  ". checked($wppizza_options[$options_key][$field],true,false)." value='1' />";
				echo "" . $label . "";
			echo "</label>";
			echo"".$description."";
		}				
				
		if($field=='new_orders_notify_pages'){	
			print'';
				print'' . $label . '<br /><br />';			
				print "<label>".__('order history', 'wppizza-admin')." <input name='".WPPIZZA_SLUG."[".$options_key."][".$field."][]' type='checkbox'  ".checked(in_array('orderhistory',$wppizza_options[$options_key][$field]),true,false)." value='orderhistory' /> </label>";
				print "<label>".__('dashboard', 'wppizza-admin')." <input name='".WPPIZZA_SLUG."[".$options_key."][".$field."][]' type='checkbox'  ".checked(in_array('dashboard',$wppizza_options[$options_key][$field]),true,false)." value='dashboard' /> </label>";
				print "<label>".__('all other admin pages', 'wppizza-admin')." <input name='".WPPIZZA_SLUG."[".$options_key."][".$field."][]' type='checkbox'  ".checked(in_array('all_admin_pages',$wppizza_options[$options_key][$field]),true,false)." value='all_admin_pages' /> </label>";			
			print'';
			print'' . $description . '';		
		
		}		
	}

	
	/*------------------------------------------------------------------------------
	#	[insert default option on install]
	#	$parameter $options array() | filter passing on filtered options
	#	@since 3.0
	#	@return array()
	------------------------------------------------------------------------------*/
	function options_default($options){
		/* 
			options 
		*/		
		$options[$this->settings_page]['new_orders_notify'] = false;		
		$options[$this->settings_page]['new_orders_audio_file'] = ''.WPPIZZA_URL.'assets/audio/notify.mp3';
		$options[$this->settings_page]['new_orders_notify_adminbar'] = true;
		$options[$this->settings_page]['new_orders_notify_pages'] = array('orderhistory'=>'orderhistory');
		
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
			$options[$this->settings_page]['new_orders_notify'] = !empty($input[$this->settings_page]['new_orders_notify']) ? true : false;	
			$options[$this->settings_page]['new_orders_notify_adminbar'] = !empty($input[$this->settings_page]['new_orders_notify_adminbar']) ? true : false;	
			
			$audio_url = esc_url($input[$this->settings_page]['new_orders_audio_file']);
			$options[$this->settings_page]['new_orders_audio_file'] = empty($audio_url) ? ''.WPPIZZA_URL.'assets/audio/notify.mp3' : $audio_url;
			
			$pages = wppizza_validate_array($input[$this->settings_page]['new_orders_notify_pages']);
			$options[$this->settings_page]['new_orders_notify_pages'] = array_combine($pages, $pages);

		}
		
	return $options;
	}
}
/***************************************************************
*
*	[ini]
*
***************************************************************/
$WPPIZZA_MODULE_SETTINGS_NEW_ORDERS_NOTIFY = new WPPIZZA_MODULE_SETTINGS_NEW_ORDERS_NOTIFY();
?>