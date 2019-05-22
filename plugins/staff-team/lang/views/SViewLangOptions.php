<?php
$countries = $this->getCountries();
?>
<div class="wrap">
    <h2><?php echo 'Available translations' ?> 
    </h2>
    <?php if (isset($_POST['lang_err_mess'])): ?>
        <div class="error"><p><?php echo $_POST['lang_err_mess']; ?></p></div>
    <?php elseif (isset($_POST['lang_success'])): ?>
        <div class="updated"><p><?php echo $_POST['lang_success']['filename'] . ' file for '. $_POST['lang_success']['lang'] ." language was successfully created."; ?></p></div>
    <?php endif; ?>
    <div class="created_lands">
        <table class="wp-list-table widefat fixed striped posts">
            <?php if (is_array($translations))
                foreach ($translations as $key => $value):
                    ?>
                    <tr>
                        <th scope="row">
                            <a href="edit.php?post_type=<?php echo $this->SL_langForPostType; ?>&page=<?php echo $this->SL_langForPostType; ?>_lang_option&lang-slug=<?php echo $key; ?>" class="lang_link"><?php echo $value; ?></a><br>
                        </th>
                        <td>
                            <a href="edit.php?post_type=<?php echo $this->SL_langForPostType; ?>&page=<?php echo $this->SL_langForPostType; ?>_lang_option&lang-slug=<?php echo $key; ?>" >Edit</a>
                        </td>
                        <td>
                            <form action='' method="POST">
                                <input type="hidden" name="task" value="lang-delete" />
                                <input type="hidden" name="lang-slug" value="<?php echo $key; ?>" />
                                <button type="submit" class="button button-small" >Delete</button>
                            </form>
                            </td>
                    <?php endforeach; ?>
        </table>
    </div>
    <hr>
    <h2><?php echo 'Add new translation' ?> 
    </h2>
    <form action="" method="post" class="wp-core-ui" id="msginit">
        <p>
            <select name="common-locale">
                <option value="">
                    <?php echo 'Select language' ?> 
                </option><?php
                    foreach ($countries['locales'] as $code => $name):
                        foreach ($name as $key => $value):
                            $country_key = $code;
                            if ($key != '')
                                $country_key .='_' . $key;
                            ?> 
                        <option value="<?php echo $country_key ?>">
                            <?php echo $value; ?> 
                        </option><?php
                        endforeach;
                    endforeach;
                    ?> 
            </select>
        </p>
        <p class="submit">
            <input type="submit" value="<?php echo 'Add' ?>" class="button button-primary button-large"/>
        </p>
    </form>
</div> 