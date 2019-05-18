<?php
#* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
#* Plugin Name: WPPizza
#* Plugin URI: https://wordpress.org/extend/plugins/wppizza/
#* Description: A Restaurant Plugin (not only for Pizza) | <a href="https://docs.wp-pizza.com/getting-started/?section=setup" target="_blank">Getting started</a> | <a href="https://docs.wp-pizza.com" target="_blank">Documentation</a> | <a href="https://wordpress.org/plugins/wppizza/#developers" target="_blank">Changelog</a> | <a target="_blank" href="https://wordpress.org/support/plugin/wppizza/reviews/?rate=5#new-post" title="Click here to rate and review this plugin on WordPress.org">Rate this plugin&nbsp;»</a>
#* Version: 3.9.6
#* Requires PHP: 5.3+
#* Author: ollybach
#* Author URI: https://www.wp-pizza.com
#* License:     GPL2
#* Text Domain: wppizza
#* Domain Path: lang
#*
#* License:
#*
#* Copyright 2012 ollybach (dev@wp-pizza.com)
#*
#* This program is free software; you can redistribute it and/or modify
#* it under the terms of the GNU General Public License, version 2, as
#* published by the Free Software Foundation.
#*
#* This program is distributed in the hope that it will be useful,
#* but WITHOUT ANY WARRANTY; without even the implied warranty of
#* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#* GNU General Public License for more details.
#*
#* You should have received a copy of the GNU General Public License
#* along with this program; if not, write to the Free Software
#* Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
#* or see <http://www.gnu.org/licenses/>.
#*
#* @package WPPizza
#* @category Core
#* @author ollybach
#* 
#* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * /

/***************************************************************
*
*	Simply exit if accessed directly
*	
***************************************************************/
if ( ! defined( 'ABSPATH' ) ) {exit();}

/***************************************************************
*
*	for simplicities sake lets put this here at the top
*	MUST ALWAYS BE SET IN LINE WITH PLUGIN VERSION NUMBER ABOVE
*
*	@since v3
*
***************************************************************/
define('WPPIZZA_VERSION', '3.9.6');

/***************************************************************
*
*
*	[CLASS]
*	@since v3
*
*
***************************************************************/
if (!class_exists( 'WPPIZZA' )){
	class WPPIZZA {

		/*
		* @var WPPIZZA
	 	* @since 3.0
	 	*/
		private static $instance;


		/*
		* @var session
	 	* @since 3.0
	 	*/
		public $session;

		/***************************************************************
		* Main WPPIZZA
		*
		* To insures only one instance of WPPIZZA exists in memory
		*
		* @since 3.0
		* @static
		* @staticvar array $instance
		* @uses WPPIZZA::setup_constants() setup constants
		* @uses WPPIZZA::requires() Include all required files
		* @uses WPPIZZA::load_textdomain() load the language files
		*
		* @return WPPIZZA
		****************************************************************/
		public static function instance() {

			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof WPPIZZA ) ) {

				self::$instance = new WPPIZZA;

				/**load text domain**/
				add_action( 'init', array( self::$instance, 'load_plugin_textdomain'));
				/**load custom functions.php - if exists**/
				add_action( 'after_setup_theme', array( self::$instance, 'load_custom_functions'));

				/*setting up some constants*/
				self::$instance->wppizza_constants(__FILE__);
    			/**include all required files **/
				self::$instance->requires();
				/**sessions**/
				self::$instance->session					= new WPPIZZA_SESSIONS();
				/**categories**/
				self::$instance->categories					= new WPPIZZA_CATEGORIES_SORTED();
				/**global helper functions**/
				self::$instance->helpers					= new WPPIZZA_HELPERS();
				/**gateways**/
				self::$instance->gateways   				= new WPPIZZA_GATEWAYS();
				/**emails**/
				self::$instance->email   					= new WPPIZZA_EMAIL();
				/**admin only**/
				if (is_admin()) {
					/**register gateways**/
					self::$instance->register_gateways   	= new WPPIZZA_REGISTER_GATEWAYS();
					/**admin user caps**/
					self::$instance->user_caps				= new WPPIZZA_USER_CAPS();
				}

				/**
					admin helper functions (order history mainly) - these might also be used outside admin area
					(so moved outside is_admin() conditional since v3.5) !
				**/
				self::$instance->admin_helper			= new WPPIZZA_ADMIN_HELPERS();

				/**
					templates | shortcodes | widget markup | sales data
				**/
				self::$instance->markup_orderinfo			= new WPPIZZA_MARKUP_ORDERINFO();		/* shortcode or enabled in cart widget */
				self::$instance->markup_openingtimes		= new WPPIZZA_MARKUP_OPENINGTIMES();	/* shortcode or enabled in cart widget */
				self::$instance->markup_minicart			= new WPPIZZA_MARKUP_MINICART();		/* shortcode or enabled in cart widget */
				self::$instance->markup_maincart			= new WPPIZZA_MARKUP_MAINCART();		/* shortcode or cart widget */
				self::$instance->markup_navigation			= new WPPIZZA_MARKUP_NAVIGATION();		/* shortcode or navigation widget */
				self::$instance->markup_search				= new WPPIZZA_MARKUP_SEARCH(); 			/* shortcode or search widget */
				self::$instance->markup_additives			= new WPPIZZA_MARKUP_ADDITIVES();		/* shortcode or in menu items, menu items loop */
				self::$instance->markup_pickup_choice		= new WPPIZZA_MARKUP_PICKUP_CHOICE();	/* shortcode and called in cart|orderpage templates */
				self::$instance->markup_totals				= new WPPIZZA_MARKUP_TOTALS(); 			/* shortcode only */
				self::$instance->markup_pages				= new WPPIZZA_MARKUP_PAGES();			/* auto (orderpage|confirmationpage|thankyoupage) and "orderhistory" shortcode */
				self::$instance->templates_menu_items		= new WPPIZZA_MARKUP_MENU_ITEMS();		/* single item, categories loop, add to cart button, bestsellers*/
				self::$instance->templates_email_print		= new WPPIZZA_MARKUP_EMAIL_PRINT();		/* email / print templates */
				self::$instance->admin_dashboard_widgets	= new WPPIZZA_DASHBOARD_WIDGETS();		/* auto dashboard widgets */
				self::$instance->sales_data					= new WPPIZZA_SALES_DATA();				/* auto sales data results */


				/**
					user login / out/ register forms
					upadate details form
					registration emails / redirects
					etc
				**/
				self::$instance->user						= new WPPIZZA_USER();/* login form, registration, emails etc */
				self::$instance->db							= new WPPIZZA_DB();/* query insert update delete etc    */
				self::$instance->order						= new WPPIZZA_ORDER();/*     */
				self::$instance->cron						= new WPPIZZA_CRON();/*  wppizza wp cronjobs   */

			}

		return self::$instance;
		}


		/*************************************************************************************
		* wppizza constants
		*
		* @access private
		* @since 3.0
		* @return void
		*************************************************************************************/
		private function wppizza_constants($PLUGIN_FILE_ABS_PATH) {
			require_once(dirname($PLUGIN_FILE_ABS_PATH) .'/includes/global.constants.inc.php');
		}

		/*************************************************************************************
		*	load wppizzas functions.php
		*	- if exists in theme/childtheme directory as ./wppizza/functions.php
		* @since 3.0
		* @return void
		*************************************************************************************/
		function load_custom_functions(){
			if(WPPIZZA_LOCATE_DIR !='' && file_exists(WPPIZZA_TEMPLATE_DIR . '/functions.php')){
		    	include_once( WPPIZZA_TEMPLATE_DIR . '/functions.php');
		    }
		}

	    /*************************************************************************************
	    * load text domain on init.
		* @since 3.0
		* @return void
	    *************************************************************************************/
	  	public function load_plugin_textdomain(){
	  		/*
	  		NOTE: BOTH only required on admin as frontend strings get added to wppizza->localization (options table) on intall
	  		and are subsequently used from there.
	  		localization is split for convenience to enable frontend localization into more languages
	  		without having to translate the whole backend too (although that would be ideal of course)
	  		*/
	  		if(is_admin()){
	        	// admin localization strings
	        	load_plugin_textdomain('wppizza-admin', false, dirname(plugin_basename( __FILE__ ) ) . '/lang' );
	        	// load after admin to insert default localization strings
	        	load_plugin_textdomain('wppizza', false, dirname(plugin_basename( __FILE__ ) ) . '/lang' );
	  		}
	    }

		/*************************************************************************************
		 * Include our required files / classes
		 *
		 * @access private
		 * @since 3.0
		 * @return void
		 *************************************************************************************/
		private function requires(){
			require_once(WPPIZZA_PATH .'includes/setup/required.files.inc.php');
		}
	}
}


/*************************************************************************************
* The main function responsible for returning WPPIZZA to functions everywhere.
*
* Example: $wppizza = WPPIZZA();
*
* @since 3.0
* @return object WPPIZZA Instance
*************************************************************************************/
function WPPIZZA() {
	return WPPIZZA::instance();
}
function wppizza_ini() {
	WPPIZZA();
}
add_action( 'plugins_loaded', 'wppizza_ini');
?>