<?php
/********************************************************************************************

	Class to allow to deal more easily with user capabilties .
	Currently only used in dependend extensions, but will (should) - one day - also
	really be utilized in this main WPPizza plugin (that's why it's here...:)


********************************************************************************************/
class WPPIZZA_USER_CAPS{
	function __construct() {
		/* remove own and higher level roles from being editable */
		add_filter('editable_roles', array($this, 'user_caps_roles_remove_higher_levels'));

		/* callable filter to RETURN admin caps output string */
		add_filter('wppizza_admin_caps', array($this, 'user_echo_admin_caps'), 10, 5 );
	}

	/******************************************************
	*
	*	[define caps filterable, set per settings page]
	*
	******************************************************/
	function define_user_caps($caps=array()) {
		$caps=apply_filters('wppizza_filter_define_caps', $caps);
	return $caps;
	}

	/******************************************************
	*
	*	[runs on plugin uninstall]
	*
	******************************************************/
	function uninstall_user_caps() {
		global $wp_roles;

		/**get filtered caps**/
		$caps = $this->define_user_caps();
		/** array of caps **/
		$remove_caps = array();
		foreach($caps as $cap){
			$remove_caps[$cap['cap']]	= $cap['cap'];
			if(!empty($cap['multi_caps'])){
				foreach($cap['multi_caps'] as $multicap){
					$remove_caps[$multicap]	= $multicap;
				}
			}

		}

		/**remove caps ***/
		foreach($remove_caps as $rCap){
			foreach($wp_roles->roles as $rName=>$rVal){
				$userRole = get_role($rName);
				if($userRole->has_cap( ''.$rCap.'' )){
					$userRole->remove_cap( $rCap );
				}
			}
		}

	return;
	}

	/****************************************************************************************
	*
	*	[update user caps on access rights save]
	*
	*****************************************************************************************/
	function user_caps_update($set_caps, $plugin_options = false, $user_capabilities = false, $option_save_key = 'admin_access_caps' ){
		global $wppizza_options;
		/* get caps as saved in options table */
		$caps_options = empty($plugin_options) ? $wppizza_options[$option_save_key] : $plugin_options[$option_save_key] ;
		/* capabilities as defined  for each sub page */
		$user_capabilities = empty($user_capabilities) ? $this -> define_user_caps() : $user_capabilities;
		/**
			@since 3.2
			only allow certain capabilities for some top level roles (superadmin and admin being non-standard roles but can be assumed that these should have admin privileges if created)
			allow filtering to - perhaps - add roles created by other plugins too that should have these caps
		**/
		$top_level_roles = apply_filters('wppizza_filter_top_level_roles_caps', array('superadmin' => true, 'admin' => true, 'administrator' => true,  'editor' => true));
		$top_level_only_caps = array('edit_others_'.WPPIZZA_SLUG.'s', 'delete_others_'.WPPIZZA_SLUG.'s');



			/**add or remove capabilities according to $set_caps submitted**/
			foreach($set_caps as $roleName=>$v){
				$userRole = get_role($roleName);
				$userCapabilities = $userRole -> capabilities;

				foreach($user_capabilities as $cap_key=>$cap_val){

					/**
						not checked, but previously selected->remove capability
					**/
					if(isset($userCapabilities[$cap_val['cap']]) && ( !is_array($set_caps[$roleName]) || !isset($set_caps[$roleName][$cap_val['cap']]))){
						/* remove */
						$userRole->remove_cap( ''.$cap_val['cap'].'' );
						unset($caps_options[$roleName][$cap_val['cap']]);

						/* using multicaps remove those too */
						if(!empty($cap_val['multi_caps'])){

							foreach($cap_val['multi_caps'] as $mCap){
								$userRole->remove_cap( ''.$mCap.'' );
								unset($caps_options[$roleName][$mCap]);
							}

							/**
								@since 3.2
								higher level roles might have these caps *ADDED* so lets remove them
							**/
							if(!empty($top_level_roles) && isset($top_level_roles[$roleName])){
								foreach($top_level_only_caps as $tloCaps){
									$userRole->remove_cap( ''.$tloCaps.'' );
									unset($caps_options[$roleName][$tloCaps]);
								}
							}

						}

					}
					/**
						checked and NOT previously selected->add capability
					**/
					if(is_array($set_caps[$roleName]) && isset($set_caps[$roleName][$cap_val['cap']]) && !isset($userCapabilities[$cap_val['cap']])){
						/* add */
						$userRole->add_cap( ''.$cap_val['cap'].'' );
						$caps_options[$roleName][$cap_val['cap']] = $cap_val['cap'];

						/*
							using multicaps add those too.

							note to self: maybe one day  enable "edit_others_posts"-> 'edit_others_wppizzas' for admins and editors ONLY ...though at some point
							a dedicated user role plugin will be more appropriate really
						*/
						if(!empty($cap_val['multi_caps'])){


							foreach($cap_val['multi_caps'] as $mCap){
								$userRole->add_cap( ''.$mCap.'' );
								$caps_options[$roleName][$mCap] = $mCap;
							}

							/**
								@since 3.2
								higher level roles should have these caps *ADDED* to
								enable editing etc of *other* menu items
							**/
							if(!empty($top_level_roles) && isset($top_level_roles[$roleName])){
								foreach($top_level_only_caps as $tloCaps){
									$userRole->add_cap( ''.$tloCaps.'' );
									$caps_options[$roleName][$tloCaps];
								}
							}

						}
					}
				}

				/**
					@since 3.2
					$top_level_roles array() of roles
					$top_level_only_caps array() of caps

					force removal of certain higher role caps for lower roles (just in case)
					check for is_empty in case we want to bypass this by simply returning an empty array
					instead of adding some caps when using wppizza_filter_top_level_roles_caps filter
				**/
				if(!empty($top_level_roles) && !isset($top_level_roles[$roleName])){
					foreach($top_level_only_caps as $tloCaps){
						$userRole->remove_cap( ''.$tloCaps.'' );
						unset($caps_options[$roleName][$tloCaps]);
					}
				}
			}

		/* return caps with key */
		$update_options = array();
		$update_options[$option_save_key] = $caps_options;

	return $update_options;
	}
	/****************************************************************************************
	*
	*	[add caps on install or - on update - remove old / add new caps]
	*	$caps=array();
	*	$caps['timed_items']=array('name'=>__('Item name','locale'),'cap'=>'my_unique_cap');
	*	$options =$this->myPluginOptions['my_option_key']
	*
	*****************************************************************************************/
	function user_caps_ini($install = false, $set_caps = false, $option_save_key = 'admin_access_caps', $plugin_options = false){
		global $wp_roles, $user_level, $wppizza_options;

		/**get filtered caps or use array of set caps**/
		$caps = empty($set_caps) ? $this->define_user_caps() : $set_caps ;

		/*get all roles that have manage_options capabilities**/
		$defaultAdmins=array();
		foreach($wp_roles->roles as $rName=>$rVal){
			if(isset($rVal['capabilities']['manage_options'])){
				$defaultAdmins[$rName]=$rName;
			}
		}
		/**
			first install, options are empty, also used when resetting caps
		**/
		if($install){
			/**foreach of these, add all capabilities**/
			$setCapsOptions=array();
			foreach($defaultAdmins as $k=>$roleName){
				$userRole = get_role($roleName);

				if(is_array($caps)){
				foreach($caps as $akey=>$aVal){

					$setCapsOptions[$k][$aVal['cap']]=$aVal['cap'];
					/**no point in adding it twice**/
					if(!$userRole->has_cap( ''.$aVal['cap'].'' )){
						$userRole->add_cap( ''.$aVal['cap'].'' );
					}

					/* add multicaps too , if defined */
					if(!empty($aVal['multi_caps'])){
						foreach($aVal['multi_caps'] as $multiCap){
							$setCapsOptions[$k][$multiCap]=$multiCap;
							/**no point in adding it twice**/
							if(!$userRole->has_cap( ''.$multiCap.'' )){
								$userRole->add_cap( ''.$multiCap.'' );
							}
						}
					}
				}}
			}
		}else{
			/**
				not first install (i.e plugin update), get currently set, compare
				current set caps. hardcoding 'admin_access_caps' for main WPPIZZA plugin to not screw up capabilities
				if plugin that uses helper function does not access rights properly defined
			**/
			$setCapsOptions = !empty($plugin_options[$option_save_key]) ? $plugin_options[$option_save_key] : $wppizza_options['admin_access_caps'];

			/** required caps  - there *might* be more or fewer when updating */
			$requiredCaps = array();
			foreach($caps as $roleCaps){
				$requiredCaps[$roleCaps['cap']]=$roleCaps['cap'];
				if(!empty($roleCaps['multi_caps'])){
					foreach($roleCaps['multi_caps'] as $mCaps){
						$requiredCaps[$mCaps]=$mCaps;
					}
				}
			}

			/**all currently assigned wppizza caps to any role ***/
			$previousCaps=array();
			foreach($setCapsOptions as $roleName=>$roleCaps){
				$flipCaps[$roleName]=array_flip($roleCaps);
				foreach($roleCaps as $prevCaps){
					$previousCaps[$prevCaps]=$prevCaps;
				}
			}

			/**get newly added caps and add to default admins***/
			$addedCaps=array_diff($requiredCaps,$previousCaps);
			foreach($defaultAdmins as $k=>$roleName){
				$userRole = get_role($roleName);
				foreach($addedCaps as  $aCap){
					/**no point in adding it twice**/
					if(!$userRole->has_cap( ''.$aCap.'' )){
						$setCapsOptions[$roleName][$aCap]=$aCap;
						$userRole->add_cap( ''.$aCap.'' );
					}
				}
			}


			/**remove caps not in use anymore***/
			$removedCaps=array_diff($previousCaps,$requiredCaps);
			foreach($removedCaps as $rCap){
				foreach($wp_roles->roles as $rName=>$rVal){
					$userRole = get_role($rName);
					if($userRole->has_cap( ''.$rCap.'' )){
						unset($setCapsOptions[$rName][$flipCaps[$rName][$rCap]]);/*remove from array*/
						$userRole->remove_cap( $rCap );
					}
				}
			}
		}

		/**
			return as array with option key to save in main options table to
			to compare on plugin update if access caps have changed (added/deleted)
		**/
		$caps = array();
		$caps[$option_save_key] = $setCapsOptions;

	return $caps;
	}
	/****************************************************************************************
	*
	*	[get caps of current user - was used by third party plugins ? - to avoid clashes, changed
	*	from current_user_caps to get_current_user_caps]
	*	should use filter 'wppizza_filter_define_caps' now
	*****************************************************************************************/
	function get_current_user_caps($set_caps = false){
		global $current_user;
		$usercaps=array();
		$capUnique=array();/*dont need to have the same thing multiple times*/
		/*user can have more than one role**/
		foreach($current_user->roles as $roleName){
			$userRole = get_role($roleName);

			/**get filtered caps or use array of set caps instead**/
			$caps = empty($set_caps) ? $this->define_user_caps() : $set_caps ;
			/* get caps for this user */
			foreach($caps as $tab=>$v){
				if(isset($userRole->capabilities[$v['cap']]) && !isset($capUnique[$v['cap']])){
					//$usercaps[]=array('tab'=>$tab,'cap'=>$v['cap'],'name'=>$v['name']);
					$usercaps['caps'][]=$v['cap'];
					$usercaps['tabs'][]=$tab;
					$usercaps['name'][]=$v['name'];
					$capUnique[$v['cap']]=1;
				}
			}
		}
		return $usercaps;
	}
	/****************************************************************************************
	*
	*	[return ul of roles and relevant caps]
	*
	*****************************************************************************************/
	function user_echo_admin_caps($field, $plugin_slug, $user_capabilities = false, $section_key = 'access', $echo = true){

			global $current_user;
			/*only get roles user is allowed to edit**/
			$roles=get_editable_roles();

			/*do not display current users role (otherwise he can screw his own access) or levels higher than current*/
			if(is_array($current_user->roles)){
			foreach($current_user->roles as $curRoles){
					if(isset($roles[$curRoles])){
						unset($roles[$curRoles]);
					}
			}}
			/* get defined capabilities  */
			$user_capabilities = empty($user_capabilities) ? WPPIZZA() -> user_caps -> define_user_caps() : $user_capabilities;

			/*
				echo roles, subpages and page capabilities
			*/
			$str = '';

			$str .= "<div id='wppizza_".$field."_options' class='wppizza_admin_options'>";
				foreach($roles as $roleName=>$v){

					$userRole = get_role($roleName);

					$str .= "<div class='wppizza_option wppizza_".$field."_option'>";

						/* role name */
						$str .= "<input type='hidden' name='".$plugin_slug."[".$section_key."][".$roleName."]' value='".$roleName."'>";
						/* role name label */
						$str .= "<span class='wppizza_label_100'>".translate_user_role($v['name']).":</span>";



						foreach($user_capabilities as $access_key=>$access_array){
							$str .= "<span><label><input name='".$plugin_slug."[".$section_key."][".$roleName."][".$access_array['cap']."]' type='checkbox' ". checked(!empty($userRole->capabilities[$access_array['cap']]),true,false)." value='".$access_array['cap']."' />".$access_array['name']."</label></span>";
						}

					$str .= "</div>";
				}
			$str .= "</div>";

		/* echo (default) */
		if($echo){
			echo $str;
		return;
		}

	return $str;
	}
//	/****************************************************************************************
//	*
//	*	[validate and set / revoke caps as required]
//	*
//	*****************************************************************************************/
//	function user_validate_admin_caps($caps, $capsCurrent, $capsPost){
//		$newCaps=$capsCurrent;
//
//		foreach($capsPost as $roleName=>$v){
//			$userRole = get_role($roleName);
//			$capsFlip[$roleName]=!empty($capsCurrent[$roleName]) ? $capsCurrent[$roleName] : array();
//
//			foreach($caps as $akey=>$aVal){
//				/**not checked, but previously selected->remove capability**/
//				if(isset($userRole->capabilities[$aVal['cap']]) && ( !is_array($capsPost[$roleName]) || !isset($capsPost[$roleName][$aVal['cap']]))){
//					unset($newCaps[$roleName][$capsFlip[$roleName][$aVal['cap']]]);/*remove from array*/
//					$userRole->remove_cap( ''.$aVal['cap'].'' );
//				}
//				/**checked and NOT previously selected->add capability*/
//				if(is_array($capsPost[$roleName]) && isset($capsPost[$roleName][$aVal['cap']]) && !isset($userRole->capabilities[$aVal['cap']])){
//					$newCaps[$roleName][]=$aVal['cap'];/*add to array*/
//					$userRole->add_cap( ''.$aVal['cap'].'' );
//				}
//			}
//		}
//		return $newCaps;
//	}
	/****************************************************************************************
	*
	*	[filter editable roles]
	*
	*****************************************************************************************/
	function user_caps_roles_remove_higher_levels($all_roles) {
    	$user = wp_get_current_user();
		/*
			wp_get_current_user is pluggable,
			so in case some other plugin/coding messes things up somewhere,
			ensure we set a level_1 as default if $user->user_level dies not exist
		*/
		$next_level = empty($user->user_level) ? 'level_1' : 'level_' . ($user->user_level + 1);
    	foreach ( $all_roles as $name => $role ) {
	        if (isset($role['capabilities'][$next_level])) {
            	unset($all_roles[$name]);
        	}
    	}
	    return $all_roles;
	}
}
?>