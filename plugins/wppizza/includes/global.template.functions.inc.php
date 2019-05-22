<?php if ( ! defined( 'ABSPATH' ) ) exit;/*Exit if accessed directly*/ ?>
<?php
/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*
*
*
*	template functions
*
*
*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\*/

/*********************************************************
*	[search results template parts ]
*********************************************************/
function wppizza_search_results_get_template_part($slug, $name){
	global $post;
	/* 
		use wppizza layout of a menu item in search results 
		if it's a wppizza post type ...
	*/
	if(is_object($post) && !empty($post->post_type) && $post->post_type == WPPIZZA_POST_TYPE && is_search()){
			$terms = wp_get_post_terms( $post->ID, WPPIZZA_TAXONOMY);
			$terms = array_shift( $terms );// get first category this belongs to as - in search results - we have no idea which one should be set 
	
			/**
				shortcode arguments
			**/
			$sc_args = array();
			
			/* simply create a single shortcode */
			$sc_args['single'] = 'single="'.$post->ID.'"';
			
			/* force to omit additives (or it will show additives below each and every item that has additives) */
			$sc_args['showadditives'] = 'showadditives="0"';			
				
			/* filter -> implode arguments */
			$sc_args = apply_filters('wppizza_filter_single_post_arguments', $sc_args, $terms);// allow filtering of arguments consistent with single item display
			$sc_args = ''.WPPIZZA_SLUG.' '.implode(' ', $sc_args);
				
			/** create the shortcode **/
			$do_single = do_shortcode("[" . $sc_args . "]");
		
		/* simply print shortcode output */
		echo $do_single;		
	}else{
		/* ... else use the get_template_part provided by theme */
		get_template_part( $slug, $name );
	}
}

/*********************************************************
*	[wppizza breadcrumbs]
* 	slightly adapted from 
*	https://wordpress.stackexchange.com/questions/204738/breadcrumbs-with-custom-post-type-without-plugin
*********************************************************/
if(!function_exists('wppizza_breadcrumbs')){
function wppizza_breadcrumbs($args = array()){
    // Set variables for later use
    $here_text        = isset($args['here_text']) 	?  $args['here_text'] : __( 'You are currently here!' );
    $home_link        = isset($args['home_link']) 	?  $args['home_link'] : home_url('/');
    $home_text        = isset($args['home_text']) 	?  $args['home_text'] : __( 'Home' );
    $link_before      = isset($args['link_before']) ?  $args['link_before'] : '<span typeof="v:Breadcrumb">';
    $link_after       = isset($args['link_after']) 	?  $args['link_after'] : '</span>';
    $link_attr        = isset($args['link_attr']) 	?  $args['link_attr'] : ' rel="v:url" property="v:title"';
    $delimiter        = isset($args['delimiter']) 	?  $args['delimiter'] : ' &raquo; ';              // Delimiter between crumbs
    $before           = isset($args['before']) 		?  $args['before'] : '<span class="current">'; // Tag before the current crumb
    $after            = isset($args['after']) 		?  $args['after'] : '</span>';// Tag after the current crumb    
    
    // construct link
    $link             = $link_before . '<a' . $link_attr . ' href="%1$s">%2$s</a>' . $link_after;
    $page_addon       = '';                       // Adds the page number if the query is paged
    $breadcrumb_trail = '';
    $category_links   = '';

    /** 
     * Set our own $wp_the_query variable. Do not use the global variable version due to 
     * reliability
     */
    $wp_the_query   = $GLOBALS['wp_the_query'];
    $queried_object = $wp_the_query->get_queried_object();

    // Handle single post requests which includes single pages, posts and attatchments
    if ( is_singular() ) {
        /** 
         * Set our own $post variable. Do not use the global variable version due to 
         * reliability. We will set $post_object variable to $GLOBALS['wp_the_query']
         */
        $post_object = sanitize_post( $queried_object );

        // Set variables 
        $title          = apply_filters( 'the_title', $post_object->post_title );
        $parent         = $post_object->post_parent;
        $post_type      = $post_object->post_type;
        $post_id        = $post_object->ID;
        $post_link      = $before . $title . $after;
        $parent_string  = '';
        $post_type_link = '';

        if ( 'post' === $post_type ){
            // Get the post categories
            $categories = get_the_category( $post_id );
            if ( $categories ) {
                // Lets grab the first category
                $category  = $categories[0];

                $category_links = get_category_parents( $category, true, $delimiter );
                $category_links = str_replace( '<a',   $link_before . '<a' . $link_attr, $category_links );
                $category_links = str_replace( '</a>', '</a>' . $link_after,             $category_links );
            }
        }

        if ( !in_array( $post_type, array('post', 'page', 'attachment')) ){
            $post_type_object = get_post_type_object( $post_type );
            $archive_link     = esc_url( get_post_type_archive_link( $post_type ) );

            $post_type_link   = sprintf( $link, $archive_link, $post_type_object->labels->singular_name );
        }

        // Get post parents if $parent !== 0
        if ( 0 !== $parent ) {
            $parent_links = array();
            while ( $parent ) {
                $post_parent = get_post( $parent );

                $parent_links[] = sprintf( $link, esc_url( get_permalink( $post_parent->ID ) ), get_the_title( $post_parent->ID ) );

                $parent = $post_parent->post_parent;
            }

            $parent_links = array_reverse( $parent_links );

            $parent_string = implode( $delimiter, $parent_links );
        }

        // Lets build the breadcrumb trail
        if ( $parent_string ) {
            $breadcrumb_trail = $parent_string . $delimiter . $post_link;
        } else {
            $breadcrumb_trail = $post_link;
        }

        if ( $post_type_link )
            $breadcrumb_trail = $post_type_link . $delimiter . $breadcrumb_trail;

        if ( $category_links )
            $breadcrumb_trail = $category_links . $breadcrumb_trail;
    }

    // Handle archives which includes category-, tag-, taxonomy-, date-, custom post type archives and author archives
    if( is_archive() ){
        if (    is_category()
             || is_tag()
             || is_tax()
        ) {
            // Set the variables for this section
            $term_object        = get_term( $queried_object );
            $taxonomy           = $term_object->taxonomy;
            $term_id            = $term_object->term_id;
            $term_name          = $term_object->name;
            $term_parent        = $term_object->parent;
            $taxonomy_object    = get_taxonomy( $taxonomy );
            $current_term_link  = $before . $taxonomy_object->labels->singular_name . ': ' . $term_name . $after;
            $parent_term_string = '';

            if ( 0 !== $term_parent )
            {
                // Get all the current term ancestors
                $parent_term_links = array();
                while ( $term_parent ) {
                    $term = get_term( $term_parent, $taxonomy );

                    $parent_term_links[] = sprintf( $link, esc_url( get_term_link( $term ) ), $term->name );

                    $term_parent = $term->parent;
                }

                $parent_term_links  = array_reverse( $parent_term_links );
                $parent_term_string = implode( $delimiter, $parent_term_links );
            }

            if ( $parent_term_string ) {
                $breadcrumb_trail = $parent_term_string . $delimiter . $current_term_link;
            } else {
                $breadcrumb_trail = $current_term_link;
            }

        } elseif ( is_author() ) {

            $breadcrumb_trail = __( 'Author archive for ') .  $before . $queried_object->data->display_name . $after;

        } elseif ( is_date() ) {
            // Set default variables
            $year     = $wp_the_query->query_vars['year'];
            $monthnum = $wp_the_query->query_vars['monthnum'];
            $day      = $wp_the_query->query_vars['day'];

            // Get the month name if $monthnum has a value
            if ( $monthnum ) {
                $date_time  = DateTime::createFromFormat( '!m', $monthnum );
                $month_name = $date_time->format( 'F' );
            }

            if ( is_year() ) {

                $breadcrumb_trail = $before . $year . $after;

            } elseif( is_month() ) {

                $year_link        = sprintf( $link, esc_url( get_year_link( $year ) ), $year );

                $breadcrumb_trail = $year_link . $delimiter . $before . $month_name . $after;

            } elseif( is_day() ) {

                $year_link        = sprintf( $link, esc_url( get_year_link( $year ) ),             $year       );
                $month_link       = sprintf( $link, esc_url( get_month_link( $year, $monthnum ) ), $month_name );

                $breadcrumb_trail = $year_link . $delimiter . $month_link . $delimiter . $before . $day . $after;
            }

        } elseif ( is_post_type_archive() ) {

            $post_type        = $wp_the_query->query_vars['post_type'];
            $post_type_object = get_post_type_object( $post_type );

            $breadcrumb_trail = $before . $post_type_object->labels->singular_name . $after;

        }
    }   

    // Handle the search page
    if ( is_search() ) {
        $breadcrumb_trail = __( 'Search query for: ' ) . $before . get_search_query() . $after;
    }

    // Handle 404's
    if ( is_404() ) {
        $breadcrumb_trail = $before . __( 'Error 404' ) . $after;
    }

    // Handle paged pages
    if ( is_paged() ) {
        $current_page = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : get_query_var( 'page' );
        $page_addon   = $before . sprintf( __( ' ( Page %s )' ), number_format_i18n( $current_page ) ) . $after;
    }

    $breadcrumb_output_link  = '';
    $breadcrumb_output_link .= '<div class="breadcrumb">';
    if (    is_home()
         || is_front_page()
    ) {
        // Do not show breadcrumbs on page one of home and frontpage
        if ( is_paged() ) {
            $breadcrumb_output_link .= $here_text . $delimiter;
            $breadcrumb_output_link .= '<a href="' . $home_link . '">' . $home_text . '</a>';
            $breadcrumb_output_link .= $page_addon;
        }
    } else {
        $breadcrumb_output_link .= $here_text . $delimiter;
        $breadcrumb_output_link .= '<a href="' . $home_link . '" rel="v:url" property="v:title">' . $home_text . '</a>';
        $breadcrumb_output_link .= $delimiter;
        $breadcrumb_output_link .= $breadcrumb_trail;
        $breadcrumb_output_link .= $page_addon;
    }
    $breadcrumb_output_link .= '</div><!-- .breadcrumbs -->';

return $breadcrumb_output_link;
}}
/*********************************************************
*	[get email skeleton template by id or shop/customer
* 	inserting content between header and footer]
*	@since 3.9.2
*	@param mixed (id or 'shop' or 'customer'. omit to use default)
*	@param str the content we want to use
*	@return str
*********************************************************/
function wppizza_skeleton_template_emails($id = 0, $content = ''){

	$output = WPPIZZA() -> templates_email_print -> get_skeleton_template('emails', $id , $content);

return $output;
}

/*********************************************************
*	[get email skeleton template by id or shop/customer
* 	inserting content between header and footer]
*	@since 3.9.2
*	@param int (id). omit to use default
*	@param str the content we want to use
*	@return str
*********************************************************/
function wppizza_skeleton_template_print($id = 'default', $content = ''){

	$output = WPPIZZA() -> templates_email_print -> get_skeleton_template('print', $id , $content);

return $output;
}

/*********************************************************
*	[get email/print template parameters
*	@since 3.9.3
*	@param array of args
*		@args str ('print' or 'emails')
*		@args mixed
*			(if $type 'emails' you can use a specific id(int),  'customer' or 'shop' to get template ids setup for the respective recipient)
*			(if $type 'print' you can use a specific id(int) or  '' (empty string) to get the default print template set to be used for printing)
*	@return array
*********************************************************/
function wppizza_template_parameters($args = array('type' => 'print', 'id' => 0 )){
	global $wppizza_options;

	/*
		get print or emails templates
		defaults to print
	*/
	$templates = in_array($args['type'], array('emails', 'print')) ? get_option(WPPIZZA_SLUG.'_templates_'.$args['type']) : get_option(WPPIZZA_SLUG.'_templates_print');

	/*
		template id - selectable by str/name if emails templates
	*/
	if($args['type'] === 'emails'){
		if(strtolower($args['id']) == 'shop'){
			// shop email
			$template_id = absint($wppizza_options['templates_apply']['emails']['recipients_default']['email_shop']);
		}elseif(strtolower($args['id']) == 'customer'){
			// customer email
			$template_id = absint($wppizza_options['templates_apply']['emails']['recipients_default']['email_customer']);
		}else{
			// by id
			$template_id = absint($args['id']) ;
		}

	}else{
		/* get the default template set to be used for print outputs if not set */
		$template_id = ($args['id'] !== '') ? absint($args['id']) : absint($wppizza_options['templates_apply']['print']) ;
	}


	/*
		get the parameters set
		for  the specific template selected
		if it des not exist, get the default (id= 0)
	*/
	$templates = !empty($templates[$template_id]) ? $templates[$template_id] : $templates[0];

	/*
		return array
	*/
	$param = array();
	$param['id'] = $template_id;
	$param['type'] = $args['type'];
	$param['content_type'] = ($templates['mail_type'] == 'phpmailer') ? 'text/html' : 'text/plain' ;
	$param['title'] = !empty($templates['title']) ? $templates['title'] : '' ;
	$param['parameters'] = $templates;


return $param;
}

/*********************************************************
*	[get template markup - markup for a specific order based on a selected email or print template]
*	@since 3.9.3
*	@param array of args
*		@args array (order already formatted)
*		@args array template parameters as returned by wppizza_template_parameters
*		@args bool false to get only the content type selected in the template, true to get plaintext AND html
*	@return mixed (array|str)
*********************************************************/
function wppizza_template_markup($args = array('order' => false, 'param' => false , 'content_type' => false )){

		/*
			a very basic sanity check
		*/
		if(empty($args['order']) || !is_array($args['order']) || empty($args['param']) || !is_array($args['param'])){
			return false;
		}

		/*
			do we want to return plaintext and html markup , or both ?
		*/
		$markup = empty($args['content_type']) ? '' : array();


		/*
			selected content
		*/
		/* get html or plaintext only */
		if(empty($args['content_type'])){

			/* html */
			if(!empty($args['param']['content_type']) && $args['param']['content_type'] == 'text/html'){
				$markup = WPPIZZA() -> templates_email_print -> get_template_email_html_sections_markup($args['order'], $args['param']['parameters'], $args['param']['type'], $args['param']['id']);
			}
			/* plaintext (returns array with sections and markup, so only return markup here */
			else{
				$markup = WPPIZZA() -> templates_email_print -> get_template_email_plaintext_sections_markup($args['order'], $args['param']['parameters'], $args['param']['type']);
				$markup = $markup['markup'];
			}
		}
		/* get html AND plaintext */
		else{
			/* html */
			$markup['text/html'] = WPPIZZA() -> templates_email_print -> get_template_email_html_sections_markup($args['order'], $args['param']['parameters'], $args['param']['type'], $args['param']['id']);
			
			/* plaintext (returns array with sections and markup, so only return markup here */
			$markup['text/plain'] = WPPIZZA() -> templates_email_print -> get_template_email_plaintext_sections_markup($args['order'], $args['param']['parameters'], $args['param']['type']);
			$markup['text/plain'] = $markup['text/plain']['markup'];
		}
/*
	return the markup (str or array)
*/
return $markup;
}

?>