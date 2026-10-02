<?php
/**
 * File containing the ezpKernel class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */

/**
 * Base eZ Publish kernel class.
 * Wraps a "kernel handler" and forwards calls to it.
 * This allows to have different kernel handlers depending on the context (i.e. "web" or "cli")
 */
class ezpKernel implements ezpWebBasedKernelHandler
{
    /**
     * @var ezpKernelHandler
     */
    private $kernelHandler;

    /**
     * @var ezpKernel
     */
    protected static $instance = null;

    public function __construct( ezpKernelHandler $kernelHandler )
    {
        /**
         * PHP 5.3.3 is our hard requirement
         */
        if ( version_compare( PHP_VERSION, '5.3.3' ) < 0 )
        {
            echo "<h1>Unsupported PHP version " . PHP_VERSION . "</h1>",
            "<p>Exponential does not run with PHP version lower than 5.3.3.</p>",
            "<p>For more information about supported software please visit ",
            "<a href=\"http://ez.no/\" >us, eZ Systems, on http://ez.no</a>. See you there :-)</p>";
            exit;
        }

        $this->kernelHandler = $kernelHandler;
        self::$instance = $this;
    }

    /**
     * Execution point for controller actions.
     * Returns false if not supported
     *
     * @return ezpKernelResult|false
     */
    public function run()
    {
        try
        {
            return $this->kernelHandler->run();
        }
        catch ( ezpFormTokenException $e )
        {
            // A form token refusal that got past the handler (the REST and
            // tree menu kernels, a module that runs the check itself) is still
            // a 403 and one warning line, never an "Unexpected error"
            if ( !eZExecution::isWebRequest() )
                throw $e;
            $rest = $this->kernelHandler instanceof ezpKernelRest;
            $body = ezpFormTokenRefusal::respond( $e, $rest );
            eZExecution::cleanup();
            eZExecution::setCleanExit();
            // index_rest.php does not print the result; the REST kernel writes its own
            if ( $rest )
            {
                echo $body;
                $body = '';
            }
            return new ezpKernelResult( $body, array( 'form_token_refused' => $e->getReason() ) );
        }
    }

    /**
     * Runs a callback function in the kernel environment.
     * This is useful to run eZ Publish 4.x code from a non-related context (like eZ Publish 5)
     *
     * @param \Closure $callback
     * @param bool $postReinitialize Default is true.
     *                               If set to false, the kernel environment will not be reinitialized.
     *                               This can be useful to optimize several calls to the kernel within the same context.
     * @return mixed The result of the callback
     */
    public function runCallback( \Closure $callback, $postReinitialize = true )
    {
        return $this->kernelHandler->runCallback( $callback, $postReinitialize );
    }

    /**
     * Sets whether to use exceptions inside the kernel.
     *
     * @param bool $useExceptions
     */
    public function setUseExceptions( $useExceptions )
    {
        $this->kernelHandler->setUseExceptions( $useExceptions );
    }

    /**
     * @param bool $usePagelayout
     */
    public function setUsePagelayout( $usePagelayout )
    {
        if ( $this->kernelHandler instanceof ezpWebBasedKernelHandler )
        {
            $this->kernelHandler->setUsePagelayout( $usePagelayout );
        }
    }

    /**
     * Reinitializes the kernel environment.
     */
    public function reInitialize()
    {
        $this->kernelHandler->reInitialize();
    }

    /**
     * Checks whether the kernel handler has the Symfony service container
     * container or not.
     *
     * @return bool
     */
    public function hasServiceContainer()
    {
        return $this->kernelHandler->hasServiceContainer();
    }

    /**
     * Returns the Symfony service container if it has been injected,
     * otherwise returns null.
     *
     * @return \Symfony\Component\DependencyInjection\ContainerInterface|null
     */
    public function getServiceContainer()
    {
        return $this->kernelHandler->getServiceContainer();
    }

    /**
     * Checks if the kernel has already been instantiated.
     *
     * @return bool
     */
    public static function hasInstance()
    {
        return self::$instance !== null;
    }

    /**
     * Returns the current instance of ezpKernel.
     *
     * @throws LogicException if no instance of ezpKernel has been instantiated
     * @return ezpKernel
     */
    public static function instance()
    {
        if ( !self::hasInstance() )
        {
            throw new LogicException(
                'Cannot return the instance of '
                . __CLASS__
                . ', it has not been instantiated'
            );
        }
        return self::$instance;
    }
}

