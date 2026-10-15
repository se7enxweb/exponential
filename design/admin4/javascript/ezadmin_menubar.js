// The admin4 frame's sidebars, phone-width menu and left column grip. Everything stays inside this function, with
// jQuery as $ from its parameter: nothing is left in the page's global scope. The header's height is kept in step
// by admin4.js.
(($) => {
    // Sidebar toggling feature
    // A column beside the content (the left one from 1024px, the right one from 1280px, the breakpoints of
    // admin4.css) follows the user's choice, kept through window.a4Pref (the head's first script) as the eZPreferences
    // admin_left_sidebar and admin_right_sidebar (1 open, the default, 0 closed), like the column width; an anonymous
    // page keeps it in the browser. Below its breakpoint a column is a drawer over the page: it starts closed, at most
    // one column is open while a drawer is (an inline column steps aside meanwhile), Escape closes it and gives the
    // focus back to its tab, and nothing is saved: a narrow window, or a column opened over the page there, must not
    // change what a wide window shows later. Only a click on a column beside the content saves a choice. The browser
    // storage keys used before (ezpAdminSidebarLeft/Right, ezpAdminLeftSidebar/RightSidebar) are dropped.
    function sidebarToggle() {
        const narrow = window.matchMedia("(max-width: 1023.98px)");
        const drawerQuery = { left: narrow, right: window.matchMedia("(max-width: 1279.98px)") };
        const prefs = { left: "admin_left_sidebar", right: "admin_right_sidebar" };
        const pref = window.a4Pref || { get: () => null, set: () => {} };
        const choice = { left: pref.get(prefs.left) !== "0", right: pref.get(prefs.right) !== "0" };
        let drawerOpen = null; // the column open over the page as a drawer: "left", "right" or null

        function stored(side) {
            return choice[side];
        }

        function store(side, open) {
            choice[side] = open;
            pref.set(prefs[side], open ? "1" : "0");
        }

        function isDrawer(side) {
            return drawerQuery[side].matches;
        }

        try {
            ["ezpAdminLeftSidebar", "ezpAdminRightSidebar", "ezpAdminSidebarLeft", "ezpAdminSidebarRight"].forEach(
                (key) => window.localStorage.removeItem(key));
        } catch (e) {
            // nothing to clean up without storage
        }

        // The tabs on the window edges are named after their column ("Left column"); aria-expanded says whether it shows.
        function show(side, open) {
            $(".sidebar." + side).toggleClass("show", open);
            const tab = $(".sidebar-control." + side).toggleClass("rotate", open);
            // A page without that column (content edit, history ...): the tab controls nothing, so it names no target
            // and reports no state
            if (!document.getElementById(side === "left" ? "leftmenu" : "rightmenu")) {
                tab.removeAttr("aria-controls").removeAttr("aria-expanded");
                return;
            }
            tab.attr("aria-expanded", open ? "true" : "false");
        }

        function shown(side) {
            return isDrawer(side) ? drawerOpen === side : drawerOpen === null && stored(side);
        }

        function apply() {
            if (drawerOpen !== null && !isDrawer(drawerOpen)) {
                drawerOpen = null;
            }
            show("left", shown("left"));
            show("right", shown("right"));
        }

        function toggle(side) {
            if (isDrawer(side)) {
                drawerOpen = drawerOpen === side ? null : side;
            } else {
                // beside the content: a column that stepped aside for a drawer comes back without a new choice
                const visible = shown(side);
                drawerOpen = null;
                if (visible || !stored(side)) {
                    store(side, !visible);
                }
            }
            apply();
        }

        $(".sidebar-control.left").on("click", () => toggle("left"));
        $(".sidebar-control.right").on("click", () => toggle("right"));

        // Escape closes a column open over the page as a drawer and gives the focus back to its tab, when the focus is
        // in that column or on its tab. While a context menu (ezpopupmenu.js) is shown, Escape belongs to the menu: this
        // does not rely on the order the scripts load in or on the menu stopping the event.
        document.addEventListener("keydown", (e) => {
            if (e.key !== "Escape" || e.defaultPrevented || drawerOpen === null || !isDrawer(drawerOpen)) {
                return;
            }
            const side = drawerOpen;
            const control = document.querySelector(".sidebar-control." + side);
            const column = document.querySelector(".sidebar." + side);
            const active = document.activeElement;
            if (!active || (active !== control && !(column && column.contains(active)))) {
                return;
            }
            if (active.closest && active.closest('[role="menu"]')) {
                return;
            }
            const menus = document.querySelectorAll("div.popupmenu");
            for (let i = 0; i < menus.length; i++) {
                const style = window.getComputedStyle(menus[i]);
                if (style.display !== "none" && style.visibility !== "hidden") {
                    return;
                }
            }
            drawerOpen = null;
            apply();
            if (control) {
                control.focus();
            }
        });

        [drawerQuery.left, drawerQuery.right].forEach((query) => {
            if (query.addEventListener) {
                query.addEventListener("change", apply);
            } else if (query.addListener) {
                query.addListener(apply);
            }
        });

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
        // the widest the column may get follows the window
        const maxWidthNow = () => Math.max(minWidth, Math.floor(window.innerWidth * 0.72));
        let startX = 0;
        let startWidth = 0;
        let activePointerId = null;

        const getToken = () => {
            const tokenNode = document.getElementById('ezxform_token_js');
            return tokenNode ? tokenNode.getAttribute('title') : '';
        };

        const applyWidth = (width) => {
            const clamped = Math.min(maxWidthNow(), Math.max(minWidth, Math.round(width)));
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

        // The grip is a focusable separator: its value is the column's width, the arrow keys change it by 16px
        // (Shift: 64px), Home and End go to the narrowest and widest; the width is saved when a key is let go.
        const announce = (width) => {
            handle.setAttribute('aria-valuemin', String(minWidth));
            const max = maxWidthNow();
            handle.setAttribute('aria-valuemax', String(max));
            handle.setAttribute('aria-valuenow', String(Math.min(max, Math.max(minWidth, Math.round(width)))));
        };
        announce(leftMenu.getBoundingClientRect().width);
        window.addEventListener('resize', () => announce(leftMenu.getBoundingClientRect().width));
        let keyed = false;
        handle.addEventListener('keydown', (event) => {
            const step = event.shiftKey ? 64 : 16;
            const width = leftMenu.getBoundingClientRect().width;
            let next = null;
            if (event.key === 'ArrowLeft' || event.key === 'ArrowDown') { next = width - step; }
            else if (event.key === 'ArrowRight' || event.key === 'ArrowUp') { next = width + step; }
            else if (event.key === 'Home') { next = minWidth; }
            else if (event.key === 'End') { next = maxWidthNow(); }
            if (next === null) {
                return;
            }
            event.preventDefault();
            announce(applyWidth(next));
            keyed = true;
        });
        handle.addEventListener('keyup', () => {
            if (keyed) {
                keyed = false;
                saveWidth(Math.round(leftMenu.getBoundingClientRect().width));
            }
        });
        handle.addEventListener('pointerup', () => announce(leftMenu.getBoundingClientRect().width));
    }

    $(function () {
        sidebarToggle();
        navbarToggle();
        leftMenuResize();
    });
})(jQuery);
