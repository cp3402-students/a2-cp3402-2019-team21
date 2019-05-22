<?php
/**
 * Template for edit gallery page
 * @var $gallery \GDGallery\Models\Gallery
 */

use GDGallery\Controllers\Frontend\GalleryPreviewController as Preview;

global $wpdb;

$gallery->setViewStyles();

$items = $gallery->getItems(true);

$gallery_data = $gallery->getGallery();

$list = $gallery->getGalleriesUrl();

$new_gallery_link = admin_url('admin.php?page=gdgallery&task=create_new_gallery');

$new_gallery_link = wp_nonce_url($new_gallery_link, 'gdgallery_create_new_gallery');

$id = $gallery->getId();


$save_data_nonce = wp_create_nonce('gdgallery_nonce_save_data' . $id);

$hidden_class = "gdgallery_hidden";
$display_type_opt = (in_array($gallery_data->view_type, array(0, 1))) ? "" : $hidden_class;
$show_title_opt = (isset($gallery_data->show_title) && $gallery_data->show_title == 1) ? "" : $hidden_class;


?>

<ul class="switch_gallery">
    <?php foreach ($list as $val): ?>
        <?php if ($val["id_gallery"] == $id): ?>
            <li class='active_gallery' id='gdgallery_active'>
                <a href="#" id="gdgallery_edit_name"><i class="fa fa-pencil" aria-hidden="true"></i></a>

                <a href="<?= $val["url"] ?>" id="gallery_active_name"><?= stripslashes($val["name"]) ?></a>
                <input type='text' name='edit_name' id='edit_name_input' value='<?= stripslashes($val["name"]) ?>'
                       class="gdgallery_hidden">
            </li>
        <?php else: ?>
            <li>
                <a href="<?= $val["url"] ?>"><?= $val["name"] ?></a>
            </li>
        <?php endif; ?>
    <?php endforeach; ?>
    <li class="add_gallery_li">
        <a href="<?= $new_gallery_link ?>"><?= __('ADD GALLERY', 'gdgallery') ?> <i class="fa fa-plus"
                                                                                    aria-hidden="true"></i></a>
    </li>
</ul>
<form action="admin.php?page=gdgallery&id=<?php echo $id; ?>&save_data_nonce=<?php echo $save_data_nonce; ?>"
      method="post" name="gdgallery_images_form" id="gdgallery_images_form">
    <div class="wrap gdgallery_edit_gallery_container">
        <div class="gdgallery_nav">


            <div id="gdgallery_tabs">
                <div style="clear: both"></div>
                <div class="settings-toogled-container">

                    <ul class="gdgallery_tabs">
                        <li class="Tabs__tab gdgallery_active_Tab gdgallery_Tab">
                            <a href="#gdgallery_gallery_style"><?php _e('Gallery Style', 'gdgallery'); ?></a>
                        </li>
                        <li class="Tabs__tab gdgallery_Tab">
                            <a href="#gdgallery_general_settings"><?php _e('General Settings', 'gdgallery'); ?></a>
                        </li>
                        <li class="Tabs__tab gdgallery_Tab">
                            <a href="#gdgallery_custom_css"><?php _e('Custom CSS', 'gdgallery'); ?></a>
                        </li>
                        <li class="Tabs__tab gdgallery_Tab">
                            <a href="#gdgallery_get_shortcode"><?php _e('Get Shortcode', 'gdgallery'); ?></a>
                        </li>
                        <li class="Tabs__presentation-slider" role="presentation"></li>
                        <a href="<?php echo Preview::previewUrl($gallery->getId(), false); ?>"
                           class="single_gallery_preview" target="_blank"><?php _e('Preview Changes', 'gdgallery'); ?>
                            <img
                                    src="<?= GDGALLERY_IMAGES_URL ?>icons/preview.png"></a>
                        <input type="submit" value="<?= _e('Save', 'gdgallery'); ?>"
                               id="gdgallery-save-buttom"
                               class="gdgallery-save-buttom gdgallery-save-all">
                        <span class="spinner"></span>

                    </ul>
                    <div id="gdgallery_gallery_style" style="display: none;">
                        <?php foreach ($gallery->getViewStyles() as $key => $view): ?>
                            <div class="gdgallery_view_item <?php if ($gallery_data->view_type == $key) echo "checked_view" ?>">
                                <label>
                                    <p><?= $view[0] ?></p>
                                    <input type="radio" <?php if ($gallery_data->view_type == $key) echo "checked" ?>
                                           name="gdgallery_view_type" value="<?= $key ?>"/>
                                    <img src="<?= $view[1] ?>">

                                </label>
                            </div>
                        <?php endforeach; ?>

                    </div>
                    <div id="gdgallery_general_settings">
                        <div class="gallery_title_div">
                            <input type="text" id="gallery_name" name="gdgallery_name"
                                   value="<?php if ($gallery->getName() != "New Created Gallery") echo $gallery->getName(); ?>"
                                   placeholder="<?= _e('Name Your Gallery', 'gdgallery'); ?>">
                            <input type="hidden" id="gdgallery_id_gallery" name="gdgallery_id_gallery"
                                   value="<?php echo $id; ?>">

                        </div>
                        <ul class="gdgallery_general_settings">
                            <?php if (isset($gallery_data->show_title)) { ?>
                                <li class="gdgallery_show_title_section">
                                    <h4><?= _e('Show Gallery Title', 'gdgallery'); ?></h4>
                                    <input type="checkbox" id="gdgallery_show_title"
                                           name="gdgallery_show_title" <?php if ($gallery_data->show_title == 1) echo "checked"; ?>>
                                </li>
                                <li class="gdgallery_title_position_section <?php echo $show_title_opt; ?>">
                                    <h4><?= _e('Gallery Title Position', 'gdgallery'); ?></h4>
                                    <select name="gdgallery_position" id="gdgallery_position">
                                        <option value="left" <?php if ($gallery_data->position == 'left') echo "selected" ?>>
                                            <?= _e('Left', 'gdgallery'); ?>
                                        </option>
                                        <option value="center" <?php if ($gallery_data->position == 'center') echo "selected" ?>>
                                            <?= _e('Center', 'gdgallery'); ?>
                                        </option>
                                        <option value="right" <?php if ($gallery_data->position == 'right') echo "selected" ?>>
                                            <?= _e('Right', 'gdgallery'); ?>
                                        </option>
                                    </select>
                                </li>
                            <?php } ?>
                            <li class="gdgallery_display_type_section <?= $display_type_opt ?>">
                                <h4><?= _e('Content Display Type', 'gdgallery'); ?></h4>
                                <select name="gdgallery_display_type" id="gdgallery_display_type">
                                    <option value="0" <?php if ($gallery_data->display_type == 0) echo "selected" ?>>
                                        <?= _e('Show All', 'gdgallery'); ?>
                                    </option>
                                    <option value="1" <?php if ($gallery_data->display_type == 1) echo "selected" ?>>
                                        <?= _e('Load more', 'gdgallery'); ?>
                                    </option>
                                    <option value="2" <?php if ($gallery_data->display_type == 2) echo "selected" ?>>
                                        <?= _e('Pagination', 'gdgallery'); ?>
                                    </option>
                                </select>
                            </li>
                            <li class="gdgallery_items_per_page_section <?php if ($gallery_data->display_type == 0) echo "gdgallery_hidden" ?>  <?= $display_type_opt ?>">
                                <h4>  <?= _e('Items Per Page', 'gdgallery'); ?></h4>
                                <input type="number" min="0" max="100" name="gdgallery_items_per_page"
                                       id="gdgallery_items_per_page" class="gdgallery_items_per_page"
                                       value="<?= $gallery_data->items_per_page ?>">
                            </li>
                            <li class="gdgallery_sorting_section">
                                <h4><?= _e('Image Sorting', 'gdgallery'); ?></h4>
                                <select name="gdgallery_sort_by" id="gdgallery_sorting">
                                    <option value="0" <?php if ($gallery_data->sort_by == 0) echo "selected" ?>>
                                        <?= _e('Custom Sorting', 'gdgallery'); ?>
                                    </option>
                                    <option value="1" <?php if ($gallery_data->sort_by == 1) echo "selected" ?>>
                                        <?= _e('Numeric / Alphabetical', 'gdgallery'); ?>
                                    </option>
                                    <option value="2" <?php if ($gallery_data->sort_by == 2) echo "selected" ?>>
                                        <?= _e('Upload Date', 'gdgallery'); ?>
                                    </option>
                                </select>
                            </li>
                            <li class="gdgallery_ordering_section">
                                <h4><?= _e('Image order', 'gdgallery'); ?></h4>
                                <select name="gdgallery_order_by" id="gdgallery_ordering">
                                    <option value="0" <?php if ($gallery_data->order_by == 0) echo "selected" ?>>
                                        <?= _e('Ascending', 'gdgallery'); ?>
                                    </option>
                                    <option value="1" <?php if ($gallery_data->order_by == 1) echo "selected" ?>>
                                        <?= _e('Descending', 'gdgallery'); ?>
                                    </option>
                                    <option value="2" <?php if ($gallery_data->order_by == 2) echo "selected" ?>>
                                        <?= _e('Random', 'gdgallery'); ?>
                                    </option>
                                </select>
                            </li>

                        </ul>


                    </div>

                    <div id="gdgallery_custom_css">
                        <div class="custom_css_col">
                            <textarea cols="8" name="custom_css"><?php
                                if ($gallery_data->custom_css != "") {
                                    echo stripslashes($gallery_data->custom_css);
                                } else {
                                    echo "#gdgallery_container_" . $id . "{}";
                                }
                                ?></textarea>
                        </div>
                    </div>
                    <div id="gdgallery_get_shortcode">
                        <div class="gdgallery_shortcode_types">
                            <div class="gdgallery_example">
                                <h3> <?= _e('Shortcode', 'gdgallery'); ?></h3>
                                <p> <?= _e('Copy and paste this shortcode into your posts or pages.', 'gdgallery'); ?></p>
                                <div class="gdgallery_highlighted">
                                    <span id="gdgallery_editor_code">[gdgallery_gallery id_gallery="<?= $id ?>"]</span>
                                    <a href="#" onclick="copyToClipboard('gdgallery_editor_code')"
                                       class="copy_shortcode" title="<?= _e('Copy shortecode', 'gdgallery'); ?>"><i
                                                class="fa fa-files-o" aria-hidden="true"></i></a>
                                </div>
                            </div>
                            <div class="gdgallery_example">
                                <h3><?= _e('Post and/or Page', 'gdgallery'); ?></h3>
                                <p> <?= _e('Insert regular shortcode to post/page using this icon', 'gdgallery'); ?></p>
                                <img src="<?= GDGALLERY_IMAGES_URL ?>page_editor.png">
                            </div>
                            <div class="gdgallery_example">
                                <h3> <?= _e('PHP Shortcode', 'gdgallery'); ?></h3>
                                <p> <?= _e('Paste the PHP Shortcode into your template file', 'gdgallery'); ?></p>
                                <div class="gdgallery_highlighted">
                                    <span id="gdgallery_php_code">
                                    &lt;?php <br>
                                    echo do_shortcode('[gdgallery_gallery id_gallery="<?= $id ?>"]'); <br>
                                    ?&gt;
                                    </span>
                                    <a href="#" onclick="copyToClipboard('gdgallery_php_code')"
                                       class="copy_shortcode" title="<?= _e('Copy PHP script', 'gdgallery'); ?>"><i
                                                class="fa fa-files-o" aria-hidden="true"></i></a>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="gdgallery_items_section">
                <?php if (!empty($items)) { ?>
                    <p class="gdgallery_select_all_items">
                        <label for="gdgallery_select_all_items"> <?= _e('Select All', 'gdgallery'); ?></label> <input
                                type="checkbox"
                                id="gdgallery_select_all_items"
                                name="select_all_items"/>
                    </p>
                    <a href="#"
                       class="gdgallery_remove_selected_images">  <?= _e('Remove selected items', 'gdgallery'); ?> <i
                                class="fa fa-times"
                                aria-hidden="true"></i></a>
                    <a href="#" class="gdgallery_edit_gallery_images">  <?= _e('Edit Images Info', 'gdgallery'); ?> <i
                                class="fa fa-pencil"
                                aria-hidden="true"></i></a>
                <?php } ?>
                <div class="gdgallery_clearfix"></div>
                <div class="gdgallery_add_new gdgallery_add_new_image" id="_unique_name_button">
                    <div class="gdgallery_add_new_plus"></div>
                    <p>  <?= _e('NEW IMAGE', 'gdgallery'); ?></p>
                </div>
                <div class="gdgallery_add_new gdgallery_add_new_video">
                    <div class="gdgallery_add_new_plus"></div>
                    <p> <?= _e('NEW VIDEO', 'gdgallery'); ?></p>
                </div>

                <ul class="gdgallery_items_list">
                    <li class="empty_space">

                    </li>
                    <?php
                    if (!empty($items)) {
                        foreach ($items as $item):
                            $icon = ($item->type == "youtube") ? "fa-youtube-play" : (($item->type == "vimeo") ? "fa-vimeo" : "fa-picture-o");
                            ?>
                            <li class="gdgallery_item" style="background-image: url('<?= $item->url ?>');">
                                <input type="hidden"
                                       name="gdgallery_ordering[<?= $item->id_image ?>]"
                                       value="<?= $item->ordering ?>">

                                <p class="gdgallery_item_title"><?= $item->name ?>
                                    <i class="fa <?= $icon ?>" aria-hidden="true"></i></p>
                                <div class="gdgallery_item_overlay">
                                    <input type="checkbox" name="items[]"
                                           value="<?= $item->id_image; ?>" class="items_checkbox"/>
                                    <div class="gdgallery_item_edit">
                                        <a href="<?php echo ($item->id_post != 0) ? admin_url() . "post.php?post=" . $item->id_post . "&action=edit&image-editor" : "#"; ?>"
                                           target="_blank" data-post-id="<?= $item->id_post ?>"
                                           data-image-id="<?= $item->id_image ?>"> <?= _e('EDIT', 'gdgallery'); ?></a>
                                    </div>
                                </div>
                            </li>
                        <?php endforeach;
                    } else {
                        echo "No items in this gallery";
                    } ?>
                </ul>
            </div>
        </div>
</form>
<?php \GDGallery\Helpers\View::render('admin/add-video.php', array('id_gallery' => $id, "save_data_nonce" => $save_data_nonce)); ?>
<?php \GDGallery\Helpers\View::render('admin/edit-images.php', array('items' => $items, 'id_gallery' => $id, "save_data_nonce" => $save_data_nonce)); ?>



