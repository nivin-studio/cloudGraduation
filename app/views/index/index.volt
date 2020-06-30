<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no" name="viewport" />
    <title>合肥学院云毕业照</title>
    <link rel="stylesheet" href="/css/ydui.css" />
    <link rel="stylesheet" href="/css/demo.css" />
</head>

<body>
    <noscript><strong>您的浏览器不支持JavaScript或版本过低，暂无法浏览此网页。</strong></noscript>
    <!-- page-welcome -->
    <section class="g-flexview page1">
        <div class="bg">
            <input id="file" type="file" accept="image/*" hidden />
            <button type="button" class="btn-block btn-uplaod"></button>
        </div>
    </section>
    <!-- page-welcome -->

    <!-- page-model -->
    <section class="g-flexview page2">
        <!-- page-model-content -->
        <section class="g-scrollview">
            <header class="m-navbar">
                <div class="navbar-center sl-model">
                </div>
            </header>
            <article class="m-list1 list-theme1">
                {% for key, val in models %}
                <a href="javascript:;" class="list-item">
                    <div class="list-card boder">
                        <div class="list-card-img">
                            <img src="/images/list_default.png" data-url="/images/{{ val }}" data-name="{{ key }}">
                        </div>
                    </div>
                </a>
                {% endfor %}
            </article>
        </section>
        <!-- page-model-content -->
    </section>
    <!-- page-model -->

    <!-- page-bg -->
    <section class="g-flexview page3">
        <!-- page-bg-content -->
        <section class="g-scrollview">
            <header class="m-navbar">
                <div class="navbar-center sl-bg">
                </div>
            </header>
            <article class="m-list2 list-theme1">
                {% for key, val in bgs %}
                <a href="javascript:;" class="list-item">
                    <div class="list-card boder">
                        <div class="list-card-img">
                            <img src="/images/list_default.png" data-url="/images/{{ val }}" data-name="{{ key }}">
                        </div>
                    </div>
                </a>
                {% endfor %}
            </article>
        </section>
        <!-- page-bg-content -->
    </section>
    <!-- page-bg -->

    <!-- page-show -->
    <section class="g-flexview page4">
        <div class="page-card">
            <div class="page-card-img boder">
                <img class="show-image" src="/images/list_default.png">
            </div>
            <div class="page-title">

            </div>
        </div>
        <div class="button-group">
            <button type="button" class="btn-block btn-reStart"></button>
            <button type="button" class="btn-block btn-reSelect"></button>
        </div>
        <canvas id="canvas" hidden></canvas>
    </section>
    <!-- page-show -->

    <script src="/js/jquery.min.js"></script>
    <script src="http://res.wx.qq.com/open/js/jweixin-1.6.0.js"></script>
    <script src="/js/ydui.js"></script>
    <script src="/js/ydui.flexible.js"></script>
    <script src="/js/resizeImg/lrz.bundle.js"></script>
    <script>
        var merge_base64 = '';
        var merge_model = 'boy1';
        var merge_bgimg = 'bg1';
        var tokenKey = '{{ tokenKey }}';
        var tokenVal = '{{ tokenVal }}';

        $(function () {
            $('.m-list2').find('img').lazyLoad({ binder: '.g-scrollview' });
            $('.m-list1').find('img').lazyLoad({ binder: '.g-scrollview' });
            selectPage(1);
        });

        $('.btn-uplaod').on('click', function () {
            $('#file').click();
        });

        $('.btn-reStart').on('click', function () {
            resetPage();
        });

        $('.btn-reSelect').on('click', function () {
            selectPage(2);
        });

        $('.page2 .list-item').on('click', function () {
            merge_model = $(this).find('img').attr('data-name');
            selectPage(3);
        });

        $('.page3 .list-item').on('click', function () {
            merge_bgimg = $(this).find('img').attr('data-name');
            requestPhoto();
        });

        function requestPhoto() {
            YDUI.dialog.loading.open('正在为您制作中');

            var data = {
                'merge_base64': merge_base64,
                'merge_model': merge_model,
                'merge_bgimg': merge_bgimg,
            };
            data[tokenKey] = tokenVal;

            $.ajax({
                url: "{{ url('api/mergeFace') }}",
                type: 'POST',
                dataType: "json",
                data: data,
                success: function (result) {
                    YDUI.dialog.loading.close();
                    if (result.code == 0) {
                        $('.show-image').attr("src", 'data:image/png;base64,' + result.data);
                        tokenKey = result.tokenKey;
                        tokenVal = result.tokenVal;
                        selectPage(4);
                    } else {
                        YDUI.dialog.toast('生成失败!', 'error', function () {
                            window.location.reload();
                        });
                    }
                },
                error: function () {
                    YDUI.dialog.loading.close();
                    YDUI.dialog.toast('网络错误！', 'error', function () {
                        window.location.reload();
                    });
                }
            });
        }

        $('#file').bind("change", function (e) {
            YDUI.dialog.loading.open('图片加载中');
            lrz(this.files[0])
                .then(function (rst) {
                    YDUI.dialog.loading.close();
                    merge_base64 = rst.base64.replace(/^data:image\/(jpeg|png|gif);base64,/, '');
                    selectPage(2);
                })
                .catch(function (err) {
                    YDUI.dialog.loading.close();
                })
                .always(function () {
                    YDUI.dialog.loading.close();
                });
        });

        function resetPage() {
            $('#file').val('');
            selectPage(1);
        }

        function selectPage(page) {
            for (var i = 1; i <= 4; i++) {
                if (page == i) {
                    $('.page' + i).show();
                } else {
                    $('.page' + i).hide();
                }
            }
        }
    </script>
    <script>
        wx.config({{ jssdk }});

        wx.ready(function () {
            var share = {{ share }};

            wx.hideMenuItems({
                menuList: [
                    'menuItem:readMode',
                    'menuItem:copyUrl',
                    'menuItem:setFont',
                    'menuItem:openWithQQBrowser',
                    'menuItem:openWithSafari',
                    'menuItem:originPage',
                    'menuItem:share:email',
                    'menuItem:favorite',
                    'menuItem:exposeArticle'
                ]
            });

            wx.updateAppMessageShareData(share);
            wx.updateTimelineShareData(share);
        });

        wx.error(function (res) {
            console.log('weixin 验证失败');
            console.log(res);
        });
    </script>
</body>

</html>