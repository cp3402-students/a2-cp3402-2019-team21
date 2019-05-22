<?php
/**
 * @var gallery_data string
 * @var images array
 */


$page_options = array(
    "nav_type" => $options["pagination_nav_type_justified"],
    "nav_text" => $options["pagination_nav_text_justified"],
    "nearby" => $options["pagination_nearby_pages_justified"]
);

$gallery_options = array();
$gallery_options = GDGallery()->settings->getOptionsByView("justified");
$json = json_encode($gallery_options);

wp_enqueue_script("gdgalleryjustified", \GDGallery()->pluginUrl() . "/resources/assets/js/frontend/ug-theme-justified.js", array('jquery'), false, true);
?>

<div id="gdgallery_container_<?= $gallery_data->id_gallery ?>" style="display:none;" data-view="justified">
    <?php foreach ($images as $key => $val):
        $video_id = ($val->type == "image") ? "" : "data-videoid = '" . $val->video_id . "'";
        ?>
        <a href="<?= $val->link ?>" target="<?= $val->target ?>">
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

<?php
if ($gallery_data->display_type == 2) {
    \GDGallery\Helpers\View::render('frontend/pagination.php', compact('gallery_data', 'images', 'page_options'));
} elseif ($gallery_data->display_type == 1) {
    ?>
    <div class="gdgallery_load_more_space">
        <button data-id="<?= $gallery_data->id_gallery ?>" data-count="<?= $gallery_data->items_per_page ?>"
                class="gdgallery_load_more"><?= $options["load_more_text_justified"] ?>

        </button>
        <?php if ($options["load_more_loader_justified"] == 1): ?>
            <ul class="gdgallery_loading gdgallery_reversed" style="display: none;">
                <li></li>
                <li></li>
                <li></li>
            </ul>
        <?php endif; ?>
    </div>
<?php } ?>


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