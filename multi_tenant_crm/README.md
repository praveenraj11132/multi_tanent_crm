# Multi-Tenant CRM System

## Project Overview
This project is a **Multi-Tenant CRM System** developed as part of the **Round 2 – Advanced Technical Assessment**.

The system allows multiple companies to operate independently with **secure authentication**, **role-based access control**, and **strict data isolation** between tenants.

This assignment demonstrates real-world backend architecture, database design, authentication, authorization, and scalability.

---

## Technology Used
- Backend: PHP (REST APIs using PDO)
- Database: MySQL
- Authentication: JWT (JSON Web Tokens)
- Frontend: HTML, CSS, JavaScript
- Server: Apache (XAMPP)

## Setup Instructions

### Prerequisites
- XAMPP (Apache + MySQL)
- PHP 8+
- Web browser (Chrome recommended)

---

### Installation Steps

1. Place the project inside:
- C:\xampp\htdocs\multi_tenant_crm
2. Start **Apache** and **MySQL** using XAMPP Control Panel.

### Database Setup
1. Open `http://localhost/phpmyadmin`
2. 3. Import the file named as **schema.sql**.

## Database Schema
Tables used:
- companies
- users
- leads
- activity_logs
- rate_limits

## Frontend Access URLs

After starting Apache and MySQL using XAMPP, access the frontend using the following URLs:

### Login Page
http://localhost/multi_tenant_crm/frontend/login.html

### Leads Management
http://localhost/multi_tenant_crm/frontend/leads.html

### Dashboard
http://localhost/multi_tenant_crm/frontend/dashboard.html

## Note: Users must log in first.  
## After successful login, a JWT token is stored in `localStorage` and used to access protected APIs.