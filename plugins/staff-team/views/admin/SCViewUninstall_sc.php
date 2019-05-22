
<?php function sc_uninstall(){?>
  <?php $prefix = "sc_"?>
  <form class="sc_form" method="post" action="admin.php?page=uninstall_plugin" style="width:99%;">
    <?php wp_nonce_field( 'uninstall_sc', 'sc_nonce' ); ?>
    <div class="wrap">
      <span class="uninstall_icon"></span>
      <h2><?php echo __('Uninstall Team WD', 'sc_back'); ?></h2>
      <p>
        <?php echo __('Deactivating Team WD plugin does not remove any data that may have been created. To completely remove this plugin, you can uninstall it here.', 'sc_back'); ?>
      </p>
      <p style="color: red;">
        <strong><?php echo __('WARNING:', 'sc_back'); ?></strong>
        <?php echo __("Once uninstalled, this can't be undone. You should use a Database Backup plugin of WordPress to back up all the data first.", 'sc_back'); ?>
      </p>
      <p style="color: red">
        <strong><?php echo __('The following Database Tables will be deleted:', 'sc_back'); ?></strong>
      </p>
      <table class="widefat">
        <thead>
        <tr>
          <th><?php echo __('Database Tables', 'sc_back'); ?></th>
        </tr>
        </thead>
        <tr>
          <td valign="top">
            <ol>
              <li><?php echo $prefix; ?>category</li>
              <li><?php echo $prefix; ?>messages</li>
              <li><?php echo $prefix; ?>options</li>
            </ol>
          </td>
        </tr>
      </table>
      <p style="text-align: center;">
        <?php echo __('Do you really want to uninstall Team WD?', 'sc_back'); ?>
      </p>
      <p style="text-align: center;">
        <input type="checkbox" name="Staff Directory" id="check_yes" value="yes" />&nbsp;<label for="check_yes"><?php echo __('Yes', 'sc_back'); ?></label>
      </p>
      <p style="text-align: center;">
        <input type="submit" name="sc_uninstall" value="UNINSTALL" class="button-primary" onclick="if (check_yes.checked) {
            if (confirm('<?php echo addslashes(__('You are About to Uninstall Team WD from WordPress. This Action Is Not Reversible.', 'sc_back')); ?>')) {
            //spider_set_input_value('task', 'uninstall');
            } else {
            return false;
            }
            }
            else {
            return false;
            }" />
      </p>
    </div>
    <input id="task" name="task" type="hidden" value="" />
  </form>

<?php }?>

<?php function sc_uninstall_success(){?>
  <?php $prefix = "sc_";
  $deactivate_url = wp_nonce_url('plugins.php?action=deactivate&amp;plugin=photo-gallery/photo-gallery.php', 'deactivate-plugin_photo-gallery/photo-gallery.php');
  ?>
  <div id="message" class="updated fade">
    <p><?php echo __('The following Database Tables successfully deleted:', 'bwg_back'); ?></p>
    <p><?php echo $prefix; ?>category</p>
    <p><?php echo $prefix; ?>messages</p>
    <p><?php echo $prefix; ?>styles_and_colors</p>
    <p><?php echo $prefix; ?>options</p>
  </div>
  <div class="wrap">
    <h2><?php echo __('Uninstall Team WD', 'bwg_back'); ?></h2>
    <!--<p><strong><a href="?page=uninstall_plugin&deactivate_plugin=true"><?php /*echo __('Click Here', 'bwg_back'); */?></a> <?php /*echo __('To Finish the Uninstallation and Team WD will be Deactivated Automatically.', 'bwg_back'); */?></strong></p>-->
    <p><strong><a href="#" class="twd_deactivate_link" data-uninstall="1"><?php _e("Click Here","twd"); ?></a><?php _e(" To Finish the Uninstallation and Team WD will be Deactivated Automatically.","twd"); ?></strong></p>

    <input id="task" name="task" type="hidden" value="" />
  </div>


<?php }?>

