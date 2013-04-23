CREATE TABLE ###TBLPREFIX###tblContent (
  ID bigint(20) unsigned NOT NULL auto_increment,
  BDID int(11) unsigned NOT NULL default '0',
  Dat longtext,
  IsBinary tinyint(1) unsigned NOT NULL default '0',
  AutoBR enum('on','off') NOT NULL default 'off',
  LanguageID int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (ID),
  KEY BDID (BDID)
) ENGINE=MyISAM;
