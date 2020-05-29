<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>登录登录</title>
    <link href="static/bootstrap-3.3.7/css/bootstrap.min.css" type="text/css" rel="stylesheet"/>
    <link href="static/bootstrap-3.3.7/css/bootstrap-theme.min.css" type="text/css" rel="stylesheet"/>
    <style>
        .background {
            background-image: url("static/image/wallhaven-13mk9v.jpg");
            position: absolute;
            display: block;
            width: 100%;
            top: 0;

            height: 100%;
            filter: opacity(0.6);
        }
        .form {
            background-color: white;
            padding: 20px 20px 30px;
            border-radius: 2px;
        }
    </style>
</head>
<body>
    <div class="background"></div>
    <div class="container" style="margin-top: 200px;">
        <div class="row">
            <div class="col-md-3 col-md-offset-8 col-sm-8 col-sm-offset-2 col-xs-10 col-xs-offset-1">
                <form class="form" method="post" action="/login">
                    <div class="form-group">
                        <label for="username">用户名</label>
                        <input class="form-control" id="username" type="text" name="username">
                    </div>
                    <div class="form-group">
                        <label for="password">密码</label>
                        <input class="form-control" id="password" type="password" name="password">
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-default">登录</button>
                    </div>                
                </form>
            </div>
        </div>
    </div>
</body>
<script type="text/javascript" src="static/js/jquery-3.5.1.min.js"></script>
<script type="text/javascript" src="static/bootstrap-3.3.7/js/bootstrap.min.js"></script>
<script type="text/javascript">
    $(document).ready(() => {
        <?php
            if (isset($err)) {
                echo "alert('$err');";
            }
        ?>
    })
</script>
</html>