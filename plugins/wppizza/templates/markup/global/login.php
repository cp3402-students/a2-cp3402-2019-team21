<?php
if ( ! defined( 'ABSPATH' ) ) exit;/*Exit if accessed directly*/
 /****************************************************************************************
 *
 *
 *
 *
 *	filters available:
 *	[after]
 *	('wppizza_filter_login_widget_markup', $markup): filters markup ($markup = array())
 ****************************************************************************************/
?>
<?php

			$markup['anchor'] = '<a name="' . $anchor_name . '" ></a>';
			$markup['div_']	= '<div class="' . $class . '">';

				/**
					toggle links
				**/
				$markup['div_toggle_']	 = '<div class="' . $class_toggle . '">';
					$markup['label']	 = '<label>'.$txt['loginout_have_account'].'</label> ';
					$markup['link_login']	 = '<a href="javascript:void(0);" class="' . $class_show . '" >'. __( 'Log In' ).'</a>';
					$markup['link_cancel']	 = '<a href="javascript:void(0);" class="' . $class_cancel . '" >'. __( 'Cancel' ).'</a>';
				$markup['_div_toggle']	 = '</div>';


	            /**
	            	wp login
	            **/
				$markup['fieldset_'] = '<fieldset class="' . $class_fieldset . '">';

					/*
						login form
					*/
					$markup['div_login_'] = '<div class="' . $class_form . '">';
						$markup['login'] = $login_form ;
					$markup['_div_login'] = '</div>';

        			/*
        				login errors
        			*/
        			$markup['error_info'] = '<div class="' . $class_info . '"></div>';

   					/*
   						lost password
   					*/
   					$markup['div_lost_password_'] = '<div class="' . $class_password . '">';
   						$markup['lost_password'] = '<a href="'.wp_lostpassword_url( get_permalink() ).'" title="'.__( 'Lost Password' ).'">'.__( 'Lost Password' ).'</a>';
   					$markup['_div_lost_password'] = '</div>';


				$markup['_fieldset'] = '</fieldset>';


			$markup['_div'] = '</div>';

?>