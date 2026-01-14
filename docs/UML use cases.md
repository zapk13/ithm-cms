# UML Use Cases

## Use Case Diagram

```mermaid
useCaseDiagram
    actor "System Admin" as SA
    actor "Main Campus Admin" as MCA
    actor "Sub Campus Admin" as SCA
    actor "Student" as ST

    package "Authentication" {
        usecase "Login" as UC1
        usecase "Forgot Password" as UC2
        usecase "Logout" as UC3
    }

    package "Campus Management" {
        usecase "Manage Main/Sub Campuses" as UC4
        usecase "Manage Institute Info" as UC5
    }

    package "User Management" {
        usecase "Manage Users & Roles" as UC6
        usecase "Manage Student Dataset" as UC7
    }

    package "Course & Fee Management" {
        usecase "Manage Courses" as UC8
        usecase "Manage Fee Structures" as UC9
        usecase "Assign Course to Campus" as UC10
    }

    package "Admission Process" {
        usecase "Apply for Admission" as UC11
        usecase "Upload Documents" as UC12
        usecase "Review Application" as UC13
        usecase "Approve/Reject Application" as UC14
        usecase "Mark Update Required" as UC15
    }

    package "Fee Management" {
        usecase "Generate Fee Voucher" as UC16
        usecase "Download Voucher" as UC17
        usecase "Upload Payment Proof" as UC18
        usecase "Verify Payment" as UC19
        usecase "Trigger Fee Reminder" as UC20
    }

    package "Certificates" {
        usecase "Upload Certificate" as UC21
        usecase "Download Certificate" as UC22
    }

    %% Relationships
    SA --> UC1
    SA --> UC3
    SA --> UC4
    SA --> UC5
    SA --> UC6
    SA --> UC7

    MCA --> UC1
    MCA --> UC3
    MCA --> UC7
    MCA --> UC8
    MCA --> UC9
    MCA --> UC10
    MCA --> UC13
    MCA --> UC14
    MCA --> UC15
    MCA --> UC19
    MCA --> UC20

    SCA --> UC1
    SCA --> UC3
    SCA --> UC13
    SCA --> UC14
    SCA --> UC15
    SCA --> UC19
    SCA --> UC20
    SCA --> UC21

    ST --> UC1
    ST --> UC2
    ST --> UC3
    ST --> UC11
    ST --> UC12
    ST --> UC17
    ST --> UC18
    ST --> UC22

    %% Includes/Extends
    UC14 ..> UC16 : <<include>>
    UC19 ..> UC20 : <<extend>>
```

## Use Case Descriptions

### 1. Authentication
- **Actors**: All
- **Description**: Users can log in using email and password. Password recovery is available.

### 2. Campus Management
- **Actors**: System Admin
- **Description**: Create, update, and delete main and sub-campuses. Manage institute details.

### 3. Course & Fee Management
- **Actors**: Main Campus Admin
- **Description**: Define courses, assign them to campuses, and set fee structures (admission, semester, monthly).

### 4. Admission Process
- **Actors**: Student, Admins
- **Description**: Students submit applications with documents. Admins review, approve, reject, or request updates. Approval triggers fee voucher generation.

### 5. Fee Management
- **Actors**: Student, Admins
- **Description**: System generates vouchers. Students pay and upload proof. Admins verify payments. Reminders can be sent manually or automatically.

### 6. Certificate Management
- **Actors**: Sub Campus Admin, Student
- **Description**: Admins upload certificates for completed courses. Students receive notifications and download them.
