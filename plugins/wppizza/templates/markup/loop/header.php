<?php
if ( ! defined( 'ABSPATH' ) ) exit;/*Exit if accessed directly*/
 /****************************************************************************************
 *
 * header_class are filterable: wppizza_filter_menu_header_class 
 * header element is filterable , defaults to h1: wppizza_filter_menu_header_element
 *
 * markup is filterable : wppizza_filter_menu_header_markup
 *
 ****************************************************************************************/
?>
<?php
	$markup['header_'] = '<header id="' . $header_id . '" class="' . $header_class['header'] . '">';
		$markup['h1']= '<'.$header_element.' class="' . $header_class['h1'] . '"> ' . $category['name'] . '</'.$header_element.'>';
		/*add description if exists*/
		if ( $category['description'] !='' ){
			$markup['description']= '<div class="' . $header_class['description'] . '">' . $category['description'] . '</div>';
		}
	$markup['_header']= '</header>';
?>