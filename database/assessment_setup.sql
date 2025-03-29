-- Create assessment_categories table
CREATE TABLE IF NOT EXISTS assessment_categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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

-- Insert default assessment categories
INSERT INTO assessment_categories (name, description) VALUES
('relating_to_people', 'How the child interacts and relates to other people, including eye contact, social responses, and engagement with others.'),
('emotional_response', 'The child\'s emotional reactions, expressions, and ability to show appropriate feelings in different situations.'),
('body_use', 'How the child uses their body, including coordination, motor skills, and physical movements.'),
('object_use', 'How the child interacts with and uses objects, toys, and other items in their environment.'),
('listening_response', 'How the child responds to sounds, verbal communication, and follows verbal instructions.'),
('adaptation_to_change', 'How well the child adapts to changes in routine, environment, or activities.'),
('fear_or_nervousness', 'The child\'s anxiety levels, fear responses, and ability to cope with stressful situations.'),
('visual_response', 'How the child responds to visual stimuli, including eye contact and visual tracking.'),
('verbal_communication', 'The child\'s verbal communication skills, including speech, language development, and conversation abilities.'),
('activity_level', 'The child\'s energy levels, attention span, and ability to regulate activity appropriately.');
