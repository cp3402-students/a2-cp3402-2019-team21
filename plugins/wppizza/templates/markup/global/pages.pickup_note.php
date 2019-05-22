<?php
if ( ! defined( 'ABSPATH' ) ) exit;/*Exit if accessed directly*/
 /****************************************************************************************
 *
 *
 *
 *
 *	filters available:
 *	[after]
 *	('wppizza_filter_pages_pickup_note_markup', $markup, $atts): filters markup ($markup = array(),$atts = array())
 ****************************************************************************************/
?>
<?php
	$markup['div_']= '<div class="' . $class . '">';
		$markup['pickup_delivery_message'] = $pickup_delivery_message;
	$markup['_div']= '</div>';
?>