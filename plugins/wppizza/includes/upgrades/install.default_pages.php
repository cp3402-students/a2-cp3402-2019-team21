<?php if ( ! defined( 'ABSPATH' ) ) exit;/*Exit if accessed directly*/ ?>
<?php

		/*************************************
			[main required pages]
		/*************************************/
		$wppizza_required_pages = array();
		$wppizza_required_pages[0] = array('title'=>__('Our Menu', 'wppizza-admin'),'shortcode'=>'['.WPPIZZA_SLUG.' noheader="1"]');
		$wppizza_required_pages[1] = array('title'=>__('Orders', 'wppizza-admin'),'shortcode'=>'['.WPPIZZA_SLUG.' type="orderpage"]');
		if(!WPPIZZA_INSTALL_REQUIRED_ONLY){/* skip if set to install required ony */
			$wppizza_required_pages[2] = array('title'=>__('Purchase History', 'wppizza-admin'),'shortcode'=>'['.WPPIZZA_SLUG.' type="orderhistory"]');
		}
		/**********************************************
		*
		*	[insert main category pages and order page
		*	and get their corresponding ids to use in install options]
		*
		**********************************************/
		$default_pages = array();
		foreach($wppizza_required_pages as $page_key=>$page){
			$item = array(
			  'post_title'    	=> wp_strip_all_tags( $page['title']),
			  'post_content'  	=> $page['shortcode'],
			  'post_name'  		=> sanitize_title_with_dashes($page['title']),
			  'post_type'     	=> 'page',
			  'post_status'   	=> 'publish',
			  'menu_order'	  	=> 0,
			  'post_parent'	  	=> 0,
			  'comment_status'	=> 'closed',
			  'ping_status'		=> 'closed'
			);
			if($page_key==0){
				/*topmost parent category page **/
				$default_pages['pages_parent_id'] = wp_insert_post($item);
			}
			if($page_key==1){
				/** order page **/
				$default_pages['orderpage_id'] = wp_insert_post($item);
			}
			if($page_key==2){
				/** orderhistory page | variable not required/stored anywhere though**/
				$default_pages['orderhistory_page_id']=wp_insert_post($item);
			}
		}
?>