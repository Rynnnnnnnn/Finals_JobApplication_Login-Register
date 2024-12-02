<?php
require_once 'core/models.php';

$models = new Models();
$applicants = $models->readApplicants();
$searchResults = [];
$message = '';
$statusCode = 200;

if (isset($_GET['message'])) {
    $message = $_GET['message'];
    $statusCode = isset($_GET['statusCode']) ? $_GET['statusCode'] : 200;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['search'])) {
    $searchResults = $models->searchApplicants($_GET['search']);
    
    if (!empty($searchResults['querySet'])) {
        $message = "Search successful!";
        $statusCode = 200;
    } else {
        $message = "No applicants found.";
        $statusCode = 404;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Application System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
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
        .form-container {
            margin-bottom: 20px;
        }
        input, button {
            padding: 8px;
            margin: 5px;
        }
        .action-buttons button {
            margin-right: 5px;
        }
        .message {
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .search-container {
            display: flex;
            align-items: center;
        }
        .search-container input {
            flex-grow: 1;
        }
        .search-container button {
            margin-left: 10px;
            display: inline-block;
            padding: 10px 15px;
            background-color: #d4edda; /* Match back to home button color */
            color: #155724;  /* Text color */
            text-decoration: none;
            border-radius: 5px;
            border: none; /* Remove border */
        }
        .search-container button:hover {
            background-color: #f8d7da; /* Hover effect color */
        }
    </style>
</head>
<body>
    <h1>Job Application System</h1>

    <!-- Display Message if Set -->
    <?php if ($message): ?>
        <div class="message <?= $statusCode == 200 ? 'success' : 'error' ?>">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>
    
    <!-- Create Applicant Form -->
    <div class="form-container">
        <form method="POST" action="core/handleForms.php">
            <h2>Create Applicant</h2>
            <input type="text" name="first_name" placeholder="First Name" required>
            <input type="text" name="last_name" placeholder="Last Name" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="text" name="phone_number" placeholder="Phone Number">
            <input type="text" name="specialization" placeholder="Specialization" required>
            <input type="number" name="experience_years" placeholder="Experience Years" required>
            <button type="submit" name="create">Add Applicant</button>
        </form>
    </div>

    <!-- Search Form with Refresh Button -->
   <h2>Search Applicants</h2>
   <div class="search-container">
        <form method="GET">
            <input type="text" name="search" placeholder="Search...">
            <button type="submit">Search</button>
        </form>
        <form method="GET" action="" style="display:inline;">
            <button type="submit">Refresh</button>
        </form>
    </div>

    <!-- Display Applicants in Table -->
    <h2>Applicants</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email</th>
                <th>Phone Number</th>
                <th>Specialization</th>
                <th>Experience (Years)</th>
                <th>Date added</th>
                <th>Last Added By</th>
                <th>Last Updated By</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $dataSet = isset($_GET['search']) ? $searchResults['querySet'] : $applicants['querySet'];
            if (!empty($dataSet)) {
                foreach ($dataSet as $applicant): 
            ?>
                <tr>
                    <td><?= $applicant['id'] ?></td>
                    <td><?= $applicant['first_name'] ?></td>
                    <td><?= $applicant['last_name'] ?></td>
                    <td><?= $applicant['email'] ?></td>
                    <td><?= $applicant['phone_number'] ?></td>
                    <td><?= $applicant['specialization'] ?></td>
                    <td><?= $applicant['experience_years'] ?></td>
                    <td><?= $applicant['application_date'] ?></td>
                    <td><?= $applicant['last_added_by'] ?? 'N/A' ?></td>
                    <td><?= $applicant['last_updated_by'] ?? 'N/A' ?></td>
                    <td class="action-buttons">
                        <!-- Delete Button -->
                        <form method="POST" action="core/handleForms.php" style="display:inline;">
                            <input type="hidden" name="id" value="<?= $applicant['id'] ?>">
                            <button type="submit" name="delete">Delete</button>
                        </form>
                        <!-- Edit Button -->
                        <a href="editApplicant.php?id=<?= $applicant['id'] ?>"><button>Edit</button></a>
                    </td>
                </tr>
            <?php endforeach; } ?>
            <a href="logs.php">View Logs</a>
            <a href="logout.php">Logout</a>
        </tbody>
    </table>
</body>
</html>
