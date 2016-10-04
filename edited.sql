/*
CREATE TABLE `application_keywords` (
  `app_id` int(11) NOT NULL,
  `keyword_id` int(11) NOT NULL,
  `priority` int(5) NOT NULL COMMENT 'the highest number the highest priority, The number is times of founded keyword in the application title and description'
);
CREATE TABLE `keywords` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
)
ALTER TABLE `application_keywords` ADD PRIMARY KEY (`app_id`,`keyword_id`);
ALTER TABLE `keywords` ADD PRIMARY KEY (`id`);
ALTER TABLE `keywords`  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `users` ADD `verify_token` VARCHAR(100) NOT NULL AFTER `remember_token`;
*/
