<?php
if ( ! defined( 'ABSPATH' ) ) exit;/*Exit if accessed directly*/
 /****************************************************************************************
 *
 *
 *
 * filters: wppizza_filter_post_title
 * filters: wppizza_filter_post_title_element
 * filters: wppizza_filter_post_title_class
 * filters: wppizza_filter_post_title_markup
 *
 ****************************************************************************************/
?>
<?php

	$markup['post_title_'] = '<'.$post_title_element.' id="' . $post_title_id . '" class="' . $post_title_class['elm'] . '">';

		$markup['post_title'] = '<span class="' . $post_title_class['title'] . '">' .$post_title . '</span>';


			/*add additives if needed*/
			if(count($post_additives)>0){

				/*wrap additives in sup element*/
				$markup['sup_'] = '<sup id="' . $post_additives_id .'" class="' . $post_title_class['additives'] .'" title="' . $txt['contains_additives'] . '">';

					/*additives associated with menu item*/
					$markup['post_additives'] = '';
					foreach($post_additives as $key=>$value){
						$markup['post_additives'] .= '<span id="'. $value['id'] . '" class="'. $value['class'] . '"  title="' . $value['name'] .'" >' . $value['ident'] . '</span>';
					}

				$markup['_sup'] = '</sup>';

			}

	$markup['_post_title'] = '</'.$post_title_element.'>';
?>