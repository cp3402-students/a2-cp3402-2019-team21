<?php
class SContCPT {

    protected static $instance = null;
    public static $post_type = 'contact';
    public static $taxonomy = 'cont_category';
    public static $taxonomy_slug = 'cont_category';

    private function __construct() {
        if (function_exists('add_action')) {
			add_action('init', array($this, 'contCustomPostType'));
			add_action('init', array($this, 'createCategory'), 0);
			// Filter by Categories
			add_action('restrict_manage_posts', array($this, 'TWDFilterByCategory'));
			
			add_action(self::$taxonomy . '_add_form_fields', array($this, 'addExtraFields'), 10, 2);
			add_action(self::$taxonomy . '_edit_form_fields', array($this, 'addExtraFields'));
			
			add_action('edited_' . self::$taxonomy, array($this, 'saveTaxonomyCustomMeta'), 10, 2);
			add_action('create_' . self::$taxonomy, array($this, 'saveTaxonomyCustomMeta'), 10, 2);
			
			add_action('save_post', array($this, 'saveContPost'));
			add_action('edit_post', array($this, 'saveContPost'));
			add_action('edit_form_after_title', array($this, 'contMetaToHead'));
			add_action('wp_ajax_get_cat_param', array($this, 'ajaxGetParams'));
		}
		if (function_exists('add_filter')) {
			add_filter('post_row_actions', array($this, 'deleteContView'), 10, 2);
			add_filter('bulk_actions-edit-' . self::$post_type, array($this, 'contBulkActions'));
			add_filter('the_content', array($this, 'contactContent'));
			add_filter('post_thumbnail_html', array($this, 'contactThumbnail'), 10, 5);
			add_filter('single_post_title', array($this, 'hide_title_func'), 10, 2);
			add_filter('enter_title_here', array($this, 'changeDefaultTitle'));
			// Filter by Categories
			add_filter('parse_query', array($this, 'TWDCatFilter'));
		}  
    }

    public function contCustomPostType() {
        $show_team_menu = (get_option( "twd_subscribe_done" ) == 1 );
        $labels = array(
        'name' => 'Team WD',
        'add_new' => 'Add New',
        'add_new_item' => 'Add new',
        'search_items' => 'Search',
        'menu_name' => 'Team WD',
        'add_new_item' => 'Add New' ,
        'new_item' => 'New Contact',
        'edit_item' =>'Edit Contact',
        'view_item' => 'View Contact',
        'all_items' => 'Team',
              'not_found' => 'No entries.'
          );
          $slug = get_option("team_slug");
          if(!$slug){
              $slug="";
          }
          $args = array(
        'label' => null,
        'labels' => $labels,
        'description' => '',
        'public' => true,
        'publicly_queryable' => true,
        'exclude_from_search' => null,
        'menu_position' => '26',
        'show_ui' => true,
        'show_in_menu' => $show_team_menu,
        'capability_type' => 'post',
        'hierarchical' => true,
        'menu_icon' => SC_URL . '/images/Staff_Directory_WD_menu.png',
        'supports' => array_merge(array('editor', 'thumbnail', 'title')),
        'taxonomies' => array(''),
        'has_archive' => false,
			  'rewrite' => array('slug' => $slug),
        'query_var' => true,
			  'show_in_nav_menus' => false,
        'register_meta_box_cb' => array($this, 'addPostMetabox')
        );
        register_post_type(self::$post_type, $args);
        flush_rewrite_rules();
    }

    public function createCategory() {
        $labels = array(
            'name' => 'Categories',
            'search_items' => 'Search Category',
            'parent_item' => 'Parent Category',
            'parent_item_colon' => 'Contact',
            'edit_item' => 'Edit Category',
            'update_item' => 'Update Category',
            'add_new_item' => 'Add New Category',
            'new_item_name' => 'New Genre Category',
            'menu_name' => 'Categories'
        );
        $args = array(
            'hierarchical' => true,
            'labels' => $labels,
            'show_ui' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'rewrite' => array('slug' => self::$taxonomy_slug)
        );
        register_taxonomy(self::$taxonomy, array(self::$post_type), $args);
        flush_rewrite_rules();
    }

/*=-=-=- Filter By categories in Contacts Page -=-=-=-=*/	
	public function TWDFilterByCategory() {
        global $typenow;
        if ($typenow == self::$post_type) {
			$twd_categ_groups = get_terms('cont_category', array( 'hide_empty' => false ) ); ?>
			<select name="twd_categs_filter">
				<option value=""><?php echo __("Show All Categories",'CWDLangDomain') ?></option>
				<?php $twd_current_v = isset( $_GET['twd_categs_filter'] ) ? $_GET['twd_categs_filter'] : '';
				foreach ( $twd_categ_groups as $label => $twd_group ) {
					printf(
						'<option value="%s"%s>%s</option>',
						$twd_group->term_id,
						$twd_group->term_id == $twd_current_v ? ' selected="selected"' : '',
						$twd_group->name
					);
				} ?>
			</select>
		<?php
        }
    }
	public function TWDCatFilter( $cwd_query ) {
		global $pagenow, $typenow;
		if ( is_admin() && $pagenow == 'edit.php' && isset( $_GET['twd_categs_filter'] ) && $_GET['twd_categs_filter'] != '' && $cwd_query->is_main_query() ) {
			$cwd_modifications['tax_query'][] = array(
				'taxonomy' => 'cont_category',
				'field'    => 'term_id',
				'terms'    => $_GET['twd_categs_filter']
			);
			$cwd_query->query_vars = array_merge(
				$cwd_query->query_vars,
				$cwd_modifications
			);
		}		
	}
	

    public function addExtraFields($tag) {
        if (is_object($tag)) {
            $t_id = $tag->term_id;
            $data = get_option("taxonomy_$t_id");
        }

        include_once(SC_DIR . '/views/admin/SCViewParam.php');
    }

    public function saveTaxonomyCustomMeta($term_id) {

        if (isset($_POST['param'])) {
            $term_meta = $_POST['param'];
            $term_meta = array_filter($term_meta);
            update_option("taxonomy_$term_id", $term_meta);
            $uninstall_taxonomy = get_option('uninstall_taxonomy');
            if(isset($uninstall_taxonomy) && $uninstall_taxonomy){
                array_push($uninstall_taxonomy , "taxonomy_$term_id");
                update_option("uninstall_taxonomy", $uninstall_taxonomy);
            } else {
                $uninstall_taxonomy = array();
                array_push($uninstall_taxonomy , "taxonomy_$term_id");
                add_option("uninstall_taxonomy", $uninstall_taxonomy);
            }

            $postslist = get_posts(array(
                'post_type' => self::$post_type,
                'showposts' => -1,
                'tax_query' => array(
                    array(
                        'taxonomy' => self::$taxonomy,
                        'terms' => $term_id,
                        'field' => 'term_id',
                    )
                )
                    )
            );
            foreach ($postslist as $post) {
                $params = get_post_meta($post->ID, 'params', TRUE);
                $param_keys = array_keys($params);
                foreach ($param_keys as $item) {
                    if (!in_array($item, $term_meta)) {
                        unset($params[$item]);
                    }
                }
                foreach ($term_meta as $item) {
                    if (!in_array($item, $param_keys)) {
                        $params[$item] = array("");
                    }
                }
                update_post_meta($post->ID, 'params', $params);
            }
        }
    }

    public function addPostMetabox() {
		add_meta_box('fieldsMetabox','Parameters', array($this,'postCustomFields'), self::$post_type, 'advanced', 'default');
    }

    public function contMetaToHead($post) {
        global $post, $wp_meta_boxes;
        if ($post->post_type == self::$post_type) {
            do_meta_boxes(get_current_screen(), 'advanced', $post);
            unset($wp_meta_boxes[get_post_type($post)]['advanced']);
        }
    }

    public function deleteContView($actions, $post) {

        if ($post->post_type == self::$post_type) {
            unset($actions["inline hide-if-no-js"]);
            return $actions;
        }
        return $actions;
    }

    public function contBulkActions($actions) {
        unset($actions['edit']);
        return $actions;
    }

    public function postCustomFields($post) {
        $post_id = $post->ID;
        $want_email = get_post_meta($post_id, 'want_email', TRUE);
        $email = get_post_meta($post_id, 'email', TRUE);
        $params = get_post_meta($post_id, 'params', TRUE);

        $team_url = get_post_meta($post_id, 'team_url', TRUE);
        $want_url = get_post_meta($post_id, 'want_url', TRUE);

        include_once(SC_DIR . '/views/admin/SCViewPostFields.php');
    }

    public function saveContPost($ID = false) {
		//echo "wwwwww";
        $new_Stuff = true;
        if (isset($_POST['post_type']) && $_POST['post_type'] == self::$post_type) {
            $order_list = get_option('staff_order_contact');
            if(isset($order_list) && $order_list){
                foreach($order_list as $id){
                    if(intval($id)==intval($ID)){
                        $new_Stuff =false;
                    }
                }
                if($new_Stuff){
                    array_push($order_list ,$ID);
                    update_option('staff_order_contact' , $order_list);
                }
            }
            if(isset($_POST['param'])){
               update_post_meta($ID, 'params', $_POST['param']);
            }
            if(isset($_POST['want_email'])){
               update_post_meta($ID, 'want_email', $_POST['want_email']);
            }
            if(isset($_POST['email'])){
               update_post_meta($ID, 'email', $_POST['email']);
            }

            if(isset($_POST['want_url'])){
              update_post_meta($ID, 'want_url', $_POST['want_url']);
            }
            if(isset($_POST['team_url'])){
              update_post_meta($ID, 'team_url', $_POST['team_url']);
            }
        } elseif (isset($_POST['post_type']) && $_POST['post_type'] == 'cont_theme') {

		   if (isset($_POST['theme_reset'])) {
                $params = array();
				/*- Short !-*/
				$params['short_soc_bg_color']='';
				$params['short_icons_hover_color']='';
				$params['short_active_pagination_bg']='';
				$params['short_hover_bg_color']='';
					$params['short_popup_bg_color']='';
					$params['short_popoup_link_hover_color']='';
					$params['short_button_bg_color']='';
					$params['short_popup_soc_hover_bg_color']='';
					$params['short_popup_icons_color']='';
				/*- Full !-*/
				$params['full_link_hover_color']='';
				$params['full_button_hover_bg_color']='';
				$params['full_social_hover_bg_color']='';
				$params['full_active_pagination_bg']='';
				$params['full_image_hover_bg_color']='';
					$params['full_popup_title_bg_color']='';
					$params['full_popup_link_hover_color']='';
					$params['full_popup_button_hover_bg']='';
					$params['full_popup_social_hover_bg_color']='';
				
				/*- Table !-*/
				$params['table_border_color']='';
				$params['table_head_color']='';
				$params['table_active_pagination_bg']='';
				$params['table_row_hover_bg_color']='';
				
				/*- Chess !-*/
				$params['chess_title_color']='';
				$params['chess_links_hover_color']='';
				$params['chess_button_hover_color']='';
				$params['chess_icons_hover_color']='';
				$params['chess_active_pagination_bg']='';
				$params['chess_hover_bg_color']='';
					$params['chess_popup_bg_color']='';
					$params['chess_popup_links_hover_color']='';
					$params['chess_popup_button_bg']='';
					$params['chess_popup_button_hover_color']='';
					$params['chess_popup_icons_hover_color']='';

				/*- Portfolio !-*/
				$params['port_active_pagination_bg']='';
				$params['port_hover_bg_color']='';
					$params['port_popup_bg_color']='';
					$params['port_popup_button_hover_color']='';
					$params['port_popup_icons_hover_color']='';
				
				/*- Blog !-*/
				$params['blog_link_color']='';
				$params['blog_button_hover_bg']='';
				$params['blog_button_color']='';
				$params['blog_active_pagination_bg']='';
				$params['blog_border_color']='';
					$params['blog_popup_header_bg_color']='';
					$params['blog_popup_link_color']='';
					$params['blog_popup_button_hover_bg']='';
					$params['blog_popup_soc_hover_bg_color']='';
				
				/*- Circle !-*/
				$params['circle_border_color']='';
				$params['circle_link_color']='';
				$params['circle_button_bg_color']='';
				$params['circle_pagination_active_bg']='';
					$params['circle_popup_header_bg_color']='';
					$params['circle_popup_link_color']='';
					$params['circle_popup_button_hover_bg']='';
					$params['circle_popup_soc_hover_bg_color']='';
					
				/*- Square !-*/
				$params['square_border_color']='';
				$params['square_social_color']='';
				$params['square_social_hover_color']='';
				$params['square_active_pagination_bg']='';
				$params['square_bg_hover_color']='';
					$params['square_popup_link_color']='';
					$params['square_popup_link_hover_color']='';
					$params['square_button_bg']='';
					$params['square_button_bg_hover_color']='';
					$params['square_popup_social_color']='';
					$params['square_popup_social_hover_color']='';
				
				/*- Single !-*/
				$params['single_title_color']='';
				$params['single_link_color']='';
				$params['single_cont_param_title_color']='';
				$params['single_mess_param_title_color']='';
				$params['single_button_bg_color']='';
				
				$thems = array(
					'Default' =>'#00A99D',
					'Dark'	  =>'#5A5A5A',
					'Blue'	  =>'#3EB3E5',
					'Green'	  =>'#00A59B',
					'Violet'  =>'#616685'
				);
				
                $value = '';
                foreach (array_keys($thems) as $item) {
                    if (strpos($_POST['post_name'], $item) !== false) {
                        $value = $thems[$item];
                    }
                }
                if ($value == '')
                    $value = $thems['default'];
                foreach ($params as $key2 => $item) {
                    $params[$key2] = $value;
                }
                $params['short_image_circle'] = '1';
                $_POST['params'] = $params;
            }
            update_post_meta($ID, 'params', $_POST['params']);
        } elseif (isset($_POST['post_type']) && $_POST['post_type'] == 'cont_mess') {
            update_post_meta($ID, 'sender_name', $_POST['sender_name']);
            update_post_meta($ID, 'sender_phone', $_POST['sender_phone']);
            update_post_meta($ID, 'sender_mail', $_POST['sender_mail']);
            update_post_meta($ID, 'sender_preference', $_POST['sender_preference']);
            update_post_meta($ID, self::$post_type, $_POST['contact']);
        } else {
            return;
        }
    }

    public function ajaxGetParams() {
        $res = array();
        $cats = '';
        $forCat = '';
        $dif = '';
        if (isset($_POST['cats']))
            $cats = $_POST['cats'];
        if (isset($_POST['forCat']))
            $forCat = $_POST['forCat'];
        if (!get_option('taxonomy_' . $forCat)) {
            wp_die();
        }
        if ($cats != NULL && $cats != '') {
            foreach ($cats as $value) {
                if (is_array(get_option('taxonomy_' . $value)))
                    $res = array_merge($res, get_option('taxonomy_' . $value));
            }
            $dif = get_option('taxonomy_' . $forCat);
        }else {
            $res = get_option('taxonomy_' . $forCat);
        }
        $res = array_unique($res);

        if (is_array($dif)) {
            $res = array_diff($dif, $res);
        }
        if (isset($_POST['unCheck'])) {
            echo json_encode($res);
            wp_die();
        }
        include_once(SC_DIR . '/views/admin/SCViewPostParam.php');
        wp_die();
    }

    public function contactContent($content) {
        global $post;

        if (is_single() && $post->post_type == self::$post_type) {
            $feat_image = '';
            if (has_post_thumbnail()) {
                $feat_image = wp_get_attachment_url(get_post_thumbnail_id($post->ID, 'pull'));
            }
            $contact_content = '';
            ob_start();
            include(SC_DIR . '/views/SCViewSingleContact.php' );
            $contact_content .= ob_get_clean();
            $content = $contact_content;
        }
        return $content;
    }

    public function contactThumbnail($html, $post_id, $post_thumbnail_id, $size, $attr) {
        if (is_single() && get_post_type($post_id) == self::$post_type) {
            $html = '<br /><br />';
        }
        return $html;
    }

    public function hide_title_func($title, $post) {
        if (is_single() && $post->post_type == self::$post_type) {
            $post->post_title = '';
        }
        return $title;
    }

    public function changeDefaultTitle($title) {
        $screen = get_current_screen();
        ;
        if (self::$post_type == $screen->post_type) {
            $title = 'Enter contact name';
        }
        return $title;
    }
    public static function getInstance() {
        if (null == self::$instance) {
            self::$instance = new self;
        }
        return self::$instance;
    }

}
