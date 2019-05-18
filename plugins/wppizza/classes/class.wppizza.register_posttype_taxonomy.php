<?php
/**
* WPPIZZA_REGISTER_POSTTYPE_AND_TAXONOMY
*
* @package     WPPIZZA
* @subpackage  post type /  taxonomy registration functions
* @copyright   Copyright (c) 2015, Oliver Bach
* @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
* @since       3.0
*/
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit();/*Exit if accessed directly*/
/************************************************************************************************************************
*
*
*	WPPIZZA_CATEGORIES filters
*
*
************************************************************************************************************************/
class WPPIZZA_REGISTER_POSTTYPE_AND_TAXONOMY{

/****************************************************************************************************************************
*
*
*	[add actions and filters]
*
*
****************************************************************************************************************************/
	/******************************************************************************************************************
	*
	*	[CONSTRUCTOR]
	*
	*
	*	@since 3.0
	*
	******************************************************************************************************************/
	function __construct() {
		add_action('init', array($this, 'register_posttype'));/*register custom posttype*/
		add_action('init', array($this, 'register_taxonomies'));/*register taxonomies*/
	}
/****************************************************************************************************************************
*
*
*	[add methods called by actions and filters]
*
*
****************************************************************************************************************************/

	/*******************************************************
	*
	*	[register wppizza custom post type]
	*
	******************************************************/
	function register_posttype(){
		
		$labels = array(
			'name'               => WPPIZZA_NAME.' '.__( 'Menu Items', 'wppizza-admin'),
			'singular_name'      => WPPIZZA_NAME.' '.__( 'Menu Item', 'wppizza-admin'),
			'add_new'            => __( 'Add New',  'wppizza-admin' ),
			'add_new_item'       => __( 'Add New Menu Item','wppizza-admin' ),
			'edit'				 => __( 'Edit', 'wppizza-admin' ),
			'edit_item'          => __( 'Edit Menu Item','wppizza-admin' ),
			'new_item'           => __( 'New Menu Item','wppizza-admin' ),
			'all_items'          => __( 'All Menu Items','wppizza-admin' ),
			'view'               => __( 'View', 'wppizza-admin' ),
			'view_item'          => __( 'View Menu Item','wppizza-admin' ),
			'search_items'       => __( 'Search Menu Items','wppizza-admin' ),
			'not_found'          => __( 'No items found','wppizza-admin' ),
			'not_found_in_trash' => __( 'No items found in the Trash','wppizza-admin' ),
			'parent_item_colon'  => '',
			'menu_name'          => ''.WPPIZZA_NAME.''
		);
		/** filter labels **/
		$labels = apply_filters('wppizza_filter_cpt_lbls', $labels);

		$args = array(
			'labels'        => $labels,
			'description'   => sprintf( __( 'Holds %1$s  menu items data', 'wppizza-admin' ), WPPIZZA_NAME ),
			'show_ui'		=> true,
			'public'        => true,
			'menu_position' => 100,
			'menu_icon'		=> WPPIZZA_MENU_ICON,
			'has_archive'   => false,
			'hierarchical'	=> false,
			'can_export'	=> false,
			'show_in_rest'	=> false,
			'supports'      => array( 'title' => 'title', 'editor' => 'editor', 'author' => 'author', 'thumbnail' => 'thumbnail', 'page-attributes' => 'page-attributes', 'comments' => 'comments'),
			'taxonomies'    => array(''), /* 'post_tag' for example*/
			'capability_type' => array(WPPIZZA_SLUG, WPPIZZA_SLUG.'s'),
			'map_meta_cap' => true,
		    'capabilities' => array(
		        'edit_post' => 'edit_'.WPPIZZA_SLUG.'',
		        'edit_posts' => 'edit_'.WPPIZZA_SLUG.'s',
		        'edit_others_posts' => 'edit_others_'.WPPIZZA_SLUG.'s',
		        'publish_posts' => 'publish_'.WPPIZZA_SLUG.'s',
		        'read_post' => 'read_'.WPPIZZA_SLUG.'',
		        'read_private_posts' => 'read_private_'.WPPIZZA_SLUG.'s',
		        'delete_post' => 'delete_'.WPPIZZA_SLUG.'',
		        'delete_posts' => 'delete_'.WPPIZZA_SLUG.'s'
		    )			
		    /* i dont think these capabilities exist actually but lets leave them here for reference for now */
		    //'create_posts' => 'edit_'.WPPIZZA_SLUG.'s',		    
		    //'edit_other_posts' => 'edit_other_'.WPPIZZA_SLUG.'s',		    
		);

		/** filter arguments **/
		$args = apply_filters('wppizza_filter_cpt_args', $args);

		/* register post type */
		register_post_type( WPPIZZA_SLUG, $args );
	}

	/*******************************************************
	*
	*	[register wppizza taxonomy]
	*
	******************************************************/
	function register_taxonomies(){

		  // Add new taxonomy, make it hierarchical (like categories)
		  $labels = array(
		    'name' => WPPIZZA_NAME. ' ' ._x( 'Categories', 'taxonomy general name' ),
		    'singular_name' => _x( 'Category', 'taxonomy singular name' ),
		    'search_items' =>  __( 'Search Categories' ),
		    'all_items' => __( 'All Categories' ),
		    'parent_item' => __( 'Parent Category' ),
		    'parent_item_colon' => __( 'Parent Category:' ),
		    'edit_item' => __( 'Edit Category' ),
		    'update_item' => __( 'Update Category' ),
		    'add_new_item' => __( 'Add New Category' ),
		    'new_item_name' => __( 'New Category Name' ),
		    'menu_name' => __( 'Categories' )
		  );
		/** filter labels **/
		$labels = apply_filters('wppizza_filter_ctx_lbls', $labels);

		  $args = array(
		    'hierarchical' => true,
		    'labels' => $labels,
		    'show_ui' => true,
		    'show_admin_column' => true,
		    'query_var' => true,
		    'rewrite' => array( 'slug' => WPPIZZA_TAXONOMY , 'hierarchical'=>true ),
			'capabilities' => array (
            	'manage_terms' => ''.WPPIZZA_SLUG.'_cap_categories',
            	'edit_terms' => ''.WPPIZZA_SLUG.'_cap_categories',
            	'delete_terms' => ''.WPPIZZA_SLUG.'_cap_categories',
            	'assign_terms' => ''.WPPIZZA_SLUG.'_cap_categories'//edit_posts
            )		    	
		  );

		/** filter arguments **/
		$args = apply_filters('wppizza_filter_ctx_args', $args);

		/* register taxonomy */
		register_taxonomy(WPPIZZA_TAXONOMY, array(WPPIZZA_SLUG), $args );
	}
}
/***************************************************************
*
*	[ini]
*
***************************************************************/
$WPPIZZA_REGISTER_POSTTYPE_AND_TAXONOMY = new WPPIZZA_REGISTER_POSTTYPE_AND_TAXONOMY()	
?>