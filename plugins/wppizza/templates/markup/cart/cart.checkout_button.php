<?php
if ( ! defined( 'ABSPATH' ) ) exit;/*Exit if accessed directly*/
 /****************************************************************************************
 *
 *

 *	filters available:
 *
 *	[after]
 *	('wppizza_filter_maincart_checkout_button_markup', $markup): filters markup ($markup = array()))
 ****************************************************************************************/
?>
<?php
	$markup['checkout_button']= '<input type="button" class="'.$class.'" '.$order_page_js_link .' value="'.$txt['place_your_order'].'" />';
?>