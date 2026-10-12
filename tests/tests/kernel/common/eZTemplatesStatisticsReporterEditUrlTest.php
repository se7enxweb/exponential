<?php
/**
 * The "Edit" links of the debug output's list of used templates follow site.ini [DebugSettings] UsedTemplateEditUrl.
 *
 *   - empty or not set (the default): <visual/templateedit>/<file>/(siteAccess)/<siteaccess>, the link the list
 *     always had
 *   - a sprintf() format: %1$s the editor's address with the file, %2$s the siteaccess, %3$s the template file;
 *     the link is HTML-escaped
 *   - a format sprintf() refuses: the usual link, and a warning
 *
 * The setting is changed in-process only (ezpINIHelper, restored in tearDown()); the list is built from template
 * usage entries of the test, nothing is rendered or written.
 *
 * Portions derived from LoveStack (mugoweb/ezpublish-legacy, commit fb20bee0d2), Copyright (C) Mugo Web, GPL v2
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 */

class eZTemplatesStatisticsReporterEditUrlTest extends PHPUnit\Framework\TestCase
{
    const FILE = 'design/standard/templates/node/view/full.tpl';

    private $saved = array();
    private $savedStats;

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        foreach ( array( 'eZTemplateDebugTemplatesUsageStatisticsEnabled', 'eZCurrentAccess' ) as $name )
            $this->saved[$name] = array_key_exists( $name, $GLOBALS ) ? array( $GLOBALS[$name] ) : null;
        $GLOBALS['eZTemplateDebugTemplatesUsageStatisticsEnabled'] = true;
        $GLOBALS['eZCurrentAccess'] = array( 'name' => 'w6site', 'type' => 1 );

        $tpl = eZTemplate::instance();
        $this->savedStats = $tpl->TemplatesUsageStatistics;
        $tpl->TemplatesUsageStatistics = array( array(
            'actual-template-name' => 'node/view/full.tpl',
            'requested-template-name' => 'node/view/full.tpl',
            'template-filename' => self::FILE,
        ) );
    }

    protected function tearDown(): void
    {
        eZTemplate::instance()->TemplatesUsageStatistics = $this->savedStats;
        ezpINIHelper::restoreINISettings();
        foreach ( $this->saved as $name => $saved )
        {
            if ( $saved === null )
                unset( $GLOBALS[$name] );
            else
                $GLOBALS[$name] = $saved[0];
        }
    }

    /** The href of the "Edit template" link of the list. */
    private static function editHref()
    {
        $html = eZTemplatesStatisticsReporter::generateStatistics( true );
        if ( !preg_match( '#<a href="([^"]*)"><img [^>]*alt="Edit template"#', $html, $m ) )
            return null;
        return $m[1];
    }

    /** The address of visual/templateedit as the list builds it. */
    private static function editor()
    {
        $editor = 'visual/templateedit';
        eZURI::transformURI( $editor );
        return $editor;
    }

    /**
     * @testdox empty (the default): the link the list always had
     */
    public function testEmptyKeepsTheUsualLink(): void
    {
        ezpINIHelper::setINISetting( 'site.ini', 'DebugSettings', 'UsedTemplateEditUrl', '' );
        $this->assertSame( self::editor() . '/' . self::FILE . '/(siteAccess)/w6site', self::editHref() );
        $this->assertFalse( eZTemplatesStatisticsReporter::templateEditUrlFormat() );
    }

    /**
     * @testdox the LoveStack default %1$s/(siteAccess)/%2$s gives the same link
     */
    public function testUsualFormatGivesTheUsualLink(): void
    {
        ezpINIHelper::setINISetting( 'site.ini', 'DebugSettings', 'UsedTemplateEditUrl', '%1$s/(siteAccess)/%2$s' );
        $this->assertSame( self::editor() . '/' . self::FILE . '/(siteAccess)/w6site', self::editHref() );
    }

    /**
     * @testdox a format of its own: the placeholders filled, the link escaped
     */
    public function testOwnFormat(): void
    {
        ezpINIHelper::setINISetting( 'site.ini', 'DebugSettings', 'UsedTemplateEditUrl', '/admin/visual/templateedit/%3$s/(siteAccess)/%2$s?a=1&b=2' );
        $this->assertSame( '/admin/visual/templateedit/' . self::FILE . '/(siteAccess)/w6site?a=1&amp;b=2', self::editHref() );
    }

    /**
     * @testdox a format sprintf() refuses: the usual link
     */
    public function testRefusedFormatFallsBack(): void
    {
        $this->assertSame( 'ed/x.tpl/(siteAccess)/w6site',
                           eZTemplatesStatisticsReporter::templateEditUrl( '%1$s/%4$s', 'ed/x.tpl', 'w6site', 'x.tpl' ) );
    }
}
