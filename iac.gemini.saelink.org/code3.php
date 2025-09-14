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
            ORDER BY c.Code_Created_At DESC");
        $stmt->bindParam(1, $searchParam, PDO::PARAM_STR);
    } else {
        $stmt = $conn->prepare("
            SELECT c.*, a.Application_Name, p.Project_Name, p.Project_ID, a.Application_ID
            FROM Ragdoll_IaC_Code c
            LEFT JOIN Ragdoll_IaC_Application a ON c.Application_ID = a.Application_ID
            LEFT JOIN Ragdoll_IaC_Project p ON a.Project_ID = p.Project_ID
            WHERE c.Application_ID = ? 
            ORDER BY c.Code_Created_At DESC");
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
        body {
            background-color: #1a1a1a;
            color: #00BFFF;
            font-family: Arial, sans-serif;
        }

        input, button, textarea {
            color: #00BFFF;
            background-color: #1a1a1a;
            border: 1px solid #00BFFF;
            margin: 5px;
        }

        textarea {
            width: 800px;
            height: 200px;
        }

        .search-form {
            position: absolute;
            top: 10px;
            right: 50px;
        }

        .search-box {
            color: #00BFFF;
            background-color: #000;
            border: 1px solid #00BFFF;
            width: 350px;
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        th {
            background-color: #800020; /* Bourgogne red */
        }

        th, td {
            padding: 5px;
            text-align: left;
        }

        tr:nth-child(odd) {
            background-color: #2e2e2e;
        }

        tr:nth-child(even) {
            background-color: #383838;
        }

        .content-col {
            white-space: pre-wrap;
            cursor: pointer;
        }

        a {
            color: #00BFFF; /* Link color */
            text-decoration: none; /* No underline */
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
            element.style.backgroundColor = '#000'; // Change background color to black
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
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($application_id); ?>">
        <input type="text" name="search" class="search-box" placeholder="Search code..." value="<?php echo htmlspecialchars($search); ?>">
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
            echo "<td><a href='" . htmlspecialchars($projectLink) . "'>" . htmlspecialchars($row['Project_Name']) . "</a> / <a href='" . htmlspecialchars($applicationLink) . "'>" . htmlspecialchars($row['Application_Name']) . "</a></td>";
            echo "<td class='content-col' onclick='copyToClipboard(this)'>" . htmlspecialchars($row['Code_Content']) . "</td>";
            echo "<td>" . $row['Code_Created_At'] . "</td>";
            echo "</tr>";
        }
        ?>
    </table>
</body>
</html>
