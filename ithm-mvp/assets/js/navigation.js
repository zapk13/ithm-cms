// Navigation and routing for ITHM MVP
class NavigationManager {
    constructor() {
        this.currentUser = this.getCurrentUser();
        this.init();
    }

    init() {
        // Only initialize navigation features on dashboard pages
        const currentPath = window.location.pathname;
        const isDashboardPage = currentPath.includes('/dashboard.html') || 
                               currentPath.includes('/admin/') || 
                               currentPath.includes('/super-admin/') || 
                               currentPath.includes('/accounts/') || 
                               currentPath.includes('/teacher/') || 
                               currentPath.includes('/student/');
        
        if (isDashboardPage) {
            this.setupNavigation();
            this.setupUserInfo();
            this.setupLogout();
            this.setupNotificationSystem();
            this.setupRealTimeUpdates();
            this.setupSearchClickOutside();
        } else {
            // For non-dashboard pages, only setup basic user info
            this.setupUserInfo();
        }
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

    logout() {
        localStorage.removeItem('demoUser');
        // Check if we're in a subdirectory and adjust path accordingly
        const currentPath = window.location.pathname;
        if (currentPath.includes('/super-admin/') || currentPath.includes('/admin/') ||
            currentPath.includes('/accounts/') || currentPath.includes('/teacher/') ||
            currentPath.includes('/student/') || currentPath.includes('/auth/')) {
            // We're in a subdirectory, go up one level
            window.location.href = '../index.html';
        } else {
            // We're in the root directory
            window.location.href = 'index.html';
        }
    }

    getRoleDisplayName(role) {
        const roleNames = {
            'super_admin': 'Super Admin',
            'admin': 'Campus Admin',
            'accounts': 'Accounts Officer',
            'teacher': 'Teacher',
            'student': 'Student'
        };
        return roleNames[role] || role;
    }

    // Notification System
    setupNotificationSystem() {
        this.notifications = [];
        this.notificationContainer = null;
        this.createNotificationContainer();
        this.setupNotificationBell();
    }

    createNotificationContainer() {
        // Create notification container if it doesn't exist
        if (!document.getElementById('notification-container')) {
            const container = document.createElement('div');
            container.id = 'notification-container';
            container.className = 'fixed top-4 right-4 z-50 space-y-2 max-w-sm w-full';
            
            // Safety check to ensure document.body exists
            if (document.body) {
                document.body.appendChild(container);
                this.notificationContainer = container;
            } else {
                console.warn('Document body not available for notification container');
                this.notificationContainer = null;
            }
        } else {
            // Use existing container
            this.notificationContainer = document.getElementById('notification-container');
        }
    }

    setupNotificationBell() {
        // Add notification bell and search to header if user is logged in
        if (this.currentUser) {
            const header = document.querySelector('header .flex.items-center.space-x-4');
            if (header && !document.getElementById('notification-bell')) {
                // Add search bar
                const searchContainer = document.createElement('div');
                searchContainer.id = 'global-search';
                searchContainer.className = 'relative mr-4';
                searchContainer.innerHTML = `
                    <div class="relative">
                        <input type="text" id="globalSearchInput" placeholder="Search across all modules..." 
                               class="w-64 px-4 py-2 pl-10 pr-4 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                               onkeyup="navigationManager.handleGlobalSearch(event)">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <div id="searchResults" class="absolute top-full left-0 right-0 mt-1 bg-white border border-gray-200 rounded-lg shadow-lg z-50 hidden max-h-96 overflow-y-auto">
                        </div>
                    </div>
                `;
                header.insertBefore(searchContainer, header.firstChild);

                // Add notification bell
                const bellContainer = document.createElement('div');
                bellContainer.id = 'notification-bell';
                bellContainer.className = 'relative';
                bellContainer.innerHTML = `
                    <button onclick="navigationManager.toggleNotificationPanel()" class="relative p-2 text-gray-500 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 rounded-full">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4.5 19.5a1.5 1.5 0 01-1.5-1.5V6a1.5 1.5 0 011.5-1.5h15A1.5 1.5 0 0121 6v12a1.5 1.5 0 01-1.5 1.5h-15z"></path>
                        </svg>
                        <span id="notification-count" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center hidden">0</span>
                    </button>
                `;
                header.appendChild(bellContainer);
            }
        }
    }

    showNotification(message, type = 'info', duration = 5000) {
        // Initialize notifications array if it doesn't exist
        if (!this.notifications) {
            this.notifications = [];
        }

        // Create notification container if it doesn't exist
        if (!this.notificationContainer) {
            this.createNotificationContainer();
        }

        const notification = {
            id: Date.now(),
            message,
            type,
            timestamp: new Date(),
            read: false
        };

        this.notifications.unshift(notification);
        
        // Only render notification if container exists
        if (this.notificationContainer) {
            this.renderNotification(notification);
            this.updateNotificationCount();
            this.saveNotifications();

            // Auto remove after duration
            setTimeout(() => {
                this.removeNotification(notification.id);
            }, duration);
        } else {
            // Fallback: show simple alert if notification system not available
            alert(message);
        }
    }

    renderNotification(notification) {
        // Check if notification container exists
        if (!this.notificationContainer) {
            console.warn('Notification container not available');
            return;
        }

        const notificationElement = document.createElement('div');
        notificationElement.id = `notification-${notification.id}`;
        notificationElement.className = `bg-white shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden transform transition-all duration-300 ease-in-out`;

        let bgColor = '';
        let iconSvg = '';
        let iconColor = '';

        switch(notification.type) {
            case 'success':
                bgColor = 'bg-green-50 border-green-200';
                iconColor = 'text-green-400';
                iconSvg = '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>';
                break;
            case 'error':
                bgColor = 'bg-red-50 border-red-200';
                iconColor = 'text-red-400';
                iconSvg = '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L11.414 10l1.293-1.293a1 1 0 001.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>';
                break;
            case 'warning':
                bgColor = 'bg-yellow-50 border-yellow-200';
                iconColor = 'text-yellow-400';
                iconSvg = '<path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>';
                break;
            case 'info':
            default:
                bgColor = 'bg-blue-50 border-blue-200';
                iconColor = 'text-blue-400';
                iconSvg = '<path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>';
                break;
        }

        notificationElement.innerHTML = `
            <div class="p-4 ${bgColor} border-l-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 ${iconColor}" fill="currentColor" viewBox="0 0 20 20">
                            ${iconSvg}
                        </svg>
                    </div>
                    <div class="ml-3 flex-1">
                        <p class="text-sm font-medium text-gray-900">${notification.message}</p>
                        <p class="text-xs text-gray-500 mt-1">${this.formatTime(notification.timestamp)}</p>
                    </div>
                    <div class="ml-auto pl-3">
                        <div class="-mx-1.5 -my-1.5">
                            <button onclick="navigationManager.removeNotification(${notification.id})" class="inline-flex rounded-md p-1.5 text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-50 focus:ring-gray-600">
                                <span class="sr-only">Dismiss</span>
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        this.notificationContainer.appendChild(notificationElement);
    }

    removeNotification(id) {
        const element = document.getElementById(`notification-${id}`);
        if (element) {
            element.remove();
        }

        this.notifications = this.notifications.filter(n => n.id !== id);
        this.updateNotificationCount();
        this.saveNotifications();
    }

    updateNotificationCount() {
        const unreadCount = this.notifications.filter(n => !n.read).length;
        const countElement = document.getElementById('notification-count');
        if (countElement) {
            if (unreadCount > 0) {
                countElement.textContent = unreadCount;
                countElement.classList.remove('hidden');
            } else {
                countElement.classList.add('hidden');
            }
        }
    }

    toggleNotificationPanel() {
        const panel = document.getElementById('notification-panel');
        if (panel) {
            panel.remove();
        } else {
            this.showNotificationPanel();
        }
    }

    showNotificationPanel() {
        const panel = document.createElement('div');
        panel.id = 'notification-panel';
        panel.className = 'fixed top-16 right-4 w-80 bg-white shadow-xl rounded-lg border border-gray-200 z-50 max-h-96 overflow-y-auto';

        const unreadNotifications = this.notifications.filter(n => !n.read);
        const recentNotifications = this.notifications.slice(0, 10);

        panel.innerHTML = `
            <div class="p-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Notifications</h3>
                    <button onclick="navigationManager.clearAllNotifications()" class="text-sm text-blue-600 hover:text-blue-800">Clear All</button>
                </div>
            </div>
            <div class="divide-y divide-gray-200">
                ${recentNotifications.length > 0 ? recentNotifications.map(notification => `
                    <div class="p-4 hover:bg-gray-50 cursor-pointer ${!notification.read ? 'bg-blue-50' : ''}" onclick="navigationManager.markAsRead(${notification.id})">
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0">
                                <div class="w-2 h-2 ${!notification.read ? 'bg-blue-500' : 'bg-gray-300'} rounded-full mt-2"></div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-gray-900">${notification.message}</p>
                                <p class="text-xs text-gray-500 mt-1">${this.formatTime(notification.timestamp)}</p>
                            </div>
                        </div>
                    </div>
                `).join('') : `
                    <div class="p-8 text-center text-gray-500">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4.5 19.5a1.5 1.5 0 01-1.5-1.5V6a1.5 1.5 0 011.5-1.5h15A1.5 1.5 0 0121 6v12a1.5 1.5 0 01-1.5 1.5h-15z"></path>
                        </svg>
                        <p class="mt-2 text-sm">No notifications</p>
                    </div>
                `}
            </div>
        `;

        document.body.appendChild(panel);
    }

    markAsRead(id) {
        const notification = this.notifications.find(n => n.id === id);
        if (notification) {
            notification.read = true;
            this.updateNotificationCount();
            this.saveNotifications();
        }
    }

    clearAllNotifications() {
        this.notifications = [];
        this.updateNotificationCount();
        this.saveNotifications();
        document.getElementById('notification-panel').remove();
    }

    formatTime(timestamp) {
        const now = new Date();
        const diff = now - timestamp;
        const minutes = Math.floor(diff / 60000);
        const hours = Math.floor(diff / 3600000);
        const days = Math.floor(diff / 86400000);

        if (minutes < 1) return 'Just now';
        if (minutes < 60) return `${minutes}m ago`;
        if (hours < 24) return `${hours}h ago`;
        return `${days}d ago`;
    }

    saveNotifications() {
        localStorage.setItem('ithm_notifications', JSON.stringify(this.notifications));
    }

    loadNotifications() {
        const saved = localStorage.getItem('ithm_notifications');
        if (saved) {
            this.notifications = JSON.parse(saved);
            this.updateNotificationCount();
        }
    }

    // Real-time updates simulation
    setupRealTimeUpdates() {
        this.loadNotifications();

        // Simulate real-time notifications
        setInterval(() => {
            this.simulateRealTimeNotification();
        }, 30000); // Every 30 seconds
        
        // Add demo scenario guidance
        this.setupDemoGuidance();
    }
    
    setupDemoGuidance() {
        // Add demo guidance for first-time users
        const isFirstVisit = !localStorage.getItem('demoVisited');
        if (isFirstVisit) {
            setTimeout(() => {
                this.showDemoWelcome();
                localStorage.setItem('demoVisited', 'true');
            }, 2000);
        }
    }
    
    showDemoWelcome() {
        const welcomeModal = document.createElement('div');
        welcomeModal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
        welcomeModal.innerHTML = `
            <div class="bg-white rounded-2xl p-8 max-w-md mx-4 shadow-2xl">
                <div class="text-center mb-6">
                    <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-indigo-600 text-2xl">🎉</span>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Welcome to ITHM CMS Demo!</h3>
                    <p class="text-gray-600">Experience the complete college management system with realistic data and workflows.</p>
                </div>
                <div class="space-y-3 mb-6">
                    <div class="flex items-center space-x-3">
                        <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center">
                            <span class="text-green-600 text-xs">✓</span>
                        </div>
                        <span class="text-sm text-gray-700">Interactive dashboards with real-time data</span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center">
                            <span class="text-green-600 text-xs">✓</span>
                        </div>
                        <span class="text-sm text-gray-700">Complete application and payment workflows</span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center">
                            <span class="text-green-600 text-xs">✓</span>
                        </div>
                        <span class="text-sm text-gray-700">PDF generation and document management</span>
                    </div>
                </div>
                <div class="flex space-x-3">
                    <button onclick="this.parentElement.parentElement.parentElement.remove()" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                        Skip Tour
                    </button>
                    <button onclick="this.parentElement.parentElement.parentElement.remove(); window.demoScenarios && window.demoScenarios.showDemoTour()" class="flex-1 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                        Start Tour
                    </button>
                </div>
            </div>
        `;
        document.body.appendChild(welcomeModal);
    }

    simulateRealTimeNotification() {
        const notifications = [
            { message: 'New application submitted for review', type: 'info' },
            { message: 'Payment received from student', type: 'success' },
            { message: 'Document verification completed', type: 'success' },
            { message: 'System maintenance scheduled for tonight', type: 'warning' },
            { message: 'New student enrolled in program', type: 'info' },
            { message: 'Fee payment deadline approaching', type: 'warning' }
        ];

        const randomNotification = notifications[Math.floor(Math.random() * notifications.length)];
        this.showNotification(randomNotification.message, randomNotification.type);
    }

    // Make notification methods globally available
    showSystemNotification(message, type = 'info') {
        this.showNotification(message, type);
    }

    // Global Search Functionality
    handleGlobalSearch(event) {
        const query = event.target.value.trim();
        const resultsContainer = document.getElementById('searchResults');
        
        if (query.length < 2) {
            resultsContainer.classList.add('hidden');
            return;
        }
        
        // Simulate search delay
        clearTimeout(this.searchTimeout);
        this.searchTimeout = setTimeout(() => {
            this.performGlobalSearch(query);
        }, 300);
    }

    performGlobalSearch(query) {
        const results = this.searchAllModules(query);
        this.displaySearchResults(results);
    }

    searchAllModules(query) {
        const results = [];
        const searchTerm = query.toLowerCase();
        
        // Search users
        if (typeof demoData !== 'undefined' && demoData.users) {
            demoData.users.forEach(user => {
                if (user.first_name.toLowerCase().includes(searchTerm) ||
                    user.last_name.toLowerCase().includes(searchTerm) ||
                    user.email.toLowerCase().includes(searchTerm)) {
                    results.push({
                        type: 'user',
                        title: `${user.first_name} ${user.last_name}`,
                        subtitle: user.email,
                        description: `${this.getRoleDisplayName(user.role)} - ${user.campus_id || 'All Campuses'}`,
                        url: this.getUserUrl(user.role),
                        icon: '👤'
                    });
                }
            });
        }
        
        // Search applications
        if (typeof demoData !== 'undefined' && demoData.applications) {
            demoData.applications.forEach(app => {
                if (app.student_name.toLowerCase().includes(searchTerm) ||
                    app.program.toLowerCase().includes(searchTerm) ||
                    app.campus.toLowerCase().includes(searchTerm)) {
                    results.push({
                        type: 'application',
                        title: `Application - ${app.student_name}`,
                        subtitle: app.program,
                        description: `${app.campus} - ${app.status}`,
                        url: this.getApplicationUrl(),
                        icon: '📋'
                    });
                }
            });
        }
        
        // Search payments
        if (typeof demoData !== 'undefined' && demoData.payments) {
            demoData.payments.forEach(payment => {
                if (payment.student_name.toLowerCase().includes(searchTerm) ||
                    payment.amount.toString().includes(searchTerm) ||
                    payment.method.toLowerCase().includes(searchTerm)) {
                    results.push({
                        type: 'payment',
                        title: `Payment - ${payment.student_name}`,
                        subtitle: `PKR ${payment.amount.toLocaleString()}`,
                        description: `${payment.method} - ${payment.status}`,
                        url: this.getPaymentUrl(),
                        icon: '💰'
                    });
                }
            });
        }
        
        // Search courses
        if (typeof demoData !== 'undefined' && demoData.courses) {
            demoData.courses.forEach(course => {
                if (course.name.toLowerCase().includes(searchTerm) ||
                    course.code.toLowerCase().includes(searchTerm) ||
                    course.campus.toLowerCase().includes(searchTerm)) {
                    results.push({
                        type: 'course',
                        title: course.name,
                        subtitle: course.code,
                        description: `${course.campus} - ${course.duration}`,
                        url: this.getCourseUrl(),
                        icon: '📚'
                    });
                }
            });
        }
        
        // Search campuses
        if (typeof demoData !== 'undefined' && demoData.campuses) {
            demoData.campuses.forEach(campus => {
                if (campus.name.toLowerCase().includes(searchTerm) ||
                    campus.location.toLowerCase().includes(searchTerm)) {
                    results.push({
                        type: 'campus',
                        title: campus.name,
                        subtitle: campus.location,
                        description: `${campus.students} students - ${campus.programs} programs`,
                        url: this.getCampusUrl(),
                        icon: '🏢'
                    });
                }
            });
        }
        
        return results.slice(0, 10); // Limit to 10 results
    }

    displaySearchResults(results) {
        const resultsContainer = document.getElementById('searchResults');
        
        if (results.length === 0) {
            resultsContainer.innerHTML = `
                <div class="p-4 text-center text-gray-500">
                    <svg class="mx-auto h-8 w-8 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <p class="text-sm">No results found</p>
                </div>
            `;
        } else {
            resultsContainer.innerHTML = results.map(result => `
                <div class="p-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0" onclick="navigationManager.navigateToSearchResult('${result.url}')">
                    <div class="flex items-start space-x-3">
                        <div class="text-lg">${result.icon}</div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">${result.title}</p>
                            <p class="text-xs text-gray-500 truncate">${result.subtitle}</p>
                            <p class="text-xs text-gray-400 truncate">${result.description}</p>
                        </div>
                        <div class="text-xs text-gray-400 capitalize">${result.type}</div>
                    </div>
                </div>
            `).join('');
        }
        
        resultsContainer.classList.remove('hidden');
    }

    navigateToSearchResult(url) {
        if (url) {
            window.location.href = url;
        }
        document.getElementById('searchResults').classList.add('hidden');
        document.getElementById('globalSearchInput').value = '';
    }

    getUserUrl(role) {
        const roleUrls = {
            'super_admin': 'super-admin/dashboard.html',
            'admin': 'admin/dashboard.html',
            'accounts': 'accounts/dashboard.html',
            'teacher': 'teacher/dashboard.html',
            'student': 'student/dashboard.html'
        };
        return roleUrls[role] || 'admin/user-management.html';
    }

    getApplicationUrl() {
        return 'super-admin/application-detail.html';
    }

    getPaymentUrl() {
        return 'accounts/payment-detail.html';
    }

    getCourseUrl() {
        return 'admin/dashboard.html';
    }

    getCampusUrl() {
        return 'admin/dashboard.html';
    }

    // Close search results when clicking outside
    setupSearchClickOutside() {
        document.addEventListener('click', (event) => {
            const searchContainer = document.getElementById('global-search');
            const resultsContainer = document.getElementById('searchResults');
            
            if (searchContainer && !searchContainer.contains(event.target)) {
                resultsContainer.classList.add('hidden');
            }
        });
    }
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
            redirect: '../super-admin/dashboard.html' 
        },
        admin: { 
            email: 'admin.lahore@ithm.edu.pk', 
            password: 'demo123', 
            first_name: 'Prof. Ahmed',
            last_name: 'Hassan',
            role: 'admin',
            redirect: '../admin/dashboard.html' 
        },
        accounts: { 
            email: 'accounts.lahore@ithm.edu.pk', 
            password: 'demo123', 
            first_name: 'Ms. Fatima',
            last_name: 'Sheikh',
            role: 'accounts',
            redirect: '../accounts/dashboard.html' 
        },
        teacher: { 
            email: 'teacher.lahore@ithm.edu.pk', 
            password: 'demo123', 
            first_name: 'Mr. Usman',
            last_name: 'Malik',
            role: 'teacher',
            redirect: '../teacher/dashboard.html' 
        },
        student: { 
            email: 'student@ithm.edu.pk', 
            password: 'demo123', 
            first_name: 'Ahmed',
            last_name: 'Khan',
            role: 'student',
            redirect: '../student/dashboard.html' 
        }
    };
    
    const creds = credentials[role];
    if (creds) {
        // Store demo login info
        localStorage.setItem('demoUser', JSON.stringify(creds));
        
        // Show loading notification if available
        if (typeof showNotification === 'function') {
            showNotification('Logging in...', 'info');
        } else {
            console.log('Logging in as', role, '...');
        }
        
        // Redirect to appropriate dashboard
        setTimeout(() => {
            try {
                console.log('Redirecting to:', creds.redirect);
                window.location.href = creds.redirect;
            } catch (error) {
                console.error('Redirect error:', error);
                if (typeof showNotification === 'function') {
                    showNotification('Redirect failed. Please try again.', 'error');
                } else {
                    alert('Redirect failed. Please try again.');
                }
            }
        }, 500);
    } else {
        console.error('Invalid role:', role);
    }
}

// Make demoLogin available globally
window.demoLogin = demoLogin;

// Global notification functions
window.showNotification = function(message, type = 'info') {
    if (window.navigationManager) {
        window.navigationManager.showNotification(message, type);
    }
};

window.showSystemNotification = function(message, type = 'info') {
    if (window.navigationManager) {
        window.navigationManager.showSystemNotification(message, type);
    }
};

// Initialize navigation manager
window.navigationManager = new NavigationManager();
