<?php
$protocol = stripos($_SERVER['SERVER_PROTOCOL'], 'https') === true ? 'https://' : 'http://';
$actual_link = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . "";

$checkREQ = '';
$pattern = "/\?/";
//$pattern_2 = "/\?page_id=/";
$pattern2 = "/&gdgallery-page=[0-9]+/";
if (preg_match($pattern, $actual_link)) {
    if (preg_match($pattern2, $actual_link)) {
        $actual_link = preg_replace($pattern2, '', $actual_link);
    }
    $checkREQ = $actual_link . '&gdgallery-page';
} else {
    $checkREQ = '?gdgallery-page';
}
$page = (isset($_GET["gdgallery-page"])) ? intval($_GET["gdgallery-page"]) : 1;

$page_nav_type = $page_options["nav_type"];
switch ($page_nav_type) {
    case 0:
        $navigation = array('<i class="fa fa-angle-double-left"></i>', "<i class='fa fa-angle-left'></i>", "<i class='fa fa-angle-right'></i>", "<i class='fa fa-angle-double-right'></i>");
        break;
    case 1:
        $navigation = explode(",", $page_options["nav_text"]);
        break;
    case 2:
        $navigation = array("disable" => true);
        break;
    default:
        $navigation = array("<i class='fa fa-angle-double-left'>", "<i class='fa fa-angle-left'>", "<i class='fa fa-angle-right'>", "<i class='fa fa-angle-double-rigt'>");
        break;
}

?>

<div class="gdgallery-pagination-<?= $gallery_data->id_gallery ?>">
    <?php


    $pervpage = '';
    if ($page != 1 && $page_nav_type != 2) {
        if (isset($navigation[0]) && $navigation[0] != "") {
            $pervpage .= '<a href= ' . $checkREQ . '=1 class="gdgallery-pagination-icon gdgallery-pagination-icon-first">' . $navigation[0] . '</a>';
        }
        if (isset($navigation[1]) && $navigation[1] != "") {
            $pervpage .= '<a href= ' . $checkREQ . '=' . ($page - 1) . ' class="gdgallery-pagination-icon gdgallery-pagination-icon-prev">' . $navigation[1] . '</a> ';
        }
    }

    $cur_page = (isset($_GET["gdgallery-page"])) ? intval($_GET["gdgallery-page"]) : 1;
    if ($cur_page <= 0) {
        $cur_page = 1;
    }
    $page_numbers = '';
    $nearby = (isset($page_options["nearby"]) && $page_options["nearby"] != "") ? $page_options["nearby"] : $gallery_data->total;
    if ($nearby == "All") $nearby = $gallery_data->total;

    for ($i = 1; $i <= $gallery_data->total; $i++) {
        if ($i >= $cur_page - $nearby && $i <= $cur_page + $nearby && $i != $cur_page) {
            $page_numbers .= '<a href= ' . $checkREQ . '=' . $i . ' class="gdgallery-pagination-icon gdgallery-pagination-icon-number">' . $i . '</a>';
        } elseif ($i == $cur_page) {
            $page_numbers .= '<a href="#" class="gdgallery-pagination-icon gdgallery-pagination-icon-active">' . $i . '</a>';
        }
    }

    $nextpage = '';
    if ($page != $gallery_data->total && $page_nav_type != 2) {
        if (isset($navigation[2]) && $navigation[2] != "") {
            $nextpage .= ' <a href= ' . $checkREQ . '=' . ($page + 1) . ' class="gdgallery-pagination-icon gdgallery-pagination-icon-next">' . $navigation[2] . '</a>';
        }
        if (isset($navigation[3]) && $navigation[3] != "") {
            $nextpage .= '<a href= ' . $checkREQ . '=' . $gallery_data->total . ' class="gdgallery-pagination-icon gdgallery-pagination-icon-last">' . $navigation[3] . '</a>';
        }
    }
    echo $pervpage . $page_numbers . $nextpage;
    ?>
</div>


<style>
    .gdgallery-pagination-<?= $gallery_data->id_gallery ?> {
        margin-top: 25px !important;
    }

    .gdgallery-pagination-<?= $gallery_data->id_gallery ?> .gdgallery-pagination-icon {
        box-shadow: none !important;
    }
</style>

