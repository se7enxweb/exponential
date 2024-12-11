// Sidebar toggling feature
function sidebarToggle() {
    function toggleeZPAdminLeftSidebar() {
        const ezpAdminLeftSidebar = localStorage?.getItem("ezpAdminLeftSidebar");

        if (ezpAdminLeftSidebar === "true") {
            if ($(window).width() < 1024) {
                $(".sidebar").removeClass("show");
                $(".sidebar-control").removeClass("rotate");
            }

            $(".sidebar.left").addClass("show");
            $(".sidebar-control.left").addClass("rotate");
            return;
        }

        $(".sidebar.left").removeClass("show");
        $(".sidebar-control.left").removeClass("rotate");
    }

    function toggleeZPAdminRightSidebar() {
        const ezpAdminRightSidebar = localStorage?.getItem("ezpAdminRightSidebar");

        if (ezpAdminRightSidebar === "true") {
            if ($(window).width() < 1024) {
                $(".sidebar").removeClass("show");
                $(".sidebar-control").removeClass("rotate");
            }

            $(".sidebar.right").addClass("show");
            $(".sidebar-control.right").addClass("rotate");
            return;
        }

        $(".sidebar.right").removeClass("show");
        $(".sidebar-control.right").removeClass("rotate");
    }

    $(".sidebar-control.left").on("click", function () {
        if (localStorage?.getItem("ezpAdminLeftSidebar") === "true") {
            localStorage?.setItem("ezpAdminLeftSidebar", "false");
        } else {
            localStorage?.setItem("ezpAdminLeftSidebar", "true");
        }

        if ($(window).width() < 1024) {
            localStorage?.setItem("ezpAdminRightSidebar", "false");
        }

        toggleeZPAdminLeftSidebar();
    });

    $(".sidebar-control.right").on("click", function () {
        if (localStorage?.getItem("ezpAdminRightSidebar") === "true") {
            localStorage?.setItem("ezpAdminRightSidebar", "false");
        } else {
            localStorage?.setItem("ezpAdminRightSidebar", "true");
        }

        if ($(window).width() < 1024) {
            localStorage?.setItem("ezpAdminLeftSidebar", "false");
        }

        toggleeZPAdminRightSidebar();
    });

    function resetSidebar() {
        const ezpAdminLeftSidebar = localStorage?.getItem("ezpAdminLeftSidebar") || 'true';
        const ezpAdminRightSidebar = localStorage?.getItem("ezpAdminRightSidebar") || 'true';


        if ($(window).width() < 1024) {
            localStorage?.setItem("ezpAdminLeftSidebar", "false");
            localStorage?.setItem("ezpAdminRightSidebar", "false");
        } else {
            localStorage?.setItem("ezpAdminLeftSidebar", ezpAdminLeftSidebar);
            localStorage?.setItem("ezpAdminRightSidebar", ezpAdminRightSidebar);
        }

        toggleeZPAdminLeftSidebar();
        toggleeZPAdminRightSidebar();
    }

    resetSidebar();
}

// Navbar menu toggling feature
function navbarToggle() {
    $(".navbar-icon").on('click', function () {
        $(".navbar-menu").toggleClass("show");
        $("body").toggleClass('navbar-open');
    });
}

// Wrap table inside responsive wrapper
function wrapTable() {
    setTimeout(() => {
        $("table").wrap('<div class="table-responsive"></div>');
    }, 1000);
}

// Adjust header height dynamically
function adjustHeaderHeight() {
    const headerHeight = document.querySelector("#header").offsetHeight;
    const dashboard = document.querySelector(".dashboard-flex");

    if (!dashboard) {
        return;
    }

    // Set the --header-height CSS variable dynamically
    dashboard.style.setProperty('--header-height', `${headerHeight / 16}rem`);
}

(($) => {
    $(function () {
        sidebarToggle();
        navbarToggle();
        adjustHeaderHeight();
        // wrapTable();
    });
})(jQuery);
