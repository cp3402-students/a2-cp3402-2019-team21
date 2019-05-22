<?php
if ( ! defined( 'ABSPATH' ) ) exit;/*Exit if accessed directly*/
 /****************************************************************************************
 *
 *

 *	filters available:
 *
 *	[after]
 *	('wppizza_filter_maincart_container_markup', $markup): filters markup ($markup = array()))
 ****************************************************************************************/
?>
<?php
		$markup['div_'] = '<div class="'.$container_class.'">';

			/* shop closed */
			if(!wppizza_is_shop_open()){
				$markup['is_closed']= $is_closed;
			}

			/* shop open */
			if(wppizza_is_shop_open()){
				/*
					convenience identifier for js alerts, although this is also checked serverside before submission
				*/
				$markup['is_open'] = $is_open;

				/*
					cart empty
				*/
				$markup['cart_empty']= $cart_empty;

				/*
					itemised items table
				*/
			  	$markup['items']= $items;

				/*
					subtotals/summary table
				*/
				$markup['summary'] = $summary;

				/*
					pickup / delivery note
				*/
				$markup['pickup_note'] = $pickup_note;

				/*
					minimum order required text
				*/
				$markup['minimum_order'] = $minimum_order;


				$markup['buttons_'] = '<div class="'.$button_class.'">';
					/*
						checkout button
					*/
					$markup['checkout_button'] = $checkout_button;
					/*
						empty_cart button
					*/
					$markup['empty_cart_button'] = $empty_cart_button;

				$markup['_buttons'] = '</div>';


			}

		$markup['_div'] = '</div>';
?>