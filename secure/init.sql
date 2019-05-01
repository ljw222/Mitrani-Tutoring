-- TODO: Put ALL SQL in between `BEGIN TRANSACTION` and `COMMIT`
BEGIN TRANSACTION;

-- TODO: create tables
--Users Table
CREATE TABLE users (
    id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
    username TEXT NOT NULL UNIQUE,
    password TEXT,
    first_name TEXT NOT NULL,
    last_name TEXT,
    grade INTEGER
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
    half TEXT NOT NULL,
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
    testimonial TEXT NOT NULL,
    rating INTEGER NOT NULL,
    date TEXT NOT NULL,
    role TEXT NOT NULL,
    user_id INTEGER
);




-- TODO: initial seed data

-- TODO: FOR HASHED PASSWORDS, LEAVE A COMMENT WITH THE PLAIN TEXT PASSWORD!

--Users Table
INSERT INTO users (id,username,first_name) VALUES (0, 'anonymous',"Anonymous");
INSERT INTO users (id,username,password,first_name,last_name,grade) VALUES (1, 'user1', '$2y$10$BAJ3Zglrt49eztL4l1LlUeG0k75zi4J2JTtrjognFyiD8RYR1Yb0K',"Fred","Smith",2); --password: user1
INSERT INTO users (id,username,password,first_name,last_name,grade) VALUES (2, 'user2', '$2y$10$h5SXw2BWV6Lp25HRrWrjruktNQaHjhkwTWYyatRK9XSV4ZOsglsCC',"Erica","Jones",11); --password: user2
INSERT INTO users (id,username,password,first_name,last_name,grade) VALUES (3, 'user3', '$2y$10$XToMCm9QSDhRgSe4zBwKxu8MAQ4nwUlWWbwn1u4nF0uU6dKeBA5Aq',"Tim","Lee",0); --password: user3
INSERT INTO users (id,username,password,first_name,last_name,grade) VALUES (4, 'ariel','$2y$10$ynNq4caJnvZJUJCXqXzJdOKf/CVK4cf7sNvQ/WSR5AMlqIQCLBE7K','Ariel','C.', 5); --password: ariel
INSERT INTO users (id,username,password,first_name,last_name,grade) VALUES (5, 'tzipora','$2y$10$Q51mGxZtgDsREShV97ETBOUGZ2u2uWfnNhEoD4OIS/XqT4Vbh4bo2','Tzipora','K.', 5); --password: tzipora


--Times Table
    --right now just info for 4/29 from 3pm-6pm
INSERT INTO times (id,date,time_start,time_end,half,available) VALUES (1, "04/29/2019","15:00","15:30","PM",1); --3pm
INSERT INTO times (id,date,time_start,time_end,half,available) VALUES (2, "04/29/2019","3:30", "4:00","PM",0); --3:30pm, taken by fred
INSERT INTO times (id,date,time_start,time_end,half,available) VALUES (3, "04/29/2019","4:00", "4:30","PM",1); --4pm
-- INSERT INTO times (id,date,time_start,time_end,available) VALUES (4, "4/29","5:30", "17:00",1); --4:30pm
-- INSERT INTO times (id,date,time_start,time_end,available) VALUES (5, "4/29","17:00", "17:30",1); --5pm
-- INSERT INTO times (id,date,time_start,time_end,available) VALUES (6, "4/29","17:30", "18:00",1); --5:30pm
INSERT INTO times (id,date,time_start,time_end,half,available) VALUES (4, "04/30/2019","3:00", "3:30","PM",0); --3pm, taken by fred

--Appointments Table
    --appointment for Fred (user1) on 4/29 at 3:30pm
INSERT INTO appointments (id,time_id,user_id) VALUES (1, 2, 1);
    --appointment for Fred (user1) on 4/30 at 3pm
INSERT INTO appointments (id,time_id,user_id) VALUES (2, 4, 1);


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

--Testimonials Table
INSERT INTO testimonials (id, testimonial, rating, date, role, user_id) VALUES (1, "Laurie is an amazing teacher, who genuinely cares about her students and their well being.", 5, "2017", "Parent", 2);
INSERT INTO testimonials (id, testimonial, rating, date, role, user_id) VALUES (2, "I couldn't have gotten through my AP Chemistry course without Laurie's help. She has amazing patience and depth of understanding. My only difficulty was figuring out how to meet because I didn't have a car to commute, but other than that, she was great.", 4, "2015", "Student", 2);
INSERT INTO testimonials (id, testimonial, rating, date, role, user_id) VALUES (3, "Teacher Laurie was really helpful in teaching me how to write better. She helped me with all the assignments I needed to check my spelling and grammar and so I did well in my class. Laurie is super nice and kind too!", 5, "2012", "Student", 1);
INSERT INTO testimonials (id, testimonial, rating, date, role, user_id) VALUES (4, "Laurie is the bestest! I like math now because she makes math fun for me all the time. We play games and she reads to me so I like Laurie.", 5, "2014", "Student", 3);
INSERT INTO testimonials (id, testimonial, rating, date, role, user_id) VALUES (5, "It is fun to work with Mrs. Mitrani. She helps me with everything and she makes learning really fun. Working with Mrs. Mitrani in 5th grade has made the year go much smoother.", 5, "2019", "Student",4);
INSERT INTO testimonials (id, testimonial, rating, date, role, user_id) VALUES (6, "Laurie Mitrani is an amazing tutor.  She’s the most exciting, funny, and most interesting tutor I ever had. She is always in a good mood and never gives up. If you want a good tutor Mrs. M is the one!! ", 5, "2019", "Student", 5);

COMMIT;
