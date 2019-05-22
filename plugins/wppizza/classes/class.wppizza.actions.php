<?php
/**
* WPPIZZA_ACTIONS Class
*
* @package     WPPIZZA
* @subpackage  WPPIZZA_ACTIONS
* @copyright   Copyright (c) 2015, Oliver Bach
* @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
* @since       3.0
*
*/
if ( ! defined( 'ABSPATH' ) ) exit;/*Exit if accessed directly*/


/************************************************************************************************************************
*
*
*	ALL FRONTEND ACTIONS excepts scripts and styles
*	WPPIZZA_ACTIONS
*
*
************************************************************************************************************************/
class WPPIZZA_ACTIONS{
	function __construct() {

		/**********************************************************
			[(try to make sure) to not cache order page - let's run this quite late]
			[wp is the earliest hook to get post->ID to check if we are actually on orderpage
		***********************************************************/
		if(!is_admin()){
		add_action('wp', array($this, 'wppizza_nocache_orderpage'), 1000);
		}
		/**********************************************************
			[ajax - logged in and non logged in users]
		***********************************************************/
		add_action('wp_ajax_wppizza_json', array($this, 'wppizza_ajax'));
		add_action('wp_ajax_nopriv_wppizza_json', array($this, 'wppizza_ajax') );
	}
	/**************************************************************
	*
	*
	* 	[ajax calls]
	*
	*
	***************************************************************/
	public function wppizza_ajax(){
		require(WPPIZZA_PATH.'ajax/ajax.wppizza.php');
		die();
	}

	/**********************************************************
		[(try to make sure) to not cache order page]
	***********************************************************/
	function wppizza_nocache_orderpage(){
		if(wppizza_is_orderpage()){
			if ( ! defined( 'DONOTCACHEPAGE' ) ){
				define( "DONOTCACHEPAGE", "true" );
			}
			if ( ! defined( 'DONOTCACHEOBJECT' ) ){
				define( "DONOTCACHEOBJECT", "true" );
			}
			if ( ! defined( 'DONOTCACHEDB' ) ){
				define( "DONOTCACHEDB", "true" );
			}
			//add WP function/headers too
			nocache_headers();
		}
	}
}
/***************************************************************
*
*	[ini]
*
***************************************************************/
$WPPIZZA_ACTIONS = new WPPIZZA_ACTIONS();
?>