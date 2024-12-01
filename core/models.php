<?php

require_once 'dbConfig.php';

class Models {
    private $conn;

    public function __construct() {
        $db = new DBConfig();
        $this->conn = $db->connect();
    }

    public function createApplicant($data) {
        $sql = "INSERT INTO applicants (first_name, last_name, email, phone_number, specialization, experience_years) 
                VALUES (:first_name, :last_name, :email, :phone_number, :specialization, :experience_years)";
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($data);
            return [
                'message' => 'Applicant added successfully',
                'statusCode' => 200
            ];
        } catch (PDOException $e) {
            return [
                'message' => 'Failed to add applicant: ' . $e->getMessage(),
                'statusCode' => 400
            ];
        }
    }

    public function deleteApplicant($id) {
        $sql = "DELETE FROM applicants WHERE id = :id";
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return [
                'message' => 'Applicant deleted successfully',
                'statusCode' => 200
            ];
        } catch (PDOException $e) {
            return [
                'message' => 'Failed to delete applicant: ' . $e->getMessage(),
                'statusCode' => 400
            ];
        }
    }

    public function readApplicants() {
        $sql = "SELECT * FROM applicants ORDER BY application_date DESC";
        try {
            $stmt = $this->conn->query($sql);
            return [
                'message' => 'Applicants retrieved successfully',
                'statusCode' => 200,
                'querySet' => $stmt->fetchAll(PDO::FETCH_ASSOC)
            ];
        } catch (PDOException $e) {
            return [
                'message' => 'Failed to retrieve applicants: ' . $e->getMessage(),
                'statusCode' => 400,
                'querySet' => []
            ];
        }
    }

    public function getApplicantById($id) {
        $sql = "SELECT * FROM applicants WHERE id = :id";
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function updateApplicant($id, $data) {
        $sql = "UPDATE applicants 
                SET first_name = :first_name, 
                    last_name = :last_name, 
                    email = :email, 
                    phone_number = :phone_number, 
                    specialization = :specialization, 
                    experience_years = :experience_years 
                WHERE id = :id";
        try {
            $stmt = $this->conn->prepare($sql);
            $data['id'] = $id; // Ensure id is added to the data
            $stmt->execute($data);
            return [
                'message' => 'Applicant updated successfully',
                'statusCode' => 200
            ];
        } catch (PDOException $e) {
            return [
                'message' => 'Failed to update applicant: ' . $e->getMessage(),
                'statusCode' => 400
            ];
        }
    }

    public function searchApplicants($search) {
        $sql = "SELECT * FROM applicants WHERE 
                first_name LIKE :search OR 
                last_name LIKE :search OR 
                email LIKE :search OR 
                phone_number LIKE :search OR 
                specialization LIKE :search OR 
                experience_years LIKE :search 
                ORDER BY application_date DESC";
        try {
            $stmt = $this->conn->prepare($sql);
            $searchTerm = '%' . $search . '%';
            $stmt->bindParam(':search', $searchTerm, PDO::PARAM_STR);
            $stmt->execute();
            return [
                'message' => 'Search completed successfully',
                'statusCode' => 200,
                'querySet' => $stmt->fetchAll(PDO::FETCH_ASSOC)
            ];
        } catch (PDOException $e) {
            return [
                'message' => 'Failed to search applicants: ' . $e->getMessage(),
                'statusCode' => 400,
                'querySet' => []
            ];
        }
    }

    // Insert new user function using the internal $conn
    public function insertNewUser($username, $password, $first_name, $last_name, $dob) {
        if (empty($username) || empty($password) || empty($first_name) || empty($last_name) || empty($dob)) {
            $_SESSION['message'] = "All fields are required.";
            return false;
        }

        $checkUserSql = "SELECT * FROM user_passwords WHERE username = ?";
        $checkUserSqlStmt = $this->conn->prepare($checkUserSql);
        $checkUserSqlStmt->execute([$username]);

        if ($checkUserSqlStmt->rowCount() == 0) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $sql = "INSERT INTO user_passwords (username, password, first_name, last_name, dob) VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->conn->prepare($sql);
            $executeQuery = $stmt->execute([$username, $hashedPassword, $first_name, $last_name, $dob]);

            if ($executeQuery) {
                $_SESSION['message'] = "User successfully inserted";
                return true;
            } else {
                $_SESSION['message'] = "An error occurred during the query";
            }
        } else {
            $_SESSION['message'] = "User already exists";
        }
        return false;
    }

    // Login user using internal $conn
    public function loginUser($username, $password) {
        $sql = "SELECT * FROM user_passwords WHERE username = ?";
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
            $_SESSION['message'] = "Username doesn't exist in the database. You may consider registration first.";
        }
        return false;
    }
}

?>
