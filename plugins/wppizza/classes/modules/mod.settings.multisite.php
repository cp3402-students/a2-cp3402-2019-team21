<?php
/**
* WPPIZZA_MODULE_SETTINGS_MULTISITE Class
*
* @package     WPPIZZA
* @subpackage  WPPIZZA_MODULE_SETTINGS_MULTISITE
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
class WPPIZZA_MODULE_SETTINGS_MULTISITE{

	private $settings_page = 'settings';/* which admin subpage (identified there by this->class_key) are we adding this to */
	private $layout_page = 'layout';/* which admin subpage (identified there by this->class_key) are we adding this to */

	private $layout_section = 'layout-itemsorting-categories';/* splicing it into a specific section adding fields */


	private $section_key = 'multisite';/* must be unique */

	private $items_keys_blog_details = array(); /* keys of menu items that should have the blogname name added before them */


	function __construct() {
		/**********************************************************
			[add settings to admin]
		***********************************************************/
		if(is_admin()){

			if(is_multisite()){

				/* add admin options settings page*/
				add_filter('wppizza_filter_settings_sections_'.$this->settings_page.'', array($this, 'admin_options_settings'), 30, 5);
				/* add admin options settings page fields */
				add_action('wppizza_admin_settings_section_fields_'.$this->settings_page.'', array($this, 'admin_options_fields_settings'), 10, 5);

				/* add admin options layout page*/
				add_filter('wppizza_filter_settings_sections_'.$this->layout_page.'', array($this, 'admin_options_layout'), 1000, 5);/* priority must be same or higher than cat items display (at 50) */
				/* add admin options layout page fields */
				add_action('wppizza_admin_settings_section_fields_'.$this->layout_page.'', array($this, 'admin_options_fields_layout'), 10, 5);
			}

			/**add default options, regardless of if it's a multisite setup or not **/
			add_filter('wppizza_filter_setup_default_options', array( $this, 'options_default'));
			/**validate options**/
			add_filter('wppizza_filter_options_validate', array( $this, 'options_validate'), 10, 2 );
		}
		/**********************************************************
			[filter/actions depending on settings]
		***********************************************************/
		if(is_multisite()){
			/* sessions per site ? default true */
			add_filter( 'wppizza_filter_session_per_site', array( $this, 'sessions_per_site' ));
			/* order history all sites (parent only)? default false */
			add_filter( 'wppizza_filter_order_history_all_sites', array( $this, 'order_history_all_sites' ));
			/* reports all sites (parent only) ? default false */
			add_filter( 'wppizza_filter_reports_all_sites', array( $this, 'reports_all_sites' ));


			/* add parameters blog name for cart/session and order variables*/
			add_filter('wppizza_filter_cart_items_from_session', array( $this, 'get_blogname_for_cart_email_order'));
			add_filter('wppizza_filter_order_items_markup', array( $this, 'get_blogname_for_cart_email_order'));
			add_filter('wppizza_filter_email_items_markup', array( $this, 'get_blogname_for_cart_email_order'));


			/* add blog name in cart */
			add_filter('wppizza_filter_cart_item_markup', array( $this, 'show_blogname_in_cart'), 10, 3 );
			/* add blog name in order */
			add_filter('wppizza_filter_order_item_markup', array( $this, 'show_blogname_in_order'), 10, 5 );
			/* add blog name in html emails */
			add_filter('wppizza_filter_templates_item_markup_html', array( $this, 'show_blogname_in_html_templates'), 10, 5 );
			/* add blog name in plaintext emails */
			add_filter('wppizza_filter_templates_item_markup_plaintext', array( $this, 'show_blogname_in_plaintext_templates'), 10, 3 );

			/* add variables for print/email templates */
			add_filter('wppizza_filter_site_details_formatted', array( $this, 'parent_site_details_formatted'), 10, 3 );

		}
	}

	/*******************************************************************************************************************************************************
	*
	*
	*
	* 	[ filters]
	*
	*
	*
	********************************************************************************************************************************************************/
	/*
		add parent site vars to formatted details
		only runs when multisite
	*/
	function parent_site_details_formatted($site_parameters , $order, $tpl_args = false){

		/* skip if there's nothing to do in the first place */
		if(empty($order['blog_info']) &&  empty($tpl_args)){
			return $site_parameters;
		}
		/*
			if we have a multisite setup , check if blog id of order is
			multisite parent site . i.e the same.
			If it is NOT  get parent sites details
		*/
		if(empty($tpl_args) && $order['blog_info']['blog_id'] != BLOG_ID_CURRENT_SITE){
			$has_parent_blog = true;
			$parent_blog = (array) get_blog_details(BLOG_ID_CURRENT_SITE);
		}

		if(!empty($has_parent_blog) || !empty($tpl_args)){

			/*
				parent site name
			*/
			if(!empty($tpl_args)){
				$site_parameters['parent_site_name']['template_default_sort']= 10;
				$site_parameters['parent_site_name']['template_default_enabled']= false;
				$site_parameters['parent_site_name']['template_parameter']	= true;
				$site_parameters['parent_site_name']['template_row_default_css'] = '';
			}else{
				$site_parameters['parent_site_name']['class_ident'] = 'parent-site-name';
				$site_parameters['parent_site_name']['value'] = $parent_blog['blogname'];
				$site_parameters['parent_site_name']['value_formatted'] = $parent_blog['blogname'];
			}
			$site_parameters['parent_site_name']['label'] = __('parent site name (if different)','wppizza-admin');
			/*
				parent site url
			*/
			if(!empty($tpl_args)){
				$site_parameters['parent_site_url']['template_default_sort']= 20;
				$site_parameters['parent_site_url']['template_default_enabled']= false;
				$site_parameters['parent_site_url']['template_parameter']	= true;
				$site_parameters['parent_site_url']['template_row_default_css'] = '';
			}else{
				$site_parameters['parent_site_url']['class_ident'] = 'parent-site-url';
				$site_parameters['parent_site_url']['value'] =  $parent_blog['siteurl'];
				$site_parameters['parent_site_url']['value_formatted'] = $parent_blog['siteurl'];
			}
			$site_parameters['parent_site_url']['label'] = __('parent site url (if different)','wppizza-admin');
			/*
				parent site id - commented as most likely unnecessary
			*/
			//if(!empty($tpl_args)){
			//	$site_parameters['site_id']['template_default_sort']= 30;
			//	$site_parameters['site_id']['template_default_enabled']= false;
			//	$site_parameters['site_id']['template_parameter']	= true;
			//	$site_parameters['site_id']['template_row_default_css'] = '';
			//}else{
			//	$site_parameters['site_id']['class_ident'] = 'site-id';
			//	$site_parameters['site_id']['value'] = $parent_blog -> site_id;
			//	$site_parameters['site_id']['value_formatted'] = $parent_blog -> site_id;
			//}
			//$site_parameters['site_id']['label'] = __('parent site id','wppizza-admin');
			/*
				blog id - commented as most likely unnecessary
			*/
			//if(!empty($tpl_args)){
			//	$site_parameters['blog_id']['template_default_sort']= 40;
			//	$site_parameters['blog_id']['template_default_enabled']= false;
			//	$site_parameters['blog_id']['template_parameter']	= true;
			//	$site_parameters['blog_id']['template_row_default_css'] = '';
			//}else{
			//	$site_parameters['blog_id']['class_ident'] = 'blog-id';
			//	$site_parameters['blog_id']['value'] = $parent_blog -> blog_id;
			//	$site_parameters['blog_id']['value_formatted'] = $parent_blog -> blog_id;
			//}
			//$site_parameters['blog_id']['label'] = __('parent blog id','wppizza-admin');
		}

	return $site_parameters;
	}
	/*********************************************************************
	*
	* 	[markup]
	*
	**********************************************************************/
	/*
		add blogname to plaintext email markup
	*/
	function show_blogname_in_plaintext_templates($markup_item, $key, $item){
		global $wppizza_options;
		/* skip if not enabled */
		if(empty($wppizza_options[$this->layout_page]['items_group_sort_print_by_category'])){
			return $markup_item;
		}

		if(!empty($wppizza_options[$this->layout_page]['items_blog_hierarchy'])){
		if(isset($this -> items_keys_blog_details[$key])){
			$prepend_blog_name = apply_filters('wppizza_filter_plaintext_line', $this -> items_keys_blog_details[$key]['blogname'], ' ', true) ;
			$markup_item = PHP_EOL . $prepend_blog_name . $markup_item ;
		}}

	return $markup_item;
	}


	/*
		add blogname to html email markup
	*/
	function show_blogname_in_html_templates($markup_item, $key, $item, $items, $colspan){
		global $wppizza_options;
		/* skip if not enabled */
		if(empty($wppizza_options[$this->layout_page]['items_group_sort_print_by_category'])){
			return $markup_item;
		}

		if(!empty($wppizza_options[$this->layout_page]['items_blog_hierarchy'])){
		if(isset($this -> items_keys_blog_details[$key])){
			$style = $wppizza_options[$this->layout_page]['items_blog_hierarchy_email_style'];
			$prepend_blog_name = '<tr class="'.WPPIZZA_SLUG.'-item-blog-'.$this -> items_keys_blog_details[$key]['blog_id'].'" class="'.WPPIZZA_SLUG.'-item-blogname"><td colspan="'.$colspan.'" style="'.$style.'">'.$this -> items_keys_blog_details[$key]['blogname'].'</td></tr>';
			array_unshift($markup_item, $prepend_blog_name);
		}}

	return $markup_item;
	}


	/*
		add blogname to order markup (checkout page, confirmation page, thank you page, users order history)
	*/
	function show_blogname_in_order($markup_item, $key, $item, $cart, $colspan ){
		global $wppizza_options;
		/* skip if not enabled */
		if(empty($wppizza_options[$this->layout_page]['items_group_sort_print_by_category'])){
			return $markup_item;
		}

		if(!empty($wppizza_options[$this->layout_page]['items_blog_hierarchy'])){
		if(isset($this -> items_keys_blog_details[$key])){
			$prepend_blog_name = '<tr><td id="'.WPPIZZA_SLUG.'-item-blog-'.$this -> items_keys_blog_details[$key]['blog_id'].'" class="'.WPPIZZA_SLUG.'-item-blogname" colspan="'.$colspan.'">'.$this -> items_keys_blog_details[$key]['blogname'].'</td></tr>';
			array_unshift($markup_item, $prepend_blog_name);
		}}

	return $markup_item;
	}


	/*
		add blogname to cart markup
	*/
	function show_blogname_in_cart($markup_item, $key, $item){
		global $wppizza_options;

		/* skip if not enabled */
		if(empty($wppizza_options[$this->layout_page]['items_group_sort_print_by_category'])){
			return $markup_item;
		}

		if(!empty($wppizza_options[$this->layout_page]['items_blog_hierarchy_cart'])){
		if(isset($this -> items_keys_blog_details[$key])){
			$prepend_blog_name = '<li class="'.WPPIZZA_SLUG.'-item-blog-'.$this -> items_keys_blog_details[$key]['blog_id'].'" class="'.WPPIZZA_SLUG.'-item-blogname">'.$this -> items_keys_blog_details[$key]['blogname'].'</li>';
			array_unshift($markup_item, $prepend_blog_name);
		}}

	return $markup_item;
	}



	/*********************************************************************
	*
	* 	[add get item keys that should have blogname added before them (will automatically be before category too if displayed)]
	*
	**********************************************************************/
	function get_blogname_for_cart_email_order($items){
		static $run_once = 0; $run_once++;
		if($run_once>1){return $items;}

		global $wppizza_options;
		/* skip if not enabled */
		if(empty($wppizza_options[$this->layout_page]['items_group_sort_print_by_category'])){
			return $items;
		}

		$add_blog_details = array();
		foreach($items as $key => $item){
			$item_blog_id = $item['blog_id'];
			if(!isset($do_blog_name[$item_blog_id])){
				$do_blog_name[$item_blog_id] = true;
				$add_blog_details[$key] = WPPIZZA() -> helpers -> wppizza_blog_details($item_blog_id);
				//$add_blog_name[$key] =  array('blog_id' => $item_blog_id, 'blog_name' => $blog_details->blogname );
			}
		}

		/* all menu item keys as keys that need blog name displayed */
		$this -> items_keys_blog_details = $add_blog_details;

	return $items;
	}



	/*
		sessions per site ? default true. i.e WPPIZZA_SLUG_'.$blog_id.'
	*/
	function sessions_per_site($session_key){
		global $blog_id, $wppizza_options;

		if(!is_multisite()){
			return $session_key;
		}
		/**
			always get settings from parent blog for this if it's not already parent anyway
		**/
		if($blog_id != BLOG_ID_CURRENT_SITE){
			switch_to_blog(BLOG_ID_CURRENT_SITE);
			/* get option from main parent blog */
			$blog_options = get_option(WPPIZZA_SLUG);

			/*
				if wppizza not activated in parent / master site to start off with, use default
			*/

			/*
				is_plugin_active only exists in admin, so lets simply recreate it, checking if plugin is active in master site
			*/
			$wppizza_master_active =  in_array( WPPIZZA_PLUGIN_INDEX, (array) get_option( 'active_plugins', array() ) );
			if(!isset($blog_options[$this->settings_page]['wp_multisite_session_per_site']) || !$wppizza_master_active){
			 	$session_key = $session_key ;
			}else{
				if(empty($blog_options[$this->settings_page]['wp_multisite_session_per_site'])){
					$session_key = WPPIZZA_SLUG;
				}
			}
			restore_current_blog();
		}else{
			if(empty($wppizza_options[$this->settings_page]['wp_multisite_session_per_site'])){
				$session_key = WPPIZZA_SLUG;
			}
		}
	return $session_key;
	}

	/*************************************

		query db - admin history - from all sites (parent sie only)

	**************************************/
	function order_history_all_sites($bool){
		global $wppizza_options, $blog_id;
		$bool = ( is_multisite() && $blog_id==BLOG_ID_CURRENT_SITE && !empty($wppizza_options[$this->settings_page]['wp_multisite_order_history_all_sites'])) ? true : false;
	return $bool;
	}
	/*************************************

		query db - admin history from all sites (parent sie only)

	**************************************/
	function reports_all_sites($bool){
		global $wppizza_options, $blog_id;
		$bool = ( is_multisite() && $blog_id==BLOG_ID_CURRENT_SITE && !empty($wppizza_options[$this->settings_page]['wp_multisite_reports_all_sites'])) ? true : false;
	return $bool;
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
		global $blog_id;
		/* in multisite setup and parentsite only */
		if(is_multisite() && $blog_id==BLOG_ID_CURRENT_SITE){
			/*section*/
			if($sections){
				$settings['sections'][$this->section_key] =  __('Multisite', 'wppizza-admin');
			}
			/*help*/
			if($help){
				$settings['help'][$this->section_key][] = array(
					'label'=>__('Multisite environment', 'wppizza-admin'),
					'description'=>array(
						__('Several options are available in a multisite environment that will allow you to - for example - administer and/or view certain aspects of all sites in the parent site.', 'wppizza-admin'),
						__('Please adjust settings as appropriate according to the information provided next to each individual option.', 'wppizza-admin')
					)
				);
			}

			/*fields*/
			if($fields){

				$field = 'wp_multisite_session_per_site';
				$settings['fields'][$this->section_key][$field] = array( __('Cart - per (sub)site', 'wppizza-admin'), array(
					'value_key'=>$field,
					'option_key'=>$this->settings_page,
					'label'=>__('Sets cart contents / order on a per site basis when using subdirectories. Turning this off has no effect/relevance when using different domains per site on the network (it will alwasy be per site in that case). You probably want this *on* when you have a multisite/network install.', 'wppizza-admin'),
					'description'=>array(
						'<span class="wppizza-highlight">'.__('THERE ARE ONLY VERY FEW SECENARIOS WHERE YOU MIGHT WANT THIS OFF', 'wppizza-admin').'</span>'
					)
				));

				$field = 'wp_multisite_order_history_all_sites';
				$settings['fields'][$this->section_key][$field] = array( __('Orders/Customers (Admin) - all subsites', 'wppizza-admin'), array(
					'value_key'=>$field,
					'option_key'=>$this->settings_page,
					'label'=>sprintf(__('check to have order history/customers to use all orders/customers of all child sites (%s -> Order History / %s -> Customers ) ', 'wppizza-admin'), WPPIZZA_NAME, WPPIZZA_NAME),
					'description'=>array(
						'<span class="wppizza-highlight">'.__('NOTE: THIS MIGHT SLOW THINGS DOWN IN THE ADMIN ORDER HISTORY PAGE OF YOUR MAIN/PARENT SITE CONSIDERABLY', 'wppizza-admin').'</span>'
					)
				));

				$field = 'wp_multisite_reports_all_sites';
				$settings['fields'][$this->section_key][$field] = array( __('Reports (Admin) - all subsites', 'wppizza-admin'), array(
					'value_key'=>$field,
					'option_key'=>$this->settings_page,
					'label'=>sprintf(__('check to have reporting - including dashboard widget - to use all orders of all child sites (%s -> Reports)', 'wppizza-admin'), WPPIZZA_NAME),
					'description'=>array(
						'<span class="wppizza-highlight">'.__('NOTE: THIS MIGHT SLOW THINGS DOWN IN THE ADMIN ORDER HISTORY PAGE OF YOUR MAIN/PARENT SITE CONSIDERABLY', 'wppizza-admin').'</span>'
					)
				));

			}
		}

		return $settings;
	}
	/*------------------------------------------------------------------------------
	#	[output option fields - setting page]
	#	@since 3.0
	#	@return array()
	------------------------------------------------------------------------------*/
	function admin_options_fields_settings($wppizza_options, $options_key, $field, $label, $description){

		if($field=='wp_multisite_session_per_site'){
			echo "<label>";
				echo "<input id='".$field."' name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' type='checkbox'  ". checked($wppizza_options[$options_key][$field],true,false)." value='1' />";
				echo "" . $label . "";
			echo "</label>";
			echo"".$description."";
		}

		if($field=='wp_multisite_reports_all_sites'){
			echo "<label>";
				echo "<input id='".$field."' name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' type='checkbox'  ". checked($wppizza_options[$options_key][$field],true,false)." value='1' />";
				echo "" . $label . "";
			echo "</label>";
			echo"".$description."";
		}

		if($field=='wp_multisite_order_history_all_sites'){
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

		/*skip section as we are adding to the "Items Sorting and Category Display" */

		/*fields*/
		if($fields){
			$add_settings = array();

			$field = 'items_blog_hierarchy';
			$add_settings['fields'][$this->layout_section][$field] = array( __('Blogname display in order pages and emails', 'wppizza-admin'), array(
				'value_key'=>$field,
				'option_key'=>$this->layout_page,
				'label'=>__('Enable to also sort by and show blogname additionally to categories in order pages and emails', 'wppizza-admin'),
				'description'=>array()
			));

			$field = 'items_blog_hierarchy_cart';
			$add_settings['fields'][$this->layout_section][$field] = array( __('Blogname display in cart', 'wppizza-admin'), array(
				'value_key'=>$field,
				'option_key'=>$this->layout_page,
				'label'=>__('Enable to also sort by and show blogname additionally to categories in cart', 'wppizza-admin'),
				'description'=>array()
			));

			$field = 'items_blog_hierarchy_email_style';
			$add_settings['fields'][$this->layout_section][$field] = array( __('Blogname style in html emails', 'wppizza-admin'), array(
				'value_key'=>$field,
				'option_key'=>$this->layout_page,
				'label'=>__('Some email services do not understand (or delete) css declarations, therefore please enter the distinct style declarations here', 'wppizza-admin'),
				'description'=>array()
			));

		}

		/*
			splice fields into the required position on admin layout page
		*/
		if(!empty($add_settings)){
			if($fields){
				$settings['fields'][$this->layout_section] = wppizza_array_splice(
					$settings['fields'][$this->layout_section],
					$add_settings['fields'][$this->layout_section],
					/* array keys here are the keys after which the above settings will be inserted - there must be as many keys as there are $add_settings*/
					array('items_category_hierarchy','items_category_hierarchy_cart','items_category_hierarchy_email_style'),
					true
				);
			}
		}

	return $settings;
	}
	/****************************************************************
	*	[output option fields - layout page]
	*	@since 3.0
	*	@return array()
	****************************************************************/
	function admin_options_fields_layout($wppizza_options, $options_key, $field, $label, $description){

		if($field=='items_blog_hierarchy'){
			print'<label>';
				print "<input id='".$field."' name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' type='checkbox'  ". checked($wppizza_options[$options_key][$field],true,false)." value='1' />";
				print'' . $label . '';
			print'</label>';
			print'' . $description . '';
		}

		if($field=='items_blog_hierarchy_cart'){
			print'<label>';
				print "<input id='".$field."' name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' type='checkbox'  ". checked($wppizza_options[$options_key][$field],true,false)." value='1' />";
				print'' . $label . '';
			print'</label>';
			print'' . $description . '';
		}

		if($field=='items_blog_hierarchy_email_style'){
			print'<label>';
				echo "<input name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' size='75' type='text'  value='".$wppizza_options[$options_key][$field]."' />";
				print'' . $label . '';
			print'</label>';
			print'' . $description . '';

		}

	}




	/*------------------------------------------------------------------------------
	#	[insert default option on install]
	#	$parameter $options array() | filter passing on filtered options
	#	@since 3.0
	#	@return array()
	------------------------------------------------------------------------------*/
	function options_default($options){
		/* settings page */
		$options[$this->settings_page]['wp_multisite_session_per_site'] = true;
		$options[$this->settings_page]['wp_multisite_reports_all_sites'] = false;
		$options[$this->settings_page]['wp_multisite_order_history_all_sites'] = false;
		/* layout page */
		$options[$this->layout_page]['items_blog_hierarchy'] = false;
		$options[$this->layout_page]['items_blog_hierarchy_cart'] = false;
		$options[$this->layout_page]['items_blog_hierarchy_email_style'] = 'text-align:center;padding:12px 2px 7px 2px;font-size:110%; font-weight:bold';
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
			global $blog_id;

			/*if not multisite or not parent site save/use defaults*/
			if(!is_multisite() || $blog_id != BLOG_ID_CURRENT_SITE){
				$options[$this->settings_page]['wp_multisite_session_per_site'] = true;
				$options[$this->settings_page]['wp_multisite_reports_all_sites'] =false;
				$options[$this->settings_page]['wp_multisite_order_history_all_sites'] =false;
				//$options[$this->settings_page]['wp_multisite_order_history_print'] = array('header_from_child'=>false,'multisite_info'=>false);
			}else{
				$options[$this->settings_page]['wp_multisite_session_per_site'] = !empty($input[$this->settings_page]['wp_multisite_session_per_site']) ? true : false;
				$options[$this->settings_page]['wp_multisite_reports_all_sites'] = !empty($input[$this->settings_page]['wp_multisite_reports_all_sites']) ? true : false;
				$options[$this->settings_page]['wp_multisite_order_history_all_sites'] = !empty($input[$this->settings_page]['wp_multisite_order_history_all_sites']) ? true : false;

				/**as we might have enabled or disabled wp_multisite_reports_all_sites , clear transient*/
				delete_transient( WPPIZZA_TRANSIENT_REPORTS_NAME.'_'.WPPIZZA_ADMIN_DASHBOARD_TRANSIENT_REPORTS_EXPIRY.'' );
			}
		}

		/*
			layout
		*/
		if(isset($_POST[''.WPPIZZA_SLUG.'_'.$this->layout_page.''])){

			$options[$this->layout_page]['items_blog_hierarchy'] = ( !empty($input[$this->layout_page]['items_blog_hierarchy']) &&  is_multisite() ) ? true : false;
			$options[$this->layout_page]['items_blog_hierarchy_cart'] = ( !empty($input[$this->layout_page]['items_blog_hierarchy_cart']) &&  is_multisite() )  ? true : false;
			/* only actually overwrite if multisite otherwise keep what we have as the input will not exist in non ms environments*/
			if(is_multisite()){
				$options[$this->layout_page]['items_blog_hierarchy_email_style']= wppizza_validate_string($input[$this->layout_page]['items_blog_hierarchy_email_style']);
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
$WPPIZZA_MODULE_SETTINGS_MULTISITE = new WPPIZZA_MODULE_SETTINGS_MULTISITE();
?>