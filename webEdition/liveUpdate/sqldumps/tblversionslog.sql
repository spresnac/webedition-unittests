CREATE TABLE ###TBLPREFIX###tblversionslog (
  `ID` bigint(20) unsigned NOT NULL auto_increment,
  `timestamp` int(11) unsigned NOT NULL,
  `action` int(11) unsigned NOT NULL,
  `userID` int(11) unsigned NOT NULL,
  `data` longtext NOT NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM ;
