<?php
if ( ! defined( 'ABSPATH' ) ) exit;/*Exit if accessed directly*/
 /****************************************************************************************
 *
 *	NOTE: THIS IS NOT THE ORDER HISTORY IN THE BACKEND, BUT
 *
 *	Markup of Order History added to a frontend page with shortcode [wppizza_admin type="admin_orderhistory"]
 *
 *
 ****************************************************************************************/
?>
<?php

		/*****************************************
			login form - if not logged in and not bypassed entirely.
			will be empty if already logged in or display "not allowed access" if logged in users capabalities do not allow access to theis)
		*****************************************/
		$markup['login_form'] = $login_form;/* uses: markup/global/login.php */


		/*****************************************
			no orders <p> element / text -
			only if logged in and there are no orders, else empty
		*****************************************/
		$markup['no_orders'] = $no_orders;/* uses: markup/global/login.php */


		/*****************************************
		 pagination: empty if omitted in atts,
		 no orders exist or no pagination is required
		*****************************************/
		$markup['pagination_top'] = $pagination;


		/*****************************************
		 by default empty string, but can be added to via filters if required 
		*****************************************/
		$markup['before_orders'] = apply_filters('wppizza_filter_pages_shortcode_orderhistory_before_orders', '', $has_orders, $order_history );

		/******************************************
			orders table
			provided there are some
		******************************************/
		if($has_orders){
		$markup['table_'] = '<table class="'.$class_table.'">';

			/*
				header
			*/
			$thead['thead_'] = '<thead>';
				$thead['thead_tr_'] = '<tr>';
					$thead['thead_dates'] = '<th class="'.$class_th_dates.'">'.__('Date / Updated', 'wppizza-admin').'</th>';
					$thead['thead_customer'] = '<th class="'.$class_th_customer.'">'.__('Customer', 'wppizza-admin').'</th>';
					$thead['thead_order'] = '<th class="'.$class_th_order.'">'.__('Order', 'wppizza-admin').'</th>';
					$thead['thead_status'] = '<th class="'.$class_th_status_type.'">'.__('Status / Type', 'wppizza-admin').'</th>';
					$thead['thead_actions'] = '<th class="'.$class_th_actions.'">&nbsp;</th>';
				$thead['_thead_tr'] = '</tr>';
			$thead['_thead'] = '</thead>';
			/*
				(allow) filter and implode header for markup
			*/
			$thead = apply_filters('wppizza_filter_pages_shortcode_orderhistory_thead', $thead, $has_orders, $order_history );
			$markup['thead'] = implode('', $thead);



			/*
				footer
			*/
			$tfoot['tfoot_'] = '<tfoot>';
				$tfoot['tfoot_tr_'] = '<tr>';
					$thead['tfoot_dates'] = '<th class="'.$class_th_dates.'">'.__('Date / Updated', 'wppizza-admin').'</th>';
					$thead['tfoot_customer'] = '<th class="'.$class_th_customer.'">'.__('Customer', 'wppizza-admin').'</th>';
					$thead['thead_order'] = '<th class="'.$class_th_order.'">'.__('Order', 'wppizza-admin').'</th>';
					$thead['tfoot_status'] = '<th class="'.$class_th_status_type.'">'.__('Status / Type', 'wppizza-admin').'</th>';
					$thead['tfoot_actions'] = '<th class="'.$class_th_actions.'">&nbsp;</th>';
				$tfoot['_tfoot_tr'] = '</tr>';
			$tfoot['_tfoot'] = '</tfoot>';
			/*
				(allow) filter and implode footer for markup
			*/
			$tfoot = apply_filters('wppizza_filter_pages_shortcode_orderhistory_tfoot', $tfoot, $has_orders, $order_history );
			$markup['tfoot'] = implode('', $tfoot);


			/*
				tbody
			*/
			$markup['tbody_'] = '<tbody>';

			/*****************************************
				loop through each order in order history
			*****************************************/
			foreach($order_history as $key => $order){
				
				$row = array(); 
				/************************************
					wrap each order into tr
				************************************/
				$row['tr_'] = '<tr id="' . $order['uoKey'] . '" class="' . $order['class'] . '">';

					/*
						order date / order update
					*/
					$row['td_dates_'] = '<td class="'.$class_dates.'">';
						$row['td_blog_name'] = ''.$order['blog_name'].'';//only shown if multisite, parent blog and enabled to query all					
						$row['td_date'] = ''.$order['order_date'].'';
						$row['td_update'] = ''.$order['order_update'].'';
					$row['_td_dates'] = '</td>';

					/*
						name / address
					*/
					$row['td_customer_'] = '<td class="'.$class_customer.'">';
						$row['td_name'] = ''.$order['name'].'';
						$row['td_address'] = ''.$order['address'].'';
					$row['_td_customer'] = '</td>';					

					/*
						order (total / payment_type)
					*/					
					$row['td_order_'] = '<td class="'.$class_order.'">';
						$row['td_total'] = ''.$order['total'].'';
						$row['td_payment_type'] = ''.$order['payment_type'].'';
					$row['_td_order'] = '</td>';					
					
					

					/*
						order_status / order_type
					*/
					$row['td_status_type_'] = '<td class="'.$class_status_type.'">';
						$row['td_status'] = ''.$order['order_status'].'';
						$row['td_type'] = ''.$order['order_type'].'';
					$row['_td_status_type'] = '</td>';


					/*
						actions (view/print)
					*/
					$row['td_actions_'] = '<td class="'.$class_actions.'">';
						$row['td_view'] = ''.$order['order_view'].'';
					$row['_td_actions'] = '</td>';



				/************************************
					end tr
				*************************************/
				$row['_tr'] = '</tr>';


				/*
					(allow) filter and implode order for markup
				*/
				$row = apply_filters('wppizza_filter_pages_shortcode_orderhistory_order', $row, $order['order_formatted'], $key );				
				$markup['order_'.$key.''] = implode('', $row);

			}
			/************************************
				end orders loop
			*************************************/
			$markup['_tbody'] = '</tbody>';

		$markup['_table'] = '</table>';
		}/* has_orders end */

		/*****************************************
		 by default empty string, but can be added to via filters if required 
		*****************************************/
		$markup['after_orders'] = apply_filters('wppizza_filter_pages_shortcode_orderhistory_after_orders', '', $has_orders, $order_history );

		/*****************************************
		 pagination: empty if omitted in atts,
		 no orders exist or no pagination is required
		*****************************************/
		$markup['pagination_bottom'] = $pagination;
?>