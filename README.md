# Job Application Tracker

A PHP 8.4 application designed to centralize the job search process by tracking applications, interview schedules, contact information, and communication logs.

## Features
*   **Interactive Dashboard:** View upcoming interviews on a calendar and receive alerts for applications pending for more than 30 days.
*   **Job Management:** Track job titles, companies, descriptions, and unlimited personal research notes.
*   **Document Storage:** Support for dual file uploads per application (Resume and Cover Letter).
*   **Interaction Logs:** Record inbound and outbound communications (emails, calls) linked to specific jobs.
*   **Contact Manager:** Centralized database for recruiters and hiring managers.
*   **Reporting:** Filterable job reports with a dedicated printer-friendly view.
*   **Security:** CSRF protection on all forms, password hashing (BCRYPT), and secure session management.

## Installation & Setup
1.  **Database Configuration:** Open `config.php` and update the `$host`, `$db`, `$user`, and `$pass` variables to match your MySQL environment.
2.  **Schema Initialization:** Run `setup.php` in your browser. This script will automatically create the necessary tables (`statuses`, `contacts`, `jobs`, `communications`, `users`) and apply all required migrations.
3.  **Admin Account Creation:** During the setup process, you will be prompted to enter a custom administrator username and password. These credentials will be securely hashed and stored as the primary account for the application.
4.  **Accessing the App:** Once setup is complete, you will be redirected to the login page to sign in with the credentials you just created.

## Application Workflow
The sidebar provides navigation to the core modules:
1.  **Dashboard:** Your primary command center for daily tasks.
2.  **Contact Manager:** Add your network of recruiters before linking them to jobs.
3.  **Add New Job:** Log a new application, upload tailored documents, and set statuses.
4.  **Job Reports:** Filter history by status and generate physical copies for review.
5.  **Database Tools:** Regularly export a backup file to prevent data loss.
