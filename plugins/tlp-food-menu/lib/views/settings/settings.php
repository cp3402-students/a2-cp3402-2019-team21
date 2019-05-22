<?php
global $TLPfoodmenu;
$settings = get_option( $TLPfoodmenu->options['settings'] );
?>
<div class="wrap">
	<div id="upf-icon-edit-pages" class="icon32 icon32-posts-page"><br/></div>
	<h2><?php _e( 'TLP Food Menu Settings', 'tlp-food-menu' ); ?></h2>
	<div class="settings-wrapper">
		<div class="fmp-settings-left">
			<form id="tlp-settings" onsubmit="tlpFmSettingsUpdate(this); return false;">

                <div class="rt-tab-container">
                    <ul class="rt-tab-nav">
						<li><a href="#general"><?php _e( 'General', 'tlp-food-menu' ); ?></a></li>
						<li><a href="#others"><?php _e( 'Others', 'tlp-food-menu' ); ?></a></li>
					</ul>
					<div id="general" class="rt-tab-content">
						<?php
						$TLPfoodmenu->render( 'settings.general', array( 'general' => isset( $settings['general'] ) ? ( $settings['general'] ? $settings['general'] : array() ) : array() ) );
						?>
					</div>
					<div id="others" class="rt-tab-content">
						<?php
						$TLPfoodmenu->render( 'settings.others', array( 'others' => isset( $settings['others'] ) ? ( $settings['others'] ? $settings['others'] : array() ) : array() ) );
						?>
					</div>
				</div>
				<p class="submit"><input type="submit" name="submit" id="tlpSaveButton" class="button button-primary"
				                         value="<?php _e( 'Save Changes', 'tlp-food-menu' ); ?>"></p>

				<?php wp_nonce_field( $TLPfoodmenu->nonceText(), $TLPfoodmenu->nonceId() ); ?>
			</form>
			<div id="response" class="updated"></div>
            <div class="rt-banner"><a target="_blank" href="https://themeforest.net/item/red-chili-restaurant-wordpress-theme/20166175?ref=RadiusTheme"> <img src="<?php echo $TLPfoodmenu->assetsUrl .'images/site_add_banner.png' ?>" /></a></div>
		</div>
		<div class="fmp-settings-right">
			<div id="pro-feature" class="postbox">
				<div class="handlediv" title="Click to toggle"><br></div><h3 class="hndle ui-sortable-handle"><span>Food Menu PRO</span></h3>
				<div class="inside">
					<?php echo $TLPfoodmenu->proFeature(); ?>
				</div>
			</div>
		</div>
	</div>


	<div class="tlp-help">
		<p style="font-weight: bold"><?php _e( 'Short Code', 'tlp-food-menu' ); ?> :</p>
		<code>[foodmenu]</code><br>
		<code>[foodmenu cat="29,24,30"]</code><br>
		<code>[foodmenu orderby="title" order="ASC"]</code><br>
		<code>[foodmenu cat="29" orderby="title" order="ASC"]</code><br>
		<p><?php _e( 'cat = category id eg. (7,8,95)', 'tlp-food-menu' ); ?></p>
		<p><?php _e( 'orderby = title, date, menu_order', 'tlp-food-menu' ); ?></p>
		<p><?php _e( 'order = ASC, DESC', 'tlp-food-menu' ); ?></p>
		<p class="tlp-help-link"><a class="button-primary"
		                            href="http://demo.radiustheme.com/wordpress/plugins/food-menu/"
		                            target="_blank"><?php _e( 'Demo', 'tlp-food-menu' ); ?></a> <a
				class="button-primary"
				href="https://radiustheme.com/how-to-setup-and-configure-tlp-food-menu-free-version-for-wordpress/"
				target="_blank"><?php _e( 'Documentation', 'tlp-food-menu' ); ?></a></p>

	</div>
</div>