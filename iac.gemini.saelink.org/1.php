<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>CUBE SYSTEMS IaC 2025</title>
    <style>
        :root {
            --accent-color: #00BFFF;        /* 强调色 */
            --bg-color: #121212;            /* 页面背景 */
            --card-bg: #1e1e1e;             /* 卡片 / 容器背景 */
            --text-color: #FFFFFF;          /* 主要文字 */
            --muted-color: #AAAAAA;         /* 次要文字 */
            --border-radius: 12px;          /* 圆角 */
            --box-shadow: 0 4px 12px rgba(0, 0, 0, 0.5); /* 投影 */
            --button-bg-color: #2C3E50;     /* 按钮背景色 */
            --button-hover-color: #34495E;  /* 按钮悬停色 */
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 20px;
            background-color: var(--bg-color);
            color: var(--text-color);
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
        }

        /* 标题样式 */
        h2 {
            font-size: 2em;
            margin-top: 0;
            margin-bottom: 16px;
            color: var(--text-color);
            text-transform: uppercase;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.7);
            text-align: left;
        }
        h2 a {
            color: var(--text-color);
            text-decoration: none;
        }
        h2 a:hover {
            text-decoration: none;
        }

        .container {
            display: flex;
            flex-wrap: nowrap;
        }

        .sidebar {
            flex-basis: 8%;
            max-width: 12%;
            padding: 20px;
            font-size: 18px;
            background-color: var(--card-bg);
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
        }

        .content {
            flex-grow: 1;
            padding: 10px;
            background-color: var(--card-bg);
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            margin-left: 20px;
        }

        /* 侧栏链接 */
        .sidebar a {
            color: var(--accent-color);
            text-decoration: none;
            display: block;
            margin-bottom: 10px;
            transition: color 0.2s;
        }
        .sidebar a:hover {
            color: var(--button-hover-color);
        }

        iframe {
            width: 100%;
            height: 1900px;
            border: none;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            background-color: var(--bg-color);
        }
    </style>
</head>
<body>

<h2><a href="/">CUBE Infrastructure as Code 2025</a></h2>

<div class="container">
    <div class="sidebar">
        <?php
        include 'db.php';

        // Fetch existing project records in alphabetical order
        $firstProjectId = null;
        try {
            $stmt = $conn->prepare("SELECT * FROM Ragdoll_IaC_Project ORDER BY Project_Name ASC");
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (count($results) > 0) {
                $firstProjectId = $results[0]['Project_ID'];
            }

            foreach ($results as $row) {
                echo "<a href='application.php?id=" . $row['Project_ID'] . "' target='contentFrame'>" 
                    . htmlspecialchars($row['Project_Name'], ENT_QUOTES, 'UTF-8') 
                    . "</a>";
            }
        } catch(PDOException $e) {
            echo "Error: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
        }
        $conn = null;
        ?>
    </div>
    <div class="content">
        <iframe name="contentFrame" id="contentFrame"></iframe>
    </div>
</div>

<script>
    window.onload = function() {
        var firstProjectId = '<?php echo $firstProjectId; ?>';
        if (firstProjectId) {
            document.getElementById('contentFrame').src = 'application.php?id=' + firstProjectId;
        }
    };
</script>

</body>
</html>
