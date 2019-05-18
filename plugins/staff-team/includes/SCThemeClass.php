<?php
class SCThemeClass {

    protected static $instance = null;
    public static $post_type = 'cont_theme';
    public static $prefix = 'cont';
    
    private function __construct() {
        add_action('init', array($this, 'themesCustomPostType'));
        add_filter('post_updated_messages', array($this, 'theme_messages'));
        add_filter('post_row_actions', array($this,'deleteThemeQEdit'), 10, 2);
        add_filter('manage_edit-cont_theme_columns' , array($this,'contThemeColumns'));
        add_action( 'manage_cont_theme_posts_custom_column' ,  array($this,'customThemeColumn'), 10, 2 );
        add_filter('bulk_actions-edit-'.self::$post_type, array($this,'themeBulkActions'));
        add_action('do_meta_boxes', array($this,'remove_image_box'));
    }

    public function themesCustomPostType() {
        $labels = array(
            'name' => 'Styles and Colors',
            'add_new' => 'Add Theme',
            'add_new_item' => '',
            'search_items' => 'Search'
            );
        $args = array('label' => null, 'labels' => $labels, 'description' => '', 'public' => false,
            'publicly_queryable' => false, 'exclude_from_search' => null, 'show_ui' => true,
            'show_in_menu' => 'edit.php?post_type=cont_theme', 'capability_type' => 'post', 'hierarchical' => false,
            'supports' => array('title'), 'taxonomies' => array(''), 'has_archive' => false, 'rewrite' => true,
            'query_var' => true, 'show_in_nav_menus' => false,
            'register_meta_box_cb' => array($this, 'addThemeMetabox'));
        register_post_type(self::$post_type, $args);
    }

    public function addThemeMetabox() {
        add_meta_box('spider_theme_metabox', 'Theme settings', array($this,
            'displayThemeMetaBox'), self::$post_type, 'advanced', 'default');
    }

    public function displayThemeMetaBox($post) {
        $post_id = $post->ID;
        $param_values = get_post_meta($post_id, 'params', TRUE);
        if (!is_array($param_values))
            $param_values = array();
		
        include_once(SC_DIR . '/views/admin/SCViewThemeStyle.php');
    }

    public function theme_messages($messages) {
        global $post, $post_ID;
        ;
        $url1 = '<a href="' . get_permalink($post_ID) . '">';
        $url2 = 'Theme';
        $url3 = '</a>';
        $s1 = 'Theme';

        $messages[self::$post_type] = array(
            1 => sprintf('%4$s updated.', $url1, $url2, $url3, $s1),
            4 => sprintf('%4$s updated. ', $url1, $url2, $url3, $s1),
            6 => sprintf('%4$s published.', $url1, $url2, $url3, $s1),
            7 => sprintf('%4$s saved.', $url1, $url2, $url3, $s1),
            8 => sprintf('%4$s submitted. ', $url1, $url2, $url3, $s1),
            10 => sprintf('%4$s draft updated.', $url1, $url2, $url3, $s1)
        );

        if ($post->post_type == self::$post_type) {
            $notices = get_option(self::$prefix.'_not_writable_warning');

            if (empty($notices)) {
                return $messages;
            }

            foreach ($notices as $post_id => $mm) {

                if ($post->ID == $post_id) {
                    $notice = '';

                    foreach ($mm as $key) {
                        $notice = $notice . ' <p style="color:red;">' . $key . '</p> ';
                    }
                    foreach ($messages[self::$post_type] as $i => $message) {
                        $messages[self::$post_type][$i] = $message . $notice;
                    }
                    unset($notices[$post_id]);
                    update_option(self::$prefix.'_not_writable_warning', $notices);
                    break;
                }
            }
        }
        return $messages;
    }
    
    public function contThemeColumns($columns){
        unset($columns['date']);
        $new_columns = array(
			'active' => 'Activation',
			'theme_date' => 'Date'       
		);
        return array_merge($columns, $new_columns);
    }
    
    public function customThemeColumn($column, $post_id) {
        $active_theme = 23;
        switch ($column) {
            case 'active' :
                if($active_theme == $post_id){
                    echo "<p class ='cont_theme_enable' theme-id='". $post_id."' style='color:green;'>Active</p>";
                }else{
                    echo  '<a href="#" class="cont_theme_activate" theme-id="'.$post_id.'">Activate</a>';
                }
                break;
            case 'theme_date' :
                $post = get_post($post_id,'OBJECT');
                echo  $post->post_date;
                break;
        }
    }
    
    public function deleteThemeQEdit($actions, $post) {        
        if ($post->post_type == self::$post_type) {
            unset($actions["inline hide-if-no-js"]);
        }
        return $actions;
    }

    public  function themeBulkActions($actions){
        unset( $actions[ 'edit' ] );
        return $actions;
    }
    
    public function remove_image_box() {
        remove_meta_box('postimagediv',  self::$post_type,'side');
    }
    
    public static function getInstance() {
        if (null == self::$instance) {
            self::$instance = new self;
        }
        return self::$instance;
    }

}
