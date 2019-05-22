jQuery(document).ready(function () {

    var items_per_page = jQuery(".gdgallery_load_more").data("count");
    var loaded_items_count = parseInt(items_per_page) * 2;

    jQuery(".gdgallery_load_more").click(function (e) {
        e.preventDefault();

        var g_id = jQuery(this).data("id"),
            t = jQuery(this),
            general_data = {
                action: "gdgallery_get_items",
                id_gallery: g_id,
                offset: loaded_items_count
            };

        jQuery.ajax({
            type: 'POST',
            url: ajaxurl,
            data: general_data,
            beforeSend: function () {
                t.hide();
                t.parent().find(".gdgallery_loading").show()
            },
            success: function (response) {

                t.show();
                t.parent().find(".gdgallery_loading").hide();
                loaded_items_count = loaded_items_count + items_per_page;
                t.attr("data-count", loaded_items_count);

                response = JSON.parse(response);

                var g_view = jQuery("#gdgallery_container_" + g_id).data("view");
                jQuery("#gdgallery_container_" + g_id).empty();
                jQuery(response.data).each(function (key, item) {
                    jQuery("#gdgallery_container_" + g_id).append("" +
                        "<a href='" + item.link + "'><img alt='" + item.name + "' data-type='" + item.type + "' src='" + item.url + "' data-image='" + item.url + "' " +
                        "data-description='" + item.description + "' data-videoid='" + item.video_id + "' style='display:block;'></a>")
                });

                jQuery("#gdgallery_container_" + g_id).hide();
                setTimeout(function () {
                    jQuery("#gdgallery_container_" + g_id).unitegallery(mainjs.options);
                    jQuery("#gdgallery_container_" + g_id).show();
                }, 0);


                if (response.show_button == 0) {
                    t.hide();
                }

            }
        });

    });

});



