<?php declare(strict_types=1);

class Helper
{
    public static function setRoleIdAndName($input, &$status, &$roleID, &$roleName, $key)
    {
        $role = explode('|', $input[$key]);

        if (count($role) < 2) {
            $status = 'fail';
        } else {
            $roleID = $role[0];
            $roleName = $role[1];
        }
    }

    public static function setSportName($db, $input, &$status, &$sportID, &$sportName, $key)
    {
        $sportID = (int) $input[$key];
        if ($sportID === '') {
            $status = 'fail';
        } else if ($sportID <= 0) {
            $status = '';
            $sportID = '';
        } else {
            $sports = $db->getAllSports();

            if (count($sports) > 0) {
                foreach ($sports as $sport) {
                    if ((int) $sport['sport_id'] === $sportID) {
                        $sportName = $sport['sport_name'];
                        break;
                    }
                }
            }
        }

        // If sport does not exist.
        if ($sportName === '') {
            $status = 'fail';
            $sportID = '';
        }
    }

    public static function setLeagueAndTeamNames($db, $input, &$status, &$leagueID, &$leagueName, &$teamID, &$teamName, $key1, $key2)
    {
        $leagueID = (int) $input[$key1];
        if ($leagueID === '') {
            $status = 'fail';
        } else if ($leagueID === 0) {
            $leagueName = 'All';
        } else if ($leagueID < 0) {
            $status = 'fail';
            $leagueID = '';
        } else {
            $leagues = $db->getAllLeagues();

            if (count($leagues) > 0) {
                foreach ($leagues as $league) {
                    if ((int) $league['league_id'] === $leagueID) {
                        $leagueName = $league['league_name'];
                        break;
                    }
                }
            }
        }

        if ($leagueName === '') {
            $status = 'fail';
            $leagueID = '';
        }

        $teamID = (int) $input[$key2];
        if ($teamID === '') {
            $status = 'fail';
        } else if ($leagueID === 0 && $teamID === 0) {
            $teamName = 'All';
        } else if ($leagueID !== '' && $leagueID >= 0 && $teamID === 0) {
            $teamName = "All Within The {$leagueName}";
        } else if ($teamID < 0) {
            $status = 'fail';
            $teamID = '';
        } else {
            $teams = $db->getTeamsByLeagueId($leagueID);

            if (count($teams) > 0) {
                foreach ($teams as $team) {
                    if ((int) $team['team_id'] === $teamID) {
                        $teamName = $team['team_name'];
                        break;
                    }
                }
            }
        }

        if ($teamName === '') {
            $status = 'fail';
            $teamID = '';
        }
    }

    public static function setLeagueName($db, $input, &$status, &$sportID, &$leagueID, &$leagueName, $key)
    {
        $leagueID = (int) $input[$key];
        if ($leagueID === '') {
            $status = 'fail';
        } else if ($leagueID <= 0) {
            $status = 'fail';
            $leagueID = '';
        } else if ($sportID > 0) {
            $leagues = $db->getLeaguesBySportId($sportID);

            if (count($leagues) > 0) {
                foreach ($leagues as $league) {
                    if ((int) $league['league_id'] === $leagueID) {
                        $leagueName = $league['league_name'];
                        break;
                    }
                }
            }
        }

        // If sport does not exist.
        if ($leagueName === '') {
            $status = 'fail';
            $leagueID = '';
        }
    }

    public static function setTeamName($db, $input, &$status, &$teamID, &$teamName, $key)
    {
        $teamID = (int) $input[$key];
        if ($teamID === '') {
            $status = 'fail';
        } else if ($teamID <= 0) {
            $status = 'fail';
            $teamID = '';
        } else {
            $teamData = $db->getTeamDataByTeamId('team_name', $teamID);

            if (count($teamData) > 0) {
                $teamName = $teamData[0]['team_name'];
            }
        }

        if ($teamName === '') {
            $status = 'fail';
            $teamID = '';
        }
    }

    public static function validateSeason($teamData, &$status, &$teamID)
    {
        $is_valid = false;
        foreach ($teamData as $team) {
            if ($teamID === $team['team_id']) {
                $is_valid = true;
                break;
            }
        }

        if (!$is_valid) {
            $status = 'fail';
            $teamID = '';
        }
    }

    public static function setSeasonYear($db, $input, &$status, &$leagueID, &$season_id, &$seasonYear, $key)
    {
        $season_id = (int) $input[$key];
        if ($season_id === '') {
            $status = 'fail';
        } else if ($season_id <= 0) {
            $status = 'fail';
            $season_id = '';
        } else if ($season_id > 0) {
            $seasons = $db->getSeasonsByLeagueId($leagueID);

            if (count($seasons) > 0) {
                foreach ($seasons as $season) {
                    if ((int) $season['season_id'] === $season_id) {
                        $seasonYear = $season['season_year'];
                        break;
                    }
                }
            }
        }

        // If sport does not exist.
        if ($seasonYear === '') {
            $status = 'fail';
            $season_id = '';
        }
    }

    public static function setPositionName($db, $input, &$status, &$sportID, &$positionID, &$positionName, $key)
    {
        $positionID = (int) $input[$key];
        if ($positionID === '') {
            $status = 'fail';
        } else if ($positionID <= 0) {
            $status = 'fail';
            $positionID = '';
        } else {
            $positions = $db->getPositionsBySportId($sportID);

            if (count($positions) > 0) {
                foreach ($positions as $position) {
                    if ((int) $position['position_id'] === $positionID) {
                        $positionName = $position['position_name'];
                        break;
                    }
                }
            }
        }

        if ($positionName === '') {
            $status = 'fail';
            $positionID = '';
        }
    }
}
