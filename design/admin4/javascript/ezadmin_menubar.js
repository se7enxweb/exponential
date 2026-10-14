// Sidebar toggling feature
// On a wide window (1024px and up) each sidebar follows the visitor's stored choice (ezpAdminSidebarLeft,
// ezpAdminSidebarRight; open when nothing is stored or storage is blocked). On a narrow window both start closed and
// at most one is open, and nothing is written: a narrow window, or a sidebar opened over the page there, must not
// change what a wide window shows later. Only a click on a wide window stores a choice. The keys before this,
// ezpAdminLeftSidebar and ezpAdminRightSidebar, were written as "false" by every narrow visit, so they are dropped.
function sidebarToggle() {
    const narrow = window.matchMedia("(max-width: 1023.98px)");
    const keys = { left: "ezpAdminSidebarLeft", right: "ezpAdminSidebarRight" };
    const fallback = { left: true, right: true }; // what is shown when storage cannot be read or written
    let narrowOpen = null; // the sidebar open over the page on a narrow window: "left", "right" or null

    function stored(side) {
        try {
            const value = window.localStorage.getItem(keys[side]);
            return value === null ? fallback[side] : value === "true";
        } catch (e) {
            return fallback[side];
        }
    }

    function store(side, open) {
        fallback[side] = open;
        try {
            window.localStorage.setItem(keys[side], open ? "true" : "false");
        } catch (e) {
            // private windows and blocked site data: the choice lasts for this page only
        }
    }

    try {
        window.localStorage.removeItem("ezpAdminLeftSidebar");
        window.localStorage.removeItem("ezpAdminRightSidebar");
    } catch (e) {
        // nothing to clean up without storage
    }

    function show(side, open) {
        $(".sidebar." + side).toggleClass("show", open);
        $(".sidebar-control." + side).toggleClass("rotate", open).attr("aria-expanded", open ? "true" : "false");
    }

    function apply() {
        if (narrow.matches) {
            show("left", narrowOpen === "left");
            show("right", narrowOpen === "right");
            return;
        }
        narrowOpen = null;
        show("left", stored("left"));
        show("right", stored("right"));
    }

    function toggle(side) {
        if (narrow.matches) {
            narrowOpen = narrowOpen === side ? null : side;
        } else {
            store(side, !stored(side));
        }
        apply();
    }

    $(".sidebar-control.left").on("click", () => toggle("left"));
    $(".sidebar-control.right").on("click", () => toggle("right"));

    if (narrow.addEventListener) {
        narrow.addEventListener("change", apply);
    } else if (narrow.addListener) {
        narrow.addListener(apply);
    }

    apply();
}

// Navbar menu toggling feature
function navbarToggle() {
    // The icon is a div with role="button" (page_topmenu.tpl): it reports its state in aria-expanded and answers
    // Enter and Space like a button.
    function toggle( icon ) {
        $(".navbar-menu").toggleClass("show");
        $("body").toggleClass('navbar-open');
        icon.setAttribute( 'aria-expanded', $(".navbar-menu").hasClass("show") ? 'true' : 'false' );
    }
    $(".navbar-icon").on('click', function () {
        toggle( this );
    }).on('keydown', function ( e ) {
        if ( e.key === 'Enter' || e.key === ' ' ) {
            e.preventDefault();
            toggle( this );
        }
    });
}

/**
 * Left sidebar menu resize functionality
 * Allows users to drag-resize the left sidebar width and save their preference
 */
function leftMenuResize() {
    const leftMenu = document.getElementById('leftmenu');
    const handle = document.getElementById('leftmenu-resize-handle');

    if (!leftMenu || !handle) {
        return;
    }

    const minWidth = 224;
    const maxWidth = Math.max(minWidth, Math.floor(window.innerWidth * 0.72));
    let startX = 0;
    let startWidth = 0;
    let activePointerId = null;

    const getToken = () => {
        const tokenNode = document.getElementById('ezxform_token_js');
        return tokenNode ? tokenNode.getAttribute('title') : '';
    };

    const applyWidth = (width) => {
        const clamped = Math.min(maxWidth, Math.max(minWidth, Math.round(width)));
        document.documentElement.style.setProperty('--left-sidebar-width', `${clamped}px`);
        return clamped;
    };

    const saveWidth = (width) => {
        const token = getToken();
        if (!window.jQuery || !window.$ || !$.ez || !token) {
            return;
        }

        $.post(
            $.ez.url.replace('ezjscore/', 'user/preferences/') + 'set_and_exit/admin_left_menu_size/' + width + 'px',
            { ezxform_token: token }
        );
    };

    const onMove = (event) => {
        if (activePointerId === null) {
            return;
        }

        const clientX = event.clientX ?? null;
        if (clientX === null) {
            return;
        }

        applyWidth(startWidth + (clientX - startX));
    };

    const stopResize = (event) => {
        if (activePointerId === null) {
            return;
        }

        if (event && event.pointerId !== undefined && activePointerId !== event.pointerId) {
            return;
        }

        const finalWidth = parseFloat(document.documentElement.style.getPropertyValue('--left-sidebar-width')) || leftMenu.getBoundingClientRect().width;
        activePointerId = null;
        document.body.classList.remove('leftmenu-resizing');
        document.removeEventListener('pointermove', onMove);
        document.removeEventListener('pointerup', stopResize);
        document.removeEventListener('pointercancel', stopResize);
        saveWidth(Math.round(finalWidth));
    };

    const startResize = (event) => {
        const clientX = event.clientX ?? null;

        if (clientX === null) {
            return;
        }

        event.preventDefault();
        startX = clientX;
        startWidth = leftMenu.getBoundingClientRect().width;
        activePointerId = event.pointerId !== undefined ? event.pointerId : 0;
        document.body.classList.add('leftmenu-resizing');
        applyWidth(startWidth);

        if (event.pointerId !== undefined && handle.setPointerCapture) {
            handle.setPointerCapture(event.pointerId);
            document.addEventListener('pointermove', onMove);
            document.addEventListener('pointerup', stopResize);
            document.addEventListener('pointercancel', stopResize);
            return;
        }

        document.addEventListener('pointermove', onMove);
        document.addEventListener('pointerup', stopResize);
        document.addEventListener('pointercancel', stopResize);
    };

    handle.addEventListener('pointerdown', startResize);
}

/**
 * Left sidebar width preset controls (Small/Medium/Large)
 * Handles clicks on preset size links and saves preference via AJAX
 */
function leftMenuWidthControls() {
    const links = document.querySelectorAll('#widthcontrol-links a');

    if (!links.length) {
        return;
    }

    const tokenNode = document.getElementById('ezxform_token_js');
    const token = tokenNode ? tokenNode.getAttribute('title') : '';

    if (!token || !window.jQuery || !window.$ || !$.ez) {
        return;
    }

    links.forEach((link) => {
        link.addEventListener('click', (event) => {
            event.preventDefault();

            $.post(link.href, { ezxform_token: token }).done(() => {
                window.location.reload();
            });
        });
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
    const header = document.querySelector("#header");
    const dashboard = document.querySelector(".dashboard-flex");

    if (!header || !dashboard) {
        return;
    }

    // Set the --header-height CSS variable dynamically
    dashboard.style.setProperty('--header-height', `${header.getBoundingClientRect().height}px`);
}

(($) => {
    $(function () {
        sidebarToggle();
        navbarToggle();
        leftMenuResize();
        leftMenuWidthControls();
        adjustHeaderHeight();
        // wrapTable();
    });
})(jQuery);
