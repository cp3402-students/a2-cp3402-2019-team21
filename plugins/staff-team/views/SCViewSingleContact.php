<?php
global $contLDomain;

$theme = 'sc_theme';
$theme_id = 23;
?>

<?php if(!is_null($post)):?>
    <?php
  $post_id = $post->ID;

  $want_email = get_post_meta($post_id, 'want_email', TRUE);
  $email = get_post_meta($post_id, 'email', TRUE);
  $params = get_post_meta($post_id, 'params', TRUE);
  /*=Get Theme's params =*/
  $paramsTheme = get_post_meta($theme_id, 'params');
  $has_image = has_post_thumbnail($post->ID);

  $mess_result['text'] = '';
  $mess_result['class'] = '';

  /*- Upload 'No Image' -*/
  $noImage = isset($paramsTheme[0]['single_no_image'])?$paramsTheme[0]['single_no_image']:'';
  if (!$has_image){
    $attach_url['thumb'] = SC_URL . '/images/noimage.jpg';
  }
  else{
    $at_id = get_post_thumbnail_id($post->ID);
    $img_src = wp_get_attachment_image_src($at_id, 'large');
    $attach_url['thumb'] = $img_src[0];
    $img_src = wp_get_attachment_image_src($at_id, 'full');
    $attach_url['full'] = $img_src[0];
  }
  $categories = wp_get_post_terms($post_id,SContCPT::$taxonomy,array("fields" => "all"));
  $category = '';
  foreach ($categories as $key => $cat) {
    if($key!=0)
      $category .= ' , ';
    $category .=  $cat->name;
  }
  wp_reset_postdata();
  wp_reset_query();
  if(!isset($_SESSION)){
    @session_start();
  }

  if(isset($_SESSION['cont_message_result'][$post_id])){
    if($_SESSION['cont_message_result'][$post_id]=='success'){
      $mess_result['text'] = __('Message successfully sent',$contLDomain);
      $mess_result['class'] = 'mess_success';
    }
    else{
      $mess_result['text'] = __('Message not sent',$contLDomain);
      $mess_result['class'] = 'mess_error';
    }
    $_SESSION['cont_message_result'][$post_id] = null;
  }

  $captcha_value = get_option('twd_captcha');
  if ($captcha_value === false){
    $captcha_value = '1';
  }
  $twd_pp_text = get_option('twd_pp_text');
  ?>

    <div class="<?php echo $theme; ?>">
        <div class="single_contact_content">
            <div class="single_contact_main">
                <div id="single_contact">
                    <div class="single_inform">
                        <div class="content">
                            <!--Image-->
                            <div class="img_content">
                              <?php if ($has_image || $noImage==""){ ?>
                                  <a href="<?php  if(isset($attach_url['full'])){echo $attach_url['full'];} ?>" <?php if(get_option('lightbox')) echo 'rel="contact_lightbox"';else echo 'target="_blank"';?> class="cont_main_picture_a" style="text-decoration:none;">
                                      <div class="single_cont_main_picture" style="background-image: url('<?php echo $attach_url['thumb']; ?>');">
                                          <img src="<?php echo $attach_url['thumb']; ?>" id="imagelightbox" style="display:none;"/>
                                      </div>
                                  </a>
                              <?php } else{  ?>
                                  <div class="single_cont_main_picture" style="background-image: url('<?php echo $noImage; ?>');">
                                      <img src="<?php echo $noImage; ?>" id="imagelightbox" style="display:none;"/>
                                  </div>
                              <?php } ?>
                            </div>

                            <!--Title-->
                            <div class="top_info">
                                <div class="cont_name"><?php echo get_the_title($post->ID); ?></div>
                                <div class="cont_categ"><?php echo $category;?></div>
                            </div>

                            <!--Parameters-->
                            <table class="single_params">
                              <?php if(!empty($email)) { ?>
                                  <tr>
                                      <td class="param_name border_top"><?php echo __('Email',$contLDomain);?>:</td>
                                      <td class="param_value border_top"><a href="mailto:<?php echo $email;?>"><?php echo $email;?></a></td>
                                  </tr>
                                <?php
                              }
                              if(is_array($params)){
                                foreach ($params as $index => $value) {
                                  for ( $i = 0; $i < count($value); $i++) {
                                    if (strlen($value[$i]) > 0) {
                                      if($i == 0) {
                                        echo '<tr><td class="param_name border_top">'.$index.':</td>'
                                          . '<td class="param_value border_top">'.$value[$i].'</td></tr>';
                                      } else{
                                        echo '<tr><td></td>'
                                          . '<td class="param_value no-border">'.$value[$i].'</td></tr>';
                                      }
                                    }
                                  }
                                }
                              }
                              ?>
                            </table>
                        </div>
                        <!--Content-->
                      <?php $desc = str_replace('<hr id="system-readmore" />', '<hr style="display:none;" id="system-readmore">', $post->post_content);?>
                        <div class="contAllDescription"><?php echo wpautop($desc);?></div>
                    </div>
                </div>

                <!--Message-->
              <?php if(esc_attr(get_option('enable_message'))): ?>
                <?php if(!empty($mess_result['text'])) { ?>
                      <p class="mess_result <?php echo $mess_result['class'] ?>"><?php echo $mess_result['text']; ?></p>
                <?php } ?>
                  <div id="message_div">
                      <form action="" name="message" method="post">
                          <table border="1" class="message_table">
                            <?php if(esc_attr(get_option('show_name'))):?>
                                <tr>
                                    <td class="mess_param">
                                      <?php _e('Your name',$contLDomain);?>:
                                        <span class="req_input">*</span>
                                    </td>
                                    <td> <input type="text" name="full_name" class="full_name"> </td>
                                </tr>
                            <?php endif;?>
                            <?php if(esc_attr(get_option('show_phone'))):?>
                                <tr>
                                    <td class="mess_param">
                                      <?php _e('Your phone',$contLDomain);?>:
                                        <span class="req_input">*</span>
                                    </td>
                                    <td> <input type="text" name="phone" class="phone"/> </td>
                                </tr>
                            <?php endif;?>
                            <?php if(esc_attr(get_option('show_email'))):?>
                                <tr>
                                    <td class="mess_param">
                                      <?php _e('Your e-mail',$contLDomain);?>:
                                        <span class="req_input">*</span>
                                    </td>
                                    <td> <input type="text" name="email" class="email"/> </td>
                                </tr>
                            <?php endif;?>
                            <?php if(esc_attr(get_option('show_phone')) && esc_attr(get_option('show_email')) && esc_attr(get_option('show_name'))):?>
                                <tr>
                                    <td class="mess_param"> <?php _e('Contact Preference',$contLDomain);?>: </td>
                                    <td>
                                        <input type="radio" name="cont_pref" class="cont_pref1" id="cont_pref1" value="1"/> <label for="cont_pref1"><?php _e('Phone',$contLDomain);?></label>
                                        <input type="radio" name="cont_pref" class="cont_pref0" id="cont_pref0" value="0" checked="checked"/> <label for="cont_pref0"><?php _e('E-mail',$contLDomain);?></label>
                                        <input type="radio" name="cont_pref" class="cont_pref2" id="cont_pref2" value="2" /> <label for="cont_pref2"><?php _e('Either',$contLDomain);?></label>
                                    </td>
                                </tr>
                            <?php endif;?>
                              <tr>
                                  <td class="mess_param">
                                    <?php _e('Title of Message',$contLDomain);?>:
                                      <span class="req_input">*</span>
                                  </td>
                                  <td> <input type="text" name="mes_title" class="mes_title"/> </td>
                              </tr>
                              <tr>
                                  <td class="mess_param">
                                    <?php _e('Text',$contLDomain);?>:
                                      <span class="req_input">*</span>
                                  </td>
                                  <td> <textarea rows="4" name="message_text" class="message_text"></textarea> </td>
                              </tr>
                              <tr class="hidden">
                                  <td colspan="2">
                                      <input type="hidden" name="want_email" value="<?php echo $want_email;?>"/>
                                      <input type="hidden" name="contact_id" class="contact_id" value="<?php echo $post_id;?>"/>
                                      <input type="hidden" name="contact_name" value="<?php echo get_the_title($post->ID); ;?>"/>
                                      <input type="hidden" name="contact_mail" value="<?php echo $email;?>"/>
                                      <input type="hidden" name="contact_categories" value="<?php echo $category;?>"/>
                                      <input type="hidden" name="view" value="showcontact"/>
                                      <input type="hidden" name="is_message" value="true"/>
                                      <input type="hidden" name="option" value=""/><br>
                                  </td>
                              </tr>
                            <?php if($captcha_value == '1'){ ?>
                                <tr>
                                    <td class="mess_param"> <?php echo __('Please, Enter the Code', $contLDomain); ?> </td>
                                    <td>
                                        <div id="staff_capcha">
                                            <div class="wd_captcha_main">
                                                <div class="wd_captcha">
                                                    <img src="<?php echo admin_url('admin-ajax.php?action=teamtwdcaptchae').'&post_id='.$post_id ?>" class="wd_captcha_img" height="24" width="80" />
                                                </div>
                                                <a href="#" style="text-decoration: none !important; border:0px !important;" class="cont_mess_captcha_ref">
                                                    <img src="<?php echo SC_URL.'/images/refresh.png'; ?>" border="0" style="border:none; min-width: 31px;" />
                                                </a>
                                            </div>
                                            <input type="text" name="code" class="message_capcode" size="6" />
                                            <span type="hidden" class="caphid"></span>
                                        </div>
                                    </td>
                                </tr>
                            <?php } else if ($captcha_value == '2') { ?>
                                <tr>
                                    <td><div class="twd_gcaptcha" id="<?php echo uniqid('twd_'); ?>"></div></td>
                                    <td></td>
                                </tr>
                            <?php } ?>
                            <?php if(!empty($twd_pp_text)) { ?>
                                <tr class="twd_pp_container">
                                    <td colspan="2">
                                        <input type="checkbox" id="twd_pp_checkbox"/>
                                        <lable for="twd_pp_checkbox"><?php echo $twd_pp_text; ?></lable>
                                    </td>
                                </tr>
                            <?php } ?>
                          </table>
                          <div class="send_button">
                              <input type="button" class="teamsendbutton" value="<?php echo __('SEND MESSAGE', $contLDomain) ?>" />
                          </div>
                      </form>
                  </div>
              <?php endif;?>
            </div>
        </div>

        <script>
            jQuery('.single_contact_content').parent().parent().parent().parent().find(".post-thumbnail-div").css('display','none');
        </script>
    </div>
<?php endif;?>
