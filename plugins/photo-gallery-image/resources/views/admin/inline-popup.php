<?php

$galleries = \GDGallery\Models\Gallery::get();


?>
<style>
    .tb_popup_form {
        position: relative;
        display: block;
    }

    .tb_popup_form li {
        display: block;
        height: 35px;
        width: 70%;
    }

    .tb_popup_form li label {
        float: left;
        width: 35%
    }

    .tb_popup_form li input {
        float: left;
        width: 60%;
    }

    .slider, .slider-container {
        display: block;
        position: relative;
        height: 35px;
        line-height: 35px;
    }


</style>
<div id="gdgallery" style="display:none;">

    <?php

    $new_gallery_link = admin_url('admin.php?page=gdgallery&task=create_new_gallery');

    $new_gallery_link = wp_nonce_url($new_gallery_link, 'gdgallery_create_new_gallery');

    if ($galleries && !empty($galleries)) {
        \GDgallery\Helpers\View::render('admin/inline-popup-gallery.php', array('galleries' => $galleries));
    } else {
        printf(
            '<p>%s<a class="button" href="%s">%s</a></p>',
            __('You have not created any galleries yet', GDGALLERY_TEXT_DOMAIN) . '<br>',
            $new_gallery_link,
            __('Create New Gallery', GDGALLERY_TEXT_DOMAIN)
        );
    }

    ?>
</div>
