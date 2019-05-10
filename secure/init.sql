BEGIN TRANSACTION;

--Users Table
CREATE TABLE users (
    id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
    username TEXT NOT NULL UNIQUE,
    password TEXT NOT NULL,
    first_name TEXT NOT NULL,
    last_name TEXT,
    grade INTEGER,
    home TEXT,
    school TEXT,
    email TEXT,
    phone TEXT
);

-- Sessions Table
CREATE TABLE sessions (
	id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
	user_id INTEGER NOT NULL,
	session TEXT NOT NULL UNIQUE
);

--Appointments Table
CREATE TABLE appointments (
    id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
    date TEXT NOT NULL,
    time_start TEXT NOT NULL,
    time_end TEXT NOT NULL,
    location TEXT NOT NULL,
    comment TEXT,
    user_id INTEGER NOT NULL
);

--Subjects Table
CREATE TABLE subjects (
    id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
    subject TEXT NOT NULL UNIQUE
);

--Appointment_subjects Table
CREATE TABLE appointment_subjects (
    id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
    appointment_id INTEGER NOT NULL,
    subject_id INTEGER NOT NULL
);

--Testimonials Table
CREATE TABLE testimonials (
    id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
    testimonial TEXT NOT NULL,
    rating INTEGER NOT NULL,
    date TEXT NOT NULL,
    role TEXT NOT NULL,
    user_id INTEGER
);

-- Initial seed data

--Users Table
INSERT INTO users (id,username,password,first_name,last_name,grade,home,school) VALUES (1, 'admin', '$2y$10$53n/ZeKPSmr430z3z7HU1OnrJ/ENcp/v2ZJRq6ioPd2vr.1aa1QrK', "Laurie", "Mitrani", NULL, "", ""); -- password: admin
INSERT INTO users (id,username,password,first_name,last_name,grade,home,school,email,phone) VALUES (2, 'user1', '$2y$10$BAJ3Zglrt49eztL4l1LlUeG0k75zi4J2JTtrjognFyiD8RYR1Yb0K',"Fred","Smith",2,'11 Pine Rd','Greenwood Elementary School','fred@gmail.com','123-456-7890'); --password: user1
INSERT INTO users (id,username,password,first_name,last_name,grade,home,school,email,phone) VALUES (3, 'user2', '$2y$10$h5SXw2BWV6Lp25HRrWrjruktNQaHjhkwTWYyatRK9XSV4ZOsglsCC',"Erica","Jones",11,'24 Main Rd','Miami High School','erica@gmail.com','222-333-4444'); --password: user2
INSERT INTO users (id,username,password,first_name,last_name,grade,home,school,email,phone) VALUES (4, 'user3', '$2y$10$XToMCm9QSDhRgSe4zBwKxu8MAQ4nwUlWWbwn1u4nF0uU6dKeBA5Aq',"Tim","Lee",0,'123 Beckett Way', 'Bridges Elementary School','tim@gmail.com','133-432-4019'); --password: user3
INSERT INTO users (id,username,password,first_name,last_name,grade,home,school,email,phone) VALUES (5, 'ariel','$2y$10$ynNq4caJnvZJUJCXqXzJdOKf/CVK4cf7sNvQ/WSR5AMlqIQCLBE7K','Ariel','C.', 5,'5 Pine Rd','Greenwood Elementary School','ariel@gmail.com','232-456-7890'); --password: ariel
INSERT INTO users (id,username,password,first_name,grade,home,school,email,phone) VALUES (6, 'tzipora','$2y$10$Q51mGxZtgDsREShV97ETBOUGZ2u2uWfnNhEoD4OIS/XqT4Vbh4bo2','D.L.S', 5,'2 Wedgewood Dr','Bridges Elementary School','tzipora@gmail.com','456-222-9204'); --password: tzipora
INSERT INTO users (id,username,password,first_name,grade,home,school,email,phone) VALUES (7, 'dls','$2y$10$ciShg8By0OO2rrk96CzYxuW6A8H6x9QwMluEHybjv0baxpyZnp2fW','D.L.S', 4,'10 Main Rd', 'Greenwood Elementary School','dls@gmail.com','413-456-3491'); --password: dls
INSERT INTO users (id,username,password,first_name,grade,home,school,email,phone) VALUES (8, 'bz','$2y$10$3EI88eJujiyIrG2D.jSF..7N09wv.QDpwCiJMi2Nvh2232BaEqjaK','B.Z.', 2,'43 Beckett Way','Bridges Elementary School','bz@gmail.com','301-444-1039'); --password: bz
INSERT INTO users (id,username,password,first_name,grade,home,school,email,phone) VALUES (9, 'tk','$2y$10$qSGR.8LzimZ8PUdEvEpp7.xvTqpiFkCTaT1JzlV9xph8QdvgarOiu','T.K.', 1,'184 Stone Rd','Greenwood Elementary School','tk@gmail.com','344-120-3910'); --password: tk

--Appointments Table
    --appointment for Fred (user1) on 5/29 at 3:30pm
INSERT INTO appointments (id,date,time_start,time_end,location,comment,user_id) VALUES (1, "05/29/2019","15:30", "16:30","School","First appointment!", 2);
    --appointment for Fred (user1) on 5/15 at 3pm
INSERT INTO appointments (id,date,time_start,time_end,location,comment,user_id) VALUES (2, "05/15/2019","15:00", "16:00","Home","Second appointment.", 2);
INSERT INTO appointments (id,date,time_start,time_end,location,comment,user_id) VALUES (3, "05/21/2019","10:00", "11:00","Office","Excited!", 3);
INSERT INTO appointments (id,date,time_start,time_end,location,comment,user_id) VALUES (4, "05/19/2019","13:00", "14:00","Home","I need to work on math.", 5);
INSERT INTO appointments (id,date,time_start,time_end,location,comment,user_id) VALUES (5, "05/24/2019","09:00", "10:00","School","Please help me with my book project.", 4);
--Subjects Table
INSERT INTO subjects (id, subject) VALUES (1, "Reading");
INSERT INTO subjects (id, subject) VALUES (2, "Math");
INSERT INTO subjects (id, subject) VALUES (3, "Writing");
INSERT INTO subjects (id, subject) VALUES (4, "Organization");
INSERT INTO subjects (id, subject) VALUES (5, "Study Skills");
INSERT INTO subjects (id, subject) VALUES (6, "Test Taking Skills");
INSERT INTO subjects (id, subject) VALUES (7, "Homework");
INSERT INTO subjects (id, subject) VALUES (8, "Project Assistance");

--Appointment_subjects Table
    --Fred signed up to work on Reading and Math on 4/29
INSERT INTO appointment_subjects (id, appointment_id, subject_id) VALUES (1, 1, 1);
INSERT INTO appointment_subjects (id, appointment_id, subject_id) VALUES (2, 1, 2);
    --Fred signed up to work on study skills on 4/30
INSERT INTO appointment_subjects (id, appointment_id, subject_id) VALUES (3, 2, 5);
INSERT INTO appointment_subjects (id, appointment_id, subject_id) VALUES (4, 3, 1);
INSERT INTO appointment_subjects (id, appointment_id, subject_id) VALUES (5, 4, 2);
INSERT INTO appointment_subjects (id, appointment_id, subject_id) VALUES (6, 5, 8);

--Testimonials Table
INSERT INTO testimonials (id, testimonial, rating, date, role, user_id) VALUES (1, "It is fun to work with Mrs. Mitrani. She helps me with everything and she makes learning really fun. Working with Mrs. Mitrani in 5th grade has made the year go much smoother.", 5, "2019", "Student",5);
INSERT INTO testimonials (id, testimonial, rating, date, role, user_id) VALUES (2, "Laurie Mitrani is an amazing tutor.  She’s the most exciting, funny, and most interesting tutor I ever had. She is always in a good mood and never gives up. If you want a good tutor Mrs. M is the one!! ", 5, "2019", "Student", 6);
INSERT INTO testimonials (id, testimonial, rating, date, role, user_id) VALUES (3, "I like working with Mrs. Mitrani. Last year on the FSA I scored high because she worked with me. Today I took the FSA for 4th grade and she also helped me.", 5, "2019", "Student", 7);
INSERT INTO testimonials (id, testimonial, rating, date, role, user_id) VALUES (4, "My child has been receiving tutoring services with Laurie Mitrani for the past 2 years. Laurie has given her confidence in her reading, made reading fun, and has encouraged a love for reading. Working with Laurie on a one on one basis has given my child the opportunity to have her skills built up in the areas which needed extra support.
She has made excellent progress, I am so grateful to have such a wonderful tutor.", 5, "2019", "Parent", 8);
INSERT INTO testimonials (id, testimonial, rating, date, role, user_id) VALUES (5, "Laurie Mitrani has worked with my daughter for two years and my son for a year. She is truly one of a kind!  She has endless patience and the love of her students and her work. My children look forward to working with her and are eager to please her and put their best foot forward. My daughter has surpassed any goals we have set for her because Laurie told her she could and continued to encourage her. When my either of them is having an off day Laurie is so good at changing up their routine and keeping them interested. Laurie always keeps my Husband and I informed of our children’s progress and is so good at explaining everything they are working on.
I would highly recommend Laurie Mitrani, we are so blessed to have her in our lives.", 5, "2019", "Parent", 9);

COMMIT;
