// Enhanced Demo Data for ITHM MVP
// This file contains realistic demo data for all system modules
const demoData = {
    // User accounts
    users: [
        {
            id: 1,
            username: 'super_admin',
            email: 'super@ithm.edu.pk',
            role: 'super_admin',
            first_name: 'Dr. Muhammad',
            last_name: 'Ali Khan',
            phone: '03001234567',
            campus_id: null,
            status: 'active'
        },
        {
            id: 2,
            username: 'admin_lahore',
            email: 'admin.lahore@ithm.edu.pk',
            role: 'admin',
            first_name: 'Prof. Ahmed',
            last_name: 'Hassan',
            phone: '03001234568',
            campus_id: 1,
            status: 'active'
        },
        {
            id: 3,
            username: 'accounts_lahore',
            email: 'accounts.lahore@ithm.edu.pk',
            role: 'accounts',
            first_name: 'Ms. Fatima',
            last_name: 'Sheikh',
            phone: '03001234569',
            campus_id: 1,
            status: 'active'
        },
        {
            id: 4,
            username: 'teacher_lahore',
            email: 'teacher.lahore@ithm.edu.pk',
            role: 'teacher',
            first_name: 'Mr. Usman',
            last_name: 'Malik',
            phone: '03001234570',
            campus_id: 1,
            status: 'active'
        },
        {
            id: 5,
            username: 'student_demo',
            email: 'student@ithm.edu.pk',
            role: 'student',
            first_name: 'Ahmed',
            last_name: 'Khan',
            phone: '03001234571',
            campus_id: 1,
            status: 'active'
        }
    ],

    // Campuses
    campuses: [
        {
            id: 1,
            name: 'ITHM Main Campus Lahore',
            code: 'MCL',
            address: '123 Gulberg III, Lahore, Pakistan',
            phone: '+92-42-35714001',
            status: 'active'
        },
        {
            id: 2,
            name: 'ITHM Karachi Campus',
            code: 'KC',
            address: '456 Defence Phase 5, Karachi, Pakistan',
            phone: '+92-21-35342001',
            status: 'active'
        },
        {
            id: 3,
            name: 'ITHM Islamabad Campus',
            code: 'IC',
            address: '789 Blue Area, Islamabad, Pakistan',
            phone: '+92-51-26110001',
            status: 'active'
        }
    ],

    // Courses
    courses: [
        {
            id: 1,
            name: 'Hotel Management',
            code: 'HM-2024',
            duration: '2 Years',
            campus_id: 1,
            admission_fee: 25000.00,
            tuition_fee: 150000.00,
            security_deposit: 10000.00,
            other_charges: 5000.00,
            total_fee: 190000.00,
            status: 'active'
        },
        {
            id: 2,
            name: 'Tourism Management',
            code: 'TM-2024',
            duration: '2 Years',
            campus_id: 1,
            admission_fee: 20000.00,
            tuition_fee: 120000.00,
            security_deposit: 10000.00,
            other_charges: 5000.00,
            total_fee: 155000.00,
            status: 'active'
        },
        {
            id: 3,
            name: 'Culinary Arts',
            code: 'CA-2024',
            duration: '18 Months',
            campus_id: 1,
            admission_fee: 30000.00,
            tuition_fee: 180000.00,
            security_deposit: 15000.00,
            other_charges: 7000.00,
            total_fee: 232000.00,
            status: 'active'
        },
        {
            id: 4,
            name: 'Event Management',
            code: 'EM-2024',
            duration: '1 Year',
            campus_id: 1,
            admission_fee: 15000.00,
            tuition_fee: 80000.00,
            security_deposit: 5000.00,
            other_charges: 3000.00,
            total_fee: 103000.00,
            status: 'active'
        }
    ],

    // Student applications
    applications: [
        {
            id: 1,
            tracking_id: 'ITHM2024001',
            user_id: 5,
            course_id: 1,
            status: 'onboarded',
            first_name: 'Ahmed',
            last_name: 'Khan',
            father_name: 'Muhammad Khan',
            cnic: '35202-1234567-1',
            date_of_birth: '2002-05-15',
            gender: 'male',
            phone: '03001234571',
            address: 'House 123, Model Town, Lahore',
            education_level: 'Intermediate',
            institution: 'Government College Lahore',
            passing_year: 2022,
            percentage: 85.50,
            guardian_name: 'Muhammad Khan',
            guardian_cnic: '35202-1234567-2',
            guardian_phone: '03009876543',
            guardian_relation: 'father',
            roll_number: 'HM24001',
            admin_notes: 'Excellent academic record',
            created_at: '2024-01-15 10:30:00'
        },
        {
            id: 2,
            tracking_id: 'ITHM2024002',
            user_id: 6,
            course_id: 2,
            status: 'accepted',
            first_name: 'Fatima',
            last_name: 'Ali',
            father_name: 'Ali Hassan',
            cnic: '35202-2345678-2',
            date_of_birth: '2003-08-22',
            gender: 'female',
            phone: '03002345672',
            address: 'Street 45, Johar Town, Lahore',
            education_level: 'Intermediate',
            institution: 'Lahore College for Women',
            passing_year: 2023,
            percentage: 78.25,
            guardian_name: 'Ali Hassan',
            guardian_cnic: '35202-2345678-3',
            guardian_phone: '03008765432',
            guardian_relation: 'father',
            roll_number: null,
            admin_notes: 'Good candidate for tourism',
            created_at: '2024-01-20 14:15:00'
        },
        {
            id: 3,
            tracking_id: 'ITHM2024003',
            user_id: 7,
            course_id: 1,
            status: 'under_review',
            first_name: 'Hassan',
            last_name: 'Ahmed',
            father_name: 'Ahmed Malik',
            cnic: '35202-3456789-3',
            date_of_birth: '2001-12-10',
            gender: 'male',
            phone: '03003456673',
            address: 'Block B, DHA, Lahore',
            education_level: 'Bachelor',
            institution: 'University of Punjab',
            passing_year: 2023,
            percentage: 65.75,
            guardian_name: 'Ahmed Malik',
            guardian_cnic: '35202-3456789-4',
            guardian_phone: '03007654321',
            guardian_relation: 'father',
            roll_number: null,
            admin_notes: 'Under document verification',
            created_at: '2024-01-25 09:45:00'
        },
        {
            id: 4,
            tracking_id: 'ITHM2024004',
            user_id: 8,
            course_id: 3,
            status: 'pending',
            first_name: 'Ayesha',
            last_name: 'Butt',
            father_name: 'Umer Butt',
            cnic: '35202-4567890-4',
            date_of_birth: '2004-03-18',
            gender: 'female',
            phone: '03004567674',
            address: 'Garden Town, Lahore',
            education_level: 'Intermediate',
            institution: 'Kinnaird College',
            passing_year: 2024,
            percentage: 92.00,
            guardian_name: 'Umer Butt',
            guardian_cnic: '35202-4567890-5',
            guardian_phone: '03006543210',
            guardian_relation: 'father',
            roll_number: null,
            admin_notes: null,
            created_at: '2024-02-01 11:20:00'
        },
        {
            id: 5,
            tracking_id: 'ITHM2024005',
            user_id: 9,
            course_id: 2,
            status: 'rejected',
            first_name: 'Usman',
            last_name: 'Sheikh',
            father_name: 'Tariq Sheikh',
            cnic: '35202-5678901-5',
            date_of_birth: '2000-07-25',
            gender: 'male',
            phone: '03005678675',
            address: 'Gulshan Ravi, Lahore',
            education_level: 'Matric',
            institution: 'Government High School',
            passing_year: 2020,
            percentage: 55.50,
            guardian_name: 'Tariq Sheikh',
            guardian_cnic: '35202-5678901-6',
            guardian_phone: '03005432109',
            guardian_relation: 'father',
            roll_number: null,
            admin_notes: 'Does not meet minimum education requirement',
            created_at: '2024-01-10 16:30:00'
        }
    ],

    // Payment records
    payments: [
        {
            id: 1,
            application_id: 1,
            voucher_number: 'FV2024001',
            amount: 25000.00,
            payment_type: 'admission_fee',
            status: 'paid',
            due_date: '2024-01-15',
            payment_date: '2024-01-12',
            payment_method: 'Bank Transfer',
            admin_notes: 'Admission fee paid on time'
        },
        {
            id: 2,
            application_id: 1,
            voucher_number: 'FV2024002',
            amount: 37500.00,
            payment_type: 'tuition_fee',
            status: 'paid',
            due_date: '2024-03-01',
            payment_date: '2024-02-28',
            payment_method: 'Cash',
            admin_notes: 'First semester fee'
        },
        {
            id: 3,
            application_id: 2,
            voucher_number: 'FV2024003',
            amount: 20000.00,
            payment_type: 'admission_fee',
            status: 'pending',
            due_date: '2024-02-01',
            payment_date: null,
            payment_method: null,
            admin_notes: 'Admission fee voucher generated'
        },
        {
            id: 4,
            application_id: 3,
            voucher_number: 'FV2024004',
            amount: 25000.00,
            payment_type: 'admission_fee',
            status: 'pending',
            due_date: '2024-01-30',
            payment_date: null,
            payment_method: null,
            admin_notes: 'Pending document verification'
        }
    ]
};

// Demo credentials for quick login
const demoCredentials = {
        super_admin: {
        email: 'super@ithm.edu.pk',
        password: 'admin123',
        role: 'super_admin',
        name: 'Dr. Muhammad Ali Khan'
        },
        admin: {
        email: 'admin.lahore@ithm.edu.pk',
        password: 'admin123',
        role: 'admin',
        name: 'Prof. Ahmed Hassan'
        },
        accounts: {
        email: 'accounts.lahore@ithm.edu.pk',
        password: 'admin123',
        role: 'accounts',
        name: 'Ms. Fatima Sheikh'
        },
        teacher: {
        email: 'teacher.lahore@ithm.edu.pk',
        password: 'admin123',
        role: 'teacher',
        name: 'Mr. Usman Malik'
        },
        student: {
        email: 'student@ithm.edu.pk',
        password: 'admin123',
        role: 'student',
        name: 'Ahmed Khan'
    }
};

// Demo users for display
const demoUsers = {
    super_admin: {
            id: 1,
        name: 'Dr. Muhammad Ali Khan',
        email: 'super@ithm.edu.pk',
        role: 'Super Admin',
        campus: 'System Wide',
        avatar: '👑'
    },
    admin: {
            id: 2,
        name: 'Prof. Ahmed Hassan',
        email: 'admin.lahore@ithm.edu.pk',
        role: 'Campus Admin',
        campus: 'Main Campus Lahore',
        avatar: '🏢'
    },
    accounts: {
            id: 3,
        name: 'Ms. Fatima Sheikh',
        email: 'accounts.lahore@ithm.edu.pk',
        role: 'Accounts Officer',
        campus: 'Main Campus Lahore',
        avatar: '💰'
    },
    teacher: {
        id: 4,
        name: 'Mr. Usman Malik',
        email: 'teacher.lahore@ithm.edu.pk',
        role: 'Teacher',
        campus: 'Main Campus Lahore',
        avatar: '👨‍🏫'
    },
    student: {
        id: 5,
        name: 'Ahmed Khan',
        email: 'student@ithm.edu.pk',
        role: 'Student',
        campus: 'Main Campus Lahore',
        avatar: '🎓'
    }
};

// Demo applications data
const demoApplications = [
    {
        id: 1,
        tracking_id: 'ITHM2024001',
        student_name: 'Ahmed Khan',
        course: 'Hotel Management',
        status: 'onboarded',
        applied_date: '2024-01-15',
        roll_number: 'HM24001'
    },
    {
        id: 2,
        tracking_id: 'ITHM2024002',
        student_name: 'Fatima Ali',
        course: 'Tourism Management',
        status: 'accepted',
        applied_date: '2024-01-20',
        roll_number: null
    },
    {
        id: 3,
        tracking_id: 'ITHM2024003',
        student_name: 'Hassan Ahmed',
        course: 'Hotel Management',
            status: 'under_review',
        applied_date: '2024-01-25',
        roll_number: null
    },
    {
        id: 4,
        tracking_id: 'ITHM2024004',
        student_name: 'Ayesha Butt',
        course: 'Culinary Arts',
        status: 'pending',
        applied_date: '2024-02-01',
        roll_number: null
    }
];

// Demo payments data
const demoPayments = [
    {
        id: 1,
        student_name: 'Ahmed Khan',
        amount: 25000,
        type: 'Admission Fee',
        status: 'paid',
        date: '2024-01-12'
    },
    {
        id: 2,
        student_name: 'Ahmed Khan',
        amount: 37500,
        type: 'Tuition Fee',
        status: 'paid',
        date: '2024-02-28'
    },
    {
        id: 3,
        student_name: 'Fatima Ali',
        amount: 20000,
        type: 'Admission Fee',
        status: 'pending',
        date: null
    }
];

// Statistics by role
const demoStatistics = {
    super_admin: {
        total_users: 127,
        active_campuses: 3,
        total_applications: 45,
        system_health: '98.5%'
            },
            admin: {
        total_onboarded: 1247,
        pending_applications: 23,
        fee_submitted: 2400000,
        fee_pending: 850000
            },
            accounts: {
        outstanding_amount: 1200000,
        monthly_collection: 3800000,
        overdue_payments: 47,
        daily_collection: 125000
            },
            teacher: {
        total_students: 127,
        assigned_courses: 8,
        attendance_rate: 94,
        pending_grades: 23
    },
    student: {
        application_status: 'accepted',
        outstanding_fee: 25000,
        uploaded_documents: 8,
        required_documents: 10,
        roll_number: 'ITHM-2024-001'
    }
};

// Demo login function
function demoLogin(role) {
    console.log('Demo login for role:', role);
    
    const userInfo = demoUsers[role];
    if (!userInfo) {
        console.error('Invalid role:', role);
        return;
    }

    // Store demo user in localStorage
    localStorage.setItem('demoUser', JSON.stringify(userInfo));
    
    // Show success notification
    showNotification(`Logged in as ${userInfo.name} (${userInfo.role})`, 'success');
    
    // Redirect to appropriate dashboard
    const redirects = {
        super_admin: 'super-admin/dashboard.html',
        admin: 'admin/dashboard.html',
        accounts: 'accounts/dashboard.html',
        teacher: 'teacher/dashboard.html',
        student: 'student/dashboard.html'
    };
    
    setTimeout(() => {
        window.location.href = redirects[role];
    }, 1000);
}

// Get statistics by role
function getStatisticsByRole(role) {
    return demoStatistics[role] || {};
}

// Get application by user ID
function getApplicationByUserId(userId) {
    return demoApplications.find(app => app.id === userId);
}

// Get payments by application ID
function getPaymentsByApplicationId(applicationId) {
    return demoPayments.filter(payment => payment.id === applicationId);
}
    
    // Show notification
function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ${
            type === 'success' ? 'bg-green-100 text-green-800 border border-green-200' :
            type === 'error' ? 'bg-red-100 text-red-800 border border-red-200' :
            type === 'warning' ? 'bg-yellow-100 text-yellow-800 border border-yellow-200' :
            'bg-blue-100 text-blue-800 border border-blue-200'
        }`;
        notification.innerHTML = `
            <div class="flex items-center space-x-2">
                <span>${message}</span>
                <button onclick="this.parentElement.parentElement.remove()" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            if (notification.parentElement) {
                notification.remove();
            }
        }, 5000);
    }

// Make functions globally available
window.demoLogin = demoLogin;
window.getStatisticsByRole = getStatisticsByRole;
window.getApplicationByUserId = getApplicationByUserId;
window.getPaymentsByApplicationId = getPaymentsByApplicationId;
window.showNotification = showNotification;
window.demoData = demoData;
window.demoUsers = demoUsers;
window.demoApplications = demoApplications;
window.demoPayments = demoPayments;
window.demoStatistics = demoStatistics;