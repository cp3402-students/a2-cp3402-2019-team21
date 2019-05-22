<?php
if ( ! defined( 'ABSPATH' ) ) exit;/*Exit if accessed directly*/
 /****************************************************************************************
 *
 * $txt[{key}] from Wppizza -> Localization and WPMLed if necessary
 *
 * filterable: wppizza_filter_noresults_class
 * filterable: wppizza_filter_menu_noresults_markup
 *
 ****************************************************************************************/
?>
<?php
	$markup['no_results_']= '<div class="' . $noresults_class . '">';
		$markup['no_results']= $txt['no_results_found']; 
	$markup['_no_results']= '</div>';
?>