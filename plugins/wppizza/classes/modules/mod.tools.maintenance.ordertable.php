<?php
/**
* WPPIZZA_MODULE_TOOLS_MAINTENANCE_ORDERTABLE Class
*
* @package     WPPIZZA
* @subpackage  WPPIZZA_MODULE_TOOLS_MAINTENANCE_ORDERTABLE
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
class WPPIZZA_MODULE_TOOLS_MAINTENANCE_ORDERTABLE{

	private $settings_page = 'tools';/* which admin subpage (identified there by this->class_key) are we adding this to */
	private $tab_key = 'maintenance';/* must be unique within this admin page*/
	private $section_key = 'ordertable';

	function __construct() {
		/**********************************************************
			[add settings to admin]
		***********************************************************/
		if(is_admin()){
			/*** add to a specific tab ***/
			add_filter('wppizza_filter_admin_tabs_'.$this->settings_page.'', array($this, 'admin_tabs'), 10);
			/* add admin options settings page*/
			add_filter('wppizza_filter_settings_sections_'.$this->settings_page.'', array($this, 'admin_options_settings'), 10, 5);
			/* add admin options settings page fields */
			add_action('wppizza_admin_settings_section_fields_'.$this->settings_page.'', array($this, 'admin_options_fields_settings'), 10, 5);
			/**add default options **/
			add_filter('wppizza_filter_setup_default_options', array( $this, 'options_default'));
			/**validate options**/
			add_filter('wppizza_filter_options_validate', array( $this, 'options_validate'), 10, 2 );
			/**add text header*/
			//add_action('wppizza_settings_sections_header_'.$this->settings_page.'', array( $this, 'sections_header'), 10, 2 );

		}
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

	/*********************************************************
			[add section to a particular tab]
	*********************************************************/
	function admin_tabs($tabs){
		$tabs['tab'][$this->tab_key]['sections'][] = $this->section_key;
	return $tabs;
	}
	/*------------------------------------------------------------------------------
	#	[settings section - setting page]
	#	@since 3.0
	#	@return array()
	------------------------------------------------------------------------------*/
	function admin_options_settings($settings, $sections, $fields, $inputs, $help){


		/*section*/
		if($sections){
			$settings['sections'][$this->section_key] =  __('Order Table Maintenance', 'wppizza-admin');
		}

		/*help*/
		if($help){
			$settings['help'][$this->section_key][] = array(
				'label'=>__('Maintenance', 'wppizza-admin'),
				'description'=>array(
					__('Please adjust settings as appropriate according to the information provided next to each individual option.', 'wppizza-admin')
				)
			);			
		}

		/*fields*/
		if($fields){
			$field = 'delete_abandoned_orders';
			$settings['fields'][$this->section_key][$field] = array(__('Maintain orders table', 'wppizza-admin') , array(
				'value_key'=>$field,
				'option_key'=>'cron',
				'label'=>__('Delete abandoned/cancelled orders from database once on "Save Changes"','wppizza-admin'),
				'description'=>array()
			));

			$field = 'schedule';
			$settings['fields'][$this->section_key][$field] = array(__('Schedule Maintanance', 'wppizza-admin') , array(
				'value_key'=>$field,
				'option_key'=>'cron',
				'label'=>__('Schedule table maintenance to run automatically based on settings above [uses wp_cron]','wppizza-admin'),
				'description'=>array(
					'<span class="wppizza-highlight">'.__('it will NOT affect any completed or pending orders.', 'wppizza-admin').'</span>'
				)
			));

			$field = 'truncate_orders';
			$settings['fields'][$this->section_key][$field] = array(__('Empty order table', 'wppizza-admin') , array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>'<span class="wppizza-highlight">'.__('completely and irreversibly EMPTIES the order table deleting ALL orders.','wppizza-admin').'</span>',
				'description'=>array()
			));

			$field = 'check_order_table';
			$settings['fields'][$this->section_key][$field] = array(__('Update order table', 'wppizza-admin') , array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('In case you installed the plugin using an unsupported mysql version, check this box and save once *after* you have updated you mysql version to the *required* 5.5+ to update table collation, columns , indexes etc.','wppizza-admin'),
				'description'=>array(
					__('If you are already using mysql v5.5+, ignore this','wppizza-admin')
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

		if($field=='truncate_orders'){
			print'<label>';
				print "<input name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' type='checkbox' value='1' />";
				print'' . $label . '';
			print'</label>';
			print'' . $description . '';
		}

		if($field=='check_order_table'){
			print'<label>';
				print "<input name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' type='checkbox' value='1' />";
				print'' . $label . '';
			print'</label>';
			print'' . $description . '';
		}


		if($field=='delete_abandoned_orders'){
			print'<label>';
				print "<input name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' type='checkbox'  value='1' />";
				print'' . $label . '';
			print'</label>';
			print'' . $description . '';

			echo"<br />";
			echo"<label>";
				echo"".__('Delete entries older than ', 'wppizza-admin')."";
				echo"<input id='wppizza_order_days_delete' type='text' size='2' name='".WPPIZZA_SLUG."[".$options_key."][days_delete]' value='".$wppizza_options[$options_key]['days_delete']."' />";
				echo" ".__('Days (minimum: 1)', 'wppizza-admin')."";
			echo"</label>";

			echo"<br />";
			echo"<label>";
				echo"<input id='wppizza_order_failed_delete' name='".WPPIZZA_SLUG."[".$options_key."][failed_delete]' ". checked($wppizza_options[$options_key]['failed_delete'],true,false)." type='checkbox' value='1' />";
				echo"".__('Delete any unconfirmed, tampered or otherwise invalid entries too.', 'wppizza-admin')."";
			echo"<label>";

			echo"<br />";
			echo'<span class="wppizza_highlight" style="color:red">'.__('Note: This will delete these entries PERMANENTLY from the db and is not reversable.', 'wppizza-admin').'</span>';
			echo'<br /><span class="description" style="font-weight:600">'.__('it will NOT affect any completed or pending orders.', 'wppizza-admin').'</span>';
		}

		if($field=='schedule'){
			/*schedule cron**/
			$cronJobs=''.print_r(get_option($options_key),true);/**if we deactivated the plugin, cron will have been disabled for this, so we set the flag accordingly**/
			$wppizzaCronRunning = strpos($cronJobs, 'wppizza_cron');/**just search for wppizza_cron in string*/
			if ($wppizzaCronRunning === false) {
				$wppizza_options[$options_key]['schedule']='';
			}
			print'<label>';
					echo"<select name='".WPPIZZA_SLUG."[".$options_key."][schedule]' />";
					echo"<option value=''>".__('do not run', 'wppizza-admin')."</option>";
					echo"<option value='hourly' ".selected($wppizza_options[$options_key]['schedule'],"hourly",false).">".__('hourly', 'wppizza-admin')."</option>";
					echo"<option value='daily' ".selected($wppizza_options[$options_key]['schedule'],"daily",false).">".__('daily', 'wppizza-admin')."</option>";
					echo"</select>";
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

			$options['cron']['days_delete'] = 7;
			$options['cron']['failed_delete'] = false;
			$options['cron']['schedule'] = false;

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
		if(isset($_POST[''.WPPIZZA_SLUG.'_'.$this->settings_page.'_'.$this->tab_key.''])){

			/**********************************
				truncate orders
			**********************************/
			if(!empty($input[$this->settings_page]['truncate_orders'])){
				$this->wppizza_truncate_order_table();
			}

			/**********************************
				update/alter  table if required
			**********************************/
			if(!empty($input[$this->settings_page]['check_order_table'])){
				
				/**update order table structure**/
				$WPPIZZA_INSTALL_UPDATE = new WPPIZZA_INSTALL_UPDATE();
				
				global $blog_id, $wpdb;//current blog id
				$blog_prefix = $wpdb -> prefix;
				
				$table_schema = $WPPIZZA_INSTALL_UPDATE -> set_table_schema(array($blog_prefix), true);

				/*get mysql version if we can*/
	  			$mysql_info=wppizza_get_mysql_version();
	  			$options['plugin_data']['mysql_version_ok']=true;//will also be true if we cannot determine mysql version
	  			if( !empty($mysql_info['version']) && version_compare( $mysql_info['version'], wppizza_required_mysql_version(), '<' )) {
	  				$options['plugin_data']['mysql_version_ok'] = false;
	  			}
			}

			/**********************************
				delete abandoned on save
			**********************************/
			if(!empty($input['cron']['delete_abandoned_orders'])){
				/**set args for deleting now**/
				$delete_args = array();
				$delete_args['days']= !empty($input['cron']['days_delete']) ? max(1,$input['cron']['days_delete']) : 7;
				$delete_args['failed_delete']= !empty($input['cron']['failed_delete']) ? true : false;

				/**delete now using same method cronjob uses, setting some arguments**/
				WPPIZZA() -> cron -> wppizza_remove_stale_order_entries($delete_args);
			}

			/**********************************
				(un-)schedule cron
			**********************************/
			if(!empty($input['cron'])){

				$options['cron'] = array();
				$options['cron']['days_delete']= !empty($input['cron']['days_delete']) ? max(1,$input['cron']['days_delete']) : 7;
				$options['cron']['failed_delete']= !empty($input['cron']['failed_delete']) ? true : false;

				$cronSchedule='';
				if(!empty($input['cron']['schedule']) && in_array($input['cron']['schedule'],array('hourly','daily')) ){
					$cronSchedule=$input['cron']['schedule'];
				}
				$options['cron']['schedule']= $cronSchedule;
				/*schedule or remove cron **/
				WPPIZZA() -> cron -> wppizza_cron_setup_schedule($options['cron']);
			}
		}

	return $options;
	}

	/*********************************************************
	*
	*	[HELPERS]
	*	@since 3.0
	*
	*********************************************************/
	/*********************************************************
	*
	*	[truncate/empty order table]
	*	@since 3.0
	*
	*********************************************************/
	function wppizza_truncate_order_table(){
		global $wpdb;
		/*no backticks or apostrophies please**/
		/** see http://codex.wordpress.org/Creating_Tables_with_Plugins **/
		$sql="TRUNCATE ".$wpdb->prefix . WPPIZZA_TABLE_ORDERS."";
		$e = $wpdb->query($sql);
	}

}
/***************************************************************
*
*	[ini]
*
***************************************************************/
$WPPIZZA_MODULE_TOOLS_MAINTENANCE_ORDERTABLE = new WPPIZZA_MODULE_TOOLS_MAINTENANCE_ORDERTABLE();
?>