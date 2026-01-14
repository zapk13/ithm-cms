<?php
/**
 * Notification System Tests
 */

namespace Tests;

use App\Models\Notification;
use App\Models\User;

class NotificationTest
{
    private Notification $notificationModel;
    private User $userModel;
    private ?int $testUserId = null;
    
    public function setUp(): void
    {
        $this->notificationModel = new Notification();
        $this->userModel = new User();
        
        $this->testUserId = $this->userModel->createUser([
            'name' => 'Notification Test User',
            'email' => 'notif_' . time() . '@test.com',
            'password' => 'test123',
            'role_id' => 4
        ]);
    }
    
    public function tearDown(): void
    {
        // Clean up notifications
        if ($this->testUserId) {
            $notifications = $this->notificationModel->getByUser($this->testUserId);
            foreach ($notifications as $notif) {
                $this->notificationModel->delete($notif['id']);
            }
            $this->userModel->delete($this->testUserId);
        }
    }
    
    public function testCanCreateNotification(): bool
    {
        $notifId = $this->notificationModel->notify(
            $this->testUserId,
            'Test Notification',
            'This is a test message',
            'system'
        );
        
        return TestRunner::assertGreaterThan(0, $notifId, 'Should create notification');
    }
    
    public function testNotificationDefaultIsUnread(): bool
    {
        $notifId = $this->notificationModel->notify(
            $this->testUserId,
            'Test Notification',
            'This is a test message',
            'system'
        );
        
        $notif = $this->notificationModel->find($notifId);
        
        return TestRunner::assertEquals(0, $notif['is_read'], 'Default should be unread');
    }
    
    public function testGetNotificationsByUser(): bool
    {
        $this->notificationModel->notify($this->testUserId, 'Test 1', 'Message 1', 'system');
        $this->notificationModel->notify($this->testUserId, 'Test 2', 'Message 2', 'system');
        
        $notifications = $this->notificationModel->getByUser($this->testUserId);
        
        return TestRunner::assertGreaterThan(1, count($notifications), 'Should get multiple notifications');
    }
    
    public function testCountUnreadNotifications(): bool
    {
        $this->notificationModel->notify($this->testUserId, 'Unread 1', 'Message', 'system');
        $this->notificationModel->notify($this->testUserId, 'Unread 2', 'Message', 'system');
        
        $count = $this->notificationModel->countUnread($this->testUserId);
        
        return TestRunner::assertGreaterThan(1, $count, 'Should count unread notifications');
    }
    
    public function testMarkNotificationAsRead(): bool
    {
        $notifId = $this->notificationModel->notify(
            $this->testUserId,
            'Read Test',
            'Message',
            'system'
        );
        
        $this->notificationModel->markAsRead($notifId);
        
        $notif = $this->notificationModel->find($notifId);
        
        return TestRunner::assertEquals(1, $notif['is_read'], 'Should be marked as read');
    }
    
    public function testMarkAllAsRead(): bool
    {
        $this->notificationModel->notify($this->testUserId, 'Test 1', 'Message', 'system');
        $this->notificationModel->notify($this->testUserId, 'Test 2', 'Message', 'system');
        
        $this->notificationModel->markAllAsRead($this->testUserId);
        
        $unreadCount = $this->notificationModel->countUnread($this->testUserId);
        
        return TestRunner::assertEquals(0, $unreadCount, 'All should be marked as read');
    }
    
    public function testAdmissionStatusNotification(): bool
    {
        $this->notificationModel->notifyAdmissionStatus(
            $this->testUserId,
            1,
            'approved',
            'APP-2024-00001'
        );
        
        $notifications = $this->notificationModel->getByUser($this->testUserId);
        $found = false;
        
        foreach ($notifications as $notif) {
            if ($notif['type'] === 'admission' && strpos($notif['title'], 'Approved') !== false) {
                $found = true;
                break;
            }
        }
        
        return TestRunner::assertTrue($found, 'Should create admission notification');
    }
    
    public function testFeeVoucherNotification(): bool
    {
        $this->notificationModel->notifyFeeVoucher(
            $this->testUserId,
            1,
            'V-202412-00001',
            25000,
            '2024-12-15'
        );
        
        $notifications = $this->notificationModel->getByUser($this->testUserId);
        $found = false;
        
        foreach ($notifications as $notif) {
            if ($notif['type'] === 'fee' && strpos($notif['title'], 'Voucher') !== false) {
                $found = true;
                break;
            }
        }
        
        return TestRunner::assertTrue($found, 'Should create fee voucher notification');
    }
    
    public function testPaymentStatusNotification(): bool
    {
        $this->notificationModel->notifyPaymentStatus(
            $this->testUserId,
            1,
            'V-202412-00001',
            'verified'
        );
        
        $notifications = $this->notificationModel->getByUser($this->testUserId);
        $found = false;
        
        foreach ($notifications as $notif) {
            if (strpos($notif['title'], 'Payment') !== false) {
                $found = true;
                break;
            }
        }
        
        return TestRunner::assertTrue($found, 'Should create payment notification');
    }
    
    public function testFeeReminderNotification(): bool
    {
        $this->notificationModel->sendFeeReminder(
            $this->testUserId,
            'V-202412-00001',
            25000,
            '2024-12-15'
        );
        
        $notifications = $this->notificationModel->getByUser($this->testUserId);
        $found = false;
        
        foreach ($notifications as $notif) {
            if (strpos($notif['title'], 'Reminder') !== false) {
                $found = true;
                break;
            }
        }
        
        return TestRunner::assertTrue($found, 'Should create fee reminder notification');
    }
    
    public function testCertificateNotification(): bool
    {
        $this->notificationModel->notifyCertificate(
            $this->testUserId,
            1,
            'Hospitality Management'
        );
        
        $notifications = $this->notificationModel->getByUser($this->testUserId);
        $found = false;
        
        foreach ($notifications as $notif) {
            if ($notif['type'] === 'certificate') {
                $found = true;
                break;
            }
        }
        
        return TestRunner::assertTrue($found, 'Should create certificate notification');
    }
}

