"use strict";

// ***** DOM ELEMENTS ***** //
const dashboardGrid = $(".grid-dashboard");
const startBtn = $(".btn-start");
const logoutBtn = $(".btn-logout");
const welcomeContainer = $(".div-welcome-info-container");
const pagesContainer = $(".div-pages-container");
const pagesHeroContainer = $(".div-pages-hero-container");
const pagesListContainer = $(".div-pages-list-container");
const pagesListItems = $(".pages-list-item");
const dateText = $(".date-text");

// ***** VARIABLES ***** //
const days = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
const months = [
    "January",
    "February",
    "March",
    "April",
    "May",
    "June",
    "July",
    "August",
    "September",
    "October",
    "November",
    "December",
];

// ***** FUNCTIONS ***** //
const setDate = function (date = new Date()) {
    const seconds = date.getSeconds();
    const minutes = date.getMinutes();
    const hours = date.getHours();
    const wDay = days[date.getDay()];
    const mDay = date.getDate();
    const month = months[date.getMonth()];
    const year = date.getFullYear();

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
    dateText[0].innerHTML = `
        <span>
            ${wDay}, ${month} ${mDay}<sup>${suffix}</sup> ${year}.
        </span>  
        <span>
            <small>${hours < 10 ? `0${hours}` : hours}</small>:` + 
            `<small>${minutes < 10 ? `0${minutes}` : minutes}</small>:` +
            `<small>${seconds < 10 ? `0${seconds}` : seconds}</small>
        </span>
    `;
};

setInterval(setDate, 1000);

const switchBtns = function (btn) {
    dashboardGrid.addClass("switch-divs");
    logoutBtn.addClass("span-across-all");
    btn.remove();
};

if (getCookie("active")) {
    switchBtns(btnStart);
}

// ***** EVENT LISTENERS ***** //
startBtn?.click(function () {
    setCookie("active", "yes");
    switchBtns(this);
});

pagesListItems.each((i, item) => {
    $(item).click(function () {
        window.open($(".page-link")[i].getAttribute("href"), "_self");
    });
});
