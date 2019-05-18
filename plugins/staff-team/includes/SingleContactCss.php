<style>
<?php echo '.' . $theme; ?>
a:focus { outline: none; }

<?php echo '.' . $theme . ' '; ?>
div { box-sizing: border-box !important; }

<?php echo '.' . $theme . ' #single_contact '; ?>
.single_inform {
	background: <?php echo isset($param_values['single_background_color']) ? $param_values['single_background_color'] : '#FFFFFF'; ?>;
	display: inline-block;
	padding:0 10px;
	margin:4% 0;
	width:100%;
}

<?php echo '.' . $theme . ' #single_contact '; ?>
.img_content {
	margin:2% <?php echo isset($param_values['single_image_middle']) && $param_values['single_image_middle'] == '1' ? '0%' : 'auto'; ?>;
	float:<?php echo isset($param_values['single_image_middle']) && $param_values['single_image_middle'] == '1' ? 'left' : 'none'; ?>;
}

<?php echo '.' . $theme . ' #single_contact '; ?>
.single_cont_main_picture {
	width: 100%; height: 100%;
	background-position: center;
	background-repeat: no-repeat;
	background-size: 75%;
}

/*-Top_Info-*/
<?php echo '.' . $theme . ' #single_contact '; ?>
.top_info{		
	display: inline-block;
	margin-bottom: 2%
}

<?php echo '.' . $theme . ' #single_contact '; ?>
.cont_name{
	color:<?php echo isset($param_values['single_title_color']) ? $param_values['single_title_color'] : '#04aeda'; ?>;
	font-size: <?php echo isset($param_values['single_title_size']) ? $param_values['single_title_size'] : '25'; ?>px;
	font-weight:700;		
}
<?php echo '.' . $theme . ' #single_contact '; ?>
.cont_categ {
	color:<?php echo isset($param_values['single_title_color']) ? $param_values['single_title_color'] : '#04aeda'; ?>;
	font-size: <?php echo isset($param_values['single_text_size']) ? $param_values['single_text_size'] : '15'; ?>px;
	padding-top: 7px;		
}

/*-Parameters-*/
<?php echo '.' . $theme . ' #single_contact '; ?>
.single_params{
	border-collapse: collapse;
	background: <?php echo isset($param_values['single_cont_param_bg_color']) ? $param_values['single_cont_param_bg_color'] : '#FFFFFF'; ?>;		
}
<?php echo '.' . $theme . ' #single_contact '; ?>
.single_params td{ 
	border: <?php echo isset($param_values['single_border_width']) ? $param_values['single_border_width'] : '1'; ?>px <?php echo isset($param_values['single_border_style']) ? $param_values['single_border_style'] : 'solid'; ?> <?php echo isset($param_values['single_border_color']) ? $param_values['single_border_color'] : '#D9D9D9'; ?>;
	padding:4px 20px;
}

<?php echo '.' . $theme . ' #single_contact '; ?>
.single_params a{
	color:<?php echo isset($param_values['single_link_color']) ? $param_values['single_link_color'] : '#04aeda'; ?> !important;
}
<?php echo '.' . $theme . ' #single_contact '; ?>
.single_params a:hover{
	color:<?php echo isset($param_values['single_link_hover_color']) ? $param_values['single_link_hover_color'] : '#797979'; ?> !important;
}

<?php echo '.' . $theme . ' #single_contact '; ?>
.param_name{
	color:<?php echo isset($param_values['single_cont_param_title_color']) ? $param_values['single_cont_param_title_color'] : '#04aeda'; ?> !important;
	font-size: <?php echo ((isset($param_values['single_text_size']) ? $param_values['single_text_size'] : '15')+2); ?>px;
}
<?php echo '.' . $theme . ' #single_contact '; ?>
.param_value{
	color:<?php echo isset($param_values['single_cont_param_text_color']) ? $param_values['single_cont_param_text_color'] : '#797979'; ?>;
	font-size: <?php echo isset($param_values['single_text_size']) ? $param_values['single_text_size'] : '15'; ?>px;
}
<?php echo '.' . $theme . ' #single_contact '; ?>
.param_value a,
<?php echo '.' . $theme . ' #single_contact '; ?>
.contAllDescription a{ color:<?php echo isset($param_values['single_link_color']) ? $param_values['single_link_color'] : '#04AEDA'; ?> !important; }

<?php echo '.' . $theme . ' #single_contact '; ?>
.param_value a:hover,
<?php echo '.' . $theme . ' #single_contact '; ?>
.contAllDescription a:hover{ color:<?php echo isset($param_values['single_link_hover_color']) ? $param_values['single_link_hover_color'] : '#797979'; ?> !important; }

<?php if(isset($param_values['single_image_middle']) && $param_values['single_image_middle'] == '1'){ ?>
	<?php echo '.' . $theme . ' #single_contact '; ?>
	.img_content { 
		width: 40%; height:210px; 
		border:1px solid <?php echo isset($param_values['single_border_color']) ? $param_values['single_border_color'] : '#D9D9D9'; ?>;
	}
	<?php echo '.' . $theme . ' #single_contact '; ?>
	.top_info{ width:55%; float:right;}
	<?php echo '.' . $theme . ' #single_contact '; ?>
	.cont_name{ 
		border-bottom:1px <?php echo isset($param_values['single_border_style']) ? $param_values['single_border_style'] : 'solid'; ?> <?php echo isset($param_values['single_border_color']) ? $param_values['single_border_color'] : '#D9D9D9'; ?>;
		float:none;
	}
	<?php echo '.' . $theme . ' #single_contact '; ?>
	.cont_categ{ float:none;}
	<?php echo '.' . $theme . ' #single_contact '; ?>
	.single_params{width:55%; float:right;}
<?php
}
else { ?>
	<?php echo '.' . $theme . ' #single_contact '; ?>
	.img_content {
		width: <?php echo isset($param_values['single_image_width']) ? $param_values['single_image_width'] : '230'; ?>px;
		height: <?php echo ((isset($param_values['single_image_width']) ? $param_values['single_image_width'] : '230')-60); ?>px;
	}
	<?php echo '.' . $theme . ' #single_contact '; ?>
	.top_info{ 
		border-bottom:1px <?php echo isset($param_values['single_border_style']) ? $param_values['single_border_style'] : 'solid'; ?> <?php echo isset($param_values['single_border_color']) ? $param_values['single_border_color'] : '#D9D9D9'; ?>;
		padding: 0 2%;
		width:100%;
	}
	<?php echo '.' . $theme . ' #single_contact '; ?>
	.cont_name{float:left;}
	<?php echo '.' . $theme . ' #single_contact '; ?>
	.cont_categ {float:right;}
	<?php echo '.' . $theme . ' #single_contact '; ?>
	.single_params{width:100%;}
<?php }	?>


/*-Content-*/
<?php echo '.' . $theme . ' #single_contact '; ?>
.contAllDescription{
	color:<?php echo isset($param_values['single_text_color']) ? $param_values['single_text_color'] : '#797979'; ?>;
	font-size: <?php echo isset($param_values['single_text_size']) ? $param_values['single_text_size'] : '15'; ?>px;
	line-height: 23px;
	margin:3% 0;
	clear: both;
}

/*-Message-*/
<?php echo '.' . $theme; ?>
.mess_result.mess_success{
	color:<?php echo isset($param_values['single_title_color']) ? $param_values['single_title_color'] : '#04aeda'; ?> !important;
	font-size: <?php echo isset($param_values['single_title_size']) ? $param_values['single_title_size'] : '25'; ?>px;
}
<?php echo '.' . $theme; ?>
.mess_result.mess_error{
	color:#ff0000 !important;
	font-size: <?php echo isset($param_values['single_title_size']) ? $param_values['single_title_size'] : '25'; ?>px;
}
<?php echo '.' . $theme; ?>
.mess_result{
	text-align:center;
	margin-bottom:2%;
}

<?php echo '.' . $theme . ' #message_div '; ?>
.message_table{
	background: <?php echo isset($param_values['single_mess_param_bg_color']) ? $param_values['single_mess_param_bg_color'] : '#FFFFFF'; ?>;		
	width:100%;		
	border:none;
}
<?php echo '.' . $theme . ' #message_div '; ?>
.message_table td { padding:10px 20px; max-width: 150px; border:none!important;}

<?php echo '.' . $theme . ' #message_div '; ?>
.message_table tr.hidden { display:none;}

<?php echo '.' . $theme . ' #message_div '; ?>
.mess_param {
	color:<?php echo isset($param_values['single_mess_param_title_color']) ? $param_values['single_mess_param_title_color'] : '#04aeda'; ?> !important;
	font-size: <?php echo ((isset($param_values['single_text_size']) ? $param_values['single_text_size'] : '15')+2); ?>px;
}

<?php echo '.' . $theme . ' #message_div '; ?>
.send_button {
	background: <?php echo isset($param_values['single_button_bg_color']) ? $param_values['single_button_bg_color'] : '#04aeda'; ?>;
	border: 1px solid <?php echo isset($param_values['single_button_border_color']) ? $param_values['single_button_border_color'] : '#167aa3'; ?>;
	border-left: 10px solid <?php echo isset($param_values['single_button_border_color']) ? $param_values['single_button_border_color'] : '#167aa3'; ?>;
	display: table;

	margin: 2% auto;
}
<?php echo '.' . $theme . ' #message_div '; ?>
.send_button:hover{
	background: <?php echo isset($param_values['single_button_hover_bg_color']) ? $param_values['single_button_hover_bg_color'] : '#797979'; ?>;
}

<?php echo '.' . $theme . ' #message_div '; ?>
.send_button input{
	color:<?php echo isset($param_values['single_button_text_color']) ? $param_values['single_button_text_color'] : '#ffffff'; ?> !important;
	background: none !important;
	outline: none !important;
	border: none !important;
}

/*-Captcha-*/
<?php echo '.' . $theme; ?>
#staff_capcha{ position:relative; line-height: 0;}
<?php echo '.' . $theme; ?>
#staff_capcha *{ display:inline-block;}
<?php echo '.' . $theme; ?>
#staff_capcha .message_capcode{
	padding: 6px 15px;
	width:50%;
}

/*--Responsive--*/

<?php echo '.' . $theme . ' .single_contact_content '; ?>
.single_contact_main.staff_phone .img_content,
<?php echo '.' . $theme . ' .single_contact_content '; ?>
.single_contact_main.staff_phone .top_info,
<?php echo '.' . $theme . ' .single_contact_content '; ?>
.single_contact_main.staff_phone .single_params { width:100% !important;}

<?php echo '.' . $theme . ' .single_contact_content '; ?>
.single_contact_main.staff_phone .top_info div{float:none !important;}

<?php echo '.' . $theme . ' .single_contact_content '; ?>
.single_contact_main.staff_phone .single_params{ display:block; border: none !important;}
	
<?php echo '.' . $theme . ' .single_contact_content '; ?>
.single_contact_main.staff_phone .single_params td{
	display:inline-block !important;
	border:none !important;
	padding:0 !important;
	width:85%;
	
}

<?php echo '.' . $theme . ' .single_contact_content '; ?>
.single_contact_main.staff_phone #message_div .message_table td {
	padding:6px 10px 6px 0 !important; 
	border:none !important;
}

@media screen and (max-width: 320px){
	<?php echo '.' . $theme . ' .single_contact_content '; ?>
	.single_contact_main.staff_phone #message_div .message_table td {
		display:table-row;
	}
}

<?php echo '.' . $theme; ?>
.staff_tablet .message_capcode,
<?php echo '.' . $theme; ?>
.staff_phone .message_capcode{width:100% !important;}


<?php echo '.' . $theme; ?>
#message_div .wd_captcha_img{
	min-width: 80px;
	max-width: 80px;
}

<?php echo '.' . $theme . ' #message_div '; ?>
.wd_captcha,
<?php echo '.' . $theme . ' #message_div '; ?> 
.cont_mess_captcha_ref{
	display:inline-block;
}
