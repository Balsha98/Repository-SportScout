<?php declare(strict_types=1);

class Database
{
    private $db;
    private static Database $instance;

    private function __construct($server, $db, $user, $pass)
    {
        $this->db = new PDO("mysql:host={$server};dbname={$db}", $user, $pass);
    }

    // ***** DATABASE RELATED METHODS ***** //

    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new Database('localhost', 'sport_scout', 'root', '');
        }

        return self::$instance;
    }

    public function alterAutoIncrement($table, $rowID)
    {
        $query = "ALTER TABLE {$table} AUTO_INCREMENT = {$rowID};";
        $result = $this->db->prepare($query);
        $result->execute();
    }

    // ***** USER RELATED METHODS ***** //

    public function getAllUsers()
    {
        $query = '
            SELECT * FROM users INNER JOIN roles 
            ON users.role_id = roles.role_id 
            ORDER BY users.user_id ASC;
        ';

        $result = $this->db->prepare($query);
        if (!$result->execute()) {
            return null;
        }

        $return = $result->fetchAll(PDO::FETCH_ASSOC);

        $array = [];
        foreach ($return as $data) {
            $leagueID = (int) $data['league_id'];
            $teamID = (int) $data['team_id'];

            if ($leagueID === 0 && $teamID === 0) {
                $data['league_name'] = 'All';
                $data['team_name'] = 'All';
            } else if ($leagueID !== 0 && $teamID === 0) {
                $leagueData = $this->getLeagueDataByLeagueId($leagueID);

                if (count($leagueData) > 0) {
                    [['league_name' => $leagueName]] = $leagueData;

                    $data['league_name'] = $leagueName;
                    $data['team_name'] = "All Within The {$leagueName}";
                } else {
                    $data['league_name'] = '';
                    $data['team_name'] = '';
                }
            } else {
                $leagueData = $this->getLeagueDataByLeagueId($leagueID);
                $teamData = $this->getTeamDataByTeamId('team_name', $teamID);

                if (count($leagueData) > 0 && count($teamData) > 0) {
                    [['league_name' => $leagueName]] = $leagueData;
                    [['team_name' => $teamName]] = $teamData;

                    $data['league_name'] = $leagueName;
                    $data['team_name'] = $teamName;
                } else {
                    $data['league_name'] = '';
                    $data['team_name'] = '';
                }
            }

            $array[] = $data;
        }

        return $array;
    }

    public function verifyUser($username, $password)
    {
        $user = $this->getCurrentUserData($username);

        // Guard clause.
        if (!$user) {
            return false;
        }

        // Hash pass for search.
        $hashed_password = hash('sha256', $password);
        if ($user['username'] === $username) {
            return hash_equals($user['password'], $hashed_password);
        }

        return false;
    }

    public function getCurrentUserData($username)
    {
        $query = '
            SELECT * FROM users INNER JOIN roles 
            ON users.role_id = roles.role_id 
            WHERE users.username = :username;
        ';

        $result = $this->db->prepare($query);
        $result->bindParam(':username', $username, PDO::PARAM_STR);

        if ($result->execute()) {
            return $result->fetch(PDO::FETCH_ASSOC);
        }

        return null;
    }

    public function insertNewUser($data)
    {
        $query = '
            INSERT INTO users (role_id, username, password, league_id, team_id) 
            VALUES (:role_id, :username, :password, :league_id, :team_id);
        ';

        // Preparing statement.
        $result = $this->db->prepare($query);

        // Hashing the password.
        $hashedPassword = hash('sha256', $data['new_user_password']);

        // Binding each value individually.
        $result->bindParam(':role_id', $data['new_user_role_id'], PDO::PARAM_INT);
        $result->bindParam(':username', $data['new_username'], PDO::PARAM_STR);
        $result->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
        $result->bindParam(':league_id', $data['new_user_league_id'], PDO::PARAM_INT);
        $result->bindParam(':team_id', $data['new_user_team_id'], PDO::PARAM_INT);

        // Execution.
        $result->execute();
    }

    public function updateAdminUser($data)
    {
        $query = '
            UPDATE users SET 
            role_id = :role_id, username = :username, 
            league_id = :league_id, team_id = :team_id 
            WHERE user_id = :user_id;
        ';

        // Get user id.
        $userID = $data['user_id'];

        // Preparing statement.
        $result = $this->db->prepare($query);
        $result->bindParam(':role_id', $data['role_id'], PDO::PARAM_INT);
        $result->bindParam(':username', $data["username_{$userID}"], PDO::PARAM_STR);
        $result->bindParam(':league_id', $data["user_league_id_{$userID}"], PDO::PARAM_INT);
        $result->bindParam(':team_id', $data["user_team_id_{$userID}"], PDO::PARAM_INT);
        $result->bindParam(':user_id', $data['user_id'], PDO::PARAM_INT);

        // Execution.
        $result->execute();
    }

    // ***** SPORT RELATED METHODS ***** //

    public function getAllSports()
    {
        $query = '
            SELECT * FROM sports
            ORDER BY sport_id ASC;
        ';

        $result = $this->db->prepare($query);

        if ($result->execute()) {
            return $result->fetchAll(PDO::FETCH_ASSOC);
        }

        return null;
    }

    public function insertNewSport($data)
    {
        $query = 'INSERT INTO sports (sport_name) VALUES (:sport_name);';
        $result = $this->db->prepare($query);
        $result->bindParam(':sport_name', $data['new_sport_name'], PDO::PARAM_STR);
        $result->execute();
    }

    public function updateSport($data)
    {
        $query = '
            UPDATE sports SET 
            sport_name = :sport_name
            WHERE sports.sport_id = :sport_id;
        ';

        // Get sport id.
        $sportID = $data['sport_id'];

        // Prepare statement.
        $result = $this->db->prepare($query);
        $result->bindParam(':sport_name', $data["sport_name_{$sportID}"], PDO::PARAM_STR);
        $result->bindParam(':sport_id', $data['sport_id'], PDO::PARAM_INT);

        $result->execute();
    }

    // ***** LEAGUE RELATED METHODS ***** //

    public function getAllLeagues()
    {
        $query = '
            SELECT * FROM leagues INNER JOIN sports 
            ON leagues.sport_id = sports.sport_id
            ORDER BY leagues.league_id ASC;
        ';

        $result = $this->db->prepare($query);

        if ($result->execute()) {
            return $result->fetchAll(PDO::FETCH_ASSOC);
        }

        return null;
    }

    public function getLeagueDataByLeagueId($id)
    {
        $query = '
            SELECT * FROM leagues INNER JOIN sports 
            ON leagues.sport_id = sports.sport_id 
            WHERE leagues.league_id = :league_id;
        ';

        $result = $this->db->prepare($query);
        $result->bindParam(':league_id', $id, PDO::PARAM_INT);

        if ($result->execute()) {
            return $result->fetchAll(PDO::FETCH_ASSOC);
        }

        return null;
    }

    public function getLeaguesBySportId($id)
    {
        $query = '
            SELECT * FROM leagues INNER JOIN sports 
            ON leagues.sport_id = sports.sport_id 
            WHERE leagues.sport_id = :sport_id;
        ';

        $result = $this->db->prepare($query);
        $result->bindParam(':sport_id', $id, PDO::PARAM_INT);

        if ($result->execute()) {
            return $result->fetchAll(PDO::FETCH_ASSOC);
        }

        return null;
    }

    public function insertNewLeague($data)
    {
        $query = '
            INSERT INTO leagues (league_name, sport_id) 
            VALUES (:league_name, :sport_id);    
        ';

        $result = $this->db->prepare($query);
        $result->bindParam(':league_name', $data['new_league_name'], PDO::PARAM_STR);
        $result->bindParam(':sport_id', $data['new_league_sport_id'], PDO::PARAM_INT);
        $result->execute();
    }

    public function updateLeague($data)
    {
        $query = '
            UPDATE leagues SET 
            league_name = :league_name, 
            sport_id = :sport_id 
            WHERE league_id = :league_id;   
        ';

        // Get league id.
        $leagueID = $data['league_id'];

        // Preparing statement.
        $result = $this->db->prepare($query);
        $result->bindParam(':league_name', $data["league_name_{$leagueID}"], PDO::PARAM_STR);
        $result->bindParam(':sport_id', $data["league_sport_id_{$leagueID}"], PDO::PARAM_INT);
        $result->bindParam(':league_id', $data['league_id'], PDO::PARAM_INT);

        // Execution.
        $result->execute();
    }

    // ***** SEASON RELATED METHODS ***** //

    public function getAllSeasons()
    {
        $query = '
            SELECT * FROM seasons 
            INNER JOIN leagues ON seasons.league_id = leagues.league_id
            INNER JOIN sports ON leagues.sport_id = sports.sport_id
            ORDER BY seasons.season_id ASC;
        ';

        $result = $this->db->prepare($query);

        if ($result->execute()) {
            return $result->fetchAll(PDO::FETCH_ASSOC);
        }

        return null;
    }

    public function getSeasonsByLeagueId($id)
    {
        $query = '
            SELECT * FROM seasons 
            INNER JOIN leagues ON seasons.league_id = leagues.league_id 
            INNER JOIN sports ON leagues.sport_id = sports.sport_id 
            WHERE leagues.league_id = :league_id;
        ';

        $result = $this->db->prepare($query);
        $result->bindParam(':league_id', $id, PDO::PARAM_INT);

        if ($result->execute()) {
            return $result->fetchAll(PDO::FETCH_ASSOC);
        }

        return null;
    }

    public function insertNewSeason($data)
    {
        $query = '
            INSERT INTO seasons (
                season_year, season_desc, league_id
            ) VALUES (
                :season_year, :season_desc, :league_id 
            );
        ';

        // Preparing statement.
        $result = $this->db->prepare($query);
        $result->bindParam(':league_id', $data['new_season_league_id'], PDO::PARAM_INT);
        $result->bindParam(':season_year', $data['new_season_year'], PDO::PARAM_STR);
        $result->bindParam(':season_desc', $data['new_season_desc'], PDO::PARAM_STR);

        // Execution.
        $result->execute();
    }

    public function updateSeason($data)
    {
        $query = '
            UPDATE seasons SET 
            season_year = :season_year, 
            season_desc = :season_desc, 
            league_id = :league_id 
            WHERE season_id = :season_id;
        ';

        // Get season id.
        $seasonID = $data['season_id'];

        // Preparing statement.
        $result = $this->db->prepare($query);
        $result->bindParam(':league_id', $data["season_league_id_{$seasonID}"], PDO::PARAM_INT);
        $result->bindParam(':season_year', $data["season_year_{$seasonID}"], PDO::PARAM_STR);
        $result->bindParam(':season_desc', $data["season_desc_{$seasonID}"], PDO::PARAM_STR);
        $result->bindParam(':season_id', $data['season_id'], PDO::PARAM_INT);

        // Execution.
        $result->execute();
    }

    // ***** TEAM RELATED METHODS ***** //

    public function getAllTeams()
    {
        $query = '
            SELECT * FROM teams 
            INNER JOIN sports ON teams.sport_id = sports.sport_id 
            INNER JOIN leagues ON teams.league_id = leagues.league_id 
            INNER JOIN seasons ON teams.season_id = seasons.season_id 
            ORDER BY teams.team_id ASC;
        ';

        $result = $this->db->prepare($query);

        if ($result->execute()) {
            return $result->fetchAll(PDO::FETCH_ASSOC);
        }

        return null;
    }

    public function getTeamsByLeagueId($id)
    {
        $query = '
            SELECT * FROM teams 
            INNER JOIN sports ON teams.sport_id = sports.sport_id 
            INNER JOIN leagues ON teams.league_id = leagues.league_id 
            INNER JOIN seasons ON teams.season_id = seasons.season_id 
            WHERE teams.league_id = :league_id 
            ORDER BY teams.team_id ASC;
        ';

        $result = $this->db->prepare($query);
        $result->bindParam(':league_id', $id, PDO::PARAM_INT);

        if ($result->execute()) {
            return $result->fetchAll(PDO::FETCH_ASSOC);
        }

        return null;
    }

    public function getTeamsBySeasonId($id)
    {
        $query = '
            SELECT * FROM teams INNER JOIN seasons 
            ON teams.season_id = seasons.season_id 
            WHERE teams.season_id = :season_id 
            ORDER BY teams.team_id ASC;
        ';

        $result = $this->db->prepare($query);
        $result->bindParam(':season_id', $id, PDO::PARAM_INT);

        if ($result->execute()) {
            return $result->fetchAll(PDO::FETCH_ASSOC);
        }

        return null;
    }

    public function getTeamDataByTeamId($column, $id)
    {
        $query = "
            SELECT {$column} FROM teams 
            INNER JOIN sports ON teams.sport_id = sports.sport_id 
            INNER JOIN leagues ON teams.league_id = leagues.league_id 
            INNER JOIN seasons ON teams.season_id = seasons.season_id 
            WHERE teams.team_id = :team_id;
        ";

        $result = $this->db->prepare($query);
        $result->bindParam(':team_id', $id, PDO::PARAM_INT);

        if ($result->execute()) {
            return $result->fetchAll(PDO::FETCH_ASSOC);
        }

        return null;
    }

    public function insertNewTeam($data)
    {
        $query = '
            INSERT INTO teams (
                team_name, sport_id, league_id, season_id, 
                home_color, away_color, max_players
            ) VALUES (
                :team_name, :sport_id, :league_id, :season_id, 
                :home_color, :away_color, :max_players 
            );
        ';

        // Preparing statement.
        $result = $this->db->prepare($query);
        $result->bindParam(':team_name', $data['new_team_name'], PDO::PARAM_STR);
        $result->bindParam(':sport_id', $data['new_team_sport_id'], PDO::PARAM_INT);
        $result->bindParam(':league_id', $data['new_team_league_id'], PDO::PARAM_INT);
        $result->bindParam(':season_id', $data['new_team_season_id'], PDO::PARAM_INT);
        $result->bindParam(':home_color', $data['new_team_home_color'], PDO::PARAM_STR);
        $result->bindParam(':away_color', $data['new_team_away_color'], PDO::PARAM_STR);
        $result->bindParam(':max_players', $data['new_team_max_players'], PDO::PARAM_INT);

        // Execution.
        $result->execute();
    }

    public function updateTeam($data)
    {
        $query = '
            UPDATE teams SET
            team_name = :team_name, league_id = :league_id, season_id = :season_id, 
            max_players = :max_players, home_color = :home_color, away_color = :away_color 
            WHERE team_id = :team_id;
        ';

        // Get team id.
        $teamID = $data['team_id'];

        // Preparing statement.
        $result = $this->db->prepare($query);
        $result->bindParam(':team_name', $data["team_name_{$teamID}"], PDO::PARAM_STR);
        $result->bindParam(':league_id', $data["team_league_id_{$teamID}"], PDO::PARAM_INT);
        $result->bindParam(':season_id', $data["team_season_id_{$teamID}"], PDO::PARAM_INT);
        $result->bindParam(':max_players', $data["team_max_players_{$teamID}"], PDO::PARAM_INT);
        $result->bindParam(':home_color', $data["team_home_color_{$teamID}"], PDO::PARAM_STR);
        $result->bindParam(':away_color', $data["team_away_color_{$teamID}"], PDO::PARAM_STR);
        $result->bindParam(':team_id', $data['team_id'], PDO::PARAM_INT);

        // Execution.
        $result->execute();
    }

    // ***** PLAYER RELATED METHODS ***** //

    public function getPlayersByTeamId($id)
    {
        $query = '
            SELECT * FROM players INNER JOIN positions 
            ON players.position_id = positions.position_id 
            WHERE players.team_id = :team_id 
            ORDER BY positions.position_id ASC;
        ';

        $result = $this->db->prepare($query);
        $result->bindParam(':team_id', $id, PDO::PARAM_INT);

        if ($result->execute()) {
            return $result->fetchAll(PDO::FETCH_ASSOC);
        }

        return null;
    }

    public function insertNewPlayer($data)
    {
        $query = '
            INSERT INTO players (
                player_first, player_last, player_dob,
                team_id, position_id, player_jersey
            ) VALUES (
                :player_first, :player_last, :player_dob,
                :team_id, :position_id, :player_jersey
            );
        ';

        // Preparing statement.
        $result = $this->db->prepare($query);
        $result->bindParam(':player_first', $data['new_player_first'], PDO::PARAM_STR);
        $result->bindParam(':player_last', $data['new_player_last'], PDO::PARAM_STR);
        $result->bindParam(':player_dob', $data['new_player_dob'], PDO::PARAM_STR);
        $result->bindParam(':team_id', $data['team_id'], PDO::PARAM_INT);
        $result->bindParam(':position_id', $data['new_position_id'], PDO::PARAM_INT);
        $result->bindParam(':player_jersey', $data['new_jersey_number'], PDO::PARAM_INT);

        // Execution.
        $result->execute();
    }

    public function updateTeamPlayer($data)
    {
        $query = '
            UPDATE players SET 
            player_first = :player_first, player_last = :player_last, player_dob = :player_dob,
            position_id = :position_id, player_jersey = :player_jersey 
            WHERE player_id = :player_id;
        ';

        // Get player id.
        $playerID = $data['player_id'];

        // Preparing statement.
        $result = $this->db->prepare($query);
        $result->bindParam(':player_first', $data["player_first_{$playerID}"], PDO::PARAM_STR);
        $result->bindParam(':player_last', $data["player_last_{$playerID}"], PDO::PARAM_STR);
        $result->bindParam(':player_dob', $data["player_dob_{$playerID}"], PDO::PARAM_STR);
        $result->bindParam(':position_id', $data["player_position_id_{$playerID}"], PDO::PARAM_INT);
        $result->bindParam(':player_jersey', $data["player_jersey_{$playerID}"], PDO::PARAM_INT);
        $result->bindParam(':player_id', $playerID, PDO::PARAM_INT);

        // Execution.
        $result->execute();
    }

    // ***** POSITION RELATED METHODS ***** //

    public function getAllPositions()
    {
        $query = '
            SELECT * FROM positions INNER JOIN sports 
            ON positions.sport_id = sports.sport_id 
            ORDER BY positions.position_id ASC;
        ';

        $result = $this->db->prepare($query);

        if ($result->execute()) {
            return $result->fetchAll(PDO::FETCH_ASSOC);
        }

        return null;
    }

    public function getPositionsBySportId($id)
    {
        $query = '
            SELECT * FROM positions INNER JOIN sports 
            ON positions.sport_id = sports.sport_id 
            WHERE positions.sport_id = :sport_id;
        ';

        $result = $this->db->prepare($query);
        $result->bindParam(':sport_id', $id, PDO::PARAM_INT);

        if ($result->execute()) {
            return $result->fetchAll(PDO::FETCH_ASSOC);
        }

        return null;
    }

    public function insertNewPosition($data)
    {
        $query = '
            INSERT INTO positions (position_name, sport_id) 
            VALUES (:position_name, :sport_id);
        ';

        // Preparing statement.
        $result = $this->db->prepare($query);
        $result->bindParam(':position_name', $data['new_position_name'], PDO::PARAM_STR);
        $result->bindParam(':sport_id', $data['new_position_sport_id'], PDO::PARAM_INT);

        // Execution.
        $result->execute();
    }

    public function updatePosition($data)
    {
        $query = '
            UPDATE positions SET 
            position_name = :position_name, sport_id = :sport_id 
            WHERE position_id = :position_id;
        ';

        // Get position id.
        $positionID = $data['position_id'];

        // Preparing statement.
        $result = $this->db->prepare($query);
        $result->bindParam(':position_name', $data["position_name_{$positionID}"], PDO::PARAM_STR);
        $result->bindParam(':sport_id', $data["position_sport_id_{$positionID}"], PDO::PARAM_INT);
        $result->bindParam(':position_id', $positionID, PDO::PARAM_INT);

        // Execution.
        $result->execute();
    }

    // ***** TEAM STAFF RELATED METHODS ***** //

    public function getStaffByTeamId($id)
    {
        $query = '
            SELECT * FROM users INNER JOIN roles 
            ON users.role_id = roles.role_id 
            WHERE team_id = :team_id 
            ORDER BY users.role_id ASC;
        ';

        $result = $this->db->prepare($query);
        $result->bindParam(':team_id', $id, PDO::PARAM_INT);

        if ($result->execute()) {
            return $result->fetchAll(PDO::FETCH_ASSOC);
        }

        return null;
    }

    public function updateTeamStaff($data)
    {
        $query = '
            UPDATE users SET 
            role_id = :role_id, username = :username
            WHERE users.user_id = :user_id;
        ';

        // Get staff id.
        $staffID = $data['staff_id'];

        // Preparing statement.
        $result = $this->db->prepare($query);
        $result->bindParam(':role_id', $data['staff_role_id'], PDO::PARAM_INT);
        $result->bindParam(':username', $data["staff_username_{$staffID}"], PDO::PARAM_STR);
        $result->bindParam(':user_id', $staffID, PDO::PARAM_INT);

        // Execution.
        $result->execute();
    }

    // ***** SCHEDULE RELATED METHODS ***** //

    public function getScheduleByTeamId($id)
    {
        $sides = ['home', 'away'];
        foreach ($sides as $side) {
            $query = "
                SELECT * FROM schedule INNER JOIN teams 
                ON schedule.{$side}_team_id = teams.team_id 
                WHERE teams.team_id = :team_id 
                ORDER BY schedule.scheduled DESC;
            ";

            $result = $this->db->prepare($query);
            $result->bindParam(':team_id', $id, PDO::PARAM_INT);

            if ($result->execute()) {
                $return[] = $result->fetchAll(PDO::FETCH_ASSOC);
            }
        }

        return $return;
    }

    public function updateScheduleTeamData($data)
    {
        $query = '
            UPDATE teams SET 
            team_name = :team_name, home_color = :home_color, away_color = :away_color 
            WHERE team_id = :team_id;
        ';

        // Preparing statement.
        $result = $this->db->prepare($query);
        $result->bindParam(':team_name', $data['team_name'], PDO::PARAM_STR);
        $result->bindParam(':home_color', $data['home_color'], PDO::PARAM_STR);
        $result->bindParam(':away_color', $data['away_color'], PDO::PARAM_STR);
        $result->bindParam(':team_id', $data['team_id'], PDO::PARAM_INT);

        // Execution.
        $result->execute();
    }

    public function updateScheduleGame($data)
    {
        $query = '
            UPDATE schedule SET 
            home_team_id = :home_team_id, home_score = :home_score, away_team_id = :away_team_id,
            away_score = :away_score, scheduled = :scheduled, status = :status 
            WHERE schedule_id = :schedule_id;
        ';

        // Preparing statement.
        $result = $this->db->prepare($query);
        $result->bindParam(':home_team_id', $data['edit_home_team_id'], PDO::PARAM_INT);
        $result->bindParam(':home_score', $data['edit_home_score'], PDO::PARAM_INT);
        $result->bindParam(':away_team_id', $data['edit_away_team_id'], PDO::PARAM_INT);
        $result->bindParam(':away_score', $data['edit_away_score'], PDO::PARAM_INT);
        $result->bindParam(':scheduled', $data['edit_scheduled'], PDO::PARAM_STR);
        $result->bindParam(':status', $data['edit_status'], PDO::PARAM_INT);
        $result->bindParam(':schedule_id', $data['schedule_id'], PDO::PARAM_INT);

        // Execution.
        $result->execute();
    }

    public function insertNewScheduleGame($data)
    {
        $query = '
            INSERT INTO schedule (
                sport_id, league_id, season_id, 
                home_team_id, away_team_id, home_score, 
                away_score, scheduled, status
            ) 
            VALUES (
                :sport_id, :league_id, :season_id, 
                :home_team_id, :away_team_id, :home_score, 
                :away_score, :scheduled, :status
            );
        ';

        // Preparing statement.
        $result = $this->db->prepare($query);
        $result->bindParam(':sport_id', $data['sport_id'], PDO::PARAM_INT);
        $result->bindParam(':league_id', $data['league_id'], PDO::PARAM_INT);
        $result->bindParam(':season_id', $data['new_season_id'], PDO::PARAM_INT);
        $result->bindParam(':home_team_id', $data['new_home_team_id'], PDO::PARAM_INT);
        $result->bindParam(':away_team_id', $data['new_away_team_id'], PDO::PARAM_INT);
        $result->bindParam(':home_score', $data['new_home_score'], PDO::PARAM_INT);
        $result->bindParam(':away_score', $data['new_away_score'], PDO::PARAM_INT);
        $result->bindParam(':scheduled', $data['new_scheduled'], PDO::PARAM_STR);
        $result->bindParam(':status', $data['new_status'], PDO::PARAM_INT);

        // Execution.
        $result->execute();
    }

    // ***** DYNAMIC METHODS ***** //

    public function getLastRowId($table, $column)
    {
        $query = "SELECT MAX({$column}_id) AS id FROM {$table};";
        $result = $this->db->prepare($query);

        if ($result->execute()) {
            return $result->fetch(PDO::FETCH_ASSOC);
        }

        return null;
    }

    public function deleteRowById($table, $column, $id)
    {
        $query = "DELETE FROM {$table} WHERE {$column}_id = :{$column}_id";
        $result = $this->db->prepare($query);
        $result->bindParam(":{$column}_id", $id, PDO::PARAM_INT);
        $result->execute();
    }
}

$db = Database::getInstance();
