var wppizzaTotalsBefore = function(){};/* executed just before the ajax call that will update the cart */
var wppizzaTotals = function(){};/* executed whenever there's an update to the cart */
var wppizzaRestoreOrder = function(){};/* can be used to remove loading div and re-enable order button */
var wppizzaPrepareOrder = function(){};/* modal overlay payment windows. prepare order */
var wppizzaPrettifyJsAlerts = function(){};/* replace js alerts with modal overlays if enabled*/
jQuery(document).ready(function($){

	/***************************
		set some globals
	***************************/
		 wppizza.shopOpen = -1 ;/* initializes as undefined */

	/***************************
		not using cache, check for wppizza-open class
		else we override as carts are loaded
	***************************/
	if(typeof wppizza.usingCache==='undefined'){
		wppizza.shopOpen = ($(".wppizza-open").length > 0) ? true : false;
	}


	/****************************
		set to has cart if using
		main cart,
		hidden cart (orderpage),
		or gettotals shortcode
	****************************/
	var hasCart = ($(".wppizza-cart").length > 0 || $(".wppizza-cart-novis").length > 0 || $(".wppizza-totals").length > 0 || $("#wppizza-minicart").length > 0) ? true : false;
	var hasLoginForm = ($(".wppizza-login-form").length > 0) ? true : false;
	var isCheckout = (typeof wppizza.isCheckout!=='undefined') ? true : false;
	var gatewayChangeRecalc = (typeof wppizza.reCalc!=='undefined') ? true : false;
	var use_confirmation_form = (typeof wppizza.cfrm!=='undefined') ? true : false;
	var is_page_reload = false;/* flag that gets set to true if whole page gets reloaded to not remove any loading divs */
	var prettify_js_alerts = (typeof wppizza.pjsa!=='undefined') ? true : false;
	var force_pickup_toggle = (typeof wppizza.fpt!=='undefined') ? true : false;//bypass isopen check when pickup toggles have forcefully been made visible even if closed and we are toggeling
	var run_refresh_on_ajaxstop = false;/* ini flag to run (or not) any functions that should run on cart refresh */

	/******************************
	* all ajax start function
	* must be before spinner
	*******************************/
	wppizza.ajaxStart_count = 0;
	$( document ).ajaxStart(function() {
		wppizza.ajaxStart_count++;
	});
	/******************************
	* all ajax stop function
	*******************************/
	wppizza.spinner_count = 0;
	$( document ).ajaxStop(function() {
		if(wppizza.spinner_count == 0){
			wppizza_spinner('complete');/* re-ini spinner when all ajax is said and done */
		}
		/* if we are not reloading the page anyway, remove all loading divs */
		if(!is_page_reload){
			removeLoadingDiv();
		}
	});
	/******************************
	* all ajax complete function
	*******************************/
	$( document ).ajaxComplete(function(event, xhr, settings) {
		wppizza_validator();/*(re)initializes validator - order page only */
	});
	/******************************
	* all ajax requests that error out
	*******************************/
	$( document ).ajaxError(function(event, xhr, textStatus, errorThrown) {
		console.log("error :");
		console.log(errorThrown);
		console.log(textStatus);
		console.log(xhr.responseText);

		removeLoadingDiv();
	});

	/******************************
	* after all ajax stop
	*******************************/
	$(document).ajaxStop(function() {
		if(run_refresh_on_ajaxstop){			
			
			wppizzaCartRefreshed(wppizza.funcCartRefr, run_refresh_on_ajaxstop);/**also run any cart refreshed functions**/
			/*
				always reset back to false after run 
			*/
			run_refresh_on_ajaxstop = false;
		}
	});


	/****************************************************
	*
	* somewhat "prettify" js alerts, provided it's enabled in wppizza->layout
	* (will not do anything for "confirms" as it's simply not really doable without jumping through 1000 hoops and would only be a hack)
	*
	*****************************************************/
	/* open modal window instead of native js */
	wppizzaPrettifyJsAlerts = function(txt, alert_type){

		/*set ok / confirm buttons */
		var modal_title = wppizza.pjsa.h1;
		var button_ok = wppizza.pjsa.ok;

		d = document;

		if(d.getElementById("wppizzaJsAlert")) return;

		mObj = d.getElementsByTagName("body")[0].appendChild(d.createElement("div"));
		mObj.id = "wppizzaJsAlert";
		mObj.style.height = d.documentElement.scrollHeight + "px";

		alertObj = mObj.appendChild(d.createElement("div"));
		alertObj.id = "wppizzaAlertBox";
		if(d.all && !window.opera) alertObj.style.top = document.documentElement.scrollTop + "px";
		alertObj.style.left = (d.documentElement.scrollWidth - alertObj.offsetWidth)/2 + "px";
		alertObj.style.visiblity="visible";

		/* title */
		h1 = alertObj.appendChild(d.createElement("div"));
		h1.id = "wppizzaAlertTitle";
		h1.appendChild(d.createTextNode(modal_title));

		/* message */
		msg = alertObj.appendChild(d.createElement("p"));
		msg.innerHTML = txt;

		/* buttons */
		btn_wrap = alertObj.appendChild(d.createElement("div"));
		btn_wrap.id = "btnWrap";

		btn_ok = btn_wrap.appendChild(d.createElement("button"));
		btn_ok.id = "wppizzaAlertOk";
		btn_ok.innerHTML = button_ok;

		/* clicking ok */
		btn_ok.onclick = function() {
			removePrettifyJsAlerts(true);
		return false;
		}

	alertObj.style.display = "block";
	return;
	}

	/*
		remove modal window again
	*/
	var removePrettifyJsAlerts = function(e){
		document.getElementsByTagName("body")[0].removeChild(document.getElementById("wppizzaJsAlert"));
	}

	/****************************
		prepare order, saving
		all user data to db
		mainly for overlay gateways
	****************************/
	wppizzaPrepareOrder = function(gateway_selected){
		var data = $('#wppizza-send-order').serialize();
		jQuery.post(wppizza.ajaxurl , {action :'wppizza_json',vars:{'type':'prepareorder','gateway_selected':gateway_selected, 'data': data}}, function(r){
			/* alert any errors */
			if(typeof r.error!=='undefined'){
				var error_id = [] ;
				var error_message = [] ;
				/* set errors to implode */
				$.each(r.error,function(e,v){
					error_id[e] = v.error_id;
					error_message[e] = v.error_message;
				});

				/* set error div*/
				var error_info = 'ERROR: '+error_id.join('|')+'\n';
					error_info += ''+error_message.join('\n')+'';

				if(prettify_js_alerts){//using prettified alerts
					wppizzaPrettifyJsAlerts(error_info, 'alert');
				}else{
					alert(error_info);
				}

				return;
			}

			run_refresh_on_ajaxstop = false;
			
		},'json');
	};

	/****************************
		remove all loading divs
		and reenable order button
		mainly for overlay gateways
	****************************/
	wppizzaRestoreOrder = function(){
		removeLoadingDiv();
		$('.wppizza-ordernow').attr("disabled", false);//re enable send order button
	};


	/********************************************************************************
	*
	*	instanciating some functions we need before all other
	*
	********************************************************************************/
	/****************************
		add loading gifs
		to all instances
	****************************/
	var addLoadingDiv = function(element, loading_class){

	/* IE doesnt like setting defaults in function parameters , so we have to do the following */
	  if(element === undefined) {
	      element = false;
	   }
	  if(loading_class === undefined) {
	      loading_class = false;
	   }

		/**if on orderpage, cover whole page**/
		if(isCheckout){
			if(!element){
				$('html').css({'position':'relative'});/*stretch html to make loading cover whole page*/
				$('body').prepend('<div class="wppizza-loading"></div>');
				$('.wppizza-ordernow').attr("disabled", "true");//disable send order button
			}
		}else{
			/* add specific loading class */
			if(element && loading_class){
				element.prepend('<div class="'+loading_class+'"></div>');
			}else{
				/*will also cover any buttons so no need to disable those specifically */
				$('.wppizza-cart').prepend('<div class="wppizza-loading"></div>');
			}
		}
	};
	/****************************
		remove loading gifs
		from all instances
	****************************/
	var removeLoadingDiv = function(element, loading_class){
	/* IE doesnt like setting defaults in function parameters , so we have to do the following */
	  if(element === undefined) {
	      element = false;
	   }
	  if(loading_class === undefined) {
	      loading_class = false;
	   }


		/* only from specific element */
		if(element && loading_class){
			element.find('.'+loading_class+'').remove();
		}else{

			var loadingDivs=$('.wppizza-loading');
			if(loadingDivs.length>0){
				$.each(loadingDivs,function(e,v){
					$(this).remove();
				});
			}
		}
	};

	/******************************
	* get currently selected gateway
	* should be called on checkout only
	*******************************/
	var wppizza_get_gateway_selected=function(){
		/*
			selected gateway ident
			depending on if we are using dropdown, radio or hidden
		*/
		var gateway_as_hidden_input = $('input[type="hidden"][name="wppizza_gateway_selected"]');
		var gateway_as_radio_input = $('input[type="radio"][name="wppizza_gateway_selected"]:checked');
		var gateway_as_select = $('select[name="wppizza_gateway_selected"] option:selected');

		var gateway_selected = '';
		if(gateway_as_hidden_input.length > 0){
			gateway_selected = gateway_as_hidden_input.val().toLowerCase();
		}
		if(gateway_as_radio_input.length > 0){
			gateway_selected = gateway_as_radio_input.val().toLowerCase();
		}
		if(gateway_as_select.length > 0){
			gateway_selected = gateway_as_select.val().toLowerCase();
		}
	return gateway_selected;
	}






	/****************************
		alert or skip
		if shop is not open or yet
		unknown

		after ajax request called with  a little timeout to let html() do it's thing first before showing alert
		otherwise alerts interrupt the html replacement .
		if someone finds a way to reliably chain this (i.e html('bla') first, alert('bla') second ), let me know
	****************************/
   var checkShopOpen = function(){
		if (wppizza.shopOpen === -1){
			return false;
		}
		if (!wppizza.shopOpen && hasCart){
			if(prettify_js_alerts){//using prettified alerts
				wppizzaPrettifyJsAlerts(wppizza.msg.closed, 'alert');
			}else{
				alert(wppizza.msg.closed);
			}
			return false;
		}
		return true;
    };
	/*
		set cache buster on checkout page for people that backpage
		after changing order (or after COD) order
	*/
	if(isCheckout){
		if (!!window.performance && window.performance.navigation.type === 2) {
            addLoadingDiv();
            // value 2 means "The page was accessed by navigating into the history"
            //console.log('Reloading');
            window.location.reload(true); // reload whole page
			return;
        }
	}
/**************************************************************************************************************************************************************************
*
*
*
*	[the main funtionalities af it all really]
*	[add to cart, remove from cart, refresh cart, force refresh cart, increment cart , empty cart]
*
*
*
**************************************************************************************************************************************************************************/

	/**
		(re)-load cart
	**/
	var update_cart = function(e){
		/*show loading*/
		if(e == 'refresh'){
			addLoadingDiv();
		}
		jQuery.post(wppizza.ajaxurl , {action :'wppizza_json',vars:{'type':'loadcart', 'isCheckout': isCheckout}}, function(response) {

			load_cart_set_height(response.markup);
			wppizza.shopOpen = response.is_open;
			run_refresh_on_ajaxstop = response.cart;
		},'json');
	};


	/****************************
		if there are several carts on a page - for some reasosn -
		and / or the carts had specific heights set, we
		loop through all carts and set height for each as
		we cannot know beforehand the size it is going to be
		when carts get loaded by ajax
	****************************/
	var load_cart_set_height = function(cart_markup){
		var all_carts = $('.wppizza-cart');

		/* display cart after setting hight for each */
		$.each(all_carts,function(e,v){
			var this_cart = $(this);
			var cart_id = this_cart.attr('id');
			var cart_height_from_id = cart_id.split('-').pop(-1);
			/*
				height not set, simply show markup
				else set height on itemised table body
			*/
			if(cart_height_from_id <= 0 || cart_height_from_id==''){
				$(this).html(cart_markup);
			}else{
				$(this).html(cart_markup);
				var cart_set_itemised_tbody_height = $('#'+cart_id+' .wppizza-order-itemised > tbody').css({'height' : ''+cart_height_from_id+'px', 'min-height' : ''+cart_height_from_id+'px', 'max-height' : ''+cart_height_from_id+'px'});
			}
		});
		return;
	};



	/****************************
	*	load cart dynamically on page load
	*	if using cache
	*	alternatively trigger update by .wppizza-cart-refresh
	****************************/
	if(hasCart){
		if(typeof wppizza.usingCache!=='undefined'){
			update_cart('load');
		}
		$(document).on('click', '.wppizza-cart-refresh', function(e){
			/*show loading*/
			//addLoadingDiv();
			update_cart('refresh');
		});
	}


	/**
		run defined functions before
		each cart refresh/update ajax call
	**/
	var wppizzaCartRefreshedBefore = (function(functionArray, res) {
		if(functionArray.length>0){
			for(i=0;i<functionArray.length;i++){
				var func = new Function("term", "return " + functionArray[i] + "(term);");
				func(res);
			}
		}
	});

	/**
		run defined functions after
		each cart refresh/update
	**/
	var wppizzaCartRefreshed = (function(functionArray, res) {
		if(functionArray.length>0){
			for(i=0;i<functionArray.length;i++){
				var func = new Function("term", "return " + functionArray[i] + "(term);");
				func(res);
			}
		}
		wppizza_spinner('refresh');
		wppizza.spinner_count++;/* counter to not reinit spinner again on completed ajax requests */
	});
	/***********************************************
	*
	*	[using totals shortcode or minicart,load via js]
	*
	***********************************************/
	if ($(".wppizza-totals-container").length > 0){

		/* toggle shortcode type=totals cart visibility */
		var element_view_cart=$(".wppizza-totals-viewcart, .wppizza-totals-viewcart-button");
		var element_cart=$(".wppizza-totals-cart");

		if (element_view_cart.length > 0){

			/* show this particular cart (minicart or totals) on click of dashicon */
			$(document).on('click', '.wppizza-totals-container>.dashicons-cart, .wppizza-totals-viewcart-button', function(e){
				var self=$(this);
				self.closest('div').find('.wppizza-totals-cart').fadeToggle();
			});
			/* if clicking anywhere else, hide cart details*/
			$('html').click(function (event) {

				/* if clicked dashicon has it's cart shown skip, but DO hide carts of all other shortcodes (in case there's more than one) */
				var target_element=$(event.target);
  				if(target_element.is('.wppizza-totals-container>.dashicons-cart, .wppizza-totals-viewcart-button>input')){
  					var self_cart = target_element.closest('div').find('.wppizza-totals-cart');
  					if(self_cart.is(":visible")){
            			return;
  					}
				}
    			if(element_cart.is(":visible")){
        			element_cart.fadeOut();
    			}
			});
		}

		/*add small loading gif - but skip when loading page */
		wppizzaTotalsBefore = function(page_load){

			/* IE doesnt like setting defaults in function parameters , so we have to do the following */
	  		if(page_load === undefined) {
	      		page_load = false;
	   		}

			var element_totals_container=$(".wppizza-totals-container");
			if(page_load !== true){
				addLoadingDiv(element_totals_container, 'wppizza-loading-small');
			}
		};
		wppizzaTotalsBefore(true);

		wppizzaTotals = function(page_load){
			/* IE doesnt like setting defaults in function parameters , so we have to do the following */
	  		if(page_load === undefined) {
	      		page_load = false;
	   		}
			var element_totals_container=$(".wppizza-totals-container");
			var element_total_order=$(".wppizza-totals-order");
			var element_total_items=$(".wppizza-totals-items");
			var element_itemcount=$(".wppizza-totals-itemcount");
			var element_checkout_button=$(".wppizza-totals-checkout-button");
			var element_view_cart=$(".wppizza-totals-viewcart");
			var element_view_cart_button=$(".wppizza-totals-viewcart-button");
			var element_cart=$(".wppizza-totals-cart");
			var element_cart_items=$(".wppizza-totals-cart-items");
			var element_cart_summary=$(".wppizza-totals-cart-summary");

			jQuery.post(wppizza.ajaxurl , {action :'wppizza_json',vars:{'type':'gettotals'}}, function(res) {


				/* add /remove empty|not empty class to surrounding parent div */
				if (res.no_of_items > 0){
					element_totals_container.removeClass('wppizza-totals-no-items');
					element_totals_container.addClass('wppizza-totals-has-items');
				}else{
					element_totals_container.removeClass('wppizza-totals-has-items');
					element_totals_container.addClass('wppizza-totals-no-items');
				}

				/** add view cart dashicons if items in cart and view cart is enabled**/
				if (element_cart.length > 0 ){

					/* totals shortcode:  adding classes if not exist already */
					if(element_view_cart.length > 0){
						/* add if not exists already */
						if (!element_view_cart.hasClass('dashicons-cart')) {
							element_view_cart.addClass('dashicons dashicons-cart');
						}

						/* totals shortcode: simply hide icon if empty cart */
						if(res.cart_empty!=''){
							element_view_cart.hide();
						}else{
							element_view_cart.show();
						}
					}
				}

				/** add is open hiddden input - if open**/
				if(res.is_open){
					wppizza.shopOpen = true;
				}

				/** order total **/
				if (element_view_cart_button.length > 0){
					element_view_cart_button.html(res.view_cart_button);
				}

				/** order total **/
				if (element_total_order.length > 0){
					element_total_order.html(res.total);
				}
				/** items only total **/
				if (element_total_items.length > 0){
					element_total_items.html(res.total_price_items);
				}
				/** number of items **/
				if (element_itemcount.length > 0){
					element_itemcount.html(res.itemcount);
				}
				/** checkout button **/
				if (element_checkout_button.length > 0){
					element_checkout_button.html(res.checkout_button);
				}

				/* set cart markup */
				if (element_cart.length > 0){

					/* loop through each totals as they might have different summary/itemised settings */
					$.each(element_cart,function(e,v){

						/* selected totals element */
						var selected_totals = $(this);

						/** get cart markup **/
						var cart_markup = '';

						/* itemised */
						if(selected_totals.hasClass('wppizza-totals-cart-items')){
							cart_markup += res.items;
						}

						/* summary */
						if(selected_totals.hasClass('wppizza-totals-cart-summary')){
							cart_markup += res.summary;
						}

						/* no checkout info */
						if(typeof res.no_checkout !=='undefined'){
							cart_markup += res.minimum_order_required;
						}

						/* set cart markup for this shortcode element */
						selected_totals.html(cart_markup);
					});
				}
				/* nothing to remove when loading page */
				if(page_load !== true){
					removeLoadingDiv(element_totals_container, 'wppizza-loading-small');
				}
			
			
			run_refresh_on_ajaxstop = false;
			
			},'json');
		}
		wppizzaTotals(true);
	}


	/***********************************************
	*
	*	[minicart behaviour]
	*
	***********************************************/
	var miniCartElm=$("#wppizza-minicart");
	var mainCartElm=$(".wppizza-cart");
	if(miniCartElm.length>0){
		var wppizzaMiniCart = function(){


			/********************************
				add to id.class instead of body if set
			********************************/
			if(typeof wppizza.crt.mElm !=='undefined'){
				miniCartElm.prependTo(''+wppizza.crt.mElm+'');
			}
			var addElmPaddingTop=wppizza.crt.mCartPadTop;
    		/********************************
    			set padding (if set) to body or distinct element
    		********************************/
    		if(typeof wppizza.crt.mPadTop !=='undefined' && wppizza.crt.mPadTop>0){

    			var addElmPaddingTop=wppizza.crt.mPadTop;

				/**add padding to set elements**/
				if(typeof wppizza.crt.mPadElm !== 'undefined'){
					var elmToPad=$(''+wppizza.crt.mPadElm+'');
				}else{
    				var elmToPad=$('body');
				}
    			/* add css padding */
    			elmToPad.css({'padding-top': '+='+wppizza.crt.mPadTop+'px'});
    		}

			/*********************************
				if set to always show skip everything after
			*********************************/
			if(typeof wppizza.crt.mStatic!=='undefined'){
				return;
			}

			/*********************************
				scrolling behaviour if not static
			*********************************/
				/**current window**/
				var currentWindow = $(window);
				var miniCartIni=true;

				/**on initial load**/
		    	setTimeout(function(){
			    	wppizzaMiniCartDo(currentWindow, miniCartElm, mainCartElm, addElmPaddingTop, elmToPad);
		    		miniCartIni=false;
		    	},500);

		    	/**on scroll**/
		    	var showMiniCart;
				currentWindow.scroll(function () {
					/**only on subsequent scrolls not when page is already scrolled on load*/
					if(!miniCartIni){
						clearTimeout(showMiniCart);
						showMiniCart=setTimeout(function(){
							wppizzaMiniCartDo(currentWindow, miniCartElm, mainCartElm,addElmPaddingTop, elmToPad);
						},300);
					}
				});
		    	/**on resize**/
		    	currentWindow.resize(function() {
					/**only on subsequent scrolls not when page is already scrolled on load*/
					if(!miniCartIni){
						clearTimeout(showMiniCart);
						showMiniCart=setTimeout(function(){
							wppizzaMiniCartDo(currentWindow, miniCartElm, mainCartElm,addElmPaddingTop, elmToPad);
						},300);
					}
				});

		}
		wppizzaMiniCart();

		var wppizzaMiniCartDo = function(currentWindow, miniCartElm, mainCartElm, addElmPaddingTop, elmToPad){

				/*get width**/
		    	var docViewWidth = currentWindow.width();

		    	/*
		    		max browser width up to which we display the minicart,
		    		though that would be a bit silly to set if no maincart exists in the first place
		    	*/
		    	if(typeof wppizza.crt.mMaxWidth !=='undefined'){
		    		var docWidthLimit=wppizza.crt.mMaxWidth;
		    	}

				/**skip if wider than max width set or on oderpage**/
				if((typeof docWidthLimit !=='undefined' && docViewWidth>docWidthLimit) || typeof wppizza.isCheckout!=='undefined'){
					/*in case its still visible*/
					if(miniCartElm.is(':visible')){
						miniCartElm.fadeOut(250);
					}
					return;
				}


				/**
					only needed  if there is actually a main cart on page in the first place,
					else just set to not in view
				**/
				if(mainCartElm.length>0){
		    		var docViewTop = currentWindow.scrollTop();
		    		var docViewBottom = docViewTop + currentWindow.height();
					var elemTop = mainCartElm.offset().top;
					var elemBottom = elemTop + mainCartElm.height();
					var notInView = (elemBottom<=docViewTop || elemTop>=docViewBottom);
				}else{
					var notInView = true;
				}

				/*fade in minicart if needed**/
				if(notInView && miniCartElm.is(':hidden')){
					/*add padding if set **/
					if(typeof elmToPad !=='undefined'){
						elmToPad.animate({'padding-top': '+='+addElmPaddingTop+'px'},250);
					}
					miniCartElm.fadeIn(250);
				}

				if(!notInView && miniCartElm.is(':visible')){
					/*reset padding if required **/
					if(typeof elmToPad !=='undefined'){
						elmToPad.animate({'padding-top': '-='+addElmPaddingTop+'px'},250);
					}
					miniCartElm.fadeOut(250);
				}
			};
	}

	/****************************

		repurchase previous order
		[adding to cart]

	****************************/
	$(document).on('click', '.wppizza-reorder-purchase', function(e){
		e.preventDefault();
		e.stopPropagation();

		/*
			let's find out if we know yet if shop is open
			(as on pageload carts might get loaded by ajax)
		*/
		var is_open = checkShopOpen();
		if(!is_open){
			return;
		}

		/*
			only if open and cart on page
		*/
		if(hasCart){

			/*show loading*/
			addLoadingDiv();

			/**get the id**/
			var self=$(this);
			var selfId=self.attr('id');

			
			wppizzaCartRefreshedBefore(wppizza.funcBeforeCartRefr,e);/**run function before ajax cart update**/
			/******************************
				make the ajax request
			******************************/
			jQuery.post(wppizza.ajaxurl , {action :'wppizza_json',vars:{'type':'reorder', 'id':selfId, 'isCheckout': isCheckout}}, function(response) {
				/**error, invalid - display error code in console **/
				if(typeof response.invalid!=='undefined'){
					console.log(response.invalid);
				}
				/*replace cart contents*/
				if(typeof response.markup!=='undefined'){
					load_cart_set_height(response.markup);
				}
				run_refresh_on_ajaxstop = response.cart;
			},'json');
		return;
		}

	});
	/****************************

		adding item
		to cart

	****************************/
	$(document).on('click', '.wppizza-add-to-cart, .wppizza-add-to-cart-select', function(e){
		e.preventDefault();
		e.stopPropagation();
		/*
			let's find out if we know yet if shop is open
			(as on pageload carts might get - slowly - loaded by ajax)
		*/
		var is_open = checkShopOpen();
		if(!is_open){
			return false;
		}

		/*
			only if open and cart on page
		*/
		if(hasCart){

			/*show loading*/
			addLoadingDiv();

			/**get the id**/
			var self=$(this);
			var selfId=self.attr('id');


			/**feedback on item add enabled ? - always skip if triggered from add_to_cart_button shortcode*/
			if(typeof wppizza.itm!=='undefined' && typeof wppizza.itm.fbatc!=='undefined' && !self.hasClass('wppizza-add-to-cart-btn')){
				var add_remove_class = true;
				var currentHtml=self.html();
				var target = self;
				/* when reordering, replace inner td */
				if(self.hasClass('wppizza-do-reorder')){
					target = self.closest('td');
					currentHtml=target.html();
					add_remove_class = false;
				}
				if(add_remove_class){
					self.removeClass('wppizza-add-to-cart');/* stop excessive double clicks */
				}

				self.fadeOut(100, function(){
					target.html( "<div class='wppizza-item-added-feedback'>"+wppizza.itm.fbatc+"</div>" ).fadeIn(400).delay(wppizza.itm.fbatcms).fadeOut(400,function(){
						target.html(currentHtml).fadeIn(100);
						if(add_remove_class){
							self.addClass('wppizza-add-to-cart');/* re add class */
						}
					});
				});
			}

			/*
				if using shortcode "add to cart button" with select dropdown
			*/
			if(self.hasClass('wppizza-add-to-cart-select')){
				var selected_base = selfId.split('_').pop(-1);
				var selected_option = self.closest('span').find('select').val() ;
				/* set/override id */
				selfId = selected_base + '-' + selected_option;
			}



			wppizzaCartRefreshedBefore(wppizza.funcBeforeCartRefr,e);/**run function before ajax cart update**/
			/******************************
				make the ajax request
			******************************/
			jQuery.post(wppizza.ajaxurl , {action :'wppizza_json',vars:{'type':'add', 'id':selfId, 'isCheckout': isCheckout}}, function(response) {
				/**error, invalid - display error code in console **/
				if(typeof response.invalid!=='undefined'){
					console.log(response.invalid);
				}
				/*replace cart contents*/
				if(typeof response.markup!=='undefined'){
					load_cart_set_height(response.markup);
					wppizza.shopOpen = response.is_open;
					setTimeout(checkShopOpen, 10);/* timeout to finish html() */
				}
				/*replace order page contents (if there's an orderpage widget on the page)*/
				if(isCheckout && typeof response.page_markup!=='undefined'){
					$('.wppizza-order-wrap').replaceWith(response.page_markup);
				}
				run_refresh_on_ajaxstop = response.cart;
				//wppizzaCartRefreshed(wppizza.funcCartRefr, response.cart);/**also run any cart refreshed functions**/
			},'json');
		return;
		}
	});


	/****************************

		remove from cart = simple click on [x]
		modify cart using input number text field

	****************************/
	$(document).on('click', '.wppizza-remove-from-cart, .wppizza-delete-from-cart', function(e){//, .wppizza-cart-modify old, using spinner
		e.preventDefault();e.stopPropagation();

		/*show loading*/
		addLoadingDiv();

		/**get the id**/
		var self=$(this);
		var selected_id=self.attr('id');
		var key=selected_id.split('-').pop(-1);
		/** delete entire quantity of this item when spinner enabled and clicked on delete button */
		if(self.hasClass('wppizza-delete-from-cart')){
			var quantity = 0;
		}else{
			/* -1 to just remove one from existing*/
			var quantity = -1;
		}

		wppizzaCartRefreshedBefore(wppizza.funcBeforeCartRefr,e);/**run function before ajax cart update**/
		/******************************
			make the ajax request
		******************************/
		jQuery.post(wppizza.ajaxurl , {action :'wppizza_json', vars:{'type':'modify', 'id': selected_id, 'quantity': quantity, 'isCheckout': isCheckout}}, function(response){

			/**error, invalid - display error code in console **/
			if(typeof response.invalid!=='undefined'){
				console.log(response.invalid);
			}
			/*replace cart contents*/
			if(typeof response.cart_markup!=='undefined'){
				load_cart_set_height(response.cart_markup);
				wppizza.shopOpen = response.is_open;
				setTimeout(checkShopOpen, 10);/* timeout to finish html() */
			}
			/*replace order page contents*/
			if(isCheckout && typeof response.page_markup!=='undefined'){
				$('.wppizza-order-wrap').replaceWith(response.page_markup);
			}
						
			run_refresh_on_ajaxstop = response.cart;
		},'json');
	return;
	});

	/*
		only allow integers in cart increase/decrease - although it's set to type=number min=0
		it still allows for negatives ...
		trigger click on enter too
	*/
	$(document).on('keyup', '.wppizza-cart-mod, .wppizza-item-qupdate', function(e){
		this.value = this.value.replace(/[^0-9]/g,'');
	});
	/*
		get value on focus,
		to trigger click on blur too if value has changed
	*/
	var wppizza_current_item_count_val='';
	/**get current value for cart increase/decrease first**/
	$(document).on('focus', '.wppizza-cart-mod, .wppizza-item-qupdate', function(e){
		wppizza_current_item_count_val = this.value.replace(/[^0-9]/g,'');
	});
	/*
		execute/trigger on blur too if value is different
	*/
	$(document).on('blur', '.wppizza-cart-mod, .wppizza-item-qupdate', function(e){
		this.value = this.value.replace(/[^0-9]/g,'');
	});


	/***********************************************
	*
	*	[if we are trying to add to cart by clicking on the title
	*	but there's more than one size to choose from, display alert]
	*	[provided  there's a cart on page and we are open]
	***********************************************/
	/*more than one size->choose alert*/
	$(document).on('click', '.wppizza-trigger-choose', function(e){
		if (wppizza.shopOpen &&  hasCart){
			if(prettify_js_alerts){//using prettified alerts
				wppizzaPrettifyJsAlerts(wppizza.msg.choosesize, 'alert');
			}else{
				alert(wppizza.msg.choosesize);
			}
		}
	});
	/*only one size, trigger click*/
	$(document).on('click', '.wppizza-trigger-click', function(e){
		if (wppizza.shopOpen &&  hasCart){

			/*just loose wppizza-article- from id*/
			var ArticleId=this.id.split("-");
			ArticleId=ArticleId.splice(2);
			ArticleId = ArticleId.join("-");
			/**make target id*/
			target=$('#wppizza-'+ArticleId+'');
			/*trigger*/
			target.trigger('click');
		}
	});

	/****************************

		empty cart entirely

	****************************/
	$(document).on('click', '.wppizza-empty-cart-button', function(e){


		e.preventDefault();
		e.stopPropagation();
		/*show loading*/
		addLoadingDiv();

		wppizzaCartRefreshedBefore(wppizza.funcBeforeCartRefr,e);/**run function before ajax cart update**/
		/******************************
			make the ajax request
		******************************/
		jQuery.post(wppizza.ajaxurl , {action :'wppizza_json',vars:{'type':'empty', 'isCheckout': isCheckout}}, function(response) {

				/**error, invalid - display error code in console **/
				if(typeof response.invalid!=='undefined'){
					console.log(response.invalid);
				}

				/*replace cart contents*/
				if(typeof response.cart_markup!=='undefined'){
					load_cart_set_height(response.cart_markup);
					wppizza.shopOpen = response.is_open;
					setTimeout(checkShopOpen, 10);/* timeout to finish html() */
				}
				/*replace order page contents*/
				if(isCheckout && typeof response.page_markup!=='undefined'){
					$('.wppizza-order-wrap').replaceWith(response.page_markup);
				}
				
				run_refresh_on_ajaxstop = response.cart;

		},'json');
	});


	/***********************************************
	*	[customer selects self pickup , session gets set via ajax
	*	reload page to reflect delivery charges....
	*	only relevant if there's a shoppingcart or orderpage on page]
	***********************************************/

	var set_pickup_elements_toggle = function(disable_elemnts, pickup_toggle){

		/* get all pickup checkboxes and radios in case there are more than one */
		var all_pickup_checkboxes=$('.wppizza-order-pickup');
		var all_pickup_toggles=$('.wppizza-toggle-pickup');
		var all_delivery_toggles=$('.wppizza-toggle-delivery');
		var all_pickup_toggle_labels = all_pickup_toggles.closest('label');
		var all_delivery_toggle_labels = all_delivery_toggles.closest('label');


		if(disable_elemnts === true){
			/* disable all checkboxes/radios */
			all_pickup_checkboxes.attr("disabled", true);/*disable checkbox to give ajax time to do things*/
			all_pickup_toggles.attr("disabled", true);/*disable radios to give ajax time to do things*/
			all_delivery_toggles.attr("disabled", true);/*disable radios to give ajax time to do things*/
		}

		if(disable_elemnts === false){
			/* enable all checkboxes/radios */
			all_pickup_checkboxes.attr("disabled", false);/*disable checkbox to give ajax time to do things*/
			all_pickup_toggles.attr("disabled", false);/*disable radios to give ajax time to do things*/
			all_delivery_toggles.attr("disabled", false);/*disable radios to give ajax time to do things*/
		}

		if(pickup_toggle === true){
			/* set to true what needs to be true*/
			all_pickup_checkboxes.attr('checked',false);
			all_pickup_toggles.attr('checked',true);
			all_delivery_toggles.attr('checked',false);
			all_pickup_toggle_labels.addClass('wppizza-pickup-toggle-selected');
			all_delivery_toggle_labels.removeClass('wppizza-pickup-toggle-selected');

		}
		if(pickup_toggle === false){
			all_pickup_checkboxes.attr('checked',true);
			all_pickup_toggles.attr('checked',false);
			all_delivery_toggles.attr('checked',true);
			all_pickup_toggle_labels.removeClass('wppizza-pickup-toggle-selected');
			all_delivery_toggle_labels.addClass('wppizza-pickup-toggle-selected');
		}

	}
	//var alert_run = false;
	$(document).on('change', '.wppizza-order-pickup', function(e){

		/*
			let's find out if we know yet if shop is open
			(as on pageload carts might get loaded by ajax)
			bypass if toggle has forced visibility to show even when closed
		*/
		if(!force_pickup_toggle){
			var is_open = checkShopOpen();
			if(!is_open){
				return;
			}
		}

		if (hasCart || force_pickup_toggle){

			var self=$(this);

			/**
				as the default for checkbox  can change to be pickup,
				instead of delivery find out whats what
			**/
			var default_is_pickup = (typeof wppizza.opt !=='undefined' && typeof wppizza.opt.puDef !== 'undefined') ? true : false;

			/** is radio toggle or checkbox */
			if (self.is(':radio')) {
				var is_pickup =(self.val()==1 ) ? true : false;

			}else{
				var is_pickup = (self.is(':checked')) ? true : false;
				/* overwrite if default is set to be pickup  */
				if(default_is_pickup){
					is_pickup = (self.is(':checked')) ? false : true;
				}
			}
			/**
				changed from default to show (right) alerts
			**/
			var changed_from_default = false ;
			if(default_is_pickup !== is_pickup){
				changed_from_default = true ;
			}

			/* disable all checkboxes/radios */
			set_pickup_elements_toggle(true, null);
			/*
				default is set to pickup or delivery,  checkbox is UN-checked , and labelled for  appropriat selection
			*/
			if(is_pickup){
				set_pickup_elements_toggle(null, true);
			}else{
				set_pickup_elements_toggle(null, false);
			}

			/*js alert if enabled and switching to opposite of default - only runs the first time*/
			if(typeof wppizza.opt!=='undefined' && typeof wppizza.opt.puAlrt!=='undefined'){
				/* only run if changing non default (i.e the opposite to the unchecked default) */
				if(changed_from_default == true){
					/*
						alert only
					*/
					if(wppizza.opt.puAlrt == 1){
						if(prettify_js_alerts){//using prettified alerts
							wppizzaPrettifyJsAlerts(wppizza.msg.pickup, 'alert');
						}else{
							alert(wppizza.msg.pickup);
						}
					}

					/*
						confirm
					*/
					if(wppizza.opt.puAlrt == 2){
						if(confirm(wppizza.msg.pickup)){
							//just continue
						}else{
							/* reset checkboxes / radios*/
							if((default_is_pickup && is_pickup) || (!default_is_pickup && is_pickup)){
								set_pickup_elements_toggle(null, false);
							}else{
								set_pickup_elements_toggle(null, true);
							}
							/* make all selectable again */
							set_pickup_elements_toggle(false, null);
						return;
						}
					}
				}
			}

			/**run function before ajax cart update**/
			wppizzaCartRefreshedBefore(wppizza.funcBeforeCartRefr,e);

			/*show loading*/
			addLoadingDiv();

			jQuery.post(wppizza.ajaxurl , {action :'wppizza_json',vars:{'type':'order-pickup', 'value': is_pickup, 'data': $('#wppizza-send-order').serialize(), 'isCheckout': isCheckout}}, function(response) {

				/* simply forcing reload of page on checkout*/
				if(isCheckout){
					window.location.reload(true);
					is_page_reload = true;
				return;
				}

				/*replace cart contents*/
				if(typeof response.cart_markup!=='undefined'){
					load_cart_set_height(response.cart_markup);
					wppizza.shopOpen = response.is_open;
					if(!force_pickup_toggle){
						setTimeout(checkShopOpen, 10);/* timeout to finish html() */
					}
				}
				/*replace order page contents*/
				if(isCheckout && typeof response.page_markup!=='undefined'){
					$('.wppizza-order-wrap').replaceWith(response.page_markup);
				}
				/*replace pickup choice contents*/
				$('.wppizza-orders-pickup-choice').replaceWith(response.cart_pickup_select);


				/* make radios/checkboxes selectable again - irrelevant as html actually gets replaced */
				run_refresh_on_ajaxstop = response.cart;
				
			},'json');
		}
	});


/**************************************************************************************************************************************************************************
*
*
*
*	[spinner]
*
*
*
**************************************************************************************************************************************************************************/
		/*******************************************************
		*	[order form , initialize spinner for increase/decrease of items if enabled]
		*	as function to allow re-initializing on ajax complete
		*******************************************************/
		var wppizza_spinner_blur_count = 0;
		var wppizza_spinner = function(action){

			/**
				only on wppizza checkout
				check isCheckout and wppizza.ofqc here too as it gets re-initialized
				on ajaxComplete !
			**/
			if(typeof wppizza.ofqc!=='undefined'){

				var spinnerElements = '';
				if(isCheckout){
					spinnerElements += '.wppizza-item-qupdate, ';
				}
				spinnerElements += '.wppizza-cart-mod';


  				var spinnerElm=$( spinnerElements );
       			spinnerElm.spinner({ min: 0});/*set min var*/
				/*
					capture original spinner value on focus
				*/
				var spinner_value;
				var spinner_update_value;
				var spinner_element;


				/*
					stop submitting if we are hitting enter
					after changing quantities
					and update checkout page instead if needed
				*/
				/*
					lets make sure to not re-initialize blur/keydown 2x
					spinner gets initialized from ajax complete.
					however, if no ajax (orderpage)  ajaxStart_count will be 0
				*/
				if(action=='load' && wppizza.ajaxStart_count > 0){
					return false;
				}

				spinnerElm.focus(function(event) {
					event.preventDefault();
					wppizza_spinner_blur_count = 0;//reset count on focus
					spinner_value = $(this).val();
					spinner_element_id = $(this).attr("id").split('-').pop(-1);

					/*
						only enable mousewheel on focus
					*/
					$(this).on( 'DOMMouseScroll mousewheel', function ( event ) {
					/*restrict scrollwheel to be >=0*/
					  if( event.originalEvent.detail > 0 || event.originalEvent.wheelDelta < 0 ) { //alternative options for wheelData: wheelDeltaX & wheelDeltaY
					    // down
					    if (parseInt(this.value) > 0) {
					    	this.value = parseInt(this.value, 10) - 1;
					    }
					  } else {
					  	// up
					  	this.value = parseInt(this.value, 10) + 1;
					  }

					//prevent page from scrolling
					return false;
					});

					return false;
				});

				spinnerElm.keydown(function(event) {
					spinner_update_value = $(this).val();
					/* simply trigger blur event */
					if(event.which == 13 || event.which == 35){
						$(this).blur();
					return false;
					}
				});
				/*
					after changing quantities on blur
					update checkout page if needed
				*/
				spinnerElm.blur(function(event) {
					spinner_update_value = $(this).val();
					event.preventDefault();
					event.stopPropagation();

					if(wppizza_spinner_blur_count == 0 ){//make sure only the first blur will call the update function
						wppizza_update_order(spinner_value, spinner_update_value, spinner_element_id);
						wppizza_spinner_blur_count++;
					}
				return false;
				});
			}
		};

/**************************************************************************************************************************************************************************
*
*
*
*	[submit order - including validation]
*
*
*
**************************************************************************************************************************************************************************/

		/*******************************************
		*	[validate order form - in function
		*	to allow re-initializing on ajax complete]
		*******************************************/
		var wppizza_validator = function(){
			/**
				only on wppizza checkout
				check isCheckout here too as it gets re-initialized
				on ajaxComplete !
			**/
			if(isCheckout){
				$("#wppizza-send-order").validate({

					rules: wppizza.validate.rules,

			    	errorElement : 'div',

					errorClass:'wppizza-validation-error error',
						
					ignore: ":hidden, :disabled, .wppizza-ignore, .ignore", 

					errorPlacement: function(error, element) {
						/* append to parent div */
						var parent = element.closest('div');
			     		error.appendTo(parent);

					},

			  		invalidHandler: function(form, validator) {
						if (!validator.numberOfInvalids()){
							return;
						}

						/**check if element is in view and scrollto if not*/
						var errorElem = $(validator.errorList[0].element);
			   			var currentWindow = $(window);

			   			var docViewTop = currentWindow.scrollTop();
			   			var docViewBottom = docViewTop + currentWindow.height();

					    var elemTop = errorElem.offset().top;
			   			var elemBottom = elemTop + errorElem.height();

				        var inView= ((elemBottom <= docViewBottom) && (elemTop >= docViewTop));

				        /**scroll into view if needed*/
				        if(!inView){
				        	$('html, body').animate({
					            scrollTop: errorElem.offset().top-50
				        	}, 300);
				        }
					},
					submitHandler: function(form, event) {
						//stop double clicks, add spinner - just for good measure. loading will cover all anyway
						var submit_button = $('#wppizza-ordernow');
						submit_button.attr('disabled', 'true');//.addClass('wppizza-ordernow-spinner');
						/*show loading*/
						addLoadingDiv();

						wppizza_submit_order(use_confirmation_form, form, event);
						return false;/* dont submit form, we'll do that via wppizza_submit_order */
					}
				});
			};
		};



		/******************************
			initialize spinner on load
		******************************/
		wppizza_spinner('load');
		/******************************
		* update order page modules via ajax instead of reload
		*******************************/
		var wppizza_update_order = function(original_value, current_value, element_id, force_reload){

		/* IE doesnt like setting defaults in function parameters , so we have to do the following */
  		if(original_value === undefined) { original_value = true;}
  		if(current_value === undefined) { current_value = false;}
  		if(element_id === undefined) { element_id = false;}
  		if(force_reload === undefined) { force_reload = false;}


			/* forcing reload of page regardless making sure not to cache*/
			if(force_reload){
				window.location.reload(true);
				is_page_reload = true;
			return;
			}

			if(original_value == current_value || !element_id ){return;}
			/* cover page with loading gif before running ajax*/
			addLoadingDiv();
			var element_totals_container=$(".wppizza-totals-container");
			addLoadingDiv(element_totals_container, 'wppizza-loading-small');

			/* when changing quantities on checkout page make sure we keep any newly entered/changed customer data */
			var data = '';
			if(isCheckout){
				data = $("#wppizza-send-order").serialize();
			}


			/** run function before ajax cart update **/
			wppizzaCartRefreshedBefore(wppizza.funcBeforeCartRefr,'update');

		     /**now send ajax to update**/
			jQuery.post(wppizza.ajaxurl , {action :'wppizza_json', vars:{'type':'update-order', 'id': element_id, 'quantity': current_value, 'isCheckout': isCheckout, 'data': data}}, function(response) {

				/**error, invalid - display error code in console **/
				if(typeof response.invalid!=='undefined'){
					console.log(response.invalid);
				}
				/*replace cart contents*/
				if(typeof response.cart_markup!=='undefined'){
					load_cart_set_height(response.cart_markup);
					wppizza.shopOpen = response.is_open;
					setTimeout(checkShopOpen, 10);/* timeout to finish html() */
				}
				/*replace page contents*/
				if(typeof response.page_markup!=='undefined'){
					$('.wppizza-order-wrap').replaceWith(response.page_markup);
				}

				run_refresh_on_ajaxstop = response.cart;
				
				return;
			},'json');
		}



		/********************************************
		*
		*	orderpage only
		*
		********************************************/
		if(isCheckout){


			/******************************
				make sure page gets reloaded/changed
				to avoid possible "cannot find order by hash"
				when simply backpaging from payment gateway pages
				without having canceled or don anything further

				strangly enough, simply adding  a class to the 'body'
				seems to make this work without having to reload
				the order page
			******************************/
		    if($('body').hasClass('wppizza-checkout')){
		    }else{
		    	$('body').addClass('wppizza-checkout');
		    }
			/******************************
				initialize validator on load
			******************************/
			wppizza_validator();
			/******************************
			* validation - set error messages
			*******************************/
			jQuery.extend(jQuery.validator.messages, {
	    		required: wppizza.validate.error.required,
	    		email: wppizza.validate.error.email,
	    		decimal: wppizza.validate.error.decimal
			})
			/******************************
				validation - add method - decimals (for tips)
			******************************/
			$.validator.methods.decimal = function (value, element) {
		    	return this.optional(element) || /^(?:\d+|\d{1,3}(?:[\s\.,]\d{3})+)(?:[\.,]\d+)?$/.test(value);
			}


			/*******************************
			*	[validate tips/gratuities]
			* 	remove any utter nonsense on keyup first
			*******************************/
			$(document).on('keyup', '#ctips', function(e){
				/* ignore arrows/home/end/backspacing*/
				if(e.which==8 || (e.which>=35 && e.which<=40)){
					return;
				}

				var self = $(this);
				var value = self.val();
				validate = value.replace(/[^0-9\.,]+/g, '');
				//validate = parseFloat(validate)
				//num = validate.toFixed(2);
				self.val(validate);
			});
			/*******************************
			*	get current tip value set
			*******************************/
			var tips_input=$("#ctips");
			var current_tips=tips_input.val();
			/*******************************
			*	get current tip value set on fucus
			*******************************/
			$(document).on('focus', '#ctips', function(e){
				current_tips=$(this).val();
			});
			/*******************************
			*	stop submitting form if we are hitting enter on tip field
			*	and just apply tip by triggering "ok" button
			*******************************/
			$(document).on('keydown', '#ctips', function(e){
				if(event.which == 13 || event.which == 35){
					event.preventDefault();
					$(this).blur();
					//apply_tips(current_tips);
				return false;
				}
			});
			$(document).on('blur', '#ctips', function(e){
				apply_tips(current_tips);
				return false;
			});
		}




		/*******************************
		*	apply tip set
		*******************************/
		var apply_tips = function(current_tips){
			var self = $(this);
			var entered_tips=$("#ctips").val();

			/**only update/refresh if the value has actually changed**/
			if( current_tips != entered_tips ){
				var data = $("#wppizza-send-order").serialize();
				/*stop double clicks*/
				self.attr('disabled', 'true');
				/*show loading*/
				addLoadingDiv();
				jQuery.post(wppizza.ajaxurl , {action :'wppizza_json',vars:{'type':'addtips','data':data, 'isCheckout': isCheckout}}, function(response) {

					/*replace cart contents*/
					if(typeof response.cart_markup!=='undefined'){
						load_cart_set_height(response.cart_markup);
						wppizza.shopOpen = response.is_open;
						setTimeout(checkShopOpen, 10);/* timeout to finish html() */
					}
					/*replace order page contents*/
					if(isCheckout && typeof response.page_markup!=='undefined'){
						$('.wppizza-order-wrap').replaceWith(response.page_markup);
					}
					
					run_refresh_on_ajaxstop = response.cart;
					
				},'json');
			}
		};

/**************************************************************************************************************************************************************************
*
*
*
*	[submit order - end validation]
*
*
*
****************************************************************************************************************************************************************************


	/******************************
	* ini selected wppizza_'+gateway_selected+'_init
	* js function if it was ddefined by currently selected
	* gateway -> on load
	*******************************/
	var wppizza_gateway_init=function(){
		/*
			get selected gateway
		*/
		var gateway_selected = wppizza_get_gateway_selected();
		if(gateway_selected == '' ){return;}
		/*
			check if gateway provides it's own init function
			to perhaps mount some fields or similar
		*/
		var gateway_function_name = 'wppizza_'+gateway_selected+'_init'/* the function name to look for */
		gateway_function_name = window[gateway_function_name];
		if(typeof gateway_function_name === 'function'){
			/*
				run defined ini function
			*/
			gateway_function_name();
		}
	}
	/*
		only run above init function on load of checkout page
		if no confirmation form is being used used
	*/
	if(!use_confirmation_form){
		wppizza_gateway_init();
	}


	/******************************
	* submit_order
	*******************************/
	var wppizza_submit_order=function(must_confirm, form, event){

		/*
			selected gateway ident
		*/


		/*
			already on confirmation page, override must_confirm
		*/
		if($('#'+form.id+'').hasClass("wppizza-order-confirmed")){
			must_confirm = false;
		}

		/*
			serialized input data
		*/
		var data = $(form).serialize();

		/*
			form element
		*/
		var target_replace = $('.wppizza-order-wrap');

		/*
			get selected gateway
		*/
		var gateway_selected = wppizza_get_gateway_selected();

		/*
			check if gateway provides it's own function (mainly for overlays)
			if so, submit order will be halted and script used instead
		*/
		var gateway_has_own_function = false;
		var gateway_function_name = 'wppizza_'+gateway_selected+'_payment'/* the function name to look for */
		gateway_function_name = window[gateway_function_name];
		if(typeof gateway_function_name === 'function'){
			gateway_has_own_function = true;
		}

		/**
			just in case something went wrong and nothing was selected
		**/
		if(gateway_selected == '' ){
			console.log('no payment method selected');
			return;
		}


		/*****
			confirmation page enabled,
			show that first
		****/
		if(must_confirm){
			jQuery.post(wppizza.ajaxurl , {action :'wppizza_json',vars:{'type':'confirmorder','data':data}}, function(response) {
				/**
					replace the whole div with the confirmation page form
					and if selected gateway has an init function, run it
				**/
				target_replace.replaceWith(response.markup).promise().done(function(elem) {
					wppizza_gateway_init();
				});

				//scroll to a bit higher than top of form
				var form_top = $("#wppizza-send-order").offset().top;
				$('html, body').animate({ scrollTop: form_top - 100 }, 300);

				run_refresh_on_ajaxstop = false;

			},'json');
		return;
		}

		/*****
			confirmation page not enabled or already on confirmation page*
		****/
		if(!must_confirm){

			/*
				run gateways own function
				should result in redirect
				to thabk you page
			*/
			if(gateway_has_own_function){
				event.stopPropagation();
				is_page_reload = true;
				gateway_function_name(form.id, data, event );
				return;
			}


			jQuery.post(wppizza.ajaxurl , {action :'wppizza_json',vars:{'type':'submitorder','gateway_selected':gateway_selected, 'wppizza_data':data}}, function(response){
				/** if we have errors , display those , replacing page */
				/** if we have no errors , redirect to thank you / results */
				if(typeof response.error!=='undefined'){
				var error_id = [] ;
				var error_message = [] ;
					/* set errors to implode */
					$.each(response.error,function(e,v){
						error_id[e] = v.error_id;
						error_message[e] = v.error_message;
					});

					/* set error div*/
					var error_info = '<div id="wppizza-order-error" class="wppizza-error">ERROR: '+error_id.join('|')+'<br /><br /></div>';
						error_info += '<div id="wppizza-order-error-details" class="wppizza-error-details"><ul><li>'+error_message.join('</li><li>')+'</li></ul></div>';

					/* replace with error */
					target_replace.replaceWith(error_info);
					/* remove loading div */
					//removeLoadingDiv();
					/* scroll to top of page */
					$('html,body').animate({scrollTop:0},300);
				return;
				}
				/**
					show email output - if enabled via DEV constant
				**/
				if(typeof response.output!=='undefined'){

					console.log(response);

					/* replace with output */
					target_replace.replaceWith(response.output);
				return;
				}

				/**
					if we have no errors and selected gateway
					needs to redirect, do this here now
				**/
				if(typeof response.gateway !=='undefined'){
					/*  if posting form*/
					if(typeof response.gateway.form !=='undefined'){
						is_page_reload = true;/* do not clear loading div */
						$(response.gateway.form).appendTo('body').submit();
					return;
					}
					/* redirect by url */
					if(typeof response.gateway.redirect !=='undefined'){
						is_page_reload = true;
						window.location.href=response.gateway.redirect;
					return;
					}
				return;
				}

				/**
					if we have no errors , and no previous redirect
					redirect to thank you (COD)
				**/
				if(typeof response.redirect_url!=='undefined'){
					is_page_reload = true;
					window.location.href=response.redirect_url;
					return;
				}
				
				run_refresh_on_ajaxstop = false;				
				
			},'json');
		}

	}

	/******************************************************
	*	[changing gateways, re-calculate handling charges
	*	and reload - if necessary]
	******************************************************/
	if(gatewayChangeRecalc){
		$(document).on('change', 'input[name="wppizza_gateway_selected"]:radio, select[name="wppizza_gateway_selected"]', function(e){
			/*show loading*/
			addLoadingDiv();
			/**get the selected value**/
			var self=$(this);
			var gateway_selected=self.val();
			/* get form data too so we set session to not loose already entered info */
			var form_data = $('#wppizza-send-order').serialize();

			jQuery.post(wppizza.ajaxurl , {action :'wppizza_json',vars:{'type':'changegateway','data':form_data,'gateway_selected':gateway_selected, 'isCheckout': isCheckout}}, function(response) {

				window.location.reload(true);
				is_page_reload = true;
				
				return;

				//run_refresh_on_ajaxstop = false;
				
			},'json');
		});
	}


	/***********************************************
	*
	*	[order form: toggle info "create account" / "continue as guest"]
	*
	***********************************************/
	$(document).on('change', '.wppizza_account', function(e){
		$("#wppizza-user-register-info" ).toggle(200);
	});

	/*******************************************
	*	[user login (order form | orderhistory): show login input fields]
	*******************************************/
	$(document).on('click', '.wppizza-login-show, .wppizza-login-cancel', function(e){
		e.preventDefault(); e.stopPropagation();
		$(".wppizza-login-fieldset").slideToggle(300);
		$(".wppizza-login-option>a").toggle();
	});
	/*******************************************
	*	[user login (order form | orderhistory): validation login]
	*******************************************/
	if(hasLoginForm){
		$(".wppizza-login-form > form ").validate({
			rules: {
				log: {
					required: true
			    },
			   	pwd: {
					required: true
				},
			},
			errorElement : 'div',
			errorClass:'wppizza-validation-error error',
			errorPlacement: function(error, element) {
			/* append to parent div */
			var parent = element.closest('p');
	    		error.appendTo(parent);
			}
		});
	};
	/*******************************************
	*	[user login (order form | orderhistory): logging in or error]
	*******************************************/
	$(document).on('click', '.wppizza-login-form #wp-submit', function(e){
	    e.preventDefault(); e.stopPropagation();
	    var self = $(this);
	    var form = self.closest('form');
	    if (form.valid()) {


			var data = form.serialize();
			var setWidth=self.css('width');
			var setHeight=self.css('height');
			var info_div = $('.wppizza-login-info');
			self.attr('disabled', 'true').val('').addClass('wppizza-wait').css({'width':setWidth,'height':setHeight});
			jQuery.post(wppizza.ajaxurl , {action :'wppizza_json',vars:{'type':'user-login','data':data}}, function(response) {
				/* successful login, -> reload */
				if(typeof response.error ==='undefined'){
					/* just reload after login */
					window.location.reload(true);
					is_page_reload = true;
					return;
				}

				/* error login, -> show info for a few seconds */
				if(typeof response.error !=='undefined'){
					/* show error, fadeout, remove again */
					info_div.append(''+response.error+'').slideDown(250).delay(3500).slideUp(1000,function(){info_div.empty()});
					/* reenable button */
					self.removeAttr("disabled").val(response.button_value).removeClass('wppizza-wait');
				return;
				}
				
				run_refresh_on_ajaxstop = false;
			
			},'json');

	    }
	});

	/*******************************************
	*	[USER - orderhistory: toggle transaction/order details]
	*******************************************/
	$(document).on('click', '.wppizza-transaction-details-orderhistory > legend', function(e){
		var self = $(this);
		var order_details = self.closest('fieldset').find('.wppizza-order-details');
		var transaction_details = self.closest('fieldset').find('.wppizza-transaction-details');

		if(order_details.is(":visible")){
			order_details.hide();
			transaction_details.fadeIn();
		}else{
			transaction_details.hide();
			order_details.fadeIn();
		}
	});

	/***********************************************
	*
	*	[navigation widget as dropdown - redirect on change]
	*
	***********************************************/
	$(document).on('change', '.wppizza-dd-categories > select', function(e){
		e.preventDefault(); e.stopPropagation();
		var url = $(this).val();
		if(url <=0 ){/* placeholder */
			return;
		}
		window.location.href = url;/*make sure page gets loaded without confirm*/
		return;
	});
});