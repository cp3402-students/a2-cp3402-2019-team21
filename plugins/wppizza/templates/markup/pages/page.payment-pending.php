<?php
if ( ! defined( 'ABSPATH' ) ) exit;/*Exit if accessed directly*/
 /**********************************************************************************************************************************************
 *	$order_formatted = array() : formatted parameters of order
 *	$wppizza_options = array() : all wppizza options, settings, localization strings etc
 *
 *	filters available - allow filtering of markup elements array
 * 	$markup = apply_filters('wppizza_filter_payment_pending_markup', $markup, $order_formatted ); (should return $markup array)
 *
 *
 **********************************************************************************************************************************************/
?>
<?php
		/*
			action
		*/	
		do_action('wppizza_payment_pendingpage', $order_formatted);


		/*
			wrap in div
		*/
		$markup['div_'] = '<div id="'.$id.'" class="'.$class.'">';

			/***************************************************
				"processing payment" - when waiting for confirmation
				from payment processor 
			***************************************************/
			$markup['payment_pending_info'] = '<div id="'.$id_payment_pending_info.'" class="'.$class_payment_pending_info.'" >'.$txt['payment_pending_info'].'</div>';
			
		$markup['_div'] = '</div>';
?>