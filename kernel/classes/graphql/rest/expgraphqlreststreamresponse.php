<?php
/**
 * The answer of a request whose response was written already, as it happened: an event stream of
 * /api/graphql/subscribe (expGraphQLRestController::doSubscribe()). The REST response writer sends nothing more for
 * it: no status, no headers (they went out with the stream's first bytes), no body.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expGraphQLRestStreamResponse implements ezcMvcResultStatusObject
{
    public function process( ezcMvcResponseWriter $writer )
    {
        if ( $writer instanceof ezpRestHttpResponseWriter )
            $writer->streamed = true;
    }
}

?>
