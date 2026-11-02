<?php
/**
 * Who sees the debug bar's User tab and how its policies are asked for, continuing expDebugBarPoliciesTest (UP-01 to
 * UP-04, UP-06 to UP-08):
 *   - UP-05 the tab: setup/setup, or an anonymous visitor with debug output; never a logged-in user without
 *           setup/setup, even with debug output (expDebugBarUser::mayView())
 *   - UP-09 expdebugbar::policies takes its arguments from the path: role id or "-", offset, the search as base64url
 *           ("-" for none); anything else refused, the search held to 100 characters
 *   - UP-10 the script asks with everything in the path, never the query string, and only when the section is opened
 *
 * No database.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expDebugBarUserPoliciesTest extends PHPUnit\Framework\TestCase
{
    /** UP-05 */
    public function testWhoMaySeeTheTab()
    {
        $this->assertTrue( expDebugBarUser::mayView( true, true, false ), 'setup/setup, with or without debug output' );
        $this->assertTrue( expDebugBarUser::mayView( true, true, true ) );
        $this->assertTrue( expDebugBarUser::mayView( false, false, true ), 'an anonymous visitor with debug output' );
        $this->assertFalse( expDebugBarUser::mayView( false, false, false ), 'an anonymous visitor without debug output' );
        $this->assertFalse( expDebugBarUser::mayView( false, true, true ), 'a logged-in user without setup/setup, even with debug output' );
        $this->assertFalse( expDebugBarUser::mayView( false, true, false ) );
    }

    private static function b64url( $text )
    {
        return rtrim( strtr( base64_encode( $text ), '+/', '-_' ), '=' );
    }

    /** UP-09 */
    public function testTheArgumentsInThePath()
    {
        $this->assertSame( array( null, 0, '' ), expDebugBarServerFunctions::policyArgs( array() ), 'nothing: all, the first page' );
        $this->assertSame( array( null, 0, '' ), expDebugBarServerFunctions::policyArgs( array( '-', '0', '-' ) ) );
        $this->assertSame( array( 7, 200, '' ), expDebugBarServerFunctions::policyArgs( array( '7', '200' ) ) );
        $this->assertSame( array( null, 0, 'content/read' ), expDebugBarServerFunctions::policyArgs( array( '-', '0', self::b64url( 'content/read' ) ) ), 'a "/" in the search' );
        $this->assertSame( array( null, 0, 'Médias::x' ), expDebugBarServerFunctions::policyArgs( array( '-', '0', self::b64url( 'Médias::x' ) ) ), 'UTF-8 and "::"' );
        $long = expDebugBarServerFunctions::policyArgs( array( '-', '0', self::b64url( str_repeat( 'ä', 150 ) ) ) );
        $this->assertSame( 100, mb_strlen( $long[2], 'UTF-8' ), 'at most 100 characters' );

        foreach ( array( array( 'x' ), array( '1;2' ), array( '-', '-1' ), array( '-', 'a' ), array( '-', '0', 'a+b/' ),
                         array( '-', '0', 'x' ), array( '-', '0', self::b64url( "\xff\xfe" ) ), array( '<script>' ) ) as $bad )
        {
            try
            {
                expDebugBarServerFunctions::policyArgs( $bad );
                $this->fail( 'refused: ' . implode( '::', $bad ) );
            }
            catch ( InvalidArgumentException $e )
            {
                $this->assertNotSame( '', $e->getMessage() );
            }
        }
    }

    /** UP-10 */
    public function testTheScriptAsksWithThePath()
    {
        $js = file_get_contents( __DIR__ . '/../../../../../design/standard/javascript/expdebugbar.js' );
        $this->assertStringContainsString( "call('policies::' + (state.role || '-') + '::' + (append ? state.offset : 0) + '::' + (state.q ? b64url(state.q) : '-'), {})", $js,
                                           'role, page and search in the path, no parameters' );
        $this->assertStringNotContainsString( "call('policies', {", $js );
        $this->assertStringContainsString( "details.addEventListener('toggle'", $js, 'asked when the section is opened' );
    }
}
