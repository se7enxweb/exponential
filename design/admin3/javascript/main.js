// Sidebar toggling feature
function sidebarToggle() {
    function toggleeZPAdminLeftSidebar() {
        const ezpAdminLeftSidebar = localStorage?.getItem("ezpAdminLeftSidebar");

        if (ezpAdminLeftSidebar === "true") {
            if ($(window).width() < 1024) {
                $(".sidebar").removeClass("show");
                $(".sidebar-control").removeClass("rotate");
            }

            $("#leftmenu").addClass("show");
            $(".sidebar-control.left").addClass("rotate");
            return;
        }

        $("#leftmenu").removeClass("show");
        $(".sidebar-control.left").removeClass("rotate");
    }

    function toggleeZPAdminRightSidebar() {
        const ezpAdminRightSidebar = localStorage?.getItem("ezpAdminRightSidebar");

        if (ezpAdminRightSidebar === "true") {
            if ($(window).width() < 1024) {
                $(".sidebar").removeClass("show");
                $(".sidebar-control").removeClass("rotate");
            }

            $("#rightmenu").addClass("show");
            $(".sidebar-control.right").addClass("rotate");
            return;
        }

        $("#rightmenu").removeClass("show");
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

    $(window).resize(function () {
        resetSidebar();
    });
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

$(function () {
    sidebarToggle();
    navbarToggle();
    wrapTable();
});