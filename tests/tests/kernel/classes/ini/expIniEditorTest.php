<?php
/**
 * The exp:ini engine: scopes, the setting syntax, every kind of write, comments, toggles, secrets, diff, errors.
 * Works on a fixture installation under var/tmp/ini-tests/ only.
 *
 *  INI-01 — scopes(): every kind (global, default, siteaccess, extension active/inactive, extension siteaccess)
 *  INI-02 — scope(): every spec form, and the errors (unknown siteaccess/extension refused, malformed = usage)
 *  INI-03 — a provider registered in settings/ini.ini [IniCommandSettings] ScopeProviders[] adds its scopes
 *  INI-04 — parseSetting(): the three syntaxes, arrays, hashes, partial forms, errors
 *  INI-05 — set: an existing value changes only its line; a new variable goes at the end of its block;
 *           a new block at the end, before the PHP wrapper's closing line; comments and '##' comments stay
 *  INI-06 — a new file starts like the files in settings/override; new dirs are created
 *  INI-07 — hashes: set a new key, replace a key, remove a key
 *  INI-08 — arrays: add after the variable's last line, add of a present value changes nothing, remove a value,
 *           clearArray
 *  INI-09 — remove a variable (all its lines), the block stays; rem of a missing variable/value/key = NOT_FOUND
 *  INI-10 — refused values: line breaks, NUL, '*' . '/' in PHP files, '##', key '0'; set plain on an array
 *  INI-11 — CRLF files get CRLF lines; a file without a final newline keeps having none
 *  INI-12 — toggle: every pair, case style kept, untoggleable refused, unset = NOT_FOUND
 *  INI-13 — isSecret() and maskValue()
 *  INI-14 — diff(): unified, the same hunks as `diff -u`
 *  INI-15 — save(): dry run writes nothing; nothing to change writes nothing; backup in var/backup/ini/<stamp>/;
 *           a second backup never overwrites the first; atomic (new inode, no temporary file left); mode kept
 *  INI-16 — save(): default scope refused unless allowDefault; create=false on a missing file = NOT_FOUND;
 *           changed on disk since read = WRITE_FAILED
 *  INI-17 — ownership (as root): a file owned by alpha stays alpha's; a new file and new directories get the
 *           owner and group of their parent directory
 *  INI-18 — as the site user, a file's group is kept when the user is a member of it (atomic)
 *  INI-19 — as the site user, a group that cannot be set: the original inode is rewritten, the group stays
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group ini
 */

require_once __DIR__ . '/fixtures/expinienginetestfixtures.php';

class expIniEditorTest extends PHPUnit\Framework\TestCase
{
    protected $root;

    public function setUp(): void
    {
        parent::setUp();
        $this->root = expIniEngineTestFixtures::makeRoot( $this->name() );
    }

    public function tearDown(): void
    {
        expIniEditor::setRoot( null );
        parent::tearDown();
    }

    private function assertThrowsCode( $code, callable $f )
    {
        try
        {
            $f();
        }
        catch ( expIniException $e )
        {
            $this->assertSame( $code, $e->getCode(), $e->getMessage() );
            return $e;
        }
        $this->fail( "Expected expIniException with code $code" );
    }

    private function read( $rel )
    {
        return file_get_contents( $this->root . $rel );
    }

    /** INI-01 */
    public function testScopesOfEveryKind()
    {
        $byName = array();
        foreach ( expIniEditor::scopes() as $s )
            $byName[$s->name()] = $s;
        $this->assertSame( array( 'global', 'default', 'siteaccess:admin', 'siteaccess:eng', 'siteaccess:plain',
                                  'extension:exta', 'extension:extb', 'extension:exta:siteaccess:eng' ), array_keys( $byName ) );
        $this->assertSame( 'global', $byName['global']->kind() );
        $this->assertSame( $this->root . 'settings/override/site.ini.append.php', $byName['global']->path( 'site' ) );
        $this->assertSame( $this->root . 'settings/site.ini', $byName['default']->path( 'site.ini' ) );
        $this->assertFalse( $byName['default']->policyWritable() );
        $this->assertFalse( $byName['default']->writable() );
        $this->assertTrue( $byName['global']->writable() );
        $this->assertSame( 'settings/siteaccess/eng/site.ini.append.php', $byName['siteaccess:eng']->relativePath( 'site' ) );
        $this->assertFalse( $byName['siteaccess:plain']->exists() );
        $this->assertTrue( $byName['extension:exta']->isActive() );
        $this->assertFalse( $byName['extension:extb']->isActive() );
        $this->assertStringContainsString( 'inactive', $byName['extension:extb']->label() );
        $this->assertSame( 'extension-siteaccess', $byName['extension:exta:siteaccess:eng']->kind() );
        $this->assertSame( 'extension/exta/settings/siteaccess/eng/content.ini.append.php',
                           $byName['extension:exta:siteaccess:eng']->relativePath( 'content.ini' ) );
    }

    /** INI-02 */
    public function testScopeSpecs()
    {
        $this->assertSame( 'global', expIniEditor::scope( 'global' )->name() );
        $this->assertSame( 'global', expIniEditor::scope( 'override' )->name() );
        $this->assertSame( 'default', expIniEditor::scope( 'default' )->name() );
        $this->assertSame( 'siteaccess:eng', expIniEditor::scope( 'siteaccess:eng' )->name() );
        $this->assertSame( 'siteaccess:eng', expIniEditor::scope( 'eng' )->name() );
        $this->assertSame( 'extension:extb', expIniEditor::scope( 'extension:extb' )->name() );
        $this->assertSame( 'extension:exta:siteaccess:eng', expIniEditor::scope( 'extension:exta:siteaccess:eng' )->name() );
        // not there yet, but the extension and the siteaccess exist
        $s = expIniEditor::scope( 'extension:exta:siteaccess:admin' );
        $this->assertSame( 'extension/exta/settings/siteaccess/admin', $s->dir() );
        $this->assertFalse( $s->exists() );

        $this->assertThrowsCode( expIniException::REFUSED, function () { expIniEditor::scope( 'siteaccess:nope' ); } );
        $this->assertThrowsCode( expIniException::REFUSED, function () { expIniEditor::scope( 'nope' ); } );
        $this->assertThrowsCode( expIniException::REFUSED, function () { expIniEditor::scope( 'extension:exta:siteaccess:nope' ); } );
        $this->assertThrowsCode( expIniException::USAGE, function () { expIniEditor::scope( 'siteaccess:../x' ); } );
        $this->assertThrowsCode( expIniException::USAGE, function () { expIniEditor::scope( 'a/b' ); } );
        $this->assertThrowsCode( expIniException::USAGE, function () { expIniEditor::scope( '' ); } );
    }

    /** INI-03 */
    public function testRegisteredScopeProvider()
    {
        file_put_contents( $this->root . 'settings/ini.ini',
                           "[IniCommandSettings]\nScopeProviders[]\nScopeProviders[]=expIniTestClusterScopeProvider\nScopeProviders[]=noSuchProviderClass\n" );
        expIniEditor::resetScopes();
        $s = expIniEditor::scope( 'cluster' );
        $this->assertSame( 'cluster', $s->kind() );
        $this->assertSame( 'var/cluster-settings/site.ini.append.php', $s->relativePath( 'site' ) );
        $e = new expIniEditor( $s, 'site' );
        $e->set( 'A', 'B', 'c' );
        $this->assertTrue( $e->save( array( 'backup' => false ) )->written() );
        $this->assertFileExists( $this->root . 'var/cluster-settings/site.ini.append.php' );
        $this->assertContains( 'expIniTestClusterScopeProvider', expIniEditor::providerClasses() );
    }

    /** INI-04 */
    public function testParseSetting()
    {
        $plain = array( 'file' => 'site', 'block' => 'SiteSettings', 'variable' => 'SiteName', 'kind' => 'plain', 'key' => null );
        foreach ( array( 'site.ini/SiteSettings/SiteName', 'site/SiteSettings/SiteName', 'site.ini [SiteSettings] SiteName',
                         'site [SiteSettings] SiteName', 'site.ini[SiteSettings]SiteName', 'site.ini:SiteSettings.SiteName',
                         'site:SiteSettings.SiteName', '  site.ini/SiteSettings/SiteName ' ) as $t )
            $this->assertSame( $plain, expIniEditor::parseSetting( $t ), $t );

        $this->assertSame( array( 'file' => 'site', 'block' => 'ExtensionSettings', 'variable' => 'ActiveExtensions', 'kind' => 'array', 'key' => null ),
                           expIniEditor::parseSetting( 'site.ini/ExtensionSettings/ActiveExtensions[]' ) );
        $this->assertSame( array( 'file' => 'site', 'block' => 'ExtensionSettings', 'variable' => 'ActiveExtensions', 'kind' => 'array', 'key' => null ),
                           expIniEditor::parseSetting( 'site.ini [ExtensionSettings] ActiveExtensions[]' ) );
        $this->assertSame( array( 'file' => 'module', 'block' => 'ModuleSettings', 'variable' => 'ModuleList', 'kind' => 'hash', 'key' => 'my/key' ),
                           expIniEditor::parseSetting( 'module.ini/ModuleSettings/ModuleList[my/key]' ) );
        $this->assertSame( 'hash', expIniEditor::parseSetting( 'module.ini:ModuleSettings.ModuleList[k]' )['kind'] );
        // block names with dots and dashes
        $p = expIniEditor::parseSetting( 'content.ini:License_GPL-2.0-only.Name' );
        $this->assertSame( array( 'License_GPL-2.0-only', 'Name' ), array( $p['block'], $p['variable'] ) );
        $p = expIniEditor::parseSetting( 'content.ini/License_GPL-2.0-only/Name' );
        $this->assertSame( 'License_GPL-2.0-only', $p['block'] );
        // partial forms
        $this->assertSame( array( 'file' => 'site', 'block' => null, 'variable' => null, 'kind' => null, 'key' => null ), expIniEditor::parseSetting( 'site.ini' ) );
        $this->assertSame( 'SiteSettings', expIniEditor::parseSetting( 'site/SiteSettings' )['block'] );
        $this->assertSame( 'SiteSettings', expIniEditor::parseSetting( 'site.ini [SiteSettings]' )['block'] );
        $this->assertSame( 'SiteSettings', expIniEditor::parseSetting( 'site.ini:SiteSettings' )['block'] );
        $this->assertNull( expIniEditor::parseSetting( 'site.ini:SiteSettings' )['variable'] );

        foreach ( array( '', 'site.ini/Block/Bad Name', 'site.ini [Block', '../site.ini/A/B', 'site.ini/A/B=c' ) as $bad )
            $this->assertThrowsCode( expIniException::USAGE, function () use ( $bad ) { expIniEditor::parseSetting( $bad ); } );
    }

    /** INI-05 */
    public function testSetKeepsEverythingElse()
    {
        $e = expIniEngineTestFixtures::editor( 'global' );
        $this->assertSame( 'Exponential', $e->get( 'SiteSettings', 'SiteName' ) );
        $this->assertFalse( $e->set( 'SiteSettings', 'SiteName', 'Exponential' ) );
        $this->assertFalse( $e->hasChanges() );

        $this->assertTrue( $e->set( 'SiteSettings', 'SiteName', 'New name' ) );
        $this->assertTrue( $e->set( 'SiteSettings', 'SiteURL', 'example.org' ) );
        $this->assertTrue( $e->set( 'SiteSettings', 'IndexPage', '/content/view/full/2' ) );
        $this->assertTrue( $e->set( 'NewBlock', 'Probe', '1' ) );
        $this->assertSame( 'New name', $e->get( 'SiteSettings', 'SiteName' ) );
        $this->assertSame( 'example.org ', $e->get( 'SiteSettings', 'SiteURL' ), 'eZINI keeps the space before ##' );
        $e->save( array( 'backup' => false ) );

        $expected = str_replace(
            array( "SiteName=Exponential\n", "SiteURL=example.com ## comment after the value\n",
                   "Servers[replica]=db2\n\n*/ ?>" ),
            array( "SiteName=New name\n", "SiteURL=example.org ## comment after the value\nIndexPage=/content/view/full/2\n",
                   "Servers[replica]=db2\n\n[NewBlock]\nProbe=1\n\n*/ ?>" ),
            expIniEngineTestFixtures::overrideSiteIni() );
        $this->assertSame( $expected, $this->read( 'settings/override/site.ini.append.php' ) );

        // what eZINI reads from it
        $ini = new eZINI( substr( $this->root, strlen( expIniEngineTestFixtures::realRoot() ) ) . 'settings/override/site.ini.append.php', false, false, false, false, true );
        $this->assertSame( 'New name', $ini->variable( 'SiteSettings', 'SiteName' ) );
        $this->assertSame( '1', $ini->variable( 'NewBlock', 'Probe' ) );
        $this->assertSame( array( 'exta', 'ezjscore' ), $ini->variable( 'ExtensionSettings', 'ActiveExtensions' ) );
    }

    /** INI-06 */
    public function testNewFileAndDirectories()
    {
        $e = expIniEngineTestFixtures::editor( 'siteaccess:plain', 'content.ini' );
        $this->assertFalse( $e->fileExists() );
        $this->assertNull( $e->get( 'A', 'B' ) );
        $this->assertSame( '', $e->diff() );
        $e->set( 'VersionManagement', 'DefaultVersionHistoryLimit', '10' );
        $this->assertStringStartsWith( "--- /dev/null\n+++ b/settings/siteaccess/plain/content.ini.append.php\n", $e->diff() );
        $r = $e->save();
        $this->assertTrue( $r->created() );
        $this->assertNull( $r->backup() );
        $this->assertSame( "<?php /* #?ini charset=\"utf-8\"?\n\n[VersionManagement]\nDefaultVersionHistoryLimit=10\n\n*/ ?>",
                           $this->read( 'settings/siteaccess/plain/content.ini.append.php' ) );

        $e = expIniEngineTestFixtures::editor( 'extension:exta:siteaccess:admin' );
        $e->add( 'DesignSettings', 'AdditionalSiteDesignList', 'exta' );
        $e->save();
        $this->assertSame( "<?php /* #?ini charset=\"utf-8\"?\n\n[DesignSettings]\nAdditionalSiteDesignList[]=exta\n\n*/ ?>",
                           $this->read( 'extension/exta/settings/siteaccess/admin/site.ini.append.php' ) );

        // an extension without a settings directory
        $e = expIniEngineTestFixtures::editor( 'extension:extb', 'extb' );
        $e->set( 'ExtbSettings', 'On', 'true' );
        $e->save();
        $this->assertFileExists( $this->root . 'extension/extb/settings/extb.ini.append.php' );
    }

    /** INI-07 */
    public function testHashes()
    {
        $e = expIniEngineTestFixtures::editor( 'global' );
        $this->assertSame( array( 'main' => 'db1', 'replica' => 'db2' ), $e->get( 'DatabaseSettings', 'Servers' ) );
        $this->assertFalse( $e->set( 'DatabaseSettings', 'Servers', 'db1', 'main' ) );
        $this->assertTrue( $e->set( 'DatabaseSettings', 'Servers', 'db9', 'main' ) );
        $this->assertTrue( $e->set( 'DatabaseSettings', 'Servers', 'db3', 'backup' ) );
        $this->assertSame( array( 'main' => 'db9', 'replica' => 'db2', 'backup' => 'db3' ), $e->get( 'DatabaseSettings', 'Servers' ) );
        $this->assertTrue( $e->remove( 'DatabaseSettings', 'Servers', null, 'replica' ) );
        $this->assertSame( array( 'main' => 'db9', 'backup' => 'db3' ), $e->get( 'DatabaseSettings', 'Servers' ) );
        $this->assertStringContainsString( "Password=s3cret\nServers[main]=db9\nServers[backup]=db3\n\n*/ ?>", $e->content() );
        $this->assertThrowsCode( expIniException::NOT_FOUND, function () use ( $e ) { $e->remove( 'DatabaseSettings', 'Servers', null, 'replica' ); } );
        $this->assertThrowsCode( expIniException::USAGE, function () use ( $e ) { $e->set( 'SiteSettings', 'SiteName', 'x', 'k' ); } );
    }

    /** INI-08 */
    public function testArrays()
    {
        $e = expIniEngineTestFixtures::editor( 'global' );
        $this->assertSame( array( 'exta', 'ezjscore' ), $e->get( 'ExtensionSettings', 'ActiveExtensions' ) );
        $this->assertFalse( $e->add( 'ExtensionSettings', 'ActiveExtensions', 'exta' ) );
        $this->assertTrue( $e->add( 'ExtensionSettings', 'ActiveExtensions', 'ezoe' ) );
        $this->assertStringContainsString( "ActiveExtensions[]\nActiveExtensions[]=exta\nActiveExtensions[]=ezjscore\nActiveExtensions[]=ezoe\n\n[DatabaseSettings]", $e->content() );
        $this->assertTrue( $e->remove( 'ExtensionSettings', 'ActiveExtensions', 'ezjscore' ) );
        $this->assertSame( array( 'exta', 'ezoe' ), $e->get( 'ExtensionSettings', 'ActiveExtensions' ) );
        $this->assertThrowsCode( expIniException::NOT_FOUND, function () use ( $e ) { $e->remove( 'ExtensionSettings', 'ActiveExtensions', 'nope' ); } );

        $this->assertTrue( $e->clearArray( 'ExtensionSettings', 'ActiveExtensions' ) );
        $this->assertSame( array(), $e->get( 'ExtensionSettings', 'ActiveExtensions' ) );
        $this->assertFalse( $e->clearArray( 'ExtensionSettings', 'ActiveExtensions' ) );
        $this->assertStringContainsString( "[ExtensionSettings]\nActiveExtensions[]\n\n[DatabaseSettings]", $e->content() );
        $this->assertTrue( $e->add( 'ExtensionSettings', 'ActiveExtensions', 'one' ) );
        $this->assertStringContainsString( "[ExtensionSettings]\nActiveExtensions[]\nActiveExtensions[]=one\n\n", $e->content() );

        // clearArray of a variable not in the file, and add to a new block
        $this->assertTrue( $e->clearArray( 'SiteSettings', 'SiteList' ) );
        $this->assertStringContainsString( "SiteURL=example.com ## comment after the value\nSiteList[]\n", $e->content() );
        $this->assertTrue( $e->add( 'RegionalSettings', 'TranslationExtensions', 'exta' ) );
        $this->assertSame( array( 'exta' ), $e->get( 'RegionalSettings', 'TranslationExtensions' ) );
        $this->assertThrowsCode( expIniException::USAGE, function () use ( $e ) { $e->add( 'SiteSettings', 'SiteName', 'x' ); } );
    }

    /** INI-09 */
    public function testRemoveVariable()
    {
        $e = expIniEngineTestFixtures::editor( 'global' );
        $this->assertTrue( $e->remove( 'ExtensionSettings', 'ActiveExtensions' ) );
        $this->assertNull( $e->get( 'ExtensionSettings', 'ActiveExtensions' ) );
        $this->assertStringContainsString( "# comment about the next block\n[ExtensionSettings]\n\n[DatabaseSettings]", $e->content() );
        $this->assertContains( 'ExtensionSettings', $e->blocks() );
        $this->assertTrue( $e->remove( 'SiteSettings', 'SiteURL' ) );
        $this->assertStringContainsString( "# the name\nSiteName=Exponential\n\n", $e->content() );

        $this->assertThrowsCode( expIniException::NOT_FOUND, function () use ( $e ) { $e->remove( 'SiteSettings', 'Missing' ); } );
        $this->assertThrowsCode( expIniException::NOT_FOUND, function () use ( $e ) { $e->remove( 'NoBlock', 'Missing' ); } );
        $e2 = expIniEngineTestFixtures::editor( 'siteaccess:admin' );
        $this->assertThrowsCode( expIniException::NOT_FOUND, function () use ( $e2 ) { $e2->remove( 'SiteSettings', 'SiteName' ); } );
    }

    /** INI-09b — removeBlock() undoes a set() that created the block, byte for byte */
    public function testRemoveBlock()
    {
        $e = expIniEngineTestFixtures::editor( 'global' );
        $e->set( 'ExpIniSmokeTest', 'Probe', '1' );
        $e->save( array( 'backup' => false ) );
        $e->remove( 'ExpIniSmokeTest', 'Probe' );
        $this->assertSame( array(), $e->variables( 'ExpIniSmokeTest' ) );
        $e->removeBlock( 'ExpIniSmokeTest', true );
        $e->save( array( 'backup' => false ) );
        $this->assertSame( expIniEngineTestFixtures::overrideSiteIni(), $this->read( 'settings/override/site.ini.append.php' ) );

        // a block in the middle, with its comment kept for the next block
        $this->assertTrue( $e->removeBlock( 'SiteSettings' ) );
        $this->assertStringStartsWith( "<?php /* #?ini charset=\"utf-8\"?\n\n# Global overrides\n# comment about the next block\n[ExtensionSettings]", $e->content() );
        $this->assertThrowsCode( expIniException::NOT_FOUND, function () use ( $e ) { $e->removeBlock( 'SiteSettings' ); } );
        $this->assertThrowsCode( expIniException::USAGE, function () use ( $e ) { $e->removeBlock( 'DatabaseSettings', true ); } );

        // no blank line before the closing line, and a plain file without a final newline
        foreach ( array( "<?php /* #?ini charset=\"utf-8\"?\n[A]\nB=1\n*/ ?>", "[A]\nB=1", "[A]\nB=1\n" ) as $n => $text )
        {
            file_put_contents( $this->root . "settings/siteaccess/eng/rb$n.ini.append.php", $text );
            $x = expIniEngineTestFixtures::editor( 'eng', "rb$n" );
            $x->set( 'New', 'V', '1' );
            $x->removeBlock( 'New' );
            $this->assertSame( $text, $x->content(), "case $n" );
        }
    }

    /** INI-18 — scope( 'extension:<new>' ): exists() false; save() refuses it unless createScope */
    public function testNewExtensionScope()
    {
        $s = expIniEditor::scope( 'extension:extnew' );
        $this->assertSame( 'extension:extnew', $s->name() );
        $this->assertSame( 'extension', $s->kind() );
        $this->assertFalse( $s->exists() );
        $this->assertSame( 'extension/extnew/settings/site.ini.append.php', $s->relativePath( 'site' ) );
        $this->assertFalse( expIniEditor::scope( 'extension:extnew:siteaccess:eng' )->exists() );
        $this->assertThrowsCode( expIniException::USAGE, function () { expIniEditor::scope( 'extension:../x' ); } );

        $e = new expIniEditor( $s, 'site' );
        $e->set( 'A', 'B', 'c' );
        $this->assertTrue( $e->save( array( 'dryRun' => true ) )->changed(), 'a dry run is fine' );
        $this->assertThrowsCode( expIniException::REFUSED, function () use ( $e ) { $e->save(); } );
        $this->assertDirectoryDoesNotExist( $this->root . 'extension/extnew' );
        $r = $e->save( array( 'createScope' => true ) );
        $this->assertTrue( $r->created() );
        $this->assertFileExists( $this->root . 'extension/extnew/settings/site.ini.append.php' );
        expIniEditor::resetScopes();
        $this->assertTrue( expIniEditor::scope( 'extension:extnew' )->exists() );
    }

    /** INI-19 — blockLines(), insertBlockLines(), removeBlock() of a whole block, blocks(), isEmpty() */
    public function testBlockLines()
    {
        $e = expIniEngineTestFixtures::editor( 'global' );
        $this->assertSame( array( 'SiteSettings', 'ExtensionSettings', 'DatabaseSettings' ), $e->blocks() );
        $this->assertSame( array( '# the name', 'SiteName=Exponential', 'SiteURL=example.com ## comment after the value', '' ),
                           $e->blockLines( 'SiteSettings' ), 'the comment above the next block stays with it' );
        $this->assertSame( array( 'Password=s3cret', 'Servers[main]=db1', 'Servers[replica]=db2' ), $e->blockLines( 'DatabaseSettings' ) );
        $this->assertNull( $e->blockLines( 'Missing' ) );
        $this->assertFalse( $e->isEmpty() );

        // move SiteSettings into a new file of an extension siteaccess, comments kept
        $lines = $e->blockLines( 'SiteSettings' );
        $t = expIniEngineTestFixtures::editor( 'extension:exta:siteaccess:eng' );
        $this->assertTrue( $t->isEmpty() );
        $this->assertTrue( $t->insertBlockLines( 'SiteSettings', $lines ) );
        $this->assertSame( "<?php /* #?ini charset=\"utf-8\"?\n\n[SiteSettings]\n# the name\nSiteName=Exponential\nSiteURL=example.com ## comment after the value\n\n*/ ?>", $t->content() );
        $this->assertStringContainsString( '+# the name', $t->diff() );
        $t->save();
        $this->assertSame( 'Exponential', $t->get( 'SiteSettings', 'SiteName' ) );
        $e->removeBlock( 'SiteSettings' );
        $this->assertSame( array( 'ExtensionSettings', 'DatabaseSettings' ), $e->blocks() );

        // append to an existing block: after its last setting
        $this->assertTrue( $e->insertBlockLines( 'DatabaseSettings', array( '# moved', 'Port=3306', '' ) ) );
        $this->assertStringContainsString( "Servers[replica]=db2\n# moved\nPort=3306\n\n*/ ?>", $e->content() );
        $this->assertFalse( $e->insertBlockLines( 'DatabaseSettings', array( '', '' ) ) );

        // insert a non-empty block, then remove it: byte-identical
        $o = expIniEngineTestFixtures::editor( 'eng' );
        $before = $o->content();
        $o->insertBlockLines( 'Moved', array( '# about', 'A=1', 'B[]=x', '', '# trailing comment' ) );
        $o->save( array( 'backup' => false ) );
        $o->removeBlock( 'Moved' );
        $o->save( array( 'backup' => false ) );
        $this->assertSame( $before, $this->read( 'settings/siteaccess/eng/site.ini.append.php' ) );

        foreach ( array( array( '[Other]' ), array( "a\nb" ), array( 'X=*/' ) ) as $bad )
            $this->assertThrowsCode( expIniException::USAGE, function () use ( $o, $bad ) { $o->insertBlockLines( 'Moved', $bad ); } );
        // a file of nothing but the wrapper and comments
        file_put_contents( $this->root . 'settings/siteaccess/eng/empty.ini.append.php', "<?php /* #?ini charset=\"utf-8\"?\n# only a comment\n\n*/ ?>" );
        $this->assertTrue( expIniEngineTestFixtures::editor( 'eng', 'empty' )->isEmpty() );
    }

    /** INI-10 */
    public function testRefusedValues()
    {
        $e = expIniEngineTestFixtures::editor( 'global' );
        foreach ( array( "a\nb", "a\rb", "a\0b", 'a */ b', 'a ## b' ) as $bad )
            $this->assertThrowsCode( expIniException::USAGE, function () use ( $e, $bad ) { $e->set( 'SiteSettings', 'SiteName', $bad ); } );
        $this->assertThrowsCode( expIniException::USAGE, function () use ( $e ) { $e->set( 'SiteSettings', 'Bad Name', 'x' ); } );
        $this->assertThrowsCode( expIniException::USAGE, function () use ( $e ) { $e->set( 'Bad]Block', 'X', 'x' ); } );
        $this->assertThrowsCode( expIniException::USAGE, function () use ( $e ) { $e->set( 'DatabaseSettings', 'Servers', 'x', '0' ); } );
        $this->assertThrowsCode( expIniException::USAGE, function () use ( $e ) { $e->set( 'DatabaseSettings', 'Servers', 'x', 'a]b' ); } );
        $this->assertThrowsCode( expIniException::USAGE, function () use ( $e ) { $e->set( 'ExtensionSettings', 'ActiveExtensions', 'x' ); } );
        $this->assertThrowsCode( expIniException::USAGE, function () { new expIniEditor( expIniEditor::scope( 'global' ), '../site' ); } );
        $this->assertFalse( $e->hasChanges() );
        // '*/' is fine in a plain .ini
        $d = expIniEngineTestFixtures::editor( 'default' );
        $this->assertTrue( $d->set( 'SiteSettings', 'Pattern', '*/x' ) );
    }

    /** INI-11 */
    public function testLineEnds()
    {
        $crlf = "<?php /* #?ini charset=\"utf-8\"?\r\n\r\n[A]\r\nB=1\r\n\r\n*/ ?>\r\n";
        file_put_contents( $this->root . 'settings/siteaccess/eng/crlf.ini.append.php', $crlf );
        $e = expIniEngineTestFixtures::editor( 'eng', 'crlf' );
        $this->assertSame( $crlf, $e->content() );
        $e->set( 'A', 'C', '2' );
        $e->set( 'A', 'B', '3' );
        $e->set( 'D', 'E', '4' );
        $e->save( array( 'backup' => false ) );
        $this->assertSame( "<?php /* #?ini charset=\"utf-8\"?\r\n\r\n[A]\r\nB=3\r\nC=2\r\n\r\n[D]\r\nE=4\r\n\r\n*/ ?>\r\n",
                           $this->read( 'settings/siteaccess/eng/crlf.ini.append.php' ) );

        $noNl = "[A]\nB=1";
        file_put_contents( $this->root . 'settings/nonl.ini', $noNl );
        $e = expIniEngineTestFixtures::editor( 'default', 'nonl' );
        $e->set( 'A', 'C', '2' );
        $this->assertSame( "[A]\nB=1\nC=2", $e->content() );
        $e->remove( 'A', 'C' );
        $this->assertSame( $noNl, $e->content() );
        $e->remove( 'A', 'B' );
        $this->assertSame( "[A]", $e->content() );
    }

    /** INI-12 */
    public function testToggle()
    {
        $pairs = array( 'enabled' => 'disabled', 'disabled' => 'enabled', 'true' => 'false', 'false' => 'true',
                        'yes' => 'no', 'no' => 'yes', 'on' => 'off', 'off' => 'on', '1' => '0', '0' => '1',
                        'Enabled' => 'Disabled', 'TRUE' => 'FALSE', 'Yes' => 'No', 'ON' => 'OFF', 'Off' => 'On' );
        foreach ( $pairs as $from => $to )
            $this->assertSame( $to, expIniEditor::toggleValue( $from ), $from );
        foreach ( array( 'maybe', '', '2', 'enabledx' ) as $bad )
            $this->assertThrowsCode( expIniException::REFUSED, function () use ( $bad ) { expIniEditor::toggleValue( $bad ); } );
        $this->assertThrowsCode( expIniException::REFUSED, function () { expIniEditor::toggleValue( array( 'a' ) ); } );

        $e = expIniEngineTestFixtures::editor( 'extension:exta' );
        $this->assertSame( 'disabled', $e->toggle( 'ExtAs', 'Feature' ) );
        $this->assertSame( 'disabled', $e->get( 'ExtAs', 'Feature' ) );
        $this->assertSame( 'enabled', $e->toggle( 'ExtAs', 'Feature' ) );
        $this->assertFalse( $e->hasChanges() );
        $this->assertThrowsCode( expIniException::NOT_FOUND, function () use ( $e ) { $e->toggle( 'ExtAs', 'Missing' ); } );
        $g = expIniEngineTestFixtures::editor( 'global' );
        // not in this file: the effective value (here from settings/site.ini) is flipped and written here
        $this->assertSame( 'enabled', $g->toggle( 'DebugSettings', 'DebugOutput' ) );
        $this->assertSame( 'enabled', $g->get( 'DebugSettings', 'DebugOutput' ) );
        $this->assertSame( 'disabled', expIniEditor::effectiveValue( 'site', 'DebugSettings', 'DebugOutput' ) );
        $this->assertSame( 'false', expIniEditor::effectiveValue( 'site', 'SiteAccessSettings', 'RequireUserLogin', 'eng' ) );
        $this->assertSame( 'Exponential', expIniEditor::effectiveValue( 'site', 'SiteSettings', 'SiteName', 'eng' ), 'settings/override wins' );
        $this->assertSame( array( 'exta', 'ezjscore' ), expIniEditor::effectiveValue( 'site', 'ExtensionSettings', 'ActiveExtensions' ) );
        $this->assertThrowsCode( expIniException::REFUSED, function () use ( $g ) { $g->toggle( 'SiteSettings', 'SiteName' ); } );
        $this->assertThrowsCode( expIniException::REFUSED, function () use ( $g ) { $g->toggle( 'ExtensionSettings', 'ActiveExtensions' ); } );
    }

    /** INI-13 */
    public function testSecrets()
    {
        foreach ( array( 'Password', 'DatabasePassword', 'password', 'SMTPPasswd', 'Passphrase', 'ClientSecret', 'SECRET',
                         'ApiToken', 'AccessToken', 'Salt', 'HashSalt', 'Credentials', 'PrivateKey', 'ApiKey', 'apikey',
                         'LicenseKey', 'license_key', 'SECRET_KEY', 'Key', 'key', 'RecaptchaSiteKey' ) as $v )
            $this->assertTrue( expIniEditor::isSecret( $v ), $v );
        foreach ( array( 'KeyField', 'Keywords', 'KeywordList', 'SortKey', 'CacheKey', 'PrimaryKey', 'ForeignKey',
                         'MonkeyList', 'SiteName', 'User', 'Server', 'HotKey', 'KeyList' ) as $v )
            $this->assertFalse( expIniEditor::isSecret( $v ), $v );
        $this->assertSame( '********', expIniEditor::maskValue( 's3cret' ) );
        $this->assertSame( '', expIniEditor::maskValue( '' ) );
        $this->assertNull( expIniEditor::maskValue( null ) );
        $this->assertSame( array( 'main' => '********', 'x' => '' ), expIniEditor::maskValue( array( 'main' => 'a', 'x' => '' ) ) );
    }

    /** INI-14 */
    public function testDiffMatchesDiffU()
    {
        $e = expIniEngineTestFixtures::editor( 'global' );
        $e->set( 'SiteSettings', 'SiteName', 'Other' );
        $e->add( 'ExtensionSettings', 'ActiveExtensions', 'ezoe' );
        $e->remove( 'DatabaseSettings', 'Servers', null, 'replica' );
        $e->set( 'Tail', 'X', '1' );
        $diff = $e->diff();
        $this->assertStringStartsWith( "--- a/settings/override/site.ini.append.php\n+++ b/settings/override/site.ini.append.php\n@@ ", $diff );
        $this->assertStringContainsString( "-SiteName=Exponential\n+SiteName=Other\n", $diff );

        if ( !is_executable( '/usr/bin/diff' ) )
            $this->markTestSkipped( 'no diff(1)' );
        $old = $this->root . 'var/old.txt';
        $new = $this->root . 'var/new.txt';
        file_put_contents( $old, $e->originalContent() );
        file_put_contents( $new, $e->content() );
        $out = array();
        exec( '/usr/bin/diff -u ' . escapeshellarg( $old ) . ' ' . escapeshellarg( $new ), $out );
        // some diff(1) builds print an empty context line as '' rather than ' ': compare without trailing blanks
        $norm = function ( array $lines ) { return implode( "\n", array_map( 'rtrim', $lines ) ); };
        $this->assertSame( $norm( array_slice( $out, 2 ) ) . "\n", $norm( array_slice( explode( "\n", $diff ), 2 ) ) );

        // and a file that gains a final newline
        $this->assertSame( "--- a\n+++ b\n@@ -1 +1 @@\n-x\n\\ No newline at end of file\n+x\n", expIniEditor::unifiedDiff( "x", "x\n" ) );
        $this->assertSame( '', expIniEditor::unifiedDiff( "same", "same" ) );
    }

    /** INI-15 */
    public function testSaveDryRunBackupAtomic()
    {
        $rel = 'settings/override/site.ini.append.php';
        $path = $this->root . $rel;
        chmod( $path, 0640 );
        $before = file_get_contents( $path );
        $inode = fileinode( $path );

        $e = expIniEngineTestFixtures::editor( 'global' );
        $r = $e->save();
        $this->assertFalse( $r->changed() );
        $this->assertFalse( $r->written() );

        $e->set( 'SiteSettings', 'SiteName', 'Dry' );
        $r = $e->save( array( 'dryRun' => true ) );
        $this->assertTrue( $r->changed() );
        $this->assertTrue( $r->dryRun() );
        $this->assertFalse( $r->written() );
        $this->assertNull( $r->backup() );
        $this->assertStringContainsString( '+SiteName=Dry', $r->diff() );
        $this->assertSame( $before, file_get_contents( $path ) );
        $this->assertFileDoesNotExist( $this->root . 'var/backup' );

        $r = $e->save();
        $this->assertTrue( $r->written() );
        $this->assertFalse( $r->created() );
        $this->assertMatchesRegularExpression( '#/var/backup/ini/\d{8}-\d{6}/settings/override/site\.ini\.append\.php$#', $r->backup() );
        $this->assertStringStartsWith( $this->root . 'var/backup/ini/', $r->backup() );
        $this->assertSame( $before, file_get_contents( $r->backup() ) );
        $this->assertSame( 0600, fileperms( $r->backup() ) & 0777 );
        clearstatcache();
        $this->assertNotSame( $inode, fileinode( $path ), 'written by rename' );
        $this->assertSame( 0640, fileperms( $path ) & 07777, 'mode kept' );
        $this->assertSame( array( 'site.ini.append.php' ), array_values( array_diff( scandir( dirname( $path ) ), array( '.', '..' ) ) ), 'no temporary file left' );

        // a second write in the same second backs up into another directory
        $e->set( 'SiteSettings', 'SiteName', 'Again' );
        $r2 = $e->save();
        $this->assertNotSame( $r->backup(), $r2->backup() );
        $this->assertSame( $before, file_get_contents( $r->backup() ) );
        $this->assertStringContainsString( 'SiteName=Dry', file_get_contents( $r2->backup() ) );

        // revert by removing what was added: the file is byte-identical to the first backup
        $e->set( 'SiteSettings', 'SiteName', 'Exponential' );
        $e->save( array( 'backup' => false ) );
        $this->assertSame( $before, file_get_contents( $path ) );
    }

    /** INI-16 */
    public function testSaveRefusals()
    {
        $d = expIniEngineTestFixtures::editor( 'default' );
        $d->set( 'SiteSettings', 'SiteName', 'Changed default' );
        $this->assertThrowsCode( expIniException::REFUSED, function () use ( $d ) { $d->save(); } );
        $this->assertSame( expIniEngineTestFixtures::defaultSiteIni(), $this->read( 'settings/site.ini' ) );
        $r = $d->save( array( 'allowDefault' => true, 'backup' => false ) );
        $this->assertTrue( $r->written() );
        $this->assertStringContainsString( "SiteName=Changed default\n", $this->read( 'settings/site.ini' ) );

        $n = expIniEngineTestFixtures::editor( 'siteaccess:admin' );
        $n->set( 'A', 'B', 'c' );
        $this->assertThrowsCode( expIniException::NOT_FOUND, function () use ( $n ) { $n->save( array( 'create' => false ) ); } );

        $g = expIniEngineTestFixtures::editor( 'global' );
        $g->set( 'SiteSettings', 'SiteName', 'Mine' );
        file_put_contents( $this->root . 'settings/override/site.ini.append.php', "changed behind our back" );
        $this->assertThrowsCode( expIniException::WRITE_FAILED, function () use ( $g ) { $g->save(); } );
        $this->assertSame( "changed behind our back", $this->read( 'settings/override/site.ini.append.php' ) );
    }

    /** INI-17b — as root, the backup tree under a var/ owned by alpha:psacln is alpha:psacln, dirs 0700, files 0600 */
    public function testBackupOwnershipAsRoot()
    {
        if ( !expIniEditor::isRoot() )
            $this->markTestSkipped( 'ownership is only changed when running as root' );
        $alpha = function_exists( 'posix_getpwnam' ) ? posix_getpwnam( 'alpha' ) : false;
        $psacln = function_exists( 'posix_getgrnam' ) ? posix_getgrnam( 'psacln' ) : false;
        if ( !$alpha || !$psacln )
            $this->markTestSkipped( 'no user alpha / group psacln here' );
        $uid = $alpha['uid'];
        $gid = $psacln['gid'];
        chown( $this->root . 'var', $uid );
        chgrp( $this->root . 'var', $gid );

        $e = expIniEngineTestFixtures::editor( 'global' );
        $e->set( 'SiteSettings', 'SiteName', 'Backed up' );
        $r = $e->save();
        $backup = $r->backup();
        clearstatcache();
        $this->assertSame( array( $uid, $gid, 0600 ), array( fileowner( $backup ), filegroup( $backup ), fileperms( $backup ) & 0777 ) );
        $stampDir = dirname( $backup, 3 );
        foreach ( array( $this->root . 'var/backup', $this->root . 'var/backup/ini', $stampDir, dirname( $backup ) ) as $d )
            $this->assertSame( array( $uid, $gid, 0700 ), array( fileowner( $d ), filegroup( $d ), fileperms( $d ) & 0777 ), $d );

        // an earlier root-owned backup root is handed back on the next backup
        chown( $this->root . 'var/backup/ini', 0 );
        chgrp( $this->root . 'var/backup/ini', 0 );
        $e->set( 'SiteSettings', 'SiteName', 'Again' );
        $e->save();
        clearstatcache();
        $this->assertSame( array( $uid, $gid ), array( fileowner( $this->root . 'var/backup/ini' ), filegroup( $this->root . 'var/backup/ini' ) ) );
    }

    /** INI-17 */
    public function testOwnershipAsRoot()
    {
        if ( !expIniEditor::isRoot() )
            $this->markTestSkipped( 'ownership is only changed when running as root' );
        $alpha = function_exists( 'posix_getpwnam' ) ? posix_getpwnam( 'alpha' ) : false;
        $psacln = function_exists( 'posix_getgrnam' ) ? posix_getgrnam( 'psacln' ) : false;
        if ( !$alpha || !$psacln )
            $this->markTestSkipped( 'no user alpha / group psacln here' );
        $uid = $alpha['uid'];
        $gid = $psacln['gid'];

        $path = $this->root . 'settings/override/site.ini.append.php';
        chown( $path, $uid );
        chgrp( $path, $gid );
        chmod( $path, 0664 );
        $e = expIniEngineTestFixtures::editor( 'global' );
        $e->set( 'SiteSettings', 'SiteName', 'Owned' );
        $r = $e->save();
        $this->assertSame( array(), $r->warnings() );
        clearstatcache();
        $this->assertSame( array( $uid, $gid, 0664 ), array( fileowner( $path ), filegroup( $path ), fileperms( $path ) & 07777 ) );

        // a new file in a dir owned by alpha:psacln, and new directories below one
        foreach ( array( 'settings/siteaccess/admin', 'extension/exta/settings/siteaccess' ) as $d )
        {
            chown( $this->root . $d, $uid );
            chgrp( $this->root . $d, $gid );
        }
        $n = expIniEngineTestFixtures::editor( 'siteaccess:admin', 'menu' );
        $n->set( 'MenuSettings', 'AvailableMenuArray', 'TopOnly' );
        $n->save();
        $new = $this->root . 'settings/siteaccess/admin/menu.ini.append.php';
        clearstatcache();
        $this->assertSame( array( $uid, $gid ), array( fileowner( $new ), filegroup( $new ) ) );

        $x = expIniEngineTestFixtures::editor( 'extension:exta:siteaccess:admin' );
        $x->set( 'A', 'B', 'c' );
        $x->save();
        $dir = $this->root . 'extension/exta/settings/siteaccess/admin';
        clearstatcache();
        $this->assertSame( array( $uid, $gid ), array( fileowner( $dir ), filegroup( $dir ) ) );
        $this->assertSame( array( $uid, $gid ), array( fileowner( "$dir/site.ini.append.php" ), filegroup( "$dir/site.ini.append.php" ) ) );
    }

    /**
     * Runs a save as alpha in a child process (started by root); returns array( output, exit code ).
     */
    private function saveAsAlpha( $extraGroup, $value )
    {
        $groups = posix_getgrnam( 'psacln' )['gid'] . ( $extraGroup === '-' ? '' : ',' . posix_getgrnam( $extraGroup )['gid'] );
        $cmd = array( 'setpriv', '--reuid=' . posix_getpwnam( 'alpha' )['uid'], '--regid=' . posix_getgrnam( 'psacln' )['gid'], '--groups=' . $groups,
                      PHP_BINARY, __DIR__ . '/fixtures/expinisaveasuser.php', rtrim( $this->root, '/' ) . '/', $value );
        $p = proc_open( $cmd, array( 1 => array( 'pipe', 'w' ), 2 => array( 'pipe', 'w' ) ), $pipes );
        $out = stream_get_contents( $pipes[1] ) . stream_get_contents( $pipes[2] );
        return array( $out, proc_close( $p ) );
    }

    private function prepareAlphaFile( &$path )
    {
        if ( !expIniEditor::isRoot() )
            $this->markTestSkipped( 'needs root to start a process as alpha' );
        $alpha = function_exists( 'posix_getpwnam' ) ? posix_getpwnam( 'alpha' ) : false;
        $psaserv = function_exists( 'posix_getgrnam' ) ? posix_getgrnam( 'psaserv' ) : false;
        if ( !$alpha || !$psaserv || !posix_getgrnam( 'psacln' ) )
            $this->markTestSkipped( 'no user alpha / groups psaserv, psacln here' );
        $path = $this->root . 'settings/override/site.ini.append.php';
        foreach ( array( 'settings/override', 'var' ) as $d )
            chown( $this->root . $d, $alpha['uid'] );
        chown( $path, $alpha['uid'] );
        chgrp( $path, $psaserv['gid'] );
        chmod( $path, 0664 );
        return array( $alpha['uid'], $psaserv['gid'] );
    }

    /** INI-18: alpha belongs to the file's group: the temporary file gets the group, the rename keeps it */
    public function testGroupKeptWhenWriterIsAMemberOfIt()
    {
        list( $uid, $gid ) = $this->prepareAlphaFile( $path );
        clearstatcache();
        $before = fileinode( $path );
        list( $out, $code ) = $this->saveAsAlpha( 'psaserv', 'ByMember' );
        $this->assertSame( 0, $code, $out );
        $this->assertStringContainsString( 'OK', $out );
        clearstatcache();
        $this->assertSame( array( $uid, $gid, 0664 ), array( fileowner( $path ), filegroup( $path ), fileperms( $path ) & 07777 ), $out );
        $this->assertNotSame( $before, fileinode( $path ), 'written atomically' );
        $this->assertStringContainsString( 'ByMember', file_get_contents( $path ) );
    }

    /** INI-19: alpha cannot set the file's group: the original inode is rewritten, so group and mode stay */
    public function testGroupKeptByInPlaceFallbackWhenItCannotBeSet()
    {
        list( $uid, $gid ) = $this->prepareAlphaFile( $path );
        clearstatcache();
        $before = fileinode( $path );
        list( $out, $code ) = $this->saveAsAlpha( '-', 'InPlace' );
        $this->assertSame( 0, $code, $out );
        $this->assertStringContainsString( 'OK', $out );
        $this->assertStringContainsString( 'in place', $out );
        clearstatcache();
        $this->assertSame( array( $uid, $gid, 0664 ), array( fileowner( $path ), filegroup( $path ), fileperms( $path ) & 07777 ), $out );
        $this->assertSame( $before, fileinode( $path ), 'same inode' );
        $this->assertStringContainsString( 'InPlace', file_get_contents( $path ) );
        $this->assertSame( array(), glob( dirname( $path ) . '/.*.tmp' ) );
    }
}

/**
 * A scope provider registered through settings/ini.ini in INI-03.
 */
class expIniTestClusterScopeProvider implements expIniScopeProvider
{
    public function scopes( $root )
    {
        return array( new expIniScope( 'cluster', 'cluster', 'var/cluster-settings', $root, 'Cluster-wide settings' ) );
    }
}
