export const killEventListeners = (element) => $(element).off();

export const toggleElement = function (popup) {
    [popup, popupOverlay].forEach((element) => {
        $(element).toggleClass("hide-element");
    });
};
