<?php
/**
* WPPIZZA_TEMPLATES_OPENINGTIMES Class
*
* @package     WPPIZZA
* @subpackage  Opening times
* @copyright   Copyright (c) 2015, Oliver Bach
* @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
* @since       3.0
*
*/
if ( ! defined( 'ABSPATH' ) ) exit;/*Exit if accessed directly*/

/* ================================================================================================================================= *
*
*
*
*	CLASS - WPPIZZA_TEMPLATES_OPENINGTIMES
*
*
*
* ================================================================================================================================= */

class WPPIZZA_MARKUP_OPENINGTIMES{

	/******************************************************************************
	*
	*
	*	[construct]
	*
	*
	*******************************************************************************/
	function __construct() {
	}

	/******************************************************************************
	*
	*
	*	[methods]
	*
	*
	*******************************************************************************/
	/***************************************
		[apply attributes]
	***************************************/
	function attributes($atts=null){
		
		
		/***************************************
			skip if not cart or shortcode
		***************************************/
		if($atts['type'] != 'cart' && $atts['type'] != 'openingtimes'){
			return;
		}
		/***************************************
			skip if cart and not set /enabled 
		****************************************/
		if($atts['type'] == 'cart' && empty($atts['openingtimes']) ){
			return;
		}
		
		/**get markup**/
		$markup = $this->get_markup($atts);
		return $markup;
	}
	
	/***************************************
		[markup]
	***************************************/
	function get_markup($atts){	
		global $wppizza_options;
		static $unique_id=0;$unique_id++;/* set unique id */
		
		$txt = $wppizza_options['localization'];/*put all text varibles into something easier to deal with**/		
		
		$weekDayStart=get_option('start_of_week',7);
		/* filter to use long day format */
		$day_format = apply_filters('wppizza_filter_openingtimes_day_format', 'D');/* filter to return 'l' if you want to return full textual representation */
		$day_format = ($day_format=='l') ? 'l' : 'D' ;/*force D or l as nothing else makes sense here really*/
		
		
		
		/**group identical standard opening times**/
		foreach($wppizza_options['openingtimes']['opening_times_standard'] as $k=>$v){
			if($k==0 && $weekDayStart!=0){$k=7;}/*for sorting reasons , set sunday temporarily to 7 here unless weekstart is set to sunday anyway**/
			/*
				do not group days with the same opening hours
			*/
			$nogrouping = !empty($wppizza_options['opening_times_format']['dont_group_days']) ? '|'.$k : '';
			
			if(!isset($times[''.$v['open'].'|'.$v['close'].''.$nogrouping])){
				$times[''.$v['open'].'|'.$v['close'].''.$nogrouping]=array();
				$times[''.$v['open'].'|'.$v['close'].''.$nogrouping][]=$k;
			}else{
				$times[''.$v['open'].'|'.$v['close'].''.$nogrouping][]=$k;
			}
		}

		foreach($times as $k=>$arr){
			/*to have sundays last when sorting, set it to 7*/
			asort($arr);
			$grouped[$k]=array('firstday'=>reset($arr),'days'=>$arr,'consecutivedays'=>$this->days_concat($arr));
		}

		/**sort by first day in array so we start with a monday regardless**/
		asort($grouped);

		/**ini array**/
		$openingtimes = array();
		foreach($grouped as $k=>$v){
			$nonConsec=explode(",",$v['consecutivedays']);
			$groupClasses='';
			$groupDays='';
			
			foreach($nonConsec as $b=>$c){
				$groupClasses.=' '.WPPIZZA_PREFIX.'-optm-'.$c.'';
				$consecDays=explode("-",$c);
				if($b>0){$groupDays.=', ';}
				if(count($consecDays)>1){
					/**create appropriate separator**/
					if(($consecDays[0]+1)==$consecDays[1]){$separator=', ';}else{$separator='-';}
					foreach($consecDays as $cc=>$cd){
						if($cc>0){
							$groupDays.=$separator;
						}
						$groupDays.=wppizza_format_weekday($cd,$day_format);
					}
				}else{
						$groupDays.=wppizza_format_weekday($consecDays[0],$day_format);
				}
			}
			
			/* class */
			$openingtimes[$k]['class'] = trim($groupClasses);
			
			/* days */
			$openingtimes[$k]['days'] = $groupDays;
			
			/* times */
			$open=explode("|",$k);
			if($open[0]==$open[1]){
				$openingtimes[$k]['times'] = $txt['openinghours_closed'];
			}else{
				if(($open[0]=='00:00' || $open[0]=='0:00' ) && $open[1]=='24:00'){						
					$openingtimes[$k]['times'] = $txt['openinghours_24hrs'];
					
				}else{
					$openingtimes[$k]['times'] = wppizza_format_time($open[0],$wppizza_options['opening_times_format']).'-'.wppizza_format_time($open[1],$wppizza_options['opening_times_format']);
				}
			}
		}
		/* set unique id */	
		$id	= WPPIZZA_PREFIX.'-opening-hours-'.$unique_id;	
				
		/* set width , if defined */
		$style = (!empty($atts['width'])) ? ' style="width:'.esc_html($atts['width']).'"' : '' ;
		
		/* classes */
		$class= array();
		/* class for all */
		$class[] = WPPIZZA_PREFIX.'-opening-hours';
		/* if not from shortcode (i.e part of cart) */
		if($atts['type']!='openingtimes'){
		$class[] = WPPIZZA_PREFIX.'-opening-hours-'.$atts['type'];
		}
		if(!empty($atts['class'])){
			$class[] = esc_html($atts['class']);
		}		
		/*
			allow class filtering
			implode for output
		*/
		$class = apply_filters('wppizza_filter_openingtimes_widget_class', $class, $atts, $unique_id);		
		$class = trim(implode(' ', $class));		
		

		/*
			ini array
		*/
		$markup = array();
		/* 
			get markup
		*/
		if(file_exists( WPPIZZA_TEMPLATE_DIR . '/markup/global/openingtimes.php')){
			require(WPPIZZA_TEMPLATE_DIR.'/markup/global/openingtimes.php');
		}else{
			require(WPPIZZA_PATH.'templates/markup/global/openingtimes.php');
		}					
		/*
			apply filter if required and implode for output
		*/
		$markup = apply_filters('wppizza_filter_openingtimes_widget_markup', $markup, $atts, $unique_id);
		$markup = trim(implode('', $markup));

			
	return $markup;
	}

	/***************************
		helper
	***************************/
		
	function days_concat($days){
	
	    // Define all days of the week, st sun(0) to 7
	    static $all_days = array('0','1', '2', '3', '4', '5', '6','7');
	
	    // prepare our output
	    $output = array();
	
	    // loop through all days of the week
	    foreach ( $all_days as $i => $day ){
	        // if it is included,
	        if ( in_array( $day, $days ) ){
	            $output[] = $day;
	        }else{/*if not*/
	            $output[] = '#';
	        }
	    }
	
	    // clean everything up
	    $output = implode( '#', $output );
	    $output = trim( $output, '#' );
	
	    // two or more consecutive hashes = days that are two or more apart
	    $output = preg_split( '/##+/', $output, NULL, PREG_SPLIT_NO_EMPTY );
	
	    // turn consecutive days into dashed days
	    foreach ( $output as $i => $value ){
	    	$output[ $i ] = preg_replace( '/#(\w+#)*/', '-', $value );
	    }
	    // format with commas
	    $output = implode( ',', $output );
	
	return $output;
	}		

}
?>