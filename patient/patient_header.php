<?php 
if (!function_exists('base_url')) {
    function base_url($atRoot=FALSE, $atCore=FALSE, $parse=FALSE){
        if (isset($_SERVER['HTTP_HOST'])) {
            $http = isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off' ? 'https' : 'http';
            $hostname = $_SERVER['HTTP_HOST'];
            $dir =  str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);

            $core = preg_split('@/@', str_replace($_SERVER['DOCUMENT_ROOT'], '', realpath(dirname(__FILE__))),   Null, PREG_SPLIT_NO_EMPTY);
            $core = $core[0];

            $tmplt = $atRoot ? ($atCore ? "%s://%s/%s/" : "%s://%s/") : ($atCore ? "%s://%s/%s/" : "%s://%s%s");
            $end = $atRoot ? ($atCore ? $core : $hostname) : ($atCore ? $core : $dir);
            $base_url = sprintf( $tmplt, $http, $hostname, $end );
        }
        else $base_url = 'http://localhost/';

        if ($parse) {
            $base_url = parse_url($base_url);
            if (isset($base_url['path'])) if ($base_url['path'] == '/') $base_url['path'] = '';
        }

        return $base_url;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <meta content="" name="description">
  <meta content="" name="keywords">
  <title>MindCheck</title>

  <!-- Favicons -->
  <link href="<?php echo base_url(TRUE); ?>MindCheck/patient_assets/img/favicon.png" rel="icon">
  <link href="<?php echo base_url(TRUE); ?>MindCheck/patient_assets/img/favicon.png" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Raleway:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="<?php echo base_url(TRUE); ?>MindCheck/patient_assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
  <link href="<?php echo base_url(TRUE); ?>MindCheck/patient_assets/vendor/animate.css/animate.min.css" rel="stylesheet">
  <link href="<?php echo base_url(TRUE); ?>MindCheck/patient_assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="<?php echo base_url(TRUE); ?>MindCheck/patient_assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="<?php echo base_url(TRUE); ?>MindCheck/patient_assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="<?php echo base_url(TRUE); ?>MindCheck/patient_assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="<?php echo base_url(TRUE); ?>MindCheck/patient_assets/vendor/remixicon/remixicon.css" rel="stylesheet">
  <link href="<?php echo base_url(TRUE); ?>MindCheck/patient_assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="<?php echo base_url(TRUE); ?>MindCheck/patient_assets/css/style.css" rel="stylesheet">
  
  <link rel="stylesheet" href="../css/animations.css">
  <link rel="stylesheet" href="../css/main.css">
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
  
  <!-- Vendor JS Files -->
  <script src="<?php echo base_url(TRUE); ?>MindCheck/patient_assets/vendor/purecounter/purecounter_vanilla.js"></script>
  <!--<script src="<?php echo base_url(TRUE); ?>MindCheck/patient_assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>-->
  <script src="<?php echo base_url(TRUE); ?>MindCheck/patient_assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="<?php echo base_url(TRUE); ?>MindCheck/patient_assets/vendor/swiper/swiper-bundle.min.js"></script>
  <script src="<?php echo base_url(TRUE); ?>MindCheck/patient_assets/vendor/php-email-form/validate.js"></script>

  <!--  Main JS File -->
  <script src="<?php echo base_url(TRUE); ?>MindCheck/patient_assets/js/main.js"></script>

</head>

<body>


  <!-- ======= Header ======= -->
  <header id="header" class="fixed-top" >
    <div class="container d-flex align-items-center">

      <h1 class="logo me-auto"><a href="<?php echo base_url(TRUE); ?>MindCheck/index.php">MindCheck</a></h1>
      <nav id="navbar" class="navbar order-last order-lg-0">
        <ul>
          <li><a class="nav-link scrollto" href="<?php echo base_url(TRUE); ?>MindCheck/index.php#hero">Home</a></li>
           <li><a class="nav-link scrollto" href="<?php echo base_url(TRUE); ?>MindCheck/patient/doctors.php">Doctors</a></li>
           <li><a href="<?php echo base_url(TRUE); ?>MindCheck/patient/schedule.php">Scheduled Sessions</a></li>
          <li><a class="nav-link scrollto" href="<?php echo base_url(TRUE); ?>MindCheck/index.php#services">Services</a></li>
          <li><a class="nav-link scrollto" href="<?php echo base_url(TRUE); ?>MindCheck/patient/contact.php">Contact</a></li>   
        </ul>
      </nav>  
      <a href="<?php echo base_url(TRUE); ?>MindCheck/patient/appointment.php" class="appointment-btn scrollto"><span class="d-none d-md-inline">Make an</span> Appointment</a>
    </div>
  </header><!-- End Header -->
  