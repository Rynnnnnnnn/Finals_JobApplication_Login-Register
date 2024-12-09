<?php
require_once 'core/models.php'; // Include database connection and helper functions

// Initialize variables
$message = '';
$uploadError = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jobId = intval($_POST['job_id']);
    $applicantId = $_SESSION['user_id'];
    $coverLetter = trim($_POST['cover_letter']);

    // File upload handling
    $resumePath = '';
    if (isset($_FILES['resume']) && $_FILES['resume']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/resumes/';
        $fileName = basename($_FILES['resume']['name']);
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        // Validate file type
        if ($fileExtension !== 'pdf') {
            $uploadError = 'Only PDF files are allowed.';
        } else {
            $newFileName = uniqid('resume_', true) . '.' . $fileExtension;
            $resumePath = $uploadDir . $newFileName;

            // Move uploaded file
            if (!move_uploaded_file($_FILES['resume']['tmp_name'], $resumePath)) {
                $uploadError = 'Failed to upload the file. Please try again.';
            }
        }
    } else {
        $uploadError = 'Please upload a valid resume.';
    }

    // If no errors, save the application
    if (empty($uploadError)) {
        $conn = new mysqli('localhost', 'root', '', 'job_application_system');

        if ($conn->connect_error) {
            die('Connection failed: ' . $conn->connect_error);
        }

        $stmt = $conn->prepare(
            "INSERT INTO applications (job_post_id, applicant_id, cover_letter, resume_path) 
            VALUES (?, ?, ?, ?)"
        );
        $stmt->bind_param('iiss', $jobId, $applicantId, $coverLetter, $resumePath);

        if ($stmt->execute()) {
            $_SESSION['message'] = 'success:Application submitted successfully!';
            header('Location: index.php');
            exit();
        } else {
            $message = 'Error: ' . $stmt->error;
        }

        $stmt->close();
        $conn->close();
    }
}

// Get the job ID from the query string
if (!isset($_GET['job_id'])) {
    $_SESSION['message'] = 'error:Invalid job selection.';
    header('Location: index.php');
    exit();
}
$jobId = intval($_GET['job_id']);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apply for Job</title>
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
        label {
            display: block;
            margin-bottom: 10px;
            font-weight: bold;
        }
        textarea, input[type="file"], input[type="submit"] {
            width: 100%;
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .error {
            color: #dc3545;
        }
        .success {
            color: #28a745;
        }
    </style>
</head>
<body>

<header>
    <h1>Apply for Job</h1>
</header>

<main>
    <?php if (!empty($message)): ?>
        <p class="error"> <?= htmlspecialchars($message) ?> </p>
    <?php endif; ?>

    <?php if (!empty($uploadError)): ?>
        <p class="error"> <?= htmlspecialchars($uploadError) ?> </p>
    <?php endif; ?>

    <form action="applyJob.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="job_id" value="<?= htmlspecialchars($jobId) ?>">

        <label for="cover_letter">Why are you fit for this job?</label>
        <textarea name="cover_letter" id="cover_letter" rows="5" required></textarea>

        <label for="resume">Upload Your Resume (PDF only):</label>
        <input type="file" name="resume" id="resume" accept="application/pdf" required>

        <input type="submit" value="Submit Application">
    </form>
</main>

</body>
</html>
