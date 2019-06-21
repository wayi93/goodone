/**
 * 回车事件
 */
$(document).keyup(function(e){
    var key = e.which;
    if(key==13){

        // 优先级最高
        layer.closeAll('dialog');

        // For 创建订单页面的产品搜索按钮
        //var key = $('#search-product-keywords-input').val();
        if($('#search-product-keywords-input').is(':focus')){
            searchProductKeyword();
        }

        if($('#search-kunden-Kid').is(':focus') || $('#search-kunden-Knachname').is(':focus') || $('#search-kunden-Kvorname').is(':focus') || $('#search-kunden-Kemail').is(':focus') || $('#search-kunden-Ktel').is(':focus') || $('#search-kunden-Kplz').is(':focus')){
            searchKunden();
        }

    }

});