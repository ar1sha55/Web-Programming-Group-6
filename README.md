**TEKAN EDIT UNTUK BACA**

Flow of Project:
college-accommodation-system/
│
**Arisha**
├── config.php                     ← DB config constants (DB_HOST, DB_USER...)
├── db_connect.php                 ← DB connection using config.php
├── create_db.php                  ← One-time DB schema + seed data creation
│
**Nazmi**
├── login.php                      ← Login form
├── login_process.php             ← Verifies login, sets session
├── logout.php                     ← Ends session
├── auth.php                       ← Session/role-based access protection
├── log_helper.php                 ← logAction() helper for history_log
│
**Tina and Arisha**
├── admin_page.php                 ← Admin dashboard
├── manage_users.php               ← Admin view of user table, add/edit/delete
├── add_user.php                   ← Admin inserts user
├── edit_user.php                  ← Admin edits user
├── delete_user.php                ← Admin deletes user
├── view_logs.php                  ← Admin views history log
│
**Nazmi**
├── edit_profile.php               ← User updates own profile (student/manager)
├── update_profile.php             ← Saves updated profile
│
**Nazmi and Khairul**
├── student_page.php               ← Student dashboard
├── apply_accommodation.php       ← Form to apply for college
├── submit_application.php         ← Handles application insert
├── view_my_application.php        ← Student views their own application(s)
│
**Khairul and Nazmi**
├── manager_page.php               ← Manager dashboard
├── view_applications.php          ← Manager views all applications
├── process_application.php        ← Manager approves/rejects applications
├── manager_report.php             ← Searchable/sortable list of apps
│
├── style.css                      ← Global styles
├── form_validation.js             ← Validates inputs across forms
├── table_filter.js                ← (Optional) Enables live table search/sort


Pages yang perlukan file form_validation.js (untuk masa sekarang):

1.apply_accommodation.php
Reason: This page has a form where the student selects a college and inputs a preferred start date. The validation checks:
Preferred start date must not be in the past.

2.edit_profile.php (for both students and managers)
Reason: This page contains a form to edit profile fields (e.g., phone number, email). The validation checks:
Email format.
Phone number format (10–15 digits).

3.add_user.php
Reason: Admin adds new users (student, manager, or admin). The validation ensures the form fields are correctly filled out, like:
Password length (at least 5 characters).

4.edit_user.php
Reason: Admin edits user data. This page should also include the validation to ensure that changes made (e.g., password, email, phone) are valid.
