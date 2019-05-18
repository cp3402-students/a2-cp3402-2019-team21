<?php
/**
* WPPIZZA Constants
*
* @package     WPPIZZA
* @subpackage  Constants
* @copyright   Copyright (c) 2015, Oliver Bach
* @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
* @since       3.0
*/
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit();/*Exit if accessed directly*/

	// if including constants on uninstall
	$PLUGIN_FILE_ABS_PATH = isset($PLUGIN_FILE_ABS_PATH) ? $PLUGIN_FILE_ABS_PATH : dirname(dirname(__FILE__)).'/wppizza.php';
	/*******************************************************************************************************************************
	*
	*
	*
	*	DO NOT EVEN THINK ABOUT CHANGING/EDITING ANY OF THE FOLLOWING CONSTANTS, IT WILL BREAK THINGS IF YOU DO
	*
	*
	*
	*******************************************************************************************************************************/
		define('WPPIZZA_SLUG', 'wppizza');
		define('WPPIZZA_PREFIX', ''.WPPIZZA_SLUG.'');/* we might do something separately with this one day */
		define('WPPIZZA_POST_TYPE', ''.WPPIZZA_SLUG.'');
		define('WPPIZZA_TAXONOMY', ''.WPPIZZA_POST_TYPE.'_menu');
		define('WPPIZZA_TABLE_ORDERS', WPPIZZA_SLUG.'_orders');
		define('WPPIZZA_TABLE_ORDERS_META', WPPIZZA_SLUG.'_orders_meta');
		/*********************************************************************************

			some constants for convenience

		**********************************************************************************/
		define('WPPIZZA_PLUGIN_PATH', $PLUGIN_FILE_ABS_PATH );
		define('WPPIZZA_PLUGIN_INDEX', 'wppizza/wppizza.php');
		define('WPPIZZA_PATH', plugin_dir_path($PLUGIN_FILE_ABS_PATH) );
		define('WPPIZZA_PATH_LOGS', WPPIZZA_PATH.'logs/');
		define('WPPIZZA_URL', plugin_dir_url($PLUGIN_FILE_ABS_PATH) );
		define('WPPIZZA_CHARSET',get_bloginfo('charset'));
		define('WPPIZZA_WP_TIME',current_time('timestamp'));/*get current time timestamp depending on timezone set*/
		define('WPPIZZA_UTC_TIME',current_time('timestamp', true));/*get utc time timestamp depending on timezone set. note: this might - very occasionally - be 1 sec behind WPPIZZA_WP_TIME +/-hours offset as current_time() runs 2x */
		define('WPPIZZA_PAYMENT_STATUS_SUCCESS','COMPLETED');
		define('WPPIZZA_TRANSIENT_REPORTS_NAME', WPPIZZA_SLUG.'_report_dataset');/*transient name for reports*/
		define('WPPIZZA_LISTENER_PARAMETER', WPPIZZA_SLUG.'-listener');/*webhook/listener get parameter to listen for (essentially an IPN listener for gateways, where GET var is siteurl?wppizza-listener=[xxx] ) */
		define('WPPIZZA_CUSTOM_HEADER_EMAIL', 'X-'.strtoupper(WPPIZZA_SLUG).'-Version: '.WPPIZZA_VERSION);/* custom header string to add to any wppizza emails */
		/*********************************************************************************

			constants for themes(or subthemes)  abs path / uri's
			[get/set Template Directories/Uri's. also check for subdir 'wppizza' and child themes]

		**********************************************************************************/
		$paths['template_dir']='';
		$paths['template_uri']='';
		$paths['locate_dir']='';
		$dir=get_stylesheet_directory();
		$uri=get_stylesheet_directory_uri();
		$content_dir=WP_CONTENT_DIR;
		$content_uri=content_url();

		/*
			customised templates must be in wppizza subdirectory
		*/

		/*
			using global wp-content directory - for multisite installs for example.

		*/
		if(is_dir($content_dir.'/'.WPPIZZA_SLUG)){
			$paths['template_dir'] = $content_dir.'/'.WPPIZZA_SLUG;
			$paths['template_uri'] = $content_uri.'/'.WPPIZZA_SLUG;
			$paths['locate_dir'] = WPPIZZA_SLUG.'/';
		}

		/*
			using theme/childtheme.
			overrides global

		*/
		if(is_dir($dir.'/'.WPPIZZA_SLUG)){
			$paths['template_dir'] = $dir.'/'.WPPIZZA_SLUG;
			$paths['template_uri'] = $uri.'/'.WPPIZZA_SLUG;
			$paths['locate_dir'] = WPPIZZA_SLUG.'/';
		}

		define('WPPIZZA_TEMPLATE_DIR', $paths['template_dir']);
		define('WPPIZZA_TEMPLATE_URI', $paths['template_uri']);
		define('WPPIZZA_LOCATE_DIR', $paths['locate_dir']);

	/*******************************************************************************************************************************
	*
	*
	*
	*	following constants can be changed by defining them in the wp-config.php
	*
	*
	*
	*******************************************************************************************************************************/
		if(!defined('WPPIZZA_NAME')){
			define('WPPIZZA_NAME', 'WPPizza');/*change of name in admin*/
		}
		if(!defined('WPPIZZA_MENU_ICON')){
			define('WPPIZZA_MENU_ICON', WPPIZZA_URL . 'assets/images/pizza_16.png');/*should be a URL - i.e http(s)://www.domain.com/somepath/someimage.png|jpg  */
		}
		if(!defined('WPPIZZA_WIDGET_CSS_CLASS')){
			define('WPPIZZA_WIDGET_CSS_CLASS', WPPIZZA_SLUG.'_widget');/*change of class name associated with wppizza widgets. */
		}
		/*
		to save us having to mess around with templates for single items (when linked from search results for example)
		set an identifier in permalinks	to change the variable (in case there are namespace clashes or just if one prefers
		another var,  set define('WPPIZZA_SINGLE_VAR', 'new-var') in the wp-config.php (lowercase , no spaces)
		*/
		if(!defined('WPPIZZA_SINGLE_PERMALINK_VAR')){
			define('WPPIZZA_SINGLE_PERMALINK_VAR', 'menu_item');
		}
		/**set max line length for any plaintext emails/templates etc @since 3.7.1 reduced from 74 to 70 */
		if(!defined('WPPIZZA_PLAINTEXT_MAX_LINE_LENGTH')){
			define('WPPIZZA_PLAINTEXT_MAX_LINE_LENGTH', 70);
		}
		/**allow perhaps for some leeway over 70 for plaintext comments etc @since 3.7.1 reduced from 74 to 70 */
		if(!defined('WPPIZZA_PLAINTEXT_MAX_LINE_LENGTH_WORDWRAP')){
			define('WPPIZZA_PLAINTEXT_MAX_LINE_LENGTH_WORDWRAP', 70);
		}
		/**
			might make that an option somewhere at some point.
			for now, use constant for NOT sorting items alphabetically, but the way they were added
		**/
		if(!defined('WPPIZZA_SORT_ITEMS_AS_ADDED')){
			define('WPPIZZA_SORT_ITEMS_AS_ADDED', false);
		}

		/**********************************************************************
			ADMIN CONSTANTS
		**********************************************************************/
		if(!defined('WPPIZZA_ADMIN_DASHBOARD_TRANSIENT_REPORTS_EXPIRY')){
			define('WPPIZZA_ADMIN_DASHBOARD_TRANSIENT_REPORTS_EXPIRY',(60*60));/*transient timeout for admiin dashboard widget reports (report page itself always returns live datasets)*/
		}
		if(!defined('WPPIZZA_ADMIN_FORMFIELDS_VALIDATION_MULTISELECT')){
			define('WPPIZZA_ADMIN_FORMFIELDS_VALIDATION_MULTISELECT', false);/*allow for multiple validation rules in wppizza order form settings*/
		}
		if(!defined('WPPIZZA_ADMIN_ORDER_DELIVERED_STATUS')){
			define('WPPIZZA_ADMIN_ORDER_DELIVERED_STATUS', serialize(array('DELIVERED')));/*set a SERIALIZED array of statuses which will update the order_delivered timestamp - by default only DELIVERED status will update. Choose from NEW, ACKNOWLEDGED, ON_HOLD, PROCESSED, DELIVERED, REJECTED, REFUNDED, OTHER, CUSTOM_1, CUSTOM_2, CUSTOM_3, CUSTOM_4*/
		}
		/**templates pagination, how many templates per page */
		if(!defined('WPPIZZA_ADMIN_TEMPLATES_PERPAGE')){
			define('WPPIZZA_ADMIN_TEMPLATES_PERPAGE', 5);
		}

		/**********************************************************************
			PRIVACY CONSTANTS
		**********************************************************************/
		/*
			by default we use the SECURE_AUTH_SALT from the wp-config.php here.
			However, if we need to move things to a different site, we should distinctly set WPPIZZA_SALT with the SECURE_AUTH_SALT from the old site
			in the wp-config.php of the new site to be able to decrypt wppizza orders table db entries (notably email columns) back to their actual values
		*/
		if(!defined('WPPIZZA_CRYPT_KEY')){
			define('WPPIZZA_CRYPT_KEY', SECURE_AUTH_SALT);
		}
		/**********************************************************************
			INSTALL CONSTANTS - only relevant on first installation of plugin
			if you have never used this plugin before, you might want to consider not setting these to famailiarize yourself with it
		**********************************************************************/
		if(!defined('WPPIZZA_INSTALL_NO_DEFAULTS')){
			define('WPPIZZA_INSTALL_NO_DEFAULTS', false);/* if true will not install ANY default pages, items, categories. not even an order page or root page that displays any items. nada.*/
		}
		if(!defined('WPPIZZA_INSTALL_REQUIRED_ONLY')){
			define('WPPIZZA_INSTALL_REQUIRED_ONLY', false);/* if true will only install order page , root menu page. No default items, categories or user history page*/
		}
		if(!defined('WPPIZZA_INSTALL_NO_MENU_ITEMS')){
			define('WPPIZZA_INSTALL_NO_MENU_ITEMS', false);/* if true will not install any default menu items or categories */
		}
		if(!defined('WPPIZZA_INSTALL_IGNORE_REQUIREMENTS')){
			define('WPPIZZA_INSTALL_IGNORE_REQUIREMENTS', false);/* bypass activation requirements check */
		}

		/**********************************************************************
			DEVELOPMENT CONSTANTS, SHOULD ALWAYS BE FALSE IN PRODUCTION ENVIRONMENTS
		**********************************************************************/
		if(!defined('WPPIZZA_DEV_ADMIN_NO_SAVE')){
			define('WPPIZZA_DEV_ADMIN_NO_SAVE', false);/*a simple no wppizza options saving/editing allowed in admin (will leave posts/categories unaffected better to use a role plugin for these - to be improved to allow for category/wppizza posts disabling)*/
		}
		if(!defined('WPPIZZA_DEV_DISABLE_CLEAR_CART')){
			define('WPPIZZA_DEV_DISABLE_CLEAR_CART', false);/*stop clearing cart on order complete, really only useful to be true for testing purposes*/
		}
		if(!defined('WPPIZZA_DEV_VIEW_EMAIL_OUTPUT')){
			define('WPPIZZA_DEV_VIEW_EMAIL_OUTPUT', false);/*show output of emails that would have been sent instead of thank you - DEVELOPMENT ONLY */
		}
		if(!defined('WPPIZZA_DEV_VIEW_SMTP_PASSWORD')){
			define('WPPIZZA_DEV_VIEW_SMTP_PASSWORD', false);/* show(decrypt) SMTP password in admin - DEVELOPMENT ONLY */
		}

		/**********************************************************************
			MISCELLANEOUS CONSTANTS - UNLESS YOU HAVE A PARAMETER CLASH WITH SOME
			OTHER PLUGIN JUST LEAVE THEM ALONE I WOULD SUGGEST
		**********************************************************************/
		if(!defined('WPPIZZA_TRANSACTION_GET_PREFIX')){
			define('WPPIZZA_TRANSACTION_GET_PREFIX', 'wpptx');/*prefix for get variable to display thank you / payment success page - will have order hash as value*/
		}
		if(!defined('WPPIZZA_TRANSACTION_CANCEL_PREFIX')){
			define('WPPIZZA_TRANSACTION_CANCEL_PREFIX', 'wppcltx');/*prefix for get variable to *cancel* order - will have order hash as value*/
		}
		/**********************************************************************
			RTL CONSTANTS
		**********************************************************************/
		if(!defined('WPPIZZA_FORCE_RTL_ON_TABLES')){
			define('WPPIZZA_FORCE_RTL_ON_TABLES', false);/* force table td's to be RTL, although themes should already do this using css body{direction:rtl} */
		}
		/**********************************************************************
			ENABLE INVISIBLE RECAPTCHA ON ORDER FORM
			i dont think this will ever be necessary as one has to jump through a lot of ajax hoops to even get there, however just in case, let's allow setting this
			with the below constants
			requirements : invisible recaptcha installed, activated and setup with Sites keys and Secret Keys under settings
			https://en-gb.wordpress.org/plugins/invisible-recaptcha/
		**********************************************************************/
		if(!defined('WPPIZZA_ENABLE_INVISIBLE_CAPTCHA')){
			define('WPPIZZA_ENABLE_INVISIBLE_CAPTCHA', false);/* enable invisibla re captcha */
		}
?>