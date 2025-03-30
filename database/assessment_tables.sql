-- Create assessments table
CREATE TABLE IF NOT EXISTS assessments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    patient_id INT NOT NULL,
    child_name VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    gender ENUM('male', 'female', 'other') NOT NULL,
    test_date DATE NOT NULL,
    birth_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patient(pid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create assessment_scores table
CREATE TABLE IF NOT EXISTS assessment_scores (
    id INT PRIMARY KEY AUTO_INCREMENT,
    assessment_id INT NOT NULL,
    category VARCHAR(50) NOT NULL,
    score DECIMAL(4,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (assessment_id) REFERENCES assessments(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create assessment_categories table
CREATE TABLE IF NOT EXISTS assessment_categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert assessment categories
INSERT INTO assessment_categories (name, description) VALUES
('relating_to_people', 'How the child interacts and relates to other people'),
('emotional_response', 'The child\'s emotional reactions and expressions'),
('body_use', 'How the child uses their body and motor skills'),
('object_use', 'How the child interacts with and uses objects'),
('listening_response', 'How the child responds to sounds and verbal communication'),
('adaptation_to_change', 'How well the child adapts to changes in routine or environment'),
('fear_or_nervousness', 'The child\'s anxiety and fear responses'),
('visual_response', 'How the child responds to visual stimuli'),
('verbal_communication', 'The child\'s verbal communication skills'),
('activity_level', 'The child\'s energy and activity levels');
