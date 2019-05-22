<?php
if ( ! defined( 'ABSPATH' ) ) exit;/*Exit if accessed directly*/
 /****************************************************************************************
 *
 *

 *	filters available:
 *
 *	[after]
 *	('wppizza_filter_maincart_empty_cart_button_markup', $markup): filters markup ($markup = array()))
 ****************************************************************************************/
?>
<?php
	$markup['empty_cart_button']='<input type="button" class="'.$class.'" value="'.$txt['empty_cart'].'" />';
?>