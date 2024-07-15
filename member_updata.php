<?php
declare(strict_types=1);

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

ini_set('display_errors', '0');
ini_set('log_errors', '1');
error_reporting(E_ALL);

function jsonResponse(array $data, int $statusCode = 200): void {
    http_response_code($statusCode);
    echo json_encode($data);
    exit();
}

try {
    require_once("./connect_cid101g3.php");

    // Get raw POST data
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    // Check for JSON parse errors
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid JSON: ' . json_last_error_msg());
    }

    // Define required fields
    $required_fields = ['mem_name', 'mem_tel', 'mem_gender', 'mem_birth', 'mem_addr', 'mem_carrier', 'mem_company', 'mem_email'];
    
    // Validate required fields
    foreach ($required_fields as $field) {
        if (!isset($data[$field]) || trim($data[$field]) === '') {
            throw new Exception("Missing or empty required field: $field");
        }
    }

    // Sanitize and validate inputs
    $mem_name = filter_var($data['mem_name'], FILTER_SANITIZE_STRING);
    $mem_tel = filter_var($data['mem_tel'], FILTER_SANITIZE_NUMBER_INT);
    $mem_gender = filter_var($data['mem_gender'], FILTER_SANITIZE_STRING);
    $mem_birth = filter_var($data['mem_birth'], FILTER_SANITIZE_STRING);
    $mem_addr = filter_var($data['mem_addr'], FILTER_SANITIZE_STRING);
    $mem_carrier = filter_var($data['mem_carrier'], FILTER_SANITIZE_SPECIAL_CHARS);
    $mem_company = filter_var($data['mem_company'], FILTER_SANITIZE_STRING);
    $mem_email = filter_var($data['mem_email'], FILTER_VALIDATE_EMAIL);

    // Validate email address
    if (!$mem_email) {
        throw new Exception("Invalid email address");
    }

    // Prepare SQL statement
    $sql = "UPDATE member SET 
            mem_name = :mem_name, 
            mem_tel = :mem_tel, 
            mem_gender = :mem_gender, 
            mem_birth = :mem_birth, 
            mem_addr = :mem_addr, 
            mem_carrier = :mem_carrier, 
            mem_company = :mem_company 
            WHERE mem_email = :mem_email";

    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        throw new Exception("Prepare statement failed: " . $conn->error);
    }

    // Bind parameters
    $stmt->bindParam(':mem_name', $mem_name, PDO::PARAM_STR);
    $stmt->bindParam(':mem_tel', $mem_tel, PDO::PARAM_INT);
    $stmt->bindParam(':mem_gender', $mem_gender, PDO::PARAM_STR);
    $stmt->bindParam(':mem_birth', $mem_birth, PDO::PARAM_STR);
    $stmt->bindParam(':mem_addr', $mem_addr, PDO::PARAM_STR);
    $stmt->bindParam(':mem_carrier', $mem_carrier, PDO::PARAM_STR);
    $stmt->bindParam(':mem_company', $mem_company, PDO::PARAM_STR);
    $stmt->bindParam(':mem_email', $mem_email, PDO::PARAM_STR);

    // Execute the update statement
    if (!$stmt->execute()) {
        throw new Exception("Update execution failed: " . $stmt->errorInfo()[2]);
    }

    // Check if any rows were affected
    if ($stmt->rowCount() > 0) {
        jsonResponse(["error" => false, "msg" => "Member information updated successfully"]);
    } else {
        jsonResponse(["error" => true, "msg" => "No matching member found or no changes made"], 404);
    }

    // Close statement and database connection
    $stmt->closeCursor();

} catch (Exception $e) {
    // Log error and return JSON response with error message
    error_log("Error in member_update.php: " . $e->getMessage());
    jsonResponse(["error" => true, "msg" => $e->getMessage()], 500);

} finally {
    // Close database connection
    if (isset($conn)) {
        $conn = null;
    }
}
?>
