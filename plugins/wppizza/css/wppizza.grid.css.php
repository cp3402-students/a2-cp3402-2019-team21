<?php
/*
	WPPIZZA : css for grid based template

	this css gets loaded after and in conjunction with the responsive.css
	overriding declarations needed for grid layout

	if you want to change it directly , copy it to your theme's directory to not loose any chengs on updates

	alternatively, either create a wppizza-custom.css in your theme's directory and override declarations as required
	or - if your theme supports it - add your custom css wherever appropriate
*/
/*

	[set headers and calculate margins etc for grid based layouts]

*/
/*tell 'em it's css**/
header("Content-Type: text/css");
header("X-Content-Type-Options: nosniff");
/*calculate margins etc depending on variables set*/
$gridvars=explode("-",$_GET['grid']);
/* sanitise - because we can */
$colcount=empty($gridvars[0]) ? 1 : (int)$gridvars[0]; //one or more
$margin=(float)$gridvars[1];//float
$fullwidth=empty($gridvars[2]) ? 480 : (int)$gridvars[2]; //default of 480 if not set 
$colwidth=round((100-(($colcount-1)*$margin))/$colcount, 2, PHP_ROUND_HALF_DOWN);
?>
/****************************************************************************************
*
*
*
*	[menu items loop - as grid - layout]
*
*
*
****************************************************************************************/
/********************
	sections
********************/
.wppizza-grid-section{border:none;}
.wppizza-grid-section{clear: both;padding: 0px !important;margin: 0px !important;margin-bottom: 2px !important;display: -webkit-flex;display: -ms-flexbox;display: flex;}
.wppizza-grid-section:before,.wppizza-grid-section:after { content:""; display:table; }
.wppizza-grid-section:after { clear:both;}
.wppizza-grid-section { zoom:1; /* For IE 6/7 */ }

/********************
	grid columns
********************/
.wppizza-article-grid{display: block; float:left; margin: 1% 0 1% <?php echo $margin ?>% !important; padding:5px 5px !important; width: <?php echo $colwidth ?>% ; border:1px dotted #CECECE; border-radius: 5px; position:relative; /*flex:1;*/}
.wppizza-article-grid.wppizza-article-last{flex:initial;}
.wppizza-article-grid:nth-child(<?php echo $colcount ?>n+1) {margin-left: 0 !important; clear:left;}

/*******************
	category header - set by theme
*******************/

/*******************
	title
*******************/
.wppizza-article-grid h2.wppizza-article-h2{padding-bottom:10px;margin:0;text-align:center; padding-top:10px}
.wppizza-article-grid .wppizza-article-additives{font-size: 60%; margin: 0; font-weight: normal;  vertical-align: super}
.wppizza-article-grid .wppizza-article-additives:before{content:'*'}
.wppizza-article-grid .wppizza-article-additive{padding:0 3px}
.wppizza-article-grid .wppizza-article-additive:before{content:'('}
.wppizza-article-grid .wppizza-article-additive:after{content:')'}

/*******************
	[thumbnails]
*******************/
.wppizza-article-grid .wppizza-article-image{text-align:center;float:none}
.wppizza-article-grid .wppizza-article-image .wppizza-article-image-thumb,
.wppizza-article-grid .wppizza-article-image .wppizza-article-image-placeholder{padding:5px; border:1px dotted #CECECE;}
.wppizza-article-grid .wppizza-article-image .wppizza-article-image-thumb{margin:3px 10px 0 0 !important;}
.wppizza-article-grid .wppizza-article-image .wppizza-article-image-placeholder{width:75px;height:75px; margin:0 auto;line-height:1.1}
.wppizza-article-grid .wppizza-article-image .wppizza-article-image-placeholder:before{content: "\f306"; font-family: "dashicons";color:#bebebe;font-size:62px;}

/*******************
	[prices and labels]
*******************/
/*prices wrap*/
.wppizza-article-grid .wppizza-article-sizes{position:relative;margin:0 auto;text-align:center;float:none;}
/*ul prices and li main currency symbol*/
.wppizza-article-grid .wppizza-article-sizes>ul{list-style-type:none; padding:0px; margin: 0px auto; display:inline-block;}
.wppizza-article-grid .wppizza-article-sizes>ul>li{margin:0 !important;line-height:normal;display:table-cell;background:transparent!important;}
.wppizza-article-grid .wppizza-article-sizes>ul>li:before{content:'';}
.wppizza-article-grid .wppizza-article-price-currency{font-size:160%; padding:0 10px !important; text-align:center; vertical-align:middle}
/*li prices list*/
.wppizza-article-grid .wppizza-article-prices>ul{list-style-type:none !important; padding:0px; margin: 0px auto; display: inline-block;}
.wppizza-article-grid .wppizza-article-prices>ul>li{float:left;margin:0!important;line-height:normal;display:inline-block;}
.wppizza-article-grid .wppizza-article-prices>ul>li:before{content:'';}
/*prices*/
.wppizza-article-grid .wppizza-article-price{text-align:center;padding:3px;font-size:120%;background:transparent!important;}
.wppizza-article-grid .wppizza-article-price:hover{cursor:pointer;}
.wppizza-article-grid .wppizza-article-price>span{margin:0 !important;padding:0;display:inline !important;}
.wppizza-article-grid .wppizza-article-price>span:hover{text-decoration:underline}
.wppizza-article-grid .wppizza-article-size{font-size:60%;text-align:center;white-space: nowrap;}
.wppizza-article-grid .wppizza-article-size:hover{text-decoration:underline}
.wppizza-article-grid .wppizza-article-size:after{content: "\f174";font-family: "dashicons"; font-size:120%;position: relative; top: 2px; margin:0 3px;}
/* loose cart display if disabled in layout */
.wppizza-article-grid .wppizza-no-cart:after{content:"" !important}
/********************
	content
********************/
.wppizza-article-grid .wppizza-article-content{font-size: 80%;text-align: justify;margin: 0;padding: 0;}

/********************
	permlink (if used)
********************/
.wppizza-article-grid .wppizza-permalink{display:block;text-align:center}

/********************
	additives
********************/
.wppizza-additives-grid { text-align: center; font-size: 90%; margin: 50px 0 25px;}
.wppizza-additives-grid > span {margin-right: 5px; line-height: 150%;}

/********************
	pagination/navigation
********************/
.wppizza-navigation-grid{display:block; overflow:auto}

/***************************************
*
*	[media query]
*	GO FULL WIDTH BELOW xx PIXELS
*
***************************************/
@media only screen and (max-width: <?php echo"".$fullwidth."" ?>px) {
	.wppizza-grid-section{display:block;}
	.wppizza-article-grid {margin: 1% auto !important; width: 100%!important;}
}