<?php
if ( ! defined( 'ABSPATH' ) ) exit;/*Exit if accessed directly*/
 /****************************************************************************************
 *
 *
 *
 *
 *
 *
 ****************************************************************************************/
?>
<?php
	/*
		available since WP 4.1, but sets and resets wp_query->max_num_pages.
		(there does not seem to be any other way around this)
		if you really  want to use it, define('WPPIZZA_WP41_POSTS_PAGINATION',1) in your config.php

		by default, the standard pagination will be used
	*/
	if($force_get_the_posts_pagination){
		/* $args are filterable using wppizza_filter_menu_pagination_args */
        $markup['pagination'] = get_the_posts_pagination($args);
	}else{
		/* default */
		$markup['pagination_'] = '<div class="' . $pagination_class . '">';
		  	$markup['pagination_links_'] = '<div class="nav-links">';
		  		$markup['pagination_prev'] = '<div class="alignleft">' . get_previous_posts_link(''.$txt['previous'].'') . '</div>';
		  		$markup['pagination_next'] = '<div class="alignright">' . get_next_posts_link(''.$txt['next'].'', $max_num_pages) . '</div>';
			$markup['_pagination_links'] = '</div>';
		$markup['_pagination'] = '</div>';
	}
?>