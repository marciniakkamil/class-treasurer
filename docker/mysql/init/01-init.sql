-- Initialize additional MySQL resources for local development
-- This file is mounted into /docker-entrypoint-initdb.d and executed on first container start

-- Create a dedicated testing database (optional use)
CREATE DATABASE IF NOT EXISTS `class_treasurer_test` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Ensure the dev user has privileges on both dev and test databases
GRANT ALL PRIVILEGES ON `class_treasurer`.* TO 'laravel'@'%';
GRANT ALL PRIVILEGES ON `class_treasurer_test`.* TO 'laravel'@'%';
FLUSH PRIVILEGES;
