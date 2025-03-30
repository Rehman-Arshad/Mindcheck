<?php
    session_start();
    if(isset($_SESSION["user"])){
        if(($_SESSION["user"])=="" or $_SESSION['usertype']!='p'){
            header("location: ../login.php");
        }else{
            $useremail=$_SESSION["user"];
        }
    }else{
        header("location: ../login.php");
    }
    
    include("../connection.php");
    include("patient_header_info.php");
    include("patient_header.php");
?>

<!-- Add navigation.js -->
<script src="../patient_assets/js/navigation.js"></script>
<section class="breadcrumbs">
    <div class="container">
        <div class="dash-body" style="margin-top: 15px">
            <table border="0" width="100%" style="border-spacing: 0;margin:0;padding:0;">
                <tr>
                    <td width="13%">
                        <a href="#" onclick="handleBackNavigation('../index.php')">
                            <button class="login-btn btn-primary-soft btn btn-icon-back" style="padding-top:11px;padding-bottom:11px;margin-left:20px;width:125px">
                                <font class="tn-in-text">Back</font>
                            </button>
                        </a>
                    </td>
                    <td>
                        <p style="font-size: 23px;padding-left:12px;font-weight: 600;">My Appointments</p>
                    </td>
                </tr>
            </table>
            <?php

            // Check if form is submitted
            if($_SERVER["REQUEST_METHOD"] == "POST") {

                // Collect form data
                $name = $_POST['name'];
                $email = $_POST['email'];
                $phone = $_POST['phone'];
                $date = $_POST['date'];
                $department = $_POST['department'];
                $doctor = $_POST['doctor'];
                $message = $_POST['message'];

                // Prepare SQL statement to insert data into appointment table
                $sql = "INSERT INTO myappointment (name, email, phone, appointment_date, department, doctor, message) VALUES (?, ?, ?, ?, ?, ?, ?)";

                // Prepare and bind parameters
                $stmt = $database->prepare($sql);
                $stmt->bind_param("sssssss", $name, $email, $phone, $date, $department, $doctor, $message);

                // Execute the statement
                if($stmt->execute()) {
                    // Appointment successfully added
                    echo "Your appointment request has been sent successfully. Thank you!";
                } else {
                    // Error occurred while adding appointment
                    echo "Error: " . $sql . "<br>" . $database->error;
                }

                // Close statement and connection
                $stmt->close();
                $database->close();
            }

            ?>
        </div>
    </div>
</section>
