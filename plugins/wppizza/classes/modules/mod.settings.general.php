<?php
/**
* WPPIZZA_MODULE_SETTINGS_GENERAL Class
*
* @package     WPPIZZA
* @subpackage  WPPIZZA_MODULE_SETTINGS_GENERAL
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
class WPPIZZA_MODULE_SETTINGS_GENERAL{

	private $settings_page = 'settings';/* which admin subpage (identified there by this->class_key) are we adding this to */
	private $section_key = 'settings_general';/* must be unique */


	function __construct() {
		/**********************************************************
			[add settings to admin]
		***********************************************************/
		if(is_admin()){
			/* add admin options settings page*/
			add_filter('wppizza_filter_settings_sections_'.$this->settings_page.'', array($this, 'admin_options_settings'), 10, 5);
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
		/* mail_type wppizza_filter_mail_type deosnt actually exist anywhere so the filter below does not do anything - yet */
		//add_filter('wppizza_filter_mail_type', array( $this, 'mail_type'));
		
		/* post_single_template */
		add_filter('the_permalink', array( $this, 'search_results_permalink'));/**change the permalink of any wppizza menu item to use loop template for single item instead of having to create a different template*/
		add_filter('pre_get_posts', array( $this, 'single_items'));/**change the loop query when dealing with single menu items**//**use loop template to also display single items**/		
		/* using_cache_plugin */
		add_filter('wppizza_filter_using_cache_plugin', array( $this, 'using_cache_plugin'));
		/* ssl on checkout */
		add_filter('wppizza_filter_ssl_on_checkout', array( $this, 'ssl_on_checkout'));
		/* js in footer */
		add_filter('wppizza_filter_js_in_footer', array( $this, 'js_in_footer'));
		/* enable gutenberg */
		add_filter('wppizza_filter_cpt_args', array( $this, 'disable_gutenberg'));		
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
	
		set general mail type ?
	
	**************************************/
//	function mail_type($mail_type = 'wp_mail'){
//		global $wppizza_options;
//		/* get available mail options */
//		$options_available = wppizza_admin_mail_delivery_options(false, false, false, false, true);
//		/* set mail type in options */
//		$set_mail_type = $wppizza_options[$this->settings_page]['mail_type'];
//		
//		/** return selected mailtype if exists, or default to 'wp_mail' */
//		$mail_type = isset($options_available[$set_mail_type]) ? $set_mail_type : $mail_type;
//	
//	return $mail_type;
//	}
	/***************************************************************
		filter loop when using single item (for example a link from
		searchresults) to only display this single item
		if not using dedicated single-wppizza.php template
	****************************************************************/
	function single_items($query){/**add relevant filters*/
		global $post, $wppizza_options;
		/* skip if not set */
		if(empty($wppizza_options[$this->settings_page]['post_single_template'])){		
			return $query;
		}	
		if(isset($_GET[WPPIZZA_SINGLE_PERMALINK_VAR]) && $query->is_main_query()){
			add_filter('wppizza_filter_loop_args', array( $this, 'loop_args_single_item'));
			add_filter('the_content', array( $this, 'single_item_content'),0);
		}
	return $query;
	}
	/**run the filter on loop query **/
	function loop_args_single_item($args){
		if(isset($_GET[WPPIZZA_SINGLE_PERMALINK_VAR])){
			$args=array(
			  'name' => $_GET[WPPIZZA_SINGLE_PERMALINK_VAR],
			  'post_type' => WPPIZZA_POST_TYPE,
			  'post_status' => 'publish',
			  'numberposts' => 1
			);
			$menuItem = get_posts($args);
			if( $menuItem ) {
				$args['post__in']=array($menuItem[0]->ID);
				unset($args['tax_query']);
			}
		}
	return $args;
	}
	/*filter content*/
	function  single_item_content($content){
		global $post, $wppizza_options;
		/*replace the content od this page with relevant shortcode*/
		if($post->ID==$wppizza_options[$this->settings_page]['post_single_template']){
			ob_start();
			$content='';
	        echo do_shortcode( '[wppizza noheader=1]' );/*no need to add any other atts as the query takes care of the rest*/
			$content = ob_get_clean();
		}
		
	return $content;		
	}


	/*******************************************************
		[wppizza modify permalink in search results to use
		wppizza loop template when clicking on link instead
		of normal blog layout - only used when no proper single-wppizza.php
		template is in use]
	******************************************************/
	function search_results_permalink($url) {
		global $wppizza_options;
		/* skip if not set */
		if(empty($wppizza_options[$this->settings_page]['post_single_template'])){		
			return $url;
		}
		if(is_search() && get_post_type()==WPPIZZA_POST_TYPE){
			/*get slug**/
			$post_data = get_post(get_the_ID(), ARRAY_A);
			$slug = $post_data['post_name'];
			/*set args**/
    		$args=array();
    		$args['page_id']=$wppizza_options[$this->settings_page]['post_single_template'];/*use selected page to display things in to keep layout*/
    		$args['wppizza']=false;
    		$args[WPPIZZA_SINGLE_PERMALINK_VAR]=''.$slug.'';
    		/**amend permalink**/
    		return esc_url_raw(add_query_arg($args, $url));
		}else{
			return	$url;
		}
	}
	/*************************************
	
		using_cache_plugin ?
	
	**************************************/
	function using_cache_plugin($bool){
		global $wppizza_options;
		$bool = !empty($wppizza_options[$this->settings_page]['using_cache_plugin']) ? true : false;
	return $bool;
	}	

	/*************************************
	
		ssl_on_checkout ?
	
	**************************************/
	function ssl_on_checkout($bool){
		global $wppizza_options;
		$bool = !empty($wppizza_options[$this->settings_page]['ssl_on_checkout']) ? true : false;
	return $bool;
	}	
	/*************************************
	
		js_in_footer ?
	
	**************************************/
	function js_in_footer($bool){
		global $wppizza_options;
		$bool = !empty($wppizza_options[$this->settings_page]['js_in_footer']) ? true : false;
	return $bool;
	}
	
	/*************************************
	
		enable gutenberg
		@ since 3.8.2
	**************************************/
	function disable_gutenberg($args){
		global $wppizza_options;
		$args['show_in_rest'] = !empty($wppizza_options[$this->settings_page]['disable_gutenberg']) ? false : true;
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
	#	[settigs section - setting page]
	#	@since 3.0
	#	@return array()
	------------------------------------------------------------------------------*/	
	function admin_options_settings($settings, $sections, $fields, $inputs, $help){
		global $wp_version;
		/********************************
		*	[general ]
		********************************/
		/*section*/
		if($sections){
			$settings['sections'][$this->section_key] = __('General', 'wppizza-admin');
		}
		/*help*/
		if($help){		
			$settings['help'][$this->section_key][] = array(
				'label'=>__('General', 'wppizza-admin'),
				'description'=>array(
					__('Please adjust settings as appropriate according to the information provided next to each individual option.', 'wppizza-admin')
				)
			);
		}		
		/*fields*/
		if($fields){
//			$field = 'mail_type';
//			$settings['fields'][$this->section_key][$field] = array( __('Select Type of Mail Delivery', 'wppizza-admin'), array(
//				'value_key'=>$field,
//				'option_key'=>$this->settings_page,
//				'label'=>__('might be worth changing if you have trouble when sending/receiving orders with the default settings or prefer html emails', 'wppizza-admin'),
//				'description'=>array(
//					__('if using PHPMailer function you probably want to edit the html template. To do so, move "wppizza-order-html-email.php" from the wppizza template directory to your theme folder and edit as required', 'wppizza-admin')
//				)
//			));
			$field = 'post_single_template';
			$settings['fields'][$this->section_key][$field] = array( __('Single menu items display', 'wppizza-admin'), array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('you could also leave this off and use the wppizza widget/shortcode for a dedicated search box', 'wppizza-admin'),
				'description'=>array(
					__('please see the <a href="http://docs.wp-pizza.com/developers/?section=wppizza-markup-single-single-php">faq\'s -> single wppizza menu items display</a> for details as to how this works', 'wppizza-admin')
				)
			));			
			$field = 'using_cache_plugin';			
			$settings['fields'][$this->section_key][$field] = array( __('I am using a caching plugin', 'wppizza-admin'), array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('ALWAYS load the cart dynamically via ajax.', 'wppizza-admin'),
				'description'=>array(
					__('Especially useful if your caching plugin does not support the exclusion of only parts of a page.', 'wppizza-admin'),
					__('Note: you still want to exclude your entire *order page* - or at least the main content of that page - from being cached in your cache plugin (please see the documentation for your choosen cache plugin for how to do this).', 'wppizza-admin'),	
					__('After you enable this, clear your cache.', 'wppizza-admin')	
				)
			));			
			$field = 'ssl_on_checkout';
			$settings['fields'][$this->section_key][$field] = array( __('SSL on checkout', 'wppizza-admin'), array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('set your order page to be https (you must have an SSL certificate installed)', 'wppizza-admin'),					
				'description'=>array()
			));			
			$field = 'js_in_footer';
			$settings['fields'][$this->section_key][$field] = array( __('Javascript in Footer', 'wppizza-admin'), array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('combines all jsVars in one tidy place, but requires wp_footer in theme', 'wppizza-admin'),
				'description'=>array()
			));
			if (version_compare($wp_version, "5", ">=")) {
			$field = 'disable_gutenberg';
			$settings['fields'][$this->section_key][$field] = array( __('Disable Gutenberg Editor', 'wppizza-admin'), array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('By default (for now) the Gutenberg editor integrated into WP 5+ has been disabled on menu items. Un-check this box if you would like to use it.', 'wppizza-admin'),
				'description'=>array()
			));
			}


		}


		return $settings;
	}
	/*------------------------------------------------------------------------------
	#	[output option fields - setting page]
	#	@since 3.0
	#	@return array()
	------------------------------------------------------------------------------*/	
	function admin_options_fields_settings($wppizza_options, $options_key, $field, $label, $description){
		global $wp_version;
//		if($field=='mail_type'){
//			echo "<label>";		
//				echo"".wppizza_admin_mail_delivery_options(''.WPPIZZA_SLUG.'['.$options_key.'][mail_type]', $wppizza_options[$options_key]['mail_type'])."";
//				echo "" . $label . "";
//			echo "</label>";
//			echo"".$description."";
//		}
	
		if($field=='post_single_template'){
			echo "<label>";		
				/**which single post template ?*/
				echo "<select name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' />";
					echo"<option value=''>".__('default or custom template [single-wppizza.php if exists]', 'wppizza-admin')."</option>";
					foreach(wppizza_get_wordpress_pages() as $k=>$v){
						if(isset($wppizza_options[$options_key]['post_single_template']) && $wppizza_options[$options_key]['post_single_template']==$v->ID){$sel=' selected="selected"';}else{$sel='';}
						echo"<option value='".$v->ID."' ".$sel.">".$v->post_title."</option>";
					}
				echo "</select>";			
				echo "" . $label . "";
			echo "</label>";
			echo"".$description."";			
		}
	
		if($field=='using_cache_plugin'){
			echo "<label>";		
				echo "<input id='".$field."' name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' type='checkbox'  ". checked($wppizza_options[$options_key][$field],true,false)." value='1' />";
				echo "" . $label . "";
			echo "</label>";
			echo"".$description."";			
		}
	
		if($field=='ssl_on_checkout'){
			echo "<label>";		
				echo "<input id='".$field."' name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' type='checkbox'  ". checked($wppizza_options[$options_key][$field],true,false)." value='1' />";
				echo "" . $label . "";
			echo "</label>";
			echo"".$description."";			
		}
	
		if($field=='js_in_footer'){
			echo "<label>";		
				echo "<input id='".$field."' name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' type='checkbox'  ". checked($wppizza_options[$options_key][$field],true,false)." value='1' />";
				echo "" . $label . "";
			echo "</label>";
			echo"".$description."";			
		}		
		/* wordpress 5 only */
		if (version_compare($wp_version, "5", ">=")) { 			
		if($field=='disable_gutenberg'){
			echo "<label>";		
				echo "<input id='".$field."' name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' type='checkbox'  ". checked($wppizza_options[$options_key][$field],true,false)." value='1' />";
				echo "" . $label . "";
			echo "</label>";
			echo"".$description."";			
		}}			
						
	}
	/*------------------------------------------------------------------------------
	#	[insert default option on install]
	#	$parameter $options array() | filter passing on filtered options
	#	@since 3.0
	#	@return array()
	------------------------------------------------------------------------------*/
	function options_default($options){
		//$options[$this->settings_page]['mail_type'] = 'wp_mail';
		$options[$this->settings_page]['post_single_template'] = array();
		$options[$this->settings_page]['using_cache_plugin'] = false;
		$options[$this->settings_page]['ssl_on_checkout'] = false;
		$options[$this->settings_page]['js_in_footer'] = true;
		$options[$this->settings_page]['disable_gutenberg'] = true;
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
			//$options[$this->settings_page]['mail_type'] = wppizza_validate_alpha_only($input[$this->settings_page]['mail_type']);
			$options[$this->settings_page]['post_single_template'] = !empty($input[$this->settings_page]['post_single_template']) ? (int)$input[$this->settings_page]['post_single_template'] : '';
			$options[$this->settings_page]['using_cache_plugin'] = !empty($input[$this->settings_page]['using_cache_plugin']) ? true : false;
			$options[$this->settings_page]['ssl_on_checkout'] = !empty($input[$this->settings_page]['ssl_on_checkout']) ? true : false;
			$options[$this->settings_page]['js_in_footer'] = !empty($input[$this->settings_page]['js_in_footer']) ? true : false;			
			$options[$this->settings_page]['disable_gutenberg'] = !empty($input[$this->settings_page]['disable_gutenberg']) ? true : false;
		}
	return $options;
	}
}
/***************************************************************
*
*	[ini]
*
***************************************************************/
$WPPIZZA_MODULE_SETTINGS_GENERAL = new WPPIZZA_MODULE_SETTINGS_GENERAL();
?>