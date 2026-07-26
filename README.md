# Admin Trainee Dashboard

A simple internal web application designed for administrators to manage trainees (interns), assign tasks, and monitor daily attendance. This project is structured as a full-stack web application suitable for an undergraduate mini-project, focusing on clean procedural code, structured design components, and standard PHP/MySQL functionalities.

---

## 🛠️ Project Structure
```text
admin-trainee-dashboard/
│
├── css/
│     style.css         # Custom layout styling (using CSS variables)
│
├── js/
│     script.js         # Client-side form validation and alerts
│
├── includes/
│     db.php            # Database connection script
│     header.php        # Header template with authentication gate
│     sidebar.php       # Sidebar layout
│     footer.php        # Footer layout script and script loads
│
├── login.php           # Admin login panel
├── dashboard.php       # Core metrics statistics dashboard
├── trainees.php        # Trainee lists and query filters
├── add_trainee.php     # Trainee registration form
├── edit_trainee.php    # Prefilled trainee update form
├── delete_trainee.php  # Trainee delete handler
├── assign_task.php     # Work assignment form and filterable table
├── edit_task.php       # Prefilled task update form
├── delete_task.php     # Task delete handler
├── attendance.php      # Daily attendance sheet and logged history grid
├── logout.php          # Session termination script
├── database.sql        # Database tables schema and sample records
└── README.md           # Documentation
```

---

## 🚀 How to Setup and Run the Project

### 1. File Placement inside XAMPP
1. Locate your XAMPP installation directory (typically `C:\xampp` or `D:\xampp`).
2. Open the `htdocs` folder.
3. Create a folder named `internshipproject` (or download/extract this repository contents directly there).
4. Make sure the files are directly under `xampp/htdocs/internshipproject/`.

### 2. Import Database Schema
1. Open the XAMPP Control Panel and start the **Apache** and **MySQL** modules.
2. Open your web browser and navigate to `http://localhost/phpmyadmin/`.
3. Click on the **New** button in the left sidebar to create a new database.
4. Name the database `admin_trainee_db` and click **Create**.
5. Select the newly created `admin_trainee_db` database.
6. Go to the **Import** tab at the top menu.
7. Click **Choose File** and select the `database.sql` file located in the project folder root.
8. Click **Import** (or **Go** at the bottom) to execute and populate the database tables with sample records.

### 3. Database Configuration
The database connection settings are located in `includes/db.php`. By default, it is configured for standard XAMPP setup:
* **Host**: `localhost`
* **Username**: `root`
* **Password**: `""` (Empty string)
* **Database**: `admin_trainee_db`

*If your MySQL user password differs from the blank XAMPP default, update the `$password` variable inside [includes/db.php](file:///d:/xampp/htdocs/internshipproject/includes/db.php).*

### 4. Running the Project
1. Open your web browser.
2. Navigate to: `http://localhost/internshipproject/login.php` (or `http://localhost/internshipproject/` if index redirection exists).
3. Use the following default admin credentials to log in:
   * **Username**: `admin`
   * **Password**: `admin123`

---

## 🔒 Security Implementation
* **Session Gates**: All dashboard pages check `$_SESSION['admin_logged_in']` and redirect unauthenticated requests to `login.php`.
* **Password Hashing**: Admin credentials stored in `database.sql` are securely hashed using PHP `password_hash()` and verified using `password_verify()`.
* **SQL Injection Prevention**: All search queries, inserts, updates, and deletes use dynamic PHP `mysqli_prepare` prepared statements to securely bind input variables.
* **Client & Server-side Validation**: Text inputs are checked on submission on the client side (using `js/script.js` listeners) and validated on the server side (using PHP filter functions).

---

## 🔮 Future Scope (40% Remaining Features)
To represent approximately 60% completion of a full-scale portal, the following expansion ideas can be implemented in future phases:
1. **Trainee Login Portal**: Allow trainees to log in using their email and a default password, letting them view assigned tasks and mark their progress (Pending -> In Progress -> Completed).
2. **Interactive Analytics Charts**: Integrate Chart.js into the dashboard to visualize monthly attendance stats and task completion progress.
3. **Pagination Support**: Add cursor or offset pagination on the Trainees, Tasks, and Attendance logs tables to support larger datasets.
4. **Export Capabilities**: Generate CSV or PDF reports for trainee performance, task listings, and attendance logs.
5. **Profile Image Uploads**: Add a profile picture upload feature during trainee creation/editing, storing files in an `uploads/` directory.
6. **Task Comments and File Attachments**: Enable chat threads or document attachments (e.g. PDFs, images) on tasks for feedback between trainees and the admin.
