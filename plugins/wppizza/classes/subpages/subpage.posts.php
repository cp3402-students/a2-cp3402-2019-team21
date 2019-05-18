<?php
/**
* WPPIZZA_POSTS Class
*
* @package     WPPIZZA
* @subpackage  WPPIZZA_POSTS
* @copyright   Copyright (c) 2015, Oliver Bach
* @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
* @since       3.0
*
*/
if ( ! defined( 'ABSPATH' ) ) exit;/*Exit if accessed directly*/


/************************************************************************************************************************
*
*
*	WPPIZZA_POSTS filters 
*
*
************************************************************************************************************************/
class WPPIZZA_POSTS{

	private $class_key = 'menu_items';/*to help consistency throughout class in various places*/
	private $submenu_caps_title ;
	private $submenu_priority = 0; /* only relevant here for caps */
	/******************************************************************************************************************
	*
	*	[CONSTRUCTOR]
	*
	*	
	*	@since 3.0
	*
	******************************************************************************************************************/
	function __construct() {

		$this->submenu_caps_title = __('Menu Items','wppizza-admin');
		
		/*register capabilities for this page*/
		add_filter('wppizza_filter_define_caps', array( $this, 'wppizza_filter_define_caps'), $this->submenu_priority);	
		
		/*metaboxes in single post edit**/
		/*add metaboxes*/
    	add_action('add_meta_boxes', array( $this, 'wppizza_add_metaboxes') );
		/*save metaboxes*/
		add_action('save_post', array( $this, 'wppizza_save_metaboxes'), 10, 2 );    

		/**sort menu item column in admin by name**/
		add_filter('request', array( $this, 'wppizza_items_sort') );

		
		/*add order/prices columns**/
		add_action('manage_edit-wppizza_columns', array( $this, 'wppizza_new_wppizza_column'));
		add_action('manage_wppizza_posts_custom_column',array( $this, 'wppizza_show_order_column'), 10, 2 );
		//add_filter('manage_edit-wppizza_sortable_columns',array( $this, 'wppizza_order_column_register_sortable'));//currently not in use

		/*add prices to quickedit*/
		add_action('quick_edit_custom_box',  array( $this, 'wppizza_add_quick_edit'), 10, 2);
		add_action('admin_footer', array( $this, 'wppizza_quick_edit_js'));
		add_filter('post_row_actions', array( $this, 'wppizza_expand_quick_edit_link'), 10, 2);
		add_action('save_post',  array( $this, 'wppizza_save_quick_edit_data'), 10, 2);		

		/*execute some helper functions once to use their return multiple times */
		add_action('current_screen', array( $this, 'wppizza_add_helpers') );
	
		/**set admin ajax**/
		add_action('wp_ajax_wppizza_admin_'.$this->class_key.'_ajax', array($this, 'set_admin_ajax') );	
			
		/** admin ajax **/
		add_action('wppizza_ajax_admin_'.$this->class_key.'', array( $this, 'admin_ajax'));		
	
	
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
	*	[add helpers]
	*	@since 3.0
	*
	* 	run on this page only or if saving this page 
		($_POST[WPPIZZA_SLUG.'_'.$this->class_key])	
	*********************************************************/	
	function wppizza_add_helpers($current_screen){		
		if( !empty($_POST[WPPIZZA_SLUG.'_'.$this->class_key]) || ($current_screen->id == WPPIZZA_POST_TYPE && $current_screen->post_type == WPPIZZA_POST_TYPE)){	
			/***enqueue scripts and styles***/
			add_action('admin_enqueue_scripts', array( $this, 'wppizza_enqueue_admin_scripts_and_styles'));
		}
	}	
	/*********************************************************
	*
	*	[class helpers]
	*	@since 3.0
	*
	*********************************************************/
    public function wppizza_enqueue_admin_scripts_and_styles($hook) {
    	wp_register_script(WPPIZZA_SLUG.'_'.$this->class_key.'', plugins_url( 'js/scripts.admin.'.$this->class_key.'.js', WPPIZZA_PLUGIN_PATH ), array('jquery'), WPPIZZA_VERSION ,true);
    	wp_enqueue_script(WPPIZZA_SLUG.'_'.$this->class_key.'');
    }	
	/*******************************************************************************************************************************************************
	*
	* 	[admin ajax]
	*
	********************************************************************************************************************************************************/
	function admin_ajax($wppizza_options){	
		/******************************************************
			[prize tier selection has been changed->add relevant price options input fields]
		******************************************************/
		if($_POST['vars']['field']=='sizeschanged' && $_POST['vars']['id']!='' && isset($_POST['vars']['inpname']) &&  $_POST['vars']['inpname']!=''){
			

			$set_size_id=(int)$_POST['vars']['id'];
			/**sizes**/
			$sizes='';
			if(is_array($wppizza_options['sizes'][$set_size_id])){
				foreach($wppizza_options['sizes'][$set_size_id] as $a=>$b){
					/*if we change the ingredient pricetier, do not use default prices , but just empty ??? what is this doing and why is it here ??? need to find out at some point**/
					if(isset($_POST['vars']['classId']) && $_POST['vars']['classId']=='ingredients'){$price='';}else{$price=$b['price'];}
					$sizes.="<input name='".$_POST['vars']['inpname']."[prices][]' type='text' size='5' value='".$price."'>";
			}}
			$obj['inp']['sizes']=$sizes;
			$obj['element']['sizes']='.'.WPPIZZA_SLUG.'_pricetiers';/**html element (by class name) to empty and replace with new input boxes**/

		
			/**allow other meta boxes to hook into this*/
			$obj=apply_filters('wppizza_ajax_action_admin_sizeschanged', $obj, $set_size_id);
		
			print"".json_encode($obj)."";
			exit();
		}	
		
	}
	/*****************************************************
 	 [sort admin column by title]
 	*****************************************************/
	function wppizza_items_sort( $request ) {
		if(isset ($request['post_type']) && $request['post_type']==''.WPPIZZA_POST_TYPE.''){
			if ( !isset( $request['orderby'] ) || ( isset( $request['orderby'] ) &&  $request['orderby']=='title' ) ) {
				$request = array_merge( $request, array('orderby' => 'title'));
			}
			if ( !isset( $request['order'] )) {
				$request = array_merge( $request, array('order' => 'asc'));
			}
		}
	return $request;
	}


/*********************************************************
*
*		[add metabox container for classes to add to]
*
*********************************************************/
	function wppizza_add_metaboxes() {
    	add_meta_box( WPPIZZA_SLUG, WPPIZZA_NAME.' '.__('Options', 'wppizza-admin'), array($this, 'wppizza_render_metaboxes'), WPPIZZA_SLUG, 'normal', 'high');
	}
	
	function wppizza_render_metaboxes( $meta_options ) {

		global $wppizza_options;
		/**
			add some parameters we probably want to use in metaboxes. 
			add here to pass on to filter and not have to run it multiple times
		**/
		/*meta data for menu item*/
		$meta_values = get_post_meta($meta_options->ID, WPPIZZA_SLUG);
		
		/* set default for new items from sizes id 0 */
		if(empty($meta_values)){
			$default = array();
			$default[0] = array();
			$default[0]['item_tax_alt'] = false;
			$default[0]['sizes'] = 0;
			$default[0]['prices'] = array();
			foreach($wppizza_options['sizes'][0] as $k=>$default_price){
				$default[0]['prices'][$k] = $default_price['price'];
			}
			$default[0]['additives'] = array();
		
		$meta_values = $default;
		}
		
		$meta_values = $meta_values[0];
		/*available sizes*/
		$wppizza_sizes = wppizza_sizes_available(true);

		/**add filter**/
		$wppizza_meta_box=array();
		$wppizza_meta_box=apply_filters('wppizza_filter_admin_metaboxes', $wppizza_meta_box, $meta_values, $wppizza_sizes, $wppizza_options);

		/**implode and output adding nonce**/
		$output=implode('',$wppizza_meta_box);
		$output .= ''.wp_nonce_field( '' . WPPIZZA_PREFIX . '_nonce_meta_box','' . WPPIZZA_PREFIX . '_nonce_meta_box',true,false).'';

		print"".$output;		
	}
	
	function wppizza_save_metaboxes($item_id, $item_details ) {		
		
		/** bypass, when doing "quickedit" (ajax) and /or "bulk edit"  as it will otherwsie loose all meta info (i.e prices, additives etc)!!!***/
		if ( defined('DOING_AJAX') || isset($_GET['bulk_edit'])){
			return;
		}
		
		/* check for nonce, which will also bypass this on install */		
		$nonce = '' . WPPIZZA_PREFIX . '_nonce_meta_box';
		if (! isset( $_POST[$nonce] ) || !wp_verify_nonce(  $_POST[$nonce] , $nonce ) ) {
			return;
		}
		
		// Check post type too
		if(!isset($item_details->post_type) || $item_details->post_type != WPPIZZA_POST_TYPE ){
			return;
		}
		
		global $wppizza_options;
		/**add filter we can hook into from other pages to add meta data**/
		$itemMeta = apply_filters('wppizza_filter_admin_save_metaboxes', $itemMeta = array(), $item_id, $wppizza_options);
		update_post_meta($item_id, WPPIZZA_SLUG, $itemMeta);
	}

	/*******************************************************
		[show order column in wppizza list of post items table]
	******************************************************/
	function wppizza_new_wppizza_column($header_text_columns) {
		$header_text_columns['wppizza-prices'] = __('Prices', 'wppizza-admin');
  		return $header_text_columns;
	}
	/**
	* show custom order column values
	*/
	function wppizza_show_order_column($name, $id){

	  switch($name){

		//ignore for now
		//case 'wppizza-menu_order':
	    // 	$order = $post->menu_order;
	    // 	echo $order;
		//break;

		case 'wppizza-prices':
			global $wppizza_options;
	     	$meta=get_post_meta($id, WPPIZZA_POST_TYPE, true );
	     	$sizes=$wppizza_options['sizes'][$meta['sizes']];
	     	$str='';
	     	if(is_array($sizes)){
	     		/*do not use tables here or bulk edit won't work - no , dunno either why*/
	      		$str.='<div class="wppizza-prices-column">';
	      		foreach($sizes as $k=>$s){
	      			$str.='<span>'.$s['lbl'].'<br />'.wppizza_output_format_price($meta['prices'][$k],$wppizza_options['prices_format']['hide_decimals']).'</span>';
	      		}
	      		$str.='</div>';
     		}
			echo $str;
		break;
		default:
		break;
	   }
	}
	/**
	* 	make column sortable /  not in use / ignored
	*	ignore for now
	*/
	//function wppizza_order_column_register_sortable($columns){
	  //$columns['wppizza-menu_order'] = 'menu_order';
	  //$columns['prices'] = 'prices';
	  //return $columns;
	//}
	
	/*****************************************************
	*
	*	[quickedit prices]
	*
	*****************************************************/
	/*add element to quickedit*/
	function wppizza_add_quick_edit($column, $post_type) {
		if ($column != 'wppizza-prices' || $post_type!=WPPIZZA_POST_TYPE ){ return;}

		/*do we need this ?*/
    	//static $printNonce = TRUE;
    	//if ( $printNonce ) {
        //	$printNonce = FALSE;
        //	wp_nonce_field( plugin_basename( __FILE__ ), 'wppizza_edit_nonce' );
    	//}
		echo'<fieldset class="inline-edit-col-right inline-edit-wppizza-prices" style="width:auto;border:1px dotted #cecece;margin:5px">';
			echo'<div class="inline-edit-col column-'.$column.'">';
				echo'<div style="font-weight:600;text-align:center;text-decoration:underline">'.__('Item Price(s)', 'wppizza-admin').'</div>';
				echo'<div id="wppizza_quickedit_prices"></div>';
			echo'</div>';
		echo'</fieldset>';

	}
	/*set js to insert values*/
	function wppizza_quick_edit_js() {
	    global $current_screen;
	    if (($current_screen->post_type != WPPIZZA_POST_TYPE) || $current_screen->parent_base!='edit') {return;}
		echo'<script type="text/javascript">'.PHP_EOL;
		echo'function wppizza_set_prices(sizes, prices, labels, meta, nonce) {'.PHP_EOL;

		        // refresh the quick menu properly
		        echo'inlineEditPost.revert();'.PHP_EOL;
		        echo'var itemSizes= sizes.split(":");'.PHP_EOL;
		        echo'var itemPrices= prices.split(":");'.PHP_EOL;
		        echo'var itemLabels= labels.split(":");'.PHP_EOL;

		        echo'var doInputs="<div class=\'wppizza-prices-column\'>";'.PHP_EOL;
		        echo'for(var i=0;i<itemSizes.length;i++){'.PHP_EOL;
		       	echo'doInputs+=\'<span>\'+itemLabels[i]+\'<br /><input type="text" name="wppizza[prices][\'+itemSizes[i]+\']" size="5" value="\'+itemPrices[i]+\'" /></span>\';'.PHP_EOL;
		        echo'}';

		        echo'doInputs+="</div>";'.PHP_EOL;
		        
		        /*
		        	allow for other plugins to hook into displaying 
		        	some other form fields or somtheing by simply doing
		        	echo'doInputs+="my addon";'.PHP_EOL;
		        	or somethig
		        */
	    		do_action('wppizza_filter_quick_edit_js' );				        

		        echo'jQuery("#wppizza_quickedit_prices").html(doInputs);'.PHP_EOL;
		echo'}'.PHP_EOL;
		echo'</script>'.PHP_EOL;
	}

	function wppizza_expand_quick_edit_link($actions, $post) {
		global $wppizza_options;
		
		/* not wppizza post type , skip */
	    if ($post->post_type != WPPIZZA_POST_TYPE) {return $actions;}
	    
		/* can this user edit the post ? if not, skip */
	    $can_edit_post    = current_user_can( 'edit_post', $post->ID );		    
	    if ( !$can_edit_post || 'trash' == $post->post_status ) {return $actions;}
	    
	    /*do we need this ?*/
	    //$nonce = wp_create_nonce( 'wppizza_'.$post->ID);

		$getMeta=get_post_meta($post->ID, WPPIZZA_POST_TYPE, true );
		$getSizes=$wppizza_options['sizes'][$getMeta['sizes']];		
			
		
		/* add other meta data as required to js function as json */
		$meta = array();
		$meta = apply_filters('wppizza_filter_expand_quick_edit_link_meta', $meta, $getMeta);
		$meta = json_encode($meta, JSON_FORCE_OBJECT);
		

		$sizes=array();
		$prices=array();
		$labels=array();
		foreach($getSizes as $k=>$s){
			$sizes[$k]=$k;
			$prices[$k]=wppizza_output_format_price($getMeta['prices'][$k],$wppizza_options['prices_format']['hide_decimals']);
			$labels[$k]=$s['lbl'];
		}
		$jsSizes=implode(':',$sizes);
		$jsPrices=implode(':',$prices);
		$jsLabels=implode(':',$labels);

	    /**hijack quick edit link*/
	    $actions['inline hide-if-no-js'] = '<a href="#" class="editinline" title="';
	    $actions['inline hide-if-no-js'] .= esc_attr( __( 'Edit this item inline' ) ) . '"';
	    $actions['inline hide-if-no-js'] .= " onclick='wppizza_set_prices(\"".$jsSizes."\",\"".$jsPrices."\",\"".$jsLabels."\",".$meta.");' >";
	    $actions['inline hide-if-no-js'] .= __( 'Quick&nbsp;Edit' );
	    $actions['inline hide-if-no-js'] .= '</a>';

	return $actions;
	}

	function wppizza_save_quick_edit_data($post_id, $post){
	 // verify if this is an auto save routine.
	  if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {return $post_id;}
	  // Check permissions
	  if ( isset($_POST['post_type'])  && WPPIZZA_POST_TYPE == $_POST['post_type'] ) {
	    if ( !current_user_can( 'edit_page', $post_id ) )
	      return $post_id;
	  } else {
	    if ( !current_user_can( 'edit_post', $post_id ) )
	    return $post_id;
	  }
	  // Authentication passed now we save the data
	  if (!empty($_POST['wppizza']['prices']) && is_array($_POST['wppizza']['prices']) && ($post->post_type != 'revision')) {
		/**current meta*/
		$metaValues=get_post_meta($post_id, WPPIZZA_POST_TYPE, true );

		/**get new prices*/
		$metaValues['prices']=array();
		foreach($_POST['wppizza']['prices'] as $k=>$price){
			$metaValues['prices'][$k]=wppizza_validate_float_only($price,2);;
		}
		/* allow for other meta data to be saved too */
		$metaValues = apply_filters('wppizza_filter_save_quick_edit_data', $metaValues);
		
		update_post_meta($post_id, WPPIZZA_POST_TYPE, $metaValues);

		return $metaValues['prices'];
	  }
	return	;
	}	

	/*********************************************************
	*
	*	[define caps]
	*	@since 3.0
	*
	*********************************************************/
	function wppizza_filter_define_caps($caps){
		/**add editing capability for this posts**/
		$caps[$this->class_key] = array();
		$caps[$this->class_key]['name'] = $this->submenu_caps_title;
		$caps[$this->class_key]['cap'] = ''.WPPIZZA_SLUG.'_cap_'.$this->class_key.'';
		/* 
			using one checkbox for multiple capabilities 
			these will be set on install and update of plugin 
			for all users with manage_options capabilities
		*/
		$caps[$this->class_key]['multi_caps'] = array();
		$caps[$this->class_key]['multi_caps']['read_post'] = 'read_'.WPPIZZA_SLUG.''; 
		$caps[$this->class_key]['multi_caps']['read_private_post'] = 'read_private_'.WPPIZZA_SLUG.'s'; 
		$caps[$this->class_key]['multi_caps']['edit_post'] = 'edit_'.WPPIZZA_SLUG.''; 
		$caps[$this->class_key]['multi_caps']['edit_posts'] = 'edit_'.WPPIZZA_SLUG.'s';
		$caps[$this->class_key]['multi_caps']['edit_others_posts'] = 'edit_others_'.WPPIZZA_SLUG.'s';//should this only be done for admins and editors at some point perhaps ?
		$caps[$this->class_key]['multi_caps']['edit_published_posts'] = 'edit_published_'.WPPIZZA_SLUG.'s';
		$caps[$this->class_key]['multi_caps']['delete_post'] = 'delete_'.WPPIZZA_SLUG.'';
		$caps[$this->class_key]['multi_caps']['delete_posts'] = 'delete_'.WPPIZZA_SLUG.'s';	
		$caps[$this->class_key]['multi_caps']['delete_others_posts'] = 'delete_others_'.WPPIZZA_SLUG.'s';	
		$caps[$this->class_key]['multi_caps']['delete_published_posts'] = 'delete_published_'.WPPIZZA_SLUG.'s';
		$caps[$this->class_key]['multi_caps']['publish_posts'] = 'publish_'.WPPIZZA_SLUG.'s';
		
				
		/* other options , dont think these are necessary but leave them here for reference */
		//$caps[$this->class_key]['multi_caps']['delete_private_posts'] = 'delete_private_'.WPPIZZA_SLUG.'s';


		$caps[$this->class_key]['multi_caps'] = apply_filters('wppizza_filter_post_caps', $caps[$this->class_key]['multi_caps'] );
		
		// let's not enable/list this option for now....probably not required anyway as one should also delete/reassign orders to someone else ...
		//$caps[$this->class_key.'-delete-customers']=array('name'=>__('Delete Customers', 'wppizza-admin') ,'cap'=>'wppizza_cap_delete_customers');
		return $caps;
	}
	/*********************************************************
	*
	*	[set required capability for this page]
	*	@since 3.0
	*
	*********************************************************/    
	function admin_option_page_capability($capability) {		
		$capability = 'wppizza_cap_'.$this->class_key.'';
	return $capability;
	}	
}
/***************************************************************
*
*	[ini]
*
***************************************************************/
$WPPIZZA_POSTS = new WPPIZZA_POSTS();
?>