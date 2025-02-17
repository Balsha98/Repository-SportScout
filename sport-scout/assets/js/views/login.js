// ***** DOM ELEMENTS ***** //
const warningPopup = $(".popup-warning");
const popupOverlay = $(".popup-overlay");
const tryAgainBtn = $(".btn-try-again");
const closeBtn = $(".popup-btn-close");
const loginBtn = $(".btn-login");

// ***** FUNCTIONS ***** //
const togglePopup = function () {
    [warningPopup, popupOverlay].forEach((element) => {
        $(element).toggleClass("hide-element");
    });

    $("#username").focus();
};

const resetInput = function (data) {
    for (const key of Object.keys(data)) {
        $(`#${key}`).val("");
    }
};

// ***** EVENT LISTENERS ***** //
[tryAgainBtn, closeBtn].forEach((element) => {
    $(element).click(togglePopup);
});

loginBtn.click(function (event) {
    event.preventDefault();

    const form = $(this.closest(".form"));
    const url = form.attr("action");
    const method = form.attr("method");

    const data = {};
    data["username"] = $("#username").val();
    data["password"] = $("#password").val();

    $.ajax({
        url: url,
        type: method,
        data: JSON.stringify(data),
        success: function (response) {
            console.log(response);
            const data = JSON.parse(response);
            const status = data["status"];

            if (status === "fail") {
                resetInput(data);
                togglePopup();
                return;
            }

            window.open("otp", "_self");
        },
    });
});
