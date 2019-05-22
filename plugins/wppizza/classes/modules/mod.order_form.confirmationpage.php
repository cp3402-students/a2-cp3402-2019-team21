<?php
/**
* WPPIZZA_MODULE_ORDERFORM_CONFIRMATIONPAGE Class
*
* @package     WPPIZZA
* @subpackage  WPPIZZA_MODULE_ORDERFORM_CONFIRMATIONPAGE
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
class WPPIZZA_MODULE_ORDERFORM_CONFIRMATIONPAGE{

	private $settings_page = 'order_form';/* which admin subpage (identified there by this->class_key) are we adding this to */
	private $section_key = 'confirmation_form';/* must be unique */
	
	private $section_form_enabled = 'confirmation_form_enabled';
	private $section_amend_order_link = 'confirmation_form_amend_order_link';
	private $section_formfields = 'formfields';
	private $section_form_localize = 'localization';


	function __construct() {
		/**********************************************************
			[add settings to admin]
		***********************************************************/
		if(is_admin()){
			/* add admin options settings page*/
			add_filter('wppizza_filter_settings_sections_'.$this->settings_page.'', array($this, 'admin_options_settings'), 20, 5);
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
		global $wppizza_options;
		/********************************
		*	[confirmation enabled ?]
		********************************/		
		/*sections*/
		if($sections){
			$settings['sections'][$this->section_form_enabled] = __('Confirmation Page', 'wppizza-admin');
		}
		/*fields*/
		if($fields){
			$field = 'confirmation_form_enabled';
			$settings['fields'][$this->section_form_enabled][$field] = array('' , array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('Some Countries/Jurisdictions require another, final , non-editable confirmation page before sending the order. If this is the case, tick this box and save. You will get some additional formfields you can make available in that final form','wppizza-admin'),
				'description'=>array(
					'<span style="color:red">'.__('Disclaimer: it is your responsibility to adhere to any required laws that might apply in your locality/jurisdiction','wppizza-admin').'</span>'
				)
			));
		}
		/*help*/
		if($help){
			$settings['help'][$this->section_form_enabled][] = array(
				'label'=>__('Confirmation Page', 'wppizza-admin'),
				'description'=>array(
					__('If you require a final confirmation page, eneable this option and save. Additional settings will be made available for this final confirmation page', 'wppizza-admin')
				)
			);
		}

		/********************************
		*	[confirmation formfields - if conirmation form enabled]
		********************************/
		if(!empty($wppizza_options[$this->section_key]['confirmation_form_enabled'])){
		
			if($sections){
				$settings['sections'][$this->section_formfields] =  __('Confirmation Page - Formfields', 'wppizza-admin');
			}
			/*fields*/
			if($fields){
	
				$field = 'formfields';
				$settings['fields'][$this->section_formfields][$field] = array('' , array(
					'value_key'=>$field,
					'option_key'=>$this->settings_page,
					'label'=>'',
					'description'=>array()
				));						
			}
			/*help*/
			if($help){		
				$settings['help'][$this->section_formfields][] = array(
					'label'=>__('Confirmation Page - Formfields', 'wppizza-admin'),
					'description'=>array(
						__('Provided you have enabled the confirmation page, there are optional formfields you can use in the confirmation page', 'wppizza-admin'),
						__('Set the sortorder using the sort option provided.', 'wppizza-admin'),
						__('Set a label for the field or link. You can use html here too if - for example - you would like to set a link for the entered text to another page).', 'wppizza-admin'),
						__('Check the "Enabled" box for the field/link to actually be displayed on the confirmation page.', 'wppizza-admin'),
						__('Enable the "Required" box to make a type checkbox to be required to be checked before a customer can submit an order / checkout.', 'wppizza-admin'),
						__('Choose between a simple text/link or checkbox. If you want the text to be a link, enter the required link html into the "Label" field.', 'wppizza-admin')
					)
				);		
			}
		}
		/********************************
		*	[confirmation form localization - if conirmation form enabled]
		********************************/
		if(!empty($wppizza_options[$this->section_key]['confirmation_form_enabled'])){
		
			if($sections){
				$settings['sections'][$this->section_form_localize] = __('Confirmation Page - Localization', 'wppizza-admin');
			}
			/*fields*/
			if($fields){
				$field = 'localization';
				$settings['fields'][$this->section_form_localize][$field] = array('' , array(
					'value_key'=>$field,
					'option_key'=>$this->settings_page,
					'label'=>'',
					'description'=>array()
				));						
			}
			/*help*/
			if($help){		
				$settings['help'][$this->section_form_localize][] = array(
					'label'=>__('Confirmation Page - Localization', 'wppizza-admin'),
					'description'=>array(
						__('Several additional text, fields, buttuns etc are being used on the final confirmation form.', 'wppizza-admin'),
						__('Adjust the labels for those as required, using the input fields provided.', 'wppizza-admin')
					)
				);		
			}
			/*inputs*/
			if($inputs == $this->section_form_localize){
				
				/*input keys / input labels for this section's field inputs*/		
				$settings['input']['change_user_details'] = array(
					'label'=>__('Confirmation Form - [labels]: link text to use for link to return to previous page for changing personal details','wppizza-admin'),
					'option_key'=>$this->section_form_localize
				);			
				$settings['input']['change_order_details'] = array(
					'label'=>__('Confirmation Form - [labels]: text and associated link to use to direct customer to a page where he/she can amend the order.','wppizza-admin'),
					'option_key'=>$this->section_form_localize
				);	
				$settings['input']['payment_method'] = array(
					'label'=>__('Confirmation Form - [labels]: label for payment method used','wppizza-admin'),
					'option_key'=>$this->section_form_localize
				);							
				$settings['input']['legend_legal'] = array(
					'label'=>__('Confirmation Form - [section header]: legal aspects','wppizza-admin'),
					'option_key'=>$this->section_form_localize
				);			
				$settings['input']['legend_personal'] = array(
					'label'=>__('Confirmation Form - [section header]: personal details','wppizza-admin'),
					'option_key'=>$this->section_form_localize
				);	
				$settings['input']['legend_payment_method'] = array(
					'label'=>__('Confirmation Form - [section header]: payment method','wppizza-admin'),
					'option_key'=>$this->section_form_localize
				);				
				$settings['input']['legend_order_details'] = array(
					'label'=>__('Confirmation Form - [section header]: order details','wppizza-admin'),
					'option_key'=>$this->section_form_localize
				);			
				$settings['input']['confirm_now_button'] = array(
					'label'=>__('Confirmation Form - [labels]: label buy now button','wppizza-admin'),
					'option_key'=>$this->section_form_localize
				);	
				$settings['input']['header_itemised_article'] = array(
					'label'=>__('Confirmation Form - [itemised header]: article','wppizza-admin'),
					'option_key'=>$this->section_form_localize
				);							
				$settings['input']['header_itemised_price_single'] = array(
					'label'=>__('Confirmation Form - [itemised header]: single price','wppizza-admin'),
					'option_key'=>$this->section_form_localize
				);			
				$settings['input']['header_itemised_quantity'] = array(
					'label'=>__('Confirmation Form - [itemised header]: quantity','wppizza-admin'),
					'option_key'=>$this->section_form_localize
				);	
				$settings['input']['header_itemised_price'] = array(
					'label'=>__('Confirmation Form - [itemised header]: price','wppizza-admin'),
					'option_key'=>$this->section_form_localize
				);				
				$settings['input']['subtotals_after_additional_info'] = array(
					'label'=>__('Confirmation Form - [miscellaneous]: additional/optional info/text to display after (sub)totals','wppizza-admin'),
					'option_key'=>$this->section_form_localize
				);	
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



		/************************************************
			confirmation enabled 
		************************************************/
		if($field=='confirmation_form_enabled'){
			echo "<label>";
				echo "<input name='".WPPIZZA_SLUG."[".$this->section_key."][".$field."]' type='checkbox'  ". checked($wppizza_options[$this->section_key][$field],true,false)." value='1' />";
				echo "".$label."";
			echo "</label>";
			echo "".$description."";
		}		

		/************************************************
			confirmation formfields
		************************************************/
		if($field=='formfields'){

			$input_fields=$wppizza_options[$this->section_key][$this->section_formfields];
			/**sort by sort**/
			asort($input_fields);			
						
			/**start table**/
			echo"<table id='wppizza_admin_table_".$this->section_key."' class='wppizza_admin_table'>";
				echo"<thead>";
					echo"<tr><th colspan='5'>".__('Legal', 'wppizza-admin')." <span class='description'>[".__('enable some formfields or text/links you might want to use and/or make required', 'wppizza-admin')."]</span></th></tr>";
					echo"<tr><th>".__('Enabled', 'wppizza-admin')."</th><th>".__('Sort', 'wppizza-admin')."</th><th>".__('Label [html allowed]', 'wppizza-admin')."</th><th>".__('Required', 'wppizza-admin')."</th><th>".__('Type', 'wppizza-admin')."</th></tr>";			
				echo"</thead>";
				
				echo"<tbody>";
				/**inputs**/
				foreach($input_fields as $k=>$v){
					echo"<tr class='".$v['key']."'>";
						echo"<td><span class='button'><input name='".WPPIZZA_SLUG."[".$this->section_key."][".$this->section_formfields."][".$k."][enabled]' type='checkbox' ". checked($v['enabled'],true,false)." value='1' /></span></td>";					
						echo"<td><input name='".WPPIZZA_SLUG."[".$this->section_key."][".$this->section_formfields."][".$k."][sort]' size='1' type='text' value='".$v['sort']."' /></td>";
						echo"<td><input name='".WPPIZZA_SLUG."[".$this->section_key."][".$this->section_formfields."][".$k."][lbl]' size='55' type='text' value='".esc_html($v['lbl'])."' /></td>";
						echo"<td><input name='".WPPIZZA_SLUG."[".$this->section_key."][".$this->section_formfields."][".$k."][required]' type='checkbox' ". checked($v['required'],true,false)." value='1' /></td>";
						echo"<td>";
							echo "<select id='".WPPIZZA_SLUG."_".$this->section_key."_type_".$k."' class='".WPPIZZA_SLUG."_".$this->section_key."_type' name='".WPPIZZA_SLUG."[".$this->section_key."][".$this->section_formfields."][".$k."][type]' />";
								echo'<option value="checkbox" '.selected($v['type'],"checkbox",false).'>'.__('checkbox', 'wppizza-admin').'</option>';
								echo'<option value="link" '.selected($v['type'],"link",false).'>'.__('text/link', 'wppizza-admin').'</option>';
								echo'<option value="radio" '.selected($v['type'],"radio",false).'>'.__('radio', 'wppizza-admin').'</option>';															
								echo'<option value="select" '.selected($v['type'],"select",false).'>'.__('select', 'wppizza-admin').'</option>';
								// if we enable the next two, we will have to add js validation options too ! so let's leave this for now. not really likely to be used as confirmation form fields anyway
								//echo'<option value="text" '.selected($v['type'],"text",false).'>'.__('text', 'wppizza-admin').'</option>';	
								//echo'<option value="textarea" '.selected($v['type'],"textarea",false).'>'.__('textarea', 'wppizza-admin').'</option>';
							echo "</select>";
							
						/**if select, add input field**/
						/*conditional defaults*/
						$display=' style="display:none"';
						$val='';
						/**select*/
						if($v['type']=='select' || $v['type']=='radio' ){
							$display=' style="display:block"';
							$val=''.implode(",",$v['value']).'';
						}
						/**select custom*/
						//if($v['type']=='selectcustom'){
						//	$display='';
						//	$valArr=array();
						//	foreach($v['value'] as $vKey=>$vVal){
						//		$valArr[]=''.$vKey.':'.$vVal.'';
						//	}
						//	$val=implode("|",$valArr);
						//}

						
						echo "<span class='".WPPIZZA_SLUG."_".$this->section_key."_select' ".$display.">";
							echo "<input name='".WPPIZZA_SLUG."[".$this->section_key."][".$this->section_formfields."][".$k."][value]' type='text' value='".$val."' />";
						echo "</span>";
						echo "<span class='".WPPIZZA_SLUG."_".$this->section_key."_select' ".$display.">";
							if($v['type']!='selectcustom'){
								echo "<span class='description'>".__('separate options with comma', 'wppizza-admin')."</span>";
							}
							//if($v['type']=='selectcustom'){echo "".__('enter required value pairs', 'wppizza-admin')."";}
						echo "</span>";							
						echo"</td>";
					echo"</tr>";
				}			
				echo"</tbody>";
			echo"</table>";
		}
		/************************************************
			confirmation form localization
		************************************************/
		if($field=='localization'){

			/** get inputs**/
			$input_fields=$this->admin_options_settings(false, false, false, $this->section_form_localize, false);
			/**sort by label**/
			asort($input_fields);
			/**start table**/
			echo"<table id='wppizza_admin_table_".$field."' class='wppizza_admin_table'>";
			/**inputs**/
			foreach($input_fields['input'] as $value_key=>$values){
				echo "<tr>";
					echo "<td>";
						echo "<label>";
							echo "<input name='".WPPIZZA_SLUG."[".$this->section_key."][".$values['option_key']."][".$value_key."]' size='30' type='text' value='".$wppizza_options[$this->section_key][$values['option_key']][$value_key]."' />";
							echo " ".$values['label']."";
						echo "</label>";

						if($value_key=='change_order_details'){
							echo"<br />";
							wp_dropdown_pages('name='.WPPIZZA_SLUG.'['.$this->section_key.']['.$this->section_amend_order_link.']&selected='.$wppizza_options[$this->section_key][$this->section_amend_order_link].'&show_option_none='.__('-- select page to link to --', 'wppizza-admin').'');
							echo"<span class='description'>".__('set link to page to allow customer to amend order', 'wppizza-admin')."</span>";
						}
					echo "</td>";
				echo "</tr>";					
			}
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

		/*****************************************
			[confirmation form on/off - using section key here as opposed to the "normal" settings_page]
		*****************************************/
		$options[$this->section_key][$this->section_form_enabled]=false;

		/*****************************************
			[confirmation form amend order link]
		*****************************************/
		$options[$this->section_key][$this->section_amend_order_link]='';
		
		/*****************************************
			[confirmation form legal/required]
		*****************************************/
		$options[$this->section_key][$this->section_formfields]=array(
			0=>array('sort'=>0,'key'=>'wpppizza_confirm_1','lbl'=>esc_html__('Accept Terms and Conditions', 'wppizza'),'value'=>array(),'type'=>'checkbox','enabled'=>false,'required'=>false, 'placeholder'=>false),
			1=>array('sort'=>1,'key'=>'wpppizza_confirm_2','lbl'=>esc_html__('Distance Selling Regulations ', 'wppizza'),'value'=>array(),'type'=>'checkbox','enabled'=>false,'required'=>false, 'placeholder'=>false),
			2=>array('sort'=>2,'key'=>'wpppizza_confirm_3','lbl'=>esc_html__('Other', 'wppizza'),'value'=>array(),'type'=>'checkbox','enabled'=>false,'required'=>false, 'placeholder'=>false),
			3=>array('sort'=>3,'key'=>'wpppizza_confirm_4','lbl'=>esc_html__('Other', 'wppizza'),'value'=>array(),'type'=>'checkbox','enabled'=>false,'required'=>false, 'placeholder'=>false),
			4=>array('sort'=>4,'key'=>'wpppizza_confirm_5','lbl'=>esc_html__('Other', 'wppizza'),'value'=>array(),'type'=>'checkbox','enabled'=>false,'required'=>false, 'placeholder'=>false),
			5=>array('sort'=>5,'key'=>'wpppizza_confirm_6','lbl'=>esc_html__('Other', 'wppizza'),'value'=>array(),'type'=>'checkbox','enabled'=>false,'required'=>false, 'placeholder'=>false)
		);
		
		/*****************************************
			[confirmation form localization]
		*****************************************/
		/**make sure keys are NOT used in "normal" localization vars too, as we are merging those two arrays to use in confirmation page */
		$options[$this->section_key][$this->section_form_localize]['change_user_details']=esc_html__('change', 'wppizza');
		$options[$this->section_key][$this->section_form_localize]['change_order_details']=esc_html__('amend order', 'wppizza');
		$options[$this->section_key][$this->section_form_localize]['payment_method']=esc_html__('selected payment method :', 'wppizza');
		$options[$this->section_key][$this->section_form_localize]['legend_legal']=esc_html__('legal aspects', 'wppizza');
		$options[$this->section_key][$this->section_form_localize]['legend_personal']=esc_html__('personal information', 'wppizza');
		$options[$this->section_key][$this->section_form_localize]['legend_payment_method']=esc_html__('payment method', 'wppizza');
		$options[$this->section_key][$this->section_form_localize]['legend_order_details']=esc_html__('order details', 'wppizza');
		$options[$this->section_key][$this->section_form_localize]['confirm_now_button']=esc_html__('buy now (legally binding)', 'wppizza');
		$options[$this->section_key][$this->section_form_localize]['header_itemised_article']=esc_html__('article', 'wppizza');
		$options[$this->section_key][$this->section_form_localize]['header_itemised_price_single']=esc_html__('single price', 'wppizza');
		$options[$this->section_key][$this->section_form_localize]['header_itemised_quantity']=esc_html__('quantity', 'wppizza');
		$options[$this->section_key][$this->section_form_localize]['header_itemised_price']=esc_html__('price', 'wppizza');
		$options[$this->section_key][$this->section_form_localize]['subtotals_after_additional_info']='';


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
			
			
		//	$options[$this->section_key] = array();
		
						
			/*****************************************
				[confirmation form on/off]
			*****************************************/
			$options[$this->section_key][$this->section_form_enabled] = !empty($input[$this->section_key][$this->section_form_enabled]) ? true : false;
	
			/*****************************************
				[confirmation form amend order link]
				[confirmation form legal/required]
				[confirmation form localization]
				
				only needs validating when they actually exist on page
				as otherwise the previous options will be kept
			*****************************************/
			if(!empty($input[$this->section_key][$this->section_form_enabled]) && isset($input[$this->section_key][$this->section_amend_order_link])){
				
				/*confirmation form amedn order link*/
				$options[$this->section_key][$this->section_amend_order_link] = (int)$input[$this->section_key][$this->section_amend_order_link];
				
				/*confirmation form added formfields*/
				foreach($input[$this->section_key][$this->section_formfields] as $a=>$b){
					$options[$this->section_key][$this->section_formfields][$a]['sort'] = (int)($input[$this->section_key][$this->section_formfields][$a]['sort']);
					$options[$this->section_key][$this->section_formfields][$a]['key'] = $options[$this->section_key][$this->section_formfields][$a]['key'];/*take key used when we added the defaults*/
					$options[$this->section_key][$this->section_formfields][$a]['lbl'] = wppizza_validate_string($input[$this->section_key][$this->section_formfields][$a]['lbl'],true);
					$options[$this->section_key][$this->section_formfields][$a]['type'] = wppizza_validate_letters_only($input[$this->section_key][$this->section_formfields][$a]['type']);
					$options[$this->section_key][$this->section_formfields][$a]['enabled'] = !empty($input[$this->section_key][$this->section_formfields][$a]['enabled']) ? true : false;
					$options[$this->section_key][$this->section_formfields][$a]['required'] = !empty($input[$this->section_key][$this->section_formfields][$a]['required']) ? true : false;
					$options[$this->section_key][$this->section_formfields][$a]['value'] = wppizza_strtoarray($input[$this->section_key][$this->section_formfields][$a]['value']);
				}
				
				/*locaization confirmation form*/
				foreach($input[$this->section_key][$this->section_form_localize] as $a=>$b){
					$html=false;
					$options[$this->section_key][$this->section_form_localize][$a]=wppizza_validate_string($b,$html);
				}
			}

		}
	
	return $options;
	}
}
/***************************************************************
*
*	[ini]
*
***************************************************************/
$WPPIZZA_MODULE_ORDERFORM_CONFIRMATIONPAGE = new WPPIZZA_MODULE_ORDERFORM_CONFIRMATIONPAGE();
?>