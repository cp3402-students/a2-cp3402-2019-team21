<?php
if ( ! defined( 'ABSPATH' ) ) exit;/*Exit if accessed directly*/
 /****************************************************************************************
 *
 *
 *
 *
 *	filters available:
 *	[before]
 * 	('wppizza_filter_navigation_widget_arguments', $args, $atts);
 *	('wppizza_filter_navigation_widget_class', $class, $atts): filters css class ($class = array())
 *	[after]
 *	('wppizza_filter_navigation_widget_dropdown_markup', $markup, $args): filters markup ($markup = array(),$args = array())
 ****************************************************************************************/
?>
<?php
	/**
		markup array (imploded for output)
	**/
	$markup['div_']= '<div id="' . $id . '" class="' . $class . '">';
		$markup['dropdown'] = wp_dropdown_categories($args);
	$markup['_div']= '</div>';
?>