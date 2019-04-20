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

-- TODO: initial seed data

-- TODO: FOR HASHED PASSWORDS, LEAVE A COMMENT WITH THE PLAIN TEXT PASSWORD!
INSERT INTO users (id,username,password,first_name,last_name,grade) VALUES (1, 'user1', '$2y$10$BAJ3Zglrt49eztL4l1LlUeG0k75zi4J2JTtrjognFyiD8RYR1Yb0K',"Fred","Smith",2); --password: user1
INSERT INTO users (id,username,password,first_name,last_name,grade) VALUES (2, 'user2', '$2y$10$h5SXw2BWV6Lp25HRrWrjruktNQaHjhkwTWYyatRK9XSV4ZOsglsCC',"Erica","Jones",11); --password: user2

COMMIT;
