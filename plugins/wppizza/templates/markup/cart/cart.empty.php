<?php
if ( ! defined( 'ABSPATH' ) ) exit;/*Exit if accessed directly*/
 /****************************************************************************************
 *
 *

 *	filters available:
 *
 *	[after]
 *	('wppizza_filter_maincart_cartempty_markup', $markup): filters markup ($markup = array()))
 ****************************************************************************************/
?>
<?php
	$markup['p_'] = '<p class="'.$class.'">';
		$markup['cartempty'] = $txt['cart_is_empty'];
	$markup['_p'] = '</p>';		
?>