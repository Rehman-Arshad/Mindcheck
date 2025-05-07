# MindCheck - Mental Health Assessment Platform

MindCheck is a comprehensive web-based platform designed to facilitate mental health assessments and appointments between mental health professionals and patients. The system provides a streamlined interface for doctors to manage appointments and patient records, while allowing patients to easily book and manage their mental health consultations.

## 🚀 Features

### 👨‍⚕️ For Doctors
- Professional dashboard with appointment overview
- Patient management and medical history tracking
- Schedule management with customizable time slots
- Real-time appointment status updates
- Profile customization and settings
- Secure patient data access

### 👤 For Patients
- Easy appointment booking system
- Personal account management
- Access to appointment history
- Mental health assessment tools
- Profile customization
- Secure communication with doctors

### 👑 For Administrators
- Complete system oversight
- Doctor management (add/edit/remove)
- Session scheduling management
- Patient records access
- System analytics and reporting
- User account management

## 💻 Technical Requirements

- Apache Version: 2.4.39+
- PHP Version: 7.3.5+
- MySQL Version: 5.7.26+
- Web Browser: Latest versions of Chrome, Firefox, Safari, or Edge

## 🛠️ Installation Guide

1. **Set up XAMPP**
   - Download and install XAMPP
   - Start Apache and MySQL services

2. **Database Setup**
   - Navigate to phpMyAdmin (http://localhost/phpmyadmin)
   - Create a new database named 'mindcheck'
   - Import the provided SQL file from `database/mindcheck.sql`

3. **Application Setup**
   - Clone or download the repository
   - Extract files to `C:\xampp\htdocs\MindCheck`
   - Access the application at http://localhost/MindCheck

## 🔑 Default Login Credentials

### Administrator
- Email: admin@mindcheck.com
- Password: admin123

### Sample Doctor
- Email: doctor@mindcheck.com
- Password: doctor123

### Sample Patient
- Email: patient@mindcheck.com
- Password: patient123

## 🌟 Complete End-to-End Flow

### 1. Initial Setup
```
Administrator Journey:
1. Login to admin dashboard
   - Use admin credentials
   - Verify system settings

2. Doctor Management
   - Add new mental health professionals
   - Set their specializations
   - Configure working hours
   - Assign access credentials

3. System Configuration
   - Configure email notifications
   - Set up assessment questionnaires
   - Define appointment slots
   - Review security settings
```

### 2. Doctor Onboarding
```
Doctor's Journey:
1. First-time Login
   - Access welcome email
   - Change default password
   - Complete profile setup
     * Add professional credentials
     * Upload profile photo
     * Set consultation fees
     * Add specialization details

2. Schedule Configuration
   - Set working days and hours
   - Define appointment duration
   - Mark vacation days
   - Set break times

3. Dashboard Familiarization
   - Review appointment interface
   - Check patient management system
   - Test notification system
   - Configure personal preferences
```

### 3. Patient Registration & Assessment
```
Patient's Journey:
1. Account Creation
   - Visit MindCheck homepage
   - Click "Register as Patient"
   - Fill personal information
   - Verify email address

2. Initial Assessment
   - Complete mental health questionnaire
   - Provide medical history
   - Specify primary concerns
   - Upload any relevant documents

3. Doctor Selection
   - Browse available specialists
   - View doctor profiles and ratings
   - Check available time slots
   - Compare consultation fees
```

### 4. Appointment Lifecycle
```
Complete Booking Flow:
1. Appointment Scheduling
   Patient:
   - Select preferred doctor
   - Choose available date/time
   - Specify consultation type
   - Make payment if required
   - Receive confirmation email

2. Pre-Appointment
   Doctor:
   - Receive booking notification
   - Review patient's assessment
   - Confirm appointment
   - Access patient history

   Patient:
   - Receive confirmation
   - Get reminder notifications
   - Update medical information
   - Prepare questions

3. During Appointment
   Doctor:
   - Mark appointment as started
   - Take consultation notes
   - Record observations
   - Prescribe treatments

   Patient:
   - Attend consultation
   - Discuss concerns
   - Receive treatment plan
   - Ask questions

4. Post-Appointment
   Doctor:
   - Complete session notes
   - Schedule follow-up if needed
   - Update patient records
   - Send prescriptions/recommendations

   Patient:
   - Receive session summary
   - Book follow-up appointment
   - Provide feedback
   - Access treatment plan
```

### 5. Ongoing Care Management
```
Long-term Process:
1. Progress Tracking
   - Regular assessment updates
   - Treatment plan adjustments
   - Progress notes
   - Milestone tracking

2. Follow-up Management
   - Scheduled check-ins
   - Treatment adjustments
   - Progress reports
   - Care plan updates

3. Patient Engagement
   - Regular feedback collection
   - Resource sharing
   - Support group connections
   - Educational materials

4. Quality Assurance
   - Service quality monitoring
   - Patient satisfaction tracking
   - Doctor performance review
   - System improvements
```

## 🔒 Security Features

- Password Encryption
- Session Management
- SQL Injection Prevention
- XSS Protection
- CSRF Protection
- Secure File Upload Handling

## 📱 Responsive Design

- Mobile-First Approach
- Tablet-Friendly Interface
- Desktop Optimization
- Cross-Browser Compatibility

## 🤝 Contributing

1. Fork the repository
2. Create feature branch
3. Commit changes
4. Push to branch
5. Open pull request

## 📄 License

This project is licensed under the MIT License - see the LICENSE file for details.

## 🆘 Support

For support, email support@mindcheck.com or open an issue in the repository.

## 🙏 Acknowledgments

- XAMPP Development Team
- Bootstrap Team
- Boxicons
- All Contributors

---
Made with ❤️ by the MindCheck Team

netstat -ano | findstr :3306
taskkill /PID 6840 /F

http://localhost/MindCheck/add_available_slots.php
run this script to add slots in doctors tab