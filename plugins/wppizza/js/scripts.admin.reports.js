/**only loaded when post_type == wppizza**/
jQuery(document).ready(function($){
    /*******************************
	*	[reports - date picker]
	*******************************/
	$(document).on('click', '#wppizza_reports_start_date,#wppizza_reports_end_date', function(e){
    	e.preventDefault();
    	$(this).datepicker({dateFormat : 'yy-mm-dd'}).datepicker( "show" );
    });
    /*******************************
	*	[reports - toggle best/worst]
	*******************************/
	$(document).on('click', '#wppizza-report-top10-volume>h3', function(e){
		$('#wppizza-report-top10-volume-ul').toggle();
		$('#wppizza-report-bottom10-volume-ul').toggle();
    });
	$(document).on('click', '#wppizza-report-top10-value>h3', function(e){
		$('#wppizza-report-top10-value-ul').toggle();
		$('#wppizza-report-bottom10-value-ul').toggle();
    });
	/******************************
	*	[reports - default options range select - onchange]
	******************************/
	$(document).on('change', '#wppizza-reports-set-range', function(e){
		var self=$(this);
		var selVal=self.val();
		var theUrl=window.location.href.split('?')[0];
		var redirUrl=theUrl+'?post_type=wppizza&page=reports';
		if(selVal!=''){
			redirUrl+='&report=' + selVal;
		}
		window.location.href=redirUrl;
	});
	/******************************
	*	[reports - custom range]
	******************************/
	$(document).on('click', '#wppizza_reports_custom_range', function(e){
		var theUrl=window.location.href.split('?')[0];
		var redirUrl=theUrl+'?post_type=wppizza&page=reports';
		var startDate=$('#wppizza_reports_start_date').val();
		var endDate=$('#wppizza_reports_end_date').val();
		if(startDate!='' && endDate!=''){
			redirUrl+='&from=' + startDate;
			redirUrl+='&to=' + endDate;
		}
		window.location.href=redirUrl;
	});
	/******************************
	*	[reports - export]
	******************************/
	$(document).on('click', '#wppizza_reports_export', function(e){
		var theUrl=window.location.href.split('?')[0];
		var redirUrl=theUrl+'?post_type=wppizza&page=reports';
		var startDate=$('#wppizza_reports_start_date').val();
		var endDate=$('#wppizza_reports_end_date').val();
		var rangeTxt=$('#wppizza-reports-set-range :selected').text();
		var rangeValue=$('#wppizza-reports-set-range').val();
		var exportType=$('#wppizza_reports_export_type').val();
		var rangeSet=false;
		if(startDate!='' && endDate!=''){
			rangeSet=true;
			redirUrl+='&from=' + startDate;
			redirUrl+='&to=' + endDate;
		}
		if(!rangeSet){
			redirUrl+='&name=' + encodeURIComponent(rangeTxt);
			redirUrl+='&report=' + encodeURIComponent(rangeValue);
		}
		// export type selction
		redirUrl+='&type=' + exportType;
		redirUrl+='&export=true';

	window.location.href=redirUrl;
	});
});