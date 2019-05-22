<?php
/**
* WPPIZZA_MODULE_SETTINGS_SEARCH Class
*
* @package     WPPIZZA
* @subpackage  WPPIZZA_MODULE_SETTINGS_SEARCH
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
class WPPIZZA_MODULE_SETTINGS_SEARCH{

	private $settings_page = 'settings';/* which admin subpage (identified there by this->class_key) are we adding this to */


	private $section_key = 'search';/* must be unique */


	function __construct() {
		/**********************************************************
			[add settings to admin]
		***********************************************************/
		if(is_admin()){
			/* add admin options settings page*/
			add_filter('wppizza_filter_settings_sections_'.$this->settings_page.'', array($this, 'admin_options_settings'), 80, 5);
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
		/***
			alter search query if/when required to filter search to include wppizza etc
			frontend only ! or else the admin search in pst types gets messes up
		**/
		if(!is_admin()){
			add_filter( 'pre_get_posts', array( $this, 'set_search_query' ));		
		}
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
	/*******************************************************
		[set search box queries ]
	******************************************************/
	function set_search_query( $query ) {
    	if (is_search() && $query->is_main_query()) {

		global $wppizza_options;
			/*******************************************************************
				exclude wppizza cpt from search results if not enabled

				furthermore, if no post_type var has been set in query, set all other used ones
				if they HAVE been set, we should not need to do anything
				as the wppizza cpt will be specifically set/added or not
			*******************************************************************/
			if(!$wppizza_options[$this->settings_page]['search_include'] && !isset($_REQUEST['post_type'])){
				if(!isset($query->query_vars['post_type']) ){
					/**get all queryable and exclude/unset wppizza***/
					$post_types = get_post_types( array('public' => true,'exclude_from_search' => false), 'names' );
					unset($post_types[WPPIZZA_POST_TYPE]);
					$query->set('post_type',$post_types);
				}
			}
			/**post types set when using shortcodes/widget etc**/
			if(isset($_REQUEST['post_type'])){
				$request_types=explode(",",$_REQUEST['post_type']);
				/**if we have set another permalink for single mnu items, rewrite this here so the query finds wppizza after all**/
				if(isset($wppizza_options[$this->settings_page]['single_item_permalink_rewrite']) && $wppizza_options[$this->settings_page]['single_item_permalink_rewrite']!='' && in_array($wppizza_options[$this->settings_page]['single_item_permalink_rewrite'],$request_types)){
					$key = array_search($wppizza_options[$this->settings_page]['single_item_permalink_rewrite'], $request_types);
					$request_types[$key]=WPPIZZA_POST_TYPE;
				}
				/**get all queryable and get intersection just to be tidy and stop people from entering random query vars***/
				$post_types = get_post_types( array('public' => true,'exclude_from_search' => false), 'names' );
				$post_types_array = array_intersect($request_types, $post_types);
				$query->set( 'post_type', $post_types_array);


    			$query = apply_filters('wppizza_filter_search', $query);
			}
    	}


    	return $query;
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
			$settings['sections'][$this->section_key] =  __('Search', 'wppizza-admin');
		}
		/*help*/
		if($help){
			$settings['help'][$this->section_key][] = array(
				'label'=>__('Search', 'wppizza-admin'),
				'description'=>array(
					__('Please adjust settings as appropriate according to the information provided next to each individual option.', 'wppizza-admin')
				)
			);
		}
		/*fields*/
		if($fields){
			$field = 'search_include';
			$settings['fields'][$this->section_key][$field] = array( __('Include wppizza menu items in regular search results', 'wppizza-admin'), array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('you could also leave this off and use the wppizza widget/shortcode for a dedicated search box', 'wppizza-admin'),
				'description'=>array()
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
		if($field=='search_include'){
			echo "<label>";
				echo "<input id='".$field."' name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' type='checkbox'  ". checked($wppizza_options[$options_key][$field],true,false)." value='1' />";
				echo "" . $label . "";
			echo "</label>";
			echo"".$description."";
		}
	}
	/*------------------------------------------------------------------------------
	#	[insert default option on install]
	#	$parameter $options array() | filter passing on filtered options
	#	@since 3.0
	#	@return array()
	------------------------------------------------------------------------------*/
	function options_default($options){
		
		$options[$this->settings_page]['search_include'] = false;		
		
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
			$options[$this->settings_page]['search_include'] = !empty($input[$this->settings_page]['search_include']) ? true : false;			
		}
	return $options;
	}
}
/***************************************************************
*
*	[ini]
*
***************************************************************/
$WPPIZZA_MODULE_SETTINGS_SEARCH = new WPPIZZA_MODULE_SETTINGS_SEARCH();
?>