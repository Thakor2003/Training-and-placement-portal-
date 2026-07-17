1	# Training & Placement Portal
2	
3	A secure, role-based Training & Placement Portal built with **PHP 8 + MySQL + Bootstrap 5**, designed for diploma/engineering colleges. Built with an MVC-inspired, modular structure so every module can be extended independently.
4	
5	---
6	
7	## ✅ What's included in this build
8	
9	| Layer | Status |
10	|---|---|
11	| Database schema (35+ normalized tables, FKs, indexes) | **Complete** — `database/schema.sql` |
12	| Sample seed data | **Complete** — `database/sample_data.sql` |
13	| Security core (CSRF, XSS escaping, password hashing, rate limiting, session hardening) | **Complete** — `includes/security.php` |
14	| Auth: Register, Login, Email Verify, Forgot/Reset Password, Logout | **Complete** — `auth/` |
15	| Design system (glassmorphism, dark/light mode, responsive) | **Complete** — `assets/css/style.css` |
16	| Public site: Home page with live stats | **Complete** — `index.php` |
17	| Role-based sidebar/topbar shell for all 4 roles | **Complete** — `includes/sidebar.php`, `includes/topbar.php` |
18	| Student Dashboard (profile meter, application funnel chart, recommended jobs) | **Complete** — `student/dashboard.php` |
19	| Admin / TPO / Company dashboards | **Scaffolded** — real stats wired to DB, ready for feature build-out |
20	
21	## 🗺️ Roadmap for remaining modules
22	
23	The foundation (auth, security, DB, design system, navigation shell) is done — everything below plugs into the same pattern used in `student/dashboard.php`:
24	
25	1. **Student**: Profile/Academic form, Skills & Certifications CRUD, Resume Builder + PDF export, Job/Internship search with AJAX filters, Application tracking, Saved jobs, Placement history, Mock Interview + AI feedback, Notifications, Calendar.
26	2. **Company**: Company profile, HR management, Post Job form, Applicant tracking board (Kanban-style shortlist/reject), Interview scheduling, Offer letter upload, Placement statistics.
27	3. **TPO**: Student/Company management tables (search, filter, pagination), Job approval workflow, Placement drive management, Interview scheduling, Eligibility rule builder, Notice board, Resume verification queue, Reports (PDF/Excel export), Analytics charts.
28	4. **Admin**: User management (activate/deactivate/reset), College settings, Department/Branch CRUD, Academic year management, Notification broadcast, System logs viewer, DB backup trigger, Security settings, CMS for Home/About/Gallery content.
29	5. **AI features**: Resume Analyzer + ATS scoring, Career recommendations, Skill gap analysis, AI interview question generator, AI chatbot, AI job matching — designed to call an LLM API (e.g. Claude) from a PHP service layer; `ai_interactions` table already stores prompts/outputs.
30	6. **Extra pages**: About, Placement Team, Recruiters, Gallery, Success Stories, Alumni, Events/Webinars, FAQ, Contact, Privacy Policy, Terms.
31	
32	Ask for any specific module by name and it'll be built to match this same design system, security model, and file structure.
33	
34	---
35	
36	## 📦 Installation Guide (XAMPP)
37	
38	1. **Install XAMPP** (PHP 8.1+, MySQL 8+, Apache).
39	2. Copy the `tpp/` folder into `C:\xampp\htdocs\tpp` (Windows) or `/Applications/XAMPP/htdocs/tpp` (Mac).
40	3. Start **Apache** and **MySQL** from the XAMPP control panel.
41	4. Open **phpMyAdmin** (`http://localhost/phpmyadmin`) and:
42	   - Run `database/schema.sql` to create the database and tables.
43	   - Run `database/sample_data.sql` to load demo data.
44	5. Open `config/database.php` and confirm `DB_USER` / `DB_PASS` match your MySQL setup (defaults: `root` / empty password — standard XAMPP).
45	6. Open `config/config.php` and set `BASE_URL` to match your local path, e.g. `http://localhost/tpp`.
46	7. Visit `http://localhost/tpp/index.php` in your browser.
47	
48	### Demo accounts (password for all: `Passw0rd!`)
49	
50	| Role | Email |
51	|---|---|
52	| Super Admin | admin@tpp.local |
53	| TPO | tpo@tpp.local |
54	| Student | student@tpp.local |
55	| Company | company@tpp.local |
56	
57	> Note: outgoing email (verification/reset links) uses PHP's built-in `mail()` function, which requires a configured mail server (or a tool like Mercury Mail in XAMPP, or swapping in PHPMailer + SMTP for real delivery). For local testing, verification tokens can be read directly from the `email_verifications` / `password_resets` tables.
58	
59	---
60	
61	## 🗂️ Folder structure
62	
63	```
64	tpp/
65	├── admin/              Super Admin module
66	├── api/                AJAX/JSON endpoints (search, filters, notifications)
67	├── assets/
68	│   ├── css/style.css   Design system (glassmorphism, dark/light)
69	│   ├── js/main.js      Theme toggle, sidebar, AJAX helpers
70	│   └── uploads/        Resumes, offer letters, logos, photos
71	├── auth/               Login, Register, Password reset, Email verification
72	├── company/            Recruiter module
73	├── config/             Database + app configuration
74	├── database/           schema.sql, sample_data.sql
75	├── includes/           Shared partials: head, sidebar, topbar, footer, functions, security
76	├── pages/              Public pages: About, FAQ, Contact, Privacy, Terms...
77	├── student/             Student module
78	├── tpo/                Training & Placement Officer module
79	└── index.php           Public home page
80	```
81	
82	## 🔒 Security implemented
83	
84	- **SQL Injection**: PDO prepared statements everywhere, `ATTR_EMULATE_PREPARES` disabled.
85	- **XSS**: All dynamic output passed through `e()` (htmlspecialchars).
86	- **CSRF**: Token generated per session, verified on every POST via `verify_csrf()`.
87	- **Passwords**: bcrypt via `password_hash()` (cost 12).
88	- **Sessions**: `httponly`, `strict_mode`, `samesite=Lax` cookies, ID regenerated on login.
89	- **Rate limiting**: Login attempts throttled per email per session window.
90	- **Access control**: `requireRole()` guards every protected page server-side.
91	
92	## 🎨 Design system
93	
94	- Palette: deep indigo institutional base + amber "achievement" accent.
95	- Typography: Fraunces (display) + Inter (body).
96	- Glassmorphism panels on auth/marketing screens, clean flat cards in dashboards.
97	- Dark/Light mode via `data-theme` attribute + `localStorage`, respects `prefers-reduced-motion`.
98	- Fully responsive: sidebar collapses to an off-canvas drawer under 900px.
99	
100	---
101	
102	## Next steps
103	
104	Tell me which module to build next (e.g. "build the Resume Builder + PDF export" or "build the TPO Student Management screen with search/filter/pagination") and it'll be delivered in the same architecture, ready to drop in.
