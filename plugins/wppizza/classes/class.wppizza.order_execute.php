<?php
/**
*  WPPIZZA_ORDER_EXECUTE Class
*
* @package     WPPIZZA
* @subpackage  WPPIZZA_ORDER_EXECUTE
* @copyright   Copyright (c) 2015, Oliver Bach
* @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
* @since       3.0
*
*/
if ( ! defined( 'ABSPATH' ) ) exit;/*Exit if accessed directly*/


/*****************************************************************************************************************************************************
*
*
*
*
*	[WPPIZZA_ORDER_EXECUTE]
*
*
*
*
*****************************************************************************************************************************************************/
class WPPIZZA_ORDER_EXECUTE{

	public $wppizza_gateway_ident;
	public $wppizza_post_data;
	public $wppizza_simplified_order_vars;
	/***********************************************************************************************************
	*
	*
	*
	*	[CONSTRUCTOR]
	*
	*
	*
	************************************************************************************************************/
	function __construct($wppizza_gateway_ident = false, $wppizza_post_data = false) {
		/*
			setting a sensible gateway ident from classname
		*/
		$this->wppizza_gateway_ident = $this->sanitize_gateway_ident($wppizza_gateway_ident);

		/*
			post (user/customer) data submitted from order / confirmation form
		*/
		$this->wppizza_post_data = $this->parse_user_postdata($wppizza_post_data);

		/*
			mapped gateway form data
		*/
		add_filter('wppizza_map_gateway_formfields', array($this, 'map_gateway_formfields'), 10, 2);

		/*
			order prepare errors
		*/
		add_filter('wppizza_order_prepare_errors', array($this, 'order_prepare_errors'), 10, 2);

		/*
			order curency and amount match errors
		*/
		add_filter('wppizza_verify_amount_currency_transactionid', array($this, 'verify_amount_currency_transactionid'), 10, 4);

	}

	/***********************************************************************************************************
	*
	*
	*
	*	[METHODS]
	*
	*
	*
	************************************************************************************************************/
	/*
		get initialized db/order (by hash), get order formatted  ect
	*/
	function get_initialized_order(){

		/***************************************************************
			get user session and check for existence of hash
		***************************************************************/
		$order_hash = $this->get_orderhash();
		if(!$order_hash){
			/* error details */
			$result['error'][] = array(
				'critical'=> false, /* force sending of email to admin */
				'error_id'=> 10005,
				'error_message' => __('Missing hash','wppizza-admin'),
				'wp_error' => ''
			);
			/* logging, and sending */
			$this->gateway_logging($result['error'], false, false, $this->wppizza_gateway_ident, 'prepare-error');

			/* bail - returning error */
			return $result;
		}
		/***************************************************************
			get order and details by hash and INITIALIZED status
		****************************************************************/
		$args = array(
			'query' => array(
				'hash' => $order_hash ,
				'payment_status' => array('INITIALIZED') ,
			),
			'format' => array(
				'blog_options' => array('localization'),// add some additional - perhaps useful - info to pass on to gateways
			),
		);
		//run query, and get results
		$order = WPPIZZA() -> db -> get_orders($args, 'get_initialized_order');
		/*************************************************
			even single order results are always arrays
			so simply use reset here returning formatted order
		*************************************************/
		$order = reset($order['orders']);

		if(empty($order)){
			/* error details */
			$result['error'][] = array(
				'critical'=> false, /* force sending of email to admin */
				'error_id'=> 10006,
				'error_message' => __('Order not found using hash','wppizza-admin'),
				'wp_error' => ''
			);
			/* logging, and sending */
			$this->gateway_logging($result['error'], false, $order_hash, $this->wppizza_gateway_ident, 'prepare-error');

			/* bail - return error array  */
			return $result;
		}

	return $order;
	}
	/*
		update userdata session, check nonces , update initialized db (by hash), get order formatted  ect
	*/
	function order_prepare(){
		global $wppizza_options;

		/*
			get server vars in use
			to capture on error
			@since 3.7
		*/
		$server_vars = !empty($_SERVER) ?  __('SERVER VARS:','wppizza-admin') . ' ' . print_r($_SERVER, true) : __(' === empty _SERVER vars ?! ===', 'wppizza-admin');

		/***************************************************************
			[save and add all user post variables to session
			(including gateway selected adding to existing on confirmation page)]
		***************************************************************/
		$data_posted = $this->set_userdata($this->wppizza_post_data);
		if(!$data_posted){
			/* error details */
			$result['error'][] = array(
				'critical'=> false, /* force sending of email to admin */
				'error_id'=> 20001 ,
				'error_message' => __('No data submitted','wppizza-admin') ,
				'wp_error' => ''
			);

			/* logging, and sending */
			$this->gateway_logging($result['error'], false, false, $this->wppizza_gateway_ident, 'prepare-error');

			/* bail - returning error */
			return $result;
		}

		/***************************************************************
			[get all sessionised user vars to store in db
			(including gateway selected)]
		***************************************************************/
		$customer_data_set = WPPIZZA()->session->get_userdata();

		/*
			allow arbitrary array data to be added/stored  in customer_ini (i.e user session) to
			- perhaps -
			save some additional values without outputting them anywhere by default
			also used when inserting updating from session data, but with different second parameter
		*/
		$customer_data_set = apply_filters('wppizza_filter_add_to_customer_ini', $customer_data_set, 'order_prepare');
		/*
			get gateway selected
		*/
		$gateway_selected = !empty($customer_data_set[''.WPPIZZA_SLUG.'_gateway_selected']) ? $customer_data_set[''.WPPIZZA_SLUG.'_gateway_selected'] : 'gateway-unknown';

		/* unset some distinct values we do not want or need to store in db customr ini*/
		unset($customer_data_set['wppizza_hash']);
		unset($customer_data_set['wppizza_nonce_checkout']);
		unset($customer_data_set['wppizza_confirmationpage']);
		unset($customer_data_set['wppizza_gateway_selected']);
		unset($customer_data_set['_wp_http_referer']);

		/***************************************************************
			[check if we can actually checkout yet]
		***************************************************************/
		$checkout_parameters = WPPIZZA()->session->get_checkout_parameters(true, true);
		if(!$checkout_parameters['can_checkout']){
			/* error details */
			$result['error'][] = array(
				'critical'=> false, /* force sending of email to admin */
				'error_id'=> 20002 ,
				'error_message' => __('Checkout prohibited.' ,'wppizza-admin') . PHP_EOL . print_r($checkout_parameters, true) . PHP_EOL . print_r($server_vars, true),
				'wp_error' => ''
			);
			/* logging, and sending */
			$this->gateway_logging($result['error'], false, false, $this->wppizza_gateway_ident, 'prepare-error');

			/* bail - returning error */
			return $result;
		}
		if(!$checkout_parameters['shop_open']){
			/* error details */
			$result['error'][] = array(
				'critical'=> false, /* force sending of email to admin */
				'error_id'=> 20003 ,
				'error_message' => __('Shop is closed. The customer tried to submit an order after the shop was closed (probably staying for a long time on the order page). This is just a notice and does not constitute an error. ','wppizza-admin'),
				'wp_error' => ''
			);
			/* logging, and sending */
			$this->gateway_logging($result['error'], false, false, $this->wppizza_gateway_ident, 'prepare-error');

			/* bail - returning error */
			return $result;
		}

		/***************************************************************
			verify posted nonce
		***************************************************************/
		$valid_nonce = $this->verify_nonce($data_posted);
		if(!$valid_nonce){
			/* error details */
			$result['error'][] = array(
				'critical'=> false, /* force sending of email to admin */
				'error_id'=> 20004 ,
				'error_message' => __('Invalid nonce', 'wppizza-admin'),
				'wp_error' => ''
			);
			/* logging, and sending */
			$this->gateway_logging($result['error'], false, false, $this->wppizza_gateway_ident, 'prepare-error');

			/* bail - returning error */
			return $result;
		}

		/***************************************************************
			get user session and check for existence of hash
		***************************************************************/
		$order_hash = $this->get_orderhash();
		if(!$order_hash){
			/* error details */
			$result['error'][] = array(
				'critical'=> false, /* force sending of email to admin */
				'error_id'=> 20005,
				'error_message' => __('Missing hash','wppizza-admin'). PHP_EOL . print_r($server_vars, true),
				'wp_error' => ''
			);
			/* logging, and sending */
			$this->gateway_logging($result['error'], false, false, $this->wppizza_gateway_ident, 'prepare-error');

			/* bail - returning error */
			return $result;
		}

		/***************************************************************
			get order and details by hash and INITIALIZED status
		****************************************************************/
		$args = array(
			'query' => array(
				'hash' => $order_hash ,
				'payment_status' => array('INITIALIZED') ,
			),
			'format' => false ,//overhead of formatting is not necessary here
		);
		//run query, and get results
		$get_orders = WPPIZZA() -> db -> get_orders($args, 'order_prepare_initialized');
		/*************************************************
			even single order results are always arrays
			so simply use reset here
		*************************************************/
		$order = reset($get_orders['orders']);
		$order_id = $order['id'];

		if(empty($order)){
			/* error details */
			$result['error'][] = array(
				'critical'=> false, /* force sending of email to admin */
				'error_id'=> 20006,
				'error_message' => __('Order not found using hash','wppizza-admin'),
				'wp_error' => ''
			);
			/* logging, and sending */
			$this->gateway_logging($result['error'], false, $order_hash, $this->wppizza_gateway_ident, 'prepare-error');

			/* bail - return error array  */
			return $result;

		}else{

			/***************************************************************
				update db values with customer submitted data
			****************************************************************/
			$update_db_values = array();
			/** amend order date / order update */
			$update_db_values['order_date'] 		= array('type'=> '%s', 'data' =>date('Y-m-d H:i:s', WPPIZZA_WP_TIME));
			$update_db_values['order_date_utc'] 		= array('type'=> '%s', 'data' =>date('Y-m-d H:i:s', WPPIZZA_UTC_TIME));
			//$update_db_values['order_update'] 		= array('type'=> '%s', 'data' =>date('Y-m-d H:i:s', WPPIZZA_WP_TIME));
			/* update customer post/sessionised vars */
			$update_db_values['customer_ini'] 		= array('type'=> '%s', 'data' =>maybe_serialize($customer_data_set));
			/* initiator uppercase */
			$update_db_values['initiator'] 		= array('type'=> '%s', 'data' =>strtoupper($gateway_selected));/* insert the gateway post var here */
			/* set status, INPROGRESS */
			$update_db_values['payment_status'] 	= array('type'=> '%s', 'data' => 'INPROGRESS');

			/**
				added filtering (since 3.2.7) - not used in plugin
				to allow other plugins to add their own data if - for example - they
				have added their own columns (or indeed change what goes in it)
			**/
			$update_db_values = apply_filters('wppizza_filter_db_column_data', $update_db_values, $customer_data_set);


			$order_update = WPPIZZA()->db->update_order(false, $order_id, false , $update_db_values, 'INITIALIZED');
			if(!$order_update){
				/* error details */
				$result['error'][] = array(
					'critical'=> true, /* force sending of email to admin */
					'error_id'=> 20007,
					'error_message' => __('DB: Could not update order: customer details, INITIALIZED => INPROGRESS','wppizza-admin'),
					'wp_error' => ''
				);

				/* logging, and sending */
				$this->gateway_logging($result['error'], false, $order_id, $this->wppizza_gateway_ident, 'prepare-error');

				/* bail - return error array */
				return $result;
			}
			/*
				get order details of now updated in progress order
			*/
			$args = array(
				'query' => array(
					'order_id' => $order_id ,
					'payment_status' => array('INPROGRESS') ,
				),
				/* add in class idents here as we'll need them for email templates */
				'format' => array(
					'blog_options' => array('localization', 'blog_info', 'date_format'),// add some additional - perhaps useful - info to pass on to gateways
				),
			);
			/*************************************************
				run query, and get results
				even single order results are always arrays
				so simply use reset here
			*************************************************/
			$order = WPPIZZA() -> db -> get_orders($args, 'order_prepare_inprogress');
			$order = reset($order['orders']);

			if(empty($order)){
				/* error details */
				$result['error'][] = array(
					'critical'=> false, /* force sending of email to admin */
					'error_id'=> 20008,
					'error_message' => __('Order not found using ID ','wppizza-admin'),
					'wp_error' => ''
				);
				/* logging, and sending */
				$this->gateway_logging($result['error'], false, $order_id, $this->wppizza_gateway_ident, 'prepare-error');

				/* bail - return error array  */
				return $result;
			}


			/*
				allow to add meta data to order on execute - i.e when an order gets submitted
			*/
			do_action('wppizza_add_order_meta', $order_id);

			/**
				return formatted order
				includes 'localization', 'blog_info', 'date_format'
			**/
			$result['order'] = $order;

		}

	return $result;
	}

	/*********************************************************************************************************
	*
	*
	*	redirect order to payment processor (if set)
	*
	*
	*********************************************************************************************************/
	function order_redirect($order_details){


		/**********************************************************
			check if class exists and has method "payment_redirect"
		***********************************************************/
		$gw_id_upper = strtoupper($this -> wppizza_gateway_ident.'');/* just for tidyness sake - although class names are case-insensitive */
		$gw_id_lower = strtolower($this -> wppizza_gateway_ident.'');
		if (class_exists('WPPIZZA_GATEWAY_'.$gw_id_upper)){
		    $class_name = 'WPPIZZA_GATEWAY_'.$gw_id_upper.'' ;
		    $gateway_class = new $class_name;
		    $gateway_class -> gatewayIdent = $gw_id_lower;/* add lowercase gatewayIdent - dont change this */
		    /***********************************************************
		    	check if payment_redirect method exists in class
		    ***********************************************************/
		    if(method_exists($gateway_class, 'payment_redirect')){

				/********
					build form or redirect url
				********/
				$build_redirect =  $gateway_class -> payment_redirect($order_details);

				$gateway_redirect = array();
				/*
					build form if action == post
				*/
				if(strtolower($build_redirect['action']) == 'post'){

					$gateway_redirect['gateway']['form'] = '<form id="'.WPPIZZA_SLUG.'_gateway_post" action="'.$build_redirect['url'].'" method="POST" accept-charset="UTF-8">'.PHP_EOL;

						if(!empty($build_redirect['parameters'])){
							/* single or up to 2-dimensions (that should really do) */
							foreach($build_redirect['parameters'] as $pKey => $pVal){

								/* simple single dimension array to inputs */
								if(!is_array($pVal)){
									$gateway_redirect['gateway']['form'] .= '<input type="hidden" name="'.$pKey.'" value="'.$pVal.'" />'.PHP_EOL;
								}

								/* if 2-dimensional (that ought to be anough) */
								if(is_array($pVal)){
									foreach($pVal as $pKey2 => $pVal2){
										$gateway_redirect['gateway']['form'] .= '<input type="hidden" name="'.$pKey2.'" value="'.$pVal2.'" />'.PHP_EOL;
									}
								}

							}
						}

					$gateway_redirect['gateway']['form'] .= '</form>'.PHP_EOL;
				}

				/*
					build redirect url string if action == get
				*/
				if(strtolower($build_redirect['action']) == 'get'){
					/*
						if any parameters are set , else just use url set
						make sure parameters are trimmed or some gateways
						will throw a tantrum
					*/
					if(!empty($build_redirect['parameters'])){
						$trimmed_parameters = array();
						foreach($build_redirect['parameters'] as $paramKey => $paramVal){
							$trimmed_parameters[$paramKey] = trim($paramVal);
						}
					}
					$build_query = !empty($build_redirect['parameters']) ? '?'.http_build_query($trimmed_parameters,'','&') : '';
					$redirect_url = !empty($build_redirect['url']) ? $build_redirect['url'] : '';
					$gateway_redirect['gateway']['redirect'] = $redirect_url . $build_query ;

				}

				/*
					make sure there's something build to redirect to
				*/
				if(empty($gateway_redirect) || !empty($build_redirect['error'])){
					/* error details */
					$result['error'][] = array(
						'critical'=> false, /* force sending of email to admin */
						'error_id'=> 20010 ,
						'error_message' => __('Building of redirect form or url failed','wppizza-admin'),
						'wp_error' => ''
					);

					if(!empty($build_redirect['error'])){
						/* error details */
						if(is_array($build_redirect['error'])){
							foreach($build_redirect['error'] as $build_error){
								$result['error'][] = array(
									'critical'=> false, /* force sending of email to admin */
									'error_id'=> print_r($build_error['error_id'], true) ,
									'error_message' =>  print_r($build_error['error_message'], true),
									'wp_error' => ''
								);
							}
						}
						else
						{
							$result['error'][] = array(
								'critical'=> false, /* force sending of email to admin */
								'error_id'=> 20011 ,
								'error_message' =>  print_r($build_redirect['error'], true),
								'wp_error' => ''
							);
						}

					}

					/* logging, and sending */
					$this->gateway_logging($result['error'], false, false, $this->wppizza_gateway_ident, 'redirect-error');

					/* bail - returning error */
					return $result;
				}

				/***************************************************************
					if we are storing the parameters submitted
					to perhaps do something with it like comparing them later on payment
					between whats stored here and what gateway sends back
					some gateways need this ability
				***************************************************************/
				if(!empty($build_redirect['parameters']) && !empty($build_redirect['store_parameters'])){

					$update_db_values = array();
					/** amend order date / order update */
					$update_db_values['order_date'] 		= array('type'=> '%s', 'data' =>date('Y-m-d H:i:s', WPPIZZA_WP_TIME));
					$update_db_values['order_date_utc'] 		= array('type'=> '%s', 'data' =>date('Y-m-d H:i:s', WPPIZZA_UTC_TIME));
					/** store parameters */
					$update_db_values['transaction_details'] 	= array('type'=> '%s', 'data' => maybe_serialize($build_redirect['parameters']));

					$order_update_transaction_details = WPPIZZA()->db->update_order(false, $order_details['ordervars']['order_id']['value'], false , $update_db_values, 'INPROGRESS');
					if(!$order_update_transaction_details){
							/* error details */
							$result['error'][] = array(
								'critical'=> true, /* force sending of email to admin */
								'error_id'=> 20012,
								'error_message' => __('DB: Could not update transaction details','wppizza-admin'),
								'wp_error' => ''
							);
						/* logging, and sending */
						$this->gateway_logging($result['error'], false, false, $this->wppizza_gateway_ident, 'redirect-error');

						/* bail - returning error */
						return $result;
					}
				}

				/***************************************************************
					simply storing some parameters without submitting any via get/post
					to perhaps do something with it like comparing them later on payment
					between whats stored here and what gateway sends back
					some gateways need this ability
				***************************************************************/
				if(empty($build_redirect['parameters']) && !empty($build_redirect['store_parameters'])){
					$update_db_values = array();
					/** amend order date / order update */
					$update_db_values['order_date'] 		= array('type'=> '%s', 'data' =>date('Y-m-d H:i:s', WPPIZZA_WP_TIME));
					$update_db_values['order_date_utc'] 		= array('type'=> '%s', 'data' =>date('Y-m-d H:i:s', WPPIZZA_UTC_TIME));
					/** store parameters */
					$update_db_values['transaction_details'] 	= array('type'=> '%s', 'data' => maybe_serialize($build_redirect['store_parameters']));

					$order_update_transaction_details = WPPIZZA()->db->update_order(false, $order_details['ordervars']['order_id']['value'], false , $update_db_values, 'INPROGRESS');
					if(!$order_update_transaction_details){
							/* error details */
							$result['error'][] = array(
								'critical'=> true, /* force sending of email to admin */
								'error_id'=> 20013,
								'error_message' => __('DB: Could not update transaction details','wppizza-admin'),
								'wp_error' => ''
							);
						/* logging, and sending */
						$this->gateway_logging($result['error'], false, false, $this->wppizza_gateway_ident, 'redirect-error');

						/* bail - returning error */
						return $result;
					}


				}


			return $gateway_redirect;
			}
		}
	return;
	}

	/*********************************************************************************************************
	*
	*
	*	EXECUTE ORDER
	*
	*	@unconfirmed @bool if true sets status to UNCONFIRMED instead of COMPLETED
	*********************************************************************************************************/
	function order_execute($order_details, $tx_id = null, $tx_details = false, $tx_errors = false, $ipn = false, $check_user_id = null, $custom_update_columns = false, $unconfirmed = null, $send_emails = true){
		global $wppizza_options, $wpdb;


		/************************************************************
			get session hash for redirect - omit for ipn execute
		************************************************************/
		$order_hash = empty($ipn) ? $this->get_orderhash() : '' ;


		/************************************************************
			map order details to vars for convenience
		************************************************************/
		$order_id = $order_details['ordervars']['order_id']['value'];
		$wp_user_id = $order_details['ordervars']['wp_user_id']['value'];
		$user_create_account = $order_details['ordervars']['create_account']['value'];
		$user_update_profile = $order_details['ordervars']['update_profile']['value'];
		$order_initiator = $order_details['ordervars']['payment_gateway']['value'];
		$ipn_error_prefix = !empty($ipn) ? '[IPN] ' : '';

		/************************************************************
			set a made up tx id if false (only for COD really, else this should be set (even if empty))
		************************************************************/
		$tx_id 	= ($tx_id === null) ? $order_initiator . WPPIZZA_WP_TIME .  $order_id : maybe_serialize($tx_id) ;

		/************************************************************
			action args
		************************************************************/
		$action_args = array(
			'order_id' => $order_id,
			'order_details' => $order_details ,
			'transaction_id' => $tx_id,
			'is_ipn' => $ipn,
		);

		/***************************************************************
			if we have some errors submitted (by a gateway payment for example),
			update db from INITIALZED to FAILED using order_id !
			and append error to redirect url or simply return false for ipns
		****************************************************************/
		if(!empty($tx_errors)){


			$update_db_values = array();
			/** amend order date / order update */
			$update_db_values['order_date'] 		= array('type'=> '%s', 'data' =>date('Y-m-d H:i:s', WPPIZZA_WP_TIME));
			$update_db_values['order_date_utc'] 	= array('type'=> '%s', 'data' =>date('Y-m-d H:i:s', WPPIZZA_UTC_TIME));
			/** let's make up some COD transaction id */
			$update_db_values['transaction_id'] 	= array('type'=> '%s', 'data' => $tx_id );
			/** save errors */
			$update_db_values['transaction_errors'] = array('type'=> '%s', 'data' => maybe_serialize($tx_details) );
			/** display errors, if any **/
			$update_db_values['display_errors'] = array('type'=> '%s', 'data' => maybe_serialize($tx_errors) );
			/* set status, FAILED */
			$update_db_values['payment_status'] 	= array('type'=> '%s', 'data' => 'FAILED');


			/*
				update db to failed
			*/
			$order_update_failed = WPPIZZA()->db->update_order(false, $order_id, false , $update_db_values, 'INPROGRESS');
			if(!$order_update_failed){
				/* error details */
				$result['error'][] = array(
					'critical'=> true, /* force sending of email to admin */
					'error_id'=> 10000,
					'error_message' => $ipn_error_prefix . __('DB: Could not update order: INITIALIZED => FAILED','wppizza-admin'),
					'wp_error' => ''
				);
				/* logging, and sending */
				$this->gateway_logging($result['error'], $tx_details, $order_id, $order_initiator, 'execute-error', $update_db_values);

				/* bail - return error array or simply return false for ipn*/
				if($ipn){
					return false;
				}else{
					return $result;
				}
			}

			/*
				log "normal" gateway tx verification failed errors
			*/
			$this->gateway_logging($tx_errors, $tx_details, $order_id, $order_initiator, 'transaction-failed');

			/*
				bail and redirect or simply return false for ipn
			*/
			if($ipn){
				return false;
			}else{
				$redirect_url = $this->set_redirect_url($order_hash, true);
				$result['redirect_url'] = $redirect_url;
			}

		return $result;
		}


		/***************************************************************
			update db from INITIALZED to CAPTURED
			(or UNCONFIRMED for gateways that require user interaction/confirmation)
			by hash in session
		****************************************************************/
		$update_db_values = array();

		/** additional user specified columns to update before the ones below to never overwrite them**/
		if(!empty($custom_update_columns) && is_array($custom_update_columns)){
			foreach($custom_update_columns as $key => $array){
				$update_db_values[$key] 		= array('type'=> $array['type'], 'data' => $array['data']);
			}
		}

		/** amend order date / order update */
		$update_db_values['order_date'] 		= array('type'=> '%s', 'data' =>date('Y-m-d H:i:s', WPPIZZA_WP_TIME));
		$update_db_values['order_date_utc'] 	= array('type'=> '%s', 'data' =>date('Y-m-d H:i:s', WPPIZZA_UTC_TIME));
		/** some made up COD transaction id if there isnt one*/
		$update_db_values['transaction_id'] 	= array('type'=> '%s', 'data' => $tx_id);
		/** insert transaction details if we have some, else a simple SUCCESS  (though this does not indicate the emails were sent) */
		$update_db_values['transaction_details'] = array('type'=> '%s', 'data' => ( empty($tx_details) ? 'SUCCESS' : maybe_serialize($tx_details) ) );
		/* set status, captured | unconfirmed */
		$order_payment_status  = ($unconfirmed === null) ? 'CAPTURED' : (($unconfirmed === true) ? 'UNCONFIRMED' : 'CONFIRMED' )  ;
		$update_db_values['payment_status'] 	=  array('type'=> '%s', 'data' => $order_payment_status);

		$inprogress_status = ($unconfirmed === null || $unconfirmed === true ) ? 'INPROGRESS' : 'UNCONFIRMED' ;
		$order_update_captured = WPPIZZA()->db->update_order(false, $order_id, false , $update_db_values, $inprogress_status);
		if(!$order_update_captured){
			/* error details */
			$result['error'][] = array(
				'critical'=> true, /* force sending of email to admin */
				'error_id'=> 10001,
				'error_message' => $ipn_error_prefix . __('DB: Could not update order: INITIALIZED|INPROGRESS => CAPTURED|UNCONFIRMED|CONFIRMED','wppizza-admin'),
				'wp_error' => '',
				'wp_last_query' => print_r($wpdb->last_query, true)
			);
			/* logging, and sending */
			$this->gateway_logging($result['error'], $tx_details, $order_id, $order_initiator, 'execute-error', $update_db_values);

			/* bail - return error array or simply return false for ipn*/
			if($ipn){
				return false;
			}else{
				return $result;
			}
		}

		/*************************************************************
			only continue executing the order here  if filter returns false

			@since 3.8
			order_payment_status will be (captured, unconfirmed or confirmed)
		*************************************************************/
		if(has_filter('wppizza_execute_update_order_to_'.strtolower($order_payment_status).'')) {
			$exit_execution = apply_filters('wppizza_execute_update_order_to_'.strtolower($order_payment_status).'', false, strtolower($order_payment_status), $action_args );
			if($exit_execution === true){
				if($ipn){
					return false;
				}else{
					$redirect_url = $this->set_redirect_url($order_hash, false);
					$result['redirect_url'] = $redirect_url;
					return $result;
				}
			}
		}

		/*
			get the final order data after all updates before sending emails
		*/
		$captured_status = ($unconfirmed === null) ? 'CAPTURED' : (($unconfirmed === true) ? 'UNCONFIRMED' : 'CONFIRMED' );
		$args = array(
			'query' => array(
				'order_id' => $order_id ,
				'payment_status' => $captured_status ,
			),
			/* add in class idents here as we'll need them for email templates */
			'format' => array(
				'blog_options' => array('localization', 'blog_info', 'date_format', 'blog_options'),// add some additional - perhaps useful - info to pass on to gateways , including full blog options usable by filters below perhaps
				'sections' => true,//leave order sections in its distinct [section] array
			),
		);
		//run query, and get results
		$order_details = WPPIZZA() -> db -> get_orders($args, 'order_execute');
		$order_details = reset($order_details['orders']);//only get this single order


		/***************************************************************
			get array of emails we need to send based on templates assigned
		***************************************************************/
		$email_templates = $this->get_email_templates($order_details);

		if(isset($email_templates['error'])){
			/* error details */
			$result['error'][] = array(
				'critical'=> true, /* force sending of email to admin */
				'error_id'=> 10002,
				'error_message' => $ipn_error_prefix . __('TEMPLATES: '.print_r($email_templates['error'], true).'','wppizza-admin'),
				'wp_error' => '',
				'wp_last_query' => print_r($wpdb->last_query, true)
			);
			/* logging, and sending */
			$this->gateway_logging($result['error'], $tx_details, $order_id, $order_initiator, 'execute-error');
			/* bail - return error array or simply return false for ipn*/
			if($ipn){
				return false;
			}else{
				return $result;
			}
		}

		/***************************************************************
			send each email - $email_results should return an empty array
		***************************************************************/
		/*
			allow for email sending to be skipped if a plugin wants to send its own
		*/
		if($send_emails === true ){


			$email_results = WPPIZZA()->email->send($email_templates, $order_details);

			/*
				throw errors if no email was sent to shop
			*/
			if(isset($email_results['shop'])){
				/* error details */
				$result['error'][] = array(
					'critical'=> true, /* force sending of email to admin */
					'error_id'=> 10003,
					'error_message' => $ipn_error_prefix .  __('EMAIL TO SHOP FAILED: '.print_r(nl2br(print_r($email_results['shop'], true)), true).'','wppizza-admin'),
					'wp_error' => '',
					'wp_last_query' => print_r($wpdb->last_query, true)

				);
				/* logging, and sending */
				$this->gateway_logging($result['error'], $tx_details, $order_id, $order_initiator, 'execute-error');
				/* bail - return error array or simply return false for ipn*/
				if($ipn){
					return false;
				}else{
					return $result;
				}
			}
		}


		/***************************************************************
			allow plugins to send their own (perhaps additional) emails
			(possibly in conjunction with setting $send_emails to false)
			@ since 3.1.7
			filter should return true (on success of email sent) or
			$send_emails[error] => "error message" ;
		***************************************************************/
		$send_emails = apply_filters('wppizza_on_order_execute_send_email', $send_emails, $order_details , $tx_id, $tx_details, $email_templates);

		/***************************************************************
			set update data - set to completed as anything major will stop execution
			before we get here
		****************************************************************/
		$update_db_values = array();

		/** amend order date and order update */
		$update_db_values['order_date'] 		= array('type'=> '%s', 'data' =>date('Y-m-d H:i:s', WPPIZZA_WP_TIME));
		$update_db_values['order_date_utc'] 	= array('type'=> '%s', 'data' =>date('Y-m-d H:i:s', WPPIZZA_UTC_TIME));

		/** set email results **/
		$update_db_values['mail_sent'] = array('type'=> '%s', 'data' => (($send_emails === true ) ? 'Y' : 'N') );
		$update_db_values['mail_error'] = array('type'=> '%s', 'data' => ( isset($email_results['error']) ? print_r($email_results['error'], true) : '' ));


		/** save shop template plaintext output to db **/
		$update_db_values['customer_details'] = array('type'=> '%s', 'data' => $email_templates['shop']['db_plaintext']['customer']);
		$update_db_values['order_details'] = array('type'=> '%s', 'data' => $email_templates['shop']['db_plaintext']['order'] . PHP_EOL . PHP_EOL . $email_templates['shop']['db_plaintext']['summary']);


		/* set status, completed|unconfirmed  */
		$completed_status = ($unconfirmed === null || $unconfirmed === false  ) ? 'COMPLETED' : 'UNCONFIRMED';
		$update_db_values['payment_status'] = array('type'=> '%s', 'data' => $completed_status ) ;

		/** clear any previous errors to not confuse things **/
		$update_db_values['display_errors'] 	= array('type'=> '%s', 'data' => NULL);
		$update_db_values['transaction_errors'] 	= array('type'=> '%s', 'data' => NULL);


		/***************************************************************
			update db to from CAPTURED to COMPLETED or FAILED etc
			updating email_sent filed and email_errors too
		****************************************************************/
		/* if we are dealing with unconfirmed statusses, search/update those */
		$captured_status = ($unconfirmed === null) ? 'CAPTURED' : (($unconfirmed === true) ? 'UNCONFIRMED' : 'CONFIRMED' ) ;
		$order_complete = WPPIZZA()->db->update_order(false, $order_id, false , $update_db_values, $captured_status);
		if(!$order_complete){
			/* error details */
			$result['error'][] = array(
				'critical'=> true, /* force sending of email to admin */
				'error_id'=> 10004,
				'error_message' => $ipn_error_prefix . __('DB: Could not update order: CAPTURED => COMPLETED','wppizza-admin'),
				'wp_error' => '',
				'wp_last_query' => print_r($wpdb->last_query, true)
			);
			/* logging, and sending */
			$this->gateway_logging($result['error'], $tx_details, $order_id, $order_initiator, 'execute-error', $update_db_values);

			/* bail - return error array or simply return false for ipn*/
			if($ipn){
				return false;
			}else{
				return $result;
			}
		}else{
			if(!empty($wppizza_options['settings']['log_successful_orders'])){
				/* log success too */
				$success = $email_templates['shop']['db_plaintext']['customer'] . PHP_EOL . $email_templates['shop']['db_plaintext']['order'];
				$this->gateway_logging(false, $success , $order_id, $order_initiator, 'success');
			}
		}

		/***************************************************************

			create account / update  profile

		****************************************************************/



		/***************************************************************
			create new account if set - will also update profile
		***************************************************************/
		if(!empty($user_create_account) && empty($wp_user_id)){
			WPPIZZA()->user->create_account($order_id, $order_details['sections']['customer']);
		}
		/***************************************************************
			update user profile if set
			only if $wp_user_id > 0 && $user_update_profile
		***************************************************************/
		if(!empty($user_update_profile) && !empty($wp_user_id)){
			WPPIZZA()->user->update_profile($wp_user_id, $order_details['sections']['customer']);
		}

		/**************************************************************
			wppizza_on_order_execute - action hook - only run on completed
			action hook - add order formatted and templates output(if set)
		**************************************************************/
		if(has_action('wppizza_on_order_execute') && $completed_status == 'COMPLETED') {


			/*
				get final completed order parameters
			*/
			$args = array(
				'query' => array(
					'order_id' => $order_id ,
					'payment_status' => $completed_status ,
				),
				/* add in class idents here as we'll need them for email templates */
				'format' => array(
					'blog_options' => array('localization', 'blog_info', 'date_format'),// add some additional - perhaps useful - info to pass on to gateways
					'sections' => true,//leave order sections in its distinct [section] array
				),
			);
			//run query, and get results
			$order_details = WPPIZZA() -> db -> get_orders($args, 'order_templates');
			$order_details = reset($order_details['orders']);//only get this single order


			/*  filter , depending on parameters set,  returns array(with order id, order deails , order formatted , selected print template output) */
			$template_ids = apply_filters('wppizza_on_order_execute_get_print_templates_by_id', array());


			/** get print templates array to add to on order execute if set above */
			$print_templates = $this->get_print_templates($template_ids, $order_id, $order_details);

			/* hook into this, passing on sections data only (backwards compatibility) as well as full details as 4th parameter */
			do_action('wppizza_on_order_execute', $order_id, $order_details['sections'], $print_templates, $order_details);
		}
		/***********************************************************
			dev, show output instead of sending - if not ipn
		***********************************************************/
		if(WPPIZZA_DEV_VIEW_EMAIL_OUTPUT && !$ipn){
			$email_details['output'] = '';

			foreach($email_templates as $key=>$vars){
				$show = array();
				$show['Template_ID'] 	= $vars['tpl_id'];
				$show['Key'] 			= $key;
				$show['Subject'] 		= $vars['Subject'];
				$show['SetFrom'] 		= $vars['SetFrom'];
				$show['AddAddress'] 	= $vars['AddAddress'];
				$show['AddCC'] 			= $vars['AddCC'];
				$show['AddReplyTo']		= $vars['AddReplyTo'];
				$show['AddBCC'] 		= $vars['AddBCC'];
				$show['AddAttachment']	= $vars['AddAttachment'];
				$show['Plaintext'] 		= '<textarea style="width:100%; min-width:600px; height:350px; font-family:monospace; font-size:medium">' . $vars['AltBody'] . '</textarea>';
				if(!empty($vars['MsgHTML'])){
					$show['Html'] 		= '<iframe srcdoc="'.str_replace('"','\'',$vars['MsgHTML']).'" src="" width="100%" height="600"></iframe>';
				}

				$email_details['output'] .= '<pre>' . print_r($show, true) . '</pre>';
			}
			return $email_details;
		}


		/***********************************************************
			set redirect url
		***********************************************************/
		$redirect_url = $this->set_redirect_url($order_hash);
		$result['redirect_url'] = $redirect_url;
		/* on success - return redirect url etc or simply return true for ipn*/
		if($ipn){
			return true;
		}else{
			return $result;
		}
	}

	/*********************************************************************************************************
	*
	*
	*	UPDATE ORDER REFUNDS
	*
	*
	*********************************************************************************************************/
	function order_refund($order_id, $tx_details = false, $order_details = false, $notes = false, $refund_amount = false){

		$update_tx_details = array();
		$update_tx_details[] = (!empty($order_details['ordervars']['tansaction_details']['value'])) ? maybe_unserialize($order_details['ordervars']['tansaction_details']['value']) : '' ;/* previous tx details */
		$update_tx_details[]= maybe_unserialize($tx_details);/* new tx details */

		/***************************************************************
			set/add refunds to order table if we have one
		****************************************************************/
		$sum_refund = (float)$order_details['ordervars']['order_refund']['value'];
		if(!empty($refund_amount) && is_numeric($refund_amount)){
			$sum_refund += (float)$refund_amount;
		}

		/***************************************************************
			add additional notes to current notes , if set
		****************************************************************/
		$update_notes = $order_details['ordervars']['notes']['value_formatted'];
		if(!empty($notes) && is_string($notes)){
			/* add line break */
			$update_notes = !empty($update_notes) ? $update_notes . PHP_EOL : '' ;
			/* add additional notes as set */
			$update_notes .= $notes ;
		}



		/***************************************************************
			set update data
		****************************************************************/
		$update_db_values = array();

		/** amend order date and order update */
		$update_db_values['order_update'] 		= array('type'=> '%s', 'data' =>date('Y-m-d H:i:s', WPPIZZA_WP_TIME));

		/** transaction details id */
		$update_db_values['transaction_details'] = array('type'=> '%s', 'data' => ( empty($update_tx_details) ? '' : maybe_serialize($update_tx_details) ) );

		/* set payment status, refunded  */
		$update_db_values['payment_status'] 	= array('type'=> '%s', 'data' => 'REFUNDED');

		/* set order status, refunded  */
		$update_db_values['order_status'] 	= array('type'=> '%s', 'data' => 'REFUNDED');

		/* set refund amount (summed) */
		$update_db_values['order_refund'] 	= array('type'=> '%f', 'data' => $sum_refund);

		/* add/update notes too if set */
		$update_db_values['notes'] = array('type'=> '%s', 'data' => ( empty($update_notes) ? '' : maybe_serialize($update_notes) ) );	//notes should never be an array really, but just in case, we'll serialize it if needs be


		$refund = WPPIZZA() -> db -> update_order(false, $order_id, false, $update_db_values);

	return $refund;
	}

	/*********************************************************************************************************
	*
	*
	*	UPDATE ORDER CANCELLED
	*
	*
	*********************************************************************************************************/
	function order_cancel($order_id, $transaction_details = false, $order_details = false, $transaction_id = false, $notes = false){

		/***************************************************************
			set update data - set to completed as anything major will stop execution
			before we get here
		****************************************************************/
		$update_db_values = array();

		/** amend order date and order update */
		$update_db_values['order_update'] 		= array('type'=> '%s', 'data' =>date('Y-m-d H:i:s', WPPIZZA_WP_TIME));

		/** transaction details */
		$update_db_values['transaction_details'] = array('type'=> '%s', 'data' => ( empty($transaction_details) ? '' : maybe_serialize($transaction_details) ) );

		/** transaction id */
		$update_db_values['transaction_id'] 	= array('type'=> '%s', 'data' => (!empty($transaction_id) ? maybe_serialize($transaction_id) : null) );


		/* set payment status, cancelled  */
		$update_db_values['payment_status'] 	= array('type'=> '%s', 'data' => 'CANCELLED');

		/* add to notes if set */
		$update_db_values['notes'] = array('type'=> '%s', 'data' => ( empty($notes) ? '' : maybe_serialize($notes) ) );	//notes should never be an array really, but just in case, we'll serialize it if needs be

		/** update order **/
		$update_order = WPPIZZA() -> db -> update_order(false, $order_id, false, $update_db_values);


	return $update_order;
	}


	/*********************************************************************************************************
	*
	*
	*	UPDATE ORDER PAYMENT_PENDING
	*
	*
	*********************************************************************************************************/
	function order_payment_pending($order_id, $transaction_details = false, $order_details = false, $transaction_id = false, $notes = false){

		/***************************************************************
			set update data - set to completed as anything major will stop execution
			before we get here
		****************************************************************/
		$update_db_values = array();

		/** amend order date and order update */
		$update_db_values['order_update'] 		= array('type'=> '%s', 'data' =>date('Y-m-d H:i:s', WPPIZZA_WP_TIME));

		/** transaction details */
		$update_db_values['transaction_details'] = array('type'=> '%s', 'data' => ( empty($transaction_details) ? '' : maybe_serialize($transaction_details) ) );

		/** transaction id */
		$update_db_values['transaction_id'] 	= array('type'=> '%s', 'data' => (!empty($transaction_id) ? maybe_serialize($transaction_id) : null) );


		/* set payment status, cancelled  */
		$update_db_values['payment_status'] 	= array('type'=> '%s', 'data' => 'PAYMENT_PENDING');

		/* add to notes if set */
		$update_db_values['notes'] = array('type'=> '%s', 'data' => ( empty($notes) ? '' : maybe_serialize($notes) ) );	//notes should never be an array really, but just in case, we'll serialize it if needs be

		/** update order **/
		$update_order = WPPIZZA() -> db -> update_order(false, $order_id, false, $update_db_values);

	return $update_order;
	}


	/*********************************************************************************************************
	*
	*
	*	UPDATE ORDER REJECT
	*
	*
	*********************************************************************************************************/
	function order_reject($order_id, $transaction_details = false, $order_details = false, $transaction_id = false, $notes = false){

		/***************************************************************
			set update data - set to completed as anything major will stop execution
			before we get here
		****************************************************************/
		$update_db_values = array();

		/** amend order date and order update */
		$update_db_values['order_update'] 		= array('type'=> '%s', 'data' =>date('Y-m-d H:i:s', WPPIZZA_WP_TIME));

		/** transaction details */
		$update_db_values['transaction_details'] = array('type'=> '%s', 'data' => ( empty($transaction_details) ? '' : maybe_serialize($transaction_details) ) );

		/** transaction id */
		$update_db_values['transaction_id'] 	= array('type'=> '%s', 'data' => (!empty($transaction_id) ? maybe_serialize($transaction_id) : null) );


		/* set payment status, cancelled  */
		$update_db_values['payment_status'] 	= array('type'=> '%s', 'data' => 'REJECTED');

		/* set order status, rejected  */
		$update_db_values['order_status'] 	= array('type'=> '%s', 'data' => 'REJECTED');

		/* add to notes if set */
		$update_db_values['notes'] = array('type'=> '%s', 'data' => ( empty($notes) ? '' : maybe_serialize($notes) ) );	//notes should never be an array really, but just in case, we'll serialize it if needs be

		/** update order **/
		$update_order = WPPIZZA() -> db -> update_order(false, $order_id, false, $update_db_values);


	return $update_order;
	}
	/*********************************************************************************************************
	*
	*
	*	UPDATE ORDER FAILED
	*
	*
	*********************************************************************************************************/
	function order_failed($order_id, $transaction_details = false, $order_details = false, $transaction_id = false, $transaction_errors = false, $display_errors = false){

		/***************************************************************
			set update data - set to completed as anything major will stop execution
			before we get here
		****************************************************************/
		$update_db_values = array();

		/** amend order date and order update */
		$update_db_values['order_update'] 		= array('type'=> '%s', 'data' =>date('Y-m-d H:i:s', WPPIZZA_WP_TIME));

		/** transaction details */
		$update_db_values['transaction_details']= array('type'=> '%s', 'data' => ( empty($transaction_details) ? '' : maybe_serialize($transaction_details) ) );

		/** transaction id */
		$update_db_values['transaction_id'] 	= array('type'=> '%s', 'data' => (!empty($transaction_id) ? maybe_serialize($transaction_id) : null) );

		/** transaction errors */
		$update_db_values['transaction_errors'] = array('type'=> '%s', 'data' => (!empty($transaction_errors) ? maybe_serialize($transaction_errors) : null) );

		/** transaction errors */
		$update_db_values['display_errors'] 	= array('type'=> '%s', 'data' => (!empty($display_errors) ? maybe_serialize($display_errors) : null) );

		/* set payment status, failed  */
		$update_db_values['payment_status'] 	= array('type'=> '%s', 'data' => 'FAILED');

		/** update order **/
		$update_order = WPPIZZA() -> db -> update_order(false, $order_id, false, $update_db_values);

	return $update_order;
	}

	/*********************************************************************************************************
	*
	*
	*	UPDATE ORDER EXPIRED
	*
	*
	*********************************************************************************************************/
	function order_expired($order_id, $transaction_details = false, $order_details = false, $transaction_id = false){

		/***************************************************************
			set update data - set to completed as anything major will stop execution
			before we get here
		****************************************************************/
		$update_db_values = array();

		/** amend order date and order update */
		$update_db_values['order_update'] 		= array('type'=> '%s', 'data' =>date('Y-m-d H:i:s', WPPIZZA_WP_TIME));

		/** transaction details */
		$update_db_values['transaction_details'] = array('type'=> '%s', 'data' => ( empty($transaction_details) ? '' : maybe_serialize($transaction_details) ) );

		/** transaction id */
		$update_db_values['transaction_id'] 	= array('type'=> '%s', 'data' => (!empty($transaction_id) ? maybe_serialize($transaction_id) : null) );

		/* set payment status, expired  */
		$update_db_values['payment_status'] 	= array('type'=> '%s', 'data' => 'EXPIRED');

		/** update order **/
		$update_order = WPPIZZA() -> db -> update_order(false, $order_id, false, $update_db_values);

	return $update_order;
	}


	/*********************************************************************************************************
	*
	*
	*	UPDATE ORDER TRANSACTION DETAILS
	*
	*
	*********************************************************************************************************/
	function update_transaction_details($order_id, $tx_details = false, $order_details = false, $transaction_id = false){

		$update_tx_details = array();
		/* any previously saved tx details ?*/
		if(!empty($order_details['ordervars']['tansaction_details']['value'])){
			$update_tx_details += maybe_unserialize($order_details['ordervars']['tansaction_details']['value']);
		}
		/* add and/or set additional tx details */
		if(!empty($tx_details)){
			$update_tx_details += maybe_unserialize($tx_details);/* new tx details */
		}
		/***************************************************************
			set update data - set to completed as anything major will stop execution
			before we get here
		****************************************************************/
		$update_db_values = array();

		/** amend order date and order update */
		$update_db_values['order_update'] 		= array('type'=> '%s', 'data' =>date('Y-m-d H:i:s', WPPIZZA_WP_TIME));

		/** transaction details id */
		$update_db_values['transaction_details'] = array('type'=> '%s', 'data' => ( empty($update_tx_details) ? '' : maybe_serialize($update_tx_details) ) );

		/** update transaction id too if set - since > 3.3.5*/
		if(!empty($transaction_id)){
		$update_db_values['transaction_id'] 	= array('type'=> '%s', 'data' => (!empty($transaction_id) ? maybe_serialize($transaction_id) : null) );
		}


		$refund = WPPIZZA() -> db -> update_order(false, $order_id, false, $update_db_values);

	return $refund;
	}

	/*******************************************************************************************************************
	*
	*	critical == true if dealing with ipn responses
	*	using wppizza_get_processed_order()
	*
	*******************************************************************************************************************/
	function get_prepared_order($order_id = false, $gateway_reply = false, $critical = false, $wp_user_id = null){
		/***************************************************************
			get user session and check for existence of hash
			unless set to specific order id
		***************************************************************/
		if(empty($order_id)){
			$order_hash = $this->get_orderhash();
			if(!$order_hash){
				/* error details */
				$result['error'][] = array(
					'critical'=> $critical, /* force sending of email to admin */
					'error_id'=> 30005,
					'error_message' => __('Missing hash','wppizza-admin'),
					'wp_error' =>  ''
				);
				/* logging, and sending */
				$this->gateway_logging($result['error'], $gateway_reply, false, $this->wppizza_gateway_ident, 'get-prepared-error');

				/* bail - returning error */
				return $result;
			}
		}

		/***************************************************************
			get order and details by hash or specific order id and INPROGRESS status
		****************************************************************/
		$args = array(
			'query' => array(
				'payment_status' => array('INPROGRESS','UNCONFIRMED') ,
				'wp_user_id' => $wp_user_id ,
			),
			'format' => array(
				'blog_options' => array('localization'),// add some additional - perhaps useful - info to pass on to gateways
			),
		);
		if(!empty($order_id)){
			$args['query']['order_id'] = $order_id;
		}else{
			$args['query']['hash'] = $order_hash;
		}
		//run query, and get results
		$result = WPPIZZA() -> db -> get_orders($args, 'get_prepared');
		/*************************************************
			even single order results are always arrays
			so simply use reset here
		*************************************************/
		$result = reset($result['orders']);


		if(empty($result)){

			if(empty($order_id)){
				/* error details */
				$result['error'][] = array(
					'critical'=> $critical, /* force sending of email to admin */
					'error_id'=> 30006,
					'error_message' => __('Order not found using hash','wppizza-admin'),
					'wp_error' => ''
				);
				/* logging, and sending */
				$this->gateway_logging($result['error'], $gateway_reply, $order_hash, $this->wppizza_gateway_ident, 'get-prepared-error');

				/* bail - return error array  */
				return $result;

			}else{
				/*
					if we receive ipn notifications multiple times for the same order, the status will already have changed to completed,
					so before throwing errors, check if that is the case and if so , simply ignore
				*/
				$args = array(
					'query' => array(
						'order_id' => $order_id ,
						'payment_status' => array('COMPLETED') ,
						'wp_user_id' => $wp_user_id ,
					),
					'format' => false,
				);
				//order query, and get results
				$order = WPPIZZA() -> db -> get_orders($args, 'get_prepared_completed');
				/*************************************************
					even single order results are always arrays
					so simply use reset here
				*************************************************/
				$order = reset($order['orders']);
				if(!empty($order)){return;}

				/* error details */
				$result['error'][] = array(
					'critical'=> $critical, /* force sending of email to admin */
					'error_id'=> 30007,
					'error_message' => __('Order not found using order id','wppizza-admin'),
					'wp_error' => ''
				);
				/* logging, and sending */
				$this->gateway_logging($result['error'], $gateway_reply, $order_id, $this->wppizza_gateway_ident, 'get-prepared-error');

				/*
				ipn, just return false !
				*/
				return false;

			}
		}else{
			/**
				set simplified order vars we can use in filter
				to check currency matches etc
			**/
			$this->wppizza_simplified_order_vars = $result;
		}

	return $result;
	}


	/*******************************************************************************************************************
	*
	*	get completed or refunded or unconfirmed order (depending on $payment_status) by orderid, transactionid , gateway ident returning simply true or false
	*
	*******************************************************************************************************************/
	function get_completed_order($order_id = false, $gateway_reply = false, $critical = false, $wp_user_id = null , $payment_status = array('COMPLETED')){

		/***************************************************************
			get completed order by id
		***************************************************************/
		if(empty($order_id)){
				/* error details */
				$result['error'][] = array(
					'critical'=> $critical, /* force sending of email to admin */
					'error_id'=> 30008,
					'error_message' => __('Missing order_id','wppizza-admin'),
					'wp_error' =>  ''
				);
				/* logging, and sending */
				$this->gateway_logging($result['error'], $gateway_reply, false, $this->wppizza_gateway_ident, 'get-completed-error');

			/* bail - returning error */
			return $result;
		}
		/***************************************************************
			get order by order id and COMPLETED or REFUNDED or UNCONFIRMED status
			(depending on $payment_status)
		****************************************************************/
		$args = array(
			'query' => array(
				'order_id' => $order_id ,
				'payment_status' => $payment_status ,
				'wp_user_id' => $wp_user_id ,
			),
			'format' => array(
				'blog_options' => array('localization'),// add some additional - perhaps useful - info to pass on to gateways
			),
		);
		//order query, and get results
		$order = WPPIZZA() -> db -> get_orders($args, 'get_completed');
		/*************************************************
			even single order results are always arrays
			so simply use reset here
		*************************************************/
		$order = reset($order['orders']);



		if(empty($order)){
			/* error details */
			$result['error'][] = array(
				'critical'=> $critical, /* force sending of email to admin */
				'error_id'=> 30009,
				'error_message' => __('Order not found using order id','wppizza-admin'),
				'wp_error' => ''
			);
			/* logging, and sending */
			$this->gateway_logging($result['error'], $gateway_reply, $order_id, $this->wppizza_gateway_ident, 'get-completed-error');

			/*
			ipn, or similar just return false !
			*/
			return false;
		}

	return $order;
	}

	/*******************************************************************************************************************
	*
	*	a simple versatile helper to get a single order by columns/values passed
	*	if you need to switch blogs , do this before calling this function (and reset after wards)
	*******************************************************************************************************************/
	function get_order_by_columns($columns = false, $simplify = true){
		global $wpdb, $wppizza_options, $blog_id;

		/***************************************************************
			make sure some columns are actually set , else just bail
		***************************************************************/
		if(empty($columns)){
			return;
		}

		/***************************************************************
			map columns
			key (column to query), value (value comparison operator and value to query for)
		***************************************************************/
		$query_columns = array();
		foreach($columns as $key => $value){
			if($value['operator'] != 'IN'){
				$query_columns[$key] = ''.$key.' '. esc_sql($value['operator']) .' \''. esc_sql($value['data']) .'\'';
			}else{
				/* allow for IN queries */
				$d = explode("," , esc_sql($value['data']) );
				$d = implode("','", $d);
				$query_columns[$key] = ''.$key.' IN (\''. $d .'\') ';
			}
		}

		/* set query */
		$query = "SELECT * FROM ".$wpdb->prefix . WPPIZZA_TABLE_ORDERS . " WHERE ".implode(' AND ', $query_columns)."  ";
		/* run query*/
		$order = $wpdb->get_row($query, ARRAY_A);
		if (!empty($order)) {

			/** add blogs date options/format to order */
			$order['date_format'] = array('date' => get_option('date_format'), 'time' => get_option('time_format'));
			/** blog_info (name etc) as they might be different for different blogs */
			$order['blog_info'] =  WPPIZZA() -> helpers -> wppizza_blog_details($blog_id);
			/** blog_options as they might be different for different blogs */
			$order['blog_options'] = $wppizza_options;
			/** unserialize order ini */
			$order['order_ini'] = maybe_unserialize($order['order_ini']);
			/** unserialize customer ini */
			$order['customer_ini'] = maybe_unserialize($order['customer_ini']);

			$result = WPPIZZA() -> order -> orders_formatted($order, false, 'get_order_by_columns');
			if(!empty($simplify)){
				$result = WPPIZZA()->order->simplify_order_values($result, array('localization'));
			}

		return $result;
		}
	return false;
	}
	/***********************************************************************************************************
	*
	*
	*
	*	[HELPERS]
	*
	*
	*
	************************************************************************************************************/
	/*********************************************************
	*
	*	map formfields to defined gateway formfields
	*
	*********************************************************/
	function map_gateway_formfields($order_details, $mapped_ff = false){

		if(empty($mapped_ff)){return $order_details;}

		/*********
			mapped formfields
		**********/
		if(!empty($mapped_ff)){
			foreach($mapped_ff as $ffKey => $ffMap){
				if(isset($order_details['customer'][$ffKey]['value']) && !empty($ffMap) ){
					$order_details['customer'][$ffMap] = array('label' => $order_details['customer'][$ffKey]['label'], 'value' => $order_details['customer'][$ffKey]['value'], 'type' =>$order_details['customer'][$ffKey]['type']);
				}
			}
		}

	return $order_details;
	}

	/*********************************************************
	*	add any order prepatre errors
	*********************************************************/
	function order_prepare_errors($transaction_errors, $order_details){
		if(!empty($order_details['error'])){
			foreach($order_details['error'] as $e => $err){
				$transaction_errors[]  = array(
					'critical'=> false,
					'error_id'=> $err['error_id'],
					'error_message' => $err['error_message']
				);
			}
		}

	return $transaction_errors;
	}

	/*********************************************************
	*	validate_amount_and_currency and add to errors if any
	*********************************************************/
	function verify_amount_currency_transactionid($transaction_errors, $amount = false, $currency = false, $transaction_id = false){

		/**************************************************
			Make sure the amount(s) paid match, rounding/formatting to 2 decimal to make sure
		**************************************************/
		if($amount !== false){
			$gwAmountValue=sprintf('%01.2f',$amount);
			$orderAmountValue=sprintf('%01.2f',$this->wppizza_simplified_order_vars['ordervars']['total']['value']);
			if ((string)$gwAmountValue !== (string)$orderAmountValue) {
				$transaction_errors[] = array(
					'critical'=> true,
					'error_id'=> '1001' ,
					'error_message' => __('Amounts do not match: ','wppizza-admin') . $amount.' != '.$this->wppizza_simplified_order_vars['ordervars']['total']['value']
				);
			}
		}

		/*************************************************
			Make sure the currency code matches
		**************************************************/
		if($currency !== false){
			if (strtolower($currency) !== strtolower($this->wppizza_simplified_order_vars['ordervars']['currency']['value']) ) {
				$transaction_errors[] = array(
					'critical'=> true,
					'error_id'=> '1002' ,
					'error_message' => __('Currency does not match: ','wppizza-admin') . $currency.' != '.$this->wppizza_simplified_order_vars['ordervars']['currency']['value']
				);
			}
		}

		/*************************************************
			Make sure transaction has not yet been processed
		**************************************************/
		if($transaction_id !== false && !empty($transaction_id)){
			if ( (string)$transaction_id === (string)$this->wppizza_simplified_order_vars['ordervars']['transaction_id']['value'] ) {
				$transaction_errors[] = array(
					'critical'=> true,
					'error_id'=> '1003' ,
					'error_message' => __('This transaction has already been processed: ','wppizza-admin') . $transaction_id
				);
			}
		}


	return $transaction_errors;
	}
	/************************************************************************
	*
	*	[parse wppizza orderform post data from string if not array already]
	*
	*************************************************************************/
	function parse_user_postdata($wppizza_post_data){
		$set_post_data = array();

		/* not empty and is string */
		if(!empty($wppizza_post_data) && is_string($wppizza_post_data) ){
			$set_post_data = array();
			parse_str($wppizza_post_data, $set_post_data);
		}
		/* not empty and is array already, just leave as is */
		if(!empty($wppizza_post_data) && is_array($wppizza_post_data) ){
			$set_post_data = $wppizza_post_data;
		}

	return $set_post_data;
	}

	/************************************************************************
	*
	*	get gateway ident
	*
	************************************************************************/
	function sanitize_gateway_ident($wppizza_gateway_ident){

		/* if its the full classname, just truncate it */
		//$is_classname = strtolower(substr($wppizza_gateway_ident, 0 , 16));


		//$wppizza_gateway_ident = 'wppizza_gateway_'.$wppizza_gateway_ident;
		/* if its the full classname, just truncate it */
		$is_classname = strtolower(substr($wppizza_gateway_ident, 0 , 16));
		$gateway_ident = ($is_classname == 'wppizza_gateway_') ? preg_replace('/[^a-z0-9_]/','', strtolower(substr($wppizza_gateway_ident,16))) : strtolower(preg_replace('/[^a-z0-9_]/','',$wppizza_gateway_ident)) ;
		$gateway_ident = !empty($gateway_ident) ? substr($gateway_ident, 0, 32) : 'unknown-gateway';/* max 32 chars into db */

	return 	$gateway_ident;
	}

	/******************************************************************
	*
	*	[log gateway responses to /logs/]
	*
	******************************************************************/

	function gateway_logging($tx_errors = false, $gateway_reply = false,  $order_id = false, $gateway_ident = false, $error_ident = false, $update_values = array(), $logs_path = WPPIZZA_PATH_LOGS ){
		global $wppizza_options;

		if(!$gateway_ident){
			$gateway_ident	= $this->wppizza_gateway_ident;
		}

		$logging = (!empty($wppizza_options['settings']['log_failed_orders']) || !empty($wppizza_options['settings']['log_successful_orders'])) ? true : false;


		$email_failed_to_admin = !empty($wppizza_options['settings']['send_failed_orders_to_admin']) ? true : false;

		/***
			skip write log if disabled or no errors and no gateway reply and not specifically set to log successful too
		***/
		$write_log = ( empty($logging) || ( empty($tx_errors) && empty($gateway_reply) ) ) ? false : true ;
		/***
			skip sending email if disabled or no errors (if success logging is enabled $gateway_reply will include order details !)
		***/
		//$send_email = (empty($email_failed_to_admin) || (empty($tx_errors) && empty($gateway_reply) )) ? false : true ;
		$send_email = ( empty($email_failed_to_admin) || empty($tx_errors)  ) ? false : true ;

		$is_critical = false;

		/***
			skip altogether if neither applies
		***/
		if(!$write_log && !$send_email){return;}


		/***
			log details
		***/
		$print['timestamp'] ='['.date('Y-m-d H:i:s', current_time('timestamp')).'] - '.get_bloginfo('name').' '.PHP_EOL;
		$print['url'] = 'URL: '.get_bloginfo('url').''.PHP_EOL . PHP_EOL;
		$print['ids'] = 'BLOG ID: '.get_current_blog_id().'';
		$print['ids'] .= !empty($order_id) ? ' - ORDER ID: '.$order_id.'' : '' ;
		$print['ids'] .= PHP_EOL . PHP_EOL ;
		if(!empty($tx_errors)){
			/* if array */
			if(is_array($tx_errors)){
			foreach($tx_errors as $k=>$e){
				if(is_array($e) && !empty($e['error_id']) && !empty($e['error_message'])){
					$err_str = 'ERROR';

					/* force email to admin if this error has this explicitly set */
					if(!empty($e['critical'])){
						$email_failed_to_admin = true;
						$is_critical = true;
						$err_str .= ' - CRITICAL';
					}
					$print['tx_error_'.$k.''] = ''.$err_str.': '.print_r(maybe_serialize($e['error_id']), true) . ' | '. print_r(maybe_serialize($e['error_message']), true) . PHP_EOL;

					/* add wp error if any , at the moment this is always empty, but leave it for now, maybe we can do something with it one day*/
					if(!empty($e['wp_error'])){
						$print['wp_error_'.$k.''] = 'WP_ERROR: '.print_r(maybe_serialize($e['wp_error']), true) . PHP_EOL;
					}
				}else{
					/* for malformed errors arrays */
					$print['tx_error'] = '[ERRORS]: '.print_r(maybe_serialize($e), true)  . PHP_EOL;
				}
			}}else{
				/* just in case its malformed up so at least we have something*/
				$print['tx_error'] = '[ERROR]: '.print_r(maybe_serialize($tx_errors), true) . PHP_EOL;
			}
		}

		/****************************************************************
			add any wpdb errors
		*****************************************************************/
		global $wpdb;
		$wpdb->show_errors     = true;
        $wpdb->suppress_errors = false;
		if($wpdb->last_error!==''){
			$print['wpdb_last_error'] = 'WPDB LAST ERROR (*might* be related): '.print_r($wpdb->last_error, true) .'' . PHP_EOL;
		}
		if($wpdb->last_query!==''){
			$print['last_query'] = 'WPDB LAST QUERY (*might* be related): '.print_r($wpdb->last_query, true) .'' . PHP_EOL;
		}

		/****************************************************************
			add any php errors
		*****************************************************************/
		$error_get_last = error_get_last();
		if(!empty($error_get_last)){
			$print['php_last_error'] = 'LAST PHP ERROR (*might* be related): '.print_r($error_get_last['message'], true) .' IN FILE: "'.print_r($error_get_last['file'], true).'" ON LINE: "'.print_r($error_get_last['line'], true).'"' . PHP_EOL;
		}

		/****************************************************************
			add any replies received from gateway
		*****************************************************************/
		if(!empty($gateway_reply)){
			$print['tx_vars'] = PHP_EOL . 'PARAMETERS RECEIVED' . PHP_EOL ;
			$print['tx_parameters'] = print_r($gateway_reply, true).PHP_EOL;
		}

		/****************************************************************
			add any update values that were supposed to be updated in the db
		*****************************************************************/
		if(!empty($update_values)){
			$print['update_vars'] = PHP_EOL . 'UPDATE VALUES' . PHP_EOL ;
			$print['updateparameters'] = print_r($update_values, true).PHP_EOL;
		}



		$print['divider'] = '--------------------------------------------------------------------------'.PHP_EOL;


		/****************************************************************
			write log
		*****************************************************************/
		if($write_log || $is_critical){

			/*
				although the log file by default goes into a htaccess protected directory anyway (ie. wppizza/logs), lets add some hash to make it harder to guess in case the directory only has an index.html
				of course, if that dir is world readable and has no protection at all, this won't make any difference
			*/
			$hash = substr(wp_hash($gateway_ident),0,12);

			/*
				add what kind of error to logfile name if necessary
			*/
			$error_ident = preg_replace('/[^a-z0-9-_]/','', strtolower($error_ident));
			$error_ident = !empty($error_ident) ? $error_ident : 'error';

			/* add critical */
			$critical = ($is_critical) ? 'CRITICAL-' : '';


			/*
				log
			*/

			/* filter/set path if you must */
			$logs_path = apply_filters('wppizza_filter_logs_path', $logs_path, $gateway_ident, $error_ident);

			/* write log */
			file_put_contents($logs_path . strtolower($gateway_ident).'-'.$critical .''. $error_ident.'-'.$hash.'.log', implode('', $print), FILE_APPEND);
		}



		/**************************************************************
			update db if critical failed with errors and we have an order id
		**************************************************************/
		if($is_critical && !empty($order_id) && !empty($tx_errors)){

			$update_db_values = array();

			/** amend order date / order update */
			$update_db_values['order_date'] 		= array('type'=> '%s', 'data' =>date('Y-m-d H:i:s', WPPIZZA_WP_TIME));
			$update_db_values['order_date_utc'] 		= array('type'=> '%s', 'data' =>date('Y-m-d H:i:s', WPPIZZA_UTC_TIME));
			/* set status, FAILED */
			$update_db_values['payment_status'] 	= array('type'=> '%s', 'data' => 'FAILED');
			/* set status, INPROGRESS */
			$update_db_values['transaction_errors'] = array('type'=> '%s', 'data' => maybe_serialize($tx_errors) );
			/* update db */
			$order_failed = WPPIZZA() -> db -> update_order(false, $order_id, false, $update_db_values);
		}



		/****************************************************************
			send error to admin
		*****************************************************************/
		if($send_email){
			/*
				email, subject, message, headers
				prefix with @ to suppress - inconsequential - AltBody debug notices (never mind if admin email is not defined or something, we cant send anything anywhere anyway)
				we could also do
				add_filter( 'wp_mail_content_type', 'set_html_content_type' );
				and
				remove_filter( 'wp_mail_content_type', 'set_html_content_type' );
				directly after
				but this seems complete overkill to me here
			*/
			@wp_mail(''.get_option('admin_email').'', 'Warning: ['.$gateway_ident.'] - Payment Error', print_r(implode('',$print), true), array(WPPIZZA_CUSTOM_HEADER_EMAIL) );
		}
	return;
	}


	/***************************************************************
		[verify nonce]
	***************************************************************/
	function verify_nonce($posted_data){
		$valid_nonce = true;
		$nonce = sanitize_text_field($posted_data['' . WPPIZZA_SLUG . '_nonce_checkout']);
		if (!wp_verify_nonce(  $nonce , '' . WPPIZZA_SLUG . '_nonce_checkout' ) ) {
			$valid_nonce = false;
		}
	return $valid_nonce;
	}

	/***************************************************************
		get user data session making sure hash is set
	***************************************************************/
	function get_orderhash(){
		/* ini as false*/
		$order_hash = false;

		$user_session = WPPIZZA()->session->get_userdata();
		if(isset($user_session[''.WPPIZZA_SLUG.'_hash'])){
			/* set hash */
			$order_hash = $user_session[''.WPPIZZA_SLUG.'_hash'];
		}
	return $order_hash;
	}

	/***************************************************************
		[get and parse all user post variables and save in session
		(including gateway selected)]
	***************************************************************/
	function set_userdata($wppizza_post_data){
		/* ini as false*/
		$data_posted = false;
		/* not empty and is string */
		if(!empty($wppizza_post_data) && is_string($wppizza_post_data) ){
			$parsed_postdata = array();
			parse_str($wppizza_post_data, $parsed_postdata);
			/* set user session */
			WPPIZZA()->session->set_userdata($parsed_postdata);

			/* set valid */
			$data_posted = $parsed_postdata;
		}

		/* not empty and is already an array */
		if(!empty($wppizza_post_data) && is_array($wppizza_post_data) ){
			/* set user session */
			WPPIZZA()->session->set_userdata($wppizza_post_data);

			$data_posted = $wppizza_post_data;
		}

	return $data_posted;
	}

	/***************************************************************
		[set redirect url]
	***************************************************************/
	function set_redirect_url($order_hash, $error = false){
		/**get links to order/amend pages*/
		$order_page_links = wppizza_page_links();
		/** add hash as get variable , dont add initiator to wpptx for internal/ajax*/
		$args[ WPPIZZA_TRANSACTION_GET_PREFIX ] = $order_hash;
		if($error){
		$args['e'] = true;
		}
		/** set redirect url **/
		$redirect_url = esc_url_raw(add_query_arg($args, $order_page_links['orderpage']));

	return $redirect_url;
	}

	/***************************************************************


		[get email templates for order]


	***************************************************************/
	//function get_email_templates($order, $order_parameters_formatted)
	function get_email_templates($order_formatted){
		global $wppizza_options;


		/*
			fix template type to 'emails' here
		*/
		$template_type = 'emails';


		/*
			get all id's of email templates to be applied/send
		*/
		$template_ids = $wppizza_options['templates_apply']['emails'];
		/*
			get email templates settings
		*/
		$templates = get_option(WPPIZZA_SLUG.'_templates_'.$template_type.'');
		$templates = apply_filters('wppizza_filter_template_options', $templates, $template_type);

		/**************************************************************************
		*
		*	global email parameters
		*
		**************************************************************************/
		/***************
			subject
		***************/
		/*
			formfields where add to subject was enabled and value !=''
		*/
		$customer_data_add_to_subject = array();
		$enabled_formfields = WPPIZZA()->helpers->enabled_formfields();
		foreach($enabled_formfields as $cdataKey=>$cDataVal){
			if(!empty($cDataVal['add_to_subject_line'])){
				/* value as saved in customer_ini */
				$set_user_value = trim($order_formatted['sections']['customer'][$cdataKey]['value']);
				if(!empty($set_user_value)){
				/*truncate textareas to single line - silly to use anyway*/
					if($cDataVal['type']=='textarea'){
						$set_user_value=explode(PHP_EOL,$set_user_value);
						$customer_data_add_to_subject[]=trim($set_user_value[0]);
					}else{
						$customer_data_add_to_subject[] = $set_user_value;
					}
				}
			}
		}
		/* implode added ssubject line fields and trim */
		$customer_data_add_to_subject=trim(implode(' ',$customer_data_add_to_subject));


		/*clear bloginfo from subject line if you want to replace it with something else*/
		$order_formatted['blog_info'] = apply_filters('wppizza_email_subject_bloginfo', $order_formatted['blog_info']);

		/** create 2 versions of bloginfo for standard output or output with customer details in subject */
		$blog_info = array();
		$blog_info['standard'] 	= $order_formatted['blog_info']['blogname'];
		$blog_info['custom'] 	= empty($order_formatted['blog_info']['blogname']) ?  '' : '['.$order_formatted['blog_info']['blogname'].'] - ';

		/** construct subject line */
		$subject = array();
		$subject['prefix'] 	= empty($customer_data_add_to_subject) ? $blog_info['standard'] : $blog_info['custom'] . $customer_data_add_to_subject ;
		$subject['main'] 	= $order_formatted['localization']['your_order'];
		$subject['suffix'] 	= date_i18n($order_formatted['date_format']['date'], WPPIZZA_WP_TIME)." ".date_i18n($order_formatted['date_format']['time'], WPPIZZA_WP_TIME)."";


		/** filter, implode, decode */
		$subject = apply_filters('wppizza_filter_email_subject', $subject, $order_formatted);
		$subject = trim(implode(' ',$subject));
		$subject = wppizza_email_decode_entities($subject);/* make sure it's decoded */


		/***************
			from: name|email
		***************/
		$from = false;
		if(!empty($wppizza_options['order_settings']['order_email_from'])){
			$from = array();
			/* set from name set in order settings, if empty, just use from email here too */
			$from['name'] = !empty($wppizza_options['order_settings']['order_email_from_name']) ? wppizza_email_decode_entities($wppizza_options['order_settings']['order_email_from_name']) : $wppizza_options['order_settings']['order_email_from'] ;
			/* set from email address set in order settings */
			$from['email'] = $wppizza_options['order_settings']['order_email_from'];
		}
		/* at least from email must be set */
		if($from === false){
			$errors['error']= array('error_id' => 20001, 'error' => 'missing "From" address', 'details' => 'missing "From" address in '.WPPIZZA_NAME.' -> Order Settings -> Emails ') ;
			return $errors;
		}

		/***************
			shop to: name|email
		***************/
		$shop_email = false;
		if(!empty($wppizza_options['order_settings']['order_email_to']) && is_array($wppizza_options['order_settings']['order_email_to'])){
			foreach($wppizza_options['order_settings']['order_email_to'] as $to){
				$shop_email[] = array('name' => $to, 'email' => $to);
			}
		}
		/* shop email must be set  */
		if($shop_email === false){
			$errors['error']= array('error_id' => 20002, 'error' => 'missing shop "To"  address', 'details' => 'missing shop "To"  address in '.WPPIZZA_NAME.' -> Order Settings -> Emails') ;
			return $errors;
		}

		/***************
			customer: name|email
		***************/
		$customer = false;
		$reply_to = false;
		if(!empty($order_formatted['sections']['customer']['cemail']['value'])){
			$cemail = $order_formatted['sections']['customer']['cemail']['value'];
			/* let's try and make a name from customer email */
			$cname = explode('@',$cemail);
			$cname=str_replace(array('.'),' ',$cname[0]);/*worth a go*/
			/* add as zero index for consistency */
			$customer[0] = array('name' => $cname, 'email' => $cemail);
			/* set reply to as weel to be customer */
			$reply_to = array('name' => $cname, 'email' => $cemail);
		}

		/***************
			bcc: name(s) == email(s)
		***************/
		$bccs = false;
		if(!empty($wppizza_options['order_settings']['order_email_bcc']) && is_array($wppizza_options['order_settings']['order_email_bcc'])){
			foreach($wppizza_options['order_settings']['order_email_bcc'] as $bcc){
				$bccs[] = array('name' => $bcc, 'email' => $bcc);
			}
		}

		/***************
			attachments
		***************/
		if(count($wppizza_options['order_settings']['order_email_attachments'])>0){
			foreach($wppizza_options['order_settings']['order_email_attachments'] as $attachment){
				if(is_file($attachment)){
					$set_attachments[]=$attachment;
				}
			}
		}
		$attachments = !isset($set_attachments) ? false : $set_attachments;

		/***************
		 shop template id
		***************/
		$shop_tpl_id		= $template_ids['recipients_default']['email_shop'];
		/***************
		 customer template id
		***************/
		$customer_tpl_id	= $template_ids['recipients_default']['email_customer'];


		/************************************************************************************************
		*
		*	get all template ids that have shop,
		*	customer or additional recipients set
		*
		************************************************************************************************/
		$used_template_ids = array();
		$additional_recipients_ids = array();
		/* keys of templates used for shop and customer */
		foreach($template_ids['recipients_default'] as $tpl_id){
			$used_template_ids[$tpl_id] = $tpl_id;
		}

		/*
			array of individual emails of templates that are to be used
			for additional recipienst that are not also shop/bcc , or customer templates
		*/
		if(isset($template_ids['recipients_additional']) && is_array($template_ids['recipients_additional'])){
			foreach($template_ids['recipients_additional'] as $tplAddRecipientsKey => $tplAddRecipients){
				/* exclude additional recipients if the are part of customer or shop email template (they will be cc'd there instead)*/
				if(!isset($used_template_ids[$tplAddRecipientsKey])){
					/* add to array of keys we need to get template output for too */
					$used_template_ids[$tplAddRecipientsKey] = $tplAddRecipientsKey;
					/* templates/recipients we send individual emails to */
					$additional_recipients_ids[$tplAddRecipientsKey] = $tplAddRecipients;/* add to array of emails we need to send separately */
				}
			}
		}


		/************************************************************************************************
		*
		*
		*	get/set settings and plaintext/html for each template to use/send
		*
		*
		************************************************************************************************/
		$template_settings = array();
		foreach($used_template_ids as $template_id){
			/*
				do we need to get html markup too for this template ?
			*/
			$is_html = ($templates[$template_id]['mail_type'] == 'wp_mail') ? false : true;

			$template_settings[$template_id] = array();

			/** a flag that tells us if we are using/outputting html */
			$template_settings[$template_id]['is_html'] = $is_html;

			$template_settings[$template_id]['attachments'] = empty($templates[$template_id]['omit_attachments']) ? $attachments : false;

			$template_settings[$template_id]['additional_recipients'] = (!empty($template_ids['recipients_additional'][$template_id]) && is_array($template_ids['recipients_additional'][$template_id])) ? $template_ids['recipients_additional'][$template_id] : false;;

			/* template values for template id */
			$template_values = $templates[$template_id] ;


			/**
				get array of plaintext sections markup
			**/
			$template_settings[$template_id]['plaintext'] =  WPPIZZA()->templates_email_print->get_template_email_plaintext_sections_markup($order_formatted, $template_values, 'emails');

			/*
				get html markup (if required)
			*/
			$template_settings[$template_id]['html'] = ($is_html) ?  WPPIZZA()->templates_email_print->get_template_email_html_sections_markup($order_formatted, $template_values, 'emails', $template_id) : false;
		}


		/************************************************************************************************
		*
		*	get all the emails and parameters thereof we have to send
		*	perhaps having to use the same template multiple times
		*
		************************************************************************************************/

		/***************
		 array or emails and param
		***************/
		$emails = array();
		/*
			if shop and customer are the same , put customer plus any additionaly recipients in cc
			replyto == customer
		*/
		if($shop_tpl_id == $customer_tpl_id){
			$tid = $shop_tpl_id;/**same for both */
			/* merge any customer and additional recipients  for cc's */
			$add_cc_merge_customer = ($customer) ? $customer : array() ;
			$add_cc_merge_additional = ($template_settings[$tid]['additional_recipients']) ? $template_settings[$tid]['additional_recipients'] : array() ;
			$add_cc_merge = array_merge($add_cc_merge_customer, $add_cc_merge_additional);
			$add_cc = (count($add_cc_merge) > 0 ) ? $add_cc_merge : false ;

			/* check if we require a plaintext pre element */
			$pre_tag = $this->set_pre_element($shop_email, $add_cc, $bccs, $template_settings[$tid]['is_html']);
			/*
				email to shop , customer in cc, reply_to == customer
			*/
			$emails['shop'] = array();
			$emails['shop']['tpl_id'] 		= $shop_tpl_id;
			$emails['shop']['db_plaintext'] = $template_settings[$tid]['plaintext'];/* separate plaintext order and customer details to store in db*/
			$emails['shop']['Subject'] 		= $subject;
			$emails['shop']['SetFrom'] 		= $from;
			$emails['shop']['AddAddress'] 	= $shop_email;
			$emails['shop']['AddCC'] 		= $add_cc;/* merge customer and additional recipients */
			$emails['shop']['AddReplyTo']	= $reply_to;/* to customer */
			$emails['shop']['AddBCC'] 		= $bccs;
			$emails['shop']['AddAttachment']= $template_settings[$tid]['attachments'];
			$emails['shop']['AltBody'] 		= $template_settings[$tid]['plaintext']['markup'];/* combined  plaintext order, customer,  etc details*/
			$emails['shop']['MsgHTML'] 		= (!empty($pre_tag['force_html']) && empty($template_settings[$tid]['html'])) ? $pre_tag['pre_'].$template_settings[$tid]['plaintext']['markup'].$pre_tag['_pre'] : $template_settings[$tid]['html'] ;

		}
		/*
			if shop email template and customer email template are not the same,
			send separate emails
			provided a customer email actually exists (else only send to shop)
		*/
		if($shop_tpl_id != $customer_tpl_id){
			/*
				shop only (including additional recipients)
			*/
			//$all_recipients = array_merge($shop_email, $template_settings[$shop_tpl_id]['additional_recipients'], $bccs);


			/* check if we require a plaintext pre element */
			$pre_tag = $this->set_pre_element($shop_email, $template_settings[$shop_tpl_id]['additional_recipients'], $bccs, $template_settings[$shop_tpl_id]['is_html']);

			$emails['shop'] = array();
			$emails['shop']['tpl_id'] 		= $shop_tpl_id;
			$emails['shop']['db_plaintext'] = $template_settings[$shop_tpl_id]['plaintext'];/* separate plaintext order and customer details*/
			$emails['shop']['Subject'] 		= $subject;
			$emails['shop']['SetFrom'] 		= $from;
			$emails['shop']['AddAddress'] 	= $shop_email;
			$emails['shop']['AddCC'] 		= $template_settings[$shop_tpl_id]['additional_recipients'];
			$emails['shop']['AddReplyTo']	= false; /*automatic to shop */
			$emails['shop']['AddBCC'] 		= $bccs;
			$emails['shop']['AddAttachment'] = $template_settings[$shop_tpl_id]['attachments'];
			$emails['shop']['AltBody'] 		= $template_settings[$shop_tpl_id]['plaintext']['markup'];/* combined  plaintext order, customer,  etc details*/
			$emails['shop']['MsgHTML'] 		= (!empty($pre_tag['force_html']) && empty($template_settings[$shop_tpl_id]['html'])) ? $pre_tag['pre_'].$template_settings[$shop_tpl_id]['plaintext']['markup'].$pre_tag['_pre'] : $template_settings[$shop_tpl_id]['html'] ;

			/*
				customer - only if cemail exists and not empty
				customer main recipient, additional in cc , replyto == ?
			*/
			if($customer){

			//$all_recipients = array_merge($customer, $template_settings[$customer_tpl_id]['additional_recipients']);


				/* check if we require a plaintext pre element */
				$pre_tag = $this->set_pre_element($customer, $template_settings[$customer_tpl_id]['additional_recipients'], false, $template_settings[$customer_tpl_id]['is_html']);

				$emails['customer'] = array();
				$emails['customer']['tpl_id'] 		= $customer_tpl_id;
				$emails['customer']['db_plaintext']	= $template_settings[$customer_tpl_id]['plaintext'];/* separate plaintext order and customer details*/
				$emails['customer']['Subject'] 		= $subject;
				$emails['customer']['SetFrom'] 		= $from;
				$emails['customer']['AddAddress'] 	= $customer;
				$emails['customer']['AddCC'] 		= $template_settings[$customer_tpl_id]['additional_recipients'];/* additional recipients */
				$emails['customer']['AddReplyTo']	= false; /* automatic to shop */
				$emails['customer']['AddBCC'] 		= false; /* no bcc's here */
				$emails['customer']['AddAttachment'] = $template_settings[$customer_tpl_id]['attachments'];
				$emails['customer']['AltBody'] 		= $template_settings[$customer_tpl_id]['plaintext']['markup'];/* combined  plaintext order, customer,  etc details*/
				$emails['customer']['MsgHTML'] 		= (!empty($pre_tag['force_html']) && empty($template_settings[$customer_tpl_id]['html'])) ? $pre_tag['pre_'].$template_settings[$customer_tpl_id]['plaintext']['markup'].$pre_tag['_pre'] : $template_settings[$customer_tpl_id]['html'] ;



			}
		}
		/*
			additional recipients only,
			all templates set to additional recipients without being sent to customer
			and/or shop. /////could probably be determined further up, but it gets messy
			separate emails, no cc, replyto == ?
		*/
		$i=0;
		foreach($additional_recipients_ids as $arTplid => $additional_recipients){
			foreach($additional_recipients as $additional_recipient_email){
				if(!empty($additional_recipient_email)){
					$add_address = array();
					$add_address[0] = array('name' => $additional_recipient_email, 'email' => $additional_recipient_email);

					/* check if we require a plaintext pre element */
					$pre_tag = $this->set_pre_element($add_address, false, false, $template_settings[$arTplid]['is_html']);

					//$all_recipients = array_merge($customer, $template_settings[$customer_tpl_id]['additional_recipients']);

					$emails[$i] = array();
					$emails[$i]['tpl_id'] 		= $arTplid;
					$emails[$i]['db_plaintext'] = $template_settings[$arTplid]['plaintext'];/* separate plaintext order and customer details*/
					$emails[$i]['Subject'] 		= $subject;
					$emails[$i]['SetFrom'] 		= $from;
					$emails[$i]['AddAddress'] 	= $add_address;
					$emails[$i]['AddCC'] 		= false; /* additional recipients */
					$emails[$i]['AddReplyTo']	= false; /* automatic to shop */
					$emails[$i]['AddBCC'] 		= false; /* no bcc's here */
					$emails[$i]['AddAttachment'] = $template_settings[$arTplid]['attachments'];
					$emails[$i]['AltBody'] 		= $template_settings[$arTplid]['plaintext']['markup'];/* combined  plaintext order, customer,  etc details*/
					$emails[$i]['MsgHTML'] 		= (!empty($pre_tag['force_html']) && empty($template_settings[$arTplid]['html'])) ? $pre_tag['pre_'].$template_settings[$arTplid]['plaintext']['markup'].$pre_tag['_pre'] : $template_settings[$arTplid]['html'] ;


				$i++;
				}
			}
		}

		/*
			should not really ever happen to be false, but check anyway
		*/
		if(count($emails)<=0){
			$errors['error']= array('error_id' => 20003, 'error' => 'no emails set to be send') ;
			return $errors;
		}

	return $emails;
	}



	/***************************************************************
		<del>google etc format/wrap plaintext as/in html</del>

		google now DOES recognise plaintext but does NOT use monospace fonts...
		so we now force html and set monospaced font family and <pre>
		(previously google forced html to start off with so a simple <pre>
		sufficed....)

		if any of the recipients emails is in the check_for array
	***************************************************************/
	function set_pre_element($AddAddress, $AddCC, $AddBCC, $is_html){

		$pretag['force_html'] = false;// flag to forcefully convert plaintext to html - using pre elements - to force monospaced fonts for stupid webmail clients
		$pretag['pre_'] = '';
		$pretag['_pre'] = '';
		/**
			if we are sending html anyway, we do not need the overhead
			to check if we need the pre tag
			as the webmail will show the html email anyway
		**/
		if($is_html){return $pretag;}


		$recipients = array();
		/* get all recipients email addresses */
		if(!empty($AddAddress) && is_array($AddAddress)){
			foreach($AddAddress as $rec){
				/* might not be an array , but the email only */
				$recipients[] = !empty($rec['email']) ? $rec['email'] : $rec;
			}
		}
		if(!empty($AddCC) && is_array($AddCC)){
			foreach($AddCC as $rec){
				/* might not be an array , but the email only */
				$recipients[] = !empty($rec['email']) ? $rec['email'] : $rec;
			}
		}
		if(!empty($AddBCC) && is_array($AddBCC)){
			foreach($AddBCC as $rec){
				/* might not be an array , but the email only */
				$recipients[] = !empty($rec['email']) ? $rec['email'] : $rec;
			}
		}
		$recipients = array_unique($recipients);/* just in case */

		/**
			which webmails are we checking, currenlty google hotmail and yahoo only
		**/
		$check_for=array('@gmail.','@googlemail.','@outlook.','@yahoo.','@hotmail.');
		$check_for=apply_filters('wppizza_email_plaintext_to_webmail_domains', $check_for);
		foreach($recipients as $recipient){
			foreach($check_for as $webmail_str){
				$pos = stripos($recipient, $webmail_str);
				if ($pos !== false) {

					$pretag['force_html'] = true;


					$pretag['pre_'] = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">'.PHP_EOL;
					$pretag['pre_'] .= '<html xmlns="http://www.w3.org/1999/xhtml">'.PHP_EOL;
					$pretag['pre_'] .= '<head>'.PHP_EOL;
					$pretag['pre_'] .= '<title></title>'.PHP_EOL;
					$pretag['pre_'] .= '<meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />'.PHP_EOL;
					$pretag['pre_'] .= '</head>'.PHP_EOL;
					$pretag['pre_'] .= '<body style="font-family: monospace;">'.PHP_EOL;
					$pretag['pre_'] .= '<pre>'.PHP_EOL;

					$pretag['_pre'] = '</pre>'.PHP_EOL;
					$pretag['_pre'] .= '</body>'.PHP_EOL;
					$pretag['_pre'] .= '</html>'.PHP_EOL;
					break;
				}
			}
		}
	return $pretag;
	}

	/***************************************************************
		filter on order execute action hook to return
		array of orderid, orderdetails, order formatted , selected print template output
		if any of the recipients is one of those wrap plaintext
		emails in <pre></pre> elements
	***************************************************************/
	function filter_on_order_execute($order_id, $order_details, $order_formatted, $print_template_id){
		$parameters = array();
		/* id */
		$parameters['order_id'] = $order_id;

		/* details */
		$parameters['order_details'] = false;
		if(!empty($order_details)){
			$parameters['order_details'] = $order_details;
		}
		/* formatted */
		$parameters['order_formatted'] = false;
		if(!empty($order_formatted)){

		}
		/* print template */
		$parameters['template_markup'] = false;
		if($print_template_id >= 0){

		}

		return $parameters;
	}

	/***************************************************************
		if array of id's is defined, return print templates for those arrays
	***************************************************************/
	function get_print_templates($tpl_ids, $order_id, $order_formatted){

		$template_data = false;

		/* fixed to print */
		$template_type = 'print';


		$tpl_ids = array_unique($tpl_ids);
		if(!empty($tpl_ids)){
		$get_templates = get_option(WPPIZZA_SLUG.'_templates_'.$template_type.'');
		$get_templates = apply_filters('wppizza_filter_template_options', $get_templates, $template_type);
			foreach($tpl_ids as $tpl_id){
				if(!empty($get_templates[$tpl_id])){
					$template_data[$tpl_id] = array();
					$as_html = ($get_templates[$tpl_id]['mail_type'] == 'wp_mail') ? false : true;
					$template_values = $get_templates[$tpl_id];
					/****************************************
						get html or plaintext output
					****************************************/
					if($as_html){
						$markup = WPPIZZA()->templates_email_print->get_template_email_html_sections_markup($order_formatted, $template_values, $template_type, $tpl_id);
						$template_data[$tpl_id]['content-type'] = 'text/html';
						$template_data[$tpl_id]['markup'] = $markup;
					}else{
						/* plaintext returns sections too, so get the array first */
						$parameters = WPPIZZA()->templates_email_print->get_template_email_plaintext_sections_markup($order_formatted, $template_values, $template_type);
						$template_data[$tpl_id]['content-type'] = 'text/plain';
						$template_data[$tpl_id]['markup'] = $parameters['markup'];
					}
				}
			}
		}
		return $template_data;
	}

}
?>