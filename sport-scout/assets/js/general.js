export const killEventListeners = (element) => $(element).off();

export const toggleElement = function (popup) {
    [popup, popupOverlay].forEach((element) => {
        $(element).toggleClass("hide-element");
    });
};

export const attachEvent = function (btns, callback) {
    btns?.each((_, btn) => {
        general.killEventListeners(btn);
        $(btn).click(callback);
    });
};
