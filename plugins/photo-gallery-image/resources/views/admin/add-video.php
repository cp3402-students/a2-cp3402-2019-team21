<div id="gdgallery-addvideo-modal" class="-gdgallery-modal">
    <div class="-gdgallery-modal-content">
        <div class="-gdgallery-modal-content-header">
            <div class="-gdgallery-modal-header-icon">

            </div>
            <div class="-gdgallery-modal-header-info">
                <h3><?= _e('Add Video URL From Youtube or Vimeo', 'gdgallery'); ?></h3>
            </div>
            <div class="-gdgallery-modal-close">
                <i class="fa fa-close"></i>
            </div>
        </div>
        <div class="-gdgallery-modal-content-body">
            <form action="admin.php?page=gdgallery&id=<?php echo $id_gallery; ?>&save_data_nonce=<?php echo $save_data_nonce; ?>"
                  method="post" id="gdgallery_add_video_form" name="gdgallery_add_video_form">

                <input type="hidden" name="gdgallery_id_gallery" value="<?= $id_gallery ?>">
                <ul class="video_fields">

                    <li><label for="gdgallery_video_url"><?= _e('Video URL (Youtube or Vimeo)', 'gdgallery'); ?>
                            :</label><br>
                        <input type="text" id="gdgallery_video_url"
                               name="gdgallery_video_url"
                               value="" required>
                    </li>

                    <li><label for="gdgallery_video_name"> <?= _e('Title', 'gdgallery'); ?>:</label><br>
                        <input type="text" id="gdgallery_video_name"
                               name="gdgallery_video_name"
                               value="">
                    </li>
                    <li>
                        <label for="gdgallery_video_description">
                            <?= _e('Description', 'gdgallery'); ?>: </label><br>
                        <input type="text" id="gdgallery_video_description"
                               name="gdgallery_video_description"
                               value=""></li>
                    <li>
                        <label for="gdgallery_video_link"> <?= _e('Link', 'gdgallery'); ?>:</label><br>
                        <input type="text" name="gdgallery_video_link"
                               id="gdgallery_video_link"
                               value=""></li>
                </ul>

                <div class="video_save">
                    <input type="submit" value="<?= _e('Save', 'gdgallery'); ?>"
                           id="gdgallery-add-video-buttom"
                           class="gdgallery-save-buttom">
                    <span class="spinner"></span>
                </div>
            </form>


        </div>
    </div>
</div>