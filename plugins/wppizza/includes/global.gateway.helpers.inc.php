<?php if ( ! defined( 'ABSPATH' ) ) exit;/*Exit if accessed directly*/ ?>
<?php
/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*
*
*
*	gateway helper functions for simplifying gateway developments
*	one day we will put all of this into a separate SDK/Class perhaps (?)
*
*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\*/

/***********************************************************************
*
*	[get cart details for an initialized order (essentially the cart session) - formatted]
*
************************************************************************/
function wppizza_get_initialized_order($wppizza_gateway_ident){
	/***************************************
		ini execute class
	***************************************/
	$ORDER_EXECUTE = new WPPIZZA_ORDER_EXECUTE($wppizza_gateway_ident);
	/***************************************
		get cart details from session data,
	***************************************/
	$order_details = $ORDER_EXECUTE -> get_initialized_order();

return $order_details;
}


/***********************************************************************
*
*	get cart details from session data, update any submitted wppizza formfields and pass on what we need
*	update userdata session, check nonces , update initialized db (by hash), get order formatted  ect
*
***********************************************************************/
function wppizza_order_prepare($wppizza_gateway_ident, $wppizza_post_data){
	/***************************************
		ini execute class
	***************************************/
	$ORDER_EXECUTE = new WPPIZZA_ORDER_EXECUTE($wppizza_gateway_ident, $wppizza_post_data);
	$order_details = $ORDER_EXECUTE -> order_prepare();

return $order_details;
}
/*********************************************************
*
*	[ return order details from db INITIALIZED status() by hash - omitting overkill data - adding mapped user data, if set]
*
*********************************************************/
function wppizza_get_prepared_order($wppizza_gateway_ident){
	/***************************************
		ini execute class
	***************************************/
	$ORDER_EXECUTE = new WPPIZZA_ORDER_EXECUTE($wppizza_gateway_ident);
	$order_details = $ORDER_EXECUTE -> get_prepared_order();

return $order_details ;
}

/*********************************************************
*
*	[ do wppizza_order_prepare and wppizza_get_prepared_order in one go]
*
*********************************************************/
function wppizza_prepare_order_for_payment($wppizza_gateway_ident, $wppizza_post_data){
	/***************************************
		ini execute class
	***************************************/
	$ORDER_EXECUTE = new WPPIZZA_ORDER_EXECUTE($wppizza_gateway_ident, $wppizza_post_data);
	$order_details = $ORDER_EXECUTE -> order_prepare();
	$order_details = $ORDER_EXECUTE -> get_prepared_order();

return $order_details ;
}

/*********************************************************
*
*	[ return order details from db INPROGRESS by id or hash - omitting overkill data - adding mapped user data, if set, bypassing user_id_status]
*
*********************************************************/
function wppizza_get_processed_order($order_id = false, $gateway_reply = false, $wppizza_gateway_ident = false){
	/***************************************
		ini execute class
	***************************************/
	$ORDER_EXECUTE = new WPPIZZA_ORDER_EXECUTE($wppizza_gateway_ident);
	$order_details = $ORDER_EXECUTE -> get_prepared_order($order_id, $gateway_reply, true, false);//* true to set all errors critical here as we are dealing with ipn orders processed and returned from gateway, wpuserid false as ipn will not have any

return $order_details ;
}

/*********************************************************
*
*	[ return order details from db UNCONFIRMED by id ]
*	$error_critical == fals eto not send unfound orders errors here
*********************************************************/
function wppizza_get_unconfirmed_order($order_id = false, $gateway_reply = false, $wppizza_gateway_ident = false, $payment_status = array('UNCONFIRMED'), $error_critical = false){
	/***************************************
		ini execute class
	***************************************/
	$ORDER_EXECUTE = new WPPIZZA_ORDER_EXECUTE($wppizza_gateway_ident);
	$order_details = $ORDER_EXECUTE -> get_completed_order($order_id, $gateway_reply, $error_critical, false, $payment_status);

return $order_details ;
}


/*********************************************************
*
*	[ return order details from db COMPLETED OR REFUNDED (if $payment_status was set)  by id ]
*
*********************************************************/
function wppizza_get_completed_order($order_id = false, $gateway_reply = false, $wppizza_gateway_ident = false, $payment_status = array('COMPLETED')){
	/***************************************
		ini execute class
	***************************************/
	$ORDER_EXECUTE = new WPPIZZA_ORDER_EXECUTE($wppizza_gateway_ident);
	$order_details = $ORDER_EXECUTE -> get_completed_order($order_id, $gateway_reply, true, false, $payment_status);//* true to set all errors critical here as we are dealing with ipn orders processed and returned from gateway, wpuserid false as ipn will not have any

return $order_details ;
}


/*********************************************************
*
*	[ versatile helper to simply query the order table by column key and values ]
*
*********************************************************/
function wppizza_get_order_by_columns($columns = array(), $simplify = false){
	/***************************************
		ini execute class (should probably be moved into WPPIZZA_DB class at some point)
	***************************************/
	$ORDER_EXECUTE = new WPPIZZA_ORDER_EXECUTE(false);
	$order_details = $ORDER_EXECUTE -> get_order_by_columns($columns, $simplify);

return $order_details ;
}

/*********************************************************
*
*	[ execute order (ajax or called directly), returning array with redirect url]
*
*	@order_details sanitized order details
*	@transaction_id string/int
*	@transaction_details string|array (transaction details from errors should also be here)
*	@transaction_errors bool|array
*	@class_name class that called this script (used for logging purposes)
*	@check_user_id null|true|false should order user id be checked against current customers user_id ?
*	@custom_update_columns bool|array() key->val of additional db wppizza orde tanle columns that should be updated or false
*	@unconfirmed true to mark order as unconfirmed instead of completed
*
*	returns array()
*********************************************************/
function wppizza_order_execute($order_details, $transaction_id = null, $transaction_details, $transaction_errors = false, $class_name = false, $check_user_id = null, $custom_update_columns = false, $unconfirmed = null, $send_emails = true){
	/***************************************
		ini execute class
	***************************************/
	$ORDER_EXECUTE = new WPPIZZA_ORDER_EXECUTE($class_name);

	/*
		force $ipn flag to be false
	*/
	$is_ipn = false;

	/*
		execute order
	*/
	$result = $ORDER_EXECUTE->order_execute($order_details, $transaction_id, $transaction_details, $transaction_errors, $is_ipn, $check_user_id, $custom_update_columns, $unconfirmed, $send_emails);

return $result;
}

/*********************************************************
*
*	[ execute order (IPN), returning simply false if error]
*
*	@order_details sanitized order details
*	@transaction_id string/int
*	@transaction_details string|array (transaction details from errors should also be here)
*	@transaction_errors bool|array
*	@class_name class that called this script (used for logging purposes)
*	@check_user_id null|true|false should order user id be checked against current customers user_id ?
*	@custom_update_columns bool|array() key->val of additional db wppizza orde tanle columns that should be updated or false
*	@unconfirmed true to mark order as unconfirmed instead of completed
*
*	returns array()
*********************************************************/
function wppizza_order_execute_ipn($order_details, $transaction_id, $transaction_details, $transaction_errors, $class_name, $custom_update_columns = false, $unconfirmed = null, $send_emails = true){
	/***************************************
		ini execute class
	***************************************/
	$ORDER_EXECUTE = new WPPIZZA_ORDER_EXECUTE($class_name);

	/*
		force $check_user_id flag to be false as it's an ipn request
	*/
	$check_user_id = false;
	/*
		force $ipn flag to be true
	*/
	$is_ipn = true;

	/*
		execute order
	*/
	$result = $ORDER_EXECUTE->order_execute($order_details, $transaction_id, $transaction_details, $transaction_errors,  $is_ipn,  $check_user_id, $custom_update_columns, $unconfirmed, $send_emails );

return $result;
}
/*********************************************************
*
*	[ set order to pendig_payment by id]
*
*********************************************************/
function wppizza_order_payment_pending($order_id, $transaction_details, $order_details, $transaction_id = false, $notes = false ){
	$ORDER_EXECUTE = new WPPIZZA_ORDER_EXECUTE();
	$result = $ORDER_EXECUTE->order_payment_pending($order_id, $transaction_details, $order_details, $transaction_id, $notes);
return $result;
}
/*********************************************************
*
*	[ refund an order by id]
*
*********************************************************/
function wppizza_order_refund($order_id, $transaction_details, $order_details, $notes = false, $amount = false){
	$ORDER_EXECUTE = new WPPIZZA_ORDER_EXECUTE();
	$result = $ORDER_EXECUTE->order_refund($order_id, $transaction_details, $order_details, $notes, $amount );
return $result;
}
/*********************************************************
*
*	[ cancel an order by id]
*
*********************************************************/
function wppizza_order_cancel($order_id, $transaction_details, $order_details, $transaction_id = false, $notes = false ){
	$ORDER_EXECUTE = new WPPIZZA_ORDER_EXECUTE();
	$result = $ORDER_EXECUTE->order_cancel($order_id, $transaction_details, $order_details, $transaction_id, $notes);
return $result;
}
/*********************************************************
*
*	[ reject an order by id]
*
*********************************************************/
function wppizza_order_reject($order_id, $transaction_details, $order_details, $transaction_id = false, $notes = false ){
	$ORDER_EXECUTE = new WPPIZZA_ORDER_EXECUTE();
	$result = $ORDER_EXECUTE->order_reject($order_id, $transaction_details, $order_details, $transaction_id, $notes);
return $result;
}
/*********************************************************
*
*	[ fail an order by id]
*
*********************************************************/
function wppizza_order_failed($order_id, $transaction_details, $order_details, $transaction_id = false , $transaction_errors = false , $display_errors = false ){
	$ORDER_EXECUTE = new WPPIZZA_ORDER_EXECUTE();
	$result = $ORDER_EXECUTE->order_failed($order_id, $transaction_details, $order_details, $transaction_id, $transaction_errors, $display_errors);
return $result;
}
/*********************************************************
*
*	[ expire an order by id]
*
*********************************************************/
function wppizza_order_expired($order_id, $transaction_details, $order_details, $transaction_id = false ){
	$ORDER_EXECUTE = new WPPIZZA_ORDER_EXECUTE();
	$result = $ORDER_EXECUTE->order_expired($order_id, $transaction_details, $order_details, $transaction_id);
return $result;
}
/*********************************************************
*
*	[ update transaction details for an order by id]
*
*********************************************************/
function wppizza_order_update_transaction_details($order_id, $transaction_details, $order_details = false, $transaction_id = false){
	$ORDER_EXECUTE = new WPPIZZA_ORDER_EXECUTE();
	$result = $ORDER_EXECUTE->update_transaction_details($order_id, $transaction_details, $order_details, $transaction_id);
return $result;
}

/*********************************************************
*
*	[ get order email template]
*
*********************************************************/
function wppizza_get_order_email_templates($blog_id = false, $order_id, $order_formatted, $hash = false, $payment_status = false, $user_id = null){

	/****************************************
		get entire order for this purchase
	****************************************/
	$args = array(
		'query' => array(
			'blog_ids' => array($blog_id),
			'order_id' => $order_id ,
			'hash' => $hash ,
			'payment_status' => $payment_status ,
			'wp_user_id' => $user_id ,
		),
		/* add in class idents here as we'll need them for email templates */
		'format' => array(
			'blog_options' => array('localization', 'blog_info', 'date_format'),// add some additional - perhaps useful - info to pass on to gateways
			'sections' => true,//leave order sections in its distinct [section] array
		),
	);
	/*************************************************
		run query, and get results
		even single order results are always arrays
		so simply use reset here
	*************************************************/
	$order = WPPIZZA() -> db -> get_orders($args, 'get_order_email_templates');
	$order = reset($order['orders']);

	/* get email templates outputs */
	$ORDER_EXECUTE = new WPPIZZA_ORDER_EXECUTE();
	$email_templates = $ORDER_EXECUTE -> get_email_templates($order);

return $email_templates;
}

/*********************************************************
*
*	[ set order page url for transactions, add parameters (array) failed etc if required
*	also allow for setting hash distinctly (might be useful for some gateway requirements) ]
*
*********************************************************/
function wppizza_transaction_url($parameters = false, $hash = false){
	global $wppizza_options;

	$args = array();

	if(!empty($hash)){
		$args[WPPIZZA_TRANSACTION_GET_PREFIX] = $hash;
	}else{
		$args[WPPIZZA_TRANSACTION_GET_PREFIX] = WPPIZZA() -> session -> get_order_hash();
	}
	if(!empty($parameters) && is_array($parameters)){
	foreach($parameters as $k=>$v){
		$args[$k] = $v ;
	}}
	$url = add_query_arg($args, get_permalink($wppizza_options['order_settings']['orderpage']) );
return $url;
}
/*********************************************************
*	[ set url for cancelled transactions ]
*	only transactions with "cancel" in query string will be cancelled in db
*********************************************************/
function wppizza_transaction_cancel_url(){
	global $wppizza_options;
	$args = array();
	$args[WPPIZZA_TRANSACTION_CANCEL_PREFIX] = WPPIZZA() -> session -> get_order_hash();
	$url = add_query_arg($args, get_permalink($wppizza_options['order_settings']['orderpage']) );
return $url;
}

/*********************************************************
*	[ simple order page url. optional arguments]
*
*********************************************************/
function wppizza_orderpage_url($parameters = false){
	global $wppizza_options;

	$args = array();
	if(!empty($parameters) && is_array($parameters)){
	foreach($parameters as $k=>$v){
		$args[$k] = $v ;
	}}

	$url = add_query_arg($args, get_permalink($wppizza_options['order_settings']['orderpage']) );

return $url;
}

/******************************************************************
*
*	[get language wpml]
*	returns language if base==true returns only first 2 chars lowercase
*
******************************************************************/
function wppizza_get_language($base=false){
	$lang='en_US';

	$locale = get_locale();
	if($locale !=''){
		$lang = $locale;
	}

	//if(WPLANG!=''){
	//	$lang = WPLANG;
	//}
	/**wpml select of full locale**/
	if(function_exists('icl_object_id') && defined('ICL_LANGUAGE_CODE')) {
		$lang = $sitepress -> get_locale(ICL_LANGUAGE_CODE);/**get full  locale**/
	}
	/**only first 2**/
	if($base){
		$lang=strtolower(substr($lang,0,2));

	}
	return $lang;
}
/******************************************************************
*
*	[statically get currently selected gateway]
*
******************************************************************/
function wppizza_selected_gateway(){
	static $selected_gateway = null;
	if($selected_gateway === null){
		$selected_gateway = strtoupper(WPPIZZA() -> session -> get_selected_gateway());
	}
return $selected_gateway;
}
/***************************************
*	NOT IN USE but could be used if needed
***************************************/
function wppizza_gateway_logging($tx_errors = false, $gateway_reply = false, $order_id = false, $wppizza_gateway_ident = false , $error_ident = false, $logs_path = WPPIZZA_PATH_LOGS){
	/***************************************
		ini execute class
	***************************************/
	$ORDER_EXECUTE = new WPPIZZA_ORDER_EXECUTE($wppizza_gateway_ident);
	$ORDER_EXECUTE->gateway_logging($tx_errors, $gateway_reply, $order_id, false, $error_ident, $logs_path);
}

?>