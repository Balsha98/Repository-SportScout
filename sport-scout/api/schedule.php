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

// Creating a new item.
if ($request === 'POST') {
    $table = $itemType !== 'schedule' ? "{$itemType}s" : $itemType;
    ['id' => $lastRowID] = $db->getLastRowId($table, $itemType);

    $sportID = (int) $input['new_schedule_sport_id'];
    $leagueID = (int) $input['new_schedule_league_id'];
    $teamID = (int) $input['new_schedule_team_id'];

    $seasonID = (int) $input['new_schedule_season_id'];
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
        'new_schedule_home_team_id'
    );

    $homeScore = (int) $input['new_schedule_home_score'];
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
        'new_schedule_away_team_id'
    );

    $awayScore = (int) $input['new_schedule_away_score'];
    if ($awayScore === '') {
        $status = 'fail';
    } else if ($awayScore < 0) {
        $status = 'fail';
        $awayScore = '';
    }

    // Correct teams.
    if ($seasonID !== '') {
        $seasonData = $db->getSeasonsByLeagueId($leagueID);

        if (count($seasonData) > 0) {
            $teamData = $db->getTeamsBySeasonId($seasonID);
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

    $scheduled = $input['new_schedule_date'];
    if ($scheduled === '') {
        $status = 'fail';
    }

    $status = (int) $input['new_schedule_completion_status'];
    if ($status === '') {
        $status = 'fail';
    } else if ($status === 0) {
        $status = 'fail';
        $status = '';
    }

    $return = [
        'status' => $status,
        'last_schedule_id' => $lastRowID,
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
        $db->alterAutoIncrement($itemType, $lastRowID);
        $db->insertNewScheduleGame($return);
    }

    echo Encoder::toJSON($return);
} else if ($request === 'PUT') {
    if ($itemType === 'team') {
        $teamID = (int) $input['team_id'];

        $teamName = Sanitize::stripString($input['team_name']);
        Sanitize::fullStringSearch($status, $teamName, 50);

        $homeColor = Sanitize::stripString($input['home_color']);
        Sanitize::fullColorSearch($status, $homeColor, 25);

        $awayColor = Sanitize::stripString($input['away_color']);
        Sanitize::fullColorSearch($status, $awayColor, 25);

        $return = [
            'status' => $status,
            'team_id' => $teamID,
            'team_name' => $teamName,
            'home_color' => $homeColor,
            'away_color' => $awayColor,
        ];

        if ($status === 'success') {
            $db->updateScheduleTeamData($return);
        }
    } else if ($itemType === 'schedule') {
        $homeTeamID = (int) $input['home_team_id'];
        if ($homeTeamID === '') {
            $status = 'fail';
        } else if ($homeTeamID <= 0) {
            $status = '';
            $homeTeamID = '';
        }

        $homeScore = (int) $input['home_score'];
        if ($homeScore === '') {
            $status = 'fail';
        } else if ($homeScore < 0) {
            $status = 'fail';
            $homeScore = '';
        }

        $awayTeamID = (int) $input['away_team_id'];
        if ($awayTeamID === '') {
            $status = 'fail';
        } else if ($awayTeamID <= 0) {
            $status = '';
            $awayTeamID = '';
        }

        $awayScore = (int) $input['away_score'];
        if ($awayScore === '') {
            $status = 'fail';
        } else if ($awayScore < 0) {
            $status = 'fail';
            $awayScore = '';
        }

        $seasonID = (int) $input['season_id'];
        if ($seasonID === '') {
            $status = '';
        } else if ($seasonID <= 0) {
            $status = 'fail';
            $seasonID = '';
        }

        $scheduled = $input['scheduled'];
        if ($scheduled === '') {
            $status = 'fail';
        }

        $status = (int) $input['status'];
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
