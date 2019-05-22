<?php
/**
* WPPIZZA_MODULE_SETTINGS_PERMALINKS Class
*
* @package     WPPIZZA
* @subpackage  WPPIZZA_MODULE_SETTINGS_PERMALINKS
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
class WPPIZZA_MODULE_SETTINGS_PERMALINKS{

	private $settings_page = 'settings';/* which admin subpage (identified there by this->class_key) are we adding this to */


	private $section_key = 'permalinks';/* must be unique */


	function __construct() {
		/**********************************************************
			[add settings to admin]
		***********************************************************/
		if(is_admin()){
			/* add admin options settings page*/
			add_filter('wppizza_filter_settings_sections_'.$this->settings_page.'', array($this, 'admin_options_settings'), 70, 5);
			/* add admin options settings page fields */
			add_action('wppizza_admin_settings_section_fields_'.$this->settings_page.'', array($this, 'admin_options_fields_settings'), 10, 5);
			/**add default options **/
			add_filter('wppizza_filter_setup_default_options', array( $this, 'options_default'));
			/**validate options**/
			add_filter('wppizza_filter_options_validate', array( $this, 'options_validate'), 10, 2 );
		}
		/**********************************************************
			[filter/actions depending on settings]
		***********************************************************/
		/** rewrite single item permalinks**/
		add_filter('wppizza_filter_cpt_args',array( $this, 'rewrite_single_item_permalink'));
		/* rewrite taxonomy parent page */
		add_filter('wppizza_filter_ctx_args', array( $this, 'taxonomy_parent_page'));

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
	/*************************************

		rewrite_single_item_permalink

	**************************************/
	function rewrite_single_item_permalink($args){
		global $wppizza_options;

		if(!empty($wppizza_options[$this->settings_page]['single_item_permalink_rewrite'])){
			/**change single item post slug from wppizza to selected slug**/
			$args['rewrite'] = array( 'slug' => sprintf( __( '%s', 'wppizza-admin' ), $wppizza_options[$this->settings_page]['single_item_permalink_rewrite'] ) );
		}
		return $args;
	}

	/*************************************

		category_parent_page

	**************************************/
	function taxonomy_parent_page($args){
		global $wppizza_options;
		if(!empty($wppizza_options[$this->settings_page]['category_parent_page'])){
			/**get the right one when using wpml**/
			if(function_exists('icl_object_id') && $options!=0) {
				$wppizza_options[$this->settings_page]['category_parent_page'] = icl_object_id($wppizza_options[$this->settings_page]['category_parent_page'],'page');
			}
			$set_category_parent = get_post($wppizza_options[$this->settings_page]['category_parent_page'],ARRAY_A);/*orig*/
			/* rewrite slug if not still empty */
			if(!empty($set_category_parent['post_name'])){
				$args['rewrite']['slug'] = $set_category_parent['post_name'];
			}
		}
	return $args;
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

	/*------------------------------------------------------------------------------
	#	[settings section - setting page]
	#	@since 3.0
	#	@return array()
	------------------------------------------------------------------------------*/
	function admin_options_settings($settings, $sections, $fields, $inputs, $help){

		/*section*/
		if($sections){
			$settings['sections'][$this->section_key] =  __('Permalinks', 'wppizza-admin');
		}
		/*help*/
		if($help){
			$settings['help'][$this->section_key][] = array(
				'label'=>__('Permalinks', 'wppizza-admin'),
				'description'=>array(
					__('Please adjust settings as appropriate according to the information provided next to each individual option.', 'wppizza-admin'),
					'<span class="wppizza-highlight-important">'.__('Note: Settings here are only applicable if *not* using plain permalink settings. When changing any of the permalink options, you MUST re-save your permalink settings in Wordpress Settings -> Permalinks', 'wppizza-admin').'<span>'
				)
			);
		}
		/*fields*/
		if($fields){
			$field = 'category_parent_page';
			$settings['fields'][$this->section_key][$field] = array( __('Categories/Pages', 'wppizza-admin'), array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>'',
				'description'=>array(
					__('only used and relevant when using widget or shortcode to display wppizza category navigation !!!','wppizza-admin'),
					__('page cannot be used as static post page (wp settings) or have any children', 'wppizza-admin')
				)
			));
			$field = 'single_item_permalink_rewrite';
			$settings['fields'][$this->section_key][$field] = array( __('Single Menu Items', 'wppizza-admin'), array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>'',
				'description'=>array(
					__('only used and relevant when actually linking to a single item from anywhere', 'wppizza-admin'),
					__('defaults to "wppizza" if left empty. Any value used here can not be used in by any other custom post type', 'wppizza-admin'),
					__('Note: by default, wppizza templates/shortcodes do not link to any single menu items. However, if you are including mneu items in search results for example or have edited a/the template(s) to include links to individual menu items you will also (probably) want to edit the single item template. see http://docs.wp-pizza.com/developers/?section=wppizza-markup-single-single-php', 'wppizza-admin')
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

		if($field=='category_parent_page'){
			/**check which pages have children (so we can exclude from dropdown as otherwise children pages will not be accessible*/
			$exclude=array();
			foreach(wppizza_get_wordpress_pages() as $k=>$v){
				$children = get_pages('child_of='.$v->ID);
				if( count( $children ) != 0 ) {$exclude[]=$v->ID;}
			}
			$exclude[]=get_option('page_for_posts');/*also exclude page thats set for default posts*/

			echo "<label>";
				echo "<select name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' />";
					echo"<option value=''>".__('no parent [default]', 'wppizza-admin')."</option>";
					foreach(wppizza_get_wordpress_pages() as $k=>$v){
						if(in_array($v->ID,$exclude)){
							echo"<option value='' style='color:red'>".$v->post_title." ".__('[not selectable]', 'wppizza-admin')."</option>";
						}else{
							if($wppizza_options[$options_key][$field]==$v->ID){$sel=' selected="selected"';}else{$sel='';}
							echo"<option value='".$v->ID."' ".$sel.">".$v->post_title."</option>";
						}
					}
				echo "</select>";
				echo "" . $label . "";
			echo "</label>";
			echo"".$description."";
			echo"<br /><span class='wppizza-highlight'>".__('when changing this setting, you MUST re-save your permalink settings', 'wppizza-admin')."</span>";
		}

		if($field=='single_item_permalink_rewrite'){
			echo "<label>";
				echo "<input name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' size='20' type='text'  value='".$wppizza_options[$options_key][$field]."' />";
				echo "" . $label . "";
			echo "</label>";
			echo"".$description."";
			echo"<br /><span class='wppizza-highlight'>".__('when changing this setting, you MUST re-save your permalink settings', 'wppizza-admin')."</span>";
		}
	}
	/*------------------------------------------------------------------------------
	#	[insert default option on install]
	#	$parameter $options array() | filter passing on filtered options
	#	@since 3.0
	#	@return array()
	------------------------------------------------------------------------------*/
	function options_default($options){
		$options[$this->settings_page]['category_parent_page'] = '';
		$options[$this->settings_page]['single_item_permalink_rewrite'] = '';
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
		/********************************
		*	[validate]
		********************************/
		if(isset($_POST[''.WPPIZZA_SLUG.'_'.$this->settings_page.''])){
			$options[$this->settings_page]['category_parent_page'] = !empty($input[$this->settings_page]['category_parent_page']) ? (int)$input[$this->settings_page]['category_parent_page'] : '';
			$options[$this->settings_page]['single_item_permalink_rewrite'] = sanitize_title($input[$this->settings_page]['single_item_permalink_rewrite']);
		}
	return $options;
	}
}
/***************************************************************
*
*	[ini]
*
***************************************************************/
$WPPIZZA_MODULE_SETTINGS_PERMALINKS = new WPPIZZA_MODULE_SETTINGS_PERMALINKS();
?>