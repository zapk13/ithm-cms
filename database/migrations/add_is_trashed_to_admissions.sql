-- Add soft-delete flag to admissions
ALTER TABLE `admissions`
    ADD COLUMN `is_trashed` TINYINT(1) NOT NULL DEFAULT 0 AFTER `status`;


