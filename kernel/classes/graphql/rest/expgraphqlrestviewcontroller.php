<?php
/**
 * The view of the GraphQL routes (expGraphQLRestProvider): JSON. The controller's status object writes the body.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expGraphQLRestViewController implements ezpRestViewControllerInterface
{
    public function loadView( ezcMvcRoutingInformation $routeInfo, ezcMvcRequest $request, ezcMvcResult $result )
    {
        return new ezpRestJsonView( $request, $result );
    }
}

?>
