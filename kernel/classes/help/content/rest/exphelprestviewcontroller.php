<?php
/**
 * The view of the context help routes (expHelpRestProvider): JSON
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expHelpRestViewController implements ezpRestViewControllerInterface
{
    public function loadView( ezcMvcRoutingInformation $routeInfo, ezcMvcRequest $request, ezcMvcResult $result )
    {
        return new ezpRestJsonView( $request, $result );
    }
}

?>
