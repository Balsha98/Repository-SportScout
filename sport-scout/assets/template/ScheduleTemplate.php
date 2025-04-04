<?php declare(strict_types=1);

require_once 'ReusableTemplate.php';
require_once 'assets/data/ScheduleData.php';

class ScheduleTemplate
{
    private static function generatePopupOptions($array, $column)
    {
        $return = '';
        foreach ($array as $option) {
            $optionID = $option["{$column}_id"];
            $columnName = $column === 'season' ? 'year' : 'name';
            $optionName = $option["{$column}_{$columnName}"];

            $return .= "
                <option value='{$optionID}|{$optionName}'>
                    {$optionName}
                </option>
            ";
        }

        return $return;
    }

    public static function generatePopups($db, $teamID)
    {
        $teamData = $db->getTeamDataByTeamId('*', $teamID);

        [['sport_name' => $sportName]] = $teamData;
        [['sport_id' => $sportID]] = $teamData;
        [['league_name' => $leagueName]] = $teamData;
        [['league_id' => $leagueID]] = $teamData;

        $return = '';
        foreach (ScheduleData::POPUPS as $i => $popup) {
            if ($i === 0) {
                $return .= sprintf(
                    $popup,
                    $sportName,
                    $leagueName,
                    self::generatePopupOptions(
                        $db->getDistinctRows('season'),
                        'season'
                    ),
                    self::generatePopupOptions(
                        $db->getDistinctRows('team'),
                        'team'
                    ),
                    self::generatePopupOptions(
                        $db->getDistinctRows('team'),
                        'team'
                    ),
                    $teamID,
                    $sportID,
                    $leagueID
                );
                continue;
            }

            $return .= sprintf(
                $popup,
                self::generatePopupOptions(
                    $db->getDistinctRows('season'),
                    'season'
                ),
                self::generatePopupOptions(
                    $db->getDistinctRows('team'),
                    'team'
                ),
                self::generatePopupOptions(
                    $db->getDistinctRows('team'),
                    'team'
                ),
                $teamID
            );
        }

        return $return;
    }

    private static function isDataAvailable($data)
    {
        $emptyCounter = 0;
        foreach ($data as $array) {
            if (count($array) === 0) {
                $emptyCounter++;
            }
        }

        $isEmpty = count($data) === $emptyCounter;
        $ruleFlexCenter = $isEmpty ? 'flex-center' : '';

        return [
            'is_empty' => $isEmpty,
            'css_rule' => $ruleFlexCenter
        ];
    }

    public static function generateTeamData($data, $roleID)
    {
        $return = '';
        ['is_empty' => $isEmpty] = self::isDataAvailable($data);

        if ($isEmpty) {
            return ReusableTemplate::generateNoneAvailableDiv('team', 'list');
        } else {
            foreach ($data as $team) {
                $teamID = $team['team_id'];
                $teamName = $team['team_name'];
                $homeColor = $team['home_color'];
                $awayColor = $team['away_color'];
                $sportName = $team['sport_name'];
                $sportID = $team['sport_id'];
                $leagueName = $team['league_name'];
                $leagueID = $team['league_id'];
                $seasonYear = $team['season_year'];
                $seasonID = $team['season_id'];

                // Submit buttons.
                $submitBtns = '';
                if ($roleID !== 5) {
                    $submitBtns = ReusableTemplate::generateFormSubmitBtns('team');
                }

                // Prevent the fan from changing input data.
                $requiredContainer = $roleID < 5 ? 'required-container' : '';
                $required = $roleID < 5 ? 'required' : 'readonly';

                $return .= "
                    <form 
                        class='form form-team-data' 
                        action='" . SERVER . "/api/schedule.php'
                    >
                        <div class='div-multi-input-containers custom-2-column-grid'>
                            <div class='div-input-container {$requiredContainer}'>
                                <label for='team_name'>Team Name:</label>
                                <input id='team_name' type='text' name='team_name' value='{$teamName}' data-prev-team-name='{$teamName}' {$required}>
                            </div>
                            <div class='div-input-container'>
                                <label for='team_id'>Team ID:</label>
                                <input id='team_id' type='number' name='team_id' value='{$teamID}' readonly>
                            </div>
                        </div>
                        <div class='div-logo-colors-container grid-2-columns'>
                            <div class='div-team-logo-container'>
                                <ion-icon class='team-icon' name='people-circle-outline'></ion-icon>
                            </div>
                            <div class='div-colors-container'>
                                <ul class='team-colors-list'>
                                    <li class='team-color-list-item'>
                                        <div class='team-color' data-colors='{$homeColor}'>&nbsp;</div>
                                        <div class='div-input-container {$requiredContainer}'>
                                            <label for='team_home_color'>Home:</label>
                                            <input id='team_home_color' type='text' name='home_color' value='{$homeColor}' {$required}>
                                        </div>
                                    </li>
                                    <li class='team-color-list-item'>
                                        <div class='team-color' data-colors='{$awayColor}'>&nbsp;</div>
                                        <div class='div-input-container {$requiredContainer}'>
                                            <label for='team_away_color'>Away:</label>
                                            <input id='team_away_color' type='text' name='away_color' value='{$awayColor}' {$required}>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class='div-multi-input-containers grid-3-columns'>
                            <div class='div-input-container'>
                                <label for='team_sport_name'>Sport:</label>
                                <input id='team_sport_name' type='text' name='sport_name' value='{$sportName}' readonly>
                            </div>
                            <div class='div-input-container'>
                                <label for='team_league_name'>League:</label>
                                <input id='team_league_name' type='text' name='league_name' value='{$leagueName}' readonly>
                            </div>
                            <div class='div-input-container'>
                                <label for='team_season_year'>Season:</label>
                                <input id='team_season_year' type='text' name='season_year' value='{$seasonYear}' readonly>
                            </div>
                        </div>
                        {$submitBtns}
                        <div class='div-hidden-inputs-container'>
                            <input id='team_sport_id' type='hidden' name='sport_id' value='{$sportID}'>
                            <input id='team_league_id' type='hidden' name='league_id' value='{$leagueID}'>
                            <input id='team_season_id' type='hidden' name='season_id' value='{$seasonID}'>
                        </div>
                    </form>
                ";
            }

            return $return;
        }
    }

    public static function generateGame($db, $data, $roleID)
    {
        [
            'is_empty' => $isEmpty,
            'css_rule' => $ruleFlexCenter
        ] = self::isDataAvailable($data);

        $scrollContainer = "
            <div class='div-scroll-container grid-2-columns {$ruleFlexCenter}'>
        ";

        if ($isEmpty) {
            $scrollContainer .= ReusableTemplate::generateNoneAvailableDiv('game', 'schedule');
        } else {
            $totalInnerArrays = 0;
            $emptyInnerCounter = 0;
            foreach ($data as $array) {
                ['is_empty' => $isEmpty] = self::isDataAvailable($array);

                if ($isEmpty) {
                    $emptyInnerCounter++;
                } else {
                    foreach ($array as $innerArray) {
                        $currTeamID = $innerArray['team_id'];
                        $scheduleID = $innerArray['schedule_id'];
                        $seasonID = $innerArray['season_id'];
                        [['season_year' => $seasonYear]] = $db->getRowDataById('season', $seasonID);

                        // Temp team values.
                        $homeScore = $innerArray['home_score'];
                        $homeTeamID = $innerArray['home_team_id'];
                        $awayScore = $innerArray['away_score'];
                        $awayTeamID = $innerArray['away_team_id'];
                        $date = $innerArray['scheduled'];

                        // Get home and away team names.
                        [['team_name' => $homeTeamName]] = $db->getTeamDataByTeamId('team_name', $homeTeamID);
                        [['team_name' => $awayTeamName]] = $db->getTeamDataByTeamId('team_name', $awayTeamID);

                        // Get appropriate visuals.
                        $status = $innerArray['status'];
                        switch ($status) {
                            case 1:
                                $css = 'canceled';
                                $icon = 'close';
                                break;
                            case 2:
                                $css = 'pending';
                                $icon = 'alert';
                                break;
                            default:
                                $css = 'completed';
                                $icon = 'checkmark';
                        }

                        // Button edit.
                        $editBtn = '';
                        if ($roleID !== 5) {
                            $editBtn = ReusableTemplate::generatePopupEditBtn();
                        }

                        $scrollContainer .= "
                            <div class='div-schedule-game game-{$scheduleID}' data-schedule-id='{$scheduleID}'>
                                {$editBtn}
                                <div class='div-scoreboard-container'>
                                    <div class='div-grid-score-container'>
                                        <p class='home-score'>{$homeScore}</p>
                                        <p>:</p>
                                        <p class='away-score'>{$awayScore}</p>
                                    </div>
                                    <div class='div-grid-score-container'>
                                        <div class='div-team-name-container'>
                                            <p class='home-team'>{$homeTeamName}</p>
                                            <span>Home</span>
                                        </div>
                                        <p>&nbsp;</p>
                                        <div class='div-team-name-container'>
                                            <p class='away-team'>{$awayTeamName}</p>
                                            <span>Away</span>
                                        </div>
                                    </div>
                                </div>
                                <div class='div-date-completion-container'>
                                    <div class='div-input-container'>
                                        <label for='scheduled_{$scheduleID}'>Game Date:</label>
                                        <input id='scheduled_{$scheduleID}' type='date' name='scheduled' value='{$date}' readonly>
                                    </div>
                                    <span class='span-completion-status {$css}' data-completion-index='{$status}'>
                                        <ion-icon class='status-icon' name='{$icon}-outline'></ion-icon>
                                    </span>
                                </div>
                                <div class='div-hidden-inputs-container'>
                                    <input id='schedule_id_{$scheduleID}' type='hidden' name='schedule_id' value='{$scheduleID}'>
                                    <input id='schedule_team_id_{$scheduleID}' type='hidden' name='team_id' value='{$currTeamID}'>
                                    <input id='schedule_season_id_{$scheduleID}' type='hidden' name='season_id' value='{$seasonID}'>
                                    <input id='schedule_season_year_{$scheduleID}' type='hidden' name='season_year' value='{$seasonYear}'>
                                    <input id='schedule_home_team_id_{$scheduleID}' type='hidden' name='home_team_id' value='{$homeTeamID}'>
                                    <input id='schedule_home_team_name_{$scheduleID}' type='hidden' name='home_team_name' value='{$homeTeamName}'>
                                    <input id='schedule_home_score_{$scheduleID}' type='hidden' name='home_score' value='{$homeScore}'>
                                    <input id='schedule_away_team_id_{$scheduleID}' type='hidden' name='away_team_id' value='{$awayTeamID}'>
                                    <input id='schedule_away_team_name_{$scheduleID}' type='hidden' name='away_team_name' value='{$awayTeamName}'>
                                    <input id='schedule_away_score_{$scheduleID}' type='hidden' name='away_score' value='{$awayScore}'>
                                    <input id='schedule_date_{$scheduleID}' type='hidden' name='date' value='{$date}'>
                                    <input id='schedule_completion_status_{$scheduleID}' type='hidden' name='completion_status' value='{$status}'>
                                </div>
                            </div>
                        ";
                    }
                }

                $totalInnerArrays++;
            }

            if ($totalInnerArrays === $emptyInnerCounter) {
                $scrollContainer = ReusableTemplate::generateNoneAvailableDiv('game', 'schedule');
            }
        }

        // Button add.
        $addBtn = '';
        if ($roleID <= 2) {
            $addBtn = ReusableTemplate::generatePopupAddBtn();
        }

        $scrollContainer .= "
            {$addBtn}
            </div>
        ";

        return $scrollContainer;
    }
}
