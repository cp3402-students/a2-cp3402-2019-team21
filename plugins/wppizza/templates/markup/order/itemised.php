<?php
if ( ! defined( 'ABSPATH' ) ) exit;/*Exit if accessed directly*/
 /****************************************************************************************
 *
 *
 *	filters available: wppizza_filter_order_item_header_markup, wppizza_filter_order_items_markup, wppizza_filter_order_item_columns, wppizza_filter_order_item_markup, wppizza_filter_order_itemised_markup
 *
 *
 ****************************************************************************************/
?>
<?php
			$markup['table_']= '<table class="' . $class_table . '">';

				/**********************************
				*
				*	thead / labels
				*
				***********************************/
				$markup['thead_']= '<thead class="' . $class_thead . '">';

					/* table row */
					$markup['thead_tr_']= '<tr>';


						/* table cells filterable */
						$thead_columns['thead_th_quantity']	= '<th class="'.$class_quantity_th.'">'.$txt['itemised_label_quantity'].'</th>';

						$thead_columns['thead_th_article']	= '<th class="'.$class_article_th.'">'.$txt['itemised_label_article'].'</th>';

						$thead_columns['thead_th_price']		= '<th class="'.$class_price_th.'">'.$txt['itemised_label_price'].'</th>';

						$thead_columns['thead_th_taxrate']	= !empty($multiple_taxrates) ? '<th class="'.$class_taxrate_th.'">'.$txt['itemised_label_taxrate'].'</th>' : '';

						$thead_columns['thead_th_total']		= '<th class="'.$class_total_th.'">'.$txt['itemised_label_total'].'</th>';

						/* thead cell filter, implode for output */
						$thead_columns = apply_filters('wppizza_filter_order_item_header_markup', $thead_columns, $txt, $type, $items);
						$markup['thead_th']= implode('', $thead_columns);


					$markup['_thead_tr']= '</tr>';

				$markup['_thead']= '</thead>';


				/***********************************
				*
				*	tbody / items
				*
				***********************************/
				$markup['tbody_']= '<tbody class="' . $class_tbody . '"' . $style_tbody . '>';

					/*
						filter items (plural!) array if needs be before loop
					*/
					$items = apply_filters('wppizza_filter_order_items_markup', $items);

					/*
						loop through items adding count
					*/
					$item_count = 0;
					foreach($items as $key=>$item){
						/* ini item markup array */
						$menu_item=array();
						/* ini column markup array */
						$menu_item_columns=array();

						/* classes  -> tr */
						$class_tr = '';
						$class_tr .= $class_row;
						$class_tr .= ($item_count == 0) ? ' '.$class_row_first.'' : '';
						$class_tr .= ($item_count == $no_of_items) ? ' '.$class_row_last.'' : '';

						/* 
							thumbnail (orderpage only ) - wrap in span for ecen spacing (but with distinct classes for items with or without thumbnails). 
							Completely omit - even if enabled - if there are no thumbnails associated with any item in cart
							(no need for wasting empty space)
						*/
						$item['thumbnail'] = (isset($item['thumbnail']) && !empty($cart_has_thumbnails)) ? ( !empty($item['thumbnail']) ? '<span class="'.$class_article_thumbnail.'">'.$item['thumbnail'].'</span>': '<span class="'.$class_article_no_thumbnail.'"></span>' ) : '' ;
						
						/* price label - wrap in span if not empty */
						$item['price_label'] = !empty($item['price_label']) ? '<span class="'.$class_article_size.'"> '.$item['price_label'].'</span>': '';
						
						/*********
							create markup row | columns for this item
						**********/
						$menu_item['tr_']= '<tr id="'.$id_row_prefix . $key.'" class="'.$class_tr.'">';

							/* columns */
							$menu_item_columns['item_td_quantity']	= '<td class="'.$class_quantity_td.'">'.$item['quantity'].'</td>';

							$menu_item_columns['item_td_article']	= '<td class="'.$class_article_td.'">'.$item['thumbnail'].'<span class="'.$class_article_title.'">'.$item['title'].'</span>'.$item['price_label'].'</td>';

							$menu_item_columns['item_td_price']		= '<td class="'.$class_price_td.'">'.wppizza_format_price($item['price'], null).'</td>';

							$menu_item_columns['item_td_taxrate']	= !empty($multiple_taxrates) ? '<td class="'.$class_taxrate_td.'">'.wppizza_output_format_percent($item['tax_rate'], true).'</td>' : '';

							$menu_item_columns['item_td_total']		= '<td class="'.$class_total_td.'">'.wppizza_format_price($item['pricetotal'], null).'</td>';

							/* filter menu item columns */
							$menu_item_columns = apply_filters('wppizza_filter_order_item_columns', $menu_item_columns, $key , $item, $items, $item_count, $order_id, $txt, $type);



							$menu_item['columns']= implode('', $menu_item_columns);

						$menu_item['_tr']= '</tr>';


						/* filter menu item (singular!) row */
						$item_count++;
						$colspan = count($menu_item_columns);
						$menu_item = apply_filters('wppizza_filter_order_item_markup', $menu_item, $key , $item, $items, $colspan, $item_count, $order_id, $txt, $type);

						/* implode item row for markup output */
						$markup['item_'.$key]= implode('', $menu_item);
					}

				$markup['_tbody']= '</tbody>';

			$markup['_table']= '</table>';

?>