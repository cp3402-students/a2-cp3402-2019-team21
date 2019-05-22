<?php
if ( ! defined( 'ABSPATH' ) ) exit;/*Exit if accessed directly*/
 /****************************************************************************************
 *
 *
 *
 *
 *	class/style: set by using (shortcode) attributes
 *	filters available:
 *	 [before]
 * 	('wppizza_filter_openingtimes_day_format', $format); $format = string , default 'D';
 *	('wppizza_filter_openingtimes_widget_class', $class, $atts): filters css class ($class = array())
 *
 *	 [inside - see below ]
 *	 wppizza_filter_openingtimes_widget_group_markup
 *
 *	[after]
 *	('wppizza_filter_openingtimes_widget_markup', $markup, $atts): filters markup ($markup = array(),$atts = array())
 ****************************************************************************************/
?>
<?php
		$markup['div_'] = '<div id="'.$id.'" class="'.$class.'"' . $style . '>';
			/*
				loop of grouped opening times
			*/
			foreach($openingtimes as $key=>$openingtime){
				$group = array();

				/* wrap */
				$group['span_'] = '<span class="'.$openingtime['class'].'">';

					/* day */
					$group['days_'] = '<span>';
						$group['days'] = $openingtime['days'];
					$group['_days'] = '</span>';

					/* times */
					$group['times_'] = '<span>';
						$group['times'] = $openingtime['times'];
					$group['_times'] = '</span>';


				$group['_span'] = '</span>';

			/**apply filter per group if required*/
			$group = apply_filters('wppizza_filter_openingtimes_widget_group_markup', $group, $key, $openingtime, $unique_id);
			/** implode for markup output **/
			$markup[$key]= implode('', $group);
			}

		$markup['_div'] = '</div>';
?>