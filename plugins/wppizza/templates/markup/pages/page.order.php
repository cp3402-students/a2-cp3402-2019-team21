<?php
if ( ! defined( 'ABSPATH' ) ) exit;/*Exit if accessed directly*/
 /**********************************************************************************************************************************************
 *	$order_formatted = array() : formatted parameters of order
 *	$wppizza_options = array() : all wppizza options, settings, localization strings etc
 *
 *	filters available - allow filtering of markup elements array
 * 	$markup = apply_filters('wppizza_filter_pages_order_markup', $markup, $order_formatted ); (should return $markup array)
 *
 *
 **********************************************************************************************************************************************/
?>
<?php

	/*
		action hook
	*/
	do_action('wppizza_orderpage', $order_formatted);


	/*
		wrapper start
	*/
	$markup['div_'] = '<div id="'.$id.'" class="'.$class.'">';


		/******************************************************************************
		*
		*
		*	shop is closed
		*
		*
		******************************************************************************/
		if(!$shop_open){
			/*
				action hook
			*/
			do_action('wppizza_orderpage_if_shop_closed', $order_formatted);

			$markup['shop_closed_'] = '<fieldset class="' . $class_shop_closed . '">';
				$markup['shop_closed'] = '<p>'.$txt['closed'].'</p>';/* wppizza -> localization */
			$markup['_shop_closed'] = '</fieldset>';
		}



		/******************************************************************************
		*
		*
		*	shop is open
		*
		*
		******************************************************************************/
		if($shop_open){
			/*
				action hook
			*/
			do_action('wppizza_orderpage_if_shop_open', $order_formatted);


			/*************************************************************
			*
			*	no items in cart, just show "cart is empty";
			*
			**************************************************************/
			if($cart_empty){
				/*
					action hook
				*/
				do_action('wppizza_orderpage_cart_empty', $order_formatted);

				$markup['cart_empty_'] = '<fieldset class="' . $class_cart_empty . '">';

					$markup['cart_empty_legend'] = '<legend>'.$txt['your_order'].'</legend>'; /* wppizza -> localization */

					$markup['cart_empty'] = '<p>'.$txt['cart_is_empty'].'</p>'; /* wppizza -> localization */

				$markup['_cart_empty'] = '</fieldset>';
			}


			/*************************************************************
			*
			*	we do have things in cart...display what needs displaying
			*
			**************************************************************/
			if(!$cart_empty){
				/*
					action hook
				*/
				do_action('wppizza_orderpage_cart_not_empty', $order_formatted);


				/*****************************************
					some additional text before form (if used)
				*****************************************/
				$markup['additional_text'] = $txt['order_ini_additional_info'] ; /* wppizza -> localization */

				/**\**\**\**\**\**\**\**\**\**\**\**\**
				*	dedicated (unused) filter for easy insertion
				**\**\**\**\**\**\**\**\**\**\**\**\**/
				$markup['order_page_before_login_form'] = apply_filters('wppizza_filter_orderpage_before_login_form', '', $order_formatted);


				/*****************************************
					login form (will be empty if already logged in or "anyone can register" is dis-abled in WP->settings)
				*****************************************/
				$markup['login_form'] = $login_form;/* uses: markup/global/login.php */


				/**\**\**\**\**\**\**\**\**\**\**\**\**
				*	dedicated (unused) filter for easy insertion
				**\**\**\**\**\**\**\**\**\**\**\**\**/
				$markup['order_page_before_orderform'] = apply_filters('wppizza_filter_orderpage_before_orderform', '', $order_formatted);

				/*
					form start
				*/
				$markup['form_'] = '<form id="' . $formid . '" method="post" accept-charset="' . WPPIZZA_CHARSET .'">';


					/*****************************************
						order_details
					*****************************************/
					$markup['order_details_'] = '<fieldset id="' . $order_fieldset_id . '" class="' . $order_fieldset_class . '">';

						$markup['order_details_legend']	= '<legend>'. $txt['your_order'] .'</legend>'; /* wppizza -> localization */

						$markup['order_details_itemised'] = $order_details_itemised ; /* uses: markup/order/itemised.php */

						$markup['order_details_summary'] = $order_details_summary ;/* uses: markup/order/summary.php */

						$markup['order_details_pickup_note'] = $order_details_pickup_note ;/* uses: markup/global/pages.pickup_note.php */

						$markup['order_details_pickup_choices'] = $order_details_pickup_choices ;/*uses: markup/global/pickup_choice.php  */

					$markup['_order_details'] = '</fieldset>';

					/**\**\**\**\**\**\**\**\**\**\**\**\**
					*	dedicated (unused) filter for easy insertion
					**\**\**\**\**\**\**\**\**\**\**\**\**/
					$markup['order_page_after_order_details'] = apply_filters('wppizza_filter_orderpage_after_order_details', '', $order_formatted);

					/*****************************************
						personal_details
					*****************************************/
					$markup['personal_details_'] = '<fieldset id="' . $personal_fieldset_id . '" class="' . $personal_fieldset_class . '">';

						$markup['personal_details_legend'] = '<legend>'. $txt['order_form_legend'] .'</legend>'; /* wppizza -> localization */

						$markup['personal_details'] = $personal_details ; /*uses: markup/global/formfields.inputs.php  */

						$markup['user_profile'] = $user_profile ; /*uses: markup/global/profile.update.php or uses: markup/global/profile.register.php  or empty string if registration is not enabled*/

					$markup['_personal_details'] = '</fieldset>';

					/**\**\**\**\**\**\**\**\**\**\**\**\**
					*	dedicated (unused) filter for easy insertion
					**\**\**\**\**\**\**\**\**\**\**\**\**/
					$markup['order_page_after_personal_details'] = apply_filters('wppizza_filter_orderpage_after_personal_details', '', $order_formatted);

					/*****************************************
						gateways/payment methods buttons
					*****************************************/
					if($can_checkout){
					/*
						action hook
					*/
					do_action('wppizza_orderpage_can_checkout', $order_formatted);

						/** single gateways use hidden input, so let's omit the fieldset */
						if(count($wppizza_options['gateways'])>1){
							$markup['payment_methods_'] = '<fieldset id="'.$payment_methods_fieldset_id.'" class="'.$payment_methods_fieldset_class.'">';
								$markup['payment_methods_legend'] = '<legend>'.$txt['gateway_select_label'].'</legend>'; /* wppizza -> localization */
						}

							$markup['payment_methods'] = $payment_methods; /* markup of gateway buttons/choices. other than the fieldset above this isn't editable, however you could use class declarations to change styles */

						/** single gateways use hidden input, so let's omit the fieldset */
						if(count($wppizza_options['gateways'])>1){
							$markup['_payment_methods'] = '</fieldset>';
						}
					}

					/****************************************
						inline payment details
						if wppizza_gateways_inline_elements_{gateway_ident} filter is in
						use by a gateway
						returns empty string if confirmation page is used
						or checkout not possible
						@since 3.6.1
					****************************************/
					if($can_checkout){
						$markup['payment_details'] = $payment_details;/* uses: markup/order/payment_details.php */
					}

					/**\**\**\**\**\**\**\**\**\**\**\**\**
					*	dedicated (unused) filter for easy insertion
					**\**\**\**\**\**\**\**\**\**\**\**\**/
					$markup['order_page_after_payment_details'] = apply_filters('wppizza_filter_orderpage_after_payment_details', '', $order_formatted);

					/****************************************
						submit button provided one can actually checkout
					****************************************/
					if($can_checkout){
						$markup['submit_button'] = $submit_button ;/* checkout button. this isn't editable, however you can use class declarations to change styles */
					}

					/****************************************
						minimum order required text (if applicable)
					****************************************/
					$markup['minimum_order'] = $minimum_order ; /* simple text string, made up of various parts of wppizza -> localization depending on order values*/

				/*
					form end
				*/
				$markup['_form'] = '</form>';
				
				/**\**\**\**\**\**\**\**\**\**\**\**\**
				*	dedicated (unused) filter for easy insertion
				**\**\**\**\**\**\**\**\**\**\**\**\**/
				$markup['order_page_after_orderform'] = apply_filters('wppizza_filter_orderpage_after_orderform', '', $order_formatted);				
			}
		}



	/*
		wrapper end
	*/
	$markup['_div'] = '</div>';

?>