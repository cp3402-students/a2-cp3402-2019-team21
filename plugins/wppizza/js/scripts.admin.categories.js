jQuery(document).ready(function($){
	/*******************************************
	*	category edit page,
	*	make it sortable and update on new sort
	*******************************************/
	if(pagenow=='edit-wppizza_menu'){
		var WPPizzaCategories = $('#the-list');
		WPPizzaCategories.sortable({
			update: function(event, ui) {
				jQuery.post(ajaxurl , {action :'wppizza_admin_categories_ajax',vars:{'field':'save_categories_sort', 'order': WPPizzaCategories.sortable('toArray').toString()}}, function(response) {
					console.log(response);
				},'html').error(function(jqXHR, textStatus, errorThrown) {alert("error : " + errorThrown);});
			}
		});
	}
});