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

    include("patient_header_info.php");
    
    include("patient_header.php");
	
?>
<section class="breadcrumbs">
    <div class="container">
        
        <div class="dash-body" style="margin-top: 15px">
            <table border="0" width="100%" style=" border-spacing: 0;margin:0;padding:0;" >
				<tr >
					<td colspan="1" class="nav-bar" >
						<p style="font-size: 23px;padding-left:12px;font-weight: 600;margin-left:20px;">Home</p>
					</td>
					<td width="25%">
					</td>
					<td width="15%">
						<p style="font-size: 14px;color: rgb(119, 119, 119);padding: 0;margin: 0;text-align: right;">
							Today's Date
						</p>
						<p class="heading-sub12" style="padding: 0;margin: 0; margin-left: 70px">
						<?php 
							date_default_timezone_set('Asia/Karachi');

							$today = date('Y-m-d');
							echo $today;

							$patientrow = $database->query("select * from patient;");
							$doctorrow = $database->query("select * from doctor;");
							$appointmentrow = $database->query("select * from appointment where appodate>='$today';");
							$schedulerow = $database->query("select * from schedule where scheduledate='$today';");
						?>
						</p>
					</td>
					<td width="10%">
						<button  class="btn-label"  style="display: flex;justify-content: center;align-items: center;"><img src="../img/calendar.svg" width="100%"></button>
					</td>
				</tr>
                <tr>
                    <td colspan="4" >
                        
                    <center>
                    <table class="filter-container doctor-header patient-header" style="background-image: url(..////img//patient-dashboard.jpg);border: none;width:95%" border="0" >
                    <tr>
                        <td style="color: black; font-weight: bolder;">
                        <div class="container" style="background-color: #2a9df4; padding: 20px;">
                        <h3 style="color: white; text-align: center;">Welcome!</h3>
                        </div>


                           
                            <h1><?php echo $username  ?></h1>
                            <p style="color: white; font-weight:200; text-align:center">Haven't any idea about Doctors? no problem let's jumping to 
                                <a href="doctors.php" class="non-style-link"><b style="color: white">"All Doctors"</b></a> section or 
                                <a href="schedule.php" class="non-style-link"><b style="color: white">"Sessions"</b> </a><br>
                                Track your past and future appointments history.<br>Also find out the expected arrival time of your doctor or medical consultant.<br><br>
                            </p>
                            
                            <h3 style="color: white; text-align:center; font-weight:bold">Channel a Doctor Here</h3>
                            <form action="schedule.php" method="post" style="display: flex; position:relative; left:21%; margin-bottom:20px">

                                <input type="search" name="search" class="input-text " placeholder="Search Doctor and We will Find The Session Available" list="doctors" style="width:45%;">&nbsp;&nbsp;
                                
                                <?php
                                    echo '<datalist id="doctors">';
                                    $list11 = $database->query("select  docname,docemail from  doctor;");
    
                                    for ($y=0;$y<$list11->num_rows;$y++){
                                        $row00=$list11->fetch_assoc();
                                        $d=$row00["docname"];
                                        echo "<option value='$d'><br/>";
                                    };
    
                                echo ' </datalist>';
    ?>
                                
                                <input type="Submit" value="Search" class="login-btn btn-primary btn" style="padding-left: 25px;padding-right: 25px;padding-top: 10px;padding-bottom: 10px;">

                            <br>
                            <br>
                            
                        </td>
                    </tr>
                    </table>
                    </center>
                    
                </td>
                </tr>
                <tr>
                    <td colspan="4">
                        <table border="0" width="100%"">
                            <tr>
                                <td width="50%">
                                    <center>
                                        <table class="filter-container" style="border: none;" border="0">
                                            <tr>
                                                <td colspan="4">
                                                    <p style="font-size: 20px;font-weight:600;padding-left: 12px;">Status</p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="width: 25%;">
                                                    <div  class="dashboard-items"  style="padding:20px;margin:auto;width:95%;display: flex">
                                                        <div>
															<div class="h1-dashboard">
																<?php    echo $doctorrow->num_rows  ?>
															</div><br>
															<div class="h3-dashboard">
																All Doctors &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
															</div>
                                                        </div>
                                                            <div class="btn-icon-back dashboard-icons" style="background-image: url('../img/icons/doctors-hover.svg');"></div>
                                                    </div>
                                                </td>
                                                <td style="width: 25%;">
                                                    <div  class="dashboard-items"  style="padding:20px;margin:auto;width:95%;display: flex;">
                                                        <div>
															<div class="h1-dashboard">
																<?php    echo $patientrow->num_rows  ?>
															</div><br>
															<div class="h3-dashboard">
																All patient &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
															</div>
                                                        </div>
                                                            <div class="btn-icon-back dashboard-icons" style="background-image: url('../img/icons/patient-hover.svg');"></div>
                                                    </div>
                                                </td>
                                                </tr>
                                                <tr>
                                                <td style="width: 25%;">
                                                    <div  class="dashboard-items"  style="padding:20px;margin:auto;width:95%;display: flex; ">
                                                        <div>
															<div class="h1-dashboard" >
																<?php    echo $appointmentrow ->num_rows  ?>
															</div><br>
															<div class="h3-dashboard" >
																NewBooking &nbsp;&nbsp;
															</div>
                                                        </div>
                                                            <div class="btn-icon-back dashboard-icons" style="margin-left: 0px;background-image: url('../img/icons/book-hover.svg');"></div>
                                                    </div>
                                                    
                                                </td>

                                                <td style="width: 25%;">
                                                    <div  class="dashboard-items"  style="padding:20px;margin:auto;width:95%;display: flex;padding-top:21px;padding-bottom:21px;">
                                                        <div>
															<div class="h1-dashboard">
																<?php    echo $schedulerow ->num_rows  ?>
															</div><br>
															<div class="h3-dashboard" style="font-size: 15px">
																Today Sessions
															</div>
                                                        </div>
                                                            <div class="btn-icon-back dashboard-icons" style="background-image: url('../img/icons/session-iceblue.svg');"></div>
                                                    </div>
                                                </td>
                                                
                                            </tr>
                                        </table>
                                    </center>

                                </td>
                                <td>

                                    <p style="font-size: 20px;font-weight:600;padding-left: 40px;" class="anime">Your Upcoming Booking</p>
                                    <center>
                                        <div class="abc scroll" style="height: 250px;padding: 0;margin: 0;">
                                        <table width="85%" class="sub-table scrolldown" border="0" >
											<thead>
												<tr>
													<th class="table-headin">Appoint. Number</th>
													<th class="table-headin">Session Title</th>
													<th class="table-headin">Doctors</th>
													<th class="table-headin">Sheduled Date & Time</th>
												</tr>
											</thead>
                                        <tbody>
                                        
                                            <?php
                                            $nextweek=date("Y-m-d",strtotime("+1 week"));
                                                $sqlmain= "select * from schedule inner join appointment on schedule.scheduleid=appointment.scheduleid inner join patient on patient.pid=appointment.pid inner join doctor on schedule.docid=doctor.docid  where  patient.pid=$userid  and schedule.scheduledate>='$today' order by schedule.scheduledate asc";
                                                //echo $sqlmain;
                                                $result= $database->query($sqlmain);
                
                                                if($result->num_rows==0){
                                                    echo '<tr>
                                                    <td colspan="4">
                                                    <br><br><br><br>
                                                    <center>
                                                    <img src="../img/notfound.svg" width="25%">
                                                    
                                                    <br>
                                                    <p class="heading-main12" style="margin-left: 45px;font-size:20px;color:rgb(49, 49, 49)">Nothing to show here!</p>
                                                    <a class="non-style-link" href="schedule.php"><button  class="login-btn btn-primary-soft btn"  style="display: flex;justify-content: center;align-items: center;margin-left:20px;">&nbsp; Channel a Doctor &nbsp;</font></button>
                                                    </a>
                                                    </center>
                                                    <br><br><br><br>
                                                    </td>
                                                    </tr>';
                                                    
                                                }
                                                else{
                                                for ( $x=0; $x<$result->num_rows;$x++){
                                                    $row=$result->fetch_assoc();
                                                    $scheduleid=$row["scheduleid"];
                                                    $title=$row["title"];
                                                    $apponum=$row["apponum"];
                                                    $docname=$row["docname"];
                                                    $scheduledate=$row["scheduledate"];
                                                    $scheduletime=$row["scheduletime"];
                                                   
                                                    echo '<tr>
                                                        <td style="padding:30px;font-size:25px;font-weight:700;"> &nbsp;'.
                                                        $apponum
                                                        .'</td>
                                                        <td style="padding:20px;"> &nbsp;'.
                                                        substr($title,0,30)
                                                        .'</td>
                                                        <td>
                                                        '.substr($docname,0,20).'
                                                        </td>
                                                        <td style="text-align:center;">
                                                            '.substr($scheduledate,0,10).' '.substr($scheduletime,0,5).'
                                                        </td>                                                       
                                                    </tr>';
                                                    
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
                    </td>
                <tr>
            </table>
        </div>
    </div>

</section><!-- breadcrumbs -->
 <br><br> <section id="about" class="about">
      <div class="container-fluid">

        <div class="row">
          <div class="col-xl-5 col-lg-6 video-box d-flex justify-content-center align-items-stretch position-relative">
            <!-- <a href="https://www.youtube.com/watch?v=jDDaplaOz7Q" class="glightbox play-btn mb-4"></a> -->
            
          </div>

          <div class="col-xl-7 col-lg-6 icon-boxes d-flex flex-column align-items-stretch justify-content-center py-5 px-lg-5">
            <h3>Learn About Us</h3>
            <p>"University of Education : Guiding Futures, Inspiring Excellence. Our dedicated team of experienced doctors and educators is committed to nurturing the potential of our students, providing invaluable guidance, and shaping the leaders of tomorrow. Explore our journey and be a part of our mission to transform dreams into achievements."</p>

            <div class="icon-box">
              <div class="icon"><i class="bx bx-fingerprint"></i></div>
              <h4 class="title"><a href="">Privacy is Our Priority</a></h4>
              <p class="description">"Your privacy is our priority. We are committed to safeguarding your personal information and ensuring a confidential, trust-based experience."</p>
            </div>

            <div class="icon-box">
              <div class="icon"><i class="bx bx-gift"></i></div>
              <h4 class="title"><a href="">Healing and Enpowerment of Youth</a></h4>
              <p class="description">"You hold the power to heal, and together, we'll journey towards your inner strength and well-being."</p>
            </div>

            <div class="icon-box">
              <div class="icon"><i class="bx bx-atom"></i></div>
              <h4 class="title"><a href="">Track Records</a></h4>
              <p class="description">"Efficient record tracking empowers our patient to monitor their progress, set goals, and stay accountable on their journey to success."</p>
            </div>

          </div>
        </div>

      </div>
    </section><!-- End About Section -->



    <section id="services" class="services">
      <div class="container">

        <div class="section-title">
          <h2>Services</h2>
          <p>What we provide to our patient</p>
        </div>

        <div class="row">
          <div class="col-lg-4 col-md-6 d-flex align-items-stretch">
            <div class="icon-box">
              <div class="icon"><i class="fas fa-heartbeat"></i></div>
              <h4><a href="">Health Isuues</a></h4>
              <p>"Nurturing potential, one at a time."</p>
            </div>
          </div>

          <div class="col-lg-4 col-md-6 d-flex align-items-stretch mt-4 mt-md-0">
            <div class="icon-box">
              <div class="icon"><i class="fas fa-pills"></i></div>
              <h4><a href="">Legal Advices</a></h4>
              <p>"Guiding you through legal complexities, one consultation when required."</p>
            </div>
          </div>

          <div class="col-lg-4 col-md-6 d-flex align-items-stretch mt-4 mt-lg-0">
            <div class="icon-box">
              <div class="icon"><i class="fas fa-hospital-user"></i></div>
              <h4><a href="">Seminars & Webinars</a></h4>
              <p>Seminars and Webinars conducted on basis of cases reports.</p>
            </div>
          </div>

    

        

        </div>

      </div>
    </section><!-- End Services Section -->



     <!-- ======= Contact Section ======= -->
    <section id="contact" class="contact">
      <div class="container">

        <div class="section-title">
          <h2>Contact</h2>
         
        </div>
      </div>

     

      <div class="container">
        <div class="row mt-5">

          <div class="col-lg-4">
            <div class="info">
              <div class="address">
                <i class="bi bi-geo-alt"></i>
                <h4>Location:</h4>
                <p>University of Education, Lahore</p>
              </div>

              <div class="email">
                <i class="bi bi-envelope"></i>
                <h4>Email:</h4>
                <p>contact@ue.edu.pk</p>
              </div>

              <div class="phone">
                <i class="bi bi-phone"></i>
                <h4>Call:</h4>
                <p>+92-42-992622</p>
              </div>

            </div>

          </div>

          <div class="col-lg-8 mt-5 mt-lg-0">

            <form action="forms/contact.php" method="post" role="form" class="php-email-form">
              <div class="row">
                <div class="col-md-6 form-group">
                  <input type="text" name="name" class="form-control" id="name" placeholder="Your Name" required>
                </div>
                <div class="col-md-6 form-group mt-3 mt-md-0">
                  <input type="email" class="form-control" name="email" id="email" placeholder="Your Email" required>
                </div>
              </div>
              <div class="form-group mt-3">
                <input type="text" class="form-control" name="subject" id="subject" placeholder="Subject" required>
              </div>
              <div class="form-group mt-3">
                <textarea class="form-control" name="message" rows="5" placeholder="Message" required></textarea>
              </div>
             <!--  <div class="my-3">
                <div class="loading">Loading</div>
                <div class="error-message"></div>
                <div class="sent-message">Your message has been sent. Thank you!</div>
              </div> -->
              <div class="text-center" ><button type="submit" style="margin-left: 604px; margin-top: 35px; background-color: #1c84e3; border-color:#1c84e3;color: white; border-radius: 20px; left: 20px;">Send Message</button></div>
            </form>

          </div>

        </div>

      </div>
    </section><!-- End Contact Section -->

<?php include("patient_footer.php"); ?>