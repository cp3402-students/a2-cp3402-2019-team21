<?php if ( ! defined( 'ABSPATH' ) ) exit;/*Exit if accessed directly*/ ?>
<?php
	/****************************************************
	*
	*	update options for updates from  < 3.0
	*
	*****************************************************/
		$current_options = get_option(WPPIZZA_SLUG);

		/**
			for updates from versions <3.x
			lets update manually per section
			it's just safer for such a big update
		**/
		$options_update = array();

		/** plugin data **/
	    $options_update['plugin_data'] = array();
	    $options_update['plugin_data']['version'] = $default_options['plugin_data']['version'];
	    $options_update['plugin_data']['upgrade'] = $default_options['plugin_data']['upgrade'];
	    $options_update['plugin_data']['nag_notice'] = $default_options['plugin_data']['nag_notice'];

		/** templates_apply **/
		$options_update['templates_apply'] = array();
		$options_update['templates_apply'] = $default_options['templates_apply'];

		/** capabilities add value as key**/
		$options_update['admin_access_caps'] = array();
		foreach($current_options['admin_access_caps'] as $k=>$v){
			$options_update['admin_access_caps'][$k] = array_combine($v, $v);
		}

		/** settings **/
		$options_update['settings'] = array();
		$options_update['settings']['post_single_template'] = !empty($current_options['plugin_data']['post_single_template']) ? $current_options['plugin_data']['post_single_template'] : '' ;
		$options_update['settings']['using_cache_plugin'] = $current_options['plugin_data']['using_cache_plugin'];
		$options_update['settings']['ssl_on_checkout'] = $current_options['plugin_data']['ssl_on_checkout'];
		$options_update['settings']['js_in_footer'] = $current_options['plugin_data']['js_in_footer'];
		$options_update['settings']['admin_order_history_max_results'] = $current_options['plugin_data']['admin_order_history_max_results'];
		$options_update['settings']['admin_order_history_polling_time'] = $current_options['plugin_data']['admin_order_history_polling_time'];
		$options_update['settings']['admin_order_history_polling_auto'] = $current_options['plugin_data']['admin_order_history_polling_auto'];
		$options_update['settings']['sku_enable'] = $current_options['plugin_data']['sku_enable'];
		$options_update['settings']['sku_search'] = $current_options['plugin_data']['sku_search'];
		$options_update['settings']['sku_search_partial'] = $current_options['plugin_data']['sku_search_partial'];
		$options_update['settings']['sku_search_length'] = $default_options['settings']['sku_search_length'];/* new */
		$options_update['settings']['always_load_all_scripts_and_styles'] = $current_options['plugin_data']['always_load_all_scripts_and_styles'];
		$options_update['settings']['dequeue_scripts'] = $current_options['plugin_data']['dequeue_scripts'];
		$options_update['settings']['category_parent_page'] = $current_options['plugin_data']['category_parent_page'];
		$options_update['settings']['single_item_permalink_rewrite'] = $current_options['plugin_data']['single_item_permalink_rewrite'];
		$options_update['settings']['search_include']  = $current_options['plugin_data']['search_include'];
		$options_update['settings']['smtp_enable'] = $current_options['plugin_data']['smtp_enable'];
		$options_update['settings']['smtp_host'] = $current_options['plugin_data']['smtp_host'];
		$options_update['settings']['smtp_port'] = $current_options['plugin_data']['smtp_port'];
		$options_update['settings']['smtp_encryption'] = $current_options['plugin_data']['smtp_encryption'];
		$options_update['settings']['smtp_authentication'] = $current_options['plugin_data']['smtp_authentication'];
		$options_update['settings']['smtp_username'] = $current_options['plugin_data']['smtp_username'];
		$options_update['settings']['smtp_password'] = $current_options['plugin_data']['smtp_password'];
		$options_update['settings']['smtp_debug'] = $current_options['plugin_data']['smtp_debug'];
		$options_update['settings']['wp_multisite_session_per_site']  = $current_options['plugin_data']['wp_multisite_session_per_site'];
		$options_update['settings']['wp_multisite_reports_all_sites']  = $current_options['plugin_data']['wp_multisite_reports_all_sites'];
		$options_update['settings']['wp_multisite_order_history_all_sites']  = $current_options['plugin_data']['wp_multisite_order_history_all_sites'];

		/** sizes **/
		$options_update['sizes'] = $current_options['sizes'];

		/** additives **/
		$options_update['additives'] = $current_options['additives'];


		/** layout **/
		$options_update['layout'] = array();
		$options_update['layout']['category_sort_hierarchy'] = $current_options['layout']['category_sort_hierarchy'];
		$options_update['layout']['sku_replaces_size'] = $current_options['layout']['sku_replaces_size'];
		$options_update['layout']['sku_display'] = $current_options['layout']['sku_display'];
		$options_update['layout']['items_per_loop'] = $current_options['layout']['items_per_loop'];
		$options_update['layout']['disable_online_order'] = $current_options['layout']['disable_online_order'];
		$options_update['layout']['style'] = $current_options['layout']['style'];
		$options_update['layout']['style_grid_columns'] = $current_options['layout']['style_grid_columns'];
		$options_update['layout']['style_grid_margins'] = $current_options['layout']['style_grid_margins'];
		$options_update['layout']['style_grid_full_width'] = $current_options['layout']['style_grid_full_width'];
		$options_update['layout']['include_css'] = $current_options['layout']['include_css'];
		$options_update['layout']['load_additional_styles'] = $default_options['layout']['load_additional_styles'];/* new */
		$options_update['layout']['css_priority'] = $current_options['layout']['css_priority'];
		$options_update['layout']['custom_css_version'] = $default_options['layout']['custom_css_version'];/* new */
		$options_update['layout']['custom_css_type'] = $default_options['layout']['custom_css_type'];/* new */
		$options_update['layout']['items_group_sort_print_by_category'] = $current_options['layout']['items_group_sort_print_by_category'];
		$options_update['layout']['items_sort_order'] = $current_options['layout']['items_sort_order'];
		$options_update['layout']['items_sort_orderby'] = $current_options['layout']['items_sort_orderby'];
		$options_update['layout']['items_category_hierarchy'] = $current_options['layout']['items_category_hierarchy'];
		$options_update['layout']['items_category_hierarchy_cart'] = $current_options['layout']['items_category_hierarchy_cart'];
		$options_update['layout']['items_category_hierarchy_email_style'] = $default_options['layout']['items_category_hierarchy_email_style'];/* new */
		$options_update['layout']['items_category_separator'] = $current_options['layout']['items_category_separator'];
		$options_update['layout']['placeholder_img'] = $current_options['layout']['placeholder_img'];
		$options_update['layout']['prettyPhoto'] = $current_options['layout']['prettyPhoto'];
		$options_update['layout']['prettyPhotoStyle'] = $current_options['layout']['prettyPhotoStyle'];
		$options_update['layout']['add_to_cart_on_title_click'] = $current_options['layout']['add_to_cart_on_title_click'];
		$options_update['layout']['jquery_fb_add_to_cart'] = $current_options['layout']['jquery_fb_add_to_cart'];
		$options_update['layout']['jquery_fb_add_to_cart_ms'] = $current_options['layout']['jquery_fb_add_to_cart_ms'];
		$options_update['layout']['suppress_loop_headers'] = $current_options['layout']['suppress_loop_headers'];
		$options_update['layout']['hide_single_pricetier'] = $current_options['layout']['hide_single_pricetier'];
		$options_update['layout']['gateway_select_as_dropdown'] = $current_options['gateways']['gateway_select_as_dropdown'];/* moved */
		$options_update['layout']['minicart_max_width_active'] = $current_options['layout']['minicart_max_width_active'];
		$options_update['layout']['minicart_elm_padding_top'] = $current_options['layout']['minicart_elm_padding_top'];
		$options_update['layout']['minicart_elm_padding_selector'] = $current_options['layout']['minicart_elm_padding_selector'];
		$options_update['layout']['minicart_add_to_element'] = $current_options['layout']['minicart_add_to_element'];
		$options_update['layout']['minicart_always_shown'] = $current_options['layout']['minicart_always_shown'];
		$options_update['layout']['minicart_viewcart'] = $current_options['layout']['minicart_viewcart'];
		$options_update['layout']['minicart_checkout'] = $default_options['layout']['minicart_checkout'];/* new */
		$options_update['layout']['minicart_itemcount'] = $default_options['layout']['minicart_itemcount'];/* new */
		$options_update['layout']['items_blog_hierarchy'] = $current_options['layout']['items_blog_hierarchy'];
		$options_update['layout']['items_blog_hierarchy_cart'] = $current_options['layout']['items_blog_hierarchy_cart'];
		$options_update['layout']['items_per_loop'] = $current_options['layout']['items_per_loop'];
		$options_update['layout']['items_blog_hierarchy_email_style'] = $default_options['layout']['items_blog_hierarchy_email_style'];/* new */


		/** localization . add new or use existing**/
		$options_update['localization'] = array();
		foreach($default_options['localization'] as $key=>$val){
			/* use exiting or new if not exist */
			$value = (isset($current_options['localization'][$key]['lbl'])) ? $current_options['localization'][$key]['lbl'] : $val ;
			$options_update['localization'][$key] = $value;
		}


		/** order_settings **/
		$options_update['order_settings'] = array();
		$options_update['order_settings']['currency'] = $current_options['order']['currency'];
		$options_update['order_settings']['currency_symbol'] = $current_options['order']['currency_symbol'];
		$options_update['order_settings']['orderpage'] = $current_options['order']['orderpage'];
		$options_update['order_settings']['orderpage_exclude'] = $current_options['order']['orderpage_exclude'];
		$options_update['order_settings']['append_internal_id_to_transaction_id'] = $current_options['order']['append_internal_id_to_transaction_id'];
		$options_update['order_settings']['gateway_showorder_on_thankyou'] = $current_options['gateways']['gateway_showorder_on_thankyou'];/* moved */
		$options_update['order_settings']['delivery_selected'] = $current_options['order']['delivery_selected'];
		$options_update['order_settings']['order_min_for_delivery'] = $current_options['order']['order_min_for_delivery'];
		$options_update['order_settings']['order_delivery_time'] = $default_options['order_settings']['order_delivery_time'];/* new */
		$options_update['order_settings']['order_min_for_pickup'] = $current_options['order']['order_min_for_pickup'];
		$options_update['order_settings']['order_min_on_totals'] = $current_options['order']['order_min_on_totals'];
		$options_update['order_settings']['delivery'] = $current_options['order']['delivery'];
		$options_update['order_settings']['delivery_calculation_exclude_item'] = !empty($current_options['order']['delivery_calculation_exclude_item']) ? $current_options['order']['delivery_calculation_exclude_item'] : array();
		$options_update['order_settings']['delivery_calculation_exclude_cat'] = !empty($current_options['order']['delivery_calculation_exclude_cat']) ? $current_options['order']['delivery_calculation_exclude_cat'] : array();
		$options_update['order_settings']['item_tax'] = $current_options['order']['item_tax'];
		$options_update['order_settings']['item_tax_alt'] = $current_options['order']['item_tax_alt'];
		$options_update['order_settings']['taxes_round_natural'] = $current_options['order']['taxes_round_natural'];
		$options_update['order_settings']['taxes_included'] = $current_options['order']['taxes_included'];
		$options_update['order_settings']['shipping_tax'] = $current_options['order']['shipping_tax'];
		$options_update['order_settings']['shipping_tax_rate'] = $current_options['order']['shipping_tax_rate'];
		$options_update['order_settings']['discount_selected'] = $current_options['order']['discount_selected'];
		$options_update['order_settings']['discounts'] = $current_options['order']['discounts'];
		$options_update['order_settings']['discount_calculation_exclude_item'] = $current_options['order']['discount_calculation_exclude_item'];
		$options_update['order_settings']['discount_calculation_exclude_cat'] = $current_options['order']['discount_calculation_exclude_cat'];
		$options_update['order_settings']['order_pickup'] = $current_options['order']['order_pickup'];
		$options_update['order_settings']['order_pickup_alert'] = $current_options['order']['order_pickup_alert'];
		$options_update['order_settings']['order_pickup_alert_confirm'] = $current_options['order']['order_pickup_alert_confirm'];
		$options_update['order_settings']['order_pickup_as_default'] = $current_options['order']['order_pickup_as_default'];
		$options_update['order_settings']['order_pickup_toggled'] = $default_options['order_settings']['order_pickup_toggled'];/* new */
		$options_update['order_settings']['order_pickup_discount'] = $current_options['order']['order_pickup_discount'];
		$options_update['order_settings']['order_pickup_preparation_time'] = $default_options['order_settings']['order_pickup_preparation_time'];/* new */
		$options_update['order_settings']['order_pickup_display_location'] = $current_options['order']['order_pickup_display_location'];
		$options_update['order_settings']['cart_increase'] = $current_options['layout']['cart_increase'];/* moved */
		$options_update['order_settings']['order_page_quantity_change'] = $current_options['layout']['order_page_quantity_change'];/* moved */
		$options_update['order_settings']['empty_cart_button'] = $current_options['layout']['empty_cart_button'];/* moved */
		$options_update['order_settings']['repurchase'] = $default_options['order_settings']['repurchase'];/* new */
		$options_update['order_settings']['order_email_to'] = $current_options['order']['order_email_to'];
		$options_update['order_settings']['order_email_bcc'] = !empty($current_options['order']['order_email_bcc']) ? $current_options['order']['order_email_bcc'] : array();
		$options_update['order_settings']['order_email_attachments'] = !empty($current_options['order']['order_email_attachments']) ? $current_options['order']['order_email_attachments'] : array();
		$options_update['order_settings']['order_email_from'] = $current_options['order']['order_email_from'] ;
		$options_update['order_settings']['order_email_from_name'] = $current_options['order']['order_email_from_name'] ;
		$options_update['order_settings']['dmarc_nag_off'] = $current_options['order']['dmarc_nag_off'];


		/** order_form , update /add as required **/
		$options_update['order_form'] = array();
		foreach($default_options['order_form'] as $key=>$val){
			/* overwrite with existing where needed */
			foreach($current_options['order_form'] as $fKey=>$fVal){
				if($fVal['key'] == $key){
					$options_update['order_form'][$key] = array();
					$options_update['order_form'][$key]['sort'] = $fVal['sort'];
					$options_update['order_form'][$key]['key'] = $fVal['key'];
					$options_update['order_form'][$key]['lbl'] = $fVal['lbl'];
					$options_update['order_form'][$key]['value'] = $fVal['value'];
					$options_update['order_form'][$key]['type'] = $fVal['type'];
					$options_update['order_form'][$key]['enabled'] = $fVal['enabled'];
					$options_update['order_form'][$key]['required'] = $fVal['required'];
					$options_update['order_form'][$key]['required_on_pickup'] = $fVal['required_on_pickup'];
					$options_update['order_form'][$key]['prefill'] = $fVal['prefill'];
					$options_update['order_form'][$key]['onregister'] = $fVal['onregister'];
					$options_update['order_form'][$key]['add_to_subject_line'] = $fVal['add_to_subject_line'];
					$options_update['order_form'][$key]['placeholder'] = $fVal['placeholder'];
					$options_update['order_form'][$key]['validation'] = $val['validation'];
				}
			}
		}
		/** sort to save in right order **/
		asort($options_update['order_form']);


		/** confirmation_form **/
		$options_update['confirmation_form'] = array();
		$options_update['confirmation_form']['confirmation_form_enabled'] = $current_options['confirmation_form_enabled'];/* moved */
		$options_update['confirmation_form']['confirmation_form_amend_order_link'] = $current_options['confirmation_form_amend_order_link'];/* moved */
		$options_update['confirmation_form']['formfields'] = array();/* moved */
		foreach($default_options['confirmation_form']['formfields'] as $key=>$val){

			$thisKey = $val['key'];

			$options_update['confirmation_form']['formfields'][$key] = array();
			$options_update['confirmation_form']['formfields'][$key]['sort'] = $val['sort'];
			$options_update['confirmation_form']['formfields'][$key]['key'] = $val['key'];
			$options_update['confirmation_form']['formfields'][$key]['lbl'] = $val['lbl'];
			$options_update['confirmation_form']['formfields'][$key]['value'] = $val['value'];
			$options_update['confirmation_form']['formfields'][$key]['type'] = $val['type'];
			$options_update['confirmation_form']['formfields'][$key]['enabled'] = $val['enabled'];
			$options_update['confirmation_form']['formfields'][$key]['required'] = $val['required'];
			$options_update['confirmation_form']['formfields'][$key]['placeholder'] = $val['placeholder'];

			/* overwrite with existing where needed/available */
			foreach($current_options['confirmation_form'] as $fKey=>$fVal){
				if($fVal['key'] == $thisKey ){
					$options_update['confirmation_form']['formfields'][$key]['sort'] = $fVal['sort'];
					$options_update['confirmation_form']['formfields'][$key]['lbl'] = $fVal['lbl'];
					$options_update['confirmation_form']['formfields'][$key]['type'] = $fVal['type'];
					$options_update['confirmation_form']['formfields'][$key]['enabled'] = $fVal['enabled'];
					$options_update['confirmation_form']['formfields'][$key]['required'] = $fVal['required'];
					$options_update['confirmation_form']['formfields'][$key]['value'] = $fVal['value'];
				}

			}
		}
		$options_update['confirmation_form']['localization'] = array();/* moved */
		foreach($default_options['confirmation_form']['localization'] as $key=>$val){
			$options_update['confirmation_form']['localization'][$key] = isset($current_options['localization_confirmation_form']['lbl']) ? $current_options['localization_confirmation_form']['lbl'] : $val;
		}


		/** openingtimes **/
		$options_update['openingtimes'] = array();
		$options_update['openingtimes']['close_shop_now'] = $current_options['globals']['close_shop_now'];/* moved */
		$options_update['openingtimes']['opening_times_standard'] = $current_options['opening_times_standard'];/* moved */
		$options_update['openingtimes']['opening_times_custom'] = !empty($current_options['opening_times_custom'])  ? $current_options['opening_times_custom'] : array() ;/* moved */
		$options_update['openingtimes']['times_closed_standard'] = !empty($current_options['times_closed_standard']) ? $current_options['times_closed_standard'] : array();/* moved */


		/** opening_times_format **/
		$options_update['opening_times_format'] = $current_options['opening_times_format'];


		/** prices_format **/
		$options_update['prices_format'] = array();
		$options_update['prices_format']['hide_prices'] = $current_options['layout']['hide_prices'] ; /* moved */
		$options_update['prices_format']['hide_decimals'] = $current_options['layout']['hide_decimals'] ; /* moved */
		$options_update['prices_format']['show_currency_with_price'] = $current_options['layout']['show_currency_with_price'] ; /* moved */
		$options_update['prices_format']['hide_item_currency_symbol'] = $current_options['layout']['hide_item_currency_symbol'] ; /* moved */
		$options_update['prices_format']['currency_symbol_left'] = $current_options['layout']['currency_symbol_left'] ; /* moved */
		$options_update['prices_format']['currency_symbol_position'] = $current_options['layout']['currency_symbol_position'] ; /* moved */
		$options_update['prices_format']['currency_symbol_spacing'] = $default_options['prices_format']['currency_symbol_spacing'] ; /* new */


		/** tools **/
		$options_update['tools'] = array();
		$options_update['tools']['disable_emails'] = $current_options['tools']['disable_emails'] ;


		/** cron **/
		$options_update['cron'] = array();
		$options_update['cron']['days_delete'] = $current_options['cron']['days_delete'] ;
		$options_update['cron']['failed_delete'] = $current_options['cron']['failed_delete'] ;
		$options_update['cron']['schedule'] = $current_options['cron']['schedule'] ;

?>