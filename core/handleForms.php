<?php
session_start();
require_once 'dbConfig.php';
require_once 'models.php';

$models = new Models();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Handle applicant creation
    if (isset($_POST['create'])) {
        $data = [
            'first_name' => $_POST['first_name'],
            'last_name' => $_POST['last_name'],
            'email' => $_POST['email'],
            'phone_number' => $_POST['phone_number'],
            'specialization' => $_POST['specialization'],
            'experience_years' => $_POST['experience_years'],
            'last_added_by' => $_SESSION['username']
        ];  

        if (method_exists($models, 'createApplicant')) {
            $result = $models->createApplicant($data);

            header("Location: ../index.php?message=" . urlencode($result['message']) . "&statusCode=" . $result['statusCode']);
            exit;
        } else {
            die('Error: createApplicant method is not defined in Models class.');
        }
    }

    // Handle applicant deletion
    if (isset($_POST['delete'])) {
        $id = $_POST['id'];
        $result = $models->deleteApplicant($id);

        header("Location: ../index.php?message=" . urlencode($result['message']) . "&statusCode=" . $result['statusCode']);
        exit;
    }

    // Handle applicant update
    if (isset($_POST['update'])) {
        $id = $_POST['id'];
        $data = [
            'first_name' => $_POST['first_name'],
            'last_name' => $_POST['last_name'],
            'email' => $_POST['email'],
            'phone_number' => $_POST['phone_number'],
            'specialization' => $_POST['specialization'],
            'experience_years' => $_POST['experience_years']
        ];

        if (method_exists($models, 'updateApplicant')) {
            $result = $models->updateApplicant($id, $data);
            echo $result['message'];
        } else {
            die('Error: updateApplicant method is not defined in Models class.');
        }
    }
}

// Handle user registration
if (isset($_POST['registerUserBtn'])) {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $dob = $_POST['dob'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (!empty($username) && !empty($password) && !empty($first_name) && !empty($last_name) && !empty($dob)) {
        $insertQuery = $models->insertNewUser($username, $password, $first_name, $last_name, $dob);
        if ($insertQuery) {
            header("Location: ../login.php");
        } else {
            header("Location: ../register.php");
        }
    } else {
        $_SESSION['message'] = "Please make sure the input fields are not empty for registration!";
        header("Location: ../register.php");
    }
}

// Handle user logout
if (isset($_GET['logoutAUser'])) {
    session_start();
    session_unset();
    session_destroy();
    header("Location: ../loginRegister.php"); // Redirect to login page
    exit();
}

// Handle user login
if (isset($_POST['loginUserBtn'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (!empty($username) && !empty($password)) {
        $loginQuery = $models->loginUser($username, $password);
        if ($loginQuery) {
            $_SESSION['username'] = $username;
            header("Location: ../index.php");
        } else {
            header("Location: ../login.php");
        }
    } else {
        $_SESSION['message'] = "Please make sure the input fields are not empty for login!";
        header("Location: ../login.php");
    }
}
?>
