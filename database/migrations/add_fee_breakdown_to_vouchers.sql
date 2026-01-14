-- Migration: Add fee_breakdown column to fee_vouchers table
-- Run this in phpMyAdmin or via database/run_migration.php

ALTER TABLE `fee_vouchers` 
ADD COLUMN `fee_breakdown` JSON NULL AFTER `amount`;

