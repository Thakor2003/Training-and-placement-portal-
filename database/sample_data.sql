1	USE tpp_db;
2	
3	-- Departments & Branches
4	INSERT INTO departments (name, code) VALUES ('Computer Engineering','CE'), ('Information Technology','IT'), ('Mechanical Engineering','ME');
5	INSERT INTO branches (department_id, name, code) VALUES
6	(1,'Computer Engineering','CE'), (2,'Information Technology','IT'), (3,'Mechanical Engineering','ME');
7	
8	INSERT INTO academic_years (year_label, is_current) VALUES ('2025-2026', 1);
9	
10	-- College settings
11	INSERT INTO college_settings (setting_key, setting_value) VALUES
12	('college_name', 'K.D. Polytechnic'),
13	('college_address', 'Patan, Gujarat, India'),
14	('placement_email', 'placements@kdpolytechnic.edu.in'),
15	('logo_path', 'assets/img/logo.png');
16	
17	-- Users: password for ALL sample accounts below is  Passw0rd!
18	-- (hash generated with PHP password_hash, bcrypt)
19	INSERT INTO users (role_id, full_name, email, password_hash, is_email_verified, is_active) VALUES
20	(1, 'System Administrator', 'admin@tpp.local', '$2y$10$7EqJtq98hPqEX7fNZaFWoOhi5X.MgxdrbKzHJnHNyPYuwqwMnZbC.', 1, 1),
21	(2, 'Priya Patel (TPO)', 'tpo@tpp.local', '$2y$10$7EqJtq98hPqEX7fNZaFWoOhi5X.MgxdrbKzHJnHNyPYuwqwMnZbC.', 1, 1),
22	(3, 'Sanjay Thakor', 'student@tpp.local', '$2y$10$7EqJtq98hPqEX7fNZaFWoOhi5X.MgxdrbKzHJnHNyPYuwqwMnZbC.', 1, 1),
23	(4, 'TechNova HR', 'company@tpp.local', '$2y$10$7EqJtq98hPqEX7fNZaFWoOhi5X.MgxdrbKzHJnHNyPYuwqwMnZbC.', 1, 1);
24	
25	INSERT INTO students (user_id, enrollment_no, department_id, branch_id, academic_year_id, semester, cgpa, tenth_percentage, twelfth_percentage) VALUES
26	(3, '216020307045', 1, 1, 1, 6, 8.40, 82.00, 78.50);
27	
28	INSERT INTO companies (user_id, company_name, industry, website, description, city, is_verified) VALUES
29	(4, 'TechNova Solutions', 'Information Technology', 'https://technova.example.com', 'A fast-growing IT services company hiring diploma & engineering graduates.', 'Ahmedabad', 1);
30	
31	INSERT INTO jobs (company_id, posted_by, title, job_type, description, location, work_mode, salary_min, salary_max, min_cgpa, application_deadline, status, approved_by_tpo) VALUES
32	(1, 4, 'Junior Web Developer', 'full_time', 'Work on real client projects using PHP, JavaScript and MySQL.', 'Ahmedabad', 'hybrid', 240000, 360000, 6.5, '2026-08-30', 'open', 1),
33	(1, 4, 'Python/AI Intern', 'internship', 'Assist in building ML-powered internal tools.', 'Remote', 'remote', NULL, NULL, 7.0, '2026-08-15', 'open', 1);
34	
35	INSERT INTO skills_master (skill_name, category) VALUES
36	('PHP','Backend'), ('JavaScript','Frontend'), ('Python','Programming'), ('MySQL','Database'), ('React','Frontend');
37	
38	INSERT INTO faqs (question, answer, sort_order) VALUES
39	('Who is eligible to register on the portal?', 'All currently enrolled students of the college, along with verified recruiters and placement staff.', 1),
40	('How do I reset a forgotten password?', 'Use the Forgot Password link on the login page to receive a secure reset link by email.', 2);
