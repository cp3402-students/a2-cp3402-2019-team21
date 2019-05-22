<?php
/**
* WPPIZZA_MARKUP_MAINCART Class
*
* @package     WPPIZZA
* @subpackage  WPPizza Main Cart
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
*	CLASS - WPPIZZA_MARKUP_MAINCART
*
*
*
* ================================================================================================================================= */

class WPPIZZA_MARKUP_MAINCART{

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
		$type = 'cart';
		$markup = $this->get_markup_components($atts, $type);
	return $markup;
	}

	/***************************************
		[markup]
	***************************************/
	function get_markup_components($atts, $type){
		static $set_cart_id = 0; $set_cart_id++;
		/** using cache or not **/
		$using_cache = apply_filters('wppizza_filter_using_cache_plugin', false);

		/** get set cart style (width / height) **/
		$cart_style = $this->get_cart_style($atts);

		/** set cart id - appending height to id to enable js to set height dynamically **/
		$cart_id = ''.WPPIZZA_PREFIX.'-cart-'.$set_cart_id.'-'.$cart_style['itemised_height'].'';



		$markup = array();

			/*
				openingtimes - if enabled
			*/
			$markup['openingtimes'] = WPPIZZA()->markup_openingtimes->attributes($atts, $type);


			/******************
				cart markup -
				includes cart contents
				includes subtotals


				caching plugin enabled -> just create empty div
				with loading gif
				to be filled with cart by ajax request

			******************/
			if($using_cache){
				$markup['div_'] = '<div id="'.$cart_id.'" class="'.WPPIZZA_PREFIX.'-cart '.WPPIZZA_PREFIX.'-cart-nocache" ' . $cart_style['cart'] . ' >';
					$markup['loading'] = '<div class="'.WPPIZZA_PREFIX.'-loading"></div>';
				$markup['_div'] ='</div>';
			}else{
				$markup['div_'] = '<div id="'.$cart_id.'" class="'.WPPIZZA_PREFIX.'-cart" ' . $cart_style['width'] . ' >';
					$markup['cart'] = WPPIZZA()->markup_maincart->cart_contents_markup_from_session(wppizza_is_orderpage(), $type, $cart_style);
				$markup['_div'] ='</div>';
			}

			/*

		  	pickup checkbox

			*/
			$markup['pickup'] = WPPIZZA() -> markup_pickup_choice -> attributes($atts, $type);

			/*
				orderinfo - if enabled
			*/
			$markup['orderinfo'] = WPPIZZA() -> markup_orderinfo -> attributes($atts, $type);


		/*
			apply filter if required - change order of components for example - and implode for output
		*/
		$markup = apply_filters('wppizza_filter_cart_outer_markup', $markup, $atts, $set_cart_id);
		$markup = trim(implode('', $markup));
		return $markup;
	}

	/*******************************************************
    *
    *
    *	create markup for cart from session values
    *
    *
    ******************************************************/
	function cart_contents_markup_from_session($is_checkout, $type = 'cart', $cart_style = array()){

		$order_formatted = WPPIZZA()->order->session_formatted();

		$container_class = ''.WPPIZZA_PREFIX.'-cart-info';
		$button_class = ''.WPPIZZA_PREFIX.'-cart-buttons';
		/*
			initialize empty vars, in case someone takes the
			conditionals out of cart.container.php
			(which we could do really, but it makes it clearer to figure out whats happening)
		*/
		$is_closed = '';
		$is_open = '';
		$cart_empty = '';
		$items = '';
		$summary = '';
		$pickup_note = '';
		$minimum_order = '';
		$checkout_button = '';
		$empty_cart_button = '';

		/*
			Modules
		*/

		/* shop closed */
		if(!wppizza_is_shop_open()){
			$is_closed = WPPIZZA() -> markup_maincart -> shop_closed($order_formatted, $type);
		}

		if(wppizza_is_shop_open()){
			/*
				convenience identifier for js alerts, although this is also checked serverside before submission
			*/
			$is_open = '<input type="hidden" class="'.WPPIZZA_PREFIX.'-open" name="'.WPPIZZA_PREFIX.'-open" />';
			/*
				cart empty
			*/
			$cart_empty = WPPIZZA() -> markup_maincart -> cart_empty($order_formatted, $type);
			/*
				itemised items table
			*/
		  	$items = WPPIZZA() -> markup_maincart -> itemised_markup($order_formatted, $type, $cart_style);
			/*
				subtotals/summary table
			*/
			$summary = WPPIZZA() -> markup_maincart -> summary_markup($order_formatted, $type);
			/*
				pickup / delivery note
			*/
			$pickup_note = WPPIZZA() -> markup_maincart -> pickup_note($order_formatted, $type);
			/*
				minimum order required text
			*/
			$minimum_order = WPPIZZA() -> markup_maincart -> minimum_order($order_formatted, $type);
			/*
				checkout button
			*/
			$checkout_button = WPPIZZA() -> markup_maincart -> checkout_button($order_formatted, $type);
			/*
				empty_cart button
			*/
			$empty_cart_button = WPPIZZA() -> markup_maincart -> empty_cart_button($order_formatted, $type);
		}

		/*
			ini array
		*/
		$markup = array();
		/*
			get markup
		*/
		if(file_exists( WPPIZZA_TEMPLATE_DIR . '/markup/cart/cart.container.php')){
			require(WPPIZZA_TEMPLATE_DIR.'/markup/cart/cart.container.php');
		}else{
			require(WPPIZZA_PATH.'templates/markup/cart/cart.container.php');
		}
		/*
			apply filter if required and implode for output
		*/
		$markup = apply_filters('wppizza_filter_maincart_container_markup', $markup);
		$markup = implode('', $markup);

	return $markup;
	}


	/*
		shop_closed
	*/
	function shop_closed($order_formatted, $type){
		global $wppizza_options;
		$txt = $wppizza_options['localization'];


		/* set class */
		$class = WPPIZZA_PREFIX.'-closed';

		/*
			ini array
		*/
		$markup = array();
		/*
			get markup
		*/
		if(file_exists( WPPIZZA_TEMPLATE_DIR . '/markup/cart/cart.shopclosed.php')){
			require(WPPIZZA_TEMPLATE_DIR.'/markup/cart/cart.shopclosed.php');
		}else{
			require(WPPIZZA_PATH.'templates/markup/cart/cart.shopclosed.php');
		}
		/*
			apply filter if required and implode for output
		*/
		$markup = apply_filters('wppizza_filter_maincart_shopclosed_markup', $markup);
		$markup = trim(implode('', $markup));

	return $markup;
	}
	/*
		cart_empty
	*/
	function cart_empty($order_formatted, $type){
		global $wppizza_options;
		$txt = $wppizza_options['localization'];
		$items = $order_formatted['order'];

		/* cart isnt empty */
		if(!empty($items['items'])){
			return '';
		}

		/* set class */
		$class = WPPIZZA_PREFIX.'-cart-empty';

		/*
			ini array
		*/
		$markup = array();
		/*
			get markup
		*/
		if(file_exists( WPPIZZA_TEMPLATE_DIR . '/markup/cart/cart.empty.php')){
			require(WPPIZZA_TEMPLATE_DIR.'/markup/cart/cart.empty.php');
		}else{
			require(WPPIZZA_PATH.'templates/markup/cart/cart.empty.php');
		}
		/*
			apply filter if required and implode for output
		*/
		$markup = apply_filters('wppizza_filter_maincart_cartempty_markup', $markup);
		$markup = trim(implode('', $markup));

	return $markup;
	}

	/*
		pickup_note
	*/
	function pickup_note($order_formatted, $type){

		global $wppizza_options;

		/*
			skip delivery/pickup note in cart
			entirely if set to no_delivery, pickup only
		*/
		if($wppizza_options['order_settings']['delivery_selected']=='no_delivery'){
			$markup = '';
			return $markup;
		}

		/**txt variables from settings->localization. put all text varibles into something easier to deal with*/
		$txt = $wppizza_options['localization'];
		$is_pickup = WPPIZZA() -> session -> is_pickup();
		$items = $order_formatted['order'];

		/* nothing in cart or not pickup */
		if(empty($items['items']) || !$is_pickup ){return '';}


		/* set class */
		$class = WPPIZZA_PREFIX.'-pickup-note';

		/*
			ini array
		*/
		$markup = array();
		/*
			get markup
		*/
		if(file_exists( WPPIZZA_TEMPLATE_DIR . '/markup/cart/cart.pickup_note.php')){
			require(WPPIZZA_TEMPLATE_DIR.'/markup/cart/cart.pickup_note.php');
		}else{
			require(WPPIZZA_PATH.'templates/markup/cart/cart.pickup_note.php');
		}
		/*
			apply filter if required and implode for output
		*/
		$markup = apply_filters('wppizza_filter_maincart_pickup_note_markup', $markup);
		$markup = trim(implode('', $markup));

	return $markup;
	}

	/*
		minimum order required text
	*/
	function minimum_order($order_formatted, $type){
		global $wppizza_options;

		$can_checkout = $order_formatted['checkout_parameters']['can_checkout'];
		$is_pickup = $order_formatted['checkout_parameters']['is_pickup'];
		$min_order_required = $order_formatted['checkout_parameters']['min_order_required'];
		$items = $order_formatted['order'];


		/* only there are actually items in the cart and a minimum order is required */
		if(empty($can_checkout) && !empty($min_order_required) && !empty($items['items']) ){
			/* pickup selected */
			if($is_pickup){
				$minimum_order_label = ''.$wppizza_options['localization']['minimum_order'] . ' ' . wppizza_format_price($min_order_required).'';
			}else{
				$minimum_order_label = ''.$wppizza_options['localization']['minimum_order_delivery'] . ' ' . wppizza_format_price($min_order_required).'';
			}
		}else{
			return '';
		}

		/* set class */
		$class = WPPIZZA_PREFIX.'-min-order';

		/*
			ini array
		*/
		$markup = array();
		/*
			get markup
		*/
		if(file_exists( WPPIZZA_TEMPLATE_DIR . '/markup/cart/cart.minimum_order.php')){
			require(WPPIZZA_TEMPLATE_DIR.'/markup/cart/cart.minimum_order.php');
		}else{
			require(WPPIZZA_PATH.'templates/markup/cart/cart.minimum_order.php');
		}


		/*
			apply filter if required and implode for output
		*/
		$markup = apply_filters('wppizza_filter_maincart_minimum_order_markup', $markup);
		$markup = trim(implode('', $markup));

	return $markup;

	}


	/*
		checkout button
	*/
	function checkout_button($order_formatted, $type){
		global $wppizza_options;
		/**txt variables from settings->localization. put all text varibles into something easier to deal with*/
		$txt = $wppizza_options['localization'];
		$can_checkout = $order_formatted['checkout_parameters']['can_checkout'];
		//$is_pickup = $order_formatted['checkout_parameters']['is_pickup'];
		//$min_order_required = $order_formatted['checkout_parameters']['min_order_required'];
		$items = $order_formatted['order'];

		/* nothing in cart or cannot checkout (min order for example)*/
		if(empty($items['items']) || empty($can_checkout) ){return '';}


		/* set class */
		$class = WPPIZZA_PREFIX.'-checkout-button';


		/* get link to order page */
		$order_page_link = wppizza_page_links('orderpage');
		$order_page_js_link = 'onclick="location.href=\''.$order_page_link.'\'"';


		$markup = array();

		/*
			get markup
		*/
		if(file_exists( WPPIZZA_TEMPLATE_DIR . '/markup/cart/cart.checkout_button.php')){
			require(WPPIZZA_TEMPLATE_DIR.'/markup/cart/cart.checkout_button.php');
		}else{
			require(WPPIZZA_PATH.'templates/markup/cart/cart.checkout_button.php');
		}

		/*
			apply filter if required and implode for output
		*/
		$markup = apply_filters('wppizza_filter_maincart_checkout_button_markup', $markup);
		$markup = trim(implode('', $markup));

	return $markup;
	}

	/*
		empty_cart button
	*/
	function empty_cart_button($order_formatted, $type){
		global $wppizza_options;
		/**txt variables from settings->localization. put all text varibles into something easier to deal with*/
		$txt = $wppizza_options['localization'];
		$items = $order_formatted['order'];

		/* nothing in cart or not enabled */
		if(empty($items['items']) || empty($wppizza_options['order_settings']['empty_cart_button']) ){
			return '';
		}


		/* set class */
		$class = WPPIZZA_PREFIX.'-empty-cart-button';


		$markup = array();

		/*
			get markup
		*/
		if(file_exists( WPPIZZA_TEMPLATE_DIR . '/markup/cart/cart.empty_cart_button.php')){
			require(WPPIZZA_TEMPLATE_DIR.'/markup/cart/cart.empty_cart_button.php');
		}else{
			require(WPPIZZA_PATH.'templates/markup/cart/cart.empty_cart_button.php');
		}

		/*
			apply filter if required and implode for output
		*/
		$markup = apply_filters('wppizza_filter_maincart_empty_cart_button_markup', $markup);
		$markup = trim(implode('', $markup));

	return $markup;
	}

	/*


		subtotals

		$cart : either session or order_ini

	*/
	function summary_markup($order_formatted, $type){
		global $wppizza_options;
		$items = $order_formatted['order'];
		$summary_array = $order_formatted['summary'];

		/*
			remove tax total entirely if all tax rates are 0
		*/
		$no_tax_total = (empty($wppizza_options['order_settings']['item_tax']) && empty($wppizza_options['order_settings']['item_tax_alt']) && empty($wppizza_options['order_settings']['shipping_tax_rate'])) ? true : false;
		/*
			also remove tax total if there is only one tax (which will then be displayed instead with % applied)
		*/
		if($no_tax_total || empty($summary_array['taxes']) || count($summary_array['taxes'])<=1){
			unset($summary_array['tax_total']);
		}

		/* nothing in cart */
		if(empty($items['items'])){
			return '';
		}

		/*
			classes
		*/
		/* table */
		$class_table = ''.WPPIZZA_PREFIX.'-table '.WPPIZZA_PREFIX.'-order-summary';

		/* tbody */
		$class_tbody = WPPIZZA_PREFIX.'-table-row-group';

		/* tr's */
		foreach($summary_array as $sKey => $summary_values){
			foreach($summary_values as $key => $values){

				/** only add key if more than 2 **/
				$add_key = (count($summary_values)<=1) ? '' : '_'.$key ;


				/** summary values to pass on to summary.php */
				$summary[ $sKey . $add_key ] =  $values;


				/* class for trs */
				$class_tr[$sKey . $add_key] = ''.WPPIZZA_PREFIX.'-'.$values['class_ident'].'';

				/** label **/
				$label[$sKey . $add_key] = !empty($values['label']) ?  $values['label'] : '';

				/** value **/
				$value[$sKey . $add_key] = !empty($values['value_formatted']) ?  $values['value_formatted'] : '';

			}
		}


		$markup = array();

		/*
			get markup
		*/
		if(file_exists( WPPIZZA_TEMPLATE_DIR . '/markup/order/summary.php')){
			require(WPPIZZA_TEMPLATE_DIR.'/markup/order/summary.php');
		}else{
			require(WPPIZZA_PATH.'templates/markup/order/summary.php');
		}

		/*
			apply filter if required (returned $markup array will be imploded for output)
		*/
		$markup = apply_filters('wppizza_filter_order_summary_markup', $markup, $summary, $type);

		/*
			implode for output
		*/
		$markup = trim(implode('', $markup));

	return $markup;

	}


	/*

	cart_itemised

	*/

	function itemised_markup($order_formatted, $type, $cart_style = array()){

		global $wppizza_options;
		/**txt variables from settings->localization*/
		$txt = $wppizza_options['localization'];/*put all text varibles into something easier to deal with**/
		$site = $order_formatted['site'];
		$order = $order_formatted['ordervars'];
		$order_items = $order_formatted['order'];
		$items = !empty($order_formatted['order']['items']) ? $order_formatted['order']['items'] : false ;
		$no_of_items = !empty($items) ? (count($items) -1) : 0 ; /* - 1 to match $itemcount */
		$multiple_taxrates = !empty($order_formatted['order']['multiple_taxrates']) ? true : false;
		$blog_id = $site['blog_id']['value'];
		$order_id =  $order['order_id']['value_formatted'];



		/***** nothing in cart ******/
		if(empty($items)){return '';}


		/**
			class table
		**/
		$class_table = ''.WPPIZZA_PREFIX.'-table '.WPPIZZA_PREFIX.'-order-itemised';

		/**
			class thead
		**/
		$class_thead = WPPIZZA_PREFIX.'-table-header-group';

		/**
			class tbody
		**/
		$class_tbody = WPPIZZA_PREFIX.'-table-row-group';

		/**
			style tbody
			set cart height on first page load if cart not cached
		**/
		$style_tbody = '';
		if($type == 'cart' && !empty($cart_style['itemised_height'])){
			$style_tbody = 	'style="height:'.$cart_style['itemised_height'].'px; min-height:'.$cart_style['itemised_height'].'px"';
		}

		/**
			id item row prefix
			as we might have cart and orderpage/orderhistory on the same
			page and even have the same order (i.e key) on the user purchase page
			multiple times, make sure it's unique
		**/
		$id_row_prefix =  ''.WPPIZZA_PREFIX.'-cart-item-'; /* cart / default */
		if($type != 'cart' && $type != 'orderhistory'){
		$id_row_prefix =  ''.WPPIZZA_PREFIX.'-order-item-'; /* pages with order(s) on them except purchase history*/
		}
		if($type == 'orderhistory'){
		$id_row_prefix =  ''.WPPIZZA_PREFIX.'-order-'.$order_id.'-item-'; /* purchase history - add order id too */
		}

		/**
			class item row
		**/
		$class_row =  WPPIZZA_PREFIX.'-item-row';
		$class_row_first =  WPPIZZA_PREFIX.'-item-row-first';
		$class_row_last =  WPPIZZA_PREFIX.'-item-row-last';


		/**
			class columns
		**/
		/* classes columns : th|td - quantity*/
		$class_quantity_th = WPPIZZA_PREFIX.'-item-quantity-th';
		$class_quantity_td = WPPIZZA_PREFIX.'-item-quantity';
		/* classes columns : th|td - article*/
		$class_article_th = WPPIZZA_PREFIX.'-item-article-th';
		$class_article_td = WPPIZZA_PREFIX.'-item-article';
		/* classes columns : th|td - price*/
		$class_price_th = WPPIZZA_PREFIX.'-item-price-th';
		$class_price_td = WPPIZZA_PREFIX.'-item-price';
		/* classes columns : th|td - taxrate*/
		$class_taxrate_th = WPPIZZA_PREFIX.'-item-taxrate-th';
		$class_taxrate_td = WPPIZZA_PREFIX.'-item-taxrate';
		/* classes columns : th|td - total*/
		$class_total_th = WPPIZZA_PREFIX.'-item-total-th';
		$class_total_td = WPPIZZA_PREFIX.'-item-total';

		/* classes columns : td - article thumbnail/title/size span*/
		$class_article_thumbnail = WPPIZZA_PREFIX.'-item-thumbnail';
		$class_article_no_thumbnail = WPPIZZA_PREFIX.'-item-no-thumbnail';
		$class_article_title = WPPIZZA_PREFIX.'-item-title';
		$class_article_size = WPPIZZA_PREFIX.'-item-size';

		/* thumbnails - if enabled - on orderpage, confirmationpage, thankyoupage */
		if(!empty($wppizza_options['layout']['cart_image']) && in_array($type, array('orderpage', 'confirmationpage', 'thankyoupage'))){

			/*
				if no item in cart has any thumbnails
				do not display a - pointless - empty placeholder either
			*/
			$cart_has_thumbnails = 0;

			/*
				some globals
			*/
			$wp_upload_directory = wp_upload_dir();
			/*
				set max image width/height (filterable)
				Note: only checked with equal max width/height set here
				when proportionally resizing dimensions !!)
				if changed, you (probably) need to override the related css too
			*/
			$cart_thumbnail_max_width_height = apply_filters('wppizza_filter_cart_thumbnail_max_width_height', array(30, 30));


			foreach($items as $key => $item){

				$thumbnail_img = false;

				if(has_post_thumbnail( $item['post_id'] )){

					/* get some attributes, check image exists, and show image (or empty placeholder) */
					$_thumbnail_id = get_post_thumbnail_id($item['post_id']);
					$_thumbnail_meta = wp_get_attachment_metadata( $_thumbnail_id );
					$_thumbnail_exist = is_file($wp_upload_directory['basedir'] . '/' . $_thumbnail_meta['file']) ? true : false;

					/*
						check for thumbnail and generate img element
					*/
					if($_thumbnail_exist){
						$_title_attribute = the_title_attribute(array('post'=>$item['post_id'], 'echo'=>0));
						$_alt_attribute = get_post_meta($_thumbnail_id, '_wp_attachment_image_alt', true);
						$_dimensions['width'] = !empty($_thumbnail_meta['sizes']['thumbnail']['width']) ? $_thumbnail_meta['sizes']['thumbnail']['width'] : $_thumbnail_meta['width'] ;
						$_dimensions['height'] = !empty($_thumbnail_meta['sizes']['thumbnail']['height']) ? $_thumbnail_meta['sizes']['thumbnail']['height'] : $_thumbnail_meta['height'] ;
						$_max_dimension_key = array_search(max($_dimensions), $_dimensions);
						$_max_dimension = $_dimensions[$_max_dimension_key];

						/* depending on which has max dimension, set or calculate width/height */
						if($_max_dimension_key == 'width'){
							$_set_width	= $cart_thumbnail_max_width_height[0] ;
							$_max_divider = ($_dimensions['width'] / $_set_width);
							$_set_height = ceil($_dimensions['height'] / $_max_divider) ;
						}else{
							$_set_height = $cart_thumbnail_max_width_height[1] ;
							$_max_divider = ($_dimensions['height'] / $_set_height);
							$_set_width	= ceil($_dimensions['width'] / $_max_divider) ;
						}

						$thumbnail_img = get_the_post_thumbnail($item['post_id'], array($_set_width ,$_set_height), array('class' => '', 'alt' => empty($_alt_attribute) ? $_title_attribute : $_alt_attribute ,'title'=> $_title_attribute));
					}
				}

				/*
					add thumbnail key to item array
				*/
				$items[$key]['thumbnail'] = !empty($thumbnail_img) ? $thumbnail_img : '' ;

				/*
					count number of thumbnails
					 a simple boolean  would also suffice, but a count might come in handy one day
				*/
				if(!empty($thumbnail_img)){
					$cart_has_thumbnails++;
				}
			}
		}

		/*
			ini markup array
		*/
		$markup = array();

		/*
			get markup
		*/
		if(file_exists( WPPIZZA_TEMPLATE_DIR . '/markup/order/itemised.php')){
			require(WPPIZZA_TEMPLATE_DIR.'/markup/order/itemised.php');
		}else{
			require(WPPIZZA_PATH.'templates/markup/order/itemised.php');
		}


		/*
			filter complete markup if required (return $markup array will be imploded for output)
		*/
		$markup = apply_filters('wppizza_filter_order_itemised_markup', $markup, $blog_id , $order_id, $items, $txt, $colspan, $type);

		/*
			implode for output
		*/
		$markup = implode('',$markup);


	return $markup;
	}

/*******************************************************
*
*
*	[helpers]
*
*
******************************************************/
     /******************************************************
     * set width height of *itemised* cart as set by user/shortcode/widget
     * will alse be set via ajax when reloading/adding to etc cart
     ******************************************************/
	function get_cart_style($atts = false){

			/* atts set width */
			if(isset($atts['width']) && $atts['width']!=''){
				$cart['width']='width:'.esc_html($atts['width']).'';
			}
			/* atts set cart min height */
			if(isset($atts['height']) && $atts['height']!=''){
				$cart['height']='min-height:'.(int)($atts['height']).'px';
			}

			/* atts set itemised height */
			if(isset($atts['height']) && $atts['height']!=''){
				//$itemised['height']='height:'.(int)($atts['height']).'px; max-height:'.(int)($atts['height']).'px';
				$itemised['height']=(int)($atts['height']);
			}

			$cart_style = array();
			$cart_style['cart'] =(isset($cart) ) ? 'style="'.implode('; ', $cart).'"' : '';/* style declaration width/min-height*/
			$cart_style['width'] =(isset($cart['width'])) ? 'style="'.$cart['width'].'"' : '';/* style declaration width only*/
			$cart_style['itemised_height'] = (isset($itemised['height'])) ? $itemised['height'] : 0;/* height integer (used in js) */

	return $cart_style;
	}
}
?>