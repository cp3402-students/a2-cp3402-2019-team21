<?php
if ( ! defined( 'ABSPATH' ) ) exit;/*Exit if accessed directly*/
/**************************************************************************************************************************************

	CLASS - WPPIZZA_TEMPLATES_EMAIL_PRINT


**************************************************************************************************************************************/
class WPPIZZA_MARKUP_EMAIL_PRINT{

/**********************************************************************************************
*
*
*	[construct]
*
*
*********************************************************************************************/
		function __construct() {
		}

/**********************************************************************************************
*
*
*	[public methods]
*	-> getters
*
*********************************************************************************************/

		/*************************************************************
		*
		*	get/set array key/values for new templates or default on install
		*
		*************************************************************/
		function admin_template_get_values($tplType, $tplId, $tplValues, $arrayIdent){

			/* in case we add some other template types one day */
			if($tplType=='print' || $tplType=='emails'){
				/*
					section/template values
				*/
				$tpl_args = array(
					'tpl_type' => $tplType,
					'tpl_id' => $tplId,
					'tpl_values'=>  $tplValues,
				);
				$tpl_values = WPPIZZA()->order->orders_formatted(false, $tpl_args, 'template_values_'.$tplId.'');

			}

		return $tpl_values;
		}


		/*************************************************************
		*
		*	create admin markup of email/print templates
		*
		*************************************************************/
		function admin_template($msgKey, $templateKey, $set_values = false, $arrayIdent = 'templates'){
			global $wppizza_options;
			/*
				get default for new template or use set values
			*/
			$tplVals = $this->admin_template_get_values($templateKey, $msgKey, $set_values, $arrayIdent);

			/*
				create markup
			*/
			$markup = '';
				/**
					template div wrapper
				**/
				$markup.='<!-- template type:'.$templateKey.' key:'.$msgKey.' -->';


				$markup.='<div id="'.WPPIZZA_SLUG.'-'.$arrayIdent.'-'.$msgKey.'" class="'.WPPIZZA_SLUG.'-'.$arrayIdent.'-template '.WPPIZZA_SLUG.'-'.$arrayIdent.'-'.$templateKey.' '.$tplVals['new_class'].'">';

				/******************************

					header/main options

				******************************/
				$markup.='<table id="'.WPPIZZA_SLUG.'-'.$arrayIdent.'-main-'.$templateKey.'-'.$msgKey.'" class="'.WPPIZZA_SLUG.'-'.$arrayIdent.'-main '.WPPIZZA_SLUG.'-'.$arrayIdent.'-main-'.$templateKey.' widefat">';
					$markup.='<tbody>';
						$markup.='<tr>';

							/**
								print template , add "use" button/radio
							**/
							if($templateKey=='print'){
								$markup.='<td class="'.WPPIZZA_SLUG.'-'.$arrayIdent.'-main-use">';
									$markup.='<label class="wppizza-dashicons wppizza-dashicons-radio">'.__('use','wppizza-admin').'<input type="radio" id="'.WPPIZZA_SLUG.'_'.$arrayIdent.'_'.$templateKey.'_print_id_'.$msgKey.'" name="'.WPPIZZA_SLUG.'[templates_apply]['.$templateKey.'][print_id]" '.checked($wppizza_options['templates_apply'][$templateKey],$msgKey,false).' value="'.$msgKey.'" /></label>';
								$markup.='</td>';
							}


							/**
								title/label internal
							**/
							$markup.='<td class="'.WPPIZZA_SLUG.'-'.$arrayIdent.'-main-label">';
								$markup.='<label><input name="'.WPPIZZA_SLUG.'['.$arrayIdent.']['.$templateKey.']['.$msgKey.'][title]" type="text" size="10" value="'.$tplVals['title'].'" /></label>';
							$markup.='</td>';


							/**
								format
							**/
							$markup.='<td class="'.WPPIZZA_SLUG.'-'.$arrayIdent.'-main-mail_type">';
									/*mail type */
									$markup.='<label>';
										$markup.='<span>'.__('format', 'wppizza-admin').'</span>';
											/*set values*/
											$mailFieldName=''.WPPIZZA_SLUG.'['.$arrayIdent.']['.$templateKey.']['.$msgKey.'][mail_type]';
											$mailFieldSelected=$tplVals['mail_type'];
											$mailID=''.WPPIZZA_SLUG.'_'.$arrayIdent.'_mail_type_'.$templateKey.'_'.$msgKey.'';
											$mailClass=''.WPPIZZA_SLUG.'_'.$arrayIdent.'_mail_type';

										$markup .= wppizza_admin_mail_delivery_options($mailFieldName, $mailFieldSelected, $mailID, $mailClass);

									$markup.='</label>';
							$markup.='</td>';


							/**
								email main recipients
							**/
							if($templateKey=='emails'){
								$markup.='<td class="'.WPPIZZA_SLUG.'-'.$arrayIdent.'-main-recipients">';
										$markup.='<span>'.__('email recipients','wppizza-admin').':</span>';
										foreach($tplVals['recipients'] as $recKey=>$recTitle){
											$markup.='<label><input type="radio" id="'.WPPIZZA_SLUG.'_'.$arrayIdent.'_'.$templateKey.'_recipients_'.$recKey.'_'.$msgKey.'" name="'.WPPIZZA_SLUG.'[templates_apply]['.$templateKey.'][recipients_default]['.$recKey.']" '.checked($tplVals['recipients_default_selected'][$recKey],$msgKey,false).' value="'.$msgKey.'" />'.$recTitle.'</label>';
										}
										$markup.='';
								$markup.='</td>';
							}

							/**
								email additional recipients
							**/
							if($templateKey=='emails'){
								$markup.='<td class="'.WPPIZZA_SLUG.'-'.$arrayIdent.'-main-additional-recipients">';
									$markup.='<label><span>'.__('additional recipients','wppizza-admin').'</span><input type="text" name="'.WPPIZZA_SLUG.'['.$arrayIdent.']['.$templateKey.']['.$msgKey.'][recipients_additional]" placeholder="'.__('comma separated emails','wppizza-admin').'" value="'.$tplVals['recipients_additional'].'" /></label>';
								$markup.='</td>';
							}
							/**
								email attachments
							**/
							if($templateKey=='emails'){
								$markup.='<td class="'.WPPIZZA_SLUG.'-'.$arrayIdent.'-main-attachments">';
									$markup.='<label><span>'.__('omit attachments','wppizza-admin').'</span><input type="checkbox"  name="'.WPPIZZA_SLUG.'['.$arrayIdent.']['.$templateKey.']['.$msgKey.'][omit_attachments]" '.checked($tplVals['omit_attachments'],true,false).' value="1" />'.__('(if any)','wppizza-admin').'</label>';
								$markup.='</td>';
							}



							/**
								edit, preview, locked etc  right
							**/
							$markup.='<td class="'.WPPIZZA_SLUG.'-'.$arrayIdent.'-main-buttons">';
									
									$icons = array();
									
									/*icon edit*/
									$icons['edit'] = "<span id='".WPPIZZA_SLUG."_".$arrayIdent."_toggle_".$templateKey."_".$msgKey."' class='".WPPIZZA_SLUG."_".$arrayIdent."_details_toggle  wppizza-dashicons dashicons-edit' title='".__('edit', 'wppizza-admin')."'></span>";

									/*icon code edit (show css/style)*/
									$icons['css'] = '<span id="'.WPPIZZA_SLUG.'-dashicons-'.$arrayIdent.'-'.$templateKey.'-media-code-'.$msgKey.'" class="wppizza-dashicons dashicons-media-code '.$tplVals['htmlactiveclass'].'" title="'.$tplVals['htmlactivetitle'].'"></span>';

									/*icon preview*/
									$icons['preview'] = "<span  id='".WPPIZZA_SLUG."_".$arrayIdent."_".$templateKey."_preview-".$msgKey."' class='".WPPIZZA_SLUG."_".$arrayIdent."_preview wppizza-dashicons dashicons-visibility' title='".__('preview', 'wppizza-admin')."'></span>";

									/*icon delete - only for template id's >0 to always keep default, filterable in case other plugins use templates that are not used otherwise*/
									$prevent_delete = apply_filters('wppizza_prevent_template_delete', array(0), $templateKey);
									if(!in_array($msgKey, $prevent_delete )){
										$icons['delete'] = "<span id='".WPPIZZA_SLUG."_".$arrayIdent."_".$templateKey."_delete-".$msgKey."' class='".WPPIZZA_SLUG."_".$arrayIdent."_delete wppizza-dashicons dashicons-trash' title='".__('delete', 'wppizza-admin')."'></span>";
									}
									/* 
										allow adding / splicing icons before imploding for markup 
									*/
									$icons = apply_filters('wppizza_filter_template_icons_'.$templateKey.'', $icons, $templateKey, $msgKey, $arrayIdent);
									$markup .= implode('',$icons);
									
									
							$markup.='</td>';
						$markup.='</tr>';
					$markup.='</tbody>';
				$markup.='</table>';


				/******************************

					drag drop sections

				******************************/
				$markup.='<table id="'.WPPIZZA_SLUG.'-'.$arrayIdent.'-sections-'.$templateKey.'-'.$msgKey.'" class="'.WPPIZZA_SLUG.'-'.$arrayIdent.'-sections '.WPPIZZA_SLUG.'-'.$arrayIdent.'-sections-'.$templateKey.' widefat">';
					$markup.='<tbody>';
						$markup.='<tr class="'.WPPIZZA_SLUG.'-'.$arrayIdent.'-parts">';
						/*
						*	iterate through sections
						*/
						foreach($tplVals['sections'] as $section_key=>$section){

							$markup.='<td class="'.WPPIZZA_SLUG.'-'.$arrayIdent.'-section-'.$section_key.' '.WPPIZZA_SLUG.'-'.$arrayIdent.'-'.$templateKey.'-section-'.$section_key.'">';

								/*
									section on off and sort icon
								*/
								$markup.='<div class="'.WPPIZZA_SLUG.'-'.$arrayIdent.'-section-part-enable '.WPPIZZA_SLUG.'-'.$arrayIdent.'-section-'.$section_key.'-part-enable">';
									$markup.='<span>';
										$markup.='<label class="button">';
											$markup.='<input id="'.WPPIZZA_SLUG.'-'.$arrayIdent.'-part-'.$msgKey.'-'.$section_key.'" class="'.WPPIZZA_SLUG.'-'.$arrayIdent.'-part" name="'.WPPIZZA_SLUG.'['.$arrayIdent.']['.$templateKey.']['.$msgKey.'][sections]['.$section_key.'][section_enabled]" type="checkbox" value="1" '.checked(!empty($section['section_enabled']),true,false).' />';//'.$section_key.'
											$markup.= $section['labels']['label'] ;
										$markup.='</label>';
									$markup.='</span>';

									$markup.='<span class="wppizza-dashicons-leftright dashicons-leftright wppizza-'.$arrayIdent.'-sort-part" title="'.__('drag and drop to sort','wppizza-admin').'">';
										$markup.='<input name="'.WPPIZZA_SLUG.'['.$arrayIdent.']['.$templateKey.']['.$msgKey.'][sort]['.$section_key.']" type="hidden" value="1" />';//'.$section_key.'
									$markup.='</span>';

								$markup.='</div>';


								/*
									label enable wrap
								*/

								$markup.='<div class="'.WPPIZZA_SLUG.'-'.$arrayIdent.'-section-part-header '.WPPIZZA_SLUG.'-'.$arrayIdent.'-section-'.$section_key.'-part-header">';
								/*
									section header label on/off
								*/
									$markup.='<label>';
										$markup.='<input name="'.WPPIZZA_SLUG.'['.$arrayIdent.']['.$templateKey.']['.$msgKey.'][sections]['.$section_key.'][label_enabled]" type="checkbox" '.checked(!empty($section['label_enabled']),true,false).' value="1" />';
										/*label for order is made up of 3 labels*/
										if($section_key=='order'){
											$orderlbl=implode(' | ',$section['labels']['parameters']);
											$markup.=''.sprintf( __( 'Show "%1$s"', 'wppizza-admin' ), $orderlbl ).'';
										}else{
											$markup.=''.sprintf( __( 'Show "%1$s" label', 'wppizza-admin' ), $section['labels']['label'] ).'';
										}
									$markup.='</label>';

								$markup.='</div>';

								/*
									drag/drop on/off variables
								*/
								$markup.='<div class="'.WPPIZZA_SLUG.'-'.$arrayIdent.'-section-parts '.WPPIZZA_SLUG.'-'.$arrayIdent.'-section-parts-'.$section_key.' '.WPPIZZA_SLUG.'-'.$arrayIdent.'-section-parts-'.$section_key.'-'.$msgKey.'">';

									foreach($section['parameters'] as $section_value_key=>$section_value){
										$markup.='<div class="'.WPPIZZA_SLUG.'-'.$arrayIdent.'-sort-var-'.$section_value_key.' '.WPPIZZA_SLUG.'-'.$arrayIdent.'-'.$templateKey.'-sort-var-'.$section_value_key.'">';

										/*
											drag/drop icon and hidden input to save selected order
											regardless of whether checkbox was selected
										*/
										$markup.='<span class="'.WPPIZZA_SLUG.'-dashicons-small dashicons-editor-ol '.WPPIZZA_SLUG.'-'.$arrayIdent.'-sort-var" title="'.__('drag and drop to sort','wppizza-admin').'">';
												$markup.='<input id="'.WPPIZZA_SLUG.'-values-order.'.$templateKey.'-'.$msgKey.'.'.$section_key.'.'.$section_value_key.'" class="'.WPPIZZA_SLUG.'-values-order" class="'.WPPIZZA_SLUG.'-values-order" name="'.WPPIZZA_SLUG.'['.$arrayIdent.']['.$templateKey.']['.$msgKey.'][sort]['.$section_key.']['.$section_value_key.']" type="hidden" value="1" />';//'.$section_value_key.'
										$markup.='</span>';

										/*
											checkbox with label
										*/
										$markup.='<label title="'.__('show in template','wppizza-admin').'">';
											$markup.='<input id="'.WPPIZZA_SLUG.'-'.$arrayIdent.'-input-var-'.$msgKey.'-'.$section_key.'-'.$section_value_key.'"  class="'.WPPIZZA_SLUG.'-'.$arrayIdent.'-input-var" name="'.WPPIZZA_SLUG.'['.$arrayIdent.']['.$templateKey.']['.$msgKey.'][sections]['.$section_key.'][parameters]['.$section_value_key.'][enabled]" type="checkbox" value="1" '.checked(!empty($section_value['enabled']),true,false).' />';//'.$section_value_key.'
											$markup.= $section_value['label'] ;
										$markup.='</label>';

										$markup.='</div>';
									}
								$markup.='</div>';
							$markup.='</td>';
						}
						$markup.='</tr>';
					$markup.='</tbody>';
				$markup.='</table>';

				/******************************

					global styles

				******************************/
				$markup.='<table id="'.WPPIZZA_SLUG.'-'.$arrayIdent.'-global-styles-'.$templateKey.'-'.$msgKey.'" class="'.WPPIZZA_SLUG.'-'.$arrayIdent.'-global-styles '.WPPIZZA_SLUG.'-'.$arrayIdent.'-global-styles-'.$templateKey.' widefat">';
					$markup.='<tbody>';
						$markup.='<tr>';
							$markup.='<td>';

								$markup.='<label class="'.WPPIZZA_SLUG.'-'.$arrayIdent.'-global-styles-body '.WPPIZZA_SLUG.'-'.$arrayIdent.'-global-styles-body-'.$templateKey.'">';
									if($templateKey=='emails'){
										$markup.=''.__('Body - element style','wppizza-admin').'';
									}
									if($templateKey=='print'){
										$markup.=''.__('Print Order CSS','wppizza-admin').'';
									}
									$markup.='<textarea name="'.WPPIZZA_SLUG.'['.$arrayIdent.']['.$templateKey.']['.$msgKey.'][global_styles][body]">'.$tplVals['global_styles']['body'].'</textarea>';
								$markup.='</label>';



							if($templateKey=='emails'){

								$markup.='<label class="'.WPPIZZA_SLUG.'-'.$arrayIdent.'-global-styles-wrapper '.WPPIZZA_SLUG.'-'.$arrayIdent.'-global-styles-wrapper-'.$templateKey.'">';
									$markup.=''.__('Wrapper - element style','wppizza-admin').'';
									$markup.='<textarea name="'.WPPIZZA_SLUG.'['.$arrayIdent.']['.$templateKey.']['.$msgKey.'][global_styles][wrapper]">'.$tplVals['global_styles']['wrapper'].'</textarea>';
								$markup.='</label>';

								$markup.='<label class="'.WPPIZZA_SLUG.'-'.$arrayIdent.'-global-styles-table '.WPPIZZA_SLUG.'-'.$arrayIdent.'-global-styles-table-'.$templateKey.'">';
									$markup.=''.__('Main Table - element style','wppizza-admin').'';
									$markup.='<textarea name="'.WPPIZZA_SLUG.'['.$arrayIdent.']['.$templateKey.']['.$msgKey.'][global_styles][table]">'.$tplVals['global_styles']['table'].'</textarea>';
								$markup.='</label>';
							}

							$markup.='</td>';
						$markup.='</tr>';
					$markup.='</tbody>';
				$markup.='</table>';


				/******************************

					sections styles - emails only

				******************************/
				if($templateKey=='emails'){
				$markup.='<table id="'.WPPIZZA_SLUG.'-'.$arrayIdent.'-sections-styles-'.$templateKey.'-'.$msgKey.'" class="'.WPPIZZA_SLUG.'-'.$arrayIdent.'-sections-styles '.WPPIZZA_SLUG.'-'.$arrayIdent.'-sections-styles-'.$templateKey.' widefat">';
					$markup.='<tbody>';
						$markup.='<tr>';
						/*
						*	iterate through sections
						*/
						foreach($tplVals['sections'] as $section_key=>$section){
							$markup.='<td>';

								/*
									label
								*/
								$markup.='<div class="'.WPPIZZA_SLUG.'-'.$arrayIdent.'-section-styles-label">';
									$markup.=''.__('Section Styles','wppizza-admin').' - '.$section['labels']['label'].'';
								$markup.='</div>';

								/*
									style table
								*/
								$markup.='<div class="'.WPPIZZA_SLUG.'-'.$arrayIdent.'-section-styles-table">';
									$markup.='<label>'.__('Table','wppizza-admin').'</label>';
									$markup.='<textarea  name="'.WPPIZZA_SLUG.'['.$arrayIdent.']['.$templateKey.']['.$msgKey.'][sections]['.$section_key.'][style][table]">'.$section['style']['table'].'</textarea>';
								$markup.='</div>';

								/*
									style header
								*/
								$markup.='<div class="'.WPPIZZA_SLUG.'-'.$arrayIdent.'-section-styles-header">';
									$markup.='<label>'.__('Headers / Labels','wppizza-admin').'</label>';
									$markup.='<textarea  name="'.WPPIZZA_SLUG.'['.$arrayIdent.']['.$templateKey.']['.$msgKey.'][sections]['.$section_key.'][style][th]">'.$section['style']['th'].'</textarea>';
								$markup.='</div>';

								/*
									style columns all
								*/
								$markup.='<div class="'.WPPIZZA_SLUG.'-'.$arrayIdent.'-section-styles-columns '.WPPIZZA_SLUG.'-'.$arrayIdent.'-section-styles-columns-'.$section_key.'">';

									/**labels**/
									if($section_key=='order'){
										$markup.='<label>'.__('Order Details','wppizza-admin').'</label>';
									}else{
										$markup.='<label>'.__('Columns','wppizza-admin').'</label>';
									}

									/**site vars have one td only**/
									if($section_key=='site'){

										$markup.=''.__('Column All','wppizza-admin').'<br />';
										$markup.='<textarea  class="'.WPPIZZA_SLUG.'-'.$arrayIdent.'-parts-style '.WPPIZZA_SLUG.'-'.$arrayIdent.'-parts-style-ctr" name="'.WPPIZZA_SLUG.'['.$arrayIdent.']['.$templateKey.']['.$msgKey.'][sections]['.$section_key.'][style][td-ctr]">'.$section['style']['td-ctr'].'</textarea>';

									}else{

										$markup.='<div>';
											$markup.=''.__('Left Column All','wppizza-admin').'<br />';
											$markup.='<textarea  class="'.WPPIZZA_SLUG.'-'.$arrayIdent.'-parts-style '.WPPIZZA_SLUG.'-'.$arrayIdent.'-parts-style-lft" name="'.WPPIZZA_SLUG.'['.$arrayIdent.']['.$templateKey.']['.$msgKey.'][sections]['.$section_key.'][style][td-lft]">'.$section['style']['td-lft'].'</textarea>';
										$markup.='</div>';

										/*order vars have 3 columns*/
										if($section_key=='order'){
										$markup.='<div>';
											$markup.=''.__('Center Column All','wppizza-admin').'<br />';
											$markup.='<textarea  class="'.WPPIZZA_SLUG.'-'.$arrayIdent.'-parts-style '.WPPIZZA_SLUG.'-'.$arrayIdent.'-parts-style-ctr" name="'.WPPIZZA_SLUG.'['.$arrayIdent.']['.$templateKey.']['.$msgKey.'][sections]['.$section_key.'][style][td-ctr]">'.$section['style']['td-ctr'].'</textarea>';
										$markup.='</div>';
										}

										$markup.='<div>';
											$markup.=''.__('Right Column All','wppizza-admin').'<br />';
											$markup.='<textarea  class="'.WPPIZZA_SLUG.'-'.$arrayIdent.'-parts-style '.WPPIZZA_SLUG.'-'.$arrayIdent.'-parts-style-rgt" name="'.WPPIZZA_SLUG.'['.$arrayIdent.']['.$templateKey.']['.$msgKey.'][sections]['.$section_key.'][style][td-rgt]">'.$section['style']['td-rgt'].'</textarea>';
										$markup.='</div>';

									}

								$markup.='</div>';

								/*
									style rows
								*/
								$markup.='<div class="'.WPPIZZA_SLUG.'-'.$arrayIdent.'-section-styles-rows '.WPPIZZA_SLUG.'-'.$arrayIdent.'-section-styles-rows-'.$section_key.'">';

									/*skip for order items*/
									if($section_key != 'order'){

										$markup.='<label>'.__('Details','wppizza-admin').'</label>';

										foreach($section['parameters'] as $section_value_key=>$section_value){
											$markup.='<div>';
												$markup.= $section_value['label'].'<br />';
												$markup.='<textarea name="'.WPPIZZA_SLUG.'['.$arrayIdent.']['.$templateKey.']['.$msgKey.'][sections]['.$section_key.'][style]['.$section_value_key.'-tdall]" >'.$section['style'][''.$section_value_key.'-tdall'].'</textarea>';//$tplVals['style'][$section_key][''.$section_value_key.'-tdall']//'.$section_value[''.$section_key.'-tdall'].'
											$markup.='</div>';
										}

									}

									/* add styles for blogname and categories if displayed */
									if($section_key=='order'){

										$markup.='<label>'.__('Category / Blogname (if enabled)','wppizza-admin').'</label>';

										$markup.='<div>';
											$markup.=''.__('Blogname','wppizza-admin').'<br />';
											$markup.='<textarea  class="'.WPPIZZA_SLUG.'-'.$arrayIdent.'-parts-style '.WPPIZZA_SLUG.'-'.$arrayIdent.'-parts-style-blogname" name="'.WPPIZZA_SLUG.'['.$arrayIdent.']['.$templateKey.']['.$msgKey.'][sections]['.$section_key.'][style][td-blogname]">'.$section['style']['td-blogname'].'</textarea>';
										$markup.='</div>';

										$markup.='<div>';
											$markup.=''.__('Category','wppizza-admin').'<br />';
											$markup.='<textarea  class="'.WPPIZZA_SLUG.'-'.$arrayIdent.'-parts-style '.WPPIZZA_SLUG.'-'.$arrayIdent.'-parts-style-catname" name="'.WPPIZZA_SLUG.'['.$arrayIdent.']['.$templateKey.']['.$msgKey.'][sections]['.$section_key.'][style][td-catname]">'.$section['style']['td-catname'].'</textarea>';
										$markup.='</div>';
									}

								$markup.='</div>';

							$markup.='</td>';
						}
						$markup.='</tr>';
					$markup.='</tbody>';
				$markup.='</table>';
				}


				$markup.='</div>';

		return $markup;
		}

/***************************************************************************************************************************************************************************************************************************
*
*
*
*	plaintext template parts
*
*
*
****************************************************************************************************************************************************************************************************************************/
	function get_template_email_plaintext_sections_markup($order_formatted, $template_parameters, $template_type, $template_contents = ''){
		$tpl = array();


		/**
			loop through sections in the order they were set
			when saving the drag drop template
		**/

		if(isset($template_parameters['sections'])){
		foreach($template_parameters['sections'] as $section_key => $section_values){
		if(!empty($section_values['section_enabled'])){


			/*
				allow filtering to override whats saved in templates
			*/
			$section_values['parameters'] = apply_filters('wppizza_filter_template_section_parameters', $section_values['parameters'], $section_key, $template_parameters, $template_type);



			/**************************************
			*
			*
			*	site vars
			*
			*
			**************************************/
			if($section_key == 'site'){
				$tpl[$section_key] = array();

				/***************************************
					label / legend
				****************************************/
				if(!empty($section_values['label_enabled'])){
					$tpl[$section_key]['section_label'] = trim($order_formatted['localization']['templates_label_'.$section_key.'']);
					/* filter for even spacing **/
					$tpl[$section_key]['section_label'] = apply_filters('wppizza_filter_plaintext_line', $tpl[$section_key]['section_label'], ' ');
				}

				/***************************************
					rows
				***************************************/
				foreach($section_values['parameters'] as $value_key => $values){

					if(!empty($values['enabled']) && !empty($order_formatted['sections'][$section_key][$value_key])){

						/* centered, without labels */
						$row = array();
						$row['ctr'] = $order_formatted['sections'][$section_key][$value_key]['value_formatted'];

						/** line **/
						$tpl[$section_key][$value_key] = apply_filters('wppizza_filter_plaintext_line', $row, ' ');

					}
				}

				/****************************************
					implode label and details for output
				****************************************/
				$tpl[$section_key] = implode(PHP_EOL,$tpl[$section_key]);
				/* allow filtering of whole section string (maybe using regexes) */
				$tpl[$section_key]  = apply_filters('wppizza_filter_template_markup_plaintext_'.$section_key.'', $tpl[$section_key], $template_type);
			}


			/**************************************
			*
			*
			*	general order parameters
			*
			*
			**************************************/
			if($section_key == 'ordervars'){

				$tpl[$section_key] = array();

				/***************************************
					label / legend
				****************************************/
				if(!empty($section_values['label_enabled'])){

					$tpl[$section_key]['section_label'] = trim($order_formatted['localization']['templates_label_'.$section_key.'']);
					/* filter for even spacing **/
					$tpl[$section_key]['section_label'] = apply_filters('wppizza_filter_plaintext_line', $tpl[$section_key]['section_label'], ' ');
				}

				/***************************************
					rows
				***************************************/
				foreach($section_values['parameters'] as $value_key => $values){

					if(!empty($values['enabled']) && !empty($order_formatted['sections'][$section_key][$value_key])){

						/* left label, right value using filter to space evenly*/
						$row = array();
						$row['lft'] = $order_formatted['sections'][$section_key][$value_key]['label'];
						$row['rgt'] = $order_formatted['sections'][$section_key][$value_key]['value_formatted'];

						/** line **/
						$tpl[$section_key][$value_key] = apply_filters('wppizza_filter_plaintext_line', $row, ' ');

					}

				}

			/** implode for output */
			$tpl[$section_key] = implode(PHP_EOL,$tpl[$section_key]);
			/* allow filtering of whole section string (maybe using regexes) */
			$tpl[$section_key]  = apply_filters('wppizza_filter_template_markup_plaintext_'.$section_key.'', $tpl[$section_key], $template_type);
			}


			/**************************************
			*
			*
			*	customer vars
			*
			*
			**************************************/
			if($section_key == 'customer'){
				$tpl[$section_key] = array();


				/***************************************
					label / legend
				****************************************/
				if(!empty($section_values['label_enabled'])){

					$tpl[$section_key]['section_label'] = trim($order_formatted['localization']['templates_label_'.$section_key.'']);
					/* filter for even spacing **/
					$tpl[$section_key]['section_label'] = apply_filters('wppizza_filter_plaintext_line', $tpl[$section_key]['section_label'], ' ');
				}

				/***************************************
					rows
				***************************************/
				foreach($section_values['parameters'] as $value_key => $values){

					if(!empty($values['enabled']) && !empty($order_formatted['sections'][$section_key][$value_key])){

						/* left label, right value using filter to space evenly*/
						$row = array();
						$row['lft'] = $order_formatted['sections'][$section_key][$value_key]['label'];
						$row['rgt'] = $order_formatted['sections'][$section_key][$value_key]['value'];

						/** line **/
						$tpl[$section_key][$value_key] = apply_filters('wppizza_filter_plaintext_line', $row, ' ');

					}

				}

			/** implode for output */
			$tpl[$section_key] = implode(PHP_EOL,$tpl[$section_key]);
			/* allow filtering of whole section string (maybe using regexes) */
			$tpl[$section_key]  = apply_filters('wppizza_filter_template_markup_plaintext_'.$section_key.'', $tpl[$section_key], $template_type);
			}


			/**************************************
			*
			*
			*	itemised order vars
			*
			*
			**************************************/
			if($section_key == 'order'){

				/***************************************
					get order column data
				***************************************/
				$order_columns = WPPIZZA()->helpers->itemised_order_columns($order_formatted, $template_parameters, $template_type);

				/***************************************
					ini array
				***************************************/
				$tpl[$section_key] = array();

				/***************************************
					label / legend - spaced by "-"
				****************************************/
				if(!empty($section_values['label_enabled'])){
					$tpl[$section_key]['section_label'] = $order_columns['column_label']['plaintext'];
				}

				/****************************************
					itemised order rows
				****************************************/
				$tpl[$section_key]['itemised'] = $order_columns['itemised']['plaintext'];


				/****************************************
					implode label and details for output
				****************************************/
				$tpl[$section_key] = implode(PHP_EOL,$tpl[$section_key]);
				/* allow filtering of whole section string (maybe using regexes) */
				$tpl[$section_key]  = apply_filters('wppizza_filter_template_markup_plaintext_'.$section_key.'', $tpl[$section_key], $template_type);
			}



			/**************************************
			*
			*
			*	summary vars
			* - todo template vars need to match
			*
			**************************************/
			if($section_key == 'summary'){
				$tpl[$section_key] = array();

				/***************************************
					label / legend
				****************************************/
				if(!empty($section_values['label_enabled'])){

					$tpl[$section_key]['section_label'] = trim($order_formatted['localization']['templates_label_'.$section_key.'']);
					/* filter for even spacing **/
					$tpl[$section_key]['section_label'] = apply_filters('wppizza_filter_plaintext_line', $tpl[$section_key]['section_label'], ' ');
				}

				/***************************************
					rows
				***************************************/
				foreach($section_values['parameters'] as $value_key => $values){

					if(!empty($values['enabled']) && !empty($order_formatted['sections'][$section_key][$value_key])){
						foreach($order_formatted['sections'][$section_key][$value_key] as $vKey => $vals){
							/* left label, right value using filter to space evenly*/
							$row = array();
							$row['lft'] = $vals['label'];
							$row['rgt'] = $vals['value_formatted'];

							/** line **/
							$tpl[$section_key][$value_key.'_'.$vKey] = apply_filters('wppizza_filter_plaintext_line', $row, ' ');
						}
					}

				}

			/** implode for output */
			$tpl[$section_key] = implode(PHP_EOL,$tpl[$section_key]);
			/* allow filtering of whole section string (maybe using regexes) */
			$tpl[$section_key]  = apply_filters('wppizza_filter_template_markup_plaintext_'.$section_key.'', $tpl[$section_key], $template_type);
			}
		}}}

		/**************************************
		*
		*	if using get_skeleton_template with content set
		*	adding hardcoded divider
		**************************************/
		if(!empty($template_contents)){

			$section_key = 'contents';
			$tpl[$section_key.'_divider'] = apply_filters('wppizza_filter_plaintext_line', '', '=');
			$tpl[$section_key] = apply_filters('wppizza_filter_plaintext_line', $template_contents, ' ', true);

		}
		/**************************************
		*
		*
		*	add footer after everything else
		*	emails only (for now)
		*
		**************************************/
		if($template_type=='emails'){
			$section_key = 'footer';
			$tpl[$section_key] = array();
			/** add centered note after **/
			$tpl[$section_key]['divider'] = apply_filters('wppizza_filter_plaintext_line', '', '=');
			if(!empty($order_formatted['localization']['templates_footer_note'])){
				$tpl[$section_key]['footer_note'] = apply_filters('wppizza_filter_plaintext_line', $order_formatted['localization']['templates_footer_note'], ' ', true);
			}
			/** implode for output */
			$tpl[$section_key] = implode(PHP_EOL,$tpl[$section_key]);
			/* allow filtering of whole section string (maybe using regexes) */
			$tpl[$section_key]  = apply_filters('wppizza_filter_template_markup_plaintext_'.$section_key.'', $tpl[$section_key], $template_type);
		}


		/* allow filtering of each section (maybe using regexes) */
		$tpl  = apply_filters('wppizza_filter_template_markup_plaintext', $tpl, $template_type);
		/** output as string **/
		$tpl['markup'] = implode(PHP_EOL.PHP_EOL,$tpl);

	return $tpl;
	}
/**********************************************************************************************************************************************************************************************************************************
*
*
*
*	html templates
*
*
*
***********************************************************************************************************************************************************************************************************************************/
	function get_template_email_html_sections_markup($order_formatted, $template_parameters, $template_type, $template_id, $template_contents = ''){

		$tpl = array();

		/**
			loop through sections in the order they were set
			when saving the drag drop template
		**/
		if(isset($template_parameters['sections'])){
		foreach($template_parameters['sections'] as $section_key => $section_values){


			/*
				allow filtering to override/add what's saved in templates
			*/
			$section_values['parameters'] = apply_filters('wppizza_filter_template_section_parameters', $section_values['parameters'], $section_key, $template_parameters, $template_type);

			/*
				allow filtering of html style to override/add what's saved in templates
				hook updated for 3.1.3 to only apply to this section
				adding template id and order_formatted[sections] to pass on all order parameters
			*/
			$section_values['style'] = empty($section_values['style']) ? array() : $section_values['style'];
			$section_values['style'] = apply_filters('wppizza_filter_template_section_'.$section_key.'_styles', $section_values['style'], $template_parameters['sections'][$section_key], $template_type, $template_id, $order_formatted['sections']);

			if(!empty($section_values['section_enabled'])){

				/**********************************************************
				*
				*
				*	site vars - typically labelled "Site Details" in Wppizza -> Templates
				*
				*
				**********************************************************/
				if($section_key == 'site'){
					$tpl[$section_key] = array();

					/***************************************
						label / legend
					****************************************/
					if(!empty($section_values['label_enabled'])){

						$section_label = trim($order_formatted['localization']['templates_label_'.$section_key.'']);

						/***wrap in tr/th, omit style declarations on print **/
						$thStyle=($template_type=='print') ? '' : ' style="'.$section_values['style']['th'].'"';
						$tpl[$section_key]['section_label'] ='<thead>'.PHP_EOL.'<tr>'.PHP_EOL.'<th '.$thStyle.'>'.PHP_EOL.''.$section_label.''.PHP_EOL.'</th>'.PHP_EOL.'</tr>'.PHP_EOL.'</thead>'.PHP_EOL;

					}
					/****************************************
						wrap in tbody
					****************************************/
					$tpl[$section_key]['tbody_'] ='<tbody>'.PHP_EOL;

					/***************************************
						rows
					***************************************/
					foreach($section_values['parameters'] as $value_key => $values){

						if(!empty($values['enabled']) && !empty($order_formatted['sections'][$section_key][$value_key])){
							/* centered, without labels */
							$row = array();
							$row['ctr'] = $order_formatted['sections'][$section_key][$value_key]['value_formatted'];

							/***wrap in tr/td**/
							if($template_type=='emails'){
								$set_email_td_all = !empty($section_values['style'][$value_key.'-tdall']) ? $section_values['style'][$value_key.'-tdall'] : '' ;//remove potential php notices
								$tpl[$section_key][$value_key] ='<tr><td style="'.$section_values['style']['td-ctr'].';'.$set_email_td_all.'">'.$row['ctr'].'</td></tr>'.PHP_EOL;
							}
							if($template_type=='print'){
								$tpl[$section_key][$value_key] ='<tr id="'.$value_key.'"><td>'.$row['ctr'].'</td></tr>'.PHP_EOL;
							}

						}
					}

					/****************************************
						close tbody
					****************************************/
					 $tpl[$section_key]['_tbody'] ='</tbody>'.PHP_EOL;

					/****************************************
						implode for output
					****************************************/
					$tpl[$section_key] = apply_filters('wppizza_filter_template_'.$section_key.'_section', $tpl[$section_key], $template_type, $template_id);
					$tpl[$section_key] = implode(PHP_EOL,$tpl[$section_key]);

					/****************************************
						wrap in table
					****************************************/
					if($template_type=='emails'){
						$tpl[$section_key]='<table id="'.$section_key.'" style="width:100%;'.$section_values['style']['table'].'">'.PHP_EOL.''.$tpl[$section_key].'</table>'.PHP_EOL.'';
					}
					if($template_type=='print'){
						$tpl[$section_key]='<table id="header">'.PHP_EOL.''.$tpl[$section_key].'</table>'.PHP_EOL.'';
					}
				}

				/**********************************************************
				*
				*
				*	general order parameters - typically labelled "Overview" in Wppizza -> Templates
				*
				*
				**********************************************************/
				if($section_key == 'ordervars'){

					$tpl[$section_key] = array();

					/***************************************
						label / legend
					****************************************/
					if(!empty($section_values['label_enabled'])){

						$section_label = trim($order_formatted['localization']['templates_label_'.$section_key.'']);

						/***wrap in tr/th, omit style declarations on print **/
						$thStyle=($template_type=='print') ? '' : ' style="'.$section_values['style']['th'].'"';
						$tpl[$section_key]['section_label'] ='<thead>'.PHP_EOL.'<tr>'.PHP_EOL.'<th  colspan="2" '.$thStyle.'>'.PHP_EOL.''.$section_label.''.PHP_EOL.'</th>'.PHP_EOL.'</tr>'.PHP_EOL.'</thead>'.PHP_EOL;
					}

					/****************************************
						wrap in tbody
					****************************************/
					$tpl[$section_key]['tbody_'] ='<tbody>'.PHP_EOL;

					/***************************************
						rows
					***************************************/


					foreach($section_values['parameters'] as $value_key => $values){

						if(!empty($values['enabled']) && !empty($order_formatted['sections'][$section_key][$value_key])){

							/* left label, right value using filter to space evenly*/
							$row = array();
							$row['lft'] = $order_formatted['sections'][$section_key][$value_key]['label'];
							$row['rgt'] = $order_formatted['sections'][$section_key][$value_key]['value_formatted'];

							/**date, pickup/delivery note  or admin notes need no label**/
							if($value_key=='order_date' ||  $value_key=='pickup_delivery' ||  $value_key=='admin_notes'){
								/***wrap in tr/td**/
								if($template_type=='emails'){
									$set_email_td_all = !empty($section_values['style'][$value_key.'-tdall']) ? $section_values['style'][$value_key.'-tdall'] : '' ;//remove potential php notices
									$tpl[$section_key][$value_key] ='<tr><td colspan="2" style="'.$set_email_td_all.'">'.$row['rgt'].'</td></tr>'.PHP_EOL;
								}
								if($template_type=='print'){
									if( $value_key=='admin_notes'){/* use nl2br for admin notes */
										$tpl[$section_key][$value_key] ='<tr id="'.$value_key.'"><td colspan="2">'.nl2br($row['rgt']).'</td></tr>'.PHP_EOL;
									}else{
										$tpl[$section_key][$value_key] ='<tr id="'.$value_key.'"><td colspan="2">'.$row['rgt'].'</td></tr>'.PHP_EOL;
									}
								}
							}else{
								/***wrap in tr/td**/
								if($template_type=='emails'){
									$set_email_td_all = !empty($section_values['style'][$value_key.'-tdall']) ? $section_values['style'][$value_key.'-tdall'] : '' ;//remove potential php notices
									$tpl[$section_key][$value_key] ='<tr><td style="'.$section_values['style']['td-lft'].';'.$set_email_td_all.'">'.$row['lft'].'</td><td style="'.$section_values['style']['td-rgt'].';'.$set_email_td_all.'">'.$row['rgt'].'</td></tr>'.PHP_EOL;
								}
								if($template_type=='print'){
									$tpl[$section_key][$value_key] ='<tr id="'.$value_key.'"><td>'.implode('</td><td>',$row).'</td></tr>'.PHP_EOL;
								}

							}
						}
					}

					/****************************************
						close tbody
					****************************************/
					 $tpl[$section_key]['_tbody'] ='</tbody>'.PHP_EOL;

					/****************************************
						implode for output
					****************************************/
					$tpl[$section_key] = apply_filters('wppizza_filter_template_'.$section_key.'_section', $tpl[$section_key], $template_type, $template_id);
					$tpl[$section_key] = implode(PHP_EOL, $tpl[$section_key]);

					/****************************************
						wrap in table
					****************************************/
					if($template_type=='emails'){
						$tpl[$section_key]='<table id="'.$section_key.'" style="width:100%;'.$section_values['style']['table'].'">'.PHP_EOL.''.$tpl[$section_key].'</table>'.PHP_EOL.'';
					}
					if($template_type=='print'){
						$tpl[$section_key]='<table id="overview">'.PHP_EOL.''.$tpl[$section_key].'</table>'.PHP_EOL.'';
					}
				}

				/**********************************************************
				*
				*
				*	customer vars - typically labelled "Customer Details" in Wppizza -> Templates
				*
				*
				**********************************************************/
				if($section_key == 'customer'){

					$tpl[$section_key] = array();

					/***************************************
						label / legend
					****************************************/
					if(!empty($section_values['label_enabled'])){

						$section_label = trim($order_formatted['localization']['templates_label_'.$section_key.'']);

						/***wrap in tr/th, omit style declarations on print **/
						$thStyle=($template_type=='print') ? '' : ' style="'.$section_values['style']['th'].'"';
						$tpl[$section_key]['section_label'] ='<thead>'.PHP_EOL.'<tr>'.PHP_EOL.'<th  colspan="2" '.$thStyle.'>'.PHP_EOL.''.$section_label.''.PHP_EOL.'</th>'.PHP_EOL.'</tr>'.PHP_EOL.'</thead>'.PHP_EOL;
					}

					/****************************************
						wrap in tbody
					****************************************/
					$tpl[$section_key]['tbody_'] ='<tbody>'.PHP_EOL;

					/***************************************
						rows
					***************************************/
					foreach($section_values['parameters'] as $value_key => $values){

						if(!empty($values['enabled']) && !empty($order_formatted['sections'][$section_key][$value_key])){

							/* left label, right value using filter to space evenly*/
							$row = array();
							$row['lft'] = $order_formatted['sections'][$section_key][$value_key]['label'];
							$row['rgt'] = $order_formatted['sections'][$section_key][$value_key]['value'];

							/***wrap in tr/td**/
							if($template_type=='emails'){
								$set_email_td_all = !empty($section_values['style'][$value_key.'-tdall']) ? $section_values['style'][$value_key.'-tdall'] : '' ;//remove potential php notices
								$tpl[$section_key][$value_key] ='<tr><td style="'.$section_values['style']['td-lft'].';'.$set_email_td_all.'">'.$row['lft'].'</td><td style="'.$section_values['style']['td-rgt'].';'.$set_email_td_all.'">'.$row['rgt'].'</td></tr>'.PHP_EOL;
							}
							if($template_type=='print'){
								$tpl[$section_key][$value_key] ='<tr id="'.$value_key.'"><td>'.implode('</td><td>',$row).'</td></tr>'.PHP_EOL;
							}
						}
					}

					/****************************************
						close tbody
					****************************************/
					 $tpl[$section_key]['_tbody'] ='</tbody>'.PHP_EOL;

					/****************************************
						implode for output
					****************************************/
					$tpl[$section_key] = apply_filters('wppizza_filter_template_'.$section_key.'_section', $tpl[$section_key], $template_type, $template_id);
					$tpl[$section_key] = implode(PHP_EOL,$tpl[$section_key]);

					/****************************************
						wrap in table
					****************************************/
					if($template_type=='emails'){
						$tpl[$section_key]='<table id="'.$section_key.'" style="width:100%;'.$section_values['style']['table'].'">'.PHP_EOL.''.$tpl[$section_key].'</table>'.PHP_EOL.'';
					}
					if($template_type=='print'){
						$tpl[$section_key]='<table id="'.$section_key.'">'.PHP_EOL.''.$tpl[$section_key].'</table>'.PHP_EOL.'';
					}
				}
				/**********************************************************
				*
				*
				*	itemised order vars - typically labelled "Order Details" in Wppizza -> Templates
				*
				*
				**********************************************************/
				if($section_key == 'order'){

					/***************************************
						get order column data
					***************************************/
					$order_columns = WPPIZZA()->helpers->itemised_order_columns($order_formatted, $template_parameters, $template_type);

					/***************************************
						ini array
					***************************************/
					$tpl[$section_key] = array();

					/***************************************
						label / legend - wrapped in <thead>
					****************************************/
					if(!empty($section_values['label_enabled'])){
						$tpl[$section_key]['section_label'] = $order_columns['column_label']['html'];
					}

					/****************************************
						itemised order rows - wrapped in <tbody>
					****************************************/
					$tpl[$section_key]['itemised'] = $order_columns['itemised']['html'];

					/****************************************
						implode for output
					****************************************/
					$tpl[$section_key] = apply_filters('wppizza_filter_template_'.$section_key.'_section', $tpl[$section_key], $template_type, $template_id);
					$tpl[$section_key] = implode(PHP_EOL,$tpl[$section_key]);

					/****************************************
						wrap in <table>
					****************************************/
					if($template_type=='emails'){
						$tpl[$section_key]='<table id="'.$section_key.'" style="width:100%;'.$section_values['style']['table'].'">'.PHP_EOL.''.$tpl[$section_key].'</table>'.PHP_EOL.'';
					}
					if($template_type=='print'){
						$tpl[$section_key]='<table id="'.$section_key.'">'.PHP_EOL.''.$tpl[$section_key].'</table>'.PHP_EOL.'';
					}
				}
				/**********************************************************
				*
				*
				*	summary vars - typically labelled "Summary" in Wppizza -> Templates
				*
				*
				**********************************************************/
				if($section_key == 'summary'){

					$tpl[$section_key] = array();

					/***************************************
						label / legend
					****************************************/
					if(!empty($section_values['label_enabled'])){

						$section_label = trim($order_formatted['localization']['templates_label_'.$section_key.'']);

						/***wrap in tr/th, omit style declarations on print **/
						$thStyle=($template_type=='print') ? '' : ' style="'.$section_values['style']['th'].'"';
						$tpl[$section_key]['section_label'] ='<thead>'.PHP_EOL.'<tr>'.PHP_EOL.'<th  colspan="2" '.$thStyle.'>'.PHP_EOL.''.$section_label.''.PHP_EOL.'</th>'.PHP_EOL.'</tr>'.PHP_EOL.'</thead>'.PHP_EOL;
					}

					/****************************************
						wrap in tbody
					****************************************/
					$tpl[$section_key]['tbody_'] ='<tbody>'.PHP_EOL;

					/***************************************
						rows
					***************************************/
					foreach($section_values['parameters'] as $value_key => $values){

						if(!empty($values['enabled']) && !empty($order_formatted['sections'][$section_key][$value_key])){
							foreach($order_formatted['sections'][$section_key][$value_key] as $vKey => $vals){
								/* left label, right value using filter to space evenly*/
								$row = array();
								$row['lft'] = $vals['label'];
								$row['rgt'] = $vals['value_formatted'];

								/***wrap in tr/td**/
								if($template_type=='emails'){
									$set_email_td_all = !empty($section_values['style'][$value_key.'-tdall']) ? $section_values['style'][$value_key.'-tdall'] : '' ;//remove potential php notices
									$tpl[$section_key][$value_key.'_'.$vKey] ='<tr><td style="'.$section_values['style']['td-lft'].';'.$set_email_td_all.'">'.$row['lft'].'</td><td style="'.$section_values['style']['td-rgt'].';'.$set_email_td_all.'">'.$row['rgt'].'</td></tr>'.PHP_EOL;
								}
								if($template_type=='print'){
									$tpl[$section_key][$value_key.'_'.$vKey] ='<tr id="'.$value_key.'"><td>'.implode('</td><td>',$row).'</td></tr>'.PHP_EOL;
								}
							}
						}
					}

					/****************************************
						close tbody
					****************************************/
					 $tpl[$section_key]['_tbody'] ='</tbody>'.PHP_EOL;

					/****************************************
						implode for output
					****************************************/
					$tpl[$section_key] = apply_filters('wppizza_filter_template_'.$section_key.'_section', $tpl[$section_key], $template_type, $template_id);
					$tpl[$section_key] = implode(PHP_EOL,$tpl[$section_key]);

					/****************************************
						wrap in table
					****************************************/
					if($template_type=='emails'){
						$tpl[$section_key]='<table id="'.$section_key.'" style="width:100%;'.$section_values['style']['table'].'">'.PHP_EOL.''.$tpl[$section_key].'</table>'.PHP_EOL.'';
					}
					if($template_type=='print'){
						$tpl[$section_key]='<table id="'.$section_key.'">'.PHP_EOL.''.$tpl[$section_key].'</table>'.PHP_EOL.'';
					}
				}

			}
		}}

		/**************************************
		*
		*	if using get_skeleton_template with content set
		*	with some hardcoded minimum padding
		**************************************/
		if(!empty($template_contents)){

			$section_key = 'contents';

			$tpl[$section_key] = '<table id="'.$section_key.'"><tbody><tr><td style="padding:30px 10px">' . $template_contents . '</td></tr></tbody></table>'.PHP_EOL.'';
		}

		/***********************************************************************
		*
		*	[add footer text in emails after everything else]
		*
		***********************************************************************/
		if($template_type=='emails'){
			$section_key = 'footer';
			$tpl[$section_key] = array();

			if(!empty($order_formatted['localization']['templates_footer_note'])){
				$tpl[$section_key]['footer_note'] = $order_formatted['localization']['templates_footer_note'];
			}

			/* make it filterable just like all other sections */
			$tpl[$section_key] = apply_filters('wppizza_filter_template_'.$section_key.'_section', $tpl[$section_key], $template_type, $template_id);


			if(count($tpl[$section_key])>0){
				$tpl[$section_key]='<table id="'.$section_key.'" style="width:100%;text-align:center;font-size:90%"><tr><td>'.PHP_EOL.''.implode(PHP_EOL,$tpl[$section_key]).'</td></tr></table>'.PHP_EOL.'';
			}else{
				$tpl[$section_key]='';
			}

		}

		/**
			filterable array sections
			output as string
		**/
		$tpl = apply_filters('wppizza_filter_template_markup', $tpl, $template_type);
		$templateMarkup = implode(PHP_EOL.PHP_EOL, $tpl);

		/**************************************************************************************************************************
		*
		*
		*
		*	[html elements wrapper to insert it into]
		*
		*
		*
		**************************************************************************************************************************/
			$html='';
			$html.='<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">'.PHP_EOL;
			$html.='<html xmlns="http://www.w3.org/1999/xhtml">'.PHP_EOL;
			$html.='<head>'.PHP_EOL;
				$html.='<title></title>'.PHP_EOL;
				$html.='<meta http-equiv="Content-Type" content="text/html;charset='.get_option('blog_charset').'" />'.PHP_EOL;
				if($template_type=='print'){
					$html.='<style type="text/css">'.apply_filters('wppizza_filter_templates_html_print_css',$template_parameters['global_styles']['body']).'</style>';
				}
			$html.='</head>'.PHP_EOL;
			if($template_type=='emails'){
				$html.='<body style="'.$template_parameters['global_styles']['body'].'">'.PHP_EOL;
			}
			if($template_type=='print'){
				$html.='<body>'.PHP_EOL;
			}
				/*only wrap for emails*/
				if($template_type=='emails'){
					$html.='<table style="border-collapse:collapse;'.$template_parameters['global_styles']['wrapper'].'">'.PHP_EOL;
						$html.='<tr>'.PHP_EOL;
							$html.='<td>'.PHP_EOL;
								$html.='<center>'.PHP_EOL;
									$html.='<table style="border-collapse:collapse;'.$template_parameters['global_styles']['table'].'">'.PHP_EOL;
										$html.='<tbody>'.PHP_EOL;
											$html.='<tr>'.PHP_EOL;
												$html.='<td>'.PHP_EOL;
				}

				/**
					insert content into body
				**/
				$html.=$templateMarkup.PHP_EOL;

				/**
					close tags
				**/
				/*only wrap td/tr/table/center etc for emails*/
				if($template_type=='emails'){
												$html.='</td>'.PHP_EOL;
											$html.='</tr>'.PHP_EOL;
										$html.='</tbody>'.PHP_EOL;
									$html.='</table>'.PHP_EOL;
								$html.='</center>'.PHP_EOL;
							$html.='</td>'.PHP_EOL;
						$html.='</tr>'.PHP_EOL;
					$html.='</table>'.PHP_EOL;
				}
			$html.='</body>'.PHP_EOL;
			$html.='</html>'.PHP_EOL;
		return $html;
	}

	/*********************************************************
	*	[get email/print skeleton template by id or shop/customer
	* 	inserting content between header and footer]
	*	@since 3.9.2
	*	@param str. emails or print
	*	@param mixed (id or id/shop/customer for email templates). omit to use default
	*	@param str the content we want to use
	*	@return array of message and message type (html/plaintext)
	*********************************************************/
	function get_skeleton_template($type = 'emails' , $id = 0, $content = ''){
		global $wppizza_options, $blog_id;

		/* skip if type is wrong */
		if($type != 'emails' && $type != 'print' ){
			return '';
		}


		/**** distinguish between email and print templates ****/
		/*
			get id/parameters - emails
		*/
		if($type == 'emails'){

			/*
				get templates for this type
			*/
			$templates = get_option(WPPIZZA_SLUG.'_templates_'.$type);


			/* template used for shop emails */
			if($id == 'shop' ){
				$template_id = $wppizza_options['templates_apply']['emails']['recipients_default']['email_shop'];
			}
			/* template used for customer emails */
			elseif($id == 'customer' ){
				$template_id = $wppizza_options['templates_apply']['emails']['recipients_default']['email_customer'];
			}
			else{
				$template_id = (int)$id;
			}

			/* if it does not exist, simply return the first one */
			if(!isset($templates[$template_id])){
				$template_id = 0;
			}

			/*
				set templates parameters
			*/
			$templates = $templates[$template_id];

		}
		/*
			get id/parameters - print
		*/
		if($type == 'print'){
			/*
				get templates for this type
			*/
			$templates = get_option(WPPIZZA_SLUG.'_templates_'.$type);

			/* default template */
			if($id == 'default' ){
				$template_id =  $wppizza_options['templates_apply']['print'];
			}

			/* if set id does not exist, return the default */
			if(!isset($templates[$template_id])){
				$template_id = 0;
			}

			/*
				set templates parameters
			*/
			$templates = $templates[$template_id];
		}
		/**** end distinction between emails and print ****/


		/*
			force disable of everything but header and footer
		*/
		$templates['sections']['ordervars']['section_enabled'] = false;//test if thsi should be disabled actually!!!
		$templates['sections']['customer']['section_enabled'] = false;
		$templates['sections']['order']['section_enabled'] = false;
		$templates['sections']['summary']['section_enabled'] = false;


		/*
			set order dummy parameters for header to pass on
			it really is only the site details blog info
			that gets passed on to the header as we are completely
			omitting any order parameters.
			Afer all, the point of this is to simply display a skeleton
			template including wrapper, and header / footer only
			with the main information that is normally the order details
			to be replaced as needed
		*/
		$dummy_order = array();
		$dummy_order['blog_info'] = wppizza_get_blog_details($blog_id);
		$dummy_order['blog_options']['localization'] = $wppizza_options['localization'];


		/* skeleton param */
		$skeleton_formatted = array();
		$skeleton_formatted['sections'] = array();
		$skeleton_formatted['sections']['site'] = 	WPPIZZA()->order->site_details_formatted($dummy_order, false);
		$skeleton_formatted['localization'] = $wppizza_options['localization'];
		$skeleton_formatted['blog_info'] = $dummy_order['blog_info'];
		$skeleton_formatted['date_format'] = wppizza_get_blog_dateformat();


		/*
			get layout type
			html/plaintext
		*/
		$template_type = $templates['mail_type'] == 'wp_mail' ? 'text/plain' : 'text/html';


		/*
			get output depending on format
		*/
		$message = '';

		if($template_type == 'text/html'){
			$message = WPPIZZA() -> templates_email_print -> get_template_email_html_sections_markup($skeleton_formatted, $templates, $type, $template_id, $content);
		}else{
			$message = WPPIZZA() -> templates_email_print -> get_template_email_plaintext_sections_markup($skeleton_formatted, $templates, $type, $content);
			$message = $message['markup'];
		}

		/*
			return message and message type (html/plaintext)
		*/
		$param = array();
		$param['type'] = $template_type;
		$param['message'] = $message;

	return $param;
	}
}
?>