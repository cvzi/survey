SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `survey`
--

-- --------------------------------------------------------

--
-- Table structure for table `combination_students_female`
--

CREATE TABLE `combination_students_female` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `text` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=67 ;

-- --------------------------------------------------------

--
-- Table structure for table `combination_students_male`
--

CREATE TABLE `combination_students_male` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `text` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=63 ;

-- --------------------------------------------------------

--
-- Table structure for table `combination_students_questions`
--

CREATE TABLE `combination_students_questions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `text` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `combination_students_stats`
--

CREATE TABLE `combination_students_stats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `for_id` int(11) NOT NULL COMMENT 'id from foreach e.g. teacherquestion',
  `set_id` tinyint(4) NOT NULL DEFAULT '1' COMMENT 'compatibility to non-combination',
  `set_combination_students_male_id` int(11) NOT NULL COMMENT 'id from combination_students_male',
  `set_combination_students_female_id` int(11) NOT NULL COMMENT 'id from combination_students_female',
  `vote_number` tinyint(4) NOT NULL DEFAULT '1',
  `uid` int(11) NOT NULL COMMENT 'User id',
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=106 ;

-- --------------------------------------------------------

--
-- Table structure for table `combination_teachers_female`
--

CREATE TABLE `combination_teachers_female` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `text` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=54 ;

-- --------------------------------------------------------

--
-- Table structure for table `combination_teachers_male`
--

CREATE TABLE `combination_teachers_male` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `text` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=52 ;

-- --------------------------------------------------------

--
-- Table structure for table `combination_teachers_questions`
--

CREATE TABLE `combination_teachers_questions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `text` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `combination_teachers_stats`
--

CREATE TABLE `combination_teachers_stats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `for_id` int(11) NOT NULL COMMENT 'id from foreach e.g. teacherquestion',
  `set_id` tinyint(4) NOT NULL DEFAULT '1' COMMENT 'compatibility to non-combination',
  `set_combination_teachers_male_id` int(11) NOT NULL COMMENT 'id from combination_teachers_male',
  `set_combination_teachers_female_id` int(11) NOT NULL COMMENT 'id from combination_teachers_female',
  `vote_number` tinyint(4) NOT NULL DEFAULT '1',
  `uid` int(11) NOT NULL COMMENT 'User id',
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=135 ;

-- --------------------------------------------------------

--
-- Table structure for table `femalestudents_questions`
--

CREATE TABLE `femalestudents_questions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `text` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=43 ;

-- --------------------------------------------------------

--
-- Table structure for table `femalestudents_stats`
--

CREATE TABLE `femalestudents_stats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `for_id` int(11) NOT NULL COMMENT 'id from foreach e.g. teacherquestion',
  `set_id` int(11) NOT NULL COMMENT 'id from a e.g. teacher',
  `vote_number` tinyint(4) NOT NULL DEFAULT '1',
  `uid` int(11) NOT NULL COMMENT 'User id',
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3940 ;

-- --------------------------------------------------------

--
-- Table structure for table `femalestudents_students`
--

CREATE TABLE `femalestudents_students` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `text` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=67 ;

-- --------------------------------------------------------

--
-- Table structure for table `femaleteachers_questions`
--

CREATE TABLE `femaleteachers_questions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `text` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=18 ;

-- --------------------------------------------------------

--
-- Table structure for table `femaleteachers_stats`
--

CREATE TABLE `femaleteachers_stats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `for_id` int(11) NOT NULL COMMENT 'id from foreach e.g. teacherquestion',
  `set_id` int(11) NOT NULL COMMENT 'id from a e.g. teacher',
  `vote_number` tinyint(4) NOT NULL DEFAULT '1',
  `uid` int(11) NOT NULL COMMENT 'User id',
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1564 ;

-- --------------------------------------------------------

--
-- Table structure for table `femaleteachers_teachers`
--

CREATE TABLE `femaleteachers_teachers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `text` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=14 ;

-- --------------------------------------------------------

--
-- Table structure for table `malestudents_questions`
--

CREATE TABLE `malestudents_questions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `text` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=45 ;

-- --------------------------------------------------------

--
-- Table structure for table `malestudents_stats`
--

CREATE TABLE `malestudents_stats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `for_id` int(11) NOT NULL COMMENT 'id from foreach e.g. teacherquestion',
  `set_id` int(11) NOT NULL COMMENT 'id from a e.g. teacher',
  `vote_number` tinyint(4) NOT NULL DEFAULT '1',
  `uid` int(11) NOT NULL COMMENT 'User id',
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4314 ;

-- --------------------------------------------------------

--
-- Table structure for table `malestudents_students`
--

CREATE TABLE `malestudents_students` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `text` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=63 ;

-- --------------------------------------------------------

--
-- Table structure for table `maleteachers_questions`
--

CREATE TABLE `maleteachers_questions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `text` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=18 ;

-- --------------------------------------------------------

--
-- Table structure for table `maleteachers_stats`
--

CREATE TABLE `maleteachers_stats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `for_id` int(11) NOT NULL COMMENT 'id from foreach e.g. teacherquestion',
  `set_id` int(11) NOT NULL COMMENT 'id from a e.g. teacher',
  `vote_number` tinyint(4) NOT NULL DEFAULT '1',
  `uid` int(11) NOT NULL COMMENT 'User id',
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1668 ;

-- --------------------------------------------------------

--
-- Table structure for table `maleteachers_teachers`
--

CREATE TABLE `maleteachers_teachers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `text` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=41 ;

-- --------------------------------------------------------

--
-- Table structure for table `pagecomments`
--

CREATE TABLE `pagecomments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `displayname` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `text` text COLLATE utf8_unicode_ci NOT NULL,
  `uid` int(11) NOT NULL DEFAULT '0',
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `useridentifier` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=79 ;

-- --------------------------------------------------------

--
-- Table structure for table `stats_history`
--

CREATE TABLE `stats_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `surveyname` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `total` int(11) NOT NULL,
  `for_id` int(11) NOT NULL,
  `for_text` text COLLATE utf8_unicode_ci NOT NULL,
  `sets` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `timestamp` (`timestamp`,`surveyname`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=55570 ;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'This ain''t a hash, we may need to look up passwords. Or sha512',
  `group` tinyint(1) NOT NULL DEFAULT '1',
  `lastlogin` timestamp NULL DEFAULT NULL,
  `lastvote` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `password` (`password`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=130 ;
