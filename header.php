<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get the current page for active state
$current_page = basename($_SERVER['PHP_SELF'], ".php");

// Determine if we're in a subdirectory
$in_patient_dir = strpos($_SERVER['PHP_SELF'], '/patient/') !== false;
$base_url = $in_patient_dir ? '../' : '';

if(!isset($base_url)){
    $base_url = '/MindCheck/';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>MindCheck</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        .header {
            background: white;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
            padding: 15px 0;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .header-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo {
            font-size: 1.5em;
            font-weight: 700;
            color: #1977cc;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .nav-menu {
            display: flex;
            gap: 30px;
            align-items: center;
        }
        .nav-link {
            color: #2c4964;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .nav-link:hover {
            color: #1977cc;
        }
        .nav-link.active {
            color: #1977cc;
        }
        .nav-link i {
            font-size: 1.2em;
        }
        .user-menu {
            position: relative;
        }
        .user-btn {
            background: none;
            border: none;
            color: #2c4964;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 1em;
            padding: 5px 10px;
            border-radius: 5px;
            transition: all 0.3s ease;
        }
        .user-btn:hover {
            background: #f8f9fa;
        }
        .mobile-menu {
            display: none;
            background: none;
            border: none;
            font-size: 1.5em;
            color: #2c4964;
            cursor: pointer;
        }
        @media (max-width: 768px) {
            .nav-menu {
                display: none;
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                background: white;
                padding: 20px;
                box-shadow: 0 2px 15px rgba(0,0,0,0.1);
                flex-direction: column;
                gap: 15px;
            }
            .nav-menu.active {
                display: flex;
            }
            .mobile-menu {
                display: block;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-container">
            <a href="<?php echo $base_url; ?>" class="logo">
                <i class='bx bx-brain'></i>
                MindCheck
            </a>

            <?php if(isset($_SESSION["user"]) && $_SESSION["usertype"] == 'p'): ?>
                <nav class="nav-menu">
                    <a href="<?php echo $base_url; ?>patient/dashboard.php" class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], 'dashboard.php') !== false ? 'active' : ''; ?>">
                        <i class='bx bx-home-alt-2'></i> Dashboard
                    </a>
                    <a href="<?php echo $base_url; ?>patient/doctors.php" class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], 'doctors.php') !== false ? 'active' : ''; ?>">
                        <i class='bx bx-user-voice'></i> Doctors
                    </a>
                    <a href="<?php echo $base_url; ?>patient/schedule.php" class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], 'schedule.php') !== false ? 'active' : ''; ?>">
                        <i class='bx bx-calendar'></i> Schedule
                    </a>
                    <a href="<?php echo $base_url; ?>patient/services.php" class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], 'services.php') !== false ? 'active' : ''; ?>">
                        <i class='bx bx-package'></i> Services
                    </a>
                    <a href="<?php echo $base_url; ?>patient/contact.php" class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], 'contact.php') !== false ? 'active' : ''; ?>">
                        <i class='bx bx-envelope'></i> Contact
                    </a>
                    <div class="user-menu">
                        <a href="<?php echo $base_url; ?>logout.php" class="nav-link">
                            <i class='bx bx-log-out'></i> Logout
                        </a>
                    </div>
                </nav>
                <button class="mobile-menu" onclick="toggleMenu()">
                    <i class='bx bx-menu'></i>
                </button>
            <?php else: ?>
                <nav class="nav-menu">
                    <a href="<?php echo $base_url; ?>login.php" class="nav-link">
                        <i class='bx bx-log-in'></i> Login
                    </a>
                </nav>
            <?php endif; ?>
        </div>
    </header>

    <script>
    function toggleMenu() {
        const menu = document.querySelector('.nav-menu');
        menu.classList.toggle('active');
    }
    </script>
</body>
</html>