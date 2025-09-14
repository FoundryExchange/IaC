<?php
include 'db.php'; // 确保文件名与您的环境一致

$application_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["code_content"])) {
    $code_content = $_POST["code_content"];

    try {
        $stmt = $conn->prepare("INSERT INTO Ragdoll_IaC_Code (Application_ID, Code_Content, Code_Created_At) VALUES (?, ?, NOW())");
        $stmt->bindParam(1, $application_id, PDO::PARAM_INT);
        $stmt->bindParam(2, $code_content, PDO::PARAM_STR);
        $stmt->execute();

        // 更新 Ragdoll_IaC_Application 表中的 Application_Created_At 字段
        $updateStmt = $conn->prepare("UPDATE Ragdoll_IaC_Application SET Application_Created_At = NOW() WHERE Application_ID = ?");
        $updateStmt->bindParam(1, $application_id, PDO::PARAM_INT);
        $updateStmt->execute();

    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

$search = "";
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["search"])) {
    $search = $_GET["search"];
}

try {
    if ($search !== "") {
        $searchParam = "%{$search}%";
        $stmt = $conn->prepare("
            SELECT c.*, a.Application_Name, p.Project_Name, p.Project_ID, a.Application_ID
            FROM Ragdoll_IaC_Code c
            LEFT JOIN Ragdoll_IaC_Application a ON c.Application_ID = a.Application_ID
            LEFT JOIN Ragdoll_IaC_Project p ON a.Project_ID = p.Project_ID
            WHERE c.Code_Content LIKE ? 
            ORDER BY c.Code_Created_At DESC
        ");
        $stmt->bindParam(1, $searchParam, PDO::PARAM_STR);
    } else {
        $stmt = $conn->prepare("
            SELECT c.*, a.Application_Name, p.Project_Name, p.Project_ID, a.Application_ID
            FROM Ragdoll_IaC_Code c
            LEFT JOIN Ragdoll_IaC_Application a ON c.Application_ID = a.Application_ID
            LEFT JOIN Ragdoll_IaC_Project p ON a.Project_ID = p.Project_ID
            WHERE c.Application_ID = ? 
            ORDER BY c.Code_Created_At DESC
        ");
        $stmt->bindParam(1, $application_id, PDO::PARAM_INT);
    }
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

$conn = null; // Close PDO connection
?>

<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        :root {
            --accent-color: #00BFFF;
            --bg-color: #121212;
            --card-bg: #1e1e1e;
            --text-color: #FFFFFF;
            --muted-color: #AAAAAA;
            --border-radius: 12px;
            --box-shadow: 0 4px 12px rgba(0, 0, 0, 0.5);
            --today-color: rgba(0, 191, 255, 0.1);
            --button-bg-color: #2C3E50;
            --button-hover-color: #34495E;
            --table-header-bg-color: #2C3E50;
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

        h2 {
            font-size: 2em;
            margin: 0 0 16px 0;
            color: var(--text-color);
            text-transform: uppercase;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.7);
            text-align: left;
        }

        form {
            margin-bottom: 20px;
        }

        textarea {
            width: 100%;
            max-width: 800px;
            height: 200px;
            padding: 12px 16px;
            font-size: 1em;
            background-color: #333;
            color: var(--text-color);
            border: 2px solid var(--accent-color);
            border-radius: var(--border-radius);
            resize: vertical;
            margin-bottom: 12px;
        }

        button {
            display: inline-block;
            padding: 12px 24px;
            font-size: 1em;
            font-weight: bold;
            cursor: pointer;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            background: var(--button-bg-color);
            color: white;
            border: none;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            transition: background 0.3s, transform 0.1s, box-shadow 0.2s;
        }
        button:hover {
            background: var(--button-hover-color);
            transform: translateY(-2px);
        }
        button:active {
            transform: translateY(0);
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.5);
        }

        .search-form {
            position: absolute;
            top: 10px;
            right: 50px;
            display: flex;
            align-items: center;
        }

        .search-box {
            width: 350px;
            padding: 12px 16px;
            font-size: 1em;
            background-color: #333;
            color: var(--text-color);
            border: 2px solid var(--accent-color);
            border-radius: var(--border-radius);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            background-color: transparent;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 16px;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid rgba(0, 191, 255, 0.3);
        }

        th {
            background-color: var(--table-header-bg-color);
            color: white;
            font-size: 1em;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        tbody tr:nth-child(odd) {
            background-color: rgba(255, 255, 255, 0.05);
        }

        tbody tr:hover {
            background-color: var(--today-color);
        }

        .highlighted {
            background-color: #333333; /* Dark grey */
        }

        .content-col {
            white-space: pre-wrap;
            cursor: pointer;
        }

        a {
            color: var(--accent-color);
            text-decoration: none;
            transition: color 0.2s;
        }
        a:hover {
            color: var(--button-hover-color);
        }

        @media (max-width: 600px) {
            body {
                padding: 12px;
            }
            textarea, button, .search-box {
                font-size: 0.9em;
                padding: 10px 14px;
            }
            table th, table td {
                padding: 8px;
                font-size: 0.9em;
            }
        }
    </style>
    <script>
        function copyToClipboard(element) {
            var range = document.createRange();
            range.selectNodeContents(element);
            window.getSelection().removeAllRanges();
            window.getSelection().addRange(range);
            document.execCommand('copy');
            window.getSelection().removeAllRanges();

            // Reset background color for all rows
            var rows = document.querySelectorAll('.content-col');
            rows.forEach(function(row) {
                row.style.backgroundColor = '';
            });

            // Highlight the current row
            element.style.backgroundColor = '#553333'; // Dark grey color
        }
    </script>
</head>
<body>
    <h2>Submit and View Code</h2>
    <form method="post" action="">
        <textarea name="code_content" required placeholder="Enter code here..."></textarea><br>
        <button type="submit">Submit</button>
    </form>

    <form class="search-form" method="get" action="">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($application_id, ENT_QUOTES, 'UTF-8'); ?>">
        <input type="text" name="search" class="search-box" placeholder="Search code..." value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>">
        <button type="submit">Search</button>
    </form>
    <br>
    <table>
        <tr>
            <th>ID</th>
            <th>Project / Application</th>
            <th>Code Content</th>
            <th>Timestamp</th>
        </tr>

        <?php
        foreach ($results as $row) {
            $projectId = $row['Project_ID'];
            $applicationId = $row['Application_ID'];
            $projectLink = "application.php?id=" . urlencode($projectId);
            $applicationLink = "code.php?id=" . urlencode($applicationId);

            echo "<tr>";
            echo "<td>" . $row['Code_ID'] . "</td>";
            echo "<td><a href='" . htmlspecialchars($projectLink, ENT_QUOTES, 'UTF-8') . "'>" . htmlspecialchars($row['Project_Name'], ENT_QUOTES, 'UTF-8') . "</a> / <a href='" . htmlspecialchars($applicationLink, ENT_QUOTES, 'UTF-8') . "'>" . htmlspecialchars($row['Application_Name'], ENT_QUOTES, 'UTF-8') . "</a></td>";
            echo "<td class='content-col' onclick='copyToClipboard(this)'>" . htmlspecialchars($row['Code_Content'], ENT_QUOTES, 'UTF-8') . "</td>";
            echo "<td>" . $row['Code_Created_At'] . "</td>";
            echo "</tr>";
        }
        ?>
    </table>
</body>
</html>
