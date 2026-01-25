# Citadel

Using AI to help the ER wait time crisis

<h2>Dependencies</h2>
pip install scikit-learn google-genai python-dotenv

<h2>Configurations + Keys</h2>
config/config.php: python path, db credentials, google maps API key <br>
backend/ai/get_wait_time.php: python path  <br>
backend/ai/.env: AI API Key

<h2>Steps to Run</h2>
1. Navigate to the ai folder in the terminal: python train_regression.py <br>
2. We used WAMP to run our project, you can import our citadel.sql for mock data

<h2>Don't get lost! Project map:</h2>

```text
project/
│
├── public/                          # All user-visible pages
│   ├── index.php                    # Landing page. Routes users to Login or Signup.
│   ├── login.php                    # Login form UI (POSTs to backend/auth/process_login.php)
│   ├── signup.php                   # Signup form UI (POSTs to backend/auth/process_signup.php)
│   │
│   ├── admin/                       # Admin-only pages (requireRole("admin"))
│   │   ├── home.php                 # Admin dashboard (Stats & Overview)
│   │   ├── addHospital.php          # Form/UI for creating hospitals
│   │   ├── approve_staff.php        # UI Table to verify doctor/nurse signups
│   │   ├── approve_admin.php        # UI Table to verify new admins
│   │
│   ├── staff/                       # Staff-only pages (requireRole("staff"))
│   │   ├── home.php                 # Triage Dashboard (Shows AI suggestions & charts)
│   │
│   └── patient/                     # Patient-only pages (requireRole("patient"))
│       ├── home.php                 # Patient dashboard (Shows dynamic wait time)
│       └── checkin.php              # Intake form (Sends data to AI Sanitization Layer)
│
│   ├── assets/                      # Frontend resources
│   │   ├── style.css
│   │   ├── anim.css
│
├── backend/                         # Logic, DB, controllers (not user-visible)
│   │
│   ├── db.php                      # mysqli connection
│   │   ├── anim.css
│
├── backend/                         # Logic, DB, controllers (not user-visible)
│   │
│   ├── db.php                       # mysqli connection
│   │
│   ├── ai/                          # The "AI Application Layer" (Python Microservices)
│   │   ├── triage_processor.py      # AI Model: Categorizes symptoms (Privacy-First)
│   │   |── .env                     # Stores your Gemini API Key securely
│   |   │── wait_time_predictor.py   # ML inference service that predicts patient wait times based on triage severity and current queue conditions
|   |   │── train_regression.py      # Offline training script for the wait-time regression model
│   |   │── severity_regression.pkl  # Pre-trained regression model mapping severity levels to expected service times
│   │
│   ├── auth/
│   │   ├── process_login.php
│   │   ├── process_signup.php
│   │   ├── logout.php
│   │   ├── process_login.php
│   │   ├── process_signup.php
│   │   ├── logout.php
│   │
│   ├── patient/
│   │   ├── create_checkin.php       # Sanitizes PII -> Calls backend/ai/triage_processor.py
│   │   ├── get_checkins.php         # History fetch & queue wait time getter
│   │   ├── get_waitlist.php         # Fetches live queue position & AI prediction
│   │   ├── get_hospitals.php        # Populates hospital dropdown
│   │
│   ├── staff/
│   │   ├── update_waitlist.php      # Saves manual overrides to DB
│   │   ├── get_pending.php          # Fetches patients waiting for Triage
│   │   ├── get_approved.php         # Fetches admitted patients
│   │   ├── get_severity_graph.php   # Returns JSON for dashboard analytics
│   │
│   ├── admin/
│   │   ├── add_hospital.php
│   │   ├── approve_staff.php
│   │   ├── approve_admin.php
│   │
│   └── export/
│       ├── export_pdf.php           # Generates hospital status reports
│
└── config/
    ├── config.php
    └── session.php                 # Auth middleware (Role-Based Access Control)
```
