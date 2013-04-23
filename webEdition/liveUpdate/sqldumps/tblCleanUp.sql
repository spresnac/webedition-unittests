CREATE TABLE ###TBLPREFIX###tblCleanUp (
  ID int(11) unsigned NOT NULL auto_increment,
  Path varchar(255) NOT NULL default '',
  `Date` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (ID),
  UNIQUE KEY Path (Path),
  KEY `Date` (`Date`)
) ENGINE=MyISAM;
