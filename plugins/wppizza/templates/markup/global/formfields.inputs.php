<?php
if ( ! defined( 'ABSPATH' ) ) exit;/*Exit if accessed directly*/
 /****************************************************************************************
 *
 *
 *
 *
 *	filters available:
 *	[after]
 *	('wppizza_filter_formfields_inputs', $markup, $formfields): filters markup ($markup = array(),$formfields = array())
 ****************************************************************************************/
?>
<?php

	do_action('wppizza_formfields_inputs', $formfields);

	foreach($formfields as $key => $formfield){

		do_action('wppizza_formfield_input_'.$key.'', $formfield);

		/*
			div wrap open
		*/
		$markup['div_'.$key.'_'] = '<div class="' .$formfield['class'] .'">';


			/*
				lables and inputs
			*/
			$markup['field_'.$key.''] = $formfield['field'];


		/*
			div wrap close
		*/
		$markup['_div_'.$key.''] = '</div>';
	}

?>