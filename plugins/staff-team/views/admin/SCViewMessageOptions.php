<div class="TWD_global_options">
	<h2 class="nav-tab-wrapper">
		<a class="nav-tab" href="edit.php?post_type=contact&page=cont_option&tab=cont_option">Global options</a>
		<a class="nav-tab nav-tab-active" href="edit.php?post_type=contact&page=cont_option&tab=mess_option">Message Options</a>
		<a class="nav-tab " href="edit.php?post_type=contact&page=cont_option&tab=custom_css">Custom CSS</a>
	</h2>

	<form action="options.php" method="post" id="adminForm" name="adminForm" class="form-table">
		<?php settings_fields('mess_option'); ?>
		<?php do_settings_sections('mess_option'); ?>
		<table style="min-width:500px" class="paramlist" cellspacing="1">
			<tr>
				<?php
				$check = "";
					if (esc_attr(get_option('enable_message')) == 1){
						$check = ' checked="checked" ';
					}
				?>
				<th scope="row"><?php echo " Send Message";?></th>
				<td class="paramlist_value">
					<input type="radio" name="enable_message" id="enable_message0" value="0"  <?php if($check==""):?> checked="checked" <?php endif;?>/>
						<label for="enable_message0"><?php echo 'Off';?></label>
					<input type="radio" name="enable_message" id="enable_message1" value="1" <?php echo $check; ?>   />
						<label for="enable_message1"><?php echo 'On';?></label>
					<p class="paramlist_descriptions"><?php echo 'Choose whether to display the Contact form in the single page or not.'; ?></p>		
				</td>
			</tr>
			<tr>
				<?php
				$check = "";
					if (esc_attr(get_option('show_name')) == 1){
						$check = ' checked="checked" ';
					}
				?>
				<th scope="row"><?php echo '"Name" Field'; ?></th>
				<td class="paramlist_value">
					<input type="radio" name="show_name" id="show_name0" value="0"  <?php if($check==""):?> checked="checked" <?php endif;?> />
						<label for="show_name0"><?php echo 'Off';?></label>
					<input type="radio" name="show_name" id="show_name1" value="1" <?php echo $check; ?>   />
						<label for="show_name1"><?php echo 'On';?></label>
					<p class="paramlist_descriptions"><?php echo 'Choose whether to display the field of "Name" in contact form of the single page or not.'; ?></p>			
				</td>
			</tr>
			<tr>
				<?php
				$check = "";
					if (esc_attr(get_option('show_phone')) == 1){
						$check = ' checked="checked" ';
					}
				?>
				<th scope="row"><?php echo '"Phone" Field'; ?></th>
				<td class="paramlist_value">
					<input type="radio" name="show_phone" id="show_phone0" value="0"  <?php if($check==""):?> checked="checked" <?php endif;?> />
						<label for="show_phone0"><?php echo 'Off';?></label>
					<input type="radio" name="show_phone" id="show_phone1" value="1" <?php echo $check; ?>   />
						<label for="show_phone1"><?php echo 'On';?></label>
					<p class="paramlist_descriptions"><?php echo 'Choose whether to display the field of "Phone" in contact form of the single page or not.'; ?></p>	
				</td>
			</tr>
			<tr>
				<?php
				$check = "";
					if (esc_attr(get_option('show_email')) == 1){
						$check = ' checked="checked" ';
					}
				?>
				<th scope="row"><?php echo '"Email" Field'; ?></th>
				<td class="paramlist_value">
					<input type="radio" name="show_email" id="show_email0" value="0"  <?php if($check==""):?> checked="checked" <?php endif;?> />
						<label for="show_email0"><?php echo 'Off';?></label>
					<input type="radio" name="show_email" id="show_email1" value="1" <?php echo $check; ?>   />
						<label for="show_email1"><?php echo 'On';?></label>
					<p class="paramlist_descriptions"><?php echo 'Choose whether to display the field of "Email" in contact form of the single page or not.'; ?></p>	
				</td>
			</tr>
			<tr>
				<?php
				$check = "";
					if (esc_attr(get_option('show_cont_pref')) == 1){
						$check = ' checked="checked" ';
					}
				?>
				<th scope="row"><?php echo '"Contact Preferences" Field';?></th>
				<td class="paramlist_value">
					<input type="radio" name="show_cont_pref" id="show_cont_pref0" value="0"  <?php if($check==""):?> checked="checked" <?php endif;?> />
						<label for="show_cont_pref0"><?php echo 'Off';?></label>
					<input type="radio" name="show_cont_pref" id="show_cont_pref1" value="1" <?php echo $check; ?>   />
						<label for="show_cont_pref1"><?php echo 'On';?></label>
					<p class="paramlist_descriptions"><?php echo 'Choose whether to display the field of "Contact Preferences" in contact form of the single page or not.'; ?></p>	
				</td>
			</tr>
			<tr>
				<?php
				$captcha_value = get_option('twd_captcha');
				if ($captcha_value === false){
					$captcha_value = '1';
				}
				$gcaptcha_key_container_style = ($captcha_value == '2') ? '' : 'style="display:none;"';
				?>
				<th scope="row"><?php echo 'Captcha'; ?></th>
				<td class="paramlist_value twd_captcha_option">
					<input type="radio" name="twd_captcha" id="no_captcha" <?php checked($captcha_value, '0'); ?> value="0"/>
					<label for="no_captcha"><?php echo 'Off';?></label>
					<input type="radio" name="twd_captcha" id="default_captcha" <?php checked($captcha_value, '1'); ?> value="1" />
					<label for="default_captcha"><?php echo 'On';?></label>
					<input type="radio" name="twd_captcha" id="google_captcha" <?php checked($captcha_value, '2'); ?> value="2" />
					<label for="google_captcha"><?php echo 'Google reCaptcha';?></label>
				</td>
			</tr>
			<tr class="gcaptcha_key_container" <?php echo $gcaptcha_key_container_style; ?>>
				<?php
				$twd_gcaptcha_key = get_option('twd_gcaptcha_key');
				if ($twd_gcaptcha_key === false){
					$twd_gcaptcha_key = '';
				}
				?>
				<th scope="row"><?php echo 'Google Captcha key'; ?></th>
				<td class="paramlist_value">
					<input type="text" name="twd_gcaptcha_key" id="twd_gcaptcha_key" value="<?php echo $twd_gcaptcha_key; ?>"/>
				</td>
			</tr>
			<tr class="gcaptcha_key_container" <?php echo $gcaptcha_key_container_style ?>>
				<?php
				$twd_gcaptcha_private_key = get_option('twd_gcaptcha_private_key');
				if ($twd_gcaptcha_private_key === false){
					$twd_gcaptcha_private_key = '';
				}
				?>
				<th scope="row"><?php echo 'Google Captcha private key'; ?></th>
				<td class="paramlist_value">
					<input type="text" name="twd_gcaptcha_private_key" id="twd_gcaptcha_private_key" value="<?php echo $twd_gcaptcha_private_key; ?>"/>
				</td>
			</tr>
            <tr>
              <?php
              $twd_pp_text = get_option('twd_pp_text');
              if ($twd_pp_text === false){
                $twd_pp_text = "";
              }

              ?>
                <th scope="row"><?php echo 'GDPR compliance checkbox text'; ?></th>
                <td class="paramlist_value">
                    <textarea cols="55" rows="7" name="twd_pp_text" id="twd_pp_text"><?php echo $twd_pp_text; ?></textarea>
                    <p class="paramlist_descriptions">Explain that you process private data submitted with this form according to your Privacy Policy. Depending on legislation, you may need to ask user's consent before obtaining their private data. If you leave this text empty, confirmation checkbox will not appear. </p>
                </td>
            </tr>
		</table>
		<?php submit_button(); ?>
	</form>
</div>

<script>
	jQuery('.twd_captcha_option input').on('change',function (event) {
		if(jQuery(this).attr("value") == '2'){
			jQuery('.gcaptcha_key_container').show();
		}else{
			jQuery('.gcaptcha_key_container').hide();
		}
	})
</script>
