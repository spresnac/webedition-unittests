CREATE TABLE ###TBLPREFIX###tblWorkflowDocTask (
  ID int(11) unsigned NOT NULL auto_increment,
  documentStepID bigint(20) unsigned NOT NULL default '0',
  workflowTaskID bigint(20) unsigned NOT NULL default '0',
  `Date` bigint(10) unsigned NOT NULL default '0',
  todoID bigint(20) unsigned NOT NULL default '0',
  `Status` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (ID)
) ENGINE=MyISAM;
