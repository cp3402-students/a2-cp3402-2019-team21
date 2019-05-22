<?php
namespace GDGallery\Controllers\Frontend;

use GDGallery\Models\Gallery;

class GalleryPreviewController
{
    /**
     * Gallery ID
     *
     * @var int
     */
    private $gallery;

    /***
     * @param
     *
     */
    public function __construct()
    {
        if (!isset($_GET['gdgallery_preview'])) return;

        $this->gallery = intval($_GET['gdgallery_preview']);

        add_filter('the_title', array($this, 'theTitle'));
        add_filter('the_content', array($this, 'theContent'), 9001);
        add_filter('template_include', array($this, 'templateInclude'));
        add_action('pre_get_posts', array($this, 'preGetPosts'));
    }

    /**
     * @return string
     */
    public function theTitle($title)
    {
        if (!in_the_loop()) return $title;

        $gallery = new Gallery(array('id_gallery' => $this->gallery));
        $title = $gallery->getName();

        return $title . " " . __('Gallery Preview', GDGALLERY_TEXT_DOMAIN);
    }

    /**
     * @return string
     */
    public function theContent()
    {
        if (!is_user_logged_in()) return __('Log In first in order to preview the Gallery.', GDGALLERY_TEXT_DOMAIN);

        return do_shortcode("[gdgallery_gallery id_gallery='{$this->gallery}']");
    }


    public static function previewUrl($gallery, $return_html = true)
    {

        if ($return_html) {
            $html = '<a target="_blank" class="gdgallery-preview" href="' . home_url() . '/?gdgallery_preview=' . $gallery . '">' . __('Preview Gallery', GDGALLERY_TEXT_DOMAIN) . '</a>';

            return $html;
        } else {
            return home_url() . '/?gdgallery_preview=' . $gallery;

        }

    }


    public static function templateInclude()
    {
        return locate_template(array('page.php', 'single.php', 'index.php'));
    }


    public static function preGetPosts($query)
    {
        $query->set('posts_per_page', 1);
    }
}