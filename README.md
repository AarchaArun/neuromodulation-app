# Neuromodulation Form Application

This project was developed as part of **The Walton Centre NHS Foundation Trust coding challenge**.  
It’s a small, self-contained PHP web application that allows clinicians or administrators to record and manage Neuromodulation form data.

The app is built using **PHP (no framework)**, **Bootstrap**, **jQuery**, and **Microsoft SQL Server** for data storage.  
All database operations (Create, Read, Update, Delete) are handled through **stored procedures**, following good database practice.

---

##  What This App Does

- Collects **patient details** (name, DOB, age — automatically calculated)
- Captures answers for the **Brief Pain Inventory (BPI)** form
- Automatically calculates a **Total Score** as the form is filled out
- Stores submissions in the database
- Provides an **Admin View** to:
  - See all completed forms
  - Edit or delete entries
  - View full patient responses
- Includes a simple **admin login** for access control

---

##  Technology Overview

| Technology | Purpose |
|-------------|----------|
| **PHP 8.3+** | Backend logic and data handling |
| **jQuery** | Front-end interactivity (score calculations, field updates) |
| **Bootstrap 5** | Layout and responsive design |
| **SQL Server 2019 (Express)** | Database engine |
| **Stored Procedures** | Used for all CRUD operations |
| **IIS / PHP Development Server** | Runs the app locally |
| **Git / GitHub** | Version control and repository management |

---

##  Installation & Environment Setup

1. Requirements
    Make sure you have the following installed:
    - PHP 8.3 or later (with `sqlsrv` and `pdo_sqlsrv` drivers)
    - Microsoft SQL Server (Express or Developer edition)
    - IIS (Internet Information Services)
    - Git


2. Clone the Repository


    git clone https://github.com/AarchaArun/neuromodulation-app.git
    cd neuromodulation-app


3. Database Setup

    -> Open SQL Server Management Studio (SSMS).

    -> Create a new database:

        CREATE DATABASE NeuromodulationDB;
        GO

    -> Execute the following scripts in this order:

        Database/create_tables.sql
        Database/stored_procedures.sql

        These scripts will create:
            Tables for storing form data
            The following stored procedures:
                add_PatientForm
                get_AllPatientForms
                get_PatientFormById
                update_PatientForm
                delete_PatientForm


4.  Environment Setup

    Copy .env.example → .env
    Update .env with your local SQL Server credentials and desired admin login:

    DB_SERVER=localhost\SQLEXPRESS
    DB_NAME=NeuromodulationDB
    DB_USER=your_sql_username
    DB_PASS=your_sql_password

    ADMIN_USER=admin
    ADMIN_PASS=admin123

    If using Windows Authentication, leave DB_USER and DB_PASS blank.


5. Run the Application

    You can use either IIS or PHP’s built-in server.

    Option A – PHP built-in server
        php -S localhost:8000 -t public
        Visit: http://localhost:8000/form.php

    Option B – IIS

        Set your IIS site’s physical path to the project’s public folder.
        Then visit: http://localhost/neuromodulation-app


6. Admin Login

    Visit: http://localhost:8000/admin/login.php

    Default credentials:
    Username: admin
    Password: admin123

---    

### .gitignore Setup

This project includes a .gitignore file to ensure sensitive data and unnecessary files are not pushed to GitHub.

Content:

    # Ignore environment configuration
    .env

    # Ignore system and cache files
    .DS_Store
    Thumbs.db
    *.log
    /cache/

    # Ignore dependencies if added later
    vendor/

    # Ignore IDE/editor files
    .vscode/
    .idea/

---

### Development Decisions

    Built using core PHP only (no frameworks)

    All CRUD operations handled through stored procedures

    Age auto-calculated from Date of Birth using JavaScript

    Total Score calculated dynamically using jQuery

    .env file used for environment configuration (no hardcoding)

    Admin credentials validated via environment variables

    Consistent version control via .gitignore

    Clean UI using Bootstrap 5

    Session-based admin authentication implemented for security

---

### Additional Enhancement: Filtering & Sorting (Optional)

    The Admin panel includes a small usability improvement: the ability to filter and sort submitted records directly in the browser.

    - A search box allows real-time filtering by name, age, DOB, score, or submission date.

    - Table columns show up/down arrows for sorting.

    - Sorting works for text, numbers, and dates.

    All functionality is implemented using lightweight JavaScript — no plugins required.
    
---

### Notes

    This project was created for a technical evaluation purpose only.

    It is not intended for clinical or production use.