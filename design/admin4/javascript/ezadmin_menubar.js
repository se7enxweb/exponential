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
    // focus back to its tab, Tab and Shift+Tab stay inside the open drawer and its tab (focus trap), and opening it
    // with its tab moves the focus into it. A narrow window, or a column opened over the page there, never changes
    // what a wide window shows later: only a click on a column beside the content saves admin_left_sidebar or
    // admin_right_sidebar. Whether the right drawer is open is a choice of its own, admin_right_drawer (1 open, 0 or
    // empty closed), saved by a click or Escape on a tablet-sized window (576 to 1279px, also 960px at 200% zoom) and
    // opened again on the next page there; on a phone the drawer would cover the whole page, so it starts closed and
    // saves nothing. The browser storage keys used before (ezpAdminSidebarLeft/Right, ezpAdminLeftSidebar/RightSidebar)
    // are dropped.
    function sidebarToggle() {
        const narrow = window.matchMedia("(max-width: 1023.98px)");
        const drawerQuery = { left: narrow, right: window.matchMedia("(max-width: 1279.98px)") };
        const phone = window.matchMedia("(max-width: 575.98px)");
        const prefs = { left: "admin_left_sidebar", right: "admin_right_sidebar" };
        const drawerPref = "admin_right_drawer";
        const pref = window.a4Pref || { get: () => null, set: () => {} };
        const choice = { left: pref.get(prefs.left) !== "0", right: pref.get(prefs.right) !== "0" };
        let rightDrawer = pref.get(drawerPref) === "1"; // the saved state of the right drawer
        // the column open over the page as a drawer: "left", "right" or null
        let drawerOpen = rightDrawer && drawerQuery.right.matches && !phone.matches ? "right" : null;

        function stored(side) {
            return choice[side];
        }

        function store(side, open) {
            choice[side] = open;
            pref.set(prefs[side], open ? "1" : "0");
        }

        // after a click on a tab or Escape: the right drawer's state, saved on a tablet-sized window only
        function storeDrawer() {
            const open = drawerOpen === "right";
            if (phone.matches || !drawerQuery.right.matches || open === rightDrawer) {
                return;
            }
            rightDrawer = open;
            pref.set(drawerPref, open ? "1" : "0");
        }

        // what Tab can reach in an open drawer: its tab first, then the drawer's own visible controls
        function trapItems(side) {
            const control = document.querySelector(".sidebar-control." + side);
            const column = document.querySelector(".sidebar." + side);
            const items = control ? [control] : [];
            if (column && typeof column.querySelectorAll === "function") {
                column.querySelectorAll('a[href], button:not([disabled]), input:not([disabled]):not([type="hidden"]), ' +
                    'select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])').forEach((el) => {
                    if (el.getClientRects().length) {
                        items.push(el);
                    }
                });
            }
            return items;
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
                storeDrawer();
                apply();
                // opened with its tab: the focus moves into the drawer (the tab is the first stop of its trap)
                const items = drawerOpen === side ? trapItems(side) : [];
                if (items.length > 1) {
                    items[1].focus();
                }
                return;
            }
            // beside the content: a column that stepped aside for a drawer comes back without a new choice
            const visible = shown(side);
            drawerOpen = null;
            storeDrawer();
            if (visible || !stored(side)) {
                store(side, !visible);
            }
            apply();
        }

        $(".sidebar-control.left").on("click", () => toggle("left"));
        $(".sidebar-control.right").on("click", () => toggle("right"));

        // Escape closes a column open over the page as a drawer and gives the focus back to its tab, when the focus is
        // in that column or on its tab. While a context menu (ezpopupmenu.js) is shown, Escape belongs to the menu: this
        // does not rely on the order the scripts load in or on the menu stopping the event.
        // Tab and Shift+Tab: the focus stays inside the open drawer and its tab, cycling from the last control back to
        // the tab and from the tab into the drawer (the tab sits in the header, so its natural next stop is the page
        // under the drawer). Focus outside the drawer, after a click on the page, is left alone.
        document.addEventListener("keydown", (e) => {
            if (e.key !== "Tab" || e.defaultPrevented || e.altKey || e.ctrlKey || e.metaKey || drawerOpen === null || !isDrawer(drawerOpen)) {
                return;
            }
            const items = trapItems(drawerOpen);
            const at = items.indexOf(document.activeElement);
            if (at === -1 || items.length < 2) {
                return;
            }
            e.preventDefault();
            items[(at + (e.shiftKey ? -1 : 1) + items.length) % items.length].focus();
        });

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
            storeDrawer();
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
        // Enter and Space like a button. Escape, the close button at the panel's end and a tap outside the header
        // close the open menu; Escape and the close button give the focus back to the icon. Below 576px the open
        // menu is a panel over the page: Tab and Shift+Tab stay in the header (its row, the icon and the panel),
        // going round from its last control to its first (focus trap). From 576px the menu is the header's row: a
        // phone menu left open when the window grows (a phone turned to landscape) is closed, so the page's
        // scrolling, locked while it is open, comes back.
        const phone = window.matchMedia ? window.matchMedia("(max-width: 575.98px)") : { matches: true };
        function isOpen() {
            return $(".navbar-menu").hasClass("show");
        }
        function setOpen( icon, open ) {
            $(".navbar-menu").toggleClass("show", open);
            $("body").toggleClass('navbar-open', open);
            icon.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
        }
        // what Tab can reach while the phone menu is open: the header's visible controls in their order (the logo,
        // the menu button, the panel, the tools of the header's row), so nothing a finger can reach is out of the
        // keyboard's reach. A control with a box but visibility: hidden (the side columns' tabs, hidden while the
        // menu is open) is skipped by Tab: counted as the last one, it let the focus leave the header.
        function trapItems( header ) {
            const items = [];
            if (header && typeof header.querySelectorAll === "function") {
                header.querySelectorAll('a[href], button:not([disabled]), input:not([disabled]):not([type="hidden"]), ' +
                    'select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])').forEach((el) => {
                    if (el.getClientRects().length && window.getComputedStyle(el).visibility !== "hidden") {
                        items.push(el);
                    }
                });
            }
            return items;
        }
        $(".navbar-icon").on('click', function () {
            setOpen( this, !isOpen() );
        }).on('keydown', function ( e ) {
            if ( e.key === 'Enter' || e.key === ' ' ) {
                e.preventDefault();
                setOpen( this, !isOpen() );
            }
        });
        $(".a4-phone-close-button").on('click', function () {
            const icon = document.querySelector(".navbar-icon");
            if (icon) {
                setOpen(icon, false);
                icon.focus();
            }
        });

        // Escape, while the menu is open and the focus is in it, on its button or nowhere in particular. Listened to
        // in the capture phase, so it is seen before the search's own Escape: an open search scope popup in the menu
        // is closed by that one and keeps the menu open, whatever order the scripts load in. A context menu that is
        // shown keeps its Escape as well.
        document.addEventListener("keydown", (e) => {
            const icon = document.querySelector(".navbar-icon");
            const menu = document.querySelector(".navbar-menu");
            if (e.key !== "Escape" || e.defaultPrevented || !icon || !menu || !menu.classList.contains("show")) {
                return;
            }
            const active = document.activeElement;
            if (active && active !== document.body && active !== icon && !menu.contains(active)) {
                return;
            }
            if (menu.querySelector(".searchscope-pane:not([hidden])") || (active && active.closest && active.closest('[role="menu"]'))) {
                return;
            }
            const menus = document.querySelectorAll("div.popupmenu");
            for (let i = 0; i < menus.length; i++) {
                const style = window.getComputedStyle(menus[i]);
                if (style.display !== "none" && style.visibility !== "hidden") {
                    return;
                }
            }
            setOpen(icon, false);
            icon.focus();
        }, true);

        // Tab and Shift+Tab while the phone menu is open: from the header's last control back to its first, and the
        // other way round. In between the browser's own order holds. Focus outside the header (after a tap on the
        // page) is left alone.
        document.addEventListener("keydown", (e) => {
            if (e.key !== "Tab" || e.defaultPrevented || e.altKey || e.ctrlKey || e.metaKey || !phone.matches) {
                return;
            }
            const menu = document.querySelector(".navbar-menu");
            if (!menu || !menu.classList.contains("show")) {
                return;
            }
            const items = trapItems(document.getElementById("header"));
            const at = items.indexOf(document.activeElement);
            if (at === -1 || items.length < 2) {
                return;
            }
            const last = items.length - 1;
            if ((e.shiftKey && at === 0) || (!e.shiftKey && at === last)) {
                e.preventDefault();
                items[e.shiftKey ? last : 0].focus();
            }
        });

        // A tap or click outside the header closes the open phone menu (the panel lies over the page); the focus
        // stays where the tap put it. pointerdown, not click: iOS sends no click to the document for a tap on an
        // element that is not clickable itself (the page's background, a paragraph).
        document.addEventListener("pointerdown", (e) => {
            const icon = document.querySelector(".navbar-icon");
            const header = document.getElementById("header");
            if (!icon || !phone.matches || !isOpen() || !header || header.contains(e.target)) {
                return;
            }
            setOpen(icon, false);
        });

        const closeWhenWide = () => {
            const icon = document.querySelector(".navbar-icon");
            if (!phone.matches && icon && isOpen()) {
                setOpen(icon, false);
            }
        };
        if (phone.addEventListener) {
            phone.addEventListener("change", closeWhenWide);
        } else if (phone.addListener) {
            phone.addListener(closeWhenWide);
        }
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
