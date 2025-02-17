<?php

class TeamData
{
    // ***** TEAM PAGE STAFF SELECT OPTIONS ***** //

    public const STAFF_SELECT_OPTIONS = [
        'Team Manager' => '3|Team Manager',
        'Team Coach' => '4|Team Coach',
        'Fan' => '5|Fan',
    ];

    // ***** TEAM PAGE POPUPS ***** //

    public const POPUPS = [
        '
            <!-- NEW PLAYER POPUP -->
            <div class="popup-add popup-show popup-player hide-element" data-popup-index="1">
                <button class="popup-btn-close">
                    <ion-icon class="close-icon" name="close-outline"></ion-icon>
                </button>
                <header class="popup-add-header">
                    <h2>Add New Player?</h2>
                    <p>Fill in the following <span>fields</span> appropriately.</p>
                </header>
                <form class="form form-add" action="/api/team.php" method="POST">
                    <input id="new_player_sport_id" type="hidden" name="sport_id" value="%s">
                    <input id="new_player_league_name" type="hidden" name="league_name" value="%s">
                    <input id="new_player_team_id" type="hidden" name="team_id" value="%s">
                    <div class="div-multi-input-containers grid-2-columns">
                        <div class="div-input-container required-container">
                            <label for="new_player_first">First Name:</label>
                            <input id="new_player_first" type="text" name="first_name" required>
                        </div>
                        <div class="div-input-container required-container">
                            <label for="new_player_last">Last Name:</label>
                            <input id="new_player_last" type="tel" name="last_name" required>
                        </div>
                    </div>
                    <div class="div-input-container required-container">
                        <label for="new_player_dob">Date of Birth:</label>
                        <input id="new_player_dob" type="date" name="dob" required>
                    </div>
                    <div class="div-multi-input-containers grid-2-columns">
                        <div class="div-input-container required-container">
                            <label for="new_player_position_id">Position ID:</label>
                            <select id="new_player_position_id" name="position_id" autocomplete="off" required>
                                <option value="">Select Position</option>
                                %s
                            </select>
                        </div>
                        <div class="div-input-container required-container">
                            <label for="new_player_jersey_number">Jersey:</label>
                            <input id="new_player_jersey_number" type="number" name="jersey_number" min="0" required>
                        </div>
                    </div>
                    <div class="div-btn-container grid-btn-container">
                        <button class="btn btn-hollow btn-cancel" type="button">
                            <ion-icon class="arrow-icon" name="arrow-back-outline"></ion-icon>
                            <span>Cancel</span>
                        </button>
                        <button class="btn btn-full btn-add-new" type="submit" data-item-type="player">
                            <span>Add</span>
                        </button>
                    </div>
                </form>
            </div>
        ',
        '
            <!-- NEW STAFF & FAN POPUP -->
            <div class="popup-add popup-show popup-staff hide-element" data-popup-index="2">
                <button class="popup-btn-close">
                    <ion-icon class="close-icon" name="close-outline"></ion-icon>
                </button>
                <header class="popup-add-header">
                    <h2>Add New Staff?</h2>
                    <p>Fill in the following <span>fields</span> appropriately.</p>
                </header>
                <form class="form form-add" action="/api/team.php" method="POST">
                    <input id="new_user_league_id" type="hidden" name="league_id" value="%s">
                    <input id="new_user_team_id" type="hidden" name="team_id" value="%s">
                    <div class="div-multi-input-containers grid-2-columns">
                        <div class="div-input-container required-container">
                            <label for="new_username">Username:</label>
                            <input id="new_username" type="text" name="username" required>
                        </div>
                        <div class="div-input-container required-container">
                            <label for="new_user_password">Password:</label>
                            <input id="new_user_password" type="password" name="password" required>
                        </div>
                    </div>
                    <div class="div-multi-input-containers grid-2-columns">
                        <div class=" div-input-container required-container">
                            <label for="new_user_role_name">Type of Role:</label>
                            <select id="new_user_role_name" name="role_name" required>
                                <option value="">Select Role</option>
                                <option value="3|Team Manager">Team Manager</option>
                                <option value="4|Team Coach">Team Coach</option>
                                <option value="5|Fan">Fan</option>
                            </select>
                        </div>
                        <div class="div-input-container">
                            <label for="new_user_league_name">League:</label>
                            <input id="new_user_league_name" type="text" name="league_name" value="%s" readonly>
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
        '
    ];
}
