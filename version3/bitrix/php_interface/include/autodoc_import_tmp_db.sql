DROP TABLE IF EXISTS `b_autodoc_import_temp`;
CREATE TABLE IF NOT EXISTS `b_autodoc_import_temp` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `CurItemID` int(10) unsigned default NULL COMMENT 'Текущий ID для данного ItemCode-BrandCode(если есть)',
  `CurPriceID` int(10) unsigned default NULL COMMENT 'Текущий ID цены для данного товара',
  `BrandCode` int(11) default NULL,
  `strBrandCode` varchar(2) default NULL,
  `ItemCode` varchar(30) default NULL,
  `Caption` varchar(50) default NULL,
  `Weight` float default NULL,
  `Price` double default NULL,
  `strCorr` varchar(50) default NULL,
  `RegionCode` int(11) default NULL,
  `userID` int(10) unsigned default NULL,
  PRIMARY KEY  (`id`),
  KEY `strBrandCode` (`strBrandCode`),
  KEY `strCorr` (`strCorr`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
