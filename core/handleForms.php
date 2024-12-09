<?php
require_once 'dbConfig.php';
require_once 'models.php';

$models = new Models();
if (isset($_POST['registerUserBtn'])) {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $dob = $_POST['dob'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role']; // Ensure role is correctly captured

    if (!empty($username) && !empty($password) && !empty($first_name) && !empty($last_name) && !empty($dob) && !empty($role)) {
        $insertQuery = $models->insertNewUser($username, $password, $first_name, $last_name, $dob, $role);
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
            // Fetch user details (including role) from the database
            $userDetails = $models->getUserDetails($username);  // Assuming you have a method to fetch user details

            // Store the role and username in the session
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $userDetails['role'];  // Store user role in session

            // Redirect based on the role
            if ($_SESSION['role'] == 'hr') {
                header("Location: ../hr_dashboard.php");  // Redirect to HR dashboard
            } else {
                header("Location: ../index.php");  // Redirect to Applicant dashboard
            }
            exit;  // Always call exit after header redirect to stop further code execution
        } else {
            // Redirect back to login page if login fails
            header("Location: ../login.php");
            exit;
        }
    } else {
        $_SESSION['message'] = "Please make sure the input fields are not empty for login!";
        header("Location: ../login.php");
        exit;
    }
}
?>
