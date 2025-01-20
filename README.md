# youdemy

# Youdemy - Interactive Learning Platform

## Project Description
Youdemy is an online learning platform designed to provide interactive and personalized educational experiences for students and teachers. The platform supports a robust back office for administrators, ensuring efficient management of courses, users, and enrollments. Built with a strong emphasis on Object-Oriented Programming (OOP) principles, secure authentication mechanisms, and responsive design, Youdemy aims to revolutionize e-learning.

---

## Features

### **Front Office**
#### **For Visitors:**
- Browse available courses.
- Search courses by title, category, or tags.
- Register as a student or teacher.

#### **For Students:**
- Enroll in courses.
- Access enrolled courses and view content (videos, documents, etc.).
- Track course progress.

#### **For Teachers:**
- Add new courses with details such as title, description, content (videos/documents), tags, and category.
- Manage courses: Modify, delete, and view student enrollments.
- Access course statistics, including the number of students enrolled and total courses created.

### **Back Office**
#### **For Administrators:**
- Manage users (students, teachers, and admins).
- Monitor and manage courses and enrollments.
- View platform-wide statistics (e.g., total users, active courses, enrollments).

---

## Technologies Used

### Backend:
- **PHP**: Core backend development with PDO for database interaction.
- **MySQL**: Relational database to manage platform data (users, courses, enrollments, etc.).

### Frontend:
- **HTML**: Structure of the web pages.
- **CSS (Tailwind CSS)**: Styling and responsive design.
- **JavaScript**: Dynamic and interactive features for users.

### Security:
- **Password Hashing**: Using PHP's `password_hash()` and `password_verify()` for secure authentication.
- **Input Validation**: Ensuring data integrity and protection against SQL injection.

---

## Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/yourusername/youdemy.git
   ```

2. Navigate to the project directory:
   ```bash
   cd youdemy
   ```

3. Set up the database:
   - Import the `youdemy.sql` file into your MySQL database.
   - Update the database configuration in the `Database` class.

4. Start a local PHP server:
   ```bash
   php -S localhost:8000
   ```

5. Access the application at [http://localhost:8000](http://localhost:8000).

---

## Usage

### Login and Registration
- Visitors can register as a **Student** or **Teacher** via the registration form.
- Existing users can log in using their email and password.

### Teachers
- Navigate to the "Manage Courses" section to create or update courses.
- Access "Course Statistics" for insights into course performance.

### Students
- Browse courses and enroll in the desired ones.
- Access enrolled courses and track progress.

### Administrators
- Use the admin dashboard to manage users, courses, and platform-wide data.

---

## Directory Structure
```
|-- classes/
|   |-- Database.php        # Database connection
|   |-- User.php            # User class for authentication
|   |-- Teacher.php         # Teacher class for course management
|   |-- Student.php         # Student class for enrollments
|   |-- Admin.php           # Admin class for back office
|
|-- pages/
|   |-- login.php           # Login page
|   |-- register.php        # Registration page
|   |-- dashboard.php       # Main dashboard
|
|-- css/
|   |-- styles.css          # Custom styles
|
|-- js/
|   |-- main.js             # Frontend interactivity
|
|-- index.php               # Entry point
|-- youdemy.sql             # Database schema
```

---

## Future Enhancements

- Implement advanced course analytics (e.g., completion rates).
- Add support for drag-and-drop content creation for teachers.
- Introduce a recommendation system for students based on their interests.
- Enable video streaming with progress tracking.

---


