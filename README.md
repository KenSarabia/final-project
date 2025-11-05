Sarabia, Ken Andrie A.
BSIS 3B



Barangay Record and Information System (BRIS)

Project Overview
The Barangay Record and Information System (BRIS) is a web-based system designed to help barangays manage and organize resident records, clearance requests, and other essential barangay information efficiently.
This system aims to reduce manual paperwork, minimize errors, and provide faster service to residents.



Key Features

 Resident Management – Add, view, edit, and delete resident records.
 Barangay Clearance – Generate and manage barangay clearance requests.
 Household Records – Maintain household profiles with related members.
 Official Management – Manage barangay officials and their positions.
 User Authentication – Secure login system for authorized personnel.
 Reports and Data Tracking – View summary reports for better decision-making.
 System Setup Module – Configure barangay details and system settings.


Technologies Used
HTML5 - Structure and layout
CSS3 / Bootstrap - Design and responsive styling
JavaScript - Frontend interactivity
PHP - Backend logic and server-side processing
MySQL - Database management
XAMPP - Local development environment


Installation and Setup

1. Clone the Repository
   git clone [https://github.com/KenSarabia/final-project.git](https://github.com/KenSarabia/final-project.git)

2. Move the Project to XAMPP
   Place the folder inside your htdocs directory:
   C:\xampp\htdocs\barangay_ris_v1

3. Import the Database

    Open phpMyAdmin
    Create a new database (for example: barangay_ris_db)
    Import the Database.sql file included in the project folder

4. Configure the Connection
   Edit the file: includes/config.php
   Set your local database credentials:
   $host = "localhost";
   $user = "root";
   $pass = "";
   $db   = "barangay_ris_db";


Project Structure
barangay_ris_v1/
includes/        - Configuration and reusable PHP components
public/          - Publicly accessible files and main pages
Database.sql     - Database structure and sample data
README.txt       - Project documentation

Developers
Ken Andrie A. Sarabia
Calapan City, Oriental Mindoro
[kenandriesarabia@gmail.com](mailto:kenandriesarabia@gmail.com)


License
This project is for educational purposes only.
All rights reserved © 2025 Ken Andrie A. Sarabia.


Acknowledgments
Special thanks to my instructors, classmates, and everyone who supported the development of this project.


