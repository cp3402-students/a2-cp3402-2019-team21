<?php
/**
* miscellaneous custom walkers
*
* @package     WPPIZZA
* @subpackage   miscellaneous custom walkers
* @copyright   Copyright (c) 2015, Oliver Bach
* @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
* @since       3.0
*
*/
if ( ! defined( 'ABSPATH' ) ) exit;/*Exit if accessed directly*/


/*

	custom walker to enable us to select the appropriate dropdown

*/
if (!class_exists('WPPIZZA_Walker_CategoryDropdown')) {/* pluggable */
	
	class WPPIZZA_Walker_CategoryDropdown extends Walker_CategoryDropdown {
		
		function start_el(&$output, $category, $depth = 0, $args = array(), $id = 0) {
			
			$pad = str_repeat(' ', $depth * 3);
			/* get category name */
			$cat_name = apply_filters('list_cats', $category->name, $category);
			// Get the URL of this category
	    	$cat_link = get_category_link( $category->term_id );
			// Get the URL of this category
	    	$cat_slug =  $category->slug ;    	
			// Get the url
			$query_slug = get_query_var('pagename');
	
			$output .= '<option class="'.WPPIZZA_SLUG.'-level-'.$depth.'" value="'.$cat_link.'"';
			
			if ( $cat_slug  == $query_slug ){
				$output .= ' selected="selected"';
			}
			
			$output .='>'. $pad . $cat_name . '';
			
			if ( $args['show_count'] ){
				$output .= '  ('. $category->count .')';
			}
			if (!empty($args['show_last_update'])) {
				$output .= '  ' . gmdate('Y-m-d', $category->last_update_timestamp);
			}
			
			$output .= "</option>\n";
		}
	}
}

/*

	custom walker navigation as list - DUMMY

*/
if (!class_exists('WPPIZZA_Walker_Category')) {/* pluggable */
	class WPPIZZA_Walker_Category extends Walker_Category {
		/* just for arguments sake here in case one wants to do something with it one day */
	}  
}
?>