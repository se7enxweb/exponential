<?php
/**
 * The shared option parsing, help and output of Exponential\Runnable\Command (#207 stage 2),
 * guide doc/bc/6.0/cli_cronjob_view_abstractions.md.
 *
 *  CH-01 — options() hands its arguments to eZScript::getOptions() unchanged and keeps the result
 *  CH-02 — start() runs startup(), getOptions() and initialize() in that order and returns the options
 *  CH-03 — output(), error() and warning() go to the terminal (eZCLI) unchanged
 *  CH-04 — isQuiet() and shutdown() go to the script
 *  CH-05 — log() writes to <script name>.log by default (the name is derived from the script file)
 *  CH-06 — The kernel commands use the helpers: no run() creates eZScript or eZCLI itself any more
 *
 * No database, no kernel: a recording script and terminal stand in for eZScript and eZCLI.
 *
 * @copyright Copyright (C) 7x / Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file.
 * @package tests
 * @group runnable
 */

foreach ( array( 'runnable', 'command' ) as $file )
    require_once __DIR__ . '/../../../../../kernel/private/classes/runnable/' . $file . '.php';

class ezpTestRecorder
{
    public $calls = array();
    public $quiet = false;
    public function __call( $name, $args )
    {
        $this->calls[] = array( $name, $args );
        if ( $name === 'getOptions' )
            return array( 'parsed' => $args );
        if ( $name === 'isQuiet' )
            return $this->quiet;
        return null;
    }
}

class ezpTestHelperCommand extends \Exponential\Runnable\Command
{
    public function run()
    {
        return 0;
    }

    public function inject( $script, $cli )
    {
        $this->script = $script;
        $this->cli = $cli;
    }

    public function logName()
    {
        return ( $this->scriptFile !== '' ? basename( $this->scriptFile, '.php' ) : 'command' ) . '.log';
    }
}

class CommandHelpersTest extends PHPUnit\Framework\TestCase
{
    private function command( &$script, &$cli, $file = '/x/bin/php/report.php' )
    {
        $script = new ezpTestRecorder();
        $cli = new ezpTestRecorder();
        $c = ezpTestHelperCommand::create( $file );
        $c->inject( $script, $cli );
        return $c;
    }

    /** CH-01 */
    public function testOptionsForwardsToGetOptions()
    {
        $c = $this->command( $script, $cli );
        $help = array( 'month' => 'The month' );
        $r = $c->options( '[month:]', '[FILE*]', $help, false, true );
        $this->assertSame( array( 'parsed' => array( '[month:]', '[FILE*]', $help, false, true ) ), $r );
        $this->assertSame( array( array( 'getOptions', array( '[month:]', '[FILE*]', $help, false, true ) ) ), $script->calls );
        // the defaults are eZScript::getOptions()'s own
        $c->options();
        $this->assertSame( array( '', '', false, false, true ), $script->calls[1][1] );
    }

    /** CH-02 */
    public function testStartRunsTheUsualSequence()
    {
        $c = $this->command( $script, $cli );
        // settings would re-create the script through eZScript::instance(); with none the injected one is used
        $r = $c->start( array(), '[a]', '', array( 'a' => 'A' ) );
        $this->assertSame( array( 'startup', 'getOptions', 'initialize' ), array_column( $script->calls, 0 ) );
        $this->assertSame( '[a]', $r['parsed'][0] );
    }

    /** CH-03 */
    public function testOutputGoesToTheTerminal()
    {
        $c = $this->command( $script, $cli );
        $c->output( 'hello' );
        $c->output( 'no eol', false );
        $c->error( 'bad' );
        $c->warning( 'careful' );
        $this->assertSame( array(
            array( 'output', array( 'hello', true ) ),
            array( 'output', array( 'no eol', false ) ),
            array( 'error', array( 'bad', true ) ),
            array( 'warning', array( 'careful', true ) ),
        ), $cli->calls );
        $this->assertSame( array(), $script->calls );
    }

    /** CH-04 */
    public function testQuietAndShutdownGoToTheScript()
    {
        $c = $this->command( $script, $cli );
        $this->assertFalse( $c->isQuiet() );
        $script->quiet = true;
        $this->assertTrue( $c->isQuiet() );
        $c->shutdown( 3, 'bye' );
        $this->assertSame( array( 'shutdown', array( 3, 'bye' ) ), end( $script->calls ) );
    }

    /** CH-05 */
    public function testLogNameFollowsTheScript()
    {
        $c = $this->command( $script, $cli, '/x/bin/php/trashpurge.php' );
        $this->assertSame( 'trashpurge.log', $c->logName() );
        $c = $this->command( $script, $cli, '' );
        $this->assertSame( 'command.log', $c->logName() );
    }

    /** CH-06 */
    public function testKernelCommandsUseTheHelpers()
    {
        $dir = dirname( __DIR__, 5 ) . '/kernel/private/classes/commands';
        $using = 0;
        foreach ( glob( "$dir/*.php" ) as $file )
        {
            $code = (string) file_get_contents( $file );
            if ( !preg_match( '/extends\s+\\\\Exponential\\\\Runnable\\\\Command\b/', $code ) )
                continue;
            // the run() body: from "function run()" to the end of the class's namespace block is enough here,
            // functions of the script outside the class may keep the plain calls
            $start = strpos( $code, 'public function run()' );
            $this->assertNotFalse( $start, basename( $file ) );
            $classEnd = strpos( $code, "\n}\n", $start );
            $body = substr( $code, $start, $classEnd === false ? null : $classEnd - $start );
            $this->assertDoesNotMatchRegularExpression( '/\$script\s*=\s*\\\\eZScript::instance\(/', $body, basename( $file ) . ' creates its script with $this->script()' );
            $this->assertDoesNotMatchRegularExpression( '/\$cli\s*=\s*\\\\eZCLI::instance\(\s*\)/', $body, basename( $file ) . ' gets the terminal with $this->cli()' );
            if ( strpos( $body, '$this->script(' ) !== false )
                $using++;
        }
        $this->assertGreaterThanOrEqual( 55, $using, 'the kernel commands create their script with $this->script()' );
    }
}
