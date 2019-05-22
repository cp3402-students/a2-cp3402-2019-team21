<?php
if ( ! defined( 'ABSPATH' ) ) exit;/*Exit if accessed directly*/
 /****************************************************************************************
 *
 *

 *	filters available:
 *
 *	[after]
 *	('wppizza_filter_maincart_minimum_order_markup', $markup): filters markup ($markup = array()))
 ****************************************************************************************/
?>
<?php
	$markup['span_'] = '<span class="'.$class.'">';
		$markup['minimum_order'] = ''.$minimum_order_label.'';
	$markup['_span'] = '</span>';		
?>