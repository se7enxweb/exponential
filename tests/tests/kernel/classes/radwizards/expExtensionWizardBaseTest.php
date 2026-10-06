<?php
/**
 * The shared part of the extension wizards of Setup > RAD (expExtensionWizard): names, text and ini values on their
 * way into generated files (nothing typed may end the php comment an ini file or a doc comment is), the licences
 * and the notices written for them, the packaging files (ezinfo.php, extension.xml, composer.json, LICENSE),
 * writing an extension and building its archive.
 *
 * No database. Writing goes to extension/<a name no extension has> and the archive to the cache directory; both
 * are removed again.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 */

require_once __DIR__ . '/expRadWizardTestHelper.php';

class expExtensionWizardBaseTestWizard extends expExtensionWizard
{
    public static $files = array( 'a/b/c.txt' => 'one', 'top.txt' => 'two' );

    public static function parts()
    {
        return array( 'first' => array( 'label' => 'First', 'description' => '', 'default' => true ),
                      'second' => array( 'label' => 'Second', 'description' => '', 'default' => false ) );
    }

    public static function settings( array $input )
    {
        return array( 'name' => self::safeName( $input['name'] ?? '' ), 'title' => self::text( $input['title'] ?? '' ),
                      'summary' => self::text( $input['summary'] ?? '' ), 'author' => self::text( $input['author'] ?? '' ),
                      'vendor' => self::safeName( $input['vendor'] ?? 'acme' ), 'version' => self::text( $input['version'] ?? '1.0.0' ),
                      'licence' => self::licence_id( $input['licence'] ?? '' ), 'parts' => $input['parts'] ?? array() );
    }

    public static function problems( array $settings )
    {
        $problems = array();
        if ( $settings['name'] === '' )
            $problems[] = 'no name';
        if ( $settings['name'] !== '' && is_dir( self::extensionPath( $settings['name'] ) ) )
            $problems['exists'] = 'exists';
        return $problems + self::licenceProblems( $settings );
    }

    public static function files( array $settings )
    {
        return self::$files;
    }

    public static function call( $method, array $settings )
    {
        return static::$method( $settings );
    }

    public static function gnu( $identifier )
    {
        return self::gnuLicence( $identifier );
    }

    public static function quoted( $value )
    {
        return self::phpString( $value );
    }
}

class expExtensionWizardBaseTest extends PHPUnit\Framework\TestCase
{
    private $created = array();
    private $injected;

    public static function setUpBeforeClass(): void
    {
        expRadWizardTestHelper::boot();
    }

    protected function setUp(): void
    {
        expRadWizardTestHelper::boot();
        // the archive is built in the cache directory: a scratch one under var/tmp
        $cache = expRadWizardTestHelper::scratch( 'wizard-cache' );
        $this->created[] = $cache;
        $this->injected = expRadWizardTestHelper::injectSiteIni( array( 'FileSettings' => array( 'CacheDir' => $cache ) ) );
    }

    protected function tearDown(): void
    {
        expRadWizardTestHelper::restoreInjected( $this->injected );
        foreach ( $this->created as $path )
            expRadWizardTestHelper::removeTree( $path );
    }

    private function settings( array $input = array() )
    {
        return expExtensionWizardBaseTestWizard::settings( $input + array( 'name' => 'k1e_base', 'title' => 'K1e Base', 'author' => 'Ada Example' ) );
    }

    // ---------------------------------------------------------------- values on their way into files

    public static function commentTextProvider()
    {
        return array(
            'plain' => array( 'A title', 'A title' ),
            'end of comment' => array( 'x */ phpinfo(); /*', 'x * phpinfo(); /*' ),
            'several stars' => array( 'x ***/ y', 'x * y' ),
            'star and two slashes' => array( 'x *// phpinfo();', null ),
            'stars and slashes' => array( '**//*//', null ),
            'open tag' => array( '<?php echo 1; ?>', '< ?php echo 1; ? >' ),
            'short open tag' => array( '<?= 1 ?>', '< ?= 1 ? >' ),
            'array' => array( array( 'x' ), '' ),
            'number' => array( 12, '12' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('commentTextProvider')]
    public function testCommentTextNeverEndsAComment( $value, $expected )
    {
        $text = expExtensionWizard::commentText( $value );
        if ( $expected !== null )
            $this->assertSame( $expected, $text );
        $this->assertStringNotContainsString( '*/', $text );
        $this->assertStringNotContainsString( '<?', $text );
        $this->assertStringNotContainsString( '?>', $text );
    }

    public function testTextStripsControlCharactersAndCuts()
    {
        $this->assertSame( 'ab', expExtensionWizard::text( "a\x00\x07b" ) );
        $this->assertSame( "a\tb\nc", expExtensionWizard::text( "  a\tb\nc  " ) );
        $this->assertSame( 'äöü', expExtensionWizard::text( 'äöüß', 3 ) );
        $this->assertSame( '', expExtensionWizard::text( array() ) );
        $this->assertSame( '', expExtensionWizard::text( null ) );
        $this->assertSame( 'x * y', expExtensionWizard::text( 'x */ y' ) );
        $this->assertStringNotContainsString( '*/', expExtensionWizard::text( 'x *// y' ) );
    }

    public function testIniValueIsOneLineWithoutCommentEnd()
    {
        $this->assertSame( 'a b c', expExtensionWizard::iniValue( "a\r\nb\nc" ) );
        $this->assertSame( 'Title * [ExtensionSettings]', expExtensionWizard::iniValue( "Title */\n[ExtensionSettings]" ) );
        $this->assertSame( 'abc', expExtensionWizard::iniValue( 'abcdef', 3 ) );
        $this->assertStringNotContainsString( '*/', expExtensionWizard::iniValue( 'a *//b' ) );
    }

    public static function safeNameProvider()
    {
        return array(
            'plain' => array( 'my_extension', 'my_extension' ),
            'capitals and spaces' => array( ' My Extension ', 'my_extension' ),
            'punctuation' => array( 'my-ext.v2', 'my_ext_v2' ),
            'leading digit' => array( '2fast', '' ),
            'too short' => array( 'ab', '' ),
            'just long enough' => array( 'abc', 'abc' ),
            'too long' => array( str_repeat( 'a', 42 ), '' ),
            'path' => array( '../../etc', 'etc' ),
            'empty' => array( '', '' ),
            'array' => array( array( 'abc' ), '' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('safeNameProvider')]
    public function testSafeName( $value, $expected )
    {
        $this->assertSame( $expected, expExtensionWizard::safeName( $value ) );
        $this->assertSame( $expected, expExtensionWizard::safeName( $value, true ) );
    }

    public function testDirectoriesOfFilesDeepestLast()
    {
        $this->assertSame( array( 'a', 'a/b', 'a/b/c', 'd' ),
                           expExtensionWizard::directories( array( 'a/b/c/x.php' => '', 'a/y.php' => '', 'd/z' => '', 'top' => '' ) ) );
        $this->assertSame( array(), expExtensionWizard::directories( array( 'README.md' => '' ) ) );
    }

    public function testPathsAreInsideTheInstallation()
    {
        $root = expExtensionWizard::installationRoot();
        $this->assertFileExists( $root . '/kernel/setup/expextensionwizard.php' );
        $this->assertSame( $root . '/extension/abc', expExtensionWizard::extensionPath( 'abc' ) );
        $this->assertIsBool( expExtensionWizard::canWrite() );
    }

    // ---------------------------------------------------------------- licences

    public static function licenceIdProvider()
    {
        return array(
            'empty is the default' => array( '', 'GPL-2.0-or-later' ),
            'configured' => array( 'MIT', 'MIT' ),
            'alias' => array( 'GPL', 'GPL-2.0-or-later' ),
            'old plain GPL-2.0' => array( 'GPL-2.0', 'GPL-2.0-or-later' ),
            'old GPL-3.0' => array( 'GPL-3.0', 'GPL-3.0-only' ),
            'old GPL-3.0+' => array( 'GPL-3.0+', 'GPL-3.0-or-later' ),
            'old proprietary' => array( 'proprietary', 'LicenseRef-Proprietary' ),
            'spaces around' => array( ' MIT ', 'MIT' ),
            'unknown kept for refusal' => array( 'WTFPL', 'WTFPL' ),
            'unknown cut at 64' => array( str_repeat( 'x', 80 ), str_repeat( 'x', 64 ) ),
            'array' => array( array( 'MIT' ), 'GPL-2.0-or-later' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('licenceIdProvider')]
    public function testLicenceId( $posted, $expected )
    {
        $this->assertSame( $expected, expExtensionWizard::licence_id( $posted ) );
    }

    public function testLicenceProblems()
    {
        $this->assertSame( array(), expExtensionWizard::licenceProblems( array( 'licence' => 'MIT' ) ) );
        $this->assertArrayHasKey( 'licence', expExtensionWizard::licenceProblems( array( 'licence' => 'WTFPL' ) ) );
        $this->assertStringContainsString( 'WTFPL', expExtensionWizard::licenceProblems( array( 'licence' => 'WTFPL' ) )['licence'] );
        $this->assertArrayHasKey( 'licence', expExtensionWizard::licenceProblems( array( 'licence' => '' ) ) );
        $this->assertArrayHasKey( 'licence', expExtensionWizard::licenceProblems( array() ) );
        $this->assertNotEmpty( expExtensionWizard::licences() );
    }

    public static function gnuProvider()
    {
        return array(
            array( 'GPL-2.0-or-later', array( 'name' => 'GNU General Public License', 'short' => 'GPL', 'version' => '2', 'later' => true ) ),
            array( 'LGPL-2.1-only', array( 'name' => 'GNU Lesser General Public License', 'short' => 'LGPL', 'version' => '2.1', 'later' => false ) ),
            array( 'AGPL-3.0-only', array( 'name' => 'GNU Affero General Public License', 'short' => 'AGPL', 'version' => '3', 'later' => false ) ),
            array( 'GFDL-1.3-or-later', array( 'name' => 'GNU Free Documentation License', 'short' => 'GFDL', 'version' => '1.3', 'later' => true ) ),
            array( 'MIT', false ), array( 'GPL-2.0', false ), array( null, false ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('gnuProvider')]
    public function testGnuLicence( $identifier, $expected )
    {
        $this->assertSame( $expected, expExtensionWizardBaseTestWizard::gnu( $identifier ) );
    }

    public static function noticeProvider()
    {
        return array(
            'MIT' => array( 'MIT', 'Released under the MIT licence', 'MIT License', 'MIT. See [LICENSE]' ),
            'proprietary' => array( 'LicenseRef-Proprietary', 'All rights reserved.', 'All rights reserved.', 'Proprietary - all rights reserved.' ),
            'GPL 2 or later' => array( 'GPL-2.0-or-later', 'either version 2 of the License, or (at your option) any later', 'You should have received a copy', 'version 2 or, at your option, any later version' ),
            'GPL 3 only' => array( 'GPL-3.0-only', 'version 3 of the License only.', 'version 3 of the License only.', 'version 3 only' ),
            'LGPL 2.1 or later' => array( 'LGPL-2.1-or-later', 'GNU Lesser General Public License as published by the Free', 'either version 2.1 of the License', 'version 2.1 or, at your option, any later version' ),
            'GFDL' => array( 'GFDL-1.3-only', 'GNU Free Documentation License', 'Version 1.3 only', 'version 1.3 only' ),
            'Creative Commons' => array( 'CC-BY-4.0', 'SPDX-License-Identifier: CC-BY-4.0', 'SPDX-License-Identifier: CC-BY-4.0', '(`CC-BY-4.0`).' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('noticeProvider')]
    public function testLicenceNoticeFileAndLine( $licence, $inNotice, $inFile, $inLine )
    {
        $settings = $this->settings( array( 'licence' => $licence ) );
        $notice = expExtensionWizardBaseTestWizard::call( 'licenceNotice', $settings );
        $this->assertStringContainsString( $inNotice, $notice );
        $this->assertStringContainsString( 'Ada Example', $notice );
        $this->assertStringContainsString( date( 'Y' ), $notice );
        $this->assertStringContainsString( $inFile, expExtensionWizardBaseTestWizard::call( 'licence', $settings ) );
        $this->assertStringContainsString( $inLine, expExtensionWizardBaseTestWizard::call( 'licenceLine', $settings ) );
        $this->assertStringNotContainsString( '*/', $notice );
    }

    public function testHolderIsTheTitleWithoutAnAuthor()
    {
        $settings = $this->settings( array( 'author' => '', 'licence' => 'MIT' ) );
        $this->assertStringContainsString( 'K1e Base', expExtensionWizardBaseTestWizard::call( 'licence', $settings ) );
    }

    // ---------------------------------------------------------------- packaging files

    public function testEzinfoIsValidPhpWithTheValuesQuoted()
    {
        $settings = $this->settings( array( 'title' => "O'Brien \\ Co", 'author' => "Ann's", 'version' => '2.0' ) );
        $php = expExtensionWizardBaseTestWizard::call( 'ezinfo', $settings );
        expRadWizardTestHelper::assertPhpParses( $this, $php, 'ezinfo.php' );
        $this->assertStringContainsString( 'class k1e_baseInfo', $php );
        $this->assertStringContainsString( "'Name'      => 'O\\'Brien \\\\ Co'", $php );
        $this->assertStringContainsString( "const SOFTWARE_VERSION = '2.0';", $php );
        $this->assertSame( "a\\'b\\\\c", expExtensionWizardBaseTestWizard::quoted( "a'b\\c" ) );
    }

    public function testExtensionXmlIsWellFormedWithMarkupInValues()
    {
        $settings = $this->settings( array( 'summary' => 'A <b>bold</b> & "quoted" summary', 'author' => 'X & Y' ) );
        $xml = expExtensionWizardBaseTestWizard::call( 'extensionXml', $settings );
        $dom = new DOMDocument();
        $this->assertTrue( $dom->loadXML( $xml ) );
        $this->assertSame( 'A <b>bold</b> & "quoted" summary', $dom->getElementsByTagName( 'summary' )->item( 0 )->textContent );
        $this->assertSame( 'k1e_base', $dom->getElementsByTagName( 'name' )->item( 0 )->textContent );
        $this->assertSame( 'X & Y', $dom->getElementsByTagName( 'maintainer' )->item( 0 )->textContent );
    }

    public function testComposerJson()
    {
        $data = json_decode( expExtensionWizardBaseTestWizard::call( 'composerJson', $this->settings( array( 'licence' => 'MIT' ) ) ), true );
        $this->assertSame( 'acme/k1e-base', $data['name'] );
        $this->assertSame( 'ezpublish-legacy-extension', $data['type'] );
        $this->assertSame( 'MIT', $data['license'] );
        $this->assertSame( array( 'installer-name' => 'k1e_base' ), $data['extra'] );
        $this->assertSame( array( array( 'name' => 'Ada Example' ) ), $data['authors'] );
        $data = json_decode( expExtensionWizardBaseTestWizard::call( 'composerJson', $this->settings( array( 'author' => '' ) ) ), true );
        $this->assertArrayNotHasKey( 'authors', $data );
    }

    public function testGitignoreAndIniHeader()
    {
        $settings = $this->settings();
        $this->assertStringContainsString( '/vendor/', expExtensionWizardBaseTestWizard::call( 'gitignore', $settings ) );
        $this->assertSame( 'extension wizard', expExtensionWizard::wizardName() );
        $this->assertSame( array( 'first' ), expExtensionWizardBaseTestWizard::chosenParts( array( 'parts' => array( 'first' => true, 'second' => false, 'other' => true ) ) ) );
        $this->assertSame( array(), expExtensionWizardBaseTestWizard::chosenParts( array() ) );
    }

    // ---------------------------------------------------------------- writing and archiving

    public function testWriteRefusesWithProblems()
    {
        $result = expExtensionWizardBaseTestWizard::write( $this->settings( array( 'name' => '' ) ) );
        $this->assertFalse( $result['ok'] );
        $this->assertSame( 'no name', $result['message'] );
        $this->assertSame( array(), $result['written'] );

        $result = expExtensionWizardBaseTestWizard::write( $this->settings( array( 'licence' => 'WTFPL' ) ) );
        $this->assertFalse( $result['ok'] );
        $this->assertStringContainsString( 'WTFPL', $result['message'] );
    }

    public function testWriteCreatesTheFilesAndRefusesAnExistingExtension()
    {
        $name = 'k1etest' . getmypid();
        $settings = $this->settings( array( 'name' => $name ) );
        $target = expExtensionWizard::extensionPath( $name );
        $this->assertDirectoryDoesNotExist( $target );
        if ( !expExtensionWizard::canWrite() )
            $this->markTestSkipped( 'extension/ is not writable here' );
        $this->created[] = $target;

        $result = expExtensionWizardBaseTestWizard::write( $settings );
        $this->assertTrue( $result['ok'], $result['message'] );
        $this->assertSame( array( 'a/b/c.txt', 'top.txt' ), $result['written'] );
        $this->assertSame( 'one', file_get_contents( $target . '/a/b/c.txt' ) );
        $this->assertSame( '0644', substr( sprintf( '%o', fileperms( $target . '/top.txt' ) ), -4 ) );

        $again = expExtensionWizardBaseTestWizard::write( $settings );
        $this->assertFalse( $again['ok'] );
        $this->assertSame( 'exists', $again['message'] );

        // the archive leaves the "exists" problems out
        if ( class_exists( 'ZipArchive' ) )
        {
            $archive = expExtensionWizardBaseTestWizard::archive( $settings );
            $this->assertTrue( $archive['ok'], $archive['message'] );
            $this->assertStringStartsWith( expRadWizardTestHelper::root() . '/var/tmp/', $archive['path'] );
            $this->assertSame( $name . '.zip', $archive['filename'] );
            $zip = new ZipArchive();
            $this->assertTrue( $zip->open( $archive['path'] ) );
            $this->assertSame( 'one', $zip->getFromName( $name . '/a/b/c.txt' ) );
            $this->assertSame( 'two', $zip->getFromName( $name . '/top.txt' ) );
            $zip->close();
        }
    }

    public function testArchiveRefusesOtherProblems()
    {
        if ( !class_exists( 'ZipArchive' ) )
            $this->markTestSkipped( 'no zip' );
        $result = expExtensionWizardBaseTestWizard::archive( $this->settings( array( 'name' => '' ) ) );
        $this->assertFalse( $result['ok'] );
        $this->assertSame( 'no name', $result['message'] );
    }
}
