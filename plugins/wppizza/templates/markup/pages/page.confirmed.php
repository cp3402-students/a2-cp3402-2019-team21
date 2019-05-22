<?php
if ( ! defined( 'ABSPATH' ) ) exit;/*Exit if accessed directly*/
 /**********************************************************************************************************************************************
 *	$order_formatted = array() : formatted parameters of order
 *	$wppizza_options = array() : all wppizza options, settings, localization strings etc
 *
 *	filters available - allow filtering of markup elements array
 * 	$markup = apply_filters('wppizza_filter_page_confirmed_title', $markup, $order_formatted ); (should return $markup [string])
 * 	$markup = apply_filters('wppizza_filter_page_confirmed_info', $markup, $order_formatted ); (should return $markup [string])
 * 	$markup = apply_filters('wppizza_filter_pages_confirmed_markup', $markup, $order_formatted ); (should return $markup array)
 *
 *
 **********************************************************************************************************************************************/
?>
<?php
		/*
			action
		*/	
		do_action('wppizza_confirmedpage', $order_formatted);


		/*
			wrap in div
		*/
		$markup['div_'] = '<div id="'.$id.'" class="'.$class.'">';
		
			/***************************************************
				text when "order is confirmed but not yet executed " - 
				initially empty, should be written to by plugins filter  
			***************************************************/
			$markup['order_confirmed_info'] = '<div id="'.$id_confirmed.'" class="'.$class_confirmed.'" >'.$txt['order_confirmed_info'].'</div>';
			
		$markup['_div'] = '</div>';
?>