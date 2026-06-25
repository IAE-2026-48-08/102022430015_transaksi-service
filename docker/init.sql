CREATE TABLE IF NOT EXISTS `migrations` (
    `id` int UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `migration` varchar(255) NOT NULL,
    `batch` int NOT NULL
);

CREATE TABLE IF NOT EXISTS `cache` (
    `key` varchar(255) NOT NULL PRIMARY KEY,
    `value` mediumtext NOT NULL,
    `expiration` int NOT NULL
);

CREATE TABLE IF NOT EXISTS `cache_locks` (
    `key` varchar(255) NOT NULL PRIMARY KEY,
    `owner` varchar(255) NOT NULL,
    `expiration` int NOT NULL
);

CREATE TABLE IF NOT EXISTS `transactions` (
    `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `account_id` varchar(255) NOT NULL,
    `type` enum('credit','debit') NOT NULL,
    `amount` decimal(15,2) NOT NULL,
    `description` text NULL,
    `reference_number` varchar(255) NULL,
    `transaction_date` timestamp NULL,
    `created_at` timestamp NULL,
    `updated_at` timestamp NULL
);

CREATE TABLE IF NOT EXISTS `repayments` (
    `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `account_id` varchar(255) NOT NULL,
    `loan_id` varchar(255) NULL,
    `installment_number` int NOT NULL,
    `due_amount` decimal(15,2) NOT NULL,
    `repayment_amount` decimal(15,2) NULL,
    `due_date` date NULL,
    `paid_at` timestamp NULL,
    `status` enum('pending','paid','overdue') DEFAULT 'pending',
    `created_at` timestamp NULL,
    `updated_at` timestamp NULL
);
