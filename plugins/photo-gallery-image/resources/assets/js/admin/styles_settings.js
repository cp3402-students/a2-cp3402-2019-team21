jQuery(document).ready(function ($) {


    jQuery('.styles_wrapper').masonry({
        itemSelector: '.settings-section-wrap'
    });

    jQuery("#settings_tab").tabs({
        activate: function(){
            setTimeout(function(){jQuery('.styles_wrapper > div ').masonry();},1);
        }
    });

    jQuery('.settings-section-heading').on('click', function () {
        var section = jQuery(this).closest('.settings-section-wrap');
        section.toggleClass('active');
        jQuery(this).parents('.ui-tabs-panel').masonry();
        //jQuery('.styles_wrapper #justified').masonry();
    });


    var load_type = jQuery(".show_loader .input-wrap select")
    var load_color = jQuery("#loader_color_slider .input-wrap select");

    // jQuery(".show_loader .input-wrap").append("<div  class='loader-" + load_color.val() + load_type.val() + "' style='position: absolute; right: 150px;'></div>");
    showLoader(load_type, load_color);

    function showLoader(type, color) {
        jQuery(".show_loader .input-wrap").append("<div id='show_loader' style='position: absolute; right: 150px;'><div  class='loader-" + color.val() + type.val() + "' ></div></div>");
        type.change(function () {
            type = jQuery(this).val();
            color = jQuery("#loader_color_slider .input-wrap select").val();
            jQuery("#show_loader").empty();
            jQuery(".show_loader .input-wrap #show_loader").append("<div  class='loader-" + color + type + "' ></div>");
        });

        color.change(function () {
            color = jQuery(this).val();
            type = jQuery(".show_loader .input-wrap select").val();
            jQuery("#show_loader").empty();
            jQuery(".show_loader .input-wrap #show_loader").append("<div  class='loader-" + color + type + "' ></div>");
        });
    }

    var link_j = jQuery("#item_as_link_justified input[type=checkbox]");
    var link_t = jQuery("#item_as_link_tiles input[type=checkbox]");
    var link_c = jQuery("#item_as_link_carousel input[type=checkbox]");
    var link_g = jQuery("#item_as_link_grid input[type=checkbox]");
    disableFiled(link_j, ["#show_icons_justified"], true);
    disableFiled(link_t, ["#show_icons_tiles"], true);
    disableFiled(link_c, ["#show_icons_carousel"], true);
    disableFiled(link_g, ["#show_icons_grid"], true);
    var zoom = jQuery("#zoom_slider input[type=checkbox]");
    disableFiled(zoom, ["#zoom_panel_slider", "#zoom_horisontal_panel_position_slider", "#zoom_vertical_panel_position_slider"], false);


    function disFiled(em, view, cond) {
        if (em.prop("checked") === cond) {
            jQuery(view).each(function (index, val) {
                jQuery(val).addClass("disabled_option");
                jQuery(val).find("select").prop("disabled", true);
            });

        }
        else {
            jQuery(view).each(function (index, val) {
                jQuery(val).removeClass("disabled_option");
                jQuery(val).find("select").prop("disabled", false);
            });
        }
    }

    function disableFiled(em, view, cond) {
        disFiled(em, view, cond);
        em.change(function () {
            disFiled(em, view, cond);
        })
    }

    jQuery(window).scroll(function () {
        if (jQuery(this).scrollTop() >= 300) {
            if (jQuery(".settings-save-head").hasClass("settings-save-fixed") === false) {
                jQuery(".settings-save-head").addClass("settings-save-fixed");
            }
            jQuery(".iframe_section").css("top", "15%");
            $('.gdgallery_scrollup').fadeIn();
        }
        else {
            if (jQuery(".settings-save-head ").hasClass("settings-save-fixed")) {
                jQuery(".settings-save-head").removeClass("settings-save-fixed");
            }
            jQuery(".iframe_section").css("top", "43%");
            $('.gdgallery_scrollup').fadeOut();
        }
    });

    $('.gdgallery_scrollup').click(function () {
        $("html, body").animate({
            scrollTop: 0
        }, 600);
        return false;
    });

    var doingAjax = false;
    jQuery('#settings_form').on('submit', function (e) {


        e.preventDefault();

        $(".pro_save_message").fadeIn().delay(3000).fadeOut();

        /* if (doingAjax) return false;

         var form = jQuery('#settings_form'),
         submitBtn = form.find('input[type=submit]'),
         formData = form.serialize(),
         general_data = {
         action: "gdgallery_save_settings",
         formdata: formData
         };


         jQuery.ajax({
         url: ajaxurl,
         method: 'post',
         data: general_data,
         dataType: 'text',
         beforeSend: function () {
         doingAjax = true;
         submitBtn.attr("disabled", 'disabled');
         submitBtn.parent().find(".spinner").css("visibility", "visible");
         }
         }).always(function () {
         doingAjax = false;
         submitBtn.removeAttr("disabled");
         submitBtn.parent().find(".spinner").css("visibility", "hidden");
         }).done(function (response) {
         if (response === 'ok') {
         toastr.success('Saved Successfully');
         FrameID = "test_frame";
         document.getElementById(FrameID).contentDocument.location.reload(true);
         } else {
         toastr.error('Error while saving');
         }
         }).fail(function () {
         toastr.error('Error while saving');
         });

         return false;*/
    });


});