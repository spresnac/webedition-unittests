CREATE TABLE ###TBLPREFIX###tblWorkflowTask (
  ID int(11) unsigned NOT NULL auto_increment,
  userID int(11) unsigned NOT NULL default '0',
  Edit int(11) unsigned NOT NULL default '0',
  Mail int(11) unsigned NOT NULL default '0',
  stepID int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (ID)
) ENGINE=MyISAM;
