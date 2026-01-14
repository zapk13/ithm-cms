-- Add password_needs_reset column to users table
-- This flag indicates if user needs to set/reset their password (for admin-created accounts)

ALTER TABLE `users` 
ADD COLUMN `password_needs_reset` TINYINT(1) DEFAULT 0 AFTER `reset_token_expiry`;

