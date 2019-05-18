<?php
if(!class_exists('FmSettings')):
	/**
	*
	*/
	class FmSettings
	{

		function __construct()
		{
			add_action( 'plugins_loaded', array($this,'tlp_food_menu_load_text_domain') );
			add_action( 'admin_menu' , array($this, 'tlp_food_menu_register'));
			add_action(	'wp_ajax_tlpFmSettingsUpdate', array($this, 'tlpFmSettingsUpdate'));
			add_filter( 'plugin_action_links_' . TLP_FOOD_MENU_PLUGIN_ACTIVE_FILE_NAME, array($this, 'tlp_team_marketing') );
		}


		function tlp_team_marketing($links){
			$links[] = '<a target="_blank" href="'. esc_url( 'http://demo.radiustheme.com/wordpress/plugins/food-menu/' ) .'">Demo</a>';
			$links[] = '<a target="_blank" href="'. esc_url( 'https://radiustheme.com/how-to-setup-and-configure-tlp-food-menu-free-version-for-wordpress/' ) .'">Documentation</a>';
			$links[] = '<a target="_blank" style="color: #39b54a;font-weight: 700;"  href="'. esc_url( 'https://www.radiustheme.com/downloads/food-menu-pro-wordpress/' ) .'">Get Pro</a>';
			return $links;
		}

		function tlpFmSettingsUpdate(){
			global $TLPfoodmenu;

			$error = true;
			if($TLPfoodmenu->verifyNonce()){

				$data = array();
				if($_REQUEST['general']){
					$general['slug'] = (isset($_REQUEST['general']['slug']) ? ($_REQUEST['general']['slug'] ? sanitize_title_with_dashes( $_REQUEST['general']['slug']) : 'food-menu') : 'food-menu');
					$general['character_limit'] = (isset($_REQUEST['general']['character_limit']) ? ($_REQUEST['general']['character_limit'] ? intval( $_REQUEST['general']['character_limit']) : 150) : 150);
					$general['em_display_col'] = ($_REQUEST['general']['em_display_col'] ? esc_attr( $_REQUEST['general']['em_display_col'] ) : 2);
					$general['currency'] = ($_REQUEST['general']['currency'] ? esc_attr( $_REQUEST['general']['currency'] ) : null);
					$general['currency_position'] = ($_REQUEST['general']['currency_position'] ? esc_attr( $_REQUEST['general']['currency_position'] ) : null);
					$general['hide_image'] = isset($_REQUEST['general']['hide_image']) ? true : false;
					$general['hide_price'] = isset($_REQUEST['general']['hide_price']) ? true : false;
					$data['general'] = $general;
					$TLPfoodmenu->activate();
				}
				if($_REQUEST['others']){
					$others['css'] = ($_REQUEST['others']['css'] ? esc_attr( $_REQUEST['others']['css'] ) : null);
					$data['others'] = $others;
				}
				update_option( $TLPfoodmenu->options['settings'], $data );
				$error = false;
				$msg = __('Settings successfully updated','tlp-food-menu');
			}else{
				$msg = __('Security Error !!','tlp-food-menu');
			}
			$response = array(
				'error'=> $error,
				'msg' => $msg
			);
			wp_send_json( $response );
			die();

		}


		function tlp_food_menu_register() {
			global $TLPfoodmenu;
			add_submenu_page( 'edit.php?post_type='.$TLPfoodmenu->post_type, __('Shortcode Generator','tlp-food-menu'), __('Shortcode','tlp-food-menu'), 'administrator', 'tlp_food_menu_shortcode', array($this, 'tlp_food_menu_shortcode') );
			add_submenu_page( 'edit.php?post_type='.$TLPfoodmenu->post_type, __('TLP food menu settings','tlp-food-menu'), __('Settings','tlp-food-menu'), 'administrator', 'tlp_food_menu_settings', array($this, 'tlp_food_menu_settings') );


		}

		function tlp_food_menu_settings(){
			global $TLPfoodmenu;
			$TLPfoodmenu->render('settings.settings');
		}
		function tlp_food_menu_shortcode(){
			global $TLPfoodmenu;
			$TLPfoodmenu->render('settings.shortcode');
		}

		/**
		 * Load the plugin text domain for translation.
		 *
		 * @since 0.1.0
		 */
		public function tlp_food_menu_load_text_domain() {

			load_plugin_textdomain( 'tlp-food-menu', FALSE,  TLP_FOOD_MENU_LANGUAGE_PATH );

		}

	}
endif;
