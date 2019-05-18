<?php
if ( ! defined( 'ABSPATH' ) ) exit;/*Exit if accessed directly*/

if (!class_exists( 'WPPizza' ) ) {return ;}
class WPPIZZA_ADMIN_HELPERS{


	function __construct() {

	}

	/***********************************************************
		order history summary visibility status
	***********************************************************/
	function orderhistory_summary_visibility_by_status(){
		/**add visible/hidden class to summary/full details depending on status**/
		$summary_visibility_status=array('delivered','rejected','refunded','other');
		/*filterable*/
		$summary_visibility_status=apply_filters('wppizza_filter_orderhistory_summary_visibility_by_status', $summary_visibility_status);


		return $summary_visibility_status;
	}

	/***********************************************************
		order history summary visibility PAYMENT status
	***********************************************************/
	function orderhistory_summary_visibility_by_payment_status(){
		/**add visible/hidden class to summary/full details depending on status**/
		$summary_visibility_status=array('unconfirmed');
		/*filterable*/
		$summary_visibility_status=apply_filters('wppizza_filter_orderhistory_summary_visibility_by_payment_status', $summary_visibility_status);


		return $summary_visibility_status;
	}

	/***********************************************************
		order history order status dropdown
	***********************************************************/
	function orderhistory_order_status_select($class_key, $unique_id, $uoKey, $selected_status, $exclude = array()){
		/****************************
			flip $exclude array
			to allow for faster isset
		****************************/
		$exclude = array_flip($exclude);

		/****************************
			get available order statuses
		****************************/
		/**markup*/
		$markup='';
		$markup.="<select id='".WPPIZZA_SLUG."-".$class_key."-order-status-".$unique_id."-".$uoKey."' name='".WPPIZZA_SLUG."-".$class_key."-order-status-".$unique_id."-".$uoKey."' class='".WPPIZZA_SLUG."-".$class_key."-order-status'>";
		foreach(wppizza_order_status_default() as $key => $label){
			if(!isset($exclude[strtoupper($key)])){
			$markup.="<option value='".$key."' ".selected(strtoupper($selected_status),$key,false).">".$label."</option>".PHP_EOL;
			}
		}
		$markup.="</select>";

	return $markup;
	}

	/***********************************************************
		order history custom options dropdown
	***********************************************************/
	function orderhistory_custom_options_select($class_key = false, $unique_id = 'select', $uoKey = false, $selected_option = false, $search = false){
		global $wppizza_options;
		$txt = $wppizza_options['localization'];
		/**markup*/
		$markup='';
		/* check if anything is set */
		if($txt['order_history_custom_status_options']==''){return $markup;}
		/****************************
			set comma separated options
		****************************/
		$set_options = explode(',',$txt['order_history_custom_status_options']);
		$id = ($uoKey) ? "".WPPIZZA_SLUG."-".$class_key."-custom-option-".$unique_id."-".$uoKey."" : "".WPPIZZA_SLUG."-".$class_key."-custom-option-".$unique_id."" ;
		$name = ($uoKey) ? "".WPPIZZA_SLUG."-".$class_key."-custom-option-".$unique_id."-".$uoKey."" :  "".WPPIZZA_SLUG."-".$class_key."-custom-option-".$unique_id."";
		$class = ($uoKey) ? "".WPPIZZA_SLUG."-".$class_key."-custom-option" : "".WPPIZZA_SLUG."-".$class_key."-custom-option-".$unique_id."" ;

		$markup.="<select id='".$id."' name='".$name."' class='".$class."'>";
		if($search){
			$markup.="<option value=''>----".__('All', 'wppizza-admin')."----</option>".PHP_EOL;
		}else{
			$markup.="<option value=''>----".__('Not Set', 'wppizza-admin')."----</option>".PHP_EOL;
		}
		foreach($set_options as $key => $label){
			$lbl = trim($label);
			$markup.="<option value='".$lbl."' ".selected(strtolower($selected_option),strtolower($lbl),false).">".$lbl."</option>".PHP_EOL;
		}
		if($search){
			$markup.="<option value='[not-set]' ".selected($selected_option,'-',false).">----".__('Not Set', 'wppizza-admin')."----</option>".PHP_EOL;
		}
		$markup.="</select>";

	return $markup;
	}



	/***********************************************************
		admin pagination
	***********************************************************/
	function admin_pagination($results, $max_per_page, $getParam=false){

			/*if we are passing just a total count*/
			if(is_numeric($results) && $results!=''){
				/**get parameter to use*****/
				$pagedParam='paged';
				/**total number of results**/
				$totalListCount=(int)$results;
				/**number of pages**/
				$totalPages=ceil($totalListCount/$max_per_page);
				/*current page */
				$currentPage=1;
				if(isset($_GET[$pagedParam]) && (int)$_GET[$pagedParam]>1 && (int)$_GET[$pagedParam]<=$totalPages){
					$currentPage=(int)$_GET[$pagedParam];
				}
				/*current offset*/
				$offset=($currentPage-1)*$max_per_page;

				/**onpage. quick and dirty*/
				if($currentPage==1){
					$onpage=$currentPage.'-'.$max_per_page;
				}
				if($currentPage!=1 && $currentPage!=$totalPages){
					$onpage=(($currentPage-1)*$max_per_page+1).'-'.($currentPage*$max_per_page);
				}
				if($currentPage==$totalPages){
					$onpage=(($currentPage-1)*$max_per_page+1).'-'.$totalListCount;
				}
				/**get the pagination***/
				$pagination_pages=$this->admin_pagination_links($currentPage, $totalPages, $pagedParam, $getParam);

				$list=false;
			}
			/**passing a results set*/
			if(!is_numeric($results) || $results==''){
				$list=!empty($results) ? $results : array();
				if(isset($list) && is_array($list)){
					/**get parameter to use*****/
					$pagedParam='paged';
					/**total number of results**/
					$totalListCount=count($list);
					/**number of pages**/
					$totalPages=ceil($totalListCount/$max_per_page);
					/*current page */
					$currentPage=1;
					if(isset($_GET[$pagedParam]) && (int)$_GET[$pagedParam]>1 && (int)$_GET[$pagedParam]<=$totalPages){
						$currentPage=(int)$_GET[$pagedParam];
					}
					/*current offset*/
					$offset=($currentPage-1)*$max_per_page;

					/**onpage. quick and dirty*/
					if($currentPage==1){
						$onpage=$currentPage.'-'.$max_per_page;
					}
					if($currentPage!=1 && $currentPage!=$totalPages){
						$onpage=(($currentPage-1)*$max_per_page+1).'-'.($currentPage*$max_per_page);
					}
					if($currentPage==$totalPages){
						$onpage=(($currentPage-1)*$max_per_page+1).'-'.$totalListCount;
					}
					/**get the pagination***/
					$pagination_pages=$this->admin_pagination_links($currentPage, $totalPages, $pagedParam, $getParam);

					/***************************************
						sort and slice
					***************************************/
					krsort($list);/*sort by key in reverse, chances are the last one added is the most useful*/
					$list=array_slice($list, $offset, $max_per_page, true);
				}
			}


		$pagination['list']=$list;
		$pagination['total_count']=!empty($totalListCount) ? $totalListCount : 0;
		$pagination['on_page']=!empty($onpage) ? $onpage : '';
		$pagination['pages']=!empty($pagination_pages) ? $pagination_pages : '';

		return $pagination;
	}
	/***********************************************************
		admin pagination links
	***********************************************************/
	function admin_pagination_links($current, $total, $pagedParam, $getParam=false){

		/**
			add additional get parameters
			(unsetting paged param to avoid duplication) if set
			only really used in ajax request using
			window.location.search.substr(1), and subsequently
			parsing to array like so
			parse_str($getparameters, $parsed);
		**/
		$add_fragment = '';
		if(is_array($getParam)){
			unset($getParam[$pagedParam]);
			$getParam=wppizza_validate_array($getParam);/*sanitize array keys/values*/
			$add_fragment = '&'.http_build_query($getParam,'','&');
		}
		$args = array(
			'base'         => '%_%',
			'format'       => '?'.$pagedParam.'=%#%',
			'total'        => $total,
			'current' 	   => $current,
			'show_all'     => False,
			'end_size'     => 3,
			'mid_size'     => 1,
			'prev_next'    => True,
			'prev_text'    => __('&#171; Previous'),
			'next_text'    => __('Next &#187;'),
			'type'         => 'plain',
			'add_args'     => False,
			'add_fragment' => $add_fragment,
			'before_page_number' => '',
			'after_page_number' => ''
		);
		$pagination=paginate_links($args);

	return $pagination;
	}

	/***********************************************************
		return sorted label, key  and type of
		enabled order form formfields
	***********************************************************/
	function admin_orderform_enabled_formfields($location){
		global $wppizza_options;

		/*
			unset tips formfields
			for gateway mapping
		*/
		if($location == 'gateways'){
			unset($wppizza_options['order_form']['ctips']);
		}

		/**allow filtering - to, for example, remove/re-sort existing or add custom formfields **/
		$wppizza_options['order_form'] = apply_filters('wppizza_filter_admin_orderform_enabled_formfields', $wppizza_options['order_form'], $location);

		/**set sort/lbl/type array of all enabled formfields*/
		$formfields=array();
		foreach($wppizza_options['order_form'] as $ffKey => $ff){

			/*
				when mapping for gateways, omit tips (above) and all non text/textarea fields
			*/
			$gateway_mapping_enabled = true ;
			if($location == 'gateways'){
				if( $ff['type']!='select' && $ff['type']!='textarea' && $ff['type']!='text' && substr($ff['type'],0,10)=='text_size_'){
					$gateway_mapping_enabled = false;
				}
			}

			/*
				do not show not enabled formfields
			*/
			if(!empty($ff['enabled']) && !empty($gateway_mapping_enabled)){
				$formfields[$ff['key']]=array('sort'=>$ff['sort'], 'lbl'=>$ff['lbl'], 'type'=>$ff['type']);
			}
		}
		/*sort by sort flag*/
		asort($formfields);

	return $formfields;
	}
	/***********************************************************
		filter html styles in templates
		to at least *try* to get rid of totally invalid css
		@return array
	***********************************************************/
	function sanitize_css($array, $css=false, $decodeSingleQuotes=false){
		foreach($array as $k=>$v){
			foreach($v as $w=>$x){
				$array[$k][$w]=$this->sanitize_css_values($x, $css, $decodeSingleQuotes);
			}
		}
	return $array;
	}

	/***********************************************************
		display (unserialized and wrapped) errors in order history
	***********************************************************/
	function unserialize_errors_to_string($value){

		$str =  print_r(maybe_unserialize($value),true);
		/* eacape html and force wordwrap */
		$str = wordwrap(esc_html($str), 150, PHP_EOL, TRUE);
		return $str;
	}
	/***********************************************************
		helper for function above.

		this could be better no doubt, but will have to do for now

		$decodeSingleQuotes to pull it out again with single quotes being that again

		@returs string
	***********************************************************/
	function sanitize_css_values($str, $css=false, $decodeSingleQuotes=false){
		$charRemove=array('{','}','<','>','`');
		/*if we are validating full css style sheets, do not strip { and } */
		if($css){
			$charRemove=array('`');
		}

		/*remove linebreaks*/
		$str=str_replace(PHP_EOL,'',$str);
		/**first convert all " to ' */
		$str=str_replace('"','\'',$str);
		/*strip tags*/
		$str=strip_tags($str);
		/*trim*/
		$str=trim($str);
		/*now ltes replace totally invalid things*/
		$str=str_replace($charRemove,'',$str);
		/*convert remaining namely single quotes */
		//$str=htmlspecialchars($str,ENT_QUOTES);

		if($decodeSingleQuotes){
			$str=str_replace("&#039;","'",$str);
		}


		return $str;
	}

	/***********************************************************
		helper function if wppizza_on_orderstatus_change
		filter has been invoked
		(runs on change of order status in admin/backend or
		when using  [wppizza_admin type=admin_orderhistory] shortcode
		in frontend

		@param int
		@param int
		@param str

		@return str (javascript alert string)
		@since 3.6
	***********************************************************/
	function process_orderstatus_change($blog_id, $order_id, $order_status){

			/*********************************************
				get entire order for this purchase
				and return formatted values and blog options
			**********************************************/
			$args = array(
				'query'=>array(
					'order_id' => $order_id ,
					'blogs' => $blog_id ,
				),
				'format' =>array(
					'blog_options' => array('localization', 'blog_info', 'date_format', 'blog_options'),
					'sections' => true,
				)
			);
			/*************************************************
				run query, and get results
				even single order results are always arrays
				so simply use reset here
			*************************************************/
			$order = WPPIZZA() -> db -> get_orders($args, 'orderstatus_change');
			$order = reset($order['orders']);

			if(!empty($order)){

				/****************************************
				 	ini empty template_markup_plaintext
				*****************************************/
				$template_markup_plaintext = '';

				/****************************************
				 	ini empty template_markup_html
				*****************************************/
				$template_markup_html = '';

				/*****************************************
					add email template if id is set
				******************************************/
				$template_id = apply_filters('wppizza_on_orderstatus_change_add_template', false, $order_status);
				if(is_numeric($template_id) &&  $template_id >= 0 ){

					/*
						using email templates here as default , but could be filtered
					*/
					$template_type = apply_filters('wppizza_on_orderstatus_change_add_template_type', 'emails');

					/*
						get template options
						for this type
					*/
					$template_options = get_option(WPPIZZA_SLUG.'_templates_'.$template_type, 0);
					/*
						get template
						by id
					*/
					if(!empty($template_options[$template_id])){
						/*
							get template options for selected id
						*/
						$template_values = $template_options[$template_id];


						/* always get plaintext */
						$template_parameters = WPPIZZA() -> templates_email_print -> get_template_email_plaintext_sections_markup($order, $template_values, $template_type );
						$template_markup_plaintext = $template_parameters['markup'];

						/* add html if set */
						if($template_values['mail_type'] == 'phpmailer'){
							$template_markup_html = WPPIZZA() -> templates_email_print -> get_template_email_html_sections_markup($order, $template_values, $template_type, $template_id);
						}
					}

				}

				/****************************************
				 	get sections and blog options for filter
				 	separately to support old implementations of this
				*****************************************/
				$order_formatted = $order['sections'];
				$blog_options = $order['blog_options'];
			}else{
				$order_formatted = false;
				$blog_options = false;
				$template_markup_plaintext = false;
				$template_markup_html = false;
			}


			/*
				return string to alert in js or return empty string for no alert
			*/
			$filter_alert = apply_filters('wppizza_on_orderstatus_change', $order_formatted, $order_status, $blog_options, $template_markup_plaintext, $template_markup_html);
			$js_alert = (is_string($filter_alert) && $filter_alert!='') ? $filter_alert : '' ;

	return $js_alert;
	}

}
?>