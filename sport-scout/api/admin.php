<?php declare(strict_types=1);

require_once '../assets/class/Encoder.php';
require_once '../assets/class/Database.php';
require_once '../assets/class/Sanitize.php';
require_once '../assets/class/Helper.php';

$request = $_SERVER['REQUEST_METHOD'];
$input = Encoder::fromJSON(file_get_contents('php://input'));
$itemType = $input['item_type'];
if (array_key_exists('item_id', $input)) {
    $itemID = $input['item_id'];
}

$status = 'success';
$return = [];

// Create new items.
if ($request === 'POST') {
    if ($itemType === 'ADD_USER') {
        $last_user_id = $db->get_last_user_id()['user_id'];

        $username = Sanitize::stripString($_POST['new_username']);
        Sanitize::fullStringSearch($status, $username, 25);

        $password = Sanitize::stripString($_POST['new_password']);
        if ($password === '') {
            $status = 'fail';
        } else if (!Sanitize::isShorter($password, 64)) {
            $status = 'fail';
            $password = '';
        }

        $role_id = '';
        $role_name = '';
        Helper::setRoleIdAndName(
            $status,
            $role_id,
            $role_name,
            'new_role_name'
        );

        $league_id = '';
        $league_name = '';
        $team_id = '';
        $team_name = '';
        Helper::setLeagueAndTeamNames(
            $db,
            $status,
            $league_id,
            $league_name,
            $team_id,
            $team_name,
            'league_id',
            'team_id'
        );

        $return = [
            'status' => $status,
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

        if ($status === 'success') {
            $db->alter_auto_increment('users', $last_user_id);
            $db->insert_new_user($return);
        }
    } else if ($itemType === 'ADD_SPORT') {
        $last_sport_id = (int) $db->get_last_sport_id()['sport_id'];

        $sport_name = Sanitize::stripString($_POST['new_sport_name']);
        Sanitize::fullStringSearch($status, $sport_name, 50);

        $return = [
            'status' => $status,
            'last_sport_id' => $last_sport_id,
            'new_sport_name' => $sport_name
        ];

        if ($status === 'success') {
            $db->alter_auto_increment('sports', $last_sport_id);
            $db->insert_new_sport($return);
        }
    } else if ($itemType === 'ADD_LEAGUE') {
        $last_league_id = $db->get_last_league_id()['league_id'];

        $league_name = Sanitize::stripString($_POST['new_league_name']);
        Sanitize::fullStringSearch($status, $league_name, 50);

        $sport_id = '';
        $sport_name = '';
        Helper::setSportName(
            $db,
            $status,
            $sport_id,
            $sport_name,
            'sport_id'
        );

        $return = [
            'status' => $status,
            'last_league_id' => $last_league_id,
            'new_league_name' => $league_name,
            'league_sport_id' => $sport_id,
            'league_sport_name' => $sport_name
        ];

        if ($status === 'success') {
            $db->alter_auto_increment('leagues', $last_league_id);
            $db->insert_new_league($return);
        }
    } else if ($itemType === 'ADD_SEASON') {
        $last_season_id = $db->get_last_season_id()['season_id'];

        $season_year = $_POST['new_season_year'];
        if ($season_year === '') {
            $status = 'fail';
        } else if (!Sanitize::isExactly($season_year, 7)) {
            $status = 'fail';
            $season_year = '';
        } else if (!Sanitize::isYearFormatted($season_year)) {
            $status = 'fail';
            $season_year = '';
        }

        $sport_name = '';
        $sport_id = '';
        Helper::setSportName(
            $db,
            $status,
            $sport_id,
            $sport_name,
            'sport_id'
        );

        $league_name = '';
        $league_id = '';
        Helper::setLeagueName(
            $db,
            $status,
            $sport_id,
            $league_id,
            $league_name,
            'league_id'
        );

        $season_desc = Sanitize::stripString($_POST['new_season_desc']);
        if ($season_desc === '') {
            $status = 'fail';
        } else if (!Sanitize::isShorter($season_desc, 50)) {
            $status = 'fail';
            $season_desc = '';
        }

        $return = [
            'status' => $status,
            'last_season_id' => $last_season_id,
            'new_season_year' => $season_year,
            'new_season_sport_id' => $sport_id,
            'season_sport_name' => $sport_name,
            'new_season_league_id' => $league_id,
            'season_league_name' => $league_name,
            'new_season_desc' => $season_desc,
        ];

        if ($status === 'success') {
            $db->alter_auto_increment('seasons', $last_season_id);
            $db->insert_new_season($return);
        }
    } else if ($itemType === 'ADD_TEAM') {
        $last_team_id = $db->get_last_team_id()['team_id'];

        $team_name = Sanitize::stripString($_POST['new_team_name']);
        Sanitize::fullStringSearch($status, $team_name, 50);

        $sport_id = '';
        $sport_name = '';
        Helper::setSportName(
            $db,
            $status,
            $sport_id,
            $sport_name,
            'sport_id'
        );

        $league_id = '';
        $league_name = '';
        Helper::setLeagueName(
            $db,
            $status,
            $sport_id,
            $league_id,
            $league_name,
            'league_id'
        );

        $season_id = '';
        $season_year = '';
        Helper::setSeasonYear(
            $db,
            $status,
            $league_id,
            $season_id,
            $season_year,
            'season_id'
        );

        $max_players = (int) $_POST['new_team_max_players'];
        if ($max_players === '') {
            $status = 'fail';
        } else if ($max_players <= 0) {
            $status = 'fail';
            $max_players = '';
        }

        $home_color = Sanitize::stripString($_POST['new_team_home_color']);
        Sanitize::fullColorSearch($status, $home_color, 25);

        $away_color = Sanitize::stripString($_POST['new_team_away_color']);
        Sanitize::fullColorSearch($status, $away_color, 25);

        $return = [
            'status' => $status,
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

        if ($status === 'success') {
            $db->alter_auto_increment('teams', $last_team_id);
            $db->insert_new_team($return);
        }
    } else if ($itemType === 'ADD_POSITION') {
        $last_position_id = (int) $db->get_last_position_id()['position_id'];

        $position_name = Sanitize::stripString($_POST['new_position_name']);
        Sanitize::fullStringSearch($status, $position_name, 50);

        $sport_id = '';
        $sport_name = '';
        Helper::setSportName(
            $db,
            $status,
            $sport_id,
            $sport_name,
            'sport_id'
        );

        $return = [
            'status' => $status,
            'last_position_id' => $last_position_id,
            'new_position_name' => $position_name,
            'new_position_sport_id' => $sport_id,
            'new_position_sport_name' => $sport_name
        ];

        if ($status === 'success') {
            $db->alter_auto_increment('positions', $last_position_id);
            $db->insert_new_position($return);
        }
    }

    echo Encoder::toJSON($return);
} else if ($request === 'PUT') {  // Update existing items.
    if ($itemType === 'UPDATE_USER') {
        $user_id = (int) $_POST['user_id'];

        $username = Sanitize::stripString($_POST['username']);
        Sanitize::fullStringSearch($status, $username, 25);

        $role_id = '';
        $role_name = '';
        Helper::setRoleIdAndName(
            $status,
            $role_id,
            $role_name,
            'role_name'
        );

        $league_id = '';
        $league_name = '';
        $team_id = '';
        $team_name = '';
        Helper::setLeagueAndTeamNames(
            $db,
            $status,
            $league_id,
            $league_name,
            $team_id,
            $team_name,
            'league_id',
            'team_id'
        );

        $return = [
            'status' => $status,
            'user_id' => $user_id,
            "username_{$user_id}" => $username,
            'role_id' => $role_id,
            "user_role_name_{$user_id}" => $role_name,
            "user_league_id_{$user_id}" => $league_id,
            "user_league_name_{$user_id}" => $league_name,
            "user_team_id_{$user_id}" => $team_id,
            "user_team_name_{$user_id}" => $team_name,
        ];

        if ($status === 'success') {
            $db->update_admin_user($return);
        }
    } else if ($itemType === 'UPDATE_SPORT') {
        $sport_id = (int) $_POST['sport_id'];

        $sport_name = Sanitize::stripString($_POST['sport_name']);
        Sanitize::fullStringSearch($status, $sport_name, 50);

        $return = [
            'status' => $status,
            'sport_id' => $sport_id,
            "sport_name_{$sport_id}" => $sport_name
        ];

        if ($status === 'success') {
            $db->update_sport($return);
        }
    } else if ($itemType === 'UPDATE_LEAGUE') {
        $league_id = (int) $_POST['league_id'];

        $league_name = Sanitize::stripString($_POST['league_name']);
        Sanitize::fullStringSearch($status, $league_name, 50);

        $sport_id = '';
        $sport_name = '';
        Helper::setSportName(
            $db,
            $status,
            $sport_id,
            $sport_name,
            'sport_id'
        );

        $return = [
            'status' => $status,
            'league_id' => $league_id,
            "league_name_{$league_id}" => $league_name,
            "league_sport_id_{$league_id}" => $sport_id,
            'sport_name' => $sport_name
        ];

        if ($status === 'success') {
            $db->update_league($return);
        }
    } else if ($itemType === 'UPDATE_SEASON') {
        $season_id = (int) $_POST['season_id'];

        $season_year = $_POST['season_year'];
        if ($season_year === '') {
            $status = 'fail';
        } else if (!Sanitize::isExactly($season_year, 7)) {
            $status = 'fail';
            $season_year = '';
        } else if (!Sanitize::isYearFormatted($season_year)) {
            $status = 'fail';
            $season_year = '';
        }

        $season_desc = Sanitize::stripString($_POST['season_desc']);
        if ($season_desc === '') {
            $status = 'fail';
        } else if (!Sanitize::isShorter($season_desc, 50)) {
            $status = 'fail';
            $season_desc = '';
        }

        $sport_id = '';
        $sport_name = '';
        Helper::setSportName(
            $db,
            $status,
            $sport_id,
            $sport_name,
            'sport_id'
        );

        $league_id = '';
        $league_name = '';
        Helper::setLeagueName(
            $db,
            $status,
            $sport_id,
            $league_id,
            $league_name,
            'league_id'
        );

        $return = [
            'status' => $status,
            'season_id' => $season_id,
            "season_year_{$season_id}" => $season_year,
            "season_desc_{$season_id}" => $season_desc,
            "season_sport_id_{$season_id}" => $sport_id,
            'sport_name' => $sport_name,
            "season_league_id_{$season_id}" => $league_id,
            'league_name' => $league_name
        ];

        if ($status === 'success') {
            $db->update_season($return);
        }
    } else if ($itemType === 'UPDATE_TEAM') {
        $team_id = (int) $_POST['team_id'];

        $team_name = Sanitize::stripString($_POST['team_name']);
        Sanitize::fullStringSearch($status, $team_name, 50);

        // Get sport for validation.
        $sport_id = (int) $_POST['sport_id'];

        $league_id = '';
        $league_name = '';
        Helper::setLeagueName(
            $db,
            $status,
            $sport_id,
            $league_id,
            $league_name,
            'league_id'
        );

        $season_id = '';
        $season_year = '';
        Helper::setSeasonYear(
            $db,
            $status,
            $league_id,
            $season_id,
            $season_year,
            'season_id'
        );

        $max_players = (int) $_POST['team_max_players'];
        if ($max_players === '') {
            $status = 'fail';
        } else if ($max_players <= 0) {
            $status = '';
            $max_players = '';
        }

        $home_color = Sanitize::stripString($_POST['team_home_color']);
        Sanitize::fullColorSearch($status, $home_color, 25);

        $away_color = Sanitize::stripString($_POST['team_away_color']);
        Sanitize::fullColorSearch($status, $away_color, 25);

        $return = [
            'status' => $status,
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

        if ($status === 'success') {
            $db->update_team($return);
        }
    } else if ($itemType === 'UPDATE_POSITION') {
        $position_id = (int) $_POST['position_id'];

        $position_name = Sanitize::stripString($_POST['position_name']);
        Sanitize::fullStringSearch($status, $position_name, 50);

        $sport_id = '';
        $sport_name = '';
        Helper::setSportName(
            $db,
            $status,
            $sport_id,
            $sport_name,
            'sport_id'
        );

        $return = [
            'status' => $status,
            'position_id' => $position_id,
            "position_name_{$position_id}" => $position_name,
            "position_sport_id_{$position_id}" => $sport_id,
            "position_sport_name_{$position_id}" => $sport_name
        ];

        if ($status === 'success') {
            $db->update_position($return);
        }
    }

    echo Encoder::toJSON($return);
} else if ($request === 'DELETE') {  // Delete existing items.
    $tableName = $itemType !== 'schedule' ? "{$itemType}s" : $itemType;
    $db->deleteRowById($tableName, $itemType, $itemID);
}
