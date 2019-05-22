<?php
if ( ! defined( 'ABSPATH' ) ) exit;/*Exit if accessed directly*/
 /****************************************************************************************
 *
 *
 *
 *
 *
 * filter after : wppizza_filter_menu_additives_markup 
 ****************************************************************************************/
?>
<?php
	$markup['additives_'] = '<div class="' . $class . '">';
		foreach($additives as $key=>$additive){
			$markup[$key] = '<span class="' . $additive['class'] . '"><sup>' . $additive['ident'] . '</sup>' . $additive['name'] . '</span>';
		}
	$markup['_additives'] = '</div>';
?>