/*=-=-=-=-=-=- Categories page / Parameters -=-=-=-=-=-=-=*/
/*=- 1. Add Parameter -=*/
jQuery(document).on('click', '.TWDCatParam .twd_addCatParam', function () {
    jQuery('.TWDCatParam .parameters_td').find('input[value=""]').attr('value', jQuery('.TWDCatParam .parameters_td input').last().val());
    if (jQuery('.TWDCatParam .parameters_td').find('input[value=""]').length < 1) {
        jQuery('.TWDCatParam .parameters_td').append('<br/><input type="text" class="parameter" name="' + jQuery('.TWDCatParam .parameters_td input').last().attr('name') + '" value=""/><div class="twd_delCatParam"></div>');
    }
});
/*=- 2. Delete Parameter -=*/
jQuery(".TWDCatParam").on('click', '.twd_delCatParam', function () {
    if (jQuery(this).prevAll('input.parameter').first().val() != "" && jQuery('.TWDCatParam .parameters_td').find('input').length > 1) {
        jQuery(this).prevAll('input.parameter').first().remove();
        jQuery(this).prev('div.twd_addCatParam').remove();
        jQuery(this).next('br').remove();
        jQuery(this).remove();
    }
});
/*=- 3. Change/Select Category -=*/
jQuery(document).on('change', '.parameter', function () {
    jQuery(this).attr('value', jQuery(this).val());
});
if(jQuery('.td_params').length<=0){
    is_checked = jQuery('input[name="tax_input[cont_category][]"]').is(':checked');
    if(is_checked){
        twd_get_parameter('.selectit input[name="tax_input[cont_category][]"]');
    }
}
/*=- 4. Reload page after add category -=*/
var cliableInputTeam = jQuery('.TWDCatParam').next().next().find('input.button-primary');
cliableInputTeam.click(function (){ setTimeout(function() { window.location.reload(); }, 1000); });


/*=-=-=-=-=-=- Single page edit / Categories parameters -=-=-=-=-=-=-=*/
/*=- 1. Add Parameter -=*/
jQuery('.twd_contParams tr').each(function() {
	jQuery(this).find('input:first').next('.twd_delParam').remove();
});
jQuery(document).on('click', '.twd_contParams .twd_addParam', function () {
    var param_td = jQuery(this).closest('tr').prev('tr').find('.td_params');
    if (param_td.find('input[value=""]').length < 1) {
        param_td.append('<br/><input type="text" class="parameter" name="' + param_td.find('input').last().attr('name') + '" value=""/><div class="twd_delParam"></div>');
    }
});
/*=- 2. Delete Parameter -=*/
jQuery(".twd_contParams").on('click', '.twd_delParam', function () {
    if (jQuery(this).prevAll('input.parameter').first().val() != "" && jQuery(this).closest('td').find('input').length > 1) {
        jQuery(this).prevAll('input.parameter').first().remove();
        jQuery(this).prev('div.twd_addParam').remove();
		jQuery(this).prev().remove();
        jQuery(this).remove();
    }
});
/*=- 3. Change/Select Category -=*/
jQuery('input[name="tax_input[cont_category][]"]').on('change', function () {
    twd_get_parameter(this);
});
function twd_get_parameter(element){
    var ischecked = jQuery(element).is(':checked');
    if (ischecked) {
        var cats = [];
        var forCat = jQuery(element).val();
        jQuery('input[name="tax_input[cont_category][]"]:checked').each(function () {
            if (forCat == jQuery(this).val())
                return;
            cats.push(jQuery(this).val());
        });
        var data = {
            action: 'get_cat_param',
            cats: cats,
            forCat: forCat
        };
		jQuery('table.twd_contParams').find('#notselyet').remove();
        jQuery.post(staff_ajaxurl, data, function (response) {
            jQuery('table.twd_contParams').append(response);
        });
    } else {
        var cats = [];
        var forCat = jQuery(element).val();
        jQuery('input[name="tax_input[cont_category][]"]:checked').each(function () {
            if (forCat == jQuery(this).val())
                return;
            cats.push(jQuery(this).val());
        });
        var data = {
            action: 'get_cat_param',
            cats: cats,
            forCat: jQuery(element).val(),
            unCheck: true
        };

        jQuery.post(staff_ajaxurl, data, function (response) {
            var res = JSON.parse(response);
            for (var key in res) {
                if (res.hasOwnProperty(key)) {
                    var tr = jQuery('table.twd_contParams td:contains("' + res[key] + '")').parent('tr');
                    tr.next('tr').remove();
                    tr.remove();
                }
            }
        });
    }
}
jQuery(document).on('keydown', 'form input.parameter', function (event) {
    if (event.keyCode == 13) {
        event.preventDefault();
        jQuery(this).change();
        return false;
    }
});


/*=-=-=-=-=-=- StylesAndColors -=-=-=-=-=-=-=*/
jQuery('#cont_tabs').tabs();
if (typeof (localStorage.currentItem) !== "undefined") {
    var current_item = localStorage.currentItem;
    jQuery("#cont_tabs > div").css("display", "none");
    jQuery(current_item).css("display", "block");
    jQuery("#cont_tabs #cont_theme_ul li").removeClass("ui-state-active");
    jQuery('#cont_tabs #cont_theme_ul li a[href="' + current_item + '"]').parent('li').addClass("ui-state-active");
} else {
    jQuery('#general').css("display", "block");
    jQuery('#cont_tabs #cont_theme_ul li:first-child').addClass("ui-state-active");
}

jQuery("#cont_theme_ul li a").each(function (indx, element) {
    jQuery(element).click(function () {
        if (typeof (Storage) !== "undefined") {
            localStorage.currentItem = jQuery(element).attr("href");
        }
        jQuery("#cont_tabs > div").css("display", "none");
        jQuery(localStorage.currentItem).css("display", "block");
        jQuery('#cont_tabs #cont_theme_ul li').removeClass("ui-state-active");
        jQuery(element).parent().addClass("ui-state-active");
    });
});
jQuery('body').on('click', '.cont_theme_activate', function () {
    var data = {
        action: 'activate_theme',
        id: jQuery(this).attr('theme-id'),
    };
    jQuery.post(staff_ajaxurl, data, function (id) {
        jQuery('.cont_theme_enable').replaceWith('<a href="#" class="cont_theme_activate" theme-id="' + jQuery('.cont_theme_enable').attr('theme-id') + '">Activate</a>');
        jQuery('.cont_theme_activate[theme-id="' + id + '"]').replaceWith("<p class ='cont_theme_enable' theme-id='" + id + "' style='color:green;'>Active</p>");
    });
    return false;
});
/*-- GoToTop --*/
jQuery( window ).scroll(function() {
    var height = jQuery(window).scrollTop();
    if(height > 400){
        jQuery('#go_to_top').css('display', 'inline');
    }
    else {
        jQuery('#go_to_top').css('display', 'none');
    }
});
jQuery('#go_to_top').click(function(){
    jQuery("html, body").animate({ scrollTop: 0 }, 1000);
    return false;
});


/*=-=-=- Messages -=-=-=-=*/
jQuery('.deleteMess').on('click', function () {
    if (confirm("Are you sure you want to delete ?")) {
        var data = {
            action: 'del_mess',
            id: jQuery(this).attr('mess-id'),
        };
        jQuery.post(staff_ajaxurl, data, function (response) {
            if (response = 'true') {
                location.reload();
            } else {
                jQuery(this).click();
            }

        });
    } else {
    }

});
jQuery("#messageDialog").dialog({
    autoOpen: false,
    resizable: false,
    position: {my: "top", at: "top", of: document},
    minWidth: 600
});
jQuery('.viewMess').on('click', function (e) {
    e.preventDefault();
    var data = {
        action: 'view_mess',
        id: jQuery(this).attr('mess-id'),
    };
    jQuery.post(staff_ajaxurl, data, function (response) {
        jQuery("#messageDialog").html(response);
    });
    jQuery("#messageDialog").dialog('open');
});
jQuery('input.sc_color').colorpicker({
    displayIndicator: false,
    displayPointer: false,
    transparentColor: true
});
jQuery('input.sc_color').on('change.color', function (event, color) {
    jQuery(this).css('background-color', color);
});
jQuery('.sc_color').each(function () {
    jQuery(this).css("background-color", jQuery(this).val());
});
jQuery("#mess_date_from_filter, #mess_date_to_filter").datetimepicker({
    timepicker: false,
    format: 'Y-m-d',
    scrollInput: false,
    closeOnDateSelect: true
});


/*=-=-=- Upload "No Image" -=-=-=-=*/
jQuery(document).ready(function() {
    var uploadID = '';

    jQuery('.sc_upload-button').click(function() {
        uploadID = jQuery(this).prev('input');
        formfield = jQuery('.upload').attr('name');
        tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');
        return false;
    });
    if ((jQuery('.sc_upload-button').length > 0)){
        window.send_to_editor = function(html) {
            imgurl = jQuery('img',html).attr('src');
            if(typeof imgurl =='undefined'){ imgurl = jQuery(html).attr('src');}
            console.log(uploadID);
            uploadID.val(imgurl);
            tb_remove();
        };
    }

    if(jQuery('.delete_demo_data').length > 0){
        var delete_demo_data = jQuery('.delete_demo_data');
        delete_demo_data.click(function(){
            jQuery('.demo_loader').css({'display':'inline-block'});
            var data = {
                action: 'delete_demo_data',
                delete: true,
            };
            jQuery.post(staff_ajaxurl, data, function (response) {
                jQuery('.d_demo_data').html('<h3>Demo data has been deleted</h3>')
            });


        });
    }

/*=-=-=- Custom url -=-=-=-=*/
    var custom_url = jQuery("#custom_url");
    var default_url = jQuery("#default_url");
    var team_url_tr = jQuery("#team_url_tr");
    var team_url_checked = default_url.attr( "checked" );
    if(team_url_checked==="checked"){
        team_url_tr.css({
            'display':'table-row'
        });
    }
    default_url.change(function () {
        team_url_tr.css({
            'display':'table-row'
        });
    });
    custom_url.change(function () {
        team_url_tr.css({
            'display':'none'
        });
    });

});



