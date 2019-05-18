<?php
if ( ! defined( 'ABSPATH' ) ) exit;/*Exit if accessed directly*/
 /****************************************************************************************
 *
 *
 *
 *	filters available:
 *	[before]
 *	('wppizza_filter_orderinfo_widget_class', $class, $atts): filters css class ($class = array(), $atts = array())

 *	[after]
 *	('wppizza_filter_orderinfo_widget_markup', $markup = array(), $atts = array(), $discounts = array(), $deliveries = array())
 ****************************************************************************************/
?>
<?php
		$markup['ul_'] = '<ul id="'.$id.'" class="'.$class.'"' . $style . '>';
			/*
				discount li's
			*/
			foreach($discounts as $key=>$discount){
				$markup['discount_'.$key.''] = '<li class="'.$class.'-discount">' . $discount . '</li>';
			}
			
			/*
				delivery charges li's
			*/
			foreach($deliveries as $key=>$delivery){
				$markup['delivery_'.$key.''] = '<li class="'.$class.'-charges">' . $delivery . '</li>';
			}

		$markup['_ul'] = '</ul>';
?>