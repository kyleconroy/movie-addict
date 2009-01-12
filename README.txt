How to install.

1. Download the Facebook Platform PHP Library
2. Create a database with two tables with the following SQL
	- Table 1 
		CREATE TABLE IF NOT EXISTS `top250` (
		  `imdbid` varchar(7) default NULL,
		  `title` varchar(100) default NULL
		) ENGINE=MyISAM DEFAULT CHARSET=latin1;
	- Table 2
		CREATE TABLE IF NOT EXISTS `users` (
		  `userid` varchar(11) default NULL,
		  `percent` varchar(6) NOT NULL default '0',
		  ) ENGINE=MyISAM DEFAULT CHARSET=latin1;
3. Fill out the config.php with all the required info
4. Setup the cron job to run everyday.

