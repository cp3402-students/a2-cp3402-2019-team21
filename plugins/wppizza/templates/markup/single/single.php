<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
header('X-Robots-Tag: noindex');/* do not index this page - it's a dummy/instruction file */
/*######################################################################################################
#
#
#
#
#	WPPIZZA SINGLE MENU ITEMS DISPLAY
#	
#	THIS IS A DUMMY/INSTRUCTION FILE
#	YOU MUST READ THE INSTRUCTIONS BELOW IF YOU WANT TO USE IT
#
#	
#
#
######################################################################################################*/
?>
<html>
<head>
<tile></tile>
<meta name="robots" content="noindex">
</head>
<body>
<pre style="text-align:center">
		
	<h1>To work with this theme, your theme's single.php must be copied and edited according to the instructions</h1>

	
	<h2>If you do, please open [server-path]/plugins/wppizza/templates/markup/single/single.php in a text editor and follow the instructions within the file</h2>	
	
	<h3>
		Please note: <br>
		This is only relevant if you are actually linking to single menu items (by adding permalinks somewhere or using the WPPizza search widget).<br>
		Typically this is NOT the case, as menu items will be displayed together in categories without links to single items themselves<br>
		If you have come here by clicking on "view" or a similar link from <em>within the administration</em> of your wppizza menu items and the above is applicable (i.e no permalinks, no search widget), you can ignore this.<br>
	</h3>		
	
	
</pre>
</body>
</html>
<?php
/******************************************************************************************************


	THIS FILE DOES NOT DO ANYTHING USEFUL BY ITSELF, PLEASE FOLLOW THE INSTRUCTIONS BELOW !!!
	


	Howto:
	
	a) if you have not done so yet , create a directory with the following structure in your child theme: /[absolute]/[path]/[to]/wp-content/themes/[my-child-theme]/wppizza/markup/single/ 	
	(please refer to https://codex.wordpress.org/Child_Themes why you should use child themes and howto create one)
	
	b) locate the single.php file in your theme directory and copy it to the above created directory so the file structure will look like this
	/[absolute]/[path]/[to]/wp-content/themes/[my-child-theme]/wppizza/markup/single/single.php

	c) open this file in a suitable text editor

	d) the file will  have something like  while ( have_posts() ) : the_post(); .....some stuff ....endwhile; in it

	e) REPLACE this (including the loop - while|endwhile) with the code below (i.e "echo $do_single")
		
	f) make sure you keep everything before "while ( have_posts() )..." and after "endwhile;"

	g) save the file

	h) you should now have the same layout for a single item as you have when displaying categories (see below)

	

	----------- QUICK SUMMARY -----------------
		
	REPLACE anything in created/copied single.php like
			
		# - codeblock to replace - #
			while ( have_posts() ) : the_post();
			if(is_single()){
				.....
			}
			endwhile;
		# - codeblock to replace end - #
		
	with

		# - codeblock replace with - #
			echo $do_single;
		# - codeblock replace with end - #


	keep what's before and after the loop 

	----------- QUICK SUMMARY END -----------------





	/---------------------------------- ADDING SIDEBARS WITHIN LOOP (DEPENDING ON THEME) ----------------------------------/

	Some themes use/set sidebars WITHIN the while .... endwhile loop, if that is the case
	add the codesnippets that deal with them in your theme before and/or after the "echo $do_single;"
	you might also have to keep surrounding div elements.
	So - as an example - this might look like this afterwards
	
	
		<!-- Two Columns -->
		<div class="row two-columns">
		
		    <!-- Main Column -->
		    <?php if($theme_posts_sidebar == 1) { ?>
		    <div class="main-column <?php if($theme_sidebar_size == 0) { ?> col-md-8 <?php } else { ?> col-md-9 <?php } ?>">
		    <?php } else { ?>
		    <div class="main-column col-md-12">
		    <?php } ?>	
		    
				<!-- Post Content -->
					<?php echo $do_single; ?>
    			<!-- /Post Content -->
    		</div>
    		
    		<!-- /Main Column -->
    
    
		    <?php if($theme_posts_sidebar == 1)  get_sidebar();  ?>
		    	
		</div>
		<!-- /Two Columns -->	
	


	/---------------------------------- ADDING COMMENTS ----------------------------------/
	 
	To be able to use comments they first of all must enabled them on a per menu item basis - or generally in WP->Settings->Discussion
	Then simply also add the codeblock your theme's single.php file provides underneath the echo $do_single; referred to above. 
	Typically this will then look something like this.

		echo $do_single;
        if ( comments_open() ) {
        	echo comments_template();
        }

	Where "if ( comments_open() ) ....etc " should be copied code from however your theme displays comments 



	/---------------------------------- NOTES ----------------------------------/

	$do_single is in fact a :
		
	do_shortcode("[ ---- shortcode arguments---- ]")
	
	the arguments of which are filterable if you want , using 'wppizza_filter_single_post_arguments' hook like so
	
	add_filter('wppizza_filter_single_post_arguments', 'myprefix_filter_single_wppizza_items', 10, 2);
	function myprefix_allow_comments_for_single_wppizza_items($args, $terms){
		
		/ -  customise arguments as required -/
		
		return $args;	
	}
	see wppizza shortcode documentation as to what arguments exist for single items and/or wordpress codex about how to use filters if you are unfimiliar with filter hooks
?>