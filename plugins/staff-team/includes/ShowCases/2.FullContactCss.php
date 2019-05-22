<style>
/*-- SEARCH --*/
<?php echo '.' . $theme . ' #full_search '; ?>
.search_cont { border: 1px solid <?php echo isset($param_values['full_search_border']) ? $param_values['full_search_border'] : '#D9D9D9'; ?>; }

<?php echo '.' . $theme . ' #full_search '; ?>
.search_cont[placeholder]{ color: <?php echo isset($param_values['full_search_color']) ? $param_values['full_search_color'] : '#999999'; ?> !important;}
<?php echo '.' . $theme . ' #full_search '; ?>
.search_cont::-moz-placeholder {color: <?php echo isset($param_values['full_search_color']) ? $param_values['full_search_color'] : '#999999'; ?> !important;} 
<?php echo '.' . $theme . ' #full_search '; ?>
.search_cont:-moz-placeholder {color: <?php echo isset($param_values['full_search_color']) ? $param_values['full_search_color'] : '#999999'; ?> !important; }
<?php echo '.' . $theme . ' #full_search '; ?>
.search_cont:-ms-input-placeholder {color: <?php echo isset($param_values['full_search_color']) ? $param_values['full_search_color'] : '#999999'; ?> !important; }


/*--- PAGINATION ---*/
<?php echo '.' . $theme . ' #full_pgnt '; ?>
.staff_pagination {
	border: 0.1em solid <?php echo isset($param_values['full_pagination_border_color']) ? $param_values['full_pagination_border_color'] : '#DADADA'; ?>;
	line-height: <?php echo ((isset($param_values['full_pagination_font']) ? $param_values['full_pagination_font'] : '16')+10); ?>px;
	border-right: 0;
}
<?php echo '.' . $theme . ' #full_pgnt '; ?>
.staff_pagination li {
	border-right: 0.1em solid <?php echo isset($param_values['full_pagination_border_color']) ? $param_values['full_pagination_border_color'] : '#DADADA'; ?>;
	background: <?php echo isset($param_values['full_pagination_bg']) ? $param_values['full_pagination_bg'] : '#FFFFFF'; ?>;
}
<?php echo '.' . $theme . ' #full_pgnt '; ?>
.staff_pagination li span,
<?php echo '.' . $theme . ' #full_pgnt '; ?>
.staff_pagination li a {
	color: <?php echo isset($param_values['full_pagination_font_color']) ? $param_values['full_pagination_font_color'] : '#000000'; ?> !important;
	font-size: <?php echo isset($param_values['full_pagination_font']) ? $param_values['full_pagination_font'] : '16'; ?>px !important;
}	
<?php echo '.' . $theme . ' #full_pgnt '; ?>
.staff_pagination li:hover,
<?php echo '.' . $theme . ' #full_pgnt '; ?>
.staff_pagination li:hover a,
<?php echo '.' . $theme . ' #full_pgnt '; ?>
.staff_pagination li:hover span,
<?php echo '.' . $theme . ' #full_pgnt '; ?>
.staff_pagination .active_pg span { 
	background: <?php echo isset($param_values['full_active_pagination_bg']) ? $param_values['full_active_pagination_bg'] : '#00A99D'; ?>;
	color: <?php echo isset($param_values['full_pagination_bg']) ? $param_values['full_pagination_bg'] : '#FFFFFF'; ?> !important
}

/*--- CONTACTS ---*/
<?php echo '.' . $theme . ' #full_contact '; ?>
.staff_contact{ 
	height: <?php echo (isset($param_values['full_contact_height']) ? $param_values['full_contact_height'] : '260'); ?>px;
	width:100%; padding:5px; 
}

/*--- IMAGES ---*/
<?php echo '.' . $theme . ' #full_contact '; ?>
.left_image{
	border: <?php echo isset($param_values['full_border_width']) ? $param_values['full_border_width'] : '1'; ?>px <?php echo isset($param_values['full_border_style']) ? $param_values['full_border_style'] : 'solid'; ?> <?php echo isset($param_values['full_border_color']) ? $param_values['full_border_color'] : '#d9d9d9'; ?>;
	/*height: <?php echo isset($param_values['full_contact_height']) ? $param_values['full_contact_height'] : '260' ?>px;*/height:100%;
	width: <?php echo isset($param_values['full_picture_width']) ? $param_values['full_picture_width'] : '35' ?>%;
	position: relative;
	overflow:hidden;
	float:left;
}


/*-- HOVER --*/
<?php 	
if(isset($param_values['full_image_hover_bg_color'])) {
	$full_hover_bg = $param_values['full_image_hover_bg_color'];
} else $full_hover_bg = '#00A99D';
$hover_bg_color='rgba('.HEXDEC(SUBSTR($full_hover_bg, 1, 2)).','.HEXDEC(SUBSTR($full_hover_bg, 3, 2)).','.HEXDEC(SUBSTR($full_hover_bg, 5, 2)).',0.5'.')';
?>

<?php echo '.' . $theme . ' #full_contact '; ?>
.staff_overlay{		
	background:<?php echo $hover_bg_color; ?> !important;
}
<?php echo '.' . $theme . ' #full_contact '; ?>
.left_image:hover .staff_overlay { opacity:1; }


<?php echo '.' . $theme . ' #full_contact '; ?>
.right_content{		
	border: <?php echo isset($param_values['full_border_width']) ? $param_values['full_border_width'] : '1'; ?>px <?php echo isset($param_values['full_border_style']) ? $param_values['full_border_style'] : 'solid'; ?> <?php echo isset($param_values['full_border_color']) ? $param_values['full_border_color'] : '#d9d9d9'; ?>;
	background: <?php echo isset($param_values['full_background_color']) ? $param_values['full_background_color'] : '#FFFFFF'; ?>;
	width:<?php echo ((100-(isset($param_values['full_picture_width']) ? $param_values['full_picture_width'] : '35'))-3); ?>%;
	/*height: <?php echo (isset($param_values['full_contact_height']) ? $param_values['full_contact_height'] : '260'); ?>px;*/height:100%;
	padding:1% 3%;
	float:right;
	position:relative;
}
<?php echo '.' . $theme . ' #full_contact '; ?>
.contact_content{
	font-size:<?php echo isset($param_values['full_text_size']) ? $param_values['full_text_size'] : '14'; ?>px;
	color: <?php echo isset($param_values['full_text_color']) ? $param_values['full_text_color'] : '#393939'; ?>;
	margin-bottom: 3%;
	line-height:22px;
	overflow: hidden;
	text-align:left;
	max-height:37%;
}
<?php echo '.' . $theme . ' #full_contact '; ?>
.contact_content a { color: <?php echo isset($param_values['full_link_color']) ? $param_values['full_link_color'] : '#B3B3B3'; ?> !important;}

<?php echo '.' . $theme . ' #full_contact '; ?>
.contact_content a:hover { 
	color: <?php echo isset($param_values['full_link_hover_color']) ? $param_values['full_link_hover_color'] : '#00A99D'; ?> !important;
}
/*--- TITLE ---*/
<?php echo '.' . $theme . ' #full_contact '; ?>
.staff_top_info{ position:inherit; }

<?php echo '.' . $theme . ' #full_contact '; ?>
.staff_cont_name{        
	border-bottom:2px dashed <?php echo isset($param_values['full_title_color']) ? $param_values['full_title_color'] : '#b3b3b3'; ?>;
	color: <?php echo isset($param_values['full_title_color']) ? $param_values['full_title_color'] : '#b3b3b3'; ?> !important;
	font-size:<?php echo isset($param_values['full_title_size']) ? $param_values['full_title_size'] : '20'; ?>px;
	padding-bottom:6px;
	width: 100%;
}
<?php echo '.' . $theme . ' #full_contact '; ?>
.staff_cont_name a,
<?php echo '.' . $theme . ' #full_contact '; ?>
.staff_cont_name a:hover{ 		
	color: <?php echo isset($param_values['full_title_color']) ? $param_values['full_title_color'] : '#b3b3b3'; ?> !important; 
	font-size:<?php echo isset($param_values['full_title_size']) ? $param_values['full_title_size'] : '20'; ?>px;
}

/*-- CATEGORY --*/
<?php echo '.' . $theme . ' #full_contact '; ?>
.staff_category{
	color: <?php echo isset($param_values['full_title_color']) ? $param_values['full_title_color'] : '#b3b3b3'; ?> !important;
	font-size:<?php echo isset($param_values['full_text_size']) ? $param_values['full_text_size'] : '14'; ?>px;
	width:100%;
}

<?php echo '.' . $theme . ' #full_contact '; ?>
.staff_full_bottom{ 
	bottom:5%; width:91%;
	position:absolute;
	display:block; 
}

/*--more_info_plus--*/
<?php echo '.' . $theme . ' #full_contact '; ?>
.staff_more_info_plus{
	border: 2px solid <?php echo isset($param_values['full_link_color']) ? $param_values['full_link_color'] : '#B3B3B3'; ?> !important;
	left: 50%; margin-left: -30px; top:50%;
}
<?php echo '.' . $theme . ' #full_contact '; ?>
.staff_more_info_plus:hover{
	border: 2px solid <?php echo isset($param_values['full_link_hover_color']) ? $param_values['full_link_hover_color'] : '#00A99D'; ?> !important;
}

<?php echo '.' . $theme . ' #full_contact '; ?>
.staff_more_info_plus:before { color:<?php echo isset($param_values['full_link_color']) ? $param_values['full_link_color'] : '#FFFFFF'; ?> !important; }

<?php echo '.' . $theme . ' #full_contact '; ?>
.staff_more_info_plus:hover:before { color:<?php echo isset($param_values['full_link_hover_color']) ? $param_values['full_link_hover_color'] : '#00A99D'; ?> !important; }

/*--more_info_button--*/
<?php echo '.' . $theme . ' #full_contact '; ?>
.staff_more_info_btn {
	background: <?php echo isset($param_values['full_button_bg_color']) ? $param_values['full_button_bg_color'] : '#FFFFFF'; ?>;
	border:1px solid <?php echo isset($param_values['full_button_color']) ? $param_values['full_button_color'] : '#B3B3B3'; ?>;
	text-align: center;
	margin-right:2%;
	float:left;
}
<?php echo '.' . $theme . ' #full_contact '; ?>
.staff_more_info_btn a {
	color:<?php echo isset($param_values['full_button_color']) ? $param_values['full_button_color'] : '#B3B3B3'; ?> !important;
	padding: 6px 15px !important;
	font-size: 20px;
}
<?php echo '.' . $theme . ' #full_contact '; ?>
.staff_more_info_btn:hover {
	background: <?php echo isset($param_values['full_button_hover_bg_color']) ? $param_values['full_button_hover_bg_color'] : '#00A99D'; ?>;
}
<?php echo '.' . $theme . ' #full_contact '; ?>
.staff_more_info_btn:hover a {
	color: <?php echo isset($param_values['full_button_hover_color']) ? $param_values['full_button_hover_color'] : '#000000'; ?> !important;
}

/*-- SOCIAL_ICON --*/
<?php echo '.' . $theme . ' #full_contact '; ?>
.staff_soc_icons ul{
    display: inline-block;
    float: right;
}

<?php echo '.' . $theme . ' #full_contact '; ?>
.staff_soc_icons ul li, 
<?php echo '.' . $theme . ' #full_contact '; ?>
.staff_icon{ 
	border-radius: <?php echo isset($param_values['full_icon_circle']) && $param_values['full_icon_circle']=='1'? '50%' : '0px'; ?>;
	display: inline-block;
}

<?php echo '.' . $theme . ' #full_contact '; ?>
.staff_icon{
	background: <?php echo isset($param_values['full_social_bg_color']) ? $param_values['full_social_bg_color'] : '#B3B3B3'; ?>;
	border: 1px solid <?php echo isset($param_values['full_icons_color']) ? $param_values['full_icons_color'] : '#FFFFFF'; ?>;
	position: relative;
	text-align: center;
	font-size: 17px;
}

<?php echo '.' . $theme . ' #full_contact '; ?>
.staff_icon:hover{background: <?php echo isset($param_values['full_social_hover_bg_color']) ? $param_values['full_social_hover_bg_color'] : '#00A99D'; ?>;}

<?php echo '.' . $theme . ' #full_contact '; ?>
.fa {
	color: <?php echo isset($param_values['full_icons_color']) ? $param_values['full_icons_color'] : '#FFFFFF'; ?>;
	padding: 15px 20px;
}
<?php echo '.' . $theme . ' #full_contact '; ?>
.fa:hover {color: <?php echo isset($param_values['full_icons_hover_color']) ? $param_values['full_icons_hover_color'] : '#FFFFFF'; ?>;}

<?php echo '.' . $theme . ' #full_contact '; ?>
.staff_icon.facebook,
<?php echo '.' . $theme . ' #full_contact '; ?>
.staff_icon.instagram,
<?php echo '.' . $theme . ' #full_contact '; ?>
.staff_icon.twitter,
<?php echo '.' . $theme . ' #full_contact '; ?>
.staff_icon.gplus{
	display: none !important;
}
/*-- CHECKED ICONS --*/
<?php 	
if(isset($param_values['full_social_icons'])  && in_array(0,$param_values['full_social_icons'])) { ?>
	<?php echo '.' . $theme . ' #full_contact '; ?>
	.staff_icon.facebook {display: block !important; }
<?php
} 
if(isset($param_values['full_social_icons'])  && in_array(1,$param_values['full_social_icons'])) { ?>
	<?php echo '.' . $theme . ' #full_contact '; ?>
	.staff_icon.instagram {display: block !important; }
<?php
}
if(isset($param_values['full_social_icons'])  && in_array(2,$param_values['full_social_icons'])) { ?>
	<?php echo '.' . $theme . ' #full_contact '; ?>
	.staff_icon.twitter {display: block !important; }
<?php
}
if(isset($param_values['full_social_icons'])  && in_array(3,$param_values['full_social_icons'])) { ?>
	<?php echo '.' . $theme . ' #full_contact '; ?>
	.staff_icon.gplus {display: block !important; }
<?php 
} ?>

/*-*********************************-POPUP-********************************-*/
/*-Close Button-*/
<?php echo '.' . $theme . ' #full_popup '; ?>
.close_popup_circle{
	border:2px solid <?php echo isset($param_values['full_popoup_close_color']) ? $param_values['full_popoup_close_color'] : '#FFFFFF'; ?> !important;
}
<?php echo '.' . $theme . ' #full_popup '; ?>
.close_popup_circle span{
	color: <?php echo isset($param_values['full_popoup_close_color']) ? $param_values['full_popoup_close_color'] : '#FFFFFF'; ?> !important; 
}

<?php echo '.' . $theme . ' #full_popup '; ?>
.popup_info{
	background: <?php echo isset($param_values['full_popup_bg_color']) ? $param_values['full_popup_bg_color'] : '#F3F3F4'; ?>;	
	width:80%; height:100%;
	position:absolute;
	overflow: hidden;
	top:0%; left: 50%;
	margin-left: -40%;
}

<?php echo '.' . $theme . ' #full_popup '; ?>
.popup_info .stPopOut{
	position: absolute;
	overflow-y: auto;
	height: 100%;
	width: 102%;
	right: -2%;
}

<?php echo '.' . $theme . ' #full_popup '; ?>
.popup_info .staff_top_info{
	background: <?php echo isset($param_values['full_popup_title_bg_color']) ? $param_values['full_popup_title_bg_color'] : '#00A99D'; ?>;
	position:relative;
	text-align: center;
	margin-bottom: 2%;
	padding: 1% 0;
	width:100%;
	top:0;
}
<?php echo '.' . $theme . ' #full_popup '; ?>
.staff_top_info .staff_cont_name{
	color: <?php echo isset($param_values['full_popup_title_color']) ? $param_values['full_popup_title_color'] : '#FFFFFF'; ?> !important;
	border-bottom:1px solid <?php echo isset($param_values['full_popup_title_color']) ? $param_values['full_popup_title_color'] : '#FFFFFF'; ?>;
	font-size: <?php echo isset($param_values['full_popup_title_size']) ? $param_values['full_popup_title_size'] : '40'; ?>px;
	padding-top: 0.5%;
	width: 80%;		
}
<?php echo '.' . $theme . ' #full_popup '; ?>
.staff_top_info .staff_category{ 
	color: <?php echo isset($param_values['full_popup_title_color']) ? $param_values['full_popup_title_color'] : '#FFFFFF'; ?> !important;
	font-size: <?php echo ((isset($param_values['full_text_size']) ? $param_values['full_text_size'] : '14')+5); ?>px !important; 
	padding: 10px 0 0;
}

<?php echo '.' . $theme . ' #full_popup '; ?>
.leftPart{
	width: <?php echo isset($param_values['full_popup_pic_width']) ? $param_values['full_popup_pic_width'] : '38'; ?>%;		
	left:1%;
	float:left;
	padding: 0% 1%;
	height: initial;
	position:relative;
}
<?php echo '.' . $theme . ' #full_popup '; ?>
.left_image{
	margin-bottom: 5%;
	border-radius:0;
	border:none;
	width:100%;
}

<?php echo '.' . $theme . ' #full_popup '; ?>
.left_image .staff_image_border { 
	height: <?php echo isset($param_values['full_popup_picture_height']) ? $param_values['full_popup_picture_height'] : '275'?>px;
	margin-bottom: 5%;
}

<?php echo '.' . $theme . ' #full_popup '; ?>
.staff_full_bottom { position:static; bottom:0; width:100%;}

/*-SocIcons-*/	
<?php echo '.' . $theme . ' #full_popup '; ?>
.staff_soc_icons { 
	float:right;
	display: block;
}

<?php echo '.' . $theme . ' #full_popup '; ?>
.staff_icon{ 
	background: <?php echo isset($param_values['full_popup_social_bg_color']) ? $param_values['full_popup_social_bg_color'] : '#FFFFFF'; ?>; 
	border-radius: 0 !important;
}
<?php echo '.' . $theme . ' #full_popup '; ?>
.staff_icon:hover{ background: <?php echo isset($param_values['full_popup_social_hover_bg_color']) ? $param_values['full_popup_social_hover_bg_color'] : '#00A99D'; ?>; }

<?php echo '.' . $theme . ' #full_popup '; ?>
.fa {
	color: <?php echo isset($param_values['full_popup_icons_color']) ? $param_values['full_popup_icons_color'] : '#B3B3B3'; ?>;
	padding:13px 20px;
}
<?php echo '.' . $theme . ' #full_popup '; ?>
.fa:hover {color: <?php echo isset($param_values['full_popup_icons_hover_color']) ? $param_values['full_popup_icons_hover_color'] : '#FFFFFF'; ?>;}

/*-- CHECKED ICONS --*/
<?php echo '.' . $theme . ' #full_popup '; ?>
.staff_icon.facebook,
<?php echo '.' . $theme . ' #full_popup '; ?>
.staff_icon.instagram,
<?php echo '.' . $theme . ' #full_popup '; ?>
.staff_icon.twitter,
<?php echo '.' . $theme . ' #full_popup '; ?>
.staff_icon.gplus{
	display: none !important;
}
<?php 	
if(isset($param_values['full_popup_social_icons'])  && in_array(0,$param_values['full_popup_social_icons'])) { ?>
	<?php echo '.' . $theme . ' #full_popup '; ?>
	.staff_icon.facebook {display: block !important; }
<?php
} 
if(isset($param_values['full_popup_social_icons'])  && in_array(1,$param_values['full_popup_social_icons'])) { ?>
	<?php echo '.' . $theme . ' #full_popup '; ?>
	.staff_icon.instagram {display: block !important; }
<?php
}
if(isset($param_values['full_popup_social_icons'])  && in_array(2,$param_values['full_popup_social_icons'])) { ?>
	<?php echo '.' . $theme . ' #full_popup '; ?>
	.staff_icon.twitter {display: block !important; }
<?php
}
if(isset($param_values['full_popup_social_icons'])  && in_array(3,$param_values['full_popup_social_icons'])) { ?>
	<?php echo '.' . $theme . ' #full_popup '; ?>
	.staff_icon.gplus {display: block !important; }
<?php 
} ?>

/*--Buttons--*/
<?php echo '.' . $theme . ' #full_popup '; ?>
.staff_more_info_btn {
	background: <?php echo isset($param_values['full_popup_button_bg']) ? $param_values['full_popup_button_bg'] : '#FFFFFF'; ?>; 
	border-bottom:8px solid <?php echo isset($param_values['full_popup_button_color']) ? $param_values['full_popup_button_color'] : '#B3B3B3'; ?>;
	height:auto; width: auto;
}
<?php echo '.' . $theme . ' #full_popup '; ?>
.staff_more_info_btn a {
	color: <?php echo isset($param_values['full_popup_button_color']) ? $param_values['full_popup_button_color'] : '#B3B3B3'; ?> !important;
	font-size: <?php echo ((isset($param_values['full_icons_size']) ? $param_values['full_icons_size'] : '22')-3); ?>px;
	width: 120px;
	margin: 8px;
}
<?php echo '.' . $theme . ' #full_popup '; ?>
.staff_more_info_btn:hover { background: <?php echo isset($param_values['full_popup_button_hover_bg']) ? $param_values['full_popup_button_hover_bg'] : '#00A99D'; ?>;}

<?php echo '.' . $theme . ' #full_popup '; ?>
.staff_more_info_btn:hover a{ 
	color: <?php echo isset($param_values['full_popup_button_hover_color']) ? $param_values['full_popup_button_hover_color'] : '#FFFFFF'; ?> !important;
}

/*--Content--*/
<?php echo '.' . $theme . ' #full_popup '; ?>
.right_content{
	width: <?php echo ((100-(isset($param_values['full_popup_pic_width']) ? $param_values['full_popup_pic_width'] : '38'))-1) ?>%;
	background:none;
	padding-top:0;
	height:auto;
	border:none;
}
<?php echo '.' . $theme . ' #full_popup '; ?>
.right_content table { border-collapse: separate; border: none; width:100%;}

<?php echo '.' . $theme . ' #full_popup '; ?>
.right_content .param_name{display:table-cell;}

<?php echo '.' . $theme . ' #full_popup '; ?>
.right_content .full_params td {
	background: <?php echo isset($param_values['full_popup_param_bg_color']) ? $param_values['full_popup_param_bg_color'] : '#F3F3F4'; ?>;
	color: <?php echo isset($param_values['full_popup_param_color']) ? $param_values['full_popup_param_color'] : '#000000'; ?> !important;
	font-size: <?php echo (isset($param_values['full_text_size']) ? $param_values['full_text_size'] : '14'); ?>px !important; 
	border:1px solid #dadada;
	padding:5px;
}
<?php echo '.' . $theme . ' #full_popup '; ?>
.right_content .full_params .param_value a,
<?php echo '.' . $theme . ' #full_popup '; ?>
.popup_content a {
	color: <?php echo isset($param_values['full_popup_link_color']) ? $param_values['full_popup_link_color'] : '#B3B3B3'; ?> !important;
}
<?php echo '.' . $theme . ' #full_popup '; ?>
.right_content .full_params .param_value a:hover,
<?php echo '.' . $theme . ' #full_popup '; ?>
.popup_content a:hover {
	color: <?php echo isset($param_values['full_popup_link_hover_color']) ? $param_values['full_popup_link_hover_color'] : '#00A99D'; ?> !important;
}
<?php echo '.' . $theme . ' #full_popup '; ?>
.right_content .param_name {
	text-transform: uppercase;
	font-style: italic;
	font-weight: 500;
}

<?php echo '.' . $theme . ' #full_popup '; ?>
.right_content .popup_content {
	color: <?php echo isset($param_values['full_popup_text_color']) ? $param_values['full_popup_text_color'] : '#393939'; ?> !important;
	font-size: <?php echo (isset($param_values['full_popup_text_size']) ? $param_values['full_popup_text_size'] : '15'); ?>px !important; 
	line-height: 23px; margin-bottom: 2%; margin-top: 3%;
}
<?php echo '.' . $theme . ' #full_popup '; ?>
.popup_content:first-letter{
	font-size: <?php echo ((isset($param_values['full_popup_text_size']) ? $param_values['full_popup_text_size'] : '15')+40); ?>px !important;
	color:<?php echo isset($param_values['full_popup_title_bg_color']) ? $param_values['full_popup_title_bg_color'] : '#B3B3B3'; ?> !important;
	font-family:serif !important;
	line-height: 0;
}

/*-******************************************************-RESPONSIVE_TABLET-********************************************************-*/
<?php echo '.' . $theme . ' #full_contact '; ?>
.staff_tablet .staff_contact {
	height: <?php echo ((isset($param_values['full_contact_height']) ? $param_values['full_contact_height'] : '260')+100); ?>px
}

<?php echo '.' . $theme . ' #full_contact '; ?>
.staff_tablet .left_image{
	width: <?php echo ((isset($param_values['full_picture_width']) ? $param_values['full_picture_width'] : '35')+3); ?>%;
	height:100%;
}
<?php echo '.' . $theme . ' #full_contact '; ?>
.staff_tablet .right_content {
	width:<?php echo ((100-(isset($param_values['full_picture_width']) ? $param_values['full_picture_width'] : '35'))-7); ?>%;
	height:100%;
}
<?php echo '.' . $theme . ' #full_contact '; ?>
.staff_tablet .staff_more_info_btn { padding: 1.1% 5%; width: 30%;} 

/*-popup-*/
<?php echo '.' . $theme . ' #full_popup '; ?>
.staff_tablet .popup_info { width:100%; height: 100%; left:0; margin:0; padding:0; }

<?php echo '.' . $theme . ' #full_contact '; ?>
.staff_tablet .staff_more_info_btn,
<?php echo '.' . $theme . ' #full_contact '; ?>
.staff_phone .staff_more_info_btn,
<?php echo '.' . $theme . ' #full_popup '; ?>
.staff_tablet .staff_more_info_btn,
<?php echo '.' . $theme . ' #full_popup '; ?>
.staff_phone .staff_more_info_btn { 
	width: auto !important; 
	margin: 2% auto !important;
    display: table; float: none;
	position:static !important;
} 

<?php echo '.' . $theme . ' #full_popup '; ?>
.staff_tablet .leftPart{
	width: 70%; float: none;
    display:flex; margin: 0 auto;
}
<?php echo '.' . $theme . ' #full_popup '; ?>
.staff_tablet .left_image{
	width:100%;
	height: inherit !important;
}
<?php echo '.' . $theme . ' #full_popup '; ?>
.staff_tablet .staff_full_bottom{
	position:absolute;
	top:100%; left:0;
}
<?php echo '.' . $theme . ' #full_popup '; ?>
.staff_tablet .staff_full_bottom.noAb{ position:static;}
 
<?php echo '.' . $theme . ' #full_contact '; ?>
.staff_tablet .staff_soc_icons,
<?php echo '.' . $theme . ' #full_contact '; ?>
.staff_phone .staff_soc_icons,
<?php echo '.' . $theme . ' #full_popup '; ?>
.staff_tablet .staff_soc_icons,
<?php echo '.' . $theme . ' #full_popup '; ?>
.staff_phone .staff_soc_icons {
	float:none; display:table;
	margin:0 auto;
}
<?php echo '.' . $theme . ' #full_popup '; ?>
.staff_tablet .left_image .staff_image_border{
	height: <?php echo ((isset($param_values['full_popup_picture_height']) ? $param_values['full_popup_picture_height'] : '275')+40); ?>px;
}
<?php echo '.' . $theme . ' #full_popup '; ?>
.staff_tablet .right_content {
	width: 95%;
    margin: 20% auto 1%;
}

/*-******************************************************-RESPONSIVE_PHONE-********************************************************-*/
<?php echo '.' . $theme . ' #full_contact '; ?>
.staff_phone .staff_contact {height: <?php echo ((isset($param_values['full_contact_height']) ? $param_values['full_contact_height'] : '260')+300); ?>px}

<?php echo '.' . $theme . ' #full_contact '; ?>
.staff_phone .left_image{ width:100%; height:35%;}
<?php echo '.' . $theme . ' #full_contact '; ?>
.staff_phone .right_content{ width:100%; height:65%;}


<?php echo '.' . $theme . ' #full_popup '; ?>
.staff_phone .left_image { width:60%; height: auto !important; overflow: visible; }

<?php echo '.' . $theme . ' #full_contact '; ?>
.staff_phone .staff_cont_name,
<?php echo '.' . $theme . ' #full_contact '; ?>
.staff_phone .staff_category{
	width:100%;
	text-align:center;
}
@media screen and (max-width: 540px){
	<?php echo '.' . $theme . ' #full_contact '; ?>
	.staff_more_info_btn{float:none; margin:2% 0;}
	<?php echo '.' . $theme . ' #full_contact '; ?>
	.staff_phone .left_image { width:100%;}
}

/*-popup-*/
<?php echo '.' . $theme . ' #full_popup '; ?>
.staff_phone .right_content{ width:100%;}

<?php echo '.' . $theme . ' #full_popup '; ?>
.staff_phone .popup_info{ width:100%; height:100%; left:0; margin:0; }

<?php echo '.' . $theme . ' #full_popup '; ?>
.staff_phone .staff_top_info .staff_cont_name{padding-top:10%;}

<?php echo '.' . $theme . ' #full_popup '; ?>
.staff_phone .left_image{ float:none; margin:0 auto; }

<?php echo '.' . $theme . ' #full_popup '; ?>
.staff_phone .leftPart{width:100%;}
