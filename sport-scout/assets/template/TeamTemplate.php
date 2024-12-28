<?php declare(strict_types=1);

require_once 'ReusableTemplate.php';
require_once 'assets/data/TeamData.php';

class TeamTemplate
{
    private static function isDataAvailable($data)
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

    public static function generatePopups($team_data)
    {
        $return = '';

        $sport_id = $team_data[0]['sport_id'];
        $league_id = $team_data[0]['league_id'];
        $league_name = $team_data[0]['league_name'];
        $team_id = $team_data[0]['team_id'];

        foreach (TeamData::POPUPS as $index => $popup) {
            if ($index === 0) {
                $return .= sprintf(
                    $popup,
                    $team_id,
                    $sport_id,
                    $league_name
                );

                continue;
            }

            $return .= sprintf(
                $popup,
                $team_id,
                $league_id,
                $league_name
            );
        }

        return $return;
    }

    public static function generatePlayersData($data, $role_id, $league_name)
    {
        [
            'is_empty' => $is_empty,
            'css_rule' => $rule_flex_center
        ] = self::isDataAvailable($data);

        $scroll_container = "
            <div class='div-scroll-container players-scroll {$rule_flex_center}'>
        ";

        if ($is_empty) {
            $scroll_container .= ReusableTemplate::generateNoneAvailableDiv('player', 'team');;
        } else {
            foreach ($data as $player) {
                $player_id = $player['player_id'];
                $player_first = $player['player_first'];
                $player_last = $player['player_last'];
                $full_name = "{$player_first} {$player_last}";
                $position_id = $player['position_id'];
                $position_name = $player['position_name'];
                $player_dob = $player['player_dob'];
                $player_jersey = $player['player_jersey'];
                $sport_id = $player['sport_id'];
                $team_id = $player['team_id'];

                $submit_btns = '';
                if ($role_id !== 5) {
                    $submit_btns = ReusableTemplate::generateFormSubmitBtns('PLAYER');
                }

                // Prevent the fan from changing input data.
                $required_container = $role_id < 5 ? 'required-container' : '';
                $required = $role_id < 5 ? 'required' : 'readonly';

                $scroll_container .= "
                    <div class='div-row-container player-row-container-{$player_id}' data-row-id='{$player_id}'>
                        <ul class='row-header-list custom-auto-3-column-grid'>
                            <li class='row-header-list-item'>
                                <p class='full-name'>{$full_name}</p>
                            </li>
                            <li class='row-header-list-item'>
                                <p class='position-name'>{$position_name}</p>
                            </li>
                            <li class='row-header-list-item'>
                                <p>
                                    <ion-icon class='open-icon icon-{$player_id}' name='chevron-down-outline'></ion-icon>
                                </p>
                            </li>
                        </ul>
                        <form class='form form-info form-{$player_id} hide-element' action='../process/process-team.php'>
                            <input type='hidden' name='team_id' value='{$team_id}'>
                            <input type='hidden' name='sport_id' value='{$sport_id}'>
                            <input type='hidden' name='player_id' value='{$player_id}'>
                            <div class='div-multi-input-containers grid-2-columns'>
                                <div class='div-input-container {$required_container}'>
                                    <label for='player_first_{$player_id}'>First Name:</label>
                                    <input id='player_first_{$player_id}' type='text' name='player_first' value='{$player_first}' autocomplete='off' {$required}>
                                </div>
                                <div class='div-input-container {$required_container}'>
                                    <label for='player_last_{$player_id}'>Last Name:</label>
                                    <input id='player_last_{$player_id}' type='text' name='player_last' value='{$player_last}' autocomplete='off' {$required}>
                                </div>
                            </div>
                            <div class='div-multi-input-containers grid-2-columns'>
                                <div class='div-input-container'>
                                    <label for='player_league_name_{$player_id}'>League:</label>
                                    <input id='player_league_name_{$player_id}' type='text' name='league_name' value='{$league_name}' readonly>
                                </div>
                                <div class='div-input-container {$required_container}'>
                                    <label for='player_dob_{$player_id}'>Date of Birth:</label>
                                    <input id='player_dob_{$player_id}' type='date' name='player_dob' value='{$player_dob}' autocomplete='off' {$required}>
                                </div>
                            </div>
                            <div class='div-multi-input-containers grid-2-columns'>
                                <div class='div-input-container {$required_container}'>
                                    <label for='player_position_id_{$player_id}'>Position ID:</label>
                                    <input id='player_position_id_{$player_id}' type='number' name='player_position_id' min='0' value='{$position_id}' autocomplete='off' {$required}>
                                </div>
                                <div class='div-input-container {$required_container}'>
                                    <label for='player_jersey_{$player_id}'>Jersey:</label>
                                    <input id='player_jersey_{$player_id}' type='number' name='player_jersey' min='0' value='{$player_jersey}' autocomplete='off' {$required}>
                                </div>
                            </div>
                            {$submit_btns}
                        </form>
                    </div>
                ";
            }
        }

        $add_btn = '';
        if ($role_id !== 5) {
            $add_btn = ReusableTemplate::generatePopupAddBtn(1);
        }

        $scroll_container .= "
            {$add_btn}
            </div>
        ";

        return $scroll_container;
    }

    public static function generateStaffData($data, $role_id, $league_name)
    {
        [
            'is_empty' => $is_empty,
            'css_rule' => $rule_flex_center
        ] = self::isDataAvailable($data);

        $scroll_container = "
            <div class='div-scroll-container staff-scroll {$rule_flex_center}'>
        ";

        if ($is_empty) {
            $scroll_container .= ReusableTemplate::generateNoneAvailableDiv('member', 'team');
        } else {
            foreach ($data as $staff) {
                // Related staff staff.
                $staff_id = $staff['user_id'];
                $username = $staff['username'];
                $staff_role_id = $staff['role_id'];
                $staff_role_name = $staff['role_name'];
                $staff_role_option = "{$staff_role_id}|{$staff_role_name}";
                $league_id = $staff['league_id'];
                $team_id = $staff['team_id'];

                $options = '';
                foreach (TeamData::STAFF_SELECT_OPTIONS as $key => $value) {
                    $selected = $staff_role_option === $value ? 'selected' : '';
                    $options .= "<option value='{$value}' {$selected}>{$key}</option>";
                }

                $submit_btns = '';
                if ($role_id !== 5) {
                    $submit_btns = ReusableTemplate::generateFormSubmitBtns('STAFF');
                }

                // Prevent the fan from changing input data.
                $select = $role_id < 5 ? 'required' : 'disabled';
                $required_container = $role_id < 5 ? 'required-container' : '';
                $required = $role_id < 5 ? 'required' : 'readonly';

                $scroll_container .= "
                <div class='div-row-container staff-row-container-{$staff_id}' data-row-id='{$staff_id}'>
                    <ul class='row-header-list custom-auto-3-column-grid'>
                        <li class='row-header-list-item'>
                            <p class='staff-name'>{$username}</p>
                        </li>
                        <li class='row-header-list-item'>
                            <p class='staff-role'>{$staff_role_name}</p>
                        </li>
                        <li class='row-header-list-item'>
                            <p>
                                <ion-icon class='open-icon icon-{$staff_id}' name='chevron-down-outline'></ion-icon>
                            </p>
                        </li>
                    </ul>
                    <form class='form form-info form-{$staff_id} hide-element' action='../process/process-team.php'>
                        <input type='hidden' name='staff_id' value='{$staff_id}'>
                        <input type='hidden' name='league_id' value='{$league_id}'>
                        <div class='div-multi-input-containers grid-2-columns'>
                            <div class='div-input-container {$required_container}'>
                                <label for='staff_username_{$staff_id}'>Username:</label>
                                <input id='staff_username_{$staff_id}' type='text' name='username' value='{$username}' autocomplete='off' {$required}>
                            </div>
                            <div class='div-input-container {$required_container}'>
                                <label for='staff_role_name_{$staff_id}'>Role:</label>
                                <select id='staff_role_name_{$staff_id}' name='role_name' autocomplete='off' {$select}>
                                    <option value=''>Select Role</option>
                                    {$options}
                                </select>
                            </div>
                        </div>
                        <div class='div-multi-input-containers custom-2-column-grid'>
                            <div class='div-input-container'>
                                <label for='staff_league_name_{$staff_id}'>League:</label>
                                <input id='staff_league_name_{$staff_id}' type='text' name='league_name' value='{$league_name}' readonly>
                            </div>
                            <div class='div-input-container'>
                                <label for='staff_team_id_{$staff_id}'>Team ID:</label>
                                <input id='staff_team_id_{$staff_id}' type='number' name='team_id' value='{$team_id}' readonly>
                            </div>
                        </div>
                        {$submit_btns}
                    </form>
                </div>
            ";
            }
        }

        $add_btn = '';
        if ($role_id !== 5) {
            $add_btn = ReusableTemplate::generatePopupAddBtn(2);
        }

        $scroll_container .= "
            {$add_btn}
            </div>
        ";

        return $scroll_container;
    }
}
