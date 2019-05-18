<?php
if ( ! defined( 'ABSPATH' ) ) exit;/*Exit if accessed directly*/
 /****************************************************************************************
 *
 *

 *	filters available:
 *	[after]
 *	('wppizza_filter_maincart_pickup_note_markup', $markup): filters markup ($markup = array()))
 ****************************************************************************************/
?>
<?php
	$markup['p_'] = '<p class="'.$class.'">';
		$markup['pickup_note'] = ''.$txt['order_self_pickup_cart'].'';
	$markup['_p'] = '</p>';		
?>