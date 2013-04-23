CREATE TABLE ###TBLPREFIX###tblformmailblock (
  id bigint(20) unsigned NOT NULL auto_increment,
  ip varchar(15) NOT NULL,
  blockedUntil int(11) unsigned NOT NULL,
  PRIMARY KEY  (id),
  KEY ipblockeduntil (ip,blockedUntil)
) ENGINE=MyISAM;
