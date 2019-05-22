<?php
/**
* WPPIZZA_MARKUP_MINICART Class
*
* @package     WPPIZZA
* @subpackage  WPPizza Minicart
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
*	CLASS - WPPIZZA_MARKUP_MINICART
*
*
*
* ================================================================================================================================= */

class WPPIZZA_MARKUP_MINICART{

	/******************************************************************************
	*
	*
	*	[construct]
	*
	*
	*******************************************************************************/
	function __construct() {		
		/** set localized js vars **/
		add_filter('wppizza_filter_js_localize', array( $this, 'js_localize'));	
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
		static $markup = null;

		/** no minicart on orderpage as it's pointless there**/
		if(wppizza_is_orderpage()){
			return;	
		}			
		/** do only once **/
		if($markup == null){
			/**set flag**/
			$markup = true;			
			/** add to footer **/
			add_action('wp_footer', array( $this, 'get_markup'), 99);
		}
	return;
	}

	/*******************************************************
     *	localize js vars adding to [crt] key
    *******************************************************/
	function js_localize($js_vars){

		/** no minicart on orderpage as it's pointless there**/
		if(wppizza_is_orderpage()){
			return $js_vars;	
		}

		global $wppizza_options;	

		/**minicart max width**/
		if(!empty($wppizza_options['layout']['minicart_max_width_active'])){
			$js_vars['crt']['mMaxWidth']	=	$wppizza_options['layout']['minicart_max_width_active'];
		}
		/**minicart body padding top**/
		if(!empty($wppizza_options['layout']['minicart_elm_padding_top'])){
			$js_vars['crt']['mPadTop']	=	$wppizza_options['layout']['minicart_elm_padding_top'];
			/**minicart padding to distinct element**/
			if(!empty($wppizza_options['layout']['minicart_elm_padding_selector'])){
				$js_vars['crt']['mPadElm']	=	$wppizza_options['layout']['minicart_elm_padding_selector'];
			}
		}
		/**minicart add to element**/
		if(!empty($wppizza_options['layout']['minicart_add_to_element'])){
			$js_vars['crt']['mElm']	=	$wppizza_options['layout']['minicart_add_to_element'];
		}
		/**minicart always displayed**/
		if(!empty($wppizza_options['layout']['minicart_always_shown'])){
			$js_vars['crt']['mStatic']	=	1;
		}	
		
	return $js_vars;
	}


	/***************************************
		[markup]
		uses the same classes as totals shortcode
		to do the ajax call only once
		even whan both shortcodes are used
	***************************************/
	function get_markup($atts = null){	
		global $wppizza_options;


		/*********************
			set unique id
		*********************/			
		$id	= WPPIZZA_PREFIX.'-minicart';	
		
		/*********************
			set container classes
		*********************/
		$class= array();
		$class[] = WPPIZZA_PREFIX.'-totals-container' ;

		/* add empty/full class ident if nothing/something in cart when loading - will also getadded/removed by js*/
		$class[] = (wppizza_cart_is_empty()) ? WPPIZZA_PREFIX.'-totals-no-items' : WPPIZZA_PREFIX.'-totals-has-items' ;	
		/**fixed to top or relative to element set  **/
		$class[]=empty($wppizza_options['layout']['minicart_add_to_element']) ? ''.WPPIZZA_PREFIX.'-minicart-fixed' : ''.WPPIZZA_PREFIX.'-minicart-relative';
		/**always visible or not**/
		$class[]=!empty($wppizza_options['layout']['minicart_always_shown']) ? ''.WPPIZZA_PREFIX.'-minicart-static' : '' ;
		/**position**/
		$class[]=($wppizza_options['layout']['minicart_position'] == 'bottom') ? ''.WPPIZZA_PREFIX.'-minicart-bottom' : '' ;

		if(!empty($atts['class'])){
			$class[] = esc_html($atts['class']);
		}
		/* implode */	
		$class = trim(implode(' ', $class));	

		/*********************
			set opacity classes
		*********************/		
		$opacity_class= array();
		$opacity_class[] = WPPIZZA_PREFIX.'-totals-opacity' ;	
		/* implode */	
		$opacity_class = trim(implode(' ', $opacity_class));
	
			
		/*********************
			set markup components
		*********************/	
		/**get links to order page*/
		$orderpage = wppizza_page_links('orderpage');
		/** checkout button **/
		if(!empty($wppizza_options['layout']['minicart_checkout'])  && !empty($orderpage)){
			$checkoutbutton = true;
			$checkoutbutton_class=''.WPPIZZA_PREFIX.'-totals-checkout-button';
			$checkoutbutton_title=''.$wppizza_options['localization']['place_your_order'].'';
		}

		/*item count enabled/position**/
		if(!empty($wppizza_options['layout']['minicart_itemcount'])){
			if($wppizza_options['layout']['minicart_itemcount']=='left'){
				$count_left = true;
				$count_left_class=''.WPPIZZA_PREFIX.'-totals-itemcount';
			}
			if($wppizza_options['layout']['minicart_itemcount']=='right'){
				$count_right = true;
				$count_right_class=''.WPPIZZA_PREFIX.'-totals-itemcount';				
			}
		}
		/**
			value full order
		**/
		$total = true;
		$total_class = WPPIZZA_PREFIX.'-totals-order' ;
		
		/**
			allow for view cart button to show cart contents 
		**/
		if(!empty($wppizza_options['layout']['minicart_viewcart'])){
			/*create view cart button */
			$viewcart=true;
			$viewcart_class=''.WPPIZZA_PREFIX.'-totals-viewcart-button';
			$viewcart_title=$wppizza_options['localization']['view_cart'];
			
			/*create hidden cart span adding classes always using full cart*/
			$cart_class = array();
			$cart_class[] = ''.WPPIZZA_PREFIX.'-totals-cart';
			$cart_class[] = ''.WPPIZZA_PREFIX.'-totals-cart-items';
			$cart_class[] = ''.WPPIZZA_PREFIX.'-totals-cart-summary';			

			$cart= true;
			$cart_class = implode(' ',$cart_class);
		}

		/*********************
			ini markup array
		*********************/				
		$markup = array();
	
		/*********************
			get markup
		*********************/			
		if(file_exists( WPPIZZA_TEMPLATE_DIR . '/markup/cart/minicart.php')){
			require(WPPIZZA_TEMPLATE_DIR.'/markup/cart/minicart.php');
		}else{
			require(WPPIZZA_PATH.'templates/markup/cart/minicart.php');
		}					

		/*********************
			apply filter if required and implode for output
		*********************/				
		$markup = apply_filters('wppizza_filter_minicart_widget_markup', $markup, $atts);
		$markup = trim(implode('', $markup));
	/* echo in wp_footer */
	echo $markup;
	}
}
?>