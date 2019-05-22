<?php

namespace GDGallery\Controllers\Admin;

use GDGallery\Helpers\View;
use GDGallery\Models\Gallery;


class AdminController
{
    /**
     * Array of pages in admin
     *
     * @var array
     */
    public $Pages;

    public function __construct()
    {


        add_action('admin_footer', array('GDGallery\Controllers\Admin\ShortcodeController', 'showInlinePopup'));

        add_action('media_buttons_context', array('GDGallery\Controllers\Admin\ShortcodeController', 'showEditorMediaButton'));

        add_action('admin_menu', array($this, 'adminMenu'), 1);

        add_action('admin_init', array(__CLASS__, 'deleteGallery'), 1);

        add_action('admin_init', array(__CLASS__, 'duplicateGallery'), 1);

        add_action('admin_init', array(__CLASS__, 'createGallery'), 1);

    }


    public static function isRequest($page, $task, $method = 'GET')
    {
        return ($_SERVER['REQUEST_METHOD'] === $method && isset($_GET['page']) && $_GET['page'] === $page && isset($_GET['task']) && $_GET['task'] === $task);
    }

    /**
     * Add admin menu pages
     */
    public function adminMenu()
    {
        $this->Pages['main_page'] = add_menu_page(__('GrandWP Gallery', GDGALLERY_TEXT_DOMAIN), __('GrandWP Gallery', GDGALLERY_TEXT_DOMAIN), 'manage_options', 'gdgallery', array(
            $this,
            'mainPage'
        ), \GDGallery()->pluginUrl() . '/resources/assets/images/icons/logo_small.png');

        $this->Pages['main_page'] = add_submenu_page('gdgallery', __('Galleries', GDGALLERY_TEXT_DOMAIN), __('Galleries', GDGALLERY_TEXT_DOMAIN), 'manage_options', 'gdgallery', array(
            $this,
            'mainPage'
        ));

        $this->Pages['styles'] = add_submenu_page('gdgallery', __('Views / Styles (Pro)', GDGALLERY_TEXT_DOMAIN), __('Views / Styles (Pro)', GDGALLERY_TEXT_DOMAIN), 'manage_options', 'gdgallery_styles', array(
            $this,
            'stylesPage'
        ));

        $this->Pages['settings'] = add_submenu_page('gdgallery', __('Settings', GDGALLERY_TEXT_DOMAIN), __('Settings', GDGALLERY_TEXT_DOMAIN), 'manage_options', 'gdgallery_settings', array(
            $this,
            'settingsPage'
        ));

        $this->Pages['featured_plugins'] = add_submenu_page('gdgallery', __('Featured Plugins', GDGALLERY_TEXT_DOMAIN), __('Featured plugins', GDGALLERY_TEXT_DOMAIN), 'manage_options', 'gdgallery_featured_plugins', array(
            $this,
            'featuredPage'
        ));
    }


    /**
     * Initialize main page
     */
    public function mainPage()
    {
        View::render('admin/header-banner.php');

        if (!isset($_GET['task'])) {

            View::render('admin/galleries-list.php');

        } else {

            $task = $_GET['task'];

            switch ($task) {
                case 'edit_gallery':


                    if (!isset($_GET['id'])) {

                        \GDGallery()->admin->printError(__('Missing "id" parameter.', GDGALLERY_TEXT_DOMAIN));

                    }

                    $id = absint($_GET['id']);

                    if (!$id) {

                        \GDGallery()->admin->printError(__('"id" parameter must be not negative integer.', GDGALLERY_TEXT_DOMAIN));

                    }

                    $gallery = new Gallery(array('id_gallery' => $id));

                    View::render('admin/edit-gallery.php', array('gallery' => $gallery));

                    break;
            }

        }

    }

    public function settingsPage()
    {
        View::render('admin/header-banner.php');
        View::render('admin/settings.php');
    }

    public function featuredPage()
    {
        View::render('admin/header-banner.php');
        View::render('admin/featured-plugins.php');
    }

    public function stylesPage()
    {
        View::render('admin/header-banner.php');

        $builder = new SettingsController();

        $builder->settingsFileds();
    }


    public function printError($error_message, $die = true)
    {

        $str = sprintf('<div class="error"><p>%s&nbsp;<a href="#" onclick="window.history.back()">%s</a></p></div>', $error_message, __('Go back', GDGALLERY_TEXT_DOMAIN));

        if ($die) {

            wp_die($str);

        } else {
            echo $str;
        }

    }

    public static function deleteGallery()
    {
        if (!self::isRequest('gdgallery', 'remove_gallery', 'GET')) {
            return;
        }

        if (!isset($_GET['id'])) {
            wp_die(__('"id" parameter is required', GDGALLERY_TEXT_DOMAIN));
        }

        $id = $_GET['id'];

        if (absint($id) != $id) {
            wp_die(__('"id" parameter must be non negative integer', GDGALLERY_TEXT_DOMAIN));
        }

        if (!isset($_GET['_wpnonce']) || !wp_verify_nonce($_GET['_wpnonce'], 'gdgallery_remove_gallery_' . $id)) {
            wp_die(__('Security check failed', GDGALLERY_TEXT_DOMAIN));
        }

        Gallery::delete($id);

        $location = admin_url('admin.php?page=gdgallery');

        header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        header("Location: $location");

        exit;

    }


    public static function duplicateGallery()
    {


        if (!self::isRequest('gdgallery', 'duplicate_gallery', 'GET')) {
            return;
        }

        if (!isset($_GET['id'])) {

            \GDGallery()->admin->printError(__('Missing "id" parameter.', GDGALLERY_TEXT_DOMAIN));

        }

        $id = absint($_GET['id']);

        if (!$id) {

            \GDGallery()->admin->printError(__('"id" parameter must be not negative integer.', GDGALLERY_TEXT_DOMAIN));

        }

        if (!isset($_GET['_wpnonce']) || !wp_verify_nonce($_GET['_wpnonce'], 'gdgallery_duplicate_gallery_' . $id)) {

            \GDGallery()->admin->printError(__('Security check failed.', GDGALLERY_TEXT_DOMAIN));

        }

        ////  continue here

        $gallery = new Gallery(array('id_gallery' => $id));

        $gallery->setName('Copy of ' . $gallery->getName());

        $gallery = $gallery->duplicateGallery();

        /**
         * after the gallery is created we need to redirect user to the edit page
         */

        if ($gallery && is_int($gallery)) {

            $location = admin_url('admin.php?page=gdgallery&task=edit_gallery&id=' . $gallery);

            $location = wp_nonce_url($location, 'gdgallery_edit_gallery_' . $gallery);

            $location = html_entity_decode($location);

            header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
            header("Location: $location");

            exit;

        } else {

            wp_die(__('Problems occured while creating new Gallery.', GDGALLERY_TEXT_DOMAIN));

        }

    }

    public static function createGallery()
    {
        if (!self::isRequest('gdgallery', 'create_new_gallery', 'GET')) {
            return;
        }

        if (!isset($_GET['_wpnonce']) || !wp_verify_nonce($_GET['_wpnonce'], 'gdgallery_create_new_gallery')) {
            \GDGallery()->admin->printError(__('Security check failed.', GDGALLERY_TEXT_DOMAIN));
        }

        $gallery = new Gallery();

        $gallery = $gallery->setName('')->save();

        /**
         * after the gallery is created we need to redirect user to the edit page
         */
        if ($gallery && is_int($gallery)) {

            $location = admin_url('admin.php?page=gdgallery&task=edit_gallery&id=' . $gallery);

            $location = wp_nonce_url($location, 'gdgallery_edit_gallery_' . $gallery);

            $location = html_entity_decode($location);

            header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
            header("Location: $location");

            exit;

        } else {

            wp_die(__('Problems occured while creating new gallery.', GDGALLERY_TEXT_DOMAIN));

        }

    }


}