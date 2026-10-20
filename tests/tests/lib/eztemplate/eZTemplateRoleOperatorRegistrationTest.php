<?php
/**
 * The role and policy template operators (lib/eztemplate/classes/eztemplateroleoperator.php,
 * doc/eng-us/features/6.0/role-and-policy-template-operators.md) are registered with the template engine
 * (lib/eztemplate/classes/eztemplateautoload.php), so a template that uses them gets no "unknown operator".
 *
 * No kernel bootstrap, no database and no siteaccess: a bare eZTemplate with the lib/eztemplate autoloads. The
 * rendered cases use arguments the operators answer without a database (an empty role name, role id 0, no roles).
 *
 *  ROP-01  The autoload entry names exactly the operators the class declares
 *  ROP-02  A template engine knows all seven operators after its autoloads
 *  ROP-03  Templates using them render, interpreted and compiled, without an error or warning
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group lib
 * @group eztemplate
 */

class eZTemplateRoleOperatorRegistrationTest extends PHPUnit\Framework\TestCase
{
    const NAMES = array( 'has_role', 'has_role_id', 'has_any_role', 'has_role_by_user', 'has_role_by_user_id',
                         'has_policy', 'has_policy_by_user' );

    /** @var string */
    private static $dir;

    private $savedGlobals = array();

    public static function setUpBeforeClass(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        self::$dir = 'var/tmp/phpunit-eztemplate-role-' . getmypid() . '-' . substr( md5( uniqid( '', true ) ), 0, 8 );
        mkdir( self::$dir . '/compiled', 0777, true );
    }

    public static function tearDownAfterClass(): void
    {
        foreach ( glob( self::$dir . '/compiled/*' ) ?: array() as $f )
            is_file( $f ) && unlink( $f );
        foreach ( glob( self::$dir . '/*' ) ?: array() as $f )
            is_file( $f ) && unlink( $f );
        @rmdir( self::$dir . '/compiled' );
        @rmdir( self::$dir );
    }

    protected function setUp(): void
    {
        foreach ( array( 'eZTemplateCompilerSettings', 'eZSiteBasics' ) as $name )
            $this->savedGlobals[$name] = array_key_exists( $name, $GLOBALS ) ? array( $GLOBALS[$name] ) : null;
    }

    protected function tearDown(): void
    {
        foreach ( $this->savedGlobals as $name => $saved )
        {
            if ( $saved === null )
                unset( $GLOBALS[$name] );
            else
                $GLOBALS[$name] = $saved[0];
        }
    }

    public function testAutoloadEntryNamesTheClassOperators()
    {
        $eZTemplateOperatorArray = array();
        include 'lib/eztemplate/classes/eztemplateautoload.php';
        $entries = array_values( array_filter( $eZTemplateOperatorArray, function ( $e ) { return $e['class'] === 'eZTemplateRoleOperator'; } ) );
        $this->assertCount( 1, $entries, 'one entry for eZTemplateRoleOperator' );
        $this->assertSame( self::NAMES, $entries[0]['operator_names'] );
        $this->assertSame( ( new eZTemplateRoleOperator() )->operatorList(), $entries[0]['operator_names'] );
    }

    public function testTheEngineKnowsTheOperators()
    {
        $tpl = new eZTemplate();
        $tpl->autoload();
        foreach ( self::NAMES as $name )
            $this->assertArrayHasKey( $name, (array)$tpl->Operators, $name );
    }

    public static function templateProvider()
    {
        $out = array();
        foreach ( array( false, true ) as $compiled )
        {
            $mode = $compiled ? 'compiled' : 'interpreted';
            $out["has_role empty name, $mode"] = array( "{if has_role('', 14)}y{else}n{/if}", 'n', $compiled );
            $out["has_role_id 0, $mode"] = array( '{if has_role_id(0, 14)}y{else}n{/if}', 'n', $compiled );
            $out["has_any_role none, $mode"] = array( '{if has_any_role(array(), 14)}y{else}n{/if}', 'n', $compiled );
            $out["has_policy empty, $mode"] = array( "{if has_policy('', '', 14)}y{else}n{/if}", 'n', $compiled );
            $out["has_role_by_user_id 0, $mode"] = array( '{if has_role_by_user_id(0, 1)}y{else}n{/if}', 'n', $compiled );
        }
        return $out;
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('templateProvider')]
    public function testTemplatesRender( $source, $expected, $compiled )
    {
        if ( $compiled )
        {
            unset( $GLOBALS['eZSiteBasics'] );
            $GLOBALS['eZTemplateCompilerSettings']['compile'] = true;
            $GLOBALS['eZTemplateCompilerSettings']['compilation-directory'] = self::$dir . '/compiled';
        }
        else
        {
            $GLOBALS['eZTemplateCompilerSettings']['compile'] = false;
            $GLOBALS['eZSiteBasics']['no-cache-adviced'] = true;
        }
        $file = self::$dir . '/' . md5( $source ) . '.tpl';
        if ( !file_exists( $file ) )
            file_put_contents( $file, $source );
        $tpl = new eZTemplate();
        $tpl->autoload();
        $output = $tpl->fetch( $file );
        $this->assertSame( array(), $tpl->errorLog(), 'no error (an unknown operator is one)' );
        $this->assertSame( array(), $tpl->warningLog() );
        $this->assertSame( $expected, $output );
    }
}
