CREATE TABLE ###TBLPREFIX###tblNewsletterBlock (
  ID bigint(20) unsigned NOT NULL auto_increment,
  NewsletterID bigint(20) unsigned NOT NULL default '0',
  Groups varchar(255) NOT NULL default '',
  `Type` tinyint(4) unsigned NOT NULL default '0',
  LinkID bigint(20) unsigned NOT NULL default '0',
  Field varchar(255) NOT NULL default '',
  Source longtext NOT NULL,
  Html longtext NOT NULL,
  Pack tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (ID),
  KEY NewsletterID (NewsletterID)
) ENGINE=MyISAM;
