<?php
/**
* WPPIZZA_MODULE_SKU Class
*
* @package     WPPIZZA
* @subpackage  WPPIZZA_MODULE_SKU
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
class WPPIZZA_MODULE_SKU{

	private $settings_page = 'settings';/* which admin subpage (identified there by this->class_key) are we adding this to */


	private $layout_page = 'layout';/* which admin subpage (identified there by this->class_key) are we adding this to */


	private $localization_page = 'localization';/* which admin subpage (identified there by this->class_key) are we adding this to */
	private $localization_position = 'itemised-order';/* at which admin position (section) should this appear on above admin subpage, integer(zero indexed - for numeric position) or key ('layout-style' for example) after which it should appear*/


	private $section_key = 'sku';/* must be unique */


	function __construct() {
		/**********************************************************
			[add settings to admin]
		***********************************************************/
		if(is_admin()){
			/* add admin options settings page*/
			add_filter('wppizza_filter_settings_sections_'.$this->settings_page.'', array($this, 'admin_options_settings'), 40, 5);
			/* add admin options settings page fields */
			add_action('wppizza_admin_settings_section_fields_'.$this->settings_page.'', array($this, 'admin_options_fields_settings'), 10, 5);

			/* add admin options layout page*/
			add_filter('wppizza_filter_settings_sections_'.$this->layout_page.'', array($this, 'admin_options_layout'), 40, 5);
			/* add admin options layout page fields */
			add_action('wppizza_admin_settings_section_fields_'.$this->layout_page.'', array($this, 'admin_options_fields_layout'), 10, 5);

			/* add admin options localization page - does not need fields action*/
			add_filter('wppizza_filter_settings_sections_'.$this->localization_page.'', array($this, 'admin_options_localization'), 40, 5);


			/** admin metaboxes skus per size **/
			add_filter('wppizza_filter_admin_metaboxes', array( $this, 'add_admin_metaboxes'), 55, 4);/* 55 to add after sizes| pricetiers and before additives */
			add_filter('wppizza_filter_admin_save_metaboxes',array( $this, 'save_admin_metaboxes'), 10, 3);
			add_filter('wppizza_ajax_action_admin_sizeschanged', array( $this, 'ajax_admin_sizeschanged'), 10, 2);/* change sku metaboxes on change of pricetier/sizes */

			/**add default options **/
			add_filter('wppizza_filter_setup_default_options', array( $this, 'options_default'));
			/**validate options**/
			add_filter('wppizza_filter_options_validate', array( $this, 'options_validate'), 10, 2 );
		}
		/**********************************************************
			[filter depending on settings]
		***********************************************************/
		/****************************************************
		*	filter sku: loop post title and price/sizes labels
		*****************************************************/
			add_filter('wppizza_filter_post_title', array( $this, 'post_title'),10,2);/** add sku's to titles in loop **/
			add_filter('wppizza_filter_post_prices', array( $this, 'post_prices'),10,2);/** add sku's to sizes in loop **/
	
		/****************************************************
		*	filter sku: add sku parameters to session menu items
		*****************************************************/	
			add_filter('wppizza_fltr_order_session', array( $this, 'order_session'),10);/** add sku's to session menu items **/	
		/****************************************************
		*	filter sku: add sku parameters to order menu items
		*****************************************************/				
			add_filter('wppizza_filter_order_details_formatted', array( $this, 'order_details_formatted'), 10, 2);/** add sku's to cart menu items **/

		
		/****************************************************
		*	output sku: add sku's to cart menu items
		*****************************************************/				
			add_filter('wppizza_filter_cart_items_from_session', array( $this, 'cart_items'));
		/****************************************************
		*	output sku: add sku's to pages (order, confirmation , thank you, user order history ) if/as enabled
		*****************************************************/
			add_filter('wppizza_filter_order_item_header_markup', array( $this, 'header_columns'),1000, 3);/** add sku label to header column. high priority to not fall founl of remove filter that removes everything in the cart display except the absolute essentials (to make things fit) **/		
			add_filter('wppizza_filter_order_item_columns', array( $this, 'item_columns'),1000, 8);/** add sku to item column. high priority to not fall founl of remove filter that removes everything in the cart display except the absolute essentials (to make things fit) **/		
		/****************************************************
		*	output sku: add sku's to email/print templates
		*****************************************************/
			add_filter('wppizza_filter_itemised_order_columns', array( $this, 'columns_templates'),10, 3);/** add sku header column and value **/
		/****************************************************
		*	filter sku: search widget. search for sku's too
		*****************************************************/
			add_filter('wppizza_filter_search',array( $this, 'search_sku'));/*search for sku's too in meta data*/

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
	/***************************************************************************************************************************
	*
	*
	*	[SKU's] allow searching for sku's
	*
	*
	***************************************************************************************************************************/
	function search_sku($query){
		global $wppizza_options;
		if(!empty($wppizza_options[$this->settings_page]['sku_enable']) && !empty($wppizza_options[$this->settings_page]['sku_search']) ){
			/**what did we search for*/
			$queryVar=($query->query_vars['s']);/*searches - according to Otto - are case insensitive anyway. no need to cast things to lowercase*/
			$queryLength=strlen($queryVar);

			/**search meta data for sku's*/
			$args = array(
			 'post_type'=>WPPIZZA_POST_TYPE,
			 'meta_query' => array(
					array (
						'key' => WPPIZZA_SLUG.'_sku',
						'value' => $queryVar,
						'compare' => '='
					)
				)
			);

			/**allow partial sku search, using integer of constant to define minimum required string length. minimum 3*/
			if(!empty($wppizza_options[$this->settings_page]['sku_search_partial'])){
				/**minimum of 5 chars to do a partial search unless overridden by constant**/
				$minQueryLength = $wppizza_options[$this->settings_page]['sku_search_length'];
				
				/**change comparisaon to LIKE if partial enabled and q str length>=$minQueryLength*/
				if($queryLength >= $minQueryLength){
					$args['meta_query'][0]['compare']='LIKE';
				}
			}


			$sku_query = new WP_Query( $args );
			/**
				if we have found posts with this sku, replace results to only show results with that sku
				by replacing s query and including  post__in instead
			**/
			if($sku_query->post_count>0){
				$sku_post_in=array();
				foreach($sku_query->posts as $key=>$post){
					$sku_post_in[$post->ID]=$post->ID;
				}
				$query->set('s','');//set search query to '' as it would not find meta seraches*/
				$query->set('post__in',$sku_post_in);

				/**
					as the search string (get_query_var( 's' )) "search for [x]" and searchbox prefill would normally be empty
					as we've unset it, use get_search_query filter to set to $_GET[s]
				**/
				add_filter('get_search_query',array($this, 'set_search_query_sku'));
			}
		}

		return $query;
	}
	/********************************************************************
	*	set found SKU as search_query
	********************************************************************/
	function set_search_query_sku($query_var){
		$query_var=esc_html($_GET['s']);
		return $query_var;
	}

	/********************************************************************************** 
	*	columns templates (email/print) - header and value  
	**********************************************************************************/
	function columns_templates($columns, $txt, $type){		
		global $wppizza_options;
		/* skip if not enabled */
		if(empty($wppizza_options[$this->settings_page]['sku_enable']) || empty($wppizza_options[$this->layout_page]['sku_display'][$type])){
			return $columns;
		}

		/** array to insert **/
		$splice[$this->section_key] = 	array();	
		$splice[$this->section_key]['key'] = 	$this->section_key ;
		//$splice[$this->section_key]['tr_class'] = 	'items' ;/* should always be items */
		$splice[$this->section_key]['label'] = 	$txt['sku_label'] ;
		$splice[$this->section_key]['fields']= array();
		$splice[$this->section_key]['fields'][]= $this->section_key;
		
		/** splice into header columns **/
		$columns = wppizza_array_splice($columns, $splice, ($wppizza_options[$this->layout_page]['sku_display'][$type]-1));
	
		
	return $columns;		
	}
	/********************************************************************************** 
	*	header columns pages (order, confirmation , thank you, user order history ) 
	**********************************************************************************/
	function header_columns($markup_header, $txt, $type){
		global $wppizza_options;
		/* skip if not enabled */
		if(empty($wppizza_options[$this->settings_page]['sku_enable']) || empty($wppizza_options[$this->layout_page]['sku_display'][$type])){
			return $markup_header;
		}		

		/** div to insert **/		
		$splice['thead_th_'.$this->section_key]= '<th class="'.WPPIZZA_SLUG.'-item-'.$this->section_key.'-th">'.$txt['sku_label'].'</th>';/* table cell */
		/** 
			splice into header columns 
			as cart only has left and right
			set 0 or 1000 respectively	
			currently not in use
		**/
		//$position = 0 ;
		if($type == 'cart'){
			$position = ($wppizza_options[$this->layout_page]['sku_display'][$type] == 'left') ? 0 : 10000;
		}else{
			$position = ($wppizza_options[$this->layout_page]['sku_display'][$type]-1) ;	
		}
		$markup_header = wppizza_array_splice($markup_header, $splice, $position);

	return $markup_header;
	}
	/**********************************************************************************
	*	item columns pages (order, confirmation, thank you, user order history ) 
	**********************************************************************************/
	function item_columns($item_column, $key , $item, $cart, $item_count, $order_id, $txt, $type){
		global $wppizza_options;
		/* 
			skip if not enabled 
			for the moment, skip for cart too
		*/
		if(empty($wppizza_options[$this->settings_page]['sku_enable']) || empty($wppizza_options[$this->layout_page]['sku_display'][$type]) || $type == 'cart' ){
			return $item_column;
		}
		/** td to insert **/		
		$splice['item_td_'.$this->section_key]= '<td class="'.WPPIZZA_SLUG.'-item-'.$this->section_key.'">'.$item[$this->section_key].'</td>';/* table cell */
		
		
		/** 
			splice into header columns 
			as cart only has left and right
			set 0 or 1000 respectively
			currently not in use	
		**/
		$position = 0 ;
		if($type == 'cart'){
			$position = ($wppizza_options[$this->layout_page]['sku_display'][$type] == 'left') ? 0 : 10000;
		}else{
			$position = ($wppizza_options[$this->layout_page]['sku_display'][$type]-1) ;	
		}		
		/** splice into item columns **/
		$item_column = wppizza_array_splice($item_column, $splice, $position);	

	return $item_column;
	}
	/*********************************************************************************
	*	[adding SKU's to post title in loop as required]
	*********************************************************************************/
	function post_title($post_title, $post_id){
		global $wppizza_options;
		/* 
			skip if not enabled 
			for the moment, skip for cart too
		*/
		if(empty($wppizza_options[$this->settings_page]['sku_enable']) || empty($wppizza_options[$this->layout_page]['sku_display']['menu_listing_title']) ){
			return $post_title;
		}
		
		/* get SKUs*/
		$post_meta = get_post_meta($post_id, WPPIZZA_SLUG, true);
		/*check if we have a main title sku*/
		$sku_menu_title=!empty($post_meta[$this->section_key][-1]) ? $post_meta[$this->section_key][-1] : false;
		/* if there's no main , get the first size if we can**/
		if(!$sku_menu_title){
			$sku_menu_title=!empty($post_meta[$this->section_key][0]) ? $post_meta[$this->section_key][0] : false;
		}

		/**apply if SKU !=''**/
		if(!empty($sku_menu_title)){
			$new_title='';

			/*sku left*/
			if($wppizza_options[$this->layout_page]['sku_display']['menu_listing_title']=='left'){
				$new_title.='<span class="'.WPPIZZA_SLUG.'_'.$this->section_key.'_title">'.$sku_menu_title.'</span>';
			}

			/*title*/
			$new_title.=''.$post_title.'';

			/*sku right*/
			if($wppizza_options[$this->layout_page]['sku_display']['menu_listing_title']=='right'){
				$new_title.='<span class="'.WPPIZZA_SLUG.'_'.$this->section_key.'_title">'.$sku_menu_title.'</span>';
			}
			
			return $new_title;
		}
		
		return $post_title;
	}
	/*********************************************************************************
	*	[adding SKU's menu items sizes in loop as  required]
	*********************************************************************************/
	function post_prices($prices, $post_data){
		global $wppizza_options;
		/* skip if not enabled */
		if(empty($wppizza_options[$this->settings_page]['sku_enable']) || empty($wppizza_options[$this->layout_page]['sku_display']['menu_listing_size']) ){
			return $prices;
		}		

		/* for convenience */
		$sku_keys = !empty($post_data -> wppizza_data[$this->section_key]) ? $post_data -> wppizza_data[$this->section_key] : array();
		
		/**add size label to meta and pre or append sku as required**/
		foreach($prices as $size_key=>$size_values){

			/*check if we have size sku*/
			$size_sku=!empty($sku_keys[$size_key]) ? $sku_keys[$size_key] : false;
			/* if there's no size main , get the main sku**/
			if(!$size_sku){
				$size_sku=!empty($sku_keys[-1]) ? $sku_keys[-1] : false;
			}

			$prices[$size_key]['size']='';
			/*prepend*/
			if(!empty($size_sku) && $wppizza_options[$this->layout_page]['sku_display']['menu_listing_size']=='left'){
				$prices[$size_key]['size'].='<span class="'.WPPIZZA_SLUG.'_sku">'.$size_sku.'</span>';
			}

			/**show if not replaced by sqk provided there is one*/
			if(empty($wppizza_options[$this->layout_page]['sku_replaces_size']) || empty($wppizza_options[$this->layout_page]['sku_display']['menu_listing_size']) || empty($size_sku)){
				$prices[$size_key]['size'].=''.$size_values['size'];
			}

			/*append*/
			if(!empty($size_sku) && $wppizza_options[$this->layout_page]['sku_display']['menu_listing_size']=='right'){
				$prices[$size_key]['size'].='<span class="'.WPPIZZA_SLUG.'_sku">'.$size_sku.'</span>';
			}
		}
	
	return $prices;
	}

	/*********************************************************************************
	*	[adding SKU's to session data]
	*********************************************************************************/
	function order_session($session){
		global $wppizza_options;


		/* skip if not enabled */
		if(empty($wppizza_options[$this->settings_page]['sku_enable']) || !isset($session['items'])){
			return $session;
		}
				
		/* loop through items, adding sku's */
		foreach($session['items'] as $key=>$item){
			/* get SKUs*/
			$post_meta = get_post_meta($item['post_id'], WPPIZZA_SLUG, true);


			/*check if we have an sku for this size*/
			$sku=!empty($post_meta[$this->section_key][$item['size']]) ? $post_meta[$this->section_key][$item['size']] : false;
			/* if there's no sku for this size , try to get main sku**/
			if(!$sku){
				$sku=!empty($post_meta[$this->section_key][-1]) ? $post_meta[$this->section_key][-1] : false;
			}
			/* add to session data */
			$session['items'][$key][$this->section_key] = $sku;
		}

	return $session;
	}

	/*********************************************************************************
	*	[adding SKU's to order data retrieved formatted from db]
	*********************************************************************************/
	function order_details_formatted($order_details_formatted, $order_values){
		global $wppizza_options;
		/* skip if not enabled */
		if(empty($wppizza_options[$this->settings_page]['sku_enable']) || !isset($order_values['items'])){
			return $order_details_formatted;
		}
				
		/* loop through items, adding it's sku's */
		$items = $order_values['items'];

		foreach($order_values['items'] as $key=>$item){
			$order_details_formatted['sections']['order']['items'][$key][$this->section_key] = $item[$this->section_key];
		}

	return $order_details_formatted;
	}


	/*********************************************************************************
	*	[replacing price label as appropriate with sku]
	*********************************************************************************/
	function cart_items($items){
		global $wppizza_options;
		/* skip if not enabled */
		if(empty($wppizza_options[$this->settings_page]['sku_enable']) || !isset($items)){
			return $items;
		}

		foreach($items as $item_key => $item){
			
			
			$items[$item_key]['price_label']='';
			/*prepend*/
			if(!empty($item['sku']) && $wppizza_options[$this->layout_page]['sku_display']['cart']=='left'){
				$items[$item_key]['price_label'].='<span class="'.WPPIZZA_SLUG.'_sku">'.$item['sku'].'</span>';
			}

			/**show if not replaced by sku provided there is one*/
			if(empty($wppizza_options[$this->layout_page]['sku_replaces_size']) || empty($wppizza_options[$this->layout_page]['sku_display']['cart']) || empty($item['sku'])){
				$items[$item_key]['price_label'].=''.$item['price_label'];
			}

			/*append*/
			if(!empty($item['sku']) && $wppizza_options[$this->layout_page]['sku_display']['cart']=='right'){
				$items[$item_key]['price_label'].='<span class="'.WPPIZZA_SLUG.'_sku">'.$item['sku'].'</span>';
			}			
				
		}
		return $items;
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
	/*********************************************************
	*
	*	[add metaboxes]
	*	@since 3.0
	*
	*********************************************************/	
	function add_admin_metaboxes($meta_boxes, $meta_values, $meal_sizes, $wppizza_options){
		/* skip if not enabled */
		if(empty($wppizza_options[$this->settings_page]['sku_enable'])){
			return $meta_boxes;
		}

		/* add sku meta boxes*/
		$meta_boxes[$this->section_key]='';
		$meta_boxes[$this->section_key].="<div class='".WPPIZZA_SLUG."_option_meta'>";
			
			$meta_boxes[$this->section_key].="<label class='".WPPIZZA_SLUG."-meta-label'>".__('SKUs', 'wppizza-admin').": </label>";
			
				/* sku item global */
				$meta_boxes[$this->section_key].="<span class='".WPPIZZA_SLUG."_sku'>";
					$val=!empty($meta_values[$this->section_key][-1]) ? $meta_values[$this->section_key][-1] : '';
					$meta_boxes[$this->section_key].="".__('Menu Item','wppizza-admin').": <input name='".WPPIZZA_SLUG."[".$this->section_key."][-1]' size='10' type='text' value='".$val."' />";			
				$meta_boxes[$this->section_key].="</span>";
				
				/* sku per size , wrapped in span to replace via ajax on size change*/
				$meta_boxes[$this->section_key].="<span class='".WPPIZZA_SLUG."_".$this->section_key."_sizes'>";
					foreach($meta_values['prices'] as $k=>$v){
						$ident=$wppizza_options['sizes'][$meta_values['sizes']][$k]['lbl'] ;
						$val=!empty($meta_values[$this->section_key][$k]) ? $meta_values[$this->section_key][$k] : '';
						$meta_boxes[$this->section_key].=" ".$ident.": <input name='".WPPIZZA_SLUG."[".$this->section_key."][".$k."]' size='5' type='text' value='".$val."' />";				
					}
				$meta_boxes[$this->section_key].="</span>";

		$meta_boxes[$this->section_key].="</div>";

	return $meta_boxes;	
	}
	/*********************************************************
	*
	*	[save metaboxes values]
	*	@since 3.0
	*
	*********************************************************/	
	function save_admin_metaboxes($item_meta, $item_id, $wppizza_options){

		/* skip if not enabled */
		if(empty($wppizza_options[$this->settings_page]['sku_enable'])){
			return $item_meta;
		}		

		//**save new sku array replacing all od values**//
		$item_meta[$this->section_key]=array();
	    
	    if(isset($_POST[WPPIZZA_SLUG]['sku'])){
	    	/*as we might have different number of sizes, delete all old single sku meta keys for this item first*/
	    	delete_post_meta($item_id, WPPIZZA_SLUG.'_'.$this->section_key);

	    	/**insert/add/edit current**/
	    	foreach($_POST[WPPIZZA_SLUG]['sku'] as $k=>$v){
	    		/**add to main wppizza meta data as serialized array*/
		    	$item_meta[$this->section_key][$k] = wppizza_validate_string($_POST[WPPIZZA_SLUG][$this->section_key][$k]);

		    	/**add individual sku keys if not empty. set to lowercase to make case insesitive searches*/
		    	/*searches - according to Otto - are case insensitive. so no need to save as lowercase for example*/
		    	if(!empty($item_meta[$this->section_key][$k])){
		    		add_post_meta($item_id, WPPIZZA_SLUG.'_'.$this->section_key, $item_meta[$this->section_key][$k] );
		    	}
	    	}
	    }
	return $item_meta;
	}

	/*********************************************************
	*
	*	[ajax change sku metaboxes on price tier (sizes) change]
	*	@since 3.0
	*
	*********************************************************/	
	function ajax_admin_sizeschanged($obj, $set_size_id){
		global $wppizza_options;
		/* skip if not enabled */
		if(empty($wppizza_options[$this->settings_page]['sku_enable'])){
			return $obj;
		}	

		/*only if sku enabled and only on post/metaboxes page*/
		$sku='';
		if(is_array($wppizza_options['sizes'][$set_size_id])){
		foreach($wppizza_options['sizes'][$set_size_id] as $size_key=>$sizes){
			$sku.=" ".$sizes['lbl'].": <input name='".WPPIZZA_SLUG."[".$this->section_key."][".$size_key."]' size='10' type='text' value='' />";
		}}
		$obj['inp'][$this->section_key]=$sku;
		$obj['element'][$this->section_key]='.'.WPPIZZA_SLUG.'_'.$this->section_key.'_sizes';/**html element to empty and replace with new input boxes**/

	return $obj;
	}	
	
	/*------------------------------------------------------------------------------
	#
	#
	#	[settings page]
	#
	#
	------------------------------------------------------------------------------*/
	/****************************************************************
	*	[settigs section  - setting page]
	*	@since 3.0
	*	@return array()
	****************************************************************/
	function admin_options_settings($settings, $sections, $fields, $inputs, $help){

		/*sections*/
		if($sections){
			$settings['sections'][$this->section_key] =  __('Sku Settings', 'wppizza-admin');
		}
		/*fields*/
		if($fields){
			$field = 'sku_enable';
			$settings['fields'][$this->section_key][$field] = array( __('Enable setting of SKU\'s:', 'wppizza-admin') , array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>sprintf( __( 'check to be able to set SKUs for menu items [more options will be become available in %s -> layout/localization]', 'wppizza-admin' ), WPPIZZA_NAME),
				'description'=>array()
			));
			$field = 'sku_search';
			$settings['fields'][$this->section_key][$field] = array( __('Enable search by SKU:', 'wppizza-admin') , array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('make SKUs searchable (through wppizza search widget/shortcode. menu item search must be enabled)', 'wppizza-admin'),
				'description'=>array()
			));
			$field = 'sku_search_partial';
			$settings['fields'][$this->section_key][$field] = array( __('Enable partial SKU search:', 'wppizza-admin') , array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('allow search to match partial SKUs', 'wppizza-admin'),
				'description'=>array()
			));
			$field = 'sku_search_length';
			$settings['fields'][$this->section_key][$field] = array( __('Partial search min characters:', 'wppizza-admin') , array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('If partial search enabled above, minimum characters to be entered in searchfield to search for partially matching SKUs [minimum 3, default 5]', 'wppizza-admin'),
				'description'=>array()
			));						
		}

		return $settings;
	}
	/****************************************************************
	*	[output option fields - setting page]
	*	@since 3.0
	*	@return array()
	****************************************************************/
	function admin_options_fields_settings($wppizza_options, $options_key, $field, $label, $description){

		if($field=='sku_enable'){
			print'<label>';
				print "<input id='".$field."' name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' type='checkbox'  ". checked($wppizza_options[$options_key][$field],true,false)." value='1' />";
				print'' . $label . '';
			print'</label>';
			print'' . $description . '';
		}
		if($field=='sku_search'){
			print'<label>';
				print "<input id='".$field."' name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' type='checkbox'  ". checked($wppizza_options[$options_key][$field],true,false)." value='1' />";
				print'' . $label . '';
			print'</label>';
			print'' . $description . '';
		}

		if($field=='sku_search_partial'){
			print'<label>';
				print "<input id='".$field."' name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' type='checkbox'  ". checked($wppizza_options[$options_key][$field],true,false)." value='1' />";
				print'' . $label . '';
			print'</label>';
			print'' . $description . '';
		}
		if($field=='sku_search_length'){
			print'<label>';
				print "<input id='".$field."' name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' size='2' type='text' value='".$wppizza_options[$options_key][$field]."' />";
				print'' . $label . '';
			print'</label>';
			print'' . $description . '';
		}		
	}

	/*------------------------------------------------------------------------------
	#
	#
	#	[layout page]
	#
	#
	------------------------------------------------------------------------------*/
	/****************************************************************
	*	[settigs section  - layout page]
	*	@since 3.0
	*	@return array()
	****************************************************************/
	function admin_options_layout($settings, $sections, $fields, $inputs, $help){
		global $wppizza_options;

		/* skip if not enabled */
		if(empty($wppizza_options[$this->settings_page]['sku_enable'])){
			return $settings;
		}

		/*sections*/
		if($sections){
			$settings['sections'][$this->section_key] =  __('SKU display:', 'wppizza-admin');
		}
		/*fields*/
		if($fields){
			$field = 'sku_replaces_size';
			$settings['fields'][$this->section_key][$field] = array( __('Replace size labels with SKU (if exist):', 'wppizza-admin') , array(
				'value_key'=>$field,
				'option_key'=>$this->layout_page,
				'label'=>__('Except for menu listing title, enabling this option will render below display positions irrelevant', 'wppizza-admin'),
				'description'=>array()
			));

			/** array of keys that have off left right options */
			foreach($this->left_right_options() as $opt_key => $opt_val){
				$field = $opt_key;
				$settings['fields'][$this->section_key][$field] = array( $opt_val['label'] , array(
					'value_key'=>$field,
					'option_key'=>$this->layout_page,
					'label'=>'',
					'description'=>array()
				));
			}

			/** array of keys that have integer options */
			foreach($this->numeric_options() as $opt_key => $opt_val){
				$field = $opt_key;
				$settings['fields'][$this->section_key][$field] = array( $opt_val['label'] , array(
					'value_key'=>$field,
					'option_key'=>$this->layout_page,
					'label'=>__('column number [ 0=disabled, 1=first, 2=second etc]', 'wppizza-admin'),
					'description'=>array()
				));
			}
		}

		return $settings;
	}
	/****************************************************************
	*	[output option fields - setting page]
	*	@since 3.0
	*	@return array()
	****************************************************************/
	function admin_options_fields_layout($wppizza_options, $options_key, $field, $label, $description){

		if($field=='sku_replaces_size'){
			print'<label>';
				print "<input id='".$field."' name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' type='checkbox'  ". checked($wppizza_options[$options_key][$field],true,false)." value='1' />";
				print'' . $label . '';
			print'</label>';
			print'' . $description . '';
		}
		
		$num_options = $this->numeric_options($field);
		if(!empty($num_options)){
			print'<label>';
				print "<input id='".$field."' name='".WPPIZZA_SLUG."[".$options_key."][sku_display][".$field."]' size='3' type='text' value='".$wppizza_options[$options_key]['sku_display'][$field]."' />";
				print'' . $label . '';
			print'</label>';
			print'' . $description . '';
		}

		$left_right_options = $this->left_right_options($field);
		if(!empty($left_right_options)){
			print'<label>';
				print "".__('off','wppizza-admin')." <input id='".$field."' name='".WPPIZZA_SLUG."[".$options_key."][sku_display][".$field."]' size='3' ". checked($wppizza_options[$options_key]['sku_display'][$field],0,false). " type='radio' value='0' />";
			print'</label>';
			print'<label>';
				print "".__('left','wppizza-admin')." <input id='".$field."' name='".WPPIZZA_SLUG."[".$options_key."][sku_display][".$field."]' size='3' ". checked($wppizza_options[$options_key]['sku_display'][$field],'left',false). "type='radio' value='left' />";
			print'</label>';
			print'<label>';
				print "".__('right','wppizza-admin')." <input id='".$field."' name='".WPPIZZA_SLUG."[".$options_key."][sku_display][".$field."]' size='3' ". checked($wppizza_options[$options_key]['sku_display'][$field],'right',false). "type='radio' value='right' />";
				print'' . $label . '';
			print'</label>';
			print'' . $description . '';
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
		if(empty($wppizza_options[$this->settings_page]['sku_enable'])){
			return $settings;
		}
		/********************************
		*	[Labels SKU]
		********************************/
		/*sections*/
		if($sections){
			$settings['sections'][$this->section_key] =  __('SKU Label - Itemised Order', 'wppizza-admin');
		}
		/*fields*/
		if($fields){
			$field = 'sku_label';
			$settings['fields'][$this->section_key][$field] = array('' , array(
				'value_key'=>$field,
				'option_key'=>$this->localization_page,
				'label'=>__('SKU Label', 'wppizza-admin')
			));				
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
			settings 
		*/
		$options[$this->settings_page]['sku_enable'] = false;
		$options[$this->settings_page]['sku_search'] = false;
		$options[$this->settings_page]['sku_search_partial'] = false;
		$options[$this->settings_page]['sku_search_length'] = 5;

		/* 
			layout 
		*/
		$options[$this->layout_page]['sku_replaces_size'] = false;
		/* off|left|right options */
		foreach($this->left_right_options() as $opt_key => $opt_val){
			$options[$this->layout_page]['sku_display'][$opt_key] = $opt_val['default'];
		}
		/* numeric options options */
		foreach($this->numeric_options() as $opt_key => $opt_val){
			$options[$this->layout_page]['sku_display'][$opt_key] = $opt_val['default'];
		}

		/* 
			localization 
		*/
		$options[$this->localization_page]['sku_label'] =  esc_html__('SKU', 'wppizza');

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
			$options[$this->settings_page]['sku_enable'] = !empty($input[$this->settings_page]['sku_enable']) ? true : false;
			$options[$this->settings_page]['sku_search'] = !empty($input[$this->settings_page]['sku_search']) ? true : false;
			$options[$this->settings_page]['sku_search_partial'] = !empty($input[$this->settings_page]['sku_search_partial']) ? true : false;
			$options[$this->settings_page]['sku_search_length'] = (wppizza_validate_int_only($input[$this->settings_page]['sku_search_length']) >=3 ) ? $input[$this->settings_page]['sku_search_length']  : 5;
		}

		/* 
			layout 
		*/
		if(isset($_POST[''.WPPIZZA_SLUG.'_'.$this->layout_page.''])){
			$options[$this->layout_page]['sku_replaces_size'] = !empty($input[$this->layout_page]['sku_replaces_size']) ? true : false;
			/* validate off|left|right options */
			foreach($this->left_right_options() as $opt_key => $opt_val){
				$options[$this->layout_page]['sku_display'][$opt_key] = !empty($input[$this->layout_page]['sku_display'][$opt_key]) ? wppizza_validate_alpha_only($input[$this->layout_page]['sku_display'][$opt_key]) : 0;
			}
			/* validate numeric options */
			foreach($this->numeric_options() as $opt_key => $opt_val){
				$options[$this->layout_page]['sku_display'][$opt_key] = !empty($input[$this->layout_page]['sku_display'][$opt_key]) ? wppizza_validate_int_only($input[$this->layout_page]['sku_display'][$opt_key]) : 0;
			}

		}
		
		/* 
			localization strings are automatically validated 
		*/

		return $options;
	}

	/*------------------------------------------------------------------------------
	#
	#
	#	[helpers]
	#
	#
	------------------------------------------------------------------------------*/
	function left_right_options($selected = false){
		/** array of display options that can be set to off|left|right */
		$options = array();
		$options['menu_listing_title']=array('label'=>__('menu listings title', 'wppizza-admin'), 'default' => 0);
		$options['menu_listing_size']=array('label'=>__('menu listings sizes', 'wppizza-admin'), 'default' => 0);
		// currently disabled  // $options['cart']=array('label'=>__('cart', 'wppizza-admin'), 'default' => 0);
		/* only return selected option or false if not exists*/
		if($selected){
			if(isset($options[$selected])){
				return 	$options[$selected];
			}else{
				return false;
			}
		}
		return $options;
	}

	function numeric_options($selected = false){
		/** array of display options that can be set by integer */
		$options = array();
		$options['orderpage']=array('label'=>__('order page', 'wppizza-admin'), 'default' => 0);
		$options['confirmationpage']=array('label'=>__('confirmation page [if used in wppizza - order form settings]', 'wppizza-admin'), 'default' => 0);
		$options['thankyoupage']=array('label'=>__('thank you page [if enabled in wppizza->order settings]:', 'wppizza-admin'), 'default' => 0);		
		$options['orderhistory']=array('label'=>__('customer purchase history', 'wppizza-admin'), 'default' => 0);
				
		$options['emails']=array('label'=>__('emails', 'wppizza-admin'), 'default' => 0);
		$options['print']=array('label'=>__('admin print order', 'wppizza-admin'), 'default' => 0);

		/* only return selected option or false if not exists*/
		if($selected){
			if(isset($options[$selected])){
				return 	$options[$selected];
			}else{
				return false;
			}
		}
	return $options;
	}
}
/***************************************************************
*
*	[ini]
*
***************************************************************/
$WPPIZZA_MODULE_SKU = new WPPIZZA_MODULE_SKU();
?>