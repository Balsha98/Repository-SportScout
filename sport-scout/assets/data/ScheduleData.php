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
                <form class="form form-add" action="api/schedule.php">
                    <input type="hidden" name="team_id" value="%s">
                    <input type="hidden" name="sport_id" value="%s">
                    <input type="hidden" name="league_id" value="%s">
                    <div class="div-multi-input-containers grid-3-columns">
                        <div class="div-input-container">
                            <label for="sport_name">Sport:</label>
                            <input id="sport_name" type="text" name="sport_name" value="%s" readonly>
                        </div>
                        <div class="div-input-container">
                            <label for="league_name">League:</label>
                            <input id="league_name" type="text" name="league_name" value="%s" readonly>
                        </div>
                        <div class="div-input-container required-container">
                            <label for="new_season_id">Season ID:</label>
                            <input id="new_season_id" type="number" name="new_season_id" required>
                        </div>
                    </div>
                    <div class="div-multi-input-containers grid-4-columns">
                        <div class="div-input-container required-container">
                            <label for="new_home_team_id">Home ID:</label>
                            <input id="new_home_team_id" type="number" name="new_home_team_id" min="1" required>
                        </div>
                        <div class="div-input-container required-container">
                            <label for="new_home_score">Home Score:</label>
                            <input id="new_home_score" type="number" name="new_home_score" min="0" value="0" required>
                        </div>
                        <div class="div-input-container required-container">
                            <label for="new_away_team_id">Away ID:</label>
                            <input id="new_away_team_id" type="number" name="new_away_team_id" min="1" required>
                        </div>
                        <div class="div-input-container required-container">
                            <label for="new_away_score">Away Score:</label>
                            <input id="new_away_score" type="number" name="new_away_score" min="0" value="0" required>
                        </div>
                    </div>
                    <div class="div-multi-input-containers grid-2-columns">
                        <div class="div-input-container required-container">
                            <label for="new_scheduled">Game Date:</label>
                            <input id="new_scheduled" type="date" name="new_scheduled" required>
                        </div>
                        <div class="div-input-container required-container">
                            <label for="new_status">Completion:</label>
                            <select id="new_status" name="new_status" required>
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
                        <button class="btn btn-full btn-add-new" type="submit" data-method="POST" data-clicked="ADD_GAME">Add</button>
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
                <form class="form form-edit" action="api/schedule.php">
                    <input type="hidden" name="team_id" value="%s">
                    <input id="schedule_id" type="hidden" name="schedule_id">
                    <div class="div-multi-input-containers grid-4-columns">
                        <div class="div-input-container">
                            <label for="edit_home_team_id">Home ID:</label>
                            <input id="edit_home_team_id" type="number" name="home_team_id" min="1" readonly>
                        </div>
                        <div class="div-input-container required-container">
                            <label for="edit_home_score">Home Score:</label>
                            <input id="edit_home_score" type="number" name="home_score" min="0" value="0" required>
                        </div>
                        <div class="div-input-container">
                            <label for="edit_away_team_id">Away ID:</label>
                            <input id="edit_away_team_id" type="number" name="away_team_id" min="1" readonly>
                        </div>
                        <div class="div-input-container required-container">
                            <label for="edit_away_score">Away Score:</label>
                            <input id="edit_away_score" type="number" name="away_score" min="0" value="0" required>
                        </div>
                    </div>
                    <div class="div-multi-input-containers custom-3-column-grid-reverse">
                        <div class="div-input-container required-container">
                            <label for="edit_season_id">Season ID:</label>
                            <input id="edit_season_id" type="number" name="season_id" min="0" required>
                        </div>
                        <div class="div-input-container required-container">
                            <label for="edit_scheduled">Game Date:</label>
                            <input id="edit_scheduled" type="date" name="scheduled" required>
                        </div>
                        <div class="div-input-container required-container">
                            <label for="edit_status">Completion:</label>
                            <select id="edit_status" name="status" required>
                                <option value="">Select Status</option>
                                <option value="1">Canceled</option>
                                <option value="2">Pending</option>
                                <option value="3">Completed</option>
                            </select>
                        </div>
                    </div>
                    <div class="div-btn-container grid-btn-container">
                        <button class="btn btn-hollow btn-delete" type="submit" data-method="POST" data-clicked="DELETE_GAME">Delete</button>
                        <button class="btn btn-full btn-update" type="submit" data-method="POST" data-clicked="UPDATE_GAME">Update</button>
                    </div>
                </form>
            </div>
        '
    ];
}
