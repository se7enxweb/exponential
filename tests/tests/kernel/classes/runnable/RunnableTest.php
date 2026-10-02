<?php
/**
 * The runnable base classes (kernel/private/classes/runnable), namespace Exponential\Runnable,
 * guide doc/bc/6.0/cli_cronjob_view_abstractions.md.
 *
 *  RN-01 — Command::main() creates the command for the script file and returns what run() returns
 *  RN-02 — CronjobPart::main() hands the including function's variables to run() and returns its result
 *  RN-03 — ModuleView::main() hands the view's variables to run() and returns its result
 *  RN-04 — create() remembers the stub's __FILE__; scriptFile() and scriptDir() give __FILE__ and __DIR__
 *  RN-05 — create() without a file gives an empty script file
 *  RN-06 — ModuleView::viewResult(): a non-empty $Result wins, else what the view returned (as eZProcess::runFile)
 *  RN-07 — implementation() is the class itself when the settings name no re-implementation for it
 *  RN-08 — implementation() can be re-implemented (override), and create()/main() then run the subclass
 *  RN-09 — The three kinds are Runnables and cannot be instantiated as abstract classes
 *  RN-10 — The ModuleView rule is the one lib/ezutils/classes/ezprocess.php runFile applies
 *
 * No database, no kernel.
 *
 * @copyright Copyright (C) 7x / Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file.
 * @package tests
 * @group runnable
 */

foreach ( array( 'runnable', 'command', 'cronjobpart', 'moduleview' ) as $file )
    require_once __DIR__ . '/../../../../../kernel/private/classes/runnable/' . $file . '.php';

class ezpTestRunnableCommand extends \Exponential\Runnable\Command
{
    public static $ran = 0;
    public function run()
    {
        self::$ran++;
        return array( 'file' => $this->scriptFile(), 'dir' => $this->scriptDir(), 'class' => static::class );
    }
}

class ezpTestRunnableCommandImpl extends ezpTestRunnableCommand
{
    public function run()
    {
        return 'impl';
    }
}

class ezpTestRunnableCommandRerouted extends ezpTestRunnableCommand
{
    public static function implementation()
    {
        return 'ezpTestRunnableCommandImpl';
    }
}

class ezpTestRunnableCronjobPart extends \Exponential\Runnable\CronjobPart
{
    public function run( array $scope )
    {
        return array( 'scope' => $scope, 'file' => $this->scriptFile() );
    }
}

class ezpTestRunnableView extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        $Result = isset( $scope['Result'] ) ? $scope['Result'] : null;
        $returned = isset( $scope['returned'] ) ? $scope['returned'] : null;
        return $this->viewResult( $Result, $returned );
    }

    public function exposeViewResult( $result, $returned )
    {
        return $this->viewResult( $result, $returned );
    }
}

class RunnableTest extends PHPUnit\Framework\TestCase
{
    /** RN-01 */
    public function testCommandMainRunsAndReturnsTheResult()
    {
        $before = ezpTestRunnableCommand::$ran;
        $r = ezpTestRunnableCommand::main( '/a/b/script.php' );
        $this->assertSame( $before + 1, ezpTestRunnableCommand::$ran );
        $this->assertSame( '/a/b/script.php', $r['file'] );
        $this->assertSame( 'ezpTestRunnableCommand', $r['class'] );
    }

    /** RN-02 */
    public function testCronjobPartMainHandsTheScopeOver()
    {
        $scope = array( 'cli' => 'the-cli', 'isQuiet' => true );
        $r = ezpTestRunnableCronjobPart::main( '/c/part.php', $scope );
        $this->assertSame( $scope, $r['scope'] );
        $this->assertSame( '/c/part.php', $r['file'] );
    }

    /** RN-03 */
    public function testModuleViewMainHandsTheScopeOverAndReturnsTheViewResult()
    {
        $r = ezpTestRunnableView::main( '/k/view.php', array( 'Result' => array( 'content' => 'x' ), 'returned' => 'ignored' ) );
        $this->assertSame( array( 'content' => 'x' ), $r );
    }

    /** RN-04 */
    public function testScriptFileAndDir()
    {
        $c = ezpTestRunnableCommand::create( '/var/www/bin/php/ezcache.php' );
        $this->assertInstanceOf( 'ezpTestRunnableCommand', $c );
        $this->assertSame( '/var/www/bin/php/ezcache.php', $c->scriptFile() );
        $this->assertSame( '/var/www/bin/php', $c->scriptDir() );
    }

    /** RN-05 */
    public function testCreateWithoutFile()
    {
        $c = ezpTestRunnableCommand::create();
        $this->assertSame( '', $c->scriptFile() );
        $this->assertSame( dirname( '' ), $c->scriptDir() );
    }

    /** RN-06 */
    public function testViewResultRule()
    {
        $v = new ezpTestRunnableView();
        $this->assertSame( 'R', $v->exposeViewResult( 'R', 'ret' ) );
        $this->assertSame( 'ret', $v->exposeViewResult( null, 'ret' ) );
        $this->assertSame( 'ret', $v->exposeViewResult( array(), 'ret' ) );
        $this->assertSame( 'ret', $v->exposeViewResult( '', 'ret' ) );
        $this->assertSame( 'ret', $v->exposeViewResult( 0, 'ret' ) );
        $this->assertSame( array( 'a' ), $v->exposeViewResult( array( 'a' ), 'ret' ) );
        $this->assertNull( $v->exposeViewResult( null, null ) );
    }

    /** RN-07 */
    public function testImplementationIsTheClassItselfWithoutSettings()
    {
        $this->assertSame( 'ezpTestRunnableCommand', ezpTestRunnableCommand::implementation() );
        $this->assertSame( 'ezpTestRunnableView', ezpTestRunnableView::implementation() );
        $this->assertSame( 'ezpTestRunnableCronjobPart', ezpTestRunnableCronjobPart::implementation() );
    }

    /** RN-08 */
    public function testReimplementationRunsTheSubclass()
    {
        $c = ezpTestRunnableCommandRerouted::create( '/x.php' );
        $this->assertInstanceOf( 'ezpTestRunnableCommandImpl', $c );
        $this->assertSame( '/x.php', $c->scriptFile() );
        $this->assertSame( 'impl', ezpTestRunnableCommandRerouted::main( '/x.php' ) );
    }

    /** RN-09 */
    public function testKindsAreAbstractRunnables()
    {
        foreach ( array( 'Command', 'CronjobPart', 'ModuleView', 'Runnable' ) as $kind )
        {
            $rc = new ReflectionClass( 'Exponential\\Runnable\\' . $kind );
            $this->assertTrue( $rc->isAbstract(), $kind );
        }
        foreach ( array( 'Command', 'CronjobPart', 'ModuleView' ) as $kind )
            $this->assertTrue( is_subclass_of( 'Exponential\\Runnable\\' . $kind, 'Exponential\\Runnable\\Runnable' ), $kind );
    }

    /** RN-10 */
    public function testViewResultMatchesEzProcessRule()
    {
        $source = file_get_contents( __DIR__ . '/../../../../../lib/ezutils/classes/ezprocess.php' );
        $this->assertStringContainsString( 'Result', $source );
        $this->assertMatchesRegularExpression( '/empty\s*\(\s*\$Result\s*\)/', $source, 'eZProcess::runFile still decides on empty( $Result )' );
    }
}
