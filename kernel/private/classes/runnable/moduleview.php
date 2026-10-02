<?php
/**
 * File containing the Exponential\Runnable\ModuleView class.
 *
 * @copyright Copyright (C) 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package kernel
 */

namespace Exponential\Runnable;

/**
 * A module view. eZModule runs the view's file through eZProcess::runFile(), which makes the view's
 * parameters local variables ($Params, $Module, ...) and takes the file's return value, or else its
 * $Result, as the view's result. The file is one call that hands those variables over:
 *
 *   <?php
 *   return \Exponential\View\Kernel\Content\View::main( __FILE__, get_defined_vars() );
 *
 * run() receives them by reference and returns the view's result ($Result when the code does not return
 * one), so eZModule gets exactly what it got before.
 */
abstract class ModuleView extends Runnable
{
    /**
     * @param string $scriptFile the view's __FILE__
     * @param array $scope get_defined_vars() of eZProcess::runFile()
     * @return mixed the view's result
     */
    public static function main( $scriptFile, array $scope )
    {
        $view = static::create( $scriptFile );
        return static::runWithEvents( $view, 'view', $scope, function () use ( $view, $scope ) {
            return $view->run( $scope );
        } );
    }

    /**
     * @param array $scope the view's variables
     * @return mixed the view's result
     */
    abstract public function run( array $scope );

    /**
     * What eZProcess::runFile() takes as the view's result: the view's $Result when it is set (not empty),
     * else what the view returned.
     *
     * @param mixed $result the view's $Result
     * @param mixed $returned what the view returned
     * @return mixed
     */
    protected function viewResult( $result, $returned )
    {
        return empty( $result ) ? $returned : $result;
    }
}
