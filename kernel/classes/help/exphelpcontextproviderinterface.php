<?php
/**
 * File containing the expHelpContextProviderInterface interface.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * A class that answers "help for this page" in code, for what one Map[] entry per view cannot say (a view
 * whose help depends on its parameters, an extension with many generated views). List it in exphelp.ini
 * [ContextHelp] Providers[]; the providers are asked first, in that order, before the map.
 */
interface expHelpContextProviderInterface
{
    /**
     * The help page for a page of the administration
     *
     * @param string $module the module, in lower case
     * @param string $view the view, in lower case, "" for none
     * @param string $design the design of the page (admin4, admin4l, admin, editor ...), "" for none
     * @return string|null "path[#anchor]" (a Markdown page under doc/, see expHelpContextRegistry::parseTarget()),
     *                     or null to leave the answer to the next provider and the map
     */
    public function helpContext( $module, $view, $design );
}

?>
