<?php

$gallery_options = array();
$gallery_options = GDGallery()->settings->getOptionsByView("grid");
$json = json_encode($gallery_options);

wp_enqueue_script("gdgallerygrid", \GDGallery()->pluginUrl() . "/resources/assets/js/frontend/ug-theme-grid.js", array('jquery'), false, true);

?>

<div id="gdgallery_container_<?= $gallery_data->id_gallery ?>" style="display:none;" data-view="grid">
    <?php foreach ($images as $key => $val):
        $video_id = ($val->type == "image") ? "" : "data-videoid = '" . $val->video_id . "'";
        ?>
        <a href="<?= $val->link ?>">
            <img alt="<?= $val->name ?>"
                 data-type="<?= $val->type ?>"
                 src="<?= $val->url ?>"
                 data-image="<?= $val->url ?>"
                 data-description="<?= $val->description ?>"
                <?= $video_id ?>
                 style="display:block">
        </a>
    <?php endforeach; ?>
</div>

<script type="text/javascript">
    (function() {
        var runMyCode = function() {
            jQuery(document).ready(function () {
                var container = jQuery("#gdgallery_container_<?= $gallery_data->id_gallery ?>");
                container.unitegallery(<?= $json ?>);
            });
        };
        var timer = function() {
            if (window.jQuery) {runMyCode(window.jQuery);}
            else {window.setTimeout(timer, 100);}
        };
        timer();
    })();
</script>