<?php if ( ! defined( 'ABSPATH' ) ) exit;/*Exit if accessed directly*/ ?>
<?php
/*********************************************
	set order table
**********************************************/
$table = $table_prefix . WPPIZZA_TABLE_ORDERS;
$orders_results = $wpdb->get_results("SELECT id, order_ini from ".$table." WHERE order_ini !='' ");
/*********************************************
	loop and update/normalize
**********************************************/
if(is_array($orders_results)){
	foreach($orders_results as $result){

		/* get id */
		$order_id=$result->id;

		/* unserialize order_ini */
		$order_details = maybe_unserialize($result->order_ini);

		/**************************
			normalize some values into distinct columns/fields
		**************************/
		$normalize=array();
		$normalize['order_no_of_items'] 	= (!empty($order_details['item']) && is_array($order_details['item'])) ? count($order_details['item']): 0 ;
		$normalize['order_items_total'] 	= !empty($order_details['total_price_items']) ? $order_details['total_price_items'] : 0 ;
		$normalize['order_discount'] 		= !empty($order_details['discount']) ? $order_details['discount'] : 0 ;
		$normalize['order_taxes'] 			= !empty($order_details['item_tax']) ? $order_details['item_tax'] : 0 ;
		$normalize['order_taxes_included'] 	= !empty($order_details['taxes_included']) ? 'Y' : 'N' ;
		$normalize['order_delivery_charges']= !empty($order_details['delivery_charges']) ? $order_details['delivery_charges'] : 0 ;
		$normalize['order_handling_charges']= !empty($order_details['handling_charge']) ? $order_details['handling_charge'] : 0 ;
		$normalize['order_tips'] 			= !empty($order_details['tips']) ? $order_details['tips'] : 0 ;
		$normalize['order_self_pickup'] 	= !empty($order_details['selfPickup']) ? 'Y' : 'N' ;
		$normalize['order_total'] 			= !empty($order_details['total']) ? $order_details['total'] : 0 ;

		/*****************************************************************************
			update order items keys
			blog_id
			postId to post_id or if that doesnt even exist for very old orders, use key[0] (exploded by ".") as post_id etc etc
			name -> title
			count -> quantity
			catIdSelected -> cat_id_selected
			additionalinfo|additionalInfo -> additional_info ... in the future this can probably be got rid of entirely and we just use extend and extend data to build output dynamically as required
			selfPickup -> self_pickup
			item -> items (or vice versa ?)
			size -> price_label ?
			etc
		*****************************************************************************/
		$order_ini 	= $order_details ;/* original */
		$multiple_taxrates = false;
		/* just io get some vaguley correct tax per item as old db entries do not have that info on a per item basis*/
		$tpi = $normalize['order_taxes'] / $normalize['order_no_of_items'];
		
		/* overwrite item(s) as needed - also converting item into items */
		if(!empty($order_ini['item'])){
			$order_ini['items'] = array();
			foreach($order_ini['item'] as $item_id => $item_param){
				$order_ini['items'][$item_id] = $item_param;
				$update_order_item = array();
				/**
					add/update blog_id if not set
				**/
				if(!isset($item_param['blog_id'])){
					/*pre 3.x used blogId as key*/
					if(!empty($item_param['blogId'])){
						$update_order_item['blog_id'] = $item_param['blogId'];
						/*unset now obsolete key */
						unset($order_ini['items'][$item_id]['blogId']);
					}
					/*very old orders (probably pre 2.x) had no blog_id at all so let's just use global */
					if(empty($item_param['blogId'])){
						$update_order_item['blog_id'] = $blog_id;
					}

				}

				// * todo add blog name and add to session in sessions, get_category_for_cart_email_order etc *//
				/*
					add/update selected category as cat_id_selected
				*/
				if(!isset($item_param['cat_id_selected'])){
					$update_order_item['cat_id_selected'] = !empty($item_param['catIdSelected']) ? $item_param['catIdSelected'] : -1;
					/*unset now obsolete key */
					unset($order_ini['items'][$item_id]['catIdSelected']);
				}
				/**
					add/update post_id if not set
				**/
				if(!isset($item_param['post_id'])){
					/*pre 3.x used postId as key*/
					if(!empty($item_param['postId'])){
						$update_order_item['post_id'] = $item_param['postId'];
						/*unset now obsolete key */
						unset($order_ini['items'][$item_id]['postId']);
					}
					/*very old orders (probably pre 2.x) had no post_id at all so let's explode the key*/
					if(empty($item_param['postId'])){
						$xKey = explode('.',$item_id);
						$update_order_item['post_id'] = $xKey[0];
					}
				}
				/**
					add/update name to title
				**/
				if(!isset($item_param['title']) && isset($item_param['name']) ){
					$update_order_item['title'] = $item_param['name'];
					/*unset now obsolete key */
					unset($order_ini['items'][$item_id]['name']);
				}
				/**
					add/update quantity
				**/
				if(!isset($item_param['quantity']) && isset($item_param['count']) ){
					$update_order_item['quantity'] = $item_param['count'];
				}else{/* just for tidyness , re-add in this place*/
					unset($order_ini['items'][$item_id]['quantity']);
					$update_order_item['quantity'] = $item_param['quantity'];
				}
				/*always unset now obsolete count key */
				unset($order_ini['items'][$item_id]['count']);
				/**
					add/update size to price_label
				**/
				if(!isset($item_param['price_label']) && isset($item_param['size']) ){
					$update_order_item['price_label'] = $item_param['size'];
					/*unset now obsolete key */
					unset($order_ini['items'][$item_id]['size']);
				}
				/**
					add/update selected size from key (not sizes id as we do not have them pre v3.x)
				**/
				if(!isset($item_param['size'])){
					$xKey = explode('.',$item_id);
					$update_order_item['size'] = (isset($xKey[1]) && is_numeric(''.$xKey[1].'')) ? $xKey[1]: '';
				}
				/*
					add/update all categories, changing index keys to useing the term ids
				*/
				if(!isset($item_param['item_in_categories'])){
					$update_order_item['item_in_categories'] = !empty($item_param['categories']) ? $item_param['categories'] : array();
					$reset_cats = array();
					foreach($update_order_item['item_in_categories'] as $catKey => $cat){
						$reset_cats[$cat['term_id']] = $cat;
					}
					$update_order_item['item_in_categories'] = $reset_cats ;
					/*unset now obsolete key */
					unset($order_ini['items'][$item_id]['categories']);
				}
				/*
					update extenddata to extend_data and remove extenddata,extend keys
				*/
				$update_order_item['extend_data'] = array();
				if(isset($item_param['extenddata'])){
					$update_order_item['extend_data'] = $item_param['extenddata'];
				}
				/*unset now obsolete keys */
				unset($order_ini['items'][$item_id]['extenddata']);
				unset($order_ini['items'][$item_id]['extend']);


				/*
					i do not think we need actually need additional_info|additionalinfo|additional_Info  anymore.
					but for the time being, lets keep it , even if it's empty
				*/
				$additional_info	=	isset($item_param['additional_info']) ? $item_param['additional_info'] : '';
				$additional_info	=	(empty($additional_info) && isset($item_param['additionalinfo'])) ? $item_param['additionalinfo'] :  '' ;/*old orders pre wppizza v3.0 might use additionalinfo or even additionalInfo instead of additional_info **/
				$additional_info	=	(empty($additional_info) && isset($item_param['additionalInfo'])) ? $item_param['additionalInfo'] :  '' ;
				$update_order_item['additional_info'] = $additional_info;

				unset($order_ini['items'][$item_id]['additional_info']);
				unset($order_ini['items'][$item_id]['additionalinfo']);
				unset($order_ini['items'][$item_id]['additionalInfo']);


				/* 
					prepend changed keys/values for a bit of consistency
				*/
				$order_ini['items'][$item_id] = $update_order_item + $order_ini['items'][$item_id];
				
				/*
					 add missing 
				*/
				$post_meta = get_post_meta($update_order_item['post_id'], WPPIZZA_SLUG);
				
				/* were we using multiple taxrates - for use in summary ? */
				if(!empty($post_meta[0]['item_tax_alt'])){
					$multiple_taxrates = true ;
				}
								
				$order_ini['items'][$item_id]['tax_rate'] = !empty($order_details['taxrate']) ? $order_details['taxrate'] : 0 ;
				
				/* $tpi is just an average that is not really correct per item, but old data does not have this info per item */
				$order_ini['items'][$item_id]['tax_included'] = empty($order_details['taxes_included']) ? $tpi : 0 ;
				$order_ini['items'][$item_id]['tax_to_add']  =  empty($order_details['taxes_included']) ? 0 : $tpi ;
				
				$order_ini['items'][$item_id]['use_alt_tax'] = !empty($post_meta[0]['item_tax_alt']) ? true : false ;
				/* set sizes id's */
				$order_ini['items'][$item_id]['sizes'] = $post_meta[0]['sizes'] ;
				/* try and find the size id */
				if(!empty($post_meta[0]['prices'])){
				foreach($post_meta[0]['prices'] as $pKey=>$pVal){
					if($pVal == $item_param['price']){
						$order_ini['items'][$item_id]['size'] = $pKey ;		
					}
				}}				

			
			}
			
			/* 
				append summary , info and param for consistency with new way of storing things (as they are used in purchase history, order print, etc ) 
			*/
			/* info */
			$order_ini['info'] = array();
			$order_ini['info']['plugin_version'] = '2.99';/* just to say it's pre 3.x */
			$order_ini['info']['session_id'] = '';/* unknown */
			$order_ini['info']['unique_id'] = $order_ini['time'];/* formerly time field */

			
			/* param */
			$order_ini['param'] = array();
			$order_ini['param']['currency'] = $order_ini['currency']; //€
			$order_ini['param']['currencyiso'] = $order_ini['currencyiso']; //EUR
			$order_ini['param']['decimals'] = empty($wppizza_blog_options['prices_format']['hide_decimals']) ? 2 : 0 ; //2
			$order_ini['param']['currency_position'] = $wppizza_blog_options['prices_format']['currency_symbol_position'] ; //left
			$order_ini['param']['tax_included'] = !empty($order_ini['taxes_included']) ? true : false ; //1
			$order_ini['param']['taxrate'] = $order_ini['taxrate']; //7
			$order_ini['param']['taxrate_alt'] = $wppizza_blog_options['order_settings']['item_tax_alt']; //0
			$order_ini['param']['taxrate_shipping'] = !empty($wppizza_blog_options['order_settings']['shipping_tax']) ? $wppizza_blog_options['order_settings']['shipping_tax_rate'] : 0 ; //7
			$order_ini['param']['min_order_delivery'] = $wppizza_blog_options['order_settings']['order_min_for_delivery']; //0.1
			$order_ini['param']['min_order_pickup'] = $wppizza_blog_options['order_settings']['order_min_for_pickup']; //0.02
			$order_ini['param']['delivery_exclude_items'] = $wppizza_blog_options['order_settings']['delivery_calculation_exclude_item'];//
			$order_ini['param']['delivery_exclude_cats'] = $wppizza_blog_options['order_settings']['delivery_calculation_exclude_cat']; //
			$order_ini['param']['discount_type'] = $wppizza_blog_options['order_settings']['discount_selected']; //none
			$order_ini['param']['discounts'] = $wppizza_blog_options['order_settings']['discounts']; //
			$order_ini['param']['self_pickup_discount'] = $wppizza_blog_options['order_settings']['order_pickup_discount'];// order_pickup_discount
			$order_ini['param']['discount_exclude_items'] =  $wppizza_blog_options['order_settings']['discount_calculation_exclude_item'];// discount_calculation_exclude_item
			$order_ini['param']['discount_exclude_cats'] =  $wppizza_blog_options['order_settings']['discount_calculation_exclude_cat'];// 	discount_calculation_exclude_cat		
									
			/* summary */
			$order_ini['summary'] = array();
            $order_ini['summary']['blog_id'] = $update_order_item['blog_id']; //1
            $order_ini['summary']['self_pickup'] = $order_ini['selfPickup'];// 
            $order_ini['summary']['number_of_items'] = $normalize['order_no_of_items'] ; //2
            $order_ini['summary']['total_price_items'] = $normalize['order_items_total']; //9.9
            $order_ini['summary']['discount'] = $normalize['order_discount'] ; //0
            $order_ini['summary']['delivery_charges'] = $normalize['order_delivery_charges']; //60.1
            $order_ini['summary']['handling_charges'] = $normalize['order_handling_charges']; //0
            //$order_ini['summary']['taxrate_average'] = ''; //7
            $order_ini['summary']['tax_by_rate'] = array();//Array
            
            $order_ini['summary']['tax_by_rate']['main'] = array();
            $order_ini['summary']['tax_by_rate']['main']['rate'] = $order_ini['taxrate'];
	        $order_ini['summary']['tax_by_rate']['main']['total'] = $normalize['order_taxes'] ; 

            $order_ini['summary']['tax_by_rate']['alt'] = array();
            $order_ini['summary']['tax_by_rate']['alt']['rate'] = $wppizza_blog_options['order_settings']['item_tax_alt'];//7;
            //$order_ini['summary']['tax_by_rate']['alt']['total'] = $order_ini['taxrate'];//7;

            
            $order_ini['summary']['tax_by_rate']['shipping'] = array();
            $order_ini['summary']['tax_by_rate']['shipping']['rate'] = !empty($wppizza_blog_options['order_settings']['shipping_tax']) ? $wppizza_blog_options['order_settings']['shipping_tax_rate'] : 0;//7;
	        //$order_ini['summary']['tax_by_rate']['shipping']['total'] = ''; //0.65              

            $order_ini['summary']['taxes'] = $normalize['order_taxes'] ; //4.59
            $order_ini['summary']['multiple_taxrates'] = $multiple_taxrates;// 
            $order_ini['summary']['tips'] = $normalize['order_tips']; //
            $order_ini['summary']['total'] = $normalize['order_total']; //70
			
			/*
				unset old "item" key
			*/
			unset($order_ini['item']);
		}


		/***************************
			replace old order ini with new values
		***************************/
		$normalize['order_ini'] = maybe_serialize($order_ini);

		/****************************
			set normalize values type
		****************************/
		$normalize_value_type=array('%d', '%f', '%f', '%f', '%s', '%f', '%f', '%f', '%s', '%f', '%s');

		/***************************
			update row
		***************************/
		$wpdb->update( $table, $normalize, array( 'id' => $order_id ), $normalize_value_type, array( '%d' ) );

	}
}
?>