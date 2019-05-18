<?php
/**
* WPPIZZA_MODULE_LOCALIZATION_COMMON Class
*
* @package     WPPIZZA
* @subpackage  WPPIZZA_MODULE_LOCALIZATION_COMMON
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
class WPPIZZA_MODULE_LOCALIZATION_COMMON{

	private $settings_page = 'localization';/* which admin subpage (identified there by this->class_key) are we adding this to */

	private $section_key = 'common';/* must be unique */


	function __construct() {
		/**********************************************************
			[add settings to admin]
		***********************************************************/
		if(is_admin()){
			/* add admin options settings page*/
			add_filter('wppizza_filter_settings_sections_'.$this->settings_page.'', array($this, 'admin_localization_help'), 10, 5);
			add_filter('wppizza_filter_settings_sections_'.$this->settings_page.'', array($this, 'admin_localization_order_labels'), 10, 5);
			add_filter('wppizza_filter_settings_sections_'.$this->settings_page.'', array($this, 'admin_localization_order_values'), 20, 5);
			add_filter('wppizza_filter_settings_sections_'.$this->settings_page.'', array($this, 'admin_localization_user_purchase_history'), 30, 5);
			add_filter('wppizza_filter_settings_sections_'.$this->settings_page.'', array($this, 'admin_localization_discounts'), 40, 5);
			add_filter('wppizza_filter_settings_sections_'.$this->settings_page.'', array($this, 'admin_localization_menu_item'), 50, 5);
			add_filter('wppizza_filter_settings_sections_'.$this->settings_page.'', array($this, 'admin_localization_pagination'), 60, 5);
			add_filter('wppizza_filter_settings_sections_'.$this->settings_page.'', array($this, 'admin_localization_openinghours'), 70, 5);
//			add_filter('wppizza_filter_settings_sections_'.$this->settings_page.'', array($this, 'admin_localization_email'), 80, 5);
			add_filter('wppizza_filter_settings_sections_'.$this->settings_page.'', array($this, 'admin_localization_orderpage'), 90, 5);
			add_filter('wppizza_filter_settings_sections_'.$this->settings_page.'', array($this, 'admin_localization_labels_itemised'), 100, 5);
			add_filter('wppizza_filter_settings_sections_'.$this->settings_page.'', array($this, 'admin_localization_price_labels_subtotals'), 110, 5);
			add_filter('wppizza_filter_settings_sections_'.$this->settings_page.'', array($this, 'admin_localization_cart'), 120, 5);
			add_filter('wppizza_filter_settings_sections_'.$this->settings_page.'', array($this, 'admin_localization_print_order_admin'), 130, 5);
			add_filter('wppizza_filter_settings_sections_'.$this->settings_page.'', array($this, 'admin_localization_template'), 140, 5);
			add_filter('wppizza_filter_settings_sections_'.$this->settings_page.'', array($this, 'admin_localization_gateways'), 150, 5);
			add_filter('wppizza_filter_settings_sections_'.$this->settings_page.'', array($this, 'admin_localization_widgets'), 160, 5);
			add_filter('wppizza_filter_settings_sections_'.$this->settings_page.'', array($this, 'admin_localization_user_profile'), 170, 5);
			add_filter('wppizza_filter_settings_sections_'.$this->settings_page.'', array($this, 'admin_localization_miscellaneous'), 180, 5);
			add_filter('wppizza_filter_settings_sections_'.$this->settings_page.'', array($this, 'admin_localization_admin_orderhistory_statuses'), 190, 5);
			add_filter('wppizza_filter_settings_sections_'.$this->settings_page.'', array($this, 'admin_localization_admin_orderhistory_custom'), 200, 5);

			/* add admin options settings page fields */
//			add_action('wppizza_admin_settings_section_fields_'.$this->settings_page.'', array($this, 'admin_options_fields_settings'), 10, 5);
			/**add default options **/
			add_filter('wppizza_filter_setup_default_options', array( $this, 'options_default'));

			/**validate options**/
			//add_filter('wppizza_filter_localization_validate', array( $this, 'options_validate'));
			/**validate string as html**/
			add_filter('wppizza_filter_localization_html', array( $this, 'options_validate_as_html'));
		}
	}

	/*******************************************************************************************************************************************************
	*
	*
	*
	* 	[frontend filters]
	*
	*
	*
	********************************************************************************************************************************************************/



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

	/*------------------------------------------------------------------------------
	#	[settings section - setting page]
	#	@since 3.0
	#	@return array()
	------------------------------------------------------------------------------*/
	function admin_localization_help($settings, $sections, $fields, $inputs, $help){

		$section_key = 'localization_help';
		/*sections*/
		if($sections){
			$settings['sections'][$section_key] =  __('Localization', 'wppizza-admin');
		}
		/*help*/
		if($help){
			$settings['help'][$section_key][] = array(
				'label'=>__('Manage Localization', 'wppizza-admin'),
				'description'=>array(
					__('Adjust / edit all your *frontend* localization strings using the form fields below according to your needs.', 'wppizza-admin'),
					__('Although defaults entered here will be in english if no language file for your particular language exists yet, you can nevertheless use the fields below to translate your frontend into *any* language.', 'wppizza-admin'),
					__('Any edits you make will be kept on future plugin updates / upgrades.', 'wppizza-admin'),
					__('<b>Order History - Order Statuses</b>: only non empty fields will be available for selection in the admin order history page. Therefore, if there are more than you need, just empty the relevant field or if you need more, enter them in the custom fields.', 'wppizza-admin'),
					__('<b>Order History - Custom Statuses</b>: if you need an additional dropdown selection (for example for drivers that have delivered a particular order), enter a label and comma separated list (of drivers names for example)', 'wppizza-admin'),
					'<b>'.__('Note: localization strings for the confirmation form - if used / enabled - can be found in "Order Form Settings"', 'wppizza-admin').'</b>'
				)
			);
		}
	return $settings;
	}

	/********************************
	*	[Common [Order Labels]]
	********************************/
	function admin_localization_order_labels($settings, $sections, $fields, $inputs, $help){

		$section_key = 'order_labels';

		/*sections*/
		if($sections){
			$settings['sections'][$section_key] =  __('Common [Order Labels]', 'wppizza-admin');
		}

		/*fields*/
		if($fields){

			$field = 'common_label_order_order_date';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('order date', 'wppizza-admin'),
				'description'=>array()
			));
			$field = 'common_label_order_order_id';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('order id', 'wppizza-admin'),
				'description'=>array()
			));
			$field = 'common_label_order_payment_outstanding';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('payment due', 'wppizza-admin'),
				'description'=>array()
			));

			$field = 'common_label_order_payment_unconfirmed';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('unconfirmed payment', 'wppizza-admin'),
				'description'=>array()
			));

			$field = 'common_label_order_payment_confirmed';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('confirmed payment', 'wppizza-admin'),
				'description'=>array()
			));

			$field = 'common_label_order_delivery_type';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('delivery type', 'wppizza-admin'),
				'description'=>array()
			));
			$field = 'common_label_order_payment_type';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('payment type', 'wppizza-admin'),
				'description'=>array()
			));
			$field = 'common_label_order_payment_method';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('payment method (i.e Cash or CC)', 'wppizza-admin'),
				'description'=>array()
			));
			//$field = 'order_paid_by';
			//$settings['fields'][$section_key][$field] = array( '', array(
			//	'value_key'=>$field,
			//	'option_key'=>$this->settings_page,
			//	'label'=>__('how was order paid for', 'wppizza-admin')
			//));
			$field = 'common_label_order_transaction_id';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('transaction id', 'wppizza-admin'),
				'description'=>array()
			));
			$field = 'common_label_order_total';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('total', 'wppizza-admin'),
				'description'=>array()
			));
			$field = 'common_label_order_refund';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('refunded', 'wppizza-admin'),
				'description'=>array()
			));
			$field = 'common_label_order_delivery_pickup_note';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('delivery / pickup note', 'wppizza-admin'),
				'description'=>array()
			));

			$field = 'common_label_order_wp_user_id';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('user id', 'wppizza-admin'),
				'description'=>array(__('currently unused', 'wppizza-admin'))
			));
			$field = 'common_label_order_currency';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('currency', 'wppizza-admin'),
				'description'=>array(__('currently unused', 'wppizza-admin'))
			));
		}

		return $settings;
	}

	/********************************
	*	[Common [Order Values]]
	********************************/
	function admin_localization_order_values($settings, $sections, $fields, $inputs, $help){

		$section_key = 'order_values';

		/*sections*/
		if($sections){
			$settings['sections'][$section_key] =  __('Common [Order Values]', 'wppizza-admin');
		}
		/*fields*/
		if($fields){
			$field = 'common_value_order_delivery';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('delivery', 'wppizza-admin'),
				'description'=>array()
			));
			$field = 'common_value_order_pickup';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('pickup', 'wppizza-admin'),
				'description'=>array()
			));
			$field = 'common_value_order_cash';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('cash', 'wppizza-admin'),
				'description'=>array()
			));
			$field = 'common_value_order_credit_card';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('credit card', 'wppizza-admin'),
				'description'=>array()
			));
		}

	return $settings;
	}
	/********************************
	*	[User Purchase History]
	********************************/
	function admin_localization_user_purchase_history($settings, $sections, $fields, $inputs, $help){
		$section_key = 'user_purchase_history';

		/*sections*/
		if($sections){
			$settings['sections'][$section_key] =  __('User Purchase History', 'wppizza-admin');
		}
		/*fields*/
		if($fields){
			$field = 'history_no_previous_orders';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('Text to display when the user has not had any previous orders', 'wppizza-admin')
			));
			$field = 'history_legend_order_details';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('Legend Order Details', 'wppizza-admin')
			));
			$field = 'history_legend_transaction_details';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('Legend Transaction Details', 'wppizza-admin')
			));
			$field = 'history_order_delivered_label';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('Label when order was delivered / fulfilled', 'wppizza-admin')
			));
		}
	return $settings;
	}
	function admin_localization_discounts($settings, $sections, $fields, $inputs, $help){
		$section_key = 'discounts';

		/*sections*/
		if($sections){
			$settings['sections'][$section_key] =  __('Discounts', 'wppizza-admin');
		}
		/*fields*/
		if($fields){
			$field = 'spend';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('Label Discount (Spend): i.e "spend" 50.00 save 10.00', 'wppizza-admin')
			));
			$field = 'save';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('Label Discount (Save): i.e spend 50.00 "save" 10.00', 'wppizza-admin')
			));
		}
	return $settings;
	}

	function admin_localization_menu_item($settings, $sections, $fields, $inputs, $help){
		$section_key = 'menu_item';

		/*sections*/
		if($sections){
			$settings['sections'][$section_key] =  __('Menu Item', 'wppizza-admin');
		}
		/*fields*/
		if($fields){
			$field = 'contains_additives';
			$settings['fields'][$section_key][$field] 	= 	array( '', array(
				'value_key'=>$field, 'localization', __('Menu Item: label when hovering over additives (if set)', 'wppizza-admin')
			));
			$field = 'add_to_cart';
			$settings['fields'][$section_key][$field] 		= 	array( '', array(
				'value_key'=>$field, 'localization', __('Menu Item: text to display when hovering over prices', 'wppizza-admin')
			));
			$field = 'contains_additives';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('Menu Item: label when hovering over additives (if set)', 'wppizza-admin')
			));
			$field = 'add_to_cart';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('Menu Item: text to display when hovering over prices', 'wppizza-admin')
			));
			$field = 'alert_closed';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('Menu Item: alert when trying to add to cart but shop is closed', 'wppizza-admin'),
				'description'=>array(__('(Only displayed when shoppingcart is displayed on page)', 'wppizza-admin'))
			));
			$field = 'alert_choose_size';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('Menu Item: alert when adding to cart by clicking on menu name but more than one size is available.', 'wppizza-admin'),
				'description'=>array(__('(Only relevant if "Add item to cart on click of *item title* " is enabled)', 'wppizza-admin'))
			));
			$field = 'jquery_fb_add_to_cart_info';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('Menu Item: text that briefly replaces selected item price when adding item to cart [html allowed].', 'wppizza-admin'),
				'description'=>array(__('(Only relevant if "Briefly replace item price with customised text" in WPPizza->Layout is enabled. CSS Class: "wppizza-item-added-feedback")', 'wppizza-admin'))
			));
			$field = 'no_results_found';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('Label if no menu items available on selected page', 'wppizza-admin')
			));
			$field = 'uncategorised';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('Category label (if displaying categories for items in cart etc) for uncategorised menu items', 'wppizza-admin')
			));
		}
	return $settings;
	}
	function admin_localization_pagination($settings, $sections, $fields, $inputs, $help){
		$section_key = 'pagination';

		/*sections*/
		if($sections){
			$settings['sections'][$section_key] =  __('Pagination', 'wppizza-admin');
		}
		/*fields*/
		if($fields){
			$field = 'previous';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('previous page', 'wppizza-admin')
			));
			$field = 'next';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('next page', 'wppizza-admin')
			));
			$field = 'pagination_info';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('pagination info (i.e 5-10 of 35 or similar, %s being replaced as required)', 'wppizza-admin')
			));
		}
	return $settings;
	}
	function admin_localization_openinghours($settings, $sections, $fields, $inputs, $help){
		$section_key = 'openinghours';

		/*sections*/
		if($sections){
			$settings['sections'][$section_key] =  __('Openinghours', 'wppizza-admin');
		}
		/*fields*/
		if($fields){
			$field = 'openinghours_closed';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('text to display when shop is closed that day ', 'wppizza-admin')
			));
			$field = 'openinghours_24hrs';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('text to display when shop is open the whole day ', 'wppizza-admin')
			));

		}
	return $settings;
	}
//	function admin_localization_email($settings, $sections, $fields, $inputs, $help){
//		$section_key = 'email';
//
//		/*sections*/
//		if($sections){
//			$settings['sections'][$section_key] =  __('Order Email', 'wppizza-admin');
//		}
//		/*fields*/
//		if($fields){
//			$field = 'order_email_footer';
//			$settings['fields'][$section_key][$field] = array( '', array(
//				'value_key'=>$field,
//				'option_key'=>$this->settings_page,
//				'label'=>__('Text you would like to display at the end of emails after everything else. [html allowed]', 'wppizza-admin')
//			));
//		}
//	return $settings;
//	}
	function admin_localization_orderpage($settings, $sections, $fields, $inputs, $help){
		$section_key = 'orderpage';

		/*sections*/
		if($sections){
			$settings['sections'][$section_key] =  __('Order Page', 'wppizza-admin'). ' / ' . __('"Thank You" Page', 'wppizza-admin');
		}
		/*fields*/
		if($fields){
			$field = 'your_order';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('label above itemised order', 'wppizza-admin')
			));
			$field = 'send_order';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('button label for sending order', 'wppizza-admin')
			));
			$field = 'update_order';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('button label for updating order [if enabled]', 'wppizza-admin')
			));
			$field = 'order_form_legend';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('label above personal info', 'wppizza-admin')
			));

			$field = 'order_page_handling';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('[Handling Charges]: text on order page if a handling charge for payment processing has been made (if applicable)', 'wppizza-admin')
			));
			$field = 'order_page_handling_oncheckout';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('[Handling Charges]: text on order page if any handling charge will be calculated on checkout by a/the gateway itself', 'wppizza-admin')
			));
			$field = 'required_field';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('message when required field is missing', 'wppizza-admin')
			));
			$field = 'required_field_email';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('message when email address is invalid', 'wppizza-admin')
			));
			$field = 'required_field_decimal';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('message when field should be a decimal number', 'wppizza-admin')
			));
			$field = 'thank_you';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('label of thank you page after order has been sent', 'wppizza-admin')
			));
			$field = 'thank_you_p';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('text of thank you page after order has been successfully sent', 'wppizza-admin'),
				'textarea' => true
			));
			$field = 'personal_information';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('label "personal information" on thank you page after an order has been sent', 'wppizza-admin')
			));
			$field = 'order_details';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('label "order details" on thank you page after an order has been sent', 'wppizza-admin')
			));
			$field = 'thank_you_error';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('text on "thank you" page if there was an *error* sending order emails ', 'wppizza-admin')
			));
			$field = 'order_not_found';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('generic text when order can not be found.', 'wppizza-admin')
			));
			$field = 'label_return_to_shop';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('generic "return to shop" label - used in links for cancelled orders for example.', 'wppizza-admin')
			));

			$field = 'order_ini_additional_info';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('text optional - additional info on order page [above all other details. only displays before submitting]', 'wppizza-admin'),
				'textarea' => true
			));
			$field = 'update_profile';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('label next to checkbox text to allow user to update profile', 'wppizza-admin')
			));
			$field = 'tips';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('Tips / Gratuities', 'wppizza-admin')
			));
			$field = 'loginout_have_account';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('[login/logout]: text before login link', 'wppizza-admin')
			));
			$field = 'register_option_label';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('[register]: text label register or continue as guest', 'wppizza-admin')
			));
			$field = 'register_option_guest';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('[register]: register option -> as guest', 'wppizza-admin')
			));
			$field = 'register_option_create_account';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('[register]: register option -> create account', 'wppizza-admin')
			));
			$field = 'register_option_create_account_info';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('[register]: additional info when create account option is chosen [html allowed]', 'wppizza-admin')
			));
			$field = 'register_option_create_account_error';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('[register]: error if email was already registered [html allowed]', 'wppizza-admin')
			));
		}
	return $settings;
	}
	function admin_localization_labels_itemised($settings, $sections, $fields, $inputs, $help){
		$section_key = 'labels_itemised';

		/*sections*/
		if($sections){
			$settings['sections'][$section_key] =  __('Labels - Itemised Order', 'wppizza-admin');
		}
		/*fields*/
		if($fields){
			$field = 'itemised_label_quantity';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('Itemised Order Label - Quantity', 'wppizza-admin')
			));
			$field = 'itemised_label_article';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('Itemised Order Label - Article', 'wppizza-admin')
			));
			$field = 'itemised_label_price';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('Itemised Order Label - Single item price', 'wppizza-admin')
			));
			$field = 'itemised_label_taxrate';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('Itemised Order Label - Applicable taxrate for item  (only shown if items have different rates)', 'wppizza-admin')
			));
			$field = 'itemised_label_total';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('Itemised Order Label - Total sum for item', 'wppizza-admin')
			));
		}
	return $settings;
	}
	function admin_localization_price_labels_subtotals($settings, $sections, $fields, $inputs, $help){
		$section_key = 'price_labels_subtotals';

		/*sections*/
		if($sections){
			$settings['sections'][$section_key] =  __('Labels - (Sub)Totals', 'wppizza-admin');
		}
		/*fields*/
		if($fields){
			$field = 'free_delivery';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('(Sub)Totals: text to display when free delivery applies', 'wppizza-admin')
			));
			$field = 'delivery_charges';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('(Sub)Totals: text delivery charges - when set to "Fixed" or "Free delivery over" (if applicable)', 'wppizza-admin')
			));
			$field = 'delivery_charges_per_item';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('(Sub)Totals: text delivery when set to "Delivery Charges per item" (if applicable)', 'wppizza-admin')
			));
			$field = 'discount';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('(Sub)Totals: text before sum of discounts applied(if any)', 'wppizza-admin')
			));
			$field = 'item_tax_total';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('(Sub)Totals: text before sum of tax applied to all items(if > 0). %s will be replaced by taxrate(s) applied', 'wppizza-admin')
			));
			$field = 'taxes_included';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('(Sub)Totals: text before sum of tax applied if prices have been entered *inclusive* of tax (if > 0)', 'wppizza-admin'),
				'description'=>array(__('%s will be replaced by taxrate(s) applied', 'wppizza-admin'))
			));
			$field = 'shipping_tax';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('(Sub)Totals: delivery charges tax (if any). %s will be replaced by taxrate(s) applied', 'wppizza-admin')
			));

			$field = 'tax_total';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('(Sub)Totals: Tax total', 'wppizza-admin')
			));

			$field = 'tax_total_included';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('(Sub)Totals: Taxes included', 'wppizza-admin')
			));


			$field = 'handling_charges';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('(Sub)Totals: handling charges (if any)', 'wppizza-admin')
			));

			$field = 'order_total';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('(Sub)Totals: text before total sum of ORDER', 'wppizza-admin')
			));
			$field = 'order_items';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('(Sub)Totals: text before total sum of ITEMS in cart', 'wppizza-admin')
			));
		}
	return $settings;
	}
	function admin_localization_print_order_admin($settings, $sections, $fields, $inputs, $help){
		$section_key = 'print_order_admin';

		/*sections*/
		if($sections){
			$settings['sections'][$section_key] =  __('Print/Email Order Templates ', 'wppizza-admin');
		}
		/*fields*/
		if($fields){
			$field = 'header_order_print_header';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('[Header]: optional - for example your shops name', 'wppizza-admin')
			));
			$field = 'header_order_print_shop_address';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('[Address]: replace with your shop\'s address [html allowed]', 'wppizza-admin')
			));
			$field = 'header_order_print_customer_label';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('[Label]: customer details', 'wppizza-admin')
			));
			$field = 'header_order_print_overview_label';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('[Label]: order overview', 'wppizza-admin')
			));
			$field = 'header_order_print_itemised_article';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('[itemised header]: article', 'wppizza-admin')
			));
			$field = 'header_order_print_itemised_price';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('[itemised header]: price', 'wppizza-admin')
			));
			$field = 'header_order_print_itemised_quantity';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('[itemised header]: quantity', 'wppizza-admin')
			));
		}
	return $settings;
	}
	function admin_localization_cart($settings, $sections, $fields, $inputs, $help){
		$section_key = 'cart';

		/*sections*/
		if($sections){
			$settings['sections'][$section_key] =  __('Shoppingcart', 'wppizza-admin');
		}
		/*fields*/
		if($fields){
			$field = 'closed';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('text to display when shop closed ', 'wppizza-admin')
			));
			$field = 'empty_cart';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('empty cart button text', 'wppizza-admin')
			));
			$field = 'view_cart';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('view cart button text', 'wppizza-admin')
			));
			$field = 'cart_is_empty';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('text to display when cart is empty', 'wppizza-admin')
			));
			$field = 'remove_from_cart';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('text to display when hovering over remove from cart icon', 'wppizza-admin')
			));
			$field = 'place_your_order';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('text of button in cart to proceed to order page', 'wppizza-admin')
			));
			$field = 'cart_checkout';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('text of button in small carts (minicart etc) to proceed to order page', 'wppizza-admin')
			));
			$field = 'cart_closed';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('text in small carts (minicart etc) if shop closed', 'wppizza-admin')
			));
		}
	return $settings;
	}
	function admin_localization_template($settings, $sections, $fields, $inputs, $help){
		$section_key = 'template';

		/*sections*/
		if($sections){
			$settings['sections'][$section_key] =  __('Templates Other', 'wppizza-admin');
		}
		/*fields*/
		if($fields){
			$field = 'templates_label_site';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('Label : "Site Details"', 'wppizza-admin')
			));
			$field = 'templates_label_ordervars';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('Label : "Overview"', 'wppizza-admin')
			));
			$field = 'templates_label_customer';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('Label : "Customer Details"', 'wppizza-admin')
			));
			$field = 'templates_label_order';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('Label : "Order Details"', 'wppizza-admin')
			));
			$field = 'templates_label_order_left';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('Label : "Order" - [1] First Column (e.g Quantity)', 'wppizza-admin')
			));
			$field = 'templates_label_order_center';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('Label : "Order" - [2] Second Column (e.g Article)', 'wppizza-admin')
			));
			$field = 'templates_label_order_right';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('Label : "Order" - [3] Third Column (e.g Price)', 'wppizza-admin')
			));
			$field = 'templates_label_summary';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('Label : "Summary"', 'wppizza-admin')
			));
			$field = 'templates_user_is_guest';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('Overview -> User : "Guest"', 'wppizza-admin')
			));
			$field = 'templates_user_is_registered';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('Overview -> User : "Registered"', 'wppizza-admin')
			));
			$field = 'templates_footer_note';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('Footer : text after summary / totals [html allowed - use linebreaks for newlines in plaintext output]', 'wppizza-admin'),
				'textarea' => true
			));
		}
	return $settings;
	}
	function admin_localization_gateways($settings, $sections, $fields, $inputs, $help){
		$section_key = 'gateways';

		/*sections*/
		if($sections){
			$settings['sections'][$section_key] =  __('Gateways', 'wppizza-admin');
		}
		/*fields*/
		if($fields){
			$field = 'gateway_select_label';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>"".__('Displayed above gateway choices if those are displayed as full width buttons. Displayed adjacent to select if gateways displayed dropdown. Edit css as required', 'wppizza-admin')." ".__('[only applicable if more than one gateway installed, activated and enabled]', 'wppizza-admin').""
			));

			$field = 'order_cancelled';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('label on order page when an order has been cancelled', 'wppizza-admin')
			));

			$field = 'order_cancelled_p';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('text on order page when an order has been cancelled', 'wppizza-admin')
			));

			$field = 'order_processing';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('label on order page when payment is being processed', 'wppizza-admin')
			));

			$field = 'order_processing_p';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('text on order page when an order is being processed [html allowed]', 'wppizza-admin')
			));

			$field = 'order_payment_pending';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('label on order page when payment has not yet been confirmed / is still pending', 'wppizza-admin')
			));

			$field = 'order_payment_pending_p';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('text on order page when payment has not yet been confirmed / is still pending (%s will be replaced by the link to the current page) [html allowed]', 'wppizza-admin')
			));

			$field = 'order_unconfirmed';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('label on order page when payment has been accepted but is still awaiting confirmation (some gateways might take some time for final payment confirmation).', 'wppizza-admin')
			));

			$field = 'order_unconfirmed_p';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('text on order page when payment has been accepted but is still awaiting confirmation (some gateways might take some time for final payment confirmation). [html allowed]', 'wppizza-admin')
			));

			$field = 'order_confirmed';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('label on order page when payment has been confirmed (for gateways that might take some time for final payment confirmation).', 'wppizza-admin')
			));

			$field = 'order_confirmed_p';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('text on order page when payment has been confirmed (for gateways that might take some time for final payment confirmation). [html allowed]', 'wppizza-admin')
			));

			$field = 'gateway_your_order';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('some gateways allow a distinct text to be displayed on their payment pages and/or receipts (might get shortened by the gateway)', 'wppizza-admin')
			));

			$field = 'gateway_enter_payment_details';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('Fieldset label if your chosen payment gateway allows payment details to be entered inline (as opposed to redirect or modal/overlay windows)', 'wppizza-admin')
			));

		}
	return $settings;
	}
	function admin_localization_widgets($settings, $sections, $fields, $inputs, $help){
		$section_key = 'widgets';

		/*sections*/
		if($sections){
			$settings['sections'][$section_key] =  __('Widgets', 'wppizza-admin');
		}
		/*fields*/
		if($fields){
			$field = 'widget_navigation_dropdown_placeholder';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('select default text/placeholder to display for navigation widget when using dropdown [empty to omit placeholder]', 'wppizza-admin')
			));
		}
	return $settings;
	}
	function admin_localization_user_profile($settings, $sections, $fields, $inputs, $help){
		$section_key = 'user_profile';

		/*sections*/
		if($sections){
			$settings['sections'][$section_key] =  __('User Profile', 'wppizza-admin');
		}
		/*fields*/
		if($fields){
			$field = 'user_profile_label_additional_info';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('Title above additional fields added/enabled', 'wppizza-admin')
			));
		}
	return $settings;
	}
	function admin_localization_miscellaneous($settings, $sections, $fields, $inputs, $help){
		$section_key = 'miscellaneous';

		/*sections*/
		if($sections){
			$settings['sections'][$section_key] =  __('Miscellaneous', 'wppizza-admin');
		}
		/*fields*/
		if($fields){

			$field = 'generic_placeholder_select';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('Generic placeholder / initial select for dropdowns', 'wppizza-admin')
			));
			$field = 'generic_placeholder_checkbox_0';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('Generic placeholder when a checkbox was left un-checked', 'wppizza-admin')
			));
			$field = 'generic_placeholder_checkbox_1';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('Generic placeholder when a checkbox was checked', 'wppizza-admin')
			));
			$field = 'admin_notify_new_order_label';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>sprintf(__('Admin bar "New Order(s)" notifications [%%s will be replaced with %s]', 'wppizza-admin'), WPPIZZA_NAME)
			));
			$field = 'failed_payment_try_again_link';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('Link Label back to order page if a payment has failed', 'wppizza-admin')
			));
			$field = 'generic_back_to_site';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('Generic "Back to Merchant Site" Label', 'wppizza-admin')
			));
			$field = 'generic_error_label';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('Generic "Error" Label', 'wppizza-admin')
			));
			$field = 'generic_error_details';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('Generic "Error Details" Label', 'wppizza-admin')
			));
			$field = 'localize_zero_price';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('Display "Free" - or whatever you set here - instead of price if price equals zero (and enabled in WPPizza -> Layout)', 'wppizza-admin')
			));

			$field = 'privacy_terms_accept';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>sprintf(__('Label for "accept privacy" checkbox on order form (if enabled in %s->Settings->GDPR / Privacy). %%s will be replaced with a link to your published privacy page set in Wordpress->Settings->Privacy', 'wppizza-admin'), WPPIZZA_NAME)
			));

			$field = 'privacy_terms_accepted';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>sprintf(__('Label if "accept privacy" checkbox was checked on confirmation page (if used and provided %s->Settings->GDPR / Privacy is enabled) ', 'wppizza-admin'), WPPIZZA_NAME)
			));


		}
	return $settings;
	}
	function admin_localization_admin_orderhistory_statuses($settings, $sections, $fields, $inputs, $help){
		$section_key = 'orderhistory_statuses';

		/*sections*/
		if($sections){
			$settings['sections'][$section_key] =  __('Admin Order History - Order Statuses <a href="javascript:void(0)" class="wppizza-show-admin-help">(see help)</a>', 'wppizza-admin');
		}
		/*fields*/
		if($fields){

			$field = 'order_history_status_new';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('Order Status : New', 'wppizza-admin')
			));
			$field = 'order_history_status_acknowledged';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('Order Status : Acknowledged', 'wppizza-admin')
			));
			$field = 'order_history_status_on_hold';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('Order Status : On Hold', 'wppizza-admin')
			));
			$field = 'order_history_status_processed';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('Order Status : Processed', 'wppizza-admin')
			));
			$field = 'order_history_status_delivered';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('Order Status : Delivered', 'wppizza-admin')
			));
			$field = 'order_history_status_rejected';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('Order Status : Rejected', 'wppizza-admin')
			));
			$field = 'order_history_status_refunded';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('Order Status : Refunded', 'wppizza-admin')
			));

			$field = 'order_history_status_other';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('Order Status : Other', 'wppizza-admin')
			));
			$field = 'order_history_status_custom_1';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('Order Status : Custom 1', 'wppizza-admin')
			));
			$field = 'order_history_status_custom_2';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('Order Status : Custom 2', 'wppizza-admin')
			));
			$field = 'order_history_status_custom_3';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('Order Status : Custom 3', 'wppizza-admin')
			));
			$field = 'order_history_status_custom_4';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('Order Status : Custom 4', 'wppizza-admin')
			));

		}
	return $settings;
	}
	function admin_localization_admin_orderhistory_custom($settings, $sections, $fields, $inputs, $help){
		$section_key = 'orderhistory_custom';

		/*sections*/
		if($sections){
			$settings['sections'][$section_key] =  __('Admin Order History - Custom Statuses <a href="javascript:void(0)" class="wppizza-show-admin-help">(see help)</a>', 'wppizza-admin');
		}
		/*fields*/
		if($fields){
			$field = 'order_history_custom_status_label';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('Custom Options Label', 'wppizza-admin')
			));
			$field = 'order_history_custom_status_options';
			$settings['fields'][$section_key][$field] = array( '', array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('Custom Options [enter a comma separated list of options]', 'wppizza-admin')
			));
		}
	return $settings;
	}





	/*------------------------------------------------------------------------------
	#	[output option fields - setting page]
	#	@since 3.0
	#	@return array()
	------------------------------------------------------------------------------*/
//	function admin_options_fields_settings($wppizza_options, $options_key, $field, $label, $description){


//		if($field=='placeholder_img'){
//			print'<label>';
//				echo "<input id='".$field."' name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' type='checkbox'  ". checked($wppizza_options[$options_key][$field],true,false)." value='1' />";
//				print'' . $label . '';
//			print'</label>';
//			print'' . $description . '';
//		}
//		if($field=='prettyPhoto' ){
//			print'<label>';
//				echo "<input id='".$field."' name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' type='checkbox'  ". checked($wppizza_options[$options_key][$field],true,false)." value='1' />";
//				print'' . $label . '';
//			print'</label>';
//			print'' . $description . '';
//		}
//		if($field=='prettyPhotoStyle'){
//			print'<label>';
//				echo "<select id='".$field."' name='".WPPIZZA_SLUG."[".$options_key."][".$field."]'>";
//					echo "<option value='pp_default' ".selected($wppizza_options[$options_key][$field],"pp_default",false).">default</option>";
//					echo "<option value='light_rounded' ".selected($wppizza_options[$options_key][$field],"light_rounded",false).">light rounded</option>";
//					echo "<option value='dark_rounded' ".selected($wppizza_options[$options_key][$field],"dark_rounded",false).">dark rounded</option>";
//					echo "<option value='light_square' ".selected($wppizza_options[$options_key][$field],"light_square",false).">light square</option>";
//					echo "<option value='dark_square' ".selected($wppizza_options[$options_key][$field],"dark_square",false).">dark square</option>";
//					echo "<option value='facebook' ".selected($wppizza_options[$options_key][$field],"facebook",false).">facebook</option>";
//				echo "</select>";
//				print'' . $label . '';
//			print'</label>';
//			print'' . $description . '';
//		}
//	}

	/****************************************************************
	*
	*	[insert default option on install]
	*	$parameter $options array() | filter passing on filtered options
	*	@since 3.0
	*	@return array()
	*	using esc_html__ here to distinctly find for poedit frontend values
	****************************************************************/
	function options_default($options){

		$options[$this->settings_page]['contains_additives'] = esc_html__('contains additives', 'wppizza');
		$options[$this->settings_page]['add_to_cart'] = esc_html__('add to cart', 'wppizza');
		$options[$this->settings_page]['alert_closed'] = esc_html__('sorry, we are currently closed', 'wppizza');
		$options[$this->settings_page]['alert_choose_size'] = esc_html__('please choose a size', 'wppizza');
		$options[$this->settings_page]['jquery_fb_add_to_cart_info'] = html_entity_decode(esc_html__('<div>&#10004;</div>item added', 'wppizza'));
		$options[$this->settings_page]['no_results_found'] = esc_html__('no results found', 'wppizza');
		$options[$this->settings_page]['uncategorised'] = esc_html__('uncategorised', 'wppizza');
		$options[$this->settings_page]['previous'] = esc_html__('< previous', 'wppizza');
		$options[$this->settings_page]['next'] = esc_html__('next >', 'wppizza');
		$options[$this->settings_page]['pagination_info'] = esc_html__('%s - %s of %s', 'wppizza');
		$options[$this->settings_page]['closed'] = esc_html__('currently closed', 'wppizza');
		$options[$this->settings_page]['empty_cart'] = esc_html__('empty cart', 'wppizza');
		$options[$this->settings_page]['view_cart'] = esc_html__('view cart', 'wppizza');
		$options[$this->settings_page]['cart_is_empty'] = esc_html__('your cart is empty', 'wppizza');
		$options[$this->settings_page]['remove_from_cart'] = esc_html__('remove from cart', 'wppizza');
		$options[$this->settings_page]['place_your_order'] = esc_html__('place your order', 'wppizza');
		$options[$this->settings_page]['cart_checkout'] = esc_html__('checkout', 'wppizza');
		$options[$this->settings_page]['cart_closed'] = esc_html__('we\'re closed', 'wppizza');
		$options[$this->settings_page]['history_no_previous_orders'] = esc_html__('You have no previous orders', 'wppizza');
		$options[$this->settings_page]['history_legend_order_details'] = esc_html__('Order Details', 'wppizza');
		$options[$this->settings_page]['history_legend_transaction_details'] = esc_html__('Transaction Details', 'wppizza');
		$options[$this->settings_page]['history_order_delivered_label'] = esc_html__('Delivered:', 'wppizza');
		$options[$this->settings_page]['your_order'] = esc_html__('your order', 'wppizza');
		$options[$this->settings_page]['send_order'] = esc_html__('send order', 'wppizza');
		$options[$this->settings_page]['update_order'] = esc_html__('update order', 'wppizza');
		$options[$this->settings_page]['order_form_legend'] = esc_html__('please enter the required information below', 'wppizza');
		$options[$this->settings_page]['order_page_handling'] = esc_html__('handling charge', 'wppizza');
		$options[$this->settings_page]['order_page_handling_oncheckout'] = esc_html__('calculated on checkout', 'wppizza');
		$options[$this->settings_page]['required_field'] = esc_html__('this is a required field', 'wppizza');
		$options[$this->settings_page]['required_field_email'] = esc_html__('invalid email address', 'wppizza');
		$options[$this->settings_page]['required_field_decimal'] = esc_html__('decimal numbers only please', 'wppizza');
		$options[$this->settings_page]['thank_you'] = esc_html__('thank you', 'wppizza');
		$options[$this->settings_page]['thank_you_p'] = esc_html__('thank you, we have received your order', 'wppizza');
		$options[$this->settings_page]['personal_information'] = esc_html__('personal information', 'wppizza');
		$options[$this->settings_page]['order_details'] = esc_html__('order details', 'wppizza');
		$options[$this->settings_page]['thank_you_error'] = esc_html__('Apologies. There was an error receiving your order. Please try again.', 'wppizza');
		$options[$this->settings_page]['order_cancelled'] = esc_html__('Order cancelled', 'wppizza');
		$options[$this->settings_page]['order_cancelled_p'] = esc_html__('This order has been cancelled. Thank you.', 'wppizza');
		$options[$this->settings_page]['order_processing'] = esc_html__('Processing Payment', 'wppizza');
		$options[$this->settings_page]['order_processing_p'] = html_entity_decode(esc_html__('<p>We are processing your payment! This page will automatically refresh and check again in a few seconds. Please wait.....</p><p>If this page appears for more than a minute, please contact us stating the following order details</p>', 'wppizza'));
		$options[$this->settings_page]['order_payment_pending'] = esc_html__('Payment Pending', 'wppizza');
		$options[$this->settings_page]['order_payment_pending_p']  = html_entity_decode(esc_html__('<p>Your payment has not yet been sent to us by your payment provider.</p><p>As soon as we have received your payment, a notification will be sent to you and we will process your order.</p><p><strong>Please note: we have no control over how quickly your provider will settle payments</strong></p><p>This page will refresh periodically, but you can also return to this page using <a href="%s">this link</a> to check back yourself later.</p>', 'wppizza'));
		$options[$this->settings_page]['order_unconfirmed'] = esc_html__('Payment Accepted - Awaiting confirmation', 'wppizza');
		$options[$this->settings_page]['order_unconfirmed_p']  = html_entity_decode(esc_html__('<p>Your payment was accepted but has not yet been confirmed.</p><p>You will receive another email when your payment has been confirmed. Your order can not be processed until final confirmation has been received.</p><p>Please be patient</p>', 'wppizza'));
		$options[$this->settings_page]['order_confirmed'] = esc_html__('Payment Confirmed', 'wppizza');
		$options[$this->settings_page]['order_confirmed_p']  = html_entity_decode(esc_html__('<p>Your payment has been confirmed. Thank you.</p>', 'wppizza'));
		$options[$this->settings_page]['gateway_your_order'] = esc_html__('Your order at', 'wppizza') . ' '. get_bloginfo('name');
		$options[$this->settings_page]['gateway_enter_payment_details'] = esc_html__('Please enter your payment details', 'wppizza');
		$options[$this->settings_page]['order_not_found'] = esc_html__('Sorry, this order can not be found.', 'wppizza');
		$options[$this->settings_page]['label_return_to_shop'] = esc_html__('Return to shop', 'wppizza');
		$options[$this->settings_page]['order_ini_additional_info']='';
		$options[$this->settings_page]['update_profile'] = esc_html__('update my user data with the details above', 'wppizza');
		$options[$this->settings_page]['tips'] = esc_html__('Tips / Gratuities', 'wppizza');
		$options[$this->settings_page]['loginout_have_account'] = esc_html__('already registered ?', 'wppizza');
		$options[$this->settings_page]['register_option_label'] = esc_html__('continue as :', 'wppizza');
		$options[$this->settings_page]['register_option_guest'] = esc_html__('guest', 'wppizza');
		$options[$this->settings_page]['register_option_create_account'] = esc_html__('create account', 'wppizza');
		$options[$this->settings_page]['register_option_create_account_info'] = esc_html__('Please ensure your email address is correct. A password will be emailed to you.', 'wppizza');
		$options[$this->settings_page]['register_option_create_account_error'] = esc_html__('This email address has already been registered. Please either <a href="#login">login</a>, use a different email address or continue as guest.', 'wppizza');
		$options[$this->settings_page]['itemised_label_quantity'] = esc_html__('Qty', 'wppizza');
		$options[$this->settings_page]['itemised_label_article'] = esc_html__('Article', 'wppizza');
		$options[$this->settings_page]['itemised_label_price'] = esc_html__('Price', 'wppizza');
		$options[$this->settings_page]['itemised_label_taxrate'] = esc_html__('Tax Rate', 'wppizza');
		$options[$this->settings_page]['itemised_label_total'] = esc_html__('Total', 'wppizza');
		//$options[$this->settings_page]['order_paid_by'] = esc_html__('Paid By:', 'wppizza');
		//$options[$this->settings_page]['order_email_footer']='';
		$options[$this->settings_page]['spend'] = esc_html__('spend', 'wppizza');
		$options[$this->settings_page]['save'] = esc_html__('save', 'wppizza');
		$options[$this->settings_page]['free_delivery'] = esc_html__('Free Delivery', 'wppizza');
		$options[$this->settings_page]['delivery_charges'] = esc_html__('Delivery Charges', 'wppizza');
		$options[$this->settings_page]['delivery_charges_per_item'] = esc_html__('Delivery Charges Per Item', 'wppizza');
		$options[$this->settings_page]['discount'] = esc_html__('Discount', 'wppizza');
		$options[$this->settings_page]['item_tax_total'] = esc_html__('Sales Tax @ %s', 'wppizza');
		$options[$this->settings_page]['taxes_included'] = esc_html__('Incl. Tax @ %s', 'wppizza');
		$options[$this->settings_page]['handling_charges'] = esc_html__('Handling Charges', 'wppizza');
		$options[$this->settings_page]['shipping_tax'] = esc_html__('Shipping Tax @ %s', 'wppizza');
		$options[$this->settings_page]['tax_total'] = esc_html__('Tax Total', 'wppizza');
		$options[$this->settings_page]['tax_total_included'] = esc_html__('Taxes Included', 'wppizza');
		$options[$this->settings_page]['order_total'] = esc_html__('Total', 'wppizza');
		$options[$this->settings_page]['order_items'] = esc_html__('Your Items', 'wppizza');
		$options[$this->settings_page]['openinghours_closed'] = esc_html__('closed', 'wppizza');
		$options[$this->settings_page]['openinghours_24hrs'] = esc_html__('all day', 'wppizza');
		$options[$this->settings_page]['header_order_print_header']=''.get_bloginfo('name').'';
		$options[$this->settings_page]['header_order_print_shop_address'] = ''.get_bloginfo('name').'';
		$options[$this->settings_page]['header_order_print_customer_label'] = esc_html__('Customer Details / Delivery Address', 'wppizza');
		$options[$this->settings_page]['header_order_print_overview_label'] = esc_html__('Order', 'wppizza');
		$options[$this->settings_page]['header_order_print_itemised_article'] = esc_html__('Article', 'wppizza');
		$options[$this->settings_page]['header_order_print_itemised_price'] = esc_html__('Price', 'wppizza');
		$options[$this->settings_page]['header_order_print_itemised_quantity'] = esc_html__('Qty', 'wppizza');
		$options[$this->settings_page]['common_value_order_delivery'] = esc_html__('For Delivery', 'wppizza');
		$options[$this->settings_page]['common_value_order_pickup'] = esc_html__('For Pickup', 'wppizza');
		$options[$this->settings_page]['common_value_order_cash'] = esc_html__('Cash', 'wppizza');
		$options[$this->settings_page]['common_value_order_credit_card'] = esc_html__('Credit Card', 'wppizza');
		$options[$this->settings_page]['common_label_order_delivery_type'] = esc_html__('Delivery Type :', 'wppizza');
		$options[$this->settings_page]['common_label_order_wp_user_id'] = esc_html__('User ID :', 'wppizza');
		$options[$this->settings_page]['common_label_order_order_id'] = esc_html__('Order ID :', 'wppizza');
		$options[$this->settings_page]['common_label_order_currency'] = esc_html__('Currency :', 'wppizza');
		$options[$this->settings_page]['common_label_order_payment_type'] = esc_html__('Paid By :', 'wppizza');
		$options[$this->settings_page]['common_label_order_delivery_pickup_note'] = esc_html__('Note :', 'wppizza');
		$options[$this->settings_page]['common_label_order_payment_method'] = esc_html__('Payment Method :', 'wppizza');
		$options[$this->settings_page]['common_label_order_order_date'] = esc_html__('Order Date :', 'wppizza');
		$options[$this->settings_page]['common_label_order_transaction_id'] = esc_html__('Transaction Id :', 'wppizza');
		$options[$this->settings_page]['common_label_order_payment_outstanding'] = esc_html__('Payment Due :', 'wppizza');
		$options[$this->settings_page]['common_label_order_payment_unconfirmed'] = esc_html__('Unconfirmed Payment :', 'wppizza');
		$options[$this->settings_page]['common_label_order_payment_confirmed'] = esc_html__('Payment Confirmed :', 'wppizza');
		$options[$this->settings_page]['common_label_order_total'] = esc_html__('Total :', 'wppizza');
		$options[$this->settings_page]['common_label_order_refund'] = esc_html__('Refunded :', 'wppizza');
		$options[$this->settings_page]['templates_label_site'] = esc_html__('Site Details', 'wppizza');
		$options[$this->settings_page]['templates_label_ordervars'] = esc_html__('Overview', 'wppizza');
		$options[$this->settings_page]['templates_label_customer'] = esc_html__('Customer Details', 'wppizza');
		$options[$this->settings_page]['templates_label_order'] = esc_html__('Order Details', 'wppizza');
		$options[$this->settings_page]['templates_label_order_left'] = esc_html__('Qty', 'wppizza');
		$options[$this->settings_page]['templates_label_order_center'] = esc_html__('Article', 'wppizza');
		$options[$this->settings_page]['templates_label_order_right'] = esc_html__('Price', 'wppizza');
		$options[$this->settings_page]['templates_label_summary'] = esc_html__('Summary', 'wppizza');
		$options[$this->settings_page]['templates_user_is_guest'] = esc_html__('Guest', 'wppizza');
		$options[$this->settings_page]['templates_user_is_registered'] = esc_html__('Registered User', 'wppizza');
		$options[$this->settings_page]['templates_footer_note'] = '';
		$options[$this->settings_page]['widget_navigation_dropdown_placeholder'] = esc_html__(' - Our Menu - ', 'wppizza');
		$options[$this->settings_page]['user_profile_label_additional_info'] = esc_html__('Additional Information', 'wppizza');
		$options[$this->settings_page]['gateway_select_label'] = esc_html__('Please select your payment method:', 'wppizza');
		$options[$this->settings_page]['generic_placeholder_select'] = esc_html__('--- Please select ---', 'wppizza');
		$options[$this->settings_page]['generic_placeholder_checkbox_0'] = esc_html__('No', 'wppizza');
		$options[$this->settings_page]['generic_placeholder_checkbox_1'] = esc_html__('Yes', 'wppizza');
		$options[$this->settings_page]['admin_notify_new_order_label'] = esc_html__('New Order(s)', 'wppizza');
		$options[$this->settings_page]['failed_payment_try_again_link'] = esc_html__('Try Again', 'wppizza');
		$options[$this->settings_page]['generic_back_to_site'] = esc_html__('Back to Merchant Site', 'wppizza');
		$options[$this->settings_page]['generic_error_label'] = esc_html__('Error', 'wppizza');
		$options[$this->settings_page]['generic_error_details'] = esc_html__('Error Details', 'wppizza');
		$options[$this->settings_page]['localize_zero_price'] = esc_html__('Free', 'wppizza');
		$options[$this->settings_page]['privacy_terms_accept'] = html_entity_decode(esc_html__('I have read and accept the <a href="%s" target="_blank">Privacy Policy</a>', 'wppizza'));
		$options[$this->settings_page]['privacy_terms_accepted'] = esc_html__('Privacy Policy accepted:', 'wppizza');
		$options[$this->settings_page]['order_history_status_new'] = esc_html__('New', 'wppizza');
		$options[$this->settings_page]['order_history_status_acknowledged'] = esc_html__('Acknowledged', 'wppizza');
		$options[$this->settings_page]['order_history_status_on_hold'] = esc_html__('On Hold', 'wppizza');
		$options[$this->settings_page]['order_history_status_processed'] = esc_html__('Processed', 'wppizza');
		$options[$this->settings_page]['order_history_status_delivered'] = esc_html__('Delivered', 'wppizza');
		$options[$this->settings_page]['order_history_status_rejected'] = esc_html__('Rejected', 'wppizza');
		$options[$this->settings_page]['order_history_status_refunded'] = esc_html__('Refunded', 'wppizza');
		$options[$this->settings_page]['order_history_status_other'] = esc_html__('Other', 'wppizza');
		$options[$this->settings_page]['order_history_status_custom_1'] = '';
		$options[$this->settings_page]['order_history_status_custom_2'] = '';
		$options[$this->settings_page]['order_history_status_custom_3'] = '';
		$options[$this->settings_page]['order_history_status_custom_4'] = '';
		$options[$this->settings_page]['order_history_custom_status_label'] = '';
		$options[$this->settings_page]['order_history_custom_status_options'] = '';

		return $options;
	}


	/*------------------------------------------------------------------------------
	#	[set localization keys wher html is allowed]
	#	array of items to allow html (such as tinymce textareas)
	#	@since 3.0
	#	@return array()
	------------------------------------------------------------------------------*/
	function options_validate_as_html($keys){

			/* add keys */
			$keys[]='thank_you_p';
			$keys[]='order_ini_additional_info';
			$keys[]='jquery_fb_add_to_cart_info';
			$keys[]='register_option_create_account_info';
			$keys[]='register_option_create_account_error';
			$keys[]='order_processing_p';
			$keys[]='order_unconfirmed_p';
			$keys[]='order_payment_pending_p';
			$keys[]='header_order_print_shop_address';
			$keys[]='privacy_terms_accept';
			//$keys[]='order_email_footer';
 			$keys[]='templates_footer_note';

		return $keys;
	}

	/*------------------------------------------------------------------------------
	#	[validate options on save/update - localizations are automatically validated]
	#
	#	@since 3.0
	#	@return array()
	------------------------------------------------------------------------------*/
	//	function options_validate($options){
	//		foreach($input[$this->settings_page] as $key=>$string){
	//			$options[$this->settings_page][$key]=$string;
	//		}
	//	return $options;
	//	}
}
/***************************************************************
*
*	[ini]
*
***************************************************************/
$WPPIZZA_MODULE_LOCALIZATION_COMMON = new WPPIZZA_MODULE_LOCALIZATION_COMMON();
?>