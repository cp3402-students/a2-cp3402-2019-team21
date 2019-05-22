<?php
$new_gallery_link = admin_url('admin.php?page=gdgallery&task=create_new_gallery');

$new_gallery_link = wp_nonce_url($new_gallery_link, 'gdgallery_create_new_gallery');
?>
<tr>
    <td colspan="5"><?php _e('No Galleries Found.', GDGALLERY_TEXT_DOMAIN); ?> <a
                href="<?php echo $new_gallery_link; ?>"><?php _e('Add New', GDGALLERY_TEXT_DOMAIN); ?></a></td>
</tr>