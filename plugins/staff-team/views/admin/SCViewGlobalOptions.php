<div class="TWD_global_options">
	<h2 class="nav-tab-wrapper">
		<a class="nav-tab nav-tab-active" href="edit.php?post_type=contact&page=cont_option&tab=cont_option">Global options</a>
		<a class="nav-tab " href="edit.php?post_type=contact&page=cont_option&tab=mess_option">Message Options</a>
		<a class="nav-tab " href="edit.php?post_type=contact&page=cont_option&tab=custom_css">Custom CSS</a>
	</h2>
	<form action="options.php" method="post" name="adminForm" id="adminForm" class="form-table">
		<?php settings_fields('cont_option'); ?>
		<?php do_settings_sections('cont_option'); ?>
		<table style="min-width:500px">
			<tbody>
				<tr>
					<?php
					$check = "";
					if (esc_attr(get_option('choose_category')) == 1){
						$check = ' checked="checked" ';
					}
					?>
					<th scope="row"><?php echo 'Search Contacts by category';?>:</th>
					<td class="paramlist_value">
						<input type="radio" name="choose_category" id="paramsenable_review0" value="0"  <?php if($check==""):?> checked="checked" <?php endif;?> />
							<label for="paramsenable_review0"><?php echo 'Off';?></label>
						<input type="radio" name="choose_category" id="paramsenable_review1" value="1" <?php echo $check; ?>   />
							<label for="paramsenable_review1"><?php echo 'On';?></label>
						<p class="paramlist_descriptions"><?php echo 'If enabled, this setting adds a search bar to contacts pages. Switch this option on to quickly filter the contacts based on selected category.'; ?></p>	
					</td>
				</tr>
				<tr>
					<?php
					$check = ' checked="checked" ';
					if (esc_attr(get_option('name_search')) != 1){
						$check = '';
					}
					?>
					<th scope="row"><?php echo 'Search Contacts by title';?>:</th>
					<td class="paramlist_value">
						<input type="radio" name="name_search" id="paramsname_search0" value="0"  <?php if($check==""):?> checked="checked" <?php endif;?> />
							<label for="paramsname_search0"><?php echo 'Off';?></label>
						<input type="radio" name="name_search" id="paramsname_search1" value="1" <?php echo $check; ?>   />
							<label for="paramsname_search1"><?php echo 'On';?></label>
						<p class="paramlist_descriptions"><?php echo 'Choose whether to search contacts by name in views or not.'; ?></p>	
						<p class="paramlist_descriptions"><?php echo 'If enabled, this setting adds a search bar to contacts pages. Visitors can search contacts by their names using this input.'; ?></p>	
					</td>
				</tr>
				<tr>
					<?php
					$check = ' checked="checked" ';
					if (esc_attr(get_option('lightbox')) != 1){
						$check = '';
					}
					?>
					<th scope="row"><?php echo 'Image View in Lightbox ';?>:</th>
					<td class="paramlist_value">
						<input type="radio" name="lightbox" id="paramsname_search3" value="0"  <?php if($check==""):?> checked="checked" <?php endif;?> />
							<label for="paramsname_search3"><?php echo 'Off';?></label>
						<input type="radio" name="lightbox" id="paramsname_search4" value="1" <?php echo $check; ?>   />
							<label for="paramsname_search4"><?php echo 'On';?></label>
						<p class="paramlist_descriptions"><?php echo 'Activate this setting to have large versions of product images displayed in a lightbox upon clicking on them.'; ?></p>		
					</td>
				</tr>
				<tr>
					<th scope="row"><?php echo 'Slug ';?>:</th>
					<td>
						<input type="text" name="team_slug" value="<?php echo get_option('team_slug');?>">
					</td>
				</tr>
			</tbody>
		</table>
		<?php
			$delete_demo_data = get_option('delete_demo_data');
			if($delete_demo_data && isset($delete_demo_data)){
				echo'
					 <div class="d_demo_data">
						<input type="button" name="delete_demo_data" class="delete_demo_data button button-default" value="Delete demo data">
						<img style="display: none;" class="demo_loader" src="'. SC_URL.'/images/loader.gif">
					 </div>
						';
			}
		?>

		<?php submit_button(); ?>
	</form>
</div>	