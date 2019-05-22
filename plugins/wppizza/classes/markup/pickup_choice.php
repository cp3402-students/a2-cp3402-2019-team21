<?php
/**
* WPPIZZA_MARKUP_PICKUP Class
*
* @package     WPPIZZA
* @subpackage  WPPizza Pickup/Delivery toggles
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
*	CLASS - WPPIZZA_MARKUP_PICKUP
*
*
*
* ================================================================================================================================= */

class WPPIZZA_MARKUP_PICKUP_CHOICE{

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
	function attributes($atts, $type){
		global $wppizza_options;
		/** set as var - php 5.3 */
		$shop_open = wppizza_is_shop_open();
		/*
			allow the pickup toggle to always be shown, even if shop is closed
			provided toggle is enabled in cart/orderpage in the first place
		*/
		if($type == 'cart' || $type == 'orderpage' ){
			$force_pickup_toggle = apply_filters('wppizza_filter_force_pickup_toggle_display', false);
			if($type == 'cart'){
				$force_pickup_toggle = 	($wppizza_options['order_settings']['order_pickup_display_location'] == 2) ? false : $force_pickup_toggle ;//check if actually enabled in cart in the first place
			}
			if($type == 'orderpage'){
				$force_pickup_toggle = 	($wppizza_options['order_settings']['order_pickup_display_location'] == 1) ? false : $force_pickup_toggle ;//check if actually enabled on orderpage in the first place
			}
		}
		/* no toggle if closed, not displayed on a particular page , not enabled anyway and not force enabled */
		if(
			empty($wppizza_options['order_settings']['order_pickup']) || /* pickup/switch not enabled */
			$wppizza_options['order_settings']['delivery_selected']=='no_delivery' || /* no delivery offered */
			($type == 'cart' && $wppizza_options['order_settings']['order_pickup_display_location']==2) || /* not displayed under cart */
			($type == 'orderpage' && $wppizza_options['order_settings']['order_pickup_display_location']==1) || /* not displayed on orderpage */
			empty($shop_open) /* shop closed */
		){

			if(empty($force_pickup_toggle)){
				return;
			}
		}

		/**get markup**/
		$markup = $this->get_markup($atts, $type);

	return $markup;
	}

	/***************************************
		[markup]
	***************************************/
	function get_markup($atts, $type){
		global $wppizza_options;
		static $unique_id=0;$unique_id++;
		$txt = $wppizza_options['localization'];

		/*
			checked or not | invert when self pickup is default
		*/
		$is_pickup = WPPIZZA() -> session -> is_pickup();
		$checkbox_is_pickup = empty($wppizza_options['order_settings']['order_pickup_as_default']) ? $is_pickup : ($is_pickup ? false : true );

		/*
			label: alternate label if pickup is default
		*/
		$label = empty($wppizza_options['order_settings']['order_pickup_as_default']) ? $txt['order_self_pickup'] : $txt['order_request_delivery'];
		/*
			checkbox: put here as it should not really be edited
		*/
		$option = '<label><input type="checkbox" class="'.WPPIZZA_PREFIX.'-order-pickup" name="'.WPPIZZA_PREFIX.'-order-pickup" value="1" ' . checked($checkbox_is_pickup,true,false) . ' />'.$label.'</label>';

		/*
			using 2 radios instead, unless toggle is specifically set to 0
		*/
		/* default */
		$force_toggle = !empty($wppizza_options['order_settings']['order_pickup_toggled']) ? true : false;
		/* set to radio toggle */
		$force_toggle = (isset($atts['toggle']) && $atts['toggle']==1 ) ? true : $force_toggle; /* if distinctly set to 1 */
		/* set to checkbox toggle */
		$force_toggle = (isset($atts['toggle']) && $atts['toggle']==0 ) ? false : $force_toggle; /* if distinctly set to 0 */

		if($force_toggle ){
				$option = '';
				$selected = (!$is_pickup) ? ' '.WPPIZZA_PREFIX.'-pickup-toggle-selected' : '' ;
				$option .= '<label class="'.WPPIZZA_PREFIX.'-pickup-toggle'.$selected.'"><input type="radio" class="'.WPPIZZA_PREFIX.'-order-pickup '.WPPIZZA_PREFIX.'-toggle-delivery" name="'.WPPIZZA_PREFIX.'-order-pickup" value="0" ' . checked(!$is_pickup,true,false) . ' /><span>'.$txt['pickup_toggle_delivery'].'</span></label>';
				$selected  = ($is_pickup) ? ' '.WPPIZZA_PREFIX.'-pickup-toggle-selected' : '' ;
				$option .= '<label class="'.WPPIZZA_PREFIX.'-pickup-toggle'.$selected.'"><input type="radio" class="'.WPPIZZA_PREFIX.'-order-pickup '.WPPIZZA_PREFIX.'-toggle-pickup" name="'.WPPIZZA_PREFIX.'-order-pickup" value="1" ' . checked($is_pickup,true,false) . ' /><span>'.$txt['pickup_toggle_pickup'].'</span></label>';
		}

		/*********************
			set unique id
		*********************/
		$id	= WPPIZZA_PREFIX.'-orders-pickup-choice-'.$unique_id;

		/*********************
			set classes
		*********************/
		$class= array();
		$class[] = WPPIZZA_PREFIX.'-orders-pickup-choice';
		if(!empty($atts['class'])){
			$class[] = esc_html($atts['class']);
		}
		if($force_toggle){
			$class[] = WPPIZZA_PREFIX.'-orders-pickup-choice-toggle';
		}
		$class = trim(implode(' ', $class));

		/*********************
			ini markup array
		*********************/
		$markup = array();

		/*********************
			get markup
		*********************/
		if(file_exists( WPPIZZA_TEMPLATE_DIR . '/markup/global/pickup_choice.php')){
			require(WPPIZZA_TEMPLATE_DIR.'/markup/global/pickup_choice.php');
		}else{
			require(WPPIZZA_PATH.'templates/markup/global/pickup_choice.php');
		}

		/*********************
			apply filter if required and implode for output
		*********************/
		$markup = apply_filters('wppizza_filter_pickup_choice_widget_markup', $markup, $atts, $unique_id, $is_pickup);
		$markup = trim(implode('', $markup));

	return $markup;


	}
}
?>