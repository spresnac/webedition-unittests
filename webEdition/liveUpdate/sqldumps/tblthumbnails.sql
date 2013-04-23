CREATE TABLE ###TBLPREFIX###tblthumbnails (
  ID bigint(20) unsigned NOT NULL auto_increment,
  Name varchar(255) NOT NULL default '',
  `Date` int(11) unsigned NOT NULL default '0',
  Format char(3) NOT NULL default '',
  Height smallint(5) unsigned default NULL,
  Width smallint(5) unsigned default NULL,
  Ratio tinyint(1) unsigned NOT NULL default '0',
  Maxsize tinyint(1) unsigned NOT NULL default '0',
  Interlace tinyint(1) unsigned NOT NULL default '1',
  Fitinside smallint(5) unsigned NOT NULL default '0',
  `Directory` varchar(255) NOT NULL default '',
  Utilize tinyint(1) unsigned NOT NULL default '0',
  `Quality` tinyint unsigned NOT NULL DEFAULT  '8',
  PRIMARY KEY  (ID)
) ENGINE=MyISAM;
