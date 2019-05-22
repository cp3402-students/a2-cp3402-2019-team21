<?php
if ( ! defined( 'ABSPATH' ) ) exit;/*Exit if accessed directly*/
 /**********************************************************************************************************************************************
 *	$order_formatted = array() : formatted parameters of order
 *	$wppizza_options = array() : all wppizza options, settings, localization strings etc
 *
 *	filters available - allow filtering of markup elements array
 * 	$markup = apply_filters('wppizza_filter_page_refunded_info', $markup, $order_formatted ); (should return $markup string)
 * 	$markup = apply_filters('wppizza_filter_pages_refunded_markup', $markup, $order_formatted ); (should return $markup array)
 *
 *
 **********************************************************************************************************************************************/
?>
<?php
		/*
			action
		*/	
		do_action('wppizza_refundedpage', $order_formatted);


		/*
			wrap in div
		*/
		$markup['div_'] = '<div id="'.$id.'" class="'.$class.'">';
		
			/***************************************************
				"order rejected" - 
				initially empty, should be written to by plugins filter  
			***************************************************/
			$markup['order_rejected_info'] = '<div id="'.$id_refunded.'" class="'.$class_refunded.'" >'.$txt['order_refunded_info'].'</div>';
			
		$markup['_div'] = '</div>';
?>