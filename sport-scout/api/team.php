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
    if ($itemType === 'player') {
        $last_player_id = (int) $db->get_last_player_id()['player_id'];

        $sport_id = $_POST['sport_id'];
        $league_name = $_POST['league_name'];
        $team_id = $_POST['team_id'];

        $player_first = Sanitize::stripString($_POST['new_player_first']);
        Sanitize::fullStringSearch($message, $player_first, 50);

        $player_last = Sanitize::stripString($_POST['new_player_last']);
        Sanitize::fullStringSearch($message, $player_last, 50);

        $player_dob = $_POST['new_player_dob'];
        if ($player_dob === '') {
            $message = 'fail';
        }

        $position_id = '';
        $position_name = '';
        Helper::setPositionName(
            $db,
            $message,
            $sport_id,
            $position_id,
            $position_name,
            'new_position_id'
        );

        $jersey_number = (int) $_POST['new_jersey_number'];
        if ($jersey_number === '') {
            $message = 'fail';
        } else if ($jersey_number < 0) {
            $message = 'fail';
            $jersey_number = '';
        }

        $data = [
            'message' => $message,
            'last_player_id' => $last_player_id,
            'team_id' => $team_id,
            'league_name' => $league_name,
            'new_player_first' => $player_first,
            'new_player_last' => $player_last,
            'new_player_dob' => $player_dob,
            'new_position_id' => $position_id,
            'new_position_name' => $position_name,
            'new_jersey_number' => $jersey_number,
        ];

        if ($message === 'success') {
            $db->alter_auto_increment('players', $last_player_id);
            $db->insert_new_player($data);
        }
    } else if ($itemType === 'staff') {
        $last_user_id = (int) $db->get_last_user_id()['user_id'];

        $username = Sanitize::stripString($_POST['new_username']);
        Sanitize::fullStringSearch($message, $username, 25);

        $password = Sanitize::stripString($_POST['new_password']);
        if ($password === '') {
            $message = 'fail';
        } else if (!Sanitize::isShorter($password, 64)) {
            $message = 'fail';
            $password = '';
        }

        $role_id = '';
        $role_name = '';
        $role = explode('|', $_POST['new_role_name']);
        if (count($role) !== 2) {
            $message = 'fail';
        } else {
            $role_id = $role[0];
            $role_name = $role[1];
        }

        $league_id = (int) $_POST['league_id'];
        $league_name = $_POST['league_name'];
        $team_id = (int) $_POST['team_id'];

        $data = [
            'message' => $message,
            'last_user_id' => $last_user_id,
            'new_role_id' => $role_id,
            'new_role_name' => $role_name,
            'new_username' => $username,
            'new_password' => $password,
            'league_id' => $league_id,
            'league_name' => $league_name,
            'team_id' => $team_id
        ];

        if ($message === 'success') {
            $db->alter_auto_increment('users', $last_user_id);
            $db->insert_new_user($data);
        }
    }

    echo Encoder::toJSON($return);
} else if ($request === 'PUT') {  // Update existing item.
    if ($itemType === 'UPDATE_PLAYER') {
        $sport_id = (int) $_POST['sport_id'];
        $player_id = (int) $_POST['player_id'];

        $player_first = Sanitize::stripString($_POST['player_first']);
        Sanitize::fullStringSearch($message, $player_first, 50);

        $player_last = Sanitize::stripString($_POST['player_last']);
        Sanitize::fullStringSearch($message, $player_last, 50);

        $player_dob = $_POST['player_dob'];
        if ($player_dob === '') {
            $message = 'fail';
        }

        $position_id = '';
        $position_name = '';
        Helper::setPositionName(
            $db,
            $message,
            $sport_id,
            $position_id,
            $position_name,
            'player_position_id'
        );

        $player_jersey = (int) $_POST['player_jersey'];
        if ($player_jersey === '') {
            $message = 'fail';
        } else if ($player_jersey < 0) {
            $message = 'fail';
            $player_jersey = '';
        }

        $data = [
            'message' => $message,
            'player_id' => $player_id,
            "player_first_{$player_id}" => $player_first,
            "player_last_{$player_id}" => $player_last,
            "player_dob_{$player_id}" => $player_dob,
            "player_position_id_{$player_id}" => $position_id,
            'player_position_name' => $position_name,
            "player_jersey_{$player_id}" => $player_jersey,
        ];

        if ($message === 'success') {
            $db->update_team_player($data);
        }
    } else if ($itemType === 'UPDATE_STAFF') {
        $staff_id = (int) $_POST['staff_id'];

        $username = Sanitize::stripString($_POST['username']);
        Sanitize::fullStringSearch($message, $username, 50);

        $role_id = '';
        $role_name = '';
        $role = explode('|', $_POST['role_name']);
        if (count($role) !== 2) {
            $message = 'fail';
        } else {
            $role_id = $role[0];
            $role_name = $role[1];
        }

        $data = [
            'message' => $message,
            'staff_id' => $staff_id,
            "staff_username_{$staff_id}" => $username,
            'staff_role_id' => $role_id,
            "staff_role_name_{$staff_id}" => $role_name
        ];

        if ($message === 'success') {
            $db->update_team_staff($data);
        }
    }

    echo Encoder::toJSON($return);
} else if ($request === 'DELETE') {  // Delete existing item.
    $table = $itemType !== 'schedule' ? "{$itemType}s" : $itemType;
    $db->deleteRowById($table, $itemType, $itemID);
}
