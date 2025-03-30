<?php
session_start();
if (!isset($_SESSION["user"]) || $_SESSION["usertype"] != 'p') {
    header("location: ../login.php");
    exit();
}

include("../connection.php");

// Get the base URL for the footer
$base_url = '../';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Services - MindCheck</title>
    <link rel="stylesheet" href="../css/animations.css">
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/admin.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        .service-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 100%;
            border-radius: 15px;
            overflow: hidden;
            background: white;
            margin-bottom: 20px;
        }
        .service-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .service-icon {
            font-size: 3rem;
            color: #1977cc;
            margin-bottom: 1rem;
        }
        .service-header {
            background: linear-gradient(45deg, #1977cc, #3291e6);
            color: white;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
        }
        .btn-custom {
            border-radius: 25px;
            padding: 8px 25px;
            transition: all 0.3s ease;
            background: #1977cc;
            color: white;
            border: none;
            text-decoration: none;
            display: inline-block;
        }
        .btn-custom:hover {
            transform: scale(1.05);
            background: #166ab5;
            color: white;
        }
        .card-body {
            padding: 2rem;
            text-align: center;
        }
        .feature-list {
            text-align: left;
            margin-top: 1rem;
            padding-left: 1.5rem;
            list-style: none;
        }
        .feature-list li {
            margin-bottom: 0.5rem;
            position: relative;
            color: #2c4964;
        }
        .feature-list li:before {
            content: "✓";
            color: #1977cc;
            position: absolute;
            left: -1.5rem;
        }
        .services-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }
    </style>
</head>
<body>
    <?php include("../header.php"); ?>

  

    <div class="services-container">
    <div class="service-header text-center">
        <h1>Our Services</h1>
        <p class="lead">Discover our comprehensive mental health services designed to support your well-being</p>
    </div>
        <div class="services-grid">
            <!-- Autism Assessment -->
            <div class="service-card">
                <div class="card-body">
                    <i class='bx bx-brain service-icon'></i>
                    <h3>Autism Assessment</h3>
                    <p>Comprehensive evaluation using standardized tools to assess autism spectrum disorders in children.</p>
                    <ul class="feature-list">
                        <li>Detailed behavioral analysis</li>
                        <li>Cognitive assessment</li>
                        <li>Personalized reports</li>
                        <li>Expert recommendations</li>
                    </ul>
                    <a href="assessment.php" class="btn-custom">Take Assessment</a>
                </div>
            </div>

            <!-- Counseling Services -->
            <div class="service-card">
                <div class="card-body">
                    <i class='bx bx-conversation service-icon'></i>
                    <h3>Counseling Services</h3>
                    <p>Professional counseling sessions with experienced therapists to help navigate life's challenges.</p>
                    <ul class="feature-list">
                        <li>Individual therapy</li>
                        <li>Group sessions</li>
                        <li>Behavioral therapy</li>
                        <li>Progress tracking</li>
                    </ul>
                    <a href="schedule.php" class="btn-custom">Book Session</a>
                </div>
            </div>

            <!-- Family Therapy -->
            <div class="service-card">
                <div class="card-body">
                    <i class='bx bx-group service-icon'></i>
                    <h3>Family Therapy</h3>
                    <p>Family-focused therapy sessions to improve communication and strengthen relationships.</p>
                    <ul class="feature-list">
                        <li>Family dynamics assessment</li>
                        <li>Communication skills</li>
                        <li>Conflict resolution</li>
                        <li>Parenting guidance</li>
                    </ul>
                    <a href="schedule.php" class="btn-custom">Learn More</a>
                </div>
            </div>

            <!-- Educational Support -->
            <div class="service-card">
                <div class="card-body">
                    <i class='bx bx-book-reader service-icon'></i>
                    <h3>Educational Support</h3>
                    <p>Specialized educational support and strategies for children with autism.</p>
                    <ul class="feature-list">
                        <li>Academic strategies</li>
                        <li>Learning assessments</li>
                        <li>IEP support</li>
                        <li>School coordination</li>
                    </ul>
                    <button class="btn-custom" onclick="contactSupport()">Contact Us</button>
                </div>
            </div>

            <!-- Support Groups -->
            <div class="service-card">
                <div class="card-body">
                    <i class='bx bx-support service-icon'></i>
                    <h3>Support Groups</h3>
                    <p>Join our supportive community groups to share experiences and learn from others.</p>
                    <ul class="feature-list">
                        <li>Parent support groups</li>
                        <li>Peer connections</li>
                        <li>Resource sharing</li>
                        <li>Monthly meetings</li>
                    </ul>
                    <button class="btn-custom" onclick="joinGroup()">Join Group</button>
                </div>
            </div>

            <!-- Home Consultation -->
            <div class="service-card">
                <div class="card-body">
                    <i class='bx bx-home-heart service-icon'></i>
                    <h3>Home Consultation</h3>
                    <p>Expert consultation services in the comfort of your home for personalized support.</p>
                    <ul class="feature-list">
                        <li>Home environment assessment</li>
                        <li>Customized strategies</li>
                        <li>Family training</li>
                        <li>Follow-up support</li>
                    </ul>
                    <button class="btn-custom" onclick="requestService()">Request Service</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function contactSupport() {
            window.location.href = 'contact.php';
        }

        function joinGroup() {
            alert('Please contact our office to join the next available support group session.');
        }

        function requestService() {
            window.location.href = 'schedule.php';
        }
    </script>

    <?php include("../footer.php"); ?>
</body>
</html>
