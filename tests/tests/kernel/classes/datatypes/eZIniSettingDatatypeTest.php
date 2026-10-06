<?php
/**
 * The INI setting datatype (ezinisetting), which writes the value an editor enters into an INI file on publish:
 * the checks of file, section and setting names, values and typed values, the array input syntax, the locations a
 * class may write to, the object and class input validation, the text export and import, and the publish step's
 * refusal to write anything it would not have accepted from the form. Nothing is written to settings/.
 *
 * No database (see eZDatatypeTestFixtures.php).
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 * @group datatypes
 */

require_once __DIR__ . '/eZDatatypeTestFixtures.php';

class eZIniSettingDatatypeTest extends eZDatatypeTestCase
{
    public static function fileNameProvider()
    {
        return array(
            array( 'site.ini', true ), array( 'my-file_2.ini', true ), array( '../site.ini', false ), array( '/etc/passwd', false ),
            array( 'a/b.ini', false ), array( 'a..ini', false ), array( '.hidden', false ), array( '', false ), array( array(), false ), array( null, false ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('fileNameProvider')]
    public function testIniFileName( $name, $expected )
    {
        $this->assertSame( $expected, (bool)eZIniSettingType::isValidIniFileName( $name ) );
    }

    public static function nameProvider()
    {
        return array(
            array( 'SiteSettings', true ), array( 'Site Name', true ), array( '', false ), array( '  ', false ), array( "A\nB", false ),
            array( 'A[0]', false ), array( 'A=B', false ), array( 'A*/B', false ), array( 'A*B', true ), array( 5, false ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('nameProvider')]
    public function testIniName( $name, $expected )
    {
        $this->assertSame( $expected, eZIniSettingType::isValidIniName( $name ) );
    }

    public static function valueProvider()
    {
        return array(
            array( 'plain value', true ), array( "tab\tseparated", true ), array( '', true ), array( "two\nlines", false ),
            array( "carriage\rreturn", false ), array( 'ends */ comment', false ), array( "nul\x00", false ), array( 1, false ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('valueProvider')]
    public function testIniValue( $value, $expected )
    {
        $this->assertSame( $expected, eZIniSettingType::isValidIniValue( $value ) );
    }

    public static function typedValueProvider()
    {
        return array(
            array( 1, 'anything', true ), array( 2, 'enabled', true ), array( 2, 'yes', false ), array( 3, 'false', true ),
            array( 3, 'no', false ), array( 4, '-12', true ), array( 4, '1.5', false ), array( 5, '1.5e3', true ),
            array( 5, 'x', false ), array( 2, '', true ), array( 4, null, false ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('typedValueProvider')]
    public function testTypedValue( $type, $value, $expected )
    {
        $this->assertSame( $expected, eZIniSettingType::isValidTypedValue( $type, $value ) );
    }

    public function testArrayInputSyntax()
    {
        $out = array();
        $this->assertTrue( eZIniSettingType::parseArrayInput( "key=value\r\nother=a=b\n=listed\nx\n", $out ) );
        $this->assertSame( array( 'key' => 'value', 'other' => 'a=b', 0 => 'listed' ), $out );
        $out = array();
        $this->assertFalse( eZIniSettingType::parseArrayInput( "no equals sign here", $out ) );
        $out = array();
        $this->assertTrue( eZIniSettingType::parseArrayInput( null, $out, true ) );
        $this->assertSame( array( '' ), $out );
        $this->assertFalse( eZIniSettingType::parseArrayInput( array(), $out ) );

        $this->assertTrue( eZIniSettingType::isValidIniArrayText( "a=1\nb=2" ) );
        $this->assertFalse( eZIniSettingType::isValidIniArrayText( "a[x]=1" ) );
        $this->assertFalse( eZIniSettingType::isValidIniArrayText( "a=1 */" ) );
        $this->assertFalse( eZIniSettingType::isValidIniArrayText( "bad line" ) );
        $this->assertFalse( eZIniSettingType::isValidIniArrayText( 1 ) );
    }

    public function testInstancePaths()
    {
        $siteAccesses = array( 'override', 'eng', '../x' );
        $this->assertSame( 'settings/override', eZIniSettingType::iniInstancePath( 0, $siteAccesses ) );
        $this->assertSame( 'settings/override', eZIniSettingType::iniInstancePath( '', $siteAccesses ) );
        $this->assertSame( 'settings/siteaccess/eng', eZIniSettingType::iniInstancePath( '1', $siteAccesses ) );
        $this->assertFalse( eZIniSettingType::iniInstancePath( 2, $siteAccesses ), 'a site access name with a directory part' );
        $this->assertFalse( eZIniSettingType::iniInstancePath( 7, $siteAccesses ) );
        $this->assertFalse( eZIniSettingType::iniInstancePath( '-1', $siteAccesses ) );
        $this->assertSame( 'settings', eZIniSettingType::iniInstancePath( '-1', $siteAccesses, true ) );
        $this->assertFalse( eZIniSettingType::iniInstancePath( 'eng', $siteAccesses ) );
        $this->assertFalse( eZIniSettingType::iniInstancePath( array(), $siteAccesses ) );
    }

    private function settingClass( array $fields = array() )
    {
        return $this->classAttribute( 'ezinisetting', array_merge( array( 'data_text1' => 'site.ini', 'data_text2' => 'SiteSettings',
                                                                          'data_text3' => 'SiteName', 'data_int1' => 1,
                                                                          'data_text4' => '0', 'data_text5' => 'override' ), $fields ) );
    }

    public static function objectInputProvider()
    {
        return array(
            'text' => array( array(), 'My site', eZInputValidator::STATE_ACCEPTED ),
            'line break' => array( array(), "My site\n[Hack]", eZInputValidator::STATE_INVALID ),
            'comment end' => array( array(), 'x */', eZInputValidator::STATE_INVALID ),
            'array' => array( array(), array( 'x' ), eZInputValidator::STATE_INVALID ),
            'enabled type' => array( array( 'data_int1' => 2 ), 'enabled', eZInputValidator::STATE_ACCEPTED ),
            'enabled type, other word' => array( array( 'data_int1' => 2 ), 'on', eZInputValidator::STATE_INVALID ),
            'integer type' => array( array( 'data_int1' => 4 ), ' 42 ', eZInputValidator::STATE_ACCEPTED ),
            'array type' => array( array( 'data_int1' => 6 ), "a=1\nb=2", eZInputValidator::STATE_ACCEPTED ),
            'array type, bad line' => array( array( 'data_int1' => 6 ), "a=1\nnope", eZInputValidator::STATE_INVALID ),
            'file outside settings' => array( array( 'data_text1' => '../site.ini' ), 'x', eZInputValidator::STATE_INVALID ),
            'bad section' => array( array( 'data_text2' => "A\nB" ), 'x', eZInputValidator::STATE_INVALID ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('objectInputProvider')]
    public function testObjectInput( $classFields, $value, $expected )
    {
        $attribute = $this->objectAttribute( 'ezinisetting', $this->settingClass( $classFields ) );
        $http = $this->post( array( 'ContentObjectAttribute_ini_setting_4711' => $value ) );
        $this->assertSame( $expected, $this->dataType( 'ezinisetting' )->validateObjectAttributeHTTPInput( $http, 'ContentObjectAttribute', $attribute ) );
    }

    public function testObjectFetchAndText()
    {
        $type = $this->dataType( 'ezinisetting' );
        $attribute = $this->objectAttribute( 'ezinisetting', $this->settingClass() );
        $this->assertSame( eZInputValidator::STATE_ACCEPTED, $type->validateObjectAttributeHTTPInput( $this->post( array() ), 'ContentObjectAttribute', $attribute ) );
        $this->assertFalse( $type->fetchObjectAttributeHTTPInput( $this->post( array() ), 'ContentObjectAttribute', $attribute ) );
        $this->assertFalse( $type->fetchObjectAttributeHTTPInput( $this->post( array( 'ContentObjectAttribute_ini_setting_4711' => array() ) ), 'ContentObjectAttribute', $attribute ) );
        $this->assertTrue( $type->fetchObjectAttributeHTTPInput( $this->post( array( 'ContentObjectAttribute_ini_setting_4711' => ' Name ', 'ContentObjectAttribute_ini_setting_make_empty_array_4711' => 'on' ) ), 'ContentObjectAttribute', $attribute ) );
        $this->assertSame( 'Name', $attribute->attribute( 'data_text' ) );
        $this->assertSame( 1, $attribute->attribute( 'data_int' ) );
        $this->assertSame( 'Name|1', $type->toString( $attribute ) );
        $this->assertTrue( $type->fetchObjectAttributeHTTPInput( $this->post( array( 'ContentObjectAttribute_ini_setting_4711' => 'a|b' ) ), 'ContentObjectAttribute', $attribute ) );
        $this->assertSame( 0, $attribute->attribute( 'data_int' ) );

        $copy = $this->objectAttribute( 'ezinisetting', $this->settingClass() );
        $this->assertTrue( $type->fromString( $copy, $type->toString( $attribute ) ) );
        $this->assertSame( 'a|b', $copy->attribute( 'data_text' ) );
        $this->assertSame( 0, $copy->attribute( 'data_int' ) );
        $this->assertTrue( $type->fromString( $copy, 'only|text' ) );
        $this->assertSame( 'only|text', $copy->attribute( 'data_text' ) );
        $this->assertTrue( $type->fromString( $copy, '' ) );
        $this->assertSame( 'only|text', $copy->attribute( 'data_text' ) );
    }

    public static function classInputProvider()
    {
        return array(
            'valid' => array( 'site.ini', 'SiteSettings', 'SiteName', '1', null, eZInputValidator::STATE_ACCEPTED ),
            'unknown section' => array( 'site.ini', 'K1NoSuchSection', 'SiteName', '1', null, eZInputValidator::STATE_INVALID ),
            'file outside' => array( '../x.ini', 'SiteSettings', 'SiteName', '1', null, eZInputValidator::STATE_INVALID ),
            'type 7' => array( 'site.ini', 'SiteSettings', 'SiteName', '7', null, eZInputValidator::STATE_INVALID ),
            'override location' => array( 'site.ini', 'SiteSettings', 'SiteName', '1', array( '0' ), eZInputValidator::STATE_ACCEPTED ),
            'unknown location' => array( 'site.ini', 'SiteSettings', 'SiteName', '1', array( '999' ), eZInputValidator::STATE_INVALID ),
            'location by name' => array( 'site.ini', 'SiteSettings', 'SiteName', '1', 'override', eZInputValidator::STATE_INVALID ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('classInputProvider')]
    public function testClassInput( $file, $section, $parameter, $type, $instances, $expected )
    {
        $post = array( 'ContentClass_ezinisetting_file_901' => $file, 'ContentClass_ezinisetting_section_901' => $section,
                       'ContentClass_ezinisetting_parameter_901' => $parameter, 'ContentClass_ezinisetting_type_901' => $type );
        if ( $instances !== null )
            $post['ContentClass_ezinisetting_ini_instance_901'] = $instances;
        $this->assertSame( $expected, $this->dataType( 'ezinisetting' )->validateClassAttributeHTTPInput( $this->post( $post ), 'ContentClass', $this->settingClass() ) );
    }

    public function testClassInputMissing()
    {
        $this->assertSame( eZInputValidator::STATE_INVALID, $this->dataType( 'ezinisetting' )->validateClassAttributeHTTPInput( $this->post( array() ), 'ContentClass', $this->settingClass() ) );
    }

    public function testPublishWritesNothingItWouldNotAccept()
    {
        $type = $this->dataType( 'ezinisetting' );
        foreach ( array( array( array( 'data_text1' => '../../etc/x.ini' ), 'v' ),
                         array( array( 'data_text2' => "S\n[T]" ), 'v' ),
                         array( array(), "value\nInjected=1" ),
                         array( array( 'data_int1' => 6 ), "a=1\n*/" ) ) as list( $classFields, $value ) )
        {
            $attribute = $this->objectAttribute( 'ezinisetting', $this->settingClass( array_merge( array( 'data_text4' => '-1' ), $classFields ) ), array( 'data_text' => $value ) );
            $before = @filemtime( 'settings/override/site.ini.append.php' );
            $type->onPublish( $attribute, null, array() );
            $this->assertSame( $before, @filemtime( 'settings/override/site.ini.append.php' ) );
        }
        // a location that names no site access is skipped, nothing is written either
        $attribute = $this->objectAttribute( 'ezinisetting', $this->settingClass( array( 'data_text1' => 'k1nosuchfile.ini', 'data_text4' => '5' ) ), array( 'data_text' => 'v' ) );
        $type->onPublish( $attribute, null, array() );
        $this->assertFileDoesNotExist( 'settings/override/k1nosuchfile.ini.append.php' );
        $this->assertFileDoesNotExist( 'settings/k1nosuchfile.ini.append' );
    }
}
