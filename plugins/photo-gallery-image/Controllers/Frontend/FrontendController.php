<?php

namespace GDGallery\Controllers\Frontend;


class FrontendController
{
    public function __construct()
    {
        add_shortcode('gdgallery_gallery', array('GDGallery\Controllers\Frontend\ShortcodeController', 'run'));
        new GalleryPreviewController();
        FrontendAssetsController::init();
    }
}