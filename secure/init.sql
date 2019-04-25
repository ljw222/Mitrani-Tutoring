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
    time_id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
    user_id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT
);

--Times Table
CREATE TABLE times (
    id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
    date TEXT NOT NULL,
    time_start TEXT NOT NULL,
    time_end TEXT NOT NULL,
    available BOOLEAN NOT NULL
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
INSERT INTO users (id,username,password,first_name,last_name,grade) VALUES (1, 'user1', '$2y$10$BAJ3Zglrt49eztL4l1LlUeG0k75zi4J2JTtrjognFyiD8RYR1Yb0K',"Fred","Smith",2); --password: user1
INSERT INTO users (id,username,password,first_name,last_name,grade) VALUES (2, 'user2', '$2y$10$h5SXw2BWV6Lp25HRrWrjruktNQaHjhkwTWYyatRK9XSV4ZOsglsCC',"Erica","Jones",11); --password: user2

COMMIT;
