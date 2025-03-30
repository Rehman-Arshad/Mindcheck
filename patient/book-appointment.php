<?php
session_start();
if (!isset($_SESSION["user"]) || $_SESSION["usertype"] != 'p') {
    header("location: ../login.php");
    exit;
}

include("../connection.php");

// Get patient ID
$email = $_SESSION["user"];
$result = $database->query("SELECT pid FROM patient WHERE pemail='$email'");
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $pid = $row['pid'];
} else {
    die("Error: Patient not found");
}

// Get doctor info
if (!isset($_GET['docid'])) {
    header("Location: doctors.php");
    exit;
}

$docid = $_GET['docid'];
$doctor_query = "SELECT d.*, s.name as specialty_name 
                FROM doctor d 
                LEFT JOIN specialties s ON d.specialties = s.id 
                WHERE docid = ?";
$stmt = $database->prepare($doctor_query);
$stmt->bind_param("i", $docid);
$stmt->execute();
$doctor_result = $stmt->get_result();

if ($doctor_result->num_rows === 0) {
    header("Location: doctors.php");
    exit;
}

$doctor = $doctor_result->fetch_assoc();

// Get available schedules for next 7 days
$schedule_query = "SELECT s.scheduleid, s.title, s.scheduledate, s.scheduletime, s.nop 
                  FROM schedule s 
                  LEFT JOIN appointment a ON s.scheduleid = a.scheduleid 
                  WHERE s.docid = ? 
                  AND s.scheduledate >= CURDATE() 
                  AND s.scheduledate <= DATE_ADD(CURDATE(), INTERVAL 7 DAY)
                  AND a.scheduleid IS NULL
                  ORDER BY s.scheduledate, s.scheduletime";

$stmt = $database->prepare($schedule_query);
$stmt->bind_param("i", $docid);
$stmt->execute();
$schedule_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Appointment - MindCheck</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../css/main.css">
    <style>
        .booking-container {
            max-width: 800px;
            margin: 40px auto;
            padding: 20px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 3px 15px rgba(0,0,0,0.08);
        }
        .doctor-info {
            display: flex;
            align-items: center;
            gap: 20px;
            padding: 20px;
            background: linear-gradient(45deg, #1977cc, #3291e6);
            color: white;
            border-radius: 10px;
            margin-bottom: 30px;
        }
        .doctor-avatar {
            width: 80px;
            height: 80px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5em;
            color: #1977cc;
        }
        .doctor-details h2 {
            margin: 0;
            font-size: 1.5em;
        }
        .doctor-specialty {
            opacity: 0.9;
            margin-top: 5px;
        }
        .schedule-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .time-slot {
            padding: 15px;
            border: 2px solid #1977cc;
            border-radius: 10px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .time-slot:hover {
            background: #1977cc;
            color: white;
        }
        .date-heading {
            margin: 30px 0 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #eee;
            color: #2c4964;
        }
        .no-slots {
            text-align: center;
            padding: 40px;
            color: #666;
            background: #f8f9fa;
            border-radius: 10px;
            margin-top: 20px;
        }
        .no-slots i {
            font-size: 3em;
            color: #1977cc;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <?php include("header.php"); ?>

    <div class="booking-container">
        <div class="doctor-info">
            <div class="doctor-avatar">
                <i class='bx bx-user'></i>
            </div>
            <div class="doctor-details">
                <h2>Dr. <?php echo htmlspecialchars($doctor['docname']); ?></h2>
                <div class="doctor-specialty"><?php echo htmlspecialchars($doctor['specialty_name']); ?></div>
            </div>
        </div>

        <h3>Available Time Slots</h3>
        <?php
        if ($schedule_result->num_rows > 0) {
            $current_date = '';
            while ($slot = $schedule_result->fetch_assoc()) {
                $date = date('F j, Y', strtotime($slot['scheduledate']));
                if ($date !== $current_date) {
                    if ($current_date !== '') {
                        echo '</div>'; // Close previous schedule-grid
                    }
                    $current_date = $date;
                    echo "<h4 class='date-heading'>$date</h4>";
                    echo "<div class='schedule-grid'>";
                }
                $time = date('g:i A', strtotime($slot['scheduletime']));
                ?>
                <div class="time-slot" onclick="bookAppointment(<?php echo $slot['scheduleid']; ?>)">
                    <i class='bx bx-time'></i>
                    <div><?php echo $time; ?></div>
                    <div><?php echo htmlspecialchars($slot['title']); ?></div>
                </div>
                <?php
            }
            if ($current_date !== '') {
                echo '</div>'; // Close last schedule-grid
            }
        } else {
            ?>
            <div class="no-slots">
                <i class='bx bx-calendar-x'></i>
                <h3>No Available Slots</h3>
                <p>Please try another date range or contact us for assistance.</p>
            </div>
            <?php
        }
        ?>
    </div>

    <script>
    function bookAppointment(scheduleId) {
        if (confirm('Would you like to book this appointment?')) {
            fetch('process-booking.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `scheduleid=${scheduleId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Appointment booked successfully!');
                    window.location.href = 'appointments.php';
                } else {
                    alert(data.message || 'Failed to book appointment. Please try again.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
        }
    }
    </script>
</body>
</html>
