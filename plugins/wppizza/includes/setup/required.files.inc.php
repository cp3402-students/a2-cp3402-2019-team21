<?php if ( ! defined( 'ABSPATH' ) ) exit;/*Exit if accessed directly*/ ?>
<?php
	global $wppizza_options;

			/**get options as global var, allow filtering**/
			$wppizza_options = get_option(WPPIZZA_SLUG,0);

			/**include some globally available validation functions*/
			require_once(WPPIZZA_PATH .'includes/global.validation.functions.inc.php');

			/**include miscellaneous globally available helper functions*/
			require_once(WPPIZZA_PATH .'includes/global.helper.functions.inc.php');

			/**include miscellaneous globally available static helper functions*/
			require_once(WPPIZZA_PATH .'includes/global.static.functions.inc.php');

			/**include globally available template functions (search results etc ) */
			require_once(WPPIZZA_PATH .'includes/global.template.functions.inc.php');

			/**include globally available formatting functions*/
			require_once(WPPIZZA_PATH .'includes/global.formatting.functions.inc.php');

			/**set and start the session and include session related functions**/
			require_once(WPPIZZA_PATH .'classes/class.wppizza.sessions.php');

			/**include globally available gateway helper functions*/
			require_once(WPPIZZA_PATH .'includes/global.gateway.helpers.inc.php');

			/**include globally available db query helper functions*/
			include_once(WPPIZZA_PATH .'includes/global.db.helpers.inc.php');
			
			/**include globally available plugin development helper functions*/
			require_once(WPPIZZA_PATH .'includes/global.plugin_dev.helpers.inc.php');			
			
			/**sort,save.update,filter,reset wppizza categories**/
			require_once(WPPIZZA_PATH .'classes/class.wppizza.categories.php');

			/**cron class**/
			require_once(WPPIZZA_PATH .'classes/class.wppizza.cron.php');

			/***
				common classes / setup
				used throughout
			***/
			/**** post type, taxonomy, permalink rewrite setup ****/
			require_once (WPPIZZA_PATH . 'classes/class.wppizza.register_posttype_taxonomy.php');


			/**admin**/
			if (is_admin()) {
				
				/*required admin functions (e.g register setting etc), css, js etc */
				require_once(WPPIZZA_PATH .'classes/admin/class.wppizza.wp_admin.php');
				/*admin actions and filters */
				require_once(WPPIZZA_PATH .'classes/admin/class.wppizza.admin_actions.php');

				/**************************************
					[wppizza post/category page(s)]
				**************************************/
				require_once(WPPIZZA_PATH .'classes/subpages/subpage.posts.php');
				require_once(WPPIZZA_PATH .'classes/subpages/subpage.categories.php');
				/**************************************
					[registered settings pages]
				**************************************/
				/*general settings - priority 0 */
				require_once(WPPIZZA_PATH .'classes/subpages/subpage.settings.php');
				/*order settings - priority 10 */
				require_once(WPPIZZA_PATH .'classes/subpages/subpage.order_settings.php');
				/*order form - priority 20 */
				require_once(WPPIZZA_PATH .'classes/subpages/subpage.order_form.php');
				/*openingtimes - priority 30 */
				require_once(WPPIZZA_PATH .'classes/subpages/subpage.openingtimes.php');
				/*gateways - priority 40 */
				require_once(WPPIZZA_PATH .'classes/subpages/subpage.gateways.php');
				/*meal sizes - priority 50 */
				require_once(WPPIZZA_PATH .'classes/subpages/subpage.meal_sizes.php');
				/*additives - priority 60 */
				require_once(WPPIZZA_PATH .'classes/subpages/subpage.additives.php');
				/*layout - priority 70 */
				require_once(WPPIZZA_PATH .'classes/subpages/subpage.layout.php');
				/*localization - priority 80 */
				require_once(WPPIZZA_PATH .'classes/subpages/subpage.localization.php');
				/*order history - priority 90 */
				require_once(WPPIZZA_PATH .'classes/subpages/subpage.order_history.php');
				/*templates - priority 100 */
				require_once(WPPIZZA_PATH .'classes/subpages/subpage.templates.php');
				/*reports - priority 110 */
				require_once(WPPIZZA_PATH .'classes/subpages/subpage.reports.php');
				/*customers - priority 120 */
				require_once(WPPIZZA_PATH .'classes/subpages/subpage.customers.php');
				/*access rights - priority 130 */
				require_once(WPPIZZA_PATH .'classes/subpages/subpage.access_rights.php');
				/*tools - priority 140 */
				require_once(WPPIZZA_PATH .'classes/subpages/subpage.tools.php');

				/**************************************
					[include user caps class -  installing/updating/set access ]
				**************************************/
				require_once(WPPIZZA_PATH .'classes/admin/class.wppizza.user_caps.inc.php');

				/**************************************
					[include gateway class to register/install etc gateways
				**************************************/
				require_once(WPPIZZA_PATH .'classes/admin/class.wppizza.register_gateways.php');

				/**************************************
					[include edd ]
				**************************************/
				require_once(WPPIZZA_PATH .'classes/shared/wppizza.edd.inc.php');

			}

			/**************************************
				[include dashboard widgets (outside of is_admin to allow for frontend display by shortcode) ]
			**************************************/
			require_once(WPPIZZA_PATH .'classes/class.wppizza.dashboard_widgets.php');
			
			/**************************************
				[include dashboard widgets (outside of is_admin to allow for frontend display by shortcode) ]
			**************************************/
			require_once(WPPIZZA_PATH .'classes/class.wppizza.sales_data.php');

			/**************************************
				[include admin helpers - 
				also loaded outside is_admin as some (orderhistory) functions might
				be in use outside admin area via shortcodes]
			**************************************/
			require_once(WPPIZZA_PATH .'classes/admin/class.wppizza.admin.helpers.php');

			/**************************************
				[include wpml - autoload ]
			**************************************/
			require_once(WPPIZZA_PATH .'classes/class.wppizza.wpml.php');

			/**************************************
				[include widgets - autoload ]
			**************************************/
			require_once(WPPIZZA_PATH .'classes/class.wppizza.widgets.php');

			/**************************************
				[include scripts and styles class - autoload]
			**************************************/
			require_once(WPPIZZA_PATH .'classes/class.wppizza.scripts_styles.php');

			/**************************************
				[include actions class - autoload]
			**************************************/
			require_once(WPPIZZA_PATH .'classes/class.wppizza.actions.php');

			/**************************************
				[include filters classs - autoload]
			**************************************/
			require_once(WPPIZZA_PATH .'classes/class.wppizza.filters.php');

			/**************************************
				[include global helpers  ]
			**************************************/
			require_once(WPPIZZA_PATH .'classes/class.wppizza.helpers.php');

			/**************************************
				[include custom walkers - extend WP walkers ]
			**************************************/
			require_once(WPPIZZA_PATH .'classes/class.wppizza.walkers.php');

			/**************************************
				db - create table , run queries
			**************************************/
			require_once(WPPIZZA_PATH .'classes/class.wppizza.db.php');
			/****************************************
				order_details - returns details of a particular order
			**************************************/
			require_once(WPPIZZA_PATH .'classes/class.wppizza.order.php');

			/****************************************
				order_execute - execute order sending emails etc
			**************************************/
			require_once(WPPIZZA_PATH .'classes/class.wppizza.order_execute.php');

			/****************************************
				email - autoload
			**************************************/
			require_once(WPPIZZA_PATH .'classes/class.wppizza.email.php');

			/**************************************
				[include gateway class to register/install etc gateways
			**************************************/
			require_once(WPPIZZA_PATH .'classes/class.wppizza.gateways.php');

			/**************************************
				add default cod and ccod gateways
			**************************************/
			require_once(WPPIZZA_PATH .'classes/gateways/gateway.cod.php');
			require_once(WPPIZZA_PATH .'classes/gateways/gateway.ccod.php');

			/**************************************
				[include user class -  login forms / registrations etc ]
			**************************************/
			require_once(WPPIZZA_PATH .'classes/class.wppizza.user.php');


			/**************************************
				[include shortcode and markup classes]
			**************************************/
			require_once(WPPIZZA_PATH .'classes/markup/shortcodes.php');
			require_once(WPPIZZA_PATH .'classes/markup/navigation.php');
			require_once(WPPIZZA_PATH .'classes/markup/orderinfo.php');
			require_once(WPPIZZA_PATH .'classes/markup/pickup_choice.php');
			require_once(WPPIZZA_PATH .'classes/markup/additives.php');
			require_once(WPPIZZA_PATH .'classes/markup/openingtimes.php');
			require_once(WPPIZZA_PATH .'classes/markup/search.php');
			require_once(WPPIZZA_PATH .'classes/markup/totals.php');
			require_once(WPPIZZA_PATH .'classes/markup/minicart.php');
			require_once(WPPIZZA_PATH .'classes/markup/hiddencart.php');
			require_once(WPPIZZA_PATH .'classes/markup/maincart.php');
			require_once(WPPIZZA_PATH .'classes/markup/pages.php');
			require_once(WPPIZZA_PATH .'classes/markup/loop.php');
			require_once(WPPIZZA_PATH .'classes/markup/email_print.php');

			/**************************************
				[include modules classes - admin parts of which will hook into subpages]
			**************************************/
			/* settings */
			require_once(WPPIZZA_PATH .'classes/modules/mod.settings.general.php');/* priority 10 */
			require_once(WPPIZZA_PATH .'classes/modules/mod.settings.orderhistory.php');/* priority 20 */
			require_once(WPPIZZA_PATH .'classes/modules/mod.settings.new_orders_notify.php');/* priority 30 */
			require_once(WPPIZZA_PATH .'classes/modules/mod.mixed.sku.php');/* priority 40 */
			//require_once(WPPIZZA_PATH .'classes/modules/mod.settings.privacy.php');/* priority 50 */	
			require_once(WPPIZZA_PATH .'classes/modules/mod.settings.miscellaneous.php');/* priority 60 */
			require_once(WPPIZZA_PATH .'classes/modules/mod.settings.permalinks.php');/* priority 70 */
			require_once(WPPIZZA_PATH .'classes/modules/mod.settings.search.php');/* priority 80 */
			require_once(WPPIZZA_PATH .'classes/modules/mod.settings.logging.php');/* priority 90 */			
			require_once(WPPIZZA_PATH .'classes/modules/mod.settings.smtp.php');/* priority 100 */

			/* order settings */
			require_once(WPPIZZA_PATH .'classes/modules/mod.order_settings.global.php');/* priority 10 */
			require_once(WPPIZZA_PATH .'classes/modules/mod.order_settings.delivery.php');/* priority 20 */
			require_once(WPPIZZA_PATH .'classes/modules/mod.order_settings.taxes.php');/* priority 30 */
			require_once(WPPIZZA_PATH .'classes/modules/mod.order_settings.discounts.php');/* priority 40 */
			require_once(WPPIZZA_PATH .'classes/modules/mod.order_settings.pickup.php');/* priority 50 */
			require_once(WPPIZZA_PATH .'classes/modules/mod.order_settings.order_update.php');/* priority 60 */
			require_once(WPPIZZA_PATH .'classes/modules/mod.order_settings.repurchase.php');/* priority 70 */
			require_once(WPPIZZA_PATH .'classes/modules/mod.order_settings.emails.php');/* priority 80 */

			/* order form */
			require_once(WPPIZZA_PATH .'classes/modules/mod.order_form.orderpage.php');/* priority 10 */
			require_once(WPPIZZA_PATH .'classes/modules/mod.order_form.confirmationpage.php');/* priority 20 */

			/* opening times */
			require_once(WPPIZZA_PATH .'classes/modules/mod.openingtimes.close_shop_now.php');/* priority 10 */
			require_once(WPPIZZA_PATH .'classes/modules/mod.openingtimes.standard.php');/* priority 20 */
			require_once(WPPIZZA_PATH .'classes/modules/mod.openingtimes.custom.php');/* priority 30 */
			require_once(WPPIZZA_PATH .'classes/modules/mod.openingtimes.closed.php');/* priority 40 */

			/* meal sizes */
			require_once(WPPIZZA_PATH .'classes/modules/mod.meal_sizes.sizes.php');/* priority 10 */

			/* additives */
			require_once(WPPIZZA_PATH .'classes/modules/mod.additives.additives.php');/* priority 10 */

			/* layout */
			require_once(WPPIZZA_PATH .'classes/modules/mod.layout.general.php');/* priority 10 */
			require_once(WPPIZZA_PATH .'classes/modules/mod.layout.style.php');/* priority 20 */
			require_once(WPPIZZA_PATH .'classes/modules/mod.layout.custom_css.php');/* priority 21 */
			require_once(WPPIZZA_PATH .'classes/modules/mod.layout.openingtimes_format.php');/* priority 30 */
			require_once(WPPIZZA_PATH .'classes/modules/mod.layout.prices_format.php');/* priority 40 */
			require_once(WPPIZZA_PATH .'classes/modules/mod.layout.items_sorting_category_display.php');/* priority 50 */
			require_once(WPPIZZA_PATH .'classes/modules/mod.layout.images.php');/* priority 60 */
			require_once(WPPIZZA_PATH .'classes/modules/mod.layout.miscellaneous.php');/* priority 70 */
			require_once(WPPIZZA_PATH .'classes/modules/mod.layout.gateways.php');/* priority 80 */
			require_once(WPPIZZA_PATH .'classes/modules/mod.layout.minicart.php');/* priority 90 */

			/* localization */
			require_once(WPPIZZA_PATH .'classes/modules/mod.localization.common.php');/* priority 10 */

			/* order history */
			require_once(WPPIZZA_PATH .'classes/modules/mod.orderhistory.orderhistory.php');/* priority 10 */

			/* templates */
			require_once(WPPIZZA_PATH .'classes/modules/mod.templates.templates.php');/* priority 10 */

			/* access rights */
			require_once(WPPIZZA_PATH .'classes/modules/mod.access_rights.access.php');/* priority 10 */

			/* tools */
			/* -> tab miscellaneous */
			require_once(WPPIZZA_PATH .'classes/modules/mod.tools.miscellaneous.various.php');/* priority 10 */
			/* -> tab privacy */
			require_once(WPPIZZA_PATH .'classes/modules/mod.tools.privacy.general.php');/* priority 10 */	
			require_once(WPPIZZA_PATH .'classes/modules/mod.tools.privacy.erase.php');/* priority 20 */	

			/* -> tab maintenance */
			require_once(WPPIZZA_PATH .'classes/modules/mod.tools.maintenance.ordertable.php');/* priority 10 */
			/* -> tab sysinfo */
			require_once(WPPIZZA_PATH .'classes/modules/mod.tools.sysinfo.overview.php');/* priority 10 */
			require_once(WPPIZZA_PATH .'classes/modules/mod.tools.sysinfo.wppizza_vars.php');/* priority 20 */
			require_once(WPPIZZA_PATH .'classes/modules/mod.tools.sysinfo.phpinfo.php');/* priority 30 */
		
			
			/* -> tab licenses */
			include_once(WPPIZZA_PATH .'classes/modules/mod.tools.licenses.init.php');/* priority 10 */



			/* multisite should be last here as it hooks into several mods above  */
			require_once(WPPIZZA_PATH .'classes/modules/mod.settings.multisite.php');/* priority 30 in settings */


			/**************************************
				install / update
				must be last to catch all the defaults from subpages filters etc
			**************************************/
			if (is_admin()) {
				require_once(WPPIZZA_PATH .'classes/admin/class.wppizza.install_update.php');
			}
?>