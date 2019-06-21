<?php
/* Template Name: Idealhit Attachment Page */
/**
 * Created by PhpStorm.
 * User: ywang
 * Date: 2018/3/19
 * Time: 17:07
 */

error_reporting(E_ALL | E_STRICT);
//require( __DIR__ . '\..\..\..\..\wp-includes\lib\jquery-file-upload-9.21.0\server\php\UploadHandler.php');

$location_jquery_file_upload = "/wp-includes/lib/jquery-file-upload-9.21.0/";

$url = home_url(add_query_arg(array()));
$url_list = explode("/attachment/", $url);
$id = substr($url_list[1], 0, 7);
$id_db = intval($id) - 3000000;

$db_table_orders = "ihattach_orders";

/**
 * 检查当前用户是否有权利删除文件
 */
$canYouDeleteFiles = false;
global $wpdb;
global $current_user;
get_current_user();
// 主表信息查询
$query = "SELECT * FROM `" . $db_table_orders . "` WHERE `meta_id` = %d";
$order_main_infos_db = $wpdb->get_results($wpdb->prepare($query, $id_db));
if(COUNT($order_main_infos_db) > 0){
    $order_main_infos = $order_main_infos_db[0];
    if($order_main_infos->create_by == $current_user->ID){
        $canYouDeleteFiles = true;
    }
}


/**
 * Session
 * 上传文件的PHP会读这个变量
 */
session_start();
$_SESSION["order-id-goodone"] = $id;


if(strlen($url_list[1]) > 10){

    //重定向浏览器
    header("Location: /404/");
    //确保重定向后，后续代码不会被执行
    exit;

}else{

    //$upload_handler = new UploadHandler(null, true, null, '200156');

    ?>

    <!DOCTYPE html>
    <html>
        <head>
            <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
            <meta charset="utf-8">
            <title>Anhang</title>
            <!-- Bootstrap styles -->
            <link rel="stylesheet" href="<?=$location_jquery_file_upload;?>css/bootstrap.min.css">
            <!-- Generic page styles -->
            <link rel="stylesheet" href="<?=$location_jquery_file_upload;?>css/style.css">
            <!-- blueimp Gallery styles -->
            <link rel="stylesheet" href="<?=$location_jquery_file_upload;?>css/blueimp-gallery.min.css">
            <!-- CSS to style the file input field as button and adjust the Bootstrap progress bars -->
            <link rel="stylesheet" href="<?=$location_jquery_file_upload;?>css/jquery.fileupload.css">
            <link rel="stylesheet" href="<?=$location_jquery_file_upload;?>css/jquery.fileupload-ui.css">
            <!-- CSS adjustments for browsers with JavaScript disabled -->
            <noscript><link rel="stylesheet" href="<?=$location_jquery_file_upload;?>css/jquery.fileupload-noscript.css"></noscript>
            <noscript><link rel="stylesheet" href="<?=$location_jquery_file_upload;?>css/jquery.fileupload-ui-noscript.css"></noscript>
            <style>
                /* Hide Angular JS elements before initializing */
                img {display:block;}
                .ng-cloak {
                    display: none;
                }
                #attachment-wrap { width: 94%; margin: 40px auto; }
                .progress { margin-bottom: 0 !important; }
                .btn-file-upload { margin-bottom: 5px; }
            </style>
        </head>
        <body>
        <div id="attachment-wrap">

            <form id="fileupload" action="https://jquery-file-upload.appspot.com/" method="POST" enctype="multipart/form-data" data-ng-app="demo" data-ng-controller="DemoFileUploadController" data-file-upload="options" data-ng-class="{'fileupload-processing': processing() || loadingFiles}">
                <!-- Redirect browsers with JavaScript disabled to the origin page -->
                <noscript><input type="hidden" name="redirect" value="https://blueimp.github.io/jQuery-File-Upload/"></noscript>
                <!-- The fileupload-buttonbar contains buttons to add/delete files and start/cancel the upload -->
                <div class="row fileupload-buttonbar">
                    <div class="col-lg-7">
                        <!-- The fileinput-button span is used to style the file input field as button -->
                        <span class="btn btn-success fileinput-button" ng-class="{disabled: disabled}">
                    <i class="glyphicon glyphicon-plus"></i>
                    <span>Datei auswählen</span>
                    <input type="file" name="files[]" multiple ng-disabled="disabled">
                </span>
                        <button type="button" class="btn btn-primary start" data-ng-click="submit()">
                            <i class="glyphicon glyphicon-upload"></i>
                            <span>Alle Dateien hochladen</span>
                        </button>
                        <button type="button" class="btn btn-warning cancel" data-ng-click="cancel()">
                            <i class="glyphicon glyphicon-ban-circle"></i>
                            <span>Alle Dateien stornieren</span>
                        </button>
                        <!-- The global file processing state -->
                        <span class="fileupload-process"></span>
                    </div>
                    <!-- The global progress state -->
                    <div class="col-lg-5 fade" data-ng-class="{in: active()}">
                        <!-- The global progress bar -->
                        <div class="progress progress-striped active" data-file-upload-progress="progress()"><div class="progress-bar progress-bar-success" data-ng-style="{width: num + '%'}"></div></div>
                        <!-- The extended global progress state -->
                        <div class="progress-extended">&nbsp;</div>
                    </div>
                </div>
                <!-- The table listing the files available for upload/download -->
                <table class="table table-striped files ng-cloak">
                    <tr data-ng-repeat="file in queue" data-ng-class="{'processing': file.$processing()}">

                        <!--
                        <td data-ng-switch data-on="!!file.thumbnailUrl">
                            <div class="preview" data-ng-switch-when="true">
                                <a href="{{file.url}}" target="_blank"><img data-ng-src="{{file.thumbnailUrl}}" alt="{{file.name}}"></a>
                            </div>
                            <div class="preview" data-ng-switch-when="false">
                                <a href="{{file.url}}" target="_blank"><img data-ng-src="/wp-content/uploads/images/icon-file.png" alt="{{file.name}}"></a>
                            </div>
                            <div class="preview" data-ng-switch-default data-file-upload-preview="file"></div>
                        </td>
                        -->
                        <td data-on="!!file.thumbnailUrl">
                            <div class="preview" ng-if="!!file.thumbnailUrl && !!file.url">
                                <a href="{{file.url}}" target="_blank"><img data-ng-src="{{file.thumbnailUrl}}" alt="{{file.name}}"></a>
                            </div>
                            <div class="preview" ng-if="!file.thumbnailUrl && !!file.url">
                                <a href="{{file.url}}" target="_blank"><img data-ng-src="/wp-content/uploads/images/icon-file.png" alt="{{file.name}}"></a>
                            </div>
                        </td>

                        <td style="max-width: 400px; word-break: break-all;">
                            <p class="name" data-ng-switch data-on="!!file.url">
                            <span data-ng-switch-when="true" data-ng-switch data-on="!!file.thumbnailUrl">
                                <!--
                                <a data-ng-switch-when="true" data-ng-href="{{file.url}}" title="{{file.name}}" download="{{file.name}}" data-gallery>{{file.name}}</a>
                                <a data-ng-switch-default data-ng-href="{{file.url}}" title="{{file.name}}" download="{{file.name}}">{{file.name}}</a>
                                -->
                                <a href="{{file.url}}" title="{{file.name}}" target="_blank">{{file.name}}</a>
                            </span>
                            <span data-ng-switch-default>{{file.name}}</span>
                            </p>
                            <strong data-ng-show="file.error" class="error text-danger">{{file.error}}</strong>
                        </td>

                        <td>
                            <p class="size">{{file.size | formatFileSize}}</p>
                            <div class="progress progress-striped active fade" data-ng-class="{pending: 'in'}[file.$state()]" data-file-upload-progress="file.$progress()"><div class="progress-bar progress-bar-success" data-ng-style="{width: num + '%'}"></div></div>
                        </td>

                        <td>
                            <button name="{{file.name}}" type="button" class="btn btn-primary start btn-file-upload" onclick="setOperationHistory(<?=$id_db?>, 'Die Datei [ ' + this.name + ' ] wurde als Anhang hochgeladen.', 1, 0);" data-ng-click="file.$submit()" data-ng-hide="!file.$submit || options.autoUpload" data-ng-disabled="file.$state() == 'pending' || file.$state() == 'rejected'">
                                <i class="glyphicon glyphicon-upload"></i>
                                <span>Hochladen</span>
                            </button>
                            <button type="button" class="btn btn-warning cancel btn-file-upload" data-ng-click="file.$cancel()" data-ng-hide="!file.$cancel">
                                <i class="glyphicon glyphicon-ban-circle"></i>
                                <span>Stornieren</span>
                            </button>
    <?php
    //echo $canYouDeleteFiles;
    if($canYouDeleteFiles){
    ?>
                            <button name="{{file.name}}" data-ng-controller="FileDestroyController" type="button" class="btn btn-danger destroy btn-file-upload" onclick="setOperationHistory(<?=$id_db?>, 'Die Datei [ ' + this.name + ' ] im Anhang wurde entfernt.', 1, 0);" data-ng-click="file.$destroy()" data-ng-hide="!file.$destroy">
                                <i class="glyphicon glyphicon-trash"></i>
                                <span>Entfernen</span>
                            </button>
    <?php
    }
    ?>
                        </td>

                    </tr>
                </table>
            </form>

        </div>

        <script src="<?=$location_jquery_file_upload;?>js/jquery.min.js"></script>
        <script src="<?=$location_jquery_file_upload;?>js/angular.min.js"></script>
        <!-- The jQuery UI widget factory, can be omitted if jQuery UI is already included -->
        <script src="<?=$location_jquery_file_upload;?>js/vendor/jquery.ui.widget.js"></script>
        <!-- The Load Image plugin is included for the preview images and image resizing functionality -->
        <script src="<?=$location_jquery_file_upload;?>js/load-image.all.min.js"></script>
        <!-- The Canvas to Blob plugin is included for image resizing functionality -->
        <script src="<?=$location_jquery_file_upload;?>js/canvas-to-blob.min.js"></script>
        <!-- Bootstrap JS is not required, but included for the responsive demo navigation -->
        <script src="<?=$location_jquery_file_upload;?>js/bootstrap.min.js"></script>
        <!-- blueimp Gallery script -->
        <script src="<?=$location_jquery_file_upload;?>js/jquery.blueimp-gallery.min.js"></script>
        <!-- The Iframe Transport is required for browsers without support for XHR file uploads -->
        <script src="<?=$location_jquery_file_upload;?>js/jquery.iframe-transport.js"></script>
        <!-- The basic File Upload plugin -->
        <script src="<?=$location_jquery_file_upload;?>js/jquery.fileupload.js"></script>
        <!-- The File Upload processing plugin -->
        <script src="<?=$location_jquery_file_upload;?>js/jquery.fileupload-process.js"></script>
        <!-- The File Upload image preview & resize plugin -->
        <script src="<?=$location_jquery_file_upload;?>js/jquery.fileupload-image.js"></script>
        <!-- The File Upload audio preview plugin -->
        <script src="<?=$location_jquery_file_upload;?>js/jquery.fileupload-audio.js"></script>
        <!-- The File Upload video preview plugin -->
        <script src="<?=$location_jquery_file_upload;?>js/jquery.fileupload-video.js"></script>
        <!-- The File Upload validation plugin -->
        <script src="<?=$location_jquery_file_upload;?>js/jquery.fileupload-validate.js"></script>
        <!-- The File Upload Angular JS module -->
        <script src="<?=$location_jquery_file_upload;?>js/jquery.fileupload-angular.js"></script>
        <!-- The main application script -->
        <script src="<?=$location_jquery_file_upload;?>js/app.js"></script>

        <script>
            /**
             * 保存操作记录
             * @param id (订单 报价 id)
             * @param msg ( "" )
             * @param typ ( 0 order 1 quote ... )
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
        </script>

        </body>
    </html>


    <?php
}