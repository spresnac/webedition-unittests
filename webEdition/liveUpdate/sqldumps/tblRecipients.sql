CREATE TABLE ###TBLPREFIX###tblRecipients (
  ID bigint(20) unsigned NOT NULL auto_increment,
  Email varchar(255) NOT NULL default '',
  PRIMARY KEY  (ID),
  UNIQUE KEY Email (Email)
) ENGINE=MyISAM;
