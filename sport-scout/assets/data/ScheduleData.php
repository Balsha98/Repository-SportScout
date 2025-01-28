<?php

class ScheduleData
{
    public const POPUPS = [
        '
            <!-- NEW GAME POPUP -->
            <div class="popup popup-add hide-element">
                <button class="popup-btn-close">
                    <ion-icon class="close-icon" name="close-outline"></ion-icon>
                </button>
                <header class="popup-header">
                    <h2>Add New Game?</h2>
                    <p>Fill in the following <span>fields</span> appropriately.</p>
                </header>
                <form class="form form-add" action="/api/schedule.php" method="POST">
                    <div class="div-multi-input-containers grid-3-columns">
                        <div class="div-input-container">
                            <label for="new_schedule_sport_name">Sport:</label>
                            <input id="new_schedule_sport_name" type="text" name="sport_name" value="%s" readonly>
                        </div>
                        <div class="div-input-container">
                            <label for="new_schedule_league_name">League:</label>
                            <input id="new_schedule_league_name" type="text" name="league_name" value="%s" readonly>
                        </div>
                        <div class="div-input-container required-container">
                            <label for="new_schedule_season_id">Season:</label>
                            <select id="new_schedule_season_id" name="season_id" autocomplete="off" required>
                                <option value="">Select Season</option>
                                %s
                            </select>
                        </div>
                    </div>
                    <div class="div-multi-input-containers custom-2-column-grid">
                        <div class="div-input-container required-container">
                            <label for="new_schedule_home_team_id">Home Team:</label>
                            <select id="new_schedule_home_team_id" name="home_team_id" autocomplete="off" required>
                                <option value="">Select Home Team</option>
                                %s
                            </select>
                        </div>
                        <div class="div-input-container required-container">
                            <label for="new_schedule_home_score">Home Score:</label>
                            <input id="new_schedule_home_score" type="number" name="home_score" min="0" value="0" required>
                        </div>
                    </div>
                    <div class="div-multi-input-containers custom-2-column-grid">
                        <div class="div-input-container required-container">
                            <label for="new_schedule_away_team_id">Away Team:</label>
                            <select id="new_schedule_away_team_id" name="away_team_id" autocomplete="off" required>
                                <option value="">Select Away Team</option>
                                %s
                            </select>
                        </div>
                        <div class="div-input-container required-container">
                            <label for="new_schedule_away_score">Away Score:</label>
                            <input id="new_schedule_away_score" type="number" name="away_score" min="0" value="0" required>
                        </div>
                    </div>
                    <div class="div-multi-input-containers grid-2-columns">
                        <div class="div-input-container required-container">
                            <label for="new_schedule_date">Game Date:</label>
                            <input id="new_schedule_date" type="date" name="date" required>
                        </div>
                        <div class="div-input-container required-container">
                            <label for="new_schedule_completion_status">Completion:</label>
                            <select id="new_schedule_completion_status" name="completion_status" required>
                                <option value="">Select Status</option>
                                <option value="1">Canceled</option>
                                <option value="2">Pending</option>
                                <option value="3">Completed</option>
                            </select>
                        </div>
                    </div>
                    <div class="div-btn-container grid-btn-container">
                        <button class="btn btn-hollow btn-cancel" type="button">
                            <ion-icon class="arrow-icon" name="arrow-back-outline"></ion-icon>
                            <span>Cancel</span>
                        </button>
                        <button class="btn btn-full btn-add-new" type="submit" data-item-type="schedule">Add</button>
                    </div>
                    <div class="div-hidden-inputs-container">
                        <input id="new_schedule_team_id" type="hidden" name="team_id" value="%s">
                        <input id="new_schedule_sport_id" type="hidden" name="sport_id" value="%s">
                        <input id="new_schedule_league_id" type="hidden" name="league_id" value="%s">
                    </div>
                </form>
            </div>
        ',
        '
            <!-- EDIT GAME POPUP -->
            <div class="popup popup-edit hide-element">
                <button class="popup-btn-close">
                    <ion-icon class="close-icon" name="close-outline"></ion-icon>
                </button>
                <header class="popup-header">
                    <h2>Edit Current Game?</h2>
                    <p>Fill in the following <span>fields</span> appropriately.</p>
                </header>
                <form class="form form-edit" action="/api/schedule.php">
                    <div class="div-input-container required-container">
                        <label for="edit_schedule_season_id">Season:</label>
                        <select id="edit_schedule_season_id" name="Season_id" autocomplete="off" required>
                            <option value="">Select Season</option>
                            %s
                        </select>
                    </div>
                    <div class="div-multi-input-containers custom-2-column-grid">
                        <div class="div-input-container">
                            <label for="edit_schedule_home_team_id">Home Team:</label>
                            <select id="edit_schedule_home_team_id" name="home_team_id" autocomplete="off" disabled>
                                <option value="">Select Home Team</option>
                                %s
                            </select>
                        </div>
                        <div class="div-input-container required-container">
                            <label for="edit_schedule_home_score">Home Score:</label>
                            <input id="edit_schedule_home_score" type="number" name="home_score" min="0" value="0" required>
                        </div>
                    </div>
                    <div class="div-multi-input-containers custom-2-column-grid">
                        <div class="div-input-container">
                            <label for="edit_schedule_away_team_id">Away Team:</label>
                            <select id="edit_schedule_away_team_id" name="away_team_id" autocomplete="off" disabled>
                                <option value="">Select Away Team</option>
                                %s
                            </select>
                        </div>
                        <div class="div-input-container required-container">
                            <label for="edit_schedule_away_score">Away Score:</label>
                            <input id="edit_schedule_away_score" type="number" name="away_score" min="0" value="0" required>
                        </div>
                    </div>
                    <div class="div-multi-input-containers grid-2-columns">
                        <div class="div-input-container required-container">
                            <label for="edit_schedule_date">Game Date:</label>
                            <input id="edit_schedule_date" type="date" name="date" required>
                        </div>
                        <div class="div-input-container required-container">
                            <label for="edit_schedule_completion_status">Completion:</label>
                            <select id="edit_schedule_completion_status" name="completion_status" required>
                                <option value="">Select Status</option>
                                <option value="1">Canceled</option>
                                <option value="2">Pending</option>
                                <option value="3">Completed</option>
                            </select>
                        </div>
                    </div>
                    <div class="div-btn-container grid-btn-container">
                        <button class="btn btn-hollow btn-delete" type="submit" data-method="DELETE" data-item-type="schedule">Delete</button>
                        <button class="btn btn-full btn-update" type="submit" data-method="PUT" data-item-type="schedule">Update</button>
                    </div>
                    <div class="div-hidden-inputs-container">
                        <input id="edit_schedule_id" id="schedule_id" type="hidden" name="schedule_id">
                        <input id="edit_schedule_team_id" type="hidden" name="team_id" value="%s">
                    </div>
                </form>
            </div>
        '
    ];
}
