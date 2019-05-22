jQuery(document).ready(function($){
	/**********************************************
	*	[order form field type "select" - onchange]
	*	if set to "select", show select option element
	*	to enter comma sparated values
	**********************************************/
	$(document).on('change', '.wppizza_order_form_type, .wppizza_confirmation_form_type', function(e){
		var self=$(this);
		var id=self.attr('id').split("_").pop(-1);

		var val=self.val();
		self.closest('td').find('.wppizza_order_form_select input, .wppizza_confirmation_form_select').val('');//empty value
		var validation_select = $('#wppizza-order_form-validation_rules-'+id+'');
		/* if checkbox/radio/hidden (if we ever implement it), validation must be default */
		if(val=='checkbox' || val=='radio' || val=='multicheckbox' || val=='hidden'){
			/* change to default and disable */
			validation_select.val('default').change().attr("disabled", "true");
		}else{
			/* make sure we enable validation options if not checkbox|radio*/
			validation_select.removeAttr("disabled");			
		}

		if(val=='select' || val=='radio' || val=='multicheckbox'){
			self.closest('td').find('.wppizza_order_form_select, .wppizza_confirmation_form_select').css('display', 'block');
		}else{
			self.closest('td').find('.wppizza_order_form_select, .wppizza_confirmation_form_select').css('display', 'none');

		}
	});

	/***************************************
	*	[validation rules, 
	*	show parameters input element
	*	for rules that require them to be set 
	* 	and are not just bool
	***************************************/
	$(document).on('change', '.wppizza-order_form-validation_rules', function(e){
		var self=$(this);
		var formfield_id=self.attr('id').split("-").pop(-1);
		/* get and empty target first span that holds parameters elements*/
		var target = $("#wppizza_validation_parameters-"+formfield_id+"");
		target.empty();
		/**get selected dropdown values (works with multiple too))*/
		var val=self.val();
		/* if not a multiselect box, count will be 1*/
		var count = 1;
		var multiselect = false;
		if(typeof val!=='string'){
			/*get count of selected options in lultiselect*/
			var count=(val==null) ?  0 : val.length;
			multiselect = true;
		}		
		/* 
			iterate through selected, and if split by "-" produces 2 - add parameter box 
			(as -hasparameters will have ben added to option value )
		*/		
		for(var i=0;i<count;i++){
			
			/**
				in multiselct, we'll have an array of values, otherwise just use selected val
			**/
			if(multiselect){
				var selected_value = val[i];
			}else{
				var selected_value = val;
			}
			/*
				split by "-" and count to see if we need to show 
				parameter input element for this
			*/
			var split = selected_value.split("-");
			if(split.length>1){
				var rule = split[0];
				/* create element */				
				var parameter_input_elm='<span>set \''+rule+'\':</span><br /><input name="wppizza[order_form]['+formfield_id+'][validation][parameters]['+rule+']" type="text" value="">';
				/* append element*/
				target.append(parameter_input_elm);
			}
		}
	});

});