<?php

class TeamData
{
    // ***** TEAM PAGE STAFF SELECT OPTIONS ***** //
    const STAFF_SELECT_OPTIONS = [
        'Team Manager' => '3|Team Manager',
        'Team Coach' => '4|Team Coach',
        'Fan' => '5|Fan',
    ];

    // ***** TEAM PAGE POPUPS ***** //
    const POPUPS = [
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
                <form class="form form-add" action="../process/process-team.php">
                    <input type="hidden" name="team_id" value="%s">
                    <input type="hidden" name="sport_id" value="%s">
                    <input type="hidden" name="league_name" value="%s">
                    <div class="div-multi-input-containers grid-2-columns">
                        <div class="div-input-container required-container">
                            <label for="new_player_first">First Name:</label>
                            <input id="new_player_first" type="text" name="new_player_first" required>
                        </div>
                        <div class="div-input-container required-container">
                            <label for="new_player_last">Last Name:</label>
                            <input id="new_player_last" type="tel" name="new_player_last" required>
                        </div>
                    </div>
                    <div class="div-multi-input-containers grid-2-columns">
                        <div class="div-input-container required-container">
                            <label for="new_player_dob">Date of Birth:</label>
                            <input id="new_player_dob" type="date" name="new_player_dob" required>
                        </div>
                        <div class="div-multi-input-containers grid-2-columns">
                            <div class="div-input-container required-container">
                                <label for="new_position_id">Position ID:</label>
                                <input id="new_position_id" type="number" name="new_position_id" min="1" required>
                            </div>
                            <div class="div-input-container required-container">
                                <label for="new_jersey_number">Jersey:</label>
                                <input id="new_jersey_number" type="number" name="new_jersey_number" min="0" required>
                            </div>
                        </div>
                    </div>
                    <div class="div-btn-container grid-btn-container">
                        <button class="btn btn-hollow btn-cancel" type="button">
                            <ion-icon class="arrow-icon" name="arrow-back-outline"></ion-icon>
                            <span>Cancel</span>
                        </button>
                        <button class="btn btn-full btn-add-new" type="submit" data-method="POST" data-clicked="ADD_PLAYER">
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
                <form class="form form-add" action="../process/process-team.php">
                    <input type="hidden" name="team_id" value="%s">
                    <input type="hidden" name="league_id" value="%s">
                    <div class="div-multi-input-containers grid-2-columns">
                        <div class="div-input-container required-container">
                            <label for="new_username">Username:</label>
                            <input id="new_username" type="text" name="new_username" required>
                        </div>
                        <div class="div-input-container required-container">
                            <label for="new_password">Password:</label>
                            <input id="new_password" type="password" name="new_password" required>
                        </div>
                    </div>
                    <div class="div-multi-input-containers grid-2-columns">
                        <div class=" div-input-container required-container">
                            <label for="new_role_name">Type of Role:</label>
                            <select id="new_role_name" name="new_role_name" required>
                                <option value="">Select Role</option>
                                <option value="3|Team Manager">Team Manager</option>
                                <option value="4|Team Coach">Team Coach</option>
                                <option value="5|Fan">Fan</option>
                            </select>
                        </div>
                        <div class="div-input-container">
                            <label for="league_name">League:</label>
                            <input id="league_name" type="text" name="league_name" value="%s" readonly>
                        </div>
                    </div>
                    <div class="div-btn-container grid-btn-container">
                        <button class="btn btn-hollow btn-cancel" type="button">
                            <ion-icon class="arrow-icon" name="arrow-back-outline"></ion-icon>
                            <span>Cancel</span>
                        </button>
                        <button class="btn btn-full btn-add-new" type="submit" data-method="POST" data-clicked="ADD_STAFF">
                            <span>Add</span>
                        </button>
                    </div>
                </form>
            </div>
        '
    ];
}
