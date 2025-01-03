"use strict";

// ***** DOM ELEMENTS ***** //
const popupOverlay = $(".popup-overlay");
const showPopups = $(".popup-show");
const closePopupBtns = $(".popup-btn-close");
const showPopupBtns = $(".btn-show");
const cancelBtns = $(".btn-cancel");
const scrollPlayers = $(".players-scroll");
const scrollStaff = $(".staff-scroll");
const rowDivs = $(".row-header-list");
const addNewBtns = $(".btn-add-new");
const updateBtns = $(".btn-update");
const deleteBtns = $(".btn-delete");

// STAFF SELECT OPTIONS
const selectOptions = {
    "Team Manager": "3|Team Manager",
    "Team Coach": "4|Team Coach",
    Fan: "5|Fan",
};

// ***** FUNCTIONS ***** //
const killAllEventListeners = function (el) {
    $(el).off();
};

const attachDropdownEvent = function (btns) {
    btns?.each((_, btn) => {
        killAllEventListeners(btn);
        $(btn).click(toggleDropdown);
    });
};

const attachAjaxEvent = function (btns, callback) {
    btns?.each((_, btn) => {
        killAllEventListeners(btn);
        $(btn).click(callback);
    });
};

const showHideEl = function (popup) {
    [popup, popupOverlay].forEach((el) => {
        $(el).toggleClass("hide-element");
    });
};

const toggleDropdown = function (clickEvent) {
    const { target } = clickEvent;

    // Get row container.
    let parent = $(target.closest(".div-row-container"));
    const rowID = +parent.data("row-id");

    // Get scroll container.
    parent = $(parent.closest(".div-scroll-container"));
    const scrollClass = parent.attr("class").split(" ")[1];

    // Get child form.
    const form = $(`.${scrollClass} .form-${rowID}`);
    form.toggleClass("hide-element");

    // Get child icon.
    const icon = $(`.${scrollClass} .icon-${rowID}`);
    icon.toggleClass("rotate");
};

const togglePopup = function () {
    let popupIndex = 0;

    if (this.classList[0].includes("close") || this.classList[2]?.includes("cancel")) {
        popupIndex = +$(this.closest(".popup-show")).data("popup-index");
    } else {
        popupIndex = +$(this).data("popup-index");
    }

    const relPopup = [...showPopups]?.find((popup) => popupIndex === +$(popup).data("popup-index"));

    showHideEl(relPopup);
};

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
                $(`#${key}`)?.closest(".div-input-container").addClass("red-container");

                continue;
            }
        }

        $(`#${key}`)?.closest(".div-input-container").removeClass("red-container");
    }
};

const getDataRowsOnly = function (scrollContainer) {
    let games = [];
    scrollContainer.children().each((_, div) => {
        const divClass = $(div).attr("class").split(" ")[0];
        if (divClass === "div-row-container") games.push(div);
    });

    return games;
};

const ajaxAdd = function (clickEvent) {
    clickEvent.preventDefault();

    const clickedBtn = $(this).data("clicked");
    const relPopup = $(this.closest(".popup-add"));
    const form = $(this.closest(".form"));

    $.ajax({
        url: form.attr("action"),
        type: $(this).data("method"),
        data: `${form.serialize()}&clicked=${clickedBtn}`,
        success: function (response) {
            const data = JSON.parse(response);
            const message = data["message"];

            if (message === "fail") {
                warnInputs(data, message);
                return;
            }

            // Back to white.
            warnInputs(data, message);

            let previousRowID;

            if (clickedBtn === "ADD_PLAYER") {
                const scrollClass = scrollPlayers.attr("class").split(" ")[1];
                const noneAvailableDiv = $(`.${scrollClass} .div-none-available-container`);
                if (noneAvailableDiv) {
                    scrollPlayers.removeClass("flex-center");
                    noneAvailableDiv.remove();
                }

                previousRowID = +data["last_player_id"] || 0;
                if (getCookie("last_team_player_id")) {
                    if (+getCookie("last_team_player_id") < previousRowID) {
                        previousRowID = +getCookie("last_team_player_id");
                    }
                }

                const teamID = data["team_id"];
                const leagueName = data["league_name"];
                const playerFirst = data["new_player_first"];
                const playerLast = data["new_player_last"];
                const fullName = `${playerFirst} ${playerLast}`;
                const playerDOB = data["new_player_dob"];
                const positionName = data["new_position_name"];
                const jerseyNumber = data["new_jersey_number"];

                scrollPlayers.append(`
                    <div class='div-row-container row-container-${++previousRowID}' data-row-id='${previousRowID}'>
                        <ul class='row-header-list custom-auto-3-column-grid'>
                            <li class='row-header-list-item'>
                                <p>${fullName}</p>
                            </li>
                            <li class='row-header-list-item'>
                                <p>${positionName}</p>
                            </li>
                            <li class='row-header-list-item'>
                                <p>
                                    <ion-icon class='open-icon icon-${previousRowID}' name='chevron-down-outline'></ion-icon>
                                </p>
                            </li>
                        </ul>
                        <form class='form form-info form-${previousRowID} hide-element' action='../process/process-team.php'>
                            <input type='hidden' name='player_id' value='${previousRowID}'>
                            <div class='div-multi-input-containers grid-2-columns'>
                                <div class='div-input-container'>
                                    <label for='player_first_${previousRowID}'>First Name:</label>
                                    <input id='player_first_${previousRowID}' type='text' name='player_first' value='${playerFirst}' autocomplete='off' required>
                                </div>
                                <div class='div-input-container'>
                                    <label for='player_last_${previousRowID}'>Last Name:</label>
                                    <input id='player_last_${previousRowID}' type='text' name='player_last' value='${playerLast}' autocomplete='off' required>
                                </div>
                            </div>
                            <div class='div-multi-input-containers grid-3-columns'>
                                <div class='div-input-container'>
                                    <label for='league_name_${previousRowID}'>League:</label>
                                    <input id='league_name_${previousRowID}' type='text' name='league_name' value='${leagueName}' readonly>
                                    <input type='hidden' name='team_id' value='${teamID}'>
                                </div>
                                <div class='div-input-container'>
                                    <label for='player_dob_${previousRowID}'>Date of Birth:</label>
                                    <input id='player_dob_${previousRowID}' type='date' name='player_dob' value='${playerDOB}' autocomplete='off' required>
                                </div>
                                <div class='div-input-container'>
                                    <label for='player_jersey_${previousRowID}'>Jersey:</label>
                                    <input id='player_jersey_${previousRowID}' type='number' name='player_jersey' min='0' value='${jerseyNumber}' required>
                                </div>
                            </div>
                            <div class='grid-btn-container'>
                                <button 
                                    class='btn btn-hollow btn-delete' 
                                    type='submit' data-method='POST' 
                                    data-clicked='DELETE_PLAYER'
                                >Delete</button>
                                <button 
                                    class='btn btn-full btn-update' 
                                    type='submit' 
                                    data-method='POST' 
                                    data-clicked='UPDATE_PLAYER'
                                >Update</button>
                            </div>
                        </form>
                    </div>
                `);
            } else if (clickedBtn === "ADD_STAFF") {
                const scrollClass = scrollStaff.attr("class").split(" ")[1];
                const noneAvailableDiv = $(`.${scrollClass} .div-none-available-container`);
                if (noneAvailableDiv) {
                    scrollStaff.removeClass("flex-center");
                    noneAvailableDiv.remove();
                }

                previousRowID = +data["last_user_id"] || 0;
                if (getCookie("last_team_member_id")) {
                    if (+getCookie("last_team_member_id") < previousRowID) {
                        previousRowID = +getCookie("last_team_member_id");
                    }
                }

                const username = data["new_username"];
                const roleID = data["new_role_id"];
                const roleName = data["new_role_name"];
                const roleOption = `${roleID}|${roleName}`;
                const leagueName = data["league_name"];
                const leagueID = data["league_id"];
                const teamID = data["team_id"];

                let options = "";
                for (const [key, value] of Object.entries(selectOptions)) {
                    const selected = roleOption === value ? "selected" : "";
                    options += `<option value="${value}" ${selected}>${key}</option>`;
                }

                scrollStaff.append(`
                    <div class='div-row-container row-container-${++previousRowID}' data-row-id='${previousRowID}'>
                        <ul class='row-header-list custom-auto-3-column-grid'>
                            <li class='row-header-list-item'>
                                <p>${username}</p>
                            </li>
                            <li class='row-header-list-item'>
                                <p>${roleName}</p>
                            </li>
                            <li class='row-header-list-item'>
                                <p>
                                    <ion-icon class='open-icon icon-${previousRowID}' name='chevron-down-outline'></ion-icon>
                                </p>
                            </li>
                        </ul>
                        <form class='form form-info form-${previousRowID} hide-element' action='../process/process-team.php'>
                            <input type='hidden' name='staff_id' value='${previousRowID}'>
                            <input type='hidden' name='league_id' value='${leagueID}'>
                            <div class='div-multi-input-containers grid-2-columns'>
                                <div class='div-input-container'>
                                    <label for='staff_username_${previousRowID}'>Username:</label>
                                    <input id='staff_username_${previousRowID}' type='text' name='username' value='${username}' autocomplete='off' required>
                                </div>
                                <div class='div-input-container'>
                                    <label for='staff_role_name_${previousRowID}'>Role:</label>
                                    <select id='staff_role_name_${previousRowID}' name='role_name' autocomplete='off' required>
                                        <option value=''>Select Role</option>
                                        ${options}
                                    </select>
                                </div>
                            </div>
                            <div class='div-multi-input-containers custom-2-column-grid'>
                                <div class='div-input-container'>
                                    <label for='staff_league_name_${previousRowID}'>League:</label>
                                    <input id='staff_league_name_${previousRowID}' type='text' name='league_name' value='${leagueName}' readonly>
                                </div>
                                <div class='div-input-container'>
                                    <label for='staff_team_id_${previousRowID}'>Team ID:</label>
                                    <input id='staff_team_id_${previousRowID}' type='number' name='team_id' value='${teamID}' readonly>
                                </div>
                            </div>
                            <div class='grid-btn-container'>
                                <button 
                                    class='btn btn-hollow btn-delete' 
                                    type='submit' data-method='POST' 
                                    data-clicked='DELETE_STAFF'
                                >Delete</button>
                                <button 
                                    class='btn btn-full btn-update' 
                                    type='submit' 
                                    data-method='POST' 
                                    data-clicked='UPDATE_STAFF'
                                >Update</button>
                            </div>
                        </form>
                    </div>
                `);
            }

            // Refresh events.
            attachDropdownEvent($(".row-header-list"));
            attachAjaxEvent($(".btn-update"), ajaxUpdate);
            attachAjaxEvent($(".btn-delete"), ajaxDelete);

            // Close popup.
            showHideEl(relPopup);
        },
    });
};

const ajaxUpdate = function (clickEvent) {
    clickEvent.preventDefault();

    const clickedBtn = $(this).data("clicked");
    const relContainer = $(this.closest(".div-row-container"));
    const relContainerClass = relContainer.attr("class").split(" ")[1];
    const form = $(this.closest(".form"));

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

            if (clickedBtn === "UPDATE_PLAYER") {
                const playerID = data["player_id"];
                const playerFirst = data[`player_first_${playerID}`];
                const playerLast = data[`player_last_${playerID}`];
                const fullName = `${playerFirst} ${playerLast}`;
                const positionName = data["player_position_name"];

                $(`.${relContainerClass} .full-name`).text(fullName);
                $(`.${relContainerClass} .position-name`).text(positionName);
            } else if (clickedBtn === "UPDATE_STAFF") {
                const staffID = data["staff_id"];
                const staffName = data[`staff_username_${staffID}`];
                const staffRole = data[`staff_role_name_${staffID}`];

                // Update row header.
                $(`.${relContainerClass} .staff-name`).text(staffName);
                $(`.${relContainerClass} .staff-role`).text(staffRole);
            }
        },
    });
};

const ajaxDelete = function (clickEvent) {
    clickEvent.preventDefault();

    const clickedBtn = $(this).data("clicked");
    const relContainer = $(this.closest(".div-row-container"));
    const form = $(this.closest(".form"));

    $.ajax({
        url: form.attr("action"),
        type: $(this).data("method"),
        data: `${form.serialize()}&clicked=${clickedBtn}`,
        success: function () {
            let members = [];
            let scrollContainer;
            let cookieKey;

            if (clickedBtn === "DELETE_PLAYER") {
                scrollContainer = scrollPlayers;
                cookieKey = "player";
            } else if (clickedBtn === "DELETE_STAFF") {
                scrollContainer = scrollStaff;
                cookieKey = "member";

                const delRowID = +relContainer.data("row-id");
                if (+getCookie("user_id") === delRowID) {
                    window.open("logout.php", "_self");
                }
            }

            members = getDataRowsOnly(scrollContainer);
            setCookie(`last_team_${cookieKey}_id`, $(members[members.length - 1]).data("row-id"));
            // Getting the last deleted row makes no sense... it kind of does.

            relContainer.remove();

            members = getDataRowsOnly(scrollContainer);
            if (members.length === 0) {
                scrollContainer.addClass("flex-center");
                scrollContainer.append(`
                    <div class='div-none-available-container'>
                        <ion-icon class='none-available-icon' name='alert-circle-outline'></ion-icon>
                        <div class='none-available-text'>
                            <h2>No ${cookieKey}s at the moment.</h2>
                            <p>Start off by adding a <span>new</span> ${cookieKey} to the team.</p>
                        </div>
                    </div>
                `);
            }
        },
    });
};

// ***** EVENT LISTENERS ***** //
[...showPopupBtns, ...cancelBtns, ...closePopupBtns].forEach((btn) => {
    $(btn)?.click(togglePopup);
});

// DROPDOWN EVENTS
attachDropdownEvent(rowDivs);

// AJAX EVENTS
attachAjaxEvent(addNewBtns, ajaxAdd);
attachAjaxEvent(updateBtns, ajaxUpdate);
attachAjaxEvent(deleteBtns, ajaxDelete);
