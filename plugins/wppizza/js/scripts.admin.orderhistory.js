var wppizzaGetOrders = function(){};/* executed whenever the order history page gets updated */
var wppizza_get_unique_order_key;/* globalised convenience/helper function to get blog_id and order_id from a selected element id if (str[_str_str ... ]-blogid_orderid)*/

jQuery(document).ready(function($){

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
	/*****************************
	*	[poll orders]
	*****************************/
	var pollObj=$('#wppizza_orderhistory_orders_poll_enabled');
	if(pollObj.length>0){
		var pollingInterval=$('#wppizza_orderhistory_orders_poll_interval').val();
		var pollOrdersInterval=setInterval(function(){pollOrders();},(pollingInterval*1000));
	}
	/*****************************
	*	[change poll interval]
	*****************************/
	$(document).on('change', '#wppizza_orderhistory_orders_poll_interval', function(e){
		var pollingInterval=$(this).val();
		clearInterval(pollOrdersInterval);
		pollOrdersInterval=setInterval(function(){pollOrders();},(pollingInterval*1000));
	});
	/*****************************
	*	[do poll if enabled]
	*****************************/
	var pollOrders=function(){
	if($('#wppizza_orderhistory_orders_poll_enabled').is(':checked')){
		$('#wppizza_orderhistory_polling_loading').addClass('wppizza-load');
		wppizza_orderhistory_get_orders();
	}};
	/******************************
	*	[get orders]
	******************************/
	var pollError=0;
	var wppizza_orderhistory_get_orders = function(){
		//e.preventDefault();
		var limit=$('#wppizza_orderhistory_orders_limit').val();
		var status=$('#wppizza_orderhistory_orders_status').val();
		/* custom statuses if set (in wppizza - > localization)*/
		var custom_status_val=$('#wppizza-orderhistory-custom-option-select').val();
		var custom = '';
		if(typeof custom_status_val !=='undefined'){
			custom=custom_status_val;
		}
		/** all form fields used when polling, might be useful for other plugins to hook into on change or so*/
		var form_data = $("#wppizza_orderhistory_polling").find("[name]").serialize();
		var getparameters = window.location.search.substr(1);/*get url parameters with leading '?' */
		jQuery.post(ajaxurl , {action :'wppizza_admin_orderhistory_ajax',vars:{'field':'get_orders','limit':limit,'status':status,'custom':custom,'getparameters':getparameters,'form_data':form_data}}, function(response) {
			/** allow to run custom functions on get orders */
			wppizzaGetOrders(wppizza.fnGetOrders,response);/**also run any cart refreshed functions**/

			$('#wppizza_orderhistory_results').html(response.orders);
			$('#wppizza_orderhistory_polling_loading').removeClass();
			pollError=0;
		},'json').error(function(jqXHR, textStatus, errorThrown) {
			pollError++;
			if(pollError>=5){
				alert("polling error ["+pollError+"x]: " + errorThrown);
			}else{
				console.log("polling error [count "+pollError+"x] : " + errorThrown);
			}
		});
	};

	/**run defined functions after order history update**/
	var wppizzaGetOrders = (function(functionArray, res) {
		if(functionArray.length>0){
			for(i=0;i<functionArray.length;i++){
				var func = new Function("term", "return " + functionArray[i] + "(term);");
				func(res);
			}
		}
	});


	/******************************
	*	[get orders again on change of status in DROPDOWN at top of page]
	******************************/
	$(document).on('change', '#wppizza_orderhistory_orders_status, #wppizza-orderhistory-custom-option-select', function(e){
		$('#wppizza_orderhistory_polling_loading').addClass('wppizza-load');
		wppizza_orderhistory_get_orders();
	});
	/*****************************
	*	[change results per page do it immediately on enter else on blur]
	*****************************/
	$(document).on('blur keyup', '#wppizza_orderhistory_orders_limit', function(e){
		e.preventDefault();
		e.stopPropagation();

		if(e.type == 'keyup'){
			if(e.keyCode == 13 || e.keyCode == 35){
				wppizza_orderhistory_get_orders();
				return;
			}
		return;
		}
		wppizza_orderhistory_get_orders();
	});
	/*****************************
	*	[change order status of an ORDER]
	*****************************/
	/* stop details popup */
	$(document).on('click', '.wppizza-orderhistory-order-status, .wppizza-orderhistory-custom-option', function(e){
		e.preventDefault();
		e.stopPropagation();
	});
	/* stop details popup when refunding */
	$(document).on('click', '.wppizza-orderhistory-enable-refund, .wppizza-orderhistory-enable-refund-label, .wppizza-spinner', function(e){
		e.stopPropagation();
	});

	/*****************************
		do refund
	*****************************/
	$(document).on('click', '.wppizza-orderhistory-process-refund', function(e){
		e.preventDefault();
		e.stopPropagation();
		/* clear polling while this is going on */
		clearInterval(pollOrdersInterval);
		/* is checkbox checked ?*/
		var self=$(this);
		var didRefund=false;
		var parentElement = self.closest('div');
		var refundEnabled = parentElement.find('.wppizza-orderhistory-enable-refund');
		var keys=wppizza_get_unique_order_key(self);
		//var mainStatusElm = $('#wppizza-orderhistory-order-status-details-'+keys.key+'');

		/*
			gateway has refunds enabled
		*/
		if(refundEnabled.length >0){
			if(refundEnabled.is(':checked')){
					/**set to loading**/
					self.attr("disabled", "true");/*disable button*/
					parentElement.prepend('<div class="wppizza-spinner"></div>');

					jQuery.post(ajaxurl , {action :'wppizza_admin_orderhistory_ajax',vars:{'field':'refund_at_gateway','id':keys.order_id,'blogid':keys.blog_id,'class':refundEnabled.val()}}, function(response) {


						if(typeof response.update_prohibited!=='undefined'){
							alert(response.update_prohibited);
							return;
						}

							if(typeof response.error !== 'undefined'){
								alert(response.error +' '+response.error_message);
							}
							else if(typeof response.success !== 'undefined'){
								didRefund = true;
							}else{
								alert('unknown error');
							}

						if(didRefund){
							/* show notes */
							$('#wppizza-orderhistory-notes-'+keys.key+'').html(response.notes);
							$('#wppizza-orderhistory-order-notes-'+keys.key+'').show();
							/* update timestamp */
							$('#wppizza-orderhistory-order-update-'+keys.key+'-time').html(response.update_timestamp);
							parentElement.fadeOut();
							alert(response.success_message);
						}

					},'json')
					.error(
						function(jqXHR, textStatus, errorThrown){
							alert("error[refund] : " + errorThrown);
						}
					)
					.done(
						function(){
							/* repoll now we are done, by force triggering change event*/
							$('#wppizza_orderhistory_orders_poll_interval').trigger('change');
						}
					);
			}
			/*
				set to REFUNDED WITHOUT also updating at gateway (i.e checkbox not checked)
			*/
			else{
				/* find closest order status dropdown */
				var order_summary_td_status = self.closest('td').find(".wppizza-orderhistory-order-status");
				order_summary_td_status.trigger('change');
			}
		}

	});

	/*****************************
	*	[change order status of an ORDER]
	* 	also updating "updated" timestamp display
	*****************************/
	/* stop polling if we are about to change order status */
	$(document).on('click', '.wppizza-orderhistory-order-status', function(e){
		clearInterval(pollOrdersInterval);
	});
	/* re-enable polling on blur */
	$(document).on('blur', '.wppizza-orderhistory-order-status', function(e){
		/*  re-enable polling on blur*/
		$('#wppizza_orderhistory_orders_poll_interval').trigger('change');
	});
	/* change order status */
	$(document).on('change', '.wppizza-orderhistory-order-status', function(e){

		e.preventDefault();
		e.stopPropagation();
		/*
			clear polling while this is going on
			and force reenable polling by triggering change event
			on .done()
		*/
		clearInterval(pollOrdersInterval);

		var self=$(this);
		var keys=wppizza_get_unique_order_key(self);
		var status=self.val();
		var selClass=status.toLowerCase();
		var mainStatusElm = $('#wppizza-orderhistory-order-status-details-'+keys.key+'');
		var tbStatusElm = $('#wppizza-orderhistory-order-status-thickbox-'+keys.key+'');
		var refundElm = $('#wppizza-orderhistory-refund-'+keys.key+'');
		var refundElmCheckbox = $('#wppizza-orderhistory-process-refund-'+keys.key+'');
		var update_failed = false;// only run complete function on success



		/*if called from details - update popup too*/
		tbStatusElm.val(''+status+'');
		/*if called from popup - update details too*/
		mainStatusElm.val(''+status+'');

		/* check if it has gateway refund option */
		if(refundElm.length>0){
			if(status == 'REFUNDED'){
				/*
					refund checkbox exists , is visible and not checked, procees as normal
					updateing to REFUNDED
				*/
				if(refundElmCheckbox.length >0 && refundElmCheckbox.is(':visible') && refundElmCheckbox.not(':checked')){
					// simply carry on
				}else{
					refundElm.fadeIn();
					return;
				}
			}else{
				refundElm.hide();
			}
		}
		jQuery.post(ajaxurl , {action :'wppizza_admin_orderhistory_ajax',vars:{'field':'orderstatuschange','id':keys.order_id,'blogid':keys.blog_id,'status':status}}, function(response) {

			if(typeof response.update_prohibited!=='undefined'){
				alert(response.update_prohibited);
				update_failed=true;
				return;
			}
			mainStatusElm.closest('tr').removeClass().addClass('wppizza-orderhistory-orderstatus wppizza-orderhistory-orderstatus-'+selClass+'');
			// update timestamp
			$('#wppizza-orderhistory-order-update-'+keys.key+'-time').html(response.update_timestamp);
			// hide any perhaps existing refund checkboxes
			refundElm.hide();


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
				/* repoll now we are done, by force triggering change event*/
				$('#wppizza_orderhistory_orders_poll_interval').trigger('change');

				/* run custom function on/after successful update */
				if(!update_failed){
					/** allow to run custom functions on get orders */
					wppizzaOrderStatusChanged(wppizza.fnStatusChanged, self, keys.blog_id, keys.order_id, status);
				}
			}
		);
	});

	/*****************************
	*	[change custom status of an ORDER]
	*****************************/
	$(document).on('change', '.wppizza-orderhistory-custom-option', function(e){
		e.preventDefault();
		e.stopPropagation();
		var self=$(this);
		var keys=wppizza_get_unique_order_key(self);
		var status=self.val();
		jQuery.post(ajaxurl , {action :'wppizza_admin_orderhistory_ajax',vars:{'field':'customoptionchange','id':keys.order_id,'blogid':keys.blog_id,'status':status}}, function(response) {

			if(typeof response.update_prohibited!=='undefined'){
				alert(response.update_prohibited);
				return;
			}


			$('#wppizza-orderhistory-order-update-'+keys.key+'-time').html(response.update_timestamp);
		},'json').error(function(jqXHR, textStatus, errorThrown) {alert("error[custom] : " + errorThrown);});
	});

	/******************************
	*	[delete order]
	******************************/
	$(document).on('click', '.wppizza-orderhistory-delete-order', function(e){
		e.preventDefault();
		e.stopPropagation();
		if(!confirm('are you sure ?')){ return false;}
		var self=$(this);
		var keys=wppizza_get_unique_order_key(self);
		jQuery.post(ajaxurl , {action :'wppizza_admin_orderhistory_ajax',vars:{'field':'delete_order','order_id':keys.order_id,'blog_id':keys.blog_id}}, function(response) {

			if(typeof response.update_prohibited!=='undefined'){
				alert(response.update_prohibited);
				return;
			}

			alert(response.feedback);
			$('#wppizza-orderhistory-order-'+keys.key+'').empty().remove();
			$('#wppizza-orderhistory-order-notes-'+keys.key+'').empty().remove();
		},'json').error(function(jqXHR, textStatus, errorThrown) {alert("error[delete] : " + errorThrown);});
	});

	/******************************
	*	[toggle bulk delete checkboxes]
	******************************/
	$(document).on('click', '.wppizza_orderhistory_bulk-delete-toggle', function(e){
		var self=$(this);
		if(self.is(':checked')){
			$('.wppizza_orderhistory_delete-selected').prop("checked", true);
		}else{
			$('.wppizza_orderhistory_delete-selected').prop("checked", false);
		}
		/* always uncheck non-visible (delivered/and refunded statusses only display summaries with the checkbox invisible) */
		$('.wppizza_orderhistory_delete-selected').each(function(e){
			if(!$(this).is(':visible')){
				$(this).prop("checked", false);
			}
		});
	});
	/******************************
	*	[delete order bulk]
	*	[only delete checked AND where checkbox is still visible (delivered/and refunded statusses only display summaries with the checkbox invisible) ]
	******************************/
	$(document).on('click', '.wppizza_orderhistory_bulk-delete-do', function(e){
		e.preventDefault();
		e.stopPropagation();
		if(!confirm('Are you really sure ?\n\nSelected orders will be irretrievably deleted !!!')){ return false;}

		var delete_order_ids = [];
		var i=0;
		$('.wppizza_orderhistory_delete-selected').each(function(e){
			if($(this).is(':checked') && $(this).is(':visible')){
		  		delete_order_ids[i] = $(this).val();
		  		i++;
			}
		});

		/* anything to delete ? */
		if(delete_order_ids.length >0 ){
			jQuery.post(ajaxurl , {action :'wppizza_admin_orderhistory_ajax',vars:{'field':'delete_order_bulk','delete_order_ids':delete_order_ids}}, function(response) {

				if(typeof response.update_prohibited!=='undefined'){
					alert(response.update_prohibited);
					return;
				}
				/*simply reload */
				window.location.reload(true)
				return;
			},'json').error(function(jqXHR, textStatus, errorThrown) {alert("error[delete bulk] : " + errorThrown);});
		}
	});
	/*****************************
	*	[update/view order notes]
	*****************************/
	$(document).on('click', '.wppizza-orderhistory-order-view-add-notes', function(e){
		e.preventDefault();
		e.stopPropagation();
		var self=$(this);
		var keys=wppizza_get_unique_order_key(self);
		var target=$('#wppizza-orderhistory-order-notes-'+keys.key+'');
		if(target.is(':visible')){
			target.fadeOut("fast");
		}else{
			target.fadeIn("slow");
		}
	});
	$(document).on('click', '.wppizza-orderhistory-do-notes', function(e){
		e.preventDefault();
		e.stopPropagation();
		var self=$(this);
		var keys=wppizza_get_unique_order_key(self);
		var entered_notes=$('#wppizza-orderhistory-notes-'+keys.key+'').val();

		jQuery.post(ajaxurl , {action :'wppizza_admin_orderhistory_ajax',vars:{'field':'ordernoteschange', 'order_id':keys.order_id, 'blog_id':keys.blog_id, 'entered_notes':entered_notes}}, function(response) {

			if(typeof response.update_prohibited!=='undefined'){
				alert(response.update_prohibited);
				return;
			}

			var toggle_button_class='wppizza-orderhistory-order-has-notes';
			var do_class='';
			if(response.notes_length>0){
				do_class=toggle_button_class;
			}

			self.closest('tr').fadeOut(250,function(){
				$('#wppizza-orderhistory-order-view-add-notes-'+keys.key+'').html(response.notes_button_label).removeClass(toggle_button_class).addClass(do_class);
				$('#wppizza-orderhistory-order-update-'+keys.key+'-time').html(response.update_timestamp);
				alert(response.notes_updated_alert);
			});
		},'json').error(function(jqXHR, textStatus, errorThrown) {alert("error[notes] : " + errorThrown);});
	});
	/*****************************
	*	[toggle summary -> fulldetails - only first 3 columns]
	*****************************/
	$(document).on('click', '.wppizza-orderhistory-ordersummary', function(e){
		e.preventDefault();
		e.stopPropagation();
		var self=$(this);
		var keys=wppizza_get_unique_order_key(self);
		/**hide summary, show details on click of summary tr**/
		$('#wppizza-orderhistory-ordersummary-'+keys.key+'').hide();
		$('#wppizza-orderhistory-order-'+keys.key+'').fadeIn();
	});
	/*****************************
	*	[toggle fulldetails -> summary]
	*****************************/
	$(document).on('click', '.wppizza-orderhistory-orderdetails-inactive', function(e){
		e.preventDefault();
		e.stopPropagation();
		var self=$(this);
		var keys=wppizza_get_unique_order_key(self);
		/**hide details, show summary on click of orderdetails-inactive tr**/
		$('#wppizza-orderhistory-order-'+keys.key+'').hide();
		$('#wppizza-orderhistory-order-notes-'+keys.key+'').hide();
		$('#wppizza-orderhistory-ordersummary-'+keys.key+'').fadeIn();
	});
	/*****************************
	*	[wp_user_details, stop toggle]
	*****************************/
	$(document).on('click', '.wppizza-orderhistory-column-actions-summary>span>a, .wppizza-orderhistory-column-actions>span>a, .wppizza-orderhistory-column-actions-summary>span>input,.wppizza-orderhistory-column-actions>span>input', function(e){
		e.stopPropagation();
	});
	/****************************************
	* 	print order history
	*	using template
	*****************************************/
	$(document).on('click', '.wppizza-orderhistory-print-order', function(e){
		e.preventDefault();
		e.stopPropagation();
		var self=$(this);
		var keys=wppizza_get_unique_order_key(self);

		jQuery.post(ajaxurl , {action :'wppizza_admin_orderhistory_ajax',vars:{'field':'print-order','id':keys.order_id,'blog_id':keys.blog_id}}, function(output) {
            //Print Page : as Android doesnt understnd this, let's open a window
            var wppizzaPrintOrder = window.open("","WppizzaOrder","width="+output['window-width']+",height="+output['window-height']+"");

	        if (wppizzaPrintOrder == null || typeof(wppizzaPrintOrder)=='undefined'){
	            alert("You must turn off your pop-up blocker to enable printing.\n\nPlease consult your device manufacturer about how to turn off pop-up blocking for this site.\n\n");
            return;
			}

			wppizzaPrintOrder.document.open("text/html", "replace");/*text/plain makes no difference....so wrap in <pre> instead*/

			/**plaintext output, wrap in pre **/
    		if(output['content-type']=='text/plain'){
    			var wpPizzaOrder=output['markup']['plaintext'];
    			wppizzaPrintOrder.document.write('<pre>'+wpPizzaOrder+'</pre>');
    		}else{
    			var wpPizzaOrder=output['markup']['html'];
    			wppizzaPrintOrder.document.write(wpPizzaOrder);
    		}

            wppizzaPrintOrder.focus();
			/*android doesn't understand .print() not my fault really*/
			wppizzaPrintOrder.print();
		},'json').error(function(jqXHR, textStatus, errorThrown) {alert("error[print] : " + errorThrown);});
	});



	/*****************************
	*	[show order details in thickbox]
	*****************************/
	$(document).on('click', '.wppizza-orderhistory-do-thickbox', function(e){
		e.preventDefault();
		e.stopPropagation();
		var content_replaced=false;
		var self=$(this);
		var keys=wppizza_get_unique_order_key(self);
		var tbTitle=$('#wppizza-orderhistory-order-txid-'+keys.key+'').text()+' | '+$('#wppizza-orderhistory-order-date-'+keys.key+'').text();


		/*mobile devices might have smaller screens, so strip all double whitespaces and set width*/
		var tbWidth=540;
		var bodyWidth=$( "body" ).width();
		if(bodyWidth<tbWidth){
			/*set with*/
			tbWidth=bodyWidth-20;
			/*get and replace double spaces in content to fit in screen*/
			var contentElm=$('#wppizza-orderhistory-thickbox-'+keys.key+' > div');
			var theContent=contentElm.html();
			var replaceContent=theContent.replace(/  +/g, ' ');/*replace all double whitespaces with single ones*/
			var setContent=contentElm.html(replaceContent);
			var content_replaced=true;
		}

		/*content element id*/
		var tbContent='wppizza-orderhistory-thickbox-'+keys.key+'';

        /*open thickbox*/
        tb_show(""+tbTitle+"", "#TB_inline?width="+tbWidth+"&height=540&inlineId="+tbContent+"");

		/*automatically set to acknowledged if new*/
		var currStatus=$('#wppizza-orderhistory-order-status-thickbox-'+keys.key+'').val();
		var setStatus='ACKNOWLEDGED';

		if(currStatus!=setStatus && currStatus=='NEW'){
			$('#wppizza-orderhistory-order-status-thickbox-'+keys.key+'').val(''+setStatus+'');/*set popup*/
			$('#wppizza-orderhistory-order-status-details-'+keys.key+'').val(''+setStatus+'');/*set main*/
			$('#wppizza-orderhistory-order-status-details-'+keys.key+'').trigger('change');/*trigger change in main screen*/
		}

		/**restore replaced content again with original on unload **/
		if(content_replaced){
			jQuery('#TB_window').on("tb_unload", function(){
				contentElm.html(theContent);
				console.log(theContent);
			});
		}

        return false;
	});
	/*****************************
	*
	*	[helper to get blogid, orderid and unique key associated with order]
	*
	*****************************/
	wppizza_get_unique_order_key= function(elm){

		var key=elm.attr('id').split("-").pop(-1);
		/**key might include blog_id**/
		var uoKey=key.split("_");
		if(uoKey.length==1){
			var blog_id='';
			var order_id=uoKey[0];
		}
		if(uoKey.length==2){
			var blog_id=uoKey[0];
			var order_id=uoKey[1];
		}

		var keys={};
		keys.key=key;
		keys.blog_id=blog_id;
		keys.order_id=order_id;



		return keys;
	}

})