# Hospital Management System

A comprehensive solution for managing hospital operations, patient records, appointments, and laboratory tests.

![Hospital Management System](https://via.placeholder.com/800x400?text=Hospital+Management+System)

## 📋 Table of Contents
- [Overview](#overview)
- [Features](#features)
- [Database Structure](#database-structure)
- [Query Functionality](#query-functionality)
- [Installation](#installation)
- [Usage](#usage)
- [API Documentation](#api-documentation)
- [Contributing](#contributing)
- [License](#license)

## 🔍 Overview

The Hospital Management System is a comprehensive web-based application designed to streamline and automate hospital operations. It facilitates efficient management of patients, doctors, appointments, and laboratory tests, enabling healthcare providers to deliver better patient care.

## ✨ Features

- **User Authentication & Management**
  - Secure login system for patients, doctors, and administrative staff
  - Role-based access control
  - Password encryption for enhanced security

- **Patient Management**
  - Patient registration and profile management
  - Medical history tracking
  - Appointment scheduling and history

- **Doctor Management**
  - Doctor registration and profile management
  - Specialization tracking
  - Appointment scheduling and calendar

- **Appointment System**
  - Schedule, reschedule, and cancel appointments
  - Appointment status tracking (Scheduled, Completed, Cancelled)
  - Notification system for upcoming appointments

- **Laboratory Management**
  - Request and track laboratory tests
  - Monitor test status (Pending, In Progress, Completed)
  - Store and access test results
  - Generate and download test reports

- **Reporting**
  - Generate statistical reports for administrative purposes
  - Patient history reports
  - Laboratory test analysis

## 🗃️ Database Structure

The system is built on a relational database (`hospital_db`) with the following tables:

- **Users**: System users with authentication information
- **Patients**: Patient demographic and contact information
- **Doctors**: Doctor profiles with specialization details
- **Appointments**: Scheduled appointments between doctors and patients
- **LabTests**: Laboratory tests ordered for patients
- **LabTestReports**: Storage for laboratory test result files

## 🔎 Query Functionality

Our hospital management system features a powerful and flexible query system that allows users to search and filter data across all database tables. This functionality is vital for healthcare professionals to quickly access critical information.

### Query Features:

#### Basic Querying
- **Patient Search**: Find patients by name, ID, phone number, or age
- **Doctor Search**: Find doctors by name, specialization, or ID
- **Appointment Lookup**: Search by date range, status, doctor, or patient
- **Lab Test Filtering**: Filter by test name, status, date range, or patient

#### Advanced Query Options
- **Multi-parameter Search**: Combine multiple search criteria for precise results
- **Wildcard Support**: Use partial matches to find records with incomplete information
- **Date Range Queries**: Filter appointments or lab tests within specific time periods
- **Status-based Filtering**: Find all appointments or tests with particular statuses

#### Query Implementation

The query system is implemented through a combination of:

1. **Frontend Search Interface**: User-friendly forms with auto-complete functionality
2. **API Endpoints**: RESTful endpoints that accept query parameters
3. **Database Layer**: Optimized SQL queries with prepared statements for security

#### Example Query Use Cases:

1. Find all appointments for a specific doctor on a given date
```sql
SELECT a.*, p.Name as PatientName 
FROM Appointments a
JOIN Patients p ON a.PatientID = p.PatientID
WHERE a.DoctorID = ? AND a.AppointmentDate = ?
ORDER BY a.AppointmentTime;
```

2. Find all pending lab tests for a specific patient
```sql
SELECT l.*, p.Name as PatientName
FROM LabTests l
JOIN Patients p ON l.PatientID = p.PatientID
WHERE l.Status = 'Pending' AND l.PatientID = ?;
```

3. Find all patients with appointments in the next week
```sql
SELECT DISTINCT p.*
FROM Patients p
JOIN Appointments a ON p.PatientID = a.PatientID
WHERE a.AppointmentDate BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY);
```

4. Search for patients by partial name match
```sql
SELECT * FROM Patients 
WHERE Name LIKE CONCAT('%', ?, '%');
```

#### Performance Considerations

The query system includes:
- **Indexed Fields**: Critical search fields are indexed for faster retrieval
- **Query Caching**: Frequent queries are cached to reduce database load
- **Pagination**: Large result sets are paginated to improve performance
- **Query Optimization**: Queries are optimized for the database engine

## 🔧 Installation

```bash
# Clone the repository
git clone https://github.com/yourusername/hospital-management-system.git

# Navigate to the project directory
cd hospital-management-system

# Install dependencies
npm install

# Set up environment variables
cp .env.example .env
# Edit .env with your database credentials

# Create database structure
mysql -u username -p < database/schema.sql

# Seed with sample data (optional)
mysql -u username -p < database/seeds.sql

# Start the application
npm start
```

## 🖥️ Usage

1. Access the application at http://localhost:3000
2. Login using the default administrator credentials:
   - Email: admin@hospital.com
   - Password: admin123
3. Create doctor and patient accounts
4. Start managing appointments and laboratory tests

## 📚 API Documentation

The system provides a RESTful API for integration with other healthcare systems:

- **Authentication Endpoints**:
  - `POST /api/auth/login`
  - `POST /api/auth/logout`

- **Patient Endpoints**:
  - `GET /api/patients`
  - `GET /api/patients/:id`
  - `POST /api/patients`
  - `PUT /api/patients/:id`

- **Doctor Endpoints**:
  - `GET /api/doctors`
  - `GET /api/doctors/:id`
  - `POST /api/doctors`
  - `PUT /api/doctors/:id`

- **Appointment Endpoints**:
  - `GET /api/appointments`
  - `GET /api/appointments/:id`
  - `POST /api/appointments`
  - `PUT /api/appointments/:id`
  - `DELETE /api/appointments/:id`

- **Lab Test Endpoints**:
  - `GET /api/labtests`
  - `GET /api/labtests/:id`
  - `POST /api/labtests`
  - `PUT /api/labtests/:id`

Detailed API documentation is available at `/api/docs` when the server is running.

## 👥 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📄 License

This project is licensed under the MIT License - see the LICENSE file for details.

---

© 2025 Hospital Management System