<?php
/**
 * Object relations in the "fields:" of a content step, without a database and without symfony/yaml: the identifiers
 * exp:migration:generate --from-content writes (remote:<remote id>, a list of them for an object relation list)
 * become the object ids the datatype's fromString() reads, and pass the check of a field map.
 *
 *  MR-01 an identifier, a list of them or a text separated by "," becomes ids joined by "-"; ids stay as they are;
 *        an empty value clears the attribute
 *  MR-02 an object that is not here, two objects for an object relation and a nested value are refused
 *  MR-03 the check of a field map takes a list of texts and numbers, never a map or a nested list
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */
class expMigrationRelationFieldsTest extends PHPUnit\Framework\TestCase
{
    private static function resolver()
    {
        $ids = array( 'remote:abc-1' => 57, 'remote:def' => 58, 'path:media/logo' => 59 );
        return function ( $identifier ) use ( $ids ) {
            if ( !isset( $ids[$identifier] ) )
                throw new RuntimeException( "no object $identifier on this installation" );
            return $ids[$identifier];
        };
    }

    private static function text( $datatype, $value )
    {
        return expMigrationStepContentBase::relationText( $datatype, $value, self::resolver(), 'related' );
    }

    /** MR-01 */
    public function testIdentifiersBecomeIds()
    {
        $this->assertSame( '57', self::text( 'ezobjectrelation', 'remote:abc-1' ) );
        $this->assertSame( '57-58-42', self::text( 'ezobjectrelationlist', array( 'remote:abc-1', 'remote:def', 42 ) ) );
        $this->assertSame( '57-59', self::text( 'ezobjectrelationlist', 'remote:abc-1, path:media/logo' ) );
        $this->assertSame( '57', self::text( 'ezobjectrelationlist', 'remote:abc-1' ) );
        $this->assertSame( '12-34', self::text( 'ezobjectrelationlist', '12-34' ), 'ids as fromString() takes them' );
        $this->assertSame( '12', self::text( 'ezobjectrelation', 12 ) );
        foreach ( array( '', ' ', array(), false ) as $empty )
            $this->assertSame( '', self::text( 'ezobjectrelationlist', $empty ) );
    }

    /** MR-02 */
    public function testWhatNamesNoObjectIsRefused()
    {
        foreach ( array( array( 'ezobjectrelation', array( 'remote:abc-1', 'remote:def' ), 'related: an object relation names one object, not 2' ),
                         array( 'ezobjectrelationlist', array( 'remote:abc-1', 'remote:gone' ), 'related: no object remote:gone on this installation' ),
                         array( 'ezobjectrelation', 'remote:gone', 'related: no object remote:gone' ),
                         array( 'ezobjectrelationlist', array( array( 'remote:abc-1' ) ), 'related: an object is named by an identifier' ) ) as $case )
        {
            try
            {
                self::text( $case[0], $case[1] );
                $this->fail( 'refused: ' . json_encode( $case[1] ) );
            }
            catch ( RuntimeException $e )
            {
                $this->assertStringContainsString( $case[2], $e->getMessage() );
            }
        }
    }

    /** MR-03 */
    public function testFieldMapsTakeLists()
    {
        $problems = new ReflectionMethod( 'expMigrationStepContentBase', 'fieldProblems' );
        if ( PHP_VERSION_ID < 80100 )
            $problems->setAccessible( true );
        $this->assertSame( array(), $problems->invoke( null, array( 'billboard' => 'remote:abc-1', 'related' => array( 'remote:abc-1', 7 ),
                                                                    'none' => array(), 'title' => 'Front' ), 'fields' ) );
        $this->assertSame( array( 'fields.tags: a value is a text, a number, true/false, null or a list of object identifiers (an object relation list), not a map',
                                  'fields.nested: a value is a text, a number, true/false, null or a list of object identifiers (an object relation list), not a map' ),
                           $problems->invoke( null, array( 'tags' => array( 'a' => 'b' ), 'nested' => array( array( 'x' ) ) ), 'fields' ) );
    }
}
