
DROP TABLE IF EXISTS `b_autodoc_analogs_temp`;
CREATE TABLE IF NOT EXISTS `b_autodoc_analogs_temp` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `B1Code` int(10) unsigned default NULL,
  `strB1Code` varchar(2) default NULL,
  `I1Code` varchar(15) default NULL,
  `B2Code` int(10) default NULL,
  `strB2Code` varchar(2) default NULL,
  `I2Code` varchar(15) default NULL,
  `isItemFound` tinyint(1) NOT NULL default '0',
  `isAnalogFound` tinyint(1) NOT NULL default '0',
  `curAnalogID` int(10) unsigned default NULL,
  PRIMARY KEY  (`id`),
  KEY `B1Code_I1Code` (`B1Code`,`I1Code`),
  KEY `B2Code_I2Code` (`B2Code`,`I2Code`),
  KEY `strB1Code` (`strB1Code`),
  KEY `strB2Code` (`strB2Code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;