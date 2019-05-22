<?php
/**
 * @var $fieldId string
 * @var $value string
 * @var $field
 */
$multiple = isset($field['multiple'])
    ? 'multiple=multiple'
    : '';

$class = "";
if (isset($field['html_class']) && !empty($field['html_class'])) {
    $class_name = "";
    foreach ($field['html_class'] as $val) {
        $class_name .= $val . " ";
    }
    $class = "class = '" . $class_name . "'";
}
?>

<label class="input-wrap">
    <span class="settings-label"><?php
        echo $field['label'];
        if (isset($field['help'])): ?>
            <span class="settings-field-help">
                <span class="settings-field-help-icon">?</span>
                <span class="settings-field-help-text-wrap">
                    <span class="settings-field-help-text"><?php echo $field['help']; ?></span>
                    <span class="settings-field-help-text-tooltip"></span>
                </span>
            </span>
        <?php endif;
        ?></span>
    <select name="settings[<?php echo $fieldId ?>]" <?php echo $multiple ?>>
        <?php foreach ($field['options'] as $optionKey => $optionName): ?>
            <option value="<?php echo $optionKey ?>" <?php selected($value, $optionKey); ?>><?php echo $optionName; ?></option>
        <?php endforeach; ?>
    </select>
</label>