/* global bootstrap */
(function () {
    "use strict";

    // Theme Onload Toast
    window.addEventListener("load", () => {
        const myAlert = document.querySelector('.toast');
        if (myAlert) {
            const bsAlert = new bootstrap.Toast(myAlert);
            bsAlert.show();
        }
    });
})();
