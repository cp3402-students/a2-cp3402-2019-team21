<?php
/**
 * @var $tabs array()
 * @var $sections array()
 * @var $fields array()
 * @var $title string
 */
?>

<div id="<?= $id ?>">
    <?php
    foreach ($sections as $key => $section):
        if ($id == $section["tab"]):
            \GDGallery\Helpers\View::render('admin/settings/section.view.php', compact('section', 'key', 'fields'));
        endif;
    endforeach; ?>
</div>


