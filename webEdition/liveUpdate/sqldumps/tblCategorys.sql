CREATE TABLE ###TBLPREFIX###tblCategorys (
  ID int(11) unsigned NOT NULL auto_increment,
  Category varchar(64) NOT NULL default '',
  `Text` varchar(64) default NULL,
  Path varchar(255) default NULL,
  ParentID bigint(20) unsigned default NULL,
  IsFolder tinyint(1) unsigned  default NULL,
  Icon varchar(64) default NULL,
  Catfields longtext NOT NULL,
  PRIMARY KEY  (ID),
  KEY Path (Path)
) ENGINE=MyISAM;
