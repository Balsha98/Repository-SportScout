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
    $table = $itemType === 'schedule' ? "{$itemType}s" : $itemType;
    $lastRowID = $db->getLastRowId($table, $itemType);

    if ($itemType === 'player') {
        $sportID = $input['sport_id'];
        $leagueName = $input['league_name'];
        $teamID = $input['team_id'];

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
            'new_position_id'
        );

        $jerseyNumber = (int) $input['new_jersey_number'];
        if ($jerseyNumber === '') {
            $status = 'fail';
        } else if ($jerseyNumber < 0) {
            $status = 'fail';
            $jerseyNumber = '';
        }

        $data = [
            'status' => $status,
            'last_player_id' => $lastRowID,
            'team_id' => $teamID,
            'league_name' => $leagueName,
            'new_player_first' => $playerFirst,
            'new_player_last' => $playerLast,
            'new_player_dob' => $playerDOB,
            'new_position_id' => $positionID,
            'new_position_name' => $positionName,
            'new_jersey_number' => $jerseyNumber,
        ];

        if ($status === 'success') {
            $db->alterAutoIncrement('players', $lastRowID);
            $db->insertNewPlayer($data);
        }
    } else if ($itemType === 'staff') {
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
        $role = explode('|', $input['new_role_name']);
        if (count($role) !== 2) {
            $status = 'fail';
        } else {
            $roleID = $role[0];
            $roleName = $role[1];
        }

        $leagueID = (int) $input['league_id'];
        $leagueName = $input['league_name'];
        $teamID = (int) $input['team_id'];

        $data = [
            'status' => $status,
            'last_user_id' => $lastRowID,
            'new_role_id' => $roleID,
            'new_role_name' => $roleName,
            'new_username' => $username,
            'new_password' => $password,
            'league_id' => $leagueID,
            'league_name' => $leagueName,
            'team_id' => $teamID
        ];

        if ($status === 'success') {
            $db->alterAutoIncrement('users', $lastRowID);
            $db->insertNewUser($data);
        }
    }

    echo Encoder::toJSON($return);
} else if ($request === 'PUT') {  // Update existing item.
    if ($itemType === 'player') {
        $sportID = (int) $input['sport_id'];

        $playerFirst = Sanitize::stripString($input['player_first']);
        Sanitize::fullStringSearch($status, $playerFirst, 50);

        $playerLast = Sanitize::stripString($input['player_last']);
        Sanitize::fullStringSearch($status, $playerLast, 50);

        $playerDOB = $input['player_dob'];
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
            'player_position_id'
        );

        $player_jersey = (int) $input['player_jersey'];
        if ($player_jersey === '') {
            $status = 'fail';
        } else if ($player_jersey < 0) {
            $status = 'fail';
            $player_jersey = '';
        }

        $data = [
            'status' => $status,
            'player_id' => $itemID,
            "player_first_{$itemID}" => $playerFirst,
            "player_last_{$itemID}" => $playerLast,
            "player_dob_{$itemID}" => $playerDOB,
            "player_position_id_{$itemID}" => $positionID,
            'player_position_name' => $positionName,
            "player_jersey_{$itemID}" => $player_jersey,
        ];

        if ($status === 'success') {
            $db->updateTeamPlayer($data);
        }
    } else if ($itemType === 'staff') {
        $username = Sanitize::stripString($input['username']);
        Sanitize::fullStringSearch($status, $username, 50);

        $roleID = '';
        $roleName = '';
        $role = explode('|', $input['role_name']);
        if (count($role) !== 2) {
            $status = 'fail';
        } else {
            $roleID = $role[0];
            $roleName = $role[1];
        }

        $data = [
            'status' => $status,
            'staff_id' => $itemID,
            "staff_username_{$itemID}" => $username,
            'staff_role_id' => $roleID,
            "staff_role_name_{$itemID}" => $roleName
        ];

        if ($status === 'success') {
            $db->updateTeamStaff($data);
        }
    }

    echo Encoder::toJSON($return);
} else if ($request === 'DELETE') {  // Delete existing item.
    $table = $itemType !== 'schedule' ? "{$itemType}s" : $itemType;
    $db->deleteRowById($table, $itemType, $itemID);
}
