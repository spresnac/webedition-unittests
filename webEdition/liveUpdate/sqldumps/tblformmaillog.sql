CREATE TABLE ###TBLPREFIX###tblformmaillog (
  id bigint(20) unsigned NOT NULL auto_increment,
  ip varchar(15) NOT NULL,
  unixTime int(11) unsigned NOT NULL,
  PRIMARY KEY  (id),
  KEY ipwhen (ip,unixTime)
) ENGINE=MyISAM;
