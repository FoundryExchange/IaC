<?php
// Include database connection settings
include 'db.php'; // 注意文件名的大小写，确保与您的环境一致

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["name_project"])) {
    $name_project = $_POST["name_project"];

    try {
        // Prepare and execute statement to insert new project record with current timestamp
        $stmt = $conn->prepare("INSERT INTO Ragdoll_IaC_Project (Project_Name, Project_Created_At) VALUES (?, NOW())");
        $stmt->bindParam(1, $name_project);
        $stmt->execute();
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Fetch existing project records
try {
    $stmt = $conn->prepare("SELECT * FROM Ragdoll_IaC_Project");
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            background-color: #000;
            color: #00BFFF;
            font-family: Arial, sans-serif;
        }

        input, button {
            color: #00BFFF;
            background-color: #000;
            border: 1px solid #00BFFF;
            width: 200px; /* Adjusted width */
            margin: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #00BFFF;
            padding: 5px;
            text-align: center;
        }

        a {
            color: #00BFFF;
            text-decoration: none; /* No underline */
        }
    </style>
</head>
<body>
    <div>
        <h2>Add Project Record</h2>
        <form method="post" action="">
            <input type="text" name="name_project" required placeholder="Project Name"><br>
            <button type="submit">Submit</button>
        </form>
    </div>
<br>
    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Timestamp</th>
        </tr>
        <?php
        foreach ($results as $row) {
            echo "<tr>";
            echo "<td>" . $row['Project_ID'] . "</td>";
            echo "<td><a href='application.php?id=" . $row['Project_ID'] . "'>" . $row['Project_Name'] . "</a></td>"; // 修改为对应程序表的链接
            echo "<td>" . $row['Project_Created_At'] . "</td>";
            echo "</tr>";
        }
        ?>
    </table>
</body>
</html>

<?php
$conn = null; // PDO 对象使用 null 来关闭连接
?>
