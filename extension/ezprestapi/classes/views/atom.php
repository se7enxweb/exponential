<?php
/**
 * File containing the the view for atom
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

/**
 * View providing atomfeed of the output
 */
class ezpRestAtomView extends ezcMvcView
{
    public function __construct( ezcMvcRequest $request, ezcMvcResult $result )
    {
        parent::__construct( $request, $result );

        $result->content = new ezcMvcResultContent();
        $result->content->type = "application/atom+xml";
        $result->content->charset = "UTF-8";
    }

    public function createZones( $layout )
    {
        return array(
            new ezcMvcFeedViewHandler( "content", new ezpRestAtomDecorator, "atom" )
        );
    }
}
