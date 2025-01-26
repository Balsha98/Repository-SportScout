export const killEventListeners = (element) => $(element).off();

export const toggleElement = function (...elements) {
    elements.forEach((element) => {
        $(element).toggleClass("hide-element");
    });
};

export const attachEvent = function (btns, callback) {
    btns?.each((_, btn) => {
        killEventListeners(btn);
        $(btn).click(callback);
    });
};

export const togglePopup = function (btn, popups, overlay) {
    let popupIndex = 0;
    if (btn.classList[0].includes("close") || btn.classList[2]?.includes("cancel")) {
        popupIndex = +$(btn.closest(".popup-show")).data("popup-index");
    } else {
        popupIndex = +$(btn).data("popup-index");
    }

    const relPopup = [...popups]?.find((popup) => popupIndex === +$(popup).data("popup-index"));
    toggleElement(relPopup, overlay);
};

export const toggleDropdown = function (clickEvent) {
    const { target } = clickEvent;
    const rowContainer = $(target.closest(".div-row-container"));
    const rowID = +rowContainer.data("row-id");

    // Get appropriate parent.
    const scrollContainer = $(rowContainer.closest(".div-scroll-container"));
    const scrollClass = scrollContainer.attr("class").split(" ")[1];

    const form = $(`.${scrollClass} .form-${rowID}`);
    form.toggleClass("hide-element");

    const icon = $(`.${scrollClass} .icon-${rowID}`);
    icon.toggleClass("rotate");
};

export const warnInputs = function (data, status) {
    for (const [key, value] of Object.entries(data)) {
        if (status === "fail") {
            if (value === "") {
                $(`#${key}`).closest(".div-input-container").addClass("red-container");

                continue;
            }
        }

        $(`#${key}`)?.closest(".div-input-container").removeClass("red-container");
    }
};

export const resetInput = function (data) {
    for (const key of Object.keys(data)) {
        const input = $(`#${key}`);
        if (input.attr("readonly") === "readonly") continue;
        input.val("");
    }
};
