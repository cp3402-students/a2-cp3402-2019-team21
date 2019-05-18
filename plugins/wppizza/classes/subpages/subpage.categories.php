<?php
/**
* WPPIZZA_CATEGORIES Class
*
* @package     WPPIZZA
* @subpackage  WPPIZZA_CATEGORIES
* @copyright   Copyright (c) 2015, Oliver Bach
* @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
* @since       3.0
*
*/
if ( ! defined( 'ABSPATH' ) ) exit;/*Exit if accessed directly*/


/************************************************************************************************************************
*
*
*	WPPIZZA_CATEGORIES filters
*
*
************************************************************************************************************************/
class WPPIZZA_CATEGORIES{
	/*
	* class ident
	* @var str
	* @since 3.0
	*/
	private $class_key = 'categories';/*to help consistency throughout class in various places*/
	private $submenu_caps_title ;
	private $submenu_priority = 0;
	/******************************************************************************************************************
	*
	*	[CONSTRUCTOR]
	*
	*
	*	@since 3.0
	*
	******************************************************************************************************************/
	function __construct() {
			
		$this->submenu_caps_title=__('Categories','wppizza-admin');
		
		/*register capabilities for this page*/
		add_filter('wppizza_filter_define_caps', array( $this, 'wppizza_filter_define_caps'), $this->submenu_priority);		
				
		if(is_admin()){

			/*when deleting or creating categories do NOT move into "wppizza_current_screen*/
			add_filter('delete_'.WPPIZZA_TAXONOMY.'', array($this,'wppizza_save_sorted_custom_category'),10,3);
			add_action('create_'.WPPIZZA_TAXONOMY.'', array($this,'wppizza_save_sorted_custom_category'),10,2);//runs as ajax call
			add_action('edit_'.WPPIZZA_TAXONOMY.'', array($this,'wppizza_save_sorted_custom_category'),10,2);

			/*add some helper functions once to use their return multiple times */
			add_action('current_screen', array( $this, 'wppizza_current_screen') );	
			
			/** admin ajax **/
			add_action('wppizza_ajax_admin_'.$this->class_key.'', array( $this, 'admin_ajax'));			
					
		}
		/**load admin ajax file**/
		add_action('wp_ajax_wppizza_admin_'.$this->class_key.'_ajax', array($this, 'set_admin_ajax') );		
	}

	/******************
	*	@since 3.0
    *	[admin ajax include file]
    *******************/
	public function set_admin_ajax(){
		require(WPPIZZA_PATH.'ajax/admin.ajax.wppizza.php');
		die();
	}
	/*******************************************************************************************************************************************************
	*
	* 	[admin ajax]
	*
	********************************************************************************************************************************************************/
	function admin_ajax($wppizza_options){	
		/************************************************************************************************************
		*
		*
		*	[save sorted categories]
		*
		*
		************************************************************************************************************/
		if($_POST['vars']['field']=='save_categories_sort'){
			
			/*
				WP might show more categories on a page than set in "Screen options: Number of items per page"
				when there are nested subcategories. So, when paging, we need to chunk it into 3 chunks: before,
				current and after taking into account categories visible, to set sortorder appropriately
			*/
			/*current category sort order*/
			$currrent_sort_hierarchy = $wppizza_options['layout']['category_sort_hierarchy'];
		
		
			/*if we do not have one yet, use default sort order*/
			if(count($currrent_sort_hierarchy)<=0){
				$currrent_sort_hierarchy = WPPIZZA()-> categories -> wppizza_get_cats_hierarchy();
			}
		
		
			/**currently displayed category id's on that particular page (this might only be a part of all cats if paged)*/
			$order = explode(',', $_POST['vars']['order']);
			$order_chunk=array();
			$last_element_in_chunk_id=-1;/*we need to account for new categories being added to a paged page and then drag and drop resorted*/
			foreach ($order as $sort=>$id) {
				$catid=(int)str_replace("tag-","",$id);
				$order_chunk[$catid]=$sort;
				$last_element_in_chunk_sort_id=$currrent_sort_hierarchy[$catid];
			}
		
			/**chunk into before, to sort and after */
			$category_chunks=array();
			foreach($currrent_sort_hierarchy as $key=>$sort){
				if(isset($order_chunk[$key])){
					$category_chunks['sort'][$key]=$order_chunk[$key];
				}
				if(!isset($order_chunk[$key])){
					/*
						accounting for newly added categories on a paged page
						where the actual sortorder would be in a paged page
						BEFORE the current one
					*/
					if($sort<$last_element_in_chunk_sort_id){
						$category_chunks['before'][$key]=$sort;
					}
					/*
						accounting for newly added categories on a paged page
						where the actual sortorder would be in a paged page
						AFTER the current one
					*/
					if($sort>$last_element_in_chunk_sort_id){
						$category_chunks['after'][$key]=$sort;
					}
				}
			}
		
			/***************loop through chunks, only re-sorting cats on current page****************************/
			$new_cat_sort=array();
			$sorter=0;
			/*before current*/
			if(isset($category_chunks['before']) && is_array($category_chunks['before'])){
				//$new_cat_sort+=$category_chunks['before'];
				//$sorter+=count($category_chunks['before']);
				/*safer to loop to make sure we do not have non identical sort ids*/
				foreach($category_chunks['before'] as $catId=>$true){
					$new_cat_sort[$catId]=$sorter;
					$sorter++;
				}
			}
			/*current, to sort*/
			if(isset($category_chunks['sort']) && is_array($category_chunks['sort'])){
				asort($category_chunks['sort']);
				foreach($category_chunks['sort'] as $catId=>$true){
					$new_cat_sort[$catId]=$sorter;
					$sorter++;
				}
			}
		
			/*after current*/
			if(isset($category_chunks['after']) && is_array($category_chunks['after'])){
				//$new_cat_sort+=$category_chunks['after'];
				//$sorter+=count($category_chunks['after']);
				/*safer to loop to make sure we do not have non identical sort ids*/
				foreach($category_chunks['after'] as $catId=>$true){
					$new_cat_sort[$catId]=$sorter;
					$sorter++;
				}
			}
		
			$updateOptions = $wppizza_options;
			$updateOptions['layout']['category_sort_hierarchy']=$new_cat_sort;
		
			update_option( WPPIZZA_SLUG, $updateOptions );
		die(1);
		}	
	}
	/*********************************************************
	*
	*	[add global helpers and enque js]
	*	@since 3.0
	*
	*********************************************************/
	public function wppizza_current_screen($current_screen){		
		if($current_screen->id == 'edit-'.WPPIZZA_TAXONOMY.'' && $current_screen->post_type == WPPIZZA_POST_TYPE && $current_screen->taxonomy == WPPIZZA_TAXONOMY){
			/***enqueue scripts and styles***/
			add_action('admin_enqueue_scripts', array( $this, 'wppizza_enqueue_admin_scripts_and_styles'));
		}
	}
	/*********************************************************
	*
	*	[js]
	*	@since 3.0
	*
	*********************************************************/
    public function wppizza_enqueue_admin_scripts_and_styles($hook) {
    	/**add sortable js*/
    	wp_enqueue_script('jquery-ui-sortable');

    	wp_register_script(WPPIZZA_SLUG.'_'.$this->class_key.'', plugins_url( 'js/scripts.admin.'.$this->class_key.'.js', WPPIZZA_PLUGIN_PATH ), array('jquery'), WPPIZZA_VERSION ,true);
    	wp_enqueue_script(WPPIZZA_SLUG.'_'.$this->class_key.'');
    }


	/*****************************************************
		[save sortorder when creating, editing, deleting categories]
	*****************************************************/
	function wppizza_save_sorted_custom_category( $column, $term_id, $term_obj=null ) {
		global $wppizza_options;
		static $run_once=0;

		if($run_once>0){return;}

		/******************************************
			bypass when activating plugin as we are installing
			already sorted default items via wp_insert_post()
		********************************************/
		if(!isset($_GET['activate'])){

			//$newSort['layout']['category_sort_hierarchy'] = $wppizza_options['layout']['category_sort_hierarchy'];


			/************************************************************************
			*
			*	we are adding,
			*	reorder as required
			*
			**************************************************************************/
			if(!isset($wppizza_options['layout']['category_sort_hierarchy'][$column]) && !empty($_POST['action']) && $_POST['action']=='add-tag'  ){

				/********************************************************
				*	adding new category or sub category
				*	set order on save
				*	as this runs after the term have been updated in the db by wordpress
				*	a simple call to wppizza_get_cats_hierarchy will do
				*********************************************************/


					/**
						if we are adding a parent cat, set to topmost
						as by default it would be sorted by name
					**/
					if(isset($_POST['parent']) && $_POST['parent']==-1){

						/**get current categories sorted */
						$update_cat_sort = WPPIZZA()->categories->wppizza_get_cats_hierarchy($wppizza_options['layout']['category_sort_hierarchy']);

						unset($update_cat_sort[$column]);

						/**resort, with new cat id set to being the first*/
						$new_sorter=0;
						$update_cat_re_sort=array();
						$update_cat_re_sort[$column]=$new_sorter;
						foreach($update_cat_sort as $catId=>$sort){
							$new_sorter++;
							$update_cat_re_sort[$catId]=$new_sorter;
						}
						$update_cat_sort = $update_cat_re_sort;
					}

					/**
						if we are adding a child cat,
						insert after parent
					**/
					if(isset($_POST['parent']) && $_POST['parent']>=0){

						/**resort, with new cat id inserted after it's parent*/
						$new_sorter=0;
						$new_child_cat = $column;
						$child_parent_cat = (int)$_POST['parent'];
						$update_cat_re_sort=array();

						//$update_cat_re_sort[$column]=$new_sorter;
						foreach($wppizza_options['layout']['category_sort_hierarchy'] as $catId=>$sort){
							$update_cat_re_sort[$catId]=$new_sorter;
							$new_sorter++;

							if($child_parent_cat == $catId){
								//$new_sorter++;
								$update_cat_re_sort[$new_child_cat]=$new_sorter;
								$new_sorter++;
							}
						}
						$update_cat_sort = $update_cat_re_sort;
					}

					$wppizza_options['layout']['category_sort_hierarchy'] = $update_cat_sort;

					/*update option*/
					update_option( WPPIZZA_SLUG, $wppizza_options );
			}

			/************************************************************************
			*	we are editing in single cat edit screen (where we can change parent)
			*	save new order as parent might have changed
			*	this runs after the term have been updated in the db by wordpress
			**************************************************************************/
			if(isset($wppizza_options['layout']['category_sort_hierarchy'][$column]) && !empty($_POST['action']) && $_POST['action']=='editedtag'){

				/*
					skip update, if parent id has not changed
					also, post parent equals -1 if topmost, but current_term->parent will equal 0 if topmost
					so let's account for that too
				*/
				$current_term = get_term($column, WPPIZZA_TAXONOMY);

				if($_POST['parent'] != $current_term->parent){
					/* parent has changed, unset first before reordering */
					unset($wppizza_options['layout']['category_sort_hierarchy'][$column]);

					/**
						changed to be parent category (post == -1)
						provided current_term->parent !=0 as current_term->parent equals zero when topmost
						instead of -1
					**/
					if($_POST['parent'] == -1 && $current_term->parent!=0){
						/**resort, with new cat id set to being the first*/
						$new_sorter=0;
						$update_cat_sort=array();
						$update_cat_sort[$column]=$new_sorter;
						foreach($wppizza_options['layout']['category_sort_hierarchy'] as $catId=>$sort){
							$new_sorter++;
							$update_cat_sort[$catId]=$new_sorter;
						}

						$wppizza_options['layout']['category_sort_hierarchy'] = $update_cat_sort;

						/*update option*/
						update_option( WPPIZZA_SLUG, $wppizza_options );
					}

					/** changed to be child category (or child category of another parent)**/
					if($_POST['parent'] >= 0){

						/**resort, with new cat id inserted after it's parent*/
						$new_sorter = 0;
						$new_child_cat = $column;
						$child_parent_cat = (int)$_POST['parent'];
						$update_cat_sort = array();
						foreach($wppizza_options['layout']['category_sort_hierarchy'] as $catId=>$sort){
							$update_cat_sort[$catId]=$new_sorter;
							$new_sorter++;

							if($child_parent_cat == $catId){
								//$new_sorter++;
								$update_cat_sort[$new_child_cat]=$new_sorter;
								$new_sorter++;
							}
						}
						$wppizza_options['layout']['category_sort_hierarchy']  = $update_cat_sort;

						/*update option*/
						update_option( WPPIZZA_SLUG, $wppizza_options );

					}
				}


				/**get categories sorted */
				//$update_cat_sort = WPPIZZA()->categories->wppizza_get_cats_hierarchy();
				//$wppizza_options['layout']['category_sort_hierarchy'] = $update_cat_sort;
				/*update option*/
				//update_option( WPPIZZA_SLUG, $wppizza_options );
			}
			/************************************************************************
			*	we are deleting -  ajax or bulk delete,
			*	save new order
			*	as this runs after the term have been updated in the db by wordpress
			*	a simple call to wppizza_get_cats_hierarchy will do
			**************************************************************************/
			if(isset($wppizza_options['layout']['category_sort_hierarchy'][$column]) && !empty($_POST['action']) && ( $_POST['action']=='delete-tag'  || (isset($_POST['delete_tags']) && is_array($_POST['delete_tags']))) ){

				/*
					bulk delete
				*/
				if(!empty($_POST['delete_tags'])){

					foreach($_POST['delete_tags'] as $cat_id_delete){
						unset($wppizza_options['layout']['category_sort_hierarchy'][$cat_id_delete]);//unset as deleted
					}
					/**resort, now we have unset deleted*/
					$new_sorter=0;
					$update_cat_sort=array();
					foreach($wppizza_options['layout']['category_sort_hierarchy'] as $catId=>$sort){
						$update_cat_sort[$catId] = $new_sorter;
						$new_sorter++;
					}

					$wppizza_options['layout']['category_sort_hierarchy'] = $update_cat_sort;

					/*update option*/
					update_option( WPPIZZA_SLUG, $wppizza_options );

				}else{
					/* simple delete, just remove from array */
					$cat_id_delete = $_POST['tag_ID'];

					//$update_cat_sort = $wppizza_options['layout']['category_sort_hierarchy'];
					unset($wppizza_options['layout']['category_sort_hierarchy'][$cat_id_delete]);//unset as deleted


					/**
						just get categories sorted on ajax delete,
						as this runs after the term have been updated in the db by wordpress
					**/
					//$update_cat_sort=WPPIZZA()->categories->wppizza_get_cats_hierarchy();
					/*update option*/
					update_option( WPPIZZA_SLUG, $wppizza_options );

				}
				//$wppizza_options['layout']['category_sort_hierarchy'] = $update_cat_sort;
			}

		$run_once++;
		}
	}

	/*********************************************************
	*
	*	[define caps]
	*	@since 3.0
	*
	*********************************************************/
	function wppizza_filter_define_caps($caps){
		/**add editing capability for this page**/
		$caps[$this->class_key]=array('name'=>$this->submenu_caps_title ,'cap'=>''.WPPIZZA_SLUG.'_cap_'.$this->class_key.'');
		// let's not enable/list this option for now....probably not required anyway as one should also delete/reassign orders to someone else ...
		//$caps[$this->class_key.'-delete-customers']=array('name'=>__('Delete Customers', 'wppizza-admin') ,'cap'=>'wppizza_cap_delete_customers');
		return $caps;
	}
}
/***************************************************************
*
*	[ini]
*
***************************************************************/
$WPPIZZA_CATEGORIES = new WPPIZZA_CATEGORIES();
?>