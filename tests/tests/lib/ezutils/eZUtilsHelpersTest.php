<?php
/**
 * Tests of small lib/ezutils helpers that need no kernel or database:
 *   - the input validators eZRegExpValidator, eZIntegerValidator, eZFloatValidator, eZDateTimeValidator and
 *     eZFileExtensionBlackListValidator (states and fixup())
 *   - eZStringUtils::explodeStr()/implodeStr() with escaped delimiters
 *   - eZMath colour conversion, eZTimestamp UTC/local conversion
 *   - eZMimeType lookups by name, URL and suffix
 *   - eZPHPCreator: variables, defines, comments and code written to a PHP file and restored again
 *     (files in a private directory under var/tmp, removed afterwards)
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group lib
 * @group ezutils
 */

class eZUtilsHelpersTest extends PHPUnit\Framework\TestCase
{
    const ACCEPTED = eZInputValidator::STATE_ACCEPTED;
    const INTERMEDIATE = eZInputValidator::STATE_INTERMEDIATE;
    const INVALID = eZInputValidator::STATE_INVALID;

    private $dir;

    private $timezone;

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        $this->timezone = date_default_timezone_get();
    }

    protected function tearDown(): void
    {
        date_default_timezone_set( $this->timezone );
        if ( $this->dir !== null && is_dir( $this->dir ) )
        {
            foreach ( scandir( $this->dir ) as $entry )
            {
                if ( $entry !== '.' && $entry !== '..' )
                    unlink( $this->dir . '/' . $entry );
            }
            rmdir( $this->dir );
        }
    }

    private function privateDirectory()
    {
        $this->dir = 'var/tmp/phpunit-ezutils-' . getmypid() . '-' . substr( md5( uniqid( '', true ) ), 0, 8 );
        mkdir( $this->dir, 0777, true );
        return $this->dir;
    }

    // ---------------------------------------------------------------- integer and float validators

    public static function integerProvider()
    {
        return array(
            'integer'            => array( false, false, '42', self::ACCEPTED ),
            'negative'           => array( false, false, '-7', self::ACCEPTED ),
            'int value'          => array( false, false, 42, self::ACCEPTED ),
            'with text'          => array( false, false, '42abc', self::INTERMEDIATE ),
            'float text'         => array( false, false, '4.2', self::INTERMEDIATE ),
            'letters'            => array( false, false, 'abc', self::INVALID ),
            'empty'              => array( false, false, '', self::INVALID ),
            'null'               => array( false, false, null, self::INVALID ),
            'array'              => array( false, false, array( 1 ), self::INVALID ),
            'in range'           => array( 1, 10, '5', self::ACCEPTED ),
            'below range'        => array( 1, 10, '0', self::INTERMEDIATE ),
            'above range'        => array( 1, 10, '11', self::INTERMEDIATE ),
            'range edges'        => array( 1, 10, '10', self::ACCEPTED ),
            'only minimum'       => array( 5, false, '1000', self::ACCEPTED ),
            'only maximum'       => array( false, 5, '-1000', self::ACCEPTED ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('integerProvider')]
    public function testIntegerValidator( $min, $max, $text, $state )
    {
        $validator = new eZIntegerValidator( $min, $max );
        $this->assertSame( $state, $validator->validate( $text ) );
    }

    public static function integerFixupProvider()
    {
        return array(
            'digits from text' => array( false, false, 'abc42def', '42' ),
            'below minimum'    => array( 1, 10, '0', 1 ),
            'above maximum'    => array( 1, 10, '99', 10 ),
            'not text'         => array( 1, 10, array( 5 ), array( 5 ) ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('integerFixupProvider')]
    public function testIntegerFixup( $min, $max, $text, $expected )
    {
        $validator = new eZIntegerValidator( $min, $max );
        $this->assertSame( $expected, $validator->fixup( $text ) );
    }

    public function testIntegerRangeIsOrdered()
    {
        $validator = new eZIntegerValidator( 10, 1 );
        $this->assertSame( self::ACCEPTED, $validator->validate( '10' ) );
        $validator->setRange( 3, 4 );
        $this->assertSame( self::INTERMEDIATE, $validator->validate( '5' ) );
    }

    public static function floatProvider()
    {
        return array(
            'integer'      => array( false, false, '42', self::ACCEPTED ),
            'decimal'      => array( false, false, '4.25', self::ACCEPTED ),
            'negative'     => array( false, false, '-0.5', self::ACCEPTED ),
            'comma'        => array( false, false, '4,25', self::INTERMEDIATE ),
            'trailing dot' => array( false, false, '4.', self::INTERMEDIATE ),
            'letters'      => array( false, false, 'x', self::INVALID ),
            'in range'     => array( 0.5, 1.5, '1.0', self::ACCEPTED ),
            'out of range' => array( 0.5, 1.5, '1.6', self::INTERMEDIATE ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('floatProvider')]
    public function testFloatValidator( $min, $max, $text, $state )
    {
        $validator = new eZFloatValidator( $min, $max );
        $this->assertSame( $state, $validator->validate( $text ) );
    }

    public function testFloatFixup()
    {
        $validator = new eZFloatValidator( 0, 100 );
        $this->assertSame( '12.5', $validator->fixup( 'about 12.5 kg' ) );
        $this->assertSame( 100, $validator->fixup( '250' ) );
    }

    // ---------------------------------------------------------------- regexp validator

    public function testRegExpValidatorWithoutRuleIsInvalid()
    {
        $validator = new eZRegExpValidator();
        $this->assertSame( self::INVALID, $validator->validate( 'x' ) );
        $this->assertSame( 'x', $validator->fixup( 'x' ) );
    }

    public function testRegExpValidatorStates()
    {
        $validator = new eZRegExpValidator( array( 'accepted' => '/^[a-z]+$/', 'intermediate' => '/[a-z]/', 'fixup' => '' ) );
        $this->assertSame( self::ACCEPTED, $validator->validate( 'abc' ) );
        $this->assertSame( self::INTERMEDIATE, $validator->validate( 'ab1' ) );
        $this->assertSame( self::INVALID, $validator->validate( '123' ) );
    }

    /**
     * fixup() with a match/replace pair worked on references into the rule: the first call replaced the
     * intermediate expression by the match expression and the pair by its replacement, so validate() gave other
     * states afterwards.
     */
    public function testRegExpFixupLeavesTheRuleUnchanged()
    {
        $rule = array( 'accepted' => '/^[0-9]+$/',
                       'intermediate' => '/^[0-9 ]+$/',
                       'fixup' => array( 'match' => '/[^0-9]+/', 'replace' => '' ) );
        $validator = new eZRegExpValidator( $rule );
        $this->assertSame( self::INVALID, $validator->validate( 'a1' ) );
        $this->assertSame( '12', $validator->fixup( '1 2' ) );
        $this->assertSame( '34', $validator->fixup( '3x4' ) );
        $this->assertSame( self::INVALID, $validator->validate( 'a1' ), 'validate() after fixup()' );
        $this->assertSame( self::INTERMEDIATE, $validator->validate( '1 2' ) );
    }

    public function testRegExpFixupWithAPlainReplacement()
    {
        $validator = new eZRegExpValidator( array( 'accepted' => '/^x+$/', 'intermediate' => '/y/', 'fixup' => 'x' ) );
        $this->assertSame( 'xxx', $validator->fixup( 'yxy' ) );
    }

    // ---------------------------------------------------------------- date and time validator

    public static function dateProvider()
    {
        return array(
            'valid'         => array( 14, 11, 2023, self::ACCEPTED ),
            'leap day'      => array( 29, 2, 2024, self::ACCEPTED ),
            'no leap day'   => array( 29, 2, 2023, self::INVALID ),
            'month 13'      => array( 1, 13, 2023, self::INVALID ),
            'day 0'         => array( 0, 1, 2023, self::INVALID ),
            'numeric text'  => array( '14', '11', '2023', self::ACCEPTED ),
            'text'          => array( 'x', 11, 2023, self::INVALID ),
            'empty'         => array( '', 11, 2023, self::INVALID ),
            'null'          => array( null, 11, 2023, self::INVALID ),
            'array'         => array( array( 1 ), 11, 2023, self::INVALID ),
            'boolean'       => array( true, 11, 2023, self::INVALID ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('dateProvider')]
    public function testValidateDate( $day, $month, $year, $state )
    {
        $this->assertSame( $state, eZDateTimeValidator::validateDate( $day, $month, $year ) );
    }

    public static function timeProvider()
    {
        return array(
            'midnight'      => array( 0, 0, 0, self::ACCEPTED ),
            'last second'   => array( 23, 59, 59, self::ACCEPTED ),
            'hour 24'       => array( 24, 0, 0, self::INVALID ),
            'minute 60'     => array( 10, 60, 0, self::INVALID ),
            'second 60'     => array( 10, 0, 60, self::INVALID ),
            'negative'      => array( -1, 0, 0, self::INVALID ),
            'text numbers'  => array( '10', '05', '00', self::ACCEPTED ),
            'letters'       => array( 'ab', 0, 0, self::INVALID ),
            'digit letters' => array( '1a', 0, 0, self::INVALID ),
            'array'         => array( array(), 0, 0, self::INVALID ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('timeProvider')]
    public function testValidateTime( $hour, $minute, $second, $state )
    {
        $this->assertSame( $state, eZDateTimeValidator::validateTime( $hour, $minute, $second ) );
    }

    public static function dateTimeProvider()
    {
        return array(
            'valid'          => array( 14, 11, 2023, 22, 13, 20, self::ACCEPTED ),
            'invalid date'   => array( 31, 4, 2023, 10, 0, 0, self::INVALID ),
            'invalid hour'   => array( 1, 4, 2023, 25, 0, 0, self::INVALID ),
            'invalid second' => array( 1, 4, 2023, 10, 0, 75, self::INVALID ),
            'text hour'      => array( 1, 4, 2023, 'x', 0, 0, self::INVALID ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('dateTimeProvider')]
    public function testValidateDateTime( $day, $month, $year, $hour, $minute, $second, $state )
    {
        $this->assertSame( $state, eZDateTimeValidator::validateDateTime( $day, $month, $year, $hour, $minute, $second ) );
    }

    // ---------------------------------------------------------------- file extension black list

    public static function fileNameProvider()
    {
        return array(
            'image'            => array( 'photo.jpg', self::ACCEPTED ),
            'no extension'     => array( 'README', self::ACCEPTED ),
            'php'              => array( 'shell.php', self::INVALID ),
            'php upper case'   => array( 'shell.PHP', self::INVALID ),
            'phar'             => array( 'app.phar', self::INVALID ),
            'double extension' => array( 'photo.php.jpg', self::ACCEPTED ),
            'with directory'   => array( '../shell.jpg', self::INVALID ),
            'absolute path'    => array( '/etc/passwd', self::INVALID ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('fileNameProvider')]
    public function testFileExtensionBlackList( $filename, $state )
    {
        $validator = new eZFileExtensionBlackListValidator();
        $this->assertContains( 'php', $validator->extensionsBlackList() );
        $this->assertSame( $state, $validator->validate( $filename ) );
    }

    // ---------------------------------------------------------------- eZStringUtils

    public static function explodeProvider()
    {
        return array(
            'plain'             => array( 'a|b|c', '|', array( 'a', 'b', 'c' ) ),
            'escaped delimiter' => array( 'a\\|b|c', '|', array( 'a|b', 'c' ) ),
            'escaped backslash' => array( 'a\\\\|b', '|', array( 'a\\', 'b' ) ),
            'empty parts'       => array( '||', '|', array( '', '', '' ) ),
            'no delimiter'      => array( 'abc', '|', array( 'abc' ) ),
            'other delimiter'   => array( 'a;b\\;c', ';', array( 'a', 'b;c' ) ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('explodeProvider')]
    public function testExplodeStr( $text, $delimiter, $expected )
    {
        $this->assertSame( $expected, eZStringUtils::explodeStr( $text, $delimiter ) );
    }

    public static function roundTripProvider()
    {
        return array(
            array( array( 'a', 'b' ) ),
            array( array( 'with|pipe', 'with\\backslash', 'both\\|' ) ),
            array( array( '', 'x', '' ) ),
            array( array( 'trailing\\' , 'x' ) ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('roundTripProvider')]
    public function testImplodeThenExplodeGivesTheValuesBack( $values )
    {
        $this->assertSame( $values, eZStringUtils::explodeStr( eZStringUtils::implodeStr( $values ) ) );
    }

    // ---------------------------------------------------------------- eZMath, eZTimestamp

    public function testRgbToCmyk()
    {
        // black is given as "rich black", all three inks and no key; white as no ink at all
        $this->assertEquals( array( 'c' => 1, 'm' => 1, 'y' => 1, 'k' => 0 ), eZMath::rgbToCMYK2( 0, 0, 0 ) );
        $this->assertEquals( array( 'c' => 0, 'm' => 0, 'y' => 0, 'k' => 0 ), eZMath::rgbToCMYK2( 1, 1, 1 ) );
        $red = eZMath::rgbToCMYK2( 1, 0, 0 );
        $this->assertEqualsWithDelta( 0, $red['c'], 0.0001 );
        $this->assertEqualsWithDelta( 1, $red['m'], 0.0001 );
        $this->assertEqualsWithDelta( 1, $red['y'], 0.0001 );
        $this->assertEqualsWithDelta( 0, $red['k'], 0.0001 );
        $grey = eZMath::rgbToCMYK2( 0.8, 0.8, 0.8 );
        $this->assertEqualsWithDelta( 0.2, $grey['k'], 0.0001 );
    }

    public function testRgbToCmykClampsValues()
    {
        $this->assertEquals( eZMath::rgbToCMYK2( 1, 0, 0 ), eZMath::rgbToCMYK2( 5, -3, 0 ) );
    }

    public function testNormalizeColorArray()
    {
        $this->assertEquals( array( 'r' => 0.5, 'g' => 0.0, 'b' => 0.25 ), eZMath::normalizeColorArray( array( 'r' => 128, 'g' => 0, 'b' => 64 ) ) );
    }

    public function testTimestampConversionInUtcIsTheIdentity()
    {
        date_default_timezone_set( 'UTC' );
        $this->assertSame( 1700000000, eZTimestamp::getUtcTimestampFromLocalTimestamp( 1700000000 ) );
        $this->assertSame( 1700000000, eZTimestamp::getLocalTimestampFromUtcTimestamp( 1700000000 ) );
    }

    public function testTimestampConversionUsesTheOffsetOfTheTimeZone()
    {
        date_default_timezone_set( 'Europe/Oslo' );
        // 2023-11-14 is winter time, UTC+1
        $this->assertSame( 1700000000 + 3600, eZTimestamp::getUtcTimestampFromLocalTimestamp( 1700000000 ) );
        $this->assertSame( 1700000000 - 3600, eZTimestamp::getLocalTimestampFromUtcTimestamp( 1700000000 ) );
        $this->assertSame( 1700000000, eZTimestamp::getLocalTimestampFromUtcTimestamp( eZTimestamp::getUtcTimestampFromLocalTimestamp( 1700000000 ) ) );
    }

    public static function badTimestampProvider()
    {
        return array( 'null' => array( null ), 'empty' => array( '' ), 'text' => array( 'soon' ), 'array' => array( array( 1 ) ) );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('badTimestampProvider')]
    public function testTimestampConversionOfSomethingThatIsNoTimestamp( $value )
    {
        $this->assertNull( eZTimestamp::getUtcTimestampFromLocalTimestamp( $value ) );
        $this->assertNull( eZTimestamp::getLocalTimestampFromUtcTimestamp( $value ) );
    }

    // ---------------------------------------------------------------- eZMimeType

    public static function mimeByUrlProvider()
    {
        return array(
            'jpeg'           => array( 'images/photo.jpg', 'image/jpeg', 'jpg' ),
            'upper suffix'   => array( 'PHOTO.JPG', 'image/jpeg', 'jpg' ),
            'png'            => array( 'a.png', 'image/png', 'png' ),
            'pdf'            => array( 'doc/manual.pdf', 'application/pdf', 'pdf' ),
            'unknown suffix' => array( 'file.t1unknown', 'application/octet-stream', 't1unknown' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('mimeByUrlProvider')]
    public function testFindByURL( $url, $name, $suffix )
    {
        $mime = eZMimeType::findByURL( $url );
        $this->assertSame( $name, $mime['name'] );
        $this->assertSame( $suffix, $mime['suffix'] );
    }

    public function testFindByURLWithoutDefault()
    {
        $this->assertFalse( eZMimeType::findByURL( 'file.t1unknown', false ) );
    }

    public function testFindByURLSplitsThePath()
    {
        $mime = eZMimeType::findByURL( 'var/storage/photo.jpg' );
        $this->assertSame( 'var/storage', $mime['dirpath'] );
        $this->assertSame( 'photo', $mime['basename'] );
        $this->assertSame( 'photo.jpg', $mime['filename'] );
        $this->assertSame( 'var/storage/photo.jpg', $mime['url'] );
    }

    public function testFindByName()
    {
        $mime = eZMimeType::findByName( 'image/png' );
        $this->assertTrue( $mime['is_valid'] );
        $this->assertSame( 'png', $mime['suffix'] );
        $this->assertFalse( eZMimeType::findByName( 'x-t1/none', false ) );
        $default = eZMimeType::findByName( 'x-t1/none' );
        $defaultMime = eZMimeType::defaultMimeType();
        $this->assertSame( $defaultMime['name'], $default['name'] );
    }

    public function testChangeBasenameAndSuffix()
    {
        $mime = eZMimeType::findByURL( 'dir/photo.jpg' );
        eZMimeType::changeBasename( $mime, 'other' );
        $this->assertSame( 'dir/other.jpg', $mime['url'] );
        eZMimeType::changeDirectoryPath( $mime, 'new' );
        $this->assertSame( 'new/other.jpg', $mime['url'] );
        eZMimeType::changeMIMEType( $mime, 'image/png' );
        $this->assertSame( 'image/png', $mime['name'] );
        $this->assertSame( 'new/other.png', $mime['url'] );
    }

    // ---------------------------------------------------------------- eZPHPCreator

    public function testStoredVariablesAreRestored()
    {
        $dir = $this->privateDirectory();
        $values = array( 'text' => "quote ' and \\ backslash", 'number' => 42, 'float' => 1.5, 'flag' => true,
                         'list' => array( 1, 'two', array( 'three' => 3 ) ) );
        $php = new eZPHPCreator( $dir, 'vars.php' );
        foreach ( $values as $name => $value )
            $php->addVariable( $name, $value );
        $this->assertTrue( (bool)$php->store() );

        $restore = new eZPHPCreator( $dir, 'vars.php' );
        $this->assertTrue( $restore->exists() );
        $this->assertTrue( $restore->canRestore() );
        $restored = $restore->restore( array_combine( array_keys( $values ), array_keys( $values ) ) );
        $this->assertSame( $values, $restored );
    }

    public function testCanRestoreComparesTheTimestamp()
    {
        $dir = $this->privateDirectory();
        $php = new eZPHPCreator( $dir, 'stamp.php' );
        $php->addVariable( 'a', 1 );
        $php->store();
        touch( $dir . '/stamp.php', 1000 );
        clearstatcache();
        $check = new eZPHPCreator( $dir, 'stamp.php' );
        $this->assertTrue( $check->canRestore( 999 ) );
        $this->assertTrue( $check->canRestore( 1000 ) );
        $this->assertFalse( $check->canRestore( 1001 ) );
        $this->assertFalse( ( new eZPHPCreator( $dir, 'missing.php' ) )->canRestore() );
    }

    public function testGeneratedCodeIsValidPhp()
    {
        $php = new eZPHPCreator( 'var/tmp', 'not-written.php' );
        $php->addComment( 'a comment' );
        $php->addDefine( 'EZ_T1_CONSTANT_' . getmypid(), 5 );
        $php->addVariable( 'v', array( 'k' => 'v' ) );
        $php->addCodePiece( "\$w = \$v['k'] . '!';\n" );
        $php->addSpace();
        $code = $php->fetch( false );
        $this->assertStringContainsString( 'a comment', $code );
        $result = eval( $code . "\nreturn \$w;" );
        $this->assertSame( 'v!', $result );
    }

    public static function variableTextProvider()
    {
        return array(
            'string'  => array( "a'b", '"a\'b"' ),
            'integer' => array( 3, 3 ),
            'true'    => array( true, 'true' ),
            'null'    => array( null, 'null' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('variableTextProvider')]
    public function testVariableText( $value, $expected )
    {
        $this->assertSame( $expected, eZPHPCreator::variableText( $value ) );
    }

    public static function variableTextRoundTripProvider()
    {
        return array(
            array( "line\nbreak" ), array( '$dollar' ), array( 'a\\b' ), array( -2.5 ), array( array() ),
            array( array( 'x' => array( 'y' => array( 1, 2 ) ) ) ), array( array( 3 => 'c', 1 => 'a' ) ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('variableTextRoundTripProvider')]
    public function testVariableTextEvaluatesToTheValue( $value )
    {
        $this->assertSame( $value, eval( 'return ' . eZPHPCreator::variableText( $value ) . ';' ) );
    }
}
