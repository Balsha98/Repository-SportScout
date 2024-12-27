"use strict";

// ***** FUNCTIONS ***** //
const getCookie = function (name) {
    const cookies = document.cookie.split(";");

    for (const cookie of cookies) {
        const data = cookie.split("=");
        if (data[0].trim() === name) {
            return data[1];
        }
    }

    return false;
};

const setCookie = function (name, value) {
    document.cookie = `${name}=${value};`;
};
