<?php
/**
 * File containing the Exponential\Runnable\Runnable base class.
 *
 * @copyright Copyright (C) 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package kernel
 */

namespace Exponential\Runnable;

/**
 * The base of everything Exponential runs from a file: CLI commands (bin/), cronjob parts (cronjobs/) and
 * module views (kernel/<module>/<view>.php, extension/<ext>/modules/<module>/<view>.php). The file keeps
 * its path and becomes one call; the work is done by a class, so it can be called from inside the system,
 * subclassed and re-implemented. Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 *
 *   Runnable
 *     Command       a CLI script            bin/php/ezcache.php       -> Exponential\Command\Kernel\EzCache
 *     CronjobPart   a cronjob part          cronjobs/workflow.php     -> Exponential\Cronjob\Kernel\Workflow
 *     ModuleView    a module view           kernel/content/view.php   -> Exponential\View\Kernel\Content\View
 */
abstract class Runnable
{
    /**
     * @var string the file this runnable was moved from (the stub that calls it), for __FILE__ and __DIR__
     *             in the moved code
     */
    protected $scriptFile = '';

    /**
     * The runnable to use for this class: the class named in the settings when a site or an extension
     * re-implements it ([RunnableSettings] Implementation[<class>]=<subclass> in site.ini), else itself.
     *
     * @return string class name
     */
    public static function implementation()
    {
        $class = static::class;
        if ( class_exists( 'eZINI', false ) )
        {
            try
            {
                $ini = \eZINI::instance();
                if ( $ini->hasVariable( 'RunnableSettings', 'Implementation' ) )
                {
                    $map = $ini->variable( 'RunnableSettings', 'Implementation' );
                    $key = ltrim( $class, '\\' );
                    if ( is_array( $map ) && isset( $map[$key] ) && is_subclass_of( $map[$key], $class ) )
                        return $map[$key];
                }
            }
            catch ( \Throwable $e )
            {
                // no settings yet (early in a script): the class itself
            }
        }
        return $class;
    }

    /**
     * @param string $scriptFile the stub's __FILE__
     * @return static
     */
    public static function create( $scriptFile = '' )
    {
        $class = static::implementation();
        $runnable = new $class();
        $runnable->scriptFile = (string) $scriptFile;
        return $runnable;
    }

    /** @return string the original script's path (what __FILE__ was) */
    public function scriptFile()
    {
        return $this->scriptFile;
    }

    /** @return string the original script's directory (what __DIR__ was) */
    public function scriptDir()
    {
        return dirname( $this->scriptFile );
    }
}
