<?php
/**
* WPPIZZA_LOCALIZATION Class
*
* @package     WPPIZZA
* @subpackage  Classes/Admin/Localization
* @copyright   Copyright (c) 2015, Oliver Bach
* @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
* @since       3.0
*
*/
if ( ! defined( 'ABSPATH' ) ) exit;/*Exit if accessed directly*/


/************************************************************************************************************************
*
*
*	WPPIZZA_LOCALIZATION
*
*
************************************************************************************************************************/
class WPPIZZA_LOCALIZATION{
	/*
	* class ident
	* @var str
	* @since 3.0
	*/
	private $class_key='localization';/*to help consistency throughout class in various places*/

	/*
	* titles/lables
	* @var str
	* @since 3.0
	*/
	private $submenu_page_header;
	private $submenu_page_title;
	private $submenu_caps_title;
	private $submenu_link_label;
	private $submenu_priority = 80;
	/******************************************************************************************************************
	*
	*	[CONSTRUCTOR]
	*
	*	Setup localization submenu page
	*	@since 3.0
	*
	******************************************************************************************************************/

	function __construct() {

		/*titles/labels throughout class*/
		add_action('init', array( $this, 'init_admin_lables') );
		/** registering submenu page -> priority 80 **/
		add_action( 'admin_menu', array( $this, 'wppizza_register_submenu_page'), $this->submenu_priority );
		/**add settings sections on this submenu page**/
		add_action('current_screen', array( $this, 'wppizza_admin_settings_sections'));
		/*register capabilities for this page*/
		add_filter('wppizza_filter_define_caps', array( $this, 'wppizza_filter_define_caps'), $this->submenu_priority);
		/*execute some helper functions once to use their return multiple times */
		add_action('current_screen', array( $this, 'wppizza_add_helpers') );
		/**admin ajax**/
		add_action('wp_ajax_wppizza_admin_'.$this->class_key.'_ajax', array($this, 'set_admin_ajax') );

		/**add to default options **/
		//add_filter('wppizza_filter_setup_default_options', array( $this, 'wppizza_options_default'));
		/**validate options**/
		add_filter('wppizza_filter_options_validate', array( $this, 'options_validate'), 10, 2 );

	}
	/******************
	*	@since 3.0.26
    *	[admin ajax include file]
    *******************/
	public function init_admin_lables(){
		/*titles/labels throughout class*/
		$this->submenu_page_header	=	apply_filters('wppizza_filter_admin_label_page_header_'.$this->class_key.'', __('Localization','wppizza-admin'));
		$this->submenu_page_title	=	apply_filters('wppizza_filter_admin_label_page_title_'.$this->class_key.'', __('Manage Localization','wppizza-admin'));
		$this->submenu_caps_title	=	apply_filters('wppizza_filter_admin_label_caps_title_'.$this->class_key.'', __('Localization','wppizza-admin'));
		$this->submenu_link_label	=	apply_filters('wppizza_filter_admin_label_link_label_'.$this->class_key.'', __('&middot; Localization','wppizza-admin'));		
	}	
	/******************
	*	@since 3.0
    *	[admin ajax include file]
    *******************/
	public function set_admin_ajax(){
		require(WPPIZZA_PATH.'ajax/admin.ajax.wppizza.php');
		die();
	}
	/*********************************************************
	*
	*	[add global helpers and enque js]
	*	@since 3.0
	*
	*********************************************************/
	public function wppizza_add_helpers($current_screen){
		if($current_screen->id == 'options' && isset($_POST[''.WPPIZZA_POST_TYPE.'_'.$this->class_key.''])){		
			/** return capabilities when saving options **/
			add_filter( 'option_page_capability_'.WPPIZZA_SLUG.'', array($this, 'admin_option_page_capability' ));
		}		
		if($current_screen->id == ''.WPPIZZA_POST_TYPE.'_page_'.$this->class_key.'' && $current_screen->post_type == WPPIZZA_POST_TYPE){
			/***enqueue scripts and styles***/
			add_action('admin_enqueue_scripts', array( $this, 'wppizza_enqueue_admin_scripts_and_styles'));
		}
	}
	/*********************************************************
	*
	*	[enqueue]
	*	@since 3.0
	*
	*********************************************************/
    public function wppizza_enqueue_admin_scripts_and_styles($hook) {
		/************
			js
		***********/
    	wp_register_script(WPPIZZA_SLUG.'_'.$this->class_key.'', plugins_url( 'js/scripts.admin.'.$this->class_key.'.js', WPPIZZA_PLUGIN_PATH ), array('jquery'), WPPIZZA_VERSION ,true);
    	wp_enqueue_script(WPPIZZA_SLUG.'_'.$this->class_key.'');
    }

	/*********************************************************
	*
	*	[register submenu page]
	*	@since 3.0
	*
	*********************************************************/
	public function wppizza_register_submenu_page(){
		$submenu_page= array(
			'url' => 'edit.php?post_type='.WPPIZZA_SLUG.'',
			'title' => ''.WPPIZZA_NAME.' '.$this->submenu_page_title,
			'link_label' => $this->submenu_link_label,
			'caps' => 'wppizza_cap_'.$this->class_key.'',
			'key' => $this->class_key,
			'callback' => array($this, 'wppizza_admin_manage_sections')
		);
		/**add submenu page**/
		$wppizza_submenu_page=add_submenu_page($submenu_page['url'], $submenu_page['title'], $submenu_page['link_label'], $submenu_page['caps'], $submenu_page['key'], $submenu_page['callback']);
		/**add contextual help**/
		add_action('load-'.$wppizza_submenu_page.'', array($this, 'wppizza_submenu_page_contextual_help'));
	}

	/*********************************************************
	*
	*	[echo manage settings]
	*
	*	wrap settings sections into div->form
	*	add uniquely identifiable id's / classes
	*	add h2 text
	*	add uniquely identifiable hidden input
	*	add submit button
	*
	*	@since 3.0
	*	@return str
	*
	*********************************************************/
	public function wppizza_admin_manage_sections(){

		/*
			wppizza post type only
		*/
		$screen = get_current_screen();
		if($screen->post_type != WPPIZZA_POST_TYPE){return;}
		
		
		/** get sections settings**/
		$settings=$this->wppizza_get_settings(true);

		/**wrap settings sections into div->form */
		echo'<div id="'.WPPIZZA_SLUG.'-'.$this->class_key.'" class="'.WPPIZZA_SLUG.'-wrap  '.WPPIZZA_SLUG.'-'.$this->class_key.'-wrap">';


		echo"<div class='".WPPIZZA_SLUG."-admin-pageheader'>";
		
			echo"<h2>";
				/*help icon*/
				echo"<a href='javascript:void(0)' class='wppizza-dashicons-admin button'><span class='dashicons dashicons-editor-help wppizza-show-admin-help'></span></a>";
				echo"<span id='".WPPIZZA_SLUG."-header'>".WPPIZZA_NAME." ".$this->submenu_page_header."</span>";
			echo"</h2>";

			/*help text*/
			echo"<span class='wppizza-help-hint'>".__('Note: Some options will have more details in the <a href="javascript:void(0)" class="wppizza-show-admin-help">help screen</a>','wppizza-admin')."</span>";

		echo"</div>";
		
		/**update info / errors etc*/
		settings_errors();


		echo'<form action="options.php" method="post">';
		echo'<input type="hidden" name="'.WPPIZZA_SLUG.'_'.$this->class_key.'" value="1" />';

			/**echo wppizza settings field*/
			settings_fields(WPPIZZA_SLUG);
			/**echo settings sections**/
			foreach($settings['sections'] as $sections_key => $sections_label){
				echo'<div class="wppizza-section wppizza-section-'.$sections_key.'">';
					do_settings_sections($sections_key);
				echo'</div>';
			}

			/**echo submit button or diabled button*/
			if(WPPIZZA_DEV_ADMIN_NO_SAVE){
				print '<input type="button" class="'.WPPIZZA_PREFIX.'-save-disabled" value="'.__('Saving Disabled', 'wppizza-admin').'">';
			}else{
				submit_button( __('Save Changes', 'wppizza-admin') );
			}

		echo'</form>';
		echo'</div>';
	}
	/*********************************************************
	*
	*	[add settings section(s) and fields]
	*
	*	@since 3.0
	*	@return void
	*
	*********************************************************/
	function wppizza_admin_settings_sections(){
		global $current_screen;
		if($current_screen->id == ''.WPPIZZA_POST_TYPE.'_page_'.$this->class_key.'' && $current_screen->post_type == WPPIZZA_POST_TYPE){
			/** get settings sections and fields **/
			$settings=$this->wppizza_get_settings(true, true);

			/**iterate through sections*/
			foreach($settings['sections'] as $sections_key=>$sections_label){
				/**add section, emptiying label if no fields (if only help is defined for example)**/
				$sections_label = !empty($settings['fields'][$sections_key]) ? $sections_label : '' ;
				add_settings_section($sections_key, $sections_label, array( $this, 'wppizza_admin_settings_section_header'), $sections_key);

				/**add section fields**/
				if(!empty($settings['fields'][$sections_key])){
				foreach($settings['fields'][$sections_key] as $fields_key=>$field_values){
					add_settings_field($fields_key, $field_values[0], array( $this, 'wppizza_admin_settings_section_fields'), $sections_key, $sections_key, $field_values[1]);
				}}
			}

		}
	}
	/*********************************************************
	*
	*	[add contextual help to submenu page]
	*	@since 3.0
	*
	*********************************************************/
	function wppizza_submenu_page_contextual_help(){

		$screen = get_current_screen();
		/** get settings sections and fields **/
		$settings=$this->wppizza_get_settings(true, false, false, true);

		foreach($settings['sections'] as $section_key=>$section_label){
			/**only add tab if there is actually any help to display**/
			if(!empty($settings['help'][$section_key])){
				/**initialize content for this tab**/
				$help_content='';
					foreach($settings['help'][$section_key] as $help_info){
						/*add label*/
						if(!empty($help_info['label'])){
							$help_content.='<h3>'.$help_info['label'].'</h3>';
						}
						/*add description*/
						if(!empty($help_info['description']) && is_array($help_info['description'])){
							foreach($help_info['description'] as $description){
								$help_content.='<p>'.$description.'</p>';
							}
						}
					}
				/**add help tabs with content**/
				$screen->add_help_tab( array('id' => 'wppizza_'.$this->class_key.'_'.$section_key.'', 'title' => $section_label, 'content' => '<div class="wppizza_admin_context_help">'.$help_content.'</div>'));
			}
		}

	}

	/*********************************************************
	*
	*	[set settings section(s)]
	*	@parameter $sections bool -> return sections
	*	@parameter $fields bool -> return fields
	*	@since 3.0
	*	@return array
	*
	*********************************************************/
	private function wppizza_get_settings($sections=true, $fields=false, $inputs=false, $help=false){

		/**ini settings array to splice into**/
		$settings=array();
		if($sections){
			$settings['sections']=array();
		}
		if($fields){
			$settings['fields']=array();
		}
		if($help){
			$settings['help']=array();
		}

		/**
			allow filtering of settings sections
			to add additional if required
		**/
		$settings=apply_filters('wppizza_filter_settings_sections_'.$this->class_key.'', $settings, $sections, $fields, $inputs, $help);

	return $settings;
	}

	/*********************************************************
	*
	*	[add setting section(s) headers]
	*
	* 	@param array
	*	@return str
	*
	*********************************************************/
	public function wppizza_admin_settings_section_header($arg){
		/*might come in useful somewhere*/
		static $section_count=0;$section_count++;

		/**add more text headers if required*/
		do_action('wppizza_settings_sections_header_'.$this->class_key.'', $arg, $section_count);
	}
	/*********************************************************
	*
	*		[echo setting section(s) fields]
	*
	*********************************************************/
	public function wppizza_admin_settings_section_fields($args){
		/** 
			wppizza options set 
		**/
		global $wppizza_options;

		/**plugin key/name - if we are writing/adding to the localization page with a different plugin we can use that plugins slug (sanitised)**/
		$plugin_slug = empty($args['plugin_slug']) ? WPPIZZA_SLUG : wppizza_validate_alpha_only($args['plugin_slug']) ;
		/**option key - array key of parent option set in options table**/
		$options_key=$args['option_key'];
		/**value key - array key of option set in options table (subkey of options_key)**/
		$field=$args['value_key'];
		/**label (if any)**/
		$label = !empty($args['label']) ? $args['label'] : '' ;
		/**description (if any)**/
		$description = !empty($args['description']) ? ' <span class="description description_'.$field.'">'.implode(' ',$args['description']).'</span>' : '' ;
		/**is textarea**/
		$is_textarea= (!empty($args['textarea']) || ( !empty($args['type']) && $args['type'] == 'html' )) ? true : false ;
		/**is plaintext textarea**/
		$is_textarea_plaintext = (!empty($args['textarea_plaintext']) || ( !empty($args['type']) && $args['type'] == 'textarea' )) ? true : false ;		
		/** get a distinctly set value (from external plugin for example) if set **/
		$value = isset($args['value']) ?  $args['value'] : $wppizza_options[$options_key][$field] ;
		


		if($is_textarea){
			
			$editorId="".strtolower(WPPIZZA_SLUG."_".$options_key."_".$field)."";/* WP 3.9 doesnt like brackets in id's*/

			$editorName="".WPPIZZA_SLUG."[".$options_key."][".$field."]";

			$editorSettings = array();
			$editorSettings['teeny'] = 1;
			$editorSettings['wpautop'] = false;
			$editorSettings['textarea_name'] = $editorName;
			$editorSettings['editor_height'] = 100;
			$editorSettings['tinymce'] = true;/* no visual editor if false*/
			$editorSettings['media_buttons'] = false ;//($field=='templates_footer_note') ? false : true;/* perhaps no media buttons only for footer note ?*/

			/**
				output html editor
			**/
			echo"<div class='".WPPIZZA_SLUG."_localization_textarea_wrap'>";
				
				echo"<span>".$label."</span>";
				
					echo"<div class='".WPPIZZA_SLUG."_localization_textarea'>";
				
						wp_editor( $wppizza_options[$options_key][$field], $editorId, $editorSettings);
				
					echo"</div>";
			
			echo"</div>";
		
		}else{
			
			/**
				output input field, label and description
			**/
			echo '<label>';
			
				/* 
					output as plaintext textarea instead of type=text input 
				*/
				if($is_textarea_plaintext){
					
					echo "<textarea name='".$plugin_slug."[".$options_key."][".$field."]' cols='51' rows='5'>".esc_html($value)."</textarea>";	
				
				}else{
				
					echo "<input name='".$plugin_slug."[".$options_key."][".$field."]' size='50' type='text' value='".esc_html($value)."'>";				
				
				}
				
				print'' . $label . $description .  '';
			
			print'</label>';
		}

		/********************************
		*	[add action for modules to hook into, also providing full args list if needed somewhere]
		********************************/
		//do_action('wppizza_admin_settings_section_fields_'.$this->class_key.'', $wppizza_options, $options_key, $field, $label, $description, $args);

	}

	/****************************************************************
	*
	*	[validate options on save/update]
	*	unlike other modules, this is in th emain subpage class
	*	for easy validation across submodules
	*	@since 3.0
	*	@return array()
	*
	****************************************************************/
	function options_validate($options, $input){
		/**
			make sure we get the full array on install/update
		**/
		if ( empty( $_POST['_wp_http_referer'] ) ) {
			return $input;
		}
		
		if(isset($_POST[''.WPPIZZA_SLUG.'_'.$this->class_key.''])){

			/* 
				keys that are validated/allowed to have html 
			*/
			$allowHtml = apply_filters('wppizza_filter_localization_html', array());

			/* 
				array of localization variables to validate 
			*/
			$options[$this->class_key] = array();//initialize as empty array
			
			/* 
				validate 
			*/
			foreach($input[$this->class_key] as $key=>$string){
				/*add new value , but keep desciption (as its not editable on frontend)*/
				if(in_array($key, $allowHtml)){$html=1;}else{$html=false;}
				$options[$this->class_key][$key] = wppizza_validate_string($string, $html);

			}
		}

	return $options;
	}

	/*********************************************************
	*
	*	[define caps]
	*	@since 3.0
	*
	*********************************************************/
	function wppizza_filter_define_caps($caps){

		/**add editing capability for this page**/
		$caps[$this->class_key]=array('name'=>$this->submenu_caps_title ,'cap'=>'wppizza_cap_'.$this->class_key.'');

		return $caps;
	}

	/*********************************************************
	*
	*	[set required capability for this page]
	*	@since 3.0
	*
	*********************************************************/    
	function admin_option_page_capability($capability) {		
		$capability = 'wppizza_cap_'.$this->class_key.'';
	return $capability;
	}	
}

/***************************************************************
*
*	[ini class]
*
***************************************************************/
$WPPIZZA_LOCALIZATION = new WPPIZZA_LOCALIZATION();
?>