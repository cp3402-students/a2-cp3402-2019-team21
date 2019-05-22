<?php

if ( ! class_exists( 'FmPostInit' ) ):

	class FmPostInit {

		protected $version;

		public function __construct() {
			$this->version = (defined('WP_DEBUG') && WP_DEBUG) ? time() : TLP_FOOD_MENU_VERSION;
			add_action( 'init', array( $this, 'register' ) );
			register_activation_hook( TLP_FOOD_MENU_PLUGIN_ACTIVE_FILE_NAME, array( $this, 'activate' ) );
			register_deactivation_hook( TLP_FOOD_MENU_PLUGIN_ACTIVE_FILE_NAME, array( $this, 'deactivate' ) );
		}

		public function register() {
			$this->register_post_type();
			$this->register_taxonomy_category();
		}

		public function activate() {
			flush_rewrite_rules();
			$this->dataInsert();
		}

		/**
		 * Fired for each blog when the plugin is deactivated.
		 *
		 * @since 0.1.0
		 */
		public function deactivate() {
			flush_rewrite_rules();
		}

		/**
		 * Register the custom post type.
		 *
		 * @link http://codex.wordpress.org/Function_Reference/register_post_type
		 */
		protected function register_post_type() {
			global $TLPfoodmenu;
			$labels   = array(
				'name'               => __( 'Food Menu', 'tlp-food-menu' ),
				'singular_name'      => __( 'Food Menu', 'tlp-food-menu' ),
				'all_items'          => __( 'All Foods', 'tlp-food-menu' ),
				'add_new'            => __( 'Add Food', 'tlp-food-menu' ),
				'add_new_item'       => __( 'Add Food', 'tlp-food-menu' ),
				'edit_item'          => __( 'Edit Food', 'tlp-food-menu' ),
				'new_item'           => __( 'New Food', 'tlp-food-menu' ),
				'view_item'          => __( 'View Food', 'tlp-food-menu' ),
				'search_items'       => __( 'Search Food', 'tlp-food-menu' ),
				'not_found'          => __( 'No Food found', 'tlp-food-menu' ),
				'not_found_in_trash' => __( 'No Food in the trash', 'tlp-food-menu' ),
			);
			$supports = array(
				'title',
				'editor',
				'thumbnail',
				'excerpt',
				'page-attributes'
			);
			$args     = array(
				'labels'          => $labels,
				'supports'        => $supports,
				'public'          => true,
				'capability_type' => 'post',
				'rewrite'         => array( 'slug' => $TLPfoodmenu->post_type_slug ),
				'menu_position'   => 20,
				'menu_icon'       => $TLPfoodmenu->assetsUrl . 'images/icon-16x16.png',
			);
			register_post_type( $TLPfoodmenu->post_type, $args );


			// register scripts
			$scripts                   = array();
			$styles                    = array();
			$scripts['fmp-image-load'] = array(
				'src'    => $TLPfoodmenu->assetsUrl . "vendor/isotope/imagesloaded.pkgd.min.js",
				'deps'   => array( 'jquery' ),
				'footer' => false
			);
			$scripts['fm-frontend']    = array(
				'src'    => $TLPfoodmenu->assetsUrl . 'js/foodmenu.js',
				'deps'   => array( 'jquery' ),
				'footer' => true
			);
			// register acf styles
			$styles['fm-frontend'] = $TLPfoodmenu->assetsUrl . 'css/tlpfoodmenu.css';


			if ( is_admin() ) {
				$scripts['fm-select2'] = array(
					'src'    => $TLPfoodmenu->assetsUrl . "vendor/select2/js/select2.min.js",
					'deps'   => array( 'jquery' ),
					'footer' => false
				);
				$scripts['fm-admin']   = array(
					'src'    => $TLPfoodmenu->assetsUrl . "js/settings.js",
					'deps'   => array( 'jquery' ),
					'footer' => true
				);
				$styles['fm-select2']  = $TLPfoodmenu->assetsUrl . 'vendor/select2/css/select2.min.css';
				$styles['fm-admin']    = $TLPfoodmenu->assetsUrl . 'css/settings.css';
			}


			foreach ( $scripts as $handle => $script ) {
				wp_register_script( $handle, $script['src'], $script['deps'], $this->version, $script['footer'] );
			}


			foreach ( $styles as $k => $v ) {
				wp_register_style( $k, $v, false, $this->version );
			}

		}

		/**
		 * Register a taxonomy for Team Categories.
		 *
		 * @link http://codex.wordpress.org/Function_Reference/register_taxonomy
		 */
		protected function register_taxonomy_category() {
			global $TLPfoodmenu;
			$labels = array(
				'name'                       => __( 'Food Categories', 'tlp-food-menu' ),
				'singular_name'              => __( 'Food Category', 'tlp-food-menu' ),
				'menu_name'                  => __( 'Categories', 'tlp-food-menu' ),
				'edit_item'                  => __( 'Edit Category', 'tlp-food-menu' ),
				'update_item'                => __( 'Update Category', 'tlp-food-menu' ),
				'add_new_item'               => __( 'Add New Category', 'tlp-food-menu' ),
				'new_item_name'              => __( 'New Category', 'tlp-food-menu' ),
				'parent_item'                => __( 'Parent Category', 'tlp-food-menu' ),
				'parent_item_colon'          => __( 'Parent Category:', 'tlp-food-menu' ),
				'all_items'                  => __( 'All Categories', 'tlp-food-menu' ),
				'search_items'               => __( 'Search Categories', 'tlp-food-menu' ),
				'popular_items'              => __( 'Popular Categories', 'tlp-food-menu' ),
				'separate_items_with_commas' => __( 'Separate categories with commas', 'tlp-food-menu' ),
				'add_or_remove_items'        => __( 'Add or remove categories', 'tlp-food-menu' ),
				'choose_from_most_used'      => __( 'Choose from the most used  categories', 'tlp-food-menu' ),
				'not_found'                  => __( 'No categories found.', 'tlp-food-menu' ),
			);
			$args   = array(
				'labels'            => $labels,
				'public'            => true,
				'show_in_nav_menus' => true,
				'show_ui'           => true,
				'show_tagcloud'     => true,
				'hierarchical'      => true,
				'rewrite'           => array( 'slug' => $TLPfoodmenu->taxonomies['category'] ),
				'show_admin_column' => true,
				'query_var'         => true,
			);
			register_taxonomy( $TLPfoodmenu->taxonomies['category'], $TLPfoodmenu->post_type, $args );
		}

		private function dataInsert() {
			global $TLPfoodmenu;
			update_option( $TLPfoodmenu->options['installed_version'], $TLPfoodmenu->options['version'] );
		}
	}

endif;
