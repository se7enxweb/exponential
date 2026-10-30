<?php
/**
 * File containing the expDebugBarPanelInterface interface: a tab of the Exp Debug bar contributed by an extension.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * A panel of the Exp Debug bar from an extension: its own tab after Memory, optionally a chip in the summary and data
 * in the page's JSON. Registered in debugbar.ini:
 *
 *   [DebugBarSettings]
 *   Panels[]=<id>
 *
 *   [Panel_<id>]
 *   Class=<class implementing this interface>
 *
 * The bar creates the class without arguments for each report it renders and asks visible() first: a panel that
 * says no draws nothing. The bar also shows to anonymous visitors when DebugByIP matches, so a panel decides who may
 * see what it knows; its constructor runs before visible(), also for them, and stays trivial, without data access.
 * html() is printed as it is: the panel escapes what it puts in. A panel that throws is left out of that report, with
 * a warning among its messages.
 */
interface expDebugBarPanelInterface
{
    /**
     * Whether the current user may see this panel on this page.
     *
     * @return bool
     */
    public function visible();

    /**
     * The tab's name, translated, as plain text.
     *
     * @return string
     */
    public function title();

    /**
     * The chip of the summary, or null for none: array( 'text' => plain text, 'title' => plain text,
     * 'level' => ok | warn | high ). A click on it opens the panel's tab.
     *
     * @return array|null
     */
    public function chip();

    /**
     * The tab's content as HTML.
     *
     * @return string
     */
    public function html();

    /**
     * Data for scripts, put into the page's JSON under panels.<id>, or null for none.
     *
     * @return array|null
     */
    public function data();
}
