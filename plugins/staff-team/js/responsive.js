jQuery('document').ready(function(){	
	if(jQuery("body").hasClass("staff_phone")){
		staff_phone();		
	}
	else if(jQuery("body").hasClass("staff_tablet")){
		staff_tablet();
	}
	else{
		checkMedia();}
	
	jQuery(window).resize(function(){checkMedia();});
	
	function checkMedia(){
		/*--------- SCREEN --*/
		if(jQuery(window).width()>1024){
			staff_screen();
		}
		/*--------- TABLET --*/
		if(jQuery(window).width()<=1024 && jQuery('body').width()>=768){
			staff_tablet();
		}
		/*--------- PHONE --*/
		if(jQuery(window).width()<768){
			staff_phone(false);
		}
	}
	
	/*------- SCREEN --*/
	function staff_screen(){
		jQuery("body").removeClass("staff_phone");
		jQuery("body").removeClass("staff_tablet");
		jQuery("body").addClass("staff_web");
		
		jQuery(".staff_sc_container").removeClass("staff_phone");
		jQuery(".staff_sc_container").removeClass("staff_tablet");
		jQuery(".staff_sc_container").addClass("staff_web");
		
		jQuery(".popup").removeClass("staff_phone");
		jQuery(".popup").removeClass("staff_tablet");
		jQuery(".popup").addClass("staff_web");
		
		jQuery(".staff_pagination").removeClass("staff_phone");
		jQuery(".staff_pagination").removeClass("staff_tablet");
		jQuery(".staff_pagination").addClass("staff_web");
		
		jQuery(".single_contact_main").removeClass("staff_phone");
		jQuery(".single_contact_main").removeClass("staff_tablet");
		jQuery(".single_contact_main").addClass("staff_web");
		
		window_cur_size	= 'staff_screen';
	}
	
	/*------- TABLET --*/
	function staff_tablet() {	
		jQuery("body").removeClass("staff_phone");
		jQuery("body").removeClass("staff_web");
		jQuery("body").addClass("staff_tablet");
		
		jQuery(".staff_sc_container").removeClass("staff_phone");
		jQuery(".staff_sc_container").removeClass("staff_web");
		jQuery(".staff_sc_container").addClass("staff_tablet");
		
		jQuery(".popup").removeClass("staff_phone");
		jQuery(".popup").removeClass("staff_web");
		jQuery(".popup").addClass("staff_tablet");
		
		jQuery(".staff_pagination").removeClass("staff_phone");
		jQuery(".staff_pagination").removeClass("staff_web");
		jQuery(".staff_pagination").addClass("staff_tablet");
		
		jQuery(".single_contact_main").removeClass("staff_phone");
		jQuery(".single_contact_main").removeClass("staff_web");
		jQuery(".single_contact_main").addClass("staff_tablet");
		
		window_cur_size	= 'tablet';
	}
	
	/*------- PHONE --*/
	function staff_phone() {
		jQuery("body").removeClass("staff_tablet");
		jQuery("body").removeClass("staff_web");
		jQuery("body").addClass("staff_phone");
		
		jQuery(".staff_sc_container").removeClass("staff_tablet");
		jQuery(".staff_sc_container").removeClass("staff_web");
		jQuery(".staff_sc_container").addClass("staff_phone");
		
		jQuery(".popup").removeClass("staff_tablet");
		jQuery(".popup").removeClass("staff_web");
		jQuery(".popup").addClass("staff_phone");
		
		jQuery(".staff_pagination").removeClass("staff_tablet");
		jQuery(".staff_pagination").removeClass("staff_web");
		jQuery(".staff_pagination").addClass("staff_phone");
		
		jQuery(".single_contact_main").removeClass("staff_tablet");
		jQuery(".single_contact_main").removeClass("staff_web");
		jQuery(".single_contact_main").addClass("staff_phone");
		
		window_cur_size	= 'phone';
	}
});	