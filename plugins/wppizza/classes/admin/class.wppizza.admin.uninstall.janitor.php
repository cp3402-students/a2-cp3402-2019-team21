<?php if ( ! defined( 'ABSPATH' ) ) exit;/*Exit if accessed directly*/ ?>
<?php
/*
* WordPress Plugin Janitor Class
*
* A simple way to remove plugin data from the database when the plugin is uninstalled.
*
* @package    wpPluginFramework <https://github.com/sscovil/wpPluginFramework>
* @author     Shaun Scovil <sscovil@gmail.com> (amended by ollybach)
* @version    1.0
*/

class wppizzaPluginJanitor {
	/*
	* Remove plugin data from database during uninstall process.
	*
	* @param array $opt  Plugin options added to wp_options table.
	* @param array $cpt  Plugin-specific custom post types.
	* @param array $tax  Plugin-specific custom taxonomies.
	*/
	public function cleanup( $opt = NULL, $cpt = NULL, $tax = NULL ) {
		// Perform security checks.
		if( self::authorize() == TRUE ) {

			// Remove plugin options from wp_options database table.
			if( $opt ) {
				foreach( $opt as $option ) {
					delete_option( $option );
				}
			}

			// Remove plugin-specific custom post type entries.
			if( $cpt ) {
				$entries = get_posts( array( 'post_type' => $cpt ,'numberposts'=>-1) );
				foreach( $entries as $entry ) {
					wp_delete_post( $entry->ID, TRUE );
				}
			}

			// Remove plugin-specific custom taxonomy terms.
			if( $tax ) {
				global $wp_taxonomies;
				foreach( $tax as $taxonomy ) {
					register_taxonomy( $taxonomy['taxonomy'],$taxonomy['object_type'] );
					$terms = get_terms( $taxonomy['taxonomy'], array( 'hide_empty' => 0 ) );
					foreach( $terms as $term ) {
						wp_delete_term( $term->term_id, $taxonomy['taxonomy'] );
					}
					delete_option( $taxonomy['taxonomy'].'_children' );
					unset( $wp_taxonomies[$taxonomy['taxonomy']] );
				}
			}

		}
	}
	/*
	* Perform security checks to authorize uninstall.
	*
	* @return bool  Returns TRUE if circumstances pass security checks.
	*/
	public function authorize() {
		// No direct access from outside of WordPress.
		if( !function_exists( 'is_admin' ) ) {
			header( 'Status: 403 Forbidden' );
			header( 'HTTP/1.1 403 Forbidden' );
			exit();
		}

		// User must be logged in to uninstall plugin.
		if( !is_user_logged_in() ) {
			wp_die( 'You must be logged in to run this script.' );
		}

		// User must have permission to uninstall plugin.
		if( !current_user_can( 'install_plugins' ) ) {
			wp_die( 'You do not have permission to run this script.' );
		}

		// Authorize uninstall.
		return TRUE;
	}
}
?>