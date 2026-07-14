# 🚀 Laravel Blog REST API

A secure and scalable Blog REST API built with **Laravel**. This project provides authentication, authorization, post management, tagging, filtering, pagination, and role-based access control using **Laravel Sanctum**.

## 📖 Project Overview

This API allows users to register, authenticate, create blog posts, manage tags, and perform CRUD operations based on their authorization level.

The project follows RESTful API standards and returns consistent JSON responses using a custom API response class.

---

## ✨ Features

### 🔐 Authentication

- User Registration
- User Login
- User Logout
- Laravel Sanctum Token Authentication
- Protected API Routes

### 👥 Authorization & Roles

- Role-based user system
- Guest users can view all published posts
- Authenticated users can:
    - Create posts
    - Edit their own posts
    - Update their own posts
    - Delete their own posts
- Unauthorized users cannot modify other users' posts
- Admin can change post status

---

### 📝 Post Management

- Create Post
- Read Posts
- Update Post
- Delete Post
- Post Status Management (Admin)
- Post Ownership Verification

---

### 🏷 Tag Management

- Many-to-Many relationship between Posts and Tags
- Pivot table implementation
- Attach multiple tags to posts

---

### 🔎 Filtering & Query Parameters

Supports multiple query parameters such as:

- Filter posts by authenticated user
- Filter posts by user ID
- Filter posts by tag
- Pagination
- Custom query parameters

Example:

```
GET /api/posts?tag=laravel
```

```
GET /api/posts?user_id=1
```

```
GET /api/posts?auth_user=true
```

```
GET /api/posts?page=2
```

---

### 📄 Pagination

Laravel Pagination has been implemented for efficient data retrieval.

---

### 📦 Standard API Response

All API responses are returned using a centralized **ApiResponse** class to maintain a consistent response structure.

Example:

```json
{
    "success": true,
    "message": "Post fetched successfully.",
    "data": {
        ...
    }
}
```

---

## 🗂 Database Relationships

### User ↔ Post

- One User has Many Posts
- One Post belongs to One User

### Post ↔ Tag

- Many-to-Many Relationship
- Pivot Table Used

Relationship summary:

```
User
  |
  | hasMany
  |
Post
  |
belongsToMany
  |
Tag
```

---

## 🛠 Technologies Used

- Laravel
- Laravel Sanctum
- MySQL
- Eloquent ORM
- REST API
- Postman
- PHP

---

## 📚 API Documentation

Complete API Documentation:

https://documenter.getpostman.com/view/10337487/2sBY4JxiUF

---

## ⚙ Installation

Clone the repository

```bash
git clone https://github.com/your-username/blog-api.git
```

Move into the project

```bash
cd blog-api
```

Install dependencies

```bash
composer install
```

Copy environment file

```bash
cp .env.example .env
```

Generate application key

```bash
php artisan key:generate
```

Configure your database in the `.env` file.

Run migrations

```bash
php artisan migrate
```

(Optional) Seed the database

```bash
php artisan db:seed
```

Start the development server

```bash
php artisan serve
```

---

## 🔑 Authentication

After login, include the Sanctum token in every protected request.

```
Authorization: Bearer YOUR_ACCESS_TOKEN
```

---

## 📌 API Endpoints

### Authentication

- Register
- Login
- Logout

### Posts

- Get All Posts
- Get Single Post
- Create Post
- Update Own Post
- Delete Own Post

### Admin

- Change Post Status

### Filters

- Posts by Tag
- Posts by User ID
- Posts by Authenticated User
- Pagination

For complete endpoint details, check the API documentation.

---

## 📁 Project Structure Highlights

```
app/
 ├── Http/
 │    ├── Controllers/
 │    ├── Requests/
 │    └── Resources/
 │
 ├── Models/
 │    ├── User.php
 │    ├── Post.php
 │    └── Tag.php
 │
 ├── Helpers/
 │    └── ApiResponse.php
```

---

## 🔒 Access Rules

| User Type | View Posts | Create | Edit Own | Delete Own | Change Status |
| --------- | ---------- | ------ | -------- | ---------- | ------------- |
| Guest     | ✅         | ❌     | ❌       | ❌         | ❌            |
| User      | ✅         | ✅     | ✅       | ✅         | ❌            |
| Admin     | ✅         | ✅     | ✅       | ✅         | ✅            |

---

## ✅ Implemented Concepts

- RESTful API
- Laravel Sanctum Authentication
- Authorization Policies
- Role-Based Access Control
- CRUD Operations
- Query Parameters
- Pagination
- Eloquent Relationships
- Pivot Tables
- API Resource Responses
- Custom API Response Class
- Validation
- Secure Protected Routes

---

![Laravel](https://img.shields.io/badge/Laravel-12.x-red)
![PHP](https://img.shields.io/badge/PHP-8.x-blue)
![License](https://img.shields.io/badge/License-MIT-green)
![Sanctum](https://img.shields.io/badge/Auth-Laravel%20Sanctum-orange)
