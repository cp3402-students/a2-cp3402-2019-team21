<div class="TWD_global_options">	
	<h2 class="nav-tab-wrapper">
		<a class="nav-tab" href="edit.php?post_type=contact&page=cont_option&tab=cont_option">Global options</a>
		<a class="nav-tab" href="edit.php?post_type=contact&page=cont_option&tab=mess_option">Message Options</a>
		<a class="nav-tab nav-tab-active" href="edit.php?post_type=contact&page=cont_option&tab=custom_css">Custom CSS</a>
	</h2>
	<?php
	$twd_custom_css = get_option("twd_custom_css");
	$custom_css = "";
	if(isset($twd_custom_css)){
		$custom_css = $twd_custom_css;
	}

	?>
	<form action="options.php" method="post" id="adminForm" name="adminForm" class="form-table">
		<?php settings_fields('custom_css'); ?>
		<?php do_settings_sections('custom_css'); ?>
		<table style="min-width:500px" class="paramlist" cellspacing="1">
			<tr>
				<th scope="row">Custom CSS</th>
				<td class="paramlist_value">
					   <textarea type="text" rows="15" cols="45" class="" id="twd_custom_css[custom_css]" name="twd_custom_css"><?php echo $custom_css;?></textarea>
					   <p class="paramlist_descriptions"><?php echo 'Custom CSS lets you to write your own additional CSS styles.<br><br> After making changes on this area, make sure to open the active theme of your catalog and click <b>Update.</b>'; ?></p>
				</td>
			</tr>
		</table>
		<?php submit_button(); ?>
	</form>
</div>