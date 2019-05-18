<?php
if ( ! defined( 'ABSPATH' ) ) exit;/*Exit if accessed directly*/
 /****************************************************************************************
 *
 *
 *	filters available:
 *	[before]
 * 	apply_filters('wppizza_filter_transaction_details', $keys, $type);

 *	keys set to
 * 	'order_date','payment_type','transaction_id','order_delivered'

*	keys available
* 	'wp_user_id','order_update','order_delivered','notes','payment_gateway','payment_status','user_data',
*	'ip_address','order_date','order_id','payment_due','pickup_delivery','payment_type','payment_method',
*	'transaction_id','total'

 *	[after]
*	see codeblock below
 ****************************************************************************************/
?>
<?php

	/*
		loop through tx details
	*/
	foreach($transaction_details as $key => $transaction_detail){
		/*
			wrap each in div
		*/
		$markup['div_'.$key.'_'] = '<div class="' . $transaction_detail['class'] . '">';

			/*
				label (might be empty for some parameters)
			*/
			$markup['label_'.$key.''] = $transaction_detail['label'];

			/*
				value (wrapped in span)
			*/
			$markup['value_'.$key.''] = $transaction_detail['value'];


		$markup['_div_'.$key.''] = '</div>';
	}
?>