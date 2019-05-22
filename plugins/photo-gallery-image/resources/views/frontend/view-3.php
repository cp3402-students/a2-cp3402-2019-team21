<?php
/**
 * @var gallery_data string
 * @var images array
 */

$gallery_options = array();
$gallery_options = GDGallery()->settings->getOptionsByView("slider");

$gallery_options["gallery_images_preload_type"] = "minimal";
$gallery_options["gallery_width"] = $options["width_slider"];
$gallery_options["gallery_height"] = $options["height_slider"];
$gallery_options["gallery_autoplay"] = $options["autoplay_slider"];
$gallery_options["gallery_play_interval"] = $options["play_interval_slider"];
$gallery_options["gallery_pause_on_mouseover"] = $options["pause_on_hover_slider"];
$gallery_options["slider_scale_mode"] = $options["scale_mode_slider"];
$gallery_options["slider_transition"] = $options["transition_slider"];
$gallery_options["slider_transition_speed"] = (int)$options["transition_speed_slider"];

$gallery_options["slider_loader_type"] = (int)$options["loader_type_slider"];
$gallery_options["slider_loader_color"] = $options["loader_color_slider"];
$gallery_options["slider_enable_bullets"] = $options["bullets_slider"];
$gallery_options["slider_bullets_align_hor"] = $options["bullets_horisontal_position_slider"];
$gallery_options["slider_bullets_align_vert"] = $options["bullets_vertical_position_slider"];
$gallery_options["slider_enable_arrows"] = $options["arrows_slider"];
$gallery_options["slider_enable_progress_indicator"] = $options["progress_indicator_slider"];
$gallery_options["slider_progress_indicator_type"] = $options["progress_indicator_type_slider"];
$gallery_options["slider_progress_indicator_align_hor"] = $options["progress_indicator_horisontal_position_slider"];
$gallery_options["slider_progress_indicator_align_vert"] = $options["progress_indicator_vertical_position_slider"];
$gallery_options["slider_enable_play_button"] = $options["play_slider"];
$gallery_options["slider_play_button_align_hor"] = $options["play_horizontal_position_slider"];
$gallery_options["slider_play_button_align_vert"] = $options["play_vertical_position_slider"];
$gallery_options["slider_enable_fullscreen_button"] = $options["fullscreen_slider"];
$gallery_options["slider_fullscreen_button_align_hor"] = $options["fullscreen_horisontal_position_slider"];
$gallery_options["slider_fullscreen_button_align_vert"] = $options["fullscreen_vertical_position_slider"];
$gallery_options["slider_control_zoom"] = $options["zoom_slider"];
if ($options["zoom_slider"] == 1) {
    $gallery_options["slider_enable_zoom_panel"] = $options["zoom_panel_slider"];
} else {
    $gallery_options["slider_enable_zoom_panel"] = false;
}
$gallery_options["slider_zoompanel_align_hor"] = $options["zoom_horisontal_panel_position_slider"];
$gallery_options["slider_zoompanel_align_vert"] = $options["zoom_vertical_panel_position_slider"];
$gallery_options["slider_controls_always_on"] = $options["controls_always_on_slider"];
$gallery_options["slider_videoplay_button_type"] = $options["video_play_type_slider"];
$gallery_options["slider_textpanel_always_on"] = $options["text_panel_always_on_slider"];
$gallery_options["slider_enable_text_panel"] = $options["text_panel_slider"];
$gallery_options["slider_textpanel_enable_title"] = $options["text_title_slider"];
$gallery_options["slider_textpanel_enable_description"] = $options["text_description_slider"];
if ($options["text_title_slider"] == 0 && $options["text_description_slider"] == 0) {
    $gallery_options["slider_enable_text_panel"] = false;
}
$gallery_options["slider_textpanel_enable_bg"] = $options["text_panel_bg_slider"];
$gallery_options["gallery_carousel"] = $options["carousel_slider"];
$gallery_options["slider_textpanel_bg_color"] = "#" . $options["text_panel_bg_color_slider"];
$gallery_options["slider_textpanel_bg_opacity"] = $options["text_panel_bg_opacity_slider"] / 100;
$gallery_options["slider_textpanel_title_font_size"] = $options["text_panel_title_size_slider"];
$gallery_options["slider_textpanel_title_color"] = "#" . $options["text_panel_title_color_slider"];
$gallery_options["slider_textpanel_desc_font_size"] = $options["text_panel_desc_size_slider"];
$gallery_options["slider_textpanel_desc_color"] = "#" . $options["text_panel_desc_color_slider"];


if ($options["playlist_slider"] == 1) {
    $gallery_options["theme_panel_position"] = $options["playlist_pos_slider"];
    $gallery_options["gallery_theme"] = "grid";
    $gallery_options["thumb_width"] = $options["thumb_width_slider"];
    $gallery_options["thumb_height"] = $options["thumb_height_slider"];
    $gallery_options["gridpanel_background_color"] = "#" . $options["playlist_bg_slider"];
    wp_enqueue_script("gdgalleryoneandothers", \GDGallery()->pluginUrl() . "/resources/assets/js/frontend/ug-theme-grid.js", array('jquery'), false, true);
} else {
    wp_enqueue_script("gdgalleryslider", \GDGallery()->pluginUrl() . "/resources/assets/js/frontend/ug-theme-slider.js", array('jquery'), false, true);
}


$gallery_options["slider_progress_indicator_offset_hor"] = 5;
$gallery_options["slider_progress_indicator_offset_vert"] = 5;

$gallery_options["slider_play_button_offset_hor"] = 60;
$gallery_options["slider_play_button_offset_vert"] = 8;

$gallery_options["slider_fullscreen_button_offset_hor"] = 37;
$gallery_options["slider_fullscreen_button_offset_vert"] = 9;

$gallery_options["slider_zoompanel_offset_hor"] = 12;
$gallery_options["slider_zoompanel_offset_vert"] = 40;


$json = json_encode($gallery_options);

?>


<div id="gdgallery_container_<?= $gallery_data->id_gallery ?>" style="display:none;" data-view="slider">

    <?php foreach ($images as $key => $val):
        $video_id = ($val->type == "image") ? "" : "data-videoid = '" . $val->video_id . "'";
        ?>
        <img alt="<?= $val->name ?>"
             data-type="<?= $val->type ?>"
             src="<?= $val->url ?>"
             data-image="<?= $val->url ?>"
             data-description="<?= $val->description ?>"
            <?= $video_id ?>
             style="display:block">

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