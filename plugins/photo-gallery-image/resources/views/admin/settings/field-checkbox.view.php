<?php
/**
 * @var $fieldId string
 * @var $value string
 * @var $field
 */

$placeholder = isset($field['placeholder'])
    ? 'placeholder=' . $field['placeholder']
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
    <input type="hidden" name="settings[<?php echo $fieldId; ?>]" <?= $class ?> value="false"/>

    <span class="switch">
        <input type="checkbox" name="settings[<?php echo $fieldId; ?>]" value="true" <?php checked(true, $value); ?> />
        <span class="slider round"></span>
    </span>

</label>
