<?php
/**
* WPPIZZA_MODULE_TOOLS_LICENCES_INIT Class
*
* @package     WPPIZZA
* @subpackage  WPPIZZA_MODULE_TOOLS_LICENCES_INIT
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
class WPPIZZA_MODULE_TOOLS_LICENCES_INIT{

	/*
		which admin subpage (identified there by this->class_key) are we adding this to
		@param str
	*/
	private $settings_page = 'tools';

	/*
		must be unique within this admin page
		@param str - _GET['tab'] value
		@param str - (sub) section of _GET['tab'] page (there's only this single section here , but might get added to in the future if required)
	*/
	private $tab_key = 'licenses';
	private $section_key = 'licenses_list';

	/*
		bool flag to see if there are any edd enabled plugins/gateways that need to be
		verified and/or have licenses and license keys displayed etc
		@param bool
	*/
	private $has_registered_edd_plugins = false ;

	/*
		array (might be empty) of edd enabled plugins/gateways
		@param array
	*/
	private $edd_registered_plugins = false ;

	/*
		time - in seconds - when we should recheck since the last verification (10800 / 3 hours) set to 0 for testing
		@param int
	*/
	private $edd_verification_interval = 10800;//10800;

	/*
		array of options for edd enabled plugins
		@param array
	*/
	private $edd_plugin_updated = array();


	function __construct() {
		/**********************************************************
			[add settings to admin]
		***********************************************************/
		if(is_admin()){
			/* check if any plugins/gateways have edd licenses registered to add labels and tabs */
			add_action('init', array($this, 'has_edd_registered_plugins'), 5 );

			/*** alter submenu label link***/
			add_filter('wppizza_filter_admin_label_link_label_'.$this->settings_page.'', array($this, 'admin_link_label'), 10);

			/*** add to a specific tab ***/
			add_filter('wppizza_filter_admin_tabs_'.$this->settings_page.'', array($this, 'admin_tabs'), 10);

			/* add admin options settings page*/
			add_filter('wppizza_filter_settings_sections_'.$this->settings_page.'', array($this, 'admin_options_settings'), 10, 5);

			/* add admin options settings page fields */
			add_action('wppizza_admin_settings_section_fields_'.$this->settings_page.'', array($this, 'admin_options_fields_settings'), 10, 5);

			/**additional text header for this tab*/
			add_action('wppizza_settings_sections_header_'.$this->settings_page.'', array( $this, 'sections_header'), 10, 2 );

			/*execute some helper functions once to use their return multiple times */
			add_action('current_screen', array( $this, 'wppizza_add_helpers') );

			/** admin ajax **/
			add_action('wppizza_ajax_admin_'.$this->settings_page.'', array( $this, 'admin_ajax'));

			/** verify license status on load of tab**/
			add_action('current_screen', array($this, 'edd_verify'));

			/** echo license inputs**/
			add_action('wppizza_edd_init', array($this, 'edd_echo'), 10, 2);

		}
	}

	/*******************************************************************************************************************************************************
	* 	[check if there are any plugins or gateways that have edd licenses defined]
	*	[must run early as otherwise we will not be able to filter the link labels etc ]
	*	@param void
	*	@return void
	*******************************************************************************************************************************************************/
	function has_edd_registered_plugins(){
		/*
			checking if there is any edd enabled plugin/extension
		*/
		$registered_plugins = apply_filters('wppizza_edd_licenses', array());
		if(!empty($registered_plugins)){
			$this -> has_registered_edd_plugins = true;
			return;
		}
		/*
			checking if there is any edd enabled gateway
		*/
		$registered_gateways = apply_filters('wppizza_register_gateways', array());
		foreach($registered_gateways as $gateway_class){
			if (is_callable(array($gateway_class, 'edd_init'))){
				$this -> has_registered_edd_plugins = true;
			return;
			}
		}
	}

	/*******************************************************************************************************************************************************
	* 	[get all plugins/gateways that have edd licenses registered including their current options set
	*	called just before verifying and displaying licenses inputs - get once only}
	*	@param void
	*	@return void
	*******************************************************************************************************************************************************/
	function get_edd_registered_plugins(){
		static $edd_registered_plugins = null;

		/*
			simply return empty array if we already know there are none
		*/
		if(empty($this -> has_registered_edd_plugins)){
			return array();
		}

		if($edd_registered_plugins === null){
			/*
				ini as empty array
			*/
			$edd_registered_plugins = array();

			/*
				get plugins/extensions with defined edd licenses
				registered by filter.
			*/
			$registered_plugins = apply_filters('wppizza_edd_licenses', array());


			foreach($registered_plugins as $slug => $registered_plugin){
				$edd_registered_plugins[$slug] = $registered_plugin;
			}

			/*
				get gateways registered by filter with defined edd_init method
			*/
			$registered_gateways = apply_filters('wppizza_register_gateways', array());
			foreach($registered_gateways as $gateway_class){
				if (is_callable(array($gateway_class, 'edd_init'))){
					$init_class = new $gateway_class();
					$edd_registered_plugins[$gateway_class] = $init_class->edd_init();
					$edd_registered_plugins[$gateway_class]['is_gateway'] = true;
				}
			}
		}
	return $edd_registered_plugins;
	}
	/*******************************************************************************************************************************************************
	*
	* 	[verify license when loading license tab]
	*
	********************************************************************************************************************************************************/
	function edd_verify($current_screen){

		if(	is_admin() &&
			( !defined('DOING_AJAX') || !DOING_AJAX ) &&
			(
				(
					$current_screen->id==''.WPPIZZA_SLUG.'_page_tools' &&  !empty($_GET['tab']) && 'licenses' == $_GET['tab']
				)
			)
		){//|| $current_screen->id=='options'


			/*
				plugins with edd defined licenses
			*/
			//$defined_licenses = apply_filters('wppizza_edd_licenses', array());
			$defined_licenses = $this->get_edd_registered_plugins();

			/*
				loop through plugins/gateways with defined licenses
				updating options as required
			*/
			foreach($defined_licenses as $plugin_slug => $plugin_data){

				/*
					force lowercase
				*/
				$plugin_slug = strtolower($plugin_slug);

				/*
					for historical reasons, gateways use GatewayEDDLicense as key
					, "licence" (with a c) instead of key
					, "timestamp" instead of verified
				*/
				$is_gateway_license = !empty($plugin_data['is_gateway']) ? true : false;


				/* standard extensions (non gateway) plugins */
				if(!$is_gateway_license){
					/*
						currently set license key, if any
					*/
					$license_data['key'] = isset($plugin_data['options']['license']['key']) ? $plugin_data['options']['license']['key'] : false ;
					/*
						currently set license status, if any
					*/
					$license_data['status'] = isset($plugin_data['options']['license']['status']) ? $plugin_data['options']['license']['status'] : false ;
					/*
						currently set expiry
					*/
					$license_data['expires'] = isset($plugin_data['options']['license']['expires']) ? $plugin_data['options']['license']['expires'] : false ;
					/*
						currently set time last time license was verified, else 0
					*/
					$license_data['verified'] = !empty($plugin_data['options']['license']['verified']) ? $plugin_data['options']['license']['verified'] : 0 ;
					/*
						currently set error last time license was verified, else 0
					*/
					$license_data['error'] = !empty($plugin_data['options']['license']['error']) ? $plugin_data['options']['license']['error'] : array();
				}else{


					/*
						currently set license key, if any
					*/
					$license_data['key'] = isset($plugin_data['options']['GatewayEDDLicense']['licence']) ? $plugin_data['options']['GatewayEDDLicense']['licence'] : false ;
					/*
						currently set license status, if any
					*/
					$license_data['status'] = isset($plugin_data['options']['GatewayEDDLicense']['status']) ? $plugin_data['options']['GatewayEDDLicense']['status'] : false ;
					/*
						currently set license status, if any
					*/
					$license_data['expires'] = isset($plugin_data['options']['GatewayEDDLicense']['expires']) ? $plugin_data['options']['GatewayEDDLicense']['expires'] : false ;
					/*
						currently set time last time license was verified, else 0
					*/
					$license_data['verified'] = !empty($plugin_data['options']['GatewayEDDLicense']['timestamp']) ? $plugin_data['options']['GatewayEDDLicense']['timestamp'] : 0 ;
					/*
						currently set error last time license was verified, else 0
					*/
					$license_data['error'] = !empty($plugin_data['options']['GatewayEDDLicense']['error']) ? $plugin_data['options']['GatewayEDDLicense']['error'] : array();

				}


				/*
					edd data (version , name , url, path etc)
				*/
				$edd_data = $plugin_data['edd_data'];

				/*
					flag to decide if we should update options
					for this plugin. ini as false
				*/
				$do_option_update = false;

				/*
					init updater
				*/
				//$edd_sl = new WPPIZZA_EDD_SL();
				$this->edd_init_updater($license_data['key'], $edd_data);

				/*
					check_license and save details in options
					if already recently verified, get those details
					if no license key entered yet, set default
				*/
				$current_license =array();

				/*
					only do any of this
					if a license key was actually set
				*/
				if($license_data['key'] !== false){

					/*
						skip license check if license key was checked in the last couple of hours or so
						and was valid at the time
					*/
					if($license_data['verified'] <= (time()-$this->edd_verification_interval) ){//&& $license_data['status']=='valid'

						/*
							check license
						*/
						$args = array();
						$args['action'] = 'check_license';
						$args['license'] = $license_data['key'];
						$args['edd']['name'] = $edd_data['name'];
						$args['edd']['url'] = $edd_data['url'];
						$edd_license = $this->edd_action($args);

						/*
							still valid
						*/
						if($edd_license['status']=='valid'){

							/*
								set flag to
								*update* current license status
							*/
							$do_option_update = true;
							/*
								set updated license options
							*/
							$current_license['key'] = $license_data['key'];// as set
							$current_license['status'] = $edd_license['status'];
							$current_license['expires'] = $edd_license['license_data']->expires;
							$current_license['verified'] = time();//update timestamp
							$current_license['error'] = $license_data['error'];// as set
						}

						/*
							invalid
						*/
						if($edd_license['status']!='valid'){

							/*
								set flag to
								*update* license status
							*/
							$do_option_update = true;
							/*
								set updated license options
							*/
							$current_license['key'] = $license_data['key'];// as set
							$current_license['status'] = $edd_license['status'];
							$current_license['expires'] = !empty($edd_license['license_data']->expires) ? $edd_license['license_data']->expires : 0;
							$current_license['verified'] = time();
							$current_license['error'] = !empty($edd_license['error']) ?  $edd_license['error'] : false ;

						}
					}
				}


				/*
					no license key set yet
					set flag to update option,
					inserting status and error etc
				*/
				if($license_data['key'] === false){

					/*
						set flag to *add* current license status
						to plugin options array
					*/
					$do_option_update = true;

					/*
						set license options
					*/
					//$current_license['activate'] = false;	// i dont think we need this
					//$current_license['deactivate'] = false; //i dont think we need this
					$current_license['key'] = false;
					$current_license['verified'] = time();
					$current_license['status'] = '';
					$current_license['expires'] = false;
					$current_license['error'] = false;
				}

				/*******************************************************
				#	[update option]
				#	if re-verified outside 2 hour window
				#	or if not ever verified previously
				*******************************************************/
				if($do_option_update){
					$update_options = $plugin_data['options'];
				 	/* extension licenses */
				 	if(!$is_gateway_license){
						$update_options['license'] = $current_license;
				 	}
				 	/* gateway licenses, setting appropriate keys as necessary */
				 	if($is_gateway_license){
				 		$update_options['GatewayEDDLicense'] = array();
				 		$update_options['GatewayEDDLicense']['licence'] = $current_license['key'];
				 		$update_options['GatewayEDDLicense']['status'] = $current_license['status'];
				 		$update_options['GatewayEDDLicense']['expires'] = $current_license['expires'];
				 		$update_options['GatewayEDDLicense']['timestamp'] = $current_license['verified'];
				 		$update_options['GatewayEDDLicense']['error'] = $current_license['error'];
				 	}

					/*
						make sure we reflect this possibly changed status
						in the page body (edd_echo) too
					*/
					$this->edd_plugin_updated[$plugin_slug] = $update_options;

					/*
						update/adding license status, errors etc etc
					*/
					update_option($plugin_slug, $update_options);
				}
			}
		}

		/** plugins page - same as above, but let's keep it separate for now. might come in useful */
		//if(	is_admin() && ( !defined( 'DOING_AJAX' ) || !DOING_AJAX ) && ( $current_screen->id=='plugins' || $current_screen->id=='plugins-network') ){
				/**check for updates**/
				//$this->wppizza_gateways_edd($this->wppizza_registered_gateways);
		//}
	}

	/*******************************************************************************************************************************************************
	*
	* 	[echo license inputs]
	*
	********************************************************************************************************************************************************/
	function edd_echo($plugin_slug, $plugin_options){
		/* cast to lowercase to match option names that are always stored lowercase*/
		$lowercase_plugin_slug = strtolower($plugin_slug);

		/*
			when we have just checked the license on page load
			the status might have changed so we immediately want to reflect this in the ouput here
			as otherwise this would only be shown when reloading the page
		*/
		if(!empty($this->edd_plugin_updated[$lowercase_plugin_slug])){
			$plugin_options['options'] = $this->edd_plugin_updated[$lowercase_plugin_slug];
		}

		/*
			for historical reasons, gateways use GatewayEDDLicense as key
			, "licence" (with a c) instead of key
			, "timestamp" instead of verified
		*/
		$is_gateway_license = !empty($plugin_options['is_gateway']) ? true : false;


		/*
			get current license key etc , if any
		*/
		/* non-gateways */
		if(!$is_gateway_license){
			/* license key*/
			$license = !empty($plugin_options['options']['license']['key']) ? $plugin_options['options']['license']['key'] : '';
			/* status*/
			$status = !empty($plugin_options['options']['license']['status']) ? $plugin_options['options']['license']['status'] : '' ;
			/* expires*/
			$expires = !empty($plugin_options['options']['license']['expires']) ? $plugin_options['options']['license']['expires'] : '' ;
			/* license last verified */
			$verified = !empty($plugin_options['options']['license']['verified']) ? $plugin_options['options']['license']['verified'] : 0 ;
			/* error messages */
			$error = !empty($plugin_options['options']['license']['error']) ? $plugin_options['options']['license']['error'] : '' ;
		}

		/* gateways */
		if($is_gateway_license){
			/* license key*/
			$license = !empty($plugin_options['options']['GatewayEDDLicense']['licence']) ? $plugin_options['options']['GatewayEDDLicense']['licence'] : '';
			/* status*/
			$status = !empty($plugin_options['options']['GatewayEDDLicense']['status']) ? $plugin_options['options']['GatewayEDDLicense']['status'] : '' ;
			/* status*/
			$expires = !empty($plugin_options['options']['GatewayEDDLicense']['expires']) ? $plugin_options['options']['GatewayEDDLicense']['expires'] : '' ;
			/* license last verified */
			$verified = !empty($plugin_options['options']['GatewayEDDLicense']['timestamp']) ? $plugin_options['options']['GatewayEDDLicense']['timestamp'] : 0 ;
			/* error messages */
			$error = !empty($plugin_options['options']['GatewayEDDLicense']['error']) ? $plugin_options['options']['GatewayEDDLicense']['error'] : '' ;
		}

		/*******************************
			markup license key inputs
		*******************************/
		$markup='';

		/*
			wrapper. must be "form" element
			for js serialization
		*/
		$markup.="<div id='".WPPIZZA_SLUG."_license_".$plugin_slug."' class='".WPPIZZA_SLUG."_license'>";

		/*
			license key input
		*/
		$markup.="<input id='".WPPIZZA_SLUG."_license_key_".$plugin_slug."' class='".WPPIZZA_SLUG."_license_key' name='".WPPIZZA_SLUG."_license_key' type='text' placeholder='".__('Enter your Licence Key')."' size='35' class='regular-text' value='".$license."' />";
		$markup.=' '.__('Licence Key', 'wppizza-admin').'<br />';

		/**
			print activate or de-activate button
		**/
		$markup.="<div id='".WPPIZZA_SLUG."_license_info_".$plugin_slug."' class='".WPPIZZA_SLUG."_license_info' style='padding:5px;line-height:120%; margin:3px 0'>";

			/*
				set action and label
			*/
			$action_class = ( $status =='' || $status != 'valid' ) ? ''.WPPIZZA_SLUG.'_license_activate' : ''.WPPIZZA_SLUG.'_license_deactivate';
			$action_label = ( $status =='' || $status != 'valid' ) ? __('Activate Licence', 'wppizza-admin') : __('De-Activate Licence', 'wppizza-admin');
			$show_remove = ( $status =='' || $status != 'valid' ) ? false : true;

			/*
				action buttons
			*/
			$markup.="<label class='".WPPIZZA_SLUG."_license_action'>";
				$markup.="<input class='button-secondary ".$action_class."' type='button' value='".$action_label."' />";
				$markup.="<input name='".WPPIZZA_SLUG."_license_edd' type='hidden' value='".maybe_serialize($plugin_options['edd_data']['method'])."' />";
				$markup.="<input name='".WPPIZZA_SLUG."_license_current' type='hidden' value='".$license."' />";
			$markup.="</label>";

			/**
				print status info
			**/
			$markup.="<span class='".WPPIZZA_SLUG."_license_status'>";
				$markup.= $this->edd_status_markup($status, $error, $expires, $show_remove);
			$markup.="</span>";

		$markup.="</div>";


		/*
			add nonce
		*/
		$markup .= ''.wp_nonce_field( '' . WPPIZZA_SLUG . '_license_nonce','' . WPPIZZA_SLUG . '_license_nonce',true,false).'';


		$markup.='</div>';


		/* print markup */
		print $markup;

	return;
	}

	/*******************************************************************************************************************************************************
	*
	* 	[license status output]
	*
	********************************************************************************************************************************************************/
	function edd_status_markup($status, $error, $expires, $show_remove){
		$markup = '';

		$expiry_time = !empty($expires) ?  (strtotime($expires)+1) : false;//+1 to make it 0:00 next day instead of 23:59:59

		if( $status =='' ) {/* not yet known or key deleted*/
			$markup.='<span> '. __('Licence in-active', 'wppizza-admin').'';
		}
		if( $status !='' && empty($error['message']) && $status == 'valid' ) {/* valid and activated */
			$markup.='<span style="color:green;"> '. __('Licence active', 'wppizza-admin').'</span>';
		}
		if( $status !='' && $status !='valid' && $status !='unknown') {/* inactive */
			$markup.='<span style="color:red;"> '. __('Licence Status', 'wppizza-admin').': '.$status.'</span>';
		}
		if( $status !='' && $status !='valid' && $status =='unknown') {/* inactive */
			$markup.='<span style="color:red;"> '. __('Licence Status unknown', 'wppizza-admin').'</span>';
		}

		if( !empty($error['message'])) {/* print error */
			$evalue = !empty($error['value']) ? ' ['.$error['value'].']' : '' ;
			$markup.='<span style="color:red;"> '. __('Error', 'wppizza-admin').': '.$error['message'].''.$evalue.'</span><br/>';
		}

		/* remove licence completely checkbox */
		if(!empty($show_remove)){
			$markup.="<br/><label style='font-size:80%'><input type='checkbox' name='".WPPIZZA_SLUG."_license_remove' value='1' /> ".__('Empty licence key field on de-activation', 'wppizza-admin')."</label>" ;
		}

		if(!empty($expiry_time)){
			if($status == 'valid' && $expiry_time > time()){
				$markup.='<br/><span style="font-size:90%">'.sprintf(__('This licence key expires on %s', 'wppizza-admin'),date('l d F, Y', $expiry_time)).'</span>';
			}
			if(in_array($status, array('expired', 'invalid')) && $expiry_time <= time()){
				$markup.='<br/><span style="font-size:90%;color:red;">'.sprintf(__('This licence key expired on %s', 'wppizza-admin'),date('l d F, Y', $expiry_time)).'</span>';
			}
		}

	return $markup ;
	}
	/*******************************************************************************************************************************************************
	*
	* 	[activate/deactivate etc]
	*
	********************************************************************************************************************************************************/
	function edd_action($edd_data){


			/*
				Call the edd API.
			*/
			$api_params = array(
				'edd_action'=> $edd_data['action'],
				'license' 	=> $edd_data['license'],
				'item_name' => urlencode( $edd_data['edd']['name'] ) // the name of our product in EDD
			);
			$response = wp_remote_get( add_query_arg( $api_params, $edd_data['edd']['url'] ), array( 'timeout' => 15, 'sslverify' => false ) );


			/*
				ini array to add in this filter
			*/
			$result = array();

			// make sure the response came back okay
			if ( is_wp_error( $response ) ){
				/*  error  */
				$result['error']=true;
			}else{
				// decode the license data
				$license_data = json_decode( wp_remote_retrieve_body( $response ) );

				/* add results to return */
				$result['success']=true;
				$result['status'] = wppizza_validate_string($license_data->license);
				$result['license_data'] = $license_data;
			}

	return $result;
	}

	/***********************************************
	*
	*	[add edd sl updater class]
	*
	***********************************************/
	function edd_init_updater($license_key, $plugin_data, $author='ollybach'){

		/*include class*/
		if( !class_exists( 'WPPIZZA_EDD_SL_UPDATER' ) ) {
			require_once(WPPIZZA_PATH .'classes/shared/wppizza.edd.plugin.updater.latest.php');
		}
		/* setup the updater */
		$edd_updater = new WPPIZZA_EDD_SL_UPDATER( $plugin_data['url'], $plugin_data['path'] , array(
			'version'		=> $plugin_data['version'],// current version number
			'license'		=> $license_key, 	// license key (used get_option above to retrieve from DB)
			'item_name'		=> $plugin_data['name'], 	// name of this plugin
			'author'		=> $author,  // author of this plugin
			'url'           => home_url(),//added
			'plugin'        => $plugin_data['path'] //added to eliminate php notice on admin plugins screen if missing
			)
		);
	}

	/*******************************************************************************************************************************************************
	*
	* 	[admin ajax]
	*
	********************************************************************************************************************************************************/
	function admin_ajax($wppizza_options){


		/*****************************************************
			[(de)activating license]
		*****************************************************/
		if($_POST['vars']['field']=='license_action'){

		/*
			get posted data and parse
		*/
		$posted_data = array();
		parse_str($_POST['vars']['data'], $posted_data);


		/*
			ini return object
		*/
		$edd_data = array();

		/*
			get entered license key
		*/
		$license_key = !empty($posted_data['' . WPPIZZA_SLUG . '_license_key']) ? sanitize_text_field($posted_data['' . WPPIZZA_SLUG . '_license_key']) : false;


		/*
			get current license key
			(as it might differ from the one entered/changed now)

		*/
		$license_key_current = !empty($posted_data['' . WPPIZZA_SLUG . '_license_current']) ? sanitize_text_field($posted_data['' . WPPIZZA_SLUG . '_license_current']) : false;

		/*
			[verify nonce]
		*/
		if (!wp_verify_nonce(  sanitize_text_field($posted_data['' . WPPIZZA_SLUG . '_license_nonce']) , '' . WPPIZZA_SLUG . '_license_nonce' ) ) {
			/* invalid nonce */
			$edd_data['error'] = __('Invalid Nonce', 'wppizza-admin');
			print"".json_encode($edd_data)."";
			exit();
		}

		/*
			[verify that there is actually a license entered]
		*/
		if (empty($license_key)){
			/* no key entered */
			$edd_data['error'] = __('No Licence Key entered', 'wppizza-admin');
			print"".json_encode($edd_data)."";
			exit();
		}

		/*
			get all wppizza edd registered plugins
		*/
		$edd_registered_plugins = $this->get_edd_registered_plugins();

		/*
			get the edd data and options
			for this plugin
		*/
		$get_class_method = explode('::',$posted_data['' . WPPIZZA_SLUG . '_license_edd']);
		$class = $get_class_method[0];
		$method = $get_class_method[1];
		/* ini plugin class and get edd values */
		$plugin = new $class();
		$edd_init = $plugin->$method(array());
		/* add flag to identify we are updating a gateway license */
		$edd_init['is_gateway'] = !empty($edd_registered_plugins[$class]['is_gateway']) ? true : false ;


		/*
			option name , by key if non gateway
			and traversing into this array to get the edd data
		*/
		if(!$edd_init['is_gateway']){
			$option_name = $edd_init[key($edd_init)]['edd_data']['option_name'];
			$edd_init['edd_data'] = $edd_init[key($edd_init)]['edd_data'];
			$edd_init['options'] = $edd_init[key($edd_init)]['options'];
		}else{
			$option_name = $edd_init['edd_data']['option_name'];
		}


		/*
			[data]
		*/
		$edd_data['license'] = $license_key;
		$edd_data['action'] = (sanitize_text_field($_POST['vars']['action']) == 'activate') ? 'activate_license' : 'deactivate_license';

		/*
			[de_activate old license if any and different from entered]
		*/
		if (!empty($license_key_current) && $license_key != $license_key_current){
			$api_args = array();
			$api_args['action'] = 'deactivate_license';
			$api_args['license'] = $license_key_current;
			$api_args['edd']['name'] = $edd_init['edd_data']['name'];
			$api_args['edd']['url'] = $edd_init['edd_data']['url'];
			$edd_data['api_results_deactivate_old'] = $this->edd_action($api_args);
		}

		/*
			activate/deactivate new/entered license
		*/
		$api_args = array();
		$api_args['action'] = $edd_data['action'];
		$api_args['license'] = $license_key;
		$api_args['edd']['name'] = $edd_init['edd_data']['name'];
		$api_args['edd']['url'] = $edd_init['edd_data']['url'];
		$edd_data['api_results'] = $this->edd_action($api_args);


		/*
			update options if success
			does not mean that the license is valid
			only that the api call succeeded
		*/
		if(!empty($edd_data['api_results']['success'])){
			/*
				get current options
			*/
			$update_options = $edd_init['options'];

			/*
				any errors ?
			*/
			$api_call_errors = !empty($edd_data['api_results']['license_data']->error) ? $edd_data['api_results']['license_data']->error : '';


			/*
				are we removing the license key entirely when de-activating ?
				 - provided we have no errors
			*/
			$remove_license = false;
			if($edd_data['action'] == 'deactivate_license' && !empty($posted_data['' . WPPIZZA_SLUG . '_license_remove']) && empty($api_call_errors)){
				$remove_license = true;
			}
			/*
				do we need to show the remove licence value checkbox (after activation)
			*/
			$show_remove = ($edd_data['action'] == 'activate_license') ? true : false;


			/*
				is gateway
			*/
			if(!empty($edd_init['is_gateway'])){
				/* overwrite previous license options */
				$update_options['GatewayEDDLicense'] = array();
				$update_options['GatewayEDDLicense']['licence'] = ($remove_license) ? '' : $license_key ;
				$update_options['GatewayEDDLicense']['status'] = ($remove_license) ? '' : $edd_data['api_results']['status'];
				$update_options['GatewayEDDLicense']['expires'] = ($remove_license) ? 0 : $edd_data['api_results']['license_data']->expires;
				$update_options['GatewayEDDLicense']['timestamp'] = time();
				$update_options['GatewayEDDLicense']['error'] = $api_call_errors ;

				/* update/adding license status, errors etc etc */
				update_option($option_name, $update_options);
			}else{
				/* overwrite previous license options */
				$update_options['license'] = array();
				$update_options['license']['key'] = ($remove_license) ? '' : $license_key ;
				$update_options['license']['status'] = ($remove_license) ? '' : $edd_data['api_results']['status'];
				$update_options['license']['expires'] = ($remove_license) ? 0 : $edd_data['api_results']['license_data']->expires;
				$update_options['license']['verified'] = time();
				$update_options['license']['error'] = $api_call_errors ;

				/* update/adding license status, errors etc etc */
				update_option($option_name, $update_options);
			}
		}

		/*
			[html elements - add/remove/change classes, labels, status]
		*/
		$edd_data['html'] = array();
		$edd_data['html']['class_add'] = ($edd_data['action'] == 'activate_license') ? '' . WPPIZZA_SLUG . '_license_deactivate' : '' . WPPIZZA_SLUG . '_license_activate';
		$edd_data['html']['class_remove'] = ($edd_data['action'] == 'deactivate_license') ? '' . WPPIZZA_SLUG . '_license_deactivate' : '' . WPPIZZA_SLUG . '_license_activate';
		$edd_data['html']['label'] = ($edd_data['action'] == 'deactivate_license') ? __('Activate License', 'wppizza-admin') : __('De-Activate License', 'wppizza-admin');
		$edd_data['html']['status'] = $this->edd_status_markup($edd_data['api_results']['status'], $api_call_errors, $edd_data['api_results']['license_data']->expires, $show_remove);
		$edd_data['html']['update_success'] = (!empty($edd_data['api_results']['success']) && ($edd_data['api_results']['license_data']->success) ) ? true : false;//check if api call itself and license update succeeded , else do not change classes or lables
		$edd_data['html']['no_license_value'] = ($remove_license) ? true : false;

		/*********
			return to ajax request
		*********/
		print"".json_encode($edd_data)."";
		exit();
		}
	}

	/*********************************************************
	*
	*	[enqueue js if necessary]
	*
	*********************************************************/
	public function wppizza_add_helpers($current_screen){
		if( !empty($this -> has_registered_edd_plugins) && $current_screen->id == ''.WPPIZZA_POST_TYPE.'_page_'.$this->settings_page.'' && $current_screen->post_type == WPPIZZA_POST_TYPE && !empty($_GET['tab']) && 'licenses' == $_GET['tab']){
			/***enqueue scripts and styles***/
			add_action('admin_enqueue_scripts', array( $this, 'wppizza_enqueue_admin_scripts_and_styles'));
		}
	}
	/*********************************************************
	*
	*	[add js]
	*
	*********************************************************/
    public function wppizza_enqueue_admin_scripts_and_styles($hook) {
    	wp_register_script(WPPIZZA_SLUG.'_'.$this->settings_page.'_'.$this->tab_key, plugins_url( 'js/scripts.admin.'.$this->settings_page.'.'.$this->tab_key.'.js', WPPIZZA_PLUGIN_PATH ), array('jquery'), WPPIZZA_VERSION ,true);
    	wp_enqueue_script(WPPIZZA_SLUG.'_'.$this->settings_page.'_'.$this->tab_key);
    }

	/*------------------------------------------------------------------------------
	#
	#
	#	[settings page]
	#
	#
	------------------------------------------------------------------------------*/
	/*********************************************************
			[alter sidebar link label]
	*********************************************************/
	function admin_link_label($label){
		if(!empty($this -> has_registered_edd_plugins)){
			$label .=' / '.__('Licences','wppizza-admin').'';
		}
	return $label;
	}
	/*********************************************************
			[add section to a particular tab]
	*********************************************************/
	function admin_tabs($tabs){
		/**
			edd license tabs - since 3.3.6
		**/
		if(!empty($this -> has_registered_edd_plugins)){
			/* main tab */
			$tabs['tab'][$this->tab_key]=array('lbl'=>__('Licences','wppizza-admin'), 'slug'=>'licenses', 'sections'=>array(), 'save_options' => false);
			/* sections in tab */
			$tabs['tab'][$this->tab_key]['sections'][] = $this->section_key;

		}

	return $tabs;
	}


	/*------------------------------------------------------------------------------
	#	[section header to output some more info]
	#	@since 3.0
	#	@return array()
	------------------------------------------------------------------------------*/
	function sections_header($settings, $sections){
		if($settings['id']==$this->section_key){
			echo''.__('Entering and activating the licence is optional, but if you choose not to do so, you will not be informed of any future bugfixes and/or updates.', 'wppizza-admin').'<br />';
			echo''.__('Changing an existing key will automatically de-activate the old key for this site when activating your new key.', 'wppizza-admin').'<br />';
			echo'<b>'.__('To ensure backwards compatibility licence key settings might additionally also still be available for plugins/gateways in their dedicated licence settings page and can interchangeably be set there.', 'wppizza-admin').'</b><br />';
			echo'<span class="wppizza-highlight">'.__('Note: you will only receive update notifications if these have not been disabled globally (perhaps by using another plugin that manages these kind of notifications)', 'wppizza-admin').'</span>';
		}
	return;
	}

	/*------------------------------------------------------------------------------
	#	[settings section - setting page]
	#	@since 3.0
	#	@return array()
	------------------------------------------------------------------------------*/
	function admin_options_settings($settings, $sections, $fields, $inputs, $help){

		/*section*/
		if($sections){
			$settings['sections'][$this->section_key] =  __('Licences', 'wppizza-admin');
		}

		/*help*/
		if($help){
		}

		/*fields*/
		if($fields){
			/*
				licences
			*/
			$defined_licenses = $this->get_edd_registered_plugins();
			foreach($defined_licenses as $pluginKey => $licenseData){
				$field = $pluginKey.'_license';
				$settings['fields'][$this->section_key][$field] = array($licenseData['edd_data']['name'] , array(
					'value_key'=>$field,
					'option_key'=>$this->settings_page,
					'label'=>'',
					'description'=>array()
				));
			}
		}

	return $settings;
	}
	/*------------------------------------------------------------------------------
	#	[output option fields - setting page]
	#	@since 3.0
	#	@return array()
	------------------------------------------------------------------------------*/
	function admin_options_fields_settings($wppizza_options, $options_key, $field, $label, $description){

		/*
			print row for each license
		*/
		$defined_licenses = $this->get_edd_registered_plugins();
		foreach($defined_licenses as $pluginKey => $licenseData){
			if($field == $pluginKey.'_license'){
				do_action('wppizza_edd_init', $pluginKey, $licenseData);
			}
		}
	}
}
/***************************************************************
*
*	[ini]
*
***************************************************************/
$WPPIZZA_MODULE_TOOLS_LICENCES_INIT = new WPPIZZA_MODULE_TOOLS_LICENCES_INIT();
?>