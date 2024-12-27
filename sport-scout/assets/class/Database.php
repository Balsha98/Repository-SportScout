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

    public static function get_instance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new Database('localhost', 'sport_scout', 'root', '');
        }

        return self::$instance;
    }

    public function alter_auto_increment($table, $row_id)
    {
        $query = "ALTER TABLE {$table} AUTO_INCREMENT = {$row_id};";
        $result = $this->db->prepare($query);
        $result->execute();
    }

    // ***** USER RELATED METHODS ***** //

    public function get_all_users()
    {
        $query = '
            SELECT * FROM users 
            INNER JOIN roles ON 
            users.role_id = roles.role_id 
            ORDER BY users.user_id ASC;
        ';

        $result = $this->db->prepare($query);
        $result->execute();

        $return = $result->fetchAll(PDO::FETCH_ASSOC);

        $array = [];
        foreach ($return as $data) {
            $league_id = (int) $data['league_id'];
            $team_id = (int) $data['team_id'];

            if ($league_id === 0 && $team_id === 0) {
                $data['league_name'] = 'All';
                $data['team_name'] = 'All';
            } else if ($league_id !== 0 && $team_id === 0) {
                $league_data = $this->get_league_data_by_league_id($league_id);

                if (count($league_data) > 0) {
                    $league_name = $league_data[0]['league_name'];

                    $data['league_name'] = $league_name;
                    $data['team_name'] = "All Within The {$league_name}";
                } else {
                    $data['league_name'] = '';
                    $data['team_name'] = '';
                }
            } else {
                $league_data = $this->get_league_data_by_league_id($league_id);
                $team_data = $this->get_team_data_by_team_id('team_name', $team_id);

                if (count($league_data) > 0 && count($team_data) > 0) {
                    $league_name = $league_data[0]['league_name'];
                    $team_name = $team_data[0]['team_name'];

                    $data['league_name'] = $league_name;
                    $data['team_name'] = $team_name;
                } else {
                    $data['league_name'] = '';
                    $data['team_name'] = '';
                }
            }

            $array[] = $data;
        }

        return $array;
    }

    public function verify_user($username, $password)
    {
        $user = $this->get_current_user_data($username);

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

    public function get_current_user_data($username)
    {
        $query = '
            SELECT * FROM users 
            INNER JOIN roles 
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

    public function get_last_user_id()
    {
        $query = 'SELECT MAX(user_id) AS user_id FROM users;';
        $result = $this->db->prepare($query);
        $result->execute();

        return $result->fetch(PDO::FETCH_ASSOC);
    }

    public function insert_new_user($data)
    {
        $query = '
            INSERT INTO users (role_id, username, password, league_id, team_id) 
            VALUES (:role_id, :username, :password, :league_id, :team_id);
        ';

        // Preparing statement.
        $result = $this->db->prepare($query);

        // Hashing the password.
        $hashedPassword = hash('sha256', $data['new_password']);

        // Binding each value individually.
        $result->bindParam(':role_id', $data['new_role_id'], PDO::PARAM_INT);
        $result->bindParam(':username', $data['new_username'], PDO::PARAM_STR);
        $result->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
        $result->bindParam(':league_id', $data['league_id'], PDO::PARAM_INT);
        $result->bindParam(':team_id', $data['team_id'], PDO::PARAM_INT);

        // Execution.
        $result->execute();
    }

    public function update_admin_user($data)
    {
        $query = '
            UPDATE users SET 
            role_id = :role_id, 
            username = :username, 
            league_id = :league_id, 
            team_id = :team_id 
            WHERE user_id = :user_id;
        ';

        $result = $this->db->prepare($query);

        // Get user id.
        $user_id = $data['user_id'];

        $result->bindParam(':role_id', $data['role_id'], PDO::PARAM_INT);
        $result->bindParam(':username', $data["username_{$user_id}"], PDO::PARAM_STR);
        $result->bindParam(':league_id', $data["user_league_id_{$user_id}"], PDO::PARAM_INT);
        $result->bindParam(':team_id', $data["user_team_id_{$user_id}"], PDO::PARAM_INT);
        $result->bindParam(':user_id', $user_id, PDO::PARAM_INT);

        $result->execute();
    }

    public function delete_user_by_id($user_id)
    {
        $query = 'DELETE FROM users WHERE user_id = :user_id';
        $result = $this->db->prepare($query);
        $result->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $result->execute();
    }

    // ***** SPORT RELATED METHODS ***** //

    public function get_all_sports()
    {
        $query = '
            SELECT * FROM sports
            ORDER BY sport_id ASC;
        ';

        $result = $this->db->prepare($query);
        $result->execute();

        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_last_sport_id()
    {
        $query = 'SELECT MAX(sport_id) AS sport_id FROM sports;';
        $result = $this->db->prepare($query);
        $result->execute();

        return $result->fetch(PDO::FETCH_ASSOC);
    }

    public function insert_new_sport($data)
    {
        $query = 'INSERT INTO sports (sport_name) VALUES (:sport_name);';
        $result = $this->db->prepare($query);
        $result->bindParam(':sport_name', $data['new_sport_name'], PDO::PARAM_STR);
        $result->execute();
    }

    public function update_sport($data)
    {
        $query = '
            UPDATE sports SET 
            sport_name = :sport_name
            WHERE sports.sport_id = :sport_id;
        ';

        // Get sport id.
        $sport_id = $data['sport_id'];

        // Prepare query.
        $result = $this->db->prepare($query);
        $result->bindParam(':sport_name', $data["sport_name_{$sport_id}"], PDO::PARAM_STR);
        $result->bindParam(':sport_id', $sport_id, PDO::PARAM_INT);

        $result->execute();
    }

    public function delete_sport_by_id($sport_id)
    {
        $query = 'DELETE FROM sports WHERE sport_id = :sport_id;';
        $result = $this->db->prepare($query);
        $result->bindParam(':sport_id', $sport_id, PDO::PARAM_INT);
        $result->execute();
    }

    // ***** LEAGUE RELATED METHODS ***** //

    public function get_all_leagues()
    {
        $query = '
            SELECT * FROM leagues 
            INNER JOIN sports 
            ON leagues.sport_id = sports.sport_id
            ORDER BY leagues.league_id ASC;
        ';

        $result = $this->db->prepare($query);
        $result->execute();

        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_league_data_by_league_id($league_id)
    {
        $query = '
            SELECT * FROM leagues 
            INNER JOIN sports 
            ON leagues.sport_id = sports.sport_id 
            WHERE leagues.league_id = :league_id;
        ';

        $result = $this->db->prepare($query);
        $result->bindParam(':league_id', $league_id, PDO::PARAM_INT);
        $result->execute();

        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_leagues_by_sport_id($sport_id)
    {
        $query = '
            SELECT * FROM leagues 
            INNER JOIN sports 
            ON leagues.sport_id = sports.sport_id 
            WHERE leagues.sport_id = :sport_id;
        ';

        $result = $this->db->prepare($query);
        $result->bindParam(':sport_id', $sport_id, PDO::PARAM_INT);
        $result->execute();

        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_last_league_id()
    {
        $query = 'SELECT MAX(league_id) AS league_id FROM leagues;';

        $result = $this->db->prepare($query);
        $result->execute();

        return $result->fetch(PDO::FETCH_ASSOC);
    }

    public function insert_new_league($data)
    {
        $query = '
            INSERT INTO leagues (league_name, sport_id) 
            VALUES (:league_name, :sport_id);    
        ';

        $result = $this->db->prepare($query);
        $result->bindParam(':league_name', $data['new_league_name'], PDO::PARAM_STR);
        $result->bindParam(':sport_id', $data['league_sport_id'], PDO::PARAM_INT);
        $result->execute();
    }

    public function update_league($data)
    {
        $query = '
            UPDATE leagues SET 
            league_name = :league_name, 
            sport_id = :sport_id 
            WHERE league_id = :league_id;   
        ';

        $result = $this->db->prepare($query);

        $league_id = $data['league_id'];

        $result->bindParam(':league_name', $data["league_name_{$league_id}"], PDO::PARAM_STR);
        $result->bindParam(':sport_id', $data["league_sport_id_{$league_id}"], PDO::PARAM_INT);
        $result->bindParam(':league_id', $league_id, PDO::PARAM_INT);

        $result->execute();
    }

    public function delete_league_by_id($league_id)
    {
        $query = 'DELETE FROM leagues WHERE league_id = :league_id;';
        $result = $this->db->prepare($query);
        $result->bindParam(':league_id', $league_id, PDO::PARAM_INT);
        $result->execute();
    }

    // ***** SEASON RELATED METHODS ***** //

    public function get_all_seasons()
    {
        $query = '
            SELECT * FROM seasons 
            INNER JOIN leagues 
            ON seasons.league_id = leagues.league_id
            INNER JOIN sports 
            ON leagues.sport_id = sports.sport_id
            ORDER BY seasons.season_id ASC;
        ';

        $result = $this->db->prepare($query);
        $result->execute();

        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_seasons_by_league_id($league_id)
    {
        $query = '
            SELECT * FROM seasons 
            INNER JOIN leagues 
            ON seasons.league_id = leagues.league_id 
            INNER JOIN sports 
            ON leagues.sport_id = sports.sport_id 
            WHERE leagues.league_id = :league_id;
        ';

        $result = $this->db->prepare($query);
        $result->bindParam(':league_id', $league_id, PDO::PARAM_INT);
        $result->execute();

        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_last_season_id()
    {
        $query = 'SELECT MAX(season_id) AS season_id FROM seasons;';
        $result = $this->db->prepare($query);
        $result->execute();

        return $result->fetch(PDO::FETCH_ASSOC);
    }

    public function insert_new_season($data)
    {
        $query = '
            INSERT INTO seasons (
                season_year, season_desc, league_id
            ) VALUES (
                :season_year, :season_desc, :league_id 
            );
        ';

        $result = $this->db->prepare($query);

        $result->bindParam(':season_year', $data['new_season_year'], PDO::PARAM_STR);
        $result->bindParam(':season_desc', $data['new_season_desc'], PDO::PARAM_STR);
        $result->bindParam(':league_id', $data['new_season_league_id'], PDO::PARAM_INT);

        $result->execute();
    }

    public function update_season($data)
    {
        $query = '
            UPDATE seasons SET 
            season_year = :season_year, 
            season_desc = :season_desc, 
            league_id = :league_id 
            WHERE season_id = :season_id;
        ';

        // Prepare statement.
        $result = $this->db->prepare($query);

        // Get season id.
        $season_id = $data['season_id'];

        // Bind each value individually.
        $result->bindParam(':season_year', $data["season_year_{$season_id}"], PDO::PARAM_STR);
        $result->bindParam(':season_desc', $data["season_desc_{$season_id}"], PDO::PARAM_STR);
        $result->bindParam(':league_id', $data["season_league_id_{$season_id}"], PDO::PARAM_INT);
        $result->bindParam(':season_id', $season_id, PDO::PARAM_INT);

        // Execution.
        $result->execute();
    }

    public function delete_season_by_id($season_id)
    {
        $query = 'DELETE FROM seasons WHERE season_id = :season_id;';
        $result = $this->db->prepare($query);
        $result->bindParam(':season_id', $season_id, PDO::PARAM_INT);
        $result->execute();
    }

    // ***** TEAM RELATED METHODS ***** //

    public function get_all_teams()
    {
        $query = '
            SELECT * FROM teams
            INNER JOIN sports
            ON teams.sport_id = sports.sport_id
            INNER JOIN leagues
            ON teams.league_id = leagues.league_id
            INNER JOIN seasons
            ON teams.season_id = seasons.season_id
            ORDER BY teams.team_id ASC;
        ';

        $result = $this->db->prepare($query);
        $result->execute();

        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_last_team_id()
    {
        $query = 'SELECT MAX(team_id) AS team_id FROM teams;';

        $result = $this->db->prepare($query);
        $result->execute();

        return $result->fetch(PDO::FETCH_ASSOC);
    }

    public function get_teams_by_league_id($league_id)
    {
        $query = '
            SELECT * FROM teams
            INNER JOIN sports
            ON teams.sport_id = sports.sport_id
            INNER JOIN leagues
            ON teams.league_id = leagues.league_id
            INNER JOIN seasons
            ON teams.season_id = seasons.season_id 
            WHERE teams.league_id = :league_id 
            ORDER BY teams.team_id ASC;
        ';

        $result = $this->db->prepare($query);
        $result->bindParam(':league_id', $league_id, PDO::PARAM_INT);
        $result->execute();

        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_teams_by_season_id($season_id)
    {
        $query = '
            SELECT * FROM teams
            INNER JOIN seasons
            ON teams.season_id = seasons.season_id 
            WHERE teams.season_id = :season_id 
            ORDER BY teams.team_id ASC;
        ';

        $result = $this->db->prepare($query);
        $result->bindParam(':season_id', $season_id, PDO::PARAM_INT);
        $result->execute();

        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_team_data_by_team_id($column, $team_id)
    {
        $query = "
            SELECT {$column} FROM teams
            INNER JOIN sports
            ON teams.sport_id = sports.sport_id
            INNER JOIN leagues
            ON teams.league_id = leagues.league_id
            INNER JOIN seasons
            ON teams.season_id = seasons.season_id 
            WHERE teams.team_id = :team_id;
        ";

        $result = $this->db->prepare($query);
        $result->bindParam(':team_id', $team_id, PDO::PARAM_INT);
        $result->execute();

        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insert_new_team($data)
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

        // Prepare the query.
        $result = $this->db->prepare($query);

        // Bind each value individually.
        $result->bindParam(':team_name', $data['new_team_name'], PDO::PARAM_STR);
        $result->bindParam(':sport_id', $data['team_sport_id'], PDO::PARAM_INT);
        $result->bindParam(':league_id', $data['team_league_id'], PDO::PARAM_INT);
        $result->bindParam(':season_id', $data['team_season_id'], PDO::PARAM_INT);
        $result->bindParam(':home_color', $data['new_team_home_color'], PDO::PARAM_STR);
        $result->bindParam(':away_color', $data['new_team_away_color'], PDO::PARAM_STR);
        $result->bindParam(':max_players', $data['new_team_max_players'], PDO::PARAM_INT);

        // Execution.
        $result->execute();
    }

    public function update_team($data)
    {
        $query = '
            UPDATE teams SET
            team_name = :team_name, 
            league_id = :league_id, 
            season_id = :season_id, 
            max_players = :max_players, 
            home_color = :home_color, 
            away_color = :away_color 
            WHERE team_id = :team_id;
        ';

        // Prepare query.
        $result = $this->db->prepare($query);

        // Get team id.
        $team_id = $data['team_id'];

        // Bind each value individually.
        $result->bindParam(':team_name', $data["team_name_{$team_id}"], PDO::PARAM_STR);
        $result->bindParam(':league_id', $data["team_league_id_{$team_id}"], PDO::PARAM_INT);
        $result->bindParam(':season_id', $data["team_season_id_{$team_id}"], PDO::PARAM_INT);
        $result->bindParam(':max_players', $data["team_max_players_{$team_id}"], PDO::PARAM_INT);
        $result->bindParam(':home_color', $data["team_home_color_{$team_id}"], PDO::PARAM_STR);
        $result->bindParam(':away_color', $data["team_away_color_{$team_id}"], PDO::PARAM_STR);
        $result->bindParam(':team_id', $team_id, PDO::PARAM_INT);

        // Execution.
        $result->execute();
    }

    public function get_players_by_team_id($team_id)
    {
        $query = '
            SELECT * FROM players 
            INNER JOIN positions 
            ON players.position_id = positions.position_id 
            WHERE players.team_id = :team_id 
            ORDER BY positions.position_id ASC;
        ';

        $result = $this->db->prepare($query);
        $result->bindParam(':team_id', $team_id, PDO::PARAM_INT);
        $result->execute();

        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_last_player_id()
    {
        $query = 'SELECT MAX(player_id) AS player_id FROM players;';
        $result = $this->db->prepare($query);
        $result->execute();

        return $result->fetch(PDO::FETCH_ASSOC);
    }

    public function insert_new_player($data)
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

        $result = $this->db->prepare($query);

        // Binding each value individually.
        $result->bindParam(':player_first', $data['new_player_first'], PDO::PARAM_STR);
        $result->bindParam(':player_last', $data['new_player_last'], PDO::PARAM_STR);
        $result->bindParam(':player_dob', $data['new_player_dob'], PDO::PARAM_STR);
        $result->bindParam(':team_id', $data['team_id'], PDO::PARAM_INT);
        $result->bindParam(':position_id', $data['new_position_id'], PDO::PARAM_INT);
        $result->bindParam(':player_jersey', $data['new_jersey_number'], PDO::PARAM_INT);

        // Execution.
        $result->execute();
    }

    public function update_team_player($data)
    {
        $query = '
            UPDATE players SET 
            player_first = :player_first,
            player_last = :player_last,
            player_dob = :player_dob,
            position_id = :position_id,
            player_jersey = :player_jersey 
            WHERE player_id = :player_id;
        ';

        $result = $this->db->prepare($query);

        $player_id = $data['player_id'];

        $result->bindParam(':player_first', $data["player_first_{$player_id}"], PDO::PARAM_STR);
        $result->bindParam(':player_last', $data["player_last_{$player_id}"], PDO::PARAM_STR);
        $result->bindParam(':player_dob', $data["player_dob_{$player_id}"], PDO::PARAM_STR);
        $result->bindParam(':position_id', $data["player_position_id_{$player_id}"], PDO::PARAM_INT);
        $result->bindParam(':player_jersey', $data["player_jersey_{$player_id}"], PDO::PARAM_INT);
        $result->bindParam(':player_id', $player_id, PDO::PARAM_INT);

        $result->execute();
    }

    public function delete_team_player_by_id($player_id)
    {
        $query = 'DELETE FROM players WHERE player_id = :player_id';
        $result = $this->db->prepare($query);
        $result->bindParam(':player_id', $player_id, PDO::PARAM_INT);
        $result->execute();
    }

    public function get_all_positions()
    {
        $query = '
            SELECT * FROM positions 
            INNER JOIN sports 
            ON positions.sport_id = sports.sport_id 
            ORDER BY positions.position_id ASC;
        ';

        $result = $this->db->prepare($query);
        $result->execute();

        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_positions_by_sport_id($sport_id)
    {
        $query = '
            SELECT * FROM positions 
            INNER JOIN sports 
            ON positions.sport_id = sports.sport_id 
            WHERE positions.sport_id = :sport_id;
        ';

        $result = $this->db->prepare($query);
        $result->bindParam(':sport_id', $sport_id, PDO::PARAM_INT);
        $result->execute();

        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_last_position_id()
    {
        $query = 'SELECT MAX(position_id) AS position_id FROM positions;';
        $result = $this->db->prepare($query);
        $result->execute();

        return $result->fetch(PDO::FETCH_ASSOC);
    }

    public function insert_new_position($data)
    {
        $query = '
            INSERT INTO positions (position_name, sport_id) 
            VALUES (:position_name, :sport_id);
        ';

        $result = $this->db->prepare($query);

        // Binding each value individually.
        $result->bindParam(':position_name', $data['new_position_name'], PDO::PARAM_STR);
        $result->bindParam(':sport_id', $data['new_position_sport_id'], PDO::PARAM_INT);

        // Execution.
        $result->execute();
    }

    public function update_position($data)
    {
        $query = '
            UPDATE positions SET 
            position_name = :position_name, 
            sport_id = :sport_id 
            WHERE position_id = :position_id;
        ';

        $result = $this->db->prepare($query);

        $position_id = $data['position_id'];

        $result->bindParam(':position_name', $data["position_name_{$position_id}"], PDO::PARAM_STR);
        $result->bindParam(':sport_id', $data["position_sport_id_{$position_id}"], PDO::PARAM_INT);
        $result->bindParam(':position_id', $position_id, PDO::PARAM_INT);
    }

    public function delete_position_by_id($position_id)
    {
        $query = 'DELETE FROM positions WHERE position_id = :position_id';
        $result = $this->db->prepare($query);
        $result->bindParam(':position_id', $position_id, PDO::PARAM_INT);
        $result->execute();
    }

    public function get_staff_by_team_id($team_id)
    {
        $query = '
            SELECT * FROM users 
            INNER JOIN roles 
            ON users.role_id = roles.role_id 
            WHERE team_id = :team_id 
            ORDER BY users.role_id ASC;
        ';

        $result = $this->db->prepare($query);
        $result->bindParam(':team_id', $team_id, PDO::PARAM_INT);
        $result->execute();

        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update_team_staff($data)
    {
        $query = '
            UPDATE users SET 
            role_id = :role_id,
            username = :username
            WHERE users.user_id = :user_id;
        ';

        // Get user id.
        $staff_id = $data['staff_id'];

        $result = $this->db->prepare($query);
        $result->bindParam(':role_id', $data['staff_role_id'], PDO::PARAM_INT);
        $result->bindParam(':username', $data["staff_username_{$staff_id}"], PDO::PARAM_STR);
        $result->bindParam(':user_id', $staff_id, PDO::PARAM_INT);

        $result->execute();
    }

    // ***** SCHEDULE RELATED METHODS ***** //

    public function get_last_scheduled_game_id()
    {
        $query = 'SELECT MAX(schedule_id) AS schedule_id FROM schedule;';
        $result = $this->db->prepare($query);
        $result->execute();

        return $result->fetch(PDO::FETCH_ASSOC);
    }

    public function get_schedule_by_team_id($team_id)
    {
        $return = [];
        $sides = ['home', 'away'];
        foreach ($sides as $side) {
            $query = "
                SELECT * FROM schedule 
                INNER JOIN teams 
                ON schedule.{$side}_team_id = teams.team_id 
                WHERE teams.team_id = :team_id 
                ORDER BY schedule.scheduled DESC;
            ";

            $result = $this->db->prepare($query);
            $result->bindParam(':team_id', $team_id, PDO::PARAM_INT);
            $result->execute();

            $return[] = $result->fetchAll(PDO::FETCH_ASSOC);
        }

        return $return;
    }

    public function update_schedule_team_data($data)
    {
        $query = '
            UPDATE teams SET 
            team_name = :team_name,
            home_color = :home_color, 
            away_color = :away_color 
            WHERE team_id = :team_id;
        ';

        $result = $this->db->prepare($query);
        $result->bindParam(':team_name', $data['team_name'], PDO::PARAM_STR);
        $result->bindParam(':home_color', $data['home_color'], PDO::PARAM_STR);
        $result->bindParam(':away_color', $data['away_color'], PDO::PARAM_STR);
        $result->bindParam(':team_id', $data['team_id'], PDO::PARAM_INT);

        $result->execute();
    }

    public function update_schedule_game($data)
    {
        $query = '
            UPDATE schedule SET 
            home_team_id = :home_team_id,
            home_score = :home_score,
            away_team_id = :away_team_id,
            away_score = :away_score,
            scheduled = :scheduled,
            status = :status 
            WHERE schedule_id = :schedule_id;
        ';

        $result = $this->db->prepare($query);
        $result->bindParam(':home_team_id', $data['edit_home_team_id'], PDO::PARAM_INT);
        $result->bindParam(':home_score', $data['edit_home_score'], PDO::PARAM_INT);
        $result->bindParam(':away_team_id', $data['edit_away_team_id'], PDO::PARAM_INT);
        $result->bindParam(':away_score', $data['edit_away_score'], PDO::PARAM_INT);
        $result->bindParam(':scheduled', $data['edit_scheduled'], PDO::PARAM_STR);
        $result->bindParam(':status', $data['edit_status'], PDO::PARAM_INT);
        $result->bindParam(':schedule_id', $data['schedule_id'], PDO::PARAM_INT);

        $result->execute();
    }

    public function insert_new_schedule_game($data)
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

        $result = $this->db->prepare($query);

        // Binding each value individually.
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

    public function delete_schedule_game_by_id($schedule_id)
    {
        $query = 'DELETE FROM schedule WHERE schedule_id = :schedule_id';
        $result = $this->db->prepare($query);
        $result->bindParam(':schedule_id', $schedule_id, PDO::PARAM_INT);
        $result->execute();
    }

    public function delete_team_by_id($team_id)
    {
        $query = 'DELETE FROM teams WHERE team_id = :team_id;';
        $result = $this->db->prepare($query);
        $result->bindParam(':team_id', $team_id, PDO::PARAM_INT);
        $result->execute();
    }
}

$db = Database::get_instance();
