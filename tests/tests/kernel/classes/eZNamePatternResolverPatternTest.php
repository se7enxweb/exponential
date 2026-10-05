<?php
/**
 * Tests of how eZNamePatternResolver turns a class's object name pattern into a name: single tokens, alternatives
 * (<short_title|title>), groups (<(<first> <last>)|login>), several groups in one pattern, text around tokens and
 * attributes without a value.
 *
 * The object is a stand-in eZContentObject that returns attribute stand-ins with the given titles; the pattern is
 * resolved without the length limit, which needs the database (the limit is covered by the regression test of
 * the live installation). No database.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 */

class eZNamePatternResolverPatternAttributeStub
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

class eZNamePatternResolverPatternObjectStub extends eZContentObject
{
    public $t1Titles = array();

    public $t1Asked = array();

    public function __construct( $titles )
    {
        $this->t1Titles = $titles;
    }

    function fetchAttributesByIdentifier( $identifierArray, $version = false, $languageArray = false, $asObject = true )
    {
        $this->t1Asked = $identifierArray;
        $attributes = array();
        foreach ( $identifierArray as $identifier )
        {
            if ( array_key_exists( $identifier, $this->t1Titles ) )
                $attributes[] = new eZNamePatternResolverPatternAttributeStub( $identifier, $this->t1Titles[$identifier] );
        }
        return $attributes;
    }
}

class eZNamePatternResolverPatternTest extends PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 4 ) );
    }

    /**
     * The name before the length limit: fetchContentAttributes() and translatePattern() are what
     * resolveNamePattern() runs before it asks the database how long the name is.
     */
    private static function resolve( $pattern, $titles )
    {
        $resolver = new eZNamePatternResolver( $pattern, new eZNamePatternResolverPatternObjectStub( $titles ) );
        $fetch = new ReflectionMethod( $resolver, 'fetchContentAttributes' );
        if ( PHP_VERSION_ID < 80100 )
            $fetch->setAccessible( true );
        $fetch->invoke( $resolver );
        $translate = new ReflectionMethod( $resolver, 'translatePattern' );
        if ( PHP_VERSION_ID < 80100 )
            $translate->setAccessible( true );
        return $translate->invoke( $resolver );
    }

    public static function patternProvider()
    {
        $person = array( 'first_name' => 'Ada', 'last_name' => 'Lovelace', 'login' => 'ada', 'title' => 'Notes',
                         'short_title' => '', 'empty' => '' );
        return array(
            'single token'           => array( '<title>', $person, 'Notes' ),
            'text around'            => array( 'About <title>!', $person, 'About Notes!' ),
            'two tokens'             => array( '<first_name> <last_name>', $person, 'Ada Lovelace' ),
            'alternative first set'  => array( '<title|login>', $person, 'Notes' ),
            'alternative fallback'   => array( '<short_title|title>', $person, 'Notes' ),
            'nothing set'            => array( '<empty|short_title>', $person, '' ),
            'unknown attribute'      => array( '<no_such_attribute>', $person, '' ),
            'group'                  => array( '<(<first_name> <last_name>)|login>', $person, 'Ada Lovelace' ),
            'group falls back'       => array( '<(<first_name> <last_name>)|login>', array( 'login' => 'ada' ), 'ada' ),
            'group after text'       => array( 'By <(<first_name> <last_name>)>', $person, 'By Ada Lovelace' ),
            'two groups'             => array( '<(<first_name> <last_name>)> / <(<login>)>', $person, 'Ada Lovelace / ada' ),
            'group and token'        => array( '<(<first_name> <last_name>)> - <title>', $person, 'Ada Lovelace - Notes' ),
            'no tokens'              => array( 'Fixed name', $person, 'Fixed name' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('patternProvider')]
    public function testPattern( $pattern, $titles, $expected )
    {
        $this->assertSame( $expected, self::resolve( $pattern, $titles ) );
    }

    public function testOnlyTheAttributesOfThePatternAreFetched()
    {
        $object = new eZNamePatternResolverPatternObjectStub( array() );
        $resolver = new eZNamePatternResolver( '<(<first_name> <last_name>)|login> <title>', $object );
        $fetch = new ReflectionMethod( $resolver, 'fetchContentAttributes' );
        if ( PHP_VERSION_ID < 80100 )
            $fetch->setAccessible( true );
        $fetch->invoke( $resolver );
        sort( $object->t1Asked );
        $this->assertSame( array( 'first_name', 'last_name', 'login', 'title' ), array_values( array_unique( $object->t1Asked ) ) );
    }
}
