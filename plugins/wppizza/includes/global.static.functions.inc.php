<?php if ( ! defined( 'ABSPATH' ) ) exit;/*Exit if accessed directly*/ ?>
<?php
/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*
*
*
*	static helper functions
*
*
*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\*/

/*********************************************************
*	[check if debug is on and logging only ]
*********************************************************/
function wppizza_debug(){
	static $debug = null;
	if($debug === null){
		$debug=false;
		if(defined('WP_DEBUG') && defined('WP_DEBUG_LOG') && defined('WP_DEBUG_DISPLAY') && WP_DEBUG === true && WP_DEBUG_LOG === true && WP_DEBUG_DISPLAY === false){
			$debug=true;
		}
	}
return $debug;
}

/*********************************************************
*	[get wppizza version]
*********************************************************/
function wppizza_major_version(){
	static $version = null;
	if($version === null){

 		if ( version_compare( WPPIZZA_VERSION, '3', '>=' ) ) {
           	$version = 3;
           	return $version;
        }
 		/* future versions */
 		if ( version_compare( WPPIZZA_VERSION, '4', '>=' ) ) {
           	$version = 4;
           	return $version;
        }
 		/* future versions */
 		if ( version_compare( WPPIZZA_VERSION, '5', '>=' ) ) {
           	$version = 5;
           	return $version;
        }
	}
return $version;
}


/*********************************************************
*	[get active wppizza widgets ]
*********************************************************/
function wppizza_active_widgets(){
	static $active_wppizza_widgets = null;
	if($active_wppizza_widgets === null){

		/* get all wppizza widgets */
		$get_wppizza_widgets =  get_option('widget_wppizza_widgets');

		/* make array of (unique) type of wppizza widgets in use in sidebar */
		$active_wppizza_widgets = array();
		$all_active_sidebar_widgets = wp_get_sidebars_widgets();
		unset($all_active_sidebar_widgets['wp_inactive_widgets']);
		if(!empty($all_active_sidebar_widgets) && is_array($all_active_sidebar_widgets)){
			foreach($all_active_sidebar_widgets as $sbID=>$widgets){
				if(!empty($widgets)){
				foreach($widgets as $widget){
					$xWidget = explode('-',$widget);
					if($xWidget[0] == 'wppizza_widgets'){
						/** get type of widget **/
						$type = $get_wppizza_widgets[$xWidget[1]]['type'];
						/** add to array **/
						$active_wppizza_widgets[$type] = true ;
					}
				}}
			}
		}
	}
return $active_wppizza_widgets;
}

/*********************************************************
*	[orderpage widget on page ?]
*********************************************************/
function wppizza_has_orderpage_widget(){
	static $has_orderpage_widget = null;
	if($has_orderpage_widget === null){

		$has_orderpage_widget = false;

		$active_wppizza_widgets = wppizza_active_widgets();
		/* if there's an active orderpage widget */
		if(!empty($active_wppizza_widgets['orderpage'])){
			$has_orderpage_widget = true;
		}
	}

return $has_orderpage_widget;
}

/***********************************************************
	get registered and enabled gateway objects
	should be used/run later than init hook |  priority:9
	@param void
	@return obj
	@since 3.9
***********************************************************/
function wppizza_get_active_gateways(){
	static $registered_gateways = null;
	if($registered_gateways === null){
		$registered_gateways = WPPIZZA() -> gateways -> gwobjects;
		/* for the time being - loose some overkill data */
		if(!empty($registered_gateways)){
		foreach($registered_gateways as $k => $obj){
			unset($registered_gateways -> $k -> gateway_settings);
		}}
	}
return $registered_gateways;
}

/*********************************************************
	[check if we are on orderpage]
*********************************************************/
function wppizza_is_orderpage(){
	static $is_orderpage = null;

	if($is_orderpage === null){


		global $wppizza_options, $post;
		/*
			the set orderpage in admin
		*/
		$order_page = $wppizza_options['order_settings']['orderpage'];

		/*ini as false*/
		$is_orderpage = false;

		/**
			set flag that we are on order page to not do any redirection for example
			provided we have a post object and ID
		**/
		if(is_object($post) && $post->ID==$order_page){
			$is_orderpage = true;
		}

		/**
			if called before post object is available, get post_id from url
		**/
		if( ( !is_object($post) || empty($post->ID) ) && (!defined('DOING_AJAX') || !DOING_AJAX)){

			$REQUEST_SCHEME = is_ssl() ? 'https' : 'http';
			$current_url = $REQUEST_SCHEME . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ;
			$post_id = url_to_postid($current_url);
			if($post_id == $order_page){
				$is_orderpage = true;
			}
		}
		/**
			if called from ajax and distinctly set to be checkout or not posting
			$_POST['vars']['isCheckout'] without having a post object
		**/
		if( !is_object($post) && empty($post->ID) && defined('DOING_AJAX') && DOING_AJAX && isset($_POST['vars']['isCheckout'])){
			// js may return a true/false string
			if(filter_var($_POST['vars']['isCheckout'], FILTER_VALIDATE_BOOLEAN)){
				$is_orderpage = true;
			}
		}
	}

return $is_orderpage;
}
/* alias of wppizza_is_orderpage */
function wppizza_is_checkout(){
	return wppizza_is_orderpage();
}

/*********************************************************
	[check if we are on users order history page
	within a hook or elsehwere that has global $post availabe
	bypass by default if already logged in ]
*********************************************************/
function wppizza_is_orderhistory($check_for_login = true){
	global $post;
	static $is_orderhistory = null;

	if($is_orderhistory === null && is_object($post)){
		/* if we are logged in already, there's no login form */
		if($check_for_login){
			if(is_user_logged_in()){
				$is_orderhistory = false;
				return $is_orderhistory;
			}
		}

		/* check if it has ANY wppizza shortcode to start off with */
		if( has_shortcode( $post->post_content, 'wppizza' ) ) {
			$pattern = get_shortcode_regex();

			/* basic match */
			if(
				preg_match_all( '/'. $pattern .'/s', $post->post_content, $matches )
				&& array_key_exists( 2, $matches )
				&& in_array( 'wppizza', $matches[2] )
			){

				/** check if its a wppizza shortcode and type 'orderhistory' **/
				if(!empty($matches[2])){
					foreach($matches[2] as $k=>$val)
					if($val == 'wppizza' && strpos($matches[3][$k], 'orderhistory') !== false){
						$is_orderhistory =  true;
						return $is_orderhistory;
					}
				}
			}
		$is_orderhistory = false;
		return $is_orderhistory;
		}
		$is_orderhistory = false;
		return $is_orderhistory;
	}
	return $is_orderhistory;
}
/***********************************************************
	get all wordpress pages for current blog
***********************************************************/
function wppizza_get_wordpress_pages() {
	static $wordpress_pages = null;
	if($wordpress_pages === null){
		/*get all pages - possibly get these hierarchical to save some child queries for 'category_parent_page' **/
		$wordpress_pages=get_pages(array('post_type'=> 'page', 'echo'=>0, 'title_li'=>''));
	}
	return $wordpress_pages;
}

/***********************************************************
	get network sites/pages (current blog only if not in network setup)
***********************************************************/
function wppizza_get_networkpages(){
	static $network_pages = null;
	if($network_pages === null){

		$network_pages=array();

		/*multisite*/
		if(is_multisite()){
			$args=array();
			$allsites=wp_get_sites( $args );
			/*get all published pages*/
			foreach($allsites as $nws=>$blog){
				if($blog['public']==1){
					switch_to_blog($blog['blog_id']);

					$network_pages[$nws]['blog_id']=$blog['blog_id'];
					$network_pages[$nws]['site_id']=$blog['site_id'];
					$network_pages[$nws]['blogname']=get_bloginfo('name');
					$network_pages[$nws]['url']=site_url();
					/*get pages*/
					$pages=get_pages(array('post_type'=> 'page','echo'=>0,'title_li'=>''));
					foreach($pages as $a=>$b){
						$pageids[$b->ID]=array('title'=>$b->post_title);
					}
					$network_pages[$nws]['pageids']=$pageids;
					restore_current_blog();
				}
			}
		}

		/*single site*/
		if(!is_multisite()){
			global $blog_id;
			$network_pages[$blog_id]['blog_id']=$blog_id;
			$network_pages[$blog_id]['site_id']=1;
			$network_pages[$blog_id]['blogname']=get_bloginfo('name');
			$network_pages[$blog_id]['url']=site_url();
			/*get pages*/
			$pages=get_pages(array('post_type'=> 'page','echo'=>0,'title_li'=>''));
			foreach($pages as $a=>$b){
				$pageids[$b->ID]=array('title'=>$b->post_title);
			}
			$network_pages[$blog_id]['pageids']=$pageids;
		}
	}


	return $network_pages;
}

/***********************************************************
	get wppizza categories
***********************************************************/
function wppizza_get_categories() {
	static $wppizza_categories = null;
	if($wppizza_categories === null){
		$args = array('taxonomy' => ''.WPPIZZA_TAXONOMY.'');
		$wppizza_categories=get_categories($args);
	}
return $wppizza_categories;
}
/***********************************************************
	get wppizza menu items
***********************************************************/
function wppizza_get_menu_items() {
	static $wppizza_menu_items = null;
	if($wppizza_menu_items === null){
		$args = array('post_type' => ''.WPPIZZA_POST_TYPE.'','posts_per_page' => -1, 'orderby'=>'title' ,'order' => 'ASC');
		$query = new WP_Query( $args );
		$wppizza_menu_items=$query->posts;
	}

	/*wp_reset_query(); probably not needed*/

	return $wppizza_menu_items;
}
/***********************************************************
	get all wppizza additives
***********************************************************/
function wppizza_all_additives() {
	static $additives = null;
	if($additives === null){
		global $wppizza_options;
		$set_additives = $wppizza_options['additives'];

		$additives = array();
		if(is_array($set_additives)){
			asort($set_additives);

			/**add key as ident in case there's no sorting set yet*/
			foreach($set_additives as $key=>$value){
				$ident = empty($value['sort']) ? $key : $value['sort'] ;

				$additives[$key] = $value;

				$additives[$key]['ident'] = $ident;

				$additives[$key]['id'] = '' . WPPIZZA_PREFIX . '-additive-' . $key . '';

				/*set classes*/
				$additives_class[$key]['class'] = array();
				$additives_class[$key]['class'][] = '' . WPPIZZA_PREFIX . '-additive';
				$additives_class[$key]['class'][] = '' . WPPIZZA_PREFIX . '-additive-' . $key . '';

				/*implode for output*/
				$additives[$key]['class'] = implode(' ', $additives_class[$key]['class']);
			}
		}

	}
	return $additives;
}


/***********************************************************
	delivery set to pickup ?
	Note: this should NOT run statically as the change
	from pickup to delivery and vice versa may happen further
	down the chain of events.
	make sure to use the right filter hook in sequence if
	you rely on this being accurate
***********************************************************/
function wppizza_is_pickup(){
	$is_pickup = WPPIZZA() -> session -> is_pickup();
return $is_pickup;
}

/***********************************************************
	get full cart contents
	@since 3.2.7
***********************************************************/
function wppizza_get_cart($is_checkout = null, $recalculate = false){
	$cart_contents = WPPIZZA() -> session -> get_cart($is_checkout, $recalculate);
return $cart_contents;
}

/***********************************************************
	get cart summary only
	@since 3.2.7
***********************************************************/
function wppizza_cart_summary($is_checkout = null, $recalculate = false){
	$cart_summary = WPPIZZA() -> session -> get_cart_summary($is_checkout, $recalculate);
return $cart_summary;
}

/***********************************************************
	is cart empty ?
***********************************************************/
function wppizza_cart_is_empty(){
	static $cart_is_empty = null;

	if($cart_is_empty === null){
		$cart_is_empty = WPPIZZA() -> session -> cart_is_empty();
	}

return $cart_is_empty;
}
/***********************************************************
	are there products added to cart ?
	(inverted alias of wppizza_cart_is_empty really )
***********************************************************/
function wppizza_cart_has_items(){
	static $cart_has_items = null;

	if($cart_has_items === null){
		$cart_has_items = WPPIZZA() -> session -> cart_has_items();
	}

return $cart_has_items;
}

/***********************************************************
	admin shortcode on frontend page ?
	@since 3.5
***********************************************************/
function wppizza_has_admin_shortcode(){
	static $has_admin_shortcode = null;
	/* allow filtering */
	if($has_admin_shortcode === null){
		$has_admin_shortcode = apply_filters('wppizza_has_admin_shortcode', false);
	}

return $has_admin_shortcode;
}

/***********************************************************
	get user personal info session data
	@since 3.7
***********************************************************/
function wppizza_user_session_personal_info(){
	static $user_session = null;
	/* allow filtering */
	if($user_session === null){
		/* get session user data */
		$user_session = WPPIZZA()->session->get_userdata();
		/* unset superfluous data that would only confuse the issue here*/
    	if(isset($user_session['wppizza_hash'])){
    		unset($user_session['wppizza_hash']);
    	}
    	if(isset($user_session['wppizza_order_id'])){
    		unset($user_session['wppizza_order_id']);
    	}
	}
return $user_session;
}

/***********************************************************
	get user email from session data
	@since 3.7
***********************************************************/
function wppizza_user_session_email(){
	static $user_session_email = null;
	/* allow filtering */
	if($user_session_email === null){
		/* get session user data */
		$user_session = WPPIZZA()->session->get_userdata();
		/* set email or empty */
		$user_session_email = isset($user_session['cemail']) ? $user_session['cemail'] : '';
	}
return $user_session_email;
}

/***********************************************************
	is shop currently open ?
***********************************************************/
function wppizza_is_shop_open(){
	global $wppizza_options;
	static $shop_open = null;

	/* allow filtering */
	$shop_open = apply_filters('wppizza_shop_is_open', $shop_open);

	/* we have forcefully closed the shop overriding everything else */
	$shop_open = !empty($wppizza_options['openingtimes']['close_shop_now']) ? false : $shop_open;

	if($shop_open === null){


		$todayWday=date("w",WPPIZZA_WP_TIME);

		$d=date("d",WPPIZZA_WP_TIME);
		$m=date("m",WPPIZZA_WP_TIME);
		$Y=date("Y",WPPIZZA_WP_TIME);

		$standard = $wppizza_options['openingtimes']['opening_times_standard'];
		$custom = $wppizza_options['openingtimes']['opening_times_custom'];
		$breaks = $wppizza_options['openingtimes']['times_closed_standard'];


		/**make sunday 7 instead of 0 to aid sorting**/
		if($todayWday==0){$yesterdayWday=6;}else{$yesterdayWday=($todayWday-1);}
		/**get the opening times today, as well as the spillover from yesterday
		in case its very early in the morning and we dont close until after midnight on the previous day**/
		$todayTimes=$standard[$todayWday];
		$yesterdayTimes=$standard[$yesterdayWday];
		$todayStartTime	= mktime(0, 0, 0, $m , $d, $Y);
		$todayEndTime	= mktime(23, 59, 59, $m , $d, $Y);
		$todayDate	= ''.$Y.'-'.$m.'-'.$d.'';
		$yesterdayDate	= date("Y-m-d",mktime(12, 0, 0, $m , $d-1, $Y));

		/**now we first check if these dates have custom dates opening times****/
		if(count($custom)>0){
			$yesterdayCustom = array_search($yesterdayDate, wppizza_array_column($custom, 'date'));/* will return key of only the first one found */
			$todayCustom = array_search($todayDate, wppizza_array_column($custom, 'date'));/* will return key of only the first one found */

		}

		/*if we have found dates in custom dates array,make start and end and use these**/
		if(isset($yesterdayCustom) && $yesterdayCustom!==false){
			$t=wpizza_get_opening_times($custom[$yesterdayCustom]['open'],$custom[$yesterdayCustom]['close'],$d,$m,$Y,'yesterday');
			if($t){
				$openToday[]=array('start'=>$t['start'],'end'=>$t['end']);
			}
		}else{//use times from standard opening times
			$t=wpizza_get_opening_times($standard[$yesterdayWday]['open'],$standard[$yesterdayWday]['close'],$d,$m,$Y,'yesterday');
			if($t){
				$openToday[]=array('start'=>$t['start'],'end'=>$t['end']);
			}
		}
		if(isset($todayCustom) && $todayCustom!==false){
			$t=wpizza_get_opening_times($custom[$todayCustom]['open'],$custom[$todayCustom]['close'],$d,$m,$Y,'today');
				$openToday[]=array('start'=>$t['start'],'end'=>$t['end']);
		}else{//use times from standard opening times
			$t=wpizza_get_opening_times($standard[$todayWday]['open'],$standard[$todayWday]['close'],$d,$m,$Y,'today');
			if($t){
				$openToday[]=array('start'=>$t['start'],'end'=>$t['end']);
			}
		}

		/*********
			check if we have added some breaks/siestas whatever you want to call it
		**********/
		if(count($breaks)>0){
			/**first check if today is a custom day and if we've set break times for it**/
			if( isset($todayCustom) && $todayCustom!==false ){
				foreach($breaks as $k=>$v){
					if($v['day']=='-1'){
						$t=wpizza_get_opening_times($v['close_start'],$v['close_end'],$d,$m,$Y,'today');
						if($t['start']<=WPPIZZA_WP_TIME && $t['end']>=WPPIZZA_WP_TIME){
							$shop_open = false;
							break;
						}
					}
				}
			}else{
				/**its not a custom day, so check if we havea break set for this weekday**/
				foreach($breaks as $k=>$v){
					if($todayWday==$v['day']){
						$t=wpizza_get_opening_times($v['close_start'],$v['close_end'],$d,$m,$Y,'today');
						if($t['start']<=WPPIZZA_WP_TIME && $t['end']>=WPPIZZA_WP_TIME){
							$shop_open = false;
							break;
						}
					}
				}

			}
		}
		/********
			we've done the siesta/break check, now check if current time is in the $openToday array between start and end
		********/
		if($shop_open === null){
			if(!empty($openToday)){
			foreach($openToday as $k=>$times){
				if( WPPIZZA_WP_TIME >= $times['start'] && WPPIZZA_WP_TIME <= $times['end']){
					$shop_open = true;
					break;
				}
			}}else{
				$shop_open = false;
			}
		}
	}

return $shop_open;
}

/****************************************************************************
	check if a timestamp is between todays todays opening and closing time
	(business days could cross midnight)
	php >=5.3

	@$timestamp (int)
	@return bool
****************************************************************************/
function wppizza_is_current_businessday($timestamp, $timestampcurrent = false){
	global $wppizza_options;
	/*php 3,3+ needed for DateTime function*/
	if( version_compare( PHP_VERSION, '5.3', '<' )) {return true;}
	/**ini as true*/
	$isCurrentBusinessday=true;
	/*no timetamp set, set current - default but changeable if needed for some reason*/
	if(!$timestampcurrent){
		$timestampcurrent=current_time('timestamp');
	}
	/*get options*/
	$standard = $wppizza_options['openingtimes']['opening_times_standard'];
	$custom = $wppizza_options['openingtimes']['opening_times_custom'];

	/*get standard opening/closing times of current day*/
	foreach($standard as $k=>$stdTime){
		$open = DateTime::createFromFormat('H:i', $stdTime['open'])->getTimestamp();
		$close = DateTime::createFromFormat('H:i', $stdTime['close'])->getTimestamp();
		/*closed<open=>add a day*/
		if($close<$open){
			$close = strtotime('+1 day', $close);
		}
		if($timestampcurrent<=$close && $timestampcurrent>=$open){
			$currentbusinessday=array('open'=>$open,'close'=>$close);
			break;
		}
	}
	/*get opening/closing times of current day if set*/
	if(!empty($custom)){
	foreach($custom as $k=>$cstDate){
		$open = DateTime::createFromFormat('Y-m-d H:i', ''.$cstDate['date'].' '.$cstDate['open'].'')->getTimestamp();
		$close = DateTime::createFromFormat('Y-m-d H:i', ''.$cstDate['date'].' '.$cstDate['close'].'')->getTimestamp();
		/*closed<open=>add a day*/
		if($close<$open){
			$close = strtotime('+1 day', $close);
		}
		if($timestampcurrent<=$close && $timestampcurrent>=$open){
			$currentbusinessday=array('open'=>$open,'close'=>$close);
			break;
		}
	}}


	if(empty($currentbusinessday) || $timestamp<$currentbusinessday['open'] || $timestamp>$currentbusinessday['close']){
		$isCurrentBusinessday=false;
	}

return $isCurrentBusinessday;
}
/****************************************************************************
	get all completed business days (i.e days where closing time is before now)
	within the last week ignoring closing times in between
	(business days could cross midnight)
	php >=5.3

	@$timestamp (int)
	@return array()
****************************************************************************/
function wppizza_completed_businessdays($current_timestamp){
	static $completed_businessdays = null;

	/* only run once */
	if($completed_businessdays === null){
		global $wppizza_options;

		/*php 3,3+ needed for DateTime function*/
		if( version_compare( PHP_VERSION, '5.3', '<' )) {return false;}

		/**ini return array*/
		$completed_businessdays = array();


		/*get opening times set options*/
		$standard = $wppizza_options['openingtimes']['opening_times_standard'];
		$custom = $wppizza_options['openingtimes']['opening_times_custom'];

		/* now week day */
		$todayWday=date("w",WPPIZZA_WP_TIME);

		/*
			loop through standard times (i reverse form today) and check if current time is already past
			closing time of this day. if so capture start/end time for this as last completed
			standard business day
		*/
		$day_key = 0;

		/*
			we start with the current day
			and *go back in time* / *in reverse* for a week
		*/
		for($i=$todayWday; $i<($todayWday+7) ; $i++){

			/*
				get the right weekday key going backwards in time for a week from now
			*/
			$setWeekDayKey = ($i - ($day_key*2));
			$weekDayKey = ($setWeekDayKey < 0) ? ($setWeekDayKey +7) : $setWeekDayKey;

			/* set open time for day */
			$open = DateTime::createFromFormat('H:i', $standard[$weekDayKey]['open'])->getTimestamp();
			$open = ($day_key>0) ? strtotime('-'.$day_key.' day', $open) : $open;

			/* set closing time for day */
			$close = DateTime::createFromFormat('H:i', $standard[$weekDayKey]['close'])->getTimestamp();
			$close = ($day_key>0) ? strtotime('-'.$day_key.' day', $close) : $close;
			$close = ($close<$open) ? strtotime('+1 day', $close) : $close;

			/* get date of this day taken from opening time */
			$ymd = date('Y-m-d', $open);
			$mdy_label = date('D, M-d-Y', $open);

			/* check if there are some custom dates set */
			if(count($custom)>0){
				/*
					as there are custom dates, check for this date in those custom times
					if this date is set in the custom days, use open/close from that one
				*/
				$custom_date_key = array_search($ymd, wppizza_array_column($custom, 'date'));/* will return key of only the first one found */
				if($custom_date_key !== false){
					$open = DateTime::createFromFormat('Y-m-d H:i', ''.$custom[$custom_date_key]['date'].' '.$custom[$custom_date_key]['open'].'')->getTimestamp();
					$close = DateTime::createFromFormat('Y-m-d H:i', ''.$custom[$custom_date_key]['date'].' '.$custom[$custom_date_key]['close'].'')->getTimestamp();
					$close = ($close<$open) ? strtotime('+1 day', $close) : $close;
				}
			}


			/*
				skip days that are entirely closed
			*/
			if($open != $close){
				/*
					if current time is after this days closing time
					capture this date as a completed business day
				*/
				if($close < $current_timestamp ){
					$completed_businessdays[$day_key] = array('date'=>$ymd, 'lbl'=> ''.$mdy_label.': '.date('H:i', $open).'-'.date('H:i', $close).'' , 'open'=>$open, 'close'=>$close, 'open_formatted'=> date('Y-m-d H:i:s', $open), 'close_formatted'=>date('Y-m-d H:i:s', $close));
				}
			}
			$day_key++;
		}
	}
return 	$completed_businessdays;
}
?>