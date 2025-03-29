<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION["user"]) || $_SESSION["usertype"] != 'p') {
    header("location: ../login.php");
    exit();
}

// Get current page for active state
$current_page = basename($_SERVER['PHP_SELF'], ".php");
?>
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="dashboard.php">
            <i class='bx bx-brain text-primary'></i>
            <span>MindCheck</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page == 'dashboard' ? 'active' : ''; ?>" href="dashboard.php">
                        <i class='bx bx-home-alt'></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page == 'doctors' ? 'active' : ''; ?>" href="doctors.php">
                        <i class='bx bx-user'></i> Doctors
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page == 'schedule' ? 'active' : ''; ?>" href="schedule.php">
                        <i class='bx bx-calendar'></i> Schedule
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page == 'services' ? 'active' : ''; ?>" href="services.php">
                        <i class='bx bx-plus-medical'></i> Services
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page == 'contact' ? 'active' : ''; ?>" href="contact.php">
                        <i class='bx bx-envelope'></i> Contact
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-danger" href="logout.php" onclick="return confirm('Are you sure you want to logout?');">
                        <i class='bx bx-log-out'></i> Logout
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<style>
.navbar {
    padding: 15px 0;
    background: white !important;
    box-shadow: 0 2px 15px rgba(0,0,0,0.1);
}

.navbar-brand {
    font-size: 1.5em;
    font-weight: 700;
    color: #1977cc !important;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 10px;
}

.navbar-brand i {
    font-size: 1.8em;
}

.nav-link {
    color: #2c4964 !important;
    font-weight: 500;
    padding: 8px 16px !important;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 5px;
}

.nav-link i {
    font-size: 1.2em;
}

.nav-link:hover, .nav-link.active {
    color: #1977cc !important;
    background: rgba(25, 119, 204, 0.1);
    border-radius: 5px;
}

.nav-link.text-danger {
    color: #dc3545 !important;
}

.nav-link.text-danger:hover {
    background: rgba(220, 53, 69, 0.1) !important;
}

@media (max-width: 991px) {
    .navbar-nav {
        padding: 1rem 0;
    }
    .nav-link {
        padding: 10px !important;
    }
}
</style>
