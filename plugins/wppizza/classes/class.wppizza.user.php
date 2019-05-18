<?php
/**
* WPPIZZA_USER Class
*
* @package     WPPIZZA
* @subpackage  WPPIZZA_USER
* @copyright   Copyright (c) 2015, Oliver Bach
* @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
* @since       3.0
*
*/
if ( ! defined( 'ABSPATH' ) ) exit;/*Exit if accessed directly*/
/********************************************************************************************



********************************************************************************************/
class WPPIZZA_USER{
	function __construct() {

		/***************
			[add selected fields to the registration process - frontend]
		***************/
		add_action( 'register_form', array( $this, 'wppizza_user_register_formfields_markup') );
		add_action( 'user_register', array( $this, 'wppizza_user_register_formfields_save'), 100 );
		/**
			registration fields - also invoked outside is_admin for themed profiles
		**/
		add_action( 'show_user_profile', array( $this, 'wppizza_user_info') );
		add_action( 'personal_options_update', array( $this, 'wppizza_user_register_update_meta' ));

		/**
			multisite
		**/
		if(is_multisite()){
			add_action( 'signup_extra_fields', array( $this, 'wppizza_user_register_formfields_markup') );
			add_filter( 'add_signup_meta',array($this, 'wppizza_ms_user_register_add_signup_meta'));//capture the data
			add_action( 'wpmu_activate_user', array($this, 'wppizza_user_register_formfields_save'), 10, 3 );//get the meta data out of signups and push it into wp_usermeta during activation
		}

		/**
			admin only
		**/
		if(is_admin()){
			/**
				registration fields - admin
			**/
			add_action( 'edit_user_profile', array( $this, 'wppizza_user_info') );
			add_action( 'edit_user_profile_update', array( $this, 'wppizza_user_register_update_meta' ));
		}

	}

	/**
		update profile
	**/
	function wppizza_user_register_update_meta($user_id){
		if ( !current_user_can( 'edit_user', $user_id ) ) { return false; }
		/* enabled formfields*/
		$formfields=WPPIZZA()->helpers->enabled_formfields();
		foreach( $formfields as $field ) {
		if($field['key']!='cemail' && !empty($_POST[''.WPPIZZA_SLUG.'_'.$field['key']])) {
			$sanitizeInput = wppizza_validate_string($_POST[''.WPPIZZA_SLUG.'_'.$field['key']]);
			update_user_meta( $user_id, ''.WPPIZZA_SLUG.'_'.$field['key'], $sanitizeInput );
		}}
		/**distinctly add email from wp email field**/
		$sanitizeEmail=wppizza_validate_string($_POST['email']);
		update_user_meta( $user_id, ''.WPPIZZA_SLUG.'_cemail', $sanitizeEmail );
	}

	/*
		multisite signup add meta
	*/
	function wppizza_ms_user_register_add_signup_meta($meta){
		$formfields=WPPIZZA()->helpers->enabled_formfields();
	    foreach( $formfields as $field ) {
	    if(!empty($field['onregister']) && isset($_POST[''.WPPIZZA_SLUG.'_'.$field['key']])) {
	    	/**selects/radios should be stored by index**/
	    	if($field['type']=='select' || $field['type']=='radio'){
	    		$posted=$_POST[''.WPPIZZA_SLUG.'_'.$field['key']];
	    		$sanitizeInput = isset($field['value'][$posted]) ? $posted : null;
	    	}else{
	    		$sanitizeInput=wppizza_validate_string($_POST[''.WPPIZZA_SLUG.'_'.$field['key']]);
	    	}

			$meta[''.WPPIZZA_SLUG.'_'.$field['key']]=$sanitizeInput;
		}}

	return $meta;
	}



	/***
		profile page - admin or themed profiles
	***/
	function wppizza_user_info($user){
		global $wppizza_options;

		/* get user meta data */
		$userMetaData=$this->user_meta($user->ID);

		/* get enabled formfields*/
		$formfields=WPPIZZA()->helpers->enabled_formfields();


		if($wppizza_options['localization']['user_profile_label_additional_info']!=''){
			print'<h3>'.$wppizza_options['localization']['user_profile_label_additional_info'].'</h3>';
		}
		print'<table class="form-table">';
			foreach( $formfields as $field ) {

				/**lets exclude disabled and "email" as wp already has this of course, as well as gratuities**/
				if($field['type']!='email' && $field['type']!='tips' && !empty($field['onregister'])){

				$selectedValue=!empty($userMetaData[''.WPPIZZA_SLUG.'_'.$field['key'].'']) ? esc_attr($userMetaData['wppizza_'.$field['key'].'']) : '';

				print'<tr><th><label for="'.WPPIZZA_SLUG.'_'.$field['key'].'">' . $field['lbl'] . '</label></th><td>';

					/**normal text input**/
					if ( $field['type']=='text'){
			    		print'<input type="text" name="'.WPPIZZA_SLUG.'_'.$field['key'].'" id="'.WPPIZZA_SLUG.'_'.$field['key'].'" value="'.$selectedValue.'" class="regular-text" />';
					}
					/**textareas**/
					if ( $field['type']=='textarea'){
						print'<textarea name="'.WPPIZZA_SLUG.'_'.$field['key'].'" id="'.WPPIZZA_SLUG.'_'.$field['key'].'" rows="5" cols="30">'.$selectedValue.'</textarea>';
					}
					/**select**/
					if ( $field['type']=='select'){
						print'<select name="'.WPPIZZA_SLUG.'_'.$field['key'].'" id="'.WPPIZZA_SLUG.'_'.$field['key'].'">';
							print'<option value="">--------</option>';
							foreach($field['value'] as $key=>$value){
							print'<option value="'.$key.'" '.selected($key,$selectedValue,false).'>'.$value.'</option>';
							}
						print'</select>';
					}
					/**checkbox**/
					if ($field['type']=='checkbox'){
						print'<input type="checkbox" name="'.WPPIZZA_SLUG.'_'.$field['key'].'" id="'.WPPIZZA_SLUG.'_'.$field['key'].'" value="1" '.checked($key,$selectedValue,false).' />';
					}
					/**radio**/
					if ($field['type']=='radio'){
						foreach($field['value'] as $key=>$radio_value){
							echo'<span><input type="radio" name="'.WPPIZZA_SLUG.'_'.$field['key'].'" id="'.WPPIZZA_SLUG.'_'.$field['key'].'"  '.checked($radio_value,$selectedValue,false).' value="'.$radio_value.'"/>'.$radio_value.' </span>';
						}
					}
				print"</td></tr>";
			}}
		print"</table>";
	}


	/******************************************************
	*
	*	[show selected fields in WP registration form]
	*
	******************************************************/
	function wppizza_user_register_formfields_markup(){

		$formfields=WPPIZZA()->helpers->enabled_formfields();

	    foreach($formfields as $field){
	    	if(!empty($field['onregister'])) {


			/* name / id / label for*/
			$name_id = ''.WPPIZZA_SLUG.'_'.$field['key'].'';
			/** class **/
			$class= 'input';

			/* entered value */
			$input_value = !empty($_POST[ ''.WPPIZZA_SLUG.'_'.$field['key']]) ? stripslashes(wppizza_validate_string($_POST[ ''.WPPIZZA_SLUG.'_'.$field['key']])) : '' ;

			/*
				output
			*/
	 		echo'<p>';
	 			/* label */
	 			echo'<label for="' . $name_id . '">';
	 			/* text input */
	 			if ( $field['type']=='text'){
	 				echo''.$field['lbl'].'<br />';
	 				echo'<input type="text" name="' . $name_id . '" id="' . $name_id . '" class="' . $class . '" value="'. $input_value . '" size="20" />';
	 			}
				/**textareas**/
				if ( $field['type']=='textarea'){
					echo''.$field['lbl'].'<br />';
					print'<textarea name="' . $name_id . '" id="' . $name_id . '" class="' . $class . '" rows="5" cols="30">' . $input_value . '</textarea>';
				}
				/**select**/
				if ( $field['type']=='select'){
					echo''.$field['lbl'].'<br />';
					print'<select name="' . $name_id . '" id="' . $name_id . '" class="' . $class . '">';
						print'<option value="">--------</option>';
						foreach($field['value'] as $key => $select_value){
							print'<option value="' . $key . '" '.selected($key,$field_value,false).'>' . $select_value . '</option>';
						}
					print'</select>';
				}
				/**checkbox**/
				if ( $field['type']=='checkbox'){
					echo''.$field['lbl'].' ';
					echo'<input type="checkbox" name="' . $name_id . '" id="' . $name_id . '" class="" value="1" />';
				}
				/**radio**/
				if ( $field['type']=='radio'){
					echo''.$field['lbl'].'<br />';
					$i=0;
					foreach($field['value'] as $key => $select_value){
						/* show radio options, preselecting first one */
						echo'<span><input type="radio" name="' . $name_id . '" id="' . $name_id . '"  value="'. $select_value . '"  '.checked($i,0,false).'/>'.$select_value.' </span>';
					$i++;
					}
				}


	 			echo'</label>';
	 		echo'</p>';
	    	}
	    }
	}
	/******************************************************
	*
	*	[save wppizza formfields that were added to WP registration form]
	*
	******************************************************/
	function wppizza_user_register_formfields_save( $user_id,  $password = '', $meta = array()){
	    $userdata       = array();
		$userdata['ID'] = $user_id;
		$formfields=WPPIZZA()->helpers->enabled_formfields();

		/** loop trough enabled form fields **/
	    foreach( $formfields as $field ) {
	    	/** only capture fields that were enabled to be used for registration **/
	    	if(!empty($field['onregister']) && isset($_POST[''.WPPIZZA_SLUG.'_'.$field['key']])) {
	    		$sanitizeInput=wppizza_validate_string($_POST[''.WPPIZZA_SLUG.'_'.$field['key']]);
				update_user_meta( $user_id, ''.WPPIZZA_SLUG.'_'.$field['key'], $sanitizeInput );
			}
		}
		/**distinctly add email from wp email field**/
		if(isset($_POST['user_email'])){
			$sanitizeEmail=wppizza_validate_string($_POST['user_email']);
			update_user_meta( $user_id, ''.WPPIZZA_SLUG.'_cemail', $sanitizeEmail );
		}

	 $new_user_id = wp_update_user( $userdata );
	}

	/******************************************************
	*
	*	[update meta from order page ]
	*	set user_id to force update for specific user (when creating account)
	******************************************************/
	function update_profile($user_id = false, $userdata = false, $force_update = false){

		/*
			can users register  and is there actually any data to update ?
		*/
		$users_can_register = is_multisite() ? apply_filters('option_users_can_register', false) :  get_option('users_can_register');
		/* users cannot register anyway or userdata empty, bail */
		if(empty($users_can_register) || empty($userdata)){
			return;
		}

		/*
			user id 0 / not logged in /  no userdata and not forced update, bail
		*/
		if((empty($user_id) || !is_user_logged_in()) && !$force_update ){return;}


		/*
			get all enabled formfields
		*/
		$formfields=WPPIZZA()->helpers->enabled_formfields();
		/**
			loop trough enabled form fields checking they are enabled for registration
			and update what we can
		**/
	    foreach( $formfields as $key=>$field ) {
    	/** only capture fields that were enabled to be used for registration **/
	    	if(!empty($field['onregister']) && isset($userdata[$key])) {
				update_user_meta( $user_id, ''.WPPIZZA_SLUG.'_'.$key, $userdata[$key]['value'] );
			}
		}

	return;
	}

	/******************************************************
	*
	*	[create account from order page when order has been successfully executed]
	*
	******************************************************/
	function create_account($order_id = false, $userdata = false){
		global $wp_version;
		/*
			 already logged in, users cannot register, no userdata or no email, bail
		*/
		$set_email = !empty($userdata['cemail']['value']) ? $userdata['cemail']['value'] : false ;
		$can_register = is_multisite() ? apply_filters('option_users_can_register', false) :  get_option('users_can_register');
		if(	is_user_logged_in() || empty($can_register) || empty($set_email) ){
			return;
		}

		/************************************************
			check if email exists already
			if it does not carry on adding account
		************************************************/
		$user_id = username_exists( $set_email );
		$email_id = email_exists( $set_email );


		/**
			user already exists, just login and update meta
			i dont think one should be doing this
			as anyone could use any others email to get logged in ...!?
		**/
		//if($user_id && $email_id){
		//	/* just login */
		//	wp_set_auth_cookie( $user_id );
		//	/** update user profile */
		//	$this->update_profile($user_id);
		//}


		/**
			new user
		**/
		if(!$user_id && !$email_id){

			/*
				change name and email address "From" for registration emals (to avoid it simply sayin "Wordpress")
				@since 3.7.1
			*/
			add_filter( 'wp_mail_from', array($this, 'registrations_sender_email' ));
			add_filter( 'wp_mail_from_name', array($this, 'registrations_sender_name' ));

			/*generate a pw**/
			$user_password = wp_generate_password( 10, true );
			/*create the user**/
			$user_id_new = wp_create_user( $set_email, $user_password, $set_email );
			/*send un/pw to user*/
			if($user_id_new && $user_password!=''){/*bit of overkill*/

				$new_user_notification = apply_filters('wppizza_new_user_notification', 'both');/* should return 'user', 'admin' or 'both'. default 'both' */

				/*old wp versions <4.3**/
 				if ( version_compare( $wp_version, '4.3', '<' ) ) {
            		wp_new_user_notification( $user_id_new, $user_password );
        		}
 				if ( version_compare( $wp_version, '4.3', '==' ) ) {
            		wp_new_user_notification( $user_id_new, $new_user_notification );
        		}
        		if ( version_compare( $wp_version, '4.3.1', '>=' ) ) {
					wp_new_user_notification( $user_id_new, null, $new_user_notification );
        		}
        		/**login too*/
				wp_set_auth_cookie( $user_id_new );
				/**turn off admin bar front by default**/
				update_user_meta( $user_id_new, 'show_admin_bar_front', 'false' );
				/** force update user profile */
				$this->update_profile($user_id_new, $userdata, true);

				/***************************************************************
					associate order with this userid now
				****************************************************************/
				$update_db_values = array();
				/** amend wp_user_id */
				$update_db_values['wp_user_id'] 		= array('type'=> '%d', 'data' =>(int)$user_id_new);
				/* update db */
				$order_update_user_id = WPPIZZA()->db->update_order(false, $order_id, false , $update_db_values);

			}
		}
	return;
	}

	/******************************************************
	*
	*	[set user registration from email and name
	*	instead of default "Wordpress" , "wordpress@...."]
	*	@since 3.7.1
	*
	******************************************************/
	function registrations_sender_email( $email_address ) {
		/*
			set to do not reply if part before @ is still "wordpress"
		*/
		$x_email_address = explode('@', $email_address);
		if(!empty($x_email_address[0]) && strtolower(trim($x_email_address[0])) == 'wordpress' && !empty($x_email_address[1])){
			/* simply replace "wordpress" as the domain has already been dealt with by wp_mail function */
			$email_address = 'do-not-reply@'.$x_email_address[1];
		}
	return $email_address;
	}
	function registrations_sender_name( $email_from ) {
		/*
			replace email from, if it is still set to be wordpress here
		*/
		if(empty($email_from) || strtolower(trim($email_from)) == 'wordpress' ){
			global $blog_id;
			$blog_info = WPPIZZA() -> helpers -> wppizza_blog_details($blog_id);

			$email_from = wp_specialchars_decode($blog_info['blogname'], ENT_QUOTES );
		}
	return $email_from;
	}

	/******************************************************
	*
	*	[login form markup on order page]
	*
	******************************************************/
	/************************************************************************
		[output login form or logout link on order page or user history or admin order history by shortcode]
	************************************************************************/
	function login_form($show_registration_disabled = false, $do_login = true, $force_login = false){
		global $wppizza_options;
		$txt=$wppizza_options['localization'];
		$users_can_register = is_multisite() ? apply_filters('option_users_can_register', false) :  get_option('users_can_register');
		$login_form_args = array('echo'=>false, 'remember' => false);

		/*
			if: not forcing to show login,
			or: registration disabled sitewide,
			or: already logged in,
			or nothing in cart (if on orderpage)
			do nothing
		*/
		if(empty($force_login)){

			if(empty($users_can_register) && $show_registration_disabled && !is_user_logged_in()){
				return __('Sorry, user registration is disabled on this system', 'wppizza-admin');
			}

			if(empty($users_can_register) || is_user_logged_in() || empty($do_login)){
				return;
			}
		}

		/**
			login enabled
		**/
		if($do_login) {

			/* set classes */
			$anchor_name = '' . WPPIZZA_PREFIX . '-login';
			$class = '' . WPPIZZA_PREFIX . '-login';
			$class_toggle = '' . WPPIZZA_PREFIX . '-login-option';
			$class_show = '' . WPPIZZA_PREFIX . '-login-show';
			$class_cancel = '' . WPPIZZA_PREFIX . '-login-cancel';
			$class_fieldset = '' . WPPIZZA_PREFIX . '-fieldset ' . WPPIZZA_PREFIX . '-login-fieldset';
			$class_form = '' . WPPIZZA_PREFIX . '-login-form';
			$class_info = '' . WPPIZZA_PREFIX . '-login-info';
			$class_password = '' . WPPIZZA_PREFIX . '-login-lostpw';


			$login_form = apply_filters('wppizza_filter_login_markup', wp_login_form(apply_filters('wppizza_filter_loginform_arguments', $login_form_args)));
			/* let's add a nonce, just for the hell of it. cannot do any harm */
			$nonce = wp_nonce_field( ''.WPPIZZA_PREFIX.'_nonce_login',''.WPPIZZA_PREFIX.'_nonce_login', false, false);
			$login_form = str_ireplace('</form', $nonce.'</form', $login_form);
			/*
				ini markup array
			*/
			$markup = array();
			/*
				get markup
			*/
			if(file_exists( WPPIZZA_TEMPLATE_DIR . '/markup/global/login.php')){
				require(WPPIZZA_TEMPLATE_DIR.'/markup/global/login.php');
			}else{
				require(WPPIZZA_PATH.'templates/markup/global/login.php');
			}
			/*
				apply filter if required and implode for output
			*/
			$markup = apply_filters('wppizza_filter_login_widget_markup', $markup);
			$markup = implode('', $markup);


			return $markup;
		}
	}

	/********************************************************************************
		[output
		div with radio options to continue as guest or simultaneous registration]
		will not output anything if registration is disabled or emails are not on
		order page
	********************************************************************************/
	function profile_options(){
		global $wppizza_options;
		$txt = $wppizza_options['localization'];
		$enabled_formfields = WPPIZZA()->helpers->enabled_formfields();
		$users_can_register = is_multisite() ? apply_filters('option_users_can_register', false) :  get_option('users_can_register');
		$user_session = WPPIZZA()->session-> get_userdata();
		/***********************************************************
			check if we have the email set to enabled and required
			as otherwise new registration on order will not work
			as there's noweher to send the password to
		*************************************************************/
		$can_register = (isset($enabled_formfields['cemail']) && !empty($enabled_formfields['cemail']['required'])) ? true : false ;
		/** profle update ? */
		$profile_update = (is_user_logged_in() && !empty($users_can_register)) ? true : false;
		/** create account */
		$create_account = (!is_user_logged_in() && !empty($users_can_register) && !empty($can_register)) ? true : false;

		/*
			user is logged in and
			registration enabled
			add profile update checkbox
		*/
		if($profile_update) {

			$id = WPPIZZA_PREFIX . '_profile_update';
			$class = WPPIZZA_PREFIX . '_profile_update';
			$name = WPPIZZA_PREFIX . '_profile_update';
			$checked = checked(!empty($user_session[''.WPPIZZA_SLUG.'_profile_update']),true,false);



			/*
				ini markup array
			*/
			$markup = array();
			/*
				get markup
			*/
			if(file_exists( WPPIZZA_TEMPLATE_DIR . '/markup/global/profile.update.php')){
				require(WPPIZZA_TEMPLATE_DIR.'/markup/global/profile.update.php');
			}else{
				require(WPPIZZA_PATH.'templates/markup/global/profile.update.php');
			}

			/*
				filter and
				implode for output
			*/
			$markup = apply_filters('wppizza_filter_profile_update_markup', $markup);
			$markup = implode('',$markup);

		return $markup;
		}


		/*
			user is NOT logged in and
			registration enabled (with email set as form field)
			add register user / continue as guest
			radio options, provided email field exists and is required
		*/
		if($create_account) {
			$id_create_account = WPPIZZA_PREFIX . '-create-account';
			$class_create_account = WPPIZZA_PREFIX . '-create-account';
			$id_account_guest = WPPIZZA_PREFIX . '_account_guest';
			$id_account_register = WPPIZZA_PREFIX . '_account_register';
			$class_guest = ''.WPPIZZA_PREFIX . '_account '.WPPIZZA_PREFIX . '_account_guest';
			$class_register = ''.WPPIZZA_PREFIX . '_account '.WPPIZZA_PREFIX . '_account_register';
			$name = WPPIZZA_PREFIX . '_account';
			$checked_guest = checked(empty($user_session[''.WPPIZZA_SLUG.'_account']),true,false);
			$checked_register = checked(!empty($user_session[''.WPPIZZA_SLUG.'_account']),true,false);
			$id_register_info = WPPIZZA_PREFIX . '-user-register-info';

			/*
				ini markup array
			*/
			$markup = array();
			/*
				get markup
			*/
			if(file_exists( WPPIZZA_TEMPLATE_DIR . '/markup/global/profile.register.php')){
				require(WPPIZZA_TEMPLATE_DIR.'/markup/global/profile.register.php');
			}else{
				require(WPPIZZA_PATH.'templates/markup/global/profile.register.php');
			}
			/*
				filter and
				implode for output
			*/
			$markup = apply_filters('wppizza_filter_profile_register_markup', $markup);
			$markup = implode('',$markup);

		return $markup;
		}

	/* if it gets here, markup will be an empty string */
	return '';
	}


	/******************************
		fieldset markup userdata
		labels and inputs
		or selected only
	******************************/
	function formfields_inputs($ffs){

		$formfields =array();

		foreach($ffs as $key=>$ff){
			/*
				omit tips as they are displayed in the order summary
			*/
			if($ff['type'] != 'tips'){


				/*
					allow label etc filtering per formfield inputs
				*/
				$ff = apply_filters('wppizza_filter_formfields_inputs_'.$key.'', $ff );

				/*
					ini array
				*/
				$formfields[$key] = array();

				/* set class */
				$formfields[$key]['class'] = ''.WPPIZZA_PREFIX.'-'.$key.'';
				/* set field */
				$formfields[$key]['field'] = '';

				/* normal links - no inputs */
				if($ff['type']=='link'){
					$formfields[$key]['field'] .= '<label for="'. $key .'"' . $ff['required_class'] . '>';
					$formfields[$key]['field'] .= '' . $ff['label'] . '';
					$formfields[$key]['field'] .= '</label>';
				}

				/* text / emails / (tips are displayed in subtotals) */
				if(in_array($ff['type'],array('text', 'email'))){
					$formfields[$key]['field'] .= '<label for="'. $key .'"' . $ff['required_class'] . '>';
					$formfields[$key]['field'] .= '' . $ff['label'] . '';
					$formfields[$key]['field'] .= '</label>';
					$formfields[$key]['field'] .= '<input id="'. $key .'" name="'. $key.'"  type="text" value="' . $ff['value'] . '" placeholder="' .$ff['placeholder'] . '"  ' . $ff['required_attribute'] . ' />';
				}

				/* textarea */
				if($ff['type']=='textarea'){
					$formfields[$key]['field'] .= '<label for="'. $key .'"' . $ff['required_class'] . '>';
					$formfields[$key]['field'] .= '' . $ff['label'] . '';
					$formfields[$key]['field'] .= '</label>';
					$formfields[$key]['field'] .= '<textarea id="'. $key .'" name="'. $key.'" placeholder="' .$ff['placeholder'] . '" ' . $ff['required_attribute'] . '>' . $ff['value'] . '</textarea>';
				}

				/* checkbox -  with label _after_ input*/
				if($ff['type']=='checkbox'){
					$formfields[$key]['field'] .= '<label for="'. $key .'"' . $ff['required_class'] . '>';
					$formfields[$key]['field'] .= '<input id="'. $key .'" name="'. $key.'"  type="checkbox" value="1"  ' . $ff['required_attribute'] . ' '.checked($ff['value'], true, false).'/>';
					$formfields[$key]['field'] .= '' . $ff['label'] . '';
					$formfields[$key]['field'] .= '</label>';
				}

				/* multicheckbox */
				if($ff['type']=='multicheckbox'){
					// convert comma separated value back to (trimmed) array
					$val_as_array = array_map('trim', explode(',' , $ff['value']) );

					$formfields[$key]['field'] .= '<label for="'. $key .'"' . $ff['required_class'] . '>';
					$formfields[$key]['field'] .= '' . $ff['label'] . '';
					$formfields[$key]['field'] .= '</label>';
					$formfields[$key]['field'] .= '<div class="'.WPPIZZA_PREFIX.'-multicheckbox">';
					foreach($ff['options'] as $k => $option){
						$formfields[$key]['field'] .= '<label><input id="'. $key .'_'.$k.'" value="'. $option .'" name="'. $key.'[]"  type="checkbox" value="'.$k.'"  ' . $ff['required_attribute'] . ' '.checked( ( !empty($val_as_array) && in_array($option, $val_as_array)) ,true, false).'/>'.$option.' </label>';
					}
					$formfields[$key]['field'] .= '</div>';
				}

				/* radio */
				if($ff['type']=='radio'){
					$formfields[$key]['field'] .= '<label for="'. $key .'"' . $ff['required_class'] . '>';
					$formfields[$key]['field'] .= '' . $ff['label'] . '';
					$formfields[$key]['field'] .= '</label>';
					$formfields[$key]['field'] .= '<div class="'.WPPIZZA_PREFIX.'-radio">';
					foreach($ff['options'] as $k=>$option){
						$formfields[$key]['field'] .= '<label><input id="'. $key .'_'.$k.'" value="'. $option .'" name="'. $key.'"  type="radio" value="1"  ' . $ff['required_attribute'] . ' '.checked($ff['value'], $option, false).'/>'.$option.' </label>';
					}
					$formfields[$key]['field'] .= '</div>';
				}

				/* select */
				if($ff['type']=='select'){
					$formfields[$key]['field'] .= '<label for="'. $key .'"' . $ff['required_class'] . '>';
					$formfields[$key]['field'] .= '' . $ff['label'] . '';
					$formfields[$key]['field'] .= '</label>';
					$formfields[$key]['field'] .= '<select id="'. $key .'" name="'. $key.'" ' . $ff['required_attribute'] . ' >';
						foreach($ff['options'] as $option){
							$set = wppizza_decode_entities_trim($ff['value']);
							$opt = wppizza_decode_entities_trim($option['value']);
							$formfields[$key]['field'] .= '<option value="'. $option['value'] .'" '.selected($set, $opt, false).'>'. $option['label'] .'</option>';
						}
					$formfields[$key]['field'] .= '</select>';
				}

				/* hidden, just add hidden field  */
				if($ff['type']=='hidden'){
					$formfields[$key]['field'] .= '<input id="'. $key .'" name="'. $key.'"  type="hidden" value="' . $ff['value'] . '" />';
				}

				/* html,  adding some custom html after it all - unused in plugin itself , but might come in handy for 3rd party plugins*/
				/* to additionally bypass the above too and just have the html, set 'type' to something not used above (like html for example) */
				if(!empty($ff['html'])){
					$formfields[$key]['field'] .= $ff['html'];
				}
			}
		}

		/*************************************

			markup

		*************************************/
		/*
			ini array
		*/
		$markup = array();
		/*
			get markup
		*/
		if(file_exists( WPPIZZA_TEMPLATE_DIR . '/markup/global/formfields.inputs.php')){
			require(WPPIZZA_TEMPLATE_DIR.'/markup/global/formfields.inputs.php');
		}else{
			require(WPPIZZA_PATH.'templates/markup/global/formfields.inputs.php');
		}
		/*
			apply filter if required and implode for output
		*/
		$markup = apply_filters('wppizza_filter_formfields_inputs', $markup, $formfields);
		$markup = implode('', $markup);


	return $markup;
	}

	/******************************
		fieldset markup userdata
		labels and values only , no inputs
	******************************/
	function formfields_values($ffs, $page){

		$formfields =array();
		if(!empty($ffs)){
		foreach($ffs as $key=>$ff){

			/*
				allow filtering of formfield values (label for example) - passing on page parameters too
			*/
			$ff = apply_filters('wppizza_filter_formfields_values_'.$key.'', $ff, $page);

			/*
				ini array
			*/
			$formfields[$key] = array();
			/* set class */
			$formfields[$key]['class'] = ''.WPPIZZA_PREFIX.'-'.$key.'';
			/* set label */
			$formfields[$key]['label'] = $ff['label'];
			/* set value */
			$formfields[$key]['value'] = is_array($ff['value']) ? implode(', ',$ff['value']) : $ff['value'] ;

		}}
		/*************************************

			markup

		*************************************/
		/*
			ini array
		*/
		$markup = array();
		/*
			get markup
		*/
		if(file_exists( WPPIZZA_TEMPLATE_DIR . '/markup/global/formfields.values.php')){
			require(WPPIZZA_TEMPLATE_DIR.'/markup/global/formfields.values.php');
		}else{
			require(WPPIZZA_PATH.'templates/markup/global/formfields.values.php');
		}
		/*
			apply filter if required and implode for output
		*/
		$markup = apply_filters('wppizza_filter_formfields_values', $markup, $formfields, $page);
		$markup = implode('', $markup);


	return $markup;

	}

	/********************************************************************************

		get user meta data (if logged in)
		used in self and markup.pages
	return array or false
	********************************************************************************/
	function user_meta($user_id = false, $single_key = ''){

		/** userid set */
		if($user_id){

			$user_metadata=get_user_meta( $user_id, $single_key, true);

			/* returning single value only */
			if(!empty($single_key)){
				return $user_metadata;
			}

			/* return as single dimension array */
			$metadata = array();
			foreach($user_metadata as $meta_key=>$meta_value){
				$metadata[$meta_key] = $meta_value[0];
			}

			/*
				selectively override cemail with registered email address
			*/
			$registered_user_data = get_userdata($user_id);
			$registered_user_email = $registered_user_data -> user_email;
			//add/override cemail with user account registered email unless it exists as distinct meta data
			$metadata[WPPIZZA_SLUG.'_cemail'] = !empty($metadata[WPPIZZA_SLUG.'_cemail']) ? $metadata[WPPIZZA_SLUG.'_cemail'] : $registered_user_email ;


		return $metadata;
		}

		/** user logged in, get id and metadata from there */
		if(is_user_logged_in()){
			$user_id = get_current_user_id();
			$user_metadata = get_user_meta( $user_id, $single_key, true);

			/* returning single value only */
			if(!empty($single_key)){
				return $user_metadata;
			}

			/* return as single dimension array */
			$metadata = array();
			foreach($user_metadata as $meta_key=>$meta_value){
				$metadata[$meta_key] = $meta_value[0];
			}

			/*
				selectively override cemail with registered email address
			*/
			$registered_user_data = get_userdata($user_id);
			$registered_user_email = $registered_user_data -> user_email;
			//add/override cemail with user account registered email unless it exists as distinct meta data
			$metadata[WPPIZZA_SLUG.'_cemail'] = !empty($metadata[WPPIZZA_SLUG.'_cemail']) ? $metadata[WPPIZZA_SLUG.'_cemail'] : $registered_user_email ;

		return $metadata;
		}


		/** no user id and not logged in */
		if(!$user_id && !is_user_logged_in()){
				$user_metadata = false;
		return $user_metadata;
		}

	}
}
?>