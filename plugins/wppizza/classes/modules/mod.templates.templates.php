<?php
/**
* WPPIZZA_MODULE_TEMPLATES_TEMPLATES Class
*
* @package     WPPIZZA
* @subpackage  WPPIZZA_MODULE_TEMPLATES_TEMPLATES
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
class WPPIZZA_MODULE_TEMPLATES_TEMPLATES{

	private $settings_page = 'templates';/* which admin subpage (identified there by this->class_key) are we adding this to */
	private $section_key = 'templates';/* must be unique */
	private $module_priority = 10;/* display order (priority) of settings in subpage */


	function __construct() {
		/**********************************************************
			[add settings to admin]
		***********************************************************/
		if(is_admin()){
			/** admin ajax **/
			add_action('wppizza_ajax_admin_'.$this->settings_page.'', array( $this, 'admin_ajax'));

		}
	}
	/*******************************************************************************************************************************************************
	*
	* 	[admin ajax]
	*
	********************************************************************************************************************************************************/
	function admin_ajax($wppizza_options){

		/*****************************************************
				[preview templates output]
		*****************************************************/
		if($_POST['vars']['field']=='preview_template'){

			/****************************************
				get the last completed order
				to use as preview
			****************************************/

			$order = WPPIZZA() -> db -> get_last_completed_blog_order_id(false);

			/****************************************
				no order exists that could be used
				as preview
			****************************************/
			if(empty($order)){
				$markup['error']="Error [ADM-101]:" . PHP_EOL . __(' Sorry, you must have at least one completed order for the preview to work.','wppizza-admin'). PHP_EOL ;

				print"".json_encode($markup)."";
				exit();
			}

			/************************************************************
				what template type (print/emails)
			************************************************************/
			$preview_type = $_POST['vars']['data']['template_type'];
			/************************************************************
				template id
			************************************************************/
			$preview_id = $_POST['vars']['data']['template_id'];
			/************************************************************
				template id
			************************************************************/
			$preview_is_html = ($_POST['vars']['data']['mail_type'] == 'phpmailer') ? true : false ;
			/************************************************************
				map preview parameters
				into the same multidimensional array (keys / values)
				as they will be stored in the options table when saving
				but ignoring
				[sort], [title], [omit_attachments] and [recipients_additional]
				as these are not required for output display/formatting
				of email/print templates
			************************************************************/
			$preview = array();/* ini array */
			/************************************************************
				plaintext or html ?
			************************************************************/
			$preview['mail_type'] = empty($preview_is_html) ? 'wp_mail' : 'phpmailer' ;
			$preview['content-type'] = empty($preview_is_html) ? 'text/plain' : 'text/html' ;

			/************************************************************
				parse all available
			************************************************************/
			$data = array();
			parse_str($_POST['vars']['data']['template_all_elements'], $data);
			$data_sort = $data[WPPIZZA_SLUG]['templates'][$preview_type][$preview_id];

			/************************************************************
				parse enabled and map
			************************************************************/
			$data = array();
			parse_str($_POST['vars']['data']['template_elements_enabled'], $data);
			$data_enabled = $data[WPPIZZA_SLUG]['templates'][$preview_type][$preview_id];

			/************************************************************
				parse styles and map
			************************************************************/
			$data = array();
			parse_str($_POST['vars']['data']['template_styles'], $data);
			$data_styles = $data[WPPIZZA_SLUG]['templates'][$preview_type][$preview_id];

			/************************************************************
				merge mapped vars (styles, enabled lables/parameters etc),
				recursively, adding to preview array

				i.e just like it will/would be saved in the options table
			************************************************************/
			$template_parameters = array_replace_recursive($data_enabled, $data_styles, $data_sort);

			/***********************************************************
				check if any sections are actually enabled
				otherwise we'll just display a message to that effect
			************************************************************/
			$sections_enabled = 0;
			foreach($template_parameters['sections'] as $section){
				if(!empty($section['section_enabled'])){
					$sections_enabled++;
					break;
				}
			}

			/******************************************
				let's check if we have anything selected
				to start off with. if not display that fact
			*****************************************/
			if(empty($sections_enabled)){

				$obj['error'] = "Error [ADM-102]: " . PHP_EOL . __('you did not select anything to display ?!','wppizza-admin'). PHP_EOL ;

			}else{

				/** get plaintext  */
				if(!$preview_is_html){
					$plaintext_data =  WPPIZZA()->templates_email_print->get_template_email_plaintext_sections_markup($order, $template_parameters, $preview_type);
					$preview['markup']['plaintext'] = $plaintext_data['markup'];
				}

				/** get html output */
				if($preview_is_html){
					$html_data =  WPPIZZA()->templates_email_print->get_template_email_html_sections_markup($order, $template_parameters, $preview_type, $preview_id);
					$preview['markup']['html'] = $html_data;
				}

			$obj=$preview;
			}

			print"".json_encode($obj)."";
			exit();
		}
		/*****************************************************
				[adding a new message/template]
		*****************************************************/
		if($_POST['vars']['field']=='add_template'){
			/**********set header********************/
			header('Content-type: application/json');
			/*emails or print ?*/
			$template_type = wppizza_validate_string($_POST['vars']['arrayKey']);

			/**get currently saved templates**/
			$templates_saved = get_option(WPPIZZA_SLUG.'_templates_'.$template_type, 0);

			/*
				get all current templates that already exist
				and determine highest key
			*/
			$nextKey=0;
			if(!empty($templates_saved)){
				$highestSetKey = max(array_keys($templates_saved));
				$nextKey=$highestSetKey + 1 + (int)$_POST['vars']['countNewKeys'];/*if we are adding more than one new one at the time, count them and add those to key id*/
			}

			$obj['markup'] = WPPIZZA()->templates_email_print->admin_template($nextKey, $template_type, false);

			print"".json_encode($obj)."";
			exit();
		}
	}

}
/***************************************************************
*
*	[ini]
*
***************************************************************/
$WPPIZZA_MODULE_TEMPLATES_TEMPLATES = new WPPIZZA_MODULE_TEMPLATES_TEMPLATES();
?>