jQuery(document).ready(function($){
	/******************************
	*	[chaging to grid layout - onchange]
	******************************/
	$(document).on('change', '#wppizza_layout_style', function(e){
		var val=$(this).val();
		if(val=='grid'){
			$('#wppizza-style-grid').css('display', 'block');
		}else{
			$('#wppizza-style-grid').css('display', 'none');
		}
	});
});