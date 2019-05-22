<?php

if ( ! class_exists( 'TLPfoodmenu' ) ) {

	class TLPfoodmenu {
		public $post_type;
		public $taxonomies;
		public $options;

		function __construct() {
			$this->options = array(
				'settings'          => 'tpl_food_menu_settings',
				'version'           => TLP_FOOD_MENU_VERSION,
				'title'             => 'Food Menu',
				'slug'              => 'tlp-food-menu',
				'installed_version' => 'tlp-food-menu-installed-version'
			);

			$settings             = get_option( $this->options['settings'] );
			$this->post_type      = "food-menu";
			$this->shortCodePT    = "fmsc";
			$this->post_type_slug = isset( $settings['general']['slug'] ) ? ( $settings['general']['slug'] ? sanitize_title_with_dashes( $settings['general']['slug'] ) : 'food-menu' ) : 'food-menu';
			$this->taxonomies     = array( 'category' => $this->post_type . '-category' );

			$this->incPath       = dirname( __FILE__ );
			$this->functionsPath = $this->incPath . '/functions/';
			$this->classesPath   = $this->incPath . '/classes/';
			$this->widgetsPath   = $this->incPath . '/widgets/';
			$this->viewsPath     = $this->incPath . '/views/';
			$this->templatePath  = $this->incPath . '/template/';
			$this->assetsUrl     = TLP_FOOD_MENU_PLUGIN_URL . '/assets/';
			$this->TPLloadFunctions( $this->functionsPath );
			$this->TPLloadClass( $this->classesPath );

		}

		function TPLloadClass( $dir ) {
			if ( ! file_exists( $dir ) ) {
				return;
			}

			$classes = array();

			foreach ( scandir( $dir ) as $item ) {
				if ( preg_match( "/.php$/i", $item ) ) {
					require_once( $dir . $item );
					$className = str_replace( ".php", "", $item );
					$classes[] = new $className;
				}
			}

			if ( $classes ) {
				foreach ( $classes as $class ) {
					$this->objects[] = $class;
				}
			}
		}

		function loadWidget( $dir ) {
			if ( ! file_exists( $dir ) ) {
				return;
			}
			foreach ( scandir( $dir ) as $item ) {
				if ( preg_match( "/.php$/i", $item ) ) {
					require_once( $dir . $item );
					$class = str_replace( ".php", "", $item );

					if ( method_exists( $class, 'register_widget' ) ) {
						$caller = new $class;
						$caller->register_widget();
					} else {
						register_widget( $class );
					}
				}
			}
		}

		function TPLloadFunctions( $dir ) {
			if ( ! file_exists( $dir ) ) {
				return;
			}

			foreach ( scandir( $dir ) as $item ) {
				if ( preg_match( "/.php$/i", $item ) ) {
					require_once( $dir . $item );
				}
			}

		}

		function render( $viewName, $args = array() ) {
			global $TLPfoodmenu;
			$path     = str_replace( ".", "/", $viewName );
			$viewPath = $TLPfoodmenu->viewsPath . $path . '.php';
			if ( ! file_exists( $viewPath ) ) {
				return;
			}

			if ( $args ) {
				extract( $args );
			}
			$pageReturn = include $viewPath;
			if ( $pageReturn AND $pageReturn <> 1 ) {
				return $pageReturn;
			}
		}

		/**
		 * Dynamicaly call any  method from models class
		 * by pluginFramework instance
		 */
		function __call( $name, $args ) {
			if ( ! is_array( $this->objects ) ) {
				return;
			}
			foreach ( $this->objects as $object ) {
				if ( method_exists( $object, $name ) ) {
					$count = count( $args );
					if ( $count == 0 ) {
						return $object->$name();
					} elseif ( $count == 1 ) {
						return $object->$name( $args[0] );
					} elseif ( $count == 2 ) {
						return $object->$name( $args[0], $args[1] );
					} elseif ( $count == 3 ) {
						return $object->$name( $args[0], $args[1], $args[2] );
					} elseif ( $count == 4 ) {
						return $object->$name( $args[0], $args[1], $args[2], $args[3] );
					} elseif ( $count == 5 ) {
						return $object->$name( $args[0], $args[1], $args[2], $args[3], $args[4] );
					} elseif ( $count == 6 ) {
						return $object->$name( $args[0], $args[1], $args[2], $args[3], $args[4], $args[5] );
					}
				}
			}
		}
	}

	global $TLPfoodmenu;
	if ( ! is_object( $TLPfoodmenu ) ) {
		$TLPfoodmenu = new TLPfoodmenu;
	}
}
