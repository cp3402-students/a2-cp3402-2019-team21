<?php
/**
* WPPIZZA_MODULE_TOOLS_PRIVACY_ERASE Class
*
* @package     WPPIZZA
* @subpackage  WPPIZZA_MODULE_TOOLS_PRIVACY_ERASE
* @copyright   Copyright (c) 2015, Oliver Bach
* @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
* @since       3.6
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
class WPPIZZA_MODULE_TOOLS_PRIVACY_ERASE{

	private $settings_page = 'tools';/* which admin subpage (identified there by this->class_key) are we adding this to */
	private $tab_key = 'privacy';/* must be unique within this admin page*/
	private $section_key = 'erase';
	private $privacy_erase_setup = array();
	private $privacy_erase_defaults = array();

	function __construct() {
		/**********************************************************
			[add settings to admin]
		***********************************************************/
		if(is_admin()){

			/*** setup array of statuses ***/
			add_action('admin_init', array($this, 'privacy_erase_setup'), 5);
			/*** add to a specific tab ***/
			add_filter('wppizza_filter_admin_tabs_'.$this->settings_page.'', array($this, 'admin_tabs'), 20);
			/* add admin options settings page*/
			add_filter('wppizza_filter_settings_sections_'.$this->settings_page.'', array($this, 'admin_options_settings'), 10, 5);
			/* add admin options settings page fields */
			add_action('wppizza_admin_settings_section_fields_'.$this->settings_page.'', array($this, 'admin_options_fields_settings'), 10, 5);
			/**add default options **/
			add_filter('wppizza_filter_setup_default_options', array( $this, 'options_default'), 10, 2 );
			/**validate options**/
			add_filter('wppizza_filter_options_validate', array( $this, 'options_validate'), 10, 2 );
			/**add text header*/
//			//add_action('wppizza_settings_sections_header_'.$this->settings_page.'', array( $this, 'sections_header'), 10, 2 );


			/** register anonymise/erase function**/
			add_filter('wp_privacy_personal_data_erasers', array( $this, 'register_privacy_eraser'));

		}

	}

	/*******************************************************************************************************************************************************
	*
	*
	*
	* 	[admin privacy actions (erase) ]
	*
	*
	*
	********************************************************************************************************************************************************/
	/*
	#	setup of erase parameters array
	#	@since 3.6
	#	@return void
	*/
	function privacy_erase_setup(){

			/*
				current site timestamp
				minus "x" seconds/hours after which
				we consider non completed hours to
				have been abandoned
			*/
			$abandoned_since = date('Y-m-d H:i:s',(WPPIZZA_WP_TIME - (24*3600)));// 24 hours ago


			/*
				setup array
			*/
			$this->privacy_erase_setup = array(

				'actions' => array(
					'no_action' => array(
						'label' => ''.__('No Action', 'wppizza-admin').''
					),
					'anonymise' => array(
						'label' => ''.__('Anonymise Order Data', 'wppizza-admin').''
					),
					'delete' => array(
						'label' => ''.__('Delete Order Data', 'wppizza-admin').''
					),
				),

				'type' => array(

					'abandoned' => array(
						'payment_status'=> array('INITIALIZED' => true, 'INPROGRESS' => true, 'AUTHORIZED' => true, 'CONFIRMED' => true, 'UNCONFIRMED' => true, 'EXPIRED' => true, 'ABANDONED' => true),
						'abandoned_since'=> $abandoned_since,
						'label'=>__('Abandoned', 'wppizza-admin'),
						'default_action' => 'delete',
					),

					'pending' => array(
						'payment_status'=> array('INITIALIZED' => true, 'INPROGRESS' => true, 'AUTHORIZED' => true, 'CONFIRMED' => true, 'UNCONFIRMED' => true),
						'label'=>__('Pending', 'wppizza-admin'),
						'default_action' => 'no_action',
					),

					'completed' => array(
						'payment_status'=>array('COMPLETED' => true, 'CAPTURED' => true),
						'label'=>__('Completed', 'wppizza-admin'),
						'default_action' => 'no_action',
					),

					'refunded' => array(
						'payment_status'=>array('REFUNDED' => true),
						'label'=>__('Refunded', 'wppizza-admin'),
						'default_action' => 'no_action',
					),

					'cancelled' => array(
						'payment_status'=>array('CANCELLED' => true),
						'label'=>__('Cancelled', 'wppizza-admin'),
						'default_action' => 'anonymise',
					),

					'rejected' => array(
						'payment_status'=>array('REJECTED' => true),
						'label'=>__('Rejected', 'wppizza-admin'),
						'default_action' => 'anonymise',
					),

					'failed' => array(
						'payment_status'=>array('FAILED' => true, 'INVALID' => true),
						'label'=>__('Failed', 'wppizza-admin'),
						'default_action' => 'delete',
					),

				),
			);

			/*
				set default erase actions
			*/
			$this->privacy_erase_defaults = array();
			foreach($this->privacy_erase_setup['type'] as $k=>$a){
				$this->privacy_erase_defaults[$k] = $a['default_action'];
			}



	}
	/*
	#	register callback
	#	@since 3.6
	#	@return array()
	*/
	function register_privacy_eraser( $erasers ) {
		global $wppizza_options;
		// only if enabled
		if(!empty($wppizza_options[$this->settings_page]['privacy'])){
	  		$erasers[WPPIZZA_SLUG] = array(
	    		'eraser_friendly_name' => WPPIZZA_NAME,
	    		'callback'             =>  array($this, 'privacy_eraser'),
	    	);
		}
	  return $erasers;
	}

	/*------------------------------------------------------------------------------
	#	[anonymise/erase data]
	#
	#	@since 3.6
	#	@return array()
	------------------------------------------------------------------------------*/
	function privacy_eraser( $email_address, $page = 1 ){
		global $wppizza_options, $wpdb;

		/*
			set custom query clause to not query for any
			orders that are set to have no_action taken
			if all statuses are all set to "no_action"
			custom query ends up being an empty string, in wich case we simply return "done"
		*/
		$order_needs_action = array();
		foreach($this->privacy_erase_setup['type'] as $type_key => $type_vars){
			if($wppizza_options[$this->settings_page]['privacy_erase'][$type_key] !== 'no_action' ){
				$order_needs_action[$type_key] = " payment_status IN ('".implode("','",array_keys($type_vars['payment_status']))."')";
				if(isset($type_vars['abandoned_since'])){
					$order_needs_action[$type_key] .= " AND order_date < '".$type_vars['abandoned_since']."' ";
				}
			}
		}
		/*
			set custom query if any status is not set to be 'no_action'
			else simply skip the rest
		*/
		if(!empty($order_needs_action)){
			$custom_query = '( ( '.implode(' ) OR ( ' , $order_needs_action).') )';
		}
		if(empty($custom_query)){
			$erase_results = array(
				'items_removed' => true,
				'items_retained' => false,
				'messages' => array(sprintf(__('No %s records to process', 'wppizza-admin'), WPPIZZA_NAME)),
				'done' => true
			);
			return $erase_results;
		}






		/*
			ini counts
		*/
		//$orders_retained_count = 0; unused
		$orders_removed_count = 0;
		$orders_anonymised_count = 0;

		$timestamp = date('Y-m-d H:i:s',WPPIZZA_WP_TIME);


		$limit = 250; // 250 Limit us to avoid timing out
		$page = (int)$page;

		/*
			anonymise order according to payment_status
			cast to uppercase
		*/
		$status_order_anonymise = array();
		$status_order_anonymise = array_map('strtoupper', $status_order_anonymise);

		/*
			delete order according to payment_status
			cast to uppercase
		*/
		$status_order_delete = array();
		$status_order_delete = array_map('strtoupper', $status_order_delete);

		/*
			email addresses stored might also be encrypted (if a WPPIZZA_CRYPT_KEY was set)
			so lets make an array of encrypted and unencrypted emails to check
		*/
		$email_erase = array();
		$email_erase[] = $email_address; //unencrypted email
		$email_erase[] = wppizza_maybe_encrypt_decrypt($email_address, true, false, true); //perhaps encrypted email
		/* make sure they are not identical (in case a WPPIZZA_CRYPT_KEY was not set) */
		$email_erase = array_unique(array_filter($email_erase));


		/*****************************************************
			query args erase/anonymise
		*****************************************************/
		$args = array(
			'query'=>array(
				'payment_status' => 'NULL',//getting all data, even abandoned/non completed
				'email' => $email_erase,//query for plain and hashed emails (if any)
				'custom_query' => $custom_query,//excluding orders that are set to 'no_action'
			),
			'pagination' =>array(
				'paged' => 	0 ,//must be 0 instead of $page as we are dealing with ajax requests that run a new query every time
				'limit' => $limit ,
			),
			'format' => false,
		);

		/*
			will get orders on ALL blogs if multisite and all orders for all sites enabled on parent if called from parent blog
		*/
		$orders_for_erasure = WPPIZZA()->db->get_orders($args, 'privacy_eraser');
		$max_pages = ceil($orders_for_erasure['total_number_of_orders'] / $limit);

		/*****************************************************
			do we have any orders to start off with ?
			also set if no email was being queried at all, just to make sure
		*****************************************************/
		if(empty($email_address) || empty($orders_for_erasure['total_number_of_orders'])){
			$erase_results = array(
				'items_removed' => true,
				'items_retained' => false,
				'messages' => array(sprintf(__('No %s records to process', 'wppizza-admin'), WPPIZZA_NAME)),
				'done' => true
			);
			return $erase_results;
		}


		/****************************************************
			loop through orders and anonymise or delete as required
		****************************************************/
		$items_removed = true;
		$items_retained = false;
		foreach ( $orders_for_erasure['orders'] as $order_id => $order ) {

			/*
				get current payment status
			*/
			$payment_status = strtoupper($order['payment_status']);

			/*
				order timestamp
			*/
			$order_date = $order['order_date'];

			/*
				blog id
			*/
			$order_blog_id = $order['blog_id'];

			/*
				order id
			*/
			$order_id = $order['id'];


			/*
				what type of order is it ?
				(completed, abandoned, refunded etc etc )
			*/
			$isofType = false;
			foreach($this->privacy_erase_setup['type'] as $order_type => $type_setup){

				/*
					distinguish between abandoned and
					not-abandoned check first
				*/
				if(!empty($type_setup['abandoned_since'])){
					/*
						is abandoned order ?
						provided it's in a noncompleted state
					*/
					if(isset($type_setup['payment_status'][$payment_status]) && $order_date <= $type_setup['abandoned_since']){

						$isofType = $order_type;//abandoned

					break;
					}

				}else{

					/*
						check status without abandoned_since
					*/
					if(isset($type_setup['payment_status'][$payment_status])){
						$isofType = $order_type;
					break;
					}

				}
			}


			/*
				what are we to do with this order ?
			*/
			$erase_action = !empty($wppizza_options[$this->settings_page]['privacy_erase'][$isofType]) ? $wppizza_options[$this->settings_page]['privacy_erase'][$isofType] : 'no_action';


			/*
				no_action - unused
			*/
			//if($erase_action == 'no_action'){
			//	// counter retained
			//	$orders_retained_count++;
			//}


			/*
				delete
			*/
			if($erase_action == 'delete'){
				// counter removed
				$orders_removed_count++;

				// delete order
				WPPIZZA() -> db -> delete_order($order_id, $order_blog_id);

			}


			/*
				anonymise
			*/
			if($erase_action == 'anonymise'){
				// counter anonymised
				$orders_anonymised_count++;


				$update_values = array();
				$update_values['anonymised'] 			= array('type'=> '%s', 'data' => $timestamp);
				$update_values['wp_user_id'] 			= array('type'=> '%d', 'data' => 0 );
				$update_values['session_id'] 			= array('type'=> '%s', 'data' => !empty($order['session_id']) ? wppizza_anonymize_data('text') : '' );
				$update_values['hash'] 					= array('type'=> '%s', 'data' => !empty($order['hash']) ? wppizza_anonymize_data('text') : '' );
				$update_values['ip_address'] 			= array('type'=> '%s', 'data' => !empty($order['ip_address']) ? wppizza_anonymize_data('ip_address', $order['ip_address']) : '0.0.0.0' );
				// for the time being at least, let's not anonymise transaction data as we will never be able to refund etc etc and there is most definitely a legitimate business interest in keeping this data */
				//$update_values['transaction_id'] 		= array('type'=> '%s', 'data' => !empty($order['transaction_id']) ? wppizza_anonymize_data('transaction_id', $order['transaction_id']) : '' );
				//$update_values['transaction_details']	= array('type'=> '%s', 'data' => !empty($order['transaction_details']) ? wppizza_anonymize_data('text') : '' );
				//$update_values['transaction_errors']	= array('type'=> '%s', 'data' => !empty($order['transaction_errors']) ? wppizza_anonymize_data('text') : '' );
				$update_values['user_data'] 			= array('type'=> '%s', 'data' => !empty($order['user_data']) ? wppizza_anonymize_data('text') : '' );
				$update_values['notes'] 				= array('type'=> '%s', 'data' => wppizza_anonymize_data('anonymised_note', $order['notes'], $timestamp ));
				$update_values['email'] 				= array('type'=> '%s', 'data' => !empty($order['email']) ? wppizza_anonymize_data('email_mask', $email_address) : '' );
				$update_values['customer_ini'] 			= array('type'=> '%s', 'data' => !empty($order['customer_ini']) ? wppizza_anonymize_data('anonymised_customer_data', $order['customer_ini']) : '');
				$update_values['order_ini']				= array('type'=> '%s', 'data' => !empty($order['order_ini']) ? wppizza_anonymize_data('anonymised_order_data', $order['order_ini']) : '');
				$update_values['customer_details'] 		= array('type'=> '%s', 'data' => !empty($order['customer_details']) ? '*** anonymised: '.wppizza_anonymize_data('email_mask', $email_address).' ***' : '' );


				$update_values = apply_filters( 'wppizza_filter_privacy_eraser', $update_values, $order );

				// anonymise order
				WPPIZZA() -> db ->  update_order($order_blog_id, $order_id, false, $update_values);

			}

		}


		/*
			Tell core if we still have more to work on
			or not as the case my be
		*/
		$done = $orders_for_erasure['number_orders_on_page'] < $limit;


		/*
			return data
		*/
		$msg = array();
		$msg[0] = sprintf(__('%s - %s %s records processed', 'wppizza-admin'), (($page-1) * $limit) ,  (($page-1) * $limit) + $orders_for_erasure['number_orders_on_page'] , WPPIZZA_NAME);
		$msg[1] = sprintf(__('%s anonymised, %s deleted', 'wppizza-admin'), $orders_anonymised_count, $orders_removed_count);//$orders_retained_count,
		$erase_results = array(
			'items_removed' => $items_removed,
			'items_retained' => $items_retained,
			'messages' => array('<b>'.$msg[0].'</b> '.$msg[1].''),
			'done' => $done
		);

		// write log
  		$erase_log_file = WPPIZZA_PATH_LOGS . WPPIZZA_SLUG.'-privacy-erase-'.wp_hash('wppizzaerase').'.log';
  		$msg = implode(', ',$msg);
		file_put_contents($erase_log_file,''.date('Y-m-d H:i:s').', '.$email_address.', ' . print_r($msg, true).''.PHP_EOL, FILE_APPEND);

		if($done){
		$erase_results['messages'][] =	array( sprintf(__('A log of this action has been saved to %s','wppizza-admin'), $erase_log_file ) );
		}


	return $erase_results;
	}

	/*******************************************************************************************************************************************************
	*
	*
	*
	* 	[add admin page options]
	*
	*
	*
	********************************************************************************************************************************************************/

	/*------------------------------------------------------------------------------
	#
	#
	#	[settings page]
	#
	#
	------------------------------------------------------------------------------*/

	/*********************************************************
			[add section to a particular tab]
	*********************************************************/
	function admin_tabs($tabs){
		$tabs['tab'][$this->tab_key]['sections'][] = $this->section_key;
	return $tabs;
	}
	/*------------------------------------------------------------------------------
	#	[settings section - setting page]
	#	@since 3.6
	#	@return array()
	------------------------------------------------------------------------------*/
	function admin_options_settings($settings, $sections, $fields, $inputs, $help){
		global $wp_version;
		$version_notice = version_compare( $wp_version, '4.9.6', '<' ) ? '<br><span class="wppizza-highlight">'.__('Requires Wordpress 4.9.6+', 'wppizza-admin').'</span>' : '' ;

		/*section*/
		if($sections){
			$settings['sections'][$this->section_key] =  __('Erase / Anonymise', 'wppizza-admin');
		}

		/*help*/
		if($help){
		}

		/*fields*/
		if($fields){

			$field = 'privacy_erase';
			$settings['fields'][$this->section_key][$field] = array( __('Erase/Anonymise', 'wppizza-admin'), array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=> __('Select the actions that should be taken depending on the status of an order associated with a user when a request for data erasure has been made', 'wppizza-admin') . ' ' . $version_notice,
				'description'=>array(
					'<b>'.__('Note: An order is considered abandoned if it has not been completed within 24 hours of adding items to the cart', 'wppizza-admin').'</b>',
				)
			));


		}
	return $settings;
	}
	/*------------------------------------------------------------------------------
	#	[output option fields - setting page]
	#	@since 3.6
	#	@return array()
	------------------------------------------------------------------------------*/
	function admin_options_fields_settings($wppizza_options, $options_key, $field, $label, $description){


		if($field=='privacy_erase'){
			echo "<table id='".WPPIZZA_PREFIX."_".$field."'>";

			echo "<thead>";
			echo "<tr>";
				echo "<th colspan='8'>";
					echo $label;
				echo "</th>";
			echo "</tr>";
			echo "</thead>";

			echo "<tfoot>";
			echo "<tr>";
				echo "<th colspan='8'>";
					echo $description;
				echo "</th>";
			echo "</tr>";
			echo "</tfoot>";

			echo "<tbody>";
			foreach($this->privacy_erase_setup['actions'] as $action_key => $action_vals){

				echo "<tr>";

					echo "<td>";
						echo "<label><b>".$action_vals['label'].":</b> </label>";
					echo "</td>";

					foreach($this->privacy_erase_setup['type'] as $type_key => $type_vals){
						echo "<td>";
							echo "<label>";
								echo "".$type_vals['label']."";
								echo "<input id='".$field."' name='".WPPIZZA_SLUG."[".$options_key."][".$field."][".$type_key."]' type='radio' ". checked($wppizza_options[$options_key][$field][$type_key], $action_key, false)." value='".$action_key."' />";
							echo "</label>";
						echo "</td>";
					}

				echo "<tr>";
			}
			echo "</tbody>";
			echo "</table>";
		}

	}

	/****************************************************************
	*
	*	[insert default option on install]
	*	$parameter $options array() | filter passing on filtered options
	*	@since 3.6
	*	@return array()
	*
	****************************************************************/
	function options_default($options, $install){
		global $wppizza_options;

		/* default erase options */
		$options[$this->settings_page]['privacy_erase'] = $this->privacy_erase_defaults;


		return $options;
	}

	/*------------------------------------------------------------------------------
	#	[validate options on save/update]
	#
	#	@since 3.6
	#	@return array()
	------------------------------------------------------------------------------*/
	function options_validate($options, $input){
		/**make sure we get the full array on install/update**/
		if ( empty( $_POST['_wp_http_referer'] ) ) {
			return $input;
		}

		/********************************
		*	[validate]
		********************************/
		if(isset($_POST[''.WPPIZZA_SLUG.'_'.$this->settings_page.'_'.$this->tab_key.''])){
			$options[$this->settings_page]['privacy_erase'] = wppizza_validate_array($input[$this->settings_page]['privacy_erase']);
		}
	return $options;
	}

}
/***************************************************************
*
*	[ini]
*
***************************************************************/
$WPPIZZA_MODULE_TOOLS_PRIVACY_ERASE = new WPPIZZA_MODULE_TOOLS_PRIVACY_ERASE();
?>