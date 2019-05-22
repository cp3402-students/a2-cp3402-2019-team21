/**
 * Created by Sales1 on 4/12/2016.
 */
jQuery(document).ready(function($) {
    $('.order_list  tr').addClass("sortable");
    var SC_start_ordering = $('.SC_start_ordering');
    if(SC_start_ordering.length > 0) {
        $('.order_list').sortable({
            'items': 'tr',
            'axis': 'y',
            animation: 150,
            placeholder: "ui-sortable-placeholder",
            'update': function (e, ui) {
                save_ordering();
            }
        });

        $('.select_category').on('change', function() {
            $('.order_list  tr').removeClass("sortable");
            if(this.value == 'Select Category'){
                $('.order_list  tr').addClass("sortable");
                $('.order_list tr').show();
            }else {


                $('.order_list tr').show();
                for (var j = 0; j < $('.single_contact').length; j++) {
                    var hide = true;
                    var data_category = $('.single_contact')[j].dataset.category;
                    var data_elementid = $('.single_contact')[j].dataset.elementid;
                    data_category = data_category.split('{split}');
                    for (i in data_category) {
                        if(data_category[i]!=""){
                            var catrgory = this.value.replace(' ' , '-');
                            if (catrgory == data_category[i]) {
                                hide = false;
                            }
                        }
                    }
                    if (hide) {
                        $('tr[data-elementid="' + data_elementid + '"]').hide();
                    }else{
                        $('tr[data-elementid="' + data_elementid + '"]').addClass("sortable");
                    }
                }
            }
        });




        var order_id = $('.order_id');
        var order_name = $('.order_name');


                         //////////id
        order_id.click(function(){
            var rows = $('.sortable').get();

            rows.sort(function(a, b) {

                var A = $(a).children('td').eq(0).text();
                var B = $(b).children('td').eq(0).text();

                A = parseInt(A);
                B = parseInt(B);

                if(A < B) {
                    return -1;
                }

                if(A > B) {
                    return 1;
                }

                return 0;

            });

            $.each(rows, function(index, row) {
                $('.order_list').append(row);
            });

            save_ordering();
        });

             ///////////name
        order_name.click(function(){
            var rows = $('.sortable').get();

            rows.sort(function(a, b) {

                var A = $(a).children('td').eq(1).text().toUpperCase();
                var B = $(b).children('td').eq(1).text().toUpperCase();

                if(A < B) {
                    return -1;
                }

                if(A > B) {
                    return 1;
                }

                return 0;

            });

            $.each(rows, function(index, row) {
                $('.order_list').append(row);
            });

            save_ordering();
        });

        function save_ordering(){
            var element_count = $('.order_list tr').length;
            var order_data = {};
            for(var i=0 ; i<element_count ; i++){
                var changetId = $('.order_list tr')[i].dataset.elementid;
                order_data[i]=changetId;
            }
            jQuery.post(staff_ajaxurl, {
                action: 'staff_order_contact',
                data:order_data
            }, function (data) {
                console.log(data);
            });
            $('.save_notice').css({'display':'block'});
            $('.notice_fon').css({'display':'block'});
            setTimeout(function(){
                $('.save_notice').css({'display':'none'});
                $('.notice_fon').css({'display':'none'});
            },500);
        }
    }

});
