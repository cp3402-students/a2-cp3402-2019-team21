<?php global $TLPfoodmenu;

?>
<div class="wrap">
	<div id="upf-icon-edit-pages" class="icon32 icon32-posts-page"><br/></div>
	<h2><?php _e( 'Shortcode generator', 'tlp-food-menu' ); ?></h2>
	<div class="tlp-content-holder">
		<div class="tch-left">
			<div class="postbox" id="scg-wrapper">
				<h3 class="hndle ui-sortable-handle"><span>Shortcode</span></h3>
				<div class="inside">
					<h4>Layout and filter</h4>
					<div class="scg-wrapper">
						<div class="scg-item-wrap">
							<div class="scg-label"><label>Column</label></div>
							<div class="scg-field">
								<select name="col">
									<option value="">Default</option>
									<?php
									$cols = $TLPfoodmenu->scColumns();
									foreach ( $cols as $key => $col ) {
										echo "<option value={$key}>$col</option>";
									}
									?>
								</select>
							</div>
						</div>
						<div class="scg-item-wrap">
							<div class="scg-label"><label>Order by</label></div>
							<div class="scg-field">
								<select name="orderby">
									<option value="">Default</option>
									<?php
									$obs = $TLPfoodmenu->scOrderBy();
									foreach ( $obs as $key => $ob ) {
										echo "<option value={$key}>$ob</option>";
									}
									?>
								</select>
							</div>
						</div>
						<div class="scg-item-wrap">
							<div class="scg-label"><label>Order</label></div>
							<div class="scg-field">
								<select name="order">
									<option value="">Default</option>
									<?php
									$orders = $TLPfoodmenu->scOrder();
									foreach ( $orders as $key => $order ) {
										echo "<option value={$key}>$order</option>";
									}
									?>
								</select>
							</div>
						</div>
						<div class="scg-item-wrap">
							<div class="scg-label"><label>Category</label></div>
							<div class="scg-field">
								<div class="checkbox-group">
									<?php
									$cat_lists = $TLPfoodmenu->getAllFmpCategoryList();
									if(!empty($cat_lists)){
										foreach ($cat_lists as $catId => $cat){
											echo "<label for='cat-{$catId}'><input name='cat' id='cat-{$catId}' type='checkbox' value='{$catId}' >{$cat}</label>";
										}
									}
									?>
									<p class="description">Leave it blank to display all.</p>
								</div>
							</div>
						</div>
						<div class="scg-item-wrap">
							<div class="scg-field">
								<label for="hide-img"><input type="checkbox" name="hide-img" value="1" id="hide-img"> Hide Image</label>
							</div>
						</div>
						<div class="scg-item-wrap">
							<div class="scg-field">
								<label for="disable-link"><input type="checkbox" name="disable-link" value="1" id="disable-link"> Disable link</label>
							</div>
						</div>
                        <div class="scg-item-wrap">
                            <div class="scg-label"><label>Wrapper Class</label></div>
                            <div class="scg-field"><input type="text" name="class"/>
                            </div>
                        </div>
					</div>
                    <h4>Style</h4>
                    <div class="scg-wrapper">
                        <div class="scg-item-wrap">
                            <div class="scg-label"><label>Title color</label></div>
                            <div class="scg-field"><input type="text" class="tlp-color" name="title-color"></div>
                            <p class="description">Please press two click for selecting predefine color.</p>
                        </div>
                    </div>
					<div id="sc-output">
						<textarea></textarea>
						<p class="description">Click to copy the shortcode.</p>
					</div>

				</div>
			</div>
            <div class="rt-banner"><a target="_blank" href="https://themeforest.net/item/red-chili-restaurant-wordpress-theme/20166175?ref=RadiusTheme"> <img src="<?php echo $TLPfoodmenu->assetsUrl .'images/site_add_banner.png' ?>" /></a></div>
		</div>
		<div class="tch-right">
			<div id="pro-feature" class="postbox">
				<div class="handlediv" title="Click to toggle"><br></div>
				<h3 class="hndle ui-sortable-handle"><span>TLP Food Menu Pro</span></h3>
				<div class="inside">
					<?php echo $TLPfoodmenu->proFeature() ?>
				</div>
			</div>
		</div>
	</div>

	<div class="tlp-help">
		<p style="font-weight: bold"><?php _e( 'Short Code', 'tlp-food-menu' ); ?> :</p>
		<code>[foodmenu col="3" orderby="menu_order" order="ASC" hide-img="true" cat="31"]</code><br>
		<p><?php _e( 'col = The number of column you want to create (1,2,3,4)', 'tlp-food-menu' ); ?></p>
		<p><?php _e( 'orderby = Orderby (title , date, menu_order)', 'tlp-food-menu'); ?></p>
		<p><?php _e( 'ordr = ASC, DESC', 'tlp-food-menu'); ?></p>
		<p><?php _e( 'cat = 89,28,37', 'tlp-food-menu'); ?></p>
		<p class="tlp-help-link"><a class="button-primary"
		                            href="http://demo.radiustheme.com/wordpress/plugins/food-menu/"
		                            target="_blank"><?php _e( 'Demo', 'tlp-food-menu'); ?></a> <a class="button-primary"
		                                                                                         href="https://radiustheme.com/how-to-setup-configure-tlp-team-free-version-for-wordpress/"
		                                                                                         target="_blank"><?php _e( 'Documentation',
					'tlp-food-menu'); ?></a></p>
	</div>

</div>
