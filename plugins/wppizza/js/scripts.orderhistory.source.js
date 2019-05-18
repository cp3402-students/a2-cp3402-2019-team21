jQuery(document).ready(function($){
	
	var adminOrdersElement = $(".wppizza-admin-orders");
	var adminOrdersAttributes = $(".wppizza-admin-orders-attributes");
	var doAdminHistory = ( adminOrdersElement.length > 0 ) ? true : false; /* if shortcode is added to a frontend page that displays admin history */
	var orderPollingInterval = (wppizza.aopt * 1000);	
	/*******************************************
	*
	*	[ADMIN - orderhistory]
	*
	*******************************************/
	if(doAdminHistory){

		/*************************************************************
			run defined functions (added by filter) after an 
			order status has been successfully changed
		*************************************************************/
		var wppizzaOrderStatusChanged = (function(functionArray, self, blog_id, order_id, status) {
			if(functionArray.length>0){
				for(i=0;i<functionArray.length;i++){
					var func = new Function("self, blog_id, order_id, status", "return " + functionArray[i] + "(self, blog_id, order_id, status);");
					func(self, blog_id, order_id, status);
				}
			}
		});

		/****************************************** 
			get order history table/results 
			including pagination
		*******************************************/
		var getOrderHistory = function(){

			/* post id to get correct pagination links via ajax */
			var post_id = adminOrdersElement.attr('id').split('-').pop(-1);	
			/* attributes set to know if we need to show pagination, login etc etc */
			var atts = adminOrdersAttributes.val();					
			/* parse attributes */
			var atts_parameters = JSON.parse( atts );

       		/* set audio alerts*/
       		if(typeof atts_parameters.audio_notify !== 'undefined'){
       			var notifyNewOrdersAudio = new Audio(atts_parameters.audio_notify);	
       		}


			/* prepend loading gif */
			adminOrdersElement.prepend('<div class="wppizza-loading"></div>');
			/* get orders via ajax */
			jQuery.post(wppizza.ajaxurl , {action :'wppizza_json',vars:{'type':'admin-order-history', 'post_id' : post_id , 'atts' : atts }}, function(response) {
				console.log('orders polling');

				/* audio notify */
				if(typeof response.notify!=='undefined'){
					notifyNewOrdersAudio.play();
					
				}
				/* replace html */
				adminOrdersElement.html(response.html);
				
			},'json').error(function(jqXHR, textStatus, errorThrown) {alert("error[print] : " + errorThrown);});;
		};


		/****************************************** 
			polling order history
		*******************************************/
		var pollOrders = setInterval(
			getOrderHistory
			//function(){}
		, orderPollingInterval);


		/****************************************** 
			changing order status of an ORDER
			also updating "updated" timestamp display
		*******************************************/
		$(document).on('change', '.wppizza-admin_orderhistory-order-status', function(e){
			/*
				clear polling while this is going on
				and reenable polling on .done()
			*/
			clearInterval(pollOrders);
	
			
			var self=$(this);
			var uoKey = self.attr('id').split('-').pop(-1);
			var status=self.val();		
			var update_failed = false;
			var blog_order_id = uoKey.split('_');


			
			jQuery.post(wppizza.ajaxurl , {action :'wppizza_json',vars:{'type': 'admin-change-status', 'uoKey':uoKey, 'status':status}}, function(response) {

				/* 
					update prohibited, alert 
				*/
				if(typeof response.update_prohibited!=='undefined'){
					alert(response.update_prohibited);
					update_failed=true;
					return;
				}

				/* 
					simply re-get the orders to reflect new classes, timestamps etc etc 
				*/
				getOrderHistory();
				
				
				/** if we have added to do somthing on order status change, we can add an alert **/
				if(typeof response.orderstatus_change_alert!=='undefined' && response.orderstatus_change_alert!='' ){
					alert(response.orderstatus_change_alert);
				}				
				
				
			},'json')
			.error(
				function(jqXHR, textStatus, errorThrown){
					alert("error[status] : " + errorThrown);
				}
			)
			.done(
				function(){
					
					/* run custom function on/after successful update */
					if(!update_failed){
						/** allow to run custom functions on get orders */
						wppizzaOrderStatusChanged(wppizza.fnOrderStatusChange, self, blog_order_id[0], blog_order_id[1], status);
					}					
					
					/*
						re-initialize polling
					*/
					 pollOrders = setInterval(getOrderHistory, orderPollingInterval);
				}
			);

		});


		/****************************************
		* 	print/view order
		*****************************************/
		$(document).on('click', '.wppizza-order-print, .wppizza-order-view', function(e){
			var self=$(this);
			var uoKey = self.attr('id').split('-').pop(-1);
			var doPrint = self.hasClass('wppizza-order-print') ? true : false;	

			jQuery.post(wppizza.ajaxurl , {action :'wppizza_json',vars:{'type':'admin-view-order','uoKey':uoKey}}, function(output) {

	            //Print Page : as Android doesnt understnd this, let's open a window
	            var wppizzaPrintViewOrder = window.open("","WppizzaOrder","width="+output['window-width']+",height="+output['window-height']+"");
	
		        if (wppizzaPrintViewOrder == null || typeof(wppizzaPrintViewOrder)=='undefined'){
		            alert("You must turn off your pop-up blocker to enable printing.\n\nPlease consult your device manufacturer about how to turn off pop-up blocking for this site.\n\n");
	            return;
				}
	
				wppizzaPrintViewOrder.document.open("text/html", "replace");/*text/plain makes no difference....so wrap in <pre> instead*/
	
				/**plaintext output, wrap in pre **/
	    		if(output['content-type']=='text/plain'){
	    			var wpPizzaOrder=output['markup']['plaintext'];
	    			wppizzaPrintViewOrder.document.write('<pre>'+wpPizzaOrder+'</pre>');
	    		}else{
	    			var wpPizzaOrder=output['markup']['html'];
	    			wppizzaPrintViewOrder.document.write(wpPizzaOrder);
	    		}
	
	            wppizzaPrintViewOrder.focus();
				/*android doesn't understand .print() not my fault really*/
				if(doPrint){
					wppizzaPrintViewOrder.print();
				}
			},'json').error(function(jqXHR, textStatus, errorThrown) {alert("error[view/print] : " + errorThrown);});
		});
	}
});