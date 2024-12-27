<?php declare(strict_types=1);

require_once '../class/Database.php';
require_once '../class/Sanitize.php';
require_once '../class/Helper.php';

// Adding a new game.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = 'success';
    $clicked = $_POST['clicked'];

    // ADD NEW GAME
    if ($clicked === 'ADD_GAME') {
        $last_schedule_id = (int) $db->get_last_scheduled_game_id()['schedule_id'];

        $sport_id = (int) $_POST['sport_id'];
        $league_id = (int) $_POST['league_id'];
        $team_id = (int) $_POST['team_id'];

        $season_id = (int) $_POST['new_season_id'];
        if ($season_id === '') {
            $message = 'fail';
        } else if ($season_id <= 0) {
            $message = 'fail';
            $season_id = '';
        }

        $home_team_id = '';
        $home_team_name = '';
        Helper::set_team_name(
            $db,
            $message,
            $home_team_id,
            $home_team_name,
            'new_home_team_id'
        );

        $home_score = (int) $_POST['new_home_score'];
        if ($home_score === '') {
            $message = 'fail';
        } else if ($home_score < 0) {
            $message = 'fail';
            $home_score = '';
        }

        $away_team_id = '';
        $away_team_name = '';
        Helper::set_team_name(
            $db,
            $message,
            $away_team_id,
            $away_team_name,
            'new_away_team_id'
        );

        $away_score = (int) $_POST['new_away_score'];
        if ($away_score === '') {
            $message = 'fail';
        } else if ($away_score < 0) {
            $message = 'fail';
            $away_score = '';
        }

        // Correct teams.
        if ($season_id !== '') {
            $season_data = $db->get_seasons_by_league_id($league_id);

            if (count($season_data) > 0) {
                $team_data = $db->get_teams_by_season_id($season_id);
                if (count($team_data) > 0) {
                    Helper::validate_season(
                        $team_data,
                        $message,
                        $home_team_id,
                    );

                    Helper::validate_season(
                        $team_data,
                        $message,
                        $away_team_id,
                    );
                } else {
                    $message = 'fail';
                    $season_id = '';
                }
            } else {
                $message = 'fail';
                $season_id = '';
            }
        }

        // Can't be the same.
        if ($home_team_id === $away_team_id) {
            $message = 'fail';
            $home_team_id = '';
            $away_team_id = '';
        }

        // Must be selected team.
        if ($home_team_id !== '' && $away_team_id !== '') {
            $is_selected = false;
            foreach ([$home_team_id, $away_team_id] as $id) {
                if ((int) $id === $team_id) {
                    $is_selected = true;
                    break;
                }
            }

            if (!$is_selected) {
                $message = 'fail';
                $home_team_id = '';
                $away_team_id = '';
            }
        }

        $scheduled = $_POST['new_scheduled'];
        if ($scheduled === '') {
            $message = 'fail';
        }

        $status = (int) $_POST['new_status'];
        if ($status === '') {
            $message = 'fail';
        } else if ($status === 0) {
            $message = 'fail';
            $status = '';
        }

        $data = [
            'message' => $message,
            'last_schedule_id' => $last_schedule_id,
            'sport_id' => $sport_id,
            'league_id' => $league_id,
            'team_id' => $team_id,
            'new_season_id' => $season_id,
            'new_home_team_id' => $home_team_id,
            'new_home_team_name' => $home_team_name,
            'new_home_score' => $home_score,
            'new_away_team_id' => $away_team_id,
            'new_away_team_name' => $away_team_name,
            'new_away_score' => $away_score,
            'new_scheduled' => $scheduled,
            'new_status' => $status
        ];

        if ($message === 'success') {
            $db->alter_auto_increment('schedule', $last_schedule_id);
            $db->insert_new_schedule_game($data);
        }
    }

    // UPDATE TEAM & STAFF
    if ($clicked === 'UPDATE_TEAM') {
        $team_id = (int) $_POST['team_id'];

        $team_name = Sanitize::strip_string($_POST['team_name']);
        Sanitize::full_string_search($message, $team_name, 50);

        $home_color = Sanitize::strip_string($_POST['home_color']);
        Sanitize::full_color_search($message, $home_color, 25);

        $away_color = Sanitize::strip_string($_POST['away_color']);
        Sanitize::full_color_search($message, $away_color, 25);

        $data = [
            'message' => $message,
            'team_id' => $team_id,
            'team_name' => $team_name,
            'home_color' => $home_color,
            'away_color' => $away_color,
        ];

        if ($message === 'success') {
            $db->update_schedule_team_data($data);
        }
    } else if ($clicked === 'UPDATE_GAME') {
        $schedule_id = (int) $_POST['schedule_id'];

        $home_team_id = (int) $_POST['home_team_id'];
        if ($home_team_id === '') {
            $message = 'fail';
        } else if ($home_team_id <= 0) {
            $message = '';
            $home_team_id = '';
        }

        $home_score = (int) $_POST['home_score'];
        if ($home_score === '') {
            $message = 'fail';
        } else if ($home_score < 0) {
            $message = 'fail';
            $home_score = '';
        }

        $away_team_id = (int) $_POST['away_team_id'];
        if ($away_team_id === '') {
            $message = 'fail';
        } else if ($away_team_id <= 0) {
            $message = '';
            $away_team_id = '';
        }

        $away_score = (int) $_POST['away_score'];
        if ($away_score === '') {
            $message = 'fail';
        } else if ($away_score < 0) {
            $message = 'fail';
            $away_score = '';
        }

        $season_id = (int) $_POST['season_id'];
        if ($season_id === '') {
            $message = '';
        } else if ($season_id <= 0) {
            $message = 'fail';
            $season_id = '';
        }

        $scheduled = $_POST['scheduled'];
        if ($scheduled === '') {
            $message = 'fail';
        }

        $status = (int) $_POST['status'];
        if ($status === '') {
            $message = 'fail';
        } else if ($status === 0) {
            $message = 'fail';
            $status = '';
        }

        $data = [
            'message' => $message,
            'schedule_id' => $schedule_id,
            'edit_home_team_id' => $home_team_id,
            'edit_home_score' => $home_score,
            'edit_away_team_id' => $away_team_id,
            'edit_away_score' => $away_score,
            'edit_season_id' => $season_id,
            'edit_scheduled' => $scheduled,
            'edit_status' => $status
        ];

        if ($message === 'success') {
            $db->update_schedule_game($data);
        }
    }

    // DELETE TEAM & GAME
    if ($clicked === 'DELETE_TEAM') {
        $db->delete_team_by_id($_POST['team_id']);
    } else if ($clicked === 'DELETE_GAME') {
        $db->delete_schedule_game_by_id($_POST['schedule_id']);
    }

    echo json_encode($data);
}
