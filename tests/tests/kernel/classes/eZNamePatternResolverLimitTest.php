<?php
/**
 * The length limit of object names made from a name pattern, called the way LoveStack's test calls its resolver:
 * the object, version, translation, limit and end sequence go in, the shortened name comes out. In Exponential that
 * call is eZNamePatternResolver::resolve(); the constructor and resolveNamePattern( $limit, $sequence ) stay as they
 * were (LoveStack replaced them with instance() and a resolveNamePattern() taking the object, an API change
 * Exponential does not make).
 *
 *  NL-01 - an ASCII name is cut to the limit, the end sequence included (EZP-21410)
 *  NL-02 - white space and control characters before the cut are dropped
 *  NL-03 - a comma and a dot before the cut stay (only spaces and control characters are trimmed)
 *  NL-04 - a UTF-8 name is cut by characters, not bytes, with and without an ideographic comma at the cut
 *  NL-05 - a name within the limit, and a limit of 0 (the column size), leave the name as it is
 *  NL-06 - the same through the constructor and resolveNamePattern( $limit, $sequence )
 *
 * The cases are those of the upstream regression test (EZP-21410) that LoveStack adapted to its call in commit
 * 95d86f5990. No database: the database instance refuses queries; the string length and the cut are those of
 * eZDBInterface (multibyte), as on every Exponential driver.
 *
 * Portions derived from LoveStack (mugoweb/ezpublish-legacy, commit 95d86f5990), Copyright (C) Mugo Web, GPL v2
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/classes/eZNamePatternResolverLimitTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group kernel
 */

require_once __DIR__ . '/datatypes/eZDatatypeTestFixtures.php';

class eZNamePatternResolverLimitTestAttribute
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

class eZNamePatternResolverLimitTestObject extends eZContentObject
{
    private $limitTitles;

    public function __construct( array $titles )
    {
        $this->limitTitles = $titles;
    }

    function fetchAttributesByIdentifier( $identifierArray, $version = false, $languageArray = false, $asObject = true )
    {
        $attributes = array();
        foreach ( $identifierArray as $identifier )
        {
            if ( array_key_exists( $identifier, $this->limitTitles ) )
                $attributes[] = new eZNamePatternResolverLimitTestAttribute( $identifier, $this->limitTitles[$identifier] );
        }
        return $attributes;
    }
}

class eZNamePatternResolverLimitTest extends PHPUnit\Framework\TestCase
{
    const JAPANESE = '私は簡単にパブリッシュの記事で使用することができるようなもの、何でも、記述する必要が、それはそう、私はただ何を書き留めて、何を参照してくださいますね、あなたが実際にTIに考えている心の中で何かが出てくるのが難しいのようなものだ登場。ポイントは、私が百五文字が必要だということです。これで十分です。くそー、もっと2';

    private $hadDb;

    private $savedDb;

    private $savedResolvers;

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        $this->hadDb = array_key_exists( 'eZDBGlobalInstance', $GLOBALS );
        $this->savedDb = $this->hadDb ? $GLOBALS['eZDBGlobalInstance'] : null;
        eZDB::setInstance( new eZDatatypeTestNoDatabase() );
        $ini = eZINI::instance( 'content.ini' );
        $this->savedResolvers = $ini->hasVariable( 'NamePatternSettings', 'ResolverClasses' ) ? array( $ini->variable( 'NamePatternSettings', 'ResolverClasses' ) ) : null;
        $ini->setVariable( 'NamePatternSettings', 'ResolverClasses', array() );
    }

    protected function tearDown(): void
    {
        $ini = eZINI::instance( 'content.ini' );
        if ( $this->savedResolvers !== null )
            $ini->setVariable( 'NamePatternSettings', 'ResolverClasses', $this->savedResolvers[0] );
        else
            $ini->removeSetting( 'NamePatternSettings', 'ResolverClasses' );
        if ( $this->hadDb )
            $GLOBALS['eZDBGlobalInstance'] = $this->savedDb;
        else
            unset( $GLOBALS['eZDBGlobalInstance'] );
    }

    public static function limitProvider()
    {
        return array(
            'NL-01 ASCII cut'                      => array( "This is a Very Long Name isn't it?", 20, '..', 'This is a Very Lon..' ),
            'NL-02 white space trimmed'            => array( "This is a Very Long \n\t\r\0Name isn't it?", 25, '..', 'This is a Very Long..' ),
            'NL-03 comma and dot kept'             => array( "This is a Very Long,.Name isn't it?", 22, '..', 'This is a Very Long,..' ),
            'NL-04 UTF-8 cut'                      => array( self::JAPANESE, 31, '..', '私は簡単にパブリッシュの記事で使用することができるようなも..' ),
            'NL-04 UTF-8 cut at ideographic comma' => array( self::JAPANESE, 32, '..', '私は簡単にパブリッシュの記事で使用することができるようなもの..' ),
            'NL-05 no change needed'               => array( "A string that doesn't need to be altered", 0, '..', "A string that doesn't need to be altered" ),
            'NL-05 within the limit'               => array( 'Short', 10, '..', 'Short' ),
        );
    }

    /** NL-01 to NL-05 */
    #[\PHPUnit\Framework\Attributes\DataProvider('limitProvider')]
    public function testResolveLimitsTheName( $name, $limit, $sequence, $expected )
    {
        $object = new eZNamePatternResolverLimitTestObject( array( 'name' => $name ) );
        $result = eZNamePatternResolver::resolve( '<name>', $object, false, false, $limit, $sequence );
        $this->assertSame( $expected, $result );
        if ( $limit > 0 )
            $this->assertLessThanOrEqual( $limit, mb_strlen( $result, 'utf-8' ) );
    }

    /** NL-06 */
    #[\PHPUnit\Framework\Attributes\DataProvider('limitProvider')]
    public function testTheConstructorGivesTheSameName( $name, $limit, $sequence, $expected )
    {
        $resolver = new eZNamePatternResolver( '<name>', new eZNamePatternResolverLimitTestObject( array( 'name' => $name ) ) );
        $this->assertSame( $expected, $resolver->resolveNamePattern( $limit, $sequence ) );
    }
}
