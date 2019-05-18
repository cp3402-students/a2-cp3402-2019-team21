<?php

namespace GDGallery\Helpers;

class View
{
    /**
     * @param $view
     * @param string $path
     * @param string $defaultPath
     *
     * @return mixed
     */
    public static function locate($view, $path = '', $defaultPath = '')
    {
        if (!$path) {
            $path = \GDGallery()->viewPath();
        }
        if (!$defaultPath) {
            $defaultPath = \GDGallery()->pluginPath() . '/resources/views/';
        }

        $template = locate_template(
            array(
                trailingslashit($path) . $view,
                $view
            )
        );

        if (!$template) {
            $template = $defaultPath . $view;
        }

        return $template;
    }

    /**
     * @param $view
     * @param array $args
     * @param string $path
     * @param string $defaultPath
     */
    public static function render($view, $args = array(), $path = '', $defaultPath = '')
    {

        if ($args && is_array($args)) {

            extract($args);

        }

        $located = self::locate($view, $path, $defaultPath);

        if (!file_exists($located)) {

            _doing_it_wrong(__FUNCTION__, sprintf('<code>%s</code> does not exist.', $located), '2.2.0');

            return;

        }

        include($located);
    }

    public static function buffer($view, $args = array(), $path = '', $defaultPath = '')
    {
        ob_start();
        self::render($view, $args, $path, $defaultPath);
        return ob_get_clean();
    }

    /**
     * @param int $total
     * @param string $method
     * @return bool
     */
    public static function pagination($total, $perpage = 25, $page = 1)
    {
        if ($total == 0) return '';

        if ($perpage == 0) $perpage = 25;

        $pages = ceil($total / $perpage);

        if ($page > $pages) {
            $page = $pages;
        }

        $from = ($page - 1) * $perpage + 1;

        $to = (int)$from + (int)$perpage - 1;

        if ($to > $total) $to = $total;

        $actual_link = "?";
        foreach ($_GET as $key => $value) {
            if ($key != 'paged') {
                $actual_link .= "{$key}=$value&amp;";
            }

        }

        $urlLink = $actual_link;
        $next_link = $urlLink . 'paged=' . ($page + 1);
        $prev_link = $urlLink . 'paged=' . ($page - 1);

        if ($pages > 1):
            $pagination = '<div class="page-navigation">';
            $pagination .= '<div class="buttons">';
            $pagination .= '<div style="display: inline-block;margin-right: 50px;color: #999;">Displaying items ' . $from . ' to ' . $to . ' from ' . $total . '</div>';

            if ($page > 1) {
                $pagination .= '<a href="' . esc_url($prev_link) . '" class="prev"><i class="fa fa-chevron-left"></i></a>';
            }

            if ($page - 5 <= 0) $paginationStart = 1;
            else $paginationStart = $page - 5;

            if ($page + 5 >= 0) $pagination_end = $pages;
            else $pagination_end = $page + 5;

            if ($page <= 5) {
                for ($i = $paginationStart; $i <= $pagination_end; $i++) {
                    $class = '';
                    if ($i == $page) $class = 'current-page';
                    $pagination .= '<a class="' . $class . '" href="' . esc_url($urlLink . 'paged=' . $i) . '" >' . $i . '</a>';
                }
            }

            if ($page < $pages) {
                $pagination .= '<a href="' . esc_url($next_link) . '" class="next"><i class="fa fa-chevron-right"></i></a>';
            }


            $pagination .= '</div></div>';
        else : $pagination = '';
        endif;

        return $pagination;
    }

}