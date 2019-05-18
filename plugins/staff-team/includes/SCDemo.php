<?php
class SCDemo{
    static function installDemoData() {
        $deno_success = get_option('demo_success');
        if($deno_success!=intval(1)){
            self::set_contact_post();
            add_option('demo_success' , 1);
        }
    }
	
	public static function demo_global_activate($networkwide)
    {
			if (function_exists('is_multisite') && is_multisite()) {
				// Check if it is a network activation - if so, run the activation function for each blog id.
				if ($networkwide) {
					global $wpdb;
					// Get all blog ids.
					$blogids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
					foreach ($blogids as $blog_id) {
						switch_to_blog($blog_id);
						self::installDemoData();
						restore_current_blog();
					}
					return;
				}
			}
			self::installDemoData();
  }

    public static function set_contact_post(){
        $id= self::setDemoCategory();
        include SC_DIR . '/includes/SCDemoData.php';
        $data = returnDemoData();
        $demoData = array();
        foreach($data as $contact){
            $parent_post_id = wp_insert_post( $contact['contact'] );
            $post_param = array();
            $post_param["Nationality"]= array(0 => $contact['param'][0]);
            $post_param["Occupation"]= array(0 => $contact['param'][1]);
            add_post_meta($parent_post_id ,'params' , $post_param);
            array_push($demoData, $parent_post_id);
            wp_set_object_terms( $parent_post_id, 'democategory', 'cont_category', false);

            update_post_meta($parent_post_id, 'want_email', $contact['option']['want_email']);
            update_post_meta($parent_post_id, 'email', $contact['option']['email']);
            $img_url = SC_URL.'/'.$contact['option']['img'];
            $img_id = self::set_thumbnail_image($img_url , $parent_post_id);
            $wde_sample_data['wde_images'][] = $img_id;
            set_post_thumbnail($parent_post_id, $img_id);
        }
        add_option('delete_demo_data' ,$demoData);
        add_option('delete_demo_category' ,$id);

    }


    public static function set_thumbnail_image($img_url, $parent_post_id = 0) {
        $wp_upload_dir = wp_upload_dir();
        $filetype = wp_check_filetype(basename($img_url), null);
        $filename = $wp_upload_dir['path'] . '/' . wp_unique_filename($wp_upload_dir['path'], basename($img_url));
        copy($img_url, $filename);
        $attachment = array(
            'guid'           => $filename,
            'post_mime_type' => $filetype['type'],
            'post_title'     => preg_replace('/\.[^.]+$/', '', basename($filename)),
            'post_content'   => '',
            'post_status'    => 'inherit'
        );

        $attach_id = wp_insert_attachment($attachment, $filename, $parent_post_id);

        require_once( ABSPATH . 'wp-admin/includes/image.php' );

        $attach_data = wp_generate_attachment_metadata($attach_id, $filename);
        wp_update_attachment_metadata($attach_id, $attach_data);

        return $attach_id;
    }

    public static function setDemoCategory(){
        $labels = array(
            'name' => 'Category',
            'search_items' => 'Search Category',
            'parent_item' => 'Parent Category',
            'parent_item_colon' => 'Contact',
            'edit_item' => 'Edit Category',
            'update_item' => 'Update Category',
            'add_new_item' => 'Add New Category',
            'new_item_name' => 'New Genre Category',
            'menu_name' => 'Category'
        );
        $args = array(
            'hierarchical' => true,
            'labels' => $labels,
            'show_ui' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'cont_category')
        );
        register_taxonomy('cont_category', 'contact', $args);
        $cat = wp_insert_term('DemoCategory', 'cont_category');
        if(!is_wp_error($cat)){
            add_option( 'taxonomy_' . $cat['term_id'], array( 0 => 'Nationality', 1 => 'Occupation' ) );
            return $cat;
        }
    }



}