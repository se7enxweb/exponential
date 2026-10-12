<?php
/**
 * Name pattern resolver classes: a class's object name or URL alias name pattern "{ClassName}" lets that PHP
 * class make the name, through eZNamePatternResolver::resolve(). The class is used only when it is listed in
 * content.ini [NamePatternSettings] ResolverClasses[] and implements expNamePatternResolverInterface; any other
 * name is ignored and logged, and normal patterns resolve as before.
 *
 * No database: the object is a stand-in and the database instance one that refuses queries.
 *
 * The idea was first written for LoveStack by Mugo Web, with thanks:
 * Portions derived from LoveStack (mugoweb/ezpublish-legacy, commit 95d86f5990), Copyright (C) Mugo Web, GPL v2
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 */

require_once __DIR__ . '/datatypes/eZDatatypeTestFixtures.php';

class eZNamePatternResolverClassTestAttribute
{
    private $identifier;
    private $title;

    public function __construct( $identifier, $title )
    {
        $this->identifier = $identifier;
        $this->title = $title;
    }

    public function contentClassAttributeIdentifier()
    {
        return $this->identifier;
    }

    public function title()
    {
        return $this->title;
    }
}

class eZNamePatternResolverClassTestObject extends eZContentObject
{
    public $t1Titles = array();

    public function __construct( $titles )
    {
        $this->t1Titles = $titles;
    }

    function fetchAttributesByIdentifier( $identifierArray, $version = false, $languageArray = false, $asObject = true )
    {
        $attributes = array();
        foreach ( $identifierArray as $identifier )
        {
            if ( array_key_exists( $identifier, $this->t1Titles ) )
                $attributes[] = new eZNamePatternResolverClassTestAttribute( $identifier, $this->t1Titles[$identifier] );
        }
        return $attributes;
    }
}

/** A listed resolver: the name from the rest of the pattern and what it was asked. */
class eZNamePatternResolverClassTestResolver implements expNamePatternResolverInterface
{
    public static $calls = array();
    private $rest;

    public function __construct( $rest )
    {
        $this->rest = $rest;
    }

    public function resolveNamePattern( eZContentObject $object, $version = false, $translation = false, $length = false, $sequence = '' )
    {
        self::$calls[] = array( 'rest' => $this->rest, 'version' => $version, 'translation' => $translation, 'length' => $length );
        return 'Custom' . $this->rest;
    }
}

/** A class with the right method but without the interface. */
class eZNamePatternResolverClassTestNoInterface
{
    public static $made = 0;

    public function __construct( $rest )
    {
        self::$made++;
    }

    public function resolveNamePattern( eZContentObject $object, $version = false, $translation = false, $length = false, $sequence = '' )
    {
        return 'never';
    }
}

/** A listed resolver that fails. */
class eZNamePatternResolverClassTestFailing implements expNamePatternResolverInterface
{
    public function __construct( $rest )
    {
    }

    public function resolveNamePattern( eZContentObject $object, $version = false, $translation = false, $length = false, $sequence = '' )
    {
        throw new RuntimeException( 'k1 failure' );
    }
}

class eZNamePatternResolverClassTest extends PHPUnit\Framework\TestCase
{
    private $savedAllowed;
    private $hadDb;
    private $savedDb;

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        $ini = eZINI::instance( 'content.ini' );
        $this->savedAllowed = $ini->hasVariable( 'NamePatternSettings', 'ResolverClasses' ) ? array( $ini->variable( 'NamePatternSettings', 'ResolverClasses' ) ) : null;
        $ini->setVariable( 'NamePatternSettings', 'ResolverClasses', array( 'eZNamePatternResolverClassTestResolver', 'eZNamePatternResolverClassTestNoInterface', 'eZNamePatternResolverClassTestFailing', 'eZNamePatternResolverClassTestMissing' ) );
        $this->hadDb = array_key_exists( 'eZDBGlobalInstance', $GLOBALS );
        $this->savedDb = $this->hadDb ? $GLOBALS['eZDBGlobalInstance'] : null;
        eZDB::setInstance( new eZDatatypeTestNoDatabase() );
        eZNamePatternResolverClassTestResolver::$calls = array();
        eZNamePatternResolverClassTestNoInterface::$made = 0;
    }

    protected function tearDown(): void
    {
        $ini = eZINI::instance( 'content.ini' );
        if ( $this->savedAllowed !== null )
            $ini->setVariable( 'NamePatternSettings', 'ResolverClasses', $this->savedAllowed[0] );
        else if ( $ini->hasVariable( 'NamePatternSettings', 'ResolverClasses' ) )
            $ini->removeSetting( 'NamePatternSettings', 'ResolverClasses' );
        if ( $this->hadDb )
            $GLOBALS['eZDBGlobalInstance'] = $this->savedDb;
        else
            unset( $GLOBALS['eZDBGlobalInstance'] );
    }

    /**
     * Runs $callback with a debug instance of the test's own (its log files in a private directory under var/tmp)
     * and returns the warning and error texts it got. The debug globals are put back afterwards.
     */
    private function captureDebug( $callback, &$result )
    {
        $keys = array( 'eZDebugGlobalInstance', 'eZDebugEnabled', 'eZDebugLogOnly', 'eZDebugAlwaysLog', 'eZDebugLogFileEnabled' );
        $saved = array();
        foreach ( $keys as $key )
            $saved[$key] = array_key_exists( $key, $GLOBALS ) ? array( $GLOBALS[$key] ) : null;
        $dir = dirname( __DIR__, 4 ) . '/var/tmp/phpunit-namepattern-ezdebug-' . getmypid() . '-' . substr( md5( uniqid( '', true ) ), 0, 8 ) . '/';
        mkdir( $dir, 0777, true );
        try
        {
            unset( $GLOBALS['eZDebugAlwaysLog'], $GLOBALS['eZDebugLogFileEnabled'] );
            $debug = new eZDebug();
            foreach ( $debug->LogFiles as $level => $file )
                $debug->LogFiles[$level] = array( $dir, $file[1] );
            $GLOBALS['eZDebugGlobalInstance'] = $debug;
            $GLOBALS['eZDebugEnabled'] = true;
            $GLOBALS['eZDebugLogOnly'] = false;

            $result = $callback();

            $messages = array();
            foreach ( $debug->DebugStrings as $message )
            {
                if ( $message['Level'] == eZDebug::LEVEL_WARNING || $message['Level'] == eZDebug::LEVEL_ERROR )
                    $messages[] = $message['String'];
            }
            return $messages;
        }
        finally
        {
            foreach ( $saved as $key => $value )
            {
                if ( $value === null )
                    unset( $GLOBALS[$key] );
                else
                    $GLOBALS[$key] = $value[0];
            }
            foreach ( glob( $dir . '*' ) as $file )
                unlink( $file );
            rmdir( $dir );
        }
    }

    private function object()
    {
        return new eZNamePatternResolverClassTestObject( array( 'title' => 'Notes', 'short_title' => '' ) );
    }

    public function testListedClassMakesTheName()
    {
        $name = eZNamePatternResolver::resolve( '{eZNamePatternResolverClassTestResolver} rest <title>', $this->object(), 3, 'eng-GB', 50 );
        $this->assertSame( 'Custom rest <title>', $name );
        $this->assertSame( array( array( 'rest' => ' rest <title>', 'version' => 3, 'translation' => 'eng-GB', 'length' => 50 ) ),
                           eZNamePatternResolverClassTestResolver::$calls );
    }

    public function testCustomNameIsStillLimited()
    {
        $name = eZNamePatternResolver::resolve( '{eZNamePatternResolverClassTestResolver}' . str_repeat( 'x', 40 ), $this->object(), false, false, 10, '...' );
        $this->assertSame( 'Customx...', $name, 'the sequence counts in the limit, as for the tokens' );
    }

    public function testUnlistedClassIsIgnoredAndLogged()
    {
        $warnings = $this->captureDebug( function ()
        {
            return eZNamePatternResolver::resolve( '{eZNamePatternResolverClassTestUnlisted}<title>', $this->object() );
        }, $name );
        $this->assertSame( '{eZNamePatternResolverClassTestUnlisted}Notes', $name, 'resolved from the tokens as before' );
        $this->assertFalse( class_exists( 'eZNamePatternResolverClassTestUnlisted', false ) );
        $this->assertCount( 1, $warnings );
        $this->assertStringContainsString( 'eZNamePatternResolverClassTestUnlisted', $warnings[0] );
        $this->assertStringContainsString( 'ResolverClasses', $warnings[0] );
    }

    public function testListedClassWithoutTheInterfaceIsNotMade()
    {
        $messages = $this->captureDebug( function ()
        {
            return eZNamePatternResolver::resolve( '{eZNamePatternResolverClassTestNoInterface}<title>', $this->object() );
        }, $name );
        $this->assertSame( 0, eZNamePatternResolverClassTestNoInterface::$made, 'never constructed' );
        $this->assertSame( '{eZNamePatternResolverClassTestNoInterface}Notes', $name );
        $this->assertCount( 1, $messages );
        $this->assertStringContainsString( 'expNamePatternResolverInterface', $messages[0] );
    }

    public function testListedClassThatDoesNotExistFallsBack()
    {
        $messages = $this->captureDebug( function ()
        {
            return eZNamePatternResolver::resolve( '{eZNamePatternResolverClassTestMissing}', $this->object() );
        }, $name );
        $this->assertSame( '{eZNamePatternResolverClassTestMissing}', $name );
        $this->assertCount( 1, $messages );
    }

    public function testFailingResolverFallsBack()
    {
        $messages = $this->captureDebug( function ()
        {
            return eZNamePatternResolver::resolve( '{eZNamePatternResolverClassTestFailing}<title>', $this->object() );
        }, $name );
        $this->assertSame( '{eZNamePatternResolverClassTestFailing}Notes', $name );
        $this->assertCount( 1, $messages );
        $this->assertStringContainsString( 'k1 failure', $messages[0] );
    }

    public function testNoListMeansNoResolverClass()
    {
        eZINI::instance( 'content.ini' )->setVariable( 'NamePatternSettings', 'ResolverClasses', array() );
        $this->captureDebug( function ()
        {
            return eZNamePatternResolver::customResolver( '{eZNamePatternResolverClassTestResolver}' );
        }, $resolver );
        $this->assertNull( $resolver );
        $this->assertSame( array(), eZNamePatternResolverClassTestResolver::$calls );
    }

    public static function normalPatternProvider()
    {
        return array(
            'token'              => array( '<title>', 'Notes' ),
            'alternatives'       => array( '<short_title|title>', 'Notes' ),
            'text around'        => array( 'About <title>!', 'About Notes!' ),
            'brace not at start' => array( 'x {eZNamePatternResolverClassTestResolver}', 'x {eZNamePatternResolverClassTestResolver}' ),
            'not a class name'   => array( '{1abc} <title>', '{1abc} Notes' ),
            'empty'              => array( '', '' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('normalPatternProvider')]
    public function testNormalPatternsUnchanged( $pattern, $expected )
    {
        $messages = $this->captureDebug( function () use ( $pattern )
        {
            return eZNamePatternResolver::resolve( $pattern, $this->object() );
        }, $name );
        $this->assertSame( $expected, $name );
        $this->assertSame( array(), $messages );
        $this->assertSame( array(), eZNamePatternResolverClassTestResolver::$calls );
        $this->assertNull( eZNamePatternResolver::customResolver( $pattern ) );
    }

    public function testLimitNameMatchesTheResolverLimit()
    {
        $this->assertSame( 'abc', eZNamePatternResolver::limitName( 'abc', 10 ) );
        $this->assertSame( 'ab...', eZNamePatternResolver::limitName( 'abcdefghij', 5, '...' ) );
        $this->assertSame( 255, mb_strlen( eZNamePatternResolver::limitName( str_repeat( 'y', 300 ) ) ), 'no limit: the column size' );
    }
}
