"use strict";

// ***** DOM ELEMENTS ***** //
const warningPopup = $(".popup-warning");
const popupOverlay = $(".popup-overlay");
const tryAgainBtn = $(".btn-try-again");
const closeBtn = $(".popup-btn-close");
const loginBtn = $(".btn-login");

// ***** FUNCTIONS ***** //
const togglePopup = function () {
    [warningPopup, popupOverlay].forEach((el) => {
        $(el).toggleClass("hide-element");
    });

    $("#login_username").focus();
};

const resetInput = function (data) {
    for (const key of Object.keys(data)) {
        $(`#${key}`).val("");
    }
};

// ***** EVENT LISTENERS ***** //
[tryAgainBtn, closeBtn].forEach((el) => {
    $(el).click(togglePopup);
});

loginBtn.click(function (event) {
    event.preventDefault();

    // Get parent form.
    const form = $(this.closest("form"));

    $.ajax({
        url: form.attr("action"),
        type: form.attr("method"),
        data: form.serialize(),
        success: function (response) {
            const data = JSON.parse(response);
            const message = data["message"];

            if (message === "fail") {
                resetInput(data);
                togglePopup();
                return;
            }

            setCookie("user_id", data["user_id"]);
            window.open("dashboard.php", "_self");
        },
    });
});
