<?php
/*
* WPPIZZA Plugin Uninstall
*
* Uses the wppizzaPluginJanitor class for standardized & secure database cleanup.
*
* @package    wpPluginFramework <https://github.com/sscovil/wpPluginFramework>
* @author     Shaun Scovil <sscovil@gmail.com>
* @version    1.0
*/
if(!defined('WP_UNINSTALL_PLUGIN') ){
    exit();
}
/***********************
	DOING UNINSTALL
***********************/
/*

 a) delete custom post type, taxonomy, widget
 b) drop orders table(s)
 c) delete cronjobs
 d) delete customer wppizza meta data
 e) delete all associated wpml strings
 f) delete user caps from roles
 g) expire transients
*/


/*********************************
 load wppizza
*********************************/
include_once( 'wppizza.php' );
/*********************************
 load constants
*********************************/
require_once( 'includes/global.constants.inc.php' );

global $wpdb;
global $wppizza_options;
$wppizza_options = get_option(WPPIZZA_SLUG, 0);
/*********
	a) cleanup custom post type, taxonomy, widget options
*********/
	function wppizza_cleanup_cpt_tax_options(){
		/*
			custom post type
		*/
		$cpt = array(WPPIZZA_POST_TYPE);

		/*
			taxonomy
		*/
		$tax = array(array('taxonomy'=>WPPIZZA_TAXONOMY, 'object_type'=> WPPIZZA_SLUG ));

		/*
			options table option_name
		*/
		$opt = array();
		/* main options */
		$opt[] = WPPIZZA_SLUG;
		/* custom css */
		$opt[] = WPPIZZA_SLUG.'_custom_css';
		/* widgets */
		$opt[] = 'widget_'.WPPIZZA_SLUG;
		$opt[] = 'widget_'.WPPIZZA_SLUG.'_widgets';
		/* templates */
		$TEMPLATE_TYPES = array('emails', 'print'); # delete emails and print template options
		foreach($TEMPLATE_TYPES as $template_ident){
		$opt[] = WPPIZZA_SLUG.'_templates_'.$template_ident.'';
		}
		/* gateways (cod and ccod only - others should use their own uninstall) */
		$opt[] = WPPIZZA_SLUG.'_gateway_cod';
		$opt[] = WPPIZZA_SLUG.'_gateway_ccod';		

		require_once(WPPIZZA_PATH.'classes/admin/class.wppizza.admin.uninstall.janitor.php');
		$janitor = new wppizzaPluginJanitor();
		$janitor->cleanup( $opt, $cpt, $tax );
	}
/*********
 	b) drop orders table(s) and meta tables
*********/
	function wppizza_drop_orders_table(){
		global $wpdb;
		$wpdb->query("DROP TABLE IF EXISTS " . $wpdb->prefix . WPPIZZA_TABLE_ORDERS . ""); #WPPIZZA_TABLE_ORDERS
		$wpdb->query("DROP TABLE IF EXISTS " . $wpdb->prefix . WPPIZZA_TABLE_ORDERS_META . ""); #WPPIZZA_TABLE_ORDERS_META
	}

/*********
 	c) unschedule cronjobs
*********/
	function wppizza_uninstall_cronjobs(){
		wp_clear_scheduled_hook( 'wppizza_cron' );
	}

/*********
	d) cleanup user meta
*********/
	function wppizza_cleanup_user_meta(){
		global $wppizza_options;
		$metaKeys = array();
		/*get all form fields **/
		if(!empty($wppizza_options['order_form'])){
		foreach($wppizza_options['order_form'] as $key => $formfield){
			$metaKeys[] = WPPIZZA_SLUG . '_' . $key;
		}}

 		$blogusers = get_users();
    	foreach ($blogusers as $user) {
    		foreach($metaKeys as $mKey){
	    		delete_user_meta( $user->ID,$mKey);
    		}
    	}
	}
/*********
	e) uninstall wppizza user caps
*********/
	function wppizza_uninstall_user_caps(){
		WPPIZZA() -> user_caps -> uninstall_user_caps();
	}

/*********
	f) cleanup wpml strings
*********/
	function wppizza_deregister_wpml_strings(){
		if(function_exists('icl_unregister_string')){
    		global $wpdb;
    		$wpml_string = $wpdb->get_results($wpdb->prepare("SELECT id, context, name FROM ". $wpdb->prefix . "icl_strings WHERE context=%s OR context LIKE '%s'", WPPIZZA_SLUG, WPPIZZA_SLUG.'_gateway_%'));
			if(!empty($wpml_string)){
			foreach($wpml_string as $arr){
				icl_unregister_string($arr->context,$arr->name);
			}}
		}
	}

/*********
	f) cleanup transients
*********/
	function wppizza_delete_transients(){
		/* dashboard widget / reports */
		delete_transient( WPPIZZA_TRANSIENT_REPORTS_NAME.'_'.WPPIZZA_ADMIN_DASHBOARD_TRANSIENT_REPORTS_EXPIRY.'' );
	}

/**************************************************************************************
	RUN THE UNINSTALL - multisite / single
**************************************************************************************/
	if ( is_multisite() ) {
 	   	$blogs = $wpdb->get_results("SELECT blog_id FROM {$wpdb->blogs}", ARRAY_A);
 	   		if ($blogs) {
        	foreach($blogs as $blog) {
           		switch_to_blog($blog['blog_id']);
					wppizza_cleanup_cpt_tax_options();
					wppizza_drop_orders_table();
					wppizza_uninstall_cronjobs();
					wppizza_cleanup_user_meta();
					wppizza_uninstall_user_caps();
					wppizza_deregister_wpml_strings();
					wppizza_delete_transients();
				restore_current_blog();
				}
 	   		}
	}else{
		wppizza_cleanup_cpt_tax_options();
		wppizza_drop_orders_table();
		wppizza_uninstall_cronjobs();
		wppizza_cleanup_user_meta();
		wppizza_uninstall_user_caps();
		wppizza_deregister_wpml_strings();
		wppizza_delete_transients();
	}
/*
	done
*/
?>