<?php
/**
* WPPIZZA_REPORTS Class
*
* @package     WPPIZZA
* @subpackage  Submenu Pages / Classes / Reports
* @copyright   Copyright (c) 2015, Oliver Bach
* @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
* @since       3.0
*
*
*/
if ( ! defined( 'ABSPATH' ) ) exit;/*Exit if accessed directly*/


/************************************************************************************************************************
*
*
*	WPPIZZA_REPORTS
*
*
************************************************************************************************************************/
class WPPIZZA_REPORTS{
	/*
	* class ident
	* @var str
	* @since 3.0
	*/
	private $class_key='reports';/*to help consistency throughout class in various places*/
	/*
	* saved wppizza option array key - not required here
	* @var str
	* @since 3.0
	*/
	//private $option_key='reports';
	/*
	* titles/lables
	* @var str
	* @since 3.0
	*/
	private $submenu_page_header;
	private $submenu_page_title;
	private $submenu_caps_title;
	private $submenu_link_label;
	private $submenu_priority = 110;
	/******************************************************************************************************************
	*
	*	[CONSTRUCTOR]
	*
	*	Setup wppizza_meal_sizes subpage
	*	@since 3.0
	*
	******************************************************************************************************************/

	function __construct() {

		/*titles/labels throughout class*/
		add_action('init', array( $this, 'init_admin_lables') );
		/** registering submenu page -> priority 110 **/
		add_action( 'admin_menu', array( $this, 'wppizza_register_submenu_page'), $this->submenu_priority );

		/*register capabilities for this page*/
		add_filter('wppizza_filter_define_caps', array( $this, 'wppizza_filter_define_caps'), $this->submenu_priority);
		/**enqueue css/js**/
		add_action('admin_enqueue_scripts', array( $this, 'wppizza_enqueue_admin_scripts_and_styles'));


		/*execute some helper functions once to use their return multiple times */
		add_action('current_screen', array( $this, 'wppizza_add_helpers') );

		/**admin ajax**/
		add_action('wp_ajax_wppizza_admin_'.$this->class_key.'_ajax', array($this, 'set_admin_ajax') );
	}

	/******************
	*	@since 3.0.26
    *	[admin ajax include file]
    *******************/
	public function init_admin_lables(){
		/*titles/labels throughout class*/
		$this->submenu_page_header	=	apply_filters('wppizza_filter_admin_label_page_header_'.$this->class_key.'', __('Reports','wppizza-admin'));
		$this->submenu_page_title	=	apply_filters('wppizza_filter_admin_label_page_title_'.$this->class_key.'', __('Manage Reports','wppizza-admin'));
		$this->submenu_caps_title	=	apply_filters('wppizza_filter_admin_label_caps_title_'.$this->class_key.'', __('Reports','wppizza-admin'));
		$this->submenu_link_label	=	apply_filters('wppizza_filter_admin_label_link_label_'.$this->class_key.'', __('&middot; Reports','wppizza-admin'));
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
	*	[add global helpers and enque js]
	*	@since 3.0
	*
	*********************************************************/
	public function wppizza_add_helpers($current_screen){
		if($current_screen->id == ''.WPPIZZA_POST_TYPE.'_page_'.$this->class_key.'' && $current_screen->post_type == WPPIZZA_POST_TYPE){
			/**
				[get data set]
			**/
			$export = !empty($_GET['export']) ? true : false;
			$this->report_data_set = WPPIZZA() -> sales_data ->wppizza_report_dataset($export);
			/**
				[export]
			**/
			$this->wppizza_report_export($this->report_data_set);
		}
	}


	/*********************************************************
	*
	*		[add scripts and styles for reports screen]
	*
	*********************************************************/
	function wppizza_enqueue_admin_scripts_and_styles(){
		global $current_screen, $wp_styles, $wp_scripts;
      	/**include reporting js**/
      	if($current_screen->id == ''.WPPIZZA_POST_TYPE.'_page_'.$this->class_key.'' && $current_screen->post_type == WPPIZZA_POST_TYPE){

			/* Get the WP built-in jquery-ui-core version to use for jquery ui*/
			$jquery_ui_core_version = $wp_scripts->registered['jquery-ui-core']->ver;

			/************
				css
			***********/
			/*datepicker*/
			wp_enqueue_style('jquery-style', '//ajax.googleapis.com/ajax/libs/jqueryui/'.$jquery_ui_core_version.'/themes/smoothness/jquery-ui.css');

			/************
				js
			***********/
			wp_enqueue_script('jquery-ui-datepicker');

    		wp_register_script(WPPIZZA_SLUG.'_'.$this->class_key.'', plugins_url( 'js/scripts.admin.'.$this->class_key.'.js', WPPIZZA_PLUGIN_PATH ), array('jquery'), WPPIZZA_VERSION ,true);
    		wp_enqueue_script(WPPIZZA_SLUG.'_'.$this->class_key.'');

	      	wp_register_script(WPPIZZA_SLUG.'-flot', plugins_url( 'js/jquery.flot.min.js', WPPIZZA_PLUGIN_PATH ), array('jquery'), WPPIZZA_VERSION ,true);
	      	wp_enqueue_script(WPPIZZA_SLUG.'-flot');

      		wp_register_script(WPPIZZA_SLUG.'-flotcats', plugins_url( 'js/jquery.flot.categories.min.js', WPPIZZA_PLUGIN_PATH ), array('jquery'), WPPIZZA_VERSION ,true);
      		wp_enqueue_script(WPPIZZA_SLUG.'-flotcats');
      	}
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
		echo'<div id="'.WPPIZZA_SLUG.'-'.$this->class_key.'" class="'.WPPIZZA_SLUG.'-wrap  '.WPPIZZA_SLUG.'-'.$this->class_key.'-wrap">';

		echo"<div class='".WPPIZZA_SLUG."-admin-pageheader'>";
			echo"<h2><span id='".WPPIZZA_SLUG."-header'>".WPPIZZA_NAME." ".$this->submenu_page_header."</span></h2>";
		echo"</div>";

		echo $this->wppizza_report_range_select($this->report_data_set);

		echo $this->wppizza_report_markup($this->report_data_set);
		$this->wppizza_report_js($this->report_data_set);

		echo'</div>';
	}

	/*********************************************************
	*
	*	[range and export selection header ]
	*
	*	@since unknown
	*	@param array
	*	@return str
	*
	*********************************************************/
	private function wppizza_report_range_select($data_set){
		/**make some vars to use**/
		$selectedReport=!empty($_GET['report']) ? $_GET['report'] : '';
		$fromVal=!empty($_GET['from']) ? $_GET['from'] : '';
		$toVal=!empty($_GET['to']) ? $_GET['to'] : '';
		$exportLabel=($data_set['view']=='ini') ? __('Export All','wppizza-admin') : __('Export Range','wppizza-admin');

		$output='';
		$output.='<div id="wppizza-reports-range"  class="button">';

			/*
				range selection
			*/
			$output.='<span id="wppizza-reports-range-select">';
				$output.='<select id="wppizza-reports-set-range">';
					$output.='<option value="" >'.__('Overview','wppizza-admin').'</option>';
					foreach($data_set['reportTypes'] as $rkey=>$rArr){
						$sel=($selectedReport==$rkey) ? 'selected="selected"' : '' ;
						$output.='<option value="'.$rkey.'" '.$sel.'>'.$rArr['lbl'].'</option>';
					}
					if(isset($_GET['from']) && isset($_GET['to'])){
						$output.='<option selected="selected">'.__('Custom Range','wppizza-admin').'</option>';
					}
				$output.='</select>';
			$output.='</span>';

			/*
				date selection
			*/
			$output.='<span id="wppizza-reports-range-set">';
				$output.=''.__('Custom range','wppizza-admin').' : ';
				$output.='<input type="text" size="9" placeholder="yyyy-mm-dd" value="'.$fromVal.'" name="wppizza_reports_start_date" id="wppizza_reports_start_date" readonly="readonly" />';
				$output.='<input type="text" size="9" placeholder="yyyy-mm-dd" value="'.$toVal.'" name="wppizza_reports_end_date" id="wppizza_reports_end_date" readonly="readonly" />';
				$output.='<input type="button" class="button" value="'.__('Go','wppizza-admin').'" id="wppizza_reports_custom_range" />';
			$output.='</span>';


			/*
				export selection and button
			*/
			$output.='<span id="wppizza-reports-range-export">';

				/* button */
				$output.='<input type="button" class="button" value="'.$exportLabel.'" id="wppizza_reports_export" />';

				/*
					allow filterable export reports selection
				*/
				$export_type = apply_filters('wppizza_filter_csv_export_select' , array('default' => __('Summary','wppizza_admin')));
				if(!empty($export_type)){
				$output.='<select id="wppizza_reports_export_type" name="wppizza_reports_export_type">';
					foreach($export_type as $select_value => $select_label){
						$output.='<option value="'.wppizza_latin_lowercase($select_value).'">'.wp_strip_all_tags($select_label, true).'</option>';
					}
				$output.='</select>';
				}

			$output.='</span>';


		$output.='</div>';

	return $output;
	}

	private function wppizza_report_markup($data_set){

		$output=array();
		$output[]='<!--  boxes and graphs -->';
		$output[]='<div id="wppizza-reports-details">';

			$output[]='<!--  sidebar boxes -->';
			$output[]='<div id="wppizza-sidebar-reports" class="wppizza-sidebar">';
			foreach($data_set['boxes'] as $vals){
				$output[]='<div id="'.$vals['id'].'" class="postbox wppizza-reports-postbox">';
				$output[]='<h3 class="button">'.$vals['lbl'].'</h3>';
				$output[]=''.$vals['val'].'';
				$output[]='</div>';
			}
			$output[]='</div>';

			$output[]='<!--  flot graphs -->';
			$output[]='<div id="wppizza-reports-canvas-wrap">';
				$output[]='<h4>'.$data_set['graphs']['label'].'</h4>';
				$output[]='<div style="min-height:150px" id="wppizza-reports-canvas"></div>';
				$output[]='<ul id="wppizza-report-choices"></ul>';
			$output[]='</div>';


			$output[]='<div id="wppizza-sidebar-reports-right" class="wppizza-sidebar-right">';
			foreach($data_set['boxesrt'] as $vals){
				$output[]='<div id="'.$vals['id'].'" class="postbox wppizza-reports-postbox-right '.$vals['class'].'">';
				$output[]='<h3 class="button">'.$vals['lbl'].'</h3>';
				$output[]=''.$vals['val'].'';
				$output[]='</div>';
			}
			$output[]='</div>';


		$output[]='</div>';
		/*implode*/
		$output = implode(PHP_EOL, $output);

		return $output;
	}

	private function wppizza_report_js($data_set){
	?>
		<script>
		jQuery(document).ready(function($){
		$(function() {
				var datasets = {
					<?php
						$i=0;
						foreach($data_set['graphs']['data'] as $gk=>$gv){
							if($i>0){print",";};
							print'"'.$gk.'":{'.$gv.'}';
						$i++;
						}
					?>
				};
				/*********tooltip hover*****/
				$("<div id='wppizza-reports-tooltip'></div>").appendTo("body");
				$("#wppizza-reports-canvas").bind("plothover", function (event, pos, item) {
						if (item) {
							var x = item.datapoint[0],
								y = item.datapoint[1].toFixed(2);

							$("#wppizza-reports-tooltip").html(y)
								.css({top: item.pageY-<?php echo $data_set['graphs']['hoverOffsetTop'] ?>, left: item.pageX+<?php echo $data_set['graphs']['hoverOffsetLeft'] ?>})
								.fadeIn(200);
						} else {
							$("#wppizza-reports-tooltip").hide();
						}
				});
				/************colours***************/
				var i = 1;
				$.each(datasets, function(key, val) {
					val.color = i;
					++i;
				});
				/************radios***************/
				var choiceContainer = $("#wppizza-report-choices");
				$.each(datasets, function(key, val) {
					if(key=='sales_value'){var valchkd='checked="checked"';}else{var valchkd='';}
					choiceContainer.append("<li><label for='" + key + "'><input type='radio' name='wppizza-graph-select' "+valchkd+" id='" + key + "' />"+ val.label + "</label></li>");
				});
				choiceContainer.find("input").click(plotAccordingToChoices);
				/************format legend***************/
				function legendFormatter(v, axis) {
					if(axis.n==1){
						return "<?php echo $data_set['currency'] ?> "+v.toFixed(2);
					}else{
						return v.toFixed(0);
					}
				}
				/************plot***************/
				function plotAccordingToChoices() {
					var data = [];
					choiceContainer.find("input:checked").each(function () {
						var key = $(this).attr("id");
						if (key && datasets[key]) {
							data.push(datasets[key]);
						}
					});
					if (data.length > 0) {
						$.plot("#wppizza-reports-canvas", data,{
							series: {
								lines: {
									show: <?php echo $data_set['graphs']['series']['lines'] ?>
								},
								bars: {
									show: <?php echo $data_set['graphs']['series']['bars'] ?>,
									barWidth: 0.6,
									align: "center"
								},
								points: {
									show: <?php echo $data_set['graphs']['series']['points'] ?>
								}
							},
							grid: {
								hoverable: true
							},
							xaxis: {
								mode: "categories"
							},
							yaxis: {
								min:0,
								tickDecimals: 0,
								tickFormatter: legendFormatter
							}
						});
					}
				}
				plotAccordingToChoices();
			});
		});
		</script>
<?php
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
		return $caps;
	}

	/*********************
		export
	********************/
	function wppizza_report_export($report_data, $delimiter = ','){

		/*
			export not called
		*/
		if(empty($_GET['export'])){
			return;
		}


		/**************************************
			generate custom reports by filter
			@since 3.9
			filter:
			@param empty str
			@param report data array
			@return str (csv format)
		**************************************/
		if(!empty($_GET['type']) && $_GET['type'] != 'default'){

			/*
				sanitise $_GET['type'] var
			*/
			$type = wppizza_latin_lowercase($_GET['type']);

			/*
				start with an empty string here
			*/
			$csv = apply_filters('wppizza_filter_csv_export_'.$type.'', '', $report_data, $type );

			/*
				write to file setting headers and
				triggering save file dialogue
			*/
			$this->do_report_export($csv);
		/*
			make sure to exit
		*/
		exit();
		}


		/*
			export your own report if you want
		*/
		do_action('wppizza_custom_report', $report_data);

		$currency = $report_data['currency'];
		$dataset = $report_data['dataset'];

		/**
			get first and last date
			or make upi a range label from get vars
		**/
		$d=0;
		if(!empty($dataset['sales'])){
			foreach($dataset['sales'] as $date => $order){
				if($d==0){
					$startdate=$date;
				}else{
					$enddate=$date;
				}
			$d++;
			}
			/**in case start and end are the same**/
			$enddate=empty($enddate) ? $startdate : $enddate;
			/** range label **/
			$range_label = ''.$startdate.' - '.$enddate.'';

		}else{
			$range_label = ''.sanitize_text_field($_GET['name']).'';
		}


		/**************************************************************************
			sales by date
		**************************************************************************/
		$result['sales_by_date']='"Range: '.$range_label.'"'.PHP_EOL.PHP_EOL;
		$result['sales_by_date'].='"'.__('sales by dates','wppizza-admin').'"'.PHP_EOL;
		/*sales*/
		$result['sales_by_date'].='"'.__('date','wppizza-admin').'", "'.__('sales value(incl. taxes, charges and discounts)','wppizza-admin').'", "'.__('items order value','wppizza-admin').'", "'.__('number of sales','wppizza-admin').'", "'.__('number of items sold','wppizza-admin').'"  , "'.__('tax on order','wppizza-admin').'"  '.PHP_EOL;
		$d=0;
		/**sum it up*/
		$sales_value_total=0;
		$items_value_total=0;
		$sales_count_total=0;
		$items_count_total=0;
		$sales_order_tax=0;
		foreach($dataset['sales'] as $date=>$order){
			$result['sales_by_date'].=$date . $delimiter . $order['sales_value_total']  . $delimiter . $order['items_value_total'] . $delimiter . $order['sales_count_total'] . $delimiter . $order['items_count_total'] . $delimiter . $order['sales_order_tax'];
			$result['sales_by_date'].=PHP_EOL;

			/**add it up**/
			$sales_value_total+=$order['sales_value_total'];
			$items_value_total+=$order['items_value_total'];
			$sales_count_total+=$order['sales_count_total'];
			$items_count_total+=$order['items_count_total'];
			$sales_order_tax+=$order['sales_order_tax'];

		$d++;
		}
		/**sums of it all*/
		$result['sales_by_date'].='"", "'.__('total','wppizza-admin').'", "'.__('total','wppizza-admin').'", "'.__('total','wppizza-admin').'", "'.__('total','wppizza-admin').'", "'.__('total','wppizza-admin').'" '.PHP_EOL;
		$result['sales_by_date'].=''. $delimiter  . $sales_value_total  . $delimiter . $items_value_total . $delimiter . $sales_count_total . $delimiter . $items_count_total . $delimiter . $sales_order_tax;


		/**************************************************************************
			sales by item
		**************************************************************************/
		if(is_array($dataset['items_summary']) && count($dataset['items_summary'])>0){

			/*add some empty lines first*/
			$result['sales_by_item']=PHP_EOL.PHP_EOL.PHP_EOL.PHP_EOL.PHP_EOL;
			$result['sales_by_item'].='"'.__('sales by item','wppizza-admin').'"'.PHP_EOL;
			/*items*/
			$result['sales_by_item'].='"'.__('quantity','wppizza-admin').'", "'.__('item','wppizza-admin').'", "'.__('total value','wppizza-admin').'"'.PHP_EOL;
			$totalNumberItems=0;
			$totalSalesItems=0;
			foreach($dataset['items_summary'] as $uniqueItem=>$itemDetails){
				$result['sales_by_item'].=$itemDetails['quantity']  . $delimiter . wppizza_decode_entities($itemDetails['title']) . $delimiter . $itemDetails['pricetotal'];
				$result['sales_by_item'].=PHP_EOL;
				/*add it up*/
				$totalNumberItems+=$itemDetails['quantity'];
				$totalSalesItems+=$itemDetails['pricetotal'];
			}

			/*add some empty lines*/
			$result['sales_by_item'].=PHP_EOL;
			//irrelevant as already displayed
			//$result.='"total quantity all items", "",  "total value all items"'.PHP_EOL;
			//$result.=$totalNumberItems  . $delimiter . '' . $delimiter . $totalSalesItems;
		}

		/**************************************************************************
			sales value by gateway
		**************************************************************************/
		if(is_array($dataset['gateways_summary']) && count($dataset['gateways_summary'])>0 && !defined('WPPIZZA_OMIT_REPORT_GATEWAYS_SUMMARY')){
			$result['sales_by_gateway']=PHP_EOL.'"'.__('payment type','wppizza-admin').'"'.PHP_EOL;
			/*items*/
			$result['sales_by_gateway'].='"'.__('type','wppizza-admin').'", "'.__('total value','wppizza-admin').'"'.PHP_EOL;
			foreach($dataset['gateways_summary'] as $uniqueGateway=>$gatewayValue){
				$result['sales_by_gateway'].=$uniqueGateway  . $delimiter . $gatewayValue;
				$result['sales_by_gateway'].=PHP_EOL;
			}
			/*add some empty lines */
			$result['sales_by_gateway'].=PHP_EOL;
		}

		/**************************************************************************
			sales value by order status
		**************************************************************************/
		if(is_array($dataset['order_status_summary']) && count($dataset['order_status_summary'])>0 && !defined('WPPIZZA_OMIT_REPORT_ORDER_STATUS_SUMMARY')){
			$result['sales_by_status']=PHP_EOL.'"'.__('order status','wppizza-admin').'"'.PHP_EOL;
			/*items*/
			$result['sales_by_status'].='"'.__('status','wppizza-admin').'", "'.__('count','wppizza-admin').'", "'.__('total value','wppizza-admin').'"'.PHP_EOL;
			foreach($dataset['order_status_summary'] as $uniqueKey=>$statusValue){
				$result['sales_by_status'].=$uniqueKey  . $delimiter . $statusValue['count'] . $delimiter . $statusValue['value'];
				$result['sales_by_status'].=PHP_EOL;
			}
			/*add some empty lines */
			$result['sales_by_status'].=PHP_EOL;
		}

		/**************************************************************************
			sales value by custom order status
		**************************************************************************/
		if(is_array($dataset['order_custom_status_summary']) && count($dataset['order_custom_status_summary'])>0 && !defined('WPPIZZA_OMIT_REPORT_CUSTOM_ORDER_STATUS_SUMMARY')){
			$result['sales_by_custom_status']=PHP_EOL.'"'.__('custom options','wppizza-admin').'"'.PHP_EOL;
			/*items*/
			$result['sales_by_custom_status'].='"'.__('option','wppizza-admin').'", "'.__('count','wppizza-admin').'", "'.__('total value','wppizza-admin').'"'.PHP_EOL;
			foreach($dataset['order_custom_status_summary'] as $uniqueKey=>$statusValue){
				$result['sales_by_custom_status'].=$uniqueKey  . $delimiter . $statusValue['count'] . $delimiter . $statusValue['value'];
				$result['sales_by_custom_status'].=PHP_EOL;
			}
			/*add some empty lines */
			$result['sales_by_custom_status'].=PHP_EOL;
		}

		/* filter array to be able to delete / add things to the output before imploding if required */
		$result = apply_filters('wppizza_filter_reports_export_results', $result, $report_data);
		$result = implode('',$result);

		/**************************************************************************
			write to file setting headers and
			triggering save file dialogue
		**************************************************************************/
		$this->do_report_export($result);

	exit();
	}

	/*********************************************************
	*
	*	[do the exporting, setting headers etc, trigger save csv dialogue]
	*	@since 3.9
	*	@param str
	*	@return void
	*********************************************************/
	function do_report_export($result){

		/*
			set some header vars
		*/
		$encoding='base64';
		$mime='text/csv; charset='.WPPIZZA_CHARSET.'';
		$extension='.csv';

		/*
			set filename - date
		*/
		$filename = array();
		$filename[]=date('Y.m.d', current_time('timestamp'));

		/*
			set filename - add range
		*/
		if(isset($_GET['from']) && isset($_GET['to'])){
			$filename[]='-[';
			$filename[]=esc_sql(str_replace("-",".",$_GET['from']));
			$filename[]='-';
			$filename[]=esc_sql(str_replace("-",".",$_GET['to']));
			$filename[]=']';
		}else{
			if(isset($_GET['name'])){
				$filename[]='-'.esc_sql(str_replace(" ","_",$_GET['name']));
			}
		}

		/*
			set filename - type. if not default
		*/
		if(isset($_GET['type']) && $_GET['type']!='default'){
			$filename[]='-'.esc_sql(str_replace(" ","_",$_GET['type']));
		}

		/*filter if you want*/
		$filename = apply_filters('wppizza_filter_report_export_title', $filename);
		$filename=implode("",$filename);//implode to string
		$filename = ''.strtolower(wppizza_validate_alpha_only(WPPIZZA_NAME)).'_report_'.$filename.''.$extension.'';

		/*
			set headers and content
		*/
		header("Content-Encoding: ".WPPIZZA_CHARSET."");
		header("Content-Type: ".$mime."");
		header('Content-Disposition: attachment; filename="'.$filename.'"');
		header("Content-Length: " . strlen($result));
		echo $result;

	exit();
	}

}

/***************************************************************
*
*	[ini]
*
***************************************************************/
$WPPIZZA_REPORTS = new WPPIZZA_REPORTS();
?>