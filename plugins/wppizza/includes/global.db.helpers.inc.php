<?php if ( ! defined( 'ABSPATH' ) ) exit;/*Exit if accessed directly*/ ?>
<?php
/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*
*
*	general helper functions that could be used for 3rd party plugin development
*	or used in custom functions outside wppizza environment (functions.php and watnot)
*
*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\*/

/********************************************************************************
	get completed orders (including failed, unconfirmed, or orders that have subsequently been rejected or refunded)
	from order table(s) depending on arguments set and optionally format
	see documentation at https://docs.wp-pizza.com/developers/?section=function-wppizza_get_orders

	@ since 3.5
	@ param array
	@ return array
************************************************************************************/
function wppizza_get_orders($args = false, $caller = '' ){
	$orders = WPPIZZA() -> db -> get_orders($args, $caller);
return $orders;
}
/********************************************************************************
	update an order by id/blog/hash and optionally selected payment status
	using update_values type/value array
	example:

	$args = array(
		'query' => array(
			'blog_id' => $blog_id,
			'order_id' => $order_id,
			'payment_status' => 'CAPTURED',
		),
		'update_values' => array(
			'order_date' 		=> array('type'=> '%s', 'data' =>date('Y-m-d H:i:s', WPPIZZA_WP_TIME)),
			'order_date_utc' 	=> array('type'=> '%s', 'data' =>date('Y-m-d H:i:s', WPPIZZA_UTC_TIME)),
		),
	);

	@ since 3.8
	@ param array
	@ return bool
************************************************************************************/
function wppizza_update_order($args){
	$blog_id = !empty($args['query']['blog_id']) ? $args['query']['blog_id'] : false;
	$order_id = !empty($args['query']['order_id']) ? $args['query']['order_id'] : false;
	$hash = !empty($args['query']['hash']) ? $args['query']['hash'] : false;
	$update_values = !empty($args['update_values']) ? $args['update_values'] : false;
	$where_payment_status = !empty($args['query']['payment_status']) ? $args['query']['payment_status'] : false;

	/* simply skip if nothing to do */
	if(empty($update_values) || !is_array($update_values) ||  ( !$blog_id && !$order_id && !$hash ) ){
		return true;
	}

	/* update order as required */
	$order_update = WPPIZZA() -> db -> update_order($blog_id, $order_id, $hash, $update_values, $where_payment_status);

return $order_update;
}
/********************************************************************************
	if outputting results from wppizza_get_orders you could use the below to get
	appropriate pagination
	see documentation at https://docs.wp-pizza.com/developers/?section=function-wppizza_get_orders

	@ since 3.5
	@ param int
	@ param int|false
	@ param bool
	@ param false|int
	@ return str
************************************************************************************/
function wppizza_orders_pagination($no_of_orders, $limit, $ellipsis = false, $pagination_info = true, $post_id = false){
	$pagination = WPPIZZA() -> markup_pages -> orderhistory_pagination($no_of_orders, $limit, $ellipsis, $pagination_info, $post_id);
return $pagination;
}

/********************************************************************************
	get all available customer form fields set in wppizza->order form

	default: excluding tips
	optionally, enabled only form fields
	optionally, include confirmation form


	@ since 3.7
	@ param bool
	@ param bool
	@ return array
************************************************************************************/
function wppizza_customer_checkout_fields($args = array('enabled_only' => false, 'confirmation_fields' => false, 'tips_excluded' => true, 'sort' => true)){
	global $wppizza_options;


	$ff = array();

	/* default , get all */
	if(!$args['enabled_only']){
		foreach($wppizza_options['order_form'] as $k=>$arr){
			$ff[$k] = $arr;
		}
	}

	/* if we want enabled only , get them here */
	if($args['enabled_only']){
		foreach($wppizza_options['order_form'] as $k=>$arr){
			if(!empty($arr['enabled'])){
				$ff[$k] = $arr;
			}
		}
	}

	// by default we exclude tips
	if($args['tips_excluded']){
		unset($ff['ctips']);
	}


	/* get confirmation form too */
	if($args['confirmation_fields']){
		foreach($wppizza_options['confirmation_form'] as $k=>$arr){
			$ff[$k] = $arr;
		}
	}
	if($args['sort']){
		asort($ff);
	}

return $ff;
}
/********************************************************************************
	add or update (if exists) meta data for an order

	@ since 3.8
	@ param int
	@ param str
	@ param mixed
	@ return bool false or meta_id we updated/inserted
************************************************************************************/
function wppizza_do_order_meta($order_id = false, $meta_name = false, $meta_value = false){
	$result = WPPIZZA() -> db -> do_order_meta($order_id, $meta_name, $meta_value);
return $result;
}
/********************************************************************************
	delete (if exists) meta data for an order

	@ since 3.8
	@ param int
	@ param str
	@ return bool
************************************************************************************/
function wppizza_delete_order_meta($order_id = false, $meta_name = false){
	$bool = WPPIZZA() -> db -> delete_order_meta($order_id, $meta_name);
return $bool;
}

/********************************************************************************
	delete (if exists) meta key for all orders

	@ since 3.8.4
	@ param str
	@ return bool
************************************************************************************/
function wppizza_delete_order_meta_by_key($meta_key){
	$bool = WPPIZZA() -> db -> delete_order_meta_by_key($meta_key);
return $bool;
}

/********************************************************************************
	get orderid and meta id  from meta table for a specific meta key
	optionally check for a specific value of this meta key too

	@ since 3.8.4
	@param str 						meta key to query
	@param $meta_value  	to query for specific meta value too
	@return array[meta_id] = order_id
************************************************************************************/
function wppizza_get_order_id_by_meta_key($meta_key = false, $meta_value = NULL){
	$array = WPPIZZA() -> db -> get_order_id_by_meta_key($meta_key, $meta_value);
return $array;
}
/********************************************************************************
	get meta data for an order

	@ since 3.8
	@ param int
	@ param str / bool
	@ return array()
************************************************************************************/
function wppizza_get_order_meta($order_id = false, $meta_name = false, $meta_value_only = false){
	$array = WPPIZZA() -> db ->get_order_meta($order_id, $meta_name,  $meta_value_only);
return $array;
}
/********************************************************************************
	get blog info by id helper

	@ since 3.9
	@ param int
	@ return array()
************************************************************************************/
function wppizza_get_blog_details($id){
	$array = WPPIZZA() -> helpers -> wppizza_blog_details($id);
return $array;
}

/********************************************************************************
	get blog date format

	@ since 3.9.2
	@ param void
	@ return array()
************************************************************************************/
function wppizza_get_blog_dateformat(){

	/* get date/time options set */
	$format['date'] = get_option('date_format');
	$format['time'] = get_option('time_format');

return $format;
}

?>