<?php
/**
 * The help of exp:velocity and exp:webserver describes what the commands do.
 *
 * Reads the command sources only: no kernel, no database, no running server.
 *
 * @copyright Copyright (C) 7x / Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file.
 * @package tests
 */
class expVelocityHelpTextTest extends PHPUnit\Framework\TestCase
{
    private static function source( $name )
    {
        $file = dirname( __DIR__, 5 ) . '/kernel/private/classes/commands/' . $name;
        $source = file_get_contents( $file );
        if ( $source === false )
            self::fail( "Cannot read $file" );
        return $source;
    }

    /**
     * The help text: the 'description' passed to script(), its string pieces joined.
     */
    private static function helpText( $source )
    {
        if ( !preg_match( "/'description' => \((.*?)\),\s*'use-session'/s", $source, $m ) )
            self::fail( 'No description block found' );
        preg_match_all( '/"((?:[^"\\\\]|\\\\.)*)"/s', $m[1], $parts );
        return stripcslashes( implode( '', $parts[1] ) );
    }

    /**
     * @testdox every verb exp:velocity accepts is listed under Commands in its help
     */
    public function testEveryVelocityVerbIsInTheHelp(): void
    {
        $source = self::source( 'velocity.php' );
        $this->assertSame( 1, preg_match( '/\$verbs = array\((.*?)\);/s', $source, $m ) );
        preg_match_all( "/'([a-z]+)'/", $m[1], $verbs );
        $this->assertContains( 'ssl', $verbs[1] );

        $help = self::helpText( $source );
        $this->assertSame( 1, preg_match( '/^Commands:\n(.*?)\n\n/ms', $help, $c ) );
        foreach ( $verbs[1] as $verb )
        {
            $this->assertMatchesRegularExpression( '/^  (?:\S+\|)*' . $verb . '(?:\|\S+)*\s/m', $c[1],
                "exp:velocity --help lists the '$verb' verb" );
        }
    }

    /**
     * @testdox exp:webserver --help recommends the qbix engine for every stage, as exp:velocity --help does
     */
    public function testWebserverHelpDescribesQbixAsRecommended(): void
    {
        $help = self::helpText( self::source( 'webserver.php' ) );
        $this->assertStringNotContainsString( 'experimental', $help );
        $this->assertMatchesRegularExpression( '/^  qbix\s+.*recommended for every stage/m', $help );

        $velocityHelp = self::helpText( self::source( 'velocity.php' ) );
        $this->assertMatchesRegularExpression( '/^  qbix\s+.*recommended for every stage/m', $velocityHelp );
    }
}
