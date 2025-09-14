<!DOCTYPE html>
<html lang="en">
<?php
// 设置默认时区为上海
date_default_timezone_set('Asia/Shanghai');

?>

<head>
    <meta charset="UTF-8">
    <title>File Browser with Enhanced Upload Feature</title>
    <style>
        body {
            background-color: #1a1a1a;
            font-family: Arial, sans-serif;
            color: #00BFFF;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 5px;
            border-bottom: 1px solid #66ccff;
            text-align: left;
        }
        th {
            background-color: #34495e;
            color: #ffffff;
        }
        .file, .directory, a {
            color: #66ccff;
            text-decoration: none; /* 去除下划线 */
        }
        .drag-drop-area {
            border: 2px dashed #00BFFF;
            margin: 20px auto;
            padding: 30px;
            text-align: center;
            color: #00BFFF;
        }
        th:nth-child(1), td:nth-child(1) {
            width: 50%;
        }
        th:nth-child(2), td:nth-child(2) {
            width: 30%;
        }
        th:nth-child(3), td:nth-child(3) {
            width: 20%;
        }
    </style>
</head>
<body>

<div class="drag-drop-area" id="drag-drop-area">
    Drag & drop files here to upload
</div>

<script>
    function dragOverHandler(ev) {
        ev.preventDefault();
    }

    function dropHandler(ev) {
        ev.preventDefault();
        var dataTransfer = ev.dataTransfer;
        var files = dataTransfer.files;
        var formData = new FormData();
        for (var i = 0; i < files.length; i++) {
            formData.append('uploaded_files[]', files[i]);
        }
        fetch('upload.php', {
            method: 'POST',
            body: formData,
        })
        .then(response => response.text())
        .then(data => {
            console.log(data);
            window.location.reload();
        })
        .catch(error => {
            console.error(error);
        });
    }

    document.getElementById('drag-drop-area').addEventListener('dragover', dragOverHandler);
    document.getElementById('drag-drop-area').addEventListener('drop', dropHandler);
</script>

<?php

function list_files($path) {
    $uri = str_replace($_SERVER['DOCUMENT_ROOT'], '', $path);
    if (!is_dir($path)) {
        echo "The specified path is not a directory.";
        return;
    }
    $files = scandir($path);
    echo "<table>";
    echo "<tr><th>Name</th><th>Modified Time</th><th>Size</th><th>Action</th></tr>";
    foreach ($files as $file) {
        // 检查文件是否为"."、".."或扩展名为".php"的文件
        if ($file == "." || $file == ".." || pathinfo($file, PATHINFO_EXTENSION) == "php") continue;
        $filePath = $path . "/" . $file;
        $fileUri = $uri . "/" . rawurlencode($file);
        echo "<tr>";
        // 文件名作为下载链接
        echo "<td><a href='$fileUri' class='file'>" . htmlspecialchars($file) . "</a></td>";
        echo "<td>" . date("Y-m-d H:i:s", filemtime($filePath)) . "</td>";
        $sizeInMb = filesize($filePath) / 1024 / 1024; // 将字节转换为MB
        echo "<td>" . number_format($sizeInMb, 2) . " MB</td>";
        // 删除链接样式调整
        echo "<td><a href='delete.php?file=" . urlencode($file) . "' style='color: #00BFFF; text-decoration: none;'>DELETE</a></td>";
        echo "</tr>";
    }
    echo "</table>";
}


$path = "./";
list_files($path);
?>
</body>
</html>
