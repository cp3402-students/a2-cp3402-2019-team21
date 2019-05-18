<?php
/**
* WPPIZZA_CUSTOMERS Class
*
* @package     WPPIZZA
* @subpackage  Submenu Pages / Classes / WPPIZZA_CUSTOMERS
* @copyright   Copyright (c) 2015, Oliver Bach
* @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
* @since       3.0
*
*/
if ( ! defined( 'ABSPATH' ) ) exit;/*Exit if accessed directly*/


/************************************************************************************************************************
*
*
*	WPPIZZA_CUSTOMERS
*
*
************************************************************************************************************************/
class WPPIZZA_CUSTOMERS{

	/*
	* class ident
	* @var str
	* @since 3.0
	*/
	private $class_key='customers';/*to help consistency throughout class in various places*/
	/*
	* titles/labels
	* @var str
	* @since 3.0
	*/
	private $submenu_page_header;
	private $submenu_page_title;
	private $submenu_caps_title;
	private $submenu_link_label;
	private $submenu_priority = 120;
	function __construct() {


		/*titles/labels throughout class*/
		add_action('init', array( $this, 'init_admin_lables') );
		/** registering submenu page -> priority 120 **/
		add_action('admin_menu', array( $this, 'wppizza_register_submenu_page'), $this->submenu_priority );
		/*register capabilities for this page*/
		add_filter('wppizza_filter_define_caps', array( $this, 'wppizza_filter_define_caps'), $this->submenu_priority);
		/**load admin ajax file**/
		add_action('wp_ajax_wppizza_admin_'.$this->class_key.'_ajax', array($this, 'set_admin_ajax') );	

	}
	/******************
	*	@since 3.0.26
    *	[admin ajax include file]
    *******************/
	public function init_admin_lables(){
		/*titles/labels throughout class*/
		$this->submenu_page_header	=	apply_filters('wppizza_filter_admin_label_page_header_'.$this->class_key.'', __('Customers','wppizza-admin'));
		$this->submenu_page_title	=	apply_filters('wppizza_filter_admin_label_page_title_'.$this->class_key.'', __('Manage Customers','wppizza-admin'));
		$this->submenu_caps_title	=	apply_filters('wppizza_filter_admin_label_caps_title_'.$this->class_key.'', __('Customers','wppizza-admin'));
		$this->submenu_link_label	=	apply_filters('wppizza_filter_admin_label_link_label_'.$this->class_key.'', __('&middot; Customers','wppizza-admin'));		
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
		/*
			wppizza post type only
		*/
		$screen = get_current_screen();
		if($screen->post_type != WPPIZZA_POST_TYPE){return;}
		
		
		/**wrap settings sections into div->form */
		echo'<div id="'.WPPIZZA_SLUG.'-'.$this->class_key.'" class="'.WPPIZZA_SLUG.'-wrap  wrap '.WPPIZZA_SLUG.'-'.$this->class_key.'-wrap">';


		echo"<div class='".WPPIZZA_SLUG."-admin-pageheader'>";
			echo"<h2>";
				echo"<span id='".WPPIZZA_SLUG."-header'>";

					echo"".WPPIZZA_NAME." ".$this->submenu_page_header."";

					/*
						skip displaying if numeric as we are only searching for one integer/user id and it would look silly
					*/
					$search_term = wppizza_validate_string((!empty($_GET['uid'])) ? $_GET['uid'] : (!empty($_GET['s']) ? $_GET['s'] : '' )) ;
					if(!empty($search_term) && !is_numeric($search_term)){
						echo' - "'.$search_term.'"';
					}
				echo"</span>";
			echo"</h2>";

		echo"</div>";

			/* search only if not uid*/
			//if(empty($_GET['uid'])){
				echo"<table id='".WPPIZZA_SLUG."_".$this->class_key."_search'>";
					echo"<tbody>";
						echo"<tr>";
							echo"<td>";
								echo"<form action='".$_SERVER['PHP_SELF']."' method='GET'>";
									echo"<label>";
										echo "<input type='hidden' name='post_type' size='20' value='".WPPIZZA_SLUG."' />";
										echo "<input type='hidden' name='page' size='20' value='".$this->class_key."' />";

										/* only non numeric ones, the rest is silly really */
										$s = !empty($_GET['s']) ? wppizza_validate_string($_GET['s']) : '';
										$s=(!empty($s) && !is_numeric($s))? $s : '' ;
										echo "<input type='text' id='".WPPIZZA_SLUG."_".$this->class_key."_search_value' name='s' size='20' value='".$s."' />";

										echo "<input type='submit' id='".WPPIZZA_SLUG."_".$this->class_key."_do_search' class='button' value='".__('Search Customers', 'wppizza-admin')."' />";
									echo'</label>';
								echo'</form>';
							echo"</td>";
						echo"</tr>";
					echo"</tbody>";
				echo"</table>";
			//}

			/*customer_list*/
			echo"<div id='".WPPIZZA_SLUG."_".$this->class_key."_results'>";
				echo $this->wppizza_customer_list_markup();
			echo"</div>";


		echo'</div>';
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
		// let's not enable/list this option for now....probably not required anyway as one should also delete/reassign orders to someone else ...
		//$caps[$this->class_key.'-delete-customers']=array('name'=>__('Delete Customers', 'wppizza-admin') ,'cap'=>'wppizza_cap_delete_customers');
		return $caps;
	}

	/*********************************************************
	*
	*	[helper]
	* 	get customer list
	*	@since 3.0
	*
	*********************************************************/
	function wppizza_customer_list_markup($limit = 10){
		//global $wppizza_options;
		$get_blog_url = get_bloginfo('url');

		/****

			get customers info and no of customers for pagination

		****/
		$customers = WPPIZZA()->db->get_customers(false, $limit);

		/****

			get pagination

		*****/
		$pagination = WPPIZZA()->admin_helper->admin_pagination($customers['total_number_of_customers'], $limit, false);

		/****

			pagination counts markup

		****/
		$markup_pagination_info = array();
		$markup_pagination_info['span_left'] = '<span class="'.WPPIZZA_SLUG.'-pagination-left">'.$pagination['on_page'].' '.__('of','wppizza-admin').' '.$pagination['total_count'].'</span>';
		$markup_pagination_info['span_right'] = '<span class="'.WPPIZZA_SLUG.'-pagination-right">'.$pagination['pages'] .'</span>';
		/**
			allow filtering of pagination_info
		**/
		$markup_pagination_info = apply_filters('wppizza_filter_'.$this->class_key.'_pagination_info', $markup_pagination_info, $pagination);
		$markup_pagination_info =implode('',$markup_pagination_info);


		/****

			header/footer markup

		****/
		$markup_header_footer = array();

		$markup_header_footer['tr_'] = "<tr>";

			$columns_header_footer = array();
		
			$columns_header_footer['user_id'] = "<th scope='col' class='manage-column ".WPPIZZA_SLUG."-".$this->class_key."-column-user_id'>".__('ID','wppizza-admin')."</th>";
			
			$columns_header_footer['name'] = "<th scope='col' class='manage-column ".WPPIZZA_SLUG."-".$this->class_key."-column-name'>".__('Name','wppizza-admin')."</th>";
			
			$columns_header_footer['email'] = "<th scope='col' class='manage-column ".WPPIZZA_SLUG."-".$this->class_key."-column-email'>".__('Email','wppizza-admin')."</th>";
			
			$columns_header_footer['purchases'] = "<th scope='col' class='manage-column ".WPPIZZA_SLUG."-".$this->class_key."-column-purchases'>".__('Orders / Items','wppizza-admin')."</th>";
			
			$columns_header_footer['averages'] = "<th scope='col' class='manage-column ".WPPIZZA_SLUG."-".$this->class_key."-column-averages'>".__('Avg. / Order','wppizza-admin')."</th>";
			
			$columns_header_footer['spent'] = "<th scope='col' class='manage-column ".WPPIZZA_SLUG."-".$this->class_key."-column-total_spent'>".__('Total Spent','wppizza-admin')."</th>";
			
			$columns_header_footer['created'] = "<th scope='col' class='manage-column ".WPPIZZA_SLUG."-".$this->class_key."-column-date_created'>".__('Date Created','wppizza-admin')."</th>";
			
			$columns_header_footer['profile'] = "<th scope='col' class='manage-column ".WPPIZZA_SLUG."-".$this->class_key."-column-profile'>".__('Profile','wppizza-admin')."</th>";
			
			/* 
				filter columns 
			*/
			$columns_header_footer = apply_filters('wppizza_filter_'.$this->class_key.'_header_footer_columns', $columns_header_footer, $customers);
			$markup_header_footer['columns'] = implode('', $columns_header_footer);
			

		$markup_header_footer['_tr'] = "</tr>";
		/**
			allow filtering of header footer markup
		**/
		$markup_header_footer = apply_filters('wppizza_filter_'.$this->class_key.'_header_footer', $markup_header_footer, $customers);
		$markup_header_footer =implode('',$markup_header_footer);


		/**************************************************************************************
		*
		*
		*
		*	markup to return/output
		*
		*
		*
		**************************************************************************************/
		$markup=array();


			/**
				customer list table
			**/
			$markup['table_']="<table id='".WPPIZZA_SLUG."_list_".$this->class_key."' class='widefat fixed striped'>";
				/**
					orders table header
				**/
				$markup['thead']="<thead>";
					
					/* pagination top */
					$markup['thead'].="<th id='".WPPIZZA_SLUG."-".$this->class_key."-pagination-top' class='".WPPIZZA_SLUG."-".$this->class_key."-pagination' colspan='".count($columns_header_footer)."'>".$markup_pagination_info."</th>";
					
					/* column labels */
					$markup['thead'].= $markup_header_footer;
				
				
				$markup['thead'].="</thead>";

				/**
					orders table footer
				**/
				$markup['tfoot']="<tfoot>";
				
					/* column labels */
					$markup['tfoot'].= $markup_header_footer;
					
					/* pagination bottom */
					$markup['tfoot'].="<th id='".WPPIZZA_SLUG."-".$this->class_key."-pagination-bottom' class='".WPPIZZA_SLUG."-".$this->class_key."-pagination' colspan='".count($columns_header_footer)."'>".$markup_pagination_info."</th>";					
					
				$markup['tfoot'].="</tfoot>";

				/**
					the customers list
				**/
				$markup['tbody_'] = "<tbody id='the-list'>";

				/*
					no customers .....
				*/
				if(count($customers['results_set'])<=0){
					$markup['tbody_no_results'] = "<tr><td colspan='".count($columns_header_footer)."' id='".WPPIZZA_SLUG."-".$this->class_key."-no-results'>".__('no results found','wppizza-admin')."</td></tr>";
				}
				
				/*
					customer loop
				*/
				if(count($customers['results_set'])>0){
				foreach($customers['results_set'] as $cID => $customer){
					/**
						ini new empty array for this customer
					**/
					$customer_markup = array();

					/****************************************************************************
					*
					*	[row]
					*
					****************************************************************************/
					/*open tr*/
					$customer_markup['tr_'.$cID.'_'] = "<tr id='".WPPIZZA_SLUG."-".$this->class_key."-".$cID."' class='".WPPIZZA_SLUG."-".$this->class_key."'>";

						
						$customer_columns = array();
						
						/*
							user id
						*/
						$customer_columns['user_id'] ="<td  id='".WPPIZZA_SLUG."-".$this->class_key."-column-user_id-".$cID."' class='".WPPIZZA_SLUG."-".$this->class_key."-column-user_id'>";
							$customer_columns['user_id'] .= (empty($customer['user_registered'])) ? '# '.$customer['wp_user_id'].'' : '<a href="'.$get_blog_url.'/wp-admin/edit.php?post_type='.WPPIZZA_SLUG.'&page='.$this->class_key.'&s='.$customer['wp_user_id'].'"># '.$customer['wp_user_id'].'</a>';
						$customer_columns['user_id'] .= "</td>";
						/*
							user name
						*/
						$customer_columns['name'] ="<td  id='".WPPIZZA_SLUG."-".$this->class_key."-column-name-".$cID."' class='".WPPIZZA_SLUG."-".$this->class_key."-column-name'>";
							$customer_columns['name'] .= (empty($customer['user_name'])) ? '' : $customer['user_name'];
							$customer_columns['name'] .= (!empty($customer['user_name'])) ? '<br />['.$customer['user_user_nicename'].']' : ''.$customer['user_user_nicename'].'';
						$customer_columns['name'] .= "</td>";
						/*
							email
						*/
						$customer_columns['email'] ="<td  id='".WPPIZZA_SLUG."-".$this->class_key."-column-email-".$cID."' class='".WPPIZZA_SLUG."-".$this->class_key."-column-email'>";
							$customer_columns['email'] .= (empty($customer['user_email'])) ? __('unknown','wppizza-admin') : $customer['user_email'];
						$customer_columns['email'] .= "</td>";
						/*
							Orders
						*/
						$customer_columns['purchases'] ="<td  id='".WPPIZZA_SLUG."-".$this->class_key."-column-purchases-".$cID."' class='".WPPIZZA_SLUG."-".$this->class_key."-column-purchases'>";
							$customer_columns['purchases'] .= $customer['user_orders_order_count'].' / '.$customer['user_orders_total_items'];
						$customer_columns['purchases'] .= "</td>";

						/*
							Averages
						*/
						$customer_columns['averages'] ="<td  id='".WPPIZZA_SLUG."-".$this->class_key."-column-averages-".$cID."' class='".WPPIZZA_SLUG."-".$this->class_key."-column-averages'>";
							$customer_columns['averages'] .= wppizza_format_price($customer['user_orders_avg_spent']);
						$customer_columns['averages'] .= "</td>";
						/*
							Total Spent
						*/
						$customer_columns['spent'] ="<td  id='".WPPIZZA_SLUG."-".$this->class_key."-column-date_created-".$cID."' class='".WPPIZZA_SLUG."-".$this->class_key."-column-total_spent'>";
							$customer_columns['spent'] .= wppizza_format_price($customer['user_orders_total_value']);
						$customer_columns['spent'] .= "</td>";
						/*
							Date Created
						*/
						$customer_columns['registered'] ="<td  id='".WPPIZZA_SLUG."-".$this->class_key."-column-date_registered-".$cID."' class='".WPPIZZA_SLUG."-".$this->class_key."-column-date_registered'>";
							$ts=strtotime($customer['user_registered']);
							$customer_columns['registered'] .= (empty($customer['user_registered'])) ? __('unknown','wppizza-admin') : date('d M Y',$ts).'<br/>'.date('H:i',$ts);
						$customer_columns['registered'] .= "</td>";
						/*
							icons/orders
						*/
						$customer_columns['profile'] ="<td  id='".WPPIZZA_SLUG."-".$this->class_key."-column-icons-".$cID."' class='".WPPIZZA_SLUG."-".$this->class_key."-column-icons'>";
							if(!empty($customer['user_registered'])){
								$customer_columns['profile'] .="<a href='".$get_blog_url."/wp-admin/edit.php?post_type=".WPPIZZA_POST_TYPE."&page=orderhistory&uid=".$customer['wp_user_id']."' class='".WPPIZZA_SLUG."-dashicons dashicons-chart-line' title='".__('Show orders for user', 'wppizza-admin').": ".$customer['wp_user_id']."'></a>";
								$customer_columns['profile'] .="<a href='".$get_blog_url."/wp-admin/user-edit.php?user_id=".$customer['wp_user_id']."' class='".WPPIZZA_SLUG."-dashicons dashicons-edit' title='".__('Edit user profile', 'wppizza-admin').": ".$customer['wp_user_id']."'></a>";
							}
						$customer_columns['profile'] .= "</td>";


						/* 
							filter columns 
						*/
						$customer_columns = apply_filters('wppizza_filter_'.$this->class_key.'_columns', $customer_columns, $cID, $customer);
						$customer_markup['columns_'.$cID.''] = implode('', $customer_columns);


					/*close tr*/
					$customer_markup['_tr_'.$cID.''] = "</tr>";

					/****************************************************************************
					*
					*	[implode tr for output]
					*
					****************************************************************************/
					$markup[$cID] = implode('',$customer_markup);
				}}
				/**********************************
					end customer tr
				**********************************/

				$markup['_tbody'] = "</tbody>";

			$markup['_table'] = '</table>';


		/**
			allow filtering of entire markup
		**/
		$markup= apply_filters('wppizza_filter_'.$this->class_key.'_markup', $markup);
		$markup=implode('',$markup);

		return $markup;
	}
}

/***************************************************************
*
*	[ini]
*
***************************************************************/
$WPPIZZA_CUSTOMERS = new WPPIZZA_CUSTOMERS();
?>