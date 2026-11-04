<?php
/**
 * {continue} and {skip} in a compiled {do} loop go on to the {/do while} condition, as they do interpreted.
 *
 * The compiled {do} was a while ( 1 ) loop with the condition at the end of the body, so the PHP continue that
 * {continue} and {skip} compile to jumped back to the top of the body without testing the condition: a loop whose
 * last iteration ran {continue} never ended (or ran on until some later iteration reached the end of the body).
 * The interpreter tests the condition after a {continue}. Each case is rendered both ways and must give the same text.
 *
 * No kernel bootstrap, no database and no siteaccess: a bare eZTemplate with the lib/eztemplate autoloads.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group lib
 * @group eztemplate
 */

#[\PHPUnit\Framework\Attributes\Group('lib')]
#[\PHPUnit\Framework\Attributes\Group('eztemplate')]
class eZTemplateCompiledDoContinueTest extends PHPUnit\Framework\TestCase
{
    /** @var string directory of this class's template files and compiled templates */
    private static $dir;

    private $savedGlobals = array();

    public static function setUpBeforeClass(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        self::$dir = 'var/tmp/phpunit-eztemplate-do-continue-' . getmypid() . '-' . substr( md5( uniqid( '', true ) ), 0, 8 );
        mkdir( self::$dir . '/compiled', 0777, true );
    }

    public static function tearDownAfterClass(): void
    {
        self::removeTree( self::$dir );
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

    private static function removeTree( $path )
    {
        if ( !$path || !file_exists( $path ) )
            return;
        if ( is_dir( $path ) && !is_link( $path ) )
        {
            foreach ( scandir( $path ) as $entry )
            {
                if ( $entry !== '.' && $entry !== '..' )
                    self::removeTree( $path . '/' . $entry );
            }
            rmdir( $path );
        }
        else
            unlink( $path );
    }

    private function render( $source, $compiled )
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
        return array( $tpl->fetch( $file ), $tpl->errorLog() );
    }

    /**
     * Cases: name => array( template, expected ). The {continue} cases stop at $i = 6 in any case, so a loop that
     * ignores the condition after {continue} prints a 6 instead of running forever.
     */
    public static function caseTable()
    {
        return array(
            'continue in an if on the last iteration' =>
                array( '{def $i=0}{do}{set $i=inc($i)}{if and(ge($i,3),lt($i,6))}{continue}{/if}{$i}{/do while lt($i,3)}', '12' ),
            'continue in an if in the middle'         =>
                array( '{def $i=0}{do}{set $i=inc($i)}{if eq($i,2)}{continue}{/if}{$i}{/do while lt($i,4)}', '134' ),
            'skip in an if on the last iteration'     =>
                array( '{def $i=0}{do}{set $i=inc($i)}{if and(ge($i,3),lt($i,6))}{skip}{/if}{$i}{/do while lt($i,3)}', '12' ),
            'continue as a direct child'              =>
                array( '{def $i=0}{do}{set $i=inc($i)}{$i}{if ge($i,6)}{break}{/if}{continue}x{/do while lt($i,2)}', '12' ),
            'break still ends the loop'               =>
                array( '{def $i=0}{do}{set $i=inc($i)}{if eq($i,2)}{break}{/if}{$i}{/do while lt($i,5)}', '1' ),
            'body runs once with a false condition'   =>
                array( '{def $i=0}{do}{set $i=inc($i)}{$i}{/do while false()}', '1' ),
            'sequence goes on after a full iteration' =>
                array( '{def $i=0}{do}{set $i=inc($i)}{$s}{/do while lt($i,3) sequence array(\'a\',\'b\') as $s}', 'aba' ),
            'nested do loops'                         =>
                array( '{def $i=0 $j=0}{do}{set $i=inc($i) $j=0}{do}{set $j=inc($j)}{if and(ge($j,2),lt($j,6))}{continue}{/if}{$j}{/do while lt($j,2)}-{/do while lt($i,2)}', '1-1-' ),
        );
    }

    public static function renderProvider()
    {
        $rows = array();
        foreach ( self::caseTable() as $name => $case )
        {
            foreach ( array( 'interpreted' => false, 'compiled' => true ) as $mode => $compiled )
                $rows["$name, $mode"] = array( $case[0], $compiled, $case[1] );
        }
        return $rows;
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('renderProvider')]
    public function testOutput( $source, $compiled, $expected )
    {
        list( $output, $errors ) = $this->render( $source, $compiled );
        $this->assertSame( array(), $errors, 'template errors' );
        $this->assertSame( $expected, $output );
    }
}
