<?php
/**
* WPPIZZA_FILTERS Class
*
* @package     WPPIZZA
* @subpackage  WPPIZZA_FILTERS
* @copyright   Copyright (c) 2015, Oliver Bach
* @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
* @since       3.0
*
*/
if ( ! defined( 'ABSPATH' ) ) exit;/*Exit if accessed directly*/


/************************************************************************************************************************
*
*
*	MISCELLANEOUS WPPIZZA_FILTERS
*
*
************************************************************************************************************************/
class WPPIZZA_FILTERS{
	function __construct() {

		add_action('init', array( $this, 'wppizza_allow_options_filter'), 5);/*allow filtering of options. let's use a reasonably high priority, but after session initialization**/

		/****filter plaintext email and print template markup******/
		add_filter( 'wppizza_filter_template_plaintext_message_markup', array( $this, 'wppizza_filter_template_plaintext_message_markup'),10,3);
		/****filter line to plaintext ******/
		add_filter( 'wppizza_filter_plaintext_line', array( $this, 'wppizza_filter_plaintext_line'),10,3);

		/****filter order dates ******/
		add_filter( 'wppizza_filter_order_date', array( $this, 'wppizza_filter_order_date'),10);


		/***dont put "WPPizza Categories" in title tag */
		add_filter( 'wp_title', array( $this, 'wppizza_filter_title_tag'),20,3);


		/***filter tax display */
		add_filter( 'wppizza_filter_combine_taxes', array( $this, 'wppizza_filter_combine_taxes'));



		/****************************************************
		*	remove all but qty, article, price and delete from header/itme columns if
		* using gettotal shortcode or is main cart to maximise space
		*****************************************************/
		add_filter('wppizza_filter_order_item_header_markup', array( $this, 'gettotals_cart_header_columns'),100, 3);
		add_filter('wppizza_filter_order_item_columns', array( $this, 'gettotals_cart_item_columns'),100, 8);

		/****************************************************
		*	reverse header and item columns for rtl
		*	if the theme - as it should - sets the css to be body{direction:rtl} then
		*	the below should not be necessary , but add a constant that can be used to
		*	force td's to be rtl
		*****************************************************/
		if(WPPIZZA_FORCE_RTL_ON_TABLES){
		 	add_filter('wppizza_filter_order_item_header_markup', array( $this, 'order_header_columns_rtl'),1000, 1);
			add_filter('wppizza_filter_order_item_columns', array( $this, 'order_item_columns_rtl'),1000, 1);
		}



		/************************************************************************
			[runs only for frontend]
		*************************************************************************/
		if(!is_admin()){

    		/*****************************************************
     		* Wrapper template when displying items in custom post type category
     		* [see header of templates/markup/loop/theme-wrapper.php for details]
     		******************************************************/
			add_filter('template_include', array( $this, 'include_loop_template'), 1 );

			/*****************************************************
			* locate search template to display wpizza type layout
			* in search results
			*****************************************************/
			add_filter('template_include', array( $this, 'include_search_template'), 1 );

		}

		/************************************************************************
			[order form - recaptcha]
		*************************************************************************/
		add_filter('wppizza_filter_pages_order_markup', array( $this, 'include_invisible_recaptcha'));

	}
    /*****************************************************
     * include invisible recaptcha https://en-gb.wordpress.org/plugins/invisible-recaptcha/
     * if constant true recaptcha plugin must be set up with secret keys etc
     ******************************************************/
	public function include_invisible_recaptcha($markup){
		if(WPPIZZA_ENABLE_INVISIBLE_CAPTCHA){
			ob_start();
			do_action('google_invre_render_widget_action');
			$captcha = ob_get_contents();
			ob_end_clean();
		$markup['_form'] = $captcha . $markup['_form'];
		}
	return $markup;
	}

    /*****************************************************
     * Wrapper template when displying items in custom post type category
     * [see header of templates/markup/loop/theme-wrapper.php for details]
     ******************************************************/
	public function include_loop_template($template_path){

		/******
			list of all items in this particular taxonomy category(term), provided ist not a search query
		*****/
		if ( get_post_type() == WPPIZZA_SLUG && !is_search()) {
			global $wppizza_options, $post;

			/*
				get current category slug
			*/
			$queried_object = sanitize_post( get_queried_object() );
			$current_term = $queried_object->slug;

			/*
				get terms of post id
			*/
			$terms = wp_get_post_terms( $post->ID, WPPIZZA_TAXONOMY);
			$terms = array_shift( $terms );


			/*****************************************
				loop
			*****************************************/
			if ( !is_single() ) {

				/**
					shortcode arguments
				**/
				$sc_args = array();

				/*exclude header*/
				if($wppizza_options['layout']['suppress_loop_headers']){
					$sc_args['header'] = 'noheader="1"';
				}

				/* category */
				$sc_args['category'] = 'category="'.$current_term.'"';

				/* filter->implode */
				$sc_args = apply_filters('wppizza_filter_wrapper_arguments', $sc_args, $terms);
				$sc_args = ''.WPPIZZA_SLUG.' '.implode(' ', $sc_args);

				/** get the shortcode **/
				$do_wppizza_loop = do_shortcode("[" . $sc_args . "]");


				/*check if the file exists in the theme, otherwise serve the file from the plugin directory if possible*/
				if ($theme_file = locate_template( array (WPPIZZA_LOCATE_DIR.'markup/loop/theme-wrapper.php' ))){
					include($theme_file);
					return;
				}
				/*check if it exists in plugin directory, otherwise we will have to serve defaults**/
				if (is_file( WPPIZZA_PATH . 'templates/markup/loop/theme-wrapper.php')){
					$theme_file=''.WPPIZZA_PATH.'templates/markup/loop/theme-wrapper.php';

					include($theme_file);
					return;
				}
			}


			/*****************************************
				single item
			*****************************************/
			if ( is_single() ) {

				/**
					shortcode arguments
				**/
				$sc_args = array();

				/* single */
				$sc_args['single'] = 'single="'.$post->ID.'"';

				/* filter -> implode */
				$sc_args = apply_filters('wppizza_filter_single_post_arguments', $sc_args, $terms);
				$sc_args = ''.WPPIZZA_SLUG.' '.implode(' ', $sc_args);

				/** get the shortcode **/
				$do_single = do_shortcode("[" . $sc_args . "]");


				/*
					check if the file exists in the theme
				*/
				if ($theme_file = locate_template( array (WPPIZZA_LOCATE_DIR.'markup/single/single.php' ))){
					include($theme_file);
					return;
				}

				/*
					file does not exists in theme directory
					serve default in plugin directory (shows instructions)
					- - currently disabled - as people just get confused
				*/
				if (is_file( WPPIZZA_PATH . 'templates/markup/single/single.php')){
				//	$theme_file=''.WPPIZZA_PATH.'templates/markup/single/single.php';
				//	include($theme_file);
				//	return;
				}

			}
		}

		return $template_path;
	}

    /*****************************************************
    *
    *	display wppizza posts in search results with
    * wppizza style / type layout
    ******************************************************/
	public function include_search_template($template_path){

		if (is_search()) {

			/*
				check if the file exists in the theme
			*/
			if ($theme_file = locate_template( array (WPPIZZA_LOCATE_DIR.'markup/search/search.php' ))){
				include($theme_file);
				return;
			}

		}
		return $template_path;
	}

	/**********************************************************************************
	*	gettotals / cart shortcode header/items columns
	**********************************************************************************/
	function gettotals_cart_header_columns($markup_header, $txt, $type){
		if($type == 'gettotals' || $type == 'cart' ){//|| $type = 'minicart' ? perhaps
			$include_only_keys = array('thead_th_quantity', 'thead_th_article', 'thead_th_total', 'item_th_delete', 'item_th_sku');
			$include_only_keys = apply_filters('wppizza_filter_include_only_header',$include_only_keys, $type);
			foreach($markup_header as $key=>$markup){
				if(!in_array($key, $include_only_keys)){
					unset($markup_header[$key]);
				}
			}
		}
	return $markup_header;
	}
	function gettotals_cart_item_columns($item_column, $key , $item, $cart, $item_count, $order_id, $txt, $type){

		if($type == 'gettotals' || $type == 'cart' ){// || $type = 'minicart' ? perhaps
			$include_only_keys = array('item_td_quantity','item_td_article', 'item_td_total', 'item_td_delete', 'item_td_sku');
			$include_only_keys = apply_filters('wppizza_filter_include_only_columns',$include_only_keys, $type);
			foreach($item_column as $key=>$markup){
				if(!in_array($key, $include_only_keys)){
					unset($item_column[$key]);
				}
			}
		}

	return $item_column;
	}
	/**********************************************************************************
	*	rtl - below rtl functions currently not in use as theyu should not be needed
	*	see note above before the filter that calls these
	**********************************************************************************/
	function order_header_columns_rtl($array){
		if(is_rtl()){
			$array = array_reverse($array);
		}
		return $array;
	}
	function order_item_columns_rtl($array){
		if(is_rtl()){
			$array = array_reverse($array);
		}
		return $array;
	}



	/*******************************************************************************
	*
	*	add hook for filtering global wppizza_options before anything else
	*	will be bypassed by  default in admin pages
	*******************************************************************************/
	function wppizza_allow_options_filter(){
		global $wppizza_options;
		/* skip for admin, but not for ajax */
		if(is_admin() && (!defined('DOING_AJAX') || !DOING_AJAX) ){
			return $wppizza_options;
		}

		/**get options as global var, allow filtering**/
		$wppizza_options = apply_filters('wppizza_filter_options', $wppizza_options);//, $session_data['order'], $session_data['user']

		/**
			in case someone forgets to return anything from the filter, make sure we use originals,
			or the plugin would get reinstalled as no version info/option would be set !!!
		**/
		if(empty($wppizza_options)){
			$wppizza_options = get_option(WPPIZZA_SLUG);
		}

	return $wppizza_options;
	}

	/*******************************************************************************
	*
	*	filter each line for consistent linespacing in plaintext output
	*
	*******************************************************************************/
	function wppizza_filter_plaintext_line($line, $separator = ' ', $center_br = false, $maxchar = WPPIZZA_PLAINTEXT_MAX_LINE_LENGTH){

		/*

		*/


		/******************************************************
			string or single key array -> centre, separator either side
		******************************************************/
		if(is_string($line) || count($line)==1){


			/*reset to get first (and only) element unless its a string anyway */
			$val=is_string($line) ? wppizza_decode_strip_length($line) : wppizza_decode_strip_length(reset($line));


			/** center any strings with linebreaks if enabled **/
			if($center_br){

				$lines = array();

				$explode_by_eol = explode(PHP_EOL, $val['string']);
				foreach($explode_by_eol as $new_line){
					$line_value = wppizza_decode_strip_length($new_line);
					/* string is <= max char, pad */
					if($line_value['length']<= $maxchar){
						/*padcount -2 to have one space either side of str regarless of separater set*/
						$pad=(($maxchar-$line_value['length']-2)/2);
						if(is_int($pad)){
							$pad_left = $pad;
							$pad_right = $pad;
						}else{
							$floor = floor($pad);
							$pad_left = $floor;
							$pad_right = $floor+1;
						}

						/* implode pad left | string | pad right */
						$str = array();
						$str['pad_left'] = str_pad('' , $pad_left, $separator);

						$str['string'] = ($line_value['string']!='') ? ' ' .$line_value['string'] . ' ' : $separator.$separator ;/* always add 1 space either side - unless str is empty*/

						$str['pad_right'] = str_pad('' , $pad_right, $separator);

						/* implode to line */
						$lines[] = implode('',$str);

					/* string is > max char, just wordwrap */
					}else{
						$lines[]=wordwrap($line_value['string'], WPPIZZA_PLAINTEXT_MAX_LINE_LENGTH_WORDWRAP, PHP_EOL, false);
					}
				}
				/* implode  */
				$row = implode(PHP_EOL,$lines);
			}

			/* center on single line */
			if(!$center_br){
				/* string is <= max char, pad */
				if($val['length']<= $maxchar){
					/*padcount -2 to have one space either side of str regarless of separater set*/
					$pad=(($maxchar-$val['length']-2)/2);
					if(is_int($pad)){
						$pad_left = $pad;
						$pad_right = $pad;
					}else{
						$floor = floor($pad);
						$pad_left = $floor;
						$pad_right = $floor+1;
					}

					/* implode pad left | string | pad right */
					$str = array();
					$str['pad_left'] = str_pad('' , $pad_left, $separator);

					$str['string'] = ($val['string']!='') ? ' ' .$val['string'] . ' ' : $separator.$separator ;/* always add 1 space either side - unless str is empty*/

					$str['pad_right'] = str_pad('' , $pad_right, $separator);

					/* implode to line */
					$row = implode('',$str);
				}
				/* string is > max char, just wordwrap */
				else{
					$row=wordwrap($val['string'], WPPIZZA_PLAINTEXT_MAX_LINE_LENGTH_WORDWRAP, PHP_EOL, false);
				}
			}
		}


		/******************************************************
			left / right array -> separator between
		******************************************************/
		if(is_array($line) && count($line)== 2 ){

			/* get left and right values (string and length)*/
			$val = array();
			$left = reset($line);
			$right = end($line);

			/** explode right by lines to use first key as right, all others as full line , right**/
			$right = explode(PHP_EOL,$right);


			$val['left'] = wppizza_decode_strip_length($left);
			$val['right'] = wppizza_decode_strip_length($right[0]);

			/*padcount strlen lft/right + 2 to allow for spaces after first / before last */
			$pad=($maxchar-($val['left']['length']+$val['right']['length']+2));

			/* implode string left | pad | string right */
			$str = array();

			/** invert for rtl **/
			if(!is_rtl()){
				$str['str_left'] = empty($val['left']['string']) ? $separator : $val['left']['string'].' ';/*add space after but if empty add separator */
				$str['pad'] = str_pad('' , $pad, $separator);
				$str['str_right'] = empty($val['right']['string']) ? $separator : ' '.$val['right']['string'];/*add space before  but if empty add separator */
			}else{
				$str['str_right'] = empty($val['right']['string']) ? $separator : $val['right']['string'] . ' ';/*add space before  but if empty add separator */
				$str['pad'] = str_pad('' , $pad, $separator);
				$str['str_left'] = empty($val['left']['string']) ? $separator : ' ' .$val['left']['string'];/*add space after but if empty add separator */			}

			/* additional lines from multiline inputs */
			if(count($right)>1){
				foreach($right as $x => $additional_line){
					if($x!=0){
						$xtra_line = wppizza_decode_strip_length($additional_line);
						$xpad = ($maxchar-$xtra_line['length']-1); /* -1 to force one space to left */
						/** EOL +  separator + string **/
						$str[$x] = PHP_EOL;

						/** invert for rtl **/
						if(!is_rtl()){
							$str[$x] .= str_pad('' , $xpad, $separator);
							$str[$x] .= ' ' . $xtra_line['string'] ;
						}else{
							$str[$x] .= $xtra_line['string'] . ' ';
							$str[$x] .= str_pad('' , $xpad, $separator);
						}
					}
				}
			}
			/* implode to line(s) */
			$row = implode('',$str);
			if($val['left']['length'] >= WPPIZZA_PLAINTEXT_MAX_LINE_LENGTH_WORDWRAP){
				$row=wordwrap($row, WPPIZZA_PLAINTEXT_MAX_LINE_LENGTH_WORDWRAP, PHP_EOL, false);
			}
			/* should possibly be if($val['right']['length'] - $val['left']['length']) but of the time being leave as is */
			if($val['right']['length'] >= WPPIZZA_PLAINTEXT_MAX_LINE_LENGTH_WORDWRAP){
				$row=wordwrap($row, WPPIZZA_PLAINTEXT_MAX_LINE_LENGTH_WORDWRAP, PHP_EOL, false);
			}
		}

		/******************************************************
			array count >= 3 -> last key = right, all others = left
		******************************************************/
		if(is_array($line) && count($line)>= 3 ){
			/* make left and right values*/
			$str['left'] = array();
			$str['right'] = array();
			/*loop through vals adding to left and right as appropriate*/
			$count = 1;
			$array_count = count($line);
			foreach($line as $string){
				if($count != $array_count){
					$str['left'][] = $string;/* add space to right*/
				}else{
					$str['right'][] = $string;/* add space to left of final value*/
				}
			$count++;
			}
			/* invert for rtl **/
			if(is_rtl()){
				$str['left'] = array_reverse($str['left']);
			};
			$str['left'] = implode(' ', $str['left']);
			$str['right'] = implode(' ', $str['right']);

			/* get left and right values*/
			$val['left'] = wppizza_decode_strip_length($str['left']);
			$val['right'] = wppizza_decode_strip_length($str['right']);

			/* padcount strlen lft/right */
			$pad=($maxchar-($val['left']['length']+$val['right']['length']+2));

			$str = array();
			/** invert for rtl **/
			if(!is_rtl()){
				$str['str_left'] = $val['left']['string'].' ';/*add space after*/
				$str['pad'] = str_pad('' , $pad, $separator);
				$str['str_right'] = ' '.$val['right']['string'];/*add space before*/
			}else{
				$str['str_right'] = $val['right']['string']. ' ';/*add space after*/
				$str['pad'] = str_pad('' , $pad, $separator);
				$str['str_left'] =  ' ' . $val['left']['string'];/*add space before*/
			}

			/* implode to line(s) */
			$row = implode('',$str);
		}



	return $row;
	}
	/*******************************************************************************
	*
	*	filter template markups - plaintext
	*	returning sections as formatted strings with consistant spacing
	*******************************************************************************/
	function wppizza_filter_template_plaintext_message_markup($plaintext_sections, $template_id, $type){
		/** ini new arrays **/
		$plaintext_sections_formatted = array();
		$plaintext_formatted = array();
		/*
			set max linelength
		*/
		$maxchar = WPPIZZA_PLAINTEXT_MAX_LINE_LENGTH;

		/*
			loop through sections
			and space consistently per line
		*/
		foreach($plaintext_sections as $section_key=>$section_values){
			$plaintext_sections_formatted[$section_key] = array();

			/*
				set different spacing character for
				section_label for order
			*/
			foreach($section_values as $line_key=>$line_values){
				/*
					set spacer '-' only for item/order header
				*/
				$spacer = ($section_key=='order' && $line_key='section_label') ? '-' : ' ';


				/*
					simple string
				*/
				if(is_string($line_values)){
					/*get decoded strings and string length*/
					$val=wppizza_decode_strip_length($line_values);

					/*padcount -2 to have one space either side of str */
					$pad=(($maxchar-$val['length']-2)/2);
					if(is_int($pad)){
						$pad_left = $pad;
						$pad_right = $pad;
					}else{
						$floor = floor($pad);
						$pad_left = $floor;
						$pad_right = $floor+1;
					}

					/*
						implode pad left | string | pad right
					*/

					$str = array();
					$str['pad_left'] = str_pad('' , $pad_left, $spacer);
					$str['string'] = ' ' .$val['string'] . ' ';/* always add 1 space either side*/
					$str['pad_right'] = str_pad('' , $pad_right, $spacer);

					/*implode_section line */
					$plaintext_sections_formatted[$section_key][$line_key] = implode('',$str);

				}
				/*
					single value - center
				*/
				if(is_array($line_values) && count($line_values) == 1 ){
					/*reset to get first (and only) element */
					$val=wppizza_decode_strip_length(reset($line_values));

					/*padcount -2 to have one space either side of str */
					$pad=(($maxchar-$val['length']-2)/2);
					if(is_int($pad)){
						$pad_left = $pad;
						$pad_right = $pad;
					}else{
						$floor = floor($pad);
						$pad_left = $floor;
						$pad_right = $floor+1;
					}

					/*
						implode pad left | string | pad right
					*/

					$str = array();
					$str['pad_left'] = str_pad('' , $pad_left, $spacer);
					$str['string'] = ' ' .$val['string'] . ' ';/* always add 1 space either side*/
					$str['pad_right'] = str_pad('' , $pad_right, $spacer);

					/*implode_section line */
					$plaintext_sections_formatted[$section_key][$line_key] = implode('',$str);

				}
				/*
					two values - left/right
				*/
				if(is_array($line_values) && count($line_values) == 2 ){
					/* get left and right values*/
					$val['left'] = wppizza_decode_strip_length(reset($line_values));
					$val['right'] = wppizza_decode_strip_length(end($line_values));

					/*padcount strlen lft/right + 2 to allow for spaces after/before */
					$pad=($maxchar-($val['left']['length']+$val['right']['length']+2));

					$str = array();
					$str['str_left'] = empty($val['left']['string']) ? $spacer : $val['left']['string'].' ';/*add space after but if empty add char */
					$str['pad'] = str_pad('' , $pad, $spacer);
					$str['str_right'] = empty($val['right']['string']) ? $spacer : ' '.$val['right']['string'];/*add space before  but if empty add char */

					/*implode_section line */
					$plaintext_sections_formatted[$section_key][$line_key] = implode('',$str);
				}
				/*
					multiple values - all left except last one to right
				*/
				if(is_array($line_values) && count($line_values) > 2 ){
					/* make left and right values*/
					$str['left'] = '';
					$str['right'] = '';
					/*loop through vals adding to left and right as appropriate*/
					$count = 1;
					foreach($line_values as $string){
						if($count!=count($line_values)){
							$str['left'].= $string . ' ';/* add space to right*/
						}else{
							$str['right'].= ' '.$string;/* add space to left of final value*/
						}
					$count++;
					}
					/* get left and right values*/
					$val['left'] = wppizza_decode_strip_length($str['left']);
					$val['right'] = wppizza_decode_strip_length($str['right']);

					/* padcount strlen lft/right */
					$pad=($maxchar-($val['left']['length']+$val['right']['length']+2));

					$str = array();
					$str['str_left'] = $val['left']['string'].' ';/*add space after as the will have been trimmed*/
					$str['pad'] = str_pad('' , $pad, $spacer);
					$str['str_right'] = ' '.$val['right']['string'];/*add space before as the will have been trimmed*/

					/*implode_section line */
					$plaintext_sections_formatted[$section_key][$line_key] = implode('',$str);
				}
			}
		}

		/**implode per section **/
		$plaintext_formatted=array();
		foreach($plaintext_sections_formatted as $section_key => $section_lines ){
			/*implode by PHP_EOL */
			$plaintext_formatted[$section_key]=implode(PHP_EOL,$section_lines);
		}


		/**full order plaintext string**/
		$plaintext = array();
		$plaintext['formatted'] = implode(PHP_EOL.PHP_EOL.PHP_EOL,$plaintext_formatted);/*implode with linebreaks between sections*/
		$plaintext['sections'] =' TODO perhaps: sections without headers';

	return $plaintext;
	}

	/*******************************************************************************
	*
	*
	*	filter transaction dates - i18n format of transaction date depending on wp date/time settings
	*
	*
	*******************************************************************************/
	function wppizza_filter_order_date($date, $date_format = false){
		if(empty($date) || $date == '0000-00-00 00:00:00'){return;}

		if(!$date_format){
			$date_format['date'] = get_option('date_format');
			$date_format['time'] = get_option('time_format');
		}
		/*
			we could also pass a timestamp if necessary
		*/
		$time = is_int($date) ? $date : strtotime($date);
		$transaction_date="".date_i18n($date_format['date'],$time)." ".date_i18n($date_format['time'],$time)."";
		return $transaction_date;
	}

	/*******************************************************************************
	*
	*
	*	[lets attempt to get rid of WPPizza Categories in title tag
	*
	*
	*******************************************************************************/
	function wppizza_filter_title_tag($title, $sep=false , $loc='right'){
		if(get_post_type()==WPPIZZA_POST_TYPE){

			$titleOrig=$title;

			/**for safeties sake loop through all conotations (though the last one probanly does the trick) */
			$catTitleSearch[] = __('WPPizza Categories', 'wppizza-admin');
			$catTitleSearch[] = __('Categories WPPizza', 'wppizza-admin');
			$catTitleSearch[] = WPPIZZA_NAME . ' ' .__('Categories');

			foreach($catTitleSearch as $strSearch){

				if($sep && $loc=='right'){
					$title=str_ireplace(''.$strSearch.' '.$sep.'','',$title);
				}
				if($sep && $loc!='right'){
					$title=str_ireplace(''.$sep.' '.$strSearch.'','',$title);
				}
				/*if we dont have a seperator or nothing has been done yet and its still the same, just try a normal str replace*/
				if(!$sep || trim($sep)=='' || $title==$titleOrig){
					$title=str_ireplace($strSearch,'',$title);
				}
				/**as last resort if it's still in the title somehow***/
				$pos = stripos($title, $strSearch);
				if ($pos !== false) {
	    			$title=str_ireplace($strSearch,'',$title);
	    			/*and - just to be sure - replace any leftover double seperators with single ones**/
	    			$title=str_replace($sep.$sep,$sep,$title);
				}

				/**might as well trim it*/
				$title=trim($title);
			}
		}

		return $title;
	}

	/*******************************************************************************
	*
	*
	*	[filter the way taxes are displayed
	*
	*
	*******************************************************************************/
	function wppizza_filter_combine_taxes($tax_display){
		global $wppizza_options;
		if($wppizza_options['order_settings']['taxes_display'] == 1 ){
			$tax_display = false;
		}
		if($wppizza_options['order_settings']['taxes_display'] == 2 ){
			$tax_display = true;
		}
		// currently not in use
		//if($wppizza_options['order_settings']['taxes_display'] == 3 ){
		//	$tax_display = null;
		//}
		//if($wppizza_options['order_settings']['taxes_display'] == 4 ){
		//	$tax_display = 'force';
		//}

	return $tax_display;
	}

}
/***************************************************************
*
*	[ini]
*
***************************************************************/
$WPPIZZA_FILTERS = new WPPIZZA_FILTERS();
?>