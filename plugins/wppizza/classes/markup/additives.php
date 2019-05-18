<?php
/**
* WPPIZZA_TEMPLATES_ADDITIVES Class
*
* @package     WPPIZZA
* @subpackage  WPPizza Additives
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
*	CLASS - WPPIZZA_TEMPLATES_ADDITIVES
*
*
*
* ================================================================================================================================= */

class WPPIZZA_MARKUP_ADDITIVES{

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
		/**get markup**/
		$markup = $this->get_markup($atts);
		return $markup;
	}

	/***************************************
		[markup]
	***************************************/
	function get_markup($atts){
		static $unique_id=0;$unique_id++;

		/*********************
			get all additives
		*********************/
		$additives = wppizza_all_additives();

		/*********************
			set unique id
		*********************/
		$id	= WPPIZZA_PREFIX.'-additives-'.$unique_id;

		/*********************
			set/add classes using attributes
		*********************/
		$class= array();
		$class[] = WPPIZZA_PREFIX.'-additives';
		if(!empty($atts['class'])){
			$class[] = esc_html($atts['class']);
		}
		/*
			implode for output
		*/
		$class = trim(implode(' ', $class));

		/*********************
			ini markup array
		*********************/
		$markup = array();

		/*********************
			get markup
		*********************/
		if(file_exists( WPPIZZA_TEMPLATE_DIR . '/markup/global/additives.php')){
			require(WPPIZZA_TEMPLATE_DIR.'/markup/global/additives.php');
		}else{
			require(WPPIZZA_PATH.'templates/markup/global/additives.php');
		}

		/*********************
			apply filter if required and implode for output
		*********************/
		$markup = apply_filters('wppizza_filter_additives_widget_markup', $markup, $atts, $unique_id, $additives);
		$markup = trim(implode('', $markup));


	return $markup;
	}

}
?>