<?php
/**
* WPPIZZA_WIDGETS Class
*
* @package     WPPIZZA
* @subpackage  widgets
* @copyright   Copyright (c) 2015, Oliver Bach
* @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
* @since       3.0
*
*/
if ( ! defined( 'ABSPATH' ) ) exit;/*Exit if accessed directly*/


/************************************************************************************************************************
*
*
*	WPPIZZA_WIDGETS
*
*
************************************************************************************************************************/
class WPPIZZA_WIDGETS extends WP_Widget{

	function __construct() {
		/***************************************
			classname and description
		***************************************/
    	$widget_options = array (
	        'classname' => WPPIZZA_WIDGET_CSS_CLASS, /*css class for widget*/
        	'description' => sprintf( __( '%s widgets', 'wppizza-admin' ), WPPIZZA_NAME)/* description under widget */
    	);
		parent::__construct(false, WPPIZZA_NAME, $widget_options );
	}

    /*******************************************************
     * Outputs the content of the widget.
     * @args            The array of form elements
     * @instance
     ******************************************************/
    function widget($args, $instance) {

		extract( $args, EXTR_SKIP );
		/*initialize output var***/
		$widgetOutput="";
		// set widget title
		$title = apply_filters( 'widget_title', $instance['title'] );

		/******************
			widget title and before widget if
			a) not cart
			b) online orders not disabled
			c) not cart and on orderpage
		******************/
		if($instance['type']=='cart' && (wppizza_is_orderpage() || !empty($wppizza_options['layout']['disable_online_order']) )){
			$widgetOutput.='';
		}else{

			/******************
				widget before
			******************/
			$widgetOutput.="". $before_widget ."";

			if( !empty( $title ) && empty( $instance['suppresstitle'] )){
				$widgetOutput.="". $before_title . $title . $after_title."";
			}
		}
		/***************************************************************************
		*
		*	shopping cart
		*
		***************************************************************************/
		if($instance['type']=='cart'){
			$do_markup = true ;

			/* do not output cart widget if we are on the orderpage */
			if(wppizza_is_orderpage()){
				$do_markup = false ;
			}
			/*disable shoppingcart when disable_online_order is set */
			if($do_markup && !empty($wppizza_options['layout']['disable_online_order'])){
				$do_markup = false ;
			}

			/*
				should we output the widget/markup ?
			*/
			if($do_markup){
				$wdgArgs=array('type="cart"');
				if(isset( $instance['openingtimes']) && $instance['openingtimes']!=''){
					$wdgArgs[]='openingtimes="1"';
				}
				if(!empty($instance['minicart'])){
					if($instance['minicart'] === 'only'){
						$wdgArgs[]='minicart="only"';
					}else{
						$wdgArgs[]='minicart="1"';
					}
				}
				if(isset( $instance['orderinfo']) && $instance['orderinfo']!=''){
					$wdgArgs[]='orderinfo="1"';
				}
				if(isset( $instance['height']) && $instance['height']>0){
					$wdgArgs[]='height="'.(int)$instance['height'].'px"';
				}
				if(isset( $instance['width']) && $instance['width']!=''){
					$wdgArgs[]='width="'.$instance['width'].'"';
				}
				$widgetOutput.= do_shortcode('['.WPPIZZA_SLUG.' '.implode(" ",$wdgArgs).']');

			}else{
				$widgetOutput.='';
			}
		}
		/***************************************************************************
		*
		*	navigation
		*
		***************************************************************************/
		if($instance['type']=='navigation'){

			$wdgArgs[]='type="'.$instance['type'].'"';

			if($instance['navterm']!=''){
				$wdgArgs[]='parent="'.$instance['navterm'].'"';
			}
			if(!empty($instance['as_dropdown'])){
				$wdgArgs[]='as_dropdown="1"';
			}

			$widgetOutput.= do_shortcode('['.WPPIZZA_SLUG.' '.implode(" ",$wdgArgs).']');
		}
		/***************************************************************************
		*
		*	openingtimes
		*
		***************************************************************************/
		if($instance['type']=='openingtimes'){
			$widgetOutput.= do_shortcode('['.WPPIZZA_SLUG.' type="'.$instance['type'].'"]');
		}

		/***************************************************************************
		*
		*	orderpage
		*
		***************************************************************************/
		if($instance['type']=='orderpage'){
			$do_markup = true;

			/* do not output orderpage widget if we are already on the orderpage */
			if(wppizza_is_orderpage() && !empty($instance['is_widget'])){
				$do_markup = false ;
			}

			/*disable orderpage when disable_online_order is set */
			if($do_markup && !empty($wppizza_options['layout']['disable_online_order'])){
				$do_markup = false ;
			}


			/*
				should we output the widget/markup ?
			*/
			if($do_markup){
				$wdgArgs=array();
				if(!empty($instance['nocart'])){
					$wdgArgs[]='nocart="1"';
				}
				if(!empty($instance['is_widget'])){
					$wdgArgs[]='is_widget="1"';
				}
				/* construct shortcode */
				$widgetOutput.= do_shortcode('['.WPPIZZA_SLUG.' type="'.$instance['type'].'" '.implode(" ",$wdgArgs).']');
			}else{
				$widgetOutput='';
			}
		}
		/***************************************************************************
		*
		*	display items in chosen category or first if not set
		*
		***************************************************************************/
		if($instance['type']=='category'){
			$wdgArgs=array();
			if($instance['term']!=''){
				$wdgArgs[]='category="'.$instance['term'].'"';
			}
			if(isset($instance['noheader']) && $instance['noheader']!=''){
				$wdgArgs[]='noheader="1"';
			}
			if(isset($instance['showadditives']) && $instance['showadditives']=='1'){
				$wdgArgs[]='showadditives="1"';
			}
			if(isset($instance['showadditives']) && $instance['showadditives']=='0'){
				$wdgArgs[]='showadditives="0"';
			}
			$widgetOutput.= do_shortcode('['.WPPIZZA_SLUG.' '.implode(" ",$wdgArgs).']');
		}
		/***************************************************************************
		*
		*	searchbox
		*
		***************************************************************************/
		if($instance['type']=='search'){

			$wdgArgs=array();
			$incl=array();
			/*types*/
			if(isset($instance['wppizza'])){
				$incl[]=''.WPPIZZA_POST_TYPE.'';
			}
			if(isset($instance['post'])){
				$incl[]='post';
			}
			if(isset($instance['page'])){
				$incl[]='page';
			}
			
			
			if(isset($instance['custom_post_type']) && !empty($instance['custom_post_type'])){
				$other_cpt=explode(',',$instance['custom_post_type']);
				/* get available post types */
				$set_cpts = get_post_types();
				foreach($other_cpt as $add_cpt){
					$add_cpt = trim($add_cpt);
					if(!empty($add_cpt) && isset($set_cpts[$add_cpt]) ){
						$incl[]=trim($add_cpt);
					}
				}
			}
			if(count($incl)>0){
				$wdgArgs[]='include="'.implode(",",$incl).'"';
			}
			if(isset($instance['loggedinonly'])){
				$wdgArgs[]='loggedinonly="1"';
			}

			/*loggedinonly*/
			if(isset($instance['loggedinonly'])  && !is_user_logged_in()){
				$widgetOutput='';
				$after_widget='';
			}else{
				$widgetOutput.= do_shortcode('['.WPPIZZA_SLUG.' type="'.$instance['type'].'" '.implode(" ",$wdgArgs).']');
			}
		}

		/******************
			widget after if
			a) not cart
			b) online orders not disabled
			c) not cart and on orderpage
		******************/
		if($instance['type']=='cart' && (wppizza_is_orderpage() || !empty($wppizza_options['layout']['disable_online_order']) )){
			$widgetOutput.='';
		}else{
			/******************
				widget after
			******************/
			$widgetOutput.="". $after_widget."";
		}


	print"".$widgetOutput;
    }

    /***************************************************************************************************************************************************************
    *
    * Generates the administration form for the widget.
    * @instance    The array of keys and values for the widget.
    *
    ****************************************************************************************************************************************************************/
	function form($instance) {

		    $instance = wp_parse_args(
		        (array)$instance,$this->wppizza_default_widget_settings()
		    );
		    $title = isset($instance['title']) ? esc_attr($instance['title']) : '';
		    $suppresstitle = checked($instance['suppresstitle'],true,false);

		    $as_dropdown = checked(!empty($instance['as_dropdown']),true,false);
		    $noheader = checked($instance['noheader'],true,false);
		    $nocart = !isset($instance['nocart']) ? false : checked($instance['nocart'],true,false);

		    $type  = isset($instance['type']) ? esc_attr($instance['type']) : '';

		    $showadditives  = isset($instance['showadditives']) ? esc_attr($instance['showadditives']) : '';

		    $type  = isset($instance['type']) ? esc_attr($instance['type']) : '';
		  	$term  = isset($instance['term']) ? esc_attr($instance['term']) : '';
		  	$navterm  = isset($instance['navterm']) ? esc_attr($instance['navterm']) : '';
		  	$openingtimes = checked($instance['openingtimes'],true,false);
		  	$minicart = !empty($instance['minicart']) ? (esc_attr($instance['minicart'] === 'only') ? 'only' : true ) : false;

		  	//$minicartonly = !empty($instance['minicartonly']) ? true : false;
		  	$orderinfo = checked($instance['orderinfo'],true,false);
		  	$width = $instance['width'] ?  esc_attr($instance['width']) : '';
		  	$height = $instance['height'] ?  absint($instance['height']) : '';


		  	$posttypewppizza = !empty($instance['wppizza']) ? true : false;
		  	$posttypepost = !empty($instance['post']) ? true : false;
		  	$posttypepage = !empty($instance['page']) ? true : false;
		  	$custom_post_type = !empty($instance['custom_post_type']) ?  esc_attr($instance['custom_post_type']) : '';
		  	$loggedinonly = !empty($instance['loggedinonly']) ? true : false;

		?>
		<div id="<?php echo $this->id; ?>" class="<?php echo WPPIZZA_SLUG; ?>">

			    <p>
			    	<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e("Widget Title", 'wppizza-admin'); ?>:</label>
			    	<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
			    	<br/>
			    	<input class="checkbox" type="checkbox" id="<?php echo $this->get_field_id('suppresstitle'); ?>" name="<?php echo $this->get_field_name('suppresstitle'); ?>" <?php echo $suppresstitle; ?> value="1" />
			    	<label for="<?php echo $this->get_field_id( 'suppresstitle' ); ?>"><?php _e("Suppress Title ?", 'wppizza-admin'); ?></label>

			    </p>

			    <p class="<?php echo WPPIZZA_SLUG; ?>-type">
			    	<label for="<?php echo $this->get_field_id( 'type' ); ?>"><?php _e("Widget Type", 'wppizza-admin'); ?>:</label>
			        <select id="<?php echo $this->get_field_id( 'type' ); ?>" class="widefat <?php echo WPPIZZA_SLUG; ?>-widget-select" name="<?php echo $this->get_field_name( 'type' ); ?>">
			        <?php foreach($this->wppizza_shortcode_type_options() as $key => $val){ ?>
			        	<option value="<?php echo $key; ?>" <?php selected($key,$type,true) ?>><?php echo $val; ?></option>
			        <?php } ?>
			        </select>
			    </p>
				<div id="<?php echo WPPIZZA_SLUG; ?>-selected-<?php echo $this->number; ?>" class="<?php echo WPPIZZA_SLUG; ?>-selected">

				    <p class="<?php echo WPPIZZA_SLUG; ?>-selected-navigation" <?php if($type=='navigation'){echo "style='display:block'";}else{echo "style='display:none'";} ?>>
						<?php
						$allterms = get_terms( WPPIZZA_TAXONOMY, array('hide_empty' => 0) );
						?>
				        <select id="<?php echo $this->get_field_id( 'navterm' ); ?>" class="widefat" name="<?php echo $this->get_field_name( 'navterm' ); ?>">
				        	<option value="" <?php selected('',$navterm,true) ?>><?php _e("All Categories [default]", 'wppizza-admin'); ?></option>
				        <?php foreach($allterms as $theterm){ ?>
				        	<option value="<?php echo $theterm->slug; ?>" <?php selected($theterm->slug,$navterm,true) ?>><?php echo $theterm->name; ?></option>
				        <?php } ?>
				        </select><br/>
				        <input class="checkbox" type="checkbox" id="<?php echo $this->get_field_id('as_dropdown'); ?>" name="<?php echo $this->get_field_name('as_dropdown'); ?>" <?php echo $as_dropdown; ?> value="1" />
				    	<label for="<?php echo $this->get_field_id( 'as_dropdown' ); ?>"><?php _e("As dropdown ?", 'wppizza-admin'); ?></label><br/>
				        <small style="color:blue"><?php _e("Please refer to <a href='http://docs.wp-pizza.com/getting-started/?section=setup' target='_blank'>Set-Up</a> and  <a href='http://docs.wp-pizza.com/shortcodes/?section=navigation' target='_blank'>Navigation Shortcode/Widget</a> documentation when using this widget (or shortcode) to display the navigation", 'wppizza-admin'); ?></small>
					</p>

				    <p class="<?php echo WPPIZZA_SLUG; ?>-selected-orderpage" <?php if($type=='orderpage'){echo "style='display:block'";}else{echo "style='display:none'";} ?>>
				    	<input class="checkbox" type="checkbox" id="<?php echo $this->get_field_id('nocart'); ?>" name="<?php echo $this->get_field_name('nocart'); ?>" <?php echo $nocart; ?> value="1" />
				    	<label for="<?php echo $this->get_field_id( 'nocart' ); ?>"><?php _e("Suppress Cart ?", 'wppizza-admin'); ?></label>
				    	<input type="hidden" id="<?php echo $this->get_field_id('is_widget'); ?>" name="<?php echo $this->get_field_name('is_widget'); ?>" value="1" />
					</p>


				    <p class="<?php echo WPPIZZA_SLUG; ?>-selected-orderpage" <?php if($type=='orderpage'){echo "style='display:block'";}else{echo "style='display:none'";} ?>>
				        <small style="color:red"><?php _e("You most probably want to create a dedicated orderpage with the following shortcode instead [wppizza type='orderpage'].", 'wppizza-admin'); ?></small>
				        <small style="color:blue"><br><?php _e("This widget will not be displayed on orderpage itself or if cart is empty", 'wppizza-admin'); ?></small>
					</p>

				    <p class="<?php echo WPPIZZA_SLUG; ?>-selected-openingtimes" <?php if($type=='openingtimes'){echo "style='display:block'";}else{echo "style='display:none'";} ?>>
				        <small style="color:blue"><?php _e("Displays openingtimes set in wppizza->settings->openingtimes. shortcode [wppizza type='openingtimes']", 'wppizza-admin'); ?></small>
					</p>

				    <p class="<?php echo WPPIZZA_SLUG; ?>-selected-category" <?php if($type=='category'){echo "style='display:block'";}else{echo "style='display:none'";} ?>>
						<?php
						$allterms = get_terms( WPPIZZA_TAXONOMY, array('hide_empty' => 0) );
						?>
				        <select id="<?php echo $this->get_field_id( 'term' ); ?>" class="widefat" name="<?php echo $this->get_field_name( 'term' ); ?>">
				        	<option value="" <?php selected('',$term,true) ?>><?php _e("First Category [default]", 'wppizza-admin'); ?></option>
				        <?php foreach($allterms as $theterm){ ?>
				        	<option value="<?php echo $theterm->slug; ?>" <?php selected($theterm->slug,$term,true) ?>><?php echo $theterm->name; ?></option>
				        <?php } ?>
				        </select><br/>

				        <small style="color:blue"><?php _e("Show Additives List at bottom of page ?", 'wppizza-admin'); ?></small><br/>
				        <select id="<?php echo $this->get_field_id( 'showadditives' ); ?>" class="widefat" name="<?php echo $this->get_field_name( 'showadditives' ); ?>">
				        	<option value="" <?php selected('',$showadditives,true) ?>><?php _e("Auto [default]", 'wppizza-admin'); ?></option>
				        	<option value="0" <?php selected('0',$showadditives,true) ?>><?php _e("Force Hide", 'wppizza-admin'); ?></option>
				        	<option value="1" <?php selected('1',$showadditives,true) ?>><?php _e("Force Show", 'wppizza-admin'); ?></option>
				        </select><br/>

				    	<input class="checkbox" type="checkbox" id="<?php echo $this->get_field_id('noheader'); ?>" name="<?php echo $this->get_field_name('noheader'); ?>" <?php echo $noheader; ?> value="1" />
				    	<label for="<?php echo $this->get_field_id( 'noheader' ); ?>"><?php _e("Suppress Category Header ?", 'wppizza-admin'); ?></label>

					</p>

					<p class="<?php echo WPPIZZA_SLUG; ?>-selected-cart" <?php if($type=='cart'){echo "style='display:block'";}else{echo "style='display:none'";} ?>>
				    	<input class="checkbox" type="checkbox" id="<?php echo $this->get_field_id('openingtimes'); ?>" name="<?php echo $this->get_field_name('openingtimes'); ?>" <?php echo $openingtimes; ?> value="1" />
				    	<label for="<?php echo $this->get_field_id( 'openingtimes' ); ?>"><?php _e("Display Openingtimes ?", 'wppizza-admin'); ?></label><br/>

				    	<input class="checkbox" type="checkbox" id="<?php echo $this->get_field_id('orderinfo'); ?>" name="<?php echo $this->get_field_name('orderinfo'); ?>" <?php echo $orderinfo; ?> value="1" />
				    	<label for="<?php echo $this->get_field_id( 'orderinfo' ); ?>"><?php _e("Display Order Info ?", 'wppizza-admin'); ?></label><br/>

						<input id="<?php echo $this->get_field_id( 'width' ); ?>" name="<?php echo $this->get_field_name( 'width' ); ?>" type="text" size="2" value="<?php echo $width; ?>" />
				    	<label for="<?php echo $this->get_field_id( 'width' ); ?>"><?php _e("Width [&#37; or px]", 'wppizza-admin'); ?></label>
				    	<br/><small style="margin-left:10px"><?php _e("i.e. 200px or 85% - defaults to 100% if left blank", 'wppizza-admin'); ?></small>
				    	<br />

				    	<input id="<?php echo $this->get_field_id( 'height' ); ?>" name="<?php echo $this->get_field_name( 'height' ); ?>" type="text" size="2" value="<?php echo $height; ?>" />
				    	<label for="<?php echo $this->get_field_id( 'height' ); ?>"><?php _e("Item List Height [Integer]", 'wppizza-admin'); ?></label>
				    	<br/><small style="margin-left:10px"><?php _e("css defaults to 250px if left blank", 'wppizza-admin'); ?></small>
				    	<br />


						<input class="checkbox" type="radio" id="<?php echo $this->get_field_id('nominicart'); ?>" name="<?php echo $this->get_field_name('minicart'); ?>" <?php checked($minicart,false,true) ?> value="0" />
				    	<label for="<?php echo $this->get_field_id( 'nominicart' ); ?>"><?php _e('No "minicart"', 'wppizza-admin'); ?></label>
				    	<br />
						<input class="checkbox" type="radio" id="<?php echo $this->get_field_id('minicart'); ?>" name="<?php echo $this->get_field_name('minicart'); ?>" <?php checked($minicart,'1',true) ?> value="1" />
				    	<label for="<?php echo $this->get_field_id( 'minicart' ); ?>"><?php _e('Add small "minicart" to top of screen if main cart not in view', 'wppizza-admin'); ?></label>
				    	<br />
						<input class="checkbox" type="radio" id="<?php echo $this->get_field_id('minicartonly'); ?>" name="<?php echo $this->get_field_name('minicart'); ?>" <?php checked($minicart,'only',true) ?> value="only" />
				    	<label for="<?php echo $this->get_field_id( 'minicartonly' ); ?>"><?php _e('Show "minicart" only, do not show/use main cart', 'wppizza-admin'); ?></label><br/>

				    	<small style="color:blue"><br><?php _e("This widget will not be displayed on orderpage itself", 'wppizza-admin'); ?></small>
				    	<small style="color:blue;"><br><?php _e("See also WPPizza->Layout->minicart for additional options", 'wppizza-admin'); ?></small>


					</p>

					<p class="<?php echo WPPIZZA_SLUG; ?>-selected-search" <?php if($type=='search'){echo "style='display:block'";}else{echo "style='display:none'";} ?>>
				    	<label><?php _e("include the following in search results:", 'wppizza-admin'); ?></label><br />

				    	<input class="checkbox" type="checkbox" id="<?php echo $this->get_field_id('wppizza'); ?>" name="<?php echo $this->get_field_name('wppizza'); ?>" <?php checked($posttypewppizza,true,true) ?> value="1" />
				    	<label for="<?php echo $this->get_field_id( 'wppizza' ); ?>"><?php _e("wppizza menu items", 'wppizza-admin'); ?>
				    	<small style="color:blue;"><br> <?php _e('If enabled, create a <a href="http://docs.wp-pizza.com/developers/?section=wppizza-markup-single-single-php">single page</a> and <a href="https://docs.wp-pizza.com/developers/?section=wppizza-markup-search-search-php">search page</a> appropriate for your theme', 'wppizza-admin'); ?></small>
				    	</label>
				    	<br/>

				    	<input class="checkbox" type="checkbox" id="<?php echo $this->get_field_id('post'); ?>" name="<?php echo $this->get_field_name('post'); ?>" <?php checked($posttypepost,true,true) ?> value="1" />
				    	<label for="<?php echo $this->get_field_id( 'post' ); ?>"><?php _e("blog posts", 'wppizza-admin'); ?></label>
				    	<br/>

				    	<input class="checkbox" type="checkbox" id="<?php echo $this->get_field_id('page'); ?>" name="<?php echo $this->get_field_name('page'); ?>" <?php checked($posttypepage,true,true) ?> value="1" />
				    	<label for="<?php echo $this->get_field_id( 'page' ); ?>"><?php _e("pages", 'wppizza-admin'); ?></label>
				    	<br/>

						<input id="<?php echo $this->get_field_id( 'custom_post_type' ); ?>" name="<?php echo $this->get_field_name( 'custom_post_type' ); ?>" type="text" size="30" value="<?php echo $custom_post_type; ?>" />
				    	<label for="<?php echo $this->get_field_id( 'custom_post_type' ); ?>"><?php _e("Additional post types", 'wppizza-admin'); ?></label>
				    	<br/><small style="margin-left:0px"><?php _e("you can add additional, comma separated,  post types here that should be included in the search.", 'wppizza-admin'); ?></small>
				    	<br/><br/>



				    	<input class="checkbox" type="checkbox" id="<?php echo $this->get_field_id('loggedinonly'); ?>" name="<?php echo $this->get_field_name('loggedinonly'); ?>" <?php checked($loggedinonly,true,true) ?> value="1" />
				    	<label for="<?php echo $this->get_field_id( 'loggedinonly' ); ?>"><?php _e("show only for logged in users ?", 'wppizza-admin'); ?></label><br/>

					</p>
				</div>
		</div>
		<?php
   	}
    /*******************************************************
    *
    * set default and return options for widget
    *
    ******************************************************/
	private function wppizza_default_widget_settings(){
		 $defaults=array(
            'title' => __("Shoppingcart", 'wppizza-admin'),
            'type' => 'cart',
            'suppresstitle' => '',
            'noheader' => '',
            'width' => '',
            'height' => '',
            'openingtimes' => 'checked="checked"',
            'orderinfo' => 'checked="checked"'
        );
		return $defaults;
	}
    /*******************************************************
    *
    * available main options to choose from in widget
    *
    ******************************************************/
	private function wppizza_shortcode_type_options(){
		$items['category']=__('Category Page', 'wppizza-admin');
		$items['navigation']=__('Navigation', 'wppizza-admin');
		$items['cart']=__('Cart', 'wppizza-admin');
		$items['orderpage']=__('Orderpage', 'wppizza-admin');
		$items['openingtimes']=__('Openingtimes', 'wppizza-admin');
		$items['search']=__('Search', 'wppizza-admin');

		return $items;
	}

}

/***************************************************************
*
*	[init widgets]
*
***************************************************************/
function wppizza_register_widgets() {
	register_widget( 'WPPIZZA_WIDGETS');
}
add_action( 'widgets_init', 'wppizza_register_widgets' );
?>