<?php if ( ! defined( 'ABSPATH' ) ) exit;/*Exit if accessed directly*/ ?>
<?php
	$current_options = get_option(WPPIZZA_SLUG);
	/**************************
		remove removed options
	**************************/
	$update_options = wppizza_array_intersect_assoc_recursive($current_options, $default_options);

	/**************************
		get newly added options
	**************************/
	$added_options = wppizza_recursive_compare_options($default_options, $current_options);

	/**************************
	 merge new options into array that has old options removed
	**************************/
	$update_options = array_merge_recursive($update_options, $added_options);

	/**************************
	 	protected options that should
	 	always be saved/updated as they have been saved/set
	 	(these have no default options set as such so the compare above would be empty)
	 	this could be done better one day
	**************************/
	$protected_options = array();
	/* sizes - keep as set */
	$protected_options['sizes'] = $current_options['sizes'];
	/* additives - keep as set */
	$protected_options['additives'] = $current_options['additives'];
	/* gateways - keep as set */
	$protected_options['gateways'] = $current_options['gateways'];
	/* category_sort_hierarchy - keep as set */
	$protected_options['layout']['category_sort_hierarchy'] = $current_options['layout']['category_sort_hierarchy'];

	/* custom opening times / custom closing times - keep as set */
	/*
		fix, as yet unknown reason, why opening_times_custom get duplicated when updating, so as a temp fix, simply unset
		to be investigated more thoroughly one day
	*/
	unset($update_options['openingtimes']['opening_times_custom']);
	$protected_options['openingtimes']['opening_times_custom'] = $current_options['openingtimes']['opening_times_custom'];
	$protected_options['openingtimes']['times_closed_standard'] = $current_options['openingtimes']['times_closed_standard'];

	/*
		recipients, bcc and attachmnts
	*/
	$protected_options['order_settings']['order_email_bcc'] = $current_options['order_settings']['order_email_bcc'];
	$protected_options['order_settings']['order_email_attachments'] = $current_options['order_settings']['order_email_attachments'];

	/**************************
	 merge protected into update
	***************************/
	$update_options = array_merge_recursive($update_options, $protected_options);


	/*
		order settings delivery and discount exclude items and categories
	*/
	$update_options['order_settings']['delivery_calculation_exclude_item'] = $current_options['order_settings']['delivery_calculation_exclude_item'];
	$update_options['order_settings']['delivery_calculation_exclude_cat'] = $current_options['order_settings']['delivery_calculation_exclude_cat'];
	$update_options['order_settings']['discount_calculation_exclude_item'] = $current_options['order_settings']['discount_calculation_exclude_item'];
	$update_options['order_settings']['discount_calculation_exclude_cat'] = $current_options['order_settings']['discount_calculation_exclude_cat'];


	/**************************
	 make sure to keep set values
	 for the following, ignoring
	 any recursive merges etc
	***************************/
	/* recipients*/
	$update_options['order_settings']['order_email_to'] = $current_options['order_settings']['order_email_to'];

	/* order form - just keep as set*/
	$update_options['order_form'] = $current_options['order_form'];

	/* templates apply - just keep as set */
	$update_options['templates_apply'] = $current_options['templates_apply'];

	/***************************
		set distinct plugin data
		always set/save this from default to
		set current plugin version etc
		for update checks
	***************************/
	$update_options['plugin_data'] = $default_options['plugin_data'];

	/**************************
	 get removed options.
	 currently not in use anywhere, but might be useful one day
	***************************/
	$removed_options = wppizza_recursive_compare_options($current_options, $update_options);



?>