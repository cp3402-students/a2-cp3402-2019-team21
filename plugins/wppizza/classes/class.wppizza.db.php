<?php
/**
* WPPIZZA_DB Class
*
* @package     WPPIZZA
* @subpackage  WPPIZZA_DB
* @copyright   Copyright (c) 2015, Oliver Bach
* @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
* @since       3.0
*
*/
if ( ! defined( 'ABSPATH' ) ) exit;/*Exit if accessed directly*/


/************************************************************************************************************************
*
*
*	WPPIZZA_DB
*
*
************************************************************************************************************************/
class WPPIZZA_DB{


	function __construct() {
		/* on orderpage, initialize/save session data to db - sanitized */
		add_action('wp', array( $this, 'order_initialize'));
	}


	/**********************************************
		get the most recent completed order of current blog
		used for templates preview
	**********************************************/
	function get_last_completed_blog_order_id($user_id = null){

		global $wpdb;
		/*
			get last completed order of this blog
		*/
		$last_order_id = $wpdb->get_row("SELECT MAX(id) as id FROM ".$wpdb->prefix . WPPIZZA_TABLE_ORDERS." WHERE payment_status='COMPLETED' ", ARRAY_A);

		/****************************************
			get the order
		****************************************/
		$args = array(
			'query' => array(
				'order_id' => $last_order_id['id'] ,
				'payment_status' => 'COMPLETED',
			),
			/* add in class idents here as we'll need them for email templates */
			'format' => array(
				'blog_options' => array('localization', 'blog_info', 'date_format'),// add some additional - perhaps useful - info to pass on
				'sections' => true,//leave order sections in its distinct [section] array to use in templates generation
			),
		);
		/*************************************************
			run query, and get results
			even single order results are always arrays
			so simply use reset here
		*************************************************/
		$order = WPPIZZA() -> db -> get_orders($args, 'last_completed_order_id');
		$order = reset($order['orders']);


	return $order;
	}

	/**************************************************************************************************

		update order

	**************************************************************************************************/
	function update_order($blog_id, $order_id, $hash, $update_values, $where_payment_status = false) {
		global $wpdb;


		/**
			if there's neither a hash nor an order id
			bail early
		**/
		if(empty($order_id) && empty($hash)){
			return false;
		}

		/**
			order table
		**/
		$order_table = $this->order_table($blog_id);

		/**
			where | orderid/hash | where_format
		**/
		$where = array();
		$where_format = array();
		if($order_id){
			$where['id'] = $order_id;
			$where_format[] = '%d';
		}
		if($hash){
			$where['hash'] = $hash;
			$where_format[] = '%s';
		}

		/**
			where | payment_status | where_format
		**/
		if(!empty($where_payment_status)){
			$where['payment_status'] = ''.$where_payment_status.'';
			$where_format[] = '%s';
		}

		/*
			set update vars
		*/
		$order = array();
		$order['data'] = array();
		$order['type'] = array();
		foreach($update_values as $key=>$val){
			$order['data'][$key] = $val['data'] ;
			$order['type'][] = $val['type'];
		}

		/*
			update order
		*/
		$update_order = $wpdb->update( $order_table , $order['data'], $where , $order['type'], $where_format);
		/*
			return bool db updated or not
		*/
		$update_order = !empty($update_order) ? true : false;



	return $update_order;
	}


	/***************************************************************************************************
		get right table depending on blog id
	***************************************************************************************************/
	function order_table($set_blog_id = false){
		global $wpdb, $blog_id;
		$order_table = $wpdb->prefix;
		if($set_blog_id && $set_blog_id != $blog_id && $set_blog_id>1){
			$order_table .= $set_blog_id.'_';
		}
		$order_table .= WPPIZZA_TABLE_ORDERS;

	return $order_table;
	}

	/***************************************************************************************************
		get right meta table depending on blog id
	***************************************************************************************************/
	function meta_table($set_blog_id = false){
		global $wpdb, $blog_id;
		$meta_table = $wpdb->prefix;
		if($set_blog_id && $set_blog_id != $blog_id && $set_blog_id>1){
			$meta_table .= $set_blog_id.'_';
		}
		$meta_table .= WPPIZZA_TABLE_ORDERS_META;

	return $meta_table;
	}



	/**************************************************************************************************
	*
	*
	*	cancel order
	*
	*
	**************************************************************************************************/
	function cancel_order($blog_id, $order_id, $hash, $limit_days = true) {
		global $wpdb;

		/**
			order table
		**/
		$order_table = $this->order_table($blog_id);


		$where = array();
		$where_format = array();
		/**
			where | orderid/hash | where_format
		**/
		if(!empty($order_id)){
			$where['id'] = array('clause'=> '=' , 'value' => (int)$order_id);
		}
		if(!empty($hash)){
			$hash = (string)sanitize_key($hash);
			$where['hash'] = array('clause'=> '=' , 'value' => "'".$hash."'");
		}

		/**
			where | payment_status
			return true for already/previously cancelled orders too
		**/
			$where['payment_status'] = array('clause'=> 'IN' , 'value' => "('INITIALIZED', 'CANCELLED', 'INPROGRESS')");

		/**
			restrict to last 7 days
			unless specifically bypassed
		**/
		if($limit_days){
			$where['order_date'] = array('clause'=> '>' , 'value' => ' TIMESTAMPADD(WEEK,-1,NOW()) ');
		}


		/**
			columns to update
		**/
		$update_values = array();
		/** amend order update */
		$update_values['order_update'] 		= array('data' =>date('Y-m-d H:i:s', WPPIZZA_WP_TIME));
		/* set status, cancelled */
		$update_values['payment_status'] 	= array('data' => 'CANCELLED');


		/*
			set update vars for query
		*/
		$data= array();
		foreach($update_values as $key=>$val){
			$data[$key] = "".$key." = '".$val['data']."'";
		}
		$data = implode(', ', $data);

		/*
			set where clause for query
		*/
		$where_clause= array();
		foreach($where as $key=>$val){
			$where_clause[$key] = "" . $key . ' ' . $val['clause'] . ' ' . $val['value'] . "";
		}
		$where_clause = implode(' AND ', $where_clause);


		/*
			run query
			we cannot use $wpdb->update with IN or > in where clause
		*/
		$sql = 'UPDATE '.$order_table.' SET '.$data.' WHERE '.$where_clause.'';
		$update_order = $wpdb->query($sql);
		$update_order = empty($update_order) ? false : true ;/* because we can */


	return $update_order;
	}

	/**************************************************************************************************
	*
	*
	*	delete order
	*
	*
	**************************************************************************************************/
	function delete_order($delete_id, $blog_id = false) {
		global $wpdb;

		$order_table = $this->order_table($blog_id);
		$wpdb->delete( $order_table, array( 'id' => $delete_id ), array( '%d' ) );

		$meta_table = $this->meta_table($blog_id);
		$wpdb->delete( $meta_table, array( 'order_id' => $delete_id ), array( '%d' ) );

	}

	/**************************************************************************************************
	*
	*
	*	initialize order
	* 	insert session into db when coming to orderpage
	*	updates too if adding tips for example
	*
	**************************************************************************************************/
	/*
		insert session into db when coming to orderpage
	*/

	function order_initialize() {
		global $wpdb, $blog_id, $post;
		/* for the time being, set this to false */
		$is_ajax = ( defined('DOING_AJAX') && DOING_AJAX ) ? true : false;

		/**
			check if an orderpage widget is on page ,
			to override check for is_orderpage
			initialize as false
		**/
		$has_orderpage_widget = wppizza_has_orderpage_widget();

		/*
			check is_orderpage
			if it is not an ajax request (to do perhaps, at the moment its always false )
			and do not insert or update
			if we cannot checkout yet anyway

		*/
		if(!$is_ajax && !wppizza_is_orderpage() && !$has_orderpage_widget){
			return;
		}

		/*
			get userdata session to either update or insert order
		*/
		$user_session = WPPIZZA()->session->get_userdata();

		/*
			get mapped order data, generating new hash
		*/
		$order_session = $this->map_order($user_session);


		/*
			which table (blog) should we be using ?
		*/
		$order_table = $this->order_table($blog_id);


		/* check if theres an initialized order already with that hash and update instead of insert new*/
		$update_id = false;
		$insert_id = false;
		if(!empty($user_session[''.WPPIZZA_SLUG.'_hash'])){
			$get_order = $wpdb->get_row( "SELECT id FROM ".$order_table." WHERE hash = '".$user_session[''.WPPIZZA_SLUG.'_hash']."' AND payment_status = 'INITIALIZED' ", ARRAY_A);
			/* update if theres an order in session */
			if (!empty($order_session) && null !== $get_order ) {
				$update_id = $get_order['id'];
			}
		}

		/*
			update db
		*/
		if($update_id){
			$is_update = $wpdb->update( $order_table , $order_session['data'], array( 'id' => $update_id ), $order_session['type'], array( '%d' ));
			/* something failed, let's do a new one to be safe*/
			if(false === $is_update){
				$update_id = false;
			}
		}

		/*
			insert new into db if not update or delete and there's an order in session
		*/
		if(!$update_id){//&& !$delete_id && !empty($order_session)

			/*
				add hash to db and session when inserting new
			*/
			$wppizza_hash = wppizza_mkHash($order_session);
			$order_session['data']['hash'] = $wppizza_hash;
			$order_session['type'][] = '%s';

			WPPIZZA()->session->set_order_hash($wppizza_hash);
			$user_session = WPPIZZA()->session->get_userdata();
			$order_session['data']['customer_ini'] = maybe_serialize($user_session);
			$order_session['type'][] = '%s';

			$wpdb->insert( $order_table , $order_session['data'], $order_session['type']);
			$insert_id = $wpdb->insert_id;

			/*
				add order id to user session when inserting new
				needed for gateways that use overlay and need to pass on the order id
			*/
			WPPIZZA()->session->set_order_id($insert_id);
			$order_id = $insert_id;
		}

		/*
			add meta data - if we want - as soon as an order gets initialized in the db
			even before it ever gets submitted for payment
		*/
		$meta_order_id = !$update_id ? $order_id : $update_id ;
		do_action('wppizza_add_order_meta_init', $meta_order_id);


	return;
	}

	/**************************************************************************************************************************************************************************************
	*
	*
	*
	*	admin get customers by search or id
	*
	*
	*
	**************************************************************************************************************************************************************************************/
	function get_customers($selected_user_id = false, $set_limit = 10 ){
		global $wppizza_options, $wpdb, $blog_id;

		/*ini return array*/
		$customers=array();
		$customers['customers_on_page']=array();
		$customers['total_number_of_customers']=0;
		$customers['results_set']=array();

		/*for consistancy add it here*/
		$sql_payment_status = "'".implode("','",explode(",",WPPIZZA_PAYMENT_STATUS_SUCCESS))."'";

		/*pagination and sliceoffset if set*/
		$paged=0;
		if(!empty($_GET['paged']) && is_numeric($_GET['paged'])){
			$paged=(int)$_GET['paged']-1;
		}

		/**
			search for customers by $_GET['s'] or $_GET['uid']
		**/
		/* if we have a distinctly set user id, set _GET['s'] variable accordingly */
		if(!empty($selected_user_id)){
			$_GET['s'] = (int)$selected_user_id;
		}
		if(!empty($_GET['s']) || !empty($_GET['uid'])){
			$is_customer_search = true;
		}

		/**
			[restrict by user id > 0]
			will be replaced with wp_user_id IN(1,2,3)
			if search applies
		**/
		$user_id_restrict=' > 0 ';

		/*************************************
			getting orders from all subsites
			only multisite->parent site and only if enabled
		*************************************/
		$multisite_all_orders = apply_filters('wppizza_filter_order_history_all_sites',false);

		/*********************************************************************************************

			getting order tables to query

		********************************************************************************************/
			$blog_tables = array();
			$k=0;
			/* all blogs */
			if ($multisite_all_orders){
	 	   		/*get all and loop through blogs*/
	 	   		$blogs = $wpdb->get_results("SELECT blog_id FROM ".$wpdb->blogs."", ARRAY_A);
				if ($blogs) {
		        	foreach($blogs as $blog) {
		        		switch_to_blog($blog['blog_id']);
		        			/*make sure plugin is active*/
		        			if(is_plugin_active(WPPIZZA_PLUGIN_INDEX)){
								$blog_tables[$blog['blog_id']] = $wpdb->prefix . WPPIZZA_TABLE_ORDERS;
		        			$k++;
		        			}
						restore_current_blog();
		        	}
				}
			}
			/* curent blog only */
			if (!$multisite_all_orders){
				$blog_tables[$blog_id] = $wpdb->prefix . WPPIZZA_TABLE_ORDERS;
			}

		/*****************************************************************************
		*
		*	[doing search of some sort, get possible user id's]
		*
		******************************************************************************/
		if(!empty($is_customer_search)){

			/**
				search for customers by $_GET['s']
			**/
			if(!empty($_GET['s'])){

				$search=esc_sql(wppizza_validate_string($_GET['s']));
				/*searching for id*/
				$id_search = (int)$search ;
				$search_int=(is_numeric($search) && !empty($id_search) ) ? $id_search : false;


				/*searching display_name, user_nicename, user email*/
				$search_columns=array();
				if(!$search_int){
					$search_columns[]= "".$wpdb->base_prefix ."users.display_name LIKE '%".$search."%' ";
					$search_columns[]= "".$wpdb->base_prefix ."users.user_nicename LIKE '%".$search."%' ";
					$search_columns[]= "".$wpdb->base_prefix ."users.user_email LIKE '%".$search."%' ";
				}
				/*if numeric, just search id*/
				if($search_int){
					$search_columns[]= "".$wpdb->base_prefix ."users.ID = ".$search_int." ";
				}

				/**construct the query*/
				$customers_query="";
				$customers_query.= " SELECT " . $wpdb->base_prefix . "users.ID" . PHP_EOL;
				$customers_query.= " FROM " . $wpdb->base_prefix . "users " . PHP_EOL;
				$customers_query.= " WHERE ".PHP_EOL." " . implode(''.PHP_EOL.' OR '.PHP_EOL.'', $search_columns) . " " . PHP_EOL;
			}

			/*get results*/
			$customers_search_results = $wpdb->get_results($customers_query);
			/*
				no search results , bail right now
			*/
			if(empty($customers_search_results)){
				return ;
			}


			$customer_search_found_ids=array();
			if(is_array($customers_search_results)){
			foreach($customers_search_results as $vars){
				$customer_search_found_ids[$vars->ID]=$vars->ID;
			}}
			/*overwrite user id search */
			$user_id_restrict =' IN ('.implode(',',$customer_search_found_ids).') ';
		}

		/*****************************************************************************
		*
		*
		*	[get all customer id's from all blogtables with user_registered date etc]
		*	[sort by user_registered DESC]
		*
		*	[only if not search]
		******************************************************************************/


				$colums=array();
				$colums['user_order_by']	= $wpdb->base_prefix.'users.user_registered as user_order_by';
				$colums['user_registered']	= $wpdb->base_prefix.'users.user_registered';
				$colums['user_nicename']	= $wpdb->base_prefix.'users.user_nicename';
				$colums['user_email']		= $wpdb->base_prefix.'users.user_email';
				$colums['display_name']		= $wpdb->base_prefix.'users.display_name';
				$customers_query='';

				/*****************************************************************************
				*
				*	[list all customrs with completed orders]
				*
				******************************************************************************/
				$c=0;
				foreach($blog_tables as $k=>$blog_table){
					//if($c>0){$customers_query.=PHP_EOL.'UNION ALL'.PHP_EOL;}/*union if more than one table */
					if($c>0){$customers_query.=PHP_EOL.'UNION'.PHP_EOL;}/*union if more than one table */
					$customers_query.= ' SELECT ' . implode(',',$colums) . ', ' . $blog_table . '.wp_user_id' . PHP_EOL;
					$customers_query.= ' FROM ' . $blog_table . ' ' . PHP_EOL;
					$customers_query.= ' LEFT JOIN ' . $wpdb->base_prefix . 'users ON ' . $blog_table . '.wp_user_id = ' . $wpdb->base_prefix . 'users.ID ' . PHP_EOL;
					$customers_query.= ' WHERE ' . $blog_table . '.wp_user_id '.$user_id_restrict.' AND payment_status IN ('.$sql_payment_status.') ' . PHP_EOL;
					$customers_query.= ' GROUP BY ' . $blog_table . '.wp_user_id ' . PHP_EOL;
					//$customers_query.= ' LIMIT '.($paged*$set_limit).','.$set_limit.' ' . PHP_EOL;//dont limit as we want to get all*/
				$c++;
				}


				/* add order by clause to end */
				$customers_query.= ' ORDER BY user_order_by DESC ' . PHP_EOL;

				/*get results*/
				$customers_results = $wpdb->get_results($customers_query, ARRAY_A);

				/**sort by user_registered desc**/
				if($customers_results){
					arsort($customers_results);
				}

			/**********************
			*
			*
			*	[total no of customers according to query ]
			*	for pagination
			*
			***********************/
			$customers['total_number_of_customers'] = is_array($customers_results) ? count($customers_results) : 0;


			/**********************
			*
			*
			*	[slice for customers we need to display on page]
			*	[offset and limited
			*
			***********************/
			$slice_offset= $paged * $set_limit;
			$customers_results = array_slice($customers_results, $slice_offset, $set_limit);


			/**********************
			*
			*
			*	[all user id's of customers displayed on page]
			*	[offset and limited
			*
			***********************/
			$ids_on_page_array = wppizza_array_column($customers_results, 'wp_user_id');
			$ids_on_page=implode(',',$ids_on_page_array);

			/**********************
			*
			*
			*	[get values from all tables for each customer displayed on page]
			*
			*
			***********************/
			$customer_values_query='';
			if(count($blog_tables) >1 ){
				$customer_values_query=PHP_EOL."SELECT wp_user_id as wp_user_id, SUM(table_count) as table_count , SUM(table_total_value) as table_total_value, SUM(table_total_items) as table_total_items  " . PHP_EOL;
				$customer_values_query.=" FROM (" . PHP_EOL;
			}
			$c = 0;
			foreach($blog_tables as $k=>$blog_table){
				if($c > 0){
					$customer_values_query.=" UNION ALL " . PHP_EOL;
				}
				$customer_values_query.=" SELECT wp_user_id, COUNT(*) as table_count,  SUM(order_total) as table_total_value, SUM(order_no_of_items) as table_total_items " . PHP_EOL;
				$customer_values_query.=" FROM ".$blog_table."" . PHP_EOL;
				$customer_values_query.=" WHERE wp_user_id IN (".$ids_on_page.") AND payment_status IN (".$sql_payment_status.") " . PHP_EOL;
				$customer_values_query.=" GROUP BY wp_user_id " . PHP_EOL;
			$c++;
			}
			if(count($blog_tables) >1 ){
				$customer_values_query.=" ) tmp" . PHP_EOL;
				$customer_values_query.=" GROUP BY wp_user_id" . PHP_EOL;
			}

			/**get results - however, if there are no orders (and therefore no customers, just return empty array) **/
			$customer_values_results = empty($ids_on_page_array) ? array() : $wpdb->get_results($customer_values_query, OBJECT_K);

			/**add user data to results set resultset on page**/
			$customers['results_set'] = array();
			foreach($customers_results as $k=>$val){
				$uid=$val['wp_user_id'];/*for convenience*/

				$user_meta_name=array();
				$user_meta_name[]=get_user_meta($uid, 'first_name', true);
				$user_meta_name[]=get_user_meta($uid, 'last_name', true);


				$customers['results_set'][$uid]['user_registered']			=	$val['user_registered'];
				$customers['results_set'][$uid]['user_email']				=	$val['user_email'];
				$customers['results_set'][$uid]['user_name']				=	trim(implode(' ',$user_meta_name));
				$customers['results_set'][$uid]['user_display_name']		=	$val['display_name'];
				$customers['results_set'][$uid]['user_user_nicename']		=	$val['user_nicename'];
				$customers['results_set'][$uid]['user_orders_order_count']	=	$customer_values_results[$uid]->table_count;
				$customers['results_set'][$uid]['user_orders_total_value']	=	$customer_values_results[$uid]->table_total_value;
				$customers['results_set'][$uid]['user_orders_total_items']	=	$customer_values_results[$uid]->table_total_items;
				$customers['results_set'][$uid]['user_orders_avg_spent']	=	($customer_values_results[$uid]->table_total_value / $customer_values_results[$uid]->table_count);
				$customers['results_set'][$uid]['wp_user_id']				=	$uid;
			}


	return $customers;
	}

	/**************************************************************************************************
	*
	*	replacement of previous get_orders_orderhistory() that can now also be used externally
	* 	via wrapper function to query completed orders
	*	(including failed, unconfirmed, or orders that have subsequently been rejected or refunded)
	*
	*	@ since 3.5
	*	@param array
	*	@param str calling function
	*	@return array
	**************************************************************************************************/
	function get_orders($args = false, $caller = false){

		global $wpdb, $blog_id, $wppizza_options;
		$force_all_rows = $args === null ? true : false; //force getting all rows
		$no_arguments_passed = $args === false ? true : false; //force default pagination limits


		/*********************************************
		*
		*
		*	sanitise arguments
		*
		*
		*********************************************/
		/*\/*\/*
		#	$args['query']['wp_user_id']
		#	@param int
		*\/*\/*/
		$args['query']['wp_user_id'] = (isset($args['query']['wp_user_id']) && is_numeric($args['query']['wp_user_id'])) ? abs((int)$args['query']['wp_user_id']) : false;

		/*\/*\/*
		#	$args['query']['email']
		#	@param str|array
		*\/*\/*/
		/* cast to array first of all */
		$sanitized_email = !empty($args['query']['email']) ? ( (!is_array($args['query']['email'])) ? array($args['query']['email']) : $args['query']['email'] ) : array();
		$sanitized_email = array_map('sanitize_text_field', $sanitized_email);// sanitize
		$args['query']['email'] = !empty($sanitized_email) ? $sanitized_email : false;


			//$args['query']['custom_status'] = !empty($sanitized_custom_status) ? $sanitized_custom_status : false;
		//$args['query']['email'] = !empty($args['query']['email']) ? ( strtoupper($args['query']['email']) === 'NULL' ? 'NULL' : sanitize_text_field($args['query']['email']) ) : false;

		/*\/*\/*
		#	$args['query']['order_id']
		#	@param int
		#	query for specific order id.
		#	in confunction with $args['query']['blogs'], this could also return (an) order(s) from (a) different blog(s). otherwise will by default query current blog only
		*\/*\/*/
		$args['query']['order_id'] = (isset($args['query']['order_id']) && is_numeric($args['query']['order_id'])) ? abs((int)$args['query']['order_id']) : false;

		/*\/*\/*
		#	$args['query']['order_id_lt'] | $args['query']['order_id_lte']
		#	@param int
		#	query for order id lower than / lower than equal to [int].
		#	in confunction with $args['query']['blogs'], this could also return (an) order(s) from (a) different blog(s). otherwise will by default query current blog only
		#	if used, will unset any order_id query above
		*\/*\/*/
		$args['query']['order_id_lt'] = (isset($args['query']['order_id_lt']) && is_numeric($args['query']['order_id_lt'])) ? abs((int)$args['query']['order_id_lt']) : false;
		$args['query']['order_id_lte'] = (isset($args['query']['order_id_lte']) && is_numeric($args['query']['order_id_lte'])) ? abs((int)$args['query']['order_id_lte']) : false;

		/*\/*\/*
		#	$args['query']['order_id_gt'] | $args['query']['order_id_gte']
		#	@param int
		#	query for order id greater than / greater than equal to [int].
		#	in confunction with $args['query']['blogs'], this could also return (an) order(s) from (a) different blog(s). otherwise will by default query current blog only
		#	if used, will unset any order_id query above
		*\/*\/*/
		$args['query']['order_id_gt'] = (isset($args['query']['order_id_gt']) && is_numeric($args['query']['order_id_gt'])) ? abs((int)$args['query']['order_id_gt']) : false;
		$args['query']['order_id_gte'] = (isset($args['query']['order_id_gte']) && is_numeric($args['query']['order_id_gte'])) ? abs((int)$args['query']['order_id_gte']) : false;

		/*\/*\/*
		#	$args['query']['order_id_in']
		#	@param array
		#	query for order id in array
		#	in confunction with $args['query']['blogs'], this could also return (an) order(s) from (a) different blog(s). otherwise will by default query current blog only
		#	if used, will unset any order_id query above
		*\/*\/*/
		$args['query']['order_id_in'] = (isset($args['query']['order_id_in']) && is_array($args['query']['order_id_in'])) ? implode(',',array_unique(array_map('intval',$args['query']['order_id_in']))) : false;


		/*\/*\/*
		#	$args['query']['hash']
		#	@param int
		#	query for specific hash.
		#	in confunction with $args['query']['blogs'], this could also return (an) order(s) from (a) different blog(s). otherwise will by default query current blog only
		*\/*\/*/
		$args['query']['hash'] = !empty($args['query']['hash']) ? (string)sanitize_key($args['query']['hash']) : false;

		/*\/*\/*
		#	$args['query']['order_date_after']
		#	@param timestamp
		#	query for an order date that is after a set timestamp.
		#
		*\/*\/*/
		$args['query']['order_date_after'] = !empty($args['query']['order_date_after']) && (int)$args['query']['order_date_after'] >0 ? (int)$args['query']['order_date_after'] : false;


		/*\/*\/*
		#	$args['query']['order_date_before']
		#	@param timestamp
		#	query for an order date that is before a set timestamp.
		#
		*\/*\/*/
		$args['query']['order_date_before'] = !empty($args['query']['order_date_before']) && (int)$args['query']['order_date_before'] >0 ? (int)$args['query']['order_date_before'] : false;


		/*\/*\/*
		#	$args['query']['payment_status']
		#	@param str | array | 'NULL'
		#	Note: if set to 'NULL' (string) payments_status query will be forefully removed
		*\/*\/*/
		$default_payment_status=explode(',',WPPIZZA_PAYMENT_STATUS_SUCCESS);//COMPLETED
		$default_payment_status[]='UNCONFIRMED';
		$default_payment_status[]='REFUNDED';
		$default_payment_status[]='REJECTED';
		/*
			restrict to these (for now) as otherwsie there would be a ton of php notices
			as many things will not be available yet for any of the other statusses
		*/
		$allowed_payment_status=explode(',',WPPIZZA_PAYMENT_STATUS_SUCCESS);//COMPLETED
		$allowed_payment_status[]='UNCONFIRMED';
		$allowed_payment_status[]='CONFIRMED';
		$allowed_payment_status[]='CAPTURED';
		$allowed_payment_status[]='REFUNDED';
		$allowed_payment_status[]='REJECTED';
		$allowed_payment_status[]='FAILED';
		$allowed_payment_status[]='INPROGRESS';
		$allowed_payment_status[]='INITIALIZED';
		$allowed_payment_status[]='AUTHORIZED';
		$allowed_payment_status[]='CANCELLED';
		$allowed_payment_status[]='PAYMENT_PENDING';

		/*
			cast to array if it is not
			sanitize and intersect with allowed status
		*/
		$sanitized_payment_status = !empty($args['query']['payment_status']) ? ( (!is_array($args['query']['payment_status'])) ? array($args['query']['payment_status']) : $args['query']['payment_status'] ) : array();
		$sanitized_payment_status = array_map('strtoupper',array_map('wppizza_validate_alpha_only', $sanitized_payment_status));// make it case insensitive and sanitize
		$sanitized_payment_status = array_values(array_intersect($allowed_payment_status, $sanitized_payment_status));//intersect and reindex
		$args['query']['payment_status'] = (isset($args['query']['payment_status']) && is_string($args['query']['payment_status']) && strtoupper($args['query']['payment_status']) === 'NULL') ? NULL : ((!empty($args['query']['payment_status']) ? $sanitized_payment_status : $default_payment_status));

		/*\/*\/*
		#	$args['query']['order_status']
		#	@param string
		*\/*\/*/
		/*
			all available db ENUM values
		*/
		$available_order_status = array('NEW','ACKNOWLEDGED','ON_HOLD','PROCESSED','DELIVERED','REJECTED','REFUNDED','OTHER','CUSTOM_1','CUSTOM_2','CUSTOM_3','CUSTOM_4');
		/* cast to array if it is not */
		$sanitized_order_status = !empty($args['query']['order_status']) ? ( (!is_array($args['query']['order_status'])) ? array($args['query']['order_status']) : $args['query']['order_status'] ) : array();
		$sanitized_order_status = array_map('strtoupper',array_map('wppizza_validate_alpha_only', $sanitized_order_status));// make it case insensitive and sanitize
		$sanitized_order_status = array_values(array_intersect($available_order_status, $sanitized_order_status));//intersect and reindex

		$args['query']['order_status'] = !empty($sanitized_order_status) ? $sanitized_order_status : false;



		/*\/*\/*
		#	$args['query']['custom_status']
		#	@param str
		# 	default  ''
		*\/*\/*/
		if(!empty($args['query']['custom_status'])){
			/* cast to array if it is not */
			$sanitized_custom_status = !empty($args['query']['custom_status']) ? ( (!is_array($args['query']['custom_status'])) ? array($args['query']['custom_status']) : $args['query']['custom_status'] ) : array();
			$sanitized_custom_status = array_map('esc_html',array_map('esc_sql', $sanitized_custom_status));// make it case insensitive and sanitize

			$args['query']['custom_status'] = !empty($sanitized_custom_status) ? $sanitized_custom_status : false;
		}

		/*\/*\/*
		#	getting orders from all subsites
		#	set multisite , overriding default filter (only multisite->parent site and only if enabled in settings)
		#
		#	$args['query']['blogs']
		#	@param bool|array
		*\/*\/*/
		/* filtered as per wppizza->settings*/
		$_multisite_orders = apply_filters('wppizza_filter_order_history_all_sites', false);

		if(is_multisite() && isset($args['query']['blogs'])){
			if($args['query']['blogs'] === false){
				$_multisite_orders = false;
			}
			if($args['query']['blogs'] === true){
				$_multisite_orders = true;
			}
			if(is_array($args['query']['blogs']) && !empty($args['query']['blogs'])){
				$_multisite_orders = true;
				$_multisite_blogs = array_flip(array_filter(array_map( 'abs', $args['query']['blogs'] )));//make sure to only have int >=1 and flip id as key for uniqueness and faster index check
			}
			if(is_string($args['query']['blogs']) && is_numeric($args['query']['blogs']) && !empty($args['query']['blogs'])){
				$_multisite_orders = true;
				$selected_blog = (int)$args['query']['blogs'];
				$_multisite_blogs[$selected_blog] = $selected_blog ;//set blog id as key for uniqueness and faster index check
			}
		}

		/*\/*\/*
		#	only getting the count results for the query
		#	ignores $args['pagination'] | $args['format'] | $args['blog_options']
		#
		#	$args['query']['summary']
		#	@param bool
		*\/*\/*/
		$args['query']['summary'] = !empty($args['query']['summary']) ? true : false;


		/*\/*\/*
		#	allow setting of additional arbitrary where clause parameters
		#
		#	$args['query']['custom_parameters']
		#	@param str
		*\/*\/*/
		$args['query']['custom_query'] = (!empty($args['query']['custom_query']) && is_string($args['query']['custom_query']) ) ? $args['query']['custom_query'] : false ;


		/*\/*\/*
		#	meta queries if defined
		#
		#	$args['query']['meta']
		#	@param array
		*\/*\/*/
		/* ini empty/default strings/values */
		$meta['table_alias'] = '';
		$meta['select'] = '';
		$meta['closure_'] = '';
		$meta['_closure'] = '';
		$meta['join'] = '';
		$meta['group_by'] = '';
		$meta['data'] = false;
		if(!empty($args['meta']['query']) && is_array($args['meta']['query'])){

			/*************
				set flag to know it's a meta table query
			*************/
			$meta['has_query'] = true;

			/*************
				set main table alias
			*************/
			$meta['table_alias'] = 'table_orders.';
			$meta['table_alias_meta'] = 'table_orders_meta';

			/*************
				get concat meta selects too if we want them added to the output data returned
			*************/
			$meta['data'] = !empty($args['meta']['data']) ? true : false ;

			if($meta['data']){
				$meta_select = array();
				$meta_select[] = "GROUP_CONCAT(".$meta['table_alias_meta'].".meta_id SEPARATOR '|') AS meta_id ";
				$meta_select[] = "GROUP_CONCAT(".$meta['table_alias_meta'].".meta_key SEPARATOR '|') AS meta_key ";
				$meta_select[] = "GROUP_CONCAT(".$meta['table_alias_meta'].".meta_value SEPARATOR '|') AS meta_value ";
				/* construct  meta selects for query */
				$meta['select'] = ', '.implode(', ', $meta_select );
			}


			/*************
				prepare the meta query before imploding
			*************/
			/*
				prepare the meta query
			*/
			$prepare_meta_query = array();
			foreach($args['meta']['query'] as $k=>$a){

				// make sure column is 'meta_id', 'order_id', 'meta_key' or 'meta_value'
				$column = ( !empty($a['column']) && in_array( strtolower($a['column']), array('meta_id', 'order_id', 'meta_key', 'meta_value') ) ) ? strtolower($a['column']) : 'meta_key';

				// only allow "!=" and  "=" (for the time being) as comparison. defaults to "=" (Maybe one day allow LIKE, IN etc too if someone asks for it )
				$operator = ( !empty($a['compare']) && in_array( $a['compare'], array('=', '!=') ) ) ? $a['compare'] : '=';

				// always force meta_value to be lowercase if querying 'meta_key' as "do_order_meta", "get_order_meta" etc always query and insert lowercase data
				$value = ($a['column'] == 'meta_key') ? strtolower($a['value']) : $a['value'];

				// prepare meta query
				$prepare_meta_query[] = $wpdb->prepare("".$column." ".$operator." %s", $value);

			}
			/*
				set relation between multiple meta queries - AND/OR -> defaults to AND
			*/
			$meta_allowed_relations = array('AND', 'OR');
			$meta_query_relation = !empty($args['meta']['relation']) && in_array(strtoupper($args['meta']['relation']),$meta_allowed_relations) ? strtoupper($args['meta']['relation']) : 'AND' ;

			/*************
				main orders table FROM enclose start/end
			*************/
			$meta['closure_'] = '(( SELECT * FROM ';
			$meta['_closure'] = ') AS '.substr($meta['table_alias'],0,-1).' ';//remove dot after alias

			/*************
				construct (implode) the meta query to run on the meta table, set on cluase etc
			*************/
			$meta['join'] = 'INNER JOIN ' . PHP_EOL;
			$meta['join'] .= '( SELECT * FROM '. $wpdb->prefix . WPPIZZA_TABLE_ORDERS_META.' WHERE '.implode(' '.$meta_query_relation.' ', $prepare_meta_query ). ') AS '.$meta['table_alias_meta'].' ' . PHP_EOL ;
			$meta['join'] .= 'ON '.$meta['table_alias_meta'].'.order_id = '.$meta['table_alias'].'id ';
			$meta['join'] .= ') ';

			/*************
				make sure we group by order id
			*************/
			$meta['group_by'] = 'GROUP BY id';

		}


		/*\/*\/*
		#	setting any pagination / limits
		#	$args['pagination']
		#	@param array
		*\/*\/*/
		$args['pagination']['paged'] = ( !isset($args['pagination']['paged']) || empty($args['pagination']['paged']) || !is_numeric($args['pagination']['paged']) ) ? 0 : (abs((int)$args['pagination']['paged']) - 1 );
		$args['pagination']['limit'] = ( !isset($args['pagination']['limit']) || empty($args['pagination']['limit']) || !is_numeric($args['pagination']['limit']) ) ? ( $no_arguments_passed === true ? $wppizza_options['settings']['admin_order_history_max_results'] : false ): abs((int)$args['pagination']['limit']);


		/*\/*\/*
		#	adding blogoptions to formatted orders
		#	as they may differ for each in a multiste setup dependng on blog
		#	$args['format']['blog_options'] bool|array
		#	$args['format']['sections'] bool - add order vars into their own [section] array
		#
		#	@param bool
		# 	default false
		#
		# note, dont set $args['format']['blog_options'] at all if not set otherwise $args['format'] will return true!
		*\/*\/*/
		/* including full blog options */
		$format_blog_options = false;
		if(isset($args['format']['blog_options']) && !empty($args['format']['blog_options'])){
			$args['format']['blog_options'] = $args['format']['blog_options'];
			$format_blog_options = $args['format']['blog_options'];
		}

		/* adding distinct section key site,ordervasr,customer,items and summary are put in to  */
		$format_order_sections = false;
		if(isset($args['format']['sections']) && !empty($args['format']['sections'])){
			$args['format']['sections'] = true;
			$format_order_sections = true;
		}
		/*
			adding things like username, first_name, last_name, registered email address to customer data
			should really only ever be true in backend (curently not used anywhere )
		*/
		$format_registered_userdata = false;
		if(isset($args['format']['registered_userdata']) && !empty($args['format']['registered_userdata'])){
			$args['format']['registered_userdata'] = true;
			$format_registered_userdata = true;
		}


		/*\/*\/*
		#	format output into a somewhat more easily dealt with object
		#	$args['format']
		#	@param bool|array
		# 	default true
		*\/*\/*/
		$args['format'] = (isset($args['format']) && empty($args['format'])) ? false : ( isset($args['format']) && is_array($args['format']) ? $args['format'] : true);

		/*\/*\/*
		#	set order by and sortorder
		#	$args['sort']['order_by'] CURRENTLY NOT IMPLEMENTED AS CHANGEABLE / SETTABLE .  FIXED TO 'date_sort'
		#	$args['sort']['sortorder']
		#	@param bool|array
		# 	default true
		*\/*\/*/
		//$args['sort']['order_by'] = 'date_sort'; // !empty($args['sort']['order_by']) ? some_sanitization_function('date_sort') : 'date_sort';
		$args['sort']['sortorder'] = (!empty($args['sort']['sortorder']) && strtoupper($args['sort']['sortorder']) === 'ASC' ) ? 'ASC' : 'DESC';


		/*********************************************
		*
		*
		*	prepare where clause from arguments
		*
		*
		*********************************************/
			$where_clause = array();


			/*******************
			#
			#	query by wp_user_id
			#
			*******************/
			/* prepare */
			if($args['query']['wp_user_id'] !== false){
				$where_clause['wp_user_id'] = $wpdb->prepare("wp_user_id = %d", $args['query']['wp_user_id']);
			}

			/*******************
			#
			#	query by email
			#
			*******************/
			/* prepare */
			if($args['query']['email'] !== false){
				/* single value */
				if(count($args['query']['email'])==1){
					/* only getting not set statusses */
					if($args['query']['email'][0] === 'NULL'){
						$where_clause['email'] = "email IS NULL";
					}else{
						$where_clause['email'] = $wpdb->prepare("email = %s ", $args['query']['email'][0]);
					}
				}else{
					$prepare = array();
		    		foreach($args['query']['email'] as $k => $v){
		    			// remove 'NULL' from prepare statement */
		    			if($v != 'NULL'){
		    				$prepare[] = $wpdb->prepare('%s', $v);
		    			}
		    		}

					/* [not-set] was not in array */
					if(!in_array('NULL', $args['query']['email'])){
						$where_clause['email'] = "email IN (".implode(',',$prepare).") ";
					}else{
						$where_clause['email'] = "(email IN (".implode(',',$prepare).") OR email IS NULL )";
					}
				}
			}

			/*******************
			#
			#	query by order_id - unless set to use order_id_lte or order_id_gte
			#
			*******************/
			/* prepare */
			if($args['query']['order_id'] !== false && $args['query']['order_id_lte'] === false && $args['query']['order_id_gte'] === false && $args['query']['order_id_lt'] === false && $args['query']['order_id_gt'] === false && $args['query']['order_id_in'] === false){
				$where_clause['id'] = $wpdb->prepare("id = %d", $args['query']['order_id']);
			}

			/*******************
			#
			#	query by order_id <(=)
			#	<= has precedence over <
			*******************/
			/* prepare */
			if($args['query']['order_id_lt'] !== false || $args['query']['order_id_lte'] !== false){
				if($args['query']['order_id_lte'] !== false){
					$where_clause['id'] = $wpdb->prepare("id <= %d", $args['query']['order_id_lte']);
				}else{
					$where_clause['id'] = $wpdb->prepare("id < %d", $args['query']['order_id_lt']);
				}
			}

			/*******************
			#
			#	query by order_id >=
			#	>= has precedence over >
			*******************/
			/* prepare */
			if($args['query']['order_id_gt'] !== false || $args['query']['order_id_gte'] !== false){
				if($args['query']['order_id_gte'] !== false){
					$where_clause['id'] = $wpdb->prepare("id >= %d", $args['query']['order_id_gte']);
				}else{
					$where_clause['id'] = $wpdb->prepare("id > %d", $args['query']['order_id_gt']);
				}
			}

			/*******************
			#
			#	query by order_id_in
			#	has precedence over =, > , >=, < , <=
			*******************/
			/* prepare */
			if($args['query']['order_id_in'] !== false ){
				$where_clause['id'] = $wpdb->prepare("id IN (%s)", $args['query']['order_id_in']);
			}
			/*******************
			#
			#	query by hash
			#
			*******************/
			/* prepare */
			if($args['query']['hash'] !== false){
				$where_clause['hash'] = $wpdb->prepare("hash = %s", $args['query']['hash']);
			}
			/*******************
			#
			#	query by order date later than
			#
			*******************/
			/* prepare */
			if($args['query']['order_date_after'] !== false){
				$where_clause['order_date'] = $wpdb->prepare("order_date > %s", date('Y-m-d H:i:s',$args['query']['order_date_after']));
			}

			/*******************
			#
			#	query by order date earlier than
			#
			*******************/
			/* prepare */
			if($args['query']['order_date_before'] !== false){
				$where_clause['order_date'] = $wpdb->prepare("order_date > %s", date('Y-m-d H:i:s',$args['query']['order_date_before']));
			}

			/*******************
			#
			#	query by payment_status , unless it's null
			#
			*******************/
			/* prepare */
			if($args['query']['payment_status'] !== NULL ){
				/* only one parameter passed */
				if(count($args['query']['payment_status'])==1){
					$where_clause['payment_status'] = $wpdb->prepare("payment_status = %s ", $args['query']['payment_status'][0]);
				}else{
					$prepare = array();
		    		foreach($args['query']['payment_status'] as $k => $v){
		    			$prepare[] = $wpdb->prepare('%s', $v);
		    		}
					$where_clause['payment_status'] = "payment_status IN (".implode(',',$prepare).") ";
				}
			}

			/*******************
			#
			#	query by order_status
			#
			*******************/
			/* prepare */
			if(!empty($args['query']['order_status'])){
				/* only one parameter passed */
				if(count($args['query']['order_status'])==1){
					$where_clause['order_status'] = $wpdb->prepare("order_status = %s ", $args['query']['order_status'][0]);
				}else{
					$prepare = array();
		    		foreach($args['query']['order_status'] as $k => $v){
		    			$prepare[] = $wpdb->prepare('%s', $v);
		    		}
					$where_clause['order_status'] = "order_status IN (".implode(',',$prepare).") ";
				}
			}

			/*******************
			#
			#	query by custom_status
			#
			*******************/
			/* prepare */
			if(!empty($args['query']['custom_status'])){
				/* only one parameter passed */
				if(count($args['query']['custom_status'])==1){
					/* only getting not set statusses */
					if($args['query']['custom_status'][0] == '[not-set]'){
						$where_clause['order_status_user_defined'] = "(order_status_user_defined = '' OR order_status_user_defined IS NULL)";
					}else{
						$where_clause['order_status_user_defined'] = $wpdb->prepare("order_status_user_defined = %s ", $args['query']['custom_status'][0]);
					}
				}else{
					$prepare = array();
		    		foreach($args['query']['custom_status'] as $k => $v){
		    			// remove [not-set] from prepare statement */
		    			if($v != '[not-set]'){
		    				$prepare[] = $wpdb->prepare('%s', $v);
		    			}
		    		}

					/* [not-set] was not in array */
					if(!in_array('[not-set]', $args['query']['custom_status'])){
						$where_clause['order_status_user_defined'] = "order_status_user_defined IN (".implode(',',$prepare).") ";
					}else{
						$where_clause['order_status_user_defined'] = "(order_status_user_defined IN (".implode(',',$prepare).") OR order_status_user_defined = '' OR order_status_user_defined IS NULL )";
					}
				}
			}

			/*******************
			#
			#	query by custom_parameters if set in arguments specifically
			#
			*******************/
			if(!empty($args['query']['custom_query'])){
				$where_clause['custom_query'] = $args['query']['custom_query'];
			}

		/*********************************************************************************************

			getting tables to query
			getting blogoptions at the same time to be able to add those to the respective results set

		********************************************************************************************/
		$blog_tables = array();
		$blog_info = array();
		$blog_options = array();
		$date_format = array();

		/*
			multiple blogs
		*/
		if($_multisite_orders){
 	   		/*get all and loop through blogs*/
 	   		$blogs = $wpdb->get_results("SELECT blog_id FROM ".$wpdb->blogs."", ARRAY_A);
 	   		$max_table_columns = array();
			if ($blogs) {
	        	foreach($blogs as $blog) {
	        		/* if we have specific blogs set skip others */
					if(empty($_multisite_blogs) || isset($_multisite_blogs[$blog['blog_id']])){
	        		switch_to_blog($blog['blog_id']);
	        			/* make sure function exists even if outside admin */
	        			require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
	        			/*make sure plugin is active*/
	        			if(is_plugin_active(WPPIZZA_PLUGIN_INDEX)){

							/* full orders table name */
							$blog_tables[$blog['blog_id']] = $wpdb->prefix . WPPIZZA_TABLE_ORDERS;

							/* all columns from table */
							$table_columns = $wpdb->get_col("DESC ".$wpdb->prefix . WPPIZZA_TABLE_ORDERS."", 0);
							$blog_columns[$blog['blog_id']] = array_combine($table_columns, $table_columns);
							/* add to max table columns as different tables might have more/or less columns (keys are set with array_combine, to avoid duplicates)*/
							$max_table_columns += $blog_columns[$blog['blog_id']];

							/*bloginfo from blog - multisite, cast to array*/
							$blog_info[$blog['blog_id']] = WPPIZZA() -> helpers -> wppizza_blog_details($blog['blog_id']);

							/*wppizza options, before any filters,  from blog we switched to*/
							if(!empty($args['format'])){
								$blog_options[$blog['blog_id']] = get_option(WPPIZZA_SLUG);
							}

							/* get date options for that blog */
							$date_format[$blog['blog_id']]= array('date' => get_option('date_format'), 'time' => get_option('time_format'));
	        			}
					restore_current_blog();
					}
	        	}
				/*
					set distinct select table columns for each blog,
					based on $max_table_columns to account for columns that do not exist
					in a table
				*/
				$blog_select_columns = array();
				if(!empty($blog_columns)){
				foreach($blog_columns as $bID => $blog_table_columns){
						$this_blog_columns = array();
						foreach($max_table_columns as $column){
							if(isset($blog_table_columns[$column])){
								$this_blog_columns[] = ''.$column.'';
							}else{
								$this_blog_columns[] = 'Null as '.$column.'';
							}

						}
					$blog_select_columns[$bID] = implode(', ',$this_blog_columns);
				}}
				/* end getting select columns */
			}
		}

		/*
			current blog only
		*/
		if (!$_multisite_orders){

			/* full orders table name */
			$blog_tables[$blog_id] = $wpdb->prefix . WPPIZZA_TABLE_ORDERS;

			/*bloginfo from blog - get_blog_details does not exist in non network setups*/
			$blog_info[$blog_id] = WPPIZZA() -> helpers -> wppizza_blog_details($blog_id);

			/* get options, before any filters if formatting*/
			if(!empty($args['format'])){
				$blog_options[$blog_id] = get_option(WPPIZZA_SLUG);
			}
			/* get date options for that blog */
			$date_format[$blog_id]= array('date' => get_option('date_format'), 'time' => get_option('time_format'));
		}

		/************************************************************************************************************
		*
		*
		*	construct and run the queries
		*
		*
		************************************************************************************************************/

			/*
				make sure we have some where clause constructed
				unless $args are specifically set to NULL to get all rows
			*/
			if(empty($where_clause) && !empty($force_all_rows)){
				return __('Error: Empty Query Parameters', 'wppizza-admin');
			}

			/***************************
			*	query getting count only
			*	, no limit
			***************************/
			$table_query = array();
			$totals_query = array();
			$t=0;
			foreach($blog_tables as $blogId => $table){
				/* counts and totals */
				$table_query[$t] = "SELECT ";
				$table_query[$t] .= "COUNT(".$meta['table_alias']."id) as order_count ";
				$table_query[$t] .= ", SUM(".$meta['table_alias']."order_total) as order_totals ";
				$table_query[$t] .= "FROM ";

				/* add for meta */
				$table_query[$t] .= "". $meta['closure_']." ";
				/* add for meta end */


				$table_query[$t] .= "" . $table . " ";
				if(empty($force_all_rows)){
					$table_query[$t] .= "WHERE ";
					$table_query[$t] .= implode(' AND ', $where_clause);
				}

				/* add for meta */
				$table_query[$t] .= "". $meta['_closure']." ";
				$table_query[$t] .= "". $meta['join']." ";
				/* add for meta end */

				/* allow filtering - 2nd parameter to identify count query */
				$table_query[$t] = apply_filters('wppizza_filter_orders_query', $table_query[$t], 'count');

			$t++;
			}

			/*
				construct query to get number of results
				and totals before pagination limits
				looping through blogs if necessary
			*/
			if(count($table_query) > 1){
				/* count */
				$query ="SELECT SUM(order_table.order_count) as order_count, SUM(order_table.order_totals) as order_totals FROM (";
				$query .= implode(' UNION ALL ', $table_query );
				$query .=" ) order_table ";
			}else{
				$query = $table_query[0];
			}

			/**********************************
			#
			#	[ini return vars]
			#
			***********************************
			/***
				add blog options to array if set by argument
				and not in multisite setup
			***/
			if(!is_multisite() && !empty($blog_options[$blog_id])){
				$results['blog_options'] = $blog_options[$blog_id];
			}

			/*
				BEFORE LIMIT COUNTS/SUMS
				run the query to get result count and sum before limits
			*/
			$result_sums_before_limit = $wpdb->get_results( $query, ARRAY_A);
			$results['total_number_of_orders'] = !empty($result_sums_before_limit[0]['order_count']) ? $result_sums_before_limit[0]['order_count'] : 0 ;/* just to simplify a bit */
			$results['total_value_of_orders'] = !empty($result_sums_before_limit[0]['order_totals']) ? $result_sums_before_limit[0]['order_totals'] : 0 ;/* just to simplify a bit */

			/**
				holds all used gateway idents
			**/
			$results['gateways_idents'] = array();
			/**
				total value sum of orders query result, LIMITED
			**/
			$results['value_orders_on_page'] = 0;

			/**
				number of orders query result, LIMITED
			**/
			$results['number_orders_on_page'] = 0;

			/********************************************
				only return the counts/totals if set
				and skip full query
			*********************************************/
			if(!empty($args['query']['summary'])){

				$query_totals = array();

				$query_totals['total_number_of_orders'] = $results['total_number_of_orders'];

				$query_totals['total_value_of_orders'] = $results['total_value_of_orders'];

			return $query_totals;
			}

			/***************************
			*	query getting results (limited if set)
			*	,sorted by date
			***************************/
			$table_query = array();
			$t=0;
			foreach($blog_tables as $blogId => $table){
				$table_query[$t] = "SELECT ";
				/*
					if quering multiple tables, we need to set distinct SELECT columns
					to make sure we have the same number of columns (any missing ones will be forced to null)
				*/
				$table_query[$t] .= !empty($blog_select_columns[$blogId]) ? ''.$blog_select_columns[$blogId].'' : ''.$meta['table_alias'].'*' ;//ADDDED ->  table_orders.*
				$table_query[$t] .= ", ".$meta['table_alias']."order_date as date_sort, '".$blogId."' as blog_id ";//ADDDED ->  table_orders.order_date

				/* add for meta */
				$table_query[$t] .= "". $meta['select']." ";
				/* add for meta end */

				$table_query[$t] .= "FROM ";

				/* add for meta */
				$table_query[$t] .= "". $meta['closure_']." ";
				/* add for meta end */

				$table_query[$t] .= "" . $table . " "; //TO ADD ->  AS table_orders

				if(empty($force_all_rows)){
					$table_query[$t] .= "WHERE ";
					$table_query[$t] .= implode(' AND ', $where_clause);
				}

				/* add for meta */
				$table_query[$t] .= "". $meta['_closure']." ";
				$table_query[$t] .= "". $meta['join']." ";
				$table_query[$t] .= "". $meta['group_by']." ";
				/* add for meta end */

				/* allow filtering - 2nd parameter to identify select/limit query */
				$table_query[$t] = apply_filters('wppizza_filter_orders_query', $table_query[$t], 'select');
			$t++;
			}
			/*
				construct query , limit, sort
				looping / union all through blogs if necessary
			*/
			if(count($table_query) > 1){
				$query ="";
				$query .= implode(' UNION ALL ', $table_query );
				$query .=" " ;
			}else{
				$query = $table_query[0] ;
			}

			$query .=" ORDER BY date_sort ".$args['sort']['sortorder']." " ;

			/**********************************
			#
			#	- provided we are not querying multiple tables -
			#	determine if we are really only expecting a single row
			#	and if so, limit query
			#	as querying for specific order id, or hash
			#	will(should) only ever return one result
			#
			***********************************/
			if(count($table_query) == 1 && ($args['query']['hash'] !== false || $args['query']['order_id'] !== false)){
				$query .=" LIMIT 0, 1";
			}else{

				/* if pagination/limits are set */
				if( $args['pagination']['limit']>0 || !empty($args['pagination']['limit']) || ($no_arguments_passed === true)){
					$query .=" LIMIT ";
					/* no, limit set , but pagination set to > 0 */
					$query .= (empty($args['pagination']['limit'])) ? $args['pagination']['paged'] : ($args['pagination']['paged'] * $args['pagination']['limit']);
					/* limit set */
					$query .= (!empty($args['pagination']['limit'])) ? ', '.$args['pagination']['limit'] : '';
				}
			}

			/**********************************
			#
			#	run the query (limited if set) to get orders results set
			#
			***********************************/
			$orders = $wpdb->get_results($query, ARRAY_A);

			/******************************************************************
				CONSTRUCT RESULTS SET :
				add date format , blog options,
				unserialize order_ini, customer_ini
				format selected parameters for consistency
			******************************************************************/
			$results['orders'] = array();

			foreach($orders as $key=>$order){

				/* returned from query */
				$order_blog_id = $order['blog_id'];
				/* create unique key made up of blog id and order id */
				$key = $order['blog_id'].'_'.$order['id'];


				/* create as array */
				$results['orders'][$key] = array();

				/* add unique order key made up from blog id and order id */
				$results['orders'][$key]['uoKey'] = $key;

				/* add all order parameters as object, formatting/unserializing some data as required for consistency throughout*/
				foreach($order as $column_key=>$column_val){
					if($column_key == 'initiator' ){/* uppercase gateway */
						$column_val = strtoupper($column_val);
						$initiator = $column_val;
					}
					if($column_key == 'order_status' ){/* lowercase order_status */
						$column_val = strtolower($column_val);
					}
					if($column_key == 'payment_status' ){/* lowercase payment_status */
						$column_val = strtolower($column_val);
					}
					if($column_key == 'order_ini' ){/* unserialize order_ini */
						$column_val = maybe_unserialize($column_val);
					}
					if($column_key == 'customer_ini' ){/* unserialize customer_ini */
						$column_val = maybe_unserialize($column_val);
					}
					if($column_key == 'user_data' ){/* unserialize user_data */
						$column_val = maybe_unserialize($column_val);
					}
					/* some parameters we want to add to the global values returned */
					if($column_key == 'initiator' ){/* uppercase gateway */
						$initiator = $column_val;
					}
					/* some parameters we want to add to the global values returned */
					if($column_key == 'order_total' ){/* uppercase gateway */
						$order_total = $column_val;
					}

				$results['orders'][$key][$column_key] = $column_val;
				}

				/** add blog info to order - used to add blog infos in orders_formatted() **/
				$results['orders'][$key]['blog_info'] = $blog_info[$order_blog_id];
				/** add blogs date options/format to order - used to format dates in orders_formatted() **/
				$results['orders'][$key]['date_format'] = $date_format[$order_blog_id];
				/** blog_options per order as in  multisite they might be different for orders from different blogs, simply omit if not formatting **/
				if(!empty($args['format'])){
					$results['orders'][$key]['blog_options'] = $blog_options[$order_blog_id];
				}


				/**
					purely for convenience, using currency set per order
					However
					 - for pre v3.x orders - ['param']['currency'] does not actually exist.
					 - if $args['format'] == false(in backend admin order history), $blog_options do not exist either (as we specifically don't add them above as they are really only needed when outputting an order formatted) so we simply set currency to 'false' to make wppizza_format_price use the global blog options
					 - this would also ONLY really ever become an issue if currencies are DIFFERENT for DIFFERENT ORDERS (or multisite blogs) AND an order IS PRE-V3.X
					so let's make a judgement call and not pollute coding and parameters more than necessary and stick with the above
				**/
				$results['orders'][$key]['currency'] = (!empty($results['orders'][$key]['order_ini']['param']['currency'])) ? $results['orders'][$key]['order_ini']['param']['currency'] : (empty($blog_options[$order_blog_id]['order_settings']['currency_symbol']) ? false : $blog_options[$order_blog_id]['order_settings']['currency_symbol'] );

				/**
					add meta data - if queried/exist
					@since 3.8
				**/
				if(isset($results['orders'][$key]['meta_id']) && isset($results['orders'][$key]['meta_key']) && isset($results['orders'][$key]['meta_value'])){

					$meta_ids = explode('|', $results['orders'][$key]['meta_id']);
					$meta_keys = explode('|', $results['orders'][$key]['meta_key']);
					$meta_values = explode('|', $results['orders'][$key]['meta_value']);

					$mata_data = array();
					foreach($meta_ids as $mKey => $meta_id){
						$mata_data[$meta_id] = array();
						$mata_data[$meta_id]['id'] = $meta_id;
						$mata_data[$meta_id]['key'] = $meta_keys[$mKey];
						$mata_data[$meta_id]['value'] = maybe_unserialize($meta_values[$mKey]);
					}


					$results['orders'][$key]['meta'] = $mata_data;

					/* unset old meta */
					unset($results['orders'][$key]['meta_id']);
					unset($results['orders'][$key]['meta_key']);
					unset($results['orders'][$key]['meta_value']);
				}

				/**
					format order (default) if not set to false
				**/
				if(!empty($args['format'])){

					/* format */
					$results['orders'][$key] = WPPIZZA()->order->orders_formatted($results['orders'][$key], false, $caller);

					/* distinctly re-add blog_info, date_format and checkout parameters (if exist)*/
					$results['orders'][$key]['blog_info'] = $blog_info[$order_blog_id];
					$results['orders'][$key]['date_format'] = $date_format[$order_blog_id];
					$results['orders'][$key]['checkout_parameters'] = !empty($results['orders'][$key]['checkout_parameters']) ? $results['orders'][$key]['checkout_parameters'] : array();


					/* simplify */
					$results['orders'][$key] = WPPIZZA()->order->simplify_order_values($results['orders'][$key],  $format_blog_options, $format_order_sections, $format_registered_userdata);

				}

				/**
					add used gateway ident using key to end up with unique array
				**/
				$results['gateways_idents'][$initiator] = $initiator;
				/**
					add to total ordered amount of shown items WITHIN LIMITS
				**/
				$results['value_orders_on_page'] += $order_total;

				/**
					add to total orderes WITHIN LIMITS
				**/
				$results['number_orders_on_page']++;

			}

	return $results;
	}

	/**************************************************************************************************
	*
	*
	*	map session order details to db fields
	*	returns false if we cannot checkout yet
	*
	*
	**************************************************************************************************/
	function map_order($user_session, $checkout_parameters_only = false) {
		global $current_user, $wpdb, $blog_id, $wppizza_options;
		/*
			grab order details in current session
			,unset irrelevant values for storing,
			add some unique id and session id(to make sure it is really unique)
		*/
		$order_session = WPPIZZA()->session->sort_and_calculate_cart(true);
		$order_session_checkout_parameters = $order_session['checkout_parameters'];
		/** only get is_checkout, can_checkout etc - orderpage **/
		if(!empty($checkout_parameters_only)){
			return $order_session_checkout_parameters;
		}

		/* unset unnecessary parameters that are only used when calculating things in pages etc but are not relevant when storing data in db*/
		unset($order_session['checkout_parameters']);


		/* add session id */
		$order_session['info']['session_id'] = session_id();
		/* add unique ident */
		$order_session['info']['unique_id'] = (function_exists('microtime')) ? microtime(true) : time();

		/*
			current date based on WP_time
		*/
		$order_date = date('Y-m-d H:i:s', WPPIZZA_WP_TIME );

		/*
			UTC
		*/
		$order_date_utc = date('Y-m-d H:i:s', WPPIZZA_UTC_TIME );

		/*
			customer
		*/
		$customer_data = apply_filters('wppizza_filter_add_to_customer_ini', $user_session, 'session');

		/*
			email
			maybe serialize(just to be sure)
			and truncate to 64 max (as db field is indexed VARCHAR 64)
		*/
		$cemail = !empty($customer_data['cemail']) ? substr(maybe_serialize($customer_data['cemail']),0, 63) : '' ;


		/*

			map data

		*/
		$wp_user_id 				= $current_user->ID; /* user id or 0 if not logged in */
		$order_date					= $order_date; /* current time based on WP timezone */
		$order_date_utc				= $order_date_utc; /* utc */
		$order_update				= '0000-00-00 00:00:00';/* 0  until status change, notes added or similar */
		$order_delivered			= '0000-00-00 00:00:00'; /* initialize as 0 when adding to db */
		$customer_details 			= '';
		$order_details				= '';
		$order_status 				= 'NEW';
		$order_ini 					= maybe_serialize(apply_filters('wppizza_filter_add_to_order_ini',$order_session));
		$order_no_of_items 			= $order_session['summary']['number_of_items'];
		$order_items_total 			= $order_session['summary']['total_price_items'];
		$order_discount 			= $order_session['summary']['discount'];
		$order_taxes 				= !empty($order_session['summary']['taxes']) ?  $order_session['summary']['taxes'] : 0 ;
		$order_taxes_included 		= !empty($order_session['param']['tax_included']) ?  'Y' : 'N' ;
		$order_delivery_charges 	= $order_session['summary']['delivery_charges'];
		$order_handling_charges 	= $order_session['summary']['handling_charges'];
		$order_tips 				= $order_session['summary']['tips'];
		$order_self_pickup 			= !empty($order_session['summary']['self_pickup']) ?  'Y' : 'N' ;
		$order_total 				= $order_session['summary']['total'];
		$order_refund 				= 0;
		$customer_ini 				= maybe_serialize($customer_data);/* allow arbitrary array data to be added/stored  in customer_ini (i.e user session) to - perhaps -	save some additional values without outputting them anywhere by default */
		$payment_status 			= 'INITIALIZED';
		$transaction_id 			= '';
		$transaction_details 		= '';
		$transaction_errors 		= '';
		$display_errors 			= '';
		$validate_initiator 		= wppizza_validate_alpha_only($user_session[''.WPPIZZA_SLUG.'_gateway_selected']);/* php 5.3 */
		$initiator 					= !empty($validate_initiator) ? $validate_initiator : 'COD';
		$mail_sent 					= 'N';
		$mail_error 				= '';
		$notes 						= '';
		$session_id 				= session_id();
		$email 						= !empty($cemail) ? wppizza_maybe_encrypt_decrypt($cemail, true, 190, true) : '' ;//store email encrypted using WPPIZZA_CRYPT_KEY (so it can be decrypted for db queries - notably privacy export)
		$ip_address 				= ( empty($wppizza_options['tools']['privacy']) || (!empty($wppizza_options['tools']['privacy']) && !empty($wppizza_options['tools']['privacy_keep_ip_address'])) ) ? $_SERVER['REMOTE_ADDR'] : wppizza_anonymize_data('ip_address', $_SERVER['REMOTE_ADDR']) ;//store ip addresses anonimised if privacy enabled unless specifically set
		$user_defined 				= maybe_serialize(apply_filters('wppizza_db_column_user_defined','', $order_session, $user_session));/* a text field that can be freely used - serialized if necessary*/
		$user_data=array();
			$user_data['HTTP_USER_AGENT']=!empty($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '--n/a--';
			$user_data['HTTP_REFERER']=!empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '--n/a--';
		$user_data = empty($wppizza_options['tools']['privacy']) || !empty($wppizza_options['tools']['privacy_keep_browser_data']) ? maybe_serialize($user_data) : '' ;
		/*

			store data in db

		*/
		$order_data = array();
		/** map fields to data, all fields, but autoinserts or irrelevant for first insert commented out */
		/*only inserting or updateing INITIALIZED order */
		$order_data['wp_user_id'] = array('data' => $wp_user_id, 'type' => '%d' );
		$order_data['order_date'] = array('data' => $order_date, 'type' => '%s' );
		$order_data['order_date_utc'] = array('data' => $order_date_utc, 'type' => '%s' );
		$order_data['order_update'] = array('data' => $order_update, 'type' => '%s' );
		$order_data['order_delivered'] = array('data' => $order_delivered, 'type' => '%s' );
		$order_data['customer_details'] = array('data' => $customer_details, 'type' => '%s' );
		$order_data['order_details'] = array('data' => $order_details, 'type' => '%s' );
		$order_data['order_status'] = array('data' => $order_status, 'type' => '%s' );
		$order_data['order_ini'] = array('data' => $order_ini, 'type' => '%s' );
		$order_data['order_no_of_items'] = array('data' => $order_no_of_items, 'type' => '%d' );
		$order_data['order_items_total'] = array('data' => $order_items_total, 'type' => '%f' );
		$order_data['order_discount'] = array('data' => $order_discount, 'type' => '%f' );
		$order_data['order_taxes'] = array('data' => $order_taxes, 'type' => '%f' );
		$order_data['order_taxes_included'] = array('data' => $order_taxes_included, 'type' => '%s' );
		$order_data['order_delivery_charges'] = array('data' => $order_delivery_charges, 'type' => '%f' );
		$order_data['order_handling_charges'] = array('data' => $order_handling_charges, 'type' => '%f' );
		$order_data['order_tips'] = array('data' => $order_tips, 'type' => '%f' );
		$order_data['order_self_pickup'] = array('data' => $order_self_pickup, 'type' => '%s' );
		$order_data['order_total'] = array('data' => $order_total, 'type' => '%s' );
		$order_data['order_refund'] = array('data' => $order_refund, 'type' => '%f' );
		$order_data['customer_ini'] = array('data' => $customer_ini, 'type' => '%s' );
		$order_data['payment_status'] = array('data' => $payment_status, 'type' => '%s' );
		$order_data['transaction_id'] = array('data' => $transaction_id, 'type' => '%s' );
		$order_data['transaction_details'] = array('data' => $transaction_details, 'type' => '%s' );
		$order_data['transaction_errors'] = array('data' => $transaction_errors, 'type' => '%s' );
		$order_data['display_errors'] = array('data' => $display_errors, 'type' => '%s' );
		$order_data['initiator'] = array('data' => $initiator, 'type' => '%s' );
		$order_data['mail_sent'] = array('data' => $mail_sent, 'type' => '%s' );
		$order_data['mail_error'] = array('data' => $mail_error, 'type' => '%s' );
		$order_data['notes'] = array('data' => $notes, 'type' => '%s' );
		$order_data['session_id'] = array('data' => $session_id, 'type' => '%s' );
		$order_data['email'] = array('data' => $email, 'type' => '%s' );
		$order_data['ip_address'] = array('data' => $ip_address, 'type' => '%s' );
		$order_data['user_data'] = array('data' => $user_data, 'type' => '%s' );
		$order_data['user_defined'] = array('data' => $user_defined, 'type' => '%s' );



		/**
			added filtering - not used in plugin
			to allow other plugins to add their own data if - for example - they
			have added their own columns (or indeed change what goes in it)
		**/
		$order_data = apply_filters('wppizza_filter_db_column_data', $order_data, $user_session);


		$order = array();
		$order['data'] = array();
		$order['type'] = array();
		foreach($order_data as $key=>$val){
			$order['data'][$key] = $val['data'];
			$order['type'][] = $val['type'];
		}
		/* get session checkout parameters */
		$order['checkout_parameters'] = $order_session_checkout_parameters;

	return $order;
	}

	/**************************************************************************************************
	*
	*	add/update metadata to order
	*
	*	@ since 3.8
	*	@param int
	*	@param str
	*	@param mixed
	*	@return bool false or meta_id
	**************************************************************************************************/
	function do_order_meta($order_id = false, $meta_key = false, $meta_value = false){
		global $wpdb;

		/*
			sanitize input
		*/
		$order_id = ( $order_id !== '' && (string)preg_replace("/[^0-9]/","",$order_id) === (string)$order_id  ) ? (int)$order_id : false ; //make sure some sensible order id was set (0 allowed) and not an empty string or some such
		$meta_key = $this->sanitize_meta_key($meta_key); //eliminate funny characters and whitespaces etc
		$meta_value = maybe_serialize($meta_value);


		/*
			check $order_id >=0 and $meta_key is a non empty string
		*/
		if( $order_id === false || !is_string($meta_key) || empty($meta_key) ){
			return false;
		}



		/*
			$meta table
		*/
		$meta_table = $wpdb->prefix . WPPIZZA_TABLE_ORDERS_META;


		/*
			check if it already exists
		*/

			/* prepare */
			$where_clause = array();
			$where_clause['order_id'] = $wpdb->prepare("order_id = %d", $order_id);
			$where_clause['meta_key'] = $wpdb->prepare("meta_key = %s", $meta_key);

			/* mk query */
			$query = "SELECT * ";
			$query .= "FROM ";
			$query .= "".$meta_table." ";
			$query .= "WHERE ";
			$query .= implode(' AND ', $where_clause);

			/* run query , only returning one result using get_row (as opposed to get_results) - as these really should be unique for each order id */
			$results = $wpdb->get_row( $query, ARRAY_A);

			/* return meta id if there is one */
			$meta_id = empty($results['meta_id']) ? 0 : $results['meta_id'] ;



		/*
			set action - add new meta key or update existing
		*/
		$action = empty($meta_id) ? 'add_meta' : 'update_meta' ;


		/*
			insert new
		*/
		if('add_meta' === $action){

			$db_meta = array();
			//order_id
			$db_meta['data']['order_id'] = $order_id;
			$db_meta['type'][] = '%d';
			//meta_key
			$db_meta['data']['meta_key'] = $meta_key;
			$db_meta['type'][] = '%s';
			//meta_value
			$db_meta['data']['meta_value'] = $meta_value;
			$db_meta['type'][] = '%s';

			//insert metadata
			$wpdb->insert( $meta_table , $db_meta['data'], $db_meta['type']);
			$insert_id = $wpdb->insert_id;

			$meta_insert_id = !empty($insert_id) ?  $insert_id : false;

		return $meta_insert_id;
		}


		/*
			update existing
		*/
		if('update_meta' === $action){

			/**
				where | where_format
			**/
			$where = array();
			$where_format = array();
			//$meta_id
				$where['meta_id'] = $meta_id;
				$where_format[] = '%d';

			//$meta_key
				$where['meta_key'] = $meta_key;
				$where_format[] = '%s';


			/**
				meta value
			**/
			$db_meta = array();
			$db_meta['data']['meta_value'] = $meta_value;
			$db_meta['type'][] = '%s';

			/**
				update meta
			**/
			$update_meta = $wpdb->update( $meta_table , $db_meta['data'], $where , $db_meta['type'], $where_format);
			$meta_update_id = (false === $update_meta) ? false : $meta_id ;// might be 0 rows updated, so let's check for false here, see Return values https://codex.wordpress.org/Class_Reference/wpdb#UPDATE_rows

		return $meta_update_id;
		}

	return;
	}

	/**************************************************************************************************
	*
	*	delete metadata from meta table for a specific order id
	*
	*	@ since 3.8
	*	@param int
	*	@param str or false if not querying for specific meta_key
	*	@return bool
	**************************************************************************************************/
	function delete_order_meta($order_id = false, $meta_key = false){
		global $wpdb;

		/*
			check $order_id is not empty and $meta_key is a set string if not false
		*/
		if(empty($order_id) || ($meta_key!==false && !is_string($meta_key)) ){
			return false;
		}

		/*
			sanitize input
		*/
		$order_id = (int)$order_id;
		$meta_key = $this->sanitize_meta_key($meta_key);


		/*
			skip if neither is defined
		*/
		if(empty($order_id) && empty($meta_key)){
			return false;
		}

		/*
			$meta table
		*/
		$meta_table = $wpdb->prefix . WPPIZZA_TABLE_ORDERS_META;


		/*
			get meta table row id's depending on query
		*/
		/* prepare */
		$where_clause = array();
		$where_clause['order_id'] = $wpdb->prepare("order_id = %d", $order_id);

		if(!empty($meta_key)){
			$where_clause['meta_key'] = $wpdb->prepare("meta_key = %s", $meta_key);
		}

		/* mk query */
		$query = "SELECT meta_id FROM ".$meta_table." WHERE ".implode(' AND ', $where_clause)." ";

		/*
			run query returning all applicable meta id's
		*/
		$results = $wpdb->get_results( $query, ARRAY_A);
		if(empty($results)){
			return false;
		}

		$bool = true;
		foreach($results as $meta){
			$meta_id = 	$meta['meta_id'];
			$delete_meta = $wpdb->delete( $meta_table, array( 'meta_id' => $meta['meta_id'] ) );
			if(false === $delete_meta){// might be 0 rows deleted, so let's check for false here, see https://codex.wordpress.org/Class_Reference/wpdb#DELETE_Rows
				$bool = false;
			}
		}

	return $bool;//only returns false here if wpdb->delete has thrown errors
	}

	/**************************************************************************************************
	*
	*	delete metadata from meta table for a specific meta key
	*
	*	@ since 3.8.4
	*	@param int
	*	@param str or false if not querying for specific meta_key
	*	@return bool
	**************************************************************************************************/
	function delete_order_meta_by_key($meta_key = false){
		global $wpdb;

		/*
			check $meta_key is a set string if not false
		*/
		if(empty($meta_key) || !is_string($meta_key)){
			return false;
		}

		/*
			sanitize input
		*/
		$meta_key = $this->sanitize_meta_key($meta_key);


		/*
			$meta table
		*/
		$meta_table = $wpdb->prefix . WPPIZZA_TABLE_ORDERS_META;


		/*
			get meta table row id's depending on query
		*/
		/* prepare */
		$where_clause = array();
		$where_clause['meta_key'] = $wpdb->prepare("meta_key = %s", $meta_key);


		/* mk query */
		$query = "SELECT meta_id FROM ".$meta_table." WHERE ".implode(' AND ', $where_clause)." ";

		/*
			run query returning all applicable meta id's
		*/
		$results = $wpdb->get_results( $query, ARRAY_A);
		if(empty($results)){
			return false;
		}

		$bool = true;
		foreach($results as $meta){
			$meta_id = 	$meta['meta_id'];
			$delete_meta = $wpdb->delete( $meta_table, array( 'meta_id' => $meta['meta_id'] ) );
			if(false === $delete_meta){// might be 0 rows deleted, so let's check for false here, see https://codex.wordpress.org/Class_Reference/wpdb#DELETE_Rows
				$bool = false;
			}
		}

	return $bool;//only returns false here if wpdb->delete has thrown errors
	}



	/**************************************************************************************************
	*
	*	get orderid and meta id  from meta table for a specific meta key
	*	optionally check for a specific value of this meta key too
	*
	*	@ since 3.8.4
	*	@param str 						meta key to query
	*	@param $meta_value  	to query for specific meta value too
	*	@return array[meta_id] = order_id
	**************************************************************************************************/
	function get_order_id_by_meta_key($meta_key = false, $meta_value = NULL){

		global $wpdb;

		/*
			check $meta_key is a set string if not false
		*/
		if(empty($meta_key) || !is_string($meta_key)){
			return false;
		}

		/*
			sanitize input
		*/
		$meta_key = $this->sanitize_meta_key($meta_key);


		/*
			$meta table
		*/
		$meta_table = $wpdb->prefix . WPPIZZA_TABLE_ORDERS_META;


		/*
			get meta table row id's depending on query
		*/
		/* prepare */
		$where_clause = array();

		$where_clause['meta_key'] = $wpdb->prepare("meta_key = %s", $meta_key);

		if($meta_value !== NULL ){
			$where_clause['meta_value'] = $wpdb->prepare("meta_value = %s", $meta_value);
		}

		/* mk query */
		$query = "SELECT meta_id, order_id FROM ".$meta_table." WHERE ".implode(' AND ', $where_clause)." ";

		/*
			run query returning all applicable meta id's
		*/
		$query_results = $wpdb->get_results( $query, ARRAY_A);
		if(empty($query_results)){
			return array();
		}else{
			$results = array();
			/* make simple meta_id -> order_id array */
			foreach($query_results as $val){
				$results[$val['meta_id']] = $val['order_id'] ;
			}
		}

	return $results;
	}



	/********************************************************************************
		get meta data for an order

		@ since 3.8
		@ param int
		@ param str / bool
		@ return mixed (false, str or array)
	************************************************************************************/
	function get_order_meta($order_id = false, $meta_key = false, $meta_value_only = false){
		global $wpdb;

		/*
			check $order_id is not empty and $meta_key is a set string - allow for 0
		*/
		if($order_id === '' || (string)preg_replace("/[^0-9]/","",$order_id) !== (string)$order_id ){
			return false;
		}


		/*
			sanitize input
		*/
		$order_id = (int)$order_id;
		$meta_key = is_string($meta_key) ? $this->sanitize_meta_key($meta_key) : false ;

		/*
			$meta table
		*/
		$meta_table = $wpdb->prefix . WPPIZZA_TABLE_ORDERS_META;

		/*
			get value
		*/

		/* prepare */
		$where_clause = array();
		/* query order id */
		$where_clause['order_id'] = $wpdb->prepare("order_id = %d", $order_id);
		/* query specific meta key if set */
		if($meta_key){
			$where_clause['meta_key'] = $wpdb->prepare("meta_key = %s", $meta_key);
		}

		/* mk query */
		$query = "SELECT * ";
		$query .= "FROM ";
		$query .= "".$meta_table." ";
		$query .= "WHERE ";
		$query .= implode(' AND ', $where_clause);

		/*
			run query ,
			only returning one result using get_row (as opposed to get_results) -
			if querying for a specific key (as these really should be unique for each order id if querying a distinct meta_key)
		*/
		$results = $meta_key ? $wpdb->get_row( $query, ARRAY_A) : $wpdb->get_results( $query, ARRAY_A);


		/* return meta value if there is one */
		$meta_values = false ;

		/* querying a specific key */
		if($meta_key && !empty($results['meta_id'])){

			$meta_values = array();

			/* (not) returning values only */
			if(empty($meta_value_only)){
				$meta_values['meta_id'] = $results['meta_id'];
				$meta_values['meta_value'] = maybe_unserialize($results['meta_value']);
			}else{
				$meta_values = maybe_unserialize($results['meta_value']);
			}

		}

		/* querying all meta for specific order without a specific key, returning array but using meta_key as array key to make them unique (as there really should only be distinct meta_keys per order)*/
		if(!$meta_key && !empty($results) && is_array($results) ){

			$meta_values = array();

			foreach($results as $k=>$metas){
				/* (not) returning values only */
				if(empty($meta_value_only)){
					$meta_values[$metas['meta_key']]['id'] = $metas['meta_id'];
					$meta_values[$metas['meta_key']]['value'] = maybe_unserialize($metas['meta_value']);
				}else{
					$meta_values[$metas['meta_key']] = maybe_unserialize($metas['meta_value']);
				}
			}
		}

	return $meta_values;
	}

	/********************************************************************************
		sanitize meta keys used

		@ since 3.8
		@ param str
		@ return str
	************************************************************************************/
	function sanitize_meta_key($meta_key){
		$meta_key = strtolower(wppizza_validate_alpha_only($meta_key));
	return $meta_key;
	}

}
?>