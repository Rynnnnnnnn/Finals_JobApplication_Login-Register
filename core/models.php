<?php
session_start();
require_once 'dbConfig.php';

class Models {
    private $conn;

    public function __construct() {
        $db = new DBConfig();
        $this->conn = $db->connect();
    }

    public function insertNewUser($username, $password, $first_name, $last_name, $dob, $role) {
        if (empty($username) || empty($password) || empty($first_name) || empty($last_name) || empty($dob) || empty($role)) {
            $_SESSION['message'] = "All fields are required.";
            return false;
        }
    
        // Check for duplicate username
        $checkUserSql = "SELECT * FROM users WHERE username = ?";
        $checkUserSqlStmt = $this->conn->prepare($checkUserSql);
        $checkUserSqlStmt->execute([$username]);
    
        if ($checkUserSqlStmt->rowCount() > 0) {
            $_SESSION['message'] = "User already exists.";
            return false;
        }
    
        // Insert new user with role
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (username, password, first_name, last_name, dob, role) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
    
        if ($stmt->execute([$username, $hashedPassword, $first_name, $last_name, $dob, $role])) {
            $_SESSION['message'] = "User successfully inserted";
    
            // Log the action
            $this->logAction($username, 'create', "New user created: $username");
            return true;
        }
    
        $_SESSION['message'] = "An error occurred during the query.";
        return false;
    }
    
    public function getJobListings() {
        // Database credentials (change as per your environment)
        $host = 'localhost';
        $dbname = 'job_system';
        $username = 'your_db_username';
        $password = 'your_db_password';
    
        try {
            // Create a PDO instance
            $db = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);  // Enable exceptions for errors
    
            // Prepare and execute the query
            $query = "SELECT * FROM job_listings";
            $stmt = $db->query($query);
    
            // Fetch all job listings as an associative array
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // In case of error, you can log it or display an error message
            die("Database error: " . $e->getMessage());
        }
    }

    public function logAction($username, $operation, $details) {
        $sql = "INSERT INTO logs (username, operation, details) VALUES (:username, :operation, :details)";
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                'username' => $username,
                'operation' => $operation,
                'details' => $details
            ]);
        } catch (PDOException $e) {
            // Handle logging failure silently
        }
    }

    public function getLogs() {
        $sql = "SELECT * FROM logs ORDER BY performed_at DESC";
        try {
            $stmt = $this->conn->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function loginUser($username, $password) {
        $sql = "SELECT * FROM users WHERE username = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$username]);

        if ($stmt->rowCount() == 1) {
            $userInfoRow = $stmt->fetch();
            $hashedPasswordFromDB = $userInfoRow['password'];

            if (password_verify($password, $hashedPasswordFromDB)) {
                $_SESSION['username'] = $username;
                $_SESSION['message'] = "Login successful!";
                return true;
            } else {
                $_SESSION['message'] = "Invalid password.";
            }
        } else {
            $_SESSION['message'] = "Username doesn't exist. Consider registration.";
        }
        return false;
    }

    public function createApplicant($data) {
        $sql = "INSERT INTO applicants (first_name, last_name, email, phone_number, specialization, experience_years, last_added_by) 
                VALUES (:first_name, :last_name, :email, :phone_number, :specialization, :experience_years, :last_added_by)";
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($data);
            return ['message' => 'Applicant created successfully.', 'statusCode' => 200];
        } catch (PDOException $e) {
            return ['message' => 'Failed to create applicant: ' . $e->getMessage(), 'statusCode' => 400];
        }
    }

    public function getUserDetails($username) {
        $sql = "SELECT * FROM users WHERE username = ?";
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$username]);
            return $stmt->fetch(PDO::FETCH_ASSOC); // Fetch user details as an associative array
        } catch (PDOException $e) {
            return false; // Return false if there's an error
        }
    }
}
?>
