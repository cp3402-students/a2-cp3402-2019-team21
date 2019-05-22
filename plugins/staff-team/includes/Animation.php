<style>
	/*------------------*/
	/*-- SOCIAL ICONS --*/
	@font-face {
		font-family: 'FontAwesome';
        src: url('https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/fonts/fontawesome-webfont.eot');
		/*src: url('font-awesome/fonts/fontawesome-webfont.eot?#iefix&v=4.2.0') format('embedded-opentype'), url('font-awesome/fonts/fontawesome-webfont.woff?v=4.2.0') format('woff'), url('font-awesome/fonts/fontawesome-webfont.ttf?v=4.2.0') format('truetype'), url('font-awesome/fonts/fontawesome-webfont.svg?v=4.2.0#fontawesomeregular') format('svg');*/
		font-weight: normal;
		font-style: normal;
	}
	
	.fa {
		font: normal normal normal 14px/1 FontAwesome;
		-webkit-font-smoothing:antialiased;
		-moz-osx-font-smoothing: grayscale;
		display: inline-block;
		text-rendering: auto;
		font-size: inherit;
	}
	
	.fa-facebook:before 	{ content: "\f09a"; }
	.fa-instagram:before 	{ content: "\f16d"; }
	.fa-twitter:before 		{ content: "\f099"; }
	.fa-google-plus:before 	{ content: "\f0d5"; }
	
	/*-------------------------*/
	/*---- ANIMATION SPEED ----*/
	.speed_05{
		-webkit-transition: 0.5s linear;
		   -moz-transition: 0.5s linear;
			 -o-transition: 0.5s linear;
				transition: 0.5s linear;
	}
	.speed_07,
	.bottom_part_content_move{
		-webkit-transition: 0.7s ease-out;
		   -moz-transition: 0.7s ease-out;
			 -o-transition: 0.7s ease-out;
				transition: 0.7s ease-out;
	}
	.speed_10{
		-webkit-transition: 1s linear;
		   -moz-transition: 1s linear;
			 -o-transition: 1s linear;
				transition: 1s linear;
	}
	.speed_15{ 
		-webkit-transition: 1.5s linear;
		   -moz-transition: 1.5s linear;
			 -o-transition: 1.5s linear;
				transition: 1.5s linear;
	}
	
	/*---------------------*/
	/*---- BLUR EFFECT ----*/
	.staff_blurred_on {
		-webkit-filter: blur(4px);
		   -moz-filter: blur(4px);
		    -ms-filter: blur(4px);
			 -o-filter: blur(4px);
			    filter: blur(4px);
	}
	
	/*----------------*/
	/*---- ROTATE ----*/
	.rotate_anim { transform: rotate(360deg); }
	
	/*----------------------*/
	/*---- MOVE_TO_LEFT ----*/
	.move_left_anim{ 
		left: 50% !important; 
		opacity:1 !important;
	}
	
	/*------------------------*/
	/*---- MOVE_TO_BOTTOM ----*/
	.move_bottom_anim{ 
		top: 50% !important;
		opacity:1 !important;
	}
	
	/*--------------------*/
	/*---- SLOW_HOVER ----*/
	.staff_soc_icons li:hover .staff_icon,
	.staff_soc_icons:not(:hover) .staff_icon,
	.staff_more_info_btn:hover,
	.staff_more_info_btn:not(:hover),
	.staff_soc_icons,
	.staff_soc_icons:not(:hover),
	.popup_mote_info,
	.popup_mote_info:not(:hover){
		-webkit-transition: 1s ease-out;
		   -moz-transition: 1s ease-out;
			 -o-transition: 1s ease-out;
				transition: 1s ease-out;
	}
	
	/*--- INPUT'S STYLES ----*/	
	input::-webkit-input-placeholder, textarea::-webkit-input-placeholder { line-height: 20px;  transition: line-height 0.5s ease;}	
	input::-moz-placeholder, textarea::-moz-placeholder { line-height: 20px;  transition: line-height 0.5s ease;}	
	input:-moz-placeholder, textarea:-moz-placeholder { line-height: 20px;  transition: line-height 0.5s ease;}	
	input:-ms-input-placeholder, textarea:-ms-input-placeholder { line-height: 20px;  transition: line-height 0.5s ease;}
	
	input:focus::-webkit-input-placeholder, textarea:focus::-webkit-input-placeholder { line-height: 100px; transition: line-height 0.5s ease;}	
	input:focus::-moz-placeholder, textarea:focus::-moz-placeholder { line-height: 100px; transition: line-height 0.5s ease;}	
	input:focus:-moz-placeholder, textarea:focus:-moz-placeholder { line-height: 100px; transition: line-height 0.5s ease;}	
	input:focus:-ms-input-placeholder, textarea:focus:-ms-input-placeholder { line-height: 100px; transition: line-height 0.5s ease;}
	
	
	/*---- ZOOM EFFECT--*/
	.staff_zoom {
		-webkit-animation-name: staff_zoom;
		   -moz-animation-name: staff_zoom;
				animation-name: staff_zoom;
	}
	@-webkit-keyframes staff_zoom {
			0% { 
				-webkit-transform: scale3d(.3, .3, .3); 
				opacity: 0;  
			}
			100% { opacity: 1; }
		}
	@-moz-keyframes staff_zoom {
			0% { 
				-moz-transform: scale3d(.3, .3, .3); 
				opacity: 0; 
			}
			100%{ opacity: 1;}
	}
	@keyframes staff_zoom {
		0% {
			-webkit-transform: scale3d(.3, .3, .3);
			   -moz-transform: scale3d(.3, .3, .3);
				-ms-transform: scale3d(.3, .3, .3);
				 -o-transform: scale3d(.3, .3, .3);
					transform: scale3d(.3, .3, .3);
			opacity: 0;
		}
		100%{ opacity: 1;}
	}
	
	.staff_animate{
		-webkit-animation-duration: 1s;
		   -moz-animation-duration: 1s;
				animation-duration: 1s;
		-webkit-animation-timing-function: ease;
		   -moz-animation-timing-function: ease;
				animation-timing-function: ease;
		-webkit-animation-fill-mode: both;
		   -moz-animation-fill-mode: both;
				animation-fill-mode: both;
	}
	
	
	/*---- ZOOM & ADD SHADOW ON HOVER ----*/
	<?php echo '.' . $theme . ' #Portfolio_contact '; ?>
    .staff_contact,
	<?php echo '.' . $theme . ' #portfolio_popup '; ?>
	.staff_contact .staff_image_border,
	<?php echo '.' . $theme . ' #chess_popup '; ?>
	.staff_contact .fa{
		opacity: 1;
		-webkit-transform: perspective(1000px) translate3d(0,0,0);
			    transform: perspective(1000px) translate3d(0,0,0);
					  -webkit-transition: -webkit-transform 0.35s;
									  transition: transform 0.35s;
	}
	<?php echo '.' . $theme . ' #Portfolio_contact '; ?>
    .staff_contact:hover,
	<?php echo '.' . $theme . ' #portfolio_popup '; ?>
	.staff_contact .staff_image_border:hover{
		box-shadow:5px 5px 15px #999;
		-webkit-transform: perspective(1000px) translate3d(0,0,50px);
			transform: perspective(1000px) translate3d(0,0,50px);
	}
	
	
	/*---chess showcase---*/
	<?php echo '.' . $theme . ' #chess_contact '; ?>
	.staff_effect_in {
		height:100%; width: 50%;
		position: absolute;
		text-align: center; 
		top: 0; left:0;
		cursor:pointer;
		opacity: 0;
	}
	
	<?php echo '.' . $theme . ' #chess_contact '; ?>
	.staff_effect_out .staff_effect_in::before, .staff_effect_out .staff_effect_in::after{
		position: absolute;
		content: '';
		opacity: 0;
	}

	<?php 	
	if(isset($param_values['chess_hover_bg_color'])) {
		$chess_hover_bg = $param_values['chess_hover_bg_color'];
	} else $chess_hover_bg = '#00A99D';
	$hover_bg_color='rgba('.HEXDEC(SUBSTR($chess_hover_bg, 1, 2)).','.HEXDEC(SUBSTR($chess_hover_bg, 3, 2)).','.HEXDEC(SUBSTR($chess_hover_bg, 5, 2)).',0.7'.')';
	?>
	<?php echo '.' . $theme . ' #chess_contact '; ?>	
	.staff_effect_out:hover .staff_effect_in{
		opacity: 1;
		background:<?php echo $hover_bg_color; ?> !important;
	}
	
	/* top-bottom lines */
	<?php echo '.' . $theme . ' #chess_contact '; ?>
	.staff_effect_out .staff_effect_in::before {
		top: 50px; right: 30px;
		bottom: 50px; left: 30px;
		border-top: 1px solid #fff;
		border-bottom: 1px solid #fff;
		-webkit-transform: scale(0,1);
			  transform: scale(0,1);
		-webkit-transform-origin: 0 0;
			  transform-origin: 0 0;
		-webkit-transition: 0.7s ease-out;
		   -moz-transition: 0.7s ease-out;
			 -o-transition: 0.7s ease-out;
				transition: 0.7s ease-out;
	}
	
	/* left-right lines */
	<?php echo '.' . $theme . ' #chess_contact '; ?>
	.staff_effect_out .staff_effect_in::after {
		top: 30px; left: 50px;
		right: 50px; bottom: 30px;
		border-right:1px solid #fff;
		border-left: 1px solid #fff;
		-webkit-transform: scale(1,0);
			    transform: scale(1,0);
		-webkit-transform-origin: 100% 0;
			    transform-origin: 100% 0;
		-webkit-transition: 0.7s ease-out;
		   -moz-transition: 0.7s ease-out;
			 -o-transition: 0.7s ease-out;
				transition: 0.7s ease-out;
	}

	<?php echo '.' . $theme . ' #chess_contact '; ?>
	.staff_effect_out .staff_effect_in::before, .staff_effect_out .staff_effect_in::after{
	  -webkit-transition: opacity 0.55s, 
			  transition: opacity 0.55s, 
	  -webkit-transform 0.55s;	  
			  transform 0.55s;
	}
	
	<?php echo '.' . $theme . ' #chess_contact '; ?>	
	.staff_effect_out:hover .staff_effect_in::before{
	  opacity: 1;
	  -webkit-transform: scale(1);
			  transform: scale(1);
	}
	<?php echo '.' . $theme . ' #chess_contact '; ?>	
	.staff_effect_out:hover .staff_effect_in::after {
	  opacity: 1;
	  -webkit-transform: scale(1);
			  transform: scale(1);
	}
	<?php echo '.' . $theme . ' #chess_contact '; ?>	
	.staff_effect_in span{	  
	  opacity: 0; z-index:2;
	  top:50%; left:50%;
	  position:absolute;
	  margin-top:-10px;
	  margin-left:-20px;
	  font-weight:800;
	  font-size:60px;
	  color:#fff;	  
	}
	
	<?php echo '.' . $theme . ' #chess_contact '; ?>	
	.staff_effect_out:hover span{
	  opacity: 1;
	  -webkit-transform: translate3d(0,0,0);
			  transform: translate3d(0,0,0);
	}