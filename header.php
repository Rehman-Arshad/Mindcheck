<?php
// Get the current page name
$current_page = basename($_SERVER['PHP_SELF'], ".php");
?>
<link rel="stylesheet" href="../css/header-styles.css">
<script src="../js/header-scroll.js" defer></script>
<div class="menu">
    <table class="menu-container" border="0">
    <tr>
    <td style="padding:10px" colspan="2">
        <table border="0" class="profile-container">
            <tr>
                <td width="30%" style="padding-left:20px" >
                    <img src="../img/user.png" alt="" width="100%" style="border-radius:50%">
                </td>
                <td style="padding:0px;margin:0px;">
                    <p class="profile-title"><?php echo substr($username,0,13)  ?>..</p>
                    <p class="profile-subtitle"><?php echo substr($useremail,0,22)  ?></p>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <a href="../logout.php" ><input type="button" value="Log out" class="logout-btn btn-primary-soft btn"></a>
                </td>
            </tr>
    </table>
    </td>
    </tr>
    <tr class="menu-row" >
    <td class="menu-btn menu-icon-dashbord <?php echo ($current_page == 'index') ? 'menu-active menu-icon-dashbord-active' : ''; ?>">
        <a href="index.php" class="non-style-link-menu"><div><p class="menu-text">Dashboard</p></a></div>
    </td>
    </tr>
    <tr class="menu-row">
    <td class="menu-btn menu-icon-appoinment <?php echo ($current_page == 'appointment') ? 'menu-active menu-icon-appoinment-active' : ''; ?>">
        <a href="appointment.php" class="non-style-link-menu"><div><p class="menu-text">My Appointments</p></a></div>
    </td>
    </tr>

    <tr class="menu-row" >
    <td class="menu-btn menu-icon-session <?php echo ($current_page == 'schedule') ? 'menu-active menu-icon-schedule-active' : ''; ?>">
        <a href="schedule.php" class="non-style-link-menu"><div><p class="menu-text">My Sessions</p></div></a>
    </td>
    </tr>
    <tr class="menu-row" >
    <td class="menu-btn menu-icon-patient <?php echo ($current_page == 'patient') ? 'menu-active menu-icon-patient-active' : ''; ?>">
        <a href="patient.php" class="non-style-link-menu"><div><p class="menu-text">My patient</p></a></div>
    </td>
    </tr>
    <tr class="menu-row" >
    <td class="menu-btn menu-icon-settings <?php echo ($current_page == 'settings') ? 'menu-active menu-icon-settings-active' : ''; ?>">
        <a href="settings.php" class="non-style-link-menu"><div><p class="menu-text">Settings</p></a></div>
    </td>
    </tr>

    </table>
</div>