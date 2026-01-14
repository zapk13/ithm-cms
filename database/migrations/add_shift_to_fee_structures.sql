-- Migration: Add shift column to fee_structures and update unique constraint
-- Run this in phpMyAdmin or via database/run_migration.php

-- Add shift column if it doesn't exist (check first)
-- Note: This will be handled by the migration script to check if column exists

-- Step 1: Add shift column
ALTER TABLE `fee_structures` 
ADD COLUMN IF NOT EXISTS `shift` ENUM('morning', 'evening') DEFAULT 'morning' AFTER `campus_id`;

-- Step 2: Update existing records to have default shift
UPDATE `fee_structures` SET `shift` = 'morning' WHERE `shift` IS NULL;

-- Step 3: For unique constraint, we'll check if it exists and recreate it
-- This is handled in the migration script to avoid foreign key issues
