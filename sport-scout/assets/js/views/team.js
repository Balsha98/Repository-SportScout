import * as general from "../helper/general.js";
import { getCookie, setCookie } from "../helper/cookie.js";
import { teamInputs } from "../data/inputs.js";

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

// ***** VARIABLES ***** //
const selectOptions = {
    "Team Manager": "3|Team Manager",
    "Team Coach": "4|Team Coach",
    Fan: "5|Fan",
};

// ***** FUNCTIONS ***** //
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

    const relPopup = $(this.closest(".popup-add"));
    const form = $(this.closest(".form"));
    const url = form.attr("action");
    const method = form.attr("method");
    const itemType = $(this).data("item-type");

    const data = {};
    data["item_type"] = itemType;
    teamInputs["add"][itemType].forEach((id) => {
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
            console.log(data);

            if (status === "fail") {
                general.warnInputs(data, status);
                return;
            }

            // Back to white.
            general.warnInputs(data, status);
            general.resetInput(data);

            let previousRowID;

            if (itemType === "player") {
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

                const sportID = data["sport_id"];
                const teamID = data["team_id"];
                const leagueName = data["league_name"];
                const playerFirst = data["new_player_first"];
                const playerLast = data["new_player_last"];
                const fullName = `${playerFirst} ${playerLast}`;
                const playerDOB = data["new_player_dob"];
                const positionID = data["new_player_position_id"];
                const positionName = data["new_player_position_name"];
                const positionOption = `${positionID}|${positionName}`;

                let distinctPositions = "";
                for (const obj of data["distinct_positions"]) {
                    const currOption = `${obj["position_id"]}|${obj["position_name"]}`;
                    const selected = positionOption === currOption ? "selected" : "";

                    distinctPositions += `
                        <option value="${currOption}" ${selected}>
                            ${obj["position_name"]}
                        </option>
                    `;
                }

                const jerseyNumber = data["new_player_jersey_number"];

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
                        <form class='form form-info form-${previousRowID} hide-element' action='/api/team.php'>
                            <input id='player_sport_id_${previousRowID}' type='hidden' name='sport_id' value='${sportID}'>
                            <input id='player_team_id_${previousRowID}' type='hidden' name='team_id' value='${teamID}'>
                            <input id='player_id_${previousRowID}' type='hidden' name='player_id' value='${previousRowID}'>
                            <div class='div-multi-input-containers grid-2-columns'>
                                <div class='div-input-container required-container'>
                                    <label for='player_first_${previousRowID}'>First Name:</label>
                                    <input id='player_first_${previousRowID}' type='text' name='player_first' value='${playerFirst}' autocomplete='off' required>
                                </div>
                                <div class='div-input-container required-container'>
                                    <label for='player_last_${previousRowID}'>Last Name:</label>
                                    <input id='player_last_${previousRowID}' type='text' name='player_last' value='${playerLast}' autocomplete='off' required>
                                </div>
                            </div>
                            <div class='div-multi-input-containers grid-2-columns'>
                                <div class='div-input-container'>
                                    <label for='league_name_${previousRowID}'>League:</label>
                                    <input id='league_name_${previousRowID}' type='text' name='league_name' value='${leagueName}' readonly>
                                </div>
                                <div class='div-input-container required-container'>
                                    <label for='player_dob_${previousRowID}'>Date of Birth:</label>
                                    <input id='player_dob_${previousRowID}' type='date' name='player_dob' value='${playerDOB}' autocomplete='off' required>
                                </div>
                            </div>
                            <div class='div-multi-input-containers grid-2-columns'>
                                <div class='div-input-container required-container'>
                                    <label for='player_position_id_${previousRowID}'>Position:</label>
                                    <select id='player_position_id_${previousRowID}' name='position_id' autocomplete='off' required>
                                        <option value=''>Select Position</option>
                                        ${distinctPositions}
                                    </select>
                                </div>
                                <div class='div-input-container required-container'>
                                    <label for='player_jersey_${previousRowID}'>Jersey:</label>
                                    <input id='player_jersey_${previousRowID}' type='number' name='player_jersey' min='0' value='${jerseyNumber}' required>
                                </div>
                            </div>
                            <div class='grid-btn-container'>
                                <button 
                                    class='btn btn-hollow btn-delete' 
                                    type='submit' 
                                    data-method='DELETE' 
                                    data-item-type='player'
                                >Delete</button>
                                <button 
                                    class='btn btn-full btn-update' 
                                    type='submit' 
                                    data-method='PUT' 
                                    data-item-type='player'
                                >Update</button>
                            </div>
                        </form>
                    </div>
                `);
            } else if (itemType === "user") {
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
                const roleID = data["new_user_role_id"];
                const roleName = data["new_user_role_name"];
                const roleOption = `${roleID}|${roleName}`;
                const leagueName = data["new_user_league_name"];
                const leagueID = data["new_user_league_id"];
                const teamID = data["new_user_team_id"];

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
                        <form class='form form-info form-${previousRowID} hide-element' action='/api/team.php'>
                            <input id='staff_id_${previousRowID}' type='hidden' name='staff_id' value='${previousRowID}'>
                            <input id='staff_league_id_${previousRowID}' type='hidden' name='league_id' value='${leagueID}'>
                            <div class='div-multi-input-containers grid-2-columns'>
                                <div class='div-input-container required-container'>
                                    <label for='staff_username_${previousRowID}'>Username:</label>
                                    <input id='staff_username_${previousRowID}' type='text' name='username' value='${username}' autocomplete='off' required>
                                </div>
                                <div class='div-input-container required-container'>
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
                                    type='submit' 
                                    data-method='DELETE' 
                                    data-item-type='user'
                                >Delete</button>
                                <button 
                                    class='btn btn-full btn-update' 
                                    type='submit' 
                                    data-method='PUT' 
                                    data-item-type='user'
                                >Update</button>
                            </div>
                        </form>
                    </div>
                `);
            }

            // Set NEW dropdown events.
            general.attachEvent($(".row-header-list"), general.toggleDropdown);

            // Set NEW AJAX events.
            general.attachEvent($(".btn-update"), ajaxUpdate);
            general.attachEvent($(".btn-delete"), ajaxDelete);

            // Close popup.
            general.toggleElement(relPopup, popupOverlay);
        },
    });
};

const ajaxUpdate = function (clickEvent) {
    clickEvent.preventDefault();

    const relContainer = $(this.closest(".div-row-container"));
    const relContainerClass = relContainer.attr("class").split(" ")[1];
    const rowID = +relContainer.data("row-id");
    const form = $(this.closest(".form"));
    const url = form.attr("action");
    const method = $(this).data("method");
    const itemType = $(this).data("item-type");

    const data = {};
    data["item_id"] = rowID;
    data["item_type"] = itemType;
    teamInputs["alter"][itemType].forEach((id) => {
        data[`${id}_${rowID}`] = $(`#${id}_${rowID}`).val();
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

            if (itemType === "player") {
                const fullName = `${data[`player_first_${rowID}`]} ${data[`player_last_${rowID}`]}`;
                $(`.${relContainerClass} .full-name`).text(fullName);

                $(`.${relContainerClass} .position-name`).text(data["player_position_name"]);
            } else if (itemType === "user") {
                $(`.${relContainerClass} .staff-name`).text(data[`staff_username_${rowID}`]);
                $(`.${relContainerClass} .staff-role`).text(data[`staff_role_name_${rowID}`]);
            }
        },
    });
};

const ajaxDelete = function (clickEvent) {
    clickEvent.preventDefault();

    const relContainer = $(this.closest(".div-row-container"));
    const rowID = +relContainer.data("row-id");
    const form = $(this.closest(".form"));
    const url = form.attr("action");
    const method = $(this).data("method");
    const itemType = $(this).data("item-type");

    const data = {};
    data["item_id"] = rowID;
    data["item_type"] = itemType;

    $.ajax({
        url: url,
        type: method,
        data: JSON.stringify(data),
        success: function () {
            let members = [];
            const scrollContainer = itemType === "player" ? scrollPlayers : scrollStaff;

            if (itemType === "user") {
                if (+getCookie("user_id") === rowID) {
                    window.open("logout", "_self");
                }
            }

            members = getDataRowsOnly(scrollContainer);
            setCookie(`last_team_${itemType}_id`, $(members[members.length - 1]).data("row-id"));
            // Getting the last deleted row makes no sense... it kind of does.

            relContainer.remove();

            members = getDataRowsOnly(scrollContainer);
            if (members.length === 0) {
                scrollContainer.addClass("flex-center");
                scrollContainer.append(`
                    <div class='div-none-available-container'>
                        <ion-icon class='none-available-icon' name='alert-circle-outline'></ion-icon>
                        <div class='none-available-text'>
                            <h2>No ${itemType}s at the moment.</h2>
                            <p>Start off by adding a <span>new</span> ${itemType} to the team.</p>
                        </div>
                    </div>
                `);
            }
        },
    });
};

// ***** EVENT LISTENERS ***** //
[...showPopupBtns, ...cancelBtns, ...closePopupBtns].forEach((btn) => {
    $(btn)?.click(function () {
        general.togglePopup(this, showPopups, popupOverlay);
    });
});

// DROPDOWN EVENTS
general.attachEvent(rowDivs, general.toggleDropdown);

// AJAX EVENTS
general.attachEvent(addNewBtns, ajaxAdd);
general.attachEvent(updateBtns, ajaxUpdate);
general.attachEvent(deleteBtns, ajaxDelete);
