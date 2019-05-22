<?php

if(isset($_GET['edit']) && !empty($_GET['edit']) && $_GET['edit'] != "undefined" ){
  $twd_edit_shortcode = str_replace('\"' , '"' ,$_GET['edit']);
  $twd_edit_shortcode_data = (shortcode_parse_atts($twd_edit_shortcode));
}
$twd_shortcode_views = array(
       "Full", "Short", "Chess", "Portfolio", "Blog", "Circle", "Square", "Table"
);

$args = array(
  'post_type' => 'contact',
  'post_status' => 'publish',
  'posts_per_page' => - 1,
  'ignore_sticky_posts' => 1
);

$twd_posts = get_posts($args);
$contact_category = get_terms('cont_category');
/*echo "<pre>";
var_dump($contact_category);
die;*/

?>


<?php if(isset($twd_posts) && is_array($twd_posts)): ?>
<form id="twd_shortcode_form">
    <div class="twd_shortcode">
        <div class="twd_shortcode_header">
            <h1>Team WD</h1>
        </div>
        <div class="twd_shortcode_menu">
            <span class="twd_active" data-view="twd_single_content">Single Contact</span>
            <span data-view="twd_contacts_category">Contacts Category</span>
        </div>
        <div class="clear"></div>
        <div class="twd_shortcode_view twd_single_content">
            <label class="twd_shortcode_label" for="twd_select_contact">Select Contact</label>
            <select name="twd_contact" id="twd_select_contact">
                <option value="select_cat">Select Contact</option>
                <?php foreach($twd_posts as $twd_post):?>
                    <?php
                        $selected_cont = "";

                        if(isset($twd_edit_shortcode_data) && isset($twd_edit_shortcode_data["id"]) && $twd_edit_shortcode_data["id"] == $twd_post->ID){
                            $selected_cont="selected";
                        }
                  ?>
                    <option <?php echo $selected_cont;?> value="<?php echo $twd_post->ID;?>"><?php echo $twd_post->post_title;?></option>
                <?php endforeach;?>
            </select>
        </div>
        <div class="twd_shortcode_view twd_contacts_category">
            <?php if(isset($contact_category) && is_array($contact_category)):?>
                <div class="twd_cat_content">
                    <span class="twd_shortcode_label">Select Category</span>
                    <div class="twd_cat">
                    <?php foreach($contact_category as $cat):?>
                            <div>
                                <?php
                                    $checked_cat = "";
                                    if(isset($twd_edit_shortcode_data) && isset($twd_edit_shortcode_data["cats"])){
                                        $saved_cats_list = explode(",", $twd_edit_shortcode_data["cats"]);
                                        if(is_array($saved_cats_list)){
                                            foreach($saved_cats_list as $twd_cat){
                                                if($twd_cat!=""){
                                                    if($twd_cat == $cat->term_id){
                                                      $checked_cat = "checked";
                                                    }
                                                }
                                            }
                                        }
                                    }

                                ?>
                                <input <?php echo $checked_cat;?> id="twd_select_cat_<?php echo $cat->term_id;?>" value="<?php echo $cat->term_id;?>" name="twd_cat" type="checkbox">
                                <label for="twd_select_cat_<?php echo $cat->term_id;?>"><?php echo $cat->name;?></label>
                            </div>
                    <?php endforeach;?>
                    </div>
                </div>
            <?php endif;?>
            <div class="twd_select_view">
                <label class="twd_shortcode_label" for="twd_select_view">View Type</label>
                <select name="twd_shortcode_view" id="twd_select_view">
                    <?php //$twd_edit_shortcode_data["type"]?>
                    <?php foreach($twd_shortcode_views as $twd_view):?>
                        <?php
                            $selected_view = "disabled";

                            if($twd_view === "Full"){
                              $selected_view = "selected";
                            }
                        /*    if(isset($twd_edit_shortcode_data) && isset($twd_edit_shortcode_data["type"])){
                              $v1 = strtolower($twd_view);
                              $v2 = strtolower($twd_edit_shortcode_data["type"]);

                              if($v1 === $v2){
                                $selected_view = "selected";
                              }
                            }*/

                        ?>
                        <option <?php echo $selected_view;?>><?php echo $twd_view;?></option>
                    <?php endforeach;?>
                </select>
                <p class="twd_shortcode_notice">Only Full view is available in free version. If you need other views, you need to buy the Paid version.</p>
            </div>
        </div>
        <div class="twd_shortcode_buttons">
            <button class="twd_add_shortcode button button-primary button-large">OK</button>
        </div>
    </div>
</form>




    <style>
        body{
            margin: 0;
        }
        .twd_shortcode_header{
            background: #fcfcfc;
            border-bottom: 1px solid #ddd;
            padding: 0;
            min-height: 36px;
        }
        .twd_shortcode_header h1{
            color: #444;
            font-size: 18px;
            font-weight: 600;
            line-height: 36px;
            margin: 0;
            padding: 0 36px 0 16px;
        }
        .twd_cat_content{
            display: inline-block;
        }
        .twd_cat_content span , .twd_cat_content .twd_cat{
            float: left;
        }
       /* .twd_cat_content span{
            margin-right: 40px;
        }*/
        .twd_contacts_category{
            display: none;
        }
        .twd_shortcode_menu{
            border-bottom: 1px solid #c5c5c5;
            display: inline-block;
            width: 100%;
            margin-top: 15px;
        }
        .twd_shortcode .twd_shortcode_menu .twd_active{
            background: #FDFDFD;
            border-bottom-color: transparent;
            margin-bottom: -1px;
            height: 14px;
        }
        .twd_shortcode_menu span{
            display: inline-block;
            border: 1px solid #c5c5c5;
            border-width: 0 1px 0 0;
            background: #fff;
            padding: 8px 15px;
            text-shadow: 0 1px 1px rgba(255,255,255,0.75);
            height: 13px;
            cursor: pointer;
            float: left;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }
  /*      .twd_shortcode .twd_shortcode_menu span:focus{
            box-shadow: 0 0 0 1px #5b9dd9, 0 0 2px 1px rgba(30,140,190,.8);
            color: #2276d2;
        }*/
        .twd_shortcode_view{
            padding: 20px;
        }
        .twd_shortcode select{
            background: #f7f7f7;
            font-size: 14px;
            font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;
            line-height: 28px;
            color: #555;
        }
        .twd_shortcode label{
            display: inline-block;
            text-shadow: 0 1px 1px rgba(255,255,255,0.75);
            overflow: hidden;
            font-size: 14px;
            color: #595959;
            font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;
            /*line-height: 0px;*/
            line-height: 10px;
            height: 14px;
        }
        .twd_shortcode_label{
            min-width: 130px;
            color: #595959;
        }
        .twd_cat label{
            color: #595959;
            font-size: 14px;
        }
        .twd_cat input[type=checkbox]
        {
            /* Double-sized Checkboxes */
            -ms-transform: scale(1.3); /* IE */
            -moz-transform: scale(1.3); /* FF */
            -webkit-transform: scale(1.3); /* Safari and Chrome */
            -o-transform: scale(1.3); /* Opera */
            padding: 5px;
            background-color: #fff;
            margin-bottom: 9px;
        }
        #twd_shortcode_form select{
            width: 260px;
            height: 28px;
        }


        .button,
        .button-primary,
        .button-secondary {
            display: inline-block;
            text-decoration: none;
            font-size: 13px;
            line-height: 26px;
            height: 28px;
            margin: 0;
            padding: 0 10px 1px;
            cursor: pointer;
            border-width: 1px;
            border-style: solid;
            -webkit-appearance: none;
            -webkit-border-radius: 3px;
            border-radius: 3px;
            white-space: nowrap;
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            box-sizing: border-box;
        }

        .button,
        .button-secondary {
            color: #555;
            border-color: #cccccc;
            background: #f7f7f7;
            -webkit-box-shadow: inset 0 1px 0 #fff, 0 1px 0 rgba(0,0,0,.08);
            box-shadow: inset 0 1px 0 #fff, 0 1px 0 rgba(0,0,0,.08);
            vertical-align: top;
        }

        p .button {
            vertical-align: baseline;
        }


        .button:hover,
        .button-secondary:hover,
        .button:focus,
        .button-secondary:focus {
            background: #fafafa;
            border-color: #999;
            color: #222;
        }

        .button:focus,
        .button-secondary:focus {
            -webkit-box-shadow: 1px 1px 1px rgba(0,0,0,.2);
            box-shadow: 1px 1px 1px rgba(0,0,0,.2);
        }

        .button:active,
        .button-secondary:active {
            background: #eee;
            border-color: #999;
            color: #333;
            -webkit-box-shadow: inset 0 2px 5px -3px rgba( 0, 0, 0, 0.5 );
            box-shadow: inset 0 2px 5px -3px rgba( 0, 0, 0, 0.5 );
        }

        .button-primary {
            background: #2ea2cc;
            border-color: #0074a2;
            -webkit-box-shadow: inset 0 1px 0 rgba(120,200,230,0.5), 0 1px 0 rgba(0,0,0,.15);
            box-shadow: inset 0 1px 0 rgba(120,200,230,0.5), 0 1px 0 rgba(0,0,0,.15);
            color: #fff;
            text-decoration: none;
        }

        .button-primary:hover,
        .button-primary:focus {
            background: #1e8cbe;
            border-color: #0074a2;
            -webkit-box-shadow: inset 0 1px 0 rgba(120,200,230,0.6);
            box-shadow: inset 0 1px 0 rgba(120,200,230,0.6);
            color: #fff;
        }

        .button-primary:focus {
            border-color: #0e3950;
            -webkit-box-shadow: inset 0 1px 0 rgba(120,200,230,0.6), 1px 1px 2px rgba(0,0,0,0.4);
            box-shadow: inset 0 1px 0 rgba(120,200,230,0.6), 1px 1px 2px rgba(0,0,0,0.4);
        }

        .button-primary:active {
            background: #1b7aa6;
            border-color: #005684;
            color: rgba(255,255,255,0.95);
            -webkit-box-shadow: inset 0 1px 0 rgba(0,0,0,0.1);
            box-shadow: inset 0 1px 0 rgba(0,0,0,0.1);
            vertical-align: top;
        }

        .twd_add_shortcode{
            position: absolute;
            bottom: 10px;
            right: 10px;
        }

        .twd_shortcode_notice{
            color: #cc0000 !important;
            word-break: break-all !important;
            font-size: 11px !important;
            margin-top: 20px;
            font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;
        }
    </style>
    <script>
        jQuery(".twd_shortcode_menu span").click(function () {
            jQuery(".twd_shortcode_menu span").removeClass("twd_active");
            jQuery(this).addClass("twd_active");
            var twd_data_view = jQuery(this).data("view");
            jQuery(".twd_shortcode_view").css({
               'display':'none'
            });
            jQuery("."+twd_data_view).css({
                'display':'block'
            });
        });
        jQuery(".twd_add_shortcode").click(function (e) {
            e.preventDefault();
            var twd_shortcode_data =  jQuery("#twd_shortcode_form").serializeArray();
            var twd_shortcode = ecwd_generate_ecwd_shortcode(twd_shortcode_data);
            if(twd_shortcode !== false){
                window.parent['wdg_cb_tw/twd'](twd_shortcode);
            }

        });
        function ecwd_generate_ecwd_shortcode(data) {
            var twd_contact = "";
            var twd_shortcode_view = "";
            var twd_cat = "";
            for(i in data){
                if(data[i]["name"] === "twd_contact"){
                    twd_contact = data[i]["value"];
                }
                if(data[i]["name"] === "twd_shortcode_view"){
                    twd_shortcode_view = data[i]["value"].toLowerCase();
                }
                if(data[i]["name"] === "twd_cat"){
                    twd_cat += data[i]["value"]+",";
                }
            }
            if(twd_contact !== "select_cat"){
                var twd_shortcode = '[Staff_Directory_WD  id="'+twd_contact+'" ]';
            }else if(twd_cat!==""){
                var twd_shortcode = '[Staff_Directory_WD  cats="'+twd_cat+'" type="'+twd_shortcode_view+'" ]';
            }else {
                twd_shortcode = false;
            }
            return twd_shortcode;
        }
    </script>
<?php endif;?>

