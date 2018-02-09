SET NAMES utf8;
SET foreign_key_checks = 0;

-- -- -- drop residual tables -- -- --
DROP TABLE IF EXISTS fleet;
DROP TABLE IF EXISTS boat;
DROP TABLE IF EXISTS port;
DROP TABLE IF EXISTS battle;
DROP TABLE IF EXISTS boat_participated_in_battle;
DROP TABLE IF EXISTS crew_participated_in_battle;
DROP TABLE IF EXISTS alce_participated_in_battle;
DROP TABLE IF EXISTS alliance;
DROP TABLE IF EXISTS crew_part_of_alliance;
DROP TABLE IF EXISTS crew;
DROP TABLE IF EXISTS pirate_in_crew;
DROP TABLE IF EXISTS pirate;
DROP TABLE IF EXISTS common_pirate;
DROP TABLE IF EXISTS captain;

-- -- -- creating tables -- -- --
CREATE TABLE fleet (
	id_fleet INT NOT NULL AUTO_INCREMENT,
	name VARCHAR(30) NOT NULL,
	id_captain INT NOT NULL,
	PRIMARY KEY ( id_fleet )
);

CREATE TABLE boat (
	id_boat INT NOT NULL AUTO_INCREMENT,
	name VARCHAR(30) NOT NULL,
	type VARCHAR(30) NOT NULL,
	capacity INT NOT NULL,
	id_port INT,
	id_fleet INT,
	id_crew INT NOT NULL,
	id_captain INT NOT NULL,
	PRIMARY KEY ( id_boat )
);

CREATE TABLE port (
	id_port INT NOT NULL AUTO_INCREMENT,
	name VARCHAR(30) NOT NULL,
	capacity INT NOT NULL,
	place VARCHAR(30) NOT NULL,
	PRIMARY KEY ( id_port )
);

CREATE TABLE battle (
	id_battle INT NOT NULL AUTO_INCREMENT,
	who_won INT NOT NULL,
	losses INT NOT NULL,
	date_happened DATE NOT NULL,
	id_port INT,
	PRIMARY KEY ( id_battle )
);

CREATE TABLE boat_participated_in_battle (
	id_boat INT NOT NULL,
	id_battle INT NOT NULL
);

CREATE TABLE crew_participated_in_battle (
	id_crew INT NOT NULL,
	id_battle INT NOT NULL
);


CREATE TABLE crew (
	id_crew INT NOT NULL AUTO_INCREMENT,
	name VARCHAR(30) NOT NULL,
	id_captain INT NOT NULL,
	id_port INT NOT NULL,
	PRIMARY KEY ( id_crew )
);

CREATE TABLE pirate_in_crew (
	id_pirate INT NOT NULL,
	id_crew INT NOT NULL
);

CREATE TABLE pirate (
	id_pirate INT NOT NULL AUTO_INCREMENT,
	passwd VARCHAR(10) NOT NULL,
	name VARCHAR(30) NOT NULL,
	nick VARCHAR(30) NOT NULL,
	date_of_birth DATE NOT NULL,
	beard_color VARCHAR(30) NOT NULL,
	date_joined_crew DATE,
	id_boat INT,
	characteristics_list VARCHAR(255),
	PRIMARY KEY ( id_pirate )
);

CREATE TABLE common_pirate (
	id_pirate INT NOT NULL,
	position VARCHAR(255) NOT NULL,
	PRIMARY KEY ( id_pirate )
);

CREATE TABLE captain (
	id_pirate INT NOT NULL,
	degree VARCHAR(255) NOT NULL,
	PRIMARY KEY ( id_pirate )
);


-- -- -- setting composite primary keys -- -- --
 ALTER TABLE boat_participated_in_battle ADD CONSTRAINT pk_boat_participated_in_battle PRIMARY KEY (id_boat, id_battle);
 ALTER TABLE crew_participated_in_battle ADD CONSTRAINT pk_crew_participated_in_battle PRIMARY KEY (id_crew, id_battle);
 ALTER TABLE pirate_in_crew ADD CONSTRAINT pk_pirate_in_crew PRIMARY KEY (id_pirate, id_crew);

 -- -- -- setting foreign keys -- -- --
 ALTER TABLE fleet ADD CONSTRAINT fk_captain1 FOREIGN KEY (id_captain) REFERENCES captain(id_pirate);

 ALTER TABLE boat ADD CONSTRAINT fk_port1 FOREIGN KEY (id_port) REFERENCES port(id_port);
 ALTER TABLE boat ADD CONSTRAINT fk_fleet FOREIGN KEY (id_fleet) REFERENCES fleet(id_fleet);
 ALTER TABLE boat ADD CONSTRAINT fk_crew1 FOREIGN KEY (id_crew) REFERENCES crew(id_crew);
 ALTER TABLE boat ADD CONSTRAINT fk_captain2 FOREIGN KEY (id_captain) REFERENCES captain(id_pirate);

 ALTER TABLE battle ADD CONSTRAINT fk_port2 FOREIGN KEY (id_port) REFERENCES port(id_port);
 ALTER TABLE battle ADD CONSTRAINT fk_winner FOREIGN KEY (who_won) REFERENCES crew(id_crew);

 ALTER TABLE boat_participated_in_battle ADD CONSTRAINT fk_boat1 FOREIGN KEY (id_boat) REFERENCES boat(id_boat);
 ALTER TABLE boat_participated_in_battle ADD CONSTRAINT fk_battle1 FOREIGN KEY (id_battle) REFERENCES battle(id_battle);

 ALTER TABLE crew_participated_in_battle ADD CONSTRAINT fk_crew2 FOREIGN KEY (id_crew) REFERENCES crew(id_crew);
 ALTER TABLE crew_participated_in_battle ADD CONSTRAINT fk_battle2 FOREIGN KEY (id_battle) REFERENCES battle(id_battle);

 ALTER TABLE crew ADD CONSTRAINT fk_captain3 FOREIGN KEY (id_captain) REFERENCES captain(id_pirate);
 ALTER TABLE crew ADD CONSTRAINT fk_port4 FOREIGN KEY (id_port) REFERENCES port(id_port);

 ALTER TABLE pirate_in_crew ADD CONSTRAINT fk_pirate1 FOREIGN KEY (id_pirate) REFERENCES pirate(id_pirate);
 ALTER TABLE pirate_in_crew ADD CONSTRAINT fk_crew4 FOREIGN KEY (id_crew) REFERENCES crew(id_crew);

 ALTER TABLE pirate ADD CONSTRAINT fk_boat2 FOREIGN KEY (id_boat) REFERENCES boat(id_boat);

 ALTER TABLE common_pirate ADD CONSTRAINT fk_pirate2 FOREIGN KEY (id_pirate) REFERENCES pirate(id_pirate);

 ALTER TABLE captain ADD CONSTRAINT fk_pirate3 FOREIGN KEY (id_pirate) REFERENCES pirate(id_pirate);
 
-- -- -- -- -- -- --  -- -- -- -- -- --
-- -- -- SAMPLE DATA INSERTION -- -- --
-- -- -- -- -- -- --  -- -- -- -- -- --
 
--          ID LEGEND        --
-- ------------------------- --
--   PIRATE ->    1 - 1000   --
--   CREW ->   1001 - 1100   --
--   BOAT ->   1101 - 1200   --
--   FLEET ->  1201 - 1300   --
--   PORT ->   1301 - 1400   --
--   BATTLE -> 1401 - 1500   --
-- ------------------------- --
 
-- -- -- Creating ports -- -- --
INSERT INTO port VALUES(1301, 'Port 1', 15, 'North Shore');
INSERT INTO port VALUES(1302, 'Port 2', 7, 'Small Island');
INSERT INTO port VALUES(1303, 'Port 3', 32, 'East Coast');

-- -- -- Creating pirates of Crew 1 -- -- --
INSERT INTO pirate VALUES(11, 'pswd11', 'Pirate 11', 'Nickname 11', STR_TO_DATE('02-JAN-1940','%d-%b-%Y'), 'black', STR_TO_DATE('02-JAN-1960','%d-%b-%Y'), NULL, 'Characteristics 11');
INSERT INTO captain VALUES(11, 'Crew Captain');
INSERT INTO pirate VALUES(12, 'pswd12', 'Pirate 12', 'Nickname 12', STR_TO_DATE('04-FEB-1945','%d-%b-%Y'), 'brown', STR_TO_DATE('30-NOV-1960','%d-%b-%Y'), NULL, 'Characteristics 12');
INSERT INTO captain VALUES(12, 'Fleet Captain');
INSERT INTO pirate VALUES(13, 'pswd13', 'Pirate 13', 'Nickname 13', STR_TO_DATE('06-MAR-1947','%d-%b-%Y'), 'red', STR_TO_DATE('28-SEP-1961','%d-%b-%Y'), NULL, 'Characteristics 13');
INSERT INTO captain VALUES(13, 'Boat Captain');
INSERT INTO pirate VALUES(14, 'pswd14', 'Pirate 14', 'Nickname 14', STR_TO_DATE('08-APR-1941','%d-%b-%Y'), 'white', STR_TO_DATE('26-JUL-1961','%d-%b-%Y'), NULL, 'Characteristics 14');
INSERT INTO captain VALUES(14, 'Boat Captain');
INSERT INTO pirate VALUES(15, 'pswd15', 'Pirate 15', 'Nickname 15', STR_TO_DATE('10-MAY-1946','%d-%b-%Y'), 'brown', STR_TO_DATE('24-MAY-1962','%d-%b-%Y'), NULL, 'Characteristics 15');
INSERT INTO captain VALUES(15, 'Boat Captain');
INSERT INTO pirate VALUES(16, 'pswd16', 'Pirate 16', 'Nickname 16', STR_TO_DATE('12-JUN-1948','%d-%b-%Y'), 'black', STR_TO_DATE('22-MAR-1963','%d-%b-%Y'), NULL, 'Characteristics 16');
INSERT INTO common_pirate VALUES(16, 'Master Gunner');
INSERT INTO pirate VALUES(17, 'pswd17', 'Pirate 17', 'Nickname 17', STR_TO_DATE('14-JUL-1942','%d-%b-%Y'), 'gray', STR_TO_DATE('20-FEB-1964','%d-%b-%Y'), NULL, 'Characteristics 17');
INSERT INTO common_pirate VALUES(17, 'Bootswain');
INSERT INTO pirate VALUES(18, 'pswd18', 'Pirate 18', 'Nickname 18', STR_TO_DATE('16-AUG-1947','%d-%b-%Y'), 'blonde', STR_TO_DATE('18-DEC-1964','%d-%b-%Y'), NULL, 'Characteristics 18');
INSERT INTO common_pirate VALUES(18,'Bootswain');
INSERT INTO pirate VALUES(19, 'pswd19', 'Pirate 19', 'Nickname 19', STR_TO_DATE('18-SEP-1949','%d-%b-%Y'), 'red', STR_TO_DATE('16-OCT-1965','%d-%b-%Y'), NULL, 'Characteristics 19');
INSERT INTO common_pirate VALUES(19,'ABS');
INSERT INTO pirate VALUES(20, 'pswd20', 'Pirate 20', 'Nickname 20', STR_TO_DATE('20-OCT-1943','%d-%b-%Y'), 'brown', STR_TO_DATE('14-AUG-1965','%d-%b-%Y'), NULL, 'Characteristics 20');
INSERT INTO common_pirate VALUES(20,'ABS');

-- -- -- Creating Crew 1 and assigning pirates -- -- --
INSERT INTO crew VALUES(1001, 'Crew 1', 11, 1301);
INSERT INTO pirate_in_crew VALUES(11, 1001);
INSERT INTO pirate_in_crew VALUES(12, 1001);
INSERT INTO pirate_in_crew VALUES(13, 1001);
INSERT INTO pirate_in_crew VALUES(14, 1001);
INSERT INTO pirate_in_crew VALUES(15, 1001);
INSERT INTO pirate_in_crew VALUES(16, 1001);
INSERT INTO pirate_in_crew VALUES(17, 1001);
INSERT INTO pirate_in_crew VALUES(18, 1001);
INSERT INTO pirate_in_crew VALUES(19, 1001);
INSERT INTO pirate_in_crew VALUES(20, 1001);

-- -- -- Creating Fleet 11 and its ships and boarding pirates -- -- --
INSERT INTO fleet VALUES(1201, 'Fleet 11', 11);
INSERT INTO boat VALUES(1101, 'Boat 111', 'French Warship', 48, 1301, 1201, 1001, 11);
INSERT INTO boat VALUES(1102, 'Boat 112', 'Frigate', 26, 1301, 1201, 1001, 13);
INSERT INTO boat VALUES(1103, 'Boat 113', 'Sloop', 8, 1301, 1201, 1001, 14);
UPDATE pirate SET id_boat = 1101 WHERE id_pirate = 11;
UPDATE pirate SET id_boat = 1101 WHERE id_pirate = 16;
UPDATE pirate SET id_boat = 1101 WHERE id_pirate = 17;
UPDATE pirate SET id_boat = 1102 WHERE id_pirate = 13;
UPDATE pirate SET id_boat = 1102 WHERE id_pirate = 19;
UPDATE pirate SET id_boat = 1103 WHERE id_pirate = 14;

-- -- -- Creating Fleet 12 and its ships and boarding pirates -- -- --
INSERT INTO fleet VALUES(1202, 'Fleet 12', 12);
INSERT INTO boat VALUES(1104, 'Boat 121', 'Brigantine', 34, NULL, 1202, 1001, 12);
INSERT INTO boat VALUES(1105, 'Boat 122', 'Sloop', 8, NULL, 1202, 1001, 15);
UPDATE pirate SET id_boat = 1104 WHERE id_pirate = 12;
UPDATE pirate SET id_boat = 1104 WHERE id_pirate = 18;
UPDATE pirate SET id_boat = 1105 WHERE id_pirate = 15;
UPDATE pirate SET id_boat = 1105 WHERE id_pirate = 20;

-- -- -- Creating pirates of Crew 2 -- -- --
INSERT INTO pirate VALUES(21, 'pswd21', 'Pirate 21', 'Nickname 21', STR_TO_DATE('22-NOV-1948','%d-%b-%Y'), 'brown', STR_TO_DATE('12-JUN-1965','%d-%b-%Y'), NULL, 'Characteristics 21');
INSERT INTO captain VALUES(21, 'Crew Captain');
INSERT INTO pirate VALUES(22, 'pswd22', 'Pirate 22', 'Nickname 22', STR_TO_DATE('24-DEC-1950','%d-%b-%Y'), 'red', STR_TO_DATE('10-APR-1966','%d-%b-%Y'), NULL, 'Characteristics 22');
INSERT INTO captain VALUES(22, 'Boat Captain');
INSERT INTO pirate VALUES(23, 'pswd23', 'Pirate 23', 'Nickname 23', STR_TO_DATE('26-JAN-1944','%d-%b-%Y'), 'gray', STR_TO_DATE('08-FEB-1967','%d-%b-%Y'), NULL, 'Characteristics 23');
INSERT INTO common_pirate VALUES(23, 'Bootswain');
INSERT INTO pirate VALUES(24, 'pswd24', 'Pirate 24', 'Nickname 24', STR_TO_DATE('28-FEB-1949','%d-%b-%Y'), 'blonde', STR_TO_DATE('06-JAN-1968','%d-%b-%Y'), NULL, 'Characteristics 24');
INSERT INTO common_pirate VALUES(24, 'ABS');
INSERT INTO pirate VALUES(25, 'pswd25', 'Pirate 25', 'Nickname 25', STR_TO_DATE('30-MAR-1951','%d-%b-%Y'), 'brown', STR_TO_DATE('04-NOV-1968','%d-%b-%Y'), NULL, 'Characteristics 25');
INSERT INTO common_pirate VALUES(25, 'ABS');

-- -- -- Creating Crew 2 and assigning pirates -- -- --
INSERT INTO crew VALUES(1002, 'Crew 2', 21, 1303);
INSERT INTO pirate_in_crew VALUES(21, 1002);
INSERT INTO pirate_in_crew VALUES(22, 1002);
INSERT INTO pirate_in_crew VALUES(23, 1002);
INSERT INTO pirate_in_crew VALUES(24, 1002);
INSERT INTO pirate_in_crew VALUES(25, 1002);

-- -- -- Creating Fleet 21 and its ships and boarding pirates -- -- --
INSERT INTO fleet VALUES(1203, 'Fleet 21', 21);
INSERT INTO boat VALUES(1106, 'Boat 211', 'Frigate', 38, 1303, 1203, 1002, 21);
INSERT INTO boat VALUES(1107, 'Boat 212', 'Sloop', 8, 1303, 1203, 1002, 22);
UPDATE pirate SET id_boat = 1106 WHERE id_pirate = 21;
UPDATE pirate SET id_boat = 1106 WHERE id_pirate = 23;
UPDATE pirate SET id_boat = 1106 WHERE id_pirate = 24;
UPDATE pirate SET id_boat = 1107 WHERE id_pirate = 22;
UPDATE pirate SET id_boat = 1107 WHERE id_pirate = 25;

-- -- -- Creating pirates of Crew 3 -- -- --
INSERT INTO pirate VALUES(31, 'pswd31', 'Pirate 31', 'Nickname 31', STR_TO_DATE('01-APR-1945','%d-%b-%Y'), 'blonde', STR_TO_DATE('02-SEP-1968','%d-%b-%Y'), NULL, 'Characteristics 31');
INSERT INTO captain VALUES(31, 'Crew Captain');
INSERT INTO pirate VALUES(32, 'pswd32', 'Pirate 32', 'Nickname 32', STR_TO_DATE('03-MAY-1950','%d-%b-%Y'), 'brown', STR_TO_DATE('31-JUL-1969','%d-%b-%Y'), NULL, 'Characteristics 32');
INSERT INTO common_pirate VALUES(32, 'Master Gunner');
INSERT INTO pirate VALUES(33, 'pswd33', 'Pirate 33', 'Nickname 33', STR_TO_DATE('05-JUN-1952','%d-%b-%Y'), 'white', STR_TO_DATE('29-MAY-1970','%d-%b-%Y'), NULL, 'Characteristics 33');
INSERT INTO common_pirate VALUES(33, 'Bootswain');
INSERT INTO pirate VALUES(34, 'pswd34', 'Pirate 34', 'Nickname 34', STR_TO_DATE('07-JUL-1946','%d-%b-%Y'), 'black', STR_TO_DATE('27-MAR-1971','%d-%b-%Y'), NULL, 'Characteristics 34');
INSERT INTO common_pirate VALUES(34, 'ABS');
INSERT INTO pirate VALUES(35, 'pswd35', 'Pirate 35', 'Nickname 35', STR_TO_DATE('09-AUG-1951','%d-%b-%Y'), 'red', STR_TO_DATE('25-JAN-1972','%d-%b-%Y'), NULL, 'Characteristics 35');
INSERT INTO common_pirate VALUES(35, 'Swab');

-- -- -- Creating Crew 3 and assigning pirates -- -- --
INSERT INTO crew VALUES(1003, 'Crew 3', 31, 1303);
INSERT INTO pirate_in_crew VALUES(31, 1003);
INSERT INTO pirate_in_crew VALUES(32, 1003);
INSERT INTO pirate_in_crew VALUES(33, 1003);
INSERT INTO pirate_in_crew VALUES(34, 1003);
INSERT INTO pirate_in_crew VALUES(35, 1003);

-- -- -- Creating Boat 311 and boarding pirates -- -- --
INSERT INTO boat VALUES(1108, 'Boat 311', 'Spanish Galleon', 52, 1302, NULL, 1003, 31);
UPDATE pirate SET id_boat = 1108 WHERE id_pirate = 31;
UPDATE pirate SET id_boat = 1108 WHERE id_pirate = 32;
UPDATE pirate SET id_boat = 1108 WHERE id_pirate = 33;
UPDATE pirate SET id_boat = 1108 WHERE id_pirate = 34;
UPDATE pirate SET id_boat = 1108 WHERE id_pirate = 35;

-- -- -- Creating Battles -- -- --
INSERT INTO battle VALUES(1401, 1001, 18, STR_TO_DATE('07-JUL-1991','%d-%b-%Y'), NULL);
INSERT INTO crew_participated_in_battle VALUES(1001, 1401);
INSERT INTO crew_participated_in_battle VALUES(1002, 1401);
INSERT INTO boat_participated_in_battle VALUES(1102, 1401);
INSERT INTO boat_participated_in_battle VALUES(1107, 1401);

INSERT INTO battle VALUES(1402, 1001, 9, STR_TO_DATE('09-JUL-1991','%d-%b-%Y'), NULL);
INSERT INTO crew_participated_in_battle VALUES(1001, 1402);
INSERT INTO crew_participated_in_battle VALUES(1003, 1402);
INSERT INTO boat_participated_in_battle VALUES(1101, 1402);
INSERT INTO boat_participated_in_battle VALUES(1108, 1402);

INSERT INTO battle VALUES(1403, 1001, 4, STR_TO_DATE('13-AUG-1991','%d-%b-%Y'), 1301);
INSERT INTO crew_participated_in_battle VALUES(1001, 1403);
INSERT INTO boat_participated_in_battle VALUES(1101, 1403);

INSERT INTO battle VALUES(1404, 1002, 23, STR_TO_DATE('15-AUG-1991','%d-%b-%Y'), 1303);
INSERT INTO crew_participated_in_battle VALUES(1002, 1404);
INSERT INTO crew_participated_in_battle VALUES(1003, 1404);
INSERT INTO boat_participated_in_battle VALUES(1107, 1404);
INSERT INTO boat_participated_in_battle VALUES(1108, 1404);

INSERT INTO battle VALUES(1405, 1003, 21, STR_TO_DATE('28-AUG-1991','%d-%b-%Y'), 1302);
INSERT INTO crew_participated_in_battle VALUES(1003, 1405);
INSERT INTO crew_participated_in_battle VALUES(1001, 1405);
INSERT INTO boat_participated_in_battle VALUES(1108, 1405);
INSERT INTO boat_participated_in_battle VALUES(1104, 1405);
INSERT INTO boat_participated_in_battle VALUES(1105, 1405);

INSERT INTO battle VALUES(1406, 1001, 82, STR_TO_DATE('04-MAY-1992','%d-%b-%Y'), 1302);
INSERT INTO crew_participated_in_battle VALUES(1001, 1406);
INSERT INTO crew_participated_in_battle VALUES(1002, 1406);
INSERT INTO crew_participated_in_battle VALUES(1003, 1406);
INSERT INTO boat_participated_in_battle VALUES(1101, 1406);
INSERT INTO boat_participated_in_battle VALUES(1102, 1406);
INSERT INTO boat_participated_in_battle VALUES(1104, 1406);
INSERT INTO boat_participated_in_battle VALUES(1105, 1406);
INSERT INTO boat_participated_in_battle VALUES(1106, 1406);
INSERT INTO boat_participated_in_battle VALUES(1107, 1406);
INSERT INTO boat_participated_in_battle VALUES(1108, 1406);
