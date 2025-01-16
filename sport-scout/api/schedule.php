<?php declare(strict_types=1);

require_once '../assets/class/Database.php';
require_once '../assets/class/Encoder.php';
require_once '../assets/class/Sanitize.php';
require_once '../assets/class/Helper.php';

$request = $_SERVER['REQUEST_METHOD'];
$input = Encoder::fromJSON('php://input');
$itemType = $input['item_type'];
if (array_key_exists('item_id', $input)) {
    $itemID = (int) $input['item_id'];
}

$status = 'success';
$return = [];

// Creating a new item.
if ($request === 'POST') {
    $table = $itemType !== 'schedule' ? "{$itemType}s" : $itemType;
    ['id' => $lastRowID] = $db->getLastRowId($table, $itemType);

    $sportID = (int) $_POST['sport_id'];
    $leagueID = (int) $_POST['league_id'];
    $teamID = (int) $_POST['team_id'];

    $seasonID = (int) $_POST['new_season_id'];
    if ($seasonID === '') {
        $status = 'fail';
    } else if ($seasonID <= 0) {
        $status = 'fail';
        $seasonID = '';
    }

    $homeTeamID = '';
    $homeTeamName = '';
    Helper::setTeamName(
        $db,
        $input,
        $status,
        $homeTeamID,
        $homeTeamName,
        'new_home_team_id'
    );

    $homeScore = (int) $_POST['new_home_score'];
    if ($homeScore === '') {
        $status = 'fail';
    } else if ($homeScore < 0) {
        $status = 'fail';
        $homeScore = '';
    }

    $awayTeamID = '';
    $awayTeamName = '';
    Helper::setTeamName(
        $db,
        $input,
        $status,
        $awayTeamID,
        $awayTeamName,
        'new_away_team_id'
    );

    $awayScore = (int) $_POST['new_away_score'];
    if ($awayScore === '') {
        $status = 'fail';
    } else if ($awayScore < 0) {
        $status = 'fail';
        $awayScore = '';
    }

    // Correct teams.
    if ($seasonID !== '') {
        $seasonData = $db->get_seasons_by_league_id($leagueID);

        if (count($seasonData) > 0) {
            $teamData = $db->get_teams_by_season_id($seasonID);
            if (count($teamData) > 0) {
                Helper::validateSeason(
                    $teamData,
                    $status,
                    $homeTeamID,
                );

                Helper::validateSeason(
                    $teamData,
                    $status,
                    $awayTeamID,
                );
            } else {
                $status = 'fail';
                $seasonID = '';
            }
        } else {
            $status = 'fail';
            $seasonID = '';
        }
    }

    // Can't be the same.
    if ($homeTeamID === $awayTeamID) {
        $status = 'fail';
        $homeTeamID = '';
        $awayTeamID = '';
    }

    // Must be selected team.
    if ($homeTeamID !== '' && $awayTeamID !== '') {
        $isSelected = false;
        foreach ([$homeTeamID, $awayTeamID] as $id) {
            if ((int) $id === $teamID) {
                $isSelected = true;
                break;
            }
        }

        if (!$isSelected) {
            $status = 'fail';
            $homeTeamID = '';
            $awayTeamID = '';
        }
    }

    $scheduled = $_POST['new_scheduled'];
    if ($scheduled === '') {
        $status = 'fail';
    }

    $status = (int) $_POST['new_status'];
    if ($status === '') {
        $status = 'fail';
    } else if ($status === 0) {
        $status = 'fail';
        $status = '';
    }

    $return = [
        'status' => $status,
        'last_schedule_id' => $last_schedule_id,
        'sport_id' => $sportID,
        'league_id' => $leagueID,
        'team_id' => $teamID,
        'new_season_id' => $seasonID,
        'new_home_team_id' => $homeTeamID,
        'new_home_team_name' => $homeTeamName,
        'new_home_score' => $homeScore,
        'new_away_team_id' => $awayTeamID,
        'new_away_team_name' => $awayTeamName,
        'new_away_score' => $awayScore,
        'new_scheduled' => $scheduled,
        'new_status' => $status
    ];

    if ($status === 'success') {
        $db->alterAutoIncrement($itemType, $last_schedule_id);
        $db->insertNewScheduleGame($return);
    }

    echo Encoder::toJSON($return);
} else if ($request === 'PUT') {
    if ($itemType === 'team') {
        $teamID = (int) $_POST['team_id'];

        $team_name = Sanitize::stripString($_POST['team_name']);
        Sanitize::fullStringSearch($status, $team_name, 50);

        $home_color = Sanitize::stripString($_POST['home_color']);
        Sanitize::fullColorSearch($status, $home_color, 25);

        $away_color = Sanitize::stripString($_POST['away_color']);
        Sanitize::fullColorSearch($status, $away_color, 25);

        $return = [
            'status' => $status,
            'team_id' => $teamID,
            'team_name' => $team_name,
            'home_color' => $home_color,
            'away_color' => $away_color,
        ];

        if ($status === 'success') {
            $db->updateScheduleTeamData($return);
        }
    } else if ($itemType === 'schedule') {
        $homeTeamID = (int) $_POST['home_team_id'];
        if ($homeTeamID === '') {
            $status = 'fail';
        } else if ($homeTeamID <= 0) {
            $status = '';
            $homeTeamID = '';
        }

        $homeScore = (int) $_POST['home_score'];
        if ($homeScore === '') {
            $status = 'fail';
        } else if ($homeScore < 0) {
            $status = 'fail';
            $homeScore = '';
        }

        $awayTeamID = (int) $_POST['away_team_id'];
        if ($awayTeamID === '') {
            $status = 'fail';
        } else if ($awayTeamID <= 0) {
            $status = '';
            $awayTeamID = '';
        }

        $awayScore = (int) $_POST['away_score'];
        if ($awayScore === '') {
            $status = 'fail';
        } else if ($awayScore < 0) {
            $status = 'fail';
            $awayScore = '';
        }

        $seasonID = (int) $_POST['season_id'];
        if ($seasonID === '') {
            $status = '';
        } else if ($seasonID <= 0) {
            $status = 'fail';
            $seasonID = '';
        }

        $scheduled = $_POST['scheduled'];
        if ($scheduled === '') {
            $status = 'fail';
        }

        $status = (int) $_POST['status'];
        if ($status === '') {
            $status = 'fail';
        } else if ($status === 0) {
            $status = 'fail';
            $status = '';
        }

        $return = [
            'status' => $status,
            'schedule_id' => $itemID,
            'edit_home_team_id' => $homeTeamID,
            'edit_home_score' => $homeScore,
            'edit_away_team_id' => $awayTeamID,
            'edit_away_score' => $awayScore,
            'edit_season_id' => $seasonID,
            'edit_scheduled' => $scheduled,
            'edit_status' => $status
        ];

        if ($status === 'success') {
            $db->updateScheduleGame($return);
        }
    }

    echo Encoder::toJSON($return);
} else if ($request === 'DELETE') {
    $table = $itemType !== 'schedule' ? "{$itemType}s" : $itemType;
    $db->deleteRowById($table, $itemType, $itemID);
}
