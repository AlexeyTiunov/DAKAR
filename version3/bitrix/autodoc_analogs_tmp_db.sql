DROP TABLE IF EXISTS `b_autodoc_analogs_temp`;
CREATE TABLE IF NOT EXISTS `b_autodoc_analogs_temp` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `B1Code` int(10) unsigned NOT NULL,
  `I1Code` varchar(15) NOT NULL,
  `B2Code` int(10) unsigned NOT NULL,
  `I2Code` varchar(15) NOT NULL,
  `isGood` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `B1Code_I1Code` (`B1Code`,`I1Code`),
  KEY `B2Code_I2Code` (`B2Code`,`I2Code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
