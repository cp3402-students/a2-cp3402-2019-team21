<?php

namespace GDGallery\Controllers\Frontend;

use GDGallery\Helpers\View;
use GDGallery\Models\Gallery;

class ShortcodeController
{

    public static function run($attrs)
    {

        $attrs = shortcode_atts(array(
            'id_gallery' => false,
        ), $attrs);


        if (!$attrs['id_gallery'] || absint($attrs['id_gallery']) != $attrs['id_gallery']) {
            throw new \Exception('"id" parameter is required and must be not negative integer.');
        }

        do_action('gdgalleryShortcodeScripts', $attrs['id_gallery']);

        return self::show($attrs['id_gallery']);
    }

    private static function show($id)
    {
        ob_start();


        $gallery = new Gallery(array('id_gallery' => $id));

        View::render('frontend/gallery.php', array('gallery' => $gallery));

        return ob_get_clean();
    }

}