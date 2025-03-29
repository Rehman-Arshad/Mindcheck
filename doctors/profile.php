<?php
session_start();
if (!isset($_SESSION["user"]) || $_SESSION["usertype"] != 'd') {
    header("location: ../login.php");
    exit();
}

include("../connection.php");
include("includes/functions.php");

// Get doctor info
$useremail = $_SESSION["user"];
$userrow = $database->query("SELECT * FROM doctor WHERE docemail='$useremail'");
$userfetch = $userrow->fetch_assoc();
$userid = $userfetch["docid"];
$username = $userfetch["docname"];

// Get doctor's statistics
$stats_query = "
    SELECT 
        (SELECT COUNT(DISTINCT pid) FROM appointment a 
         INNER JOIN schedule s ON a.scheduleid = s.scheduleid 
         WHERE s.docid = ?) as total_patients,
        (SELECT COUNT(*) FROM schedule WHERE docid = ? AND scheduledate >= CURDATE()) as upcoming_sessions,
        (SELECT COUNT(*) FROM appointment a 
         INNER JOIN schedule s ON a.scheduleid = s.scheduleid 
         WHERE s.docid = ? AND a.status = 1) as completed_sessions
";

$stmt = $database->prepare($stats_query);
$stmt->bind_param("iii", $userid, $userid, $userid);
$stmt->execute();
$stats = $stmt->get_result()->fetch_assoc();

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $name = $database->real_escape_string($_POST['name']);
    $email = $database->real_escape_string($_POST['email']);
    $password = !empty($_POST['password']) ? 
                password_hash($_POST['password'], PASSWORD_DEFAULT) : 
                $userfetch['docpassword'];
    $spec = $database->real_escape_string($_POST['specialization']);
    $qual = $database->real_escape_string($_POST['qualification']);
    $tel = $database->real_escape_string($_POST['tel']);

    $update_query = "
        UPDATE doctor 
        SET docname=?, docemail=?, docpassword=?, specialties=?, doctel=?, qualifications=?
        WHERE docid=?
    ";
    
    $stmt = $database->prepare($update_query);
    $stmt->bind_param("ssssssi", $name, $email, $password, $spec, $tel, $qual, $userid);
    
    if ($stmt->execute()) {
        $success_message = "Profile updated successfully!";
        // Refresh doctor info
        $userrow = $database->query("SELECT * FROM doctor WHERE docid='$userid'");
        $userfetch = $userrow->fetch_assoc();
    } else {
        $error_message = "Error updating profile: " . $database->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Profile - MindCheck</title>
    <link rel="stylesheet" href="../css/animations.css">
    <link rel="stylesheet" href="../css/main.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #1977cc;
            --secondary: #2c4964;
            --success: #28a745;
            --danger: #dc3545;
            --warning: #ffc107;
            --info: #17a2b8;
            --light: #f8f9fa;
            --dark: #343a40;
        }
        .main-content {
            margin-top: 60px;
            padding: 2rem;
        }
        .page-header {
            background: white;
            padding: 1.5rem;
            border-radius: 15px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            margin-bottom: 2rem;
        }
        .profile-grid {
            display: grid;
            grid-template-columns: 350px 1fr;
            gap: 2rem;
        }
        .profile-sidebar {
            background: white;
            padding: 1.5rem;
            border-radius: 15px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .profile-main {
            background: white;
            padding: 1.5rem;
            border-radius: 15px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .profile-avatar {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background: var(--primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 4rem;
            margin: 0 auto 1.5rem;
        }
        .profile-name {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--secondary);
            text-align: center;
            margin-bottom: 0.5rem;
        }
        .profile-specialty {
            color: var(--primary);
            text-align: center;
            font-size: 1.1rem;
            margin-bottom: 1.5rem;
        }
        .profile-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
            margin: 1.5rem 0;
        }
        .stat-item {
            text-align: center;
            padding: 1rem;
            background: var(--light);
            border-radius: 8px;
        }
        .stat-value {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--primary);
            margin: 0;
        }
        .stat-label {
            font-size: 0.875rem;
            color: #666;
            margin: 0;
        }
        .profile-contact {
            margin-top: 1.5rem;
        }
        .contact-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1rem;
            color: var(--secondary);
        }
        .contact-item i {
            font-size: 1.25rem;
            color: var(--primary);
        }
        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
        }
        .form-grid .col-full {
            grid-column: 1 / -1;
        }
        .alert {
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .alert i {
            font-size: 1.25rem;
        }
        @media (max-width: 992px) {
            .profile-grid {
                grid-template-columns: 1fr;
            }
            .form-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php include("header.php"); ?>

        <div class="container-fluid">
            <div class="page-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 mb-0">Doctor Profile</h1>
                        <p class="text-muted mb-0">View and update your profile information</p>
                    </div>
                </div>
            </div>

            <?php if (isset($success_message)): ?>
                <div class="alert alert-success">
                    <i class='bx bx-check-circle'></i>
                    <?php echo $success_message; ?>
                </div>
            <?php endif; ?>

            <?php if (isset($error_message)): ?>
                <div class="alert alert-danger">
                    <i class='bx bx-error-circle'></i>
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>

            <div class="profile-grid">
                <div class="profile-sidebar">
                    <div class="profile-avatar">
                        <i class='bx bx-user'></i>
                    </div>
                    <h2 class="profile-name"><?php echo htmlspecialchars($userfetch['docname']); ?></h2>
                    <p class="profile-specialty"><?php echo htmlspecialchars($userfetch['specialties']); ?></p>

                    <div class="profile-stats">
                        <div class="stat-item">
                            <p class="stat-value"><?php echo $stats['total_patients']; ?></p>
                            <p class="stat-label">Patients</p>
                        </div>
                        <div class="stat-item">
                            <p class="stat-value"><?php echo $stats['upcoming_sessions']; ?></p>
                            <p class="stat-label">Sessions</p>
                        </div>
                        <div class="stat-item">
                            <p class="stat-value"><?php echo $stats['completed_sessions']; ?></p>
                            <p class="stat-label">Completed</p>
                        </div>
                    </div>

                    <div class="profile-contact">
                        <div class="contact-item">
                            <i class='bx bx-envelope'></i>
                            <span><?php echo htmlspecialchars($userfetch['docemail']); ?></span>
                        </div>
                        <div class="contact-item">
                            <i class='bx bx-phone'></i>
                            <span><?php echo htmlspecialchars($userfetch['doctel']); ?></span>
                        </div>
                        <div class="contact-item">
                            <i class='bx bx-award'></i>
                            <span><?php echo htmlspecialchars($userfetch['qualifications']); ?></span>
                        </div>
                    </div>
                </div>

                <div class="profile-main">
                    <h3 class="h4 mb-4">Update Profile Information</h3>
                    <form method="POST" action="">
                        <div class="form-grid">
                            <div class="mb-3">
                                <label class="form-label">Full Name</label>
                                <input type="text" name="name" class="form-control" 
                                       value="<?php echo htmlspecialchars($userfetch['docname']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email Address</label>
                                <input type="email" name="email" class="form-control" 
                                       value="<?php echo htmlspecialchars($userfetch['docemail']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Phone Number</label>
                                <input type="tel" name="tel" class="form-control" 
                                       value="<?php echo htmlspecialchars($userfetch['doctel']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Specialization</label>
                                <input type="text" name="specialization" class="form-control" 
                                       value="<?php echo htmlspecialchars($userfetch['specialties']); ?>" required>
                            </div>
                            <div class="mb-3 col-full">
                                <label class="form-label">Qualifications</label>
                                <textarea name="qualification" class="form-control" rows="3" 
                                          required><?php echo htmlspecialchars($userfetch['qualifications']); ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">New Password</label>
                                <input type="password" name="password" class="form-control" 
                                       placeholder="Leave blank to keep current password">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Confirm New Password</label>
                                <input type="password" name="confirm_password" class="form-control" 
                                       placeholder="Leave blank to keep current password">
                            </div>
                            <div class="col-full">
                                <button type="submit" name="update_profile" class="btn btn-primary">
                                    <i class='bx bx-save'></i> Save Changes
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
