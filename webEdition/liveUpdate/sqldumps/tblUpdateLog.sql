CREATE TABLE ###TBLPREFIX###tblUpdateLog (
  ID int(11) unsigned NOT NULL auto_increment,
  dortigeID int(11) unsigned NOT NULL default '0',
  datum timestamp default CURRENT_TIMESTAMP, 
  aktion text NOT NULL,
  versionsnummer varchar(10) NOT NULL default '',
  module text NOT NULL,
  error tinyint(1) unsigned NOT NULL default '0',
  step smallint(4) unsigned NOT NULL default '0',
  PRIMARY KEY  (ID)
) ENGINE=MyISAM;
