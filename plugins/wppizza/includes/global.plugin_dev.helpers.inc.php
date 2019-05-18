<?php
if ( ! defined( 'ABSPATH' ) ) exit;/*Exit if accessed directly*/
/*####################################################################################################################################################################
#
#
#
#	@since 3.2.5+
#	WPPizza - globally available plugin development helper functions
#	[under development / amended  as needed - NOT TO BE USED YET]
#
#	_phs_ abbr. "plugin helper standalone" (couldnt think of anything better) for standalone plugins. i.e plugins that have their own option table entrya and subpage(s) with tabs.
#	_phi_ abbr. "plugin helper integrated" (couldnt think of anything better) for plugins that integrate directly into the main wppizza option pages (plugins will have their option/values added directly to the main wppizza_options. No additioanl subpages or access rights will be created)
#
####################################################################################################################################################################*/

/*******************************************************************************
*	@since 3.3.6
*
*	@var - string
*	@var - array()
*	@var - string
*
*	@return void;
*
*	install a plugin - admin
*	get plugin default settings and
*	- add options to main wppizza options
*******************************************************************************/
function wppizza_phi_install($plugin_slug, $plugin_settings){//, $settings_page

	if(empty($plugin_slug) || $plugin_slug == WPPIZZA_SLUG){
		die('the plugin slug must not be empty, and cannnot be "'.WPPIZZA_SLUG.'" ');
	}
	/*****************************************************
		adding this plugins default options
		(starting with a clean slate)
	*****************************************************/
	$defaultOptions = array();
	$defaultOptions = wppizza_phi_default_options($plugin_slug, $plugin_settings);//, $settings_page

	/*****************************************************
		install option
	*****************************************************/
	add_option($plugin_slug, $defaultOptions);
}

/*******************************************************************************
*	@since 3.3.6
*
*	@var - array()
*	@var - string
*	@var - string
*
*	@return array;
*
*	default options
*******************************************************************************/
function wppizza_phi_default_options($plugin_slug, $plugin_options){//, $settings_page

	$options = array();

	/*
		setting any non-editable global options
	*/
	if(isset($plugin_options['global'][$plugin_slug])){
		$options = $plugin_options['global'][$plugin_slug];
	}

	/*
		editable options: only using 'fields' key of this 'plugin_slug'  when installing
	*/
	$plugin_options = $plugin_options['fields'][$plugin_slug];

	/*
		iterate and validate
	*/
	if(!empty($plugin_options)){
	foreach($plugin_options as $field_key => $field_parameters){

		/*
			as key[0] == label
			lets only deal with key[1] here
		*/
		$field_parameters = !empty($field_parameters[1]) ? $field_parameters[1] : false;

		/*
			no options key as such set at all (text/textarea/email etc)
			making sure we have actually a default var for this
		*/
		if(isset($field_parameters['default'])){
			/*
				install option
			*/
			$options[$field_parameters['settings_page']][$field_key] = $field_parameters['default'];
		}

		/*
			check for suboptions
			but also saving parent
		*/
		if(isset($field_parameters['options']) && is_array($field_parameters['options'])){
			foreach($field_parameters['options'] as $option_key => $option_parameters){

				/*
					are there any suboptions ?
				*/
				if(isset($option_parameters['options']) && is_array($option_parameters['options'])){
				foreach($option_parameters['options'] as $sub_option_key => $sub_option_parameters){

					/*
						make sure we have actually a default var for this
					*/
					if(isset($sub_option_parameters['default'])){
						/*
							install option
						*/
						$options[$field_parameters['settings_page']][$sub_option_parameters['option']][$sub_option_key] = $sub_option_parameters['default'];
					}

				}}else{
					break;//no need to do the same thing multiple times
				}
			}
		}

	}}

return $options;
}
/*******************************************************************************
*	@since 3.3.6
*
*	@var - array()
*	@var - string
*	@var - string
*
*	@return array;
*
*	validated options according to validation_callback
*******************************************************************************/
function wppizza_phi_validate($plugin_slug, $plugin_options, $plugin_settings, $settings_page, $parent_slug = false ){//, $settings_page


	/*
		use $parent_slug as post_slug/key if set
	*/
	$post_slug = !empty($parent_slug) ? $parent_slug : $plugin_slug;


	/*
		make sure to get all currently set
		options for this plugin first
	*/
	$update_options = $plugin_options;

	/*
		iterate through plugin settings
		and validate any posted vars that were set
		for this settings page
	*/
	if(!empty($plugin_settings['fields'][$plugin_slug])){
	foreach($plugin_settings['fields'][$plugin_slug] as $field_key => $field_parameters){


		/*
			as the first key ([0]) is the label, let's simplyfy to
			only use the second one ([1])
		*/
		$field_parameters = $field_parameters[1];

		/*
			no options key as such set at all (text/textarea/email etc)
			making sure we have actually a posted var for this
		*/
		if(!isset($field_parameters['options']) ){//&& isset($_POST[$post_slug][$settings_page][$field_key])

			/*
				raw post val - might not be set for checkboxes
			*/
			$validated = isset($_POST[$post_slug][$settings_page][$field_key]) ? $_POST[$post_slug][$settings_page][$field_key] : false ;

			/*
				using callback if exists
			*/
			$validated = wppizza_phs_validate_by_callback($validated, $field_parameters['validation_callback']);
			/*
				update option
			*/
			$update_options[$settings_page][$field_key] = $validated;

		}

		/*
			check for suboptions
			but also saving parent
		*/
		if(isset($field_parameters['options']) && is_array($field_parameters['options'])){
			$iteration = 0;
			foreach($field_parameters['options'] as $option_key => $option_parameters){

				/*
					parent , first iteration only
				*/
				$iteration ++;

				/* text/email etc  - as checkboxes might be unset entirely we need to deal with them differently */
				if($iteration == 1){
					if(isset($_POST[$post_slug][$settings_page][$field_key])){

						/*
							raw post val
						*/
						$validated = $_POST[$post_slug][$settings_page][$field_key] ;
						/*
							using callback if exists
						*/
						$validated = wppizza_phs_validate_by_callback($validated, $field_parameters['validation_callback']);
						/*
							update option
						*/
						$update_options[$settings_page][$field_key] = $validated;

					}else{
						/*making sure to unset entirely if nothing set (for cases were checkboxes are used and none is selected) */
						unset($update_options[$settings_page][$field_key]);
					}
				}

				/*
					are there any suboptions ?
				*/
				if(isset($option_parameters['options']) && is_array($option_parameters['options'])){
				foreach($option_parameters['options'] as $sub_option_key => $sub_option_parameters){

					/*
						make sure we have actually a posted var for this
					*/
					if(isset($_POST[$post_slug][$settings_page][$sub_option_parameters['option']][$sub_option_key])){

						/*
							raw post val
						*/
						$validated = $_POST[$post_slug][$settings_page][$sub_option_parameters['option']][$sub_option_key] ;
						/*
							using callback if exists
						*/
						$validated = wppizza_phs_validate_by_callback($validated, $sub_option_parameters['validation_callback']);

						/*
							update option
						*/
						$update_options[$settings_page][$sub_option_parameters['option']][$sub_option_key] = $validated;
					}

				}}else{
					break;//no need to do the same thing multiple times
				}
			}
		}
	}}


	/*****************************************************
		update options
	*****************************************************/
	update_option($plugin_slug, $update_options);
return;
}

/*******************************************************************************
*	@since 3.8
*
*	@var - string
*	@var - array()
*	@var - array()
*	@var - string
*	@var - string
*
*	@return array;
*
*	validated options according to validation_callback for a plugin that is an addon to another plugin
*	where the options are nested according to options pages/tabs
*	very similar to wppizza_phi_validate, but $plugin_settings['fields'][$plugin_slug] array is one level deeper
*	to account for options pages/tabs and $POST values are parent keys
*******************************************************************************/
function wppizza_phi_validate_plugin_extend($plugin_slug, $plugin_options, $plugin_settings, $settings_page, $parent_slug){

	/*
		make sure to get all currently set
		options for this plugin first
	*/
	$update_options = $plugin_options;


	/*
		iterate through plugin settings
		and validate any posted vars that were set
		for this settings page
	*/
	if(!empty($plugin_settings['fields'][$plugin_slug])){
	foreach($plugin_settings['fields'][$plugin_slug] as $field_key => $field_parameters){


		/*
			as the first key ([0]) is the label, let's simplyfy to
			only use the second one ([1])
		*/
		$field_parameters = $field_parameters[1];


		/*
			no options key as such set at all (text/textarea/email etc)
			making sure we have actually a posted var for this
			and its in the plugin_settings->fields->slug  array that should be validated for this tab/option page
		*/
		if(!isset($field_parameters['options']) && $field_parameters['settings_page'] == $settings_page ){
			/*
				raw post val - might not be set for checkboxes
			*/
			$validated = isset($_POST[$parent_slug][$settings_page][$plugin_slug][$field_key]) ? $_POST[$parent_slug][$settings_page][$plugin_slug][$field_key] : false ;

			/*
				update option
			*/
			$update_options[$settings_page][$field_key] = $validated;

		}

		/*
			check for suboptions
			but also saving parent

			// NOT USED ANYWHERE YET, BUT SHOULD BE CHECKED
		*/
		if(isset($field_parameters['options']) && is_array($field_parameters['options']) && $field_parameters['settings_page'] == $settings_page){
			$iteration = 0;
			foreach($field_parameters['options'] as $option_key => $option_parameters){
				/*
					parent , first iteration only
				*/
				$iteration ++;
				if(isset($_POST[$parent_slug][$settings_page][$plugin_slug][$field_key]) && $iteration == 1){

					/*
						raw post val
					*/
					$validated = $_POST[$parent_slug][$settings_page][$plugin_slug][$field_key] ;
					/*
						using callback if exists
					*/
					$validated = wppizza_phs_validate_by_callback($validated, $field_parameters['validation_callback']);
					/*
						update option
					*/
					$update_options[$settings_page][$field_key] = $validated;

				}

				/*
					are there any suboptions ?
				*/
				if(isset($option_parameters['options']) && is_array($option_parameters['options'])){
				foreach($option_parameters['options'] as $sub_option_key => $sub_option_parameters){

					/*
						make sure we have actually a posted var for this
					*/
					if(isset($_POST[$parent_slug][$settings_page][$sub_option_parameters['option']][$sub_option_key])){
						/*
							raw post val
						*/
						$validated = $_POST[$parent_slug][$settings_page][$sub_option_parameters['option']][$sub_option_key] ;
						/*
							using callback if exists
						*/
						$validated = wppizza_phs_validate_by_callback($validated, $sub_option_parameters['validation_callback']);
						/*
							update option
						*/
						$update_options[$settings_page][$sub_option_parameters['option']][$sub_option_key] = $validated;
					}

				}}else{
					break;//no need to do the same thing multiple times
				}
			}
		}
	}}

	/*****************************************************
		update options
	*****************************************************/
	update_option($plugin_slug, $update_options);
return;
}


/*******************************************************************************
*	@since 3.2.5
*
*	@var - array()
*	@var - string
*	@var - string
*	@var - string
*
*	@return void;
*
*	install a plugin - admin
*	get plugin settings and
*	- add options to options_table
*	- set tabs/page capabilities
*******************************************************************************/
function wppizza_phs_install($plugin_settings, $option_name, $plugin_name, $caps_option_name = 'admin_access_caps'){

	/*
		ini options
	*/
	$defaultOptions = array();

	/*
		ini caps
	*/
	$caps = array();

	/*
		ini options that are to be wmpl'ed
	*/
	$wpml_strings = array();


	/*****************************************************
		set tabs/pages options provided there are some....
	*****************************************************/
	if(!empty($plugin_settings)){
	foreach($plugin_settings as $settings_key => $settings){

		/*
			set non-tabs/pages i.e general options
		*/
		if($settings_key !== 'tabs'){

			foreach($settings as $globalKey => $globalArray){

				$defaultOptions[$globalKey] = $globalArray;

			}
		}

		/*
			set tabs/pages options and get caps for each tab/page
		*/
		if($settings_key === 'tabs'){
		foreach($settings as $tabKey => $tabArray){

			/*
				caps for tab/page
			*/
			if(!empty($tabArray['cap'])){
				$caps[$tabKey]['cap'] = $option_name.'_'.$tabKey ;
			}

			/*
				options
			*/
			if(!empty($tabArray['options'])){
			foreach($tabArray['options'] as $sectionKey => $sectionOptions){

				if(!empty($sectionOptions)){
				foreach($sectionOptions as $optionKey => $optionValues){

					/* if optionKey = 'default' */
					if($optionKey === 'default'){
						$defaultOptions[$tabKey][$sectionKey] = $optionValues;
					}

					/* if 'default' key in array */
					if(isset($optionValues['default'])){
						$defaultOptions[$tabKey][$sectionKey][$optionKey] = $optionValues['default'];
					}

					/* wpml'ed */
					if(!empty($optionValues['wpml'])){
						$wpml_strings[$tabKey][$sectionKey][$optionKey] = !empty($optionValues['default']) ? $optionValues['default'] : '' ;
					}


				}}

			}}

		}}

	}}

	/*****************************************************
		set capabilities and add to options array
	*****************************************************/
	$defaultOptions += WPPIZZA()-> user_caps -> user_caps_ini(true, $caps, $caps_option_name);


	/*****************************************************
		register WPML strings - if any
	*****************************************************/
	if(function_exists('icl_register_string')){
		wppizza_phs_wpml_register($wpml_strings, $option_name, $plugin_name);
	}



	/*****************************************************
		install options
	*****************************************************/
	add_option($option_name, $defaultOptions);

return;
}
/*******************************************************************************
*	@since 3.2.5
*
*	@var - string
*	@var - array()
*	@var - array()
*	@var - string
*	@var - string
*	@var - string
*
*	@return void;
*
*	update a plugin - admin
*	get plugin settings and
*	- add new options to options_table
*	- set new tabs/page capabilities
*	- remove old options from options_table
*	- remove old  tabs/page capabilities
*******************************************************************************/
function wppizza_phs_update($current_version, $current_options, $plugin_settings, $option_name, $plugin_name, $caps_option_name = 'admin_access_caps'){

	if( is_admin() && version_compare($current_version, $current_options['plugin_data']['version'], '>') ){

		/*
			ini options as set now
		*/
		$defaultOptions = array();

		/*
			ini caps
		*/
		$caps = array();

		/*
			ini options that are to be wmpl'ed
			or to be removed from string translation
		*/
		$wpml_strings = array();
		$wpml_strings_obsolete = array();

		/*****************************************************
			set tabs/pages options provided there are some....
		*****************************************************/
		if(!empty($plugin_settings)){
		foreach($plugin_settings as $settings_key => $settings){

			/*
				set non-tabs/pages i.e general options
			*/
			if($settings_key !== 'tabs'){

				foreach($settings as $globalKey => $globalArray){

					$defaultOptions[$globalKey] = $globalArray;

				}
			}

			/*
				set tabs/pages options and get caps for each tab/page
			*/
			if($settings_key === 'tabs'){
			foreach($settings as $tabKey => $tabArray){

				/*
					caps for tab/page
				*/
				if(!empty($tabArray['cap'])){
					$caps[$tabKey]['cap'] = $option_name.'_'.$tabKey ;
				}

				/*
					options
				*/
				if(!empty($tabArray['options'])){
				foreach($tabArray['options'] as $sectionKey => $sectionOptions){

					if(!empty($sectionOptions)){
					foreach($sectionOptions as $optionKey => $optionValues){

						/* if optionKey = 'default' */
						if($optionKey === 'default'){
							$defaultOptions[$tabKey][$sectionKey] = $optionValues;
						}

						/* if 'default' key in array */
						if(isset($optionValues['default'])){
							$defaultOptions[$tabKey][$sectionKey][$optionKey] = $optionValues['default'];
						}

						/* wpml'ed */
						if(!empty($optionValues['wpml'])){
							$wpml_strings[$tabKey][$sectionKey][$optionKey] = !empty($optionValues['default']) ? $optionValues['default'] : '' ;
						}


					}}

				}}

			}}

		}}

		/*****************************************************
			set/update capabilities and add to options array
		*****************************************************/
		$defaultOptions += WPPIZZA()-> user_caps -> user_caps_ini(false, $caps, $caps_option_name, $current_options);

		/*****************************************************
			if a license array is defined add it here distinctly
			(as it has no options and would not be added to the default options)
		*****************************************************/
		if(!empty($current_options['license'])){
			$defaultOptions['license'] = $current_options['license'];
		}

		/*****************************************************
			obsolete options - do this first , before adding new ones
		*****************************************************/
		/* get options to remove */
		$removedOptions = wppizza_phs_update_array_diff_key_recursive($current_options, $defaultOptions);
		/* remove options from array */
		if(!empty($removedOptions)){
    		$current_options = wppizza_phs_array_remove_key_recursive($removedOptions, $current_options);
			/* get obsolete wpml strings */
			if(function_exists('icl_register_string')){
				$wpml_strings_obsolete = wppizza_phs_array_remove_key_recursive($wpml_strings, $removedOptions);
			}
		}

		/*****************************************************
			added options
		*****************************************************/
		/** get options to add **/
		$addedOptions = wppizza_phs_update_array_diff_key_recursive($defaultOptions, $current_options);
		/* add options to array */
		if(!empty($addedOptions)){
			$current_options = array_merge_recursive($current_options, $addedOptions);
		}


		/*****************************************************
			register new / remove obsolete WPML strings - if any
		*****************************************************/
		if(function_exists('icl_register_string')){
			wppizza_phs_wpml_register($wpml_strings, $option_name, $plugin_name, $wpml_strings_obsolete);
		}

		/*
			always overwrite with new version number
		*/
		$current_options['plugin_data']['version'] = $current_version;

	/*****************************************************
		current update options
	*****************************************************/
	return $current_options;
	}
return false;
}

/*******************************************************************************
*	@since 3.2.10
*	@var - array
*	@var - array
*	@return array;
*
*	on update plugin, recursivle compare current options with new options
*	to determine if options need to be removed or added
*******************************************************************************/
function wppizza_phs_update_array_diff_key_recursive (array $arr1, array $arr2) {
    $diff = array_diff_key($arr1, $arr2);
    $intersect = array_intersect_key($arr1, $arr2);
    foreach ($intersect as $k => $v) {
        if (is_array($arr1[$k]) && is_array($arr2[$k])) {
            $d = wppizza_phs_update_array_diff_key_recursive($arr1[$k], $arr2[$k]);
            if ($d) {
               $diff[$k] = $d;
            }
        }
    }
    return $diff;
}

/*******************************************************************************
*	@since 3.2.10
*	@var - array
*	@var - array
*	@return array;
*
*	on update plugin, remove obsolete options
*******************************************************************************/
function wppizza_phs_array_remove_key_recursive(array $defaults, $new_values) {
  $result = array();

  foreach ($defaults as $key => $val) {
    if (is_array($val) && isset($new_values[$key])) {
      $tmp = wppizza_phs_array_remove_key_recursive($val, $new_values[$key]);
      if ($tmp) {
        $result[$key] = $tmp;
      }
    }
    elseif (!isset($new_values[$key])) {
      $result[$key] = NULL;
    }
    elseif ($val != $new_values[$key]) {
      $result[$key] = $new_values[$key];
    }
    if (isset($new_values[$key])) {
      unset($new_values[$key]);
    }
  }

  $result = $result + $new_values;
  return $result;
}

/*******************************************************************************
*	@since 3.2.6
*	@var - string
*
*	@return void;
*
*	uninstall plugin, options, access rights
*******************************************************************************/
function wppizza_phs_uninstall($option_name){
	/* get roles */
	global $wp_roles;

	/*
		get options
	*/
	$plugin_options = get_option($option_name);


	/*
		get tabs(to determine caps set)
		will actually get a few that do not exist
		but for simplicities sake this is ok
	*/
	$plugin_caps = array();
	if(!empty($plugin_options)){
	foreach($plugin_options as $tab_key => $settings){
		$plugin_caps[] = $option_name . '_' . $tab_key ;
	}}



	/*delete options*/
	if ( is_multisite() ) {
		global $wpdb;
		$blogs = $wpdb->get_results("SELECT blog_id FROM {$wpdb->blogs}", ARRAY_A);
		if ($blogs) {
			foreach($blogs as $blog) {
				switch_to_blog($blog['blog_id']);

				/* delete db option entry */
				delete_option($option_name);
				/* delete access rights for each tab */
				foreach($wp_roles->roles as $roleName=>$v){
					$userRole = get_role($roleName);
					foreach($plugin_caps as $cap){
						$userRole->remove_cap( ''.$cap.'' );
					}
				}

			}
			restore_current_blog();
		}
	}else{

		/* delete db option entry */
		delete_option($option_name);
		/* delete access rights for each tab */
		foreach($wp_roles->roles as $roleName=>$v){
			$userRole = get_role($roleName);
			foreach($plugin_caps as $cap){
				$userRole->remove_cap( ''.$cap.'' );
			}
		}
	}

}
/*******************************************************************************
*	@since 3.2.5
*	@var - array()
*	@var - string
*
*	@return void;
*
*	get caps of current user for this plugin
*******************************************************************************/
function wppizza_phs_user_caps($plugin_settings, $option_name){

	/*
		ini array
	*/
	$caps = array();

	/*****************************************************
		set tabs/pages options provided there are some....
	*****************************************************/
	if(!empty($plugin_settings)){
	foreach($plugin_settings as $settings_key => $settings){

		/*
			set tabs/pages options and get caps for each tab/page
		*/
		if($settings_key === 'tabs'){
		foreach($settings as $tabKey => $tabArray){

			/*
				caps and label for tab/page
			*/
			if(!empty($tabArray['cap'])){
				$caps[$tabKey]['name'] = $tabArray['label'] ;
				$caps[$tabKey]['cap'] = $option_name.'_'.$tabKey ;
			}

		}}

	}}

	/*
		get current users caps of all available ones for this plugin
	*/
	$current_user_caps = WPPIZZA() -> user_caps -> get_current_user_caps($caps);


return  $current_user_caps;
}

/*******************************************************************************
*	@since 3.2.5
*	@var - array()
*	@var - string
*	@var - array
*
*	@return array;
*
*	get access rights checkboxes
*******************************************************************************/
function wppizza_phs_access_rights($option, $option_name, $user_caps){
	/*
		simply skip if not admin
	*/
	if(!is_admin()){return;}

	$caps = array();
	if(!empty($user_caps)){
	foreach($user_caps['caps'] as $capKey=>$cap){
		$caps[$user_caps['tabs'][$capKey]] = array('name' => $user_caps['name'][$capKey], 'cap' => $cap);
	}}

	$access_rights = WPPIZZA()->user_caps->user_echo_admin_caps($option, $option_name, $caps, $option, false);

return  $access_rights;
}
/*******************************************************************************
*	@since 3.2.5
*	@var - array()
*	@var - array()
*
*	@return array;
*
*	access rights checkboxes->apply caps to roles as set and save in options for reference
*******************************************************************************/
function wppizza_phs_access_rights_validate($posted, $args){

	/*
		get plugin setup options
	*/
	$caps_key = key($args);
	$class = new $args[$caps_key]['class']();
	$function = $args[$caps_key]['method'];
	$plugin_setup = $class -> $function();

	/*
	posted parameters/checkboxes - 1st parameter
	*/
	$access[0] = $posted;
	/*
		current options in db - 2nd parameter
	*/
	$access[1] = $args[$caps_key]['options'];
	/*
		all caps for all tabs - 3rd parameter
	*/
	$access[2] = array();
	foreach($plugin_setup['tabs'] as $cKey => $vals){
		$access[2][$cKey]['cap'] = $args[$caps_key]['slug'] . '_' . $cKey;
	}
	/*
		access cap key - 4th parameter
	*/
	$access[3] = $caps_key;



	/*
		update caps and return options to save in db
	*/
	$update_caps = WPPIZZA() -> user_caps -> user_caps_update($access[0], $access[1], $access[2], $access[3]);

/*
	return caps set as key/val pair to store in db to display (un-)checked vals in access option page
*/
return $update_caps;
}

/*******************************************************************************
*	@since 3.2.6
*	@var - array()
*	@var - string
*	@var - string
*
*	@return void;
*
*	register wpml strings
*******************************************************************************/
function wppizza_phs_wpml_register($wpml_strings, $option_name, $plugin_name, $wpml_strings_obsolete = array()){
	/* register any wpml strings */
	if(!empty($wpml_strings)){
	foreach($wpml_strings as $tab_key => $tab_sections){
		if(!empty($tab_sections)){
		foreach($tab_sections as $section_key => $section_options){
			if(!empty($section_options)){
			foreach($section_options as $option_key => $string){
				//$string_name = '"' . $tab_key . '" "' . $section_key . '" "' . $option_key . '"';
				$string_name = $option_key ;// should definitely work if all option_key are unique, else we perhaps need the above ?
				/* register strings if not already registered */
				$is_registered_string = icl_st_is_registered_string(WPPIZZA_NAME.' '.$plugin_name , $string_name);
				if (empty($is_registered_string)){
					icl_register_string( WPPIZZA_NAME.' '.$plugin_name, $string_name, $string );
				}
			}}
		}}
	}}

	/* un register any obsolete wpml strings */
	if(!empty($wpml_strings_obsolete)){
	foreach($wpml_strings_obsolete as $tab_key => $tab_sections){
		if(!empty($tab_sections)){
		foreach($tab_sections as $section_key => $section_options){
			if(!empty($section_options)){
			foreach($section_options as $option_key => $string){
				//$string_name = '"' . $tab_key . '" "' . $section_key . '" "' . $option_key . '"';
				$string_name = $option_key ;// should definitely work if all option_key are unique, else we perhaps need the above ?
				/* register strings if not already registered */
				$is_registered_string = icl_st_is_registered_string(WPPIZZA_NAME.' '.$plugin_name , $string_name);
				if (!empty($is_registered_string)){
					icl_unregister_string( WPPIZZA_NAME.' '.$plugin_name, $string_name );
				}
			}}
		}}
	}}

}

/*******************************************************************************
*	@since 3.2.6
*	@var - array()
*	@var - array()
*	@var - string
*
*	@return array;
*
*	translate wpml'ed strings
*******************************************************************************/
function wppizza_phs_wpml_translate($plugin_options, $plugin_settings, $plugin_name){//$plugin_name
	global $sitepress;

	/*
		simply return as is if no wpml exists or is same language etc etc
	*/
	if(
		empty($sitepress) || // sitepress non existant
		empty($plugin_options) || //no options yet (install for example)
		empty($plugin_settings) || //no settings (just for completeness)
		(is_admin() && (!defined( 'DOING_AJAX' ) || !DOING_AJAX) ) || //admin , but non-ajax (updateig plugin for example)
		!function_exists('icl_translate') || // icl_translate non existant
		!defined('ICL_LANGUAGE_CODE') || // ICL_LANGUAGE_CODE non existant
		ICL_LANGUAGE_CODE == $sitepress->get_default_language() // current and default language are the same
	){
		return $plugin_options;
	}

	/*****************************************************
		translate wpml'ed strings
	*****************************************************/
	$wpml_strings = array();
	if(!empty($plugin_settings)){
	foreach($plugin_settings as $settings_key => $settings){
		if($settings_key === 'tabs'){
		foreach($settings as $tabKey => $tabArray){
			if(!empty($tabArray['options'])){
			foreach($tabArray['options'] as $sectionKey => $sectionOptions){
				if(!empty($sectionOptions)){
				foreach($sectionOptions as $optionKey => $optionValues){
					/* wpml'ed */
					if(!empty($optionValues['wpml'])){
						//$un_wpml_strings[$tabKey][$sectionKey][$optionKey] = !empty($optionValues['default']) ? $optionValues['default'] : '' ;

						$plugin_options[$tabKey][$sectionKey][$optionKey] = icl_translate(WPPIZZA_NAME.' '.$plugin_name, $optionKey, $plugin_options[$tabKey][$sectionKey][$optionKey]);


					}
				}}
			}}
		}}
	}}

return $plugin_options;
}
/*******************************************************************************
*	@since 3.2.5
*	@var - array()
*	@var - string
*	@var - array()
*	echos string;
*
*	@return void
*
*	output manage sections
*******************************************************************************/
function wppizza_phs_validate_settings($input, $plugin_settings, $option_name, $current_user_caps){

		/**
			make sure we simply get the full array on install/update
			or when not validating the plugin itself to start off with
		**/
		if ( empty( $_POST['_wp_http_referer'] ) || empty($_POST['option_page']) || ($option_name != wppizza_validate_alpha_only($_POST['option_page']))) {
			return $input;
		}

		/*
			verify nonce
		*/
		if ( ! isset( $_POST['_nonce_'.$option_name.''] ) || !wp_verify_nonce( $_POST['_nonce_'.$option_name.''], '_nonce_'.$option_name.'') ) {
			die('Invalid Nonce');
		}
		/*
			verify caps for this tab
		*/
		if(!in_array($option_name.'_'.key($_POST[$option_name]), $current_user_caps['caps'])){
			die('You do not have permissions to edit this resource.');
		}

		/*
			get full current options - to override as necessary by tab/key below
		*/
		$update_options = get_option($option_name);


		/*
			get tab key
		*/
		$tab_id = key($_POST[$option_name]);


		/********************************
		*	[validate]
		********************************/
		if(isset($_POST[$option_name])){

			/*
				initialize tab options we are setting
			*/
			$update_options[$tab_id] = array();

			/*
				get all defined options that have NO sub sections set
			*/
			$set_tab_options = !empty($plugin_settings['tabs'][$tab_id]['tab']) ? $plugin_settings['tabs'][$tab_id]['tab'] :  false ;
			/*
				iterate through each non-sectioned options for this tab
			*/

			if(!empty($set_tab_options)){
			foreach($set_tab_options as $tab_key=>$tab_options){

					/* post val */
					$validated = !empty($_POST[$option_name][$tab_id]) ? $_POST[$option_name][$tab_id] : false ;
					/* using callback if exists else use as is  */
					$validated = !empty($tab_options['validation_callback']) ? wppizza_phs_validate_by_callback($validated, $tab_options['validation_callback']) : $validated ;


					/* option update */
					$update_options[$tab_key] = $validated;
			}}

			/*
				get all defined options per *sub* sections of this tab
			*/
			$set_section_options = !empty($plugin_settings['tabs'][$tab_id]['options']) ? $plugin_settings['tabs'][$tab_id]['options'] :  false ;
			/*
				iterate through each section for this tab
			*/
			if(!empty($set_section_options)){
			foreach($set_section_options as $section_key=>$section_options){

				$update_options[$tab_id][$section_key] = array();

				/* iterate through each section options */
				foreach($section_options as $option_key=>$option_values){

					/* also validate suboptions if there are some */
					if(!empty($option_values['options'])){
						foreach($option_values['options'] as $sub_option_key=>$sub_option_values){
							if(!empty($sub_option_values['options'])){
								foreach($sub_option_values['options'] as $sub_sub_option_key=>$sub_sub_option_values){
									/* post val */
									$validated = !empty($_POST[$option_name][$tab_id][$sub_option_key][$sub_sub_option_key]) ? $_POST[$option_name][$tab_id][$sub_option_key][$sub_sub_option_key] : false ;
									/* using callback if exists */
									$validated = wppizza_phs_validate_by_callback($validated, $sub_sub_option_values['validation_callback']);
									/* option update */
									$update_options[$tab_id][$sub_option_key][$sub_sub_option_key] = $validated;
								}
							}
						}
					}

					/* post val */
					$validated = !empty($_POST[$option_name][$tab_id][$section_key][$option_key]) ? $_POST[$option_name][$tab_id][$section_key][$option_key] : false ;
					/* using callback if exists */
					$validated = wppizza_phs_validate_by_callback($validated, $option_values['validation_callback']);
					/* option update */
					$update_options[$tab_id][$section_key][$option_key] = $validated;

				}

			}}
		}

	return $update_options;
}
/*******************************************************************************
*	@since 3.2.5
*	@var - mixed
*	@var - mixed
*
*	@return mixed
*	validate input using specified callback function
*******************************************************************************/
function wppizza_phs_validate_by_callback($input, $callback){
	/* no validation set */
	if(empty($callback)){
		return $input;
	}

	/* use globally available validations, no parameters passed*/
	if(is_string($callback)){
		/* validate by function */
		$validated = $callback($input);
	}

	/* use class validation  and/or callback with argumnts*/
	if(is_array($callback)){
		$class = $callback[0];
		$method = $callback[1];
		$args = !empty($callback[2]) ? $callback[2] : array();

		/* validate */
		if(!empty($class)){
			$validated = $class->$method($input, $args);
		}else{
			$validated = $method($input, $args);
		}
	}

return $validated;
}
/*******************************************************************************
*	@since 3.2.5
*	@var - array()
*	@var - string
*	@var - array()
*	echos string;
*
*	@return void
*
*	output manage sections
*******************************************************************************/
function wppizza_phs_manage_sections($plugin_settings, $option_name, $current_user_caps, $plugin_label){


		/****************************************
			make tabs from settings
		****************************************/
		$tabs = wppizza_phs_get_tabs($plugin_settings, $option_name, $current_user_caps);
		/* for convenience */
		$current = $tabs['current'];


		/****************************************
			output tabs
		****************************************/
		echo $tabs['tabs'];


		/****************************************
			wrapper div
		****************************************/
		echo'<div id="'.$option_name.'" class="'.WPPIZZA_PREFIX.'-wrap '.$option_name.'-wrap '.$option_name.'-'.$tabs['current'].'">';


			/*********************
				help icon / test div
			*********************/
			echo"<div class='".WPPIZZA_PREFIX."-admin-pageheader'>";

				/*
					icon
				*/
				echo"<h2>";
					echo"<a href='javascript:void(0)' class='".WPPIZZA_PREFIX."-dashicons-admin button'><span class='dashicons dashicons-editor-help ".WPPIZZA_PREFIX."-show-admin-help'></span></a>";
					echo"<span id='".WPPIZZA_SLUG."-header'>".WPPIZZA_NAME." ".$plugin_label." - ".$tabs['label']."</span>";
				echo"</h2>";

				/*
					text
				*/
				echo"<span class='".WPPIZZA_PREFIX."-help-hint'>".__('Note: Some options will have more details in the <a href="javascript:void(0)" class="wppizza-show-admin-help">help screen</a>','wppizza-admin')."</span>";

			echo"</div>";

			/********************
				show update info / errors etc
			********************/
			settings_errors();


			/********************
				output form
			********************/
			echo'<form action="options.php" method="post">';
				echo'<input type="hidden" name="'.$option_name.'" value="1" />';

				/*
					adding nonce and referrer
				*/
				wp_nonce_field( '_nonce_'.$option_name.'', '_nonce_'.$option_name.'', false );

				/*
					settings field
				*/
				settings_fields($option_name);


				/*
					settings sections in tab, without subsection
				*/
				if(!empty($plugin_settings['tabs'][$current]['tab'])){
				foreach($plugin_settings['tabs'][$current]['tab'] as $tab_key => $tab_options){
					echo'<div class="wppizza-section wppizza-section-'.$tab_key.'">';
						do_settings_sections($tab_key);
					echo'</div>';
				}}


				/*
					settings sections as subsections of tab
				*/
				if(!empty($plugin_settings['tabs'][$current]['sections'])){
				foreach($plugin_settings['tabs'][$current]['sections'] as $sections_key => $section_label){
					echo'<div class="wppizza-section wppizza-section-'.$sections_key.'">';
						do_settings_sections($sections_key);
					echo'</div>';
				}}

				/*
					echo submit button or disabled button
				*/
				if(WPPIZZA_DEV_ADMIN_NO_SAVE){
					print '<input type="button" class="'.WPPIZZA_PREFIX.'-save-disabled" value="'.__('Saving Disabled', 'wppizza-admin').'">';
				}else{
					submit_button( __('Save Changes', 'wppizza-admin') );
				}

			echo'</form>';
			/********************
				end form
			********************/

		echo'</div>';
		/********************
			end wrapper
		********************/
}

/*******************************************************************************
	@since 3.2.5
	@var - array()
	add contextual help screen to a plugin - admin
*******************************************************************************/
function wppizza_phs_get_tabs($plugin_settings, $option_name, $current_user_caps){

	/*
		get defined / available tabs/pages based on access rights
	*/
	$tabs = wppizza_phs_user_caps($plugin_settings, $option_name);

	/*
		get current tab or set first availabe for user if no tab specifically selected
	*/
	$current = !empty($_GET['tab']) ?  $_GET['tab'] : $current_user_caps['tabs'][0];

	/*
		tabs to output based on caps and user capabilities
	*/
	$str = '';
	$str .= '<h2 class="nav-tab-wrapper">';
	if(!empty($tabs['tabs'])){
	foreach( $tabs['tabs'] as $tab_key => $tab_id ){
    if(in_array($tab_id, $current_user_caps['tabs'])){

    	$class = ( $tab_id == $current ) ? ' nav-tab-active' : '';
        $str .=  "<a class='nav-tab".$class."' href='?post_type=".WPPIZZA_POST_TYPE."&page=".$option_name."&tab=".$tab_id."'>".$tabs['name'][$tab_key]."</a>";

        /*
        	get label for currently selected tab
        */
        if( $tab_id == $current ){
        	$label = $tabs['name'][$tab_key];
        }

    }}}
    $str .=  '</h2>';

	/*
		get array of tabs to echo and array of lallabels
	*/
	$res = array();
	$res['tabs'] = $str;
	$res['current'] = $current;
	$res['label'] = $label;


return $res;
}


/*******************************************************************************
	@since 3.2.5
	@var - array()
	@return void
	add settings sections
*******************************************************************************/
function wppizza_phs_settings_sections($plugin_settings, $option_name, $options_set, $current_user_caps){

	global $current_screen;

	if($current_screen->id == ''.WPPIZZA_POST_TYPE.'_page_'.$option_name.'' && $current_screen->post_type == WPPIZZA_POST_TYPE){


		/*
			get current tab or set first availabe for user if no tab specifically selected
		*/
	    $current = !empty($_GET['tab']) ?  $_GET['tab'] : $current_user_caps['tabs'][0];

		/*
			iterate through globals that have no specific sections set up in a tab
		*/
		if(!empty($plugin_settings['tabs'][$current]['tab'])){
		foreach($plugin_settings['tabs'][$current]['tab'] as $tab_key => $tab_options){


			add_settings_section($tab_key,'' , null, $tab_key);


			/* add some useful parameters to options args */
			$args = $tab_options;
			$args['slug'] = $option_name;
			$args['tab'] = $current;
			$args['options_set'] = $options_set;
			//$args['section'] = $section_key;
			//$args['option'] = $option_key;

			add_settings_field($tab_key,  $plugin_settings['tabs'][$current]['label'] , 'wppizza_phs_section_options', $tab_key, $tab_key, $args);

		}}

		/*
			iterate through sections for this tab
		*/
		if(!empty($plugin_settings['tabs'][$current]['sections'])){
		foreach($plugin_settings['tabs'][$current]['sections'] as $section_key=>$section_label){
			/*
				add section
			*/
			add_settings_section($section_key, $section_label, null, $section_key);

			/*
				iterate through and add section options/fields
			*/
			if(!empty($plugin_settings['tabs'][$current]['options'][$section_key])){
			foreach($plugin_settings['tabs'][$current]['options'][$section_key] as $option_key=>$options){

				/* add some useful parameters to options args */
				$args = $options;
				$args['slug'] = $option_name;
				$args['tab'] = $current;
				$args['section'] = $section_key;
				$args['field'] = $option_key;
				$args['options_set'] = $options_set;
				add_settings_field($option_key, $options['label'], 'wppizza_phs_section_options', $section_key, $section_key, $args);
			}}
		}}
	}
}

/*******************************************************************************
	@since 3.2.5
	@var - array()


	echo output options of plugin in admin
*******************************************************************************/
function wppizza_phs_section_options($args, $echo = true){

	/**input type**/
	$type = !empty($args['type']) ? $args['type'] : 'text';//text as default
	/** id slug **/
	$slug = !empty($args['slug']) ? $args['slug'] : 'noslug' ;
	/** tab key **/
	$tab = !empty($args['tab']) ? $args['tab'] : '' ;
	/** section key **/
	$section = !empty($args['section']) ? $args['section'] : '' ;
	/**options, if any (for checkbox/radios/selects etc)**/
	$options = !empty($args['options']) ? $args['options'] : false ;
	/**options set/saved in plugin**/
	$options_set = !empty($args['options_set']) ? $args['options_set'] : false ;
	/**value key - array key of option set in options table (subkey of options_key)**/
	$field = !empty($args['field']) ? $args['field'] : '' ;
	/**set value dont use !empty here as values might be 0 **/
	$value = isset($args['value']) ? $args['value'] : ( isset($options_set[$tab][$section][$field]) ? $options_set[$tab][$section][$field] : false);
	/**placeholder - if any (text/textarea)**/
	$placeholder = !empty($args['placeholder']) ? $args['placeholder'] : false ;
	/**label (if any)**/
	$info = !empty($args['info']) ? $args['info'] : '' ;
	/**description (if any)**/
	$description = !empty($args['description']) ? '<br/><span class="description">'.implode('</span><br /><span class="description">', $args['description'] ).'</span>' : '' ;


	/*
		ini output - maybe wrap in li unless it's hidden ?
	*/
	$formfield = '';
	//$formfield = ($type!='hidden') ? '<li>' : '' ;

	/*
		text / email options
	*/
	if($type == 'email' || $type == 'text'){

		/* if width is set */
		$px_width = !empty($args['width']) ? esc_html($args['width']) : '500px' ;

		$formfield .= '<label id="'.$slug  .'_'. $tab .'_'. $section .'_'. $field .'">';
		$formfield .= '<input name="'.$slug.'['.$tab.']['.$section.']['.$field.']"  type="text" value="' . stripslashes(esc_html($value)) . '" placeholder="' .stripslashes(esc_html($placeholder)) . '" style="width:'.$px_width.'" />';
		if(!empty($info) || !empty($description) ){
			$formfield .= '' . $info . '' . $description . '';
		}
		$formfield .= '</label>';
	}

	/*
		textarea options
	*/
	if($type=='textarea'){
		$formfield .= '<label id="'.$slug  .'_'. $tab .'_'. $section .'_'. $field .'">';
		$formfield .= '<textarea name="'.$slug.'['.$tab.']['.$section.']['.$field.']" placeholder="' .stripslashes(esc_html($placeholder)) . '" style="width:500px;max-height:175px" >' . stripslashes(esc_textarea($value)) . '</textarea>';
		if(!empty($info) || !empty($description) ){
			$formfield .= '' . $info . '' . $description . '';
		}
		$formfield .= '</label>';
	}

	/*
		checkbox options
	*/
	if($type=='checkbox'){
		$formfield .= '<div id="'.$slug  .'_'. $tab .'_'. $section .'_'. $field .'">';

			/* if mutiple options */
			if(is_array($options)){
				foreach($options as $option_value => $option_array){

					/* inline (no br's ) if set  */
					$add_br = (!empty($args['style']) && $args['style'] == 'inline' ) ? '' : '<br/>' ;

					/* another sub span wrap to be able to distinguish and perhaps style possible suboptions*/
					$formfield .= '<span>';

					/* output checkbox */
					$formfield .= '<label><input name="'.$slug.'['.$tab.']['.$section.']['.$field.']['.$option_value.']"  type="checkbox" value="'.esc_html($option_value).'" '.checked((isset($value[$option_value]) || (!empty($value) && in_array($option_value, $value) ) ), true, false).'/>'.$option_array['label'].''.$add_br.'</label>';

					/* further options - if any */
					if(!empty($option_array['options'])){
						$formfield .= "<ul style='margin-left:20px' id='".$option_value."_options'>";
						foreach($option_array['options'] as $oKey=>$oArgs){

							/* add tab/section/field (i.e option key) if not exist */
							$oArgs['slug'] = empty($oArgs['slug']) ? $slug : $oArgs['slug'];
							$oArgs['tab'] = empty($oArgs['tab']) ? $tab : $oArgs['tab'];
							$oArgs['section'] = empty($oArgs['section']) ? $section : $oArgs['section'];
							$oArgs['field'] = empty($oArgs['field']) ? $oKey : $oArgs['field'];
							//$oArgs['value'] = empty($oArgs['value']) ? '-- to set using options set --': $oArgs['value'];
							$oArgs['options_set'] = empty($oArgs['options_set']) ? $options_set : $oArgs['options_set'];

							/* use function itself returning output without echoing */
							$sub_option = wppizza_phs_section_options($oArgs, false);
							$formfield .= "<li>".$sub_option."</li>";//$this->set_formfields($oVal, $options_key, $settings_key, $oKey);

						}
						$formfield .= "</ul>";
					}

					/* sub div wrap end */
					$formfield .= '</span>';



				}
			}else{
				$formfield .= '<label><input name="'.$slug.'['.$tab.']['.$section.']['.$field.']"  type="checkbox" value="1" '.checked($value, true, false).'/></label>';
			}
			if(!empty($info) || !empty($description) ){
				$formfield .= '<span style="margin-left:10px">';
				$formfield .= '' . $info . '' . $description . '';
				$formfield .= '</span>';
			}
		$formfield .= '</div>';
	}

	/*
		radio options
	*/
	if($type=='radio'){
		$formfield .= '<div id="'.$slug  .'_'. $tab .'_'. $section .'_'. $field .'">';

			/* if mutiple options */
			if(is_array($options)){
				foreach($options as $option_value => $option_array){


					/* inline (no br's ) if set  */
					$add_br = (!empty($args['style']) && $args['style'] == 'inline' ) ? '' : '<br/>' ;


					/* another sub span wrap to be able to distinguish and perhaps style possible suboptions*/
					$formfield .= '<span>';

					/* output radios */
					$formfield .= '<label><input class="'.$slug.'_'.$tab.'_'.$section.'_'.$field.'" name="'.$slug.'['.$tab.']['.$section.']['.$field.']"  type="radio" value="'.esc_html($option_value).'" '.checked($value, $option_value, false).'/>'.$option_array['label'].''.$add_br.'</label>';

					/* further options - if any */
					if(!empty($option_array['options'])){
						$formfield .= "<ul style='margin-left:20px' id='".$option_value."_options'>";
						foreach($option_array['options'] as $oKey=>$oArgs){

							/* add tab/section/field (i.e option key) if not exist */
							$oArgs['slug'] = empty($oArgs['slug']) ? $slug : $oArgs['slug'];
							$oArgs['tab'] = empty($oArgs['tab']) ? $tab : $oArgs['tab'];
							$oArgs['section'] = empty($oArgs['section']) ? $section : $oArgs['section'];
							$oArgs['field'] = empty($oArgs['field']) ? $oKey : $oArgs['field'];
							//$oArgs['value'] = empty($oArgs['value']) ? '-- to set using options set --': $oArgs['value'];
							$oArgs['options_set'] = empty($oArgs['options_set']) ? $options_set : $oArgs['options_set'];

							/* use function itself returning output without echoing */
							$sub_option = wppizza_phs_section_options($oArgs, false);
							$formfield .= "<li>".$sub_option."</li>";//$this->set_formfields($oVal, $options_key, $settings_key, $oKey);

						}
						$formfield .= "</ul>";
					}

					/* sub div wrap end */
					$formfield .= '</span>';


				}
			}else{
				$formfield .= '<label><input name="'.$slug.'['.$tab.']['.$section.']['.$field.']"  type="radio" value="1" '.checked($value, true, false).'/></label>';
			}
			if(!empty($info) || !empty($description) ){
				$formfield .= '<span style="margin-left:10px">';
				$formfield .= '' . $info . '' . $description . '';
				$formfield .= '</span>';
			}
		$formfield .= '</div>';
	}




	/* select dropdowns options */
	if($type=='select' || $type=='multiple_select'){
		$formfield .= '<div id="'.$slug  .'_'. $tab .'_'. $section .'_'. $field .'">';

			/* is multi select ? */
			$multiple = ($type=='multiple_select') ? 'multiple' : '' ;
			$multistyle = ($type=='multiple_select') ? 'style="min-width:500px;height:200px"' : '' ;




			$formfield .= '<select name="'.$slug.'['.$tab.']['.$section.']['.$field.']" '.$multiple.' '.$multistyle.' >';
				foreach($options as $option_value => $option_label){
					$formfield .= '<option value="'. $option_value .'" '.selected($value, $option_value, false).'>'. esc_html($option_label) .'</option>';
				}
			$formfield .= '</select>';
				if(!empty($info) || !empty($description) ){
					$formfield .= '<span style="margin-left:10px">';
					$formfield .= '' . $info . '' . $description . '';
					$formfield .= '</span>';
				}

		$formfield .= '</div>';
	}


	/*
		just adding a hidden field
	*/
	if($type=='hidden'){
		$formfield .= '<input id="'.$slug  .'_'. $tab .'_'. $section .'_'. $field .'" name="'.$slug.'['.$tab.']['.$section.']['.$field.']"  type="hidden" value="' . stripslashes(esc_html($value)) . '" />';
	}


	/*
		custom , simply output whats set in options plud nfo
	*/
	if($type=='custom'){
		$formfield .= $options;
		if(!empty($info) || !empty($description) ){
			$formfield .= '<span style="margin-left:10px">';
			$formfield .= '' . $info . '' . $description . '';
			$formfield .= '</span>';
		}
	}


	/*
		end li wrapper - unless it's hidden
	*/
	//$formfield .= ($type!='hidden') ? '</li>' : '' ;

/* echo the shebang or return */
	if($echo){
		echo $formfield;
	}else{
		return $formfield;
	}
}



/*******************************************************************************
	@since 3.2.5
	@var - array()


	echo output options of plugin in admin
*******************************************************************************/
function wppizza_phi_section_options($plugin_slug, $plugin_options, $args, $echo = true){
	//global $current_screen;

	if(!empty($args['plugin']) && $args['plugin'] == $plugin_slug){

		/*
			global
		*/
		/* main plugin slug i.e option name in db */
		$_PLUGIN = $plugin_slug;
		/** slug of admin page we are on - this really should be set !**/
		$_PAGE = !empty($args['settings_page']) ? $args['settings_page'] : '' ;
		/** check if suboption of another option **/
		$_IS_SUBOPTION = !empty($args['is_suboption']) ? true : false ;

		/*
			output type and info
		*/
		/**input type**/
		$type = !empty($args['type']) ? $args['type'] : 'text';//text as default
		/**sub option key, if any (for checkbox/radios/selects etc)**/
		$option_key = !empty($args['option_key']) ? $args['option_key'] : false ;
		/**value key - array key of option set in options table **/
		$field = !empty($args['value_key']) ? $args['value_key'] : '' ;
		/**saved value in options table **/
		if(!$_IS_SUBOPTION){
			$value = isset($plugin_options[$_PAGE][$field]) ? $plugin_options[$_PAGE][$field] : false ;
		}else{
			$value = isset($plugin_options[$_PAGE][$option_key][$field]) ? $plugin_options[$_PAGE][$option_key][$field] : false ;
		}
		/**sub options, if any (for checkbox/radios/selects etc)**/
		$options = !empty($args['options']) ? $args['options'] : false ;
		/**label (if any)**/
		$info = !empty($args['info']) ? $args['info'] : false ;
		/**description (if any)**/
		$description = !empty($args['description']) ? '<br/><span class="description">'.implode('</span><br /><span class="description">', $args['description'] ).'</span>' : false ;
		/**placeholder - if any (text/textarea)**/
		$placeholder = !empty($args['placeholder']) ? $args['placeholder'] : false ;

		/*
			ini output
		*/
		$formfield = '';

		/*******************************************************************
		#
		#	text / email
		#
		*******************************************************************/
		if($type == 'email' || $type == 'text'){

			/* if 'width' arg is set */
			$px_width = !empty($args['width']) ? esc_html($args['width']) : '300px' ;

			/* no suboption defined for this textfield */
			if(empty($_IS_SUBOPTION)){
				$formfield .= '<label id="'.$_PLUGIN.'_'.$_PAGE.'_'. $field .'">';
				$formfield .= '<input name="'.$_PLUGIN.'['.$_PAGE.']['.$field.']"  type="text" value="' . stripslashes(esc_html($value)) . '" placeholder="' .stripslashes(esc_html($placeholder)) . '" style="width:'.$px_width.'" />';
			}else{
				$formfield .= '<label id="'.$_PLUGIN.'_'.$_PAGE.'_'. $option_key .'_'. $field .'">';
				$formfield .= '<input name="'.$_PLUGIN.'['.$_PAGE.']['.$option_key.']['.$field.']"  type="text" value="' . stripslashes(esc_html($value)) . '" placeholder="' .stripslashes(esc_html($placeholder)) . '" style="width:'.$px_width.'" />';
			}

			/* info/descriptions */
			if(!empty($info) || !empty($description) ){
				$formfield .= '' . $info . '' . $description . '';
			}
			$formfield .= '</label>';
		}


		/*******************************************************************
		#
		#	textarea
		#
		*******************************************************************/
		if($type=='textarea'){


			/* if 'width' arg is set */
			$px_width = !empty($args['width']) ? esc_html($args['width']) : '300px' ;

			/* no suboption defined for this textfield */
			if(empty($_IS_SUBOPTION)){
				$formfield .= '<label id="'.$_PLUGIN.'_'.$_PAGE.'_'. $field .'">';
				$formfield .= '<textarea name="'.$_PLUGIN.'['.$_PAGE.']['.$field.']" placeholder="' .stripslashes(esc_html($placeholder)) . '" style="width:'.$px_width.';max-height:175px" >' . stripslashes(esc_textarea($value)) . '</textarea>';
			}else{
				$formfield .= '<label id="'.$_PLUGIN.'_'.$_PAGE.'_'. $option .'_'. $field .'">';
				$formfield .= '<textarea name="'.$_PLUGIN.'['.$_PAGE.']['.$option_key.']['.$field.']" placeholder="' .stripslashes(esc_html($placeholder)) . '" style="width:'.$px_width.';max-height:175px" >' . stripslashes(esc_textarea($value)) . '</textarea>';
			}
			/* info/descriptions */
			if(!empty($info) || !empty($description) ){
				$formfield .= '' . $info . '' . $description . '';
			}
			$formfield .= '</label>';

		}


		/*******************************************************************
		#
		#	select / dropdowns
		#
		*******************************************************************/
		if($type=='select' || $type=='multiple_select'){
			$formfield .= '<div id="'.$_PLUGIN.'_'. $_PAGE .'_'. $field .'">';

				/* is multi select ? */
				$multiple = ($type=='multiple_select') ? 'multiple' : '' ;
				$multistyle = ($type=='multiple_select') ? 'style="min-width:500px;height:200px"' : '' ;

				/*
					simple dropdown
				*/
				if(empty($multiple)){
					if(empty($_IS_SUBOPTION)){
						$formfield .= '<select name="'.$_PLUGIN.'['.$_PAGE.']['.$field.']" >';
					}else{
						$formfield .= '<select name="'.$_PLUGIN.'['.$_PAGE.']['.$option_key.']['.$field.']" >';
					}
				/*
					multi select
				*/
				}else{
					$formfield .= '<select name="'.$_PLUGIN.'['.$_PAGE.']['.$field.'][]" '.$multiple.' '.$multistyle.' >';
				}

				/* options */
				foreach($options as $option_value => $option_label){
					$formfield .= '<option value="'. $option_value .'" '.selected(($value == $option_value), true, false).' >'. esc_html($option_label) .'</option>';
				}

				$formfield .= '</select>';

				/* info/descriptions */
				if(!empty($info) || !empty($description) ){
					$formfield .= '<span style="margin-left:10px">';
					$formfield .= '' . $info . '' . $description . '';
					$formfield .= '</span>';
				}

			$formfield .= '</div>';
		}



		/*******************************************************************
		#
		#	checkbox / radio (checkbox_sortable to add icon)
		#
		*******************************************************************/
		if($type=='checkbox' || $type=='checkbox_sortable' || $type=='radio'  || $type=='radio_sortable' ){

			/*
				reset type to checkbox / radio
				if sortable and add flab
			*/
			if($type=='checkbox_sortable'){
				$type='checkbox';
				$sortable = true;
			}

			if($type=='radio_sortable'){
				$type='radio';
				$sortable = true;
			}


			$formfield .= '<div id="'.$_PLUGIN.'_'. $_PAGE .'_'. $field .'">';

				/* if mutiple options */
				if(is_array($options)){
					foreach($options as $option_value => $option_array){

						/* inline (no br's ) if set  */
						$add_br = (!empty($args['style']) && $args['style'] == 'inline' ) ? '' : '<br/>' ;


						/* another sub span wrap to be able to distinguish and perhaps style possible suboptions*/
						$formfield .= '<span>';

						/* add sortable handle  and hidden input*/
						if(!empty($sortable)){
							$formfield .= '<span class="wppizza-dashicons-small dashicons-sort" title="'.__('Drag/Drop to Sort', 'wppizza-admin').'"><input name="'.$_PLUGIN.'['.$_PAGE.']['.$field.'_sortable]['.esc_html($option_value).']"  type="hidden" value="1" /></span>';
						}


						/*
							output checkbox or radio
						*/
						$formfield .= '<label><input name="'.$_PLUGIN.'['.$_PAGE.']['.$field.']['.esc_html($option_value).']"  type="'.$type.'" value="1" '.checked( !empty($value[$option_value]) , true, false).'/>'.$option_array['label'].''.$add_br.'</label>';

						/* further options - if any */
						if(!empty($option_array['options'])){
							$formfield .= "<ul style='margin-left:20px' id='".$option_value."_options'>";
							foreach($option_array['options'] as $oKey=>$oArgs){


								$sArgs = array();
								/** suboption flag **/
								$sArgs['is_suboption'] = true;
								/** plugin slug **/
								$sArgs['plugin'] = $_PLUGIN;
								/**settings page **/
								$sArgs['settings_page'] = $_PAGE;//!empty($oArgs['settings_page']) ? $oArgs['settings_page'] : '';
								/**input type**/
								$sArgs['type'] = !empty($oArgs['type']) ? $oArgs['type'] : 'text';//text as default
								/**sub options, if any (for checkbox/radios/selects etc)**/
								$sArgs['option_key'] = !empty($oArgs['option']) ? $oArgs['option'] : false ;
								/**value key - array key of option set in options table **/
								$sArgs['value_key'] = !empty($oKey) ? $oKey : '' ;
								/**sub options, if any (for checkbox/radios/selects etc)**/
								$sArgs['options'] = !empty($oArgs['options']) ? $oArgs['options'] : false ;
								/**label (if any)**/
								$sArgs['info'] = !empty($oArgs['info']) ? $oArgs['info'] : false ;
								/**description (if any)**/
								$sArgs['description'] = !empty($oArgs['description']) ? $oArgs['description'] : false ;
								/**placeholder - if any (text/textarea)**/
								$sArgs['placeholder'] = !empty($oArgs['placeholder']) ? $oArgs['placeholder'] : false ;
								/**width - if any (text/textarea)**/
								$sArgs['width'] = !empty($oArgs['width']) ? esc_html($oArgs['width']) : false ;

								/*
									use function itself but returning output without echoing
								*/
								$sub_option = wppizza_phi_section_options($_PLUGIN, $plugin_options, $sArgs, false);


								$formfield .= "<li>".$sub_option."</li>";//".$sub_option."

							}
							$formfield .= "</ul>";
						}

						/* sub div wrap end */
						$formfield .= '</span>';
					}
				}else{
					$formfield .= '<label><input name="'.$_PLUGIN.'['.$_PAGE.']['.$field.']"  type="'.$type.'" value="1" '.checked($value, true, false).' /></label>';
				}
				if(!empty($info) || !empty($description) ){
					$formfield .= '<span style="margin-left:10px">';
					$formfield .= '' . $info . '' . $description . '';
					$formfield .= '</span>';
				}
			$formfield .= '</div>';
		}



		/*******************************************************************
		#
		#	adding a hidden field
		#
		*******************************************************************/
		if($type=='hidden'){
			$formfield .= '<input id="'.$_PLUGIN  .'_'. $_PAGE .'_'. $field .'" name="'.$_PLUGIN.'['.$_PAGE.']['.$field.']"  type="hidden" value="' . stripslashes(esc_html($value)) . '" />';
		}

		/*******************************************************************
		#
		#	custom , simply output whats set in options
		#
		*******************************************************************/
		if($type=='custom'){
			$formfield .= $options;
			if(!empty($info) || !empty($description) ){
				$formfield .= '<span style="margin-left:10px">';
				$formfield .= '' . $info . '' . $description . '';
				$formfield .= '</span>';
			}
		}


		/*
			echo the shebang
			(or return - when it's a suboption)
		*/
		if($echo){
			echo $formfield;
		}else{
			return $formfield;
		}
	}
}
/*******************************************************************************
	@since 3.2.5
	@var - array()
	add contextual help screen to a plugin - admin
*******************************************************************************/
function wppizza_phs_contextual_help($plugin_settings, $option_name, $current_user_caps){
		/*
			get current screen info
		*/
		$screen = get_current_screen();

		/*
			get help tabs / info array and simplyfiy for sanity
		*/
		$simplify_help = array();
		if(!empty($plugin_settings['tabs'])){
		foreach($plugin_settings['tabs'] as $tab_key => $tab_settings ){
			if(!empty($tab_settings['help'])){
			foreach($tab_settings['help'] as $help_section => $help_info ){
				$simplify_help[$tab_key][$help_section] = $help_info ;
			}}
		}}

		/* output contextual help */
		if(!empty($simplify_help)){
		foreach($simplify_help as $tab_key => $tab_help){

			/*
				set main tab left
			*/
			$tab_title = $plugin_settings['tabs'][$tab_key]['label'];//$plugin_settings[$tab_key]['name'];

			/*
				initialize help content for this tab
			*/
			$help_content='';
			if(!empty($tab_help)){
			foreach($tab_help as $section_key => $section_help){
				if(!empty($section_help)){
				foreach($section_help as $help_key => $help_info){
					/*add label*/
					if(!empty($help_info['label'])){
						$help_content.='<h3>'.$help_info['label'].'</h3>';
					}

					/*add description*/
					if(!empty($help_info['description']) && is_array($help_info['description'])){
					foreach($help_info['description'] as $description){
						$help_content.='<div>'.$description.'</div>';
					}}
				}}
			}}

			/**add help tabs with content**/
			$screen -> add_help_tab( array('id' => $option_name.'_'.$tab_key.'', 'title' => $tab_title, 'content' => '<div class="wppizza_admin_context_help">'.$help_content.'</div>'));
		}}
}
/*******************************************************************************
*	@since 3.2.6
*	@var - array()
*	@var - string
*
*	@return void;
*
*	output admin nag notices
*******************************************************************************/
function wppizza_phs_nag_notice($notices, $option_name){

	if(!empty($notices) && is_array($notices)){

		$the_notice = '';

		foreach($notices as $key => $notice){
			$the_notice .= $notice;
			$the_notice .= '<br /><br />';
		}
		print'<div id="'.$option_name.'_admin_notice" class="notice notice-success '.WPPIZZA_PREFIX.'_admin_notice" style="padding:20px;"><br/>'.$the_notice.'<br/><a href="javascript:void(0);" onclick="'.$option_name.'_dismiss_notice(\''.$option_name.'\'); return false;" class="button-primary">'.__('dismiss','wppizza-admin').'</a></div>';
	}
}
/*******************************************************************************
*	@since 3.2.6
*	@var - string
*
*	@return void;
*
*	output admin nag notices js for dismissal of notice
*******************************************************************************/
function wppizza_phs_nag_notice_js($option_name){
	$js="";
	$js.="<script type='text/javascript'>".PHP_EOL."";
	$js.="/* <![CDATA[ */".PHP_EOL."";
	$js.="jQuery(document).ready(function($) {".PHP_EOL."";
		$js.="".$option_name."_dismiss_notice = function (e) {".PHP_EOL."";
			$js.="var data = {action: '".$option_name."', vars: {'dismiss-admin-notice': 1}};".PHP_EOL."";
			$js.="jQuery.post(ajaxurl, data, function(response) {".PHP_EOL."";
					$js.="$('#".$option_name."_admin_notice').hide('slow');".PHP_EOL."";
			$js.="});".PHP_EOL."";
		$js.="};".PHP_EOL."";
	$js.="});".PHP_EOL."";
	$js.="/* ]]> */".PHP_EOL."";
	$js.="</script>".PHP_EOL."";
print"".$js;
}
/*******************************************************************************
*	@since 3.2.6
*	@var - string
*
*	@return void;
*
*	set ['plugin_data']['nag_notice'] to be zero to not be shown anymore
*******************************************************************************/
function wppizza_phs_nag_notice_dismiss($option_name){

	if(!empty($_POST['vars']['dismiss-admin-notice'])){
		/* get all current */
		$update_options = get_option($option_name);
		/* overwrite nagnotice to be false now */
		$update_options['plugin_data']['nag_notice'] = 0;
		/* update options */
		update_option($option_name, $update_options);
	die();
	}
exit();
}
?>