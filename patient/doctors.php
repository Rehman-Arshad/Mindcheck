<?php

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION["user"]) || $_SESSION["usertype"] != 'p') {
        header("location: ../login.php");
        exit;
    }

    include("../connection.php");

    // Get the base URL for the footer
    $base_url = '../';

    // Fetch all doctors
    $query = "SELECT * FROM doctor ORDER BY docname";
    $result = $database->query($query);

    // Handle doctor profile view
    $show_profile = false;
    $profile_data = null;
    if (isset($_GET['action']) && $_GET['action'] == 'view' && isset($_GET['id'])) {
        $id = $_GET['id'];
        $profile_query = "SELECT * FROM doctor WHERE docid='$id'";
        $profile_result = $database->query($profile_query);
        if ($profile_result && $profile_result->num_rows > 0) {
            $show_profile = true;
            $profile_data = $profile_result->fetch_assoc();
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Doctors - MindCheck</title>
    <link rel="stylesheet" href="../css/animations.css">
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/admin.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        .doctors-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .page-title {
            color: #2c4964;
            margin-bottom: 30px;
            text-align: center;
        }
        .doctors-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 25px;
            margin-top: 20px;
        }
        .doctor-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            transition: transform 0.3s ease;
        }
        .doctor-card:hover {
            transform: translateY(-5px);
        }
        .doctor-header {
            background: linear-gradient(45deg, #1977cc, #3291e6);
            color: white;
            padding: 20px;
            text-align: center;
        }
        .doctor-header h3 {
            margin: 0;
            font-size: 1.5em;
        }
        .doctor-body {
            padding: 20px;
        }
        .doctor-info {
            margin-bottom: 20px;
        }
        .doctor-info p {
            margin: 10px 0;
            color: #555;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .doctor-info i {
            color: #1977cc;
            font-size: 1.2em;
        }
        .doctor-actions {
            display: flex;
            gap: 10px;
        }
        .action-btn {
            flex: 1;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 500;
            text-align: center;
            text-decoration: none;
            transition: background-color 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
        }
        .schedule-btn {
            background: #1977cc;
            color: white;
        }
        .schedule-btn:hover {
            background: #1565c0;
        }
        .view-btn {
            background: #e3f2fd;
            color: #1977cc;
        }
        .view-btn:hover {
            background: #bbdefb;
        }
        .no-doctors {
            text-align: center;
            padding: 50px 20px;
            color: #666;
        }
        .no-doctors i {
            font-size: 3em;
            color: #1977cc;
            margin-bottom: 20px;
        }
        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
        }
        .modal.show {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .modal-content {
            background: white;
            padding: 30px;
            border-radius: 15px;
            width: 90%;
            max-width: 600px;
            position: relative;
        }
        .close-modal {
            position: absolute;
            top: 15px;
            right: 15px;
            font-size: 24px;
            cursor: pointer;
            color: #666;
        }
        .doctor-profile {
            text-align: center;
        }
        .doctor-profile h2 {
            color: #2c4964;
            margin-bottom: 20px;
        }
        .profile-info {
            text-align: left;
            margin-top: 20px;
        }
        .profile-info p {
            margin: 10px 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .profile-info i {
            color: #1977cc;
            font-size: 1.2em;
        }
        @media (max-width: 768px) {
            .doctors-grid {
                grid-template-columns: 1fr;
            }
            .doctor-actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <?php include("../header.php"); ?>

    <div class="doctors-container">
        <div class="page-title">
            <h1>Our Mental Health Professionals</h1>
            <p>Meet our team of experienced and dedicated mental health specialists</p>
        </div>

        <?php if($result && $result->num_rows > 0): ?>
            <div class="doctors-grid">
                <?php while($doctor = $result->fetch_assoc()): ?>
                    <div class="doctor-card">
                        <div class="doctor-header">
                            <h3>Dr. <?php echo htmlspecialchars($doctor['docname']); ?></h3>
                        </div>
                        <div class="doctor-body">
                            <div class="doctor-info">
                                <p>
                                    <i class='bx bx-briefcase-alt-2'></i>
                                    <?php echo htmlspecialchars($doctor['specialties']); ?>
                                </p>
                                <p>
                                    <i class='bx bx-time'></i>
                                    <?php echo htmlspecialchars($doctor['docexp']); ?> years experience
                                </p>
                                <p>
                                    <i class='bx bx-envelope'></i>
                                    <?php echo htmlspecialchars($doctor['docemail']); ?>
                                </p>
                            </div>
                            <div class="doctor-actions">
                                <a href="schedule.php?doctor=<?php echo $doctor['docid']; ?>" class="action-btn schedule-btn">
                                    <i class='bx bx-calendar-plus'></i> Schedule
                                </a>
                                <button onclick="viewDoctor(<?php echo $doctor['docid']; ?>)" class="action-btn view-btn">
                                    <i class='bx bx-user'></i> Profile
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="no-doctors">
                <i class='bx bx-user-x'></i>
                <h2>No Doctors Available</h2>
                <p>We are currently updating our directory. Please check back later.</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Doctor Profile Modal -->
    <div id="doctorModal" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal()">&times;</span>
            <div id="doctorProfile" class="doctor-profile">
                <!-- Profile content will be loaded here -->
            </div>
        </div>
    </div>

    <script>
    function viewDoctor(docId) {
        const modal = document.getElementById('doctorModal');
        const profileDiv = document.getElementById('doctorProfile');
        
        // Fetch doctor details using AJAX
        fetch(`get_doctor.php?id=${docId}`)
            .then(response => response.json())
            .then(doctor => {
                profileDiv.innerHTML = `
                    <h2>Dr. ${doctor.docname}</h2>
                    <div class="profile-info">
                        <p><i class='bx bx-briefcase-alt-2'></i> <strong>Specialization:</strong> ${doctor.specialties}</p>
                        <p><i class='bx bx-time'></i> <strong>Experience:</strong> ${doctor.docexp} years</p>
                        <p><i class='bx bx-envelope'></i> <strong>Email:</strong> ${doctor.docemail}</p>
                        <p><i class='bx bx-phone'></i> <strong>Phone:</strong> ${doctor.doctel}</p>
                    </div>
                `;
                modal.classList.add('show');
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error loading doctor profile');
            });
    }

    function closeModal() {
        document.getElementById('doctorModal').classList.remove('show');
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        const modal = document.getElementById('doctorModal');
        if (event.target == modal) {
            modal.classList.remove('show');
        }
    }
    </script>

    <?php 
    if($_GET){
        
        $id=$_GET["id"];
        $action=$_GET["action"];
        if(isset($action)=='drop'){
            $nameget=isset($_GET["name"]) ? $_GET["name"] : "";
            echo '
            <div id="popup1" class="overlay">
                    <div class="popup">
                    <center>
                        <h2>Are you sure?</h2>
                        <a class="close" href="doctors.php">&times;</a>
                        <div class="content">
                            You want to delete this record<br>('.substr($nameget,0,40).').
                            
                        </div>
                        <div style="display: flex;justify-content: center;">
                        <a href="delete-doctors.php?id='.$id.'" class="non-style-link"><button  class="btn-primary btn"  style="display: flex;justify-content: center;align-items: center;margin:10px;padding:10px;"<font class="tn-in-text">&nbsp;Yes&nbsp;</font></button></a>&nbsp;&nbsp;&nbsp;
                        <a href="doctors.php" class="non-style-link"><button  class="btn-primary btn"  style="display: flex;justify-content: center;align-items: center;margin:10px;padding:10px;"><font class="tn-in-text">&nbsp;&nbsp;No&nbsp;&nbsp;</font></button></a>

                        </div>
                    </center>
            </div>
            </div>
            ';
        }elseif($action=='view'){
            $sqlmain= "select * from doctor where docid='$id'";
            $result= $database->query($sqlmain);
            $row=$result->fetch_assoc();
            $name=$row["docname"];
            $email=$row["docemail"];
            $spe=$row["specialties"];
            
            $spcil_res= $database->query("select sname from specialties where id='$spe'");
            $spcil_array= $spcil_res->fetch_assoc();
            $spcil_name=$spcil_array["sname"];
            $nic=$row['docnic'];
            $tele=$row['doctel'];
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
                                    '.$name.'<br><br>
                                </td>
                                
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    <label for="Email" class="form-label">Email: </label>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                '.$email.'<br><br>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    <label for="nic" class="form-label">NIC: </label>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                '.$nic.'<br><br>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    <label for="Tele" class="form-label">Telephone: </label>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                '.$tele.'<br><br>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    <label for="spec" class="form-label">Specialties: </label>
                                    
                                </td>
                            </tr>
                            <tr>
                            <td class="label-td" colspan="2">
                            '.$spcil_name.'<br><br>
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
            </div>
            </div>
            ';
        }elseif($action=='session'){
            $name=$_GET["name"];
            echo '
            <div id="popup1" class="overlay">
                    <div class="popup">
                    <center>
                        <h2>Redirect to Doctors sessions?</h2>
                        <a class="close" href="doctors.php">&times;</a>
                        <div class="content">
                            You want to view All sessions by <br>('.substr($name,0,40).').
                            
                        </div>
                        <form action="schedule.php" method="post" style="display: flex">

                                <input type="hidden" name="search" value="'.$name.'">

                                
                        <div style="display: flex;justify-content:center;margin-left:45%;margin-top:6%;;margin-bottom:6%;">
                        
                        <input type="submit"  value="Yes" class="btn-primary btn"   >
                        
                        
                        </div>
                    </center>
            </div>
            </div>
            ';
        }
        }elseif(isset($action)=='edit'){
            $sqlmain= "select * from doctor where docid='$id'";
            $result= $database->query($sqlmain);
            $row=$result->fetch_assoc();
            $name=$row["docname"];
            $email=$row["docemail"];
            $spe=$row["specialties"];
            
            $spcil_res= $database->query("select sname from specialties where id='$spe'");
            $spcil_array= $spcil_res->fetch_assoc();
            $spcil_name=$spcil_array["sname"];
            $nic=$row['docnic'];
            $tele=$row['doctel'];

            $error_1=$_GET["error"];
                $errorlist= array(
                    '1'=>'<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Already have an account for this Email address.</label>',
                    '2'=>'<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Password Conformation Error! Reconform Password</label>',
                    '3'=>'<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;"></label>',
                    '4'=>"",
                    '0'=>'',

                );

            if($error_1!='4'){
                    echo '
                    <div id="popup1" class="overlay">
                            <div class="popup">
                            <center>
                            
                                <a class="close" href="doctors.php">&times;</a> 
                                <div style="display: flex;justify-content: center;">
                                <div class="abc">
                                <table width="80%" class="sub-table scrolldown add-doc-form-container" border="0">
                                <tr>
                                        <td class="label-td" colspan="2">'.
                                            $errorlist[$error_1]
                                        .'</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <p style="padding: 0;margin: 0;text-align: left;font-size: 25px;font-weight: 500;">Edit Doctor Details.</p>
                                        Doctor ID : '.$id.' (Auto Generated)<br><br>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-td" colspan="2">
                                            <form action="edit-doc.php" method="POST" class="add-new-form">
                                            <label for="Email" class="form-label">Email: </label>
                                            <input type="hidden" value="'.$id.'" name="id00">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-td" colspan="2">
                                        <input type="email" name="email" class="input-text" placeholder="Email Address" value="'.$email.'" required><br>
                                        </td>
                                    </tr>
                                    <tr>
                                        
                                        <td class="label-td" colspan="2">
                                            <label for="name" class="form-label">Name: </label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-td" colspan="2">
                                            <input type="text" name="name" class="input-text" placeholder="Doctor Name" value="'.$name.'" required><br>
                                        </td>
                                        
                                    </tr>
                                    
                                    <tr>
                                        <td class="label-td" colspan="2">
                                            <label for="nic" class="form-label">NIC: </label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-td" colspan="2">
                                            <input type="text" name="nic" class="input-text" placeholder="NIC Number" value="'.$nic.'" required><br>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-td" colspan="2">
                                            <label for="Tele" class="form-label">Telephone: </label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-td" colspan="2">
                                            <input type="tel" name="Tele" class="input-text" placeholder="Telephone Number" value="'.$tele.'" required><br>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-td" colspan="2">
                                            <label for="spec" class="form-label">Choose specialties: (Current'.$spcil_name.')</label>
                                            
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-td" colspan="2">
                                            <select name="spec" id="" class="box">';
                                                
                
                                                $list11 = $database->query("select  * from  specialties;");
                
                                                for ($y=0;$y<$list11->num_rows;$y++){
                                                    $row00=$list11->fetch_assoc();
                                                    $sn=$row00["sname"];
                                                    $id00=$row00["id"];
                                                    echo "<option value=".$id00.">$sn</option><br/>";
                                                };
                
                
                
                                                
                                echo     '       </select><br><br>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-td" colspan="2">
                                            <label for="password" class="form-label">Password: </label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-td" colspan="2">
                                            <input type="password" name="password" class="input-text" placeholder="Defind a Password" required><br>
                                        </td>
                                    </tr><tr>
                                        <td class="label-td" colspan="2">
                                            <label for="cpassword" class="form-label">Conform Password: </label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-td" colspan="2">
                                            <input type="password" name="cpassword" class="input-text" placeholder="Conform Password" required><br>
                                        </td>
                                    </tr>
                                    
                        
                                    <tr>
                                        <td colspan="2">
                                            <input type="reset" value="Reset" class="login-btn btn-primary-soft btn" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        
                                            <input type="submit" value="Save" class="login-btn btn-primary btn">
                                        </td>
                        
                                    </tr>
                                
      
                                </form>
                                    </tr>
                                </table>
                                </div>
                                </div>
                            </center>
                            <br><br>
                    </div>
                    </div>
                    ';
        }else{
            echo '
                <div id="popup1" class="overlay">
                        <div class="popup">
                        <center>
                        <br><br><br><br>
                            <h2>Edit Successfully!</h2>
                            <a class="close" href="doctors.php">&times;</a>
                            <div class="content">
                                
                                
                            </div>
                            <div style="display: flex;justify-content: center;">
                            
                            <a href="doctors.php" class="non-style-link"><button  class="btn-primary btn"  style="display: flex;justify-content: center;align-items: center;margin:10px;padding:10px;"><font class="tn-in-text">&nbsp;&nbsp;OK&nbsp;&nbsp;</font></button></a>

                            </div>
                            <br><br>
                        </center>
                </div>
                </div>
    ';

        }; 
    };

?>
</div>

</body>
</html>
<?php include($base_url."footer.php"); ?>