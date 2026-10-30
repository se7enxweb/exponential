<?php
/**
 * The server summary of the Exp Debug bar (kernel/classes/debugbar/expdebugbarsummary.php, expDebugBarSummary::collect())
 * names the version of PHP only to a user who may read the server's lists (setup/setup): the report's JSON reaches
 * anonymous visitors too when DebugByIP matches.
 *
 *  PV-01 - The anonymous user gets no PHP version
 *  PV-02 - An administrator (a user with setup/setup) gets PHP_VERSION
 *
 * Reads the users of the installation and sets the current user through the globals eZUser::currentUser() reads,
 * without a session, restoring them afterwards; skipped without an installation database.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expDebugBarSummaryPhpVersionTest extends PHPUnit\Framework\TestCase
{
    /** @var array the globals of the current user before the test: name => array( value ) or null when unset */
    private $saved = array();

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 5 ) );
        try
        {
            if ( !eZDB::instance()->isConnected() )
                $this->markTestSkipped( 'No installation database' );
        }
        catch ( Throwable $e )
        {
            $this->markTestSkipped( 'No installation database: ' . $e->getMessage() );
        }
    }

    protected function tearDown(): void
    {
        foreach ( $this->saved as $name => $value )
        {
            if ( $value === null )
                unset( $GLOBALS[$name] );
            else
                $GLOBALS[$name] = $value[0];
        }
        $this->saved = array();
    }

    /**
     * Makes $user the current user of this run without a session (no regeneration, no session data, no login
     * listeners): the globals eZUser::currentUser() reads first.
     */
    private function signIn( eZUser $user )
    {
        foreach ( array( 'eZUserGlobalInstance_', 'eZUserGlobalInstance_' . $user->attribute( 'contentobject_id' ) ) as $name )
        {
            if ( !array_key_exists( $name, $this->saved ) )
                $this->saved[$name] = array_key_exists( $name, $GLOBALS ) ? array( $GLOBALS[$name] ) : null;
            $GLOBALS[$name] = $user;
        }
    }

    public function testTheAnonymousUserGetsNoPhpVersion()
    {
        $anonymous = eZUser::fetch( (int)eZINI::instance()->variable( 'UserSettings', 'AnonymousUserID' ) );
        if ( !$anonymous instanceof eZUser )
            $this->markTestSkipped( 'The installation has no anonymous user' );
        $this->signIn( $anonymous );
        $access = $anonymous->hasAccessTo( 'setup', 'setup' );
        if ( $access['accessWord'] !== 'no' )
            $this->markTestSkipped( 'The anonymous user of this installation may use setup/setup' );

        $summary = expDebugBarSummary::collect( null, array() );
        $this->assertArrayHasKey( 'php', $summary );
        $this->assertNull( $summary['php'] );
        $this->assertStringNotContainsString( PHP_VERSION, expDebugBarSummary::json() );
    }

    public function testAnAdministratorGetsThePhpVersion()
    {
        $admin = eZUser::fetchByName( 'admin' );
        if ( !$admin instanceof eZUser )
            $this->markTestSkipped( 'The installation has no user admin' );
        $this->signIn( $admin );
        $access = $admin->hasAccessTo( 'setup', 'setup' );
        if ( $access['accessWord'] === 'no' )
            $this->markTestSkipped( 'The user admin of this installation may not use setup/setup' );

        $this->assertSame( PHP_VERSION, expDebugBarSummary::collect( null, array() )['php'] );
    }
}
