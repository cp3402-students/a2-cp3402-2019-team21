<?php
if ( ! defined( 'ABSPATH' ) ) exit;/*Exit if accessed directly*/
 /**********************************************************************************************************************************************
 *	$order_formatted = array() : formatted parameters of order
 *	$wppizza_options = array() : all wppizza options, settings, localization strings etc
 *
 *	filters available - allow filtering of markup elements array
 * 	$markup = apply_filters('wppizza_filter_pages_processing_markup', $markup, $order_formatted ); (should return $markup array)
 *
 *
 **********************************************************************************************************************************************/
?>
<?php
		/*
			action
		*/	
		do_action('wppizza_processingpage', $order_formatted);


		/*
			wrap in div
		*/
		$markup['div_'] = '<div id="'.$id.'" class="'.$class.'">';

			/***************************************************
				"processing payment" - when waiting for ipn responses
			***************************************************/
			$markup['processing_payment_label'] = '<p id="'.$id_processing.'" class="'.$class_processing.'">'.$txt['order_processing'].'</p>';
			$markup['processing_payment_wait'] = '<div id="'.$id_wait.'" class="'.$class_wait.'" >'.$txt['order_processing_p'].'</div>';
			
		$markup['_div'] = '</div>';
?>