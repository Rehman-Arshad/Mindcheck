<?php
include("../connection.php");

// Check if it's a GET request with an ID
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
    $docid = (int)$_GET['id'];
    
    // First check if the doctor has any appointments
    $check_query = "SELECT COUNT(*) as count FROM schedule WHERE docid = ?";
    $stmt = $database->prepare($check_query);
    $stmt->bind_param("i", $docid);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    $response = array();
    
    if ($row['count'] > 0) {
        $response['success'] = false;
        $response['message'] = 'Cannot delete doctor with existing schedules';
    } else {
        // Delete the doctor
        $delete_query = "DELETE FROM doctor WHERE docid = ?";
        $stmt = $database->prepare($delete_query);
        $stmt->bind_param("i", $docid);
        
        try {
            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = 'Doctor deleted successfully';
            } else {
                throw new Exception($stmt->error);
            }
        } catch (Exception $e) {
            $response['success'] = false;
            $response['message'] = 'Error: ' . $e->getMessage();
        }
    }
    
    $stmt->close();
} else {
    $response['success'] = false;
    $response['message'] = 'Invalid request';
}

$database->close();

header('Content-Type: application/json');
echo json_encode($response);
exit;
?>
