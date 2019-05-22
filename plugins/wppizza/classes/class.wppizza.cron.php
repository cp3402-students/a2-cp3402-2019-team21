<?php
/**
* WPPIZZA_CRON Class
*
* @package     WPPIZZA
* @subpackage  Cronjobs
* @copyright   Copyright (c) 2015, Oliver Bach
* @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
* @since       3.0
*
*/
if ( ! defined( 'ABSPATH' ) ) exit;/*Exit if accessed directly*/


/************************************************************************************************************************
*
*
*	WPPIZZA_CRON
*
*
************************************************************************************************************************/
class WPPIZZA_CRON{


	function __construct() {
		/**add cronjob action**/
		add_action( 'wppizza_cron', array( $this, 'wppizza_remove_stale_order_entries'));
	}

	/****************************************************************************************************************
	*
	*
	*	[]
	*
	*
	****************************************************************************************************************/
	/*********************************************************
	*
	*		[cron events]
	*
	*********************************************************/
	/**********************************
	*	[setup or delete cronjob]
	***********************************/
	function wppizza_cron_setup_schedule($cron_args) {
		/**clear all other old schedules**/
		wp_clear_scheduled_hook( 'wppizza_cron');
		/*setup new**/
		if($cron_args['schedule']!=''){
			if ( ! wp_next_scheduled( 'wppizza_cron' ) ) {
				wp_schedule_event( time(), $cron_args['schedule'], 'wppizza_cron');
			}
		}
	}
	/**********************************
	*	[do cronjob or run on save ]
	***********************************/
	function wppizza_remove_stale_order_entries($args=array()) {
		global $wpdb, $wppizza_options;

		/*days to delete**/
		$days=!empty($args['days']) ? $args['days'] : $wppizza_options['cron']['days_delete'];


		$delete_all_stale=false;
		/**delete now**/
		if(count($args)!=0 && !empty($args['failed_delete']) ){
			$delete_all_stale=true;
		}
		/**delete cron**/
		if(count($args)==0 && !empty($wppizza_options['cron']['failed_delete']) ){
			$delete_all_stale=true;
		}
		/**do or dont delete all non completed orders**/
		$pStatusQuery=" IN ('INITIALIZED', 'CANCELLED', 'EXPIRED' ) ";/* delete INITIALIZED and CANCELLED */
		if($delete_all_stale){
			$pStatusQuery=" NOT IN ('COMPLETED', 'INPROGRESS', 'AUTHORIZED', 'CAPTURED', 'REFUNDED', 'REJECTED') "; /* ONLY DELETE everything that's NOT */
		}

		/*create sql*/
		$sql="DELETE FROM ".$wpdb->prefix . WPPIZZA_TABLE_ORDERS." WHERE order_date < TIMESTAMPADD(DAY, -%d, NOW()) AND payment_status ".$pStatusQuery." ";
		/**run query*/
		$res=$wpdb->query( $wpdb->prepare($sql, $days));

		/**add to log if cron**/
		if(count($args)==0){
			error_log("WPPIZZA CRON RUN");
		}
	}
}
?>