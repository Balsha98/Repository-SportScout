import { setCookie } from "../helper/cookie.js";

// ***** DOM ELEMENTS ***** //
const otpInputs = $(".otp-input");
const btnVerify = $(".btn-verify");

// ***** FUNCTIONS ***** //
const keyTimeout = function () {
    const value = $(this).val().split("");

    if (value.length >= 1) {
        const nextFocus = +$(this).data("next");

        // Guard clause.
        if (isNaN(nextFocus)) {
            btnVerify.focus();
            return;
        }

        $(`#otp_num_${nextFocus}`).focus();
    }
};

// ***** EVENT LISTENERS ***** //
otpInputs.each((_, input) => {
    $(input).keypress(function () {
        setTimeout(keyTimeout.bind(this), 100);
    });
});

btnVerify.click(function (formEvent) {
    formEvent.preventDefault();

    const form = $(this.closest(".form"));
    const url = form.attr("action");
    const method = form.attr("method");

    const data = {};
    data["otp"] = "";
    otpInputs.each((_, input) => {
        data["otp"] += $(input).val();
    });

    $.ajax({
        url: url,
        method: method,
        data: JSON.stringify(data),
        success: function (response) {
            const data = JSON.parse(response);

            // Guard clause.
            if (data["status"] === "fail") {
                return;
            }

            setCookie("user_id", data["user_id"]);
        },
    });
});
