<?php

/**
 * @var $id_gallery \GDGallery\Models\Gallery
 * @var $gallery_data \GDGallery\Models\Gallery
 */

$container = "#gdgallery-container-".$id_gallery;

echo "<style>";
?>

<?= $container ?> a.ug-thumb-wrapper{
    box-shadow: none !important;
}

.ug-lightbox .ug-lightbox-top-panel-overlay{
    background-color: #<?= $options['top_panel_bg_color_wide']; ?> !important;
}

<?=  $gallery_data->custom_css; ?>
<?= "</style>" ?>
