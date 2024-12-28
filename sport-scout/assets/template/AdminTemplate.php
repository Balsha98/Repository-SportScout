<?php declare(strict_types=1);

require_once 'ReusableTemplate.php';
require_once 'assets/data/AdminData.php';

class AdminTemplate
{
    private static function isDataAvailable($data)
    {
        $empty_counter = 0;
        foreach ($data as $array) {
            if (count($array) === 0) {
                $empty_counter++;
            }
        }

        $is_empty = (bool) (count($data) === $empty_counter);
        $rule_flex_center = $is_empty ? 'flex-center' : '';

        return [
            'is_empty' => $is_empty,
            'css_rule' => $rule_flex_center
        ];
    }

    static function generatePopups($role_id)
    {
        $needed_index = self::getArrayIndexByRoleId($role_id);
        $related_popups = array_slice(AdminData::POPUPS, $needed_index);

        $return = '';
        foreach ($related_popups as $popup) {
            $return .= sprintf($popup);
        }

        return $return;
    }

    static function generateLinks($role_id)
    {
        $return = '';

        $needed_index = self::getArrayIndexByRoleId($role_id);
        $sidebar_links = array_slice(AdminData::SIDEBAR_LINKS, $needed_index);

        foreach ($sidebar_links as $array)
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
    static function getArrayIndexByRoleId($role_id)
    {
        $needed_index = 0;
        switch ($role_id) {
            case 2:
                $needed_index = $role_id;
                break;
            case 3:
            case 4:
                $needed_index = 4;
                break;
            default:
                $needed_index = 0;
        }

        return $needed_index;
    }

    static function generateUsersDataContainer($data)
    {
        [
            'is_empty' => $is_empty,
            'css_rule' => $rule_flex_center
        ] = self::isDataAvailable($data);

        $data_container = "
            <div class='div-data-container hide-element' data-container-index='1'>
                <header class='data-container-header grid-4-columns'>
                    <p>ID</p>
                    <p>Username</p>
                    <p>Role</p>
                    <p>Details</p>
                </header>
                <div class='div-scroll-container users-scroll {$rule_flex_center}'>
        ";

        if ($is_empty) {
            $data_container .= ReusableTemplate::generateNoneAvailableDiv('user', 'list');
        } else {
            foreach ($data as $user) {
                $user_id = $user['user_id'];
                $username = $user['username'];
                $role_id = $user['role_id'];
                $role_name = $user['role_name'];
                $role_option = "{$role_id}|{$role_name}";
                $league_id = $user['league_id'];
                $league_name = $user['league_name'];
                $team_id = $user['team_id'];
                $team_name = $user['team_name'];

                $options = '';
                foreach (AdminData::USER_SELECT_OPTIONS as $key => $value) {
                    $selected = $role_option === $value ? 'selected' : '';
                    $options .= "<option value='{$value}' {$selected}>{$key}</option>";
                }

                $submit_btns = ReusableTemplate::generateFormSubmitBtns('USER');

                $data_container .= "
                    <div class='div-row-container user-row-container-{$user_id}' data-row-id='{$user_id}'>
                        <ul class='row-header-list grid-4-columns'>
                            <li class='row-header-list-item'>
                                <p>{$user_id}</p>
                            </li>
                            <li class='row-header-list-item'>
                                <p class='username'>{$username}</p>
                            </li>
                            <li class='row-header-list-item'>
                                <p class='role-name' data-old-role-id='{$role_id}'>{$role_name}</p>
                            </li>
                            <li class='row-header-list-item'>
                                <p>
                                    <ion-icon class='open-icon icon-{$user_id}' name='chevron-down-outline'></ion-icon>
                                </p>
                            </li>
                        </ul>
                        <form class='form form-info form-{$user_id} hide-element' action='../process/process-admin.php'>
                            <input type='hidden' name='user_id' value='{$user_id}'>
                            <div class='div-multi-input-containers grid-2-columns'>
                                <div class='div-input-container required-container'>
                                    <label for='username_{$user_id}'>Username:</label>
                                    <input id='username_{$user_id}' type='text' name='username' value='{$username}' autocomplete='off' required>
                                </div>
                                <div class='div-input-container required-container'>
                                    <label for='user_role_name_{$user_id}'>Role Type:</label>
                                    <select id='user_role_name_{$user_id}' name='role_name' autocomplete='off' required>
                                        <option value=''>Select Role</option>
                                        {$options}
                                    </select>
                                </div>
                            </div>
                            <div class='div-multi-input-containers grid-4-columns'>
                                <div class='div-input-container'>
                                    <label for='user_league_name_{$user_id}'>League:</label>
                                    <input id='user_league_name_{$user_id}' type='text' name='league_name' value='{$league_name}' autocomplete='off' readonly>
                                </div>
                                <div class='div-input-container required-container'>
                                    <label for='user_league_id_{$user_id}'>League ID:</label>
                                    <input id='user_league_id_{$user_id}' type='number' name='league_id' value='{$league_id}' min='0' autocomplete='off' required>
                                </div>
                                <div class='div-input-container'>
                                    <label for='user_team_name_{$user_id}'>Team:</label>
                                    <input id='user_team_name_{$user_id}' type='text' name='team_name' value='{$team_name}' autocomplete='off' readonly>
                                </div>
                                <div class='div-input-container required-container'>
                                    <label for='user_team_id_{$user_id}'>Team ID:</label>
                                    <input id='user_team_id_{$user_id}' type='number' name='team_id' value='{$team_id}' min='0' autocomplete='off' required>
                                </div>
                            </div>
                            {$submit_btns}
                        </form>
                    </div>
                ";
            }
        }

        $add_btn = ReusableTemplate::generatePopupAddBtn(1);

        $data_container .= "
                </div>
                {$add_btn}
            </div>
        ";

        return $data_container;
    }

    static function generateSportsDataContainer($data)
    {
        [
            'is_empty' => $is_empty,
            'css_rule' => $rule_flex_center
        ] = self::isDataAvailable($data);

        $data_container = "
            <div class='div-data-container hide-element' data-container-index='2'>
                <header class='data-container-header grid-3-columns'>
                    <p>ID</p>
                    <p>Sport</p>
                    <p>Details</p>
                </header>
                <div class='div-scroll-container sports-scroll {$rule_flex_center}'>
        ";

        if ($is_empty) {
            $data_container .= ReusableTemplate::generateNoneAvailableDiv('sport', 'list');
        } else {
            foreach ($data as $sport) {
                $sport_id = $sport['sport_id'];
                $sport_name = $sport['sport_name'];

                $submit_btns = ReusableTemplate::generateFormSubmitBtns('SPORT');

                $data_container .= "
                    <div class='div-row-container sport-row-container-{$sport_id}' data-row-id='{$sport_id}'>
                        <ul class='row-header-list grid-3-columns'>
                            <li class='row-header-list-item'>
                                <p>{$sport_id}</p>
                            </li>
                            <li class='row-header-list-item'>
                                <p class='sport-name'>{$sport_name}</p>
                            </li>
                            <li class='row-header-list-item'>
                                <p>
                                    <ion-icon class='open-icon icon-{$sport_id}' name='chevron-down-outline'></ion-icon>
                                </p>
                            </li>
                        </ul>
                        <form class='form form-info form-{$sport_id} hide-element' action='../process/process-admin.php'>
                            <div class='div-multi-input-containers custom-2-column-grid'>
                                <div class='div-input-container required-container'>
                                    <label for='sport_name_{$sport_id}'>Sport Name:</label>
                                    <input id='sport_name_{$sport_id}' type='text' name='sport_name' value='{$sport_name}' autocomplete='off' required>
                                </div>
                                <div class='div-input-container'>
                                    <label for='sport_id_{$sport_id}'>Sport ID:</label>
                                    <input id='sport_id_{$sport_id}' type='text' name='sport_id' value='{$sport_id}' readonly>
                                </div>
                            </div>
                            {$submit_btns}
                        </form>
                    </div>
                ";
            }
        }

        $add_btn = ReusableTemplate::generatePopupAddBtn(2);

        $data_container .= "
                </div>
                {$add_btn}
            </div>
        ";

        return $data_container;
    }

    static function generateLeaguesDataContainer($data, $role_id)
    {
        [
            'is_empty' => $is_empty,
            'css_rule' => $rule_flex_center
        ] = self::isDataAvailable($data);

        $data_container = "
            <div class='div-data-container hide-element' data-container-index='3'>
                <header class='data-container-header grid-3-columns'>
                    <p>ID</p>
                    <p>League</p>
                    <p>Details</p>
                </header>
                <div class='div-scroll-container leagues-scroll {$rule_flex_center}'>
        ";

        if ($is_empty) {
            $data_container .= ReusableTemplate::generateNoneAvailableDiv('league', 'list');;
        } else {
            foreach ($data as $league) {
                $league_id = $league['league_id'];
                $league_name = $league['league_name'];
                $sport_id = $league['sport_id'];
                $sport_name = $league['sport_name'];

                $submit_btns = ReusableTemplate::generateFormSubmitBtns('LEAGUE');

                $data_container .= "
                    <div class='div-row-container league-row-container-{$league_id}' data-row-id='{$league_id}'>
                        <ul class='row-header-list grid-3-columns'>
                            <li class='row-header-list-item'>
                                <p>{$league_id}</p>
                            </li>
                            <li class='row-header-list-item'>
                                <p class='league-name'>{$league_name}</p>
                            </li>
                            <li class='row-header-list-item'>
                                <p>
                                    <ion-icon class='open-icon icon-{$league_id}' name='chevron-down-outline'></ion-icon>
                                </p>
                            </li>
                        </ul>
                        <form class='form form-info form-{$league_id} hide-element' action='../process/process-admin.php'>
                            <input type='hidden' name='league_id' value='{$league_id}'>
                            <div class='div-multi-input-containers grid-3-columns'>
                                <div class='div-input-container required-container'>
                                    <label for='league_name_{$league_id}'>League Name:</label>
                                    <input id='league_name_{$league_id}' type='text' name='league_name' value='{$league_name}' autocomplete='off' required>
                                </div>
                                <div class='div-input-container'>
                                    <label for='league_sport_name_{$league_id}'>Sport:</label>
                                    <input id='league_sport_name_{$league_id}' type='text' name='sport_name' value='{$sport_name}' autocomplete='off' readonly>
                                </div>
                                <div class='div-input-container required-container'>
                                    <label for='league_sport_id_{$league_id}'>Sport ID:</label>
                                    <input id='league_sport_id_{$league_id}' type='number' name='sport_id' value='{$sport_id}' min='1' autocomplete='off' required>
                                </div>
                            </div>
                            {$submit_btns}
                        </form>
                    </div>
                ";
            }
        }

        $add_btn = '';
        if ($role_id <= 1) {
            $add_btn = ReusableTemplate::generatePopupAddBtn(3);
        }

        $data_container .= "
                </div>
                {$add_btn}
            </div>
        ";

        return $data_container;
    }

    static function generateSeasonsDataContainer($data, $role_id)
    {
        [
            'is_empty' => $is_empty,
            'css_rule' => $rule_flex_center
        ] = self::isDataAvailable($data);

        $data_container = "
            <div class='div-data-container hide-element' data-container-index='4'>
                <header class='data-container-header grid-4-columns'>
                    <p>ID</p>
                    <p>Year</p>
                    <p>Description</p>
                    <p>Details</p>
                </header>
                <div class='div-scroll-container seasons-scroll {$rule_flex_center}'>
        ";

        if ($is_empty) {
            $data_container .= ReusableTemplate::generateNoneAvailableDiv('season', 'list');;
        } else {
            foreach ($data as $season) {
                $season_id = $season['season_id'];
                $season_year = $season['season_year'];
                $season_desc = $season['season_desc'];
                $sport_id = $season['sport_id'];
                $sport_name = $season['sport_name'];
                $league_id = $season['league_id'];
                $league_name = $season['league_name'];

                $submit_btns = ReusableTemplate::generateFormSubmitBtns('SEASON');

                $data_container .= "
                    <div class='div-row-container season-row-container-{$season_id}' data-row-id='{$season_id}'>
                        <ul class='row-header-list grid-4-columns'>
                            <li class='row-header-list-item'>
                                <p>{$season_id}</p>
                            </li>
                            <li class='row-header-list-item'>
                                <p class='season-year'>{$season_year}</p>
                            </li>
                            <li class='row-header-list-item'>
                                <p class='season-desc'>{$season_desc}</p>
                            </li>
                            <li class='row-header-list-item'>
                                <p>
                                    <ion-icon class='open-icon icon-{$season_id}' name='chevron-down-outline'></ion-icon>
                                </p>
                            </li>
                        </ul>
                        <form class='form form-info form-{$season_id} hide-element' action='../process/process-admin.php'>
                            <input type='hidden' name='season_id' value='{$season_id}'>
                            <div class='div-multi-input-containers grid-2-columns'>
                                <div class='div-input-container required-container'>
                                    <label for='season_year_{$season_id}'>Season Year:</label>
                                    <input id='season_year_{$season_id}' type='text' name='season_year' value='{$season_year}' autocomplete='off' required>
                                </div>
                                <div class='div-input-container required-container'>
                                    <label for='season_desc_{$season_id}'>Season Description:</label>
                                    <input id='season_desc_{$season_id}' type='text' name='season_desc' value='{$season_desc}' autocomplete='off' required>
                                </div>
                            </div>
                            <div class='div-multi-input-containers grid-4-columns'>
                                <div class='div-input-container'>
                                    <label for='season_sport_name_{$season_id}'>Sport:</label>
                                    <input id='season_sport_name_{$season_id}' type='text' name='sport_name' value='{$sport_name}' readonly>
                                </div>
                                <div class='div-input-container required-container'>
                                    <label for='season_sport_id_{$season_id}'>Sport ID:</label>
                                    <input id='season_sport_id_{$season_id}' type='number' name='sport_id' value='{$sport_id}' min='1' required>
                                </div>
                                <div class='div-input-container'>
                                    <label for='season_league_name_{$season_id}'>League:</label>
                                    <input id='season_league_name_{$season_id}' type='text' name='league_name' value='{$league_name}' autocomplete='off' readonly>
                                </div>
                                <div class='div-input-container required-container'>
                                    <label for='season_league_id_{$season_id}'>League ID:</label>
                                    <input id='season_league_id_{$season_id}' type='number' name='league_id' value='{$league_id}' min='1' autocomplete='off' required>
                                </div>
                            </div>
                            {$submit_btns}
                        </form>
                    </div>
                ";
            }
        }

        $add_btn = '';
        if ($role_id <= 2) {
            $add_btn = ReusableTemplate::generatePopupAddBtn(4);
        }

        $data_container .= "
                </div>
                {$add_btn}
            </div>
        ";

        return $data_container;
    }

    static function generateTeamsDataContainer($data, $role_id)
    {
        [
            'is_empty' => $is_empty,
            'css_rule' => $rule_flex_center
        ] = self::isDataAvailable($data);

        $data_container = "
            <div class='div-data-container hide-element' data-container-index='5'>
                <header class='data-container-header grid-5-columns'>
                    <p>ID</p>
                    <p>Team</p>
                    <p>League</p>
                    <p>Season</p>
                    <p>Details</p>
                </header>
                <div class='div-scroll-container teams-scroll {$rule_flex_center}'>
        ";

        if ($is_empty) {
            $data_container .= ReusableTemplate::generateNoneAvailableDiv('team', 'list');;
        } else {
            foreach ($data as $team) {
                $team_id = $team['team_id'];
                $team_name = $team['team_name'];
                $sport_id = $team['sport_id'];
                $sport_name = $team['sport_name'];
                $league_id = $team['league_id'];
                $league_name = $team['league_name'];
                $season_id = $team['season_id'];
                $season_year = $team['season_year'];
                $max_players = $team['max_players'];
                $home_color = $team['home_color'];
                $away_color = $team['away_color'];

                $submit_btns = '';
                if ($role_id <= 2) {
                    $submit_btns = ReusableTemplate::generateFormSubmitBtns('TEAM');
                }

                // Prevent the coaches from changing input data.
                $span_across = $role_id <= 2 ? 'grid-2-columns' : 'span-across-all';
                $required_container = $role_id <= 2 ? 'required-container' : '';
                $required = $role_id <= 2 ? 'required' : 'readonly';

                $data_container .= "
                    <div class='div-row-container team-row-container-{$team_id}' data-row-id='{$team_id}'>
                        <ul class='row-header-list grid-5-columns'>
                            <li class='row-header-list-item'>
                                <p>{$team_id}</p>
                            </li>
                            <li class='row-header-list-item'>
                                <p class='team-name'>{$team_name}</p>
                            </li>
                            <li class='row-header-list-item'>
                                <p class='league-name'>{$league_name}</p>
                            </li>
                            <li class='row-header-list-item'>
                                <p class='season-year'>{$season_year}</p>
                            </li>
                            <li class='row-header-list-item'>
                                <p>
                                    <ion-icon class='open-icon icon-{$team_id}' name='chevron-down-outline'></ion-icon>
                                </p>
                            </li>
                        </ul>
                        <form class='form form-info form-{$team_id} hide-element' action='../process/process-admin.php'>
                            <input type='hidden' name='team_id' value='{$team_id}'>
                            <input type='hidden' name='sport_id' value='{$sport_id}'>
                            <div class='div-multi-input-containers custom-2-column-grid'>
                                <div class='div-input-container {$required_container}'>
                                    <label for='team_name_{$team_id}'>Team Name:</label>
                                    <input id='team_name_{$team_id}' type='text' name='team_name' value='{$team_name}' autocomplete='off' {$required}>
                                </div>
                                <div class='div-input-container'>
                                    <label for='team_id_{$team_id}'>Team ID:</label>
                                    <input id='team_id_{$team_id}' type='number' name='team_id' value='{$team_id}' readonly>
                                </div>
                            </div>
                            <div class='div-multi-input-containers grid-4-columns'>
                                <div class='div-input-container'>
                                    <label for='team_sport_name_{$team_id}'>Sport:</label>
                                    <input id='team_sport_name_{$team_id}' type='text' name='sport_name' value='{$sport_name}' readonly>
                                </div>
                                <div class='div-input-container {$required_container}'>
                                    <label for='team_league_id_{$team_id}'>League ID:</label>
                                    <input id='team_league_id_{$team_id}' type='number' name='league_id' min='1' value='{$league_id}' autocomplete='off' {$required}>
                                </div>
                                <div class='div-input-container {$required_container}'>
                                    <label for='team_season_id_{$team_id}'>Season ID:</label>
                                    <input id='team_season_id_{$team_id}' type='number' name='season_id' min='1' value='{$season_id}' autocomplete='off' {$required}>
                                </div>
                                <div class='div-input-container {$required_container}'>
                                    <label for='team_max_players_{$team_id}'>Max Players:</label>
                                    <input id='team_max_players_{$team_id}' type='number' name='team_max_players' min='1' max='{$max_players}' value='{$max_players}' autocomplete='off' {$required}>
                                </div>
                            </div>
                            <div class='div-multi-input-containers grid-2-columns'>
                                <div class='div-input-container {$required_container}'>
                                    <label for='team_home_color_{$team_id}'>Home Colors:</label>
                                    <input id='team_home_color_{$team_id}' type='text' name='team_home_color' value='{$home_color}' autocomplete='off' {$required}>
                                </div>
                                <div class='div-input-container {$required_container}'>
                                    <label for='team_away_color_{$team_id}'>Away Colors:</label>
                                    <input id='team_away_color_{$team_id}' type='text' name='team_away_color' value='{$away_color}' autocomplete='off' {$required}>
                                </div>
                            </div>
                            <div class='div-multi-input-containers {$span_across}'>
                                {$submit_btns}
                                <div class='grid-btn-container'>
                                    <a class='btn btn-full btn-view' href='/team/{$team_id}'>View Team</a>
                                    <a class='btn btn-full btn-view' href='/schedule/{$team_id}'>View Schedule</a>
                                </div>
                            </div>
                        </form>
                    </div>
                ";
            }
        }

        $add_btn = '';
        if ($role_id <= 2) {
            $add_btn = ReusableTemplate::generatePopupAddBtn(5);
        }

        $data_container .= "
                </div>
                {$add_btn}
            </div>
        ";

        return $data_container;
    }

    static function generatePositionsDataContainer($data, $role_id)
    {
        [
            'is_empty' => $is_empty,
            'css_rule' => $rule_flex_center
        ] = self::isDataAvailable($data);

        $data_container = "
            <div class='div-data-container hide-element' data-container-index='6'>
                <header class='data-container-header grid-3-columns'>
                    <p>ID</p>
                    <p>Name</p>
                    <p>Details</p>
                </header>
                <div class='div-scroll-container positions-scroll {$rule_flex_center}'>
        ";

        if ($is_empty) {
            $data_container .= ReusableTemplate::generateNoneAvailableDiv('position', 'list');;
        } else {
            foreach ($data as $position) {
                $position_id = $position['position_id'];
                $position_name = $position['position_name'];
                $sport_id = $position['sport_id'];
                $sport_name = $position['sport_name'];

                $submit_btns = '';
                if ($role_id <= 2) {
                    $submit_btns = ReusableTemplate::generateFormSubmitBtns('POSITION');
                }

                // Prevent the coaches from changing input data.
                $required_container = $role_id <= 2 ? 'required-container' : '';
                $required = $role_id <= 2 ? 'required' : 'readonly';

                $data_container .= "
                    <div class='div-row-container position-row-container-{$position_id}' data-row-id='{$position_id}'>
                        <ul class='row-header-list grid-3-columns'>
                            <li class='row-header-list-item'>
                                <p>{$position_id}</p>
                            </li>
                            <li class='row-header-list-item'>
                                <p class='position-name'>{$position_name}</p>
                            </li>
                            <li class='row-header-list-item'>
                                <p>
                                    <ion-icon class='open-icon icon-{$position_id}' name='chevron-down-outline'></ion-icon>
                                </p>
                            </li>
                        </ul>
                        <form class='form form-info form-{$position_id} hide-element' action='../process/process-admin.php'>
                            <input type='hidden' name='position_id' value='{$position_id}'>
                            <div class='div-multi-input-containers grid-3-columns'>
                                <div class='div-input-container {$required_container}'>
                                    <label for='position_name_{$position_id}'>Team Name:</label>
                                    <input id='position_name_{$position_id}' type='text' name='position_name' value='{$position_name}' autocomplete='off' {$required}>
                                </div>
                                <div class='div-input-container'>
                                    <label for='position_sport_name_{$position_id}'>Sport:</label>
                                    <input id='position_sport_name_{$position_id}' type='text' name='sport_name' value='{$sport_name}' readonly>
                                </div>
                                <div class='div-input-container {$required_container}'>
                                    <label for='position_sport_id_{$position_id}'>Sport ID:</label>
                                    <input id='position_sport_id_{$position_id}' type='number' name='sport_id' value='{$sport_id}' min='1' {$required}>
                                </div>
                            </div>
                            {$submit_btns}
                        </form>
                    </div>
                ";
            }
        }

        $add_btn = '';
        if ($role_id < 5) {
            $add_btn = ReusableTemplate::generatePopupAddBtn(6);
        }

        $data_container .= "
                </div>
                {$add_btn}
            </div>
        ";

        return $data_container;
    }
}
