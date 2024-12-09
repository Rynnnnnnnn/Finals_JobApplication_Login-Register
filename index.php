<?php
session_start();

// Handle messages from previous actions
$message = '';
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
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
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
        }
        header {
            background-color: #343a40;
            color: #fff;
            padding: 20px;
            text-align: center;
        }
        h1 {
            margin: 0;
        }
        main {
            padding: 20px;
        }
        h2 {
            margin-top: 40px;
            color: #343a40;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        a {
            color: #007bff;
            text-decoration: none;
            margin: 0 10px;
        }
        a:hover {
            text-decoration: underline;
        }
        .message {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }
        .success {
            background-color: #28a745;
            color: white;
        }
        .error {
            background-color: #dc3545;
            color: white;
        }
        footer {
            background-color: #343a40;
            color: #fff;
            padding: 15px;
            text-align: center;
        }
        footer a {
            color: #fff;
            text-decoration: none;
        }
        footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<header>
    <h1>FindHire - Job Application System</h1>
</header>

<main>
    <!-- Display Message if Set -->
    <?php if ($message): ?>
        <div class="message <?= strpos($message, 'success') !== false ? 'success' : 'error' ?>">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <!-- Available Job Listings Section -->
    <section>
        <h2>Available Job Listings</h2>
        <table>
            <thead>
                <tr>
                    <th>Job ID</th>
                    <th>Description</th>
                    <th>Requirements</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <!-- Example loop for job listings -->
                <tr>
                    <td>1</td>
                    <td>Software Engineer</td>
                    <td>JavaScript, HTML, CSS</td>
                    <td>
                        <a href="applyJob.php?job_id=1" class="btn btn-primary">Apply</a>
                    </td>
                </tr>
            </tbody>
        </table>
    </section>

    <!-- Your Applications Section -->
    <section>
        <h2>Your Applications</h2>
        <table>
            <thead>
                <tr>
                    <th>Application ID</th>
                    <th>Job Title</th>
                    <th>Status</th>
                    <th>Follow Up</th>
                </tr>
            </thead>
            <tbody>
                <!-- Example loop for applications -->
                <tr>
                    <td>1</td>
                    <td>Software Engineer</td>
                    <td>Pending</td>
                    <td><a href="followUp.php?application_id=1" class="btn btn-secondary">Follow Up</a></td>
                </tr>
            </tbody>
        </table>
    </section>

    <!-- You Sent Messages Section -->
    <section>
        <h2>You Sent Messages</h2>
        <table>
            <thead>
                <tr>
                    <th>Message ID</th>
                    <th>Message Content</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <!-- Example loop for messages -->
                <tr>
                    <td>1</td>
                    <td>Can you provide an update on my application?</td>
                    <td>Sent</td>
                </tr>
            </tbody>
        </table>
    </section>
</main>

<footer>
    <p>&copy; 2024 FindHire | <a href="logout.php">Logout</a></p>
</footer>

</body>
</html>
