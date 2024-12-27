"use strict";

// ***** DOM ELEMENTS ***** //
const dashboardGrid = query(".grid-dashboard");

// In case the user is logged in.
const btnStart = query(".btn-start");
const btnLogOut = query(".btn-logout");
if (getCookie("user")) switchBtns(btnStart);

const welcomeContainer = query(".div-welcome-info-container");
const pagesContainer = query(".div-pages-container");
const pagesHeroContainer = query(".div-pages-hero-container");
const pagesListContainer = query(".div-pages-list-container");
const pagesListItems = queryAll(".pages-list-item");
const dateText = query(".date-text");

// ***** VARIABLES ***** //
// prettier-ignore
const days = [
    "Sunday", "Monday", "Tuesday", 
    "Wednesday", "Thursday", "Friday", "Saturday",
];

// prettier-ignore
const months = [
    "January", "February", "March", "April",
    "May", "June", "July", "August",
    "September", "October","November", "December",
];

// ***** FUNCTIONS ***** //
function query(idClass) {
    return document.querySelector(idClass);
}

function queryAll(idClass) {
    return document.querySelectorAll(idClass);
}

function setDate(date = new Date()) {
    const seconds = date.getSeconds(),
        minutes = date.getMinutes(),
        hours = date.getHours(),
        wDay = days[date.getDay()],
        mDay = date.getDate(),
        month = months[date.getMonth()],
        year = date.getFullYear();

    let suffix = "";
    switch (mDay) {
        case 1:
            suffix = "st";
            break;
        case 2:
            suffix = "nd";
            break;
        case 3:
            suffix = "rd";
            break;
        default:
            suffix = "th";
    }

    // prettier-ignore
    dateText.innerHTML = `
        <span>
            ${wDay}, ${month} ${mDay}<sup>${suffix}</sup> ${year}.
        </span>  
        <span>
            <small>${hours < 10 ? `0${hours}` : hours}</small>:` + 
            `<small>${minutes < 10 ? `0${minutes}` : minutes}</small>:` +
            `<small>${seconds < 10 ? `0${seconds}` : seconds}</small>
        </span>
    `;
}

function switchBtns(btn) {
    // Switch divs.
    dashboardGrid.classList.add("switch-divs");
    btnLogOut.classList.add("span-across-all");

    // Remove button.
    btn.remove();
}

setInterval(setDate, 1000);
setDate();

// ***** EVENT LISTENERS ***** //
btnStart?.addEventListener("click", function () {
    setCookie("user", "active");
    switchBtns(this);
});

pagesListItems.forEach((item, i) => {
    item.addEventListener("click", function () {
        // Get page link item.
        const pageLinks = queryAll(".page-link");
        window.open(pageLinks[i].getAttribute("href"), "_self");
    });
});
