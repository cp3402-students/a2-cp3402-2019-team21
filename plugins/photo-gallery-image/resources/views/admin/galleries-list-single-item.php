<?php
/**
 * Template for galleries list single item
 *
 * @var $gallery \GDGallery\Models\Gallery
 */

$GalleryId = $gallery->getId();

$EditUrl = admin_url('admin.php?page=gdgallery&task=edit_gallery&id=' . $GalleryId);

$EditUrl = wp_nonce_url($EditUrl, 'gdgallery_edit_gallery_' . $GalleryId);

$RemoveUrl = admin_url('admin.php?page=gdgallery&task=remove_gallery&id=' . $GalleryId);

$RemoveUrl = wp_nonce_url($RemoveUrl, 'gdgallery_remove_gallery_' . $GalleryId);

$DuplicateUrl = admin_url('admin.php?page=gdgallery&task=duplicate_gallery&id=' . $GalleryId);

$DuplicateUrl = wp_nonce_url($DuplicateUrl, 'gdgallery_duplicate_gallery_' . $GalleryId);


?>
<tr>
    <td class="form-id">
        <?= $gallery->getId(); ?>
    </td>
    <td class="form-name"><a
                href="<?php echo $EditUrl; ?>"><?php echo esc_html(stripslashes($gallery->getName())); ?></a></td>
    <td class="form-fields"><?php echo count($gallery->getItems());; ?></td>
    <td class="form-shortcode">[gdgallery_gallery id_gallery="<?php echo $GalleryId; ?>"]

        <span class="use_another_shortcode">or use php shortcode</span>

        <span class="gdgallery_php_shortcode"> &lt;?php echo do_shortcode('[gdgallery_gallery id_gallery=<?php echo $GalleryId; ?>
            ]'); ?&gt;</span>
    </td>
    <td class="form-actions">
        <a class="gdgallery_edit_form" href="<?php echo $EditUrl; ?>"><i class="gdicon gdicon-setting"
                                                                         aria-hidden="true"></i></a>
        <a class="gdgallery_duplicate_form" href="<?php echo $DuplicateUrl; ?>"><i class="gdicon gdicon-duplicate"
                                                                                   aria-hidden="true"></i></a>
        <a class="gdgallery_delete_form" href="<?php echo $RemoveUrl; ?>"><i class="gdicon gdicon-remove"
                                                                             aria-hidden="true"></i></a>
        <a class="gdgallery_preview_form" target="_blank"
           href="<?php echo \GDGallery\Controllers\Frontend\GalleryPreviewController::previewUrl($gallery->getId(), false); ?>"><i
                    class="gdicon gdicon-eye"
                    aria-hidden="true"></i></a>
    </td>
</tr>

