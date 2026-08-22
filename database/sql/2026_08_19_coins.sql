-- Coins feature. Same as
-- database/migrations/2026_08_19_000001_create_coin_tables.php,
-- for servers where `php artisan migrate` cannot be run (cPanel / phpMyAdmin).

CREATE TABLE IF NOT EXISTS `coin_transactions` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `type` VARCHAR(20) NOT NULL,
  `coins` INT NOT NULL,
  `book_id` INT UNSIGNED NULL,
  `reader_id` INT UNSIGNED NULL,
  `redemption_id` INT UNSIGNED NULL,
  `note` VARCHAR(255) NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  KEY `idx_coin_user` (`user_id`),
  KEY `idx_coin_book` (`book_id`),
  KEY `idx_coin_type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- One credit per reader per book: the unique key is what stops farming.
CREATE TABLE IF NOT EXISTS `book_read_credits` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `book_id` INT UNSIGNED NOT NULL,
  `reader_id` INT UNSIGNED NOT NULL,
  `uploader_id` INT UNSIGNED NOT NULL,
  `coins` INT NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_book_reader` (`book_id`, `reader_id`),
  KEY `idx_credit_uploader` (`uploader_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `coin_redemptions` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `coins` INT NOT NULL,
  `amount` DECIMAL(10,2) NOT NULL,
  `code` VARCHAR(40) NULL,
  `status` VARCHAR(20) NOT NULL DEFAULT 'pending',
  `fail_reason` VARCHAR(255) NULL,
  `woo_coupon_id` INT UNSIGNED NULL,
  `issued_at` TIMESTAMP NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_redeem_code` (`code`),
  KEY `idx_redeem_user` (`user_id`),
  KEY `idx_redeem_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `users` ADD COLUMN `coin_balance` INT NOT NULL DEFAULT 0 AFTER `rollnumber`;

ALTER TABLE `settings`
  ADD COLUMN `coins_enabled` TINYINT NOT NULL DEFAULT 1,
  ADD COLUMN `coins_per_read` INT NOT NULL DEFAULT 1,
  ADD COLUMN `coins_per_upload` INT NOT NULL DEFAULT 10,
  ADD COLUMN `coin_value` DECIMAL(8,4) NOT NULL DEFAULT 0.1000,
  ADD COLUMN `coins_min_redeem` INT NOT NULL DEFAULT 500;

INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_08_19_000001_create_coin_tables', COALESCE(MAX(`batch`), 0) + 1
FROM `migrations`;
