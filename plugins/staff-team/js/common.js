function check_captcha(parent,type , code,id){
    jQuery.ajax({
                type: 'post',
                url: contactAjaxUrl,
                data: {
                    action: 'teamtwdcaptchae',
                    checkcap: type,
                    cap_code : code,
                    post_id  : id
                },
                success: function (response) {
                    if(response != '1'){
                         alert(contLDomain.mess_text[3]);
                         refreshCaptchaCont(parent);
                    }else{
                        parent.find('form').submit();
                    }
                }
    });
}
function team_submit_message(parent) {
    function checkEmail() {
        var email = parent.find('.email');
        var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        if (parent.find('.email').length ==0)
            return true;
        else if (!filter.test(email.val())) {
            alert(contLDomain.mess_text[5]);
            email.focus();
            return false;
        }
        else
            return true;
    }
    if (parent.find(".full_name").length > 0 && parent.find(".full_name").val() == '')
    {
        alert(contLDomain.mess_text[0]);
        parent.find(".full_name").focus();
    }
    else
    if (parent.find(".phone").length > 0 && parent.find(".phone").val() == '')
    {
        alert(contLDomain.mess_text[4]);
        parent.find(".phone").focus();
    }
    else
    if (parent.find(".mes_title").val() == '')
    {
        alert(contLDomain.mess_text[2]);
        parent.find(".mes_title").focus();
    }
    else
    if (parent.find(".message_text").val() == '')
    {
        alert(contLDomain.mess_text[1]);
        parent.find(".message_text").focus();
    }
    else
    if (checkEmail()) {
        if(parent.find("#staff_capcha").length > 0) {
            check_captcha(parent, 1, parent.find('.message_capcode').val(), parent.find('.contact_id').val());
        }else if(parent.find(".twd_gcaptcha").length > 0){
            var captchaContainerId = parent.find(".twd_gcaptcha").attr('id');
            var captchaId = twd_captcha_widgets[captchaContainerId];
            if(grecaptcha.getResponse(captchaId) == ''){
                alert('Captcha not valid');
                grecaptcha.reset(captchaId);
            }else{
                parent.find('form').submit();
            }
        }else{
            parent.find('form').submit();
        }
    }
}

function refreshCaptchaCont(parent)
{
    parent.find('.wd_captcha_img').attr('src',parent.find('.wd_captcha_img').attr('src').split("&")[0] + '&post_id='+parent.find('.contact_id').val()+'&r=' + Math.floor(Math.random() * 100));
    parent.find('.message_capcode').val('');
}

var twd_captcha_widgets = {};

function twdOnloadGcaptcha() {
    jQuery('.twd_gcaptcha').each(function (i) {
        twd_captcha_widgets[jQuery(this).attr('id')] = grecaptcha.render(this, {'sitekey': twd_gcaptcha_key})
    })
}
