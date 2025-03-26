<?php
function getBackUrl() {
    $current_page = basename($_SERVER['PHP_SELF'], '.php');
    
    // Define default back destinations for each page
    $back_destinations = [
        'settings' => 'index.php',
        'appointment' => 'index.php',
        'schedule' => 'index.php',
        'patient' => 'index.php',
        'delete-appointment' => 'appointment.php',
        'delete-session' => 'schedule.php',
        'edit-doc' => 'settings.php'
    ];
    
    // Return the default destination or index.php if not found
    return isset($back_destinations[$current_page]) ? $back_destinations[$current_page] : 'index.php';
}
?>
