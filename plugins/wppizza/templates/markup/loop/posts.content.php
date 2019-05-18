<?php
if ( ! defined( 'ABSPATH' ) ) exit;/*Exit if accessed directly*/
 /****************************************************************************************
 *
 *
 *
 * filters: wppizza_filter_post_content_class
 * filters: wppizza_filter_post_content
 * filters: wppizza_filter_post_content_markup
 *
 ****************************************************************************************/
?>
<?php
	$markup['post_content_'] = '<'.$post_content_element.' id="' . $post_content_id . '" class="' . $post_content_class . '">';
		$markup['post_content'] = $post->post_content;
	$markup['_post_content'] = '</'.$post_content_element.'>';
?>