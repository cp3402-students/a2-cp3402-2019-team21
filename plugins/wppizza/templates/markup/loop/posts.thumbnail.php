<?php
if ( ! defined( 'ABSPATH' ) ) exit;/*Exit if accessed directly*/
 /****************************************************************************************
 *
 *
 * filters: wppizza_filter_post_thumbnail_class
 * filters: wppizza_filter_post_thumbnail_markup
 *
 ****************************************************************************************/
?>
<?php

	/* has thumbnail */
	if(!empty($has_thumbnail)){
		$markup['post_thumbnail_'] = '<div id="' . $post_thumbnail_id . '" class="' . $post_thumbnail_class['div'] . '">';
			$markup['post_thumbnail'] = $featured_image;
		$markup['_post_thumbnail'] = '</div>';
	}

	/* placeholder */
	if(!empty($has_placeholder)){
		$markup['post_thumbnail_'] = '<div id="' . $post_thumbnail_id . '" class="' . $post_thumbnail_class['div'] . '">';
			$markup['post_thumbnail'] = '<div class="' . $post_thumbnail_class['placeholder'] . '"></div>';
		$markup['_post_thumbnail'] = '</div>';
	}
?>