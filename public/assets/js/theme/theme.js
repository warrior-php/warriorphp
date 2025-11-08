(function () {
    "use strict";

    // 如果文档还没加载完成，等它加载完
    if (document.readyState === 'loading') {
        document.addEventListener("DOMContentLoaded", () => {
            setTimeout(initTheme, 500);
        });
    } else {
        setTimeout(initTheme, 500);
    }

    function initTheme() {
        // =================================
        // Tooltip
        // =================================
        const tooltipTriggerList = Array.from(
            document.querySelectorAll('[data-bs-toggle="tooltip"]')
        );
        tooltipTriggerList.forEach((tooltipTriggerEl) => {
            new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // =================================
        // Popover
        // =================================
        const popoverTriggerList = [].slice.call(
            document.querySelectorAll('[data-bs-toggle="popover"]')
        );
        popoverTriggerList.map(function (popoverTriggerEl) {
            return new bootstrap.Popover(popoverTriggerEl);
        });

        // Hide preloader
        const preloader = document.querySelector(".preloader");
        if (preloader) preloader.style.display = "none";

        // Increment & Decrement
        document.querySelectorAll(".minus, .add").forEach((button) => {
            button.addEventListener("click", function () {
                const qtyInput = this.closest("div").querySelector(".qty");
                let currentVal = parseInt(qtyInput.value); // ✅ 改为 let
                const isAdd = this.classList.contains("add");

                if (!isNaN(currentVal)) {
                    qtyInput.value = isAdd ? ++currentVal : currentVal > 0 ? --currentVal : currentVal;
                }
            });
        });

        // Fixed header shadow
        window.addEventListener("scroll", function () {
            const topbar = document.querySelector(".topbar");
            if (topbar) {
                if (window.scrollY >= 60) {
                    topbar.classList.add("shadow-sm");
                } else {
                    topbar.classList.remove("shadow-sm");
                }
            }
        });
    }
})();
