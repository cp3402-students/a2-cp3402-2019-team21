<?php
/**
* WPPIZZA_GATEWAYS Class
*
* @package     WPPIZZA
* @subpackage  WPPIZZA_GATEWAYS
* @copyright   Copyright (c) 2015, Oliver Bach
* @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
* @since       3.0
*
*/
if ( ! defined( 'ABSPATH' ) ) exit;/*Exit if accessed directly*/


/************************************************************************************************************************
*
*
*	WPPIZZA_GATEWAYS
*
*
************************************************************************************************************************/
class WPPIZZA_GATEWAYS{

	public $must_recalculate = false;

	public $gwobjects;

	function __construct() {

		/** ini gateways **/
		add_action('init', array($this, 'ini_gateways'), 9);/* should be lower than priority 10 so we can use default priorities in gateways */

		if( !is_admin() || (is_admin() && defined( 'DOING_AJAX' ) && DOING_AJAX ) ){
			global $wppizza_options;


			/** charge/cancel on return/redirect to thank you page by gateway */
			add_action('init', array($this, 'process_transactions'), 11);// priority must be > 10 for do_action hook added by this function to fire in a gateway

			/***
				enqueue any gateway scripts and styles we need BEFORE main wppizza
				@since 3.7
			***/
			add_action('wp_enqueue_scripts', array( $this, 'wppizza_gateways_enqueue_pre_scripts_and_styles'), ($wppizza_options['layout']['css_priority']-1));

			/*** enqueue gateway scripts and styles ***/
			add_action('wp_enqueue_scripts', array( $this, 'wppizza_gateways_enqueue_acripts_and_styles'), $wppizza_options['layout']['css_priority']);
		}
	}


	/******************************************************************
		respond to webhook (aka IPN) or payments charge action hook

		@param void
		@since 3.7
		@return void
	******************************************************************/
	function process_transactions(){
		/*
			skip on all admin (including ajax )requests
		*/
		if(is_admin()){return;}

		/*
			check for webhook listener parameter
		*/
		if(!empty($_GET[WPPIZZA_LISTENER_PARAMETER])){

			/* sanitoze, just for good measure */
			$webhook = strtolower(wppizza_alpha_only($_GET[WPPIZZA_LISTENER_PARAMETER]));

			/* sanitize request vars */
			$request_vars = array();
			/* sanitize any get vars */
			if(!empty($_GET)){
				$get_vars = wppizza_sanitize_post_vars($_GET);
				$request_vars += $get_vars;
			}
			/* sanitize any post vars */
			if(!empty($_POST)){
				$post_vars = wppizza_sanitize_post_vars($_POST);
				$request_vars += $post_vars;
			}

			// retrieve the request's body and parse it as JSON
			$body = @file_get_contents( 'php://input' );
			$transaction_details = json_decode( $body );


			/*
				add webhook/listener do_action hook
				passing on sanitized get/post parameters
			*/
			do_action('wppizza_gateways_listener_'.$webhook.'', $transaction_details , $request_vars);

		return;
		}
		/*
			check for transaction hash parameter
		*/
		if(!empty($_GET[WPPIZZA_TRANSACTION_GET_PREFIX])){


			/*
				run query, restricted to current site, last 24 hours, hash and INPROGRESS status
			*/
			$args = array();
			$args['query']['hash'] = wppizza_sanitize_hash($_GET[WPPIZZA_TRANSACTION_GET_PREFIX]);
			$args['query']['payment_status'] = 'INPROGRESS' ;
			$args['query']['blogs'] = false ;
			$args['query']['order_date_after'] = (WPPIZZA_WP_TIME - 86400);// current minus 24 hours to restrict it a bit
			$orders = wppizza_get_orders($args, 'process_transactions');
			/*
				make sure that there's one - and only one - order returned
				else skip
			*/
			if($orders['total_number_of_orders'] != 1){
				return;
			}

			/*
				reset to get data of this first/only applicable order
			*/
			$order_data = reset($orders['orders']);

			/*******************************************
				add a do action hook of gateway used
				passing on order id, order data and any request vars
			********************************************/
			/* order id */
			$order_id = $order_data['ordervars']['order_id']['value'];
			/* order_amount */
			$order_amount = $order_data['ordervars']['total']['value'];
			/* order_currency */
			$order_currency = $order_data['ordervars']['currency']['value'];

			/* gateway ident */
			$gateway_used = strtolower($order_data['ordervars']['payment_gateway']['value']);

			/* sanitize request vars */
			$request_vars = array();
			/* sanitize any get vars */
			if(!empty($_GET)){
				$get_vars = wppizza_sanitize_post_vars($_GET);
				$request_vars += $get_vars;
			}
			/* sanitize any post vars */
			if(!empty($_POST)){
				$post_vars = wppizza_sanitize_post_vars($_POST);
				$request_vars += $post_vars;
			}

			/*
				add gateway do_action hook
			*/
			do_action('wppizza_gateways_process_transaction_'.$gateway_used.'', $order_id, $order_amount, $order_currency, $order_data,  $request_vars );

		return;
		}
	return;
	}
	/******************************************************************

		initialize user session gateways with first available

	******************************************************************/
	function session_ini(){
		global $wppizza_options;
		if(empty($wppizza_options)){return ;}
	    /* ini as false */
	    $ini = false;
	    /* get all enabled gateways from options */
	    $gateways_enabled = !empty($wppizza_options['gateways']) ? $wppizza_options['gateways'] : array();
	    /* if there is one, use key as ini value */
	    if(count($gateways_enabled)>0){
	    	/* get first available */
	    	reset($gateways_enabled);
			$ini = key($gateways_enabled);
	    }

		return $ini;
	}

	/******************************************************************
		ini enabled gateways
		and check if any have handling charges or discounts set
		to enable forced recalculation on checkout
	******************************************************************/
	function ini_gateways(){
		global $wppizza_options;


	    /*
	    	get all enabled gateways from wppizza options
	    */
	    $gateways_enabled = !empty($wppizza_options['gateways']) ? $wppizza_options['gateways'] : array();
	    $gateways_enabled = apply_filters('wppizza_filter_gateways_orderpage', $gateways_enabled) ;
	    /*
	    	if there is one, use key as ini value
	    */
	    if(count($gateways_enabled)>0){

	    	/*
	    		ini gwobjects
	    	*/
	    	$this->gwobjects = new stdClass();


	    	/*
	    		loop through gateway classes
	    	*/
			foreach($gateways_enabled as $ident=>$val){

				/*
					make gateway class name
				*/
				$gateway_classname = 'WPPIZZA_GATEWAY_'.$ident.'';

				/*
					lowercase ident
				*/
				$gateway_ident_lowercase  = strtolower($ident);

				/*
					make gateway options name
				*/
				$gateway_options_name  = 'wppizza_gateway_'.$gateway_ident_lowercase.'';

				/*
					instanciate gateway class
				*/
				if (class_exists($gateway_classname)){


					$gw = new $gateway_classname;
					//$gw -> gatewayIdent = $ident;
					$gw -> gatewayIdent = $gateway_ident_lowercase;

					/*
						get set options of gateway
					*/
					$gateway_options = $gw->gatewayOptions;


					$this->gwobjects->$ident = new stdClass();
					$this->gwobjects->$ident->version = $gw->gatewayVersion;
					$this->gwobjects->$ident->gateway_ident = $ident;
					$this->gwobjects->$ident->label = $gw->gatewayOptions['_gateway_label'];
					$this->gwobjects->$ident->additional_info = $gw->gatewayOptions['_gateway_additional_info'];
					$this->gwobjects->$ident->logo = $gw->gatewayOptions['_gateway_logo'];
					$this->gwobjects->$ident->button = $gw->gatewayOptions['_gateway_button'];
					$this->gwobjects->$ident->gateway_type = ($gw->gatewayType == 'cod') ? 'cod' : 'prepay' ;/*cash on delivery or prepay(cc) */
					$this->gwobjects->$ident->gateway_settings = (method_exists($gw, 'gateway_settings')) ? $gw->gateway_settings($ident, $gw->gatewayOptions, $gateway_options_name ) : array();
					$this->gwobjects->$ident->gateway_refunds =  !empty($gw->gatewayRefunds) ? true : false ;
					$this->gwobjects->$ident->gateway_refunds_method = !empty($gw->gatewayRefunds) ? $gw->gatewayRefunds : false ;
					$this->gwobjects->$ident->gateway_options = $gw->gatewayOptions ;/* get set options */					
					$this->gwobjects->$ident->gatewayForceReload = !empty($gw->gatewayOptions) ? true : false ;/* reload / must_recalculate forced ? */


					/*
						get surcharges or discounts defined for this gateway
						if discounts or surcharges are set set must_recalculate to be true
					*/
					if(
						!empty($gateway_options['_gateway_surcharge_percent']) ||
						!empty($gateway_options['_gateway_surcharge_fixed']) ||
						!empty($gateway_options['_gateway_discount_percent']) ||
						!empty($gateway_options['_gateway_discount_fixed']) ||
						!empty($this->gwobjects->$ident->gatewayForceReload) /*force reloading of order page */
					){

						$this->gwobjects->$ident->surcharges['percent'] = !empty($gateway_options['_gateway_surcharge_percent']) ? $gateway_options['_gateway_surcharge_percent'] : 0;
						$this->gwobjects->$ident->surcharges['fixed'] = !empty($gateway_options['_gateway_surcharge_fixed']) ? $gateway_options['_gateway_surcharge_fixed'] : 0;
						$this->gwobjects->$ident->discounts['percent'] = !empty($gateway_options['_gateway_discount_percent']) ? $gateway_options['_gateway_discount_percent'] : 0;
						$this->gwobjects->$ident->discounts['fixed'] = !empty($gateway_options['_gateway_discount_fixed']) ? $gateway_options['_gateway_discount_fixed'] : 0;
						$this->gwobjects->$ident->min_order_value = !empty($gateway_options['_gateway_discount_min_order']) ? $gateway_options['_gateway_discount_min_order'] : 0;

						/*
							set flag that we must recalculate when changing gateways
							this will reload the order page
						*/
						$this->must_recalculate = true;
					}


					/*
						using button image if only one gateway selected
					*/
					if(!empty($this->gwobjects->$ident->button) && count($gateways_enabled)==1){
						$image_url = $this->gwobjects->$ident->button;
						add_filter('wppizza_filter_submit_as_image', function( $content ) use($image_url) {
							return $image_url;
						});
					}
				}
			}

	    	/*
	    		below filter currently used to add WPML compatibility
	    	*/
	    	$this->gwobjects = apply_filters('wppizza_filter_gateway_objects', $this->gwobjects);
	    }
	return;
	}
	/******************************************************************

		get buttons. dropdown or value
		if $selected == true, we are on confirmation page
	******************************************************************/
	function markup($selected = false){


			global $wppizza_options;


			/*
				ini localized script
			*/
			$localized_script = '';

			/*
				get user data
			*/
			$user_data = WPPIZZA() -> session -> get_userdata();

			/*
				get order data
				unsetting blog options (for sanity)
			*/
			$order_data = WPPIZZA()->order->session_formatted();
			unset($order_data['blog_options']);


			/* distinctly selected */
			if(!$user_data && $selected){
				$selected_gateway = $selected;
			}else{
				$session_gateway_ident = ''.WPPIZZA_SLUG.'_gateway_selected';/* must be equivalent to user session key for selected gateways */
				$selected_gateway = $user_data[$session_gateway_ident];
			}
			/*
				only get selected gateway name/label (confirmationpage)
				$ident must be passed
				also add any localized gw script and styles
			*/
			if($selected){
				$markup = !empty($this->gwobjects->$selected_gateway) ? $this->gwobjects->$selected_gateway->label : '';

				/***
					add selected gateway as hidden input
				***/
				$markup .= '<input type="hidden" name="'.$session_gateway_ident.'" value="'.$selected_gateway.'" />';

				/**
					localized gateway javascript/css
					IF using confirmation form
				**/
				$localized_script = apply_filters('wppizza_filter_gateways_localize_scripts_'.strtolower($selected_gateway).'', $localized_script ,  $order_data,  $user_data,  $selected );
				$markup .= $localized_script;

			return $markup;
			}


			if(!$selected){
				$markup = '';
				/* no extra sorting needed as sorted on save */
				if(!empty($this->gwobjects)){

					/*
						@since 3.2.5
						allow more succinct filtering of payment options
					*/
					$this->gwobjects = apply_filters('wppizza_filter_gateways_payment_options', $this->gwobjects, $order_data,  $user_data);

					/*
						@since 3.2.5
						make sure we also filter $wppizza_options['gateways'] depending on  $this->gwobjects as the filter might have disabled some
					*/
					$s = 0;
					foreach($wppizza_options['gateways'] as $gwObjId => $gwObj){
						if(!isset($this->gwobjects->$gwObjId)){
							unset($wppizza_options['gateways'][$gwObjId]);
						}else{
							if($s == 0 ){
								/* make sure we have a backup selection if the prevuiously selected gateway was removed by the filter*/
								$backup_gateway	= $gwObjId;
							$s++;
							}
						}

					}
					/*
						@since 3.2.5
						if we have removed a gateway with the filter above that was previously selected by the customer
						let's select the first of the leftover ones
					*/
					if(empty($wppizza_options['gateways'][$selected_gateway]) && !empty($backup_gateway)){
						$selected_gateway = $backup_gateway;
					}





					$number_of_gateways = count(get_object_vars($this->gwobjects));

					/*
						using dropdown , add as select
					*/
					if( $number_of_gateways > 1 && !empty($wppizza_options['layout']['gateway_select_as_dropdown'])){
						$markup .= '<select id="'.$session_gateway_ident.'" name="'.$session_gateway_ident.'" class="'.WPPIZZA_PREFIX.'-gateway-select">';
					}

					foreach($this->gwobjects as $ident => $val){

						$key = strtolower($ident);

						/**
							show choices of more than one
						**/
						if( $number_of_gateways > 1 ){

							/*
								using dropdown or buttons
							*/
							if(!empty($wppizza_options['layout']['gateway_select_as_dropdown'])){
								$markup .= '<option value="'.$ident.'" '.selected($selected_gateway, $ident ,false).' >'.$val->label.'</option>';
							}else{
								/*
									add some markup before button if required
								*/
								$markup = apply_filters('wppizza_gateway_button_prepend_'.$key.'', $markup, $order_data, $user_data, $selected );

								$markup .= '<div id="'.WPPIZZA_PREFIX.'-gateway-'.$key.'" class="'.WPPIZZA_PREFIX.'-gateway-button button">';

									$markup .= '<label>';

										/*
											radio
										*/
										$markup .= '<input type="radio" id="'.$session_gateway_ident.'_'.$key.'" name="'.$session_gateway_ident.'" value="'.$ident.'" '.checked($selected_gateway, $ident, false).' />';

										/*
											logo
										*/
										if(!empty($val->logo)){
											$markup .= '<img src="'.$val->logo.'" class="'.WPPIZZA_PREFIX.'-gateway-img" id="'.WPPIZZA_PREFIX.'-gateway-img-'.$ident.'" />';
										}

										/*
											text/label
										*/
										$markup .= ' '.$val->label.' ';

									$markup .= '</label>';

									/*
										additional info
									*/
									if($val->additional_info!=''){
										$markup .= '<span class="'.WPPIZZA_PREFIX.'-gateway-addinfo">'.$val->additional_info.'</span>';
									}

								$markup .= '</div>';

								/*
									add some markup after button if required
								*/

								$markup = apply_filters('wppizza_gateway_button_append_'.$key.'', $markup, $order_data, $user_data, $selected );

							}

						}
						/**
							if there's only one, just add hidden input and button (if set )
						**/
						else{
							$markup .= '<input type="hidden" name="'.$session_gateway_ident.'" value="'.$ident.'" />';
						}

						/**
							localized gateway javascript/css
							if *not* using confirmation form
						**/
						if(empty($wppizza_options['confirmation_form']['confirmation_form_enabled'])){
							$localized_script = apply_filters('wppizza_filter_gateways_localize_scripts_'.$key.'', $localized_script, $order_data, $user_data, $selected );
						}

					}

					/*
						using dropdown , add as select
					*/
					if( $number_of_gateways > 1 && !empty($wppizza_options['layout']['gateway_select_as_dropdown'])){
						$markup .= '</select>';
					}
				}
				/**
					add localized script after buttons/dropdown
				**/
				$markup .= $localized_script;

			}
		return $markup;
	}


	/******************************************************************

		enqueue gateway scripts and style by filter that are needed BEFORE
		main wppizza js
		@since 3.7
	******************************************************************/
	function wppizza_gateways_enqueue_pre_scripts_and_styles(){
		/** only enqueue on checkout page **/
		if(!wppizza_is_checkout()){return;}

		/*
			add script for gateways that allow inline payments
			when this particular gateway has been selected
		*/
		$selected_gateway_ident = WPPIZZA()->session->get_selected_gateway();
		do_action('wppizza_checkout_pre_enqueue_'.$selected_gateway_ident.'', $selected_gateway_ident);

	}

	/******************************************************************

		enqueue gateway scripts and style by filter

	******************************************************************/
	function wppizza_gateways_enqueue_acripts_and_styles(){

		/** only enqueue on checkout page **/
		if(!wppizza_is_checkout()){return;}

		/*
			get scripts/styles to enqueue by filter
		*/
		if(!empty($this->gwobjects)){
		foreach($this->gwobjects as $ident => $val){

			/* cast ident to lowercase */
			$key = strtolower($ident);

			/* apply filters */
			$scripts = apply_filters('wppizza_filter_gateways_enqueue_scripts_'.$key.'', array());

			/*
				enqueue scripts/styles
			*/
			if(!empty($scripts)){
			foreach($scripts as $scriptKey => $script){

				/* enqueue css - one only per gateway */
				if($script['type'] == 'stylesheet'){
					$enqueue_key = WPPIZZA_SLUG.'-gw-'.$key.'';
					wp_register_style($enqueue_key , $script['url'] , array(), $val->version);
					wp_enqueue_style($enqueue_key);
				}
				/* enqueue javascript */
				if($script['type'] == 'javascript'){
					$enqueue_key = WPPIZZA_SLUG.'-gw-js-'.$key.'-'.$scriptKey.'';
					wp_register_script($enqueue_key, $script['url'] , array(WPPIZZA_SLUG), $val->version, apply_filters('wppizza_filter_js_in_footer', false));
					wp_enqueue_script($enqueue_key);

				}
			}}
		}}
	}
}
?>