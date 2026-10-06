<?php
/**
 * What the settings pages (settings/view, settings/edit) work out before they show or write anything. No database,
 * no settings files: the chains are built from layers in memory.
 *
 *  SP-01 - Origins: the path of a settings file names its place in the chain, as eZINI::findSettingPlacement() does
 *  SP-02 - A plain value: the last file that sets it wins, every earlier one is overridden; changed from default
 *  SP-03 - An array: a reset drops what came before, each element names the file it comes from, hash keys replace
 *  SP-04 - An array appended to over three files keeps every file; a setting with no default counts as changed
 *  SP-05 - Summary figures and the comparison of two siteaccesses
 *  SP-06 - Secrets: names the rule masks and the ones it does not; the configured list; exp:ini's rule always applies
 *  SP-07 - Secrets: masked values say set or empty; last characters only when configured and long enough
 *  SP-08 - A password inside a URL, DSN or connection string is masked in any setting
 *  SP-09 - A secret's value is never found by a search; block, name and other values are
 *  SP-10 - Files, siteaccesses, blocks and names: only listed ones; no traversal, no line breaks, no comment end
 *  SP-11 - Where the edit form may write, and which chain files "Remove selected" may change
 *  SP-12 - The array field of the edit form; restart rules; rows with a search, the changed filter and pending values
 *  SP-13 - The old template variable "settings" keeps its shape, with the true origin of every array element
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expSettingsPageTest extends PHPUnit\Framework\TestCase
{
    /** One line of a file, as expIniWriter::entries() gives it */
    private static function e( $type, $block, $var = null, $value = null, $key = null )
    {
        return array( 'type' => $type, 'block' => $block, 'var' => $var, 'value' => $value, 'key' => $key );
    }

    /** A chain of site.ini for siteaccess "admin": default, an extension, the siteaccess, the override */
    private static function siteChain()
    {
        return new expSettingsChain( array(
            array( 'path' => 'settings/site.ini', 'entries' => array(
                self::e( 'block', 'SiteSettings' ),
                self::e( 'plain', 'SiteSettings', 'SiteName', 'Default name' ),
                self::e( 'plain', 'SiteSettings', 'Timeout', '30' ),
                self::e( 'block', 'SiteAccessSettings' ),
                self::e( 'reset', 'SiteAccessSettings', 'AvailableSiteAccessList' ),
                self::e( 'append', 'SiteAccessSettings', 'AvailableSiteAccessList', 'site' ),
                self::e( 'append', 'SiteAccessSettings', 'AvailableSiteAccessList', 'admin' ),
                self::e( 'block', 'DatabaseSettings' ),
                self::e( 'plain', 'DatabaseSettings', 'Password', '' ),
                self::e( 'block', 'RegionalSettings' ),
                self::e( 'hash', 'RegionalSettings', 'Map', 'a', 'one' ),
                self::e( 'hash', 'RegionalSettings', 'Map', 'b', 'two' ),
            ) ),
            array( 'path' => 'extension/ezoe/settings/site.ini.append.php', 'entries' => array(
                self::e( 'block', 'SiteAccessSettings' ),
                self::e( 'append', 'SiteAccessSettings', 'AvailableSiteAccessList', 'ezoe-sa' ),
                self::e( 'block', 'RegionalSettings' ),
                self::e( 'hash', 'RegionalSettings', 'Map', 'B', 'two' ),
            ) ),
            array( 'path' => 'settings/siteaccess/admin/site.ini.append.php', 'entries' => array(
                self::e( 'block', 'SiteSettings' ),
                self::e( 'plain', 'SiteSettings', 'SiteName', 'Admin name' ),
                self::e( 'plain', 'SiteSettings', 'Timeout', '30' ),
            ) ),
            array( 'path' => 'settings/override/site.ini.append.php', 'entries' => array(
                self::e( 'comment', '' ),
                self::e( 'block', 'SiteSettings' ),
                self::e( 'plain', 'SiteSettings', 'SiteName', 'Override name' ),
                self::e( 'block', 'SiteAccessSettings' ),
                self::e( 'reset', 'SiteAccessSettings', 'AvailableSiteAccessList' ),
                self::e( 'append', 'SiteAccessSettings', 'AvailableSiteAccessList', 'site' ),
                self::e( 'append', 'SiteAccessSettings', 'AvailableSiteAccessList', 'editor' ),
                self::e( 'block', 'DatabaseSettings' ),
                self::e( 'plain', 'DatabaseSettings', 'Password', 'correct-horse-battery' ),
                self::e( 'plain', 'DatabaseSettings', 'Server', 'localhost' ),
            ) ),
        ) );
    }

    /** SP-01 */
    public function testPlacementOfPaths()
    {
        $p = expSettingsChain::placementOf( 'settings/site.ini' );
        $this->assertSame( array( 'default', 'default' ), array( $p['kind'], $p['legacy'] ) );
        $p = expSettingsChain::placementOf( 'settings/override/site.ini.append.php' );
        $this->assertSame( array( 'override', 'override' ), array( $p['kind'], $p['legacy'] ) );
        $p = expSettingsChain::placementOf( 'settings/siteaccess/admin/site.ini.append.php' );
        $this->assertSame( array( 'siteaccess', 'siteaccess', 'admin' ), array( $p['kind'], $p['legacy'], $p['siteaccess'] ) );
        $p = expSettingsChain::placementOf( 'extension/ezoe/settings/site.ini.append.php' );
        $this->assertSame( array( 'extension', 'extension:ezoe', 'ezoe' ), array( $p['kind'], $p['legacy'], $p['extension'] ) );
        $p = expSettingsChain::placementOf( 'extension/ezoe/settings/siteaccess/admin/site.ini.append.php' );
        $this->assertSame( array( 'extension-siteaccess', 'ext-siteaccess:ezoe', 'ezoe', 'admin' ),
                           array( $p['kind'], $p['legacy'], $p['extension'], $p['siteaccess'] ) );
        $p = expSettingsChain::placementOf( 'extension/multi/settings/group1/site.ini.append.php' );
        $this->assertSame( array( 'extension-dir', 'ext-siteaccess-group1:multi' ), array( $p['kind'], $p['legacy'] ) );
        $this->assertSame( 'other', expSettingsChain::placementOf( 'var/x/site.ini' )['kind'] );
        $this->assertSame( 'settings/override/site.ini.append.php',
                           expSettingsChain::relativePath( '/srv/www/settings/override/site.ini.append.php', '/srv/www/' ) );
        $this->assertSame( 'settings/site.ini', expSettingsChain::relativePath( './settings/site.ini', '/nowhere/' ) );
    }

    /** SP-02 */
    public function testPlainValueWinsLast()
    {
        $chain = self::siteChain();
        $s = $chain->setting( 'SiteSettings', 'SiteName' );
        $this->assertSame( 'Override name', $s['value'] );
        $this->assertSame( 'settings/override/site.ini.append.php', $s['winner'] );
        $this->assertSame( 'override', $s['winnerPlacement']['kind'] );
        $this->assertSame( array( 'overridden', 'overridden', 'wins' ), array_column( $s['steps'], 'status' ) );
        $this->assertSame( array( 'settings/site.ini', 'settings/siteaccess/admin/site.ini.append.php' ), $s['overridden'] );
        $this->assertTrue( $s['changed'] );
        $this->assertSame( 'Default name', $s['default'] );
        $this->assertSame( 'string', $s['type'] );

        // set again to the default value: not changed, the siteaccess file still wins
        $t = $chain->setting( 'SiteSettings', 'Timeout' );
        $this->assertFalse( $t['changed'] );
        $this->assertSame( 'siteaccess', $t['winnerPlacement']['kind'] );
        $this->assertSame( 'numeric', $t['type'] );
        $this->assertNull( $chain->setting( 'SiteSettings', 'Nothing' ) );
    }

    /** SP-03 */
    public function testArrayResetAndElementOrigins()
    {
        $chain = self::siteChain();
        $s = $chain->setting( 'SiteAccessSettings', 'AvailableSiteAccessList' );
        $this->assertSame( array( 'site', 'editor' ), $s['value'] );
        $this->assertSame( 'list', $s['kind'] );
        // the override's reset dropped the default's and the extension's elements
        $this->assertSame( array( 'overridden', 'overridden', 'adds' ), array_column( $s['steps'], 'status' ) );
        $this->assertSame( array( 'settings/site.ini', 'extension/ezoe/settings/site.ini.append.php' ), $s['overridden'] );
        $this->assertSame( 'settings/override/site.ini.append.php', $s['winner'] );
        $this->assertSame( array( 'override', 'override' ), array( $s['elements'][0]['placement']['kind'], $s['elements'][1]['placement']['kind'] ) );
        $this->assertTrue( $s['steps'][2]['resets'] );
        $this->assertSame( array( 'reset', 'append', 'append' ), array_column( $s['steps'][2]['ops'], 'op' ) );
        $this->assertTrue( $s['changed'] );

        // a hash: the extension replaced key "two", key "one" still comes from the default
        $m = $chain->setting( 'RegionalSettings', 'Map' );
        $this->assertSame( array( 'one' => 'a', 'two' => 'B' ), $m['value'] );
        $this->assertSame( 'hash', $m['kind'] );
        $origins = array();
        foreach ( $m['elements'] as $el )
            $origins[$el['key']] = $el['placement']['kind'];
        $this->assertSame( array( 'one' => 'default', 'two' => 'extension' ), $origins );
        $this->assertSame( array( 'adds', 'adds' ), array_column( $m['steps'], 'status' ) );
        $this->assertSame( array( 'one' => 'a', 'two' => 'b' ), $m['default'] );
    }

    /** SP-04 */
    public function testAppendsKeepEveryFileAndAddedSettings()
    {
        $chain = new expSettingsChain( array(
            array( 'path' => 'settings/design.ini', 'entries' => array(
                self::e( 'append', 'StylesheetSettings', 'CSSFileList', 'core.css' ) ) ),
            array( 'path' => 'extension/a/settings/design.ini.append.php', 'entries' => array(
                self::e( 'append', 'StylesheetSettings', 'CSSFileList', 'a.css' ) ) ),
            array( 'path' => 'settings/override/design.ini.append.php', 'entries' => array(
                self::e( 'append', 'StylesheetSettings', 'CSSFileList', 'o.css' ),
                self::e( 'plain', 'Extra', 'Only', 'here' ) ) ),
        ) );
        $s = $chain->setting( 'StylesheetSettings', 'CSSFileList' );
        $this->assertSame( array( 'core.css', 'a.css', 'o.css' ), $s['value'] );
        $this->assertSame( array( 'adds', 'adds', 'adds' ), array_column( $s['steps'], 'status' ) );
        $this->assertSame( 'settings/design.ini', $s['winner'] );
        $this->assertSame( array( 'settings/design.ini', 'extension/a/settings/design.ini.append.php', 'settings/override/design.ini.append.php' ),
                           array_column( $s['elements'], 'path' ) );
        $only = $chain->setting( 'Extra', 'Only' );
        $this->assertFalse( $only['inDefault'] );
        $this->assertTrue( $only['changed'] );
        $this->assertNull( $only['default'] );
        $this->assertSame( array( 'core.css', 'x' ), expSettingsChain::valueOfOps( array(
            array( 'op' => 'append', 'key' => null, 'value' => 'core.css' ), array( 'op' => 'append', 'key' => null, 'value' => 'x' ) ) ) );
    }

    /** SP-05 */
    public function testSummaryAndCompare()
    {
        $chain = self::siteChain();
        $sum = $chain->summary();
        $this->assertSame( 4, $sum['blocks'] );
        $this->assertSame( 6, $sum['settings'] );
        $this->assertSame( 2, $sum['arrays'] );
        $this->assertSame( 1, $sum['added'] );   // Server: in no default
        $this->assertSame( 5, $sum['changed'] ); // SiteName, AvailableSiteAccessList, Password, Map, Server
        $this->assertSame( 4, $sum['files'] );
        $this->assertSame( array( 'settings/site.ini', 'extension/ezoe/settings/site.ini.append.php',
                                  'settings/siteaccess/admin/site.ini.append.php', 'settings/override/site.ini.append.php' ), $chain->paths() );

        $other = new expSettingsChain( array(
            array( 'path' => 'settings/site.ini', 'entries' => array(
                self::e( 'plain', 'SiteSettings', 'SiteName', 'Override name' ),
                self::e( 'plain', 'SiteSettings', 'Timeout', '60' ),
                self::e( 'plain', 'Only', 'InB', 'b' ) ) ),
        ) );
        $diff = expSettingsChain::compare( $chain, $other );
        $keys = array();
        foreach ( $diff as $d )
            $keys[] = $d['block'] . '/' . $d['name'] . ':' . $d['in'];
        $this->assertContains( 'SiteSettings/Timeout:both', $keys );
        $this->assertNotContains( 'SiteSettings/SiteName:both', $keys );
        $this->assertContains( 'DatabaseSettings/Password:a', $keys );
        $this->assertContains( 'Only/InB:b', $keys );
    }

    /** SP-06 */
    public function testSecretNames()
    {
        $rule = new expSettingsSecretRule();
        foreach ( array( 'Password', 'TransportPassword', 'ClientSecret', 'AccessToken', 'Salt', 'ApiKey', 'LicenseKey',
                         'license_key', 'Key', 'PrivateKey', 'Credentials', 'DSN', 'DatabaseDsn', 'Password[]' ) as $name )
            $this->assertTrue( $rule->isSecretName( $name ), $name );
        foreach ( array( 'User', 'Server', 'SiteName', 'KeyField', 'KeywordList', 'Monkey', 'SortKey', 'CacheKey', 'PrimaryKey', '' ) as $name )
            $this->assertFalse( $rule->isSecretName( $name ), $name );

        // a configured list adds names; exp:ini's own rule cannot be switched off by UnmaskedNameList
        $custom = new expSettingsSecretRule( array( 'Pin', 'signing*' ), array( 'Password', 'PinCode' ) );
        $this->assertTrue( $custom->isSecretName( 'Pin' ) );
        $this->assertFalse( $custom->isSecretName( 'PinCode' ) );
        $this->assertTrue( $custom->isSecretName( 'signingSeed' ) );
        $this->assertTrue( $custom->isSecretName( 'Password' ) );
        $this->assertFalse( $custom->isSecretName( 'Seed' ) );
    }

    /** SP-07 */
    public function testMaskValue()
    {
        $rule = new expSettingsSecretRule();
        $this->assertSame( array( 'state' => 'set', 'text' => expSettingsSecretRule::MASK, 'count' => 1, 'set' => 1 ), $rule->maskValue( 'hunter2-long-enough' ) );
        $this->assertSame( 'empty', $rule->maskValue( '' )['state'] );
        $this->assertSame( 'empty', $rule->maskValue( null )['state'] );
        $array = $rule->maskValue( array( 'a', '', 'b' ) );
        $this->assertSame( array( 'array', 3, 2 ), array( $array['state'], $array['count'], $array['set'] ) );
        $this->assertSame( expSettingsSecretRule::MASK, $rule->displayValue( 'Password', 'abc' ) );
        $this->assertSame( '', $rule->displayValue( 'Password', '' ) );
        $this->assertSame( array( 'k' => expSettingsSecretRule::MASK, 0 => '' ), $rule->displayValue( 'ApiKey', array( 'k' => 'v', 0 => '' ) ) );

        $reveal = new expSettingsSecretRule( null, null, 2 );
        $this->assertSame( expSettingsSecretRule::MASK . 'yz', $reveal->maskValue( 'abcdefghxyz' )['text'] );
        // too short to give away two characters (fewer than 4 x 2)
        $this->assertSame( expSettingsSecretRule::MASK, $reveal->maskValue( 'abcxyz' )['text'] );
        // never more than 4 characters
        $this->assertSame( expSettingsSecretRule::MASK . 'xxab', ( new expSettingsSecretRule( null, null, 9 ) )->maskValue( str_repeat( 'x', 14 ) . 'ab' )['text'] );
    }

    /** SP-08 */
    public function testInlineSecrets()
    {
        $m = expSettingsSecretRule::MASK;
        $this->assertSame( "mysql://root:$m@db.example/xa", expSettingsSecretRule::maskInline( 'mysql://root:s3cr3t@db.example/xa' ) );
        $this->assertSame( "smtp://:$m@mail:25", expSettingsSecretRule::maskInline( 'smtp://:pw@mail:25' ) );
        $this->assertSame( "user:$m@host:3306/db", expSettingsSecretRule::maskInline( 'user:pw@host:3306/db' ) );
        $this->assertSame( "Server=db;Password=$m;Port=1", expSettingsSecretRule::maskInline( 'Server=db;Password=x1;Port=1' ) );
        $this->assertSame( "host=a pwd=$m", expSettingsSecretRule::maskInline( 'host=a pwd=zz' ) );
        foreach ( array( 'https://example.com/path', 'info@example.com', 'mysql', 'a=b;c=d', 'design:node/view/full.tpl' ) as $plain )
            $this->assertSame( $plain, expSettingsSecretRule::maskInline( $plain ), $plain );
        $this->assertTrue( expSettingsSecretRule::hasInlineSecret( 'pgsql://u:p@h/d' ) );
        $this->assertFalse( expSettingsSecretRule::hasInlineSecret( 'pgsql://h/d' ) );
        $rule = new expSettingsSecretRule();
        $this->assertSame( array( "ftp://u:$m@h", 'plain' ), $rule->displayValue( 'Servers', array( 'ftp://u:p@h', 'plain' ) ) );
    }

    /** SP-09 */
    public function testSearchNeverMatchesSecretValues()
    {
        $rule = new expSettingsSecretRule();
        $this->assertTrue( $rule->matchesSearch( 'database', 'DatabaseSettings', 'Password', 'x' ) );
        $this->assertTrue( $rule->matchesSearch( 'pass', 'DatabaseSettings', 'Password', 'x' ) );
        $this->assertFalse( $rule->matchesSearch( 'horse', 'DatabaseSettings', 'Password', 'correct-horse' ) );
        $this->assertFalse( $rule->matchesSearch( 's3cr', 'X', 'Url', 'mysql://u:s3cr3t@h/d' ) );
        $this->assertTrue( $rule->matchesSearch( 'mysql', 'X', 'Url', 'mysql://u:s3cr3t@h/d' ) );
        $this->assertTrue( $rule->matchesSearch( 'EDITOR', 'SiteAccessSettings', 'List', array( 'site', 'editor' ) ) );
        $this->assertTrue( $rule->matchesSearch( 'two', 'R', 'Map', array( 'two' => 'b' ) ) );
        $this->assertFalse( $rule->matchesSearch( 'nothing', 'R', 'Map', array( 'two' => 'b' ) ) );
        $this->assertTrue( $rule->matchesSearch( '  ', 'R', 'Map', 'x' ) );

        $hits = expSettingsPage::search( 'site.ini', self::siteChain(), $rule, 'horse' );
        $this->assertSame( array(), $hits );
        $hits = expSettingsPage::search( 'site.ini', self::siteChain(), $rule, 'password' );
        $this->assertCount( 1, $hits );
        $this->assertSame( expSettingsSecretRule::MASK, $hits[0]['text'] );
        $this->assertSame( 'exp-set-DatabaseSettings-Password', $hits[0]['anchor'] );
        $this->assertCount( 2, expSettingsPage::search( 'site.ini', self::siteChain(), $rule, 'o', 2 ) );
    }

    /** SP-10 */
    public function testRequestNamesAreChecked()
    {
        $known = array( 'site.ini', 'content.ini' );
        $this->assertSame( 'site.ini', expSettingsTarget::iniFile( 'site.ini', $known ) );
        foreach ( array( '../site.ini', '/etc/site.ini', 'site.ini/../x.ini', "site.ini\0", 'design.ini', 'site', array( 'site.ini' ), null, '.ini' ) as $bad )
            $this->assertNull( expSettingsTarget::iniFile( $bad, $known ), var_export( $bad, true ) );
        $sas = array( 'site', 'admin' );
        $this->assertSame( 'admin', expSettingsTarget::siteAccess( 'admin', $sas ) );
        foreach ( array( '../admin', 'admin/', 'editor', '', null, array( 'admin' ) ) as $bad )
            $this->assertNull( expSettingsTarget::siteAccess( $bad, $sas ), var_export( $bad, true ) );

        $this->assertTrue( expSettingsTarget::isValidBlock( 'SiteSettings' ) );
        $this->assertTrue( expSettingsTarget::isValidBlock( 'Tool_toolbar_node bookmarks' ) );
        foreach ( array( '', ' Lead', "A\nB", "A\rB", 'A]B', '[A', "A\0", 'A*/B', 'A##B', str_repeat( 'x', 201 ), 5 ) as $bad )
            $this->assertFalse( expSettingsTarget::isValidBlock( $bad ), var_export( $bad, true ) );
        $this->assertTrue( expSettingsTarget::isValidName( 'Class_identifier' ) );
        $this->assertTrue( expSettingsTarget::isValidName( 'Some-Name@x' ) );
        foreach ( array( '', 'A B', 'A=B', "A\n", 'A[]', 'A/*', 'Ä' ) as $bad )
            $this->assertFalse( expSettingsTarget::isValidName( $bad ), var_export( $bad, true ) );
        $this->assertTrue( expSettingsTarget::isValidArrayKey( 'one two' ) );
        foreach ( array( '', 'a]', "a\nb", 'a*/' ) as $bad )
            $this->assertFalse( expSettingsTarget::isValidArrayKey( $bad ) );
    }

    /** SP-11 */
    public function testWriteDirectoriesAndRemovableFiles()
    {
        $ext = array( 'ezoe', 'ezjscore' );
        $this->assertSame( 'settings/siteaccess/admin', expSettingsTarget::writeDirectory( 'siteaccess', 'admin', $ext ) );
        $this->assertSame( 'settings/override', expSettingsTarget::writeDirectory( 'override', 'admin', $ext ) );
        $this->assertSame( 'extension/ezoe/settings', expSettingsTarget::writeDirectory( 'ezoe', 'admin', $ext ) );
        foreach ( array( '../../var', 'extension:ezoe', 'default', 'notactive', '', null, '/etc' ) as $bad )
            $this->assertNull( expSettingsTarget::writeDirectory( $bad, 'admin', $ext ), var_export( $bad, true ) );
        $this->assertNull( expSettingsTarget::writeDirectory( 'siteaccess', '../x', $ext ) );

        $ok = array( 'settings/override/site.ini.append.php', 'settings/siteaccess/admin/site.ini.append.php',
                     'settings/override/site.ini.append', 'extension/ezoe/settings/site.ini.append.php',
                     'extension/ezoe/settings/siteaccess/admin/site.ini.append.php' );
        foreach ( $ok as $path )
            $this->assertTrue( expSettingsTarget::isWritableChainFile( $path, 'site.ini', 'admin', $ext ), $path );
        $bad = array( 'settings/site.ini', 'settings/override/content.ini.append.php', 'settings/siteaccess/site/site.ini.append.php',
                      'settings/override/../../x/site.ini.append.php', '/settings/override/site.ini.append.php',
                      'extension/other/settings/site.ini.append.php', 'settings/override/site.ini.php' );
        foreach ( $bad as $path )
            $this->assertFalse( expSettingsTarget::isWritableChainFile( $path, 'site.ini', 'admin', $ext ), $path );

        // "Remove selected" takes a setting out of the installation's own settings only
        $chain = self::siteChain();
        $this->assertSame( 'settings/override/site.ini.append.php', expSettingsPage::removeFrom( $chain->setting( 'SiteSettings', 'SiteName' ) ) );
        $this->assertSame( 'settings/siteaccess/admin/site.ini.append.php', expSettingsPage::removeFrom( $chain->setting( 'SiteSettings', 'Timeout' ) ) );
        $this->assertNull( expSettingsPage::removeFrom( $chain->setting( 'RegionalSettings', 'Map' ) ) );
    }

    /** SP-12 */
    public function testArrayTextRestartAndRows()
    {
        $p = expSettingsTarget::parseArrayText( "\n=a\n[k]=v\nplain\n=\n" );
        $this->assertSame( array( 0 => null, 1 => 'a', 'k' => 'v', 2 => 'plain' ), $p['values'] );
        $this->assertSame( array(), $p['invalid'] );
        $p = expSettingsTarget::parseArrayText( "=x=y\r\n[a]b]=c" );
        $this->assertSame( array( 'x=y' ), $p['values'] );
        $this->assertSame( array( '[a]b]=c' ), $p['invalid'] );

        $list = array( 'velocity.ini', 'site.ini/DatabaseSettings', 'site.ini/ExtensionSettings/ActiveExtensions' );
        $this->assertTrue( expSettingsPage::restartNeeded( 'velocity.ini', 'Any', 'Thing', $list ) );
        $this->assertTrue( expSettingsPage::restartNeeded( 'site.ini', 'DatabaseSettings', 'Server', $list ) );
        $this->assertTrue( expSettingsPage::restartNeeded( 'site.ini', 'ExtensionSettings', 'ActiveExtensions', $list ) );
        $this->assertFalse( expSettingsPage::restartNeeded( 'site.ini', 'ExtensionSettings', 'ExtensionDirectory', $list ) );
        $this->assertFalse( expSettingsPage::restartNeeded( 'site.ini', 'SiteSettings', 'SiteName', $list ) );
        $this->assertFalse( expSettingsPage::restartNeeded( 'content.ini', 'DatabaseSettings', 'X', $list ) );

        $rule = new expSettingsSecretRule();
        $chain = self::siteChain();
        $runtime = array( 'SiteSettings' => array( 'SiteName' => 'Admin name', 'Timeout' => '30' ),
                          'DatabaseSettings' => array( 'Password' => 'old-password', 'Server' => 'localhost' ),
                          'SiteAccessSettings' => array( 'AvailableSiteAccessList' => array( 'site', 'editor' ) ),
                          'RegionalSettings' => array( 'Map' => array( 'one' => 'a', 'two' => 'B' ) ) );
        $all = expSettingsPage::rows( $chain, $rule, array( 'runtime' => $runtime, 'file' => 'site.ini', 'restart' => $list ) );
        $this->assertSame( 6, $all['shown'] );
        $this->assertSame( 2, $all['pending'] );
        $this->assertSame( 1, $all['secrets'] );
        $byName = array();
        foreach ( $all['blocks'] as $b )
            foreach ( $b['settings'] as $r )
                $byName[$r['name']] = $r;
        $this->assertSame( array( 'DatabaseSettings', 'RegionalSettings', 'SiteAccessSettings', 'SiteSettings' ), array_column( $all['blocks'], 'name' ) );
        $this->assertTrue( $byName['Password']['secret'] );
        $this->assertSame( expSettingsSecretRule::MASK, $byName['Password']['text'] );
        $this->assertSame( expSettingsSecretRule::MASK, $byName['Password']['runtime_text'] );
        $this->assertTrue( $byName['Password']['restart'] );
        $this->assertSame( '', $byName['Password']['default_text'] );
        $this->assertSame( 'Admin name', $byName['SiteName']['runtime_text'] );
        $this->assertFalse( $byName['Timeout']['pending'] );
        foreach ( $byName['Password']['steps'] as $step )
            foreach ( $step['lines'] as $line )
                $this->assertNotSame( 'correct-horse-battery', $line['text'] );
        $this->assertSame( 'siteaccess', $byName['AvailableSiteAccessList']['edit_placement'] );
        $this->assertSame( 'override', $byName['SiteName']['edit_placement'] );
        $this->assertSame( 2, $byName['AvailableSiteAccessList']['count'] );

        $changed = expSettingsPage::rows( $chain, $rule, array( 'changed' => true ) );
        $this->assertSame( 5, $changed['shown'] );
        $found = expSettingsPage::rows( $chain, $rule, array( 'query' => 'editor' ) );
        $this->assertSame( 1, $found['shown'] );
        $this->assertSame( 'SiteAccessSettings', $found['blocks'][0]['name'] );
        $this->assertSame( 0, expSettingsPage::rows( $chain, $rule, array( 'query' => 'battery' ) )['shown'] );
        $readOnly = expSettingsPage::rows( $chain, $rule, array( 'readOnly' => function ( $b, $n ) { return $b === 'SiteSettings'; } ) );
        $this->assertFalse( $readOnly['blocks'][3]['settings'][0]['editable'] );
        $this->assertFalse( $readOnly['blocks'][3]['settings'][0]['removable'] );
        $this->assertSame( 'exp-set-A-B-C', expSettingsPage::anchor( 'A B/C' ) );
    }

    /** SP-13 */
    public function testLegacySettingsVariable()
    {
        $rule = new expSettingsSecretRule();
        $legacy = expSettingsPage::legacySettings( self::siteChain(), $rule );
        $this->assertSame( array( 'DatabaseSettings', 'RegionalSettings', 'SiteAccessSettings', 'SiteSettings' ), array_keys( $legacy ) );
        $this->assertSame( 2, $legacy['DatabaseSettings']['count'] );
        $this->assertSame( expSettingsSecretRule::MASK, $legacy['DatabaseSettings']['content']['Password']['content'] );
        $this->assertSame( 'override', $legacy['DatabaseSettings']['content']['Password']['placement'] );
        $list = $legacy['SiteAccessSettings']['content']['AvailableSiteAccessList'];
        $this->assertSame( 'array', $list['type'] );
        $this->assertSame( '', $list['placement'] );
        $this->assertSame( array( 'override', 'override' ), array_column( $list['content'], 'placement' ) );
        $map = $legacy['RegionalSettings']['content']['Map']['content'];
        $this->assertSame( array( 'default', 'extension:ezoe' ), array( $map['one']['placement'], $map['two']['placement'] ) );
        $this->assertTrue( $legacy['SiteSettings']['content']['SiteName']['removeable'] );
        $this->assertFalse( $legacy['RegionalSettings']['content']['Map']['removeable'] );
        $this->assertTrue( $legacy['SiteSettings']['editable'] );
    }
}
