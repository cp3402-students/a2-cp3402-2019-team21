<?php
if ( ! defined( 'ABSPATH' ) ) exit;/*Exit if accessed directly*/
/**********************************************************************************************************************************************************************
*
*
*	WPPizza - Plaintext Template for Orders
*
*	[constructing sections and part separately, to allow easier filtering and sorting of sections and parts]
*
*
**********************************************************************************************************************************************************************/

	$messageSections=array();

	/***********************************************************************
	*
	*	[site details (if enabled) - no labels ]
	*
	***********************************************************************/
	$partsKey='site';
	if(!empty($siteDetails)){
		$siteVars=array();

		/*
			label/header
		*/
		if(!empty($sectionLabels[$partsKey])){
			$siteVars['section_label'] = $sectionLabels[$partsKey];
		}

		/*
			loop through site details
		*/
		foreach($siteDetails as $valKey=>$vars){
			$siteVars[$valKey] = $vars['value'];
		}


		$messageSections[$partsKey] = $siteVars;
	}

	/***********************************************************************
	*
	*	[db/transaction field details - if enabled]
	*
	***********************************************************************/
	$partsKey='ordervars';
	if(!empty($orderDetails)){

		$transactionVars=array();

		/*
			label/header
		*/
		if(!empty($sectionLabels[$partsKey])){
			$transactionVars['section_label'] = $sectionLabels[$partsKey];

		}

		/*
			loop through order details
		*/
		foreach($orderDetails as $valKey=>$vars){
			/*ini new*/
			$lblval = array();
			/*
				2 columns if label AND value exist
			*/
			if(!empty($vars['label']) && !empty($vars['value'])){
				$lblval['label'] = $vars['label'];
				$lblval['value'] = $vars['value'];
			}

			/*
				omitted lables add colspan to td's
			*/
			if(empty($vars['label'])){
				$lblval['value'] = $vars['value'];
			}

//			/**use to add consistent linespacing/length**/
//			$lblval = apply_filters('wppizza_filter_template_plaintext_transaction_details', $lblval, 'transaction');
//*tmp diable			$transactionVars[$valKey]=implode('',$lblval);
			$transactionVars[$valKey]=$lblval;
		}

//$plaintext_array[$partsKey] = $transactionVars;

		//$messageSections[$partsKey]=implode(PHP_EOL, $transactionVars);
		$messageSections[$partsKey] = $transactionVars;
	}

	/***********************************************************************
	*
	*	[customer details - if enabled]
	*
	***********************************************************************/
	$partsKey='customer';
	if(!empty($customerDetails)){

		/*
			label/header
		*/
		if(!empty($sectionLabels[$partsKey])){
			$customerVars['section_label'] = $sectionLabels[$partsKey];
		}
		/*
			loop through customer details
		*/
		foreach($customerDetails as $valKey=>$vars){
			/*ini new*/
			$lblval = array();

			$lblval['label'] = $vars['label'];
			$lblval['value'] = $vars['value'];

//			/**use to add consistent linespacing/length**/
//			$lblval = apply_filters('wppizza_filter_template_plaintext_transaction_details', $lblval, 'transaction');
//*tmp diable						$customerVars[$valKey]=implode('',$lblval);
			$customerVars[$valKey]=$lblval;
		}

//$plaintext_array[$partsKey] = $customerVars;


		//$messageSections[$partsKey]=implode(PHP_EOL, $customerVars);
		$messageSections[$partsKey] = $customerVars;
	}

	/***********************************************************************
	*
	*	[items/order details - if enabled. with header]
	*
	***********************************************************************/
	$partsKey='order';
	if(!empty($cartItems)){

		/**create markup for all items looping through each and using/adding eneabled vars**/
		$cartItemMarkup=array();

		/*
			label/header
		*/
		if(!empty($sectionLabels[$partsKey])){
			/*ini new*/
			$lblval = array();

			/**array of header columns to allow filtering*/
			$headerMarkupTh['left'] = $sectionLabels[$partsKey]['left'];
			$headerMarkupTh['center'] = $sectionLabels[$partsKey]['center'];
			$headerMarkupTh['right'] = $sectionLabels[$partsKey]['right'];

//			/**filter header info if required*/
//			$headerMarkup = apply_filters('wppizza_filter_template_item_header_plaintext_markup', $headerMarkup, $txt, $type.'_template', $template_id, true);


			/*implode individual header labels into ine string */
			$cartItemMarkup['section_label']= $headerMarkupTh;
		}
		/*
			loop through individual cart items
		*/
		foreach($cartItems as $itemKey=>$itemVars){




		}


//$plaintext_array[$partsKey] = $cartItemMarkup;

		/*implode all items by EOL*/
		//$messageSections[$partsKey]=implode(PHP_EOL, $cartItemMarkup);
		$messageSections[$partsKey] = $cartItemMarkup;
	}

	/***********************************************************************
	*
	*	[summary details - if enabled]
	*
	***********************************************************************/
	$partsKey='summary';

	if(!empty($orderSummary)){
		$summaryVars=array();

		/*
			label/thead ?
		*/
		if(!empty($sectionLabels[$partsKey])){
			$summaryVars['section_label'] = $sectionLabels[$partsKey];
		}
		/*
			loop through summary details
		*/
		foreach($orderSummary as $valKey=>$vars){
			/*ini new*/
			$lblval = array();
			/*
				2 columns if label AND value exist
			*/
			if(!empty($vars['label']) && !empty($vars['value'])){
				$lblval['label'] = $vars['label'];
				$lblval['value'] = $vars['value'];
			}

			/*
				omitted lables add colspan to td's
			*/
			if(empty($vars['label'])){
				$lblval['value'] = $vars['value'];
			}

//			/**use to add consistent linespacing/length**/
//			$lblval = apply_filters('wppizza_filter_template_plaintext_summary_detail', $lblval, 'summary');
//*tmp diable									$summaryVars[$valKey]=implode('',$lblval);
			$summaryVars[$valKey]=$lblval;
		}

//$plaintext_array[$partsKey] = $summaryVars;


		//$messageSections[$partsKey]=implode(PHP_EOL, $summaryVars);
		$messageSections[$partsKey] = $summaryVars;
	}

/**************************************************************************************************************************
*
*
*
*	[lets put the sections together in the chosen/right order]
*
*
*
**************************************************************************************************************************/
	$plaintext_sections=array();
	foreach($sectionsEnabled as $partsKey => $vars){
		$plaintext_sections[$partsKey]=$messageSections[$partsKey];
	}

	/***********************************************************************
	*
	*	[add footer text after everything else]
	*
	***********************************************************************/
	$partsKey='footer';
	if(!empty($miscVariables['footer_note'])){
		$footerVars=array();
		/*footer mssage*/
		$footerVars['order_email_footer'] = $miscVariables['footer_note'];
//		$footerVars['order_email_footer'] = apply_filters('wppizza_filter_template_plaintext_padstring', $footerVars['order_email_footer'], $partsKey);

		/*add another linebreak before footer*/
		//$plaintext[$partsKey]=PHP_EOL.implode(PHP_EOL, $footerVars);

		$plaintext_sections[$partsKey] = $footerVars;
//$plaintext_array[$partsKey] = $footerVars;
	}
?>