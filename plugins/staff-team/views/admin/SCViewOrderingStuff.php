<?php
    function drawOrderingTable($contactList){
        global $wpdb;
        $order = get_option('staff_order_contact');
        $contact_category = get_terms('cont_category');
        if($order && isset($order)) {
            if ($contactList && isset($contactList)) {
                $order_list = array();
                foreach ($order as $id) {
                    foreach ($contactList as $contact) {
                        if ($contact->ID == $id) {
                            array_push($order_list, $contact);
                        }
                    }
                }
                $contactList = $order_list;
            }
        }
?>
        <div class="notice_fon"></div>
<div class="save_notice"><span class="success_icon dashicons dashicons-yes"></span><p>Changes Saved</p></div>
<div class="wrap SC_start_ordering">
    <h1>Ordering</h1>
    <select class="select_category">
        <option>Select Category</option>
    <?php
        foreach($contact_category as $category){
            echo"<option value='".$category->name."'>".$category->name."</option>";
        }
    ?>
    </select>
    <input type="button" class="order_id button button-default" value="Order by id">
    <input type="button" class="order_name button button-default" value="Order by name">
    <table class="wp-list-table widefat fixed striped posts">
        <thead>
        <tr>
            <td id="cb" class="manage-column column-cb check-column">
            </td>
            <th>
                <p>
                    <strong><span>ID</span></strong>
                </p>
            </th>
            <th>
                <p>
                    <strong><span>Name</span></strong>
                </p>
            </th>
            <th>
                <p>
                    <strong><span>Category</span></strong>
                </p>
            </th>
            <th>
                <p>
                    <strong><span>Date</span></strong>
                </p>
            </th>
        </tr>
        </thead>

        <tbody id="the-list" class="order_list ui-sortable">
        <?php
        if(is_array($contactList) && count($contactList)>0){
            foreach($contactList as $contact){
                $contact_category = '';
                $all_category = "";
                $wp_term_relationships = $wpdb->get_results("SELECT term_taxonomy_id FROM ".$wpdb->prefix."term_relationships WHERE object_id='".$contact->ID."'");
                foreach($wp_term_relationships as $term_taxonomy_id){
                    $wp_term_taxonomy = $wpdb->get_results("SELECT name FROM ".$wpdb->prefix."terms WHERE term_id='".$term_taxonomy_id->term_taxonomy_id."'");
                    foreach($wp_term_taxonomy as $category_name){
                        $contact_category .=' <span>'.$category_name->name.'</span> ';
                        $category_n = str_replace(' ' , '-' , $category_name->name);
                        $category_n = str_replace(array("'", '"'), array("\'", '\"'), $category_n);
                        $all_category.=$category_n."{split}";
                    }
                }
                if($all_category==null || $all_category===""){
                    $all_category = "";
                }
                echo"
					<tr class='single_contact' data-show='true' data-category =".$all_category." data-elementId='".$contact->ID."'>
			            <th scope='row' class='check-column'><img class='move_cursor' src='".SC_URL."/images/move_cursor.png'></th>
		                <td><strong><p>".$contact->ID."</p></strong></td>
		                <td><p>".$contact->post_title."</p></td>
                        <td>
                            ".$contact_category."
                        </td>
                        <td>Published<br><abbr>".$contact->post_date."</abbr></td>
	                </tr>
		            ";
            }
        }else{
            echo'<tr class="no-items">
                    <td class="colspanchange" colspan="3">No posts found.</td>
                 </tr>';
        }
        ?>
        </tbody>

        <tfoot>
        <tr>
            <td id="cb" class="manage-column column-cb check-column">
            </td>
            <th>
                <p>
                    <strong><span>ID</span></strong>
                </p>
            </th>
            <th>
                <p>
                    <strong><span>Name</span></strong>
                </p>
            </th>
            <th>
                <p>
                    <strong><span>Category</span></strong>
                </p>
            </th>
            <th>
                <p>
                    <strong><span>Date</span></strong>
                </p>
            </th>
        </tr>
        </tfoot>

    </table>
</div>
<?php }?>