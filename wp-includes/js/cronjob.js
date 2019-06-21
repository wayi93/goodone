var cronjob_update_product_data = setInterval(function() {
    updateProductDataBackendWeak("BackendOnly");
}, (1000 * 60 * 60)); // 60 分钟