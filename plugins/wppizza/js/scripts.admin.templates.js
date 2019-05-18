jQuery(document).ready(function($){
	/****************************************
	*
	*
	* 	templates emails/print
	*
	*
	*****************************************/
	/**********************************
	*	[template - add new]
	**********************************/
	$(document).on('click', '.wppizza_add_templates', function(e){
		e.preventDefault();
		var self=$(this);
		var arrayKey = self.attr("id").split("_").pop(-1);/*email or print etc*/
		self.attr("disabled", "true");/*disable button*/
		var countNewKeys=$(".wppizza-templates-new").length;
		jQuery.post(ajaxurl , {action :'wppizza_admin_templates_ajax',vars:{'field':'add_template', 'arrayKey': arrayKey, 'countNewKeys':countNewKeys}}, function(response) {
			$('#wppizza_list_templates_new').prepend(response.markup);
			self.removeAttr("disabled");/*re-enable button*/
			/**reinitialise sortable*/
			sortableParts();
			sortableVars();
		},'json').error(function(jqXHR, textStatus, errorThrown) {alert("error : " + errorThrown);});
	});
	/**********************************
	*	[template - remove]
	**********************************/
	$(document).on('click', '.wppizza_templates_delete', function(e){
		var self=$(this);
		var selId = self.attr("id").split("-").pop(-1);
		var arrayKey = self.attr("id").split("_")[2];/*email or print etc*/
		var elm=$('#wppizza-templates-'+selId+'');
		elm.empty().remove();
		/**if print and to be deleted is selected, select default now*/
		if(arrayKey=='print'){
			if($('#wppizza_templates_print_print_id_'+selId+':checked')){
				$('#wppizza_templates_print_print_id_0').prop('checked',true);
			}
		}
		/**if emails and to be deleted is selected, select default now*/
		if(arrayKey=='emails'){
			if($('#wppizza_templates_emails_recipients_email_customer_'+selId+':checked')){
				$('#wppizza_templates_emails_recipients_email_customer_0').prop('checked',true);
			}
			if($('#wwppizza_templates_emails_recipients_email_shop_'+selId+':checked')){
				$('#wppizza_templates_emails_recipients_email_shop_0').prop('checked',true);
			}
		}
		/**add input field to mark for deletion if not a copy of something or new**/
		if(!elm.hasClass('wppizza-templates-new')){
			$('#wppizza_list_templates_new').append('<input type="hidden" name="wppizza[template_remove]['+arrayKey+']['+selId+']" value="'+selId+'" />');
		}
	});
	/**********************************
	*	[make template parts sortable]
	**********************************/
	var sortableParts=function(){
		var wpPizzaSortableTemplateParts = $('.wppizza-templates-parts');
		if(typeof wpPizzaSortableTemplateParts!=='undefined'){
			wpPizzaSortableTemplateParts.sortable({
				handle: '.wppizza-templates-sort-part',
				axis: 'x' ,
				delay: 150,
				distance: 10
			});
		}
	};
	sortableParts();
	/**********************************
	*	[make template parts variables sortable]
	**********************************/
	var sortableVars=function(){
		var wpPizzaSortablePartsVars = $('.wppizza-templates-section-parts');
		if(typeof wpPizzaSortablePartsVars!=='undefined'){
			wpPizzaSortablePartsVars.sortable({
				handle: '.wppizza-templates-sort-var',
				axis: 'y' ,
				delay: 150,
				distance: 10,
				cancel: '.wppizza-templates-sort-var-addinfo' /*disable for add info*//*,.wppizza-template-sort-vars-pricetotal,.wppizza-template-sort-vars-quantity*/
			});
		}
	};
	sortableVars();

	/**********************************
	*	[toggle template style buttons and inputs on format change]
	*	print as well as emails
	**********************************/
	$(document).on('change', '.wppizza_templates_mail_type', function(e){
		var self=$(this);
		var mailType=self.val();
		var split=self.attr("id").split("_");
		var id = split.pop(-1);
		var tpl = split.pop(-3);

		/**reset visibility of all first, regardless of selection**/
		$("#wppizza-templates-sections-"+tpl+"-"+id+", #wppizza-templates-global-styles-"+tpl+"-"+id+", #wppizza-templates-sections-styles-"+tpl+"-"+id+" ").fadeOut(250);

		/**html, enable css buttons and inputs etc**/
		if(mailType=='phpmailer'){
			 $('#wppizza-dashicons-templates-'+tpl+'-media-code-'+id+'').removeClass('wppizza-dashicons-templates-'+tpl+'-media-code-inactive').addClass('wppizza_templates_style_toggle wppizza-dashicons-templates-'+tpl+'-media-code');
		}
		/**plaintext, disable css buttons and inputs etc**/
		if(mailType=='wp_mail'){
			 $('#wppizza-dashicons-templates-'+tpl+'-media-code-'+id+'').removeClass('wppizza-dashicons-templates-'+tpl+'-media-code wppizza_templates_style_toggle').addClass('wppizza-dashicons-templates-'+tpl+'-media-code-inactive');
		}
	});

	/**********************************
	*	[toggle template details/values visibility]
	**********************************/
	$(document).on('click', '.wppizza_templates_details_toggle', function(e){
		var self=$(this);
		var split=self.attr("id").split("_");
		var id = split.pop(-1);
		var tpl = split.pop(-2);
		var valSections=$('#wppizza-templates-sections-'+tpl+'-'+id+'');
		var styleGlobal=$('#wppizza-templates-global-styles-'+tpl+'-'+id+'');
		var styleSections=$('#wppizza-templates-sections-styles-'+tpl+'-'+id+'');
		/**hide if visible to toggle on repeated icon click**/
		if((valSections.is(":visible"))){
			valSections.hide();
		}else{
			styleGlobal.hide();
			styleSections.hide();
			valSections.fadeIn();
		}
	});
	/**********************************
	*	[toggle style/css inputs visibility]
	**********************************/
	$(document).on('click', '.wppizza_templates_style_toggle', function(e){
		var self=$(this);
		var split=self.attr("id").split("-");
		var id = split[split.length-1];
		var tpl = split[split.length-4];

		var valSections=$('#wppizza-templates-sections-'+tpl+'-'+id+'');
		var styleGlobal=$('#wppizza-templates-global-styles-'+tpl+'-'+id+'');
		var styleSections=$('#wppizza-templates-sections-styles-'+tpl+'-'+id+'');

		/**hide if visible to toggle on repeated icon click**/
		if((styleGlobal.is(":visible"))){//if((tBody.is(":visible") || globStyles.is(":visible") ) && tplStyleOrValue=='style'){
			styleGlobal.fadeOut();
			styleSections.fadeOut();
		}else{
			valSections.hide();
			styleGlobal.fadeIn();
			styleSections.fadeIn();
		}
	});
	/**********************************
	*	[template - preview]
	**********************************/
	$(document).on('click', '.wppizza_templates_preview', function(e){
		var self=$(this);
		/*get id*/
		var selected_element_id = self.attr("id").split("-").pop(-1);
		/*ini data to send to ajax*/
		var data={};
		/*template id */
		data['template_id'] = selected_element_id;
		/*what kind of template - email or print etc */
		data['template_type'] = self.attr("id").split("_")[2];
		/*html y/n ?*/
		data['mail_type'] = $('#wppizza_templates_mail_type_'+data['template_type']+'_'+selected_element_id+'').val();


		/*all available template elements in order , checked or not */
		var template_all_elements = $('#wppizza-templates-sections-'+data['template_type']+'-'+selected_element_id+' :input[type="hidden"], #wppizza-templates-sections-'+data['template_type']+'-'+selected_element_id+' :input[type="hidden"]');
		data['template_all_elements'] = $.param(template_all_elements);

		/*checked template elements */
		var template_elements_enabled = $('#wppizza-templates-sections-'+data['template_type']+'-'+selected_element_id+' :input[type="checkbox"]:checked, #wppizza-templates-sections-'+data['template_type']+'-'+selected_element_id+' :input[type="radio"]:checked');
		data['template_elements_enabled'] = $.param(template_elements_enabled);

		/*template style values*/
		var template_styles = $('#wppizza-templates-global-styles-'+data['template_type']+'-'+selected_element_id+' textarea, #wppizza-templates-sections-styles-'+data['template_type']+'-'+selected_element_id+' textarea');
		data['template_styles'] = $.param(template_styles);


		//console.log(data);

		/**send to ajax to create preview*/
		jQuery.post(ajaxurl , {action :'wppizza_admin_templates_ajax',vars:{'field':'preview_template', 'data': data}}, function(response) {
			
			//console.log(response);

			/** errors **/
			if(typeof response.error!=='undefined'){
				alert(response.error);
				return;
			}
			

			/*open window in center*/
			var previewWidth=650;
			var previewHeight=550;
			var previewLeftPosition = (screen.width) ? (screen.width-previewWidth)/2 : 0;
			var previewTopPosition = (screen.height) ? (screen.height-previewHeight)/2 : 0;
			var previewSettings ='height='+previewHeight+',width='+previewWidth+',top='+previewTopPosition+',left='+previewLeftPosition+'';

			/**for true text , write into textarea*/
			var previewContent;
			if(response['content-type']=='text/html'){
				previewContent=response.markup.html;
			}else{
				previewContent='<div style="text-align:center"><pre style="font-family: Courier, monospace; font-size:90%; text-align:left; display:inline-block">'+response.markup.plaintext+'</pre></div>';
			}

			/*do preview*/
    		var previewWindow = window.open("", "WpPizzaPreviewWindow", previewSettings);
    		if(response['content-type']=='text/html'){
    			previewWindow.document.open("text/html", "replace");
    		}else{
    			previewWindow.document.open("text/plain", "replace");
    		}
    		previewWindow.document.write(previewContent);
    		previewWindow.focus();

		},'json').error(function(jqXHR, textStatus, errorThrown) {alert("error : " + errorThrown);});
	});
	/**********************************
	*	[template - style input - expand/contract textareas on focus/blur]
	**********************************/
	var textareaHeight;
	$(document).on('focus blur', '.wppizza-templates-template textarea', function(e){
		var self=$(this);
		var focusType=e.type;
		if(focusType == 'focusin'){
			/**let's make a note of what the height was for re-setting on focusout*/
			textareaHeight=self.outerHeight();
		}
		if(focusType == 'focusin'){
			$(this).animate({height:250},200);
		}
		if(focusType == 'focusout'){
			$(this).animate({height:textareaHeight},200);
		}
	});
	/**********************************
	*	[toggle child parts depending on parent selection]
	**********************************/
	$(document).on('click', '.wppizza-templates-part', function(e){
		var self=$(this);
		var selId = self.attr("id").split("-");
		var msgId=selId[3];
		var partId=selId[4];
		var target=$('.wppizza-templates-section-parts-'+partId+'-'+msgId+' input');
		if ( self.is( ":checked" ) ){
			target.prop('checked',true);
		}else{
			target.prop('checked',false);
		}
	});
	/**********************************
	*	[toggle parent part depending on child selection]
	**********************************/
	$(document).on('click', '.wppizza-templates-input-var', function(e){
		var self=$(this);
		var selId=self.attr("id").split("-");
		var msgId=selId[4];
		var partId=selId[5];
		var target=$('#wppizza-templates-part-'+msgId+'-'+partId+'');
		var vars=self.closest('.wppizza-templates-section-parts-'+partId+'-'+msgId+'').find('input[type=checkbox]');
		var hasChecked=0;
		$.each(vars,function(e,v){
			if ( $(this).is( ":checked" ) ){
				hasChecked++;
			}
		});
		if(hasChecked==0){
			target.prop('checked',false);
		}
		else{
			target.prop('checked',true);
		}
	});
})