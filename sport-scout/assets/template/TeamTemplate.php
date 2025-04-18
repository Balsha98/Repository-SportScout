<?php declare(strict_types=1);

require_once 'ReusableTemplate.php';
require_once 'assets/data/TeamData.php';

class TeamTemplate
{
    public static function generatePopups($db, $teamData)
    {
        [['sport_id' => $sportID]] = $teamData;
        [['league_id' => $leagueID]] = $teamData;
        [['league_name' => $leagueName]] = $teamData;
        [['team_id' => $teamID]] = $teamData;

        $return = '';
        foreach (TeamData::POPUPS as $index => $popup) {
            if ($index === 0) {
                $options = '';
                foreach ($db->getDistinctRows('position') as $value) {
                    $optionValue = "{$value['position_id']}|{$value['position_name']}";

                    if ($value['sport_id'] === $sportID) {
                        $options .= "<option value='{$optionValue}'>{$value['position_name']}</option>";
                    }
                }

                $return .= sprintf(
                    $popup,
                    $sportID,
                    $leagueName,
                    $teamID,
                    $options
                );

                continue;
            }

            $return .= sprintf(
                $popup,
                $leagueID,
                $teamID,
                $leagueName
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

    public static function generatePlayersData($db, $data, $roleID, $leagueName)
    {
        [
            'is_empty' => $isEmpty,
            'css_rule' => $ruleFlexCenter
        ] = self::isDataAvailable($data);

        $scrollContainer = "
            <div class='div-scroll-container players-scroll {$ruleFlexCenter}'>
        ";

        if ($isEmpty) {
            $scrollContainer .= ReusableTemplate::generateNoneAvailableDiv('player', 'team');;
        } else {
            foreach ($data as $player) {
                $playerID = $player['player_id'];
                $playerFirstName = $player['player_first'];
                $playerLastName = $player['player_last'];
                $fullName = "{$playerFirstName} {$playerLastName}";
                $positionID = $player['position_id'];
                $positionName = $player['position_name'];
                $playerDOB = $player['player_dob'];
                $playerJersey = $player['player_jersey'];
                $sportID = $player['sport_id'];
                $teamID = $player['team_id'];

                $options = '';
                foreach ($db->getDistinctRows('position') as $value) {
                    $currID = $value['position_id'];
                    $currName = $value['position_name'];
                    $currValue = "{$currID}|{$currName}";

                    if ($value['sport_id'] === $sportID) {
                        $selected = "{$positionID}|{$positionName}" === $currValue ? 'selected' : '';
                        $options .= "<option value='{$currValue}' {$selected}>{$currName}</option>";
                    }
                }

                $submitBtns = '';
                if ($roleID !== 5) {
                    $submitBtns = ReusableTemplate::generateFormSubmitBtns('player');
                }

                // Prevent the fan from changing input data.
                $requiredContainer = $roleID < 5 ? 'required-container' : '';
                $required = $roleID < 5 ? 'required' : 'readonly';

                $scrollContainer .= "
                    <div class='div-row-container player-row-container-{$playerID}' data-row-id='{$playerID}'>
                        <ul class='row-header-list custom-auto-3-column-grid'>
                            <li class='row-header-list-item'>
                                <p class='full-name'>{$fullName}</p>
                            </li>
                            <li class='row-header-list-item'>
                                <p class='position-name'>{$positionName}</p>
                            </li>
                            <li class='row-header-list-item'>
                                <p>
                                    <ion-icon class='open-icon icon-{$playerID}' name='chevron-down-outline'></ion-icon>
                                </p>
                            </li>
                        </ul>
                        <form 
                            class='form form-info form-{$playerID} hide-element' 
                            action='" . SERVER . "/api/team.php'
                        >
                            <input id='player_sport_id_{$playerID}' type='hidden' name='sport_id' value='{$sportID}'>
                            <input id='player_team_id_{$playerID}' type='hidden' name='team_id' value='{$teamID}'>
                            <input id='player_id_{$playerID}' type='hidden' name='player_id' value='{$playerID}'>
                            <div class='div-multi-input-containers grid-2-columns'>
                                <div class='div-input-container {$requiredContainer}'>
                                    <label for='player_first_{$playerID}'>First Name:</label>
                                    <input id='player_first_{$playerID}' type='text' name='first' value='{$playerFirstName}' autocomplete='off' {$required}>
                                </div>
                                <div class='div-input-container {$requiredContainer}'>
                                    <label for='player_last_{$playerID}'>Last Name:</label>
                                    <input id='player_last_{$playerID}' type='text' name='last' value='{$playerLastName}' autocomplete='off' {$required}>
                                </div>
                            </div>
                            <div class='div-multi-input-containers grid-2-columns'>
                                <div class='div-input-container'>
                                    <label for='player_league_name_{$playerID}'>League:</label>
                                    <input id='player_league_name_{$playerID}' type='text' name='league_name' value='{$leagueName}' readonly>
                                </div>
                                <div class='div-input-container {$requiredContainer}'>
                                    <label for='player_dob_{$playerID}'>Date of Birth:</label>
                                    <input id='player_dob_{$playerID}' type='date' name='dob' value='{$playerDOB}' autocomplete='off' {$required}>
                                </div>
                            </div>
                            <div class='div-multi-input-containers grid-2-columns'>
                                <div class='div-input-container {$requiredContainer}'>
                                    <label for='player_position_id_{$playerID}'>Position:</label>
                                    <select id='player_position_id_{$playerID}' name='position_id' autocomplete='off' required>
                                        <option value=''>Select Position</option>
                                        {$options}
                                    </select>
                                </div>
                                <div class='div-input-container {$requiredContainer}'>
                                    <label for='player_jersey_number_{$playerID}'>Jersey:</label>
                                    <input id='player_jersey_number_{$playerID}' type='number' name='jersey_number' min='0' value='{$playerJersey}' autocomplete='off' {$required}>
                                </div>
                            </div>
                            {$submitBtns}
                        </form>
                    </div>
                ";
            }
        }

        $addBtn = '';
        if ($roleID !== 5) {
            $addBtn = ReusableTemplate::generatePopupAddBtn(1);
        }

        $scrollContainer .= "
            {$addBtn}
            </div>
        ";

        return $scrollContainer;
    }

    public static function generateStaffData($data, $roleID, $leagueName)
    {
        [
            'is_empty' => $isEmpty,
            'css_rule' => $ruleFlexCenter
        ] = self::isDataAvailable($data);

        $scrollContainer = "
            <div class='div-scroll-container staff-scroll {$ruleFlexCenter}'>
        ";

        if ($isEmpty) {
            $scrollContainer .= ReusableTemplate::generateNoneAvailableDiv('member', 'team');
        } else {
            foreach ($data as $staff) {
                $staffID = $staff['user_id'];
                $username = $staff['username'];
                $staffRoleID = $staff['role_id'];
                $staffRoleName = $staff['role_name'];
                $staffRoleOption = "{$staffRoleID}|{$staffRoleName}";
                $leagueID = $staff['league_id'];
                $teamID = $staff['team_id'];

                $options = '';
                foreach (TeamData::STAFF_SELECT_OPTIONS as $key => $value) {
                    $selected = $staffRoleOption === $value ? 'selected' : '';
                    $options .= "<option value='{$value}' {$selected}>{$key}</option>";
                }

                $submitBtns = '';
                if ($roleID !== 5) {
                    $submitBtns = ReusableTemplate::generateFormSubmitBtns('user');
                }

                // Prevent the fan from changing input data.
                $select = $roleID < 5 ? 'required' : 'disabled';
                $requiredContainer = $roleID < 5 ? 'required-container' : '';
                $required = $roleID < 5 ? 'required' : 'readonly';

                $scrollContainer .= "
                    <div class='div-row-container staff-row-container-{$staffID}' data-row-id='{$staffID}'>
                        <ul class='row-header-list custom-auto-3-column-grid'>
                            <li class='row-header-list-item'>
                                <p class='staff-name'>{$username}</p>
                            </li>
                            <li class='row-header-list-item'>
                                <p class='staff-role'>{$staffRoleName}</p>
                            </li>
                            <li class='row-header-list-item'>
                                <p>
                                    <ion-icon class='open-icon icon-{$staffID}' name='chevron-down-outline'></ion-icon>
                                </p>
                            </li>
                        </ul>
                        <form 
                            class='form form-info form-{$staffID} hide-element' 
                            action='" . SERVER . "/api/team.php'
                        >
                            <input id='staff_id_{$staffID}' type='hidden' name='staff_id' value='{$staffID}'>
                            <input id='staff_league_id_{$staffID}' type='hidden' name='league_id' value='{$leagueID}'>
                            <div class='div-multi-input-containers grid-2-columns'>
                                <div class='div-input-container {$requiredContainer}'>
                                    <label for='staff_username_{$staffID}'>Username:</label>
                                    <input id='staff_username_{$staffID}' type='text' name='username' value='{$username}' autocomplete='off' {$required}>
                                </div>
                                <div class='div-input-container {$requiredContainer}'>
                                    <label for='staff_role_name_{$staffID}'>Role:</label>
                                    <select id='staff_role_name_{$staffID}' name='role_name' autocomplete='off' {$select}>
                                        <option value=''>Select Role</option>
                                        {$options}
                                    </select>
                                </div>
                            </div>
                            <div class='div-multi-input-containers custom-2-column-grid'>
                                <div class='div-input-container'>
                                    <label for='staff_league_name_{$staffID}'>League:</label>
                                    <input id='staff_league_name_{$staffID}' type='text' name='league_name' value='{$leagueName}' readonly>
                                </div>
                                <div class='div-input-container'>
                                    <label for='staff_team_id_{$staffID}'>Team ID:</label>
                                    <input id='staff_team_id_{$staffID}' type='number' name='team_id' value='{$teamID}' readonly>
                                </div>
                            </div>
                            {$submitBtns}
                        </form>
                    </div>
                ";
            }
        }

        $addBtn = '';
        if ($roleID !== 5) {
            $addBtn = ReusableTemplate::generatePopupAddBtn(2);
        }

        $scrollContainer .= "
            {$addBtn}
            </div>
        ";

        return $scrollContainer;
    }
}
