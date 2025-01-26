import * as general from "./general.js";
import { getCookie, setCookie } from "./helper/cookie.js";
import { adminInputs } from "./data/inputs.js";

// ***** DOM ELEMENTS ***** //
const popupOverlay = $(".popup-overlay");
const popupCloseBtns = $(".popup-btn-close");
const showPopupBtns = $(".btn-show");
const showPopups = $(".popup-show");
const cancelBtns = $(".btn-cancel");
const sidebarBtns = $(".btn-sidebar");
const noneSelectedDiv = $(".div-none-selected-container");
const dataContainerDivs = $(".div-data-container");
const scrollUsers = $(".users-scroll");
const scrollSports = $(".sports-scroll");
const scrollLeagues = $(".leagues-scroll");
const scrollSeasons = $(".seasons-scroll");
const scrollTeams = $(".teams-scroll");
const scrollPositions = $(".positions-scroll");
const rowDivs = $(".row-header-list");
const addNewBtns = $(".btn-add-new");
const viewBtns = $(".btn-view");
const updateBtns = $(".btn-update");
const deleteBtns = $(".btn-delete");

// ***** VARIABLES ***** //
const selectOptions = {
    Administrator: "1|Administrator",
    "League Manager": "2|League Manager",
    "Team Manager": "3|Team Manager",
    "Team Coach": "4|Team Coach",
    Fan: "5|Fan",
};

// ***** FUNCTIONS ***** //
const attachViewBtnEvent = function (btns) {
    btns.each((_, btn) => {
        $(btn).click(function () {
            setCookie("view_team_by_id", +$(this).attr("href").split("/")[2]);
        });
    });
};

const togglePopup = function () {
    let popupIndex = 0;
    if (this.classList[0].includes("close") || this.classList[2]?.includes("cancel")) {
        popupIndex = +$(this.closest(".popup-show")).data("popup-index");
    } else {
        popupIndex = +$(this).data("popup-index");
    }

    const relPopup = [...showPopups].find((popup) => popupIndex === +$(popup).data("popup-index"));
    general.toggleElement(relPopup, popupOverlay);
};

const getDataRowsOnly = function (scrollContainer) {
    let games = [];
    scrollContainer.children().each((_, div) => {
        const divClass = $(div).attr("class").split(" ")[0];
        if (divClass === "div-row-container") games.push(div);
    });

    return games;
};

const reloadWindow = function (seconds) {
    setTimeout(() => {
        location.reload();
    }, seconds * 1000);
};

const ajaxAdd = function (clickEvent) {
    clickEvent.preventDefault();

    const relPopup = $(this.closest(".popup-show"));
    const form = $(this.closest(".form"));
    const url = form.attr("action");
    const method = form.attr("method");
    const itemType = $(this).data("item-type");

    const data = {};
    data["item_type"] = itemType;
    adminInputs["add"][itemType].forEach((id) => {
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

            if (status === "fail") {
                general.warnInputs(data, status);
                return;
            }

            // Back to white.
            general.warnInputs(data, status);
            general.resetInput(data);

            let previousRowID = 0;

            if (itemType === "user") {
                // Checking for items.
                const noneAvailable = $(".div-none-available-container");
                if (noneAvailable) {
                    scrollUsers.removeClass("flex-center");
                    noneAvailable.remove();
                }

                previousRowID = +data["last_user_id"] || 0;
                if (getCookie("last_admin_user_id")) {
                    if (+getCookie("last_admin_user_id") < previousRowID) {
                        previousRowID = +getCookie("last_admin_user_id");
                    }
                }

                const username = data["new_username"];
                const roleID = data["new_user_role_id"];
                const roleName = data["new_user_role_name"];
                const roleOption = `${roleID}|${roleName}`;
                const leagueID = data["new_user_league_id"];
                const leagueName = data["new_user_league_name"];
                const teamID = data["new_user_team_id"];
                const teamName = data["new_user_team_name"];

                let options = "";
                for (const [key, value] of Object.entries(selectOptions)) {
                    const selected = roleOption === value ? "selected" : "";
                    options += `<option value="${value}" ${selected}>${key}</option>`;
                }

                scrollUsers.append(`
                    <div class='div-row-container user-row-container-${++previousRowID}' data-row-id='${previousRowID}'>
                        <ul class='row-header-list grid-4-columns'>
                            <li class='row-header-list-item'>
                                <p>${previousRowID}</p>
                            </li>
                            <li class='row-header-list-item'>
                                <p class='username'>${username}</p>
                            </li>
                            <li class='row-header-list-item'>
                                <p class='role-name' data-old-role-name="${roleName}">${roleName}</p>
                            </li>
                            <li class='row-header-list-item'>
                                <p>
                                    <ion-icon class='open-icon icon-${previousRowID}' name='chevron-down-outline'></ion-icon>
                                </p>
                            </li>
                        </ul>
                        <form class='form form-info form-${previousRowID} hide-element' action='/api/admin.php'>
                            <input id='user_id_${previousRowID}' type='hidden' name='user_id' value='${previousRowID}'>
                            <div class='div-multi-input-containers grid-2-columns'>
                                <div class='div-input-container required-container'>
                                    <label for='username_${previousRowID}'>Username:</label>
                                    <input id='username_${previousRowID}' type='text' name='username' value='${username}' autocomplete='off' required>
                                </div>
                                <div class='div-input-container required-container'>
                                    <label for='user_role_name_${previousRowID}'>Role Type:</label>
                                    <select id='user_role_name_${previousRowID}' name='role_name' value='${roleOption}' autocomplete='off' required>
                                        <option value=''>Select Role</option>
                                        ${options}
                                    </select>
                                </div>
                            </div>
                            <div class='div-multi-input-containers grid-4-columns'>
                                <div class='div-input-container'>
                                    <label for='user_league_name_${previousRowID}'>League:</label>
                                    <input id='user_league_name_${previousRowID}' type='text' name='league_name' value='${leagueName}' autocomplete='off' readonly>
                                </div>
                                <div class='div-input-container required-container'>
                                    <label for='user_league_id_${previousRowID}'>League ID:</label>
                                    <input id='user_league_id_${previousRowID}' type='number' name='league_id' value='${leagueID}' min='0' autocomplete='off' required>
                                </div>
                                <div class='div-input-container'>
                                    <label for='user_team_name_${previousRowID}'>Team:</label>
                                    <input id='user_team_name_${previousRowID}' type='text' name='team_name' value='${teamName}' autocomplete='off' readonly>
                                </div>
                                <div class='div-input-container required-container'>
                                    <label for='user_team_id_${previousRowID}'>Team ID:</label>
                                    <input id='user_team_id_${previousRowID}' type='number' name='team_id' value='${teamID}' min='0' autocomplete='off' required>
                                </div>
                            </div>
                            <div class='grid-btn-container'>
                                <button class='btn btn-hollow btn-delete' type='submit' data-method='DELETE' data-item-type='user'>Delete</button>
                                <button class='btn btn-full btn-update' type='submit' data-method='PUT' data-item-type='user'>Update</button>
                            </div>
                        </form>
                    </div>    
                `);
            } else if (itemType === "sport") {
                // Checking for items.
                const noneAvailable = $(".div-none-available-container");
                if (noneAvailable) {
                    scrollSports.removeClass("flex-center");
                    noneAvailable.remove();
                }

                previousRowID = +data["last_sport_id"] || 0;
                if (getCookie("last_admin_sport_id")) {
                    if (+getCookie("last_admin_sport_id") < previousRowID) {
                        previousRowID = +getCookie("last_admin_sport_id");
                    }
                }

                const sportName = data["new_sport_name"];

                scrollSports.append(`
                    <div class='div-row-container sport-row-container-${++previousRowID}' data-row-id='${previousRowID}'>
                        <ul class='row-header-list grid-3-columns'>
                            <li class='row-header-list-item'>
                                <p>${previousRowID}</p>
                            </li>
                            <li class='row-header-list-item'>
                                <p class='sport-name'>${sportName}</p>
                            </li>
                            <li class='row-header-list-item'>
                                <p>
                                    <ion-icon class='open-icon icon-${previousRowID}' name='chevron-down-outline'></ion-icon>
                                </p>
                            </li>
                        </ul>
                        <form class='form form-info form-${previousRowID} hide-element' action='/api/admin.php'>
                            <div class='div-multi-input-containers custom-2-column-grid'>
                                <div class='div-input-container required-container'>
                                    <label for='sport_name_${previousRowID}'>Sport Name:</label>
                                    <input id='sport_name_${previousRowID}' type='text' name='sport_name' value='${sportName}' autocomplete='off' required>
                                </div>
                                <div class='div-input-container'>
                                    <label for='sport_id_${previousRowID}'>Sport ID:</label>
                                    <input id='sport_id_${previousRowID}' type='text' name='sport_id' value='${previousRowID}' readonly>
                                </div>
                            </div>
                            <div class='grid-btn-container'>
                                <button class='btn btn-hollow btn-delete' type='submit' data-method='DELETE' data-item-type='sport'>Delete</button>
                                <button class='btn btn-full btn-update' type='submit' data-method='PUT' data-item-type='sport'>Update</button>
                            </div>
                        </form>
                    </div>
                `);
            } else if (itemType === "league") {
                // Checking for items.
                const noneAvailable = $(".div-none-available-container");
                if (noneAvailable) {
                    scrollLeagues.removeClass("flex-center");
                    noneAvailable.remove();
                }

                previousRowID = +data["last_league_id"] || 0;
                if (getCookie("last_admin_league_id")) {
                    if (+getCookie("last_admin_league_id") < previousRowID) {
                        previousRowID = +getCookie("last_admin_league_id");
                    }
                }

                const leagueName = data["new_league_name"];
                const sportID = data["new_league_sport_id"];
                const sportName = data["league_sport_name"];

                scrollLeagues.append(`
                    <div class='div-row-container league-row-container-${++previousRowID}' data-row-id='${previousRowID}'>
                        <ul class='row-header-list grid-3-columns'>
                            <li class='row-header-list-item'>
                                <p>${previousRowID}</p>
                            </li>
                            <li class='row-header-list-item'>
                                <p class='league-name'>${leagueName}</p>
                            </li>
                            <li class='row-header-list-item'>
                                <p>
                                    <ion-icon class='open-icon icon-${previousRowID}' name='chevron-down-outline'></ion-icon>
                                </p>
                            </li>
                        </ul>
                        <form class='form form-info form-${previousRowID} hide-element' action='/api/admin.php'>
                            <input id='league_id_${previousRowID}' type='hidden' name='league_id' value='${previousRowID}'>
                            <div class='div-multi-input-containers grid-3-columns'>
                                <div class='div-input-container required-container'>
                                    <label for='league_name_${previousRowID}'>League Name:</label>
                                    <input id='league_name_${previousRowID}' type='text' name='league_name' value='${leagueName}' autocomplete='off' required>
                                </div>
                                <div class='div-input-container'>
                                    <label for='league_sport_name_${previousRowID}'>Sport:</label>
                                    <input id='league_sport_name_${previousRowID}' type='text' name='sport_name' value='${sportName}' autocomplete='off' readonly>
                                </div>
                                <div class='div-input-container required-container'>
                                    <label for='league_sport_id_${previousRowID}'>Sport ID:</label>
                                    <input id='league_sport_id_${previousRowID}' type='number' name='sport_id' value='${sportID}' min='1' autocomplete='off' required>
                                </div>
                            </div>
                            <div class='grid-btn-container'>
                                <button class='btn btn-hollow btn-delete' type='submit' data-method='DELETE' data-item-type='league'>Delete</button>
                                <button class='btn btn-full btn-update' type='submit' data-method='PUT' data-item-type='league'>Update</button>
                            </div>
                        </form>
                    </div>
                `);
            } else if (itemType === "season") {
                // Checking for items.
                const noneAvailable = $(".div-none-available-container");
                if (noneAvailable) {
                    scrollSeasons.removeClass("flex-center");
                    noneAvailable.remove();
                }

                previousRowID = +data["last_season_id"] || 0;
                if (getCookie("last_admin_season_id")) {
                    if (+getCookie("last_admin_season_id") < previousRowID) {
                        previousRowID = +getCookie("last_admin_season_id");
                    }
                }

                const seasonYear = data["new_season_year"];
                const sportID = data["new_season_sport_id"];
                const sportName = data["season_sport_name"];
                const leagueID = data["new_season_league_id"];
                const leagueName = data["season_league_name"];
                const seasonDesc = data["new_season_desc"];

                scrollSeasons.append(`
                    <div class='div-row-container season-row-container-${++previousRowID}' data-row-id='${previousRowID}'>
                        <ul class='row-header-list grid-4-columns'>
                            <li class='row-header-list-item'>
                                <p>${previousRowID}</p>
                            </li>
                            <li class='row-header-list-item'>
                                <p class='season-year'>${seasonYear}</p>
                            </li>
                            <li class='row-header-list-item'>
                                <p class='season-desc'>${seasonDesc}</p>
                            </li>
                            <li class='row-header-list-item'>
                                <p>
                                    <ion-icon class='open-icon icon-${previousRowID}' name='chevron-down-outline'></ion-icon>
                                </p>
                            </li>
                        </ul>
                        <form class='form form-info form-${previousRowID} hide-element' action='/api/admin.php'>
                            <input id='season_id_${previousRowID}' type='hidden' name='season_id' value='${previousRowID}'>
                            <div class='div-multi-input-containers grid-2-columns'>
                                <div class='div-input-container required-container'>
                                    <label for='season_year_${previousRowID}'>Season Year:</label>
                                    <input id='season_year_${previousRowID}' type='text' name='season_year' value='${seasonYear}' autocomplete='off' required>
                                </div>
                                <div class='div-input-container required-container'>
                                    <label for='season_desc_${previousRowID}'>Season Description:</label>
                                    <input id='season_desc_${previousRowID}' type='text' name='season_desc' value='${seasonDesc}' autocomplete='off' required>
                                </div>
                            </div>
                            <div class='div-multi-input-containers grid-4-columns'>
                                <div class='div-input-container'>
                                    <label for='season_sport_name_${previousRowID}'>Sport:</label>
                                    <input id='season_sport_name_${previousRowID}' type='text' name='season_sport_name' value='${sportName}' readonly>
                                </div>
                                <div class='div-input-container required-container'>
                                    <label for='season_sport_id_${previousRowID}'>Sport ID:</label>
                                    <input id='season_sport_id_${previousRowID}' type='number' name='season_sport_id' value='${sportID}' min='1' required>
                                </div>
                                <div class='div-input-container'>
                                    <label for='season_league_name_${previousRowID}'>League:</label>
                                    <input id='season_league_name_${previousRowID}' type='text' name='season_league_name' value='${leagueName}' autocomplete='off' readonly>
                                </div>
                                <div class='div-input-container required-container'>
                                    <label for='season_league_id_${previousRowID}'>League ID:</label>
                                    <input id='season_league_id_${previousRowID}' type='number' name='season_league_id' value='${leagueID}' min='1' autocomplete='off' required>
                                </div>
                            </div>
                            <div class='grid-btn-container'>
                                <button class='btn btn-hollow btn-delete' type='submit' data-method='DELETE' data-item-type='season'>Delete</button>
                                <button class='btn btn-full btn-update' type='submit' data-method='PUT' data-item-type='season'>Update</button>
                            </div>
                        </form>
                    </div>
                `);
            } else if (itemType === "team") {
                // Checking for items.
                const noneAvailable = $(".div-none-available-container");
                if (noneAvailable) {
                    scrollTeams.removeClass("flex-center");
                    noneAvailable.remove();
                }

                previousRowID = +data["last_team_id"] || 0;
                if (getCookie("last_admin_team_id")) {
                    if (+getCookie("last_admin_team_id") < previousRowID) {
                        previousRowID = +getCookie("last_admin_team_id");
                    }
                }

                const teamName = data["new_team_name"];
                const sportID = data["new_team_sport_id"];
                const sportName = data["team_sport_name"];
                const leagueID = data["new_team_league_id"];
                const leagueName = data["team_league_name"];
                const seasonID = data["new_team_season_id"];
                const seasonYear = data["team_season_year"];
                const maxPlayers = data["new_team_max_players"];
                const homeColors = data["new_team_home_color"];
                const awayColors = data["new_team_away_color"];

                scrollTeams.append(`
                    <div class='div-row-container team-row-container-${++previousRowID}' data-row-id='${previousRowID}'>
                        <ul class='row-header-list grid-5-columns'>
                            <li class='row-header-list-item'>
                                <p>${previousRowID}</p>
                            </li>
                            <li class='row-header-list-item'>
                                <p class='team-name'>${teamName}</p>
                            </li>
                            <li class='row-header-list-item'>
                                <p class='league-name'>${leagueName}</p>
                            </li>
                            <li class='row-header-list-item'>
                                <p class='season-year'>${seasonYear}</p>
                            </li>
                            <li class='row-header-list-item'>
                                <p>
                                    <ion-icon class='open-icon icon-${previousRowID}' name='chevron-down-outline'></ion-icon>
                                </p>
                            </li>
                        </ul>
                        <form class='form form-info form-${previousRowID} hide-element' action='/api/admin.php'>
                            <input id='team_sport_id_${previousRowID}' type='hidden' name='sport_id' value='${sportID}'>
                            <div class='div-multi-input-containers custom-2-column-grid'>
                                <div class='div-input-container required-container'>
                                    <label for='team_name_${previousRowID}'>Team Name:</label>
                                    <input id='team_name_${previousRowID}' type='text' name='team_name' value='${teamName}' autocomplete='off' required>
                                </div>
                                <div class='div-input-container'>
                                    <label for='team_id_${previousRowID}'>Team ID:</label>
                                    <input id='team_id_${previousRowID}' type='number' name='team_id' value='${previousRowID}' readonly>
                                </div>
                            </div>
                            <div class='div-multi-input-containers grid-4-columns'>
                                <div class='div-input-container'>
                                    <label for='team_sport_name_${previousRowID}'>Sport:</label>
                                    <input id='team_sport_name_${previousRowID}' type='text' name='team_sport_name' value='${sportName}' readonly>
                                </div>
                                <div class='div-input-container required-container'>
                                    <label for='team_league_id_${previousRowID}'>League ID:</label>
                                    <input id='team_league_id_${previousRowID}' type='number' name='team_league_id' min='1' value='${leagueID}' autocomplete='off' required>
                                </div>
                                <div class='div-input-container required-container'>
                                    <label for='team_season_id_${previousRowID}'>Season ID:</label>
                                    <input id='team_season_id_${previousRowID}' type='number' name='team_season_id' min='1' value='${seasonID}' autocomplete='off' required>
                                </div>
                                <div class='div-input-container required-container'>
                                    <label for='team_max-players_${previousRowID}'>Max Players:</label>
                                    <input id='team_max-players_${previousRowID}' type='number' name='team_max_players' min='1' max='${maxPlayers}' value='${maxPlayers}' autocomplete='off' required>
                                </div>
                            </div>
                            <div class='div-multi-input-containers grid-2-columns'>
                                <div class='div-input-container required-container'>
                                    <label for='team_home_colors_${previousRowID}'>Home Colors:</label>
                                    <input id='team_home_colors_${previousRowID}' type='text' name='team_home_colors' value='${homeColors}' autocomplete='off' required>
                                </div>
                                <div class='div-input-container required-container'>
                                    <label for='team_away_colors_${previousRowID}'>Away Colors:</label>
                                    <input id='team_away_colors_${previousRowID}' type='text' name='team_away_colors' value='${awayColors}' autocomplete='off' required>
                                </div>
                            </div>
                            <div class='div-multi-input-containers grid-2-columns'>
                                <div class='grid-btn-container'>
                                    <button class='btn btn-hollow btn-delete' type='submit' data-method='DELETE' data-item-type='team'>Delete</button>
                                    <button class='btn btn-full btn-update' type='submit' data-method='PUT' data-item-type='team'>Update</button>
                                </div>
                                <div class='grid-btn-container'>
                                    <a class='btn btn-full btn-view' href='team.php?team_id=${previousRowID}'>View Team</a>
                                    <a class='btn btn-full btn-view' href='schedule.php?team_id=${previousRowID}'>View Schedule</a>
                                </div>
                            </div>
                        </form>
                    </div>
                `);

                attachViewBtnEvent($(".btn-view"));
            } else if (itemType === "position") {
                // Checking for items.
                const noneAvailable = $(".div-none-available-container");
                if (noneAvailable) {
                    scrollTeams.removeClass("flex-center");
                    noneAvailable.remove();
                }

                previousRowID = +data["last_position_id"] || 0;
                if (getCookie("last_admin_position_id")) {
                    if (+getCookie("last_admin_position_id") < previousRowID) {
                        previousRowID = +getCookie("last_admin_position_id");
                    }
                }

                console.log(data);

                const positionName = data["new_position_name"];
                const sportID = data["new_position_sport_id"];
                const sportName = data["position_sport_name"];

                scrollPositions.append(`
                    <div class='div-row-container position-row-container-${++previousRowID}' data-row-id='${previousRowID}'>
                        <ul class='row-header-list grid-3-columns'>
                            <li class='row-header-list-item'>
                                <p>${previousRowID}</p>
                            </li>
                            <li class='row-header-list-item'>
                                <p class='team-name'>${positionName}</p>
                            </li>
                            <li class='row-header-list-item'>
                                <p>
                                    <ion-icon class='open-icon icon-${previousRowID}' name='chevron-down-outline'></ion-icon>
                                </p>
                            </li>
                        </ul>
                        <form class='form form-info form-${previousRowID} hide-element' action='/api/admin.php'>
                            <input id='position_id_${previousRowID}' type='hidden' name='position_id' value='${previousRowID}'>
                            <div class='div-multi-input-containers grid-3-columns'>
                                <div class='div-input-container required-container'>
                                    <label for='position_name_${previousRowID}'>Team Name:</label>
                                    <input id='position_name_${previousRowID}' type='text' name='position_name' value='${positionName}' autocomplete='off' required>
                                </div>
                                <div class='div-input-container'>
                                    <label for='position_sport_name_${previousRowID}'>Sport:</label>
                                    <input id='position_sport_name_${previousRowID}' type='text' name='sport_name' value='${sportName}' readonly>
                                </div>
                                <div class='div-input-container required-container'>
                                    <label for='position_sport_id_${previousRowID}'>Sport ID:</label>
                                    <input id='position_sport_id_${previousRowID}' type='number' name='sport_id' value='${sportID}' min='1' required>
                                </div>
                            </div>
                            <div class='grid-btn-container'>
                                <button class='btn btn-hollow btn-delete' type='submit' data-method='DELETE' data-item-type='position'>Delete</button>
                                <button class='btn btn-full btn-update' type='submit' data-method='PUT' data-item-type='position'>Update</button>
                            </div>
                        </form>
                    </div>
                `);
            }

            general.attachEvent($(".row-header-list"), general.toggleDropdown);

            // Reattach ajax events.
            general.attachEvent($(".btn-update"), ajaxUpdate);
            general.attachEvent($(".btn-delete"), ajaxDelete);

            // Hide popup.
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
    data["item_type"] = itemType;
    data["item_id"] = rowID;
    adminInputs["alter"][itemType].forEach((id) => {
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

            // Guard clause.
            if (status === "fail") {
                general.warnInputs(data, status);
                return;
            }

            // Back to white.
            general.warnInputs(data, status);

            if (itemType === "user") {
                const username = data[`username_${rowID}`];
                $(`.${relContainerClass} .username`).text(username);
                if (rowID === +getCookie("user_id")) {
                    setCookie("new_username", username);
                    $(".span-username").text(username);
                }

                const newRoleID = +data["role_id"];
                const oldRoleID = $(`.${relContainerClass} .role-name`).data("old-role-id");
                $(`.${relContainerClass} .role-name`).data("old-role-id", newRoleID);

                if (rowID === +getCookie("user_id")) {
                    if (oldRoleID !== newRoleID) {
                        setCookie("new_role_id", newRoleID);
                        reloadWindow(1);
                    }
                }

                $(`.${relContainerClass} .role-name`).text(data[`user_role_name_${rowID}`]);
                $(`#user_league_name_${rowID}`).val(data[`user_league_name_${rowID}`]);
                $(`#user_team_name_${rowID}`).val(data[`user_team_name_${rowID}`]);
            } else if (itemType === "sport") {
                $(`.${relContainerClass} .sport-name`).text(data[`sport_name_${rowID}`]);

                reloadWindow(1);
            } else if (itemType === "league") {
                $(`.${relContainerClass} .league-name`).text(data[`league_name_${rowID}`]);
                $(`#league_sport_name_${rowID}`).val(data[`league_sport_name_${rowID}`]);

                reloadWindow(1);
            } else if (itemType === "season") {
                $(`.${relContainerClass} .season-year`).text(data[`season_year_${rowID}`]);
                $(`.${relContainerClass} .season-desc`).text(data[`season_desc_${rowID}`]);
                $(`#season_sport_name_${rowID}`).val(data[`season_sport_name_${rowID}`]);
                $(`#season_league_name_${rowID}`).val(data[`season_league_name_${rowID}`]);

                reloadWindow(1);
            } else if (itemType === "team") {
                $(`.${relContainerClass} .team-name`).text(data[`team_name_${rowID}`]);
                $(`.${relContainerClass} .league-name`).text(data[`team_league_name_${rowID}`]);
                $(`.${relContainerClass} .season-year`).text(data[`team_season_year_${rowID}`]);

                reloadWindow(1);
            } else if (itemType === "position") {
                $(`.${relContainerClass} .position-name`).text(data[`position_name_${rowID}`]);
                $(`#position_sport_name_${rowID}`).val(data[`position_sport_name_${rowID}`]);
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
    data["item_type"] = itemType;
    data["item_id"] = rowID;

    $.ajax({
        url: url,
        type: method,
        data: JSON.stringify(data),
        success: function (response) {
            console.log(response);
            let scrollContainer;
            let members = [];

            if (itemType === "user") {
                if (rowID === +getCookie("user_id")) {
                    window.open("logout", "_self");
                    return;
                }

                scrollContainer = scrollUsers;
            } else if (itemType === "sport") {
                scrollContainer = scrollSports;
            } else if (itemType === "league") {
                scrollContainer = scrollLeagues;
            } else if (itemType === "season") {
                scrollContainer = scrollSeasons;
            } else if (itemType === "team") {
                scrollContainer = scrollTeams;
            } else if (itemType === "position") {
                scrollContainer = scrollPositions;
            }

            if (itemType !== "user" && itemType !== "position") reloadWindow(1);

            members = getDataRowsOnly(scrollContainer);
            setCookie(`last_admin_${itemType}_id`, $(members[members.length - 1]).data("row-id"));
            // setCookie(`last_${cookieKey}_id`, relContainer.data("row-id"));
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
[...showPopupBtns, ...cancelBtns, ...popupCloseBtns].forEach((btn) => {
    $(btn).click(togglePopup);
});

sidebarBtns.each((_, btn) => {
    $(btn).click(function () {
        if (!noneSelectedDiv.hasClass("hide-element")) {
            noneSelectedDiv.addClass("hide-element");
        }

        sidebarBtns.each((_, btn) => {
            $(btn).removeClass("active-btn");
        });

        $(this).addClass("active-btn");

        const currContainerIndex = +$(this).data("container-index");
        dataContainerDivs.each((_, div) => {
            const relContainerIndex = +$(div).data("container-index");
            if (relContainerIndex === currContainerIndex) {
                $(div).removeClass("hide-element");
            } else {
                if (!$(div).hasClass("hide-element")) {
                    $(div).addClass("hide-element");
                }
            }
        });
    });
});

// Dropdown event.
general.attachEvent(rowDivs, general.toggleDropdown);

// Ajax events.
general.attachEvent(addNewBtns, ajaxAdd);
general.attachEvent(updateBtns, ajaxUpdate);
general.attachEvent(deleteBtns, ajaxDelete);

// Set team cookie event.
attachViewBtnEvent(viewBtns);
