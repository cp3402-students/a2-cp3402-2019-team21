<?php

namespace GDGallery\Controllers\Admin;

use GDGallery\Models\Gallery;
use GDGallery\Models\Settings;
use GDGallery\Core\Model;

/**
 * Class AjaxController
 * @package GDGALLERY\Controllers\Admin
 */
class AjaxController
{
    public static function init()
    {
        add_action('wp_ajax_gdgallery_save_gallery', array(__CLASS__, 'saveGallery'));

        add_action('wp_ajax_gdgallery_save_gallery_images', array(__CLASS__, 'saveGalleryImages'));

        add_action('wp_ajax_gdgallery_remove_gallery_items', array(__CLASS__, 'removeGalleryItems'));

        add_action('wp_ajax_gdgallery_add_gallery_image', array(__CLASS__, 'AddGalleryImage'));

        add_action('wp_ajax_gdgallery_edit_thumbnail', array(__CLASS__, 'EditGalleryThumbnail'));

        add_action('wp_ajax_gdgallery_add_gallery_video', array(__CLASS__, 'AddGalleryVideo'));

        add_action('wp_ajax_gdgallery_save_settings', array(__CLASS__, 'saveGallerySettings'));

        add_action('wp_ajax_gdgallery_save_plugin_settings', array(__CLASS__, 'savePluginSettings'));

    }


    public static function saveGallery()
    {
        if (!isset($_REQUEST['nonce']) || !wp_verify_nonce($_REQUEST['nonce'], 'gdgallery_save_gallery')) {
            die('security check failed');
        }

        $gallery_id = absint($_REQUEST['gallery_id']);

        $gallery_data = str_replace("gdgallery_", "", $_REQUEST["formdata"]);


        $gallery = new Gallery(array('id_gallery' => $gallery_id));
        $gallery_data_arr = array();
        parse_str($gallery_data, $gallery_data_arr);

        if (isset($gallery_data_arr["items"])) {
            unset($gallery_data_arr["items"]);
        }

        if (isset($gallery_data_arr["select_all_items"])) {
            unset($gallery_data_arr["select_all_items"]);
        }

        $gallery_data_arr["custom_css"] = str_replace("#container", "#gdgallery_container", $gallery_data_arr["custom_css"]);
        $gallery_data_arr["custom_css"] = sanitize_text_field($gallery_data_arr["custom_css"]);

        if (Model::isset_table_column(Gallery::getTableName(), "show_title")) {
            $gallery_data_arr["show_title"] = (isset($gallery_data_arr["show_title"])) ? 1 : 0;
        }


        $ordering = (isset($gallery_data_arr["ordering"])) ? $gallery_data_arr["ordering"] : array();
        unset($gallery_data_arr["ordering"]);

        if (!empty($ordering)) {
            $gallery->updateImageOrdering($ordering);
        }

        $updated = $gallery->saveGallery($gallery_data_arr);
        if ($updated) {
            echo 1;
            die();
        } else {
            die('something went wrong');
        }
    }

    public static function saveGalleryImages()
    {
        if (!isset($_REQUEST['nonce']) || !wp_verify_nonce($_REQUEST['nonce'], 'gdgallery_save_gallery')) {
            die('security check failed');
        }

        $gallery_id = absint($_REQUEST['gallery_id']);

        $gallery_data = str_replace("gdgallery_images_", "", $_REQUEST["formdata"]);

        $gallery = new Gallery(array('id_gallery' => $gallery_id));
        $gallery_data_arr = array();
        parse_str($gallery_data, $gallery_data_arr);


        $updated = null;

        $updated = $gallery->saveGalleryImages($gallery_data_arr);


        if ($updated) {
            echo 1;
            die();
        } else {
            die('something went wrong');
        }
    }

    public static function removeGalleryItems()
    {
        if (!isset($_REQUEST['nonce']) || !wp_verify_nonce($_REQUEST['nonce'], 'gdgallery_save_gallery')) {
            die('security check failed');
        }

        $gallery_id = absint($_REQUEST['gallery_id']);

        $gallery = new Gallery(array('id_gallery' => $gallery_id));

        $updated = null;
        if (!empty($_REQUEST["formdata"])) {
            $updated = $gallery->removeGalleryItems($_REQUEST["formdata"]);
        }

        if ($updated) {
            echo 1;
            die();
        } else {
            die('something went wrong');
        }
    }


    public static function AddGalleryImage()
    {
        if (!isset($_REQUEST['nonce']) || !wp_verify_nonce($_REQUEST['nonce'], 'gdgallery_save_gallery')) {
            die('security check failed');
        }

        $gallery_id = absint($_REQUEST['gallery_id']);

        $gallery_data = $_REQUEST["formdata"];

        $gallery = new Gallery(array('id_gallery' => $gallery_id));

        $inserted = null;
        $inserted = $gallery->addGalleryImage($gallery_data, $gallery_id);

        if ($inserted) {
            echo 1;
            die();
        } else {
            die('something went wrong');
        }
    }

    public static function EditGalleryThumbnail()
    {
        if (!isset($_REQUEST['nonce']) || !wp_verify_nonce($_REQUEST['nonce'], 'gdgallery_save_gallery')) {
            die('security check failed');
        }

        $gallery_id = absint($_REQUEST['gallery_id']);
        $image_id = absint($_REQUEST['image_id']);
        $gallery_data = $_REQUEST["formdata"];
        $gallery = new Gallery(array('id_gallery' => $gallery_id));

        $edited = null;
        $edited = $gallery->EditGalleryThumbnail($gallery_data, $image_id);

        if ($edited == 1) {
            echo $edited;
            die();
        } else {
            die('something went wrong');
        }

    }

    public static function AddGalleryVideo()
    {
        if (!isset($_REQUEST['nonce']) || !wp_verify_nonce($_REQUEST['nonce'], 'gdgallery_save_gallery')) {
            die('security check failed');
        }


        $gallery_id = absint($_REQUEST['gallery_id']);

        $gallery_data = $_REQUEST["formdata"];

        $gallery = new Gallery(array('id_gallery' => $gallery_id));
        $gallery_data_arr = array();
        parse_str($gallery_data, $gallery_data_arr);

        $gallery_data_arr["gdgallery_id_gallery"] = $gallery_id;


        $inserted = null;
        $inserted = $gallery->addGalleryVideo($gallery_data_arr);


        if ($inserted) {
            echo 1;
            die();
        } else {
            die('something went wrong');
        }
    }


    public static function saveGallerySettings()
    {

        $settings_arr = array();
        parse_str($_REQUEST["formdata"], $settings_arr);
        $settings = new Settings();
        foreach ($settings_arr["settings"] as $key => $item) {
            $settings->setOption($key, $item);
        }

        echo 'ok';
        die;
    }


    public static function savePluginSettings()
    {
        if (!isset($_REQUEST['nonce']) || !wp_verify_nonce($_REQUEST['nonce'], 'gdgallery_save_plugin_settings')) {
            die('security check failed');
        }


        $settings_data = $_REQUEST['formData'];

        foreach ($settings_data as $input) {
            //$saved[] = \GDGallery()->settings->setOption($input['name'], $input['value']);
            $saved[] = update_option($input["name"], $input["value"]);
        }

        if (!empty($saved)) {
            echo json_encode(array("success" => 1));
            die();
        } else {
            die('something went wrong');
        }

    }


}