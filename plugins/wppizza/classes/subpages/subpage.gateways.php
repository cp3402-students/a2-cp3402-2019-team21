<?php
/**
* WPPIZZA_MANAGE_GATEWAYS Class
*
* @package     WPPIZZA
* @subpackage  Classes / Admin-Subpages / Manage Gateways
* @copyright   Copyright (c) 2015, Oliver Bach
* @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
* @since       3.0
*
*/
if ( ! defined( 'ABSPATH' ) ) exit;/*Exit if accessed directly*/


/************************************************************************************************************************
*
*
*	WPPIZZA_MANAGE_GATEWAYS to GATEWAYS subpages
*
*
************************************************************************************************************************/
class WPPIZZA_MANAGE_GATEWAYS{

	/*
	* class ident
	* @var str
	* @since 3.0
	*/
	private $class_key='gateways';/*to help consistency throughout class in various places*/

	/*
	* class gatyeways registered
	* @var str
	* @since 3.0
	*/
	private $gateways;

	/*
	* titles/lables
	* @var str
	* @since 3.0
	*/
	private $submenu_page_header;
	private $submenu_page_title;
	private $submenu_caps_title;
	private $submenu_link_label;
	private $submenu_priority = 40;
	function __construct() {

		/*titles/labels throughout class*/
		add_action('init', array( $this, 'init_admin_lables') );

		/** registering submenu page -> priority 40 **/
		add_action('admin_menu', array( $this, 'wppizza_register_submenu_page'), $this->submenu_priority );

		/*register capabilities for this page*/
		add_filter('wppizza_filter_define_caps', array( $this, 'wppizza_filter_define_caps'), $this->submenu_priority);

		/*execute some helper functions once to use their return multiple times */
		add_action('current_screen', array( $this, 'wppizza_add_helpers') );

		/**validate options**/
		add_filter('wppizza_filter_options_validate', array( $this, 'options_validate'), 10, 2 );

		/**load admin ajax file**/
		add_action('wp_ajax_wppizza_admin_'.$this->class_key.'_ajax', array($this, 'set_admin_ajax') );
	}

	/******************
	*	@since 3.0.26
    *	[admin ajax include file]
    *******************/
	public function init_admin_lables(){
		$this->submenu_page_header	=	apply_filters('wppizza_filter_admin_label_page_header_'.$this->class_key.'', __('Gateways','wppizza-admin'));
		$this->submenu_page_title	=	apply_filters('wppizza_filter_admin_label_page_title_'.$this->class_key.'', __('Manage Gateways','wppizza-admin'));
		$this->submenu_caps_title	=	apply_filters('wppizza_filter_admin_label_caps_title_'.$this->class_key.'', __('Gateways','wppizza-admin'));
		$this->submenu_link_label	=	apply_filters('wppizza_filter_admin_label_link_label_'.$this->class_key.'', __('&middot; Gateways','wppizza-admin'));
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
	*	[add helpers]
	*	@since 3.0
	*
	* 	run on this page only or if saving this page
		($_POST[WPPIZZA_SLUG.'_'.$this->class_key])
	*********************************************************/
	function wppizza_add_helpers($current_screen){

		if($current_screen->id == 'options' && isset($_POST[''.WPPIZZA_POST_TYPE.'_'.$this->class_key.''])){
			/** return capabilities when saving options **/
			add_filter( 'option_page_capability_'.WPPIZZA_SLUG.'', array($this, 'admin_option_page_capability' ));
		}

		if( !empty($_POST[WPPIZZA_SLUG.'_'.$this->class_key]) || ($current_screen->id == ''.WPPIZZA_POST_TYPE.'_page_'.$this->class_key.'' && $current_screen->post_type == WPPIZZA_POST_TYPE)){

			/**add registered gateways to class object**/
			$this->gateways=WPPIZZA()->register_gateways;

			if($current_screen->id!='options'){
				/**admin settings sections**/
				$this->wppizza_admin_settings_sections();
			}

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
    	wp_register_script(WPPIZZA_SLUG.'_'.$this->class_key.'', plugins_url( 'js/scripts.admin.'.$this->class_key.'.js', WPPIZZA_PLUGIN_PATH ), array('jquery'), WPPIZZA_VERSION ,true);
    	wp_enqueue_script(WPPIZZA_SLUG.'_'.$this->class_key.'');
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

		/**ini setiings array**/
		$settings=array();
		/**gateway count**/
		$gateway_count=count((array)$this->gateways->registered_gateways);

		/**sort gateway display order by sort flag **/
		$registered_gatewy_sort=array();
		foreach($this->gateways->registered_gateways as $reg_gateway_ident=>$reg_gateway_values){
			$sort=$reg_gateway_values->gatewayOptions['_gateway_sortorder'];
			$registered_gatewy_sort[$reg_gateway_ident]=$sort;
		}
		asort($registered_gatewy_sort);


		/********************************
		*	[include sorted gateways as sections]
		********************************/
		foreach($registered_gatewy_sort as $ident=>$sort){

			/**get whole gateway object*/
			$gateway_object=$this->gateways->registered_gateways->$ident;

			/*sections*/
			if($sections){
				$args=array();
				$args['ident']=$gateway_object->gatewayIdent;
				$args['option_name']=$gateway_object->gatewayOptionName;
				$args['addinfo']=$gateway_object->gatewayAdditionalInfo;
				$args['admininfo']=!empty($gateway_object->gatewayDescription) ? $gateway_object->gatewayDescription : '' ;

				/*section(gateway) parameters*/
				$settings['sections'][$gateway_object->gatewayIdent] = array('ident'=>$gateway_object->gatewayIdent, 'name'=>$gateway_object->gatewayName, 'arg'=>$args );

			}
			/*fields*/
			if($fields){
				foreach($gateway_object->gatewaySettings as $options_key=>$options_value){
					/**if settings section, add to args**/
					$args['gateway_name']=$gateway_object->gatewayName;
					$args['gateway_options_name']=$gateway_object->gatewayOptionName;
					$args['option_key']=$options_key;
					$args['settings']=!empty($gateway_object->gatewaySettings[$options_key]) ? $gateway_object->gatewaySettings[$options_key] : array();
					$args['value']=$options_value;
					$args['gateway_count']=$gateway_count;
					/**settings**/
					$settings['fields'][$gateway_object->gatewayIdent][$options_key] = array('options_key'=>$options_key, 'label'=>'', 'section'=>$gateway_object->gatewayIdent, 'args'=>$args);
				}
			}
		}


		/**
			allow filtering of settings sections
			do add additional if required
		**/
		//$settings=apply_filters('wppizza_filter_settings_sections_'.$this->class_key.'', $settings, $sections, $fields, $inputs, $help);

		return $settings;
	}


	/****************************************************************
	*
	*	[validate options on save/update]
	*	@since 3.0
	*	@return array()
	*
	****************************************************************/
	function options_validate($options, $input){

		/**just get the full array on install/update**/
		if ( empty( $_POST['_wp_http_referer'] ) ) {
			return $input;
		}

		if(isset($_POST[''.WPPIZZA_SLUG.'_'.$this->class_key.''])){


			/*ini array*/
			$gateway_options=array();

			/**ini array for main wppizza options value**/
			$wppizza_options_gateways=array();

			/***
				loop through gateways and validate options
			***/
			foreach($this->gateways->registered_gateways as $gateway_ident=>$gateway_values){

				/**db option name**/
				$gateway_db_option_name=$gateway_values->gatewayOptionName;
				/*get currently set options*/
				$gateway_options[$gateway_db_option_name]=array();//=get_option($gateway_db_option_name,0);

				/**distinctly save version no*/
				$gateway_options[$gateway_db_option_name]['_gateway_version'] 				= $gateway_values->gatewayVersion;
				/**distinctly save enabled*/
				$gateway_options[$gateway_db_option_name]['_gateway_enabled'] 				= !empty($input['gateways'][$gateway_db_option_name]['_gateway_enabled']) ? true : false;

				/**
					[validate individual settings for this gateway]
				**/
				$gateway_settings=$gateway_values->gatewaySettings;

				foreach($gateway_settings as $option_key=>$option_settings_values){


					$to_validate=!empty($input['gateways'][$gateway_db_option_name][$option_key]) ? $input['gateways'][$gateway_db_option_name][$option_key] : '' ;

					/**
						callback defined
					**/
					if(!empty($option_settings_values['validateCallback'])){

						/**
							callback is a simple function name,
							not an array
						**/
						if(!is_array($option_settings_values['validateCallback'])){
							$val=$option_settings_values['validateCallback']($to_validate);
						}



						/**
							callback is an array using inbuilt
							wppizza validation function (first parameter being a string / the function name)
						**/
						if(is_array($option_settings_values['validateCallback']) && is_string($option_settings_values['validateCallback'][0])){
							/**the number of parameters passed could be done more intelligently, but up to 3 will do for now**/
							if(count($option_settings_values['validateCallback'])==2){
								$val=$option_settings_values['validateCallback'][0]($to_validate, $option_settings_values['validateCallback'][1]);
							}
							if(count($option_settings_values['validateCallback'])==3){
								$val=$option_settings_values['validateCallback'][0]($to_validate, $option_settings_values['validateCallback'][1], $option_settings_values['validateCallback'][2]);
							}
							if(count($option_settings_values['validateCallback'])==4){
								$val=$option_settings_values['validateCallback'][0]($to_validate, $option_settings_values['validateCallback'][1], $option_settings_values['validateCallback'][2], $option_settings_values['validateCallback'][3]);
							}

						}

						/**
							callback is an array using classes own validation function
							(first parameter being an array like array(__CLASS__,'function_name'), second being an array of passed parameters)
						**/
						if(is_array($option_settings_values['validateCallback']) && is_array($option_settings_values['validateCallback'][0])){
							$parameters=!empty($option_settings_values['validateCallback'][1]) ? $option_settings_values['validateCallback'][1] : null;
							$class = new $option_settings_values['validateCallback'][0][0];
							$method = $option_settings_values['validateCallback'][0][1];
							$val = $class -> $method($parameters);
						}



					}
					/**
						no callback defined, just insert input or false if not set
					**/
					if(empty($option_settings_values['validateCallback'])){
						$val=!empty($to_validate) ? esc_sql($to_validate) : false;
					}

					/**
						do not allow empty label, but use gatewayName if empty
					**/
					if($option_key == '_gateway_label' && trim($val)==''){
						$val = $gateway_values->gatewayName;
					}

					/***add to array**/
					$gateway_options[$gateway_db_option_name][$option_key] = $val;//$val;
				}


				/*filter*/
				$gateway_options[$gateway_db_option_name]=apply_filters('wppizza_filter_gateway_options_validate_'.$gateway_ident.'', $gateway_options[$gateway_db_option_name]);

				/*********
					[add/set options for main wppizza option value if enabled]
				*********/
				if($gateway_options[$gateway_db_option_name]['_gateway_enabled']){
					$wppizza_options_gateways[$gateway_ident]=array('sort'=>$gateway_options[$gateway_db_option_name]['_gateway_sortorder'], 'ident'=>$gateway_ident);
				}

			}
			/***
				update options table for each gateway
			***/
			foreach($gateway_options as $gateway_options_db_name=>$gateway_options_db_values){
				/*update option table values for this gateway**/
				update_option($gateway_options_db_name, $gateway_options_db_values);
			}
			/******************************************
		 		update main wppizza options
		 		adding enabled gateways to $options returned
			******************************************/
			/**sort by sort order set and save**/
			asort($wppizza_options_gateways);
			$options['gateways']=$wppizza_options_gateways;
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
	}
	/*********************************************************
	*
	*	[add settings section(s) and fields]
	*
	*	@since 3.0
	*	@return void
	*
	*********************************************************/
	public function wppizza_admin_settings_sections(){
		/** get settings sections and fields **/
		$settings=$this->wppizza_get_settings(true, true);
		/**iterate through sections*/
		foreach($settings['sections'] as $sections_key=>$sections_values){
			/**add section**/
			add_settings_section($sections_values['ident'], '', array( $this, 'wppizza_admin_settings_section_header'),  $sections_values['ident']);
			/**add section fields**/
			foreach($settings['fields'][$sections_key] as $fields_key=>$field_values){
				add_settings_field($field_values['options_key'], $field_values['label'], array( $this, 'wppizza_admin_settings_section_fields'), $field_values['section'], $field_values['section'], $field_values['args']);
			}
		}
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
		global $wppizza_options;

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
				echo"<span id='".WPPIZZA_SLUG."-header'>".WPPIZZA_NAME." ".$this->submenu_page_header."</span>";
			echo"</h2>";

		echo"</div>";

		/**update info / errors etc*/
		settings_errors();


		echo'<form action="options.php" method="post">';
		echo'<input type="hidden" name="'.WPPIZZA_SLUG.'_'.$this->class_key.'" value="1" />';

			/**echo wppizza settings field*/
			settings_fields(WPPIZZA_SLUG);


			/**echo settings sections**/
			foreach($settings['sections'] as $sections_key => $sections_values){

				echo'<div id="wppizza-section-gateways-'.$sections_key.'" class="wppizza-section-gateways button">';
					/**label (gateway name)**/
					print'<span class="wppizza-gateway-label">'.$sections_values['name'].'</span>';

					/*show options button */
					print'<span id="wppizza-gateway-show-options-'.$sections_key.'" class="wppizza-gateway-show-options button">'.__('show options','wppizza-admin').'</span>';

					/**enable button*/ // '.checked(!empty($wppizza_options['gateways'][$sections_key]),true,false).'
					print'<label class="wppizza-gateway-enable button"><input name="'.WPPIZZA_SLUG.'[gateways]['.$sections_values['arg']['option_name'].'][_gateway_enabled]" type="checkbox" '.checked(!empty($wppizza_options['gateways'][$sections_key]),true,false).' value="1">'.__('enabled Y/N','wppizza-admin').'</label>';

				echo'</div>';


				echo'<div id="wppizza-fields-gateways-'.$sections_key.'" class="wppizza-fields-gateways">';
				if(!empty($sections_values['arg']['admininfo'])){
					print'<div class="wppizza-fields-gateways-addinfo">'.$sections_values['arg']['admininfo'].'</div>';
				}

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
	*		[echo setting section(s) fields]
	*
	*********************************************************/
	public function wppizza_admin_settings_section_fields($args){
		/** wppizza options set **/
		global $wppizza_options;


		/**option key**/
		$gateway_name=$args['gateway_name'];
		/**option key**/
		$option_key=$args['option_key'];
		/*gateway_options_name***/
		$gateway_options_name=$args['gateway_options_name'];
		/**number of gateways **/
		$gateway_count=$args['gateway_count'];
		/**settings**/
		$settings=$args['settings'];
		/**value**/
		$value=$args['value'];

		/**
			gateway specific variables
		**/
		if(count($settings)>0){
			echo"<label style='float:left;width:17%'>";
				echo"".$settings['label']." ";
			echo"</label>";

			echo"<div style='float:right;width:82%;'>";
			/*
				normal selects ?
			*/
			//$selected=!empty($settings['selected']) ? $settings['selected'] : '' ;
			/*
				checkboxes / radio
			*/
			$selected = '' ;/* ini for non check/radio */
			if($settings['type'] == 'checkbox' || $settings['type'] == 'radio'){
				$selected=!empty($settings['selected']) ? 'checked="checked"' : '' ;
			}
			//if($settings['type'] == 'select'){
			//	$selected=!empty($settings['selected']) ? 'selected="selected"' : '' ;
			//}


			$this->wppizza_echo_gateway_formfield($settings['type'], $settings['key'], WPPIZZA_SLUG."[gateways][".$gateway_options_name."][".$settings['key']."]",$settings['value'], $settings['placeholder'], $settings['options'], $selected);

			/*description if set**/
			if(!empty($settings['descr'])){
				echo"<span class='description'>";
					echo"".$settings['descr']."";
				echo"</span>";
			}
		echo"</div>";

		}
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
	/*********************************************************
	*
	*	[helpers]
	*
	*********************************************************/
	/****************************************************************************
		[output gateway formfields depending on type]
	*	@since 3.0
	****************************************************************************/
	function wppizza_echo_gateway_formfield($type='text', $id='', $name='', $value='', $placeholder='' , $options='', $selected=''){

		/*
			output text / email / text with set size
		*/
		if($type=='text' || $type=='email' || substr($type,0,10)=='text_size_'){

			$input_field_type= ($type=='email') ? 'email' : 'text';
			$set_size = !empty($type) ? (int)substr($type, 10) : 0 ;
			$input_field_size=!empty($set_size) ? (int)substr($type, 10) : 40;

			echo'<input type="'.$input_field_type.'" class="'.$id.'" name="'.$name.'" value="'.$value.'"  size="'.$input_field_size.'"  placeholder="'.$placeholder.'" />';
		}

		/*
			output checkbox
		*/
		if($type=='checkbox'){
			echo'<input type="checkbox" class="'.$id.'" name="'.$name.'" value="'.$value.'" '.$selected.' />';
		}

		/*
			output radio button(s)
		*/
		if($type=='radio'){
			if(is_array($options)){
				$i=0;
				foreach($options as $key=>$val){
					echo'<input type="radio" id="'.$key.'_'.$i.'" name="'.$key.'" value="'.$val.'" '.checked(is_array($selected) && in_array($val,$selected),true,false).'/>';
				$i++;
				}
			}else{
				echo'<input type="rdo" id="'.$id.'" name="'.$name.'" value="'.$value.'" '.$selected.'/>';
			}
		}

		/*
			output multiple checkboxes
		*/
		if($type=='checkboxmulti'){
			if(isset($options) && is_array($options)){
			foreach($options as $k=>$v){
				echo'<span style="padding-right:10px;"><input type="checkbox" id="'.$id.'_'.$k.'" name="'.$name.'['.$k.']" value="'.$k.'" '.checked(in_array($k,$value),true,false).'/>';
					echo''.$v.'';
				echo'</span>';
			}}
		}

		/*
			output textarea
		*/
		if($type=='textarea'){
			echo'<textarea class="'.$id.'" name="'.$name.'">'.$value.'</textarea>';
		}

		/*
			output textarea html editor
		*/
		if($type=='texteditor'){
			$id=strtolower(str_replace(array('[',']'),'_',$name));/* WP 3.9 doesnt like brackets in id's*/
			echo'<div class="'.WPPIZZA_SLUG.'-texteditor">';
			wp_editor( $value, $id , array('teeny'=>1,'wpautop'=>false,'media_buttons'=>false,'textarea_name'=>$name) );
			echo'</div>';
		}

		/*
			output select/dropdown
		*/
		if($type=='select'){
			echo'<select id="'.$id.'" name="'.$name.'" >';
			foreach($options as $selValue=>$selLabel){
				echo'<option value="'.$selValue.'" '.selected((!empty($value) && $value == $selValue), true ,false).' >'.$selLabel.'</option>';
			}
			echo'</select>';
		}

		/*
			output multi select/dropdown
		*/
		if($type=='selectmulti'){
			echo'<select id="'.$id.'" name="'.$name.'[]" multiple="multiple">';
			asort($options);
			foreach($options as $selValue=>$selLabel){
				echo'<option value="'.$selValue.'" '.selected((!empty($value) &&  in_array($selValue, $value) ), true ,false).' >'.$selLabel.'</option>';
			}
			echo'</select>';
		}


		/*
			output formfields dropdown
		*/
		if($type=='formfields' && !empty($options) && is_array($options)){

			/** get enabled order form fields **/
			$enabled_formfields = WPPIZZA() -> admin_helper -> admin_orderform_enabled_formfields('gateways');

			/*
				echo dropdown
			*/
			echo'<span class="'.WPPIZZA_SLUG.'-formfields-table-span"><table class="'.WPPIZZA_SLUG.'-formfields-table">';

			foreach($enabled_formfields as $effKey => $effArr){

				echo'<tr>';

					echo'<td>'.$effArr['lbl'].'</td>';

					echo'<td>';

						echo'<select name="'.$name.'['.$effKey.']">';

							foreach($options as $oValue => $oLabel){
								echo'<option value="'.$oValue.'" '.selected((!empty($value[$effKey]) && $value[$effKey] == $oValue), true ,false).' >'.$oLabel.'</option>';
							}

						echo'</select>';

					echo'</td>';

				echo'</tr>';

			}
			echo'</table></span>';
		}

		/*
			set to custom, just output
			what's set in options
		*/
		if($type=='custom'){
			echo''.$options;
		}
	}

}
/***************************************************************
*
*	[ini]
*
***************************************************************/
$WPPIZZA_MANAGE_GATEWAYS = new WPPIZZA_MANAGE_GATEWAYS();
?>