<?php
/**
* WPPIZZA_WPML Class
*
* @package     WPPIZZA
* @subpackage  WPPIZZA_WPML
* @copyright   Copyright (c) 2015, Oliver Bach
* @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
* @since       3.0
*
*/
if ( ! defined( 'ABSPATH' ) ) exit;/*Exit if accessed directly*/


/************************************************************************************************************************
*
*
*	WPPIZZA_WPML
*
*
************************************************************************************************************************/
class WPPIZZA_WPML{

	private $settings_page = 'tools';/* which admin subpage (identified there by this->class_key) are we adding this to */
	private $tab_key = 'miscellaneous';/* must be unique within this admin page*/
	private $section_key = 'wpml';


	function __construct() {
		global $wppizza_options, $sitepress;
		/*
			[add settings to admin]
			wppizza auto registers wpml strings on install and update, provided wpml is enabled already
			if not, add ability to wpml register strings to tools - might be needed if wpml was installed after wppizza
		*/
		if(is_admin()){

			/** run register strings on install **/
			add_action('wppizza_plugin_install', array($this, 'wpml_on_install_plugin'));
			/** run (de)register strings on plugin update **/
			add_action('wppizza_plugin_update', array($this, 'wpml_on_update_plugin'));


			/*** add to a specific tab ***/
			add_filter('wppizza_filter_admin_tabs_'.$this->settings_page.'', array($this, 'admin_tabs'), 10);
			/* add to admin options tools page*/
			add_filter('wppizza_filter_settings_sections_'.$this->settings_page.'', array($this, 'admin_options_settings'), 20, 5);
			/* add admin options settings page fields */
			add_action('wppizza_admin_settings_section_fields_'.$this->settings_page.'', array($this, 'admin_options_fields_settings'), 20, 5);
			/**add default options **/
			//add_filter('wppizza_filter_setup_default_options', array( $this, 'options_default'));
			/**validate options**/
			add_filter('wppizza_filter_options_validate', array( $this, 'options_validate'), 1000, 2 );/* set high priority to catch set options */
		}
		/*

			frontend - WPML >=4.0+

		*/
		if ( version_compare( ICL_SITEPRESS_VERSION, '4', '>=' ) ) {
			/*
				set a session language variable as WPML - inexplicably - deos not also pass on current langauge to ajax requests by default
				but "Language filtering for AJAX operations" has to be explicitly enabled - for reasons i cannot understand to be honest
			*/
			add_action( 'init', array( $this, 'init_wpml_language_session'), 5); /* priority should be <10 and >= 5 */
			/*
				alter WPML related session language variable on langugae switch (non-ajax)
			*/
			add_action( 'wpml_language_has_switched', array( $this, 'switch_wpml_language') );


			if ( !is_admin() || ( is_admin() && defined( 'DOING_AJAX' ) && DOING_AJAX /* needs testing && ( isset($_POST['action']) && $_POST['action']=='wppizza_json') */)){
				/* set wpmled order page and amend order link - frontend / ajax only */
				add_action( 'init', array( $this, 'wpml_orderpage_wpml4'),10 );
				/* set wpmled strings  - frontend / ajax only */
				add_action( 'init', array( $this, 'wpml_strings_wpml4'), 10 );
				/* set wpmled gateway settings - frontend / ajax only */
				add_filter('wppizza_filter_gateway_objects', array( $this, 'wpml_gateways'),5 );
			}


		}



		/*

			frontend - WPML <4.0

		*/

    	/*
    		deprecated apparently, but leave here for future reference for a bit

    		#original

    		if($sitepress->get_current_language() != $sitepress->get_default_language())


			#apparently,

			$wpml_current_language = apply_filters( 'wpml_current_language', null );
			if($wpml_current_language != $sitepress->get_default_language())

			could also be used instead of  $sitepress->get_current_language();
			however, that filter hasnt been around for that long from what i can tell,

			#so let's use the
			ICL_LANGUAGE_CODE constant for the time being which has been around for ages
    	*/
    	if ( version_compare( ICL_SITEPRESS_VERSION, '4', '<' ) ) {
    		if(ICL_LANGUAGE_CODE != $sitepress->get_default_language()){
				if ( !is_admin() || ( is_admin() && defined( 'DOING_AJAX' ) && DOING_AJAX /* needs testing && ( isset($_POST['action']) && $_POST['action']=='wppizza_json') */)){
					/* set wpmled order page and amend order link - frontend / ajax only*/
					add_filter('wppizza_filter_options', array( $this, 'wpml_orderpage'),5 );	/*lets use a reasonably highg priority here */
					/* set wpmled strings*/
					add_filter('wppizza_filter_options', array( $this, 'wpml_strings'),5 );	/*lets use a reasonably highg priority here */
					/* set wpmled gateway settings*/
					add_filter('wppizza_filter_gateway_objects', array( $this, 'wpml_gateways'),5 );
				}
    		}
    	}
	}

/********************************************************************************************************************************************************
*
*
*			[WPML WORKAROUND FOR AJAX REQUESTS ]
*
*
********************************************************************************************************************************************************/
	/*
		set initial wpml language in session variable if it has not ever been set yet
		@since 3.6.2
		@retun void
	*/
	function init_wpml_language_session(){
		if (!session_id()) {session_start();}
		if(!isset($_SESSION[WPPIZZA_SLUG.'_userdata']['wpml_lang']) && defined('ICL_LANGUAGE_CODE')){
			$_SESSION[WPPIZZA_SLUG.'_userdata']['wpml_lang'] = ICL_LANGUAGE_CODE;
		}
	}
	/*
		alter WPML session language variable on change
		pseudo static - non ajax requests only (the whole point of doing this)
		@since 3.6.2
		@retun void
	*/
	function switch_wpml_language(){
		static $switched = null;
		if($switched === null && !is_admin()){
			if(ICL_LANGUAGE_CODE != $_SESSION[WPPIZZA_SLUG.'_userdata']['wpml_lang']){
				$_SESSION[WPPIZZA_SLUG.'_userdata']['wpml_lang'] = ICL_LANGUAGE_CODE;
			}
		$switched = true;
		}
	}
/********************************************************************************************************************************************************
*
*
*			[Admin - Install/Update]
*
*
********************************************************************************************************************************************************/
	function wpml_on_install_plugin($options){
		$this->wpml_register_all_strings($options);
	}
	function wpml_on_update_plugin($options){

		/* confirmation form */
		$this->wpml_register_confirmation_settings($options['options_added'], $options['options_removed']);

		/* order form */
		$this->wpml_register_orderform_settings($options['options_added'], $options['options_removed']);

		/* main localization */
		$this->wpml_register_localization_strings($options['options_added'], $options['options_removed']);
	}
/********************************************************************************************************************************************************
*
*
*			[Admin - Tools]
*
*
********************************************************************************************************************************************************/

	/*********************************************************
			[add section to a particular tab]
	*********************************************************/
	function admin_tabs($tabs){
		$tabs['tab'][$this->tab_key]['sections'][] = $this->section_key;
	return $tabs;
	}

	/*------------------------------------------------------------------------------
	#	[settings section - setting page]
	#	@since 3.0
	#	@return array()
	------------------------------------------------------------------------------*/
	function admin_options_settings($settings, $sections, $fields, $inputs, $help){
		/** string translation not installed **/
		if(!function_exists('icl_register_string')){
			return $settings;
		}


		/*section*/
		if($sections){
			$settings['sections'][$this->section_key] =  __('WPML', 'wppizza-admin');
		}

		/*help*/
		if($help){
		}

		/*fields*/
		if($fields){
			$field = 'wpml_register_string_translations';
			$settings['fields'][$this->section_key][$field] = array(__('(De)Register WPML Strings', 'wppizza-admin') , array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>sprintf( __( 'If you have enabled/added WPML *after* installing or updating %s, check this box and save to register all translatable %s strings.', 'wppizza-admin' ), WPPIZZA_NAME, WPPIZZA_NAME),
				'description'=>array(
					sprintf(__( 'Note: Once WPML string translations have been registered, you can also run this again at any time to de-register any obsolete %s translations that may have been added over time (such as removed additives, sizes etc)', 'wppizza-admin' ), WPPIZZA_NAME)
				)
			));
		}
	return $settings;
	}
	/*------------------------------------------------------------------------------
	#	[output option fields - setting page]
	#	@since 3.0
	#	@return array()
	------------------------------------------------------------------------------*/
	function admin_options_fields_settings($wppizza_options, $options_key, $field, $label, $description){
		/* force string translation registration */
		if($field=='wpml_register_string_translations'){
			print'<label>';
				print "<input name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' type='checkbox' value='1' />";
				print'' . $label . '';
			print'</label>';
			print'' . $description . '';
		}
	}
	/****************************************************************
	*
	*	[register translatable strings on install / update]
	*	$parameter $options array() | filter passing on filtered options
	*	@since 3.0
	*	@return array()
	*
	****************************************************************/
//	function options_default($options){
//			//$this->wpml_register_all_strings();
//		return $options;
//	}
	/*------------------------------------------------------------------------------
	#	[(de)register strings on update]
	#
	#	@since 3.0
	#	@return array()
	------------------------------------------------------------------------------*/
	function options_validate($options, $input){
		/**make sure we get the full array on install/update**/
		if ( empty( $_POST['_wp_http_referer'] ) ) {
			return $input;
		}
		/**
			tools -> miscellaneous
		**/
		if(isset($_POST[''.WPPIZZA_SLUG.'_'.$this->settings_page.'_'.$this->tab_key.''])){
			if(!empty($input[$this->settings_page]['wpml_register_string_translations'])){
				/** register used */
				$registered_string_ids = $this->wpml_register_all_strings();

				/** cleanup unused wpml strings **/
				$this->wpml_unregister_unused_strings($registered_string_ids);
			}
		}
		/**
			order settings
		**/
		if(isset($_POST[''.WPPIZZA_SLUG.'_order_settings'])){
			$this->wpml_register_order_settings($options);
		}
		/**
			meal sizes
		**/
		if(isset($_POST[''.WPPIZZA_SLUG.'_meal_sizes'])){
			$this->wpml_register_sizes($options, true);
		}
		/**
			additives
		**/
		if(isset($_POST[''.WPPIZZA_SLUG.'_additives'])){
			$this->wpml_register_additives($options, true);
		}


		/**
			gateway settings
		**/
		if(isset($_POST[''.WPPIZZA_SLUG.'_gateways'])){
			$this->wpml_register_gateway_strings($options);
		}


	return $options;
	}

/********************************************************************************************************************************************************
*
*
*			[Frontend]
*
*
********************************************************************************************************************************************************/
	/**
		wpml select of order page
		and amend order link (if confirmation form is used)
	**/
	function wpml_orderpage($wppizza_options){

		if(!function_exists('icl_object_id')) {return $wppizza_options;}

		/* order page link */
		$wppizza_options['order_settings']['orderpage']=icl_object_id($wppizza_options['order_settings']['orderpage'], 'page');

		/*confirmation page enabled -> amend order link translate**/
		if(!empty($wppizza_options['confirmation_form']['confirmation_form_amend_order_link'])){
			$wppizza_options['confirmation_form']['confirmation_form_amend_order_link']=icl_object_id($wppizza_options['confirmation_form']['confirmation_form_amend_order_link'], 'page');
		}

	return $wppizza_options;
	}

	/**
		WPML v4+
		wpml select of order page
		and amend order link (if confirmation form is used)
		@since 3.6.2
		@sets/aters global wppizza wpml'd options
	**/
	function wpml_orderpage_wpml4(){
		global $wppizza_options;

		if(!function_exists('icl_object_id')) {return $wppizza_options;}

		$wpml_session_lng = $_SESSION[WPPIZZA_SLUG.'_userdata']['wpml_lang'];

		/* order page link */
		$wppizza_options['order_settings']['orderpage'] = apply_filters( 'wpml_object_id', $wppizza_options['order_settings']['orderpage'], 'page', true, $wpml_session_lng );

		/*confirmation page enabled -> amend order link translate**/
		if(!empty($wppizza_options['confirmation_form']['confirmation_form_amend_order_link'])){
			$wppizza_options['confirmation_form']['confirmation_form_amend_order_link']= apply_filters( 'wpml_object_id', $wppizza_options['confirmation_form']['confirmation_form_amend_order_link'], 'page', true, $wpml_session_lng );
		}
	}




	/**
		wpml'd strings
	**/
	function wpml_strings($wppizza_options){
		if(!function_exists('icl_translate')) {return $wppizza_options;}

		/**
			general localization strings
		**/

		foreach($wppizza_options['localization'] as $k=>$str){
			$wppizza_options['localization'][$k] = icl_translate(WPPIZZA_SLUG,''. $k.'', $str);
		}

		/**
			additives
		**/
		foreach($wppizza_options['additives'] as $k=>$arr){
			$wppizza_options['additives'][$k]['name'] = icl_translate(WPPIZZA_SLUG,'additives_'. $k.'_name', $arr['name']);
			$wppizza_options['additives'][$k]['sort'] = icl_translate(WPPIZZA_SLUG,'additives_'. $k.'_sort', $arr['sort']);
		}

		/**
			meal sizes
		**/
		foreach($wppizza_options['sizes'] as $k=>$arr){
			foreach($arr as $sKey=>$sArr){
				$wppizza_options['sizes'][$k][$sKey]['lbl']  = icl_translate(WPPIZZA_SLUG,'sizes_'. $k.'_'.$sKey.'', $sArr['lbl']);
			}
		}

		/**
			order form
		**/
		foreach($wppizza_options['order_form'] as $k=>$arr){
			$wppizza_options['order_form'][$k]['lbl'] = icl_translate(WPPIZZA_SLUG,'order_form_'. $k.'', $arr['lbl']);
		}

		/**
			confrmation form
		**/
		/* formfields */
		foreach($wppizza_options['confirmation_form']['formfields'] as $k=>$arr){
			$wppizza_options['confirmation_form']['formfields'][$k]['lbl'] = icl_translate(WPPIZZA_SLUG,'confirmation_form_'. $k.'', $arr['lbl']);
		}
		/* localization */
		foreach($wppizza_options['confirmation_form']['localization'] as $k=>$str){
			$wppizza_options['confirmation_form']['localization'][$k]  = icl_translate(WPPIZZA_SLUG,'confirmation_'. $k.'', $str);
		}

		/**
			order settings
		**/
		/**order email attachments **/
		foreach($wppizza_options['order_settings']['order_email_attachments'] as $k=>$arr){
			$wppizza_options['order_settings']['order_email_attachments'][$k] = icl_translate(WPPIZZA_SLUG,'order_email_attachments_'. $k.'', $arr);
		}
		/**order email bcc **/
		foreach($wppizza_options['order_settings']['order_email_bcc'] as $k=>$arr){
			$wppizza_options['order_settings']['order_email_bcc'][$k] = icl_translate(WPPIZZA_SLUG,'order_email_bcc_'. $k.'', $arr);
		}
		/**order email to **/
		foreach($wppizza_options['order_settings']['order_email_to'] as $k=>$arr){
			$wppizza_options['order_settings']['order_email_to'][$k] = icl_translate(WPPIZZA_SLUG,'order_email_to_'. $k.'', $arr);
		}
		/**order from name**/
		if($wppizza_options['order_settings']['order_email_from_name'] != '' ){
			$wppizza_options['order_settings']['order_email_from_name'] = icl_translate(WPPIZZA_SLUG,'order_email_from_name', $wppizza_options['order_settings']['order_email_from_name']);
		}
		/**order from**/
		if($wppizza_options['order_settings']['order_email_from'] != ''){
			$wppizza_options['order_settings']['order_email_from'] =  icl_translate(WPPIZZA_SLUG,'order_email_from',$wppizza_options['order_settings']['order_email_from']);
		}


	return $wppizza_options;
	}

	/**
		WPML v4+
		wpml strings
		@since 3.6.2
		@sets/aters global wppizza wpml'd options
	**/
	function wpml_strings_wpml4(){
		global $wppizza_options;

		if(!function_exists('icl_translate')) {
			return $wppizza_options;
		}


		$wpml_session_lng = $_SESSION[WPPIZZA_SLUG.'_userdata']['wpml_lang'];

		/**
			general localization strings
		**/
		foreach($wppizza_options['localization'] as $k=>$str){
			$wppizza_options['localization'][$k] = apply_filters('wpml_translate_single_string', $str, WPPIZZA_SLUG, ''. $k.'', $wpml_session_lng);
		}

		/**
			additives
		**/
		foreach($wppizza_options['additives'] as $k=>$arr){
			$wppizza_options['additives'][$k]['name'] = apply_filters('wpml_translate_single_string', $arr['name'], WPPIZZA_SLUG, ''. $k.'_name', $wpml_session_lng);
			$wppizza_options['additives'][$k]['name'] = apply_filters('wpml_translate_single_string', $arr['sort'], WPPIZZA_SLUG, ''. $k.'_sort', $wpml_session_lng);
		}

		/**
			meal sizes
		**/
		foreach($wppizza_options['sizes'] as $k=>$arr){
			foreach($arr as $sKey=>$sArr){
				$wppizza_options['sizes'][$k][$sKey]['lbl'] = apply_filters('wpml_translate_single_string', $sArr['lbl'], WPPIZZA_SLUG, 'sizes_'. $k.'_'.$sKey.'', $wpml_session_lng);
			}
		}

		/**
			order form
		**/
		foreach($wppizza_options['order_form'] as $k=>$arr){
			$wppizza_options['order_form'][$k]['lbl'] = apply_filters('wpml_translate_single_string', $arr['lbl'], WPPIZZA_SLUG, 'order_form_'. $k.'', $wpml_session_lng);
		}

		/**
			confrmation form
		**/
		/* formfields */
		foreach($wppizza_options['confirmation_form']['formfields'] as $k=>$arr){
			$wppizza_options['confirmation_form']['formfields'][$k]['lbl'] = apply_filters('wpml_translate_single_string', $arr['lbl'], WPPIZZA_SLUG, 'confirmation_form_'. $k.'', $wpml_session_lng);
		}
		/* localization */
		foreach($wppizza_options['confirmation_form']['localization'] as $k=>$str){
			$wppizza_options['confirmation_form']['localization'][$k]  = apply_filters('wpml_translate_single_string', $str, WPPIZZA_SLUG,'confirmation_'. $k.'', $wpml_session_lng);
		}

		/**
			order settings
		**/
		/**order email attachments **/
		foreach($wppizza_options['order_settings']['order_email_attachments'] as $k=>$arr){
			$wppizza_options['order_settings']['order_email_attachments'][$k] = apply_filters('wpml_translate_single_string', $arr, WPPIZZA_SLUG,'order_email_attachments_'. $k.'', $wpml_session_lng);
		}
		/**order email bcc **/
		foreach($wppizza_options['order_settings']['order_email_bcc'] as $k=>$arr){
			$wppizza_options['order_settings']['order_email_bcc'][$k] = apply_filters('wpml_translate_single_string', $arr, WPPIZZA_SLUG,'order_email_bcc_'. $k.'', $wpml_session_lng);
		}
		/**order email to **/
		foreach($wppizza_options['order_settings']['order_email_to'] as $k=>$arr){
			$wppizza_options['order_settings']['order_email_to'][$k] = apply_filters('wpml_translate_single_string', $arr, WPPIZZA_SLUG,'order_email_to_'. $k.'', $wpml_session_lng);
		}
		/**order from name**/
		if($wppizza_options['order_settings']['order_email_from_name'] != '' ){
			$wppizza_options['order_settings']['order_email_from_name'] = apply_filters('wpml_translate_single_string', $wppizza_options['order_settings']['order_email_from_name'], WPPIZZA_SLUG, 'order_email_from_name', $wpml_session_lng);
		}
		/**order from**/
		if($wppizza_options['order_settings']['order_email_from'] != ''){
			$wppizza_options['order_settings']['order_email_from'] =  apply_filters('wpml_translate_single_string', $wppizza_options['order_settings']['order_email_from'] , WPPIZZA_SLUG, 'order_email_from', $wpml_session_lng);
		}

	}
	/**
		wpml'd gateway settings
	**/
	function wpml_gateways($gateway_objects){
		if(!function_exists('icl_translate')) {return $gateway_objects;}

		foreach($gateway_objects as $gateway_ident => $gateway){
			$gw_ident = strtolower($gateway_ident);
			$wpml_domain = WPPIZZA_SLUG.'_gateway_'.$gw_ident;
			/* label and info */
			$gateway_objects->$gateway_ident->label = icl_translate($wpml_domain, '_gateway_label', $gateway_objects->$gateway_ident->label);
			$gateway_objects->$gateway_ident->additional_info = icl_translate($wpml_domain, '_gateway_additional_info', $gateway_objects->$gateway_ident->additional_info);

			/* get wpml'd settings */
			foreach($gateway_objects->$gateway_ident->gateway_settings as $k => $setting){
				if(!empty($setting['wpml'])){
					$gateway_objects->$gateway_ident->gateway_settings[$k]['value'] = icl_translate($wpml_domain, $setting['key'], $setting['value']);
				}
			}
		}
	return $gateway_objects;
	}

/********************************************************************************************************************************************************
*
*
*			[Helpers]
*
*
********************************************************************************************************************************************************/
	/*
		deregister all wppizza wpml strings
		that are not in use
	*/
	function wpml_unregister_unused_strings($protected_ids){
    	global $wpdb;
    	$wpml_string = $wpdb->get_results($wpdb->prepare("SELECT id, context, name FROM ". $wpdb->prefix . "icl_strings WHERE context=%s OR context LIKE '%s'", WPPIZZA_SLUG, WPPIZZA_SLUG.'_gateway_%'));
		if(!empty($wpml_string)){
		foreach($wpml_string as $arr){
			if(!isset($protected_ids[$arr->id])){
				icl_unregister_string($arr->context,$arr->name);
			}
		}}
	}
	/*
		register translatable strings
		order of functions determines order in
		WPML String Translation page (in reverse !)
		return string translation id's
	*/
	function wpml_register_all_strings($options = null){

		$registered_string_ids = array();

		if(!function_exists('icl_register_string')){return;}
		global $wppizza_options;
		/** on install, use options parameters passed  */
		if($options !== null){
			$wppizza_options = $options;
		}

		/* gateway settings */
		$registered_string_ids[] = $this->wpml_register_gateway_strings($wppizza_options);
		/* global settings */
		$registered_string_ids[] = $this->wpml_register_global_settings($wppizza_options);
		/* order settings */
		$registered_string_ids[] = $this->wpml_register_order_settings($wppizza_options);
		/* confirmation form */
		$registered_string_ids[] = $this->wpml_register_confirmation_settings($wppizza_options);
		/* additives */
		$registered_string_ids[] = $this->wpml_register_additives($wppizza_options);
		/* sizes */
		$registered_string_ids[] = $this->wpml_register_sizes($wppizza_options);
		/* order form */
		$registered_string_ids[] = $this->wpml_register_orderform_settings($wppizza_options);
		/*
			localization -
			registered last to be topmost in string translation screen
		*/
		$registered_string_ids[] = $this->wpml_register_localization_strings($wppizza_options);

		$string_ids = array();
		foreach($registered_string_ids as $id_array){
			foreach($id_array as $string_id){
				$string_ids[$string_id]=$string_id;
			}
		}



	return $string_ids;
	}


/***********************************************************************************************
*
*
*		register translatable strings
*
*
***********************************************************************************************/
	/*
		global settings
	*/
	function wpml_register_global_settings($set_options){
		$registered_string_ids = array();
		/**single item permalink**/
		if($set_options['settings']['single_item_permalink_rewrite'] !='' ){
			$registered_string_ids[] = icl_register_string(WPPIZZA_SLUG, 'single_item_permalink_rewrite', $set_options['settings']['single_item_permalink_rewrite']);
		}

	return $registered_string_ids;
	}
	/*
		order settings
	*/
	function wpml_register_order_settings($set_options){
		$registered_string_ids = array();

		/**order email attachments **/
		if(!empty($set_options['order_settings']['order_email_attachments'])){
		arsort($set_options['order_settings']['order_email_attachments']);/* reverse sort to - somewhat - correspond with order set */
		foreach($set_options['order_settings']['order_email_attachments'] as $k=>$arr){
			if($arr != ''){
				$registered_string_ids[] = icl_register_string(WPPIZZA_SLUG,'order_email_attachments_'. $k.'', $arr);
			}
		}}
		/**order email bcc **/
		if(!empty($set_options['order_settings']['order_email_bcc'])){
		arsort($set_options['order_settings']['order_email_bcc']);/* reverse sort to - somewhat - correspond with order set */
		foreach($set_options['order_settings']['order_email_bcc'] as $k=>$arr){
			if($arr != ''){
				$registered_string_ids[] = icl_register_string(WPPIZZA_SLUG,'order_email_bcc_'. $k.'', $arr);
			}
		}}

		/**order email to **/
		if(!empty($set_options['order_settings']['order_email_to'])){
		arsort($set_options['order_settings']['order_email_to']);/* reverse sort to - somewhat - correspond with order set */
		foreach($set_options['order_settings']['order_email_to'] as $k=>$arr){
			if($arr != ''){
				$registered_string_ids[] = icl_register_string(WPPIZZA_SLUG,'order_email_to_'. $k.'', $arr);
			}
		}}

		/**order from name**/
		if($set_options['order_settings']['order_email_from_name'] != '' ){
			$registered_string_ids[] = icl_register_string(WPPIZZA_SLUG,'order_email_from_name', $set_options['order_settings']['order_email_from_name']);
		}
		/**order from**/
		if($set_options['order_settings']['order_email_from'] != ''){
			$registered_string_ids[] = icl_register_string(WPPIZZA_SLUG,'order_email_from',$set_options['order_settings']['order_email_from']);
		}

	return $registered_string_ids;
	}

	/*
		order form
	*/
	function wpml_register_orderform_settings($set_options, $removed_options = false){
		$registered_string_ids = array();

		/* unregister obsolete strings */
		if(!empty($removed_options['order_form'])){
		foreach($removed_options['order_form'] as $k=>$str){
			icl_unregister_string(WPPIZZA_SLUG,'order_form_'.$k);
		}}


		if(!empty($set_options['order_form'])){
		arsort($set_options['order_form']);/* reverse sort to - somewhat - correspond with order set */
		foreach($set_options['order_form'] as $k=>$arr){
			if($arr['lbl'] != ''){
				$registered_string_ids[] = icl_register_string(WPPIZZA_SLUG,'order_form_'. $k.'', $arr['lbl']);
			}
		}}

	return $registered_string_ids;
	}

	/*
		confirmation form
	*/
	function wpml_register_confirmation_settings($set_options, $removed_options = false){
		$registered_string_ids = array();

		/* unregister obsolete strings */
		if(!empty($removed_options['confirmation_form']['formfields'])){
		foreach($removed_options['confirmation_form']['formfields'] as $k=>$str){
			icl_unregister_string(WPPIZZA_SLUG,'confirmation_form_'.$k);
		}}

		/*
			confirmation form -> formfields
		*/
		if(!empty($set_options['confirmation_form']['formfields'])){
		arsort($set_options['confirmation_form']['formfields']);/* reverse sort to - somewhat - correspond with order set */
		foreach($set_options['confirmation_form']['formfields'] as $k=>$arr){
			if($arr['lbl'] != ''){
				$registered_string_ids[] = icl_register_string(WPPIZZA_SLUG,'confirmation_form_'. $k.'', $arr['lbl']);
			}
		}}



		/* unregister obsolete strings */
		if(!empty($removed_options['confirmation_form']['localization'])){
		foreach($removed_options['confirmation_form']['localization'] as $k=>$str){
			icl_unregister_string(WPPIZZA_SLUG,'confirmation_'.$k);
		}}
		/*
			confirmation form -> localization
		*/
		if(!empty($set_options['confirmation_form']['localization'])){
		arsort($set_options['confirmation_form']['localization']);/* reverse sort to - somewhat - correspond with order set */
		foreach($set_options['confirmation_form']['localization'] as $k=>$str){
			if($str != ''){
				$registered_string_ids[] = icl_register_string(WPPIZZA_SLUG,'confirmation_'. $k.'', $str);
			}
		}}

	return $registered_string_ids;
	}

	/*
		gateway settings
	*/
	function wpml_register_gateway_strings($set_options){
		$registered_string_ids = array();
		/**
			get gateways, gateway options
		**/
		$gateways = WPPIZZA() -> register_gateways;
		$gateways -> gateways_get_editable_settings($gateways -> registered_gateways);

		/**
			loop through gateway settings, registering strings where enabled
		**/
		if(!empty($gateways->registered_gateways)){
		foreach($gateways->registered_gateways as $gateway_ident => $obj){
			$gw_ident = strtolower($gateway_ident);
			$wpml_domain = WPPIZZA_SLUG.'_gateway_'.$gw_ident;
			$gw_settings = $obj->gatewaySettings;
			if(!empty($gw_settings)){
			foreach($gw_settings as $key => $arr){
				if(!empty($arr['wpml']) && $arr['value'] != '' ){
					$registered_string_ids[] = icl_register_string($wpml_domain, $arr['key'], $arr['value']);
				}
			}}
		}}

	return $registered_string_ids;
	}

	/*
		meal sizes
	*/
	function wpml_register_sizes($set_options, $unregister = false){
		$registered_string_ids = array();

		/* unregister obsolete strings */
		if($unregister){
			global $wppizza_options;
			$current = $wppizza_options['sizes'];
			$obsolete = array_diff_key($current, $set_options['sizes']);
			if(!empty($obsolete)){
			foreach($obsolete as $obsolete_key => $arr){
				foreach($arr as $sKey=>$sArr){
					icl_unregister_string(WPPIZZA_SLUG,'sizes_'. $obsolete_key.'_'.$sKey.'');
				}
			}}
		}


		if(!empty($set_options['sizes'])){
		krsort($set_options['sizes']);/* reverse sort to - somewhat - correspond with order set */
		foreach($set_options['sizes'] as $k=>$arr){
			krsort($arr);/* reverse sort to - somewhat - correspond with order set */
			foreach($arr as $sKey=>$sArr){
				if($sArr['lbl'] != ''){
					$registered_string_ids[] = icl_register_string(WPPIZZA_SLUG,'sizes_'. $k.'_'.$sKey.'', $sArr['lbl']);
				}
			}
		}}

	return $registered_string_ids;
	}

	/*
		additives
	*/
	function wpml_register_additives($set_options, $unregister = false){
		$registered_string_ids = array();

		/* unregister obsolete strings */
		if($unregister){
			global $wppizza_options;
			$current = $wppizza_options['additives'];
			$obsolete = array_diff_key($current, $set_options['additives']);
			if(!empty($obsolete)){
			foreach($obsolete as $obsolete_key => $arr){
				icl_unregister_string(WPPIZZA_SLUG,'additives_'. $obsolete_key.'_name');
				icl_unregister_string(WPPIZZA_SLUG,'additives_'. $obsolete_key.'_sort');
			}}
		}

		if(!empty($set_options['additives'])){
		arsort($set_options['additives']);/* reverse sort to - somewhat - correspond with order set */
		foreach($set_options['additives'] as $k=>$arr){
			if($arr['name'] != ''){
				$registered_string_ids[] = icl_register_string(WPPIZZA_SLUG,'additives_'. $k.'_name', $arr['name']);
			}
			if($arr['sort'] != ''){
				$registered_string_ids[] = icl_register_string(WPPIZZA_SLUG,'additives_'. $k.'_sort', $arr['sort']);
			}
		}}

	return $registered_string_ids;
	}

	/*
		localization strings
	*/
	function wpml_register_localization_strings($set_options, $removed_options = false){
		$registered_string_ids = array();

		/* unregister obsolete strings */
		if(!empty($removed_options['localization'])){
		foreach($removed_options['localization'] as $k=>$str){
			icl_unregister_string(WPPIZZA_SLUG,$k);
		}}

		if(!empty($set_options['localization'])){
		arsort($set_options['localization']);/* reverse sort to - somewhat - correspond with order set */
		foreach($set_options['localization'] as $k=>$str){
			if($str != ''){
				$registered_string_ids[] = icl_register_string(WPPIZZA_SLUG,''. $k.'', $str);
			}
		}}

	return $registered_string_ids;
	}



}
/***************************************************************
*
*	[ini - if WPML]
*
***************************************************************/
if( defined('ICL_SITEPRESS_VERSION') && function_exists('icl_object_id') || function_exists('icl_register_string') ) {
	$WPPIZZA_WPML = new WPPIZZA_WPML();
}
?>