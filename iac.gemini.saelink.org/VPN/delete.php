<?php
if (isset($_GET['file'])) {
    $file = basename($_GET['file']); // 获取要删除的文件名并确保只取文件名，防止路径遍历
    $path = __DIR__ . '/' . $file; // 构造文件的完整路径

    // 检查文件是否存在于当前脚本目录下
    if (file_exists($path) && is_file($path)) {
        // 删除文件
        unlink($path);
        // 删除成功，重定向回文件列表页面
        header('Location: index.php');
    } else {
        // 文件不存在，重定向回文件列表页面，可能需要传递错误消息
        header('Location: index.php?error=FileNotFound');
    }
} else {
    // 没有指定文件名，直接重定向回文件列表页面
    header('Location: /');
}
?>
