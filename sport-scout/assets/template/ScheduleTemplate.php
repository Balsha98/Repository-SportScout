<?php declare(strict_types=1);

require_once 'ReusableTemplate.php';
require_once '../data/ScheduleData.php';
require_once '../class/Cookie.php';
require_once '../class/Database.php';

class ScheduleTemplate
{
    private static Database $db;

    static function set_database($db)
    {
        if (!isset(self::$db)) {
            self::$db = $db;
        }
    }

    private static function is_data_available($data)
    {
        $empty_counter = 0;
        foreach ($data as $array) {
            if (count($array) === 0) {
                $empty_counter++;
            }
        }

        $is_empty = count($data) === $empty_counter;
        $rule_flex_center = $is_empty ? 'flex-center' : '';

        return [
            'is_empty' => $is_empty,
            'css_rule' => $rule_flex_center
        ];
    }

    static function generate_popups($team_id)
    {
        $return = '';
        $team_data = self::$db->get_team_data_by_team_id('*', $team_id);

        $sport_name = $team_data[0]['sport_name'];
        $sport_id = $team_data[0]['sport_id'];
        $league_name = $team_data[0]['league_name'];
        $league_id = $team_data[0]['league_id'];

        foreach (ScheduleData::POPUPS as $popup) {
            $return .= sprintf(
                $popup,
                $team_id,
                $sport_id,
                $league_id,
                $sport_name,
                $league_name
            );
        }

        return $return;
    }

    static function generate_team_data($data, $role_id)
    {
        $return = '';
        ['is_empty' => $is_empty] = self::is_data_available($data);

        if ($is_empty) {
            return ReusableTemplate::generate_none_available_div('team', 'list');
        } else {
            foreach ($data as $team) {
                $team_id = $team['team_id'];
                $team_name = $team['team_name'];
                $home_color = $team['home_color'];
                $away_color = $team['away_color'];
                $sport_name = $team['sport_name'];
                $sport_id = $team['sport_id'];
                $league_name = $team['league_name'];
                $league_id = $team['league_id'];
                $season_year = $team['season_year'];
                $season_id = $team['season_id'];

                // Submit buttons.
                $submit_btns = '';
                if ($role_id !== 5) {
                    $submit_btns = ReusableTemplate::generate_form_submit_btns('TEAM');
                }

                // Prevent the fan from changing input data.
                $required_container = $role_id < 5 ? 'required-container' : '';
                $required = $role_id < 5 ? 'required' : 'readonly';

                $return .= "
                    <form class='form form-team-data' action='../process/process-schedule.php'>
                        <input type='hidden' name='sport_id' value='{$sport_id}'>
                        <input type='hidden' name='league_id' value='{$league_id}'>
                        <input type='hidden' name='season_id' value='{$season_id}'>
                        <div class='div-multi-input-containers custom-2-column-grid'>
                            <div class='div-input-container {$required_container}'>
                                <label for='team_name'>Team Name:</label>
                                <input id='team_name' type='text' name='team_name' value='{$team_name}' data-prev-team-name='{$team_name}' {$required}>
                            </div>
                            <div class='div-input-container'>
                                <label for='team_id'>Team ID:</label>
                                <input id='team_id' type='number' name='team_id' value='{$team_id}' readonly>
                            </div>
                        </div>
                        <div class='div-logo-colors-container grid-2-columns'>
                            <div class='div-team-logo-container'>
                                <ion-icon class='team-icon' name='people-circle-outline'></ion-icon>
                            </div>
                            <div class='div-colors-container'>
                                <ul class='team-colors-list'>
                                    <li class='team-color-list-item'>
                                        <div class='team-color' data-colors='{$home_color}'>&nbsp;</div>
                                        <div class='div-input-container {$required_container}'>
                                            <label for='home_color'>Home:</label>
                                            <input id='home_color' type='text' name='home_color' value='{$home_color}' {$required}>
                                        </div>
                                    </li>
                                    <li class='team-color-list-item'>
                                        <div class='team-color' data-colors='{$away_color}'>&nbsp;</div>
                                        <div class='div-input-container {$required_container}'>
                                            <label for='away_color'>Away:</label>
                                            <input id='away_color' type='text' name='away_color' value='{$away_color}' {$required}>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class='div-multi-input-containers grid-3-columns'>
                            <div class='div-input-container'>
                                <label for='team_sport_name'>Sport:</label>
                                <input id='team_sport_name' type='text' name='sport_name' value='{$sport_name}' readonly>
                            </div>
                            <div class='div-input-container'>
                                <label for='team_league_name'>League:</label>
                                <input id='team_league_name' type='text' name='league_name' value='{$league_name}' readonly>
                            </div>
                            <div class='div-input-container'>
                                <label for='team_season_year'>Season:</label>
                                <input id='team_season_year' type='text' name='season_year' value='{$season_year}' readonly>
                            </div>
                        </div>
                        {$submit_btns}
                    </form>
                ";
            }

            return $return;
        }
    }

    static function generate_game($data, $role_id)
    {
        [
            'is_empty' => $is_empty,
            'css_rule' => $rule_flex_center
        ] = self::is_data_available($data);

        $scroll_container = "
            <div class='div-scroll-container grid-2-columns {$rule_flex_center}'>
        ";

        if ($is_empty) {
            $scroll_container .= ReusableTemplate::generate_none_available_div('game', 'schedule');
        } else {
            $total_inner_arrays = 0;
            $empty_inner_counter = 0;
            foreach ($data as $array) {
                ['is_empty' => $is_empty] = self::is_data_available($array);

                if ($is_empty) {
                    $empty_inner_counter++;
                } else {
                    foreach ($array as $inner_array) {
                        $schedule_id = $inner_array['schedule_id'];
                        $season_id = $inner_array['season_id'];

                        // Temp team values.
                        $home_score = $inner_array['home_score'];
                        $home_team_id = $inner_array['home_team_id'];
                        $away_score = $inner_array['away_score'];
                        $away_team_id = $inner_array['away_team_id'];
                        $date = $inner_array['scheduled'];

                        // Get home and away team names.
                        $home_team = self::$db->get_team_data_by_team_id('team_name', $home_team_id)[0]['team_name'];
                        $away_team = self::$db->get_team_data_by_team_id('team_name', $away_team_id)[0]['team_name'];

                        // Get appropriate visuals.
                        $status = $inner_array['status'];
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

                        // Dataset.
                        $dataset = "{$home_team_id}|{$home_score}|{$away_team_id}|{$away_score}|{$season_id}|{$status}|{$date}";

                        // Button edit.
                        $edit_btn = '';
                        if ($role_id !== 5) {
                            $edit_btn = ReusableTemplate::generate_popup_edit_btn();
                        }

                        $scroll_container .= "
                            <div class='div-schedule-game game-{$schedule_id}' data-schedule-id='{$schedule_id}' data-editable-data='{$dataset}'>
                                {$edit_btn}
                                <div class='div-scoreboard-container'>
                                    <div class='div-grid-score-container'>
                                        <p class='home-score'>{$home_score}</p>
                                        <p>:</p>
                                        <p class='away-score'>{$away_score}</p>
                                    </div>
                                    <div class='div-grid-score-container'>
                                        <div class='div-team-name-container'>
                                            <p class='home-team'>{$home_team}</p>
                                            <span>Home</span>
                                        </div>
                                        <p>&nbsp;</p>
                                        <div class='div-team-name-container'>
                                            <p class='away-team'>{$away_team}</p>
                                            <span>Away</span>
                                        </div>
                                    </div>
                                </div>
                                <div class='div-date-completion-container'>
                                    <div class='div-input-container'>
                                        <label for='scheduled_{$schedule_id}'>Game Date:</label>
                                        <input id='scheduled_{$schedule_id}' type='date' name='scheduled' value='{$date}' readonly>
                                    </div>
                                    <div class='div-completion-status {$css}' data-completion-index='{$status}'>
                                        <ion-icon class='status-icon' name='{$icon}-outline'></ion-icon>
                                    </div>
                                </div>
                            </div>
                        ";
                    }
                }

                $total_inner_arrays++;
            }

            if ($total_inner_arrays === $empty_inner_counter) {
                $scroll_container = ReusableTemplate::generate_none_available_div('game', 'schedule');
            }
        }

        // Button add.
        $add_btn = '';
        if ($role_id <= 2) {
            $add_btn = ReusableTemplate::generate_popup_add_btn();
        }

        $scroll_container .= "
            {$add_btn}
            </div>
        ";

        return $scroll_container;
    }
}

ScheduleTemplate::set_database($db);
