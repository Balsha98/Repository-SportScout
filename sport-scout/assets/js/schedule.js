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

// ***** VARIABLES ***** //
const newItemInputs = {
    schedule: [
        "new_schedule_team_id",
        "new_schedule_sport_id",
        "new_schedule_league_id",
        "new_schedule_season_id",
        "new_schedule_home_team_id",
        "new_schedule_home_score",
        "new_schedule_away_team_id",
        "new_schedule_away_score",
        "new_schedule_date",
        "new_schedule_completion_status",
    ],
};

const existingItemInputs = {
    schedule: [
        "schedule_team_id",
        "schedule_season_id",
        "schedule_home_team_id",
        "schedule_home_score",
        "schedule_away_team_id",
        "schedule_away_score",
        "schedule_date",
        "schedule_completion_status",
    ],
};

// ***** FUNCTIONS ***** //
const killAllEventListeners = function (element) {
    $(element).off();
};

const toggleElement = function (popup) {
    [popup, popupOverlay]?.forEach((element) => {
        $(element)?.toggleClass("hide-element");
    });
};

const toggleEditGamePopup = function () {
    // Get the the schedule id and set the data.
    const scheduleID = $(this.closest(".div-schedule-game")).data("schedule-id");
    existingItemInputs["schedule"].forEach((id) => $(`#edit_${id}`).val($(`#${id}_${scheduleID}`).val()));
    $("#edit_schedule_id").val($(`#schedule_id_${scheduleID}`).val());

    // Show the popup.
    toggleElement(editPopup);
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
        if (input.attr("readonly")) continue;

        input.val("");
    }
};

const warnInputs = function (data, status) {
    for (const [key, value] of Object.entries(data)) {
        if (status === "fail") {
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
        toggleElement($(this.closest(".popup")));
    });
});

// SHOW ADD POPUP BUTTON
showAddPopupBtn.click(function () {
    toggleElement(addPopup);
});

// SHOW EDIT POPUP BUTTONS
attachEditBtnEvent(showEditPopupBtns);

// ADD NEW GAME BUTTON
addNewGameBtn?.click(function (clickEvent) {
    clickEvent.preventDefault();

    const form = $(this.closest(".form"));
    const url = form.attr("action");
    const method = form.attr("method");
    const itemType = $(this).data("item-type");

    const data = {};
    data["item_type"] = itemType;
    newItemInputs[itemType].forEach((id) => {
        data[id] = $(`#${id}`).val();
    });

    $.ajax({
        url: url,
        type: method,
        data: JSON.stringify(data),
        success: function (response) {
            console.log(response);
            const data = JSON.parse(response);
            const status = data["status"];

            // An error occurs.
            if (status === "fail") {
                warnInputs(data, status);
                return;
            }

            // Back to white.
            warnInputs(data, status);
            resetInput(data);

            // Last known id, or starting from scratch.
            let prevScheduleID = +data["last_schedule_id"] || 0;
            if (getCookie("last_deleted_game")) {
                if (+getCookie("last_deleted_game") < prevScheduleID) {
                    prevScheduleID = +getCookie("last_deleted_game");
                }
            }

            // Getting the season id.
            const seasonID = data["new_schedule_season_id"];

            // Getting the home team.
            const homeID = data["new_schedule_home_team_id"];
            const homeName = data["new_home_team_name"];
            const homeScore = data["new_schedule_home_score"];

            // Getting the away team.
            const awayID = data["new_schedule_away_team_id"];
            const awayName = data["new_away_team_name"];
            const awayScore = data["new_schedule_away_score"];

            // Getting the date & status.
            const newDate = data["new_schedule_date"];
            const compStatus = +data["new_schedule_completion_status"];

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
                <div class="div-schedule-game game-${++prevScheduleID}" data-schedule-id="${prevScheduleID}">
                    <div class="div-btn-edit">
                        <button class="btn-edit">
                            <ion-icon class="btn-edit-icon" name="create-outline"></ion-icon>
                        </button>
                    </div>
                    <div class="div-scoreboard-container">
                        <div class="div-grid-score-container">
                            <p class="home-score">${homeScore}</p>
                            <p>:</p>
                            <p class="away-score">${awayScore}</p>
                        </div>
                        <div class="div-grid-score-container">
                            <div class="div-team-name-container">
                                <p>${homeName}</p>
                                <span>Home</span>
                            </div>
                            <p>&nbsp;</p>
                            <div class="div-team-name-container">
                                <p>${awayName}</p>
                                <span>Away</span>
                            </div>
                        </div>
                    </div>
                    <div class="div-date-completion-container">
                        <div class="div-input-container">
                            <label for="date_${prevScheduleID}">Game Date:</label>
                            <input id="date_${prevScheduleID}" type="date" name="date_${prevScheduleID}" value="${newDate}" disabled>
                        </div>
                        <div class="span-completion-status ${css}" data-completion-index="${compStatus}">
                            <ion-icon class="status-icon" name="${icon}-outline"></ion-icon>
                        </div>
                    </div>
                    <div class="div-hidden-inputs-container">
                        <input id="schedule_season_id_${prevScheduleID}" type="hidden" name="season_id" value="${seasonID}">
                        <input id="schedule_home_id_${prevScheduleID}" type="hidden" name="home_id" value="${homeID}">
                        <input id="schedule_home_score_${prevScheduleID}" type="hidden" name="home_score" value="${homeScore}">
                        <input id="schedule_away_id_${prevScheduleID}" type="hidden" name="away_id" value="${awayID}">
                        <input id="schedule_away_score_${prevScheduleID}" type="hidden" name="away_score" value="${awayScore}">
                        <input id="schedule_completion_${prevScheduleID}" type="hidden" name="completion" value="${compStatus}">
                        <input id="schedule_date_${prevScheduleID}" type="hidden" name="date" value="${newDate}">
                    </div>
                </div>
            `);

            // Reattach button events.
            attachEditBtnEvent($(".btn-edit"));

            // Close popup.
            toggleElement(addPopup);
        },
    });
});

// UPDATE BUTTONS
formUpdateBtns?.each((_, btn) => {
    $(btn)?.click(function (clickEvent) {
        clickEvent.preventDefault();

        const editPopup = $(this.closest(".popup-edit"));
        const form = $(this.closest("form"));
        const url = form.attr("action");
        const method = $(this).data("method");
        const itemType = $(this).data("item-type");

        const data = {};
        data["item_type"] = itemType;

        let scheduleID;
        if (itemType === "schedule") {
            scheduleID = +$("#edit_schedule_id").val();
            data["item_id"] = scheduleID;
        }

        existingItemInputs[itemType].forEach((id) => {
            if (itemType === "schedule") data[`edit_${id}`] = $(`#edit_${id}`).val();
            else data[`${id}`] = $(`#${id}`).val();
        });

        console.log(data);

        $.ajax({
            url: url,
            type: method,
            data: JSON.stringify(data),
            success: function (response) {
                console.log(response);
                const data = JSON.parse(response);
                const status = data["status"];

                if (status === "fail") {
                    warnInputs(data, status);
                    return;
                }

                // Back to white.
                warnInputs(data, status);

                if (itemType === "team") {
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
                } else if (itemType === "schedule") {
                    existingItemInputs[itemType].forEach((id) => $(`#${id}_${scheduleID}`).val(data[`edit_${id}`]));

                    // Update each visual individually.
                    $(`.game-${scheduleID} .home-score`).text(data["edit_schedule_home_score"]);
                    $(`.game-${scheduleID} .away-score`).text(data["edit_schedule_away_score"]);
                    $(`#schedule_date_${scheduleID}`).val(data["edit_schedule_date"]);

                    const [css, icon] = getVisuals(data["edit_schedule_completion_status"]);

                    const statusSpan = $(`.game-${scheduleID} .span-completion-status`);
                    statusSpan.attr("class", `span-completion-status ${css}`);
                    statusSpan.data("completion-index", data["edit_schedule_completion_status"]);
                    $(`.game-${scheduleID} .status-icon`).attr("name", `${icon}-outline`);

                    toggleElement(editPopup);
                }
            },
        });
    });
});

// DELETE BUTTONS
formDeleteBtns?.each((_, btn) => {
    $(btn).click(function (clickEvent) {
        clickEvent.preventDefault();

        const itemType = $(this).data("clicked");
        const editPopup = $(this.closest(".popup-edit"));
        const form = $(this.closest("form"));

        $.ajax({
            url: form.attr("action"),
            type: $(this).data("method"),
            data: `${form.serialize()}&clicked=${itemType}`,
            success: function () {
                if (itemType === "DELETE_TEAM") {
                    $(".form-team-data").remove();
                    scrollContainer.remove();

                    [teamInfoContainer, scheduleContainer].forEach((div) => {
                        $(div).append(`
                            <div class="div-none-selected-container">
                                <ion-icon class="none-selected-icon" name="alert-circle-outline"></ion-icon>
                                <div class="none-selected-text">
                                    <h2>No schedule has been selected.</h2>
                                    <p>Please select one of the teams from the <a href="admin.php">Admin</a> page.</p>
                                </div>
                            </div>    
                        `);
                    });
                } else if (itemType === "DELETE_GAME") {
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
                            <div class="div-none-available-container">
                                <ion-icon class="none-available-icon" name="alert-circle-outline"></ion-icon>
                                <div class="none-available-text">
                                    <h2>No games have been scheduled.</h2>
                                    <p>Start off by adding a new game to the schedule.</p>
                                </div>
                            </div>
                        `);
                    }

                    toggleElement(editPopup);
                }
            },
        });
    });
});
