ITHM CENTRAL MANAGEMENT SYSTEM (CMS)

Comprehensive Technical Specification & Solution Architecture

Fee Related Notification Update:

Fee related notifications are system triggered as well as can be manually triggered by both Main Campus Admin and Sub Campus Admin.

\------------------------------------------------------------

1\. SYSTEM OVERVIEW

The ITHM CMS is a centralized management platform designed for the Institute of Tourism and Hospitality Management, enabling multi-campus operations, complete admission lifecycle management, fee processing workflows, and automated notifications.

Technology Stack:

\- PHP Core

\- MySQL

\- Tailwind CSS

\- HTML & CSS

\- JavaScript (Alpine.js preferred, Vue optional)

User Roles:

1\. System Admin

2\. Main Campus Admin

3\. Sub Campus Admin

4\. Student

\------------------------------------------------------------

2\. USER ROLES & PERMISSIONS

System Admin:

\- Manage all technical and system-wide configurations.

\- Add/edit/delete users, roles.

\- Manage SMTP, API settings.

\- Manage image/document storage.

\- Create/edit/Delete main campus and sub campuses.

\- Manage institute information: name, logo, focal person, details.

\- Manage full student dataset across all campuses.

Main Campus Admin:

\- Access all campuses' data.

\- Manage course settings, fee structures, custom fee schedules.

\- Review admission applications across all campuses.

\- Perform fee verification.

\- Trigger fee reminders manually.

\- Manage OTC (over-the-counter) admission submissions.

Sub Campus Admin:

\- Access only their own campus data.

\- Review, approve, reject, or update required admissions.

\- Verify fee submissions.

\- Assign roll numbers.

\- Trigger fee reminders manually.

\- Manage certificate distribution for their campus.

Student:

\- Apply for admissions.

\- Upload documents.

\- Download fee vouchers.

\- Upload manual payment proof.

\- Track status updates.

\- Receive notifications.

\------------------------------------------------------------

3\. SYSTEM MODULES

Authentication:

\- Email/password login.

\- Forgot password.

\- Role based access.

Campus Management:

\- System Admin manages main and sub campuses.

\- Auto-fetched details for admission forms and fee vouchers.

Course & Fee Structure Management:

\- Accessible only to Main Campus Admin.

\- Attach fee schedules, admission fees, semester/monthly fees.

\- Course-campuses mapping.

Admission Management:

Student Side:

\- Apply to any course in any campus.

\- Multisection form: personal, guardian, academics, documents, CNIC/B-form.

Admin Side:

\- Review submitted applications.

\- Approve, reject, mark update-required.

\- Auto-generate fee voucher upon approval.

Fee Voucher Module:

\- Auto-generated voucher with auto-fetched structure, due date.

\- Student downloads PDF.

\- Uploads transaction ID, screenshot, receipt.

\- Admin verifies manually.

Fee Notifications:

\- Automatic system-triggered notifications:

\* New fee voucher created

\* Fee due reminder

\* Fee verification result (approved/rejected)

\- Admin-triggered notifications:

\* Manual fee reminders (Main Campus Admin or Sub Campus Admin)

Student Dashboard:

\- View application statuses, fee vouchers, notifications, certificates.

Certificate Management:

\- Admin uploads certificate files.

\- Students receive notification.

\- Download option available.

\------------------------------------------------------------

4\. ADMIN DASHBOARD METRICS

Main Campus Dashboard:

\- Total students across all campuses.

\- Admissions (pending, approved, rejected).

\- Students by course, batch, year, shift.

\- Fee defaulters.

\- Seat availability.

\- Manual fee reminders panel.

Sub Campus Dashboard:

\- Same as above but filtered to campus-specific data.

\------------------------------------------------------------

5\. NOTIFICATION SYSTEM

Automated (System generated):

\- Admission status updates.

\- Fee voucher issuance.

\- Fee due reminders.

\- Fee approval or rejection.

\- Certificate availability.

Manual (Admin triggered):

\- Fee reminders:

\* Main Campus Admin → All campuses or specific campus

\* Sub Campus Admin → Only their campus

\------------------------------------------------------------

6\. DATABASE STRUCTURE (High-Level)

Core Tables:

\- users

\- roles

\- campuses

\- courses

\- fee_structures

\- admissions

\- admission_documents

\- fee_vouchers

\- fee_payments

\- notifications

\- certificates

\- settings

\------------------------------------------------------------

7\. SECURITY & VALIDATION

\- Input sanitization.

\- Server-side validation.

\- Role restricted access control.

\- Password hashing using bcrypt.

\- Limited file types for uploads.

\- Session timeout and CSRF protection.

\------------------------------------------------------------

8\. DATA FLOW

Admission Flow:

Student submits → Admin reviews → Approval → Auto fee voucher → Student payment → Admin verification → Roll number assignment → Enrollment confirmed

Fee Notification Flow:

System auto triggers (voucher/due/verification) OR admin manually triggers reminder.

Certificate Flow:

Admin uploads → System notifies (To download and Collect the printed copy as well) → Student downloads.

\------------------------------------------------------------

This document provides the final structured specification ready for development in PHP Core, MySQL, Tailwind, HTML, CSS, and JS.