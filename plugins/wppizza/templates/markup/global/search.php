<?php
if ( ! defined( 'ABSPATH' ) ) exit;/*Exit if accessed directly*/
 /****************************************************************************************
 *
 *	$class: set/append by using (shortcode) attributes class=''
 *	filters available:
 *	[after]
 *	('wppizza_filter_search_widget_markup', $markup, $atts): filters markup ($markup = array(),$atts = array())
 * searchform uses a filtered wordpress' get_search_form(); to add relevant hidden input fields
 ****************************************************************************************/
?>
<?php
	$markup['span_']= '<span id="' . $id . '" class="' . $class . '">';
		$markup['searchform'] = $searchform;
	$markup['_span']= '</span>';
?>