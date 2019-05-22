<?php
if(!defined('DOING_AJAX') || !DOING_AJAX){
	header('HTTP/1.0 400 Bad Request', true, 400);
	print"you cannot call this script directly";
  exit; //just for good measure
}
/**testing variables ***********************/
//sleep(2);//when testing jquery fadeins etc
/******************************************/
global $wppizza_options;


#########################################
#	[supress errors unless debug]
#########################################
$wppizzaDebug=wppizza_debug();
if(!$wppizzaDebug){
	error_reporting(0);
}

###################################################################
#	action hooks for modules to hook into to execute ajax calls.
###################################################################
do_action('wppizza_ajax_admin', $wppizza_options);/* global admin ajax */
/* subpages admin ajax */
if(!empty($this->class_key)){
	do_action('wppizza_ajax_admin_'.$this->class_key.'', $wppizza_options);
}
?>