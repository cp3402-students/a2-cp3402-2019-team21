<?php
/**
* WPPIZZA_INSTALL_UPDATE Class
*
* @package     WPPIZZA
* @subpackage  Install / Update
* @copyright   Copyright (c) 2016, Oliver Bach
* @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
* @since       3.0
*
*/
if ( ! defined( 'ABSPATH' ) ) exit;/*Exit if accessed directly*/

/************************************************************************************************************************
*
*
*	INSTALL/UPDATE
*
*
************************************************************************************************************************/
class WPPIZZA_INSTALL_UPDATE{

	function __construct() {
		/* register plugin checking for requirements */
		register_activation_hook( WPPIZZA_PLUGIN_INDEX, array($this, 'check_requirements'));
		/* check if we are installing or updating */
		add_action('admin_init', array( $this, 'install_or_update'));
		/* nag screens */
		add_action('admin_notices', array( $this, 'admin_nagscreens') );
		/** admin ajax **/
		add_action('wppizza_ajax_admin', array( $this, 'admin_nagscreens_ajax'));

	}
	/**************************************
	*
	*	do we need to install, or update
	*
	**************************************/
	function install_or_update(){
		global $wppizza_options;
		if(empty($wppizza_options)){
			/* install */
			$this -> install_plugin();
		}else{
			/* update */
			$this -> update_plugin($wppizza_options);
		}
	}
	/*************************************
	*
	*	install plugin
	*
	*************************************/
	function install_plugin(){
		global $blog_id, $wpdb ;
		/* for consistency with multisite setups */
		$blog_ids = array();
		$blog_ids[$blog_id] = $wpdb -> prefix;
		/* create orders table */
		$this->set_table_schema($blog_ids, false);
		/* insert options, pages, categories and user capabilities */
		$default_options = $this->install_options_pages_categories_caps();
		/* initialize templates options */
		$template_values = $this->initialize_template_values($blog_ids);
		/* wppizza install action hook */
		do_action('wppizza_plugin_install', $default_options);
		/*
			redirect after install for wppizza to show up
		*/
		wp_redirect(admin_url('edit.php?post_type='.WPPIZZA_POST_TYPE.'&page=order_settings'));
	exit();
	}
	/*************************************
	*
	*	update plugin if necessary or forced
	*
	*************************************/
	function update_plugin($wppizza_options){

		$force_update = false ;
		$reset_admin_caps = false ;
		$installed_version = $wppizza_options['plugin_data']['version'];
		/*
			allow admin with cap 'update_plugins' to force update of plugin via get variable
		*/
		if(is_admin()){
			global $current_user;
			$user_roles = array_flip($current_user->roles);
			if(isset($user_roles['administrator']) && current_user_can( 'update_plugins' ) && !empty($_GET['wppizza_plugin_update']) && $_GET['wppizza_plugin_update'] == 'forced' ){
				$force_update = true ;
			}
		}
		/**
			check if we actually need to update anything at all
		**/
		if(version_compare( $installed_version, WPPIZZA_VERSION, '<' ) || !empty($force_update)  || !empty($reset_admin_caps) ){
			$updated_options = $this -> do_update($installed_version, $force_update, $reset_admin_caps);
		}
		return;
	}


	/**************************************************************************************************************************************************
	*
	*
	*
	*	update plugin depending on version
	*	allowing to force update to make sure table schema gets set as required (even if current version is already the latest one)
	*
	*
	*
	***************************************************************************************************************************************************/
	function do_update($installed_version, $force_update, $reset_admin_caps){
		global $wppizza_options;

		/*
			get all blog id's with an active wppizza install
		*/
		$blog_ids = $this->get_blog_ids();
		/*
			updating v3 to higher v3 version
		*/
		$is_v3_update = true;


		/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*
		*
		*	Update for all versions  < 3.6
		*	Replacing non-sensical (and not used) ENUM value 'OTHER' and 'NOTAPPLICABLE' payment_status values
		*	with 'INITIALISED' value before updating table schema which will not have those anymore
		*	Replacing all old 'COD' payment_status values with 'COMPLETED'
		*	Replacing all old 'PENDING' payment_status values with 'INPROGRESS'
		*
		*	(the chances are, non of these were never used/set anyway, but let's make sure)
		*	@since 3.6
		*
		*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\*/
		if ( version_compare( $installed_version, '3.6', '<' ) ) {
			$this->update_payment_status_table_column($blog_ids);
		}



		/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*
		*
		*	UPGRADE  v2.x to v3.x ONLY
		*	as of 3.x, account for 3.x beta too using < 2.999
		*	ONLY TO BE USED FOR UPDATES FROM VERSIONS < 3.x
		*	WILL ALWAYS UPDATE TABLE SCHEMA
		*
		*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\*/

		if ( version_compare( $installed_version, '2.999', '<' ) ) {

			/*
				update main options
			*/
			$this->v2x_to_v3_update_option_values($blog_ids, $installed_version);

			/*
				we need to overwrite global wppizza_options with new values now
			*/
			$wppizza_options = get_option(WPPIZZA_SLUG, 0);

			/*
				removing VERY OLD table columns
			*/
			$this->remove_obsolete_table_columns($blog_ids);

			/*
				update table schema
			*/
			$this->set_table_schema($blog_ids, true);

			/*
				update TABLE order values to get some consistency in
			*/
			/**
				get wppizza options of each blogs
				for updating otherwsie unknown values
				in "v2x_to_v3_update_table_values"
			**/
			$blog_wppizza_options = $this->get_blog_wppizza_options();
			$this->v2x_to_v3_update_table_values($blog_ids, $blog_wppizza_options);

			/*
				update user caps
			*/
			$this->install_user_caps(false);

			/*
				initialize templates options
			*/
			$this->initialize_template_values($blog_ids);

			/** set now newly installed version to upgrade to higher versions if the first update was missed**/
			$installed_version = WPPIZZA_VERSION;
			$is_v3_update = false;
		}


		/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*
		*
		*	update table schema
		*	ONLY if it wasn't an update from 2.x as that would have been done already
		*
		*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\*/
		if ( version_compare( $installed_version, '3', '>=' ) ) {
			if($is_v3_update){
				$this->set_table_schema($blog_ids, true);
			}



			/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*
			*
			*	Update for all versions  >= 3.5 and < 3.6
			*	making sure to reset email column to NULL first and then storing hashed email again
			*	so we do not need to manually run the re-indexer we had in  v3.5.0.1 and v3.5.0.2
			*
			*	@since 3.6
			*
			*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\*/
			if ( version_compare( $installed_version, '3.5', '>=' ) && version_compare( $installed_version, '3.6', '<' ) ) {
				$this->encrypt_email_column($blog_ids);
			}

			/* update options if necessary */
			$updated_options = $this->update_option_values($blog_ids, $installed_version);
		}


		/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*
		*
		*	allow to force an update of certain settings if set
		*
		*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\*/
		if(!empty($force_update)){
			$this->set_table_schema($blog_ids, true);
			$updated_options = $this->update_option_values($blog_ids, $installed_version);
		}


		/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*
		*
		*	allow a way to reset caps
		*	just in case there is a way to break them
		*	by setting $reset_admin_caps to true in $this->update_plugin() function
		*
		*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\*/
		if(!empty($reset_admin_caps)){
			$this->install_user_caps(true);
		}


		/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*
		*
		*	clear user/cart session
		*	when updating plugin
		*
		*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\*/
		WPPIZZA() -> session -> clear_session();


	return $updated_options;
	}

	/*************************************************************************************************************************************************************************
	*
	*
	*
	*
	*	HELPERS
	*
	*
	*
	*
	*************************************************************************************************************************************************************************/
	/*************************************************************************************
	*
	*
	*	get all blogid's tables we need to update
	*
	*
	*************************************************************************************/
	function get_blog_ids() {
		global $blog_id, $wpdb;

		/* array of blog ids and prefixes*/
		$blog_ids = array();
		if(is_multisite()){
	 	   	/*get all and loop through blogs*/
	 	   	$blogs = $wpdb->get_results("SELECT blog_id FROM ".$wpdb->blogs."", ARRAY_A);
			if ($blogs) {
				foreach($blogs as $blog) {
					switch_to_blog($blog['blog_id']);
					/*make sure plugin is active*/
					if(is_plugin_active(WPPIZZA_PLUGIN_INDEX)){
						$blog_ids[$blog['blog_id']] = $wpdb -> prefix ;
					}
					restore_current_blog();
				}
			}
		}else{
			$blog_ids[$blog_id] = $wpdb -> prefix;
		}

	return $blog_ids;
	}

	/*************************************************************************************
	*
	*
	*	get all blogid's tables we need to update
	*
	*
	*************************************************************************************/
	function get_blog_wppizza_options() {
		global $blog_id, $wpdb;

		/* array of blog ids and prefixes*/
		$blog_ids = array();
		if(is_multisite()){
	 	   	/*get all and loop through blogs*/
	 	   	$blogs = $wpdb->get_results("SELECT blog_id FROM ".$wpdb->blogs."", ARRAY_A);
			if ($blogs) {
				foreach($blogs as $blog) {
					switch_to_blog($blog['blog_id']);
					$wpp_options = get_option(WPPIZZA_SLUG, 0);
					/*make sure plugin is active*/
					if(is_plugin_active(WPPIZZA_PLUGIN_INDEX)){
						$blog_ids[$blog['blog_id']] = $wpp_options ;
					}
					restore_current_blog();
				}
			}
		}else{
			$wpp_options = get_option(WPPIZZA_SLUG, 0);
			$blog_ids[$blog_id] = $wpp_options;
		}

	return $blog_ids;
	}


	/*************************************************************************************
	*
	*	get default options set in subpages
	*
	*************************************************************************************/
	function get_default_options($install = false, $upgrade_from = null){
		$options = array();
		/**always set plugin version**/
		$options['plugin_data']['version'] = WPPIZZA_VERSION;
		/**initial install**/
		$options['plugin_data']['upgrade'] = empty($install) ? $upgrade_from : false;
		/**initial install - thank you for installing etc**/
		$options['plugin_data']['nag_notice'] = 1;

		/**apply filters to add options as required*/
		$options = apply_filters('wppizza_filter_setup_default_options', $options, $install);

	return $options;
	}
	/*************************************************************************************
	*
	*	set default capabilities
	*	$install bool to true to insert(or if forced force) false to update
	*************************************************************************************/
	function install_user_caps($install) {
		$caps = WPPIZZA()-> user_caps -> user_caps_ini($install);
	return $caps;
	}
	/*******************************************************************************************************
	*
	*
	*	create  / update orders table
	*
	*
	*******************************************************************************************************/
	function set_table_schema($blog_ids, $is_update){
		global $wpdb;
		$table_schema = array();

		/*********************************************
			loop through all order tables of all blogs
		**********************************************/
		foreach($blog_ids as $id=> $table_prefix){
			/*********************************************
				set order table
			**********************************************/
			$table = $table_prefix . WPPIZZA_TABLE_ORDERS;
			/*********************************************
				Drop Old Indexes if table exists to avoid
				duplicates and all sorts of other issues
				on update only
			**********************************************/
			if($is_update){
			if($wpdb->get_var("SHOW TABLES LIKE '$table'") == $table) {
				$tblIndexes = $wpdb->get_results("SHOW INDEX FROM " . $table ."", ARRAY_A);
				$tIdx=array();
				foreach($tblIndexes as $idx){
					if($idx['Key_name']!='PRIMARY' && !in_array($idx['Key_name'],$tIdx)){
						$tIdx[]=$idx['Key_name'];
					}
				}
				if(count($tIdx)>0){
					$sql = "ALTER TABLE " . $table ." DROP INDEX `".implode("`, DROP INDEX `",$tIdx)."`";
					$wpdb->query($sql);
				}
			}}

			/********************************************
				get $table_schema_sql
			********************************************/
			$table_schema_sql = $this -> get_table_schema($table);

			/********************************************
				update table using dbDelta
			********************************************/
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			$table_schema['table'] = dbDelta($table_schema_sql);

			/*********************************************
				set order meta table
			**********************************************/
			$meta_table = $table_prefix . WPPIZZA_TABLE_ORDERS_META;
			/********************************************
				get $table_schema_eta_sql
			********************************************/
			$meta_table_schema_sql = $this -> get_table_schema_meta($meta_table);
			/********************************************
				update table using dbDelta
			********************************************/
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			$table_schema['meta_table'] = dbDelta($meta_table_schema_sql);

		}

		return $table_schema;
	}
	/*****************************************************
	*
	*	do table schema using db delta
	*
	******************************************************/
	function get_table_schema($table = false){
		/********************************************
			set $table_schema_sql - implode array
		********************************************/
		require(WPPIZZA_PATH .'includes/upgrades/update.table_schema.php');
		$table_schema_sql = implode('',$table_schema_sql);

	return $table_schema_sql;
	}

	/*****************************************************
	*
	*	do table schema meta using db delta
	*
	******************************************************/
	function get_table_schema_meta($meta_table = false){
		/********************************************
			set $table_schema_sql - implode array
		********************************************/
		require(WPPIZZA_PATH .'includes/upgrades/update.table_schema_meta.php');
		$meta_table_schema_sql = implode('',$meta_table_schema_sql);

	return $meta_table_schema_sql;
	}

	/*****************************************************
	*
	*	some very old installs might still have a
	*	mail_construct column we can definitely get rid of
	*
	******************************************************/
	function remove_obsolete_table_columns($blog_ids){
		global $wpdb;
		/*********************************************
			loop through all order tables of all blogs
			and drop obsolute columns
		**********************************************/
		foreach($blog_ids as $id=> $table_prefix){
			/*********************************************
				set order table
			**********************************************/
			$table = $table_prefix . WPPIZZA_TABLE_ORDERS;
			$wpdb->hide_errors();
			/* check for column first */
			$has_mail_construct_column = $wpdb->get_results("SHOW FIELDS FROM ".$table." ", ARRAY_A);//and column_name = 'mail_construct'
			$column_search = array_search('mail_construct', wppizza_array_column($has_mail_construct_column, 'Field'));
			if(!empty($column_search)){
				$wpdb->query( "ALTER TABLE ".$table." DROP COLUMN mail_construct" );
			}
			$wpdb->show_errors();
		}
	}


	/*********************************************************************************************
	*
	* set old "COD" payment status to "COMPLETED"
	* set old/unused/non-sensical "NOTAPPLICABLE", "OTHER" and "ABANDONED" payment status to "INITIALIZED"
	* set old/unused "PENDING" payment status to "INPROGESS"
	*
	* before removing (when updating table schema) ENUM "COD", "NOTAPPLICABLE", "OTHER", "ABANDONED",  "PENDING" from payment_status
	*
	**********************************************************************************************/
	function update_payment_status_table_column($blog_ids){
		global $wpdb;
		/*********************************************
			loop through all order tables of all blogs
			before dropping obsolute column ENUM values
		**********************************************/
		foreach($blog_ids as $id=> $table_prefix){
			/*********************************************
				set order table
			**********************************************/
			$table = $table_prefix . WPPIZZA_TABLE_ORDERS;
			$wpdb->hide_errors();

			// update COD to COMPLETED
			$wpdb->update( $table, 	array( 'payment_status' => 'COMPLETED' ), array( 'payment_status' => 'COD' ), array('%s'), array( '%s' ));
			// update NOTAPPLICABLE to INITIALIZED
			$wpdb->update( $table, 	array( 'payment_status' => 'INITIALIZED' ), array( 'payment_status' => 'NOTAPPLICABLE' ), array('%s'), array( '%s' ));
			// update OTHER to INITIALIZED
			$wpdb->update( $table, 	array( 'payment_status' => 'INITIALIZED' ), array( 'payment_status' => 'OTHER' ), array('%s'), array( '%s' ));
			// update ABANDONED to INITIALIZED
			$wpdb->update( $table, 	array( 'payment_status' => 'INITIALIZED' ), array( 'payment_status' => 'ABANDONED' ), array('%s'), array( '%s' ));
			// update PENDING to INPROGESS
			$wpdb->update( $table, 	array( 'payment_status' => 'INPROGRESS' ), array( 'payment_status' => 'PENDING' ), array('%s'), array( '%s' ));

			$wpdb->show_errors();
		}
	}

	/*********************************************************************************************
	*
	* encrypt email column
	* making sure to null columns first before storing encrypted data (as this was already done differently in 3.5.0.1 and 3.5.0.2)
	* setting it to NULL first will help if we hit a timeout as the queryset will progressively
	* become smaller until all has been set using 'anonymised' column - that was added in 3.6 - as helper to not start from scratch all the time
	* in case of timeouts
	*
	* @param blogids
	* @return void
	* @since 3.6
	*
	**********************************************************************************************/
	function encrypt_email_column($blog_ids){
		global $wpdb;
		$wpdb->hide_errors();

		/*********************************************
			loop through all order tables of all blogs hashing emails if we can
		**********************************************/
		foreach($blog_ids as $id => $table_prefix){

			/*********************************************
				re-NULL email column to start off with

				use anonymised time flag as helper in case we
				have timeouts for some reasons so we can just reload
				page until all records are updated
			**********************************************/
			$table = $table_prefix . WPPIZZA_TABLE_ORDERS;
			$wpdb->query($wpdb->prepare(" UPDATE ".$table." SET email = NULL WHERE id > %d AND anonymised = '0000-00-00 00:00:00' ", 0));
			/*********************************************
				get rows with customer_ini data and email columns that are NULL
			**********************************************/
			$order_for_email_crypt = $wpdb->get_results($wpdb->prepare("SELECT id, customer_ini from ".$table." WHERE email IS NULL AND customer_ini !='' AND  id > %d ", 0), ARRAY_A);
			if(!empty($order_for_email_crypt)){
				foreach($order_for_email_crypt as $row){

					$order_id = $row['id'];
					$customer_ini = maybe_unserialize($row['customer_ini']);

					/* update with email - hashed if required - or '' if empty value */
					$this_order_email = !empty($customer_ini['cemail']) ? wppizza_maybe_encrypt_decrypt($customer_ini['cemail'], true, 190, true) : '' ;

					/* update row with hashed email */
					$updated = $wpdb->query($wpdb->prepare("UPDATE ".$table." SET email = %s , anonymised = %s  WHERE id = %d ", $this_order_email, '1970-01-01 00:00:01', $order_id));
				}
			}
		}
		/*********************************************
			loop through all order tables of all blogs again
			when we are done with all rows for all tables, to reset anonymised time
			as we should new be quite safe on the timeout front
		**********************************************/
		foreach($blog_ids as $id=> $table_prefix){
			$table = $table_prefix . WPPIZZA_TABLE_ORDERS;
			$wpdb->query($wpdb->prepare(" UPDATE ".$table." SET anonymised = '0000-00-00 00:00:00' WHERE id > %d ", 0));
		}

		$wpdb->show_errors();
	}

	/*********************************************
	*	v3.x update table values
	*	having updated the table to v3.x
	*	normalize some order data after table update
	*	and update some order_ini keys for consistency
	**********************************************/
	function v2x_to_v3_update_table_values($blog_ids, $blog_wppizza_options){
		global $wpdb;
		/*********************************************
			loop through all order tables of all blogs
		**********************************************/
		foreach($blog_ids as $blog_id => $table_prefix){
			$wppizza_blog_options = $blog_wppizza_options[$blog_id];
			/****************************
				loop through results and normalize / update
			****************************/
			require(WPPIZZA_PATH .'includes/upgrades/v2.to.v3.update.table_values.php');
		}
	}
	/*********************************************
	*
	*	v3.x update main OPTION values
	*	as some option and option keys
	*	have changed in 3.x , update as required
	*	USE ONLY FOR UPDATES FROM VERSIONS < 3.x
	**********************************************/
	function v2x_to_v3_update_option_values($blog_ids, $installed_version){

		/**************************
			get default options
		**************************/
		$default_options = $this->get_default_options(false, $installed_version);
		$registered_widgets_class = 'wppizza_widgets';
		/*********************************************
			loop through all blogs
		**********************************************/
		foreach($blog_ids as $blog_id => $table_prefix){
			if(is_multisite()){
				/*
					blog to get that blogs options
				*/
				switch_to_blog($blog_id);
					/**************************
						update wppizza options
					**************************/
					$options_update = array();
					require(WPPIZZA_PATH .'includes/upgrades/v2.to.v3.update.options.php');
					update_blog_option($blog_id, WPPIZZA_SLUG, $options_update);


					/**************************
						update wppizza widget names
					**************************/
					$widget_data = get_blog_option($blog_id, 'widget_'.WPPIZZA_SLUG.'', 0);
					if(!empty($widget_data)){
						/* add(rename) new widget data */
						update_blog_option($blog_id, 'widget_'.$registered_widgets_class.'', $widget_data);
						/* remove old widget data */
						delete_blog_option($blog_id, 'widget_'.WPPIZZA_SLUG.'');
					}

					/**************************
						update sidebars
					**************************/
					$sidebar_widgets = get_blog_option($blog_id, 'sidebars_widgets', 0);
					$do_sidebar_update = false;
					if(!empty($sidebar_widgets)){
						foreach($sidebar_widgets as $sbKey => $sidebar){
							if(is_array($sidebar)){
							foreach($sidebar as $widgetKey => $widget_name){
								if(substr($widget_name,0,7) == ''.WPPIZZA_SLUG.'' && substr($widget_name,0,15) != $registered_widgets_class ){
									$sidebar_widgets[$sbKey][$widgetKey] = str_replace(''.WPPIZZA_SLUG.'', $registered_widgets_class, $widget_name);
									$do_sidebar_update = true;
								}
							}}
						}
					}
					if($do_sidebar_update){
						update_blog_option($blog_id, 'sidebars_widgets', $sidebar_widgets);
					}



				/*
					restore blog
				*/
				restore_current_blog();

			}else{
				/**************************
					update wppizza options
				**************************/
				$options_update = array();
				require(WPPIZZA_PATH .'includes/upgrades/v2.to.v3.update.options.php');
				update_option(WPPIZZA_SLUG, $options_update);

				/**************************
					update wppizza widget names
				**************************/
				$widget_data = get_option('widget_'.WPPIZZA_SLUG.'', 0);
				/* add(rename) new widget data */
				if(!empty($widget_data)){
					/* add(rename) new widget data */
					update_option('widget_'.$registered_widgets_class.'', $widget_data);
					/* remove old widget data */
					delete_option('widget_'.WPPIZZA_SLUG.'');
				}
				/**************************
					update sidebars
				**************************/
				$sidebar_widgets = get_option('sidebars_widgets', 0);
				$do_sidebar_update = false;
				if(!empty($sidebar_widgets)){
					foreach($sidebar_widgets as $sbKey => $sidebar){
						if(is_array($sidebar)){
						foreach($sidebar as $widgetKey => $widget_name){
							if(substr($widget_name,0,7) == ''.WPPIZZA_SLUG.'' && substr($widget_name,0,15) != $registered_widgets_class ){
								$sidebar_widgets[$sbKey][$widgetKey] = str_replace(''.WPPIZZA_SLUG.'', $registered_widgets_class , $widget_name);
								$do_sidebar_update = true;
							}
						}}
					}
				}
				if($do_sidebar_update){
					update_option('sidebars_widgets', $sidebar_widgets);
				}


			}
		}
	}

	/*********************************************
	*
	*	general v3.x update main OPTION values
	*	used only if we have previously updated
	*	to v3.x+
	**********************************************/
	function update_option_values($blog_ids, $installed_version){
		global $wppizza_options;

		/**************************
			get default, current options
		**************************/
		$default_options = $this->get_default_options(false, $installed_version);

//		/**************************
//			passing set, added and removed options to action hook,
//			by blog id
//			might be useful one day
//		**************************/
//		$option_parameters = array();

		/*********************************************
			loop through all blogs
		**********************************************/
		foreach($blog_ids as $blog_id => $table_prefix){
			if(is_multisite()){
				/*
					switch blog to get that blogs options
				*/
				switch_to_blog($blog_id);
					/**************************
						update wppizza options
					**************************/
					$update_options = array();
					require(WPPIZZA_PATH .'includes/upgrades/update.options.php');

					/**************************
		 				update access capabilities
					**************************/
					$update_options += $this->install_user_caps(false);
					/**************************
		 				update options
					**************************/
					update_blog_option($blog_id, WPPIZZA_SLUG, $update_options);

					/**
						passing set, added and removed options to action hook,
						might be useful here or there
					**/
					$option_parameters = array();
					$option_parameters['blog_id'] = $blog_id;
					$option_parameters['table_prefix'] = $table_prefix;
					$option_parameters['options_added'] = $added_options;
					$option_parameters['options_removed'] = $removed_options;
					$option_parameters['options_current'] = $update_options;

					/* wppizza update plugin action hook passing on parameters*/
					do_action('wppizza_plugin_update', $option_parameters);


				/*
					restore blog
				*/
				restore_current_blog();

			}else{
				/**************************
					update wppizza options
				**************************/
				$update_options = array();
				require(WPPIZZA_PATH .'includes/upgrades/update.options.php');

				/**************************
		 			update access capabilities
				**************************/
				$update_options += $this->install_user_caps(false);

				/**************************
		 			update options
				**************************/
				update_option(WPPIZZA_SLUG, $update_options);

				/**
					passing set, added and removed options to action hook,
					might be useful here or there
				**/
				$option_parameters = array();
				$option_parameters['blog_id'] = $blog_id;
				$option_parameters['table_prefix'] = $table_prefix;
				$option_parameters['options_added'] = $added_options;
				$option_parameters['options_removed'] = $removed_options;
				$option_parameters['options_current'] = $update_options;

				/* wppizza update plugin action hook passing on parameters*/
				do_action('wppizza_plugin_update', $option_parameters);


			}
		}
	return;
	}


	/*********************************************
	*	v3.x update TEMPLATE OPTION values
	*	template handling has changed in 3.x
	*	USE ONLY FOR UPDATES FROM VERSIONS < 3.x
	*	@return void
	**********************************************/
	function initialize_template_values($blog_ids){

		/***************************
			get default email/print templates
		***************************/
		$TEMPLATES = new WPPIZZA_MANAGE_TEMPLATES();
		$templates_default = $TEMPLATES -> set_default_templates();

		/*********************************************
			loop through all blogs
		**********************************************/
		foreach($blog_ids as $blog_id => $table_prefix){
			if(is_multisite()){
				/**************************
					add template defaults
				**************************/
				foreach($templates_default as $tplKey => $template_options){
					$template_option_name = WPPIZZA_SLUG.'_'.$tplKey.'';
					update_blog_option($blog_id, $template_option_name, $template_options);
				}
			}else{
				/**************************
					add template defaults
				**************************/
				foreach($templates_default as $tplKey => $template_options){
					$template_option_name = WPPIZZA_SLUG.'_'.$tplKey.'';
					update_option($template_option_name, $template_options, false);
				}
			}
		}
	}

	/*********************************************
	*
	*	v3.x install options, pages etc
	*
	**********************************************/
	function install_options_pages_categories_caps(){
		global $wppizza_options;

		/**************************
		 	ini options array
		**************************/
		$default_options = array();

		/**************************
			get default options
		**************************/
		$default_options = $this->get_default_options(true);

		/**************************
		 	add access capabilities
		**************************/
		$default_options += $this->install_user_caps(true);

		/**************************
			get default pages
		**************************/
		require(WPPIZZA_PATH .'includes/upgrades/install.default_pages.php');
		/**************************
			set orderpage
		**************************/
		$default_options['order_settings']['orderpage'] = $default_pages['orderpage_id'];

		/**************************
			get/set default items and categories
		**************************/
		$pages_parent_id = $default_pages['pages_parent_id'];
		require(WPPIZZA_PATH .'includes/upgrades/install.default_items_and_categories.php');
		/**************************
			prices, sizes, additives,
			cat hierarchy etc
			to save in options table
		**************************/
		$default_options['sizes'] = $default_sizes;
		$default_options['additives'] = $default_additives;
		$default_options['layout']['category_sort_hierarchy'] = $category_sort_hierarchy;

		/**************************
			set global wppizza options
			to be able to use them now
			subsequently
		**************************/
		$wppizza_options = $default_options;

		/** insert options */
		update_option(WPPIZZA_SLUG, $default_options);

	return $default_options;
	}


	/**************************************
	*
	*	nag screens
	*
	**************************************/
	function admin_nagscreens(){
		global $wppizza_options, $current_screen;
		/*
			dismissible notices
		*/
		$nag_notices = array();
		/*
			install notice
		*/
		if(empty($wppizza_options['plugin_data']['upgrade']) && !empty($wppizza_options['plugin_data']['nag_notice'])){

			//$pluginInfoInstallationUrl = admin_url( 'plugin-install.php?tab=plugin-information&plugin='.WPPIZZA_SLUG.'&section=installation&TB_iframe=true&width=600&height=800');
			//$pluginInfoFaqUrl = admin_url( 'plugin-install.php?tab=plugin-information&plugin='.WPPIZZA_SLUG.'&section=faq&TB_iframe=true&width=600&height=800');

			$pluginInfoInstallationUrl = 'http://docs.wp-pizza.com/getting-started/?section=setup';
			$pluginInfoFaqUrl = 'http://docs.wp-pizza.com/faqs/';


			$nag_notices['install'] = '';
			$nag_notices['install'].='<b>'.sprintf(__('%s Installed. Thank you. ','wppizza-admin'),WPPIZZA_NAME).'</b><br/><br/>';
			$nag_notices['install'].='<br/>';
			$nag_notices['install'].='<b>'.__('Quick start:.','wppizza-admin').'</b><br/>';
			$nag_notices['install'].='<b>'.sprintf(__('A) Go to "Appearance -> Widget" and put the "%s  widget" - setting type to "cart" - into a sidebar.','wppizza-admin'), WPPIZZA_NAME).'</b><br/>';
			$nag_notices['install'].='<b>'.sprintf(__('B) Add the created %s pages to your menu by going to "Appearance -> Menu" (Suggestion: use "Our Menu" as parent page and add all other %s created pages as children of it)','wppizza-admin'), WPPIZZA_NAME, WPPIZZA_NAME).'</b><br/>';
			$nag_notices['install'].='<b>'.__('C) Go to "Settings -> General" and ensure your timezone setting is correct','wppizza-admin').'</b><br/>';
			$nag_notices['install'].='<b>'.sprintf(__('D) Go to "%s -> Opening Times" and edit as appropriate.','wppizza-admin'), WPPIZZA_NAME).'</b><br/>';
			$nag_notices['install'].='<br/>';
			$nag_notices['install'].='<b>'.__('For more details please make sure to read the <a href="'.$pluginInfoInstallationUrl.'" target="_blank">"Installation Instructions"</a> and <a href="'.$pluginInfoFaqUrl.'" target="_blank">"FAQ"</a>','wppizza-admin').'</b>';
			//$nag_notices['install'].=__('For more details please make sure to read the <a href="'.$pluginInfoInstallationUrl.'" taget="thickbox">"Installation Instructions"</a> and <a href="'.$pluginInfoFaqUrl.'" class="thickbox">"FAQ"</a>','wppizza-admin');
			$nag_notices['install'].='<br/><br/>';
		}

		/*output*/
		if(!empty($nag_notices)){
			foreach($nag_notices as $key => $nag_notice){
				print'<div id="'.WPPIZZA_PREFIX.'_admin_notice_'.$key.'" class="notice notice-success '.WPPIZZA_PREFIX.'_admin_notice" style="padding:20px;">'.$nag_notice.'<br/><a href="javascript:void(0);" onclick="wppizza_dismiss_notice(\''.$key.'\'); return false;" class="button-primary">'.__('dismiss','wppizza-admin').'</a></div>';

			}
		}

		/*
			static - non dismissible - notices (DMARC ETC)
		*/
		$static_notices = array();
		/*
			dmarc
		*/
		if(!$wppizza_options['order_settings']['dmarc_nag_off'] && empty($_POST) && $current_screen->post_type == WPPIZZA_POST_TYPE){
			/*get domain*/
			$urlobj=parse_url(get_site_url());
			$domain=$urlobj['host'];
			if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs)) {
    			$domain=$regs['domain'];
  			}
			/*check if in static from email*/
			$staticFromEmail=$wppizza_options['order_settings']['order_email_from'];
			$pos = strpos($staticFromEmail, $domain);
			if ($pos === false) {
				$static_notices['dmarc'] = sprintf(__('<b>EMAIL DMARC POLICIES:</b><br /><br />
					Due to policy changes by many email servers (yahoo, google hotmail - others may follow suit) it is <span style="color:red; font-weight:600">strongly advised to set a static email address in %s -> Order Settings -> "From email address"</span>, that corrosponds to your domain name.<br />
					As your domain appears to be <b>"%s"</b> you should <span style="color:red; font-weight:600">set an email address like "abc@%s"</span><br />
					<span style="color:red; font-weight:600">If you do NOT do this, some emails might NOT get delivered to you and/or your customers</span> as they might be in violation of DMARC policies.<br /><br />
					<b>This notice will remain until acted upon or you forcefully switch it off in %s -> Order Settings -> "Turn Off DMARC Notice" .</b><br /><br />
					Thank you<br/>(search on your favourite searchengine for "DMARC" if you would like to find out more.)','wppizza-admin'), WPPIZZA_NAME, $domain, $domain, WPPIZZA_NAME );
			}
		}

		/*output*/
		if(!empty($static_notices)){
			foreach($static_notices as $key => $static_notice){
				print'<div id="'.WPPIZZA_PREFIX.'_admin_notice_'.$key.'" class="notice notice-error '.WPPIZZA_PREFIX.'_admin_notice" style="padding:20px;">'.$static_notice.'</div>';

			}
		}
	}

	/*******************************************************************************************************************************************************
	*
	* 	[admin ajax - dismissing nagscreens ]
	*
	********************************************************************************************************************************************************/
	function admin_nagscreens_ajax($wppizza_options){
		/******************************************************
			[dismiss install nag]
		******************************************************/
		if($_POST['vars']['field']=='dismiss-notice'){
			if($_POST['vars']['key'] == 'install'){
    			$wppizza_options['plugin_data']['nag_notice']=0;
    			update_option(WPPIZZA_SLUG, $wppizza_options);
        		die();
			}
			exit();
		}
	}
	/**************************************
	*
	*	check_requirements on activation
	*
	**************************************/
	function check_requirements(){
		/* bypass requirements check */
		$constant_wppizza_install_ignore_requirements = WPPIZZA_INSTALL_IGNORE_REQUIREMENTS;/* cast to var for php 5.3 */
		if(!empty($constant_wppizza_install_ignore_requirements)){
			return;
		}
		global $wpdb, $wppizza_options;

		/*
			ini as true
		*/
		$requirements_met = true ;

		/*
			ini notices array
		*/
		$notices = array();

		/*
			checks and error messages
		*/
		/* mbstring */
		$check['mbstring'] = array('check' => function_exists( 'mb_internal_encoding' ), 'notice' => sprintf( __( "%s requires the mbstring extension to be installed", 'wppizza-admin' ), WPPIZZA_NAME));

		/* php */
		$min_version_php = '5.3';
		$check['php_min_version'] = array('check' => version_compare( $min_version_php , PHP_VERSION, '<' ), 'notice' => sprintf( __( "%s requires PHP version %s or higher", 'wppizza-admin' ), WPPIZZA_NAME, $min_version_php ));

		/* mysql */
		$min_version_sql = '5.5';
		$check['mysql_min_version'] = array('check' => version_compare( $min_version_sql, $wpdb->db_version(), '<' ), 'notice' => sprintf( __( "%s requires MySQL version %s or higher", 'wppizza-admin' ), WPPIZZA_NAME, $min_version_sql ));

		/* session support*/
		$session_support = (session_start()) ?  true : false;
		$check['session_support'] = array('check' => $session_support , 'notice' => sprintf( __( "%s requires PHP session support", 'wppizza-admin' ), WPPIZZA_NAME));


		/* session savepath*/
		$ssp = ini_get( 'session.save_path' );
		$session_save_path = (!empty($ssp)) ?  true : false;
		$check['session_save_path'] = array('check' => $session_save_path , 'notice' => sprintf(__( "%s requires PHP session support. Your <a href='http://php.net/manual/en/function.session-save-path.php'>session.save_path</a> in your php.ini does not appear to be set. This must be set and be read/writeable for sessions to work.", 'wppizza-admin' ), WPPIZZA_NAME));


		/* check max_input_vars*/
		$min_input_vars = ( ini_get( 'max_input_vars' ) >= 500) ?  true : false;
		$check['min_input_vars'] = array('check' => $min_input_vars , 'notice' =>  __( 'your php.ini should allow 500 (or more) max_input_vars (php default is usually 1000)', 'wppizza-admin' ));


		/* wppizza min version if updating to v3*/
		if(!empty($wppizza_options)){
			$min_version_wppizza = '2.16.11.10';
			$check['wppizza_v3_update'] = array('check' => version_compare( $min_version_wppizza, $wppizza_options['plugin_data']['version'], '<' ), 'notice' => sprintf( __( "To update %s to version 3+, you must first update to version %s+ of the version 2 branch", 'wppizza-admin' ), WPPIZZA_NAME, $min_version_wppizza ));
		}

		/*
			run checks
		*/
		foreach($check as $k=> $arr){
			if(empty($arr['check'])){
				$requirements_met = false ;
				$notices[] = $arr['notice'];
			}
		}

		/*
			deactivate if a check failed
		*/
		if(empty($requirements_met)){
			$error = '';
			$error .= '<div style="text-align:center"><b>' .sprintf( __( "%s de-activated ", 'wppizza-admin'), WPPIZZA_NAME ).'</b></div>';
			$error .= '<br /><br />';
			$error .= '<b>' . sprintf( __( "Sorry, there were some problems activating the %s plugin:", 'wppizza-admin'), WPPIZZA_NAME, WPPIZZA_VERSION ).'</b>';
			$error .= '<br /><br />';
			$error .= sprintf( __( "The following requirements must be met for %s version %s to work: ", 'wppizza-admin'), WPPIZZA_NAME, WPPIZZA_VERSION );
			$error .= '<br />';
			$error .= '<ul>';
			foreach($notices as $notice){
				$error .= '<li>'.$notice.'</li>';
			}
			$error .= '</ul>';

			$error .= '' . __( "However, if you feel this message to be in error, you can add the following constant to your wp-config.php to bypass this requirement check", 'wppizza-admin').'';

			$error .= '<div style="text-align:center; font-weight:600;background:#fcfcfc;padding:20px;margin:10px 0; border:1px solid #efefef"><pre>define("WPPIZZA_INSTALL_IGNORE_REQUIREMENTS", true);</pre></div>';


			deactivate_plugins(WPPIZZA_PLUGIN_INDEX);

			wp_die( $error );
		exit();/* just for good measure */
		}
	return ;
	}

}
$WPPIZZA_INSTALL_UPDATE = new WPPIZZA_INSTALL_UPDATE();
?>