<?php

if(!class_exists('FmFunction')):

	/**
	*
	*/
	class FmFunction
	{

		function __construct()
		{

		}

		function getPrice(){
			$price = get_post_meta( get_the_id(), 'price', true );
		}

		function getCurrency(){
			global $TLPfoodmenu;
			$settings = get_option($TLPfoodmenu->options['settings']);
			$currency = ($settings['general']['currency'] ? esc_attr( $settings['general']['currency'] ) : "USD");
			return $currency;
		}

		function getPriceWithLabel(){
			global $TLPfoodmenu;
			$settings = get_option($TLPfoodmenu->options['settings']);
			$currency = (@$settings['general']['currency'] ? esc_attr( @$settings['general']['currency'] ) : "USD");
			$currencyP = (@$settings['general']['currency_position'] ? esc_attr( @$settings['general']['currency_position'] ) : "right");
			@$price = get_post_meta( get_the_id(), 'price', true );
			$cList = $TLPfoodmenu->currency_list();
			$symbol = $cList[$currency]['symbol'];

			switch ($currencyP) {
				case 'left':
					$price = $symbol.$price;
				break;

				case 'right':
					$price = $price.$symbol;
				break;

				case 'left_space':
					$price = $symbol. " " .$price;
				break;

				case 'right_space':
					$price = $price . " " . $symbol;
				break;

				default:

				break;
			}

			return $price;
		}
	}


endif;
