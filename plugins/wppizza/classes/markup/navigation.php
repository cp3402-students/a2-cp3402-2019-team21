<?php
/**
* WPPIZZA_MARKUP_NAVIGATION Class
*
* @package     WPPIZZA
* @subpackage  WPPizza Navigation
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
*	CLASS - WPPIZZA_MARKUP_NAVIGATION
*
*
*
* ================================================================================================================================= */

class WPPIZZA_MARKUP_NAVIGATION{

	/******************************************************************************
	*
	*
	*	[construct]
	*
	*
	*******************************************************************************/
	function __construct() {
	}

	/******************************************************************************
	*
	*
	*	[methods]
	*
	*
	*******************************************************************************/
	/***************************************
		[apply attributes]
	***************************************/
	function attributes($atts = null){
		global $wppizza_options;

		extract(shortcode_atts(array('title' => ''), $atts));
		$child_of=0;
		/*if only getting categories of a particular parent*/
		if(isset($atts['parent'])){
			$query=get_term_by('slug', $atts['parent'], WPPIZZA_TAXONOMY);
			if($query){
				$child_of=$query->term_id;
			}
		}

		$args = array(
		  'taxonomy'     => WPPIZZA_TAXONOMY,
		  'orderby'      => 'name',
		  'show_count'   => 0,      // 1 for yes, 0 for no
		  'pad_counts'   => 0,      // 1 for yes, 0 for no
		  'hierarchical' => 1,      // 1 for yes, 0 for no
		  'title_li'     => $title,
		  'depth'     	 => 0,
		  'exclude'      => (isset($atts['exclude'])) ? $atts['exclude'] : '' ,
		  'child_of'     => $child_of,
		  'show_option_none'   => empty($atts['as_dropdown']) ? __('Nothing here') : $wppizza_options['localization']['widget_navigation_dropdown_placeholder']  ,
		  'hide_empty'   => 1,
		  'echo'   => 0 ,				// keep as variable
		  'walker'	=> ( !empty($atts['as_dropdown']) ? new WPPIZZA_Walker_CategoryDropdown() : new WPPIZZA_Walker_Category() )/* set (filterable) walkers for dropdown or (current dummy) for normal list*/
		);

		/***add a filter if required*****/
		$args = apply_filters('wppizza_filter_navigation_widget_arguments', $args, $atts);

		/**get markup**/
		$markup = $this->get_markup($args, $atts);
		return $markup;

	}

	/***************************************
		[markup]
	***************************************/
	function get_markup($args, $atts){
		static $unique_id=0; $unique_id++;
	/*
	TODO make sure
	add_filter('terms_clauses', array($this,'wppizza_term_filter'), '', 1);
	is applied here from somewhere, probably global filters
	*/

	/*********************
		set id
	*********************/
	$id = ''.WPPIZZA_PREFIX.'-categories-'.$unique_id;

	/*********************
		set classes
	*********************/
	$class = array();
	$class[] = ''.WPPIZZA_PREFIX.'-categories';
	/*
		allow class filtering
		implode for output
	*/
	$class = apply_filters('wppizza_filter_navigation_widget_class', $class, $atts, $unique_id);
	$class = trim(implode(' ', $class));


	/*********************
		get markup
	*********************/
	$markup = array();
	

		/*
			as list
		*/
		if(empty($atts['as_dropdown'])){
			if(file_exists( WPPIZZA_TEMPLATE_DIR . '/markup/global/navigation.list.php')){
				require(WPPIZZA_TEMPLATE_DIR.'/markup/global/navigation.list.php');
			}else{
				require(WPPIZZA_PATH.'templates/markup/global/navigation.list.php');
			}

			/**filterable**/
			$markup = apply_filters('wppizza_filter_navigation_widget_markup', $markup, $atts, $unique_id);
		}

		/*
			as dropdown - using WPPIZZA_Walker_CategoryDropdown to redirect on change with links
		*/
		if(!empty($atts['as_dropdown'])){
			if(file_exists( WPPIZZA_TEMPLATE_DIR . '/markup/global/navigation.dropdown.php')){
				require(WPPIZZA_TEMPLATE_DIR.'/markup/global/navigation.dropdown.php');
			}else{
				require(WPPIZZA_PATH.'templates/markup/global/navigation.dropdown.php');
			}
			/**filterable**/
			$markup = apply_filters('wppizza_filter_navigation_widget_dropdown_markup', $markup, $atts, $unique_id);
		}


		/*implode for output after filter ()*/
		$markup = trim(implode(PHP_EOL, $markup));

		return $markup;
	}

}
?>