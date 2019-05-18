<?php
/**
* WPPIZZA_MARKUP_ORDERINFO Class
*
* @package     WPPIZZA
* @subpackage  WPPizza Order Info
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
*	CLASS - WPPIZZA_MARKUP_ORDERINFO
*
*
*
* ================================================================================================================================= */

class WPPIZZA_MARKUP_ORDERINFO{

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

		/***********************
			skip if not cart or shortcode
		***********************/
		if($atts['type'] != 'cart' && $atts['type'] != 'orderinfo'){
			return;
		}
		/* omit if cart and not set /enabled */
		if($atts['type'] == 'cart' && empty($atts['orderinfo']) ){
			return;
		}
		
		
		
		global $wppizza_options;
		$txt = $wppizza_options['localization'];/*put all text varibles into something easier to deal with**/
		

		/***********************
			get all available discounts to display
		***********************/
		$pricing = array();
		/** 
			percentage discounts
		**/
		if($wppizza_options['order_settings']['discount_selected']=='percentage'){
			sort($wppizza_options['order_settings']['discounts']['percentage']['discounts']);
			foreach($wppizza_options['order_settings']['discounts']['percentage']['discounts'] as $key=>$value){
				if($value['discount']>0){
					$pricing['pricing_discounts'][] = sprintf('%1s <span>%2s</span> %3s <span>%4s</span>', $txt['spend'], wppizza_format_price($value['min_total']), $txt['save'], wppizza_output_format_percent($value['discount'], true));					
				}
			}
		}
		/** 
			value discount
		**/
		if($wppizza_options['order_settings']['discount_selected']=='standard'){
			/**get all available discounts to display***/
			sort($wppizza_options['order_settings']['discounts']['standard']['discounts']);
			foreach($wppizza_options['order_settings']['discounts']['standard']['discounts'] as $key=>$value){
				if($value['discount']>0){
					$pricing['pricing_discounts'][] = sprintf('%1s <span>%2s</span> %3s <span>%4s</span>', $txt['spend'], wppizza_format_price($value['min_total']), $txt['save'], wppizza_format_price($value['discount']));	
				}
			}
		}

		/** 
			delivery settings - minimum total
		**/
		if($wppizza_options['order_settings']['delivery_selected']=='minimum_total'){
			if($wppizza_options['order_settings']['delivery']['minimum_total']['min_total']>0){
				$pricing['pricing_delivery'] = sprintf('%1s <span>%2s</span>', $txt['free_delivery_for_orders_of'], wppizza_format_price($wppizza_options['order_settings']['delivery']['minimum_total']['min_total']));
			}else{
				$pricing['pricing_delivery'] = "".$txt['free_delivery']."";
			}
		}
		/** 
			delivery settings - per item
		**/
		if($wppizza_options['order_settings']['delivery_selected']=='per_item'){
			if($wppizza_options['order_settings']['delivery']['per_item']['delivery_per_item_free']>0){
				$pricing['pricing_delivery'] = sprintf('%1s <span>%2s</span>', $txt['delivery_charges_per_item'], wppizza_format_price($wppizza_options['order_settings']['delivery']['per_item']['delivery_charge_per_item']));
				$pricing['pricing_delivery_per_item_free'] = sprintf('%1s <span>%2s</span>', $txt['free_delivery_for_orders_of'], wppizza_format_price($wppizza_options['order_settings']['delivery']['per_item']['delivery_per_item_free']));
			}else{
				$pricing['pricing_delivery'] = sprintf('%1s <span>%2s</span>', $txt['delivery_charges_per_item'], wppizza_format_price($wppizza_options['order_settings']['delivery']['per_item']['delivery_charge_per_item']));
			}
		}


		/***********************
			get markup
		***********************/
		$markup = $this->get_markup($atts, $txt, $pricing);
		return $markup;

	}
	
	/***************************************
		[markup]
	***************************************/
	function get_markup($atts, $txt, $pricing){	
		static $static=0;$static++;
		
		/*********************
			set unique id
		*********************/			
		$id	= WPPIZZA_PREFIX.'-orders-info-'.$static;	
		
		/*********************
			set classes
		*********************/
		$class= array();
		$class[] = WPPIZZA_PREFIX.'-orders-info';
		if(!empty($atts['class'])){
			$class[] = esc_html($atts['class']);
		}		
		/*
			allow class filtering
			implode for output
		*/
		$class = apply_filters('wppizza_filter_orderinfo_widget_class', $class, $atts);		
		$class = trim(implode(' ', $class));
		
		/*********************
			set width , if defined 
		**********************/
		$style = (!empty($atts['width'])) ? ' style="width:'.esc_html($atts['width']).'"' : '' ;
		
		
		/* info - which parts to display */
		$info = array();
		/* default */
		if(empty($atts['info'])){
			$info['discounts'] = true;
			$info['deliveries'] = true;
		}
		/* only particular info set */
		if(!empty($atts['info'])){
			$iExplode=explode(',',$atts['info']);
			foreach($iExplode as $iKey){
				$key = trim($iKey);
				$info[$key] = true;
			}
		}
		
		/** discounts **/
		$discounts = array();
		if(!empty($pricing['pricing_discounts']) && !empty($info['discounts'])){
			$discounts = $pricing['pricing_discounts'];
		}
		
		
		/** delivery **/
		$deliveries = array();
		if(isset($pricing['pricing_delivery']) && !empty($info['deliveries'])){
			$deliveries['charges'] = $pricing['pricing_delivery'];
			if(isset($pricing['pricing_delivery_per_item_free'])){
				$deliveries['free']  = $pricing['pricing_delivery_per_item_free'];
			}
		}
		
		/*
			ini array
		*/
		$markup = array();

		/* 
			get markup
		*/
		if(file_exists( WPPIZZA_TEMPLATE_DIR . '/markup/global/orderinfo.php')){
			require(WPPIZZA_TEMPLATE_DIR.'/markup/global/orderinfo.php');
		}else{
			require(WPPIZZA_PATH.'templates/markup/global/orderinfo.php');
		}					
		/*
			apply filter if required and implode for output
		*/
		$markup = apply_filters('wppizza_filter_orderinfo_widget_markup', $markup, $atts, $static, $discounts, $deliveries);
		$markup = trim(implode('', $markup));

			
	return $markup;
	}

}
?>