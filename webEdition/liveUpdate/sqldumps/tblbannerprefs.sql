CREATE TABLE ###TBLPREFIX###tblbannerprefs (
  pref_name varchar(255) NOT NULL,
  pref_value varchar(255) NOT NULL default '',
  PRIMARY KEY  (pref_name)
) ENGINE=MyISAM;
