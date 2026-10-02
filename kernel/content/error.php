<?php
/**
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

$NodeID = $Params['NodeID'] ?? null;
$Module = $Params['Module'];


$tpl = eZTemplate::factory();

$Module->setTitle( "Error 404 object " . $NodeID . " not found" );

$tpl->setVariable( "object", $NodeID );

$Result = array();
$Result['content'] = $tpl->fetch( "design:content/error.tpl" );


?>
