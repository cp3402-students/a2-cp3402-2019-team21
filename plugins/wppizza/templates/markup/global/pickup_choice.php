<?php
if ( ! defined( 'ABSPATH' ) ) exit;/*Exit if accessed directly*/
 /****************************************************************************************
 *
 *
 *
 *	[before]
 *	$class: set/append by using (shortcode) attributes
 *	filters available:
 *	[after]
 *	('wppizza_filter_pickup_choice_widget_markup', $markup, $atts, $is_pickup): filters markup ($markup = array(),$atts = array(),$is_pickup = bool)
 ****************************************************************************************/
?>
<?php

		$markup['div_'] = '<div id="'.$id.'" class="'.$class.'">';

			$markup['option'] = $option;/* using checkbox or using 2-radio toggle */

		$markup['_div'] = '</div>';
?>