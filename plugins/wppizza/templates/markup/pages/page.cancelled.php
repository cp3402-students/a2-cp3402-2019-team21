<?php
if ( ! defined( 'ABSPATH' ) ) exit;/*Exit if accessed directly*/
 /**********************************************************************************************************************************************
 *	$order_formatted = array() : session (if directlink from gateway) or order parameters of order and user
 *	$wppizza_options = array() : all wppizza options, settings, localization strings etc
 *
 *	filters available - allow filtering of markup elements array
 * 	$markup = apply_filters('wppizza_filter_pages_cancelled_markup', $markup, $order_formatted ); (should return $markup array)
 *
 *
 **********************************************************************************************************************************************/
?>
<?php
		do_action('wppizza_cancelledpage', $order_formatted, $cancel_type);

		/*
			wrap in div
		*/
		$markup['div_'] = '<div id="'.$id.'" class="'.$class.'">';

				/***************************************************
				 cancelled text , or order_not_found if trying to cancel
				 a non existent (or already processed) order
				***************************************************/
				$markup['cancelled'] =  '<p>'.$cancel_text.'</p>' ;

				/***************************************************
				 return to shop
				***************************************************/
				$markup['return_link'] =  $return_link ;


		$markup['_div'] = '</div>';

?>