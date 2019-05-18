<?php
/**
* WPPIZZA_TEMPLATES_TOTALS Class
*
* @package     WPPIZZA
* @subpackage  WPPizza Totals (widget/shortcode)
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
*	CLASS - WPPIZZA_TEMPLATES_TOTALS
*
*
*
* ================================================================================================================================= */

class WPPIZZA_MARKUP_TOTALS{

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
		static $unique_id = 0; $unique_id++;
		global $wppizza_options;

		/*********************
			set unique id
		*********************/			
		$id	= WPPIZZA_PREFIX.'-totals-'.$unique_id;	
		
		/*********************
			set classes
		*********************/
		$class= array();
		$class[] = WPPIZZA_PREFIX.'-totals-container' ;
		$class[] = WPPIZZA_PREFIX.'-totals';
		if(!empty($atts['class'])){
			$class[] = esc_html($atts['class']);
		}		
		$class = trim(implode(' ', $class));			
			
		/*********************
			set markup components
		*********************/	
		/**get links to order page*/
		$orderpage = wppizza_page_links('orderpage');
		/** checkout button **/
		if(!empty($atts['checkout'])  && !empty($orderpage)){
			$checkoutbutton = true;
			$checkoutbutton_class=''.WPPIZZA_PREFIX.'-totals-checkout-button';
			$checkoutbutton_title=''.$wppizza_options['localization']['place_your_order'].'';
		}

		/*item count enabled/position**/
		if(!empty($atts['itemcount'])){
			if($atts['itemcount']=='left'){
				$count_left = true;
				$count_left_class=''.WPPIZZA_PREFIX.'-totals-itemcount';
			}
			if($atts['itemcount']=='right'){
				$count_right = true;
				$count_right_class=''.WPPIZZA_PREFIX.'-totals-itemcount';				
			}
		}
		/**
			value
		**/
		$total = true;
		$total_class = ( !empty($atts['value']) && $atts['value']=='items' ) ? WPPIZZA_PREFIX.'-totals-items' : WPPIZZA_PREFIX.'-totals-order' ;
		
		/**
			allow for view cart button to show cart contents 
		**/
		if(!empty($atts['viewcart'])){
			/*create view cart button */
			$viewcart=true;
			$viewcart_class=''.WPPIZZA_PREFIX.'-totals-viewcart';
			//$viewcart_title = $wppizza_options['localization']['view_cart'];
			
			/*create hidden cart span */
			$cart_class = array();
			$cart_class[] = ''.WPPIZZA_PREFIX.'-totals-cart';
			if(!empty($atts['cart_view'])){
				$xCartView = explode(',',$atts['cart_view']);
				foreach($xCartView as $module){
					if(strtolower(trim($module)) == 'itemised'){
						$cart_class[] = ''.WPPIZZA_PREFIX.'-totals-cart-items';	
					}
					if(strtolower(trim($module)) == 'summary'){
						$cart_class[] = ''.WPPIZZA_PREFIX.'-totals-cart-summary';	
					}
				}
				
			}else{
				$cart_class[] = ''.WPPIZZA_PREFIX.'-totals-cart-items';
				$cart_class[] = ''.WPPIZZA_PREFIX.'-totals-cart-summary';
			}

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
		if(file_exists( WPPIZZA_TEMPLATE_DIR . '/markup/global/totals.php')){
			require(WPPIZZA_TEMPLATE_DIR.'/markup/global/totals.php');
		}else{
			require(WPPIZZA_PATH.'templates/markup/global/totals.php');
		}					
		/* 
			remove display of 
			price if set by attribute (in case we only want count)
		*/
		if(!empty($atts['count_only'])){
			unset($markup['total']);
		}
		/*********************
			apply filter if required and implode for output
		*********************/				
		$markup = apply_filters('wppizza_filter_totals_widget_markup', $markup, $atts, $unique_id);
		$markup = trim(implode('', $markup));

	return $markup;
	}
}
?>