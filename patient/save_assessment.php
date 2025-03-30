<?php
session_start();
include("../connection.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = json_decode(file_get_contents("php://input"), true);
    
    if (!isset($_SESSION["user"])) {
        echo json_encode(["status" => "error", "message" => "User not logged in"]);
        exit;
    }

    $user_email = $_SESSION["user"];
    
    // Get patient ID from email
    $patient_query = $database->query("select pid from patient where pemail='$user_email'");
    if ($patient_query->num_rows == 0) {
        echo json_encode(["status" => "error", "message" => "Patient not found"]);
        exit;
    }
    $patient = $patient_query->fetch_assoc();
    $patient_id = $patient['pid'];

    // Insert assessment info
    $child_name = $database->real_escape_string($data['name']);
    $phone = $database->real_escape_string($data['phone']);
    $gender = $database->real_escape_string($data['gender']);
    $test_date = $database->real_escape_string($data['testDate']);
    $birth_date = $database->real_escape_string($data['birthDate']);

    $sql = "INSERT INTO assessments (patient_id, child_name, phone, gender, test_date, birth_date) 
            VALUES ('$patient_id', '$child_name', '$phone', '$gender', '$test_date', '$birth_date')";
    
    if (!$database->query($sql)) {
        echo json_encode(["status" => "error", "message" => "Error saving assessment info"]);
        exit;
    }
    
    $assessment_id = $database->insert_id;

    // Save category scores
    foreach ($data['scores'] as $category => $score) {
        $category = $database->real_escape_string($category);
        $score = floatval($score);
        
        $sql = "INSERT INTO assessment_scores (assessment_id, category, score) 
                VALUES ('$assessment_id', '$category', '$score')";
        
        if (!$database->query($sql)) {
            echo json_encode(["status" => "error", "message" => "Error saving scores"]);
            exit;
        }
    }

    echo json_encode([
        "status" => "success",
        "message" => "Assessment saved successfully",
        "assessment_id" => $assessment_id
    ]);
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method"]);
}
?>
