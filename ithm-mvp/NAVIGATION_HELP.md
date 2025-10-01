# ITHM CMS MVP - Navigation Help

## ✅ **Correct File Paths**

The dashboard files are located in the correct directories. Here are the proper paths:

### **Dashboard Locations:**
- **Super Admin**: `ithm-mvp/super-admin/dashboard.html`
- **Campus Admin**: `ithm-mvp/admin/dashboard.html`
- **Accounts Officer**: `ithm-mvp/accounts/dashboard.html`
- **Teacher**: `ithm-mvp/teacher/dashboard.html`
- **Student**: `ithm-mvp/student/dashboard.html`

### **How to Access:**

#### **Option 1: Start from Landing Page (Recommended)**
1. Open `ithm-mvp/index.html` in your web browser
2. Click any of the 5 demo login buttons
3. You'll be automatically redirected to the correct dashboard

#### **Option 2: Direct Access**
If you need to access dashboards directly:
- **Super Admin**: `http://localhost/ithm-cms/ithm-mvp/super-admin/dashboard.html`
- **Campus Admin**: `http://localhost/ithm-cms/ithm-mvp/admin/dashboard.html`
- **Accounts**: `http://localhost/ithm-cms/ithm-mvp/accounts/dashboard.html`
- **Teacher**: `http://localhost/ithm-cms/ithm-mvp/teacher/dashboard.html`
- **Student**: `http://localhost/ithm-cms/ithm-mvp/student/dashboard.html`

### **Common Issues:**

#### **❌ Wrong Path:**
- `/ithm-mvp/auth/super-admin/dashboard.html` ← This is WRONG
- The `auth` folder only contains login, register, and forgot-password pages

#### **✅ Correct Path:**
- `/ithm-mvp/super-admin/dashboard.html` ← This is CORRECT
- Dashboard files are in their own role-specific folders

### **File Structure:**
```
ithm-mvp/
├── index.html                    ← Start here
├── auth/                        ← Authentication pages only
│   ├── login.html
│   ├── register.html
│   └── forgot-password.html
├── super-admin/                 ← Super Admin dashboard
│   └── dashboard.html
├── admin/                       ← Campus Admin dashboard
│   └── dashboard.html
├── accounts/                    ← Accounts Officer dashboard
│   └── dashboard.html
├── teacher/                     ← Teacher dashboard
│   └── dashboard.html
└── student/                     ← Student dashboard
    └── dashboard.html
```

### **🚀 Quick Start:**
1. Open `ithm-mvp/index.html` in your browser
2. Click any demo login button
3. You'll be redirected to the correct dashboard automatically

**The system is working correctly - just make sure you're using the right file paths!**
