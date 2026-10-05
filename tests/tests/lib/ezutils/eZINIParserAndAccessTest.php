<?php
/**
 * eZINI (lib/ezutils) on INI files of its own, read without cache, text codec or override directories:
 *   - parsing: groups, plain values, values containing "=", "#" comment lines and "##" comments at the end of a
 *     line, empty arrays (Name[]), list items (Name[]=x), hash items (Name[key]=x), a group given twice, the
 *     .append.php file read after the .ini file (its arrays restarting with Name[], its values replacing),
 *     the PHP comment wrapper of .append.php files, Windows line ends
 *   - access: variable(), hasVariable(), hasGroup()/hasSection(), group(), groups(), variableArray() (";" lists),
 *     variableMulti() with the "enabled" signature, assign(), settingType(), setVariable()/isVariableModified(),
 *     setVariables(), removeSetting()/removeGroup(), getNamedArray(), filename(), rootDir()
 *   - injected settings: injectSettings() replacing values, injectMergeSettings() adding to arrays, in variable(),
 *     variableMulti(), group(), groups(), hasVariable() and hasGroup()
 *   - eZINI::exists()
 *
 * Files go to a private directory under var/tmp that tearDown() removes; injected settings are cleared.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group lib
 * @group ezutils
 */

class eZINIParserAndAccessTest extends PHPUnit\Framework\TestCase
{
    private $dir;

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        $this->dir = 'var/tmp/phpunit-ezini-' . getmypid() . '-' . substr( md5( uniqid( '', true ) ), 0, 8 );
        mkdir( $this->dir, 0777, true );
        file_put_contents( $this->dir . '/x3.ini',
            "#?ini charset=\"utf-8\"?\n" .
            "# a comment\n" .
            "[Main]\n" .
            "Name=Exponential\n" .
            "Formula=a=b+c\n" .
            "Trailing=value ## a comment\n" .
            "Switch=enabled\n" .
            "Off=disabled\n" .
            "Number=42\n" .
            "Flag=true\n" .
            "Pairs=a;b;;c\n" .
            "Empty=\n" .
            "List[]\n" .
            "List[]=one\n" .
            "List[]=two\n" .
            "Hash[first]=1\n" .
            "Hash[second]=2\n" .
            "Nothing[]\n" .
            "\n" .
            "[Other]\n" .
            "Thing=x\r\n" .
            "[Main]\n" .
            "Again=joined\n"
        );
        file_put_contents( $this->dir . '/x3.ini.append.php',
            "<?php /* #?ini charset=\"utf-8\"?\n\n" .
            "[Main]\n" .
            "Name=Exponential CMS\n" .
            "List[]\n" .
            "List[]=three\n" .
            "Hash[third]=3\n" .
            "\n" .
            "[Appended]\n" .
            "Only=here\n" .
            "*/ ?>\n"
        );
    }

    protected function tearDown(): void
    {
        eZINI::injectSettings( array() );
        eZINI::injectMergeSettings( array() );
        foreach ( glob( $this->dir . '/*' ) as $file )
            unlink( $file );
        rmdir( $this->dir );
    }

    private function ini()
    {
        // file name, root dir, no text codec, no cache, no local overrides, direct access (.ini and .append.php only)
        return new eZINI( 'x3.ini', $this->dir, false, false, false, true );
    }

    public function testValues()
    {
        $ini = $this->ini();
        $this->assertSame( 'x3.ini', $ini->filename() );
        $this->assertSame( $this->dir, $ini->rootDir() );
        $this->assertSame( 'Exponential CMS', $ini->variable( 'Main', 'Name' ), 'the .append.php file comes last' );
        $this->assertSame( 'a=b+c', $ini->variable( 'Main', 'Formula' ) );
        $this->assertSame( 'value ', $ini->variable( 'Main', 'Trailing' ) );
        $this->assertSame( '', $ini->variable( 'Main', 'Empty' ) );
        $this->assertSame( 'joined', $ini->variable( 'Main', 'Again' ), 'a group given twice is one group' );
        $this->assertSame( 'x', $ini->variable( 'Other', 'Thing' ), 'Windows line ends' );
        $this->assertSame( 'here', $ini->variable( 'Appended', 'Only' ) );
        $this->assertFalse( @$ini->variable( 'Main', 'Missing' ) );
        $this->assertFalse( @$ini->variable( 'NoGroup', 'Missing' ) );
    }

    public function testArraysAndHashes()
    {
        $ini = $this->ini();
        $this->assertSame( array( 'three' ), $ini->variable( 'Main', 'List' ), 'Name[] in the .append.php file restarts the list' );
        $this->assertSame( array( 'first' => '1', 'second' => '2', 'third' => '3' ), $ini->variable( 'Main', 'Hash' ) );
        $this->assertSame( array(), $ini->variable( 'Main', 'Nothing' ) );
        $this->assertSame( array( 'a', 'b', '', 'c' ), $ini->variableArray( 'Main', 'Pairs' ) );
        $this->assertSame( array(), $ini->variableArray( 'Main', 'Empty' ) );
        $this->assertSame( array( 'first' => array( '1' ), 'second' => array( '2' ), 'third' => array( '3' ) ), $ini->variableArray( 'Main', 'Hash' ) );
    }

    public function testPresenceAndGroups()
    {
        $ini = $this->ini();
        $this->assertTrue( $ini->hasVariable( 'Main', 'Name' ) );
        $this->assertFalse( $ini->hasVariable( 'Main', 'Missing' ) );
        $this->assertTrue( $ini->hasGroup( 'Other' ) );
        $this->assertTrue( $ini->hasSection( 'Appended' ) );
        $this->assertFalse( $ini->hasGroup( 'NoGroup' ) );
        $this->assertSame( array( 'Thing' => 'x' ), $ini->group( 'Other' ) );
        $this->assertNull( @$ini->group( 'NoGroup' ) );
        $this->assertSame( array( 'Main', 'Other', 'Appended' ), array_keys( $ini->groups() ) );
        $this->assertSame( $ini->groups(), $ini->getNamedArray() );
    }

    public function testVariableMultiAndAssign()
    {
        $ini = $this->ini();
        $this->assertSame(
            array( 'name' => 'Exponential CMS', 'on' => true, 'off' => false, 'missing' => null ),
            $ini->variableMulti( 'Main', array( 'name' => 'Name', 'on' => 'Switch', 'off' => 'Off', 'missing' => 'Missing' ),
                                 array( 'on' => 'enabled', 'off' => 'enabled' ) )
        );
        $this->assertFalse( @$ini->variableMulti( 'NoGroup', array( 'Name' ) ) );

        $this->assertTrue( $ini->assign( 'Main', 'Number', $number ) );
        $this->assertSame( '42', $number );
        $this->assertFalse( $ini->assign( 'Main', 'Missing', $none ) );
    }

    public function testSettingType()
    {
        $ini = $this->ini();
        $this->assertSame( 'array', $ini->settingType( array() ) );
        $this->assertSame( 'numeric', $ini->settingType( '42' ) );
        $this->assertSame( 'true/false', $ini->settingType( 'true' ) );
        $this->assertSame( 'enable/disable', $ini->settingType( 'disabled' ) );
        $this->assertSame( 'string', $ini->settingType( 'Exponential' ) );
    }

    public function testChangesInMemory()
    {
        $ini = $this->ini();
        $this->assertFalse( $ini->isVariableModified( 'Main', 'Name' ) );
        $ini->setVariable( 'Main', 'Name', 'Changed' );
        $this->assertTrue( $ini->isVariableModified( 'Main', 'Name' ) );
        $this->assertSame( 'Changed', $ini->variable( 'Main', 'Name' ) );
        $ini->setVariables( array( 'New' => array( 'A' => '1', 'B' => '2' ) ) );
        $this->assertSame( array( 'A' => '1', 'B' => '2' ), $ini->group( 'New' ) );

        $ini->removeSetting( 'Other', 'Thing' );
        $this->assertFalse( $ini->hasGroup( 'Other' ), 'the last setting of a group takes the group with it' );
        $ini->removeGroup( 'Appended' );
        $this->assertFalse( $ini->hasGroup( 'Appended' ) );
        $this->assertFileExists( $this->dir . '/x3.ini', 'nothing is written' );
        $this->assertStringContainsString( 'Name=Exponential', file_get_contents( $this->dir . '/x3.ini' ) );
    }

    public function testInjectedSettings()
    {
        eZINI::injectSettings( array( 'x3.ini' => array(
            'Main' => array( 'Name' => 'Injected', 'Switch' => 'disabled', 'OnlyInjected' => 'yes' ),
            'InjectedGroup' => array( 'Key' => 'value' ),
        ) ) );
        $ini = $this->ini();
        $this->assertSame( 'Injected', $ini->variable( 'Main', 'Name' ) );
        $this->assertSame( 'yes', $ini->variable( 'Main', 'OnlyInjected' ) );
        $this->assertTrue( $ini->hasVariable( 'Main', 'OnlyInjected' ) );
        $this->assertTrue( $ini->hasGroup( 'InjectedGroup' ) );
        $this->assertSame( array( 'Key' => 'value' ), $ini->group( 'InjectedGroup' ), 'a group that only exists injected' );
        $this->assertSame( 'Injected', $ini->group( 'Main' )['Name'] );
        $this->assertSame( 'Injected', $ini->groups()['Main']['Name'] );
        $this->assertArrayHasKey( 'InjectedGroup', $ini->getNamedArray() );

        $multi = $ini->variableMulti( 'Main', array( 'Name', 'Switch' ), array( 1 => 'enabled' ) );
        $this->assertSame( array( 'Injected', false ), $multi, 'the signature applies to the injected value' );
    }

    public function testInjectedFalseIsAbsent()
    {
        eZINI::injectSettings( array( 'x3.ini' => array( 'Main' => array( 'Name' => false ) ) ) );
        $this->assertFalse( $this->ini()->hasVariable( 'Main', 'Name' ) );
    }

    /**
     * Merge settings add to the value found, from the files or injected; variableMulti() merged into the file value
     * only, so it ignored an injected array and failed on one that exists only injected.
     */
    public function testInjectedMergeSettings()
    {
        eZINI::injectSettings( array( 'x3.ini' => array( 'Main' => array( 'InjectedList' => array( 'i1' ) ) ) ) );
        eZINI::injectMergeSettings( array( 'x3.ini' => array( 'Main' => array( 'List' => array( 'merged' ), 'InjectedList' => array( 'i2' ) ) ) ) );
        $ini = $this->ini();
        $this->assertSame( array( 'three', 'merged' ), $ini->variable( 'Main', 'List' ) );
        $this->assertSame( array( 'i1', 'i2' ), $ini->variable( 'Main', 'InjectedList' ) );
        $this->assertSame( array( array( 'three', 'merged' ), array( 'i1', 'i2' ) ),
                           $ini->variableMulti( 'Main', array( 'List', 'InjectedList' ) ) );
        $this->assertSame( array( 'three', 'merged' ), $ini->groups()['Main']['List'] );
    }

    public function testMergeIntoAStringIsRefused()
    {
        eZINI::injectMergeSettings( array( 'x3.ini' => array( 'Main' => array( 'Name' => array( 'x' ) ) ) ) );
        $this->expectException( RuntimeException::class );
        $this->ini()->variable( 'Main', 'Name' );
    }

    public function testExists()
    {
        $this->assertTrue( eZINI::exists( 'x3.ini', $this->dir ) );
        $this->assertFalse( eZINI::exists( 'no-such.ini', $this->dir ) );
        $this->assertTrue( eZINI::exists( 'site.ini', 'settings' ) );
    }
}
