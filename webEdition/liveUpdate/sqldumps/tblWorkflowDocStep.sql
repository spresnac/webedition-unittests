CREATE TABLE ###TBLPREFIX###tblWorkflowDocStep (
  ID int(11) unsigned NOT NULL auto_increment,
  workflowDocID int(11) unsigned NOT NULL default '0',
  workflowStepID bigint(20) unsigned NOT NULL default '0',
  startDate bigint(10) unsigned NOT NULL default '0',
  finishDate bigint(10) unsigned NOT NULL default '0',
  `Status` tinyint(4) unsigned NOT NULL default '0',
  PRIMARY KEY  (ID)
) ENGINE=MyISAM;
