<?php

namespace GDGallery\Controllers\Frontend;

use GDGallery\Models\Gallery;

class AjaxController
{

    public static function init()
    {
        add_action('wp_ajax_gdgallery_get_items', array(__CLASS__, 'getItems'));
        add_action('wp_ajax_nopriv_gdgallery_get_items', array(__CLASS__, 'getItems'));
    }

    public static function getItems()
    {
        global $wpdb;

        $id_gallery = intval($_REQUEST["id_gallery"]);
        $start = intval($_REQUEST["offset"]);

        if ($id_gallery <= 0) {
            return false;
        }

        $gallery = new Gallery(array("id_gallery" => $id_gallery));

        $total_count = $gallery->getItemsCount();
        $order_inf = $gallery->getOrderInfo();

        if($order_inf["order"] == 'RAND') {
            $query = $wpdb->prepare("SELECT * FROM " . $gallery::getItemsTableName() . " where id_gallery = '%d' order by RAND() LIMIT " . $start, $id_gallery);
        } else {
            $query = $wpdb->prepare("SELECT * FROM " . $gallery::getItemsTableName() . " where id_gallery = '%d' order by " . $order_inf["sort"] . " " . $order_inf["order"] . " LIMIT " . $start, $id_gallery);
        }
        $items = $wpdb->get_results($query);
        foreach ($items as $key => $val) {
            if ($val->id_post != 0) {
                $post = get_post($val->id_post);
                $items[$key]->url = wp_get_attachment_url($post->ID);
//                $items[$key]->name = $post->post_title;
                $items[$key]->name = $val->name;
            }
        }


        $show_button = (count($items) < $total_count) ? 1 : 0;

        echo json_encode(array(
            "success" => true,
            "data" => $items,
            "show_button" => $show_button
        ));
        die();
    }

}