<?php
Helper::registry("/file", function() {
    return 'file';
});

Helper::registry("/listFile", function () {
    $tempDir = Constant::TMP_DIR;
    $c = null;
    $res = Helper::recursiveScanDir($tempDir, $c = (function($filename, $parentDir) use(&$c, $tempDir) {
        $filePath = $parentDir . $filename;
        $isDir = is_dir($filePath);
        $createTime = date(Constant::DATE_FORMAT, filectime($filePath));
        $size = sprintf("%.2d k", filesize($filePath) / 1024);
        $modifyTime = date(Constant::DATE_FORMAT, filemtime($filePath));
        return [
            'text' => "$filename($size,$modifyTime)", 
            'data' => [
                'filename' => $filename,
                'filePath' => Helper::relativePath($tempDir, $filePath),
                'createTime' => $createTime,
                'size' => $size,
                "modifyTime" => $modifyTime,
                'isDir' => $isDir
            ],
            'type' => $isDir ? 'dir' : 'file',
            'children' => $isDir ? Helper::recursiveScanDir($filePath . DIRECTORY_SEPARATOR, $c) : [],
        ];
    }));
    return Res::ok($res);
});

Helper::registry("/remoteDownload", function() {
    $tempDir = Constant::TMP_DIR;
    $url = Helper::json('url');
    $url = preg_replace('#\?.*$#', '', $url);

    try {
        $filename = ($arr = array_filter(explode("/", $url)))[count($arr)];
        if ($filename == '') {
            $filename = time() + ".file";
        }
      
        $fp = fopen($tempDir . $filename, 'w');
        $success = false;
        $maxRedirect = 10;
    
        for(;;) {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_FILE, $fp);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    
            if (!curl_exec($ch)) {
                break;
            }
            $info = curl_getinfo($ch);
    
            curl_close($ch);
            if (in_array($info['http_code'], [301, 302, 303, 307])) {
                if ($maxRedirect-- > 0) {
                    $url = $info['redirect_url'];
                    continue;
                } else {
                    break;
                }
            }
            if ($info['http_code'] == 200) {
                $success = true;
            }
            break;
        }
    
        fclose($fp);
        if (!$success) {
            if (file_exists($fp)) {
                unlink($fp);
            }
            return Res::err('下载失败!');
        }
        return Res::ok();
    } catch (Exception $e) {
        return Res::err($e);
    }
});

Helper::registry("/downFile", function() {
    $tempDir = Constant::TMP_DIR;
    $files = Helper::json('files');
    if ($files !== null && count($files) == 0) {
        return Res::err("文件不能为空");
    }

    sort($files);

    $fileName;
    if (count($files) > 1) {
        $zip = new ZipArchive();
        $zipName = Constant::ZIP_DIR . time() . '.zip';
        $err = $zip->open($zipName, ZipArchive::OVERWRITE | ZipArchive::CREATE);
        if (!$err) {
            return Res::err('创建压缩文件失败! err='. $err);
        }
        foreach ($files as $file) {
            $p = $tempDir . $file;
            $zP = Helper::relativePath($tempDir, $p);
            if (is_dir($p)) {
                $zip->addEmptyDir($zP);
            } else {
                $zip->addFile($p, $zP);
            }
        }
        $zip->close();
        $fileName = $zipName;
    } else {
        $fileName = Constant::TMP_DIR . $files[0];
    }

    return Res::ok(Helper::relativePath(Constant::ROOT_DIR, $fileName));
});

Helper::registry("/rmFile", function () {
    $files = Helper::json('files');
    sort($files);
    foreach ($files as $file) {
        if ($file !== null) {
            $filename = Constant::TMP_DIR . $file;
            if (file_exists($filename)) {
                if (is_dir($filename)) {
                    Helper::recursiveScanDir($filename . DIRECTORY_SEPARATOR, $c = function ($fileName, $parentDir) use(&$c) {
                        $p = $parentDir . $fileName;
                        if (file_exists($p)) {
                            if (is_dir($p)) {
                                $p .= DIRECTORY_SEPARATOR;
                                Helper::recursiveScanDir($p, $c);
                                if (file_exists($p)) {
                                    rmdir($p);
                                }
                            } else {
                                unlink($p);
                            }
                        }
                    });
                    if (file_exists($filename)) {
                        rmdir($filename);
                    }
                } else {
                    unlink($filename);
                }
            }
        }
    }
    return Res::ok();
});