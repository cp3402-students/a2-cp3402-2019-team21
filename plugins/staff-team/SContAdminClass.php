<?php
class SContAdminClass {

    protected static $instance = null;
    public $shortcode_tag = 'contact';
    public $version = SC_version;
    private function __construct() {
        $this->uninstall_redirect();
        add_action('init', array($this, 'check_silent_update'));
        add_action('admin_menu', array($this, 'SContSubmenu'),9);
        add_action('admin_init', array($this,'setup_redirect'));
        add_action('admin_init', array($this, 'registerContOptions'));
        add_action('admin_enqueue_scripts', array($this, 'includeAdminStyle'));
        add_action('admin_enqueue_scripts', array($this, 'includeAdminScripts'));
        add_action('admin_head', array($this, 'insert_contacts'));
        add_action('admin_head', array($this, 'insert_cont_cats'));
        add_action('admin_head', array($this, 'plugin_url'));
        add_action('init', array($this, 'admin_head'));


        add_action( 'enqueue_block_editor_assets', array($this,'enqueue_block_editor_assets' ));
        add_action('wp_ajax_twd_shortcode', array($this, 'twd_shortcode_data'));

        add_action('admin_notices', array($this, 'create_pro_logo_to_head'));
        require_once 'lang/SLangClass.php';
        $plugin_folder_name = explode('/' , plugin_basename( __FILE__ ));
        new StaffDirLangClass('contact', $plugin_folder_name[0]);
        add_action('admin_menu', array($this, 'SCuninstallPlugin'),11);
        add_action('wp_ajax_staff_order_contact', array($this, 'staff_order_contact'));
        add_action('wp_ajax_delete_demo_data', array($this, 'delete_demo_data'));
        add_filter("plugin_row_meta", array($this, 'scont_add_plugin_meta_links'), 10, 2);
    }

    public function twd_shortcode_data(){
      if(wp_verify_nonce ($_GET['nonce'], "twd_shortcode")){
        wp_print_scripts('jquery');
        require_once ("views/admin/twd-shortcode-iframe.php");
        die();
      }
      die;
    }
    public function enqueue_block_editor_assets() {
    $wd_bp_plugin_url = SC_URL;
    $key = 'tw/twd';
    $plugin_name = "Teame Wd";
    $twd_shortcode_nonce = wp_create_nonce( "twd_shortcode" );
    $url 	  = add_query_arg(array('action' => 'twd_shortcode', 'nonce'=>$twd_shortcode_nonce), admin_url('admin-ajax.php'));
    $icon_url = $wd_bp_plugin_url . '/images/Staff_Directory_WD_icon.png';
    $icon_svg = $wd_bp_plugin_url . '/images/Staff_Directory_WD_icon.png';
    ?>
      <script>
          if ( !window['tw_gb_twd'] ) {
              window['tw_gb_twd'] = {};
          }
          if ( !window['tw_gb_twd']['<?php echo $key; ?>'] ) {
              window['tw_gb_twd']['<?php echo $key; ?>'] = {
                  title: '<?php echo $plugin_name; ?>',
                  iconUrl: '<?php echo $icon_url; ?>',
                  iconSvg: {
                      width: '30',
                      height: '30',
                      src: '<?php echo $icon_svg; ?>'
                  },
                  isPopup: true,
                  data: {
                      shortcodeUrl: '<?php echo $url; ?>'
                  }
              };
          }
      </script>
    <?php
    wp_enqueue_style('wditw-gb-twd_block', $wd_bp_plugin_url . '/css/twd_block.css', array( 'wp-edit-blocks' ), SC_version );
    wp_enqueue_script( 'wditw-twd_block', $wd_bp_plugin_url . '/js/twd_block.js', array( 'wp-blocks', 'wp-element' ), SC_version );
  }

    public function SContSubmenu() {
        add_submenu_page('edit.php?post_type=contact', 'Team Ordering', 'Team Ordering', 'manage_options', 'ordering_staff', array($this , 'ordering_staff'));
        add_submenu_page('edit.php?post_type=contact', 'Messages', 'Messages', 'manage_options', 'edit.php?post_type=cont_mess');
        add_submenu_page('edit.php?post_type=contact', 'Styles and Colors', 'Styles and Colors', 'manage_options', 'styles_colors' , array($this , 'styles_colors'));
        add_submenu_page('edit.php?post_type=contact', 'Global Options', 'Options', 'manage_options', 'cont_option', array($this, 'displayGlobalOptions'));
    }
    public function SCuninstallPlugin(){
        add_submenu_page('edit.php?post_type=contact', 'Uninstall', 'Uninstall', 'manage_options', 'uninstall_plugin', array($this,'uninstallPlugin'));
    }
    public function includeAdminStyle() {
        $screen = get_current_screen();
        if($screen->post_type=="contact" || $screen->post_type =="cont_mess" || $screen->post_type =="cont_theme" || $screen->post_type == "page" ||  $screen->post_type=="post" || $screen->base==="admin_page_uninstall_plugin" || $screen->base == "contact_page_uninstall_plugin") {
            wp_register_style('contAdminStyle', plugins_url('css/admin.css', __FILE__), 1, $this->version);
            wp_register_style('jQueryDialog', plugins_url('css/jquery-ui-dialog.css', __FILE__), 1, 'all');
            wp_enqueue_style('contAdminStyle');
            wp_enqueue_style('jQueryDialog');
            wp_register_style('cont-evol-colorpicker-min', plugins_url('css/evol.colorpicker.css', __FILE__), 1, 'all');
            wp_enqueue_style('cont-evol-colorpicker-min');
            wp_enqueue_style('cont-admin-datetimepicker-css', plugins_url('css/jquery.datetimepicker.css', __FILE__), array(), 1, 'all');

            if ($screen->base == "admin_page_uninstall_plugin" || $screen->base == "contact_page_uninstall_plugin") {
                wp_enqueue_style('twd_deactivate-css', SC_URL . '/wd/assets/css/deactivate_popup.css', array(), $this->version);
            }
            /*Upload*/
            wp_enqueue_style('thickbox');
        }
    }

    public function ordering_staff(){
        include_once('views/admin/SCViewOrderingStuff.php' );
        global $wpdb;
        $contactList = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."posts WHERE post_type='contact' AND post_status='publish'");
        drawOrderingTable($contactList);

    }
    public function staff_order_contact(){
        $staff_order_contact = get_option('staff_order_contact');
        update_option('staff_order_contact' , $_POST['data']);


    }
    public function create_pro_logo_to_head(){
        global $pagenow, $post;
        $staff_page = $this->staff_page();
        if (is_array($staff_page) && $staff_page) { ?>
          <div class="update-nag twd_help_bar_wrap">
            <span class="twd_help_bar_text">
              <?php echo $staff_page["text"];?>
              <a style="color: #5CAEBD; text-decoration: none;border-bottom: 1px dotted;" class="twd_hb_t_link" target="_blank"
                 href="<?php echo $staff_page["link"];?>">Read More in User Guide
            </span>
            <div class="twd_hb_buy_pro">
              <a class="twd_support_link" href="https://wordpress.org/support/plugin/staff-team" target="_blank">
                <img src="<?php echo SC_URL; ?>/images/i_support.png" >
                Support Forum
              </a>
              <a class="twd_update_pro_link" target="_blank" href="https://web-dorado.com/files/fromStaffTeam.php">
                UPGRADE TO PAID VERSION
              </a>
            </div>
          </div>

            <?php
        }
    }
    private function staff_page(){
    $screen = get_current_screen();
    if ($screen->id == 'contact_page_styles_colors' || $screen->id == 'edit-contact' || $screen->id == 'contact' || $screen->id == 'edit-cont_category' || $screen->id == 'contact_page_ordering_staff' || $screen->id=='edit-cont_mess' || $screen->id == 'contact_page_styles_colors' || $screen->id == 'contact_page_cont_option' || $screen->id == 'contact_page_cont_featured_plugins' || $screen->id == 'contact_page_cont_featured_themes' || $screen->id == 'contact_page_contact_lang_option' || $screen->id == 'edit-cont_theme') {
      $text = "";
      $link = "https://web-dorado.com/wordpress-team-wd/options.html";
      if($screen->id === "edit-cont_category"){
        $text = "This section allows you to create, edit and delete Categories. ";
        $link = "https://web-dorado.com/wordpress-team-wd/adding-category.html";
      }elseif ($screen->id === "contact"){
        if($screen->action === "add"){
          $text = "This section allows you to add new team contact. ";
          $link = "https://web-dorado.com/wordpress-team-wd/adding-contact.html";
        }else{
          $text = "This section allows you to edit new team contact. ";
          $link = "https://web-dorado.com/wordpress-team-wd/adding-contact.html";
        }
      }elseif ($screen->id === "edit-contact"){
        $text = "This section allows you to create, edit and delete team contact.";
        $link = "https://web-dorado.com/wordpress-team-wd/adding-contact.html";
      }elseif ($screen->id === "edit-contact"){
        $text = "This section allows you to create, edit and delete team contact.";
        $link = "https://web-dorado.com/wordpress-team-wd/adding-contact.html";
      }elseif ($screen->id === "contact_page_ordering_staff"){
        $text = "This section allows you to change members/contacts ordering.";
        $link = "https://web-dorado.com/wordpress-team-wd/team-ordering.html";
      }elseif ($screen->id === "edit-cont_mess"){
        $text = "This section allows you to view the list of the submitted messages.";
        $link = "https://web-dorado.com/wordpress-team-wd/messages.html";
      }elseif ($screen->id === "edit-cont_theme"){
        $text = "This section allows you to create themes.";
        $link = "https://web-dorado.com/wordpress-team-wd/styles-and-colors.html";
      }elseif ($screen->id === "contact_page_cont_option"){
        $text = "This section allows you to change the options.";
        $link = "https://web-dorado.com/wordpress-team-wd/options.html";
      }elseif ($screen->id === "contact_page_contact_lang_option"){
        $text = "This section allows you to add a new language.";
        $link = "https://web-dorado.com/wordpress-team-wd/options.html";
      }


      return array(
        "text" => $text,
        'link' => $link
      );
    } else {
      return false;
    }
  }

    public function styles_colors(){
        echo"
            <h4 style='color: #cc0000 ;'>Styles and Colors options are disabled in free version. If you need this functionality, you need to buy the <a href='https://web-dorado.com/products/wordpress-team-wd.html'>commercial version.</a></h4>
            <img class='free_style_colors' src='".SC_URL."/images/styles_colors.png'>
            ";
    }

    public function includeAdminScripts() {
        $screen = get_current_screen();
        if($screen->post_type=="contact" || $screen->post_type =="cont_mess" || $screen->post_type =="cont_theme" || $screen->post_type == "page" ||  $screen->post_type=="post" || $screen->base==="admin_page_uninstall_plugin" || $screen->base == "contact_page_uninstall_plugin") {
          wp_register_script('SCAdminScript', plugins_url('js/admin/admin.js', __FILE__), array(
              'jquery',
              'jquery-ui-widget',
              'jquery-ui-tabs',
              'jquery-ui-dialog',
              'media-upload',
              'thickbox'
            ), $this->version, true);
            wp_localize_script('SCAdminScript', 'staff_ajaxurl', admin_url('admin-ajax.php'));
            wp_register_script('colorPicker-min', plugins_url('js/admin/evol.colorpicker.js', __FILE__), array(
              'jquery',
              'jquery-ui-widget'
            ), 1, true);
            wp_enqueue_script('colorPicker-min');
            wp_register_script('datetimepicker', plugins_url('js/admin/jquery.datetimepicker.js', __FILE__), array(
              'jquery',
              'jquery-ui-widget'
            ), 1, true);
            wp_enqueue_script('datetimepicker');
            wp_enqueue_script('SCAdminScript');
            wp_enqueue_media();
            /*Upload*/
            wp_enqueue_script('media-upload');
            wp_enqueue_script('thickbox');
            //wp_register_script('my-upload', plugins_url('js/admin/admin.js', __FILE__), array('jquery',));
            wp_enqueue_script('my-upload');
            wp_register_script('contact_order', plugins_url('js/admin/contact_order.js', __FILE__), array(
              'jquery',
              'jquery-ui-widget',
              'jquery-ui-tabs',
              'jquery-ui-dialog'
            ), 1, true);
            wp_enqueue_script('contact_order');
            if ($screen->base == "admin_page_uninstall_plugin" || $screen->base == "contact_page_uninstall_plugin") {
                wp_enqueue_script('twd-deactivate-popup', SC_URL . '/wd/assets/js/deactivate_popup.js', array(), $this->version, true);
                $admin_data = wp_get_current_user();
                wp_localize_script('twd-deactivate-popup', 'twdWDDeactivateVars', array(
                  "prefix" => "twd",
                  "deactivate_class" => 'twd_deactivate_link',
                  "email" => $admin_data->data->user_email,
                  "plugin_wd_url" => "https://web-dorado.com/products/wordpress-team-wd.html",
                ));
            }
        }
    }

    public function plugin_url() { ?>
        <script>
            var sc_plugin_url = '<?php echo SC_URL; ?>';
        </script>
    <?php
    }

    public function admin_head() {
        if (!current_user_can('edit_posts') && !current_user_can('edit_pages')) {
            return;
        }
        if ('true' == get_user_option('rich_editing')) {
            add_filter('mce_external_plugins', array($this, 'mce_external_plugins'));
            add_filter('mce_buttons', array($this, 'mce_buttons'));
        }
    }

    public function mce_external_plugins($plugin_array) {
        $screen = get_current_screen();
        if ($screen->post_type != 'contact') {
            $plugin_array[$this->shortcode_tag] = plugins_url('js/mce-cont-button.js', __FILE__);
        }
        return $plugin_array;
    }

    public function mce_buttons($buttons) {
        array_push($buttons, $this->shortcode_tag);
        return $buttons;
    }

    public function insert_contacts() {
        $contacts = get_posts(array('post_type' => 'contact', 'numberposts' => -1,));  ?>
        <script>
            var contacts = [];
            contacts[0] =
			{
				text: "<?php echo 'Select a Contact'; ?>",
				value: 0
			};
        </script>
        <?php
        $i = 0;
        foreach ($contacts as $cont) {
            ?>
            <script>
                contacts[<?php echo ++$i; ?>] =
				{
					text: "<?php echo $cont->post_title; ?>",
					value: '<?php echo $cont->ID; ?>'
				};
            </script>
        <?php
        }
    }

    public function insert_cont_cats() {
        $args = array(
            'orderby' => 'id',
            'order' => 'ASC',
            'hide_empty' => TRUE,
            'exclude' => array(),
            'exclude_tree' => array(),
            'include' => array(),
            'number' => '',
            'fields' => 'all',
            'slug' => '',
            'parent' => '',
            'hierarchical' => true,
            'child_of' => 0,
            'get' => '',
            'name__like' => '',
            'pad_counts' => false,
            'offset' => '',
            'search' => '',
            'cache_domain' => 'core'
        );

        $cats = get_terms('cont_category', $args);  ?>
        <script>
            var contCats = [];
        </script>
        <?php
        $i = 0;
        foreach ($cats as $cat) { ?>
            <script>
                contCats[<?php echo $i++; ?>] =
				{
					text: '<?php echo $cat->name; ?>',
					value: '<?php echo $cat->term_id; ?>'
				};
            </script>
        <?php
        }
    }

    public function registerContOptions() {
        register_setting('cont_option', 'choose_category');
        register_setting('cont_option', 'name_search');
        register_setting('cont_option', 'lightbox');
        register_setting('cont_option', 'team_slug');
        register_setting('mess_option', 'enable_message');
        register_setting('mess_option', 'show_name');
        register_setting('mess_option', 'show_email');
        register_setting('mess_option', 'twd_captcha');
        register_setting('mess_option', 'twd_gcaptcha_key');
        register_setting('mess_option', 'twd_gcaptcha_private_key');
        register_setting('mess_option', 'twd_pp_text');
        register_setting('mess_option', 'show_phone');
        register_setting('mess_option', 'show_cont_pref');

        register_setting('custom_css', 'twd_custom_css');
    }

    public function displayGlobalOptions() {
        if(isset($_GET['tab']) && $_GET['tab']=='mess_option'){
            include('views/admin/SCViewMessageOptions.php');
        }elseif (isset($_GET['tab']) && $_GET['tab']=='custom_css'){
            include('views/admin/SCViewCustomCss.php');
        }else{
            include('views/admin/SCViewGlobalOptions.php');
        }
    }
    public function uninstall_redirect (){
        if(isset($_GET['deactivate_plugin'])){
            require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
            deactivate_plugins(plugin_basename(SC_FILE));
            header('Location:'.home_url().'/wp-admin/plugins.php');
        }
    }
    public function uninstallPlugin(){
        include_once('views/admin/SCViewUninstall_sc.php');
        global  $twd_options;
        if(!class_exists("DoradoWebConfig")){
            include_once (SC_DIR . "/wd/config.php");
        }
        if(!class_exists("DoradoWebDeactivate")) {
            include_once (SC_DIR . "/wd/includes/deactivate.php");
        }
        $config = new DoradoWebConfig();

        $config->set_options( $twd_options );

        $deactivate_reasons = new DoradoWebDeactivate($config);
        //$deactivate_reasons->add_deactivation_feedback_dialog_box();
        $deactivate_reasons->submit_and_deactivate();
        if(isset($_POST["sc_uninstall"])){
            include_once('includes/SCUninistall.php');
            sc_uninstall_plugin();
            sc_uninstall_success();
        }else{
            sc_uninstall();
        }

    }

  public function check_silent_update(){
    $current_version = $this->version;
    $saved_version = get_option('twd_version');
    if($saved_version === false){
      $saved_version = '0.0.1';
    }

    $old_version =  substr($saved_version, 2);
    $new_version =  substr($current_version, 2);

    if($new_version  != $old_version ){
      self::contActivate();
    }

  }


  public static function global_activate($networkwide)
    {
			if (function_exists('is_multisite') && is_multisite()) {
				// Check if it is a network activation - if so, run the activation function for each blog id.
				if ($networkwide) {
					global $wpdb;
					// Get all blog ids.
					$blogids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
					foreach ($blogids as $blog_id) {
						switch_to_blog($blog_id);
						self::contActivate();
						restore_current_blog();
					}
					return;
				}
			}
			self::contActivate();
  }
    
    public static function contActivate() {
        update_option('name_search',1);
        update_option('lightbox',1);
        update_option('enable_message',1);
        update_option('show_name',1);
        update_option('show_email',1);
        update_option('show_phone',1);
        update_option('show_cont_pref',1);

        $saved_version = get_option('twd_version');
        if($saved_version === false){
          $saved_version = '0.0.1';
        }
        $saved_version =  substr($saved_version, 2);

      if(get_option('twd_pp_text') === false){

        $url = '';
        include_once ABSPATH . WPINC . '/link-template.php';

        if(function_exists('get_privacy_policy_url')){
          $url = get_privacy_policy_url();
        }

        if(empty($url)) {
          $twd_pp_text = 'Our company collects this data to be able to provide services to you. We process this data according to our Privacy Policy. If you consent to our usage of your data, click this checkbox.';
        } else {
          $twd_pp_text = 'Our company collects this data to be able to provide services to you. We process this data according to our <a href="' . $url . '" target="blank">Privacy Policy</a>. If you consent to our usage of your data, click this checkbox.';
        }
        update_option('twd_pp_text', $twd_pp_text);

      }


      $team_slug = get_option("team_slug");
        if(!$team_slug){
            update_option('team_slug','person');
        }
        $activation_post = array();
        add_option('sc_uninstall_plugin' , $activation_post);
        update_option('twd_version', SC_version);
        add_option('twd_do_activation_set_up_redirect', 1);
    }
    public function delete_demo_data(){
        if(isset($_POST["delete"]) && $_POST["delete"]=='true'){
            $delete_demo_data = get_option('delete_demo_data');
            foreach($delete_demo_data as $demo_data){
                $_thumbnail_id = get_post_meta($demo_data , '_thumbnail_id');
                wp_delete_post($_thumbnail_id[0] , true);
                delete_post_meta($demo_data , 'email');
                delete_post_meta($demo_data , 'want_email');
                wp_delete_post($demo_data , true);
            }
            $delete_demo_category = get_option('delete_demo_category');
            wp_delete_term($delete_demo_category["term_id"], 'cont_category');

            delete_option('delete_demo_data');
            delete_option('delete_demo_category');
        }
    }

    public function setup_redirect() {
        if (get_option('twd_do_activation_set_up_redirect')) {
            update_option('twd_do_activation_set_up_redirect',0);
            //wp_safe_redirect( admin_url( 'index.php?page=gmwd_setup' ) );
            wp_safe_redirect( admin_url( 'admin.php?page=twd_subscribe' ) );
            exit;
        }
    }

  public function scont_add_plugin_meta_links($meta_fields, $file){

    if(SC_BASENAME == $file) {

      $meta_fields[] = "<a href='https://wordpress.org/support/plugin/staff-team/' target='_blank'>Support Forum</a>";
      $meta_fields[] = "<a href='https://wordpress.org/support/plugin/staff-team/reviews#new-post' target='_blank' title='Rate'>
            <i class='scont-rate-stars'>"
        . "<svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-star'><polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg>"
        . "<svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-star'><polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg>"
        . "<svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-star'><polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg>"
        . "<svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-star'><polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg>"
        . "<svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-star'><polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg>"
        . "</i></a>";

      $stars_color = "#ffb900";

      echo "<style>"
        . ".scont-rate-stars{display:inline-block;color:" . $stars_color . ";position:relative;top:3px;}"
        . ".scont-rate-stars svg{fill:" . $stars_color . ";}"
        . ".scont-rate-stars svg:hover{fill:" . $stars_color . "}"
        . ".scont-rate-stars svg:hover ~ svg{fill:none;}"
        . "</style>";
    }

    return $meta_fields;
  }

    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new self;
        }
        return self::$instance;
    }

}
