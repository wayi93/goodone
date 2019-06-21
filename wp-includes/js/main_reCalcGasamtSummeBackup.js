/**
 * 全局变量
 */
var data_persistence_long = 60; //分钟
var groupedProducts = [];
groupedProducts['refresh'] = [];
groupedProducts['grouped-products'] = [];
groupedProducts['un-grouped-products'] = []; // key为ean, value为老孙给的所有数据
groupedProducts['product-top-categorys'] = [];
groupedProducts['master-product-quantity-in-category'] = [];
groupedProducts['refresh']['time'] = '2018-01-01 00:00:00';
/**
 * 订单支付信息价格数据
 */
var orderSummary = {};
orderSummary.nettoPreissumme = 0;
orderSummary.rabatt = 0;
orderSummary.rabattBeiAbholung = 0;
orderSummary.zwischensumme = 0;
orderSummary.mwst = 0;
orderSummary.gesamtsumme = 0;
/**
 * 增值税 0 还是  19
 */
var taxVal = 19;
/**
 * Rabatt & Rabatt bei Abholung
 */
var rabatt = 0;
var rabattBeiAbholung = 0;
/**
 * 全局变量: 国家代码
 */
var countries = [];
/**
 * 全局变量: 被挂起的方法，加入数组，等Ajax返回了结果，再一起执行了。
 */
var suspenedFuncs = [];
/**
 * 全局变量: 确保同一个AJAX,不会同时发出第二次请求
 */
isloadProductsAjaxRunning = false;
isloadProductHistoryAjaxRunning = false;
/**
 * 全局变量: 从E2拿来的客户ID
 */
var e2Data = {};
e2Data.customer_id = 0;
e2Data.customer_userIdPlattform = "NONE";
/**
 * 全局变量: 颜色配色表
 * colors[i][0] 是原色
 * colors[i][1] 是80%透明度
 */
var colors = [
    ['#e4dbcd', '#eae3d8'],
    ['#ff9f47', '#ffb36c'],
    ['#3de0aa', '#60dab2'],
    ['#00c6fa', '#33cbf4'],
    ['#a82969', '#b95487'],
    ['#fff8a2', '#fff9b5'],
    ['#ffcc2f', '#ffd659'],
    ['#efe8dc', '#f3ede4'],
    ['#00c6fa', '#33d1fb'],
    ['#38d19f', '#64e6bb'],
    ['#e5967f', '#eaab99'],
    ['#f0e8dd', '#f2ede3'],
    ['#b4ada1', '#c3bdb4'],
    ['#3de0aa', '#64e6bb'],
    ['#efe7dc', '#f1ece2'],
    ['#ff9d47', '#ffa169'],
    ['#efe7db', '#f2ece3'],
    ['#e4dcce', '#eae3d8']
];
/**
 * 全局变量: 购物车
 */
var shoppingCartArray = {};
if(localStorage.getItem("shopping-cart") !== null){
    shoppingCartArray = JSON.parse(localStorage.getItem("shopping-cart"));
}
/**
 * 全局变量: 订单信息
 */
var orderInfoArray = {};
if(localStorage.getItem("order-info") !== null){
    orderInfoArray = JSON.parse(localStorage.getItem("order-info"));
}else{
    orderInfoArray.versandkosten = 0;
    orderInfoArray.teilbezahlt = 0;
    localStorage.setItem("order-info", JSON.stringify(orderInfoArray));
}
/**
 * 全局变量: 折扣比例
 */
var rabattStufen = [];
/**
 * 全局变量: 搜索用户以后得到的结果数组，排除掉重复项
 */
var searchCustomerResults = [];

/* 转移到 方法 showProducts 里面
var posQuantityInShoppingCart = getJsonLen(shoppingCartArray);
if(posQuantityInShoppingCart > 0){
    // 修改购物车右上角显示的数量
    shopping_cart_pos_quantity_modify(posQuantityInShoppingCart);
}
*/
//console.log(getJsonLen(shoppingCartArray));

/**
 * 全局变量: 搜索产品的池 - 当前一级类别
 * -1 代表所有类别 (全搜索)
 *  0 代表一级分类 "其他"
 *  其他数字分别代表各自的分类
 */
var searchProductPool = -1;



/**
 * 检测是否可以使用 localStorage
 * @type {boolean}
 */
var canLocalStorage = false;
if (typeof(Storage) !== "undefined") {
    canLocalStorage = true;
}
//console.log('localStorage:'+canLocalStorage);

/**
 * 根据【shoppingCartArray】更新购物车里的HTML内容
 */
function updateShoppingCart() {

    shoppingCartArrayLen = getJsonLen(shoppingCartArray);

    // 计数器
    shopping_cart_pos_quantity_modify(shoppingCartArrayLen);

    // 购物车里面的文字
    var htmlTxt = '';
    if(shoppingCartArrayLen < 1){
        htmlTxt = '<i class="fa fa-info-circle"></i>&nbsp;&nbsp;Ihr Warenkorb ist derzeit leer.';
    }else{
        htmlTxt = htmlTxt + '<h3 style="margin-top: 5px !important;">Warenkorb</h3><hr class="line2">';
        for(var k in shoppingCartArray){
            var qic = parseInt(shoppingCartArray[k].qInCart);
            var q = parseInt(shoppingCartArray[k].quantity);
            var ean = shoppingCartArray[k].ean;
            var price = shoppingCartArray[k].price;
            var selectHtml = '<select ';
            if(qic > q){
                selectHtml = selectHtml + 'style="color:#FF0000;"';
            }
            selectHtml = selectHtml + ' class="form-control select-in-cart" onchange=updateShoppingCartPosQuantity(this,"'+ean+'"); onkeyup=updateShoppingCartPosQuantity(this,"'+ean+'"); ><option>0</option>';
            for(var i=1; i<=q; ++i){
                if(i == qic){
                    selectHtml = selectHtml + '<option selected>' + i + '</option>';
                }else{
                    selectHtml = selectHtml + '<option>' + i + '</option>';
                }
            }
            if(qic > q){
                selectHtml = selectHtml + '<option disabled>---</option>';
                selectHtml = selectHtml + '<option selected>' + qic + '</option>';
            }
            selectHtml += '</select>';

            htmlTxt = htmlTxt + '<div class="pos-in-cart-wrap"><div>' + selectHtml + '&nbsp;&nbsp;x&nbsp;&nbsp;<span id="price-span-' + ean + '">' + price + '</span>&nbsp;&euro;&nbsp;(netto)</div><div>EAN:&nbsp' + ean + '</div><div style="color: #9a9a9a;">' + shoppingCartArray[k].title + '</div></div><hr class="line1">';
        }
        htmlTxt = htmlTxt + '<div><span class="btn btn-primary btn-in-cart" onclick="clearShoppingCart();">Alle Artikel löschen</span><br><span class="btn btn-primary btn-in-cart" onclick=redirectAndScrollPage("/order-create/","scrollPageCode","0","add"); >Bestellung weiter anlegen</span></div>';
    }
    $('#warenkorb-sidebar-wrap').html(htmlTxt);

    // 格式化价格
    $('#price-span-' + ean).priceFormat({
        prefix: '',
        allowNegative: true,
        clearPrefix: true,
        clearSuffix: true,
        centsSeparator: ',',
        thousandsSeparator: '.'
    });


    /**
     * 跟随着购物车一起刷新的Block
     */
    if($('#create-order-step-3').length > 0){
        updatePaymentSummary();
    }


}

function updateMwStVal() {
    var mwst = 0;
    if(taxVal > 0){
        mwst = Math.round(orderSummary.zwischensumme * taxVal / 100);
    }
    // 显示
    setInputPrice('input-mwst', mwst);

    orderSummary.mwst = mwst;
}

function updateRabattAbholungVal() {
    // 显示
    setInputPrice('input-rabatt-abholung', getRabattAbholungVal());

    if(rabattBeiAbholung < 1){
        $('#li-rabatt-bei-abholung').css('display', 'none');
    }else{
        $('#li-rabatt-bei-abholung').css('display', 'block');
    }
}

function updateShoppingCartPosQuantity(sObj, e) {
    var old_val = shoppingCartArray[e].qInCart;
    var new_val = sObj.value;
    if(new_val == 0){
        layer.confirm(
            'Möchten Sie diesen Artikel löschen?',
            {
                icon: 3,
                title: 'Warenkorb Tipp: ID-10007',
                btn: ['Nein', 'Ja, löschen']
            },
            function () {
                sObj.value = old_val;
                layer.closeAll('dialog');
            },
            function () {
                // 修改
                delete shoppingCartArray[e];
                // 更新 LS
                localStorage.setItem("shopping-cart", JSON.stringify(shoppingCartArray));
                // 更新购物车
                updateShoppingCart();
                // 取消数字
                //shopping_cart_pos_quantity_modify(0);
                // 关闭窗口
                layer.closeAll('dialog');
            }
        );
    }else{
        layer.confirm(
            'Möchten Sie die Menge von ' + old_val + ' auf ' + new_val + ' ändern?',
            {
                icon: 3,
                title: 'Warenkorb Tipp: ID-10006',
                btn: ['Nein', 'Ja, ändern']
            },
            function () {
                sObj.value = old_val;
                layer.closeAll('dialog');
            },
            function () {
                // 修改
                shoppingCartArray[e].qInCart = new_val;
                // 更新 LS
                localStorage.setItem("shopping-cart", JSON.stringify(shoppingCartArray));
                // 更新购物车
                updateShoppingCart();
                // 关闭窗口
                layer.closeAll('dialog');
            }
        );
    }
}

function clearShoppingCart() {
    layer.confirm(
        'Möchten Sie alle Artikel aus dem Warenkorb löschen?',
        {
            icon: 3,
            title: 'Warenkorb Tipp: ID-10005',
            btn: ['Nein', 'Ja, löschen']
        },
        function (){
            layer.closeAll('dialog');
        },
        function () {
            // 清空JS池
            shoppingCartArray = {};
            // 清除 LS
            localStorage.removeItem("shopping-cart");
            // 更新购物车
            updateShoppingCart();
            // 取消数字
            shopping_cart_pos_quantity_modify(0);
            // 关闭窗口
            layer.closeAll('dialog');
        }
    );
}

/**
 * 把产品加入购物车
 * @param e ean
 */
function addToShoppingCart(e) {

    // 现有库存
    var quantity = groupedProducts['un-grouped-products'][e].quantity;

    // 检查预定数量
    var howMany = $('#input-add-product-quantity-'+e).val();
    if(howMany === undefined){
        layer.open({
            title: 'System Fehler: ID-10001',
            icon: 7,
            content: 'Fehler beim Abrufen der Produktmenge. Bitte kontaktieren Sie die IT-Abteilung bei Sogood GmbH.',
            btn: ['Schließen']
        });
    }else if(isNaN(parseInt(howMany)) || parseInt(howMany) < 1){
        layer.open({
            title: 'Warenkorb Warnung: ID-10013',
            icon: 5,
            content: 'Bitte geben Sie richtige Menge ein.',
            btn: ['Schließen']
        });
    }else{

        /*
        if(parseInt(howMany) > parseInt(quantity)){
            layer.open({
                title: 'Lagerbestand Warnung: ID-10002',
                icon: 5,
                content: 'Auf Lager gibt es momentan nur ' + quantity + ' Stück zur Verfügung.',
                btn: ['Schließen']
            });
        }else{
        */

            //var title = groupedProducts['un-grouped-products'][e].title;
            //var picUrl = groupedProducts['un-grouped-products'][e].picUrl;

            // 商品加入购物车之前，判断里面是否已经有这件商品，如果有，就把数量叠加
            if(shoppingCartArray.hasOwnProperty(e)){
                var qInCart = shoppingCartArray[e].qInCart;
                layer.confirm(
                    'Der Artikel (' + qInCart + ' Stück) ist schon im Warenkorb. Möchten Sie wirklich ' + howMany + ' Stück hinzufügen?',
                    {
                        icon: 3,
                        title: 'Warenkorb Tipp: ID-10003',
                        btn: ['Nein', 'Trotzdem Ja']
                    },
                    function () {
                        layer.closeAll('dialog');
                    },
                    function () {

                        layer.closeAll('dialog');

                        var qInCart_new = parseInt(shoppingCartArray[e].qInCart) + parseInt(howMany);
                        if(parseInt(qInCart_new) > parseInt(quantity)){
                            layer.confirm(
                                'Auf Lager gibt es momentan nur ' + quantity + ' Stück zur Verfügung. Möchten Sie wirklich noch ' + howMany + ' Stück hinzufügen?',
                                {
                                    icon: 3,
                                    title: 'Lagerbestand Warnung: ID-10015',
                                    btn: ['Nein', 'Trotzdem Ja']
                                },
                                function () {
                                    layer.closeAll('dialog');
                                },
                                function () {
                                    addToShoppingCart_real_ext(e, qInCart_new);
                                }
                            );
                        }else{
                            addToShoppingCart_real_ext(e, qInCart_new);
                        }

                    }
                );
            }else{

                // 这件商品，在购物车里面还没有，那么判断要添加的数量，是不是大于库存量
                if(parseInt(howMany) > parseInt(quantity)){
                    layer.confirm(
                        'Auf Lager gibt es momentan nur ' + quantity + ' Stück zur Verfügung. Möchten Sie wirklich ' + howMany + ' Stück hinzufügen?',
                        {
                            icon: 3,
                            title: 'Lagerbestand Warnung: ID-10015',
                            btn: ['Nein', 'Trotzdem Ja']
                        },
                        function () {
                            layer.closeAll('dialog');
                        },
                        function () {
                            addToShoppingCart_real_notExt(e, howMany);
                        }
                    );
                }else{
                    addToShoppingCart_real_notExt(e, howMany);
                }

            }

        /*}*/

    }

}

/**
 * [添加产品进购物车] 如果购物车里面已经存在这件商品
 * @param e ean
 * @param h 购物车里面，这件Artikel 新的数量
 */
function addToShoppingCart_real_ext(e, h) {

    // 动画
    addToShoppingCartAnimation();

    shoppingCartArray[e].qInCart = h;
    localStorage.setItem("shopping-cart", JSON.stringify(shoppingCartArray));
    updateShoppingCart();

}

/**
 * [添加产品进购物车] 如果购物车里面没有这件商品
 * @param e ean
 * @param h 多少件产品要添加进去
 */
function addToShoppingCart_real_notExt(e, h) {
    var quantity = groupedProducts['un-grouped-products'][e].quantity;
    var title = groupedProducts['un-grouped-products'][e].title;
    var picUrl = groupedProducts['un-grouped-products'][e].picUrl;
    var price = groupedProducts['un-grouped-products'][e].price;
    var shippingCost = groupedProducts['un-grouped-products'][e].shippingCost;

    // 动画
    addToShoppingCartAnimation();

    shoppingCartArray[e] = {};
    shoppingCartArray[e].ean = e;
    shoppingCartArray[e].title = title;
    shoppingCartArray[e].quantity = quantity;
    shoppingCartArray[e].price = price;
    shoppingCartArray[e].shippingCost = shippingCost;
    shoppingCartArray[e].picUrl = picUrl;
    shoppingCartArray[e].qInCart = h;
    localStorage.setItem("shopping-cart", JSON.stringify(shoppingCartArray));

    //console.log(JSON.stringify(shoppingCartArray));

    // 修改购物车右上角显示的数量
    shopping_cart_pos_quantity_modify(getJsonLen(shoppingCartArray));

    updateShoppingCart();
}

/**
 * 修改小图标的产品数量
 * @param q
 */
function shopping_cart_pos_quantity_modify(q) {
    var obj = $('#shopping-cart-pos-quantity');
    if(q > 0){
        obj.addClass("label label-warning");
        obj.html(q);
    }else{
        obj.removeClass();
        obj.html('');
    }
}

function addToShoppingCartAnimation() {
    var pic_url = "/wp-content/uploads/images/icons-product-for-cart.png";
    var offset = $("#icon-shopping-cart-rt").offset();
    var flyer = $('<img class="u-flyer" src="' + pic_url + '"/>');

    // 确定位置
    var mPos = getMousePos();
    var sPos = getScrollPos();
    var fly_start_left = mPos.x - sPos.x - 40;
    var fly_start_top = mPos.y - sPos.y - 40;
    var fly_end_left = offset.left - sPos.x;
    var fly_end_top = offset.top - sPos.y;
    //console.log('x:'+mPos.x+';y:'+mPos.y);

    flyer.fly({
        start: {
            left: fly_start_left,
            top: fly_start_top
        },
        end: {
            left: fly_end_left,
            top: fly_end_top,
            width: 0,
            height: 0
        }
    });
}

/**
 * 拿到鼠标位置
 * @param event
 * @returns {{x: (Number|number), y: (Number|number)}}
 */
function getMousePos(event) {
    var e = event || window.event;
    var scrollX = document.documentElement.scrollLeft || document.body.scrollLeft;
    var scrollY = document.documentElement.scrollTop || document.body.scrollTop;
    var x = e.pageX || e.clientX + scrollX;
    var y = e.pageY || e.clientY + scrollY;
    return { 'x': x, 'y': y };
}

/**
 * 获得页面滚动距离
 * @returns {{x: number, y: number}}
 */
function getScrollPos() {
    var scrollX = document.documentElement.scrollLeft || document.body.scrollLeft;
    var scrollY = document.documentElement.scrollTop || document.body.scrollTop;
    return { 'x': scrollX, 'y': scrollY };
}

/**
 * 从老孙的E2系统里面读取产品数据
 * @param pageId 页面的Link，标识服务于哪个页面
 *               如果pageId是"BackendOnly",那么说明不需要显示什么到前台页面上
 */
function loadProducts(pageId) {
    // 判断浏览器是否支持 localStorage
    if(canLocalStorage){
        // localStorage 中是否已经存有数据
        if(localStorage.getItem("grouped-products") !== null && localStorage.getItem("products-refresh-time") !== null){
            // 判断 localStorage 的数据是否已经过期
            var data_d = new Date(localStorage.getItem("products-refresh-time"));
            var d = new Date();
            var diffT = diffTime(data_d, d, 'minute');
            if(diffT <= data_persistence_long){
                console.log('Product data was refreshed ' + diffT + ' minutes ago, data will be refreshed in ' + (data_persistence_long - diffT) + ' minutes again.');
                groupedProducts['grouped-products'] = JSON.parse(localStorage.getItem("grouped-products"));
                groupedProducts['un-grouped-products'] = JSON.parse(localStorage.getItem("un-grouped-products"));
                groupedProducts['product-top-categorys'] = JSON.parse(localStorage.getItem("product-top-categorys"));
                groupedProducts['master-product-quantity-in-category'] = JSON.parse(localStorage.getItem("master-product-quantity-in-category"));
                groupedProducts['refresh']['time'] = data_d;
                showProducts(pageId);
            }else {
                loadProductsAjax(pageId);
            }
        }else{
            loadProductsAjax(pageId);
        }
    }else{
        //loadProductsAjax(pageId);
        layer.open({
            title: 'System Fehler: ID-10008',
            icon: 7,
            content: 'Ihr Web-Browser unterstützt LocalStorage leider nicht. Bitte kontaktieren Sie die IT-Abteilung bei Sogood GmbH.',
            btn: ['Schließen']
        });
    }
}

/**
 * 根据不同的页面，选择不同的产品显示方法
 * @param pageId 页面的Link，标识服务于哪个页面
 */
function showProducts(pageId) {
    switch (pageId){
        case "product-list":
            showProducts_One(-1);
            break;
        case "product-overview-0":
            setProductPieChart(0, "productPieChart-0");
            break;
        case "product-overview-1":
            setProductPieChart(1, "productPieChart-1");
            break;
        case "order-create":
            showCreateOrderSearchProductBlock();
            break;
        case "dashboard-0":
            removeLoadingLayer();
            layer.open({
                title: 'Produkt Tipp: ID-10009',
                icon: 1,
                content: 'Die Produkt-Daten wurden erfolgreich aktualisiert.',
                btn: ['Schließen']
            });
            break;
        case "BackendOnly":
            //
            break;
        default:
            // NO DEFAULT VALUE
    }

    var posQuantityInShoppingCart = getJsonLen(shoppingCartArray);
    if(posQuantityInShoppingCart > 0){
        // 修改购物车右上角显示的数量
        shopping_cart_pos_quantity_modify(posQuantityInShoppingCart);
    }
}

/**
 * Load完产品数据以后，在创建订单的页面，在第一步查找产品里面，显示两种搜索产品的方式
 */
function showCreateOrderSearchProductBlock() {

    var t_c = groupedProducts['product-top-categorys'];

    var htmlTxt = '<h4>Methode 1: Stichwort suchen ( wie google )</h4>' +
        '          <div class="input-group">' +
                       // 按钮
        '              <div class="input-group-btn">' +
        '                  <button id="order-create-keyword-search-btn" type="button" class="btn btn-primary">Suchen</button>' +
        '              </div>' +
                       // 输入框
        '              <input id="search-product-keywords-input" type="text" class="form-control" placeholder="Sie können einen oder mehrere durch Leerzeichen getrennte Suchbegriffe eingeben." />' +
                       // in
        '              <span class="input-group-addon">in</span>' +
                       // 下拉菜单
        '              <div class="input-group-btn">' +
        '                  <button id="searchProductPoolBtn" type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">';
    if(searchProductPool < 0){
        htmlTxt += 'Alle Kategorien';
    }else{
        htmlTxt += t_c[searchProductPool];
    }
    htmlTxt = htmlTxt + '<span class="fa fa-caret-down" style="margin-left:8px;"></span></button>' +
        '                  <ul class="dropdown-menu" style="margin-left: -100px; min-width: 230px;">';

    // 产品分类选项
    htmlTxt += '<li class="setSearchProductPoolOption"><a onclick="setSearchProductPool(-1);">Alle Kategorien</a></li>';
    for(var i = 1; i<t_c.length; ++i){
        htmlTxt = htmlTxt + '<li class="setSearchProductPoolOption"><a onclick="setSearchProductPool(' + i + ');">' + t_c[i] + '</a></li>';
    }
    htmlTxt = htmlTxt + '<li class="setSearchProductPoolOption"><a onclick="setSearchProductPool(0);">' + t_c[0] + '</a></li>';


    htmlTxt = htmlTxt + '                  </ul>' +
        '              </div>' +
        '          </div>';

    // 搜索的结果显示在这里
    htmlTxt = htmlTxt + '<div id="order-create-product-search-result-wrap"><div class="box"><div class="box-header"><h3 class="box-title">Suchergebnisse</h3></div><div class="box-body"></div></div></div>';

        // 方式 2
    htmlTxt = htmlTxt + '          <h4>Methode 2: Liste suchen ( Katagorie -> Produkt -> Variable )</h4>' +
        '          <button id="btn-link-to-product-list" type="button" class="btn btn-primary" onclick=location.href="/product-list/";>Link zur Produktliste-Seite</button>';
    $('#order-create-step-1-body').html(htmlTxt);

    // 按钮事件
    var ocksb = document.getElementById("order-create-keyword-search-btn");
    if(ocksb.addEventListener)
        ocksb.addEventListener("click",searchProductKeyword,false);
    if(ocksb.attachEvent)
        ocksb.attachEvent("onclick",searchProductKeyword);


}

/**
 * 把关键词打散成Array (通过空格)
 * 在产品类别的池中查找 var searchProductPool
 * 最多显示10条结果
 */
function searchProductKeyword() {

    var isError = false;
    var key = $('#search-product-keywords-input').val();

    if(key !== ''){

        var keys_linshi = removeSameKey(removeInvalidKey(key.split(" ")));
        //console.log(keys_linshi);

        if(keys_linshi.length > 0){

            // 显示loading
            showLoadingLayer();

            searchAndShowProducts_Ajax(function(results){});

        }else{

            isError = true;

        }


    }else {

        isError = true;

    }

    if(isError){

        $('#search-product-keywords-input').blur();
        $('#order-create-product-search-result-wrap').css('display','none');
        layer.open({
            title: 'Suchen Fehler: ID-10012',
            icon: 5,
            content: 'Die Stichwortlänge vom jedem Stichwort muss mindestens 2 Zeichen betragen.',
            btn: ['Schließen']
        });

    }


}


/**
 * 整理产品搜索结果的功能，耗时太长，写成异步操作
 */
function searchAndShowProducts_Ajax() {


    setTimeout(function () {


    var key = $('#search-product-keywords-input').val();
    var keys_linshi = removeSameKey(removeInvalidKey(key.split(" ")));

    $('#order-create-product-search-result-wrap').css('display','block');
    //$("body").css('cursor', 'progress');

    // 重写客户的 Input
    var iptNewTxt = '';
    for(var i=0; i<keys_linshi.length; ++i){
        iptNewTxt += keys_linshi[i];
        iptNewTxt += ' ';
    }
    $('#search-product-keywords-input').val(iptNewTxt);

    // 把元素按照从长到短排序，为了适合搜索
    var keys = sortByKeyLen(keys_linshi, 2);
    //console.log(keys);

    /**
     * 根据大类整理 池
     * 情况1: 所有大类产品
     * 情况2: 某一个大类的产品
     */
    var pool = [];
    var poolFrom = groupedProducts['un-grouped-products'];
    for(var k in poolFrom){
        poolFrom[k].forSearch = (poolFrom[k].ean + '::' + poolFrom[k].title).toLowerCase();
        if(searchProductPool < 0){
            pool.push(poolFrom[k]);
        }else{
            var category_of_pool = groupedProducts['product-top-categorys'][searchProductPool];
            var category_of_item = getTopCategoryByProductName(poolFrom[k].category);
            if(category_of_pool === category_of_item){
                pool.push(poolFrom[k]);
            }
        }
    }
    //console.log(pool);

    /**
     * 从池子里面，根据关键词匹配出 前10名
     */
    var resArray = [];
    for(var j=0; j<pool.length; ++j){

        var forSearchTxt = pool[j].forSearch;
        //console.log(forSearchTxt + ' >>> ' + forSearchTxt.search(keys[0].toLowerCase()));
        var isNeed = true;

        for(var z=0; z<keys.length; ++z){
            var kWord = keys[z].toLowerCase();
            if(forSearchTxt.indexOf(kWord) > -1){
                forSearchTxt = forSearchTxt.replace(new RegExp(kWord,'g'), '');
            }else{
                isNeed = false;
            }
        }

        if(isNeed){
            resArray.push(pool[j]);

            /**
             * 控制，搜索，只出10条结果最多
             */
            //if(resArray.length > 9){
            //break;
            //}

        }

    }
    //console.log(resArray);

    showSearchResultTableInPageCreateOrder(resArray, keys);




    }, 1000);



}


/**
 * 把下单页面查找产品的结果显示出来
 */
function showSearchResultTableInPageCreateOrder(pList, keys) {
    var elems = pList;
    var htmlTxt = '<div class="box">' +
        '            <div class="box-header">' +
        '              <h3 class="box-title">Suchergebnisse ( ';
    htmlTxt += elems.length;
    htmlTxt += ' Stück gefunden )</h3>' +
        '            </div>' +
        '            <!-- /.box-header -->' +
        '            <div class="box-body">' +
        '              <table id="t-create-order-search-result" class="table table-bordered table-hover">' +
        '                <thead>' +
        '                <tr>' +
        '                  <th></th>' +
        '                  <th>EAN</th>' +
        '                  <th>Produktname</th>' +
        '                  <th>Verfügbar</th>' +
        '                  <th>Preis (Netto)</th>' +
        '                  <th>Notsent</th>' +
        '                </tr>' +
        '                </thead>' +
        '                <tbody>';

    for(var i = 0; i<elems.length; ++i){

        var actRowHtml = '';
        if(/*parseInt(elems[i]["quantity"]) > 0 && */parseFloat(elems[i]["price"]) >= 0){
            actRowHtml = '<input id="input-add-product-quantity-' + elems[i]["ean"] + '" class="input-add-product-quantity" value="1"/><span class="btn btn-primary btn-add-product-quantity" onclick=addToShoppingCart("' + elems[i]["ean"] + '"); >Add</span>';
        }else{
            actRowHtml = '<input class="input-add-product-quantity" value="0" disabled="disabled" /><span class="btn btn-danger btn-add-product-quantity" onclick=customAlert("Lagerbestand&nbsp;Warnung:&nbsp;ID-10014",5,"Der&nbsp;Preis&nbsp;des&nbsp;Produkts&nbsp;ist&nbsp;ungültig."); >Add</span>';
        }

        var res_ean = elems[i]["ean"];
        var res_title = elems[i]["title"];
        for(var ki = 0; ki < keys.length; ++ki){
            res_ean = res_ean.replace(new RegExp(keys[ki],'gi'), '<span class="highlightForSearchResult">' + keys[ki] + '</span>');
            res_title = res_title.replace(new RegExp(keys[ki],'gi'), '<span class="highlightForSearchResult">' + keys[ki] + '</span>');
        }

        htmlTxt = htmlTxt + '<tr>' +
            '<td>' + actRowHtml + '</td>' +
            '<td>' + res_ean + '</td>' +
            '<td>' + res_title + '</td>' +
            '<td>' + elems[i]["quantity"] + '</td>' +
            '<td>' + formatThePriceYing(elems[i]["price"], "EUR") + '</td>' +
            '<td>' + elems[i]["notsent"] + '</td>' +
            '</tr>';
    }

    htmlTxt += '                </tbody>' +
        '              </table>' +
        '            </div>' +
        '            <!-- /.box-body -->' +
        '          </div>';

    $('#order-create-product-search-result-wrap').html(htmlTxt);

    // 表格属性
    $('#t-create-order-search-result').DataTable({
        "columns": [
            { "orderable": false },
            null,
            null,
            null,
            null,
            null
        ],
        'paging'      : true,
        'lengthChange': false,
        'searching'   : false,
        'ordering'    : true,
        'info'        : true,
        'autoWidth'   : true,
        "language": {
            "lengthMenu": "Auf jeder Seite maximal _MENU_ Produkte anzeigen.",
            "zeroRecords": "Kein Produkt gefunden.",
            "info": "Seite _PAGE_ von _PAGES_",
            "infoEmpty": "Kein Produkt gefunden.",
            "infoFiltered": "(filtered from _MAX_ total records)",
            "search": "Suche:",
            "paginate": {
                "first": "Erste Seite",
                "last": "Letzte Seite",
                "next": "Nächste Seite",
                "previous": "Vorherige Seite"
            }
        }
    });

    // 页面弹回顶部
    //returnPageTop();

    removeLoadingLayer();
}

function setSearchProductPool(i) {
    var t_c = groupedProducts['product-top-categorys'];
    searchProductPool = i;
    $('#searchProductPoolBtn').html(t_c[searchProductPool] + '<span class="fa fa-caret-down" style="margin-left:8px;"></span>');
    $('#search-product-keywords-input').focus();
}

/**
 * 根据数组里面，每一个值的字符串长度排序
 * @param list
 * @param t   1:从短到长 2:从长到短
 * @returns {Array}
 */
function sortByKeyLen(list, t) {
    var listLen = list.length;
    for(var i=0; i<(listLen-1); ++i){
        for(var j=0; j<(listLen-1-i); ++j){
            var len1 = list[j].length;
            var len2 = list[j+1].length;
            var shouldBeChanged = false;
            if(t == 1){
                if(len1 > len2){
                    shouldBeChanged = true;
                }
            }else{
                if(len1 < len2){
                    shouldBeChanged = true;
                }
            }

            // 交换
            if(shouldBeChanged){
                var linshi = list[j];
                list[j] = list[j+1];
                list[j+1] = linshi;
            }
        }
    }
    return list;
}

/**
 * 删除无效的key
 * key的长度最短为2
 */
function removeInvalidKey(list) {
    var invalidKeys = [];
    for(var i=0; i<list.length; ++i){
        if(list[i].length < 2){
            if(list[i].length > 0){
                invalidKeys.push(list[i]);
            }
            list.splice(i,1);
            --i;
        }
    }
    if(invalidKeys.length > 0) {
        var invalidKeysStr = '';
        var invalidKeysLen = invalidKeys.length;
        for(var z=0; z<invalidKeysLen; ++z){
            if(z > 0 && invalidKeysLen > 1){
                invalidKeysStr += ', ';
            }
            invalidKeysStr = invalidKeysStr + '[' + invalidKeys[z] + ']';
        }

        var msg_part_1 = 'Das folgende Stichwort wurde ignoriert:';
        if(invalidKeysLen > 1){
            msg_part_1 = 'Die folgende Stichworte wurden ignoriert:';
        }

        var msg_part_2 = 'Die Stichwortlänge muss mindestens 2 Zeichen betragen.';

        layer.open({
            title: 'Suchen Tipp: ID-10011',
            icon: 6,
            content: msg_part_1 + '<br>' + invalidKeysStr + '<br>' + msg_part_2,
            btn: ['Schließen']
        });
    }
    return list;
}

/**
 * js 数组去除重复对象
 * @param list
 * @returns {Array.<T>}
 */
/*
function removeSameKey(list){
    return list.concat().sort().filter(function(item, index, list){
        return !index || item !== list[index - 1];
    });
}
*/
function removeSameKey(list){
    var new_list = [];
    for(var i=0; i<list.length; ++i){
        if(!isItemInArray(list[i], new_list)){
            new_list.push(list[i]);
        }
    }
    return new_list;
}

/**
 * 判断一个元素是否在数组里面
 * @param itm
 * @param list
 * @returns {boolean}
 */
function isItemInArray(itm, list) {
    var isIIA = false;
    for(var i=0; i<list.length; ++i){
        if(itm == list[i]){
            isIIA = true;
        }
    }
    return isIIA;
}

/**
 * 返回两个时间的间隔(默认单位：秒)
 * @param date1
 * @param date2
 * @param unit_of_time
 * @returns {Number} int
 */
function diffTime(date1, date2, unit_of_time) {
    var dt = 0;
    switch (unit_of_time){
        case 'second':
            dt = Math.abs(date2-date1)/1000;
            break;
        case 'minute':
            dt = Math.abs(date2-date1)/1000/60;
            break;
        case 'hour':
            dt = Math.abs(date2-date1)/1000/60/60;
            break;
        default:
            dt = Math.abs(date2-date1)/1000;
    }
    return parseInt(dt);
}

/**
 * loadProductsAjax
 * @param pageId 页面的Link，标识服务于哪个页面
 */
function loadProductsAjax(pageId) {
    if(!isloadProductsAjaxRunning){
        isloadProductsAjaxRunning = true;

        console.log('Loading Products from E2 start ...');
        $.ajax({
            url:'/api/getproductsinentelliship',
            data:{},
            type:'post',
            cache:false,
            dataType:'json',
            success:function(data) {
                if(data.name == 'successful'){
                    var products = data.ng_products;

                    /**
                     * 把价格，解析为价格和运费
                     * 老孙发过来的格式 [price]24.95|0.0
                     * 我需要的格式 [price]24.95 [shippingCost]0.0
                     */
                    products = getJieXiPrice(products);

                    console.log(products.length + ' Products were loaded ' + data.name + '.');

                    var unGroupedProducts = getUnGroupedProducts(products);
                    var groupedProducts = getGroupedProducts(products);
                    var productTopCategorys = getProductTopCategorys(groupedProducts);
                    var masterProductQuantityInCategorys = getMasterProductQuantityInCategorys(groupedProducts);
                    setGroupedProductsToLocal(groupedProducts, productTopCategorys, masterProductQuantityInCategorys, unGroupedProducts);
                    showProducts(pageId);

                    // 处理掉挂起的程序
                    if(suspenedFuncs.length > 0){
                        for(var i=0; i<suspenedFuncs.length; ++i){
                            showProducts(suspenedFuncs[i]["pageId"]);
                        }
                    }
                    // 清空挂起程序的数组
                    suspenedFuncs = [];
                }
                isloadProductsAjaxRunning = false;
            },
            error : function() {
                // view("异常！");
                // alert("异常！");
            }
        });
    }else{
        // Ajax 程序运行中，其他程序需要挂起
        var suspenedFunc = [];
        suspenedFunc["pageId"] = pageId;
        suspenedFuncs.push(suspenedFunc);
    }
}

/**
 * 把价格，解析为价格和运费
 * 老孙发过来的格式 [price]24.95|0.0
 * 我需要的格式 [price]24.95 [shippingCost]0.0
 * @param pList
 * @returns {{}}
 */
function getJieXiPrice(pList) {
    var res = [];
    console.log('Separate price start ...');
    for(var i=0; i<pList.length; ++i){
        var original_price = pList[i].price;
        if(original_price.toString().indexOf("|") != -1){
            var op_arr = original_price.split("|");
            pList[i].price = Math.round((op_arr[0] * 100 / 119) * 100) / 100;
            pList[i].shippingCost = 0;
        }else{
            pList[i].price = Math.round((pList[i].price * 100 / 119) * 100) / 100;
            pList[i].shippingCost = 0;
        }
        res.push(pList[i]);
    }
    console.log('Separate price finish.');
    return res;
}

/**
 * 得到所有顶级分类的名称
 * @param gpList
 * @returns {Array}
 */
function getProductTopCategorys(gpList) {
    var res = [];
    for(var k in gpList){
        var tc = gpList[k]["topCategory"];
        if(res.indexOf(tc) > -1){
            //
        }else{
            res.push(tc);
        }
    }
    res.sort();
    return res;
}

/**
 * 拿到每个顶级大类里面  有多少个Master产品
 * @param l
 * @returns {Array}
 */
function getMasterProductQuantityInCategorys(l) {
    var res = {};
    var allCT = 0;
    for(var k in l){
        var tc = l[k]["topCategory"];
        if(checkIsKeyInArray(tc, res)){
            res[tc] = res[tc] + 1;
        }else{
            res[tc] = 1;
        }

        ++allCT;
    }
    res["Alle Kategorien"] = allCT;
    return res;
}

function setGroupedProductsToLocal(gps, ptcs, mpqic, ungps) {
    var d = new Date();
    // 保存到JS临时缓冲区
    groupedProducts['grouped-products'] = gps;
    groupedProducts['un-grouped-products'] = ungps;
    groupedProducts['product-top-categorys'] = ptcs;
    groupedProducts['master-product-quantity-in-category'] = mpqic;
    groupedProducts['refresh']['time'] = d;
    // 保存到localStorage
    if(canLocalStorage){
        localStorage.setItem("grouped-products", JSON.stringify(gps));
        localStorage.setItem("un-grouped-products", JSON.stringify(ungps));
        localStorage.setItem("product-top-categorys", JSON.stringify(ptcs));
        localStorage.setItem("master-product-quantity-in-category", JSON.stringify(mpqic));
        localStorage.setItem("products-refresh-time", d);
    }
}

function getUnGroupedProducts(pList) {
    var res = {};
    console.log('Loading all Products start ...');
    for(var i=0; i<pList.length; ++i){
        var pEAN = pList[i]["ean"];
        res[pEAN] = pList[i];
    }
    console.log('Loading all Products finish.');
    return res;
}

function getGroupedProducts(pList) {
    console.log('Grouping Products start ...');
    var res = {};
    for(var i=0; i<pList.length; ++i){
        if(typeof pList[i]["category"] === 'object'){
            pList[i]["category"] = 'ZUB';
        }
        var product_category = getRealProductCategoryByAbbr(pList[i]["category"]);
        if(!checkIsKeyInArray(product_category, res)){
            res[product_category] = {};
            res[product_category].category_pic = '';
            res[product_category].notsent = 0;
            res[product_category].quantity = 0;
            res[product_category].elems = [];
            res[product_category].topCategory = '';
        }
        if(res[product_category].category_pic === '' && typeof pList[i]["picUrl"] === 'string'){
            res[product_category].category_pic = pList[i]["picUrl"];
        }
        res[product_category].notsent += parseInt(pList[i]["notsent"]);
        res[product_category].quantity += parseInt(pList[i]["quantity"]);
        res[product_category].elems.push(pList[i]);
        res[product_category].topCategory = getTopCategoryByProductName(pList[i]["category"]);
    }
    console.log('Grouping Products finish.');
    console.log('Sorting Groups start ...');
    res = objKeySort(res);
    console.log('Sorting Groups finish.');
    return res;
}

function getRealProductCategoryByAbbr(abbr) {
    var res = abbr;
    switch (abbr){
        case 'Test':
            res = 'Test';
            break;
        default:
            //
    }
    return res;
}

function checkIsKeyInArray(k, a) {
    var res = false;
    if(a.hasOwnProperty(k)){
        res = true;
    }
    return res;
}

/**
 * product-list 页面 显示产品列表 One
 * @param ptc 产品顶级目录 Product Top Category
 */
function showProducts_One(ptc_id) {
    var t_c = groupedProducts['product-top-categorys'];
    var ptc = 'Alle Kategorien';
    if(ptc_id > -1){
        ptc = t_c[ptc_id];
    }
    var p = groupedProducts['grouped-products'];
    var mpqic = groupedProducts['master-product-quantity-in-category'];

	// Info: Die Produkt-Daten wurden um 9:32 aktualisiert.
    var akt_date = new Date(groupedProducts['refresh']['time']);
    var htmlTxt = '<div class="callout callout-info"><p>Die Produkt-Daten wurden um ' + akt_date.getHours() + ':' + (akt_date.getMinutes()<10?'0':'') + akt_date.getMinutes() + ' aktualisiert.</p></div>';

    // Filter
    htmlTxt += '<div id="productlist-filter-btn-wrap">' +
            '<div class="btn-group width100per">' +
                '<button type="button" class="btn btn-default" style="width: calc(100% - 27px)">' + ptc + '&nbsp;[' + (mpqic.hasOwnProperty(ptc)?mpqic[ptc]:"0") + ']' + '</button>' +
                '<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">' +
                    '<span class="caret"></span>' +
                    '<span class="sr-only">Toggle Dropdown</span>' +
                '</button>' +
                '<ul class="dropdown-menu" role="menu" style="width: calc(100% - 27px)">' +
                    '<li><a href="#" onclick=showProducts_One(-1)>Alle Kategorien [' + (mpqic.hasOwnProperty("Alle Kategorien")?mpqic["Alle Kategorien"]:"0") + ']</a></li>';

                    for(var i = 1; i<t_c.length; ++i){
                        htmlTxt = htmlTxt + '<li><a href="#" onclick=showProducts_One(' + i + ')>' + t_c[i] + ' [' + (mpqic.hasOwnProperty(t_c[i])?mpqic[t_c[i]]:"0") + ']</a></li>';
                    }
                    htmlTxt = htmlTxt + '<li><a href="#" onclick=showProducts_One(0)>' + t_c[0] + ' [' + (mpqic.hasOwnProperty(t_c[0])?mpqic[t_c[0]]:"0") + ']</a></li>';

     htmlTxt += '</ul>' +
            '</div>' +
        '</div>';


     // 显示产品
    htmlTxt += '<div id="master-product-box-wrap">';
	for(var k in p){
	    if(typeof k === 'string'){

	        //判断这个产品是否展出
            var isShow = false;
            if(ptc_id < 0){
                isShow = true;
            }else{
                if(p[k]["topCategory"] === ptc){
                    isShow = true;
                }
            }

            if(isShow){

                var n = parseInt(p[k]["notsent"]);
                var q_aufLager = parseInt(p[k]["quantity"]);
                // 这里的quantity -> q_aufLager已经不包含Notsent了
                var q = q_aufLager + n;

                // 产品图
                var cate_pic_url = '/wp-content/uploads/images/cate_pic_404.png';
                if(p[k]["category_pic"] !== ''){
                    cate_pic_url = p[k]["category_pic"];
                }

                var perList = float2Per((parseFloat(n) / parseFloat(q)), 2);
                if(parseFloat(q) === 0){
                    if(parseFloat(n) === 0){
                        perList["per_float"] = 0;
                        perList["per_str"] = '0%';
                        perList["per_str_complement"] = '100%';
                    }else{
                        perList["per_float"] = 1.0;
                        perList["per_str"] = '100%';
                        perList["per_str_complement"] = '0%';
                    }
                }

                var bgColor = getColorCodeByFloat(parseFloat(perList["per_float"]), 0);

                htmlTxt = htmlTxt + '<div class="info-box master-product-box bg-' +
                    bgColor +
                    '" onclick=showProduktVariante("' + k + '");>' +
                    '<span class="info-box-icon" style="line-height: 0 !important;">' +
                    '<img class="lazyload product-img" src="/wp-content/uploads/images/loading-camera.svg" data-original="' + cate_pic_url + '" width="100%" height="100%" />' +
                    '</span>' +
                    '<div class="info-box-content">' +
                    '<span class="info-box-text">' + k.split("-")[1] + '</span>' +
                    '<span class="info-box-number">' + q_aufLager + ' Stück verfügbar</span>' +
                    '<div class="progress">' +
                    '<div class="progress-bar" style="width: ' + perList["per_str_complement"] + ';"></div>' +
                    '</div>' +
                    '<span class="fLeft progress-description">';
                if(n < 1){
                    htmlTxt += "Keine Notsent.";
                }else if(n === 1){
                    htmlTxt += "1 Stück ist Notsent.";
                }else{
                    htmlTxt += n;
                    htmlTxt += " Stück sind Notsent.";
                }
                htmlTxt = htmlTxt + '</span>' +
                    '<span class="fRight">' +
                    perList["per_str"] +
                    '</span>' +
                    '</div>' +
                    '</div>';

            }


        }
	}
	htmlTxt += '</div>';
	$('#master-pros-wrap').html(htmlTxt);
    //Lazyload
    $("img.lazyload").lazyload({
        effect:'fadeIn'
    });
}

/**
 * 排序的函数
 * @param arys
 * @returns {{}}
 */
function objKeySort(arys) {
    //先用Object内置类的keys方法获取要排序对象的属性名，再利用Array原型上的sort方法对获取的属性名进行排序，newkey是一个数组
    var newkey = Object.keys(arys).sort();
    //console.log('newkey='+newkey);
    var newObj = {}; //创建一个新的对象，用于存放排好序的键值对
    for(var i = 0; i < newkey.length; i++) {
        //遍历newkey数组
        newObj[newkey[i]] = arys[newkey[i]];
        //向新创建的对象中按照排好的顺序依次增加键值对

    }
    return newObj; //返回排好序的新对象
}

/**
 * 小数转换成百分数
 * @param f 小数
 * @param num 小数点后保留多少位
 * @returns string
 */
function float2Per(f, num) {
    var res = [];
    var res_str = '';
    var res_float = 0;
    var t = (Math.round(f * 100 * Math.pow(10, num))).toString();
    if(t.length < (2 + num)){
        switch ((2 + num) - t.length){
            case 1:
                t = "000" + t;
                break;
            case 2:
                t = "00" + t;
                break;
            case 3:
                t = "0" + t;
                break;
            case 4:
                t = "" + t;
                break;
            default:
                //
        }
    }
    res_float = parseFloat(t.substring(0, (t.length - 2)) + '.' + t.substring((t.length - 2), (t.length)));
    res_str = res_float.toString() + '%';

    res["per_float"] = (res_float / 100);
    res["per_str"] = res_str;
    res["per_str_complement"] = (100 - res_float).toString() + '%';

    return res;
}

/**
 * 自定义产品大类
 * @param pn Xiaobo 在 E2 里面定义的 Category，实际上是产品名称
 */
function getTopCategoryByProductName(pn) {
    var tc = '';
    if(pn.indexOf('WP-') > -1){
        tc = 'Badewannen';
    }else if(pn.indexOf('Schiebetür-') > -1){
        tc = 'Glasschiebetüren';
    }else if(pn.indexOf('Walk_In-') > -1){
        tc = 'Walk-In-Duschen';
    }else if(pn.indexOf('VDG-') > -1){
        tc = 'Vordächer';
    }else if(pn.indexOf('Ersatz-') > -1){
        tc = 'Ersatzteil';
    }else if(pn.indexOf('Dusche-') > -1){
        tc = 'Duschkabinen';
    }else if(pn.indexOf('Nische-') > -1){
        tc = 'Duschnischen';
    }else if(pn.indexOf('DT-') > -1){
        tc = 'Duschtassen';
    }else if(pn.indexOf('WB-') > -1){
        tc = 'Waschbecken';
    }else if(pn.indexOf('WC-') > -1){
        tc = 'Toiletten';
    }else if(pn.indexOf('BM-') > -1){
        tc = 'Badmöbel';
    }else if(pn.indexOf('Armatur-') > -1){
        tc = 'Armaturen';
    }else if(pn.indexOf('Zub-') > -1){
        tc = 'Badzubehör';
    }else{
        tc = 'Andere Produkte';
    }
    return tc;
}

/**
 * 通过Float值得到颜色代码
 * @param f 数字，通常小于1
 * @param t 方案，例如大于多少就什么颜色
 * @returns {string}
 */
function getColorCodeByFloat(f, t){
    var c = '';

    switch (t){
        case 0:
            c = 'green';
            if(f > 0.1){
                c = "red";
            }else if(f > 0.02 && f <= 0.1){
                c = "orange";
            }
            break;
        case 1:
            //
            break;
        case 2:
            //
            break;
        default:
            //
    }

    return c;
}

/**
 * 显示一个Master产品下面所有的尺寸
 * @param n Master产品名称
 */
function showProduktVariante(n) {
    var product = groupedProducts['grouped-products'][n];
    var elems = product['elems'];
    var htmlTxt = '<div class="box">' +
        '            <div class="box-header">' +
        '              <h3 class="box-title">' + n.split("-")[1] + '</h3>' +
        '            </div>' +
        '            <!-- /.box-header -->' +
        '            <div class="box-body">' +
        '              <table id="t-product-variables" class="table table-bordered table-striped">' +
        '                <thead>' +
        '                <tr>' +
        '                  <th></th>' +
        '                  <th>EAN</th>' +
        '                  <th>Produktname</th>' +
        '                  <th>Verfügbar</th>' +
        '                  <th>Preis (Netto)</th>' +
        '                  <th>Notsent</th>' +
        '                </tr>' +
        '                </thead>' +
        '                <tbody>';

    for(var i = 0; i<elems.length; ++i){

        var actRowHtml = '';
        if(/*parseInt(elems[i]["quantity"]) > 0 && */parseFloat(elems[i]["price"]) >= 0){
            actRowHtml = '<input id="input-add-product-quantity-' + elems[i]["ean"] + '" class="input-add-product-quantity" value="1"/><span class="btn btn-primary btn-add-product-quantity" onclick=addToShoppingCart("' + elems[i]["ean"] + '"); >Add</span>';
        }else{
            actRowHtml = '<input class="input-add-product-quantity" value="0" disabled="disabled" /><span class="btn btn-danger btn-add-product-quantity" onclick=customAlert("Lagerbestand&nbsp;Warnung:&nbsp;ID-10014",5,"Produkt&nbsp;ist&nbsp;nicht&nbsp;auf&nbsp;Lager&nbsp;oder&nbsp;Preis&nbsp;ist&nbsp;ungültig."); >Add</span>';
        }

        htmlTxt = htmlTxt + '<tr>' +
            '<td>' + actRowHtml + '</td>' +
            '<td>' + elems[i]["ean"] + '</td>' +
            '<td>' + elems[i]["title"] + '</td>' +
            '<td>' + elems[i]["quantity"] + '</td>' +
            '<td>' + formatThePriceYing(elems[i]["price"], "EUR") + '</td>' +
            '<td>' + elems[i]["notsent"] + '</td>' +
            '</tr>';
    }

        htmlTxt += '                </tbody>' +
        '                <tfoot>' +
        '                <tr>' +
        '                  <th></th>' +
        '                  <th>EAN</th>' +
        '                  <th>Produktname</th>' +
        '                  <th>Verfügbar</th>' +
        '                  <th>Preis (Netto)</th>' +
        '                  <th>Notsent</th>' +
        '                </tr>' +
        '                </tfoot>' +
        '              </table>' +
        '            </div>' +
        '            <!-- /.box-body -->' +
        '          </div>';

    $('#pro-elems-wrap').html(htmlTxt);

    // 表格属性
    $('#t-product-variables').DataTable({
        "columns": [
            { "orderable": false },
            null,
            null,
            null,
            null,
            null
        ],
        'paging'      : true,
        'lengthChange': true,
        'searching'   : true,
        'ordering'    : true,
        'info'        : true,
        'autoWidth'   : true,
        "language": {
            "lengthMenu": "Auf jeder Seite maximal _MENU_ Produkte anzeigen.",
            "zeroRecords": "Kein Produkt gefunden.",
            "info": "Seite _PAGE_ von _PAGES_",
            "infoEmpty": "Kein Produkt gefunden.",
            "infoFiltered": "(filtered from _MAX_ total records)",
            "search": "Suche:",
            "paginate": {
                "first": "Erste Seite",
                "last": "Letzte Seite",
                "next": "Nächste Seite",
                "previous": "Vorherige Seite"
            }
        }
    });

    // Lazyload
    $("img.lazyload").lazyload({
        effect:'fadeIn'
    });

    // 格式化价格
    formatPrice("table-price");

    // 页面弹回顶部
    returnPageTop();
}

var returnPageTop = function(){
    //设置定时器
    timer = setInterval(function(){
        //获取滚动条距离顶部的高度
        var osTop = document.documentElement.scrollTop || document.body.scrollTop;  //同时兼容了ie和Chrome浏览器

        //减小的速度
        var isSpeed = Math.floor(-osTop / 6);
        document.documentElement.scrollTop = document.body.scrollTop = osTop + isSpeed;
        //console.log( osTop + isSpeed);

        isTop = true;

        //判断，然后清除定时器
        if (osTop === 0) {
            clearInterval(timer);
        }
    },30);

};

/**
 * 根据load的产品数据，画出产品饼图
 * @param t 饼图类型  ->  0:根据产品的库存数量 | 1:根据产品的Element数量
 * @param canvasId
 */
function setProductPieChart(t, canvasId) {

    //------------------
    //- PIE CHART HTML -
    //------------------

    //-------------
    //- PIE CHART -
    //-------------
    // Get context with jQuery - using jQuery's .get() method.
    var pieChartCanvas = $('#' + canvasId).get(0).getContext('2d');
    var pieChart       = new Chart(pieChartCanvas);

    //根据饼图的参数t(饼图类型)加载数据
    var PieData = [];
    var gps = groupedProducts['grouped-products'];
    switch (t){
        case 0:
            var countNr = 0;
            for(var k in gps){
                if(k !== "ZUB"){
                    var PieDataItm = {
                        value    : gps[k]["quantity"],
                        color    : getColorByCountNr(countNr)[1],
                        highlight: getColorByCountNr(countNr)[0],
                        label    : k
                    };
                    PieData.push(PieDataItm);
                    ++countNr;
                }
            }
            break;
        case 1:
            var countNr = 0;
            for(var k in gps){
                //if(k !== "ZUB"){
                    var PieDataItm = {
                        value    : gps[k]["elems"].length,
                        color    : getColorByCountNr(countNr)[1],
                        highlight: getColorByCountNr(countNr)[0],
                        label    : k
                    };
                    PieData.push(PieDataItm);
                    ++countNr;
                //}
            }
            break;
        default:
            // NO DEFAULT VALUE
    }

    var pieOptions     = {
        //Boolean - Whether we should show a stroke on each segment
        segmentShowStroke    : true,
        //String - The colour of each segment stroke
        segmentStrokeColor   : '#fff',
        //Number - The width of each segment stroke
        segmentStrokeWidth   : 2,
        //Number - The percentage of the chart that we cut out of the middle
        percentageInnerCutout: 50, // This is 0 for Pie charts
        //Number - Amount of animation steps
        animationSteps       : 100,
        //String - Animation easing effect
        animationEasing      : 'easeOutBounce',
        //Boolean - Whether we animate the rotation of the Doughnut
        animateRotate        : true,
        //Boolean - Whether we animate scaling the Doughnut from the centre
        animateScale         : false,
        //Boolean - whether to make the chart responsive to window resizing
        responsive           : true,
        // Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
        maintainAspectRatio  : true,
        //String - A legend template
        legendTemplate       : '<ul class="<%=name.toLowerCase()%>-legend"><% for (var i=0; i<segments.length; i++){%><li><span style="background-color:<%=segments[i].fillColor%>"></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>'
    };
    //Create pie or douhnut chart
    // You can switch between pie and douhnut using the method below.
    pieChart.Doughnut(PieData, pieOptions);

}

/**
 * 从全局变量的Color颜色列表里面拿到一个颜色
 * @param n 要从0开始
 * @returns {Array}
 */
function getColorByCountNr(n) {
    if(n > (colors.length - 1)){
        n = n % colors.length;
    }
    return colors[n];
}

/**
 * 计算json长度，例如 var shoppingCartArray = {}; 里面有多少Item
 * @param j
 * @returns {number}
 */
function getJsonLen(j) {
    var len = 0;
    for(var k in j){
        ++len;
    }
    return len;
}

/**
 * 强：更新产品数据 (强行删除本地和LocalStorage的数据)
 * * 如果pageId是页面的Link,那么会显示相应的产品数据到页面上
 * * 如果pageId是"BackendOnly",那么说明不需要显示什么到前台页面上
 */
function updateProductDataBackend(pageId) {
    showLoadingLayer();
    localStorage.removeItem("products-refresh-time");
    localStorage.removeItem("grouped-products");
    localStorage.removeItem("master-product-quantity-in-category");
    localStorage.removeItem("product-top-categorys");
    localStorage.removeItem("un-grouped-products");
    loadProducts(pageId);
}

/**
 * 弱：更新产品数据 (如果本地和LocalStorage有数据，那就不发送ALAX请求)
 * * 如果pageId是页面的Link,那么会显示相应的产品数据到页面上
 * * 如果pageId是"BackendOnly",那么说明不需要显示什么到前台页面上
 */
function updateProductDataBackendWeak(pageId) {
    loadProducts(pageId);
}

/**
 * 弹窗Alert
 * @param id error-id
 * @param i icon-id
 * @param t text
 */
function customAlert(id,i,t) {
    layer.open({
        title: id,
        icon: i,
        content: t,
        btn: ['Schließen']
    });
}

/**
 * 滚动页面到适当的位置
 * @param nr
 *
 * [nr] 0 : /order-create/#step-2/ 创建订单页面第二步
 * [nr] 1 : /order-create/#step-1/ 创建订单页面第一步
 * [nr] 2 : /order-create/#step-3/ 创建订单页面第三步
 * [nr] 3 : /order-create/#step-4/ 创建订单页面第四步
 * [nr] 4 : /order-create/#step-5/ 创建订单页面第五步
 * [nr] 5 : /order-create/#form-wrap-kundeninfo/ 创建订单页面 用户信息Form
 */
function scrollPage(nr) {
    var scroll_height = 0;
    switch (nr){
        case 0:
            scroll_height = $("#create-order-step-1").height() + 90;
            //console.log('Scroll-Y: ' + scroll_height + 'px');
            scrollPageTo(scroll_height);
            doSession("scrollPageCode","","delete");
            break;
        case 1:
            scrollPageTo(0);
            break;
        case 2:
            scroll_height = $("#create-order-step-1").height() + $("#create-order-step-2").height() + 143;
            scrollPageTo(scroll_height);
            break;
        case 3:
            scroll_height = $("#create-order-step-1").height() + $("#create-order-step-2").height() + $("#create-order-step-3").height() + 196;
            scrollPageTo(scroll_height);
            break;
        case 4:
            scroll_height = $("#create-order-step-1").height() + $("#create-order-step-2").height() + $("#create-order-step-3").height() + $("#create-order-step-4").height() + 249;
            scrollPageTo(scroll_height);
            break;
        case 5:
            scroll_height = $("#create-order-step-1").height() + 346;
            scrollPageTo(scroll_height);
            break;
        default:
            //
    }
}

function scrollPageTo(px) {
    //window.scrollTo(0, );
    $body = (window.opera) ? (document.compatMode == "CSS1Compat" ? $('html') : $('body')) : $('html,body');
    // 上面这行是 Opera 的补丁, 少了它 Opera 是直接用跳的而且画面闪烁

    $body.animate({scrollTop: px}, 500);
    return false;
}

function redirectAndScrollPage(link,sK,sVal,sAct) {
    $.ajax( {
        url:'/api/setsession',
        data:{
            key : sK,
            value : sVal,
            action : sAct
        },
        type:'post',
        cache:false,
        dataType:'json',
        success : function() {
            location.href= link;
        },
        error : function() {
            // view("异常！");
            // alert("异常！");
        }
    });
}

function doSession(sK,sVal,sAct) {
    $.ajax( {
        url:'/api/setsession',
        data:{
            key : sK,
            value : sVal,
            action : sAct
        },
        type:'post',
        cache:false,
        dataType:'json',
        success : function(data) {
            //
        },
        error : function() {
            // view("异常！");
            // alert("异常！");
        }
    });
}

function getCountriesAjax() {
    $.ajax( {
        url:'/api/getcountriesoftheworld',
        data:{},
        type:'post',
        cache:false,
        dataType:'json',
        success : function(data) {
            if(data.isSuccess){
                var cs = data.data;
                for(var i=0; i<cs.length; ++i){
                    var country = {};
                    country.name = cs[i]["country"];
                    country.iso_2 = cs[i]["iso_2"];
                    country.in_common_use = cs[i]["in_common_use"];
                    countries.push(country);
                }
                localStorage.setItem("countries-of-the-world", JSON.stringify(countries));
                fillCountrySelect("KBundesland-RA");
                fillCountrySelect("KBundesland-LA");
            }
        },
        error : function() {
            // view("异常！");
            // alert("异常！");
        }
    });
}

function fillCountrySelect(id) {
    htmlTxt = '';
    if(countries.length > 0){
        htmlTxt += '<option disabled>--------------------------------</option>';
        htmlTxt += '<option class="disabledSelectOption" disabled>Häufige&nbsp;Zielländer&nbsp;:</option>';
        htmlTxt += '<option disabled>--------------------------------</option>';
        for(var i=0; i<countries.length; ++i){

            // 增加分割线
            if(i > 0){
                if(countries[i].in_common_use != countries[i-1].in_common_use){
                    htmlTxt += '<option disabled>----------------------------------------</option>';
                    htmlTxt += '<option class="disabledSelectOption" disabled>Nicht&nbsp;häufige&nbsp;Zielländer&nbsp;:</option>';
                    htmlTxt += '<option disabled>----------------------------------------</option>';
                }
            }

            if(countries[i].iso_2 == "DE"){
                htmlTxt = htmlTxt + '<option value="' + countries[i].iso_2 + '" selected>' + countries[i].name + '</option>';
            }else{
                htmlTxt = htmlTxt + '<option value="' + countries[i].iso_2 + '">' + countries[i].name + '</option>';
            }

        }
    }
    $('#'+id).html(htmlTxt);
}

/**
 * 开启或者关闭运输地址的表单
 * @param act (off 或者 on)
 */
function switchLieferanschriftForm(act) {
    if(act == "on"){
        $('#cb-iLavR').prop('checked', false);
        customAlert('System Tipp: ID-10029',4,'Lieferanschrift muss momentan gleich wie Rechnungsanschrift sein.');
    }else{
        $('#form-wrap-lieferanschrift-box').css("opacity", "0.3");
        $('#form-wrap-lieferanschrift').css("background-image", "url(/wp-content/uploads/images/LgwR.png)");
        $('#form-wrap-lieferanschrift-watermark').css("display", "block");
        $('#KFirma-LA').prop('disabled', true);
        $('#KVorname-LA').prop('disabled', true);
        $('#KNachname-LA').prop('disabled', true);
        $('#KStrasse-LA').prop('disabled', true);
        $('#KStrasse2-LA').prop('disabled', true);
        $('#KPLZ-LA').prop('disabled', true);
        $('#KOrt-LA').prop('disabled', true);
        $('#KBundesland-LA').prop('disabled', true);
        $('#Ktelefon-LA').prop('disabled', true);
    }
    /*
    if(act == "on"){
        $('#form-wrap-lieferanschrift-box').css("opacity", "1.0");
        $('#form-wrap-lieferanschrift').css("background-image", "");
        $('#form-wrap-lieferanschrift-watermark').css("display", "none");

        $('#KFirma-LA').prop('disabled', false);
        $('#KVorname-LA').prop('disabled', false);
        $('#KNachname-LA').prop('disabled', false);
        $('#KStrasse-LA').prop('disabled', false);
        $('#KStrasse2-LA').prop('disabled', false);
        $('#KPLZ-LA').prop('disabled', false);
        $('#KOrt-LA').prop('disabled', false);
        $('#KBundesland-LA').prop('disabled', false);
        $('#Ktelefon-LA').prop('disabled', false);
    }else{
        $('#form-wrap-lieferanschrift-box').css("opacity", "0.3");
        $('#form-wrap-lieferanschrift').css("background-image", "url(/wp-content/uploads/images/LgwR.png)");
        $('#form-wrap-lieferanschrift-watermark').css("display", "block");

        $('#KFirma-LA').prop('disabled', true);
        $('#KVorname-LA').prop('disabled', true);
        $('#KNachname-LA').prop('disabled', true);
        $('#KStrasse-LA').prop('disabled', true);
        $('#KStrasse2-LA').prop('disabled', true);
        $('#KPLZ-LA').prop('disabled', true);
        $('#KOrt-LA').prop('disabled', true);
        $('#KBundesland-LA').prop('disabled', true);
        $('#Ktelefon-LA').prop('disabled', true);
    }
    */
}

function syncAdresseR2L() {
    syncInput("KFirma-RA", "KFirma-LA", true);
    syncInput("KVorname-RA", "KVorname-LA", true);
    syncInput("KNachname-RA", "KNachname-LA", true);
    syncInput("KStrasse-RA", "KStrasse-LA", true);
    syncInput("KStrasse2-RA", "KStrasse2-LA", true);
    syncInput("KPLZ-RA", "KPLZ-LA", true);
    syncInput("KOrt-RA", "KOrt-LA", true);
    syncInput("KBundesland-RA", "KBundesland-LA", true);
    syncInput("Ktelefon-RA", "Ktelefon-LA", true);
}

/**
 * 同步Input内容
 * @param fromId
 * @param toId
 * @param bStrong - true：auf jeden fall 强硬的覆盖
 */
function syncInput(fromId, toId, bStrong) {
    var fVal = $('#'+fromId).val();
    /*
    var tVal = $('#'+toId).val();
    */
    if(bStrong){
        $('#'+toId).val(fVal);
    }else{
        if(!$('#cb-iLavR').is(":checked")){
            $('#'+toId).val(fVal);
        }
    }
}

/**
 * 根据ean 获取历史库存数据
 * @param e
 */
function getProductStockHistoryByEAN_Ajax(e) {

    if(!isloadProductHistoryAjaxRunning) {
        isloadProductHistoryAjaxRunning = true;

        $.ajax({
            url: '/api/getproductstockhistorybyean',
            data: {
                ean: e
            },
            type: 'post',
            cache: false,
            dataType: 'json',
            success: function (data) {
                if (data.isSuccess) {
                    isloadProductHistoryAjaxRunning = false;
                    if (data.data.length > 0) {
                        $('#lagerbestand-historie-chart-wrap').html('');
                        setProductStockHistoryLineChart(data.data);
                    } else {
                        $('#lagerbestand-historie-chart-wrap').html('<div style="padding: 10px 0 10px 3px; color: #FF0000;">' + data.msg + '</div>');
                    }
                }
            },
            error: function () {
                // view("异常！");
                // alert("异常！");
            }
        });
    }
}

/**
 * 把 Line Chart 画出来
 */
function setProductStockHistoryLineChart(l){
    $('#lagerbestand-historie-chart-wrap').html('<div class="chart" id="product-stock-history" style="height: 300px;"></div>');

    var productName = '';

    //整理数据
    var datas = [];
    for(var i=0; i<l.length; ++i){
        var data = {};
        data.datum = l[i][7].replace(/\./g, "-");
        data.num = l[i][3];
        // 把负数的库存量都刷成0
        if(data.num < 0){
            data.num = 0;
        }
        productName = l[i][5];
        datas.push(data);
    }

    var area = new Morris.Area({
        element: 'product-stock-history',
        resize: true,
        data: datas,
        xkey: 'datum',
        ykeys: ['num'],
        labels: ['Lagerbestand'],
        lineColors: ['#3c8dbc'],
        hideHover: 'auto'
    });

    $('#lagerbestand-historie-product-name').html('<i class="fa fa-fw fa-search"></i>&nbsp;Produkt:&nbsp;' + productName);

}

function getProductStockHistory(inputObj) {
    var val = inputObj.value;
    $('#lagerbestand-historie-product-name').html('');
    if(val.length == 13){
        $('#lagerbestand-historie-chart-wrap').html('<img src="/wp-content/uploads/images/loading-spinning-circles.svg" />');
        getProductStockHistoryByEAN_Ajax(val);
    }else{
        $('#lagerbestand-historie-chart-wrap').html('');
    }
}

function initDatepicker(id) {
    var o = $('#'+id);
    o.attr("readonly",true);
    o.css("background-color", "#FFFFFF");
    o.datepicker({
        locale: 'de',
        format: 'dd.mm.yyyy',
        //startDate: '+1d',
        startDate: '0d',
        defaultViewDate: 'today',
        todayHighlight: true,
        autoclose: true
    });
    o.datepicker("setDate", getDatum('de'));
}

function getDatum(l) {
    var todayDatum = new Date();
    var todayDatum_d = todayDatum.getDate();
    if(todayDatum_d < 10){
        todayDatum_d = "0" + todayDatum_d;
    }
    var todayDatum_m = todayDatum.getMonth() + 1;
    if(todayDatum_m < 10){
        todayDatum_m = "0" + todayDatum_m;
    }
    var todayDatum_y = todayDatum.getFullYear();
    var datum = '';
    switch (l){
        case 'de':
            datum = todayDatum_d + '.' + todayDatum_m + '.' + todayDatum_y;
            break;
        default:
            datum = todayDatum_y + '-' + todayDatum_m + '-' + todayDatum_d;
    }
    return datum;
}

function setInputPrice(id, price) {
    var o =  $('#'+id);
    o.val(price);
    o.priceFormat({
        prefix: '',
        allowNegative: true,
        clearPrefix: true,
        clearSuffix: true,
        centsSeparator: ',',
        thousandsSeparator: '.'
    });
}

/**
 * 根据class 大面积修改价格
 * @param c
 */
function formatPrice(c) {
    var o =  $('.' + c);
    o.priceFormat({
        prefix: '',
        allowNegative: true,
        clearPrefix: true,
        clearSuffix: true,
        centsSeparator: ',',
        thousandsSeparator: '.'
    });
}

/**
 * 正则表达式处理价格格式
 * @param p
 * @returns {string}
 */
function formatThePriceYing(p, currency) {
    var price = "";
    switch(currency){
        case "EUR":
            price = parseFloat(p, 10).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,").toString();
            price = price.replace(/,/g, "#DH#");
            price = price.replace(/\./g, ",");
            price = price.replace(/#DH#/g, ".");
            break;
        case "USD":
            price = parseFloat(p, 10).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,").toString();
            break;
        default:
            price = parseFloat(p, 10).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,").toString();
    }
    return price;
}

/**
 * 只要购物车有变，就跟着执行这个
 */
function updateBezahlungZwischensumme() {
    // 计算订单总额: Zwischensumme
    var zwischensumme = orderSummary.nettoPreissumme + orderSummary.rabatt + orderSummary.rabattBeiAbholung;
    setInputPrice('input-summe-zwischensumme', zwischensumme);

    orderSummary.zwischensumme = zwischensumme;
}

function updateNettoPreissumme() {
    // 计算订单总额:
    var nettoPreissumme = getNettoPreissumme();
    setInputPrice('input-netto-preissumme', nettoPreissumme);

    orderSummary.nettoPreissumme = nettoPreissumme;
}

/**
 * 只要购物车有变，就跟着执行这个
 */
function updateBezahlungGesamtsumme() {
    var gesamtsumme =  orderSummary.zwischensumme + orderSummary.mwst;
    setInputPrice('input-summe-gesamtsumme', gesamtsumme);

    orderSummary.gesamtsumme = gesamtsumme;
}

/**
 * 只要购物车有变，就跟着执行这个
 */
function updateShippingCosts() {
    var shippingCosts = getShippingCosts();
    setInputPrice('input-summe-ver-Bear', shippingCosts);
}

function updateRabattSelectOptions() {

    // 设置可以打折的折扣
    var o = $('#select-rabatt');
    var htmlTxt = '';
    var maxRabattPercent = 0;
    var nextLvlRabattPercent = 100;
    var nextLvlRabattNeedSumme = 0;
    for(var k in rabattStufen){
        if(parseFloat(orderSummary.nettoPreissumme) >= parseFloat(k*100) && parseInt(rabattStufen[k]) > maxRabattPercent){
            maxRabattPercent = parseInt(rabattStufen[k]);
        }
        if(parseFloat(orderSummary.nettoPreissumme) < parseFloat(k*100) && parseInt(rabattStufen[k]) < nextLvlRabattPercent){
            nextLvlRabattPercent = parseInt(rabattStufen[k]);
            nextLvlRabattNeedSumme = parseInt(k);
        }
    }
    for(var i=0; i<=maxRabattPercent; ++i){
        if(i == 0){
            htmlTxt = htmlTxt + '<option value="' + i + '">kein&nbsp;Rabatt</option>';
        }else if(i == rabatt){
            htmlTxt = htmlTxt + '<option value="' + i + '" selected>-&nbsp;' + i + '%</option>';
        }else{
            htmlTxt = htmlTxt + '<option value="' + i + '">-&nbsp;' + i + '%</option>';
        }
    }
    if(nextLvlRabattPercent != 100 && nextLvlRabattNeedSumme != 0){
        htmlTxt = htmlTxt + '<option disabled>&nbsp;</option>';
        htmlTxt = htmlTxt + '<option style="color: #3c8dbc; background-color: #E8E8E8;" disabled>-----&nbsp;Tipp&nbsp;-----</option>';
        htmlTxt = htmlTxt + '<option style="color: #3c8dbc; background-color: #E8E8E8;" disabled>Wenn&nbsp;Zwischensumme&nbsp;mehr&nbsp;als&nbsp;' + nextLvlRabattNeedSumme + '&nbsp;&euro;&nbsp;ist,&nbsp;&nbsp;&nbsp;&nbsp;</option>';
        htmlTxt = htmlTxt + '<option style="color: #3c8dbc; background-color: #E8E8E8;" disabled>sehen Sie mehr Möglichkeiten vom Rabatt.&nbsp;&nbsp;&nbsp;&nbsp;</option>';
    }
    o.html(htmlTxt);
}

function updateRabattVal() {
    var rbv = getRabattVal();
    orderSummary.rabatt = rbv;
    // 显示
    setInputPrice('input-summe-rabatt', rbv);

    var rbtLvl = $('#select-rabatt').val();
    if(rbtLvl < 1){
        $('#li-rabatt').css('display','none');
    }else{
        $('#li-rabatt').css('display','block');
    }
}

function getRabattVal() {
    // 读取打折幅度
    var rbtLvl = $('#select-rabatt').val();
    return (0 - Math.round(orderSummary.nettoPreissumme * rbtLvl / 100));
}

function getRabattAbholungVal() {
    // 读取打折幅度
    var rbtLvl = rabattBeiAbholung;
    var rbv = (0 - Math.round(orderSummary.nettoPreissumme * rbtLvl / 100));
    orderSummary.rabattBeiAbholung = rbv;
    return rbv;
}

function updateVersandkosten(o) {
    var vk = parseInt(o.value.replace(/,/g, "").replace(/\./g, ""));

    orderInfoArray.versandkosten = vk/100;
    localStorage.setItem("order-info", JSON.stringify(orderInfoArray));

    setInputPrice('input-summe-ver-Bear', vk);
    updateBezahlungGesamtsumme();
}

function getNettoPreissumme() {
    var nps = 0;
    for(var k in shoppingCartArray){
        nps += Math.round(parseFloat(shoppingCartArray[k].qInCart) * parseFloat(shoppingCartArray[k].price) * 100);
    }
    return nps;
}

function getShippingCosts() {
    var res = 0;
    for(var k in shoppingCartArray){
        res += Math.round(parseFloat(shoppingCartArray[k].qInCart) * parseFloat(shoppingCartArray[k].shippingCost) * 100);
    }
    return res;
}

function setInputVal(id, v) {
    $('#' + id).val(v);
}

function calculateNotizTxtLen(id) {
    o = $('#'+id).val();
    if(o.length > 0){
        $('#notiz-txt-len').html('Notiz der Bestellung ( ' + o.length + '/500 )');
    }else{
        $('#notiz-txt-len').html('Notiz der Bestellung');
    }

    if(o.length > 500){
        $('#'+id).val(o.substring(0,500));
    }
}

function searchKunden() {

    var k = $('#search-kunden-Kid').val();
    var n = $('#search-kunden-Knachname').val();
    var v = $('#search-kunden-Kvorname').val();
    var e = $('#search-kunden-Kemail').val();
    var t = $('#search-kunden-Ktel').val();
    var p = $('#search-kunden-Kplz').val();

    if((k+n+v+e+t+p).length > 0){

        scrollPage(0);

        showLoadingLayer();

        $.ajax({
            url: '/api/getcustomersinentelliship',
            data: {
                id: k,
                surname: n,
                firstname: v,
                mail: e,
                phone: t,
                postcode: p
            },
            type: 'post',
            cache: false,
            dataType: 'json',
            success: function (data) {
                if (data.name == "successful") {
                    //console.log('GOT! ' + data.customers.length);
                    showCustomersFromE2(data);
                }else{
                    $('#search-kunden-result-wrap').css('display', 'block');
                    $('#search-kunden-result-wrap').html('<div class="col-md-12" style="color: #f39c12;"><i class="fa fa-warning"></i>&nbsp;&nbsp;Kunden wurde nicht gefunden.</div>');
                    removeLoadingLayer();
                }
            },
            error: function () {
                // view("异常！");
                // alert("异常！");
            }
        });

    }

}

function showCustomersFromE2(d) {
    var customers = d.customers;

    // 去除重复的item
    customers = getUniqueCustomers(customers);

    var htmlTxt = '<div class="col-md-12" style="padding: 10px 10px 20px 10px;">'+
        '<div class="box">' +
        '            <div class="box-header">' +
        '              <h3 class="box-title">Suchergebnisse ( ';
    htmlTxt += customers.length;
    htmlTxt += ' Kunden gefunden )</h3>' +
        '            </div>' +
        '            <!-- /.box-header -->' +
        '            <div class="box-body">' +
        '              <table id="t-search-kunden-result" class="table table-bordered table-hover">' +
        '                <thead>' +
        '                <tr>' +
        '                  <th></th>' +
        '                  <th>ID</th>' +
        '                  <th>Name</th>' +
        '                  <th>Adresse</th>' +
        '                  <th>Stadt</th>' +
        '                  <th>PLZ-Land</th>' +
        '                  <th>E-Mail</th>' +
        '                  <th>Tel.</th>' +
        '                </tr>' +
        '                </thead>' +
        '                <tbody>';

    for(var i=0; i<customers.length; ++i){
        var actRowHtml = '<span class="btn btn-primary btn-accept-kunden" onclick=useThisKunden("' + customers[i]["id"] + '"); >übernehmen</span>';
        htmlTxt = htmlTxt + '<tr>' +
            '<td>' + actRowHtml + '</td>' +
            '<td>' + customers[i]["id"] + '</td>' +
            '<td>' + customers[i]["firstName"] + '&nbsp;' + customers[i]["lastName"] + '</td>' +
            '<td>' + customers[i]["street"] + '</td>' +
            '<td>' + customers[i]["city"] + '</td>' +
            '<td>' + customers[i]["postalCode"] + '-' + customers[i]["countryISO"] + '</td>' +
            '<td>' + customers[i]["mail"] + '</td>' +
            '<td>' + customers[i]["phone"] + '</td>' +
        '</tr>';
    }

    htmlTxt += '                </tbody>' +
        '              </table>' +
        '            </div>' +
        '            <!-- /.box-body -->' +
        '          </div>';
    htmlTxt += '</div>';

    $('#search-kunden-result-wrap').html(htmlTxt);
    $('#search-kunden-result-wrap').css('display', 'block');

    // 表格属性
    $('#t-search-kunden-result').DataTable({
        "columns": [
            { "orderable": false },
            null,
            null,
            null,
            null,
            null,
            null,
            null
        ],
        'paging'      : true,
        'lengthChange': false,
        'searching'   : false,
        'ordering'    : true,
        'info'        : true,
        'autoWidth'   : true,
        "language": {
            "lengthMenu": "Auf jeder Seite maximal _MENU_ Produkte anzeigen.",
            "zeroRecords": "Kein Produkt gefunden.",
            "info": "Seite _PAGE_ von _PAGES_",
            "infoEmpty": "Kein Produkt gefunden.",
            "infoFiltered": "(filtered from _MAX_ total records)",
            "search": "Suche:",
            "paginate": {
                "first": "Erste Seite",
                "last": "Letzte Seite",
                "next": "Nächste Seite",
                "previous": "Vorherige Seite"
            }
        }
    });

    removeLoadingLayer();

}

function getUniqueCustomers(l) {
    searchCustomerResults = [];
    for(var i=0; i<l.length; ++i){
        var isInL = false;
        var item_i = l[i];
        for(var j=0; j<searchCustomerResults.length; ++j){
            var item_j = searchCustomerResults[j];
            if(item_i.company == item_j.company && item_i.city == item_j.city && item_i.countryISO == item_j.countryISO && item_i.firstName == item_j.firstName && item_i.lastName == item_j.lastName && item_i.mail == item_j.mail && item_i.phone == item_j.phone && item_i.postalCode == item_j.postalCode && item_i.street == item_j.street && item_i.userIdPlattform == item_j.userIdPlattform){
                isInL = true;
            }
        }
        if(!isInL){
            searchCustomerResults.push(item_i);
        }
    }
    return searchCustomerResults;
}

function useThisKunden(id) {
    for(var i=0; i<searchCustomerResults.length; ++i){
        var itm = searchCustomerResults[i];
        if(id == itm.id){
            e2Data.customer_id = id;
            e2Data.customer_userIdPlattform = itm.userIdPlattform;
            // E-Mail Adresse
            $('#Kemail').val(itm.mail);
            // company
            $('#KFirma-RA').val(itm.company);
            $('#KFirma-LA').val(itm.company);
            // Name
            $('#KVorname').val(itm.firstName);
            $('#KNachname').val(itm.lastName);
            $('#KVorname-RA').val(itm.firstName);
            $('#KNachname-RA').val(itm.lastName);
            $('#KVorname-LA').val(itm.firstName);
            $('#KNachname-LA').val(itm.lastName);
            // Straße und Hausnummer
            $('#KStrasse-RA').val(itm.street);
            $('#KStrasse-LA').val(itm.street);
            // PLZ Ort
            $('#KPLZ-RA').val(itm.postalCode);
            $('#KOrt-RA').val(itm.city);
            $('#KPLZ-LA').val(itm.postalCode);
            $('#KOrt-LA').val(itm.city);
            // Land
            $('#KBundesland-RA').val(itm.countryISO);
            $('#KBundesland-LA').val(itm.countryISO);
            // Tel.
            $('#Ktelefon-RA').val(itm.phone);
            $('#Ktelefon-LA').val(itm.phone);

            $('#search-kunden-result-wrap').css('display', 'none');
            $('#search-kunden-result-wrap').html('');

            scrollPage(5);

            break;
        }
    }
}

function searchKundenBlockOtherInput() {
    var k = $('#search-kunden-Kid').val();
    var no = $('#search-kunden-Knachname');
    var vo = $('#search-kunden-Kvorname');
    var eo = $('#search-kunden-Kemail');
    var to = $('#search-kunden-Ktel');
    var po = $('#search-kunden-Kplz');
    if(k == ''){
        no.prop('disabled', false);
        vo.prop('disabled', false);
        eo.prop('disabled', false);
        to.prop('disabled', false);
        po.prop('disabled', false);
    }else{
        no.val("");
        vo.val('');
        eo.val('');
        to.val('');
        po.val('');
        no.prop('disabled', true);
        vo.prop('disabled', true);
        eo.prop('disabled', true);
        to.prop('disabled', true);
        po.prop('disabled', true);
    }
}

function showLoadingLayer() {
    layer.load(1, {
        shade: [0.5,'#000000']
    });
}

function removeLoadingLayer() {
    layer.closeAll('loading');
}

function retryCreateOrder(id) {
    layer.confirm('Werden Sie es nochmal versuchen, um die Bestellung zu erstellen?', {
        icon: 3,
        title: 'Bestellung Tipp: ID-10024',
        btn: ['Ja, erstellen','Nein, abbrechen'] //按钮
    }, function(index){
        layer.close(index);
        showLoadingLayer();
        getPosQuantityWantInOrder_Ajax(id);
    }, function(index){
        layer.close(index);
    });
}

function getPosQuantityWantInOrder_Ajax(id) {
    $.ajax({
        url:'/api/getposquantitywantinorder',
        data: { "id_db": id },
        dataType: "json",
        type: "POST",
        traditional: true,
        success: function (data) {
            if(data.isSuccess){
                var qWantData = data.data;
                var isNAL = false;
                var errorMsg = "Die folgende Artikel sind nicht auf Lager:";
                for(var i=0; i<qWantData.length; ++i){
                    // 现有库存
                    var quantity = parseInt(groupedProducts['un-grouped-products'][qWantData[i].ean].quantity);
                    // 需求量
                    var quantityWant = parseInt(qWantData[i].quantity_want);
                    // 判断
                    if(quantityWant > quantity){
                        isNAL = true;
                        errorMsg = errorMsg + "<br>[" + qWantData[i].ean + "] " + qWantData[i].title + " (" + quantity + " Stück auf Lager)";
                    }
                }
                if(isNAL){
                    removeLoadingLayer();
                    customAlert("Bestellung Fehler: ID-10025", 2, errorMsg);
                }else {
                    retryCreateOrder_Ajax(id);
                }
            }else{
                removeLoadingLayer();
                customAlert("Bestellung Fehler: ID-10018", 2, data.msg);
            }
        }
    });
}

function retryCreateOrder_Ajax(id) {
    $.ajax({
        url:'/api/resendordertoe2',
        data: { "id_db": id },
        dataType: "json",
        type: "POST",
        traditional: true,
        success: function (data) {
            if(data.isSuccess){
                var e2_rsp = eval('(' + data.data + ')');
                if(e2_rsp.name == "successful"){
                    var rsp_desc = e2_rsp.description;
                    var reg = /[^\(\)]+(?=\))/g;
                    var ab_id = rsp_desc.match(reg);

                    var newData = {};
                    newData.meta_id = id;
                    newData.order_id_ab = ab_id[0];
                    newData.status = "Versandvorbereitung";

                    updateOrder(newData);

                }else{
                    removeLoadingLayer();
                    customAlert("E2 Fehler: ID-10027", 2, "Fehler bei E2 auftaucht. Bitte versuchen Sie es später noch einmal.");
                }
            }else{
                removeLoadingLayer();
                customAlert("Bestellung Fehler: ID-10018", 2, data.msg);
            }
        }
    });
}

function updateOrder(newData_Json) {
    $.ajax({
        url:'/api/updateorder',
        data: { "order_new_data": encodeURI(JSON.stringify(newData_Json)) },
        dataType: "json",
        type: "POST",
        traditional: true,
        success: function (data) {

            if(data.isSuccess){
                layer.confirm('Die Bestellung wurde erfolgreich erstellt.', {
                    icon: 1,
                    title: 'Bestellung Tipp: ID-10017',
                    btn: ['Alles klar'] //按钮
                }, function(index){
                    layer.close(index);
                    location.reload();
                });
            }else{
                customAlert("Bestellung Fehler: ID-10026", 2, "Die Bestellung wurde schon in Afterbuy erstellt. Aber in GoodOne gibt es Fehler.");
            }

        }
    });
}

function createOrder() {

    /**
     * Loading 开启
     */
    showLoadingLayer();
    var errorList = [];
    //var isCreatingFinish = false;


    /**
     * 整理并验证数据
     */
    var ods = {};
    var iscbILavRChecked = $('#cb-iLavR').is(":checked");
    var iscbITaxFreeChecked = $('#cb-iTaxFree').is(":checked");

    // 订单状态
    ods.status = "";
    // 购物车
    if(getJsonLen(shoppingCartArray) < 1){
        ods.soldItems = {};
        ods.soldItems.items = [];
        errorList.push("[Bei Schritt 1] Ihr Warenkorb ist derzeit leer.");
    }else{
        ods.soldItems = {};
        ods.soldItems.items = [];
        for(var k in shoppingCartArray){
            var ean = shoppingCartArray[k].ean;
            var price = shoppingCartArray[k].price;
            var qic = parseInt(shoppingCartArray[k].qInCart);
            var q = parseInt(shoppingCartArray[k].quantity);
            var t = shoppingCartArray[k].title;
            var sc = shoppingCartArray[k].shippingCost;
            var aPos = {};
            aPos.ean = ean;
            aPos.price = price;
            aPos.qInCart = qic;
            aPos.quantity = q;
            aPos.title = t;
            aPos.shipping_cost = sc;
            aPos.tax = taxVal;
            ods.soldItems.items.push(aPos);

            if(qic > q){
                ods.status = "Nicht auf Lager";
            }
        }
    }
    // E-Mail Adresse
    var KemailVal = $('#Kemail').val();
    if(KemailVal == undefined || KemailVal == ""){
        errorList.push("[Bei Schritt 2] Bitte geben Sie E-Mail-Adresse des Käufers ein.");
    }else if(!isRightEmailFormat(KemailVal)){
        errorList.push("[Bei Schritt 2] Bitte geben Sie eine gültige E-Mail-Adresse ein.");
    }else{
        ods.customerMail = KemailVal;
    }
    // Vorname
    var KVornameVal = $('#KVorname').val();
    if(KVornameVal == undefined || KVornameVal == ""){
        errorList.push("[Bei Schritt 2] Bitte geben Sie Vorname des Käufers ein.");
    }else{
        ods.goodoneCustomerFirstname = KVornameVal;
    }
    var KVornameRAVal = $('#KVorname-RA').val();
    if(KVornameRAVal == undefined || KVornameRAVal == ""){
        errorList.push("[Bei Schritt 2] Bitte geben Sie Vorname des Käufers für die Rechnung ein.");
    }else{
        ods.customerFirstname = KVornameRAVal;
    }
    if(iscbILavRChecked){
        var KVornameLAVal = $('#KVorname-LA').val();
        if(KVornameLAVal == undefined || KVornameLAVal == ""){
            errorList.push("[Bei Schritt 2] Bitte geben Sie Vorname des Käufers für die Lieferung ein.");
        }else{
            ods.customerShippingFirstname = KVornameLAVal;
        }
    }else{
        ods.customerShippingFirstname = ods.customerFirstname;
    }
    // Nachname
    var KNachnameVal = $('#KNachname').val();
    if(KNachnameVal == undefined || KNachnameVal == ""){
        errorList.push("[Bei Schritt 2] Bitte geben Sie Nachname des Käufers ein.");
    }else{
        ods.goodoneCustomerSurname = KNachnameVal;
    }
    var KNachnameRAVal = $('#KNachname-RA').val();
    if(KNachnameRAVal == undefined || KNachnameRAVal == ""){
        errorList.push("[Bei Schritt 2] Bitte geben Sie Nachname des Käufers für die Rechnung ein.");
    }else{
        ods.customerSurname = KNachnameRAVal;
    }
    if(iscbILavRChecked){
        var KNachnameLAVal = $('#KNachname-LA').val();
        if(KNachnameLAVal == undefined || KNachnameLAVal == ""){
            errorList.push("[Bei Schritt 2] Bitte geben Sie Nachname des Käufers für die Lieferung ein.");
        }else{
            ods.customerShippingSurname = KNachnameLAVal;
        }
    }else{
        ods.customerShippingSurname = ods.customerSurname;
    }
    // VAT Nr.
    if(iscbITaxFreeChecked){
        var KVatNrVal = $('#KVatNr').val();
        if(KVatNrVal == undefined || KVatNrVal == ""){
            errorList.push("[Bei Schritt 2] Bitte geben Sie MwSt-Nr. ein.");
        }else{
            ods.vat_nr = KVatNrVal;
        }
    }else{
        ods.vat_nr = "";
    }
    ods.tax = taxVal;
    // Firma
    var KFirmaRAVal = $('#KFirma-RA').val();
    if(KFirmaRAVal == undefined || KFirmaRAVal == ""){
        ods.customerCompany = "";
    }else {
        ods.customerCompany = KFirmaRAVal;
    }
    if(iscbILavRChecked){
        var KFirmaLAVal = $('#KFirma-LA').val();
        if(KFirmaLAVal == undefined || KFirmaLAVal == ""){
            ods.customerShippingCompany = "";
        }else{
            ods.customerShippingCompany = KFirmaLAVal;
        }
    }else{
        ods.customerShippingCompany = ods.customerCompany;
    }
    // Straße und Hausnummer (Adresszusatz)
    var KStrasseRAVal = $('#KStrasse-RA').val();
    var KStrasse2RAVal = $('#KStrasse2-RA').val();
    if(KStrasseRAVal == undefined || KStrasseRAVal == ""){
        errorList.push("[Bei Schritt 2] Bitte geben Sie Straße und Hausnummer des Käufers für die Rechnung ein.");
    }else{
        if(KStrasse2RAVal == undefined || KStrasse2RAVal == ""){
            ods.customerStreet = KStrasseRAVal;
        }else{
            ods.customerStreet = KStrasseRAVal + " (" + KStrasse2RAVal + ")";
        }

        ods.goodoneCustomerStreet = KStrasseRAVal;
        ods.goodoneCustomerStreet1 = KStrasse2RAVal;
    }
    if(iscbILavRChecked){
        var KStrasseLAVal = $('#KStrasse-LA').val();
        var KStrasse2LAVal = $('#KStrasse2-LA').val();
        if(KStrasseLAVal == undefined || KStrasseLAVal == ""){
            errorList.push("[Bei Schritt 2] Bitte geben Sie Straße und Hausnummer des Käufers für die Lieferung ein.");
        }else{
            if(KStrasse2LAVal == undefined || KStrasse2LAVal == ""){
                ods.customerShippingStreet = KStrasseLAVal;
            }else{
                ods.customerShippingStreet = KStrasseLAVal + " (" + KStrasse2LAVal + ")";
            }

            ods.goodoneCustomerShippingStreet = KStrasseLAVal;
            ods.goodoneCustomerShippingStreet1 = KStrasse2LAVal;
        }
    }else{
        ods.customerShippingStreet = ods.customerStreet;
        ods.goodoneCustomerShippingStreet = ods.goodoneCustomerStreet;
        ods.goodoneCustomerShippingStreet1 = ods.goodoneCustomerStreet1;
    }
    // PLZ
    var KPLZRAVal = $('#KPLZ-RA').val();
    if(KPLZRAVal == undefined || KPLZRAVal == ""){
        errorList.push("[Bei Schritt 2] Bitte geben Sie PLZ des Käufers für die Rechnung ein.");
    }else{
        ods.customerPostcode = KPLZRAVal;
    }
    if(iscbILavRChecked){
        var KPLZLAVal = $('#KPLZ-LA').val();
        if(KPLZLAVal == undefined || KPLZLAVal == ""){
            errorList.push("[Bei Schritt 2] Bitte geben Sie PLZ des Käufers für die Lieferung ein.");
        }else{
            ods.customerShippingPostcode = KPLZLAVal;
        }
    }else{
        ods.customerShippingPostcode = ods.customerPostcode;
    }
    // Ort
    var KOrtRAVal = $('#KOrt-RA').val();
    if(KOrtRAVal == undefined || KOrtRAVal == ""){
        errorList.push("[Bei Schritt 2] Bitte geben Sie Ort des Käufers für die Rechnung ein.");
    }else{
        ods.customerCity = KOrtRAVal;
    }
    if(iscbILavRChecked){
        var KOrtLAVal = $('#KOrt-LA').val();
        if(KOrtLAVal == undefined || KOrtLAVal == ""){
            errorList.push("[Bei Schritt 2] Bitte geben Sie Ort des Käufers für die Lieferung ein.");
        }else{
            ods.customerShippingCity = KOrtLAVal;
        }
    }else{
        ods.customerShippingCity = ods.customerCity;
    }
    // Land
    ods.customerCountry = $('#KBundesland-RA').val();
    ods.customerCountryName = $('#KBundesland-RA option:selected').text();
    if(iscbILavRChecked){
        ods.customerShippingCountry = $('#KBundesland-LA').val();
        ods.customerShippingCountryName = $('#KBundesland-LA option:selected').text();
    }else{
        ods.customerShippingCountry = ods.customerCountry;
        ods.customerShippingCountryName = ods.customerCountryName;
    }
    // Telefonnummer
    var KtelefonRAVal = $('#Ktelefon-RA').val();
    if(KtelefonRAVal == undefined || KtelefonRAVal == ""){
        ods.customerTelephone = "";
    }else {
        ods.customerTelephone = KtelefonRAVal;
    }
    if(iscbILavRChecked){
        var KtelefonLAVal = $('#Ktelefon-LA').val();
        if(KtelefonLAVal == undefined || KtelefonLAVal == ""){
            ods.customerShippingTelephone = "";
        }else{
            ods.customerShippingTelephone = KtelefonLAVal;
        }
    }else{
        ods.customerShippingTelephone = ods.customerTelephone;
    }
    // 支付信息 Rabatt
    var rbtVal = parseInt($('#select-rabatt').val());
    ods.discount = 0;
    if(rbtVal > 0){
        ods.discount = rbtVal;
        var rbtPrice = 0 - ((Math.round(orderSummary.nettoPreissumme * rbtVal / 100))/100);
        var disPos = {};
        disPos.ean = "8888888888888";
        disPos.price = rbtPrice.toString();
        disPos.qInCart = 1;
        disPos.quantity = 1;
        disPos.title = rbtVal + "% Rabatt auf Ihre Bestellung";
        disPos.shipping_cost = 0;
        //disPos.tax = 0;
        disPos.tax = taxVal;
        ods.soldItems.items.push(disPos);
    }
    // 支付信息 Zahlungsmethode
    var inputZahlungsmethodeVal = $('#input-zahlungsmethode').val();
    if(inputZahlungsmethodeVal == undefined || inputZahlungsmethodeVal == ""){
        errorList.push("[Bei Schritt 3] Bitte geben Sie Zahlungsmethode ein.");
    }else {
        ods.paymentMethod = inputZahlungsmethodeVal;
    }
    // 支付信息 (已经支付了多少)
    ods.paidSum = orderSummary.gesamtsumme / 100;
    //ods.paidSum = (orderSummary.gesamtsumme + 1) / 100; // Ying gesamtsumme + 1, 应对Afterbuy里面差1cent的误差
    if(inputZahlungsmethodeVal == "Überweisung"){
        ods.paidSum = 0;

        if(ods.status != ""){
            ods.status = ods.status + ",Unbezahlt";
        }else{
            ods.status = "Unbezahlt";
        }

    }
    // 物流信息 Versandart
    var inputVersandartVal = $('#input-versandart').val();
    if(inputVersandartVal == undefined || inputVersandartVal == ""){
        ods.shippingMethod = "";
    }else {
        ods.shippingMethod = inputVersandartVal;
    }
    if(ods.shippingMethod == "Abholung"){
        var rbtPrice_abholung = 0 - ((Math.round(orderSummary.nettoPreissumme * rabattBeiAbholung / 100))/100);
        var disPos_abholung = {};
        disPos_abholung.ean = "9999999999999";
        disPos_abholung.price = rbtPrice_abholung.toString();
        disPos_abholung.qInCart = 1;
        disPos_abholung.quantity = 1;
        disPos_abholung.title = rabattBeiAbholung + "% Rabatt wegen Selbstabholung";
        disPos_abholung.shipping_cost = 0;
        //disPos_abholung.tax = 0;
        disPos_abholung.tax = taxVal;
        ods.soldItems.items.push(disPos_abholung);
    }
    ods.discount_abholung = rabattBeiAbholung;
    // 物流信息 Zusatzinfo
    // ...
    // 物流信息 Voraussichtliches Lieferdatum
    ods.expectedDeliveryDate = $('#v-l-datepicker').val();
    // 来自E2的客户ID，但是客户信息有可能修改过
    ods.customer_id = e2Data.customer_id;
    ods.customer_userIdPlattform = e2Data.customer_userIdPlattform;
    // 其他信息
    ods.customer_fax = "";
    ods.customer_title = "";
    ods.customer_shipping_fax = "";
    ods.customer_shipping_title = "";
    ods.order_id_e2 = "N/A";
    // 订单备注
    var orderCommentVal = $('#order-comment').val();
    if(orderCommentVal.length > 500){
        errorList.push("[Bei Schritt 4] Notiz darf maximal 500 Zeichen sein.");
    }else {
        ods.memo = orderCommentVal.replace(/\n|\r\n/g,"<br>");
    }


    if(errorList.length < 1){
        /**
         * AJAX发送数据
         */
        if(ods.status == ""){
            sentOrderToE2Ajax(ods);
        }else{
            ods.order_id_ab = "N/A";
            createOrderAjax(ods);
        }
    }else{
        removeLoadingLayer();
        var layerContent = "";
        for(var i=0; i<errorList.length; ++i){
            if(i>0){
                layerContent += "<br>";
            }
            layerContent = layerContent + (i+1) +".&nbsp;" + errorList[i] + "&nbsp;&nbsp;";
        }
        layer.open({
            title: 'Bestellung Fehler: ID-10016',
            icon: 2,
            content: layerContent,
            btn: ['Schließen']
        });
    }

}

function createOrderAjax(ods) {
    $.ajax({
        url:'/api/setorder',
        data: { "order_details": encodeURI(JSON.stringify(ods)) },
        dataType: "json",
        type: "POST",
        traditional: true,
        success: function (data) {
            removeLoadingLayer();
            if(data.isSuccess){
                // 清空购物车和所有表单
                clearOrderForm();

                //customAlert("Bestellung Tipp: ID-10017", 1, "Die Bestellung wurde erfolgreich erstellt.");
                /**
                 * 询问是否打开订单页面
                 */
                layer.confirm('Die Bestellung wurde erfolgreich erstellt.<br>Werden Sie die Bestellung ansehen?', {
                    icon: 1,
                    title: 'Bestellung Tipp: ID-10017',
                    btn: ['Ja, ansehen','Nein, hier bleiben'] //按钮
                }, function(){
                    window.location.href="/order/" + (parseInt(data.data.order_id) + 3000000).toString();
                }, function(){
                    //console.log(ods);
                });

            }else{
                customAlert("Bestellung Fehler: ID-10018", 2, data.msg);
            }
        }
    });
}

function sentOrderToE2Ajax(ods) {
    $.ajax({
        url:'/api/sendordertoe2',
        data: { "order_details": encodeURI(JSON.stringify(ods)) },
        dataType: "json",
        type: "POST",
        traditional: true,
        success: function (data) {
            // 如果成功，就拿着ID号，把订单存入本地数据库
            // 如果不成功，就报错
            if(data.isSuccess){
                var e2_rsp = eval('(' + data.data + ')');
                if(e2_rsp.name == "successful"){
                    var rsp_desc = e2_rsp.description;
                    var reg = /[^\(\)]+(?=\))/g;
                    var ab_id = rsp_desc.match(reg);
                    ods.order_id_ab = ab_id[0];
                    ods.status = "Versandvorbereitung";
                }else{
                    ods.order_id_ab = "N/A";
                    ods.status = "Fehler Bei Afterbuy";
                }
            }else{
                ods.status = "Fehler Bei GoodOne";
            }

            //console.log(ods);
            createOrderAjax(ods);
        }
    });
}

function isRightEmailFormat(e) {
    var res = false;
    var myreg = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    if(myreg.test(e)){
        res = true;
    }
    return res;
}

function clearOrderForm() {
    /**
     * 清空购物车
     */
    // 清空JS池
    shoppingCartArray = {};
    // 清除 LS
    localStorage.removeItem("shopping-cart");
    // 更新购物车
    updateShoppingCart();
    // 取消数字
    shopping_cart_pos_quantity_modify(0);
    /**
     * 清空客户信息
     */
    $('#Kemail').val('');
    $('#KVorname').val('');
    $('#KNachname').val('');
    $('#KFirma-RA').val('');
    $('#KVorname-RA').val('');
    $('#KNachname-RA').val('');
    $('#KStrasse-RA').val('');
    $('#KStrasse2-RA').val('');
    $('#KPLZ-RA').val('');
    $('#KOrt-RA').val('');
    $('#KBundesland-RA').val('DE');
    $('#Ktelefon-RA').val('');
    switchLieferanschriftForm("off");
    syncAdresseR2L();
    /**
     * 跟随着购物车一起刷新的Block
     */
    if($('#create-order-step-3').length > 0){
        rabatt = 0;
        rabattBeiAbholung = 0;
        updatePaymentSummary();
    }
    /**
     * 清空支付信息
     */
    $('#select-rabatt').val('0');
    $('#input-zahlungsmethode').val('');
    /**
     * 清空发货信息
     */
    $('#input-versandart').val('');
    $('#input-zusatzinfo').val('');
    $('#v-l-datepicker').datepicker("setDate", getDatum('de'));
    /**
     * 清空来自E2的客户id
     */
    e2Data.customer_id = 0;
    e2Data.customer_userIdPlattform = "NONE";
}

function activeInputForChangingPsw() {
    layer.prompt(
        {
            title: 'Aktuelle Passwort eingeben',
            formType: 1
        },
        function(pass, index){
            layer.close(index);
            showLoadingLayer();
            $.ajax({
                url:'/api/dopassword',
                data: {
                    "action": 'match',
                    "password": encodeURI(pass)
                },
                dataType: "json",
                type: "POST",
                traditional: true,
                success: function (data) {
                    removeLoadingLayer();
                    if(data.isSuccess){
                        var kpcpb = $('#konto-page-change-psw-btn');
                        kpcpb.html('Neue Passwort speichern');
                        kpcpb.toggleClass('btn-warning');
                        kpcpb.attr('onclick', 'updatePsw();');
                        $('#pwd-input-wrap').html('<input id="password" type="password" class="form-control" placeholder="Neue Passwort" /><div style="margin: 5px 0 8px 12px; color: #FF0000; line-height: 15px;">Das Passwort muss <ul style="margin: 3px 0 3px 25px;"><li>mindestens 4 Zeichen</li><li>mindestens eine Buchstabe</li><li>mindestens eine Zahl</li><li>mindestens ein Sonderzeichen</li><li>höchstens 50 Zeichen</li></ul>umfassen.</div>');
                    }else{
                        customAlert("Konto Fehler: ID-10019", 2, "Passwort ist falsch.");
                    }
                }
            });
        }
    );
}

function updatePsw() {
    var p = $('#password').val();
    //var reg = /^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{4,}$/; //Minimum 4 characters, at least one letter and one number
    var reg = /^(?=.*[A-Za-z])(?=.*\d)(?=.*[$@$!%*#?&])[A-Za-z\d$@$!%*#?&]{4,}$/; //Minimum 4 characters, at least one letter, one number and one special character
    if(reg.test(p) && p.length < 51){

        showLoadingLayer();
        $.ajax({
            url:'/api/dopassword',
            data: {
                "action": 'update',
                "password": encodeURI(p)
            },
            dataType: "json",
            type: "POST",
            traditional: true,
            success: function (data) {
                removeLoadingLayer();
                if(data.isSuccess){
                    var kpcpb = $('#konto-page-change-psw-btn');
                    kpcpb.html('Passwort ändern');
                    kpcpb.toggleClass('btn-warning');
                    kpcpb.attr('onclick', 'activeInputForChangingPsw();');
                    $('#pwd-input-wrap').html('&middot;&middot;&middot;&middot;&middot;&middot;&middot;&middot;');
                    customAlert("Konto Tipp: ID-10022", 1, "Das Passwort wurde erfolgreich geändert.");
                }else{
                    customAlert("Konto Fehler: ID-10021", 2, "Das Passwort wurde leider nicht geändert.<br>Bitte versuchen Sie es später noch einmal.");
                }
            }
        });

    }else{
        customAlert("Konto Fehler: ID-10020", 2, "Bitte&nbsp;geben&nbsp;Sie&nbsp;eine&nbsp;gültige&nbsp;Passwort&nbsp;ein.<br>Das&nbsp;Passwort&nbsp;muss&nbsp;&nbsp;<br>&nbsp;&nbsp;1.&nbsp;mindestens 4 Zeichen<br>&nbsp;&nbsp;2.&nbsp;mindestens eine Buchstabe<br>&nbsp;&nbsp;3.&nbsp;mindestens eine Zahl<br>&nbsp;&nbsp;4.&nbsp;mindestens ein Sonderzeichen<br>&nbsp;&nbsp;5.&nbsp;höchstens 50 Zeichen<br>umfassen.");
    }
}

function queryOrderMainInfos(type) {
    $.ajax({
        url:'/api/getordermaininfos',
        data: {
            type: type
        },
        dataType: "json",
        type: "POST",
        traditional: true,
        success: function (data) {
            if(data.isSuccess){
                drawOrderMainInfosTable(data.data);
            }
        }
    });
}

function drawOrderMainInfosTable(os) {
    var elems = os;
    var htmlTxt = '<div class="box">' +
        '            <div class="box-body">' +
        '              <table id="t-order-main-infos" class="table table-bordered table-striped">' +
        '                <thead>' +
        '                <tr>' +
        '                  <th></th>' +
        '                  <th>Bestellung-ID</th>' +
        '                  <th>Erstellzeit</th>' +
        '                  <th>Kundenname</th>' +
        '                  <th>Lieferort</th>' +
        '                  <th>Bestellung-ID (Afterbuy)</th>' +
        '                  <th>Ansprechpartner</th>' +
        '                  <th>status</th>' +
        '                </tr>' +
        '                </thead>' +
        '                <tbody>';

    for(var i = 0; i<elems.length; ++i){

        htmlTxt = htmlTxt + '<tr>' +
            '<td><span class="btn btn-primary btn-add-product-quantity" onclick="openOrder('+elems[i]["id"]+');">Details</span></td>' +
            '<td>' + parseInt(elems[i]["id"]) + '</td>' +
            '<td>' + elems[i]["create_at"] + '</td>' +
            '<td>' + elems[i]["customer_firstName"] + '&nbsp;' + elems[i]["customer_lastName"] + '</td>' +
            '<td>' + elems[i]["customer_shipping_ort"] + '</td>';

        var ab_id = elems[i]["order_id_ab"];
        if(ab_id.length == 10 && elems[i]["status"] == "Versandvorbereitung"){
            htmlTxt = htmlTxt + '<td><a href="https://farm02.afterbuy.de/afterbuy/auktionsliste.aspx?art=edit&id=' + ab_id + '" target="_blank">' + ab_id + '</a></td>';
        }else{
            htmlTxt = htmlTxt + '<td>' + ab_id + '</td>';
        }

        htmlTxt = htmlTxt + '<td>' + elems[i]["create_by"] + '</td>';

            var status = elems[i]["status"];
            if(status != 'Versandvorbereitung'){
                htmlTxt = htmlTxt + '<td style="background-color: #f39c12; color: #ffffff; ">' + elems[i]["status"] + '</td>';
            }else{
                htmlTxt = htmlTxt + '<td>' + elems[i]["status"] + '</td>';
            }

            '</tr>';

    }

    htmlTxt += '                </tbody>' +
        '              </table>' +
        '            </div>' +
        '            <!-- /.box-body -->' +
        '          </div>';

    $('#orders-wrap').html(htmlTxt);

    // 表格属性
    $('#t-order-main-infos').DataTable({
        "columns": [
            { "orderable": false },
            null,
            null,
            null,
            null,
            null,
            null,
            null
        ],
        'paging'      : true,
        'lengthChange': true,
        'searching'   : true,
        'ordering'    : true,
        'info'        : true,
        'autoWidth'   : true,
        "language": {
            "lengthMenu": "Auf jeder Seite maximal _MENU_ Bestellungen anzeigen.",
            "zeroRecords": "Keine Bestellung gefunden.",
            "info": "Seite _PAGE_ von _PAGES_",
            "infoEmpty": "Keine Bestellung gefunden.",
            "infoFiltered": "(filtered from _MAX_ total records)",
            "search": "Suche:",
            "paginate": {
                "first": "Erste Seite",
                "last": "Letzte Seite",
                "next": "Nächste Seite",
                "previous": "Vorherige Seite"
            }
        }
    });

    // 页面弹回顶部
    returnPageTop();
}

/**
 * 打开单一订单页面
 * @param id
 * 这个id为 GoodOne Order ID
 */
function openOrder(id) {
    location.href='/order/' + id;
}

/**
 * 根据客户的类型，设置增值税的值，并且显示税号输入Input
 */
function setTaxFrontend(t) {
    taxVal = parseInt(t);
    var vatNrWrap = $('#KVatNr-wrap');
    switch (taxVal){
        case 0:
            vatNrWrap.css('display', 'block');
            break;
        case 19:
            vatNrWrap.val('');
            vatNrWrap.css('display', 'none');
            break;
        default:
            taxVal = 19;
            vatNrWrap.val('');
            vatNrWrap.css('display', 'none');
    }
    updatePaymentSummary();
}

function setRabattBeiAbholung(t) {
    if(t == "Abholung"){
        rabattBeiAbholung = 5;
    }else{
        rabattBeiAbholung = 0;
    }

    updatePaymentSummary();
}

function setRabatt(o) {
    rabatt = o.value;
}

function updatePaymentSummary() {
    updateNettoPreissumme();
    updateRabattVal();
    updateRabattAbholungVal();
    updateBezahlungZwischensumme();
    updateMwStVal();
    updateBezahlungGesamtsumme();

    updateRabattSelectOptions();
}

function showPaymentMethodTipp(id) {
    var o = $('#payment-method-tipp-wrap');
    switch (id){
        case 0:
            o.css('display', 'none');
            o.html('');
            break;
        case 1:
            o.css('display', 'none');
            o.html('');
            break;
        case 2:
            o.html('<div class="alert alert-info alert-dismissible">\n' +
                '                                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>\n' +
                '                                                <div style="font-weight: bold; font-size: 16px;"><i class="icon fa fa-info"></i>Bankverbindung:</div>\n' +
                '                                                <div style="padding: 10px;">\n' +
                '                                                    <b>Institut:</b> Commerzbank\n' +
                '                                                    <br><b>BLZ:</b> 50040000\n' +
                '                                                    <br><b>Konto:</b> 413058900\n' +
                '                                                    <br><b>BIC:</b> COBADEFF004\n' +
                '                                                    <br><b>IBAN:</b> DE72 5004 0000 0413 0589 00\n' +
                '                                                    <br><b>USt-IdNr.:</b> DE265808049\n' +
                '                                                </div>\n' +
                '                                            </div>');
            o.css('display', 'block');
            break;
        case 3:
            o.css('display', 'none');
            o.html('');
            break;
        default:
            o.css('display', 'none');
            o.html('');
    }
}

function activeUpdateOrder() {
    $('#order-btn-bestellen').css('display', 'none');
    $('#order-btn-bearbeiten').css('display', 'none');
    $('#order-btn-print-wrap').css('display', 'none');
    $('#order-btn-speichern').css('display', 'block');
    $('#order-btn-abbrechen').css('display', 'block');
}

function deactiveUpdateOrder() {
    $('#order-btn-bestellen').css('display', 'block');
    $('#order-btn-bearbeiten').css('display', 'block');
    $('#order-btn-print-wrap').css('display', 'block');
    $('#order-btn-speichern').css('display', 'none');
    $('#order-btn-abbrechen').css('display', 'none');
}