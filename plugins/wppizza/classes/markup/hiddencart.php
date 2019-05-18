<?php
/**
* WPPIZZA_MARKUP_HIDDENCART Class
*
* @package     WPPIZZA
* @subpackage  WPPizza Hidden Cart
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
*	CLASS - WPPIZZA_MARKUP_HIDDENCART
*
*
*
* ================================================================================================================================= */

class WPPIZZA_MARKUP_HIDDENCART{

	/******************************************************************************
	*
	*
	*	[construct]
	*
	*
	*******************************************************************************/
	function __construct() {
		/* include an invisible cart on orderpage including is open check*/
		add_action('wp_footer', array( $this, 'hidden_cart'),99);
	}

	/******************************************************************************
	*
	*
	*	[methods]
	*
	*
	*******************************************************************************/

	/*******************************************************
     *	add hidden input/div  - when open - to orderpage
     *	to be be able to still add things to cart and reload (for upsells, cart amendments etc)
     *	orderpage only -  let's not worry about caching as the orderpage should never be cached anyway
     *	add class if open - used in javascripts for alert when closed - class set to -novis , 
	 *	to not write to element when adding to cart (only carts with wppizza-cart will be written to)
     ******************************************************/
	function hidden_cart(){
		if(wppizza_is_orderpage() && wppizza_is_shop_open()){
			echo'<span class="'.WPPIZZA_PREFIX.'-open '.WPPIZZA_PREFIX.'-cart-novis" style="display:none"></span>';
		}
	}
}
/***************************************************************
*
*	[ini]
*
***************************************************************/
if(!is_admin()){
	$WPPIZZA_MARKUP_HIDDENCART = new WPPIZZA_MARKUP_HIDDENCART();
}
?>