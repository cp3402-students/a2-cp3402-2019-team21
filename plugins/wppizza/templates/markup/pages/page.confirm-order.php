<?php
if ( ! defined( 'ABSPATH' ) ) exit;/*Exit if accessed directly*/
 /****************************************************************************************
 *	$order_formatted = array() : formatted parameters of order
 *	$wppizza_options = array() : all wppizza options, settings, localization strings etc
 *
 *	filters available
 *	[filter after]
 * 	$markup = apply_filters('wppizza_filter_pages_confirm_order_markup', $markup); (should return array)
 *
 ****************************************************************************************/
?>
<?php

		do_action('wppizza_confirmationpage', $order_formatted);

		/*
			wrap in div and form
		*/
		$markup['div_'] = '<div id="' . $id . '" class="' . $class . '">';
			$markup['form_'] = '<form id="' . $id_form . '" class="' . $class_form . '" method="post" accept-charset="' . WPPIZZA_CHARSET .'">';

				/********************************************
					legal / confirmation form inputs if any
				********************************************/
				if($has_inputs){

					do_action('wppizza_confirmationpage_before_inputs', $order_formatted);

					$markup['confirm_'] = '<fieldset class="' . $class_legal . '">';

						/* legend */
						$markup['confirm_legend'] = '<legend>'. $txt['legend_legal'] .'</legend>';/* wppizza -> localization */

						/* confirmation form input fields */
						$markup['confirm_inputs'] = $confirm_inputs;/*uses: markup/global/formfields.inputs.php  */


					$markup['_confirm'] = '</fieldset>';

				}

				/********************************************
					personal_details
				********************************************/
				$markup['personal_details_'] = '<fieldset class="' . $class_personal_details . '">';

					/* legend */
					$markup['personal_details_legend'] = '<legend>'. $txt['legend_personal'] .' <a href="'. $href_orderpage . '">' . $txt['change_user_details'] . '</a></legend>';/* uses: wppizza->settings -> orderpage | wppizza -> localization */

					/* details */
					$markup['personal_details'] = $personal_details;/*uses: markup/global/formfields.values.php  */


				$markup['_personal_details'] = '</fieldset>';


				/********************************************
					payment_method
				********************************************/
				$markup['payment_method_'] = '<fieldset class="' . $class_payment_method . '">';

					/* legend */
					$markup['payment_method_legend'] = '<legend>'. $txt['legend_payment_method'] .' <a href="'. $href_orderpage . '">' . $txt['change_user_details'] . '</a></legend>';/* uses: wppizza->settings -> orderpage | wppizza -> localization */

					/* payment type label */
					$markup['payment_method_label'] = '<label>'. $txt['payment_method'] .'</label>';

					/* payment type value */
					$markup['payment_method'] = '<span>'. $payment_method .'</span>';/* value of payment method selected */


				$markup['_payment_method'] = '</fieldset>';


				/********************************************
					order_details
				********************************************/
				$markup['order_details_'] = '<fieldset id="' . $id_order_details . '" class="' . $class_order_details . '">';

					/* legend */
					$markup['order_details_legend'] = '<legend>'. $txt['legend_order_details'] .' <a href="' . $href_amendorder . '">' . $txt['change_order_details'] . '</a></legend>';/* uses: wppizza->orderform ->Confirmation Page -> amend order link | wppizza -> localization */

					/* pickup / delivery note */
					$markup['order_details_pickup_note'] = $order_details_pickup_note;/* uses: markup/global/pages.pickup_note.php */

					/*	order_itemised	*/
					$markup['order_details_itemised'] = $order_details_itemised;/* uses: markup/order/itemised.php */

					/*	order_summary */
					$markup['order_details_summary'] = $order_details_summary;/* uses: markup/order/summary.php */

					/*	additional info */
					$markup['order_details_additional_info'] = '<div id="' . $id_subtotals_after . '">'. $txt['subtotals_after_additional_info'] .'</div>';/* wppizza -> localization */

				$markup['_order_details'] = '</fieldset>';

				/********************************************
					payment details  - inline payment_details.
					if wppizza_gateways_inline_elements_{gateway_ident} filter in use
					else returns an empty string
					@since 3.6.1
				********************************************/
				$markup['payment_details'] = $payment_details;/* uses: markup/order/payment_details.php */

				/********************************************
					submit button
				********************************************/

				$markup['submit_button'] = $submit_button;/* checkout button. this isn't editable, however you can use class declarations to change styles */


			$markup['_form'] = '</form>';
		$markup['_div'] = '</div>';
?>