(function($){
    function equalHeight() {
        $(".tlp-food-menu").each(function () {
            var maxH = 0;
            $(this).children('div').children(".tlp-equal-height").height('auto');
            $(this).children('div').each(function () {
                var cH = $(this).outerHeight();
                console.log(cH);
                if (cH > maxH) {
                    maxH = cH;
                }
            });
            $(this).children('div').children(".tlp-equal-height").css('height',maxH + "px");
        });
    }
    equalHeight();
    $(window).load(function(){
        equalHeight();
    });
    $(window).resize( function(){
        equalHeight();
    });
})(jQuery);

