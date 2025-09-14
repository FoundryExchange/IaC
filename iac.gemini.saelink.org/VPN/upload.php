<?php
// 指定文件将被上传到的目录
$targetDir = "./";

// 确保上传目录存在
if (!file_exists($targetDir)) {
    mkdir($targetDir, 0777, true);
}

$response = []; // 用于存储响应信息

// 检查是否有文件被上传
if (!empty($_FILES['uploaded_files'])) {
    foreach ($_FILES['uploaded_files']['name'] as $key => $name) {
        // 获取文件的临时路径
        $tmpName = $_FILES['uploaded_files']['tmp_name'][$key];

        // 生成目标路径
        $targetFilePath = $targetDir . basename($name);

        // 尝试将文件移动到目标目录
        if (move_uploaded_file($tmpName, $targetFilePath)) {
            // 文件上传成功
            $response[] = "File uploaded successfully: " . $name;
        } else {
            // 文件上传失败
            $response[] = "Error: There was a problem uploading " . $name;
        }
    }
} else {
    // 没有文件被上传
    $response[] = "Error: No files were uploaded.";
}

// 输出响应信息
echo json_encode($response);
