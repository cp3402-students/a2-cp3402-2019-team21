<?php
/**
 * @var $fieldId string
 * @var $field array
 */
$type = isset($field['type']) ? $field['type'] : 'text';
$value = isset($field['value']) ? $field['value'] : GDGallery()->settings->getOption($fieldId);

$class = "";
if (isset($field['html_class']) && !empty($field['html_class'])) {
    $class = implode(" ", $field["html_class"]);
}

?>
<div class="settings-field <?= $class ?>" id="<?php echo $fieldId ?>">
    <?php
    \GDGallery\Helpers\View::render('admin/settings/field-' . $type . '.view.php', compact('fieldId', 'field', 'value'));
    ?>
</div>

