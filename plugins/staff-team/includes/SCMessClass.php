<?php
class SCMessClass {

    protected static $instance = null;
    public static $post_type = 'cont_mess';
    public static $taxonomy_slug = 'cont_category';

    const prefix = 'mess';

    private function __construct() {
        add_action('init', array($this, 'contMessPostType'));
        add_action('admin_head', array($this, 'messPopUp'));
        add_filter('manage_edit-cont_mess_columns', array($this, 'contMessColumns'));
        add_action('manage_cont_mess_posts_custom_column', array($this, 'customMessColumn'), 10, 2);
        add_action('wp_ajax_del_mess', array($this, 'ajaxDelMess'));
        add_action('wp_ajax_view_mess', array($this, 'ajaxViewMess'));
        add_filter('post_row_actions', array($this, 'deleteMess'), 10, 2);
        add_filter('bulk_actions-edit-cont_mess', '__return_empty_array');
        add_action('restrict_manage_posts', array($this, 'messRestrictManage'));
        add_filter('admin_head', array($this, 'hide_month_filter'));
        add_filter('parse_query', array($this, 'mess_table_filter'));
        add_action( 'views_edit-cont_mess', array($this , 'remove_views' ));
        add_filter('manage_edit-cont_mess_columns', array($this , 'add_views_sortable_column'));


    }
   public function remove_views( $views ) {
        unset($views['all']);
        unset($views['publish']);
        unset($views['trash']);

        return $views;
    }
    public function add_views_sortable_column($sortable_columns){
        $sortable_columns['title'] = 'Name';
        return $sortable_columns;
    }



    public function contMessPostType() {
        $labels = array(
            'name' => 'Messages',
            'search_items' => 'Search'
        );
        $args = array(
            'label' => null,
            'labels' => $labels,
            'description' => '',
            'public' => FALSE,
            'publicly_queryable' => true,
            'exclude_from_search' => null,
            'show_ui' => true,
            'show_in_menu' => FALSE,
            'capability_type' => 'post',
            'capabilities' => array(
                'create_posts' => false,
                'delete_posts' => FALSE,
            ),
            'hierarchical' => false,
            'has_archive' => false,
            'rewrite' => FALSE,
            'query_var' => false,
            'show_in_nav_menus' => false
        );

        register_post_type(self::$post_type, $args);
    }

    public function messPopUp() {
        echo '<div id="messageDialog" title="Message"></div>';
    }

    public function contMessColumns($columns) {
        ;
        unset($columns['date']);
        $new_columns = array(
            'name' => 'Contact Name',
            'sender' => 'Sender Name',
            'phone' => 'Sender Phone',
            'preference' => 'Contact Preference with Sender',
            'mess_date' => 'Date'
        );
        return array_merge($columns, $new_columns);
    }

    public function customMessColumn($column, $post_id) {
        ;
        switch ($column) {
            case 'name' :
                echo get_post_meta($post_id, 'contact', TRUE);
                break;
            case 'sender' :
                echo get_post_meta($post_id, 'sender_name', TRUE);
                break;
            case 'preference' :
                $pref = get_post_meta($post_id, 'sender_preference', TRUE);
                switch ($pref) {
                    case '1' :
                        echo 'Phone';
                        break;
                    case '2' :
                        echo 'Either';
                        break;
                    default :
                        echo 'Mail';
                        break;
                }
                break;
            case 'phone' :
                echo get_post_meta($post_id, 'sender_phone', TRUE);
                break;
            case 'mess_date' :
                $post = get_post($post_id, 'OBJECT');
                echo $post->post_date;
                break;
        }
    }

    public function deleteMess($actions, $post) {
        if ($post->post_type == self::$post_type) {
            unset($actions);
            $actions['view'] = '<a href="#" title="View" class="viewMess" mess-id="' . $post->ID . '" rel="permalink">View</a>';
            $actions['delete'] = '<a href="#" title="" class="deleteMess" mess-id="' . $post->ID . '" rel="permalink">Delete</a>';
        }
        return $actions;
    }

    public function ajaxDelMess() {
        wp_delete_post($_POST['id'], true);
        echo 'true';
        wp_die();
    }

    public function ajaxViewMess() {
        ;
        $post_id = $_POST['id'];
        $post = get_post($post_id, OBJECT);
        $title = $post->post_title;
        $date = $post->post_date;
        $text = $post->post_content;
        $sender = get_post_meta($post_id, 'sender_name', TRUE);
        $sender_phone = get_post_meta($post_id, 'sender_phone', TRUE);
        $sender_mail = get_post_meta($post_id, 'sender_mail', TRUE);
        $pref = get_post_meta($post_id, 'sender_preference', TRUE);
        switch ($pref) {
            case '1' :
                $sender_cont_pref = 'Phone';
                break;
            case '2' :
                $sender_cont_pref = 'Either';
                break;
            default :
                $sender_cont_pref = 'Mail';
                break;
        }
        $name = get_post_meta($post_id, 'contact', TRUE);
        $category = get_post_meta($post_id, 'categories', TRUE);

        include_once(SC_DIR . '/views/admin/SCViewMessage.php');
        wp_die();
    }

  public function saveMess() {
    $value_m = (object) $_POST;
    if(!isset($_SESSION)){
      @session_start();
    }
    if($this->checkCaptcha($value_m) == false){
      return false;
    }
    if ($value_m->want_email !== '2') {
      $this->save_message_database($value_m);
    }
    if ($value_m->want_email === '1' || $value_m->want_email === '2') {
      if(isset($value_m->cont_pref)){
        $cont_pref = $value_m->cont_pref;
        if(intval($cont_pref) ==1){
          $cont_pref = 'Phone';
        }elseif(intval($cont_pref) ==2){
          $cont_pref = 'Either';
        }else{
          $cont_pref = 'Mail';
        }

        $headers  = "Content-type: text/html; charset=iso-8859-1\r\n";
        $headers .= 'From: ' . $value_m->full_name . ' < ' . $value_m->email . ' > ' . "\r\n";
        $message_text = 'Name: '.$value_m->full_name.'<br> Phone: '.$value_m->phone.'<br> Email: '.$value_m->email.'<br> Contact Preference: '.$cont_pref.'<br>Text: '.$value_m->message_text;
        return wp_mail($value_m->contact_mail, $value_m->mes_title, $message_text, $headers);
      }
    }
    return true;
  }


  private function save_message_database($value_m){
    $sc_uninstall_mess = get_option('sc_uninstall_mess');
    $new_post = array(
      'menu_order' => 0,
      'post_auther' => 1,
      'post_content' => $value_m->message_text,
      'post_status' => 'publish',
      'post_title' => $value_m->mes_title,
      'post_type' => self::$post_type
    );
    $ID = wp_insert_post($new_post);
    if(isset($sc_uninstall_mess) && $sc_uninstall_mess){
      array_push($sc_uninstall_mess , $ID);
      update_option('sc_uninstall_mess' , $sc_uninstall_mess);
    }else{
      $sc_uninstall_mess = array();
      array_push($sc_uninstall_mess , $ID);
      add_option('sc_uninstall_mess' , $sc_uninstall_mess);
    }
    if(isset($value_m->contact_categories)){
      update_post_meta($ID, 'categories', $value_m->contact_categories);
    }
    if(isset($value_m->full_name)){
      update_post_meta($ID, 'sender_name', $value_m->full_name);
    }
    if(isset($value_m->phone)){
      update_post_meta($ID, 'sender_phone', $value_m->phone);
    }
    if(isset($value_m->email)){
      update_post_meta($ID, 'sender_mail', $value_m->email);
    }
    if(isset($value_m->contact_name)){
      update_post_meta($ID, 'contact', $value_m->contact_name);
    }
    if(isset($value_m->cont_pref)){
      update_post_meta($ID, 'sender_preference', $value_m->cont_pref);
    }
    update_post_meta($ID, 'mess_date', date('Y/m/d'));
  }

    private function checkCaptcha($value_m) {
        $twd_captcha = get_option('twd_captcha');
        if ($twd_captcha === false) {
            $twd_captcha = '1';//default captcha
        }

        $return_val = true;
        switch ($twd_captcha) {
            case '0'://captcha disable
                break;
            case '1'://old captcha
                if (md5($value_m->code) != $_SESSION['captcha_code'][$value_m->contact_id]) {
                    $return_val = false;
                }
                break;
            case '2'://google reCaptcha

                $return_val = false;
                if(empty($_POST['g-recaptcha-response'])){
                    break;
                }

                $recaptcha_response = wp_remote_post('https://www.google.com/recaptcha/api/siteverify', array(
                  'body' => array(
                    'secret' => get_option('twd_gcaptcha_private_key'),
                    'response' => $_POST['g-recaptcha-response']
                  )
                ));

                if(is_wp_error($recaptcha_response) || empty($recaptcha_response['body'])){
                    break;
                }

                $recaptcha_response = json_decode($recaptcha_response['body'], true);
                $return_val = (isset($recaptcha_response['success']) && $recaptcha_response['success'] == true);
        }
        return $return_val;
    }


    public function messRestrictManage() {
        global $typenow;
        ;
        if ($typenow == self::$post_type) {
            $selected = isset($_GET['mess_contact_filter']) ? $_GET['mess_contact_filter'] : null;
            $postlist = get_posts(array(
                'post_type' => self::$post_type,
                'showposts' => -1,
                'order' => 'ASC'
            ));
            echo "<select name='mess_contact_filter' id='mess_contact_filter' class='postform'>";
            echo "<option value=''>".'All Contacts'."</option>";
            $conts = array();
            foreach ($postlist as $post) {
                $cont_name = get_post_meta($post->ID, 'contact', TRUE);
                if (!in_array($cont_name, $conts) && $cont_name != '')
                    $conts[$post->ID] = $cont_name;
            }
            foreach ($conts as $value) {
                echo '<option value=' . $post->ID, $selected == $post->ID ? ' selected="selected"' : '', '>' . $value . '</option>';
            }
            echo "</select>";
            _e('From');
            ?>
            <input type="text" style="width: 90px"
                   id="<?php echo self::prefix; ?>_date_from_filter"
                   name="<?php echo self::prefix; ?>_date_from_filter"
                   class="<?php echo self::prefix; ?>_event_date"
                   value="<?php echo isset($_GET[self::prefix . '_date_from_filter']) ? $_GET[self::prefix . '_date_from_filter'] : ''; ?>" />
            <?php _e('To'); ?>
            <input type="text" style="width: 90px"
                   id="<?php echo self::prefix; ?>_date_to_filter"
                   name="<?php echo self::prefix; ?>_date_to_filter"
                   class="<?php echo self::prefix; ?>_event_date"
                   value="<?php echo isset($_GET[self::prefix . '_date_to_filter']) ? $_GET[self::prefix . '_date_to_filter'] : ''; ?>" />
            <a style="display:inline-block; margin:1px 0px;" href="<?php echo admin_url('edit.php?post_type=' . self::$post_type); ?>" class="button" >Reset</a>
        <?php
        }
    }

    public function hide_month_filter() {
        global $typenow;
        if ($typenow == self::$post_type) {
            add_filter('months_dropdown_results', '__return_empty_array');
            echo "<style>.bulkactions{display:none;}</style>";
        }
    }

    public function mess_table_filter($query) {
        if (is_admin() AND isset($query->query['post_type']) AND $query->query['post_type'] == self::$post_type) {
            $qv = &$query->query_vars;
            $qv['meta_query'] = array();

            if (!empty($_GET['mess_contact_filter'])) {
                $meta_name = get_post_meta($_GET['mess_contact_filter'], 'contact');
                $qv['meta_query'][] = array(
                    'key' => 'contact',
                    'value' => $meta_name[0],
                    'compare' => '='
                );
            }
            if (!empty($_GET[self::prefix . '_date_from_filter'])) {
                $qv['meta_query'][] = array(
                    'key' => 'mess_date',
                    'value' => $_GET[self::prefix . '_date_from_filter'],
                    'compare' => '>=',
                    'type' => 'DATE'
                );
            }
            if (!empty($_GET[self::prefix . '_date_to_filter'])) {
                $qv['meta_query'][] = array(
                    'key' => 'mess_date',
                    'value' => $_GET[self::prefix . '_date_to_filter'],
                    'compare' => '<=',
                    'type' => 'DATE'
                );
            }
        }
    }

    public static function getInstance() {
        if (null == self::$instance) {
            self::$instance = new self;
        }
        return self::$instance;
    }

}
