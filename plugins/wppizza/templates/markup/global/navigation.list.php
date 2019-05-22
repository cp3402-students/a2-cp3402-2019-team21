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
 *	('wppizza_filter_navigation_widget_markup', $markup, $args): filters markup ($markup = array(),$args = array())
 ****************************************************************************************/
?>
<?php
	$markup['ul_']= '<ul id="' . $id . '" class="' . $class . '">';
		$markup['list'] = wp_list_categories( $args );
	$markup['_ul']= '</ul>';
?>