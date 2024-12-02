<?php
session_start();
require_once 'core/models.php';

$models = new Models();
$logs = $models->getLogs();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity Logs</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
        a {
            display: inline-block;
            margin: 20px 0;
            padding: 10px 15px;
            background-color: #d4edda; /* Match search button color */
            color: #155724;  /* Text color */
            text-decoration: none;
            border-radius: 5px;
            border: none; /* Remove border */
        }
        a:hover {
            background-color: #f8d7da; /* Hover effect color */
        }
    </style>
</head>
<body>
    <h1>Activity Logs</h1>

    <!-- Back to Home Link -->
    <a href="index.php">Back to Home</a>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Operation</th>
                <th>Details</th>
                <th>Performed At</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($logs as $log): ?>
                <tr>
                    <td><?= $log['id'] ?></td>
                    <td><?= htmlspecialchars($log['username']) ?></td>
                    <td><?= htmlspecialchars($log['operation']) ?></td>
                    <td><?= htmlspecialchars($log['details']) ?></td>
                    <td><?= htmlspecialchars($log['performed_at']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
