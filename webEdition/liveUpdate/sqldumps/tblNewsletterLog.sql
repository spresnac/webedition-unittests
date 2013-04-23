CREATE TABLE ###TBLPREFIX###tblNewsletterLog (
  ID bigint(20) unsigned NOT NULL auto_increment,
  NewsletterID bigint(20) unsigned NOT NULL default '0',
  LogTime bigint(20) unsigned NOT NULL default '0',
  Log varchar(255) NOT NULL default '',
  Param varchar(255) NOT NULL default '',
  PRIMARY KEY  (ID),
  KEY NewsletterID (NewsletterID)
) ENGINE=MyISAM;
