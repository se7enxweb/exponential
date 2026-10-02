<?php
/**
 * File containing the Exponential\Runnable\CronjobPart class.
 *
 * @copyright Copyright (C) 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package kernel
 */

namespace Exponential\Runnable;

/**
 * A cronjob part. runcronjobs.php includes the part's file inside eZRunCronjobs::runScript(), where the
 * part reads that function's variables ($cli, $isQuiet, ...); the file is one call that hands them over:
 *
 *   <?php
 *   return \Exponential\Cronjob\Kernel\Workflow::main( __FILE__, get_defined_vars() );
 *
 * run() receives them by reference, so the part's code reads and writes them as it did.
 */
abstract class CronjobPart extends Runnable
{
    /**
     * @param string $scriptFile the part's __FILE__
     * @param array $scope get_defined_vars() of the including function
     * @return mixed what run() returns
     */
    public static function main( $scriptFile, array $scope )
    {
        return static::create( $scriptFile )->run( $scope );
    }

    /**
     * @param array $scope the including function's variables
     * @return mixed
     */
    abstract public function run( array $scope );
}
