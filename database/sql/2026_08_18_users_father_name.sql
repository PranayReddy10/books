-- Sign-up collects the father's name from the hall-ticket lookup.
-- Run this if you cannot use `php artisan migrate` on the server.

ALTER TABLE `users` ADD COLUMN `father_name` VARCHAR(255) NULL AFTER `name`;

INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_08_18_000001_add_father_name_to_users_table', COALESCE(MAX(`batch`), 0) + 1
FROM `migrations`;
