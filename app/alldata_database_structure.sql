-- phpMyAdmin SQL Dump
-- version 4.2.3
-- http://www.phpmyadmin.net
--
-- Host: dd33828.kasserver.com
-- Erstellungszeit: 06. Mrz 2015 um 18:24
-- Server Version: 5.5.40-nmm1-log
-- PHP-Version: 5.4.38-nmm1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Datenbank: `d01c339f`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `exports`
--

CREATE TABLE IF NOT EXISTS `exports` (
`id` int(11) NOT NULL,
  `name` varchar(50) COLLATE utf8_bin NOT NULL,
  `project_id` int(11) NOT NULL,
  `value_ids` text COLLATE utf8_bin NOT NULL,
  `format` varchar(5) COLLATE utf8_bin NOT NULL,
  `interval_type` varchar(10) COLLATE utf8_bin NOT NULL,
  `interval_count` int(11) NOT NULL,
  `auth` varchar(40) COLLATE utf8_bin NOT NULL,
  `dateformat` varchar(15) COLLATE utf8_bin NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=13 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `inputs`
--

CREATE TABLE IF NOT EXISTS `inputs` (
`id` int(11) NOT NULL,
  `name` varchar(40) COLLATE utf8_bin NOT NULL,
  `project_id` int(11) NOT NULL,
  `template_path` varchar(255) COLLATE utf8_bin NOT NULL,
  `type` varchar(5) COLLATE utf8_bin NOT NULL,
  `timestamp_pos` varchar(255) COLLATE utf8_bin NOT NULL,
  `timestamp_format` varchar(15) COLLATE utf8_bin NOT NULL,
  `delimiter` varchar(5) COLLATE utf8_bin NOT NULL,
  `data_row` int(11) NOT NULL,
  `head_row` int(11) NOT NULL,
  `list_url` varchar(255) COLLATE utf8_bin NOT NULL,
  `link_regex` varchar(255) COLLATE utf8_bin NOT NULL,
  `page_container` varchar(255) COLLATE utf8_bin NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=45 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `inputs_values`
--

CREATE TABLE IF NOT EXISTS `inputs_values` (
`id` int(11) NOT NULL,
  `input_id` int(11) NOT NULL,
  `value_id` int(11) NOT NULL,
  `path` varchar(255) COLLATE utf8_bin NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=64 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `logs`
--

CREATE TABLE IF NOT EXISTS `logs` (
`id` int(11) NOT NULL,
  `type` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `data` text COLLATE utf8_unicode_ci NOT NULL,
  `data_2` text COLLATE utf8_unicode_ci,
  `data_3` text COLLATE utf8_unicode_ci,
  `data_4` text COLLATE utf8_unicode_ci,
  `data_5` text COLLATE utf8_unicode_ci,
  `data_6` text COLLATE utf8_unicode_ci,
  `time` int(11) NOT NULL,
  `error` tinyint(4) NOT NULL,
  `related_id` int(11) DEFAULT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=526015 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `measures`
--

CREATE TABLE IF NOT EXISTS `measures` (
`id` bigint(20) NOT NULL,
  `timestamp` int(11) NOT NULL,
  `data` varchar(255) COLLATE utf8_bin NOT NULL,
  `value_id` int(11) NOT NULL,
  `import_timestamp` int(11) NOT NULL,
  `state` tinyint(4) NOT NULL,
  `conflict_data` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `original_data` text COLLATE utf8_bin NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=588587 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `methods`
--

CREATE TABLE IF NOT EXISTS `methods` (
`id` int(11) NOT NULL,
  `name` varchar(20) COLLATE utf8_bin NOT NULL,
  `description` text COLLATE utf8_bin NOT NULL,
  `params` varchar(255) COLLATE utf8_bin NOT NULL,
  `code` text COLLATE utf8_bin NOT NULL,
  `project_id` int(11) DEFAULT NULL,
  `perTimestamp` tinyint(4) NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=17 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `projects`
--

CREATE TABLE IF NOT EXISTS `projects` (
`id` int(11) NOT NULL,
  `name` varchar(40) COLLATE utf8_bin NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=24 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `projects_users`
--

CREATE TABLE IF NOT EXISTS `projects_users` (
`id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `state` tinyint(4) NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=41 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `units`
--

CREATE TABLE IF NOT EXISTS `units` (
`id` int(11) NOT NULL,
  `name` varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `symbol` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `project_id` int(11) DEFAULT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=38 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `users`
--

CREATE TABLE IF NOT EXISTS `users` (
`id` int(11) NOT NULL,
  `name` varchar(40) COLLATE utf8_bin NOT NULL,
  `description` text COLLATE utf8_bin NOT NULL,
  `email` varchar(40) COLLATE utf8_bin NOT NULL,
  `password` varchar(50) COLLATE utf8_bin NOT NULL,
  `activation_code` varchar(50) COLLATE utf8_bin NOT NULL,
  `register_time` int(11) NOT NULL,
  `activated` tinyint(4) NOT NULL,
  `isAdmin` tinyint(4) NOT NULL,
  `isGuest` tinyint(4) NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=95349411 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `values`
--

CREATE TABLE IF NOT EXISTS `values` (
`id` int(11) NOT NULL,
  `name` varchar(40) COLLATE utf8_bin NOT NULL,
  `project_id` int(11) NOT NULL,
  `unit_id` int(11) NOT NULL,
  `prefix` varchar(1) COLLATE utf8_bin NOT NULL,
  `maximum` double DEFAULT NULL,
  `minimum` double DEFAULT NULL,
  `max_variation` double DEFAULT NULL,
  `error_codes` text COLLATE utf8_bin NOT NULL,
  `method_id` int(11) DEFAULT NULL,
  `interval_count` int(11) NOT NULL,
  `interval_type` int(11) NOT NULL,
  `method_params` text COLLATE utf8_bin NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=54 ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `exports`
--
ALTER TABLE `exports`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inputs`
--
ALTER TABLE `inputs`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inputs_values`
--
ALTER TABLE `inputs_values`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `logs`
--
ALTER TABLE `logs`
 ADD PRIMARY KEY (`id`), ADD KEY `type` (`type`);

--
-- Indexes for table `measures`
--
ALTER TABLE `measures`
 ADD PRIMARY KEY (`id`), ADD KEY `Daten` (`timestamp`,`data`,`value_id`), ADD KEY `value` (`value_id`), ADD KEY `timestamp` (`timestamp`);

--
-- Indexes for table `methods`
--
ALTER TABLE `methods`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `projects_users`
--
ALTER TABLE `projects_users`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `units`
--
ALTER TABLE `units`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `values`
--
ALTER TABLE `values`
 ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `exports`
--
ALTER TABLE `exports`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=13;
--
-- AUTO_INCREMENT for table `inputs`
--
ALTER TABLE `inputs`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=45;
--
-- AUTO_INCREMENT for table `inputs_values`
--
ALTER TABLE `inputs_values`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=64;
--
-- AUTO_INCREMENT for table `logs`
--
ALTER TABLE `logs`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=526015;
--
-- AUTO_INCREMENT for table `measures`
--
ALTER TABLE `measures`
MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=588587;
--
-- AUTO_INCREMENT for table `methods`
--
ALTER TABLE `methods`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=17;
--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=24;
--
-- AUTO_INCREMENT for table `projects_users`
--
ALTER TABLE `projects_users`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=41;
--
-- AUTO_INCREMENT for table `units`
--
ALTER TABLE `units`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=38;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=95349411;
--
-- AUTO_INCREMENT for table `values`
--
ALTER TABLE `values`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=54;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;


--
-- Daten für Tabelle `methods`
--

INSERT INTO `methods` (`id`, `name`, `description`, `params`, `code`, `project_id`, `perTimestamp`) VALUES
(1, 'Mean', 'Calculates the mean of a range', 'value:val:The value from which the result is calculated', '$c = 0;\r\nforeach($value[''data''] as $i => $m) {\r\n	 $c += $m[''data''];\r\n}\r\nreturn $c == 0 ? 0 : $c / count($value[''data'']);', NULL, 0),
(2, 'Max', 'Calculates the max value of a range', 'value:val:The value from which the result is calculated', 'foreach($value[''data''] as $i => $m) {\r\n	if(!isset($h)) $h = $m[''data''];\r\n     if($m[''data''] > $h) {\r\n        $h = $m[''data''];\r\n    }\r\n}\r\nreturn $h;', NULL, 0),
(3, 'Min', 'Calculates the minimum of a range', 'value:val:The value from which the result is calculated', 'foreach($value[''data''] as $i => $m) {\r\n	if(!isset($h)) $h = $m[''data''];\r\n     if($m[''data''] < $h) {\r\n        $h = $m[''data''];\r\n    }\r\n}\r\nreturn $h;', NULL, 0),
(5, 'Sum', 'Calculates the sum of all data points.', 'value:val:The value from which the result is calculated', '$last = end($results);\r\n$sum = $last[''data''] ? $last[''data''] : 0;\r\nforeach($value[''data''] as $i => $m) {\r\n	if(!is_numeric($m[''data'']))\r\n    	$m[''data''] = 0;\r\n	$sum += $m[''data''];\r\n}\r\nreturn $sum;', NULL, 0),
(6, 'Difference', 'Calculates the difference between all data points.', 'value:val:The value from which the result is calculated', '$m = $value[''data''][0];\r\n$last = end($value[''past'']);\r\nif(!$last) \r\n	$last = $m;\r\n\r\n$d = $m[''data''] - $last[''data''];\r\n\r\nreturn $d;\r\n', NULL, 0),
(12, 'SetToMin', 'Sets all measures smaller than the provided minimum value to this minimum value.', 'value:val:The value from which the result is calculated\r\nminimum:num:The minimum to which smaller data should be set', 'if($value[''data''][0][''data''] < $minimum)\r\n	return $minimum;\r\nelse\r\n	return $value[''data''][0][''data''];', NULL, 1),
(14, 'Median', 'Calculates the median of the data range.', 'value:val:The value from which the result is calculated', '$l = count($value[''data'']);\r\nsort($value[''data'']);\r\n$m = 0;\r\n//debug($value[''data'']);\r\nif($l % 2 == 0) {\r\n    $c = $l / 2;\r\n    $m = ($value[''data''][$c][''data''] + $value[''data''][$c + 1][''data'']) / 2;\r\n} else {\r\n	$c = ($l + 1) / 2;\r\n	$m = $value[''data''][$c][''data''];\r\n}\r\nreturn $m;\r\n\r\n', NULL, 0);

--
-- Daten für Tabelle `units`
--

INSERT INTO `units` (`id`, `name`, `symbol`, `project_id`) VALUES
(1, 'Meter', 'm', NULL),
(2, 'Second', 's', NULL),
(3, 'Degree Celsius', '°C', NULL),
(4, 'Gram', 'g', NULL),
(5, 'Ampere', 'A', NULL),
(6, 'Meter per second', 'm/s', NULL),
(8, 'Volt', 'V', NULL),
(9, 'Text', 'Text', NULL),
(10, 'Farad', 'F', NULL),
(11, 'Liter', 'l', NULL),
(12, 'Watt', 'W', NULL),
(13, 'Mol', 'mol', NULL),
(14, 'Watt per square meter', 'W/m^2', NULL),
(15, 'Percent', '%', NULL),
(16, 'Pascal', 'Pa', NULL),
(17, 'Meter Wassersäule', 'WS', NULL),
(18, 'Siemens per Centimeter', 'S/cm', NULL),
(19, 'Number', 'Num', NULL),
(20, 'Nephelometric Turbidity Unit', 'NTU', NULL),
(21, 'Gram per liter', 'g/L', NULL),
(22, 'Degrees north', 'degN', NULL),
(23, 'Meter per square second', 'm/s^2', NULL);


INSERT INTO `values` (`id`, `name`, `project_id`, `unit_id`, `prefix`, `maximum`, `minimum`, `max_variation`, `error_codes`, `method_id`, `interval_count`, `interval_type`, `method_params`) VALUES
(-1, 'Timestamp', 0, 0, '', 0, 0, NULL, '', 0, 0, 0, '');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
