<?php
/**
* WPPIZZA_MODULE_ORDERFORM_ORDERPAGE Class
*
* @package     WPPIZZA
* @subpackage  WPPIZZA_MODULE_ORDERFORM_ORDERPAGE
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
class WPPIZZA_MODULE_ORDERFORM_ORDERPAGE{

	private $settings_page = 'order_form';/* which admin subpage (identified there by this->class_key) are we adding this to */
	private $section_key = 'order_form';/* must be unique */


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

		/*sections*/
		if($sections){
			$settings['sections'][$this->section_key] = __('Order Page', 'wppizza-admin');
		}
		/*fields*/
		if($fields){
			$field = 'order_form';
			$settings['fields'][$this->section_key][$field] = array('' , array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>'',
				'description'=>array()
			));
		}
		/*help*/
		if($help){
			$settings['help'][$this->section_key][] = array(
				'label'=>__('Order Page - Formfields', 'wppizza-admin'),
				'description'=>array(
					__('Set the form fields you would like to show when a customer places an order', 'wppizza-admin'),
					__('You can add html to the label if using type "checkbox".', 'wppizza-admin'),
					__('Note: only the email can and should be used to send email notifications of the order to the customer (if enabled).', 'wppizza-admin'),
					__('Additionally, if you want to offer customers to be able to register a new account on the order page - provided they are not logged in already - the email field must be set to be "enabled" and "required". Furthermore "Anyone can register" in "Settings->General" has to be enabled too.', 'wppizza-admin')
				)
			);
		}


		return $settings;
	}
	/*------------------------------------------------------------------------------
	#	[output option fields - setting page]
	#	@since 3.0
	#	@return array()
	------------------------------------------------------------------------------*/
	function admin_options_fields_settings($wppizza_options, $options_key, $field, $label, $description){

		if($field=='order_form'){

			$rules_available = $this->get_validation_rules();

			$input_fields = apply_filters('wppizza_filter_register_orderform_formfields',$wppizza_options[$options_key]);
			
			/**sort by sort**/
			asort($input_fields);

			echo"<table id='wppizza_admin_table_".$options_key."' class='wppizza_admin_table'>";
				/*header*/
				echo"<thead>";
				echo"<tr>";
					echo"<th>".__('Enabled', 'wppizza-admin')."</th>";
					echo"<th>".__('Sort', 'wppizza-admin')."</th>";
					echo"<th>".__('Label', 'wppizza-admin')."&nbsp;/<br />".__('Placeholder', 'wppizza-admin')."</th>";
					echo"<th>".__('Required:', 'wppizza-admin')."</th>";
					echo"<th>".__('Prefill<br />[if known]', 'wppizza-admin')."</th>";
					echo"<th>".__('Use when<br />Registering ?', 'wppizza-admin')."</th>";
					echo"<th>".__('add to email<br />subject line ?', 'wppizza-admin')."</th>";
					echo"<th>".__('Type', 'wppizza-admin')."&nbsp;/<br />".__('Validation', 'wppizza-admin')."</th>";
				echo"</tr>";
				echo"</thead>";

			/*each field*/
			echo"<tbody>";
			foreach($input_fields as $k=>$v){
				/**
					set some conditionals according to input
				**/
	
				$attr = array();
				$attr['enabled'] = true;
				$attr['sort'] = true;
				$attr['label'] = true;
				$attr['placeholder'] = true;
				$attr['required'] = true;
				$attr['required_on_pickup'] = true;
				$attr['prefill'] = true;
				$attr['onregister'] = true;
				$attr['add_to_subject_line'] = true;
				$attr['validation_rules'] = true;

				
				
				/* 
					allow filtering of attributed by key/type 
				*/	
				$attr = apply_filters('wppizza_filter_register_orderform_formfields_column_disabled_'.$k.'',$attr, $v['type']);								
								

				if($v['key']=='cemail'){
					$attr['onregister'] = false;
					$attr['fixedType'] = 'email';
					$attr['validation_rules'] = false;
				}
				if($v['key']=='ctips'){
					$attr['onregister'] = false;
					$attr['prefill'] = false;
					$attr['fixedType'] = 'tips';
					$attr['validation_rules'] = false;
					$attr['add_to_subject_line'] = false;
				}


				/**each option in field**/
				echo"<tr class='wppizza_".$v['key']."'>";

					/*enabled - label for mobile*/
					echo"<td class='".WPPIZZA_SLUG."_tr_label_mobile'>";
						echo"".$v['lbl']."";
					echo"</td>";

					/*enabled*/
					echo"<td>";
						if(isset($attr['fixedEnabled'])){
							echo "".$attr['fixedEnabled']."";
						}else{
							echo"<span class='".WPPIZZA_SLUG."_td_label_mobile'>".__('Enabled', 'wppizza-admin')."</span>";
							echo"<span class='button' title='ID : ".$v['key']."'><input name='".WPPIZZA_SLUG."[".$options_key."][".$k."][enabled]' type='checkbox' ". checked($v['enabled'],true,false)." value='1' /></span>";
						}
					echo"</td>";


					/*sort*/
					echo"<td>";
						echo"<span class='".WPPIZZA_SLUG."_td_label_mobile'>".__('Sort', 'wppizza-admin')."</span>";
						if($attr['sort']){echo"<input name='".WPPIZZA_SLUG."[".$options_key."][".$k."][sort]' size='1' type='text' value='".$v['sort']."' />";}else{echo"".__('N/A', 'wppizza-admin')."";}
					echo"</td>";


					/*label / placeholder*/
					echo"<td>";
						echo"<span class='".WPPIZZA_SLUG."_td_label_mobile'>".__('Label / Placeholder', 'wppizza-admin')."</span>";
						if($attr['label']){echo"<input name='".WPPIZZA_SLUG."[".$options_key."][".$k."][lbl]' size='15' type='text' value='".$v['lbl']."' placeholder='".__('Label', 'wppizza-admin')."' />";}else{echo"".__('N/A', 'wppizza-admin')."";}
						if($attr['placeholder']){echo"<input name='".WPPIZZA_SLUG."[".$options_key."][".$k."][placeholder]' size='15' type='text' value='".$v['placeholder']."' placeholder='".__('Placeholder', 'wppizza-admin')."' />";}
					echo"</td>";


					/*required - delivery / pickup */
					echo"<td>";
						echo"<span class='".WPPIZZA_SLUG."_td_label_mobile'>".__('Required', 'wppizza-admin')."</span>";
						/* required delivery */
						if($attr['required']){echo"<label><input name='".WPPIZZA_SLUG."[".$options_key."][".$k."][required]' type='checkbox' ". checked($v['required'],true,false)." value='1' />".__('on Delivery', 'wppizza-admin')."</label>";}else{echo"".__('N/A', 'wppizza-admin')."<br />";}
						/* required pickup */
						if($attr['required_on_pickup']){echo"<label><input name='".WPPIZZA_SLUG."[".$options_key."][".$k."][required_on_pickup]' type='checkbox' ". checked($v['required_on_pickup'],true,false)." value='1' />".__('on Pickup', 'wppizza-admin')."</label>";}else{echo"".__('N/A', 'wppizza-admin')."";}
					echo"</td>";


					/*prefill*/
					echo"<td>";
						echo"<span class='".WPPIZZA_SLUG."_td_label_mobile'>".__('Prefill', 'wppizza-admin')."</span>";
						if($attr['prefill']){echo"<input name='".WPPIZZA_SLUG."[".$options_key."][".$k."][prefill]' type='checkbox' ". checked($v['prefill'],true,false)." value='1' />";}else{echo"".__('N/A', 'wppizza-admin')."";}
					echo"</td>";


					/*add to registration*/
					echo"<td>";
						echo"<span class='".WPPIZZA_SLUG."_td_label_mobile'>".__('On register', 'wppizza-admin')."</span>";
						if($attr['onregister']){echo"<input name='".WPPIZZA_SLUG."[".$options_key."][".$k."][onregister]' type='checkbox' ". checked($v['onregister'],true,false)." value='1' />";}else{echo"".__('N/A', 'wppizza-admin')."";}
					echo"</td>";


					/*add to email subject line*/
					echo"<td>";
						echo"<span class='".WPPIZZA_SLUG."_td_label_mobile'>".__('in subject line', 'wppizza-admin')."</span>";
						if($attr['add_to_subject_line']){echo"<input name='".WPPIZZA_SLUG."[".$options_key."][".$k."][add_to_subject_line]' type='checkbox' ". checked($v['add_to_subject_line'],true,false)." value='1' />";}else{echo"".__('N/A', 'wppizza-admin')."";}
					echo"</td>";


					/*formfield type*/
					echo"<td>";
						echo"<span class='".WPPIZZA_SLUG."_td_label_mobile'>".__('Type / Validation', 'wppizza-admin')."</span>";
						/**non editable just show label and use hidden input**/
						if(isset($attr['fixedType'])){
							echo "".$attr['fixedType']."";
							echo "<input type='hidden' name='".WPPIZZA_SLUG."[".$options_key."][".$k."][type]' value='".$v['type']."'/>";
						}else{
							echo "<select id='".WPPIZZA_SLUG."_".$options_key."_type_".$k."' class='".WPPIZZA_SLUG."_".$options_key."_type' name='".WPPIZZA_SLUG."[".$options_key."][".$k."][type]' />";
								echo'<option value="text" '.selected($v['type'],"text",false).'>text</option>';
								echo'<option value="email" '.selected($v['type'],"email",false).'>email</option>';
								echo'<option value="textarea" '.selected($v['type'],"textarea",false).'>textarea</option>';
								echo'<option value="select" '.selected($v['type'],"select",false).'>select</option>';
								echo'<option value="radio" '.selected($v['type'],"radio",false).'>radio</option>';
								echo'<option value="checkbox" '.selected($v['type'],"checkbox",false).'>checkbox</option>';
								echo'<option value="multicheckbox" '.selected($v['type'],"multicheckbox",false).'>multicheckbox</option>';
							echo "</select>";
						}

						/**if select, add input field**/
						/*conditional defaults*/
						$display=' style="display:none"';
						$val='';
						/**select*/
						if($v['type']=='select' || $v['type']=='radio' || $v['type']=='multicheckbox' ){
							$display='';
							$val=''.implode(",",$v['value']).'';
						}

						echo "<span class='".WPPIZZA_SLUG."_".$options_key."_select' ".$display.">";
							echo "<input name='".WPPIZZA_SLUG."[".$options_key."][".$k."][value]' type='text' value='".$val."' />";
						echo "</span>";
						echo "<span class='".WPPIZZA_SLUG."_".$options_key."_select' ".$display.">";
								echo "<span class='description'>".__('separate options with comma', 'wppizza-admin')."</span>";
						echo "</span>";


						/* validation type / pattern */
						if($attr['validation_rules']){

							/*allow for multiple validation rules if WPPIZZA_ADMIN_FORMFIELDS_VALIDATION_MULTISELECT == true*/
							$constant_wppizza_validation_multiselect = WPPIZZA_ADMIN_FORMFIELDS_VALIDATION_MULTISELECT;/* cast to var for php 5.3 */
							$multiple_rules = !empty($constant_wppizza_validation_multiselect) ? ' multiple="multiple"' : '';

							echo"<label><select id='".WPPIZZA_SLUG."-".$options_key."-validation_rules-".$k."'  class='".WPPIZZA_SLUG."-".$options_key."-validation_rules'   name='".WPPIZZA_SLUG."[".$options_key."][".$k."][validation][rule][]' ".$multiple_rules.">";
							foreach($this->get_validation_rules() as $validation_key=>$rule){
								$option_has_parameters = (!empty($rule['parameters'])) ? '-hasparameters' : '';
								echo'<option value="'. $validation_key . $option_has_parameters .'" '.selected(isset($v['validation'][$validation_key]),true,false).'>'.$rule['lbl'].'</option>';
							}
							echo"</select></label>";

							/* rules parameters for rules that require them */
							echo "<span id='".WPPIZZA_SLUG."_validation_parameters-".$k."' class='".WPPIZZA_SLUG."_validation_parameters' >";
									foreach($v['validation'] as $rule_key=>$rule_value){
										/* only for rules that have / need parameters set */
										if($rules_available[$rule_key]['parameters']){
										//echo"<span>".$rules_available[$rule_key]['lbl'].":</span>";
										echo"<input name='".WPPIZZA_SLUG."[".$options_key."][".$k."][validation][parameters][".$rule_key."]'  type='text' value='".$rule_value."' placeholder='".__('Parameters', 'wppizza-admin')."' />";
										}
									}
							echo "</span>";

						}
					echo"</td>";

				echo"</tr>";

				/**for tips, add description tr - kind of superflous and messes up odd/even css**/
				//if($v['key']=='ctips'){
				//	echo"<tr class='wppizza_".$v['key']."_info'>";
				//		echo"<td colspan='9'>";
				//			echo"<span class='description'>";
				//				echo"".__('<b>Tips/Gratuities:</b> allow the customer can enter a <b>numerical</b> amount to be used as tips/gratuities.<br />This field will not be added to the users profile and can therefore not be pre-filled or used in the registration form.', 'wppizza-admin')."";
				//			echo"</span>";
				//		echo"</td>";
				//	echo"</tr>";
				//}

			}
			echo"</tbody>";
			echo"</table>";
		}

	}
	/*------------------------------------------------------------------------------
	#	[insert default option on install]
	#	$parameter $options array() | filter passing on filtered options
	#	@since 3.0
	#	@return array()
	------------------------------------------------------------------------------*/
	function options_default($options){

		$options[$this->settings_page]=array(
			'cname'=>array(
				'sort'=>0,'key'=>'cname','lbl'=>esc_html__('Name :', 'wppizza'),'value'=>array(),'type'=>'text','enabled'=>true,'required'=>false,'required_on_pickup'=>false,'prefill'=>true,'onregister'=>false,'add_to_subject_line'=>true, 'placeholder'=>false, 'validation'=>array('default'=>true)
			),
			'cemail'=>array(
				'sort'=>1,'key'=>'cemail','lbl'=>esc_html__('Email :', 'wppizza'),'value'=>array(),'type'=>'email','enabled'=>true,'required'=>true,'required_on_pickup'=>true,'prefill'=>true,'onregister'=>false,'add_to_subject_line'=>false, 'placeholder'=>false, 'validation'=>array('email'=>true)
			),
			'caddress'=>array(
				'sort'=>2,'key'=>'caddress','lbl'=>esc_html__('Address :', 'wppizza'),'value'=>array(),'type'=>'textarea','enabled'=>true,'required'=>true,'required_on_pickup'=>false,'prefill'=>true,'onregister'=>false,'add_to_subject_line'=>false, 'placeholder'=>false, 'validation'=>array('default'=>true)
			),
			'ctel'=>array(
				'sort'=>3,'key'=>'ctel','lbl'=>esc_html__('Telephone :', 'wppizza'),'value'=>array(),'type'=>'text','enabled'=>true,'required'=>true,'required_on_pickup'=>true,'prefill'=>true,'onregister'=>false,'add_to_subject_line'=>false, 'placeholder'=>false, 'validation'=>array('number'=>true)
			),
			'ccomments'=>array(
				'sort'=>4,'key'=>'ccomments','lbl'=>esc_html__('Comments :', 'wppizza'),'value'=>array(),'type'=>'textarea','enabled'=>true,'required'=>false,'required_on_pickup'=>false,'prefill'=>false,'onregister'=>false,'add_to_subject_line'=>false, 'placeholder'=>false, 'validation'=>array('default'=>true)
			),
			'ccustom1'=>array(
				'sort'=>5,'key'=>'ccustom1','lbl'=>esc_html__('Custom Field 1 :', 'wppizza'),'value'=>array(),'type'=>'text','enabled'=>false,'required'=>false,'required_on_pickup'=>false,'prefill'=>false,'onregister'=>false,'add_to_subject_line'=>false, 'placeholder'=>false, 'validation'=>array('default'=>true)
			),
			'ccustom2'=>array(
				'sort'=>6,'key'=>'ccustom2','lbl'=>esc_html__('Custom Field 2 :', 'wppizza'),'value'=>array(),'type'=>'text','enabled'=>false,'required'=>false,'required_on_pickup'=>false,'prefill'=>false,'onregister'=>false,'add_to_subject_line'=>false, 'placeholder'=>false, 'validation'=>array('default'=>true)
			),
			'ccustom3'=>array(
				'sort'=>7,'key'=>'ccustom3','lbl'=>esc_html__('Custom Field 3 :', 'wppizza'),'value'=>array(),'type'=>'text','enabled'=>false,'required'=>false,'required_on_pickup'=>false,'prefill'=>false,'onregister'=>false,'add_to_subject_line'=>false, 'placeholder'=>false, 'validation'=>array('default'=>true)
			),
			'ccustom4'=>array(
				'sort'=>8,'key'=>'ccustom4','lbl'=>esc_html__('Custom Field 4 :', 'wppizza'),'value'=>array(),'type'=>'text','enabled'=>false,'required'=>false,'required_on_pickup'=>false,'prefill'=>false,'onregister'=>false,'add_to_subject_line'=>false, 'placeholder'=>false, 'validation'=>array('default'=>true)
			),
			'ccustom5'=>array(
				'sort'=>9,'key'=>'ccustom5','lbl'=>esc_html__('Custom Field 5 :', 'wppizza'),'value'=>array(),'type'=>'text','enabled'=>false,'required'=>false,'required_on_pickup'=>false,'prefill'=>false,'onregister'=>false,'add_to_subject_line'=>false, 'placeholder'=>false, 'validation'=>array('default'=>true)
			),
			'ccustom6'=>array(
				'sort'=>10,'key'=>'ccustom6','lbl'=>esc_html__('Custom Field 6 :', 'wppizza'),'value'=>array(),'type'=>'text','enabled'=>false,'required'=>false,'required_on_pickup'=>false,'prefill'=>false,'onregister'=>false,'add_to_subject_line'=>false, 'placeholder'=>false, 'validation'=>array('default'=>true)
			),
			'ctips'=>array(
				'sort'=>11,'key'=>'ctips','lbl'=>esc_html__('Tips/Gratuities :', 'wppizza'),'value'=>array(),'type'=>'tips','enabled'=>false,'required'=>false,'required_on_pickup'=>false,'prefill'=>false,'onregister'=>false,'add_to_subject_line'=>false, 'placeholder'=>false, 'validation'=>array('decimal'=>true)
			)
		);

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

			/*****************************************
				[main order form]
			*****************************************/
			$rules_available = $this->get_validation_rules();
			
			foreach($input[$this->settings_page] as $key=>$b){


				/*
					force some types for certain keys
				*/
				if($key == 'cemail'){ 
					$type = 'email'; 
				}
				elseif($key == 'ctips'){ 
					$type = 'tips'; 
				}
				else{
					$type = wppizza_validate_letters_only($input[$this->settings_page][$key]['type']);					
				}

				/*
					force some validation rules for certain keys

				*/
				$validation = array();
				/*
					loop through rules and validate parameters (if callback != false)
				*/
				$selected_rules = !empty($input[$this->settings_page][$key]['validation']['rule']) ? $input[$this->settings_page][$key]['validation']['rule'] : '' ;
				$selected_rule_parameters = !empty($input[$this->settings_page][$key]['validation']['parameters']) ? $input[$this->settings_page][$key]['validation']['parameters'] : '';

				/**
					if we do not overwrite by rules below, make sure we select at least default
					(in case no rule was selected at all in multiselect)
					also add fixed rules for cemail, ctips
				**/
				$validation['default'] = true;
				if($key == 'cemail'){ $validation['email'] = true; }
				if($key == 'ctips'){ $validation['decimal'] = true; }

				/** loop through rules selected **/
				if(is_array($selected_rules)){
				$validation = array();
				foreach($selected_rules as $rule){
					/* make sure we loose the -hasparameters */
					$rule = str_replace('-hasparameters','',$rule);

					/** validate that such a rule exists **/
					if(isset($rules_available[$rule])){
						$validation[$rule]=true;/////////* set trua as string to use in lozalized js
						/** validate parameters submitted using callback function */
						if(!empty($rules_available[$rule]['parameters'])){
							/*get callback function if not false*/
							$callback_function = !empty($rules_available[$rule]['callback']) ? $rules_available[$rule]['callback'] : 'esc_html' ;
							/* validate each rule parameters */
							$validation[$rule]=$callback_function($selected_rule_parameters[$rule]);
						}
					}
				}}

				/** force default rule for checkbox, radio, hidden **/
				if(in_array($type, array('checkbox', 'radio', 'multicheckbox', 'hidden'))){
					$validation = array();
					$validation['default'] = true;
				}
				/*
					set array values for this key
				*/
				$options[$this->settings_page][$key]['sort'] = (int)($input[$this->settings_page][$key]['sort']);
				$options[$this->settings_page][$key]['key'] = $key;
				$options[$this->settings_page][$key]['lbl'] = ($type == 'checkbox' ) ? wppizza_validate_string($input[$this->settings_page][$key]['lbl'], true) : wppizza_validate_string($input[$this->settings_page][$key]['lbl']);// allow for html when using checkboxes
				$options[$this->settings_page][$key]['type'] = $type;
				$options[$this->settings_page][$key]['enabled'] 			= !empty($input[$this->settings_page][$key]['enabled']) ? true : false;
				$options[$this->settings_page][$key]['required'] 			= !empty($input[$this->settings_page][$key]['required']) ? true : false;
				$options[$this->settings_page][$key]['required_on_pickup'] 	= !empty($input[$this->settings_page][$key]['required_on_pickup']) ? true : false;
				$options[$this->settings_page][$key]['prefill'] 			= !empty($input[$this->settings_page][$key]['prefill']) ? true : false;
				$options[$this->settings_page][$key]['onregister'] 			= !empty($input[$this->settings_page][$key]['onregister']) ? true : false;
				$options[$this->settings_page][$key]['add_to_subject_line'] = !empty($input[$this->settings_page][$key]['add_to_subject_line']) ? true : false;
				$options[$this->settings_page][$key]['value'] 				= isset($input[$this->settings_page][$key]['value']) ? wppizza_sanitize_post_vars(wppizza_strtoarray($input[$this->settings_page][$key]['value'])) : '' ;/* sanitize these the same as user input vars so we can check for matches in multicheckboxes etc */
				$options[$this->settings_page][$key]['placeholder'] 		= isset($input[$this->settings_page][$key]['placeholder']) ? wppizza_validate_string($input[$this->settings_page][$key]['placeholder']) : '' ;
				$options[$this->settings_page][$key]['validation'] 			= $validation;

			}

			/* save sorted */
			asort($options[$this->settings_page]);
			
		}


	return $options;
	}

/***************************************************************
*
*
*	[HELPERS]
*
*
***************************************************************/
	function get_validation_rules(){
			/**validation patterns - available in validation methods and additional validation methods **/
			$validation_rules = array();

			/* default - required set by required attribute on formfield depending on pickup/delivey */
			$validation_rules['default']  		 		= array( 'lbl'=> __('default', 'wppizza-admin'), 				'parameters'=>false, 'callback'=>false, 'enabled'=>true);

			/*standard jquery validator methods*/
			$validation_rules['email'] 		 		 	= array( 'lbl'=> __('email', 'wppizza-admin'), 				'parameters'=>false, 'callback'=>false, 'enabled'=>true);
			$validation_rules['url'] 	 		 		= array( 'lbl'=> __('url', 'wppizza-admin'), 					'parameters'=>false, 'callback'=>false, 'enabled'=>true);
			$validation_rules['date'] 	 				= array( 'lbl'=> __('date', 'wppizza-admin'), 					'parameters'=>false, 'callback'=>false, 'enabled'=>false);
			$validation_rules['dateISO']  				= array( 'lbl'=> __('date ISO', 'wppizza-admin'), 				'parameters'=>false, 'callback'=>false, 'enabled'=>false);
			$validation_rules['number'] 	 			= array( 'lbl'=> __('number', 'wppizza-admin'), 				'parameters'=>false, 'callback'=>false, 'enabled'=>true);
			$validation_rules['digits'] 	 			= array( 'lbl'=> __('digits', 'wppizza-admin'), 				'parameters'=>false, 'callback'=>false, 'enabled'=>false);
			$validation_rules['minlength']  			= array( 'lbl'=> __('min length', 'wppizza-admin'), 			'parameters'=>true,  'callback'=>'wppizza_validate_int_only', 'enabled'=>true);
			$validation_rules['maxlength']  			= array( 'lbl'=> __('max length', 'wppizza-admin'), 			'parameters'=>true,  'callback'=>'wppizza_validate_int_only', 'enabled'=>true);
			$validation_rules['rangelength'] 			= array( 'lbl'=> __('rangelength', 'wppizza-admin'), 			'parameters'=>false, 'callback'=>false, 'enabled'=>false);
			$validation_rules['min'] 	 				= array( 'lbl'=> __('min', 'wppizza-admin'), 					'parameters'=>false, 'callback'=>'wppizza_validate_int_only', 'enabled'=>false);
			$validation_rules['max'] 	 				= array( 'lbl'=> __('max', 'wppizza-admin'), 					'parameters'=>false, 'callback'=>'wppizza_validate_int_only', 'enabled'=>false);
			$validation_rules['range'] 	 				= array( 'lbl'=> __('range', 'wppizza-admin'), 				'parameters'=>false, 'callback'=>false, 'enabled'=>false);
			$validation_rules['step'] 	 				= array( 'lbl'=> __('step', 'wppizza-admin'), 					'parameters'=>false, 'callback'=>false, 'enabled'=>false);
			$validation_rules['equalTo']  				= array( 'lbl'=> __('equal to', 'wppizza-admin'), 				'parameters'=>false, 'callback'=>false, 'enabled'=>false);
			$validation_rules['remote'] 	 			= array( 'lbl'=> __('remote', 'wppizza-admin'), 				'parameters'=>false, 'callback'=>false, 'enabled'=>false);
			/* additional  jquery validator methods */
			$validation_rules['alphanumeric']			= array( 'lbl'=> __('alphanumeric', 'wppizza-admin'), 			'parameters'=>false, 'callback'=>false, 'enabled'=>false);
			$validation_rules['maxWords']				= array( 'lbl'=> __('max words', 'wppizza-admin'), 			'parameters'=>false, 'callback'=>false, 'enabled'=>false);
			$validation_rules['minWords']				= array( 'lbl'=> __('min words', 'wppizza-admin'), 			'parameters'=>false, 'callback'=>false, 'enabled'=>false);
			$validation_rules['rangeWords']				= array( 'lbl'=> __('range words', 'wppizza-admin'), 			'parameters'=>false, 'callback'=>false, 'enabled'=>false);
			$validation_rules['accept']					= array( 'lbl'=> __('accept', 'wppizza-admin'), 				'parameters'=>false, 'callback'=>false, 'enabled'=>false);
			$validation_rules['bankaccountNL']			= array( 'lbl'=> __('bankaccount NL', 'wppizza-admin'), 		'parameters'=>false, 'callback'=>false, 'enabled'=>false);
			$validation_rules['bankorgiroaccountNL']	= array( 'lbl'=> __('bankorgiroaccount NL', 'wppizza-admin'), 	'parameters'=>false, 'callback'=>false, 'enabled'=>false);
			$validation_rules['bic']					= array( 'lbl'=> __('bic', 'wppizza-admin'), 					'parameters'=>false, 'callback'=>false, 'enabled'=>false);
			$validation_rules['cifES']					= array( 'lbl'=> __('cif ES', 'wppizza-admin'), 				'parameters'=>false, 'callback'=>false, 'enabled'=>false);
			$validation_rules['cpfBR']					= array( 'lbl'=> __('cpf BR', 'wppizza-admin'), 				'parameters'=>false, 'callback'=>false, 'enabled'=>false);
			$validation_rules['creditcard']				= array( 'lbl'=> __('creditcard', 'wppizza-admin'), 			'parameters'=>false, 'callback'=>false, 'enabled'=>false);
			$validation_rules['creditcardtypes']		= array( 'lbl'=> __('creditcard types', 'wppizza-admin'), 		'parameters'=>false, 'callback'=>false, 'enabled'=>false);
			$validation_rules['currency']				= array( 'lbl'=> __('currency', 'wppizza-admin'), 				'parameters'=>false, 'callback'=>false, 'enabled'=>false);
			$validation_rules['dateFA']					= array( 'lbl'=> __('date FA', 'wppizza-admin'), 				'parameters'=>false, 'callback'=>false, 'enabled'=>false);
			$validation_rules['dateITA']				= array( 'lbl'=> __('date ITA', 'wppizza-admin'), 				'parameters'=>false, 'callback'=>false, 'enabled'=>false);
			$validation_rules['dateNL']					= array( 'lbl'=> __('date NL', 'wppizza-admin'), 				'parameters'=>false, 'callback'=>false, 'enabled'=>false);
			$validation_rules['extension']				= array( 'lbl'=> __('extension', 'wppizza-admin'), 			'parameters'=>false, 'callback'=>false, 'enabled'=>false);
			$validation_rules['giroaccountNL']			= array( 'lbl'=> __('giroaccount NL', 'wppizza-admin'), 		'parameters'=>false, 'callback'=>false, 'enabled'=>false);
			$validation_rules['iban']					= array( 'lbl'=> __('iban', 'wppizza-admin'), 					'parameters'=>false, 'callback'=>false, 'enabled'=>false);
			$validation_rules['integer']				= array( 'lbl'=> __('integer', 'wppizza-admin'), 				'parameters'=>false, 'callback'=>false, 'enabled'=>true);
			$validation_rules['ipv4']					= array( 'lbl'=> __('ipv4', 'wppizza-admin'), 					'parameters'=>false, 'callback'=>false, 'enabled'=>false);
			$validation_rules['ipv6']					= array( 'lbl'=> __('ipv6', 'wppizza-admin'), 					'parameters'=>false, 'callback'=>false, 'enabled'=>false);
			$validation_rules['lettersonly']			= array( 'lbl'=> __('lettersonly', 'wppizza-admin'), 			'parameters'=>false, 'callback'=>false, 'enabled'=>true);
			$validation_rules['letterswithbasicpunc']	= array( 'lbl'=> __('letters w. basic punc', 'wppizza-admin'), 'parameters'=>false, 'callback'=>false, 'enabled'=>true);
			$validation_rules['mobileNL']				= array( 'lbl'=> __('mobile NL', 'wppizza-admin'), 			'parameters'=>false, 'callback'=>false, 'enabled'=>false);
			$validation_rules['mobileUK']				= array( 'lbl'=> __('mobile UK', 'wppizza-admin'), 			'parameters'=>false, 'callback'=>false, 'enabled'=>false);
			$validation_rules['nieES']					= array( 'lbl'=> __('nieES', 'wppizza-admin'), 				'parameters'=>false, 'callback'=>false, 'enabled'=>false);
			$validation_rules['nifES']					= array( 'lbl'=> __('nifES', 'wppizza-admin'), 				'parameters'=>false, 'callback'=>false, 'enabled'=>false);
			$validation_rules['notEqualTo']				= array( 'lbl'=> __('not equal to', 'wppizza-admin'), 			'parameters'=>false, 'callback'=>false, 'enabled'=>false);
			$validation_rules['nowhitespace']			= array( 'lbl'=> __('no whitespace', 'wppizza-admin'), 		'parameters'=>false, 'callback'=>false, 'enabled'=>true);
			$validation_rules['parameters']				= array( 'lbl'=> __('parameters', 'wppizza-admin'), 			'parameters'=>false, 'callback'=>false, 'enabled'=>false);
			$validation_rules['phoneNL']				= array( 'lbl'=> __('phone NL', 'wppizza-admin'), 				'parameters'=>false, 'callback'=>false, 'enabled'=>false);
			$validation_rules['phoneUK']				= array( 'lbl'=> __('phone UK', 'wppizza-admin'), 				'parameters'=>false, 'callback'=>false, 'enabled'=>false);
			$validation_rules['phoneUS']				= array( 'lbl'=> __('phone US', 'wppizza-admin'), 				'parameters'=>false, 'callback'=>false, 'enabled'=>false);
			$validation_rules['phonesUK']				= array( 'lbl'=> __('phones UK', 'wppizza-admin'), 			'parameters'=>false, 'callback'=>false, 'enabled'=>false);
			$validation_rules['postalCodeCA']			= array( 'lbl'=> __('postalcode CA', 'wppizza-admin'), 		'parameters'=>false, 'callback'=>false, 'enabled'=>false);
			$validation_rules['postalcodeBR']			= array( 'lbl'=> __('postalcode BR', 'wppizza-admin'), 		'parameters'=>false, 'callback'=>false, 'enabled'=>false);
			$validation_rules['postalcodeIT']			= array( 'lbl'=> __('postalcode IT', 'wppizza-admin'), 		'parameters'=>false, 'callback'=>false, 'enabled'=>false);
			$validation_rules['postalcodeNL']			= array( 'lbl'=> __('postalcode NL', 'wppizza-admin'), 		'parameters'=>false, 'callback'=>false, 'enabled'=>false);
			$validation_rules['postcodeUK']				= array( 'lbl'=> __('postcode UK', 'wppizza-admin'), 			'parameters'=>false, 'callback'=>false, 'enabled'=>false);
			$validation_rules['require_from_group']		= array( 'lbl'=> __('require_from_group', 'wppizza-admin'), 	'parameters'=>false, 'callback'=>false, 'enabled'=>false);
			$validation_rules['skip_or_fill_minimum']	= array( 'lbl'=> __('skip_or_fill_minimum', 'wppizza-admin'), 	'parameters'=>false, 'callback'=>false, 'enabled'=>false);
			$validation_rules['stateUS']				= array( 'lbl'=> __('state US', 'wppizza-admin'), 				'parameters'=>false, 'callback'=>false, 'enabled'=>false);
			$validation_rules['time']					= array( 'lbl'=> __('time', 'wppizza-admin'), 					'parameters'=>false, 'callback'=>false, 'enabled'=>false);
			$validation_rules['time12h']				= array( 'lbl'=> __('time12h', 'wppizza-admin'), 				'parameters'=>false, 'callback'=>false, 'enabled'=>false);
			$validation_rules['url2']					= array( 'lbl'=> __('url2', 'wppizza-admin'), 					'parameters'=>false, 'callback'=>false, 'enabled'=>false);
			$validation_rules['vinUS']					= array( 'lbl'=> __('vinUS', 'wppizza-admin'), 				'parameters'=>false, 'callback'=>false, 'enabled'=>false);
			$validation_rules['zipcodeUS']				= array( 'lbl'=> __('zipcodeUS', 'wppizza-admin'), 			'parameters'=>false, 'callback'=>false, 'enabled'=>false);
			$validation_rules['ziprange']				= array( 'lbl'=> __('ziprange', 'wppizza-admin'), 				'parameters'=>false, 'callback'=>false, 'enabled'=>false);
			/* this plugin custom added methods */
			$validation_rules['decimal']				= array( 'lbl'=> __('decimal', 'wppizza-admin'), 				'parameters'=>false, 'callback'=>false, 'enabled'=>false);

		/*
			// customise rules
			// see jqueryvalidation.com what each rule/method does

			// add filter
			add_filter('wppizza_filter_validation_rules','my_function');
			//to enable an exiting  rule above - let's say 'phoneUS'
			function my_function($validation_rules){
				$validation_rules['phoneUS']['enabled'] = true;
			return $validation_rules;
			}

			//to add your onw rule - let's say 'my_rule'
			function my_function($validation_rules){
				$validation_rules['my_rule']= array( 'lbl'=> 'My Rule', 'parameters'=>false, 'callback'=>false, 'enabled'=>true);
			return $validation_rules;
			}
			// then add the rule like so via wp_footer action hook (or add it to an already existing js file)
			// adjust the validation pattern as appropriate for your validation rule
			$.validator.methods.my_rule = function (value, element) {
	    		return this.optional(element) || /^(?:\d+|\d{1,3}(?:[\s\.,]\d{3})+)(?:[\.,]\d+)?$/.test(value);
			}

		*/
		$validation_rules = apply_filters('wppizza_filter_validation_rules', $validation_rules);

		/* only return enabled*/
		$rules = array();
		foreach($validation_rules as $key=>$rule){
			if(!empty($rule['enabled'])){
				$rules[$key] = $rule;
			}
		}

		return $rules;
	}


}
/***************************************************************
*
*	[ini]
*
***************************************************************/
$WPPIZZA_MODULE_ORDERFORM_ORDERPAGE = new WPPIZZA_MODULE_ORDERFORM_ORDERPAGE();
?>