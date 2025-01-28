<?php

class AdminData
{
    // ***** ADMIN PAGE USER SELECT OPTIONS ***** //

    public const SIDEBAR_SECTIONS = [
        'users',
        'sports',
        'leagues',
        'seasons',
        'teams',
        'positions'
    ];

    // ***** ADMIN PAGE SIDEBAR LINKS ***** //
    public const SIDEBAR_LINKS = [
        [
            'container' => 1,
            'name' => 'Users',
            'icon' => 'people',
        ],
        [
            'container' => 2,
            'name' => 'Sports',
            'icon' => 'ribbon'
        ],
        [
            'container' => 3,
            'name' => 'Leagues',
            'icon' => 'trophy'
        ],
        [
            'container' => 4,
            'name' => 'Seasons',
            'icon' => 'analytics'
        ],
        [
            'container' => 5,
            'name' => 'Teams',
            'icon' => 'people-circle'
        ],
        [
            'container' => 6,
            'name' => 'Positions',
            'icon' => 'man'
        ]
    ];

    public const DISTINCT_DATA = [
        ['users' => 'league|team'],
        ['sports' => null],
        ['leagues' => 'sport'],
        ['seasons' => 'sport|league'],
        ['teams' => 'sport|league'],  // TODO: Add season as final.
        ['positions' => 'sport']
    ];

    // ***** ADMIN PAGE POPUPS ***** //
    public const POPUPS = [
        '
            <!-- ADD NEW USER POPUP -->
            <div class="popup-add popup-show popup-user hide-element" data-popup-index="1">
                <button class="popup-btn-close">
                    <ion-icon class="close-icon" name="close-outline"></ion-icon>
                </button>
                <header class="popup-add-header">
                    <h2>Add New User?</h2>
                    <p>Fill in the following <span>form</span> appropriately.</p>
                </header>
                <form class="form form-add" action="api/admin.php" method="POST">
                    <div class="div-multi-input-containers grid-2-columns">
                        <div class="div-input-container required-container">
                            <label for="new_username">Username:</label>
                            <input id="new_username" type="text" name="username" autocomplete="off" required>
                        </div>
                        <div class="div-input-container required-container">
                            <label for="new_user_password">Password:</label>
                            <input id="new_user_password" type="password" name="password" autocomplete="off" required>
                        </div>
                    </div>
                    <div class="div-input-container required-container">
                        <label for="new_user_role_name">Type of Role:</label>
                        <select id="new_user_role_name" name="role_name" autocomplete="off" required>
                            <option value="">Select Role</option>
                            <option value="1|Administrator">Administrator</option>
                            <option value="2|League Manager">League Manager</option>
                            <option value="3|Team Manager">Team Manager</option>
                            <option value="4|Team Coach">Team Coach</option>
                            <option value="5|Fan">Fan</option>
                        </select>
                    </div>
                    <div class="div-multi-input-containers grid-2-columns">
                        <div class="div-input-container required-container">
                            <label for="new_user_league_id">League:</label>
                            <select id="new_user_league_id" name="league_id" autocomplete="off" required>
                                <option value="">Select League</option>
                                <option value="0|All">All</option>
                                %s
                            </select>
                        </div>
                        <div class="div-input-container required-container">
                            <label for="new_user_team_id">Team:</label>
                            <select id="new_user_team_id" name="team_id" autocomplete="off" required>
                                <option value="">Select Team</option>
                                <option value="0|All">All</option>
                                <option value="0|All Withing The League">All Within The League</option>
                                %s
                            </select>
                        </div>
                    </div>
                    <div class="div-btn-container grid-btn-container">
                        <button class="btn btn-hollow btn-cancel" type="button">
                            <ion-icon class="arrow-icon" name="arrow-back-outline"></ion-icon>
                            <span>Cancel</span>
                        </button>
                        <button class="btn btn-full btn-add-new" type="submit" data-item-type="user">
                            <span>Add</span>
                        </button>
                    </div>
                </form>
            </div>
        ',
        '
            <!-- ADD NEW SPORT POPUP -->
            <div class="popup-add popup-show popup-sport hide-element" data-popup-index="2">
                <button class="popup-btn-close">
                    <ion-icon class="close-icon" name="close-outline"></ion-icon>
                </button>
                <header class="popup-add-header">
                    <h2>Add New Sport?</h2>
                    <p>Fill in the following <span>fields</span> appropriately.</p>
                </header>
                <form class="form form-add" action="api/admin.php" method="POST">
                    <div class="div-input-container required-container">
                        <label for="new_sport_name">Sport Name:</label>
                        <input id="new_sport_name" type="text" name="sport_name" autocomplete="off" required>
                    </div>
                    <div class="div-btn-container grid-btn-container">
                        <button class="btn btn-hollow btn-cancel" type="button">
                            <ion-icon class="arrow-icon" name="arrow-back-outline"></ion-icon>
                            <span>Cancel</span>
                        </button>
                        <button class="btn btn-full btn-add-new" type="submit" data-item-type="sport">
                            <span>Add</span>
                        </button>
                    </div>
                </form>
            </div>
        ',
        '
            <!-- ADD NEW LEAGUE POPUP -->
            <div class="popup-add popup-show popup-league hide-element" data-popup-index="3">
                <button class="popup-btn-close">
                    <ion-icon class="close-icon" name="close-outline"></ion-icon>
                </button>
                <header class="popup-add-header">
                    <h2>Add New League?</h2>
                    <p>Fill in the following <span>fields</span> appropriately.</p>
                </header>
                <form class="form form-add" action="api/admin.php" method="POST">
                    <div class="div-multi-input-containers grid-2-columns">
                        <div class="div-input-container required-container">
                            <label for="new_league_name">League Name:</label>
                            <input id="new_league_name" type="text" name="league_name" autocomplete="off" required>
                        </div>
                        <div class="div-input-container required-container">
                            <label for="new_league_sport_id">Sport:</label>
                            <select id="new_league_sport_id" name="sport_id" autocomplete="off" required>
                                <option value="">Select Sport</option>
                                %s
                            </select>
                        </div>
                    </div>
                    <div class="div-btn-container grid-btn-container">
                        <button class="btn btn-hollow btn-cancel" type="button">
                            <ion-icon class="arrow-icon" name="arrow-back-outline"></ion-icon>
                            <span>Cancel</span>
                        </button>
                        <button class="btn btn-full btn-add-new" type="submit" data-item-type="league">
                            <span>Add</span>
                        </button>
                    </div>
                </form>
            </div>
        ',
        '   
            <!-- ADD NEW SEASON POPUP -->
            <div class="popup-add popup-show popup-season hide-element" data-popup-index="4">
                <button class="popup-btn-close">
                    <ion-icon class="close-icon" name="close-outline"></ion-icon>
                </button>
                <header class="popup-add-header">
                    <h2>Add New Season?</h2>
                    <p>Fill in the following <span>fields</span> appropriately.</p>
                </header>
                <form class="form form-add" action="api/admin.php" method="POST">
                    <div class="div-multi-input-containers grid-2-columns">
                        <div class="div-input-container required-container">
                            <label for="new_season_year">Season Year:</label>
                            <input id="new_season_year" type="text" name="season_year" placeholder="YYYY/YY" autocomplete="off" required>
                        </div>
                        <div class="div-input-container required-container">
                            <label for="new_season_desc">Season Description:</label>
                            <input id="new_season_desc" type="text" name="season_desc" autocomplete="off" required>
                        </div>
                    </div>
                    <div class="div-multi-input-containers grid-2-columns">
                        <div class="div-input-container required-container">
                            <label for="new_season_sport_id">Sport:</label>
                            <select id="new_season_sport_id" name="sport_id" autocomplete="off" required>
                                <option value="">Select Sport</option>
                                %s
                            </select>
                        </div>
                        <div class="div-input-container required-container">
                            <label for="new_season_league_id">League:</label>
                            <select id="new_season_league_id" name="league_id" autocomplete="off" required>
                                <option value="">Select League</option>
                                %s
                            </select>
                        </div>
                    </div>
                    <div class="div-btn-container grid-btn-container">
                        <button class="btn btn-hollow btn-cancel" type="button">
                            <ion-icon class="arrow-icon" name="arrow-back-outline"></ion-icon>
                            <span>Cancel</span>
                        </button>
                        <button class="btn btn-full btn-add-new" type="submit" data-item-type="season">
                            <span>Add</span>
                        </button>
                    </div>
                </form>
            </div>
        ',
        '
            <!-- ADD NEW TEAM POPUP -->
            <div class="popup-add popup-show popup-team hide-element" data-popup-index="5">
                <button class="popup-btn-close">
                    <ion-icon class="close-icon" name="close-outline"></ion-icon>
                </button>
                <header class="popup-add-header">
                    <h2>Add New Team?</h2>
                    <p>Fill in the following <span>fields</span> appropriately.</p>
                </header>
                <form class="form form-add" action="api/admin.php" method="POST">
                    <div class="div-multi-input-containers grid-2-columns">
                        <div class="div-input-container required-container">
                            <label for="new_team_name">Team Name:</label>
                            <input id="new_team_name" type="text" name="team_name" autocomplete="off" required>
                        </div>
                        <div class="div-input-container required-container">
                            <label for="new_team_sport_id">Sport:</label>
                            <select id="new_team_sport_id" name="sport_id" autocomplete="off" required>
                                <option value="">Select Sport</option>
                                %s
                            </select>
                        </div>
                    </div>
                    <div class="div-multi-input-containers grid-2-columns">
                        <div class="div-input-container required-container">
                            <label for="new_team_league_id">League:</label>
                            <select id="new_team_league_id" name="league_id" autocomplete="off" required>
                                <option value="">Select League</option>
                                %s
                            </select>
                        </div>
                        <div class="div-multi-input-containers grid-2-columns">
                            <div class="div-input-container required-container">
                                <label for="new_team_season_id">Season ID:</label>
                                <input id="new_team_season_id" type="number" name="season_id" min="1" autocomplete="off" required>
                            </div>
                            <div class="div-input-container required-container">
                                <label for="new_team_max_players">Max Players:</label>
                                <input id="new_team_max_players" type="number" name="team_max_players" min="1" autocomplete="off" required>
                            </div>
                        </div>
                    </div>
                    <div class="div-multi-input-containers grid-2-columns">
                        <div class="div-input-container required-container">
                            <label for="new_team_home_color">Home Colors:</label>
                            <input id="new_team_home_color" type="text" name="team_home_color" placeholder="Color/Color" autocomplete="off" required>
                        </div>
                        <div class="div-input-container required-container">
                            <label for="new_team_away_color">Away Colors:</label>
                            <input id="new_team_away_color" type="text" name="team_away_color" placeholder="Color/Color" autocomplete="off" required>
                        </div>
                    </div>
                    <div class="div-btn-container grid-btn-container">
                        <button class="btn btn-hollow btn-cancel" type="button">
                            <ion-icon class="arrow-icon" name="arrow-back-outline"></ion-icon>
                            <span>Cancel</span>
                        </button>
                        <button class="btn btn-full btn-add-new" type="submit" data-item-type="team">
                            <span>Add</span>
                        </button>
                    </div>
                </form>
            </div>
        ',
        '
            <!-- ADD NEW POSITION POPUP -->
            <div class="popup-add popup-show popup-position hide-element" data-popup-index="6">
                <button class="popup-btn-close">
                    <ion-icon class="close-icon" name="close-outline"></ion-icon>
                </button>
                <header class="popup-add-header">
                    <h2>Add New Position?</h2>
                    <p>Fill in the following <span>fields</span> appropriately.</p>
                </header>
                <form class="form form-add" action="api/admin.php" method="POST">
                    <div class="div-multi-input-containers grid-2-columns">
                        <div class="div-input-container required-container">
                            <label for="new_position_name">Position Name:</label>
                            <input id="new_position_name" type="text" name="position_name" autocomplete="off" required>
                        </div>
                        <div class="div-input-container required-container">
                            <label for="new_position_sport_id">Sport:</label>
                            <select id="new_position_sport_id" name="sport_id" autocomplete="off" required>
                                <option value="">Select Sport</option>
                                %s
                            </select>
                        </div>
                    </div>
                    <div class="div-btn-container grid-btn-container">
                        <button class="btn btn-hollow btn-cancel" type="button">
                            <ion-icon class="arrow-icon" name="arrow-back-outline"></ion-icon>
                            <span>Cancel</span>
                        </button>
                        <button class="btn btn-full btn-add-new" type="submit" data-item-type="position">
                            <span>Add</span>
                        </button>
                    </div>
                </form>
            </div>
        ',
    ];
}
