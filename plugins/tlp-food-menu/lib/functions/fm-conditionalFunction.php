<?php

if ( ! function_exists( 'is_food_taxonomy' ) ) {

	/**
	 * is_product_taxonomy - Returns true when viewing a product taxonomy archive.
	 * @return bool
	 */
	function is_food_taxonomy() {
		global $TLPfoodmenu;
		return is_tax( get_object_taxonomies( $TLPfoodmenu->post_type ) );
	}
}
