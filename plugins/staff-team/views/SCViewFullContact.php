<?php
global $contLDomain;
$conts = array();
foreach ($cont_posts as $key => $item) {
    $cont['id'] = $item->ID;
    $cont['title'] = $item->post_title;
    $cont_result = get_extended( $item->post_content );
	if(isset($item->post_content) && $item->post_content!=NULL){
        $cont['description'] = preg_replace('/<img[^>]+./' , '' , $cont_result['main']);
        $cont['description_popup'] = mb_substr(apply_filters('the_content',$item->post_content), 0, 5000);
	}else{
        $cont['description'] = $cont_result['main'];
		$cont['description_popup'] = "";
	}
    $cont['img'] = array();
	if (!has_post_thumbnail($item->ID)){
        $cont['img']['thumb'] = SC_URL . '/images/noimage.jpg';
        $cont['img']['full'] = SC_URL . '/images/noimage.jpg';
    } else{
        $at_id = get_post_thumbnail_id($item->ID);
        $img_src = wp_get_attachment_image_src($at_id, 'large');
        $cont['img']['thumb'] = $img_src[0];
        $img_src = wp_get_attachment_image_src($at_id, 'full');
        $cont['img']['full'] = $img_src[0];
    }

    $cont['email']= get_post_meta($item->ID, 'email', TRUE);
    $cont['params'] = get_post_meta($item->ID, 'params', TRUE);
    $categories = wp_get_post_terms($item->ID,SContCPT::$taxonomy,array("fields" => "all"));
    $category = '';
    foreach ($categories as $key => $cat) {
		if($key!=0)
			$category .= ', ';
		$category .=  $cat->name;
    }
    $cont['category'] = $category;
    $team_url = get_post_meta($item->ID, 'team_url', TRUE);
    $want_url = get_post_meta($item->ID, 'want_url', TRUE);
    if(isset($want_url) && intval($want_url)===1){
      $cont['link'] = $team_url;
    }else{
      $cont['link'] = get_permalink( $item->ID );
    }
    $conts[] = $cont;
}
$params = get_post_meta($theme_id, 'params');
$full_conts = json_encode($conts);
/*- Contact's count -*/
$viewCount  = isset($params[0]['full_cont_count_in_page'])?$params[0]['full_cont_count_in_page']:'4';
/*- Upload 'No Image' -*/
$noImage  = isset($params[0]['full_no_image'])?$params[0]['full_no_image']:'';
/*- Social -*/
$staff_face = (isset($params[0]['full_social_fb'])) ? $params[0]['full_social_fb'] : "";
$staff_inst = (isset($params[0]['full_social_ins'])) ? $params[0]['full_social_ins'] : "";
$staff_tw = (isset($params[0]['full_social_tw'])) ? $params[0]['full_social_tw'] : "";
$staff_gp = (isset($params[0]['full_social_gp'])) ? $params[0]['full_social_gp'] : "";

$search_type = 0;
if(get_option('choose_category')==1)
    $search_type++;
if(get_option('name_search')==1)
    $search_type+=2;
?>
<?php
$staff_uniqid = uniqid();
?>
<div class="<?php echo $theme; ?>" id="theme">
    <!--Search-->
	<div id="full_search" class="search_<?php echo $staff_uniqid;?>">
		<?php if(get_option('name_search')!=0 || get_option('choose_category')!=0):?>
        <div class="staff_search">
            <input type="text" class="search_cont" name="search" placeholder="<?php _e('Search',$contLDomain);?>..."/>
        </div>
        <?php endif;?>
	</div>

	<!--Contacts-->
	<div id="full_contact" class="parentDiv">
        <div class="staff_sc_container staff_<?php echo $staff_uniqid;?>"></div>
		<!-- Popup -->
		<div id="full_popup">
			<div id="popup" class="popup"></div>
		</div>
    </div>

	<!--Pagination-->
    <div id="full_pgnt">
        <div class="staff_pagination pagination_<?php echo $staff_uniqid;?>"></div>
    </div>

    <script>
        var full_cont = contactView(<?php echo $full_conts;?>,<?php echo $viewCount;?>,'<?php echo $theme;?>','full',<?php echo $search_type ?>,<?php echo get_option('lightbox')?>, '<?php echo $staff_face; ?>', '<?php echo $staff_inst; ?>', '<?php echo $staff_tw; ?>', '<?php echo $staff_gp; ?>', '<?php echo get_option('enable_message'); ?>', '<?php echo $noImage; ?>' , '<?php echo $staff_uniqid;?>');
    </script>
</div>
