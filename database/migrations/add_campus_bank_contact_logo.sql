-- Migration: Add bank account, contact details, and logo to campuses table
-- Run this in phpMyAdmin or via database/run_migration.php

ALTER TABLE `campuses` 
ADD COLUMN `bank_account_name` VARCHAR(150) NULL AFTER `focal_person`,
ADD COLUMN `bank_account_number` VARCHAR(50) NULL AFTER `bank_account_name`,
ADD COLUMN `bank_name` VARCHAR(150) NULL AFTER `bank_account_number`,
ADD COLUMN `bank_branch` VARCHAR(150) NULL AFTER `bank_name`,
ADD COLUMN `iban` VARCHAR(50) NULL AFTER `bank_branch`,
ADD COLUMN `contact_person_name` VARCHAR(150) NULL AFTER `iban`,
ADD COLUMN `contact_person_phone` VARCHAR(30) NULL AFTER `contact_person_name`,
ADD COLUMN `contact_person_email` VARCHAR(150) NULL AFTER `contact_person_phone`,
ADD COLUMN `logo` VARCHAR(255) NULL AFTER `contact_person_email`;

