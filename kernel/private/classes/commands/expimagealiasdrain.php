<?php
/**
 * The code of bin/php/expimagealiasdrain.php: a drainer of the image alias backlog, a process of its own.
 * @description Make the image aliases waiting in the backlog of this server, as a process of its own (started by a web request)
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

/**
 * A web request whose page or image left aliases in the backlog (expImageAliasBacklog) starts this process after its
 * answer instead of making them itself ([AliasSettings] BacklogDrainer=process): it holds drainer lock <slot>, makes
 * the backlog in a conversion slot, oldest first, until it is empty or BacklogTime seconds have passed, and ends. At
 * most MaxParallel drainers run on a server at once (one per lock). Started by root (Velocity), it becomes the owner
 * of var/ first, as the other workers do.
 */
class Expimagealiasdrain extends \Exponential\Runnable\Command
{
    public function run()
    {
        $root = dirname( __DIR__, 4 );
        $dropError = \expContentJobWorker::dropPrivileges( $root );
        $script = $this->script( array( 'description' => "Makes the image aliases waiting in the backlog of this server (settings/image.ini\n" .
                                                         "[AliasSettings] Backlog, BacklogDrainer). Started by web requests after their answer;\n" .
                                                         "one process per drainer slot (--slot, 0 to MaxParallel - 1).",
                                        'use-session' => false,
                                        'use-modules' => false,
                                        'use-extensions' => true ) );
        $options = $this->startup( '[slot:]', '', array( 'slot' => 'the drainer slot this process holds (0 to MaxParallel - 1)' ) );
        if ( $dropError !== null )
        {
            $this->error( 'FAIL ' . $dropError );
            $script->shutdown( 1 );
        }
        $slot = isset( $options['slot'] ) && is_numeric( $options['slot'] ) ? (int)$options['slot'] : 0;
        $worked = \expImageAliasBacklog::runDrainer( $slot );
        if ( $worked === false )
            $this->cli()->output( "drainer $slot: another process holds it, nothing done" );
        else
            $this->cli()->output( "drainer $slot: $worked made, " . \expImageAliasBacklog::count() . ' left' );
        $script->shutdown();
    }
}

}
