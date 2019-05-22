<?php
/**
* WPPIZZA_SHORTCODES Class
*
* @package     WPPIZZA
* @subpackage  WPPizza Shortcodes
* @copyright   Copyright (c) 2015, Oliver Bach
* @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
* @since       3.0
*
*/
if ( ! defined( 'ABSPATH' ) ) exit;/*Exit if accessed directly*/


/************************************************************************************************************************
*
*
*	WPPIZZA_SHORTCODES
*
*
************************************************************************************************************************/
class WPPIZZA_SHORTCODES{


	function __construct() {
		/*
			used in ajax request for cart contents too so must be available when ajax and on front AND backend!
		*/
		add_shortcode(WPPIZZA_SLUG, array($this, 'add_shortcodes'));

		/*
			admin shortcodes (in case we want to be able to add certain admin parts to frontend pages)
		*/
		add_shortcode(WPPIZZA_SLUG.'_admin', array($this, 'add_admin_shortcodes'));

		/*
			if using admin shortcodes in frontend pages, (compact admin order history for example)
			define wppizza_has_admin_shortcode() to return true for other plugins to check
			alongside is_admin() for example
		*/
		add_action('init', array($this, 'has_admin_shortcode'), 9);

	}

    /*****************************************************
    * @return void
    * @since 3.5
    ******************************************************/
	function has_admin_shortcode(){

		/*
			as we are running on init , there is no post object yet
			and we have to get the id by url
		*/
		$REQUEST_SCHEME = is_ssl() ? 'https' : 'http';
		$current_url = $REQUEST_SCHEME . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ;
		$post_id = url_to_postid($current_url);
		$post_content = get_post_field('post_content', $post_id);
		if(has_shortcode( $post_content, WPPIZZA_SLUG.'_admin')) {

			/* add thickbox */
			add_action( 'wp_enqueue_scripts', array($this, 'add_admin_shortcode_thickbox') );

			/* return true for global wppizza_is_admin_shortcode() function */
			add_filter('wppizza_has_admin_shortcode', array($this, 'is_admin_shortcode'));
		}
	}

    /*****************************************************
    * make global is_admin_shortcode() function return true
    * @return bool (true)
    * @since 3.5
    ******************************************************/
	function is_admin_shortcode($bool){
		return true;
	}
    /*****************************************************
    * enqueue thickbox for pages that include admin shortcodes
    * @return void
    * @since 3.5
    ******************************************************/
	function add_admin_shortcode_thickbox(){
		add_thickbox();
	}
    /*****************************************************
     * Generates wppizza admin shortcode output utilising templates
     * @atts    The array of shortcode attributes
     * @since 3.5
     * @return str
     ******************************************************/
	function add_admin_shortcodes($atts){

		$type = !empty($atts['type']) ? $atts['type'] : '' ;

		$is_admin = true ;

		$markup='';

		/**********************************************
			possible attributes:
			type='admin_orderhistory' 	(required [str])

			#	perhaps to add in the future
			#
			#	compact=0|1 			(optional [bool])
			#	maxpp=10 				(optional [int])
			#

			example: 				[wppizza_admin type='admin_orderhistory']

		**********************************************/
		if($type == 'admin_orderhistory'){
			$markup = WPPIZZA()->markup_pages->markup($type, $atts, $is_admin);
		return $markup;
		}

		/**********************************************
			possible attributes:
			type='admin_dashboard_widget' 	(required [str])
			unprotected='1' 	(optional bool)
			example: 				[wppizza_admin type='admin_dashboard_widget' unprotected='1']

		**********************************************/
		if($type == 'admin_dashboard_widget'){
			$markup = WPPIZZA()->markup_pages->markup($type, $atts, $is_admin);
		return $markup;
		}


	return $markup;
	die();//needed !!!
	}

    /*****************************************************
     * Generates shortcode output utilising templates
     * @atts    The array of shortcode attributes
     ******************************************************/
	function add_shortcodes($atts){

		$markup='';
		extract(shortcode_atts(array('type' => ''), $atts));

		/**********************************************
			[navigation]
				possible attributes:
				type='navigation' 		(required [str])
			 	title='some title' 		(optional[str]: will render as h2 as first element in cart elemnt if set)
			 	parent='slug-name' 		(optionsl [str]): only show child categories of this slug
			 	exclude='6,5,8' 		(optional [comma separated category id's]): exclude some id's
			example: 		[wppizza type='navigation' title='some title' parent='slug-name' exclude='6,5,8']
		**********************************************/
		if($type=='navigation'){
			$markup = WPPIZZA()->markup_navigation->attributes($atts);
		return $markup;
		}
		/**********************************************
			[orderinfo]
			possible attributes:
			type='orderinfo' (required [str])
			width='200px' (optional [int + px|%] )
			height='200px' (optional [int + px|%])
			class='myclass' (optional [string] default='')
			info='discounts,deliveries' (optional [comma separated string of modules . discounts and/or deliveries ] if omitted == discounts, deliveries)
			example: 		[wppizza type='openingtimes' width='80%' height='200px' class='my_class' info='discounts, deliveries']
			returns discounts/delivery costs etc  in a string
		**********************************************/
		if($type=='orderinfo'){
			$markup = WPPIZZA()->markup_orderinfo->attributes($atts);
		return $markup;
		}

		/**********************************************
			[openingtimes]
			possible attributes:
			type='openingtimes' (required [str])
			width='200px' (optional [int + px|%] )
			height='200px' (optional [int + px|%])
			class='myclass' (optional [string] default='')
			example: 		[wppizza type='openingtimes' class='my_class']
			returns grouped opening times in a string
		**********************************************/
		if($type=='openingtimes'){
			$markup = WPPIZZA()->markup_openingtimes->attributes($atts);
		return $markup;
		}

		/**********************************************
			[additives]
			possible attributes:
			type='additives' (required [str])
			class='myclass' (optional [string] default='')
			example: 		[wppizza type='additives' class='my_class']
			returns all additives in an html string
		**********************************************/
		if($type=='additives'){
			$markup = WPPIZZA()->markup_additives->attributes($atts);
		return $markup;
		}

		/**********************************************
			[pickup choices]
			possible attributes:
			type='pickup_choices' (required [str])
			class='myclass' (optional [string] default='')
			toggle='1' (bool - displays to buttons)
			example: 		[wppizza type='pickup_choices' class='my_class']
			returns array with checkbox (un/selected)
		**********************************************/
		if($type=='pickup_choices'){
			$markup = WPPIZZA() -> markup_pickup_choice -> attributes($atts, $type);
		return $markup;
		}

		/**********************************************
			[searchbox]
				possible attributes:
				type='search' 		(required [str])
			 	include='wppizza,post,page,attachment,revision,nav_menu_item' (optional[str]: include menu items, posts, pages and/or other cpts respectively)
			 	loggedinonly='1' (optional[bool]: anything. if defined searchform only gets displayed for logged in users)
			 	class='myclass' (optional [string] default='')
			example: 		[wppizza type='search'  include='wppizza,post,page' loggedinonly='1']
		**********************************************/
		if($type=='search'){
			$markup = WPPIZZA()->markup_search->attributes($atts);
		return $markup;
		}
		/**********************************************
			[totals]
			possible attributes:
			type='totals' (required [str])
			class='myclass' (optional [string] default='')
			value='items' (optional[str]) - if used , only displays value of items as ooposed to totals including delivery etc
			itemcount='left|right'  (optional [str]) - if used , count of item will be displayed left or right of the total
			checkout='bool' (optional) - will display a button to go to order page

			viewcart = '1' [bool] adds view cart button

			cart_view = 'itemised, summary'; if empty, will show both otherwise only selected


			example: 		[wppizza type='totals']
			returns div that with current cart totals (loaded via js)
		**********************************************/

		if($type=='totals'){
    		global $wppizza_options;
			/*disable when disable_online_order is set or is orderpage */
			if(!empty($wppizza_options['layout']['disable_online_order']) || wppizza_is_orderpage()){
				return;
			}
			$markup = WPPIZZA()->markup_totals->attributes($atts);
		return $markup;
		}

		/*
			lets split the minicart out of the main cart, which also allows us to only display this
			class='myclass' (optional [string] default='')
			example: 	[wppizza type='minicart']
		*/

		if($type=='minicart'){
    		global $wppizza_options;
			/*disable when disable_online_order is set or is orderpage */
			if(!empty($wppizza_options['layout']['disable_online_order']) || wppizza_is_orderpage() ){
				return;
			}
			/** static , only ever invoked once no matter how many carts have been put on page*/
			$markup = WPPIZZA()->markup_minicart->attributes($atts);
		return $markup;
		}

		/**********************************************
			[cart]
			possible attributes:
				type='cart' 			(required [str])
		 		openingtimes='1' 		(optional[bool]: anything. if its defined it gets displayed)
		 		orderinfo=1				(optional[bool]: anything. if its defined it gets displayed)
		 		minicart=1 | only		(optional[bool]: anything. if its defined it gets displayed. setting to only is the same as just using minicart shortcode)
		 		width='200px' 			(optional[str]: value in px or % ) (although under 150px is probably bad)
		 		height='200' 			(optional[str]: value in px )
			example: 		[wppizza type='cart']
		**********************************************/
		if($type=='cart'){
    		global $wppizza_options;
			/*disable when disable_online_order is set or is orderpage */
			if(!empty($wppizza_options['layout']['disable_online_order']) || wppizza_is_orderpage() ){
				return;
			}

			if(!empty($atts['minicart'])){
				/** static , only ever invoked once no matter how many carts have been put on page*/
				$markup = WPPIZZA()->markup_minicart->attributes($atts);
			}

			/* skip if only showing minicart */
			$markup = (!empty($atts['minicart']) && $atts['minicart']==='only')? '' : WPPIZZA()->markup_maincart->attributes($atts);

		return $markup;
		}

		/**********************************************
			[$type==orderpage]
				possible attributes:
				type='orderpage' 			(required [str])
				nocart='1' 			(optional)
				example: 		[wppizza type='orderpage']


			[$type==orderhistory]
				possible attributes:
				type='orderhistory' 			(required [str])
				multisite='1' 					(optional [bool])
				maxpp='1' 					(optional [int])
				example: 		[wppizza type='orderhistory']
		**********************************************/
		if($type=='orderpage' || $type=='orderhistory' ){
    		global $wppizza_options;
			/*disable when disable_online_order is set */
			if(!empty($wppizza_options['layout']['disable_online_order'])){
				return;
			}

			$markup = WPPIZZA()->markup_pages->markup($type, $atts);
		return $markup;
		}

		/**********************************************
			[default pages (item loops)]
				possible attributes:
				category='pizza' 		(optional: '[category-slug]')
				noheader='1' 			(optional: 'anything')
				style='' 				(optional: default | responsive | grid if omitted , style set in layout will be used. if set to other than style in layout, make sure to enable stylecheet (layout ->load additional styles) )
				showadditives='1' 		(optional[bool]: 0 or 1)
				exclude='6,5,8' 		(optional [comma separated menu item id's]): exclude some id's
				include='6,5,8' 		(optional [comma separated menu item id's]): include only these id's (overrides exclude)
				bestsellers='11' 		(optional: integer - shows n number of bestsellers, sorted by number of purchases desc)
				viewonly='1' 		(optional[bool]: 0 or 1 - removes add-to-cart class and shopping cart icon)
			example: 		[wppizza category='pizza' noheader='1' exclude='6,7,8']
			or
			example: 		[wppizza category='pizza' noheader='1' include='6,7,8']



			[single]
				possible attributes:
				single='11' 		(required [str] single id of menu item)
				viewonly='1' 		(optional[bool]: 0 or 1 - removes add-to-cart class and shopping cart icon)
			example: 		[wppizza single='11' ]



			[bestsellers]
				attributes:

				bestsellers=’10’ (required: integer of how many items to display).
				showadditives=’0' (optional: ‘0’ or ‘1’. if omitted, a list of additives will be displayed if any of the items has additives added. if set (0 or 1): force to display/hide additives list. useful when displaying more than 1 category on a page)
				include=’6,5,8' (optional [comma separated menu item id’s]): ADDITIONALLY include these id’s to the bestsellers already displayed
				ifempty=’5,9,8,11' (optional [comma separated menu item id’s]): if no sales have been made yet and no include have been defined – mainly for new installations – set the menu item id’s you want to have displayed instead – ignores include attribute of appicable)
				viewonly='1' 		(optional[bool]: 0 or 1 - removes add-to-cart class and shopping cart icon)
			examples: [wppizza bestsellers='10' ifempty='5,9,8,11' showadditives='0' include='6,5,8']

		**********************************************/
		if($type==''){
			$markup = WPPIZZA()->templates_menu_items->markup($atts);
		return $markup;
		}
		/**********************************************
			[add_item_to_cart_button]
			required attributes:
			type='add_to_cart_button' 	(required [str])
			id='6' 						(required [int]) - id of wppizza menu item
			size='0'  					(optional [int]) - id (zero indexed) of wppizza menu size for that item
			single='1'  				(optional [bool]) - if set will only show single button without dropdown
			example: 					[wppizza type='add_item_to_cart_button' id='6' size='0']
			returns a button (with or without dropdown) to add a menu item to cart
		**********************************************/
		if($type=='add_item_to_cart_button'){
    		global $wppizza_options;
			/*disable when disable_online_order is set */
			if(!empty($wppizza_options['layout']['disable_online_order'])){
				return;
			}
			$markup = WPPIZZA()->templates_menu_items->markup($atts, $type);
		return $markup;
		}


		return $markup;
		die();//needed !!!
	}
}
/***************************************************************
*
*	[ini]
*
***************************************************************/
$WPPIZZA_SHORTCODES = new WPPIZZA_SHORTCODES();
?>