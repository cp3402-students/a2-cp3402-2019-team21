<?php foreach ($res as $value): ?>
    <tr>
        <td width="100" align="right"><?php echo $value ?>:</td>
        <td class="td_params">
            <input type="text" class="parameter" name="param[<?php echo $value ?>][]" value=""/>
            <div class="del"></div>
        </td>
    </tr>
    <tr>
        <td> </td>
        <td class="td_param_plus">
            <div class="param_plus"></div>
        </td>
    </tr>
<?php endforeach; ?>

