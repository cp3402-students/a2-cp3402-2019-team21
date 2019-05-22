<?php
/**
 * @var $galleries \GDGallery\Models\Gallery[]
 */

?>

<form method="post" action="">
    <h3><?= _e('Select The Gallery', 'gdgallery'); ?></h3>

    <select id="grand_gallery_select">
        <?php
        foreach ($galleries as $gallery) {
            ?>
            <option value="<?php echo $gallery->getId(); ?>"><?php echo $gallery->getName(); ?></option>
            <?php
        }
        ?>
    </select>
    <button class='button primary'
            id='grand_gallery_insert'><?php _e('Insert Gallery', GDGALLERY_TEXT_DOMAIN); ?></button>
</form>
