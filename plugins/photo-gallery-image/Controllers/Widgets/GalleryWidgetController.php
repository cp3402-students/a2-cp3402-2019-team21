<?php

namespace GDGallery\Controllers\Widgets;

use GDGallery\Helpers\View;

class GalleryWidgetController extends \WP_Widget
{
    /**
     * Widget constructor.
     */
    public function __construct()
    {
        parent::__construct(
            'GDGallery_Widget',
            __('GrandWP Gallery', GDGALLERY_TEXT_DOMAIN),
            array('description' => __('GrandWP Gallery', GDGALLERY_TEXT_DOMAIN),)
        );
    }

    /**
     * @param array $args
     * @param array $instance
     */
    public function widget($args, $instance)
    {
        extract($args);

        if (isset($instance['gdgallery_gallery_id']) && (absint($instance['gdgallery_gallery_id']) == $instance['gdgallery_gallery_id'])) {
            $gdgallery_gallery_id = $instance['gdgallery_gallery_id'];

            $title = apply_filters('widget_title', $instance['title']);

            if (!empty($title)) {
                echo $title;
            }

            echo do_shortcode("[gdgallery_gallery id_gallery='{$gdgallery_gallery_id}']");
        } else {
            echo __('Select Gallery to Display', GDGALLERY_TEXT_DOMAIN);
        }
    }

    /**
     * @param array $new_instance
     * @param array $old_instance
     * @return array
     */
    public function update($new_instance, $old_instance)
    {
        $instance = array();
        $instance['gdgallery_gallery_id'] = strip_tags($new_instance['gdgallery_gallery_id']);
        $instance['title'] = strip_tags($new_instance['title']);

        return $instance;
    }

    /**
     * @param array $instance
     * @var $instance
     * @return string|void
     */
    public function form($instance)
    {
        $galleryInstance = (isset($instance['gdgallery_gallery_id']) ? $instance['gdgallery_gallery_id'] : 0);
        $title = (isset($instance['title']) ? $instance['title'] : '');

        View::render('admin/Widgets/GalleryWidget.php', array('widget' => $this, 'title' => $title, 'galleryInstance' => $galleryInstance));
    }
}