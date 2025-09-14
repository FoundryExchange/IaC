<?php
// Include database connection settings
include 'db.php';

$project_id = isset($_GET['id']) ? $_GET['id'] : 0;

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["name_application"])) {
    $name_application = $_POST["name_application"];

    try {
        // Insert new application record with current timestamp
        $stmt = $conn->prepare("
            INSERT INTO Ragdoll_IaC_Application
                (Project_ID, Application_Name, Application_Created_At)
            VALUES (?, ?, NOW())
        ");
        $stmt->bindParam(1, $project_id, PDO::PARAM_INT);
        $stmt->bindParam(2, $name_application, PDO::PARAM_STR);
        $stmt->execute();
    } catch (PDOException $e) {
        echo "Error: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
    }
}

// Fetch application records related to the given project ID
try {
    $stmt = $conn->prepare("
        SELECT *
        FROM Ragdoll_IaC_Application
        WHERE Project_ID = ?
        ORDER BY Application_Created_At DESC
    ");
    $stmt->bindParam(1, $project_id, PDO::PARAM_INT);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Applications</title>
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

        input[type="text"] {
            width: 100%;
            max-width: 400px;
            padding: 12px 16px;
            font-size: 1em;
            background-color: #333;
            color: var(--text-color);
            border: 2px solid var(--accent-color);
            border-radius: var(--border-radius);
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
            text-align: center;
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
            input[type="text"], button {
                font-size: 0.9em;
                padding: 10px 14px;
            }
            table th, table td {
                padding: 8px;
                font-size: 0.9em;
            }
        }
    </style>
</head>
<body>
    <h2>Application Name</h2>
    <form method="post" action="">
        <input
            type="text"
            name="name_application"
            required
            placeholder="Application Name"
        >
        <br>
        <button type="submit">Submit</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Application Name</th>
                <th>Timestamp</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($results as $row): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['Application_ID'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td>
                    <a href="code.php?id=<?php echo urlencode($row['Application_ID']); ?>">
                        <?php echo htmlspecialchars($row['Application_Name'], ENT_QUOTES, 'UTF-8'); ?>
                    </a>
                </td>
                <td><?php echo htmlspecialchars($row['Application_Created_At'], ENT_QUOTES, 'UTF-8'); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>

<?php
$conn = null;
?>
