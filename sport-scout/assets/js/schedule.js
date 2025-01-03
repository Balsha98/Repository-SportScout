"use strict";

// ***** DOM ELEMENTS ***** //
const popupOverlay = $(".popup-overlay");
const popupCloseBtns = $(".popup-btn-close");
const addPopup = $(".popup-add");
const editPopup = $(".popup-edit");
const colorDivs = $(".team-color");
const teamInfoContainer = $(".div-team-info-container");
const scheduleContainer = $(".div-schedule-container");
const scrollContainer = $(".div-scroll-container");
const addNewGameBtn = $(".btn-add-new");
const cancelBtn = $(".btn-cancel");
const showAddPopupBtn = $(".btn-add");
const showEditPopupBtns = $(".btn-edit");
const formUpdateBtns = $(".btn-update");
const formDeleteBtns = $(".btn-delete");

// Inputs.
const editableValues = [
    "schedule_id",
    "edit_home_team_id",
    "edit_home_score",
    "edit_away_team_id",
    "edit_away_score",
    "edit_season_id",
    "edit_status",
    "edit_scheduled",
];

// ***** FUNCTIONS ***** //
const killAllEventListeners = function (el) {
    $(el).off();
};

const showHideEl = function (popup) {
    [popup, popupOverlay]?.forEach((el) => {
        $(el)?.toggleClass("hide-element");
    });
};

const toggleEditGamePopup = function () {
    const editPopupClass = editPopup.attr("class").split(" ")[1];

    // Fetch the data from the div and edit it.
    const gameDiv = $(this.closest(".div-schedule-game"));
    const scheduleID = gameDiv.data("schedule-id");

    // Get schedule data.
    const gameData = gameDiv.data("editable-data").split("|");
    gameData.unshift(scheduleID);

    // Setting the values.
    const length = editableValues.length - 1;
    for (let i = 0; i < length; i++) {
        $(`.${editPopupClass} #${editableValues[i]}`).val(gameData[i]);
    }

    $(`.${editPopupClass} #${editableValues[length]}`).val(gameData[length]);

    showHideEl(editPopup);
};

const attachEditBtnEvent = function (btns) {
    btns?.each((_, btn) => {
        killAllEventListeners(btn);
        $(btn).click(toggleEditGamePopup);
    });
};

const setTeamColors = function (colors = []) {
    colorDivs.each((i, div) => {
        if (colors.length !== 0) $(div).data("colors", colors[i]);

        const currColors = $(div)
            .data("colors")
            .split("/")
            .map((color) => {
                return color.toLowerCase();
            });

        div.style = `
            background-image: conic-gradient(
                ${currColors[1]} 180deg,
                ${currColors[0]} 180deg 
            );
        `;
    });
};

setTeamColors();

const resetInput = function (data) {
    for (const key of Object.keys(data)) {
        const input = $(`#${key}`);
        if (input.attr("readonly") === "readonly") continue;
        input.val("");
    }
};

const warnInputs = function (data, message) {
    for (const [key, value] of Object.entries(data)) {
        if (message === "fail") {
            if (value === "") {
                $(`#${key}`).closest(".div-input-container").addClass("red-container");

                continue;
            }
        }

        $(`#${key}`).closest(".div-input-container").removeClass("red-container");
    }
};

const getOnlyGames = function () {
    let games = [];
    scrollContainer.children().each((_, div) => {
        const divClass = $(div).attr("class").split(" ")[0];
        if (divClass.includes("schedule")) games.push(div);
    });

    return games;
};

const getVisuals = function (status) {
    let css = "";
    let icon = "";

    switch (status) {
        case 1:
            css = "canceled";
            icon = "close";
            break;
        case 2:
            css = "pending";
            icon = "alert";
            break;
        default:
            css = "completed";
            icon = "checkmark";
    }

    return [css, icon];
};

// ***** EVENT LISTENERS ***** //
// POPUP CLOSE & CANCEL BUTTONS
[...popupCloseBtns, cancelBtn].forEach((btn) => {
    $(btn)?.click(function () {
        showHideEl($(this.closest(".popup")));
    });
});

// SHOW ADD POPUP BUTTON
showAddPopupBtn.click(function () {
    showHideEl(addPopup);
});

// SHOW EDIT POPUP BUTTONS
attachEditBtnEvent(showEditPopupBtns);

// ADD NEW GAME BUTTON
addNewGameBtn?.click(function (clickEvent) {
    clickEvent.preventDefault();

    const clickedBtn = $(this).data("clicked");
    const form = $(this.closest(".form"));

    $.ajax({
        url: form.attr("action"),
        type: $(this).data("method"),
        data: `${form.serialize()}&clicked=${clickedBtn}`,
        success: function (response) {
            console.log(response);
            const data = JSON.parse(response);
            const message = data["message"];

            // An error occurs.
            if (message === "fail") {
                warnInputs(data, message);
                return;
            }

            // Back to white.
            warnInputs(data, message);
            resetInput(data);

            // Last known id, or starting from scratch.
            let prevScheduleID = +data["last_schedule_id"] || 0;
            if (getCookie("last_deleted_game")) {
                if (+getCookie("last_deleted_game") < prevScheduleID) {
                    prevScheduleID = +getCookie("last_deleted_game");
                }
            }

            // Getting the season id.
            const seasonID = data["new_season_id"];

            // Getting the home team.
            const homeID = data["new_home_team_id"];
            const homeName = data["new_home_team_name"];
            const homeScore = data["new_home_score"];

            // Getting the away team.
            const awayID = data["new_away_team_id"];
            const awayName = data["new_away_team_name"];
            const awayScore = data["new_away_score"];

            // Getting the date & status.
            const newScheduled = data["new_scheduled"];
            const compStatus = +data["new_status"];

            // Setting the editable dataset.
            const dataset = `${homeID}|${homeScore}|${awayID}|${awayScore}|${seasonID}|${compStatus}|${newScheduled}`;

            // Visuals.
            const [css, icon] = getVisuals(compStatus);

            // Checking for games.
            const noneAvailableDiv = $(".div-none-available-container");
            if (noneAvailableDiv) {
                scrollContainer.removeClass("flex-center");
                noneAvailableDiv.remove();
            }

            // Display new game.
            scrollContainer.append(`
                <div class='div-schedule-game game-${++prevScheduleID}' data-schedule-id='${prevScheduleID}' data-editable-data='${dataset}'>
                    <div class="div-btn-edit">
                        <button class="btn-edit">
                            <ion-icon class="btn-edit-icon" name="create-outline"></ion-icon>
                        </button>
                    </div>
                    <div class='div-scoreboard-container'>
                            <div class='div-grid-score-container'>
                            <p class="home-score">${homeScore}</p>
                            <p>:</p>
                            <p class="away-score">${awayScore}</p>
                        </div>
                        <div class='div-grid-score-container'>
                            <div class='div-team-name-container'>
                                <p>${homeName}</p>
                                <span>Home</span>
                            </div>
                            <p>&nbsp;</p>
                            <div class='div-team-name-container'>
                                <p>${awayName}</p>
                                <span>Away</span>
                            </div>
                        </div>
                    </div>
                    <div class='div-date-completion-container'>
                            <div class='div-input-container'>
                            <label for='scheduled_${prevScheduleID}'>Game Date:</label>
                            <input id='scheduled_${prevScheduleID}' type='date' name='scheduled_${prevScheduleID}' value='${newScheduled}' disabled>
                        </div>
                        <div class='div-completion-status ${css}' data-completion-index='${compStatus}'>
                            <ion-icon class='status-icon' name='${icon}-outline'></ion-icon>
                        </div>
                    </div>
                </div>
            `);

            // Reattach button events.
            attachEditBtnEvent($(".btn-edit"));

            // Close popup.
            showHideEl(addPopup);
        },
    });
});

// UPDATE BUTTONS
formUpdateBtns?.each((_, btn) => {
    $(btn)?.click(function (clickEvent) {
        clickEvent.preventDefault();

        const clickedBtn = $(this).data("clicked");
        const editPopup = $(this.closest(".popup-edit"));
        const form = $(this.closest("form"));

        $.ajax({
            url: form.attr("action"),
            type: $(this).data("method"),
            data: `${form.serialize()}&clicked=${clickedBtn}`,
            success: function (response) {
                console.log(response);
                const data = JSON.parse(response);
                const message = data["message"];

                if (message === "fail") {
                    warnInputs(data, message);
                    return;
                }

                // Back to white.
                warnInputs(data, message);

                if (clickedBtn === "UPDATE_TEAM") {
                    setTeamColors([data["home_color"], data["away_color"]]);

                    const inTeamName = $("#team_name");
                    const newTeamName = inTeamName.val();
                    const oldTeamName = inTeamName.data("prev-team-name");
                    inTeamName.data("prev-team-name", newTeamName);

                    const homeTeamNames = $(".home-team");
                    const awayTeamNames = $(".away-team");
                    [...homeTeamNames, ...awayTeamNames].forEach((teamName) => {
                        if ($(teamName).text() === oldTeamName) {
                            $(teamName).text(newTeamName);
                        }
                    });
                } else if (clickedBtn === "UPDATE_GAME") {
                    const editPopupClass = editPopup.attr("class").split(" ")[1];
                    const relGame = [...scrollContainer.children()].find(
                        (div) => +$(div).data("schedule-id") === +$(`.${editPopupClass} #schedule_id`).val()
                    );

                    const relGameClass = $(relGame).attr("class").split(" ")[1];

                    const scheduleID = data["schedule_id"];
                    const homeID = data["edit_home_team_id"];
                    const homeScore = data["edit_home_score"];
                    const awayID = data["edit_away_team_id"];
                    const awayScore = data["edit_away_score"];
                    const seasonID = data["edit_season_id"];
                    const scheduled = data["edit_scheduled"];
                    const compStatus = +data["edit_status"];

                    const dataset = `${homeID}|${homeScore}|${awayID}|${awayScore}|${seasonID}|${compStatus}|${scheduled}`;
                    $(relGame).data("editable-data", dataset);

                    // Update each visual individually.
                    $(`.${relGameClass} .home-score`).text(homeScore);
                    $(`.${relGameClass} .away-score`).text(awayScore);
                    $(`.${relGameClass} #scheduled_${scheduleID}`).val(scheduled);

                    const [css, icon] = getVisuals(compStatus);

                    const statusDiv = $(`.${relGameClass} .div-completion-status`);
                    statusDiv.attr("class", `div-completion-status ${css}`);
                    statusDiv.data("completion-index", compStatus);

                    $(`.${relGameClass} .status-icon`).attr("name", `${icon}-outline`);

                    showHideEl(editPopup);
                }
            },
        });
    });
});

// DELETE BUTTONS
formDeleteBtns?.each((_, btn) => {
    $(btn).click(function (clickEvent) {
        clickEvent.preventDefault();

        const clickedBtn = $(this).data("clicked");
        const editPopup = $(this.closest(".popup-edit"));
        const form = $(this.closest("form"));

        $.ajax({
            url: form.attr("action"),
            type: $(this).data("method"),
            data: `${form.serialize()}&clicked=${clickedBtn}`,
            success: function () {
                if (clickedBtn === "DELETE_TEAM") {
                    $(".form-team-data").remove();
                    scrollContainer.remove();

                    [teamInfoContainer, scheduleContainer].forEach((div) => {
                        $(div).append(`
                            <div class='div-none-selected-container'>
                                <ion-icon class='none-selected-icon' name='alert-circle-outline'></ion-icon>
                                <div class='none-selected-text'>
                                    <h2>No schedule has been selected.</h2>
                                    <p>Please select one of the teams from the <a href='admin.php'>Admin</a> page.</p>
                                </div>
                            </div>    
                        `);
                    });
                } else if (clickedBtn === "DELETE_GAME") {
                    let scheduleGames = getOnlyGames();

                    const delGame = $(
                        scheduleGames.find((div) => {
                            const popupName = editPopup.attr("class").split(" ")[1];
                            return +$(div).data("schedule-id") === +$(`.${popupName} #schedule_id`).val();
                        })
                    );

                    setCookie("last_deleted_game", delGame.data("schedule-id"));
                    // Getting the last deleted row makes no sense... it kind of does.

                    delGame.remove();

                    scheduleGames = getOnlyGames();
                    if (scheduleGames.length === 0) {
                        scrollContainer.addClass("flex-center");
                        scrollContainer.append(`
                            <div class='div-none-available-container'>
                                <ion-icon class='none-available-icon' name='alert-circle-outline'></ion-icon>
                                <div class='none-available-text'>
                                    <h2>No games have been scheduled.</h2>
                                    <p>Start off by adding a new game to the schedule.</p>
                                </div>
                            </div>
                        `);
                    }

                    showHideEl(editPopup);
                }
            },
        });
    });
});
