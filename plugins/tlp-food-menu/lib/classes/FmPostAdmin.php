<?php

if(!class_exists('FmPostAdmin')):

	/**
	*
	*/
	class FmPostAdmin
	{

		function __construct()
		{
			add_filter( 'manage_edit-food-menu_columns', array($this, 'arrange_food_menu_columns'));
			add_action( 'manage_food-menu_posts_custom_column', array($this,'manage_food_menu_columns'), 10, 2);
			add_action( 'restrict_manage_posts', array( $this, 'add_taxonomy_filters' ) );
		}


		public function add_taxonomy_filters() {
			global $typenow, $TLPfoodmenu;
			// Must set this to the post type you want the filter(s) displayed on
			if ( $TLPfoodmenu->post_type !== $typenow ) {
				return;
			}
			foreach ( $TLPfoodmenu->taxonomies as $tax_slug ) {
				echo $this->build_taxonomy_filter( $tax_slug );
			}
		}

		/**
		 * Build an individual dropdown filter.
		 *
		 * @param  string $tax_slug Taxonomy slug to build filter for.
		 *
		 * @return string Markup, or empty string if taxonomy has no terms.
		 */
		protected function build_taxonomy_filter( $tax_slug ) {
			$terms = get_terms( $tax_slug );
			if ( 0 == count( $terms ) ) {
				return '';
			}
			$tax_name         = $this->get_taxonomy_name_from_slug( $tax_slug );
			$current_tax_slug = isset( $_GET[$tax_slug] ) ? $_GET[$tax_slug] : false;
			$filter  = '<select name="' . esc_attr( $tax_slug ) . '" id="' . esc_attr( $tax_slug ) . '" class="postform">';
			$filter .= '<option value="0">' . esc_html( $tax_name ) .'</option>';
			$filter .= $this->build_term_options( $terms, $current_tax_slug );
			$filter .= '</select>';
			return $filter;
		}

		/**
		 * Get the friendly taxonomy name, if given a taxonomy slug.
		 *
		 * @param  string $tax_slug Taxonomy slug.
		 *
		 * @return string Friendly name of taxonomy, or empty string if not a valid taxonomy.
		 */
		protected function get_taxonomy_name_from_slug( $tax_slug ) {
			$tax_obj = get_taxonomy( $tax_slug );
			if ( ! $tax_obj )
				return '';
			return $tax_obj->labels->name;
		}

		/**
		 * Build a series of option elements from an array.
		 *
		 * Also checks to see if one of the options is selected.
		 *
		 * @param  array  $terms            Array of term objects.
		 * @param  string $current_tax_slug Slug of currently selected term.
		 *
		 * @return string Markup.
		 */
		protected function build_term_options( $terms, $current_tax_slug ) {
			$options = '';
			foreach ( $terms as $term ) {
				$options .= sprintf(
					"<option value='%s' %s />%s</option>",
					esc_attr( $term->slug ),
					selected( $current_tax_slug, $term->slug, false ),
					esc_html( $term->name . '(' . $term->count . ')' )
				);
				// $options .= selected( $current_tax_slug, $term->slug );
			}
			return $options;
		}


		public function arrange_food_menu_columns( $columns ) {
			$column_thumbnail = array( 'thumbnail' => __( 'Image', 'tlp-food-menu' ) );
			$column_price = array( 'price' => __( 'Price', 'tlp-food-menu' ) );
			return array_slice( $columns, 0, 2, true ) + $column_thumbnail + $column_price + array_slice( $columns, 1, null, true );
		}

		public function manage_food_menu_columns( $column ) {
			// global $post;
			switch ( $column ) {
				case 'thumbnail':
					echo get_the_post_thumbnail( get_the_ID(), array( 35, 35 ) );
					break;
				case 'price':
					echo sprintf("%.2f",get_post_meta( get_the_ID(), 'price', true));
					break;
			}
		}
	}


endif;
