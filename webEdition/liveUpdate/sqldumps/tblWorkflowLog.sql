CREATE TABLE ###TBLPREFIX###tblWorkflowLog (
  ID bigint(20) unsigned NOT NULL auto_increment,
  RefID bigint(20) unsigned NOT NULL default '0',
  docTable varchar(255) NOT NULL default '',
  userID bigint(20) unsigned NOT NULL default '0',
  logDate bigint(10) unsigned NOT NULL default '0',
  `Type` tinyint(4) unsigned NOT NULL default '0',
  Description varchar(255) NOT NULL default '',
  PRIMARY KEY  (ID)
) ENGINE=MyISAM;
