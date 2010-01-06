SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

INSERT INTO `user` VALUES(NULL, 'Max Mustermann', 'uhgfk', 1, '2009-02-10 14:15:48', '2009-02-09 11:58:12');
INSERT INTO `user` VALUES(NULL, 'Otto Normalverbraucher', '6bf90a', 1, '2009-02-28 19:04:42', '2009-02-28 19:05:47');
INSERT INTO `user` VALUES(NULL, 'Erika Mustermann', 'ks15f', 1, '2009-02-14 22:17:55', '2009-02-10 14:47:10');
INSERT INTO `user` VALUES(NULL, 'Lieschen Müller', 'a8gjk', 1, '2009-02-14 22:17:55', '2009-02-10 14:47:10');


INSERT INTO `maleteachers_stats` (`id`, `for_id`, `set_id`, `vote_number`, `uid`, `time`) VALUES
(1668, 1, 7, 1, 130, '2009-10-17 16:20:54'),
(1669, 2, 12, 1, 130, '2009-10-17 16:21:07'),
(1670, 4, 11, 1, 130, '2009-10-17 16:21:21');

--
-- Dumping data for table `combination_students_questions`
--

INSERT INTO `combination_students_questions` VALUES(1, 'Traumpaar');

--
-- Dumping data for table `combination_teachers_female`
--

INSERT INTO `combination_teachers_female` VALUES(4, 'Miller');
INSERT INTO `combination_teachers_female` VALUES(9, 'Schmidt');
INSERT INTO `combination_teachers_female` VALUES(12, 'Schneider');
INSERT INTO `combination_teachers_female` VALUES(18, 'Fischer');
INSERT INTO `combination_teachers_female` VALUES(21, 'Weber');
INSERT INTO `combination_teachers_female` VALUES(23, 'Meyer');
INSERT INTO `combination_teachers_female` VALUES(25, 'Wagner');
INSERT INTO `combination_teachers_female` VALUES(28, 'Becker');
INSERT INTO `combination_teachers_female` VALUES(40, 'Schulz');
INSERT INTO `combination_teachers_female` VALUES(45, 'Hoffmann');
INSERT INTO `combination_teachers_female` VALUES(48, 'Schäfer');
INSERT INTO `combination_teachers_female` VALUES(52, 'Koch');
INSERT INTO `combination_teachers_female` VALUES(53, 'Bauer');

--
-- Dumping data for table `combination_teachers_male`
--

INSERT INTO `combination_teachers_male` VALUES(1, 'Richter');
INSERT INTO `combination_teachers_male` VALUES(2, 'Klein');
INSERT INTO `combination_teachers_male` VALUES(3, 'Wolf');
INSERT INTO `combination_teachers_male` VALUES(5, 'Schröder');
INSERT INTO `combination_teachers_male` VALUES(6, 'Neumann');
INSERT INTO `combination_teachers_male` VALUES(7, 'Schwarz');
INSERT INTO `combination_teachers_male` VALUES(8, 'Krüger');
INSERT INTO `combination_teachers_male` VALUES(10, 'Hartmann');
INSERT INTO `combination_teachers_male` VALUES(11, 'Lange');
INSERT INTO `combination_teachers_male` VALUES(13, 'Schmitt');
INSERT INTO `combination_teachers_male` VALUES(14, 'Krause');
INSERT INTO `combination_teachers_male` VALUES(15, 'Lehmann');

--
-- Dumping data for table `combination_teachers_questions`
--

INSERT INTO `combination_teachers_questions` VALUES(1, 'Lehrertraumpaar');

--
-- Dumping data for table `femalestudents_questions`
--

INSERT INTO `femalestudents_questions` VALUES(1, 'Tratschtante');
INSERT INTO `femalestudents_questions` VALUES(2, 'Streber');
INSERT INTO `femalestudents_questions` VALUES(3, 'Knackarsch');
INSERT INTO `femalestudents_questions` VALUES(4, 'Wer bekommt zuerst ein Kind?');
INSERT INTO `femalestudents_questions` VALUES(5, 'Toilettengänger');
INSERT INTO `femalestudents_questions` VALUES(6, 'Hartz-IV-Empfänger');
INSERT INTO `femalestudents_questions` VALUES(7, 'Millionär');
INSERT INTO `femalestudents_questions` VALUES(8, 'Politiker');
INSERT INTO `femalestudents_questions` VALUES(9, 'Dummschwätzer');
INSERT INTO `femalestudents_questions` VALUES(10, 'Meister der Abwesenheit');
INSERT INTO `femalestudents_questions` VALUES(11, 'Schleimer');
INSERT INTO `femalestudents_questions` VALUES(12, 'Bäckersucht');
INSERT INTO `femalestudents_questions` VALUES(13, 'Raucher');
INSERT INTO `femalestudents_questions` VALUES(14, 'Lästermaul');
INSERT INTO `femalestudents_questions` VALUES(15, 'Party People');
INSERT INTO `femalestudents_questions` VALUES(16, 'Sportlichste');
INSERT INTO `femalestudents_questions` VALUES(17, 'Optimist');
INSERT INTO `femalestudents_questions` VALUES(18, 'Ausländer');
INSERT INTO `femalestudents_questions` VALUES(19, 'Traumschwiegertochter');
INSERT INTO `femalestudents_questions` VALUES(20, 'Öko');
INSERT INTO `femalestudents_questions` VALUES(21, 'Zicke');
INSERT INTO `femalestudents_questions` VALUES(22, 'Klassenclown');
INSERT INTO `femalestudents_questions` VALUES(23, 'Kaffeejunkie');
INSERT INTO `femalestudents_questions` VALUES(24, 'Künstlerin');
INSERT INTO `femalestudents_questions` VALUES(25, 'Bestaussehendste');
INSERT INTO `femalestudents_questions` VALUES(26, 'Auffälligstes Lachen');
INSERT INTO `femalestudents_questions` VALUES(27, 'Katastrophenfahrer');
INSERT INTO `femalestudents_questions` VALUES(28, 'Zuspätkommer');
INSERT INTO `femalestudents_questions` VALUES(29, 'Verpeilteste');
INSERT INTO `femalestudents_questions` VALUES(30, 'Tollste Haare');
INSERT INTO `femalestudents_questions` VALUES(31, 'Extrovertierteste');
INSERT INTO `femalestudents_questions` VALUES(32, 'Bester Kleidungsstil');
INSERT INTO `femalestudents_questions` VALUES(33, 'Gangster');
INSERT INTO `femalestudents_questions` VALUES(34, 'Chaot');
INSERT INTO `femalestudents_questions` VALUES(35, 'Sarkasmus in Person');
INSERT INTO `femalestudents_questions` VALUES(36, 'Chiller');
INSERT INTO `femalestudents_questions` VALUES(37, 'Faulste');
INSERT INTO `femalestudents_questions` VALUES(38, 'Spickzettelkönigin');
INSERT INTO `femalestudents_questions` VALUES(39, 'Min/Max Prinzip *Min. Aufwand, Max. Ergebnis*');
INSERT INTO `femalestudents_questions` VALUES(40, 'Organisationstalent');
INSERT INTO `femalestudents_questions` VALUES(41, 'Wer wird uns ewig ein Rätsel bleiben?');
INSERT INTO `femalestudents_questions` VALUES(42, 'Lehrerliebling');

--
-- Dumping data for table `femaleteachers_questions`
--

INSERT INTO `femaleteachers_questions` VALUES(1, 'Beste Lehrerin');
INSERT INTO `femaleteachers_questions` VALUES(2, '''bester'' Tafelanschrieb');
INSERT INTO `femaleteachers_questions` VALUES(3, 'Bestaussehendste');
INSERT INTO `femaleteachers_questions` VALUES(4, 'Auffälligstes Lachen');
INSERT INTO `femaleteachers_questions` VALUES(5, 'Katastrophenfahrer');
INSERT INTO `femaleteachers_questions` VALUES(6, 'Zuspätkommer');
INSERT INTO `femaleteachers_questions` VALUES(7, 'Verpeilteste');
INSERT INTO `femaleteachers_questions` VALUES(8, 'Tollste Haare');
INSERT INTO `femaleteachers_questions` VALUES(9, 'Extrovertierteste');
INSERT INTO `femaleteachers_questions` VALUES(10, 'Bester Kleidungsstil');
INSERT INTO `femaleteachers_questions` VALUES(11, 'Sarkasmus in Person');
INSERT INTO `femaleteachers_questions` VALUES(12, 'Bestechlich');
INSERT INTO `femaleteachers_questions` VALUES(13, 'Spickzettel Entdeckerin');
INSERT INTO `femaleteachers_questions` VALUES(14, 'Schönstes Auto');
INSERT INTO `femaleteachers_questions` VALUES(15, 'Vom-Thema-Abschweiferin');
INSERT INTO `femaleteachers_questions` VALUES(16, 'Organisationstalent');
INSERT INTO `femaleteachers_questions` VALUES(17, 'Wer wird uns ewig ein Rätsel bleiben?');

--
-- Dumping data for table `femaleteachers_teachers`
--

INSERT INTO `femaleteachers_teachers` VALUES(1, 'Schneider');
INSERT INTO `femaleteachers_teachers` VALUES(2, 'Fischer');
INSERT INTO `femaleteachers_teachers` VALUES(3, 'Weber');
INSERT INTO `femaleteachers_teachers` VALUES(4, 'Meyer');
INSERT INTO `femaleteachers_teachers` VALUES(5, 'Wagner');
INSERT INTO `femaleteachers_teachers` VALUES(6, 'Becker');
INSERT INTO `femaleteachers_teachers` VALUES(7, 'Schulz');

--
-- Dumping data for table `malestudents_questions`
--

INSERT INTO `malestudents_questions` VALUES(1, 'Tratschtante');
INSERT INTO `malestudents_questions` VALUES(2, 'Streber');
INSERT INTO `malestudents_questions` VALUES(3, 'Knackarsch');
INSERT INTO `malestudents_questions` VALUES(4, 'Wer bekommt zuerst ein Kind?');
INSERT INTO `malestudents_questions` VALUES(5, 'Toilettengänger');
INSERT INTO `malestudents_questions` VALUES(6, 'Hartz-IV-Empfänger');
INSERT INTO `malestudents_questions` VALUES(7, 'Millionär');
INSERT INTO `malestudents_questions` VALUES(8, 'Politiker');
INSERT INTO `malestudents_questions` VALUES(9, 'Dummschwätzer');
INSERT INTO `malestudents_questions` VALUES(10, 'Meister der Abwesenheit');
INSERT INTO `malestudents_questions` VALUES(11, 'Schleimer');
INSERT INTO `malestudents_questions` VALUES(12, 'Bäckersucht');
INSERT INTO `malestudents_questions` VALUES(13, 'Raucher');
INSERT INTO `malestudents_questions` VALUES(14, 'Lästermaul');
INSERT INTO `malestudents_questions` VALUES(15, 'Party People');
INSERT INTO `malestudents_questions` VALUES(16, 'Sportlichster');
INSERT INTO `malestudents_questions` VALUES(17, 'Optimist');
INSERT INTO `malestudents_questions` VALUES(18, 'Ausländer');
INSERT INTO `malestudents_questions` VALUES(19, 'Traumschwiegersohn');
INSERT INTO `malestudents_questions` VALUES(20, 'Öko');
INSERT INTO `malestudents_questions` VALUES(21, 'Zicke');
INSERT INTO `malestudents_questions` VALUES(22, 'Klassenclown');
INSERT INTO `malestudents_questions` VALUES(23, 'Kaffeejunkie');
INSERT INTO `malestudents_questions` VALUES(24, 'Künstler');
INSERT INTO `malestudents_questions` VALUES(25, 'Bestaussehendster');
INSERT INTO `malestudents_questions` VALUES(26, 'Auffälligstes Lachen');
INSERT INTO `malestudents_questions` VALUES(27, 'Katastrophenfahrer');
INSERT INTO `malestudents_questions` VALUES(28, 'Zuspätkommer');
INSERT INTO `malestudents_questions` VALUES(29, 'Verpeiltester');
INSERT INTO `malestudents_questions` VALUES(30, 'Tollste Haare');
INSERT INTO `malestudents_questions` VALUES(31, 'Extrovertiertester');
INSERT INTO `malestudents_questions` VALUES(32, 'Bester Kleidungsstil');
INSERT INTO `malestudents_questions` VALUES(34, 'Gangster');
INSERT INTO `malestudents_questions` VALUES(35, 'Chaot');
INSERT INTO `malestudents_questions` VALUES(36, 'Sarkasmus in Person');
INSERT INTO `malestudents_questions` VALUES(37, 'Alkoholvernichter');
INSERT INTO `malestudents_questions` VALUES(38, 'Chiller');
INSERT INTO `malestudents_questions` VALUES(39, 'Faulster');
INSERT INTO `malestudents_questions` VALUES(40, 'Spickzettelkönig');
INSERT INTO `malestudents_questions` VALUES(41, 'Min/Max Prinzip *Min. Aufwand, Max. Ergebnis*');
INSERT INTO `malestudents_questions` VALUES(42, 'Organisationstalent');
INSERT INTO `malestudents_questions` VALUES(43, 'Wer wird uns ewig ein Rätsel bleiben?');
INSERT INTO `malestudents_questions` VALUES(44, 'Lehrerliebling');

--
-- Dumping data for table `maleteachers_questions`
--

INSERT INTO `maleteachers_questions` VALUES(1, 'Bestaussehendster');
INSERT INTO `maleteachers_questions` VALUES(2, 'Auffälligstes Lachen');
INSERT INTO `maleteachers_questions` VALUES(3, 'Katastrophenfahrer');
INSERT INTO `maleteachers_questions` VALUES(4, 'Zuspätkommer');
INSERT INTO `maleteachers_questions` VALUES(5, 'Verpeiltester');
INSERT INTO `maleteachers_questions` VALUES(6, 'Tollste Haare');
INSERT INTO `maleteachers_questions` VALUES(7, 'Extrovertiertester');
INSERT INTO `maleteachers_questions` VALUES(8, 'Bester Kleidungsstil');
INSERT INTO `maleteachers_questions` VALUES(9, 'Bester Lehrer');
INSERT INTO `maleteachers_questions` VALUES(10, '''bester'' Tafelanschrieb');
INSERT INTO `maleteachers_questions` VALUES(11, 'Sarkasmus in Person');
INSERT INTO `maleteachers_questions` VALUES(12, 'Bestechlich');
INSERT INTO `maleteachers_questions` VALUES(13, 'Spickzettel Entdecker');
INSERT INTO `maleteachers_questions` VALUES(14, 'Schönstes Auto');
INSERT INTO `maleteachers_questions` VALUES(15, 'Vom-Thema-Abschweifer');
INSERT INTO `maleteachers_questions` VALUES(16, 'Organisationstalent');
INSERT INTO `maleteachers_questions` VALUES(17, 'Wer wird uns ewig ein Rätsel bleiben?');

--
-- Dumping data for table `maleteachers_teachers`
--

INSERT INTO `maleteachers_teachers` VALUES(1, 'Schneider');
INSERT INTO `maleteachers_teachers` VALUES(2, 'Fischer');
INSERT INTO `maleteachers_teachers` VALUES(3, 'Weber');
INSERT INTO `maleteachers_teachers` VALUES(4, 'Meyer');
INSERT INTO `maleteachers_teachers` VALUES(5, 'Wagner');
INSERT INTO `maleteachers_teachers` VALUES(6, 'Becker');
INSERT INTO `maleteachers_teachers` VALUES(7, 'Schulz');
