<?php
session_start();  // Start the session

require_once 'core/models.php';

// Create an instance of the Models class
$models = new Models();
$message = "";
$statusCode = 200;

// Check if the applicant ID is provided in the URL
if (!isset($_GET['id'])) {
    header("Location: index.php?message=Invalid Applicant ID&statusCode=400");
    exit;
}

$applicantId = $_GET['id'];

// Retrieve the applicant details from the database
$applicant = $models->getApplicantById($applicantId);
if (!$applicant) {
    header("Location: index.php?message=Applicant not found&statusCode=400");
    exit;
}

// Check if the form is submitted (POST request)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ensure the user is logged in and username is available in the session
    if (!isset($_SESSION['username'])) {
        header("Location: login.php?message=You must be logged in to update applicants&statusCode=403");
        exit;
    }

    // Get the username from session
    $username = $_SESSION['username'];

    // Prepare the updated data
    $data = [
        'first_name' => $_POST['first_name'],
        'last_name' => $_POST['last_name'],
        'email' => $_POST['email'],
        'phone_number' => $_POST['phone_number'],
        'specialization' => $_POST['specialization'],
        'experience_years' => $_POST['experience_years']
    ];

    // Call the updateApplicant method with the necessary arguments
    $result = $models->updateApplicant($applicantId, $data, $username);

    // Redirect with the result message
    header("Location: index.php?message=" . urlencode($result['message']) . "&statusCode=" . $result['statusCode']);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Applicant</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .form-container {
            max-width: 600px;
            margin: auto;
        }
        input, button {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            box-sizing: border-box;
        }
        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <h1>Edit Applicant</h1>
    <div class="form-container">
        <form method="POST">
            <input type="text" name="first_name" placeholder="First Name" value="<?= htmlspecialchars($applicant['first_name']) ?>" required>
            <input type="text" name="last_name" placeholder="Last Name" value="<?= htmlspecialchars($applicant['last_name']) ?>" required>
            <input type="email" name="email" placeholder="Email" value="<?= htmlspecialchars($applicant['email']) ?>" required>
            <input type="text" name="phone_number" placeholder="Phone Number" value="<?= htmlspecialchars($applicant['phone_number']) ?>">
            <input type="text" name="specialization" placeholder="Specialization" value="<?= htmlspecialchars($applicant['specialization']) ?>" required>
            <input type="number" name="experience_years" placeholder="Experience Years" value="<?= htmlspecialchars($applicant['experience_years']) ?>" required>
            <button type="submit">Update Applicant</button>
        </form>
    </div>
</body>
</html>