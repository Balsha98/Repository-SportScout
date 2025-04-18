<?php declare(strict_types=1);

require_once '../assets/class/Encoder.php';
require_once '../assets/class/Database.php';
require_once '../assets/class/Sanitize.php';
require_once '../assets/class/Helper.php';

$request = $_SERVER['REQUEST_METHOD'];
$input = Encoder::fromJSON(file_get_contents('php://input'));
$itemType = $input['item_type'];
if (array_key_exists('item_id', $input)) {
    $itemID = (int) $input['item_id'];
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

        $password = Sanitize::stripString($input['new_user_password']);
        if ($password === '') {
            $status = 'fail';
        } else if (!Sanitize::isShorter($password, 64)) {
            $status = 'fail';
            $password = '';
        }

        $options = [];
        $inputKeys = ['new_user_role_name', 'new_user_league_id', 'new_user_team_id'];
        foreach ($inputKeys as $key) {
            $option = $input[$key];
            if ($option === '') {
                $status = 'fail';
                foreach (['', ''] as $value) {
                    $options[] = $value;
                }
            } else {
                $options[] = explode('|', $option)[0];
                $options[] = explode('|', $option)[1];
            }
        }

        $return = [
            'status' => $status,
            'last_user_id' => $lastRowID,
            'new_username' => $username,
            'new_user_password' => $password,
            'new_user_role_id' => $options[0],
            'new_user_role_name' => $options[1],
            'distinct_roles' => $db->getDistinctRows('role'),
            'new_user_league_id' => $options[2],
            'new_user_league_name' => $options[3],
            'distinct_leagues' => $db->getDistinctRows('league'),
            'new_user_team_id' => $options[4],
            'new_user_team_name' => $options[5],
            'distinct_teams' => $db->getDistinctRows('team'),
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
        $sportOption = $input['new_league_sport_id'];
        if ($sportOption === '') {
            $status = 'fail';
        } else {
            $sportID = explode('|', $sportOption)[0];
            $sportName = explode('|', $sportOption)[1];
        }

        $return = [
            'status' => $status,
            'last_league_id' => $lastRowID,
            'new_league_name' => $leagueName,
            'new_league_sport_id' => $sportID,
            'league_sport_name' => $sportName,
            'distinct_sports' => $db->getDistinctRows('sport')
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

        $seasonDesc = Sanitize::stripString($input['new_season_desc']);
        if ($seasonDesc === '') {
            $status = 'fail';
        } else if (!Sanitize::isShorter($seasonDesc, 50)) {
            $status = 'fail';
            $seasonDesc = '';
        }

        $options = [];
        $inputKeys = ['new_season_sport_id', 'new_season_league_id'];
        foreach ($inputKeys as $key) {
            $option = $input[$key];
            if ($option === '') {
                $status = 'fail';
                foreach (['', ''] as $value) {
                    $options[] = $value;
                }
            } else {
                $options[] = explode('|', $option)[0];
                $options[] = explode('|', $option)[1];
            }
        }

        $return = [
            'status' => $status,
            'last_season_id' => $lastRowID,
            'new_season_year' => $seasonYear,
            'new_season_sport_id' => $options[0],
            'season_sport_name' => $options[1],
            'distinct_sports' => $db->getDistinctRows('sport'),
            'new_season_league_id' => $options[2],
            'season_league_name' => $options[3],
            'distinct_leagues' => $db->getDistinctRows('league'),
            'new_season_desc' => $seasonDesc,
        ];

        if ($status === 'success') {
            $db->alterAutoIncrement($table, $lastRowID);
            $db->insertNewSeason($return);
        }
    } else if ($itemType === 'team') {
        $teamName = Sanitize::stripString($input['new_team_name']);
        Sanitize::fullStringSearch($status, $teamName, 50);

        $options = [];
        $inputKeys = ['new_team_sport_id', 'new_team_league_id', 'new_team_season_id'];
        foreach ($inputKeys as $key) {
            $option = $input[$key];
            if ($option === '') {
                $status = 'fail';
                foreach (['', ''] as $value) {
                    $options[] = $value;
                }
            } else {
                $options[] = explode('|', $option)[0];
                $options[] = explode('|', $option)[1];
            }
        }

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
            'new_team_sport_id' => $options[0],
            'team_sport_name' => $options[1],
            'new_team_league_id' => $options[2],
            'team_league_name' => $options[3],
            'distinct_leagues' => $db->getDistinctRows('league'),
            'new_team_season_id' => $options[4],
            'team_season_year' => $options[5],
            'distinct_seasons' => $db->getDistinctRows('season'),
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
        $sportOption = $input['new_position_sport_id'];
        if ($sportOption === '') {
            $status = 'fail';
        } else {
            $sportID = explode('|', $sportOption)[0];
            $sportName = explode('|', $sportOption)[1];
        }

        $return = [
            'status' => $status,
            'last_position_id' => $lastRowID,
            'new_position_name' => $positionName,
            'new_position_sport_id' => $sportID,
            'position_sport_name' => $sportName,
            'distinct_sports' => $db->getDistinctRows('sport')
        ];

        if ($status === 'success') {
            $db->alterAutoIncrement($table, $lastRowID);
            $db->insertNewPosition($return);
        }
    }

    echo Encoder::toJSON($return);
} else if ($request === 'PUT') {  // Update existing items.
    if ($itemType === 'user') {
        $username = Sanitize::stripString($input["username_{$itemID}"]);
        Sanitize::fullStringSearch($status, $username, 25);

        $options = [];
        $inputKeys = ["user_role_name_{$itemID}", "user_league_id_{$itemID}", "user_team_id_{$itemID}"];
        foreach ($inputKeys as $key) {
            $option = $input[$key];
            if ($option === '') {
                $status = 'fail';
                foreach (['', ''] as $value) {
                    $options[] = $value;
                }
            } else {
                $options[] = explode('|', $option)[0];
                $options[] = explode('|', $option)[1];
            }
        }

        $return = [
            'status' => $status,
            'user_id' => $itemID,
            "username_{$itemID}" => $username,
            'role_id' => $options[0],
            "user_role_name_{$itemID}" => $options[1],
            "user_league_id_{$itemID}" => $options[2],
            "user_league_name_{$itemID}" => $options[3],
            "user_team_id_{$itemID}" => $options[4],
            "user_team_name_{$itemID}" => $options[5],
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
            "sport_name_{$itemID}" => $sportName
        ];

        if ($status === 'success') {
            $db->updateSport($return);
        }
    } else if ($itemType === 'league') {
        $leagueName = Sanitize::stripString($input["league_name_{$itemID}"]);
        Sanitize::fullStringSearch($status, $leagueName, 50);

        $sportID = '';
        $sportName = '';
        $sportOption = $input["league_sport_id_{$itemID}"];
        if ($sportOption === '') {
            $status = 'fail';
        } else {
            $sportID = explode('|', $sportOption)[0];
            $sportName = explode('|', $sportOption)[1];
        }

        $return = [
            'status' => $status,
            'league_id' => $itemID,
            "league_name_{$itemID}" => $leagueName,
            "league_sport_id_{$itemID}" => $sportID,
            "league_sport_name_{$itemID}" => $sportName
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

        $options = [];
        $inputKeys = ["season_sport_id_{$itemID}", "season_league_id_{$itemID}"];
        foreach ($inputKeys as $key) {
            $option = $input[$key];
            if ($option === '') {
                $status = 'fail';
                foreach (['', ''] as $value) {
                    $options[] = $value;
                }
            } else {
                $options[] = explode('|', $option)[0];
                $options[] = explode('|', $option)[1];
            }
        }

        $return = [
            'status' => $status,
            'season_id' => $itemID,
            "season_year_{$itemID}" => $seasonYear,
            "season_desc_{$itemID}" => $seasonDesc,
            "season_sport_id_{$itemID}" => $options[0],
            "season_sport_name_{$itemID}" => $options[1],
            "season_league_id_{$itemID}" => $options[2],
            "season_league_name_{$itemID}" => $options[3]
        ];

        if ($status === 'success') {
            $db->updateSeason($return);
        }
    } else if ($itemType === 'team') {
        $teamName = Sanitize::stripString($input["team_name_{$itemID}"]);
        Sanitize::fullStringSearch($status, $teamName, 50);

        // Get sport for validation.
        $sportID = (int) $input["team_sport_id_{$itemID}"];

        $options = [];
        $inputKeys = ["team_league_id_{$itemID}", "team_season_id_{$itemID}"];
        foreach ($inputKeys as $key) {
            $option = $input[$key];
            if ($option === '') {
                $status = 'fail';
                foreach (['', ''] as $value) {
                    $options[] = $value;
                }
            } else {
                $options[] = explode('|', $option)[0];
                $options[] = explode('|', $option)[1];
            }
        }

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
            "team_name_{$itemID}" => $teamName,
            "team_league_id_{$itemID}" => $options[0],
            "team_league_name_{$itemID}" => $options[1],
            "team_season_id_{$itemID}" => $options[2],
            "team_season_year_{$itemID}" => $options[3],
            "team_max_players_{$itemID}" => $maxPlayers,
            "team_home_color_{$itemID}" => $homeColor,
            "team_away_color_{$itemID}" => $awayColor,
        ];

        if ($status === 'success') {
            $db->updateTeam($return);
        }
    } else if ($itemType === 'position') {
        $positionName = Sanitize::stripString($input["position_name_{$itemID}"]);
        Sanitize::fullStringSearch($status, $positionName, 50);

        $sportID = '';
        $sportName = '';
        $sportOption = $input["position_sport_id_{$itemID}"];
        if ($sportOption === '') {
            $status = 'fail';
        } else {
            $sportID = explode('|', $sportOption)[0];
            $sportName = explode('|', $sportOption)[1];
        }

        $return = [
            'status' => $status,
            'position_id' => $itemID,
            "position_name_{$itemID}" => $positionName,
            "position_sport_id_{$itemID}" => $sportID,
            "position_sport_name_{$itemID}" => $sportName
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
