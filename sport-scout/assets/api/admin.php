<?php declare(strict_types=1);

require_once '../class/Database.php';
require_once '../class/Sanitize.php';
require_once '../class/Helper.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $clicked = $_POST['clicked'];
    $message = 'success';
    $data = [];

    // ADD USERS, SPORTS, LEAGUES, SEASONS, TEAMS
    if ($clicked === 'ADD_USER') {
        $last_user_id = $db->get_last_user_id()['user_id'];

        $username = Sanitize::strip_string($_POST['new_username']);
        Sanitize::full_string_search($message, $username, 25);

        $password = Sanitize::strip_string($_POST['new_password']);
        if ($password === '') {
            $message = 'fail';
        } else if (!Sanitize::is_shorter($password, 64)) {
            $message = 'fail';
            $password = '';
        }

        $role_id = '';
        $role_name = '';
        Helper::set_role_id_and_name(
            $message,
            $role_id,
            $role_name,
            'new_role_name'
        );

        $league_id = '';
        $league_name = '';
        $team_id = '';
        $team_name = '';
        Helper::set_league_and_team_names(
            $db,
            $message,
            $league_id,
            $league_name,
            $team_id,
            $team_name,
            'league_id',
            'team_id'
        );

        $data = [
            'message' => $message,
            'last_user_id' => $last_user_id,
            'new_username' => $username,
            'new_password' => $password,
            'new_role_id' => $role_id,
            'new_role_name' => $role_name,
            'league_id' => $league_id,
            'league_name' => $league_name,
            'team_id' => $team_id,
            'team_name' => $team_name
        ];

        if ($message === 'success') {
            $db->alter_auto_increment('users', $last_user_id);
            $db->insert_new_user($data);
        }
    } else if ($clicked === 'ADD_SPORT') {
        $last_sport_id = (int) $db->get_last_sport_id()['sport_id'];

        $sport_name = Sanitize::strip_string($_POST['new_sport_name']);
        Sanitize::full_string_search($message, $sport_name, 50);

        $data = [
            'message' => $message,
            'last_sport_id' => $last_sport_id,
            'new_sport_name' => $sport_name
        ];

        if ($message === 'success') {
            $db->alter_auto_increment('sports', $last_sport_id);
            $db->insert_new_sport($data);
        }
    } else if ($clicked === 'ADD_LEAGUE') {
        $last_league_id = $db->get_last_league_id()['league_id'];

        $league_name = Sanitize::strip_string($_POST['new_league_name']);
        Sanitize::full_string_search($message, $league_name, 50);

        $sport_id = '';
        $sport_name = '';
        Helper::set_sport_name(
            $db,
            $message,
            $sport_id,
            $sport_name,
            'sport_id'
        );

        $data = [
            'message' => $message,
            'last_league_id' => $last_league_id,
            'new_league_name' => $league_name,
            'league_sport_id' => $sport_id,
            'league_sport_name' => $sport_name
        ];

        if ($message === 'success') {
            $db->alter_auto_increment('leagues', $last_league_id);
            $db->insert_new_league($data);
        }
    } else if ($clicked === 'ADD_SEASON') {
        $last_season_id = $db->get_last_season_id()['season_id'];

        $season_year = $_POST['new_season_year'];
        if ($season_year === '') {
            $message = 'fail';
        } else if (!Sanitize::is_exactly($season_year, 7)) {
            $message = 'fail';
            $season_year = '';
        } else if (!Sanitize::is_year_formatted($season_year)) {
            $message = 'fail';
            $season_year = '';
        }

        $sport_name = '';
        $sport_id = '';
        Helper::set_sport_name(
            $db,
            $message,
            $sport_id,
            $sport_name,
            'sport_id'
        );

        $league_name = '';
        $league_id = '';
        Helper::set_league_name(
            $db,
            $message,
            $sport_id,
            $league_id,
            $league_name,
            'league_id'
        );

        $season_desc = Sanitize::strip_string($_POST['new_season_desc']);
        if ($season_desc === '') {
            $message = 'fail';
        } else if (!Sanitize::is_shorter($season_desc, 50)) {
            $message = 'fail';
            $season_desc = '';
        }

        $data = [
            'message' => $message,
            'last_season_id' => $last_season_id,
            'new_season_year' => $season_year,
            'new_season_sport_id' => $sport_id,
            'season_sport_name' => $sport_name,
            'new_season_league_id' => $league_id,
            'season_league_name' => $league_name,
            'new_season_desc' => $season_desc,
        ];

        if ($message === 'success') {
            $db->alter_auto_increment('seasons', $last_season_id);
            $db->insert_new_season($data);
        }
    } else if ($clicked === 'ADD_TEAM') {
        $last_team_id = $db->get_last_team_id()['team_id'];

        $team_name = Sanitize::strip_string($_POST['new_team_name']);
        Sanitize::full_string_search($message, $team_name, 50);

        $sport_id = '';
        $sport_name = '';
        Helper::set_sport_name(
            $db,
            $message,
            $sport_id,
            $sport_name,
            'sport_id'
        );

        $league_id = '';
        $league_name = '';
        Helper::set_league_name(
            $db,
            $message,
            $sport_id,
            $league_id,
            $league_name,
            'league_id'
        );

        $season_id = '';
        $season_year = '';
        Helper::set_season_year(
            $db,
            $message,
            $league_id,
            $season_id,
            $season_year,
            'season_id'
        );

        $max_players = (int) $_POST['new_team_max_players'];
        if ($max_players === '') {
            $message = 'fail';
        } else if ($max_players <= 0) {
            $message = 'fail';
            $max_players = '';
        }

        $home_color = Sanitize::strip_string($_POST['new_team_home_color']);
        Sanitize::full_color_search($message, $home_color, 25);

        $away_color = Sanitize::strip_string($_POST['new_team_away_color']);
        Sanitize::full_color_search($message, $away_color, 25);

        $data = [
            'message' => $message,
            'last_team_id' => $last_team_id,
            'new_team_name' => $team_name,
            'team_sport_id' => $sport_id,
            'team_sport_name' => $sport_name,
            'team_league_id' => $league_id,
            'team_league_name' => $league_name,
            'team_season_id' => $season_id,
            'team_season_year' => $season_year,
            'new_team_max_players' => $max_players,
            'new_team_home_color' => $home_color,
            'new_team_away_color' => $away_color
        ];

        if ($message === 'success') {
            $db->alter_auto_increment('teams', $last_team_id);
            $db->insert_new_team($data);
        }
    } else if ($clicked === 'ADD_POSITION') {
        $last_position_id = (int) $db->get_last_position_id()['position_id'];

        $position_name = Sanitize::strip_string($_POST['new_position_name']);
        Sanitize::full_string_search($message, $position_name, 50);

        $sport_id = '';
        $sport_name = '';
        Helper::set_sport_name(
            $db,
            $message,
            $sport_id,
            $sport_name,
            'sport_id'
        );

        $data = [
            'message' => $message,
            'last_position_id' => $last_position_id,
            'new_position_name' => $position_name,
            'new_position_sport_id' => $sport_id,
            'new_position_sport_name' => $sport_name
        ];

        if ($message === 'success') {
            $db->alter_auto_increment('positions', $last_position_id);
            $db->insert_new_position($data);
        }
    }

    // UPDATE USERS, SPORTS, LEAGUES, SEASONS, TEAMS
    if ($clicked === 'UPDATE_USER') {
        $user_id = (int) $_POST['user_id'];

        $username = Sanitize::strip_string($_POST['username']);
        Sanitize::full_string_search($message, $username, 25);

        $role_id = '';
        $role_name = '';
        Helper::set_role_id_and_name(
            $message,
            $role_id,
            $role_name,
            'role_name'
        );

        $league_id = '';
        $league_name = '';
        $team_id = '';
        $team_name = '';
        Helper::set_league_and_team_names(
            $db,
            $message,
            $league_id,
            $league_name,
            $team_id,
            $team_name,
            'league_id',
            'team_id'
        );

        $data = [
            'message' => $message,
            'user_id' => $user_id,
            "username_{$user_id}" => $username,
            'role_id' => $role_id,
            "user_role_name_{$user_id}" => $role_name,
            "user_league_id_{$user_id}" => $league_id,
            "user_league_name_{$user_id}" => $league_name,
            "user_team_id_{$user_id}" => $team_id,
            "user_team_name_{$user_id}" => $team_name,
        ];

        if ($message === 'success') {
            $db->update_admin_user($data);
        }
    } else if ($clicked === 'UPDATE_SPORT') {
        $sport_id = (int) $_POST['sport_id'];

        $sport_name = Sanitize::strip_string($_POST['sport_name']);
        Sanitize::full_string_search($message, $sport_name, 50);

        $data = [
            'message' => $message,
            'sport_id' => $sport_id,
            "sport_name_{$sport_id}" => $sport_name
        ];

        if ($message === 'success') {
            $db->update_sport($data);
        }
    } else if ($clicked === 'UPDATE_LEAGUE') {
        $league_id = (int) $_POST['league_id'];

        $league_name = Sanitize::strip_string($_POST['league_name']);
        Sanitize::full_string_search($message, $league_name, 50);

        $sport_id = '';
        $sport_name = '';
        Helper::set_sport_name(
            $db,
            $message,
            $sport_id,
            $sport_name,
            'sport_id'
        );

        $data = [
            'message' => $message,
            'league_id' => $league_id,
            "league_name_{$league_id}" => $league_name,
            "league_sport_id_{$league_id}" => $sport_id,
            'sport_name' => $sport_name
        ];

        if ($message === 'success') {
            $db->update_league($data);
        }
    } else if ($clicked === 'UPDATE_SEASON') {
        $season_id = (int) $_POST['season_id'];

        $season_year = $_POST['season_year'];
        if ($season_year === '') {
            $message = 'fail';
        } else if (!Sanitize::is_exactly($season_year, 7)) {
            $message = 'fail';
            $season_year = '';
        } else if (!Sanitize::is_year_formatted($season_year)) {
            $message = 'fail';
            $season_year = '';
        }

        $season_desc = Sanitize::strip_string($_POST['season_desc']);
        if ($season_desc === '') {
            $message = 'fail';
        } else if (!Sanitize::is_shorter($season_desc, 50)) {
            $message = 'fail';
            $season_desc = '';
        }

        $sport_id = '';
        $sport_name = '';
        Helper::set_sport_name(
            $db,
            $message,
            $sport_id,
            $sport_name,
            'sport_id'
        );

        $league_id = '';
        $league_name = '';
        Helper::set_league_name(
            $db,
            $message,
            $sport_id,
            $league_id,
            $league_name,
            'league_id'
        );

        $data = [
            'message' => $message,
            'season_id' => $season_id,
            "season_year_{$season_id}" => $season_year,
            "season_desc_{$season_id}" => $season_desc,
            "season_sport_id_{$season_id}" => $sport_id,
            'sport_name' => $sport_name,
            "season_league_id_{$season_id}" => $league_id,
            'league_name' => $league_name
        ];

        if ($message === 'success') {
            $db->update_season($data);
        }
    } else if ($clicked === 'UPDATE_TEAM') {
        $team_id = (int) $_POST['team_id'];

        $team_name = Sanitize::strip_string($_POST['team_name']);
        Sanitize::full_string_search($message, $team_name, 50);

        // Get sport for validation.
        $sport_id = (int) $_POST['sport_id'];

        $league_id = '';
        $league_name = '';
        Helper::set_league_name(
            $db,
            $message,
            $sport_id,
            $league_id,
            $league_name,
            'league_id'
        );

        $season_id = '';
        $season_year = '';
        Helper::set_season_year(
            $db,
            $message,
            $league_id,
            $season_id,
            $season_year,
            'season_id'
        );

        $max_players = (int) $_POST['team_max_players'];
        if ($max_players === '') {
            $message = 'fail';
        } else if ($max_players <= 0) {
            $message = '';
            $max_players = '';
        }

        $home_color = Sanitize::strip_string($_POST['team_home_color']);
        Sanitize::full_color_search($message, $home_color, 25);

        $away_color = Sanitize::strip_string($_POST['team_away_color']);
        Sanitize::full_color_search($message, $away_color, 25);

        $data = [
            'message' => $message,
            'team_id' => $team_id,
            "team_name_{$team_id}" => $team_name,
            "team_league_id_{$team_id}" => $league_id,
            "team_league_name_{$team_id}" => $league_name,
            "team_season_id_{$team_id}" => $season_id,
            "team_season_year_{$team_id}" => $season_year,
            "team_max_players_{$team_id}" => $max_players,
            "team_home_color_{$team_id}" => $home_color,
            "team_away_color_{$team_id}" => $away_color,
        ];

        if ($message === 'success') {
            $db->update_team($data);
        }
    } else if ($clicked === 'UPDATE_POSITION') {
        $position_id = (int) $_POST['position_id'];

        $position_name = Sanitize::strip_string($_POST['position_name']);
        Sanitize::full_string_search($message, $position_name, 50);

        $sport_id = '';
        $sport_name = '';
        Helper::set_sport_name(
            $db,
            $message,
            $sport_id,
            $sport_name,
            'sport_id'
        );

        $data = [
            'message' => $message,
            'position_id' => $position_id,
            "position_name_{$position_id}" => $position_name,
            "position_sport_id_{$position_id}" => $sport_id,
            "position_sport_name_{$position_id}" => $sport_name
        ];

        if ($message === 'success') {
            $db->update_position($data);
        }
    }

    // DELETE USERS, SPORTS, LEAGUES, SEASONS, TEAMS
    if ($clicked === 'DELETE_USER') {
        $db->delete_user_by_id($_POST['user_id']);
    } else if ($clicked === 'DELETE_SPORT') {
        $db->delete_sport_by_id($_POST['sport_id']);
    } else if ($clicked === 'DELETE_LEAGUE') {
        $db->delete_league_by_id($_POST['league_id']);
    } else if ($clicked === 'DELETE_SEASON') {
        $db->delete_season_by_id($_POST['season_id']);
    } else if ($clicked === 'DELETE_TEAM') {
        $db->delete_team_by_id($_POST['team_id']);
    } else if ($clicked === 'DELETE_POSITION') {
        $db->delete_position_by_id($_POST['position_id']);
    }

    echo json_encode($data);
}
