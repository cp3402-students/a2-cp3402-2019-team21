<?php
if ( ! defined( 'ABSPATH' ) ) exit;/*Exit if accessed directly*/
 /**********************************************************************************************************************************************
 *	$order_formatted = array() : formatted parameters of order
 *	$wppizza_options = array() : all wppizza options, settings, localization strings etc
 *
 *	filters available - allow filtering of markup elements array
 * 	$markup = apply_filters('wppizza_filter_pages_unconfirmed_markup', $markup, $order_formatted ); (should return $markup array)
 *
 *
 **********************************************************************************************************************************************/
?>
<?php
		/*
			action
		*/	
		do_action('wppizza_unconfirmedpage', $order_formatted);


		/*
			wrap in div
		*/
		$markup['div_'] = '<div id="'.$id.'" class="'.$class.'">';
		
			/***************************************************
				"unconfirmed payment" -  waiting for ipn response confirmation 
			***************************************************/
			$markup['processing_payment_wait'] = '<div id="'.$id_unconfirmed.'" class="'.$class_unconfirmed.'" >'.$txt['order_unconfirmed_p'].'</div>';
			
		$markup['_div'] = '</div>';
?>