<?php declare(strict_types=1);

require_once 'ReusableTemplate.php';
require_once 'assets/data/AdminData.php';

class AdminTemplate
{
    private static function isDataAvailable($data)
    {
        $emptyCounter = 0;
        foreach ($data as $array) {
            if (count($array) === 0) {
                $emptyCounter++;
            }
        }

        $isEmpty = (bool) (count($data) === $emptyCounter);
        $ruleFlexCenter = $isEmpty ? 'flex-center' : '';

        return [
            'is_empty' => $isEmpty,
            'css_rule' => $ruleFlexCenter
        ];
    }

    private static function generateOptions($array, $column)
    {
        $return = '';
        foreach ($array as $option) {
            $optionID = $option["{$column}_id"];
            $optionName = $option["{$column}_name"];

            $return .= "
                <option value='{$optionID}|{$optionName}'>
                    {$optionName}
                </option>
            ";
        }

        return $return;
    }

    public static function generatePopups($db, $roleID)
    {
        $neededIndex = self::getArrayIndexByRoleId($roleID);
        $relatedPopups = array_slice(AdminData::POPUPS, $neededIndex);
        $distinctData = array_slice(AdminData::DISTINCT_DATA, $neededIndex);
        $currSections = array_slice(AdminData::SIDEBAR_SECTIONS, $neededIndex);

        $return = '';
        foreach ($relatedPopups as $i => $popup) {
            $distinctValue = $distinctData[$i][$currSections[$i]];

            if ($distinctValue === null) {
                $return .= sprintf($popup);
                continue;
            }

            $columns = explode('|', $distinctValue);
            switch (count($columns)) {
                case 1:
                    $return .= sprintf(
                        $popup,
                        self::generateOptions(
                            $db->getDistinctRows($columns[0]),
                            $columns[0]
                        )
                    );
                    break;
                case 2:
                    $return .= sprintf(
                        $popup,
                        self::generateOptions(
                            $db->getDistinctRows($columns[0]),
                            $columns[0]
                        ),
                        self::generateOptions(
                            $db->getDistinctRows($columns[1]),
                            $columns[1]
                        )
                    );
                    break;
                default:
                    // TODO: Implement case for seasons.
            }
        }

        return $return;
    }

    public static function generateLinks($roleID)
    {
        $return = '';

        $neededIndex = self::getArrayIndexByRoleId($roleID);
        $sidebarLinks = array_slice(AdminData::SIDEBAR_LINKS, $neededIndex);

        foreach ($sidebarLinks as $array)
            $return .= "
                <li class='sidebar-nav-list-item'>
                    <button class='btn btn-full btn-sidebar' data-container-index='{$array['container']}'>
                        <span>
                            <ion-icon class='sidebar-nav-icon' name='{$array['icon']}-outline'></ion-icon>
                            <span>{$array['name']}</span>
                        </span>
                        <ion-icon class='open-icon' name='caret-forward-outline'></ion-icon>
                    </button>
                </li>
            ";

        return $return;
    }

    /**
     * Get the right index for the admin sidebar.
     * @param mixed $role-id - user's role id.
     * @return mixed the right array index.
     */
    public static function getArrayIndexByRoleId($roleID)
    {
        $neededIndex = 0;
        switch ($roleID) {
            case 2:
                $neededIndex = $roleID;
                break;
            case 3:
            case 4:
                $neededIndex = 4;
                break;
            default:
                $neededIndex = 0;
        }

        return $neededIndex;
    }

    public static function generateUsersDataContainer($data)
    {
        [
            'is_empty' => $isEmpty,
            'css_rule' => $ruleFlexCenter
        ] = self::isDataAvailable($data);

        $dataContainer = "
            <div class='div-data-container hide-element' data-container-index='1'>
                <header class='data-container-header grid-4-columns'>
                    <p>ID</p>
                    <p>Username</p>
                    <p>Role</p>
                    <p>Details</p>
                </header>
                <div class='div-scroll-container users-scroll {$ruleFlexCenter}'>
        ";

        if ($isEmpty) {
            $dataContainer .= ReusableTemplate::generateNoneAvailableDiv('user', 'list');
        } else {
            foreach ($data as $user) {
                $userID = $user['user_id'];
                $username = $user['username'];
                $roleID = $user['role_id'];
                $roleName = $user['role_name'];
                $roleOption = "{$roleID}|{$roleName}";
                $leagueID = $user['league_id'];
                $leagueName = $user['league_name'];
                $teamID = $user['team_id'];
                $teamName = $user['team_name'];

                $options = '';
                foreach (AdminData::USER_SELECT_OPTIONS as $key => $value) {
                    $selected = $roleOption === $value ? 'selected' : '';
                    $options .= "<option value='{$value}' {$selected}>{$key}</option>";
                }

                $submitBtns = ReusableTemplate::generateFormSubmitBtns('user');

                $dataContainer .= "
                    <div class='div-row-container user-row-container-{$userID}' data-row-id='{$userID}'>
                        <ul class='row-header-list grid-4-columns'>
                            <li class='row-header-list-item'>
                                <p>{$userID}</p>
                            </li>
                            <li class='row-header-list-item'>
                                <p class='username'>{$username}</p>
                            </li>
                            <li class='row-header-list-item'>
                                <p class='role-name' data-old-role-id='{$roleID}'>{$roleName}</p>
                            </li>
                            <li class='row-header-list-item'>
                                <p>
                                    <ion-icon class='open-icon icon-{$userID}' name='chevron-down-outline'></ion-icon>
                                </p>
                            </li>
                        </ul>
                        <form 
                            class='form form-info form-{$userID} hide-element' 
                            action='" . SERVER . "/api/admin.php'
                        >
                            <input id='user_id_{$userID}' type='hidden' name='user_id' value='{$userID}'>
                            <div class='div-multi-input-containers grid-2-columns'>
                                <div class='div-input-container required-container'>
                                    <label for='username_{$userID}'>Username:</label>
                                    <input id='username_{$userID}' type='text' name='username' value='{$username}' autocomplete='off' required>
                                </div>
                                <div class='div-input-container required-container'>
                                    <label for='user_role_name_{$userID}'>Role Type:</label>
                                    <select id='user_role_name_{$userID}' name='role_name' autocomplete='off' required>
                                        <option value=''>Select Role</option>
                                        {$options}
                                    </select>
                                </div>
                            </div>
                            <div class='div-multi-input-containers grid-4-columns'>
                                <div class='div-input-container'>
                                    <label for='user_league_name_{$userID}'>League:</label>
                                    <input id='user_league_name_{$userID}' type='text' name='league_name' value='{$leagueName}' autocomplete='off' readonly>
                                </div>
                                <div class='div-input-container required-container'>
                                    <label for='user_league_id_{$userID}'>League ID:</label>
                                    <input id='user_league_id_{$userID}' type='number' name='league_id' value='{$leagueID}' min='0' autocomplete='off' required>
                                </div>
                                <div class='div-input-container'>
                                    <label for='user_team_name_{$userID}'>Team:</label>
                                    <input id='user_team_name_{$userID}' type='text' name='team_name' value='{$teamName}' autocomplete='off' readonly>
                                </div>
                                <div class='div-input-container required-container'>
                                    <label for='user_team_id_{$userID}'>Team ID:</label>
                                    <input id='user_team_id_{$userID}' type='number' name='team_id' value='{$teamID}' min='0' autocomplete='off' required>
                                </div>
                            </div>
                            {$submitBtns}
                        </form>
                    </div>
                ";
            }
        }

        $addBtn = ReusableTemplate::generatePopupAddBtn(1);

        $dataContainer .= "
                </div>
                {$addBtn}
            </div>
        ";

        return $dataContainer;
    }

    public static function generateSportsDataContainer($data)
    {
        [
            'is_empty' => $isEmpty,
            'css_rule' => $ruleFlexCenter
        ] = self::isDataAvailable($data);

        $dataContainer = "
            <div class='div-data-container hide-element' data-container-index='2'>
                <header class='data-container-header grid-3-columns'>
                    <p>ID</p>
                    <p>Sport</p>
                    <p>Details</p>
                </header>
                <div class='div-scroll-container sports-scroll {$ruleFlexCenter}'>
        ";

        if ($isEmpty) {
            $dataContainer .= ReusableTemplate::generateNoneAvailableDiv('sport', 'list');
        } else {
            foreach ($data as $sport) {
                $sportID = $sport['sport_id'];
                $sportName = $sport['sport_name'];

                $submitBtns = ReusableTemplate::generateFormSubmitBtns('sport');

                $dataContainer .= "
                    <div class='div-row-container sport-row-container-{$sportID}' data-row-id='{$sportID}'>
                        <ul class='row-header-list grid-3-columns'>
                            <li class='row-header-list-item'>
                                <p>{$sportID}</p>
                            </li>
                            <li class='row-header-list-item'>
                                <p class='sport-name'>{$sportName}</p>
                            </li>
                            <li class='row-header-list-item'>
                                <p>
                                    <ion-icon class='open-icon icon-{$sportID}' name='chevron-down-outline'></ion-icon>
                                </p>
                            </li>
                        </ul>
                        <form 
                            class='form form-info form-{$sportID} hide-element' 
                            action='" . SERVER . "/api/admin.php'
                        >
                            <div class='div-multi-input-containers custom-2-column-grid'>
                                <div class='div-input-container required-container'>
                                    <label for='sport_name_{$sportID}'>Sport Name:</label>
                                    <input id='sport_name_{$sportID}' type='text' name='sport_name' value='{$sportName}' autocomplete='off' required>
                                </div>
                                <div class='div-input-container'>
                                    <label for='sport_id_{$sportID}'>Sport ID:</label>
                                    <input id='sport_id_{$sportID}' type='text' name='sport_id' value='{$sportID}' readonly>
                                </div>
                            </div>
                            {$submitBtns}
                        </form>
                    </div>
                ";
            }
        }

        $addBtn = ReusableTemplate::generatePopupAddBtn(2);

        $dataContainer .= "
                </div>
                {$addBtn}
            </div>
        ";

        return $dataContainer;
    }

    public static function generateLeaguesDataContainer($data, $roleID)
    {
        [
            'is_empty' => $isEmpty,
            'css_rule' => $ruleFlexCenter
        ] = self::isDataAvailable($data);

        $dataContainer = "
            <div class='div-data-container hide-element' data-container-index='3'>
                <header class='data-container-header grid-3-columns'>
                    <p>ID</p>
                    <p>League</p>
                    <p>Details</p>
                </header>
                <div class='div-scroll-container leagues-scroll {$ruleFlexCenter}'>
        ";

        if ($isEmpty) {
            $dataContainer .= ReusableTemplate::generateNoneAvailableDiv('league', 'list');;
        } else {
            foreach ($data as $league) {
                $leagueID = $league['league_id'];
                $leagueName = $league['league_name'];
                $sportID = $league['sport_id'];
                $sportName = $league['sport_name'];

                $submitBtns = ReusableTemplate::generateFormSubmitBtns('league');

                $dataContainer .= "
                    <div class='div-row-container league-row-container-{$leagueID}' data-row-id='{$leagueID}'>
                        <ul class='row-header-list grid-3-columns'>
                            <li class='row-header-list-item'>
                                <p>{$leagueID}</p>
                            </li>
                            <li class='row-header-list-item'>
                                <p class='league-name'>{$leagueName}</p>
                            </li>
                            <li class='row-header-list-item'>
                                <p>
                                    <ion-icon class='open-icon icon-{$leagueID}' name='chevron-down-outline'></ion-icon>
                                </p>
                            </li>
                        </ul>
                        <form 
                            class='form form-info form-{$leagueID} hide-element' 
                            action='" . SERVER . "/api/admin.php'
                        >
                            <input id='league_id_{$leagueID}' type='hidden' name='league_id' value='{$leagueID}'>
                            <div class='div-multi-input-containers grid-3-columns'>
                                <div class='div-input-container required-container'>
                                    <label for='league_name_{$leagueID}'>League Name:</label>
                                    <input id='league_name_{$leagueID}' type='text' name='league_name' value='{$leagueName}' autocomplete='off' required>
                                </div>
                                <div class='div-input-container'>
                                    <label for='league_sport_name_{$leagueID}'>Sport:</label>
                                    <input id='league_sport_name_{$leagueID}' type='text' name='sport_name' value='{$sportName}' autocomplete='off' readonly>
                                </div>
                                <div class='div-input-container required-container'>
                                    <label for='league_sport_id_{$leagueID}'>Sport ID:</label>
                                    <input id='league_sport_id_{$leagueID}' type='number' name='sport_id' value='{$sportID}' min='1' autocomplete='off' required>
                                </div>
                            </div>
                            {$submitBtns}
                        </form>
                    </div>
                ";
            }
        }

        $addBtn = '';
        if ($roleID <= 1) {
            $addBtn = ReusableTemplate::generatePopupAddBtn(3);
        }

        $dataContainer .= "
                </div>
                {$addBtn}
            </div>
        ";

        return $dataContainer;
    }

    public static function generateSeasonsDataContainer($data, $roleID)
    {
        [
            'is_empty' => $isEmpty,
            'css_rule' => $ruleFlexCenter
        ] = self::isDataAvailable($data);

        $dataContainer = "
            <div class='div-data-container hide-element' data-container-index='4'>
                <header class='data-container-header grid-4-columns'>
                    <p>ID</p>
                    <p>Year</p>
                    <p>Description</p>
                    <p>Details</p>
                </header>
                <div class='div-scroll-container seasons-scroll {$ruleFlexCenter}'>
        ";

        if ($isEmpty) {
            $dataContainer .= ReusableTemplate::generateNoneAvailableDiv('season', 'list');;
        } else {
            foreach ($data as $season) {
                $seasonID = $season['season_id'];
                $seasonYear = $season['season_year'];
                $seasonDesc = $season['season_desc'];
                $sportID = $season['sport_id'];
                $sportName = $season['sport_name'];
                $leagueID = $season['league_id'];
                $leagueName = $season['league_name'];

                $submitBtns = ReusableTemplate::generateFormSubmitBtns('season');

                $dataContainer .= "
                    <div class='div-row-container season-row-container-{$seasonID}' data-row-id='{$seasonID}'>
                        <ul class='row-header-list grid-4-columns'>
                            <li class='row-header-list-item'>
                                <p>{$seasonID}</p>
                            </li>
                            <li class='row-header-list-item'>
                                <p class='season-year'>{$seasonYear}</p>
                            </li>
                            <li class='row-header-list-item'>
                                <p class='season-desc'>{$seasonDesc}</p>
                            </li>
                            <li class='row-header-list-item'>
                                <p>
                                    <ion-icon class='open-icon icon-{$seasonID}' name='chevron-down-outline'></ion-icon>
                                </p>
                            </li>
                        </ul>
                        <form 
                            class='form form-info form-{$seasonID} hide-element' 
                            action='" . SERVER . "/api/admin.php'
                        >
                            <input id='season_id_{$seasonID}' type='hidden' name='season_id' value='{$seasonID}'>
                            <div class='div-multi-input-containers grid-2-columns'>
                                <div class='div-input-container required-container'>
                                    <label for='season_year_{$seasonID}'>Season Year:</label>
                                    <input id='season_year_{$seasonID}' type='text' name='season_year' value='{$seasonYear}' autocomplete='off' required>
                                </div>
                                <div class='div-input-container required-container'>
                                    <label for='season_desc_{$seasonID}'>Season Description:</label>
                                    <input id='season_desc_{$seasonID}' type='text' name='season_desc' value='{$seasonDesc}' autocomplete='off' required>
                                </div>
                            </div>
                            <div class='div-multi-input-containers grid-4-columns'>
                                <div class='div-input-container'>
                                    <label for='season_sport_name_{$seasonID}'>Sport:</label>
                                    <input id='season_sport_name_{$seasonID}' type='text' name='sport_name' value='{$sportName}' readonly>
                                </div>
                                <div class='div-input-container required-container'>
                                    <label for='season_sport_id_{$seasonID}'>Sport ID:</label>
                                    <input id='season_sport_id_{$seasonID}' type='number' name='sport_id' value='{$sportID}' min='1' required>
                                </div>
                                <div class='div-input-container'>
                                    <label for='season_league_name_{$seasonID}'>League:</label>
                                    <input id='season_league_name_{$seasonID}' type='text' name='league_name' value='{$leagueName}' autocomplete='off' readonly>
                                </div>
                                <div class='div-input-container required-container'>
                                    <label for='season_league_id_{$seasonID}'>League ID:</label>
                                    <input id='season_league_id_{$seasonID}' type='number' name='league_id' value='{$leagueID}' min='1' autocomplete='off' required>
                                </div>
                            </div>
                            {$submitBtns}
                        </form>
                    </div>
                ";
            }
        }

        $addBtn = '';
        if ($roleID <= 2) {
            $addBtn = ReusableTemplate::generatePopupAddBtn(4);
        }

        $dataContainer .= "
                </div>
                {$addBtn}
            </div>
        ";

        return $dataContainer;
    }

    public static function generateTeamsDataContainer($data, $roleID)
    {
        [
            'is_empty' => $isEmpty,
            'css_rule' => $ruleFlexCenter
        ] = self::isDataAvailable($data);

        $dataContainer = "
            <div class='div-data-container hide-element' data-container-index='5'>
                <header class='data-container-header grid-5-columns'>
                    <p>ID</p>
                    <p>Team</p>
                    <p>League</p>
                    <p>Season</p>
                    <p>Details</p>
                </header>
                <div class='div-scroll-container teams-scroll {$ruleFlexCenter}'>
        ";

        if ($isEmpty) {
            $dataContainer .= ReusableTemplate::generateNoneAvailableDiv('team', 'list');;
        } else {
            foreach ($data as $team) {
                $teamID = $team['team_id'];
                $teamName = $team['team_name'];
                $sportID = $team['sport_id'];
                $sportName = $team['sport_name'];
                $leagueID = $team['league_id'];
                $leagueName = $team['league_name'];
                $seasonID = $team['season_id'];
                $seasonYear = $team['season_year'];
                $maxPlayers = $team['max_players'];
                $homeColor = $team['home_color'];
                $awayColor = $team['away_color'];

                $submitBtns = '';
                if ($roleID <= 2) {
                    $submitBtns = ReusableTemplate::generateFormSubmitBtns('team');
                }

                // Prevent the coaches from changing input data.
                $spanAcross = $roleID <= 2 ? 'grid-2-columns' : 'span-across-all';
                $requiredContainer = $roleID <= 2 ? 'required-container' : '';
                $required = $roleID <= 2 ? 'required' : 'readonly';

                $dataContainer .= "
                    <div class='div-row-container team-row-container-{$teamID}' data-row-id='{$teamID}'>
                        <ul class='row-header-list grid-5-columns'>
                            <li class='row-header-list-item'>
                                <p>{$teamID}</p>
                            </li>
                            <li class='row-header-list-item'>
                                <p class='team-name'>{$teamName}</p>
                            </li>
                            <li class='row-header-list-item'>
                                <p class='league-name'>{$leagueName}</p>
                            </li>
                            <li class='row-header-list-item'>
                                <p class='season-year'>{$seasonYear}</p>
                            </li>
                            <li class='row-header-list-item'>
                                <p>
                                    <ion-icon class='open-icon icon-{$teamID}' name='chevron-down-outline'></ion-icon>
                                </p>
                            </li>
                        </ul>
                        <form 
                            class='form form-info form-{$teamID} hide-element' 
                            action='" . SERVER . "/api/admin.php'
                        >
                            <input id='team_sport_id_{$teamID}' type='hidden' name='sport_id' value='{$sportID}'>
                            <div class='div-multi-input-containers custom-2-column-grid'>
                                <div class='div-input-container {$requiredContainer}'>
                                    <label for='team_name_{$teamID}'>Team Name:</label>
                                    <input id='team_name_{$teamID}' type='text' name='team_name' value='{$teamName}' autocomplete='off' {$required}>
                                </div>
                                <div class='div-input-container'>
                                    <label for='team_id_{$teamID}'>Team ID:</label>
                                    <input id='team_id_{$teamID}' type='number' name='team_id' value='{$teamID}' readonly>
                                </div>
                            </div>
                            <div class='div-multi-input-containers grid-4-columns'>
                                <div class='div-input-container'>
                                    <label for='team_sport_name_{$teamID}'>Sport:</label>
                                    <input id='team_sport_name_{$teamID}' type='text' name='sport_name' value='{$sportName}' readonly>
                                </div>
                                <div class='div-input-container {$requiredContainer}'>
                                    <label for='team_league_id_{$teamID}'>League ID:</label>
                                    <input id='team_league_id_{$teamID}' type='number' name='league_id' min='1' value='{$leagueID}' autocomplete='off' {$required}>
                                </div>
                                <div class='div-input-container {$requiredContainer}'>
                                    <label for='team_season_id_{$teamID}'>Season ID:</label>
                                    <input id='team_season_id_{$teamID}' type='number' name='season_id' min='1' value='{$seasonID}' autocomplete='off' {$required}>
                                </div>
                                <div class='div-input-container {$requiredContainer}'>
                                    <label for='team_max_players_{$teamID}'>Max Players:</label>
                                    <input id='team_max_players_{$teamID}' type='number' name='team_max_players' min='1' max='{$maxPlayers}' value='{$maxPlayers}' autocomplete='off' {$required}>
                                </div>
                            </div>
                            <div class='div-multi-input-containers grid-2-columns'>
                                <div class='div-input-container {$requiredContainer}'>
                                    <label for='team_home_color_{$teamID}'>Home Colors:</label>
                                    <input id='team_home_color_{$teamID}' type='text' name='team_home_color' value='{$homeColor}' autocomplete='off' {$required}>
                                </div>
                                <div class='div-input-container {$requiredContainer}'>
                                    <label for='team_away_color_{$teamID}'>Away Colors:</label>
                                    <input id='team_away_color_{$teamID}' type='text' name='team_away_color' value='{$awayColor}' autocomplete='off' {$required}>
                                </div>
                            </div>
                            <div class='div-multi-input-containers {$spanAcross}'>
                                {$submitBtns}
                                <div class='grid-btn-container'>
                                    <a class='btn btn-full btn-view' href='/team/{$teamID}'>View Team</a>
                                    <a class='btn btn-full btn-view' href='/schedule/{$teamID}'>View Schedule</a>
                                </div>
                            </div>
                        </form>
                    </div>
                ";
            }
        }

        $addBtn = '';
        if ($roleID <= 2) {
            $addBtn = ReusableTemplate::generatePopupAddBtn(5);
        }

        $dataContainer .= "
                </div>
                {$addBtn}
            </div>
        ";

        return $dataContainer;
    }

    public static function generatePositionsDataContainer($data, $roleID)
    {
        [
            'is_empty' => $isEmpty,
            'css_rule' => $ruleFlexCenter
        ] = self::isDataAvailable($data);

        $dataContainer = "
            <div class='div-data-container hide-element' data-container-index='6'>
                <header class='data-container-header grid-3-columns'>
                    <p>ID</p>
                    <p>Name</p>
                    <p>Details</p>
                </header>
                <div class='div-scroll-container positions-scroll {$ruleFlexCenter}'>
        ";

        if ($isEmpty) {
            $dataContainer .= ReusableTemplate::generateNoneAvailableDiv('position', 'list');;
        } else {
            foreach ($data as $position) {
                $positionID = $position['position_id'];
                $positionName = $position['position_name'];
                $sportID = $position['sport_id'];
                $sportName = $position['sport_name'];

                $submitBtns = '';
                if ($roleID <= 2) {
                    $submitBtns = ReusableTemplate::generateFormSubmitBtns('position');
                }

                // Prevent the coaches from changing input data.
                $requiredContainer = $roleID <= 2 ? 'required-container' : '';
                $required = $roleID <= 2 ? 'required' : 'readonly';

                $dataContainer .= "
                    <div class='div-row-container position-row-container-{$positionID}' data-row-id='{$positionID}'>
                        <ul class='row-header-list grid-3-columns'>
                            <li class='row-header-list-item'>
                                <p>{$positionID}</p>
                            </li>
                            <li class='row-header-list-item'>
                                <p class='position-name'>{$positionName}</p>
                            </li>
                            <li class='row-header-list-item'>
                                <p>
                                    <ion-icon class='open-icon icon-{$positionID}' name='chevron-down-outline'></ion-icon>
                                </p>
                            </li>
                        </ul>
                        <form 
                            class='form form-info form-{$positionID} hide-element' 
                            action='" . SERVER . "/api/admin.php'
                        >
                            <input id='position_id_{$positionID}' type='hidden' name='position_id' value='{$positionID}'>
                            <div class='div-multi-input-containers grid-3-columns'>
                                <div class='div-input-container {$requiredContainer}'>
                                    <label for='position_name_{$positionID}'>Position Name:</label>
                                    <input id='position_name_{$positionID}' type='text' name='position_name' value='{$positionName}' autocomplete='off' {$required}>
                                </div>
                                <div class='div-input-container'>
                                    <label for='position_sport_name_{$positionID}'>Sport:</label>
                                    <input id='position_sport_name_{$positionID}' type='text' name='sport_name' value='{$sportName}' readonly>
                                </div>
                                <div class='div-input-container {$requiredContainer}'>
                                    <label for='position_sport_id_{$positionID}'>Sport ID:</label>
                                    <input id='position_sport_id_{$positionID}' type='number' name='sport_id' value='{$sportID}' min='1' {$required}>
                                </div>
                            </div>
                            {$submitBtns}
                        </form>
                    </div>
                ";
            }
        }

        $addBtn = '';
        if ($roleID < 5) {
            $addBtn = ReusableTemplate::generatePopupAddBtn(6);
        }

        $dataContainer .= "
                </div>
                {$addBtn}
            </div>
        ";

        return $dataContainer;
    }
}
