<?php
if ( ! defined( 'ABSPATH' ) ) exit;/*Exit if accessed directly*/
 /****************************************************************************************
 *
 *
 *
 * filters: wppizza_filter_post_prices_class
 * filters: wppizza_filter_loop_prices 
 * filters: wppizza_filter_post_prices_markup
 *
 ****************************************************************************************/
?>
<?php
	$markup['post_sizes_'] = '<div id="' . $post_sizes_id . '" class="' . $post_prices_class['sizes'] . '">';
	$markup['post_sizes_ul_'] = '<ul>';

		/**
			main currency symbol left - if shown
		**/
		if(!empty($currency_left)){
			$markup['post_prices_currency'] = '<li class="' . $post_prices_class['currency'] . '">' . $currency . '</li>';
		}

		/**
			loop through prices
		**/
		$markup['post_prices_'] = '<li id="' . $post_prices_id . '" class="' . $post_prices_class['prices'] . '" >';

			$markup['post_prices_ul_'] = '<ul>';

			foreach($prices as $key=>$price){
				$markup['post_price_'.$key.'_'] = '<li id="' . $price['id'] . '" class="' . $price['class_price'] . '" ' . $price['title'] . '>';

					/*price - i18n formatted*/
					$markup['post_price_'.$key.'_span'] = '<span>' . $price['price'] . '</span>';

					/*label, omit if set to not display when only one */
					if(empty($price['no_label'])){
						$markup['post_price_'.$key.'_label'] = '<div class="' . $price['class_size'] . '">' . $price['size'] . '</div>';
					}

				$markup['_post_price_'.$key.''] = '</li>';
			}

			$markup['_post_prices_ul'] = '</ul>';

		$markup['_post_prices'] = '</li>';



		/**
			main currency symbol right - if shown
		**/
		if(!empty($currency_right)){
			$markup['post_prices_currency'] = '<li class="' . $post_prices_class['currency'] . '">' . $currency . '</li>';
		}


		$markup['_post_sizes_ul'] = '</ul>';
	$markup['_post_sizes'] = '</div>';

?>