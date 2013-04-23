###UPDATEONLY###DELETE FROM ###TBLPREFIX###tblcustomerfilter WHERE modelTable="";
/* query separator */

CREATE TABLE ###TBLPREFIX###tblcustomerfilter (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `modelId` bigint(20) unsigned NOT NULL,
  `modelType` enum('folder','objectFile','text/webedition')  NOT NULL,
  `modelTable` enum('tblFile','tblObjectFiles') NOT NULL,
  `accessControlOnTemplate` tinyint(1) unsigned NOT NULL default '0',
  `errorDocNoLogin` bigint(20) unsigned NOT NULL default '0',
  `errorDocNoAccess` bigint(20) unsigned NOT NULL default '0',
  `mode` tinyint(4) unsigned NOT NULL default '0',
  `specificCustomers` text NOT NULL,
  `filter` text NOT NULL,
  `whiteList` text NOT NULL,
  `blackList` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `mode` (`mode`),
  UNIQUE KEY `modelIdN` (`modelId`,`modelType`,`modelTable`),
  KEY modelType (modelType,accessControlOnTemplate)
) ENGINE=MyISAM;
