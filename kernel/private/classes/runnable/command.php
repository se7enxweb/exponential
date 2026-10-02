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
 *
 * Shared option parsing, help and output: one place for what every command does the same way.
 *
 *   $script = $this->script( array( 'description' => "Writes the report", 'use-session' => false ) );
 *   $script->startup();
 *   $options = $this->options( '[month:]', '', array( 'month' => 'The month, YYYY-MM' ) );
 *   $script->initialize();
 *   $this->output( "Done" );           // nothing with -q / --quiet
 *   $this->shutdown( 0 );
 *
 * or the usual sequence in one call: $options = $this->start( $settings, '[month:]', '', $help );
 *
 * --help, -q/--quiet, -s/--siteaccess, -l/--login, -d/--debug, -v/--verbose and the other standard options
 * come from eZScript::getOptions(); these helpers hand them on unchanged, so a command's --help text and
 * exit codes are the same whichever way it is written.
 */
abstract class Command extends Runnable
{
    /** @var \eZCLI|null */
    protected $cli;

    /** @var \eZScript|null */
    protected $script;

    /** @var array the options of the last options() call */
    protected $options = array();

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

    /**
     * The terminal (eZCLI::instance()).
     *
     * @return \eZCLI
     */
    public function cli()
    {
        if ( $this->cli === null )
            $this->cli = \eZCLI::instance();
        return $this->cli;
    }

    /**
     * The script: eZScript::instance( $settings ). The first call with settings creates it (description,
     * use-session, use-modules, use-extensions, site-access, ...), later calls update or just return it.
     *
     * @param array $settings eZScript settings
     * @return \eZScript
     */
    public function script( array $settings = array() )
    {
        if ( $this->script === null || !empty( $settings ) )
            $this->script = \eZScript::instance( $settings );
        return $this->script;
    }

    /**
     * Parses the command line: eZScript::getOptions() with the same arguments. It shows --help and exits,
     * applies -q, --siteaccess, --login, --debug and the other standard options.
     *
     * @param string $config option config, e.g. "[clear-tag:][clear-all]"
     * @param string $argumentConfig argument config, e.g. "[FILE*]"
     * @param array|false $optionHelp option => help text
     * @param array|false $arguments the arguments to parse, false for the command line
     * @param bool|array $useStandardOptions
     * @return array|null the options
     */
    public function options( $config = '', $argumentConfig = '', $optionHelp = false, $arguments = false, $useStandardOptions = true )
    {
        $this->options = $this->script()->getOptions( $config, $argumentConfig, $optionHelp, $arguments, $useStandardOptions );
        return $this->options;
    }

    /**
     * The usual start of a command in one call: script( $settings ), startup(), options(), initialize().
     *
     * @return array|null the options
     */
    public function start( array $settings, $config = '', $argumentConfig = '', $optionHelp = false, $arguments = false, $useStandardOptions = true )
    {
        $script = $this->script( $settings );
        $script->startup();
        $options = $this->options( $config, $argumentConfig, $optionHelp, $arguments, $useStandardOptions );
        $script->initialize();
        return $options;
    }

    /** @return bool -q / --quiet was given */
    public function isQuiet()
    {
        return $this->script()->isQuiet();
    }

    /**
     * Writes a line (eZCLI::output(), which writes nothing with -q).
     *
     * @param string $text
     * @param bool $addEOL
     */
    public function output( $text = false, $addEOL = true )
    {
        $this->cli()->output( $text, $addEOL );
    }

    /** Writes an error (shown with -q too). */
    public function error( $text = false, $addEOL = true )
    {
        $this->cli()->error( $text, $addEOL );
    }

    /** Writes a warning. */
    public function warning( $text = false, $addEOL = true )
    {
        $this->cli()->warning( $text, $addEOL );
    }

    /**
     * Writes a message to a log in var/log (eZLog::write()), by default <command>.log after the script's name.
     *
     * @param string $message
     * @param string|null $logName
     */
    public function log( $message, $logName = null )
    {
        if ( $logName === null )
            $logName = ( $this->scriptFile !== '' ? basename( $this->scriptFile, '.php' ) : 'command' ) . '.log';
        \eZLog::write( $message, $logName );
    }

    /**
     * Ends the command: eZScript::shutdown(), which exits with $exitCode.
     *
     * @param int|false $exitCode
     * @param string|false $exitText
     */
    public function shutdown( $exitCode = false, $exitText = false )
    {
        $this->script()->shutdown( $exitCode, $exitText );
    }
}
