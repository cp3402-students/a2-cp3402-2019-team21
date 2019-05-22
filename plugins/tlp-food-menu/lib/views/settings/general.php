<?php
	global $TLPfoodmenu;
	$slug = (isset($general['slug']) ? ($general['slug'] ? sanitize_title_with_dashes($general['slug']) : 'food-menu' ) : 'food-menu');
	$characterLimit = (isset($general['character_limit']) ? ($general['character_limit'] ? intval($general['character_limit']) : 150 ) : 150);
	$currencyS = (isset($general['currency']) ? ($general['currency'] ? $general['currency'] : 'USD') : 'USD');
	$currency_position = (isset($general['currency_position']) ? ($general['currency_position'] ? $general['currency_position'] : 'left') : 'left');
	$hide_image = !empty($general['hide_image']) ? true : false;
    $hide_price = !empty($general['hide_price']) ? true : false;
?>
<div class="tab-content">
	<div class='field-holder'>
		<div class="label-holder">
			<label for=""><?php _e('Slug', 'tlp-food-menu'); ?></label>
		</div>
		<div class="field">
			<input name="general[slug]" type="text" value="<?php echo $slug; ?>" size="15" class="">
			<p class="description"><?php _e('Slug configuration','tlp-food-menu');?></p>
		</div>
	</div>

	<div class='field-holder'>
		<div class="label-holder">
			<label for=""><?php _e('Excerpt (short description) character limit', 'tlp-food-menu'); ?></label>
		</div>
		<div class="field">
			<input name="general[character_limit]" type="number" value="<?php echo $characterLimit; ?>" size="3" class="">
			<p class="description"><?php _e('Set character limit for short description','tlp-food-menu');?></p>
		</div>
	</div>

	<div class='field-holder'>
		<div class="label-holder">
			<label for=""><?php _e('Food Menu item display', 'tlp-food-menu'); ?></label>
		</div>
		<div class="field">
			<select name="general[em_display_col]" id="em_display_col" class="tlpselect">
				<?php
					foreach($TLPfoodmenu->col_lists() as $key => $value ){
						$colS = (isset($general['em_display_col']) ? ($general['em_display_col'] == $key ? 'selected' : null) : null);
						echo "<option value='{$key}'>{$value}</option>";
					}
				?>
			</select>
		</div>
	</div>

    <div class='field-holder'>
        <div class="label-holder">
            <label for=""><?php _e('Hide image at detail page', 'tlp-food-menu'); ?></label>
        </div>
        <div class="field">
            <label for="hide-image"><input type="checkbox" value="1" <?php echo $hide_image ? "checked" : null; ?> name="general[hide_image]" id="hide-image"> Enable</label>
        </div>
    </div>
    <div class='field-holder'>
        <div class="label-holder">
            <label for=""><?php _e('Hide price at detail page', 'tlp-food-menu'); ?></label>
        </div>
        <div class="field">
            <label for="hide-price"><input type="checkbox" value="1" <?php echo $hide_price ? "checked" : null; ?> name="general[hide_price]" id="hide-price"> Enable</label>
        </div>
    </div>

	<div class='field-holder'>
		<div class="label-holder">
			<label for=""><?php _e('Currency', 'tlp-food-menu'); ?></label>
		</div>
		<div class="field">
			<select name="general[currency]" class="tlpselect">
				<option value=""><?php _e('Select one', 'tlp-food-menu'); ?></option>
				<?php
					foreach ($TLPfoodmenu->currency_list() as $key => $currency) {
						$cslt = ($currencyS == $key ? "selected" : null);
						echo "<option value='$key' $cslt>{$currency['name']} ({$currency['symbol']})</option>";
					}
				?>
			</select>
		</div>
	</div>

	<div class='field-holder'>
		<div class="label-holder">
			<label for=""><?php _e('Currency Position', 'tlp-food-menu'); ?></label>
		</div>
		<div class="field">
			<select name="general[currency_position]"  class="tlpselect">
				<?php _e('Select one', 'tlp-food-menu'); ?>
				<?php
					foreach ($TLPfoodmenu->currency_position_list() as $key => $currencyp) {
						$cpslt = ($currency_position == $key ? "selected" : null);
						echo "<option value='$key' $cpslt>{$currencyp}</option>";
					}
				?>
			</select>
		</div>
	</div>
</div>
