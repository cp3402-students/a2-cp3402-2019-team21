<?php
/**
* WPPIZZA_SCRIPTS_AND_STYLES Class
*
* @package     WPPIZZA
* @subpackage  WPPIZZA_SCRIPTS_AND_STYLES
* @copyright   Copyright (c) 2015, Oliver Bach
* @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
* @since       3.0
*
*/
if ( ! defined( 'ABSPATH' ) ) exit;/*Exit if accessed directly*/


/************************************************************************************************************************
*
*
*	ALL WPPIZZA_SCRIPTS_AND_STYLES
*
*
*
************************************************************************************************************************/
class WPPIZZA_SCRIPTS_AND_STYLES{
	function __construct() {
		/******************************
			[runs only for frontend]
		******************************/
		if(!is_admin()){
			global $wppizza_options;


			/*** enqueue header scripts on checkout ***/
			add_action( 'wp_head', array($this, 'wppizza_wp_head_on_checkout'));

			/***enqueue frontend styles***/
			add_action('wp_enqueue_scripts', array( $this, 'wppizza_register_enqueue_acripts_and_styles'), $wppizza_options['layout']['css_priority']);

		}
	}
	/******************************************************************
		add any header script on checkout

		@param void
		@since 3.7
		@return void
	******************************************************************/
	function wppizza_wp_head_on_checkout(){
		/*
			skip on all admin (including ajax )requests
			checkout only
		*/
		if(!wppizza_is_checkout()){return;}

		/*
			add script for gateways that allow inline payments
			when this particular gateway has been selected
		*/
		$selected_gateway_ident = WPPIZZA()->session->get_selected_gateway();
		do_action('wppizza_wp_head_on_checkout', $selected_gateway_ident);
	}
	/**********************************************************************************************************************************************************************************
	*
	*
	*
	*	[register and enqueue css and js ]
	*
	*
	*
	**********************************************************************************************************************************************************************************/
	function wppizza_register_enqueue_acripts_and_styles(){
		global $wppizza_options, $wp_scripts, $post;
		$is_orderpage = wppizza_is_orderpage();/*are we on orderpage ? bool */
		/** force "is_orderpage" if there is an orderpage widget on page */
		$has_orderpage_widget = wppizza_has_orderpage_widget();
		$is_orderpage = !empty($has_orderpage_widget) ? true : $is_orderpage ;
		/* load validation js on user order history page too **/
		$is_orderhistory = !empty($is_orderpage) ? false : wppizza_is_orderhistory();


		$force_script_style_inclusion = apply_filters('wppizza_filter_force_scripts_and_styles', false, $is_orderpage);/* allow to include script and styles on all pages, regardless of what page we are on if true */
		/*****************************************************************************************************
		*
		*
		*	[register and enqueue css]
		*
		*
		******************************************************************************************************/

		/***
			skip all styles if not including
		***/
		if(!empty($wppizza_options['layout']['include_css'])){
			/**
				get available style and enqueue as required
			**/
			$styles=wppizza_public_styles();

			/*
				array of styles to enqueue
			*/
			$enqueue_styles_ident=array();
			/*
				last style enqueued to set custom css dependency
			*/
			$dependency_last_style = null;

			/*
				load common global style beforee all others
			*/
			$css_filename=''.WPPIZZA_SLUG.'.css';
			$css_enqueue_ident= ''.WPPIZZA_SLUG.'';
			if (file_exists( WPPIZZA_TEMPLATE_DIR . '/css/'.$css_filename.'')){
				/**stylesheet copied to template directory to keep settings**/
				$enqueue_styles_ident[$css_enqueue_ident] = wp_register_style($css_enqueue_ident, WPPIZZA_TEMPLATE_URI.'/css/'.$css_filename, array(), WPPIZZA_VERSION);
			}else{
				$enqueue_styles_ident[$css_enqueue_ident] = wp_register_style($css_enqueue_ident, WPPIZZA_URL . 'css/'.$css_filename, array(), WPPIZZA_VERSION);
			}


			/*
				loop through available styles, registering the ones that need to be registered
			*/
			foreach($styles as $style_key=>$style){

				/**************************************
					register selected only or all
				**************************************/
				if( $style_key == $wppizza_options['layout']['style'] || !empty($wppizza_options['layout']['load_additional_styles'][$style_key])){

					/*************************************
						[register any dependencies first]
					*************************************/
					if(!empty($style['dependency'])){
						$css_file_extension = !empty($styles[$style['dependency']]['ext']) ? $styles[$style['dependency']]['ext'] : 'css';
						$css_filename=''.WPPIZZA_SLUG.'.'.$style['dependency'].'.'.$css_file_extension;
						$css_enqueue_ident= ''.WPPIZZA_SLUG.'-'.$style['dependency'];

						if (file_exists( WPPIZZA_TEMPLATE_DIR . '/css/'.$css_filename.'')){
							/**stylesheet copied to template directory to keep settings**/
							$enqueue_styles_ident[$css_enqueue_ident] = wp_register_style($css_enqueue_ident, WPPIZZA_TEMPLATE_URI.'/css/'.$css_filename, array(), WPPIZZA_VERSION);
						}else{
							$enqueue_styles_ident[$css_enqueue_ident] = wp_register_style($css_enqueue_ident, WPPIZZA_URL . 'css/'.$css_filename, array(), WPPIZZA_VERSION);
						}
					}
					/*************************************
						[register (selected) stylesheet, adding dependency if set]
					*************************************/
					$css_file_extension = !empty($style['ext']) ? $style['ext'] : 'css';
					$css_filename=''.WPPIZZA_SLUG.'.'.$style['id'].'.'.$css_file_extension;
					$css_enqueue_ident=''.WPPIZZA_SLUG.'-'.$style['id'];
					$css_dependency=!empty($style['dependency']) ? array(''.WPPIZZA_SLUG.'-'.$style['dependency']) : array(WPPIZZA_SLUG);

					$gridParameters=($style['id']=='grid') ? '?grid='.$wppizza_options['layout']['style_grid_columns'].'-'.$wppizza_options['layout']['style_grid_margins'].'-'.$wppizza_options['layout']['style_grid_full_width'].'' : '' ;

					if (file_exists( WPPIZZA_TEMPLATE_DIR . '/css/'.$css_filename.'')){
						/**stylesheet copied to template directory to keep settings**/
						$enqueue_styles_ident[$css_enqueue_ident] = wp_register_style($css_enqueue_ident, WPPIZZA_TEMPLATE_URI.'/css/'.$css_filename . $gridParameters, $css_dependency, WPPIZZA_VERSION);
					}else{
						$enqueue_styles_ident[$css_enqueue_ident] = wp_register_style($css_enqueue_ident, WPPIZZA_URL . 'css/'.$css_filename . $gridParameters , $css_dependency, WPPIZZA_VERSION);
					}

					/*
						set last style enqueued
					*/
					$dependency_last_style = $css_enqueue_ident;

				}
			}
			/**************************************
				register rtl css if required
			**************************************/
			if ( is_rtl() ) {
				$css_filename=''.WPPIZZA_SLUG.'.rtl.css';
				$css_enqueue_ident=''.WPPIZZA_SLUG.'-rtl';

				if (file_exists( WPPIZZA_TEMPLATE_DIR . '/css/'.$css_filename.'')){
					/**stylesheet copied to template directory to keep settings**/
					$enqueue_styles_ident[$css_enqueue_ident] = wp_register_style($css_enqueue_ident, WPPIZZA_TEMPLATE_URI.'/css/'.$css_filename, $dependency_last_style, WPPIZZA_VERSION);
				}else{
					$enqueue_styles_ident[$css_enqueue_ident] = wp_register_style($css_enqueue_ident, WPPIZZA_URL . 'css/'.$css_filename , $dependency_last_style, WPPIZZA_VERSION);
				}

				/*
					set last style enqueued
				*/
				$dependency_last_style = $css_enqueue_ident;
			}

			/*
				enqueue dashicons for cartimage under prices and empty image/photo placeholder
			*/
			$css_enqueue_ident='dashicons';
			$enqueue_styles_ident[$css_enqueue_ident] = wp_register_style($css_enqueue_ident, get_stylesheet_uri(), array($dependency_last_style));
			$dependency_last_style = $css_enqueue_ident;


			/************************************
				register custom css (CUSTOM FILE IN TEMPLATES) AFTER all other loaded css
				if we want to keep all the original css (including future changes) but only want to overwrite some lines ,
				add wppizza-custom.css to your template directory
			*************************************/
			$css_filename=''.WPPIZZA_SLUG.'.custom.css';
			if (file_exists( WPPIZZA_TEMPLATE_DIR . '/css/'.$css_filename.'')){
				$css_enqueue_ident=''.WPPIZZA_SLUG.'-custom';
				$enqueue_styles_ident[$css_enqueue_ident] = wp_register_style($css_enqueue_ident, WPPIZZA_TEMPLATE_URI.'/css/'.$css_filename, array($dependency_last_style), WPPIZZA_VERSION);
				$dependency_last_style = $css_enqueue_ident;
			}


			/************************************
				register custom css (WPPIZZA->LAYOUT) OPTION set in layout page
				and either load file (if possible)
				or inline if necessary
			*************************************/
			$css_filename=''.WPPIZZA_PREFIX.'.style.css';
			$custom_css_file_path = WPPIZZA_PATH.'css/'.$css_filename.'';
			/* mke sure to regenerate file if it does not exists but should (on plugin update for example) */
			if($wppizza_options['layout']['custom_css_type'] == 'file' && !file_exists($custom_css_file_path)){
				$custom_css = get_option(WPPIZZA_SLUG.'_custom_css', '');
				@file_put_contents($custom_css_file_path, $custom_css);
			}

			if($wppizza_options['layout']['custom_css_type'] == 'file' && file_exists($custom_css_file_path)){
				$css_enqueue_ident=''.WPPIZZA_SLUG.'-style';
				$enqueue_styles_ident[$css_enqueue_ident] = wp_register_style($css_enqueue_ident, WPPIZZA_URL . 'css/'.$css_filename , $dependency_last_style, $wppizza_options['layout']['custom_css_version']);
				$dependency_last_style = $css_enqueue_ident;
			}
			/** add custom style inline if we need to ***/
			if($wppizza_options['layout']['custom_css_type'] == 'inline'){
        		$custom_css = get_option(WPPIZZA_SLUG.'_custom_css', '');
        		if(!empty($custom_css)){
            		wp_add_inline_style( $dependency_last_style, $custom_css );
        		}
			}

			/***************************************
			*
			*	[enqueue all styles registered above]
			*
			***************************************/
			foreach($enqueue_styles_ident as $enqueue_id => $enqueue){
				wp_enqueue_style($enqueue_id);
			}
		}

		/*
			enqueue global styles
		*/
		$enqueue_styles_global = array();
		/**
			pretty photo css
		**/
		if($wppizza_options['layout']['prettyPhoto']){
			$css_filename=''.WPPIZZA_SLUG.'.prettyphoto.css';
			$css_enqueue_ident=''.WPPIZZA_SLUG.'-prettyphoto';
			$enqueue_styles_global[$css_enqueue_ident] = wp_register_style($css_enqueue_ident, WPPIZZA_URL.'css/'.$css_filename, array(), WPPIZZA_VERSION);
		}

		/**
			include spinner css/js on orderpage or globally (if cart increase) if enabled
		**/
		if(($is_orderpage && !empty($wppizza_options['order_settings']['order_page_quantity_change'])) || !empty($wppizza_options['order_settings']['cart_increase']) || $force_script_style_inclusion ){

			/* include spinner js */
			$ui = $wp_scripts->query('jquery-ui-spinner');

			/* make sure there's a style set */
			if(!empty($wppizza_options['order_settings']['order_page_quantity_change_style'])){
				$ui_style = $wppizza_options['order_settings']['order_page_quantity_change_style'];
				wp_enqueue_style('jquery-ui-'.$ui_style.'', "//ajax.googleapis.com/ajax/libs/jqueryui/".$ui->ver."/themes/".$ui_style."/jquery-ui.min.css", false, null);
			}

		}


		/***************************************
		*
		*	[enqueue any global styles registered above]
		*
		***************************************/
		foreach($enqueue_styles_global as $enqueue_id => $enqueue){
			wp_enqueue_style($enqueue_id);
		}

		/*****************************************************************************************************
		*
		*
		*	[register and enqueue js]
		*
		*
		******************************************************************************************************/
		/*main js**/
		$js_filename='scripts.min.js';
		$js_enqueue_ident=''.WPPIZZA_SLUG.'';
    	$js_enqueue[$js_enqueue_ident] = wp_register_script($js_enqueue_ident, WPPIZZA_URL.'js/'.$js_filename , array('jquery'), WPPIZZA_VERSION, apply_filters('wppizza_filter_js_in_footer', false));

		/*validation  - order page , login form user order history, or  if filter returns true*/
		$enqueue_validation = apply_filters('wppizza_filter_enqueue_validation', false);
		if( $is_orderpage || $is_orderhistory || !empty($enqueue_validation) ){
			/* validation */
			$js_filename='jquery.validate.min.js';
			$js_enqueue_ident=''.WPPIZZA_SLUG.'-validate';
			$js_enqueue[$js_enqueue_ident] = wp_register_script($js_enqueue_ident, WPPIZZA_URL.'js/validate/'.$js_filename , array(WPPIZZA_SLUG), WPPIZZA_VERSION, apply_filters('wppizza_filter_js_in_footer', false));

			/* validation methods */
			$js_filename='additional-methods.min.js';
			$js_enqueue_ident=''.WPPIZZA_SLUG.'-validate-methods';
			$js_enqueue[$js_enqueue_ident] = wp_register_script($js_enqueue_ident, WPPIZZA_URL.'js/validate/'.$js_filename , array(WPPIZZA_SLUG.'-validate'), WPPIZZA_VERSION, apply_filters('wppizza_filter_js_in_footer', false));

		}

		/**include spinner js on orderpage if enabled**/
		if($wppizza_options['order_settings']['order_page_quantity_change'] && ( $is_orderpage || $force_script_style_inclusion ) ){
			$js_enqueue["jquery-ui-spinner"] = "jquery-ui-spinner";
		}
		/**include spinner if cart increase by input is enabled **/
		if(!empty($wppizza_options['order_settings']['cart_increase'])){
			$js_enqueue["jquery-ui-spinner"] = "jquery-ui-spinner";
		}


    	/**pretty photo**/
    	if($wppizza_options['layout']['prettyPhoto']){
			$js_filename='jquery.prettyPhoto.js';
			$js_enqueue_ident=''.WPPIZZA_SLUG.'-prettyphoto';
			$js_enqueue[$js_enqueue_ident] = wp_register_script($js_enqueue_ident, WPPIZZA_URL.'js/'.$js_filename , array('jquery'), WPPIZZA_VERSION, apply_filters('wppizza_filter_js_in_footer', false));

			/*custom pretty photo*/
			$js_filename='jquery.prettyPhoto.custom.js';
			$js_enqueue_ident=''.WPPIZZA_SLUG.'-ppCustom';

    		/**copy js to template directory to edit settings (theme etc)**/
    		if (file_exists( WPPIZZA_TEMPLATE_DIR . '/js/'.$js_filename.'')){
				$js_enqueue[$js_enqueue_ident] = wp_register_style($js_enqueue_ident, WPPIZZA_TEMPLATE_URI.'/js/'.$js_filename, array('jquery'), WPPIZZA_VERSION, apply_filters('wppizza_filter_js_in_footer', false));
    		}else{
				$js_enqueue[$js_enqueue_ident] = wp_register_script($js_enqueue_ident, WPPIZZA_URL.'js/'.$js_filename.'.php?t='.$wppizza_options['layout']['prettyPhotoStyle'].'' , array('jquery'), WPPIZZA_VERSION, apply_filters('wppizza_filter_js_in_footer', false));
    		}
    	}


		/***************************************
		*
		*	[enqueue any scripts registered above]
		*
		***************************************/
		foreach($js_enqueue as $enqueue_id => $enqueue){
			wp_enqueue_script($enqueue_id);
		}

		/*****************************************************************************************************
		*
		*
		*	[localize js variables]
		*
		*
		******************************************************************************************************/
		$localize = array();

		/*****************************

			ajax url

		*****************************/
		/**in case force_ssl_admin is set */
		$ajaxUrl = admin_url('admin-ajax.php');
		if ( force_ssl_admin() &&  !is_ssl() ) {
			$ajaxUrl = set_url_scheme($ajaxUrl, 'http');
		}
		/*
			add to localized script
		*/
		$localize['ajaxurl'] = $ajaxUrl;

		/*****************************
			set current page/post id
		*****************************/
		if(!empty($post->ID)){
			$localize['pid'] = $post->ID;
		}

		/*****************************

			set flag to indicate we are on checkout page
			to not do any redirection for example
		*****************************/
		/** somewhat pretend it's an orderpage if it has an order page widget */
		$has_orderpage_widget = wppizza_has_orderpage_widget();

		if(wppizza_is_orderpage() || $has_orderpage_widget){
			$localize['isCheckout'] = 1;
			/*
				set flag to recalc on gateway change due to discounts or surcharges set
				 - orderpage only
			*/
			if(!empty(WPPIZZA()->gateways->must_recalculate)){
			$localize['reCalc'] = 1;
			}
		}

		/*****************************

			various alert messages

		*****************************/
		$messages['closed']=''.$wppizza_options['localization']['alert_closed'].'';
		if($wppizza_options['layout']['add_to_cart_on_title_click']){
			$messages['choosesize']=''.$wppizza_options['localization']['alert_choose_size'].'';
		}
		/* if delivery default */
		if($wppizza_options['order_settings']['order_pickup'] && $wppizza_options['order_settings']['order_pickup_alert'] && empty($wppizza_options['order_settings']['order_pickup_as_default']) ){
			$messages['pickup']=''.sprintf($wppizza_options['localization']['order_self_pickup_cartjs'], $wppizza_options['order_settings']['order_pickup_preparation_time']).'';
		}
		/* if pickup default (overrides above)*/
		if($wppizza_options['order_settings']['order_pickup'] && $wppizza_options['order_settings']['order_pickup_alert'] && !empty($wppizza_options['order_settings']['order_pickup_as_default']) ){
			$messages['pickup']=''.sprintf($wppizza_options['localization']['order_delivery_cartjs'], $wppizza_options['order_settings']['order_delivery_time']).'';
		}
		/*
			decode, escape, add messages
			to localized script
		*/
		foreach($messages as $jsmKey => $jsMessage){
			$messages[$jsmKey] = wppizza_decode_entities($jsMessage);
		}
		$localize['msg'] = $messages;

		/*
			localize cart
		*/
		$localize['crt'] = array();

		/*****************************
			befor cart refresh function
			filterable individually!
			add functions (names) to run before cart is being updated
		*****************************/
		$funcBeforeCartRefr = array();
		$funcBeforeCartRefr[]='wppizzaTotalsBefore';
		$funcBeforeCartRefr = apply_filters('wppizza_filter_js_before_cart_refresh_functions', $funcBeforeCartRefr);
		$funcBeforeCartRefr = array_keys(array_flip($funcBeforeCartRefr));/*flip to make unique, keys to just get the function name to sanitise things*/
		/*
			add to localized script
		*/
		$localize['funcBeforeCartRefr'] = $funcBeforeCartRefr;
		/*****************************
			cart refresh function
			filterable individually!
			add functions (names) to run when cart has been updated
		*****************************/
		$funcCartRefr = array();
		$funcCartRefr[]='wppizzaTotals';
		$funcCartRefr = apply_filters('wppizza_filter_js_cart_refresh_functions', $funcCartRefr);
		$funcCartRefr = array_keys(array_flip($funcCartRefr));/*flip to make unique, keys to just get the function name to sanitise things*/
		/*
			add to localized script
		*/
		$localize['funcCartRefr'] = $funcCartRefr;

		/*****************************
			order status changed functions
			filterable individually!
			add functions (names) to run when an order status was updated
			(can be used when adding order history via shortcode )
		*****************************/
		$funcStatusChanged = array();
		$funcStatusChanged = apply_filters('wppizza_filter_admin_js_status_changed_function', $funcStatusChanged);
		$funcStatusChanged = array_keys(array_flip($funcStatusChanged));/*flip to make unique, keys to just get the function name to sanitise things*/
		/*
			add to localized script if not empty
		*/
		if(!empty($funcStatusChanged)){
		$localize['fnOrderStatusChange'] = $funcStatusChanged;
		}


		/*****************************

			various localized options
			alert or confirm when changing from pickup to delivery (or vice versa)
		*****************************/
		if($wppizza_options['order_settings']['order_pickup'] && $wppizza_options['order_settings']['order_pickup_alert']){
			$localized_options['puAlrt'] = empty($wppizza_options['order_settings']['order_pickup_alert_confirm']) ? 1 : 2 ;
		}
		/** default to pickup ? */
		if(!empty($wppizza_options['order_settings']['order_pickup_as_default'])){
			$localized_options['puDef'] = 1;
		}
		/*
			add to localized script
		*/
		if(!empty($localized_options)){
			$localize['opt']=$localized_options;
		}

		/*****************************

			other miscellaneous
			localized options

		*****************************/
		$miscOptions = array();

		/**are we using a cache plugin ?**/
		if(apply_filters('wppizza_filter_using_cache_plugin', false)){
			$miscOptions['usingCache']=1;
		}

		/**are we using a confirmation form too ?**/
		if(!empty($wppizza_options['confirmation_form']['confirmation_form_enabled'])){
			$miscOptions['cfrm']=1;
		}

		/** quantity change in cart or enabled **/
		if(!empty($wppizza_options['order_settings']['cart_increase']) ){
			$miscOptions['ofqc']=1;
		}
		/**do we want to be able to still change quantities on order page**/
		if($wppizza_options['order_settings']['order_page_quantity_change'] && ( $is_orderpage || $force_script_style_inclusion ) ){
			$miscOptions['ofqc']=1;
		}

		/** admin order polling time **/
		$miscOptions['aopt'] = $wppizza_options['settings']['admin_order_history_polling_time'] ;

		/** forceing pickup toggle to be visible when closed should also bypass js isOpen check when toggeling**/
		$force_pickup_toggle = apply_filters('wppizza_filter_force_pickup_toggle_display', false);
		if(!empty($force_pickup_toggle)){
		$miscOptions['fpt'] = 1 ;
		}

		/** feedback add to cart**/
		if($wppizza_options['layout']['jquery_fb_add_to_cart']!=''){
			$miscOptions['itm']['fbatc'] = $wppizza_options['localization']['jquery_fb_add_to_cart_info'];
			$miscOptions['itm']['fbatcms'] = $wppizza_options['layout']['jquery_fb_add_to_cart_ms'];
		}

		/**prettify js alerts localization strings**/
		if(!empty($wppizza_options['layout']['prettify_js_alerts'])){
			$miscOptions['pjsa'] = array();
			$miscOptions['pjsa']['h1'] = get_bloginfo( 'name' );
			$miscOptions['pjsa']['ok'] = __('OK');
		}

		/*
			add (merge) miscellaneous options to localized script
		*/
		$localize = array_merge($localize, $miscOptions);

		/*****************************
			allow adding of veriables for
			extending plugins
			filterable individually!
		*****************************/
		$jsExtend = array();
		$jsExtend = apply_filters('wppizza_filter_js_extend', $jsExtend);
		/*
			add to localized script
		*/
		$localize['extend'] = $jsExtend;


		/*****************************
			allow fitering
			of all
			localized js
			variables
		*****************************/
		$localize = apply_filters('wppizza_filter_js_localize', $localize);


		/*****************************
			allow fitering of selected gateway
			localized js on checkout
			@since 3.7
		*****************************/
		if(wppizza_is_checkout()){
			$selected_gateway_ident = WPPIZZA()->session->get_selected_gateway();
			$localize = apply_filters('wppizza_gateways_inline_localize_'.$selected_gateway_ident.'', $localize, $selected_gateway_ident);
		}

		/*****************************************************************************


			localize it all


		*****************************************************************************/
		wp_localize_script( WPPIZZA_SLUG, WPPIZZA_SLUG, $localize );

	}

}
/***************************************************************
*
*	[ini]
*
***************************************************************/
$WPPIZZA_SCRIPTS_AND_STYLES = new WPPIZZA_SCRIPTS_AND_STYLES();
?>