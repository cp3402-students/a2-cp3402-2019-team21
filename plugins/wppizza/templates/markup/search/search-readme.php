<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
header('X-Robots-Tag: noindex');/* do not index this page - it's a dummy/instruction file */
/*######################################################################################################
#
#
#
#
#	WPPIZZA MENU ITEMS DISPLAY IN SEARCH RESULTS
#	
#	THIS IS A INSTRUCTION FILE THAT DOES NOTHING BY ITSELF
#	READ THE INSTRUCTIONS BELOW IF YOU WANT TO USE IT
#
#	
#
#
######################################################################################################*/


/******************************************************************************************************

	Purpose:
	
	By default, themes will simply display titles and content/excerpts or similar in their search results
	without the the clickable prices etc you would get when listing wppizza items per category or other 
	wppizza category/menu item related shortcodes.
	
	Please fellow the instructions below if you would like the layout of wppizza menu items that appear 
	in search results to be the same as they are displayed "normally" when using wppizza related shortcodes 

******************************************************************************************************/


/******************************************************************************************************

	Howto:

	a) 	if you have not done so yet , create a directory with the following structure in your child theme: /[absolute]/[path]/[to]/wp-content/themes/[my-child-theme]/wppizza/markup/search/ 	
		(please refer to https://codex.wordpress.org/Child_Themes why you should use child themes and howto create one)


	b) 	locate the search.php file in your theme directory and copy it to the above created directory so the file structure will look like this
		/[absolute]/[path]/[to]/wp-content/themes/[my-child-theme]/wppizza/markup/search/search.php
	
		Note: Some themes do not provide a search.php file. I would suggest you ask the theme developer to create/add one or ask him/her which template part is responsible for displaying search results
		Unfortunately, I will not be able to help you with your particular theme if this is the case.

	c) 	open this file in a suitable text editor


	d) 	the file will  have something similar to  
		
			while ( have_posts() ) : the_post(); 
				get_template_part( 'template-parts/content', 'search' ); 
			endwhile; 
		
		in it

	e) REPLACE 
	
		get_template_part( 'template-parts/content', 'search' );
		
	with 
	
		if(function_exists('wppizza_search_results_get_template_part')){
			wppizza_search_results_get_template_part('template-parts/content', 'search' );
		}else{
			get_template_part( 'template-parts/content', 'search' );
		}	

	
	
	Note: make sure the parameters match. i.e if your original is   	

		get_template_part( 'abcdef', 'xyz' );
	
	 REPLACE it with 	

		if(function_exists('wppizza_search_results_get_template_part')){
			wppizza_search_results_get_template_part('abcdef', 'xyz' );
		}else{
			get_template_part( 'abcdef', 'xyz' );
		}


	f) make sure you keep everything before and including "while ( have_posts() ) : the_post();"  as well as "endwhile;" and everything after intact


	g) save the file


	h) you should now have the same layout for a single wppizza item in your search results as you have when displaying categories (see below)


******************************************************************************************************/


/******************************************************************************************************
	
	QUICK SUMMARY:
		
	#1
	COPY 
		/[your]/[path]/[to]/wp-content/themes/my-theme/search.php 
	TO 
		/[your]/[path]/[to]/wp-content/themes/[my-child-theme]/wppizza/markup/search/search.php
		
		
		
		
	#2	
	REPLACE anything in created/copied search.php like
			
		# - codeblock to replace - #
			while ( have_posts() ) : the_post();
				get_template_part( 'abcdef', 'xyz' );
			endwhile;
		# - codeblock to replace end - #
		
	with

		# - codeblock replace with - #
			if(function_exists('wppizza_search_results_get_template_part')){
				wppizza_search_results_get_template_part('abcdef', 'xyz' );
			}else{
				get_template_part( 'abcdef', 'xyz' );
			}
		# - codeblock replace with end - #

	#3
	keep everything else 

******************************************************************************************************/

/******************************************************************************************************

	
	NOTES:



	the output generated is in fact a shortcode like so :
		
		echo do_shortcode("[wppizza single='9' showadditives='0']");
	
	(where '9' would be the appropriate post_id)
	
	the arguments of which are filterable if you want , using 'wppizza_filter_single_post_arguments' hook like so
	
	add_filter('wppizza_filter_single_post_arguments', 'myprefix_filter_single_wppizza_items', 10, 2);
	function myprefix_allow_comments_for_single_wppizza_items($args, $terms){
		
		/ -  customise arguments as required -/
		
		return $args;	
	}
	see wppizza shortcode documentation as to what arguments exist for single items and/or wordpress codex about how to use filters if you are unfimiliar with filter hooks


******************************************************************************************************/
?>