<?php
session_start();
if (!isset($_SESSION["user"]) || $_SESSION["usertype"] != 'p') {
    header("location: ../login.php");
    exit;
}

include("../connection.php");

// Handle form submission
$message = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $subject = $_POST['subject'];
    $message_text = $_POST['message'];
    
    // You can add code here to save to database or send email
    // For now, we'll just show a success message
    $message = "Thank you for your message. We will get back to you soon!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - MindCheck</title>
    <link rel="stylesheet" href="../css/animations.css">
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/admin.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        .contact-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .contact-info {
            background: linear-gradient(45deg, #1977cc, #3291e6);
            padding: 40px;
            border-radius: 15px;
            margin-bottom: 40px;
            color: white;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .contact-info h1 {
            font-size: 2.5em;
            margin-bottom: 20px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            margin-top: 30px;
        }
        .info-item {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .info-item i {
            font-size: 2em;
        }
        .info-item .details h3 {
            font-size: 1.2em;
            margin-bottom: 5px;
        }
        .info-item .details p {
            opacity: 0.9;
        }
        .contact-form {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }
        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group.full {
            grid-column: 1 / -1;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #2c4964;
            font-weight: 500;
        }
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1em;
            transition: border-color 0.3s ease;
        }
        .form-group input:focus,
        .form-group textarea:focus {
            border-color: #1977cc;
            outline: none;
        }
        .form-group textarea {
            height: 150px;
            resize: vertical;
        }
        .submit-btn {
            background: #1977cc;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 30px;
            cursor: pointer;
            font-size: 1em;
            transition: all 0.3s ease;
        }
        .submit-btn:hover {
            background: #166ab5;
            transform: translateY(-2px);
        }
        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
            .info-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php include("../header.php"); ?>

    <div class="contact-container">
        <div class="contact-info">
            <h1>Get in Touch</h1>
            <p>Have questions? We'd love to hear from you. Send us a message and we'll respond as soon as possible.</p>
            
            <div class="info-grid">
                <div class="info-item">
                    <i class='bx bx-map'></i>
                    <div class="details">
                        <h3>Address</h3>
                        <p>123 Healthcare Ave, Medical District, City</p>
                    </div>
                </div>
                <div class="info-item">
                    <i class='bx bx-phone'></i>
                    <div class="details">
                        <h3>Phone</h3>
                        <p>+1 234 567 8900</p>
                    </div>
                </div>
                <div class="info-item">
                    <i class='bx bx-envelope'></i>
                    <div class="details">
                        <h3>Email</h3>
                        <p>contact@mindcheck.com</p>
                    </div>
                </div>
            </div>
        </div>

        <?php if($message): ?>
            <div class="success-message">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <div class="contact-form">
            <form action="" method="POST" class="form-grid">
                <div class="form-group">
                    <label for="name">Your Name</label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="email">Your Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group full">
                    <label for="subject">Subject</label>
                    <input type="text" id="subject" name="subject" required>
                </div>
                <div class="form-group full">
                    <label for="message">Message</label>
                    <textarea id="message" name="message" required></textarea>
                </div>
                <div class="form-group full">
                    <button type="submit" class="submit-btn">Send Message</button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>
