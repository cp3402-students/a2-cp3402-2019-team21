<?php
/**
* WPPIZZA_TEMPLATES_MENU_ITEMS Class
*
* @package     WPPIZZA
* @subpackage  WPPizza display menu items (loop)
* @copyright   Copyright (c) 2015, Oliver Bach
* @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
* @since       3.0
*
*/
if ( ! defined( 'ABSPATH' ) ) exit;/*Exit if accessed directly*/

/* ================================================================================================================================= *
*
*
*
*	CLASS - WPPIZZA_TEMPLATES_MENU_ITEMS
*
*
*
* ================================================================================================================================= */

class WPPIZZA_MARKUP_MENU_ITEMS{

	private $wppizza_terms = array();/*as we might need to access t his in several places, just get it once for all*/
	private $wppizza_terms_by_slug = array();
	/***************************************************************************************************************
	*
	*
	*	[construct]
	*
	*
	****************************************************************************************************************/
	function __construct() {
		/* get sorted categories */
		add_action('init', array($this,'get_wppizza_terms'));

		/**
			markup modules
		**/
		/*header before loop*/
		add_filter('wppizza_filter_menu_header', array($this, 'wppizza_filter_menu_header'), 10, 6 );

		/*
		loop item elemens are added by doing
		'wppizza_filter_menu_loop_'.$element.'
		available elements are set by adding them to set_template_elements() -> $available_elements
		then add required filter and create output like so (if element is xyz)
		add_filter('wppizza_filter_menu_loop_xyz', array($this, 'wppizza_filter_menu_loop_xyz'), 10, 8 );
		and create function as for the others
		*/

		/*in loop - article element open*/
		add_filter('wppizza_filter_menu_loop_article_open', array($this, 'wppizza_filter_menu_loop_article_open'), 10, 8 );
		/*in loop - title*/
		add_filter('wppizza_filter_menu_loop_title', array($this, 'wppizza_filter_menu_loop_title'), 10, 8 );
		/*in loop - thumbnail*/
		add_filter('wppizza_filter_menu_loop_thumbnail', array($this, 'wppizza_filter_menu_loop_thumbnail'), 10, 8 );
		/*in loop - category*/
		add_filter('wppizza_filter_menu_loop_category', array($this, 'wppizza_filter_menu_loop_category'), 10, 8 );
		/*in loop - prices*/
		add_filter('wppizza_filter_menu_loop_prices', array($this, 'wppizza_filter_menu_loop_prices'), 10, 8 );
		/*in loop - content*/
		add_filter('wppizza_filter_menu_loop_content', array($this, 'wppizza_filter_menu_loop_content'), 10, 8 );
		/*in loop - additives*/
		add_filter('wppizza_filter_menu_loop_additives', array($this, 'wppizza_filter_menu_loop_additives'), 10, 8 );
		/*in loop - permalink*/
		add_filter('wppizza_filter_menu_loop_permalink', array($this, 'wppizza_filter_menu_loop_permalink'), 10, 8 );
//		/*in loop - single post - comments*/
//		add_filter('wppizza_filter_menu_loop_comments', array($this, 'wppizza_filter_menu_loop_comments'), 10, 8 );
		/*in loop - article element close*/
		add_filter('wppizza_filter_menu_loop_article_close', array($this, 'wppizza_filter_menu_loop_article_close'), 10, 8 );
		/*no results - instead of loop*/
		add_filter('wppizza_filter_menu_no_results', array($this, 'wppizza_filter_menu_no_results'), 10, 6 );
		/*additives after loop*/
		add_filter('wppizza_filter_menu_additives', array($this, 'wppizza_filter_menu_additives'), 10, 6 );
		/*pagination after loop*/
		add_filter('wppizza_filter_menu_pagination', array($this, 'wppizza_filter_menu_pagination'), 10, 6 );

	}

	/***********************************************************************************************************************************************************
	*
	*
	*
	*
	*	[  get / build queries and markup based on attributes and type set in shortcodes ]
	*
	*
	*
	*	return array
	***********************************************************************************************************************************************************/
	function markup($atts = null, $type = null){


	$markup='';

		/***********************************************
		*
		*	regular loops
		*
		***********************************************/
		if(empty($type) && (!isset($atts['bestsellers']) && !isset($atts['single']) )){
			global $wppizza_options;

			/***********************************
				set type to ident type of shortcode
			***********************************/
			$type = 'category';

			/***********************************
				get all categories by slug
			***********************************/
			$all_categories=$this->get_wppizza_terms_by_slug();

			/***********************************
				omit cats without menu items if set
			***********************************/
			$omit_empty_categories = !empty($wppizza_options['layout']['omit_empty_categories']) ? true : false ;
			if($omit_empty_categories){
				if(!empty($all_categories)){
					foreach($all_categories as $cat_key => $cat_array){
						if(empty($cat_array['count'])){
							unset($all_categories[$cat_key]);
						}
					}
				}
			}


			/***********************************
				get set categories
			***********************************/
			if(!empty($atts['category'])){
				$category_slugs = explode(',',$atts['category']);
			}

			/***********************************
				get first category if not set
			***********************************/
			if(empty($atts['category'])){
				$first_category = reset($all_categories);
				$category_slugs = array($first_category['slug']);
			}
			/***********************************
				set slugs depending on exclusion and !all, sorted by category sort
			***********************************/
			if(in_array('!all', $category_slugs)){

				/**get all sorted ones first */
				$set_categories=$all_categories;
				/**check if we are excluding some categories**/
				foreach($category_slugs as $sKey=>$slug){
					if(substr($slug,0,1)=='-'){
						$excl_slug = trim(substr($slug,1));
						unset($set_categories[$excl_slug]);
					}
				}
				/*set resulting**/
				$category_slugs = array_keys($set_categories);
				/**force posts per page to be -1 when using !all*/
				$posts_per_page = -1;
			}

			/**
				force navigation to be off
				when displaying multiple categories
				/*to check, should also be done by number of shortcodes on page i guess
			**/
			if(count($category_slugs)>1){
				$atts['no_pagination'] = true;
			}


			/***********************************
				run the query for each category slug
				provided it exists in the first place
			***********************************/
			foreach($category_slugs as $slug){

				if(!empty($all_categories[$slug])){
					/**
						set category id and slug to refer to later
					**/
					$category = array('id' => $all_categories[$slug]['id'], 'slug'=> $slug , 'name'=> $all_categories[$slug]['name'], 'description'=> $all_categories[$slug]['description'] );


					/**
						set arguments for query
					**/
					$query_args = array();
					$query_args['posts_per_page'] = !empty($posts_per_page) ? $posts_per_page : $wppizza_options['layout']['items_per_loop'] ;
					$query_args['exclude'] = (empty($atts['exclude']) || !empty($atts['include']) )? array() : array_unique(explode(',',$atts['exclude']));/*include, overrides exclude*/
					$query_args['include'] = !empty($atts['include']) ? array_unique(explode(',',$atts['include'])) : array();
					$query_args['tax_query']['field'] =  'slug';
					$query_args['tax_query']['terms'] =  strtolower(trim($slug));


					/**
						run the query
					**/
					$query_results = $this->get_query_results($query_args);

					/**
						set markup parameters
					**/
					$parameters = $this->set_markup_parameters($query_results, $atts, $type, $category);

					/**
						get markup by parameters set . looped, so MUST BE CONCAT
					**/
					$markup.= $this->get_markup_by_arguments($parameters);
				}
			}

		return $markup;
		}

		/***********************************************
		*
		*	single item
		*
		***********************************************/
		if(empty($type) && isset($atts['single']) ){
			/***********************************
				set type to ident type of shortcode
			***********************************/
			$type = 'single';

			/***********************************
				get all categories by slug
			***********************************/
			$all_categories=$this->get_wppizza_terms_by_slug();

			/************************************
				set arguments for query
			************************************/

			/*allow to pass comma separated array*/
			$selected_items = explode(',',$atts['single']);

			/*if array, trim and cast to integer*/
			$single_items_array = array();
			foreach($selected_items as $selected_item){
				$id=(int)trim($selected_item);
				$single_items_array[$id]=$id;/*automatically remove duplicates*/
			}

			$query_args['include'] = $single_items_array;
			$query_args['orderby'] = 'post__in';

			/************************************
				run the query
			************************************/
			$query_results = $this->get_query_results($query_args);


			/************************************
				set markup parameters
			************************************/
			$parameters = $this->set_markup_parameters($query_results, $atts, $type, false);


			/************************************
				allow to only show some/one price(s)
				when using single items shortcode
				by using $atts['price_id']
				provided $atts[single] only refers to one single menu item
				(at least one of the id's in $atts['price_id'] must exist for this item)
				DONT USE !empty($atts['price_id']) as they can be 0 !!
			************************************/

			if(isset($atts['price_id']) && $atts['price_id']!='' && count($selected_items) >= 1){

				/*
					create prices id (aka sizes) array
					associated with each post id
				*/
				$price_ids = explode(',' , $atts['price_id']);
				$price_id_sizes = array();
				foreach($selected_items as $k => $pid){
					$price_id_sizes[$k][$pid] = explode(':',$price_ids[$k]);//allow multiple sizes for single item (using : )
				}

				/*
					make returned zero indexed post ids to equate actual post id's to be able to map
					sizes_ids to post ids
				*/
				$sc_post_ids = array_map(function($val){return $val->ID;}, $parameters['posts']);
				$parameters['posts'] = array_combine($sc_post_ids , $parameters['posts']);
				$sc_posts = array_combine($sc_post_ids , $parameters['posts']);


				$reset_post_data = array();
				foreach($price_id_sizes as $k => $sizes_in_post){
					foreach($sizes_in_post as $pid => $sizes){

						$post_data = (array)$sc_posts[$pid];//sc_posts MUST be converted to array or it will all go horribly wrong

						$reset_post_data[$k] = $post_data;

						$intersect = array_intersect_key($post_data['wppizza_data']['prices'], array_flip($sizes));
						if(!empty($intersect)){
							$reset_post_data[$k]['wppizza_data']['prices'] = $intersect;
						}
					}
				}

				/*
					add (back) to parameters as object
				*/
				$parameters['posts'] = array();
				foreach($reset_post_data as $k => $data){
					$parameters['posts'][$k] = (object)$reset_post_data[$k];
				}
			}

			/***********************************
				get markup by parameters set
			***********************************/
			$markup = $this->get_markup_by_arguments($parameters);



		return $markup;
		}


		/***********************************************
		*
		*	bestsellers
		*
		***********************************************/
		if(empty($type) && isset($atts['bestsellers']) ){

			/***********************************
				set type to iden type of shortcode
			***********************************/
			$type = 'bestsellers';
			/***********************************
				get all categories by slug
			***********************************/
			$all_categories=$this->get_wppizza_terms_by_slug();
			/***********************************
				get bestsellers from db
			***********************************/
			global $wpdb;
			/**wppizza posts to compare against, making sure posts still exists**/
			$wppPostsQuery="SELECT ID FROM ".$wpdb->prefix ."posts where post_type='".WPPIZZA_POST_TYPE."' AND post_status='publish' ";
			$wppPostsRes = $wpdb->get_results($wppPostsQuery,OBJECT_K );

			/**run the query**/
			$bestsellersQuery="SELECT id, order_ini FROM ".$wpdb->prefix . WPPIZZA_TABLE_ORDERS." WHERE payment_status='COMPLETED' ";
			$bestsellersRes = $wpdb->get_results($bestsellersQuery);

			$bestsellers=array();
			/**loop through items and get quantities**/
			foreach($bestsellersRes as $b=>$bs){
				$thisOrderDetails=maybe_unserialize($bs->order_ini);
				if(isset($thisOrderDetails['items']) && is_array($thisOrderDetails['items'])){
					foreach($thisOrderDetails['items'] as $item){
						/**make sure this post still exists and has been sold more than 0 times**/
						if(!empty($item['post_id']) && isset($wppPostsRes[$item['post_id']]) && $item['quantity']>0){
							if(!isset($bestsellers[$item['post_id']])){
								$bestsellers[$item['post_id']] = $item['quantity'];
							}else{
								$bestsellers[$item['post_id']] += $item['quantity'];
							}
						}
					}
				}
			}

			/*sort by quantity*/
			arsort($bestsellers);

			/*chunk to required bits*/
			$chunks=(int)$atts['bestsellers'];
			$bestsellers=array_chunk($bestsellers, $chunks, true);


			/**get bestsellers**/
			$set_bestsellers = array();
			if(count($bestsellers)>0){
				/**required bestsellers - first chunk**/
				$set_bestsellers=array_keys($bestsellers[0]);
			}
			/**add distinctly set includes (if any)**/
			if(isset($atts['include']) && $atts['include']!=''){
				$set_bestsellers = array_merge($set_bestsellers, explode(',',$atts['include']));
			}
			//alternatives if empty*/
			if(count($set_bestsellers)<=0 && isset($atts['ifempty'])){
				$set_bestsellers = 	explode(',',$atts['ifempty']);
			}
			/* make them unique in case $atts['include'] duplicates id's */
			$set_bestsellers = array_unique($set_bestsellers);


			/************************************
				set arguments for query
			************************************/
			/*if array, trim and cast to integer*/
			$single_items_array = array();
			foreach($set_bestsellers as $selected_item){
				$id=(int)trim($selected_item);
				$single_items_array[$id]=$id;/*automatically remove duplicates*/
			}

			$query_args['include'] = $single_items_array;
			$query_args['orderby'] = 'post__in';

			/************************************
				run the query
			************************************/
			$query_results = $this->get_query_results($query_args);


			/************************************
				set markup parameters
			************************************/
			$atts['noheader'] = 1 ; /**force omit header for bestsellers */
			$parameters = $this->set_markup_parameters($query_results, $atts, $type, false);

			/***********************************
				get markup by parameters set
			***********************************/
			$markup = $this->get_markup_by_arguments($parameters);

		return $markup;
		}


		/***********************************************
		*
		*	type = add_item_to_cart_button
		*
		***********************************************/
		if(!empty($type) && $type == 'add_item_to_cart_button'){
			$markup = $this->add_item_to_cart_button($atts);
		return $markup;
		}

	/*script should never get here, but just for the hell of it*/
	return $markup;
	}

	/***********************************************************************************************************************************************************
	*
	*
	*
	*
	*	[  build and run the query  ]
	*
	*
	*
	*	return array
	***********************************************************************************************************************************************************/
	function get_query_results($query_args){
		global $wppizza_options;

			/**
				set query args
			**/
			$args =  array();
				$args['post_type'] 		=  WPPIZZA_POST_TYPE ;
				$args['posts_per_page'] =  !empty($query_args['posts_per_page']) ? $query_args['posts_per_page'] : $wppizza_options['layout']['items_per_loop'] ;
				$args['paged'] 			=  $this->set_paged_var();
				$args['post__not_in'] 	=  !empty($query_args['exclude']) ? $query_args['exclude'] : '' ;
				$args['post__in'] 		=  !empty($query_args['include']) ? $query_args['include'] : '' ;

				/**
					omit tax query if not set (single items for example)
				**/
				if(!empty($query_args['tax_query'])){
					$args['tax_query'] =  array() ;
					$args['tax_query'][0]['taxonomy'] 			=  WPPIZZA_TAXONOMY ;
					$args['tax_query'][0]['field'] 				=  !empty($query_args['tax_query']['field']) ? $query_args['tax_query']['field'] : 'slug' ;
					$args['tax_query'][0]['terms']				=  !empty($query_args['tax_query']['terms']) ? $query_args['tax_query']['terms'] : array() ;
					$args['tax_query'][0]['include_children'] 	=  false ;
				}

				$args['orderby'] 		=  !empty($query_args['orderby']) ? $query_args['orderby'] : 'menu_order' ;
				$args['order'] 			=  !empty($query_args['order']) ? $query_args['order'] : 'ASC' ;

			/**
				apply filters if required
			**/
			$args = apply_filters('wppizza_filter_loop_args', $args);

			/**
				execute query
			**/
			$the_query = new WP_Query( $args );

	/**
		return results
	**/
	return $the_query;
	}

	/***********************************************************************************************************************************************************
	*
	*
	*
	*
	*	[  set parameters to  build markup ]
	*
	*
	*
	*	return array
	***********************************************************************************************************************************************************/
	function set_markup_parameters($query_results, $atts, $type, $category = false){
		global $wppizza_options;

		/*
			set currency symbol
		*/
		$currency_symbol = $wppizza_options['order_settings']['currency_symbol'];
		/*
			set blogid
		*/
		global $blog_id;

		/*
			get set wppizza categories
		*/
		$get_wppizza_terms = get_terms(WPPIZZA_TAXONOMY);
		/*
			get templeta vars from
			from helper function
		*/
		$style = $this -> set_template_style($atts);
		$item_class = $this -> set_template_item_class($atts);
		$elements = $this -> set_template_elements($atts, $style);
		$attributes = $this -> set_template_attributes($atts);/*includes currency position next to prices*/


		/*********************
			ini return array
		**********************/
		$parameters = array();

		/**********************************************
		*
		*
		*	add global parameters
		*
		*
		**********************************************/
		$parameters['global']['shortcode_type'] = $type;

		$parameters['global']['rtl'] = is_rtl() ? true : false ;/*add right to left class if required*/
		$parameters['global']['blog_id'] = $blog_id;
		$parameters['global']['post_count'] = $query_results->post_count;
		$parameters['global']['max_num_pages'] = $query_results->max_num_pages;

		$parameters['global']['categoy'] = $category;

		$parameters['global']['additives'] = array(); /*holds all additives for all posts returned by query */

		/** include/exclude from display **/
		$parameters['global']['include']['header'] = ( empty($category) || !empty($atts['noheader']) || !empty($wppizza_options['layout']['suppress_loop_headers'])) ? false : true; /* show header if we have a category and atts[noheader] is not defined*/
		$parameters['global']['include']['loop'] = !empty($query_results->post_count) ? true : false; /* include loop, else include noresults */
		$parameters['global']['include']['no_results'] = empty($query_results->post_count) ? true : false; /* include noresults */
		$parameters['global']['include']['additives'] = isset($atts['showadditives']) ? wppizza_validate_boolean($atts['showadditives']) : 'auto' ;/*set bool if defined or 'auto' to override below as required*/
		$parameters['global']['include']['pagination'] = (!empty($atts['no_pagination']) || $query_results->max_num_pages <= 1) ?  false : true;
		$parameters['global']['include']['viewonly'] = !empty($atts['viewonly']) ?  true : false;


		/** individual template overrides **/
		$parameters['global']['template'] = array();
		$parameters['global']['template']['style'] = $style;
		$parameters['global']['template']['item_class'] = $item_class;
		$parameters['global']['template']['elements'] = $elements;
		$parameters['global']['template']['attributes'] = $attributes;
		//$parameters['global']['template']['elements_exclude'] = $elements_exclude;
		//$parameters['global']['template']['elements_include'] = $elements_include;




		/**********************************************
		*
		*
		*	add posts parameters
		*
		*
		**********************************************/
		$parameters['posts'] = array();
		/*****
		*
		*	add wppizza meta data, category_id and showing additives (y/n) to post object
		*
		*****/

		foreach($query_results -> posts as $key=>$post){


			/*
				add standard WP post object first of all
			*/
			$parameters['posts'][$key] = $post;
			/*
				custom add - has thumbnail ?
			*/
			$parameters['posts'][$key]->has_post_thumbnail = has_post_thumbnail( $post->ID );

			/*
				custom add -  title stripped of html
			*/
			$parameters['posts'][$key]->the_title_attribute = the_title_attribute(array('post'=>$post->ID, 'echo'=>0));

			/*
				custom add -  permalink
			*/
			$parameters['posts'][$key]->permalink = get_permalink( $post->ID );

			/*
				custom add -  blogid
			*/
			$parameters['posts'][$key]->blog_id = $blog_id;

			/*
				strip all tags of content if notags attribute set
			*/
			$parameters['posts'][$key]->post_content = !empty($atts['notags']) ? wp_strip_all_tags( $post->post_content ) : $post->post_content ;

			/*
				custom add
				get and add wppizza metadata - allow filtering
			*/
			$wppizza_metadata = apply_filters('wppizza_filter_loop_meta',get_post_meta($post->ID, WPPIZZA_POST_TYPE, true ), $post->ID);
			$parameters['posts'][$key]->wppizza_data = $wppizza_metadata;

			/*
				add sizes, prices (as 'value') / formatted prices (as 'price') , labels (as size)- overwriting original price meta
			*/
			foreach($wppizza_metadata['prices'] as $price_key=>$price){
				$parameters['posts'][$key]->wppizza_data['prices'][$price_key] = array('value'=>$price, 'price'=>wppizza_format_price($price, $currency_symbol, $attributes['currency_price']), 'size'=>$wppizza_options['sizes'][$wppizza_metadata['sizes']][$price_key]['lbl'] );		//$wppizza_options['sizes']

			}
			/*
				add additives, overwriting original when done
			*/
			if(!empty($wppizza_metadata['additives']) && count($wppizza_metadata['additives'])>0){
				$post_additives = array();;
				foreach($wppizza_metadata['additives'] as $additive_id){
					$post_additives[$additive_id]= array('sort'=>$wppizza_options['additives'][$additive_id]['sort'], 'name'=>$wppizza_options['additives'][$additive_id]['name'] );
					$parameters['global']['additives'][$additive_id] = $post_additives[$additive_id]; /*add to global array using key for uniqueness*/
				}
				asort($post_additives);/*sort, keeping index*/
				/*overwrite original additives key*/
				$parameters['posts'][$key]->wppizza_data['additives'] = $post_additives;
			}

			/*
				add global categories if query is for a category anyway
				else get all this is assigned to (for atts[single] shortcode for example)
			*/
			if(!empty($category)){
				$parameters['posts'][$key]->wppizza_data['category'] =  $category;
			}
			/** if we do not have a category (atts[single] for example), get all for this item and use first or set as false if uncategorised*/
			if(empty($category)){
				$get_post_terms = wp_get_post_terms( $post->ID, WPPIZZA_TAXONOMY);
				/*get topmost that applies from sorted categories if there are any at all*/
				if ($get_post_terms && !is_wp_error($get_post_terms)){
					$plucked = wp_list_pluck( $get_post_terms, 'term_id');
					$plucked = array_flip($plucked);// flip to get term_id as key
					foreach($this->wppizza_terms as $term_id=>$term){
						if(isset($plucked[$term_id])){
							$term_key = $plucked[$term_id];
							$post_category = array('id' => $get_post_terms[$term_key]->term_id, 'slug'=> $get_post_terms[$term_key]->slug , 'name'=> $get_post_terms[$term_key]->name, 'description'=> $get_post_terms[$term_key]->description );
							$parameters['posts'][$key]->wppizza_data['category'] =  $post_category;
							break;
						}
					}
				}else{
					/*
						force an uncategorised category if it was not assigned to any (possible if using [wppizza single=x] shortcode) - @since 3.6.1
					*/
					$post_category = array('id' => 0, 'slug'=> sanitize_title_with_dashes($wppizza_options['localization']['uncategorised']) , 'name'=> $wppizza_options['localization']['uncategorised'], 'description'=> '' );
					$parameters['posts'][$key]->wppizza_data['category'] =  $post_category;
				}
			}

			/*
				get all the terms (categories) this item belongs to
			*/
			$post_terms = wp_get_post_terms( $post->ID, WPPIZZA_TAXONOMY );

			/* keeps things as arrays here */
			$parameters['posts'][$key]->wppizza_data['terms'] = !empty($post_terms) ? json_decode(json_encode($post_terms), true)  : array();

		}

		/*
			sort global additives
		*/
		asort($parameters['global']['additives']);/*sort, keeping index*/
		/*
			overwrite global include_additives if set to auto
		*/
		if($parameters['global']['include']['additives'] === 'auto'){
			$parameters['global']['include']['additives'] = false;
			if(!empty($parameters['global']['additives']) && count($parameters['global']['additives'])>0){
				$parameters['global']['include']['additives'] = true;
			}
		}

		/*
			allow filtering of all parameters we could/can in markup
		*/
		$parameters = apply_filters('wppizza_filter_set_markup_parameters', $parameters, $style, $item_class, $elements, $attributes );


	return $parameters;
	}

	/***********************************************************************************************************************************************************
	*
	*
	*
	*
	*	[  build markup - loop - using filters to put elements in right order (or indeed omit)]
	*
	*
	*
	*	return string
	***********************************************************************************************************************************************************/
	function get_markup_by_arguments($parameters){

		/**
			temp, enable permalinks
		**/
		//$parameters['global']['template']['elements']['permalink'] = 'permalink';


		/*************************************

			parameters to pass on to filters
			somewhat split into distinct
			variables for convenience

		*************************************/

		/**add options data to actions*/
		global $wppizza_options;

		/**add session data to actions perhaps ?*/
		//$wppizza_session = array();
		//$wppizza_session['user'] = 'todo';//$_SESSION[$this->session_key_userdata];
		//$wppizza_session['cart'] = 'todo';//$_SESSION[$this->session_key_cart];

		/**add style used */
		$wppizza_style = $parameters['global']['template']['style'];

		/*is rtl*/
		$is_rtl = $parameters['global']['rtl'];

		/* localization - for ease of use */
		$txt = $wppizza_options['localization'];

		/* all additives */
		$additives = wppizza_all_additives();

		/* max num pages */
		$max_num_pages = $parameters['global']['max_num_pages'];

		/*category id/slug/name/description - for ease of use */
		$category = $parameters['global']['categoy'];

		/*number of posts*/
		$post_count = $parameters['global']['post_count'];

		/*layout vars*/
		$layout = $parameters['global']['template'];

		/************************************************************************************************************************
		*
		*
		*	creating loop markup
		*
		*
		*	Note regarding filter hooks:
		*	filter hooks are added for convenience, on could instead also the filters applied to each section such as
		*	wppizza_filter_menu_header, wppizza_filter_menu_no_results, wppizza_filter_menu_additives,
		*	wppizza_filter_menu_pagination etc etc , or indeed wppizza_filter_menu-markup
		*
		*	if using the supplied filter hooks , they should add to the markup array like so :
		*	$markup['unique_key'] = 'markup element - text - etc ';
		*
		************************************************************************************************************************/
		$markup=array();/*ini markup string*/


		/*****************************

			header

		*****************************/
		/* convenience filter hook */
		$markup = apply_filters('wppizza_loop_before_header', $markup, $txt, $additives, $category, $max_num_pages, $post_count);

		if(!empty($parameters['global']['include']['header'])){

			$markup = apply_filters('wppizza_filter_menu_header', $markup, $txt, $additives, $parameters , $is_rtl , $category);
		}

		/* convenience filter hook */
		$markup = apply_filters('wppizza_loop_after_header', $markup, $txt, $additives, $category, $max_num_pages, $post_count);



		/*****************************

			before loop / no results

		*****************************/
		/* convenience filter hook */
		$markup = apply_filters('wppizza_loop_before_menu_items', $markup, $txt, $additives, $category, $max_num_pages, $post_count);
		/*****************************

			loop

		*****************************/
		if(!empty($parameters['global']['include']['loop'])){

			$articlecount = 1;
			foreach($parameters['posts'] as $count=>$post_vars){
				$menu_item_markup = array();

				/*
					if we do not have a category set (i.e when using [single=x] attribute in shortcode for example)
					lets see if we can get a category detail for this post itself
				*/
				$post_category = ( empty($category) && !empty($post_vars->wppizza_data['category']) )? $post_vars->wppizza_data['category'] : $category ;

				/*
					always open article element before - not settable via shortcode
				*/
				$menu_item_markup = apply_filters('wppizza_filter_menu_loop_article_open', $menu_item_markup, $post_vars , $txt, $additives, $parameters , $is_rtl , $post_category, $articlecount);

				/*
					add apply filter per module - in order of appearance in shortcode (or defaults)
				*/
				foreach($layout['elements'] as $element){

					$menu_item_markup = apply_filters('wppizza_filter_menu_loop_'.$element.'', $menu_item_markup, $post_vars , $txt, $additives, $parameters , $is_rtl, $post_category, $articlecount);

				}
				/*
					always close article element after - not settable via shortcode
				*/
				$menu_item_markup = apply_filters('wppizza_filter_menu_loop_article_close', $menu_item_markup, $post_vars , $txt, $additives, $parameters , $is_rtl , $post_category, $articlecount);


			$markup['menu_item_markup_'.$articlecount] = implode('',$menu_item_markup);

			$articlecount++;
			}
		}

		/***********************

			no_results

		***********************/
		if(!empty($parameters['global']['include']['no_results'])){

			$markup = apply_filters('wppizza_filter_menu_no_results', $markup, $txt, $additives, $parameters , $is_rtl , $category);//$articlecount $post_vars

		}

		/*****************************

			after loop / no results

		*****************************/
		/* convenience filter hook */
		$markup = apply_filters('wppizza_loop_after_menu_items', $markup, $txt, $additives, $category, $max_num_pages, $post_count);

		/***********************

			additives

		***********************/
		/* convenience filter hook */
		$markup = apply_filters('wppizza_loop_before_additives', $markup, $txt, $additives, $category, $max_num_pages, $post_count);

		if(!empty($parameters['global']['include']['additives'])){

			$markup = apply_filters('wppizza_filter_menu_additives', $markup, $txt, $additives, $parameters , $is_rtl , $category);//$articlecount $post_vars

		}

		/* convenience filter hook */
		$markup = apply_filters('wppizza_loop_after_additives', $markup, $txt, $additives, $category, $max_num_pages, $post_count);

		/***********************

			pagination -
			only if not single and more than 1 page

		***********************/
		/* convenience filter hook */
		$markup = apply_filters('wppizza_loop_before_pagination', $markup, $txt, $additives, $category, $max_num_pages, $post_count);

		if(!empty($parameters['global']['include']['pagination'])){

			$markup = apply_filters('wppizza_filter_menu_pagination', $markup, $txt, $additives, $parameters , $is_rtl , $category);//$articlecount $post_vars

		}

		/* convenience filter hook */
		$markup = apply_filters('wppizza_loop_after_pagination', $markup, $txt, $additives, $category, $max_num_pages, $post_count);

		/************************

			reset postdata and query

		************************/
		wp_reset_query();

	/*markup - for more convenience when filtering - in an array , so implode for output*/
	$markup = apply_filters('wppizza_filter_menu-markup', $markup, $wppizza_style);
	$markup = implode('', $markup);
	return $markup;
	}

	/***********************************************************************************************************************************************************
	*
	*
	*
	*
	*	[ markup parts /modules- output as returned by filters]
	*
	*
	*
	*
	***********************************************************************************************************************************************************/

	/************************************************************************************************************
	*
	*
	*
	*	[ markup parts - header markup]
	*
	*
	*	return @array
	************************************************************************************************************/
	function wppizza_filter_menu_header($markup, $txt, $additives, $parameters, $is_rtl, $category){

			/********************
				parameters for convenience
			*********************/
			$style = $parameters['global']['template']['style'];

			/********************
				set header id
			*********************/
			$header_id = ''. WPPIZZA_POST_TYPE .'-header-'.$category['slug'].'-'.$category['id'].'';

			/*********************
				set header classes
			*********************/
			$header_class=array();

			$header_class['header'][] = 'entry-header';
			$header_class['header'][] = 'page-header';//header only shown when there's a single ctaegory anyway, so we can add this everywhere one thinks
			$header_class['header'][] = ''.WPPIZZA_POST_TYPE.'-header';
			$header_class['header'][] = ''.WPPIZZA_POST_TYPE.'-header-'.$style.'';
			$header_class['header'][] = !empty($is_rtl) ? ''.WPPIZZA_POST_TYPE.'-header-rtl' : '';
			$header_class['header'][] = ''.WPPIZZA_POST_TYPE.'-header-'.$category['slug'].'';

			$header_class['h1'][] ='entry-title';
			$header_class['h1'][] =''.WPPIZZA_POST_TYPE.'-entry-title ';

			$header_class['description'][] = 'entry-meta';
			$header_class['description'][] = ''.WPPIZZA_POST_TYPE.'-header-meta';

			/**
				allow filtering
			**/
			$header_class= apply_filters('wppizza_filter_menu_header_class', $header_class);

			/*
				implode classes for output
			*/
			$header_class['header']= implode(' ', $header_class['header']);
			$header_class['h1']= implode(' ', $header_class['h1']);
			$header_class['description']= implode(' ', $header_class['description']);


			/**
				allow filtering of h1 elemnt
			**/
			$header_element = apply_filters('wppizza_filter_menu_header_element', 'h1');

		/*********************

			get header markup from template - returns/ adds to $markup array

		*********************/
		if(file_exists( WPPIZZA_TEMPLATE_DIR . '/markup/loop/header.php')){
			require(WPPIZZA_TEMPLATE_DIR.'/markup/loop/header.php');
		}else{
			require(WPPIZZA_PATH.'templates/markup/loop/header.php');
		}
		/*********************
			apply filter if required
		*********************/
		$markup = apply_filters('wppizza_filter_menu_header_markup', $markup, $category, $style);


	return 	$markup;
	}
	/************************************************************************************************************
	*
	*
	*
	*	[ markup parts - article open - does/should not exist as editable template]
	*
	*
	*	return @array
	************************************************************************************************************/
	function wppizza_filter_menu_loop_article_open($markup, $post , $txt, $additives, $parameters, $is_rtl, $category, $articlecount){

			static $sectionid = 0;
			/********************
				parameters for convenience
			*********************/
			global $wppizza_options;
			$style = $parameters['global']['template']['style'];
			$item_class = $parameters['global']['template']['item_class'];
			$post_count = $parameters['global']['post_count'];

			/********************
				set article id
			*********************/
			$post_article_id='post-'.$post->ID.'';

			/*********************
				set article classes
			*********************/
			$post_article_class = array();

			/*general class*/
			$post_article_class[] = ''.WPPIZZA_SLUG.'-article';
			/*selected style class*/
			$post_article_class[] = ''.WPPIZZA_SLUG.'-article-'.$style.'';
			/*custom classes added in shortcode */
			$post_article_class[] = !empty($item_class) ? $item_class : '';
			/*rtl*/
			$post_article_class[] = !empty($is_rtl) ? ''.WPPIZZA_POST_TYPE.'-article-rtl' : '';


			/*add - first*/
			if($articlecount==1){
				$post_article_class[]=''.WPPIZZA_SLUG.'-article-first';
			}
			/*add - last if more than one*/
			if($articlecount==$post_count && $post_count>1){
				$post_article_class[]=''.WPPIZZA_SLUG.'-article-last';
			}
			/*category*/
			$post_article_class[]=''.WPPIZZA_SLUG.'-article-'.$category['slug'].'';
			/*category with id*/
			$post_article_class[]=''.WPPIZZA_SLUG.'-article-'.$category['slug'].'-'.$category['id'].'';
			/*
				add all terms this belongs to using WPPIZZA_TAXONOMY here as WP > 4.2 adds some of these already and we
				can simply use array_unique below to get rid of superflous ones
			*/
			foreach($post->wppizza_data['terms'] as $term){
				$post_article_class[]=''.WPPIZZA_TAXONOMY.'-'.$term['slug'].'';
				$post_article_class[]=''.WPPIZZA_TAXONOMY.'-term-'.$term['term_id'].'';
			}
			/*general*/
			$post_article_class[]='entry-content';

			/**
				allow filtering
			**/
			$post_article_class = apply_filters('wppizza_filter_post_arcticle_class', $post_article_class, $style, $post->ID);

			/**add standard*/
			$post_article_class = get_post_class($post_article_class, $post->ID);

			/*
				implode classes for output
			*/
			$post_article_class = array_unique($post_article_class);/* no need to have the same class multiple times */
			$post_article_class=implode(' ', $post_article_class);


		/*************************

			markup

		*************************/
		/*
			add sections for grid layout
		*/
		if( !is_single() && $style == 'grid' ){
			$section_count = (($articlecount-1)/$wppizza_options['layout']['style_grid_columns']);
			$section_max = ceil($post_count/$wppizza_options['layout']['style_grid_columns'])-1;
			if(is_int($section_count)){

				/**add first and last*/
				$first_section_class = $section_count==0 ? ' '.WPPIZZA_SLUG.'-grid-section-first' : '';
				$last_section_class = $section_count==$section_max ? ' '.WPPIZZA_SLUG.'-grid-section-last' : '';


				$markup['section_'] = '<section id="'.WPPIZZA_SLUG.'-grid-section-'.$sectionid.'"  class="'.WPPIZZA_SLUG.'-grid-section '.WPPIZZA_SLUG.'-grid-section-'.$section_count.''.$first_section_class.''.$last_section_class.'">';
				/*advance counter*/
				$sectionid++;
			}
		}
		/*
			add article markup
		*/
		$markup['article_'] = '<article id="' . $post_article_id . '" class="' . $post_article_class . '">';
		/**is this required/uesful actually as class added to article??*/
		//if(is_single()){/*to add to articleclasses*/
			//$markup .= "<div class='entry-content'>";
		//}

	return $markup;
	}
	/************************************************************************************************************
	*
	*
	*
	*	[ markup parts - post title]
	*
	*
	*	return @array
	************************************************************************************************************/
	function wppizza_filter_menu_loop_title($markup, $post , $txt, $additives, $parameters, $is_rtl, $category, $articlecount){

			/********************
				parameters for convenience
			*********************/
			global $wppizza_options;
			/* classes filter parameters */
			$filter_parameters = $this->set_post_classes_filter_parameters($post, $parameters, $articlecount) ;
			/*single category, provided item does actually belong to one */
			if(!empty($post->wppizza_data['category'])){
				$category = $post->wppizza_data['category'];
			}else{
				/* we need a category, lets just get the first one else use first available */
				$category = wppizza_force_first_category();
			}


			/*********************
				get style
			*********************/
			$style = $filter_parameters['layout']['style'];

			/*********************
				set title
			*********************/
			$post_title = apply_filters('wppizza_filter_post_title', $post->post_title , $post->ID);

			/********************
				set id's
			*********************/
			/* title id: including postid, sizes id, +zero to trigger first if set */
			$post_title_id = ''.WPPIZZA_SLUG.'-article-'.$post->blog_id.'-'.$category['id'].'-'.$post->ID.'-'.$post->wppizza_data['sizes'].'-0';
			/* additives id	*/
			$post_additives_id = ''.WPPIZZA_SLUG.'-article-additives-'.$post->ID.'';

			/********************
				set h2 elemnt
			********************/
			$post_title_element = apply_filters('wppizza_filter_post_title_element', 'h2');


			/*********************
				set title classes
			*********************/
			$post_title_class = array();

			/*
				h2
			*/
			$post_title_class['elm'] = array();
			$post_title_class['elm'][] = ''.WPPIZZA_POST_TYPE.'-article-'.$post_title_element.'';
			/**
				if selected in admin, make click on title add to cart or
				show alert when there are more than one size
			**/
			if(!empty($wppizza_options['layout']['add_to_cart_on_title_click'])){
			 	/*no of prices/sizes*/
		 		$numberOfPrices=count($post->wppizza_data['prices']);

		 		/*trigger add to cart**/
		 		if($numberOfPrices==1){
					$post_title_class['elm'][]=' '.WPPIZZA_SLUG.'-trigger-click';
		 		}
		 		/*more than one size available, show alert**/
		 		if($numberOfPrices>1){
			 		$post_title_class['elm'][]=' '.WPPIZZA_SLUG.'-trigger-choose';
		 		}
			}

			/*
				title
			*/
			$post_title_class['title'] = array();
			$post_title_class['title'][] = ''.WPPIZZA_POST_TYPE.'-article-title';


			/*
				additives
			*/
			$post_title_class['additives'] = array();
			$post_title_class['additives'][] = ''.WPPIZZA_POST_TYPE.'-article-additives';


			/**
				allow filtering
			**/
			$post_title_class= apply_filters('wppizza_filter_post_title_class', $post_title_class, $filter_parameters['post_data'], $filter_parameters['layout'], $filter_parameters['parameters'], $filter_parameters['articlecount'], $filter_parameters['session']);

			/*
				implode for output
			*/
			$post_title_class['elm']= implode(' ', $post_title_class['elm']);
			$post_title_class['title']= implode(' ', $post_title_class['title']);
			$post_title_class['additives']= implode(' ', $post_title_class['additives']);


			/*
				additives
				$map to full additives vars
			*/
			$set_post_additives = $post->wppizza_data['additives'];
			$post_additives = array();
			foreach($set_post_additives as $key=>$value){
				$post_additives[$key]['id'] = ''.WPPIZZA_SLUG.'-article-additive-' . $post->ID . '-' .$key .'';
				$post_additives[$key]['class'] =  ''.WPPIZZA_SLUG.'-article-additive '.WPPIZZA_SLUG.'-article-additive-' .$key .'';
				$post_additives[$key]['ident'] = $additives[$key]['ident'];
				$post_additives[$key]['name'] = $additives[$key]['name'];
			}


		/*************************

			markup

		*************************/
		if(file_exists( WPPIZZA_TEMPLATE_DIR . '/markup/loop/posts.title.php')){
			require(WPPIZZA_TEMPLATE_DIR.'/markup/loop/posts.title.php');
		}else{
			require(WPPIZZA_PATH.'templates/markup/loop/posts.title.php');
		}

		/*********************
			apply filter if required
		*********************/
		$markup = apply_filters('wppizza_filter_post_title_markup', $markup, $style);

	return $markup;
	}



	/************************************************************************************************************
	*
	*
	*
	*	[ markup parts - additives - only used when using elements='additives' attribute in shortcode]
	*
	*
	*	return @array
	************************************************************************************************************/
	function wppizza_filter_menu_loop_additives($markup, $post , $txt, $additives, $parameters, $is_rtl, $category, $articlecount){



			/********************
				parameters for convenience
			*********************/
			global $wppizza_options;
			/* classes filter parameters */
			$filter_parameters = $this->set_post_classes_filter_parameters($post, $parameters, $articlecount) ;


			/*********************
				get style
			*********************/
			$style = $filter_parameters['layout']['style'];


			/********************
				set id's
			*********************/
			/* additives id	*/
			$additives_loop_id = ''.WPPIZZA_SLUG.'-post-additives-'.$post->ID.'';

			/********************
				set span elemnt
			********************/
			$additive_loop_element = apply_filters('wppizza_filter_post_additives_element', 'span');


			/*********************
				set classes
			*********************/
			$additives_loop_class = array();

			/*
				additives wrapper classes
			*/
			$additives_loop_class['additives'] = array();
			$additives_loop_class['additives'][] = ''.WPPIZZA_POST_TYPE.'-post-additives';


			/**
				allow filtering
			**/
			$additives_loop_class= apply_filters('wppizza_filter_post_additives_class', $additives_loop_class, $filter_parameters['post_data'], $filter_parameters['layout'], $filter_parameters['parameters'], $filter_parameters['articlecount'], $filter_parameters['session']);

			/*
				implode for output
			*/
			$additives_loop_class['additives']= implode(' ', $additives_loop_class['additives']);


			/*
				additives
				$map to full additives vars
			*/
			$set_post_additives = $post->wppizza_data['additives'];
			$post_additives = array();
			foreach($set_post_additives as $key=>$value){
				$post_additives[$key]['id'] = ''.WPPIZZA_SLUG.'-article-additive-' . $post->ID . '-' .$key .'';
				$post_additives[$key]['class'] =  ''.WPPIZZA_SLUG.'-article-additive '.WPPIZZA_SLUG.'-article-additive-' .$key .'';
				$post_additives[$key]['ident'] = $additives[$key]['ident'];
				$post_additives[$key]['name'] = $additives[$key]['name'];
			}


		/*************************

			markup

		*************************/
		if(file_exists( WPPIZZA_TEMPLATE_DIR . '/markup/loop/posts.additives.php')){
			require(WPPIZZA_TEMPLATE_DIR.'/markup/loop/posts.additives.php');
		}else{
			require(WPPIZZA_PATH.'templates/markup/loop/posts.additives.php');
		}

		/*********************
			apply filter if required
		*********************/
		$markup = apply_filters('wppizza_filter_post_additives_markup', $markup, $style);

	return $markup;
	}


	/************************************************************************************************************
	*
	*
	*
	*	[ markup parts - thumbnail/placeholder]
	*
	*
	*	return @array
	************************************************************************************************************/
	function wppizza_filter_menu_loop_thumbnail($markup, $post , $txt, $additives, $parameters, $is_rtl, $category, $articlecount){

			/********************
				parameters for convenience
			*********************/
			global $wppizza_options;
			/* classes filter parameters */
			$filter_parameters = $this->set_post_classes_filter_parameters($post, $parameters, $articlecount) ;
			$image_placeholder = $parameters['global']['template']['attributes']['image_placeholder'];
			$image_prettyphoto = $parameters['global']['template']['attributes']['image_prettyphoto'];


			/*********************
				get style
			*********************/
			$style = $filter_parameters['layout']['style'];

			/********************
				set id's
			*********************/
			$post_thumbnail_id = ''.WPPIZZA_SLUG.'-article-img-'.$post->ID.'';

			/*********************
				set thumbmail classes
			*********************/
			$post_thumbnail_class = array();

			$post_thumbnail_class['div'][] = ''.WPPIZZA_SLUG.'-article-image';
			$post_thumbnail_class['placeholder'][] = ''.WPPIZZA_SLUG.'-article-image-placeholder';

			/*
				allow filtering
			*/
			$post_thumbnail_class= apply_filters('wppizza_filter_post_thumbnail_class', $post_thumbnail_class, $filter_parameters['post_data'], $filter_parameters['layout'], $filter_parameters['parameters'], $filter_parameters['articlecount'], $filter_parameters['session']);

			/*
				implode for output
			*/
			$post_thumbnail_class['div']= implode(' ', $post_thumbnail_class['div']);
			$post_thumbnail_class['placeholder']= implode(' ', $post_thumbnail_class['placeholder']);


			/**********************
				create image/thumbnail elements for output
			**********************/

			$has_thumbnail = !empty($post->has_post_thumbnail) ? true : false ;
			$has_placeholder = (empty($post->has_post_thumbnail) && !empty($image_placeholder) ) ? true : false ;

			/**create the actual image markup */
			if(!empty($has_thumbnail)){
				$featured_image = '';

				if(!empty($image_prettyphoto)){
					$full_image_data = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'full' );
					$featured_image .= '<a href="'.$full_image_data[0].'" rel="wpzpp-' . $post->post_name	.'" title="' . $post->the_title_attribute	.'">';
				}

				$featured_image .= get_the_post_thumbnail($post->ID, 'thumbnail', array('class' => ''.WPPIZZA_SLUG.'-article-image-thumb', 'title'=>$post->the_title_attribute));

				if(!empty($image_prettyphoto)){
					$featured_image.= "</a>";
				}
			}

		/*************************

			markup

		*************************/
		if(file_exists( WPPIZZA_TEMPLATE_DIR . '/markup/loop/posts.thumbnail.php')){
			require(WPPIZZA_TEMPLATE_DIR.'/markup/loop/posts.thumbnail.php');
		}else{
			require(WPPIZZA_PATH.'templates/markup/loop/posts.thumbnail.php');
		}

		/*********************
			apply filter if required
		*********************/
		$markup = apply_filters('wppizza_filter_post_thumbnail_markup', $markup, $style, $has_thumbnail, $has_placeholder);


	return $markup;
	}
	/************************************************************************************************************
	*
	*
	*
	*	[ markup parts - category]
	*
	*
	*	return @array
	************************************************************************************************************/
	function wppizza_filter_menu_loop_category($markup, $post , $txt, $additives, $parameters, $is_rtl, $category, $articlecount){

			/********************
				parameters for convenience
			*********************/
			/* classes filter parameters */
			$filter_parameters = $this->set_post_classes_filter_parameters($post, $parameters, $articlecount) ;


			$post_category = $category;

			/*********************
				get style
			*********************/
			$style = $filter_parameters['layout']['style'];


			/*********************
				set category classes
			*********************/
			$post_category_class = array();
			/*general class*/
			$post_category_class[] = ''.WPPIZZA_SLUG.'-category';
			$post_category_class[] = ''.WPPIZZA_SLUG.'-category-'.$post_category['id'].'';
			$post_category_class[] = ''.WPPIZZA_SLUG.'-category-'.$post_category['slug'].'';

			/*
				allow filtering
			*/
			$post_category_class = apply_filters('wppizza_filter_post_category_class', $post_category_class, $filter_parameters['post_data'], $filter_parameters['layout'], $filter_parameters['parameters'], $filter_parameters['articlecount'], $filter_parameters['session']);

			/*
				implode for output
			*/
			$post_category_class = implode(' ', $post_category_class);


		/*************************

			markup

		*************************/
		if(file_exists( WPPIZZA_TEMPLATE_DIR . '/markup/loop/posts.category.php')){
			require(WPPIZZA_TEMPLATE_DIR.'/markup/loop/posts.category.php');
		}else{
			require(WPPIZZA_PATH.'templates/markup/loop/posts.category.php');
		}

		/*********************
			apply filter if required
		*********************/
		$markup = apply_filters('wppizza_filter_post_category_markup', $markup, $style);

	return $markup;
	}
	/************************************************************************************************************
	*
	*
	*
	*	[ markup parts - prices, sizes, currency symbols]
	*
	*
	*	return @array
	************************************************************************************************************/

	function wppizza_filter_menu_loop_prices($markup, $post , $txt, $additives, $parameters, $is_rtl, $category, $articlecount){

			/********************
				parameters for convenience
			*********************/
			global $wppizza_options;
			$prices = $post->wppizza_data['prices'];
			$sizes_id = $post->wppizza_data['sizes'];
			/* classes filter parameters */
			$filter_parameters = $this->set_post_classes_filter_parameters($post, $parameters, $articlecount) ;
			/*symbol*/
			$currency=$wppizza_options['order_settings']['currency_symbol'];
			/*position or omit main currency symbol*/
			$currency_display = $parameters['global']['template']['attributes']['currency_main'];
			/*single category, provided item does actually belong to one , else use first available*/
			if(!empty($post->wppizza_data['category'])){
				$category = $post->wppizza_data['category'];
			}else{
				/* we need a category, lets just get the first one else use first available */
				$category = wppizza_force_first_category();
			}



			/********************
				main, large currency symbol position
			********************/
			$currency_left = ($currency_display==='left' ) ? true : false ;
			$currency_right = ($currency_display==='right' ) ? true : false ;


			/*********************
				get style
			*********************/
			$style = $filter_parameters['layout']['style'];

			/********************
				set sizes/prices id's
			*********************/
			$post_sizes_id = ''.WPPIZZA_SLUG.'-article-sizes-'.$post->ID.'';
			$post_prices_id = ''.WPPIZZA_SLUG.'-article-prices-'.$post->ID.'';

			/*********************
				set sizes/prices classes
			*********************/
			$post_prices_class = array();

			/*wrapper outer class*/
			$post_prices_class['sizes'][] = ''.WPPIZZA_SLUG.'-article-sizes';
			$post_prices_class['sizes'][] = ''.WPPIZZA_SLUG.'-article-prices-'.$sizes_id.'';

			/*wrapper inner class*/
			$post_prices_class['prices'][] = ''.WPPIZZA_SLUG.'-article-prices';

			/*class currency*/
			$post_prices_class['currency'][] = ''.WPPIZZA_SLUG.'-article-price-currency';
			$post_prices_class['currency'][] = (!empty($currency_left)) ? ''.WPPIZZA_SLUG.'-article-price-right' : ''.WPPIZZA_SLUG.'-article-price-left';

			/*
				allow filtering
			*/
			$post_prices_class = apply_filters('wppizza_filter_post_prices_class', $post_prices_class, $filter_parameters['post_data'], $filter_parameters['layout'], $filter_parameters['parameters'], $filter_parameters['articlecount'], $filter_parameters['session']);

			/*
				implode for output
			*/
			$post_prices_class['sizes']= implode(' ', $post_prices_class['sizes']);
			$post_prices_class['prices']= implode(' ', $post_prices_class['prices']);
			$post_prices_class['currency']= implode(' ', $post_prices_class['currency']);


			/*
				individual prices
				add id's, classes, titels
			*/
			foreach($prices as $key=>$price){
				/*id consists of prefix - category id - post id - selected sizes - seleted size*/
				$prices[$key]['id'] = ''.WPPIZZA_SLUG.'-'.$post->blog_id.'-'.$category['id'].'-'.$post->ID.'-'.$sizes_id.'-'.$key.'';

				/*class price, allow add to cart if enabled and not viewonly in shortcode*/
				$prices[$key]['class_price'] = ''.WPPIZZA_SLUG.'-article-price '.WPPIZZA_SLUG.'-article-price-'.$sizes_id.'-'.$key.'';
				$prices[$key]['class_price'] .= (empty($wppizza_options['layout']['disable_online_order']) && empty($parameters['global']['include']['viewonly']) ) ? ' '.WPPIZZA_SLUG.'-add-to-cart' : '' ;
				$prices[$key]['class_price'] .= !empty($parameters['global']['include']['viewonly']) ? ' '.WPPIZZA_SLUG.'-article-price-viewonly' : '' ;

				/*tile attribute - if applicable*/
				$prices[$key]['title'] = !empty($wppizza_options['layout']['disable_online_order']) ||  !empty($parameters['global']['include']['viewonly']) ? '' : ' title="'.$txt['add_to_cart'].'"' ;

				/*class size, add nocart class if required*/
				$prices[$key]['class_size'] = ''.WPPIZZA_SLUG.'-article-size';
				$prices[$key]['class_size'] .= !empty($wppizza_options['layout']['hide_cart_icon']) || !empty($parameters['global']['include']['viewonly'])  ? ' '.WPPIZZA_SLUG.'-no-cart' : '' ;

				/**no label - hide pricetiers if only one and set to hide*/
				$prices[$key]['no_label'] = (!empty($wppizza_options['layout']['hide_single_pricetier']) && count($prices)<=1) ?  true : false ;

				/**set to "free" if zero */
				$prices[$key]['price'] =  (empty($price['value']) && !empty($wppizza_options['prices_format']['localize_zero_price']) && !empty($wppizza_options['localization']['localize_zero_price'])  ) ?  $wppizza_options['localization']['localize_zero_price'] : $price['price'] ;

			}

			/* allow filtering */
			$prices = apply_filters('wppizza_filter_post_prices', $prices, $post, $style);

		/*************************

			markup

		*************************/
		if(file_exists( WPPIZZA_TEMPLATE_DIR . '/markup/loop/posts.prices.php')){
			require(WPPIZZA_TEMPLATE_DIR.'/markup/loop/posts.prices.php');
		}else{
			require(WPPIZZA_PATH.'templates/markup/loop/posts.prices.php');
		}


		/*********************
			apply filter if required
		*********************/
		$markup = apply_filters('wppizza_filter_post_prices_markup', $markup, $style, $post, $prices);


	return $markup;
	}

	/************************************************************************************************************
	*
	*
	*
	*	[ markup parts - post content]
	*
	*
	*	return @array
	************************************************************************************************************/
	function wppizza_filter_menu_loop_content($markup, $post , $txt, $additives,  $parameters, $is_rtl, $category, $articlecount){

			global $wppizza_options;

			/********************
				parameters for convenience
			*********************/
			/* classes filter parameters */
			$filter_parameters = $this->set_post_classes_filter_parameters($post, $parameters, $articlecount) ;

			/*********************
				get style
			*********************/
			$style = $filter_parameters['layout']['style'];

			/********************
				set content id
			*********************/
			$post_content_id = ''.WPPIZZA_SLUG.'-article-content-'.$post->ID.'';

			/*********************
				set content classes
			*********************/
			$post_content_class = array();
			/*general class*/
			$post_content_class[] = ''.WPPIZZA_SLUG.'-article-content';


			/*
				allow filtering
			*/
			$post_content_class = apply_filters('wppizza_filter_post_content_class', $post_content_class, $filter_parameters['post_data'], $filter_parameters['layout'], $filter_parameters['parameters'], $filter_parameters['articlecount'], $filter_parameters['session']);

			/*
				implode for output
			*/
			$post_content_class = implode(' ', $post_content_class);


			/*
				allow shortcodes and other filters
			*/
			$post_content_element = 'p';
			if(!empty($wppizza_options['layout']['apply_menu_items_content_filter'])){
				$post_content_element = 'div';
				$post->post_content = apply_filters('the_content', $post->post_content);
			}

			/*
				allow wppizza specific post content filter
			*/
			$post->post_content = apply_filters('wppizza_filter_post_content', $post->post_content , $post->ID);


		/*************************

			markup

		*************************/
		if(file_exists( WPPIZZA_TEMPLATE_DIR . '/markup/loop/posts.content.php')){
			require(WPPIZZA_TEMPLATE_DIR.'/markup/loop/posts.content.php');
		}else{
			require(WPPIZZA_PATH.'templates/markup/loop/posts.content.php');
		}

		/*********************
			apply filter if required
		*********************/
		$markup = apply_filters('wppizza_filter_post_content_markup', $markup, $style, $post->ID);

	return $markup;
	}
	/************************************************************************************************************
	*
	*
	*
	*	[ markup parts - permalink]
	*
	*
	*	return @array
	************************************************************************************************************/
	function wppizza_filter_menu_loop_permalink($markup, $post , $txt, $additives, $parameters, $is_rtl, $category, $articlecount){

			/********************
				parameters for convenience
			*********************/
			/* classes filter parameters */
			$filter_parameters = $this->set_post_classes_filter_parameters($post, $parameters, $articlecount) ;

			/*********************
				get style
			*********************/
			$style = $filter_parameters['layout']['style'];

			/********************
				set permalink id
			*********************/
			$post_permalink_id = ''.WPPIZZA_SLUG.'-permalink-'.$post->ID.'';

			/*********************
				set permalink classes
			*********************/
			$post_permalink_class = array();
			/*general class*/
			$post_permalink_class[] = ''.WPPIZZA_SLUG.'-permalink';


			/*
				allow filtering
			*/
			$post_permalink_class = apply_filters('wppizza_filter_post_permalink_class', $post_permalink_class, $filter_parameters['post_data'], $filter_parameters['layout'], $filter_parameters['parameters'], $filter_parameters['articlecount'], $filter_parameters['session']);

			/*
				implode for output
			*/
			$post_permalink_class = implode(' ', $post_permalink_class);


		/*************************

			markup

		*************************/
		if(file_exists( WPPIZZA_TEMPLATE_DIR . '/markup/loop/posts.permalink.php')){
			require(WPPIZZA_TEMPLATE_DIR.'/markup/loop/posts.permalink.php');
		}else{
			require(WPPIZZA_PATH.'templates/markup/loop/posts.permalink.php');
		}

		/*********************
			apply filter if required
		*********************/
		$markup = apply_filters('wppizza_filter_post_permalink_markup', $markup, $style);

	return $markup;
	}

	/************************************************************************************************************
	*
	*
	*
	*	[ markup parts - comments todo perhaps one day when we are really bored]
	*
	*
	*	return @array
	************************************************************************************************************/
	function wppizza_filter_menu_loop_comments($markup, $post , $txt, $additives, $parameters, $is_rtl, $category, $articlecount){
		//		if(is_single()){
		//			comments_template( '', true );
		//		}
		/***********TODO COMMENTS***********************************************************************/
				//Gather comments for a specific page/post
		//		$comments = get_comments(array(
		//			'post_id' => $post->ID,
		//			'status' => 'approve' //Change this to the type of comments to be displayed
		//		));
		//		ob_start();
		//		//Display the list of comments
		//		wp_list_comments(array(
		//			'per_page' => 10, //Allow comment pagination
		//			'reverse_top_level' => false //Show the latest comments at the top of the list
		//		), $comments);
		//		$wppizza_loop_markup .= ob_get_clean();




		//		/**should only disply on single posts*/
		//		ob_start();
		//			comments_template( '', true );
		//			comment_form();
		//		$wppizza_loop_markup .= ob_get_clean();

					//ob_start();
					//$wppizza_loop_markup = wp_list_comments( array( 'post_id' => $post->ID ) ));
					//$wppizza_loop_markup .= ob_get_clean();


				//	ob_start();
		 		//	comments_template( '', true );
				//	$wppizza_loop_markup .= ob_get_clean();

	return $markup;
	}
	/************************************************************************************************************
	*
	*
	*
	*	[ markup parts - article close - does/should not exist as editable template]
	*
	*
	*	return @array
	************************************************************************************************************/
	function wppizza_filter_menu_loop_article_close($markup, $post , $txt, $additives, $parameters, $is_rtl, $category, $articlecount){
			/********************
				parameters for convenience
			*********************/
			global $wppizza_options;
			$style = $parameters['global']['template']['style'];
			$post_count = $parameters['global']['post_count'];

			/**is this required/uesful actually as class added to article??*/
			//if(is_single()){
			//$markup .= "</div>";
			//}

		/*************************

			markup

		*************************/
		$markup['_article'] = '</article>';

		/*
			end sections for grid layout
		*/
		if(!is_single() && $style == 'grid' ){
			$section_count = (($articlecount)/$wppizza_options['layout']['style_grid_columns']);
			if(is_int($section_count) || $articlecount==$post_count){
				$markup['_section'] = '</section>';
			}
		}

	return $markup;
	}

	/************************************************************************************************************
	*
	*
	*
	*	[ markup parts - no_results]
	*
	*
	*	return @array
	************************************************************************************************************/
	function wppizza_filter_menu_no_results($markup, $txt, $additives, $parameters, $is_rtl, $category){
			/********************
				parameters for convenience
			*********************/
			$style = $parameters['global']['template']['style'];

			/*********************
				set classes
			*********************/
			$noresults_class = array();
			/*general class*/
			$noresults_class[] = ''.WPPIZZA_SLUG.'-no_results_found';
			/*selected style class*/
			$noresults_class[] = ''.WPPIZZA_SLUG.'-no_results_found-'.$style.'';
			/*rtl class*/
			$noresults_class[] = !empty($is_rtl) ? ''.WPPIZZA_SLUG.'-no_results_found-rtl' : '';

			/*
				allow filtering
			*/
			$noresults_class = apply_filters('wppizza_filter_noresults_class', $noresults_class);

			/*
				implode for output
			*/
			$noresults_class= implode(' ', $noresults_class);

		/*************************

			markup

		*************************/
		if(file_exists( WPPIZZA_TEMPLATE_DIR . '/markup/loop/no_results.php')){
			require(WPPIZZA_TEMPLATE_DIR.'/markup/loop/no_results.php');
		}else{
			require(WPPIZZA_PATH.'templates/markup/loop/no_results.php');
		}

		/*********************
			apply filter if required
		*********************/
		$markup = apply_filters('wppizza_filter_menu_noresults_markup', $markup, $style);

	return $markup;
	}
	/************************************************************************************************************
	*
	*
	*
	*	[ markup parts - additives]
	*
	*
	*	return @array
	************************************************************************************************************/
	function wppizza_filter_menu_additives($markup, $txt, $additives, $parameters, $is_rtl, $category){
			/********************
				parameters for convenience
			*********************/
			$style = $parameters['global']['template']['style'];

			/*********************
				set classes
			*********************/
			$class = array();
			/*general class*/
			$class[] = ''.WPPIZZA_SLUG.'-additives';
			/*selected style class*/
			$class[] = ''.WPPIZZA_SLUG.'-additives-'.$style.'';
			/*rtl class*/
			//$class[] = !empty($is_rtl) ? ''.WPPIZZA_SLUG.'-additives-rtl' : '';

			/*
				allow filtering
			*/
			$class = apply_filters('wppizza_filter_menu_additives_class', $class);

			/*
				implode for output
			*/
			$class= implode(' ', $class);


		/*************************

			markup

		*************************/
		if(file_exists( WPPIZZA_TEMPLATE_DIR . '/markup/loop/additives.php')){
			require(WPPIZZA_TEMPLATE_DIR.'/markup/loop/additives.php');
		}else{
			require(WPPIZZA_PATH.'templates/markup/loop/additives.php');
		}

		/*********************
			apply filter if required
		*********************/
		$markup = apply_filters('wppizza_filter_menu_additives_markup', $markup, $style);

	return $markup;
	}

	/************************************************************************************************************
	*
	*
	*
	*	[ markup  parts - pagination]
	*
	*	return @array
	*
	************************************************************************************************************/
	function wppizza_filter_menu_pagination($markup, $txt, $additives, $parameters, $is_rtl, $category){

			/********************
				parameters for convenience
			*********************/
			$max_num_pages = $parameters['global']['max_num_pages'];
			$force_get_the_posts_pagination = false;
			$style = $parameters['global']['template']['style'];

			/*********************
				set classes
			*********************/
			$pagination_class = array();
			/*general class*/
			$pagination_class[] = 'navigation';
			$pagination_class[] = ''.WPPIZZA_SLUG.'-navigation';
			/*selected style class*/
			$pagination_class[] = ''.WPPIZZA_SLUG.'-navigation-'.$style.'';
			/*rtl class*/
			$pagination_class[] = !empty($is_rtl) ? ''.WPPIZZA_SLUG.'-navigation-rtl' : '';
			/*
				allow filtering
			*/
			$pagination_class= apply_filters('wppizza_filter_menu_pagination_class', $pagination_class);

			/*
				implode for output
			*/
			$pagination_class= implode(' ', $pagination_class);

			/*
				available since WP 4.1
			*/
			if(function_exists('get_the_posts_pagination') && defined('WPPIZZA_WP41_POSTS_PAGINATION')){
				/*set flag*/
				$force_get_the_posts_pagination = true;

				/*filterable args*/
	 			$args = array(
	           		'mid_size' => 1,
	           		'prev_text' => $txt['previous'] ,
	           		'next_text' => $txt['next'],
	           		'screen_reader_text' => __( 'Posts navigation')
	        	);

				$args = apply_filters('wppizza_filter_menu_pagination_args', $args);

				/*
				NOTE: get_the_posts_pagination does not seem to want to listen to
				the max_num_pages of the query, so we capture what it is, force it
				to be the max_num_pages we need for the pagination, and reset it again after
				*/
				/*set $GLOBALS['wp_query']->max_num_pages to max num pages of this wppizza query*/
        		global $wp_query;
        		$get_max_num_pages = $wp_query->max_num_pages;/*allow to reset again*/
        		$wp_query->max_num_pages = $max_num_pages;
			}

		/*************************

			markup

		*************************/
		if(file_exists( WPPIZZA_TEMPLATE_DIR . '/markup/loop/pagination.php')){
			require(WPPIZZA_TEMPLATE_DIR.'/markup/loop/pagination.php');
		}else{
			require(WPPIZZA_PATH.'templates/markup/loop/pagination.php');
		}

		/*********************
			apply filter if required
		*********************/
		$markup = apply_filters('wppizza_filter_menu_pagination_markup', $markup, $style);

		/*
			get_the_posts_pagination available since WP 4.1 - reset wp_query (see notes above)
		*/
		if(function_exists('get_the_posts_pagination') && defined('WPPIZZA_WP41_POSTS_PAGINATION')){
			$wp_query->max_num_pages = $get_max_num_pages;
		}

	return $markup;
	}
	/************************************************************************************************************
	*
	*
	*
	*	[ markup - add_item_to_cart_button ]
	*
	*
	*
	************************************************************************************************************/
	function add_item_to_cart_button($atts){
		global $wppizza_options, $blog_id;

		$markup = '';

		/*missing or empty attributes**/
		if(!isset($atts['id']) ||  $atts['id']<=0 || $atts['id']=='' ){
			return $markup;
		}
		/**not the correct post type*/
		$posttype=get_post_type( $atts['id'] );
		if($posttype!=WPPIZZA_POST_TYPE){
			return $markup;
		}

		/*
			get first category associated to item
			unless specifically set
		*/
		if(empty($atts['category_id'])){
			$first_term = wp_get_post_terms($atts['id'], WPPIZZA_TAXONOMY);
			$first_term = reset($first_term);
			$category_id = $first_term->term_id;
		}else{
			$category_id = (int)$atts['category_id'];
		}

		/* get sizes for this menu item*/
		$meta = get_post_meta($atts['id'], WPPIZZA_SLUG, true);//, WPPIZZA_SLUG, true

		/*size id*/
		$size = $meta['sizes'];

		/*price/tier id*/
		$selectedPrice=0;//default to first

		/*check if size exists*/
		if(isset($atts['size']) && isset($meta['prices'][$atts['size']])){
			$selectedPrice = $atts['size'];
		}

		$dropdown='';
		$hasdropdown=false;
		if(empty($atts['single'])){/*if not forced to single button*/
			if(count($meta['prices'])>1){
				$hasdropdown=true;
				$dropdown.='<select id="wppizza-add-to-cart-size-'.$atts['id'].'" class="wppizza-add-to-cart-size">';
					foreach($meta['prices'] as $selPrice=>$value){
					$dropdown.='<option value="'.$size.'-'.$selPrice.'" '.selected($selPrice,$selectedPrice,false).'>'.$wppizza_options['sizes'][$size][$selPrice]['lbl'].'</option>';
					}
				$dropdown.='</select>';
			}
		}

		/****label - filterable****/
		$add_item_to_cart_button_label = apply_filters('wppizza_filter_add_item_to_cart_button_label', $wppizza_options['localization']['add_to_cart'], $atts);


		/****output****/
		$markup='<span id="wppizza-add-to-cart-btn-'.$atts['id'].'" class="wppizza-add-to-cart-btn-wrap">';
		 if($hasdropdown){
			/**dropdown if multiple*/
			$markup.=$dropdown;
			/*size id and tier*/
			$markup.='<input type="button" id="'.WPPIZZA_SLUG.'-add-to-cart-select_'.$blog_id.'-'.$category_id.'-'.$atts['id'].'" class="'.WPPIZZA_SLUG.'-add-to-cart-select '.WPPIZZA_SLUG.'-add-to-cart-btn" value="'.$add_item_to_cart_button_label.'" />';
		 }else{
			/*direct selection*/
			$markup.='<input type="button" id="'.WPPIZZA_SLUG.'-'.$blog_id.'-'.$category_id.'-'.$atts['id'].'-'.$size.'-'.$selectedPrice.'" class="'.WPPIZZA_SLUG.'-add-to-cart '.WPPIZZA_SLUG.'-add-to-cart-btn" value="'.$add_item_to_cart_button_label.'" />';
		 }
		$markup.='</span>';

		return $markup;
	}


	/*************************************************************************************************************************************************************************
	*
	*
	*
	*
	*	[helpers]
	*
	*
	*
	*
	*************************************************************************************************************************************************************************/

	/********************************************************************************
	*
	*	[get all terms]
	*
	*********************************************************************************/
	function get_wppizza_terms(){
		global $wppizza_options;
		if($wppizza_options==0){return;}
		/* get sorted categories */
		$this->wppizza_terms = WPPIZZA()->categories->wppizza_get_cats_hierarchy($wppizza_options['layout']['category_sort_hierarchy']);
	return ;
	}
	/********************************************************************************
	*
	*	[get all terms - with slugs as keys]
	*
	*********************************************************************************/
	function get_wppizza_terms_by_slug(){

		static $s=0;
		if($s==0){
			$terms=array();
			foreach($this->wppizza_terms as $term_id=>$term_sort){
				$get_term = get_term( $term_id, WPPIZZA_TAXONOMY );
				$terms[$get_term->slug]['id'] = $term_id;
				$terms[$get_term->slug]['slug'] = $get_term->slug;
				$terms[$get_term->slug]['name'] = $get_term->name;
				$terms[$get_term->slug]['description'] = $get_term->description;
				$terms[$get_term->slug]['parent'] = $get_term->parent;
				$terms[$get_term->slug]['count'] = $get_term->count;
			}
			$this->wppizza_terms_by_slug = $terms;
		}
		$s++;

	return $this->wppizza_terms_by_slug;
	}

	/********************************************************************************
	*
	*	[set paged var]
	*
	*********************************************************************************/
	function set_paged_var(){
		global $paged;

		/* home|frontpage uses "page" instead of "paged". no, dunno either*/
		if(is_home() || is_front_page()){
			$paged = (get_query_var('page')) ? get_query_var('page') : 1 ;
		}else{
			$paged = (get_query_var('paged')) ? get_query_var('paged') : 1 ;
		}

		return $paged;
	}

	/*************************************************************
	*
	*	custom class(es) to add for this template
	*
	*************************************************************/
	function set_template_item_class($atts){
		$item_class= !empty($atts['item_class']) ? wppizza_validate_alpha_only($atts['item_class'], true) : '';
	return $item_class;
	}
	/*************************************************************
	*
	*	set style to use for this template if set and available
	*	defaulting to what is set in wppizza->layout otherwise
	*
	*************************************************************/
	function set_template_style($atts){
		global $wppizza_options;

		$set_style = isset($atts['style']) ? wppizza_validate_alpha_only($atts['style']) : '' ;

		/*
			default to style set in wppizza->layout
			in case style set is invalid
		*/
		$style = $wppizza_options['layout']['style'];

		if(isset($set_style) && $set_style!=''){
			/*
				set as string
			*/
			if(!is_numeric($set_style)){

				/*
					styles registered in wppizza->layout
				*/
				$styles_registered = wppizza_public_styles();

				/*
					allow for filtering of allowed style attribute in shortcodes
				*/
				$styles_registered = apply_filters('wppizza_filter_registered_shortcode_styles', $styles_registered);

				if(isset($styles_registered[$set_style])){
					$style = $set_style;
				}
			}
			/**future use if using template id's*/
			if(is_numeric($set_style)){
				/*
					todo when it's available
				*/
			}
		}

	return $style;
	}

	/*************************************************************
	*
	*	which modules to display
	*	defaulting to 'title, thumbnail, prices, content' (in that order)
	*
	*	return array
	*************************************************************/
	function set_template_elements($atts, $style){

		global $wppizza_options;
		/*available modules*/
		$available_elements = array('title', 'thumbnail', 'category', 'prices', 'content', 'permalink', 'comments', 'additives');

		/*
			default modules in modules order - as string
			depending on style used
		*/
		$styles_registered = wppizza_public_styles();
		$styles_registered = apply_filters('wppizza_filter_registered_shortcode_styles', $styles_registered);
		$default_elements = $styles_registered[$style]['elements'];

		/*
			set include modules
		*/
		$set_include_elements = !empty($atts['elements']) ? $atts['elements'] : $default_elements ;
		$set_include_elements = explode(',', $set_include_elements);

		/*
			set exclude modules - setting by shortcode attributes currently not implemented
		*/
		$set_exclude_elements = array();
		//$set_exclude_elements = !empty($atts['elements_exclude']) ? $atts['elements_exclude'] : array() ;
		//$set_exclude_elements = explode(',', $set_exclude_elements);
		/*remove prices if specifically set in layout*/
		if(!empty($wppizza_options['prices_format']['hide_prices'])){
		 $set_exclude_elements['prices'] = 'prices';
		}

		/*
			loop and include/exclude
		*/
		$elements = array();
		foreach($set_include_elements as $element){
			$element = strtolower(trim($element));/*cast to lowercase*/
			if(in_array($element, $available_elements) && !in_array($element, $set_exclude_elements)){
				$elements[$element] = $element;
			}
		}

	return $elements;
	}

	/*************************************************************
	*
	*	which elements to use/show/hide, overriding whats set in layout
	*	unless specifically set in wppizza->layout
	*
	*	return array
	*************************************************************/
	function set_template_attributes($atts){
		global $wppizza_options;

		/*
			for convenience. map 0|off/left/right
		*/
		$map_position['off'] = 0;
		$map_position['0'] = 0;
		$map_position['left'] = 'left';
		$map_position['1'] = 'left';
		$map_position['right'] = 'right';
		$map_position['2'] = 'right';
		/*
			set main currency position
			0=off
			1 = left
			2 = right
		*/
		/*default to off first of all*/
		$main_currency_position = 0;
		/**att not set, use globsl setting*/
		if(!isset($atts['currency_main'])){
			/*not hiding*/
			if(empty($wppizza_options['prices_format']['hide_item_currency_symbol'])){
				/*right, default*/
				if(empty($wppizza_options['prices_format']['currency_symbol_left'])){
					$main_currency_position = $map_position[2];
				}else{
					$main_currency_position = $map_position[1];
				}
			}
		}
		/**set in attribute**/
		if(isset($atts['currency_main'])){
			$main_currency_position = $map_position[$atts['currency_main']];
		}

		/*main currency symbols*/
		$elements['currency_main'] = $main_currency_position ;/*main currency symbol*/
		$elements['currency_price'] = (isset($atts['currency_price']) && in_array($atts['currency_price'],array('0','left','right')))?  $map_position[$atts['currency_price']] : $map_position[$wppizza_options['prices_format']['show_currency_with_price']];/*currency next to each price, 0-off, 1-left 2-right*/

		/*images*/
		$elements['image_placeholder'] = isset($atts['image_placeholder']) ? wppizza_validate_boolean($atts['image_placeholder']) : $wppizza_options['layout']['placeholder_img'];
		$elements['image_prettyphoto'] = isset($atts['image_prettyphoto']) ? wppizza_validate_boolean($atts['image_prettyphoto']) : $wppizza_options['layout']['prettyPhoto'];

	return $elements;
	}

	/*************************************************************
	*
	*	set parameters we can use when filtering classes
	*	unless specifically set in wppizza->layout
	*
	*	return array
	*************************************************************/
	function set_post_classes_filter_parameters($post, $parameters, $articlecount){

		$filter_parameters = array();

		$filter_parameters['post_data'] = $post;

		$filter_parameters['layout'] = $parameters['global']['template'];

		$filter_parameters['parameters'] = array();
		$filter_parameters['parameters']['shortcode_type'] = $parameters['global']['shortcode_type'];
		$filter_parameters['parameters']['post_count'] = $parameters['global']['post_count'];
		$filter_parameters['parameters']['max_num_pages'] = $parameters['global']['max_num_pages'];
		$filter_parameters['parameters']['categoy'] = $parameters['global']['categoy'];

		$filter_parameters['articlecount'] = $articlecount;

		$filter_parameters['session'] = '--- [#to add perhaps#] ---';

	return	$filter_parameters;
	}
}
?>