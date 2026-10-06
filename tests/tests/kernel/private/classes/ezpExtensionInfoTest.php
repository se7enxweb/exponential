<?php
/**
 * ezpExtension: the load order from extension.xml (requires and uses load after, extends loads before), and the
 * extension information of the about page from <metadata> of extension.xml, with third-party software, falling back
 * to ezinfo.php when extension.xml has no metadata, and broken files skipped instead of failing.
 *
 * No database. The extensions are made for the test under var/tmp (a directory added to site.ini
 * [ExtensionSettings] AdditionalExtensionDirectories, put back in tearDown()) with names used only here.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 */

class ezpExtensionInfoTest extends PHPUnit\Framework\TestCase
{
    private $root;
    private $saved;
    private $prefix;

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 5 ) );
        $this->root = 'var/tmp/phpunit-k1b-extensions-' . getmypid() . '-' . mt_rand();
        mkdir( $this->root, 0777, true );
        $ini = eZINI::instance();
        $this->saved = $ini->hasVariable( 'ExtensionSettings', 'AdditionalExtensionDirectories' ) ? array( $ini->variable( 'ExtensionSettings', 'AdditionalExtensionDirectories' ) ) : null;
        $directories = $this->saved ? (array)$this->saved[0] : array();
        $directories[] = $this->root;
        $ini->setVariable( 'ExtensionSettings', 'AdditionalExtensionDirectories', $directories );
        $this->prefix = 'k1bext' . substr( md5( $this->root ), 0, 8 );
    }

    protected function tearDown(): void
    {
        $ini = eZINI::instance();
        if ( $this->saved === null )
            $ini->removeSetting( 'ExtensionSettings', 'AdditionalExtensionDirectories' );
        else
            $ini->setVariable( 'ExtensionSettings', 'AdditionalExtensionDirectories', $this->saved[0] );
        if ( is_dir( $this->root ) )
            eZDir::recursiveDelete( $this->root );
    }

    private function extension( $suffix, array $files )
    {
        $name = $this->prefix . $suffix;
        mkdir( $this->root . '/' . $name );
        foreach ( $files as $file => $content )
            file_put_contents( $this->root . '/' . $name . '/' . $file, str_replace( '%NAME%', $name, $content ) );
        return $name;
    }

    public function testInstancesAreSharedByName()
    {
        $this->assertSame( ezpExtension::getInstance( $this->prefix . 'a' ), ezpExtension::getInstance( $this->prefix . 'a' ) );
        $this->assertNotSame( ezpExtension::getInstance( $this->prefix . 'a' ), ezpExtension::getInstance( $this->prefix . 'b' ) );
    }

    public function testUnknownExtensionHasNoInfoAndNoOrder()
    {
        $extension = ezpExtension::getInstance( $this->prefix . 'missing' );
        $this->assertNull( $extension->getInfo() );
        $this->assertSame( array( 'before' => array(), 'after' => array() ), $extension->getLoadingOrder() );
    }

    public function testLoadingOrder()
    {
        $name = $this->extension( 'order', array( 'extension.xml' => '<?xml version="1.0"?><extension name="x"><dependencies>'
            . '<requires><extension name="core"/><extension name="base"/></requires><uses><extension name="opt"/></uses>'
            . '<extends><extension name="parent"/></extends></dependencies></extension>' ) );
        $this->assertSame( array( 'before' => array( 'parent' ), 'after' => array( 'core', 'base', 'opt' ) ),
                           ezpExtension::getInstance( $name )->getLoadingOrder() );
    }

    public function testLoadingOrderWithoutExtensionXml()
    {
        $name = $this->extension( 'noxml', array( 'readme.txt' => 'x' ) );
        $this->assertSame( array( 'before' => array(), 'after' => array() ), ezpExtension::getInstance( $name )->getLoadingOrder() );
        $this->assertNull( ezpExtension::getInstance( $name )->getInfo() );
    }

    public function testBrokenExtensionXmlGivesNullAndLeavesLibxmlAsItWas()
    {
        $name = $this->extension( 'broken', array( 'extension.xml' => '<extension><unclosed></extension>' ) );
        $before = libxml_use_internal_errors( false );
        $this->assertNull( ezpExtension::getInstance( $name )->getLoadingOrder() );
        $this->assertNull( ezpExtension::getInstance( $name )->getInfo() );
        $this->assertFalse( libxml_use_internal_errors( $before ) );
        $this->assertSame( array(), libxml_get_errors() );
    }

    public function testInfoFromMetadata()
    {
        $name = $this->extension( 'meta', array( 'extension.xml' => '<?xml version="1.0"?><extension name="x"><metadata>'
            . '<name>Name &amp; Co</name><version>1.2.3</version><copyright>(c) K1</copyright><author></author>'
            . '<license>GPL</license><info_url>https://k1.example.invalid/</info_url>'
            . '<software><uses><name>Lib A</name><version>2</version></uses><uses><name>Lib B</name><license>MIT</license></uses></software>'
            . '</metadata></extension>',
            'ezinfo.php' => '<?php class %NAME%Info { static function info() { return array( "Name" => "from ezinfo" ); } }' ) );
        $info = ezpExtension::getInstance( $name )->getInfo();
        $this->assertSame( array(
            'name' => 'Name & Co',
            'version' => '1.2.3',
            'copyright' => '(c) K1',
            'license' => 'GPL',
            'info_url' => 'https://k1.example.invalid/',
            'Includes the following third-party software' => array( 'name' => 'Lib A', 'version' => '2' ),
            'Includes the following third-party software (2)' => array( 'name' => 'Lib B', 'license' => 'MIT' ),
        ), $info );
    }

    public function testMetadataWithoutSoftware()
    {
        $name = $this->extension( 'nosoft', array( 'extension.xml' => '<extension><metadata><name>N</name><software/></metadata></extension>' ) );
        $this->assertSame( array( 'name' => 'N' ), ezpExtension::getInstance( $name )->getInfo() );
    }

    public function testEzInfoWhenExtensionXmlHasNoMetadata()
    {
        $name = $this->extension( 'ezinfo', array( 'extension.xml' => '<extension name="x"><dependencies/></extension>',
            'ezinfo.php' => '<?php class %NAME%Info { static function info() { return array( "Name" => "From ezinfo", "Version" => "6.0.15" ); } }' ) );
        $this->assertSame( array( 'Name' => 'From ezinfo', 'Version' => '6.0.15' ), ezpExtension::getInstance( $name )->getInfo() );
    }

    public function testEmptyMetadataWithoutEzInfoGivesEmptyArray()
    {
        $name = $this->extension( 'empty', array( 'extension.xml' => '<extension><metadata><name></name></metadata></extension>' ) );
        $this->assertSame( array(), ezpExtension::getInstance( $name )->getInfo() );
    }

    public static function brokenEzInfoProvider()
    {
        return array(
            'no class' => array( '<?php // nothing' ),
            'info is not an array' => array( '<?php class %NAME%Info { static function info() { return "text"; } }' ),
            'info throws' => array( '<?php class %NAME%Info { static function info() { throw new RuntimeException( "no" ); } }' ),
            'parse error' => array( '<?php class %NAME%Info { static function info() { return array( ; } }' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('brokenEzInfoProvider')]
    public function testBrokenEzInfoIsSkipped( $code )
    {
        $name = $this->extension( 'bad' . substr( md5( $code ), 0, 6 ), array( 'ezinfo.php' => $code ) );
        $this->assertNull( ezpExtension::getInstance( $name )->getInfo() );
    }
}
