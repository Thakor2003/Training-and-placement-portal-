1	-- ============================================================
2	-- TRAINING & PLACEMENT PORTAL — DATABASE SCHEMA
3	-- Engine: MySQL 8+  |  Charset: utf8mb4
4	-- ============================================================
5	
6	CREATE DATABASE IF NOT EXISTS tpp_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
7	USE tpp_db;
8	
9	SET FOREIGN_KEY_CHECKS = 0;
10	
11	-- ============================================================
12	-- 1. CORE / IDENTITY
13	-- ============================================================
14	
15	CREATE TABLE roles (
16	    role_id       INT AUTO_INCREMENT PRIMARY KEY,
17	    role_name     VARCHAR(50) NOT NULL UNIQUE   -- super_admin, tpo, student, company
18	);
19	
20	INSERT INTO roles (role_name) VALUES ('super_admin'), ('tpo'), ('student'), ('company');
21	
22	CREATE TABLE users (
23	    user_id          INT AUTO_INCREMENT PRIMARY KEY,
24	    role_id          INT NOT NULL,
25	    full_name        VARCHAR(150) NOT NULL,
26	    email            VARCHAR(150) NOT NULL UNIQUE,
27	    password_hash    VARCHAR(255) NOT NULL,
28	    phone            VARCHAR(20),
29	    profile_photo    VARCHAR(255) DEFAULT NULL,
30	    is_email_verified TINYINT(1) DEFAULT 0,
31	    is_active        TINYINT(1) DEFAULT 1,
32	    theme_pref       ENUM('light','dark') DEFAULT 'light',
33	    created_at       DATETIME DEFAULT CURRENT_TIMESTAMP,
34	    updated_at       DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
35	    FOREIGN KEY (role_id) REFERENCES roles(role_id) ON DELETE RESTRICT
36	) ENGINE=InnoDB;
37	
38	CREATE TABLE email_verifications (
39	    id          INT AUTO_INCREMENT PRIMARY KEY,
40	    user_id     INT NOT NULL,
41	    token       VARCHAR(255) NOT NULL,
42	    expires_at  DATETIME NOT NULL,
43	    created_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
44	    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
45	) ENGINE=InnoDB;
46	
47	CREATE TABLE password_resets (
48	    id          INT AUTO_INCREMENT PRIMARY KEY,
49	    user_id     INT NOT NULL,
50	    token       VARCHAR(255) NOT NULL,
51	    expires_at  DATETIME NOT NULL,
52	    used        TINYINT(1) DEFAULT 0,
53	    created_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
54	    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
55	) ENGINE=InnoDB;
56	
57	CREATE TABLE user_sessions (
58	    session_id   VARCHAR(128) PRIMARY KEY,
59	    user_id      INT NOT NULL,
60	    ip_address   VARCHAR(45),
61	    user_agent   VARCHAR(255),
62	    last_active  DATETIME DEFAULT CURRENT_TIMESTAMP,
63	    created_at   DATETIME DEFAULT CURRENT_TIMESTAMP,
64	    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
65	) ENGINE=InnoDB;
66	
67	-- ============================================================
68	-- 2. COLLEGE STRUCTURE (Admin Module)
69	-- ============================================================
70	
71	CREATE TABLE departments (
72	    department_id   INT AUTO_INCREMENT PRIMARY KEY,
73	    name            VARCHAR(100) NOT NULL,
74	    code            VARCHAR(20) NOT NULL UNIQUE
75	) ENGINE=InnoDB;
76	
77	CREATE TABLE branches (
78	    branch_id       INT AUTO_INCREMENT PRIMARY KEY,
79	    department_id   INT NOT NULL,
80	    name            VARCHAR(100) NOT NULL,
81	    code            VARCHAR(20) NOT NULL UNIQUE,
82	    FOREIGN KEY (department_id) REFERENCES departments(department_id) ON DELETE CASCADE
83	) ENGINE=InnoDB;
84	
85	CREATE TABLE academic_years (
86	    academic_year_id INT AUTO_INCREMENT PRIMARY KEY,
87	    year_label        VARCHAR(20) NOT NULL UNIQUE,  -- e.g. 2025-2026
88	    is_current         TINYINT(1) DEFAULT 0
89	) ENGINE=InnoDB;
90	
91	CREATE TABLE college_settings (
92	    setting_key    VARCHAR(100) PRIMARY KEY,
93	    setting_value  TEXT
94	) ENGINE=InnoDB;
95	
96	CREATE TABLE system_logs (
97	    log_id        BIGINT AUTO_INCREMENT PRIMARY KEY,
98	    user_id       INT NULL,
99	    action        VARCHAR(255) NOT NULL,
100	    details       TEXT,
101	    ip_address    VARCHAR(45),
102	    created_at    DATETIME DEFAULT CURRENT_TIMESTAMP,
103	    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL
104	) ENGINE=InnoDB;
105	
106	-- ============================================================
107	-- 3. STUDENT MODULE
108	-- ============================================================
109	
110	CREATE TABLE students (
111	    student_id        INT AUTO_INCREMENT PRIMARY KEY,
112	    user_id            INT NOT NULL UNIQUE,
113	    enrollment_no      VARCHAR(50) UNIQUE,
114	    department_id      INT,
115	    branch_id          INT,
116	    academic_year_id   INT,
117	    semester           TINYINT,
118	    cgpa               DECIMAL(4,2),
119	    backlog_count      INT DEFAULT 0,
120	    tenth_percentage    DECIMAL(5,2),
121	    twelfth_percentage  DECIMAL(5,2),
122	    diploma_percentage  DECIMAL(5,2),
123	    date_of_birth      DATE,
124	    gender             ENUM('male','female','other'),
125	    address            TEXT,
126	    city               VARCHAR(100),
127	    state              VARCHAR(100),
128	    linkedin_url       VARCHAR(255),
129	    github_url         VARCHAR(255),
130	    portfolio_url      VARCHAR(255),
131	    profile_completion INT DEFAULT 0,
132	    is_placed          TINYINT(1) DEFAULT 0,
133	    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
134	    FOREIGN KEY (department_id) REFERENCES departments(department_id) ON DELETE SET NULL,
135	    FOREIGN KEY (branch_id) REFERENCES branches(branch_id) ON DELETE SET NULL,
136	    FOREIGN KEY (academic_year_id) REFERENCES academic_years(academic_year_id) ON DELETE SET NULL
137	) ENGINE=InnoDB;
138	
139	CREATE TABLE skills_master (
140	    skill_id     INT AUTO_INCREMENT PRIMARY KEY,
141	    skill_name   VARCHAR(100) NOT NULL UNIQUE,
142	    category     VARCHAR(50)
143	) ENGINE=InnoDB;
144	
145	CREATE TABLE student_skills (
146	    id            INT AUTO_INCREMENT PRIMARY KEY,
147	    student_id    INT NOT NULL,
148	    skill_id      INT NOT NULL,
149	    proficiency   ENUM('beginner','intermediate','advanced','expert') DEFAULT 'beginner',
150	    FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE,
151	    FOREIGN KEY (skill_id) REFERENCES skills_master(skill_id) ON DELETE CASCADE,
152	    UNIQUE KEY uq_student_skill (student_id, skill_id)
153	) ENGINE=InnoDB;
154	
155	CREATE TABLE certifications (
156	    certification_id INT AUTO_INCREMENT PRIMARY KEY,
157	    student_id        INT NOT NULL,
158	    title             VARCHAR(200) NOT NULL,
159	    issuer            VARCHAR(150),
160	    issue_date        DATE,
161	    credential_url    VARCHAR(255),
162	    file_path         VARCHAR(255),
163	    FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE
164	) ENGINE=InnoDB;
165	
166	CREATE TABLE resumes (
167	    resume_id      INT AUTO_INCREMENT PRIMARY KEY,
168	    student_id     INT NOT NULL,
169	    resume_title   VARCHAR(150) DEFAULT 'My Resume',
170	    builder_json   LONGTEXT,          -- structured data used by the resume builder
171	    file_path      VARCHAR(255),      -- generated / uploaded PDF
172	    is_primary     TINYINT(1) DEFAULT 1,
173	    ats_score      DECIMAL(5,2),
174	    ai_feedback    TEXT,
175	    created_at     DATETIME DEFAULT CURRENT_TIMESTAMP,
176	    updated_at     DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
177	    FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE
178	) ENGINE=InnoDB;
179	
180	-- ============================================================
181	-- 4. COMPANY MODULE
182	-- ============================================================
183	
184	CREATE TABLE companies (
185	    company_id     INT AUTO_INCREMENT PRIMARY KEY,
186	    user_id        INT NOT NULL UNIQUE,   -- primary HR account
187	    company_name   VARCHAR(200) NOT NULL,
188	    industry       VARCHAR(100),
189	    website        VARCHAR(255),
190	    logo_path      VARCHAR(255),
191	    description    TEXT,
192	    address        TEXT,
193	    city           VARCHAR(100),
194	    company_size   VARCHAR(50),
195	    is_verified    TINYINT(1) DEFAULT 0,
196	    created_at     DATETIME DEFAULT CURRENT_TIMESTAMP,
197	    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
198	) ENGINE=InnoDB;
199	
200	CREATE TABLE company_hr (
201	    hr_id         INT AUTO_INCREMENT PRIMARY KEY,
202	    company_id    INT NOT NULL,
203	    user_id       INT NOT NULL UNIQUE,
204	    designation   VARCHAR(100),
205	    is_primary    TINYINT(1) DEFAULT 0,
206	    FOREIGN KEY (company_id) REFERENCES companies(company_id) ON DELETE CASCADE,
207	    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
208	) ENGINE=InnoDB;
209	
210	-- ============================================================
211	-- 5. JOB / INTERNSHIP MODULE
212	-- ============================================================
213	
214	CREATE TABLE jobs (
215	    job_id             INT AUTO_INCREMENT PRIMARY KEY,
216	    company_id         INT NOT NULL,
217	    posted_by          INT NOT NULL,           -- user_id (HR / TPO)
218	    title              VARCHAR(200) NOT NULL,
219	    job_type           ENUM('full_time','internship','part_time') DEFAULT 'full_time',
220	    description        TEXT,
221	    responsibilities   TEXT,
222	    location           VARCHAR(150),
223	    work_mode          ENUM('onsite','remote','hybrid') DEFAULT 'onsite',
224	    salary_min         DECIMAL(10,2),
225	    salary_max         DECIMAL(10,2),
226	    min_cgpa           DECIMAL(4,2) DEFAULT 0,
227	    max_backlogs       INT DEFAULT 0,
228	    eligible_branches  VARCHAR(255),           -- CSV of branch_ids (or normalize below)
229	    application_deadline DATE,
230	    status             ENUM('draft','open','closed','cancelled') DEFAULT 'open',
231	    approved_by_tpo    TINYINT(1) DEFAULT 0,
232	    created_at         DATETIME DEFAULT CURRENT_TIMESTAMP,
233	    FOREIGN KEY (company_id) REFERENCES companies(company_id) ON DELETE CASCADE,
234	    FOREIGN KEY (posted_by) REFERENCES users(user_id) ON DELETE CASCADE
235	) ENGINE=InnoDB;
236	
237	CREATE TABLE job_eligible_branches (
238	    id        INT AUTO_INCREMENT PRIMARY KEY,
239	    job_id    INT NOT NULL,
240	    branch_id INT NOT NULL,
241	    FOREIGN KEY (job_id) REFERENCES jobs(job_id) ON DELETE CASCADE,
242	    FOREIGN KEY (branch_id) REFERENCES branches(branch_id) ON DELETE CASCADE
243	) ENGINE=InnoDB;
244	
245	CREATE TABLE applications (
246	    application_id   INT AUTO_INCREMENT PRIMARY KEY,
247	    job_id           INT NOT NULL,
248	    student_id       INT NOT NULL,
249	    resume_id        INT,
250	    status           ENUM('applied','shortlisted','interview_scheduled','selected','rejected','withdrawn') DEFAULT 'applied',
251	    applied_at       DATETIME DEFAULT CURRENT_TIMESTAMP,
252	    updated_at       DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
253	    FOREIGN KEY (job_id) REFERENCES jobs(job_id) ON DELETE CASCADE,
254	    FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE,
255	    FOREIGN KEY (resume_id) REFERENCES resumes(resume_id) ON DELETE SET NULL,
256	    UNIQUE KEY uq_job_student (job_id, student_id)
257	) ENGINE=InnoDB;
258	
259	CREATE TABLE saved_jobs (
260	    id          INT AUTO_INCREMENT PRIMARY KEY,
261	    student_id  INT NOT NULL,
262	    job_id      INT NOT NULL,
263	    saved_at    DATETIME DEFAULT CURRENT_TIMESTAMP,
264	    FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE,
265	    FOREIGN KEY (job_id) REFERENCES jobs(job_id) ON DELETE CASCADE,
266	    UNIQUE KEY uq_saved (student_id, job_id)
267	) ENGINE=InnoDB;
268	
269	CREATE TABLE interviews (
270	    interview_id    INT AUTO_INCREMENT PRIMARY KEY,
271	    application_id  INT NOT NULL,
272	    round_name      VARCHAR(100),
273	    scheduled_at    DATETIME,
274	    mode            ENUM('online','offline') DEFAULT 'offline',
275	    location_or_link VARCHAR(255),
276	    result          ENUM('pending','pass','fail') DEFAULT 'pending',
277	    remarks         TEXT,
278	    FOREIGN KEY (application_id) REFERENCES applications(application_id) ON DELETE CASCADE
279	) ENGINE=InnoDB;
280	
281	CREATE TABLE offer_letters (
282	    offer_id        INT AUTO_INCREMENT PRIMARY KEY,
283	    application_id  INT NOT NULL UNIQUE,
284	    file_path       VARCHAR(255) NOT NULL,
285	    ctc             DECIMAL(10,2),
286	    joining_date    DATE,
287	    uploaded_at     DATETIME DEFAULT CURRENT_TIMESTAMP,
288	    FOREIGN KEY (application_id) REFERENCES applications(application_id) ON DELETE CASCADE
289	) ENGINE=InnoDB;
290	
291	CREATE TABLE placement_history (
292	    placement_id    INT AUTO_INCREMENT PRIMARY KEY,
293	    student_id      INT NOT NULL,
294	    company_id      INT NOT NULL,
295	    job_id          INT,
296	    package_ctc     DECIMAL(10,2),
297	    placement_date  DATE,
298	    academic_year_id INT,
299	    FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE,
300	    FOREIGN KEY (company_id) REFERENCES companies(company_id) ON DELETE CASCADE,
301	    FOREIGN KEY (job_id) REFERENCES jobs(job_id) ON DELETE SET NULL,
302	    FOREIGN KEY (academic_year_id) REFERENCES academic_years(academic_year_id) ON DELETE SET NULL
303	) ENGINE=InnoDB;
304	
305	-- ============================================================
306	-- 6. PLACEMENT DRIVES / CAMPUS RECRUITMENT (TPO Module)
307	-- ============================================================
308	
309	CREATE TABLE placement_drives (
310	    drive_id        INT AUTO_INCREMENT PRIMARY KEY,
311	    company_id      INT NOT NULL,
312	    title           VARCHAR(200),
313	    drive_date      DATE,
314	    venue           VARCHAR(200),
315	    eligibility_criteria TEXT,
316	    status          ENUM('upcoming','ongoing','completed','cancelled') DEFAULT 'upcoming',
317	    created_by      INT NOT NULL,   -- tpo user_id
318	    created_at      DATETIME DEFAULT CURRENT_TIMESTAMP,
319	    FOREIGN KEY (company_id) REFERENCES companies(company_id) ON DELETE CASCADE,
320	    FOREIGN KEY (created_by) REFERENCES users(user_id) ON DELETE CASCADE
321	) ENGINE=InnoDB;
322	
323	CREATE TABLE drive_participants (
324	    id          INT AUTO_INCREMENT PRIMARY KEY,
325	    drive_id    INT NOT NULL,
326	    student_id  INT NOT NULL,
327	    status      ENUM('registered','attended','absent') DEFAULT 'registered',
328	    FOREIGN KEY (drive_id) REFERENCES placement_drives(drive_id) ON DELETE CASCADE,
329	    FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE,
330	    UNIQUE KEY uq_drive_student (drive_id, student_id)
331	) ENGINE=InnoDB;
332	
333	CREATE TABLE notices (
334	    notice_id    INT AUTO_INCREMENT PRIMARY KEY,
335	    posted_by    INT NOT NULL,
336	    title        VARCHAR(200) NOT NULL,
337	    content      TEXT,
338	    target_role  ENUM('all','student','tpo','company') DEFAULT 'all',
339	    is_pinned    TINYINT(1) DEFAULT 0,
340	    created_at   DATETIME DEFAULT CURRENT_TIMESTAMP,
341	    FOREIGN KEY (posted_by) REFERENCES users(user_id) ON DELETE CASCADE
342	) ENGINE=InnoDB;
343	
344	-- ============================================================
345	-- 7. AI FEATURES (logs / caching AI outputs)
346	-- ============================================================
347	
348	CREATE TABLE ai_interactions (
349	    id            BIGINT AUTO_INCREMENT PRIMARY KEY,
350	    user_id       INT NOT NULL,
351	    feature       ENUM('resume_analyzer','ats_score','career_recommendation','skill_gap','interview_prep','chatbot','job_recommendation') NOT NULL,
352	    input_summary TEXT,
353	    output_data   LONGTEXT,
354	    created_at    DATETIME DEFAULT CURRENT_TIMESTAMP,
355	    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
356	) ENGINE=InnoDB;
357	
358	CREATE TABLE mock_interviews (
359	    mock_id       INT AUTO_INCREMENT PRIMARY KEY,
360	    student_id    INT NOT NULL,
361	    topic         VARCHAR(150),
362	    questions_json LONGTEXT,
363	    answers_json   LONGTEXT,
364	    ai_score       DECIMAL(5,2),
365	    ai_feedback    TEXT,
366	    created_at     DATETIME DEFAULT CURRENT_TIMESTAMP,
367	    FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE
368	) ENGINE=InnoDB;
369	
370	-- ============================================================
371	-- 8. NOTIFICATIONS / CALENDAR
372	-- ============================================================
373	
374	CREATE TABLE notifications (
375	    notification_id INT AUTO_INCREMENT PRIMARY KEY,
376	    user_id          INT NOT NULL,
377	    title            VARCHAR(200) NOT NULL,
378	    message          TEXT,
379	    type             VARCHAR(50) DEFAULT 'general',
380	    is_read          TINYINT(1) DEFAULT 0,
381	    created_at       DATETIME DEFAULT CURRENT_TIMESTAMP,
382	    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
383	) ENGINE=InnoDB;
384	
385	CREATE TABLE calendar_events (
386	    event_id     INT AUTO_INCREMENT PRIMARY KEY,
387	    user_id      INT NOT NULL,
388	    title        VARCHAR(200) NOT NULL,
389	    description  TEXT,
390	    event_date   DATETIME NOT NULL,
391	    event_type   VARCHAR(50),
392	    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
393	) ENGINE=InnoDB;
394	
395	-- ============================================================
396	-- 9. CMS / EXTRA MODULES
397	-- ============================================================
398	
399	CREATE TABLE success_stories (
400	    story_id     INT AUTO_INCREMENT PRIMARY KEY,
401	    student_id   INT,
402	    title        VARCHAR(200),
403	    content      TEXT,
404	    photo_path   VARCHAR(255),
405	    is_published TINYINT(1) DEFAULT 1,
406	    created_at   DATETIME DEFAULT CURRENT_TIMESTAMP,
407	    FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE SET NULL
408	) ENGINE=InnoDB;
409	
410	CREATE TABLE gallery (
411	    gallery_id   INT AUTO_INCREMENT PRIMARY KEY,
412	    title        VARCHAR(200),
413	    image_path   VARCHAR(255) NOT NULL,
414	    category     VARCHAR(100),
415	    uploaded_at  DATETIME DEFAULT CURRENT_TIMESTAMP
416	) ENGINE=InnoDB;
417	
418	CREATE TABLE events (
419	    event_id      INT AUTO_INCREMENT PRIMARY KEY,
420	    title         VARCHAR(200) NOT NULL,
421	    description   TEXT,
422	    event_type    ENUM('event','webinar','workshop') DEFAULT 'event',
423	    event_date    DATETIME,
424	    venue_or_link VARCHAR(255),
425	    created_by    INT,
426	    FOREIGN KEY (created_by) REFERENCES users(user_id) ON DELETE SET NULL
427	) ENGINE=InnoDB;
428	
429	CREATE TABLE event_registrations (
430	    id         INT AUTO_INCREMENT PRIMARY KEY,
431	    event_id   INT NOT NULL,
432	    user_id    INT NOT NULL,
433	    registered_at DATETIME DEFAULT CURRENT_TIMESTAMP,
434	    FOREIGN KEY (event_id) REFERENCES events(event_id) ON DELETE CASCADE,
435	    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
436	    UNIQUE KEY uq_event_user (event_id, user_id)
437	) ENGINE=InnoDB;
438	
439	CREATE TABLE alumni (
440	    alumni_id      INT AUTO_INCREMENT PRIMARY KEY,
441	    student_id     INT,
442	    passout_year   YEAR,
443	    current_company VARCHAR(200),
444	    designation    VARCHAR(150),
445	    testimonial    TEXT,
446	    linkedin_url   VARCHAR(255),
447	    FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE SET NULL
448	) ENGINE=InnoDB;
449	
450	CREATE TABLE faqs (
451	    faq_id     INT AUTO_INCREMENT PRIMARY KEY,
452	    question   VARCHAR(255) NOT NULL,
453	    answer     TEXT NOT NULL,
454	    sort_order INT DEFAULT 0
455	) ENGINE=InnoDB;
456	
457	CREATE TABLE contact_messages (
458	    id          INT AUTO_INCREMENT PRIMARY KEY,
459	    name        VARCHAR(150),
460	    email       VARCHAR(150),
461	    subject     VARCHAR(200),
462	    message     TEXT,
463	    is_resolved TINYINT(1) DEFAULT 0,
464	    created_at  DATETIME DEFAULT CURRENT_TIMESTAMP
465	) ENGINE=InnoDB;
466	
467	CREATE TABLE feedback (
468	    feedback_id  INT AUTO_INCREMENT PRIMARY KEY,
469	    user_id      INT,
470	    rating       TINYINT,
471	    comments     TEXT,
472	    created_at   DATETIME DEFAULT CURRENT_TIMESTAMP,
473	    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL
474	) ENGINE=InnoDB;
475	
476	SET FOREIGN_KEY_CHECKS = 1;
477	
478	-- ============================================================
479	-- INDEXES for performance
480	-- ============================================================
481	CREATE INDEX idx_jobs_status ON jobs(status);
482	CREATE INDEX idx_applications_status ON applications(status);
483	CREATE INDEX idx_students_branch ON students(branch_id);
484	CREATE INDEX idx_notifications_user_read ON notifications(user_id, is_read);
