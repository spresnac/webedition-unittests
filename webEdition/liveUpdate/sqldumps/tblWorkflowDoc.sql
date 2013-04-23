CREATE TABLE ###TBLPREFIX###tblWorkflowDoc (
  ID int(11) unsigned NOT NULL auto_increment,
  workflowID int(11) unsigned NOT NULL default '0',
  documentID int(11) unsigned NOT NULL default '0',
  userID int(11) unsigned NOT NULL default '0',
  `Status` tinyint(4) unsigned NOT NULL default '0',
  PRIMARY KEY  (ID)
) ENGINE=MyISAM;
