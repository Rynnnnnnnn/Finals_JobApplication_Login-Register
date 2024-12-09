<?php
session_start();

/**
 * Establishes a database connection.
 * @return mysqli The database connection object.
 */
function getDBConnection() {
    $host = 'localhost';
    $username = 'root';
    $password = '';
    $database = 'job_application_system';

    $db = new mysqli($host, $username, $password, $database);

    if ($db->connect_error) {
        die('Database connection error: ' . $db->connect_error);
    }

    return $db;
}

/**
 * Retrieves the HR ID associated with the application.
 * @param int $application_id
 * @return int|null HR ID or null if not found
 */
function getHRIdForApplication($application_id) {
    $db = getDBConnection();
    $query = "SELECT hr_id FROM job_posts jp JOIN applications app ON jp.id = app.job_post_id WHERE app.id = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param('i', $application_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $hr = $result->fetch_assoc();
    return $hr['hr_id'] ?? null;
}

/**
 * Saves a message to the database.
 * @param int $sender_id
 * @param int $receiver_id
 * @param string $message
 * @return bool True if the message was saved successfully
 */
function sendMessage($sender_id, $receiver_id, $message) {
    $db = getDBConnection();
    $query = "INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)";
    $stmt = $db->prepare($query);
    $stmt->bind_param('iis', $sender_id, $receiver_id, $message);
    return $stmt->execute();
}

// Check if the user is logged in as an applicant
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'applicant') {
    header('Location: login.php');
    exit;
}

// Handle the follow-up message submission
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $application_id = $_POST['application_id'];
    $follow_up_message = trim($_POST['follow_up_message']);

    if (empty($follow_up_message)) {
        $message = 'Message cannot be empty!';
    } else {
        // Retrieve HR ID for messaging
        $hr_id = getHRIdForApplication($application_id);
        if ($hr_id) {
            // Save the message to the database
            $success = sendMessage($_SESSION['user_id'], $hr_id, $follow_up_message);
            if ($success) {
                $_SESSION['message'] = 'Follow-up message sent successfully!';
                header('Location: index.php');
                exit;
            } else {
                $message = 'Failed to send the follow-up message. Please try again later.';
            }
        } else {
            $message = 'Unable to identify HR contact for this application.';
        }
    }
}

// Get application ID from query string
if (!isset($_GET['application_id']) || empty($_GET['application_id'])) {
    header('Location: index.php');
    exit;
}
$application_id = $_GET['application_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Follow Up Application</title>
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
        form {
            max-width: 600px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            resize: vertical;
        }
        .btn {
            background-color: #007bff;
            color: #fff;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .message {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            text-align: center;
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
    <h1>Follow Up Application</h1>
</header>

<main>
    <?php if ($message): ?>
        <div class="message error">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <input type="hidden" name="application_id" value="<?= htmlspecialchars($application_id) ?>">

        <div class="form-group">
            <label for="follow_up_message">Message to HR:</label>
            <textarea id="follow_up_message" name="follow_up_message" rows="5" placeholder="Enter your follow-up message here..."></textarea>
        </div>

        <button type="submit" class="btn">Send Follow-Up</button>
    </form>
</main>

<footer>
    <p>&copy; 2024 FindHire | <a href="logout.php">Logout</a></p>
</footer>

</body>
</html>
