<?php
/**
* WPPIZZA_MODULE_ORDER_SETTINGS_ORDER_UPDATE Class
*
* @package     WPPIZZA
* @subpackage  WPPIZZA_MODULE_ORDER_SETTINGS_ORDER_UPDATE
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
class WPPIZZA_MODULE_ORDER_SETTINGS_ORDER_UPDATE{

	private $settings_page = 'order_settings';/* which admin subpage (identified there by this->class_key) are we adding this to */

	private $section_key = 'order_update';/* must be unique */


	function __construct() {
		/**********************************************************
			[add settings to admin]
		***********************************************************/
		if(is_admin()){
			/* add admin options settings page*/
			add_filter('wppizza_filter_settings_sections_'.$this->settings_page.'', array($this, 'admin_options_settings'), 60, 5);
			/* add admin options settings page fields */
			add_action('wppizza_admin_settings_section_fields_'.$this->settings_page.'', array($this, 'admin_options_fields_settings'), 10, 5);
			/**add default options **/
			add_filter('wppizza_filter_setup_default_options', array( $this, 'options_default'));
			/**validate options**/
			add_filter('wppizza_filter_options_validate', array( $this, 'options_validate'), 10, 2 );
		}
		/****************************************************
		*	allow for cart increase via input boxes 
		****************************************************/
		add_filter('wppizza_filter_order_item_header_markup', array( $this, 'cart_increase_header'), 10, 8 );
		add_filter('wppizza_filter_order_item_columns', array( $this, 'cart_increase'), 10, 8 );
		/****************************************************
		*	output spinner input order page: add to pages  if/as enabled
		*****************************************************/
		add_filter('wppizza_filter_order_item_header_markup', array( $this, 'wppizza_order_form_item_quantity_update_header'),100, 3);/** replace header th with update th **/		
		add_filter('wppizza_filter_order_item_columns', array( $this, 'wppizza_order_form_item_quantity_input'),100, 8);/** replace quantity column with input**/		
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
	/********************************************************************************** 
	*	header columns pages (order, confirmation , thank you, user order history ) 
	**********************************************************************************/
	function cart_increase_header($markup_header, $txt, $type){
		global $wppizza_options;
		// 'gettotals' used in minicart full order info
		if(($type == 'cart' || $type == 'gettotals') && !empty($wppizza_options['order_settings']['cart_increase'])){
			
			if($wppizza_options['order_settings']['menu_item_remove_button'] == 'left'){
				/* just to eliminate some possible undefined notices */
				if(!isset($markup_header['item_th_quantity'])){
					$markup_header['item_th_quantity']='';
				}
				$markup_header['item_th_quantity'] .= '<th class="'.WPPIZZA_PREFIX.'-item-delete-th">##</th>';	
			}
						
			/* add empty header dummy cell */
			if($wppizza_options['order_settings']['menu_item_remove_button'] == 'right'){
				$markup_header['item_th_delete']= '<th class="'.WPPIZZA_PREFIX.'-item-delete-th"></th>';
			}
		}
				

	return $markup_header;
	}

	function cart_increase($menu_item_columns, $key , $item, $items, $item_count, $order, $txt, $type){
		global $wppizza_options;
		if($type == 'cart' || $type == 'gettotals'){ // 'gettotals' used in minicart full order info
			/** add quantity as imputs **/
			if(!empty($wppizza_options['order_settings']['cart_increase'])){
				$menu_item_columns['item_td_quantity'] = '';
				
				/** add delete button left **/
				if($wppizza_options['order_settings']['menu_item_remove_button'] == 'left'){
					$menu_item_columns['item_td_quantity'] .= '<td class="'.WPPIZZA_PREFIX.'-item-delete">';
						$menu_item_columns['item_td_quantity'] .= '<input type="button" id="'.WPPIZZA_PREFIX.'-cart-'.$key.'" class="'.WPPIZZA_PREFIX.'-delete-from-cart" title="'.$txt['remove_from_cart'].'" value="x" />';
					$menu_item_columns['item_td_quantity'] .= '</td>';					
				}				
				

				$menu_item_columns['item_td_quantity'] .= '<td class="'.WPPIZZA_PREFIX.'-item-quantity">';
					if($type == 'cart'){//spinner in cart only. there's simply not enough space in minicart details
						$menu_item_columns['item_td_quantity'] .= '<input type="text" size="30" id="'.WPPIZZA_PREFIX.'-cart-modify-'.$key.'" class="'.WPPIZZA_PREFIX.'-cart-mod" name="'.WPPIZZA_PREFIX.'-cart-mod" value="'.$item['quantity'].'" />';
					}else{
						$menu_item_columns['item_td_quantity'] .= $item['quantity'];
					}
				$menu_item_columns['item_td_quantity'] .= '</td>';	
				
				
				/** add delete button right **/
				if($wppizza_options['order_settings']['menu_item_remove_button'] == 'right'){
					$menu_item_columns['item_td_delete'] = '';
					$menu_item_columns['item_td_delete'] .= '<td class="'.WPPIZZA_PREFIX.'-item-delete">';
						$menu_item_columns['item_td_delete'] .= '<input type="button" id="'.WPPIZZA_PREFIX.'-cart-'.$key.'" class="'.WPPIZZA_PREFIX.'-delete-from-cart" title="'.$txt['remove_from_cart'].'" value="x" />';
					$menu_item_columns['item_td_delete'] .= '</td>';					
				}
				
			}else{
				/** append [x] delete before **/
				$menu_item_columns['item_td_quantity'] = '';
				$menu_item_columns['item_td_quantity'] .= '<td class="'.WPPIZZA_PREFIX.'-item-quantity">';
					$menu_item_columns['item_td_quantity'] .= '<span id="'.WPPIZZA_PREFIX.'-cart-'.$key.'" class="'.WPPIZZA_PREFIX.'-remove-from-cart" title="'.$txt['remove_from_cart'].'">x</span>';
					$menu_item_columns['item_td_quantity'] .= ''.$item['quantity'].'';					
				$menu_item_columns['item_td_quantity'] .= '</td>';	
			}		
		}
	return $menu_item_columns;
	}

	/********************************************************************************** 
	*	header columns pages (order, confirmation , thank you, user order history ) 
	**********************************************************************************/
	function wppizza_order_form_item_quantity_update_header($markup_header, $txt, $type){
		global $wppizza_options;
		/* skip if not enabled and only on orderpage*/
		if(empty($wppizza_options['order_settings']['order_page_quantity_change']) || $type!='orderpage'){
			return $markup_header;
		}
		
		/* replace quantity header and adding empty delete th too */
		$markup_header['thead_th_quantity'] = '<th class="'.WPPIZZA_PREFIX.'-item-update-th">'.$txt['itemised_label_quantity'].'</th>';/* table cell */	
		if($wppizza_options['order_settings']['menu_item_remove_button'] == 'right'){
			$markup_header['thead_th_delete'] = '<th class="'.WPPIZZA_PREFIX.'-item-delete-th"></th>';/* table cell */		
		}
				

	return $markup_header;
	}
	/**********************************************************************************
	*	item columns pages (order, confirmation, thank you, user order history ) 
	**********************************************************************************/
	function wppizza_order_form_item_quantity_input($item_column, $key , $item, $cart, $item_count, $order_id, $txt, $type){
		global $wppizza_options;
		/* skip if not enabled and ONLY on orderpage*/
		if(empty($wppizza_options['order_settings']['order_page_quantity_change']) || $type!='orderpage'){
			return $item_column;
		}		
				
		/* replace quantity text with input */
		$item_column['item_td_quantity'] ='';
		$item_column['item_td_quantity'] .='<td class="'.WPPIZZA_PREFIX.'-item-update">';
			// use for delete button on left
			if($wppizza_options['order_settings']['menu_item_remove_button'] == 'left'){
				$item_column['item_td_quantity'] .= '<input type="button" id="'.WPPIZZA_PREFIX.'-cart-'.$key.'" class="'.WPPIZZA_PREFIX.'-delete-from-cart" title="'.$txt['remove_from_cart'].'" value="x" />';
			}
			$item_column['item_td_quantity'] .= '<input type="text" size="1" id="'.WPPIZZA_PREFIX.'-qkey-'.($key).'" class="'.WPPIZZA_PREFIX.'-item-qupdate" value="'.$item['quantity'].'" /></td>';
		$item_column['item_td_quantity'] .='</td>';
		
		
		/* add delete button*/
		if($wppizza_options['order_settings']['menu_item_remove_button'] == 'right'){
			$item_column['item_td_delete']='';
			$item_column['item_td_delete'] .='<td class="'.WPPIZZA_PREFIX.'-item-delete">';
				$item_column['item_td_delete'] .= '<input type="button" id="'.WPPIZZA_PREFIX.'-cart-'.$key.'" class="'.WPPIZZA_PREFIX.'-delete-from-cart" title="'.$txt['remove_from_cart'].'" value="x" />';
			$item_column['item_td_delete'] .='</td>';		
		}
		

	return $item_column;
	}
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

		/*section*/
		if($sections){
			$settings['sections'][$this->section_key] =  __('Item and Cart Updates and Amendments', 'wppizza-admin');
		}

		/*help*/
		if($help){
			$settings['help'][$this->section_key][] = array(
				'label'=>__('Item and Cart Updates', 'wppizza-admin'),
				'description'=>array(
					__('Set required options using the settings available', 'wppizza-admin')
				)
			);
		}

		/*fields*/
		if($fields){
			$field = 'cart_increase';
			$settings['fields'][$this->section_key][$field] = array( __('*Cart* items update', 'wppizza-admin') , array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('Enable increase/decrease of items in cart via input field/textbox', 'wppizza-admin'),
				'description'=>array()
			));

			$field = 'order_page_quantity_change';
			$settings['fields'][$this->section_key][$field] = array( __('*Order Form* items update', 'wppizza-admin') , array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('Enable increase/decrease of items in order form via input field/textbox', 'wppizza-admin'),
				'description'=>array()
			));
			
			$field = 'order_page_quantity_change_style';
			$settings['fields'][$this->section_key][$field] = array( __('Items update style', 'wppizza-admin') , array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('Which style would you like to use for the quantity change input fields', 'wppizza-admin'),
				'description'=>array()
			));			
						
			$field = 'menu_item_remove_button';
			$settings['fields'][$this->section_key][$field] = array( __('Enable "remove" button too', 'wppizza-admin') , array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('Additionally enables a distinct button to remove/delete an item from cart', 'wppizza-admin'),
				'description'=>array()
			));			
			
			
			$field = 'empty_cart_button';
			$settings['fields'][$this->section_key][$field] = array( __('Enable "empty cart" button', 'wppizza-admin') , array(
				'value_key'=>$field,
				'option_key'=>$this->settings_page,
				'label'=>__('Additionally add a distinct "empty cart" button alongside the "checkout" button', 'wppizza-admin'),
				'description'=>array()
			));			
		}

		return $settings;
	}
	/*------------------------------------------------------------------------------
	#	[output option fields - setting page]
	#	@since 3.0
	#	@return array()
	------------------------------------------------------------------------------*/
	function admin_options_fields_settings($wppizza_options, $options_key, $field, $label, $description){

		if($field=='cart_increase'){
			print'<label>';
			echo "<input id='".$field."' name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' type='checkbox'  ". checked($wppizza_options[$options_key][$field],true,false)." value='1' />";
				print'' . $label . '';
			print'</label>';
			print'' . $description . '';
		}
		if($field=='order_page_quantity_change'){

			print'<label>';
				echo "<input id='".$field."' name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' type='checkbox'  ". checked($wppizza_options[$options_key][$field],true,false)." value='1' />";
				print'' . $label . '';
			print'</label>';
			print'' . $description . '';


//			echo"<br />";
//			$uiStyle=array('ui-lightness','ui-darkness','smoothness','start','redmond','sunny','overcast','le-frog','flick','pepper-grinder','eggplant','dark-hive','cupertino','south-street','blitzer','humanity','hot-sneaks','excite-bike','vader','dot-luv','mint-choc','black-tie','trontastic','swanky-purse');
//			sort($uiStyle);
//			print'<label>';
//				echo "<select name='".WPPIZZA_SLUG."[".$options_key."][order_page_quantity_change_style]'>";
//					foreach($uiStyle as $k=>$style){
//					echo "<option value='".$style."' ".selected($wppizza_options[$options_key]['order_page_quantity_change_style'],$style,false).">".$style."</option>";
//					}
//					echo "<option value='' ".selected($wppizza_options[$options_key]['order_page_quantity_change_style'],'',false).">".__('a (themeroller) style is already loaded / I provide my own style', 'wppizza-admin')."</option>";
//				echo "</select>";
//				echo" ".__('set style to use for above enabled quantity change inputs', 'wppizza-admin')."";
//			print'</label>';
		}

		if($field=='order_page_quantity_change_style'){

			$uiStyle=array('ui-lightness','ui-darkness','smoothness','start','redmond','sunny','overcast','le-frog','flick','pepper-grinder','eggplant','dark-hive','cupertino','south-street','blitzer','humanity','hot-sneaks','excite-bike','vader','dot-luv','mint-choc','black-tie','trontastic','swanky-purse');
			sort($uiStyle);
			print'<label>';
				echo "<select name='".WPPIZZA_SLUG."[".$options_key."][order_page_quantity_change_style]'>";
					foreach($uiStyle as $k=>$style){
					echo "<option value='".$style."' ".selected($wppizza_options[$options_key]['order_page_quantity_change_style'],$style,false).">".$style."</option>";
					}
					echo "<option value='' ".selected($wppizza_options[$options_key]['order_page_quantity_change_style'],'',false).">".__('a (themeroller) style is already loaded / I provide my own style', 'wppizza-admin')."</option>";
				echo "</select>";
				print'' . $label . '';
			print'</label>';
		}
		if($field=='menu_item_remove_button'){
			print'<label>';
				echo "<select id='".$field."' name='".WPPIZZA_SLUG."[".$options_key."][".$field."]'>";
					echo "<option value='off' ".selected($wppizza_options[$options_key][$field],"off",false).">".__('off', 'wppizza-admin')."</option>";
					echo "<option value='left' ".selected($wppizza_options[$options_key][$field],"left",false).">".__('left', 'wppizza-admin')."</option>";
					echo "<option value='right' ".selected($wppizza_options[$options_key][$field],"right",false).">".__('right', 'wppizza-admin')."</option>";
				echo "</select>";
				print'' . $label . '';
			print'</label>';
			print'' . $description . '';
		}


		if($field=='empty_cart_button'){
			print'<label>';
				echo "<input id='".$field."' name='".WPPIZZA_SLUG."[".$options_key."][".$field."]' type='checkbox'  ". checked($wppizza_options[$options_key][$field],true,false)." value='1' />";
				print'' . $label . '';
			print'</label>';
			print'' . $description . '';
		}

	}

	/****************************************************************
	*
	*	[insert default option on install]
	*	$parameter $options array() | filter passing on filtered options
	*	@since 3.0
	*	@return array()
	*
	****************************************************************/
	function options_default($options){
		$options[$this->settings_page]['cart_increase'] = true;
		$options[$this->settings_page]['order_page_quantity_change'] = true;
		$options[$this->settings_page]['menu_item_remove_button'] = 'right';
		//$options[$this->settings_page]['order_page_quantity_change_left'] = false;
		$options[$this->settings_page]['order_page_quantity_change_style'] = 'smoothness';
		$options[$this->settings_page]['empty_cart_button'] = true;		
		
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
		if(isset($_POST[''.WPPIZZA_SLUG.'_'.$this->settings_page.''])){
			$options[$this->settings_page]['cart_increase'] = !empty($input[$this->settings_page]['cart_increase']) ? true : false;
			$options[$this->settings_page]['order_page_quantity_change'] = !empty($input[$this->settings_page]['order_page_quantity_change']) ? true : false;
			//$options[$this->settings_page]['order_page_quantity_change_left'] = !empty($input[$this->settings_page]['order_page_quantity_change_left']) ? true : false;
			$options[$this->settings_page]['menu_item_remove_button'] = in_array($input[$this->settings_page]['menu_item_remove_button'],array('off','left','right')) ? $input[$this->settings_page]['menu_item_remove_button'] : 'off';			
			$options[$this->settings_page]['order_page_quantity_change_style']=wppizza_validate_string($input[$this->settings_page]['order_page_quantity_change_style']);			
			$options[$this->settings_page]['empty_cart_button'] = !empty($input[$this->settings_page]['empty_cart_button']) ? true : false;			
		}

	return $options;
	}
}
/***************************************************************
*
*	[ini]
*
***************************************************************/
$WPPIZZA_MODULE_ORDER_SETTINGS_ORDER_UPDATE = new WPPIZZA_MODULE_ORDER_SETTINGS_ORDER_UPDATE();
?>