<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php
    $hostname = gethostname(); // 获取服务器的名称
    ?>
    <title>Lineperceptor 2025 <?php echo $hostname; ?></title> <!-- 动态显示服务器名称 -->
    <link rel="manifest" href="/index.json">
    <style>
        body {
            background-color: #333;
            color: #00BFFF;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        h1, #lastUpdated {
            font-size: 24px;
            color: #00BFFF;
            margin-top: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border: none; // 移除边框以匹配之前的风格
        }
        th {
            background-color: #5C0015; // 更改背景颜色以匹配之前的风格
            color: #00BFFF;
        }
        tr:nth-child(odd) {
            background-color: #444; // 添加奇数行背景色
        }
        tr:nth-child(even) {
            background-color: #383838; // 更改偶数行背景色以匹配之前的风格
        }
        .up { color: #00FF00; }
        .down { color: #FF0000; }
    </style>

<?php include("matomo.php"); ?>



</head>
<body>
    <h1>Lineperceptor 2025 <?php echo $hostname; ?></h1> <!-- 同样动态显示服务器名称 -->
    <div id="lastUpdated">TS: Not yet updated</div>
    <br>
    <table id="statusTable">
        <thead>
            <tr>
                <th>NUM</th>
                <th>DOMAIN</th>
                <th>PORT</th>
                <th>STATUS</th>
            </tr>
        </thead>
        <tbody>
            <!-- Status rows will be populated here -->
        </tbody>
    </table>

    <script>
        function fetchData() {
            fetch('data.json') // 确保路径与 JSON 文件的实际位置相匹配
                .then(response => response.json())
                .then(data => {
                    updateStatus(data.domains);
                    updateUpdateTime(data.updateTime);
                })
                .catch(error => console.error('Error fetching data:', error));
        }

        function updateStatus(domains) {
            const statusTableBody = document.getElementById('statusTable').getElementsByTagName('tbody')[0];
            statusTableBody.innerHTML = '';

            domains.forEach((domain, index) => {
                const statusClass = domain.status === 'UP' ? 'up' : 'down';
                statusTableBody.innerHTML += `<tr><td>${index + 1}</td><td>${domain.displayName}</td><td>${domain.port}</td><td class="${statusClass}">${domain.status}</td></tr>`;
            });
        }

        function updateUpdateTime(time) {
            const dateTimeElement = document.getElementById('lastUpdated');
            dateTimeElement.innerText = `TS: ${time}`;
        }

        setInterval(fetchData, 30000); // 每30秒更新一次
        fetchData(); // 初始加载
    </script>
</body>
</html>
