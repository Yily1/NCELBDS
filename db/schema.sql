CREATE DATABASE chicken_behavior_db;

USE chicken_behavior_db;

CREATE TABLE users (
user_id INT AUTO_INCREMENT PRIMARY KEY,
username VARCHAR(50) NOT NULL,
role VARCHAR(20) NOT NULL,
date_added DATE DEFAULT CURRENT_DATE
);

CREATE TABLE chickens (
chicken_id INT AUTO_INCREMENT PRIMARY KEY,
tag_number VARCHAR(50) NOT NULL,
age INT,
date_added DATE DEFAULT CURRENT_DATE
);

CREATE TABLE user_logs (
log_id INT AUTO_INCREMENT PRIMARY KEY,
user_id INT,
chicken_id INT,
behavior VARCHAR(100),
log_date DATE DEFAULT CURRENT_DATE,
FOREIGN KEY (user_id) REFERENCES users(user_id),
FOREIGN KEY (chicken_id) REFERENCES chickens(chicken_id)
);

INSERT INTO users(username,role) VALUES
('admin','admin'),
('user','user');

INSERT INTO chickens(tag_number,age) VALUES
('CHK001',1),
('CHK002',2); 
