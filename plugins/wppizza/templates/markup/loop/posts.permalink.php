<?php
if ( ! defined( 'ABSPATH' ) ) exit;/*Exit if accessed directly*/
/****************************************************************************************
*
*
*
*
****************************************************************************************/
?>
<?php
	$markup['post_permalink_'] = '<span class="' . $post_permalink_class . '">';
		$markup['post_permalink_a_'] = '<a href="' . $post->permalink . '" id="' . $post_permalink_id . '" title="' . $post->the_title_attribute . '">';
			$markup['post_permalink_anchor'] = $post->post_title;
		$markup['_post_permalink_a'] = '</a>';
	$markup['_post_permalink'] = '</span>';
?>