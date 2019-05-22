<?php
/**
* WPPIZZA_MODULE_ORDERHISTORY_ORDERHISTORY Class
*
* @package     WPPIZZA
* @subpackage  WPPIZZA_MODULE_ORDERHISTORY_ORDERHISTORY
* @copyright   Copyright (c) 2015, Oliver Bach
* @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
* @since       3.0
*
*/
if ( ! defined( 'ABSPATH' ) ) exit;/*Exit if accessed directly*/


/************************************************************************************************************************
*
*
*
*
*
*
************************************************************************************************************************/
class WPPIZZA_MODULE_ORDERHISTORY_ORDERHISTORY{

	private $settings_page = 'orderhistory';/* which admin subpage (identified there by this->class_key) are we adding this to */
	private $section_key = 'orderhistory';/* must be unique */
	private $module_priority = 10;/* display order (priority) of settings in subpage */


	function __construct() {
		/**********************************************************
			[add settings to admin]
		***********************************************************/
		if(is_admin()){

			/** orders on load **/
			add_action('wppizza_admin_orderhistory_results', array( $this, 'order_history_results') );

			/** admin ajax **/
			add_action('wppizza_ajax_admin_'.$this->settings_page.'', array( $this, 'admin_ajax'));

		}
	}
	/*******************************************************************************************************************************************************
	*
	* 	[admin ajax]
	*
	********************************************************************************************************************************************************/
	function admin_ajax($wppizza_options){

		/*************************************************************************************
		*
		*
		*
		*	[show/get orders wppizza->order history]
		*
		*
		*
		***********************************************************************************/
		if($_POST['vars']['field']=='get_orders'){
			/**********set header********************/
			header('Content-type: application/json');
			$order_history = json_decode($this->order_history_markup());

			$obj=array();
			$obj['orders']=$order_history -> markup;
		 	$obj['post']=$_POST;
		 	$obj['values']=$order_history -> values;

			print"".json_encode($obj)."";
			exit();
		}
		/*************************************************************************************
		*
		*
		*
		*	[get new orders (when enabled in Wppizza->Settings->New Order Notifications)]
		*
		*
		*
		***********************************************************************************/
		if($_POST['vars']['field']=='new_orders'){

			/*
				get number of
				any new orders
			*/
			$args = array(
				'query'=>array(
					'payment_status' => 'COMPLETED',
					'order_status' => 'NEW',
					'summary' => true,// only return count/totals etc before pagination/limits
				),
			);
			$new_orders = WPPIZZA() -> db -> get_orders($args, 'new_orders');

			$results = array();
			$results['new_orders'] = $new_orders['total_number_of_orders'];
			print"".json_encode($results)."";
		exit();
		}


		/********************************************
		*
		*
		*	[order history -> refund at gateway]
		*
		*
		********************************************/
		if($_POST['vars']['field']=='refund_at_gateway'){

			/*
				check for delete capabilities
			*/
			if(!current_user_can('wppizza_cap_delete_order') || WPPIZZA_DEV_ADMIN_NO_SAVE ){
				/*
					saving disabled
				*/
				if(WPPIZZA_DEV_ADMIN_NO_SAVE){
					$obj['update_prohibited'] = __('Update Prohibited', 'wppizza-admin');
					print"".json_encode($obj)."";
				exit();
				}
				return;
			}


			$blog_id = (int)$_POST['vars']['blogid'];
			$order_id = (int)$_POST['vars']['id'];
			$class = $_POST['vars']['class'];

			/*
				get refunded or completed order
			*/
			$args = array(
				'query' => array(
					'order_id' => $order_id ,
					'payment_status' => array('COMPLETED', 'REFUNDED') ,
					'blogs' => array($blog_id) ,
				),
				/* add in class idents here as we'll need them for email templates */
				'format' => array(
					'sections' => true,//leave order sections in its distinct [section] array
				),
			);
			//run query, and get results
			$order_results = WPPIZZA() -> db -> get_orders($args, 'refund_at_gateway');
			$order_results = reset($order_results['orders']);//only get this single order

			if(empty($order_results)){
				$results['error'] = __('Order not found', 'wppizza-admin');
				$results['error_message'] = 'Blog ID: '.$blog_id.' Order ID: '.$order_id;
				print"".json_encode($results)."";
				exit();
			}

			/*
				order already refunded
			*/
			if($order_results['sections']['ordervars']['payment_status']['value'] == 'REFUNDED'){
				$results['error'] = __('This order has already been refunded', 'wppizza-admin');
				$results['error_message'] = 'Blog ID: '.$blog_id.' Order ID: '.$order_id;
				print"".json_encode($results)."";
				exit();
			}

			$transaction_id = $order_results['sections']['ordervars']['transaction_id']['value'];
			$total = $order_results['sections']['ordervars']['total']['value'];
			$order_details = $order_results['sections'];
			$current_notes = $order_details['ordervars']['notes']['value_formatted'];


			/**
				ini response
			**/
			$results = array();

			/**
				load class and refund
			**/
			$gateway = new $class;
			$method = $gateway->gatewayRefunds;
			$refund = $gateway->$method($order_id, $transaction_id, $total, $order_details);
			if($refund === true || is_string($refund)){
				$results['success'] = true;
				$results['success_message'] = __('Payment Refunded', 'wppizza-admin');


				/***************************************************************
					update order status with update timestamp
				****************************************************************/
				$update_db_values = array();

				/**
					amend order update
				**/
				$update_db_values['order_update'] 	= array('type'=> '%s', 'data' =>date('Y-m-d H:i:s', WPPIZZA_WP_TIME));

				/**
					set status, captured
				**/
				$update_db_values['order_status'] 	= array('type'=> '%s', 'data' => 'REFUNDED' );

				/**
					update payment status to refunded
				**/
				$update_db_values['payment_status'] 	= array('type'=> '%s', 'data' => 'REFUNDED' );

				/**
					set refunded amount to total
				**/
				$update_db_values['order_refund'] 	= array('type'=> '%f', 'data' => $total );

				/**
					update / add to  notes if set as string
				**/
				if(!empty($refund) && is_string($refund)){
					/* add line break */
					$refund_notes = !empty($current_notes) ? $current_notes . PHP_EOL : '' ;
					/* prefix date */
					$refund_notes .= '['.wppizza_wpdate_formatted().'] ' . $refund ;
					/* update notes section */
					$update_db_values['notes'] 	= array('type'=> '%s', 'data' => $refund_notes );
				}

				/*
					run query
				*/
				$order_update = WPPIZZA()->db->update_order($blog_id, $order_id, false , $update_db_values );


				/**
					return new timestamp formatted to js ajax result
				**/
				$results['update_timestamp']= date("d-M-Y H:i:s",WPPIZZA_WP_TIME);
				$results['update_timestamp']= apply_filters('wppizza_filter_order_history_update_timestamp', $results['update_timestamp'], WPPIZZA_WP_TIME);
				$results['notes']= (!empty($refund_notes)) ? $refund_notes : $current_notes ;

			}else{
				$results['error'] = __('Refund Failed', 'wppizza-admin');
				if(!empty($refund['error'])){
					$results['error_message'] = '['.wppizza_wpdate_formatted().'] ';
					if(is_array($refund['error'])){
						foreach($refund['error'] as $k=>$e){
							$results['error_message'] .= ''.$k.': '.print_r($e, true);
						}
					}else{
						$results['error_message'] .= ''.print_r($refund['error'], true);
					}
				}
			}

			print"".json_encode($results)."";
		exit();
		}



		/********************************************
		*
		*
		*	[order history -> update order status]
		*
		*
		********************************************/
		if($_POST['vars']['field']=='orderstatuschange' && isset($_POST['vars']['id']) && $_POST['vars']['id']>=0){
			/**********set header********************/
			header('Content-type: application/json');


			/*
				saving disabled
			*/
			if(WPPIZZA_DEV_ADMIN_NO_SAVE){
				$obj['update_prohibited'] = __('Update Prohibited', 'wppizza-admin');
				print"".json_encode($obj)."";
			exit();
			}

			/*order id*/
			$order_id=(int)$_POST['vars']['id'];

			/*blog id*/
			$blog_id=(int)$_POST['vars']['blogid'];

			/****get oder status ***/
			$order_status=esc_sql($_POST['vars']['status']);

			/** not checking for payment_status as it mught previously been COMPLETED, REFUNDED, CANCELLED etc **/
			$payment_status = false;

			/***************************************************************
				update order status with update timestamp
			****************************************************************/
			$update_db_values = array();

			/**
				amend order update
			**/
			$update_db_values['order_update'] 	= array('type'=> '%s', 'data' =>date('Y-m-d H:i:s', WPPIZZA_WP_TIME));

			/**
				set status, captured
			**/
			$update_db_values['order_status'] 	= array('type'=> '%s', 'data' => $order_status );

			/**
				update payment status too if set to refunded
			**/
			if($order_status=='REFUNDED' ){
				$update_db_values['payment_status'] 	= array('type'=> '%s', 'data' => $order_status );
			}

			/**
				set order delivered time if set as delivered
			**/
			if(in_array($order_status, unserialize(WPPIZZA_ADMIN_ORDER_DELIVERED_STATUS))){
				$update_db_values['order_delivered']= array('type'=> '%s', 'data' =>date('Y-m-d H:i:s', WPPIZZA_WP_TIME));
			}else{
				$update_db_values['order_delivered']= array('type'=> '%s', 'data' =>'0000-00-00 00:00:00');
			}

			/*
				run query
			*/
			$order_update = WPPIZZA()->db->update_order($blog_id, $order_id, false , $update_db_values, $payment_status );


			/**
				return new timestamp formatted to js
			**/
			$obj['update_timestamp']= date("d-M-Y H:i:s",WPPIZZA_WP_TIME);
			$obj['update_timestamp']= apply_filters('wppizza_filter_order_history_update_timestamp', $obj['update_timestamp'], WPPIZZA_WP_TIME);


			/*
				allow an action to run on order status change
			*/
			$obj['orderstatus_change_alert'] = '';/* ini as empty*/

			/*
				if filter has been added get order details
				and return alert as set
			*/
			if(has_filter('wppizza_on_orderstatus_change')){
				/* using helper function since 3.6 */
				$obj['orderstatus_change_alert'] = WPPIZZA() -> admin_helper -> process_orderstatus_change($blog_id, $order_id, $order_status);
			}


		print"".json_encode($obj)."";
		exit();
		}

		/********************************************
		*
		*
		*	[order history -> update custom]
		*
		*
		********************************************/
		if($_POST['vars']['field']=='customoptionchange' && isset($_POST['vars']['id']) && $_POST['vars']['id']>=0){
			/**********set header********************/
			header('Content-type: application/json');


			/*
				saving disabled
			*/
			if(WPPIZZA_DEV_ADMIN_NO_SAVE){
				$obj['update_prohibited'] = __('Update Prohibited', 'wppizza-admin');
				print"".json_encode($obj)."";
			exit();
			}

			/*order id*/
			$order_id=(int)$_POST['vars']['id'];

			/*blog id*/
			$blog_id=(int)$_POST['vars']['blogid'];


			/****get custom status ***/
			$custom_status=trim(esc_html(stripslashes($_POST['vars']['status'])));



			/***************************************************************
				update order status with update timestamp
			****************************************************************/
			$update_db_values = array();
			/** amend order update */
			$update_db_values['order_update'] 	= array('type'=> '%s', 'data' =>date('Y-m-d H:i:s', WPPIZZA_WP_TIME));
			/* set status, captured */
			$update_db_values['order_status_user_defined'] 	= array('type'=> '%s', 'data' => $custom_status );

			/* run query */
			$order_update = WPPIZZA()->db->update_order($blog_id, $order_id, false , $update_db_values );

			/**
				return new timestamp formatted to js
			**/
			$obj['update_timestamp']= date("d-M-Y H:i:s",WPPIZZA_WP_TIME);
			$obj['update_timestamp']= apply_filters('wppizza_filter_order_history_update_timestamp', $obj['update_timestamp'], WPPIZZA_WP_TIME);

			print"".json_encode($obj)."";
			exit();


		}
		/*****************************************************
		*
		*
		*	[order history -> delete order]
		*
		*
		*****************************************************/
		if($_POST['vars']['field']=='delete_order'){
			/**********set header********************/
			header('Content-type: application/json');

			/*
				check for delete capabilities
			*/
			if(!current_user_can('wppizza_cap_delete_order')){
				return;
			}

			/*
				saving disabled
			*/
			if(WPPIZZA_DEV_ADMIN_NO_SAVE){
				$obj['update_prohibited'] = __('Update Prohibited', 'wppizza-admin');
				print"".json_encode($obj)."";
			exit();
			}


			/*blog_id*/
			$blog_id=(int)$_POST['vars']['blog_id'];

			/*order id*/
			$order_delete_id=(int)$_POST['vars']['order_id'];

			/* delete from db */
			$res = WPPIZZA()->db->delete_order($order_delete_id, $blog_id);

			$obj['feedback']="".__('order deleted', 'wppizza-admin')."";
			print"".json_encode($obj)."";
			exit();
		}

		/*****************************************************
		*
		*
		*	[order history -> delete order bulk]
		*
		*
		*****************************************************/
		if($_POST['vars']['field']=='delete_order_bulk'){

			/**********set header********************/
			header('Content-type: application/json');
			$obj = array();//avoid php notices


			/*
				check for delete capabilities
			*/
			if(!current_user_can('wppizza_cap_delete_order')){
				return;
			}
			/*
				saving disabled
			*/
			if(WPPIZZA_DEV_ADMIN_NO_SAVE){
				$obj['update_prohibited'] = __('Update Prohibited', 'wppizza-admin');
				print"".json_encode($obj)."";
			exit();
			}
			/*
				delete each order from db
			*/
			foreach($_POST['vars']['delete_order_ids'] as $blog_order_id){
				$this_blog_order_id = explode('_', $blog_order_id);//split to get blog and order id
				$selected_blog_id = (int)$this_blog_order_id[0];
				$selected_order_id = (int)$this_blog_order_id[1];

				/* delete from db */
				$res = WPPIZZA()->db->delete_order($selected_order_id, $selected_blog_id);
			}
		print"".json_encode($obj)."";
		exit();
		}
		/********************************************
		*
		*
		*		[order history -> update notes]
		*
		*
		********************************************/
		if($_POST['vars']['field']=='ordernoteschange' && isset($_POST['vars']['order_id']) && $_POST['vars']['order_id']>=0){
			/**********set header********************/
			header('Content-type: application/json');

			/*
				saving disabled
			*/
			if(WPPIZZA_DEV_ADMIN_NO_SAVE){
				$obj['update_prohibited'] = __('Update Prohibited', 'wppizza-admin');
				print"".json_encode($obj)."";
			exit();
			}


			/*blog_id*/
			$blog_id=(int)$_POST['vars']['blog_id'];

			/*order id*/
			$order_id=(int)$_POST['vars']['order_id'];

			/*add notes to db*/
			$notes=wppizza_validate_string($_POST['vars']['entered_notes']);
			$notes_length=strlen($notes);

			/***************************************************************
				update order notes and update timestamp
			****************************************************************/
			$update_db_values = array();
			/** amend order update */
			$update_db_values['order_update'] = array('type'=> '%s', 'data' =>date('Y-m-d H:i:s', WPPIZZA_WP_TIME));
			/* set notes */
			$update_db_values['notes'] 	= array('type'=> '%s', 'data' => $notes );
			/* run query */
			$order_update = WPPIZZA()->db->update_order($blog_id, $order_id, false , $update_db_values);


			/**get strlen of notes to show/hide things as required*/
			//$obj['notes_class'] = empty($notes_length) ? true : false ;
			$obj['notes_length'] = $notes_length;
			$obj['notes_button_label'] = empty($notes_length) ? __('add notes', 'wppizza-admin') :  __('view notes', 'wppizza-admin') ;
			$obj['notes_updated_alert'] =__('ok', 'wppizza-admin');
			$obj['update_timestamp']= date("d-M-Y H:i:s",WPPIZZA_WP_TIME);
			$obj['update_timestamp']= apply_filters('wppizza_filter_order_history_update_timestamp', $obj['update_timestamp'], WPPIZZA_WP_TIME);


			print"".json_encode($obj)."";
			exit();
		}
		/*************************************************************************************
		*
		*
		*	[order history -> print order]
		*
		*
		*************************************************************************************/
		if($_POST['vars']['field']=='print-order' && $_POST['vars']['id']>=0){
			/**********set header********************/
			header('Content-type: application/json');
			/*ini array for json*/
			$obj=array();

			/*order id*/
			$order_id=(int)$_POST['vars']['id'];
			/*blog_id*/
			$blog_id=(int)$_POST['vars']['blog_id'];
			/*template type*/
			$template_type='print';

			/****************************************
				get the order
			****************************************/
			$args = array(
				'query' => array(
					'blogs' => array($blog_id),/* in case we are in a multisite setup */
					'order_id' => $order_id ,
					'payment_status' => array('COMPLETED','REFUNDED','REJECTED') ,
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
			$order = WPPIZZA() -> db -> get_orders($args, 'print-order');
			$order = reset($order['orders']);


			/****************************************
				no order exists that could be used
				as preview
			****************************************/
			if(empty($order)){
				$markup['str']="Error [AOH-101]:".__(' Sorry, this order does not exist.','wppizza-admin');
				print"".json_encode($markup)."";
				exit();
			}


			/***************************************
				get selected template id and vars
			***************************************/
			global $wppizza_options;
			$template_id = $wppizza_options['templates_apply'][$template_type];

			/* default values  (-1) */
			$as_html = true;
			$template_values = false;

			/* saved template , anything != -1 */
			if($template_id != -1){
				/**get set print template options**/
				$template_options = get_option(WPPIZZA_SLUG.'_templates_'.$template_type,0);
				$template_options = apply_filters('wppizza_filter_template_options', $template_options, $template_type);
				$template_values = $template_options[$template_id];
				$as_html = ($template_values['mail_type'] == 'phpmailer') ?  true : false ;
			}

			/****************************************
				what size do we want to open the window
			****************************************/
			$obj['window-width'] = apply_filters('wppizza_filter_admin_print_window_width','750');
			$obj['window-height'] = apply_filters('wppizza_filter_admin_print_window_height','550');
			/****************************************
				object to return to ajax, content type
			****************************************/
			$obj['content-type'] = ($as_html) ? 'text/html' : 'text/plain';
			/****************************************
				get html or plaintext output
			****************************************/
			if($as_html){
				$obj['markup']['html'] = WPPIZZA()->templates_email_print->get_template_email_html_sections_markup($order, $template_values, $template_type, $template_id );

			}else{
				/* plaintext returns sections too, so get the array first */
				$tpl = WPPIZZA()->templates_email_print->get_template_email_plaintext_sections_markup($order, $template_values, $template_type );
				$obj['markup']['plaintext'] = $tpl['markup'];
			}

			print"".json_encode($obj)."";
			exit();
		}
	}

	function order_history_results(){
		$order_history = json_decode($this->order_history_markup());
		echo $order_history -> markup;
	}
	/*********************************************************
	*
	*	[helper]
	* 	get orders markup
	*	@since 3.0
	*
	*********************************************************/
	function order_history_markup(){
		global $wppizza_options;
		$get_blog_url = get_bloginfo('url');
		$get_blog_id = get_current_blog_id();

		/********************************************************************
		#
		#
		#	get orders
		#	returns total count as well as paginated orders on page
		#
		#
		********************************************************************/

			/************************
				set query arguments
			************************/
			$args = array();

			$args['format'] = false; //always getting raw(ish) data for now until he have time to tidy up the order history markup script
			/*
				non-ajax including pagination, userid etc in GET vars
			*/
			if(empty($_POST['vars'])){

				/* set queried order status, sanitize and cast to uppercase to be safe */
				$order_status = empty($_GET['status']) ? false : strtoupper(wppizza_validate_alpha_only($_GET['status']));

				/*
					set args
				*/
				/* query for user id */
				$args['query']['wp_user_id'] = isset($_GET['uid']) ? $_GET['uid'] : false;

				/* query for order status */
				$args['query']['order_status'] = (empty($order_status) || $order_status == 'FAILED') ? false : $order_status;

				/* query payment_status instead if 'FAILED' (as this does not exists as order status) but we force added it to status dropdown */
				$args['query']['payment_status'] = (!empty($order_status) && $order_status == 'FAILED') ? $order_status : false ;

				/* query for any custom status */
				$args['query']['custom_status'] = empty($_GET['custom']) ? false : $_GET['custom'];

				/* set limits */
				$args['pagination']['paged'] = isset($_GET['paged']) ? $_GET['paged'] : 0 ;
				$args['pagination']['limit'] = isset($_GET['limit']) ? $_GET['limit'] : apply_filters('wppizza_filter_order_history_max_results',25) ;
			}
			/*
				ajax , changing dropdown options for example
			*/
			if(!empty($_POST['vars'])){

				/*
					parse ajax parameters
				*/
				$getparameters = $_POST['vars']['getparameters'];
				parse_str($getparameters, $parsed_parameters);

				/*
					set status for header / footer
					,sanitize and cast to uppercase to be safe
					or unset if needs be
				*/
				$order_status = empty($_POST['vars']['status']) ? false : strtoupper(wppizza_validate_alpha_only($_POST['vars']['status']));
				/**add status parameter here to pass on as get var if set*/
				if(!empty($order_status)){
					$parsed_parameters['order_status'] = $order_status;
				}else{
					unset($parsed_parameters['order_status']);
				}

				/*
					set args
				*/
				/* query for user id */
				$args['query']['wp_user_id'] = isset($parsed_parameters['uid']) ? $parsed_parameters['uid'] : false;


				/* query for order status */
				//old $args['query']['order_status'] = empty($_POST['vars']['status']) ? false : $_POST['vars']['status'];
				$args['query']['order_status'] = (empty($order_status) || $order_status == 'FAILED' ) ? false : $order_status;
				/**add order_status parameter here to pass on as get var if set*/
				if(!empty($args['query']['order_status'])){ $parsed_parameters['status'] = $args['query']['order_status'];} else { unset($parsed_parameters['status']); }


				/* query payment_status instead if 'FAILED' (as this does not exists as order status) but we force added it to status dropdown */
				$args['query']['payment_status'] = (!empty($order_status) && $order_status == 'FAILED') ? $order_status : false ;


				/* query for any custom status */
				$args['query']['custom_status'] = empty($_POST['vars']['custom']) ? false : $_POST['vars']['custom'];
				/**add custom_status parameter here to pass on as get var if set*/
				if(!empty($args['query']['custom_status'])){ $parsed_parameters['custom'] = $args['query']['custom_status'];} else { unset($parsed_parameters['custom']); }


				/* set limits */
				$args['pagination']['paged'] = isset($parsed_parameters['paged']) ? $parsed_parameters['paged'] : 0 ;
				$args['pagination']['limit'] = isset($_POST['vars']['limit']) ? $_POST['vars']['limit']: apply_filters('wppizza_filter_order_history_max_results',25) ;
				/**add limit parameter here to pass on as get var if set*/
				if(!empty($args['pagination']['limit'])){ $parsed_parameters['limit'] = $args['pagination']['limit'];} else { unset($parsed_parameters['limit']); }

			}
			/************************
				get order parameters
			************************/
			$orders = WPPIZZA() -> db -> get_orders($args, 'order_history_markup');

			/************************
				has orders flag
			************************/
			$has_orders = (!empty($orders['orders'])) ? true : false ;


		/********************************************************************
		#
		#	set pagination args
		#
		********************************************************************/
		$pagination_args = array(
			'total_orders' => $orders['total_number_of_orders'] ,
			'limit' => $args['pagination']['limit'],
			'parameters' => empty($parsed_parameters) ? false : $parsed_parameters,
		);


		/************************
		 set $user_id (for convenience)
		************************/
		$user_id = $args['query']['wp_user_id'];

		/**
			array that knows if gateway has refunds enabled
		**/
		$gateway_refunds = array();



		/********************************************************************
		#
		#	get gateway idents transforming to set label
		#
		********************************************************************/
		foreach($orders['gateways_idents'] as $gwIdent){
			$gateway_options = get_option(strtolower(WPPIZZA_SLUG.'_gateway_'.$gwIdent),0);
			if($gateway_options!=0){
				$gateway_label[strtoupper($gwIdent)]=!empty($gateway_options['_gateway_label']) ? $gateway_options['_gateway_label'] : strtoupper($gwIdent) ;
			}else{
				$gateway_label[strtoupper($gwIdent)]=strtoupper($gwIdent);
			}
		}


		/**************************************************************************************
		*
		*
		*
		*	markup of orders and pagination to return/output
		*
		*
		*
		**************************************************************************************/
		$markup=array();

			/**
				if we are only displaying for a distinct user id,
				make this clear here

			**/
			if(!empty($user_id) && is_numeric($user_id) && $user_id>0){
				$user_data = get_userdata($user_id);
				$markup['history_user_info'] = '<h2 id="'.WPPIZZA_SLUG.'_'.$this->section_key.'_user-info">'.sprintf(__('Orders for user "%s"', 'wppizza-admin'), print_r($user_data->user_login, true)).'</h2>';
			}

			/**
				bulk delete toggle
			**/
			if (current_user_can('wppizza_cap_delete_order')){
			$markup['bulk_delete'] = '<div id="'.WPPIZZA_SLUG.'_'.$this->section_key.'_bulk-delete"><label class="button"><input class="'.WPPIZZA_SLUG.'_'.$this->section_key.'_bulk-delete-toggle" type="checkbox" value="1" /></label><label class="'.WPPIZZA_SLUG.'_'.$this->section_key.'_bulk-delete-do '.WPPIZZA_SLUG.'-dashicons dashicons-trash" /></div>';
			}

			/**
				pagination top
			**/
			$markup['pagination_top'] = $this->pagination_markup($pagination_args, 'top');


			/**
				orders table
			**/
			$markup['orders_table_']="<table id='".WPPIZZA_SLUG."_list_".$this->section_key."' class='widefat fixed striped'>";

				/**
					orders table header
				**/
				$markup['thead'] = $this -> thead_tfoot_markup($orders, $order_status, 'thead');

				/**
					orders table footer
				**/
				$markup['tfoot'] = $this -> thead_tfoot_markup($orders, $order_status, 'tfoot');



				/*************************************************************************
				#
				#
				#	the orders loop table (or no results)
				#
				#
				*************************************************************************/
				$markup['tbody_']="<tbody id='the-list'>";

					/***************
					*
					*	no orders
					*
					***************/
					if(count($orders['orders'])<=0){
						$markup['tbody_no_results']="<tr><td colspan='4' id='".WPPIZZA_SLUG."-".$this->section_key."-no-orders'>".__('no results found','wppizza-admin')."</td></tr>";
					}

					/***************
					*
					*	orders loop
					*
					***************/
					if(count($orders['orders'])>0){
					foreach($orders['orders'] as $uoKey => $order){


						/**
							check if gateway has refunds enabled
							and has delete capabilities
						**/
						$gateway_ident = $order['initiator'];
						if(!isset($gateway_refunds[$gateway_ident]) && current_user_can('wppizza_cap_delete_order')){
							$gw_class = 'WPPIZZA_GATEWAY_'.$gateway_ident.'';
							if (class_exists( $gw_class )){
								$gw = new $gw_class();
								$gw_name = $gw->gatewayName;// store name of gateway that called the refund
								$gw_allows_refunds = !empty($gw->gatewayRefunds) ? $gw->gatewayRefunds : false ;


								/*
									check if we are referring to another class perhaps
									and see if that one has refunds enabled in case
									we want to use that one
								*/
								if(class_exists($gw_allows_refunds)){
									$gw_class = $gw_allows_refunds;
									$gw = new $gw_class();
									$gw_allows_refunds = !empty($gw->gatewayRefunds) ? $gw->gatewayRefunds : false ;
								}

								/*
									set gw name, class and refund method
								*/
								$gateway_refunds[$gateway_ident] = array('name' => $gw_name , 'classname' => $gw_class ,'method' => $gw_allows_refunds);
							}
						}

						/**
							if payment status failed , override order status class
						**/
						$order_status_class=$order['order_status'];
						if($order['payment_status'] == 'failed'){
							$order_status_class='failed';
						}

						/**
							if payment status unconfirmed, do not add class that opens thickbox
						**/
						$open_thickbox_class = ($order['payment_status']!='unconfirmed') ? "".WPPIZZA_SLUG."-".$this->section_key."-do-thickbox" : "";


						/**
							payment status unconfirmed, add class
						**/
						$payment_unconfirmed_class = ($order['payment_status']=='unconfirmed') ? " ".WPPIZZA_SLUG."-".$this->section_key."-payment-unconfirmed" : "" ;

						/**
							add/apply filters to individual variables where required
							and any variables/markup we want to  re-use in multiple places
						**/
						/**allow filtering/formatting. supply current time too for servers where php.ini timezone is incorrectly set*/
						$order['order_date'] = apply_filters('wppizza_filter_order_history_order_timestamp', $order['order_date'], strtotime($order['order_date'], WPPIZZA_WP_TIME));
						/* order delivered */
						$order['order_delivered'] = apply_filters('wppizza_filter_order_history_order_timestamp', $order['order_delivered'], strtotime($order['order_delivered'], WPPIZZA_WP_TIME));
						/* order transation id */
						$order['transaction_id'] = apply_filters('wppizza_filter_transaction_id',$order['transaction_id'], $order['id'] );


						/**allow filtering/formatting. synchronous in line with js result when changing order status - supply current time too for servers where php.ini timezone is incorrectly set*/
						$cast_order_update = (int)$order['order_update'];/* simply cast date to integer to check if it's zero */
						$order_update=!empty($cast_order_update) ? ''.$order['order_update'] : $order['order_date'] ;
						$order_update= apply_filters('wppizza_filter_order_history_update_timestamp', $order_update, strtotime($order_update, WPPIZZA_WP_TIME));



						/******************************
						*
						*	thickbox
						*	needs double div wrapping to be able to style
						*
						*******************************/
						$orderthickbox=array();
						$orderthickbox['div_']="<div id='".WPPIZZA_SLUG."-".$this->section_key."-thickbox-".$uoKey."' style='display:none'><div class='".WPPIZZA_SLUG."-".$this->section_key."-thickbox'>";

						if($order['payment_status']!='failed'){
							$orderthickbox['order_status']="<pre class='".WPPIZZA_SLUG."-".$this->section_key."-thickbox-order_status'><span>".__('Status', 'wppizza-admin')." ".WPPIZZA()->admin_helper->orderhistory_order_status_select($this->section_key, 'thickbox', $uoKey, $order['order_status'])."</span></pre>";
							$orderthickbox['customer_details']="<pre class='".WPPIZZA_SLUG."-".$this->section_key."-thickbox-customer_details'>".$order['customer_details']."</pre>";
							$orderthickbox['order_details']="<pre class='".WPPIZZA_SLUG."-".$this->section_key."-thickbox-order_details'>".$order['order_details']."</pre>";
						}else{
							/*sanitize for output*/
							$transaction_errors=WPPIZZA()->admin_helper->unserialize_errors_to_string($order['transaction_errors']);
							$mail_errors=WPPIZZA()->admin_helper->unserialize_errors_to_string($order['mail_error']);
							$orderthickbox['transaction_errors']="<pre class='".WPPIZZA_SLUG."-".$this->section_key."-thickbox-transaction_errors'>".$transaction_errors."</pre>";
							$orderthickbox['mail_errors']="<pre class='".WPPIZZA_SLUG."-".$this->section_key."-thickbox-mail_errors'>".$mail_errors."</pre>";
						}
						$orderthickbox['_div']="</div></div>";

						/**allow filtering**/
						$orderthickbox= apply_filters('wppizza_filter_orderhistory_thickbox', $orderthickbox, $order);
						$orderthickbox=implode('',$orderthickbox);


						/**
							add visible/hidden class to summary/full details depending on status
						**/
						$summary_visibility_status = WPPIZZA() -> admin_helper -> orderhistory_summary_visibility_by_status();
						$summary_visibility_payment_status = WPPIZZA() -> admin_helper -> orderhistory_summary_visibility_by_payment_status();
						/*class and dashicons*/
						$summary_visibility_class='';
						//$summary_visibility_dashicon='';
						$details_visibility_class='';
						//$details_visibility_dashicon='';
						if(in_array($order['order_status'],$summary_visibility_status) || in_array($order['payment_status'],$summary_visibility_payment_status)){
							$summary_visibility_class="".WPPIZZA_SLUG."-".$this->section_key."-ordersummary-active";
							$details_visibility_class="".WPPIZZA_SLUG."-".$this->section_key."-orderdetails-inactive";
						}

						/**
							ini new empty array for this order
						**/
						$order_markup = array();


						/****************************************************************************
						*
						*
						*	[first row -> summary - only shown if delivered]
						*
						*
						****************************************************************************/
						/*open tr*/
						$order_markup['summary_tr_'] = "<tr id='".WPPIZZA_SLUG."-".$this->section_key."-ordersummary-".$uoKey."' class='".WPPIZZA_SLUG."-".$this->section_key."-ordersummary ".WPPIZZA_SLUG."-".$this->section_key."-orderstatus-".$order_status_class."".$payment_unconfirmed_class." ".$summary_visibility_class."'>";


							/***************************************************************
							*
							*	first row, first column,
							*	summary transaction info
							*
							****************************************************************/
							$order_markup['summary_info_td_'] ="<td  id='".WPPIZZA_SLUG."-".$this->section_key."-column-details-summary-".$uoKey."' class='".WPPIZZA_SLUG."-".$this->section_key."-column-details-summary'>";


								$ordersummary_info=array();

								/******************************
								*
								*	multisite, blog info if exists, appropriate
								*
								*******************************/
								if(!empty($order['blog_info']['blogname'])){
									$ordersummary_info['blogname']= "<span class='".WPPIZZA_SLUG."-".$this->section_key."-ordersummary-blogname'>".$order['blog_info']['blogname']."</span>";
								}
								/******************************
								*
								*	order date
								*
								*******************************/
								$ordersummary_info['order_date']="<span class='".WPPIZZA_SLUG."-".$this->section_key."-ordersummary-order_date'>".$order['order_date']."</span>";

								/******************************
								*
								*	get used gateway label
								*
								*******************************/
								$ordersummary_info['payment']="<span class='".WPPIZZA_SLUG."-".$this->section_key."-ordersummary-payment'>".$wppizza_options['localization']['common_label_order_payment_method']." ". $gateway_label[$order['initiator']] ."</span>";

								/******************************
								*
								*	status
								*
								*******************************/
								$ordersummary_info['status']="<span class='".WPPIZZA_SLUG."-".$this->section_key."-ordersummary-status'>".__('Status', 'wppizza-admin').": ". wppizza_order_status_default(false,$order['order_status']) ."</span>";

								/******************************
								*
								*	unconfirmed
								*
								*******************************/
								if($order['payment_status']=='unconfirmed'){
									$ordersummary_info['unconfirmed']="<span class='".WPPIZZA_SLUG."-".$this->section_key."-ordersummary-unconfirmed'>".__('Unconfirmed', 'wppizza-admin')."</span>";
								}

								/******************************
								*
								*	delivered time
								*
								*******************************/
								if($order['order_status'] == 'delivered' && !empty($order['order_delivered'])){
									$ordersummary_info['order_delivered']="<span class='".WPPIZZA_SLUG."-".$this->section_key."-ordersummary-order_delivered'>".__('On', 'wppizza-admin').": ". $order['order_delivered'] ."</span>";
								}

								/******************************
								*
								*	custom status
								*
								*******************************/
								if($wppizza_options['localization']['order_history_custom_status_options'] !='' && $order['order_status_user_defined']!=''){
									$ordersummary_info['custom_status']="<span class='".WPPIZZA_SLUG."-".$this->section_key."-ordersummary-order_status_user_defined'>".$wppizza_options['localization']['order_history_custom_status_label']." ".$order['order_status_user_defined']."</span>";
								}

								/**allow filtering**/
								$ordersummary_info = apply_filters('wppizza_filter_orderhistory_ordersummary_info', $ordersummary_info, $order);
								$order_markup['summary_info']='<div>'.implode('<br />',$ordersummary_info).'</div>';



							$order_markup['_summary_info_td']="</td>";

							/***************************************************************
							*
							*	first row, second column,
							*	summary order info
							*
							****************************************************************/
							$order_markup['summary_td_']="<td  id='".WPPIZZA_SLUG."-".$this->section_key."-column-order-summary-".$uoKey."' class='".WPPIZZA_SLUG."-".$this->section_key."-column-order-summary'>";

								/**get enabled formfields and display first 4 non-empty, non-textarea lines **/
								$formfields=WPPIZZA()->admin_helper->admin_orderform_enabled_formfields('orderhistory');
								$ordersummary_customer=array();
								$count=0;
								/*custom define limit if required*/
								$limit=apply_filters('wppizza_filter_orderhistory_summary_max_lines_cdetails', 4);
								foreach($formfields as $ffKey=>$ff){
									/*this is a summary only, so omit textareas, empty values and limit to first 4 (or filtered) lines only*/
									if($ff!='textarea' && !empty($order['customer_ini'][$ffKey])){
										if($count>=$limit){break;}
										/* implode if array (multicheckboxes) */
										$customer_value = is_array($order['customer_ini'][$ffKey]) ? implode(', ',$order['customer_ini'][$ffKey]) : $order['customer_ini'][$ffKey];
										$ordersummary_customer[$ffKey] = "<span>".$ff['lbl'] . ' ' .$customer_value ."</span>";

										$count++;
									}
								}
								/**allow filtering**/
								$ordersummary_customer = apply_filters('wppizza_filter_orderhistory_ordersummary_customer', $ordersummary_customer, $order);
								$order_markup['summary_customer']="<div class='".WPPIZZA_SLUG."-".$this->section_key."-ordersummary-customer'>".implode('',$ordersummary_customer)."</div>";


							/***************************************************************
							*
							*	first row, second column,
							*	summary order info
							*
							****************************************************************/

								$ordersummary_order=array();

								/******************************
								*
								*	no of items
								*
								*******************************/
								$ordersummary_order['items']="<span class='".WPPIZZA_SLUG."-".$this->section_key."-ordersummary-items'>".__('Items','wppizza-admin')." [".$order['order_no_of_items']."]: ".wppizza_format_price($order['order_items_total'], $order['currency'])."</span>";
								/******************************
								*
								*	discounts
								*
								*******************************/
								if(!empty($order['order_discount'])){
									$ordersummary_order['discount']="<span class='".WPPIZZA_SLUG."-".$this->section_key."-ordersummary-discount'>".__('Discount','wppizza-admin').": -".wppizza_format_price($order['order_discount'], $order['currency']) ."</span>";
								}
								/******************************
								*
								*	taxes - only show added here (for now) so the total sum correlates
								*
								*******************************/
								if(!empty($order['order_taxes']) && $order['order_taxes_included'] == 'N'){
									$taxes_included = ($order['order_taxes_included'] == 'Y') ? ' ['.__('Included','wppizza-admin').']' : '' ;
									$ordersummary_order['taxes']="<span class='".WPPIZZA_SLUG."-".$this->section_key."-ordersummary-taxes'>".__('Taxes','wppizza-admin').": ".wppizza_format_price($order['order_taxes'],$order['currency']) ."".$taxes_included."</span>";
								}
								/******************************
								*
								*	Shipping/Handling
								*
								*******************************/
								if(!empty($order['order_delivery_charges']) || !empty($order['order_handling_charges']) ){
									$delivery=empty($order['order_delivery_charges']) ? '' : ''.__('Delivery','wppizza-admin').': '.wppizza_format_price($order['order_delivery_charges'],$order['currency']);


									if(empty($order['order_delivery_charges'])){/* no devider if no delivery charges */
										$handling=empty($order['order_handling_charges']) ? '' : __('Handling','wppizza-admin').': '.wppizza_format_price($order['order_handling_charges'],$order['currency']);
									}else{
										$handling=empty($order['order_handling_charges']) ? '' : ' | '.__('Handling','wppizza-admin').': '.wppizza_format_price($order['order_handling_charges'],$order['currency']);
									}

									$ordersummary_order['delivery_handling']="<span class='".WPPIZZA_SLUG."-".$this->section_key."-ordersummary-delivery_handling'>". $delivery ."  ". $handling ."</span>";
								}
								if(empty($order['order_delivery_charges']) && $order['order_self_pickup']=='N'){
									$ordersummary_order['free_delivery']="<span class='".WPPIZZA_SLUG."-".$this->section_key."-ordersummary-free_delivery'>".__('Free Delivery','wppizza-admin')."</span>";
								}
								/******************************
								*
								*	pickup
								*
								*******************************/
								if($order['order_self_pickup']=='Y'){
									$ordersummary_order['pickup']="<span class='".WPPIZZA_SLUG."-".$this->section_key."-ordersummary-self-pickup'>".__('pickup by customer','wppizza-admin')."</span>";
								}
								/******************************
								*
								*	tips
								*
								*******************************/
								if(!empty($order['order_tips'])){
									$ordersummary_order['tips']="<span class='".WPPIZZA_SLUG."-".$this->section_key."-ordersummary-tips'>".__('Tips','wppizza-admin').": ".wppizza_format_price($order['order_tips'],$order['currency']) ."</span>";
								}
								/******************************
								*
								*	total
								*
								*******************************/
								$ordersummary_order['total']="<span class='".WPPIZZA_SLUG."-".$this->section_key."-ordersummary-total'>".__('Total','wppizza-admin').": ".wppizza_format_price($order['order_total'],$order['currency']) ."</span>";


								/**allow filtering**/
								$ordersummary_order= apply_filters('wppizza_filter_orderhistory_ordersummary_order', $ordersummary_order, $order);
								$order_markup['summary_order']="<div class='".WPPIZZA_SLUG."-".$this->section_key."-ordersummary-order'>".implode('',$ordersummary_order)."</div>";



							$order_markup['_summary_td']="</td>";

							/***************************************************************
							*
							*	first row, third column,
							*	summary actions (user id/guest)
							*
							****************************************************************/
							$order_markup['summary_actions_td_']="<td id='".WPPIZZA_SLUG."-".$this->section_key."-column-actions-summary-".$uoKey."' class='".WPPIZZA_SLUG."-".$this->section_key."-column-actions-summary'>";

								$ordersummary_actions=array();

								/******************************
								*
								*	userid/guest
								*
								*******************************/
								if(!empty($order['wp_user_id'])){
									$ordersummary_actions['wp_user_id']="<span class='".WPPIZZA_SLUG."-".$this->section_key."-ordersummary-wp_user_id'>			<a href='".$get_blog_url."/wp-admin/edit.php?post_type=".WPPIZZA_POST_TYPE."&page=customers&s=".$order['wp_user_id']."' class='".WPPIZZA_SLUG."-dashicons dashicons-businessman' title='".__('User Id', 'wppizza-admin').": ".$order['wp_user_id']."'></a></span>";
									$ordersummary_actions['wp_user_id'].="<span class='".WPPIZZA_SLUG."-".$this->section_key."-ordersummary-wp_user_id-orders'>	<a href='".$get_blog_url."/wp-admin/edit.php?post_type=".WPPIZZA_POST_TYPE."&page=".$this->section_key."&uid=".$order['wp_user_id']."' class='".WPPIZZA_SLUG."-dashicons dashicons-chart-line' title='".__('Show orders for user', 'wppizza-admin').": ".$order['wp_user_id']."'></a></span>";
								}else{
									$ordersummary_actions['wp_user_id']="<span class='".WPPIZZA_SLUG."-".$this->section_key."-wp_user_guest'><a class='".WPPIZZA_SLUG."-dashicons dashicons-admin-users' title='".__('Guest', 'wppizza-admin')."'></a>".__('Guest', 'wppizza-admin')."</span>";
								}

								/**allow filtering**/
								$ordersummary_actions= apply_filters('wppizza_filter_orderhistory_ordersummary_actions', $ordersummary_actions, $order);
								$order_markup['summary_actions']=implode('',$ordersummary_actions);

							$order_markup['_summary_actions_td']="</td>";

						/*close tr*/
						$order_markup['_summary_tr'] = "</tr>";


						/****************************************************************************
						*
						*
						*	[second row -> details - shown if != delivered]
						*
						*
						****************************************************************************/
						$order_markup['details_tr_'] = "<tr id='".WPPIZZA_SLUG."-".$this->section_key."-order-".$uoKey."' class='".WPPIZZA_SLUG."-".$this->section_key."-orderdetails ".WPPIZZA_SLUG."-".$this->section_key."-orderstatus-".$order_status_class."".$payment_unconfirmed_class."  ".$details_visibility_class."'>";


							/***************************************************************
							*
							*	second row, first column,
							*	order info (id, transaction id etc)
							*
							****************************************************************/
							$order_markup['details_info_td_']="<td id='".WPPIZZA_SLUG."-".$this->section_key."-column-details-".$uoKey."' class='".WPPIZZA_SLUG."-".$this->section_key."-column-details'>";


									$orderdetails_info=array();

									/******************************
									*
									*	multisite, blog info if exists, appropriate
									*	only on parent site if order from all sites enabled
									*******************************/
									if(is_multisite() && !empty($order['blog_info']['blogname']) && $get_blog_id == 1 && !empty($wppizza_options['settings']['wp_multisite_order_history_all_sites'])){
										$orderdetails_info['blogname']= "<span id='".WPPIZZA_SLUG."-".$this->section_key."-order-blogname-".$uoKey."' class='".WPPIZZA_SLUG."-".$this->section_key."-order-blogname'>".$order['blog_info']['blogname']."</span>";
									}
									/******************************
									*
									*	order date
									*
									*******************************/
									$orderdetails_info['date']= "<span id='".WPPIZZA_SLUG."-".$this->section_key."-order-date-".$uoKey."' class='".WPPIZZA_SLUG."-".$this->section_key."-order-date'>".$order['order_date']."</span>";


									/******************************
									*
									*	get delivery type (pickup/delivery)
									*	provided pickup option is enabled
									*
									*******************************/
									if(!empty($wppizza_options['order_settings']['order_pickup'])){
										$orderdetails_info['delivery_type']="<span class='".WPPIZZA_SLUG."-".$this->section_key."-ordersummary-delivery_type'>";
										$orderdetails_info['delivery_type'] .= $wppizza_options['localization']['common_label_order_delivery_type'].' ';
										if($order['order_self_pickup']=='N'){
											$orderdetails_info['delivery_type'] .= "<span id='".WPPIZZA_SLUG."-".$this->section_key."-order-delivery_type-".$uoKey."' class='".WPPIZZA_SLUG."-".$this->section_key."-order-delivery_type-delivery'>";
												$orderdetails_info['delivery_type'] .= $wppizza_options['localization']['common_value_order_delivery'];
											$orderdetails_info['delivery_type'] .="</span>";
										}else{
											$orderdetails_info['delivery_type'] .= "<span id='".WPPIZZA_SLUG."-".$this->section_key."-order-delivery_type-".$uoKey."' class='".WPPIZZA_SLUG."-".$this->section_key."-order-delivery_type-pickup'>";
												$orderdetails_info['delivery_type'] .= $wppizza_options['localization']['common_value_order_pickup'] ;
											$orderdetails_info['delivery_type'] .="</span>";
										}

										$orderdetails_info['delivery_type'] .="</span>";
									}


									/******************************
									*
									*	get used gateway label
									*
									*******************************/
									$orderdetails_info['payment']="<span class='".WPPIZZA_SLUG."-".$this->section_key."-order-payment'>".$wppizza_options['localization']['common_label_order_payment_method']." <span id='".WPPIZZA_SLUG."-".$this->section_key."-order-payment-".$uoKey."'>". $gateway_label[$order['initiator']] ."</span></span>";


									/******************************
									*
									*	transaction_id
									*
									*******************************/
									if(!empty($order['transaction_id'])){
										$orderdetails_info['transaction_id']="<span id='".WPPIZZA_SLUG."-".$this->section_key."-order-txid-".$uoKey."' class='".WPPIZZA_SLUG."-".$this->section_key."-order-txid' title='".$order['transaction_id']."'>".$order['transaction_id']."</span>";
									}


									/****************************
									*
									*	print order status dropdown if not failed or unconfirmed
									*
									****************************/
									/**
										dropdown only if not failed and not unconfirmed
									**/
									if($order['payment_status']!='failed' && $order['payment_status']!='unconfirmed'){

										$orderdetails_info['status']="<span class='".WPPIZZA_SLUG."-".$this->section_key."-order-status'>".__('Status', 'wppizza-admin')." ";
										/**dropdown*/
											$orderdetails_info['status'].=WPPIZZA()->admin_helper->orderhistory_order_status_select($this->section_key, 'details', $uoKey, $order['order_status']);
										$orderdetails_info['status'].="</span>";
									}
									/**
										static label if failed
									**/
									if($order['payment_status']=='failed'){
										$orderdetails_info['status']="<span class='".WPPIZZA_SLUG."-".$this->section_key."-order-status-failed'>".__('Status', 'wppizza-admin').": ".__('Failed','wppizza-admin')."</span>";
									}

									/**
										add static unconfirmed label if unconfirmed
									**/
									if($order['payment_status']=='unconfirmed'){
										$orderdetails_info['status'] ="<span class='".WPPIZZA_SLUG."-".$this->section_key."-ordersummary-status'>".__('Status', 'wppizza-admin').": ". wppizza_order_status_default(false,$order['order_status']) ."</span>";
										$orderdetails_info['status'].="<span class='".WPPIZZA_SLUG."-".$this->section_key."-order-status-unconfirmed'>".__('Unconfirmed', 'wppizza-admin')."</span>";
									}

									/**custom options **/
									if($wppizza_options['localization']['order_history_custom_status_options'] !='' ){
										$orderdetails_info['custom_status']="<span class='".WPPIZZA_SLUG."-".$this->section_key."-order-status-custom'>".$wppizza_options['localization']['order_history_custom_status_label']." ";
										/**dropdown*/
										$orderdetails_info['custom_status'].=WPPIZZA()->admin_helper->orderhistory_custom_options_select($this->section_key, 'details', $uoKey, $order['order_status_user_defined'], false);

										$orderdetails_info['custom_status'].="</span>";
									}


									/******************************
									*
									*	refunds
									*
									*******************************/
									if(!empty($gateway_refunds[$order['initiator']]['method'])){
										$orderdetails_info['refund'] = "<div id='".WPPIZZA_SLUG."-".$this->section_key."-refund-".$uoKey."' class='".WPPIZZA_SLUG."-".$this->section_key."-refund'>";
											$orderdetails_info['refund'] .= "<label class='".WPPIZZA_SLUG."-".$this->section_key."-enable-refund-label'>";
											$orderdetails_info['refund'] .= "<input type='checkbox' class='".WPPIZZA_SLUG."-".$this->section_key."-enable-refund' value='".$gateway_refunds[$order['initiator']]['classname']."' />";
											$orderdetails_info['refund'] .= sprintf(__('Refund in %s', 'wppizza-admin'), $gateway_refunds[$gateway_ident]['name']);
											$orderdetails_info['refund'] .= "</label>";
											$orderdetails_info['refund'] .= " <input type='button' id='".WPPIZZA_SLUG."-".$this->section_key."-process-refund-".$uoKey."' class='".WPPIZZA_SLUG."-".$this->section_key."-process-refund' value='".__('OK', 'wppizza-admin')."' />";
										$orderdetails_info['refund'] .= "</div>";
									}


									/******************************
									*
									*	order update . empty if still
									*	0000-00-00 00:00:00
									*
									*******************************/
									$orderdetails_info['order_update'] ="<span id='".WPPIZZA_SLUG."-".$this->section_key."-order-update-".$uoKey."'>".__('Updated', 'wppizza-admin').": <span id='".WPPIZZA_SLUG."-".$this->section_key."-order-update-".$uoKey."-time'>".$order_update."</span>";

									/**allow filtering**/
									$orderdetails_info= apply_filters('wppizza_filter_orderhistory_order_info', $orderdetails_info, $order);
									$order_markup['details_info']=''.implode('',$orderdetails_info).'';// brs set by display:block


							$order_markup['_details_info_td'] = "</td>";


							/***************************************************************
								second row, second column,
								order details or error details on failed
							****************************************************************/
							$order_markup['details_order_td_']="<td id='".WPPIZZA_SLUG."-".$this->section_key."-column-order-".$uoKey."' class='".WPPIZZA_SLUG."-".$this->section_key."-column-order'>";

								/*
									customer details
								*/
								$orderdetails_customer=array();

								/**non failed*/
								if($order['payment_status']!='failed'){
									$orderdetails_customer['customer_details']="<div id='".WPPIZZA_SLUG."-".$this->section_key."-customer-pre-".$uoKey."' class='".WPPIZZA_SLUG."-".$this->section_key."-pre ".$open_thickbox_class."'><pre>".$order['customer_details']."</pre></div>";
								}

								/**failed*/
								if($order['payment_status']=='failed'){
									/*sanitize for output*/
									$transaction_errors = WPPIZZA()->admin_helper->unserialize_errors_to_string($order['transaction_errors']);
									$orderdetails_customer['transaction_errors']="<div id='".WPPIZZA_SLUG."-".$this->section_key."-customer-pre-".$uoKey."' class='".WPPIZZA_SLUG."-".$this->section_key."-pre'><pre>".$transaction_errors."</pre></div>";
								}

								/**allow filtering**/
								$orderdetails_customer= apply_filters('wppizza_filter_orderhistory_order_customer', $orderdetails_customer, $order);
								$order_markup['details_customer']=implode('<br />',$orderdetails_customer);


								/*
									order details
								*/
								$orderdetails_order=array();

								/**non failed*/
								if($order['payment_status']!='failed'){
									$orderdetails_order['order_details']="<div id='".WPPIZZA_SLUG."-".$this->section_key."-order-pre-".$uoKey."' class='".WPPIZZA_SLUG."-".$this->section_key."-pre ".$open_thickbox_class."'><pre>".$order['order_details']."</pre></div>";
								}
								/**failed*/
								if($order['payment_status']=='failed'){
									/*sanitize for output*/
									$mail_errors=WPPIZZA()->admin_helper->unserialize_errors_to_string($order['mail_error']);
									$orderdetails_order['mail_errors']="<div id='".WPPIZZA_SLUG."-".$this->section_key."-order-pre-".$uoKey."' class='".WPPIZZA_SLUG."-".$this->section_key."-pre'><pre>".$mail_errors."</pre></div>";
								}

								/**non failed*/
								if($order['payment_status']!='failed' ){
									$order_markup['show_details_button']="<div id='".WPPIZZA_SLUG."-".$this->section_key."-vieworder-".$uoKey."' class='".WPPIZZA_SLUG."-".$this->section_key."-vieworder ".$open_thickbox_class." button'>".__('view order', 'wppizza-admin')."</div>";
								}
								/**failed*/
								if($order['payment_status']=='failed'){
									$order_markup['show_details_button']="<div id='".WPPIZZA_SLUG."-".$this->section_key."-viewfailed-".$uoKey."' class='".WPPIZZA_SLUG."-".$this->section_key."-viewfailed ".$open_thickbox_class." button'>".__('view details', 'wppizza-admin')."</div>";
								}

								/**allow filtering**/
								$orderdetails_order= apply_filters('wppizza_filter_orderhistory_order_details', $orderdetails_order, $order);
								$order_markup['details_order']=implode('<br />',$orderdetails_order);


							$order_markup['_details_order_td']="</td>";


							/***************************************************************
								second row, fourth column,
								delete, print, add notes
							****************************************************************/
							$order_markup['details_actions_td_']="<td id='".WPPIZZA_SLUG."-".$this->section_key."-column-actions-".$uoKey."' class='".WPPIZZA_SLUG."-".$this->section_key."-column-actions'>";

								$orderdetails_actions=array();
								/******************************
								*
								*	delete order button [admin only]
								*
								******************************/
								if (current_user_can('wppizza_cap_delete_order')){
									$orderdetails_actions['delete_'] = "<span class='".WPPIZZA_SLUG."-".$this->section_key."-order-delete_order'>";
									$orderdetails_actions['delete']  = "<a href='javascript:void(0)' id='".WPPIZZA_SLUG."-".$this->section_key."-delete-order-".$uoKey."' class='".WPPIZZA_SLUG."-".$this->section_key."-delete-order ".WPPIZZA_SLUG."-dashicons dashicons-trash' title='".__('delete order', 'wppizza-admin')."'></a>";
									/* bulk delete toggle */
									$orderdetails_actions['delete_bulk'] = '<input class="'.WPPIZZA_SLUG.'_'.$this->section_key.'_delete-selected" type="checkbox" value="'.$uoKey.'" />';
									$orderdetails_actions['_delete'] = "</span>";
								}
								/************************
								*
								*	print order button
								*	(unless its unconfirmed)
								************************/
								if($order['payment_status']!='unconfirmed'){
									$orderdetails_actions['print_'] = "<span class='".WPPIZZA_SLUG."-".$this->section_key."-order-print_order'>";
									$orderdetails_actions['print']  = "<a href='javascript:void(0);'  id='".WPPIZZA_SLUG."-".$this->section_key."-print-order-".$uoKey."' class='".WPPIZZA_SLUG."-".$this->section_key."-print-order ".WPPIZZA_SLUG."-dashicons dashicons-media-document' title='".__('print order', 'wppizza-admin')."'></a>";
									$orderdetails_actions['_print'] = "</span>";
								}

								/******************************
								*
								*	userid/guest
								*
								*******************************/
								if(!empty($order['wp_user_id'])){
									$orderdetails_actions['wp_user_id_'] = "<span>";
									$orderdetails_actions['wp_user_id']  = "<span class='".WPPIZZA_SLUG."-".$this->section_key."-order-wp_user_id'><a href='".$get_blog_url."/wp-admin/edit.php?post_type=".WPPIZZA_POST_TYPE."&page=customers&s=".$order['wp_user_id']."' class='".WPPIZZA_SLUG."-dashicons dashicons-businessman' title='".__('User Id', 'wppizza-admin').": ".$order['wp_user_id']."'></a></span>";
									$orderdetails_actions['wp_user_id'] .= "<span class='".WPPIZZA_SLUG."-".$this->section_key."-order-wp_user_id-orders'><a href='".$get_blog_url."/wp-admin/edit.php?post_type=".WPPIZZA_POST_TYPE."&page=".$this->section_key."&uid=".$order['wp_user_id']."' class='".WPPIZZA_SLUG."-dashicons dashicons-chart-line' title='".__('Show orders for user', 'wppizza-admin').": ".$order['wp_user_id']."'></a></span>";
									$orderdetails_actions['_wp_user_id']  = "</span>";
								}else{
									$orderdetails_actions['wp_user_id_'] = "<span class='".WPPIZZA_SLUG."-".$this->section_key."-wp_user_guest'>";
									$orderdetails_actions['wp_user_id']  = "<a class='".WPPIZZA_SLUG."-dashicons dashicons-admin-users' title='".__('Guest', 'wppizza-admin')."'></a>".__('Guest', 'wppizza-admin')."</span>";
									$orderdetails_actions['_wp_user_id'] = "</span>";
								}

								/************************
								*
								*	add/edit notes button
								*
								************************/
								if(trim($order['notes'])==''){
									$view_add_notes_label=__('add notes', 'wppizza-admin');
									$order_has_notes_class='';
								}else{
									$view_add_notes_label=__('view notes', 'wppizza-admin');
									$order_has_notes_class="".WPPIZZA_SLUG."-".$this->section_key."-order-has-notes";
								}
								$orderdetails_actions['notes']="<span class='".WPPIZZA_SLUG."-".$this->section_key."-order-add_notes'><a href='javascript:void(0);'  id='".WPPIZZA_SLUG."-".$this->section_key."-order-view-add-notes-".$uoKey."' class='".WPPIZZA_SLUG."-".$this->section_key."-order-view-add-notes ".$order_has_notes_class." button'>".$view_add_notes_label."</a></span>";


								/**
									allow filtering
								**/
								$orderdetails_actions = apply_filters('wppizza_filter_orderhistory_order_actions', $orderdetails_actions, $order);
								$order_markup['details_actions']=implode('',$orderdetails_actions);

							$order_markup['_details_actions_td']="</td>";

						$order_markup['_details_tr'] = "</tr>";


						/****************************************************************************
						*
						*
						*	[third row -> order notes and thickbox]
						*
						*
						****************************************************************************/
						$order_markup['notes_tr_'] = "<tr id='".WPPIZZA_SLUG."-".$this->section_key."-order-notes-".$uoKey."' class='".WPPIZZA_SLUG."-".$this->section_key."-order-notes'>";
							$order_markup['notes_td_']="<td colspan='3'>";

								$orderdetails_notes=array();
								/**
									notes
								**/
								$orderdetails_notes['textarea_notes']="<textarea id='".WPPIZZA_SLUG."-".$this->section_key."-notes-".$uoKey."' class='".WPPIZZA_SLUG."-".$this->section_key."-notes' placeholder='".__('notes:', 'wppizza-admin')."'>".$order['notes']."</textarea>";
								$orderdetails_notes['textarea_notes_ok']="<a href='javascript:void(0);'  id='".WPPIZZA_SLUG."-".$this->section_key."-do-notes-".$uoKey."' class='".WPPIZZA_SLUG."-".$this->section_key."-do-notes button'>".__('ok', 'wppizza-admin')."</a>";
								/**
									allow filtering
								**/
								$orderdetails_notes= apply_filters('wppizza_filter_orderhistory_order_notes', $orderdetails_notes, $order);
								$order_markup['notes']=implode('',$orderdetails_notes);


								/******************************
								*
								*	thickbox div
								*
								*******************************/
								$order_markup['order_thickbox']=$orderthickbox;

							$order_markup['_notes_td']="</td>";
						$order_markup['_notes_tr'] = "</tr>";

						/****************************************************************************
						*
						*	[implode for output]
						*
						****************************************************************************/
						$markup['order_'.$uoKey] = implode('',$order_markup);
					}}

				/**********************************
				*
				*	end order
				*
				**********************************/

				$markup['_tbody']="</tbody>";


			$markup['_orders_table']='</table>';

			/**
				pagination bottom
			**/
			$markup['pagination_bottom'] = $this->pagination_markup($pagination_args, 'bottom');


		/****************************************
		*
		*	allow filtering of entire markup
		*
		****************************************/
		$markup = apply_filters('wppizza_filter_orderhistory_markup', $markup, $has_orders);
		$markup = implode('',$markup);

		/*
			return object containing markup
			and raw parameters
		*/
		$results = array();
		$results['markup'] = $markup;
		$results['values'] = $orders;

	return json_encode($results);
	}


	/******************************************************************************************************************************
	*
	*
	*	[HELPERS]
	*	@since 3.5
	*
	*
	******************************************************************************************************************************/
	/***************************************************************
	*
	*	[heeader / footer ]
	*	@since 3.5
	*	@param array()
	*	@param str
	*	@return str
	*
	***************************************************************/
	function thead_tfoot_markup($orders, $order_status, $element = 'thead'){

		/* thead or tfoot element wrapper*/
		$markup[$element.'_'] = "<".$element.">";

			/* tr */
			$markup['tr_'] = "<tr>";

				/*
					first header column
				*/
				$markup['th_1_'] = "<th scope='col' class='manage-column ".WPPIZZA_PREFIX."-".$this->section_key."-column-left'>";
					$markup['th_1_order'] = __('Summary','wppizza-admin');
				$markup['_th_1'] = "</th>";


				/*
					second header column
				*/
				$markup['th_2_'] = "<th scope='col' class='manage-column ".WPPIZZA_PREFIX."-".$this->section_key."-column-order'>";
					if($order_status!='FAILED'){
						$markup['th_2_customer_details'] = __('Order Details','wppizza-admin');
					}else{
						$markup['th_2_tx_error_details'] = __('Error Details','wppizza-admin');
					}
				$markup['_th_2'] = "</th>";


				/*
					third header column
				*/

				$markup['th_3_'] = "<th scope='col' class='manage-column ".WPPIZZA_PREFIX."-".$this->section_key."-column-right'>";
					$markup['th_3_value'] = "".__('Value','wppizza-admin')." ".wppizza_format_price($orders['value_orders_on_page'])."";
				$markup['_th_3'] = "</th>";


			/* tr end */
			$markup['_tr'] = "</tr>";



		/* thead or tfoot element wrapper end*/
		$markup['_'.$element] = "</".$element.">";


		/****************
			allow filtering of header footer elements
			before imploding
		****************/
		$markup = apply_filters('wppizza_filter_orderhistory_header_footer', $markup, $orders, $order_status, $element);
		$markup =implode('',$markup);


	/* return string */
	return $markup;
	}
	/***************************************************************
	*
	*	[pagination]
	*	@since 3.5
	*	@param array()
	*	@param str
	*	@return str
	*
	***************************************************************/
	function pagination_markup($args , $ident =''){
		static $param = null;

		/* only run once */
		if($param === null){
			$param = WPPIZZA()->admin_helper->admin_pagination($args['total_orders'], $args['limit'], $args['parameters']);
		}
		/*
			construct output to implode
		*/
		$pagination = array();

		/* wrapper div */
		$pagination['div_']='<div class="widefat '.WPPIZZA_PREFIX.'-pagination '.WPPIZZA_PREFIX.'-pagination-'.$ident.'">';

			/* counts - left */
			$pagination['span_left'] = '<span class="'.WPPIZZA_PREFIX.'-pagination-left">'.$param['on_page'].' '.__('of','wppizza-admin').' '.$param['total_count'].'</span>';

			/* pages - right */
			$pagination['span_right'] = '<span class="'.WPPIZZA_PREFIX.'-pagination-right">'.$param['pages'] .'</span>';

		/* wrapper div end */
		$pagination['_div']='</div>';


		/****************
			allow filtering of pagination_info
			before imploding
		****************/
		$pagination = apply_filters('wppizza_filter_orderhistory_pagination_info', $pagination, $param);
		$pagination =implode('',$pagination);

	/* return string */
	return $pagination;
	}


}
/***************************************************************
*
*	[ini]
*
***************************************************************/
$WPPIZZA_MODULE_ORDERHISTORY_ORDERHISTORY = new WPPIZZA_MODULE_ORDERHISTORY_ORDERHISTORY();
?>