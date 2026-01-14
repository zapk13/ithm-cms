# API Documentation

This document outlines the internal API endpoints used by the ITHM CMS frontend (Alpine.js/Vue.js) to communicate with the PHP backend.

**Base URL**: `/api`

## Authentication

### Login
- **Endpoint**: `POST /auth/login`
- **Body**:
  ```json
  {
    "email": "user@example.com",
    "password": "password123"
  }
  ```
- **Response**:
  - `200 OK`: `{ "token": "...", "user": { ... } }` (if using JWT) or `{ "message": "Login successful" }` (if session-based)
  - `401 Unauthorized`: `{ "error": "Invalid credentials" }`

### Logout
- **Endpoint**: `POST /auth/logout`
- **Response**:
  - `200 OK`: `{ "message": "Logged out successfully" }`

## Campuses

### Get All Campuses
- **Endpoint**: `GET /campuses`
- **Response**:
  ```json
  [
    { "id": 1, "name": "Main Campus", "type": "Main" },
    { "id": 2, "name": "City Campus", "type": "Sub" }
  ]
  ```

## Courses

### Get All Courses
- **Endpoint**: `GET /courses`
- **Query Params**: `?campus_id=1` (optional filter)
- **Response**:
  ```json
  [
    { "id": 101, "name": "Hospitality Management", "code": "HM-101" }
  ]
  ```

## Admissions

### Submit Application
- **Endpoint**: `POST /admissions`
- **Body**:
  ```json
  {
    "course_id": 101,
    "campus_id": 1,
    "personal_info": { ... },
    "academic_info": { ... }
  }
  ```
- **Response**:
  - `201 Created`: `{ "admission_id": 500, "message": "Application submitted" }`

### Upload Document
- **Endpoint**: `POST /admissions/{id}/documents`
- **Body**: `FormData` with file field `document` and text field `type`
- **Response**:
  - `200 OK`: `{ "message": "Document uploaded" }`

### Get Application Status
- **Endpoint**: `GET /admissions/my-applications`
- **Response**:
  ```json
  [
    { "id": 500, "course": "...", "status": "Pending" }
  ]
  ```

## Fees

### Get My Vouchers
- **Endpoint**: `GET /fees/vouchers`
- **Response**:
  ```json
  [
    { "id": 900, "amount": 5000, "due_date": "2023-12-01", "status": "Unpaid" }
  ]
  ```

### Upload Payment Proof
- **Endpoint**: `POST /fees/vouchers/{id}/pay`
- **Body**: `FormData` with file field `proof` and text field `transaction_id`
- **Response**:
  - `200 OK`: `{ "message": "Payment proof submitted" }`

## Notifications

### Get Notifications
- **Endpoint**: `GET /notifications`
- **Response**:
  ```json
  [
    { "id": 1, "title": "Fee Due", "message": "...", "is_read": false }
  ]
  ```

### Mark as Read
- **Endpoint**: `POST /notifications/{id}/read`
- **Response**:
  - `200 OK`: `{ "success": true }`
