<?php
/**
* WPPIZZA_MODULE_TOOLS_MISCELLANEOUS Class
*
* @package     WPPIZZA
* @subpackage  WPPIZZA_MODULE_TOOLS_MISCELLANEOUS
* @copyright   Copyright (c) 2015, Oliver Bach
* @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
* @since       3.0
*
*/
if ( ! defined( 'ABSPATH' ) ) exit;/*Exit if accessed directly*/


/************************************************************************************************************************
*
*
*
*
*
*
************************************************************************************************************************/
class WPPIZZA_MODULE_TOOLS_MISCELLANEOUS{

	private $settings_page = 'tools';/* which admin subpage (identified there by this->class_key) are we adding this to */
	private $tab_key = 'miscellaneous';/* must be unique within this admin page*/
	private $section_key = 'various';

	function __construct() {
		/**********************************************************
			[add settings to admin]
		***********************************************************/
		if(is_admin()){
			/*** add to a specific tab ***/
			add_filter('wppizza_filter_admin_tabs_'.$this->settings_page.'', array($this, 'admin_tabs'), 10);
			/* add admin options settings page*/
			add_filter('wppizza_filter_settings_sections_'.$this->settings_page.'', array($this, 'admin_options_settings'), 10, 5);
			/* add admin options settings page fields */
			add_action('wppizza_admin_settings_section_fields_'.$this->settings_page.'', array($this, 'admin_options_fields_settings'), 10, 5);
			/**add default options **/
			add_filter('wppizza_filter_setup_default_options', array( $this, 'options_default'));
			/**validate options**/
			add_filter('wppizza_filter_options_validate', array( $this, 'options_validate'), 10, 2 );
			/**add text header*/
//			//add_action('wppizza_settings_sections_header_'.$this->settings_page.'', array( $this, 'sections_header'), 10, 2 );
		}
		/* disable email sending if set */
		add_filter('wppizza_filter_send_emails', array( $this, 'filter_send_emails'), 10);
	}

	/*******************************************************************************************************************************************************
	*
	*
	*
	* 	[frontend filters]
	*
	*
	*
	********************************************************************************************************************************************************/
	/* disable email sending if set */
	function filter_send_emails($bool){
		global $wppizza_options;
		$bool = empty($wppizza_options[$this->settings_page]['disable_emails']) ? true : false;
	return $bool;
	}


	/*******************************************************************************************************************************************************
	*
	*
	*
	* 	[add admin page options]
	*
	*
	*
	********************************************************************************************************************************************************/

	/*------------------------------------------------------------------------------
	#
	#
	#	[settings page]
	#
	#
	------------------------------------------------------------------------------*/

	/*********************************************************
			[add section to a particular tab]
	*********************************************************/
	function admin_tabs($tabs){
		$tabs['tab'][$this->tab_key]['sections'][] = $this->section_key;
	return $tabs;
	}
	/*------------------------------------------------------------------------------
	#	[settings section - setting page]
	#	@since 3.0
	#	@return array()
	------------------------------------------------------------------------------*/
	function admin_options_settings($settings, $sections, $fields, $inputs, $help){

		/*section*/
		if($sections){
			$settings['sections'][$this->section_key] =  __('Miscellaneous', 'wppizza-admin');
		}

		/*help*/
		if($help){
			$settings['help'][$this->section_key][] = array(
				'label'=>__('Miscellaneous', 'wppizza-admin'),
				'description'=>array(
					__('Please adjust settings as appropriate according to the information provided next to each individual option.', 'wppizza-admin')
				)
			);
		}

		/*fields*/
		if($fields){

			$field = 'disable_emails';
			$settings['fields'][$this->section_key][$field] = array(__('Disable email sending', 'wppizza-admin') , array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('Check this box to stop sending emails. If you want to test things without actually sending any emails', 'wppizza-admin'),
				'description'=>array()
			));


			$field = 'empty_category_and_items';
			$settings['fields'][$this->section_key][$field] = array(__('Delete Categories/Items/Images', 'wppizza-admin') , array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('Delete ALL Categories and Items','wppizza-admin'),
				'description'=>array(
					'<span class="wppizza-highlight">'.__('use with care','wppizza-admin').'</span>',
					'<span class="wppizza-highlight">'.__('if you select "delete images too", all featured images used for any menu items will be deleted too','wppizza-admin').'</span>',
					'<span class="wppizza-highlight">'.__('if you use these images elsewhere, you should not select this !').'</span>'
				)
			));

			$field = 'debug';
			$settings['fields'][$this->section_key][$field] = array(__('Enable debug', 'wppizza-admin') , array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('Set your debug configuration in your wp-config.php like so:','wppizza-admin'),
				'description'=>array(
					'<pre>define("WP_DEBUG", true);<br />define("WP_DEBUG_LOG", true);<br />define("WP_DEBUG_DISPLAY", false);/*this should NEVER be true for production sites*/</pre>',
					__('REPLACING your current wp-config.php debug settings if different','wppizza-admin'),
					'<b>'.__('Make sure these constants are added/set BEFORE /* That\'s all, stop editing! Happy blogging. */','wppizza-admin').'</b>',
					'<br>'.__('your debug log will be located at wp-content/debug.log','wppizza-admin'),
				)
			));

			$field = 'category_repair';
			$settings['fields'][$this->section_key][$field] = array(__('Repair categories', 'wppizza-admin') , array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('Enable and save to repair', 'wppizza-admin'),
				'description'=>array(
					__('There exists an (as yet) unknown sequence of events related to saving/adding/editing/deleting categories of this plugin that may result in the last category being repeated when using the category=!all shortcode attribute and/or not all categories showing up in the admin of the plugin.', 'wppizza-admin'),
					__('If this should be the case, you can try repairing this by checking the box above and saving once.', 'wppizza-admin'),
					'<span class="wppizza-highlight">'.__('If you use this function, categories will be re-set using default alphabetical sort order, so please ensure your category order is still as required as you might have to re-sort - i.e drag and drop -  categories again.', 'wppizza-admin').'</span>',
					__('In case this does not solve the issue, please contact me, letting me know anything you did before this issue occured if possible.', 'wppizza-admin')
				)
			));

			$field = 'default_templates_reset';
			$settings['fields'][$this->section_key][$field] = array(__('Reset default templates', 'wppizza-admin') , array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('Enable and save to reset the *default* email and print templates', 'wppizza-admin'),
				'description'=>array(
					'<span class="wppizza-highlight">'.__('Note: any additional templates added will remain unaffected. Current css declarations will be preserved.', 'wppizza-admin').'</span>',
				)
			));

		}
	return $settings;
	}
	/*------------------------------------------------------------------------------
	#	[output option fields - setting page]
	#	@since 3.0
	#	@return array()
	------------------------------------------------------------------------------*/
	function admin_options_fields_settings($wppizza_options, $options_key, $field, $label, $description){

		if($field=='disable_emails'){
			print'<label>';
				print "<input name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' type='checkbox'  ". checked($wppizza_options[$options_key][$field],true,false)." value='1' />";
				print'' . $label . '';
			print'</label>';
			print'' . $description . '';
		}

		if($field=='empty_category_and_items'){
			print'<label>';
				print "<input name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' type='checkbox' value='1' />";
				print'' . $label . '';
			print'</label>';

			print"<br />";
			print"<label>";
				print"<input id='empty_category_and_items_delete_attachments' name='".WPPIZZA_SLUG."[".$options_key."][delete_attachments]' type='checkbox'  value='1' />".__('Delete images too', 'wppizza-admin')."</label>";
			print'' . $description . '';
		}


		if($field=='category_repair'){
			print'<label>';
				print "<input name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' type='checkbox' value='1' />";
				print'' . $label . '';
			print'</label>';
			print'' . $description . '';
		}

		if($field=='default_templates_reset'){
			print'<label>';
				print "<input name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' type='checkbox' value='1' />";
				print'' . $label . '';
			print'</label>';
			print'' . $description . '';
		}

		if($field=='debug'){
			print'<label>';
				print'' . $label . '';
			print'</label>';
			print'' . $description . '';
		}
	}

	/****************************************************************
	*
	*	[insert default option on install]
	*	$parameter $options array() | filter passing on filtered options
	*	@since 3.0
	*	@return array()
	*
	****************************************************************/
	function options_default($options){
			$options[$this->settings_page]['disable_emails'] = false;
		return $options;
	}

	/*------------------------------------------------------------------------------
	#	[validate options on save/update]
	#
	#	@since 3.0
	#	@return array()
	------------------------------------------------------------------------------*/
	function options_validate($options, $input){
		/**make sure we get the full array on install/update**/
		if ( empty( $_POST['_wp_http_referer'] ) ) {
			return $input;
		}
		if(isset($_POST[''.WPPIZZA_SLUG.'_'.$this->settings_page.'_'.$this->tab_key.''])){
			$options[$this->settings_page]['disable_emails'] = !empty($input[$this->settings_page]['disable_emails']) ? true : false;


			/*********************************
				repair categories if enabled,
				skip if deleting all anyway
			*********************************/
			if(!empty($input[$this->settings_page]['category_repair']) && empty($input[$this->settings_page]['empty_category_and_items']) ){
				$category_sort_reset = WPPIZZA() -> categories -> wppizza_get_cats_hierarchy();
				/***overwrite old vars**/
				$options['layout']['category_sort_hierarchy']=$category_sort_reset;
			}

			/*********************************
				reset default email/print templates,
			*********************************/
			if(!empty($input[$this->settings_page]['default_templates_reset']) ){

				/***************************
					get default email/print templates
				***************************/
				$TEMPLATES = new WPPIZZA_MANAGE_TEMPLATES();
				$templates_default = $TEMPLATES -> set_default_templates();

				/**************************
					add template defaults
				**************************/
				foreach($templates_default as $tplKey => $template_options){
					$template_option_name = WPPIZZA_SLUG.'_'.$tplKey.'';
					$template_options = get_option($template_option_name);

					/* get current */
					$current_defaults = $template_options[0];

					/* override [0] (default) key with ini vars */
					$template_options[0] = $templates_default[$tplKey][0];

					/* re-apply current|old css settings */
					$template_options[0]['global_styles'] = $current_defaults['global_styles'];
					foreach($current_defaults['sections'] as $sKey=>$sVal){
						$template_options[0]['sections'][$sKey]['style'] = $sVal['style'] ;
					}
					/* update resetting defualt option */
					update_option($template_option_name, $template_options, false);
				}

			}


			/************************************
				delete wppizza posts, categories
				and - possibly - images/attachments
			************************************/
			if(!empty($input[$this->settings_page]['empty_category_and_items'])){
				/**delete cats and posts**/
				$this->wppizza_empty_taxonomy_posts(!empty($input[$this->settings_page]['delete_attachments']) ? true : false);
				/***reset cat sort (as we will have deleted all categories)**/
				$options['layout']['category_sort_hierarchy']=array();
			}

		}

	return $options;
	}

	/*********************************************************
	*
	*	[delete wppizza categories, posts with/out attachments]
	*	@since 3.0
	*
	*********************************************************/
	function wppizza_empty_taxonomy_posts($deleteAttachments=false){
		$terms = get_terms(''.WPPIZZA_TAXONOMY.'', array('hide_empty' => false));

		  if(count($terms)>0){
			/*************************************************************************************************
			*
			*	[first get all posts and make an array of all post we have to use in attachment/post delete]
			*
			*************************************************************************************************/
			$postids=array();
			$args = array('post_type'=> WPPIZZA_SLUG ,'posts_per_page'=>-1);
			$the_query = new WP_Query( $args );
			if($the_query->have_posts()) {
				$posts=$the_query->posts;
			}
			if(isset($posts) && is_array($posts)){
			foreach($posts as $k=>$v){
				$postids[]=$v->ID;
			}}
			/*************************************************************************************************
			*
			*	[as attachments parents get set to 0 when a post is deleted , delete attachments first (ifset)]
			*
			*************************************************************************************************/
			if($deleteAttachments){
				if(isset($postids) && is_array($postids)){
				foreach($postids as $k=>$v){
					$args = array(
					'post_parent' => $v,
					'post_status' => null,
					'post_type' => 'attachment'
					);
					$attachments = get_children( $args );
					foreach($attachments as $attachment){
						wp_delete_attachment( $attachment->ID,true );
					}
				}}
			}
			/*************************************************************************************************
			*
			*	[now lets delete all posts]
			*
			*************************************************************************************************/
			if(isset($postids) && is_array($postids)){
			foreach($postids as $k=>$v){
				wp_delete_post( $v, true );
			}}


			/*************************************************************************************************
			*
			*	[now lets delete all terms]
			*
			*************************************************************************************************/
			foreach( $terms as $term ){
				wp_delete_term( $term->term_id, WPPIZZA_TAXONOMY );
			}
		  }
	}


}
/***************************************************************
*
*	[ini]
*
***************************************************************/
$WPPIZZA_MODULE_TOOLS_MISCELLANEOUS = new WPPIZZA_MODULE_TOOLS_MISCELLANEOUS();
?>