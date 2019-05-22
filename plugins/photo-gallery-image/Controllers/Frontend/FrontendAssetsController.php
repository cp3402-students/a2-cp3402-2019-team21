<?php

namespace GDGallery\Controllers\Frontend;

use GDGallery\Models\Gallery;
use GDGallery\Models\Settings;

class FrontendAssetsController
{

    public static function init()
    {
        add_action('gdgalleryShortcodeScripts', array(__CLASS__, 'addScripts'));

        add_action('gdgalleryShortcodeScripts', array(__CLASS__, 'addStyles'));

        add_action('wp_head', array(__CLASS__, 'addAjaxUrlJs'));

    }

    /**
     * Add Scripts
     *
     */
    public static function addScripts($GalleryId)
    {
        wp_enqueue_script("jquery");
       // wp_head();
        $gallery = new Gallery(array('id_gallery' => $GalleryId));

        wp_enqueue_script("gdgalleryunite", \GDGallery()->pluginUrl() . "/resources/assets/js/frontend/unitegallery.js", array('jquery'), false, true);
        wp_enqueue_script('gdgalleryFrontJs', \GDGallery()->pluginUrl() . '/resources/assets/js/frontend/main.js', array('jquery'), false, true);


        self::localizeScripts($GalleryId);

    }


    /**
     * Define the 'ajaxurl' JS variable, used by themes and plugins as an AJAX endpoint.
     *
     */
    public static function addAjaxUrlJs()
    {
        ?>

        <script
                type="text/javascript">var ajaxurl = '<?php echo admin_url('admin-ajax.php', is_ssl() ? 'admin' : 'http'); ?>';</script>
        <?php
    }

    /**
     * Add Styles
     */
    public static function addStyles()
    {
        wp_enqueue_style('fontAwesome', \GDGallery()->pluginUrl() . '/resources/assets/css/font-awesome.min.css', false);
        wp_enqueue_style('gdgalleryunit', \GDGallery()->pluginUrl() . '/resources/assets/css/frontend/unite-gallery.css');
    }

    public static function localizeScripts($id)
    {

        $gallery = new Gallery(array("id_gallery" => $id));
        $data = $gallery->getGallery();
        $view = null;
        $options = array();
        if ($data->view_type == 0) {
            $view = "justified";
        } elseif ($data->view_type == 1) {
            $view = "tiles";
        }
        elseif ($data->view_type == 2) {
	        $view = "carousel";
        }
        elseif ($data->view_type == 3) {
	        $view = "slider";
        }else {
	        $view = "grid";
        }

	    if (!is_null($view)) {

            $options = \GDGallery()->settings->getOptionsByView($view);
        }

        wp_localize_script('gdgalleryFrontJs', 'mainjs', array(
            'options' => $options
        ));
    }

}