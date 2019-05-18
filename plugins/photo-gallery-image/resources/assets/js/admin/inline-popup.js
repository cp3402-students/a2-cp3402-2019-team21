jQuery(document).ready(function () {
    jQuery('#grand_gallery_insert').on('click', function () {
        var id = jQuery('#grand_gallery_select option:selected').val();
        window.send_to_editor('[gdgallery_gallery id_gallery="' + id + '"]');
        tb_remove();
        return false;
    });
});