// Demo Data for ITHM MVP
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
    ],

    // Statistics data
    statistics: {
        super_admin: {
            total_users: 127,
            active_campuses: 3,
            total_applications: 45,
            system_health: '98.5%',
            role_distribution: {
                student: 89,
                teacher: 15,
                admin: 8,
                accounts: 3,
                super_admin: 2
            }
        },
        admin: {
            pending_applications: 8,
            monthly_applications: 12,
            accepted_applications: 12,
            onboarded_students: 15
        },
        accounts: {
            outstanding_amount: 450000,
            monthly_collection: 1200000,
            overdue_payments: 5,
            daily_collection: 75000
        },
        teacher: {
            total_students: 25,
            assigned_courses: 3,
            attendance_percentage: 87,
            pending_grades: 12
        },
        student: {
            application_status: 'onboarded',
            outstanding_fee: 0,
            uploaded_documents: 3,
            required_documents: 3,
            roll_number: 'HM24001'
        }
    },

    // Admission intakes
    admissionIntakes: [
        {
            id: 1,
            name: 'Fall 2024',
            course_id: 1,
            campus_id: 1,
            start_date: '2024-08-01',
            end_date: '2024-09-30',
            status: 'active',
            created_by: 2,
            created_at: '2024-07-01T10:00:00Z',
            max_applications: 100,
            current_applications: 45
        },
        {
            id: 2,
            name: 'Spring 2025',
            course_id: 2,
            campus_id: 1,
            start_date: '2025-01-01',
            end_date: '2025-02-28',
            status: 'active',
            created_by: 2,
            created_at: '2024-12-01T10:00:00Z',
            max_applications: 80,
            current_applications: 12
        },
        {
            id: 3,
            name: 'Summer 2024',
            course_id: 3,
            campus_id: 2,
            start_date: '2024-06-01',
            end_date: '2024-07-31',
            status: 'closed',
            created_by: 2,
            created_at: '2024-05-01T10:00:00Z',
            max_applications: 50,
            current_applications: 50
        }
    ],

    // Admission forms
    admissionForms: [
        {
            id: 'ADM2024001',
            intake_id: 1,
            student_name: 'Ahmed Ali Khan',
            student_email: 'ahmed.ali@email.com',
            student_phone: '03001234567',
            status: 'pending_review',
            submitted_by: 'student',
            submitted_by_id: 5,
            submitted_at: '2024-08-15T14:30:00Z',
            personal_info: {
                full_name: 'Ahmed Ali Khan',
                father_name: 'Muhammad Ali Khan',
                mother_name: 'Fatima Khan',
                cnic: '12345-1234567-1',
                date_of_birth: '2000-05-15',
                gender: 'Male',
                marital_status: 'Single',
                nationality: 'Pakistani',
                religion: 'Islam',
                blood_group: 'O+',
                address: '123 Main Street, Lahore',
                city: 'Lahore',
                province: 'Punjab',
                postal_code: '54000',
                phone: '03001234567',
                email: 'ahmed.ali@email.com',
                emergency_contact: '03009876543',
                emergency_relation: 'Brother'
            },
            guardian_info: {
                guardian_name: 'Muhammad Ali Khan',
                guardian_relation: 'Father',
                guardian_cnic: '12345-1234567-2',
                guardian_phone: '03001234568',
                guardian_occupation: 'Business',
                guardian_address: '123 Main Street, Lahore',
                guardian_email: 'ali.khan@email.com'
            },
            academic_info: {
                matric_marks: 850,
                matric_total: 1100,
                matric_percentage: 77.27,
                matric_board: 'Lahore Board',
                matric_year: 2018,
                intermediate_marks: 750,
                intermediate_total: 1100,
                intermediate_percentage: 68.18,
                intermediate_board: 'Lahore Board',
                intermediate_year: 2020,
                bachelor_degree: 'BSc Computer Science',
                bachelor_institution: 'University of Lahore',
                bachelor_year: 2024,
                bachelor_cgpa: 3.2,
                master_degree: null,
                master_institution: null,
                master_year: null,
                master_cgpa: null
            },
            distinctions: [
                {
                    title: 'Best Student Award',
                    institution: 'University of Lahore',
                    year: 2023,
                    description: 'Awarded for outstanding academic performance'
                },
                {
                    title: 'Programming Competition Winner',
                    institution: 'TechFest 2023',
                    year: 2023,
                    description: 'First place in national programming competition'
                }
            ],
            certificates: [
                {
                    name: 'Web Development Certificate',
                    institution: 'Coursera',
                    year: 2023,
                    duration: '6 months'
                },
                {
                    name: 'Python Programming',
                    institution: 'Udemy',
                    year: 2022,
                    duration: '3 months'
                }
            ],
            previous_studentship: {
                has_previous: true,
                institution: 'University of Punjab',
                program: 'BS Information Technology',
                start_year: 2020,
                end_year: 2022,
                reason_for_leaving: 'Transferred to better program'
            },
            undertaking: {
                information_accurate: true,
                agree_to_terms: true,
                agree_to_verification: true,
                signature_date: '2024-08-15',
                digital_signature: 'Ahmed Ali Khan'
            },
            documents: [
                {
                    type: 'CNIC',
                    filename: 'ahmed_cnic.pdf',
                    uploaded_at: '2024-08-15T14:25:00Z',
                    status: 'verified'
                },
                {
                    type: 'Matric Certificate',
                    filename: 'ahmed_matric.pdf',
                    uploaded_at: '2024-08-15T14:26:00Z',
                    status: 'pending'
                },
                {
                    type: 'Passport Size Photo',
                    filename: 'ahmed_photo.jpg',
                    uploaded_at: '2024-08-15T14:27:00Z',
                    status: 'verified'
                }
            ],
            timeline: [
                {
                    action: 'Form Submitted',
                    user: 'Ahmed Ali Khan',
                    timestamp: '2024-08-15T14:30:00Z',
                    status: 'pending_review'
                }
            ]
        },
        {
            id: 'ADM2024002',
            intake_id: 1,
            student_name: 'Sara Ahmed',
            student_email: 'sara.ahmed@email.com',
            student_phone: '03001234568',
            status: 'under_review',
            submitted_by: 'admin',
            submitted_by_id: 2,
            submitted_at: '2024-08-10T10:15:00Z',
            personal_info: {
                full_name: 'Sara Ahmed',
                father_name: 'Muhammad Ahmed',
                mother_name: 'Ayesha Ahmed',
                cnic: '12345-1234567-3',
                date_of_birth: '1999-03-20',
                gender: 'Female',
                marital_status: 'Single',
                nationality: 'Pakistani',
                religion: 'Islam',
                blood_group: 'A+',
                address: '456 Park Avenue, Karachi',
                city: 'Karachi',
                province: 'Sindh',
                postal_code: '75000',
                phone: '03001234568',
                email: 'sara.ahmed@email.com',
                emergency_contact: '03009876544',
                emergency_relation: 'Father'
            },
            guardian_info: {
                guardian_name: 'Muhammad Ahmed',
                guardian_relation: 'Father',
                guardian_cnic: '12345-1234567-4',
                guardian_phone: '03001234569',
                guardian_occupation: 'Engineer',
                guardian_address: '456 Park Avenue, Karachi',
                guardian_email: 'm.ahmed@email.com'
            },
            academic_info: {
                matric_marks: 920,
                matric_total: 1100,
                matric_percentage: 83.64,
                matric_board: 'Karachi Board',
                matric_year: 2017,
                intermediate_marks: 880,
                intermediate_total: 1100,
                intermediate_percentage: 80.00,
                intermediate_board: 'Karachi Board',
                intermediate_year: 2019,
                bachelor_degree: 'BSc Software Engineering',
                bachelor_institution: 'NED University',
                bachelor_year: 2023,
                bachelor_cgpa: 3.7,
                master_degree: 'MSc Data Science',
                master_institution: 'University of Karachi',
                master_year: 2024,
                master_cgpa: 3.8
            },
            distinctions: [
                {
                    title: 'Dean\'s List',
                    institution: 'NED University',
                    year: 2022,
                    description: 'Maintained GPA above 3.5 for consecutive semesters'
                }
            ],
            certificates: [
                {
                    name: 'Data Science Specialization',
                    institution: 'Coursera',
                    year: 2023,
                    duration: '8 months'
                }
            ],
            previous_studentship: {
                has_previous: false
            },
            undertaking: {
                information_accurate: true,
                agree_to_terms: true,
                agree_to_verification: true,
                signature_date: '2024-08-10',
                digital_signature: 'Sara Ahmed'
            },
            documents: [
                {
                    type: 'CNIC',
                    filename: 'sara_cnic.pdf',
                    uploaded_at: '2024-08-10T10:10:00Z',
                    status: 'verified'
                },
                {
                    type: 'Matric Certificate',
                    filename: 'sara_matric.pdf',
                    uploaded_at: '2024-08-10T10:11:00Z',
                    status: 'verified'
                },
                {
                    type: 'Passport Size Photo',
                    filename: 'sara_photo.jpg',
                    uploaded_at: '2024-08-10T10:12:00Z',
                    status: 'verified'
                }
            ],
            timeline: [
                {
                    action: 'Form Submitted',
                    user: 'Prof. Ahmed Hassan',
                    timestamp: '2024-08-10T10:15:00Z',
                    status: 'under_review'
                },
                {
                    action: 'Documents Verified',
                    user: 'Prof. Ahmed Hassan',
                    timestamp: '2024-08-12T14:30:00Z',
                    status: 'under_review'
                }
            ]
        }
    ]
};

// Helper functions
function getCurrentUser() {
    const demoUser = localStorage.getItem('demoUser');
    return demoUser ? JSON.parse(demoUser) : null;
}

function getApplicationsByStatus(status) {
    return demoData.applications.filter(app => app.status === status);
}

function getApplicationsByCampus(campusId) {
    return demoData.applications.filter(app => {
        const course = demoData.courses.find(c => c.id === app.course_id);
        return course && course.campus_id === campusId;
    });
}

function getPaymentsByStatus(status) {
    return demoData.payments.filter(payment => payment.status === status);
}

function getStatisticsByRole(role) {
    return demoData.statistics[role] || {};
}

function formatCurrency(amount) {
    return new Intl.NumberFormat('en-PK', {
        style: 'currency',
        currency: 'PKR',
        minimumFractionDigits: 0
    }).format(amount);
}

function formatDate(dateString) {
    return new Date(dateString).toLocaleDateString('en-PK', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
}

function timeAgo(dateString) {
    const now = new Date();
    const date = new Date(dateString);
    const diffInSeconds = Math.floor((now - date) / 1000);
    
    if (diffInSeconds < 60) return 'Just now';
    if (diffInSeconds < 3600) return `${Math.floor(diffInSeconds / 60)} minutes ago`;
    if (diffInSeconds < 86400) return `${Math.floor(diffInSeconds / 3600)} hours ago`;
    if (diffInSeconds < 2592000) return `${Math.floor(diffInSeconds / 86400)} days ago`;
    if (diffInSeconds < 31536000) return `${Math.floor(diffInSeconds / 2592000)} months ago`;
    return `${Math.floor(diffInSeconds / 31536000)} years ago`;
}

function getStatusColor(status) {
    const colors = {
        pending: 'text-yellow-600',
        under_review: 'text-blue-600',
        accepted: 'text-green-600',
        rejected: 'text-red-600',
        onboarded: 'text-purple-600',
        active: 'text-green-600',
        inactive: 'text-gray-600',
        suspended: 'text-red-600'
    };
    return colors[status] || 'text-gray-600';
}

function getStatusBadgeClass(status) {
    const classes = {
        pending: 'status-pending',
        under_review: 'status-under_review',
        accepted: 'status-accepted',
        rejected: 'status-rejected',
        onboarded: 'status-onboarded',
        active: 'status-active',
        inactive: 'status-inactive',
        suspended: 'status-suspended'
    };
    return classes[status] || 'status-inactive';
}

// Export for use in other files
if (typeof module !== 'undefined' && module.exports) {
    module.exports = demoData;
}
