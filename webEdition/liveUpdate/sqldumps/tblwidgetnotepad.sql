CREATE TABLE ###TBLPREFIX###tblwidgetnotepad (
  `ID` bigint(20) unsigned NOT NULL auto_increment,
  `WidgetName` varchar(100) NOT NULL default '',
  `UserID` int(10) unsigned NOT NULL default '0',
  `CreationDate` date NOT NULL default '0000-00-00',
  `Title` varchar(255) NOT NULL default '',
  `Text` text NOT NULL,
  `Priority` enum('low','medium','high') NOT NULL default 'low',
  `Valid` enum('always','date','period') NOT NULL default 'always',
  `ValidFrom` date NOT NULL default '0000-00-00',
  `ValidUntil` date NOT NULL default '0000-00-00',
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM;
/* query separator */
###INSTALLONLY###INSERT IGNORE INTO ###TBLPREFIX###tblwidgetnotepad VALUES (1, 'webEdition', 1, NOW(), 'Welcome to webEdition!', '', 'low', 'always', NOW(), "3000-01-01");
