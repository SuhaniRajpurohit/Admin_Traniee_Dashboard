CREATE TABLE IF NOT EXISTS admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS trainees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(20) NOT NULL,
    batch VARCHAR(50) NOT NULL,
    department VARCHAR(100) NOT NULL,
    joining_date DATE NOT NULL,
    status ENUM('Active', 'Completed') DEFAULT 'Active'
);

CREATE TABLE IF NOT EXISTS tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    trainee_id INT NOT NULL,
    task_title VARCHAR(150) NOT NULL,
    task_description TEXT NOT NULL,
    assigned_date DATE NOT NULL,
    due_date DATE NOT NULL,
    priority ENUM('Low', 'Medium', 'High') DEFAULT 'Medium',
    status ENUM('Pending', 'In Progress', 'Completed') DEFAULT 'Pending',
    FOREIGN KEY (trainee_id) REFERENCES trainees(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS attendance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    trainee_id INT NOT NULL,
    date DATE NOT NULL,
    status ENUM('Present', 'Absent') NOT NULL,
    FOREIGN KEY (trainee_id) REFERENCES trainees(id) ON DELETE CASCADE,
    UNIQUE KEY unique_trainee_date (trainee_id, date)
);

INSERT INTO admin (username, password) VALUES 
('admin', '$2y$10$h7B11Mwm4mfz9DhbRjoXEeWzAzXEl2kZooB8ddhWDcPAgc.V9jiBa')
ON DUPLICATE KEY UPDATE id=id;

INSERT INTO trainees (full_name, email, phone, batch, department, joining_date, status) VALUES
('Dev Kumar', 'devkumar@gmail.com', '9876543210', 'Batch-A (2026)', 'Web Development', '2026-06-01', 'Active'),
('Sanjay Gupta', 'sanjaygupta@gmail.com', '9876543211', 'Batch-A (2026)', 'UI/UX Design', '2026-06-01', 'Active'),
('Prakarti Jain', 'prakartijain@gmail.com', '9876543212', 'Batch-B (2026)', 'Backend Development', '2026-07-01', 'Active'),
('Ekta Arora', 'ektaarora@gmail.com', '9876543213', 'Batch-C (2025)', 'Full Stack', '2025-10-01', 'Completed')
ON DUPLICATE KEY UPDATE id=id;

INSERT INTO tasks (trainee_id, task_title, task_description, assigned_date, due_date, priority, status) VALUES
(1, 'Implement Login Feature', 'Create a secure login page using PHP sessions and password hashing.', '2026-07-10', '2026-07-15', 'High', 'Completed'),
(1, 'Build Dashboard Statistics', 'Query the database to get counts of trainees, tasks, and display them.', '2026-07-16', '2026-07-22', 'Medium', 'In Progress'),
(2, 'Design Sidebar Navigation', 'Create a responsive sidebar UI with a dark green theme.', '2026-07-12', '2026-07-18', 'Medium', 'Completed'),
(3, 'Setup Database Migration Schema', 'Create the SQL script with appropriate foreign keys and indexes.', '2026-07-20', '2026-07-25', 'Low', 'Pending')
ON DUPLICATE KEY UPDATE id=id;

INSERT INTO attendance (trainee_id, date, status) VALUES
(1, '2026-07-23', 'Present'),
(2, '2026-07-23', 'Present'),
(3, '2026-07-23', 'Absent'),
(1, '2026-07-24', 'Present'),
(2, '2026-07-24', 'Present'),
(3, '2026-07-24', 'Present')
ON DUPLICATE KEY UPDATE id=id;
