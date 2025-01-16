<?php declare(strict_types=1);

require_once '../assets/class/Database.php';
require_once '../assets/class/Encoder.php';
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

// Create new item.
if ($request === 'POST') {
    $table = $itemType !== 'schedule' ? "{$itemType}s" : $itemType;
    ['id' => $lastRowID] = $db->getLastRowId($table, $itemType);

    if ($itemType === 'player') {
        $sportID = $input['new_player_sport_id'];
        $leagueName = $input['new_player_league_name'];
        $teamID = $input['new_player_team_id'];

        $playerFirst = Sanitize::stripString($input['new_player_first']);
        Sanitize::fullStringSearch($status, $playerFirst, 50);

        $playerLast = Sanitize::stripString($input['new_player_last']);
        Sanitize::fullStringSearch($status, $playerLast, 50);

        $playerDOB = $input['new_player_dob'];
        if ($playerDOB === '') {
            $status = 'fail';
        }

        $positionID = '';
        $positionName = '';
        Helper::setPositionName(
            $db,
            $input,
            $status,
            $sportID,
            $positionID,
            $positionName,
            'new_player_position_id'
        );

        $jerseyNumber = (int) $input['new_player_jersey_number'];
        if ($jerseyNumber === '') {
            $status = 'fail';
        } else if ($jerseyNumber <= 0) {
            $status = 'fail';
            $jerseyNumber = '';
        }

        $return = [
            'status' => $status,
            'last_player_id' => $lastRowID,
            'team_id' => $teamID,
            'league_name' => $leagueName,
            'new_player_first' => $playerFirst,
            'new_player_last' => $playerLast,
            'new_player_dob' => $playerDOB,
            'new_player_position_id' => $positionID,
            'new_player_position_name' => $positionName,
            'new_player_jersey_number' => $jerseyNumber
        ];

        if ($status === 'success') {
            $db->alterAutoIncrement($table, $lastRowID);
            $db->insertNewPlayer($return);
        }
    } else if ($itemType === 'user') {
        $username = Sanitize::stripString($input['new_username']);
        Sanitize::fullStringSearch($status, $username, 25);

        $password = Sanitize::stripString($input['new_user_password']);
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
            'new_user_role_name',
        );

        $leagueID = (int) $input['new_user_league_id'];
        $leagueName = $input['new_user_league_name'];
        $teamID = (int) $input['new_user_team_id'];

        $return = [
            'status' => $status,
            'last_user_id' => $lastRowID,
            'new_user_role_id' => $roleID,
            'new_user_role_name' => $roleName,
            'new_username' => $username,
            'new_user_password' => $password,
            'new_user_league_id' => $leagueID,
            'new_user_league_name' => $leagueName,
            'new_user_team_id' => $teamID
        ];

        if ($status === 'success') {
            $db->alterAutoIncrement($table, $lastRowID);
            $db->insertNewUser($return);
        }
    }

    echo Encoder::toJSON($return);
} else if ($request === 'PUT') {  // Update existing item.
    if ($itemType === 'player') {
        $sportID = (int) $input["player_sport_id_{$itemID}"];

        $playerFirst = Sanitize::stripString($input["player_first_{$itemID}"]);
        Sanitize::fullStringSearch($status, $playerFirst, 50);

        $playerLast = Sanitize::stripString($input["player_last_{$itemID}"]);
        Sanitize::fullStringSearch($status, $playerLast, 50);

        $playerDOB = $input["player_dob_{$itemID}"];
        if ($playerDOB === '') {
            $status = 'fail';
        }

        $positionID = '';
        $positionName = '';
        Helper::setPositionName(
            $db,
            $input,
            $status,
            $sportID,
            $positionID,
            $positionName,
            "player_position_id_{$itemID}"
        );

        $player_jersey = (int) $input["player_jersey_number_{$itemID}"];
        if ($player_jersey === '') {
            $status = 'fail';
        } else if ($player_jersey < 0) {
            $status = 'fail';
            $player_jersey = '';
        }

        $return = [
            'status' => $status,
            'player_id' => $itemID,
            "player_first_{$itemID}" => $playerFirst,
            "player_last_{$itemID}" => $playerLast,
            "player_dob_{$itemID}" => $playerDOB,
            "player_position_id_{$itemID}" => $positionID,
            'player_position_name' => $positionName,
            "player_jersey_number_{$itemID}" => $player_jersey,
        ];

        if ($status === 'success') {
            $db->updateTeamPlayer($return);
        }
    } else if ($itemType === 'user') {
        $username = Sanitize::stripString($input["staff_username_{$itemID}"]);
        Sanitize::fullStringSearch($status, $username, 50);

        $roleID = '';
        $roleName = '';
        Helper::setRoleIdAndName(
            $input,
            $status,
            $roleID,
            $roleName,
            "staff_role_name_{$itemID}",
        );

        $return = [
            'status' => $status,
            'staff_id' => $itemID,
            "staff_username_{$itemID}" => $username,
            'staff_role_id' => $roleID,
            "staff_role_name_{$itemID}" => $roleName
        ];

        if ($status === 'success') {
            $db->updateTeamStaff($return);
        }
    }

    echo Encoder::toJSON($return);
} else if ($request === 'DELETE') {  // Delete existing item.
    $table = $itemType !== 'schedule' ? "{$itemType}s" : $itemType;
    $db->deleteRowById($table, $itemType, $itemID);
}
