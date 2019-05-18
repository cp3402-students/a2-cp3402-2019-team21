<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/********************************************************************************************************
*
*
*	[WPPizza - Main Wrapper Template]
*
*	you should make a copy of the archive.php, page.php or index.php
*	file in your theme folder into a subdirectory [mytheme/mytheme-child]/wppizza/markup/loop/
*	rename it to wrapper.php so the whole structure is like so
*
*	[mytheme|mytheme-child directory]/wppizza/markup/loop/theme-wrapper.php
*
*	then copy the bit marked below into that copied page
*	, REPLACING the WHOLE loop . ie the bit that will look something like (or similar) :
*	-------------
*	while ( have_posts() ) : the_post();
*
*		//stuff inside the loop
*
*	endwhile; // end of the loop.
*	------------------
*
*	to make the thing play nice with your theme......
*
********************************************************************************************************/
get_header();

?>
	<div id="primary" class="site-content">
		<div id="content" role="main">
<?php

/******************************************************************************
*
*	[if you want to include breadcrumbs use the following]
*
*****************************************************************************/
/*
	// default arguments are as follows
	$args = array();
 	$args['here_text']        = __( 'You are currently here!' );
    $args['home_link']        = home_url('/');
    $args['home_text']        = __( 'Home' );
    $args['link_before']      = '<span typeof="v:Breadcrumb">';
    $args['link_after']       = '</span>';
    $args['link_attr']        = ' rel="v:url" property="v:title"';
    $args['delimiter']        = ' &raquo; ';              // Delimiter between crumbs
    $args['before']           = '<span class="current">'; // Tag before the current crumb
    $args['after']            = '</span>';// Tag after the current crumb    

	// output the breadcrumbs according to arguments
	echo wppizza_breadcrumbs($args);
*/


/******************************************************************************
*
*	[copy from here .....]
*
*****************************************************************************/

	/**
		Note:
		$do_wppizza_loop is in fact a :

		do_shortcode("[ ---- shortcode arguments---- ]")

		the arguments of which are filterable if you want , using 'wppizza_filter_wrapper_arguments' hook
		see wppizza shortcode documentation as to what arguments exist and/or wordpress codex about how to use filters if you are unfimiliar with filter hooks
	**/
	echo $do_wppizza_loop;

/******************************************************************************
*
*	[...........copy to here]
*
*****************************************************************************/
?>
		</div><!-- #content -->
	</div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>