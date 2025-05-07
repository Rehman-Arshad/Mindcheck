<?php

session_start();

if (isset($_SESSION["user"])) {
    if (($_SESSION["user"]) == "" or $_SESSION['usertype'] != 'p') {
        header("location: ../login.php");
    } else {
        $useremail = $_SESSION["user"];
    }
} else {
    header("location: ../login.php");
}

include("patient_header_info.php");
include("display_appointment_status.php"); // Include the helper function

// Make sure we have the patient ID from patient_header_info.php
if (!isset($userId)) {
    die("Patient ID not found. Please log in again.");
}

// Fixed query to use only columns that exist in the database
$sqlmain = "SELECT 
    appointment.appoid,
    schedule.scheduleid,
    schedule.title,
    doctor.docname,
    patient.pname,
    schedule.scheduledate,
    schedule.scheduletime,
    appointment.status,
    doctor.specialties
FROM schedule 
INNER JOIN appointment ON schedule.scheduleid = appointment.scheduleid 
INNER JOIN patient ON patient.pid = appointment.pid 
INNER JOIN doctor ON schedule.docid = doctor.docid 
WHERE patient.pid = " . $userId . " ";

if ($_POST) {
    if (!empty($_POST["sheduledate"])) {
        $sheduledate = $_POST["sheduledate"];
        $sqlmain .= " and schedule.scheduledate='$sheduledate' ";
    };
}

// Order by schedule date instead of the non-existent appodate column
$sqlmain .= "ORDER BY schedule.scheduledate ASC, schedule.scheduletime ASC";

// For debugging
// echo "<pre>" . htmlspecialchars($sqlmain) . "</pre>";

$result = $database->query($sqlmain);

include("patient_header.php");
?>
<section class="breadcrumbs">
    <div class="container">
        <div class="dash-body">
            <div class="row" style="margin-top: 25px; margin-bottom: 20px;">
                <div class="col-md-8">
                    <h2 style="color: #2c4964; font-weight: 600; margin-bottom: 5px;">
                        <i class="fas fa-calendar-check" style="color: #0A76D8;"></i> My Appointments
                    </h2>
                    <p style="color: #777777; font-size: 16px;">View and manage your scheduled appointments</p>
                </div>
                <div class="col-md-4 text-right">
                    <div class="date-display" style="background-color: #f8f9fa; padding: 10px 15px; border-radius: 8px; display: inline-block; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
                        <i class="far fa-calendar-alt" style="color: #0A76D8; margin-right: 8px;"></i>
                        <span style="font-weight: 500; color: #2c4964;">
                            <?php
                            date_default_timezone_set('Asia/Karachi');
                            $today = date('l, F j, Y');
                            echo $today;
                            ?>
                        </span>
                    </div>
                </div>
            </div>
                <!-- <tr>
                    <td colspan="4" >
                        <div style="display: flex;margin-top: 40px;">
                        <div class="heading-main12" style="margin-left: 45px;font-size:20px;color:rgb(49, 49, 49);margin-top: 5px;">Schedule a Session</div>
                        <a href="?action=add-session&id=none&error=0" class="non-style-link"><button  class="login-btn btn-primary btn button-icon"  style="margin-left:25px;background-image: url('../img/icons/add.svg');">Add a Session</font></button>
                        </a>
                        </div>
                    </td>
                </tr> -->
            <div class="row" style="margin-bottom: 20px;">
                <div class="col-md-6">
                    <div class="appointment-count" style="background-color: #D8EBFA; padding: 12px 20px; border-radius: 8px; display: inline-block;">
                        <i class="fas fa-clipboard-list" style="color: #0A76D8; margin-right: 10px;"></i>
                        <span style="font-weight: 500; color: #2c4964; font-size: 16px;">Total Appointments: <b><?php echo $result->num_rows; ?></b></span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="filter-section" style="float: right;">
                        <form action="" method="post" class="form-inline">
                            <div class="input-group" style="box-shadow: 0 2px 5px rgba(0,0,0,0.05); border-radius: 8px; overflow: hidden;">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" style="background-color: #D8EBFA; border: none; padding: 10px 15px;">
                                        <i class="far fa-calendar-alt" style="color: #1b62b3;"></i>
                                    </span>
                                </div>
                                <input type="date" name="sheduledate" id="date" class="form-control" style="border: none; padding: 10px 15px; width: 200px;">
                                <div class="input-group-append">
                                    <button type="submit" name="filter" class="btn" style="background-color: #0A76D8; color: white; border: none; padding: 10px 20px;">
                                        <i class="fas fa-filter" style="margin-right: 5px;"></i> Filter
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="appointment-container" style="background-color: #fff; border-radius: 10px; box-shadow: 0 3px 10px rgba(0,0,0,0.05); padding: 20px;">
                        <div class="row" id="appointment-cards">

                                        <?php

                                        if ($result->num_rows == 0) {
                                            echo '<tr>
                                    <td colspan="7">
                                    <br><br><br><br>
                                    <center>
                                    <img src="../img/notfound.svg" width="25%">
                                    
                                    <br>
                                    <p class="heading-main12" style="margin-left: 45px;font-size:20px;color:rgb(49, 49, 49)">We  couldnt find anything related to your keywords !</p>
                                    <a class="non-style-link" href="appointment.php"><button  class="login-btn btn-primary-soft btn"  style="display: flex;justify-content: center;align-items: center;margin-left:20px;">&nbsp; Show all Appointments &nbsp;</font></button>
                                    </a>
                                    </center>
                                    <br><br><br><br>
                                    </td>
                                    </tr>';
                                        } else {

                                            for ($x = 0; $x < ($result->num_rows); $x++) {
                                                echo "<tr>";
                                                for ($q = 0; $q < 3; $q++) {
                                                    $row = $result->fetch_assoc();
                                                    if (!isset($row)) {
                                                        break;
                                                    };
                                                    $scheduleid = $row["scheduleid"];
                                                    $title = $row["title"];
                                                    $docname = $row["docname"];
                                                    $scheduledate = $row["scheduledate"];
                                                    $scheduletime = $row["scheduletime"];
                                                    $specialty = $row["specialties"];
                                                    $appoid = $row["appoid"];
                                                    $status = $row["status"];

                                                    if ($scheduleid == "") {
                                                        break;
                                                    }

                                                    echo '
                                            <td style="width: 25%;">
                                                    <div class="dashboard-items search-items" style="padding: 15px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                                                        <div style="width:100%;">
                                                            <div class="h3-search" style="margin-bottom: 10px;">
                                                                <span style="color: #0A76D8; font-weight: bold;">Reference:</span> MindCheck-' . $appoid . '
                                                            </div>
                                                            
                                                            <div class="h1-search" style="font-size: 18px; font-weight: bold; color: #2c4964; margin-bottom: 10px;">
                                                                ' . substr($title, 0, 30) . '
                                                            </div>
                                                            
                                                            <div class="h3-search" style="margin-bottom: 8px;">
                                                                <i class="fas fa-user-md" style="color: #0A76D8;"></i> 
                                                                <span style="font-weight: 500;">' . substr($docname, 0, 30) . '</span>
                                                            </div>
                                                            
                                                            <div class="h3-search" style="margin-bottom: 8px; color: #666;">
                                                                <i class="fas fa-stethoscope" style="color: #0A76D8;"></i> 
                                                                ' . $specialty . '
                                                            </div>
                                                            
                                                            <div class="h4-search" style="margin: 12px 0; padding: 8px; background-color: #f8f9fa; border-radius: 5px;">
                                                                <i class="far fa-calendar-alt" style="color: #0A76D8;"></i> 
                                                                <b>' . date("d M Y", strtotime($scheduledate)) . '</b>
                                                                <br>
                                                                <i class="far fa-clock" style="color: #0A76D8;"></i> 
                                                                <b>' . date("h:i A", strtotime($scheduletime)) . '</b>
                                                            </div>
                                                            
                                                            <div id="status-' . $appoid . '" style="margin-bottom: 15px;">
                                                                ' . displayAppointmentStatus($status) . '
                                                            </div>
                                                            
                                                            ' . ($status != 'cancelled' ? 
                                                                '<a href="?action=drop&id=' . $appoid . '&title=' . $title . '&doc=' . $docname . '" >
                                                                    <button class="login-btn btn-primary-soft btn" style="padding: 10px; width: 100%; border-radius: 5px; transition: all 0.3s;">
                                                                        <i class="fas fa-times-circle"></i> Cancel Booking
                                                                    </button>
                                                                </a>' : 
                                                                '<button disabled class="btn btn-secondary" style="padding: 10px; width: 100%; opacity: 0.7; border-radius: 5px;">
                                                                    <i class="fas fa-ban"></i> Cancelled
                                                                </button>') . '
                                                        </div>
                                                    </div>
                                                </td>';
                                                }
                                                echo "</tr>";

                                                // for ( $x=0; $x<$result->num_rows;$x++){
                                                //     $row=$result->fetch_assoc();
                                                //     $appoid=$row["appoid"];
                                                //     $scheduleid=$row["scheduleid"];
                                                //     $title=$row["title"];
                                                //     $docname=$row["docname"];
                                                //     $scheduledate=$row["scheduledate"];
                                                //     $scheduletime=$row["scheduletime"];
                                                //     $pname=$row["pname"];
                                                //     
                                                //     
                                                //     echo '<tr >
                                                //         <td style="font-weight:600;"> &nbsp;'.

                                                //         substr($pname,0,25)
                                                //         .'</td >
                                                //         <td style="text-align:center;font-size:23px;font-weight:500; color: var(--btnnicetext);">
                                                //         '.$apponum.'

                                                //         </td>
                                                //         <td>
                                                //         '.substr($title,0,15).'
                                                //         </td>
                                                //         <td style="text-align:center;;">
                                                //             '.substr($scheduledate,0,10).' @'.substr($scheduletime,0,5).'
                                                //         </td>

                                                //         <td style="text-align:center;">
                                                //             '.$appodate.'
                                                //         </td>

                                                //         <td>
                                                //         <div style="display:flex;justify-content: center;">

                                                //         <!--<a href="?action=view&id='.$appoid.'" class="non-style-link"><button  class="btn-primary-soft btn button-icon btn-view"  style="padding-left: 40px;padding-top: 12px;padding-bottom: 12px;margin-top: 10px;"><font class="tn-in-text">View</font></button></a>
                                                //        &nbsp;&nbsp;&nbsp;-->
                                                //        <a href="?action=drop&id='.$appoid.'&name='.$pname.'&session='.$title.'&apponum='.$apponum.'" class="non-style-link"><button  class="btn-primary-soft btn button-icon btn-delete"  style="padding-left: 40px;padding-top: 12px;padding-bottom: 12px;margin-top: 10px;"><font class="tn-in-text">Cancel</font></button></a>
                                                //        &nbsp;&nbsp;&nbsp;</div>
                                                //         </td>
                                                //     </tr>';

                                            }
                                        }

                                        ?>

                                    </tbody>

                                </table>
                            </div>
                        </center>
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <?php
    if ($_GET) {
        $id = $_GET["id"];
        $action = $_GET["action"];
        if ($action == 'booking-added') {

            echo '
            <div id="popup1" class="overlay">
                    <div class="popup">
                    <center>
                    <br><br>
                        <h2>Booking Successfully.</h2>
                        <a class="close" href="appointment.php">&times;</a>
                        <div class="content">
							Your Appointment number is ' . $id . '.<br><br>
                        </div>
                        <div style="display: flex;justify-content: center;">
							<a href="appointment.php" class="non-style-link"><button  class="btn-primary btn"  style="display: flex;justify-content: center;align-items: center;margin:10px;padding:10px;"><font class="tn-in-text">&nbsp;&nbsp;OK&nbsp;&nbsp;</font></button></a>
							<br><br><br><br>
                        </div>
                    </center>
            </div>
            </div>
            ';
        } elseif ($action == 'drop') {
            $title = $_GET["title"];
            $docname = $_GET["doc"];

            echo '
            <div id="popup1" class="overlay">
                    <div class="popup">
                    <center>
                        <h2>Are you sure?</h2>
                        <a class="close" href="appointment.php">&times;</a>
                        <div class="content">
                            You want to Cancel this Appointment?<br><br>
                            Session Name: &nbsp;<b>' . substr($title, 0, 40) . '</b><br>
                            doctor name&nbsp; : <b>' . substr($docname, 0, 40) . '</b><br><br>
                        </div>
                        <div style="display: flex;justify-content: center;">
							<a href="delete-appointment.php?id=' . $id . '" class="non-style-link"><button  class="btn-primary btn"  style="display: flex;justify-content: center;align-items: center;margin:10px;padding:10px;"<font class="tn-in-text">&nbsp;Yes&nbsp;</font></button></a>&nbsp;&nbsp;&nbsp;
						<a href="appointment.php" class="non-style-link"><button  class="btn-primary btn"  style="display: flex;justify-content: center;align-items: center;margin:10px;padding:10px;"><font class="tn-in-text">&nbsp;&nbsp;No&nbsp;&nbsp;</font></button></a>
                        </div>
                    </center>
            </div>
            </div>
            ';
        } elseif ($action == 'view') {
            $sqlmain = "select * from doctor where docid='$id'";
            $result = $database->query($sqlmain);
            $row = $result->fetch_assoc();
            $name = $row["docname"];
            $email = $row["docemail"];
            $spe = $row["specialties"];

            $spcil_res = $database->query("select sname from specialties where id='$spe'");
            $spcil_array = $spcil_res->fetch_assoc();
            $spcil_name = $spcil_array["sname"];
            $nic = $row['docnic'];
            $tele = $row['doctel'];
            echo '
            <div id="popup1" class="overlay">
                    <div class="popup">
                    <center>
                        <h2></h2>
                        <a class="close" href="doctors.php">&times;</a>
                        <div class="content">
                            eDoc Web App<br>
                        </div>
                        <div style="display: flex;justify-content: center;">
                        <table width="80%" class="sub-table scrolldown add-doc-form-container" border="0">
                            <tr>
                                <td>
                                    <p style="padding: 0;margin: 0;text-align: left;font-size: 25px;font-weight: 500;">View Details.</p><br><br>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    <label for="name" class="form-label">Name: </label>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    ' . $name . '<br><br>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    <label for="Email" class="form-label">Email: </label>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                ' . $email . '<br><br>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    <label for="nic" class="form-label">NIC: </label>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                ' . $nic . '<br><br>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    <label for="Tele" class="form-label">Telephone: </label>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                ' . $tele . '<br><br>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    <label for="spec" class="form-label">Specialties: </label>
                                </td>
                            </tr>
                            <tr>
                            <td class="label-td" colspan="2">
                            ' . $spcil_name . '<br><br>
                            </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <a href="doctors.php"><input type="button" value="OK" class="login-btn btn-primary-soft btn" ></a>
                                </td>
                            </tr>
                        </table>
                        </div>
                    </center>
                    <br><br>
            </div>
            </div>
            ';
        }
    }

    ?>
    <!-- ======= Appointment Section ======= -->
    <section id="appointment" class="appointment section-bg">
        <div class="container">

            <div class="section-title">
                <h2>Make an Appointment</h2>
                <p>Magnam dolores commodi suscipit. Necessitatibus eius consequatur ex aliquid fuga eum quidem. Sit sint consectetur velit. Quisquam quos quisquam cupiditate. Et nemo qui impedit suscipit alias ea. Quia fugiat sit in iste officiis commodi quidem hic quas.</p>
            </div>

            <form action="my-appointment.php" method="post" role="form" class="php-email-form">
                <div class="row">
                    <div class="col-md-4 form-group">
                        <input type="text" name="name" class="form-control" id="name" placeholder="Your Name" data-rule="minlen:4" data-msg="Please enter at least 4 chars">
                        <div class="validate"></div>
                    </div>
                    <div class="col-md-4 form-group mt-3 mt-md-0">
                        <input type="email" class="form-control" name="email" id="email" placeholder="Your Email" data-rule="email" data-msg="Please enter a valid email">
                        <div class="validate"></div>
                    </div>
                    <div class="col-md-4 form-group mt-3 mt-md-0">
                        <input type="tel" class="form-control" name="phone" id="phone" placeholder="Your Phone" data-rule="minlen:4" data-msg="Please enter at least 4 chars">
                        <div class="validate"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 form-group mt-3">
                        <input type="datetime" name="date" class="form-control datepicker" id="date" placeholder="Appointment Date" data-rule="minlen:4" data-msg="Please enter at least 4 chars">
                        <div class="validate"></div>
                    </div>
                    <div class="col-md-4 form-group mt-3">
                        <select name="department" id="department" class="form-select">
                            <option value="">Select Department</option>
                            <option value="Department 1">Department 1</option>
                            <option value="Department 2">Department 2</option>
                            <option value="Department 3">Department 3</option>
                        </select>
                        <div class="validate"></div>
                    </div>
                    <div class="col-md-4 form-group mt-3">
                        <select name="doctor" id="doctor" class="form-select">

                            <option value="">Select Doctor</option>
                            <?php

                            $list11 = $database->query("select  * from  doctor order by docname asc;");

                            for ($y = 0; $y < $list11->num_rows; $y++) {
                                $row00 = $list11->fetch_assoc();
                                $sn = $row00["docname"];
                                $id00 = $row00["docid"];
                                echo "<option value=" . $id00 . ">$sn</option><br/>";
                            };


                            ?>

                        </select>

                        <div class="validate"></div>
                    </div>
                </div>

                <div class="form-group mt-3">
                    <textarea class="form-control" name="message" rows="5" placeholder="Message (Optional)"></textarea>
                    <div class="validate"></div>
                </div>
                <div class="mb-3">
                    <div class="loading">Loading</div>
                    <div class="error-message"></div>
                    <div class="sent-message">Your appointment request has been sent successfully. Thank you!</div>
                </div>
                <div class="text-center"><button type="submit">Make an Appointment</button></div>
            </form>

        </div>
    </section>
    <!-- End Appointment Section -->
    </div>
</section><!-- breadcrumbs -->
<?php include("patient_footer.php"); ?>