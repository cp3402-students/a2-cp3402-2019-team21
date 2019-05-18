<?php
function contShortcodeHandler($atts) {
    $theme_id = 23;
    $theme = 'sc_theme';
    $html = '';
    ob_start();
    if (isset($atts['id'])) {
        $post = get_post($atts['id']);
        $only_single = TRUE;
        include (SC_DIR . '/views/SCViewSingleContact.php');
        $html = ob_get_clean();
    } else {
        $cats = explode(',', $atts['cats']);
        $cont_posts = array();
//        $order = $atts['order'];
//        if ($order == 'id') {
//            $orderBy = 'ID';
//        } else {
//            $orderBy = 'title';
//        }
        $orderBy = 'ID';
        $postslist = get_posts(array(
            'post_type' => SContCPT::$post_type,
            'showposts' => -1,
            'tax_query' => array(
                array(
                    'taxonomy' => SContCPT::$taxonomy,
                    'terms' => array_filter($cats),
                    'field' => 'term_id',
                )
            ),
            'orderby' => $orderBy,
            'order' => 'ASC'
        ));
        $order = get_option('staff_order_contact');
        if($order && isset($order)){
            $order_list = array();
            foreach($order as $id){
                foreach($postslist as $contact){
                    if($contact->ID == $id){
                        array_push($order_list , $contact);
                    }
                }
            }
            $cont_posts = array_merge($cont_posts, $order_list);
        }else{
            $cont_posts = array_merge($cont_posts, $postslist);
        }

        $cont_posts = array_map("unserialize", array_unique(array_map("serialize", $cont_posts)));
        wp_reset_postdata();
        wp_reset_query();

        switch ($atts['type']) {
            case 'short':
                include SC_DIR . '/views/SCViewShortContact.php';
                $html = ob_get_clean();
                break;
            case 'full':
                include SC_DIR . '/views/SCViewFullContact.php';
                $html = ob_get_clean();
                break;
            case 'table':
                include SC_DIR . '/views/SCViewTableContact.php';
                $html = ob_get_clean();
                break;
			case 'chess':
                include SC_DIR . '/views/SCViewChessContact.php';
                $html = ob_get_clean();
                break;
			case 'Portfolio':
                include SC_DIR . '/views/SCViewPortolioContact.php';
                $html = ob_get_clean();
                break;

			case 'blog':
                include SC_DIR . '/views/SCViewBlogContact.php';
                $html = ob_get_clean();
                break;

			case 'circle':
                include SC_DIR . '/views/SCViewCircleContact.php';
                $html = ob_get_clean();
                break;

			case 'square':
                include SC_DIR . '/views/SCViewSquareContact.php';
                $html = ob_get_clean();
                break;
        }
    }

    $s_contact = SContClass::getInstance();
    $s_contact->contactStyles("shortcode");

    return $html;
}

add_shortcode('Staff_Directory_WD', 'contShortcodeHandler');

function staff_contact_wd_captcha() {
    if (isset($_POST['checkcap'])) {
        $p_id = $_POST['post_id'];
        if ($_POST['checkcap'] == '1') {
            if(!isset($_SESSION)){
                session_start();
            }
            if (isset($_POST['cap_code'])) {
                if (md5($_POST['cap_code']) == $_SESSION['session_captcha_code'][$p_id]) {
                    echo '1';
                    die();
                }
            }
            unset($_SESSION['session_captcha_code']);
        }
        echo '0';
        die();
    } else {
        $p_id = $_GET['post_id'];
        if(!isset($_SESSION)){
            session_start();
        }
        //You can do any necessary settings as you wish here
        //If you reduce the width and height of the captcha here then you have to change it in the css file as well
        $image_width = 80;
        $image_height = 30;
        $characters_on_image = 6;
        $font = SC_DIR . '/images/monofont.ttf';

        //The characters that can be used in the CAPTCHA code. Avoid confusing characters (l 1 and i for example)
        $possible_letters = '23456789bcdfghjkmnpqrstvwxyz';
        $random_dots = 0;
        $random_lines = 15;
        $captcha_text_color = "0x142864";
        $captcha_noice_color = "0x142864";

        $code = '';
        $i = 0;
        while ($i < $characters_on_image) {
            $code .= substr($possible_letters, mt_rand(0, strlen($possible_letters) - 1), 1);
            $i++;
        }
        $font_size = $image_height * 0.75;
        $image = @imagecreate($image_width, $image_height);


        /* Setting the background, text and noise colours here */
        $background_color = imagecolorallocate($image, 255, 255, 255);

        $arr_text_color = RGB_HEX($captcha_text_color);
        $text_color = imagecolorallocate($image, $arr_text_color['red'], $arr_text_color['green'], $arr_text_color['blue']);
        $arr_noice_color = RGB_HEX($captcha_noice_color);
        $image_noise_color = imagecolorallocate($image, $arr_noice_color['red'], $arr_noice_color['green'], $arr_noice_color['blue']);

        /* This generates the dots randomly strings in background */
        for ($i = 0; $i < $random_dots; $i++) {
            imagefilledellipse($image, mt_rand(0, $image_width), mt_rand(0, $image_height), 2, 3, $image_noise_color);
        }

        /* This generates lines randomly strings in background of image */
        for ($i = 0; $i < $random_lines; $i++) {
            imageline($image, mt_rand(0, $image_width), mt_rand(0, $image_height), mt_rand(0, $image_width), mt_rand(0, $image_height), $image_noise_color);
        }

        /* This creates a text box and add 6 letters code in it */
        $textbox = imagettfbbox($font_size, 0, $font, $code);
        $x = ($image_width - $textbox[4]) / 2;
        $y = ($image_height - $textbox[5]) / 2;
        imagettftext($image, $font_size, 0, $x, $y, $text_color, $font, $code);

        /* Show captcha image in the page html page */
        header('Content-Type: image/jpeg'); // defining the image type to be shown in browser widow
        imagejpeg($image); //showing the image
        imagedestroy($image); //destroying the image instance
        $_SESSION['captcha_code'][$p_id] = md5($code);
        $_SESSION['session_captcha_code'][$p_id] = md5($code);
    }
    die('');
}

add_action('wp_ajax_teamtwdcaptchae', 'staff_contact_wd_captcha');
add_action('wp_ajax_nopriv_teamtwdcaptchae', 'staff_contact_wd_captcha');

function RGB_HEX($hexstr) {
    $int = hexdec($hexstr);
    return array("red" => 0xFF & ($int >> 0x10), "green" => 0xFF & ($int >> 0x8), "blue" => 0xFF & $int);
}
