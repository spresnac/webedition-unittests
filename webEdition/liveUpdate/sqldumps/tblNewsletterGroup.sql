CREATE TABLE ###TBLPREFIX###tblNewsletterGroup (
  ID bigint(20) unsigned NOT NULL auto_increment,
  NewsletterID bigint(20) NOT NULL default '0',
  Emails longtext NOT NULL,
  Customers longtext NOT NULL,
  SendAll tinyint(1) unsigned NOT NULL default '0',
  Filter blob NOT NULL,
  Extern longtext,
  PRIMARY KEY (ID),
  KEY NewsletterID (NewsletterID)
) ENGINE=MyISAM;
