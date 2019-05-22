<?php
if ( ! defined( 'ABSPATH' ) ) exit;/*Exit if accessed directly*/
 /****************************************************************************************
 *
 *
 *
 *
 *	filters available:
 *	[after]
 *	('wppizza_filter_profile_register', $markup): filters markup ($markup = array(),$atts = array())
 ****************************************************************************************/
?>
<?php

	do_action('wppizza_profile_register');

		/*********************************************************
			create account | proceed as guest options
		*********************************************************/
		/*
			div wrap open
		*/
		$markup['div_options_'] = '<div id="' .$id_create_account .'" class="' .$class_create_account .'">';
			/*
				label
			*/
			$markup['option_label'] = '<span>'.$txt['register_option_label'].'</span>';

			/*
				option guest
			*/
			$markup['option_1'] = '<label><input type="radio" id="' . $id_account_guest . '" class="' . $class_guest . '" name="' . $name . '" value="0"  ' . $checked_guest . ' />'.$txt['register_option_guest'].'</label>';

			/*
				option create account
			*/
			$markup['option_2'] = '<label><input type="radio" id="' . $id_account_register . '" class="' . $class_register . '" name="' . $name . '" value="1" '. $checked_register .' />'.$txt['register_option_create_account'].'</label>';
		/*
			div wrap close
		*/
		$markup['_div_options'] = '</div>';


		/********************************************************
			initially hidden info, shown when creating account
		********************************************************/
		/*
			div wrap open
		*/
		$markup['div_info_'] = '<div id="' .$id_register_info .'">';

			/*
				info text
			*/
	    	$markup['info'] = ''.$txt['register_option_create_account_info'].'';
		/*
			div wrap close
		*/
		$markup['_div_info']='</div>';


?>