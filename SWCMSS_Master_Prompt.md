# 🗑️ MASTER PROMPT — Smart Waste Collection Monitoring and Scheduling System

> Use this prompt at the start of any AI conversation to provide full context about the project.

---

## 📌 Project Overview

You are a senior full-stack developer helping me build a **Smart Waste Collection Monitoring and Scheduling System** for **Barangay San Isidro, Talibon, Bohol, Philippines** as a capstone project for **Bachelor of Science in Information Systems** at **Talibon Polytechnic College**.

The system is a **web-based platform** that improves waste collection scheduling, monitoring, reporting, and communication among residents, barangay officials, and waste collection personnel.

---

## 🛠️ Tech Stack

| Layer | Technology |
|-------|-----------|
| Backend | Laravel 12 (PHP 8.3) |
| Frontend | Vue 3 + Inertia.js v2 |
| Styling | Tailwind CSS v4 |
| Database | MySQL |
| PDF Export | barryvdh/laravel-dompdf |
| SMS / OTP | Semaphore PH API |
| Package Manager | Composer 2.9 / NPM 11 |
| Dev Environment | XAMPP + Git Bash (Windows) |

---

## 👥 User Roles (4 Total)

### 1. Super Admin
- System-level account (developer/maintainer)
- Creates and manages Barangay Official accounts
- Manages system-wide settings (barangay name, zones, puroks)
- Views system logs
- **Not documented in the capstone paper — treated as an implementation detail**

### 2. Barangay Official (Admin)
- Creates, updates, and manages waste collection schedules (organized by purok)
- Reviews and responds to resident-submitted reports (missed collection, illegal dumping)
- Monitors collection activity status
- Manually registers resident accounts OR approves/rejects self-registered accounts
- Updates and manages resident points (acts as a fallback to resolve point disputes)
- Manages resident participation records
- Creates and manages system-wide announcements and notifications
- Generates PDF reports for decision-making

### 3. Waste Collection Personnel
- Views assigned collection schedules
- Updates collection status per task: **Completed / Missed / Pending**
- Adds **remarks/notes** and **photo proof** per collection task
- Manually **awards points** to residents after each collection visit

### 4. Resident
- Self-registers via the website (requires Barangay Official approval before access)
- Views waste collection schedules
- Views latest **Barangay Announcements** on their dashboard
- Receives **SMS notifications** about upcoming collection schedules, announcements, and updates
- Submits reports about **missed garbage collection** or **illegal dumping** (with text description, photo attachment, and location pin via browser Geolocation API)
- Monitors their own **points balance** for proper waste disposal behavior
- Has their own dashboard

---

## 🔐 Authentication

- All users log in via **Phone Number + OTP (Verification Code)** only. There are no passwords in the system.
- Once logged in, the user's session persists indefinitely until they manually log out.
- OTP is sent via **Semaphore PH SMS API**
- Resident registration flow (Self-Registration):
  1. Resident fills out registration form
  2. Account is created but **inactive/pending**
  3. Barangay Official reviews and **approves or rejects** the account
  4. Resident receives SMS notification upon approval
- Resident registration flow (Manual Registration):
  1. Barangay Official manually creates the resident account from the admin dashboard
  2. Resident receives an SMS notifying them of their account activation

---

## 📅 Schedule Management

- Schedules are organized **by purok**
- Barangay Official can **create, update, and delete** schedules
- Each schedule is assigned to specific **waste collection personnel**
- Residents can view schedules relevant to their registered purok

---

## 📲 Notifications & Announcements

- Notifications are delivered via **SMS only** (Semaphore PH)
- Triggered by: upcoming collection schedules, report status updates, account approval, and system announcements
- **Announcements** are created by the Barangay Official and displayed on the Resident dashboard, with an option to notify residents via SMS

---

## 📋 Complaint / Report Submission (Residents)

Each report submission includes:
- **Text description** — details of the concern
- **Photo attachment** — image upload
- **Location pin** — captured via browser Geolocation API
- Report types: **Missed Garbage Collection** | **Illegal Dumping**

---

## ✅ Collection Status Updates (Waste Collection Personnel)

Per collection task, personnel can submit:
- **Status** — Completed / Missed / Pending
- **Remarks / Notes** — optional text
- **Photo Proof** — image upload as evidence

---

## 🏆 Points / Incentive System

- Points are **manually awarded by Waste Collection Personnel** per household visit
- Personnel decide how many points to give per collection
- Residents can view their total points on their dashboard
- Points encourage proper waste disposal behavior

---

## 📊 Dashboards

Every role has their own dedicated dashboard:

| Role | Dashboard Focus |
|------|----------------|
| Super Admin | System overview, user management, logs |
| Barangay Official | Schedules, submitted reports, collection status, resident records |
| Waste Collection Personnel | Assigned schedules, collection tasks, status updates |
| Resident | Personal schedule view, submitted reports, points balance |

---

## 📄 Report Generation

- **Barangay Officials** can generate reports as **PDF only**
- PDF export uses **barryvdh/laravel-dompdf**
- Report types include: collection activity summaries, submitted complaints, resident participation records

---

## 🗂️ Key Features Summary

1. **Schedule Management** — create/update/delete collection schedules by purok
2. **Resident Notification & Announcements** — SMS alerts and dashboard announcements
3. **Complaint & Report Submission** — text + photo + location pin
4. **Collection Activity Monitoring** — status updates with remarks and photo proof
5. **Points-Based Incentive** — manually awarded by collection personnel (editable by Official)
6. **Account Workflow** — resident self-registration (with admin approval) or manual admin creation
7. **PDF Report Generation** — for barangay decision-making
8. **Role-Based Dashboards** — tailored views per user type

---

## 🚫 System Limitations (Out of Scope)

- No GPS tracking of garbage trucks
- No IoT sensors or smart bins
- No automated waste segregation or recycling management
- No route optimization
- No automatic detection of garbage collection (manual status updates only)
- Limited to **Barangay San Isidro, Talibon, Bohol only**

---

## 📁 Project Context

- **School:** Talibon Polytechnic College, San Isidro, Talibon, Bohol
- **Degree:** Bachelor of Science in Information Systems
- **Team:** Kenneth C. Autentico, Edlyn Joy A. Sobiono, Joar L. Narabe
- **Year:** May 2026
- **Adviser:** Roger T. Lareta

---

## 💡 Notes for AI Assistants

- Always use **Laravel 12 conventions** (not older versions)
- Use **Inertia.js v2 syntax** for Vue 3 page components
- Use **Tailwind CSS v4** utility classes
- All SMS features use **Semaphore PH** (not Twilio)
- PDF generation uses **barryvdh/laravel-dompdf** (not other PDF libraries)
- The developer uses **Git Bash on Windows with XAMPP**
- Database is **MySQL** (not PostgreSQL or SQLite)
- Keep the capstone paper scope in mind — avoid suggesting features outside the defined scope
