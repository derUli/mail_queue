ALTER TABLE `{prefix}mail_queue` ADD `priority` 
tinyint(3) unsigned NOT NULL DEFAULT 128 AFTER `fails`;
