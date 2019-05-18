<?php
if ( ! defined( 'ABSPATH' ) ) exit;/*Exit if accessed directly*/
 /**********************************************************************************************************************************************
 *	$order_formatted = array() : formatted parameters of order
 *	$wppizza_options = array() : all wppizza options, settings, localization strings etc
 *
 *	filters available - allow filtering of markup elements array
 * 	$markup = apply_filters('wppizza_filter_pages_thankyou_markup', $markup, $order_formatted ); (should return $markup array)
 *
 *
 **********************************************************************************************************************************************/
?>
<?php
		/*
			action
		*/
		do_action('wppizza_thankyoupage', $order_formatted);


		/*
			wrap in div
		*/
		$markup['div_'] = '<div id="'.$id.'" class="'.$class.'">';

			/***************************************************
				no order found | order does not exist | order failed
			***************************************************/
			if(!$is_order){
				/*
					action
				*/				
				do_action('wppizza_thankyoupage_no_order', $order_formatted);				
				
				$markup['noorder'] = '<p id="'.$id_noorder.'" class="'.$class_noorder.'">'. $txt['thank_you_error'] .'</p>';
				$markup['errors'] = '<div id="'.$id_errors.'" class="'.$class_errors.'" >'. $errors .'</div>';
				$markup['order_page_link'] = $order_page_link;
			}


			/***************************************************
				header - additional text (provided order exists)
			***************************************************/
			if($is_order){
				/*
					action
				*/					
				do_action('wppizza_thankyoupage_is_order', $order_formatted);
								
				$markup['header'] =  $txt['thank_you_p'] ;
			}


			/***************************************************
				showing whole order , not just thank you above
			***************************************************/
			if($show_results){
				/*
					action
				*/	
				do_action('wppizza_thankyoupage_show_results', $order_formatted);


				/**************************************
					general transaction details
				**************************************/
				$markup['transaction_details_'] = '<fieldset class="' . $class_transaction_details . '">';
					/* legend */
					$markup['transaction_details_legend'] = '<legend>'. $txt['your_order'] .'</legend>';
					/* details */
					$markup['transaction_details'] = $transaction_details;
					/* pickup / delivery note */
					$markup['pickup_delivery_note'] = $order_details_pickup_note;

				$markup['_transaction_details'] = '</fieldset>';


				/*************************************
					personal_details
				*************************************/
				$markup['personal_details_'] = '<fieldset class="'. $class_personal_details .'">';
					/* legend */
					$markup['personal_details_legend'] = '<legend>'. $txt['personal_information'] .'</legend>';
					/* details */
					$markup['personal_details'] = $personal_details;

				$markup['_personal_details'] = '</fieldset>';


				/************************************
					order-details : itemised, summary
				************************************/
				$markup['order_details_'] = '<fieldset class="' . $class_order_details . '">';
					/* legend */
					$markup['order_details_legend'] = '<legend>'. $txt['order_details'] .'</legend>';
					/*	order_itemised	*/
					$markup['order_itemised'] = $order_details_itemised;
					/*	order_summary */
					$markup['order_summary'] = $order_details_summary;

				$markup['_order_details'] = '</fieldset>';
			}
		$markup['_div'] = '</div>';
?>