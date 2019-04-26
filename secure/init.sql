-- TODO: Put ALL SQL in between `BEGIN TRANSACTION` and `COMMIT`
BEGIN TRANSACTION;

-- TODO: create tables
--Users Table
CREATE TABLE users (
    id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
    username TEXT NOT NULL UNIQUE,
    password TEXT NOT NULL,
    first_name TEXT NOT NULL,
    last_name TEXT NOT NULL,
    grade INTEGER NOT NULL
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
    time_id INTEGER NOT NULL,
    user_id INTEGER NOT NULL
);

--Times Table
CREATE TABLE times (
    id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
    date TEXT NOT NULL,
    time_start TEXT NOT NULL,
    time_end TEXT NOT NULL,
    available BIT NOT NULL
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
    testimonial TEXT NOT NULL UNIQUE,
    rating INTEGER NOT NULL,
    date TEXT NOT NULL,
    role TEXT NOT NULL,
    user_id INTEGER NOT NULL UNIQUE
);




-- TODO: initial seed data

-- TODO: FOR HASHED PASSWORDS, LEAVE A COMMENT WITH THE PLAIN TEXT PASSWORD!

--Users Table
INSERT INTO users (id,username,password,first_name,last_name,grade) VALUES (1, 'user1', '$2y$10$BAJ3Zglrt49eztL4l1LlUeG0k75zi4J2JTtrjognFyiD8RYR1Yb0K',"Fred","Smith",2); --password: user1
INSERT INTO users (id,username,password,first_name,last_name,grade) VALUES (2, 'user2', '$2y$10$h5SXw2BWV6Lp25HRrWrjruktNQaHjhkwTWYyatRK9XSV4ZOsglsCC',"Erica","Jones",11); --password: user2

--Times Table
    --right now just info for 4/29 from 3pm-6pm
INSERT INTO times (id,date,time_start,time_end,available) VALUES (1, "4/29","15:00", "15:30",1); --3pm
INSERT INTO times (id,date,time_start,time_end,available) VALUES (2, "4/29","15:30", "16:00",0); --3:30pm, taken by fred
INSERT INTO times (id,date,time_start,time_end,available) VALUES (3, "4/29","16:00", "16:30",1); --4pm
INSERT INTO times (id,date,time_start,time_end,available) VALUES (4, "4/29","16:30", "17:00",1); --4:30pm
INSERT INTO times (id,date,time_start,time_end,available) VALUES (5, "4/29","17:00", "17:30",1); --5pm
INSERT INTO times (id,date,time_start,time_end,available) VALUES (6, "4/29","17:30", "18:00",1); --5:30pm

--Appointments Table
    --appointment for Fred (user1) on 4/29 at 3:30pm
INSERT INTO appointments (id,time_id,user_id) VALUES (1, 2, 1);

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
    --Fred signed up to work on Reading and Math
INSERT INTO appointment_subjects (id, appointment_id, subject_id) VALUES (1, 1, 1);
INSERT INTO appointment_subjects (id, appointment_id, subject_id) VALUES (2, 1, 2);

--Testimonials Table
INSERT INTO testimonials (id, testimonial, rating, date, role, user_id) VALUES (1, "Laurie is an amazing teacher, who genuinely cares about her students and their well being.", 5, "2017", "Parent", 2);


COMMIT;
