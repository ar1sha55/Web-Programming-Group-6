**TEKAN EDIT UNTUK BACA**

**Aku dah letak template project yang aku dah buat, korang boleh try run kat localhost korang try explore and fahamkan dulu. file kat sini tak lengkap sangat pun. korang boleh adjust and tambah apa yang patut ikut korang punya part. and yang paling penting, korang kena design sendiri file yang ada html page ikut part masing masing.**

**Flow of Project:**
**TICK GUNA ✅ KALAU DAH COMPLETE**

college-accommodation-system/
│
**Arisha**
├── config.php                     ← DB config variable
├── db_connect.php                 ← DB connection using config.php
├── create_db.php                  ← One-time DB schema + seed data creation
│
**Nazmi**
├── login.php   ✅               ← Login form
├── login_process.php  ✅            ← Verifies login, sets session
├── logout.php  ✅                    ← Ends session
├── auth.php    ✅                    ← Session/role-based access protection
├── log_helper.php  ✅                ← logAction() helper for history_log
│
**Tina and Arisha**
├── admin_page.php   (nazmi)              ← Admin dashboard
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
├── student_page.php      (nazmi)        ← Student dashboard
├── apply_accommodation.php       ← Form to apply for college
├── submit_application.php         ← Handles application insert
├── view_my_application.php        ← Student views their own application(s)
│
**Khairul and Nazmi**
├── manager_page.php     (nazmi)          ← Manager dashboard
├── view_applications.php          ← Manager views all applications
├── process_application.php        ← Manager approves/rejects applications
├── manager_report.php             ← Searchable/sortable list of apps
│
letak file.css korang kat sini untuk mudah track
├── style.css                      ← Global styles
├── dashboard.css                      ← style untuk dashboard
├── form_validation.js             ← Validates inputs across forms
├── table_filter.js                ← (Optional) Enables live table search/sort


**JAVASCRIPT (DOM)**
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

**HTML & CSS**
kalau letak dekat semua dekat style.css aku rasa susah nak synckan and boleh pening. so aku rasa:
untuk file .css untuk styling html page, korang boleh buat file .css khas. contoh untuk index.php -> index.css, tapi untuk admin, manager and student punya dashboard boleh satukan sebab sama je untuk consistency -> dashboard.css

jangan lupa untuk letak  <link rel="stylesheet" href="style.css"> (wajib) and <link rel="stylesheet" href="FILECSSKHAS.css"> kat bahagian head


