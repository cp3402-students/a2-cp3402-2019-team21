<?php
/**
* WPPIZZA_HELPERS Class
*
* @package     WPPIZZA
* @subpackage  WPPIZZA_HELPERS
* @copyright   Copyright (c) 2015, Oliver Bach
* @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
* @since       3.0
*
*/
if ( ! defined( 'ABSPATH' ) ) exit;/*Exit if accessed directly*/


/************************************************************************************************************************
*
*
*	WPPIZZA_HELPERS
*
*
************************************************************************************************************************/
class WPPIZZA_HELPERS{

	function __construct() {
	}

	/*************************************************************
	*
	*	email templates helper email_shop/email_customer
	*	(for consistancy across all functions)
	*
	*************************************************************/
	function default_email_recipients($val=false){

		$default_recipients=array();

		$default_recipients['email_shop']['lbl'] = __('shop and bccs','wppizza-admin');
		$default_recipients['email_shop']['ini_val'] = 0;

		$default_recipients['email_customer']['lbl']=__('customer','wppizza-admin');
		$default_recipients['email_customer']['ini_val'] = 0;


		$recipients=array();
		foreach($default_recipients as $rKey=>$rVal){
			/*by default, return labels, otherwise ini values (e.g on install)*/
			if(!$val){
				$recipients[$rKey] = $rVal['lbl'];
			}else{
				$recipients[$rKey] = $rVal['ini_val'];
			}
		}
		return $recipients;
	}


	/***************************************

		[get enabled formfiels confirmation form]

	***************************************/
	function enabled_confirmation_formfields($keys_only = false, $enabled_only = true){
		global $wppizza_options;

		/*get all enabled form fields **/
		$enabled_formfields = array();
		foreach($wppizza_options['confirmation_form'] as $key=>$formfield){
			/* enabled only */
			if($enabled_only){
				if($formfield['enabled']){
					$enabled_formfields[$formfield['key']] = $formfield;
				}
			}
			/* get all */
			if(!$enabled_only){
				$enabled_formfields[$formfield['key']] = $formfield;
			}
		}
		/* count the fileds we actually have enabled */
		$count_fields = count($enabled_formfields);

		/*sort by sort key*/
		if($count_fields>0){
			uasort($enabled_formfields, array($this, 'sort_by_sortkey'));
		}
		/*only return keys*/
		if($keys_only && $count_fields > 0){
			$enabled_formfields = array_keys($enabled_formfields);
			/** make keys and vals the same to be able to use isset */
			$enabled_formfields = array_combine($enabled_formfields,$enabled_formfields);
		}


	return $enabled_formfields;
	}

	/***************************************

		[alias to get all formfields without tips - as that is a special case]
		[for easy of use in admin of other plugins]
		typically used to show all *other* formfields one can splice into by filter
		in order form output
	***************************************/
	function customer_formfields($exclude_key = array(), $enabled_only = true){
		$ff = $this->enabled_formfields(false, $enabled_only, true);
		/** exclude keys from array **/
		foreach($exclude_key as $xKey){
			unset($ff[$xKey]);
		}
	return $ff;
	}
	/***************************************

		[get enabled formfiels main order form - filterable]

	***************************************/
	function enabled_formfields($keys_only = false, $enabled_only = true, $omit_tips = false, $caller = false){
		global $wppizza_options;

		/* omit tips if necessary*/
		if($omit_tips){
			unset($wppizza_options['order_form']['ctips']);
		}
		//if($wppizza_options==0){return;}

		/*
			just to loose some potential php notices
		*/
		$wppizza_options['order_form'] = empty($wppizza_options['order_form']) ? array() : $wppizza_options['order_form'];


		/*********
			for legacy reasons, pass on 'orderpage' parameters to filters if on orderpage
		*********/
		$caller = wppizza_is_checkout() ? 'orderpage' : '' ;


		/* allow filtering */
		$wppizza_options['order_form'] = apply_filters('wppizza_filter_formfields', $wppizza_options['order_form'], $caller);
		/* alias filter - going forward the above will - at some point - be deprecated to avoid confusion with other filters*/
		$wppizza_options['order_form'] = apply_filters('wppizza_register_formfields', $wppizza_options['order_form'], $caller);

		/*get all enabled form fields **/
		$enabled_formfields = array();
		foreach($wppizza_options['order_form'] as $key=>$formfield){
			/* enabled only */
			if($enabled_only){
				if($formfield['enabled']){
					$enabled_formfields[$formfield['key']] = $formfield;
					/* for consistency with other template values for example, also set/add [label] key */
					/* one day perhaps change 'lbl' to 'label' in wppizza->formfields and dependents*/
					$enabled_formfields[$formfield['key']]['label'] = $formfield['lbl'];
				}
			}
			/* get all */
			if(!$enabled_only){
				$enabled_formfields[$formfield['key']] = $formfield;
				/* for consistency with other template values for example, also set/add [label] key */
				/* one day perhaps change 'lbl' to 'label' in wppizza->formfields and dependents*/
				$enabled_formfields[$formfield['key']]['label'] = $formfield['lbl'];
			}
		}

		/* count the fileds we actually have enabled */
		$count_fields = count($enabled_formfields);

		/*sort by sort key*/
		if($count_fields>0){
			uasort($enabled_formfields, array($this, 'sort_by_sortkey'));
		}

		/*only return keys*/
		if($keys_only){
			if($count_fields > 0){
				$enabled_formfields = array_keys($enabled_formfields);
				/** make keys and vals the same to be able to use isset */
				$enabled_formfields = array_combine($enabled_formfields,$enabled_formfields);
			}

			/** adding some other specifically allowed keys added to orderpage without being in wppizza->form fields **/
			$enabled_formfields[''.WPPIZZA_SLUG.'_profile_update'] 	= ''.WPPIZZA_SLUG.'_profile_update';
			$enabled_formfields[''.WPPIZZA_SLUG.'_account'] 			= ''.WPPIZZA_SLUG.'_account';
			$enabled_formfields[''.WPPIZZA_SLUG.'_gateway_selected'] 	= ''.WPPIZZA_SLUG.'_gateway_selected';
		}

	return $enabled_formfields;
	}
	/***************************************

		[get user metadata - if logged in]

	***************************************/
	function user_meta(){

		global $wppizza_options;

		/* allow filtering */
		$wppizza_options['order_form'] = apply_filters('wppizza_filter_formfields', $wppizza_options['order_form'], 'user_meta');
		/* alias filter - going forward the above will - at some point - be deprecated to avoid confusion with other filters*/
		$wppizza_options['order_form'] = apply_filters('wppizza_register_formfields', $wppizza_options['order_form'], 'user_meta');


		/*get all enabled form fields **/
		$enabled_formfields = array();
		foreach($wppizza_options['order_form'] as $key=>$formfield){
			if($formfield['enabled']){
				$enabled_formfields[$formfield['key']] = $formfield;
			}
		}

		/* count the fileds we actually have enabled */
		$count_fields = count($enabled_formfields);

		/*sort by sort key*/
		if($count_fields>0){
			uasort($enabled_formfields, array($this, 'sort_by_sortkey'));
		}


	return $user_meta;
	}

	/***************************************

		[sort formfields by sort flag]
		using uasort instead of asort to reliably
		sort by sort flag , even if one formfield has
		more (or fewer) parameters than another
	***************************************/
	function sort_by_sortkey($a, $b){
		return $a['sort'] > $b['sort'];
	}

	/***************************************

		[get columns for itemised order]

		first, penultimate(third) and last column can only have one parameter
		second column can have multiple parameters

	***************************************/
	function itemised_order_columns($order_parameters, $template_parameters, $template_type, $return_markup = true ){

			/** only dealing with order vars **/
			$section_key = 'order';
			$order_id = $order_parameters['sections']['ordervars']['order_id']['value'];

			/** allow filtering **/
			//$order_parameters['sections'][$section_key]['items'] = apply_filters('wppizza_filter_itemised_items', $order_parameters['sections'][$section_key]['items']);


			$section_styles = ($template_type=='emails') ? $template_parameters['sections'][$section_key]['style'] :  array();;


			/* set tr class - non email only*/
			//$tr_class['class'] = ($template_type=='emails') ? '' : () ' class="item" ' ;



			/* all available parameters */
			$order_parameters_available = $template_parameters['sort'][$section_key];
			$order_parameters_available_count = count($order_parameters_available);
			/* only selected/enabled parameters */
			$order_parameters_selected = $template_parameters['sections'][$section_key]['parameters'];


			/* available column labels */
			$column_th[0] 	= array('key'=>'quantity', 'label'=>trim($order_parameters['localization']['itemised_label_quantity']));
			$column_th[1] 	= array('key'=>'article', 'label'=>trim($order_parameters['localization']['itemised_label_article']));
			$column_th[2] 	= array('key'=>'taxrate', 'label'=>trim($order_parameters['localization']['itemised_label_taxrate']));
			$column_th[3] 	= array('key'=>'price', 'label'=>trim($order_parameters['localization']['itemised_label_price']));


			/* assigng labels/parameters per column, removing column if not displayed */
			$columns = array();
			$counter = 1;
			foreach($order_parameters_available as $key => $val){

				/* first */
				if($counter == 1 && isset($order_parameters_selected[$key])){
					$columns[0]['key'] = $column_th[0]['key'];
					//$columns[0]['tr_class'] = 'items';/* tr class should be set for all columns, as some might get unset in filter*/
					$columns[0]['label'] = $column_th[0]['label'];
					$columns[0]['fields'][] = $key;
				}
				/*second....upto last but one*/
				if($counter > 1 && $counter < ($order_parameters_available_count-1) && isset($order_parameters_selected[$key])){
					$columns[1]['key'] = $column_th[1]['key'];
					//$columns[1]['tr_class'] = 'items';/* tr class should be set for all columns, as some might get unset in filter*/
					$columns[1]['label'] = $column_th[1]['label'];
					$columns[1]['fields'][] = $key;
				}
				/*last but one*/
				if($counter == ($order_parameters_available_count-1) && isset($order_parameters_selected[$key])){
					$columns[2]['key'] = $column_th[2]['key'];
					//$columns[2]['tr_class'] = 'items';/* tr class should be set for all columns, as some might get unset in filter*/
					$columns[2]['label'] = $column_th[2]['label'];
					$columns[2]['fields'][] = $key;
				}

				/*last*/
				if($counter == $order_parameters_available_count && isset($order_parameters_selected[$key])){
					$columns[3]['key'] = $column_th[3]['key'];
					//$columns[3]['tr_class'] = 'items';/* tr class should be set for all columns, as some might get unset in filter*/
					$columns[3]['label'] = $column_th[3]['label'];
					$columns[3]['fields'][] = $key;
				}
			$counter++;
			}

			/*
				allow filter - for sku's, categories and or blog id's  for example
			*/
			$columns = apply_filters('wppizza_filter_itemised_order_columns', $columns, $order_parameters['localization'], $template_type);


			/* reindex - zero indexed*/
			$columns=array_values($columns);


			/* add flag for first/last column and tr classname (first column only)*/
			$i=1;
			$column_count = count($columns);
			foreach($columns as $cKey => $column){
				/* first column */
				if($i==1){
					$columns[$cKey]['first_column'] = true ;/* not really required to set, but might be useful somewhere */
					$columns[$cKey]['id_style'] = ($template_type=='emails') ? ' style="'.$section_styles['td-lft'].';'.$section_styles['th'].'" ' : ' id="'.$column['key'].'" ' ;
					$columns[$cKey]['column_style'] = ($template_type=='emails') ? ' style="'.$section_styles['td-lft'].'" ' : '' ;
				}
				/* center columns */
				if($i>1 && $i!=$column_count ){
					$columns[$cKey]['id_style'] = ($template_type=='emails') ? ' style="'.$section_styles['td-ctr'].';'.$section_styles['th'].'" ' : ' id="'.$column['key'].'" ' ;
					$columns[$cKey]['column_style'] = ($template_type=='emails') ? ' style="'.$section_styles['td-ctr'].'" ' : '' ;
				}
				/* last column (only if 2+ columns) */
				if($i==$column_count && $column_count>1){
					$columns[$cKey]['last_column'] = true;/* not really required to set, but might be useful somewhere */
					$columns[$cKey]['id_style'] = ($template_type=='emails') ? ' style="'.$section_styles['td-rgt'].';'.$section_styles['th'].'" ' : ' id="'.$column['key'].'" ' ;
					$columns[$cKey]['column_style'] = ($template_type=='emails') ? ' style="'.$section_styles['td-rgt'].'" ' : '' ;
				}
			$i++;
			}


			/************************************************

				return markup - by default

			************************************************/
			if($return_markup){
				/*
					column labels plaintext
				*/
				$column_label = array();
				foreach($columns as $cKey=>$param){
					$column_label[$cKey] = $param['label'];
				}
				/* filter for even spacing */
				$markup['column_label']['plaintext'] = apply_filters('wppizza_filter_plaintext_line', $column_label, '-');


				/*
					column labels html
				*/
				$markup['column_label']['html'] = '';
				$markup['column_label']['html'] .= '<thead>'.PHP_EOL.'<tr>';
				foreach($columns as $cKey=>$param){
					$markup['column_label']['html'] .= '<th '.$param['id_style'].'>'.PHP_EOL.''.$param['label'].''.PHP_EOL.'</th>';
				}
				$markup['column_label']['html'] .= '</tr>'.PHP_EOL.'</thead>';




				/*
					itemised order details in column  -> plaintext
				*/
				$rows = array();
				/** loop through each item **/
				if(isset($order_parameters['sections'][$section_key]['items'])){

				$item_count = 0;
				/* filter items array */
				//$order_parameters['sections'][$section_key]['items'] = apply_filters('wppizza_filter_email_items_markup', $order_parameters['sections'][$section_key]['items'], 'plaintext');
				// as of 3.1.8 = using dynamic template type parameter (emails/print) instead of plaintext/html here
				$order_parameters['sections'][$section_key]['items'] = apply_filters('wppizza_filter_email_items_markup', $order_parameters['sections'][$section_key]['items'], $template_type);

				foreach($order_parameters['sections'][$section_key]['items'] as $itemKey => $arr){
					$itemised_row = array();
					foreach($columns as $column_key=>$column_detail){
						/* get parameters for this column */
						foreach($column_detail['fields'] as $field_key){
							$itemised_row[] = $arr[$field_key];
						}
					}
					/* space row parameters */
					$rows[$itemKey] = apply_filters('wppizza_filter_plaintext_line', $itemised_row, ' ');


					/* filter items array */
					$item_count++;
					$rows[$itemKey] = apply_filters('wppizza_filter_templates_item_markup_plaintext', $rows[$itemKey], $itemKey , $arr, count($itemised_row), $order_parameters['sections'][$section_key]['items'], $item_count, $order_id, $order_parameters['localization'], $template_type);
				}}




				/* implode rows by PHP_EOL */
				$markup['itemised']['plaintext'] = implode(PHP_EOL,$rows);


				/*
					itemised order details in column -> html
				*/
				$markup['itemised']['html'] = '';
				$markup['itemised']['html'] .= '<tbody>'.PHP_EOL;

				/** loop through each item **/
				if(isset($order_parameters['sections'][$section_key]['items'])){

				$item_count = 0;
				/* filter items array */
				// removed in 3.1.8 - as it doubles up the same filter above
				//$order_parameters['sections'][$section_key]['items'] = apply_filters('wppizza_filter_email_items_markup', $order_parameters['sections'][$section_key]['items'], 'html');


				foreach($order_parameters['sections'][$section_key]['items'] as $itemKey => $arr){

					$menu_item=array();

					/* tr */
					//$markup['itemised']['html'] .= '<tr>'.PHP_EOL;
					/* column keys are zero indexed */
					$column_count = count($columns);
					foreach($columns as $column_key=>$column_detail){

						/*
							open wrap tr
						*/
						if($column_key == 0){

							//$tr_class = ($template_type!='emails' && !empty($column_detail['tr_class']) ) ? ' class="'.$column_detail['tr_class'].'" ' : '' ;
							$tr_class = ($template_type!='emails') ? ' class="items" ' : '' ;
							$menu_item['tr_'.$column_key.'_'] = '<tr '. $tr_class .'>'.PHP_EOL;
						}

						$column_parameters = array();
						$menu_item['td_'.$column_key.'_'] = '<td '.$column_detail['column_style'].'>'.PHP_EOL;

							/* get parameters for this column */
							foreach($column_detail['fields'] as $field_key){
								/* only if not email */
								$parameter_id = ($template_type=='emails') ? '' : ' id="'.$field_key.'-'.$itemKey.'" class="'.$field_key.'" ';
								$column_parameters[] = '<span '.$parameter_id.'>'.$arr[$field_key].'</span>';
							}
							/* implode parameters for this column for output */
							$menu_item['column_'.$column_key.''] = implode(' ',$column_parameters).PHP_EOL;

						$menu_item['_td_'.$column_key.'']= '</td>'.PHP_EOL;


						/*
							close wrap tr
						*/
						if($column_key == ($column_count-1)){
							$menu_item['_tr_'.$column_key.''] = '</tr>'.PHP_EOL;
						}

					//$column_count++;
					}


					/* filter items array */
					$item_count++;
					$menu_item = apply_filters('wppizza_filter_templates_item_markup_html',$menu_item, $itemKey , $arr, $order_parameters['sections'][$section_key]['items'], $column_count, $item_count, $order_id, $order_parameters['localization'], $template_type);
					$markup['itemised']['html'] .= implode('', $menu_item);

					/* /tr */
					//$markup['itemised']['html'] .= '</tr>'.PHP_EOL;
				}}

				$markup['itemised']['html'] .= '</tbody>'.PHP_EOL;


			/*
				return markup array
			*/
			return $markup;
			}




		return 	$columns;
	}
	/*
		get blog details depending if it's multisite or single install
	*/
	function wppizza_blog_details($id){
		global $blog_id;
		if(is_multisite()){
			$blog_details = (array) get_blog_details( $id );
		}else{
			$blog_details = array();
			$blog_details['blog_id'] = $blog_id ;
			$blog_details['site_id'] = $blog_id ;
			$blog_details['blogname'] = get_bloginfo('name');
			$blog_details['siteurl'] = get_bloginfo('url');
			$blog_details['lang_id'] = get_bloginfo('language');
		}
		return $blog_details;
	}
}
?>