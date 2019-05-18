<?php
/**
* WPPIZZA_ORDERHISTORY Class
*
* @package     WPPIZZA
* @subpackage  Submenu Pages / Classes / WPPIZZA_ORDERHISTORY
* @copyright   Copyright (c) 2015, Oliver Bach
* @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
* @since       3.0
*
*/
if ( ! defined( 'ABSPATH' ) ) exit;/*Exit if accessed directly*/


/************************************************************************************************************************
*
*
*	WPPIZZA_ORDERHISTORY
*
*
************************************************************************************************************************/
class WPPIZZA_ORDERHISTORY{
	
	/*
	* class ident
	* @var str
	* @since 3.0
	*/
	private $class_key='orderhistory';/*to help consistency throughout class in various places*/	
	/*
	* titles/lables
	* @var str
	* @since 3.0
	*/	
	private $submenu_page_header;
	private $submenu_page_title;
	private $submenu_caps_title;
	private $submenu_link_label;	
	private $submenu_priority = 90;
	
	function __construct() {
				
		/*titles/labels throughout class*/
		add_action('init', array( $this, 'init_admin_lables') );		
		/** registering submenu page -> priority 100 **/
		add_action('admin_menu', array( $this, 'wppizza_register_submenu_page'), $this->submenu_priority );	
		
		/*register capabilities for this page*/
		add_filter('wppizza_filter_define_caps', array( $this, 'wppizza_filter_define_caps'), $this->submenu_priority);	
		
		/*execute some helper functions once to use their return multiple times */
		add_action('current_screen', array( $this, 'wppizza_add_helpers') );

		/**admin ajax**/
		add_action('wp_ajax_wppizza_admin_'.$this->class_key.'_ajax', array($this, 'set_admin_ajax') );


	}
	
	/******************
	*	@since 3.0.26
    *	[admin ajax include file]
    *******************/
	public function init_admin_lables(){
		/*titles/labels throughout class*/
		$this->submenu_page_header	=	apply_filters('wppizza_filter_admin_label_page_header_'.$this->class_key.'', __('Order History','wppizza-admin'));
		$this->submenu_page_title	=	apply_filters('wppizza_filter_admin_label_page_title_'.$this->class_key.'', __('Manage Order History','wppizza-admin'));
		$this->submenu_caps_title	=	apply_filters('wppizza_filter_admin_label_caps_title_'.$this->class_key.'', __('Order History','wppizza-admin'));
		$this->submenu_link_label	=	apply_filters('wppizza_filter_admin_label_link_label_'.$this->class_key.'', __('&middot; Order History','wppizza-admin'));		
	}	
	
	/*********************************************************
	*
	*	[add global helpers and enque js]
	*	@since 3.0
	*
	*********************************************************/
	public function wppizza_add_helpers($current_screen){
		if($current_screen->id == ''.WPPIZZA_POST_TYPE.'_page_'.$this->class_key.'' && $current_screen->post_type == WPPIZZA_POST_TYPE){
			/***enqueue scripts and styles***/
			add_action('admin_enqueue_scripts', array( $this, 'wppizza_enqueue_admin_scripts_and_styles'));
		}
	}
	/*********************************************************
	*
	*	[class helpers]
	*	@since 3.0
	*
	*********************************************************/
    public function wppizza_enqueue_admin_scripts_and_styles($hook) {  	
    	/*enqueue js*/
    	wp_register_script(WPPIZZA_SLUG.'_'.$this->class_key.'', plugins_url( 'js/scripts.admin.'.$this->class_key.'.js', WPPIZZA_PLUGIN_PATH ), array('jquery'), WPPIZZA_VERSION ,true);
    	wp_enqueue_script(WPPIZZA_SLUG.'_'.$this->class_key.'');
    }
    
	/******************
	*	@since 3.0
    *	[admin ajax include file]
    *******************/
	public function set_admin_ajax(){
		require(WPPIZZA_PATH.'ajax/admin.ajax.wppizza.php');
		die();
	}	
		
	/*********************************************************
	*
	*	[register submenu page]
	*	@since 3.0
	*
	*********************************************************/
	public function wppizza_register_submenu_page(){
		$submenu_page= array(
			'url' => 'edit.php?post_type='.WPPIZZA_SLUG.'',
			'title' => ''.WPPIZZA_NAME.' '.$this->submenu_page_title,
			'link_label' => $this->submenu_link_label,
			'caps' => 'wppizza_cap_'.$this->class_key.'',
			'key' => $this->class_key,
			'callback' => array($this, 'wppizza_admin_manage_sections')		
		);
		/**add submenu page**/
		$wppizza_submenu_page=add_submenu_page($submenu_page['url'], $submenu_page['title'], $submenu_page['link_label'], $submenu_page['caps'], $submenu_page['key'], $submenu_page['callback']);
	}

	/*********************************************************
	*
	*	[echo manage settings]
	*
	*	wrap settings sections into div->form
	*	add uniquely identifiable id's / classes
	*	add h2 text
	*	add uniquely identifiable hidden input
	*	add submit button
	*
	*	@since 3.0
	*	@return str
	*
	*********************************************************/
	public function wppizza_admin_manage_sections(){
		global $wppizza_options;

		/*
			wppizza post type only
		*/
		$screen = get_current_screen();
		if($screen->post_type != WPPIZZA_POST_TYPE){return;}
		

		$get_blog_url = get_bloginfo('url');

		/**wrap settings sections into div->form */
		echo'<div id="'.WPPIZZA_SLUG.'-'.$this->class_key.'" class="'.WPPIZZA_SLUG.'-wrap  wrap '.WPPIZZA_SLUG.'-'.$this->class_key.'-wrap">';


		echo"<div class='".WPPIZZA_SLUG."-admin-pageheader'>";
		
			echo"<h2>";
				echo"<span id='".WPPIZZA_SLUG."-header'>".WPPIZZA_NAME." ".$this->submenu_page_header."</span>";
			echo"</h2>";
		
		echo"</div>";		
		
		/**echo wppizza settings field*/
		settings_fields(WPPIZZA_SLUG);
				
		/* polling etc */	
		if((empty($_GET['paged']) || (int)$_GET['paged']<=1)){		
			echo"<table id='".WPPIZZA_SLUG."_".$this->class_key."_polling' class='widefat'>";
				echo"<tbody>";
					echo"<tr>";
						echo"<td>";
							
							echo"<label>";
								echo "<a href='".$get_blog_url."/wp-admin/edit.php?post_type=".WPPIZZA_POST_TYPE."&page=".$this->class_key."' id='".WPPIZZA_SLUG."-".$this->class_key."-reset' class='".WPPIZZA_SLUG."-".$this->class_key."-reset ".WPPIZZA_SLUG."-dashicons dashicons-admin-home' title='".__('reset', 'wppizza-admin')."'></a>";
							echo'</label>';
							
							/* add action hook */
							do_action('wppizza_admin_orderhistory_parameters_before_status');

							
							echo"<label>";
								echo"".__('status', 'wppizza-admin').": ";
								$selected_status=!empty($_GET['status']) ? strtoupper($_GET['status']) : '';
								echo "<select id='".WPPIZZA_SLUG."_".$this->class_key."_orders_status' name='".WPPIZZA_SLUG."_".$this->class_key."_orders_status'>";
									echo"<option value=''>".__('-- All (except failed) --', 'wppizza-admin')."</option>";
									foreach(wppizza_order_status_default() as $key => $label){
										echo"<option value='".$key."' class='".WPPIZZA_SLUG."-".$this->class_key."-orderstatus-".strtolower($key)."' ".selected(strtoupper($selected_status),$key,false).">".$label."</option>".PHP_EOL;
									}
									echo"<option value='FAILED' class='wppizza-orderhistory-orderstatus-failed' ".selected($selected_status,'FAILED',false).">".__('failed', 'wppizza-admin')."</option>";
								echo "</select>";
							echo"</label>";
							
							/* add action hook */
							do_action('wppizza_admin_orderhistory_parameters_after_status');
							
							/**custom options if set **/
							if($wppizza_options['localization']['order_history_custom_status_options'] !='' ){
								echo"<label>".$wppizza_options['localization']['order_history_custom_status_label']." ";
								/**dropdown*/
								$selected_custom_option = !empty($_GET['custom']) ? $_GET['custom'] : '' ;
								echo WPPIZZA()->admin_helper->orderhistory_custom_options_select($this->class_key, 'select', false, $selected_custom_option , true);
								echo"</label>";
								
								/* add action hook */
								do_action('wppizza_admin_orderhistory_parameters_after_custom_status');
							}							
														
							echo"<label>";
								echo"".__('results per page', 'wppizza-admin')." <input id='".WPPIZZA_SLUG."_".$this->class_key."_orders_limit' name='".WPPIZZA_SLUG."_".$this->class_key."_orders_limit' size='3' type='text' value='".apply_filters('wppizza_filter_order_history_max_results',25)."' />";
							echo"</label>";
							
							/* add action hook */
							do_action('wppizza_admin_orderhistory_parameters_after_limit');
							
						echo"</td>";
						
						echo"<td>";
							echo"<label>";
								echo "".__('check every', 'wppizza-admin')." <input id='".WPPIZZA_SLUG."_".$this->class_key."_orders_poll_interval' name='".WPPIZZA_SLUG."_".$this->class_key."_orders_poll_interval' size='2' type='text' value='".apply_filters('wppizza_filter_order_history_polling_time',60)."' />".__('seconds', 'wppizza-admin')." ";
							echo"</label>";
							
							/* add action hook */
							do_action('wppizza_admin_orderhistory_parameters_after_polling_interval');
							
							echo"<label class='button'>";
								echo"<input id='".WPPIZZA_SLUG."_".$this->class_key."_orders_poll_enabled' type='checkbox' ". checked(apply_filters('wppizza_filter_order_history_polling_auto',true),true,false)." value='1' />".__('on|off', 'wppizza-admin')."";
							echo"</label>";
							
							/* add action hook */
							do_action('wppizza_admin_orderhistory_parameters_after_polling_enabled');							
							
							
							// loading icon (uses whole td)
							echo "<div id='".WPPIZZA_SLUG."_".$this->class_key."_polling_loading'></div>";/*shows loading icon*/
													
						echo"</td>";
					echo"</tr>";
				echo"</tbody>";
			echo"</table>";
		}
		
		/*get markup of orders*/
		echo"<div id='".WPPIZZA_SLUG."_".$this->class_key."_results'>";
			do_action('wppizza_admin_orderhistory_results');
		echo"</div>";

		echo'</div>';
	}

	/*********************************************************
	*
	*	[define caps]
	*	@since 3.0
	*
	*********************************************************/
	function wppizza_filter_define_caps($caps){
		/**add editing capability for this page**/
		$caps[$this->class_key]=array('name'=>$this->submenu_caps_title ,'cap'=>'wppizza_cap_'.$this->class_key.'');
		$caps[$this->class_key.'-delete-order']=array('name'=>__('Delete Orders', 'wppizza-admin') ,'cap'=>'wppizza_cap_delete_order');
		return $caps;
	}	
}
/***************************************************************
*
*	[ini]
*
***************************************************************/
$WPPIZZA_ORDERHISTORY = new WPPIZZA_ORDERHISTORY();
?>