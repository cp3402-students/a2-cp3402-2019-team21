<?php
/**
* WPPIZZA_MODULE_TOOLS_SYSINFO_PHPINFO Class
*
* @package     WPPIZZA
* @subpackage  WPPIZZA_MODULE_TOOLS_SYSINFO_PHPINFO
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
class WPPIZZA_MODULE_TOOLS_SYSINFO_PHPINFO{

	private $settings_page = 'tools';/* which admin subpage (identified there by this->class_key) are we adding this to */
	private $tab_key = 'sysinfo';/* must be unique within this admin page*/
	private $section_key = 'phpinfo';

	function __construct() {
		/**********************************************************
			[add settings to admin]
		***********************************************************/
		if(is_admin()){
			/*** add to a specific tab ***/
			add_filter('wppizza_filter_admin_tabs_'.$this->settings_page.'', array($this, 'admin_tabs'), 30);
			/* add admin options settings page*/
			add_filter('wppizza_filter_settings_sections_'.$this->settings_page.'', array($this, 'admin_options_settings'), 30, 5);
			/* add admin options settings page fields */
			add_action('wppizza_admin_settings_section_fields_'.$this->settings_page.'', array($this, 'admin_options_fields_settings'), 10, 5);
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

		/******************************************************
			[tools - get php info]
		******************************************************/
		if($_POST['vars']['field']=='get-php-vars'){

			/*
				general configuration
			*/
			ob_start();
			phpinfo(INFO_GENERAL);
			preg_match ('%<style type="text/css">(.*?)</style>.*?(<body>.*</body>)%s', ob_get_clean(), $matches);
			
			echo "<div class='phpinfodisplay'><style type='text/css'>\n",
			    join("\n",
			        array_map( array($this,'map_php_info'), preg_split( '/\n/', $matches[1] ) ) 
			    ),
			    "</style>\n",
			    $matches[2],
			    "\n</div>\n";


			/*
				info configuration
			*/
			ob_start();
			phpinfo(INFO_CONFIGURATION);
			preg_match ('%<style type="text/css">(.*?)</style>.*?(<body>.*</body>)%s', ob_get_clean(), $matches);
			
			echo "<div class='phpinfodisplay'><style type='text/css'>\n",
			    join("\n",
			        array_map( array($this,'map_php_info'), preg_split( '/\n/', $matches[1] ) ) 
			    ),
			    "</style>\n",
			    $matches[2],
			    "\n</div>\n";
		
			/*
				modules and module settings
			*/
			ob_start();
			phpinfo(INFO_MODULES);
			preg_match ('%<style type="text/css">(.*?)</style>.*?(<body>.*</body>)%s', ob_get_clean(), $matches);
			
			echo "<div class='phpinfodisplay'><style type='text/css'>\n",
			    join("\n",
			        array_map( array($this,'map_php_info'), preg_split( '/\n/', $matches[1] ) ) 
			    ),
			    "</style>\n",
			    $matches[2],
			    "\n</div>\n";
			exit();
		}
	}


	/*------------------------------------------------------------------------------
	#
	#
	#	[helpers]
	#
	#
	------------------------------------------------------------------------------*/
	function map_php_info($i){
		return ".phpinfodisplay " . preg_replace( "/,/", ",.phpinfodisplay ", $i );		
	}
	/*------------------------------------------------------------------------------
	#
	#
	#	[settings page]
	#
	#
	------------------------------------------------------------------------------*/
	/*********************************************************
			[add section to tab]
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
			$settings['sections'][$this->section_key] =  __('PHP Configuration', 'wppizza-locale');
		}

		/*help*/
		if($help){
		}

		/*fields*/
		if($fields){
			$field = 'php_config';
			$settings['fields'][$this->section_key][$field] = array('' , array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>'',
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
		if($field=='php_config'){
			echo "<h2><a href='javascript:void(0)' id='wppizza_show_php_vars' class='button'>".__('show php configuration', 'wppizza-locale')."</a></h2>";
			echo"<div id='wppizza_php_info'></div>";
		}
	}
}
/***************************************************************
*
*	[ini]
*
***************************************************************/
$WPPIZZA_MODULE_TOOLS_SYSINFO_PHPINFO = new WPPIZZA_MODULE_TOOLS_SYSINFO_PHPINFO();
?>