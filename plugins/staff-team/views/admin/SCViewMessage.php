<form action="" method="post" name="adminForm" class="messtable">
    <legend class="mess_leg"><?php echo 'A Message from';?> <?php echo isset($sender )?$sender :'';?> </legend>
    <table>
        <tr>
            <td width="40%" class="paramlist_key"><span class="editlinktip">&nbsp;</span></td>
            <td class="paramlist_value"><b><?php echo 'Sender Details';?></b></td>
        </tr>
        <tr>
            <td width="100" align="right" class="key">
                <?php echo 'Sender Phone';?>:
            </td>
            <td>
                <?php  echo isset($sender_phone )?$sender_phone :'';?>
            </td>
        </tr>
        <tr>
            <td width="100" align="right" class="key">
                <?php echo 'Sender Email';?>:
            </td>
            <td>
                <?php echo isset($sender_mail)?$sender_mail:''; ?>
            </td>
        </tr>
        <tr>
            <td width="100" align="right" class="key">
                <?php echo 'Sender Contact Preference';?>:
            </td>
            <td>
                <?php echo isset($sender_cont_pref)?$sender_cont_pref:''; ?>
            </td>
        </tr>
    </table>
    <br>
    <table>
        <tr>
            <td width="40%" class="paramlist_key"><span class="editlinktip">&nbsp;</span></td>
            <td class="paramlist_value"><b><?php echo 'Message Details';?></b></td>
        </tr>
        <tr>
            <td width="100" align="right" class="key">
                <?php echo 'To Contact';?>:
            </td>
            <td>
                <?php echo isset($name)?$name:''; ?> <?php if(isset($category)):?>(<?php echo 'Category';?> : <?php echo $category; ?>) <?php endif;?>
            </td>
        </tr>
        <tr>
            <td width="100" align="right" class="key">
                <?php echo 'Date';?>:
            </td>
            <td>
                <?php echo isset($date)?$date:''; ?>
            </td>
        </tr>
        <tr>
            <td width="100" align="right" class="key">
                <?php echo 'Title';?>:
            </td>
            <td>
                <?php echo isset($title)?$title:''; ?>
            </td>
        </tr>
        <tr>
            <td width="100" align="right" class="key">
                <?php echo 'Message';?>:
            </td>
            <td>
               <?php echo isset($text)?$text:''; ?>
            </td>
        </tr>
    </table>
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="controller" value="messages" />
</form>