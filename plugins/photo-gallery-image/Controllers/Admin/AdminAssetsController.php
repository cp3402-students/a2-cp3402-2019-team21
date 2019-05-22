<?php

namespace GDGallery\Controllers\Admin;

use GDGallery;


class AdminAssetsController
{
    public static function init()
    {
        add_action('admin_enqueue_scripts', array(__CLASS__, 'adminStyles'));
        add_action('admin_enqueue_scripts', array(__CLASS__, 'adminScripts'));
    }

    /**
     * @param $hook
     */
    public static function adminStyles($hook)
    {

        if ($hook === \GDGallery()->admin->Pages['main_page'] || $hook === \GDGallery()->admin->Pages['styles'] || $hook === \GDGallery()->admin->Pages['settings'] || $hook === \GDGallery()->admin->Pages['featured_plugins']) {


            wp_enqueue_style('jqueryUI', \GDGallery()->pluginUrl() . '/resources/assets/css/jquery-ui.min.css');

            wp_enqueue_style('fontAwesome', \GDGallery()->pluginUrl() . '/resources/assets/css/font-awesome.min.css', false);

            wp_enqueue_style('gdgallerytoastrjs', \GDGallery()->pluginUrl() . '/resources/assets/css/admin/toastr.css');

            wp_enqueue_style('gdgalleryBannerStyle', \GDGallery()->pluginUrl() . '/resources/assets/css/admin/banner.css');

            wp_enqueue_style('roboto', 'https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&amp;subset=cyrillic');

        }


        if ($hook === \GDGallery()->admin->Pages['main_page'] || $hook === \GDGallery()->admin->Pages['settings']) {
            wp_enqueue_style('gdgalleryAdminStyles', \GDGallery()->pluginUrl() . '/resources/assets/css/admin/main.css');
        }

        if ($hook == \GDGallery()->admin->Pages["main_page"]) {
            wp_enqueue_style('gdgallery_modal', \GDGallery()->pluginUrl() . '/resources/assets/css/admin/gdgallery-modal.css', false);
        }

        if ($hook === \GDGallery()->admin->Pages['settings']) {
            wp_enqueue_style('gdgallerySettings', \GDGallery()->pluginUrl() . '/resources/assets/css/admin/settings.css');
        }

        if ($hook === \GDGallery()->admin->Pages['styles'] || $hook === \GDGallery()->admin->Pages['featured_plugins']) {
            wp_enqueue_style('gdgalleryStyleSettings', \GDGallery()->pluginUrl() . '/resources/assets/css/admin/style_settings.css');
        }

    }

    /**
     * @param $hook
     */
    public static function adminScripts($hook)
    {
        wp_enqueue_script('jquery');

        wp_enqueue_script('jqueryUI', \GDGallery()->pluginUrl() . '/resources/assets/js/jquery-ui.min.js');


        wp_enqueue_script('masonry');


        wp_enqueue_script('gdgallerytoastrjs', \GDGallery()->pluginUrl() . '/resources/assets/js/admin/toastr.min.js');

        if ($hook === \GDGallery()->admin->Pages['main_page']) {

            wp_enqueue_media();


            if (isset($_GET['task']) && $_GET['task'] == 'edit_gallery') {
                wp_enqueue_script('gdgallery_modal', \GDGallery()->pluginUrl() . '/resources/assets/js/admin/gdgallery_modal.js', array('jquery'), false, true);
            }

            wp_enqueue_script('gdgalleryAdminJs', \GDGallery()->pluginUrl() . '/resources/assets/js/admin/main.js', array('jquery', 'jqueryUI'), false, true);
        }

        if (in_array($hook, array('post.php', 'post-new.php'))) {
            wp_enqueue_script("gdgalleryInlinePopup", \GDGallery()->pluginUrl() . "/resources/assets/js/admin/inline-popup.js", array('jquery'), false, true);
        }

        if ($hook === \GDGallery()->admin->Pages['settings']) {
            wp_enqueue_script('gdgallerySettings', \GDGallery()->pluginUrl() . '/resources/assets/js/admin/settings.js', array('jquery'), false, true);
        }

        if ($hook === \GDGallery()->admin->Pages['styles']) {
            wp_enqueue_script('gdgallery_styles', \GDGallery()->pluginUrl() . '/resources/assets/js/admin/styles_settings.js', array('jquery', 'jqueryUI', 'gdgallerytoastrjs'), false, true);
            wp_enqueue_script('gdgallery_jscolor', \GDGallery()->pluginUrl() . '/resources/assets/js/admin/jscolor.js', array(), false, true);
        }


        self::localizeScripts();

    }

    public static function localizeScripts()
    {

        wp_localize_script('gdgalleryAdminJs', 'gdgallery_save', array(
            'nonce' => wp_create_nonce('gdgallery_save_gallery'),
        ));

        wp_localize_script('gdgalleryInlinePopup', 'gdgallery_inlinePopup', array(
            'nonce' => wp_create_nonce('gdgallery_save_shortcode_options'),
        ));

        wp_localize_script('gdgallerySettings', 'gdgallery_settingsSave', array(
            'nonce' => wp_create_nonce('gdgallery_save_plugin_settings'),
        ));

    }
}