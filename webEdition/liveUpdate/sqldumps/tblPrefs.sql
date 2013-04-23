CREATE TABLE ###TBLPREFIX###tblPrefs (
  userID bigint(20) unsigned NOT NULL default '0',
  `key` varchar(100) NOT NULL default '',
	value text NOT NULL,
  PRIMARY KEY (`userID`,`key`),
	KEY lookup (`key`)
) ENGINE=MyISAM;

