<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mental Health Assessment - MindCheck</title>
    <link rel="stylesheet" href="css/animations.css">  
    <link rel="stylesheet" href="css/main.css">  
    <link rel="stylesheet" href="patient_assets/css/assessment.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <style>
        body {
            background: #f8f9fa;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .hero-section {
            text-align: center;
            padding: 60px 20px;
            background: linear-gradient(45deg, #1977cc, #3291e6);
            color: white;
            border-radius: 15px;
            margin: 40px 0;
            box-shadow: 0 10px 30px rgba(25, 119, 204, 0.2);
            position: relative;
        }
        .hero-section h1 {
            font-size: 2.8em;
            margin-bottom: 20px;
            line-height: 1.3;
        }
        .hero-section p {
            font-size: 1.2em;
            margin-bottom: 40px;
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
            line-height: 1.6;
        }
        .btn-primary {
            background: white;
            color: #1977cc;
            padding: 15px 40px;
            border: none;
            border-radius: 30px;
            font-size: 1.1em;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            background: #f8f9fa;
        }
        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin: 50px 0;
        }
        .feature-card {
            background: white;
            padding: 30px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }
        .feature-card i {
            font-size: 2.5em;
            color: #1977cc;
            margin-bottom: 20px;
        }
        .feature-card h3 {
            color: #2c4964;
            margin-bottom: 15px;
        }
        .feature-card p {
            color: #666;
            line-height: 1.6;
        }
        .login-prompt {
            text-align: center;
            margin-top: 20px;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }
        .back-button {
            position: absolute;
            top: 20px;
            left: 20px;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 30px;
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            font-weight: 500;
        }
        .back-button:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateX(-5px);
        }
        .back-button i {
            font-size: 20px;
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="hero-section">
            <a href="index.php" class="back-button">
                <i class='bx bx-arrow-back'></i>
                Back to Home
            </a>
            <h1>Mental Health Assessment for Special Children</h1>
            <p>Take our comprehensive assessment to understand your child's needs better. This assessment takes about 15-20 minutes to complete and provides valuable insights into various behavioral and developmental areas.</p>
            <button id="getStarted" class="btn-primary">Start Assessment</button>
        </div>

        <div class="features">
            <div class="feature-card">
                <i class="bx bx-time"></i>
                <h3>Quick & Easy</h3>
                <p>Complete the assessment in 15-20 minutes with simple, straightforward questions</p>
            </div>
            <div class="feature-card">
                <i class="bx bx-chart"></i>
                <h3>Detailed Report</h3>
                <p>Get instant results with visual charts and actionable recommendations</p>
            </div>
            <div class="feature-card">
                <i class="bx bx-lock-alt"></i>
                <h3>Private & Secure</h3>
                <p>Your information is kept completely confidential and secure</p>
            </div>
        </div>

        <!-- Assessment Modal -->
        <div id="modalOverlay" class="modal-overlay"></div>
        <div id="assessmentModal" class="modal">
            <button id="closeModal" class="close-btn">&times;</button>

            <!-- Progress Indicator -->
            <div class="progress-indicator">
                <div class="step active" data-step="1">Info</div>
                <div class="step" data-step="2">Assessment</div>
                <div class="step" data-step="3">Report</div>
            </div>

            <!-- User Information Section -->
            <div id="userInfoSection">
                <h2>Personal Information</h2>
                <form id="userInfoForm" class="assessment-form">
                    <div class="form-group">
                        <label for="name">Child's Name</label>
                        <input type="text" id="name" name="name" required>
                    </div>

                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone" required>
                    </div>

                    <div class="form-group">
                        <label for="gender">Gender</label>
                        <select id="gender" name="gender" required>
                            <option value="">Select Gender</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="testDate">Test Date</label>
                        <input type="date" id="testDate" name="testDate" required>
                    </div>

                    <div class="form-group">
                        <label for="birthDate">Birth Date</label>
                        <input type="date" id="birthDate" name="birthDate" required>
                    </div>

                    <button type="submit" class="btn-primary">Next</button>
                </form>
            </div>

            <!-- MCQ Section -->
            <div id="mcqSection" style="display: none;">
                <h2>Assessment Questions</h2>
                <div id="mcqContainer"></div>
            </div>

            <!-- Report Section -->
            <div id="reportSection" style="display: none;">
                <h2>Assessment Report</h2>
                <div class="chart-container">
                    <canvas id="reportChart"></canvas>
                </div>
                <div class="report-summary">
                    <h3>Summary</h3>
                    <p>This report provides an overview of your child's assessment results across different behavioral and developmental areas. The scores range from 1 (Never) to 4 (Always), indicating the frequency of observed behaviors in each category.</p>
                    <div class="recommendations">
                        <h4>Next Steps</h4>
                        <ul>
                            <li>Share this report with your child's healthcare provider</li>
                            <li>Schedule a follow-up appointment to discuss the results</li>
                            <li>Keep track of any changes in behaviors over time</li>
                        </ul>
                    </div>
                </div>
                <div class="report-actions">
                    <button id="downloadReport" class="btn-primary">Download Report</button>
                    <button onclick="resetAssessment()" class="btn-secondary">Start New Assessment</button>
                </div>
                <div class="login-prompt">
                    <h4>Want to save your results?</h4>
                    <p>Create an account or log in to save your assessment history and track progress over time.</p>
                    <a href="login.php" class="btn-primary">Log In / Sign Up</a>
                </div>
            </div>
        </div>
    </div>

    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="patient_assets/js/assessment.js"></script>
</body>
</html>
