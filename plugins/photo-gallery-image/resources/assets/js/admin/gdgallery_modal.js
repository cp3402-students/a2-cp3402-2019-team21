var gdgalleryModalGallery = {

    show: function (elementId, args) {
        var el = jQuery('#' + elementId);
        if (el.length) {
            el.css('display', 'block');
        }
    },

    hide: function (elementId) {
        var el = jQuery('#' + elementId);
        el.css('display', 'none');
    }
};

jQuery(document).ready(function () {
    jQuery('body').on('click', '.-gdgallery-modal-close', function () {
        gdgalleryModalGallery.hide(jQuery(this).closest('.-gdgallery-modal').attr('id'));
    });
});