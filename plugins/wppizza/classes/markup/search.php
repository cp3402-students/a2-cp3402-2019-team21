<?php
/**
* WPPIZZA_MARKUP_SEARCH Class
*
* @package     WPPIZZA
* @subpackage  WPPizza Search
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
*	CLASS - WPPIZZA_MARKUP_SEARCH
*
*
*
* ================================================================================================================================= */

class WPPIZZA_MARKUP_SEARCH{
	/* to pass onto get_search_form filter */
	private $shortcode_attributes = array();

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
	function attributes($atts=null){
		$this->shortcode_attributes = $atts;
		/**get markup**/
		$markup = $this->get_markup($atts);
		return $markup;
	}

	/***************************************
		[markup]
	***************************************/
	function get_markup($atts){
		static $unique_id=0;$unique_id++;
		/**only display for logged in users**/
		if(!empty($atts['loggedinonly']) && !is_user_logged_in()){
			return;
		}
		/*********************
			set unique id
		*********************/
		$id	= WPPIZZA_PREFIX.'-search-'.$unique_id;

		/*********************
			set classes
		*********************/
		$class= array();
		$class[] = WPPIZZA_PREFIX.'-search';
		if(!empty($atts['class'])){
			$class[] = esc_html($atts['class']);
		}
		$class = trim(implode(' ', $class));

		/*********************
			add/remove filter search vars
			add hidden wppizza input elm
			remove filter search vars
			reset to original or we will always have the post_type appended to the serach form once it has been run
		*********************/
		add_filter( 'get_search_form', array( $this, 'searchvars' ));
		$searchform = get_search_form(false);
		remove_filter( 'get_search_form', array( $this, 'searchvars' ));

		/*********************
			ini markup array
		*********************/
		$markup = array();
		/*********************
			get markup
		*********************/
		if(file_exists( WPPIZZA_TEMPLATE_DIR . '/markup/global/search.php')){
			require(WPPIZZA_TEMPLATE_DIR.'/markup/global/search.php');
		}else{
			require(WPPIZZA_PATH.'templates/markup/global/search.php');
		}
		/*********************
			apply filter if required and implode for output
		*********************/
		$markup = apply_filters('wppizza_filter_search_widget_markup', $markup, $atts, $unique_id);
		$markup = trim(implode('', $markup));

	return $markup;
	}




	/***************************************
		[add set search variables]
	***************************************/
    function searchvars( $markup ) {
    	global $wppizza_options;
    	$atts = $this->shortcode_attributes;
    	if(!empty($atts['include'])){
    		$val=$atts['include'];// note: even if other , comma separated,  custom_post_type is set in widget, only registered post types will be included
    		$inc=explode(",", $atts['include']);

    		if(in_array(WPPIZZA_POST_TYPE, $inc)){
    			/**if we have set another permalink for single mnu items, rewrite this here so the query finds wppizza after all**/
				if($wppizza_options['settings']['single_item_permalink_rewrite']!=''){
					$key = array_search(WPPIZZA_POST_TYPE, $inc);
					$inc[$key]=$wppizza_options['settings']['single_item_permalink_rewrite'];
					$val=implode(",",$inc);
				}
    			$hidden_wppizza_search_variables='<input type="hidden" name="post_type" value="'.$val.'" />'.PHP_EOL.'</form';/*leave form tag open here to allow for spaces**/

				$markup = str_ireplace('</form', $hidden_wppizza_search_variables, $markup);
    		}else{
    			/**
    				even if we do not have wppizza menu items enabled in the widget, add hidden values (post types) anyway
    				(could be part of the if() above, but for sanity put it into the "else" here )
    			**/
    			$hidden_wppizza_search_variables='<input type="hidden" name="post_type" value="'.$val.'" />'.PHP_EOL.'</form';/*leave form tag open here to allow for spaces**/
				$markup = str_ireplace('</form', $hidden_wppizza_search_variables, $markup);
    		}

    	}
        return $markup;
    }
}
?>