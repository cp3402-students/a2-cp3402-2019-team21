<?php
/**
* WPPIZZA_MODULE_TOOLS_PRIVACY_GENERAL Class
*
* @package     WPPIZZA
* @subpackage  WPPIZZA_MODULE_TOOLS_PRIVACY_GENERAL
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
class WPPIZZA_MODULE_TOOLS_PRIVACY_GENERAL{

	private $settings_page = 'tools';/* which admin subpage (identified there by this->class_key) are we adding this to */
	private $tab_key = 'privacy';/* must be unique within this admin page*/
	private $section_key = 'general';

	function __construct() {
		/**********************************************************
			[add settings to admin]
		***********************************************************/
		if(is_admin()){
			/*** add to a specific tab ***/
			add_filter('wppizza_filter_admin_tabs_'.$this->settings_page.'', array($this, 'admin_tabs'), 10);
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
		}

		/**********************************************************
			Register WP Privacy actions/filters (admin)
		**********************************************************/
		global $wppizza_options, $wp_version;

		/*
			admin only
		*/
		if(is_admin()){
			/* suggested privacy policy text */
			add_action( 'admin_init', array( $this, 'register_privacy_policy_template'));
			/* register data export */
			add_filter('wp_privacy_personal_data_exporters',array( $this, 'register_data_exporter'), 10);
		}


		/*
			frontend
		*/
		/* add accept T/C & Privacy checkbox, must be wp >=4.9.6 */
		if ( version_compare( $wp_version, '4.9.6', '>=' ) ) {
			add_filter('wppizza_register_formfields',array( $this, 'register_formfields_accept_privacy_checkbox'), 100);
		}

	}

	/*******************************************************************************************************************************************************
	*
	*
	*
	* 	[admin privacy actions (export/erase/suggested text) ]
	*
	*
	*
	********************************************************************************************************************************************************/

	/*------------------------------------------------------------------------------
	#	[suggested privacy policy text]
	#
	#	@since 3.6
	#	@return array()
	------------------------------------------------------------------------------*/
	function register_privacy_policy_template() {
		global $wppizza_options;
		/*
			skip if function does not exist (WP < 4.9.6)
		*/
		if ( ! function_exists( 'wp_add_privacy_policy_content' ) ) {
			return;
		}

		/*
			set suggested text
		*/
		$registered_formfields = array();
		if(!empty($wppizza_options['order_form'])){
		foreach($wppizza_options['order_form'] as $ff){
			if(!empty($ff['enabled'])){
			$registered_formfields[] = $ff['lbl'];
			}
		}}
		$ffs = "&middot; ".implode(PHP_EOL."&middot; ", $registered_formfields).PHP_EOL."&middot; Credit Card / Payment Details.";

		/* text */
		$content = '<div class="wppizza-highlight">'.sprintf(__('Disclaimer: the below is some suggested privacy policy text when using the %s plugin. Edit as required for your particular site/setup. It does not constitute legal advise as to what is appropriate in your particular scenario.', 'wppizza-admin' ),WPPIZZA_NAME).'</div>';
		$content .=  sprintf(__( '
			We collect information about you during the checkout process on our store.
			This information may include but is not limited to:
			%s
			alongside any other details that might be requested from you for the purpose of processing your order.
			However, please note that specific credit card / payment details such as actual full credit card numbers, CVC codes and expiry dates are NOT being collected or processed by this server but are processed in a secure environmemt by our payment provider(s)
			<span class="wppizza-highlight">(i.e Paypal, Stripe etc - DELETE/ADD AS APPROPRIATE)</span>.

			On the basis of our legitimate business interests handling this data will allows us to:
			&middot; Send you important order/service/account information.
			&middot; Respond to your queries, refund requests or complaints.
			&middot; Process payments and prevent fraudulent transactions.
			&middot; Set up and administer your account, provide technical and/or customer support and to verify your identity.

			Additionally we may also collect the following information:
			&middot; Your location if you place an order, or if we need to estimate taxes and delivery costs based on your location.
			&middot; If you choose to create an account with us, your name, address, and email address, which will be used to populate the checkout for future orders.
			&middot; Your account email/password to allow you to access your account, if you have registered an account with us.
		', 'wppizza-admin' ), $ffs );
		$content .= "\n\n";


		$suggested_text = wp_kses_post( apply_filters( 'wppizza_privacy_policy_content', $content ) );

	wp_add_privacy_policy_content(WPPIZZA_NAME, wpautop( $suggested_text ) );
	}



	/*------------------------------------------------------------------------------
	#	[register data exporters]
	#
	#	@since 3.5
	#	@return array()
	------------------------------------------------------------------------------*/
	function register_data_exporter($exporters){
	  	$exporters[] = array(
	    	'exporter_friendly_name' => WPPIZZA_NAME,
	    	'callback'               => array($this, 'data_exporter'),
	    );
	  return $exporters;
	}
	/*------------------------------------------------------------------------------
	#	[export data]
	#
	#	@since 3.5
	#	@return array()
	------------------------------------------------------------------------------*/
	function data_exporter( $email_address, $page = 1 ){
		$limit = 250; // Limit us to avoid timing out
		$page = (int)$page;


		// Core group IDs include 'comments', 'posts', etc.
		// But you can add your own group IDs as needed
		$group_id = WPPIZZA_SLUG;

		// Optional group label. Core provides these for core groups.
		// If you define your own group, the first exporter to
		// include a label will be used as the group label in the
		// final exported report
		$group_label = sprintf(__( '%s Orders', 'wppizza-admin' ), get_bloginfo( 'name' ));


		/*
			email addresses stored might also be encrypted (using WPPIZZA_CRYPT_KEY)
			so lets make an array of encrypted and unencrypted emails to check
		*/
		$email_export = array();
		$email_export[] = $email_address; //unencrypted email
		$email_export[] = wppizza_maybe_encrypt_decrypt($email_address, true, false, true); //perhaps encrypted email
		/* make sure they are not identical (in case a WPPIZZA_CRYPT_KEY was not set) */
		$email_export = array_unique(array_filter($email_export));

		/*
			ini items to export
		*/
		$export_items = array();

		/*
			query args
		*/
		$args = array(
			'query'=>array(
				'payment_status' => 'NULL',//getting all data, even abandoned/non completed
				'email' => $email_export,//query for plain and hashed emails (if any)
			),
			'pagination' =>array(
				'paged' => 	$page ,
				'limit' => $limit ,
			),
		);
		$orders_for_export = WPPIZZA()->db->get_orders($args, 'data_exporter');

		/*
			do we have any orders to start off with ?
		*/
		if(empty($orders_for_export['total_number_of_orders'])){
			$export_data = array('data' => array(), 'done' => true);
			return $export_data;
		}

		foreach ( $orders_for_export['orders'] as $order_id => $order ) {

			/* ini customer data array */
			$customer_data = array();

			/* Order ID (blog_orderid) , append status*/
			$order_status = !empty($order['ordervars']['payment_status']['value_formatted']) ? ' ('.$order['ordervars']['payment_status']['value_formatted'].')' : '';
			$customer_data['blog_order_id']['name'] = __('Order ID (Status)' ,'wppizza-admin');
			$customer_data['blog_order_id']['value'] = str_replace('_', '.' ,$order_id) . $order_status ;


			/*
				customer_ini data , omitting accept privacy policy acceptance (for now as initialized orders will also be exported but will not necessarily have the policy accepted yet and could lead to confusion)
				also omitting wordpress registered users firts name / last name / email / login
				as that data already exists in the standard WP User export data

			*/
			foreach($order['customer'] as $cKey => $cArr){
				if(!empty($cArr['value']) && !in_array($cKey, array('privacy_terms_accept', 'email', 'first_name', 'last_name', 'login'))){
					$customer_data[$cKey]['name'] = $cArr['label'];
					$customer_data[$cKey]['value'] = $cArr['value'];
				}
			}

			/* ipaddress */
			if(!empty($order['ordervars']['ip_address']['value_formatted'])){
				$customer_data['ip_address']['name'] = $order['ordervars']['ip_address']['label'];
				$customer_data['ip_address']['value'] = $order['ordervars']['ip_address']['value_formatted'];
			}

			/* browser data */
			if(!empty($order['ordervars']['user_data']['value_formatted']['HTTP_USER_AGENT'])){
				$customer_data['user_data']['name'] = 'HTTP_USER_AGENT';
				$customer_data['user_data']['value'] = $order['ordervars']['user_data']['value_formatted']['HTTP_USER_AGENT'];
			}

			/*
				allow filtering for other plugins
				that have added their own columns perhaps
			*/
			$customer_data = apply_filters('wppizza_filter_data_exporter', $customer_data, $order, $order_id);

			/* payment status - to perhaps make sense of privacy policies acceptance if set to NO - dont think that's necessary, but leave it here for now*/
			//if(!empty($order['ordervars']['payment_status']['value_formatted'])){
			//	$customer_data['payment_status']['name'] = $order['ordervars']['payment_status']['label'];
			//	$customer_data['payment_status']['value'] = $order['ordervars']['payment_status']['value_formatted'];
			//}

			/*
				dataset for this order
			*/
			if(!empty(	$customer_data)){
				$export_items[] = array(
					'group_id'    => $group_id,
					'group_label' => $group_label,
					'item_id'     => $order_id,
					'data'        => $customer_data,
				);
			}

		}

		// Tell core if we have more to work on still
		$done = $orders_for_export['number_orders_on_page'] < $limit;

		/*
			return data to export
		*/
		$export_data = array();
		$export_data['data'] = $export_items;
		$export_data['done'] = $done;

	return $export_data;
	}


	/*******************************************************************************************************************************************************
	*
	*
	*
	* 	[Privacy Frontend]
	*
	*
	*
	********************************************************************************************************************************************************/
	/*------------------------------------------------------------------------------
	#	[add checkbox to checkout form that accepts t&c's + privacy]
	#
	#	@since 3.5
	#	@param array()
	#	@return array()
	------------------------------------------------------------------------------*/
	function register_formfields_accept_privacy_checkbox($formfields){

		global $wppizza_options;
		// only if enabled
		if(empty($wppizza_options[$this->settings_page]['privacy'])){
			return $formfields;
		}

		$privacy_link_id = (int)get_option('wp_page_for_privacy_policy');
		$privacy_permalink = get_permalink($privacy_link_id);
		$privacy_link_status = get_post_status($privacy_link_id);

		/* make sure privacy page status == 'publish' */
		if('publish' == $privacy_link_status){
			global $wppizza_options;

			/* making sure we always add this last (adding 10 instead of just 1, just for good measure)*/
			$mk_last_sort_id = max(wppizza_array_column($formfields, 'sort'))+10;

			$privacy_terms_label = (wppizza_is_checkout()) ? sprintf($wppizza_options['localization']['privacy_terms_accept'], $privacy_permalink) : $wppizza_options['localization']['privacy_terms_accepted'] ;

			$formfields['privacy_terms_accept'] = array();
			$formfields['privacy_terms_accept']['sort'] = $mk_last_sort_id;
			$formfields['privacy_terms_accept']['key'] = 'privacy_terms_accept';
			$formfields['privacy_terms_accept']['lbl'] = $privacy_terms_label;
			$formfields['privacy_terms_accept']['value'] = array();
			$formfields['privacy_terms_accept']['type'] = 'checkbox';
			$formfields['privacy_terms_accept']['enabled'] = true;
			$formfields['privacy_terms_accept']['required'] = true;
			$formfields['privacy_terms_accept']['required_on_pickup'] = true;
			$formfields['privacy_terms_accept']['prefill'] = null;//null => never prefill/check
			$formfields['privacy_terms_accept']['onregister'] = false;
			$formfields['privacy_terms_accept']['add_to_subject_line'] = false;
			$formfields['privacy_terms_accept']['placeholder'] = '';
			$formfields['privacy_terms_accept']['validation'] = array('default' => true);
		}


		return $formfields;
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

		$version_notice = version_compare( $wp_version, '4.9.6', '<' ) ? '<span class="wppizza-highlight">'.__('Requires Wordpress 4.9.6+', 'wppizza-admin').'</span>' : '' ;

		/*section*/
		if($sections){
			$settings['sections'][$this->section_key] =  __('Privacy', 'wppizza-admin');
		}

		/*help*/
		if($help){
			$settings['help'][$this->section_key][] = array(
				'label'=>__('Privacy (General)', 'wppizza-admin'),
				'description'=>array(
					'<h3>'.__('Enables certain privacy settings and functions to <b>help</b> you comply with privacy regulations (notably GDPR, if applicable) for your business.', 'wppizza-admin'). ' ' .$version_notice.'</h3>',
					'<span class="wppizza-highlight">'.__('Disclaimer: The below does not constitute legal advise. Neither does enabling the Privacy / GDPR setting here automatically make your site compliant with privacy regulations in your country. Furthermore, the outlines below <b>only</b> apply to the WPPizza plugin. Please consult a lawyer regarding full requirements to make your site (and business) compliant according to the privacy laws of your country.', 'wppizza-admin').'</span>',
					'<br>',

					'<b>'.__('The following will apply if you check "Enable Privacy Settings" below.', 'wppizza-admin').'</b>',
					'<ul>
						<li><b>'.__('Agree to Privacy Policy', 'wppizza-admin').'</b> - '.sprintf(__('A checkbox will be added to the end of the personal information form at checkout that must be ticked by the customer to be able to place an order.<br>If required, you can change the text adjacent to this checkbox in the "%s -> Localization -> Miscellaneous" settings of this plugin.<br>Use " %s -> Templates -> Emails / Templates -> Print" to show/hide the submitted value if required<br><span class="wppizza-highlight">Make sure you also write, set and publish a page that contains your privacy policy in "Wordpress -> Setings-> Privacy".<br/>Your privacy statement should include a statement why you need to collect certain data (i.e the formfields you have enabled in "%s -> Order Form") to fulfill an order.</span>', 'wppizza-admin'), WPPIZZA_NAME, WPPIZZA_NAME, WPPIZZA_NAME).'</li>
						<li><b>'.__('Personal Data - "Keep Browser Data"', 'wppizza-admin').'</b> - '.__('Unless this option is specifically enabled/checked, Browser Data (such as Referrer URL, User Agent etc) will not be stored anymore, as this data is typically unnecessary for processing or fulfillment of an order', 'wppizza-admin').'</li>
						<li><b>'.__('Personal Data - "Keep IP Addresses"', 'wppizza-admin').'</b> - '.__('Unless this option is specifically enabled/checked all IP Addresses will be stored anonymised, as this data is typically unnecessary for processing or fulfillment of an order.', 'wppizza-admin').'</li>
						<li><b>'.__('Export Personal Data', 'wppizza-admin').'</b> - '.__('Any personal data captured in the orders table of this plugin will be appended to the data export information when using "Wordpress -> Tools -> Export Personal Data"', 'wppizza-admin').'</li>
						<li><b>'.__('Erase Personal Data', 'wppizza-admin').'</b> - '.__('If you have received a request to erase a users personal data, you can choose which action (if any) should be taken for the various order statuses when using "Wordpress -> Tools -> Erase Personal Data"', 'wppizza-admin').'</li>
						<li><b>'.__('Data Retention', 'wppizza-admin').'</b> - '.sprintf(__('You might want to consider removing old database entries of orders that were not being completed for one reason or another in "%s -> Tools -> Maintenance"', 'wppizza-admin'), WPPIZZA_NAME ).'</li>
					</ul>',
					'<b>'.__('The functionalities associated with the settings here might be amended over time as requirements and possibilities (i.e additions to the Wordpress core) develop.', 'wppizza-admin').'</b>',

					'<br>',
					'<h3>'.__('A note on if you need to move this site elsewhere:', 'wppizza-admin').'</h3>'.
					'<span class="wppizza-highlight"><b>('.__('ONLY relevant if you ever need to move/copy any wppizza orders you have received on *this* site here to a *new* site. Else you can completely ignore the below', 'wppizza-admin').')</b></span>'.
					'<ul>'.
						'<li>'.__('Email addresses associated with privacy functions are stored encrypted in the database utilizing the standard - but unique to every site - <code>"SECURE_AUTH_SALT"</code> constant defined in your wp-config.php.', 'wppizza-admin').'</li>'.
						'<li>'.__('If for one reason or another you need to move existing ordesr from this site to another you must *add* the string from your <code>"SECURE_AUTH_SALT"</code> constant from *this* sites wp-config.php as <code>"WPPIZZA_CRYPT_KEY"</code> constant to the *new* sites wp-config.php', 'wppizza-admin').'</li>'.
						'<li>'.__('Example: If your current/this sites wp-config.php has the following: <code>define("SECURE_AUTH_SALT", "abcdefghifklmn");</code> *add* <code>"define("WPPIZZA_CRYPT_KEY", "abcdefghifklmn");"</code> to the *new* sites wp-config.php', 'wppizza-admin').'</li>'.
						'<li>'.__('Nothing will break if you do not do this, but some privacy functions might not work as expected on your new site if you do not define this constant.', 'wppizza-admin').'</li>'.
					'</ul>',

					'<br/>'.__('If you have any questions or comments regarding the above, please use the ususal channels (preferably the <a href="https://www.wp-pizza.com/support/" target="_blank">"Support forum"</a> in this case to make conversations about this topic visible to anyone)', 'wppizza-admin').'',
				)
			);
		}


		/*fields*/
		if($fields){

			$field = 'privacy';
			$settings['fields'][$this->section_key][$field] = array( __('Enable Privacy Settings', 'wppizza-admin'), array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('Simply enabling this option will NOT automatically make you GDPR compliant.', 'wppizza-admin').'<br/><span class="wppizza-highlight"><b>'.__('Please refer to the <a href="javascript:void(0)" class="wppizza-show-admin-help">help screen</a> for more details.', 'wppizza-admin').'</b></span>',
				'description'=>array($version_notice,)
			));

			$field = 'privacy_keep_ip_address';
			$settings['fields'][$this->section_key][$field] = array( __('Keep IP Address', 'wppizza-admin'), array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('If - for legal or other reasons - you need to always store full ip addresses with your orders, even if you have the privacy settings enabled above, check this box.', 'wppizza-admin'),
				'description'=>array($version_notice,)
			));

			$field = 'privacy_keep_browser_data';
			$settings['fields'][$this->section_key][$field] = array( __('Keep Browser Data', 'wppizza-admin'), array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('If - for legal or other reasons - you need to always store browser data (Referrer URL, User Agent) with your orders, even if you have the privacy settings enabled above, check this box', 'wppizza-admin'),
				'description'=>array($version_notice,)
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

		if($field=='privacy'){
			echo "<label>";
				echo "<input id='".$field."' name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' type='checkbox'  ". checked($wppizza_options[$options_key][$field],true,false)." value='1' />";
				echo "" . $label . "";
			echo "</label>";
			echo"".$description."";
		}

		if($field=='privacy_keep_ip_address'){
			echo "<label>";
				echo "<input id='".$field."' name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' type='checkbox'  ". checked($wppizza_options[$options_key][$field],true,false)." value='1' />";
				echo "" . $label . "";
			echo "</label>";
			echo"".$description."";
		}

		if($field=='privacy_keep_browser_data'){
			echo "<label>";
				echo "<input id='".$field."' name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' type='checkbox'  ". checked($wppizza_options[$options_key][$field],true,false)." value='1' />";
				echo "" . $label . "";
			echo "</label>";
			echo"".$description."";
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

		/* enable privacy (keeping any settings from v3.5.0.x though)*/
		$options[$this->settings_page]['privacy'] = isset($wppizza_options['settings']['privacy']) ? $wppizza_options['settings']['privacy'] : false;
		/* keep ip address even if privacy enabled (keeping any settings from v3.5.0.x though)*/
		$options[$this->settings_page]['privacy_keep_ip_address'] = isset($wppizza_options['settings']['privacy_keep_ip_address']) ? $wppizza_options['settings']['privacy_keep_ip_address'] : false;
		/* keep bowser data  even if privacy enabled (keeping any settings from v3.5.0.x though) */
		$options[$this->settings_page]['privacy_keep_browser_data'] = isset($wppizza_options['settings']['privacy_keep_browser_data']) ? $wppizza_options['settings']['privacy_keep_browser_data'] :  false;

		/*
		unset old 3.5.0.x parameters
		*/
		unset($options['settings']['privacy']);
		unset($options['settings']['privacy_keep_ip_address']);
		unset($options['settings']['privacy_keep_browser_data']);
		unset($options['settings']['privacy_copy_emails']);



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
			global $wppizza_options;

			$options[$this->settings_page]['privacy'] = !empty($input[$this->settings_page]['privacy']) ? true : false;

			$options[$this->settings_page]['privacy_keep_ip_address'] = !empty($input[$this->settings_page]['privacy_keep_ip_address']) ? true : false;

			$options[$this->settings_page]['privacy_keep_browser_data'] = !empty($input[$this->settings_page]['privacy_keep_browser_data']) ? true : false;

		}
	return $options;
	}

}
/***************************************************************
*
*	[ini]
*
***************************************************************/
$WPPIZZA_MODULE_TOOLS_PRIVACY_GENERAL = new WPPIZZA_MODULE_TOOLS_PRIVACY_GENERAL();
?>