-- Database creation
CREATE DATABASE multi_tenant_crm;
USE multi_tenant_crm;

-- ----------------
-- Companies Table
-- ----------------
-- Stores tenant (company) information
CREATE TABLE companies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- -------------
-- Users Table
-- -------------
-- Stores users belonging to a company with roles
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('super_admin', 'admin', 'staff') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_users_company
        FOREIGN KEY (company_id) REFERENCES companies(id)
        ON DELETE CASCADE
);

-- -------------
-- Leads Table
-- -------------
-- Stores CRM leads per company
CREATE TABLE leads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    assigned_to INT DEFAULT NULL,
    full_name VARCHAR(150) NOT NULL,
    phone VARCHAR(20),
    email VARCHAR(150),
    source VARCHAR(100),
    status ENUM('New', 'Contacted', 'Follow-up', 'Converted', 'Rejected') DEFAULT 'New',
    priority ENUM('Low', 'Medium', 'High') DEFAULT 'Medium',
    notes TEXT,
    is_deleted TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_leads_company
        FOREIGN KEY (company_id) REFERENCES companies(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_leads_user
        FOREIGN KEY (assigned_to) REFERENCES users(id)
        ON DELETE SET NULL
);

-- -------------------
-- Activity Logs Table
-- -------------------
-- Logs system activities like login, lead updates, assignments
CREATE TABLE activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    user_id INT NOT NULL,
    action VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_logs_company
        FOREIGN KEY (company_id) REFERENCES companies(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_logs_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE
);

CREATE TABLE rate_limits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    identifier VARCHAR(100),
    endpoint VARCHAR(100),
    attempts INT DEFAULT 1,
    last_attempt DATETIME,
    INDEX(identifier, endpoint)
);

