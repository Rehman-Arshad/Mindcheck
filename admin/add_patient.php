<?php
include("../connection.php");

// Check if it's a POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate input
    $pname = $database->real_escape_string($_POST['pname']);
    $ptel = $database->real_escape_string($_POST['ptel']);
    $pemail = $database->real_escape_string($_POST['pemail']);
    $ppassword = password_hash($_POST['ppassword'], PASSWORD_DEFAULT);

    // Check if email already exists
    $check_query = "SELECT COUNT(*) as count FROM patient WHERE pemail = ?";
    $stmt = $database->prepare($check_query);
    $stmt->bind_param("s", $pemail);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    $response = array();
    
    if ($row['count'] > 0) {
        $response['success'] = false;
        $response['message'] = 'Email already exists';
    } else {
        // Insert the patient
        $query = "INSERT INTO patient (pname, ptel, pemail, ppassword) VALUES (?, ?, ?, ?)";
        $stmt = $database->prepare($query);
        $stmt->bind_param("ssss", $pname, $ptel, $pemail, $ppassword);
        
        try {
            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = 'Patient added successfully';
            } else {
                throw new Exception($stmt->error);
            }
        } catch (Exception $e) {
            $response['success'] = false;
            $response['message'] = 'Error: ' . $e->getMessage();
        }
    }
    
    $stmt->close();
    $database->close();

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// If not POST request, redirect to patients page
header("Location: patients.php");
exit;
?>
