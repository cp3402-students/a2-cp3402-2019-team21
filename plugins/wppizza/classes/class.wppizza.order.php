<?php
/**
* WPPIZZA_ORDER Class
*
* @package     WPPIZZA
* @subpackage  WPPIZZA_ORDER
* @copyright   Copyright (c) 2015, Oliver Bach
* @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
* @since       3.0
*
*/
if ( ! defined( 'ABSPATH' ) ) exit;/*Exit if accessed directly*/


/************************************************************************************************************************
*
*
*	WPPIZZA_ORDER
*
*
************************************************************************************************************************/
class WPPIZZA_ORDER{
	function __construct() {

	}
	/* order formatted from session */
	public static function session_formatted($caller = 'session'){
		global $wppizza_options, $blog_id;
		static $order_details_formatted = null;

		if($order_details_formatted == null){

			/* get user data session */
			$user_session	= WPPIZZA()->session->get_userdata();
			/* map session data to db fields and also return various other parameters we can use separately (is_open, is_checkout etc etc)*/
			$mapped_cart	= WPPIZZA()->db->map_order($user_session);

			/* if cart is empty (i.e cannot checkout as theres nothing to checkout with), bail */
			if(empty($mapped_cart)){return;}

			/* create object for formatting */
			$order_values = array();
			/*
				on orderpage: if a new db entry is be added,
				the order id will be added to the user session
				we can now pass on (some gateways using overlays might need this), else we set the id to be -1 to start off with
			*/
			$order_values['id'] = !empty($user_session[''.WPPIZZA_SLUG.'_order_id']) ? $user_session[''.WPPIZZA_SLUG.'_order_id'] : -1; /* not yet known in session until inserted into db */


			foreach($mapped_cart['data'] as $key=>$var){
				$order_values[$key] = maybe_unserialize($var);
			}
			$order_values['date_format'] = array('date' => get_option('date_format'), 'time' => get_option('time_format'));
			$order_values['blog_info'] = WPPIZZA() -> helpers -> wppizza_blog_details($blog_id);
			$order_values['blog_options'] = $wppizza_options;

			/*
				format
			*/
			$order_details_formatted = array();

			/* various helpful session parameters (can_checkout, is_checkout, min_order_required, shop_open, is_pickup) if getting details from session */
			$order_details_formatted['checkout_parameters'] = $mapped_cart['checkout_parameters'];

			/* blog_options  */
			$order_details_formatted['blog_options'] = 	self::blog_options($order_values, false, $caller);

			/* customer confirmation form inputs - formatted */
			$order_details_formatted['confirmation'] = 	self::customer_confirmation_formatted(false, $caller);

			/* order siteinfo - formatted */
			$order_details_formatted['site'] = 			self::site_details_formatted($order_values, false, $caller);

			/* general order details - formatted */
			$order_details_formatted['ordervars'] = 	self::general_ordervars_formatted($order_values, false, $caller);

			/* customer details - formatted */
			$order_details_formatted['customer'] = 		self::customer_details_formatted($order_values, false, $caller);

			/* order itemised - formatted */
			$order_details_formatted['order'] = 		self::itemised_details_formatted($order_values, false, $caller);

			/* order summary - formatted */
			$order_details_formatted['summary'] = 		self::summary_details_formatted($order_values, false, $caller);


		}

	return $order_details_formatted;
	}

	/******************************************************************
	*
	*	format get_orders query results
	*	@param array()
	*	@param str (will contain function name that called this function - could be used for dbugging purpose)
	*	@since 3.9
	*
	******************************************************************/
	function orders_formatted($order_values = false, $template_args = false, $caller = false){


		/******************************
		#
		#	ini formated output array
		#
		******************************/
		$order_details_formatted = array();


		/******************************
		#	global settings - templates only
		******************************/
		if(!empty($template_args)){
			$order_details_formatted = 							self::global_parameters($order_values, $template_args, $caller);
		}

		/******************************
		#	global styles - templates only
		******************************/
		if(!empty($template_args)){
			$order_details_formatted['global_styles'] = 		self::global_styles($order_values, $template_args, $caller);
		}

		/*******************************
		#	get checkout parameters
		#	use/add parameters to order_details_formatted data
		#	from current user session data
		*******************************/
		$is_checkout = wppizza_is_checkout();
		$has_orderpage_widget = wppizza_has_orderpage_widget();
		if($is_checkout || $has_orderpage_widget){
			$user_session	= WPPIZZA()->session->get_userdata();
			$checkout_parameters	= WPPIZZA()->db->map_order($user_session, true);
			if(!empty($checkout_parameters)){
				$order_details_formatted['checkout_parameters'] = $checkout_parameters;
			}
		}

		/******************************
		#	blog_options - NON templates only
		******************************/
		if(!empty($order_values)){
			$order_details_formatted['blog_options'] = 			self::blog_options($order_values, $template_args, $caller);
		}

		/******************************
		#	customer confirmation form inputs - formatted - skip for templates
		******************************/
		if(empty($template_args)){
			$order_details_formatted['confirmation'] = 			self::customer_confirmation_formatted($template_args, $caller);
		}

		/******************************
		#	order details - sections
		*******************************/

		/* siteinfo */
		$order_details_formatted['sections']['site'] = 			self::site_details_formatted($order_values, $template_args, $caller);

		/* general order details */
		$order_details_formatted['sections']['ordervars'] = 	self::general_ordervars_formatted($order_values, $template_args, $caller);

		/* customer details */
		$order_details_formatted['sections']['customer'] = 		self::customer_details_formatted($order_values, $template_args, $caller);

		/* order itemised */
		$order_details_formatted['sections']['order'] = 		self::itemised_details_formatted($order_values, $template_args, $caller);

		/* order summary */
		$order_details_formatted['sections']['summary'] = 		self::summary_details_formatted($order_values, $template_args, $caller);


		/*****************************************
			allow filtering - skip for templates
		*****************************************/
		if(empty($template_args)){
			$order_details_formatted = apply_filters('wppizza_filter_order_details_formatted', $order_details_formatted, $order_values['order_ini'], $order_values['customer_ini'], $order_values) ;
		}


		/*****************************************
			templates - install only
			default email and print templates
		*****************************************/
		if(!empty($template_args['tpl_install'])){

			$template_defaults = array();
			$template_defaults['sort'] = array();
			$template_defaults['title'] = $order_details_formatted['title'];//from global_parameters
			$template_defaults['mail_type'] = $order_details_formatted['mail_type'];//from global_parameters
			$template_defaults['omit_attachments'] = false;
			$template_defaults['recipients_additional'] = array();
			$template_defaults['global_styles'] = $order_details_formatted['global_styles'];
			$template_defaults['sections'] = array();

			foreach($order_details_formatted['sections'] as $section_key=>$section_values){
				/* sort */
				$template_defaults['sort'][$section_key] = array();

				/* style */
				$template_defaults['sections'][$section_key]['style'] = !empty($section_values['style']) ? $section_values['style'] : array();

				/* section enabled */
				if(!empty($section_values['section_enabled'])){
					$template_defaults['sections'][$section_key]['section_enabled'] = true;
				}

				/* label enabled */
				if(!empty($section_values['label_enabled'])){
					$template_defaults['sections'][$section_key]['label_enabled'] = true;
				}

				/* parameters ini array */
				$template_defaults['sections'][$section_key]['parameters'] = array();

				if(!empty($section_values['parameters'])){
					foreach($section_values['parameters'] as $section_paramater_key=>$section_paramater_value){

						/* sort */
						$template_defaults['sort'][$section_key][$section_paramater_key] = true;

						/* parameters enabled */
						if(!empty($section_paramater_value['enabled'])){
							$template_defaults['sections'][$section_key]['parameters'][$section_paramater_key]['enabled'] = true;
						}

					}
				}
			}
		/* return and insert into options table */
		return $template_defaults;
		}
		/*****************************************
			end templates install
		*****************************************/

	return $order_details_formatted;
	}

	/******************************************************************
	*
	*	simplify order results array
	*	only return things we might actually really need
	*	by default keeping localization vars
	******************************************************************/
	function simplify_order_values($order_results, $blog_options = false, $sections = false, $registered_userdata = false){

		/****************************************
		*
		*	simplify site data
		*
		****************************************/
		foreach($order_results['sections']['site'] as $sKey => $sArr){
			$order_results['sections']['site'][$sKey] = array();
			$order_results['sections']['site'][$sKey]['label'] = $sArr['label'];
			$order_results['sections']['site'][$sKey]['value'] = $sArr['value'];
			$order_results['sections']['site'][$sKey]['value_formatted'] = $sArr['value_formatted'];
			$order_results['sections']['site'][$sKey]['class_ident'] = !empty($sArr['class_ident']) ? $sArr['class_ident'] : '' ;
		}

		/****************************************
		*
		*	simplify ordervars data
		*
		****************************************/
		foreach($order_results['sections']['ordervars'] as $sKey => $sArr){
			$order_results['sections']['ordervars'][$sKey] = array();
			$order_results['sections']['ordervars'][$sKey]['label'] = $sArr['label'];
			$order_results['sections']['ordervars'][$sKey]['value'] = $sArr['value'];
			$order_results['sections']['ordervars'][$sKey]['value_formatted'] = $sArr['value_formatted'];
			$order_results['sections']['ordervars'][$sKey]['class_ident'] = !empty($sArr['class_ident']) ? $sArr['class_ident'] : '' ;
		}

		/****************************************
		*
		*	simplify customer data - also adding
		* 	required_attribute, required_class, placeholder and html
		* 	here
		*
		****************************************/
		foreach($order_results['sections']['customer'] as $cKey => $cArr){
			$order_results['sections']['customer'][$cKey] = array();
			$order_results['sections']['customer'][$cKey]['label'] = $cArr['label'];
			$order_results['sections']['customer'][$cKey]['value'] = is_array($cArr['value']) ? implode(', ', $cArr['value']) : $cArr['value'];
			$order_results['sections']['customer'][$cKey]['type'] = !empty($cArr['type']) ? $cArr['type'] : '';//in some queries type will not exixt
			$order_results['sections']['customer'][$cKey]['class_ident'] = !empty($cArr['class_ident']) ? $cArr['class_ident'] : '' ;
			$order_results['sections']['customer'][$cKey]['required_attribute'] = !empty($cArr['required_attribute']) ? $cArr['required_attribute'] : '' ;
			$order_results['sections']['customer'][$cKey]['required_class'] = !empty($cArr['required_class']) ? $cArr['required_class'] : '' ;
			$order_results['sections']['customer'][$cKey]['options'] = !empty($cArr['options']) ? $cArr['options'] : array() ;
			$order_results['sections']['customer'][$cKey]['placeholder'] = !empty($cArr['placeholder']) ? $cArr['placeholder'] : '' ;
			$order_results['sections']['customer'][$cKey]['html'] = !empty($cArr['html']) ? $cArr['html'] : '' ;
		}

		/* unset tips as they are available in and belong to summary */
		if(isset($order_results['sections']['customer']['ctips'])){
			unset($order_results['sections']['customer']['ctips']);
		}

		/****************************************
		*
		*	add mapped user data
		*
		****************************************/
		/****************************************
		*
		*	add mapped user data - only if specifically enabled in query
		*
		****************************************/
		if(!empty($registered_userdata)){

			/*********
				user logged in
			**********/
			if($order_results['sections']['ordervars']['wp_user_id']['value']>0){/* we are logged in , lets see what we can fill in*/
				/*
					get user data
				*/
				$getUserData = get_userdata($order_results['sections']['ordervars']['wp_user_id']['value']);
				/*
					in case user was deleted
				*/
				if(empty($getUserData)){
					$getUserData = new stdClass();
					$getUserData->user_login = '';
					$getUserData->first_name = '';
					$getUserData->last_name = '';
					$getUserData->user_email = '';
				}

				/*
					add to array
				*/
				$order_results['sections']['customer']['login'] = array('label' => __('Login'), 'value' => $getUserData->user_login, 'type' =>'text');
				$order_results['sections']['customer']['first_name'] = array('label' => __('First Name'), 'value' => $getUserData->first_name, 'type' =>'text');
				$order_results['sections']['customer']['last_name'] = array('label' => __('Last Name'), 'value' => $getUserData->last_name, 'type' =>'text');
				$order_results['sections']['customer']['email'] = array('label' => __('Email'), 'value' => $getUserData->user_email, 'type' =>'email');
			}

			/*********
				user not logged in, try filling first name from cname
				provided it's set and type text or textarea
			**********/
			if(empty($order_results['sections']['ordervars']['wp_user_id']['value'])){
				if(isset($order_results['sections']['customer']['cname']) && ( in_array($order_results['sections']['customer']['cname']['type'], array('text', 'textarea')) || substr($order_results['sections']['customer']['cname']['type'],0,10)=='text_size_' ) ){
					$array=explode(" ",trim($order_results['sections']['customer']['cname']['value']));
					$arLen=count($array);
					if(isset($array[0])){
						$order_results['sections']['customer']['first_name'] = array('label' => __('First Name'), 'value' => trim($array[0]), 'type' =>'text', 'class_ident' => 'wp_first_name');
					}
					if($arLen>1){
						$order_results['sections']['customer']['last_name'] = array('label' => __('Last Name'), 'value' => trim($array[($arLen-1)]), 'type' =>'text', 'class_ident' => 'wp_last_name');
					}
				}
			}
			/*********
				override email if set
			**********/
			if(isset($order_results['sections']['customer']['cemail']['value'])){
				$order_results['sections']['customer']['email'] =  array('label' => $order_results['sections']['customer']['cemail']['label'], 'value' => $order_results['sections']['customer']['cemail']['value'], 'type' =>'email', 'class_ident' => 'wp_email');
			}

		}

		/****************************************
		*
		*	simplify summary data
		*
		****************************************/
		foreach($order_results['sections']['summary'] as $sKey => $sArr){
			$order_results['sections']['summary'][$sKey] = array();
			foreach($sArr as $cKey => $cArr){
				$order_results['sections']['summary'][$sKey][$cKey] = array();
				$order_results['sections']['summary'][$sKey][$cKey]['label'] = $cArr['label'];
				$order_results['sections']['summary'][$sKey][$cKey]['value'] = $cArr['value'];
				$order_results['sections']['summary'][$sKey][$cKey]['value_formatted'] = $cArr['value_formatted'];
				$order_results['sections']['summary'][$sKey][$cKey]['class_ident'] = !empty($cArr['class_ident']) ? $cArr['class_ident'] : '' ;
			}
		}

		/***********************************************************************
		*
		*	only return things we might actually need
		*
		************************************************************************/
		if(empty($sections)){
			$results = $order_results['sections'];
		}else{
			$results = array();
			$results['sections'] = $order_results['sections']; //optionally leave as section array
		}

		/*
			add full or partial blog_options if enabled by args or localization only if set
		*/
		if(!empty($blog_options)){
			/* distinctly adding various blog options */
			if($blog_options !== true){
				/*
					distinctly adding localization
					(can be expanded to include other blog options too if needs be one day)
				*/
				if(is_array($blog_options)){
					foreach($blog_options as $mod){
						/* add localization distinctly*/
						if($mod == 'localization'){
							$results['localization'] = $order_results['blog_options']['localization'] + $order_results['blog_options']['confirmation_form']['localization'];
						}
						/* add blog_info distinctly*/
						if($mod == 'blog_info'){
							$results['blog_info'] = $order_results['blog_info'];
						}
						/* add date_format distinctly*/
						if($mod == 'date_format'){
							$results['date_format'] = $order_results['date_format'];
						}
						/* add full blog_options distinctly as well/instead if needed */
						if($mod == 'blog_options'){
							$results['blog_options'] = $order_results['blog_options'];
						}
						/* add checkout_parameters */
						if($mod == 'checkout_parameters'){
							$results['checkout_parameters'] = $order_results['checkout_parameters'];
						}
						/* add confirmation */
						if($mod == 'confirmation'){
							$results['confirmation'] = $order_results['confirmation'];
						}
					}
				}
			}else{
				/* adding full blog options, skipping localization as it's in blog_options already  */
				$results['blog_options'] = $order_results['blog_options'];
				$results['blog_info'] = $order_results['blog_info'];
				$results['date_format'] = $order_results['date_format'];
				$results['checkout_parameters'] = $order_results['checkout_parameters'];
				$results['confirmation'] = $order_results['confirmation'];
			}
		}

		/* mini tidy up */
		unset($order_results);

	return $results ;
	}


/********************************************************************************************************************************************************************
*
*
*	HELPERS - Mapping All Variables
*
*
*********************************************************************************************************************************************************************/

public static function global_parameters($order = false, $tpl_args = false, $caller = false){
	global $wppizza_options;


	$templates_ident = 'templates';


	/*****************************************
	#
	#	if getting template values
	#
	*****************************************/
	/* ascertain if we only need to get get_template_parameters */
	//$get_template_parameters = !empty($tpl_args) ? true : false ;// not required here as function only runs for templates anyway
	/* get template id  */
	$template_id = !empty($tpl_args['tpl_id']) ? $tpl_args['tpl_id'] : 1 ;
	/* get template type (emails/print) */
	$template_type = !empty($tpl_args['tpl_type']) ? $tpl_args['tpl_type'] : false ;
	/* get template values */
	$template_values = !empty($tpl_args['tpl_values']) ? $tpl_args['tpl_values'] : false ;



	$template_parameters = array();


	/*
		default or selected values for email templates only
	*/
	if( $template_type == 'emails' ){
		/*
			default recipients available (email shop/bcc, email customer)
		*/
		$template_parameters['recipients'] = WPPIZZA()->helpers->default_email_recipients();
		/*
			selected template for default recipients
		*/
		$template_parameters['recipients_default_selected'] = $wppizza_options['templates_apply'][$template_type]['recipients_default'] ;
		/*
			additional recipients
		*/
		$template_parameters['recipients_additional']= (empty($template_values['recipients_additional'])) ? '' : implode(',',$template_values['recipients_additional']) ;
		/*
			omit_attachments
		*/
		$template_parameters['omit_attachments'] = (empty($template_values['omit_attachments']) ) ? false  : true ;
	}


	/*
		default or selected values for print templates only
	*/
	if( $template_type == 'print' ){
		/*
			is this the print template in use ?
		*/
		$template_parameters['print_id'] = (empty($template_values)) ? false  : ( $wppizza_options['templates_apply'][$template_type] == $template_id ? true : false) ;


	}

	/*
		common default values print or emails
	*/
	if( in_array($template_type, array('print','emails'))){

		/*
			class to identify we are adding a new template
		*/
		$template_parameters['new_class'] = (empty($template_values)) ? ''.WPPIZZA_SLUG.'-'.$templates_ident.'-new' : '' ;
		/*
			admin sort order
		*/
		//$template_parameters['admin_sort'] = (empty($template_values)) ? '' : $template_values['admin_sort'] ;
		/*
			title - if ID ==0 title == default (on install)
		*/
		$template_parameters['title'] = (empty($template_values)) ? ( ($template_id == 0) ? __('default', 'wppizza-admin').'' : __('new', 'wppizza-admin').' [ID:'.$template_id.'] ') : $template_values['title'] ;
		/*
			mail/display type, icon classes
		*/
		$template_parameters['mail_type'] = (empty($template_values) ) ? 'phpmailer'  : $template_values['mail_type'] ;
		/* icon active/inactive class */
		$template_parameters['htmlactiveclass'] = ($template_parameters['mail_type']=='phpmailer') ? ''.WPPIZZA_SLUG.'_'.$templates_ident.'_style_toggle '.WPPIZZA_SLUG.'-dashicons-'.$templates_ident.'-'.$template_type.'-media-code' : ' '.WPPIZZA_SLUG.'-dashicons-'.$templates_ident.'-'.$template_type.'-media-code-inactive' ;
		/* icon active/inactive title */
		$template_parameters['htmlactivetitle'] = ($template_parameters['mail_type']=='phpmailer') ? __('toggle style input','wppizza-admin') : __('N/A while plaintext template','wppizza-admin') ;
	}


return $template_parameters;
}
/*******************************************
*
*	Map global styles
*
********************************************/
public static function global_styles($order = false, $tpl_args = false, $caller = false){

	$section_key = 'global_styles';


	/*****************************************
	#
	#	if getting template values
	#
	*****************************************/
	/* ascertain if we only need to get get_template_parameters */
	//$get_template_parameters = !empty($tpl_args) ? true : false ;// not required here as function only runs for templates anyway
	/* get template id  */
	$template_id = !empty($tpl_args['tpl_id']) ? $tpl_args['tpl_id'] : false ;
	/* get template type (emails/print) */
	$template_type = !empty($tpl_args['tpl_type']) ? $tpl_args['tpl_type'] : false ;
	/* get template values */
	$template_values = !empty($tpl_args['tpl_values']) ? $tpl_args['tpl_values'] : false ;


	$template_parameters = array();
	/*
		global section styles
	*/
	$template_parameters = array();

	if($template_type=='emails'){
    	$template_parameters['body'] = 'margin: 0px; background-color: #FFFFFF; font-size: 14px; color: #444444; font-family: Verdana, Helvetica, Arial, sans-serif;';
    	$template_parameters['wrapper'] = 'margin:10px 0; width:100%;';
    	$template_parameters['table'] = 'width: 500px; margin: 0 auto; border: 1px dotted #CECECE; background: #F4F3F4;';
	}

	if($template_type=='print'){
		$printCss=array();
		$printCss[]='html,body,table,tbody,tr,td,th,span{font-size:12px;font-family:Arial, Verdana, Helvetica, sans-serif;margin:0;padding:0;text-align:left;}';
		$printCss[]='table{width:100%;margin:0 0 10px 0;}';
		$printCss[]='th{padding:5px;border-top:2px solid;border-bottom:2px solid;font-size:120%;white-space:nowrap;text-align:center}';
		$printCss[]='td{padding:0 5px;vertical-align:top;white-space:nowrap;}';
		$printCss[]='#header tbody > tr > td{text-align:center;padding-bottom:5px;font-size: 250%;}';
		$printCss[]='#header #site_address td{font-size:130%;white-space: nowrap;padding: 5px 0 ;}';
		$printCss[]='#overview {table-layout: fixed;}';
		$printCss[]='#overview tbody > tr > td{width:50%;word-wrap: break-word;white-space: initial;}';
		$printCss[]='#overview tbody > tr > td:first-child{text-align:right}';
		$printCss[]='#overview tbody > tr > td.td-ctr{text-align:center;width:100%;}';
		$printCss[]='#overview #order_date td {border-top: 2px solid; border-bottom: 2px solid; font-size: 120%; text-align: center;padding: 5px;}';
		$printCss[]='#overview #order_id td{font-size:180%}';
		$printCss[]='#overview #payment_due td{font-size:180%}';
		$printCss[]='#overview #pickup_delivery td{font-size:150%;white-space:normal;text-align:center;width:100%;}';
		$printCss[]='#overview #self_pickup td{font-size: 150%;padding: 10px 0;}';
		$printCss[]='#overview #admin_notes td{white-space:normal;text-align:center; width:100%; padding:10px 0;}';
		$printCss[]='#customer tbody > tr > td{white-space:inherit;}';
		$printCss[]='#customer tbody > tr > td:first-child{white-space:nowrap;}';
		$printCss[]='#order th{text-align:left}';
		$printCss[]='#order th:first-child,#order th:last-child{width:20px;}';
		$printCss[]='#order tbody > tr.items > td{padding-top:5px;font-size:100%}';
		$printCss[]='#order tbody > tr.items > td:first-child{text-align:center;}';
		$printCss[]='#order tbody > tr.items > td:last-child{text-align:right;}';
		$printCss[]='#order tbody > tr.divider > td > hr {border:none;border-top:1px dotted #AAAAAA;}';
		$printCss[]='#order .item-blog td{padding:5px 2px 5px 2px; border-bottom:1px solid;font-weight:600;font-size:120%}';
		$printCss[]='#order .item-category td{padding:5px 2px 2px 2px; border-bottom:1px dashed }';
		$printCss[]='#summary {border-top:1px solid;border-bottom:1px solid;}';
		$printCss[]='#summary tbody > tr > td{text-align:right}';
		$printCss[]='#summary tbody > tr > td:last-child{width:100px}';
		$printCss[]='#footer #footer_note td{text-align:center;width:100%;}';


		$template_parameters['body'] = implode(PHP_EOL,$printCss);

	}


	/*************************************************************************
		overwrite sort order and values with set/saved values if there are any
	*************************************************************************/
	if(!empty($template_values)){

		$template_parameters['body'] = !empty($template_values[$section_key]['body']) ? $template_values[$section_key]['body'] : '';

		if($template_type=='emails'){
			$template_parameters['wrapper'] = !empty($template_values[$section_key]['wrapper']) ? $template_values[$section_key]['wrapper'] : '';
			$template_parameters['table'] = !empty($template_values[$section_key]['table']) ? $template_values[$section_key]['table'] : '';
		}
	}

return $template_parameters;
}
/*******************************************
*
*	get blog options, omit for templates
*	in a multisite setup these are the options
*	of the site where the order _was made_
*	so if diaplying all orders from all sites in the parent page
*	this will be the options of the child blog if the order was initially made there !
********************************************/
public static function blog_options($order = false, $tpl_args = false, $caller = false){
	$blog_options = array();
		/* only if NOT getting template parameters */
		if(empty($tpl_args)){
			$blog_options = $order['blog_options'];
		}
	return $blog_options;
}
/*******************************************
*
*	Map Site/Multisite Details
*
********************************************/
public static function site_details_formatted($order = false, $tpl_args = false, $caller = false){
	global $wppizza_options;


	$section_key = 'site';

	/*
		ini array
	*/
	$site_parameters = array();

	/*****************************************
	#
	#	if getting template values
	#
	*****************************************/
	/* ascertain if we only need to get get_template_parameters */
	$get_template_parameters = !empty($tpl_args) ? true : false ;
	/* get template id  */
	$template_id = !empty($tpl_args['tpl_id']) ? $tpl_args['tpl_id'] : false ;
	/* get template type (emails/print) */
	$template_type = !empty($tpl_args['tpl_type']) ? $tpl_args['tpl_type'] : false ;
	$is_email_template = ( !empty($tpl_args['tpl_type']) && $tpl_args['tpl_type']=='emails' )  ? true : false ;
	/* get template values */
	$template_values = !empty($tpl_args['tpl_values']) ? $tpl_args['tpl_values'] : false ;


	/*
		blog id
	*/
	if($get_template_parameters){
		$site_parameters['blog_id']['template_default_sort']= 50;
		$site_parameters['blog_id']['template_default_enabled']= false;
		$site_parameters['blog_id']['template_parameter']	= false;
		$site_parameters['blog_id']['template_row_default_css'] = '';
	}else{
		$site_parameters['blog_id']['class_ident'] = 'blog-id';
		$site_parameters['blog_id']['value'] = $order['blog_info']['blog_id'];
		$site_parameters['blog_id']['value_formatted'] = $order['blog_info']['blog_id'];
	}
	$site_parameters['blog_id']['label'] = __('blog id','wppizza-admin');


	/*
		site name
	*/
	if($get_template_parameters){
		$site_parameters['site_name']['template_default_sort']= 60;
		$site_parameters['site_name']['template_default_enabled']= true;
		$site_parameters['site_name']['template_parameter']	= true;
		$site_parameters['site_name']['template_row_default_css'] = 'font-size: 160%; font-weight: 600;';
	}else{
		$site_parameters['site_name']['class_ident'] = 'site-name';
		$site_parameters['site_name']['value'] = $order['blog_info']['blogname'];
		$site_parameters['site_name']['value_formatted'] = $order['blog_info']['blogname'];
	}
	$site_parameters['site_name']['label'] = __('site name','wppizza-admin');

	/*
		site url
	*/
	if($get_template_parameters){
		$site_parameters['site_url']['template_default_sort']= 70;
		$site_parameters['site_url']['template_default_enabled']= false;
		$site_parameters['site_url']['template_parameter']	= true;
		$site_parameters['site_url']['template_row_default_css'] = '';
	}else{
		$site_parameters['site_url']['class_ident'] = 'site-url';
		$site_parameters['site_url']['value'] = $order['blog_info']['siteurl'];
		$site_parameters['site_url']['value_formatted'] = $order['blog_info']['siteurl'];
	}
	$site_parameters['site_url']['label'] = __('siteurl','wppizza-admin');


	/*
		site header
	*/
	if($get_template_parameters){
		$site_parameters['site_header']['template_default_sort']= 80;
		$site_parameters['site_header']['template_default_enabled']= false;
		$site_parameters['site_header']['template_parameter']	= true;
		$site_parameters['site_header']['template_row_default_css'] = '';
	}else{
		$site_parameters['site_header']['class_ident'] = 'site-header';
		$site_parameters['site_header']['value'] = $order['blog_options']['localization']['header_order_print_header'];//$localizedVars['header_order_print_header'];
		$site_parameters['site_header']['value_formatted'] = $order['blog_options']['localization']['header_order_print_header'];//$localizedVars['header_order_print_header'];
	}
	$site_parameters['site_header']['label'] = __('header','wppizza-admin');


	/*
		site address
	*/
	if($get_template_parameters){
		$site_parameters['site_address']['template_default_sort']= 90;
		$site_parameters['site_address']['template_default_enabled']= false;
		$site_parameters['site_address']['template_parameter']	= true;
		$site_parameters['site_address']['template_row_default_css'] = '';
	}else{
		$site_parameters['site_address']['class_ident'] = 'site-address';
		$site_parameters['site_address']['value'] = $order['blog_options']['localization']['header_order_print_shop_address'];
		$site_parameters['site_address']['value_formatted'] = $order['blog_options']['localization']['header_order_print_shop_address'];
	}
	$site_parameters['site_address']['label'] = __('address','wppizza-admin');


	/*
		language id
	*/
	if($get_template_parameters){
		$site_parameters['lang_id']['template_default_sort']= 100;
		$site_parameters['lang_id']['template_default_enabled']= false;
		$site_parameters['lang_id']['template_parameter']	= false;
		$site_parameters['lang_id']['template_row_default_css'] = '';
	}else{
		$site_parameters['lang_id']['class_ident'] = 'lang-id';
		$site_parameters['lang_id']['value'] = $order['blog_info']['lang_id'];
		$site_parameters['lang_id']['value_formatted'] = $order['blog_info']['lang_id'];
	}
	$site_parameters['lang_id']['label'] = __('lang id','wppizza-admin');


	/*
		allow filtering
	*/
	$site_parameters = apply_filters('wppizza_filter_site_details_formatted', $site_parameters , $order, $tpl_args);

	/*
		template default sort, enabled param, labels and keys only
	*/
	if($get_template_parameters){
		$template_enabled_parameters = array();
		/*
			global section styles
		*/
		if( $is_email_template ){
		$template_enabled_parameters['style'] = array();
        $template_enabled_parameters['style']['table'] = 'padding: 30px; text-align: center; background-color: #21759B; color: #FFFFFF;';
        $template_enabled_parameters['style']['th'] = '';
        $template_enabled_parameters['style']['td-ctr'] = 'text-align: center';
		}

		/*
			label for section
		*/
		$template_enabled_parameters['labels']['label'] 	= $wppizza_options['localization']['templates_label_'.$section_key.''];
		/*
			section enabled - defaults to true for new templates
		*/
		$template_enabled_parameters['section_enabled'] = true;
		/*
			section label enabled - defaults to false for new templates
		*/
		$template_enabled_parameters['label_enabled'] = false;

		$template_enabled_parameters['parameters'] = array();
		foreach($site_parameters as $site_parameters_key=>$site_parameters_values){
			if(!empty($site_parameters_values['template_parameter'])){
				/* parameters: sort , enabled, label */
				$template_enabled_parameters['parameters'][$site_parameters_key] = array();
				$template_enabled_parameters['parameters'][$site_parameters_key]['sort'] = $site_parameters_values['template_default_sort'];
				$template_enabled_parameters['parameters'][$site_parameters_key]['enabled'] = $site_parameters_values['template_default_enabled'];
				$template_enabled_parameters['parameters'][$site_parameters_key]['label'] = $site_parameters_values['label'];

				/* parameters: template styles */
				if( $is_email_template ){
				$template_enabled_parameters['style'][$site_parameters_key.'-tdall']  				= $site_parameters_values['template_row_default_css'];
				}

			}
		}

		/*************************************************************************
			overwrite sort order and values with set/saved values if there are any
		*************************************************************************/
		if(!empty($template_values)){
			/* global section styles */
			if( $is_email_template ){
        		$template_enabled_parameters['style']['table'] = !empty($template_values['sections'][$section_key]['style']['table']) ? $template_values['sections'][$section_key]['style']['table'] : '';
        		$template_enabled_parameters['style']['th'] = !empty($template_values['sections'][$section_key]['style']['th']) ? $template_values['sections'][$section_key]['style']['th'] : '';
        		$template_enabled_parameters['style']['td-ctr'] = !empty($template_values['sections'][$section_key]['style']['td-ctr']) ? $template_values['sections'][$section_key]['style']['td-ctr'] : '';
			}

			/* section enabled */
			$template_enabled_parameters['section_enabled'] = !empty($template_values['sections'][$section_key]['section_enabled']) ? true : false;
			/* section label enabled */
			$template_enabled_parameters['label_enabled'] = !empty($template_values['sections'][$section_key]['label_enabled']) ? true : false;

			$resort = 0;
			foreach($template_values['sort'][$section_key] as $parameter_key=>$enabled){
				/* resort */
				$template_enabled_parameters['parameters'][$parameter_key]['sort'] = $resort;
				/* enabled ?*/
				$template_enabled_parameters['parameters'][$parameter_key]['enabled'] = !empty($template_values['sections'][$section_key]['parameters'][$parameter_key]['enabled']) ? true : false;
				/* css*/
				if( $is_email_template ){
				$template_enabled_parameters['style'][$parameter_key.'-tdall'] = !empty($template_values['sections'][$section_key]['style'][$parameter_key.'-tdall']) ? $template_values['sections'][$section_key]['style'][$parameter_key.'-tdall'] : '';
				}

			$resort++;
			}
		}


		/* sort */
		if(is_array($template_enabled_parameters['parameters'])){
			asort($template_enabled_parameters['parameters']);
		}


		return $template_enabled_parameters;
	}

return $site_parameters;
}


/*******************************************
*
*	Map General Order Vars
*
********************************************/
public static function general_ordervars_formatted($order = false, $tpl_args = false, $caller = false){

		$section_key = 'ordervars';



		/*****************************************
		#
		#	if getting template values
		#
		*****************************************/
		/* ascertain if we only need to get get_template_parameters */
		$get_template_parameters = !empty($tpl_args) ? true : false ;
		/* get template id  */
		$template_id = !empty($tpl_args['tpl_id']) ? $tpl_args['tpl_id'] : false ;
		/* get template type (emails/print) */
		$template_type = !empty($tpl_args['tpl_type']) ? $tpl_args['tpl_type'] : false ;
		/* get template values */
		$template_values = !empty($tpl_args['tpl_values']) ? $tpl_args['tpl_values'] : false ;



		/*
			templates - get labels/keys
		*/
		if($get_template_parameters){
			global $wppizza_options;
			$blog_options = $wppizza_options;
		}

		/*
			get full values
		*/
		if(!$get_template_parameters){
			/*
				unserialize order values to use some of its values
			*/
			$order_details = $order['order_ini'];
			$blog_options = $order['blog_options'];
			/*
				gateway selected
			*/
			$gw = $order['initiator'];

			/*
				if gateway has subsequently been disabled, just use initiator label
			*/
			if(!empty(WPPIZZA()->gateways->gwobjects->$gw)){
				$gw_settings = WPPIZZA()->gateways->gwobjects->$gw;
			}else{
				/* gateways used */
				$gw_class = 'WPPIZZA_GATEWAY_'.strtoupper($gw).'';// constrict class name of gateway used
				if(class_exists($gw_class)){
					$gw_used  = new $gw_class;
				}

				$gw_settings = new stdClass();
				$gw_settings->gateway_type = strtolower($order['initiator']);
				$gw_settings->label = $order['initiator'];
				$gw_settings->supports_refunds = !empty($gw_used->gatewayRefunds) ? true :  false;
			}


			/*
				order total
			*/
			$order_total = wppizza_format_price($order['order_total'], $order_details['param']['currency'], $order_details['param']['currency_position'], $order_details['param']['decimals']);
			$order_paid = wppizza_format_price(0, $order_details['param']['currency'], $order_details['param']['currency_position'], $order_details['param']['decimals']);
		}

		/*
			ini array
		*/
		$order_parameters = array();
		/*************************************************
		*
		*	default disabled, can be enabled by filter
		*
		*************************************************/
		/*
			order_date_utc [currently no label in localization]
		*/
		if($get_template_parameters){
			$order_parameters['order_date_utc']['template_default_sort']= 0;
			$order_parameters['order_date_utc']['template_default_enabled']= false;
			$order_parameters['order_date_utc']['template_parameter']	= false;
			$order_parameters['order_date_utc']['template_row_default_css']		=	'';
		}else{
			$order_parameters['order_date_utc']['class_ident'] = 'order-date-utc';
			$order_parameters['order_date_utc']['value'] = $order['order_date_utc'];
			$order_parameters['order_date_utc']['value_formatted'] = apply_filters('wppizza_filter_order_date', $order['order_date_utc'], $order['date_format']) ;
		}
		$order_parameters['order_date_utc']['label'] = __('Date UTC','wppizza-admin') ;


		/*
			order_update [currently no label in localization]
		*/
		if($get_template_parameters){
			$order_parameters['order_update']['template_default_sort']= 0;
			$order_parameters['order_update']['template_default_enabled']= false;
			$order_parameters['order_update']['template_parameter']	= false;
			$order_parameters['order_update']['template_row_default_css']		=	'';
		}else{
			$order_parameters['order_update']['class_ident'] = 'order-update';
			$order_parameters['order_update']['value'] = $order['order_update'];
			$order_parameters['order_update']['value_formatted'] = apply_filters('wppizza_filter_order_date', $order['order_update'], $order['date_format']) ;
		}
		$order_parameters['order_update']['label'] = __('Last Update','wppizza-admin') ;

		/*
			order_delivered [currently no label in localization]
		*/
		if($get_template_parameters){
			$order_parameters['order_delivered']['template_default_sort']= 0;
			$order_parameters['order_delivered']['template_default_enabled']= false;
			$order_parameters['order_delivered']['template_parameter']	= false;
			$order_parameters['order_delivered']['template_row_default_css']		=	'';
		}else{
			$order_parameters['order_delivered']['class_ident'] = 'order-delivered';
			$order_parameters['order_delivered']['value'] = $order['order_delivered'];
			$order_parameters['order_delivered']['value_formatted'] = apply_filters('wppizza_filter_order_date', $order['order_delivered'], $order['date_format']) ;
		}
		$order_parameters['order_delivered']['label'] = $blog_options['localization']['history_order_delivered_label'];


		/*
			order_notes [currently no label in localization]
		*/
		if($get_template_parameters){
			$order_parameters['notes']['template_default_sort']= 0;
			$order_parameters['notes']['template_default_enabled']= false;
			$order_parameters['notes']['template_parameter']	= false;
			$order_parameters['notes']['template_row_default_css']		=	'';
		}else{
			$order_parameters['notes']['class_ident'] = 'notes';
			$order_parameters['notes']['value'] = $order['notes'] ;
			$order_parameters['notes']['value_formatted'] = $order['notes'] ;
		}
		$order_parameters['notes']['label'] = __('Notes','wppizza-admin') ;

		/*
			payment gateway ID [currently no label in localization]
		*/
		if($get_template_parameters){
			$order_parameters['payment_gateway']['template_default_sort']= 0;
			$order_parameters['payment_gateway']['template_default_enabled']= false;
			$order_parameters['payment_gateway']['template_parameter']	= false;
			$order_parameters['payment_gateway']['template_row_default_css']		=	'';
		}else{
			$order_parameters['payment_gateway']['class_ident'] = 'gateway';
			$order_parameters['payment_gateway']['value'] = $order['initiator'] ;
			$order_parameters['payment_gateway']['value_formatted'] = $order['initiator'] ;
		}
		$order_parameters['payment_gateway']['label'] = __('Gateway Ident','wppizza-admin') ;


		/*
			gateway supports refunds
		*/
		if($get_template_parameters){
			$order_parameters['gateway_supports_refunds']['template_default_sort']= 0;
			$order_parameters['gateway_supports_refunds']['template_default_enabled']= false;
			$order_parameters['gateway_supports_refunds']['template_parameter']	= false;
			$order_parameters['gateway_supports_refunds']['template_row_default_css']		=	'';
		}else{
			$order_parameters['gateway_supports_refunds']['class_ident'] = 'gateway-refunds';
			$order_parameters['gateway_supports_refunds']['value'] = empty($gw_settings->supports_refunds) ? false : true ;
			$order_parameters['gateway_supports_refunds']['value_formatted'] = empty($gw_settings->supports_refunds) ? $blog_options['localization']['generic_placeholder_checkbox_0'] : $blog_options['localization']['generic_placeholder_checkbox_1'] ;
		}
		$order_parameters['gateway_supports_refunds']['label'] = __('Gateway Supports Refunds','wppizza-admin') ;


		/*
			payment_status [currently no label in localization]
		*/
		if($get_template_parameters){
			$order_parameters['payment_status']['template_default_sort']= 0;
			$order_parameters['payment_status']['template_default_enabled']= false;
			$order_parameters['payment_status']['template_parameter']	= false;
			$order_parameters['payment_status']['template_row_default_css']		=	'';
		}else{
			$order_parameters['payment_status']['class_ident'] = 'payment-status';
			$order_parameters['payment_status']['value'] = $order['payment_status'] ;
			$order_parameters['payment_status']['value_formatted'] = $order['payment_status'] ;
		}
		$order_parameters['payment_status']['label'] = __('Payment Status','wppizza-admin');

		/*
			order_status [currently no label in localization]
		*/
		if($get_template_parameters){
			$order_parameters['order_status']['template_default_sort']= 0;
			$order_parameters['order_status']['template_default_enabled']= false;
			$order_parameters['order_status']['template_parameter']	= false;
			$order_parameters['order_status']['template_row_default_css']		=	'';
		}else{
			$order_parameters['order_status']['class_ident'] = 'order-status';
			$order_parameters['order_status']['value'] = $order['order_status'] ;
			$order_parameters['order_status']['value_formatted'] = $order['order_status'] ;
		}
		$order_parameters['order_status']['label'] = __('Order Status','wppizza-admin');



		/*
			display_errors [currently no label in localization]
		*/
		if($get_template_parameters){
			$order_parameters['display_errors']['template_default_sort']= 0;
			$order_parameters['display_errors']['template_default_enabled']= false;
			$order_parameters['display_errors']['template_parameter']	= false;
			$order_parameters['display_errors']['template_row_default_css']		=	'';
		}else{

			$display_errors_value = maybe_unserialize($order['display_errors']);
			$display_errors_formatted = '';
			if(!empty($display_errors_value) && is_array($display_errors_value)){
			$display_errors_formatted .='<div class="'.WPPIZZA_SLUG.'-error-details-label">'.$blog_options['localization']['generic_error_details'].'</div>';
			foreach($display_errors_value as $val){
				$display_errors_formatted .='<div>';
					if(!empty($val['error_id'])){
						$display_errors_formatted .= '<span>'.esc_html($val['error_id']).':</span> ';
					}
					if(!empty($val['error_message'])){
						$display_errors_formatted .= '<span>'.nl2br(esc_html($val['error_message'])).'</span>';
					}
				$display_errors_formatted .='</div>';
			}}

			$order_parameters['display_errors']['class_ident'] = 'payment-error';
			$order_parameters['display_errors']['value'] = $display_errors_value ;
			$order_parameters['display_errors']['value_formatted'] = $display_errors_formatted ;
		}
		$order_parameters['display_errors']['label'] = __('Errors','wppizza-admin') ;


		/*
			user data [currently no label in localization]
		*/
		if($get_template_parameters){
			$order_parameters['user_data']['template_default_sort']= 0;
			$order_parameters['user_data']['template_default_enabled']= false;
			$order_parameters['user_data']['template_parameter']	= false;
			$order_parameters['user_data']['template_row_default_css']		=	'';
		}else{
			$order_parameters['user_data']['class_ident'] = 'user-data';
			$order_parameters['user_data']['value'] = $order['user_data'];
			$order_parameters['user_data']['value_formatted'] = maybe_unserialize($order['user_data']) ;
		}
		$order_parameters['user_data']['label'] = __('User Data','wppizza-admin');

		/*
			create new account for user ?
		*/
		if($get_template_parameters){
			$order_parameters['create_account']['template_default_sort']= 0;
			$order_parameters['create_account']['template_default_enabled']= false;
			$order_parameters['create_account']['template_parameter']	= false;
			$order_parameters['create_account']['template_row_default_css']		=	'';
		}else{
			$create_user_account = !empty($order['customer_ini'][''.WPPIZZA_SLUG.'_account']) ? true : false;

			$order_parameters['create_account']['class_ident'] = 'create-account';
			$order_parameters['create_account']['value'] = $create_user_account;
			$order_parameters['create_account']['value_formatted'] = $create_user_account ;
		}
		$order_parameters['create_account']['label'] = __('Create Account','wppizza-admin');

		/*
			update account for user ?
		*/
		if($get_template_parameters){
			$order_parameters['update_profile']['template_default_sort']= 0;
			$order_parameters['update_profile']['template_default_enabled']= false;
			$order_parameters['update_profile']['template_parameter']	= false;
			$order_parameters['update_profile']['template_row_default_css']		=	'';
		}else{
			$user_profile_update = !empty($order['customer_ini'][''.WPPIZZA_SLUG.'_profile_update']) ? true : false;

			$order_parameters['update_profile']['class_ident'] = 'update-profile';
			$order_parameters['update_profile']['value'] = $user_profile_update;
			$order_parameters['update_profile']['value_formatted'] = $user_profile_update ;
		}
		$order_parameters['update_profile']['label'] = __('Update Profile','wppizza-admin');

		/*
			ip Address [currently no label in localization]
		*/
		if($get_template_parameters){
			$order_parameters['ip_address']['template_default_sort']= 0;
			$order_parameters['ip_address']['template_default_enabled']= false;
			$order_parameters['ip_address']['template_parameter']	= false;
			$order_parameters['ip_address']['template_row_default_css']		=	'';
		}else{
			$order_parameters['ip_address']['class_ident'] = 'ip-address';
			$order_parameters['ip_address']['value'] = $order['ip_address'];
			$order_parameters['ip_address']['value_formatted'] = $order['ip_address'];
		}
		$order_parameters['ip_address']['label'] = __('IP Address','wppizza-admin');

		/*
			session_id [currently no label in localization]
		*/
		if($get_template_parameters){
			$order_parameters['session_id']['template_default_sort']= 0;
			$order_parameters['session_id']['template_default_enabled']= false;
			$order_parameters['session_id']['template_parameter']	= false;
			$order_parameters['session_id']['template_row_default_css']		=	'';
		}else{
			$order_parameters['session_id']['class_ident'] = 'session-id';
			$order_parameters['session_id']['value'] = $order['session_id'];
			$order_parameters['session_id']['value_formatted'] = $order['session_id'];
		}
		$order_parameters['session_id']['label'] = __('Session ID','wppizza-admin');


		// only add if gotten from db...
		if($caller!='session'){
				/*
					hash [currently no label in localization]
					empty if called from session
				*/
				if($get_template_parameters){
					$order_parameters['hash']['template_default_sort']= 0;
					$order_parameters['hash']['template_default_enabled']= false;
					$order_parameters['hash']['template_parameter']	= false;
					$order_parameters['hash']['template_row_default_css']		=	'';
				}else{
					$order_parameters['hash']['class_ident'] = 'hash';
					$order_parameters['hash']['value'] = !empty($order['hash']) ? $order['hash'] : '';
					$order_parameters['hash']['value_formatted'] = !empty($order['hash']) ? $order['hash'] : '';
				}
				$order_parameters['hash']['label'] = __('hash','wppizza-admin');
		}

		/*
			currency iso [currently no label in localization]
		*/
		if($get_template_parameters){
			$order_parameters['currency']['template_default_sort']= 0;
			$order_parameters['currency']['template_default_enabled']= false;
			$order_parameters['currency']['template_parameter']	= false;
			$order_parameters['currency']['template_row_default_css']		=	'';
		}else{
			$order_parameters['currency']['class_ident'] = 'currency';
			$order_parameters['currency']['value'] = $order_details['param']['currencyiso'];
			$order_parameters['currency']['value_formatted'] = $order_details['param']['currency'];
		}
		$order_parameters['currency']['label'] = __('Currency','wppizza-admin');


		/*
			transaction_details
		*/
		if($get_template_parameters){
			$order_parameters['transaction_details']['template_default_sort']= 0;
			$order_parameters['transaction_details']['template_default_enabled']= false;
			$order_parameters['transaction_details']['template_parameter']	= false;
			$order_parameters['transaction_details']['template_row_default_css']		=	'';
		}else{
			$order_parameters['transaction_details']['class_ident'] = 'tx-details';
			$order_parameters['transaction_details']['value'] = $order['transaction_details'];
			$order_parameters['transaction_details']['value_formatted'] = maybe_unserialize($order['transaction_details']);
		}
		$order_parameters['transaction_details']['label'] = __('TX Details','wppizza-admin');



		/*************************************************
		*
		*	default enabled, can be disabled by filter
		*
		*************************************************/


		/*
			wp user id (formatted as Guest or "Registered user" - localization)
		*/
		if($get_template_parameters){
			$order_parameters['wp_user_id']['template_default_sort']= 0;
			$order_parameters['wp_user_id']['template_default_enabled']= false;
			$order_parameters['wp_user_id']['template_parameter']	= true;
			$order_parameters['wp_user_id']['template_row_default_css']		=	'';
		}else{
			$order_parameters['wp_user_id']['class_ident'] = 'user-id';
			$order_parameters['wp_user_id']['value'] = $order['wp_user_id'];
			$order_parameters['wp_user_id']['value_formatted'] = empty($order['wp_user_id']) ? $blog_options['localization']['templates_user_is_guest'] : $blog_options['localization']['templates_user_is_registered'].' (#'.$order['wp_user_id'].')' ;
		}
		$order_parameters['wp_user_id']['label'] = $blog_options['localization']['common_label_order_wp_user_id'] ;


		/*
			order_date
		*/
		if($get_template_parameters){
			$order_parameters['order_date']['template_default_sort']= 10;
			$order_parameters['order_date']['template_default_enabled']= true;
			$order_parameters['order_date']['template_parameter']	= true;
			$order_parameters['order_date']['template_row_default_css']		=	'text-align: center;';
		}else{
			$order_parameters['order_date']['class_ident'] = 'order-date';
			$order_parameters['order_date']['value'] = $order['order_date'];
			$order_parameters['order_date']['value_formatted'] = apply_filters('wppizza_filter_order_date',$order['order_date'], $order['date_format']) ;
		}
		$order_parameters['order_date']['label'] = $blog_options['localization']['common_label_order_order_date'] ;

		/*
			order id
		*/
		if($get_template_parameters){
			$order_parameters['order_id']['template_default_sort']= 20;
			$order_parameters['order_id']['template_default_enabled']= true;
			$order_parameters['order_id']['template_parameter']	= true;
			$order_parameters['order_id']['template_row_default_css']		=	'';
		}else{
			$order_parameters['order_id']['class_ident'] = 'order-id';
			$order_parameters['order_id']['value'] = $order['id'];
			$order_parameters['order_id']['value_formatted'] = apply_filters('wppizza_filter_order_id', $order['id'], $order['transaction_id']);
		}
		$order_parameters['order_id']['label'] =  $blog_options['localization']['common_label_order_order_id'] ;

		/*
			payment due if cod==total , if prepay == 0
		*/
		if($get_template_parameters){
			$order_parameters['payment_due']['template_default_sort']= 30;
			$order_parameters['payment_due']['template_default_enabled']= true;
			$order_parameters['payment_due']['template_parameter']	= true;
			$order_parameters['payment_due']['template_row_default_css']		=	'';
		}else{
			$order_parameters['payment_due']['class_ident'] = 'payment-due';
			$order_parameters['payment_due']['value'] = ($gw_settings->gateway_type == 'cod') ? $order['order_total'] : 0 ;
			$order_parameters['payment_due']['value_formatted'] = ($gw_settings->gateway_type == 'cod') ? $order_total : $order_paid ;
		}
		$order_parameters['payment_due']['label'] = $blog_options['localization']['common_label_order_payment_outstanding'];


		/*
			delivery/pickup type
		*/
		if($get_template_parameters){
			$order_parameters['order_type']['template_default_sort'] =	40;
			$order_parameters['order_type']['template_default_enabled'] =	true;
			$order_parameters['order_type']['template_parameter']	=	true;
			$order_parameters['order_type']['template_row_default_css'] =	'';
		}else{
			$order_parameters['order_type']['class_ident']			=	($order['order_self_pickup'] == 'N') ? 'delivery' : 'pickup';
			$order_parameters['order_type']['value']				=	$order['order_self_pickup'];
			$order_parameters['order_type']['value_formatted']		=	($order['order_self_pickup'] == 'N') ? $blog_options['localization']['common_value_order_delivery'] : $blog_options['localization']['common_value_order_pickup'];
		}
		$order_parameters['order_type']['label'] = $blog_options['localization']['common_label_order_delivery_type'];



		/*
			payment_type
		*/
		if($get_template_parameters){
			$order_parameters['payment_type']['template_default_sort']= 50;
			$order_parameters['payment_type']['template_default_enabled']= true;
			$order_parameters['payment_type']['template_parameter']	= true;
			$order_parameters['payment_type']['template_row_default_css']		=	'';
		}else{
			$order_parameters['payment_type']['class_ident'] = 'payment-type';
			$order_parameters['payment_type']['value'] = $gw_settings->label ;
			$order_parameters['payment_type']['value_formatted'] = $gw_settings->label ;
		}
		$order_parameters['payment_type']['label'] = $blog_options['localization']['common_label_order_payment_type'];

		/*
			payment method
		*/
		if($get_template_parameters){
			$order_parameters['payment_method']['template_default_sort']= 60;
			$order_parameters['payment_method']['template_default_enabled']= false;
			$order_parameters['payment_method']['template_parameter']	= true;
			$order_parameters['payment_method']['template_row_default_css']		=	'';
		}else{
			$order_parameters['payment_method']['class_ident'] = 'payment-method';
			$order_parameters['payment_method']['value'] = ($gw_settings->gateway_type == 'cod') ? 'cod' : 'prepaid' ;
			$order_parameters['payment_method']['value_formatted'] = ($gw_settings->gateway_type == 'cod') ? $blog_options['localization']['common_value_order_cash'] : $blog_options['localization']['common_value_order_credit_card'] ;
		}
		$order_parameters['payment_method']['label'] = $blog_options['localization']['common_label_order_payment_method'];

		/*
			transaction id
		*/
		if($get_template_parameters){
			$order_parameters['transaction_id']['template_default_sort']= 70;
			$order_parameters['transaction_id']['template_default_enabled']= true;
			$order_parameters['transaction_id']['template_parameter']	= true;
			$order_parameters['transaction_id']['template_row_default_css']		=	'';
		}else{
			$order_parameters['transaction_id']['class_ident'] = 'transaction-id';
			$order_parameters['transaction_id']['value'] = $order['transaction_id'];
			$order_parameters['transaction_id']['value_formatted'] = apply_filters('wppizza_filter_transaction_id', $order['transaction_id'], $order['id']);
		}
		$order_parameters['transaction_id']['label'] = $blog_options['localization']['common_label_order_transaction_id'] ;


		/*
			total add here too. might come in useful in places
		*/
		if($get_template_parameters){
			$order_parameters['total']['template_default_sort']= 80;
			$order_parameters['total']['template_default_enabled']= false;
			$order_parameters['total']['template_parameter']	= true;
			$order_parameters['total']['template_row_default_css']		=	'';
		}else{
			$order_parameters['total']['class_ident'] = 'total';
			$order_parameters['total']['value'] = $order['order_total'] ;
			$order_parameters['total']['value_formatted'] = $order_total ;
		}
		$order_parameters['total']['label'] = $blog_options['localization']['common_label_order_total'];


		/*
			refunds
		*/
		if($get_template_parameters){
			$order_parameters['order_refund']['template_default_sort']= 90;
			$order_parameters['order_refund']['template_default_enabled']= false;
			$order_parameters['order_refund']['template_parameter']	= false;
			$order_parameters['order_refund']['template_row_default_css']		=	'';
		}else{
			$order_parameters['order_refund']['class_ident'] = 'order_refund';
			$order_parameters['order_refund']['value'] = $order['order_refund'] ;
			$order_parameters['order_refund']['value_formatted'] = wppizza_format_price($order['order_refund'], $order_details['param']['currency'], $order_details['param']['currency_position'], $order_details['param']['decimals']) ;
		}
		$order_parameters['order_refund']['label'] = $blog_options['localization']['common_label_order_refund'];


		/*
			pickup or delivery note
		*/
		if($get_template_parameters){
			$order_parameters['pickup_delivery']['template_default_sort'] =	100;
			$order_parameters['pickup_delivery']['template_default_enabled'] =	true;
			$order_parameters['pickup_delivery']['template_parameter']	=	true;
			$order_parameters['pickup_delivery']['template_row_default_css'] =	'text-align:center; font-weight:bold; padding:3px';
		}else{
			$order_parameters['pickup_delivery']['class_ident']			=	($order['order_self_pickup'] == 'N') ? 'delivery-note' : 'pickup-note';
			$order_parameters['pickup_delivery']['value']				=	$order['order_self_pickup']; //$order_values['summary']['delivery_charges'] ;
			$order_parameters['pickup_delivery']['value_formatted']		=	($order['order_self_pickup'] == 'N') ? sprintf($blog_options['localization']['order_page_delivery_time'], $blog_options['order_settings']['order_delivery_time']) :  ( ($blog_options['order_settings']['delivery_selected'] == 'no_delivery') ? $blog_options['localization']['order_page_no_delivery'] : sprintf($blog_options['localization']['order_page_selfpickup'], $blog_options['order_settings']['order_pickup_preparation_time']) ); //!empty($order_values['summary']['delivery_charges']) ? wppizza_format_price($order_values['summary']['delivery_charges'], $currency) : ' ' ;/* add space to force empty td in templates*/
		}
		$order_parameters['pickup_delivery']['label'] = $blog_options['localization']['common_label_order_delivery_pickup_note'];


		/*
			order admin notes
		*/
		if($get_template_parameters){
			$order_parameters['admin_notes']['template_default_sort'] =	110;
			$order_parameters['admin_notes']['template_default_enabled'] =	false;
			$order_parameters['admin_notes']['template_parameter']	=	($get_template_parameters == 'print') ? true : false ;
			$order_parameters['admin_notes']['template_row_default_css'] =	'';/*not used in emails anyway */
		}else{
			$order_parameters['admin_notes']['class_ident']			=	'admin-notes';
			$order_parameters['admin_notes']['value']				=	$order['notes'];
			$order_parameters['admin_notes']['value_formatted']		=	$order['notes'];
		}
		$order_parameters['admin_notes']['label'] = __('Admin Notes','wppizza-admin');





		/*
			template default sort, enabled param, labels an keys only
		*/
		if($get_template_parameters){
			$template_enabled_parameters = array();

			/*
				global section styles
			*/
			if($get_template_parameters=='emails'){
			$template_enabled_parameters['style'] = array();
            $template_enabled_parameters['style']['table'] = 'margin: 5px 0 30px 0; border-bottom: 1px dotted #cecece;';
            $template_enabled_parameters['style']['th'] = '';
            $template_enabled_parameters['style']['td-lft'] = 'width: 50%; white-space:nowrap; text-align: right; padding:2px';
            $template_enabled_parameters['style']['td-rgt'] = 'padding: 2px; word-break: break-word;';
			}
			/*
				label for section
			*/
			$template_enabled_parameters['labels']['label'] 	= $blog_options['localization']['templates_label_'.$section_key.''];
			/*
				section enabled - defaults to true for new templates
			*/
			$template_enabled_parameters['section_enabled'] = true;
			/*
				section label enabled - defaults to true for new templates
			*/
			$template_enabled_parameters['label_enabled'] = false;
			/*
				parameters and parameter default styles
			*/
			$template_enabled_parameters['parameters'] = array();
			foreach($order_parameters as $order_parameters_key=>$order_parameters_values){
				if(!empty($order_parameters_values['template_parameter'])){
					/* parameters: sort , enabled, label */
					$template_enabled_parameters['parameters'][$order_parameters_key] = array();
					$template_enabled_parameters['parameters'][$order_parameters_key]['sort'] = $order_parameters_values['template_default_sort'];
					$template_enabled_parameters['parameters'][$order_parameters_key]['enabled'] = $order_parameters_values['template_default_enabled'];
					$template_enabled_parameters['parameters'][$order_parameters_key]['label'] = $order_parameters_values['label'];
					/* parameters: template styles */
					if($get_template_parameters=='emails'){
					$template_enabled_parameters['style'][$order_parameters_key.'-tdall']  	= $order_parameters_values['template_row_default_css'];
					}
				}
			}

			/*************************************************************************
				overwrite sort order and values with set/saved values if there are any
			*************************************************************************/
			if(!empty($template_values)){
				/* global section styles */
				if($get_template_parameters=='emails'){
            		$template_enabled_parameters['style']['table'] = !empty($template_values['sections'][$section_key]['style']['table']) ? $template_values['sections'][$section_key]['style']['table'] : '';
            		$template_enabled_parameters['style']['th'] = !empty($template_values['sections'][$section_key]['style']['th']) ? $template_values['sections'][$section_key]['style']['th'] : '';
            		$template_enabled_parameters['style']['td-lft'] = !empty($template_values['sections'][$section_key]['style']['td-lft']) ? $template_values['sections'][$section_key]['style']['td-lft'] : '';
            		$template_enabled_parameters['style']['td-rgt'] = !empty($template_values['sections'][$section_key]['style']['td-rgt']) ? $template_values['sections'][$section_key]['style']['td-rgt'] : '';
				}

				/* section enabled */
				$template_enabled_parameters['section_enabled'] = !empty($template_values['sections'][$section_key]['section_enabled']) ? true : false;
				/* section label enabled */
				$template_enabled_parameters['label_enabled'] = !empty($template_values['sections'][$section_key]['label_enabled']) ? true : false;

				$resort = 0;
				foreach($template_values['sort'][$section_key] as $parameter_key=>$enabled){
					/* resort */
					$template_enabled_parameters['parameters'][$parameter_key]['sort'] = $resort;
					/* enabled ?*/
					$template_enabled_parameters['parameters'][$parameter_key]['enabled'] = !empty($template_values['sections'][$section_key]['parameters'][$parameter_key]['enabled']) ? true : false;
					/* css*/
					if($get_template_parameters=='emails'){
					$template_enabled_parameters['style'][$parameter_key.'-tdall'] = !empty($template_values['sections'][$section_key]['style'][$parameter_key.'-tdall']) ? $template_values['sections'][$section_key]['style'][$parameter_key.'-tdall'] : '';
					}

				$resort++;
				}
			}

			/* default sort */
			if(is_array($template_enabled_parameters['parameters'])){
				asort($template_enabled_parameters['parameters']);
			}

			return $template_enabled_parameters;
		}


		$order_parameters = apply_filters('wppizza_filter_ordervars_formatted', $order_parameters, $order);


	return $order_parameters;
	}

/*******************************************
*
*	Map Customer confirmation form fields
*
********************************************/
public static function customer_confirmation_formatted($get_template_parameters = false, $caller = false){
	global $wppizza_options ;


	/*
		skip if confirmation not enabled
		or just getting template parameters
	*/
	if(empty($wppizza_options['confirmation_form']['confirmation_form_enabled']) || !empty($get_template_parameters)){
		return;
	}


	/**
		get the confirmation form fields
		and sort
	**/
	$formfields = $wppizza_options['confirmation_form']['formfields'];
	if(!empty($formfields)){
		asort($formfields);
	}


	/*
		ini and build array
	*/
	$confirmation_form_parameters = array();

	foreach($formfields as $key => $val){

		if(!empty($val['enabled'])){
			/* set id as key */
			$key = $val['key'];

			/*********
				set a consistent class ident
			**********/
			$confirmation_form_parameters[$key]['class_ident'] = $key;

			/*********
				get label
			**********/
			$confirmation_form_parameters[$key]['label'] = $val['lbl'];

			/**********
				get value , always not set when confirmation form used
			**********/
			$confirmation_form_parameters[$key]['value'] = '' ;

			/*********
				check if required
			*********/
			$confirmation_form_parameters[$key]['required_attribute'] = !empty($val['required']) ? 'required="required"' : '' ;

			/*********
				set required class (on label)
			*********/
			$confirmation_form_parameters[$key]['required_class'] = !empty($val['required']) ? 'class="'.WPPIZZA_PREFIX.'-label-required"' : '' ;

			/*********
				input type
			*********/
			$confirmation_form_parameters[$key]['type'] = $val['type'];

			/*********
				radio/select options
			*********/
			if($val['type'] == 'radio'){
				$confirmation_form_parameters[$key]['options'] = $val['value'];
			}

			/*********
				select options, add placeholder
			*********/
			if($val['type'] == 'select'){
				$placeholder = empty($val['placeholder']) ? $wppizza_options['localization']['generic_placeholder_select'] : $val['placeholder'];
				$options = array();
				$options[] = array('value'=>'', 'label' => $placeholder);
				foreach($val['value'] as $option){
					if(!empty($option)){
						$options[] = array('value'=>$option, 'label' => $option);
					}
				}
				$confirmation_form_parameters[$key]['options'] = $options;
			}

			/*********
				placeholder - confirmation fields - at the moment - do not have any placeholders actually, but just to avoid php notices....
			*********/
			$confirmation_form_parameters[$key]['placeholder'] = empty($val['placeholder']) ? '' : $val['placeholder'];
		}
	}

return $confirmation_form_parameters;
}
/*******************************************
*
*	Map Customer Details
*
********************************************/
public static function customer_details_formatted($order = false, $tpl_args = false, $caller = false){
	global $wppizza_options;
	$txt = $wppizza_options['localization'] + $wppizza_options['confirmation_form']['localization'];

	$section_key = 'customer';


	/*****************************************
	#
	#	if getting template values
	#
	*****************************************/
	/* ascertain if we only need to get get_template_parameters */
	$get_template_parameters = !empty($tpl_args) ? true : false ;
	/* get template id  */
	$template_id = !empty($tpl_args['tpl_id']) ? $tpl_args['tpl_id'] : false ;
	/* get template type (emails/print) */
	$template_type = !empty($tpl_args['tpl_type']) ? $tpl_args['tpl_type'] : false ;
	/* get template values */
	$template_values = !empty($tpl_args['tpl_values']) ? $tpl_args['tpl_values'] : false ;

	/*********
		get data from session on checkout page
	*********/
	$is_orderpage = ($caller == 'orderpage') ?  true : false ;


	/*****************************************
		only return enabled wppizza formfields
		to use labels in admin templates sections
	*****************************************/
	if($get_template_parameters){

		/*
			these should already be saved as sorted - distinctly unsetting tips
		*/
		$formfields = WPPIZZA()-> helpers -> enabled_formfields(false, false, true);

		$template_enabled_parameters = array();
		/*
			global section styles
		*/
		if($get_template_parameters=='emails'){
		$template_enabled_parameters['style'] = array();
        $template_enabled_parameters['style']['table'] = 'margin: 20px 0;';
        $template_enabled_parameters['style']['th'] = '';
        $template_enabled_parameters['style']['td-lft'] = 'text-align: left; padding: 2px; white-space:nowrap; vertical-align:top';
        $template_enabled_parameters['style']['td-rgt'] = 'text-align: right;padding: 2px;';
		}
		/*
			label for section
		*/
		$template_enabled_parameters['labels']['label'] 	= $txt['templates_label_'.$section_key.''];
		/*
			section enabled - defaults to true for new templates
		*/
		$template_enabled_parameters['section_enabled'] = true;
			/*
				section label enabled - defaults to true for new templates
			*/
		$template_enabled_parameters['label_enabled'] = true;
		if(is_array($formfields)){
		$sorter = 0;
		foreach($formfields as $ff_key=>$ff_values){
			/* parameters: sort , enabled, label */
			$template_enabled_parameters['parameters'][$ff_key] = $ff_values;
			//$template_enabled_parameters['parameters'][$ff_key]['sort'] = $sorter;
			//$template_enabled_parameters['parameters'][$ff_key]['enabled'] = !empty($ff_values['enabled']) ? true : false;
			// $template_enabled_parameters['parameters'][$ff_key]['label'] = $ff_values['label'];
			/* parameters: template styles - dummy as not used really*/
			if( $get_template_parameters == 'emails' ){
				$template_enabled_parameters['style'][''.$ff_key.'-tdall']  	= '';
			}
		}}


		/*************************************************************************
			overwrite sort order and values with set/saved values if there are any
		*************************************************************************/
		if(!empty($template_values)){

			/* global section styles */
			if($get_template_parameters=='emails'){
        		$template_enabled_parameters['style']['table'] = !empty($template_values['sections'][$section_key]['style']['table']) ? $template_values['sections'][$section_key]['style']['table'] : '';
        		$template_enabled_parameters['style']['th'] = !empty($template_values['sections'][$section_key]['style']['th']) ? $template_values['sections'][$section_key]['style']['th'] : '';
        		$template_enabled_parameters['style']['td-lft'] = !empty($template_values['sections'][$section_key]['style']['td-lft']) ? $template_values['sections'][$section_key]['style']['td-lft'] : '';
        		$template_enabled_parameters['style']['td-rgt'] = !empty($template_values['sections'][$section_key]['style']['td-rgt']) ? $template_values['sections'][$section_key]['style']['td-rgt'] : '';
			}

			/* section enabled */
			$template_enabled_parameters['section_enabled'] = !empty($template_values['sections'][$section_key]['section_enabled']) ? true : false;
			/* section label enabled */
			$template_enabled_parameters['label_enabled'] = !empty($template_values['sections'][$section_key]['label_enabled']) ? true : false;

			$resort = 0;
			$set_keys = array();/* get keys of form fields that have been set for template to account for newly added enabled formfields in wppizza->forder form settings */
			foreach($template_values['sort'][$section_key] as $parameter_key=>$enabled){
				/*
					skip any values that are not (anymore) in the registered formfields
					@since 3.9
				*/
				if(isset($formfields[$parameter_key])){

					$set_keys[$parameter_key] = $parameter_key;
					/* resort */
					$template_enabled_parameters['parameters'][$parameter_key]['sort'] = $resort;
					/* enabled ?*/
					$template_enabled_parameters['parameters'][$parameter_key]['enabled'] = !empty($template_values['sections'][$section_key]['parameters'][$parameter_key]['enabled']) ? true : false;
					/* css*/
					if($get_template_parameters=='emails'){
					$template_enabled_parameters['style'][$parameter_key.'-tdall'] = !empty($template_values['sections'][$section_key]['style'][$parameter_key.'-tdall']) ? $template_values['sections'][$section_key]['style'][$parameter_key.'-tdall'] : '';
					}

				$resort++;
				}
			}

			/* account for newly added formfields */
			$added_formfields = array_diff_key($formfields, $template_values['sort'][$section_key] );

			foreach($added_formfields as $parameter_key=>$ff){
				/* sort */
				$template_enabled_parameters['parameters'][$parameter_key]['sort'] = $resort;
				/* overwrite enabled - will always actually be false here */
				$template_enabled_parameters['parameters'][$parameter_key]['enabled'] = !empty($template_values['sections'][$section_key]['parameters'][$parameter_key]['enabled']) ? true : false;
				/* css - will always actually be empty*/
				if($get_template_parameters=='emails'){
				$template_enabled_parameters['style'][$parameter_key.'-tdall'] = !empty($template_values['sections'][$section_key]['style'][$parameter_key.'-tdall']) ? $template_values['sections'][$section_key]['style'][$parameter_key.'-tdall'] : '';
				}
			$resort++;
			}

		}

		/* sort */
		if(!empty($template_enabled_parameters['parameters'])){
			asort($template_enabled_parameters['parameters']);
		}

	return $template_enabled_parameters;
	}

	/*****************************************
		-- end templates --
	*****************************************/


	/*****************************************
	*
	*	if we require session/user data (i.e if we are on order page)
	*
	*****************************************/
	if(!empty($is_orderpage)){

		/*
			some vars for convenience
		*/
		$is_pickup = !empty($order['order_ini']['summary']['self_pickup']) ? true : false;

		/*
			user saved meta data - for prefilling formfields
		*/
		$user_meta_data = WPPIZZA()->user->user_meta();

		/*
			user session data
		*/
		$user_session_data = WPPIZZA()->session->get_userdata();

	}


	/*
		ini and build array
	*/
	/*
		as this might be coming from a different blog, we use $order['blog_options']['order_form'] instead of
		WPPIZZA()-> helpers -> enabled_formfields here !!
		however, we allow filtering same way as for above function - so we could use the same method hooked to  wppizza_filter_formfields to keep it consistent !!!
	*/
	$order['blog_options']['order_form'] = apply_filters('wppizza_filter_formfields', $order['blog_options']['order_form'],  $caller);
	/* alias filter - going forward the above will - at some point - be deprecated to avoid confusion with other filters*/
	$order['blog_options']['order_form'] = apply_filters('wppizza_register_formfields', $order['blog_options']['order_form'],  $caller);

	$customer_parameters = array();

	/*
		avoid some php warnings.
		although $order['blog_option']['order_form'] should never be empty
		and if it is the case, the problem is elsewhere really
		session setup, db collation, caching issues  (opcache perhaps ?) etc etc .
		(and if not one of these, then i really do not know)
	*/
	if(empty($order['blog_options']['order_form'])){
		$order['blog_options']['order_form'] = $wppizza_options['order_form'];
	}
	foreach($order['blog_options']['order_form'] as $key => $val){


		/** if using session (i.e when actually doing on order) only get enabled ones */
		/** if NOT doing session - i.e just returning db values - don't get tips */
		if( ( empty($is_orderpage) && $val['type'] !='tips' && !empty($val['enabled']) ) ||  (  !empty($is_orderpage) && !empty($val['enabled']) )  ){

			/* set id as key */
			$key = $val['key'];

			/* set a consistent class ident */
			$customer_parameters[$key]['class_ident'] = $key;
			/* get label */
			$customer_parameters[$key]['label'] = $val['lbl'];
			/* input type */
			$customer_parameters[$key]['type'] = $val['type'];

			/* return a set value for checkboxes */
			if($val['type'] == 'checkbox'){
				$customer_parameters[$key]['value'] = empty($order['customer_ini'][$key]) ? $txt['generic_placeholder_checkbox_0'] : ( empty($val['placeholder']) ? $txt['generic_placeholder_checkbox_1'] : $val['placeholder'] );
			}


			/* return a set value for checkboxes */
			if($val['type'] == 'multicheckbox'){

				$customer_parameters[$key]['value'] = ( !empty($order['customer_ini'][$key]) && is_array($order['customer_ini'][$key]) ) ? implode(', ',$order['customer_ini'][$key]) : (!empty($order['customer_ini'][$key]) ? $order['customer_ini'][$key] : '');

			}

			if($val['type'] != 'checkbox' && $val['type'] != 'multicheckbox' ){
				/* get value , will come from session or saved/executed order - overwritten with prefill below if necessary*/
				$customer_parameters[$key]['value'] = !empty($order['customer_ini'][$key]) ? $order['customer_ini'][$key] : '' ;
			}



			/*
				fields returned from session
				(i.e input fields on order page)
			*/
			if(!empty($is_orderpage)){
				/*********
					get value, prefill or session
				*********/
				$user_value = '';

				/* if prefill, get meta value first */
				if(!empty($val['prefill']) && !empty($user_meta_data[WPPIZZA_SLUG.'_'.$key.''])){
					$user_value = $user_meta_data[WPPIZZA_SLUG.'_'.$key.''] ;
				}
				/* if session set, provided prefill is !== null, overwrite prefill (even if empty) */
				if($val['prefill'] !== null && isset($user_session_data[$key])){
					$user_value = $user_session_data[$key] ;
				}
				/* if user has just logged in, overwrite any empty formfields with saved vars if we have some. first time only */
				if(!empty($user_session_data[WPPIZZA_SLUG.'_has_just_loggedin'])){
					if(empty($user_session_data[$key]) && !empty($user_meta_data[WPPIZZA_SLUG.'_'.$key.''])){
						$user_value = $user_meta_data[WPPIZZA_SLUG.'_'.$key.''] ;
					}
				}

				/* format tips - without currency symbol - prefill always disabled, make sure we format to proper float firts if non comma as decimal separator  */
				if($val['type'] == 'tips'){
					$user_value	= wppizza_format_price(wppizza_format_price_float($user_value), null) ;
				}

				/* get value , will come from session or saved/executed order - overwritten with prefill below if necessary*/
				$customer_parameters[$key]['value'] = $user_value ;

				/*********
					check if required , depending on whether current selection is pickup or delivery and whether either is set to required in admin
				*********/
				$is_required = false;
				if(!$is_pickup && !empty($val['required'])){
					$is_required = true;
				}
				if($is_pickup && !empty($val['required_on_pickup'])){
					$is_required = true;
				}
				//$is_required = ((!$is_pickup && !empty($val['required']) ) ? true : ( ( $is_pickup && !empty($val['required_on_pickup'])) ? true : false ));
				$customer_parameters[$key]['required_attribute'] = !empty($is_required) ? 'required="required"' : '' ;


				/*********
					set required class (on label)
				*********/
				$customer_parameters[$key]['required_class'] = !empty($is_required) ? 'class="'.WPPIZZA_PREFIX.'-label-required"' : '' ;


				/*********
					radio
				*********/
				if($val['type'] == 'radio'){
					$customer_parameters[$key]['options'] = $val['value'];
				}

				/*********
					multicheckbox
				*********/
				if($val['type'] == 'multicheckbox'){
					$customer_parameters[$key]['options'] = $val['value'];
				}

				/*********
					select options, add placeholder
				*********/
				if($val['type'] == 'select'){
					$placeholder = empty($val['placeholder']) ? $txt['generic_placeholder_select'] : $val['placeholder'];
					$options = array();
					$options[] = array('value'=>'', 'label' => $placeholder);
					foreach($val['value'] as $option){
						if(!empty($option)){
							$options[] = array('value'=>$option, 'label' => $option);
						}
					}
					$customer_parameters[$key]['options'] = $options;
				}


				/*********
					placeholder
				*********/
				$customer_parameters[$key]['placeholder'] = $val['placeholder'];

				/*********
					html - unused in plugin, but might be useful
				*********/
				$customer_parameters[$key]['html'] = !empty($val['html']) ? $val['html'] : '' ;

			}
		}
	}


	/*
		if user has just logged in, we have overwritten any still empty formfields with saved vars above.
		however. this should only happen first time only / directly after login so we unset this flag
		when we are done
	*/
	if(!empty($user_session_data['has_just_loggedin'])){
		WPPIZZA()->session->remove_userdata_key('has_just_loggedin');
	}


return $customer_parameters;
}

/*******************************************
*
*	Map Order Itemised Details
*
********************************************/
	public static function itemised_details_formatted($order = false, $tpl_args = false, $caller = false){
		global $wppizza_options;


		$section_key = 'order';


		/*****************************************
		#
		#	if getting template values
		#
		*****************************************/
		/* ascertain if we only need to get get_template_parameters */
		$get_template_parameters = !empty($tpl_args) ? true : false ;
		/* get template id  */
		$template_id = !empty($tpl_args['tpl_id']) ? $tpl_args['tpl_id'] : false ;
		/* get template type (emails/print) */
		$template_type = !empty($tpl_args['tpl_type']) ? $tpl_args['tpl_type'] : false ;
		/* get template values */
		$template_values = !empty($tpl_args['tpl_values']) ? $tpl_args['tpl_values'] : false ;


		/******************************************
			set some dynamic defaults for default templates
			depending on order values
		******************************************/
		$default_enable['tax_rate_formatted'] = true;
		/* for default templates, if multiple taxrates, show taxrate per line i necessary*/
		if( $template_id ==-1 && !empty($order)){
			$default_enable['tax_rate_formatted'] = ($order['sections']['order']['multiple_taxrates']) ? true : false;
		}else{
			/* drag/drop template: disable taxrate display per line as default, but can be turnd on if required */
			$default_enable['tax_rate_formatted'] = false;
		}


		/*
			set keys used for itemised order variables to also use in admin template display parameters
		*/
		$ident = array();
		$ident['blog_id'] 				= array('key' => 'blog_id', 				'label' => __('Blog ID','wppizza-admin'), 				'template_parameter' => false, 	'template_default_sort' => 1,	'template_default_enabled' => false );
		$ident['post_id'] 				= array('key' => 'post_id',					'label' => __('Post ID','wppizza-admin'), 				'template_parameter' => false, 	'template_default_sort' => 1,	'template_default_enabled' => false );
		$ident['title'] 				= array('key' => 'title', 					'label' => __('Title','wppizza-admin'), 				'template_parameter' => true, 	'template_default_sort' => 20,	'template_default_enabled' => true );
		$ident['price_label'] 			= array('key' => 'price_label', 			'label' => __('Size Label','wppizza-admin'), 			'template_parameter' => true, 	'template_default_sort' => 30,	'template_default_enabled' => true );
		$ident['quantity'] 				= array('key' => 'quantity', 				'label' => __('Quantity','wppizza-admin'), 				'template_parameter' => true, 	'template_default_sort' => 10,	'template_default_enabled' => true );
		$ident['tax_rate'] 				= array('key' => 'tax_rate', 				'label' => __('Tax Rate','wppizza-admin'), 				'template_parameter' => false, 	'template_default_sort' => 1,	'template_default_enabled' => false );
		$ident['tax_rate_formatted'] 	= array('key' => 'tax_rate_formatted', 		'label' => __('Tax Rate','wppizza-admin'), 				'template_parameter' => true, 	'template_default_sort' => 50,	'template_default_enabled' => $default_enable['tax_rate_formatted'] );
		$ident['tax_included'] 			= array('key' => 'tax_included', 			'label' => __('Tax Included','wppizza-admin'), 			'template_parameter' => false, 	'template_default_sort' => 1,	'template_default_enabled' => false );
		$ident['tax_to_add'] 			= array('key' => 'tax_to_add', 				'label' => __('Tax Added','wppizza-admin'), 			'template_parameter' => false, 	'template_default_sort' => 1,	'template_default_enabled' => false );
		$ident['use_alt_tax'] 			= array('key' => 'use_alt_tax', 			'label' => __('Alt Tax Rate','wppizza-admin'), 			'template_parameter' => false, 	'template_default_sort' => 1,	'template_default_enabled' => false );
		$ident['sizes'] 				= array('key' => 'sizes', 					'label' => __('Sizes ID','wppizza-admin'), 				'template_parameter' => false, 	'template_default_sort' => 1,	'template_default_enabled' => false );
		$ident['size'] 					= array('key' => 'size', 					'label' => __('Size ID','wppizza-admin'), 				'template_parameter' => false, 	'template_default_sort' => 1,	'template_default_enabled' => false );
		$ident['price'] 				= array('key' => 'price', 					'label' => __('Single Item Price','wppizza-admin'), 	'template_parameter' => false, 	'template_default_sort' => 1,	'template_default_enabled' => false );
		$ident['price_formatted'] 		= array('key' => 'price_formatted', 		'label' => __('Single Item Price','wppizza-admin'), 	'template_parameter' => true, 	'template_default_sort' => 40,	'template_default_enabled' => false );
		$ident['pricetotal'] 			= array('key' => 'pricetotal', 				'label' => __('Subotal Items','wppizza-admin'), 		'template_parameter' => false, 	'template_default_sort' => 1,	'template_default_enabled' => false );
		$ident['pricetotal_formatted'] 	= array('key' => 'pricetotal_formatted', 	'label' => __('Subotal Items','wppizza-admin'), 		'template_parameter' => true, 	'template_default_sort' => 60,	'template_default_enabled' => true );
		$ident['cat_id_selected'] 		= array('key' => 'cat_id_selected', 		'label' => __('Category ID','wppizza-admin'), 			'template_parameter' => false, 	'template_default_sort' => 1,	'template_default_enabled' => false );
		$ident['item_in_categories'] 	= array('key' => 'item_in_categories', 		'label' => __('Categories','wppizza-admin'), 			'template_parameter' => false, 	'template_default_sort' => 1,	'template_default_enabled' => false );
		$ident['extend_data'] 			= array('key' => 'extend_data', 			'label' => __('Extend Data','wppizza-admin'), 			'template_parameter' => false, 	'template_default_sort' => 1,	'template_default_enabled' => false );
		$ident['custom_data'] 			= array('key' => 'custom_data', 			'label' => __('Custom Data','wppizza-admin'), 			'template_parameter' => false, 	'template_default_sort' => 1,	'template_default_enabled' => false );// @since 3.8.6

		/*
			template default sort, labels an keys only
		*/
		if($get_template_parameters){
			$template_enabled_parameters = array();

			/*
				global section styles
			*/
			if( $template_type == 'emails' ){
			$template_enabled_parameters['style'] = array();
            $template_enabled_parameters['style']['table'] = 'margin: 10px 0;';
            $template_enabled_parameters['style']['th'] = 'font-weight: bold ;white-space: nowrap; padding:5px 2px; border-bottom:1px solid; border-top: 1px solid;';
            $template_enabled_parameters['style']['td-lft'] = 'text-align: left; padding: 2px; white-space: nowrap;';
            $template_enabled_parameters['style']['td-ctr'] = 'text-align: left; padding:2px;';
            $template_enabled_parameters['style']['td-rgt'] = 'text-align: right; padding: 2px; white-space: nowrap;';
			$template_enabled_parameters['style']['td-blogname'] = 'font-size: 120%; text-decoration: underline; padding:10px 2px;';
			$template_enabled_parameters['style']['td-catname'] = 'border-bottom: 1px dotted #cecece; padding: 7px 2px;';
			}
			/*
				label for section
			*/
			$template_enabled_parameters['labels']['label'] 			= $wppizza_options['localization']['templates_label_'.$section_key.''];

			/* order labels (qty, article , taxrate, price)*/
			$template_enabled_parameters['labels']['parameters']['quantity'] 	= $wppizza_options['localization']['itemised_label_quantity'];
			$template_enabled_parameters['labels']['parameters']['article'] 	= $wppizza_options['localization']['itemised_label_article'];
			$template_enabled_parameters['labels']['parameters']['taxrate'] 	= $wppizza_options['localization']['itemised_label_taxrate'];
			$template_enabled_parameters['labels']['parameters']['total'] 		= $wppizza_options['localization']['itemised_label_total'];


			/*
				section enabled - defaults to true for new templates
			*/
			$template_enabled_parameters['section_enabled'] = true;
			/*
				section label enabled - defaults to true for new templates
			*/
			$template_enabled_parameters['label_enabled'] = true;

			/*************************************************************************
				return section parameters
			*************************************************************************/
			$template_enabled_parameters['parameters'] = array();
			foreach($ident as $ident_key=>$ident_values){
				if(!empty($ident_values['template_parameter'])){
					/* parameters: sort , enabled, label */
					$template_enabled_parameters['parameters'][$ident_key] = array();
					$template_enabled_parameters['parameters'][$ident_key]['sort'] = $ident_values['template_default_sort'];
					$template_enabled_parameters['parameters'][$ident_key]['enabled'] = $ident_values['template_default_enabled'];
					$template_enabled_parameters['parameters'][$ident_key]['label'] = $ident_values['label'];
					/* parameters: template styles - dummy as not used really*/
					if( $template_type == 'emails' ){
					$template_enabled_parameters['style']['td-'.$ident_key]  	= '';
					}
				}
			}

			/*************************************************************************
				overwrite sort order and values with set/saved values if there are any
			*************************************************************************/
			if(!empty($template_values)){
				/* global section styles */
				if( $template_type == 'emails' ){
            		$template_enabled_parameters['style']['table'] = !empty($template_values['sections'][$section_key]['style']['table']) ? $template_values['sections'][$section_key]['style']['table'] : '';
            		$template_enabled_parameters['style']['th'] = !empty($template_values['sections'][$section_key]['style']['th']) ? $template_values['sections'][$section_key]['style']['th'] : '';
            		$template_enabled_parameters['style']['td-lft'] = !empty($template_values['sections'][$section_key]['style']['td-lft']) ? $template_values['sections'][$section_key]['style']['td-lft'] : '';
            		$template_enabled_parameters['style']['td-ctr'] = !empty($template_values['sections'][$section_key]['style']['td-ctr']) ? $template_values['sections'][$section_key]['style']['td-ctr'] : '';
            		$template_enabled_parameters['style']['td-rgt'] = !empty($template_values['sections'][$section_key]['style']['td-rgt']) ? $template_values['sections'][$section_key]['style']['td-rgt'] : '';
            		$template_enabled_parameters['style']['td-blogname'] = !empty($template_values['sections'][$section_key]['style']['td-blogname']) ? $template_values['sections'][$section_key]['style']['td-blogname'] : '';
            		$template_enabled_parameters['style']['td-catname'] = !empty($template_values['sections'][$section_key]['style']['td-catname']) ? $template_values['sections'][$section_key]['style']['td-catname'] : '';
				}

				/* section enabled */
				$template_enabled_parameters['section_enabled'] = !empty($template_values['sections'][$section_key]['section_enabled']) ? true : false;
				/* section label enabled */
				$template_enabled_parameters['label_enabled'] = !empty($template_values['sections'][$section_key]['label_enabled']) ? true : false;

				$resort = 0;
				foreach($template_values['sort'][$section_key] as $parameter_key=>$enabled){
					/* resort */
					$template_enabled_parameters['parameters'][$parameter_key]['sort'] = $resort;
					/* enabled ?*/
					$template_enabled_parameters['parameters'][$parameter_key]['enabled'] = !empty($template_values['sections'][$section_key]['parameters'][$parameter_key]['enabled']) ? true : false;
					/* css*/
					if( $template_type == 'emails' ){
					$template_enabled_parameters['style']['td-'.$parameter_key] = !empty($template_values['sections'][$section_key]['style'][$parameter_key.'-tdall']) ? $template_values['sections'][$section_key]['style'][$parameter_key.'-tdall'] : '';
					}

				$resort++;
				}
			}



			/* sort */
			if(is_array($template_enabled_parameters['parameters'])){
				asort($template_enabled_parameters['parameters']);
			}

			return $template_enabled_parameters;
		}


		/*
			ini return array
		*/
		$item_parameters = array();

		/*
			map some values for convenience
		*/
		$oItems = $order['order_ini']['items'];
		$multiple_taxrates = $order['order_ini']['summary']['multiple_taxrates'];
		$blog_options = $order['blog_options'];
		$currency = !empty($order['order_ini']['param']['currency']) ? wppizza_decode_entities($order['order_ini']['param']['currency']) : wppizza_decode_entities($blog_options['order_settings']['currency']) ;

		if(!empty($oItems)){

		/*
			flag to identify if we are using variable taxrates - omit if there are no items to start off with
		*/
		$item_parameters['multiple_taxrates'] = !empty($multiple_taxrates) ? true : false;

		/*
			flag to identify if taxes are included in prices or separate
		*/
		$item_parameters['taxes_included'] = !empty($order['order_ini']['param']['tax_included'])  ? true : false;



		foreach($oItems as $k=>$v){


				/**
					set some vars we can re-use if need be
				**/
				/*blog id**/
				$blog_id			=	(int)$v['blog_id'] ;

				/*post id**/
				$post_id			=	(int)$v['post_id'] ;

				/* item name*/
				$title				=	!empty($v['title']) ? $v['title'] : __('Untitled','wppizza-admin');

				/* item size unless "do not display if only one size is enabled if it only has one size" */
				$price_label		=	( empty($v['price_label']) || ( !empty($blog_options['layout']['hide_single_pricetier']) && count($blog_options['sizes'][$v['sizes']])<=1 )) ?  '' : $v['price_label'] ;

				/*old orders might use count instead of quantity**/
				//$quantity			=	isset($v['quantity']) ? (int)$v['quantity'] : (int)$v['count'] ;/*old orders pre wppizza v3.0 might use count instead of quantity**/
				$quantity			=	(int)$v['quantity'];

				/*taxrate as float or '' if non existing (old orders pre wppizza v3.0)**/
				$tax_rate			=	isset($v['tax_rate']) ? wppizza_format_price_float($v['tax_rate'], false) : '' ;
				/*taxrate formatted **/
				$tax_rate_formatted	=	isset($v['tax_rate']) ? wppizza_output_format_percent(wppizza_format_price_float($v['tax_rate'], false)).'%' : '' ;
				/* taxes included */
				$tax_included		= 	$v['tax_included'];
				/* taxes added */
				$tax_to_add			= 	$v['tax_to_add'];
				/* using alt tax ? */
				$use_alt_tax		= 	$v['use_alt_tax'];

				/* sizes / tiers id */
				$sizes				= 	$v['sizes'];

				/* selected size id */
				$size				= 	$v['size'];

				/*item price as float**/
				$price				=	!empty($v['price']) ? wppizza_format_price_float($v['price']) : 0 ;

				/*item price formatted**/
				$price_formatted	=	!empty($v['price']) ? wppizza_format_price($v['price'], $currency) : '' ;

				/*subtotal item**/
				$pricetotal			=	!empty($v['pricetotal']) ? wppizza_format_price_float($v['pricetotal']) : 0 ;

				/*subtotal item formatted**/
				$pricetotal_formatted=	!empty($v['pricetotal']) ? wppizza_format_price($v['pricetotal'], $currency) : '' ;

				/*category id this item was in when added to cart. use in conjunction with "$categories" to get name etc**/
				$cat_id_selected	=	$v['cat_id_selected'] ;/*old orders pre wppizza v3.0 might use catIdSelected instead of cat_id_selected**/


				/*all categories this item was assigned to when added to cart**/
				$item_in_categories	=	$v['item_in_categories'];

				/*for 3rd party plugins storing array of all data if they want to use it **/
				$extend_data		=	isset($v['extend_data']) ? $v['extend_data'] : array();

				/*for 3rd party plugins storing array of all data if they want to use it @since 3.8.6 **/
				$custom_data		=	isset($v['custom_data']) ? $v['custom_data'] : array();

				/******************************
					add to item_parameters array
				******************************/
				$item_parameters['items'][$k][$ident['blog_id']['key']]				=	$blog_id;
				$item_parameters['items'][$k][$ident['post_id']['key']]				=	$post_id;
				$item_parameters['items'][$k][$ident['title']['key']]				=	$title;
				$item_parameters['items'][$k][$ident['price_label']['key']]			=	$price_label;
				$item_parameters['items'][$k][$ident['quantity']['key']]			=	$quantity;
				$item_parameters['items'][$k][$ident['tax_rate']['key']]			=	$tax_rate;
				$item_parameters['items'][$k][$ident['tax_rate_formatted']['key']]	=	$tax_rate_formatted;
				$item_parameters['items'][$k][$ident['tax_included']['key']]		=	$tax_included;
				$item_parameters['items'][$k][$ident['tax_to_add']['key']]			=	$tax_to_add;
				$item_parameters['items'][$k][$ident['use_alt_tax']['key']]			=	$use_alt_tax;
				$item_parameters['items'][$k][$ident['sizes']['key']]				=	$sizes;
				$item_parameters['items'][$k][$ident['size']['key']]				=	$size;
				$item_parameters['items'][$k][$ident['price']['key']]				=	$price;
				$item_parameters['items'][$k][$ident['price_formatted']['key']]		=	$price_formatted;
				$item_parameters['items'][$k][$ident['pricetotal']['key']]			=	$pricetotal;
				$item_parameters['items'][$k][$ident['pricetotal_formatted']['key']]=	$pricetotal_formatted;
				$item_parameters['items'][$k][$ident['cat_id_selected']['key']]		=	$cat_id_selected;
				$item_parameters['items'][$k][$ident['item_in_categories']['key']]	=	$item_in_categories;
				$item_parameters['items'][$k][$ident['extend_data']['key']]			=	$extend_data;
				$item_parameters['items'][$k][$ident['custom_data']['key']]			=	$custom_data;// @since 3.8.6

		}}

		/*
			sort by category first before item
			if categories are to be shown
			@since 3.6.2
		*/
		if(!empty($wppizza_options['layout']['items_group_sort_print_by_category']) && !empty($item_parameters['items'])){
			$sort_by_cat = array();
			foreach($wppizza_options['layout']['category_sort_hierarchy'] as $catKey=>$sortOrder){
				foreach($item_parameters['items'] as $item_key =>$item_array){
					if($item_array['cat_id_selected'] == $catKey){
						$sort_by_cat['items'][$item_key] = $item_array;
					}
				}
			}
			$item_parameters['items'] = $sort_by_cat['items'];
		}


	return $item_parameters;
	}

/*******************************************
*
*	Map Order Summary Details
*
********************************************/
	public static function summary_details_formatted($order = false, $tpl_args = false, $caller = false){


		$section_key = 'summary';

		/*****************************************
		#
		#	if getting template values
		#
		*****************************************/
		/* ascertain if we only need to get get_template_parameters */
		$get_template_parameters = !empty($tpl_args) ? true : false ;
		/* get template id  */
		$template_id = !empty($tpl_args['tpl_id']) ? $tpl_args['tpl_id'] : false ;
		/* get template type (emails/print) */
		$template_type = !empty($tpl_args['tpl_type']) ? $tpl_args['tpl_type'] : false ;
		/* get template values */
		$template_values = !empty($tpl_args['tpl_values']) ? $tpl_args['tpl_values'] : false ;


		/*
			makeing internal keys reserved
			to disallow those to be set/overwritten by additional
			discounts filter (or any other filter we might add in the future here)

		*/
		$reserved_keys = array(
			'total_price_items',
			'discount',
			'delivery_charges',
			'handling_charge',
			'taxes',
			'tax_total',
			'total_price_items',
			'tips',
			'total',
		);

		/*
			templates - get labels/keys
		*/
		if($get_template_parameters){
			global $wppizza_options;
			$blog_options = $wppizza_options;
		}


		/*
			get formatted values for order/session
		*/
		if(!$get_template_parameters){
			/*
				map some values
			*/
			$order_values = $order['order_ini'];
			$blog_options = $order['blog_options'];

			/*
				for convenience
			*/
			$currency = !empty($order_values['param']['currency']) ? wppizza_decode_entities($order_values['param']['currency']) : wppizza_decode_entities($blog_options['order_settings']['currency']) ;
			$taxes_included = empty($order_values['param']['tax_included']) ? false : true;

		}

		/*
			ini  summary array
		*/
		$summary = array();

		/*
			items sum
		*/
		if(!empty($order_values['summary']['total_price_items']) || $get_template_parameters){
			/*
				getting template parts only
			*/
			if($get_template_parameters){
				$summary['total_price_items']['template_default_sort']	=	10;
				$summary['total_price_items']['template_default_enabled']	=	true;
				$summary['total_price_items']['template_parameter']	=	true;
				$summary['total_price_items']['template_row_default_css']		=	'';
				$summary['total_price_items']['label']				=	$blog_options['localization']['order_items'];
			}else{
				$summary['total_price_items'][0]['sort']				=	10;
				$summary['total_price_items'][0]['class_ident']		=	'total-items';
				$summary['total_price_items'][0]['label']				=	$blog_options['localization']['order_items'];
				$summary['total_price_items'][0]['value']				=	$order_values['summary']['total_price_items'] ;
				$summary['total_price_items'][0]['value_formatted']	=	!empty($order_values['summary']['total_price_items']) ? wppizza_format_price($order_values['summary']['total_price_items'], $currency) : '' ;
			}
		}

		/*
			discount
		*/
		if(!empty($order_values['summary']['discount']) || $get_template_parameters){
			/*
				getting template parts only
			*/
			if($get_template_parameters){
				$summary['discount']['template_default_sort']		=	30;
				$summary['discount']['template_default_enabled']	=	true;
				$summary['discount']['template_parameter']			=	true;
				$summary['discount']['template_row_default_css']	=	'';
				$summary['discount']['label']						=	$blog_options['localization']['discount'];
			}else{
				$summary['discount'][0]['sort']						=	30;
				$summary['discount'][0]['class_ident']					=	'discount';
				$summary['discount'][0]['label']						=	$blog_options['localization']['discount'];
				$summary['discount'][0]['value']						=	$order_values['summary']['discount'] ;
				$summary['discount'][0]['value_formatted']				=	!empty($order_values['summary']['discount']) ? '- '.wppizza_format_price($order_values['summary']['discount'], $currency) : '' ;
			}
		}

		/*
			@since 3.9
			additional discounts - external plugins .
			not used natively in wppizza plugin itself must be an array
			should be skipped - for now anyway - in templates generation as filters should be used instead
		*/
		if((!empty($order_values['summary']['additional_discounts']) && is_array($order_values['summary']['additional_discounts']))){//|| $get_template_parameters
			foreach($order_values['summary']['additional_discounts'] as $adKey => $additional_discounts){
				if(is_array($additional_discounts) && !in_array($adKey, $reserved_keys) ){// make sure there is no clash in keys here
					/*
						getting template parts only - skipped here for the time being
						as it's not to be displayed in template settings but should be enabled by filter
						in the extenal plugin that utilises the wppizza_fltr_additional_discounts filter hook
					*/
					if($get_template_parameters){

					//	$summary[$adKey]['template_default_sort']		=	30;
					//	$summary[$adKey]['template_default_enabled']	=	true;
					//	$summary[$adKey]['template_parameter']			=	true;
					//	$summary[$adKey]['template_row_default_css']	=	'';
					//	$summary[$adKey]['label']						=	!empty($additional_discounts['label']) ? $additional_discounts['label'] : '#n/a#' ;
					}else{

						$summary[$adKey][0]['sort']						=	30;
						$summary[$adKey][0]['class_ident']				=	$adKey;
						$summary[$adKey][0]['label']					=	!empty($additional_discounts['label']) ? $additional_discounts['label'] : '' ;
						$summary[$adKey][0]['value']					=	!empty($additional_discounts['value']) ? $additional_discounts['value'] : '' ;
						$summary[$adKey][0]['value_formatted']			=	isset($additional_discounts['value_formatted']) ?  $additional_discounts['value_formatted'] : (!empty($additional_discounts['value']) ? '- '.wppizza_format_price($additional_discounts['value'], $currency) : '') ;//allow formatting here if set

					}
				}
			}
		}


		/*
			delivery charges - if not self pickup -  show delivery charges or free delivery
		*/
		if(empty($order_values['summary']['self_pickup']) || $get_template_parameters){
			/*
				getting template parts only
			*/
			if($get_template_parameters){
				$summary['delivery_charges']['template_default_sort'] =	40;
				$summary['delivery_charges']['template_default_enabled'] =	true;
				$summary['delivery_charges']['template_parameter']	=	true;
				$summary['delivery_charges']['template_row_default_css'] =	'';
				$summary['delivery_charges']['label']				=	$blog_options['localization']['delivery_charges'];
			}else{
				$summary['delivery_charges'][0]['sort']				=	40;
				$summary['delivery_charges'][0]['class_ident']			=	'delivery';
				$summary['delivery_charges'][0]['label']				=	!empty($order_values['summary']['delivery_charges']) ? $blog_options['localization']['delivery_charges'] : $blog_options['localization']['free_delivery'] ;
				$summary['delivery_charges'][0]['value']				=	$order_values['summary']['delivery_charges'] ;
				$summary['delivery_charges'][0]['value_formatted']		=	!empty($order_values['summary']['delivery_charges']) ? wppizza_format_price($order_values['summary']['delivery_charges'], $currency) : ' ' ;/* add space to force empty td in templates*/
			}
		}

		/*
			handling charges - automatically 0 if not on checkout page
		*/
		if(!empty($order_values['summary']['handling_charges']) || $get_template_parameters){
			/*
				getting template parts only
			*/
			if($get_template_parameters){
				$summary['handling_charge']['template_default_sort'] =	50;
				$summary['handling_charge']['template_default_enabled']	=	true;
				$summary['handling_charge']['template_parameter']	=	true;
				$summary['handling_charge']['template_row_default_css'] =	'';
				$summary['handling_charge']['label']				=	$blog_options['localization']['handling_charges'];
			}else{
				$summary['handling_charge'][0]['sort']					=	50;
				$summary['handling_charge'][0]['class_ident']			=	'handling-charge';
				$summary['handling_charge'][0]['label']				=	$blog_options['localization']['handling_charges'];
				$summary['handling_charge'][0]['value']				=	$order_values['summary']['handling_charges'] ;
				$summary['handling_charge'][0]['value_formatted']		=	wppizza_format_price($order_values['summary']['handling_charges'], $currency) ;
			}
		}


		/*
			taxes - included sort @ 20 excluded(added) sort @60
		*/
		if(!empty($order_values['summary']['tax_by_rate']) || $get_template_parameters){

			/*
				getting template parts only
			*/
			if($get_template_parameters){

				$summary['taxes']['template_default_sort']			=	60;
				$summary['taxes']['template_default_enabled']		=	true;
				$summary['taxes']['template_parameter']				=	true;
				$summary['taxes']['template_row_default_css']		=	'';
				$summary['taxes']['label']							=	sprintf($blog_options['localization']['item_tax_total'], '');
			}else{
				/**
					if anyone wants to revert to old style combined tax display,
					as yet unused in plugin. if the request comes many times , we'll make it an option

					return "false" [default] to show separate
					return "true" to show combined only
					return null to show both
					return 'force' to always show both
				**/
				$combine_taxes = apply_filters('wppizza_filter_combine_taxes', false);
				/*
					sum rounded taxes again to avoid anny possible rounding errors
					for the convenience keys taxes_included and taxes_added
				*/
				$sum_rounded_taxes = 0;


				/** get separate tax rates if applicable **/
				if($combine_taxes === false || $combine_taxes === null || $combine_taxes === 'force' ){

					foreach($order_values['summary']['tax_by_rate'] as $key => $val){

						if(!empty($val['total'])){
							/** set label **/
							$separate_taxes_label = !empty($taxes_included) ?  sprintf($blog_options['localization']['taxes_included'], ''.$val['rate'].'%') : sprintf($blog_options['localization']['item_tax_total'], ''.$val['rate'].'%') ;
							/** change label for 'shipping_handling */
							$separate_taxes_label = ($key == 'shipping') ? sprintf($blog_options['localization']['shipping_tax'], ''.$blog_options['order_settings']['shipping_tax_rate'].'%') : $separate_taxes_label;
							/** sum rounded taxes **/
							$sum_rounded_taxes += $val['total'];

							$summary['taxes'][$key]['sort']							=	!empty($taxes_included) ?  20 : 60 ;/*after items if taxes added, before tips if taxes included*/
							$summary['taxes'][$key]['class_ident']					=	'tax-'.$key.'';
							$summary['taxes'][$key]['label']						=	$separate_taxes_label ;
							$summary['taxes'][$key]['value']						=	$val['total'] ;
							$summary['taxes'][$key]['value_formatted']				=	wppizza_format_price($val['total'], $currency) ;
						}
					}
				}

				/** get combined tax if applicable/set or set to both AND more than one separate in the first place **/
				if($combine_taxes === true || $combine_taxes === 'force' || ($combine_taxes === null && count($summary['taxes'])>1)){

					/** sum rounded taxes **/
					$sum_rounded_taxes += $order_values['summary']['taxes'];

					$summary['taxes']['full']['sort']							=	!empty($taxes_included) ?  20 : 60 ;/*after items if taxes added, before tips if taxes included*/
					$summary['taxes']['full']['class_ident']					=	'tax-total';
					$summary['taxes']['full']['label']							=	$blog_options['localization']['tax_total'] ;
					$summary['taxes']['full']['value']							=	$order_values['summary']['taxes'] ;
					$summary['taxes']['full']['value_formatted']				=	wppizza_format_price($order_values['summary']['taxes'], $currency) ;
				}
			}
		}

		/*
			for convenience, add all included / added / total taxes into summary
			this is not added to templates, but returned in some functions
			(e.g gateway helpers)
		*/
		if($get_template_parameters){
			$summary['tax_total']['template_default_sort']		= 0;
			$summary['tax_total']['template_default_enabled']	= false;
			$summary['tax_total']['template_parameter']			= false;
			$summary['tax_total']['template_row_default_css']	=	'';
			$summary['tax_total']['label']						=	'';
		}else{
			$summary['tax_total'][0]['sort'] = 0;
			$summary['tax_total'][0]['class_ident'] 	= 'tax-total';
			$summary['tax_total'][0]['label'] 			= $blog_options['localization']['tax_total'];
			$summary['tax_total'][0]['value'] 			= !empty($sum_rounded_taxes) ? $sum_rounded_taxes : 0 ;
			$summary['tax_total'][0]['value_formatted'] = !empty($sum_rounded_taxes) ? wppizza_format_price($sum_rounded_taxes, $currency) : '';
		}

		/*
			getting template parts only
		*/
		if($get_template_parameters){
			$summary['total_price_items']['template_default_sort']		=	10;
			$summary['total_price_items']['template_default_enabled']	=	true;
			$summary['total_price_items']['template_parameter']			=	true;
			$summary['total_price_items']['template_row_default_css']	=	'';
			$summary['total_price_items']['label']						=	$blog_options['localization']['order_items'];
		}else{
			$summary['total_price_items'][0]['sort']			=	10;
			$summary['total_price_items'][0]['class_ident']		=	'total-items';
			$summary['total_price_items'][0]['label']			=	$blog_options['localization']['order_items'];
			$summary['total_price_items'][0]['value']			=	$order_values['summary']['total_price_items'] ;
			$summary['total_price_items'][0]['value_formatted']	=	!empty($order_values['summary']['total_price_items']) ? wppizza_format_price($order_values['summary']['total_price_items'], $currency) : '' ;
		}


		/**************************************************
			tips are somewhat different
			if caller == 'orderpage' and generally enabled,
			show input field instead
		**************************************************/
		/*
			getting template parts only
		*/
		if($get_template_parameters){
			$summary['tips']['template_default_sort']			=	70;
			$summary['tips']['template_default_enabled']		=	true;
			$summary['tips']['template_parameter']				=	true;
			$summary['tips']['template_row_default_css']		=	'';
			$summary['tips']['label']							=	$blog_options['localization']['tips'];
		}else{
			if(wppizza_is_checkout()){
				$ctips_key = 'ctips';
				$ctips_options = isset($blog_options['order_form'][$ctips_key]) ? $blog_options['order_form'][$ctips_key] : false;
				/* check if enabled first */
				if(!empty($ctips_options['enabled'])){
					$summary['tips'][0]['sort']					=	70;
					$summary['tips'][0]['class_ident']			=	'tips';
					$summary['tips'][0]['label']				=	$blog_options['localization']['tips'];
					$summary['tips'][0]['value']				=	$order_values['summary']['tips'] ;
					$required_attribute = '';
					/* is pickup */
					if(!empty($order_values['summary']['self_pickup']) && !empty($ctips_options['required_on_pickup']) ){
						$required_attribute = 'required = "required" ';
					}
					/* is delivery */
					if(empty($order_values['summary']['self_pickup']) && !empty($ctips_options['required']) ){
						$required_attribute = 'required = "required" ';
					}
					$value = ($order_values['summary']['tips']!== false) ? wppizza_format_price($order_values['summary']['tips'], null) : '' ;
					/* wrap in div for possible error messages */
					$summary['tips'][0]['value_formatted']  = '<div><input id="'. $ctips_key .'" name="'. $ctips_key.'"  type="text" value="' . $value . '" placeholder="' .$ctips_options['placeholder'] . '"  ' . $required_attribute . ' /></div>';
				}

			}else{
				/* default tips display */
				$show_tips = (!empty($order_values['summary']['tips'])) ? true : false;
				/* tips enabled, required on pickup and order is pickup, else use natural setting above*/
				$show_tips = (!empty($blog_options['order_form']['ctips']['enabled']) && !empty($blog_options['order_form']['ctips']['required_on_pickup']) &&  !empty($order_values['summary']['self_pickup'])) ? true : $show_tips ;
				/* tips enabled, required on pickup and order is pickup, else use natural setting above*/
				$show_tips = (!empty($blog_options['order_form']['ctips']['enabled']) && !empty($blog_options['order_form']['ctips']['required']) &&  empty($order_values['summary']['self_pickup'])) ? true : $show_tips ;
				/* do not show tips (in cart for example) if they are not even set yet though */
				$show_tips = (!isset($order_values['summary']['tips']) || !is_numeric($order_values['summary']['tips'])) ? false : $show_tips;

				if(!empty($show_tips)){
					$summary['tips'][0]['sort']					=	70;
					$summary['tips'][0]['class_ident']			=	'tips';
					$summary['tips'][0]['label']				=	$blog_options['localization']['tips'];
					$summary['tips'][0]['value']				=	$order_values['summary']['tips'] ;
					$summary['tips'][0]['value_formatted']		=	wppizza_format_price($order_values['summary']['tips'], $currency) ;
				}
			}
		}

		/*
			total:
			always use isset here instead of empty as 0 should be displayed
		*/
		if(isset($order_values['summary']['total']) || $get_template_parameters){

			/*
				getting template parts only
			*/
			if($get_template_parameters){
				$summary['total']['template_default_sort']			=	80;
				$summary['total']['template_default_enabled']		=	true;
				$summary['total']['template_parameter']				=	true;
				$summary['total']['template_row_default_css']		=	'font-weight: 600; padding: 10px 0; border-top: 1px dotted #cecece';
				$summary['total']['label']							=	$blog_options['localization']['order_total'];
			}else{
				$summary['total'][0]['sort']						=	80;
				$summary['total'][0]['class_ident']					=	'total';
				$summary['total'][0]['label']						=	$blog_options['localization']['order_total'];
				$summary['total'][0]['value']						=	$order_values['summary']['total'] ;
				$summary['total'][0]['value_formatted']				=	wppizza_format_price($order_values['summary']['total'], $currency) ;
			}
		}


		/*
			template default sort, labels an keys only
		*/
		if($get_template_parameters){
			$template_enabled_parameters = array();
			/*
				global section styles
			*/
			if($get_template_parameters=='emails'){
			$template_enabled_parameters['style'] = array();
            $template_enabled_parameters['style']['table'] = 'margin: 0 0 10px; border-top: 1px dotted #cecece';
            $template_enabled_parameters['style']['th'] = '';
            $template_enabled_parameters['style']['td-lft'] = 'text-align: left; padding:2px';
            $template_enabled_parameters['style']['td-rgt'] = 'text-align: right; padding:2px';
			}
			/*
				label for section
			*/
			$template_enabled_parameters['labels']['label'] 	= $blog_options['localization']['templates_label_'.$section_key.''];
			/*
				section enabled - defaults to true for new templates
			*/
			$template_enabled_parameters['section_enabled'] = true;
			/*
				section label enabled - defaults to true for new templates
			*/
			$template_enabled_parameters['label_enabled'] = false;


			/*************************************************************************
				return section parameters
			*************************************************************************/
			$template_enabled_parameters['parameters'] = array();
			foreach($summary as $summary_key=>$summary_values){
				if(!empty($summary_values['template_parameter'])){
					/* parameters: sort , enabled, label */
					$template_enabled_parameters['parameters'][$summary_key] = array();
					$template_enabled_parameters['parameters'][$summary_key]['sort'] 	= $summary_values['template_default_sort'];
					$template_enabled_parameters['parameters'][$summary_key]['enabled'] = $summary_values['template_default_enabled'];
					$template_enabled_parameters['parameters'][$summary_key]['label']  	= $summary_values['label'];
					/* parameters: template styles */
					if($get_template_parameters=='emails'){
					$template_enabled_parameters['style'][''.$summary_key.'-tdall']  			= $summary_values['template_row_default_css'];
					}
				}
			}


			/*************************************************************************
				overwrite sort order and values with set/saved values if there are any
			*************************************************************************/
			if(!empty($template_values)){
				/* global section styles */
				if($get_template_parameters=='emails'){
            		$template_enabled_parameters['style']['table'] = !empty($template_values['sections'][$section_key]['style']['table']) ? $template_values['sections'][$section_key]['style']['table'] : '';
            		$template_enabled_parameters['style']['th'] = !empty($template_values['sections'][$section_key]['style']['th']) ? $template_values['sections'][$section_key]['style']['th'] : '';
            		$template_enabled_parameters['style']['td-lft'] = !empty($template_values['sections'][$section_key]['style']['td-lft']) ? $template_values['sections'][$section_key]['style']['td-lft'] : '';
            		$template_enabled_parameters['style']['td-rgt'] = !empty($template_values['sections'][$section_key]['style']['td-rgt']) ? $template_values['sections'][$section_key]['style']['td-rgt'] : '';
				}

				/* section enabled */
				$template_enabled_parameters['section_enabled'] = !empty($template_values['sections'][$section_key]['section_enabled']) ? true : false;
				/* section label enabled */
				$template_enabled_parameters['label_enabled'] = !empty($template_values['sections'][$section_key]['label_enabled']) ? true : false;

				$resort = 0;
				foreach($template_values['sort'][$section_key] as $parameter_key=>$enabled){
					/* resort */
					$template_enabled_parameters['parameters'][$parameter_key]['sort'] = $resort;
					/* enabled ?*/
					$template_enabled_parameters['parameters'][$parameter_key]['enabled'] = !empty($template_values['sections'][$section_key]['parameters'][$parameter_key]['enabled']) ? true : false;
					/* css*/
					if($get_template_parameters=='emails'){
					$template_enabled_parameters['style'][$parameter_key.'-tdall'] = !empty($template_values['sections'][$section_key]['style'][$parameter_key.'-tdall']) ? $template_values['sections'][$section_key]['style'][$parameter_key.'-tdall'] : '';
					}

				$resort++;
				}
			}

			/* sort */
			if(is_array($template_enabled_parameters['parameters'])){
				asort($template_enabled_parameters['parameters']);
			}

		return $template_enabled_parameters;
		}


		$summary = apply_filters('wppizza_filter_summary_details_formatted', $summary, $order_values, $caller, $template_id);

	return $summary;
	}

}
?>