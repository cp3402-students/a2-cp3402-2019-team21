<?php
if ( ! defined( 'ABSPATH' ) ) exit;/*Exit if accessed directly*/
 /****************************************************************************************
 *
 *
 *
 *
 *	filters available:
 *	[after]
 *	('wppizza_filter_profile_update_markup', $markup): filters markup ($markup = array())
 ****************************************************************************************/
?>
<?php

	do_action('wppizza_profile_update');

		/*
			div wrap open
		*/
		$markup['div_']='<div class="' . $class . '">';

		/*
			label wrap open
		*/
		$markup['label_']='<label for="' . $id . '">';

		/*
			checkbox
		*/
		$markup['input']='<input id="' . $id . '" name="' . $name . '" type="checkbox" value="1" '.$checked.' />';

		/*
			label
		*/

		$markup['label']='' . $txt['update_profile'] . '';


		/*
			label wrap close
		*/
		$markup['_label']='</label>';


		/*
			label wrap close
		*/
		$markup['_div']='</div>';

?>