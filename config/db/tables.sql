--
-- Table structure for table `ext_search_filtercondition`
--

CREATE TABLE IF NOT EXISTS `ext_search_filtercondition` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `date_update` int(10) unsigned NOT NULL,
  `date_create` int(10) unsigned NOT NULL DEFAULT '0',
  `id_user_create` smallint(5) unsigned NOT NULL DEFAULT '0',
  `deleted` tinyint(2) NOT NULL DEFAULT '0',
  `id_set` smallint(5) unsigned NOT NULL,
  `filter` varchar(64) NOT NULL,
  `value` varchar(100) NOT NULL,
  `negate` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=265 ;

--
-- Table structure for table `ext_search_filterset`
--

CREATE TABLE IF NOT EXISTS `ext_search_filterset` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `date_update` int(10) unsigned NOT NULL,
  `date_create` int(10) unsigned NOT NULL DEFAULT '0',
  `deleted` tinyint(2) NOT NULL DEFAULT '0',
  `sorting` smallint(5) unsigned NOT NULL,
  `is_hidden` tinyint(2) NOT NULL DEFAULT '0',
  `id_user` smallint(5) unsigned NOT NULL,
  `usergroups` varchar(16) NOT NULL,
  `type` varchar(16) NOT NULL,
  `title` varchar(64) NOT NULL,
  `conjunction` varchar(3) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;