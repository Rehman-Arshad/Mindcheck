<?php
session_start();
include("../connection.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate input
    $docname = $database->real_escape_string($_POST['docname']);
    $specialties = (int)$_POST['specialties'];
    $doctel = $database->real_escape_string($_POST['doctel']);
    $docemail = $database->real_escape_string($_POST['docemail']);
    $docpassword = password_hash($_POST['docpassword'], PASSWORD_DEFAULT);
    $address = $database->real_escape_string($_POST['address']);
    $status = isset($_POST['status']) ? (int)$_POST['status'] : 1;

    // Check if email already exists
    $check_query = "SELECT COUNT(*) as count FROM doctor WHERE docemail = ?";
    $stmt = $database->prepare($check_query);
    $stmt->bind_param("s", $docemail);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    $response = array();
    
    if ($row['count'] > 0) {
        $response['success'] = false;
        $response['message'] = 'Email already exists';
    } else {
        // Verify that specialty exists
        $check_specialty = "SELECT COUNT(*) as count FROM specialties WHERE id = ?";
        $stmt = $database->prepare($check_specialty);
        $stmt->bind_param("i", $specialties);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        if ($row['count'] == 0) {
            $response['success'] = false;
            $response['message'] = 'Invalid specialty selected';
        } else {
            // Insert the doctor
            $query = "INSERT INTO doctor (docname, specialties, doctel, docemail, docpassword, address, status) 
                      VALUES (?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $database->prepare($query);
            $stmt->bind_param("sissssi", $docname, $specialties, $doctel, $docemail, $docpassword, $address, $status);
            
            try {
                if ($stmt->execute()) {
                    $response['success'] = true;
                    $response['message'] = 'Doctor added successfully';
                } else {
                    throw new Exception($stmt->error);
                }
            } catch (Exception $e) {
                $response['success'] = false;
                $response['message'] = 'Error: ' . $e->getMessage();
            }
        }
    }
    
    $stmt->close();
    $database->close();

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
} else {
    $response['success'] = false;
    $response['message'] = 'Invalid request method';

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
?>
