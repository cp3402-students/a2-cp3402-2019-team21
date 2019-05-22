<?php

if(!class_exists('FmTemplate')):

	/**
	*
	*/
	class FmTemplate
	{

		function __construct()
		{
			add_filter( 'template_include', array( $this, 'template_loader' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'load_templatesctipt' ));
		}

		public static function template_loader( $template ) {
			$find = array();
			$file = null;
			global $TLPfoodmenu;
			if ( is_single() && get_post_type() == $TLPfoodmenu->post_type ) {

				$file 	= 'single-food-menu.php';
				$find[] = $file;
				$find[] = $TLPfoodmenu->templatePath . $file;

			}elseif ( is_food_taxonomy() ) {

				$term   = get_queried_object();

				if ( is_tax( $TLPfoodmenu->taxonomies['category'] ) ) {
					$file = 'taxonomy-' . $term->taxonomy . '.php';
				} else {
					$file = 'archive-food-menu-category.php';
				}

				$find[] = 'taxonomy-' . $term->taxonomy . '-' . $term->slug . '.php';
				$find[] = $TLPfoodmenu->templatePath . 'taxonomy-' . $term->taxonomy . '-' . $term->slug . '.php';
				$find[] = 'taxonomy-' . $term->taxonomy . '.php';
				$find[] = $TLPfoodmenu->templatePath . 'taxonomy-' . $term->taxonomy . '.php';
				$find[] = $file;
				$find[] = $TLPfoodmenu->templatePath . $file;

			} elseif ( is_post_type_archive( $TLPfoodmenu->post_type ) ) {

				$file 	= 'archive-food-menu-category.php';
				$find[] = $file;
				$find[] = $TLPfoodmenu->templatePath . $file;

			}

			if ( $file ) {

				$template = locate_template( array_unique( $find ) );
				if ( ! $template ) {
					$template = $TLPfoodmenu->templatePath  . $file;
				}
			}
			return $template;
		}

		public function load_templatesctipt(){
			global $TLPfoodmenu;
			if(get_post_type() == $TLPfoodmenu->post_type || is_post_type_archive($TLPfoodmenu->post_type)){
				wp_enqueue_style( 'tlp-fm-css', $TLPfoodmenu->assetsUrl .'css/tlpfoodmenu.css' );
				wp_enqueue_script( 'tlp-fm-js', $TLPfoodmenu->assetsUrl .'js/tlpfoodmenu.js', array('jquery'), '', true );
			}

		}


	}

endif;
