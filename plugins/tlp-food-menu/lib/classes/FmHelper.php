<?php
if ( ! class_exists( 'FmHelper' ) ):

	class FmHelper {
		function verifyNonce() {
			$nonce = ! empty( $_REQUEST[ $this->nonceId() ] ) ? $_REQUEST[ $this->nonceId() ] : null;
			if ( ! wp_verify_nonce( $nonce, $this->nonceText() ) ) {
				return false;
			}

			return true;
		}

		function nonceId() {
			return "tlp_fm_nonce";
		}

		function nonceText() {
			return "tlp_food_menu_nonce";
		}

		function the_excerpt_max_charlength( $charLength ) {
			$excerpt = get_the_excerpt();
			$charLength ++;
			$html = null;
			if ( mb_strlen( $excerpt ) > $charLength ) {
				$subex   = mb_substr( $excerpt, 0, $charLength - 5 );
				$exwords = explode( ' ', $subex );
				$excut   = - ( mb_strlen( $exwords[ count( $exwords ) - 1 ] ) );
				if ( $excut < 0 ) {
					$html .= mb_substr( $subex, 0, $excut );
				} else {
					$html .= $subex;
				}
			} else {
				$html .= $excerpt;
			}

			return $html;
		}

		function t( $text ) {
			return __( $text, 'tlp-food-menu' );
		}


		function string_limit_words( $string, $word_limit ) {
			$words = explode( ' ', $string );

			return implode( ' ', array_slice( $words, 0, $word_limit ) );
		}

		function rtFieldGenerator( $fields = array() ) {
			$html = null;
			if ( is_array( $fields ) && ! empty( $fields ) ) {
				$fmField = new FmField();
				foreach ( $fields as $fieldKey => $field ) {
					$html .= $fmField->Field( $fieldKey, $field );
				}
			}

			return $html;
		}

		function sanitize( $field = array(), $value = null ) {
			$newValue = null;
			if ( is_array( $field ) ) {
				$type = ( ! empty( $field['type'] ) ? $field['type'] : 'text' );
				if ( empty( $field['multiple'] ) ) {
					if ( $type == 'text' || $type == 'number' || $type == 'select' || $type == 'checkbox' || $type == 'radio' ) {
						$newValue = sanitize_text_field( $value );
					} else if ( $type == 'price' ) {
						$newValue = ( '' === $value ) ? '' : FMP()->format_decimal( $value );
					} else if ( $type == 'url' ) {
						$newValue = esc_url( $value );
					} else if ( $type == 'slug' ) {
						$newValue = sanitize_title_with_dashes( $value );
					} else if ( $type == 'textarea' ) {
						$newValue = wp_kses_post( $value );
					} else if ( $type == 'custom_css' ) {
						$newValue = esc_attr( $value );
					} else if ( $type == 'colorpicker' ) {
						$newValue = $this->sanitize_hex_color( $value );
					} else if ( $type == 'image_size' ) {
						$newValue = array();
						foreach ( $value as $k => $v ) {
							$newValue[ $k ] = esc_attr( $v );
						}
					} else if ( $type == 'style' ) {
						$newValue = array();
						foreach ( $value as $k => $v ) {
							if ( $k == 'color' ) {
								$newValue[ $k ] = $this->sanitize_hex_color( $v );
							} else {
								$newValue[ $k ] = $this->sanitize( array( 'type' => 'text' ), $v );
							}
						}
					} else {
						$newValue = sanitize_text_field( $value );
					}

				} else {
					$newValue = array();
					if ( ! empty( $value ) ) {
						if ( is_array( $value ) ) {
							foreach ( $value as $key => $val ) {
								if ( $type == 'style' && $key == 0 ) {
									if ( function_exists( 'sanitize_hex_color' ) ) {
										$newValue = sanitize_hex_color( $val );
									} else {
										$newValue[] = $this->sanitize_hex_color( $val );
									}
								} else {
									$newValue[] = sanitize_text_field( $val );
								}
							}
						} else {
							$newValue[] = sanitize_text_field( $value );
						}
					}
				}
			}

			return $newValue;
		}

		function sanitize_hex_color( $color ) {
			if ( function_exists( 'sanitize_hex_color' ) ) {
				return sanitize_hex_color( $color );
			} else {
				if ( '' === $color ) {
					return '';
				}

				// 3 or 6 hex digits, or the empty string.
				if ( preg_match( '|^#([A-Fa-f0-9]{3}){1,2}$|', $color ) ) {
					return $color;
				}
			}
		}

		/* Convert hexdec color string to rgb(a) string */
		function rtHex2rgba( $color, $opacity = .5 ) {

			$default = 'rgb(0,0,0)';

			//Return default if no color provided
			if ( empty( $color ) ) {
				return $default;
			}

			//Sanitize $color if "#" is provided
			if ( $color[0] == '#' ) {
				$color = substr( $color, 1 );
			}

			//Check if color has 6 or 3 characters and get values
			if ( strlen( $color ) == 6 ) {
				$hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
			} elseif ( strlen( $color ) == 3 ) {
				$hex = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
			} else {
				return $default;
			}

			//Convert hexadec to rgb
			$rgb = array_map( 'hexdec', $hex );

			//Check if opacity is set(rgba or rgb)
			if ( $opacity ) {
				if ( abs( $opacity ) > 1 ) {
					$opacity = 1.0;
				}
				$output = 'rgba(' . implode( ",", $rgb ) . ',' . $opacity . ')';
			} else {
				$output = 'rgb(' . implode( ",", $rgb ) . ')';
			}

			//Return rgb(a) color string
			return $output;
		}

		function getAllFmpCategoryList() {
			global $TLPfoodmenu;
			$terms    = array();
			$termList = get_terms( array( $TLPfoodmenu->taxonomies['category'] ), array( 'hide_empty' => 0 ) );
			if ( is_array( $termList ) && ! empty( $termList ) && empty( $termList['errors'] ) ) {
				foreach ( $termList as $term ) {
					$terms[ $term->term_id ] = $term->name;
				}
			}

			return $terms;
		}

		function get_image_sizes() {
			global $_wp_additional_image_sizes;

			$sizes = array();
			foreach ( get_intermediate_image_sizes() as $_size ) {
				if ( in_array( $_size, array( 'thumbnail', 'medium', 'large' ) ) ) {
					$sizes[ $_size ]['width']  = get_option( "{$_size}_size_w" );
					$sizes[ $_size ]['height'] = get_option( "{$_size}_size_h" );
					$sizes[ $_size ]['crop']   = (bool) get_option( "{$_size}_crop" );
				} elseif ( isset( $_wp_additional_image_sizes[ $_size ] ) ) {
					$sizes[ $_size ] = array(
						'width'  => $_wp_additional_image_sizes[ $_size ]['width'],
						'height' => $_wp_additional_image_sizes[ $_size ]['height'],
						'crop'   => $_wp_additional_image_sizes[ $_size ]['crop'],
					);
				}
			}

			$imgSize = array();
			foreach ( $sizes as $key => $img ) {
				$imgSize[ $key ] = ucfirst( $key ) . " ({$img['width']}*{$img['height']})";
			}
			$imgSize['fmp_custom'] = __( "Custom image size", "tlp-food-menu" );

			return $imgSize;
		}
	}

endif;