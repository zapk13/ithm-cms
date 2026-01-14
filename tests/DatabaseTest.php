<?php
/**
 * Database Connection & Schema Tests
 */

namespace Tests;

use App\Core\Database;

class DatabaseTest
{
    private Database $db;
    
    public function setUp(): void
    {
        $this->db = Database::getInstance();
    }
    
    public function testDatabaseConnectionExists(): bool
    {
        return TestRunner::assertNotNull($this->db->getConnection(), 'Database connection failed');
    }
    
    public function testRolesTableExists(): bool
    {
        $result = $this->db->fetchAll("SHOW TABLES LIKE 'roles'");
        return TestRunner::assertCount(1, $result, 'Roles table does not exist');
    }
    
    public function testUsersTableExists(): bool
    {
        $result = $this->db->fetchAll("SHOW TABLES LIKE 'users'");
        return TestRunner::assertCount(1, $result, 'Users table does not exist');
    }
    
    public function testCampusesTableExists(): bool
    {
        $result = $this->db->fetchAll("SHOW TABLES LIKE 'campuses'");
        return TestRunner::assertCount(1, $result, 'Campuses table does not exist');
    }
    
    public function testCoursesTableExists(): bool
    {
        $result = $this->db->fetchAll("SHOW TABLES LIKE 'courses'");
        return TestRunner::assertCount(1, $result, 'Courses table does not exist');
    }
    
    public function testAdmissionsTableExists(): bool
    {
        $result = $this->db->fetchAll("SHOW TABLES LIKE 'admissions'");
        return TestRunner::assertCount(1, $result, 'Admissions table does not exist');
    }
    
    public function testFeeVouchersTableExists(): bool
    {
        $result = $this->db->fetchAll("SHOW TABLES LIKE 'fee_vouchers'");
        return TestRunner::assertCount(1, $result, 'Fee vouchers table does not exist');
    }
    
    public function testFeePaymentsTableExists(): bool
    {
        $result = $this->db->fetchAll("SHOW TABLES LIKE 'fee_payments'");
        return TestRunner::assertCount(1, $result, 'Fee payments table does not exist');
    }
    
    public function testNotificationsTableExists(): bool
    {
        $result = $this->db->fetchAll("SHOW TABLES LIKE 'notifications'");
        return TestRunner::assertCount(1, $result, 'Notifications table does not exist');
    }
    
    public function testCertificatesTableExists(): bool
    {
        $result = $this->db->fetchAll("SHOW TABLES LIKE 'certificates'");
        return TestRunner::assertCount(1, $result, 'Certificates table does not exist');
    }
    
    public function testSettingsTableExists(): bool
    {
        $result = $this->db->fetchAll("SHOW TABLES LIKE 'settings'");
        return TestRunner::assertCount(1, $result, 'Settings table does not exist');
    }
    
    public function testDefaultRolesCreated(): bool
    {
        $roles = $this->db->fetchAll("SELECT * FROM roles");
        return TestRunner::assertEquals(4, count($roles), 'Expected 4 default roles');
    }
    
    public function testDefaultAdminUserExists(): bool
    {
        $user = $this->db->fetch("SELECT * FROM users WHERE email = 'admin@ithm.edu.pk'");
        return TestRunner::assertNotNull($user, 'Default admin user not found');
    }
    
    public function testMainCampusExists(): bool
    {
        $campus = $this->db->fetch("SELECT * FROM campuses WHERE type = 'main'");
        return TestRunner::assertNotNull($campus, 'Main campus not found');
    }
    
    public function testDefaultSettingsExist(): bool
    {
        $settings = $this->db->fetchAll("SELECT * FROM settings");
        return TestRunner::assertGreaterThan(5, count($settings), 'Expected at least 6 default settings');
    }
}

