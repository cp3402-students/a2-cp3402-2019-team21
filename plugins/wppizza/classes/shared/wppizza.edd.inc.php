<?php
if ( ! defined( 'ABSPATH' ) ) exit;/*Exit if accessed directly*/
/******************************************************************************************************************
*
*	EDD_SL: http://wordpress.org/plugins/easy-digital-downloads/
*	Class to allow to integrate with EDD if used in additional extensions outside the WP repository.
*	Can be used to allow automatic update notifications of extensions to WPPizza in the WP Dashboard via EDD_SL.
*	Not used in the main WPPizza plugin (as any updates of WPPizza will automatically be available).
*	requires WPPIZZA_EDD_SL_PLUGIN_UPDATER in classes/wppizza.edd.plugin.updater.inc.php
*
******************************************************************************************************************/
if (!class_exists( 'WPPizza' ) ) {return ;}
class WPPIZZA_EDD_SL{

	private $wppizza_registered_gateways;

	private $gateways_edd_status;

	/*
		time - in seconds - when we should recheck
		since the last verification
		set to 0 for testing
	*/
	private $verification_interval = 7200;//7200;

	function __construct() {

		/* only loads on is_admin()anyway, but also skip if (frontend) ajax */
		if(defined( 'DOING_AJAX' ) && DOING_AJAX ){return;}

		/*
			[EDD for Gateways]
		*/
		add_action('admin_init',array($this, 'gateways_add_edd_gateway_filters'));
		add_action('current_screen', array($this, 'gateways_current_screen_edd_check'));

		/*
			[EDD for plugins]
		*/
		add_action('wppizza_edd_for_plugins', array($this, 'edd_for_plugins_markup'));//use do+action to output fields
		add_filter('wppizza_edd_for_plugins', array($this, 'edd_for_plugins_markup'), 10, 2);//use apply_filters to return output as variable

		add_action('wppizza_edd_verify', array( $this, 'edd_verify'), 10 , 8);
	}

/********************************************************************************
*
*
*	[EDD for Plugins]
*
*
********************************************************************************/
	function edd_verify($option_name = false, $current_options = false, $required_screen = false, $plugin_version = false, $edd_name = false, $edd_url = false, $path = '', $extra_get_param = 'tab', $extra_get_param_val = 'license'){

		$current_screen = get_current_screen();
		static $posted = null, $show_license = null ;

		/* temp for checking if there's an update (set to lower than current version) */
		//$plugin_version = 2;



		/*
			get current license key etc , if any
		*/
		$current_license_key = !empty($current_options['license']['key']) ? $current_options['license']['key'] : false ;
		$current_license_status = !empty($current_options['license']['status']) ? $current_options['license']['status'] : false ;
		$current_license_verified = !empty($current_options['license']['verified']) ? $current_options['license']['verified'] : 0 ;




		/******************************************
			setup edd updater but bypass on save, when license is not valid i the first place
			or we are on current licensing tab/page (as it automatically checks on load every so often)
		******************************************/
		if($current_license_status == 'valid'){
		if($current_screen->id != $required_screen  || empty($_GET[$extra_get_param]) || ($current_screen->id == $required_screen &&  $_GET[$extra_get_param]!= $extra_get_param_val) ){
			if(!empty($current_license_key) && empty($_POST[$option_name]['license']) ){
				$edd_updater = $this -> edd_load_plugin_updater($current_license_key, $edd_name, $edd_url, $plugin_version, $path);
			}
		}}


		/*****************************************
			run when saving license from license screen
		*****************************************/
		if($required_screen == 'options' && isset($_POST[$option_name]['license']) ){
			if($posted === null ){


				/*
					set new posted variables
					adding our currently saved vars
				*/
				$posted = array();
				$posted['activate'] 	= !empty($_POST[$option_name]['license']['activate']) ?  true : false ;
				$posted['deactivate'] 	= !empty($_POST[$option_name]['license']['deactivate']) ?  true : false ;
				$posted['key'] 			= wppizza_validate_alpha_only($_POST[$option_name]['license']['key']);
				$posted['verified'] 	= time();
				$posted['status'] 		= !empty($current_options['license']['status']) ? $current_options['license']['status'] : '' ;
				$posted['error'] 		= !empty($current_options['license']['error']) ? $current_options['license']['error'] : '' ;


				/*
					load edd
				*/
				$edd_updater = $this -> edd_load_plugin_updater($current_license_key, $edd_name, $edd_url, $plugin_version, $path);


				/*
					force deactivate action if simply changing keys
				*/
				if($posted['key'] != $current_license_key && $current_license_key!=''){
					/* not output or saved anywhere, just deactivate*/
					$deactivate = $this->edd_plugin_action('deactivate_license', $current_license_key, $edd_name, $edd_url, $plugin_version, $path);
					$forced_dectivation = true;
				}


				/*
					update status, verified and error if (de-)activating, provided license key is not empty to start off with
				*/
				if((!empty($posted['activate']) || !empty($posted['deactivate'])) && !empty($posted['key'])){

					$action = !empty($posted['activate']) ? 'activate_license' : 'deactivate_license';
					/* activate or deactivate */
					$edd = $this->edd_plugin_action($action, $posted['key'], $edd_name, $edd_url, $plugin_version, $path);

					/* set new status */
					$posted['status'] = $edd['status'];
					$posted['error'] = $edd['error'];
				}

				/*
					if license key empty, reset what we have also if not activating
				*/
				if(empty($posted['key']) || !empty($forced_dectivation)){
					$posted['status'] = '';
					$posted['error'] = false;
				}

			}

			/* set license data above as post vars */
			$_POST[$option_name]['license'] = $posted ;
		}

		/*********************************************************************
			when loading license page,
			depending on last verified
			and if key is not empty
		*********************************************************************/
		if(empty($_POST[$option_name]['license']) && !empty($current_license_key) && $show_license === null){
			if($current_screen->post_type==WPPIZZA_POST_TYPE && $current_screen->id == $required_screen  && !empty($_GET[$extra_get_param]) &&  $_GET[$extra_get_param]==$extra_get_param_val ){
				/* only every couple of hours or so */
				if($current_license_verified <= (time()-$this->verification_interval) && $current_license_status=='valid'){

					/* check_license */
					$edd = $this->edd_plugin_action('check_license', $current_license_key, $edd_name, $edd_url, $plugin_version, $path);

					/* still valid */
					if($edd['status']=='valid'){
						return;
					}else{
						$update_options = $current_options;
						$update_options['license']['verified'] = time();
						$update_options['license']['status'] = $edd['status'];
						$update_options['license']['error'] = $edd['error'];
						/* update with new status */
						update_option($option_name, $update_options);
					}
				}
			$show_license = 1;
			}
		}
	return;
	}

	function edd_for_plugins_markup($option_name = false, $echo = true){

		/* skip if no option name is defined */
		if(empty($option_name)){
				return;
		}

		$option = get_option($option_name);

		/* license key */
		$license = !empty($option['license']['key']) ? $option['license']['key'] : '';
		/* status*/
		$status = !empty($option['license']['status']) ? $option['license']['status'] : '' ;
		/* license last verified */
		$verified = !empty($option['license']['verified']) ? $option['license']['verified'] : 0 ;
		/* error messages */
		$error = !empty($option['license']['error']) ? $option['license']['error'] : '' ;

		/*
			markup
		*/
		$markup='';

		/*
			license key input
		*/
		$markup.="<input name='".$option_name."[license][key]' type='text' placeholder='".__('Enter your License Key', 'wppizza-admin')."' size='30' class='regular-text' value='".$license."' />";
		$markup.=' '.__('License Key', 'wppizza-admin').'<br />';

		/**
			print activate or de-activate button
		**/
		$markup.="<div style='padding:5px;line-height:120%; margin:3px 0'>";
		if( $status =='' || $status != 'valid' ) {
			$markup.="<label class='button-secondary'><input name='".$option_name."[license][activate]' type='checkbox' value='1' /> ".__('Activate License', 'wppizza-admin')."</label>";
		}else{
			$markup.="<label class='button-secondary'><input name='".$option_name."[license][deactivate]' type='checkbox' value='1' /> ".__('De-Activate License', 'wppizza-admin')."</label>";
		}
		/**
			print status info
		**/
		if( $status !='' && empty($error['message']) && $status == 'valid' ) {/* valid and activated */
			$markup.='<span style="color:green;"> '. __('License active', 'wppizza-admin').'</span>';
		}
		if( $status =='' ) {/* not yet known */
			$markup.='<span> '. __('License in-active', 'wppizza-admin').'';
		}
		if( $status !='' && $status !='valid' && $status !='unknown') {/* inactive */
			$markup.='<span style="color:red;"> '. __('License Status', 'wppizza-admin').': '.$status.'</span>';
		}
		if( $status !='' && $status !='valid' && $status =='unknown') {/* inactive */
			$markup.='<span style="color:red;"> '. __('License Status unknown', 'wppizza-admin').'</span>';
		}
		$markup.="</div>";

		if( !empty($error['message'])) {/* print error */
			$evalue = !empty($error['value']) ? ' ['.$error['value'].']' : '' ;
			$markup.='<span style="color:red;"> '. __('Error', 'wppizza-admin').': '.$error['message'].''.$evalue.'</span><br/>';
		}
		$markup.='<br/>'.__('Please note: entering and activating the license is optional, but if you choose not to do so, you will not be informed of any future bugfixes and/or updates.', 'wppizza-admin').'<br />';
		$markup.='<span class="wppizza-highlight">'.__('Please be advised that you will only receive update notifications if they have not been disabled globally (perhaps by using another plugin that manages these kind of notifications)', 'wppizza-admin').'</span><br/>';
		if($license!=''){
			$markup.=''.__('Changing the existing key will automatically de-activate the old key for this site when saving', 'wppizza-admin').'<br />';
		}

		/* default, echo markup if using do_action */
		if($echo){
			echo $markup;
		}
	/* return markup as variable if using apply_filters */
	return $markup;
	}
/********************************************************************************
*
*
*	[EDD for Gateways]
*
*
********************************************************************************/

	/******************************************************************

		[check licence status on option save]

	******************************************************************/
	function gateways_current_screen_edd_check($current_screen){

		/** gateways and options(save) page */
		if(	is_admin() && ( !defined( 'DOING_AJAX' ) || !DOING_AJAX ) && ( $current_screen->id==''.WPPIZZA_SLUG.'_page_gateways' || $current_screen->id=='options' ) ){
			/**check if some edd is defined and apply as required**/
			$this->wppizza_gateways_edd($this->wppizza_registered_gateways);
		}

		/** plugins page - same as above, but let's keep it separate for now. might come in useful */
		if(	is_admin() && ( !defined( 'DOING_AJAX' ) || !DOING_AJAX ) && ( $current_screen->id=='plugins' || $current_screen->id=='plugins-network') ){
			/**check for updates**/
			$this->wppizza_gateways_edd($this->wppizza_registered_gateways);
		}


	}
	/******************************************************************

		[toggle check update licences - admin gateways page only]

	******************************************************************/
	function wppizza_gateways_edd($wppizza_registered_gateways){
		/**loop through gateways, checking if edd is defined**/
		foreach($wppizza_registered_gateways as $gateway_ident=>$wppizza_gateway_object){


			/**check for edd method and filter in gateway**/
			if(method_exists($wppizza_gateway_object, 'gateway_edd_sl') && has_filter('wppizza_gateway_filter_settings_'.$gateway_ident.'')){

				/*
					get edd variables for gateway
				*/
				$gateway_edd_sl = $wppizza_gateway_object->gateway_edd_sl();
				$gatewayEddSl = new WPPIZZA_EDD_SL();
				$gatewayEddSl->gateway_edd_updater($wppizza_gateway_object->gatewayOptions, $gateway_edd_sl['url'], $wppizza_gateway_object->gatewayVersion, $gateway_edd_sl['name'], $gateway_edd_sl['path']);

				/**get status of licence for gateway*/
				$this->gateways_edd_status = $gatewayEddSl->gateway_edd_toggle($wppizza_gateway_object->gatewayOptions, $wppizza_gateway_object->gatewayOptionName, $gateway_edd_sl['name'], $gateway_edd_sl['url']);

				/**add checked licence status filter to update options on save of gateways options**/
				add_filter('wppizza_filter_gateway_options_validate_'.$gateway_ident.'', array( $this, 'gateway_edd_options_validate'));
			}
		}
	}
	/***********************************************
	*
	*	[run filter to add toggled edd status to options on save fo gateway]
	*
	***********************************************/
	function gateway_edd_options_validate($gateway_options){
		/**get validated licence key**/
		$licence_key=$gateway_options['GatewayEDDLicense'];
		/**save as array including state, error etc*/
		$gateway_options['GatewayEDDLicense']=array();
		$gateway_options['GatewayEDDLicense']['licence']	=	$licence_key;
		$gateway_options['GatewayEDDLicense']['status']		=	empty($this->gateways_edd_status['status']) ? '' : $this->gateways_edd_status['status'];
		$gateway_options['GatewayEDDLicense']['error']		=	empty($this->gateways_edd_status['error']) ? '' : $this->gateways_edd_status['error'];
		$gateway_options['GatewayEDDLicense']['timestamp']	=	time();

	return $gateway_options;
	}

	/******************************************************************

		[add filter to settings to be able to add edd inputs on gateway options display ]

	******************************************************************/
	function gateways_add_edd_gateway_filters(){
		/**get registered gateways**/
		$this->wppizza_registered_gateways = WPPIZZA()-> register_gateways -> registered_gateways;
		/*iterate through gateways**/
		foreach($this->wppizza_registered_gateways as $gateway_ident=>$wppizza_gateway_object){
			/**
				check for edd method and add filter to gateway settings page
				
				in future versions,  at some point yet t be determined , we will skip icence input fields in gateways settings themselves
				if gateway has v3.7 edd_init method which will add licence key inputs ONLY to it's own license page instead
				by checking for && !method_exists($wppizza_gateway_object, 'edd_init')
				
				for the moment, we wiil allow input in both - plugin gateways own tabs as well as dedicated/tools->license tabs - places
			**/
			if(method_exists($wppizza_gateway_object, 'gateway_edd_sl')){//&& !method_exists($wppizza_gateway_object, 'edd_init')
				/**add edd input/options**/
				add_filter('wppizza_gateway_filter_settings_'.$gateway_ident.'', array($this, 'gateway_edd_settings'),1,4);
			}
		}
	}

	/***********************************************
	*
	*	[add edd settings fields to gateways]
	*
	***********************************************/
	function gateway_edd_settings($gatewaySettings, $gatewayOptions, $gatewayOptionsName, $type){
		/**current settings array**/
		$gwSettings = !empty($gatewaySettings) ? $gatewaySettings : array();

		/**edd settings global**/
		$gwSettings['_gateway_edd']=array();
		$gwSettings['_gateway_edd']['key']='GatewayEDDLicense';
		$gwSettings['_gateway_edd']['value']=!empty($gatewayOptions['GatewayEDDLicense']) ? $gatewayOptions['GatewayEDDLicense'] : array('licence'=>'', 'status'=>'', 'error'=>'', 'timestamp'=>'');

		/**add more parameters only when not saving defaults (as not needed there)**/
		if($type=='edit'){
			$gwSettings['_gateway_edd']['value']=!empty($gatewayOptions['GatewayEDDLicense']['licence']) ? $gatewayOptions['GatewayEDDLicense']['licence'] : '' ;
			$gwSettings['_gateway_edd']['type'] = 'text';
			$gwSettings['_gateway_edd']['options'] = false;
			$gwSettings['_gateway_edd']['validateCallback'] = 'wppizza_validate_string';
			$gwSettings['_gateway_edd']['label'] = __('License Key','wppizza-admin');
			$gwSettings['_gateway_edd']['descr'] = !empty($gatewayOptions['GatewayEDDLicense']) ? $this->gateway_edd_licence_status($gatewayOptions['GatewayEDDLicense'], $gatewayOptionsName) : '';
			$gwSettings['_gateway_edd']['placeholder'] = __('License Key','wppizza-admin');
			$gwSettings['_gateway_edd']['wpml'] = false;

		}
	return $gwSettings;
	}

	/***********************************************
	*
	*	[output editable licence key input and licence status]
	*
	***********************************************/
	function gateway_edd_licence_status($licence_status, $gateway_options_name){


		$current_license_status= (!empty($licence_status['status']) && $licence_status['status']=='valid') ? array('key'=>'GatewayLicenseDeactivate','lbl'=>__('Deactivate License', 'wppizza-admin'),'colour'=>'green') : array('key'=>'GatewayLicenseActivate','lbl'=>__('Activate License', 'wppizza-admin'),'colour'=>'red');
		/***output relevant input and buttons in  desciption element***/
		$output='';

		$output.="<p>";
		$output.="<label class='button-secondary' style='padding-left:5px;'>";
			$output.="<input name='".WPPIZZA_SLUG."[gateways][".$gateway_options_name."][".$current_license_status['key']."]' type='checkbox' value='1' /> ".$current_license_status['lbl']."";
		$output.="</label>";
		if(!empty($licence_status['status'])){
			$output.='<span style="color:'.$current_license_status['colour'].'"> '.$licence_status['status'].'</span>';
		}
		$output.="</p>";
		$output.="<span class='description' style='display:block;clear:both;padding-top:5px'>".__('Please note: entering and activating the license is optional, but if you choose not to do so, you will not be informed of any future bugfixes and/or updates.', 'wppizza-admin')."</span>";


		return $output;
	}

	/***********************************************
	*
	*	[add edd sl updater class to gateways]
	*
	***********************************************/
	function gateway_edd_updater($gateway_options, $gateway_edd_url, $gateway_edd_version, $gateway_edd_name, $gateway_pluginpath=false, $author='ollybach'){

		/*include class*/
		if( !class_exists( 'WPPIZZA_EDD_SL_UPDATER' ) ) {
			require_once(WPPIZZA_PATH .'classes/shared/wppizza.edd.plugin.updater.latest.php');
		}
		/**fix for old gateways with missing paths parameter**/
		$gateway_pluginpath = $this -> edd_fix_missing_pluginpath($gateway_edd_name, $gateway_pluginpath);

		/*******
		passing along array of options as opposed to single value (as it might still be undefined before saving the first time)
		also check that we are passing the whole options array for legacy / old gateways that only pass on GatewayEDDLicense array.
		although old gateways will still throw a (inconsequential) phpnotice before first save
		********/
		/*retrieve our license key from the DB*/
		$license_key=empty($gateway_options['GatewayEDDLicense']['licence']) ? '' : $gateway_options['GatewayEDDLicense']['licence'];

		/* setup the updater */
		$edd_updater = new WPPIZZA_EDD_SL_UPDATER( $gateway_edd_url, $gateway_pluginpath , array(
			'version'		=> $gateway_edd_version, 		// current version number
			'license'		=> $license_key, 	// license key (used get_option above to retrieve from DB)
			'item_name'		=> $gateway_edd_name, 	// name of this plugin
			'author'		=> $author,  // author of this plugin
			'url'           => home_url(),//added
			'plugin'        => $gateway_pluginpath //added to eliminate php notice on admin plugins screen if missing
			)
		);
	}

	/***********************************************
	*
	*	[toogle edd activation in gateways]
	*
	***********************************************/
	function gateway_edd_toggle($gateway_options, $gateway_options_name, $gateway_edd_name, $gateway_edd_url){//$this->gatewayOptions,$this->gatewayOptionsName
		global $pagenow;


		/*********update and (de)-activate license when saving*******/
		if($pagenow=='options.php' && isset($_POST[''.WPPIZZA_SLUG.'_gateways']) ){

			$licenseCurrent=!empty($gateway_options['GatewayEDDLicense']['licence']) ? $gateway_options['GatewayEDDLicense']['licence'] : '' ;/*current license number**/
			$licenseNew=trim(wppizza_validate_string($_POST[WPPIZZA_SLUG]['gateways'][$gateway_options_name]['GatewayEDDLicense']));/*posted license number**/


			/**defaults/previously set vars,  if no action taken**/
			$edd['error']=false;
			$edd['status']=!empty($gateway_options['GatewayEDDLicense']['status']) ? $gateway_options['GatewayEDDLicense']['status'] : '';/*current license status**/

			/***deactivate currently set license first, if it was not '' anyway and new one is different***/
			if($licenseCurrent!='' && $licenseNew!=$licenseCurrent && $edd['status']=='valid'){
				$edd=$this->edd_action('deactivate_license', $licenseCurrent, $gateway_edd_name, $gateway_edd_url);
			}


			/***if new different license has been set and we had no error (otherwise we'll just keep the original settings and desplay error****/
			if($licenseNew!='' && !$edd['error']){

				/**its a new key, so lets reset  the status***/
				if($licenseNew!=$licenseCurrent){
					$edd['status']='';
				}
				/**if we are activating**/
				if(isset($_POST[WPPIZZA_SLUG]['gateways'][$gateway_options_name]['GatewayLicenseActivate'])){
						$edd=$this->edd_action('activate_license',$licenseNew,$gateway_edd_name,$gateway_edd_url);
				}
				/**if we are de-activating**/
				if(isset($_POST[WPPIZZA_SLUG]['gateways'][$gateway_options_name]['GatewayLicenseDeactivate'])){
					$edd=$this->edd_action('deactivate_license',$licenseNew,$gateway_edd_name,$gateway_edd_url);
				}
			}





		/*if there was an error, keep old license and display error message else set new licence*/
		$edd['license']= (!$edd['error']) ? $licenseNew : $licenseCurrent;

		return $edd;
		}

	return;
	}

	function edd_fix_missing_pluginpath($gateway_edd_name, $gatewaypluginpath){
		/*****************
			fix pluginpath variable for EDD used in some gateways -
			WILL BE OBSOLETE ONCE ALL AFFECTED GATEWAYS HAVE BEEN UPDATED TO HAVE THIS PARAMETER
			(should be passed as __FILE__ in gateways)
		*******************/
		if(!$gatewaypluginpath){
			$gatewaypluginpath=__FILE__;//ini var (although __FILE__ is actually wrong here )

			/*make lowercase*/
			$gwname=strtolower($gateway_edd_name);

			if (strpos($gwname,'paypal') !== false) {
				$gatewaypluginpath='wppizza-gateway-paypal/wppizza-gateway-paypal.php';
			}
			if (strpos($gwname,'stripe') !== false) {
				$gatewaypluginpath='wppizza-gateway-stripe/wppizza-gateway-stripe.php';
			}
			if (strpos($gwname,'2checkout') !== false) {
				$gatewaypluginpath='wppizza-gateway-2checkout/wppizza-gateway-2checkout.php';
			}
			if (strpos($gwname,'authorize') !== false) {
				$gatewaypluginpath='wppizza-gateway-authorize.net/wppizza-gateway-authorize.net.php';
			}
			if (strpos($gwname,'cardsave') !== false) {
				$gatewaypluginpath='wppizza-gateway-cardsave/wppizza-gateway-cardsave.php';
			}
			if (strpos($gwname,'epay') !== false) {
				$gatewaypluginpath='wppizza-gateway-epay.dk/wppizza-gateway-epay.dk.php';
			}
			if (strpos($gwname,'epdq') !== false) {
				$gatewaypluginpath='wppizza-gateway-epdq/wppizza-gateway-epdq.php';
			}
			if (strpos($gwname,'realex') !== false) {
				$gatewaypluginpath='wppizza-gateway-realex/wppizza-gateway-realex.php';
			}
			if (strpos($gwname,'saferpay') !== false) {
				$gatewaypluginpath='wppizza-gateway-saferpay/wppizza-gateway-saferpay.php';
			}
			if (strpos($gwname,'sagepay') !== false) {
				$gatewaypluginpath='wppizza-gateway-sagepay/wppizza-gateway-sagepay.php';
			}
			if (strpos($gwname,'sisow') !== false) {
				$gatewaypluginpath='wppizza-gateway-sisow/wppizza-gateway-sisow.php';
			}
			if (strpos($gwname,'sofort') !== false) {
				$gatewaypluginpath='wppizza-gateway-stripe/wppizza-gateway-stripe.php';
			}
			if (strpos($gwname,'payeezy') !== false) {/*only applicable to payeezu v1.0*/
				$gatewaypluginpath='wppizza-gateway-payeezye/wppizza-gateway-payeezy.php';
			}
		}

		return $gatewaypluginpath;
	}
	/********************************************************************************
	*
	*	[EDD plugin helpers]
	*
	********************************************************************************/
	/*
		setup the updater
	*/
	function edd_load_plugin_updater($license, $edd_name, $edd_url, $plugin_version, $path, $author='ollybach'){
		/*include class*/
		if( !class_exists( 'WPPIZZA_EDD_SL_UPDATER' ) ) {
			require_once(WPPIZZA_PATH .'classes/shared/wppizza.edd.plugin.updater.latest.php');
		}
		/* setup the updater */
		$edd_updater = new WPPIZZA_EDD_SL_UPDATER( $edd_url, $path , array(
			'version'		=> $plugin_version, // current version number $plugin_version
			'license'		=> $license, 	// license key $license
			'item_name'		=> $edd_name, 	// name of this plugin
			'author'		=> $author,  // author of this plugin
			'url'           => home_url(),//added
			'plugin'        => basename(dirname($path)).'/'.basename($path) //added to eliminate php notice on admin plugins screen if missing
			)
		);
	return $edd_updater;
	}
	/*
		edd actions
	*/
	function edd_plugin_action($action, $license, $edd_name, $edd_url, $plugin_version, $path){
		/*
			set edd api action
		*/
		$api_params = array(
			'edd_action'=> $action,
			'license' 	=> $license,
			'item_name' => urlencode( $edd_name ) // the name of our product in EDD
		);

		/*
			Call the custom API.
		*/
		$response = wp_remote_get( add_query_arg( $api_params, $edd_url ), array( 'timeout' => 15, 'sslverify' => false ) );



		/*
			make sure the response came back okay
		*/
		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
			$err_message = $response->get_error_message();
			$edd['error']['message'] =  ( is_wp_error( $response ) && ! empty( $err_message ) ) ? 'WP Error: ' . $err_message : '['.wp_remote_retrieve_response_code( $response ).'] '.__( 'Response Error: An error occurred, please try again.' );
			$edd['error']['value'] = false;
			$edd['status'] = 'unknown';
		}else{
			$edd['error']['message'] = '';
			$edd['error']['value'] = '';

			// decode the license data
			$license_data = json_decode( wp_remote_retrieve_body( $response ) );

			if ( false === $license_data->success ) {

				switch( $license_data->error ) {

					case 'expired' :

						$message = sprintf(
							__( 'Your license key expired on %s.' ),
							date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
						);
						break;

					case 'revoked' :

						$message = __( 'Your license key has been disabled.' );
						break;

					case 'missing' :

						$message = __( 'Invalid license.' );
						break;

					case 'invalid' :

					case 'site_inactive' :

						$message = __( 'Your license is not active for this URL.' );
						break;

					case 'item_name_mismatch' :

						$message = sprintf( __( 'This appears to be an invalid license key for %s.' ), $edd_name );
						break;

					case 'no_activations_left':

						$message = __( 'Your license key has reached its activation limit.' );
						break;

					case 'license_not_activable':

						$message = __( 'You cannot activate a bundle license.' );
						break;

					default :
						//$license_data->license = 'unknown';
						$message = __( 'An unspecified error occurred, please try again.' );
						break;
				}
			$edd['error']['message'] = $message;
			$edd['error']['value'] = $license_data->error;
			}
		/* capture status */
		$edd['status']=wppizza_validate_string($license_data->license);
		}

	return $edd;
	}


	/********************************************************************************
	*
	*	[EDD gateways helpers]
	*
	********************************************************************************/
	function edd_action($action, $license, $eddName, $eddUrl){
		$api_params = array(
			'edd_action'=> $action,
			'license' 	=> $license,
			'item_name' => urlencode( $eddName ) // the name of our product in EDD
		);

		// Call the custom API.
		$response = wp_remote_get( add_query_arg( $api_params, $eddUrl ), array( 'timeout' => 15, 'sslverify' => false ) );

		// make sure the response came back okay
		$edd['error']=false;
		if ( is_wp_error( $response ) ){
			$edd['error']=true;
		}else{
			// decode the license data
			$license_data = json_decode( wp_remote_retrieve_body( $response ) );
			$edd['status']=wppizza_validate_string($license_data->license);
		}

	return $edd;
	}
}
$WPPIZZA_EDD_SL=new WPPIZZA_EDD_SL();
?>