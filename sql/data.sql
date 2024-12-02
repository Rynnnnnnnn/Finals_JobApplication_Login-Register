CREATE TABLE applicants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone_number VARCHAR(15),
    specialization VARCHAR(100),
    experience_years INT,
    application_date DATE DEFAULT CURRENT_DATE,
    last_added_by VARCHAR(255) DEFAULT NULL,
    last_updated_by VARCHAR(255) DEFAULT NULL
);

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    dob DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL,
    operation VARCHAR(50) NOT NULL,
    details TEXT,
    performed_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
