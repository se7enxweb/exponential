<?php
/**
 * What an upgraded site gets: the shipped settings of the features of 6.0.15 that were rebuilt from LoveStack keep
 * today's behaviour unless the feature is a plain fix or one of the security checks that are on for everyone. An
 * upgrade keeps its settings/override and siteaccess files; what changes is only what the shipped files say, so
 * these are read alone (eZINI::fetchFromFile(): no siteaccess, extension or override settings), as an upgraded site
 * without its own value sees them.
 *
 *  SD-L01 - ezjscnode node data calls: RequirePolicy=disabled, the function listed for the policy's limitation
 *  SD-L02 - registration: PolicyOmitListExclude[] empty, user/register still in PolicyOmitList[]
 *  SD-L05 - uploaded images checked by content (on for everyone): ValidateMIMEType=enabled; SVG listed, with SanitizeSVG=enabled
 *  SD-L07 - deprecations to deprecated.log (on by default): DeprecatedLog=enabled
 *  SD-L08 - log size 2 MB, three rotated files; config.php-RECOMMENDED documents the constants that win over them
 *  SD-L10 - the used templates' edit link: UsedTemplateEditUrl empty (the usual link)
 *  SD-L13 - location audit: removal recorded as before; adding a location and changing the main one are mapped
 *           but not in Enabled[]
 *  SD-L14 - site data in templates: TemplateReadable[] empty
 *  SD-L18 - name pattern resolver classes: ResolverClasses[] empty
 *  SD-L19 - class filter default: DefaultClassFilterType empty (today's results)
 *  SD-L22 - hidden custom tags: none (IsHidden[] only as a comment)
 *  SD-L23 - custom tag controls in the editor: CustomTagControls=disabled
 *  SD-L27 - the rawurlencode and rawurldecode operators are declared
 *  SD-L29 - error page headers: HeaderList[] empty for every HTTP error block
 *  SD-L30 - ezjscore search order: SortBy[published]=desc, the order it always had
 *  SD-L32 - request URLs decoded once: DecodeOnce=disabled
 *  SD-L33 - IRI alias trailing cleanup: TrailingCleanup=disabled
 *
 * The values a new installation gets where they differ are written by the installer into settings/override and
 * are tested with the installer, not here.
 * No database, nothing written.
 *
 * The features are rebuilt from LoveStack (mugoweb/ezpublish-legacy), with thanks to Mugo Web; the commits are
 * named in the 6.0.15 changelog. This test is Exponential's own.
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/classes/expShippedFeatureDefaultsTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group kernel
 */

class expShippedFeatureDefaultsTest extends PHPUnit\Framework\TestCase
{
    /** @var eZINI[] shipped file => its settings */
    private static $files = array();

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 4 ) );
    }

    public static function tearDownAfterClass(): void
    {
        self::$files = array();
    }

    private static function shipped( $file )
    {
        if ( !isset( self::$files[$file] ) )
        {
            self::assertFileExists( $file );
            self::$files[$file] = eZINI::fetchFromFile( $file );
        }
        return self::$files[$file];
    }

    private static function value( $file, $group, $name )
    {
        $ini = self::shipped( $file );
        self::assertTrue( $ini->hasVariable( $group, $name ), "$file [$group] $name is shipped" );
        return $ini->variable( $group, $name );
    }

    /** A list setting without its empty entries */
    private static function listValue( $file, $group, $name )
    {
        return array_values( array_filter( (array)self::value( $file, $group, $name ), function ( $v ) { return $v !== '' && $v !== null; } ) );
    }

    /** SD-L01 */
    public function testEzjscnodePolicyIsOffOnAnUpgrade()
    {
        $file = 'extension/ezjscore/settings/ezjscore.ini';
        $this->assertSame( 'disabled', self::value( $file, 'ezjscServer_ezjscnode', 'RequirePolicy' ) );
        $this->assertContains( 'ezjscnode', self::listValue( $file, 'ezjscServer_ezjscnode', 'Functions' ) );
        $this->assertContains( 'ezjscnode', self::listValue( $file, 'ezjscServer', 'FunctionList' ) );
    }

    /** SD-L02 */
    public function testRegistrationStaysOpenOnAnUpgrade()
    {
        $this->assertSame( array(), self::listValue( 'settings/site.ini', 'RoleSettings', 'PolicyOmitListExclude' ) );
        $this->assertContains( 'user/register', self::listValue( 'settings/site.ini', 'RoleSettings', 'PolicyOmitList' ) );
    }

    /** SD-L05 */
    public function testImageUploadsAreCheckedByContentForEveryone()
    {
        $this->assertSame( 'enabled', self::value( 'settings/image.ini', 'ImageUploadSettings', 'ValidateMIMEType' ) );
        $types = self::listValue( 'settings/image.ini', 'ImageUploadSettings', 'AllowedMIMETypes' );
        foreach ( array( 'image/jpeg', 'image/png', 'image/gif', 'image/webp' ) as $type )
            $this->assertContains( $type, $types );
        // SVG is accepted because it is sanitized on upload
        $this->assertContains( 'image/svg+xml', $types );
        $this->assertSame( 'enabled', self::value( 'settings/image.ini', 'ImageUploadSettings', 'SanitizeSVG' ) );
    }

    /** SD-L07, SD-L08, SD-L10 */
    public function testDiagnosticsDefaults()
    {
        $this->assertSame( 'enabled', self::value( 'settings/site.ini', 'DebugSettings', 'DeprecatedLog' ) );
        $this->assertSame( '2097152', self::value( 'settings/site.ini', 'FileSettings', 'LogFileMaxSize' ) );
        $this->assertSame( '3', self::value( 'settings/site.ini', 'FileSettings', 'LogFileRotateCount' ) );
        $this->assertSame( '', self::value( 'settings/site.ini', 'DebugSettings', 'UsedTemplateEditUrl' ) );
    }

    /**
     * SD-L08 (config.php-RECOMMENDED): the four log constants it documents are commented out, so the site.ini
     * settings apply, and they are the constants eZDebug and eZLog read; the settings it names are shipped
     */
    public function testConfigRecommendedDocumentsTheLogSettings()
    {
        $config = file_get_contents( 'config.php-RECOMMENDED' );
        $code = file_get_contents( 'lib/ezutils/classes/ezdebug.php' ) . file_get_contents( 'lib/ezfile/classes/ezlog.php' );
        foreach ( array( 'EZPUBLISH_LOG_MAX_FILE_SIZE', 'EZPUBLISH_LOG_ROTATE_FILES', 'CUSTOM_LOG_MAX_FILE_SIZE', 'CUSTOM_LOG_ROTATE_FILES' ) as $constant )
        {
            $this->assertMatchesRegularExpression( "#^// define\( '$constant', \d+ \);$#m", $config, "$constant is documented, commented out" );
            $this->assertStringContainsString( "defined( '$constant' )", $code, "$constant is read" );
        }
        foreach ( array( 'LogFileMaxSize', 'LogFileRotateCount' ) as $setting )
        {
            $this->assertStringContainsString( $setting, $config );
            self::value( 'settings/site.ini', 'FileSettings', $setting );
        }
        $this->assertStringContainsString( 'deprecated.log', $config );
    }

    /** SD-L13 */
    public function testLocationAuditKeepsTodaysRecords()
    {
        $enabled = self::listValue( 'settings/audit.ini', 'AuditEventSettings', 'Enabled' );
        $this->assertContains( 'content.node.remove.*', $enabled, 'removing a location is recorded, as before' );
        foreach ( array( 'content.node.add', 'content.node.main', 'content.node.*', 'content.*' ) as $event )
            $this->assertNotContains( $event, $enabled, "$event is not switched on by an upgrade" );
        $map = self::value( 'settings/audit.ini', 'AuditCompatSettings', 'Map' );
        $this->assertSame( 'content.node.add', $map['location-assign'] );
        $this->assertSame( 'content.node.remove', $map['location-remove'] );
        $this->assertSame( 'content.node.main', $map['main-node-update'] );
    }

    /** SD-L14, SD-L18, SD-L19 */
    public function testContentOptionsAreOff()
    {
        $this->assertSame( array(), self::listValue( 'settings/site.ini', 'SiteDataSettings', 'TemplateReadable' ) );
        $this->assertSame( array(), self::listValue( 'settings/content.ini', 'NamePatternSettings', 'ResolverClasses' ) );
        $this->assertSame( '', self::value( 'settings/content.ini', 'FetchSettings', 'DefaultClassFilterType' ) );
    }

    /** SD-L22, SD-L23 */
    public function testEditorOptionsAreOff()
    {
        $this->assertSame( 'disabled', self::value( 'extension/ezoe/settings/ezoe.ini', 'EditorSettings', 'CustomTagControls' ) );
        $content = self::shipped( 'extension/ezoe/settings/content.ini.append.php' );
        $hidden = $content->hasVariable( 'CustomTagSettings', 'IsHidden' ) ? array_filter( (array)$content->variable( 'CustomTagSettings', 'IsHidden' ) ) : array();
        $this->assertSame( array(), $hidden, 'no custom tag is hidden' );
    }

    /** SD-L27 */
    public function testRawUrlOperatorsAreDeclared()
    {
        $operators = self::value( 'settings/template.ini', 'PHP', 'PHPOperatorList' );
        $this->assertSame( 'rawurlencode', $operators['rawurlencode'] );
        $this->assertSame( 'rawurldecode', $operators['rawurldecode'] );
    }

    /** SD-L29 */
    public function testErrorPagesSendNoExtraHeaders()
    {
        $error = self::shipped( 'settings/error.ini' );
        $blocks = 0;
        foreach ( $error->groups() as $group => $settings )
        {
            if ( strpos( $group, 'HTTPError-' ) !== 0 || !array_key_exists( 'HeaderList', $settings ) )
                continue;
            $blocks++;
            $this->assertSame( array(), array_values( array_filter( (array)$settings['HeaderList'] ) ), "[$group] HeaderList[]" );
        }
        $this->assertGreaterThanOrEqual( 4, $blocks, 'HTTPError-404, 301, 401 and 403 ship the empty list' );
    }

    /** SD-L30 */
    public function testEzjscoreSearchKeepsItsOrder()
    {
        $sortBy = self::value( 'extension/ezjscore/settings/ezfind.ini.append.php', 'SearchSettings', 'SortBy' );
        $this->assertSame( array( 'published' => 'desc' ), array_filter( (array)$sortBy ) );
    }

    /** SD-L32, SD-L33 */
    public function testUrlOptionsAreOff()
    {
        $this->assertSame( 'disabled', self::value( 'settings/site.ini', 'URLTranslator', 'DecodeOnce' ) );
        $this->assertSame( 'disabled', self::value( 'settings/site.ini', 'URLTranslator', 'TrailingCleanup' ) );
    }
}
