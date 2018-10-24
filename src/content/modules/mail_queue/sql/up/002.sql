ALTER TABLE `{prefix}mail_queue` ADD `fails` 
INT NOT NULL DEFAULT '0' AFTER `created`;
