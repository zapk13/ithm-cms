// Navigation and routing for ITHM MVP
class NavigationManager {
    constructor() {
        this.currentUser = this.getCurrentUser();
        this.init();
    }

    init() {
        this.setupNavigation();
        this.setupUserInfo();
        this.setupLogout();
    }

    getCurrentUser() {
        const demoUser = localStorage.getItem('demoUser');
        return demoUser ? JSON.parse(demoUser) : null;
    }

    setupNavigation() {
        // Add active class to current page
        const currentPath = window.location.pathname;
        const navItems = document.querySelectorAll('.nav-item');
        
        navItems.forEach(item => {
            const href = item.getAttribute('href');
            if (href && currentPath.includes(href)) {
                item.classList.add('active');
            }
        });
    }

    setupUserInfo() {
        if (this.currentUser) {
            // Update user info in header
            const userInfoElements = document.querySelectorAll('.user-info');
            userInfoElements.forEach(element => {
                element.textContent = `${this.currentUser.first_name} ${this.currentUser.last_name}`;
            });

            // Update user role
            const roleElements = document.querySelectorAll('.user-role');
            roleElements.forEach(element => {
                element.textContent = this.getRoleDisplayName(this.currentUser.role);
            });
        } else {
            // If no user is logged in, redirect to login page
            const currentPath = window.location.pathname;
            if (currentPath.includes('/super-admin/') || currentPath.includes('/admin/') || 
                currentPath.includes('/accounts/') || currentPath.includes('/teacher/') || 
                currentPath.includes('/student/')) {
                window.location.href = '../auth/login.html';
            }
        }
    }

    setupLogout() {
        const logoutButtons = document.querySelectorAll('.logout-btn');
        logoutButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                this.logout();
            });
        });
    }

    getRoleDisplayName(role) {
        const roleNames = {
            'super_admin': 'Super Administrator',
            'admin': 'Campus Administrator',
            'accounts': 'Accounts Officer',
            'teacher': 'Professor',
            'student': 'Student'
        };
        return roleNames[role] || role;
    }

    logout() {
        localStorage.removeItem('demoUser');
        // Get current base URL and construct proper redirect path
        const currentUrl = window.location.href;
        const baseUrl = currentUrl.substring(0, currentUrl.lastIndexOf('/') + 1);
        
        // Check if we're in a subdirectory and adjust path accordingly
        const currentPath = window.location.pathname;
        if (currentPath.includes('/super-admin/') || currentPath.includes('/admin/') ||
            currentPath.includes('/accounts/') || currentPath.includes('/teacher/') ||
            currentPath.includes('/student/') || currentPath.includes('/auth/')) {
            // We're in a subdirectory, go up one level
            const parentUrl = baseUrl.substring(0, baseUrl.lastIndexOf('/', baseUrl.length - 2) + 1);
            window.location.href = parentUrl + 'index.html';
        } else {
            // We're in the root directory
            window.location.href = baseUrl + 'index.html';
        }
    }

    // Navigation helper methods
    navigateTo(url) {
        window.location.href = url;
    }

    goBack() {
        window.history.back();
    }

    // Show loading state
    showLoading(element) {
        if (element) {
            element.innerHTML = '<div class="loading-spinner"></div>';
        }
    }

    // Hide loading state
    hideLoading(element, content) {
        if (element) {
            element.innerHTML = content;
        }
    }

    // Show notification
    showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ${this.getNotificationClass(type)}`;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }

    getNotificationClass(type) {
        const classes = {
            success: 'bg-green-100 text-green-800 border border-green-200',
            error: 'bg-red-100 text-red-800 border border-red-200',
            warning: 'bg-yellow-100 text-yellow-800 border border-yellow-200',
            info: 'bg-blue-100 text-blue-800 border border-blue-200'
        };
        return classes[type] || classes.info;
    }

    // Form validation helpers
    validateForm(form) {
        const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
        let isValid = true;
        
        inputs.forEach(input => {
            if (!input.value.trim()) {
                this.showFieldError(input, 'This field is required');
                isValid = false;
            } else {
                this.clearFieldError(input);
            }
        });
        
        return isValid;
    }

    showFieldError(input, message) {
        this.clearFieldError(input);
        
        const errorDiv = document.createElement('div');
        errorDiv.className = 'form-error';
        errorDiv.textContent = message;
        
        input.parentNode.appendChild(errorDiv);
        input.classList.add('border-red-500');
    }

    clearFieldError(input) {
        const errorDiv = input.parentNode.querySelector('.form-error');
        if (errorDiv) {
            errorDiv.remove();
        }
        input.classList.remove('border-red-500');
    }

    // Table helpers
    setupDataTable(tableId) {
        const table = document.getElementById(tableId);
        if (!table) return;

        // Add sorting functionality
        const headers = table.querySelectorAll('th[data-sort]');
        headers.forEach(header => {
            header.style.cursor = 'pointer';
            header.addEventListener('click', () => {
                this.sortTable(table, header.dataset.sort);
            });
        });
    }

    sortTable(table, column) {
        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));
        
        rows.sort((a, b) => {
            const aVal = a.querySelector(`td[data-sort="${column}"]`)?.textContent || '';
            const bVal = b.querySelector(`td[data-sort="${column}"]`)?.textContent || '';
            return aVal.localeCompare(bVal);
        });
        
        rows.forEach(row => tbody.appendChild(row));
    }

    // Modal helpers
    showModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
    }

    hideModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    }

    // Search functionality
    setupSearch(inputId, tableId) {
        const searchInput = document.getElementById(inputId);
        const table = document.getElementById(tableId);
        
        if (searchInput && table) {
            searchInput.addEventListener('input', (e) => {
                this.filterTable(table, e.target.value);
            });
        }
    }

    filterTable(table, searchTerm) {
        const rows = table.querySelectorAll('tbody tr');
        const term = searchTerm.toLowerCase();
        
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(term) ? '' : 'none';
        });
    }

    // Pagination helpers
    setupPagination(containerId, currentPage, totalPages, onPageChange) {
        const container = document.getElementById(containerId);
        if (!container) return;

        let paginationHTML = '<div class="flex items-center justify-between">';
        
        // Previous button
        if (currentPage > 1) {
            paginationHTML += `<button onclick="navigationManager.goToPage(${currentPage - 1})" class="px-3 py-1 border border-gray-300 rounded-md text-sm hover:bg-gray-50">Previous</button>`;
        }
        
        // Page numbers
        for (let i = 1; i <= totalPages; i++) {
            const activeClass = i === currentPage ? 'bg-indigo-600 text-white' : 'border-gray-300 hover:bg-gray-50';
            paginationHTML += `<button onclick="navigationManager.goToPage(${i})" class="px-3 py-1 border rounded-md text-sm ${activeClass}">${i}</button>`;
        }
        
        // Next button
        if (currentPage < totalPages) {
            paginationHTML += `<button onclick="navigationManager.goToPage(${currentPage + 1})" class="px-3 py-1 border border-gray-300 rounded-md text-sm hover:bg-gray-50">Next</button>`;
        }
        
        paginationHTML += '</div>';
        container.innerHTML = paginationHTML;
    }

    goToPage(page) {
        // This would typically make an API call or update the page
        console.log(`Navigating to page ${page}`);
    }
}

// Initialize navigation manager
const navigationManager = new NavigationManager();

// Global helper functions
function showNotification(message, type = 'info') {
    navigationManager.showNotification(message, type);
}

function validateForm(formId) {
    const form = document.getElementById(formId);
    return navigationManager.validateForm(form);
}

function showModal(modalId) {
    navigationManager.showModal(modalId);
}

function hideModal(modalId) {
    navigationManager.hideModal(modalId);
}

// Demo login function
function demoLogin(role) {
    const credentials = {
        super_admin: { 
            email: 'super@ithm.edu.pk', 
            password: 'demo123', 
            first_name: 'Dr. Muhammad',
            last_name: 'Ali Khan',
            role: 'super_admin',
            redirect: 'super-admin/dashboard.html' 
        },
        admin: { 
            email: 'admin.lahore@ithm.edu.pk', 
            password: 'demo123', 
            first_name: 'Prof. Ahmed',
            last_name: 'Hassan',
            role: 'admin',
            redirect: 'admin/dashboard.html' 
        },
        accounts: { 
            email: 'accounts.lahore@ithm.edu.pk', 
            password: 'demo123', 
            first_name: 'Ms. Fatima',
            last_name: 'Sheikh',
            role: 'accounts',
            redirect: 'accounts/dashboard.html' 
        },
        teacher: { 
            email: 'teacher.lahore@ithm.edu.pk', 
            password: 'demo123', 
            first_name: 'Mr. Usman',
            last_name: 'Malik',
            role: 'teacher',
            redirect: 'teacher/dashboard.html' 
        },
        student: { 
            email: 'student@ithm.edu.pk', 
            password: 'demo123', 
            first_name: 'Ahmed',
            last_name: 'Khan',
            role: 'student',
            redirect: 'student/dashboard.html' 
        }
    };
    
    const creds = credentials[role];
    if (creds) {
        // Store demo login info
        localStorage.setItem('demoUser', JSON.stringify(creds));
        // Show loading
        showNotification('Logging in...', 'info');
        
        // Get current base URL and construct proper redirect path
        const currentUrl = window.location.href;
        const baseUrl = currentUrl.substring(0, currentUrl.lastIndexOf('/') + 1);
        const redirectUrl = baseUrl + creds.redirect;
        
        console.log('Current URL:', currentUrl);
        console.log('Base URL:', baseUrl);
        console.log('Redirect URL:', redirectUrl);
        
        // Redirect to appropriate dashboard
        setTimeout(() => {
            try {
                window.location.href = redirectUrl;
            } catch (error) {
                console.error('Redirect error:', error);
                showNotification('Redirect failed. Please try again.', 'error');
                // Fallback: try direct path
                window.location.href = creds.redirect;
            }
        }, 500);
    }
}

// Make demoLogin available globally
window.demoLogin = demoLogin;
