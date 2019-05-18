<?php
 /**
 * Plugin Name:  Team WD
 * Plugin URI: https://web-dorado.com/products/wordpress-team-wd.html
 * Description:  Team WD is a WordPress team list plugin with large and affecting capabilities which helps you to display information about the group of people more intelligible, effective and convenient.  Team WD’s main feature is the possibility to create and manage your own list of different contacts with corresponding images, data and with a feedback option.
 * Version: 1.1.0
 * Author: WebDorado
 * Author URI: https://web-dorado.com/wordpress-plugins-bundle.html
 * License:  GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

define ('SC_FILE' , __FILE__);
define ('SC_DIR',dirname(__FILE__));
define ('SC_URL',plugins_url(plugin_basename(dirname(__FILE__))));
define ('SC_version', '1.1.0');
define ('SC_BASENAME',plugin_basename(__FILE__));
require_once('SContClass.php');
add_action( 'plugins_loaded', array( 'SContClass', 'getInstance' ) );
include_once dirname( __FILE__ ) . '/includes/SCDemo.php';
register_activation_hook( __FILE__, array( 'SCDemo', 'demo_global_activate' ) );
if(is_admin()){
	require_once('SContAdminClass.php');
	$cont_admin = SContAdminClass::getInstance();
}

register_activation_hook( __FILE__,  array( 'SContAdminClass', 'global_activate' ));
//team-staff
add_action('init', 'twd_wd_lib_init', 9);



function register_twd_plugin_exporter( $exporters ) {
  $exporters['twd'] = array(
    'exporter_friendly_name' => __( 'Team WD' ),
    'callback' => 'twd_plugin_exporter',
  );
  return $exporters;
}
function twd_plugin_exporter( $email_address, $page = 1 ) {
  $done = false;
  $export_items = array();

  $meta_query_args = array(
    'relation' => 'AND',
    array(
      'key'     => 'sender_mail',
      'value'   => $email_address,
      'compare' => '='
    )
  );
  $args = array(
    'numberposts' => -1,
    'post_type' => array( 'cont_mess' ),
    'meta_query' => $meta_query_args
  );
  $posts = get_posts($args);


  foreach ($posts as $twd_post){
    $submission_data = array();
    $post_metas = get_post_meta($twd_post->ID);
    $group_id = 'submission';
    $item_id = $twd_post->ID;
    if(isset($post_metas["sender_name"]) && isset($post_metas["sender_name"][0])){
      $sender_name = $post_metas["sender_name"][0];
      array_push($submission_data , array(
        'name' => __( 'Sender name' ),
        'value' => $sender_name
      ));
    }
    if(isset($post_metas["sender_phone"]) && isset($post_metas["sender_phone"][0])){
      $sender_phone = $post_metas["sender_phone"][0];
      array_push($submission_data ,  array(
        'name' => __( 'Sender phone' ),
        'value' => $sender_phone
      ));
    }
    if(isset($post_metas["sender_mail"]) && isset($post_metas["sender_mail"][0])){
      $sender_mail = $post_metas["sender_mail"][0];
      array_push($submission_data ,  array(
        'name' => __( 'Sender mail' ),
        'value' => $sender_mail
      ));
    }
    if(isset($post_metas["contact"]) && isset($post_metas["contact"][0])){
      $contact = $post_metas["contact"][0];
      array_push($submission_data ,  array(
        'name' => __( 'Contact' ),
        'value' => $contact
      ));
    }
    if(isset($post_metas["mess_date"]) && isset($post_metas["mess_date"][0])){
      $mess_date = $post_metas["mess_date"][0];
      array_push($submission_data ,  array(
        'name' => __( 'Message date' ),
        'value' => $mess_date
      ));
    }
    if(!empty($submission_data)){
      $done = true;
      $export_items[] = array(
        'group_id' => $group_id,
        'group_label' => 'Team WD form submission data',
        'item_id' => $item_id,
        'data' => $submission_data,
      );
    }
  }


  $contact_meta_query_args = array(
    'relation' => 'AND',
    array(
      'key'     => 'email',
      'value'   => $email_address,
      'compare' => '='
    )
  );
  $contact_args = array(
    'numberposts' => -1,
    'post_type' => array( 'contact' ),
    'meta_query' => $contact_meta_query_args
  );
  $contact_posts = get_posts($contact_args);


  foreach ($contact_posts as $twd_contact_post) {
    $contact_data = array();
    $contact_post_metas = get_post_meta($twd_contact_post->ID);
    $categories_array = get_terms("cont_category");
    $category_list = "";
    $group_id = 'member';
    $item_id = $twd_contact_post->ID;
    if(isset($categories_array) && is_array($categories_array)){
      foreach ($categories_array as $category){
        if(isset($category->name)){
          $category_list.= $category->name. " ";
        }
      }
    }
    if(isset($twd_contact_post->post_title)){
      array_push($contact_data ,  array(
        'name' => __( 'Title' ),
        'value' => $twd_contact_post->post_title
      ));
    }
    if(isset($twd_contact_post->post_content)){
      array_push($contact_data ,  array(
        'name' => __( 'Content' ),
        'value' => $twd_contact_post->post_content
      ));
    }
    if(!empty($category_list)){
      array_push($contact_data ,  array(
        'name' => __( 'Category' ),
        'value' => $category_list
      ));
    }
    if(isset($contact_post_metas["params"]) && isset($contact_post_metas["params"][0])){
      $contact_params = unserialize($contact_post_metas["params"][0]);
      if(isset($contact_params["Nationality"]) && isset($contact_params["Nationality"][0])){
        $nationality = $contact_params["Nationality"][0];
        array_push($contact_data ,  array(
          'name' => __( 'Nationality' ),
          'value' => $nationality
        ));
      }
      if(isset($contact_params["Occupation"]) && isset($contact_params["Occupation"][0])){
        $occupation = $contact_params["Occupation"][0];
        array_push($contact_data ,  array(
          'name' => __( 'Occupation' ),
          'value' => $occupation
        ));
      }
    }
    if(isset($contact_post_metas["email"]) && isset($contact_post_metas["email"][0])){
      $contact_email = $contact_post_metas["email"][0];
      array_push($contact_data ,  array(
        'name' => __( 'Email' ),
        'value' => $contact_email
      ));
    }
    if(isset($contact_post_metas["team_url"]) && isset($contact_post_metas["team_url"][0]) && !empty($contact_post_metas["team_url"][0])){
      $team_url = $contact_post_metas["team_url"][0];
      array_push($contact_data ,  array(
        'name' => __( 'Team url' ),
        'value' => $team_url
      ));
    }
    if(isset($contact_post_metas["_thumbnail_id"]) && isset($contact_post_metas["_thumbnail_id"][0])){
      $featured_image_url = wp_get_attachment_image_src($contact_post_metas["_thumbnail_id"][0]);
      if(isset($featured_image_url) && is_array($featured_image_url) && isset($featured_image_url[0])){
        array_push($contact_data ,  array(
          'name' => __( 'Featured image' ),
          'value' => $featured_image_url[0]
        ));
      }
    }
    if(!empty($contact_data)){
      $done = true;
      $export_items[] = array(
        'group_id' => $group_id,
        'group_label' => 'Team WD member data',
        'item_id' => $item_id,
        'data' => $contact_data,
      );
    }
  }

  return array(
    'data' => $export_items,
    'done' => true,
  );
}

add_filter(
  'wp_privacy_personal_data_exporters',
  'register_twd_plugin_exporter',
  10
);



function register_twd_plugin_eraser( $erasers ) {
  $erasers['twd'] = array(
    'eraser_friendly_name' => __( 'Team WD' ),
    'callback'             => 'twd_plugin_eraser',
  );
  return $erasers;
}
add_filter(
  'wp_privacy_personal_data_erasers',
  'register_twd_plugin_eraser',
  10
);
function twd_plugin_eraser( $email_address, $page = 1 ) {
  $items_removed = false;
  $meta_query_args = array(
    'relation' => 'AND',
    array(
      'key'     => 'sender_mail',
      'value'   => $email_address,
      'compare' => '='
    )
  );
  $args = array(
    'numberposts' => -1,
    'post_type' => array( 'cont_mess' ),
    'meta_query' => $meta_query_args
  );
  $posts = get_posts($args);
  foreach ($posts as $twd_post) {
    $items_removed = true;
    wp_delete_post( $twd_post->ID, true );
  }


  $contact_meta_query_args = array(
    'relation' => 'AND',
    array(
      'key'     => 'email',
      'value'   => $email_address,
      'compare' => '='
    )
  );
  $contact_args = array(
    'numberposts' => -1,
    'post_type' => array( 'contact' ),
    'meta_query' => $contact_meta_query_args
  );
  $contact_posts = get_posts($contact_args);

  foreach ($contact_posts as $twd_contact_post) {
      $items_removed = true;
      $attachment_id = get_post_thumbnail_id( $twd_contact_post->ID );
      wp_delete_attachment($attachment_id, true);
      wp_delete_post( $twd_contact_post->ID, true );
  }



  return array(
    'items_removed' => $items_removed,
    'items_retained' => false,
    'messages' => array(),
    'done' => true,
  );
}






function twd_wd_lib_init() {
  if (!isset($_REQUEST['ajax'])) {
    if (!class_exists("DoradoWeb")) {
      require_once(SC_DIR . '/wd/start.php');
    }
    global $twd_options;
    $twd_options = array(
      "prefix" => "twd",
      "wd_plugin_id" => 153,
      "plugin_title" => "Team WD",
      "plugin_wordpress_slug" => "staff-team",
      "plugin_dir" => SC_DIR,
      "plugin_main_file" => __FILE__,
      "description" => __("A perfect solution to display the members of your staff, team or employees on your WordPress website. Show details about team members, including bio, featured image, nationality, occupation and more.", 'twd'),
      // from web-dorado.com
      "plugin_features" => array(
        0 => array(
          "title" => __("Easy Configuration", "twd"),
          "description" => __("Install and activate Team WD plugin and you can easily add your team/staff members from the admin area. Add each team member separately, including name, photo, bio, position/occupation, contact email, and other information.", "twd"),
        ),
        1 => array(
          "title" => __("Team Ordering", "twd"),
          "description" => __("You can order team members to show in the list by ID or by name. You can also set the order that the members appear on the page using the simple drag & drop ordering.", "twd"),
        ),
        2 => array(
          "title" => __("Display Options", "twd"),
          "description" => __("The Team WD plugin allows you to display team/staff member photos and information in 8 different layouts - Short, Full, Chess, Portfolio, Blog, Circle, Square and Table. Simply choose the view type that better fits your website when adding the team list to the page.", "twd"),
        ),
        3 => array(
          "title" => __("Styles and Colors/Themes", "twd"),
          "description" => __("The WordPress team plugin comes with 5 different built-in styles and colors – Default, Dark, Blue, Green and Violet.", "twd"),
        ),
        4 => array(
          "title" => __("Lightbox", "twd"),
          "description" => __("The WordPress team plugin comes with Lightbox integration. You can choose to activate the Lightbox feature to showcase your staff images in a more attractive pop up view.", "twd"),
        )
      ),
      // user guide from web-dorado.com
      "user_guide" => array(
        0 => array(
          "main_title" => __("Adding a Category", "twd"),
          "url" => "https://web-dorado.com/wordpress-team-wd/adding-category.html",
          "titles" => array()
        ),
        1 => array(
          "main_title" => __("Adding a Contact", "twd"),
          "url" => "https://web-dorado.com/wordpress-team-wd/adding-contact.html",
          "titles" => array()
        ),
        2 => array(
          "main_title" => __("Team Ordering", "twd"),
          "url" => "https://web-dorado.com/wordpress-team-wd/team-ordering.html",
          "titles" => array()
        ),
        3 => array(
          "main_title" => __("Messages", "twd"),
          "url" => "https://web-dorado.com/wordpress-team-wd/messages.html",
          "titles" => array()
        ),
        4 => array(
          "main_title" => __("Options", "twd"),
          "url" => "https://web-dorado.com/wordpress-team-wd/options.html",
          "titles" => array()
        ),
        5 => array(
          "main_title" => __("Styles and Colors", "twd"),
          "url" => "https://web-dorado.com/wordpress-team-wd/styles-and-colors.html",
          "titles" => array()
        ),
        6 => array(
          "main_title" => __("Inserting Team WD in a Page or Post", "twd"),
          "url" => "https://web-dorado.com/wordpress-team-wd/inserting-team-wd.html",
          "titles" => array()
        ),
      ),
      'overview_welcome_image' => SC_URL . '/images/welcome_image.png',
      "video_youtube_id" => null,
      // e.g. https://www.youtube.com/watch?v=acaexefeP7o youtube id is the acaexefeP7o
      "plugin_wd_url" => "https://web-dorado.com/products/wordpress-team-wd.html",
      "plugin_wd_demo_link" => "http://wpdemo.web-dorado.com/team-wd/?_ga=1.146655351.212018776.1470817467",
      //"plugin_wd_forum_link" => "https://web-dorado.com/forum/team-wd.html",
      //"plugin_wd_addons_link" => "https://web-dorado.com/products/wordpress-google-maps-plugin/add-ons/marker-clustering.html",
      "after_subscribe" => "edit.php?post_type=contact&page=overview_twd",
      // this can be plagin overview page or set up page
      "plugin_wizard_link" => null,
      "plugin_menu_title" => "Team WD",
      //null
      "plugin_menu_icon" => SC_URL . '/images/Staff_Directory_WD_menu.png',
      //null
      "deactivate" => true,
      "subscribe" => true,
      "custom_post" => 'edit.php?post_type=contact',
      //"custom_post" => false,
      // if true => edit.php?post_type=contact
    );
    dorado_web_init($twd_options);
  }
}

add_filter('wp_get_default_privacy_policy_content', 'team_wd_privacy_policy');
function team_wd_privacy_policy($content){
  $title = __('Team WD', "StaffDirectoryWD");

  $text = __('The plugin optionally allows sending emails to team members. Emails, Names, Phone Numbers and other personal information may be sent. The mentioned personal data , as well as messages are also being stored in WP database. You must obtain User consent when they submit contact forms and delete/export submitted data upon their request.  Under GDPR you may be responsible to make sure that your team members and third-party services they use (e.g. mailing services) also comply with the high standards of privacy and data protection. For that purpose, you might need  to check privacy policies of third parties and refer to them in your privacy policy.', "StaffDirectoryWD");
  $pp_text = '<h3>' . $title . '</h3>' . '<p class="wp-policy-help">' . $text . '</p>';

  $content .= $pp_text;
  return $content;
}

if (!function_exists('staff_bp_install_notice')) {

  if(get_option('wds_bk_notice_status')==='' || get_option('wds_bk_notice_status')==='1'){
    return;
  }

  function staff_bp_script_style() {
    $staff_bp_plugin_url = plugins_url('', __FILE__);
    $get_current = get_current_screen();
    $current_screen_id = array(
      'edit-contact',
      'contact',
      'edit-cont_category',
      'contact_page_ordering_staff',
      'edit-cont_mess',
      'edit-cont_theme',
      'cont_theme',
      'contact_page_cont_option',
      'contact_page_contact_lang_option',
      'contact_page_overview_twd',
      'contact_page_uninstall_plugin',
      'contact_page_twd_updates'
    );

    if(in_array($get_current->id, $current_screen_id)){
      wp_enqueue_script('staff_bck_install', $staff_bp_plugin_url . '/js/wd_bp_install.js', array('jquery'));
      wp_enqueue_style('staff_bck_install', $staff_bp_plugin_url . '/css/wd_bp_install.css');
    }

  }
  add_action('admin_enqueue_scripts', 'staff_bp_script_style');

  /**
   * Show notice to install backup plugin
   */
  function staff_bp_install_notice() {
    $staff_bp_plugin_url = plugins_url('', __FILE__);
    $get_current = get_current_screen();
    $current_screen_id = array(
      'edit-contact',
      'contact',
      'edit-cont_category',
      'contact_page_ordering_staff',
      'edit-cont_mess',
      'edit-cont_theme',
      'cont_theme',
      'contact_page_cont_option',
      'contact_page_contact_lang_option',
      'contact_page_overview_twd',
      'contact_page_uninstall_plugin',
      'contact_page_twd_updates'
    );

    if(in_array($get_current->id, $current_screen_id)){
		$prefix = 'staff';
		$meta_value = get_option('wd_seo_notice_status');
		if ($meta_value === '' || $meta_value === false) {
		  ob_start();
		  ?>
		  <div class="notice notice-info" id="wd_bp_notice_cont">
			<p>
			  <img id="wd_bp_logo_notice" src="<?php echo $staff_bp_plugin_url . '/images/seo_logo.png'; ?>">
			  <?php _e("Team WD advises: Optimize your web pages for search engines with the", $prefix) ?>
			  <a href="https://wordpress.org/plugins/seo-by-10web/" title="<?php _e("More details", $prefix) ?>"
				 target="_blank"><?php _e("FREE SEO", $prefix) ?></a>
			  <?php _e("plugin to keep your data and website safe.", $prefix) ?>
			  <a class="button button-primary"
				 href="<?php echo esc_url(wp_nonce_url(self_admin_url('update.php?action=install-plugin&plugin=seo-by-10web'), 'install-plugin_seo-by-10web')); ?>">
				<span onclick="staff_bp_notice_install()"><?php _e("Install", $prefix); ?></span>
			  </a>
			</p>
			<button type="button" class="wd_bp_notice_dissmiss notice-dismiss"><span class="screen-reader-text"></span>
			</button>
		  </div>
		  <script>staff_bp_url = '<?php echo add_query_arg(array('action' => 'wd_seo_dismiss',), admin_url('admin-ajax.php')); ?>'</script>
		  <?php
		  echo ob_get_clean();
		}
	}
  }

  if (!is_dir(plugin_dir_path(dirname(__FILE__)) . 'seo-by-10web')) {
    add_action('admin_notices', 'staff_bp_install_notice');
  }

  /**
   * Add usermeta to db
   *
   * empty: notice,
   * 1    : never show again
   */
  function staff_bp_install_notice_status() {
    update_option('wd_seo_notice_status', '1', 'no');
  }
  add_action('wp_ajax_wd_seo_dismiss', 'staff_bp_install_notice_status');
}
?>