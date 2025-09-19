-- ================================================
-- Maintio Database Migration Script
-- Created: 2025-09-19
-- Target: MySQL/MariaDB
-- ================================================

-- Create database (optional - can be done manually in phpMyAdmin)
CREATE DATABASE IF NOT EXISTS `maintio` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `maintio`;

-- ================================================
-- DROP EXISTING TABLES (in correct order due to foreign keys)
-- ================================================
DROP TABLE IF EXISTS `work_order_components`;
DROP TABLE IF EXISTS `preventive_maintenance`;
DROP TABLE IF EXISTS `work_orders`;
DROP TABLE IF EXISTS `assets`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `settings`;

-- ================================================
-- 1. USERS TABLE
-- ================================================
CREATE TABLE `users` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `username` varchar(100) NOT NULL,
    `email` varchar(150) NOT NULL,
    `password_hash` varchar(255) NOT NULL,
    `first_name` varchar(100) DEFAULT NULL,
    `last_name` varchar(100) DEFAULT NULL,
    `role` enum('admin','manager','technician','viewer') NOT NULL DEFAULT 'technician',
    `status` enum('active','inactive') NOT NULL DEFAULT 'active',
    `phone` varchar(50) DEFAULT NULL,
    `department` varchar(100) DEFAULT NULL,
    `last_login` datetime DEFAULT NULL,
    `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `username` (`username`),
    UNIQUE KEY `email` (`email`),
    INDEX `idx_role` (`role`),
    INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================
-- 2. ASSETS TABLE
-- ================================================
CREATE TABLE `assets` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(200) NOT NULL,
    `asset_number` varchar(100) NOT NULL,
    `type` varchar(100) NOT NULL,
    `location` varchar(200) NOT NULL,
    `status` enum('operational','maintenance','out_of_order','decommissioned') NOT NULL DEFAULT 'operational',
    `priority` enum('low','medium','high','critical') NOT NULL DEFAULT 'medium',
    `manufacturer` varchar(150) DEFAULT NULL,
    `model` varchar(100) DEFAULT NULL,
    `serial_number` varchar(100) DEFAULT NULL,
    `installation_date` date DEFAULT NULL,
    `purchase_price` decimal(10,2) DEFAULT NULL,
    `description` text DEFAULT NULL,
    `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `asset_number` (`asset_number`),
    INDEX `idx_type` (`type`),
    INDEX `idx_location` (`location`),
    INDEX `idx_status` (`status`),
    INDEX `idx_priority` (`priority`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================
-- 3. WORK ORDERS TABLE
-- ================================================
CREATE TABLE `work_orders` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `work_order_number` varchar(100) DEFAULT NULL,
    `title` varchar(200) NOT NULL,
    `description` text DEFAULT NULL,
    `type` enum('instandhaltung','instandsetzung','inspektion','notfall','preventive') NOT NULL,
    `status` enum('open','in_progress','completed','cancelled','on_hold') NOT NULL DEFAULT 'open',
    `priority` enum('low','medium','high','critical') NOT NULL DEFAULT 'medium',
    `asset_id` int(11) DEFAULT NULL,
    `assigned_user_id` int(11) DEFAULT NULL,
    `created_by_user_id` int(11) NOT NULL,
    `estimated_duration` int(11) DEFAULT NULL COMMENT 'in minutes',
    `actual_duration` int(11) DEFAULT NULL COMMENT 'in minutes',
    `scheduled_date` datetime DEFAULT NULL,
    `started_at` datetime DEFAULT NULL,
    `completed_at` datetime DEFAULT NULL,
    `notes` text DEFAULT NULL,
    `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `work_order_number` (`work_order_number`),
    INDEX `idx_type` (`type`),
    INDEX `idx_status` (`status`),
    INDEX `idx_priority` (`priority`),
    INDEX `idx_asset_id` (`asset_id`),
    INDEX `idx_assigned_user_id` (`assigned_user_id`),
    INDEX `idx_created_by_user_id` (`created_by_user_id`),
    INDEX `idx_scheduled_date` (`scheduled_date`),
    CONSTRAINT `fk_work_orders_asset_id` FOREIGN KEY (`asset_id`) REFERENCES `assets` (`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_work_orders_assigned_user_id` FOREIGN KEY (`assigned_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_work_orders_created_by_user_id` FOREIGN KEY (`created_by_user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================
-- 4. PREVENTIVE MAINTENANCE TABLE
-- ================================================
CREATE TABLE `preventive_maintenance` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `asset_id` int(11) NOT NULL,
    `schedule_name` varchar(200) NOT NULL,
    `description` text DEFAULT NULL,
    `task_details` text DEFAULT NULL,
    `interval_type` enum('daily','weekly','monthly','quarterly','annually','hours','cycles','kilometers') NOT NULL,
    `interval_value` int(11) NOT NULL,
    `priority` enum('low','medium','high','critical') NOT NULL DEFAULT 'medium',
    `estimated_duration` int(11) DEFAULT NULL COMMENT 'in minutes',
    `auto_generate_work_orders` tinyint(1) NOT NULL DEFAULT 0,
    `lead_time_days` int(11) NOT NULL DEFAULT 7,
    `last_completed` datetime DEFAULT NULL,
    `next_due` datetime NOT NULL,
    `is_active` tinyint(1) NOT NULL DEFAULT 1,
    `assigned_user_id` int(11) DEFAULT NULL,
    `category` varchar(100) DEFAULT NULL,
    `required_tools` text DEFAULT NULL,
    `required_parts` text DEFAULT NULL,
    `safety_notes` text DEFAULT NULL,
    `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_asset_id` (`asset_id`),
    INDEX `idx_interval_type` (`interval_type`),
    INDEX `idx_priority` (`priority`),
    INDEX `idx_next_due` (`next_due`),
    INDEX `idx_is_active` (`is_active`),
    INDEX `idx_assigned_user_id` (`assigned_user_id`),
    INDEX `idx_category` (`category`),
    CONSTRAINT `fk_preventive_maintenance_asset_id` FOREIGN KEY (`asset_id`) REFERENCES `assets` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_preventive_maintenance_assigned_user_id` FOREIGN KEY (`assigned_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================
-- 5. WORK ORDER COMPONENTS TABLE
-- ================================================
CREATE TABLE `work_order_components` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `work_order_id` int(11) NOT NULL,
    `component_name` varchar(200) NOT NULL,
    `component_type` varchar(100) DEFAULT NULL,
    `quantity` decimal(10,2) NOT NULL DEFAULT 1.00,
    `unit` varchar(50) DEFAULT NULL,
    `cost_per_unit` decimal(10,2) DEFAULT NULL,
    `total_cost` decimal(10,2) DEFAULT NULL,
    `supplier` varchar(150) DEFAULT NULL,
    `part_number` varchar(100) DEFAULT NULL,
    `notes` text DEFAULT NULL,
    `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_work_order_id` (`work_order_id`),
    INDEX `idx_component_type` (`component_type`),
    INDEX `idx_part_number` (`part_number`),
    CONSTRAINT `fk_work_order_components_work_order_id` FOREIGN KEY (`work_order_id`) REFERENCES `work_orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================
-- 6. SETTINGS TABLE
-- ================================================
CREATE TABLE `settings` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `setting_key` varchar(100) NOT NULL,
    `setting_value` text DEFAULT NULL,
    `setting_type` enum('string','integer','boolean','json') NOT NULL DEFAULT 'string',
    `description` varchar(255) DEFAULT NULL,
    `is_public` tinyint(1) NOT NULL DEFAULT 0,
    `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `setting_key` (`setting_key`),
    INDEX `idx_is_public` (`is_public`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================
-- SAMPLE DATA INSERTS
-- ================================================

-- Default Admin User (password: admin123)
INSERT INTO `users` (`username`, `email`, `password_hash`, `first_name`, `last_name`, `role`, `status`) VALUES
('admin', 'admin@maintio.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System', 'Administrator', 'admin', 'active'),
('techniker1', 'tech1@maintio.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Max', 'Mustermann', 'technician', 'active'),
('manager1', 'mgr1@maintio.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Anna', 'Schmidt', 'manager', 'active');

-- Sample Assets
INSERT INTO `assets` (`name`, `asset_number`, `type`, `location`, `status`, `priority`, `manufacturer`, `model`, `installation_date`) VALUES
('Produktionsmaschine A1', 'PROD-001', 'Produktionsmaschine', 'Halle 1', 'operational', 'high', 'Siemens', 'S7-1500', '2023-01-15'),
('Kompressor B2', 'KOMP-002', 'Kompressor', 'Technikraum', 'operational', 'medium', 'Atlas Copco', 'GA22', '2022-08-10'),
('Förderband C3', 'FOER-003', 'Förderband', 'Halle 2', 'maintenance', 'high', 'SEW', 'MoviDrive', '2023-03-20'),
('Hydraulikpresse D4', 'HYDR-004', 'Hydraulikpresse', 'Halle 1', 'operational', 'critical', 'Bosch Rexroth', 'A4VSO', '2023-06-01');

-- Sample Work Orders
INSERT INTO `work_orders` (`work_order_number`, `title`, `description`, `type`, `status`, `priority`, `asset_id`, `assigned_user_id`, `created_by_user_id`, `estimated_duration`, `scheduled_date`) VALUES
('WO-2025-001', 'Ölwechsel Produktionsmaschine A1', 'Routinemäßiger Ölwechsel nach 500 Betriebsstunden', 'instandhaltung', 'open', 'medium', 1, 2, 1, 120, '2025-09-25 08:00:00'),
('WO-2025-002', 'Inspektion Kompressor B2', 'Monatliche Sicherheitsinspektion', 'inspektion', 'completed', 'low', 2, 2, 1, 60, '2025-09-15 10:00:00'),
('WO-2025-003', 'Reparatur Förderband C3', 'Motor läuft unrund, Lager prüfen', 'instandsetzung', 'in_progress', 'high', 3, 2, 3, 240, '2025-09-20 09:00:00');

-- Sample Preventive Maintenance Schedules
INSERT INTO `preventive_maintenance` (`asset_id`, `schedule_name`, `description`, `task_details`, `interval_type`, `interval_value`, `priority`, `estimated_duration`, `auto_generate_work_orders`, `lead_time_days`, `next_due`, `assigned_user_id`) VALUES
(1, 'Wöchentliche Reinigung', 'Grundreinigung der Produktionsmaschine', 'Filter prüfen, Oberflächen reinigen, Schmierung kontrollieren', 'weekly', 1, 'medium', 90, 1, 3, '2025-09-26 08:00:00', 2),
(1, 'Monatlicher Ölwechsel', 'Öl und Filter wechseln', 'Hydrauliköl ablassen, neues Öl einfüllen, Filter tauschen', 'monthly', 1, 'high', 180, 1, 7, '2025-10-01 08:00:00', 2),
(2, 'Quarterly Wartung Kompressor', 'Umfassende Wartung alle 3 Monate', 'Riemen prüfen, Kühlsystem kontrollieren, Kondensatableiter reinigen', 'quarterly', 1, 'medium', 240, 1, 14, '2025-12-01 08:00:00', 2),
(4, 'Jährliche Hauptinspektion', 'Jährliche Sicherheitsprüfung der Hydraulikpresse', 'Alle Sicherheitsventile prüfen, Dichtungen kontrollieren, Kalibrierung', 'annually', 1, 'critical', 480, 1, 30, '2026-06-01 08:00:00', 2);

-- Sample Settings
INSERT INTO `settings` (`setting_key`, `setting_value`, `setting_type`, `description`, `is_public`) VALUES
('app_name', 'Maintio', 'string', 'Application Name', 1),
('app_version', '1.0.0', 'string', 'Application Version', 1),
('default_work_order_duration', '120', 'integer', 'Default estimated duration for work orders in minutes', 0),
('auto_generate_work_order_numbers', 'true', 'boolean', 'Automatically generate work order numbers', 0),
('maintenance_reminder_days', '7', 'integer', 'Days before maintenance due to send reminders', 0);

-- ================================================
-- INDEXES FOR PERFORMANCE
-- ================================================

-- Additional composite indexes for common queries
CREATE INDEX `idx_work_orders_status_priority` ON `work_orders` (`status`, `priority`);
CREATE INDEX `idx_work_orders_asset_status` ON `work_orders` (`asset_id`, `status`);
CREATE INDEX `idx_preventive_maintenance_due_active` ON `preventive_maintenance` (`next_due`, `is_active`);
CREATE INDEX `idx_preventive_maintenance_asset_active` ON `preventive_maintenance` (`asset_id`, `is_active`);

-- ================================================
-- VIEWS FOR COMMON QUERIES
-- ================================================

-- View for active work orders with asset and user information
DROP VIEW IF EXISTS `v_active_work_orders`;
CREATE VIEW `v_active_work_orders` AS
SELECT
    wo.id,
    wo.work_order_number,
    wo.title,
    wo.description,
    wo.type,
    wo.status,
    wo.priority,
    wo.estimated_duration,
    wo.scheduled_date,
    wo.created_at,
    a.name as asset_name,
    a.asset_number,
    a.location as asset_location,
    u.username as assigned_user,
    CONCAT(u.first_name, ' ', u.last_name) as assigned_user_full_name,
    cb.username as created_by_user
FROM work_orders wo
LEFT JOIN assets a ON wo.asset_id = a.id
LEFT JOIN users u ON wo.assigned_user_id = u.id
LEFT JOIN users cb ON wo.created_by_user_id = cb.id
WHERE wo.status IN ('open', 'in_progress');

-- View for overdue preventive maintenance
DROP VIEW IF EXISTS `v_overdue_maintenance`;
CREATE VIEW `v_overdue_maintenance` AS
SELECT
    pm.id,
    pm.schedule_name,
    pm.description,
    pm.priority,
    pm.next_due,
    pm.lead_time_days,
    a.name as asset_name,
    a.asset_number,
    a.location as asset_location,
    CONCAT(u.first_name, ' ', u.last_name) as assigned_user_full_name,
    DATEDIFF(NOW(), pm.next_due) as days_overdue
FROM preventive_maintenance pm
JOIN assets a ON pm.asset_id = a.id
LEFT JOIN users u ON pm.assigned_user_id = u.id
WHERE pm.is_active = 1
AND pm.next_due < NOW();

-- ================================================
-- TRIGGERS FOR AUDIT LOGGING (Optional)
-- ================================================

-- Trigger to automatically update work order numbers
DROP TRIGGER IF EXISTS `tr_work_orders_before_insert`;
DELIMITER $$
CREATE TRIGGER `tr_work_orders_before_insert`
BEFORE INSERT ON `work_orders`
FOR EACH ROW
BEGIN
    IF NEW.work_order_number IS NULL OR NEW.work_order_number = '' THEN
        SET NEW.work_order_number = CONCAT('WO-', YEAR(NOW()), '-', LPAD((
            SELECT COALESCE(MAX(CAST(SUBSTRING(work_order_number, 9) AS UNSIGNED)), 0) + 1
            FROM work_orders
            WHERE work_order_number LIKE CONCAT('WO-', YEAR(NOW()), '-%')
        ), 3, '0'));
    END IF;
END$$
DELIMITER ;

-- ================================================
-- SCRIPT COMPLETED
-- ================================================

-- Display creation summary
SELECT 'Database migration completed successfully!' as status,
       NOW() as completed_at,
       (SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = 'maintio') as tables_created;