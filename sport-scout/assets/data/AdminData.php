<?php

class AdminData
{
    // ***** ADMIN PAGE USER SELECT OPTIONS ***** //

    public const USER_SELECT_OPTIONS = [
        'Administrator' => '1|Administrator',
        'League Manager' => '2|League Manager',
        'Team Manager' => '3|Team Manager',
        'Team Coach' => '4|Team Coach',
        'Fan' => '5|Fan',
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
                <form class="form form-add" action="../process/process-admin.php">
                    <div class="div-multi-input-containers grid-2-columns">
                        <div class="div-input-container required-container">
                            <label for="new_username">Username:</label>
                            <input id="new_username" type="text" name="new_username" autocomplete="off" required>
                        </div>
                        <div class="div-input-container required-container">
                            <label for="new_password">Password:</label>
                            <input id="new_password" type="password" name="new_password" autocomplete="off" required>
                        </div>
                    </div>
                    <div class="div-multi-input-containers grid-2-columns">
                        <div class="div-input-container required-container">
                            <label for="new_role_name">Type of Role:</label>
                            <select id="new_role_name" name="new_role_name" autocomplete="off" required>
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
                                <label for="league_id">League ID:</label>
                                <input id="league_id" type="number" name="league_id" min="0" value="0" autocomplete="off" required>
                            </div>
                            <div class="div-input-container required-container">
                                <label for="team_id">Team ID:</label>
                                <input id="team_id" type="number" name="team_id" min="0" value="0" autocomplete="off" required>
                            </div>
                        </div>
                    </div>
                    <div class="div-btn-container grid-btn-container">
                        <button class="btn btn-hollow btn-cancel" type="button">
                            <ion-icon class="arrow-icon" name="arrow-back-outline"></ion-icon>
                            <span>Cancel</span>
                        </button>
                        <button class="btn btn-full btn-add-new" type="submit" data-method="POST" data-clicked="ADD_USER">
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
                <form class="form form-add" action="../process/process-admin.php">
                    <div class="div-input-container required-container">
                        <label for="new_sport_name">Sport Name:</label>
                        <input id="new_sport_name" type="text" name="new_sport_name" autocomplete="off" required>
                    </div>
                    <div class="div-btn-container grid-btn-container">
                        <button class="btn btn-hollow btn-cancel" type="button">
                            <ion-icon class="arrow-icon" name="arrow-back-outline"></ion-icon>
                            <span>Cancel</span>
                        </button>
                        <button class="btn btn-full btn-add-new" type="submit" data-method="POST" data-clicked="ADD_SPORT">
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
                <form class="form form-add" action="../process/process-admin.php">
                    <div class="div-multi-input-containers custom-2-column-grid">
                        <div class="div-input-container required-container">
                            <label for="new_league_name">League Name:</label>
                            <input id="new_league_name" type="text" name="new_league_name" autocomplete="off" required>
                        </div>
                        <div class="div-input-container required-container">
                            <label for="league_sport_id">Sport ID:</label>
                            <input id="league_sport_id" type="number" name="sport_id" min="1" autocomplete="off" required>
                        </div>
                    </div>
                    <div class="div-btn-container grid-btn-container">
                        <button class="btn btn-hollow btn-cancel" type="button">
                            <ion-icon class="arrow-icon" name="arrow-back-outline"></ion-icon>
                            <span>Cancel</span>
                        </button>
                        <button class="btn btn-full btn-add-new" type="submit" data-method="POST" data-clicked="ADD_LEAGUE">
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
                <form class="form form-add" action="../process/process-admin.php">
                    <div class="div-multi-input-containers grid-2-columns">
                        <div class="div-input-container required-container">
                            <label for="new_season_year">Season Year:</label>
                            <input id="new_season_year" type="text" name="new_season_year" placeholder="YYYY/YY" autocomplete="off" required>
                        </div>
                        <div class="div-multi-input-containers grid-2-columns">
                            <div class="div-input-container required-container">
                                <label for="new_season_sport_id">Sport ID:</label>
                                <input id="new_season_sport_id" type="number" name="sport_id" min="1" autocomplete="off" required>
                            </div>
                            <div class="div-input-container required-container">
                                <label for="new_season_league_id">League ID:</label>
                                <input id="new_season_league_id" type="number" name="league_id" min="1" autocomplete="off" required>
                            </div>
                        </div>
                    </div>
                    <div class="div-input-container required-container">
                        <label for="new_season_desc">Season Description:</label>
                        <input id="new_season_desc" type="text" name="new_season_desc" autocomplete="off" required>
                    </div>
                    <div class="div-btn-container grid-btn-container">
                        <button class="btn btn-hollow btn-cancel" type="button">
                            <ion-icon class="arrow-icon" name="arrow-back-outline"></ion-icon>
                            <span>Cancel</span>
                        </button>
                        <button class="btn btn-full btn-add-new" type="submit" data-method="POST" data-clicked="ADD_SEASON">
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
                <form class="form form-add" action="../process/process-admin.php">
                    <div class="div-multi-input-containers">
                        <div class="div-input-container required-container">
                            <label for="new_team_name">Team Name:</label>
                            <input id="new_team_name" type="text" name="new_team_name" autocomplete="off" required>
                        </div>
                    </div>
                    <div class="div-multi-input-containers grid-4-columns">
                        <div class="div-input-container required-container">
                            <label for="team_sport_id">Sport ID:</label>
                            <input id="team_sport_id" type="number" name="sport_id" min="1" autocomplete="off" required>
                        </div>
                        <div class="div-input-container required-container">
                            <label for="team_league_id">League ID:</label>
                            <input id="team_league_id" type="number" name="league_id" min="1" autocomplete="off" required>
                        </div>
                        <div class="div-input-container required-container">
                            <label for="team_season_id">Season ID:</label>
                            <input id="team_season_id" type="number" name="season_id" min="1" autocomplete="off" required>
                        </div>
                        <div class="div-input-container required-container">
                            <label for="new_team_max_players">Max Players:</label>
                            <input id="new_team_max_players" type="number" name="new_team_max_players" min="1" autocomplete="off" required>
                        </div>
                    </div>
                    <div class="div-multi-input-containers grid-2-columns">
                        <div class="div-input-container required-container">
                            <label for="new_team_home_color">Home Colors:</label>
                            <input id="new_team_home_color" type="text" name="new_team_home_color" placeholder="Color/Color" autocomplete="off" required>
                        </div>
                        <div class="div-input-container required-container">
                            <label for="new_team_away_color">Away Colors:</label>
                            <input id="new_team_away_color" type="text" name="new_team_away_color" placeholder="Color/Color" autocomplete="off" required>
                        </div>
                    </div>
                    <div class="div-btn-container grid-btn-container">
                        <button class="btn btn-hollow btn-cancel" type="button">
                            <ion-icon class="arrow-icon" name="arrow-back-outline"></ion-icon>
                            <span>Cancel</span>
                        </button>
                        <button class="btn btn-full btn-add-new" type="submit" data-method="POST" data-clicked="ADD_TEAM">
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
                <form class="form form-add" action="../process/process-admin.php">
                    <div class="div-multi-input-containers custom-2-column-grid">
                        <div class="div-input-container required-container">
                            <label for="new_position_name">Position Name:</label>
                            <input id="new_position_name" type="text" name="new_position_name" autocomplete="off" required>
                        </div>
                        <div class="div-input-container required-container">
                            <label for="new_position_sport_id">Sport ID:</label>
                            <input id="new_position_sport_id" type="number" name="sport_id" min="1" autocomplete="off" required>
                        </div>
                    </div>
                    <div class="div-btn-container grid-btn-container">
                        <button class="btn btn-hollow btn-cancel" type="button">
                            <ion-icon class="arrow-icon" name="arrow-back-outline"></ion-icon>
                            <span>Cancel</span>
                        </button>
                        <button class="btn btn-full btn-add-new" type="submit" data-method="POST" data-clicked="ADD_POSITION">
                            <span>Add</span>
                        </button>
                    </div>
                </form>
            </div>
        ',
    ];
}
