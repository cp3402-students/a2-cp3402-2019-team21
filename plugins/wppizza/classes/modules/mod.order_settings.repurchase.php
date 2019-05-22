<?php
/**
* WPPIZZA_MODULE_ORDERSETTINGS_REPURCHASE Class
*
* @package     WPPIZZA
* @subpackage  WPPIZZA_MODULE_ORDERSETTINGS_REPURCHASE
* @copyright   Copyright (c) 2015, Oliver Bach
* @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
* @since       3.0
*
*/
if ( ! defined( 'ABSPATH' ) ) exit;/*Exit if accessed directly*/


/************************************************************************************************************************
*
*
*
*
*
*
************************************************************************************************************************/
class WPPIZZA_MODULE_ORDERSETTINGS_REPURCHASE{

	private $settings_page = 'order_settings';/* which admin subpage (identified there by this->class_key) are we adding this to */

	private $localization_page = 'localization';/* which admin subpage (identified there by this->class_key) are we adding this to */
	private $localization_position = 'user_purchase_history';/* at which admin position (section) should this appear on above admin subpage, integer(zero indexed - for numeric position) or key ('layout-style' for example) after which it should appear*/

	private $section_key = 'repurchase';/* must be unique */


	function __construct() {
		/**********************************************************
			[add settings to admin]
		***********************************************************/
		if(is_admin()){
			/* add admin options settings page*/
			add_filter('wppizza_filter_settings_sections_'.$this->settings_page.'', array($this, 'admin_options_settings'), 70, 5);
			/* add admin options settings page fields */
			add_action('wppizza_admin_settings_section_fields_'.$this->settings_page.'', array($this, 'admin_options_fields_settings'), 10, 5);
			/**add default options **/
			add_filter('wppizza_filter_setup_default_options', array( $this, 'options_default'));
			/**validate options**/
			add_filter('wppizza_filter_options_validate', array( $this, 'options_validate'), 10, 2 );
			
			/* add admin options localization page - does not need fields action*/
			add_filter('wppizza_filter_settings_sections_'.$this->localization_page.'', array($this, 'admin_options_localization'), 100, 5);/* highish priority to be able to splice in the right place*/			
		}
		/**********************************************************
			[filter/actions depending on settings]
		***********************************************************/
		/** add repurchase buttons (and header) in user purchase history **/
		add_filter('wppizza_filter_order_item_header_markup',array($this, 'orderhistory_reorder_purchase_item_header'), 10, 3);
		//add_filter('wppizza_filter_order_item_markup',array($this, 'orderhistory_reorder_purchase_item'), 10, 9);
		add_filter('wppizza_filter_order_item_columns',array($this, 'orderhistory_reorder_purchase_item'), 10, 8);
	}

	/*******************************************************************************************************************************************************
	*
	*
	*
	* 	[frontend filters]
	*
	*
	*
	********************************************************************************************************************************************************/
	/***************************************

		[check if we can reorder an item from user purchase history
		will return false if reordering cannot take place due to items/ids/etc not available
		else an array of parameters per item, if all match also allow reorder of whole order]

	***************************************/
	function orderhistory_reorder_purchase_item_header($markup_header, $txt, $type){
		global $wppizza_options;
		/** only apply filter in user's order history **/
		if( $type != 'orderhistory' || empty($wppizza_options[$this->settings_page]['repurchase'])){
			return $markup_header;
		}
		
		/* add repurchase label/legend to end*/
		$markup_header['thead_th_reorder']= '<th class="'.WPPIZZA_SLUG.'-item-reorder-th">'.$txt['history_label_reorder_header'].'</th>';/* table cell */
		
		return $markup_header;
	}


	function orderhistory_reorder_purchase_item($markup, $key , $item, $items, $item_count, $order_id, $txt, $type){
		global $wppizza_options, $blog_id;

		/** only apply filter in user's order history and if enabled**/
		if($type != 'orderhistory'  || empty($wppizza_options[$this->settings_page]['repurchase']) ){return $markup;}
						
		/* to count items we can repurchase */
		static $can_repurchase = array();
		
		/* get number of unique items in cart. if we can reorder all individually, add button to enable purchase of the whole order */
		$unique_items_in_cart = count($items);		
		
		/*
			remove filter first of all 
			resetting counts for each order
		*/
		if(empty($can_repurchase[$order_id])){			
			$can_repurchase[$order_id]['item'] = 0;
			$can_repurchase[$order_id]['order'] = 0;
			remove_filter( 'wppizza_filter_order_itemised_markup', array($this, 'orderhistory_reorder_purchase_order') );	
		}


		/***************************************
			check if item still exists as it was 
			to allow reordering
		***************************************/
		/** same blog as current ! */
		if($item['blog_id'] == $blog_id || !is_multisite()){
			/* get object */
			$post = get_post( $item['post_id']);
			$terms = get_the_terms($item['post_id'], WPPIZZA_TAXONOMY);	
			$termids_of_item = wppizza_array_column($terms, 'term_id', true);
			
//print"------------todo perhaps -- get sizes and size key-------------";

			/* check if (still) published , compare title to make sure it's not something completely different now */
			if ( $post->post_status == 'publish' && wppizza_compare_title($item['title']) == wppizza_compare_title($post->post_title) && isset($termids_of_item[$item['cat_id_selected']])) {
				/* we can reorder this item - filterable */
				$repurchase_item = apply_filters('wppizza_filter_can_repurchase_item', true, $key, $item, $items );
				$repurchase_order = apply_filters('wppizza_filter_can_repurchase_order', true, $key, $item, $items );
				/* increase counter for items enabled fr re-order */
				if($repurchase_item){
					$can_repurchase[$order_id]['item']++;
				}
				/* increase counter for items enabled fr re-order */
				if($repurchase_order){
					$can_repurchase[$order_id]['order']++;
				}				
			}
		}



		/** different blog then current - only if multisite in the first place ?! */
		if($item['blog_id'] != $blog_id && is_multisite()){		
			switch_to_blog($item['blog_id']);
			/* get object */
			$post = get_post( $item['post_id']);
			$terms = get_the_terms($item['post_id'], WPPIZZA_TAXONOMY);	
			$termids_of_item = wppizza_array_column($terms, 'term_id', true);
			
//print"------------todo perhaps -- get sizes and size key-------------";
						
			/* check if (still) published , compare title to make sure it's not something completely different now */
			if ( $post->post_status == 'publish' && wppizza_compare_title($item['title']) == wppizza_compare_title($post->post_title) && isset($termids_of_item[$item['cat_id_selected']])) {
				/* we can reorder this item - filterable */
				$repurchase_item = apply_filters('wppizza_filter_can_repurchase_item', true, $key, $item, $items );			
				$repurchase_order = apply_filters('wppizza_filter_can_repurchase_order', true, $key, $item, $items );
				/* increase counter for items enabled fr re-order */
				if($repurchase_item){
					$can_repurchase[$order_id]['item']++;
				}
				/* increase counter for items enabled fr re-order */
				if($repurchase_order){
					$can_repurchase[$order_id]['order']++;
				}					
			}
			/**restore current**/
			restore_current_blog();			
		}

		
		
		
		
		/* 
			add repurchase button for entire order 
			if all items can be reordered
		*/
		if($can_repurchase[$order_id]['order'] == $unique_items_in_cart){
			add_filter('wppizza_filter_order_itemised_markup', array($this, 'orderhistory_reorder_purchase_order'), 10, 7);
		}

		/* 
			cannot reorder, insert n/a  
		*/
		if(empty($can_repurchase[$order_id]['item'])){
			$markup['reorder'] = '<td class="'.WPPIZZA_SLUG.'-item-reorder '.WPPIZZA_SLUG.'-item-noreorder">'.$txt['history_reorder_not_available'].'</td>';
		}else{
			
			/** 
				repurchase button id - allow filtering - 
				to check do we actually need an id here ? this can be taken from closest tr after all (thoughg that has "." as splits) == to do !?
			*/
			$id = ''.WPPIZZA_SLUG.'-'.$item['blog_id'].'-'.$item['cat_id_selected'].'-'.$item['post_id'].'-'.$item['sizes'].'-'.$item['size'].'';
			$class = apply_filters('wppizza_filter_reorder_item_button_id', $id, $key , $item, $items, $item_count, $order_id, $txt, $type );
			/** repurchase button class - allow filtering */
			$class="".WPPIZZA_SLUG."-add-to-cart ".WPPIZZA_SLUG."-do-reorder";
			$class = apply_filters('wppizza_filter_reorder_item_button_class', $class, $key , $item, $items, $item_count, $order_id, $txt, $type );
			
			/** add repurchase button for item - allow filtering */
			$markup['reorder'] = '<td class="'.WPPIZZA_SLUG.'-item-reorder"><input id="'.$id.'" class="'.$class.'" type="button" value="'.$txt['history_label_item_reorder_button'].'" title="'.$txt['history_title_item_reorder_button'].'" /></td>';
			$markup['reorder'] = apply_filters('wppizza_filter_reorder_item_button', $markup['reorder'], $key , $item, $items, $item_count, $order_id, $txt, $type );
		}
		
		/* 	reset counter for every order after last item */
		//if($unique_items_in_cart == ($item_count+1)){
		//	$repurchase_count = 0; 
		//}	

	return $markup;	
	}
	
	
	
	/* button to re purchase whole order, skip for cart */
	function orderhistory_reorder_purchase_order($markup, $blog_id, $order_id, $cart, $txt, $colspan, $type){
		/*
			don't add this to current cart though
			as that would be silly
		*/
		if($type == 'cart'){
			return $markup;
		}
		/*
			add the repurchase button
		*/
		$reorder_entire_purchase_markup = '<div class="'.WPPIZZA_SLUG.'-reorder"><input id="'.WPPIZZA_SLUG.'-reorder-'.$blog_id.'-'.$order_id.'" class="'.WPPIZZA_SLUG.'-reorder-purchase" type="button" title="'.$txt['history_title_reorder_purchase_button'].'"  value="'.$txt['history_label_reorder_purchase_button'].'" /></div>';
		/** add button after itemised order */
		array_splice($markup, (count($markup)), 0, $reorder_entire_purchase_markup);
	
	return $markup;
	}

	/*******************************************************************************************************************************************************
	*
	*
	*
	* 	[add admin page options]
	*
	*
	*
	********************************************************************************************************************************************************/

	/*------------------------------------------------------------------------------
	#
	#
	#	[settings page]
	#
	#
	------------------------------------------------------------------------------*/

	/*------------------------------------------------------------------------------
	#	[settings section - setting page]
	#	@since 3.0
	#	@return array()
	------------------------------------------------------------------------------*/
	function admin_options_settings($settings, $sections, $fields, $inputs, $help){

		/*section*/
		if($sections){
			$settings['sections'][$this->section_key] = __('Repurchase', 'wppizza-admin');
		}
		/*help*/
		if($help){
			$settings['help'][$this->section_key][] = array(
				'label'=>__('Repurchase', 'wppizza-admin'),
				'description'=>array(
					__('Please adjust settings as appropriate according to the information provided next to each individual option.', 'wppizza-admin')
				)
			);
		}
		/*fields*/
		if($fields){
			$field = 'repurchase';
			$settings['fields'][$this->section_key][$field] = array( __('Order Repurchase', 'wppizza-admin'), array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('allow items to be repurchased from users order purchase page. ', 'wppizza-admin'). ' '.__('if it deos not exists already create a page with the shortcode as described <a href="http://docs.wp-pizza.com/shortcodes/?section=user-orderhistory">here</a>.', 'wppizza-admin') .' '.sprintf(__('furthermore ensure this page also has a %s cart on it (widget/shortcode) as without it there is nothing to put the re-order into.','wppizza-admin'), WPPIZZA_NAME) ,
				'description'=>array()
			));			
		}

		return $settings;
	}
	/*------------------------------------------------------------------------------
	#	[output option fields - setting page]
	#	@since 3.0
	#	@return array()
	------------------------------------------------------------------------------*/
	function admin_options_fields_settings($wppizza_options, $options_key, $field, $label, $description){
		if($field=='repurchase'){
			echo "<label>";
				echo "<input id='".$field."' name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' type='checkbox'  ". checked($wppizza_options[$options_key][$field],true,false)." value='1' />";
				echo "" . $label . "";
			echo "</label>";
			echo"".$description."";
		}
	}
	/*------------------------------------------------------------------------------
	#
	#
	#	[localization page]
	#
	#
	------------------------------------------------------------------------------*/
	/****************************************************************
	*	[settigs section  - localization page]
	*	@since 3.0
	*	@return array()
	****************************************************************/
	function admin_options_localization($settings, $sections, $fields, $inputs, $help){
		global $wppizza_options;
		/* skip if not enabled */
		if(empty($wppizza_options[$this->settings_page]['repurchase'])){
			return $settings;
		}
		/********************************
		*	[Labels]
		********************************/
		/*sections*/
		if($sections){
			$add_settings['sections'][$this->section_key] =  __('User Purchase History - Repurchasing', 'wppizza-admin');
		}
		/*fields*/
		if($fields){
			$field = 'history_label_reorder_header';
			$add_settings['fields'][$this->section_key][$field] = array( '' , array(
				'value_key'=>$field,
				'option_key'=>$this->localization_page,
				'label'=>__('Label Header Reorder', 'wppizza-admin')
			));				
			$field = 'history_label_item_reorder_button';
			$add_settings['fields'][$this->section_key][$field] = array( '' , array(
				'value_key'=>$field,
				'option_key'=>$this->localization_page,
				'label'=>__('Label Reorder Item Button', 'wppizza-admin')
			));	
			$field = 'history_title_item_reorder_button';
			$add_settings['fields'][$this->section_key][$field] = array( '' , array(
				'value_key'=>$field,
				'option_key'=>$this->localization_page,
				'label'=>__('Title Reorder Item Button (shown on hover)', 'wppizza-admin')
			));						
			$field = 'history_label_reorder_purchase_button';
			$add_settings['fields'][$this->section_key][$field] = array( '' , array(
				'value_key'=>$field,
				'option_key'=>$this->localization_page,
				'label'=>__('Label Reorder Entire Purchase Button', 'wppizza-admin')
			));				
			$field = 'history_title_reorder_purchase_button';
			$add_settings['fields'][$this->section_key][$field] = array( '' , array(
				'value_key'=>$field,
				'option_key'=>$this->localization_page,
				'label'=>__('Title Reorder Entire Purchase Button (shown on hover)', 'wppizza-admin')
			));	
			$field = 'history_reorder_not_available';
			$add_settings['fields'][$this->section_key][$field] = array( '' , array(
				'value_key'=>$field,
				'option_key'=>$this->localization_page,
				'label'=>__('Label Reorder Not Available', 'wppizza-admin')
			));			
		
		}
	
		/*
			splice section and fields into the required position on admin page
		*/
		if(!empty($add_settings)){
			if($sections){
				$settings['sections'] = wppizza_array_splice($settings['sections'], $add_settings['sections'], $this->localization_position);
			}
			if($fields){
				$settings['fields'] = wppizza_array_splice($settings['fields'], $add_settings['fields'], $this->localization_position);
			}
		}
		return $settings;
	}	
	
	
	
	
	
	/*------------------------------------------------------------------------------
	#	[insert default option on install]
	#	$parameter $options array() | filter passing on filtered options
	#	@since 3.0
	#	@return array()
	------------------------------------------------------------------------------*/
	function options_default($options){

		/* 
			order settings 
		*/		
		$options[$this->settings_page]['repurchase'] = false;		

		/* 
			localization 
		*/
		$options[$this->localization_page]['history_label_reorder_header'] =  esc_html__('Re-order', 'wppizza');
		$options[$this->localization_page]['history_label_item_reorder_button'] =  esc_html__('+', 'wppizza');
		$options[$this->localization_page]['history_title_item_reorder_button'] =  esc_html__('Re-order this item', 'wppizza');
		$options[$this->localization_page]['history_label_reorder_purchase_button'] =  esc_html__('Re-order', 'wppizza');
		$options[$this->localization_page]['history_title_reorder_purchase_button'] =  esc_html__('Re-order', 'wppizza');
		$options[$this->localization_page]['history_reorder_not_available'] =  esc_html__('N/A', 'wppizza-admin');
		
	return $options;
	}

	/*------------------------------------------------------------------------------
	#	[validate options on save/update]
	#
	#	@since 3.0
	#	@return array()
	------------------------------------------------------------------------------*/
	function options_validate($options, $input){
		/**make sure we get the full array on install/update**/
		if ( empty( $_POST['_wp_http_referer'] ) ) {
			return $input;
		}
		/* 
			settings 
		*/
		if(isset($_POST[''.WPPIZZA_SLUG.'_'.$this->settings_page.''])){
			$options[$this->settings_page]['repurchase'] = !empty($input[$this->settings_page]['repurchase']) ? true : false;			
		}
		
		/* 
			localization strings are automatically validated 
		*/		
		
	return $options;
	}
}
/***************************************************************
*
*	[ini]
*
***************************************************************/
$WPPIZZA_MODULE_ORDERSETTINGS_REPURCHASE = new WPPIZZA_MODULE_ORDERSETTINGS_REPURCHASE();
?>