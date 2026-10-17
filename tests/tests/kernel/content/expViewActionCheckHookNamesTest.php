<?php
/**
 * The action checks of content/edit and user/register have names of their own.
 *
 * Both views hand attribute_edit.php an "action_check" hook by function name. They used to share the name
 * checkContentActions, each defined only when no function of that name existed yet. A persistent-worker server
 * (Velocity with ForkPerRequest=disabled) keeps every function a worker ever defined, so whichever view ran first in a
 * worker supplied the action check of the other: after one user/register in a worker, a Publish in content/edit there
 * ran the user/register operation on the article (activation of a user that does not exist, the registration mail,
 * a 500), and the reverse let a registration skip its own operation.
 *
 *  AC-01 - neither view defines or hooks the shared name checkContentActions
 *  AC-02 - each view hooks exactly the function it defines, and the two names differ
 *  AC-03 - with both views loaded in one process (as in a worker), each name is a different function from its own file
 *
 * No database. Run: php vendor/bin/phpunit tests/tests/kernel/content/expViewActionCheckHookNamesTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group kernel
 */

class expViewActionCheckHookNamesTest extends PHPUnit\Framework\TestCase
{
    const VIEWS = array(
        'content/edit' => 'kernel/private/classes/views/content/edit.php',
        'user/register' => 'kernel/private/classes/views/user/register.php',
    );

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 4 ) );
    }

    /**
     * @return array view => array( defined function, hooked function )
     */
    private static function names()
    {
        $names = array();
        foreach ( self::VIEWS as $view => $file )
        {
            $source = file_get_contents( $file );
            preg_match( '/function_exists\(\s*\'([A-Za-z_]+)\'\s*\)\s*\)\s*\{\s*function\s+([A-Za-z_]+)\s*\(/', $source, $defined );
            preg_match_all( '/addHook\(\s*\'action_check\'\s*,\s*\'([A-Za-z_]+)\'\s*\)/', $source, $hooked );
            $names[$view] = array( 'guard' => $defined[1] ?? null, 'defined' => $defined[2] ?? null, 'hooked' => $hooked[1] );
        }
        return $names;
    }

    /** AC-01 */
    public function testNoViewUsesTheSharedName()
    {
        foreach ( self::VIEWS as $view => $file )
            $this->assertDoesNotMatchRegularExpression( '/function\s+checkContentActions\b|\'checkContentActions\'/', file_get_contents( $file ), $view );
    }

    /** AC-02 */
    public function testEachViewHooksItsOwnFunction()
    {
        $names = self::names();
        foreach ( $names as $view => $n )
        {
            $this->assertNotNull( $n['defined'], "$view defines its action check" );
            $this->assertSame( $n['defined'], $n['guard'], "$view guards the name it defines" );
            $this->assertSame( array( $n['defined'] ), $n['hooked'], "$view hooks the function it defines" );
        }
        $this->assertNotSame( $names['content/edit']['defined'], $names['user/register']['defined'] );
    }

    /** AC-03 */
    public function testBothViewsInOneProcessKeepTheirOwnCheck()
    {
        foreach ( self::VIEWS as $file )
            require_once $file;
        foreach ( self::names() as $view => $n )
        {
            $this->assertTrue( function_exists( $n['defined'] ), $n['defined'] );
            $function = new ReflectionFunction( $n['defined'] );
            $this->assertSame( realpath( self::VIEWS[$view] ), realpath( $function->getFileName() ), "$view's check comes from its own file" );
        }
    }
}
