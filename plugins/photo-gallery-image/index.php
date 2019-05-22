<?php

/**
 * Plugin Name: GrandWP Gallery
 * Plugin URI: https://grandwp.com/wordpress-photo-gallery
 * Description: GrandWP Gallery is equipped with all necessary options to make the image publishing process easier and more convenient.
 * Version:     1.1.0
 * Author:      GrandWP
 * Author URI:  https://grandwp.com
 * License: GNU/GPLv3 https://www.gnu.org/licenses/gpl-3.0.html
 * Domain Path: /languages
 * Text Domain: gdgallery
 */

if (!defined('ABSPATH')) {
    exit();
}


function gutenberg_gd_photo_gallery()
{

    wp_register_script(
        'gd-photo-gallery-gutenberg',
        plugins_url('resources/assets/js/admin/block.js', __FILE__),
        array('wp-blocks', 'wp-element', 'wp-components')
    );
    wp_register_style(
        'gd-photo-gallery-gutenberg',
        plugins_url('resources/assets/css/admin/block.css', __FILE__),
        array('wp-edit-blocks'),
        filemtime(plugin_dir_path(__FILE__) . 'resources/assets/css/admin/block.css')
    );

    global $wpdb;

    $gdgalleries = $wpdb->get_results("SELECT id_gallery,name FROM " . $wpdb->prefix . "gdgallerygalleries");

    $options = array(
        array(
            'value' => '',
            'label' => 'Select Gallery'
        )
    );

    foreach ($gdgalleries as $gdgallery) {
        $options[] = array(
            'value' => $gdgallery->id_gallery,
            'label' => $gdgallery->name,
        );
    }

    wp_localize_script('gd-photo-gallery-gutenberg', 'gdphotogalleryblock', array(
        'gdgallery' => $options
    ));
    if (function_exists('register_block_type')) {
        register_block_type('photo-gallery-image/index', array(
            'editor_script' => 'gd-photo-gallery-gutenberg',
            'editor_style' => 'gd-photo-gallery-gutenberg',
        ));
    }
}
add_action( 'init', 'gutenberg_gd_photo_gallery' );

function gd_photo_gallery_gutenberg_category( $categories, $post ) {
    if ( $post->post_type !== 'post' ) {
        return $categories;
    }
    return array_merge(
        $categories,
        array(
            array(
                'slug' => 'photo-gallery-image',
                'title' => __( 'GrandWP Gallery', 'gdgallery' ),
                'icon'  => 'format-gallery',
            ),
        )
    );
}
add_filter( 'block_categories', 'gd_photo_gallery_gutenberg_category', 10, 2 );


if (get_option("gdgallery_removetablesuninstall") == "on") {
    register_uninstall_hook(__FILE__, array('GDGallery\Database\Uninstall', 'run'));
}

require 'autoload.php';

require 'GDGallery.php';


/**
 * Main instance of GDGallery.
 *
 * Returns the main instance of GDGallery to prevent the need to use globals.
 *
 * @return \GDGallery\GDGallery
 */

function GDGallery()
{
    return \GDGallery\GDGallery::instance();
}

$GLOBALS['GDGallery'] = GDGallery();




