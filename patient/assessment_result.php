<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION["user"]) || $_SESSION["usertype"] != 'p') {
    header("location: ../login.php");
    exit;
}

include("../connection.php");

if (!isset($_GET['id'])) {
    header("location: dashboard.php");
    exit;
}

$assessment_id = $_GET['id'];
$pid = $_SESSION["pid"];

// Get assessment details
$stmt = $database->prepare("SELECT * FROM assessments WHERE id = ? AND patient_id = ?");
$stmt->bind_param("ii", $assessment_id, $pid);
$stmt->execute();
$assessment = $stmt->get_result()->fetch_assoc();

if (!$assessment) {
    header("location: dashboard.php");
    exit;
}

// Get responses by category
$query = "SELECT aq.category, AVG(ar.score) as avg_score
          FROM assessment_responses ar
          JOIN assessment_questions aq ON ar.question_id = aq.id
          WHERE ar.assessment_id = ?
          GROUP BY aq.category";
$stmt = $database->prepare($query);
$stmt->bind_param("i", $assessment_id);
$stmt->execute();
$category_scores = $stmt->get_result();

// Get detailed responses
$query = "SELECT aq.question, aq.category, ar.score
          FROM assessment_responses ar
          JOIN assessment_questions aq ON ar.question_id = aq.id
          WHERE ar.assessment_id = ?
          ORDER BY aq.category, aq.id";
$stmt = $database->prepare($query);
$stmt->bind_param("i", $assessment_id);
$stmt->execute();
$responses = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assessment Results - MindCheck</title>
    <link rel="stylesheet" href="../css/animations.css">
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/admin.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .result-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .result-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .result-header h1 {
            color: #2c4964;
            margin-bottom: 10px;
        }
        .result-header p {
            color: #666;
        }
        .result-card {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            margin-bottom: 30px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .info-item {
            text-align: center;
        }
        .info-item h3 {
            color: #1977cc;
            margin-bottom: 5px;
        }
        .info-item p {
            color: #666;
            margin: 0;
        }
        .chart-container {
            margin-bottom: 30px;
            padding: 20px;
        }
        .responses-section h2 {
            color: #2c4964;
            margin-bottom: 20px;
        }
        .category-section {
            margin-bottom: 30px;
        }
        .category-header {
            color: #1977cc;
            margin: 20px 0 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e3f2fd;
        }
        .response-item {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 10px;
        }
        .response-item p {
            margin: 0;
            color: #2c4964;
        }
        .score-indicator {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 15px;
            font-size: 0.9em;
            margin-top: 5px;
        }
        .score-low {
            background: #ffebee;
            color: #c62828;
        }
        .score-medium {
            background: #fff3e0;
            color: #ef6c00;
        }
        .score-high {
            background: #e8f5e9;
            color: #2e7d32;
        }
        .action-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 30px;
        }
        .action-btn {
            padding: 10px 25px;
            border-radius: 25px;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }
        .primary-btn {
            background: #1977cc;
            color: white;
        }
        .primary-btn:hover {
            background: #1565c0;
        }
        .secondary-btn {
            background: #e3f2fd;
            color: #1977cc;
        }
        .secondary-btn:hover {
            background: #bbdefb;
        }
        @media print {
            .no-print {
                display: none;
            }
            .result-container {
                padding: 0;
            }
            .result-card {
                box-shadow: none;
            }
        }
    </style>
</head>
<body>
    <?php include("../header.php"); ?>

    <div class="result-container">
        <div class="result-header">
            <h1>Assessment Results</h1>
            <p>Detailed analysis of your child's assessment</p>
        </div>

        <div class="result-card">
            <div class="info-grid">
                <div class="info-item">
                    <h3><?php echo htmlspecialchars($assessment['child_name']); ?></h3>
                    <p>Child's Name</p>
                </div>
                <div class="info-item">
                    <h3><?php echo $assessment['age']; ?> years</h3>
                    <p>Age</p>
                </div>
                <div class="info-item">
                    <h3><?php echo ucfirst($assessment['gender']); ?></h3>
                    <p>Gender</p>
                </div>
                <div class="info-item">
                    <h3><?php echo number_format($assessment['total_score'], 1); ?></h3>
                    <p>Overall Score</p>
                </div>
            </div>

            <div class="chart-container">
                <canvas id="categoryChart"></canvas>
            </div>

            <div class="responses-section">
                <h2>Detailed Responses</h2>
                <?php 
                $current_category = '';
                while($response = $responses->fetch_assoc()): 
                    if ($current_category != $response['category']):
                        $current_category = $response['category'];
                        echo "<h3 class='category-header'>" . ucwords(str_replace('_', ' ', $response['category'])) . "</h3>";
                    endif;
                ?>
                    <div class="response-item">
                        <p><?php echo htmlspecialchars($response['question']); ?></p>
                        <div class="score-indicator <?php 
                            echo $response['score'] <= 2 ? 'score-low' : 
                                 ($response['score'] <= 3 ? 'score-medium' : 'score-high'); 
                        ?>">
                            Score: <?php echo $response['score']; ?>/5
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>

            <div class="action-buttons no-print">
                <button onclick="window.print()" class="action-btn primary-btn">
                    <i class='bx bx-printer'></i> Print Report
                </button>
                <a href="assessment.php" class="action-btn secondary-btn">
                    <i class='bx bx-plus'></i> New Assessment
                </a>
            </div>
        </div>
    </div>

    <script>
        // Prepare chart data
        const categoryData = {
            labels: [
                <?php 
                $category_scores->data_seek(0);
                while($score = $category_scores->fetch_assoc()) {
                    echo "'" . ucwords(str_replace('_', ' ', $score['category'])) . "',";
                }
                ?>
            ],
            datasets: [{
                label: 'Category Scores',
                data: [
                    <?php 
                    $category_scores->data_seek(0);
                    while($score = $category_scores->fetch_assoc()) {
                        echo $score['avg_score'] . ",";
                    }
                    ?>
                ],
                backgroundColor: 'rgba(25, 119, 204, 0.2)',
                borderColor: 'rgba(25, 119, 204, 1)',
                borderWidth: 2,
                pointBackgroundColor: 'rgba(25, 119, 204, 1)'
            }]
        };

        // Create radar chart
        const ctx = document.getElementById('categoryChart').getContext('2d');
        new Chart(ctx, {
            type: 'radar',
            data: categoryData,
            options: {
                scales: {
                    r: {
                        beginAtZero: true,
                        max: 5,
                        ticks: {
                            stepSize: 1
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    </script>
</body>
</html>
