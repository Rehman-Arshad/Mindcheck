<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION["user"]) || $_SESSION["usertype"] != 'p') {
    header("location: ../login.php");
    exit;
}

include("../connection.php");

// Get patient ID from email
$email = $_SESSION["user"];
$result = $database->query("SELECT pid FROM patient WHERE pemail='$email'");
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $pid = $row['pid'];
} else {
    die("Error: Patient not found");
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $child_name = $_POST["child_name"];
    $phone = $_POST["phone"];
    $gender = $_POST["gender"];
    $birth_date = $_POST["birth_date"];
    $test_date = date('Y-m-d'); // Current date
    
    // Create assessment record
    $stmt = $database->prepare("INSERT INTO assessments (patient_id, child_name, phone, gender, test_date, birth_date) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssss", $pid, $child_name, $phone, $gender, $test_date, $birth_date);
    
    if (!$stmt->execute()) {
        die("Error creating assessment: " . $database->error);
    }
    $assessment_id = $stmt->insert_id;
    
    // Process each category's score
    $categories = ['relating_to_people', 'emotional_response', 'body_use', 'object_use', 
                  'listening_response', 'adaptation_to_change', 'fear_or_nervousness', 
                  'visual_response', 'verbal_communication', 'activity_level'];
    
    foreach ($categories as $category) {
        if (isset($_POST[$category])) {
            $score = floatval($_POST[$category]);
            $stmt = $database->prepare("INSERT INTO assessment_scores (assessment_id, category, score) VALUES (?, ?, ?)");
            $stmt->bind_param("isd", $assessment_id, $category, $score);
            $stmt->execute();
        }
    }
    
    // Redirect to results page
    header("Location: assessment_results.php?id=" . $assessment_id);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Child Assessment Form - MindCheck</title>
    <link rel="stylesheet" href="../css/animations.css">
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/assessment.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <?php include("../header.php"); ?>

    <div class="container">
        <div class="assessment-form">
            <h1>Child Assessment Form</h1>
            <p class="instructions">Please fill out this form with your child's information and answer all questions honestly.</p>

            <form method="POST" action="">
                <div class="form-section">
                    <h2>Basic Information</h2>
                    <div class="form-group">
                        <label for="child_name">Child's Name</label>
                        <input type="text" id="child_name" name="child_name" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone" required>
                    </div>
                    <div class="form-group">
                        <label for="gender">Gender</label>
                        <select id="gender" name="gender" required>
                            <option value="">Select Gender</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="birth_date">Date of Birth</label>
                        <input type="date" id="birth_date" name="birth_date" required>
                    </div>
                </div>

                <?php
                // Get categories and display assessment questions
                $categories_query = "SELECT * FROM assessment_categories ORDER BY id";
                $categories_result = $database->query($categories_query);
                
                while ($category = $categories_result->fetch_assoc()): 
                ?>
                <div class="form-section">
                    <h2><?php echo ucwords(str_replace('_', ' ', $category['name'])); ?></h2>
                    <p class="category-desc"><?php echo $category['description']; ?></p>
                    
                    <div class="rating-group">
                        <label>Rate on a scale of 1-5:</label>
                        <div class="rating-options">
                            <input type="radio" name="<?php echo $category['name']; ?>" value="1" required> 1
                            <input type="radio" name="<?php echo $category['name']; ?>" value="2"> 2
                            <input type="radio" name="<?php echo $category['name']; ?>" value="3"> 3
                            <input type="radio" name="<?php echo $category['name']; ?>" value="4"> 4
                            <input type="radio" name="<?php echo $category['name']; ?>" value="5"> 5
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>

                <div class="form-actions">
                    <button type="submit" class="btn-primary">Submit Assessment</button>
                </div>
            </form>
        </div>
    </div>

    <script src="../js/jquery.min.js"></script>
    <script src="../js/assessment.js"></script>
</body>
</html>
