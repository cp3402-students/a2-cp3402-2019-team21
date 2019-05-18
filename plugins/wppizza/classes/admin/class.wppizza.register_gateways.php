<?php
/**
* WPPIZZA_REGISTER_GATEWAYS Class
*
* @package     WPPIZZA
* @subpackage  WPPIZZA_REGISTER_GATEWAYS
* @copyright   Copyright (c) 2015, Oliver Bach
* @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
* @since       3.0
*
*/
if ( ! defined( 'ABSPATH' ) ) exit;/*Exit if accessed directly*/

/************************************************************************************************************************
*
*
*	[WPPIZZA_REGISTER_GATEWAYS]
*
*
************************************************************************************************************************/
class WPPIZZA_REGISTER_GATEWAYS{

	/*
		@type : object
		@param : gateway objects
	*/
	public $registered_gateways;



/******************************************************************************************************************
*
*
*	[construct]
*
*
******************************************************************************************************************/
	function __construct() {

		// note: to allow gateways to register some ajax too we might need to run !is_admin() check too
		//for now, keep the original until further investigation at some point
		//if(!is_admin() && defined( 'DOING_AJAX' ) && DOING_AJAX ){return;}

		/* skip if (frontend) ajax */
		if(defined( 'DOING_AJAX' ) && DOING_AJAX ){return;}


		/**instanciate empty object**/
		$this->registered_gateways = new stdClass();

		/**
			register gateways as applied by filter
		**/
		add_action('admin_init',array($this,'gateway_register'), 1);

		/**
			install / update gateways
		**/
		add_action('admin_init',array($this,'gateway_update_install'));

		/**
			add settings on admin gateways page
		**/
		add_action('current_screen', array($this, 'current_screen_add_settings'), 9);

	}


	/******************************************************************

		[add basic gateway settings to object from filter]

	******************************************************************/
	function gateway_register(){
		global $wppizza_options;
		/*skip on plugin install*/
		if($wppizza_options==0){return;}

		/**apply filter to get all gateways**/
		$registered_gateways = array();/* needed for php 7.1 */
		$registered_gateways = apply_filters('wppizza_register_gateways', $registered_gateways);


		/* iterate through gateways and instanciate */
		foreach($registered_gateways as $wppizza_gateway_class){
			/**restrict to classes starting with WPPIZZA_GATEWAY_ for consistancy**/
			if(substr(strtoupper($wppizza_gateway_class),0, 16)=='WPPIZZA_GATEWAY_'){

				/**create ident*/
				$gateway_ident=substr($wppizza_gateway_class,16);/*remove WPPIZZA_GATEWAY_, upercase and validate to A-Z0-9*/
				$gateway_ident=strtoupper($gateway_ident);
				$gateway_ident=(preg_replace("/[^A-Z0-9_]/","",$gateway_ident));

				/*instanciate class*/
				${$gateway_ident}=new $wppizza_gateway_class;
				${$gateway_ident}->gatewayIdent = $gateway_ident;
				//${$gateway_ident}->gatewayParameter = strtolower($gateway_ident);/* lowercase ident */
				${$gateway_ident}->gatewayOptionName = strtolower($wppizza_gateway_class);
				${$gateway_ident}->gatewayOptions = array();/*ini as empty array**/
				${$gateway_ident}->gatewaySettings = array();/*ini as empty array**/
			}

			/**registered gateways added to object**/
			$this->registered_gateways->$gateway_ident=${$gateway_ident};

		}

		return $this->registered_gateways;
	}

	/******************************************************************

		[install or update gateways,
		saving enabled gateways in main wppizza option too]

	******************************************************************/
	function gateway_update_install($registered_gateways = array()){
		global $wppizza_options;

		/*skip on plugin install*/
		if($wppizza_options==0){return;}


		/**
			get currently frontend enabled gateways. if none enabled save/set empty array
		**/
		$wppizza_current_gateways = !empty($wppizza_options['gateways']) ? $wppizza_options['gateways'] : array();

		/**
			ini array of gateways that are set to enabled
			if the resulting array differs from
			$wppizza_current_gateways
			we will update the main option table
		**/
		$wppizza_enabled_gateways=array();


		/**
			iterate through gateways and get options or add options if none exist
		**/
		foreach($this->registered_gateways as $wppizza_gateway_object){

			/**get options set, 0 if none existent*/
			$gateway_options_set = get_option($wppizza_gateway_object->gatewayOptionName, 0);

			/**gateway ident , for simplicities sake*/
			$gateway_ident = $wppizza_gateway_object->gatewayIdent;

			/******************************************
		 		GATEWAY INSTALL
			******************************************/
			/**
				if gateway has not had it's own options set in options table,
				do it now and add to object
			**/
			if($gateway_options_set == 0){
				/**get default options**/
				$gateway_options_set = $this->gateway_get_options($wppizza_gateway_object, $gateway_options_set, 'install');

				/**add gateway specific options to options table*/
				update_option($wppizza_gateway_object->gatewayOptionName, $gateway_options_set);
			}

			/******************************************
		 		GATEWAY UPDATE
			******************************************/
			if ($gateway_options_set != 0 && version_compare( $wppizza_gateway_object->gatewayVersion, $gateway_options_set['_gateway_version'], '>' ) ) {

				/**get set options and update as required**/
				$gateway_options_set = $this->gateway_get_options($wppizza_gateway_object, $gateway_options_set, 'update');

				/**update gateway specific options in options table*/
				update_option($wppizza_gateway_object->gatewayOptionName, $gateway_options_set);
			}


			/**
				check if gateway is enabled to add to main wppizza options if required
			**/
			if(!empty($gateway_options_set['_gateway_enabled'])){
				$wppizza_enabled_gateways[$gateway_ident]=array('sort'=> $gateway_options_set['_gateway_sortorder'], 'ident'=> $gateway_ident);
			}

			/**
				add set options to object. these will either be already available from options table or newly inserted ones
			**/
			$this->registered_gateways->$gateway_ident->gatewayOptions = $gateway_options_set;
		}


		/**
			update main wppizza options with enabled gateways if currently set gateways in option table are different to what should be enabled
			due to gateway sunnstall for example
		**/
		if($wppizza_enabled_gateways!=$wppizza_current_gateways){
			$wppizza_options['gateways']=$wppizza_enabled_gateways;
			update_option(WPPIZZA_SLUG, $wppizza_options);
		}

	return;
	}

	/****************************************************************
	*
	*	[insert default option on install / update options on version change]
	*	$parameter $options array() | filter passing on filtered options
	*	@since 3.0
	*	@return array()
	*
	****************************************************************/
	function gateway_get_options($gateway_object, $gateway_options_set, $type){

		$save_defaults = ($type=='install') ? true : false;

		/**
			ini options array
		**/
		$gateway_default_options=array();

		/**
			set version
		**/
		$gateway_default_options['_gateway_version']					= !empty($gateway_object->gatewayVersion) ? $gateway_object->gatewayVersion : '1.0';/*required. if not set will default to 1.0*/

		/**
			auto enable if set
		**/
		$gateway_default_options['_gateway_enabled']					= (($save_defaults && !empty($gateway_object->gatewayAutoEnable) ) || !empty($gateway_options_set['_gateway_enabled']) ) ? true : false;

		/**
			set sort order
		**/
		$gateway_default_options['_gateway_sortorder']					= !empty($gateway_options_set['_gateway_sortorder']) ? $gateway_options_set['_gateway_sortorder'] : 0 ;

		/**
			gateway label
		**/
		/** default **/
		$gw_label=(!empty($gateway_object->gatewayName) && $save_defaults) ? $gateway_object->gatewayName : $gateway_object->gatewayIdent;
		/** from saved **/
		if(!$save_defaults){
			$gw_label=!empty($gateway_options_set['_gateway_label']) ? $gateway_options_set['_gateway_label'] : '';
		}
		$gateway_default_options['_gateway_label']						= $gw_label ;/*required - if empty, will use ident as default*/

		/**
			gateway additional info
		**/
		/** default **/
		$gw_additional_info=(!empty($gateway_object->gatewayAdditionalInfo) && $save_defaults) ? $gateway_object->gatewayAdditionalInfo : '';
		/** from saved **/
		if(!$save_defaults){
			$gw_additional_info=!empty($gateway_options_set['_gateway_additional_info']) ? $gateway_options_set['_gateway_additional_info'] : '';
		}
		$gateway_default_options['_gateway_additional_info']				= $gw_additional_info ;

		/**
			gateway logo
		**/
		/** default **/
		$gw_logo=(!empty($gateway_object->gatewayLogo) && $save_defaults) ? $gateway_object->gatewayLogo : '';
		/** from saved **/
		if(!$save_defaults){
			$gw_logo = !empty($gateway_options_set['_gateway_logo']) ? $gateway_options_set['_gateway_logo'] : '';
		}
		$gateway_default_options['_gateway_logo']				= $gw_logo ;

		/**
			gateway button
		**/
		/** default **/
		$gw_button=(!empty($gateway_object->gatewayButton) && $save_defaults) ? $gateway_object->gatewayButton : '';
		/** from saved **/
		if(!$save_defaults){
			$gw_button=!empty($gateway_options_set['_gateway_button']) ? $gateway_options_set['_gateway_button'] : '';
		}
		$gateway_default_options['_gateway_button']				= $gw_button ;


		/**
			discounts enabled (cod for example)
		**/
		if(!empty($gateway_object->gatewayDiscount) || !empty($gateway_object->gatewayDiscountsSurcharges) ){
			$gateway_default_options['_gateway_discount_percent']			= !empty($gateway_options_set['_gateway_discount_percent']) ? $gateway_options_set['_gateway_discount_percent'] : 0;
			$gateway_default_options['_gateway_discount_fixed']				= !empty($gateway_options_set['_gateway_discount_fixed']) ? $gateway_options_set['_gateway_discount_fixed'] : 0;
			$gateway_default_options['_gateway_discount_min_order']			= !empty($gateway_options_set['_gateway_discount_min_order']) ? $gateway_options_set['_gateway_discount_min_order'] : 0;
		}

		/**
			surcharges enabled (default most gateways)
		**/
		if(empty($gateway_object->gatewayDiscount) || !empty($gateway_object->gatewayDiscountsSurcharges) ){
			$gateway_default_options['_gateway_surcharge_percent']			= !empty($gateway_options_set['_gateway_surcharge_percent']) ? $gateway_options_set['_gateway_surcharge_percent'] : 0;
			$gateway_default_options['_gateway_surcharge_fixed']			= !empty($gateway_options_set['_gateway_surcharge_fixed']) ? $gateway_options_set['_gateway_surcharge_fixed'] : 0;
		}

		/**
			if gateway has gateway_settings method, add these too to options
		**/
		if(method_exists($gateway_object, 'gateway_settings')){

			/**
				iterate over the set default options of this gateway to set in options table
			**/
			$gateway_settings_options = $gateway_object->gateway_settings($gateway_object->gatewayIdent, $gateway_object->gatewayOptions, $gateway_object->gatewayOptionName, '');

			/**
				allow filtering
			**/
			$gateway_settings_options = apply_filters('wppizza_gateway_filter_settings_'.$gateway_object->gatewayIdent.'', $gateway_options_set, $gateway_object->gatewayOptions, $gateway_object->gatewayOptionName, $type);

			/**
				iterate through settings and add as options
				could be done more elegantly i guess (using the same for install and update)
				but this will have to do for now
			**/
			// install gateway
			if($type=='install'){
				if(is_array($gateway_settings_options)){
				foreach($gateway_settings_options as $gateway_settings_set_options){
				/** set option **/
					$gateway_default_options[$gateway_settings_set_options['key']] = $gateway_settings_set_options['value'];
				}}
			}

			// update gateway
			if($type=='update'){
				foreach($gateway_settings_options as $settings_key => $settings_value){
					/** set option **/
					$gateway_default_options[$settings_key] = $settings_value;
				}
			}

		}

	return $gateway_default_options;
	}


	/******************************************************************

		[current screen, gateways subpage and options save. add settings]

	******************************************************************/
	function current_screen_add_settings($current_screen){

		/**
			only run on plugins and gateways page non ajax and when saving options
		**/
		if(	is_admin() &&
			( !defined( 'DOING_AJAX' ) || !DOING_AJAX ) &&
				(
					$current_screen->id=='plugins' ||
					$current_screen->id=='wppizza_page_gateways' ||
					($current_screen->id=='options' && isset($_POST[''.WPPIZZA_SLUG.'_gateways']))/**also regsiter when saving/updating options**/
			)
		){
			/**
				add settings to object
			**/
			$this->gateways_get_editable_settings($this->registered_gateways);
		}
	}

	/******************************************************************

		[add all settings fields]

	******************************************************************/
	function gateways_get_editable_settings($registered_gateways){

		/**if gateway has gateway_settings method, add these too to options**/
		foreach($registered_gateways as $gateway_ident=>$gateway_object){

			/* on install, or update we are already */
			$gateway_set_options = get_option($gateway_object->gatewayOptionName, 0);


			/**
				make all gateway objects filterable
				- for example - to add discount setting as well as surcharges for
				a given prepay gateway by adding
				$gateway_object->gatewayDiscountsSurcharges = true
				or whatever else one can think of
				use entirely at your own risk !!!
			**/
			$gateway_object = apply_filters('wppizza_filter_gateway_object_'.strtolower($gateway_ident).'', $gateway_object);


			/*
				[add  default/common editable  settings]
			*/
			$this->registered_gateways->$gateway_ident->gatewaySettings['_gateway_sortorder']=array(
                            'key' => '_gateway_sortorder',
                            'value' => $gateway_set_options['_gateway_sortorder'],
                            'type' => 'text_size_1',
                            'options' => '',
                            'validateCallback' => 'wppizza_validate_int_only',
                            'label' => __('Frontend display order','wppizza-admin'),
                            'descr' => '',
                            'placeholder' => '',
                            'wpml' => false
			);
			$this->registered_gateways->$gateway_ident->gatewaySettings['_gateway_label']=array(
                            'key' => '_gateway_label',
                            'value' => $gateway_set_options['_gateway_label'],
                            'type' => 'text',
                            'options' => '',
                            'validateCallback' => 'wppizza_validate_string',
                            'label' => __('Frontend Label','wppizza-admin'),
                            'descr' => '['.sprintf(__('displays "%s" if empty','wppizza-admin'),$gateway_object->gatewayName).']<br/>['.__('Used in emails and frontend. However, it is only being displayed on frontend order page if more than one gateway installed, activated and enabled','wppizza-admin').']',
                            'placeholder' => '',
                            'wpml' => true
			);
			$this->registered_gateways->$gateway_ident->gatewaySettings['_gateway_additional_info']=array(
                            'key' => '_gateway_additional_info',
                            'value' => $gateway_set_options['_gateway_additional_info'],
                            'type' => 'textarea',
                            'options' => '',
                            'validateCallback' => 'wppizza_validate_string',
                            'label' => __('Frontend: Additional Plugin Information','wppizza-admin'),
                            'descr' => __('only displayed if more than one gateway installed, activated and enabled','wppizza-admin'),
                            'placeholder' => '',
                            'wpml' => true
			);

			$this->registered_gateways->$gateway_ident->gatewaySettings['_gateway_logo']=array(
                            'key' => '_gateway_logo',
                            'value' => $gateway_set_options['_gateway_logo'],
                            'type' => 'text_size_100',
                            'options' => '',
                            'validateCallback' => 'wppizza_validate_string',
                            'label' => __('Small logo','wppizza-admin'),
                            'descr' => __('Optional. Empty field to not show any logo.','wppizza-admin'),
                            'placeholder' => 'http(s)://',
                            'wpml' => false
			);

			$this->registered_gateways->$gateway_ident->gatewaySettings['_gateway_button']=array(
                            'key' => '_gateway_button',
                            'value' => $gateway_set_options['_gateway_button'],
                            'type' => 'text_size_100',
                            'options' => '',
                            'validateCallback' => 'wppizza_validate_string',
                            'label' => __('Button','wppizza-admin'),
                            'descr' => __('Optional: Using image as button if only this gateway has been enabled (empty field to use standard button)','wppizza-admin'),
                            'placeholder' => 'http(s)://',
                            'wpml' => false
			);

			/**
				discounts enabled (cod for example)
			**/
			if(!empty($gateway_object->gatewayDiscount) || !empty($gateway_object->gatewayDiscountsSurcharges) ){
				$this->registered_gateways->$gateway_ident->gatewaySettings['_gateway_discount_percent']=array(
                            'key' => '_gateway_discount_percent',
                            'value' => (!empty($gateway_set_options['_gateway_discount_percent']) ? $gateway_set_options['_gateway_discount_percent'] : 0 ) ,
                            'type' => 'text_size_3',
                            'options' => '',
                            'validateCallback' => 'wppizza_validate_float_pc',
                            'label' => __('Discount (in %)','wppizza-admin'),
                            'descr' => '',
                            'placeholder' => '',
                            'wpml' => false
				);
				$this->registered_gateways->$gateway_ident->gatewaySettings['_gateway_discount_fixed']=array(
                            'key' => '_gateway_discount_fixed',
                            'value' => (!empty($gateway_set_options['_gateway_discount_fixed']) ? $gateway_set_options['_gateway_discount_fixed'] : 0 ) ,
                            'type' => 'text_size_3',
                            'options' => '',
                            'validateCallback' => 'wppizza_validate_float_only',
                            'label' => __('Fixed Discount','wppizza-admin'),
                            'descr' => '',
                            'placeholder' => '',
                            'wpml' => false
				);
				$this->registered_gateways->$gateway_ident->gatewaySettings['_gateway_discount_min_order']=array(
                            'key' => '_gateway_discount_min_order',
                            'value' => (!empty($gateway_set_options['_gateway_discount_min_order']) ? $gateway_set_options['_gateway_discount_min_order'] : 0 ) ,
                            'type' => 'text_size_3',
                            'options' => '',
                            'validateCallback' => 'wppizza_validate_float_only',
                            'label' => __('Minimum Order Value for above discounts to be applied','wppizza-admin'),
                            'descr' => '',
                            'placeholder' => '',
                            'wpml' => false
				);
			}

			/**
				surcharges enabled (i.e no dscounts)
			**/
			if(empty($gateway_object->gatewayDiscount) || !empty($gateway_object->gatewayDiscountsSurcharges) ){
				$this->registered_gateways->$gateway_ident->gatewaySettings['_gateway_surcharge_percent']=array(
                            'key' => '_gateway_surcharge_percent',
                            'value' => !empty($gateway_set_options['_gateway_surcharge_percent']) ? $gateway_set_options['_gateway_surcharge_percent'] : 0 ,
                            'type' => 'text_size_3',
                            'options' => '',
                            'validateCallback' => 'wppizza_validate_float_pc',
                            'label' => __('Surcharge (in %) [0 to disable]','wppizza-admin'),
                            'descr' => '',
                            'placeholder' => '',
                            'wpml' => false
				);
				$this->registered_gateways->$gateway_ident->gatewaySettings['_gateway_surcharge_fixed']=array(
                            'key' => '_gateway_surcharge_fixed',
                            'value' => !empty($gateway_set_options['_gateway_surcharge_fixed']) ? $gateway_set_options['_gateway_surcharge_fixed'] : 0 ,
                            'type' => 'text_size_3',
                            'options' => '',
                            'validateCallback' => 'wppizza_validate_float_only',
                            'label' => __('Fixed Surcharge [0 to disable]','wppizza-admin'),
                            'descr' => '',
                            'placeholder' => '',
                            'wpml' => false
				);
			}

			/**
				if gateway has gateway_settings method, add these too to options
			**/
			if(method_exists($gateway_object, 'gateway_settings')){

				/**iterate over the set default options of this gateway to set in options table**/
				$gateway_settings=$gateway_object->gateway_settings($gateway_object->gatewayIdent, $gateway_set_options, $gateway_object->gatewayOptionName);
				/**allow filtering**/
				$gateway_settings = apply_filters('wppizza_gateway_filter_settings_'.$gateway_object->gatewayIdent.'', $gateway_settings, $gateway_set_options, $gateway_object->gatewayOptionName, 'edit');

				/**add settings to object  re-indexing by key**/
				foreach($gateway_settings as $gateway_settings_option){
					/**
						settings
					**/
					$this->registered_gateways->$gateway_ident->gatewaySettings[$gateway_settings_option['key']] = $gateway_settings_option;
				}
			}
			/*
				we can use this filyet to reorder the settings fields in the admin - if we wish
			*/
			$this->registered_gateways->$gateway_ident->gatewaySettings = apply_filters('wppizza_filter_gateways_settings_'.$gateway_ident.'', $this->registered_gateways->$gateway_ident->gatewaySettings);

		}
	}

}
?>