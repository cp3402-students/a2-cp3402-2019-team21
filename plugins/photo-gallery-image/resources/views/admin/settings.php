<div class="wrap gdgallery_list_container " id="gdgallery-settings">

    <div class="gdgallery_content" style="padding:0px;">

        <div class="gdgallery-list-header">
            <button id="save-form-button"><?php _e('Save'); ?></button>
        </div>

        <form id="grand-gallery">
            <div class="one-third">
                <div class="setting-block">
                    <div class="setting-block-title">
                        <img src="<?php echo GDGALLERY_IMAGES_URL . 'icons/uninstall.png'; ?>">
                        <?php _e('Uninstall', GDGALLERY_TEXT_DOMAIN); ?>
                    </div>

                    <div class="setting-row">
                        <p><?php _e('The option is switched OFF by default.', GDGALLERY_TEXT_DOMAIN); ?></p>
                        <label class="switcher switch-checkbox" for="remove-tables-uninstall">
                            <div class="three-fourth">
                                <?php _e('Turn the option ON before uninstalling the plugin, if you want to remove all plugin related data (Database tables)', GDGALLERY_TEXT_DOMAIN); ?>
                            </div>
                            <div class="one-fourth">
                                <input type="hidden"
                                       name="gdgallery_removetablesuninstall"
                                       value="off"/>
                                <input type="checkbox"
                                       class="switch-checkbox" <?php checked('on', get_option("gdgallery_removetablesuninstall")) ?>
                                       name="gdgallery_removetablesuninstall" id="remove-tables-uninstall"><span
                                        class="switch"></span>
                            </div>
                        </label>
                    </div>
                </div>

            </div>
        </form>
    </div>

</div>
<?php
?>