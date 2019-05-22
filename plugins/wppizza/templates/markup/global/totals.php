<?php
if ( ! defined( 'ABSPATH' ) ) exit;/*Exit if accessed directly*/
 /****************************************************************************************
 *
 * 	$class : set in shortcode attributes
 *	[after]
 *	('wppizza_filter_totals_widget_markup', $markup, $atts): filters markup ($markup = array(),$atts = array())
 ****************************************************************************************/
?>
<?php
	$markup['div_'] = '<div id="' . $id . '" class="' . $class . '">';

		$markup['viewcart'] = !empty($viewcart) ? '<span class="'.$viewcart_class.'"></span>' : '';/* $viewcart==false if not used */

		$markup['count_left'] = !empty($count_left) ? '<span class="'.$count_left_class.'"></span>' : '' ;/* $count_left == false if not used or count is displayed to right of total*/

		$markup['total'] = '<span class="'.$total_class.'"></span>';/* order value total or items only,  depending on shortcode*/

		$markup['count_right'] =  !empty($count_right) ? '<span class="'.$count_right_class.'"></span>' : '' ;/* $count_right == false if not used or count is displayed to left of total*/

		$markup['checkoutbutton'] = !empty($checkoutbutton) ? '<span class="'.$checkoutbutton_class.'"></span>' : '' ;/* button if checkout possible, text if shop closed, empty if user cannot check out (minimum order not reached for example)*/

		$markup['cart'] = !empty($cart) ? '<span class="'.$cart_class.'" style="display:none"></span>' : '' ;/* empty if view cart is not used , will be ajax filled with order details*/

	$markup['_div'] = '</div>';
?>