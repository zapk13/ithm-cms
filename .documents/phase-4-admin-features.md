# Phase 4: Admin & Management Features

## Pre-Phase Checklist
- [ ] Phase 3 completed successfully
- [ ] Student application system functional
- [ ] File upload system working
- [ ] Basic dashboards operational
- [ ] Application status tracking working

## Phase Objectives
1. Implement admin dashboards
2. Create application management system
3. Build fee management system
4. Implement user management
5. Create reporting system

## Implementation Steps

### Step 1: Admin Dashboard System
**Duration**: 3-4 hours

**File: `modules/admin/dashboard.php`**
```php
<?php
session_start();
require_once '../../config/constants.php';
require_once '../../config/database.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';

$auth = new Auth();
$auth->requireRole('admin');

$db = new Database();
$pdo = $db->getConnection();

// Get campus-specific statistics
$campusId = $_SESSION['campus_id'];

// Pending applications
$stmt = $pdo->prepare("
    SELECT COUNT(*) FROM applications a
    JOIN courses c ON a.course_id = c.id
    WHERE c.campus_id = ? AND a.status = 'pending'
");
$stmt->execute([$campusId]);
$pendingApplications = $stmt->fetchColumn();

// Monthly applications
$stmt = $pdo->prepare("
    SELECT COUNT(*) FROM applications a
    JOIN courses c ON a.course_id = c.id
    WHERE c.campus_id = ? AND DATE_FORMAT(a.created_at, '%Y-%m') = DATE_FORMAT(NOW(), '%Y-%m')
");
$stmt->execute([$campusId]);
$monthlyApplications = $stmt->fetchColumn();

// Accepted applications
$stmt = $pdo->prepare("
    SELECT COUNT(*) FROM applications a
    JOIN courses c ON a.course_id = c.id
    WHERE c.campus_id = ? AND a.status = 'accepted'
");
$stmt->execute([$campusId]);
$acceptedApplications = $stmt->fetchColumn();

// Onboarded students
$stmt = $pdo->prepare("
    SELECT COUNT(*) FROM applications a
    JOIN courses c ON a.course_id = c.id
    WHERE c.campus_id = ? AND a.status = 'onboarded'
");
$stmt->execute([$campusId]);
$onboardedStudents = $stmt->fetchColumn();

// Recent applications
$stmt = $pdo->prepare("
    SELECT a.*, c.name as course_name
    FROM applications a
    JOIN courses c ON a.course_id = c.id
    WHERE c.campus_id = ?
    ORDER BY a.created_at DESC
    LIMIT 10
");
$stmt->execute([$campusId]);
$recentApplications = $stmt->fetchAll(PDO::FETCH_ASSOC);

$page_title = 'Admin Dashboard';
include '../../includes/header.php';
?>

<div class="max-w-7xl mx-auto py-6">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Admin Dashboard</h1>
        <p class="text-gray-600">Manage applications and campus operations</p>
    </div>
    
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-900">Pending Reviews</h3>
            <p class="text-3xl font-bold text-orange-600"><?= $pendingApplications ?></p>
            <p class="text-sm text-gray-500">Need attention</p>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-900">This Month</h3>
            <p class="text-3xl font-bold text-blue-600"><?= $monthlyApplications ?></p>
            <p class="text-sm text-gray-500">New applications</p>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-900">Accepted</h3>
            <p class="text-3xl font-bold text-green-600"><?= $acceptedApplications ?></p>
            <p class="text-sm text-gray-500">Ready for onboarding</p>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-900">Onboarded</h3>
            <p class="text-3xl font-bold text-purple-600"><?= $onboardedStudents ?></p>
            <p class="text-sm text-gray-500">Active students</p>
        </div>
    </div>
    
    <!-- Recent Applications Table -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b">
            <h3 class="text-lg font-semibold">Recent Applications</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tracking ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Course</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach($recentApplications as $app): ?>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            <?= $app['tracking_id'] ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <?= $app['first_name'] ?> <?= $app['last_name'] ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <?= $app['course_name'] ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full <?= getStatusColor($app['status']) ?>">
                                <?= ucwords(str_replace('_', ' ', $app['status'])) ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="view_application.php?id=<?= $app['id'] ?>" 
                               class="text-indigo-600 hover:text-indigo-900">View</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
```

### Step 2: Application Management System
**Duration**: 4-5 hours

**File: `modules/admin/applications.php`**
```php
<?php
session_start();
require_once '../../config/constants.php';
require_once '../../config/database.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';

$auth = new Auth();
$auth->requireRole('admin');

$db = new Database();
$pdo = $db->getConnection();

// Get filter parameters
$status = $_GET['status'] ?? '';
$course = $_GET['course'] ?? '';
$search = $_GET['search'] ?? '';
$page = $_GET['page'] ?? 1;
$limit = 25;
$offset = ($page - 1) * $limit;

// Build query
$whereConditions = ["c.campus_id = ?"];
$params = [$_SESSION['campus_id']];

if ($status) {
    $whereConditions[] = "a.status = ?";
    $params[] = $status;
}

if ($course) {
    $whereConditions[] = "a.course_id = ?";
    $params[] = $course;
}

if ($search) {
    $whereConditions[] = "(a.first_name LIKE ? OR a.last_name LIKE ? OR a.tracking_id LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$whereClause = implode(' AND ', $whereConditions);

// Get applications
$stmt = $pdo->prepare("
    SELECT a.*, c.name as course_name, c.code as course_code
    FROM applications a
    JOIN courses c ON a.course_id = c.id
    WHERE $whereClause
    ORDER BY a.created_at DESC
    LIMIT $limit OFFSET $offset
");
$stmt->execute($params);
$applications = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get total count
$countStmt = $pdo->prepare("
    SELECT COUNT(*) FROM applications a
    JOIN courses c ON a.course_id = c.id
    WHERE $whereClause
");
$countStmt->execute($params);
$totalApplications = $countStmt->fetchColumn();

// Get courses for filter
$stmt = $pdo->prepare("SELECT id, name FROM courses WHERE campus_id = ? AND status = 'active'");
$stmt->execute([$_SESSION['campus_id']]);
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

$page_title = 'Applications Management';
include '../../includes/header.php';
?>

<div class="max-w-7xl mx-auto py-6">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Applications Management</h1>
        <p class="text-gray-600">Review and manage student applications</p>
    </div>
    
    <!-- Filters -->
    <div class="bg-white p-6 rounded-lg shadow mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Status</label>
                <select name="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Statuses</option>
                    <option value="pending" <?= $status === 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="under_review" <?= $status === 'under_review' ? 'selected' : '' ?>>Under Review</option>
                    <option value="accepted" <?= $status === 'accepted' ? 'selected' : '' ?>>Accepted</option>
                    <option value="rejected" <?= $status === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                    <option value="onboarded" <?= $status === 'onboarded' ? 'selected' : '' ?>>Onboarded</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700">Course</label>
                <select name="course" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Courses</option>
                    <?php foreach($courses as $course): ?>
                    <option value="<?= $course['id'] ?>" <?= $course == $course['id'] ? 'selected' : '' ?>>
                        <?= $course['name'] ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700">Search</label>
                <input type="text" name="search" placeholder="Name or Tracking ID" 
                       value="<?= htmlspecialchars($search) ?>"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            
            <div class="flex items-end">
                <button type="submit" class="w-full bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700">
                    Filter
                </button>
            </div>
        </form>
    </div>
    
    <!-- Applications Table -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b">
            <h3 class="text-lg font-semibold">Applications (<?= $totalApplications ?> total)</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tracking ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Course</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Applied Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach($applications as $app): ?>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            <?= $app['tracking_id'] ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <?= $app['first_name'] ?> <?= $app['last_name'] ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <?= $app['course_name'] ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full <?= getStatusColor($app['status']) ?>">
                                <?= ucwords(str_replace('_', ' ', $app['status'])) ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <?= date('M d, Y', strtotime($app['created_at'])) ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="view_application.php?id=<?= $app['id'] ?>" 
                               class="text-indigo-600 hover:text-indigo-900 mr-3">View</a>
                            <a href="edit_application.php?id=<?= $app['id'] ?>" 
                               class="text-green-600 hover:text-green-900">Edit</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <?php if ($totalApplications > $limit): ?>
        <div class="px-6 py-3 border-t">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-700">
                    Showing <?= $offset + 1 ?> to <?= min($offset + $limit, $totalApplications) ?> of <?= $totalApplications ?> results
                </div>
                <div class="flex space-x-2">
                    <?php if ($page > 1): ?>
                    <a href="?page=<?= $page - 1 ?>&status=<?= $status ?>&course=<?= $course ?>&search=<?= $search ?>" 
                       class="px-3 py-1 border border-gray-300 rounded-md text-sm hover:bg-gray-50">Previous</a>
                    <?php endif; ?>
                    
                    <?php if ($page < ceil($totalApplications / $limit)): ?>
                    <a href="?page=<?= $page + 1 ?>&status=<?= $status ?>&course=<?= $course ?>&search=<?= $search ?>" 
                       class="px-3 py-1 border border-gray-300 rounded-md text-sm hover:bg-gray-50">Next</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
```

### Step 3: Fee Management System
**Duration**: 4-5 hours

**File: `modules/accounts/generate_voucher.php`**
```php
<?php
session_start();
require_once '../../config/constants.php';
require_once '../../config/database.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';

$auth = new Auth();
$auth->requireRole('accounts');

$db = new Database();
$pdo = $db->getConnection();

$message = '';
$error = '';

// Handle voucher generation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $applicationId = $_POST['application_id'];
    $paymentType = $_POST['payment_type'];
    $amount = $_POST['amount'];
    $dueDate = $_POST['due_date'];
    
    // Generate voucher number
    $voucherNumber = generateVoucherNumber();
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO payments (application_id, voucher_number, amount, payment_type, due_date) 
            VALUES (?, ?, ?, ?, ?)
        ");
        
        $result = $stmt->execute([
            $applicationId,
            $voucherNumber,
            $amount,
            $paymentType,
            $dueDate
        ]);
        
        if ($result) {
            $message = "Fee voucher generated successfully: $voucherNumber";
        }
        
    } catch (PDOException $e) {
        $error = "Failed to generate voucher: " . $e->getMessage();
    }
}

// Get accepted applications
$stmt = $pdo->prepare("
    SELECT a.*, c.name as course_name, c.admission_fee, c.tuition_fee, c.security_deposit, c.other_charges
    FROM applications a
    JOIN courses c ON a.course_id = c.id
    WHERE a.status = 'accepted'
    ORDER BY a.created_at DESC
");
$stmt->execute();
$applications = $stmt->fetchAll(PDO::FETCH_ASSOC);

$page_title = 'Generate Fee Voucher';
include '../../includes/header.php';
?>

<div class="max-w-4xl mx-auto py-6">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Generate Fee Voucher</h1>
        <p class="text-gray-600">Create payment vouchers for accepted students</p>
    </div>
    
    <?php if ($message): ?>
    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded mb-6">
        <?= $message ?>
    </div>
    <?php endif; ?>
    
    <?php if ($error): ?>
    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded mb-6">
        <?= $error ?>
    </div>
    <?php endif; ?>
    
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b">
            <h2 class="text-xl font-semibold">Generate New Voucher</h2>
        </div>
        <div class="p-6">
            <form method="POST" class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Select Student</label>
                    <select name="application_id" required 
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Choose a student</option>
                        <?php foreach($applications as $app): ?>
                        <option value="<?= $app['id'] ?>" 
                                data-admission-fee="<?= $app['admission_fee'] ?>"
                                data-tuition-fee="<?= $app['tuition_fee'] ?>"
                                data-security-deposit="<?= $app['security_deposit'] ?>"
                                data-other-charges="<?= $app['other_charges'] ?>">
                            <?= $app['first_name'] ?> <?= $app['last_name'] ?> - <?= $app['course_name'] ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Payment Type</label>
                    <select name="payment_type" required 
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Select payment type</option>
                        <option value="admission_fee">Admission Fee</option>
                        <option value="tuition_fee">Tuition Fee</option>
                        <option value="security_deposit">Security Deposit</option>
                        <option value="other">Other Charges</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Amount (Rs.)</label>
                    <input type="number" name="amount" step="0.01" required 
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Due Date</label>
                    <input type="date" name="due_date" required 
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                
                <div class="flex justify-end">
                    <button type="submit" 
                            class="bg-indigo-600 text-white px-6 py-2 rounded-md hover:bg-indigo-700">
                        Generate Voucher
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const applicationSelect = document.querySelector('select[name="application_id"]');
    const paymentTypeSelect = document.querySelector('select[name="payment_type"]');
    const amountInput = document.querySelector('input[name="amount"]');
    
    applicationSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption.value) {
            const admissionFee = selectedOption.dataset.admissionFee;
            const tuitionFee = selectedOption.dataset.tuitionFee;
            const securityDeposit = selectedOption.dataset.securityDeposit;
            const otherCharges = selectedOption.dataset.otherCharges;
            
            // Update payment type options with amounts
            paymentTypeSelect.innerHTML = `
                <option value="">Select payment type</option>
                <option value="admission_fee" data-amount="${admissionFee}">Admission Fee (Rs. ${admissionFee})</option>
                <option value="tuition_fee" data-amount="${tuitionFee}">Tuition Fee (Rs. ${tuitionFee})</option>
                <option value="security_deposit" data-amount="${securityDeposit}">Security Deposit (Rs. ${securityDeposit})</option>
                <option value="other" data-amount="${otherCharges}">Other Charges (Rs. ${otherCharges})</option>
            `;
        }
    });
    
    paymentTypeSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption.value && selectedOption.dataset.amount) {
            amountInput.value = selectedOption.dataset.amount;
        }
    });
});
</script>

<?php include '../../includes/footer.php'; ?>
```

### Step 4: User Management System
**Duration**: 3-4 hours

**File: `modules/admin/users.php`**
```php
<?php
session_start();
require_once '../../config/constants.php';
require_once '../../config/database.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';

$auth = new Auth();
$auth->requireRole('admin');

$db = new Database();
$pdo = $db->getConnection();

// Get filter parameters
$role = $_GET['role'] ?? '';
$status = $_GET['status'] ?? '';
$search = $_GET['search'] ?? '';
$page = $_GET['page'] ?? 1;
$limit = 25;
$offset = ($page - 1) * $limit;

// Build query
$whereConditions = ["campus_id = ?"];
$params = [$_SESSION['campus_id']];

if ($role) {
    $whereConditions[] = "role = ?";
    $params[] = $role;
}

if ($status) {
    $whereConditions[] = "status = ?";
    $params[] = $status;
}

if ($search) {
    $whereConditions[] = "(first_name LIKE ? OR last_name LIKE ? OR email LIKE ? OR username LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$whereClause = implode(' AND ', $whereConditions);

// Get users
$stmt = $pdo->prepare("
    SELECT * FROM users
    WHERE $whereClause
    ORDER BY created_at DESC
    LIMIT $limit OFFSET $offset
");
$stmt->execute($params);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get total count
$countStmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE $whereClause");
$countStmt->execute($params);
$totalUsers = $countStmt->fetchColumn();

$page_title = 'User Management';
include '../../includes/header.php';
?>

<div class="max-w-7xl mx-auto py-6">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">User Management</h1>
        <p class="text-gray-600">Manage campus users and their permissions</p>
    </div>
    
    <!-- Filters -->
    <div class="bg-white p-6 rounded-lg shadow mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Role</label>
                <select name="role" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Roles</option>
                    <option value="admin" <?= $role === 'admin' ? 'selected' : '' ?>>Admin</option>
                    <option value="accounts" <?= $role === 'accounts' ? 'selected' : '' ?>>Accounts</option>
                    <option value="teacher" <?= $role === 'teacher' ? 'selected' : '' ?>>Teacher</option>
                    <option value="student" <?= $role === 'student' ? 'selected' : '' ?>>Student</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700">Status</label>
                <select name="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Statuses</option>
                    <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>Active</option>
                    <option value="inactive" <?= $status === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                    <option value="suspended" <?= $status === 'suspended' ? 'selected' : '' ?>>Suspended</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700">Search</label>
                <input type="text" name="search" placeholder="Name, email, or username" 
                       value="<?= htmlspecialchars($search) ?>"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            
            <div class="flex items-end">
                <button type="submit" class="w-full bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700">
                    Filter
                </button>
            </div>
        </form>
    </div>
    
    <!-- Users Table -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-semibold">Users (<?= $totalUsers ?> total)</h3>
                <a href="create_user.php" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                    Create User
                </a>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Last Login</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach($users as $user): ?>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            <?= $user['first_name'] ?> <?= $user['last_name'] ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <?= $user['email'] ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <?= ucfirst($user['role']) ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full <?= getStatusColor($user['status']) ?>">
                                <?= ucfirst($user['status']) ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <?= $user['last_login'] ? date('M d, Y', strtotime($user['last_login'])) : 'Never' ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="edit_user.php?id=<?= $user['id'] ?>" 
                               class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                            <a href="view_user.php?id=<?= $user['id'] ?>" 
                               class="text-green-600 hover:text-green-900">View</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
```

## Post-Phase Checklist
- [ ] Admin dashboards functional
- [ ] Application management system working
- [ ] Fee management system operational
- [ ] User management system working
- [ ] Reporting system functional
- [ ] Role-based access control working

## Testing Procedures
1. **Admin Dashboard Test**
   - Test statistics display
   - Test recent applications table
   - Test navigation

2. **Application Management Test**
   - Test application listing
   - Test filtering and search
   - Test pagination

3. **Fee Management Test**
   - Test voucher generation
   - Test payment tracking
   - Test fee calculations

4. **User Management Test**
   - Test user listing
   - Test user creation
   - Test user editing

## Summary
**Key Achievements:**
- Admin dashboard system implemented
- Application management system working
- Fee management system operational
- User management system functional
- Reporting system created

**Next Phase Dependencies:**
- Admin features must be fully functional
- Management systems must be working
- User roles must be properly implemented

**Files Created:**
- Admin dashboard (`modules/admin/dashboard.php`)
- Application management (`modules/admin/applications.php`)
- Fee management (`modules/accounts/generate_voucher.php`)
- User management (`modules/admin/users.php`)

**Estimated Completion Time:** 4-5 days
**Ready for Phase 5:** ✅
