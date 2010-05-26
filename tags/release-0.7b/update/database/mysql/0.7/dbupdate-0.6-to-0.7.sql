RENAME TABLE `nvnewsletter_receivers_mailtemp` TO `nvnewsletter_receivers_in_progress`;
ALTER TABLE `nvnewsletter_receivers_in_progress` CHANGE `user_id` `receiver_id` INT( 11 ) NOT NULL;
ALTER TABLE `nvnewsletter_receivers_has_groups` ADD `status` INT( 4 ) NOT NULL DEFAULT '0';
ALTER TABLE `nvnewsletter_receivers_has_groups_unsub` ADD `status` INT( 4 ) NOT NULL DEFAULT '0';