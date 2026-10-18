<?php
/**
 * Context help (workstream G): which help page answers a module/view/design. [ContextHelp] of exphelp.ini and of
 * every active extension's exphelp.ini.append.php (so extensions register their own help); falls back to the
 * module, then the dashboard. Skeleton: G replaces the bodies.
 *
 * lookup() result: array( 'id' => page id|'', 'anchor' => string, 'url' => module-relative URL, 'title' => string,
 *                         'level' => view|module|dashboard )
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expHelpContextRegistry
{
    /** @var expHelpContextRegistry|null */
    protected static $instance = null;

    /** @return expHelpContextRegistry */
    public static function instance()
    {
        if ( self::$instance === null )
            self::$instance = new self();
        return self::$instance;
    }

    /**
     * @param string $module
     * @param string $view
     * @param string $design
     * @return array
     */
    public function lookup( $module, $view = '', $design = '' )
    {
        return array( 'id' => '', 'anchor' => '', 'url' => expHelp::url( 'dashboard' ), 'title' => '', 'level' => 'dashboard' );
    }

    /** @return array "module/view" or "module" => "page id#anchor" */
    public function map() { return array(); }
}

?>
