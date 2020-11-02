-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Värd: 127.0.0.1
-- Tid vid skapande: 02 nov 2020 kl 15:27
-- Serverversion: 10.4.14-MariaDB
-- PHP-version: 7.4.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Databas: `computer_club`
--

DELIMITER $$
--
-- Procedurer
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `AdminCreateProject` (IN `in_date_start` DATE, IN `in_full_description` VARCHAR(4000), IN `in_leader` INT, IN `in_short_description` VARCHAR(300), IN `in_title` VARCHAR(150), IN `areas_array` VARCHAR(254), IN `members_array` VARCHAR(254))  BEGIN
SELECT id FROM projects ORDER BY id DESC LIMIT 1 INTO @current_last_id;
START TRANSACTION;	
    INSERT INTO projects (title, date_start, full_description, leader, short_description) VALUES (in_title, in_date_start, in_full_description, in_leader, in_short_description);
    SET @project_id	= LAST_INSERT_ID();    
CALL AdminUpdateProjectAreas(@project_id, areas_array);
CALL AdminUpdateProjectMembers(@project_id, members_array);
COMMIT;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `AdminGetMembers` ()  BEGIN
SELECT 
members.id,
CONCAT (first_name," ",last_name) as name
FROM members 
INNER JOIN people ON members.person = people.id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `AdminGetProjectByID` (IN `proj_id` INT)  NO SQL
SELECT projects.id, full_description, leader, result, short_description,status, date_end, date_start, title, GROUP_CONCAT(areas.type SEPARATOR ', ') as areas_array 
FROM projects 
LEFT JOIN projects_areas ON projects_areas.project = projects.id
LEFT JOIN areas ON projects_areas.area = areas.id
WHERE projects.id=proj_id
GROUP BY projects.id$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `AdminGetProjectMembers` (IN `proj_id` INT)  NO SQL
SELECT first_name, last_name, members.id 
FROM projects_members 
LEFT JOIN members ON projects_members.member = members.id
LEFT JOIN people ON members.person=people.id
WHERE projects_members.project=proj_id$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `AdminUpdateProject` (IN `proj_id` INT, IN `in_date_end` DATE, IN `in_full_description` VARCHAR(4000), IN `in_leader` INT, IN `in_result` VARCHAR(2000), IN `in_short_description` VARCHAR(300), IN `in_title` VARCHAR(150), IN `areas_array` VARCHAR(254), IN `members_array` VARCHAR(254))  BEGIN
SET @in_status='active';
START TRANSACTION;
IF (in_date_end IS NULL OR in_date_end = '0000-00-00') THEN
SET @in_status='active';
SET in_date_end = NULL; 
ELSE
SET @in_status ='finish'; 
END IF;
UPDATE projects
SET date_end=in_date_end, 
status=@in_status,
full_description=in_full_description,
leader = in_leader,
result = in_result,
short_description =in_short_description,
title=in_title
WHERE id=proj_id;
CALL AdminUpdateProjectAreas(proj_id, areas_array);
CALL AdminUpdateProjectMembers(proj_id, members_array);
COMMIT;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `AdminUpdateProjectAreas` (IN `proj_id` INT, IN `areas_array` VARCHAR(254))  BEGIN
SET @A=REPLACE(areas_array,' ','');
	DELETE FROM projects_areas WHERE project=proj_id AND (FIND_IN_SET(area,@A)=0 OR 
   FIND_IN_SET(area,@A) IS NULL);
 
    WHILE(LOCATE(', ', areas_array)>0) 
DO
    INSERT INTO projects_areas (project,area) 
              VALUES (proj_id, CAST(SUBSTRING_INDEX(areas_array, ', ', 1) AS INTEGER)) ON DUPLICATE KEY UPDATE project=VALUES(project),area=VALUES(area);
    
   SET areas_array = SUBSTRING(areas_array FROM LOCATE(', ', areas_array) + 2);   
END WHILE;
IF NOT areas_array = "" THEN  
   INSERT INTO projects_areas (project, area) VALUES (proj_id, CAST(areas_array AS INTEGER))
   ON DUPLICATE KEY UPDATE project=VALUES(project),area=VALUES(area); 
   END IF;
   END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `AdminUpdateProjectMembers` (IN `proj_id` INT, IN `members_array` VARCHAR(254))  BEGIN
SET @A=REPLACE(members_array,' ','');
	DELETE FROM projects_members WHERE project=proj_id AND (FIND_IN_SET(member,@A)=0 OR 
   FIND_IN_SET(member,@A) IS NULL);
 
    WHILE(LOCATE(', ', members_array)>0) 
DO
    INSERT INTO projects_members (project,member) 
              VALUES (proj_id, CAST(SUBSTRING_INDEX(members_array, ', ', 1) AS INTEGER)) ON DUPLICATE KEY UPDATE project=VALUES(project),member=VALUES(member);
    
   SET members_array = SUBSTRING(members_array FROM LOCATE(', ', members_array) + 2);   
END WHILE;
IF NOT members_array = "" THEN  
   INSERT INTO projects_members (project, member) VALUES (proj_id, CAST(members_array AS INTEGER))
   ON DUPLICATE KEY UPDATE project=VALUES(project),member=VALUES(member); 
   END IF;
   END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `deleteMember` (IN `member_id` INT)  NO SQL
BEGIN
DECLARE userID, personID, pub int;

SELECT user INTO userID FROM members WHERE members.id = member_id;
SELECT person INTO personID FROM members WHERE members.id = member_id;

DELETE FROM members WHERE members.id = member_id;
DELETE FROM users WHERE users.id = userID;
DELETE FROM projects_members WHERE projects_members.member = member_id;

SELECT COUNT(*) INTO pub FROM publications_people WHERE publications_people.person = personID;

IF pub < 1 THEN
DELETE FROM people WHERE people.id = personID;
END IF;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetAreas` ()  BEGIN
SELECT * FROM areas;
UPDATE audit SET times_called = Times_called+1 WHERE Procedure_name='SelectFromAreas';
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `getAuthorPublication` (IN `input_id` INT)  NO SQL
BEGIN
select 
	publications.id as pub_id, 
    concat(first_name,' ', last_name) as name, 
    members.id as member, members.id as id 
    from publications INNER JOIN publications_people on publications.id = publications_people.publication
INNER JOIN people ON publications_people.person = people.id left join members on people.id = members.person where publications.id = input_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetFilteredListProjects` (IN `project_status` VARCHAR(10), IN `search_word` VARCHAR(50), IN `project_start_from` VARCHAR(10), IN `project_start_to` VARCHAR(10), IN `project_end_from` VARCHAR(10), IN `project_end_to` VARCHAR(10), IN `size` INT, IN `start_id` INT, IN `research_areas` VARCHAR(254))  BEGIN 

SET @AA= CONCAT("SELECT projects.id, projects.title, short_description, status, date_start, date_end, GROUP_CONCAT(areas.type SEPARATOR ', ') as all_areas, leader, first_name, last_name, titles.type FROM projects RIGHT JOIN projects_areas ON projects.id=projects_areas.project LEFT JOIN areas on projects_areas.area=areas.id LEFT JOIN members on leader=members.id LEFT JOIN people on members.person=people.id LEFT JOIN titles on members.title=titles.id WHERE ");
IF start_id IS NULL THEN
SET @A=true; 
ELSE 
SET @A=CONCAT("( projects.id <= ",start_id,")"); END IF;
IF project_status IS NULL THEN
SET @B=true;
ELSE
SET @B=CONCAT("( projects.status = '",project_status,"' )"); END IF;

IF search_word IS NULL THEN
SET @C=true;    
ELSE            
SET @C =CONCAT("(projects.title LIKE ('%",search_word,"%') OR projects.short_description LIKE ('%",search_word,"%'))"); END IF;

IF project_start_from IS NULL OR project_start_to IS NULL THEN 
SET @D=true;
ELSE
SET @D=CONCAT("(projects.date_start BETWEEN '",project_start_from,"' AND '",project_start_to,"')"); END IF;

IF project_end_from IS NULL OR project_end_to IS NULL THEN
SET @E=true;
ELSE 
SET @E=CONCAT("(projects.date_end BETWEEN '",project_end_from,"' AND '",project_end_to,"')"); END IF;

IF research_areas IS NULL THEN 
SET @F=" Group BY projects.id";
ELSE
SET @F=CONCAT(" GROUP BY projects.id HAVING ifProjectHasAreas( '",research_areas,"', all_areas)>0"); END IF;
              
SET @G=CONCAT(" ORDER BY projects.id DESC LIMIT ",size,";"); 
SET @pr=CONCAT(@AA, @A, " AND ",@B," AND ",@C," AND ",@D," AND ",@E,@F, @G);
PREPARE stmt FROM @pr;
EXECUTE stmt;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetFilteredProjects` (IN `project_status` VARCHAR(10), IN `search_word` VARCHAR(50), IN `project_start_from` VARCHAR(10), IN `project_start_to` VARCHAR(10), IN `project_end_from` VARCHAR(10), IN `project_end_to` VARCHAR(10), IN `size` INT, IN `start_id` INT, IN `research_areas` VARCHAR(254), IN `direction` VARCHAR(4))  BEGIN 

SET @AA= CONCAT("SELECT s.* FROM (SELECT projects.id as id, projects.title, short_description, status, date_start, date_end, GROUP_CONCAT(areas.type SEPARATOR ', ') as all_areas, leader, first_name, last_name, titles.type FROM projects LEFT JOIN projects_areas ON projects.id=projects_areas.project LEFT JOIN areas on projects_areas.area=areas.id LEFT JOIN members on leader=members.id LEFT JOIN people on members.person=people.id LEFT JOIN titles on members.title=titles.id WHERE ");
IF start_id IS NULL OR start_id=0 THEN
SET @A=true; 
ELSEIF direction LIKE 'next' THEN 
SET @A=CONCAT("( projects.id < ",start_id,")"); 
ELSEIF direction LIKE 'prev' THEN
INSERT INTO audit VALUES (direction, 2);  
SET @A=CONCAT("( projects.id > ",start_id,")"); 
END IF;
IF project_status IS NULL THEN
SET @B=true;
ELSE
SET @B=CONCAT("( projects.status = '",project_status,"' )"); END IF;

IF search_word IS NULL THEN
SET @C=true;    
ELSE            
SET @C =CONCAT("(projects.title LIKE ('%",search_word,"%') OR projects.short_description LIKE ('%",search_word,"%'))"); END IF;

IF project_start_from IS NULL OR project_start_to IS NULL THEN 
SET @D=true;
ELSE
SET @D=CONCAT("(projects.date_start BETWEEN '",project_start_from,"' AND '",project_start_to,"')"); END IF;

IF project_end_from IS NULL OR project_end_to IS NULL THEN
SET @E=true;
ELSE 
SET @E=CONCAT("(projects.date_end BETWEEN '",project_end_from,"' AND '",project_end_to,"')"); END IF;

IF research_areas IS NULL THEN 
SET @F=" Group BY projects.id";
ELSE
SET @F=CONCAT(" GROUP BY projects.id HAVING ifProjectHasAreas( '",research_areas,"', all_areas)>0"); END IF;

IF direction LIKE 'next' THEN
SET @G=CONCAT(" ORDER BY projects.id DESC LIMIT ",size,") s"); 
ELSEIF direction LIKE 'prev' THEN
SET @G=CONCAT(" ORDER BY projects.id ASC LIMIT ",size,") s"); 
END If;

SET @H=CONCAT(" ORDER BY s.id DESC;"); 
SET @pr=CONCAT(@AA, @A, " AND ",@B," AND ",@C," AND ",@D," AND ",@E,@F, @G, @H);
PREPARE stmt FROM @pr;
EXECUTE stmt;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `getMember` (IN `input_id` INT)  BEGIN
	SELECT
    members.id as id,
    	first_name,
        last_name,
        titles.type as title,
        areas.type as area,
        email,
        phone,
        avatar,
        biography
	FROM
    	members inner join people on members.person = people.id INNER JOIN titles on members.title = titles.id 
        LEFT JOIN members_areas on members.id = members_areas.member LEFT JOIN areas on members_areas.area = areas.id WHERE members.id = input_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `getMemberAreas` (IN `input_id` INT)  BEGIN
select areas.type from members inner join members_areas on members.id = members_areas.member inner join areas on members_areas.area = areas.id Where members.id = input_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `getMemberProjects` (IN `input_id` INT)  BEGIN
	SELECT
    	projects.id as id,
    	title,
        short_description,
        status
    FROM
    projects INNER JOIN projects_members on projects.id = projects_members.project WHERE projects_members.member = input_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `getMemberPublications` (IN `input_id` INT)  BEGIN
SELECT publications.title, publications.id as pub_id, date, description, first_name, last_name
FROM publications INNER JOIN publications_people ON publications.id = publications_people.publication
INNER join people ON publications_people.person = people.id 
inner join members ON members.person = people.id
where members.id = input_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetProjectByID` (IN `project_id` INT)  BEGIN
SELECT 
projects.title, 
date_end, 
date_start, 
full_description, 
result, 
status,
GROUP_CONCAT(areas.type SEPARATOR ', ') as all_areas,
leader,
first_name, 
last_name, 
titles.type, 
avatar, 
email, 
phone 
FROM projects
LEFT JOIN projects_areas ON projects.id=projects_areas.project
LEFT JOIN areas ON projects_areas.area=areas.id
LEFT JOIN members on leader=members.id 
LEFT JOIN people on members.person=people.id
LEFT JOIN titles on members.title=titles.id
WHERE projects.id=project_id
Group BY projects.id;
UPDATE audit SET Times_called=Times_called+1 WHERE Procedure_name='GetProjectByID';
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetProjectMembers` (IN `projectID` INT)  BEGIN
SELECT members.id, first_name, last_name, titles.type, avatar FROM members 
RIGHT JOIN projects_members on projects_members.member=members.id 
LEFT JOIN people on members.person=people.id
LEFT JOIN titles on members.title=titles.id
WHERE projects_members.project=projectID;
UPDATE audit SET Times_called=Times_called+1 WHERE Procedure_name='GetProjectMembers';
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetProjectPublications` (IN `project_id` INT)  BEGIN
SELECT 
publications.id,
title,
abstract, 
date, 
description, 
file_name,
publication_type.type
FROM publications 
LEFT JOIN publications_projects ON publications.id = publications_projects.publication
LEFT JOIN publication_type ON publication_type.id = publications.type
WHERE publications_projects.project=project_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetProjectsShortInfo` ()  BEGIN
SELECT projects.id, projects.title, short_description, status, date_start, date_end,  GROUP_CONCAT(areas.type SEPARATOR ', ') as areas, avatar, first_name, last_name, titles.type  
FROM projects 
RIGHT JOIN projects_areas ON projects.id=projects_areas.project 
LEFT JOIN areas on projects_areas.area=areas.id 
LEFT JOIN members on leader=members.id 
LEFT JOIN people on members.person=people.id 
LEFT JOIN titles on members.title=titles.id
GROUP by id;
UPDATE audit SET times_called = Times_called+1 WHERE Procedure_name='GetProjectsShortInfo';
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetPublicationAuthors` (IN `publication_id` INT)  BEGIN
SELECT 
CONCAT (SUBSTR(first_name,1,1),'. ',last_name) as author,
members.id
FROM publications_people
INNER JOIN people ON people.id=publications_people.person
LEFT JOIN members ON publications_people.person=members.person 
WHERE publications_people.publication = publication_id;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `insertMembers_area` (IN `memberID` INT, IN `areaID` INT)  NO SQL
BEGIN
INSERT INTO members_areas (members_areas.member, members_areas.area) VALUES( memberID, areaID);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `insertNewMember` (IN `fname` VARCHAR(50), IN `lname` VARCHAR(50), IN `phone` VARCHAR(20), IN `email` VARCHAR(254), IN `title` VARCHAR(50), IN `area` VARCHAR(50), IN `biography` VARCHAR(2000), IN `user` VARCHAR(50), IN `passwd` VARCHAR(50), IN `img` VARCHAR(50))  BEGIN
DECLARE peopleID, userID, titleID, areaID, memberID int;

SELECT AUTO_INCREMENT INTO peopleID FROM information_schema.TABLES WHERE TABLE_SCHEMA = "computer_club" AND TABLE_NAME = "people";

SELECT AUTO_INCREMENT INTO userID FROM information_schema.TABLES WHERE TABLE_SCHEMA = "computer_club" AND TABLE_NAME = "users";

SELECT AUTO_INCREMENT INTO memberID FROM information_schema.TABLES WHERE TABLE_SCHEMA = "computer_club" AND TABLE_NAME = "members";

SELECT id INTO titleID FROM titles WHERE titles.type = title;

SELECT id INTO areaID FROM areas WHERE areas.type = area;

INSERT INTO people (first_name, last_name) VALUES (fname, lname);

INSERT INTO users (login, password) VALUES (user, passwd);

INSERT INTO members (person, user, phone, email, title, avatar, biography) VALUES (peopleID, userID, phone, email, titleID, img, biography);

INSERT INTO members_areas (member, area) values (memberID, areaID);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `registerMember` (IN `fname` VARCHAR(50), IN `lname` VARCHAR(50), IN `phone` VARCHAR(20), IN `email` VARCHAR(254), IN `title` VARCHAR(50), IN `area` VARCHAR(50), IN `biography` VARCHAR(2000), IN `user` VARCHAR(50), IN `password` VARCHAR(50), IN `image` VARCHAR(50))  BEGIN
insert into new_members (firstname, lastname, phone, email, title, area, biography, username, password, image_info) values (fname, lname, phone, email, title, area, biography, user, password, image);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `removeMember_areas` (IN `id` INT)  NO SQL
BEGIN
DELETE FROM members_areas WHERE members_areas.member = id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `updateImage` (IN `id` INT, IN `img` VARCHAR(255))  NO SQL
BEGIN
UPDATE members SET members.avatar = img WHERE members.id = id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `updateMemberInfo` (IN `id` INT, IN `fname` VARCHAR(50), IN `lname` VARCHAR(50), IN `email` VARCHAR(254), IN `phone` VARCHAR(20), IN `biography` VARCHAR(2000), IN `title` INT)  NO SQL
BEGIN
DECLARE personID int;
SELECT person INTO personID FROM members WHERE members.id = id;

UPDATE members SET members.email = email, members.phone = phone, members.biography = biography WHERE members.id = id;

UPDATE members SET members.title = title WHERE members.id = id;

UPDATE people SET people.first_name = fname, people.last_name = lname WHERE people.id = personID;

END$$

--
-- Funktioner
--
CREATE DEFINER=`root`@`localhost` FUNCTION `GetNextIntElementFromCharArray` (`char_array` CHAR(254)) RETURNS INT(11) RETURN CAST(SUBSTRING_INDEX(char_array,', ', 1) AS INT)$$

CREATE DEFINER=`root`@`localhost` FUNCTION `ifProjectHasAreas` (`areas_array` VARCHAR(254), `all_areas` VARCHAR(254)) RETURNS INT(11) BEGIN

WHILE (TRUE)
DO
	IF (LOCATE(', ', areas_array)=0) THEN 
		RETURN (LOCATE(areas_array, all_areas)>0);
    ELSE  
    SET @value = SUBSTRING_INDEX(areas_array, ', ',1);
    SET areas_array= SUBSTRING(areas_array FROM LOCATE(', ',areas_array) + 2);
	IF(LOCATE(@value, all_areas)=0) THEN
    RETURN 0; 
    END IF;
    END IF;
END WHILE;
RETURN 1;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Ersättningsstruktur för vy `all_members`
-- (See below for the actual view)
--
CREATE TABLE `all_members` (
`id` int(11)
,`first_name` varchar(50)
,`last_name` varchar(50)
,`title` varchar(50)
);

-- --------------------------------------------------------

--
-- Tabellstruktur `areas`
--

CREATE TABLE `areas` (
  `id` int(11) NOT NULL,
  `type` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumpning av Data i tabell `areas`
--

INSERT INTO `areas` (`id`, `type`) VALUES
(1, 'Biomedical Engineering'),
(2, 'Embedded Systems'),
(3, 'Robotics'),
(4, 'Artificial Intelligence'),
(5, 'UI/UX'),
(6, 'Web Development'),
(7, 'Mobile development'),
(8, 'Big data');

-- --------------------------------------------------------

--
-- Tabellstruktur `audit`
--

CREATE TABLE `audit` (
  `Procedure_name` varchar(4000) NOT NULL,
  `Times_called` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumpning av Data i tabell `audit`
--

INSERT INTO `audit` (`Procedure_name`, `Times_called`) VALUES
('next', 1),
('next', 1),
('next', 1),
('next', 1),
('next', 0),
('next', 90),
('next', 90),
('next', 90),
('next', 0),
('next', 0),
('next', 0),
('SELECT s.* FROM (SELECT projects.id as id, projects.title, short_description, status, date_start, date_end, GROUP_CONCAT(areas.type SEPARATOR \', \') as all_areas, leader, first_name, last_name, titles.type FROM projects LEFT JOIN projects_areas ON projects.id=projects_areas.project LEFT JOIN areas on projects_areas.area=areas.id LEFT JOIN members on leader=members.id LEFT JOIN people on members.person=people.id LEFT JOIN titles on members.title=titles.id WHERE 1 AND ( projects.status = \'null\' ) AND (projects.title LIKE (\'%null%\') OR projects.short_description LIKE (\'%null%\')) AND (projects.date_start BETWEEN \'null\' AND \'null\') AND (projects.date_end BETWEEN \'null\' AND \'null\') GROUP BY projects.id HAVING ifProjectHasAreas( \'null\', all_areas)>0 ORDER BY projects.id DESC LIMIT 5) s ORDER BY s.id DESC;', 9),
('next', 90),
('SELECT s.* FROM (SELECT projects.id as id, projects.title, short_description, status, date_start, date_end, GROUP_CONCAT(areas.type SEPARATOR \', \') as all_areas, leader, first_name, last_name, titles.type FROM projects LEFT JOIN projects_areas ON projects.id=projects_areas.project LEFT JOIN areas on projects_areas.area=areas.id LEFT JOIN members on leader=members.id LEFT JOIN people on members.person=people.id LEFT JOIN titles on members.title=titles.id WHERE ( projects.id < 90) AND 1 AND 1 AND 1 AND 1 Group BY projects.id ORDER BY projects.id DESC LIMIT 5) s ORDER BY s.id DESC;', 9),
('next', 0),
('SELECT s.* FROM (SELECT projects.id as id, projects.title, short_description, status, date_start, date_end, GROUP_CONCAT(areas.type SEPARATOR \', \') as all_areas, leader, first_name, last_name, titles.type FROM projects LEFT JOIN projects_areas ON projects.id=projects_areas.project LEFT JOIN areas on projects_areas.area=areas.id LEFT JOIN members on leader=members.id LEFT JOIN people on members.person=people.id LEFT JOIN titles on members.title=titles.id WHERE 1 AND ( projects.status = \'null\' ) AND (projects.title LIKE (\'%null%\') OR projects.short_description LIKE (\'%null%\')) AND (projects.date_start BETWEEN \'null\' AND \'null\') AND (projects.date_end BETWEEN \'null\' AND \'null\') GROUP BY projects.id HAVING ifProjectHasAreas( \'null\', all_areas)>0 ORDER BY projects.id DESC LIMIT 5) s ORDER BY s.id DESC;', 9),
('\"nex', 0),
('prev', 2),
('prev', 2),
('prev', 2),
('prev', 2),
('prev', 2),
('prev', 2),
('prev', 2),
('prev', 2),
('prev', 2),
('prev', 2),
('prev', 2),
('prev', 2),
('prev', 2),
('prev', 2),
('prev', 2),
('prev', 2),
('prev', 2),
('prev', 2),
('prev', 2),
('prev', 2),
('prev', 2),
('prev', 2),
('prev', 2),
('prev', 2),
('prev', 2),
('prev', 2),
('prev', 2),
('prev', 2),
('prev', 2),
('prev', 2),
('prev', 2),
('prev', 2),
('prev', 2),
('prev', 2),
('prev', 2),
('prev', 2),
('prev', 2),
('prev', 2),
('prev', 2),
('prev', 2),
('prev', 2),
('prev', 2),
('prev', 2),
('prev', 2),
('prev', 2),
('prev', 2),
('prev', 2),
('prev', 2),
('prev', 2),
('prev', 2),
('prev', 2),
('prev', 2),
('prev', 2),
('prev', 2),
('prev', 2),
('prev', 2),
('prev', 2),
('prev', 2),
('prev', 2),
('prev', 2),
('prev', 2),
('prev', 2),
('prev', 2),
('prev', 2),
('prev', 2),
('prev', 2),
('prev', 2),
('prev', 2),
('prev', 2),
('prev', 2),
('prev', 2),
('prev', 2),
('prev', 2),
('prev', 2),
('prev', 2),
('prev', 2),
('prev', 2),
('prev', 2),
('prev', 2),
('prev', 2),
('prev', 2),
('prev', 2),
('prev', 2),
('prev', 2),
('prev', 2),
('prev', 2),
('prev', 2),
('prev', 2),
('prev', 2),
('prev', 2),
('prev', 2),
('prev', 2),
('prev', 2),
('prev', 2),
('prev', 2),
('prev', 2),
('prev', 2),
('prev', 2),
('prev', 2),
('prev', 2),
('prev', 2),
('prev', 2),
('prev', 2),
('prev', 2),
('prev', 2);

-- --------------------------------------------------------

--
-- Tabellstruktur `members`
--

CREATE TABLE `members` (
  `id` int(11) NOT NULL,
  `person` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(254) NOT NULL,
  `title` int(11) NOT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `biography` varchar(2000) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumpning av Data i tabell `members`
--

INSERT INTO `members` (`id`, `person`, `user`, `phone`, `email`, `title`, `avatar`, `biography`) VALUES
(1, 1, 1, '076-34343-67', 'mna19002@student.mdh.se', 2, 'marnememewalET.jpg', NULL),
(2, 2, 2, '073-5351355', 'ewn19004@student.mdh.se', 2, 'u2.jpg', NULL),
(3, 3, 3, '070-6666666', 'ihn19002@student.mdh.se', 1, 'u3.jpg', NULL),
(4, 4, 4, '070-5555555', 'lln19011@student.mdh.se', 1, 'u4.jpg', NULL),
(5, 5, 5, '072-54002213', 'eriand18@student.mdh.se', 2, 'u5.jpg', NULL),
(7, 7, 7, NULL, 'wilsmi@mdh.se', 4, 'u7.jpg', NULL),
(8, 8, 8, '072-5678964', 'vicosc@mdh.se', 3, 'u8.jpg', NULL),
(9, 9, 9, NULL, 'parnys@mdh.se', 5, 'u9.jpg', NULL);

-- --------------------------------------------------------

--
-- Tabellstruktur `members_areas`
--

CREATE TABLE `members_areas` (
  `member` int(11) NOT NULL,
  `area` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumpning av Data i tabell `members_areas`
--

INSERT INTO `members_areas` (`member`, `area`) VALUES
(1, 2),
(1, 5),
(2, 5),
(3, 5),
(5, 3),
(5, 4),
(7, 1);

-- --------------------------------------------------------

--
-- Tabellstruktur `new_members`
--

CREATE TABLE `new_members` (
  `id` int(11) NOT NULL,
  `firstname` varchar(50) DEFAULT NULL,
  `lastname` varchar(50) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(254) NOT NULL,
  `title` varchar(50) DEFAULT NULL,
  `area` varchar(50) DEFAULT NULL,
  `biography` varchar(2000) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(50) DEFAULT NULL,
  `image_info` varchar(250) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumpning av Data i tabell `new_members`
--

INSERT INTO `new_members` (`id`, `firstname`, `lastname`, `phone`, `email`, `title`, `area`, `biography`, `username`, `password`, `image_info`) VALUES
(18, 'Gerd', 'Svensk', '021-44321', 'gerd.svensk@mdh.se', 'Student', 'UI/UX', 'Im an old lady', 'gerd', '123', 'u9.jpg'),
(19, 'Sara', 'Young', '076-2344454', 'sayo@mdh.se', 'Student', 'Robotics', NULL, 'saris', '123', NULL),
(20, 'Fred', 'Tysk', '021-223456', 'tysk@mdh.se', 'Professor', 'UI/UX', 'HejHejHej', 'fred', '123', 'u8.jpg'),
(21, 'Anna', 'Paulsson', NULL, 'anna@mdh.se', 'Student', 'UI/UX', 'Det här är lattjo', 'anna', '123', 'u9.jpg');

-- --------------------------------------------------------

--
-- Tabellstruktur `people`
--

CREATE TABLE `people` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumpning av Data i tabell `people`
--

INSERT INTO `people` (`id`, `first_name`, `last_name`) VALUES
(1, 'Mariia', 'Nema'),
(2, 'Emelie', 'Wallin'),
(3, 'Isabella', 'Hansen'),
(4, 'Lucas', 'Larsson'),
(5, 'Erik', 'Andersson'),
(7, 'William', 'Smith'),
(8, 'Victoria', 'Oscarsson'),
(9, 'Pär', 'Nyström'),
(10, 'Kurt', 'Claesson'),
(13, 'Conrad', 'SwanLake');

-- --------------------------------------------------------

--
-- Tabellstruktur `projects`
--

CREATE TABLE `projects` (
  `id` int(11) NOT NULL,
  `status` enum('finish','active') NOT NULL DEFAULT 'active',
  `title` varchar(150) NOT NULL,
  `date_start` date NOT NULL DEFAULT current_timestamp(),
  `date_end` date DEFAULT NULL,
  `short_description` varchar(300) DEFAULT NULL,
  `full_description` varchar(4000) NOT NULL,
  `result` varchar(2000) DEFAULT NULL,
  `leader` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumpning av Data i tabell `projects`
--

INSERT INTO `projects` (`id`, `status`, `title`, `date_start`, `date_end`, `short_description`, `full_description`, `result`, `leader`) VALUES
(2, 'active', 'The secret project', '2020-10-06', NULL, '', '<p>The project is a big secret. No information available.</p>', '', 8),
(6, 'active', 'The Unicorn', '2020-10-08', NULL, 'The vision of UNICORN is to streamline the handling of refuse in housing areas with the help of autonomous robots. ', '<p>The UNICORN project is a collaboration between Mälardalens University, Chalmers University of Technology, Husqvarna Group, Volvo Group Truck Operations, PWS Nordic, HIAB, and Gothenburg Municipality.&nbsp;</p><p>The vision of UNICORN is to streamline the handling of refuse in housing areas with the help of <strong>autonomous robots</strong>.&nbsp;</p><p>The project started in 2017 with the goal to demonstrate the project at Apelsingatan in Gothenburg 2020. All the research partners have taken on different tasks in the project. Chalmers is the coordinator of the project, managing risks, and deadlines, as well as being responsible for creating the control towers that will coordinate and schedule the different parts of the infrastructure.&nbsp;</p><p>PWS is responsible for creating the bins that will be used, as well as a central underground bin where the robots will place the refuse. HIAB is in charge of constructing an autonomous crane that can be used on the trucks that empty the central refuse station.&nbsp;</p><p>Volvo GTO and Husqvarna will assist with the development and with their extensive knowledge. MDH is in charge of creating the small autonomous robots that will work in the housing areas.&nbsp;</p><p>Creating a robot that is supposed to work in an unsupervised urban area presents multiple challenges, especially with regards to safety, security, communication, and navigation. When the robot is fully developed, it is expected to be able to plan a path, retrieve, empty, and return refuse bins, drive autonomously in an urban environment, and communicate its intention of movement.</p>', '', 7),
(82, 'active', 'Test project', '2020-10-22', NULL, 'This is a test project', '<h4>Welcome to test project!</h4><p>&nbsp;</p><p><strong>This should be bold, </strong><i>this should be italic, </i><a href=\"youtube.com\"><i>here is a link to youtube</i></a><i>.&nbsp;</i></p><p>Here should be another paragraph.&nbsp;</p><p>Project\'s chef should be Erik Andersson, the start date is 2020-10-22.<br><br>&nbsp;</p>', '', 5),
(88, 'active', 'Very secret project', '2020-10-24', NULL, 'Very secret project, no more details', '<p>TOP SECRET</p>', '', 9),
(90, 'finish', 'The Study Buddy', '2020-05-01', '2020-08-15', 'Focusing students\' attention through interaction with friendly sloth', '<h4>The project was originally an assignment on UI/UX course.&nbsp;</h4><p>Mobilen tar upp mer och mer tid av våra liv och bidrar även till en ökning av psykisk ohälsa. Mobilens stora tillgänglighet till oändliga flöden med ständig uppdatering gör att dragningskraften till den är enorm och att ett beroende är lätt att skapa. På grund av hur denna ständiga uppkoppling skadar vårt samhälle blev en mobillåda 2019 årets julklapp.&nbsp;</p><p>Förutom att mobilen i sig tar upp mycket tid är den även en distraktionsfaktor vid studier. Vi har därför valt att utveckla mobillådan ytterligare genom att skapa en prototyp med syftet att den ska användas under studier. Tanken är att vår prototyp ska låsa in mobilen under en studiesession för att förhindra mobilanvändning. Dessutom ska prototypen öka motivationen till att studera. Vid utvecklandet av prototypen följde vi Jacob Nielsen’s (1994) principer för interaktionsdesign, bland annat: “Synlighet av system status”, “Överensstämmelse mellan systemet och den verkliga världen”, “Estetisk och minimalistisk design”, “Känna igen istället för att komma ihåg” och “Hjälp och dokumentation”.&nbsp;</p><p>Med “Synlighet av system status” menas att systemet ska ge feedback om vad som händer, exempelvis att en lampa börjar lysa när användaren trycker på en knapp. “Överensstämmelse mellan systemet och den verkliga världen” innebär att användbarheten förenklas om det finns en igenkänningsfaktor för användaren. Användaren bör kunna följa en naturlig och logisk ordning i sitt interagerande. Det bör även finnas en igenkänningsfaktor för exempelvis knappar enligt principen om “Känna igen istället för att komma ihåg”, det vill säga att en startknapp bör se ut som en startknapp och inget annat. “Estetisk och minimalistisk design” syftar till att irrelevant information bör väljas bort. Däremot kan information för att hjälpa användaren att interagera med systemet användas enligt “Hjälp och dokumentation”-principen.&nbsp;</p>', '<p>The result is presented <a href=\"https://youtu.be/vFWW3JR3MfY\\\">here</a></p>', 2),
(98, 'finish', 'Computer club', '2020-10-01', '2020-11-01', 'Grupp 11 Web development project. A website for members in computer club, where they can see club\'s projects and participants of those projects', '<h4>Welcome to computer club!<br>&nbsp;</h4><p>We are glad to welcome you here. Our project is an assignment in <strong>Web development</strong> course at MDH.&nbsp;<br>We create a place where MDH students and scientists can share their experience. Our tasks are:</p><p>- list all active and closed projects that computer club has;<br>- list all members;<br>- implement basic admin operations like create and edit project;<br>- make it possible to register for membership and login to be able to apply for project;<br>- make web application responsible.<br><br>Feel free to join us!&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p>', '', 9),
(101, 'finish', 'I have been up for 22 days', '2020-10-01', '2020-10-30', 'Investigating programmers\' ability to sleep when bugs are still not fixed.', '<p>Welcome to our project! Our goal is to find out a way for programmers to sleep. Here you can see what we know about sleep so far:&nbsp;</p><blockquote><p>&nbsp;<br><strong>Sleep</strong> is a naturally recurring state of mind and body, characterized by altered <a href=\"https://en.wikipedia.org/wiki/Consciousness\">consciousness</a>, relatively inhibited sensory activity, reduced muscle activity and inhibition of nearly all <a href=\"https://en.wikipedia.org/wiki/Voluntary_muscle\">voluntary muscles</a> during <a href=\"https://en.wikipedia.org/wiki/Rapid_eye_movement_sleep\">rapid eye movement</a> (REM) sleep, and reduced interactions with surroundings. It is distinguished from <a href=\"https://en.wikipedia.org/wiki/Wakefulness\">wakefulness</a> by a decreased ability to react to <a href=\"https://en.wikipedia.org/wiki/Stimulus_(physiology)\">stimuli</a>, but more reactive than a <a href=\"https://en.wikipedia.org/wiki/Coma\">coma</a> or <a href=\"https://en.wikipedia.org/wiki/Disorders_of_consciousness\">disorders of consciousness</a>, with sleep displaying very different and active brain patterns.</p></blockquote><p>Sleep occurs in <a href=\"https://en.wikipedia.org/wiki/Sleep_cycle\">repeating periods</a>, in which the body alternates between two distinct modes: <a href=\"https://en.wikipedia.org/wiki/Rapid_eye_movement_sleep\">REM</a> sleep and <a href=\"https://en.wikipedia.org/wiki/Non-rapid_eye_movement_sleep\">non-REM</a> sleep. Although REM stands for \"rapid eye movement\", this mode of sleep has many other aspects, including virtual <a href=\"https://en.wikipedia.org/wiki/Paralysis\">paralysis</a> of the body. A well-known feature of sleep is the <a href=\"https://en.wikipedia.org/wiki/Dream\">dream</a>, an experience typically recounted in <a href=\"https://en.wikipedia.org/wiki/Narrative\">narrative</a> form, which resembles waking life while in progress, but which usually can later be distinguished as fantasy. During sleep, most of the <a href=\"https://en.wikipedia.org/wiki/Human_body\">body\'s systems</a> are in an <a href=\"https://en.wikipedia.org/wiki/Anabolic\">anabolic</a> state, helping to restore the immune, nervous, skeletal, and muscular systems; these are vital processes that maintain mood, memory, and cognitive function, and play a large role in the function of the <a href=\"https://en.wikipedia.org/wiki/Endocrine_system\">endocrine</a> and <a href=\"https://en.wikipedia.org/wiki/Immune_system\">immune systems</a>. The internal <a href=\"https://en.wikipedia.org/wiki/Circadian_clock\">circadian clock</a> promotes sleep daily at night. The diverse purposes and mechanisms of sleep are the subject of substantial ongoing research. Sleep is a highly <a href=\"https://en.wikipedia.org/wiki/Conserved_sequence\">conserved</a> behavior across animal evolution.</p><p>Humans may suffer from various <a href=\"https://en.wikipedia.org/wiki/Sleep_disorder\">sleep disorders</a>, including <a href=\"https://en.wikipedia.org/wiki/Dyssomnia\">dyssomnias</a> such as <a href=\"https://en.wikipedia.org/wiki/Insomnia\">insomnia</a>, <a href=\"https://en.wikipedia.org/wiki/Hypersomnia\">hypersomnia</a>, <a href=\"https://en.wikipedia.org/wiki/Narcolepsy\">narcolepsy</a>, and <a href=\"https://en.wikipedia.org/wiki/Sleep_apnea\">sleep apnea</a>; <a href=\"https://en.wikipedia.org/wiki/Parasomnia\">parasomnias</a> such as <a href=\"https://en.wikipedia.org/wiki/Sleepwalking\">sleepwalking</a> and <a href=\"https://en.wikipedia.org/wiki/Rapid_eye_movement_sleep_behavior_disorder\">rapid eye movement sleep behavior disorder</a>; <a href=\"https://en.wikipedia.org/wiki/Bruxism\">bruxism</a>; and and <a href=\"https://en.wikipedia.org/wiki/Circadian_rhythm_sleep_disorder\">circadian rhythm sleep disorders</a>. The use of <a href=\"https://en.wikipedia.org/wiki/Artificial_light\">artificial light</a> has substantially altered humanity\'s sleep patterns.</p>', '', 1),
(102, 'active', 'Scrolling vs Paging ', '2015-10-15', NULL, 'A UX study on how infinite scrolling influence teenagers', '<h2>Infinite Scrolling</h2><p>Infinite scrolling is a technique that allows users to scroll through a massive chunk of content with no finishing-line in sight. This technique simply keeps refreshing a page when you scroll down it. No matter how good it sounds, the technique isn’t a one-size-fits-all solution for every site or app.</p><h2>Pros #1: User Engagement and Content Discovery</h2><p>When you use scrolling as your prime method of exploring the data, it <i>may</i> make the user to stay longer on your web page, and so increase user engagement. With the popularity of social media, massive amounts of data are being consumed; infinite scrolling offers an <i>efficient way to browse that ocean of information</i>, without extra clicks/taps.</p><p>Infinite scrolling is almost a must-have feature for <i>discovery interfaces</i>. When the user does not search for something specific so they need to see a large amount of items to find the one thing they like.<br>&nbsp;</p><h2>Pros #2: Scrolling is Better Than Clicking</h2><p><i>Scrolling has lower interaction cost than clicking/tapping—t</i>he mouse wheels or touchscreens make scrolling faster and easier than clicking. Plus, infinite scroll can be addictive. For a continuous and lengthy content, like a tutorial, scrolling provides even <a href=\"http://www.hugeinc.com/ideas/perspective/everybody-scrolls\">better usability</a> than slicing up the text to several separate screens or pages.</p><h2>Pros #3: Scrolling is Good For Mobile Devices</h2><p><i>The smaller the screen, the longer the scroll</i>. The popularization of mobile browsing is another significant supporter of long scrolling. The gesture controls of mobile devices make scrolling intuitive and easy to use. As a result, the users have a better chance to enjoy browsing experience.</p><h2>Cons #1: Page Performance and Device Resources</h2><p><i>Page-loading speed is everything for good user experience</i>. Multiple researches have <a href=\"https://blog.kissmetrics.com/loading-time/\">shown</a> that slow load times result in people leaving your site or delete your app which result in low conversion rates. And that’s bad news for those who use an infinite-scrolling. The more users scroll down a page, more content has to load on the same page. As a result, the <i>page performance will increasingly slow down</i>.</p><p>Another problem is limited resources of the user’s device. On many infinite scrolling sites, especially those with many images, devices with limited resources (such as mobile devices or tablets with dated hardware) can start slowing down because of the sheer number of assets it has loaded.</p><h2>Cons#2: Item Search and Location</h2><p>Another issue with infinite scrolling is that when users get to a certain point in the stream, they <i>can’t bookmark </i>their location and come back to it later. If they leave the site, they’ll lose all their progress and will have to scroll down again to get back to the same spot. This inability to determine the scrolling position of the user not only causes annoyance or confusion to the users but also hurts the overall user experience, as a result.</p><p>In 2012 Etsy had spent time implementing an infinite scroll interface and <a href=\"http://www.slideshare.net/danmckinley/design-for-continuous-experimentation\">found</a> that the new interface just didn’t perform as well as a pagination. Although the amount of purchases stayed roughly the same, user engagement has gone down — now people weren’t using the search so much.</p>', '<p>We discovered that infinite scrolling is a Diablo\'s invention.&nbsp;</p>', 7),
(103, 'active', 'Robots: will they be able to debug themselves? ', '2017-10-26', NULL, 'Future of debugging: self-debugging is no fantasy', '<p>The terms \"bug\" and \"debugging\" are popularly attributed to <a href=\"https://en.wikipedia.org/wiki/Admiral_Grace_Hopper\">Admiral Grace Hopper</a> in the 1940s.<a href=\"https://en.wikipedia.org/wiki/Debugging#cite_note-1\">[1]</a> While she was working on a <a href=\"https://en.wikipedia.org/wiki/Harvard_Mark_II\">Mark II</a> computer at Harvard University, her associates discovered a moth stuck in a relay and thereby impeding operation, whereupon she remarked that they were \"debugging\" the system. However, the term \"bug\", in the sense of \"technical error\", dates back at least to 1878 and <a href=\"https://en.wikipedia.org/wiki/Thomas_Edison\">Thomas Edison</a> (see <a href=\"https://en.wikipedia.org/wiki/Software_bug\">software bug</a> for a full discussion). Similarly, the term \"debugging\" seems to have been used as a term in aeronautics before entering the world of computers. Indeed, in an interview Grace Hopper remarked that she was not coining the term.[<a href=\"https://en.wikipedia.org/wiki/Wikipedia:Citation_needed\"><i>citation needed</i></a>] The moth fit the already existing terminology, so it was saved. A letter from <a href=\"https://en.wikipedia.org/wiki/J._Robert_Oppenheimer\">J. Robert Oppenheimer</a> (director of the WWII atomic bomb \"Manhattan\" project at Los Alamos, NM) used the term in a letter to Dr. <a href=\"https://en.wikipedia.org/wiki/Ernest_Lawrence\">Ernest Lawrence</a> at UC Berkeley, dated October 27, 1944,<a href=\"https://en.wikipedia.org/wiki/Debugging#cite_note-2\">[2]</a> regarding the recruitment of additional technical staff.</p><p>The <a href=\"https://en.wikipedia.org/wiki/Oxford_English_Dictionary\">Oxford English Dictionary</a> entry for \"debug\" quotes the term \"debugging\" used in reference to airplane engine testing in a 1945 article in the Journal of the Royal Aeronautical Society. An article in \"Airforce\" (June 1945 p.&nbsp;50) also refers to debugging, this time of aircraft cameras. Hopper\'s <a href=\"https://en.wikipedia.org/wiki/Computer_bug\">bug</a> was found on September 9, 1947. Computer programmers did not adopt the term until the early 1950s. The seminal article by Gill<a href=\"https://en.wikipedia.org/wiki/Debugging#cite_note-3\">[3]</a> in 1951 is the earliest in-depth discussion of programming errors, but it does not use the term \"bug\" or \"debugging\". In the <a href=\"https://en.wikipedia.org/wiki/Association_for_Computing_Machinery\">ACM</a>\'s digital library, the term \"debugging\" is first used in three papers from 1952 ACM National Meetings.<a href=\"https://en.wikipedia.org/wiki/Debugging#cite_note-4\">[4]</a><a href=\"https://en.wikipedia.org/wiki/Debugging#cite_note-5\">[5]</a><a href=\"https://en.wikipedia.org/wiki/Debugging#cite_note-6\">[6]</a> Two of the three use the term in quotation marks. By 1963 \"debugging\" was a common-enough term to be mentioned in passing without explanation on page 1 of the <a href=\"https://en.wikipedia.org/wiki/Compatible_Time-Sharing_System\">CTSS</a> manual.<a href=\"https://en.wikipedia.org/wiki/Debugging#cite_note-7\">[7]</a></p><p><a href=\"https://en.wikipedia.org/wiki/Peggy_A._Kidwell\">Peggy A. Kidwell</a>\'s article <i>Stalking the Elusive Computer Bug</i><a href=\"https://en.wikipedia.org/wiki/Debugging#cite_note-8\">[8]</a> discusses the etymology of \"bug\" and \"debug\" in greater detail.</p>', '', 9);

-- --------------------------------------------------------

--
-- Tabellstruktur `projects_areas`
--

CREATE TABLE `projects_areas` (
  `project` int(11) NOT NULL,
  `area` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumpning av Data i tabell `projects_areas`
--

INSERT INTO `projects_areas` (`project`, `area`) VALUES
(2, 1),
(2, 2),
(2, 3),
(2, 4),
(6, 2),
(6, 3),
(82, 8),
(88, 2),
(88, 4),
(90, 5),
(98, 6),
(101, 1),
(102, 5),
(103, 3),
(103, 4);

-- --------------------------------------------------------

--
-- Tabellstruktur `projects_members`
--

CREATE TABLE `projects_members` (
  `member` int(11) NOT NULL,
  `project` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumpning av Data i tabell `projects_members`
--

INSERT INTO `projects_members` (`member`, `project`) VALUES
(1, 90),
(1, 98),
(1, 101),
(2, 88),
(2, 90),
(2, 98),
(3, 2),
(3, 90),
(3, 98),
(3, 102),
(4, 98),
(4, 103),
(5, 6),
(5, 82),
(5, 102),
(7, 6),
(7, 102),
(8, 2),
(8, 101),
(9, 2),
(9, 88),
(9, 98),
(9, 102),
(9, 103);

-- --------------------------------------------------------

--
-- Tabellstruktur `publications`
--

CREATE TABLE `publications` (
  `id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `type` int(11) NOT NULL,
  `date` date NOT NULL,
  `description` varchar(300) DEFAULT NULL,
  `file_name` varchar(255) NOT NULL,
  `abstract` varchar(2000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumpning av Data i tabell `publications`
--

INSERT INTO `publications` (`id`, `title`, `type`, `date`, `description`, `file_name`, `abstract`) VALUES
(1, 'The Study Buddy', 6, '2020-10-08', 'TEI Confernce 2020', 'StudyBuddy.pdf', 'For over two thirds of among the youth, the use of mobile phone takes up to three hours a day. Thus, the mobile phone is a\r\ndistraction during study sessions. We present the Study Buddy design prototype as a remedy to mobile phone usage\r\ndistraction. The main idea is that the mobile phone is locked in a box, controlled by a timer. Students choose the desired\r\nstudy time, and get encouragements from a friendly little sloth who slowly climbs a tree, tells when it is time for a break, and\r\nwhen the study session is over. This paper presents the design and formative user experience evaluations of the Study\r\nBuddy. The results indicate that users feel it is easier to concentrate on their studies when their mobile phone is locked away,\r\nand that the Study Buddy makes them aware, in a relatively playful way, how much time remains of the study session.'),
(2, 'Study Buddy: Från koncept till fungerande\r\nprototyp', 5, '2020-06-01', 'Project development process.', 'Pictorial final.pdf', 'Mobilen tar upp mer av vår tid och är dessutom en distraktionsfaktor vid studier. Vi har därför valt att skapa en Study Buddy-prototyp som ska hjälpa till med studierna genom att förhindra mobilanvändning under en studiesession. Konceptet bygger på att användarens mobil läggs i ett mobilfack som låser sig för att förhindra att mobilen används. Användaren väljer önskad studietid och får hjälp av sin Study Buddy, en söt liten sengångare, som peppar och säger till när det är dags för rast. Baserat på dessa idéer byggde vi en pappersprototyp som vi utvärderade med hjälp av en användarstudie i form av observationer. Resultatet visade framförallt att mobilfacket var svårt att hitta och därför ändrades placeringen av mobilfacket vid utformandet av prototypen. Under användarstudien för pappersprototypen framkom det även att en reset-knapp för nödsituationer skulle vara användbart. Då hela idéen bygger på att mobilen ska vara inlåst har vi valt bort en sådan funktion. Slutligen genomförde vi en användarstudie på den utvecklade prototypen. \r\n'),
(3, 'Tbe Influence of weather on blind people', 7, '2020-09-07', NULL, 'infuence_weather.pdf', 'Weather influence on us all kinds of weird ways. and we want to know how it happens.'),
(4, 'The unicorn project', 2, '2020-07-22', NULL, '5455.pdf', 'The UNICORN project is a three-year project spanning 2017-2020. The vision is to streamline the refuse handling in housing areas by introducing autonomous systems and robots. The UNICORN project is a collaborative object with multiple partners where the area of work for M¨alardalens University is to develop the autonomous robots. This report is the documentation of the work done during the second iteration of the project within the scope of the courses ”Project course in robotics” (DVA473) and ”Project in advanced embedded systems” (DVA474). During this iteration of the project a lift system has been constructed, a human-robot intention system has been implemented, and localisation with the help of ultra-wideband technology has been explored.\r\n\r\n');

-- --------------------------------------------------------

--
-- Tabellstruktur `publications_areas`
--

CREATE TABLE `publications_areas` (
  `publication` int(11) NOT NULL,
  `area` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumpning av Data i tabell `publications_areas`
--

INSERT INTO `publications_areas` (`publication`, `area`) VALUES
(1, 5),
(2, 5),
(3, 1),
(4, 2),
(4, 3);

-- --------------------------------------------------------

--
-- Tabellstruktur `publications_people`
--

CREATE TABLE `publications_people` (
  `person` int(11) NOT NULL,
  `publication` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumpning av Data i tabell `publications_people`
--

INSERT INTO `publications_people` (`person`, `publication`) VALUES
(1, 2),
(2, 1),
(2, 2),
(3, 1),
(3, 2),
(7, 4),
(9, 3),
(10, 4);

-- --------------------------------------------------------

--
-- Tabellstruktur `publications_projects`
--

CREATE TABLE `publications_projects` (
  `publication` int(11) NOT NULL,
  `project` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumpning av Data i tabell `publications_projects`
--

INSERT INTO `publications_projects` (`publication`, `project`) VALUES
(1, 90),
(2, 90),
(4, 6);

-- --------------------------------------------------------

--
-- Tabellstruktur `publication_type`
--

CREATE TABLE `publication_type` (
  `id` int(11) NOT NULL,
  `type` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumpning av Data i tabell `publication_type`
--

INSERT INTO `publication_type` (`id`, `type`) VALUES
(1, 'Article'),
(2, 'Report'),
(3, 'Doctoral Thesis'),
(4, 'Patent'),
(5, 'Pictorial'),
(6, 'Conference Paper'),
(7, 'Other');

-- --------------------------------------------------------

--
-- Tabellstruktur `titles`
--

CREATE TABLE `titles` (
  `id` int(11) NOT NULL,
  `type` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumpning av Data i tabell `titles`
--

INSERT INTO `titles` (`id`, `type`) VALUES
(1, 'Student'),
(2, 'Doctoral Student'),
(3, 'Post Doc'),
(4, 'Professor'),
(5, 'Lecturer');

-- --------------------------------------------------------

--
-- Tabellstruktur `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `login` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumpning av Data i tabell `users`
--

INSERT INTO `users` (`id`, `login`, `password`) VALUES
(1, 'marnem', '202cb962ac59075b964b07152d234b70'),
(2, 'emewal', '202cb962ac59075b964b07152d234b70'),
(3, 'isahan', '202cb962ac59075b964b07152d234b70'),
(4, 'luclar', '202cb962ac59075b964b07152d234b70'),
(5, 'eriand', '202cb962ac59075b964b07152d234b70'),
(7, 'wilsmi', '202cb962ac59075b964b07152d234b70'),
(8, 'vicosc', '202cb962ac59075b964b07152d234b70'),
(9, 'parnys', '202cb962ac59075b964b07152d234b70'),
(10, 'admin', '202cb962ac59075b964b07152d234b70');

-- --------------------------------------------------------

--
-- Struktur för vy `all_members`
--
DROP TABLE IF EXISTS `all_members`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `all_members`  AS  select `members`.`id` AS `id`,`people`.`first_name` AS `first_name`,`people`.`last_name` AS `last_name`,`titles`.`type` AS `title` from ((`members` join `people` on(`members`.`person` = `people`.`id`)) join `titles` on(`members`.`title` = `titles`.`id`)) ;

--
-- Index för dumpade tabeller
--

--
-- Index för tabell `areas`
--
ALTER TABLE `areas`
  ADD PRIMARY KEY (`id`);

--
-- Index för tabell `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`id`),
  ADD KEY `person` (`person`),
  ADD KEY `title` (`title`),
  ADD KEY `user` (`user`);

--
-- Index för tabell `members_areas`
--
ALTER TABLE `members_areas`
  ADD PRIMARY KEY (`member`,`area`),
  ADD KEY `members_area_ibfk_1` (`area`);

--
-- Index för tabell `new_members`
--
ALTER TABLE `new_members`
  ADD PRIMARY KEY (`id`,`email`);

--
-- Index för tabell `people`
--
ALTER TABLE `people`
  ADD PRIMARY KEY (`id`);

--
-- Index för tabell `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `leader` (`leader`);

--
-- Index för tabell `projects_areas`
--
ALTER TABLE `projects_areas`
  ADD PRIMARY KEY (`project`,`area`),
  ADD UNIQUE KEY `unq_projects_areas` (`project`,`area`),
  ADD KEY `area` (`area`);

--
-- Index för tabell `projects_members`
--
ALTER TABLE `projects_members`
  ADD PRIMARY KEY (`member`,`project`),
  ADD KEY `project` (`project`);

--
-- Index för tabell `publications`
--
ALTER TABLE `publications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `type` (`type`);

--
-- Index för tabell `publications_areas`
--
ALTER TABLE `publications_areas`
  ADD PRIMARY KEY (`publication`,`area`),
  ADD KEY `area` (`area`);

--
-- Index för tabell `publications_people`
--
ALTER TABLE `publications_people`
  ADD PRIMARY KEY (`person`,`publication`),
  ADD KEY `publication` (`publication`);

--
-- Index för tabell `publications_projects`
--
ALTER TABLE `publications_projects`
  ADD PRIMARY KEY (`publication`,`project`),
  ADD KEY `project` (`project`);

--
-- Index för tabell `publication_type`
--
ALTER TABLE `publication_type`
  ADD PRIMARY KEY (`id`);

--
-- Index för tabell `titles`
--
ALTER TABLE `titles`
  ADD PRIMARY KEY (`id`);

--
-- Index för tabell `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `login` (`login`);

--
-- AUTO_INCREMENT för dumpade tabeller
--

--
-- AUTO_INCREMENT för tabell `areas`
--
ALTER TABLE `areas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT för tabell `members`
--
ALTER TABLE `members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT för tabell `new_members`
--
ALTER TABLE `new_members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT för tabell `people`
--
ALTER TABLE `people`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT för tabell `projects`
--
ALTER TABLE `projects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=112;

--
-- AUTO_INCREMENT för tabell `publications`
--
ALTER TABLE `publications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT för tabell `publication_type`
--
ALTER TABLE `publication_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT för tabell `titles`
--
ALTER TABLE `titles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT för tabell `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- Restriktioner för dumpade tabeller
--

--
-- Restriktioner för tabell `members`
--
ALTER TABLE `members`
  ADD CONSTRAINT `members_ibfk_1` FOREIGN KEY (`person`) REFERENCES `people` (`id`),
  ADD CONSTRAINT `members_ibfk_2` FOREIGN KEY (`title`) REFERENCES `titles` (`id`),
  ADD CONSTRAINT `members_ibfk_3` FOREIGN KEY (`user`) REFERENCES `users` (`id`);

--
-- Restriktioner för tabell `members_areas`
--
ALTER TABLE `members_areas`
  ADD CONSTRAINT `members_areas_ibfk_1` FOREIGN KEY (`area`) REFERENCES `areas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `members_areas_ibfk_2` FOREIGN KEY (`member`) REFERENCES `members` (`id`) ON DELETE CASCADE;

--
-- Restriktioner för tabell `projects`
--
ALTER TABLE `projects`
  ADD CONSTRAINT `projects_ibfk_1` FOREIGN KEY (`leader`) REFERENCES `members` (`id`);

--
-- Restriktioner för tabell `projects_areas`
--
ALTER TABLE `projects_areas`
  ADD CONSTRAINT `projects_areas_ibfk_1` FOREIGN KEY (`area`) REFERENCES `areas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `projects_areas_ibfk_2` FOREIGN KEY (`project`) REFERENCES `projects` (`id`) ON DELETE CASCADE;

--
-- Restriktioner för tabell `projects_members`
--
ALTER TABLE `projects_members`
  ADD CONSTRAINT `projects_members_ibfk_1` FOREIGN KEY (`member`) REFERENCES `members` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `projects_members_ibfk_2` FOREIGN KEY (`project`) REFERENCES `projects` (`id`) ON DELETE CASCADE;

--
-- Restriktioner för tabell `publications`
--
ALTER TABLE `publications`
  ADD CONSTRAINT `publications_ibfk_1` FOREIGN KEY (`type`) REFERENCES `publication_type` (`id`);

--
-- Restriktioner för tabell `publications_areas`
--
ALTER TABLE `publications_areas`
  ADD CONSTRAINT `publications_areas_ibfk_1` FOREIGN KEY (`area`) REFERENCES `areas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `publications_areas_ibfk_2` FOREIGN KEY (`publication`) REFERENCES `publications` (`id`) ON DELETE CASCADE;

--
-- Restriktioner för tabell `publications_people`
--
ALTER TABLE `publications_people`
  ADD CONSTRAINT `publications_people_ibfk_1` FOREIGN KEY (`person`) REFERENCES `people` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `publications_people_ibfk_2` FOREIGN KEY (`publication`) REFERENCES `publications` (`id`) ON DELETE CASCADE;

--
-- Restriktioner för tabell `publications_projects`
--
ALTER TABLE `publications_projects`
  ADD CONSTRAINT `publications_projects_ibfk_1` FOREIGN KEY (`project`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `publications_projects_ibfk_2` FOREIGN KEY (`publication`) REFERENCES `publications` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
