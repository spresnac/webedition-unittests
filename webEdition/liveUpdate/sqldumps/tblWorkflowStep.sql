CREATE TABLE ###TBLPREFIX###tblWorkflowStep (
  ID int(11) unsigned NOT NULL auto_increment,
  `Worktime` float NOT NULL default '0',
  timeAction tinyint(1) unsigned NOT NULL default '0',
  stepCondition int(11) unsigned NOT NULL default '0',
  workflowID int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (ID)
) ENGINE=MyISAM;
