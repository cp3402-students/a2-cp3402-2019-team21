<?php
/**
* WPPIZZA_MANAGE_TEMPLATES Class
*
* @package     WPPIZZA
* @subpackage  Submenu Pages / Classes / WPPIZZA_MANAGE_TEMPLATES
* @copyright   Copyright (c) 2015, Oliver Bach
* @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
* @since       3.0
*
*/
if ( ! defined( 'ABSPATH' ) ) exit;/*Exit if accessed directly*/


/************************************************************************************************************************
*
*
*	WPPIZZA_MANAGE_TEMPLATES
*
*
************************************************************************************************************************/
class WPPIZZA_MANAGE_TEMPLATES{

	/*
	* class ident
	* @var str
	* @since 3.0
	*/
	private $class_key='templates';/*to help consistency throughout class in various places*/
	/*
	* titles/lables
	* @var str
	* @since 3.0
	*/
	private $submenu_page_header;
	private $submenu_page_title;
	private $submenu_caps_title;
	private $submenu_link_label;
	private $tab_emails = 'emails';
	private $tab_print = 'print';
	private $submenu_priority = 100;
	function __construct() {


		/*titles/labels throughout class*/
		add_action('init', array( $this, 'init_admin_lables') );

		/** registering submenu page -> priority 100 **/
		add_action('admin_menu', array( $this, 'wppizza_register_submenu_page'), $this->submenu_priority );
		/*register capabilities for this page*/
		add_filter('wppizza_filter_define_caps', array( $this, 'wppizza_filter_define_caps'), $this->submenu_priority);

		/**add settings sections on this submenu page**/
		add_action('current_screen', array( $this, 'wppizza_admin_settings_sections'));

		/**add to default options **/
		add_filter('wppizza_filter_setup_default_options', array( $this, 'wppizza_options_default'));

		/*execute some helper functions once to use their return multiple times */
		add_action('current_screen', array( $this, 'wppizza_add_helpers') );


		/**validate options**/
		add_filter('wppizza_filter_options_validate', array( $this, 'wppizza_options_validate'), 10, 2 );

		/**admin ajax**/
		add_action('wp_ajax_wppizza_admin_'.$this->class_key.'_ajax', array($this, 'set_admin_ajax') );


	}
	/******************
	*	@since 3.0.26
    *	[admin ajax include file]
    *******************/
	public function init_admin_lables(){
		/*titles/labels throughout class*/
		$this->submenu_page_header	=	apply_filters('wppizza_filter_admin_label_page_header_'.$this->class_key.'', __('Templates','wppizza-admin'));
		$this->submenu_page_title	=	apply_filters('wppizza_filter_admin_label_page_title_'.$this->class_key.'', __('Manage Templates','wppizza-admin'));
		$this->submenu_caps_title	=	apply_filters('wppizza_filter_admin_label_caps_title_'.$this->class_key.'', __('Templates','wppizza-admin'));
		$this->submenu_link_label	=	apply_filters('wppizza_filter_admin_label_link_label_'.$this->class_key.'', __('&middot; Templates','wppizza-admin'));
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
	*	[class helpers]
	*	@since 3.0
	*
	*********************************************************/
    public function wppizza_enqueue_admin_scripts_and_styles($hook) {
    	/**include sortable js*/
		wp_enqueue_script('jquery-ui-sortable');

    	wp_register_script(WPPIZZA_SLUG.'_'.$this->class_key.'', plugins_url( 'js/scripts.admin.'.$this->class_key.'.js', WPPIZZA_PLUGIN_PATH ), array('jquery'), WPPIZZA_VERSION ,true);
    	wp_enqueue_script(WPPIZZA_SLUG.'_'.$this->class_key.'');
    }




	/****************************************************************
	*
	*	[insert default option on install]
	*	$parameter $options array() | filter passing on filtered options
	*	@since 3.0
	*	@return array()
	*
	****************************************************************/
	/*template types*/
	function all_templates_types(){
		$type = array();
		$type['emails'] = ''.$this->class_key.'_emails';
		$type['print'] = ''.$this->class_key.'_print';
	return $type;
	}

	/*default templates - id == 0*/
	function set_default_templates(){
		$template_types = $this->all_templates_types();
		$template_options = array();
		foreach($template_types as $template_type => $template_ident){
			$tpl_args = array(
				'tpl_type' => $template_type,
				'tpl_id' => 0,
				'tpl_values'=>  false,
				'tpl_install'=>  true,
			);
			$template_options[$template_ident][0] = WPPIZZA()->order->orders_formatted(false, $tpl_args, 'template_install_'.$template_type.'');
		}

	return $template_options;
	}

	/*default templates applied to user*/
	function wppizza_options_default($options){
		/*default recipients*/
		$recipients_defaults = WPPIZZA()->helpers->default_email_recipients(true);

		$options['templates_apply']=array(
			'emails'=>array(
				'recipients_default'=> $recipients_defaults,/*default to original editable templates*/
				'recipients_additional'=>array()/*default to no additional recipients*/
			),
			'print'=>0
		);

		return $options;
	}

	/****************************************************************
	*
	*	[validate options on save/update]
	*	@since 3.0
	*	@return array()
	*
	****************************************************************/
	function wppizza_options_validate($options, $input){
		/**make sure we get the full array on install/update**/
		if ( empty( $_POST['_wp_http_referer'] ) ) {
			return $input;
		}

		/****************************************
		*
		*	[validate on save]
		*
		****************************************/
		if(isset($_POST[''.WPPIZZA_SLUG.'_'.$this->class_key.''])){

			/**********************************************************************

				overwrite global mail delivery if set
				(in emails standard template)

			**********************************************************************/
			if(!empty($input['settings']['mail_type']) && in_array($input['settings']['mail_type'],array('phpmailer','wp_mail'))){
				$options['settings']['mail_type']=$input['settings']['mail_type'];
			}


			/******************************************************************************
			*
			*
			*	validate/save email templates
			*
			*
			******************************************************************************/
			if(!empty($input[$this->class_key]['emails'])){
				$tplKey='emails';
				$option_name=WPPIZZA_SLUG.'_'.$this->class_key.'_'.$tplKey.'';
				$template_options=get_option($option_name);/*get current*/


				/***********************************************
					unset deleted
					however, if we deleted a template that had a
					recipient set, we need to set the recipient to - 1
					to use default further down
				***********************************************/
				if(!empty($input['template_remove'][$tplKey]) && is_array($input['template_remove'][$tplKey])){
					foreach($input['template_remove'][$tplKey] as $iId){
						unset($template_options[$iId]);
						/*unset from main templates_apply->emails->recipients_additional wppizza options too*/
						//unset($options['templates_apply'][$tplKey]['recipients_additional'][$iId]);
					}
				}
				/***********************************************
					[loop through set templates on page]
				***********************************************/
				if(!empty($input[$this->class_key][$tplKey])){
				foreach($input[$this->class_key][$tplKey] as $key=>$val){

					/**reset this key first of all**/
					$template_options[$key]=array();
					/*admin sort order*/
					$template_options[$key]['sort']=$val['sort'];//!empty($val['admin_sort']) ? json_decode($val['admin_sort'],true) :
					/*validate title*/
					$template_options[$key]['title']=!empty($val['title']) ? wppizza_validate_string($val['title']) : 'undefined';
					/*html or plaintext ? */
					$template_options[$key]['mail_type']=wppizza_validate_alpha_only($val['mail_type']);
					/*omit attachments ?*/
					$template_options[$key]['omit_attachments']=!empty($val['omit_attachments']) ? true : false;
					/* recipients_additional */
					$template_options[$key]['recipients_additional']=wppizza_validate_email_array(trim($val['recipients_additional']));
					/*global_styles*/
					$template_options[$key]['global_styles']=$val['global_styles'];
					/*sections - only storing parameters we actually need*/
					$template_options[$key]['sections']=array();
					if(!empty($val['sections'])){
					foreach($val['sections'] as $section_key=>$section_val){
						/* section styles */
						if(!empty($section_val['style'])){
							$template_options[$key]['sections'][$section_key]['style']=$section_val['style'];
						}
						/* section enabled */
						if(!empty($section_val['section_enabled'])){
							$template_options[$key]['sections'][$section_key]['section_enabled']= true;
						}
						/* label enabled */
						if(!empty($section_val['label_enabled'])){
							$template_options[$key]['sections'][$section_key]['label_enabled']= true;
						}

						/* parameters */
						if(!empty($section_val['parameters'])){
							foreach($section_val['parameters'] as $parameter_key=>$parameter_val){
								if(!empty($parameter_val['enabled'])){
								$template_options[$key]['sections'][$section_key]['parameters'][$parameter_key]['enabled']= true;
								}
							}
						}

					}}

				}}

				/*

					set email recipients, overwriting old values if set

				*/
				/*shop recipient | customer recipient */
				foreach(WPPIZZA()->helpers->default_email_recipients() as $rKey=>$rVal){
					if(isset($input['templates_apply'][$tplKey]['recipients_default'][$rKey]) && $input['templates_apply'][$tplKey]['recipients_default'][$rKey]!=''){
						$options['templates_apply'][$tplKey]['recipients_default'][$rKey]=(int)$input['templates_apply'][$tplKey]['recipients_default'][$rKey];
					}
				}

				/*
					set additional email recipients that are set for each template
					looping over all templates one more time and adding any add recipients set for default
					template
				*/
				$recipients_additional = array();
				/* default template */
				$add_recipients_default = !empty($input['templates_apply'][$tplKey]['recipients_additional'][-1]) ? wppizza_validate_email_array($input['templates_apply'][$tplKey]['recipients_additional'][-1]) : array();
				if(count($add_recipients_default)>0){
					$recipients_additional[-1] = $add_recipients_default;
				}
				/** each drag and drop template **/
				if(!empty($template_options)){
					foreach($template_options as $set_template_key=>$set_templates){
						if(count($set_templates['recipients_additional'])>0){
								$recipients_additional[$set_template_key] = $set_templates['recipients_additional'];
						}
					}
				}
				$options['templates_apply'][$tplKey]['recipients_additional'] = $recipients_additional;

			}
			/******************************************************************************
			*
			*
			*	validate/save print templates
			*
			*
			******************************************************************************/
			if(!empty($input[$this->class_key]['print'])){

				$tplKey='print';
				$option_name=WPPIZZA_SLUG.'_'.$this->class_key.'_'.$tplKey.'';
				$template_options=get_option($option_name);/*get current*/


				/***********************************************
					unset deleted
					however, if we deleted a template that had a
					recipient set, we need to set the recipient to - 1
					to use default further down
				***********************************************/
				if(!empty($input['template_remove'][$tplKey]) && is_array($input['template_remove'][$tplKey])){
					foreach($input['template_remove'][$tplKey] as $iId){
						unset($template_options[$iId]);
					}
				}

				/***********************************************
					[loop through set templates on page]
				***********************************************/
				foreach($input[$this->class_key][$tplKey] as $key=>$val){

					/**reset this key first of all**/
					$template_options[$key]=array();
					/*admin sort order*/
					$template_options[$key]['sort']=$val['sort'];//!empty($val['admin_sort']) ? json_decode($val['admin_sort'],true) :
					/*validate title*/
					$template_options[$key]['title']=!empty($val['title']) ? wppizza_validate_string($val['title']) : 'undefined';
					/*html or plaintext ? */
					$template_options[$key]['mail_type']=wppizza_validate_alpha_only($val['mail_type']);
					/*global_styles*/
					$template_options[$key]['global_styles']=$val['global_styles'];
					/*sections - only storing parameters we actually need*/
					$template_options[$key]['sections']=array();
					if(!empty($val['sections'])){
					foreach($val['sections'] as $section_key=>$section_val){
						/* section styles */
						if(!empty($section_val['style'])){
							$template_options[$key]['sections'][$section_key]['style']=$section_val['style'];
						}
						/* section enabled */
						if(!empty($section_val['section_enabled'])){
							$template_options[$key]['sections'][$section_key]['section_enabled']= true;
						}
						/* label enabled */
						if(!empty($section_val['label_enabled'])){
							$template_options[$key]['sections'][$section_key]['label_enabled']= true;
						}

						/* parameters */
						if(!empty($section_val['parameters'])){
							foreach($section_val['parameters'] as $parameter_key=>$parameter_val){
								if(!empty($parameter_val['enabled'])){
								$template_options[$key]['sections'][$section_key]['parameters'][$parameter_key]['enabled']= true;
								}
							}
						}

					}}

					/***set which print template to use if enabled in this template**/
					if(isset($input['templates_apply'][$tplKey]['print_id']) && $input['templates_apply'][$tplKey]['print_id']!=''){
						$options['templates_apply'][$tplKey]=(int)$input['templates_apply'][$tplKey]['print_id'];
					}
				}


				/* select default template to use if all drag/drop templates deleted*/
				if(empty($template_options) || count($template_options)==0){
					$options['templates_apply'][$tplKey] = -1;
				}

			}

			/*
				update relevant template options
			*/
			if(!empty($option_name)){
				update_option($option_name, $template_options);

			}
		}

		return $options;
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
				/**add section**/
				add_settings_section($sections_key, $sections_label, array( $this, 'wppizza_admin_settings_section_header'), $sections_key);
				/**add section fields**/
				foreach($settings['fields'][$sections_key] as $fields_key=>$field_values){
					add_settings_field($fields_key, $field_values[0], array( $this, 'wppizza_admin_settings_section_fields'), $sections_key, $sections_key, $field_values[1]);
				}
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
						/* add tab label if not exists */
						$section_label = empty($section_label) ? $help_info['label'] : $section_label;

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
				$screen->add_help_tab( array('id' => 'wppizza_'.$this->class_key.'_'.$section_key.'', 'title' => ''.$section_label, 'content' => '<div class="wppizza_admin_context_help">'.$help_content.'</div>'));
			}
		}
	}
	/*********************************************************
	*
	*		[echo setting section(s) fields]
	*
	*********************************************************/
	public function wppizza_admin_settings_section_fields($args){
		/** wppizza options set **/
		global $wppizza_options;

		/* get section. default or custom */
		$template_section=$args['type'];
		/* get type (emails or print) */
		$template_type=$args['tab'];
		/** get options **/
		$template_options = get_option(WPPIZZA_SLUG.'_'.$this->class_key.'_'.$template_type.'',0);


		/***********************************
		*
		*	drag drop email/print templates - paginated
		*
		************************************/

			/*get pagination*/
			$pagination=WPPIZZA()->admin_helper->admin_pagination($template_options, WPPIZZA_ADMIN_TEMPLATES_PERPAGE);

			echo"<div id='".WPPIZZA_SLUG."_list_".$this->class_key."_new' class='".WPPIZZA_SLUG."_list_".$this->class_key."'></div>";

			echo"<div id='".WPPIZZA_SLUG."_list_".$this->class_key."_custom' class='".WPPIZZA_SLUG."_list_".$this->class_key."'>";

				/**pagination top**/
				echo'<div class="widefat '.WPPIZZA_SLUG.'-pagination '.WPPIZZA_SLUG.'-pagination-top">';
					echo'<span class="'.WPPIZZA_SLUG.'-pagination-left">'.$pagination['on_page'].' '.__('of','wppizza-admin').' '.$pagination['total_count'].'</span>';
					echo'<span class="'.WPPIZZA_SLUG.'-pagination-right">'.$pagination['pages'] .'</span>';
				echo'</div>';

				foreach($pagination['list'] as $template_key=>$template_values){
					$tpl = WPPIZZA()->templates_email_print->admin_template($template_key, $template_type, $template_values);
					echo'' . $tpl . '';
				}

				/**pagination bottom**/
				echo'<div class="widefat '.WPPIZZA_SLUG.'-pagination '.WPPIZZA_SLUG.'-pagination-bottom">';
					echo'<span class="'.WPPIZZA_SLUG.'-pagination-left">'.$pagination['on_page'].' '.__('of','wppizza-admin').' '.$pagination['total_count'].'</span>';
					echo'<span class="'.WPPIZZA_SLUG.'-pagination-right">'.$pagination['pages'] .'</span>';
				echo'</div>';


			echo"</div>";
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
		/** get tabs**/
		$tabs=$this->wppizza_admin_tabs();
		/**sections in tab**/
		$sections_in_tab=$tabs['tab'][$tabs['current']]['sections'];
		/**sections in tab**/
		$tab_save=!empty($tabs['tab'][$tabs['current']]['save_options']) ? true : false;

		/** output tabs at top**/
		echo $tabs['markup'];

		/**wrap settings sections into div->form */
		echo'<div id="'.WPPIZZA_SLUG.'-'.$this->class_key.'" class="'.WPPIZZA_SLUG.'-wrap  '.WPPIZZA_SLUG.'-'.$this->class_key.'-wrap">';


		echo"<div class='".WPPIZZA_SLUG."-admin-pageheader'>";

			echo"<h2>";
				/*help icon*/
				echo"<a href='javascript:void(0)' class='wppizza-dashicons-admin button'><span class='dashicons dashicons-editor-help wppizza-show-admin-help'></span></a>";
				echo"<span id='".WPPIZZA_SLUG."-header'>".WPPIZZA_NAME." ".$this->submenu_page_header." - ".$tabs['label']."</span>";
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

			/**echo settings sections according to tab selected**/
			foreach($settings['sections'] as $sections_key => $sections_label){

				/**if using tabs, only display appropriate sections per tab */
				if(in_array($sections_key,$sections_in_tab)){
					echo'<div class="'.WPPIZZA_SLUG.'-section '.WPPIZZA_SLUG.'-section-'.$this->class_key.' '.WPPIZZA_SLUG.'-section-'.$sections_key.'">';
						do_settings_sections($sections_key);
					echo'</div>';
				}
			}

			/**only output save botton when enabled**/
			if($tab_save){
				/**echo submit button or diabled button*/
				if(WPPIZZA_DEV_ADMIN_NO_SAVE){
					print '<input type="button" class="'.WPPIZZA_PREFIX.'-save-disabled" value="'.__('Saving Disabled', 'wppizza-admin').'">';
				}else{
					submit_button( __('Save Changes', 'wppizza-admin') );
				}
			}

		echo'</form>';
		echo'</div>';
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
		global $blog_id;
		/**ini setiings array**/
		$settings=array();

		/********************************
		*	[emails]
		********************************/
		/*sections*/
		if($sections){
			//$settings['sections']['templates-emails'] = array('section'=>'templates-emails', 'section_title'=>'', 'help_title'=>__('Emails','wppizza-admin'));
			$settings['sections'][$this->tab_emails] = '';
		}

		/*fields*/
		if($fields){
//			$field = 'standard';
//			$settings['fields'][$this->tab_emails][$field] = array('', array(
//				'tab'=>$this->tab_emails,
//				'type'=>$field
//			));
			$field = 'custom';
			$settings['fields'][$this->tab_emails][$field] = array('', array(
				'tab'=>$this->tab_emails,
				'type'=>$field
			));
		}
		/*help*/
		if($help){
	   		/********************
	    		emails help screen
	    	********************/
			$emailsHelpTabContent='';
		    $emailsHelpTabContent.='<div class="wppizza_help_tab_item">';
		    $emailsHelpTabContent.='<h3>use the options below to create/add/edit email templates you wish to send to selected recipients when an order completes</h3>';
		    $emailsHelpTabContent.='</div>';

		    $emailsHelpTabContent.='<div class="wppizza_help_tab_item">';
		    $emailsHelpTabContent.='<b>general:</b> labels - where applicable - are set in the "localization" or "order form settings" screen ';
		    $emailsHelpTabContent.='</div>';

		    $emailsHelpTabContent.='<div class="wppizza_help_tab_item">';
		    $emailsHelpTabContent.='<b>recipients (shop and bccs, customer, additional recipients):</b> the first selected one will be the main recipient of any emails sent. all others will be in cc. (bccs set in wppizza order settings will of course still  be in bcc if set to "shop and bccs" )';
		    $emailsHelpTabContent.='<div style="margin-left:10px"><b>examples:</b><ul>';
		   	$emailsHelpTabContent.='<li>"shop and bccs" as well as  "customer" selected: shop as recipient, customer in cc (provided email was given), bccs as set in order settings</li>';
		   	$emailsHelpTabContent.='<li>"customer" and "additional recipients" selected: customer as recipient, additional recipients in cc </li>';
		   	$emailsHelpTabContent.='<li>"additional recipients" only: every individual additional recipient will receive *separate* emails using the selected template</li>';
		    $emailsHelpTabContent.='</ul>';
		    $emailsHelpTabContent.='</div>';
		    $emailsHelpTabContent.='</div>';

		    $emailsHelpTabContent.='<div class="wppizza_help_tab_item">';
		    $emailsHelpTabContent.='<b>omit attachments:</b> do not attach any files from "wppizza->order settings : Email Attachments" to the email. Default template will always include any attachments defined';
		    $emailsHelpTabContent.='</div>';

		    $emailsHelpTabContent.='<div class="wppizza_help_tab_item">';
		    $emailsHelpTabContent.='<span class="wppizza_template_toggle  wppizza-dashicons-admin dashicons-edit"></span>';
		    $emailsHelpTabContent.='<span class="wppizza_help_tab_info"><b>edit:</b> click to be able to  move entire sections such as "site details", "overview", "customer details" etc (drag/drop left/right) in your preferred order. Drag and drop (up/down) individual values into the order you prefer for output in that template. To enable or disable any particular value(s) enable or disable its checkbox.</span>';
		    $emailsHelpTabContent.='</div>';

		    $emailsHelpTabContent.='<div class="wppizza_help_tab_item">';
		    $emailsHelpTabContent.='<span class="wppizza-dashicons-admin dashicons-media-code  wppizza-dashicons-template-emails-media-code"></span>';
		    $emailsHelpTabContent.='<span class="wppizza_help_tab_info"><b>css/style (HTML format only):</b> if HTML as output format has been selected, this button becomes available which will let you edit the style declarations on individual sections and/or values. if you edit any declarations, make sure you preview your changes before saving</span>';
		    $emailsHelpTabContent.='</div>';

		    $emailsHelpTabContent.='<div class="wppizza_help_tab_item">';
		    $emailsHelpTabContent.='<span class="wppizza_template_preview wppizza-dashicons-admin dashicons-visibility" title="preview"></span>';
		    $emailsHelpTabContent.='<span class="wppizza_help_tab_info"><b>preview:</b> click for a preview of your current settings before committing/saving any changes</span>';
		    $emailsHelpTabContent.='</div>';

		    $emailsHelpTabContent.='<div class="wppizza_help_tab_item">';
		    $emailsHelpTabContent.='<span class="wppizza_template_delete wppizza-dashicons-admin dashicons-trash" title="delete"></span>';
		    $emailsHelpTabContent.='<span class="wppizza_help_tab_info"><b>remove:</b> click to remove template.</span>';
		    $emailsHelpTabContent.='</div>';

		    $emailsHelpTabContent.='<div class="wppizza_help_tab_item">';
		    $emailsHelpTabContent.='<span class="wppizza_help_tab_info"><b>to commit your changes you must click on "save changes" to save any edits you may have made.</b></span>';
		    $emailsHelpTabContent.='</div>';

			$settings['help'][$this->tab_emails][] = array(
				'label'=>__('Emails', 'wppizza-admin'),
				'description'=>array($emailsHelpTabContent)
			);
		}

		/********************************
		*	[print_order]
		********************************/
		/*sections*/
		if($sections){
			$settings['sections'][$this->tab_print] = '';
		}

		/*fields*/
		if($fields){
//			$field = 'standard';
//			$settings['fields'][$this->tab_print][$field] = array('', array(
//				'tab'=>$this->tab_print,
//				'type'=>$field
//			));
			$field = 'custom';
			$settings['fields'][$this->tab_print][$field] = array('', array(
				'tab'=>$this->tab_print,
				'type'=>$field
			));
		}

		/*help*/
		if($help){
		    /********************
		    	print help screen
		    ********************/
		    $printHelpTabContent='';
		    $printHelpTabContent.='<div class="wppizza_help_tab_item">';
		    $printHelpTabContent.='<h3>use the options below to create/add/edit the template you wish to use when printing from the order history screen</h3>';
		    $printHelpTabContent.='</div>';

		    $printHelpTabContent.='<div class="wppizza_help_tab_item">';
		    $printHelpTabContent.='<b>general:</b> labels - where applicable - are set in the "localization" or "order form settings" screen';
		    $printHelpTabContent.='</div>';

		    $printHelpTabContent.='<div class="wppizza_help_tab_item">';
		    $printHelpTabContent.='<b>format - if applicable:</b> select from plaintext or HTML format';
		    $printHelpTabContent.='</div>';

		    $printHelpTabContent.='<div class="wppizza_help_tab_item">';
			$printHelpTabContent.='<label class="wppizza-dashicons-admin wppizza-dashicons-radio">use <input type="radio" checked="checked" value="1"></label>';
			$printHelpTabContent.='<span class="wppizza_help_tab_info">check to select that particular template when printing from the order history screen</span>';
		    $printHelpTabContent.='</div>';

		    $printHelpTabContent.='<div class="wppizza_help_tab_item">';
		    $printHelpTabContent.='<span class="wppizza_template_toggle  wppizza-dashicons-admin dashicons-edit"></span>';
		    $printHelpTabContent.='<span class="wppizza_help_tab_info"><b>edit:</b> click to be able to  move entire sections such as "site details", "overview", "customer details" etc (drag/drop left/right) in your preferred order. Drag and drop (up/down) individual values into the order you prefer for output in that template. To enable or disable any particular value(s) enable or disable its checkbox.</span>';
		    $printHelpTabContent.='</div>';

		    $printHelpTabContent.='<div class="wppizza_help_tab_item">';
		    $printHelpTabContent.='<span class="wppizza-dashicons-admin dashicons-media-code  wppizza-dashicons-template-emails-media-code"></span>';
		    $printHelpTabContent.='<span class="wppizza_help_tab_info"><b>css/style (HTML format only):</b> if HTML as output format has been selected, this button becomes available which will let you edit the css for that template. if you edit any declarations, make sure you preview your changes before saving.<br /><b>use the "preview" and your browsers element inspector in that preview to view all available classes and id\'s </b></span>';
		    $printHelpTabContent.='</div>';

		    $printHelpTabContent.='<div class="wppizza_help_tab_item">';
		    $printHelpTabContent.='<span class="wppizza_template_preview wppizza-dashicons-admin dashicons-visibility" title="preview"></span>';
		    $printHelpTabContent.='<span class="wppizza_help_tab_info"><b>preview:</b> click for a preview of your current settings before committing/saving any changes</span>';
		    $printHelpTabContent.='</div>';

		    $printHelpTabContent.='<div class="wppizza_help_tab_item">';
		    $printHelpTabContent.='<span class="wppizza_template_delete wppizza-dashicons-admin dashicons-trash" title="delete"></span>';
		    $printHelpTabContent.='<span class="wppizza_help_tab_info"><b>remove:</b> click to remove template.</span>';
		    $printHelpTabContent.='</div>';

		    $printHelpTabContent.='<div class="wppizza_help_tab_item">';
		    $printHelpTabContent.='<span class="wppizza_help_tab_info"><b>to commit your changes you must click on "save changes" to save any edits you may have made.</b></span>';
		    $printHelpTabContent.='</div>';

			$settings['help'][$this->tab_print][] = array(
				'label'=>__('Print Order', 'wppizza-admin'),
				'description'=>array($printHelpTabContent)
			);
		}
		/**
			allow filtering of settings sections
			do add additional if required
		**/
		$settings=apply_filters('wppizza_filter_settings_sections_'.$this->class_key.'', $settings, $sections, $fields, $inputs, $help);

		return $settings;
	}
	/*********************************************************
		[Set Subpages Tabs and sections per tab]
	*********************************************************/
	function wppizza_admin_tabs() {
		/**available tabs => label, slug, sections in page for tab, enable save button **/
		$tabs['tab']['emails']=array('lbl'=>__('E-Mails','wppizza-admin'), 'slug'=>'emails', 'sections'=>array($this->tab_emails), 'save_options' => true);
		$tabs['tab']['print']=array('lbl'=>__('Print Order','wppizza-admin'), 'slug'=>'print', 'sections'=>array($this->tab_print), 'save_options' => true);
		// future use perhaps // $tabs['tab']['pages']=array('lbl'=>__('Pages','wppizza-admin'), 'slug'=>'pages', 'sections'=>array($this->tab_pages), 'save_options' => true);
		// future use perhaps // $tabs['tab']['layout']=array('lbl'=>__('Layout Styles','wppizza-admin'), 'slug'=>'layout', 'sections'=>array($this->tab_layout), 'save_options' => true);


		/**allow filtering**/
		$tabs=apply_filters('wppizza_filter_admin_tabs_'.$this->class_key.'', $tabs);

		/**get selected or first if none selected**/
		$tab_keys=array_keys($tabs['tab']);
		$first_tab = reset($tab_keys);
		$tabs['current'] = (!empty($_GET['tab']) && isset($tabs['tab'][$_GET['tab']])) ?  $_GET['tab'] : $tabs['tab'][$first_tab]['slug'];
		$tabs['label'] = (!empty($_GET['tab']) && isset($tabs['tab'][$_GET['tab']])) ?  $tabs['tab'][$_GET['tab']]['lbl'] : $tabs['tab'][$first_tab]['lbl'];

		/**tabs markup**/
		$tabs['markup']='';
		$tabs['markup'].='<div id="icon-themes" class="icon32"><br></div>';
		$tabs['markup'].='<h2 class="nav-tab-wrapper '.WPPIZZA_SLUG.'-nav-tab-wrapper">';
		foreach( $tabs['tab'] as $tab => $arr ){
		    $class = ( $arr['slug'] == $tabs['current'] ) ? ' nav-tab-active' : '';
		    $tabs['markup'].="<a class='nav-tab ".$class."' href='?post_type=".WPPIZZA_POST_TYPE."&page=".$this->class_key."&tab=".$arr['slug']."'>".$arr['lbl']."</a>";
		}
		$tabs['markup'].='</h2>';

		return $tabs;
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
		static $section_count=0;$section_count++;

		/**add new template button**/
		echo"<span id='".WPPIZZA_SLUG."-".$this->class_key."-add'>";
		echo "<a href='javascript:void(0)' id='".WPPIZZA_SLUG."_add_".$arg['id']."' class='button ".WPPIZZA_SLUG."_add_".$this->class_key."  ".WPPIZZA_SLUG."-".$this->class_key."-add-button'>".__('add template', 'wppizza-admin')."</a>";
		echo"</span>";

		/**add more text headers if required*/
		do_action('wppizza_settings_sections_header_'.$this->class_key.'', $arg, $section_count);
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
*	[ini]
*
***************************************************************/
$WPPIZZA_MANAGE_TEMPLATES = new WPPIZZA_MANAGE_TEMPLATES();
?>