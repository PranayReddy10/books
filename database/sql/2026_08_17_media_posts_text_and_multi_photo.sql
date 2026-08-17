-- Same change as the migration
-- database/migrations/2026_08_17_000001_allow_text_and_multi_photo_media_posts.php,
-- for servers where `php artisan migrate` cannot be run (cPanel / phpMyAdmin).
--
-- Run it once against the app's database, then reload the app.

-- 1. media_type was ENUM('photo','video'); text posts need a third value.
ALTER TABLE `media_posts`
    MODIFY `media_type` VARCHAR(20) NOT NULL DEFAULT 'photo';

-- 2. a text post has no file, so file_url must accept NULL.
ALTER TABLE `media_posts`
    MODIFY `file_url` VARCHAR(255) NULL;

-- 3. photos after the first, stored as a JSON array. The cover stays in file_url.
ALTER TABLE `media_posts`
    ADD COLUMN `extra_images` TEXT NULL AFTER `thumb_url`;

-- 4. record the migration so a later `php artisan migrate` skips it.
--    If your migrations table has no `batch` column, drop that part.
INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_08_17_000001_allow_text_and_multi_photo_media_posts', COALESCE(MAX(`batch`), 0) + 1
FROM `migrations`;
