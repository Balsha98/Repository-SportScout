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
    $table = $itemType !== 'schedule' ? "{$itemType}s" : $itemType;
    ['id' => $lastRowID] = $db->getLastRowId($table, $itemType);

    if ($itemType === 'user') {
        $username = Sanitize::stripString($input['new_username']);
        Sanitize::fullStringSearch($status, $username, 25);

        $password = Sanitize::stripString($input['new_password']);
        if ($password === '') {
            $status = 'fail';
        } else if (!Sanitize::isShorter($password, 64)) {
            $status = 'fail';
            $password = '';
        }

        $roleID = '';
        $roleName = '';
        Helper::setRoleIdAndName(
            $status,
            $roleID,
            $roleName,
            'new_role_name'
        );

        $leagueID = '';
        $leagueName = '';
        $teamID = '';
        $teamName = '';
        Helper::setLeagueAndTeamNames(
            $db,
            $status,
            $leagueID,
            $leagueName,
            $teamID,
            $teamName,
            'new_user_league_id',
            'new_user_team_id'
        );

        $return = [
            'last_user_id' => $lastRowID,
            'status' => $status,
            'new_username' => $username,
            'new_password' => $password,
            'new_role_id' => $roleID,
            'new_role_name' => $roleName,
            'new_user_league_id' => $leagueID,
            'league_name' => $leagueName,
            'new_user_team_id' => $teamID,
            'team_name' => $teamName
        ];

        if ($status === 'success') {
            $db->alterAutoIncrement($table, $lastRowID);
            $db->insertNewUser($return);
        }
    } else if ($itemType === 'sport') {
        $sportName = Sanitize::stripString($input['new_sport_name']);
        Sanitize::fullStringSearch($status, $sportName, 50);

        $return = [
            'status' => $status,
            'last_sport_id' => $lastRowID,
            'new_sport_name' => $sportName
        ];

        if ($status === 'success') {
            $db->alterAutoIncrement($table, $lastRowID);
            $db->insertNewSport($return);
        }
    } else if ($itemType === 'league') {
        $leagueName = Sanitize::stripString($input['new_league_name']);
        Sanitize::fullStringSearch($status, $leagueName, 50);

        $sportID = '';
        $sportName = '';
        Helper::setSportName(
            $db,
            $status,
            $sportID,
            $sportName,
            'new_league_sport_id'
        );

        $return = [
            'status' => $status,
            'last_league_id' => $lastRowID,
            'new_league_name' => $leagueName,
            'new_league_sport_id' => $sportID,
            'league_sport_name' => $sportName
        ];

        if ($status === 'success') {
            $db->alterAutoIncrement($table, $lastRowID);
            $db->insertNewLeague($return);
        }
    } else if ($itemType === 'season') {
        $seasonYear = $input['new_season_year'];
        if ($seasonYear === '') {
            $status = 'fail';
        } else if (!Sanitize::isExactly($seasonYear, 7)) {
            $status = 'fail';
            $seasonYear = '';
        } else if (!Sanitize::isYearFormatted($seasonYear)) {
            $status = 'fail';
            $seasonYear = '';
        }

        $sportName = '';
        $sportID = '';
        Helper::setSportName(
            $db,
            $status,
            $sportID,
            $sportName,
            'new_season_sport_id'
        );

        $leagueName = '';
        $leagueID = '';
        Helper::setLeagueName(
            $db,
            $status,
            $sportID,
            $leagueID,
            $leagueName,
            'new_season_league_id'
        );

        $seasonDesc = Sanitize::stripString($input['new_season_desc']);
        if ($seasonDesc === '') {
            $status = 'fail';
        } else if (!Sanitize::isShorter($seasonDesc, 50)) {
            $status = 'fail';
            $seasonDesc = '';
        }

        $return = [
            'status' => $status,
            'last_season_id' => $lastRowID,
            'new_season_year' => $seasonYear,
            'new_season_sport_id' => $sportID,
            'season_sport_name' => $sportName,
            'new_season_league_id' => $leagueID,
            'season_league_name' => $leagueName,
            'new_season_desc' => $seasonDesc,
        ];

        if ($status === 'success') {
            $db->alterAutoIncrement($table, $lastRowID);
            $db->insertNewSeason($return);
        }
    } else if ($itemType === 'team') {
        $teamName = Sanitize::stripString($input['new_team_name']);
        Sanitize::fullStringSearch($status, $teamName, 50);

        $sportID = '';
        $sportName = '';
        Helper::setSportName(
            $db,
            $status,
            $sportID,
            $sportName,
            'new_team_sport_id'
        );

        $leagueID = '';
        $leagueName = '';
        Helper::setLeagueName(
            $db,
            $status,
            $sportID,
            $leagueID,
            $leagueName,
            'new_team_league_id'
        );

        $seasonID = '';
        $seasonYear = '';
        Helper::setSeasonYear(
            $db,
            $status,
            $leagueID,
            $seasonID,
            $seasonYear,
            'new_team_season_id'
        );

        $maxPlayers = (int) $input['new_team_max_players'];
        if ($maxPlayers === '') {
            $status = 'fail';
        } else if ($maxPlayers <= 0) {
            $status = 'fail';
            $maxPlayers = '';
        }

        $homeColor = Sanitize::stripString($input['new_team_home_color']);
        Sanitize::fullColorSearch($status, $homeColor, 25);

        $awayColor = Sanitize::stripString($input['new_team_away_color']);
        Sanitize::fullColorSearch($status, $awayColor, 25);

        $return = [
            'status' => $status,
            'last_team_id' => $lastRowID,
            'new_team_name' => $teamName,
            'new_team_sport_id' => $sportID,
            'team_sport_name' => $sportName,
            'new_team_league_id' => $leagueID,
            'team_league_name' => $leagueName,
            'new_team_season_id' => $seasonID,
            'team_season_year' => $seasonYear,
            'new_team_max_players' => $maxPlayers,
            'new_team_home_color' => $homeColor,
            'new_team_away_color' => $awayColor
        ];

        if ($status === 'success') {
            $db->alterAutoIncrement($table, $lastRowID);
            $db->insertNewTeam($return);
        }
    } else if ($itemType === 'position') {
        $positionName = Sanitize::stripString($input['new_position_name']);
        Sanitize::fullStringSearch($status, $positionName, 50);

        $sportID = '';
        $sportName = '';
        Helper::setSportName(
            $db,
            $status,
            $sportID,
            $sportName,
            'new_position_sport_id'
        );

        $return = [
            'status' => $status,
            'last_position_id' => $lastRowID,
            'new_position_name' => $positionName,
            'new_position_sport_id' => $sportID,
            'position_sport_name' => $sportName
        ];

        if ($status === 'success') {
            $db->alterAutoIncrement($table, $lastRowID);
            $db->insertNewPosition($return);
        }
    }

    echo Encoder::toJSON($return);
} else if ($request === 'PUT') {  // Update existing items.
    if ($itemType === 'user') {
        $userID = (int) $input['user_id'];

        $username = Sanitize::stripString($input['username']);
        Sanitize::fullStringSearch($status, $username, 25);

        $roleID = '';
        $roleName = '';
        Helper::setRoleIdAndName(
            $status,
            $roleID,
            $roleName,
            'role_name'
        );

        $leagueID = '';
        $leagueName = '';
        $teamID = '';
        $teamName = '';
        Helper::setLeagueAndTeamNames(
            $db,
            $status,
            $leagueID,
            $leagueName,
            $teamID,
            $teamName,
            'league_id',
            'team_id'
        );

        $return = [
            'status' => $status,
            'user_id' => $userID,
            "username_{$userID}" => $username,
            'role_id' => $roleID,
            "user_role_name_{$userID}" => $roleName,
            "user_league_id_{$userID}" => $leagueID,
            "user_league_name_{$userID}" => $leagueName,
            "user_team_id_{$userID}" => $teamID,
            "user_team_name_{$userID}" => $teamName,
        ];

        if ($status === 'success') {
            $db->updateAdminUser($return);
        }
    } else if ($itemType === 'UPDATE_SPORT') {
        $sportID = (int) $input['sport_id'];

        $sportName = Sanitize::stripString($input['sport_name']);
        Sanitize::fullStringSearch($status, $sportName, 50);

        $return = [
            'status' => $status,
            'sport_id' => $sportID,
            "sport_name_{$sportID}" => $sportName
        ];

        if ($status === 'success') {
            $db->updateSport($return);
        }
    } else if ($itemType === 'UPDATE_LEAGUE') {
        $leagueID = (int) $input['league_id'];

        $leagueName = Sanitize::stripString($input['league_name']);
        Sanitize::fullStringSearch($status, $leagueName, 50);

        $sportID = '';
        $sportName = '';
        Helper::setSportName(
            $db,
            $status,
            $sportID,
            $sportName,
            'sport_id'
        );

        $return = [
            'status' => $status,
            'league_id' => $leagueID,
            "league_name_{$leagueID}" => $leagueName,
            "league_sport_id_{$leagueID}" => $sportID,
            'sport_name' => $sportName
        ];

        if ($status === 'success') {
            $db->updateLeague($return);
        }
    } else if ($itemType === 'UPDATE_SEASON') {
        $seasonID = (int) $input['season_id'];

        $seasonYear = $input['season_year'];
        if ($seasonYear === '') {
            $status = 'fail';
        } else if (!Sanitize::isExactly($seasonYear, 7)) {
            $status = 'fail';
            $seasonYear = '';
        } else if (!Sanitize::isYearFormatted($seasonYear)) {
            $status = 'fail';
            $seasonYear = '';
        }

        $seasonDesc = Sanitize::stripString($input['season_desc']);
        if ($seasonDesc === '') {
            $status = 'fail';
        } else if (!Sanitize::isShorter($seasonDesc, 50)) {
            $status = 'fail';
            $seasonDesc = '';
        }

        $sportID = '';
        $sportName = '';
        Helper::setSportName(
            $db,
            $status,
            $sportID,
            $sportName,
            'sport_id'
        );

        $leagueID = '';
        $leagueName = '';
        Helper::setLeagueName(
            $db,
            $status,
            $sportID,
            $leagueID,
            $leagueName,
            'league_id'
        );

        $return = [
            'status' => $status,
            'season_id' => $seasonID,
            "season_year_{$seasonID}" => $seasonYear,
            "season_desc_{$seasonID}" => $seasonDesc,
            "season_sport_id_{$seasonID}" => $sportID,
            'sport_name' => $sportName,
            "season_league_id_{$seasonID}" => $leagueID,
            'league_name' => $leagueName
        ];

        if ($status === 'success') {
            $db->updateSeason($return);
        }
    } else if ($itemType === 'UPDATE_TEAM') {
        $teamID = (int) $input['team_id'];

        $teamName = Sanitize::stripString($input['team_name']);
        Sanitize::fullStringSearch($status, $teamName, 50);

        // Get sport for validation.
        $sportID = (int) $input['sport_id'];

        $leagueID = '';
        $leagueName = '';
        Helper::setLeagueName(
            $db,
            $status,
            $sportID,
            $leagueID,
            $leagueName,
            'league_id'
        );

        $seasonID = '';
        $seasonYear = '';
        Helper::setSeasonYear(
            $db,
            $status,
            $leagueID,
            $seasonID,
            $seasonYear,
            'season_id'
        );

        $maxPlayers = (int) $input['team_max_players'];
        if ($maxPlayers === '') {
            $status = 'fail';
        } else if ($maxPlayers <= 0) {
            $status = '';
            $maxPlayers = '';
        }

        $homeColor = Sanitize::stripString($input['team_home_color']);
        Sanitize::fullColorSearch($status, $homeColor, 25);

        $awayColor = Sanitize::stripString($input['team_away_color']);
        Sanitize::fullColorSearch($status, $awayColor, 25);

        $return = [
            'status' => $status,
            'team_id' => $teamID,
            "team_name_{$teamID}" => $teamName,
            "team_league_id_{$teamID}" => $leagueID,
            "team_league_name_{$teamID}" => $leagueName,
            "team_season_id_{$teamID}" => $seasonID,
            "team_season_year_{$teamID}" => $seasonYear,
            "team_max_players_{$teamID}" => $maxPlayers,
            "team_home_color_{$teamID}" => $homeColor,
            "team_away_color_{$teamID}" => $awayColor,
        ];

        if ($status === 'success') {
            $db->updateTeam($return);
        }
    } else if ($itemType === 'UPDATE_POSITION') {
        $position_id = (int) $input['position_id'];

        $positionName = Sanitize::stripString($input['position_name']);
        Sanitize::fullStringSearch($status, $positionName, 50);

        $sportID = '';
        $sportName = '';
        Helper::setSportName(
            $db,
            $status,
            $sportID,
            $sportName,
            'sport_id'
        );

        $return = [
            'status' => $status,
            'position_id' => $position_id,
            "position_name_{$position_id}" => $positionName,
            "position_sport_id_{$position_id}" => $sportID,
            "position_sport_name_{$position_id}" => $sportName
        ];

        if ($status === 'success') {
            $db->updatePosition($return);
        }
    }

    echo Encoder::toJSON($return);
} else if ($request === 'DELETE') {  // Delete existing items.
    $table = $itemType !== 'schedule' ? "{$itemType}s" : $itemType;
    $db->deleteRowById($table, $itemType, $itemID);
}
