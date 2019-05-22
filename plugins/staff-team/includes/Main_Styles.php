<style>
body.staff_phone #imagelightbox-overlay, body.staff_phone #imagelightbox { display:none; }

.staff_soc_icons ul, .staff_soc_icons ul li{ padding:0 !important; margin:0 !important;}

.sc_theme button, .sc_theme input, .sc_theme textarea, .sc_theme button:focus, .sc_theme input:focus, .sc_theme textarea:focus{border:1px solid #dadada !important;}

/**, *::before, *::after{box-sizing: initial !important;}*/

/*-- LINKS --*/
a:focus{ outline: none; }

a { box-shadow:none !important; }

.staff_contact a,
.popup_info a{
	text-decoration: none !important;
	border:none !important;
}


/*-- SEARCH --*/
.staff_search{
	display: flex;
	margin:2% 0 3%;
	outline: none;
	float:left;
	width:100%;
}

.search_cont{
	background: url(../../images/search.png) no-repeat 99% center !important;
	background-size:4% !important;
	padding: 15px 0 15px 20px !important;
	width:98%; height: 20px;
	text-overflow:ellipsis;
	outline: none;
}
.search_cont:focus { background-image:none; }

.search_cont{
	padding: 20px 15px !important;
	width:100% !important;
}

/*-- PAGINATION --*/
#short_pgnt, #full_pgnt, #chess_pgnt, #portfolio_pgnt, #blog_pgnt, #circle_pgnt, #square_pgnt, #table_pgnt { text-align: center; }

.staff_pagination{
	font-family: Arial,Helvetica,sans-serif;
	border: 1px solid #D9D9D9;
	display: inline-table;
	border-radius: 4px;
	margin: 5% 0px;
}
.staff_pagination ul {
	margin: 0 !important;
	padding:0 !important;
	display:table;
	width: 100%;
}
.staff_pagination li{
	vertical-align:middle;
	display:table-cell;
	list-style:none;
}
.staff_pagination li a {
	display: block;
    padding: 15px 20px;
}
.staff_pagination.staff_phone  li a { padding:6px 10px; }

.staff_pagination .paginate:hover{
	text-decoration: none;
	color:black;
}
.staff_pagination .active_pg span,.staff_pagination li span{
	cursor: default !important;
	padding: 19px 20px 20px;
}

.staff_pagination.staff_phone  .active_pg span,
.staff_pagination.staff_phone  li span { padding: 10px 15px 11px;}

/*--  --*/
#short_contact div, #full_contact div, #chess_contact div, #Portfolio_contact div, #blog_contact div, #circle_contact div, #square_contact div,
#short_contact input, #full_contact input, #chess_contact input, #Portfolio_contact input, #blog_contact input, #circle_contact input, #square_contact input {
	box-sizing: border-box !important;
}

/*-- CONTACTS --*/
.staff_sc_container{
	position:relative;
	float: left;
	width: 100%;
	padding:0;
}

.staff_contact{
	margin:0% 2% 2% 0% !important;
	position:relative;
	float:left;
}

/*-- IMAGES --*/
.staff_image_border, .short_cont_main_picture, .full_cont_main_picture, .table_cont_main_picture, 
.showCase1_cont_main_picture, .port_cont_main_picture, .blog_cont_main_picture, .circle_cont_main_picture{	
	background-size: cover !important;
	background-repeat: no-repeat;
	background-position: center;
	width: 100%; height: 100%;
}

/*--images on content-popup--*/
.contact_content img,.single_contact_content img, .popup_content img,staff_contact img{
	width:auto;
	display: block;
	margin:0 !important;
	padding:0 !important;
	max-width:100% !important;
}


/*-- HOVER --*/
.staff_overlay{
	width:100%; height:100%;
	position: absolute;
	opacity:0;
}

/*-- TITLE --*/
.staff_top_info{
	width:100%;
	position: absolute;	
}

.staff_cont_name, .staff_category{
	margin:0 auto !important;
	display: inline-block;		
	overflow: hidden !important;
	white-space: nowrap !important;
	text-overflow: ellipsis !important;
}

/*-- CATEGORY --*/
.staff_category{
	word-break: break-all;
	word-wrap: break-word;
	width: 95%;
}

/*-- MORE INFO --*/
.staff_more_info_plus{
	width:60px; height:60px;
	margin-top: -30px;
	position:absolute;
	border-radius:50%;
	cursor:pointer;
	display:table;
	top:50%;
}

.staff_more_info_plus:before{ 
	font-family: fantasy !important;
	font-size: 60px !important;
	vertical-align:middle;
	display:table-cell;
	text-align: center;
	cursor:pointer;
    line-height: 0;
	content: "+";
}

.staff_more_info_btn {display:table;}

.staff_more_info_btn a { 
	text-decoration: none;
	vertical-align:middle;
	display:table-cell;
}

/*-- POPUP --*/
#popup_back { position:relative; }

.popup{	
	height: 100%; width: 100%;
	top:0%; left:0%;
	max-width: 100%;
	position: fixed;
	z-index:9999;
}

.close_popup_circle,.close_popup_square{
	width:40px;height:40px;
	position: absolute;
	top:1%; right:3%;
	cursor:pointer;
	display:table;
	z-index:5;
}
.close_popup_circle{border-radius:50%;}

.close_popup_circle span,.close_popup_square span{
	cursor:pointer;
	font-weight: bold;
	text-align:center;
	display:table-cell;
	vertical-align:middle;	
}

.popup_info::-webkit-scrollbar {  display: none; }

blockquote{
	font-size: 16px !important;
    margin: 2% !important;
}

.staff_more_info_btn{ cursor:pointer; }

.message_table label{ color: #000 !important; }

/*-- LIGHTBOX --*/
#imagelightbox{
	cursor: pointer;
	position: fixed;
	z-index: 10000;
	-ms-touch-action: none;
		touch-action: none;
	-webkit-box-shadow: 0 0 3.125em rgba( 0, 0, 0, .75 ); 
			box-shadow: 0 0 3.125em rgba( 0, 0, 0, .75 ); 
}
#imagelightbox-loading, #imagelightbox-loading div{
	border-radius: 50%;
}
#imagelightbox-loading{
	width: 2.5em; height: 2.5em; 
	background-color: #444;
	background-color: rgba( 0, 0, 0, .5 );
	position: fixed;
	z-index: 10003;
	top: 50%; left: 50%;
	padding: 0.625em; 
	margin: -1.25em 0 0 -1.25em; 

	-webkit-box-shadow: 0 0 2.5em rgba( 0, 0, 0, .75 );
	box-shadow: 0 0 2.5em rgba( 0, 0, 0, .75 ); 
}
#imagelightbox-loading div {
	width: 1.25em; 
	height: 1.25em; 
	background-color: #fff;
	-webkit-animation: imagelightbox-loading .5s ease infinite;
			animation: imagelightbox-loading .5s ease infinite;
}

@-webkit-keyframes imagelightbox-loading{
	from { opacity: .5;	-webkit-transform: scale( .75 ); }
	50%	 { opacity: 1;	-webkit-transform: scale( 1 ); }
	to	 { opacity: .5;	-webkit-transform: scale( .75 ); }
}
@keyframes imagelightbox-loading{
	from { opacity: .5;	transform: scale( .75 ); }
	50%	 { opacity: 1;	transform: scale( 1 ); }
	to	 { opacity: .5;	transform: scale( .75 ); }
}

#imagelightbox-overlay{
	background-color: #fff;
	background-color: rgba( 255, 255, 255, .9 );
	top: 0; bottom: 0;
	right: 0; left: 0;
	position: fixed;
	z-index: 9999;
}
#imagelightbox-loading, #imagelightbox-overlay,
#imagelightbox-close, #imagelightbox-caption,
#imagelightbox-nav, .imagelightbox-arrow {
	-webkit-animation: fade-in .25s linear;
	animation: fade-in .25s linear;
}
@-webkit-keyframes fade-in {
	from	{ opacity: 0; }
	to		{ opacity: 1; }
}
@keyframes fade-in {
	from	{ opacity: 0; }
	to		{ opacity: 1; }
}
.hidetitle .entry-title { 
	display:none !important;
}
.staff_contact td{
	vertical-align: middle !important;
}
.staff_contact p{
	word-break: break-all !important;
}
.staff_contact a{
	display: block!important;
}
.staff_cont_name, .staff_category{
	display: block !important;
}