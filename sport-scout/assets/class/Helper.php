<?php declare(strict_types=1);

class Helper
{
    static function set_role_id_and_name(&$message, &$role_id, &$role_name, $key)
    {
        $role = explode('|', $_POST[$key]);

        if (count($role) < 2) {
            $message = 'fail';
        } else {
            $role_id = $role[0];
            $role_name = $role[1];
        }
    }

    static function set_sport_name($db, &$message, &$sport_id, &$sport_name, $key)
    {
        $sport_id = (int) $_POST[$key];
        if ($sport_id === '') {
            $message = 'fail';
        } else if ($sport_id <= 0) {
            $message = '';
            $sport_id = '';
        } else {
            $sports = $db->get_all_sports();

            if (count($sports) > 0) {
                foreach ($sports as $sport) {
                    if ((int) $sport['sport_id'] === $sport_id) {
                        $sport_name = $sport['sport_name'];
                        break;
                    }
                }
            }
        }

        // If sport does not exist.
        if ($sport_name === '') {
            $message = 'fail';
            $sport_id = '';
        }
    }

    static function set_league_and_team_names($db, &$message, &$league_id, &$league_name, &$team_id, &$team_name, $key1, $key2)
    {
        $league_id = (int) $_POST[$key1];
        if ($league_id === '') {
            $message = 'fail';
        } else if ($league_id === 0) {
            $league_name = 'All';
        } else if ($league_id < 0) {
            $message = 'fail';
            $league_id = '';
        } else {
            $leagues = $db->get_all_leagues();

            if (count($leagues) > 0) {
                foreach ($leagues as $league) {
                    if ((int) $league['league_id'] === $league_id) {
                        $league_name = $league['league_name'];
                        break;
                    }
                }
            }
        }

        if ($league_name === '') {
            $message = 'fail';
            $league_id = '';
        }

        $team_id = (int) $_POST[$key2];
        if ($team_id === '') {
            $message = 'fail';
        } else if ($league_id === 0 && $team_id === 0) {
            $team_name = 'All';
        } else if ($league_id !== '' && $league_id >= 0 && $team_id === 0) {
            $team_name = "All Within The {$league_name}";
        } else if ($team_id < 0) {
            $message = 'fail';
            $team_id = '';
        } else {
            $teams = $db->get_teams_by_league_id($league_id);

            if (count($teams) > 0) {
                foreach ($teams as $team) {
                    if ((int) $team['team_id'] === $team_id) {
                        $team_name = $team['team_name'];
                        break;
                    }
                }
            }
        }

        if ($team_name === '') {
            $message = 'fail';
            $team_id = '';
        }
    }

    static function set_league_name($db, &$message, &$sport_id, &$league_id, &$league_name, $key)
    {
        $league_id = (int) $_POST[$key];
        if ($league_id === '') {
            $message = 'fail';
        } else if ($league_id <= 0) {
            $message = 'fail';
            $league_id = '';
        } else if ($sport_id > 0) {
            $leagues = $db->get_leagues_by_sport_id($sport_id);

            if (count($leagues) > 0) {
                foreach ($leagues as $league) {
                    if ((int) $league['league_id'] === $league_id) {
                        $league_name = $league['league_name'];
                        break;
                    }
                }
            }
        }

        // If sport does not exist.
        if ($league_name === '') {
            $message = 'fail';
            $league_id = '';
        }
    }

    static function set_team_name($db, &$message, &$team_id, &$team_name, $key)
    {
        $team_id = (int) $_POST[$key];
        if ($team_id === '') {
            $message = 'fail';
        } else if ($team_id <= 0) {
            $message = 'fail';
            $team_id = '';
        } else {
            $team_data = $db->get_team_data_by_team_id('team_name', $team_id);

            if (count($team_data) > 0) {
                $team_name = $team_data[0]['team_name'];
            }
        }

        if ($team_name === '') {
            $message = 'fail';
            $team_id = '';
        }
    }

    static function validate_season($team_data, &$message, &$team_id)
    {
        $is_valid = false;
        foreach ($team_data as $team) {
            if ($team_id === $team['team_id']) {
                $is_valid = true;
                break;
            }
        }

        if (!$is_valid) {
            $message = 'fail';
            $team_id = '';
        }
    }

    static function set_season_year($db, &$message, &$league_id, &$season_id, &$season_year, $key)
    {
        $season_id = (int) $_POST[$key];
        if ($season_id === '') {
            $message = 'fail';
        } else if ($season_id <= 0) {
            $message = 'fail';
            $season_id = '';
        } else if ($season_id > 0) {
            $seasons = $db->get_seasons_by_league_id($league_id);

            if (count($seasons) > 0) {
                foreach ($seasons as $season) {
                    if ((int) $season['season_id'] === $season_id) {
                        $season_year = $season['season_year'];
                        break;
                    }
                }
            }
        }

        // If sport does not exist.
        if ($season_year === '') {
            $message = 'fail';
            $season_id = '';
        }
    }

    static function set_position_name($db, &$message, &$sport_id, &$position_id, &$position_name, $key)
    {
        $position_id = (int) $_POST[$key];
        if ($position_id === '') {
            $message = 'fail';
        } else if ($position_id <= 0) {
            $message = 'fail';
            $position_id = '';
        } else {
            $positions = $db->get_positions_by_sport_id($sport_id);

            if (count($positions) > 0) {
                foreach ($positions as $position) {
                    if ((int) $position['position_id'] === $position_id) {
                        $position_name = $position['position_name'];
                        break;
                    }
                }
            }
        }

        if ($position_name === '') {
            $message = 'fail';
            $position_id = '';
        }
    }
}
