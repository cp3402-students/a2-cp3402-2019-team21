<?php
/**
* WPPIZZA_SESSIONS Class
*
* @package     WPPIZZA
* @subpackage  Sessions
* @copyright   Copyright (c) 2015, Oliver Bach
* @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
* @since       3.0
*
*/
if ( ! defined( 'ABSPATH' ) ) exit;/*Exit if accessed directly*/


/************************************************************************************************************************
*
*
*	WPPIZZA_SESSIONS
*
*
************************************************************************************************************************/
class WPPIZZA_SESSIONS{

	/**
	 * Holds our session data
	 *
	 * @var array
	 * @access private
	 * @since 1.5
	 */
	public $session_key_cart;/* session key cart - typically "wppizza" or "wppizza+blogid" if multisite*/
	public $session_key_userdata;/* session key userdata */
	public $session_order;/* set to run cart calculations only once when required mutliple times on page */


/***********************************
*
*	CONSTRUCT
*
***********************************/
	function __construct() {
		/**set idents**/
		add_action('init', array( $this, 'set_session_idents'), 3);
		/**start session if not already done so*/
		add_action('init', array( $this, 'maybe_init_sessions'), 4);/*needed for admin AND frontend, lets set a reasonably high priority too***/
	}


/*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*
*
*
*
*		[sort items in cart and calculate values
*		(discounts etc etc , THE MAIN THING REALLY THAT HOLDS IT ALL TOGETHER)]
*
*
*
*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*/
	function sort_and_calculate_cart($is_checkout = null, $recalculate_all = false){
		static $calc = 0, $filter_param = array();
		global $wppizza_options, $blog_id;

		/**
			in case calulations have already been run and we would otherwise
			calculate the same thing multiple times,
			just return var as set when calculated the first time
			however, DO recalculate when adding by looping through items (for example when re-purchasing a whole order)
		**/

		if( $calc === 0 || !empty($recalculate_all) ){

			/**
				advance static counter
			**/
			$calc++;
			/**
				items in cart as set in session.
				just for convenience really put in a shorter var
			**/
			$session = $_SESSION[$this->session_key_cart];
			#*\*/*\*/*\*/*\*/*\*/*\*/*\*/*\*/*\*/*\*/*\*/*\*/*#
			#	allow filtering session before grouping, formatting , taxcalculations etc
			#*\*/*\*/*\*/*\*/*\*/*\*/*\*/*\*/*\*/*\*/*\*/*\*/*#
			$session = apply_filters('wppizza_fltr_session', $session);

			/*
				items
			*/
			$session_items = !empty($session['items']) ? $session['items'] : array();


			/**
				check if we are on checkout page
				unless this has already been
				specifically set (usually by ajax calls)
			**/
			if(!isset($is_checkout)){
				$is_checkout = wppizza_is_orderpage() ? true : false;
			}

			/**
				selected gateway
				just for convenience really put in a shorter var
			**/
			$gateway_selected = $_SESSION[$this->session_key_userdata][''.WPPIZZA_SLUG.'_gateway_selected'];
			$gateway_surcharges = !empty(WPPIZZA()->gateways->gwobjects->$gateway_selected->surcharges) ? WPPIZZA()->gateways->gwobjects->$gateway_selected->surcharges : false;
			$gateway_discounts = !empty(WPPIZZA()->gateways->gwobjects->$gateway_selected->discounts) ? WPPIZZA()->gateways->gwobjects->$gateway_selected->discounts : false;
			$gateway_min_order_value = !empty(WPPIZZA()->gateways->gwobjects->$gateway_selected->min_order_value) ? WPPIZZA()->gateways->gwobjects->$gateway_selected->min_order_value : false;

			/**
				flattend cart items array using only first item in group
				for easy sorting
			**/

			$groupedItems = array();
			foreach($session_items as $group_key=>$grouped_items){
				$groupedItems[$group_key]['sort'] = $grouped_items[0]['sortname'];
				$groupedItems[$group_key]['size'] = $grouped_items[0]['size'];
			}

			/**
				might make that an option somewhere at some point.
				for now, use constant for NOT sorting items alphabetically, but the way they were added
			**/
			if(!WPPIZZA_SORT_ITEMS_AS_ADDED){
				asort($groupedItems);
			}


			#*\*/*\*/*\*/*\*/*\*/*\*/*\*/*\*/*\*/*\*/*\*/*\*/*#
			#	allow filtering cart_items
			#*\*/*\*/*\*/*\*/*\*/*\*/*\*/*\*/*\*/*\*/*\*/*\*/*#
			$groupedItems = apply_filters('wppizza_fltr_cart_items', $groupedItems, $session_items);

			/****************************************************************************************

				session order, counting same items,  calculating discounts, delivery charges etc etc

			****************************************************************************************/
			/**
				some vars for convenience
			**/
			/* currency symbol */
			$this_order_currency = !empty($wppizza_options['order_settings']['currency_symbol']) ? wppizza_decode_entities($wppizza_options['order_settings']['currency_symbol']) : '';
			/* taxes included in prices ? yes/no */
			$this_order_tax_included = !empty($wppizza_options['order_settings']['taxes_included']) ? true : false;
			/* tax rounding natural ? yes/no */
			$tax_round_natural = !empty($wppizza_options['order_settings']['taxes_round_natural']) ? true : false;
			/* items and cats excluded from discount calculation*/
			$discount_exclude_items = $wppizza_options['order_settings']['discount_calculation_exclude_item'];
			$discount_exclude_cats = $wppizza_options['order_settings']['discount_calculation_exclude_cat'];
			/* items and cats excluded from delivery calculation*/
			$delivery_exclude_items = $wppizza_options['order_settings']['delivery_calculation_exclude_item'];
			$delivery_exclude_cats = $wppizza_options['order_settings']['delivery_calculation_exclude_cat'];
			/* set to self pickup or not */
			$this_order_self_pickup = (isset($_SESSION[$this->session_key_cart]['self_pickup'])) ? $_SESSION[$this->session_key_cart]['self_pickup'] : false ;/*bool*/
			/* sum of included taxes */
			$this_order_taxes_included = 0;/* to calculate */
			/* sum of taxes to add */
			$this_order_taxes_to_add = 0;/* to calculate */

			/*
				taxes @ main and alt taxrate
			*/

			$this_order_taxes_at_rate = array();

			$has_main_tax = false ;
			$this_order_taxes_at_rate['main'] = array();
			$this_order_taxes_at_rate['main']['rate'] = !empty($wppizza_options['order_settings']['item_tax']) ? $wppizza_options['order_settings']['item_tax'] : 0; ;
			$this_order_taxes_at_rate['main']['included'] = 0 ;/* to calculate */
			$this_order_taxes_at_rate['main']['to_add'] = 0 ;/* to calculate */
			$this_order_taxes_at_rate['main']['item_at_taxrate'] = 0 ;/* to calculate */

			$has_alt_tax = false ;
			$this_order_taxes_at_rate['alt'] = array();
			$this_order_taxes_at_rate['alt']['rate'] = !empty($wppizza_options['order_settings']['item_tax_alt']) ? $wppizza_options['order_settings']['item_tax_alt'] : 0;
			$this_order_taxes_at_rate['alt']['included'] = 0 ;/* to calculate */
			$this_order_taxes_at_rate['alt']['to_add'] = 0 ;/* to calculate */
			$this_order_taxes_at_rate['alt']['item_at_taxrate'] = 0 ;/* to calculate */

			/* how many rates are there, start with what we have, subtract if not set further down */
			$number_of_taxrates = count($this_order_taxes_at_rate);


			/* shipping taxes, to be added to taxes above */
			$this_order_taxes_shipping = 0;/* to calculate */
			/* sum of delivery charges */
			$this_order_delivery_charges = 0;/* to calculate */
			/* sum of handling charges */
			$this_order_handling_charges = 0;/* to calculate */
			/* sum of discounts */
			$this_order_discount_selected = $wppizza_options['order_settings']['discount_selected'];
			/* sum of discounts */
			$this_order_discount = 0;/* to calculate */
			/* sum of taxrate all items to be devided by number of items*/
			$this_order_taxrates_total_items = 0;/* to calculate, used to determine average taxrate to be able to calculate discount taxes pro rata */
			/* average taxrate per item */
			$this_order_taxrates_average_per_item = 0;
			/* get total price of items before any exclusions */
			$this_order_total_price_items = 0;/* to calculate */
			/* get total price of items eligible for discount calculations */
			$this_order_total_price_items_for_discount = 0;/* to calculate */
			/* get total price of items eligible for delivery calculations */
			$this_order_total_price_items_for_delivery = 0;/* to calculate */
			/* number of items. if 0 disable checkout */
			$this_order_number_of_items = 0;/* to calculate */
			/* number of items after excluded ones (when calculating discounts for example) */
			$this_order_number_of_items_afer_exclusions = 0;/* to calculate */


			/******************************************************************************************************************

				ini session array

			******************************************************************************************************************/
			$order_session 				= array(); /* ini array */
			$order_session['info'] 		= array(); /* ini array general variables */
			$order_session['param'] 	= array(); /* ini array settings variables */
			$order_session['checkout_parameters'] 	= array(); /* ini array calculated session variables  */
			$order_session['items'] 	= array(); /* ini array menu items variables*/
			$order_session['summary'] 	= array(); /* ini array summary variables*/

			/******************************************************************************************************************

				ini running current values array - to pass on to filters

			******************************************************************************************************************/
			$filter_param 										= array(); /* ini array */
			$filter_param['number_of_items'] 					= $this_order_number_of_items ;
			$filter_param['number_of_items_afer_exclusions'] 	= $this_order_number_of_items_afer_exclusions ;
			$filter_param['total_price_items'] 					= $this_order_total_price_items ;
			$filter_param['total_price_items_for_discount'] 	= $this_order_total_price_items_for_discount ;
			$filter_param['total_price_items_for_delivery'] 	= $this_order_total_price_items_for_delivery ;

			/*************************************************************
			*
			*
			*	some general variables
			*	to save to db in order_ini
			*
			*
			*************************************************************/
			$order_session['info']['plugin_version'] = WPPIZZA_VERSION ;/*might come in handy one day if we ever have to update the db data*/

			/******************************************************************************************************
			*
			*	ORDER SETTINGS PARAMETERS AT TIME OF ORDER
			*	could - in theory - be used to recalculate the order based on the order settings
			*	at time of order captured here. Other than the currency, nothing from this array is really used in
			*	any output, but might come in useful one day
			*
			*	to save to db in order_ini
			*******************************************************************************************************/

			$order_session['param']['currency'] 					= $this_order_currency;
			$order_session['param']['currencyiso'] 					= !empty($wppizza_options['order_settings']['currency']) ? $wppizza_options['order_settings']['currency'] : '';
			$order_session['param']['decimals'] 					= !empty($wppizza_options['prices_format']['hide_decimals']) ? 0 : (!defined('WPPIZZA_DECIMALS') ? 2 : (int)WPPIZZA_DECIMALS );
			$order_session['param']['currency_position'] 			= $wppizza_options['prices_format']['currency_symbol_position'];
			$order_session['param']['tax_included'] 				= $this_order_tax_included;
			$order_session['param']['taxrate'] 						= !empty($wppizza_options['order_settings']['item_tax']) ? $wppizza_options['order_settings']['item_tax'] : 0;
			$order_session['param']['taxrate_alt'] 					= !empty($wppizza_options['order_settings']['item_tax_alt']) ? $wppizza_options['order_settings']['item_tax_alt'] : 0;
			$order_session['param']['taxrate_shipping']				= !empty($wppizza_options['order_settings']['shipping_tax']) ? $wppizza_options['order_settings']['shipping_tax_rate'] : 0; /*0 or whats set if enabled*/
			$order_session['param']['min_order_delivery'] 			= !empty($wppizza_options['order_settings']['order_min_for_delivery']) ? $wppizza_options['order_settings']['order_min_for_delivery'] : 0 ;
			$order_session['param']['min_order_pickup'] 			= !empty($wppizza_options['order_settings']['order_min_for_pickup']) ? $wppizza_options['order_settings']['order_min_for_pickup'] : 0 ;
			$order_session['param']['delivery_exclude_items'] 		= !empty($delivery_exclude_items) ? $delivery_exclude_items : false;
			$order_session['param']['delivery_exclude_cats'] 		= !empty($delivery_exclude_cats) ? $delivery_exclude_cats : false;
			$order_session['param']['discount_type'] 				= $this_order_discount_selected;
			$order_session['param']['discounts'] 					= ( $this_order_discount_selected!='none') ? $wppizza_options['order_settings']['discounts'][$this_order_discount_selected]['discounts'] : false;
			$order_session['param']['self_pickup_discount'] 		= (!empty($this_order_self_pickup)) ? $wppizza_options['order_settings']['order_pickup_discount'] : 0 ;
			$order_session['param']['discount_exclude_items'] 		= !empty($discount_exclude_items) ? $discount_exclude_items : false;
			$order_session['param']['discount_exclude_cats'] 		= !empty($discount_exclude_cats) ? $discount_exclude_cats : false;


			/*************************************************************
			*
			*
			*	session variables - for session calculations
			*	but not needing to be saved to db
			*
			*
			*************************************************************/
			$order_session['checkout_parameters']['can_checkout'] = (count($wppizza_options['gateways'])<=0) ? false : true;/*ini as false if no gateways else as true checking min order etc at end of fucntion*/
			$order_session['checkout_parameters']['is_checkout'] = $is_checkout ;/*check if we are on order page*/
			$order_session['checkout_parameters']['min_order_required'] = 0 ;/* min order value required which must be reached to be able to checkout*/
			$order_session['checkout_parameters']['shop_open'] = wppizza_is_shop_open();// ? true : false;/*check if we are open*/
			$order_session['checkout_parameters']['is_pickup'] = $this_order_self_pickup;// ? true : false;/*are we picking up the order ourselves ?*/
			/*************************************************************
			*
			*
			*	MENU ITEMS // SUMING // TAXES
			*	to save to db in order_ini
			*
			*
			*	note: old orders use item instead of items !! make sure to account for in history print etc, or update db
			*
			**************************************************************/
			foreach($groupedItems  as $group_key=>$grouped_items){

				/*multipliers etc*/
				$item_blog_id 		= $session['items'][$group_key][0]['blog_id'];
				$item_post_id 		= $session['items'][$group_key][0]['post_id'];
				$item_cat_id 		= $session['items'][$group_key][0]['cat_id_selected'];
				$item_in_categories = $session['items'][$group_key][0]['item_in_categories'];
				$item_quantity 		= count($session['items'][$group_key]);
				$item_price 		= wppizza_round($session['items'][$group_key][0]['price']);/*round according to decimal settinsg*/
				$item_total 		= ($item_quantity * $item_price);
				$item_tax_rate 		= $session['items'][$group_key][0]['tax_rate'];
				$item_tax_to_add 	= empty($this_order_tax_included) ? ($item_total / 100 * $item_tax_rate) : 0;
				$item_tax_included 	= empty($this_order_tax_included) ? 0 : ($item_total/(100+$item_tax_rate)*$item_tax_rate);
				$item_use_alt_tax 	= $session['items'][$group_key][0]['use_alt_tax'] ;
				$item_taxrate_sum 	= ($item_quantity * $item_tax_rate);/* to allow us to calculate average taxrates*/
				$item_extend_data 	= empty($session['items'][$group_key][0]['extend_data']) ? array() : $session['items'][$group_key][0]['extend_data'] ;
				$item_custom_data 	= empty($session['items'][$group_key][0]['custom_data']) ? array() : $session['items'][$group_key][0]['custom_data'] ;
				/*
					indicate that at least one item uses alternative taxrate
				*/
				if($item_use_alt_tax){
					$this_order_multiple_taxrates = true;
				}

				/*item*/
				$order_session['items'][$group_key] = array();
				$order_session['items'][$group_key]['blog_id'] 				= 	$item_blog_id;
				$order_session['items'][$group_key]['cat_id_selected']		= 	$item_cat_id;
				$order_session['items'][$group_key]['post_id'] 				= 	$item_post_id;
				$order_session['items'][$group_key]['title'] 				= 	$session['items'][$group_key][0]['title'];
				$order_session['items'][$group_key]['quantity']				= 	$item_quantity;
				$order_session['items'][$group_key]['price'] 				= 	$item_price;
				$order_session['items'][$group_key]['price_formatted'] 		= 	!empty($item_price) ? wppizza_format_price($item_price, $this_order_currency) : 0 ;
				$order_session['items'][$group_key]['pricetotal'] 			= 	$item_total;
				$order_session['items'][$group_key]['pricetotal_formatted'] = 	!empty($item_total) ? wppizza_format_price($item_total, $this_order_currency) : 0 ;
				$order_session['items'][$group_key]['tax_rate'] 			= 	$item_tax_rate;
				$order_session['items'][$group_key]['tax_rate_formatted'] 	= 	!empty($item_tax_rate) ? wppizza_output_format_percent(wppizza_format_price_float($item_tax_rate, false)).'%' : '' ;
				$order_session['items'][$group_key]['tax_included'] 		= 	$item_tax_included;
				$order_session['items'][$group_key]['tax_to_add'] 			= 	$item_tax_to_add;
				$order_session['items'][$group_key]['use_alt_tax']			= 	$item_use_alt_tax;
				$order_session['items'][$group_key]['sizes'] 				= 	$session['items'][$group_key][0]['sizes'];
				$order_session['items'][$group_key]['size'] 				= 	$session['items'][$group_key][0]['size'];
				$order_session['items'][$group_key]['price_label'] 			= 	$session['items'][$group_key][0]['price_label'];
				$order_session['items'][$group_key]['item_in_categories'] 	= 	$item_in_categories;
				$order_session['items'][$group_key]['extend_data'] 			= 	$item_extend_data;
				$order_session['items'][$group_key]['custom_data'] 			= 	$item_custom_data;


				/*sum item prices total*/
				$this_order_total_price_items += $item_total;

				/*sum item taxe rates total  to allow us to calculate average taxrates*/
				$this_order_taxrates_total_items += $item_taxrate_sum; /** */

				 /* taxes - included or to add*/
				$this_order_taxes_included += $item_tax_included;
				$this_order_taxes_to_add += $item_tax_to_add;

				/* item uses alt tax rate*/
				if($item_use_alt_tax){
					$has_alt_tax = true;
					$this_order_taxes_at_rate['alt']['included'] += $item_tax_included ;
					$this_order_taxes_at_rate['alt']['to_add'] += $item_tax_to_add ;
					$this_order_taxes_at_rate['alt']['item_at_taxrate'] += $item_total ;
				}else{
					$has_main_tax = true;
					$this_order_taxes_at_rate['main']['included'] += $item_tax_included ;
					$this_order_taxes_at_rate['main']['to_add'] += $item_tax_to_add ;
					$this_order_taxes_at_rate['main']['item_at_taxrate'] += $item_total ;
				}




				/*sum item count total or customised (filtered) version thereof to allow delivery prices per item to be restricted */
				$this_order_number_of_items += $item_quantity;

				/**allow items to be excluded from count when calculating delivery prices per item**/
				#*\*/*\*/*\*/*\*/*\*/*\*/*\*/*\*/*\*/*\*/*\*/*\*/*#
				#	allow filtering cart_items
				#*\*/*\*/*\*/*\*/*\*/*\*/*\*/*\*/*\*/*\*/*\*/*\*/*#
				$excludeFromCount = apply_filters('wppizza_fltr_exclude_item_from_count', $excludeFromCount=false, $order_session['items'][$group_key]);

				if(!$excludeFromCount){
					$this_order_number_of_items_afer_exclusions += $item_quantity;
				}


				/*sum item prices total excluding items not eligable for discounts*/
				$intersect_cats_discount=array_intersect_key($item_in_categories, $discount_exclude_cats);
				if(!isset($discount_exclude_items[$item_post_id]) && count($intersect_cats_discount)<=0 ){
					$this_order_total_price_items_for_discount += $item_total;
				}
				/*sum item prices total excluding items omitted to calculate free delivery*/
				$intersect_cats_delivery=array_intersect_key($item_in_categories, $delivery_exclude_cats);
				if(!isset($delivery_exclude_items[$item_post_id]) && count($intersect_cats_delivery)<=0 ){
					$this_order_total_price_items_for_delivery += $item_total;
				}

			}


			#*\*/*\*/*\*/*\*/*\*/*\*/*\*/*\*/*\*/*\*/*\*/*\*/*#
			#	allow filtering of each cart item
			#*\*/*\*/*\*/*\*/*\*/*\*/*\*/*\*/*\*/*\*/*\*/*\*/*#
			$order_session['items'] = apply_filters('wppizza_fltr_session_items', $order_session['items']);


			/*************************************************************
			*
			*	MENU ITEMS // SUMING // TAXES ==> END
			*
			**************************************************************/



			/* update filter parameters */
			$filter_param['number_of_items'] 					= $this_order_number_of_items ;
			$filter_param['number_of_items_afer_exclusions'] 	= $this_order_number_of_items_afer_exclusions ;
			$filter_param['total_price_items'] 					= $this_order_total_price_items ;
			$filter_param['total_price_items_for_discount'] 	= $this_order_total_price_items_for_discount ;
			$filter_param['total_price_items_for_delivery'] 	= $this_order_total_price_items_for_delivery ;

			/**
				if there are no items with a aprticular taxrate, unset
				else calculate/set percentage of this taxrate applied
				of total order
			**/
			if(!$has_alt_tax){
				unset($this_order_taxes_at_rate['alt']);
				$number_of_taxrates	--;
			}

			if(!$has_main_tax){
				unset($this_order_taxes_at_rate['main']);
				$number_of_taxrates	--;
			}

			$one_percent_of_total = ($this_order_total_price_items == 0 ) ? 1 : ($this_order_total_price_items / 100);
			foreach($this_order_taxes_at_rate as $tbrKey => $txbyrate){
				/** calculate percentage of total for each taxrate */
				$this_order_taxes_at_rate[$tbrKey]['item_at_taxrate'] = $this_order_taxes_at_rate[$tbrKey]['item_at_taxrate'] / $one_percent_of_total / 100;
			}

			/**************************************

				[discounts]

			**************************************/
			/*no discount - adding 0 just for consistenacy*/
			if($this_order_discount_selected=='none'){
				$this_order_discount += 0;
			}
			/** percentage discount**/
			if($this_order_discount_selected=='percentage'){
				/**sort highest to lowest to get most relevant discount to apply to price if it aplies, if it does, apply and stop loop (only want to appply one!**/
				rsort($wppizza_options['order_settings']['discounts']['percentage']['discounts']);
				foreach($wppizza_options['order_settings']['discounts']['percentage']['discounts'] as $k=>$v){
					if($this_order_total_price_items_for_discount>=$v['min_total']){
						$this_order_discount += ($this_order_total_price_items_for_discount/100*$v['discount']);
					break;
					}
				}
			}
			/** value/standard money off discount**/
			if($this_order_discount_selected=='standard'){
				/**sort highest to lowest to get most relevant discount to apply to price if it aplies, if it does, apply and stop loop (only want to appply one!**/
				rsort($wppizza_options['order_settings']['discounts']['standard']['discounts']);
				foreach($wppizza_options['order_settings']['discounts']['standard']['discounts'] as $k=>$v){
					if($this_order_total_price_items_for_discount>=$v['min_total']){
						$this_order_discount += $v['discount'];
					break;
					}
				}
			}
			/***self pickup discount added to other discounts (if any)**/
			if($wppizza_options['order_settings']['order_pickup_discount']>0 && !empty($session['self_pickup']) ){
				$this_order_discount += ($this_order_total_price_items_for_discount/100*$wppizza_options['order_settings']['order_pickup_discount']);
			}

			/********************************************************
			*	[additional discounts for using a particular gateway -
			*	only on checkout page and only if min value reached ]
			*********************************************************/
			//if($is_checkout){
				if(!empty($gateway_discounts) && $this_order_total_price_items_for_discount >= $gateway_min_order_value){
					/* fixed */
					if(!empty($gateway_discounts['fixed'])){
						$this_order_discount += $gateway_discounts['fixed'];
					}
					/* percent */
					if(!empty($gateway_discounts['percent'])){
						$this_order_discount += $this_order_total_price_items_for_discount / 100 * $gateway_discounts['percent'];
					}
				}
			//}


			#*\*/*\*/*\*/*\*/*\*/*\*/*\*/*\*/*\*/*\*/*\*/*\*/*#
			#	allow filtering discount
			#*\*/*\*/*\*/*\*/*\*/*\*/*\*/*\*/*\*/*\*/*\*/*\*/*#
			$this_order_discount = apply_filters('wppizza_fltr_discount', $this_order_discount, $session_items, $filter_param);

			/**let's make sure discount is never > total_price_items_for_discount*/
			$this_order_discount = min($this_order_discount, $this_order_total_price_items_for_discount);



			/********************************************************
			*	[any additional - filterable  - discounts ]
			* 	@since 3.9 array => label|value pair
			*	values summed to calculate deliveries based on settings
			*********************************************************/
			$additional_discounts = apply_filters('wppizza_fltr_additional_discounts', array(), $session_items, $filter_param);
			$additional_discounts_sum = 0;		/* sum of any additional discounts */
			foreach($additional_discounts as $adKey => $adParam){
				$additional_discounts[$adKey]['label'] = $adParam['label'];
				$additional_discounts[$adKey]['value'] = wppizza_round($adParam['value']);
				/* sum value to calculate totals, delivery charges etc etc */
				$additional_discounts_sum += wppizza_round($adParam['value']);
			}



			#*************************************************#
			#	delivery prices based on
			#	total item prices *before* discount
			#*************************************************#
			if(!empty($wppizza_options['order_settings']['discount_calculate_delivery_before_discount'])){
				$this_order_total_price_items_for_delivery -=  ($this_order_discount + $additional_discounts_sum);
			}

			/**************************************

				[delivery charges]

			**************************************/
			/*
				if self pickup | no delivery , force delivery charges to zero , only really for consistency as it's initialized at zero anyway
			*/
			if(!empty($this_order_self_pickup) || $wppizza_options['order_settings']['delivery_selected']=='no_delivery'){
				$this_order_delivery_charges = 0;
			}
			/*
				not self pickup and delivery enabled, calc delivery
			*/
			if(empty($this_order_self_pickup) && $wppizza_options['order_settings']['delivery_selected']!='no_delivery'){

				/*
				*	standard (i.e. fixed delivery charges)
				*/
				if($wppizza_options['order_settings']['delivery_selected']=='standard'){
					if($wppizza_options['order_settings']['delivery']['standard']['delivery_charge']>0){
						$this_order_delivery_charges = $wppizza_options['order_settings']['delivery']['standard']['delivery_charge'];
					}
				}

				/*
				*	minimum total
				*/
				if($wppizza_options['order_settings']['delivery_selected']=='minimum_total'){

					if(!empty($wppizza_options['order_settings']['delivery']['minimum_total']['deliver_below_total'])){
						if($this_order_total_price_items_for_delivery < $wppizza_options['order_settings']['delivery']['minimum_total']['min_total']){
							$this_order_delivery_charges = $wppizza_options['order_settings']['delivery']['minimum_total']['min_total']-$this_order_total_price_items_for_delivery;
						}
					}

					/**fixed price set if below free delivery: overrides "deliver_below_total" above **/
					if($wppizza_options['order_settings']['delivery']['minimum_total']['deliverycharges_below_total']>0){
						if($this_order_total_price_items_for_delivery < $wppizza_options['order_settings']['delivery']['minimum_total']['min_total']){
							$this_order_delivery_charges = $wppizza_options['order_settings']['delivery']['minimum_total']['deliverycharges_below_total'];
						}
					}
				}

				/*
				*	delivery charges on a per item basis (applicable items are filterable)
				*/
				if($wppizza_options['order_settings']['delivery_selected']=='per_item'){

					/**free delivery isset>0**/
					if($wppizza_options['order_settings']['delivery']['per_item']['delivery_per_item_free'] >0 ){
						/*value not reached for free delivery*/
						if($this_order_total_price_items_for_delivery < $wppizza_options['order_settings']['delivery']['per_item']['delivery_per_item_free']){
							/*number of items*deliverycharges per item*/
							if(	$this_order_number_of_items_afer_exclusions > 0 && $wppizza_options['order_settings']['delivery']['per_item']['delivery_charge_per_item']  >0){
								$this_order_delivery_charges = ($this_order_number_of_items_afer_exclusions * $wppizza_options['order_settings']['delivery']['per_item']['delivery_charge_per_item']);
							}
						}
					}else{/*no free delivery set (i.e set to 0)*/
						/*number of items*deliverycharges per item*/
						if($this_order_number_of_items_afer_exclusions>0){
							$this_order_delivery_charges = ($this_order_number_of_items_afer_exclusions*$wppizza_options['order_settings']['delivery']['per_item']['delivery_charge_per_item']);
						}
					}
				}
			}

			#*\*/*\*/*\*/*\*/*\*/*\*/*\*/*\*/*\*/*\*/*\*/*\*/*#
			#	allow filtering delivery charges
			#*\*/*\*/*\*/*\*/*\*/*\*/*\*/*\*/*\*/*\*/*\*/*\*/*#
			$this_order_delivery_charges = apply_filters('wppizza_fltr_delivery_charges', $this_order_delivery_charges, $session_items, $filter_param);
			$this_order_delivery_charges = empty($this_order_delivery_charges) ? 0 : $this_order_delivery_charges ;//simply sanitisation for filters that return an empty value

			/**********************************************************
			*
			*	[taxes -  calc average tax rate per item (in case there are different taxrates per item applied)]
			*
			**********************************************************/
			$this_order_taxrates_average_per_item = ($this_order_number_of_items>0) ? ($this_order_taxrates_total_items /  $this_order_number_of_items) : 0 ;

			/**********************************************************
			*
			*	[taxes -  tax NOT included in set prices]
			*
			**********************************************************/
			if(!$this_order_tax_included){

				/*
					discount taxes
				*/
				/*
					discount tax (substract)
					if there is any discount, calculate what the tax is there and SUSTRACT it from included tax (as the discount applies before tax)
					we will have to use the average taxrate here to apply the discount evenly across items with different taxrates
				*/
				if(!empty($this_order_discount)){
					/** combined tax **/
					$this_order_taxes_to_add -= ($this_order_discount + $additional_discounts_sum) / 100 * $this_order_taxrates_average_per_item;

					/** tax by rate **/
					foreach($this_order_taxes_at_rate as $tbrKey => $txbyrate){
						$this_order_taxes_at_rate[$tbrKey]['to_add'] -=  (($this_order_discount + $additional_discounts_sum) * $txbyrate['item_at_taxrate']) / 100 * $txbyrate['rate'] ;
					}
				}

				/*
					shipping / handling tax
				*/
				if(!empty($wppizza_options['order_settings']['shipping_tax']) && !empty($wppizza_options['order_settings']['shipping_tax_rate'])){
					$this_order_taxes_shipping = ($this_order_delivery_charges / 100 * $wppizza_options['order_settings']['shipping_tax_rate']);
					/*also add to total taxes to add */
					$this_order_taxes_to_add += $this_order_taxes_shipping;
					/* separate breakdown for shipping_handling tax*/
					$this_order_taxes_at_rate['shipping']['rate'] =  $wppizza_options['order_settings']['shipping_tax_rate'];
					$this_order_taxes_at_rate['shipping']['included'] =  0;
					$this_order_taxes_at_rate['shipping']['to_add'] =  $this_order_taxes_shipping;
				}
			}


			/**********************************************************
			*
			*	[taxes - tax IS included in prices !!!]
			*
			**********************************************************/
			if($this_order_tax_included){

				/*
					discount tax (substract)
					if there is any discount, calculate what the tax is there and SUSTRACT it from included tax (as the discount applies before tax)
					we will have to use the average taxrate here to apply the discount evenly across items with different taxrates
				*/
				if(!empty($this_order_discount)){
					/** combined tax **/
					$this_order_taxes_included -= ($this_order_discount  + $additional_discounts_sum ) / (100 + $this_order_taxrates_average_per_item) * $this_order_taxrates_average_per_item;
					/** tax by rate **/
					foreach($this_order_taxes_at_rate as $tbrKey => $txbyrate){
						$this_order_taxes_at_rate[$tbrKey]['included'] -=  (($this_order_discount + $additional_discounts_sum) * $txbyrate['item_at_taxrate']) / (100 + $this_order_taxrates_average_per_item) * $txbyrate['rate'];
					}
				}

				/*
					shipping / handling tax
				*/
				if(!empty($wppizza_options['order_settings']['shipping_tax']) && !empty($wppizza_options['order_settings']['shipping_tax_rate'])){
					$this_order_taxes_shipping = ($this_order_delivery_charges / (100 + $wppizza_options['order_settings']['shipping_tax_rate']) * $wppizza_options['order_settings']['shipping_tax_rate']);
					/*also add to total taxes_include */
					$this_order_taxes_included += $this_order_taxes_shipping;
					/* separate breakdown for shipping_handling tax*/
					$this_order_taxes_at_rate['shipping']['rate'] =  $wppizza_options['order_settings']['shipping_tax_rate'];
					$this_order_taxes_at_rate['shipping']['included'] =  $this_order_taxes_shipping;
					$this_order_taxes_at_rate['shipping']['to_add'] =  0;
				}
			}

			/***************************************
				using taxes broken down per taxrate
			***************************************/
			$tax_by_rate 		= array();
			$total_taxes_by_rate_included = 0;
			$total_taxes_by_rate_to_add = 0;
			foreach($this_order_taxes_at_rate as $tbrKey => $txbyrate){
				/** round and sum per rate */
				$taxes_by_rate_included = !empty($txbyrate['included']) ?  ( empty($tax_round_natural) ? wppizza_round_up($txbyrate['included']) : wppizza_round($txbyrate['included']) ) : 0 ;/* round taxes included */
				$taxes_by_rate_to_add =   !empty($txbyrate['to_add']) ?  ( empty($tax_round_natural) ? wppizza_round_up($txbyrate['to_add']) : wppizza_round($txbyrate['to_add']) ) : 0 ;/* round taxes to add */
				$taxes_by_rate = $taxes_by_rate_included + $taxes_by_rate_to_add ;

				/* add used rate and tax for this rate to array */
				/*
					included
				*/
				//$tax_by_rate['included'][$tbrKey]['rate']	= !empty($txbyrate['rate']) ? $txbyrate['rate'] : 0 ;
				//$tax_by_rate['included'][$tbrKey]['total']	= $taxes_by_rate_included;

				/*
					to add
				*/
				//$tax_by_rate['add'][$tbrKey]['rate']	= !empty($txbyrate['rate']) ? $txbyrate['rate'] : 0 ;
				//$tax_by_rate['add'][$tbrKey]['total']	= $taxes_by_rate_to_add;


				/*
					total
				*/
				$tax_by_rate[$tbrKey]['rate']	= !empty($txbyrate['rate']) ? $txbyrate['rate'] : 0 ;
				$tax_by_rate[$tbrKey]['total']	= $taxes_by_rate;



				/** sum **/
				$total_taxes_by_rate_included += $taxes_by_rate_included;
				$total_taxes_by_rate_to_add += $taxes_by_rate_to_add;
			}
			/**
				if we have more than one , overwrite combined tax , should possibly always be applied , even if only one
				let's see in a few months time
			**/
			if(count($this_order_taxes_at_rate)>1){
				$this_order_taxes_included = $total_taxes_by_rate_included;
				$this_order_taxes_to_add = $total_taxes_by_rate_to_add;
			}

			/**************************************
			*	[total order before tips and handling ]
			***************************************/
			$totalOrderBeforeTipsAndHandling = $this_order_total_price_items - ($this_order_discount  + $additional_discounts_sum) + $this_order_delivery_charges + $this_order_taxes_to_add;

			/**************************************
			*	[surcharges]
			***************************************/
			//if($is_checkout){/*only add to session on order pages or when adding to db so $is_checkout must be true when adding to db - only on checkout page*/
				if(!empty($gateway_surcharges)){
					/* fixed */
					if(!empty($gateway_surcharges['fixed'])){
						$this_order_handling_charges += $gateway_surcharges['fixed'];
					}
					/* percent */
					if(!empty($gateway_surcharges['percent'])){
						/** calculate percentage handling charges on sum before tips */
						$this_order_handling_charges += $totalOrderBeforeTipsAndHandling/100*$gateway_surcharges['percent'];
					}
				}
			//}
			#*\*/*\*/*\*/*\*/*\*/*\*/*\*/*\*/*\*/*\*/*\*/*\*/*#
			#	allow filtering surcharges
			#*\*/*\*/*\*/*\*/*\*/*\*/*\*/*\*/*\*/*\*/*\*/*\*/*#
			$this_order_handling_charges = apply_filters('wppizza_fltr_surcharges', $this_order_handling_charges, $session_items, $filter_param);



			/**************************************
			*	set/check min order values (delivery/pickup)
			*	also set can_checkout to false if nothing in cart
			***************************************/
			/** ON DELIVERY - minimum order value set but not reached - as long as its not set to $options['order_settings']['delivery_selected']==no_delivery***/
			if(empty($this_order_self_pickup) && $wppizza_options['order_settings']['delivery_selected']!='no_delivery'){

				/** required order total -> delivery*/
				$order_session['checkout_parameters']['min_order_required'] = $wppizza_options['order_settings']['order_min_for_delivery'] ;

				/**
					minimum order value set but not reached -> delivery
				**/
				/* 	minimum order items value only */
				if(empty($wppizza_options['order_settings']['order_min_on_totals'])){
					if(!empty($wppizza_options['order_settings']['order_min_for_delivery']) && $wppizza_options['order_settings']['order_min_for_delivery'] > $this_order_total_price_items_for_delivery ){
						/*disable place order button**/
						$order_session['checkout_parameters']['can_checkout'] = false;/*no checkout*/
					}
				}
				else
				/* 	minimum order on total before tips and handling */
				{
					/* are we excluding delivery charges from this calculation ? */
					$exclude_delivery_charges = !empty($wppizza_options['order_settings']['order_min_exclude_delivery_charges']) ? $this_order_delivery_charges : 0;
					if(!empty($wppizza_options['order_settings']['order_min_for_delivery']) && $wppizza_options['order_settings']['order_min_for_delivery'] > ($totalOrderBeforeTipsAndHandling-$exclude_delivery_charges) ){
						/*disable place order button**/
						$order_session['checkout_parameters']['can_checkout'] = false;/*no checkout*/
					}

				}
			}


			/**ON PICKUP -> minimum order value set but not reached  or if set to pickup only***/
			if(!empty($this_order_self_pickup) || $wppizza_options['order_settings']['delivery_selected']=='no_delivery'){

				/** required order total -> self pickup*/
				$order_session['checkout_parameters']['min_order_required'] = $wppizza_options['order_settings']['order_min_for_pickup'] ;/* min order value required which must be reached to be able to checkout*/

				/**
					minimum order value set but not reached -> self pickup
				**/

				/* 	minimum order items value only */
				if(empty($wppizza_options['order_settings']['order_min_on_totals'])){
					if(!empty($wppizza_options['order_settings']['order_min_for_pickup']) && $wppizza_options['order_settings']['order_min_for_pickup'] > $this_order_total_price_items_for_delivery ){
						/*disable place order button**/
						$order_session['checkout_parameters']['can_checkout'] = false;/*no checkout*/
					}
				}
				else
				/* 	minimum order on total before tips and handling */
				{
					if(!empty($wppizza_options['order_settings']['order_min_for_pickup']) && $wppizza_options['order_settings']['order_min_for_pickup'] > $totalOrderBeforeTipsAndHandling ){
						/*disable place order button**/
						$order_session['checkout_parameters']['can_checkout'] = false;/*no checkout*/
					}

				}
			}


			/** for consistency - disable checkout too if no item in cart*/
			if($this_order_number_of_items<=0){
				$order_session['checkout_parameters']['can_checkout'] = false;/*no checkout*/
			}

			/**************************************
			*	[gratuities  / tips - from userdata, converting comma separators if exist in language]
			***************************************/
			$session_tip = 	(isset($_SESSION[$this->session_key_userdata]['ctips']) && is_numeric($_SESSION[$this->session_key_userdata]['ctips'])) ? wppizza_format_price_float($_SESSION[$this->session_key_userdata]['ctips']) : '';
			$this_order_tips = $session_tip ;
			$this_order_tips_set = (!empty($session_tip)) ? wppizza_round($session_tip) : ( is_numeric($session_tip) ? wppizza_round(0) : false) ;// set distinctly to zero if a zero tip was added

			/******************************************************************************************************
			*
			*
			*
			*	ROUND AND SUM TOTALS / SUBTOTALS
			*
			*
			*
			*
			*******************************************************************************************************/

			/**************************************
				rounding
			**************************************/
			$total_price_items = wppizza_round($this_order_total_price_items);/*already rounded as appropriate, but for consistency ..*/
			$discount = wppizza_round($this_order_discount);	/* round discount */
			$delivery_charges = wppizza_round($this_order_delivery_charges); /* round sum of delivery_charges*/
			$handling_charges = wppizza_round($this_order_handling_charges); /* round sum of handling_charge /  surcharges */
			$taxes_included = empty($tax_round_natural) ? wppizza_round_up($this_order_taxes_included) : wppizza_round($this_order_taxes_included);/* round taxes included */
			$taxes_to_add =  empty($tax_round_natural) ? wppizza_round_up($this_order_taxes_to_add) : wppizza_round($this_order_taxes_to_add) ;/* round taxes to add */
			$taxes = $taxes_included + $taxes_to_add ;/* taxes to display, sum of included and to add (as one of these will always be 0 depending on taxes being included or not) */
			$tips = wppizza_round($this_order_tips);/* round to add to total */



			/**************************************
			*	[sum total of rounded values]
			* items - discount + delivery (0 when pickup) + taxes(to add) + handling + tips
			***************************************/
			$total = $total_price_items - ( $discount + $additional_discounts_sum) + $delivery_charges + $taxes_to_add + $handling_charges + $tips;


			/**************************************
				set summmary / subtotals
			**************************************/
			$order_session['summary']['blog_id'] 			= $blog_id ;
			$order_session['summary']['self_pickup'] 		= $this_order_self_pickup;
			$order_session['summary']['number_of_items'] 	= $this_order_number_of_items;
			$order_session['summary']['total_price_items'] 	= $total_price_items;
			$order_session['summary']['discount']			= $discount;
			$order_session['summary']['additional_discounts']= $additional_discounts;
			$order_session['summary']['total_discounts']	= wppizza_round($discount + $additional_discounts_sum);// round to deal with possible precision errors
			$order_session['summary']['delivery_charges'] 	= $delivery_charges;
			$order_session['summary']['handling_charges'] 	= $handling_charges;
			$order_session['summary']['taxrate_average'] 	= $this_order_taxrates_average_per_item;
			$order_session['summary']['tax_by_rate'] 		= $tax_by_rate;/* summed taxes by rate */
			$order_session['summary']['taxes'] 				= $taxes; /* combined taxes */
			$order_session['summary']['multiple_taxrates'] 	= !empty($this_order_multiple_taxrates) ? true : false;
			$order_session['summary']['tips'] 				= $this_order_tips_set;
			$order_session['summary']['total'] 				= $total;


			/****************************************************************************
			*
			*
			*	even though the cart and its calculations *might* be required multiple times
			*	on a single page (when having 2 carts perhaps , or cart and minicart and / or min totals
			*	and or cart plus orderpage/thankyiou page etc etc)
			*	we only run the calculations once statically and other wise use the set var
			*
			*****************************************************************************/
			#*\*/*\*/*\*/*\*/*\*/*\*/*\*/*\*/*\*/*\*/*\*/*\*/*#
			#	allow filtering order_session
			#*\*/*\*/*\*/*\*/*\*/*\*/*\*/*\*/*\*/*\*/*\*/*\*/*#
			$order_session = apply_filters('wppizza_fltr_order_session', $order_session, $session_items, $filter_param);


			#*\*/*\*/*\*/*\*/*\*/*\*/*\*/*\*/*\*/*\*/*\*/*\*/*#
			#	allow additional filtering after cart update only
			#*\*/*\*/*\*/*\*/*\*/*\*/*\*/*\*/*\*/*\*/*\*/*\*/*#
			if(has_action('wppizza_on_cart_update')){

				$order_session = apply_filters('wppizza_fltr_order_session_update', $order_session, $filter_param);
			}


			$this->session_order = $order_session;

		}
		/* end static */



	return $this->session_order;
	}

/****************************************************************************************************************
*
*
*	[VARIOUS SESSION HELPERS, ACTIONS, INITS]
*
*
****************************************************************************************************************/

	/*******************************************************

		[set session idents]

	******************************************************/
	function set_session_idents() {
		if(is_multisite() ){
			global $blog_id;
			/* session per site , or global. default per site including blog id */
			$this->session_key_cart = apply_filters('wppizza_filter_session_per_site', ''.WPPIZZA_SLUG.'_'.$blog_id );
		}else{
			$this->session_key_cart = WPPIZZA_SLUG;
		}

		/**session name for user data for example such as address etc that keeps it's values across multisites**/
		$this->session_key_userdata = WPPIZZA_SLUG.'_userdata';
	}

	/*******************************************************

		[start/init session]

	******************************************************/
	function maybe_init_sessions() {
		global $wppizza_options;
	    if (!session_id()) {session_start();}


	    /*initialize if not set*/
	    if(!isset($_SESSION[$this->session_key_cart])){

			/*ini array */
	    	$_SESSION[$this->session_key_cart] = array();

	    	/*holds items in cart*/
	    	$_SESSION[$this->session_key_cart]['items']=array();

	    	/*gross sum of all items in cart,before discounts etc*/
	    	$_SESSION[$this->session_key_cart]['total_price_items']=0;

		    /**
		    	if default set to be self pickup or set to be no_delivery
				make sure we force for to "no delivery" if no session set yet
			**/
		    if(
		    	(
		    		!empty($wppizza_options['order_settings']['order_pickup']) &&
		    		!empty($wppizza_options['order_settings']['order_pickup_as_default'])
		    	) ||
		    		$wppizza_options['order_settings']['delivery_selected']=='no_delivery'
		    ){
				$_SESSION[$this->session_key_cart]['self_pickup'] = true;
		    }
	    }

	    /**
	    	alwasy force to pickup if no delivery is selected
	    	regardless of session set or not

	    	mainly really to not confuse site admins that switch to no_delivery but have a session set already
			and then leave messages on forums or emails along the lines of "dont work" when it's simply down to
			not having cleared the cache
	    **/
	    if($wppizza_options['order_settings']['delivery_selected']=='no_delivery'){
	    	$_SESSION[$this->session_key_cart]['self_pickup'] = true;
	    }



	    if(!isset($_SESSION[$this->session_key_userdata])){
	    	/**userdata like address etc*****/
	    	$_SESSION[$this->session_key_userdata]=array();

			/* initialize with first available gateway */
	    	$_SESSION[$this->session_key_userdata][''.WPPIZZA_SLUG.'_gateway_selected'] = WPPIZZA()->gateways->session_ini();
	    }

	    /*
	    	in case gateway(s) were enabled during a session when none was available before
	    	make sure one is set.
	    	wont happen too often though, but just in case...
	    */
	    if(empty($_SESSION[$this->session_key_userdata][''.WPPIZZA_SLUG.'_gateway_selected'])){
	    	$_SESSION[$this->session_key_userdata][''.WPPIZZA_SLUG.'_gateway_selected'] = WPPIZZA()->gateways->session_ini();
	    }

	}
	/************************************************************************************

		[clear session data - used when updating plugin]

	************************************************************************************/
	function clear_session(){
		if(isset($_SESSION[$this->session_key_userdata])){
			unset($_SESSION[$this->session_key_userdata]);
		}
		if(isset($_SESSION[$this->session_key_cart])){
			unset($_SESSION[$this->session_key_cart]);
		}
	}

	/*******************************************************


		[just getting whats in the cart]


	******************************************************/
	function get_cart($is_checkout = null, $recalculate = false){
		/**********
			sort and calculate from session
		**********/
		$obj['cart'] = $this -> sort_and_calculate_cart($is_checkout, $recalculate);
	return $obj;
	}

	/*******************************************************


		[just return checkout parameters]


	******************************************************/
	function get_checkout_parameters($is_checkout = null, $recalculate = false){
		/**********
			sort and calculate from session
		**********/
		$obj = $this -> sort_and_calculate_cart($is_checkout, $recalculate);

	return $obj['checkout_parameters'];
	}

	/*******************************************************


		[just getting the summary, in many cases that's enough]


	******************************************************/
	function get_cart_summary($is_checkout = null, $recalculate = false){
		/**********
			sort and calculate from session
		**********/
		$obj = $this -> sort_and_calculate_cart($is_checkout, $recalculate);
	return $obj['summary'];
	}


	/*******************************************************

		[adding a menu item to order session]

	******************************************************/
	function add_item_to_cart($element_id, $is_checkout, $recalculate = true){
		global $blog_id, $wppizza_options, $switched;


		/*********
			split submitted id vars into their respective elements
			the id submitted will be something like
			wppizza-1-8-47-1-0 , so remove slug first
		*********/
		$element_id = explode('-',str_replace(WPPIZZA_SLUG.'-', '', $element_id));
		/*********
			just skip if not 5 elements, as someone will
			have probably messed around with the id
		*********/
		if(count($element_id)!=5){
			$obj['invalid'] = 'cheating, huh ? error: [S-1001]';
			return $obj;
		}
		/*********
			map exploded id's - also an easy way to make sure no invalid data gets submitted
			by casting absolute integers of everything
		*********/
		$element = array();
		$element['blog_id']			= abs((int)$element_id[0]);/* blog id */
		$element['cat_id_selected']	= abs((int)$element_id[1]);/* category id selected */
		$element['post_id']			= abs((int)$element_id[2]);/* post (menu item) id */
		$element['sizes']			= abs((int)$element_id[3]);/* id of sizes */
		$element['size']			= abs((int)$element_id[4]);/* id of selected size == price */

		/*
			if we are adding from a different blog - user oder history -> repurchase for example
			switch to that blog first
		*/
		if(is_multisite() && $element['blog_id'] != $blog_id){
			switch_to_blog($element['blog_id']);
		}
		/*********
			get/set item posttype , meta, cats etc
			according to $element array
		*********/
		/*post blogid*/
		$post_blog_id = $element['blog_id'];
		/*post id*/
		$post_id = $element['post_id'];
		/*post type*/
		$post_type = get_post_type($element['post_id']);
		/*post title attribute -  title with stripped html*/
		$post_title_attribute = the_title_attribute(array('post' => $element['post_id'], 'echo' => 0));
		/*"normal" post title including html if someone puts it in there ...*/
		$post_title = get_the_title($element['post_id'])	;
		/*post slug - currently unused */
		//$post_slug = get_post_field( 'post_name', get_post($element['post_id']) );
		/*post meta*/
		$post_meta = get_post_meta($element['post_id'], WPPIZZA_SLUG);
		$post_meta = $post_meta[0];
		/*post sizes / tiers set in wppizza->sizes*/
		$post_sizes = $element['sizes'];
		/* post size (i.e price tier) selected */
		$post_size = $element['size'];
		/* resulting price of selected size*/
		$post_price = $post_meta['prices'][$post_size];
		/* price label of selected size*/
		$post_price_label = $wppizza_options['sizes'][$post_sizes][$post_size]['lbl'];
		/* taxrate applied to this menu item */
		$post_taxrate = empty($post_meta['item_tax_alt']) ? $wppizza_options['order_settings']['item_tax'] : $wppizza_options['order_settings']['item_tax_alt'];
		/* using alt tax rate ? */
		$post_uses_alt_tax = empty($post_meta['item_tax_alt']) ? false : true;
		/* SINGLE category this item was in when selected */
		$post_cat_id = $element['cat_id_selected'] ;
		/* ALL categories this item belongs to */
		$terms = get_the_terms( $element['post_id'], WPPIZZA_TAXONOMY );
		$post_cats	= array();
		if ( $terms && ! is_wp_error( $terms ) ) {
			foreach($terms as $term){
				/*only set some useful ones*/
				$post_cats[$term->term_id] = array();
				$post_cats[$term->term_id]['name']=$term->name;
				$post_cats[$term->term_id]['slug']=$term->slug;
				$post_cats[$term->term_id]['description']=$term->description;
				$post_cats[$term->term_id]['parent']=$term->parent;
				$post_cats[$term->term_id]['count']=$term->count;
			}
		}else{
			/*
				if it has not been set to belong to any category
				let's force one uncotegorised with id 0
			*/
			//$post_cats = wppizza_force_first_category(true);
			$post_cats[0] = array();
			$post_cats[0]['name']=$wppizza_options['localization']['uncategorised'];
			$post_cats[0]['slug']=sanitize_title_with_dashes($wppizza_options['localization']['uncategorised']);
			$post_cats[0]['description']='';
			$post_cats[0]['parent']=0;
			$post_cats[0]['count']=0;
		}

		/**********
		 check validity , comparing post types etc to submitted
		 $_POST['vars']['id']
		 2000+ integers as error codes
		 (in case someone wants to mess with frontent id's)
		**********/
		$verify = array();
		$verify[] = ($post_blog_id == $blog_id) ?  0 : 2001; /*verify blog id*/
		$verify[] = (isset($post_cats[$post_cat_id])) ?  0 : 2002; /*verify category id*/
		$verify[] = ($post_type == WPPIZZA_POST_TYPE) ?  0 : 2003; /*verify post type*/
		$verify[] = ($post_meta['sizes'] == $post_sizes) ?  0 : 2004; /*verify sizes id*/
		$verify[] = (isset($post_meta['prices'][$post_size])) ?  0 : 2005; /*verify price id*/

		$invalid = array_sum($verify);

		/*not valid (sum of check > 0 ), stop right here, output error id's in console**/
		if(!empty($invalid)){
			$obj['invalid'] = 'cheating , huh ? errors: [S-'.implode(' | S-', $verify).']';
		return $obj;
		}

		/*
			restore current blog if switched
		*/
		if(!empty($switched)){
			restore_current_blog();
		}
		/*********
			session item data
		*********/
		/*to group same items together, create group key*/
		$session_item_group_key = ''.$post_blog_id.'.'.$post_cat_id.'.'.$post_id.'.'.$post_size.'';
		$session_item = array();
		$session_item['sortname']			= strtolower($post_title_attribute);
		$session_item['blog_id']			= $post_blog_id;/* blog id */
		$session_item['cat_id_selected']	= $post_cat_id;/* category id selected */
		$session_item['post_id']			= $post_id;/* post (menu item) id */
		$session_item['sizes']				= $post_sizes;/* id of sizes this item belongs to - not really required anywhere but might come in useful*/
		$session_item['size']				= $post_size;/* id of selected size */
		$session_item['price']				= $post_price;/* add item price  base price of selected size */
		$session_item['price_label']		= $post_price_label;/* selected price label */
		$session_item['tax_rate']			= $post_taxrate;/* get tax_rate applicable tax rate */
		$session_item['use_alt_tax']		= $post_uses_alt_tax;/* using alternative taxrate [bool] */
		$session_item['title']				= $post_title;/*add item name */
		$session_item['title_attribute']	= $post_title_attribute;/*add item name with stripped html here incase someone put html into post title. might be useful one day*/
		$session_item['item_in_categories']	= $post_cats;	/** all category id's item is assigned to**/

		/***********
			allow adding / filtering of custom_data when adding to cart
			if a 3rd party plugin uses this, it really should add its own unique key to this array too !!!
			@since 3.8.6
		***********/
		$session_item['custom_data'] = apply_filters('wppizza_filter_session_add_item_to_cart', array() , $session_item);

		/**********
			add item to session
		**********/
		$_SESSION[$this->session_key_cart]['items'][$session_item_group_key][] = $session_item;

		/**********

			sort and calculate from session

		**********/
		$obj = array();
		if($recalculate){
			$obj['cart'] = $this -> sort_and_calculate_cart($is_checkout);
		}

	return $obj;
	}


	/*******************************************************

		[adjust count of items from cart]

	******************************************************/
	function modify_items_in_cart($session_cart_id, $quantity, $is_checkout, $recalculate = true){

		/*********
			split submitted id vars into their respective elements
			the id submitted will be something like
			wppizza-1-8-47-1-0 , so remove slug first to get key in items array
		*********/
		$session_cart_id = str_replace(WPPIZZA_PREFIX.'-cart-', '', $session_cart_id);

		/*
			current quantity
			if it does not exist, set to 1 (as we will be removing one when clicking on remove from cart icon)
		*/
		$current_quantity = !empty($_SESSION[$this->session_key_cart]['items'][$session_cart_id]) ? count($_SESSION[$this->session_key_cart]['items'][$session_cart_id]) : 1 ;


		/*
			required quantity - substract if -1 , else use integer sent
		*/
		$required_quantity = ($quantity == -1 ) ? ($current_quantity - 1) : $quantity;

		/**
			quantity set
		**/
		/*remove entirely if resulting is 0*/
		if($required_quantity <= 0){
			unset($_SESSION[$this->session_key_cart]['items'][$session_cart_id]);
		}

		/*remove up to resulting required , provided there are more in cart than we want*/
		if($required_quantity > 0 && $current_quantity >= $required_quantity){
			array_splice($_SESSION[$this->session_key_cart]['items'][$session_cart_id], $required_quantity);
		}

		/*add up to resulting required , if there are fewer in cart than we want*/
		if($required_quantity > 0 && $current_quantity < $required_quantity){
			/*get difference between waht we have and what we need */
			$to_add =  ($required_quantity - $current_quantity);
			$get_item = $_SESSION[$this->session_key_cart]['items'][$session_cart_id][0];
			for($i=0; $i< $to_add ;$i++){
				$_SESSION[$this->session_key_cart]['items'][$session_cart_id][] = $get_item;

			}
		}

		$obj = array();
		if($recalculate){
			$obj['cart'] = $this -> sort_and_calculate_cart($is_checkout);
		}
	return $obj;
	}


	/*******************************************************

		[empty cart]

	******************************************************/
	function empty_cart($is_checkout, $recalculate = true){

		$selected_pickup_option = isset($_SESSION[$this->session_key_cart]['self_pickup']) ? $_SESSION[$this->session_key_cart]['self_pickup'] : false;

		/**empty session array**/
		$_SESSION[$this->session_key_cart] = array();

		/* specifically epmpy tips too from user session*/
		unset($_SESSION[$this->session_key_userdata]['ctips']);

		/* specifically keep chosen pickup/delivery */
		$_SESSION[$this->session_key_cart]['self_pickup'] = $selected_pickup_option;

		$obj = array();
		if($recalculate){
			$obj['cart'] = $this -> sort_and_calculate_cart($is_checkout);
		}
	return $obj;
	}

	/*******************************************************

		[set session to be pickup or delivery ]

	return bool
	******************************************************/
	function set_pickup($is_pickup){
		$_SESSION[$this->session_key_cart]['self_pickup'] = $is_pickup;

		/* allow to run action on change from pickup to delivery and vice versa*/
		do_action('wppizza_on_pickup_delivery_change', $is_pickup);

	return $is_pickup;
	}
	/*******************************************************

		[check if session is set to be pickup ]

	return bool
	******************************************************/
	function is_pickup(){

		$is_pickup = !empty($_SESSION[$this->session_key_cart]['self_pickup']) ? true : false;

	return $is_pickup;
	}


	/*******************************************************

		[check if session cart is empty]

	return bool
	******************************************************/
	function cart_is_empty(){

		$cart_is_empty = empty($_SESSION[$this->session_key_cart]['items']) ? true : false;

	return $cart_is_empty;
	}

	/*******************************************************

		[check if session cart has items]

	return bool
	******************************************************/
	function cart_has_items(){

		$cart_has_items = !empty($_SESSION[$this->session_key_cart]['items']) ? true : false;

	return $cart_has_items;
	}

	/*******************************************************

		[get sessioned user data formfield inputs etc]

	******************************************************/
	function get_userdata() {
			$session_userdata = $_SESSION[$this->session_key_userdata];
		return $session_userdata;
	}


	/*******************************************************

		[get current gateways selected]

	******************************************************/
	function get_selected_gateway() {
			$gateway_selected = strtolower($_SESSION[$this->session_key_userdata][WPPIZZA_SLUG.'_gateway_selected']);
		return $gateway_selected;
	}


	/*******************************************************

		[(un)set hash of current order for this user]
		stored with normal/other userdata session param
	******************************************************/
	function set_order_hash($hash){
		if(!$hash){
			unset($_SESSION[$this->session_key_userdata][''.WPPIZZA_SLUG.'_hash']);
		}else{
			$_SESSION[$this->session_key_userdata][''.WPPIZZA_SLUG.'_hash'] = $hash;
		}
	}


	/*******************************************************

		[get hash of current order for this user]

	******************************************************/
	function get_order_hash(){
		$hash = '';
		if(!empty($_SESSION[$this->session_key_userdata][''.WPPIZZA_SLUG.'_hash'])){
			$hash = $_SESSION[$this->session_key_userdata][''.WPPIZZA_SLUG.'_hash'];
		}
	return $hash;
	}

	/*******************************************************

		[(un)set order id  of current order for this user]
		stored with normal/other userdata session param
	******************************************************/
	function set_order_id($order_id){
		if(!$order_id){
			unset($_SESSION[$this->session_key_userdata][''.WPPIZZA_SLUG.'_order_id']);
		}else{
			$_SESSION[$this->session_key_userdata][''.WPPIZZA_SLUG.'_order_id'] = $order_id;
		}
	}

	/*******************************************************

		[sanitize and set user data session from formfield inputs or distinctly]

	******************************************************/
	function set_userdata($posted_vars) {
		$is_main_order_page = false;
		/* sanitize all post vars */
		$posted_vars = wppizza_sanitize_post_vars($posted_vars);
		/* gateway selected key */
		$gateway_selected_key = ''.WPPIZZA_SLUG.'_gateway_selected';

		/*
			submitting from main order page
			set user session for fields enabled in orderpage
		*/
		if(!isset($posted_vars[''.WPPIZZA_SLUG.'_confirmationpage'])){
			$is_main_order_page = true; /* indicate it's the main order page */
			/* set allowed keys in main order page*/
			$enabled_formfield_keys = WPPIZZA()->helpers->enabled_formfields(true);
		}

		/*
			ignore confirmation page for session as any checkboxes here should not be sessionised
			submitting from confirmation page
			set user session for fields enabled in confirmation
		*/
		//if($posted_vars[''.WPPIZZA_SLUG.'_checkout_page'] == 'confirmationpage'){
		//	/* set allowed keys in confirmation page*/
		//	$enabled_formfield_keys = WPPIZZA()->helpers->enabled_confirmation_formfields(true);
		//}


		/*
			deal with gateway selection separately
			setting new gateway selection, provided it actually exists and is enabled
		*/
		if($is_main_order_page && isset($posted_vars[$gateway_selected_key])){
			$gw_key = $posted_vars[$gateway_selected_key];
			if(isset(WPPIZZA()->gateways->gwobjects->$gw_key)){
				$_SESSION[$this->session_key_userdata][$gateway_selected_key] = $gw_key;
			}
		}

		/* add to session - enabled formfields depend on whether its main orderpage or confirmation page*/
		if(isset($enabled_formfield_keys) && is_array($enabled_formfield_keys)){
			foreach($enabled_formfield_keys as $key){
				/*
					bypass for gateways selection and tips
					to deal with separately
				*/
				if($key != $gateway_selected_key && $key != 'ctips'){
					$_SESSION[$this->session_key_userdata][$key] = !empty($posted_vars[$key]) ? $posted_vars[$key] : false;
				}
				/* deal with tips separately to allow for 0 in tip field */
				if($key == 'ctips'){
					// updated version
					$tips_value = isset($posted_vars[$key]) ? (wppizza_validate_float_only($posted_vars[$key])) : '' ;
					$_SESSION[$this->session_key_userdata][$key] = ($tips_value >= 0 &&  isset($posted_vars[$key]) &&  is_numeric($posted_vars[$key])) ? $tips_value : false;

					// original: keep for now - works as intended ,  throws php notices though in some circumstances. re-activate if the above "fix" to eliminate php notices has unintended consequences
					//$tips_value = (wppizza_validate_float_only($posted_vars[$key]) );
					//$_SESSION[$this->session_key_userdata][$key] = ($tips_value >= 0 && is_numeric($posted_vars[$key])) ? $tips_value : false;
				}

			}
		}
	return ;
	}

	/*******************************************************

		[add a specific session userdata key/value]

	******************************************************/
	function add_userdata_key($key, $val = true) {
		if(!empty($key)){
			$_SESSION[$this->session_key_userdata][''.WPPIZZA_SLUG.'_'.$key.''] = $val;
		}
	return ;
	}

	/*******************************************************

		[add a specific session userdata variable]

	******************************************************/
	function remove_userdata_key($key) {
		if(isset($_SESSION[$this->session_key_userdata][''.WPPIZZA_SLUG.'_'.$key.''])){
			unset($_SESSION[$this->session_key_userdata][''.WPPIZZA_SLUG.'_'.$key.'']);
		}
	return ;
	}

	/*******************************************************

		[unset distinct user data session key(s)]

	******************************************************/
	function unset_userdata($keys) {
		if(is_array($keys)){
		foreach($keys as $key){
			unset($_SESSION[$this->session_key_userdata][$key]);
		}}else{
			unset($_SESSION[$this->session_key_userdata][$keys]);
		}
	return ;
	}

}
?>