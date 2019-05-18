jQuery(document).ready(function () {

    /* save plugin settings */
    jQuery('.gdgallery_content').on("click", "#save-form-button", function () {

        var grandForm = jQuery('#grand-gallery');

        formData = grandForm.serializeArray();

        var general_data = {
            action: "gdgallery_save_plugin_settings",
            nonce: gdgallery_settingsSave.nonce,
            formData: formData,
        };

        jQuery(this).attr("disabled", "disabled")
        jQuery.post(ajaxurl, general_data, function (response) {
            if (response.success) {
                jQuery('#save-form-button').removeAttr('disabled');
                toastr.success('Saved Successfully');
            } else {
                toastr.error('Error while saving');
            }
        }, "json");

        return false;
    });
});