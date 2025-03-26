<?php
    session_start();
    // Add cache control headers
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");

    if(isset($_SESSION["user"])){
        if(($_SESSION["user"])=="" or $_SESSION['usertype']!='d'){
            header("location: ../login.php");
        }else{
            $useremail=$_SESSION["user"];
        }
    }else{
        header("location: ../login.php");
    }
    
    include("../connection.php");
    include("includes/functions.php");
    
    $userrow = $database->query("select * from doctor where docemail='$useremail'");
    $userfetch=$userrow->fetch_assoc();
    $userid= $userfetch["docid"];
    $username=$userfetch["docname"];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/animations.css">  
    <link rel="stylesheet" href="../css/main.css">  
    <link rel="stylesheet" href="../css/admin.css">
        
    <title>Patients</title>
    <style>
        .dashbord-tables{
            animation: transitionIn-Y-over 0.5s;
        }
        .filter-container{
            animation: transitionIn-X  0.5s;
        }
        .sub-table{
            animation: transitionIn-Y-bottom 0.5s;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php include("../header.php"); ?>
        <div class="dash-body" style="margin-top: 15px">
            <table border="0" width="100%" style=" border-spacing: 0;margin:0;padding:0;margin-top:25px; ">
                <tr >
                    <td width="13%">
                    <a href="<?php echo getBackUrl(); ?>">   
                    <button class="login-btn btn-primary-soft btn btn-icon-back" style="padding-top:11px;padding-bottom:11px;margin-left:20px;width:125px">
                                <font class="tn-in-text">Back</font>
                    </button>                     
                    </td>
                    <td>
                        
                        <form action="" method="post" class="header-search">

                            <input type="search" name="search12" class="input-text header-searchbar" placeholder="Search Patient Name or Email" list="patient">&nbsp;&nbsp;
                            
                            <?php
                                echo '<datalist id="patient">';
                                $list11 = $database->query("select appointment.*, patient.*, schedule.*, department.description as deptDescription, semester.description as semesterDescription from appointment inner join patient on patient.pid=appointment.pid inner join schedule on schedule.scheduleid=appointment.scheduleid 
                                inner join department on department.id = patient.paddress
                                inner join semester on semester.id = patient.ptel 
                                where schedule.docid=$userid;");
                               //$list12= $database->query("select * from appointment inner join patient on patient.pid=appointment.pid inner join schedule on schedule.scheduleid=appointment.scheduleid where schedule.docid=1;");

                                for ($y=0;$y<$list11->num_rows;$y++){
                                    $row00=$list11->fetch_assoc();
                                    $d=$row00["pname"];
                                    $c=$row00["pemail"];
                                    echo "<option value='$d'><br/>";
                                    echo "<option value='$c'><br/>";
                                };

                            echo ' </datalist>';
?>
                            
                       
                            <input type="Submit" value="Search" name="search" class="login-btn btn-primary btn" style="padding-left: 25px;padding-right: 25px;padding-top: 10px;padding-bottom: 10px;">
                        
                        </form>
                        
                    </td>
                    <td width="15%">
                        <p style="font-size: 14px;color: rgb(119, 119, 119);padding: 0;margin: 0;text-align: right;">
                            Today's Date
                        </p>
                        <p class="heading-sub12" style="padding: 0;margin: 0; margin-left: 100px;">
                            <?php 
                        date_default_timezone_set('Asia/Karachi');

                        $date = date('Y-m-d');
                        echo $date;
                        ?>
                        </p>
                    </td>
                    <td width="10%">
                        <button  class="btn-label"  style="display: flex;justify-content: center;align-items: center;"><img src="../img/calendar.svg" width="100%"></button>
                    </td>
                </tr>
                <tr>
                    <td colspan="4" style="padding-top:10px;">
                        <p class="heading-main12" style="margin-left: 45px;font-size:18px;color:rgb(49, 49, 49)">My Patient (<?php $list11 = $database->query("select appointment.*, patient.*, schedule.*, department.description as deptDescription, semester.description as semesterDescription from appointment inner join patient on patient.pid=appointment.pid inner join schedule on schedule.scheduleid=appointment.scheduleid 
                            inner join department on department.id = patient.paddress
                            inner join semester on semester.id = patient.ptel 
                            where schedule.docid=$userid;"); echo $list11->num_rows; ?>)</p>
                    </td>
                </tr>
                <tr>
                    <td colspan="4" style="padding-top:0px;width: 100%;" >
                        <center>
                        <table class="filter-container" border="0" >
 
                        <form action="" method="post">
                        
                        <td  style="text-align: right;">
                        Show Details About : &nbsp;
                        </td>
                        <td width="30%">
                        <select name="showonly" id="" class="box filter-container-items" style="width:90% ;height: 37px;margin: 0;" >
                                    <option value="" disabled selected hidden>My patient Only</option><br/>
                                    <option value="my">My patient Only</option><br/>
                                    <option value="all">All patient</option><br/>
                        </select>
                    </td>
                    <td width="12%">
                        <input type="submit"  name="filter" value=" Filter" class=" btn-primary-soft btn button-icon btn-filter"  style="padding: 15px; margin :0;width:100%">
                        </form>
                    </td>

                    </tr>
                            </table>

                        </center>
                    </td>
                    
                </tr>
                  
                <tr>
                   <td colspan="4">
                       <center>
                        <div class="abc scroll">
                        <table width="93%" class="sub-table scrolldown"  style="border-spacing:0;">
                        <thead>
                        <tr>
                            <th class="table-headin">      
                            SAP ID
                            </th>
                            <th class="table-headin">
                            DEPARTMENT
                            </th>
                            <th class="table-headin">
                            SEMESTER
                            </th>
                            <th class="table-headin">
                            Email
                            </th>
                            <th class="table-headin">
                            Report
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        
                            <?php

                                
                                $result= $database->query("select appointment.*, patient.*, schedule.*, department.description as deptDescription, semester.description as semesterDescription from appointment inner join patient on patient.pid=appointment.pid inner join schedule on schedule.scheduleid=appointment.scheduleid 
                                inner join department on department.id = patient.paddress
                                inner join semester on semester.id = patient.ptel 
                                where schedule.docid=$userid;");
                                //echo $sqlmain;
                                if($result->num_rows==0){
                                    echo '<tr>
                                    <td colspan="4">
                                    <br><br><br><br>
                                    <center>
                                    <img src="../img/notfound.svg" width="25%">
                                    
                                    <br>
                                    <p class="heading-main12" style="margin-left: 45px;font-size:20px;color:rgb(49, 49, 49)">We  couldnt find anything related to your keywords !</p>
                                    <a class="non-style-link" href="patients.php"><button  class="login-btn btn-primary-soft btn"  style="display: flex;justify-content: center;align-items: center;margin-left:20px;">&nbsp; Show all patient &nbsp;</font></button>
                                    </a>
                                    </center>
                                    <br><br><br><br>
                                    </td>
                                    </tr>';
                                    
                                }
                                else{
                                for ( $x=0; $x<$result->num_rows;$x++){
                                    $row=$result->fetch_assoc();
                                    $pid=$row["pid"];
                                    $name=$row["pnic"];
                                    $email=$row["pemail"];
                                    $nic=$row["deptDescription"];
                                    $tel=$row["semesterDescription"];

                                    $sqlmain= "select * from reportform where patientid=$pid and doctorid='$useremail' order by id desc";
                                    $reportFormData= $database->query($sqlmain);
                                    $buttonText = "";
                                    if($reportFormData->num_rows==0)
                                    {
                                        echo '<tr>
                                        <td> &nbsp;'.
                                        $name
                                        .'</td>
                                        <td>
                                        '.$nic.'
                                        </td>
                                        <td>
                                        '.$tel.'
                                        </td>
                                        <td>
                                        '.$email.'
                                         </td>
                                        <td>
                                        <div style="display:flex;justify-content: center;">
                                            <a href="?action=report&id='.$pid.'" class="non-style-link"><button  class="btn-primary-soft btn button-icon btn-view"  style="padding-left: 40px;padding-top: 12px;padding-bottom: 12px;margin-top: 10px;"><font class="tn-in-text">Add Report</font></button></a>
                                        </div>
                                        </td>
                                    </tr>';
                                        
                                    }else{
                                        $reportFormRow=$reportFormData->fetch_assoc();
                                        echo '<tr>
                                        <td> &nbsp;'.
                                        $name
                                        .'</td>
                                        <td>
                                        '.$nic.'
                                        </td>
                                        <td>
                                        '.$tel.'
                                        </td>
                                        <td>
                                        '.$email.'
                                         </td>
                                        <td>
                                        <div style="display:flex;justify-content: center;">
                                            <a href="?action=view&id='.$reportFormRow["id"].'" class="non-style-link"><button  class="btn-primary-soft btn button-icon btn-view"  style="padding-left: 40px;padding-top: 12px;padding-bottom: 12px;margin-top: 10px;"><font class="tn-in-text">View</font></button></a>
                                        </div>
                                        </td>
                                    </tr>';
                                    }
                                    
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
    if($_GET){
        
        $id=$_GET["id"];
        $action=$_GET["action"];
        IF(isset($action) && $action == "view"){
            $sqlmain= "select * from reportform where id=$id;";
            $reportFormData= $database->query($sqlmain);
            $reportFormRow=$reportFormData->fetch_assoc();
            echo '
            <div id="popup1" class="overlay">
                <div class="popup">
                    <center>
                        <a class="close" href="patients.php">&times;</a>
                        <div class="content"></div>
                        <div style="display: flex;justify-content: center;">
                            <form name="reportForm" id="idForm" action="" method="POST">
                                <table width="80%" class="sub-table scrolldown add-doc-form-container" border="0">
                                    <tr>
                                        <td>
                                            <p style="padding: 0;margin: 0;text-align: left;font-size: 25px;font-weight: 500;">View Details</p><br><br>
                                        </td>
                                    </tr>
                                     <tr>
                                        <td class="label-td" colspan="2">
                                            <label for="patienttype" class="form-label">Patient Type: </label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-td" colspan="2">
                                            <input class="form-check-input" type="radio" disabled="disabled" name="patienttype" id="perpetrator" value="perpetrator" '.($reportFormRow["patienttype"] == 'perpetrator' ? "checked" : "").'>
                                              <label class="form-check-label" for="perpetrator">
                                                Perpetrator
                                              </label>
                                            <input class="form-check-input" type="radio" disabled="disabled" name="patienttype" id="victim" value="victim" '.($reportFormRow["patienttype"] == 'victim' ? "checked" : "").'>
                                              <label class="form-check-label" for="victim">
                                                Victims
                                              </label>
                                            <input class="form-check-input" type="radio" disabled="disabled" name="patienttype" id="bystander" value="bystander" '.($reportFormRow["patienttype"] == 'bystander' ? "checked" : "").'>
                                              <label class="form-check-label" for="bystander">
                                                Bystander
                                              </label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-td" colspan="2">
                                            <label for="casetype" class="form-label">Case Type </label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-td" colspan="2">
                                            <input class="form-check-input" type="radio" disabled="disabled" name="casetype" id="CriticalCase" value="CriticalCase" '.($reportFormRow["casetype"] == 'CriticalCase' ? "checked" : "").'>
                                              <label class="form-check-label" for="CriticalCase">
                                                Crtical Case
                                              </label>
                                            <input class="form-check-input" type="radio" disabled="disabled" name="casetype" id="CounselingCaseOnly" value="CounselingCaseOnly" '.($reportFormRow["casetype"] == 'CounselingCaseOnly' ? "checked" : "").'>
                                              <label class="form-check-label" for="CounselingCaseOnly">
                                                Counselling Case Only
                                        </td>
                                    </tr>
                                     <tr>
                                        <td class="label-td" colspan="2">
                                            <label for="socialMedia" class="form-label">Social Media </label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-td" colspan="2">
                                            <input class="form-check-input" type="radio" disabled="disabled" name="socialMedia" id="Instagram" value="Instagram" '.($reportFormRow["socialmedia"] == 'Instagram' ? "checked" : "").'>
                                              <label class="form-check-label" for="Instagram">
                                                Instagram
                                              </label>
                                            <input class="form-check-input" type="radio" disabled="disabled" name="socialMedia" id="Facebook" value="Facebook" '.($reportFormRow["socialmedia"] == 'Facebook' ? "checked" : "").'>
                                              <label class="form-check-label" for="Facebook">
                                                Facebook
                                               </label>
                                            <input class="form-check-input" type="radio" disabled="disabled" name="socialMedia" id="Whatsapp" value="Whatsapp" '.($reportFormRow["socialmedia"] == 'Whatsapp' ? "checked" : "").'>
                                              <label class="form-check-label" for="Whatsapp">
                                                Whatsapp
                                             </label>
                                            <input class="form-check-input" type="radio" disabled="disabled" name="socialMedia" id="Twitter" value="Twitter" '.($reportFormRow["socialmedia"] == 'Twitter' ? "checked" : "").'>
                                              <label class="form-check-label" for="Twitter">
                                                Twitter
                                             </label>
                                            <input class="form-check-input" type="radio" disabled="disabled" name="socialMedia" id="Others" value="Others" '.($reportFormRow["socialmedia"] == 'Others' ? "checked" : "").'>
                                              <label class="form-check-label" for="Others">
                                              Others
                                             </label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-td" colspan="2">
                                            <label for="Category" class="form-label">Category</label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-td" colspan="2">
                                            <input class="form-check-input" type="checkbox" disabled="disabled" name="Harrasment" value="DiscriminatoryHarrasment" id="DiscriminatoryHarrasment"  '.($reportFormRow["discriminatoryharrasment"] == 'DiscriminatoryHarrasment' ? "checked" : "").'>
                                              <label class="form-check-label" for="DiscriminatoryHarrasment">
                                              Discriminatory Harrasment
                                             </label>
                                             <input class="form-check-input" type="checkbox" disabled="disabled" name="SexualHarrasement" value="Sexual Harrasement" id="SexualHarrasement" '.($reportFormRow["sexualharrasement"] == 'Sexual Harrasement' ? "checked" : "").'>
                                              <label class="form-check-label" for="SexualHarrasement">
                                              Sexual Harrasement
                                             </label>
                                             <input class="form-check-input" type="checkbox" disabled="disabled" name="CyberBullying" value="Cyber Bullying" id="CyberBullying" '.($reportFormRow["cyberbullying"] == 'Cyber Bullying' ? "checked" : "").'>
                                              <label class="form-check-label" for="CyberBullying">
                                              Cyber Bullying
                                             </label>
                                              <input class="form-check-input" type="checkbox" disabled="disabled" name="RelationshipBreakdown" value="RelationshipBreakdown" id="RelationshipBreakdown" '.($reportFormRow["relationshipbreakdown"] == 'RelationshipBreakdown' ? "checked" : "").'>
                                              <label class="form-check-label" for="RelationshipBreakdown">
                                              Relationship Breakdown
                                             </label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-td" colspan="2">
                                            <label for="Effects" class="form-label">Effects</label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-td" colspan="2">
                                            <input class="form-check-input" type="checkbox" disabled="disabled" name="PanicAttacks" value="PanicAttacks" id="PanicAttacks" id="RelationshipBreakdown" '.($reportFormRow["panicattacks"] == 'PanicAttacks' ? "checked" : "").'>
                                              <label class="form-check-label" for="PanicAttacks">
                                              Panic Attacks
                                             </label>
                                             <input class="form-check-input" type="checkbox" disabled="disabled" name="Anxiety" value="Anxiety" id="Anxiety" id="RelationshipBreakdown" '.($reportFormRow["anxiety"] == 'Anxiety' ? "checked" : "").'>
                                              <label class="form-check-label" for="Anxiety">
                                              Anxiety
                                             </label>
                                             <input class="form-check-input" type="checkbox" disabled="disabled" name="Depression" value="Depression" id="Depression" id="RelationshipBreakdown" '.($reportFormRow["depression"] == 'Depression' ? "checked" : "").'>
                                              <label class="form-check-label" for="Depression">
                                              Depression
                                             </label>
                                        </td>
                                    </tr>
                                   <tr>
                                        <td class="label-td" colspan="2">
                                            <label for="casetype" class="form-label">Comments </label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-td" colspan="2">
                                            <input class="input-text form-check-input" type="text" name="Comments" id="Comments" readonly="readonly" value="'.$reportFormRow["comments"].'">
                                        </td>
                                    </tr>
                                </table>
                            </form>
                        </div>
                    </center>
                    <br><br>
                </div>
            </div>
            ';
        }
        ELSE IF(isset($action) && $action == "report"){
            echo '
            <div id="popup1" class="overlay">
                <div class="popup">
                    <center>
                        <a class="close" href="patients.php">&times;</a>
                        <div class="content"></div>
                        <div style="display: flex;justify-content: center;">
                            <form name="reportForm" id="idForm" action="" method="POST">
                                <input type="hidden" name="patientid" id="patientid" value="'.$id.'">
                                <input type="hidden" name="doctorid" id="doctorid" value="'.$useremail.'">
                                <table width="80%" class="sub-table scrolldown add-doc-form-container" border="0">
                                    <tr>
                                        <td>
                                            <p style="padding: 0;margin: 0;text-align: left;font-size: 25px;font-weight: 500;">Add Details</p><br><br>
                                        </td>
                                    </tr>
                                     <tr>
                                        <td class="label-td" colspan="2">
                                            <label for="patienttype" class="form-label">Patient Type: </label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-td" colspan="2">
                                            <input class="form-check-input" type="radio" name="patienttype" id="perpetrator" value="perpetrator">
                                              <label class="form-check-label" for="perpetrator">
                                                Perpetrator
                                              </label>
                                            <input class="form-check-input" type="radio" name="patienttype" id="victim" value="victim">
                                              <label class="form-check-label" for="victim">
                                                Victims
                                              </label>
                                            <input class="form-check-input" type="radio" name="patienttype" id="bystander" value="bystander">
                                              <label class="form-check-label" for="bystander">
                                                Bystander
                                              </label>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="label-td" colspan="2">
                                            <label for="casetype" class="form-label">Case Type </label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-td" colspan="2">
                                            <input class="form-check-input" type="radio" name="casetype" id="CriticalCase" value="CriticalCase">
                                              <label class="form-check-label" for="CriticalCase">
                                                Crtical Case
                                              </label>
                                            <input class="form-check-input" type="radio" name="casetype" id="CounselingCaseOnly" value="CounselingCaseOnly">
                                              <label class="form-check-label" for="CounselingCaseOnly">
                                                Counselling Case Only
                                             
                                        </td>
                                    </tr>


                                     <tr>
                                        <td class="label-td" colspan="2">
                                            <label for="socialMedia" class="form-label">Social Media </label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-td" colspan="2">
                                            <input class="form-check-input" type="radio" name="socialMedia" id="Instagram" value="Instagram">
                                              <label class="form-check-label" for="Instagram">
                                                Instagram
                                              </label>
                                            <input class="form-check-input" type="radio" name="socialMedia" id="Facebook" value="Facebook">
                                              <label class="form-check-label" for="Facebook">
                                                Facebook
                                               </label>
                                            <input class="form-check-input" type="radio" name="socialMedia" id="Whatsapp" value="Whatsapp">
                                              <label class="form-check-label" for="Whatsapp">
                                                Whatsapp
                                             </label>
                                            <input class="form-check-input" type="radio" name="socialMedia" id="Twitter" value="Twitter">
                                              <label class="form-check-label" for="Twitter">
                                                Twitter
                                             </label>
                                            <input class="form-check-input" type="radio" name="socialMedia" id="Others" value="Others">
                                              <label class="form-check-label" for="Others">
                                              Others
                                             </label>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="label-td" colspan="2">
                                            <label for="Category" class="form-label">Category</label>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="label-td" colspan="2">
                                            <input class="form-check-input" type="checkbox" name="Harrasment" value="DiscriminatoryHarrasment" id="DiscriminatoryHarrasment" >
                                              <label class="form-check-label" for="DiscriminatoryHarrasment">
                                              Discriminatory Harrasment
                                             </label>
                                             <input class="form-check-input" type="checkbox" name="SexualHarrasement" value="Sexual Harrasement" id="SexualHarrasement">
                                              <label class="form-check-label" for="SexualHarrasement">
                                              Sexual Harrasement
                                             </label>
                                             <input class="form-check-input" type="checkbox" name="CyberBullying" value="Cyber Bullying" id="CyberBullying">
                                              <label class="form-check-label" for="CyberBullying">
                                              Cyber Bullying
                                             </label>
                                              <input class="form-check-input" type="checkbox" name="RelationshipBreakdown" value="RelationshipBreakdown" id="RelationshipBreakdown">
                                              <label class="form-check-label" for="RelationshipBreakdown">
                                              Relationship Breakdown
                                             </label>
                                          
                                        </td>
                                    </tr>


                                    <tr>
                                        <td class="label-td" colspan="2">
                                            <label for="Effects" class="form-label">Effects</label>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="label-td" colspan="2">
                                            <input class="form-check-input" type="checkbox" name="PanicAttacks" value="PanicAttacks" id="PanicAttacks">
                                              <label class="form-check-label" for="PanicAttacks">
                                              Panic Attacks
                                             </label>
                                             <input class="form-check-input" type="checkbox" name="Anxiety" value="Anxiety" id="Anxiety">
                                              <label class="form-check-label" for="Anxiety">
                                              Anxiety
                                             </label>
                                             <input class="form-check-input" type="checkbox" name="Depression" value="Depression" id="Depression">
                                              <label class="form-check-label" for="Depression">
                                              Depression
                                             </label>
                                          
                                        </td>
                                    </tr>

                                   <tr>
                                        <td class="label-td" colspan="2">
                                            <label for="casetype" class="form-label">Comments </label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-td" colspan="2">
                                            <input class="input-text form-check-input" type="text" name="Comments" id="Comments" placeholder="Please write your comments here...">
                                             
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">
                                            <a href="patients.php" style="margin-left:470px; margin-top:50px;"><input type="submit" value="Add" name="addReport" class="login-btn btn-primary-soft btn"></a>
                                        </td>
                                    </tr>
                                </table>
                            </form>
                        </div>
                    </center>
                    <br><br>
                </div>
            </div>
            ';
        } 
    };

?>
</div>

</body>
</html>