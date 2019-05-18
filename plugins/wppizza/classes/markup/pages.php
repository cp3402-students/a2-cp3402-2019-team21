<?php
/**
* WPPIZZA_MARKUP_PAGES Class
*
* @package     WPPIZZA
* @subpackage  WPPizza Pages Markup
* @copyright   Copyright (c) 2015, Oliver Bach
* @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
* @since       3.0
*
*/
if ( ! defined( 'ABSPATH' ) ) exit;/*Exit if accessed directly*/

/* ================================================================================================================================= *
*
*
*
*	CLASS - WPPIZZA_MARKUP_PAGES
*
*
*
* ================================================================================================================================= */

class WPPIZZA_MARKUP_PAGES{

	/******************************************************************************
	*
	*
	*	[construct]
	*
	*
	*******************************************************************************/
	function __construct() {

		/* get title and order details on thank you page */
		add_action('wp', array($this, 'thankyou_order_info'));

		/** add some localized js variables (validation rules)*/
		add_filter('wppizza_filter_js_localize',array($this, 'localize_js_orderpage_validation_rules'));

		/** thank you page, replace title/content **/
		add_filter('the_title', array($this, 'thankyoupage_title'), 10, 2);
		add_filter('the_content', array($this, 'thankyoupage_content'), 10, 2);
	}

	/******************************************************************************
	*
	*
	*	[methods]
	*
	*
	*******************************************************************************/
	/***************************************
		[get order details once to display right title and info
		on thank you page (instead of running the query 2x) ]
	***************************************/
	function thankyou_order_info($param, $type = 'thankyoupage'){
		global $post, $wppizza_options;


		/*
			get relevant title and content on thank you page
		*/
		if(is_object($post) && $wppizza_options['order_settings']['orderpage'] == $post->ID && ( isset($_GET[WPPIZZA_TRANSACTION_GET_PREFIX]) || isset($_GET[WPPIZZA_TRANSACTION_CANCEL_PREFIX]) ) ){

			/*
				get order from transaction hash
			*/
			if(isset($_GET[WPPIZZA_TRANSACTION_GET_PREFIX])){

				/****************************************
					set the transaction order  query args
				****************************************/
				$args = array(
					'query' => array(
						'hash' => wppizza_sanitize_hash($_GET[WPPIZZA_TRANSACTION_GET_PREFIX]) ,
						'payment_status' => array('COMPLETED', 'FAILED', 'INPROGRESS', 'AUTHORIZED', 'CAPTURED', 'CANCELLED', 'UNCONFIRMED', 'CONFIRMED', 'REJECTED', 'REFUNDED', 'PAYMENT_PENDING' ),
					),
				);

			}

			/*
				get order from cancel prefix hash
			*/
			if(isset($_GET[WPPIZZA_TRANSACTION_CANCEL_PREFIX])){
				/****************************************
					set the cancel order query args
				****************************************/
				$args = array(
					'query' => array(
						'hash' => wppizza_sanitize_hash($_GET[WPPIZZA_TRANSACTION_CANCEL_PREFIX]) ,
						'payment_status' => array('COMPLETED', 'FAILED', 'INPROGRESS', 'AUTHORIZED', 'CAPTURED', 'CANCELLED', 'UNCONFIRMED'),
					),
				);

			}

			/*
				add formatting arguments, run query and restun results
			*/
			if(isset($_GET[WPPIZZA_TRANSACTION_GET_PREFIX]) || isset($_GET[WPPIZZA_TRANSACTION_CANCEL_PREFIX]) ){

				/*
					adding class idents, distinct secion and adding all blog options
				*/
				$args['format'] = array(
					'blog_options' => array('blog_options'),// add some additional - perhaps useful - info to pass on to gateways
					'sections' => true,//leave order sections in its distinct [section] array to use in templates generation
				);

				/*************************************************
					run query, and get results
					even single order results are always arrays
					so simply use reset here
				*************************************************/
				$results_formatted = WPPIZZA() -> db -> get_orders($args, $type);
				$results_formatted = reset($results_formatted['orders']);
			}


			/*
				cast payment status to uppercase for legacy reasons
				as it will also be passed on to
				$this -> thankyoupage_results_formatted
			*/
			if(!empty($results_formatted)){

				$results_formatted['sections']['ordervars']['payment_status']['value'] = strtoupper($results_formatted['sections']['ordervars']['payment_status']['value']);
				$payment_status = strtoupper($results_formatted['sections']['ordervars']['payment_status']['value']);

				/********************
					add action hook that can be used
					to - for example - alter localizations
					only run whene there are actually results
				********************/
				do_action('wppizza_thankyou_order_info', $payment_status, $results_formatted);
			}


			/********************
				get/set page title
			********************/

			/* generic | unknown | failed */
			$this -> thankyoupage_title = $wppizza_options['localization']['generic_error_label'];


			/* cancelled */
			if((!empty($payment_status) && $payment_status == 'CANCELLED') || isset($_GET[WPPIZZA_TRANSACTION_CANCEL_PREFIX]) ){
				$this -> thankyoupage_title = $wppizza_options['localization']['order_cancelled'];
			}

			/* payment pending */
			if(!empty($payment_status) && $payment_status == 'PAYMENT_PENDING'){
				$this -> thankyoupage_title = $wppizza_options['localization']['order_payment_pending'];
			}

			/* in progress/captured - empty title as it's already displayed in the body*/
			if(!empty($payment_status) && in_array( $payment_status, array('INPROGRESS', 'CAPTURED') )){
				$this -> thankyoupage_title = '&nbsp;';
			}

			/* unconfirmed */
			if(!empty($payment_status) && $payment_status == 'UNCONFIRMED'){
				$this -> thankyoupage_title = $wppizza_options['localization']['order_unconfirmed'];
			}

			/* confirmed, not yet executed */
			if(!empty($payment_status) && $payment_status == 'CONFIRMED'){
				$this -> thankyoupage_title = apply_filters('wppizza_filter_page_confirmed_title', '' , $results_formatted);
			}

			/* rejected */
			if(!empty($payment_status) && $payment_status == 'REJECTED'){
				$this -> thankyoupage_title = apply_filters('wppizza_filter_page_rejected_title', '' , $results_formatted);
			}

			/* completed */
			if(!empty($payment_status) && $payment_status == 'COMPLETED'){
				$this -> thankyoupage_title = $wppizza_options['localization']['thank_you'];
			}

			/* refunded */
			if(!empty($payment_status) && $payment_status == 'REFUNDED'){
				$this -> thankyoupage_title = apply_filters('wppizza_filter_page_refunded_title', '' , $results_formatted);
			}

			/* specifically set to error */
			if(isset($_GET['e'])){
				$this -> thankyoupage_title = $wppizza_options['localization']['generic_error_label'];
			}

			/********************
				get/set order results formatted
			********************/
			$this -> thankyoupage_results_formatted = !empty($results_formatted) ? $results_formatted : false;
		}


	return;
	}

	/***************************************
		[change title in thank you page after order]
	***************************************/
	function thankyoupage_title($title, $post_id = null) {
		global $wppizza_options;
		$orderpage_id = $wppizza_options['order_settings']['orderpage'];

		if($orderpage_id == $post_id && is_page( $post_id ) && !empty($this -> thankyoupage_title)){
			$title = $this -> thankyoupage_title;
		}

	return $title;
	}

	/***************************************
		[change content in page of thank you page after order
		- if $_GET['wpptx'] or $_GET['wppcltx']   ]
	***************************************/
	function thankyoupage_content($content){
		global $wppizza_options, $post;
		$orderpage_id = $wppizza_options['order_settings']['orderpage'];

		if ( $orderpage_id == $post->ID && is_page( $post->ID )  && isset($_GET[WPPIZZA_TRANSACTION_GET_PREFIX]) ) {
			$content = self::markup('thankyoupage');
	    }

		if ( $orderpage_id == $post->ID && is_page( $post->ID )  && isset($_GET[WPPIZZA_TRANSACTION_CANCEL_PREFIX]) ) {
			$content = self::markup('ordercancelled');
	    }

	return $content;
	}

	/***************************************
		[add some localized js variables for validations]
		orderpage only
	***************************************/
	function localize_js_orderpage_validation_rules($localize){
		/* bail if not orderpage and no orderpage widget*/
		if(!wppizza_is_orderpage() && !wppizza_has_orderpage_widget()){
			return $localize;
		}

		global $wppizza_options;

		/*****************************
			validation rules, as set in formfields
		*****************************/
		$validate_rules = array();
		/** forced to orderpage (because it is parameter) */
		foreach(WPPIZZA()->helpers->enabled_formfields(false, true, false) as $key=>$value){
			$rules=$value['validation'];
			foreach($rules as $rule_key=>$rule_value)
			if($rule_key!='default'){
				$validate_rules[$key][$rule_key] = $rule_value;
			}
		}
		/*
			add to localized script
		*/
		$localize['validate']['rules'] = $validate_rules;

		/*****************************
			validation error messages
		*****************************/
		$validate_error['email'] = $wppizza_options['localization']['required_field_email'];
		$validate_error['required'] = $wppizza_options['localization']['required_field'];
		$validate_error['decimal'] = $wppizza_options['localization']['required_field_decimal'];
		/* decode any entities */
		foreach($validate_error as $jsmKey => $jsMessage){
			$validate_error[$jsmKey] = wppizza_decode_entities($jsMessage);
		}

		/*
			add to localized script
		*/
		$localize['validate']['error'] = $validate_error;

	return	$localize;
	}
	/***************************************
		[apply attributes]
	***************************************/
	function markup($type, $atts = null, $is_admin = false){
		global $wppizza_options;

		/* merge localization */
		$txt = $wppizza_options['confirmation_form']['localization'] + $wppizza_options['localization'];

		/* get session user data */
		$user_session = WPPIZZA()->session->get_userdata();

		/**get markup**/
		if($type == 'orderpage'){
			$markup = self::order_page($type, $atts, $txt, $user_session);
		}

		/**get markup**/
		if($type == 'confirmationpage'){
			$markup = self::confirmation_page($type, $atts, $txt, $user_session);
		}

		/**get markup**/
		if($type == 'thankyoupage'){
			$markup = self::thankyou_page($type, $atts, $txt, $user_session);
		}

		/**get markup**/
		if($type == 'ordercancelled'){
			$markup = self::cancelled_page($type, $atts, $txt, $user_session);
		}

		/**get markup (users order history)**/
		if($type == 'orderhistory'){
			$markup = self::orderhistory_page($type, $atts, $txt, $user_session);
		}

		/**get markup (admin, full order history)**/
		if($type == 'admin_orderhistory' && $is_admin === true){
			$markup = self::admin_orderhistory_page($type, $atts, $txt, $user_session);
		}

		/**get markup (admin_dashboard_widget)**/
		if($type == 'admin_dashboard_widget' && $is_admin === true){
			$markup = self::admin_dashboard_widget_page($type, $atts, $txt, $user_session);
		}

		return $markup;
	}

	/***************************************

		[orderpage]

	***************************************/
	function order_page($type, $atts , $txt, $user_session){
		global $wppizza_options;

		/****************************************
			set the order query args
			get order details form session hash and get formatted output
		****************************************/
		$args = array(
			'query' => array(
				'hash' => $user_session['wppizza_hash'] ,
				'payment_status' => array('INITIALIZED'),
			),
			'format' => array(
				'blog_options' => array('checkout_parameters', 'blog_options'),// add some additional info
				'sections' => true,
			),
		);
		/*************************************************
			run query, and get results
			even single order results are always arrays
			so simply use reset here
		*************************************************/
		$order_results = WPPIZZA() -> db -> get_orders($args, $type);
		$order_results = reset($order_results['orders']);


		/* show formfields from session as customer_ini might not have been set yet */
		$order_formatted = $order_results['sections'];/* for consistency with carts etc */
		$order_formatted['checkout_parameters'] = $order_results['checkout_parameters'];
		$cart_empty = empty($order_formatted['order']['items']) ? true :  false ;
		$shop_open = wppizza_is_shop_open();
		$can_checkout = !empty($order_formatted['checkout_parameters']['can_checkout']) ? true : false;
		$nocart  = !empty($atts['nocart']) ?  true : false;
		//$is_widget  = !empty($atts['is_widget']) ?  true : false;/* not really in use here, but might be useful one day */


		/*
			ids'|classes
		*/
		$id = ''.WPPIZZA_PREFIX.'-order-wrap-'.$type.'';
		$formid = ''.WPPIZZA_PREFIX.'-send-order';
		$class = ''.WPPIZZA_PREFIX.'-order-wrap';
		if(!empty($order_formatted['checkout_parameters']['is_pickup'])){
		$class .= ' '.WPPIZZA_PREFIX.'-order-ispickup';
		}


		$class_shop_closed = ''.WPPIZZA_PREFIX.'-fieldset '.WPPIZZA_PREFIX.'-shop-closed';
		$class_cart_empty = ''.WPPIZZA_PREFIX.'-fieldset '.WPPIZZA_PREFIX.'-cart-empty';

		$order_fieldset_id = ''.WPPIZZA_PREFIX.'-order-details';
		$order_fieldset_class = ''.WPPIZZA_PREFIX.'-fieldset '.WPPIZZA_PREFIX.'-order-details';

		$personal_fieldset_id = ''.WPPIZZA_PREFIX.'-personal-details';
		$personal_fieldset_class = ''.WPPIZZA_PREFIX.'-fieldset '.WPPIZZA_PREFIX.'-personal-details';

		$payment_methods_fieldset_id = ''.WPPIZZA_PREFIX.'-payment-methods';
		$payment_methods_fieldset_class = ''.WPPIZZA_PREFIX.'-fieldset '.WPPIZZA_PREFIX.'-payment-methods';


		/**************************************
			login form - will never get here if no items in cart anyway
		**************************************/
		$login_form = WPPIZZA()->user->login_form();// uses markup/global/login.php

		/**************************************
			order
		**************************************/
		if(!$nocart){/* if cart not disabled by 'nocart=1|true' in shortcode/widget */
			/*
				itemised items table
			*/
	  		$order_details_itemised = WPPIZZA() -> markup_maincart -> itemised_markup($order_formatted, $type);

			/*
				subtotals/summary table
			*/
			$order_details_summary = WPPIZZA() -> markup_maincart -> summary_markup($order_formatted, $type);
			/*
				pickup / delivery note
			*/
			$order_details_pickup_note = self::pickup_note_markup($order_formatted, $type);
			/*
		  		pickup checkbox/toggle
			*/
			$order_details_pickup_choices = WPPIZZA() -> markup_pickup_choice -> attributes($atts, $type);
		}else{
			/** avoid php notices **/
			$order_details_itemised = '';
			$order_details_summary = '';
			$order_details_pickup_note = '';
			$order_details_pickup_choices = '';

		}
		/*************************************
			personal_details
		*************************************/
		/*
			user input fields
		*/
		$personal_details = WPPIZZA()->user->formfields_inputs($order_formatted['customer']);
		/*
			user update info - within fieldset. might be an empty string if certain conditions are not met
		*/
		$user_profile = WPPIZZA()->user->profile_options();


		/**************************************
			gateways buttons
		**************************************/
		$payment_methods ='';
		if($can_checkout){/* will also have checked if there are any gateways enabled */
			$payment_methods = WPPIZZA()->gateways->markup();
		}


		/**************************************
			inline payment details - confirmation page not enabled
		**************************************/
		$payment_details = '';
		if($can_checkout){/* will also have checked if there are any gateways enabled */
			$payment_details = self::payment_details_markup($type);
		}


		/**************************************
			submit button provided one can actually checkout
		**************************************/
		$submit_button = '';
		if($can_checkout){
			$button_class = array();
			$button_class[] = 'submit';
			$button_class[] = !empty($wppizza_options['confirmation_form']['confirmation_form_enabled']) ? ''.WPPIZZA_PREFIX.'-confirm-order' : ''.WPPIZZA_PREFIX.'-ordernow';/** are we using a confirmation form too ?**/
			$button_class = implode(' ', $button_class);


			/*
				a generic - but empty - error div right before the submit button one can write any errors to if needed
				@since v3.7
			*/
			$submit_button .= '<div role="alert" id="wppizza-submit-error" class="wppizza-validation-error"></div>';
			/**
				use image instead of button
			**/
			$submit_image = apply_filters('wppizza_filter_submit_as_image', '');
			if(!empty($submit_image)){
				$submit_button .='<div id="'.WPPIZZA_PREFIX.'-ordernow" class="'. $button_class .' '.WPPIZZA_PREFIX.'-ordernow-img" title="'.$txt['send_order'].'" >';
					$submit_button .='<input type="image" src="'.$submit_image.'" border="0" alt="'.$txt['send_order'].'" />';
				$submit_button .='</div>';
			}else{
				$submit_button .='<input id="'.WPPIZZA_PREFIX.'-ordernow" class="'. $button_class .'" type="submit" style="display:block" value="'.$txt['send_order'].'" />';
			}
			/* add nonce */
			$submit_button .= ''.wp_nonce_field( '' . WPPIZZA_PREFIX . '_nonce_checkout','' . WPPIZZA_PREFIX . '_nonce_checkout',true,false).'';



			/* add cache buster input - in conjunction with js force reloading order page when backpaging after cod order */
			$submit_button .='<input id="'.WPPIZZA_PREFIX.'_no_cache" type="hidden" name="'.WPPIZZA_PREFIX.'_no_cache" value="'.time().'" />';
		}

		/**
			no checkout applies if min order not reached OR no gateway enabled.
			only if min order applues will a note be displayed though
			if there's no gateway enabled, then nothing will show
		**/
		/*
			minimum order required text
		*/
		$minimum_order = WPPIZZA() -> markup_maincart -> minimum_order($order_formatted, $type);


		/*************************************

			markup

		*************************************/
		/*
			ini array
		*/
		$markup = array();

		/*
			get markup
		*/
		if(file_exists( WPPIZZA_TEMPLATE_DIR . '/markup/pages/page.order.php')){
			require(WPPIZZA_TEMPLATE_DIR.'/markup/pages/page.order.php');
		}else{
			require(WPPIZZA_PATH.'templates/markup/pages/page.order.php');
		}


		/*
			Big Red Note if email sending is disabled
			right after opening div
		*/
		if(!empty($wppizza_options['tools']['disable_emails'])){
			$markup['div_'] .= '<h3 style="color:red; text-align:center">'.__('Email sending has currently been disabled by the administrator', 'wppizza_admin').'</h3>';
		}


		/*
			only displayed if shop is closed
			and pickup choices were force-enabled with 'wppizza_filter_force_pickup_toggle_display' filter
			and toggle is enabled on order page
			make sure to wrap in form or the toggle does nothing
		*/
		$force_pickup_toggle = apply_filters('wppizza_filter_force_pickup_toggle_display', false);
		if(!$shop_open && !empty($force_pickup_toggle)){
			/*
				form start
			*/
			$markup['form_'] = '<form id="' . $formid . '" method="post" accept-charset="' . WPPIZZA_CHARSET .'">';
				$markup['order_details_pickup_choices'] = $order_details_pickup_choices ; /*uses: markup/global/pickup_choice.php  */
			$markup['_form'] = '</form>';
		}

		/*
			apply filter if required and implode for output
		*/
		$markup = apply_filters('wppizza_filter_pages_order_markup', $markup, $order_formatted );

		/*
			if cart disabled by 'nocart=1|true' shortcode , remove fieldset
		*/
		if($nocart){
			unset($markup['order_details_']);
			unset($markup['order_details_legend']);
			unset($markup['order_details_itemised']);
			unset($markup['order_details_summary']);
			unset($markup['order_details_pickup_note']);
			unset($markup['order_details_pickup_choices']);
			unset($markup['_order_details']);
		}

		$markup = implode('', $markup);

	return $markup;

	}

	/***************************************

		[confirmation page]

	***************************************/
	function confirmation_page($type, $atts = null, $txt, $user_session){

		/****************************************
			set the order query args
			get order details from db
		****************************************/
		$args = array(
			'query' => array(
				'hash' => $user_session['wppizza_hash'] ,
				'payment_status' => array('INITIALIZED'),
			),
			'format' => array(
				'blog_options' => array('confirmation', 'blog_options'),// add some additional info
				'sections' => true,
			),
		);
		/*************************************************
			run query, and get results
			even single order results are always arrays
			so simply use reset here
		*************************************************/
		$order_results = WPPIZZA() -> db -> get_orders($args, $type);
		$order_results = reset($order_results['orders']);



		$order_formatted = $order_results['sections'];/* for consistency with carts etc */
		$order_formatted['confirmation'] = $order_results['confirmation'];

		/**get links to order/amend pages*/
		$href_orderpage = wppizza_page_links('orderpage');
		$href_amendorder = wppizza_page_links('amendorderlink');
		/** do we have any confirmation inputs ?*/
		$has_inputs = !empty($order_formatted['confirmation']) ?  true : false;


		/*
			ids'|classes
		*/
		$id = ''.WPPIZZA_PREFIX.'-order-wrap-'.$type.'';
		$class = ''.WPPIZZA_PREFIX.'-order-wrap '.WPPIZZA_PREFIX.'-order-wrap-'.$type.'';
		if(!empty($order_formatted['ordervars']['pickup_delivery']['value']) && $order_formatted['ordervars']['pickup_delivery']['value'] == 'Y'){
		$class .= ' '.WPPIZZA_PREFIX.'-order-ispickup';
		}


		$id_form = ''.WPPIZZA_PREFIX.'-send-order';
		$class_form = ''.WPPIZZA_PREFIX.'-order-confirmed';

		$class_legal = ''.WPPIZZA_PREFIX.'-fieldset '.WPPIZZA_PREFIX.'-legal';
		$class_personal_details = ''.WPPIZZA_PREFIX.'-fieldset '.WPPIZZA_PREFIX.'-personal-details';
		$class_payment_method = ''.WPPIZZA_PREFIX.'-fieldset '.WPPIZZA_PREFIX.'-payment-method';

		$id_order_details = ''.WPPIZZA_PREFIX.'-order-details-'.$type.'';
		$class_order_details = ''.WPPIZZA_PREFIX.'-fieldset '.WPPIZZA_PREFIX.'-order-details';

		$id_subtotals_after = ''.WPPIZZA_PREFIX.'-'.$type.'-subtotals-after';





		/***********************

			[markup parameters]

		************************/

		/*
			confirmation form input fields
		*/
		if($has_inputs){
			$confirm_inputs = WPPIZZA()->user->formfields_inputs($order_formatted['confirmation']);
		}
		/*
			personal_details
		*/
			$personal_details= WPPIZZA()->user->formfields_values($order_formatted['customer'], 'confirmationpage');

		/*
			payment-method
		*/
			$payment_method = WPPIZZA()->gateways->markup(true);

		/*
			order-details
		*/
			/* pickup / delivery note */
			$order_details_pickup_note = self::pickup_note_markup($order_formatted, $type);

			/*	order_itemised	*/
			$order_details_itemised = WPPIZZA()->markup_maincart->itemised_markup($order_formatted, $type); /* cart_itemised */

			/*	order_summary */
			$order_details_summary = WPPIZZA()->markup_maincart->summary_markup($order_formatted, $type); /* summary */


		/*
			inline payment details - confirmation page not enabled
		*/

			$payment_details = self::payment_details_markup($type);

		/*
			submit_button
		*/

			$submit_button = '';
			/* a generic - but empty - error div right before the submit button one can write any errors to if needed @since v3.7 */
			$submit_button .= '<div role="alert" id="wppizza-submit-error" class="wppizza-validation-error"></div>';
			/* button element */
			$submit_button .='<input id="'.WPPIZZA_PREFIX.'-ordernow" class="submit '.WPPIZZA_PREFIX.'-ordernow" type="submit" style="display:block" value="'.$txt['confirm_now_button'].'" />';
			/* set flag that this is the confirmation page to not override session vars from main order page*/
			$submit_button .='<input type="hidden" name="'.WPPIZZA_PREFIX.'_'.$type.'" value="1" />';
			/* add nonce */
			$submit_button .= ''.wp_nonce_field( '' . WPPIZZA_PREFIX . '_nonce_checkout','' . WPPIZZA_PREFIX . '_nonce_checkout',true,false).'';


		/*************************************

			markup

		*************************************/
		/*
			ini array
		*/
		$markup = array();
		/*
			get markup
		*/
		if(file_exists( WPPIZZA_TEMPLATE_DIR . '/markup/pages/page.confirm-order.php')){
			require(WPPIZZA_TEMPLATE_DIR.'/markup/pages/page.confirm-order.php');
		}else{
			require(WPPIZZA_PATH.'templates/markup/pages/page.confirm-order.php');
		}
		/*
			apply filter if required and implode for output
		*/
		$markup = apply_filters('wppizza_filter_pages_confirmorder_markup', $markup, $order_formatted);
		$markup = implode('', $markup);


	return $markup;



	}
	/***************************************

		[thank you  page]

	***************************************/
	function thankyou_page($type, $atts = null, $txt, $user_session){
		global $wppizza_options, $blog_id ;
		/*
			also search failed and any progress statusses here to display errors or "waiting page" if necessary
			furthermore, do not restrict to user id  / 0 / session either. we will display those specifically
		*/
		$show_results = false;
		$is_order = false;
		$is_completed = false;
		$is_inprogress = false;
		$is_payment_pending = false;
		$is_unconfirmed = false;
		$is_confirmed = false;
		$is_cancelled = false;
		$is_rejected = false;
		$order_page_link = '';


		if(!empty($this -> thankyoupage_results_formatted)){

			$order_results = $this -> thankyoupage_results_formatted;

			$order_formatted = $order_results['sections'];/* for consistency with carts etc */
			$show_results = apply_filters('wppizza_filter_showorder_on_thankyou', true);

			/** check if this order actually completed and not failed */
			$is_completed = ($order_formatted['ordervars']['payment_status']['value'] == 'COMPLETED') ? true : false ;

			/** in progress statusses, will make page reload a few times**/
			$is_inprogress = (in_array($order_formatted['ordervars']['payment_status']['value'],array('INPROGRESS', 'AUTHORIZED', 'CAPTURED'))) ? true : false ;

			/** payment_pending */
			$is_payment_pending = ($order_formatted['ordervars']['payment_status']['value'] == 'PAYMENT_PENDING') ? true : false ;

			/** unconfirmed status**/
			$is_unconfirmed = (in_array($order_formatted['ordervars']['payment_status']['value'],array('UNCONFIRMED'))) ? true : false ;

			/** confirmed status**/
			$is_confirmed = (in_array($order_formatted['ordervars']['payment_status']['value'],array('CONFIRMED'))) ? true : false ;

			/** rejected status**/
			$is_rejected = (in_array($order_formatted['ordervars']['payment_status']['value'],array('REJECTED'))) ? true : false ;

			/** refunded status**/
			$is_refunded = (in_array($order_formatted['ordervars']['payment_status']['value'],array('REFUNDED'))) ? true : false ;

			/** check if it was cancelled **/
			$is_cancelled = (in_array($order_formatted['ordervars']['payment_status']['value'],array('CANCELLED'))) ? true : false ;


			/** if failed order, return false **/
			$is_order = ($is_completed) ? true : false ;
			/** if failed order, return false **/
			$show_results = ($is_completed) ? true : false ;
			/**
				if logged in and wp_user_id does not match or
				not logged and sessionid does not match, do not show full results
			**/
			if(is_user_logged_in() && $order_formatted['ordervars']['wp_user_id']['value'] != get_current_user_id()){
				$show_results = false;
			}
			if(!is_user_logged_in() && $order_formatted['ordervars']['session_id']['value'] != session_id()){
				$show_results = false;
			}


			/** if failed order, get errors **/
			$errors = ($is_completed || $is_inprogress) ? '' : $order_formatted['ordervars']['display_errors']['value_formatted'];

			/*
				ids'|classes
			*/
			$id = ''.WPPIZZA_PREFIX.'-order-'.$type.'-'.$order_formatted['site']['blog_id']['value_formatted'].'-'.$order_formatted['ordervars']['order_id']['value_formatted'].'';
			$class = ''.WPPIZZA_PREFIX.'-order-wrap '.WPPIZZA_PREFIX.'-order-wrap-'.$type.'';
			if(!empty($order_formatted['ordervars']['pickup_delivery']['value']) && $order_formatted['ordervars']['pickup_delivery']['value'] == 'Y'){
				$class .= ' '.WPPIZZA_PREFIX.'-order-ispickup';
			}
			$class_transaction_details = ''.WPPIZZA_PREFIX.'-fieldset '.WPPIZZA_PREFIX.'-transaction-details';
			$class_personal_details = ''.WPPIZZA_PREFIX.'-fieldset '.WPPIZZA_PREFIX.'-personal-details';
			$class_order_details = ''.WPPIZZA_PREFIX.'-fieldset '.WPPIZZA_PREFIX.'-order-details';

		}else{
			/*
				in case there was an error or order does not exist
			*/
			$id = ''.WPPIZZA_PREFIX.'-order-'.$type.'-error';
			$class = ''.WPPIZZA_PREFIX.'-order-wrap '.WPPIZZA_PREFIX.'-order-wrap-'.$type.'-error';
			$order_results = false;
			$show_results = false;
			$order_formatted = array();/* as there's no order just return an empty array */
			$errors = '';/* no errors known as db returned no results */
		}

		/*
			general error ids'|classes| links
		*/
		$id_errors = ''.WPPIZZA_PREFIX.'-order-errors';
		$class_errors = ''.WPPIZZA_PREFIX.'-order-error';
		$id_noorder = ''.WPPIZZA_PREFIX.'-noorder-'.$type.'';
		$class_noorder = ''.WPPIZZA_PREFIX.'-noorder';

		/*
			general processing ids'|classes| links
		*/
		$id_processing = ''.WPPIZZA_PREFIX.'-order-processing';
		$class_processing = ''.WPPIZZA_PREFIX.'-order-processing';
		$id_wait = ''.WPPIZZA_PREFIX.'-processing-'.$type.'';
		$class_wait = ''.WPPIZZA_PREFIX.'-processing';
		$class_order_id = ''.WPPIZZA_PREFIX.'-order-processing-id';


		/*
			general payment_pending ids'|classes| links
		*/
		$id_payment_pending_info = ''.WPPIZZA_PREFIX.'-payment_pending-'.$type.'';
		$class_payment_pending_info = ''.WPPIZZA_PREFIX.'-payment_pending';


		/*
			general unconfirmed ids'|classes| links
		*/
		$id_unconfirmed = ''.WPPIZZA_PREFIX.'-order-unconfirmed';
		$class_unconfirmed = ''.WPPIZZA_PREFIX.'-order-unconfirmed';

		/*
			general confirmed ids'|classes| links
		*/
		$id_confirmed = ''.WPPIZZA_PREFIX.'-order-confirmed';
		$class_confirmed = ''.WPPIZZA_PREFIX.'-order-confirmed';

		/*
			general rejected ids'|classes| links
		*/
		$id_rejected = ''.WPPIZZA_PREFIX.'-order-rejected';
		$class_rejected = ''.WPPIZZA_PREFIX.'-order-rejected';

		/*
			general refunded ids'|classes| links
		*/
		$id_refunded = ''.WPPIZZA_PREFIX.'-order-refunded';
		$class_refunded = ''.WPPIZZA_PREFIX.'-order-refunded';


		if(!$is_completed && !$is_inprogress && !$is_payment_pending && !$is_cancelled && !$is_unconfirmed && !$is_confirmed && !$is_rejected && !$is_refunded){
			/**get links to order/amend pages*/
			$order_page_link = wppizza_page_links();
			$order_page_link = '<div class="'.WPPIZZA_PREFIX.'-try-again"><a href="'.$order_page_link['orderpage'].'">'.$txt['failed_payment_try_again_link'].'</a></div>';
		}


		/***********************************************************
			empty cart session when order completed or in progress of completion
		***********************************************************/
		if($is_completed || $is_inprogress || $is_payment_pending || $is_unconfirmed || $is_confirmed || $is_rejected || $is_refunded){
			if(!WPPIZZA_DEV_DISABLE_CLEAR_CART){
				WPPIZZA()->session->empty_cart(true);
			}
		}

		/**********************************************************
			showing whole order , not just thank you
		**********************************************************/
		if($show_results){
			/**************************************
				general transaction details
			**************************************/
			$transaction_details = self::order_transaction_details($order_formatted['ordervars'], $type);// uses markup/order/transaction_details.php
			/* pickup / delivery note */
			$order_details_pickup_note = self::pickup_note_markup($order_formatted, $type);//uses markup/global/pages.pickup_note.php
			/*************************************
				personal_details
			*************************************/
			$personal_details = WPPIZZA()->user->formfields_values($order_formatted['customer'], 'thankyoupage');//uses markup/global/formfields.values.php
			/************************************
				order-details : itemised, summary
			************************************/
			/*	order_itemised	*/
			$order_details_itemised = WPPIZZA()->markup_maincart->itemised_markup($order_formatted, $type); /* cart_itemised - uses markup/order/itemised.php */
			/*	order_summary */
			$order_details_summary = WPPIZZA()->markup_maincart->summary_markup($order_formatted, $type); /* summary - uses markup/order/summary.php */
		}

		/*************************************

			cancelled payment

		*************************************/
		if($is_cancelled){

			/** in case there was an error or order does not exist **/
			$id = ''.WPPIZZA_PREFIX.'-order-cancelled';
			$class = ''.WPPIZZA_PREFIX.'-order-wrap '.WPPIZZA_PREFIX.'-order-wrap-cancelled';

			/*
				[cancelled text]
			*/
			$cancel_text = $txt['order_cancelled_p'];
			/*
				create return to shop link
			*/
			$return_link = '<div class="'.WPPIZZA_PREFIX.'-back-to-shop"><a href="'.site_url().'">'.$txt['label_return_to_shop'].'</a></div>';

			/*
				cancelled via ipn
			*/
			$cancel_type = 'ipn';

			/*
				ini array
			*/
			$markup = array();
			/*
				get markup
			*/
			if(file_exists( WPPIZZA_TEMPLATE_DIR . '/markup/pages/page.cancelled.php')){
				require(WPPIZZA_TEMPLATE_DIR.'/markup/pages/page.cancelled.php');
			}else{
				require(WPPIZZA_PATH.'templates/markup/pages/page.cancelled.php');
			}

			/*
				apply filter if required and implode for output
			*/
			$markup = apply_filters('wppizza_filter_pages_cancelled_markup', $markup, $order_formatted, $cancel_type);
			$markup = implode('', $markup);




		return $markup;
		}

		/*************************************

			processing payment / INPROGRESS

		*************************************/
		if($is_inprogress){

			/** in case there was an error or order does not exist **/
			$id = ''.WPPIZZA_PREFIX.'-order-wrap-processing';
			$class = ''.WPPIZZA_PREFIX.'-order-wrap';

			/*
				ini array
			*/
			$markup = array();
			/*
				get markup
			*/
			if(file_exists( WPPIZZA_TEMPLATE_DIR . '/markup/pages/page.processing.php')){
				require(WPPIZZA_TEMPLATE_DIR.'/markup/pages/page.processing.php');
			}else{
				require(WPPIZZA_PATH.'templates/markup/pages/page.processing.php');
			}
			/** order info */
			$markup['order_info_']='<div class="'.$class_order_id.'">';
				$markup['order_info_date']='['.$order_formatted['ordervars']['order_date']['value_formatted'].']';
				$markup['order_info_id']='<br />'.$order_formatted['ordervars']['payment_gateway']['value_formatted'].' - ID:'.$order_formatted['ordervars']['order_id']['value_formatted'].'';
				$markup['order_info_txid']='<br />TXID: '.$order_formatted['ordervars']['transaction_id']['value_formatted'].'';
				$markup['order_info_status']=' - '.$order_formatted['ordervars']['payment_status']['value_formatted'].'';
			$markup['_order_info']='</div>';
			/** add processing js */
			$markup['refresh_page']='<script>setInterval(function(){window.location = window.location.href;return;},5000);</script>';

			/*
				apply filter if required and implode for output
			*/
			$markup = apply_filters('wppizza_filter_pages_processing_markup', $markup, $order_formatted);
			$markup = implode('', $markup);




		return $markup;
		}

		/*************************************

			payment pending ....

		*************************************/
		if($is_payment_pending){
			/** in case there was an error or order does not exist **/
			$id = ''.WPPIZZA_PREFIX.'-order-wrap-payment-pending';
			$class = ''.WPPIZZA_PREFIX.'-order-wrap';

			/* add link back to order page including hash */
			$order_check_link = wppizza_orderpage_url(array(WPPIZZA_TRANSACTION_GET_PREFIX=>$order_formatted['ordervars']['hash']['value']));
			$txt['payment_pending_info'] = sprintf($txt['order_payment_pending_p'], $order_check_link, $order_check_link);// add it 2x in case someone wants to use it 2x

			/*
				ini array
			*/
			$markup = array();

			/*
				get markup
			*/
			if(file_exists( WPPIZZA_TEMPLATE_DIR . '/markup/pages/page.payment-pending.php')){
				require(WPPIZZA_TEMPLATE_DIR.'/markup/pages/page.payment-pending.php');
			}else{
				require(WPPIZZA_PATH.'templates/markup/pages/page.payment-pending.php');
			}

			/** add 15 seconds refresh js anyway, even though payments could take hours or even days to arrive for thos status....*/
			$markup['refresh_page']='<script>setInterval(function(){window.location = "'.$order_check_link.'";return;},15000);</script>';

			/*
				apply filter if required and implode for output
			*/
			$markup = apply_filters('wppizza_filter_payment_pending_markup', $markup, $order_formatted);
			$markup = implode('', $markup);


		return $markup;

		}
		/*************************************

			waiting for confirmation / UNCONFIRMED

		*************************************/
		if($is_unconfirmed){

			/** in case there was an error or order does not exist **/
			$id = ''.WPPIZZA_PREFIX.'-order-wrap-unconfirmed';
			$class = ''.WPPIZZA_PREFIX.'-order-wrap';

			/*
				ini array
			*/
			$markup = array();
			/*
				get markup
			*/
			if(file_exists( WPPIZZA_TEMPLATE_DIR . '/markup/pages/page.unconfirmed.php')){
				require(WPPIZZA_TEMPLATE_DIR.'/markup/pages/page.unconfirmed.php');
			}else{
				require(WPPIZZA_PATH.'templates/markup/pages/page.unconfirmed.php');
			}
//			/** order info */
//			$markup['order_info_']='<div class="'.$class_order_id.'">';
//				$markup['order_info_date']='['.$order_formatted['ordervars']['order_date']['value_formatted'].']';
//				$markup['order_info_id']='<br />'.$order_formatted['ordervars']['payment_gateway']['value_formatted'].' - ID:'.$order_formatted['ordervars']['order_id']['value_formatted'].'';
//				$markup['order_info_txid']='<br />TXID: '.$order_formatted['ordervars']['transaction_id']['value_formatted'].'';
//				$markup['order_info_status']=' - '.$order_formatted['ordervars']['payment_status']['value_formatted'].'';
//			$markup['_order_info']='</div>';
//			/** add processing js */
//			$markup['refresh_page']='<script>setInterval(function(){window.location.reload(true);},5000);</script>';

			/*
				apply filter if required and implode for output
			*/
			$markup = apply_filters('wppizza_filter_pages_unconfirmed_markup', $markup, $order_formatted);
			$markup = implode('', $markup);


		return $markup;
		}

		/*************************************

			order confirmed by user, waiting for confirmation (execution) by shop

		*************************************/
		if($is_confirmed){

			/** in case there was an error or order does not exist **/
			$id = ''.WPPIZZA_PREFIX.'-order-wrap-confirmed';
			$class = ''.WPPIZZA_PREFIX.'-order-wrap';

			/** empty by default plugins should use the filter to write to this as to what will happen next */
			$empty_string = '';
			$txt['order_confirmed_info'] = apply_filters('wppizza_filter_page_confirmed_info', $empty_string , $order_formatted);

			/*
				ini array
			*/
			$markup = array();


			/*
				get markup
			*/
			if(file_exists( WPPIZZA_TEMPLATE_DIR . '/markup/pages/page.confirmed.php')){
				require(WPPIZZA_TEMPLATE_DIR.'/markup/pages/page.confirmed.php');
			}else{
				require(WPPIZZA_PATH.'templates/markup/pages/page.confirmed.php');
			}

			/*
				apply filter if required and implode for output
			*/
			$markup = apply_filters('wppizza_filter_pages_confirmed_markup', $markup, $order_formatted);
			$markup = implode('', $markup);


		return $markup;
		}

		/*************************************

			rejected

		*************************************/
		if($is_rejected){

			/** in case there was an error or order does not exist **/
			$id = ''.WPPIZZA_PREFIX.'-order-wrap-rejected';
			$class = ''.WPPIZZA_PREFIX.'-order-wrap';

			/** empty by default - plugins should use the filter to write rejection reason to this */
			$empty_string = '';
			$txt['order_rejected_info'] = apply_filters('wppizza_filter_page_rejected_info', $empty_string , $order_formatted);

			/*
				ini array
			*/
			$markup = array();
			/*
				get markup
			*/
			if(file_exists( WPPIZZA_TEMPLATE_DIR . '/markup/pages/page.rejected.php')){
				require(WPPIZZA_TEMPLATE_DIR.'/markup/pages/page.rejected.php');
			}else{
				require(WPPIZZA_PATH.'templates/markup/pages/page.rejected.php');
			}
			/*
				apply filter if required and implode for output
			*/
			$markup = apply_filters('wppizza_filter_pages_rejected_markup', $markup, $order_formatted);
			$markup = implode('', $markup);


		return $markup;

		}

		/*************************************

			refunded

		*************************************/
		if($is_refunded){

			/** in case there was an error or order does not exist **/
			$id = ''.WPPIZZA_PREFIX.'-order-wrap-refunded';
			$class = ''.WPPIZZA_PREFIX.'-order-wrap';

			/** empty by default - plugins should use the filter to write refunded reason to this */
			$empty_string = '';
			$txt['order_refunded_info'] = apply_filters('wppizza_filter_page_refunded_info', $empty_string , $order_formatted);

			/*
				ini array
			*/
			$markup = array();
			/*
				get markup
			*/
			if(file_exists( WPPIZZA_TEMPLATE_DIR . '/markup/pages/page.refunded.php')){
				require(WPPIZZA_TEMPLATE_DIR.'/markup/pages/page.refunded.php');
			}else{
				require(WPPIZZA_PATH.'templates/markup/pages/page.refunded.php');
			}
			/*
				apply filter if required and implode for output
			*/
			$markup = apply_filters('wppizza_filter_pages_refunded_markup', $markup, $order_formatted);
			$markup = implode('', $markup);


		return $markup;

		}

		/*************************************

			markup thank you / failed / unknown

		*************************************/
		/*
			ini array
		*/
		$markup = array();
		/*
			get markup
		*/
		if(file_exists( WPPIZZA_TEMPLATE_DIR . '/markup/pages/page.thankyou.php')){
			require(WPPIZZA_TEMPLATE_DIR.'/markup/pages/page.thankyou.php');
		}else{
			require(WPPIZZA_PATH.'templates/markup/pages/page.thankyou.php');
		}
		/*
			apply filter if required and implode for output
		*/
		$markup = apply_filters('wppizza_filter_pages_thankyou_markup', $markup, $order_formatted);
		$markup = implode('', $markup);


	return $markup;
	}
	/***************************************

		[order cancelled  page]

	***************************************/
	function cancelled_page($type, $atts = null, $txt, $user_session){

		/***************************************************************
			update db/order to CANCELLED by _GET[WPPIZZA_TRANSACTION_CANCEL_PREFIX]
		****************************************************************/
		/*
			set db to cancel by hash. will silently not do anything and return false
		 	if hash does not match anything , $_GET will automatically be sanitized
		*/
		$order_cancel = WPPIZZA()->db->cancel_order(false, false, $_GET[WPPIZZA_TRANSACTION_CANCEL_PREFIX]);


		/***************************************************************
			end db cancellation
		****************************************************************/

		/** in case there was an error or order does not exist **/
		$id = ''.WPPIZZA_PREFIX.'-order-cancelled';
		$class = ''.WPPIZZA_PREFIX.'-order-wrap '.WPPIZZA_PREFIX.'-order-wrap-cancelled';

		/***************************************************************
			if that order does not exist, show error
		****************************************************************/
		if($order_cancel){
			$cancel_text = $txt['order_cancelled_p'];
		}else{
			$cancel_text = $txt['order_not_found'];
		}
		/*
			create return to shop link
		*/
		$return_link = '<div class="'.WPPIZZA_PREFIX.'-back-to-shop"><a href="'.site_url().'">'.$txt['label_return_to_shop'].'</a></div>';

		/*
			for consistency use user_session as $order_formatted
		*/
		$order_formatted = $user_session;

		/*
			cancelled via direct link from gateway
		*/
		$cancel_type = 'link';
		/*************************************

			markup

		*************************************/

		/*
			ini array
		*/
		$markup = array();
		/*
			get markup
		*/
		if(file_exists( WPPIZZA_TEMPLATE_DIR . '/markup/pages/page.cancelled.php')){
			require(WPPIZZA_TEMPLATE_DIR.'/markup/pages/page.cancelled.php');
		}else{
			require(WPPIZZA_PATH.'templates/markup/pages/page.cancelled.php');
		}
		/*
			apply filter if required and implode for output
		*/
		$markup = apply_filters('wppizza_filter_pages_cancelled_markup', $markup, $order_formatted, $cancel_type);
		$markup = implode('', $markup);

	return $markup;
	}
	/***************************************

		[USERS order history page]

	***************************************/
	function orderhistory_page($type, $atts = null, $txt, $user_session){

		/*
			ids'|classes
		*/
		$id = ''.WPPIZZA_PREFIX.'-orders-wrap-'.$type.'';/* wrapper id */
		$class = ''.WPPIZZA_PREFIX.'-orders-wrap '.WPPIZZA_PREFIX.'-order-wrap-'.$type.'';/* wrapper class */

		$id_noorders = ''.WPPIZZA_PREFIX.'-noorders-'.$type.'';
		$class_noorders = ''.WPPIZZA_PREFIX.'-noorders';

		/*
			login form
			- only shown if not logged in and registration is enabled in the first place
			show registration disabled here if that is the case and user (typically admin) is not logged in
		*/
		$login_form = WPPIZZA()->user->login_form(true);// uses markup/global/login.php


		/*
			we are not logged in
		*/
		if(!is_user_logged_in()){
			$no_orders = false ;
			$is_logged_in = false ;
			$purchase_history = array();
			$pagination = '';
			$txt['history_no_previous_orders'] = '' ;/* just to make sure if someone takes the if($no_orders) out of the template */
		}
		/*
			we are logged in
		*/
		if(is_user_logged_in()){
			$is_logged_in = true ;
			$user_id = get_current_user_id() ;

			/*
				query args
				get (only) completed orders for this user
			*/
			$args = array(
				'query'=>array(
					'wp_user_id' => $user_id ,
					'payment_status' => 'COMPLETED',
					'blogs' => (!empty($atts['multisite']) ? true : false ),//force multisite if set by atts
				),
				'pagination' =>array(
					'paged' => 	(isset($_GET['pg']) ? $_GET['pg'] : 0) ,
					'limit' => (( !empty($atts['maxpp']) && (int)$atts['maxpp']>0) ? (int)$atts['maxpp'] : 10 ) ,
				),
				'format' => array(
					'blog_options' => true,
				),
			);
			$orders_completed = WPPIZZA()->db->get_orders($args, $type);

			/*
				no orders
			*/
			if(empty($orders_completed['total_number_of_orders'])){

				$no_orders = true;
				$purchase_history = array();
				$pagination = '';

			}else{

			/*
				classes, loop and pagination
			*/

				$no_orders = false;
				$purchase_history = array();
				$pagination = self::orderhistory_pagination($orders_completed['total_number_of_orders'], $args['pagination']['limit'], 2, true);

				/* set classes */
				$class_order = ''.WPPIZZA_PREFIX.'-order-wrap '.WPPIZZA_PREFIX.'-order-'.$type.'';
				$class_fieldset = ''.WPPIZZA_PREFIX.'-fieldset '.WPPIZZA_PREFIX.'-transaction-details-'.$type.'';
				$class_legend_span = ''.WPPIZZA_PREFIX.'-dashicon '.WPPIZZA_PREFIX.'-toggle-order-transaction-details dashicons dashicons-plus';
				$class_order_details = ''.WPPIZZA_PREFIX.'-order-details';
				$class_transaction_details = ''.WPPIZZA_PREFIX.'-transaction-details';

				/* loop through orders */
				foreach($orders_completed['orders'] as $uoKey => $order_formatted){

					/* set id */
					$purchase_history[$uoKey] = array();

					$purchase_history[$uoKey]['id'] = ''.WPPIZZA_PREFIX.'-order-'.$type.'-'.$uoKey.'';

					/* might come in useful in filters */
					$purchase_history[$uoKey]['blog_id'] = $order_formatted['site']['blog_id']['value_formatted'];
					$purchase_history[$uoKey]['order_id'] = $order_formatted['ordervars']['order_id']['value_formatted'];
					/* itemised */
					$purchase_history[$uoKey]['order_itemised'] = WPPIZZA()->markup_maincart->itemised_markup($order_formatted, $type);
					/*	order_summary */
					$purchase_history[$uoKey]['order_summary'] = WPPIZZA()->markup_maincart->summary_markup($order_formatted, $type);
					/* transaction details */
					$purchase_history[$uoKey]['transaction_details'] = self::order_transaction_details($order_formatted['ordervars'], $type);

				}
			}
		}

		/*************************************

			markup

		*************************************/
		/*
			ini markup array
		*/
		$markup = array();

		/*
			get markup
		*/
		if(file_exists( WPPIZZA_TEMPLATE_DIR . '/markup/pages/page.purchase-history.php')){
			require(WPPIZZA_TEMPLATE_DIR.'/markup/pages/page.purchase-history.php');
		}else{
			require(WPPIZZA_PATH.'templates/markup/pages/page.purchase-history.php');
		}
		/*
			apply filter if required and implode for output
		*/
		$markup = apply_filters('wppizza_filter_pages_purchasehistory_markup', $markup, $purchase_history, $user_id);
		$markup = implode('', $markup);


	return $markup;
	}

	/***************************************
	#
	#	[ADMIN admin_dashboard_widget_page, can be added somewhere by shortcode]
	#	IT'S THE USERS RESPONSIBILITY TO SECURE THE PAGE IF ATT SET TO UNPROTECTED
	#
	#	@since 3.8
	#	@param string
	#	@param array
	#	@param array
	#	@param array
	#
	#	@return string
	***************************************/
	function admin_dashboard_widget_page($type, $atts = null, $txt, $user_session){
		global $wppizza_options, $current_user;

		/*
			ini markup
		*/
		$markup = '';


		/************************
		#
		# attributes
		#
		************************/
		/*
			if distinctly set, we can bypass wordpress login requirements
			which makes it the responsibility of the user to protect this page
		*/
		$protected = empty($atts['unprotected']) ? true : false;

		/*******************
			we are not logged in yet
			display login form (provided we are not bypassing protection altogether via attribute)
		*******************/
		if(!empty($protected) && !is_user_logged_in()){

			/*
				filter to simplify form , omitting toggles, forgot password etc etc
			*/
			add_filter('wppizza_filter_login_widget_markup', array($this, 'admin_orderhistory_login_markup'));
			/*
				login form markup
			*/
			$markup = WPPIZZA() -> user -> login_form(false, true, true);// uses markup/global/login.php

			/* set flag to disallow access to orders */
			$access_denied = true;
		}

		/*******************
			we are logged in
			with protection enabled (default)
			but do not have the required capabilities
		*******************/
		if (!empty($protected) && is_user_logged_in() && empty($current_user->allcaps['wppizza_cap_orderhistory'])){
			$markup = __('Sorry, you are not allowed to access this page.');

			/* set flag to disallow access to orders */
			$access_denied = true;
		}
		/***************************************************
			we are logged in
			or bypassing protection altogether

			=> get widget

		***************************************************/
		if( ($protected === false || is_user_logged_in()) && empty($access_denied)){

			/*************************************

				markup

			*************************************/
			/*
				ini markup array
			*/
			$widget_markup = WPPIZZA() -> admin_dashboard_widgets -> wppizza_do_dashboard_widget_sales(true);
			/*
				wrap in same div ids as admin
			*/
	    	$markup = '<div id="'.WPPIZZA_PREFIX.'_dashboard_widget" class="postbox"><div class="inside">'.$widget_markup.'</div></div>';


			/* enqueue dashicons */
			wp_enqueue_style( 'dashicons' );


			/* enqueue global admin scripts to make reload work */
	    	wp_register_script(	WPPIZZA_SLUG.'-global', WPPIZZA_URL.'js/scripts.admin.global.js' , array('jquery'), WPPIZZA_VERSION, true);
			wp_enqueue_script(	WPPIZZA_SLUG.'-global');

		}


	return $markup;
	}

	/***************************************
	#
	#	[ADMIN order history page, can be added somewhere by shortcode]
	#	IT'S THE USERS RESPONSIBILITY TO SECURE THE PAGE IF ATT SET TO UNPROTECTED
	#
	#	@since 3.5
	#	@param string
	#	@param array
	#	@param array
	#	@param array
	#
	#	@return string
	***************************************/
	function admin_orderhistory_page($type, $atts = null, $txt, $user_session){
		global $wppizza_options, $current_user;
		static $shortcode_id = 0; $shortcode_id++;
		$get_blog_id = get_current_blog_id();

		$constant = 'admin-orders';


		/************************
		#
		# attributes
		#
		************************/
		/*
			if distinctly set, we can bypass wordpress login requirements
			which makes it the responsibility of the user to protect this page
		*/
		$protected = empty($atts['unprotected']) ? true : false;

		/*
			passing on post_id to pagination
			in/for ajax calls
		*/
		$set_post_id = empty($atts['post_id']) ? false : (int)$atts['post_id'];

		/*
			keys that make up the name
		*/
		$name_keys = !empty($atts['name']) ? array_filter(explode(',',str_replace(' ','', $atts['name']))) : array('cname');

		/*
			keys that make up the address
		*/
		$address_keys = !empty($atts['address']) ? array_filter(explode(',',str_replace(' ','', $atts['address']))) : array('caddress');

		/*
			omit pagination
		*/
		$show_pagination = !empty($atts['no_pagination']) ? false : true;

		/*
			print_view
		*/
		$print_view = !empty($atts['print_view']) ? true : false;


		/************************
		#
		# set audio attributes if notifications
		# are enabled for order history
		#
		************************/
		if(!empty($wppizza_options['settings']['new_orders_notify']) && !empty($wppizza_options['settings']['new_orders_audio_file']) && isset($wppizza_options['settings']['new_orders_notify_pages']['orderhistory']) ){
			$atts['audio_notify'] = $wppizza_options['settings']['new_orders_audio_file'];
		}

		/************************
		#
		# ids'|classes
		#
		************************/

		/* no order id/classes */
		$id_noorders = ''.WPPIZZA_PREFIX.'-'.$constant.'-noorders-'.$shortcode_id.'';
		$class_noorders = ''.WPPIZZA_PREFIX.'-'.$constant.'-noorders';

		/* order table thead/tfoot classes */
		$class_table = ''.WPPIZZA_PREFIX.'-'.$constant.'-table';

		/* order table thead/tfoot classes */
		$class_th_dates = ''.WPPIZZA_PREFIX.'-th-dates';
		$class_th_customer = ''.WPPIZZA_PREFIX.'-th-customer';
		$class_th_order = ''.WPPIZZA_PREFIX.'-th-order';
		$class_th_status_type = ''.WPPIZZA_PREFIX.'-th-status-type';
		$class_th_actions = ''.WPPIZZA_PREFIX.'-th-actions';

		/* order classes */
		$class_dates = ''.WPPIZZA_PREFIX.'-dates';
		$class_customer = ''.WPPIZZA_PREFIX.'-customer';
		$class_order = ''.WPPIZZA_PREFIX.'-order';
		$class_status_type = ''.WPPIZZA_PREFIX.'-status-type';
		$class_actions = ''.WPPIZZA_PREFIX.'-actions';


		/************************
		#
		# ini strings/parameters
		#
		************************/
		$login_form = '';
		$pagination = '';
		$no_orders = '';
		$has_orders = false;
		$order_history = array();

		/*******************
			we are not logged in yet
			display login form (provided we are not bypassing protection altogether via attribute)
		*******************/
		if(!empty($protected) && !is_user_logged_in()){

			/*
				filter to simplify form , omitting toggles, forgot password etc etc
			*/
			add_filter('wppizza_filter_login_widget_markup', array($this, 'admin_orderhistory_login_markup'));
			/*
				login form markup
			*/
			$login_form = WPPIZZA() -> user -> login_form(false, true, true);// uses markup/global/login.php

			/* set flag to disallow access to orders */
			$access_denied = true;
		}


		/*******************
			we are logged in
			with protection enabled (default)
			but do not have the required capabilities
		*******************/
		if (!empty($protected) && is_user_logged_in() && empty($current_user->allcaps['wppizza_cap_orderhistory'])){
			$login_form = __('Sorry, you are not allowed to access this page.');

			/* set flag to disallow access to orders */
			$access_denied = true;
		}

		/***************************************************
			we are logged in
			or bypassing protection altogether

			=> get orders

		***************************************************/
		if( ($protected === false || is_user_logged_in()) && empty($access_denied)){

			/*
				query args
				get completed orders only for all users
				omitting failed, cancelled etc
			*/
			$args = array(
				'query'=>array(
					'payment_status' => 'COMPLETED',
				),
				'pagination' =>array(
					'paged' => 	(isset($_GET['pg']) ? $_GET['pg'] : 0) ,
					'limit' => (( !empty($atts['maxpp']) && (int)$atts['maxpp']>0) ? (int)$atts['maxpp'] : $wppizza_options['settings']['admin_order_history_max_results'] ) ,
				),
				'format' => true,
			);
			$orders_completed = WPPIZZA()->db->get_orders($args, $type);

			/*
				do we have any orders ?
			*/
			$has_orders = empty($orders_completed['total_number_of_orders']) ? false : true;


			/*
				no orders
			*/
			if(empty($has_orders)){

				$no_orders = '<p id="' . $id_noorders . '" class="' . $class_noorders . '">'. __('no orders found','wppizza-admin') .'</p>';

			}

			if(!empty($has_orders)){
			/*
				got orders
			*/

				/*
					get pagination if not hidden by attribute
				*/
				if($show_pagination){
					$pagination = self::orderhistory_pagination($orders_completed['total_number_of_orders'], $args['pagination']['limit'], 2, false, $set_post_id);
				}


				/***************************
					loop through orders
				***************************/

				foreach($orders_completed['orders'] as $uoKey => $order_formatted){

					/***
					#	multisite info blog name if parent and enabled
					#	(added before total)
					***/
					$blog_name = '';//.print_r($order_formatted, true);
					if(is_multisite() && !empty($order_formatted['site']['site_name']['value_formatted']) && $get_blog_id == 1 && !empty($wppizza_options['settings']['wp_multisite_order_history_all_sites'])){
						$blog_name= "<div class='".WPPIZZA_PREFIX."-blogname'>".$order_formatted['site']['site_name']['value_formatted']."</div> ";
					}


					/***
					#	dates
					***/
					$order_date = ($order_formatted['ordervars']['order_date']['value'] != '0000-00-00 00:00:00' ) ? date('d M, H:i' ,strtotime($order_formatted['ordervars']['order_date']['value'])) : '&nbsp;' ;
					$order_date = '<div class="'.WPPIZZA_PREFIX.'-'.$order_formatted['ordervars']['order_date']['class_ident'].'">'.$order_date.'</div>';
					$order_update = ($order_formatted['ordervars']['order_update']['value'] != '0000-00-00 00:00:00' ) ? date('d M, H:i' ,strtotime($order_formatted['ordervars']['order_update']['value'])) : '&nbsp;' ;
					$order_update = '<div class="'.WPPIZZA_PREFIX.'-'.$order_formatted['ordervars']['order_update']['class_ident'].'">'.$order_update.'</div>';

					/***
					#	name
					***/
					$name = array();
					foreach($name_keys as $nkey){
						if(!empty($order_formatted['customer'][$nkey]['value'])){
							$name[] = '<span class="'.$nkey.'">'.$order_formatted['customer'][$nkey]['value'].'</span>';
						}
					}
					$name = !empty($name) ? '<div class="'.WPPIZZA_PREFIX.'-customer-name">'.implode(' ',$name).'</div>' : '';

					/***
					#	address
					***/
					$address = array();
					$address_raw = array();

					foreach($address_keys as $akey){
						if(!empty($order_formatted['customer'][$akey]['value'])){
							$address[] = '<span class="'.$akey.'">'.implode(', ',explode(PHP_EOL, $order_formatted['customer'][$akey]['value'])).'</span>';
							$address_raw[] = ''.implode(', ',explode(PHP_EOL, $order_formatted['customer'][$akey]['value'])).'';
						}
					}
					$address = !empty($address) ? implode(', ',$address) : '';
					$address_raw = !empty($address_raw) ? implode(', ',$address_raw) : '';
					$address = !empty($address) ? '<div class="'.WPPIZZA_PREFIX.'-customer-address"><a href="https://www.google.com/maps/search/'.urlencode(esc_html($address_raw)).'" target="_blank">'.$address.'</a></div>' : '';

					/***
					#	total, payment
					***/
					$total = '<div class="'.WPPIZZA_PREFIX.'-total"><span>'.$order_formatted['ordervars']['total']['value_formatted'].'</span></div>' ;
					$payment_type = '<div class="'.WPPIZZA_PREFIX.'-payment-type">'.$order_formatted['ordervars']['payment_type']['value_formatted'].'</div>' ;

					/***
					#	status, type
					***/
					$excluded_status = apply_filters('wppizza_filter_pages_admin_orderhistory_status', array('REFUNDED', 'REJECTED', 'OTHER'));
					$order_status = WPPIZZA() -> admin_helper -> orderhistory_order_status_select($type, 'compact', $uoKey, $order_formatted['ordervars']['order_status']['value'], $excluded_status);
					$order_status = '<div class="'.WPPIZZA_PREFIX.'-order-status">'.$order_status.'</div>';
					//type
					$order_type ='<div class="'.WPPIZZA_PREFIX.'-order-type">'.$order_formatted['ordervars']['order_type']['value_formatted'].'</div>';

					/***
					#	order view (print)
					***/
					$print_class = empty($print_view) ? 'view' : 'print'; //determines if js launches print screen or just view order
					$order_view ='<span id="'.WPPIZZA_PREFIX.'-order-print-'.$uoKey.'" class="'.WPPIZZA_PREFIX.'-order-'.$print_class.' '.WPPIZZA_PREFIX.'-dashicon dashicons dashicons-media-document" title="'.__('view / print order', 'wppizza-admin').'"></span>';

					/*************************************************
					*
					*	set columns to use in template
					*
					*************************************************/
					$order_history[$uoKey] = array();

					$order_history[$uoKey]['uoKey'] = ''.WPPIZZA_PREFIX.'-order-'.$uoKey.'';

					$order_history[$uoKey]['class'] = ''.WPPIZZA_PREFIX.'-status-'.$order_formatted['ordervars']['order_status']['value'].'';

					$order_history[$uoKey]['blog_name'] = ''.$blog_name.'';

					$order_history[$uoKey]['order_date'] = ''.$order_date.'';

					$order_history[$uoKey]['order_update'] = ''.$order_update.'';

					$order_history[$uoKey]['name'] = ''.$name.'';

					$order_history[$uoKey]['address'] = ''.$address.'';

					$order_history[$uoKey]['total'] = ''.$total.'';

					$order_history[$uoKey]['payment_type'] = ''.$payment_type.'';

					$order_history[$uoKey]['order_status'] = ''.$order_status.'';

					$order_history[$uoKey]['order_type'] = ''.$order_type.'';

					$order_history[$uoKey]['order_view'] = ''.$order_view.'';

					$order_history[$uoKey]['order_formatted'] = $order_formatted;

				}

			}

			/*
				if we are showing orders, we need to also pass the shortcode attributes to the js when polling
			*/
			$shortcode_attributes = "<input type='hidden' class='".WPPIZZA_PREFIX."-".$constant."-attributes' value='".esc_html(json_encode($atts))."' />";
		}

		/*************************************

			markup

		*************************************/
		/*
			ini markup array
		*/
		$markup = array();

		/*
			get markup
		*/
		if(file_exists( WPPIZZA_TEMPLATE_DIR . '/markup/pages/page.admin-order-history.php')){
			require(WPPIZZA_TEMPLATE_DIR.'/markup/pages/page.admin-order-history.php');
		}else{
			require(WPPIZZA_PATH.'templates/markup/pages/page.admin-order-history.php');
		}
		/*
			apply filter if required and implode for output
		*/
		$markup = apply_filters('wppizza_filter_pages_shortcode_orderhistory_markup', $markup, $has_orders, $order_history);
		$markup = implode('', $markup);
		/*
			add attributes if logged in or not protected in the first place
		*/
		$markup .= !empty($shortcode_attributes) ?  $shortcode_attributes : '' ;

		/*
			add a non-removable wrapper to ajax into so to speak on load
			and include relevant js
		*/
		if(!defined('DOING_AJAX') || !DOING_AJAX){
			/*
				current_post_id passed on to ajax request (must be last part of element id) to create the right pagination links to this page
				skip surrounding div to disable polling if we need to be logged in first
			*/
			$current_post_id = get_the_ID();
			$markup = empty($access_denied) ? '<div id="'.WPPIZZA_PREFIX.'-'.$constant.'-'.$shortcode_id.'-'.$current_post_id.'" class="'.WPPIZZA_PREFIX.'-'.$constant.'">'.$markup.'</div>' : $markup ;
			/*
				enqueue js to poll orders, change status, view/print order etc etc
			*/
			$js_filename='scripts.orderhistory.min.js';
			$js_enqueue_ident=''.WPPIZZA_SLUG.'-orderhistory';
    		wp_register_script($js_enqueue_ident, WPPIZZA_URL.'js/'.$js_filename , array('jquery'), WPPIZZA_VERSION, true);
			wp_enqueue_script($js_enqueue_ident);

		}


	return $markup;
	}


/******************************************************************************************************************************
#
#
#
#	[helpers]
#
#
#
******************************************************************************************************************************/
	function orderhistory_pagination($no_of_orders, $maxpp, $ellipsis = false, $pagination_info = true, $post_id = false){
		global $wppizza_options;

		/* skip if fewer orders than maxpp */
		if($no_of_orders<=$maxpp){
			$markup = '';
			return $markup ;
		}

		$total_pages=ceil($no_of_orders/$maxpp);
		$currentPageLink = empty($post_id) ? get_permalink() : get_permalink($post_id);//allow ajax calls for example to pass on page links
		$current_page = empty($_GET['pg']) ? 1 : (int)$_GET['pg'];
		$current_orders_from = ($current_page==1) ? $current_page : ( ($current_page==$total_pages) ? (($current_page-1)*$maxpp+1) : (($current_page-1)*$maxpp+1) );
		$current_orders_to = ($current_page==1) ? $maxpp : ( ($current_page==$total_pages) ? $no_of_orders : ($current_page*$maxpp) );


		$markup = array();

		$markup['div_'] = '<div class="'.WPPIZZA_PREFIX.'-history-pagination">';
			/*
				previous link
			*/
			if($current_page>1){
				$link= esc_url_raw(add_query_arg(array('pg' => ($current_page-1)), $currentPageLink ));
				$markup['previous'] = '<a href="'.$link.'" class="'.WPPIZZA_PREFIX.'-history-pagination-previous">'.__('&laquo; Previous Page').'</a>';
			}else{
				$markup['previous_disabled'] = '<a class="'.WPPIZZA_PREFIX.'-history-pagination-previous '.WPPIZZA_PREFIX.'-history-pagination-disabled" disabled="disabled">'.__('&laquo; Previous Page').'</a>';
			}

			/*
				page links - limited
			*/
			if((int)$ellipsis > 0 ){
				for($i=1;$i<=$total_pages;$i++){

					/* links/buttons */
					if($i <= $ellipsis || $i > $total_pages - $ellipsis || $i==$current_page || ($i>=($current_page-$ellipsis) && $i < $current_page) || ($i<=($current_page+$ellipsis) && $i > $current_page) ){

						if($current_page==$i){
							$markup[$i]= '<a href="javascript:void(0)" class="'.WPPIZZA_PREFIX.'-history-pagination-selected">'.$i.'</a>';
						}else{
							$link= esc_url_raw(add_query_arg(array('pg' => $i), $currentPageLink ));
							$markup[$i]= '<a href="'.$link.'" class="'.WPPIZZA_PREFIX.'-history-pagination-count">'.$i.'</a>';
						}

					}else{
						/* dots */
						if($i == ($ellipsis+1) || $i == ($total_pages - $ellipsis - 1) ){
							$markup[$i]= '...';
						}

					}
				}
			}


			/*
				page links - unlimited
			*/
			if(empty($ellipsis)){
				for($i=1;$i<=$total_pages;$i++){
					if($current_page==$i){
						$markup[$i]= '<a href="javascript:void(0)" class="'.WPPIZZA_PREFIX.'-history-pagination-selected">'.$i.'</a>';
					}else{
						$link= esc_url_raw(add_query_arg(array('pg' => $i), $currentPageLink ));
						$markup[$i]= '<a href="'.$link.'" class="'.WPPIZZA_PREFIX.'-history-pagination-count">'.$i.'</a>';
					}
				}
			}

			/*
				next link
			*/
			if($current_page<$total_pages){
				$link= esc_url_raw(add_query_arg(array('pg' => ($current_page+1)), $currentPageLink ));
				$markup['next']= '<a href="'.$link.'" class="'.WPPIZZA_PREFIX.'-history-pagination-next">'.__('Next Page &raquo;').'</a>';
			}else{
				$markup['next_disabled']= '<a class="'.WPPIZZA_PREFIX.'-history-pagination-next '.WPPIZZA_PREFIX.'-history-pagination-disabled" disabled="disabled">'.__('Next Page &raquo;').'</a>';
			}

			/*
				pagination info (no of orders on page) if more than one page
			*/
			if($total_pages>1 && !empty($pagination_info)){
				$markup['info_'] = '<p class="'.WPPIZZA_PREFIX.'-history-pagination-info">';
					$markup['info'] =  sprintf($wppizza_options['localization']['pagination_info'],$current_orders_from, $current_orders_to, $no_of_orders);//  $on_page . ' / ' . $no_of_orders;
				$markup['_info'] = '</p>';
			}


		$markup['_div'] = '</div>';



		/*
			apply filter if required and implode for output
		*/
		$markup = apply_filters('wppizza_filter_pagination_purchasehistory_markup', $markup);
		$markup = implode('', $markup);

	return $markup;
	}


	/*************************************

		global_details. order date, order id, transation id etc etc

	*************************************/
	function order_transaction_details($order, $type){

		/*
			get selected (filterable) parameters
			available keys:
			[wp_user_id] [order_update] [order_delivered] [notes] [payment_gateway] [payment_status] [user_data]
			[ip_address] [order_date] [order_id] [payment_due] [pickup_delivery] [payment_type] [payment_method]
			[transaction_id] [total]
		*/
		$tx_details_keys = array('order_date','payment_type','transaction_id','order_delivered');
		$tx_details_keys = apply_filters('wppizza_filter_transaction_details', $tx_details_keys, $type);

		/*
			omitting empty ones (like order_update) that do not exist yet
			restricted to set keys
		*/
		$transaction_details = array();
		foreach($order as $key=>$parameter){
			if(!empty($parameter['value_formatted']) && in_array($key, $tx_details_keys) ){
				$transaction_details[$key] = array();
				$transaction_details[$key]['class'] = ''.WPPIZZA_PREFIX.'-'.$parameter['class_ident'].'';
				$transaction_details[$key]['label'] = '<label>'.$parameter['label'].'</label>';
				$transaction_details[$key]['value'] = '<span>'.$parameter['value_formatted'].'</span>';
			}
		}

		/*************************************

			markup

		*************************************/
		/*
			ini array
		*/
		$markup = array();
		/*
			get markup
		*/
		if(file_exists( WPPIZZA_TEMPLATE_DIR . '/markup/order/transaction_details.php')){
			require(WPPIZZA_TEMPLATE_DIR.'/markup/order/transaction_details.php');
		}else{
			require(WPPIZZA_PATH.'templates/markup/order/transaction_details.php');
		}

		/*
			apply filter if required (return $markup array will be imploded for output)
		*/
		$markup = apply_filters('wppizza_filter_order_transactiondetails_markup', $markup, $transaction_details, $type);

		$markup = implode('', $markup);

	return $markup;
	}

	/*************************************

		pickup_note

	*************************************/
	function pickup_note_markup($order_formatted, $type){
		$vals = $order_formatted['ordervars']['pickup_delivery'];

		/* nothing set */
		if(empty($vals['value_formatted'])){return '';}

		$class = ''.WPPIZZA_PREFIX.'-'.$vals['class_ident'].'';
		$pickup_delivery_message = $vals['value_formatted'];

		/*************************************

			markup

		*************************************/
		/*
			ini array
		*/
		$markup = array();
		/*
			get markup
		*/
		if(file_exists( WPPIZZA_TEMPLATE_DIR . '/markup/global/pages.pickup_note.php')){
			require(WPPIZZA_TEMPLATE_DIR.'/markup/global/pages.pickup_note.php');
		}else{
			require(WPPIZZA_PATH.'templates/markup/global/pages.pickup_note.php');
		}
		/*
			apply filter if required and implode for output
		*/
		$markup = apply_filters('wppizza_filter_pages_pickup_note_markup', $markup);
		$markup = implode('', $markup);


	return $markup;
	}

	/*************************************

		inline payment details markup
		if wppizza_gateways_inline_elements_{gateway ident} filter was added
		by a gateways
		@param str
		@retun str
		@since 3.6.1
	*************************************/
	function payment_details_markup($type){


		$selected_gateway_ident = WPPIZZA()->session->get_selected_gateway();

		$markup = '';

		if(has_filter('wppizza_gateways_inline_elements_'.$selected_gateway_ident)){
			/*
				order page or confirmation page conditionals
			*/
			global $wppizza_options;
			if( ( $type == 'orderpage' && empty($wppizza_options['confirmation_form']['confirmation_form_enabled'])) || $type == 'confirmationpage' ){

				$payment_details_fieldset_id = ''.WPPIZZA_PREFIX.'-payment-details';
				$payment_details_fieldset_class = ''.WPPIZZA_PREFIX.'-fieldset '.WPPIZZA_PREFIX.'-payment-details';
				$payment_details_ssl_lock = is_ssl() ? '<span class="'.WPPIZZA_PREFIX.'-dashicon dashicons dashicons-lock"></span>' : '' ;
				$payment_details = implode('', apply_filters('wppizza_gateways_inline_elements_'.$selected_gateway_ident, array()));


				/*
					ini array
				*/
				$markup = array();

				/*
					get markup
				*/
				if(file_exists( WPPIZZA_TEMPLATE_DIR . '/markup/order/payment_details.php')){
					require(WPPIZZA_TEMPLATE_DIR.'/markup/order/payment_details.php');
				}else{
					require(WPPIZZA_PATH.'templates/markup/order/payment_details.php');
				}

				/*
					apply filter if required and implode for output
				*/
				$markup = apply_filters('wppizza_filter_order_payment_details_markup', $markup, $selected_gateway_ident);
				$markup = implode('', $markup);

			}
		}

	return $markup;
	}




	/*************************************

		user login filter, removing
		various elements of the login screen
		if we are displaying admin order history
		by shortcode somewhere
	*************************************/
	function admin_orderhistory_login_markup($markup){
		/*
			only return basic login markup
			without the fancy stuff
		*/
		$base_login = array();
		$base_login['login'] = $markup['login'];

	return $base_login;
	}
}
?>