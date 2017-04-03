<html>

    <head>
        <title>Fetch articles</title>



        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="files/css/bootstrap.min.css" >
        <link rel="stylesheet" href="files/css/test.css"  crossorigin="anonymous">
        <!-- Optional theme -->
        <link rel="stylesheet" href="files/css/bootstrap-theme.min.css" >
        <meta http-equiv="Content-type" content="text/html; charset=utf-8" />

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
        <!-- Latest compiled and minified JavaScript -->
        <script src="files/js/bootstrap.min.js" ></script>
    </head>

    <body>
        <div class="container">
            <h1>Fetch</h1>
            <table class="table" id="fetch" >
                <thead>
                    <tr>
                        <th>Site</th>
                        <th>Status</th>
                        <th>Button</th>
                    </tr>
                </thead>
                <tbody>
                    <tr id="tvline" status="0">
                        <th>Tvline</th>
                        <th class="st">Off</th>
                        <th><button class="btn btn-default start" >Start</button><br/></th>
                    </tr>
                    <tr id="deadline" status="0">
                        <th>Deadline</th>
                        <th class="st" >Off</th>
                        <th><button class="btn btn-default start" >Start</button><br/></th>
                    </tr>
                    <tr id="variety" status="0">
                        <th>Variety</th>
                        <th class="st" >Off</th>
                        <th><button class="btn btn-default start " >Start</button><br/></th>
                    </tr>
                </tbody>
            </table>

        </div>


        <script type="text/javascript">
            $(function () {
                $('.start').on('click', function () {
                    var tr = $(this).parent().parent('tr').attr('id');
                    var status = $(this).parent().parent('tr').attr('status');
                    if (status == 0) {
                        var next = null;
                        start(tr, next);
                        $(this).html('Stop');
                        $(this).parent().parent('tr').attr('status', 1);
                        $(this).parent().parent().find('.st').html('<img height="50" src="/files/img/712.GIF"  />')
                    } else {
                        $(this).html('Start');
                        $(this).parent().parent('tr').attr('status', 0);
                        $(this).parent().parent().find('.st').html('Off')
                    }

                });


                function start(tr, next) {
                    $.ajax({
                        url: '/ajaxManager.php',
                        type: 'POST',
                        dataType: 'json',
                        data: {site: tr, action: 'go', next: next},
                        success: function (data) {
                            send_ajax(data);
                        }
                    });
                }

                function send_ajax(url) {
                    var status = $('#' + url.site).attr('status');
                    if (status == 1)
                        $.ajax({
                            url: '/ajaxManager.php',
                            type: 'POST',
                            dataType: 'json',
                            data: {site: url.site, url: url},
                            success: function (data) {
                                if (data.status == 1) {
                                    start(data.url.site, data.url.href)
                                } else {
                                    $('#' + url.site).attr('status', 0);
                                    $('#' + url.site).find('.st').html('Complited');
                                    $('#' + url.site).find('button').html('Start');

                                }
                            }

                        });
                }
            });
        </script>


    </body>
</html>



