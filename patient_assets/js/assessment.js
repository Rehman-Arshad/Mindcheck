// Assessment questions data
const assessmentQuestions = {
    'relating_to_people': [
        { question: 'Does your child make eye contact when interacting?', category: 1 },
        { question: 'Does your child respond when called by name?', category: 1 },
        { question: 'Does your child show interest in playing with other children?', category: 1 }
    ],
    'emotional_response': [
        { question: 'Does your child show appropriate emotional reactions?', category: 2 },
        { question: 'Can your child express their feelings verbally or non-verbally?', category: 2 },
        { question: 'Does your child understand others\' emotions?', category: 2 }
    ],
    'body_use': [
        { question: 'Does your child have good balance and coordination?', category: 3 },
        { question: 'Does your child engage in repetitive body movements?', category: 3 },
        { question: 'Can your child imitate physical actions?', category: 3 }
    ],
    'object_use': [
        { question: 'Does your child play appropriately with toys?', category: 4 },
        { question: 'Does your child show interest in different objects?', category: 4 },
        { question: 'Can your child use objects imaginatively during play?', category: 4 }
    ],
    'listening_response': [
        { question: 'Does your child respond to verbal instructions?', category: 5 },
        { question: 'Does your child show interest in environmental sounds?', category: 5 },
        { question: 'Can your child distinguish between different sounds?', category: 5 }
    ],
    'adaptation_to_change': [
        { question: 'Does your child adapt well to changes in routine?', category: 6 },
        { question: 'Can your child transition between activities easily?', category: 6 },
        { question: 'Does your child accept new experiences positively?', category: 6 }
    ],
    'fear_or_nervousness': [
        { question: 'Does your child show appropriate caution in new situations?', category: 7 },
        { question: 'Does your child have specific fears or anxieties?', category: 7 },
        { question: 'Can your child self-regulate when anxious?', category: 7 }
    ],
    'visual_response': [
        { question: 'Does your child maintain appropriate visual attention?', category: 8 },
        { question: 'Does your child show interest in visual details?', category: 8 },
        { question: 'Can your child track moving objects with their eyes?', category: 8 }
    ],
    'verbal_communication': [
        { question: 'Does your child use words or gestures to communicate?', category: 9 },
        { question: 'Can your child engage in back-and-forth conversation?', category: 9 },
        { question: 'Does your child understand and follow verbal instructions?', category: 9 }
    ],
    'activity_level': [
        { question: 'Does your child have appropriate energy levels?', category: 10 },
        { question: 'Can your child sit still when required?', category: 10 },
        { question: 'Does your child participate in physical activities?', category: 10 }
    ]
};

let currentQuestion = 0;
let answers = [];
let userInfo = {};

document.addEventListener('DOMContentLoaded', function() {
    initializeFormValidation();
    initializeModalControls();
    updateProgressIndicator(1);
});

function initializeFormValidation() {
    const form = document.getElementById('userInfoForm');
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        if (validateForm()) {
            saveUserInfo();
            startAssessment();
            updateProgressIndicator(2);
        }
    });
}

function validateForm() {
    const name = document.getElementById('name').value;
    const phone = document.getElementById('phone').value;
    const gender = document.getElementById('gender').value;
    const testDate = document.getElementById('testDate').value;
    const birthDate = document.getElementById('birthDate').value;

    if (!name || !phone || !gender || !testDate || !birthDate) {
        alert('Please fill in all fields');
        return false;
    }
    return true;
}

function saveUserInfo() {
    userInfo = {
        name: document.getElementById('name').value,
        phone: document.getElementById('phone').value,
        gender: document.getElementById('gender').value,
        testDate: document.getElementById('testDate').value,
        birthDate: document.getElementById('birthDate').value
    };
}

function initializeModalControls() {
    const modal = document.getElementById('assessmentModal');
    const overlay = document.getElementById('modalOverlay');
    const closeBtn = document.getElementById('closeModal');

    document.getElementById('getStarted').addEventListener('click', function() {
        modal.style.display = 'block';
        overlay.style.display = 'block';
    });

    closeBtn.addEventListener('click', function() {
        if (confirm('Are you sure you want to exit? Your progress will be lost.')) {
            modal.style.display = 'none';
            overlay.style.display = 'none';
            resetAssessment();
        }
    });

    // Initialize download report button
    document.getElementById('downloadReport').addEventListener('click', function() {
        generatePDF();
    });
}

function updateProgressIndicator(step) {
    const steps = document.querySelectorAll('.step');
    steps.forEach((s, index) => {
        if (index + 1 < step) {
            s.classList.add('completed');
            s.classList.remove('active');
        } else if (index + 1 === step) {
            s.classList.add('active');
            s.classList.remove('completed');
        } else {
            s.classList.remove('active', 'completed');
        }
    });
}

function startAssessment() {
    document.getElementById('userInfoSection').style.display = 'none';
    document.getElementById('mcqSection').style.display = 'block';
    displayQuestion();
}

function displayQuestion() {
    const mcqContainer = document.getElementById('mcqContainer');
    if (currentQuestion >= 30) {
        showReport();
        updateProgressIndicator(3);
        return;
    }

    const categories = Object.keys(assessmentQuestions);
    const categoryIndex = Math.floor(currentQuestion / 3);
    const questionInCategory = currentQuestion % 3;
    const question = assessmentQuestions[categories[categoryIndex]][questionInCategory];

    mcqContainer.innerHTML = `
        <div class="question">
            <h3>Question ${currentQuestion + 1}/30</h3>
            <p>${question.question}</p>
            <div class="options">
                <button class="option-btn" onclick="selectAnswer(1)">Never</button>
                <button class="option-btn" onclick="selectAnswer(2)">Sometimes</button>
                <button class="option-btn" onclick="selectAnswer(3)">Often</button>
                <button class="option-btn" onclick="selectAnswer(4)">Always</button>
            </div>
        </div>
        <div class="progress-bar">
            <div class="progress" style="width: ${(currentQuestion / 30) * 100}%"></div>
        </div>
    `;
}

function selectAnswer(score) {
    const categories = Object.keys(assessmentQuestions);
    const categoryIndex = Math.floor(currentQuestion / 3);
    
    answers.push({
        category: categories[categoryIndex],
        score: score
    });

    currentQuestion++;
    displayQuestion();
}

function showReport() {
    document.getElementById('mcqSection').style.display = 'none';
    document.getElementById('reportSection').style.display = 'block';
    generateReport();
}

function generateReport() {
    const categoryScores = {};
    const categories = Object.keys(assessmentQuestions);
    
    categories.forEach(category => {
        const categoryAnswers = answers.filter(a => a.category === category);
        const average = categoryAnswers.reduce((sum, a) => sum + a.score, 0) / categoryAnswers.length;
        categoryScores[category] = average;
    });

    // Create chart using Chart.js
    const ctx = document.getElementById('reportChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: categories.map(c => c.split('_').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ')),
            datasets: [{
                label: 'Category Scores',
                data: Object.values(categoryScores),
                backgroundColor: 'rgba(0, 123, 255, 0.5)',
                borderColor: 'rgba(0, 123, 255, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    max: 4
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });

    // Save the scores for database submission
    userInfo.scores = categoryScores;
}

function generatePDF() {
    const element = document.getElementById('reportSection');
    html2pdf()
        .set({
            margin: 1,
            filename: 'assessment_report.pdf',
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2 },
            jsPDF: { unit: 'in', format: 'letter', orientation: 'portrait' }
        })
        .from(element)
        .save();
}

function resetAssessment() {
    currentQuestion = 0;
    answers = [];
    userInfo = {};
    document.getElementById('userInfoSection').style.display = 'block';
    document.getElementById('mcqSection').style.display = 'none';
    document.getElementById('reportSection').style.display = 'none';
    document.getElementById('userInfoForm').reset();
    updateProgressIndicator(1);
}
