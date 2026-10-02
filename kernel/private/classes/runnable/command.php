<?php
/**
 * File containing the Exponential\Runnable\Command class.
 *
 * @copyright Copyright (C) 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package kernel
 */

namespace Exponential\Runnable;

/**
 * A CLI command. The script in bin/ (or extension/<ext>/bin/) is one call:
 *
 *   require_once 'autoload.php';
 *   \Exponential\Command\Kernel\EzCache::main( __FILE__ );
 *
 * The command's code runs in run(). A script's top-level variables were globals, and functions of the
 * script read them with "global $cli"; the moved code keeps that: main() runs run() with the script's
 * variables bound to $GLOBALS (see the generated classes).
 */
abstract class Command extends Runnable
{
    /**
     * Runs the command for the script that calls it.
     *
     * @param string $scriptFile the script's __FILE__
     * @return mixed what run() returns
     */
    public static function main( $scriptFile = '' )
    {
        return static::create( $scriptFile )->run();
    }

    /**
     * The command's work.
     *
     * @return mixed
     */
    abstract public function run();
}
