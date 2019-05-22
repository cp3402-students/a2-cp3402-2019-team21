<style>
div.twd_delParam{
    background: url('<?php echo SC_URL."/images/param_del.png";?>');
    background-size: contain;
}
div.twd_addParam{
    background: url('<?php echo SC_URL."/images/param_plus.png";?>');
    background-size: contain;
}
.twd_contParams .parameter {
    width:80% !important;
}
</style>
<h3 class="TWDSMetaBoxTitle"><?php echo 'Email'; ?></h3>
<span class="TWDSMetaBoxDesc"><?php echo "Enable this setting to send message from single contact page to this email address."; ?></span>
<table class="twd_contParamsEmails">
    <tr>
        <td class="key"> <?php echo 'Email';?>: </td>
        <td> <input  class="text_area" type="email" name="email" id="email" size="50" maxlength="250" value="<?php echo isset($email)?$email:''; ?>" /> </td>
    </tr>
	
    <tr>
        <td class="key"> <?php echo 'Send message';?>: </td>
        <td>
          <?php
          $check0 = ""; $check1 = ""; $check2 = "";
          if ($want_email === '0'){
            $check0 = ' checked="checked" ';
          }
          elseif($want_email === '1'){
            $check1 = ' checked="checked" ';
          }
          elseif($want_email === '2'){
            $check2 = ' checked="checked" ';
          }else{
            $check2 = ' checked="checked" ';
          }
          ?>
            <input type="radio" name="want_email" id="want_email0" value="0"  <?php echo isset($check0)?$check0:''; ?> />
				<label for="want_email0"><?php echo 'Save in database';?></label>
            <input type="radio" name="want_email" id="want_email1" value="1" <?php echo isset($check1)?$check1:''; ?>   />
				<label for="want_email1"><?php echo 'Save in database and send email';?></label>
          <input type="radio" name="want_email" id="want_email2" value="2" <?php echo isset($check2)?$check2:''; ?>   />
        <label for="want_email2"><?php echo 'Only send email';?></label>
        </td>
    </tr>
</table>
<br/>

<h3 class="TWDSMetaBoxTitle"><?php echo 'Category'; ?></h3>
<span class="TWDSMetaBoxDesc"><?php echo "After selecting a category its parameters will display here."; ?></span>
<table class="twd_contParams">
    <?php 
	$post_id = $post->ID;
	$TWDCheckSelectCateg = get_the_terms($post_id, 'cont_category');
	if($TWDCheckSelectCateg==false || $params==""){ 
		echo "<span id='notselyet'>Please select a category from right column of the editor.</span>";
	} 
	else {
		if(is_array($params))foreach ($params as $key => $value):?>
		<tr>
		   <td class="key"><?php echo $key?>:</td>
		   <td class="td_params">
				<?php foreach ($value as $item):?>
				<br/><input type="text" class="parameter" name="param[<?php echo $key?>][]" value="<?php echo $item?>"/>
				<div class="twd_delParam"></div>
				<?php endforeach;?>
		   </td>
		</tr>
		<tr>
			<td> </td>
			<td class="td_param_plus">
				<div class="twd_addParam"></div>
			</td>
		</tr>
		<?php 
		endforeach;
	} ?>
</table>
<br/>

<h3 class="TWDSMetaBoxTitle"><?php echo 'Custom url'; ?></h3>
<span class="TWDSMetaBoxDesc"><?php echo "If enabled, team member title link will redirect the user to the specified page."; ?></span>
<table class="twd_contParamsUrl">
	<tr>
		<td class="key"> <?php echo 'Custom url';?>: </td>
		<td>
			<?php
			$check0 = "";
			$check1 = "";
			if ($want_url == 0)
				$check0 = ' checked="checked" ';
			if ($want_url == 1)
				$check1 = ' checked="checked" ';
			?>
			<input type="radio" name="want_url" id="custom_url" value="0"  <?php echo isset($check0)?$check0:''; ?> />
			<label for="custom_url"><?php echo 'No';?></label>
			<input type="radio" name="want_url" id="default_url" value="1" <?php echo isset($check1)?$check1:''; ?>   />
			<label for="default_url"><?php echo 'Yes';?></label>
		</td>
	</tr>
	<tr id="team_url_tr">
		<td width="100" align="right" class="key"> <?php echo 'Url';?>: </td>
		<td> <input  class="text_area" type="url" name="team_url" id="team_url" size="50" maxlength="250" value="<?php echo isset($team_url)?$team_url:''; ?>" /> </td>
	</tr>
</table>
