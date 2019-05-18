<?php if (isset($_POST['lang_err_mess'])): ?>
    <div class="error" style="display: inline-block;width: 100%"><p><?php echo $_POST['lang_err_mess']; ?></p></div>
<?php elseif (isset($_POST['lang_success'])): ?>
    <div class="updated" style="display: inline-block;width: 100%"><p><?php echo 'File was successfully updated.'; ?></p></div>
<?php endif; ?>
<?php if (isset($_POST['lang_success_synchron'])): ?>
    <div class="updated" style="display: inline-block;width: 100%"><p><?php echo $_POST['lang_success_synchron']; ?></p></div>
<?php endif; ?>
<div class="wrap">
    <form method="post" style="float: left;">
        <input type="submit" value="<?php echo 'Synchronize with POT' ?>" class="button button-primary button-large"/>
        <h2 style="float: left;padding: 0 10px;"><?php echo $lang; ?></h2>
        <input type="hidden" name="synchron" value="pot_synchron"/>
    </form>
    <form action="" method="post" name="tanslation_form">
        <div class='lang_save'>
            <p class="submit" style="text-align: right">
                <input type="submit" value="<?php echo 'Save' ?>" class="button button-primary button-large"/>
            </p>
        </div>
        <div class="lang_words">
            <table class="wp-list-table widefat fixed striped posts">
                <thead>
                <th scope="col"><?php echo 'Source text'; ?></th>
                <th scope="col"><?php echo $lang . ' translation'; ?></th>
                </thead>
                <?php
                $i = 1;
                foreach ($data as $key => $value):
                    ?>
                    <tr>
                        <th scope="row">
                            <label><?php echo $value[0]; ?></label>
                        <td><input type="text" style="width: 100%" name="data[<?php echo $key; ?>][1]" value="<?php echo htmlspecialchars($value[1]); ?>"/></td>
                    </tr>
                    <?php
                    $i++;
                endforeach;
                ?>
            </table>
            <div class='lang_save'>
                <p class="submit" style="text-align: right">
                    <input type="submit" value="<?php echo 'Save' ?>" class="button button-primary button-large"/>
                </p>
            </div>
        </div>
    </form>
</div>
