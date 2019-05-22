<?php
if ( ! defined( 'ABSPATH' ) ) exit;/*Exit if accessed directly*/
 /****************************************************************************************
 *
 *
 *
 *
 *
 *
 ****************************************************************************************/
?>
<?php

do_action('wppizza_purchasehistory', $purchase_history);

	/*************************************
		wrapper div start
	*************************************/
	$markup['div_'] = '<div id="'.$id.'" class="'.$class.'">';


		/*****************************************
			login form (will be empty if already logged in or "anyone can register" is dis-abled in WP->settings)
		*****************************************/
		$markup['login_form'] = $login_form;/* uses: markup/global/login.php */


		/*****************************************
			empty , but filterable
			to add additional info before orders
			filter should return an array
			empty string if not logged in
		*****************************************/
		$markup['before_orders'] = $is_logged_in ? implode('', apply_filters('wppizza_filter_pages_purchasehistory_before_orders', array(), $purchase_history, $user_id)) : '' ;/* allow markup before orders - logged in only */

		/*****************************************
			no order -  only if logged in and there are no orders
		*****************************************/
		if($no_orders){
			do_action('wppizza_purchasehistory_noorders', $purchase_history);
			$markup['no_orders'] = '<p id="' . $id_noorders . '" class="' . $class_noorders . '">'. $txt['history_no_previous_orders'] .'</p>';
		}else{
			if($is_logged_in){
			do_action('wppizza_purchasehistory_hasorders', $purchase_history);
			}
		}

		/*****************************************
			loop through each order in purchase history
			there will only be an array of orders if logged in
			though it might be empty too if no orders exist yet of course
		*****************************************/
		foreach($purchase_history as $key=>$order){

			do_action('wppizza_purchasehistory_each_order', $order);

			/************************************
				wrap each order into its own div
			************************************/
			$markup['div_'.$key.'_'] = '<div id="' . $order['id'] . '" class="' . $class_order . '">';

				/************************************
					fieldset
				************************************/
				$markup['fieldset_'.$key.'_'] = '<fieldset class="' . $class_fieldset . '">';


					/************************************
						legend toggles visibility orderdetails|tx details
					************************************/
					$markup['legend_'.$key.'']  = '<legend><span class="' . $class_legend_span . '"></span>'. $txt['history_legend_order_details'] .' / '. $txt['history_legend_transaction_details'] .'</legend>';/* wppizza -> localization */



					/************************************
						order-details : itemised, summary (visibility toggled)
					************************************/
					$markup['order_details_'.$key.'_'] = '<div class="' . $class_order_details . '">';

						/*	order_itemised	*/
						$markup['order_itemised_'.$key.''] = $order['order_itemised'];/* uses: markup/order/itemised.php */

						/*	order_summary */
						$markup['order_summary_'.$key.''] = $order['order_summary'];/* uses: markup/order/summary.php */

					$markup['_order_details_'.$key.''] = '</div>';



					/************************************
						general transaction details (visibility toggled)
					************************************/
					$markup['transaction_details_'.$key.'_'] = '<div class="' . $class_transaction_details . '">';

						/* tx details */
						$markup['transaction_details_'.$key.''] = $order['transaction_details'];/* uses: markup/order/transaction_details.php */


					$markup['_transaction_details_'.$key.''] = '</div>';



				/************************************
					end fieldset | div
				*************************************/
				$markup['_fieldset_'.$key.''] = '</fieldset>';
			$markup['_div_'.$key.''] = '</div>';

		}
		/************************************
			end loop
		*************************************/


		/*****************************************
			 pagination: empty if not logged in,
			 no orders exist or no pagination required
		*****************************************/
		$markup['pagination'] = $pagination;



		/*****************************************
			empty , but filterable
			to add additional info after orders
			filter should return an array
			empty string if not logged in
		*****************************************/
		$markup['after_orders'] = $is_logged_in ? implode('', apply_filters('wppizza_filter_pages_purchasehistory_after_orders', array(), $purchase_history, $user_id)) : '' ;/* allow markup before orders - logged in only */


	/************************************
		end wrapper
	*************************************/
	$markup['_div'] = '</div>';

?>