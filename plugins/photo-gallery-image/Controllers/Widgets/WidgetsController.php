<?php

namespace GDGallery\Controllers\Widgets;

class WidgetsController
{
    public static function init()
    {
        register_widget('GDGallery\Controllers\Widgets\GalleryWidgetController');
    }
}