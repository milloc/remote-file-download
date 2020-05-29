<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>远程文件下载</title>
    <link href="static/bootstrap-3.3.7/css/bootstrap.min.css" type="text/css" rel="stylesheet"/>
    <link href="static/bootstrap-3.3.7/css/bootstrap-theme.min.css" type="text/css" rel="stylesheet"/>
    <link href="static/jstree-3.3.9/themes/default/style.min.css" type="text/css" rel="stylesheet"/>
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
            <div class="col-md-8">
                <form class="form-inline">
                    <a class="btn btn-default" href="javascript: rmFile();" role="button">删除已选</a>
                    <a class="btn btn-default" href="javascript: downFile();" role="button">下载已选</a>
                    <div class="form-group">
                        <label class="sr-only" for="url">Email address</label>
                        <input type="text" class="form-control" id="url" placeholder="url" name="url" id="url">
                    </div>
                    <a class="btn btn-default" href="javascript: remoteDownload();" role="button">新增远程下载</a>
                </form>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12" id="fileList">

            </div>
        </div>
    </div>
</body>
<script type="text/javascript" src="static/js/jquery-3.5.1.min.js"></script>
<script type="text/javascript" src="static/bootstrap-3.3.7/js/bootstrap.min.js"></script>
<script type="text/javascript" src="static/jstree-3.3.9/jstree.min.js"></script>
<script type="text/javascript">
    localStorage.setItem("token", <?php echo "'".Token::generateCurrentToken()."'" ?>)

    function getSelectedFiles() {
        let files;
        return (files = $('#fileList').jstree().get_selected(true)).length == 0 ? [] : files.map((item) => item.data.filePath);
    }

    function fileList() {
        $.ajax('listFile', {
            headers: {'token': localStorage.getItem('token')},
            success: (res) => {
                console.log(res);
                if (res.code == 200) {
                    var option = {
                        'core': {
                            'data': [
                                {
                                    "text": "root",
                                    "type": "dir",
                                    "data": {
                                        "filePath": "",
                                        "isDir": true,
                                    },
                                    "children": res.data,
                                }
                            ]
                        },
                        "types" : {
                            "dir" : {
                                "icon" : "glyphicon glyphicon-folder-open"
                            },
                            "file" : {
                                "icon" : "glyphicon glyphicon-file"
                            }
                        },
                        "plugins" : ["checkbox", "types"]
                    };
                    console.log(option)
                    $('#fileList').jstree(option);
                } 
            },
        });
    }

    function rmFile() {
        let selectedFiles = getSelectedFiles().filter(f => f != '');
        if (selectedFiles.length == 0) {
            alert('请选择文件!');
            return;
        }

        $.ajax('rmFile', {
            headers: {'token': localStorage.getItem('token'), 'content-type': 'application/json'},
            type: 'post',
            data: JSON.stringify({'files': selectedFiles}),
            success: (res) => {
                if (res.code == 200) {
                    confirm('删除成功!');
                } else {
                    alert(res.err);
                }
                history.go();
            }
        });
    }

    function downFile() {
        let selectedFiles = getSelectedFiles();
        if (selectedFiles.length == 0) {
            alert('请选择文件!');
            return;
        }

        $.ajax('downFile', {
            headers: {'token': localStorage.getItem('token'), 'content-type': 'application/json'},
            type: 'post',
            data: JSON.stringify({'files': selectedFiles}),
            success: (res) => {
                if (res.code == 200) {
                    location.href = res.data;
                } else {
                    alert(res.err);
                    history.go();
                }
            }
        });
    }

    function remoteDownload() {
        let url = $('#url').val();
        if (url === null) {
            alert("请输入地址！");
            return;
        }

        $.ajax("remoteDownload", {
            headers: {'token': localStorage.getItem('token'), 'content-type': 'application/json'},
            type: 'post',
            data: JSON.stringify({'url': url}),
            success: (res) => {
                if (res.code == 200) {
                    alert('下载成功！');
                    history.go();
                } else {
                    alert(res.err);
                }
            }
        });
    }

    $(document).ready(() => {
        fileList();
        $('#fileList').on("changed.jstree", function (e, data) {
        });
    });
</script>
</html>
