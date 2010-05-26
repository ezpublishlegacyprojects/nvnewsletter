-- --------------------------------------------------------

--
-- Rakenne taululle `nvnewsletter_clicktrack`
--

CREATE TABLE IF NOT EXISTS `nvnewsletter_clicktrack` (
  `newsletter_id` int(11) NOT NULL default '0',
  `link_id` int(11) NOT NULL default '0',
  `action_date` date NOT NULL default '0000-00-00',
  `data_int` int(11) NOT NULL default '0',
  KEY `newsletter_id` (`newsletter_id`),
  KEY `link_id` (`link_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Rakenne taululle `nvnewsletter_clicktrack_link`
--

CREATE TABLE IF NOT EXISTS `nvnewsletter_clicktrack_link` (
  `id` int(11) NOT NULL auto_increment,
  `link` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `link` (`link`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Rakenne taululle `nvnewsletter_newsletters`
--

CREATE TABLE IF NOT EXISTS `nvnewsletter_newsletters` (
  `id` int(11) NOT NULL auto_increment,
  `contentobject_id` int(11) default NULL,
  `contentobject_version` int(11) default NULL,
  `status` tinyint(4) NOT NULL default '0',
  `send_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `send_start_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `send_last_access_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `send_end_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `total_mail_count` int(11) NOT NULL default '0',
  `sent_mail_count` int(11) NOT NULL default '0',
  `info` varchar(255) collate utf8_unicode_ci default NULL,
  `locale` char(6) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `contentobject` (`contentobject_id`,`contentobject_version`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Rakenne taululle `nvnewsletter_receiverfields`
--

CREATE TABLE IF NOT EXISTS `nvnewsletter_receiverfields` (
  `id` int(11) NOT NULL auto_increment,
  `status` tinyint(4) NOT NULL default '0',
  `field_name` varchar(255) collate utf8_unicode_ci NOT NULL,
  `field_type` varchar(45) collate utf8_unicode_ci NOT NULL,
  `required` tinyint(4) NOT NULL,
  `meta` text collate utf8_unicode_ci,
  `field_order` int(11) NOT NULL,
  PRIMARY KEY  (`id`,`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Rakenne taululle `nvnewsletter_receivergroups`
--

CREATE TABLE IF NOT EXISTS `nvnewsletter_receivergroups` (
  `id` int(11) NOT NULL auto_increment,
  `group_name` varchar(255) collate utf8_unicode_ci NOT NULL,
  `group_description` text collate utf8_unicode_ci,
  `status` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`,`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Rakenne taululle `nvnewsletter_receivers`
--

CREATE TABLE IF NOT EXISTS `nvnewsletter_receivers` (
  `id` int(11) NOT NULL auto_increment,
  `email_address` varchar(255) collate utf8_unicode_ci NOT NULL,
  `status` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`,`status`),
  KEY `email_address` (`email_address`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Rakenne taululle `nvnewsletter_receivers_has_fields`
--

CREATE TABLE IF NOT EXISTS `nvnewsletter_receivers_has_fields` (
  `receiver_id` int(11) NOT NULL,
  `receiverfield_id` int(11) NOT NULL,
  `data` varchar(255) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`receiver_id`,`receiverfield_id`),
  KEY `data` (`data`(15))
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Rakenne taululle `nvnewsletter_receivers_has_groups`
--

CREATE TABLE IF NOT EXISTS `nvnewsletter_receivers_has_groups` (
  `receiver_id` int(11) NOT NULL,
  `receivergroup_id` int(11) NOT NULL,
  `mail_type` tinyint(4) NOT NULL default '1',
  `pub_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `status` int(4) NOT NULL default '0',
  PRIMARY KEY  (`receiver_id`,`receivergroup_id`),
  KEY `fk_nvnewsletter_receivergroups_nvnewsletter_receivers` (`receiver_id`),
  KEY `fk_nvnewsletter_receivergroups_nvnewsletter_receivergroups` (`receivergroup_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Rakenne taululle `nvnewsletter_receivers_has_groups_unsub`
--

CREATE TABLE IF NOT EXISTS `nvnewsletter_receivers_has_groups_unsub` (
  `receiver_id` int(11) NOT NULL,
  `receivergroup_id` int(11) NOT NULL,
  `mail_type` tinyint(4) NOT NULL default '1',
  `unsub_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `status` int(4) NOT NULL default '0',
  PRIMARY KEY  (`receiver_id`,`receivergroup_id`),
  KEY `fk_nvnewsletter_receivergroups_nvnewsletter_receivers` (`receiver_id`),
  KEY `fk_nvnewsletter_receivergroups_nvnewsletter_receivergroups` (`receivergroup_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Rakenne taululle `nvnewsletter_receivers_in_progress`
--

CREATE TABLE IF NOT EXISTS `nvnewsletter_receivers_in_progress` (
  `id` int(11) NOT NULL auto_increment,
  `contentobject_id` int(11) NOT NULL,
  `contentobject_version` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `email_address` varchar(255) collate utf8_unicode_ci NOT NULL,
  `mail_type` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `receiver` (`contentobject_id`,`email_address`,`contentobject_version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Rakenne taululle `nvnewsletter_senders`
--

CREATE TABLE IF NOT EXISTS `nvnewsletter_senders` (
  `id` int(11) NOT NULL auto_increment,
  `sender_name` varchar(255) collate utf8_unicode_ci default NULL,
  `sender_email` varchar(255) collate utf8_unicode_ci NOT NULL,
  `reply_to` varchar(255) collate utf8_unicode_ci default NULL,
  `status` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`,`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Rakenne taululle `nvnewsletter_statistics`
--

CREATE TABLE IF NOT EXISTS `nvnewsletter_statistics` (
  `id` int(11) NOT NULL auto_increment,
  `newsletter_id` int(11) NOT NULL default '0',
  `receiver_id` int(11) NOT NULL,
  `action` tinyint(1) NOT NULL,
  `action_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `data_text` varchar(255) collate utf8_unicode_ci default NULL,
  `data_int` int(11) default NULL,
  PRIMARY KEY  (`id`),
  KEY `newsletter_id` (`newsletter_id`),
  KEY `receiver_id` (`receiver_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
