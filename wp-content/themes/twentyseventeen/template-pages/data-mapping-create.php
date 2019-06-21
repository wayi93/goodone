<?php
/* Template Name: Idealhit Datenmapping Bearbeiten Page */
/**
 * Created by PhpStorm.
 * User: ywang
 * Date: 2018/3/19
 * Time: 17:07
 */
get_header();

global $current_user;
get_current_user();
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Datenmapping Bearbeiten
            <small>Herzlich willkommen bei GoodOne Rechnungsplattform</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="/"><i class="fa fa-dashboard"></i>&nbsp;Home</a></li>
            <li>Ersatzteil</li>
            <li class="active">Datenmapping Bearbeiten</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">

        <!--------------------------
          | Your Page Content Here |
          -------------------------->

        <div class="row">

            <div id="cPanel" class="col-md-6">

                <!-- 重复模块 -->
                <div class="box box-primary" style="padding: 10px;">
                    <div class="box-header">

                        <div class="row">
                            <div class="col-md-4"><h3 class="box-title"><b>1</b> x Element</h3></div>
                            <div class="col-md-8"><h3 class="box-title"><b>n</b> x Ersatzteile</h3></div>
                        </div>

                    </div>
                    <div class="box-body box-profile">

                        <div class="row">
                            <div class="col-md-4">
                                <div class="margin-b-10">
                                    <input class="form-control" id="mapping-elm-ean" placeholder="Element EAN" onkeyup="disableTextArea('tags-wrap-001');" />
                                </div>
                                <div>
                                    <button id="btn-search-mappings-001" type="button" class="btn btn-primary" onclick="searchMappingsByEAN(1);">Mappings suchen</button>
                                </div>
                            </div>
                            <div id="tags-wrap-001-div" class="col-md-8">
                                <div class="margin-b-10">
                                    <textarea id="tags-wrap-001" class="width100per mapping-eans-wrap" placeholder=""></textarea>
                                </div>
                                <div>
                                    <button id="btn-save-mappings-001" type="button" class="btn btn-primary" onclick="saveMappings(1);" disabled>Speichern</button>
                                </div>
                            </div>
                        </div>

                        <div style="margin-top: 30px;">
                            <hr>
                        </div>

                        <div class="row" style="color: #46799b;">
                            <div class="col-md-12">
                                <div style="font-size: 16px;">1 x Element Details:</div>
                                <div id="mappings-001-elm-Details">
                                    N/A
                                </div>
                                <div style="font-size: 16px; padding-top: 10px;">n x Ersatzteile Details:</div>
                                <div id="mappings-001-est-Details">
                                    N/A
                                </div>
                            </div>
                        </div>

                    </div>

                </div>
                <!-- 重复模块 -->

            </div>

            <div id="cPanel" class="col-md-6">

                <!-- 重复模块 -->
                <div class="box box-primary" style="padding: 10px;">
                    <div class="box-header">

                        <div class="row">
                            <div class="col-md-8"><h3 class="box-title"><b>n</b> x Elements</h3></div>
                            <div class="col-md-4"><h3 class="box-title"><b>1</b> x Ersatzteil</h3></div>
                        </div>

                    </div>
                    <div class="box-body box-profile">

                        <div class="row">
                            <div id="tags-wrap-002-div" class="col-md-8">
                                <div class="margin-b-10">
                                    <textarea id="tags-wrap-002" class="width100per mapping-eans-wrap" placeholder=""></textarea>
                                </div>
                                <div>
                                    <button id="btn-save-mappings-002" type="button" class="btn btn-primary" onclick="saveMappings(2);" disabled>Speichern</button>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="margin-b-10">
                                    <input class="form-control" id="mapping-est-ean" placeholder="Ersatzteil EAN" onkeyup="disableTextArea('tags-wrap-002');" />
                                </div>
                                <div>
                                    <button id="btn-search-mappings-002" type="button" class="btn btn-primary" onclick="searchMappingsByEAN(2);">Mappings suchen</button>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div style="margin-top: 30px;">
                        <hr>
                    </div>

                    <div class="row" style="color: #46799b;">
                        <div class="col-md-12">
                            <div style="font-size: 16px;">n x Elements Details:</div>
                            <div id="mappings-002-elm-Details">
                                N/A
                            </div>
                            <div style="font-size: 16px; padding-top: 10px;">1 x Ersatzteil Details:</div>
                            <div id="mappings-002-est-Details">
                                N/A
                            </div>
                        </div>
                    </div>

                </div>
                <!-- 重复模块 -->

            </div>

        </div>

        <div style="height: 300px;">&nbsp;</div>


    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->



    <script>

        // jQuery UI autocomplete extension - suggest labels may contain HTML tags
        // github.com/scottgonzalez/jquery-ui-extensions/blob/master/src/autocomplete/jquery.ui.autocomplete.html.js
        (function($){var proto=$.ui.autocomplete.prototype,initSource=proto._initSource;function filter(array,term){var matcher=new RegExp($.ui.autocomplete.escapeRegex(term),"i");return $.grep(array,function(value){return matcher.test($("<div>").html(value.label||value.value||value).text());});}$.extend(proto,{_initSource:function(){if(this.options.html&&$.isArray(this.options.source)){this.source=function(request,response){response(filter(this.options.source,request.term));};}else{initSource.call(this);}},_renderItem:function(ul,item){return $("<li></li>").data("item.autocomplete",item).append($("<a></a>")[this.options.html?"html":"text"](item.label)).appendTo(ul);}});})(jQuery);

        var cache = {};
        function googleSuggest(request, response) {
            var term = request.term;
            if (term in cache) { response(cache[term]); return; }
            /*
            // Link 经常访问不到
            $.ajax({
                url: 'https://query.yahooapis.com/v1/public/yql',
                dataType: 'JSONP',
                data: { format: 'json', q: 'select * from xml where url="http://google.com/complete/search?output=toolbar&q='+term+'"' },
                success: function(data) {
                    var suggestions = [];
                    try { var results = data.query.results.toplevel.CompleteSuggestion; } catch(e) { var results = []; }
                    $.each(results, function() {
                        try {
                            var s = this.suggestion.data.toLowerCase();
                            suggestions.push({label: s.replace(term, '<b>'+term+'</b>'), value: s});
                        } catch(e){}
                    });
                    cache[term] = suggestions;
                    response(suggestions);
                }
            });
            */
        }

        $(function() {
            $('#tags-wrap-001').tagEditor({
                placeholder: 'Ersatzteile EANs eingeben ...',
                autocomplete: { source: googleSuggest, minLength: 3, delay: 250, html: true, position: { collision: 'flip' } },
                onChange: function(field, editor, tags){
                    $('#btn-save-mappings-001').removeAttr('disabled');

                    $('#mappings-001-elm-Details').html('N/A');
                    updateDetails(1, $('#mapping-elm-ean').val());
                }
            });
            $('#tags-wrap-002').tagEditor({
                placeholder: 'Elements EANs eingeben ...',
                autocomplete: { source: googleSuggest, minLength: 3, delay: 250, html: true, position: { collision: 'flip' } },
                onChange: function(field, editor, tags){
                    $('#btn-save-mappings-002').removeAttr('disabled');

                    $('#mappings-002-elm-Details').html('N/A');
                    updateDetails(2, $('#mapping-elm-ean').val());
                }
            });
        });

        if (~window.location.href.indexOf('http')) {
            (function() {var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;po.src = 'https://apis.google.com/js/plusone.js';var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);})();
            (function(d, s, id) {var js, fjs = d.getElementsByTagName(s)[0];if (d.getElementById(id)) return;js = d.createElement(s); js.id = id;js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.4&appId=114593902037957";fjs.parentNode.insertBefore(js, fjs);}(document, 'script', 'facebook-jssdk'));
            !function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');
            $('#github_social').html('\
                <iframe style="float:left;margin-right:15px" src="//ghbtns.com/github-btn.html?user=Pixabay&repo=jQuery-tagEditor&type=watch&count=true" allowtransparency="true" frameborder="0" scrolling="0" width="110" height="20"></iframe>\
                <iframe style="float:left;margin-right:15px" src="//ghbtns.com/github-btn.html?user=Pixabay&repo=jQuery-tagEditor&type=fork&count=true" allowtransparency="true" frameborder="0" scrolling="0" width="110" height="20"></iframe>\
            ');
        }

    </script>

<?php get_footer();