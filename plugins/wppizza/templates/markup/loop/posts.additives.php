<?php
if ( ! defined( 'ABSPATH' ) ) exit;/*Exit if accessed directly*/
 /****************************************************************************************
 *
 * this template is only used when added by shortcode i.e something like
 * [wppizza category="desserts" noheader="1" elements='title, thumbnail, content, additives, prices']
 *
 * filters: wppizza_filter_post_additives_element
 * filters: wppizza_filter_post_additives_class
 * filters: wppizza_filter_post_additives_markup
 *
 ****************************************************************************************/
?>
<?php

	if(count($post_additives)>0){

		/* wrap additives in span */
		$markup['element_additives_'] = '<'.$additive_loop_element.' id="' . $additives_loop_id .'" class="' . $additives_loop_class['additives'] .'" title="' . $txt['contains_additives'] . '">';

				/*additives associated with menu item*/
				$markup['element_additives'] = '';
				foreach($post_additives as $key=>$value){
					$markup['element_additives'] .= '<span id="'. $value['id'] . '" class="'. $value['class'] . '"  title="' . $value['name'] .'" >' . $value['ident'] . '</span>';
				}

		$markup['_element_additives'] = '</'.$additive_loop_element.'>';
	}
?>