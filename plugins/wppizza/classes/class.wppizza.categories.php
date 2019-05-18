<?php
/**
* WPPIZZA_CATEGORIES Class
*
* @package     WPPIZZA
* @subpackage  WPPIZZA_CATEGORIES
* @copyright   Copyright (c) 2015, Oliver Bach
* @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
* @since       3.0
*
*/
if ( ! defined( 'ABSPATH' ) ) exit;/*Exit if accessed directly*/


/************************************************************************************************************************
*
*
*	WPPIZZA_CATEGORIES_SORTED
*
*
************************************************************************************************************************/
class WPPIZZA_CATEGORIES_SORTED{


	function __construct() {
		/*sort categories**/
		add_action('init', array( $this, 'wppizza_add_get_terms_filter'));
	}

	/****************************************************************************************************************
	*
	*
	*	[]	
	*
	*
	****************************************************************************************************************/
		/*****************************************************
			add get term filter where and as required
		*****************************************************/
		function wppizza_add_get_terms_filter() {
			/*do not run when deleting tags via bulk actions*/
			if(empty($_POST['delete_tags'])){
				add_filter('get_terms', array($this,'wppizza_do_sort_custom_posts_category'), 10, 2);
			}
		}
		/*****************************************************
			return wppizza category sort order according to
			custom sorting sort order
		*****************************************************/
		function wppizza_do_sort_custom_posts_category($terms, $taxonomy) {
			global $wppizza_options;
			/**
				should be bypassed when creating/deleting categories (i.e when there's a post[action])			
			**/
			$is_admin = (is_admin() && isset($_GET['taxonomy']) && $_GET['taxonomy']==WPPIZZA_TAXONOMY && in_array(WPPIZZA_TAXONOMY, $taxonomy) ) ? true : false;
			/** frontend **/
			$is_frontent = (!is_admin() && in_array(WPPIZZA_TAXONOMY, $taxonomy)) ? true : false;

			/*
				should be bypassed when creating/deleting categories (i.e when there's a post[action])
			*/
			if($is_admin || $is_frontent){
				/*
					 in cate category order is messed up / not set , return at least default array
				*/
				if(empty($wppizza_options['layout']['category_sort_hierarchy']) || count($wppizza_options['layout']['category_sort_hierarchy'])==0){
					return	$terms;				
				}

				/*
					return categories depending on sort order	set
				*/				
				$termArray=array();
				foreach($terms as $k=>$term){
					/*
						do not use !empty but isset as the value might be 0
					*/
					if(is_object($term) && isset($wppizza_options['layout']['category_sort_hierarchy'][$term->term_id])){
						$key = $wppizza_options['layout']['category_sort_hierarchy'][$term->term_id];
						$termArray[$key]=$term;
					}
				}
				ksort($termArray);

				return	$termArray;
			}else{
				return	$terms;
			}
		}
		/*************************************************************************************************
			[get fully sorted WP recursive hierarchy (sorted by WP default -> name asc)
			of wppizza categories and subcategories in a flat array with key=>categoryId,  val=>sortorder]
			(if $custom_sort=false and $full_details=false)

			if $custom_sort=array() - typically set 'category_sort_hierarchy' variable OR $full_details=true),
			it will returns full category object
			(currently unused)

			wppizza_do_sort_custom_posts_category can be used to return categories
			in custom set order set in ['category_sort_hierarchy']

		*************************************************************************************************/
		function wppizza_get_cats_hierarchy($custom_sort=false, $full_details=false){


			/*custom sort set*/
			if(!empty($custom_sort)){$full_details=true;}

			/*ini sort order*/
			$sort=0;
			/*ini results array*/
			$cats_in_order=array();
			/*run query, getting all wppizza parent categories*/
			$args=array('orderby' => 'name', 'order' => 'ASC', 'hide_empty' => 0, 'parent' => 0, 'taxonomy' => WPPIZZA_TAXONOMY	);
			$default_sort_cats = get_categories($args);
			$wpml_sort_cats_lang = array();/*categories for all other non current languages if WPML category translation*/

			/**if site is WPML  enabled, get_categories only ever returns cats of currently active language , so let's get all others too*/
			if(function_exists('icl_get_languages')){
				global $sitepress;
				$languages = icl_get_languages();
				/**loop through non current languages to get categories**/
				foreach($languages as $lang_code=>$lang_arr){
					if($lang_code!=ICL_LANGUAGE_CODE){
						$sitepress->switch_lang($lang_code);
						/*get cats for that language**/
						$wpml_sort_cats_lang[$lang_code] = get_categories($args);
					}
				}
				/**make sure we switch back to current language when done**/
				$sitepress->switch_lang(ICL_LANGUAGE_CODE);
			}


			/**loop through parent cats - non wpml or if wpml , current language*/
			foreach($default_sort_cats as $cat){

				/**add parent to results array*/
				if($full_details){
					$cats_in_order[$sort]= array('sort'=>$sort, 'id'=>$cat->term_id, 'parent'=>$cat->parent, 'name'=>$cat->name, 'slug'=>$cat->slug, 'description'=>$cat->description);
				}else{
					$cats_in_order[$cat->term_id]= $sort;
				}
				$sort++;/*advance sorter*/

				/**get subcategory tree for this parent**/
				$get_category_tree= $this->wppizza_cat_tree_recursive( $cat->term_id, $sort, $full_details);
				if(!empty($get_category_tree)){
					/*add full tree to parent cat*/
					$cats_in_order+=$get_category_tree;
					$sort+=count($get_category_tree);/*advance sorter*/
				}
			}
			
				
			/**************************************************
				if cats are wpml enabled and there's more
				than one category language, add wpml cats tree
				for categories in this language
			****************************************************/
			if(count($wpml_sort_cats_lang)>=1){
				foreach($wpml_sort_cats_lang as $lang_code=>$default_sort_cats){
					$sitepress->switch_lang($lang_code);
					foreach($default_sort_cats as $cat){
						/**add parent to results array*/
						if($full_details){
							$cats_in_order[$cat->term_id]= array('sort'=>$sort, 'id'=>$cat->term_id, 'parent'=>$cat->parent, 'name'=>$cat->name, 'slug'=>$cat->slug, 'description'=>$cat->description);
						}else{
							$cats_in_order[$cat->term_id]= $sort;
						}
						$sort++;/*advance sorter*/

						/**get subcategory tree for ththis parent**/
						$get_category_tree= $this->wppizza_cat_tree_recursive( $cat->term_id, $sort, $full_details);

						if(!empty($get_category_tree)){
							/*add full tree to parent cat*/
							$cats_in_order+=$get_category_tree;
							$sort+=count($get_category_tree);/*advance sorter*/
						}
					}
				}
				/**make sure we switch back to current language when done**/
				$sitepress->switch_lang(ICL_LANGUAGE_CODE);
			}


			/*custom sorting */
			if(!empty($custom_sort) && is_array($custom_sort)){
				$cats_in_order_custom_sort=array();
				foreach($cats_in_order as $key=>$arr){
					$cSort=$custom_sort[$arr['id']];/*set key [sort] according to custom sort */
					$cats_in_order_custom_sort[$arr['id']]=array();
					$cats_in_order_custom_sort[$arr['id']]['sort']=$cSort;
					$cats_in_order_custom_sort[$arr['id']]['id']=$arr['id'];
					$cats_in_order_custom_sort[$arr['id']]['parent']=$arr['parent'];
					$cats_in_order_custom_sort[$arr['id']]['name']=$arr['name'];
					$cats_in_order_custom_sort[$arr['id']]['slug']=$arr['slug'];
					$cats_in_order_custom_sort[$arr['id']]['description']=$arr['description'];
				}
				asort($cats_in_order_custom_sort);/*sort by sort flag, key = catid*/
				$cats_in_order=$cats_in_order_custom_sort;/*set sorted*/
			}

		/*return sorted cats - either default or by custom sort array*/
		return $cats_in_order;
		}
		/**************************************************
			recursively get hierarchy tree for parent category
		**************************************************/
		function wppizza_cat_tree_recursive( $cat, $sort, $full_details=false ) {
			/**get categories of this parent*/
			$args=array('orderby' => 'name', 'order' => 'ASC', 'hide_empty' => 0, 'parent' => $cat, 'taxonomy' => WPPIZZA_TAXONOMY);
			$sub_cat_tree = get_categories($args);

			if($sub_cat_tree){
			foreach( $sub_cat_tree as $cat ){

				/**add to results**/
				if($full_details){
					$cats_in_order[$sort]=array('sort'=>$sort, 'id'=>$cat->term_id, 'parent'=>$cat->parent, 'name'=>$cat->name, 'slug'=>$cat->slug, 'description'=>$cat->description);
				}else{
					$cats_in_order[$cat->term_id]= $sort;
				}

				$sort++;/*advance sort*/
				/*recursive*/
				$process= $this->wppizza_cat_tree_recursive( $cat->term_id, $sort, $full_details);
				if(!empty($process)){
					$cats_in_order+=$process;//$sort as key
					$sort+=count($process);
				}
			}}

			if(!empty($cats_in_order)){
				return $cats_in_order;
			}
			return;
		}

		/*************************************************
			get wppizza taxonomy parents 
		*************************************************/		
		function wppizza_get_taxonomy_parents($cat_id, $separator = '/', $path='full', $link = false, $nicename = false, $visited = array() ){
			/* off */
			if($path == 'none'){return '';}
			
			/* topmost */
			if($path == 'topmost'){
				$explode_separator = '[~#/#|!|#/#~]';/* some highly unlikely to be used string in category name as separator */
				$topmost = $this->wppizza_get_taxonomy_parents_markup( $cat_id, $explode_separator , $link , $nicename, $visited, 'full' );
				$topmost = explode($explode_separator, $topmost);
				return $topmost[0];
			}

			$chain = $this->wppizza_get_taxonomy_parents_markup( $cat_id, $separator , $link , $nicename, $visited, $path );
			
			/* parent only */
			if($path == 'parent'){
				return $chain;
			}
			
			/* remove trailing separator */
			$separator_length = strlen($separator);
	        $chain = substr($chain, 0, -$separator_length); 
	        
	        return $chain;
		}
				
		
		function wppizza_get_taxonomy_parents_markup( $id, $separator , $link , $nicename, $visited, $path  ) {
	        $chain = '';
	        $parent = get_term( $id, WPPIZZA_TAXONOMY);
	        if ( is_wp_error( $parent ) ){
	                return $parent;
	        }
	        	        
	        if ($nicename){	
	        	$name = $parent->slug;
	        }else{
				$name = $parent->name;
	        }
			/* parent only */
		    if($path == 'parent'){
		    	return $name;
		    }
	
	        if ( $parent->parent && ( $parent->parent != $parent->term_id ) && !in_array( $parent->parent, $visited ) ) {
				$visited[] = $parent->parent;
				$chain .= $this->wppizza_get_taxonomy_parents_markup( $parent->parent, $separator, $link, $nicename, $visited, $path  );
	        }
	
			// to do perhaps if needed one day
	        //if ($link){
				//$chain .= '<a href="' . esc_url( get_term_link( $parent->term_id ) ) . '">'.$name.'</a>' . $separator;
	        //}else{
				$chain .= $name . $separator;
	        //}
	        	        			    	        
	        return $chain;
		}		
		
		
		/*************************************************
			check if wppizza cats (wppizza_menu taxonomy) 
			are enabled for wpml translation
		*************************************************/
		function wppizza_wpml_cats_translated($taxonomy){
			$taxonomy_translated=false;
			/**check if wpml enabled first **/
			if ( function_exists('icl_object_id') ){
				global $sitepress_settings;
				/**if taxonomy enabled for translation, return true*/
				if(!empty($sitepress_settings['taxonomies_sync_option'][$taxonomy])){
					$taxonomy_translated=true;	
				}
			}	
			return $taxonomy_translated;
		}		
}
?>