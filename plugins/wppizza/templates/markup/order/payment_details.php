<?php
if ( ! defined( 'ABSPATH' ) ) exit;/*Exit if accessed directly*/
 /****************************************************************************************
 *
 *
 *	this file only gets loaded if wppizza_gateways_inline_elements_{gateway_ident} filter is being used
 *	filters available:
 *	[after]
 *	('wppizza_filter_order_payment_details_markup', $markup, $gateway_ident): filters markup ($markup = array(),  $gateway_ident = lowercase string)
 ****************************************************************************************/
?>
<?php

	$markup['fieldset_'] = '<fieldset id="'.$payment_details_fieldset_id.'" class="'.$payment_details_fieldset_class.'">';

		$markup['legend'] = '<legend>'.$payment_details_ssl_lock.''.$wppizza_options['localization']['gateway_enter_payment_details'].'</legend>'; /* wppizza -> localization */

		//html and script added via wppizza_gateways_inline_elements_{gateway_ident} filter that gateways should use to add their own cc etc input fields and any inline javascripts
		$markup['payment_details'] = $payment_details;

	$markup['_fieldset']= '</fieldset>';
?>