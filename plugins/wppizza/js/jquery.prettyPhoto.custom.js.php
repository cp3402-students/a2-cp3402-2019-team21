<?php
/******************************************************************************************************************
	if you want to customize prettyPhoto() (theme etc) , 
	copy this file as wppizza.prettyPhoto.custom.js (WITHOUT THE PHP EXTENSION) to your theme directory 
	and edit as required (get rid of all the php tags , echo etc, $_GET vars etc and hardcode as required)
	see: http://www.no-margin-for-errors.com/projects/prettyphoto-jquery-lightbox-clone/documentation for options
******************************************************************************************************************/
header('Content-Type: application/javascript');
?>
jQuery(document).ready(function($){
	$("a[rel^='wpzpp']").prettyPhoto({theme:'<?php echo $_GET['t'] ?>',social_tools:false,show_title:false});
});