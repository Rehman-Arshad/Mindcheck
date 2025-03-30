<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assessment Results - MindCheck</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../css/animations.css">
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/assessment.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <?php 
    session_start();
    if (!isset($_SESSION["user"]) || $_SESSION["usertype"] != 'p') {
        header("location: ../login.php");
        exit;
    }

    include("../connection.php");

    if (!isset($_GET['id'])) {
        header("Location: assessment.php");
        exit;
    }

    $assessment_id = $_GET['id'];
    $email = $_SESSION["user"];

    // Get patient ID
    $result = $database->query("SELECT pid FROM patient WHERE pemail='$email'");
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $pid = $row['pid'];
    } else {
        die("Error: Patient not found");
    }

    // Get assessment details
    $stmt = $database->prepare("
        SELECT a.*, GROUP_CONCAT(CONCAT(ac.name, ':', as2.score) SEPARATOR ',') as scores
        FROM assessments a
        LEFT JOIN assessment_scores as2 ON a.id = as2.assessment_id
        LEFT JOIN assessment_categories ac ON as2.category = ac.name
        WHERE a.id = ? AND a.patient_id = ?
        GROUP BY a.id
    ");
    $stmt->bind_param("ii", $assessment_id, $pid);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        die("Assessment not found or unauthorized access");
    }

    $assessment = $result->fetch_assoc();

    // Parse scores
    $scores = [];
    $categories = explode(',', $assessment['scores']);
    foreach ($categories as $category) {
        list($name, $score) = explode(':', $category);
        $scores[$name] = floatval($score);
    }

    // Calculate average score
    $avg_score = array_sum($scores) / count($scores);

    // Determine areas of concern (scores below 3)
    $concerns = array_filter($scores, function($score) {
        return $score < 3;
    });

    // Get recommended specialists based on concerns
    $specialties = [];
    if (!empty($concerns)) {
        foreach (array_keys($concerns) as $category) {
            switch ($category) {
                case 'relating_to_people':
                case 'social_interaction':
                    $specialties[] = 'Child Psychiatrist';
                    $specialties[] = 'Child Psychologist';
                    break;
                case 'emotional_response':
                case 'fear_or_nervousness':
                    $specialties[] = 'Child Psychologist';
                    $specialties[] = 'Behavioral Therapist';
                    break;
                case 'body_use':
                    $specialties[] = 'Occupational Therapist';
                    $specialties[] = 'Physical Therapist';
                    break;
                case 'verbal_communication':
                    $specialties[] = 'Speech Therapist';
                    $specialties[] = 'Language Pathologist';
                    break;
                default:
                    $specialties[] = 'Child Development Specialist';
            }
        }
        $specialties = array_unique($specialties);
    }
    ?>
    <?php include("header.php"); ?>

    <div class="container py-5">
        <div class="results-container">
            <h1>Assessment Results</h1>
            <p class="assessment-date">Assessment taken on <?php echo date('F j, Y', strtotime($assessment['test_date'])); ?></p>

            <div class="results-summary">
                <div class="overall-score">
                    <h2>Overall Assessment</h2>
                    <div class="score-circle <?php echo $avg_score >= 3 ? 'good' : 'concern'; ?>">
                        <?php echo number_format($avg_score, 1); ?>
                    </div>
                    <p class="score-label">Average Score</p>
                </div>

                <div class="chart-container">
                    <canvas id="resultsChart"></canvas>
                </div>
            </div>

            <?php if (!empty($concerns)): ?>
            <div class="concerns-section">
                <h2>Areas Needing Attention</h2>
                <ul class="concerns-list">
                    <?php foreach ($concerns as $category => $score): ?>
                    <li>
                        <span class="category"><?php echo ucwords(str_replace('_', ' ', $category)); ?></span>
                        <span class="score"><?php echo number_format($score, 1); ?>/5</span>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>

            <?php if (!empty($specialties)): ?>
            <div class="recommendations-section">
                <h2>Recommended Specialists</h2>
                <p>Based on the assessment results, we recommend consulting with the following specialists:</p>
                <ul class="specialists-list">
                    <?php 
                    // Get doctors with matching specialties
                    $specialty_list = implode("','", $specialties);
                    $doctor_query = "SELECT docid, docname, specialties FROM doctor WHERE specialties IN ('$specialty_list') LIMIT 3";
                    $doctor_result = $database->query($doctor_query);
                    
                    if ($doctor_result && $doctor_result->num_rows > 0):
                        while($doctor = $doctor_result->fetch_assoc()): 
                    ?>
                    <li>
                        <i class='bx bx-user-circle'></i>
                        <div class="specialist-info">
                            <span class="doctor-name"><?php echo $doctor['docname']; ?></span>
                            <span class="specialty"><?php echo $doctor['specialties']; ?></span>
                            <a href="book-appointment.php?docid=<?php echo $doctor['docid']; ?>" class="btn-book">
                                Book Appointment
                            </a>
                        </div>
                    </li>
                    <?php 
                        endwhile;
                    else:
                    ?>
                    <?php foreach ($specialties as $specialty): ?>
                    <li>
                        <i class='bx bx-user-circle'></i>
                        <div class="specialist-info">
                            <span class="specialty"><?php echo $specialty; ?></span>
                            <a href="doctors.php?specialty=<?php echo urlencode($specialty); ?>" class="btn-book">
                                Find Specialist
                            </a>
                        </div>
                    </li>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
            <?php endif; ?>

            <div class="actions-section">
                <a href="assessment.php" class="btn-secondary">Take Another Assessment</a>
                <button onclick="window.print()" class="btn-primary">
                    <i class='bx bx-printer'></i> Print Results
                </button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Create the results chart
        const ctx = document.getElementById('resultsChart').getContext('2d');
        new Chart(ctx, {
            type: 'radar',
            data: {
                labels: <?php echo json_encode(array_map(function($cat) { 
                    return ucwords(str_replace('_', ' ', $cat)); 
                }, array_keys($scores))); ?>,
                datasets: [{
                    label: 'Score',
                    data: <?php echo json_encode(array_values($scores)); ?>,
                    fill: true,
                    backgroundColor: 'rgba(25, 119, 204, 0.2)',
                    borderColor: 'rgb(25, 119, 204)',
                    pointBackgroundColor: 'rgb(25, 119, 204)',
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: 'rgb(25, 119, 204)'
                }]
            },
            options: {
                scales: {
                    r: {
                        angleLines: {
                            display: true
                        },
                        suggestedMin: 0,
                        suggestedMax: 5
                    }
                }
            }
        });
    </script>
</body>
</html>
