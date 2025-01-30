import * as general from "../helper/general.js";
import { getCookie, setCookie } from "../helper/cookie.js";
import { scheduleInputs } from "../data/inputs.js";

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

// ***** FUNCTIONS ***** //
const toggleEditGamePopup = function () {
    // Get the the schedule id and set the data.
    const scheduleID = $(this.closest(".div-schedule-game")).data("schedule-id");

    const seasonID = $(`#schedule_season_id_${scheduleID}`).val();
    const seasonYear = $(`#schedule_season_year_${scheduleID}`).val();
    $("#edit_schedule_season_id").val(`${seasonID}|${seasonYear}`);

    ["home", "away"].forEach((side) => {
        const teamID = $(`#schedule_${side}_team_id_${scheduleID}`).val();
        const teamName = $(`#schedule_${side}_team_name_${scheduleID}`).val();
        $(`#edit_schedule_${side}_team_id`).val(`${teamID}|${teamName}`);
    });

    scheduleInputs["alter"]["schedule"].forEach((id) => {
        $(`#edit_${id}`).val($(`#${id}_${scheduleID}`).val());
    });

    $("#edit_schedule_id").val($(`#schedule_id_${scheduleID}`).val());

    // Show the popup.
    general.toggleElement(editPopup, popupOverlay);
};

const setTeamColors = function (colors = []) {
    colorDivs.each((i, div) => {
        if (colors.length !== 0) $(div).data("colors", colors[i]);

        const currColors = $(div)
            .data("colors")
            .split("/")
            .map((color) => color.toLowerCase());

        div.style = `
            background-image: conic-gradient(
                ${currColors[1]} 180deg,
                ${currColors[0]} 180deg 
            );
        `;
    });
};

setTeamColors();

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
        general.toggleElement($(this.closest(".popup")), popupOverlay);
    });
});

// SHOW ADD POPUP BUTTON
showAddPopupBtn.click(function () {
    general.toggleElement(addPopup, popupOverlay);
});

// SHOW EDIT POPUP BUTTONS
general.attachEvent(showEditPopupBtns, toggleEditGamePopup);

// ADD NEW GAME BUTTON
addNewGameBtn?.click(function (clickEvent) {
    clickEvent.preventDefault();

    const form = $(this.closest(".form"));
    const url = form.attr("action");
    const method = form.attr("method");
    const itemType = $(this).data("item-type");

    const data = {};
    data["item_type"] = itemType;
    scheduleInputs["add"][itemType].forEach((id) => {
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
                general.warnInputs(data, status);
                return;
            }

            // Back to white.
            general.warnInputs(data, status);
            general.resetInput(data);

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
                        <input id="schedule_id_${prevScheduleID}" type="hidden" name="schedule_id" value="${prevScheduleID}">
                        <input id="schedule_team_id_${prevScheduleID}" type="hidden" name="team_id" value="${teamID}">
                        <input id="schedule_season_id_${prevScheduleID}" type="hidden" name="season_id" value="${seasonID}">
                        <input id="schedule_season_year_${prevScheduleID}" type="hidden" name="season_year" value="${seasonYear}">
                        <input id="schedule_home_id_${prevScheduleID}" type="hidden" name="home_id" value="${homeID}">
                        <input id="schedule_home_team_name_${prevScheduleID}" type="hidden" name="home_team_name" value="${homeName}">
                        <input id="schedule_home_score_${prevScheduleID}" type="hidden" name="home_score" value="${homeScore}">
                        <input id="schedule_away_id_${prevScheduleID}" type="hidden" name="away_id" value="${awayID}">
                        <input id="schedule_away_team_name_${prevScheduleID}" type="hidden" name="away_team_name" value="${awayName}">
                        <input id="schedule_away_score_${prevScheduleID}" type="hidden" name="away_score" value="${awayScore}">
                        <input id="schedule_date_${prevScheduleID}" type="hidden" name="date" value="${newDate}">
                        <input id="schedule_completion_${prevScheduleID}" type="hidden" name="completion" value="${compStatus}">
                    </div>
                </div>
            `);

            // Reattach button events.
            general.attachEvent($(".btn-edit"), toggleEditGamePopup);

            // Close popup.
            general.toggleElement(addPopup, popupOverlay);
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

        scheduleInputs["alter"][itemType].forEach((id) => {
            if (itemType === "schedule") data[`edit_${id}`] = $(`#edit_${id}`).val();
            else data[`${id}`] = $(`#${id}`).val();
        });

        $.ajax({
            url: url,
            type: method,
            data: JSON.stringify(data),
            success: function (response) {
                console.log(response);
                const data = JSON.parse(response);
                const status = data["status"];

                if (status === "fail") {
                    general.warnInputs(data, status);
                    return;
                }

                // Back to white.
                general.warnInputs(data, status);

                if (itemType === "team") {
                    setTeamColors([data["team_home_color"], data["team_away_color"]]);

                    const inTeamName = $("#team_name");
                    const newTeamName = inTeamName.val();
                    const oldTeamName = inTeamName.data("prev-team-name");
                    inTeamName.data("prev-team-name", newTeamName);

                    [...$(".home-team"), ...$(".away-team")].forEach((teamName) => {
                        if ($(teamName).text() === oldTeamName) {
                            $(teamName).text(newTeamName);
                        }
                    });
                } else if (itemType === "schedule") {
                    scheduleInputs["alter"][itemType].forEach((id) =>
                        $(`#${id}_${scheduleID}`).val(data[`edit_${id}`])
                    );

                    // Update each visual individually.
                    $(`.game-${scheduleID} .home-score`).text(data["edit_schedule_home_score"]);
                    $(`.game-${scheduleID} .away-score`).text(data["edit_schedule_away_score"]);
                    $(`#schedule_date_${scheduleID}`).val(data["edit_schedule_date"]);

                    const [css, icon] = getVisuals(data["edit_schedule_completion_status"]);

                    const statusSpan = $(`.game-${scheduleID} .span-completion-status`);
                    statusSpan.attr("class", `span-completion-status ${css}`);
                    statusSpan.data("completion-index", data["edit_schedule_completion_status"]);
                    $(`.game-${scheduleID} .status-icon`).attr("name", `${icon}-outline`);

                    general.toggleElement(editPopup, popupOverlay);
                }
            },
        });
    });
});

// DELETE BUTTONS
formDeleteBtns?.each((_, btn) => {
    $(btn).click(function (clickEvent) {
        clickEvent.preventDefault();

        const editPopup = $(this.closest(".popup-edit"));
        const form = $(this.closest("form"));
        const url = form.attr("action");
        const method = $(this).data("method");
        const itemType = $(this).data("item-type");

        const data = {};
        data["item_id"] = $(`#${itemType === "schedule" ? `edit_${itemType}` : itemType}_id`).val();
        data["item_type"] = itemType;

        $.ajax({
            url: url,
            type: method,
            data: JSON.stringify(data),
            success: function () {
                if (itemType === "team") {
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
                } else if (itemType === "schedule") {
                    const delGame = $(`.game-${data["item_id"]}`);
                    setCookie("last_deleted_game", delGame.data("schedule-id"));
                    delGame.remove();

                    const remainingGames = getOnlyGames();
                    if (remainingGames.length === 0) {
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

                    general.toggleElement(editPopup, popupOverlay);
                }
            },
        });
    });
});
