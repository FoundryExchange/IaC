<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Ragdoll Cloud 2024</title>
    <style>
        body {
            background-color: #1a1a1a;
            font-family: Arial, sans-serif;
            color: #00BFFF;
        }
        h2 {
            color: #00BFFF;
            text-align: left;
            margin-left: 20px; /* 标题左对齐并留有空间 */
        }
        .container {
            display: flex;
            flex-wrap: nowrap;
        }
        .sidebar {
            flex-basis: 8%; /* 初始基础宽度，容纳大约20个英文字母 */
            max-width: 8%; /* 最大宽度限制 */
            padding: 20px;
            font-size: 18px; /* 增加字号 */
        }
        .content {
            flex-grow: 1; /* 允许右侧区域占据剩余空间 */
            padding: 10px;
        }
        a {
            color: #66ccff;
            text-decoration: none; /* 去除下划线 */
            display: block; /* 使链接占满整行 */
            margin-bottom: 10px; /* 增加链接间距 */
        }
        iframe {
            width: 100%; /* 自动填充容器宽度 */
            height: 600px; /* 或根据需要调整高度 */
            border: none; /* 移除边框 */
        }
    </style>
</head>
<body>

<!-- 修改标题部分，将其放入<a>标签中，链接指向本页面自身 -->
<h2><a href="/cloud.php" style="color: #00BFFF; text-decoration: none;">Ragdoll Cloud  2024</a></h2>

<div class="container">
    <div class="sidebar">
        <?php
        $directories = array_filter(glob('*'), 'is_dir');
        foreach ($directories as $directory) {
            echo "<a href='./$directory/index.php' target='contentFrame'>$directory</a>";
        }
        ?>
    </div>
    <div class="content">
        <iframe name="contentFrame"></iframe>
    </div>
</div>

</body>
</html>
