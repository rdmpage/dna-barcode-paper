# Dark taxa

To build a timeline of NCBI taxa we first get a list of all NCBI tax_ids for each month using the mdat or the edit fields (there doesn’t seem to be a particularly easy way to get the actual entry date from NCBI). The PHP script month.php will fetch the meat or eat data.

We then get the NCBI taxonomy file from ftp://ftp.ncbi.nlm.nih.gov/pub/taxonomy/ and create a MySQL database.

Once we have the database we can then join the mdat or eat tables and get counts of taxon dates by most (the PHP script stats.php does this).

## MySQL

CREATE TABLE `ncbi_names` (
  `tax_id` mediumint(11) unsigned NOT NULL default ‘0’,
  `name_txt` varchar(255) NOT NULL default ‘’,
  `unique_name` varchar(255) default NULL,
  `name_class` varchar(32) NOT NULL default ‘’,
  KEY `tax_id` (`tax_id`),
  KEY `name_class` (`name_class`),
  KEY `name_txt` (`name_txt`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOAD DATA INFILE ‘/Users/rpage/names.dmp’ 
INTO TABLE ncbi_names 
FIELDS TERMINATED BY ‘\t|\t’ 
LINES TERMINATED BY ‘\t|\n’ 
(tax_id, name_txt, unique_name, name_class);

CREATE TABLE `ncbi_nodes` (
  `tax_id` mediumint(11) unsigned NOT NULL default ‘0’,
  `parent_tax_id` mediumint(8) unsigned NOT NULL default ‘0’,
  `rank` varchar(32) default NULL,
  `embl_code` varchar(16) default NULL,
  `division_id` smallint(6) NOT NULL default ‘0’,
  `inherited_div_flag` tinyint(4) NOT NULL default ‘0’,
  `genetic_code_id` smallint(6) NOT NULL default ‘0’,
  `inherited_GC_flag` tinyint(4) NOT NULL default ‘0’,
  `mitochondrial_genetic_code_id` smallint(4) NOT NULL default ‘0’,
  `inherited_MGC_flag` tinyint(4) NOT NULL default ‘0’,
  `GenBank_hidden_flag` smallint(4) NOT NULL default ‘0’,
  `hidden_subtree_root_flag` tinyint(4) NOT NULL default ‘0’,
  `comments` varchar(255) default NULL,
  PRIMARY KEY  (`tax_id`),
  KEY `parent_tax_id` (`parent_tax_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOAD DATA INFILE ‘/Users/rpage/nodes.dmp’ 
INTO TABLE ncbi_nodes 
FIELDS TERMINATED BY ‘\t|\t’ 
LINES TERMINATED BY ‘\t|\n’ 
(tax_id, parent_tax_id,rank,embl_code,division_id,inherited_div_flag,genetic_code_id,inherited_GC_flag,
mitochondrial_genetic_code_id,inherited_MGC_flag,GenBank_hidden_flag,hidden_subtree_root_flag,comments);

CREATE TABLE `tax_id_by_edat` (
  `month_start` date DEFAULT NULL,
  `month_end` date DEFAULT NULL,
  `tax_id` int(11) unsigned NOT NULL,
  UNIQUE KEY `tax_id` (`tax_id`),
  KEY `month_start` (`month_start`),
  KEY `month_end` (`month_end`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `tax_id_by_mdat` (
  `month_start` date DEFAULT NULL,
  `month_end` date DEFAULT NULL,
  `tax_id` int(11) unsigned NOT NULL,
  UNIQUE KEY `tax_id` (`tax_id`),
  KEY `month_start` (`month_start`),
  KEY `month_end` (`month_end`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;





