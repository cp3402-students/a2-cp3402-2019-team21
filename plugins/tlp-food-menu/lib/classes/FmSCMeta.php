<?php

if ( ! class_exists( 'FmSCMeta' ) ):
	/**
	 *
	 */
	class FmSCMeta {

		function __construct() {
			add_action( 'add_meta_boxes', array( $this, 'fm_sc_meta_boxes' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
			add_action( 'save_post', array( $this, 'save_post' ), 10, 2 );
			add_action( 'edit_form_after_title', array( $this, 'fmp_sc_after_title' ) );
			add_action( 'admin_init', array( $this, 'fm_pro_remove_all_meta_box' ) );
			add_filter( 'manage_edit-fmsc_columns', array( $this, 'arrange_fm_sc_columns' ) );
			add_action( 'manage_fmsc_posts_custom_column', array( $this, 'manage_fm_sc_columns' ), 10, 2 );
		}

		public function manage_fm_sc_columns( $column ) {
			switch ( $column ) {
				case 'fm_short_code':
					echo '<input type="text" onfocus="this.select();" readonly="readonly" value="[foodmenu id=&quot;' . get_the_ID() . '&quot; title=&quot;' . get_the_title() . '&quot;]" class="large-text code rt-code-sc">';
					break;
				default:
					break;
			}
		}

		public function arrange_fm_sc_columns( $columns ) {
			$shortcode = array( 'fm_short_code' => __( 'Shortcode', 'food-menu-pro' ) );

			return array_slice( $columns, 0, 2, true ) + $shortcode + array_slice( $columns, 1, null, true );
		}

		/**
		 * This will add input text field for shortCode
		 *
		 * @param $post
		 */
		function fmp_sc_after_title( $post ) {
			global $TLPfoodmenu;
			if ( $TLPfoodmenu->shortCodePT !== $post->post_type ) {
				return;
			}

			$html = null;
			$html .= '<div class="postbox" style="margin-bottom: 0;"><div class="inside">';
			$html .= '<p><input type="text" onfocus="this.select();" readonly="readonly" value="[foodmenu id=&quot;' . $post->ID . '&quot; title=&quot;' . $post->post_title . '&quot;]" class="large-text code rt-code-sc">
            <input type="text" onfocus="this.select();" readonly="readonly" value="&#60;&#63;php echo do_shortcode( &#39;[foodmenu id=&quot;' . $post->ID . '&quot; title=&quot;' . $post->post_title . '&quot;]&#39; ); &#63;&#62;" class="large-text code rt-code-sc">
            </p>';
			$html .= '</div></div>';
			echo $html;
		}

		function fm_pro_remove_all_meta_box() {
			if ( is_admin() ) {
				global $TLPfoodmenu;
				add_filter( "get_user_option_meta-box-order_" . $TLPfoodmenu->shortCodePT,
					array( $this, 'remove_all_meta_boxes_fmp_sc' ) );
			}
		}


		/**
		 * Add only custom meta box
		 * @return array
		 */
		function remove_all_meta_boxes_fmp_sc() {
			global $wp_meta_boxes, $TLPfoodmenu;
			$publishBox                                 = $wp_meta_boxes[ $TLPfoodmenu->shortCodePT ]['side']['core']['submitdiv'];
			$scBox                                      = $wp_meta_boxes[ $TLPfoodmenu->shortCodePT ]['normal']['high'][ $TLPfoodmenu->shortCodePT . '_sc_settings_meta' ];
			$wp_meta_boxes[ $TLPfoodmenu->shortCodePT ] = array(
				'side'   => array( 'core' => array( 'submitdiv' => $publishBox ) ),
				'normal' => array(
					'high' => array(
						$TLPfoodmenu->shortCodePT . '_sc_settings_meta' => $scBox
					)
				)
			);

			return array();
		}

		function admin_enqueue_scripts() {

			global $TLPfoodmenu, $pagenow, $typenow;
			// validate page
			if ( ! in_array( $pagenow, array( 'post.php', 'post-new.php', 'edit.php' ) ) ) {
				return;
			}
			if ( $typenow != $TLPfoodmenu->shortCodePT ) {
				return;
			}

			wp_enqueue_media();
			// scripts
			wp_enqueue_script( array(
				'jquery',
				'wp-color-picker',
				'fm-select2',
				'fm-admin'
			) );

			// styles
			wp_enqueue_style( array(
				'wp-color-picker',
				'fm-select2',
				'fm-admin'
			) );

			$nonce = wp_create_nonce( $TLPfoodmenu->nonceText() );
			wp_localize_script( 'fm-admin', 'fm',
				array(
					'nonceId' => $TLPfoodmenu->nonceId(),
					'nonce'   => $nonce,
					'ajaxurl' => admin_url( 'admin-ajax.php' )
				) );


		}

		function fm_sc_meta_boxes() {
			global $TLPfoodmenu;
			add_meta_box(
				$TLPfoodmenu->shortCodePT . '_sc_settings_meta',
				__( 'Short Code Generator', 'tlp-food-menu' ),
				array( $this, 'fm_sc_settings_selection' ),
				$TLPfoodmenu->shortCodePT,
				'normal',
				'high' );
		}

		/**
		 * Setting Sections
		 *
		 * @param $post
		 */
		function fm_sc_settings_selection( $post ) {
			global $TLPfoodmenu;
			wp_nonce_field( $TLPfoodmenu->nonceText(), $TLPfoodmenu->nonceId() );
			$html = null;
			$html .= '<div class="rt-tab-container">';
			$html .= '<ul class="rt-tab-nav">
	                            <li><a href="#sc-fm-layout">' . __( 'Layout Settings', 'food-menu-pro' ) . '</a></li>
	                            <li><a href="#sc-fm-filter">' . __( 'Filtering', 'food-menu-pro' ) . '</a></li>
	                            <li><a href="#sc-fm-field-selection">' . __( 'Field selection', 'food-menu-pro' ) . '</a></li>
	                            <li><a href="#sc-fm-style">' . __( 'Styling', 'food-menu-pro' ) . '</a></li>
	                          </ul>';
			$html .= '<div id="sc-fm-layout" class="rt-tab-content">';
			$html .= "<div class='rt-sc-meta-field-holder'>";
			$html .= $TLPfoodmenu->rtFieldGenerator( $TLPfoodmenu->scLayoutMetaFields() );
			$html .= "</div>";
			$html .= '</div>';

			$html .= '<div id="sc-fm-filter" class="rt-tab-content">';
			$html .= "<div class='rt-sc-meta-field-holder'>";
			$html .= $TLPfoodmenu->rtFieldGenerator( $TLPfoodmenu->scFilterMetaFields() );
			$html .= "</div>";
			$html .= '</div>';

			$html .= '<div id="sc-fm-field-selection" class="rt-tab-content">';
			$html .= "<div class='rt-sc-meta-field-holder'>";
			$html .= $TLPfoodmenu->rtFieldGenerator( $TLPfoodmenu->scItemFields() );
			$html .= "</div>";
			$html .= '</div>';

			$html .= '<div id="sc-fm-style" class="rt-tab-content">';
			$html .= "<div class='rt-sc-meta-field-holder'>";
			$html .= $TLPfoodmenu->rtFieldGenerator( $TLPfoodmenu->scStyleFields() );
			$html .= "</div>";
			$html .= '</div>';
			$html .= '</div>';

			echo $html;
		}


		/**
		 *  Preview section
		 */
		function fm_sc_preview_selection() {
			$html = null;
			$html .= "<div class='fmp-response'><span class='spinner'></span></div>";
			$html .= "<div id='fmp-preview-container'>";
			$html .= "</div>";

			echo $html;

		}


		function save_post( $post_id, $post ) {

			global $TLPfoodmenu;

			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return;
			}
			if ( ! $TLPfoodmenu->verifyNonce() ) {
				return $post_id;
			}
			if ( $TLPfoodmenu->shortCodePT != $post->post_type ) {
				return $post_id;
			}

			$mates = $TLPfoodmenu->fmpScMetaFields();
			foreach ( $mates as $metaKey => $field ) {
				$rValue = ! empty( $_REQUEST[ $metaKey ] ) ? $_REQUEST[ $metaKey ] : null;
				$value  = $TLPfoodmenu->sanitize( $field, $rValue );
				if ( empty( $field['multiple'] ) ) {
					update_post_meta( $post_id, $metaKey, $value );
				} else {
					delete_post_meta( $post_id, $metaKey );
					if ( is_array( $value ) && ! empty( $value ) ) {
						foreach ( $value as $item ) {
							add_post_meta( $post_id, $metaKey, $item );
						}
					} else {
						update_post_meta( $post_id, $metaKey, "" );
					}
				}
			}

		}
	}
endif;