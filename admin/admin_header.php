<?php
session_start();
if(!isset($_SESSION["user"]) || $_SESSION["usertype"] != 'a') {
    header("location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MindCheck Admin</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #1977cc;
            --primary-light: rgba(25, 119, 204, 0.1);
            --secondary: #3291e6;
            --text-dark: #2c4964;
            --text-light: #6c757d;
            --light: #f8f9fa;
            --white: #ffffff;
        }

        body {
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
            color: var(--text-dark);
            background-color: #f6f9ff;
        }

        .navbar {
            background: var(--white);
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
            padding: 0.75rem 1.5rem;
        }

        .navbar-brand {
            color: var(--primary);
            font-size: 1.5rem;
            font-weight: 600;
            letter-spacing: -0.5px;
        }

        .navbar-brand:hover {
            color: var(--secondary);
        }

        .nav-link {
            color: var(--text-dark);
            font-weight: 500;
            padding: 0.75rem 1rem !important;
            position: relative;
        }

        .nav-link:hover, .nav-link.active {
            color: var(--primary);
        }

        .nav-link.active::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 1rem;
            right: 1rem;
            height: 2px;
            background: var(--primary);
        }

        .navbar-nav .nav-link i {
            font-size: 1.1rem;
            margin-right: 0.5rem;
            vertical-align: -3px;
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.5rem 1rem;
            color: var(--text-dark);
            text-decoration: none;
        }

        .user-menu:hover {
            color: var(--primary);
            background: var(--light);
            border-radius: 0.5rem;
        }

        .user-menu i {
            font-size: 1.5rem;
        }

        .dropdown-menu {
            border: none;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            border-radius: 0.5rem;
            padding: 0.5rem;
        }

        .dropdown-item {
            color: var(--text-dark);
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
        }

        .dropdown-item:hover {
            color: var(--primary);
            background: var(--light);
        }

        .dropdown-item i {
            margin-right: 0.5rem;
            font-size: 1.1rem;
            vertical-align: -3px;
        }

        @media (max-width: 991.98px) {
            .navbar-collapse {
                background: var(--white);
                padding: 1rem;
                border-radius: 0.5rem;
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
                margin-top: 1rem;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">MindCheck</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>" href="index.php">
                            <i class='bx bxs-dashboard'></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'patients.php' ? 'active' : ''; ?>" href="patients.php">
                            <i class='bx bxs-user-detail'></i>Patients
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'doctors.php' ? 'active' : ''; ?>" href="doctors.php">
                            <i class='bx bxs-user-rectangle'></i>Doctors
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'schedule.php' ? 'active' : ''; ?>" href="schedule.php">
                            <i class='bx bx-calendar'></i>Schedule
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'appointment.php' ? 'active' : ''; ?>" href="appointment.php">
                            <i class='bx bx-calendar-check'></i>Appointments
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'assessment.php' ? 'active' : ''; ?>" href="assessment.php">
                            <i class='bx bx-clipboard'></i>Assessment
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'reports.php' ? 'active' : ''; ?>" href="reports.php">
                            <i class='bx bx-line-chart'></i>Reports
                        </a>
                    </li>
                </ul>
                <div class="dropdown">
                    <a href="#" class="user-menu dropdown-toggle" data-bs-toggle="dropdown">
                        <i class='bx bxs-user-circle'></i>
                        <span>Admin</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="profile.php">
                                <i class='bx bx-user'></i>Profile
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="settings.php">
                                <i class='bx bx-cog'></i>Settings
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="../logout.php">
                                <i class='bx bx-log-out'></i>Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>
    <div class="container-fluid py-4">
        <h4 class="mb-0"><?php echo ucfirst(str_replace('.php', '', basename($_SERVER['PHP_SELF']))); ?></h4>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
