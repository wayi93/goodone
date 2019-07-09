/**
 * 全局变量
 */
var momDE = moment().locale('de-DE');
var data_persistence_long = 60; //分钟
var groupedProducts = [];
groupedProducts['refresh'] = [];
groupedProducts['grouped-products'] = [];
groupedProducts['un-grouped-products'] = []; // key为ean, value为老孙给的所有数据
groupedProducts['product-top-categorys'] = [];
groupedProducts['master-product-quantity-in-category'] = [];
groupedProducts['refresh']['time'] = '2018-01-01 00:00:00';
/**
 * 大客户可以打的最高折扣
 */
var maxRabattPercentForPrivilegeUser = 0;
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
isloadshowChart1AjaxRunning = false;
isloadshowChart2AjaxRunning = false;
isexportAllProductsFulfillmentRateAjaxRunning = false;
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

/**
 * 全局变量
 */
let ersatzteilReasons = {};
let estShoppingCartGlobalObj;

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
 * 过滤ajax参数中的特殊不合法字符
 */
function enFilterParamDangerousChars(str){
    let res = str.replace(/'/g, "###DYH###");
    return res;
}


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
        mwst = Math.round(orderSummary.gesamtsumme * taxVal / (100 + taxVal));
    }
    // 显示
    setInputPrice('input-mwst', mwst);

    orderSummary.mwst = mwst;
}
/* 为了适应Paypal而修改算法 失败，还是去调整发给Paypal的参数
function updateMwStVal() {
    var mwst = 0;
    if(taxVal > 0){
        mwst = Math.round(orderSummary.zwischensumme * taxVal / 100);
    }
    // 显示
    setInputPrice('input-mwst', mwst);

    orderSummary.mwst = mwst;
}
*/

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
    //addToShoppingCartAnimation();

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
    var title = getTopCategoryByProductName(groupedProducts['un-grouped-products'][e].category) + " " + groupedProducts['un-grouped-products'][e].title;
    var picUrl = groupedProducts['un-grouped-products'][e].picUrl;
    var price = groupedProducts['un-grouped-products'][e].price;
    var shippingCost = groupedProducts['un-grouped-products'][e].shippingCost;

    // 动画
    if(parseInt(quantity) >= parseInt(h)){
        addToShoppingCartAnimation();
    }

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
function getMousePos(){
    Ev= getEvent();
    var mousePos = mouseCoords(Ev);
    return { 'x': mousePos.x, 'y': mousePos.y };
}
function mouseCoords(ev){
    if(ev.pageX||ev.pageY){
        return {x:ev.pageX, y:ev.pageY};
    }
    return{
        x:ev.clientX+document.body.scrollLeft-document.body.clientLeft,
        y:ev.clientY+document.body.scrollTop-document.body.clientTop
    };
}
//获取事件
function getEvent() {
    if (document.all) {
        return window.event;// 如果是ie
    }
    func = getEvent.caller;
    while (func != null) {
        var arg0 = func.arguments[0];
        if (arg0) {
            if ((arg0.constructor == Event || arg0.constructor == MouseEvent)
                || (typeof(arg0) == "object" && arg0.preventDefault && arg0.stopPropagation)) {
                return arg0;
            }
        }
        func = func.caller;
    }
    return null;
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
            showProducts_One(-1, pageId);
            break;
        case "ersatzteil-create":
            showProducts_One(-1, pageId);
            break;
        case "product-overview-0":
            setProductPieChart(0, "productPieChart-0");
            break;
        case "product-overview-1":
            setProductPieChart(1, "productPieChart-1");
            break;
        case "order-create":
            showCreateOrderSearchProductBlock("order");
            break;
        case "quote-create":
            showCreateOrderSearchProductBlock("quote");
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
function showCreateOrderSearchProductBlock(pageDW) {

    var t_c = groupedProducts['product-top-categorys'];

    var htmlTxt = '';

    if(pageDW == "order"){
        htmlTxt = htmlTxt + '<div class="callout callout-info">' +
            '<h4>Wollen Sie den Lagerbestand wegen der Bestellung ändern lassen?</h4>' +
            '<div class="form-group" style="margin-bottom: 0 !important;">' +
            '<label class="radio-order-change-stock">' +
            '<input type="radio" name="subtract_from_inventory" class="minimal-red" value="1" checked>' +
            '&nbsp;Ja, ändern' +
            '</label>' +
            '<label class="radio-order-change-stock">' +
            '<input type="radio" name="subtract_from_inventory" class="minimal-red" value="0">' +
            '&nbsp;Nein, nicht ändern ( z.B.: direkt aus China bestellen )' +
            '</label>' +
            '</div>' +
            '</div>';
    }

    htmlTxt = htmlTxt + '<h4>Methode 1: Stichwort suchen ( wie google )</h4>' +
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

        // 重写用户的 Input
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
        '                  <th class="t-a-l">EAN</th>' +
        '                  <th class="t-a-l">Produktname</th>' +
        '                  <th class="t-a-c">Verfügbar</th>' +
        '                  <th class="t-a-r">Preis (netto)</th>' +
        '                  <th class="t-a-c">Notsent</th>' +
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

        let res_ean = elems[i]["ean"];
        let res_title = elems[i]["title"];
        for(let ki = 0; ki < keys.length; ++ki){
            if(typeof res_ean == 'string'){
                res_ean = res_ean.replace(new RegExp(keys[ki],'gi'), '<span class="highlightForSearchResult">' + keys[ki] + '</span>');
                res_title = res_title.replace(new RegExp(keys[ki],'gi'), '<span class="highlightForSearchResult">' + keys[ki] + '</span>');
            }else{
                console.log('发现了EAN异常的产品：');
                console.log(elems[i]);
            }
        }

        htmlTxt = htmlTxt + '<tr>' +
            '<td>' + actRowHtml + '</td>' +
            '<td class="t-a-l">' + res_ean + '</td>' +
            '<td class="t-a-l">' + res_title + '</td>' +
            '<td class="t-a-c">' + elems[i]["quantity"] + '</td>' +
            '<td class="t-a-r">' + formatThePriceYing(elems[i]["price"], "EUR") + '&nbsp;&euro;&nbsp;&nbsp;&nbsp;&nbsp;</td>' +
            '<td class="t-a-c">' + elems[i]["notsent"] + '</td>' +
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
            "lengthMenu": "Auf jeder Seite maximal&nbsp;&nbsp;_MENU_&nbsp;&nbsp;Produkte anzeigen.",
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
 * @param pageId product-list 或 ersatzteil-create
 */
function showProducts_One(ptc_id, pageId) {
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
        '<li><a href="#" onclick=showProducts_One(-1,"' + pageId + '")>Alle Kategorien [' + (mpqic.hasOwnProperty("Alle Kategorien")?mpqic["Alle Kategorien"]:"0") + ']</a></li>';

    for(let i = 1; i<t_c.length; ++i){
        let isShow = true;

        if(pageId === 'ersatzteil-create'){
            if(t_c[i] === 'Ersatzteil'){
                isShow = false;
            }
        }

        if(isShow){
            htmlTxt = htmlTxt + '<li><a href="#" onclick=showProducts_One(' + i + ',"' + pageId + '")>' + t_c[i] + ' [' + (mpqic.hasOwnProperty(t_c[i])?mpqic[t_c[i]]:"0") + ']</a></li>';
        }
    }
    htmlTxt = htmlTxt + '<li><a href="#" onclick=showProducts_One(0,"' + pageId + '")>' + t_c[0] + ' [' + (mpqic.hasOwnProperty(t_c[0])?mpqic[t_c[0]]:"0") + ']</a></li>';

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
    }else if(pn.indexOf('Heizung-') > -1){
        tc = 'Heizung';
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
        '                  <th class="t-a-l">EAN</th>' +
        '                  <th class="t-a-l">Produktname</th>' +
        '                  <th class="t-a-c">Verfügbar</th>' +
        '                  <th class="t-a-r">Preis (netto)</th>' +
        '                  <th class="t-a-c">Notsent</th>' +
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
            '<td class="t-a-l">' + elems[i]["ean"] + '</td>' +
            '<td class="t-a-l">' + elems[i]["title"] + '</td>' +
            '<td class="t-a-c">' + elems[i]["quantity"] + '</td>' +
            '<td class="t-a-r">' + formatThePriceYing(elems[i]["price"], "EUR") + '&nbsp;&euro;&nbsp;&nbsp;&nbsp;&nbsp;</td>' +
            '<td class="t-a-c">' + elems[i]["notsent"] + '</td>' +
            '</tr>';
    }

    htmlTxt += '                </tbody>' +
        '                <tfoot>' +
        '                <tr>' +
        '                  <th></th>' +
        '                  <th class="t-a-l">EAN</th>' +
        '                  <th class="t-a-l">Produktname</th>' +
        '                  <th class="t-a-c">Verfügbar</th>' +
        '                  <th class="t-a-r">Preis (netto)</th>' +
        '                  <th class="t-a-c">Notsent</th>' +
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
            "lengthMenu": "Auf jeder Seite maximal&nbsp;&nbsp;_MENU_&nbsp;&nbsp;Produkte anzeigen.",
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
    /*
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
     */

    if(act.toLowerCase() === "on"){
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
function getProductStockHistoryByEAN_Ajax(e, zr) {

    if(!isloadProductHistoryAjaxRunning) {
        isloadProductHistoryAjaxRunning = true;

        $.ajax({
            url: '/api/getproductstockhistorybyean',
            data: {
                ean: get4250ean(e),
                zr: zr
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
    var numBigerThan5Nr = 0;
    var datas = [];
    for(var i=0; i<l.length; ++i){
        var data = {};
        data.datum = l[i][7].replace(/\./g, "-");
        data.num = l[i][3];
        // 计算有多少天是在线的 (小于等于5个就掉线)
        if(data.num > 5){
            numBigerThan5Nr++;
        }
        // 把负数的库存量都刷成0
        if(data.num < 0){
            data.num = 0;
        }
        data.dreitv = 0;
        if(l[i].length > 8 && l[i][8].indexOf("|") > -1){
            var umsatz_list = l[i][8].split("|");
            data.dreitv = umsatz_list[4];
        }
        productName = l[i][5];
        datas.push(data);
    }

    // 计算 Fulfillment
    var allPointsNr = datas.length;
    var fulfillmentRate = (Math.round(numBigerThan5Nr / allPointsNr * 10000)) / 100;

    var area = new Morris.Area({
        element: 'product-stock-history',
        resize: true,
        data: datas,
        xkey: 'datum',
        ykeys: ['num', 'dreitv'],
        labels: ['Lagerbestand', '3-Tage-Verkauft'],
        lineColors: ['#3c8dbc', '#dca9c3'],
        hideHover: 'auto',
        pointSize: 3
    });

    $('#lagerbestand-historie-product-name').html('<i class="fa fa-fw fa-search"></i>&nbsp;<b>Produkt:</b>&nbsp;' + productName + '&nbsp;&nbsp;&nbsp;&nbsp;<span style="color: #FF0000;"><b>Produkt Online Real Fulfillment:</b>&nbsp;' + fulfillmentRate + '%</span>');

}

function getProductStockHistory() {
    var val = $('#input-lagerbestand-historie-ean').val();
    var zr = $('#lagerbestand-historie-zeitraum').val();
    $('#lagerbestand-historie-product-name').html('');
    if(val.length == 13){
        $('#lagerbestand-historie-chart-wrap').html('<img src="/wp-content/uploads/images/loading-spinning-circles.svg" />');
        getProductStockHistoryByEAN_Ajax(val, zr);
    }else{
        $('#lagerbestand-historie-chart-wrap').html('');
    }
}

function initDatepicker(id) {
    let o = $('#'+id);
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
        case 'slash':
            datum = todayDatum_m + '/' + todayDatum_d + '/' + todayDatum_y;
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
    var zwischensumme = orderSummary.gesamtsumme - orderSummary.mwst;
    setInputPrice('input-summe-zwischensumme', zwischensumme);

    orderSummary.zwischensumme = zwischensumme;
}
/* 为了适应Paypal而修改算法 失败，还是去调整发给Paypal的参数
function updateBezahlungZwischensumme() {
    // 计算订单总额: Zwischensumme
    var zwischensumme = orderSummary.nettoPreissumme + orderSummary.rabatt + orderSummary.rabattBeiAbholung;
    setInputPrice('input-summe-zwischensumme', zwischensumme);

    orderSummary.zwischensumme = zwischensumme;
}
*/

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
    //var gesamtsumme =  orderSummary.zwischensumme + orderSummary.mwst;
    //var gesamtsumme = Math.round((orderSummary.nettoPreissumme + orderSummary.rabatt + orderSummary.rabattBeiAbholung) * (taxVal + 100) / 100);
    // 下面这个计算Gesamtsumme的方法是根据Afterbuy原理
    var gesamtsumme = 0;
    for(var k in shoppingCartArray){
        var qic = parseInt(shoppingCartArray[k].qInCart);
        var price = shoppingCartArray[k].price;
        gesamtsumme = gesamtsumme + (Math.round((price * 100) * (taxVal + 100) / 100) * qic);
    }
    gesamtsumme = gesamtsumme + Math.round(orderSummary.rabatt * (taxVal + 100) / 100);
    gesamtsumme = gesamtsumme + Math.round(orderSummary.rabattBeiAbholung * (taxVal + 100) / 100);

    setInputPrice('input-summe-gesamtsumme', gesamtsumme);

    orderSummary.gesamtsumme = gesamtsumme;
}
/* 为了适应Paypal而修改算法 失败，还是去调整发给Paypal的参数
function updateBezahlungGesamtsumme() {
    //var gesamtsumme =  orderSummary.zwischensumme + orderSummary.mwst;
    //var gesamtsumme = Math.round((orderSummary.nettoPreissumme + orderSummary.rabatt + orderSummary.rabattBeiAbholung) * (taxVal + 100) / 100);
    // 下面这个计算Gesamtsumme的方法是根据Afterbuy原理
    var gesamtsumme = Math.round(orderSummary.zwischensumme + orderSummary.mwst);
    setInputPrice('input-summe-gesamtsumme', gesamtsumme);

    orderSummary.gesamtsumme = gesamtsumme;
}
*/

/**
 * 只要购物车有变，就跟着执行这个
 */
function updateShippingCosts() {
    var shippingCosts = getShippingCosts();
    setInputPrice('input-summe-ver-Bear', shippingCosts);
}

function afterInputCustomerEmail() {
    o = $('#Kemail').val();
    if(isValueInList(o, bisZu35RabattUserList)){
        maxRabattPercentForPrivilegeUser = 35;
        $('#cb-kRB').prop("checked", true);
    }else if(isValueInList(o, bisZu25RabattUserList)){
        maxRabattPercentForPrivilegeUser = 25;
        $('#cb-kRB').prop("checked", true);
    }else{
        maxRabattPercentForPrivilegeUser = 0;
        $('#cb-kRB').prop("checked", false);
    }
    updateRabattSelectOptions();
    updatePaymentSummary();
}

function isValueInList(v, l) {
    var res = false;
    for(var i = 0; i < l.length; ++i){
        if(l[i].toLowerCase() == v.toLowerCase()){
            res = true;
            break;
        }
    }
    return res;
}

function updateRabattSelectOptions() {
    removeRabattBeschraenkung = $('#cb-kRB').is(":checked");
    //console.log(removeRabattBeschraenkung);
    if(!removeRabattBeschraenkung){
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
    }else{
        // 设置可以打折的折扣
        var o = $('#select-rabatt');
        var htmlTxt = '';
        var maxRabattPercent = maxRabattPercentForPrivilegeUser;
        for(var i=0; i<=maxRabattPercent; ++i){
            if(i == 0){
                htmlTxt = htmlTxt + '<option value="' + i + '">kein&nbsp;Rabatt</option>';
            }else if(i == rabatt){
                htmlTxt = htmlTxt + '<option value="' + i + '" selected>-&nbsp;' + i + '%</option>';
            }else{
                htmlTxt = htmlTxt + '<option value="' + i + '">-&nbsp;' + i + '%</option>';
            }
        }
        o.html(htmlTxt);
    }

    // test
    //console.log($('#select-rabatt').val());
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
    let maxLen = "200";
    let response_div_id = "";
    let error = '';

    if(id.substr(0, 19) === 'est-reason-ta-act2-'){
        let reasonIdLst = id.split('-');
        let reasonId = reasonIdLst[reasonIdLst.length - 1];
        maxLen = "800";
        response_div_id = "reason-len-tip-" + reasonId;
    }else{
        switch (id){
            case 'order-comment':
                maxLen = "800";
                response_div_id = "notiz-txt-len-1";
                break;
            case 'order-comment-kunden':
                maxLen = "800";
                response_div_id = "notiz-txt-len-2";
                break;
            case 'ersatzteil-reason-act1':
                maxLen = "800";
                response_div_id = "reason-len-tip";
                break;
            default:
                error = 'ID not exist.';
        }
    }

    if(error === ''){
        o = $('#'+id).val();
        $('#' + response_div_id).html(o.length + '/' + maxLen);

        if(o.length > parseInt(maxLen)){
            $('#'+id).val(o.substring(0,parseInt(maxLen)));
        }
    }else {
        console.log('[ ' + id + ' ] ' + error);
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

    if(customers.hasOwnProperty('firstName')){
        var newCustomers = [];
        newCustomers.push(customers);
        customers = newCustomers;
    }

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
            "lengthMenu": "Auf jeder Seite maximal&nbsp;&nbsp;_MENU_&nbsp;&nbsp;Produkte anzeigen.",
            "zeroRecords": "Kein Kunde gefunden.",
            "info": "Seite _PAGE_ von _PAGES_",
            "infoEmpty": "Kein Kunde gefunden.",
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

            // 判断这个用户是否可以享受35%Rabatt的优惠
            if(isValueInList(itm.mail, bisZu35RabattUserList)){
                maxRabattPercentForPrivilegeUser = 35;
                $('#cb-kRB').prop("checked", true);
            }else if(isValueInList(itm.mail, bisZu25RabattUserList)){
                maxRabattPercentForPrivilegeUser = 25;
                $('#cb-kRB').prop("checked", true);
            }else{
                maxRabattPercentForPrivilegeUser = 0;
                $('#cb-kRB').prop("checked", false);
            }
            updateRabattSelectOptions();

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

function retryCreateOrder(id, pageDW_id, subtract_from_inventory) {
    if(pageDW_id == 0){
        layer.confirm('Werden Sie es nochmal versuchen, um die Bestellung zu erstellen?', {
            icon: 3,
            title: 'Bestellung Tipp: ID-10024',
            btn: ['Ja, erstellen','Nein, abbrechen'] //按钮
        }, function(index){
            layer.close(index);
            showLoadingLayer();

            $.ajax({
                url:'/api/setoperationhistory',
                data: {
                    order_id : id,
                    message : 'Es wurde versucht, um die Bestellung an Afterbuy zu senden.',
                    doc_type : 0,
                    user_id : 0
                },
                dataType: "json",
                type: "POST",
                traditional: true,
                success: function (data) {
                    getPosQuantityWantInOrder_Ajax(id, subtract_from_inventory);
                }
            });

        }, function(index){
            layer.close(index);
        });
    }else if(pageDW_id == 1){
        layer.open({
            type: 1,
            skin: 'layui-layer-rim', //加上边框
            area: ['600px', '570px'], //宽高
            title: 'Bestellung Tipp: ID-10030',
            content: getPopupWinHTMLTxtWhenConvertQuoteToOrder(id)
        });
        initDatepicker('v-l-datepicker');
    }

}

/**
 * 会计查账用
 * @param id
 * @param pageDW_id
 * @param subtract_from_inventory
 * @param is_status_only_Unbezahlt
 * @param oldStatus
 */
function retryCreateOrder_paid(id, pageDW_id, subtract_from_inventory, is_status_only_Unbezahlt, oldStatus) {
    layer.confirm('Hat der Kunde die Bestellung wirklich bezahlt?', {
        icon: 3,
        title: 'Bestellung Tipp: ID-10031',
        btn: ['Ja, bezahlt','Nein, noch nicht'] //按钮
    }, function(index){
        layer.close(index);
        showLoadingLayer();
        //getPosQuantityWantInOrder_Ajax(id, subtract_from_inventory);
        var newData = {};
        switch(is_status_only_Unbezahlt){
            case 0:
                // 将状态修改为 Versandvorbereitung, 并且将订单发送至 Afterbuy
                // 如果不修改库存，那么就只改订单状态，不往Afterbuy发了
                if(subtract_from_inventory == 1){
                    retryCreateOrder_Ajax(id);
                }else{
                    newData.meta_id = id;
                    newData.order_id_ab = 'N/A';
                    newData.status = "Versandvorbereitung";
                    updateOrder(newData);
                }
                break;
            case 1:
            // 将状态中的 Unbezahlt 去掉
                var newStatus = '';
                if(oldStatus.indexOf(', Unbezahlt') > -1){
                    newStatus = oldStatus.replace(/, Unbezahlt/, "");
                }else if(oldStatus.indexOf('Unbezahlt, ') > -1){
                    newStatus = oldStatus.replace(/Unbezahlt, /, "");
                }
                newData.meta_id = id;
                newData.order_id_ab = 'N/A';
                newData.status = newStatus;
                updateOrder(newData);
                break;
            default:
            //
        }

        setOperationHistory(id, 'Die Zahlung wurde bestätigt.', 0, 0);

    }, function(index){
        layer.close(index);
    });
}

function cancelOrder(id, pageDW_id) {
    layer.confirm('Möchten Sie die Bestellung #' + (parseInt(id)+3000000) + ' wirklich stornieren?', {
        icon: 3,
        title: 'Bestellung Tipp: ID-10032',
        btn: ['Ja, stornieren','Nein'] //按钮
    }, function(index){
        layer.close(index);
        showLoadingLayer();

        var newData = {};
        newData.meta_id = id;
        newData.order_id_ab = 'N/A';
        newData.status = 'Storniert';
        updateOrder(newData);

        var historyMsg = '';
        if(pageDW_id == 0){
            historyMsg = 'Die Bestellung wurde storniert.';
        }else if(pageDW_id == 1){
            historyMsg = 'Das Angebot wurde storniert.';
        }
        setOperationHistory(id, historyMsg, pageDW_id, 0);

    }, function(index){
        layer.close(index);
    });
}

function getPopupWinHTMLTxtWhenConvertQuoteToOrder(id, subtract_from_inventory) {
    var htmlTxt = '<div class="padding-20"><div class="col-md-12"><div class="callout callout-info forbidSelectText"><p>Um Angebot in Bestellung zu umwandeln, bitte füllen Sie folgende Felder aus:</p></div><div class="box"><div class="box-body box-profile"><div class="padding-10">';
    // Zahlungsmethode
    htmlTxt = htmlTxt + '<label for="input-zahlungsmethode">Zahlungsmethode *</label>\n' +
        '                                            <div class="input-group">\n' +
        '                                                <span class="input-group-addon"><i class="fa fa-credit-card"></i></span>\n' +
        '                                                <input type="text" class="form-control" id="input-zahlungsmethode" disabled />\n' +
        '\n' +
        '                                                <div class="input-group-btn">\n' +
        '                                                    <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">setzen&nbsp;<span class="fa fa-caret-down"></span></button>\n' +
        '                                                    <ul class="dropdown-menu" style="margin-left: -86px;">\n' +
        '                                                        <li><a onclick="setInputVal(\'input-zahlungsmethode\', \'Barzahlung\');">Barzahlung</a></li>\n' +
        '                                                        <li><a onclick="setInputVal(\'input-zahlungsmethode\', \'EC-Karte\');">EC-Karte</a></li>\n' +
        '                                                        <li><a onclick="setInputVal(\'input-zahlungsmethode\', \'Überweisung\');">Überweisung</a></li>\n' +
        '                                                        <li><a onclick="setInputVal(\'input-zahlungsmethode\', \'Paypal\');">Paypal</a></li>\n' +
        '                                                    </ul>\n' +
        '                                                </div>\n' +
        '\n' +
        '                                            </div>';
    // Versandart
    htmlTxt = htmlTxt + '<div class="padding-t-10">\n' +
        '                                                <label for="input-versandart">Versandart</label>\n' +
        '                                                <div class="input-group date">\n' +
        '                                                    <div class="input-group-addon">\n' +
        '                                                        <i class="fa fa-truck"></i>\n' +
        '                                                    </div>\n' +
        '                                                    <input type="text" class="form-control pull-right" id="input-versandart" value="Zusendung" disabled />\n' +
        '\n' +
        '                                                    <div class="input-group-btn">\n' +
        '                                                        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">setzen&nbsp;<span class="fa fa-caret-down"></span></button>\n' +
        '                                                        <ul class="dropdown-menu" style="margin-left: -86px;">\n' +
        '                                                            <li><a onclick="setInputVal(\'input-versandart\', \'Abholung OR\');">Abholung OR</a></li>\n' +
        '                                                            <li><a onclick="setInputVal(\'input-versandart\', \'Zusendung\');">Zusendung</a></li>\n' +
        '                                                        </ul>\n' +
        '                                                    </div>\n' +
        '\n' +
        '                                                </div>\n' +
        '                                            </div>';
    // Voraussichtliches Lieferdatum
    htmlTxt = htmlTxt + '<div class="padding-t-10 padding-b-10">\n' +
        '                                                <label for="v-l-datepicker">Voraussichtliches Lieferdatum</label>\n' +
        '                                                <div class="input-group date">\n' +
        '                                                    <div class="input-group-addon">\n' +
        '                                                        <i class="fa fa-calendar"></i>\n' +
        '                                                    </div>\n' +
        '                                                    <input type="text" class="form-control pull-right" id="v-l-datepicker" />\n' +
        '                                                </div>\n' +
        '                                            </div>';
    // Wollen Sie den Lagerbestand wegen der Bestellung ändern lassen?
    htmlTxt = htmlTxt + '<div style="margin: 15px 0 10px 0; padding: 10px 10px 8px 10px; border: 1px solid #bbb; border-radius: 3px;">' +
        '<div><b>Wollen Sie den Lagerbestand wegen der Bestellung ändern lassen?</b></div>' +
        '<div class="form-group" style="margin-bottom: 0 !important;">' +
        '<label class="radio-order-change-stock" style="color: #000;">' +
        '<input type="radio" name="subtract_from_inventory" class="minimal-red" value="1" checked>' +
        '&nbsp;Ja, ändern' +
        '</label>' +
        '<label class="radio-order-change-stock" style="color: #000;">' +
        '<input type="radio" name="subtract_from_inventory" class="minimal-red" value="0">' +
        '&nbsp;Nein, nicht ändern' +
        '</label>' +
        '</div>' +
        '</div>';

    htmlTxt += '</div></div></div>';
    // BTN
    htmlTxt += '<div style="padding-left: 120px;"><button type="button" class="btn btn-primary" onclick="convertQuoteToOrder('+id+');">Ja, Bestellen</button><button type="button" style="margin-left: 50px;" class="btn btn-default layui-layer-close layui-layer-close1">Nein, Abbrechen</button></div>';
    htmlTxt += '</div></div>';
    return htmlTxt;
}

function convertQuoteToOrder(id) {
    showLoadingLayer();

    var errorList = [];
    var ods_add = {};

    // 是否减掉库存
    var subtract_from_inventory = "";
    var s_f_i = $("input[name='subtract_from_inventory']:checked").val();
    if(s_f_i == undefined){
        //
    }else{
        if(s_f_i == 0){
            subtract_from_inventory = "NO";
        }else{
            subtract_from_inventory = "YES";
        }
    }
    ods_add.subtract_from_inventory = subtract_from_inventory;

    // 支付信息 Zahlungsmethode
    let inputZahlungsmethodeVal = $('#input-zahlungsmethode').val();
    if(inputZahlungsmethodeVal == undefined || inputZahlungsmethodeVal == ''){
        errorList.push("Bitte geben Sie Zahlungsmethode ein.");
    }else {
        ods_add.paymentMethod = inputZahlungsmethodeVal;
    }

    // 物流信息 Versandart
    var inputVersandartVal = $('#input-versandart').val();
    if(inputVersandartVal == undefined || inputVersandartVal == ""){
        ods_add.shippingMethod = "";
    }else {
        ods_add.shippingMethod = inputVersandartVal;
    }
    // 物流信息 Voraussichtliches Lieferdatum
    ods_add.expectedDeliveryDate = $('#v-l-datepicker').val();

    //console.log(ods_add);

    if(errorList.length < 1){


        getPosQuantityInQuote_Ajax(id, ods_add);

        //createOrderAjax(ods);
        //sentOrderToE2Ajax(ods);

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

function getPosQuantityWantInOrder_Ajax(id, subtract_from_inventory) {
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

                // Log
                if(isNAL){
                    setOperationHistory(id, 'Der Lagerbestand wurde überprüft. Waren nicht genug.', 0, 88888888);
                }else{
                    setOperationHistory(id, 'Der Lagerbestand wurde überprüft.', 0, 88888888);
                }

                if(isNAL && subtract_from_inventory == 1){
                    removeLoadingLayer();
                    customAlert("Bestellung Fehler: ID-10025", 2, errorMsg);
                }else {

                    // 如果不修改库存，那么就只改订单状态，不往Afterbuy发了
                    if(subtract_from_inventory == 1){
                        retryCreateOrder_Ajax(id);
                    }else{
                        var newData = {};
                        newData.meta_id = id;
                        newData.order_id_ab = 'N/A';
                        newData.status = "Versandvorbereitung";
                        updateOrder(newData);
                    }

                }
            }else{
                removeLoadingLayer();
                customAlert("Bestellung Fehler: ID-10018", 2, data.msg);
            }
        }
    });
}

function getPosQuantityInQuote_Ajax(id, ods_add) {
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

                //console.log(isNAL);
                //console.log(ods_add);
                // Log
                if(isNAL){
                    setOperationHistory(id, 'Der Lagerbestand wurde überprüft. Waren nicht genug.', 1, 88888888);
                }else{
                    setOperationHistory(id, 'Der Lagerbestand wurde überprüft.', 1, 88888888);
                }

                ods_add.status = "";
                if(!isNAL && ods_add.paymentMethod != "Überweisung" && ods_add.paymentMethod != "Paypal" && ods_add.subtract_from_inventory != "NO"){
                    // 发送到E2，再到Afterbuy，再到本地
                    ods_add.status = "Versandvorbereitung";
                    updateQuoteToOrder("YES", id, ods_add);
                }else {
                    // 只存到本地
                    if(isNAL){
                        ods_add.status = "Nicht auf Lager";
                    }
                    if(ods_add.paymentMethod == "Überweisung" || ods_add.paymentMethod == "Paypal"){
                        if(ods_add.status == ""){
                            ods_add.status = "Unbezahlt";
                        }else{
                            ods_add.status = ods_add.status + ", Unbezahlt";
                        }
                    }
                    if(ods_add.status == "" && ods_add.subtract_from_inventory == "NO"){
                        ods_add.status = "Versandvorbereitung";
                    }
                    updateQuoteToOrder("NO", id, ods_add);
                }

            }else{
                removeLoadingLayer();
                customAlert("Bestellung Fehler: ID-10018", 2, data.msg);
            }
        }
    });
}

/**
 * Angebot 转 Bestellung
 * @param isToE2
 * @param id
 * @param ods_add
 */
function updateQuoteToOrder(isToE2, id, ods_add) {
    $.ajax({
        url:'/api/updatequotetoorder',
        data: {
            "isToE2" : isToE2,
            "id_db": id,
            "expectedDeliveryDate": ods_add.expectedDeliveryDate,
            "paymentMethod": ods_add.paymentMethod,
            "shippingMethod": ods_add.shippingMethod,
            "status": ods_add.status
        },
        dataType: "json",
        type: "POST",
        traditional: true,
        success: function (data) {
            if(data.isSuccess){

                console.log(data.data);

                layer.confirm('Die Bestellung wurde erfolgreich erstellt.<br>Werden Sie die Bestellung ansehen?', {
                    icon: 1,
                    title: 'Bestellung Tipp: ID-10017',
                    btn: ['Ja, ansehen','Nein, zu Angebotsliste'] //按钮
                }, function(){
                    window.location.href="/order/" + (parseInt(id) + 3000000).toString();
                }, function(){
                    window.location.href="/quote-list/";
                });

                var id_nr_str = (parseInt(id) + 3000000).toString();
                setOperationHistory(id, 'Das Angebot <a href=###SHUANGYINHAO###/quote/' + id_nr_str + '/###SHUANGYINHAO###>#' + id_nr_str + '</a> wurde erfolgreich an die Bestellung <a href=###SHUANGYINHAO###/order/' + id_nr_str + '/###SHUANGYINHAO###>#' + id_nr_str + '</a> umwandeln.', 1, 0);

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
                layer.confirm('Die Bestellung wurde erfolgreich aktualisiert.', {
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

function createOrder(pageDW) {

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


    /**
     * 判断Afterbuy的账户
     */
     ods.afterbuyAccount = 'maimai';
     let abKonto = $('#ab-konto').val();
     if(abKonto !== undefined){
         ods.afterbuyAccount = abKonto;
     }


    // 是否减掉库存
    var subtract_from_inventory = "";
    var s_f_i = $("input[name='subtract_from_inventory']:checked").val();
    if(pageDW === 'ersatzteil'){
        subtract_from_inventory = "YES";
    }else if(s_f_i == undefined){
        //
    }else{
        if(s_f_i == 0){
            subtract_from_inventory = "NO";
        }else{
            subtract_from_inventory = "YES";
        }
    }
    ods.subtract_from_inventory = subtract_from_inventory;
    //console.log(subtract_from_inventory);

    // 是否需要显示客户姓名
    // Sollen Vor- und Nachname in der PDF Dokument angezeigt werden?
    let show_customer_name_in_doc = 'Y';
    if($('#cb-wVNiPDF').is(":checked") || pageDW === 'ersatzteil'){
        show_customer_name_in_doc = 'Y';
    }else{
        show_customer_name_in_doc = 'N';
    }
    ods.show_customer_name_in_doc = show_customer_name_in_doc;


    // 订单 和 报价 状态
    ods.status = "";
    ods.status_quote = "";
    // 购物车 和 ersatzteil 的购物车
    if(pageDW === 'ersatzteil'){

        if(estShoppingCartGlobalObj === undefined || JSON.stringify(estShoppingCartGlobalObj.positions) === '{}'){

            ods.soldItems = {};
            ods.soldItems.items = [];
            errorList.push("[Bei Schritt 4] Ihr Warenkorb ist derzeit leer.");

        }else{

            ods.soldItems = {};
            ods.soldItems.items = [];
            let estPositions = estShoppingCartGlobalObj.positions;
            for(let ek in estPositions){
                let ean = estPositions[ek].ean;
                let price = 0;

                let qic = 0;
                let qic_sel_obj = $('#' + estPositions[ek].level + '-' + removeAllJingHao(estPositions[ek].fullPath) + '-menge');
                if(qic_sel_obj.val() !== undefined){
                    qic = parseInt(qic_sel_obj.val());
                }

                let reasons = '';
                let rs_sel_obj = $('#' + estPositions[ek].level + '-' + removeAllJingHao(estPositions[ek].fullPath) + '-reason');
                if(rs_sel_obj.val() !== undefined){
                    reasonsIdArr = rs_sel_obj.val();
                    if(typeof(reasonsIdArr) === 'object' && reasonsIdArr.length > 0){
                        for(let i_ria = 0; i_ria < reasonsIdArr.length; ++i_ria){
                            if(i_ria > 0){
                                reasons += ',';
                            }
                            reasons += reasonsIdArr[i_ria];
                        }
                    }
                }
                if(reasons === ''){
                    errorList.push("[Bei Schritt 4] Der Grund zum " + estPositions[ek].level + " [" + estPositions[ek].title + "] ist leer.");
                }

                let q = parseInt(estPositions[ek].quantity);
                if(q < 1){
                    errorList.push("[Bei Schritt 4] " + estPositions[ek].level + " [" + estPositions[ek].title + "] ist nicht auf Lager.");
                }
                let t = estPositions[ek].title;
                let sc = 0;
                let aPos = {};
                if(ods.afterbuyAccount === 'sogood'){
                    aPos.ean = get4250ean(ean);
                    aPos.mapping = get4250ean(estPositions[ek].fullPath);
                }else{
                    aPos.ean = get4251ean(ean);
                    aPos.mapping = get4251ean(estPositions[ek].fullPath);
                }
                aPos.price = price;
                aPos.qInCart = qic;
                aPos.quantity = q;
                aPos.title = t;
                aPos.shipping_cost = sc;
                aPos.tax = taxVal;
                aPos.reasons = reasons;
                ods.soldItems.items.push(aPos);

                if(qic > q){
                    ods.status = "Nicht auf Lager";
                }

            }

        }

    }else{

        if(getJsonLen(shoppingCartArray) < 1){
            ods.soldItems = {};
            ods.soldItems.items = [];
            errorList.push("[Bei Schritt 1] Ihr Warenkorb ist derzeit leer.");
        }else{
            ods.soldItems = {};
            ods.soldItems.items = [];
            for(let k in shoppingCartArray){
                let ean = shoppingCartArray[k].ean;
                let price = shoppingCartArray[k].price;
                let qic = parseInt(shoppingCartArray[k].qInCart);
                let q = parseInt(shoppingCartArray[k].quantity);
                let t = shoppingCartArray[k].title;
                let sc = shoppingCartArray[k].shippingCost;
                let aPos = {};
                aPos.ean = ean;
                aPos.price = price;
                aPos.qInCart = qic;
                aPos.quantity = q;
                aPos.title = t;
                aPos.shipping_cost = sc;
                aPos.tax = taxVal;
                aPos.reasons = '';
                aPos.mapping = '';
                ods.soldItems.items.push(aPos);

                if(qic > q){
                    ods.status = "Nicht auf Lager";
                    ods.status_quote = "Nicht auf Lager";
                }
            }
        }

    }


    // E-Mail Adresse
    var KemailVal = $('#Kemail').val();
    if(KemailVal == undefined || KemailVal == ""){
        //errorList.push("[Bei Schritt 2] Bitte geben Sie E-Mail-Adresse des Käufers ein.");
        ods.customerMail = 'keine.kunden.email@maimai24.de';
    }else if(!isRightEmailFormat(KemailVal)){
        errorList.push("[Bei Schritt 2] Bitte geben Sie eine gültige E-Mail-Adresse ein.");
    }else{
        ods.customerMail = KemailVal;
    }

    // Vorname
    let KVornameRAVal = $('#KVorname-RA').val();
    /**
     * 下面的9行，是处理 ersatzteil 创建页面， 没有 单独的姓名输入框的问题
     */
    if(pageDW === 'ersatzteil'){
        ods.goodoneCustomerFirstname = (KVornameRAVal === undefined) ? '' : KVornameRAVal;
    }else{
        let KVornameVal = $('#KVorname').val();
        if(KVornameVal == undefined || KVornameVal == ""){
            errorList.push("[Bei Schritt 2] Bitte geben Sie Vorname des Käufers ein.");
        }else{
            ods.goodoneCustomerFirstname = KVornameVal;
        }
    }
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
    let KNachnameRAVal = $('#KNachname-RA').val();
    /**
     * 下面的9行，是处理 ersatzteil 创建页面， 没有 单独的姓名输入框的问题
     */
    if(pageDW === 'ersatzteil'){
        ods.goodoneCustomerSurname = (KNachnameRAVal === undefined) ? '' : KNachnameRAVal;
    }else {
        let KNachnameVal = $('#KNachname').val();
        if (KNachnameVal == undefined || KNachnameVal == "") {
            errorList.push("[Bei Schritt 2] Bitte geben Sie Nachname des Käufers ein.");
        } else {
            ods.goodoneCustomerSurname = KNachnameVal;
        }
    }
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
    let rbtVal = parseInt($('#select-rabatt').val());
    ods.discount = 0;
    if($('#select-rabatt').val() !== undefined && rbtVal > 0){
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
    if(inputZahlungsmethodeVal == undefined || inputZahlungsmethodeVal == ''){
        if(pageDW === 'order'){
            errorList.push("[Bei Schritt 3] Bitte geben Sie Zahlungsmethode ein.");
        }else{
            ods.paymentMethod = '';
        }
    }else {
        ods.paymentMethod = inputZahlungsmethodeVal;
    }
    if(pageDW === 'ersatzteil'){
        ods.paymentMethod = 'Zahlung nicht erforderlich';
    }

    // 支付信息 (已经支付了多少)
    ods.paidSum = orderSummary.gesamtsumme / 100;
    if(inputZahlungsmethodeVal == "Überweisung" || inputZahlungsmethodeVal == "Paypal"){
        // ods.paidSum = 0;

        if(ods.status != ""){
            ods.status = ods.status + ", Unbezahlt";
        }else{
            ods.status = "Unbezahlt";
        }

        if(ods.status_quote != ""){
            ods.status_quote = ods.status_quote + ", Unbezahlt";
        }else{
            ods.status_quote = "Unbezahlt";
        }

    }
    // 物流信息 Versandart
    var inputVersandartVal = $('#input-versandart').val();
    if(inputVersandartVal == undefined || inputVersandartVal == ""){
        ods.shippingMethod = "";
    }else {
        ods.shippingMethod = inputVersandartVal;
    }
    if(ods.shippingMethod == "Abholung MR"){
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
    ods.expectedDeliveryDate = ($('#v-l-datepicker').val()) === undefined ? getDatum('slash') : $('#v-l-datepicker').val();
    // 来自E2的客户ID，但是客户信息有可能修改过
    ods.customer_id = e2Data.customer_id;
    ods.customer_userIdPlattform = e2Data.customer_userIdPlattform;
    // 其他信息
    ods.customer_fax = "";
    ods.customer_title = "";
    ods.customer_shipping_fax = "";
    ods.customer_shipping_title = "";
    ods.order_id_e2 = "N/A";
    // 订单备注 会显示在 Lieferschein 上面
    var orderCommentVal = $('#order-comment').val();
    if(orderCommentVal.length > 800){
        errorList.push("[Bei Schritt 4] Interne Notiz darf maximal 800 Zeichen sein.");
    }else {
        ods.memo = orderCommentVal.replace(/\n|\r\n/g,"<br>");
    }
    // 订单备注 展示给大客户的备注 会打印在 Angebot Rechnung 一系列单据上
    if($('#order-comment-kunden').val() !== undefined){
        let orderCommentKundenVal = $('#order-comment-kunden').val();
        if(orderCommentKundenVal.length > 800){
            errorList.push("[Bei Schritt 4] Kunden Notiz darf maximal 800 Zeichen sein.");
        }else {
            ods.memo_big_account = orderCommentKundenVal.replace(/\n|\r\n/g,"<br>");
        }
    }else{
        ods.memo_big_account = '';
    }

    /**
     * 如果订单是 Ersatzteil，那么需要在备注里面标明，原订单出处。
     * 如果订单是 Ersatzteil，加入原始客户afterbuy id
     */
    let jiaYiJuHua = '[Ersatzteillieferung] Die originale Afterbuy Order-ID: ' + $('#order-id').val() + '<br/>';
    if(pageDW === 'ersatzteil'){
        ods.memo = jiaYiJuHua + ods.memo;
        ods.memo_big_account = jiaYiJuHua + ods.memo_big_account;
        ods.afterbuy_customer_id = $('#afterbuy-customer-id-nr').html();
    }

    // 是 order 还是 quote 还是 ersatzteil
    ods.deal_with = pageDW;
    ods.first_deal_with = pageDW;


    /**
     * 对特殊字符进行编码
     */
    ods.customerShippingStreet = enFilterParamDangerousChars(ods.customerShippingStreet);
    ods.customerStreet = enFilterParamDangerousChars(ods.customerStreet);
    ods.goodoneCustomerShippingStreet = enFilterParamDangerousChars(ods.goodoneCustomerShippingStreet);
    ods.goodoneCustomerShippingStreet1 = enFilterParamDangerousChars(ods.goodoneCustomerShippingStreet1);
    ods.goodoneCustomerStreet = enFilterParamDangerousChars(ods.goodoneCustomerStreet);
    ods.goodoneCustomerStreet1 = enFilterParamDangerousChars(ods.goodoneCustomerStreet1);
    ods.customerCity = enFilterParamDangerousChars(ods.customerCity);
    ods.customerShippingCity = enFilterParamDangerousChars(ods.customerShippingCity);
    ods.memo = enFilterParamDangerousChars(ods.memo);
    ods.memo_big_account = enFilterParamDangerousChars(ods.memo_big_account);


    if(errorList.length < 1){

        /**
         * 先向E2请求Rechnungsnummer
         */
        $.ajax({
            url: '/api/getdocumentnumberfromentelliship',
            data: {
                doctyp: 3
            },
            type: 'post',
            cache: false,
            dataType: 'json',
            success: function (data) {
                ods.invoiceNr = "";
                if (data.name == "successful") {
                    ods.invoiceNr = data.invoiceNr;

                    /**
                     * AJAX发送数据
                     */

                    if(ods.deal_with === 'ersatzteil'){

                        sentOrderToAfterbuyAjax(ods);
                        //console.log(ods);

                    }else{

                        if(ods.status != "" || ods.deal_with != "order" || ods.subtract_from_inventory == "NO"){

                            if(ods.deal_with == "quote"){
                                if(ods.status_quote == ""){
                                    ods.status_quote = "Erstellt";
                                }
                            }else{
                                if(ods.subtract_from_inventory == "NO" && ods.status == ""){
                                    ods.status = "Versandvorbereitung";
                                }
                            }

                            ods.order_id_ab = "N/A";

                            // 只在GoodOne本地保存订单
                            createOrderAjax(ods);

                        }else{
                            // ods.status 是空的
                            // 并且
                            // ods.deal_with 是 order
                            // 订单发送到E2，老孙会把订单转给Afterbuy

                            sentOrderToE2Ajax(ods);

                        }

                    }


                }else{
                    // 显示报错窗口
                    removeLoadingLayer();
                }
            },
            error: function () {
                // view("异常！");
                // alert("异常！");
            }
        });


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
                    window.location.href="/" + ods.first_deal_with + "/" + (parseInt(data.data.order_id) + 3000000).toString();
                }, function(){
                    //console.log(ods);
                });

            }else{
                customAlert("Bestellung Fehler: ID-10018", 2, data.msg);
            }
        }
    });
}

function sentOrderToAfterbuyAjax(ods) {
    $.ajax({
        url:'/api/afterbuy-createorder',
        data: { "order_details": encodeURI(JSON.stringify(ods)) },
        dataType: "json",
        type: "POST",
        traditional: true,
        success: function (data) {
            // 如果成功，就拿着ID号，把订单存入本地数据库
            // 如果不成功，就报错
            if(data.isSuccess){
                ods.order_id_ab = data.msg;
                ods.status = "Versandvorbereitung";
            }else{
                ods.status = "Fehler Bei Afterbuy Schnittstelle.";
            }

            //console.log(ods);
            createOrderAjax(ods);
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
    $('#payment-method-tipp-wrap').css('display', 'none');
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
            formType: 1,
            btn: ['Weiter', 'Abbrechen']
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

function queryOrderMainInfos(type, pageDW) {
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
                drawOrderMainInfosTable(data.data, pageDW);
            }
        }
    });
}

function drawOrderMainInfosTable(os, pageDW) {
    let pageDW_title = "";
    let pageDW_title_plural = "";
    let pageDW_id = 0;
    switch(pageDW){
        case 'order':
            pageDW_title = "Bestellung";
            pageDW_title_plural = "Bestellungen";
            pageDW_id = 0;
            break;
        case 'quote':
            pageDW_title = "Angebot";
            pageDW_title_plural = "Angebote";
            pageDW_id = 1;
            break;
        case 'ersatzteil':
            pageDW_title = "Ersatzteil Bestellung";
            pageDW_title_plural = "Ersatzteil Bestellungen";
            pageDW_id = 2;
            break;
        default:
            //
    }

    var elems = os;
    var htmlTxt = '<div class="box">' +
        '            <div class="box-body">' +
        '              <table id="t-order-main-infos" class="table table-bordered table-striped" style="width: 100% !important;">' +
        '                <thead>' +
        '                <tr>' +
        //'                  <th class="order-tbl-col-hidn-7"></th>' +
        '                  <th class="t-a-c">' + pageDW_title + '-ID</th>';

    if(pageDW != "quote"){
        htmlTxt = htmlTxt + '                  <th class="t-a-c">Rechnungsnr.</th>';
    }

    htmlTxt = htmlTxt + '                  <th class="t-a-c order-tbl-col-hidn-5">Erstellzeit</th>' +
        '                  <th class="t-a-l order-tbl-col-hidn-4">Kundenname</th>' +
        '                  <th class="t-a-l order-tbl-col-hidn-1">Lieferort</th>';

    if(pageDW != "quote"){
        htmlTxt += '<th class="t-a-c order-tbl-col-hidn-8">Order-ID (Afterbuy)</th>';
    }

    if(pageDW !== "ersatzteil"){
        htmlTxt += '<th class="t-a-r order-tbl-col-hidn-9">Gesamtsumme (brutto)</th>';
    }

    htmlTxt += '                  <th class="t-a-l order-tbl-col-hidn-6">Erstellt von</th>' +
        '                  <th class="t-a-l order-tbl-col-hidn-2">Aktualisiert von</th>';

    // 是否减掉库存
    if(pageDW != "quote"){
        htmlTxt = htmlTxt + '<th class="t-a-l order-tbl-col-hidn-3">Lagerbestand ändern?</th>';
    }

    htmlTxt = htmlTxt + '                  <th class="t-a-l">status</th>' +
        '                </tr>' +
        '                </thead>' +
        '                <tbody>';

    for(var i = 0; i<elems.length; ++i){

        var status = "";
        switch(pageDW){
            case 'order':
                status = elems[i]["status"];
                break;
            case 'quote':
                status = elems[i]["status_quote"];
                break;
            case 'ersatzteil':
                status = elems[i]["status"];
                break;
            default:
                //
        }

        // 把订单和报价 列表的ID都改造成 Link
        var link_key = 'order';
        switch(pageDW_id){
            case 0:
                link_key = 'order';
                break;
            case 1:
                link_key = 'quote';
                break;
            case 2:
                link_key = 'ersatzteil';
                break;
            default:
                link_key = 'order';
        }
        var id_with_link = '<a href="/' + link_key + '/' + elems[i]["id"] + '/" target="_blank">' + elems[i]["id"] + '</a>';

        htmlTxt = htmlTxt + '<tr>' +
            //'<td class="order-tbl-col-hidn-7"><span class="btn btn-primary btn-add-product-quantity" onclick="openOrder('+elems[i]["id"]+', ' + pageDW_id + ');">Details</span></td>' +
            '<td class="t-a-c">' + id_with_link + '</td>';

        if(pageDW != "quote"){
            htmlTxt = htmlTxt + '<td class="t-a-c">' + (elems[i]["number"] === null ? "N/A" : elems[i]["number"]) + '</td>';
        }

        htmlTxt = htmlTxt + '<td class="t-a-c order-tbl-col-hidn-5">' + elems[i]["create_at"] + '</td>' +
            '<td class="t-a-l order-tbl-col-hidn-4">' + elems[i]["customer_firstName"] + '&nbsp;' + elems[i]["customer_lastName"] + '</td>' +
            '<td class="t-a-l order-tbl-col-hidn-1">' + elems[i]["customer_shipping_ort"] + '</td>';

        if(pageDW != "quote"){
            var ab_id = elems[i]["order_id_ab"];
            if(ab_id.length > 8 && ab_id.length < 11 && status == "Versandvorbereitung"){
                htmlTxt = htmlTxt + '<td class="t-a-c order-tbl-col-hidn-8"><a href="https://farm02.afterbuy.de/afterbuy/auktionsliste.aspx?art=edit&id=' + ab_id + '" target="_blank">' + ab_id + '</a></td>';
            }else{
                htmlTxt = htmlTxt + '<td class="t-a-c order-tbl-col-hidn-8">' + ab_id + '</td>';
            }
        }

        if(pageDW !== "ersatzteil"){
            htmlTxt = htmlTxt + '<td class="t-a-r order-tbl-col-hidn-9">' + formatThePriceYing(elems[i]["paidSum"], "EUR") + '&nbsp;&euro;&nbsp;&nbsp;&nbsp;&nbsp;</td>';
        }

        htmlTxt = htmlTxt + '<td class="t-a-l order-tbl-col-hidn-6">' + elems[i]["create_by"] + '</td>';
        htmlTxt = htmlTxt + '<td class="t-a-l order-tbl-col-hidn-2">' + elems[i]["update_by"] + '</td>';

        // 是否减掉库存
        if(pageDW != "quote"){
            var subtract_from_inventory = elems[i]["subtract_from_inventory"];
            if(subtract_from_inventory == "YES"){
                htmlTxt = htmlTxt + '<td class="t-a-l order-tbl-col-hidn-3">Ja</td>';
            }else if(subtract_from_inventory == "NO"){
                htmlTxt = htmlTxt + '<td class="t-a-l order-tbl-col-hidn-3">Nein</td>';
            }else{
                htmlTxt = htmlTxt + '<td class="t-a-l order-tbl-col-hidn-3">Ja</td>';
            }
        }

        if(status.indexOf('Storniert') > -1){
            htmlTxt = htmlTxt + '<td class="t-a-l" style="background-color: #b2b2b2; color: #ffffff; ">' + status + '</td>';
        }else if(status != 'Versandvorbereitung' && status != 'Erstellt' && status != 'Bestellt'){
            htmlTxt = htmlTxt + '<td class="t-a-l" style="background-color: #f39c12; color: #ffffff; ">' + status + '</td>';
        }else{
            htmlTxt = htmlTxt + '<td class="t-a-l">' + status + '</td>';
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
    let columnsArr = [];
    switch(pageDW){
        case 'order':
            columnsArr = [
                //{ "orderable": false },
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null];
            break;
        case 'quote':
            columnsArr = [
                //{ "orderable": false },
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null];
            break;
        case 'ersatzteil':
            columnsArr = [
                //{ "orderable": false },
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null];
            break;
        default:
        //
    }




    if(pageDW != "quote"){

    }else{

    }
    $('#t-order-main-infos').DataTable({
        "columns": columnsArr,
        "order": [[ 0, "desc" ]],
        'paging'      : true,
        'lengthChange': true,
        'searching'   : true,
        'ordering'    : true,
        'info'        : true,
        'autoWidth'   : true,
        "language": {
            "lengthMenu": "Auf jeder Seite maximal&nbsp;&nbsp;_MENU_&nbsp;&nbsp;" + pageDW_title_plural + " anzeigen.",
            "zeroRecords": "Keine " + pageDW_title + " gefunden.",
            "info": "Seite _PAGE_ von _PAGES_",
            "infoEmpty": "Keine " + pageDW_title + " gefunden.",
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
function openOrder(id, pageDW_id) {
    if(pageDW_id == 0){
        location.href='/order/' + id;
    }else if(pageDW_id == 1){
        location.href='/quote/' + id;
    }
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

function setRabattBeiAbholung(i) {
    rabattBeiAbholung = i;
    updatePaymentSummary();
}

function setRabatt(o) {
    rabatt = o.value;
}

function updatePaymentSummary() {

    updateNettoPreissumme();
    updateRabattVal();
    updateRabattAbholungVal();

    updateBezahlungGesamtsumme();
    updateMwStVal();
    updateBezahlungZwischensumme();
    /* 为了适应Paypal而修改算法 失败，还是去调整发给Paypal的参数
    updateBezahlungZwischensumme();
    updateMwStVal();
    updateBezahlungGesamtsumme();
    */
    updateRabattSelectOptions();


    /**
     * 为了解决BUG，再来一次。。。
     */
    updateNettoPreissumme();
    updateRabattVal();
    updateRabattAbholungVal();
    updateBezahlungGesamtsumme();
    updateMwStVal();
    updateBezahlungZwischensumme();

}

function showPaymentMethodTipp(id) {
    var o = $('#payment-method-tipp-wrap');
    switch (id){
        case 0:
            o.html('<div class="alert alert-info alert-dismissible">\n' +
                '                                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>\n' +
                '                                                <div style="font-weight: bold; font-size: 16px;"><i class="icon fa fa-info"></i>Barzahlung:</div>\n' +
                '                                                <div style="padding: 10px;">\n' +
                '                                                    Wenn der Kunde nicht 100% des Beitrags bezahlt hat, bitte wählen Sie die Option [Barzahlung] nicht.' +
                '                                                </div>\n' +
                '                                            </div>');
            o.css('display', 'block');
            break;
        case 1:
            o.html('<div class="alert alert-info alert-dismissible">\n' +
                '                                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>\n' +
                '                                                <div style="font-weight: bold; font-size: 16px;"><i class="icon fa fa-info"></i>EC-Karte:</div>\n' +
                '                                                <div style="padding: 10px;">\n' +
                '                                                    Wenn der Kunde nicht 100% des Beitrags bezahlt hat, bitte wählen Sie die Option [EC-Karte] nicht.' +
                '                                                </div>\n' +
                '                                            </div>');
            o.css('display', 'block');
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
            o.html('<div class="alert alert-info alert-dismissible">\n' +
                '                                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>\n' +
                '                                                <div style="font-weight: bold; font-size: 16px;"><i class="icon fa fa-info"></i>Paypal:</div>\n' +
                '                                                <div style="padding: 10px;">\n' +
                '                                                    Nachdem die Bestellung erstellt wird, bekommt der Kunde einen QR-Code Auf der Auftragsbestätigung. Damit kann der Kunde mit Paypal Konto im Handy zahlen.' +
                '                                                </div>\n' +
                '                                            </div>');
            o.css('display', 'block');
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

function openAnhangWindow(id,dw) {
    //iframe层
    layer.open({
        type: 2,
        title: 'Anhang von ' + dw + ' #' + id,
        shadeClose: true,
        shade: 0.8,
        area: ['800px', '600px'],
        content: '/attachment/' + id + '/', //iframe的url
        end: function () {
            //showLoadingLayer();
            //location.reload();
            updateFileQuantityOrderPage(id);
        }
    });
}

function getMonthTitle(id)
{
    $title = '';
    switch(parseInt(id)){
        case 1:
            $title = 'Januar';
            break;
        case 2:
            $title = 'Februar';
            break;
        case 3:
            $title = 'März';
            break;
        case 4:
            $title = 'April';
            break;
        case 5:
            $title = 'Mai';
            break;
        case 6:
            $title = 'Juni';
            break;
        case 7:
            $title = 'Juli';
            break;
        case 8:
            $title = 'August';
            break;
        case 9:
            $title = 'September';
            break;
        case 10:
            $title = 'Oktober';
            break;
        case 11:
            $title = 'November';
            break;
        case 12:
            $title = 'Dezember';
            break;
        default:
        //
    }
    return $title;
}

/**
 * ID 格式举例 3000012
 * @param id
 */
function updateFileQuantityOrderPage(id) {
    $('#file-quantity-'+id).html('<img src="/wp-content/uploads/images/loading-spin-1s-200px.svg" style="height: 16px !important;" />');
    $.ajax({
        url:'/api/getfilequantity',
        data: {
            orderId: id
        },
        dataType: "json",
        type: "POST",
        traditional: true,
        success: function (data) {
            if(data.isSuccess){
                $('#file-quantity-'+id).html(data.data.fileQuantity);
            }
        }
    });
}

/**
 * 切换
 * Wird Vorname und Nachname in der PDF Dokument angezeigt?
 */
function switch_cb_wVNiPDF() {
    var isChecked = $('#cb-wVNiPDF').is(":checked");
    if(isChecked){
        $('#KVorname').val('');
        $('#KNachname').val('');
        $('#KVorname-RA').val('');
        $('#KNachname-RA').val('');
        $('#KVorname-LA').val('');
        $('#KNachname-LA').val('');
    }else{
        $('#KVorname').val('N/A');
        $('#KNachname').val('N/A');
        $('#KVorname-RA').val('N/A');
        $('#KNachname-RA').val('N/A');
        $('#KVorname-LA').val('N/A');
        $('#KNachname-LA').val('N/A');
    }
}

function showChart1() {

    if(!isloadshowChart1AjaxRunning){

        $('#chart-1-product-name').html('');

        var ean = $('#chart-1-ean').val();
        var long = $('#chart-1-zeitraum').val();
        var interval = 3;

        if(ean.length == 13){
            $('#chart-1-wrap').html('<img src="/wp-content/uploads/images/loading-spinning-circles.svg" />');

            // 开始读取数据
            isloadshowChart1AjaxRunning = true;
            $.ajax({
                url:'/api/getsoldquantity',
                data:{
                    ean: get4250ean(ean),
                    long: long,
                    interval: interval
                },
                type:'post',
                cache:false,
                dataType:'json',
                success:function(data) {
                    if(data.isSuccess && parseInt(data.data_quantity) > 0){
                        drawChart1(data.data);
                    }else{
                        $('#chart-1-product-name').html('&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-fw fa-search"></i>&nbsp;&nbsp;&nbsp;&nbsp;<span style="color: #FF0000;">Keine Produktdaten gefunden.</span>');
                        $('#chart-1-wrap').html('');
                    }
                    isloadshowChart1AjaxRunning = false;
                },
                error : function() {
                    // view("异常！");
                    // alert("异常！");
                }
            });

        }else{
            $('#chart-1-wrap').html('');
        }

    }

}

function drawChart1(l) {
    $('#chart-1-wrap').html('<div class="chart" id="chart-1-huabu" style="height: 300px;"></div>');

    var productName = '';

    /*
        判断每三天显示一次，今天当天的需要显示
         */
    var interval = 3;

    //整理数据
    var datas = [];
    var num_list_for_median = [];
    for(var i=0; i<l.length; ++i){

        /*
        判断每三天显示一次，今天当天的需要显示
         */
        if((i % interval) == 2){

            var data = {};
            data.datum = l[i]["date"].replace(/\./g, "-");
            data.num = 0;
            if(l[i]["quantity_data"].indexOf("|") > -1){
                var num_list = l[i]["quantity_data"].split("|");
                data.num = num_list[4];
            }
            productName = l[i]["product_name"];

            /**
             * 暂时忽略 2018年8月1日 以前的数据
             */
            if(isDateAfterDate(data.datum, '2018-07-31')){
                datas.push(data);
                num_list_for_median.push(parseInt(data.num));
            }

        }

    }

    /**
     * 取出中位值
     */
    num_list_for_median.sort(sortNumber);
    var num_median_id = Math.floor(num_list_for_median.length/2);
    var num_median = num_list_for_median[num_median_id];

    var area = new Morris.Line({
        element: 'chart-1-huabu',
        resize: true,
        data: datas,
        xkey: 'datum',
        ykeys: ['num'],
        labels: ['3-Tage-Verkauft'],
        lineColors: ['#3c8dbc'],
        goals: [num_median],
        goalLineColors: ['#FF0000'],
        hideHover: 'auto',
        pointSize: 3
    });

    $('#chart-1-product-name').html('&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-fw fa-search"></i>&nbsp;&nbsp;<b>Produkt:&nbsp;' + productName + '</b>&nbsp;&nbsp;&nbsp;&nbsp;<span style="color: #FF0000;">Die rote gerade horizontale Linie bedeutet Median: ' + num_median + ' Stück.</span>');
}

function showChart2() {

    if(!isloadshowChart2AjaxRunning){

        $('#chart-2-product-name').html('');

        var ean = $('#chart-2-ean').val();
        var long = $('#chart-2-zeitraum').val();
        var interval = $('#chart-2-einheitszeit-laenge').val();
        var fulfillment = $('#chart-2-fulfillment').val();

        if(ean.length == 13){
            $('#chart-2-wrap').html('<img src="/wp-content/uploads/images/loading-spinning-circles.svg" />');

            // 开始读取数据
            isloadshowChart2AjaxRunning = true;
            $.ajax({
                url:'/api/getsoldquantity',
                data:{
                    ean: get4250ean(ean),
                    long: long,
                    interval: interval
                },
                type:'post',
                cache:false,
                dataType:'json',
                success:function(data) {
                    if(data.isSuccess && parseInt(data.data_quantity) > 0){
                        drawChart2(data.data, interval, long, fulfillment);
                    }else{
                        $('#chart-2-product-name').html('&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-fw fa-search"></i>&nbsp;&nbsp;&nbsp;&nbsp;<span style="color: #FF0000;">Keine Produktdaten gefunden.</span>');
                        $('#chart-2-wrap').html('');
                    }
                    isloadshowChart2AjaxRunning = false;
                },
                error : function() {
                    // view("异常！");
                    // alert("异常！");
                }
            });

        }else{
            $('#chart-2-wrap').html('');
        }

    }

}

function drawChart2(l, interval, long, fulfillment) {
    //console.log(fulfillment);

    $('#chart-2-wrap').html('<div class="chart" id="chart-2-huabu" style="height: 300px;"></div>');

    var productName = '';

    //整理数据
    var datas = [];
    var num_list_for_calculate = [];
    for(var i=0; i<l.length; ++i){

        var data = {};
        data.datum = l[i]["date"].replace(/\./g, "-");
        data.num = 0;
        if(l[i]["quantity_data"].indexOf("|") > -1){
            var num_list = l[i]["quantity_data"].split("|");
            switch (parseInt(interval)){
                case 3:
                    data.num = num_list[4];
                    break;
                case 7:
                    data.num = num_list[3];
                    break;
                case 14:
                    data.num = num_list[2];
                    break;
                case 30:
                    data.num = num_list[1];
                    break;
                case 90:
                    data.num = num_list[0];
                    break;
                default:
                    data.num = num_list[4];
            }

        }
        productName = l[i]["product_name"];

        /**
         * 暂时忽略 2018年8月1日 以前的数据
         */
        if(isDateAfterDate(data.datum, '2018-07-31')){
            datas.push(data);
            num_list_for_calculate.push(parseInt(data.num));
        }

    }

    /**
     * 取出最大值最小值，还有刻度
     */
    num_list_for_calculate.sort(sortNumber);
    //console.log(num_list_for_calculate);
    var min_num = num_list_for_calculate[0];
    var max_num = num_list_for_calculate[num_list_for_calculate.length - 1];
    var graduation_num = 10;
    switch(parseInt(long)){
        case 365:
            graduation_num = 10;
            break;
        case 550:
            graduation_num = 15;
            break;
        default:
            graduation_num = 10;
    }
    graduation = (parseInt(max_num) - parseInt(min_num)) / graduation_num;
    //console.log(min_num);
    //console.log(max_num);
    //console.log(graduation);

    var final_datas = [];
    for(var j = 0; j < graduation_num; ++j){
        var final_data = {};
        final_data.min_num = Math.round((min_num + j * graduation) * 100) / 100;
        if(j == (graduation_num - 1)){
            final_data.max_num = max_num;
        }else{
            final_data.max_num = Math.round((min_num + (j + 1) * graduation) * 100) / 100;
        }

        /**
         * 统计，在这个区间里，有多少个数据
         */
        var howmany = 0;
        for(var z = 0; z < num_list_for_calculate.length; ++z){
            if(j == (graduation_num - 1)){
                if(parseFloat(num_list_for_calculate[z]) >= parseFloat(final_data.min_num) && parseFloat(num_list_for_calculate[z]) <= parseFloat(final_data.max_num)){
                    howmany++;
                }
            }else{
                if(parseFloat(num_list_for_calculate[z]) >= parseFloat(final_data.min_num) && parseFloat(num_list_for_calculate[z]) < parseFloat(final_data.max_num)){
                    howmany++;
                }
            }
        }
        final_data.title = final_data.min_num + '->' + final_data.max_num;
        final_data.howmany = howmany;

        final_datas.push(final_data);

    }

    //console.log(final_datas);

    var area = new Morris.Bar({
        element: 'chart-2-huabu',
        resize: true,
        data: final_datas,
        xkey: 'title',
        ykeys: ['howmany'],
        labels: ['Wie viele [' + interval + ' Tage]'],
        lineColors: ['#3c8dbc'],
        hideHover: 'auto'
    });


    /**
     * 根据Fulfillment覆盖率，预测3天或7天销量
     */
    var num_list_for_calculate_len = num_list_for_calculate.length;
    var howManyLeft = Math.round(num_list_for_calculate_len * fulfillment);
    //console.log(num_list_for_calculate);
    num_list_for_fulfillment = num_list_for_calculate.splice(0, howManyLeft);
    //console.log(num_list_for_fulfillment);
    var totalFFM = 0;
    for(var iFFM = 0; iFFM < num_list_for_fulfillment.length; ++iFFM){
        totalFFM += num_list_for_fulfillment[iFFM];
    }
    var vadv_pro = Math.round(totalFFM / num_list_for_fulfillment.length * 10) / 10;

    $('#chart-2-product-name').html('&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-fw fa-search"></i>&nbsp;&nbsp;<b>Produkt:&nbsp;' + productName + '</b><br><span style="color: #FF0000;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Ergebnismenge:</b>&nbsp;' + num_list_for_calculate_len + '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Mindestverkaufsmenge pro [' + interval + ' Tage]:</b> ' + min_num + ' Stück<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Maximale Verkaufsmenge pro [' + interval + ' Tage]:</b> ' + max_num + ' Stück<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Bedarf pro [' + interval + ' Tage]:</b>&nbsp;' + vadv_pro +  '&nbsp;Stück&nbsp;(&nbsp;Fulfillment:&nbsp;' + Math.round(fulfillment * 100) + '%&nbsp;)</span>');
}

function showChart3() {
    alert('此功能还在开发中...');
}

function sortNumber(a,b)
{
    return a - b;
}

/**
 * 判断日期a，是否在日期b 以后
 * a 的格式 2017-10-31
 */
function isDateAfterDate(a, b) {
    var res = false;
    var dateTime = dateParse(a).getTime();
    var compareDateTime = dateParse(b).getTime();
    if(compareDateTime > dateTime){
        res = false;
    }else{
        res = true;
    }
    //console.log(res);
    return res;
}

/**
 * 日期格式 2017-10-31
 */
function dateParse(dateString){
    var SEPARATOR_BAR = "-";
    var SEPARATOR_SLASH = "/";
    var SEPARATOR_DOT = ".";
    var dateArray;
    if(dateString.indexOf(SEPARATOR_BAR) > -1){
        dateArray = dateString.split(SEPARATOR_BAR);
    }else if(dateString.indexOf(SEPARATOR_SLASH) > -1){
        dateArray = dateString.split(SEPARATOR_SLASH);
    }else{
        dateArray = dateString.split(SEPARATOR_DOT);
    }
    return new Date(dateArray[0], dateArray[1]-1, dateArray[2]);
}

/**
 * 修正ean格式
 */
function get4250ean(ean) {
    return ean.replace(/42512429/g, "42507553");
}
function get4251ean(ean) {
    return ean.replace(/42507553/g, "42512429");
}

function exportAllProductsFulfillmentRate() {

    if(!isexportAllProductsFulfillmentRateAjaxRunning){
        isexportAllProductsFulfillmentRateAjaxRunning = true;

        var zr = $('#lagerbestand-historie-zeitraum').val();
        $('#AllProductsFulfillmentRateCSVLink').hide();
        $('#dtlsPanel').hide();
        showLoadingLayer();

        // 隐藏导出按钮
        //$('#apfr_csv_export_btn').hide();

        //console.log('Export CSV AllProductsFulfillmentRate start...');
        $.ajax({
            url:'/api/export-csv-allproductsfulfillmentrate',
            data:{
                zr : zr
            },
            type:'post',
            cache:false,
            dataType:'json',
            success:function(data) {
                if(data.isSuccess){
                    var csv_link_obj = $('#AllProductsFulfillmentRateCSVLink');
                    csv_link_obj.html('<b>CSV Datei wurde erfolgreich erstellt:</b><br><a href="/wp-content/uploads/export/' + data.datas.csv_name + '"><i class="fa fa-download"></i>&nbsp;&nbsp;' + data.datas.csv_name + '</a>');
                    csv_link_obj.show();
                    var dtlsPanel_obj = $('#dtlsPanel');
                    $('#dtlsPanel-title').html('Daten vom letzten ' + zr + ' Tage [ Erstelldatum: ' + data.datas.erstelldatum_de + ' ]');
                    var dtls_wrap = $('#dtls-wrap');


                    /**
                     * 显示数据表格
                     */
                    var fulfillmentList = data.datas.fulfillmentList;
                    var htmlTxt = '<table id="t-online-fulfillment-infos" class="table table-bordered table-striped">' +
                        '                <thead>' +
                        '                <tr>' +
                        '                  <th class="t-a-c">EAN</th>' +
                        '                  <th class="t-a-c">Kategorie</th>' +
                        '                  <th class="t-a-l">Produktname</th>' +
                        '                  <th class="t-a-l">Fulfillment</th>'+
                    '</thead></tr>';

                    htmlTxt += '<body>';
                    for(var fKey in fulfillmentList){
                        htmlTxt = htmlTxt + '<tr>' +
                            '<td class="t-a-c">' + fKey + '</td>' +
                            '<td class="t-a-c">' + fulfillmentList[fKey]["category"] + '</td>' +
                            '<td class="t-a-l">' + fulfillmentList[fKey]["name"] + '</td>' +
                            '<td class="t-a-l">' + fulfillmentList[fKey]["fulfillment"] + '</td></tr>';
                    }
                    htmlTxt += '</body>';

                    htmlTxt += '</tbody></table>';
                    dtls_wrap.html(htmlTxt);



                    dtlsPanel_obj.show();
                    isexportAllProductsFulfillmentRateAjaxRunning = false;
                    removeLoadingLayer();


                    /**
                     * 配置表格
                     */
                    var pageDW_title_plural = 'Fulfillment Info';
                    var pageDW_title = 'Fulfillment Info';
                    $('#t-online-fulfillment-infos').DataTable({
                        "columns": [
                            { "orderable": false },
                            null,
                            null,
                            null],
                        'paging'      : true,
                        'lengthChange': true,
                        'searching'   : true,
                        'ordering'    : true,
                        'info'        : true,
                        'autoWidth'   : true,
                        "language": {
                            "lengthMenu": "Auf jeder Seite maximal&nbsp;&nbsp;_MENU_&nbsp;&nbsp;" + pageDW_title_plural + " anzeigen.",
                            "zeroRecords": "Keine " + pageDW_title + " gefunden.",
                            "info": "Seite _PAGE_ von _PAGES_",
                            "infoEmpty": "Keine " + pageDW_title + " gefunden.",
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



                }else{
                    removeLoadingLayer();
                    layer.open({
                        title: 'System Fehler: ID-10033',
                        icon: 7,
                        content: 'Fehler beim CSV Exportieren. Bitte kontaktieren Sie die IT-Abteilung bei Sogood GmbH.',
                        btn: ['Schließen']
                    });
                }
            },
            error : function() {
                // view("异常！");
                // alert("异常！");
            }
        });

    }

}

function drawLieferscheineListeTable() {
    var wrapobj = $('#delivery-notes-list-wrap');
    $.ajax({
        url:'/api/getdeliverynoteslist',
        data:{},
        type:'post',
        cache:false,
        dataType:'json',
        success:function(data) {
            if(data.isSuccess){
                // Mai & Mai
                var filenames_maimai = data.data.filenames_maimai;
                var htmlTxt = '<table><tr><th class="deliverynote-table-td-name">Mai & Mai Lieferscheine</th><th style="text-align: center;">Ausgedruckt?</th></tr>';
                for(var fm_i = 0; fm_i < filenames_maimai.length; ++fm_i){
                    var cbHtml = '<input id="' + filenames_maimai[fm_i]["name"] + '" type="checkbox" class="minimal" ';
                    if(filenames_maimai[fm_i]["already_printed"] == 'yes'){
                        cbHtml += 'checked';
                    }
                    cbHtml += ' />';
                    htmlTxt += '<tr><td class="deliverynote-table-td-name"><a href="/wp-content/downloads/liferscheine/' + filenames_maimai[fm_i]["name"] + '" target="_blank"><i class="fa fa-file-pdf-o"></i>&nbsp;&nbsp;' + filenames_maimai[fm_i]["show_name"] + '</a></td><td align="center">' + cbHtml + '</td></tr>';
                }
                htmlTxt += '</table>';
                // Sogood
                var filenames_sogood = data.data.filenames_sogood;
                htmlTxt += '<table><tr><th class="deliverynote-table-td-name">Sogood Lieferscheine</th><th style="text-align: center;">Ausgedruckt?</th></tr>';
                for(var fs_i = 0; fs_i < filenames_sogood.length; ++fs_i){
                    var cbHtml = '<input id="' + filenames_sogood[fs_i]["name"] + '" type="checkbox" class="minimal" ';
                    if(filenames_sogood[fs_i]["already_printed"] == 'yes'){
                        cbHtml += 'checked';
                    }
                    cbHtml += ' />';
                    htmlTxt += '<tr><td class="deliverynote-table-td-name"><a href="/wp-content/downloads/liferscheine/' + filenames_sogood[fs_i]["name"] + '" target="_blank"><i class="fa fa-file-pdf-o"></i>&nbsp;&nbsp;' + filenames_sogood[fs_i]["show_name"] + '</a></td><td align="center">' + cbHtml + '</td></tr>';
                }
                wrapobj.html(htmlTxt);

                //iCheck for checkbox and radio inputs
                $('input[type="checkbox"].minimal').on('ifClicked', function (event) {
                    if(event.type === "ifClicked"){
                        //$(this).trigger('click');
                        //$('input').iCheck('update');
                        //console.log($(this));
                        //console.log($(this)[0].id);
                        //console.log($(this)[0].checked);
                        setPrintDeliveryNoteLog($(this)[0].id, $(this)[0].checked);
                    }
                }).iCheck({
                    checkboxClass: 'icheckbox_minimal-blue',
                    increaseArea: '20%' // optional
                });

            }else{
                wrapobj.html(data.msg);
            }
        },
        error : function() {
            // view("异常！");
            // alert("异常！");
        }
    });
}


/**
 * [编辑订单中各种的Memo] Cache
 */
var cache_Memo = '';
var cache_Rechnungsvermerk = '';
var cache_Buchhaltungsmemo = '';
function getMemoNameById(id) {
    var memoName = '';
    switch(id){
        case 0:
            memoName = 'Memo';
            break;
        case 1:
            memoName = 'Rechnungsvermerk';
            break;
        case 2:
            memoName = 'Buchhaltungsmemo';
            break;
        default:
            memoName = '';
    }
    return memoName;
}
/**
 * [编辑订单中各种的Memo] 激活编辑框
 * @param memoId [ 例如：0:Memo 1:Rechnungsvermerk 2:Buchhaltungsmemo ]
 */
function activeMemoEditMode(memoId) {
    var memoName = getMemoNameById(memoId);
    var wrap = $('#p-' + memoName);
    var originalTxt = $.trim(wrap.html());
    switch(memoName){
        case 'Rechnungsvermerk':
            cache_Rechnungsvermerk = originalTxt;
            break;
        case 'Buchhaltungsmemo':
            cache_Buchhaltungsmemo = originalTxt;
            break;
        case 'Memo':
            cache_Memo = originalTxt;
            break;
        default:
            //
    }
    wrap.html('<textarea id="new' + memoName + 'TA" type="text" style="resize: vertical;" >' + originalTxt.replace(/<br>/g,"\r\n") + '</textarea>');
    $('#p-' + memoName + '-btns').html(memoName + ': <span class="btn btn-default" style="font-size: 12px;" onclick="saveNewMemoAjax('+memoId+',800);">Speichern</span> <span class="btn btn-default" style="font-size: 12px;" onclick="deactiveMemoEditMode('+memoId+')";>Abbrechen</span>');
}
/**
 * [编辑订单中各种的Memo] 取消编辑框
 * @param memoId [ 例如：0:Memo 1:Rechnungsvermerk 2:Buchhaltungsmemo ]
 */
function deactiveMemoEditMode(memoId) {
    var memoName = getMemoNameById(memoId);
    var originalTxt = '';
    switch(memoName){
        case 'Rechnungsvermerk':
            originalTxt = cache_Rechnungsvermerk;
            break;
        case 'Buchhaltungsmemo':
            originalTxt = cache_Buchhaltungsmemo;
            break;
        case 'Memo':
            originalTxt = cache_Memo;
            break;
        default:
        //
    }
    $('#p-' + memoName).html(originalTxt);
    $('#p-' + memoName + '-btns').html(memoName + ': <span class="btn btn-default" style="font-size: 12px;" onclick="activeMemoEditMode('+memoId+');">Bearbeiten</span>');
}
/**
 * [编辑订单中各种的Memo] 发送Ajax请求保存新的Memo
 * @param memoId [ 例如：0:Memo 1:Rechnungsvermerk 2:Buchhaltungsmemo ]
 * @param maxMemoLen [ 例如：800 ]
 */
function saveNewMemoAjax(memoId, maxMemoLen) {
    var memoName = getMemoNameById(memoId);
    var newMemo =$('#new' + memoName + 'TA').val();
    newMemo = newMemo.replace(/\n|\r\n/g,"<br>");

    if(newMemo.length < maxMemoLen){
        showLoadingLayer();
        var order_id = $('#order_id').html();
        var newData_Json = {};
        newData_Json.meta_id = order_id;
        switch(memoId){
            case 0:
                memoName = 'Memo';
                newData_Json.memo = newMemo;
                break;
            case 1:
                memoName = 'Rechnungsvermerk';
                newData_Json.memo_big_account = newMemo;
                break;
            case 2:
                memoName = 'Buchhaltungsmemo';
                newData_Json.memo_kuaiji = newMemo;
                break;
            default:
                //
        }
        $.ajax({
            url:'/api/updateorder',
            data: { "order_new_data": encodeURI(JSON.stringify(newData_Json)) },
            dataType: "json",
            type: "POST",
            traditional: true,
            success: function (data) {
                $('#p-' + memoName + '-btns').html(memoName + ': <span class="btn btn-default" style="font-size: 12px;" onclick="activeMemoEditMode('+memoId+');">Bearbeiten</span>');
                removeLoadingLayer();
                if(data.isSuccess){
                    $('#p-' + memoName).html(newMemo);
                    layer.confirm('Das' + memoName + ' wurde erfolgreich aktualisiert.', {
                        icon: 1,
                        title: 'Bestellung Tipp: ID-10017',
                        btn: ['Alles klar'] //按钮
                    }, function(index){
                        layer.close(index);
                    });
                    setOperationHistory(order_id, 'Das ' + memoName + ' wurde erfolgreich aktualisiert.', 0, 0);
                }else{
                    customAlert("Bestellung Fehler: ID-10034", 2, "Das " + memoName + " kann nicht aktualisiert werden, bitte wenden Sie an IT Abteilung.");
                }
            }
        });
    }else{
        layer.confirm('Maximale Länge von ' + memoName+ ': ' + maxMemoLen + ' Zeichen. ', {
            icon: 2,
            title: 'Bestellung Tipp: ID-10035',
            btn: ['Alles klar'] //按钮
        }, function(index){
            layer.close(index);
        });
    }
}



/**
 * Lager打印Lieferschein的日志
 * @param fileName
 * @param isNotChecked
 */
function setPrintDeliveryNoteLog(fileName, isNotChecked) {
    showLoadingLayer();
    var already_printed = 0;
    if(isNotChecked){
        already_printed = 0;
    }else{
        already_printed = 1;
    }
    $.ajax({
        url:'/api/setPrintFileLog',
        data: {
            file_name : fileName,
            file_type : 'pdf',
            file_category : 'Lieferschein',
            already_printed : already_printed
        },
        dataType: "json",
        type: "POST",
        traditional: true,
        success: function (data) {
            removeLoadingLayer();
            if(data.isSuccess){
                customAlert("Datei Ausdrucken Tipp: ID-10036", 1, "Status der Datei wurde erfolgreich gespeichert.");
            }else{
                customAlert("Bestellung Fehler: ID-10034", 2, "Status der Datei kann nicht aktualisiert werden, bitte wenden Sie an IT Abteilung.");
            }
        }
    });
}

/**
 * 保存操作记录
 * @param id (订单 报价 id)
 * @param msg ( "" )
 * @param typ ( order quote ... )
 * @param uid ( 如果是0，就写当前登陆用户 )
 */
function setOperationHistory(id, msg, typ, uid) {
    $.ajax({
        url:'/api/setoperationhistory',
        data: {
            order_id : id,
            message : msg,
            doc_type : typ,
            user_id : uid
        },
        dataType: "json",
        type: "POST",
        traditional: true,
        success: function (data) {
            //
        }
    });
}

/**
 * 在产品订单页面，把库存set进去
 */
function setVerfuegbarByEAN(ean, divId) {
    let eanMaiMai = get4251ean(ean);
    setTimeout(function () {
        var obj = $('#' + divId);
        var verfuegbar = groupedProducts['un-grouped-products'][eanMaiMai]['quantity'];
        obj.html(verfuegbar);
    }, 500);
}

function showUmsatz(IsMustSelectStaff, staffId) {
    var u_user_ids = $('#umsatzstatistik-user-ids').val();
    if(typeof(u_user_ids) == 'undefined'){
        u_user_ids = staffId;
    }
    var zeitraum = $('#start-end-date').val();
    if(u_user_ids == '0'){
        if(IsMustSelectStaff){
            customAlert("Betriebsfehler: ID-10038", 2, "Bitte einen Mitarbeiter wählen.");
        }
    }else{
        showLoadingLayer();
        $.ajax({
            url:'/api/getumsatzstatistik',
            data: {
                u_user_ids : u_user_ids,
                zeitraum : zeitraum
            },
            dataType: "json",
            type: "POST",
            traditional: true,
            success: function (data) {
                if(data.isSuccess){
                    drowUmsatzChart(data.data);
                }else{
                    removeLoadingLayer();
                    customAlert("API Fehler: ID-10039", 2, "Die Daten können derzeit leider nicht geladen werden, bitte versuchen Sie später noch einmal.");
                }
            }
        });
    }
}

function drowUmsatzChart(d) {

    /**
     * 显示柱图
     * @type {jQuery|HTMLElement}
     */
    var resultChartWrap = $('#result-chart');
    var chartHtmlTxt = '<div class="box box-primary margin-t-20" style="background-color: #f7f7f7;">\n' +
        '            <div class="box-header with-border">\n' +
        '              <h3 class="box-title">Umsatz pro Tag</h3>\n' +
        '            </div>\n' +
        '            <div class="box-body">\n' +
        '              <div class="chart">\n' +
        '                <div id="barChart-Umsatz" style="height:230px"></div>\n' +
        '              </div>\n' +
        '            </div>\n' +
        '            <!-- /.box-body -->\n' +
        '          </div>';
    resultChartWrap.html(chartHtmlTxt);

    // 开始操作 Morris Bar
    var barChartDatas = [];
    var umsatzList = d.UmsatzList;
    for(var i = 0; i < umsatzList.length; ++i){
        var usz_paid = umsatzList[i].umsatz_paid_float;
        var usz_unpaid = umsatzList[i].umsatz_unpaid_float;
        var barChartData = {
            x   :   umsatzList[i].date_de,
            y   :   usz_paid,
            z   :   usz_unpaid
        }
        barChartDatas.push(barChartData);
    }

    Morris.Bar({
        element: 'barChart-Umsatz',
        data: barChartDatas,
        xkey: 'x',
        ykeys: ['y', 'z'],
        labels: ['Umsatz', 'Unbezahlt'],
        barColors: ['#6a98d6', '#dae6f5'],
        hoverFillColor: '#94c3fc',
        stacked: true,
        resize: true,
        barWidth: 20,
        yLabelFormat: function (y) {
            var parts = y.toString().split(".");
            parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            var price_de = parts.join(",");
            if(price_de.indexOf(",") != -1){
                var price_de_lst = price_de.split(",");
                if(price_de_lst[1].length == 1){
                    price_de += '0';
                }
            }else{
                price_de += ',00';
            }
            return price_de + ' €';
        },
        xLabelFormat: function (x) {
            var date_de = x.src.x;
            var date_de_lst = date_de.split(".");
            var month_abk = '';
            switch(parseInt(date_de_lst[1])){
                case 1:
                    month_abk = 'Jan';
                    break;
                case 2:
                    month_abk = 'Feb';
                    break;
                case 3:
                    month_abk = 'Mär';
                    break;
                case 4:
                    month_abk = 'Apr';
                    break;
                case 5:
                    month_abk = 'Mai';
                    break;
                case 6:
                    month_abk = 'Jun';
                    break;
                case 7:
                    month_abk = 'Jul';
                    break;
                case 8:
                    month_abk = 'Aug';
                    break;
                case 9:
                    month_abk = 'Sep';
                    break;
                case 10:
                    month_abk = 'Okt';
                    break;
                case 11:
                    month_abk = 'Nov';
                    break;
                case 12:
                    month_abk = 'Dez';
                    break;
                default:
                    month_abk = '';
            }
            return date_de_lst[0] + '. ' + month_abk;
        },
        xLabelAngle: 45,
        grid: true,
        gridTextSize: 9
        //goals: ['2000'],
        //goalLineColors: ['#FF0000'],
        //goalStrokeWidth: 1
    });


    /**
     * 显示各种总和
     */
    var resultTotalWrap = $('#umsatz-result-total-table-wrap');
    ut = d.UmsatzTotal;
    var totalTableHtmlTxt = '<table><tr><th class="th-l">Zeitraum</th><th class="th-m">Genauer Zeitraum</th><th class="th-r">Umsatz</th><th class="th-r th-r-unpaid">Unbezahlt</th></tr>';
    totalTableHtmlTxt += '<tr><td class="td-l">ausgewählter Zeitraum</td><td class="td-m">[ ' + ut["UmsatzSelected"]["start_time"] + ' ] bis [ ' + ut["UmsatzSelected"]["end_time"] + ' ]</td><td class="td-r">' + ut["UmsatzSelected"]["paid_format_DE"] + '&nbsp;&euro;</td><td class="td-r clr-c0c0c0 td-r-unpaid">' + ut["UmsatzSelected"]["unpaid_format_DE"] + '&nbsp;&euro;</td></tr>';
    totalTableHtmlTxt += '<tr><td class="td-l">Heute</td><td class="td-m">[ ' + ut["UmsatzToday"]["start_time"] + ' ] bis [ ' + ut["UmsatzToday"]["end_time"] + ' ]</td><td class="td-r">' + ut["UmsatzToday"]["paid_format_DE"] + '&nbsp;&euro;</td><td class="td-r clr-c0c0c0 td-r-unpaid">' + ut["UmsatzToday"]["unpaid_format_DE"] + '&nbsp;&euro;</td></tr>';
    totalTableHtmlTxt += '<tr><td class="td-l">Gestern</td><td class="td-m">[ ' + ut["UmsatzYesterday"]["start_time"] + ' ] bis [ ' + ut["UmsatzYesterday"]["end_time"] + ' ]</td><td class="td-r">' + ut["UmsatzYesterday"]["paid_format_DE"] + '&nbsp;&euro;</td><td class="td-r clr-c0c0c0 td-r-unpaid">' + ut["UmsatzYesterday"]["unpaid_format_DE"] + '&nbsp;&euro;</td></tr>';
    totalTableHtmlTxt += '<tr><td class="td-l">Letzte 7 Tage</td><td class="td-m">[ ' + ut["Umsatz7Days_noToday"]["start_time"] + ' ] bis [ ' + ut["Umsatz7Days_noToday"]["end_time"] + ' ]</td><td class="td-r">' + ut["Umsatz7Days_noToday"]["paid_format_DE"] + '&nbsp;&euro;</td><td class="td-r clr-c0c0c0 td-r-unpaid">' + ut["Umsatz7Days_noToday"]["unpaid_format_DE"] + '&nbsp;&euro;</td></tr>';
    totalTableHtmlTxt += '<tr><td class="td-l">Letzte 31 Tage</td><td class="td-m">[ ' + ut["Umsatz31Days_noToday"]["start_time"] + ' ] bis [ ' + ut["Umsatz31Days_noToday"]["end_time"] + ' ]</td><td class="td-r">' + ut["Umsatz31Days_noToday"]["paid_format_DE"] + '&nbsp;&euro;</td><td class="td-r clr-c0c0c0 td-r-unpaid">' + ut["Umsatz31Days_noToday"]["unpaid_format_DE"] + '&nbsp;&euro;</td></tr>';
    totalTableHtmlTxt += '<tr><td class="td-l">Letzte 90 Tage</td><td class="td-m">[ ' + ut["Umsatz90Days_noToday"]["start_time"] + ' ] bis [ ' + ut["Umsatz90Days_noToday"]["end_time"] + ' ]</td><td class="td-r">' + ut["Umsatz90Days_noToday"]["paid_format_DE"] + '&nbsp;&euro;</td><td class="td-r clr-c0c0c0 td-r-unpaid">' + ut["Umsatz90Days_noToday"]["unpaid_format_DE"] + '&nbsp;&euro;</td></tr>';
    totalTableHtmlTxt += '<tr><td class="td-l">In dieser Woche</td><td class="td-m">[ ' + ut["UmsatzThisWeek"]["start_time"] + ' ] bis [ ' + ut["UmsatzThisWeek"]["end_time"] + ' ]</td><td class="td-r">' + ut["UmsatzThisWeek"]["paid_format_DE"] + '&nbsp;&euro;</td><td class="td-r clr-c0c0c0 td-r-unpaid">' + ut["UmsatzThisWeek"]["unpaid_format_DE"] + '&nbsp;&euro;</td></tr>';
    totalTableHtmlTxt += '<tr><td class="td-l">In diesem Monat</td><td class="td-m">[ ' + ut["UmsatzThisMonth"]["start_time"] + ' ] bis [ ' + ut["UmsatzThisMonth"]["end_time"] + ' ]</td><td class="td-r">' + ut["UmsatzThisMonth"]["paid_format_DE"] + '&nbsp;&euro;</td><td class="td-r clr-c0c0c0 td-r-unpaid">' + ut["UmsatzThisMonth"]["unpaid_format_DE"] + '&nbsp;&euro;</td></tr>';
    totalTableHtmlTxt += '</table>';
    resultTotalWrap.html(totalTableHtmlTxt);

    removeLoadingLayer();
}

/**
 * 在操作记录里面查询，这张Lieferschein打印了几次
 * 如果 大于等于 1次， 那么就不要再去写Afterbuy的 Rechnungsdatum
 */
function getDeliveryNotePrintTimes(token, oid) {
    $.ajax({
        url:'/api/getdeliverynoteprinttimes',
        data: {
            oid : oid
        },
        dataType: "json",
        type: "POST",
        traditional: true,
        success: function (data) {
            if(data.isSuccess){
                var deliveryNotePrintTimes = parseInt(data.data.DeliveryNotePrintTimes);
                if(deliveryNotePrintTimes > 0){
                    window.location.href = "/document/"+token+"/";
                }else{
                    // 修改Afterbuy的Rechnungsdatum
                    updateInvoiceDateInAfterbuy(oid, token);
                }
            }else{
                //
            }
        }
    });
}

/**
 * 使用E2Api 修改Afterbuy的Rechnungsdatum
 */
function updateInvoiceDateInAfterbuy(oid, token){
    var ab_oid = $('#order-id-ab').html();
    $.ajax({
        url:'/api/setinvoicedatetoe2',
        data: {
            aboid : ab_oid,
            gooid : oid
        },
        dataType: "json",
        type: "POST",
        traditional: true,
        success: function (data) {
            if(data.isSuccess){
                window.location.href = "/document/"+token+"/";
            }else{
                //
            }
        }
    });
}

/**
 * 打印Lieferschein之前的确认询问
 */
function printDeliveryNoteConfirm(token, oid) {
    layer.confirm('Wenn der Lieferschein jetzt hier manuell ausgedruckt wird, wird es nicht mehr im Lager automatisch ausgedruckt.<br>Werden Sie trotzdem jetzt den Lieferschein ausdrucken?', {
        icon: 3,
        title: 'Bestellung Tipp: ID-10040',
        btn: ['Ja, drucken','Nein, abbrechen'] //按钮
    }, function(index){
        showLoadingLayer();
        getDeliveryNotePrintTimes(token, oid);
    }, function(index){
        //
    });
}

/**
 * 在订单页面里，读取 Afterbuy 的 Memo 和 Rechnungsvermerk
 */
function loadMemoAndRVFromAB(order_id_ab) {
    console.log('loadMemoAndRVFromAB: ' + order_id_ab);
}


/**
 * Mapping 创建 & 编辑 页面
 * http://www.ying.com/datenmapping-bearbeiten/
 * https://goodies.pixabay.com/jquery/tag-editor/demo.html
 */
function disableTextArea(id) {
    showLoadingLayer();

    let idNr = id.substring(12, 13);
    removeAllEANs(id);
    removeAllDetails(idNr);

    //searchMappingsByEAN(idNr);
    removeLoadingLayer();
}

function removeAllDetails(idNr) {
    $('#mappings-00'+idNr+'-elm-Details').html('N/A');
    $('#mappings-00'+idNr+'-est-Details').html('N/A');
}

function updateDetails(nr, ean){
    var elmDetails = '';
    var estDetails = '';
    var tagsList = $('#tags-wrap-00'+nr).val();
    switch (parseInt(nr)){
        case 1:
            elmDetails = '<i class="fa fa-angle-double-right padding-l-10"></i>&nbsp;&nbsp;' + ean;
            if(ean.length > 10){
                elmDetails = '<i class="fa fa-angle-double-right padding-l-10"></i>&nbsp;&nbsp;' + ean + '&nbsp;-&nbsp;' + getProductTitleByEAN(ean);
            }
            tagsListArr = tagsList.split(',');
            tagsListArrTxt = '';
            if(tagsListArr.length > 0){
                for(var i = 0; i < tagsListArr.length; ++i){
                    if(tagsListArr[i].length > 12){
                        tagsListArrTxt += '<i class="fa fa-angle-double-right padding-l-10"></i>&nbsp;&nbsp;' + tagsListArr[i] + '&nbsp;-&nbsp;' + getProductTitleByEAN(tagsListArr[i]);
                        tagsListArrTxt += '<br>';
                    }
                }
            }
            if(tagsListArrTxt == ''){
                tagsListArrTxt = 'N/A';
            }
            estDetails = tagsListArrTxt;
            break;
        case 2:
            tagsListArr = tagsList.split(',');
            tagsListArrTxt = '';
            if(tagsListArr.length > 0){
                for(var i = 0; i < tagsListArr.length; ++i){
                    if(tagsListArr[i].length > 12) {
                        tagsListArrTxt += '<i class="fa fa-angle-double-right padding-l-10"></i>&nbsp;&nbsp;' + tagsListArr[i] + '&nbsp;-&nbsp;' + getProductTitleByEAN(tagsListArr[i]);
                        tagsListArrTxt += '<br>';
                    }
                }
            }
            if(tagsListArrTxt == ''){
                tagsListArrTxt = 'N/A';
            }
            elmDetails = tagsListArrTxt;
            estDetails = '<i class="fa fa-angle-double-right padding-l-10"></i>&nbsp;&nbsp;' + ean + '&nbsp;-&nbsp;' + getProductTitleByEAN(ean);
            break;
        default:
            //
    }
    $('#mappings-00'+nr+'-elm-Details').html(elmDetails);
    $('#mappings-00'+nr+'-est-Details').html(estDetails);
}

function getProductTitleByEAN(ean){
    if(ean !== null && ean !== '' && ean !== undefined){
        var eanSG = ean;
        if(eanSG.substring(0, 8) == '42507553'){
            eanSG = eanSG.replace(/42507553/, "42512429");
        }
        var p = groupedProducts['un-grouped-products'][eanSG];
        //console.log(p);
        if(p == undefined){
            return '[ Title nicht gefunden ]';
        }else{
            return p["title"];
        }
    }else{
        return 'EAN IS NULL';
    }
}

function removeAllEANs(id) {
    var tags = $('#' + id).tagEditor('getTags')[0].tags;
    for (var i = 0; i < tags.length; i++) { $('#' + id).tagEditor('removeTag', tags[i]); }
}

function searchMappingsByEAN(nr) {

    showLoadingLayer();

    let ean = '';
    switch (parseInt(nr)){
        case 1:
            ean = $('#mapping-elm-ean').val();
            break;
        case 2:
            ean = $('#mapping-est-ean').val();
            break;
        default:
            //
    }

    //console.log(ean);

    if(isRightEAN(ean)){
        loadMappingEANs(nr, ean, 'elm-est');
    }else{
        customAlert("Mapping Fehler: ID-10043", 2, 'Bitte die richtige EAN (42507553XXXXX) eingeben.');
        removeLoadingLayer();
    }

}

function loadMappingEANs(nr, ean, mappingtyp) {

    let apiname = '';
    let dataparam = {};
    switch (mappingtyp){
        case 'pro-elm':
            apiname = 'getmappingproductandelementlist';
            dataparam.type = nr;
            dataparam.ean = ean;
            break;
        case 'elm-est':
            apiname = 'getmappingelementandersatzteillist';
            dataparam.type = nr;
            dataparam.ean = ean;
            break;
        case 'shelf-elm':
            apiname = 'getmappingshelfandelementlist';
            dataparam.type = nr;
            dataparam.shelfid = ean;
        default:
            //
    }

    if(apiname !== ''){

        $.ajax({
            url:'/api/' + apiname,
            data: dataparam,
            dataType: "json",
            type: "POST",
            traditional: true,
            success: function (data) {
                if(data.isSuccess){

                    var eansResult = data.data;

                    switch(parseInt(nr)){
                        case 1:
                            if(mappingtyp == 'elm-est'){
                                for(var i = 0; i < eansResult.length; ++i){
                                    $('#tags-wrap-00'+nr).tagEditor('addTag', eansResult[i]["ean_est"]);
                                }
                                updateDetails(nr, ean);
                            }else if(mappingtyp == 'shelf-elm'){
                                for(var i = 0; i < eansResult.length; ++i){
                                    $('#tags-wrap-00'+nr).tagEditor('addTag', eansResult[i]["ean_elm"]);
                                }
                                updateDetails(nr, ean);
                            }
                            break;
                        case 2:
                            if(mappingtyp == 'elm-est') {
                                for (var i = 0; i < eansResult.length; ++i) {
                                    $('#tags-wrap-00' + nr).tagEditor('addTag', eansResult[i]["ean_elm"]);
                                }
                                updateDetails(nr, ean);
                            }
                            break;
                        case 3:
                            createKompletteMappingListe(eansResult, mappingtyp);
                            break;
                        default:
                        //
                    }

                    $('#btn-save-mappings-00' + nr).attr("disabled","disabled");
                    if(parseInt(nr) != 3){
                        customAlert("Mapping Info: ID-10042", 1, data.msg);
                    }

                }else{
                    customAlert("Mapping Fehler: ID-10041", 2, data.msg);
                }

                removeLoadingLayer();
            }
        });

    }


}

function saveMappings(nr) {

    showLoadingLayer();

    /**
     * 参数准备
     */
    var type = nr;
    var elm = '';
    var est = '';
    var eanList = $('#tags-wrap-00' + nr).tagEditor('getTags')[0].tags;
    switch (parseInt(nr)){
        case 1:
            elm = $('#mapping-elm-ean').val();
            for (var i = 0; i < eanList.length; i++) {
                est += eanList[i];
                if(i < eanList.length - 1){
                    est += ',';
                }
            }
            break;
        case 2:
            for (var i = 0; i < eanList.length; i++) {
                elm += eanList[i];
                if(i < eanList.length - 1){
                    elm += ',';
                }
            }
            est = $('#mapping-est-ean').val();
            break;
        default:
        //
    }

    /**
     * 参数验证
     */
    switch (parseInt(nr)){
        case 1:
            if(!isRightEAN(elm)){
                customAlert("Mapping Fehler: ID-10043", 2, 'Bitte die richtige EAN (42507553XXXXX) eingeben.');
                removeLoadingLayer();
                return false;
            }
            break;
        case 2:
            if(!isRightEAN(est)){
                customAlert("Mapping Fehler: ID-10043", 2, 'Bitte die richtige EAN (42507553XXXXX) eingeben.');
                removeLoadingLayer();
                return false;
            }
            break;
        default:
        //
    }
    for (var j = 0; j < eanList.length; j++) {
        if(!isRightEAN(eanList[j])){
            customAlert("Mapping Fehler: ID-10043", 2, 'Bitte die richtige EAN (42507553XXXXX) eingeben.');
            removeLoadingLayer();
            return false;
        }
    }

    /**
     * 呼叫Ajax
     */
    //console.log('呼叫AJAX');
    //console.log(type);
    //console.log(elm);
    //console.log(est);
    if(eanList.length > 0){
        $.ajax({
            url:'/api/updatemappingelementandersatzteillist',
            data: {
                type : type,
                elm : elm,
                est : est
            },
            dataType: "json",
            type: "POST",
            traditional: true,
            success: function (data) {
                if(data.isSuccess){
                    customAlert("Mapping Info: ID-10044", 1, data.msg);
                }else{
                    customAlert("Mapping Fehler: ID-10041", 2, data.msg);
                }

                removeLoadingLayer();
            }
        });
    }else{
        customAlert("Mapping Fehler: ID-10045", 2, 'EAN Felder darf nicht leer sein.');
        removeLoadingLayer();
    }


}

function isRightEAN(ean) {
    // 4250755330211
    if(ean.length == 13 && ean.substring(0, 8) == '42507553'){
        return true;
    }else{
        return false;
    }
}

function isRightRegalId(id) {
    // 4250755330211
    if(id.length > 3){
        return true;
    }else{
        return false;
    }
}

function getBtnColumnNameByMappingtyp(mappingtyp, pos) {

    let btnColumnName_L = '';
    let btnColumnName_R = '';
    switch (mappingtyp){
        case 'pro-elm':
            btnColumnName_L = 'ean_pro';
            btnColumnName_R = 'ean_elm';
            break;
        case 'elm-est':
            btnColumnName_L = 'ean_elm';
            btnColumnName_R = 'ean_est';
            break;
        case 'shelf-elm':
            btnColumnName_L = 'shelf_id';
            btnColumnName_R = 'ean_elm';
        default:
        //
    }

    switch (pos){
        case 'L':
            return btnColumnName_L;
            break;
        case 'R':
            return btnColumnName_R;
            break;
        default:
            return '';
    }

}

function createKompletteMappingListe(list, mappingtyp) {

    /**
     * 整理数据
     * 筛选出有哪些 左侧的   【proList】或【elmList】或【shelf_id】
     * 筛选出有哪些 右侧的   【estList】或【elmList】
     */

    let btnColumnName_L = getBtnColumnNameByMappingtyp(mappingtyp, 'L');
    let btnColumnName_R = getBtnColumnNameByMappingtyp(mappingtyp, 'R');

    //console.log(btnColumnName_L);
    //console.log(btnColumnName_R);
    //console.log(list);

    let List_L = [];
    let List_R = [];
    for(let i = 0; i < list.length; ++i){
        //elmList
        let new_L = list[i][btnColumnName_L];
        if(!isValueInList(new_L, List_L)){
            List_L.push(new_L);
        }
        //estList
        let new_R = list[i][btnColumnName_R];
        if(!isValueInList(new_R, List_R)){
            List_R.push(new_R);
        }
    }
    List_L.sort();
    List_R.sort();

    //console.log(List_L);
    //console.log(List_R);

    drawKompletteMappingListeCanvas(list, List_L, List_R, -1, -1, mappingtyp);

}

function drawKompletteMappingListeCanvas(list, List_L, List_R, mouseX, mouseY, mappingtyp) {

    let btnColumnName_L = getBtnColumnNameByMappingtyp(mappingtyp, 'L');
    let btnColumnName_R = getBtnColumnNameByMappingtyp(mappingtyp, 'R');

    /**
     * Global
     */
    let clicked_btn = {};

    /**
     * Draw Canvas
     */
        // 定义画布的尺寸
    let c_width = parseFloat($('#all-mappings-list-wrap')[0].clientWidth) - 21;
    let items_vertical_count = (List_L.length > List_R.length) ? (List_L.length) : (List_R.length);
    let c_height = items_vertical_count * 30 * 1.3 + 25;

    let padding = 10;

    /**
     * 使用双缓冲绘图机制
     * wrap_canvas
     * amlw_canvas 是  cache canvas
     *
     */

    // 前台页面的DIV 用来做canvas的容器
    let canvas_wrap = document.getElementById('all-mappings-list-wrap-canvas');
    canvas_wrap.innerHTML = '';
    canvas_wrap.width = c_width;
    canvas_wrap.height = c_height;

    // 创建缓存画布
    let amlw_canvas = document.createElement("canvas");
    amlw_canvas.width = c_width;
    amlw_canvas.height = c_height;
    // Context
    let amlw_ctx = amlw_canvas.getContext('2d');

    // 创建真实画布
    let real_canvas = document.createElement("canvas");
    real_canvas.width = c_width;
    real_canvas.height = c_height;
    // Context
    let real_ctx = real_canvas.getContext('2d');


    // 把真的canvase加入div
    canvas_wrap.appendChild(real_canvas);


    // 搜集可以连线的所有点
    let btn_conn_points = {};
    btn_conn_points['left'] = {};
    btn_conn_points['right'] = {};


    // 画出左侧的 Element 竖列 List_L
    let left_column_x = padding;
    for(let idx_l = 0; idx_l < List_L.length; ++idx_l){

        let eANBtnInMappingCanvas = new EANBtnInMappingCanvas(
            amlw_ctx,
            left_column_x,
            padding + idx_l * 30 * 1.3,
            List_L[idx_l],
            mouseX,
            mouseY,
            'left',
            false
        );
        eANBtnInMappingCanvas.draw();
        btn_conn_points['left'][List_L[idx_l]] = {};
        btn_conn_points['left'][List_L[idx_l]]['x'] = eANBtnInMappingCanvas.conn_point_right_x;
        btn_conn_points['left'][List_L[idx_l]]['y'] = eANBtnInMappingCanvas.conn_point_right_y;
        btn_conn_points['left'][List_L[idx_l]]['obj'] = eANBtnInMappingCanvas;

        // save: which btn was clicked
        if(eANBtnInMappingCanvas.isClicked()){
            clicked_btn.ean = List_L[idx_l];
            clicked_btn.column = 'left';
        }

    }

    // 画出右侧的 Ersatzteil 竖列 List_R
    let right_column_x = c_width - 140 - padding;
    for(let idx_r = 0; idx_r < List_R.length; ++idx_r){

        let eANBtnInMappingCanvas = new EANBtnInMappingCanvas(
            amlw_ctx,
            right_column_x,
            padding + idx_r * 30 * 1.3,
            List_R[idx_r],
            mouseX,
            mouseY,
            'right',
            false
        );
        eANBtnInMappingCanvas.draw();
        btn_conn_points['right'][List_R[idx_r]] = {};
        btn_conn_points['right'][List_R[idx_r]]['x'] = eANBtnInMappingCanvas.conn_point_left_x;
        btn_conn_points['right'][List_R[idx_r]]['y'] = eANBtnInMappingCanvas.conn_point_left_y;
        btn_conn_points['right'][List_R[idx_r]]['obj'] = eANBtnInMappingCanvas;

        // save: which btn was clicked
        if(eANBtnInMappingCanvas.isClicked()){
            clicked_btn.ean = List_R[idx_r];
            clicked_btn.column = 'right';
        }

    }


    // save all btn objs, which should show name
    let btn_obj_showname_list = [];
    // draw lines
    for(let i = 0; i < list.length; ++i){
        let btn_ean_l = list[i][btnColumnName_L];
        let btn_ean_r = list[i][btnColumnName_R];

        let isThisLineActive = false;
        if(clicked_btn.ean != undefined && clicked_btn.column != undefined){

            switch (clicked_btn.column){
                case 'left':
                    if(btn_ean_l == clicked_btn.ean){
                        isThisLineActive = true;
                        // 将 line 末端的 button 设为 active
                        let target_btn_obj = btn_conn_points['right'][btn_ean_r]['obj'];
                        target_btn_obj.setActive();
                    }
                    break;
                case 'right':
                    if(btn_ean_r == clicked_btn.ean){
                        isThisLineActive = true;
                        // 将 line 末端的 button 设为 active
                        let target_btn_obj = btn_conn_points['left'][btn_ean_l]['obj'];
                        target_btn_obj.setActive();
                    }
                    break;
                default:
                //
            }

        }

        let lineBetweenBtnsInMappingCanvas = new LineBetweenBtnsInMappingCanvas(
            amlw_ctx,
            btn_conn_points["left"][btn_ean_l]["x"],
            btn_conn_points["left"][btn_ean_l]["y"],
            btn_conn_points["right"][btn_ean_r]["x"],
            btn_conn_points["right"][btn_ean_r]["y"],
            mouseX, mouseY, isThisLineActive
        );
        lineBetweenBtnsInMappingCanvas.draw();

        /**
         * 显示名称
         */
        if(isThisLineActive){
            switch (clicked_btn.column){
                case 'left':
                    //btn_conn_points['right'][btn_ean_r]['obj'].drawName();
                    btn_obj_showname_list.push(btn_conn_points['right'][btn_ean_r]['obj']);
                    break;
                case 'right':
                    //btn_conn_points['left'][btn_ean_l]['obj'].drawName();
                    btn_obj_showname_list.push(btn_conn_points['left'][btn_ean_l]['obj']);
                    break;
                default:
                //
            }
        }

    }
    if(clicked_btn.ean != undefined && clicked_btn.column != undefined){
        //btn_conn_points[clicked_btn.column][clicked_btn.ean]['obj'].drawName();
        btn_obj_showname_list.push(btn_conn_points[clicked_btn.column][clicked_btn.ean]['obj']);
    }

    /**
     * 显示所有的 btn name
     * @param e
     */
    for(let i = 0; i < btn_obj_showname_list.length; ++i){
        btn_obj_showname_list[i].drawName();
    }



    // 把关联关系计入 每一个 Button 的实例
    //

    // Add Listener
    let listenerFunc = function (e) {
        showLoadingLayer();
        real_ctx.clearRect(0, 0, c_width, c_height);
        drawKompletteMappingListeCanvas(list, List_L, List_R, e.offsetX, e.offsetY, mappingtyp);

        // 移除 Listener
        real_canvas.removeEventListener('click', listenerFunc, false);

        removeLoadingLayer();
    };
    real_canvas.addEventListener('click', listenerFunc, false);



    // 将缓存中的canvas绘制到页面上的div中
    real_ctx.drawImage(amlw_canvas, 0, 0);


    //test
    /*
    for(let i = 0; i < list.length; ++i){
        let btn_ean_l = list[i][btnColumnName_L];
        let btn_ean_r = list[i][btnColumnName_R];
        console.log('### '+btn_ean_l+' | x:'+btn_conn_points["left"][btn_ean_l]["x"]+' | y:'+btn_conn_points["left"][btn_ean_l]["y"]+' ### '+btn_ean_r+' | x:'+btn_conn_points["right"][btn_ean_r]["x"]+' | y:'+btn_conn_points["right"][btn_ean_r]["y"]+' ###');
    }
*/


}

class LineBetweenBtnsInMappingCanvas
{

    constructor(ctx, start_x, start_y, end_x, end_y, mouse_x, mouse_y, isActive)
    {
        // parameters
        this.ctx = ctx;
        this.start_x = start_x;
        this.start_y = start_y;
        this.end_x = end_x;
        this.end_y = end_y;
        this.mouse_x = mouse_x;
        this.mouse_y = mouse_y;
        this.isActive = isActive;
        // attributes
        this.color = '#3c8dbc';
        this.global_alpha = 0.1;
        // active attributes
        //this.active_color = '#3c8dbc';
        this.active_global_alpha = 1.0;
    }

    draw()
    {

        // if this line is active
        if(this.isActive){
            this.global_alpha = this.active_global_alpha;
        }

        // globalAlpha
        this.ctx.globalAlpha = this.global_alpha;

        this.ctx.strokeStyle = this.color;
        this.ctx.beginPath();
        this.ctx.moveTo(this.start_x, this.start_y);
        this.ctx.lineTo(this.end_x, this.end_y);
        this.ctx.stroke();

    }

}

/**
 * @param ctx context
 * @param left_top_x 按钮左上角的x值
 * @param left_top_y 按钮左上角的值
 */
class EANBtnInMappingCanvas
{

    constructor(ctx, left_top_x, left_top_y, txt, mouse_x, mouse_y, column, isActive)
    {
        // parameters
        this.ctx = ctx;
        this.left_top_x = left_top_x;
        this.left_top_y = left_top_y;
        this.txt = txt;
        this.mouse_x = mouse_x;
        this.mouse_y = mouse_y;
        this.isActive = isActive;
        this.column = column;
        // attributes
        this.btn_width = 140;
        this.btn_height = 30;
        this.btn_bg_color = '#f7f7f7';
        this.btn_border_color = '#3c8dbc';
        this.btn_txt_color = '#337ab7';
        this.btn_font = 'normal 16px Arial';
        this.btn_global_alpha = 0.6;
        this.is_name_drawed = false;
        this.line_width = 1;
        // active attributes
        this.active_btn_bg_color = '#3c8dbc';
        this.active_btn_border_color = '#3c8dbc';
        this.active_btn_txt_color = '#ffffff';
        this.active_btn_global_alpha = 1.0;
        this.active_btn_pname_color = '#000000';
    }

    get conn_point_left_x ()
    {
        return this.left_top_x;
    }

    get conn_point_left_y ()
    {
        return this.left_top_y + this.btn_height / 2;
    }

    get conn_point_right_x ()
    {
        return this.left_top_x + this.btn_width;
    }

    get conn_point_right_y ()
    {
        return this.left_top_y + this.btn_height / 2;
    }

    isClicked ()
    {
        let res = false;
        if(this.mouse_x >= 0 && this.mouse_y >= 0){
            if(this.isPointInPath(this.mouse_x, this.mouse_y, this.left_top_x, this.left_top_y, this.btn_width, this.btn_height)){
                res = true;
            }
        }
        return res;
    }

    setActive ()
    {
        this.isActive = true;
        this.draw();
    }

    /**
     * draw the button
     * void
     */
    draw()
    {

        // be clicked or be active
        if(this.isClicked() || this.isActive){
            this.btn_bg_color = this.active_btn_bg_color;
            this.btn_border_color = this.active_btn_border_color;
            this.btn_txt_color = this.active_btn_txt_color;
            this.btn_global_alpha = this.active_btn_global_alpha;
        }

        // init
        this.ctx.globalAlpha = this.btn_global_alpha;
        this.ctx.lineWidth = this.line_width;

        // button background
        this.ctx.fillStyle = this.btn_bg_color;
        this.ctx.fillRect(this.left_top_x, this.left_top_y, this.btn_width, this.btn_height);

        // button border
        this.ctx.strokeStyle = this.btn_border_color;
        this.ctx.beginPath();
        this.ctx.moveTo(this.left_top_x, this.left_top_y);
        this.ctx.lineTo(this.left_top_x + this.btn_width, this.left_top_y);
        this.ctx.lineTo(this.left_top_x + this.btn_width, this.left_top_y + this.btn_height);
        this.ctx.lineTo(this.left_top_x, this.left_top_y + this.btn_height);
        this.ctx.lineTo(this.left_top_x, this.left_top_y);
        this.ctx.stroke();

        // button text
        this.ctx.fillStyle = this.btn_txt_color;
        this.ctx.strokeStyle = this.btn_bg_color;
        this.ctx.font = this.btn_font; // 字体
        this.ctx.textBaseline = "middle"; // 竖直对齐
        this.ctx.textAlign = "center";// 水平对齐
        this.ctx.fillText(this.txt, this.left_top_x + this.btn_width / 2, this.left_top_y + this.btn_height / 2 + 2, this.btn_width); // 绘制文字 中间两个参数是文字中心点的 x y 坐标

    }

    drawName()
    {
        /**
         * 是10位以上的ean 才显示名称
         */
        if(this.txt.length > 10){

            if(!this.is_name_drawed)
            {
                if(this.isClicked() || this.isActive)
                {

                    this.ctx.globalAlpha = 1.0;

                    this.ctx.textAlign = "left";// 水平对齐
                    let p_name_x = this.left_top_x + this.btn_width + 12;
                    if(this.column === 'right'){
                        this.ctx.textAlign = "right";// 水平对齐
                        p_name_x = this.left_top_x - 12;
                    }

                    let p_name = getProductTitleByEAN(this.txt);
                    this.ctx.fillStyle = this.active_btn_pname_color;
                    this.ctx.strokeStyle = '#ffffff';
                    this.ctx.lineWidth = 5;
                    this.ctx.strokeText(p_name, p_name_x, this.left_top_y + this.btn_height / 2 + 2);
                    this.ctx.lineWidth = this.line_width;
                    this.ctx.fillText(p_name, p_name_x, this.left_top_y + this.btn_height / 2 + 2);

                    this.is_name_drawed = true;

                }
            }

        }

    }

    /**
     * check if the point of click event in the area of this button
     * @param p_x
     * @param p_y
     * @param lt_x
     * @param lt_y
     * @param width
     * @param height
     * @returns {boolean}
     */
    isPointInPath(p_x, p_y, lt_x, lt_y, width, height){
        if(p_x >= lt_x && p_x <= lt_x + width && p_y >= lt_y && p_y <= lt_y + height){
            return true;
        }else{
            return false;
        }
    }

}

/**
 * Ersatzteil
 * 参数说明
 * @param act       0: load all records
 *                  1: insert a new one
 *                  2: update a reason
 *                  3: 下单页面读reason数据
 * @param id        record id
 * @param reason    reason
 */
function doErsatzteil(act, id, ta_id) {

    showLoadingLayer();

    let reason = '';

    let error = '';

    /**
     * 获取input值
     */
    let reason_TA_obj = $('#'+ta_id);
    let ersatzteil_reasons_wrap_obj = $('#ersatzteil-reasons-wrap');
    if(parseInt(act) === 0){
        if(!ersatzteil_reasons_wrap_obj.length > 0){
            error = 'Wrap für Gründe Liste is nicht vorhanden.';
        }
    }else if(parseInt(act) === 1 || parseInt(act) === 2){
        if(reason_TA_obj.val() !== undefined){
            reason = reason_TA_obj.val();
            if(reason.length < 1 || reason.length > 800){
                error = 'Der Grund darf nicht leer.';
            }
        }else{
            error = 'Textarea is nicht vorhanden.';
        }
    }


    let entpoint = '/api/updateersatzteilreason';
    let params = {};
    switch (parseInt(act)){
        case 0:
            params.act = act;
            break;
        case 1:
            params.act = act;
            params.reason = reason;
            break;
        case 2:
            params.act = act;
            params.id = id;
            params.reason = reason;
            break;
        case 3:
            params.act = act;
            break;
        default:
            //
    }

    if(error === ''){

        $.ajax({
            url: entpoint,
            data: params,
            dataType: "json",
            type: "POST",
            traditional: true,
            success: function (data) {
                if(data.isSuccess){

                    // 处理结果
                    switch (parseInt(act)){
                        case 0:
                            let est_reasons = data.data;
                            let htmlTxt = '<div class="box">\n' +
                                '              <table class="table table-bordered">\n' +
                                '                <tr>\n' +
                                '                  <th style="width: 50px; text-align: center;">ID</th>\n' +
                                '                  <th style="padding-left: 30px !important;">Grund</th>\n' +
                                '                  <th style="width: 130px;"></th>\n' +
                                '                </tr>\n';

                            for(let i = 0; i < est_reasons.length; ++i){
                                htmlTxt += '                <tr>\n' +
                                    '                  <td align="center">' + est_reasons[i].meta_id + '</td>\n' +
                                    '                  <td style="padding-left: 30px !important; padding-right: 30px !important;">' +
                                    '                       <span id="est-reason-span-' + est_reasons[i].meta_id + '">' + est_reasons[i].reason + '</span>' +
                                    '                  </td>\n' +
                                    '                  <td align="center" id="btn-wrap-' + est_reasons[i].meta_id + '" style="text-align: left; padding-left: 25px;">' +
                                    '                      <a class="mouseoverhand forbidSelectText" onclick="doErsatzteil_activeUpdate(' + est_reasons[i].meta_id + ');"><i class="fa fa-edit"></i>&nbsp;&nbsp;Verbessern</a>' +
                                    '                  </td>\n' +
                                    '                </tr>\n';
                            }

                            htmlTxt += '              </table>\n' +
                                '          </div>';
                            ersatzteil_reasons_wrap_obj.html(htmlTxt);
                            break;
                        case 1:
                            reason_TA_obj.val('');
                            doErsatzteil(0,0,'NULL');
                            customAlert("Ersatzteil Bearbeiten Info: ID-10046", 1, data.msg);
                            break;
                        case 2:
                            doErsatzteil(0,0,'NULL');
                            customAlert("Ersatzteil Bearbeiten Info: ID-10046", 1, data.msg);
                            break;
                        case 3:
                            let est_reasons_1 = data.data;
                            for(let i = 0; i < est_reasons_1.length; ++i){
                                ersatzteilReasons[est_reasons_1[i]["meta_id"]] = est_reasons_1[i]["reason"];
                            }
                            break;
                        default:
                        //
                    }

                }else{
                    customAlert("Ersatzteil Bearbeiten Fehler: ID-10047", 2, data.msg);
                }

                removeLoadingLayer();
            }
        });

    }else{

        customAlert("Ersatzteil Bearbeiten Fehler: ID-10048", 2, error);
        removeLoadingLayer();

    }



}

let oldErsatzteilReasonList = {};
function doErsatzteil_activeUpdate(id) {
    // 文字部分
    let estReasonSpan = $('#est-reason-span-'+id);
    oldErsatzteilReasonList[id] = estReasonSpan.html();
    let htmlTxt1 = '<div class="form-group">\n' +
        '               <label for="est-reason-ta-act2-' + id + '">Der Grund verbessern ( maximale Länge: 800 Zeichen )</label>\n' +
        '               <textarea id="est-reason-ta-act2-' + id + '" class="form-control" rows="2" style="max-width: 100%; min-width: 100%;" placeholder="" onkeyup=calculateNotizTxtLen("est-reason-ta-act2-' + id + '");>' + oldErsatzteilReasonList[id] + '</textarea>\n' +
        '               <span id="reason-len-tip-' + id + '"></span>\n' +
        '          </div>';
    estReasonSpan.html(htmlTxt1);

    // 按钮部分
    let btnWrap = $('#btn-wrap-' + id);
    let htmlTxt2 = '<br><a class="mouseoverhand forbidSelectText" onclick=doErsatzteil_deactiveUpdate(' + id + ');><i class="fa fa-reply"></i>&nbsp;&nbsp;Abbrechen</a>';
    htmlTxt2 += '<br><a class="mouseoverhand forbidSelectText" onclick="doErsatzteil_save(' + id + ');"><i class="fa fa-save"></i>&nbsp;&nbsp;Speichern</a>';
    btnWrap.html(htmlTxt2);
}
function doErsatzteil_deactiveUpdate(id) {
    // 文字部分
    let estReasonSpan = $('#est-reason-span-'+id);
    estReasonSpan.html(oldErsatzteilReasonList[id]);
    // 按钮部分
    let btnWrap = $('#btn-wrap-' + id);
    let htmlTxt2 = '<a class="mouseoverhand forbidSelectText" onclick="doErsatzteil_activeUpdate(' + id + ');"><i class="fa fa-edit"></i>&nbsp;&nbsp;Verbessern</a>';
    btnWrap.html(htmlTxt2);
}
function doErsatzteil_save(id) {
    doErsatzteil(2, id, 'est-reason-ta-act2-' + id);
}




/**
 * Lager mapping
 */
function searchMappingsByShelfId() {

    showLoadingLayer();

    let shelfId = $('#mapping-elm-ean').val();

    if(isRightRegalId(shelfId)){
        loadMappingEANs(1, shelfId, 'shelf-elm');
    }else{
        customAlert("Mapping Fehler: ID-10049", 2, 'Bitte die richtige Regal ID (xx-x) eingeben.');
        removeLoadingLayer();
    }

}

function saveShelfMappings(nr) {

    showLoadingLayer();

    /**
     * 参数准备
     */
    let type = nr;
    let shelf_id = '';
    let elm = '';
    let eanList = $('#tags-wrap-00' + nr).tagEditor('getTags')[0].tags;
    switch (parseInt(nr)){
        case 1:
            shelf_id = $('#mapping-elm-ean').val();
            for (let i = 0; i < eanList.length; i++) {
                elm += eanList[i];
                if(i < eanList.length - 1){
                    elm += ',';
                }
            }
            break;
        default:
        //
    }

    /**
     * 参数验证
     */
    switch (parseInt(nr)){
        case 1:
            if(!isRightRegalId(shelf_id)){
                customAlert("Mapping Fehler: ID-10049", 2, 'Bitte die richtige Regal ID (xx-x) eingeben.');
                removeLoadingLayer();
                return false;
            }
            break;
        default:
        //
    }
    for (let j = 0; j < eanList.length; j++) {
        if(!isRightEAN(eanList[j])){
            customAlert("Mapping Fehler: ID-10043", 2, 'Bitte die richtige EAN (42507553XXXXX) eingeben.');
            removeLoadingLayer();
            return false;
        }
    }

    /**
     * 呼叫Ajax
     */
    if(eanList.length > 0){
        $.ajax({
            url:'/api/updatemappingshelfandelementlist',
            data: {
                type : type,
                elm : elm,
                shelfid : shelf_id
            },
            dataType: "json",
            type: "POST",
            traditional: true,
            success: function (data) {
                if(data.isSuccess){
                    customAlert("Mapping Info: ID-10044", 1, data.msg);
                }else{
                    customAlert("Mapping Fehler: ID-10041", 2, data.msg);
                }

                removeLoadingLayer();
            }
        });
    }else{
        customAlert("Mapping Fehler: ID-10045", 2, 'EAN Felder darf nicht leer sein.');
        removeLoadingLayer();
    }


}

function searchOrderFromAfterbuy() {
    showLoadingLayer();

    //autoSelectAfterbuyAccount();
    ersatzteilCreatePageClear();

    let error = '';

    let abKonto = $('#ab-konto').val();
    let abOrderId = $('#order-id').val();
    if(abKonto === null){
        error = 'Bitte Afterbuy Konto auswählen.';
    }else if(abOrderId.length < 1){
        error = 'Bitte Afterbuy Order-ID eingeben.';
    }

    if(error === ''){
        getOrderFromAfterbuyById(abKonto, abOrderId);
    }else{
        customAlert("Ersatzteil Bestellen Fehler: ID-10050", 2, error);
        removeLoadingLayer();
    }

}

function getOrderFromAfterbuyById(abKonto, abOrderId) {
    $.ajax({
        url:'/api/getafterbuyorderbyid',
        data: {
            abKonto : abKonto,
            abOrderId : abOrderId
        },
        dataType: "json",
        type: "POST",
        traditional: true,
        success: function (data) {
            if(data.isSuccess){
                let dataSet = data.data[0];
                getKundenInfo(dataSet);
                $('#block-customer-info').css('display', 'block');
                //customAlert("Ersatzteil Bestellen Info: ID-10052", 1, data.msg);
            }else{
                ersatzteilCreatePageClear();
                customAlert("Ersatzteil Bestellen Fehler: ID-10051", 2, data.msg);
                removeLoadingLayer();
            }
        }
    });
}

function getKundenInfo(dataSet) {

    let billingAddress = dataSet.BillingAddress;
    let shippingAddress = dataSet.ShippingAddress;
    let isBAddrSAddrNotSame = dataSet.IsBAddrSAddrNotSame;

    let afterbuyUserID = billingAddress.AfterbuyUserID;
    let userIDPlattform = billingAddress.UserIDPlattform;
    $('#afterbuy-customer-id').html(userIDPlattform + '&nbsp;(' + afterbuyUserID + ')');

    let htmlTxt = '<div class="col-md-6" id="form-wrap-rechnungsanschrift">\n' +
        '                <div id="afterbuy-customer-id-nr" style="display: none;">' + afterbuyUserID + '</div>\n' +
        '                <div class="box">\n' +
        '                    <div class="box-header with-border">\n' +
        '                        <h3 class="box-title">Rechnungsanschrift</h3>\n' +
        '                    </div>\n' +
        '                    <form role="form">\n' +
        '                        <div class="box-body">\n' +
        '                            <div class="form-group">\n' +
        '                                <label for="KFirma-RA">Firma</label>\n' +
        '                                <input type="text" class="form-control" id="KFirma-RA" placeholder="Firmenname des Empfängers">\n' +
        '                            </div>\n' +
        '                            <div class="form-group">\n' +
        '                                <label for="KVorname-RA">Vorname *</label>\n' +
        '                                <input type="text" class="form-control" id="KVorname-RA" placeholder="Vorname des Empfängers">\n' +
        '                            </div>\n' +
        '                            <div class="form-group">\n' +
        '                                <label for="KNachname-RA">Nachname *</label>\n' +
        '                                <input type="text" class="form-control" id="KNachname-RA" placeholder="Nachname des Empfängers">\n' +
        '                            </div>\n' +
        '                            <div class="form-group">\n' +
        '                                <label for="KStrasse-RA">Straße und Hausnummer *</label>\n' +
        '                                <input type="text" class="form-control" id="KStrasse-RA" placeholder="Straße und Hausnummer">\n' +
        '                            </div>\n' +
        '                            <div class="form-group">\n' +
        '                                <label for="KStrasse2-RA">Adresszusatz</label>\n' +
        '                                <input type="text" class="form-control" id="KStrasse2-RA" placeholder="Zusatzinfo der Adresse">\n' +
        '                            </div>\n' +
        '                            <div class="form-group">\n' +
        '                                <div class="col-md-4" style="padding-left: unset !important;">\n' +
        '                                    <label for="KPLZ-RA">PLZ *</label>\n' +
        '                                    <input type="text" class="form-control" id="KPLZ-RA" placeholder="PLZ">\n' +
        '                                </div>\n' +
        '                                <div class="col-md-4" style="padding-left: unset !important;">\n' +
        '                                    <label for="KOrt-RA">Ort *</label>\n' +
        '                                    <input type="text" class="form-control" id="KOrt-RA" placeholder="Ort">\n' +
        '                                </div>\n' +
        '                                <div class="col-md-4" style="padding-left: unset !important; padding-right: unset !important; padding-bottom: 15px !important;">\n' +
        '                                    <label for="KBundesland-RA">Land *</label>\n' +
        '                                    <select id="KBundesland-RA" class="form-control" style="border-radius: 3px;"><option disabled="">--------------------------------</option><option class="disabledSelectOption" disabled="">Häufige&nbsp;Zielländer&nbsp;:</option><option disabled="">--------------------------------</option><option value="BE">Belgien</option><option value="DE" selected="">Deutschland</option><option value="FR">Frankreich</option><option value="IT">Italien</option><option value="LU">Luxemburg</option><option value="NL">Niederlande</option><option value="AT">Österreich</option><option disabled="">----------------------------------------</option><option class="disabledSelectOption" disabled="">Nicht&nbsp;häufige&nbsp;Zielländer&nbsp;:</option><option disabled="">----------------------------------------</option><option value="AF">Afghanistan</option><option value="EG">Ägypten</option><option value="AX">Åland</option><option value="AL">Albanien</option><option value="DZ">Algerien</option><option value="VI">Amerikanische Jungferninseln</option><option value="AS">Amerikanisch-Samoa</option><option value="AD">Andorra</option><option value="AO">Angola</option><option value="AI">Anguilla</option><option value="AQ">Antarktika</option><option value="AG">Antigua und Barbuda</option><option value="GQ">Äquatorialguinea</option><option value="AR">Argentinien</option><option value="AM">Armenien</option><option value="AW">Aruba</option><option value="AZ">Aserbaidschan</option><option value="ET">Äthiopien</option><option value="AU">Australien</option><option value="BS">Bahamas</option><option value="BH">Bahrain</option><option value="BD">Bangladesch</option><option value="BB">Barbados</option><option value="TF">Bassas da India</option><option value="BY">Belarus</option><option value="BZ">Belize</option><option value="BJ">Benin</option><option value="BM">Bermuda</option><option value="BT">Bhutan</option><option value="BO">Bolivien</option><option value="BA">Bosnien und Herzegowina</option><option value="BW">Botsuana</option><option value="BV">Bouvetinsel</option><option value="BR">Brasilien</option><option value="VG">Britische Jungferninseln</option><option value="IO">Britisches Territorium im Indischen Ozean</option><option value="BN">Brunei Darussalam</option><option value="BG">Bulgarien</option><option value="BF">Burkina Faso</option><option value="BI">Burundi</option><option value="CV">Cabo Verde</option><option value="CL">Chile</option><option value="CN">China</option><option value="FR">Clipperton</option><option value="CK">Cookinseln</option><option value="CR">Costa Rica</option><option value="CI">Côte d’Ivoire</option><option value="DK">Dänemark</option><option value="DM">Dominica</option><option value="DO">Dominikanische Republik</option><option value="DJ">Dschibuti</option><option value="EC">Ecuador</option><option value="SV">El Salvador</option><option value="ER">Eritrea</option><option value="EE">Estland</option><option value="TF">Europa</option><option value="FK">Falklandinseln</option><option value="FO">Färöer</option><option value="FJ">Fidschi</option><option value="FI">Finnland</option><option value="FX">Frakreich (metropolitanes)</option><option value="TF">Französische Süd- und Antarktisgebiete</option><option value="GF">Französisch-Guayana</option><option value="PF">Französisch-Polynesien</option><option value="GA">Gabun</option><option value="GM">Gambia</option><option value="PS">Gazastreifen</option><option value="GE">Georgien</option><option value="GH">Ghana</option><option value="GI">Gibraltar</option><option value="TF">Glorieuses</option><option value="GD">Grenada</option><option value="GR">Griechenland</option><option value="GL">Grönland</option><option value="GB">Großbritannien</option><option value="GP">Guadeloupe</option><option value="GU">Guam</option><option value="GT">Guatemala</option><option value="GG">Guernsey</option><option value="GN">Guinea</option><option value="GW">Guinea-Bissau</option><option value="GY">Guyana</option><option value="HT">Haiti</option><option value="HM">Heard und McDonaldinseln</option><option value="HN">Honduras</option><option value="HK">Hongkong</option><option value="IN">Indien</option><option value="ID">Indonesien</option><option value="IM">Insel Man</option><option value="IQ">Irak</option><option value="IR">Iran</option><option value="IE">Irland</option><option value="IS">Island</option><option value="IL">Israel</option><option value="JM">Jamaika</option><option value="JP">Japan</option><option value="YE">Jemen</option><option value="JE">Jersey</option><option value="JO">Jordanien</option><option value="TF">Juan de Nova</option><option value="KY">Kaimaninseln</option><option value="KH">Kambodscha</option><option value="CM">Kamerun</option><option value="CA">Kanada</option><option value="KZ">Kasachstan</option><option value="QA">Katar</option><option value="KE">Kenia</option><option value="KG">Kirgisistan</option><option value="KI">Kiribati</option><option value="UM">Kleinere Amerikanische Überseeinseln</option><option value="CC">Kokosinseln (Keelinginseln)</option><option value="CO">Kolumbien</option><option value="KM">Komoren</option><option value="CG">Kongo</option><option value="CD">Kongo, Demokratische Republik</option><option value="KP">Korea, Demokratische Volksrepublik</option><option value="KR">Korea, Republik</option><option value="HR">Kroatien</option><option value="CU">Kuba</option><option value="KW">Kuwait</option><option value="LA">Laos</option><option value="LS">Lesotho</option><option value="LV">Lettland</option><option value="LB">Libanon</option><option value="LR">Liberia</option><option value="LY">Libyen</option><option value="LI">Liechtenstein</option><option value="LT">Litauen</option><option value="MO">Macau</option><option value="MG">Madagaskar</option><option value="MW">Malawi</option><option value="MY">Malaysia</option><option value="MV">Malediven</option><option value="ML">Mali</option><option value="MT">Malta</option><option value="MA">Marokko</option><option value="MH">Marshallinseln</option><option value="MQ">Martinique</option><option value="MR">Mauretanien</option><option value="MU">Mauritius</option><option value="YT">Mayotte</option><option value="MK">Mazedonien</option><option value="MX">Mexiko</option><option value="FM">Mikronesien</option><option value="MD">Moldau</option><option value="MC">Monaco</option><option value="MN">Mongolei</option><option value="ME">Montenegro</option><option value="MS">Montserrat</option><option value="MZ">Mosambik</option><option value="MM">Myanmar</option><option value="NA">Namibia</option><option value="NR">Nauru</option><option value="NP">Nepal</option><option value="NC">Neukaledonien</option><option value="NZ">Neuseeland</option><option value="NI">Nicaragua</option><option value="AN">Niederländische Antillen</option><option value="NE">Niger</option><option value="NG">Nigeria</option><option value="NU">Niue</option><option value="MP">Nördliche Marianen</option><option value="NF">Norfolkinsel</option><option value="NO">Norwegen</option><option value="OM">Oman</option><option value="PK">Pakistan</option><option value="PW">Palau</option><option value="PA">Panama</option><option value="PG">Papua-Neuguinea</option><option value="PY">Paraguay</option><option value="PE">Peru</option><option value="PH">Philippinen</option><option value="PN">Pitcairninseln</option><option value="PL">Polen</option><option value="PT">Portugal</option><option value="PR">Puerto Rico</option><option value="RE">Réunion</option><option value="RW">Ruanda</option><option value="RO">Rumänien</option><option value="RU">Russische Föderation</option><option value="MF">Saint-Martin</option><option value="SB">Salomonen</option><option value="ZM">Sambia</option><option value="WS">Samoa</option><option value="SM">San Marino</option><option value="ST">São Tomé und Príncipe</option><option value="SA">Saudi-Arabien</option><option value="SE">Schweden</option><option value="CH">Schweiz</option><option value="SN">Senegal</option><option value="RS">Serbien</option><option value="CS">Serbien und Montenegro</option><option value="SC">Seychellen</option><option value="SL">Sierra Leone</option><option value="ZW">Simbabwe</option><option value="SG">Singapur</option><option value="SK">Slowakei</option><option value="SI">Slowenien</option><option value="SO">Somalia</option><option value="ES">Spanien</option><option value="SJ">Spitzbergen</option><option value="LK">Sri Lanka</option><option value="BL">St. Barthélemy</option><option value="SH">St. Helena, Ascension und Tristan da Cunha</option><option value="KN">St. Kitts und Nevis</option><option value="LC">St. Lucia</option><option value="PM">St. Pierre und Miquelon</option><option value="VC">St. Vincent und die Grenadinen</option><option value="ZA">Südafrika</option><option value="SD">Sudan</option><option value="GS">Südgeorgien und die Südlichen Sandwichinseln</option><option value="SS">Südsudan</option><option value="SR">Suriname</option><option value="SZ">Swasiland</option><option value="SY">Syrien</option><option value="TJ">Tadschikistan</option><option value="TW">Taiwan</option><option value="TZ">Tansania</option><option value="TH">Thailand</option><option value="TL">Timor-Leste</option><option value="TG">Togo</option><option value="TK">Tokelau</option><option value="TO">Tonga</option><option value="TT">Trinidad und Tobago</option><option value="TF">Tromelin</option><option value="TD">Tschad</option><option value="CZ">Tschechische Republik</option><option value="TN">Tunesien</option><option value="TR">Türkei</option><option value="TM">Turkmenistan</option><option value="TC">Turks- und Caicosinseln</option><option value="TV">Tuvalu</option><option value="UG">Uganda</option><option value="UA">Ukraine</option><option value="HU">Ungarn</option><option value="UY">Uruguay</option><option value="UZ">Usbekistan</option><option value="VU">Vanuatu</option><option value="VA">Vatikanstadt</option><option value="VE">Venezuela</option><option value="AE">Vereinigte Arabische Emirate</option><option value="US">Vereinigte Staaten</option><option value="VN">Vietnam</option><option value="WF">Wallis und Futuna</option><option value="CX">Weihnachtsinsel</option><option value="PS">Westjordanland</option><option value="EH">Westsahara</option><option value="CF">Zentralafrikanische Republik</option><option value="CY">Zypern</option></select>\n' +
        '                                </div>\n' +
        '                            </div>\n' +
        '                            <div class="form-group">\n' +
        '                                <label for="Ktelefon-RA">Telefonnummer</label>\n' +
        '                                <input type="text" class="form-control" id="Ktelefon-RA" placeholder="Telefonnummer des Empfängers">\n' +
        '                            </div>\n' +
        '                            <div class="form-group">\n' +
        '                               <label for="Kemail">E-Mail Adresse</label>\n' +
        '                               <input type="email" class="form-control" id="Kemail" onkeyup="afterInputCustomerEmail();" placeholder="E-Mail des Käufers">\n' +
        '                            </div>' +
        '                            <div class="checkbox" style="padding-top: 8px;">\n' +
        '                                <label>\n' +
        '                                    <input class="iCheck-helper" type="checkbox" id="cb-iLavR"> Ist Lieferanschrift abweichend<br/>von Rechnungsanschrift?\n' +
        '                                </label>\n' +
        '                            </div>\n' +
        '                        </div>\n' +
        '                    </form>\n' +
        '                </div>\n' +
        '            </div>\n' +
        '            <div class="col-md-6" id="form-wrap-lieferanschrift" style="background-image: url(&quot;/wp-content/uploads/images/LgwR.png&quot;);">\n' +
        '                <div class="box" id="form-wrap-lieferanschrift-box" style="opacity: 0.3;">\n' +
        '                    <div class="box-header with-border">\n' +
        '                        <h3 class="box-title">Lieferanschrift</h3>\n' +
        '                    </div>\n' +
        '                    <form role="form">\n' +
        '                        <div class="box-body">\n' +
        '                            <div class="form-group">\n' +
        '                                <label for="KFirma-LA">Firma</label>\n' +
        '                                <input type="text" class="form-control" id="KFirma-LA" placeholder="Firmenname des Empfängers" disabled="">\n' +
        '                            </div>\n' +
        '                            <div class="form-group">\n' +
        '                                <label for="KVorname-LA">Vorname *</label>\n' +
        '                                <input type="text" class="form-control" id="KVorname-LA" placeholder="Vorname des Empfängers" disabled="">\n' +
        '                            </div>\n' +
        '                            <div class="form-group">\n' +
        '                                <label for="KNachname-LA">Nachname *</label>\n' +
        '                                <input type="text" class="form-control" id="KNachname-LA" placeholder="Nachname des Empfängers" disabled="">\n' +
        '                            </div>\n' +
        '                            <div class="form-group">\n' +
        '                                <label for="KStrasse-LA">Straße und Hausnummer *</label>\n' +
        '                                <input type="text" class="form-control" id="KStrasse-LA" placeholder="Straße und Hausnummer" disabled="">\n' +
        '                            </div>\n' +
        '                            <div class="form-group">\n' +
        '                                <label for="KStrasse2-LA">Adresszusatz</label>\n' +
        '                                <input type="text" class="form-control" id="KStrasse2-LA" placeholder="Zusatzinfo der Adresse" disabled="">\n' +
        '                            </div>\n' +
        '                            <div class="form-group">\n' +
        '                                <div class="col-md-4" style="padding-left: unset !important;">\n' +
        '                                    <label for="KPLZ-LA">PLZ *</label>\n' +
        '                                    <input type="text" class="form-control" id="KPLZ-LA" placeholder="PLZ" disabled="">\n' +
        '                                </div>\n' +
        '                                <div class="col-md-4" style="padding-left: unset !important;">\n' +
        '                                    <label for="KOrt-LA">Ort *</label>\n' +
        '                                    <input type="text" class="form-control" id="KOrt-LA" placeholder="Ort" disabled="">\n' +
        '                                </div>\n' +
        '                                <div class="col-md-4" style="padding-left: unset !important; padding-right: unset !important; padding-bottom: 15px !important;">\n' +
        '                                    <label for="KBundesland-LA">Land *</label>\n' +
        '                                    <select id="KBundesland-LA" class="form-control" style="border-radius: 3px;" disabled=""><option disabled="">--------------------------------</option><option class="disabledSelectOption" disabled="">Häufige&nbsp;Zielländer&nbsp;:</option><option disabled="">--------------------------------</option><option value="BE">Belgien</option><option value="DE" selected="">Deutschland</option><option value="FR">Frankreich</option><option value="IT">Italien</option><option value="LU">Luxemburg</option><option value="NL">Niederlande</option><option value="AT">Österreich</option><option disabled="">----------------------------------------</option><option class="disabledSelectOption" disabled="">Nicht&nbsp;häufige&nbsp;Zielländer&nbsp;:</option><option disabled="">----------------------------------------</option><option value="AF">Afghanistan</option><option value="EG">Ägypten</option><option value="AX">Åland</option><option value="AL">Albanien</option><option value="DZ">Algerien</option><option value="VI">Amerikanische Jungferninseln</option><option value="AS">Amerikanisch-Samoa</option><option value="AD">Andorra</option><option value="AO">Angola</option><option value="AI">Anguilla</option><option value="AQ">Antarktika</option><option value="AG">Antigua und Barbuda</option><option value="GQ">Äquatorialguinea</option><option value="AR">Argentinien</option><option value="AM">Armenien</option><option value="AW">Aruba</option><option value="AZ">Aserbaidschan</option><option value="ET">Äthiopien</option><option value="AU">Australien</option><option value="BS">Bahamas</option><option value="BH">Bahrain</option><option value="BD">Bangladesch</option><option value="BB">Barbados</option><option value="TF">Bassas da India</option><option value="BY">Belarus</option><option value="BZ">Belize</option><option value="BJ">Benin</option><option value="BM">Bermuda</option><option value="BT">Bhutan</option><option value="BO">Bolivien</option><option value="BA">Bosnien und Herzegowina</option><option value="BW">Botsuana</option><option value="BV">Bouvetinsel</option><option value="BR">Brasilien</option><option value="VG">Britische Jungferninseln</option><option value="IO">Britisches Territorium im Indischen Ozean</option><option value="BN">Brunei Darussalam</option><option value="BG">Bulgarien</option><option value="BF">Burkina Faso</option><option value="BI">Burundi</option><option value="CV">Cabo Verde</option><option value="CL">Chile</option><option value="CN">China</option><option value="FR">Clipperton</option><option value="CK">Cookinseln</option><option value="CR">Costa Rica</option><option value="CI">Côte d’Ivoire</option><option value="DK">Dänemark</option><option value="DM">Dominica</option><option value="DO">Dominikanische Republik</option><option value="DJ">Dschibuti</option><option value="EC">Ecuador</option><option value="SV">El Salvador</option><option value="ER">Eritrea</option><option value="EE">Estland</option><option value="TF">Europa</option><option value="FK">Falklandinseln</option><option value="FO">Färöer</option><option value="FJ">Fidschi</option><option value="FI">Finnland</option><option value="FX">Frakreich (metropolitanes)</option><option value="TF">Französische Süd- und Antarktisgebiete</option><option value="GF">Französisch-Guayana</option><option value="PF">Französisch-Polynesien</option><option value="GA">Gabun</option><option value="GM">Gambia</option><option value="PS">Gazastreifen</option><option value="GE">Georgien</option><option value="GH">Ghana</option><option value="GI">Gibraltar</option><option value="TF">Glorieuses</option><option value="GD">Grenada</option><option value="GR">Griechenland</option><option value="GL">Grönland</option><option value="GB">Großbritannien</option><option value="GP">Guadeloupe</option><option value="GU">Guam</option><option value="GT">Guatemala</option><option value="GG">Guernsey</option><option value="GN">Guinea</option><option value="GW">Guinea-Bissau</option><option value="GY">Guyana</option><option value="HT">Haiti</option><option value="HM">Heard und McDonaldinseln</option><option value="HN">Honduras</option><option value="HK">Hongkong</option><option value="IN">Indien</option><option value="ID">Indonesien</option><option value="IM">Insel Man</option><option value="IQ">Irak</option><option value="IR">Iran</option><option value="IE">Irland</option><option value="IS">Island</option><option value="IL">Israel</option><option value="JM">Jamaika</option><option value="JP">Japan</option><option value="YE">Jemen</option><option value="JE">Jersey</option><option value="JO">Jordanien</option><option value="TF">Juan de Nova</option><option value="KY">Kaimaninseln</option><option value="KH">Kambodscha</option><option value="CM">Kamerun</option><option value="CA">Kanada</option><option value="KZ">Kasachstan</option><option value="QA">Katar</option><option value="KE">Kenia</option><option value="KG">Kirgisistan</option><option value="KI">Kiribati</option><option value="UM">Kleinere Amerikanische Überseeinseln</option><option value="CC">Kokosinseln (Keelinginseln)</option><option value="CO">Kolumbien</option><option value="KM">Komoren</option><option value="CG">Kongo</option><option value="CD">Kongo, Demokratische Republik</option><option value="KP">Korea, Demokratische Volksrepublik</option><option value="KR">Korea, Republik</option><option value="HR">Kroatien</option><option value="CU">Kuba</option><option value="KW">Kuwait</option><option value="LA">Laos</option><option value="LS">Lesotho</option><option value="LV">Lettland</option><option value="LB">Libanon</option><option value="LR">Liberia</option><option value="LY">Libyen</option><option value="LI">Liechtenstein</option><option value="LT">Litauen</option><option value="MO">Macau</option><option value="MG">Madagaskar</option><option value="MW">Malawi</option><option value="MY">Malaysia</option><option value="MV">Malediven</option><option value="ML">Mali</option><option value="MT">Malta</option><option value="MA">Marokko</option><option value="MH">Marshallinseln</option><option value="MQ">Martinique</option><option value="MR">Mauretanien</option><option value="MU">Mauritius</option><option value="YT">Mayotte</option><option value="MK">Mazedonien</option><option value="MX">Mexiko</option><option value="FM">Mikronesien</option><option value="MD">Moldau</option><option value="MC">Monaco</option><option value="MN">Mongolei</option><option value="ME">Montenegro</option><option value="MS">Montserrat</option><option value="MZ">Mosambik</option><option value="MM">Myanmar</option><option value="NA">Namibia</option><option value="NR">Nauru</option><option value="NP">Nepal</option><option value="NC">Neukaledonien</option><option value="NZ">Neuseeland</option><option value="NI">Nicaragua</option><option value="AN">Niederländische Antillen</option><option value="NE">Niger</option><option value="NG">Nigeria</option><option value="NU">Niue</option><option value="MP">Nördliche Marianen</option><option value="NF">Norfolkinsel</option><option value="NO">Norwegen</option><option value="OM">Oman</option><option value="PK">Pakistan</option><option value="PW">Palau</option><option value="PA">Panama</option><option value="PG">Papua-Neuguinea</option><option value="PY">Paraguay</option><option value="PE">Peru</option><option value="PH">Philippinen</option><option value="PN">Pitcairninseln</option><option value="PL">Polen</option><option value="PT">Portugal</option><option value="PR">Puerto Rico</option><option value="RE">Réunion</option><option value="RW">Ruanda</option><option value="RO">Rumänien</option><option value="RU">Russische Föderation</option><option value="MF">Saint-Martin</option><option value="SB">Salomonen</option><option value="ZM">Sambia</option><option value="WS">Samoa</option><option value="SM">San Marino</option><option value="ST">São Tomé und Príncipe</option><option value="SA">Saudi-Arabien</option><option value="SE">Schweden</option><option value="CH">Schweiz</option><option value="SN">Senegal</option><option value="RS">Serbien</option><option value="CS">Serbien und Montenegro</option><option value="SC">Seychellen</option><option value="SL">Sierra Leone</option><option value="ZW">Simbabwe</option><option value="SG">Singapur</option><option value="SK">Slowakei</option><option value="SI">Slowenien</option><option value="SO">Somalia</option><option value="ES">Spanien</option><option value="SJ">Spitzbergen</option><option value="LK">Sri Lanka</option><option value="BL">St. Barthélemy</option><option value="SH">St. Helena, Ascension und Tristan da Cunha</option><option value="KN">St. Kitts und Nevis</option><option value="LC">St. Lucia</option><option value="PM">St. Pierre und Miquelon</option><option value="VC">St. Vincent und die Grenadinen</option><option value="ZA">Südafrika</option><option value="SD">Sudan</option><option value="GS">Südgeorgien und die Südlichen Sandwichinseln</option><option value="SS">Südsudan</option><option value="SR">Suriname</option><option value="SZ">Swasiland</option><option value="SY">Syrien</option><option value="TJ">Tadschikistan</option><option value="TW">Taiwan</option><option value="TZ">Tansania</option><option value="TH">Thailand</option><option value="TL">Timor-Leste</option><option value="TG">Togo</option><option value="TK">Tokelau</option><option value="TO">Tonga</option><option value="TT">Trinidad und Tobago</option><option value="TF">Tromelin</option><option value="TD">Tschad</option><option value="CZ">Tschechische Republik</option><option value="TN">Tunesien</option><option value="TR">Türkei</option><option value="TM">Turkmenistan</option><option value="TC">Turks- und Caicosinseln</option><option value="TV">Tuvalu</option><option value="UG">Uganda</option><option value="UA">Ukraine</option><option value="HU">Ungarn</option><option value="UY">Uruguay</option><option value="UZ">Usbekistan</option><option value="VU">Vanuatu</option><option value="VA">Vatikanstadt</option><option value="VE">Venezuela</option><option value="AE">Vereinigte Arabische Emirate</option><option value="US">Vereinigte Staaten</option><option value="VN">Vietnam</option><option value="WF">Wallis und Futuna</option><option value="CX">Weihnachtsinsel</option><option value="PS">Westjordanland</option><option value="EH">Westsahara</option><option value="CF">Zentralafrikanische Republik</option><option value="CY">Zypern</option></select>\n' +
        '                                </div>\n' +
        '                            </div>\n' +
        '                            <div class="form-group">\n' +
        '                                <label for="Ktelefon-LA">Telefonnummer</label>\n' +
        '                                <input type="text" class="form-control" id="Ktelefon-LA" placeholder="Telefonnummer des Empfängers" disabled="">\n' +
        '                            </div>\n' +
        '                        </div>\n' +
        '                    </form>\n' +
        '                </div>\n' +
        '            </div>';

    $('#customer-info-wrap').html(htmlTxt);


    /**
     * set values
     */
    // billingAddress
    $('#KFirma-RA').val(billingAddress.Company);
    $('#KVorname-RA').val(billingAddress.FirstName);
    $('#KNachname-RA').val(billingAddress.LastName);
    $('#KStrasse-RA').val(billingAddress.Street);
    $('#KStrasse2-RA').val(billingAddress.Street2);
    $('#KPLZ-RA').val(billingAddress.PostalCode);
    $('#KOrt-RA').val(billingAddress.City);
    $('#KBundesland-RA').val(billingAddress.CountryISO);
    $('#Ktelefon-RA').val(billingAddress.Phone);
    $('#Kemail').val(billingAddress.Mail);
    // shippingAddress
    if(isBAddrSAddrNotSame){
        $('#cb-iLavR').prop('checked', true);
        switchLieferanschriftForm('ON');
        $('#KFirma-LA').val(shippingAddress.Company);
        $('#KVorname-LA').val(shippingAddress.FirstName);
        $('#KNachname-LA').val(shippingAddress.LastName);
        $('#KStrasse-LA').val(shippingAddress.Street);
        $('#KStrasse2-LA').val(shippingAddress.Street2);
        $('#KPLZ-LA').val(shippingAddress.PostalCode);
        $('#KOrt-LA').val(shippingAddress.City);
        $('#KBundesland-LA').val(shippingAddress.CountryISO);
        $('#Ktelefon-LA').val(shippingAddress.Phone);
    }



    $('#cb-iLavR').click(
        function () {
            if(this.checked){
                switchLieferanschriftForm("on");
            }else{
                switchLieferanschriftForm("off");
                syncAdresseR2L();
            }
        }
    );

    getMappingTree(dataSet);
}

function getMappingTree(dataSet) {
    let eans = dataSet.EANs;
    $.ajax({
        url:'/api/getmappingproductandelementandersatzteillist',
        data: {
            eans : eans
        },
        dataType: "json",
        type: "POST",
        traditional: true,
        success: function (data) {
            if(data.isSuccess){

                let dataForTree = [];

                if(data.data.length < 1){
                    let eanList = eans.split(',');
                    for(let i = 0; i<eanList.length; ++i){
                        if(eanList[i].length == 13){
                            let oneDataForTree = {};
                            oneDataForTree.ean_pro = eanList[i];
                            oneDataForTree.ean_elm = null;
                            oneDataForTree.ean_est = null;
                            dataForTree.push(oneDataForTree);
                        }
                    }
                }else{
                    dataForTree = data.data;
                }

                createErsatzteileSelectTree(dataForTree);

            }else{
                customAlert("Ersatzteil Bestellen Fehler: ID-10051", 2, data.msg);
            }
            removeLoadingLayer();
        }
    });
}

function getTreeNodeText(lvl, ean) {
    let lvlText = '';
    switch (lvl){
        case 'parent':
            lvlText = 'Produkt';
            break;
        case 'son':
            lvlText = 'Paket';
            break;
        case 'sunzi':
            lvlText = 'Ersatzteil';
            break;
        default:
            //
    }
    return '<b>' + ean + '</b>&nbsp;&nbsp;-&nbsp;&nbsp;' + '[&nbsp;<b>' + lvlText + '</b>&nbsp;]&nbsp;&nbsp;' + getProductTitleByEAN(ean);
}

function createErsatzteileSelectTree(fullMappings) {

    $('#ersatzteile-select-wrap').html('');
    $('#shopping-cart-div').html('');
    $('#order-comment').val('');

    /**
     * 整理数据格式
     */
    let estTreeDataLvl1 = {};
    let estTreeDataLvl2 = {};
    for (let aMP in fullMappings){

        let ean_pro = fullMappings[aMP]['ean_pro'];
        let ean_elm = fullMappings[aMP]['ean_elm'];
        let ean_est = fullMappings[aMP]['ean_est'];

        // 第一层 Mapping    Product -> Element
        if(ean_pro !== undefined){
            if(!estTreeDataLvl1.hasOwnProperty(ean_pro)){
                estTreeDataLvl1[ean_pro] = [];
            }
            if(!isValueInList(ean_elm, estTreeDataLvl1[ean_pro])){
                estTreeDataLvl1[ean_pro].push(ean_elm);
            }
        }

        // 第二层 Mapping    Element -> Ersatzteil
        if(ean_elm !== undefined){
            if(!estTreeDataLvl2.hasOwnProperty(ean_elm)){
                estTreeDataLvl2[ean_elm] = [];
            }
            if(ean_est !== null){
                if(!isValueInList(ean_est, estTreeDataLvl2[ean_elm])){
                    estTreeDataLvl2[ean_elm].push(ean_est);
                }
            }
        }

    }

    /**
     * Define multi-level tree nodes
     */
    let estTreeObject = [];
    let positionNr = 0;
    let positionNrArr = {};
    for (let eta1 in estTreeDataLvl1){

        if(eta1 !== null){

            positionNr++;
            positionNrArr[eta1] = positionNr;

            let itmParent = {};

            // 父亲标题
            itmParent.text = 'Position&nbsp;' + positionNr + '&nbsp;-&nbsp;' + getTreeNodeText('parent', eta1);
            itmParent.fullPath = eta1;

            itmParent.children = [];
            let itmErZiArray = estTreeDataLvl1[eta1];
            for(let eta1_1 = 0; eta1_1 < itmErZiArray.length; ++eta1_1){
                itmErZi = {};

                // 儿子标题
                itmErZi.text = getTreeNodeText('son', itmErZiArray[eta1_1]);
                itmErZi.fullPath = eta1 + '###' + itmErZiArray[eta1_1];

                itmErZi.children = [];
                let itmSunZiArray = estTreeDataLvl2[itmErZiArray[eta1_1]];
                for(let eta1_2 = 0; eta1_2 < itmSunZiArray.length; ++eta1_2){
                    itmSunZi = {};

                    // 孙子标题
                    itmSunZi.text = getTreeNodeText('sunzi', itmSunZiArray[eta1_2]);
                    itmSunZi.fullPath = eta1 + '###' + itmErZiArray[eta1_1] + '###' + itmSunZiArray[eta1_2];

                    itmErZi.children.push(itmSunZi);
                }

                itmParent.children.push(itmErZi);
            }

            estTreeObject.push(itmParent);

        }


    }

    if(estTreeObject.length > 0){

        // Initialize the treeview plugin.
        let estTree = new TreeView(estTreeObject, {
            showAlwaysCheckBox: true,
            fold: true,
            openAllFold: false
        });

        // Append the treeview to the webpage.
        document.getElementById('ersatzteile-select-wrap').appendChild(estTree.root);

        // 购物车实例化
        let shoppingCart = new ShoppingCart('Ersatzteil', { 'estTreeDataLvl1' : estTreeDataLvl1, 'estTreeDataLvl2' : estTreeDataLvl2 }, fullMappings);

        // TreeView 监听
        createTreeViewListner(shoppingCart, positionNrArr);

    }else{
        document.getElementById('ersatzteile-select-wrap').innerHTML = 'Leider keine Position gefunden.';
    }

    $('#block-ersatzteil-info').css('display', 'block');
    $('#shopping-cart-wrap').css('display', 'block');
    $('#order-comment-wrap').css('display', 'block');
    $('#create-ersatzteil-btn').css('display', 'block');

    scrollPageTo(255);

}

function createTreeViewListner(shoppingCart, positionNrArr){
    $('#ersatzteile-select-wrap span').on('click', function() {
        //doErsatzteileShoppingCart(this);
        doErsatzteileShoppingCart($('#ersatzteile-select-wrap span'), shoppingCart, positionNrArr);
    });
}

function doErsatzteileShoppingCart(obj, shoppingCart, positionNrArr) {

    shoppingCart.clear();
    estShoppingCartGlobalObj = shoppingCart;

    for(let i = 0; i < obj.length; ++i){
        if(parseInt(obj[i].checked) === 1){
            let pos = {};
            let textArr = obj[i].data.text.split('&nbsp;&nbsp;');
            pos.ean = textArr[0].substring(3, textArr[0].length -4);
            pos.ean = '42512429' + pos.ean.substr(pos.ean.length-5);
            //pos.title = textArr[3];
            pos.title = getProductTitleByEAN(pos.ean);
            pos.level = textArr[2].substring(10, textArr[2].length -11);
            pos.price = 0;
            pos.shippingCost = 0;
            pos.picUrl = '';

            // fullPath 是树形结构的完整路径  例如：4250755388526###4250755388526###4250755388526
            pos.fullPath = obj[i].data.fullPath;

            pos.positionNr = positionNrArr[get4250ean(pos.fullPath.substring(0,13))];

            if(groupedProducts['un-grouped-products'][pos.ean] !== undefined){
                pos.quantity = groupedProducts['un-grouped-products'][pos.ean]['quantity'];
            }else{
                pos.quantity = '?';
            }
            pos.qInCart = 1;
            shoppingCart.addPositions(pos);
        }
    }

    shoppingCart.show();
    estShoppingCartGlobalObj = shoppingCart;

}

/**
 * 购物车的类
 * typ暂时分为 'Order', 'Ersatzteil'
 */
class ShoppingCart {

    constructor(typ, estTreeData, fullMappings) {
        // parameters
        this._typ = typ;
        this._estTreeData = estTreeData;
        this._fullMappings = fullMappings;
        // attributes
        this._positions = {};
    }

    get typ(){
        return this._typ;
    }

    get positions(){
        return this._positions;
    }

    get fullMappings(){
        return this._fullMappings;
    }

    /**
     * add a new position
     * @param pos
     */
    addPositions(pos){
        if(pos.ean.length === 13){
            this._positions[pos.fullPath] = pos;
        }
    }

    clear() {
        this._positions = {};
    }

    /**
     * 去重复
     */
    deduplicate() {

        let list = this._positions;
        let estTreeDataLvl1 = this._estTreeData.estTreeDataLvl1;
        let estTreeDataLvl2 = this._estTreeData.estTreeDataLvl2;

        let lvls = {
            "1" : {
                "typ" : 'Paket',
                "treeData" : estTreeDataLvl2,
            },
            "2" : {
                "typ" : 'Produkt',
                "treeData" : estTreeDataLvl1,
            }
        };

        for(let i = 1; i < 3; ++i){
            let lvlInfo = lvls['' + i];
            for(let fullPath in list){

                let ean = list[fullPath].ean;

                if(list[fullPath].level === lvlInfo.typ){
                    let childrenList = lvlInfo.treeData[get4250ean(ean)];

                    /*
                    console.log(fullPath);
                    console.log('Paket 或者 Produkt 节点的 ean ' + get4250ean(ean));
                    console.log(lvlInfo.treeData);
                    console.log(childrenList);
                    */

                    if(childrenList !== undefined && childrenList.length > 0){
                        // 只要是 Product 或者 是 Paket，就清除所有子节点
                        for(let i = 0; i < childrenList.length; ++i){
                            delete list[fullPath + '###' + childrenList[i]];
                        }
                    }
                }

            }
        }

    }

    show() {

        this.deduplicate();

        let scDiv = $('#shopping-cart-div');
        scDiv.html('');

        let htmlTxt_reason_options = '';
        for (let id in ersatzteilReasons){
            htmlTxt_reason_options += '<option value="' + id + '">' + '[' + id + ']&nbsp;' + ersatzteilReasons[id] + '</option>';
        }

        let reason_select_ids = [];
        let htmlTxt = '<div class="box">\n' +
            '\t<table class="table table-bordered">\n' +
            '\t\t<tbody>\n' +
            '\t\t\t<tr>\n' +
            '\t\t\t\t<th style="width: 180px; text-align: center;">Art</th>\n' +
            '\t\t\t\t<th style="width: 150px; text-align: center;">EAN</th>\n' +
            '\t\t\t\t<th style="padding-left: 30px !important; text-align: left;">Name</th>\n' +
            '\t\t\t\t<th style="padding-left: 30px !important; text-align: left;">Grund</th>\n' +
            '\t\t\t\t<th style="width: 80px; text-align: center;">Menge</th>\n' +
            '\t\t\t</tr>\n';

        let pList = this._positions;
        for (let ean in pList){
            let aSelectId = pList[ean].level + '-' + removeAllJingHao(pList[ean].fullPath) + '-reason';
            reason_select_ids.push(aSelectId);
            htmlTxt += '\t\t\t<tr>\n' +
                '\t\t\t\t<td align="center">' + pList[ean].level + ' (aus Pos. ' + pList[ean].positionNr + ')</td>\n' +
                '\t\t\t\t<td align="center">' + pList[ean].ean + '</td>\n' +
                '\t\t\t\t<td style="padding-left: 30px !important; padding-right: 30px !important; text-align: left;">' + pList[ean].title + '</td>\n' +
                '\t\t\t\t<td style="width: 300px; padding-left: 30px !important; padding-right: 30px !important; text-align: left;">' + '<select id="'+aSelectId+'" multiple="multiple" style="width: 100%; height: 22px;">' + htmlTxt_reason_options + '</select>' + '</td>\n';

            let optQuantity = parseInt(pList[ean].quantity);
            if(optQuantity > 0){

                /**
                 * 如果库存太多了，例如上万，那么只显示 50 个库存
                 */
                if(optQuantity > 50){
                    optQuantity = 50;
                }

                htmlTxt += '\t\t\t\t<td align="center">' + '<select id="' + pList[ean].level + '-' + removeAllJingHao(pList[ean].fullPath) + '-menge" style="width: 100%; height: 22px; color: #333333 !important;">';
                for(let qi = 0; qi < optQuantity; ++qi){
                    htmlTxt += '<option value="' + (qi+1) + '">' + (qi+1) + '</option>';
                }
                htmlTxt += '</select>' + '</td>\n';
            }else{
                htmlTxt += '\t\t\t\t<td align="center">NAL</td>\n';
            }

            htmlTxt += '\t\t\t</tr>\n';
        }

        htmlTxt += '\t\t</tbody>\n' +
            '\t</table>\n' +
            '</div>';

        scDiv.html(htmlTxt);

        /**
         * 初始化 Multiple Select
         */
        for(let i_rs = 0; i_rs < reason_select_ids.length; ++i_rs){
            $(function() {
                $('#' + reason_select_ids[i_rs]).multipleSelect();
            })
        }

    }

}

function removeAllJingHao(str) {
    return str.replace(/#/g,'');
}

function filterLagebestandReal() {
    let params = {};
    let rid_arr = $('#lg-rgl-sel').val();
    for(let i = 0; i < rid_arr.length; ++i){
        if(i === 0){
            params.rid = rid_arr[i];
        }else{
            params.rid += ',';
            params.rid += rid_arr[i];
        }
    }
    params.cat = $('#lg-cat-sel').val();
    showRealLagerbestandFullTable(true, params);
}

function showRealLagerbestandFullTable(isFilter, params) {
    if(isFilter){
        showLoadingLayer();
    }else{
        params.rid = 'all';
        params.cat = 'all';
    }
    $.ajax({
        url:'/api/getregallagerbestand',
        data: {
            rid : params.rid,
            cat : params.cat
        },
        dataType: "json",
        type: "POST",
        traditional: true,
        success: function (data) {
            if(data.isSuccess){
                let list = data.datas;
                let shelf_data = list.shelf_data;
                let htmlTxt = '<div class="box">\n' +
                    '            <div class="box-header">\n' +
                    '              <h3 class="box-title">Daten wurde am <b>' + list.date + '</b> um <b>06:00</b> Uhr aktualisiert.<br/><a href="/wp-content/uploads/export/RegalLagerbestand.csv" style="font-size: 16px; line-height: 26px; font-style:italic;"><i class="fa fa-fw fa-download"></i>&nbsp;&nbsp;Daten als CSV Datei herunterladen</a></h3>\n' +
                    '            </div>\n' +
                    '            <!-- /.box-header -->\n' +
                    '            <div class="box-body table-responsive no-padding">\n' +
                    '              <table class="table table-hover">\n' +
                    '                <tr style="background-color: #e2eef5;">\n' +
                    '                  <th class="center">Regal-ID</th>\n' +
                    '                  <th>EAN</th>\n' +
                    '                  <th>Name</th>\n' +
                    '                  <th class="center">Kategorie</th>\n' +
                    '                  <th class="center">Wirkliche-Menge</th>\n' +
                    '                </tr>\n';

                for (let itm in shelf_data){
                    htmlTxt += '                <tr>\n' +
                        '                  <td class="center">' + shelf_data[itm].shelf_id + '</td>\n' +
                        '                  <td>' + shelf_data[itm].ean_elm + '</td>\n' +
                        '                  <td>' + shelf_data[itm].name + '</td>\n' +
                        '                  <td class="center">' + shelf_data[itm].category + '</td>\n' +
                        '                  <td class="center">' + shelf_data[itm].real_quantity + '</td>\n' +
                        '                </tr>\n';
                }

                htmlTxt += '              </table>\n' +
                    '            </div>\n' +
                    '            <!-- /.box-body -->\n' +
                    '          </div>';
                $('#data-wrap').html(htmlTxt);


                /**
                 * 创建Filter功能区
                 */
                let shelf_id_array = list.shelf_id_array;
                let category_array = list.category_array;
                htmlTxt = '<div class="box">\n' +
                    '            <div class="box-header">\n' +
                    '              <h3 class="box-title">Filter</h3>\n' +
                    '            </div>\n' +
                    '            <!-- /.box-header -->\n' +
                    '            <div class="box-body">\n' +
                    '              <div class="padding-20" style="padding-top: 10px !important;">' +

                    // Regal select
                    '<div id="lg-rgl-sel-wrap" class="col-md-4">' +
                    '                <select id="lg-rgl-sel" multiple="multiple" style="border-radius: 3px;" onchange="activeButton(\'btn-filter-lagebestand-real\');" onkeyup="activeButton(\'btn-filter-lagebestand-real\');">\n';
                for (let si_itm in shelf_id_array){
                    if(params.rid.indexOf(shelf_id_array[si_itm]) > -1 || !isFilter){
                        htmlTxt += '<option value="' + shelf_id_array[si_itm] + '" selected>' + shelf_id_array[si_itm] + '</option>\n';
                    }else{
                        htmlTxt += '<option value="' + shelf_id_array[si_itm] + '">' + shelf_id_array[si_itm] + '</option>\n';
                    }
                }
                htmlTxt += '                </select>' +
                    '</div>' +

                    // Kategorie select
                    '<div class="col-md-4">' +
                    '                <select id="lg-cat-sel" class="form-control" style="border-radius: 3px;" onchange="activeButton(\'btn-filter-lagebestand-real\');" onkeyup="activeButton(\'btn-filter-lagebestand-real\');">\n' +
                    '                  <option value="all">Alle Kategorien</option>\n';
                for (let ca_itm in category_array){
                    if(params.cat === category_array[ca_itm]){
                        htmlTxt += '<option value="' + category_array[ca_itm] + '" selected>' + category_array[ca_itm] + '</option>\n';
                    }else{
                        htmlTxt += '<option value="' + category_array[ca_itm] + '">' + category_array[ca_itm] + '</option>\n';
                    }
                }
                htmlTxt += '                </select>' +
                    '</div>' +

                    // Button
                    '<div class="col-md-4">' +
                    '<button id="btn-filter-lagebestand-real" type="button" class="btn btn-primary" onclick="filterLagebestandReal();">Suchen</button>' +
                    '</div>' +


                    '<div class="clear"></div>' +

                    '              </div>' +
                    '            </div>\n' +
                    '            <!-- /.box-body -->\n' +
                    '          </div>';
                $('#lagerbestand-real-filter-wrap').html(htmlTxt);


                /**
                 * 初始化 Multiple Select
                 */
                $('#lg-rgl-sel').multipleSelect();

                deactiveButton('btn-filter-lagebestand-real');

                //customAlert("", 1, data.msg);
            }else{
                customAlert("Lagerbestand Real Fehler: ID-10053", 2, data.msg);
            }
            if(isFilter){
                removeLoadingLayer();
            }
        }
    });
}

function activeButton(id){
    $('#' + id).removeAttr('disabled');
}
function deactiveButton(id){
    $('#' + id).attr("disabled","disabled");
}

function getAfterbuyKontoByOrderID(id) {
    let strLen = id.length;
    let konto = '';
    switch (strLen){
        case 10:
            konto = 'sogood';
            break;
        case 9:
            konto = 'maimai';
            break;
        default:
        //
    }
    return konto;
}

function autoSelectAfterbuyAccount() {
    let konto = getAfterbuyKontoByOrderID($('#order-id').val());
    if(konto !== ''){
        $("#ab-konto").val(konto);
    }
}

function ersatzteilCreatePageClear() {
    $('#customer-info-wrap').html('');
    $('#ersatzteile-select-wrap').html('');
    $('#shopping-cart-div').html('');
    $('#order-comment').val('');

    $('#block-customer-info').css('display', 'none');
    $('#block-ersatzteil-info').css('display', 'none');
    $('#shopping-cart-wrap').css('display', 'none');
    $('#order-comment-wrap').css('display', 'none');
    $('#create-ersatzteil-btn').css('display', 'none');
}

function initErsatzteilReasontartSMonthSelect(thisYear, thisMonth) {
    let howManyMonth = 12;
    let selectedYear = parseInt($('#ersatzteil-reason-start-year').val());
    if(selectedYear === parseInt(thisYear)){
        howManyMonth = parseInt(thisMonth);
    }

    let htmlTxt = '<select id="ersatzteil-reason-start-month" title="" class="padding-l-10 padding-r-10" style="height: 30px;">';
    for(let i = 1; i <= howManyMonth; ++i){
        let selectedTagM = '';
        if(selectedYear === parseInt(thisYear) && i === thisMonth){
            selectedTagM = 'selected';
        }
        htmlTxt += '<option value="' + i + '" ' + selectedTagM + '>' + getMonthTitle(i) + '</option>';
    }
    htmlTxt += '</select>';

    $('#ersatzteil-reason-start-month-div').html(htmlTxt);
}


function getMonthWidgetHtml (dateKey, data, reasons)
{

    let htmlTxt = '<div class="col-md-6 month-widget-wrap">';

    htmlTxt += '<div class="box box-widget widget-user-2">' +
            '<div class="widget-user-header bg-aqua-active">' +
                '<div class="fLeft" style="margin-left: 20px;">' +
                    '<img class="img-circle" style="max-width: 60% !important;" src="/wp-content/uploads/images/statistics_widget.jpg" alt="User Avatar">' +
                '</div>' +
                '<div class="fLeft" style="margin-left: -20px;">' +
                        '<div class="widget-user-username" style="margin-left: 0 !important; margin-top: 7px;"><b>' + getMonthTitle(parseInt(dateKey.substring(5, 7))) + '&nbsp;' + dateKey.substring(0, 4) + '</b>&nbsp;&nbsp;&nbsp;&nbsp;<a href="/data-analytics/ersatzteil-reason-details/?year=' + dateKey.substring(0,4) + '&month=' + parseInt(dateKey.substring(5,7)) + '" target="_blank" class="btn btn-default btn-flat" style="color: #222d32; padding: 10px 20px;">&nbsp;<i class="fa fa-sign-out"></i>&nbsp;&nbsp;<span>Details zeigen</span></a></div>' +
                        '<div class="widget-user-desc" style="margin-left: 0 !important;">WARUM UND WIE VIEL</div>' +
                '</div>' +
                '<div class="clear"></div>' +
            '</div>' +
            '<div class="box-footer no-padding">' +
                '<ul class="nav nav-stacked">';

    /**
     * 列出所有的原因
     * @type {string}
     * reasons[val].meta_id 1,2,3...
     */
    //console.log(data);
    for(let val in reasons){

        let ersatzteileQuantity = 0;
        let quantityTagColor = '';

        if(data !== undefined){

            let reasonId = reasons[val].meta_id;
            for(let index in data){
                if(isValueInList(reasonId, data[index].reasons.split(','))){
                    ersatzteileQuantity += parseInt(data[index].quantity);
                }
            }

            if(ersatzteileQuantity > 0){
                quantityTagColor = 'bg-red';
            }else {
                quantityTagColor = 'bg-green';
            }

        }

        htmlTxt += '<li><a href="/data-analytics/ersatzteil-reason-details/?year=' + dateKey.substring(0, 4) + '&month=' + parseInt(dateKey.substring(5, 7)) + '" target="_blank">' + reasons[val].reason + ' <span class="pull-right badge ' + quantityTagColor + '">' + ersatzteileQuantity + '</span></a></li>';

    }

        htmlTxt += '</ul>' +
            '</div>' +
        '</div>';

    htmlTxt += '</div>';

    return htmlTxt;

}

class ErsatzteileReasonsManager
{
    constructor()
    {
        if(!ErsatzteileReasonsManager.instance){
            ErsatzteileReasonsManager.instance = this;
        }
        return ErsatzteileReasonsManager.instance;
    }

    exportCSV (page = '')
    {
        switch (page)
        {
            case 'data-export-1':
                showLoadingLayer();
                $.ajax({
                    url:'/api/export-csv-ersatzteilreason',
                    data: {
                        data_format : 'csv'
                    },
                    dataType: "json",
                    type: "POST",
                    traditional: true,
                    success: function (data) {
                        if(data.isSuccess){

                            let htmlTxt = '<b>CSV Datei wurde erfolgreich erstellt:</b><br/>';
                            htmlTxt += '<a href="/wp-content/uploads/export/' + data.datas.csv_info.name + data.datas.csv_info.extension + '"><i class="fa fa-download"></i>&nbsp;&nbsp;' + data.datas.csv_info.name + data.datas.csv_info.extension + '</a>';
                            $('#ersatzteileReasonsCSVLink').html(htmlTxt);
                            $('#ersatzteileReasonsCSVLink').css('display', 'block');

                        }else{
                            customAlert("Ersatzteil Gründe Exportieren Fehler: ID-10055", 2, data.msg);
                        }
                        removeLoadingLayer();
                    }
                });
                break;
            case 'data-analytics-1':

                $('#ersatzteil-reason-wrap').html('<img src="/wp-content/uploads/images/loading-spinning-circles.svg" />');

                let startYear = $('#ersatzteil-reason-start-year').val();
                let startMonth = $('#ersatzteil-reason-start-month').val();
                $.ajax({
                    url:'/api/export-csv-ersatzteilreason',
                    data: {
                        data_format : 'json',
                        start_year : startYear,
                        start_month : startMonth
                    },
                    dataType: "json",
                    type: "POST",
                    traditional: true,
                    success: function (data) {
                        if(data.isSuccess){

                            let dateKeys = data.datas.date_keys;
                            let reasons = data.datas.reasons;
                            let positions = data.datas.positions;

                            let htmlTxt = '<div class="row">';

                            for(let i = 0; i < dateKeys.length; ++i){
                                htmlTxt += getMonthWidgetHtml(dateKeys[i], positions[dateKeys[i]], reasons);
                            }

                            htmlTxt += '</div>';

                            $('#ersatzteil-reason-wrap').html(htmlTxt);

                        }else{
                            $('#ersatzteil-reason-wrap').html('');
                            customAlert("Ersatzteil Gründe Exportieren Fehler: ID-10055", 2, data.msg);
                        }
                    }
                });
                break;
            case 'ersatzteil-reason-details-1':
                let showYear = $('#show-year-div').html();
                let showMonth = $('#show-month-div').html();
                $.ajax({
                    url:'/api/export-csv-ersatzteilreason',
                    data: {
                        data_format : 'json',
                        show_year : showYear,
                        show_month : showMonth
                    },
                    dataType: "json",
                    type: "POST",
                    traditional: true,
                    success: function (data) {
                        if(data.isSuccess){

                            let dateKeys = data.datas.date_keys;
                            let reasons = data.datas.reasons;
                            let positions = data.datas.positions;

                            //console.log(positions[dateKeys]);
                            /**
                             * 从 positions[dateKeys] 里面整理出 需要的数据, 分别放到对应的原因里面
                             * 每个原因是一个key：reason1,reason2...
                             * @type {string}
                             */
                            let problemProductList = {};
                            for(let p_i in positions[dateKeys]){

                                let aPosition = positions[dateKeys][p_i];
                                let aReasons = aPosition.reasons.split(',');

                                let aProblemProduct = {};
                                aProblemProduct.ean_product = '';
                                aProblemProduct.name_product = '';
                                if(aPosition.mapping !== ''){
                                    aProblemProduct.ean_product = aPosition.mapping.substring(0, 13);
                                    aProblemProduct.name_product = getProductTitleByEAN(aProblemProduct.ean_product);
                                }
                                aProblemProduct.ean_ersatzteil = aPosition.ean;
                                aProblemProduct.name_ersatzteil = aPosition.name;
                                aProblemProduct.quantity = aPosition.quantity;

                                for(let ar_i in aReasons){
                                    //console.log(aReasons[ar_i]);
                                    if(!problemProductList.hasOwnProperty('reason' + aReasons[ar_i])){
                                        problemProductList['reason' + aReasons[ar_i]] = [];
                                    }
                                    problemProductList['reason' + aReasons[ar_i]].push(aProblemProduct);
                                }

                            }

                            //console.log(problemProductList);

                            /**
                             * 把 problemProductList 中同样的产品都累加起来，数量求和。
                             * @type {string}
                             */
                            let problemProductListFinal = {};
                            for(let key in problemProductList){

                                if(!problemProductListFinal.hasOwnProperty(key)) {
                                    problemProductListFinal[key] = {};
                                }

                                let problemProductsInAReason = problemProductList[key];

                                for(let key_i in problemProductsInAReason){

                                    let eanProduct = problemProductsInAReason[key_i].ean_product;

                                    if(!problemProductListFinal[key].hasOwnProperty(eanProduct)) {

                                        problemProductListFinal[key][eanProduct] = {};
                                        problemProductListFinal[key][eanProduct].ean_product = eanProduct;
                                        problemProductListFinal[key][eanProduct].quantity = problemProductsInAReason[key_i].quantity;
                                        problemProductListFinal[key][eanProduct].name_product = problemProductsInAReason[key_i].name_product;

                                        problemProductListFinal[key][eanProduct].ersatzteile = [];
                                        problemProductListFinal[key][eanProduct].ersatzteile.push({'ean' : problemProductsInAReason[key_i].ean_ersatzteil, 'name' : problemProductsInAReason[key_i].name_ersatzteil });

                                    }else{

                                        problemProductListFinal[key][eanProduct].quantity = parseInt(problemProductListFinal[key][eanProduct].quantity) + parseInt(problemProductsInAReason[key_i].quantity);

                                        let isInsertEst = true;
                                        for(let key_est in problemProductListFinal[key][eanProduct].ersatzteile){
                                            //console.log(problemProductListFinal[key][eanProduct].ersatzteile[key_est]);
                                            if(problemProductListFinal[key][eanProduct].ersatzteile[key_est].ean === problemProductsInAReason[key_i].ean_ersatzteil){
                                                isInsertEst = false;
                                            }
                                        }
                                        if(isInsertEst){
                                            problemProductListFinal[key][eanProduct].ersatzteile.push({'ean' : problemProductsInAReason[key_i].ean_ersatzteil, 'name' : problemProductsInAReason[key_i].name_ersatzteil});
                                        }

                                    }

                                }

                            }

                            //console.log(problemProductListFinal);


                            let quantityInAReasonListJson = {};
                            let htmlTxt = '';

                            for(let i in reasons){

                                let problemProductListFinalForAReason = problemProductListFinal['reason' + reasons[i].meta_id];


                                /**
                                 * 计算一个原因里面的 ersatzteil 的个数
                                 */
                                let quantityInAReason = 0;
                                for(let pp_j in problemProductListFinalForAReason){
                                    quantityInAReason += parseInt(problemProductListFinalForAReason[pp_j].quantity);
                                }
                                quantityInAReasonListJson['reason' + reasons[i].meta_id] = {};
                                quantityInAReasonListJson['reason' + reasons[i].meta_id].quantity = quantityInAReason;


                                if(problemProductListFinalForAReason === undefined){
                                    htmlTxt += '<div id="reason-box-' + reasons[i].meta_id + '" class="box ersatzteil-reason-box">\n' +
                                        '\t<div class="box-header">\n' +
                                        '\t\t<h3 class="box-title">[&nbsp;Grund&nbsp;' + reasons[i].meta_id + '&nbsp;]&nbsp;[&nbsp;Anteil:&nbsp;<span id="reason'+reasons[i].meta_id+'-percent-val" style="font-weight: bold;"><img src="/wp-content/uploads/images/loading-spin-1s-200px.svg" style="height: 16px !important;" /></span><b>%</b>&nbsp;(0)&nbsp;]&nbsp;&nbsp;' + reasons[i].reason + '</h3>\n' +
                                        '\t</div>\n' +
                                        '\t<div class="box-body no-padding">\n' +
                                        '\t\t<table class="table table-striped">\n' +
                                        '\t\t\t<tr>\n' +
                                        '\t\t\t\t<th>Kein Produkt hier.</th>\n' +
                                        '\t\t\t</tr>\n' +
                                        '\t\t</table>\n' +
                                        '\t</div>\n' +
                                        '</div>';
                                }else{
                                    htmlTxt += '<div id="reason-box-' + reasons[i].meta_id + '" class="box ersatzteil-reason-box">\n' +
                                        '\t<div class="box-header">\n' +
                                        '\t\t<h3 class="box-title">[&nbsp;Grund&nbsp;' + reasons[i].meta_id + '&nbsp;]&nbsp;[&nbsp;Anteil:&nbsp;<span id="reason'+reasons[i].meta_id+'-percent-val" style="font-weight: bold;"><img src="/wp-content/uploads/images/loading-spin-1s-200px.svg" style="height: 16px !important;" /></span><b>%</b>&nbsp;(' + quantityInAReason + ')&nbsp;]&nbsp;&nbsp;' + reasons[i].reason + '</h3>\n' +
                                        '\t</div>\n' +
                                        '\t<div class="box-body no-padding">\n' +
                                        '\t\t<table class="table table-striped">\n' +
                                        '\t\t\t<tr>\n' +
                                        '\t\t\t\t<th style="width: 50px">#</th>\n' +
                                        '\t\t\t\t<th style="width: 120px">EAN</th>\n' +
                                        '\t\t\t\t<th style="width: 80px; text-align: center;">Menge</th>\n' +
                                        '\t\t\t\t<th style="padding-left: 30px;">Produkt</th>\n' +
                                        '\t\t\t</tr>\n';

                                    let nr = 1;
                                    for(let pp_i in problemProductListFinalForAReason){
                                        htmlTxt += '\t\t\t<tr>\n' +
                                            '\t\t\t\t<td>' + nr + '.</td>\n' +
                                            '\t\t\t\t<td>' + problemProductListFinalForAReason[pp_i].ean_product + '</td>\n' +
                                            '\t\t\t\t<td align="center"><span class="badge bg-red">' + problemProductListFinalForAReason[pp_i].quantity + '</span></td>\n' +
                                            '\t\t\t\t<td style="padding-left: 30px;">' + problemProductListFinalForAReason[pp_i].name_product + '</td>\n' +
                                            '\t\t\t</tr>\n';
                                        nr++;
                                    }

                                    htmlTxt += '\t\t</table>\n' +
                                        '\t</div>\n' +
                                        '</div>';

                                }


                            }

                            $('#result-wrap').html(htmlTxt);


                            /**
                             * 计算每个原因的百分比，并且写入HTML
                             */
                            let quantityTatal = 0;
                            for(let qirj_i in quantityInAReasonListJson){
                                let q = quantityInAReasonListJson[qirj_i].quantity;
                                quantityTatal += parseInt(q);
                            }
                            for(let qirj_j in quantityInAReasonListJson){
                                let q = quantityInAReasonListJson[qirj_j].quantity;
                                let per = q * 100 / quantityTatal;
                                per = Math.round(per * 10) / 10;
                                $('#' + qirj_j + '-percent-val').html(per);
                            }


                        }else{
                            $('#ersatzteil-reason-wrap').html('');
                            customAlert("Ersatzteil Gründe Exportieren Fehler: ID-10055", 2, data.msg);
                        }
                    }
                });
                break;
            default:
                customAlert("Ersatzteil Gründe Exportieren Fehler: ID-10054", 2, 'Parameter [page] nicht gefunden.');
                removeLoadingLayer();
        }
    }
}
let ersatzteileReasonsManager = new ErsatzteileReasonsManager();
