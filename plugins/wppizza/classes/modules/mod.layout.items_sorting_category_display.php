<?php
/**
* WPPIZZA_MODULE_ITEMS_SORTING_CATEGORY_DISPLAY Class
*
* @package     WPPIZZA
* @subpackage  WPPIZZA_MODULE_ITEMS_SORTING_CATEGORY_DISPLAY
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
class WPPIZZA_MODULE_ITEMS_SORTING_CATEGORY_DISPLAY{

	private $layout_page = 'layout';/* which admin subpage (identified there by this->class_key) are we adding this to */
	private $layout_section = 'layout-itemsorting-categories';/* must be unique */

	private $items_keys_categories = array(); /* keys of menu items that should have the category name added before them */

	function __construct() {
		/**********************************************************
			[add settings to admin]
		***********************************************************/
		if(is_admin()){
			/* add admin options */
			add_filter('wppizza_filter_settings_sections_'.$this->layout_page.'', array($this, 'admin_options_settings'), 50, 5);
			/* add admin options fileds */
			add_action('wppizza_admin_settings_section_fields_'.$this->layout_page.'', array($this, 'admin_options_fields'), 10, 5);
			/**add default options **/
			add_filter('wppizza_filter_setup_default_options', array( $this, 'options_default'));
			/**validate options**/
			add_filter('wppizza_filter_options_validate', array( $this, 'options_validate'), 10, 2 );
		}
		/**********************************************************
			[filter depending on settings]
		***********************************************************/
		/***
			set menu item sort order
		****/
		add_filter('wppizza_filter_loop_args', array( $this, 'filter_loop_args'));

		/***
			add category / blogname to cart|order display
		***/
		/* group items by blogid, category and sort as per settings - global for cart and order */
		add_filter('wppizza_fltr_cart_items', array( $this, 'group_sort_items'), 10, 2 );

		/* add parameters blog name, category name for cart/session and order variables*/
		// 3.9.5 - the 'wppizza_filter_cart_items_from_session' filter does not actually seem to exist (anymore) but leave it here for now
		//add_filter('wppizza_filter_cart_items_from_session', array( $this, 'get_category_for_cart_email_order'));
		add_filter('wppizza_filter_order_items_markup', array( $this, 'get_category_for_cart_email_order'));//shopping cart / minicart / orderpage / purchase history
		add_filter('wppizza_filter_email_items_markup', array( $this, 'get_category_for_cart_email_order'));//emails/print templates

		/* add blog name , category name in cart */
		// 3.9.5 - the 'wppizza_filter_cart_items_from_session' filter does not actually seem to exist (anymore) but leave it here for now
		//add_filter('wppizza_filter_cart_item_markup', array( $this, 'show_category_in_cart'), 10, 3 );

		/* add blog name , category name in order */
		add_filter('wppizza_filter_order_item_markup', array( $this, 'show_category_in_order'), 10, 9 );
		/* add blog name , category name in html emails */
		add_filter('wppizza_filter_templates_item_markup_html', array( $this, 'show_category_in_html_templates'), 10, 5 );
		/* add blog name , category name in plaintext emails */
		add_filter('wppizza_filter_templates_item_markup_plaintext', array( $this, 'show_category_in_plaintext_templates'), 10, 3 );

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
	/*********************************************************************
	*
	* 	[markup]
	*
	**********************************************************************/
	/*
		add to plaintext email markup
	*/
	function show_category_in_plaintext_templates($markup_item, $key, $item){
		global $wppizza_options;
		/* skip if not enabled */
		if(empty($wppizza_options[$this->layout_page]['items_group_sort_print_by_category'])){
			return $markup_item;
		}

		/*
			prepend to string as required
		*/
		$blog_cat_id = $item['blog_id'].'.'.$item['cat_id_selected'];
		if(isset($this -> items_keys_categories[$blog_cat_id]) && $this -> items_keys_categories[$blog_cat_id]['item_key'] == $key && $this -> items_keys_categories[$blog_cat_id]['category_path'] !=''){
			$prepend_category_name = '['.$this -> items_keys_categories[$blog_cat_id]['category_path'].']' ;
			$markup_item = PHP_EOL . $prepend_category_name .  PHP_EOL . $markup_item ;
		}
	return $markup_item;
	}

	/*
		add to html email markup
	*/
	function show_category_in_html_templates($markup_item, $key, $item, $items, $colspan){
		global $wppizza_options;
		/* skip if not enabled */
		if(empty($wppizza_options[$this->layout_page]['items_group_sort_print_by_category'])){
			return $markup_item;
		}
		/*
			prepend to array as required
		*/
		$blog_cat_id = $item['blog_id'].'.'.$item['cat_id_selected'];
		if(isset($this -> items_keys_categories[$blog_cat_id]) && $this -> items_keys_categories[$blog_cat_id]['item_key'] == $key && $this -> items_keys_categories[$blog_cat_id]['category_path'] !=''){
			$style = $wppizza_options[$this->layout_page]['items_category_hierarchy_email_style'];
			$prepend_category_name = '<tr class="'.WPPIZZA_SLUG.'-item-category"><td colspan="'.($colspan).'" style="'.$style.'">'.$this -> items_keys_categories[$blog_cat_id]['category_path'].'</td></tr>';
			array_unshift($markup_item, $prepend_category_name);
		}
	return $markup_item;
	}

	/*
		add category info to order markup
		(checkout page, confirmation page, thank you page, users order history)
	*/
	function show_category_in_order($markup_item, $key, $item, $cart, $colspan, $item_count, $order_id, $txt, $type){
		global $wppizza_options;

		/*
			skip if not enabled
		*/
		if(empty($wppizza_options[$this->layout_page]['items_group_sort_print_by_category'])){
			return $markup_item;
		}

		/*
			prepend category info (tr/td) to array as required
		*/
		$blog_cat_id = $item['blog_id'].'.'.$item['cat_id_selected'];
		if(isset($this -> items_keys_categories[$blog_cat_id]) && $this -> items_keys_categories[$blog_cat_id]['category_path'] !=''){

			/*
				cat id for this item
			*/
			$cat_id = $this -> items_keys_categories[$blog_cat_id]['category_id'];

			/*
				only add for the first item in a particular category 
				but for each instance of a cart on page or called by ajax
			*/
			if(empty($this->order_category_info[$type][$order_id][$cat_id])){

				/*
					category tr/td
				*/
				$prepend_category_name['category_' . $cat_id . ''] = '<tr><td class="'.WPPIZZA_SLUG.'-item-category" colspan="'.($colspan-1).'">'.$this -> items_keys_categories[$blog_cat_id]['category_path'].'</td></tr>';
				/*
					prepend category to item output
				*/
				$markup_item = $prepend_category_name + $markup_item;
				/*
					keep track of already added cat headres for each order
					so we only add it once for each category
				*/
				$this->order_category_info[$type][$order_id][$cat_id] = true;
			}

		}

	return $markup_item;
	}

	/*
		add to cart markup
	*/
//	function show_category_in_cart($markup_item, $key, $item){
//		global $wppizza_options;
//		/* skip if not enabled */
//		if(empty($wppizza_options[$this->layout_page]['items_group_sort_print_by_category']) || $wppizza_options[$this->layout_page]['items_category_hierarchy_cart'] == 'none'){
//			return $markup_item;
//		}
//
//		/* prepend to array as required */
//		if(isset($this -> items_keys_categories[$key]) && $this -> items_keys_categories[$key]['category_path'] !=''){
//			$prepend_category_name = '<li class="'.WPPIZZA_SLUG.'-item-category">'.$this -> items_keys_categories[$key]['category_path'].'</li>';
//			array_unshift($markup_item, $prepend_category_name);
//		}
//	return $markup_item;
//	}


	/*********************************************************************
	*
	* 	[add get item keys that should have blogname /  category added ]
	*	as the purchase history page has multiple and different  orders
	*	we need to run this multiple times, but only add not yet determined
	*	categories/paths where necessary
	**********************************************************************/
	function get_category_for_cart_email_order($items){
		global $wppizza_options;

		/*
			skip if not enabled
		*/
		if(empty($wppizza_options[$this->layout_page]['items_group_sort_print_by_category'])){
			return $items;
		}
		/*
			loop through items of order
		*/
		foreach($items as $key => $item){

			$item_cat_id = $item['cat_id_selected'];
			$blog_cat_id = $item['blog_id'].'.'.$item_cat_id;

			/*
				if the category info  has already
				been added to the array, skip
			*/
			if(isset($this -> items_keys_categories[$blog_cat_id])){
				continue;
			}
			$category_title = !empty($item['item_in_categories'][$item_cat_id]['name']) ? $item['item_in_categories'][$item_cat_id]['name'] : $item['title'] ;
			$category_path = WPPIZZA() -> categories -> wppizza_get_taxonomy_parents($item_cat_id, $wppizza_options[$this->layout_page]['items_category_separator'], $wppizza_options[$this->layout_page]['items_category_hierarchy_cart']);
			/*
				if item has not been set to belong to any category, the above will throw an error
				so let's make one up from the title as otherwise "display and group by category" will simply fall over
				as it has no idea what to group by
			*/
			if ( is_wp_error( $category_path ) ) {
				$category_path = $wppizza_options['localization']['uncategorised'];
			}

			$this -> items_keys_categories[$blog_cat_id] = array('category_id' => $item_cat_id, 'category_path' => $category_path, 'category_title' => $category_title, 'item_key' => $key  );
		}


	return $items;
	}

	/*********************************************************************
	*
	* 	[group menu itmes and sort in cart by blog/category ]
	*
	**********************************************************************/
	function group_sort_items($groupedItems, $items){
		global $wppizza_options, $blog_id;

		/* skip if not enabled, or no items */
		if(empty($wppizza_options[$this->layout_page]['items_group_sort_print_by_category']) || empty($items)){
			return $groupedItems;
		}
		/* get all unique blog ids first to get categories order from particular blog and not switch blogs all day long further down*/
		$unique_blog_ids = array();
		if(isset($items) && is_array($items)){
		foreach($items as $group_key=>$grouped_items){
			$unique_blog_ids[$grouped_items[0]['blog_id']] = $grouped_items[0]['blog_id'];
		}}
		/**
			recreate groupedItems array for easy re-sorting
			loop through blog ids to get category sortorder from blog id plus menu sort order set for menu item
		**/
		$groupedItems = array();
		foreach($unique_blog_ids as $bid){
			if(is_multisite() && $bid != $blog_id){
				switch_to_blog($bid);
					$wppizza_blog_options = get_option(WPPIZZA_SLUG,0);
					$category_sortorder = $wppizza_blog_options['layout']['category_sort_hierarchy'];
					$blog_name = get_bloginfo('name') ;
					/* items that belong to this blog */
					if(isset($items) && is_array($items)){
					foreach($items as $group_key=>$grouped_items){
						if($grouped_items[0]['blog_id'] == $bid){
							$groupedItems[$group_key]['blog_id'] = $grouped_items[0]['blog_id'];
							$groupedItems[$group_key]['cat_id_sort'] = isset($category_sortorder[$grouped_items[0]['blog_id']][$grouped_items[0]['cat_id_selected']]) ? $category_sortorder[$grouped_items[0]['cat_id_selected']] : $grouped_items[0]['cat_id_selected'];
							$groupedItems[$group_key]['post_id'] = $grouped_items[0]['post_id'];
							$groupedItems[$group_key]['title'] = $grouped_items[0]['sortname'];
							$groupedItems[$group_key]['size'] = $grouped_items[0]['size'];
							$groupedItems[$group_key]['post_date'] = get_post_field( 'post_date',$grouped_items[0]['post_id'], 'raw');
							$groupedItems[$group_key]['menu_order'] = get_post_field( 'menu_order',$grouped_items[0]['post_id'], true);
							$groupedItems[$group_key]['blog_name'] = $blog_name;
						}
					}}
				restore_current_blog();
			}else{
				$category_sortorder = $wppizza_options['layout']['category_sort_hierarchy'];
				$blog_name = get_bloginfo('name') ;
					/* items that belong to this blog */
					if(isset($items) && is_array($items)){
					foreach($items as $group_key=>$grouped_items){
						if($grouped_items[0]['blog_id'] == $bid){
							$groupedItems[$group_key]['blog_id'] = $grouped_items[0]['blog_id'];
							$groupedItems[$group_key]['cat_id_sort'] = isset($category_sortorder[$grouped_items[0]['blog_id']][$grouped_items[0]['cat_id_selected']]) ? $category_sortorder[$grouped_items[0]['cat_id_selected']] : $grouped_items[0]['cat_id_selected'];
							$groupedItems[$group_key]['post_id'] = $grouped_items[0]['post_id'];
							$groupedItems[$group_key]['title'] = $grouped_items[0]['sortname'];
							$groupedItems[$group_key]['size'] = $grouped_items[0]['size'];
							$groupedItems[$group_key]['post_date'] = get_post_field( 'post_date',$grouped_items[0]['post_id'], 'raw');
							$groupedItems[$group_key]['menu_order'] = get_post_field( 'menu_order',$grouped_items[0]['post_id'], true);
							$groupedItems[$group_key]['blog_name'] = $blog_name;
						}
					}}
			}
		}

		/* multisort sort array*/
		$multisort_sort = array();
		foreach($groupedItems as $k=>$v) {
    		$multisort_sort['blog_id'][$k] = $v['blog_id'];
    		$multisort_sort['cat_id_sort'][$k] = $v['cat_id_sort'];
    		$multisort_sort['post_id'][$k] = $v['post_id'];
    		$multisort_sort['post_date'][$k] = $v['post_date'];
    		$multisort_sort['menu_order'][$k] = $v['menu_order'];
    		$multisort_sort['title'][$k] = $v['title'];
    		$multisort_sort['size'][$k] = $v['size'];
    		$multisort_sort['blog_name'][$k] = $v['blog_name'];
		}
		$sort_flags = array();
		$sort_flags['blog_id'] = SORT_ASC;
		$sort_flags['cat_id_sort'] = SORT_ASC;
		$sort_flags['post_id'] = ($wppizza_options[$this->layout_page]['items_sort_orderby'] == 'ID' &&  $wppizza_options[$this->layout_page]['items_sort_order'] == 'DESC') ? SORT_DESC : SORT_ASC;
		$sort_flags['post_date'] = ($wppizza_options[$this->layout_page]['items_sort_orderby'] == 'date' &&  $wppizza_options[$this->layout_page]['items_sort_order'] == 'DESC') ? SORT_DESC : SORT_ASC;
		$sort_flags['menu_order'] = ($wppizza_options[$this->layout_page]['items_sort_orderby'] == 'menu_order' &&  $wppizza_options[$this->layout_page]['items_sort_order'] == 'DESC') ? SORT_DESC : SORT_ASC;
		$sort_flags['title'] = ($wppizza_options[$this->layout_page]['items_sort_orderby'] == 'title' &&  $wppizza_options[$this->layout_page]['items_sort_order'] == 'DESC') ? SORT_DESC : SORT_ASC;
		$sort_flags['size'] = SORT_ASC;
		$sort_flags['blog_name'] = SORT_ASC;

		/* set sorting as required */
		if($wppizza_options[$this->layout_page]['items_sort_orderby'] == 'menu_order'){/* by order set */
			array_multisort(
				$multisort_sort['blog_id'], $sort_flags['blog_id'],
				$multisort_sort['cat_id_sort'], $sort_flags['cat_id_sort'],
				$multisort_sort['menu_order'], $sort_flags['menu_order'],
				$multisort_sort['title'], $sort_flags['title'],
				$multisort_sort['size'], $sort_flags['size'],
				$groupedItems
			);
		}
		if($wppizza_options[$this->layout_page]['items_sort_orderby'] == 'title'){/* by title */
			array_multisort(
				$multisort_sort['blog_id'], $sort_flags['blog_id'],
				$multisort_sort['cat_id_sort'], $sort_flags['cat_id_sort'],
				$multisort_sort['title'], $sort_flags['title'],
				$multisort_sort['size'], $sort_flags['size'],
				$groupedItems
			);
		}
		if($wppizza_options[$this->layout_page]['items_sort_orderby'] == 'ID'){/* by id */
			array_multisort(
				$multisort_sort['blog_id'], $sort_flags['blog_id'],
				$multisort_sort['cat_id_sort'], $sort_flags['cat_id_sort'],
				$multisort_sort['post_id'], $sort_flags['post_id'],
				$groupedItems
			);
		}
		if($wppizza_options[$this->layout_page]['items_sort_orderby'] == 'date'){/* by date */
			array_multisort(
				$multisort_sort['blog_id'], $sort_flags['blog_id'],
				$multisort_sort['cat_id_sort'], $sort_flags['cat_id_sort'],
				$multisort_sort['post_date'], $sort_flags['post_date'],
				$multisort_sort['title'], $sort_flags['title'],
				$multisort_sort['size'], $sort_flags['size'],
				$groupedItems
			);
		}

	return $groupedItems;
	}

	/*********************************************************************
	*
	* 	[set menu item sort order by title/ID/order etc ASC|DESC]
	*
	**********************************************************************/
	function filter_loop_args($args){
		global $wppizza_options;
			/* set order by */
			$args['orderby'] 		=  !empty($wppizza_options[$this->layout_page]['items_sort_orderby']) ? $wppizza_options[$this->layout_page]['items_sort_orderby'] :  $args['orderby'] ;
			/* set order */
			$args['order'] 		=  !empty($wppizza_options[$this->layout_page]['items_sort_order']) ? $wppizza_options[$this->layout_page]['items_sort_order'] :  $args['order'] ;

		return $args;
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
	function admin_options_settings($settings, $sections, $fields, $inputs, $help){


		/********************************
		*	[Item Sorting and Categories Display]
		********************************/
		/*sections*/
		if($sections){
			$settings['sections'][$this->layout_section] = __('Items Sorting and Category Display', 'wppizza-admin');
		}
		/*fields*/
		if($fields){
			$field = 'items_sort_orderby';
			$settings['fields'][$this->layout_section][$field] = array(__('Menu items sort order', 'wppizza-admin'), array(
				'value_key'=>$field,
				'option_key'=>$this->layout_page,
				'label'=>__('How are menu items sorted within a category', 'wppizza-admin'),
				'description'=>array()
			));
			$field = 'omit_empty_categories';
			$settings['fields'][$this->layout_section][$field] = array(__('Do not display categories that have no menu items', 'wppizza-admin'), array(
				'value_key'=>$field,
				'option_key'=>$this->layout_page,
				'label'=>__('will not display categories that have no menu items associated with them instead of "no results found"', 'wppizza-admin'),
				'description'=>array()
			));
			$field = 'items_group_sort_print_by_category';
			$settings['fields'][$this->layout_section][$field] = array(__('Group, sort and display menu items by category', 'wppizza-admin'), array(
				'value_key'=>$field,
				'option_key'=>$this->layout_page,
				'label'=>__('sorts by and displays categories in cart, order page and emails', 'wppizza-admin'),
				'description'=>array()
			));
			$field = 'items_category_hierarchy';
			$settings['fields'][$this->layout_section][$field] = array(__('Category display in order pages and emails', 'wppizza-admin'), array(
				'value_key'=>$field,
				'option_key'=>$this->layout_page,
				'label'=>__('How would you like to display the categories in order pages and emails ? [only relevant in hierarchical category structure]', 'wppizza-admin'),
				'description'=>array()
			));
			$field = 'items_category_hierarchy_cart';
			$settings['fields'][$this->layout_section][$field] = array(__('Category display in cart', 'wppizza-admin'), array(
				'value_key'=>$field,
				'option_key'=>$this->layout_page,
				'label'=>__('How would you like to display the categories in the cart ? [as the cart might have space restrictions you can adjust this separately]', 'wppizza-admin'),
				'description'=>array()
			));
			$field = 'items_category_hierarchy_email_style';
			$settings['fields'][$this->layout_section][$field] = array(__('Category style in html emails', 'wppizza-admin'), array(
				'value_key'=>$field,
				'option_key'=>$this->layout_page,
				'label'=>__('Some email services do not understand (or delete) css declarations, therefore please enter the distinct style declarations here', 'wppizza-admin'),
				'description'=>array()
			));
			$field = 'items_category_separator';
			$settings['fields'][$this->layout_section][$field] = array(__('Category Separator', 'wppizza-admin'), array(
				'value_key'=>$field,
				'option_key'=>$this->layout_page,
				'label'=>'',
				'description'=>array()
			));
		}

		return $settings;
	}
	/****************************************************************
	*
	*	[output option fields]
	*	@since 3.0
	*	@return array()
	*
	****************************************************************/
	function admin_options_fields($wppizza_options, $options_key, $field, $label, $description){


		if($field=='items_sort_orderby'){
			print'<label>';
				print "<select name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' />";
					print"<option value='menu_order' ".selected($wppizza_options[$options_key][$field],"menu_order",false).">".__('Default (set order attribute)', 'wppizza-admin')."</option>";
					print"<option value='title' ".selected($wppizza_options[$options_key][$field],"title",false).">".__('Title', 'wppizza-admin')."</option>";
					print"<option value='ID' ".selected($wppizza_options[$options_key][$field],"ID",false).">".__('ID', 'wppizza-admin')."</option>";
					print"<option value='date' ".selected($wppizza_options[$options_key][$field],"date",false).">".__('Date', 'wppizza-admin')."</option>";
				print "</select>";
				print "<select name='".WPPIZZA_SLUG."[".$options_key."][items_sort_order]' />";
					print"<option value='ASC' ".selected($wppizza_options[$options_key]['items_sort_order'],"ASC",false).">".__('Ascending', 'wppizza-admin')."</option>";
					print"<option value='DESC' ".selected($wppizza_options[$options_key]['items_sort_order'],"DESC",false).">".__('Descending', 'wppizza-admin')."</option>";
				print "</select>";
				print'' . $label . '';
			print'</label>';
			print'' . $description . '';
		}

		if($field=='items_group_sort_print_by_category'){
			print'<label>';
				print "<input id='".$field."' name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' type='checkbox'  ". checked($wppizza_options[$options_key][$field],true,false)." value='1' />";
				print'' . $label . '';
			print'</label>';
			print'' . $description . '';
		}

		if($field=='omit_empty_categories'){
			print'<label>';
				print "<input id='".$field."' name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' type='checkbox'  ". checked($wppizza_options[$options_key][$field],true,false)." value='1' />";
				print'' . $label . '';
			print'</label>';
			print'' . $description . '';
		}

		if($field=='items_category_hierarchy'){
			print'';
				print'' . $label . '<br /><br />';
				print "<label>".__('full path', 'wppizza-admin')." <input name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' type='radio'  ".checked($wppizza_options[$options_key][$field],'full',false)." value='full' /> </label>";
				print "<label>".__('parent category', 'wppizza-admin')." <input name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' type='radio'  ".checked($wppizza_options[$options_key][$field],'parent',false)." value='parent' /> </label>";
				print "<label>".__('topmost category', 'wppizza-admin')." <input name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' type='radio'  ".checked($wppizza_options[$options_key][$field],'topmost',false)." value='topmost' /> </label>";
			print'';
			print'' . $description . '';

		}

		if($field=='items_category_hierarchy_cart'){
			print'';
				print'' . $label . '<br /><br />';

				print "<label>".__('do not display categories', 'wppizza-admin')." <input name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' type='radio'  ".checked($wppizza_options[$options_key][$field],'none',false)." value='none' /> </label>";
				print "<label>".__('full path', 'wppizza-admin')." <input name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' type='radio'  ".checked($wppizza_options[$options_key][$field],'full',false)." value='full' /> </label>";
				print "<label>".__('parent category', 'wppizza-admin')." <input name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' type='radio'  ".checked($wppizza_options[$options_key][$field],'parent',false)." value='parent' /> </label>";
				print "<label>".__('topmost category', 'wppizza-admin')." <input name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' type='radio'  ".checked($wppizza_options[$options_key][$field],'topmost',false)." value='topmost' /> </label>";
			print'';
			print'' . $description . '';

		}

		if($field=='items_category_hierarchy_email_style'){
			print'<label>';
				echo "<input name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' size='75' type='text'  value='".$wppizza_options[$options_key][$field]."' />";
				print'' . $label . '';
			print'</label>';
			print'' . $description . '';

		}

		if($field=='items_category_separator'){
			print'<label>';
				echo "<input name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' size='2' type='text'  value='".$wppizza_options[$options_key][$field]."' />";
				print'' . $label . '';
			print'</label>';
			print'' . $description . '';

		}
	}
	/****************************************************************
	*
	*	[insert default option on install]
	*	$parameter $options array() | filter passing on filtered options
	*	@since 3.0
	*	@return array()
	*
	****************************************************************/
	function options_default($options){
		$options[$this->layout_page]['items_group_sort_print_by_category'] = false;
		$options[$this->layout_page]['omit_empty_categories'] = true;
		$options[$this->layout_page]['items_sort_order'] = 'ASC';
		$options[$this->layout_page]['items_sort_orderby'] = 'menu_order';
		$options[$this->layout_page]['items_category_hierarchy'] = 'full';
		$options[$this->layout_page]['items_category_hierarchy_cart'] = 'parent';
		$options[$this->layout_page]['items_category_hierarchy_email_style'] = 'border-bottom:1px dotted #cecece; padding:12px 2px 7px 2px;';
		$options[$this->layout_page]['items_category_separator'] = ' &raquo; ';

	return $options;
	}
	/****************************************************************
	*
	*	[validate options on save/update]
	*	@since 3.0
	*	@return array()
	*
	****************************************************************/
	function options_validate($options, $input){
		/**make sure we get the full array on install/update**/
		if ( empty( $_POST['_wp_http_referer'] ) ) {
			return $input;
		}
		if(isset($_POST[''.WPPIZZA_SLUG.'_'.$this->layout_page.''])){
			$options[$this->layout_page]['items_group_sort_print_by_category'] = !empty($input[$this->layout_page]['items_group_sort_print_by_category']) ? true : false;
			$options[$this->layout_page]['omit_empty_categories'] = !empty($input[$this->layout_page]['omit_empty_categories']) ? true : false;
			$options[$this->layout_page]['items_sort_order'] = (in_array($input[$this->layout_page]['items_sort_order'],array('ASC','DESC'))) ? $input[$this->layout_page]['items_sort_order'] : 'ASC';
			$options[$this->layout_page]['items_sort_orderby'] = (in_array($input[$this->layout_page]['items_sort_orderby'],array('menu_order','title','ID','date'))) ? $input[$this->layout_page]['items_sort_orderby'] : 'menu_order';
			$options[$this->layout_page]['items_category_hierarchy'] = preg_replace("/[^a-z]/","",$input[$this->layout_page]['items_category_hierarchy']);
			$options[$this->layout_page]['items_category_hierarchy_cart'] = preg_replace("/[^a-z]/","",$input[$this->layout_page]['items_category_hierarchy_cart']);
			$options[$this->layout_page]['items_category_hierarchy_email_style']=wppizza_validate_string($input[$this->layout_page]['items_category_hierarchy_email_style']);
			$options[$this->layout_page]['items_category_separator']=wppizza_validate_string($input[$this->layout_page]['items_category_separator']);
		}
		return $options;
	}
}
/***************************************************************
*
*	[ini]
*
***************************************************************/
$WPPIZZA_MODULE_ITEMS_SORTING_CATEGORY_DISPLAY = new WPPIZZA_MODULE_ITEMS_SORTING_CATEGORY_DISPLAY();
?>