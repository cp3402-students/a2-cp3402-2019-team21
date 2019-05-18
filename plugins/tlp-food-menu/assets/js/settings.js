(function($) {

    $(document).ready(function () {
        $(".rt-tab-nav li:first-child a").trigger('click');
    });

    if ($("select.tlp-select").length) {
        $("select.tlp-select").select2({
            theme: "classic",
            dropdownAutoWidth: true,
            width: '100%'
        });
    }
    if ($("#scg-wrapper .tlp-color").length) {
        var cOptions = {
            defaultColor: false,
            change: function (event, ui) {
                createShortCode();
            },
            clear: function () {
                createShortCode();
            },
            hide: true,
            palettes: true
        };
        $("#scg-wrapper .tlp-color").wpColorPicker(cOptions);
    }
    $(window).on('load', function () {
        createShortCode();
    });
    $("#scg-wrapper").on('change', 'select,input', function () {
        createShortCode();
    });
    $("#scg-wrapper").on("input propertychange", function () {
        createShortCode();
    });
    function createShortCode() {
        var sc = "[foodmenu",
            cat = [];
        $("#scg-wrapper").find('input[name],select[name],textarea[name]').each(function (index, item) {
           if(this.name == "cat" || this.name == "hide-img"|| this.name == "disable-link"){
               if($(this).is(':checked') && this.name == "cat") {
                   cat.push($(this).val());
               }else if($(this).is(':checked') && this.name == "hide-img"){
                   sc = sc + ' hide-img="true"';
               }else if($(this).is(':checked') && this.name == "disable-link"){
                   sc = sc + ' disable-link="true"';
               }
           }else{
               var v = $(this).val(),
                   name = this.name;
           }
            sc = v ? sc + " " + name + "=" + '"'+ v +'"' : sc;
        });
        if(cat.length > 0){
            sc = sc + ' cat="'+ cat.toString() + '"';
        }
        sc = sc + "]";
        $("#sc-output textarea").val(sc);
    }
    $("#sc-output textarea").on('click', function () {
        $(this).select();
        document.execCommand('copy');
    });
    /* rt tab active navigation */
    $(".rt-tab-nav li").on('click', 'a', function (e) {
        e.preventDefault();
        var $this = $(this),
            container = $this.parents('.rt-tab-container'),
            nav = container.children('.rt-tab-nav'),
            content = container.children(".rt-tab-content"),
            $id = $this.attr('href');
        content.hide();
        nav.find('li').removeClass('active');
        $this.parent().addClass('active');
        container.find($id).show();
    });

    // $( "#tabs" ).tabs();
    // $(".tlpselect").select2();
})(jQuery);

function tlpFmSettingsUpdate(e) {
    jQuery('#response').hide();
    arg = jQuery(e).serialize();
    bindElement = jQuery('#tlpSaveButton');
    AjaxCall(bindElement, 'tlpFmSettingsUpdate', arg, function(data) {
        console.log(data);
        if (!data.error) {
            jQuery('#response').removeClass('error');
            jQuery('#response').show('slow').text(data.msg);
        } else {
            jQuery('#response').addClass('error');
            jQuery('#response').show('slow').text(data.msg);
        }
    });
}

function AjaxCall(element, action, arg, handle) {
    if (action) data = "action=" + action;
    if (arg) data = arg + "&action=" + action;
    if (arg && !action) data = arg;
    data = data;
    var n = data.search("tlp_fm_nonce");
    if (n < 0) {
        data = data + "&tlp_fm_nonce=" + tpl_fm_var.tlp_fm_nonce;
    }
    jQuery.ajax({
        type: "post",
        url: ajaxurl,
        data: data,
        beforeSend: function() {
            jQuery("<span class='tlp_loading'></span>").insertAfter(element);
        },
        success: function(data) {
            jQuery(".tlp_loading").remove();
            handle(data);
        }
    });
}
