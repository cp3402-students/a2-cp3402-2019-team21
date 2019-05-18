<?php
    $edit = FALSE;
    if(isset($data))
        $edit =  TRUE;
    $data[] = '';
?>
<style>
<?php if($edit):?>
.param_table .parameter{
    width:80% !important;
}
<?php endif;?>
div.twd_delCatParam{
    background: url('<?php echo SC_URL."/images/param_del.png";?>');
    background-size: contain;
}
div.twd_addCatParam{
    background: url('<?php echo SC_URL."/images/param_plus.png";?>');
    background-size: contain;
}
</style>
<table class="TWDCatParam form-table">
    <tr>
        <?php if(!$edit): ?>
			<td><?php echo 'Parameters';?></td>
        <?php else:?>
			<th scope="row"><?php echo 'Parameters';?></th>
        <?php endif;?>
		<td class="parameters_td">		
            <?php foreach ($data as $key => $item): ?>
				<?php if(!$edit): ?>
					<input type="text" class="parameter" name="param[]" value="<?php echo $item;?>"/>
				<?php else: ?>
					<input type="text" class="parameter" name="param[]" value="<?php echo $item;?>"/>						
					<div class="twd_delCatParam"></div><br/>
				<?php
				endif;  
			endforeach; ?>
        </td>
    </tr>
    <tr>
        <td> </td>
        <td class="td_param_plus">
            <div class="twd_addCatParam"></div>
        </td>
    </tr>
</table>
<p class="paramlist_descriptions"><?php echo 'You can have supplementary characteristics for your products with parameters, e.g. Nationality, Occupation, Date of Birth, Department, etc. <br> Click the plus (+) button to add them.'; ?><br/><br/><br/></p>
