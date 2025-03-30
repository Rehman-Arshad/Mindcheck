<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION["user"]) || $_SESSION["usertype"] != 'd') {
    header("location: ../login.php");
    exit();
}

include_once("../connection.php");

// Get doctor info
$useremail = $_SESSION["user"];
$userrow = $database->query("SELECT * FROM doctor WHERE docemail='$useremail'");
$userfetch = $userrow->fetch_assoc();
$username = $userfetch["docname"] ?? 'Doctor';

// Get current page for active nav highlighting
$current_page = basename($_SERVER['PHP_SELF']);
?>

<style>
    :root {
        --primary: #1a73e8;
    }

    body {
        margin: 0;
        padding: 0;
        font-family: system-ui, -apple-system, sans-serif;
        background: #f8f9fa;
    }

    .navbar {
        background: var(--primary);
        padding: 0.75rem 1rem;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        z-index: 1000;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .navbar-container {
        max-width: 1400px;
        margin: 0 auto;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0 1rem;
    }

    .navbar-brand {
        color: white;
        font-size: 1.5rem;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        white-space: nowrap;
    }

    .navbar-brand i {
        font-size: 1.75rem;
    }

    .nav-menu {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin: 0;
        padding: 0;
        list-style: none;
    }

    .nav-item {
        margin: 0;
    }

    .nav-link {
        color: rgba(255,255,255,0.8);
        text-decoration: none;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .nav-link i {
        font-size: 1.25rem;
    }

    .nav-link:hover {
        color: white;
        background: rgba(255,255,255,0.1);
    }

    .nav-link.active {
        color: white;
        background: rgba(255,255,255,0.2);
    }

    .nav-link.logout {
        color: #dc3545;
        background: white;
    }

    .nav-link.logout:hover {
        background: #dc3545;
        color: white;
    }

    .main-content {
        margin-top: 80px;
        padding: 2rem;
        min-height: calc(100vh - 80px);
        background: #f8f9fa;
    }

    @media (max-width: 768px) {
        .nav-menu {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: var(--primary);
            padding: 0.5rem;
            justify-content: space-around;
            box-shadow: 0 -2px 4px rgba(0,0,0,0.1);
        }

        .nav-link {
            flex-direction: column;
            font-size: 0.75rem;
            padding: 0.5rem;
            text-align: center;
        }

        .nav-link i {
            font-size: 1.5rem;
        }

        .main-content {
            margin-bottom: 70px;
        }

        .navbar-brand span {
            display: none;
        }
    }
</style>

<nav class="navbar">
    <div class="navbar-container">
        <a href="index.php" class="navbar-brand">
            <i class='bx bx-brain'></i>
            <span>MindCheck</span>
        </a>
        <ul class="nav-menu">
            <li class="nav-item">
                <a href="index.php" class="nav-link <?php echo $current_page === 'index.php' ? 'active' : ''; ?>">
                    <i class='bx bx-home'></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="appointment.php" class="nav-link <?php echo $current_page === 'appointment.php' ? 'active' : ''; ?>">
                    <i class='bx bx-calendar'></i>
                    <span>Appointments</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="patient.php" class="nav-link <?php echo $current_page === 'patient.php' ? 'active' : ''; ?>">
                    <i class='bx bx-group'></i>
                    <span>Patients</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="schedule.php" class="nav-link <?php echo $current_page === 'schedule.php' ? 'active' : ''; ?>">
                    <i class='bx bx-time'></i>
                    <span>Schedule</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="profile.php" class="nav-link <?php echo $current_page === 'profile.php' ? 'active' : ''; ?>">
                    <i class='bx bx-user'></i>
                    <span>Profile</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="../logout.php" class="nav-link logout" onclick="return confirm('Are you sure you want to logout?')">
                    <i class='bx bx-log-out'></i>
                    <span>Logout</span>
                </a>
            </li>
        </ul>
    </div>
</nav>

<div class="main-content">
    <div class="container">
