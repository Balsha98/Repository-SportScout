DROP DATABASE IF EXISTS sport_scout;

CREATE DATABASE sport_scout;

USE sport_scout;


-- TABLE ROLES
DROP TABLE IF EXISTS roles;

CREATE TABLE roles(
    role_id INT NOT NULL AUTO_INCREMENT,
    role_name VARCHAR(25) NOT NULL,
    PRIMARY KEY (role_id)
);

INSERT INTO roles VALUES 
(1, "Administrator"),
(2, "League Manager"),
(3, "Team Manager"),
(4, "Team Coach"),
(5, "Fan");

-- SELECT * FROM roles;

-- TABLE SPORTS
DROP TABLE IF EXISTS sports;

CREATE TABLE sports(
    sport_id INT NOT NULL AUTO_INCREMENT,
    sport_name VARCHAR(50) NOT NULL,
    PRIMARY KEY (sport_id)
);

INSERT INTO sports VALUES
(1, "Basketball"),
(2, "Soccer");

-- SELECT * FROM sports;

-- TABLE LEAGUES
DROP TABLE IF EXISTS leagues;

CREATE TABLE leagues(
    league_id INT NOT NULL AUTO_INCREMENT,
    league_name VARCHAR(50) NOT NULL,
    sport_id INT NOT NULL,
    PRIMARY KEY (league_id),
    FOREIGN KEY (sport_id) REFERENCES sports (sport_id) 
        ON DELETE CASCADE  
        ON UPDATE CASCADE
);

INSERT INTO leagues VALUES
(1, "NBA", 1),
(2, "EuroLeague", 1),
(3, "Champions League", 2);

-- SELECT * FROM leagues;

-- TABLE SEASONS
DROP TABLE IF EXISTS seasons;

CREATE TABLE seasons(
    season_id INT NOT NULL AUTO_INCREMENT,
    season_year CHAR(7),
    season_desc VARCHAR(50) NOT NULL,
    league_id INT NOT NULL,
    PRIMARY KEY (season_id),
    FOREIGN KEY (league_id) REFERENCES leagues (league_id) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE
);

INSERT INTO seasons VALUES
(1, "2023/24", "NBA Season 2023/24", 1),
(2, "2022/23", "EuroLeague 2022/23", 2),
(3, "2020/21", "Champions League 2020/21", 3);

-- SELECT * FROM seasons;


-- TABLE TEAMS
DROP TABLE IF EXISTS teams;

CREATE TABLE teams(
    team_id INT NOT NULL AUTO_INCREMENT,
    team_name VARCHAR(50) NOT NULL,
    sport_id INT NOT NULL,
    league_id INT NOT NULL,
    season_id INT NOT NULL,
    home_color VARCHAR(25) NOT NULL,
    away_color VARCHAR(25) NOT NULL,
    max_players INT NOT NULL,
    PRIMARY KEY (team_id),
    FOREIGN KEY (sport_id) 
        REFERENCES sports (sport_id) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
    FOREIGN KEY (league_id) 
        REFERENCES leagues (league_id) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
    FOREIGN KEY (season_id) 
        REFERENCES seasons (season_id) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE
);

INSERT INTO teams VALUES
(1, "Partizan", 1, 2, 2, "Black/White", "White/Black", 12),
(2, "Red Star", 1, 2, 2, "Red/White", "White/Red", 12),
(3, "LA Lakers", 1, 1, 1, "Gold/Purple", "Purple/Gold", 12),
(4, "Boston Celtics", 1, 1, 1, "Green/White", "White/Green", 12),
(5, "Real Madrid", 2, 3, 3, "White/Purple", "Purple/White", 25),
(6, "FC Barcelona", 2, 3, 3, "Blue/Red", "Red/Blue", 25);

-- SELECT * FROM teams;

-- TABLE SCHEDULE
DROP TABLE IF EXISTS schedule;

CREATE TABLE schedule(
    schedule_id INT NOT NULL AUTO_INCREMENT,
    sport_id INT NOT NULL,
    league_id INT NOT NULL,
    season_id INT NOT NULL,
    home_team_id INT NOT NULL,
    away_team_id INT NOT NULL,
    home_score INT NOT NULL,
    away_score INT NOT NULL,
    scheduled DATE NOT NULL,
    status INT NOT NULL,
    PRIMARY KEY (schedule_id),
    FOREIGN KEY (sport_id) 
        REFERENCES sports (sport_id) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
    FOREIGN KEY (league_id) 
        REFERENCES leagues (league_id) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
    FOREIGN KEY (season_id) 
        REFERENCES seasons (season_id) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
    FOREIGN KEY (home_team_id) 
        REFERENCES teams (team_id) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
    FOREIGN KEY (away_team_id) 
        REFERENCES teams (team_id) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE
);

INSERT INTO schedule VALUES
(1, 1, 2, 2, 1, 2, 90, 89, "2023-03-08", 3),
(2, 1, 2, 2, 2, 1, 0, 0, "2023-12-15", 1),
(3, 1, 2, 2, 1, 2, 0, 0, "2023-04-12", 1),
(4, 1, 2, 2, 2, 1, 79, 81, "2022-11-27", 3),
(5, 1, 1, 1, 3, 4, 132, 111, "2023-05-16", 3),
(6, 1, 1, 1, 4, 3, 0, 0, "2023-12-07", 2),
(7, 1, 1, 1, 4, 3, 121, 115, "2023-02-14", 3),
(8, 2, 3, 3, 6, 5, 2, 0, "2022-11-27", 3),
(9, 2, 3, 3, 5, 6, 0, 0, "2023-03-08", 0);


-- SELECT * FROM schedule;

-- TRUNCATE schedule;

-- TABLE POSITIONS
DROP TABLE IF EXISTS positions;

CREATE TABLE positions(
    position_id INT NOT NULL AUTO_INCREMENT,
    position_name VARCHAR(50) NOT NULL,
    sport_id INT NOT NULL,
    PRIMARY KEY (position_id),
    FOREIGN KEY (sport_id) REFERENCES sports (sport_id)
        ON DELETE CASCADE 
        ON UPDATE CASCADE
);

INSERT INTO positions VALUES
(1, "Point Guard", 1),
(2, "Shooting Guard", 1),
(3, "Small Forward", 1),
(4, "Power Forward", 1),
(5, "Center", 1),
(6, "Goalkeeper", 2),
(7, "Defender", 2),
(8, "Midfielder", 2),
(9, "Forward", 2);

-- SELECT * FROM positions;

-- TABLE PLAYERS
DROP TABLE IF EXISTS players;

CREATE TABLE players(
    player_id INT NOT NULL AUTO_INCREMENT,
    player_first VARCHAR(50) NOT NULL,
    player_last VARCHAR(50) NOT NULL,
    player_dob DATE NOT NULL,
    team_id INT NOT NULL,
    position_id INT NOT NULL,
    player_jersey INT NOT NULL,
    PRIMARY KEY (player_id),
    FOREIGN KEY (team_id) 
        REFERENCES teams (team_id) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
    FOREIGN KEY (position_id) 
        REFERENCES positions (position_id) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE
);

INSERT INTO players (player_first, player_last, player_dob, team_id, position_id, player_jersey) VALUES
('Kevin', 'Punter', '1993-06-25', 1, 2, '00'),
('James', 'Nunnally', '1990-07-14', 1, 3, '21'),
('Mathias', 'Lessort', '1995-09-29', 1, 5, '28'),
('Dante', 'Exum', '1995-07-13', 1, 1, '11'),
('Zach', 'LeDay', '1994-05-30', 1, 4, '3'),
('Alen', 'Smailagić', '2000-08-18', 1, 5, '9'),
('Balša', 'Koprivica', '2000-05-01', 1, 5, '12'),
('Danilo', 'Andjušić', '1991-04-22', 1, 2, '33'),
('Aleksa', 'Avramović', '1994-10-25', 1, 1, '10'),
('Tristan', 'Vukčević', '2003-03-11', 1, 5, '7'),
('Nemanja', 'Nedović', '1991-06-16', 2, 1, '26'),
('Luka', 'Mitrović', '1993-03-21', 2, 4, '9'),
('Ognjen', 'Dobrić', '1994-10-27', 2, 2, '13'),
('Filip', 'Petrušev', '2000-04-15', 2, 5, '33'),
('Nikola', 'Ivanović', '1994-02-19', 2, 1, '12'),
('Ben', 'Bentil', '1995-03-29', 2, 4, '50'),
('Branko', 'Lazić', '1989-01-12', 2, 2, '10'),
('Stefan', 'Marković', '1988-04-25', 2, 1, '55'),
('Hassan', 'Martin', '1995-11-12', 2, 5, '12'),
('Dejan', 'Davidovac', '1995-01-17', 2, 3, '8'),
('LeBron', 'James', '1984-12-30', 3, 3, '6'),
('Anthony', 'Davis', '1993-03-11', 3, 5, '3'),
('Austin', 'Reaves', '1998-05-29', 3, 2, '15'),
('D\'Angelo', 'Russell', '1996-02-23', 3, 1, '1'),
('Rui', 'Hachimura', '1998-02-08', 3, 4, '28'),
('Gabe', 'Vincent', '1996-06-14', 3, 1, '7'),
('Jaxson', 'Hayes', '2000-05-23', 3, 5, '10'),
('Jarred', 'Vanderbilt', '1999-04-03', 3, 4, '2'),
('Taurean', 'Prince', '1994-03-22', 3, 3, '12'),
('Christian', 'Wood', '1995-09-27', 3, 5, '35'),
('Jayson', 'Tatum', '1998-03-03', 4, 3, '0'),
('Jaylen', 'Brown', '1996-10-24', 4, 2, '7'),
('Kristaps', 'Porzingis', '1995-08-02', 4, 5, '6'),
('Derrick', 'White', '1994-07-02', 4, 1, '9'),
('Al', 'Horford', '1986-06-03', 4, 5, '42'),
('Malcolm', 'Brogdon', '1992-12-11', 4, 1, '13'),
('Robert', 'Williams', '1997-10-17', 4, 5, '44'),
('Payton', 'Pritchard', '1998-01-28', 4, 1, '11'),
('Sam', 'Hauser', '1997-12-08', 4, 3, '30'),
('Oshae', 'Brissett', '1998-06-20', 4, 4, '12'),
('Thibaut', 'Courtois', '1992-05-11', 6, 6, '1'),
('Dani', 'Carvajal', '1992-01-11', 6, 7, '2'),
('David', 'Alaba', '1992-06-24', 6, 7, '4'),
('Antonio', 'Rüdiger', '1993-03-03', 6, 7, '22'),
('Ferland', 'Mendy', '1995-06-08', 6, 7, '23'),
('Toni', 'Kroos', '1990-01-04', 6, 8, '8'),
('Luka', 'Modric', '1985-09-09', 6, 8, '10'),
('Eduardo', 'Camavinga', '2002-11-10', 6, 8, '12'),
('Aurélien', 'Tchouaméni', '2000-01-27', 6, 8, '18'),
('Vinícius', 'Júnior', '2000-07-12', 6, 9, '7'),
('Rodrygo', 'Goes', '2001-01-09', 6, 9, '11'),
('Marc-André', 'ter Stegen', '1992-04-30', 5, 6, '1'),
('Jules', 'Koundé', '1998-11-12', 5, 7, '23'),
('Ronald', 'Araújo', '1999-03-07', 5, 7, '4'),
('Andreas', 'Christensen', '1996-04-10', 5, 7, '15'),
('Alejandro', 'Balde', '2003-10-18', 5, 7, '12'),
('Frenkie', 'de Jong', '1997-05-12', 5, 7, '21'),
('Ilkay', 'Gündogan', '1990-10-24', 5, 7, '8'),
('Gavi', 'Paez', '2004-08-05', 5, 7, '6'),
('Pedri', 'González', '2002-11-25', 5, 7, '8'),
('Robert', 'Lewandowski', '1988-08-21', 5, 9, '9'),
('Ferran', 'Torres', '2000-02-29', 5, 9, '7');

-- TABLE USERS
DROP TABLE IF EXISTS users;

CREATE TABLE users(
    user_id INT NOT NULL AUTO_INCREMENT,
    role_id INT NOT NULL,
    username VARCHAR(25) NOT NULL,
    password CHAR(64) NOT NULL,
    league_id INT NULL,
    team_id INT NULL,
    PRIMARY KEY (user_id),
    FOREIGN KEY (role_id) 
        REFERENCES roles (role_id) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE
);

INSERT INTO users (user_id, role_id, username, password, league_id, team_id) VALUES
(1, 1, "Admin", "8c6976e5b5410415bde908bd4dee15dfb167a9c873fc4bb8a81f6f2ab448a918", 0, 0);

-- SELECT * FROM users;

-- SHOW TABLES;
