<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

$Module = $Params['Module'];

$text = <<<'COPYRIGHT'
<p>Copyright (C) 1998-2026 7x / Exponential Foundation. All rights reserved.</p>

<p>Exponential is free software: you may redistribute it and/or modify it
under the terms of the "GNU General Public License" version 2 as published
by the Free Software Foundation and appearing in the file LICENSE included
in the packaging of this software.</p>

<p>Exponential is provided AS IS with NO WARRANTY OF ANY KIND, INCLUDING
THE WARRANTY OF DESIGN, MERCHANTABILITY AND FITNESS FOR A PARTICULAR
PURPOSE.</p>

<p>Exponential was originally developed by eZ Systems as eZ Publish (Legacy):
Copyright (C) 1999-2014 eZ Systems AS. All rights reserved. The copyright
notices of the original authors and of the third-party software included
with Exponential are kept in the source files and in the LICENSE file, as
the license requires.</p>

<p>The "GNU General Public License" (GPL) is available at
<a href="https://www.gnu.org/licenses/old-licenses/gpl-2.0.html">https://www.gnu.org/licenses/old-licenses/gpl-2.0.html</a>
and in the file LICENSE included in the packaging of this software.</p>
COPYRIGHT;

// The notice is rendered through design:ezinfo/copyright.tpl, so a design can present it;
// Without a result, the plain notice is shown.
$tpl = eZTemplate::factory();
$tpl->setVariable( 'copyright_notice', $text );
$content = $tpl->fetch( 'design:ezinfo/copyright.tpl' );

$Result = array();
$Result['content'] = ( is_string( $content ) && trim( $content ) !== '' ) ? $content : $text;
$Result['path'] = array( array( 'url' => false,
                                'text' => ezpI18n::tr( 'kernel/ezinfo', 'Info' ) ),
                         array( 'url' => false,
                                'text' => ezpI18n::tr( 'kernel/ezinfo', 'Copyright' ) ) );

?>
