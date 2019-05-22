<?php
	$css = (isset($others['css']) ? ($others['css'] ? $others['css'] : null) : null);
?>
<div class="tab-content">
	<div class='field-holder'>
		<div class="label-holder">
			<label for=""><?php _e('Custom CSS', 'tlp-food-menu'); ?></label>
		</div>
		<div class="field">
			<textarea name="others[css]" cols="40" rows="10"><?php echo $css; ?></textarea>
		</div>
	</div>

</div>
