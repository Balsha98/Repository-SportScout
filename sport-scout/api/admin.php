<?php declare(strict_types=1);

require_once '../assets/class/Encoder.php';
require_once '../assets/class/Database.php';
require_once '../assets/class/Sanitize.php';
require_once '../assets/class/Helper.php';

$request = $_SERVER['REQUEST_METHOD'];
$input = Encoder::fromJSON(file_get_contents('php://input'));
$itemType = $input['item_type'];
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
            $input,
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
            $input,
            $status,
            $leagueID,
            $leagueName,
            $teamID,
            $teamName,
            'new_user_league_id',
            'new_user_team_id'
        );

        $return = [
            'status' => $status,
            'last_user_id' => $lastRowID,
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
            $input,
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
            $input,
            $status,
            $sportID,
            $sportName,
            'new_season_sport_id'
        );

        $leagueName = '';
        $leagueID = '';
        Helper::setLeagueName(
            $db,
            $input,
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
            $input,
            $status,
            $sportID,
            $sportName,
            'new_team_sport_id'
        );

        $leagueID = '';
        $leagueName = '';
        Helper::setLeagueName(
            $db,
            $input,
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
            $input,
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
            $input,
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
    $itemID = (int) $input['item_id'];

    if ($itemType === 'user') {
        $username = Sanitize::stripString($input["username_{$itemID}"]);
        Sanitize::fullStringSearch($status, $username, 25);

        $roleID = '';
        $roleName = '';
        Helper::setRoleIdAndName(
            $input,
            $status,
            $roleID,
            $roleName,
            "user_role_name_{$itemID}"
        );

        $leagueID = '';
        $leagueName = '';
        $teamID = '';
        $teamName = '';
        Helper::setLeagueAndTeamNames(
            $db,
            $input,
            $status,
            $leagueID,
            $leagueName,
            $teamID,
            $teamName,
            "user_league_id_{$itemID}",
            "user_team_id_{$itemID}"
        );

        $return = [
            'status' => $status,
            'user_id' => $itemID,
            'username' => $username,
            'role_id' => $roleID,
            'user_role_name' => $roleName,
            'user_league_id' => $leagueID,
            'user_league_name' => $leagueName,
            'user_team_id' => $teamID,
            'user_team_name' => $teamName,
        ];

        if ($status === 'success') {
            $db->updateAdminUser($return);
        }
    } else if ($itemType === 'sport') {
        $sportName = Sanitize::stripString($input["sport_name_{$itemID}"]);
        Sanitize::fullStringSearch($status, $sportName, 50);

        $return = [
            'status' => $status,
            'sport_id' => $itemID,
            'sport_name' => $sportName
        ];

        if ($status === 'success') {
            $db->updateSport($return);
        }
    } else if ($itemType === 'league') {
        $leagueName = Sanitize::stripString($input["league_name_{$itemID}"]);
        Sanitize::fullStringSearch($status, $leagueName, 50);

        $sportID = '';
        $sportName = '';
        Helper::setSportName(
            $db,
            $input,
            $status,
            $sportID,
            $sportName,
            "league_sport_id_{$itemID}"
        );

        $return = [
            'status' => $status,
            'league_id' => $itemID,
            'league_name' => $leagueName,
            'league_sport_id' => $sportID,
            'league_sport_name' => $sportName
        ];

        if ($status === 'success') {
            $db->updateLeague($return);
        }
    } else if ($itemType === 'season') {
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

        $seasonDesc = Sanitize::stripString($input["season_desc_{$itemID}"]);
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
            $input,
            $status,
            $sportID,
            $sportName,
            "season_sport_id_{$itemID}"
        );

        $leagueID = '';
        $leagueName = '';
        Helper::setLeagueName(
            $db,
            $input,
            $status,
            $sportID,
            $leagueID,
            $leagueName,
            "season_league_id_{$itemID}"
        );

        $return = [
            'status' => $status,
            'season_id' => $itemID,
            'season_year' => $seasonYear,
            'season_desc' => $seasonDesc,
            'season_sport_id' => $sportID,
            'season_sport_name' => $sportName,
            'season_league_id' => $leagueID,
            'season_league_name' => $leagueName
        ];

        if ($status === 'success') {
            $db->updateSeason($return);
        }
    } else if ($itemType === 'team') {
        $teamName = Sanitize::stripString($input["team_name_{$itemID}"]);
        Sanitize::fullStringSearch($status, $teamName, 50);

        // Get sport for validation.
        $sportID = (int) $input["team_sport_id_{$itemID}"];

        $leagueID = '';
        $leagueName = '';
        Helper::setLeagueName(
            $db,
            $input,
            $status,
            $sportID,
            $leagueID,
            $leagueName,
            "team_league_id_{$itemID}"
        );

        $seasonID = '';
        $seasonYear = '';
        Helper::setSeasonYear(
            $db,
            $input,
            $status,
            $leagueID,
            $seasonID,
            $seasonYear,
            "team_season_id_{$itemID}"
        );

        $maxPlayers = (int) $input["team_max_players_{$itemID}"];
        if ($maxPlayers === '') {
            $status = 'fail';
        } else if ($maxPlayers <= 0) {
            $status = '';
            $maxPlayers = '';
        }

        $homeColor = Sanitize::stripString($input["team_home_color_{$itemID}"]);
        Sanitize::fullColorSearch($status, $homeColor, 25);

        $awayColor = Sanitize::stripString($input["team_away_color_{$itemID}"]);
        Sanitize::fullColorSearch($status, $awayColor, 25);

        $return = [
            'status' => $status,
            'team_id' => $itemID,
            'team_name' => $teamName,
            'team_league_id' => $leagueID,
            'team_league_name' => $leagueName,
            'team_season_id' => $seasonID,
            'team_season_year' => $seasonYear,
            'team_max_players' => $maxPlayers,
            'team_home_color' => $homeColor,
            'team_away_color' => $awayColor,
        ];

        if ($status === 'success') {
            $db->updateTeam($return);
        }
    } else if ($itemType === 'position') {
        $positionName = Sanitize::stripString($input["position_name_{$itemID}"]);
        Sanitize::fullStringSearch($status, $positionName, 50);

        $sportID = '';
        $sportName = '';
        Helper::setSportName(
            $db,
            $input,
            $status,
            $sportID,
            $sportName,
            "position_sport_id_{$itemID}"
        );

        $return = [
            'status' => $status,
            'position_id' => $position_id,
            'position_name' => $positionName,
            'position_sport_id' => $sportID,
            'position_sport_name' => $sportName
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
