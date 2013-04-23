CREATE TABLE ###TBLPREFIX###tblnavigationrules (
  ID int(11) unsigned NOT NULL auto_increment,
  NavigationName varchar(255) default NULL,
  NavigationID int(11) unsigned NOT NULL default '0',
  SelectionType varchar(16) NOT NULL default '',
  FolderID int(11) unsigned NOT NULL default '0',
  DoctypeID int(11) unsigned NOT NULL default '0',
  Categories text NOT NULL,
  ClassID int(11) unsigned NOT NULL default '0',
  WorkspaceID int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (ID)
) ENGINE=MyISAM;
