# Project Folder Structure for PHP Core

This document outlines the directory structure for the ITHM CMS, built using core PHP with an MVC (Model-View-Controller) architecture.

```text
cms.ithm.edu.pk/
├── app/
│   ├── Controllers/       # Handles incoming requests and application logic
│   │   ├── AuthController.php
│   │   ├── AdminController.php
│   │   ├── StudentController.php
│   │   └── ...
│   ├── Models/            # Database interactions and business data logic
│   │   ├── User.php
│   │   ├── Campus.php
│   │   ├── Course.php
│   │   ├── Admission.php
│   │   └── ...
│   ├── Core/              # Framework core files
│   │   ├── Database.php   # Database connection wrapper (PDO)
│   │   ├── Router.php     # Handles URL routing
│   │   ├── Controller.php # Base controller class
│   │   └── Model.php      # Base model class
│   ├── Helpers/           # Utility functions
│   │   ├── SessionHelper.php
│   │   ├── ValidationHelper.php
│   │   └── FileUploadHelper.php
│   └── Middleware/        # Request filtering (Auth, Roles)
│       └── AuthMiddleware.php
├── config/                # Configuration files
│   ├── database.php       # DB credentials
│   └── config.php         # App constants (BASE_URL, etc.)
├── public/                # Web root (only this folder is accessible via browser)
│   ├── index.php          # Entry point
│   ├── assets/
│   │   ├── css/           # Tailwind output, custom CSS
│   │   ├── js/            # Alpine.js scripts, custom JS
│   │   └── images/        # Static images (logos, icons)
│   └── uploads/           # Publicly accessible uploads (if any, else in storage)
├── resources/             # Views and raw assets
│   ├── views/             # HTML templates (PHP files)
│   │   ├── layouts/       # Header, footer, sidebar
│   │   ├── auth/          # Login, forgot password
│   │   ├── admin/         # Admin dashboard and pages
│   │   └── student/       # Student dashboard and pages
│   └── css/               # Source CSS (Tailwind input)
├── storage/               # Private files, logs, uploads
│   ├── uploads/           # Student documents, receipts (protected)
│   └── logs/              # Error logs
├── routes/                # Route definitions
│   └── web.php            # Define URL routes here
├── .env                   # Environment variables (DB_HOST, DB_USER, etc.)
├── .gitignore             # Git ignore rules
├── composer.json          # Dependency management (if using Composer for autoloading)
└── README.md              # Project documentation
```

## Key Directories

- **app/**: Contains the core application logic.
- **public/**: The entry point for the web server. `index.php` initializes the app.
- **resources/views/**: Contains the HTML/PHP templates for the UI.
- **config/**: Stores configuration settings.
- **routes/**: Defines the mapping between URLs and Controllers.
```
