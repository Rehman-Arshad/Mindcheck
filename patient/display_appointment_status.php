<?php
// Helper function to display appointment status with appropriate styling
function displayAppointmentStatus($status) {
    $statusColor = '';
    $statusBg = '';
    
    switch($status) {
        case 'pending':
            $statusColor = '#ffc107'; // warning/yellow
            $statusBg = 'rgba(255,193,7,0.1)';
            break;
        case 'confirmed':
            $statusColor = '#28a745'; // success/green
            $statusBg = 'rgba(40,167,69,0.1)';
            break;
        case 'cancelled':
            $statusColor = '#dc3545'; // danger/red
            $statusBg = 'rgba(220,53,69,0.1)';
            break;
        default:
            $statusColor = '#6c757d'; // secondary/gray
            $statusBg = 'rgba(108,117,125,0.1)';
    }
    
    echo "<div style='margin-top:10px;padding:8px;border-radius:5px;background-color:{$statusBg};color:{$statusColor};'>";
    echo "<b>Status: " . ucfirst($status) . "</b>";
    echo "</div>";
}
?>
