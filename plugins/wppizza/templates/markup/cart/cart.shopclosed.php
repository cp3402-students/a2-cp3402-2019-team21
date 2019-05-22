<?php
if ( ! defined( 'ABSPATH' ) ) exit;/*Exit if accessed directly*/
 /****************************************************************************************
 *
 *

 *	filters available:
 *	[after]
 *	('wppizza_filter_maincart_shopclosed_markup', $markup): filters markup ($markup = array()))
 ****************************************************************************************/
?>
<?php
	$markup['p_'] = '<p class="'.$class.'">';
		$markup['shopclosed'] = ''.$txt['closed'].'';
	$markup['_p'] = '</p>';
?>