<?php
/**
 * The quick location buttons of ezmultiupload's upload page (6.1.8, expMultiuploadService::quickLocations() on the
 * kernel's expNodeQuickPick): which buttons are built, which places the user's permissions leave out, and which one
 * is marked as the target. The tree, the permissions and the classes are held in memory (a subclass replaces the
 * methods that read the database); no database.
 *
 *  QL-01  the buttons: Websites and Media first, then a row of the locations below each in the tree's order, with
 *         labels, paths and the location as JSON for the page's script; the row length and "More"
 *  QL-02  permission filtering: a location the user cannot read, cannot create in, that is no container, where none
 *         of the uploaded classes may be created, or that is hidden is left out; a place left out keeps its row
 *  QL-03  the current marking: the target's button is current, the place it lies in within (never by an id prefix)
 *  QL-04  the settings: QuickLocations=disabled, the keys and the limit bounded; the fetch function refuses bad ids
 *  QL-05  the template and the scripts: buttons with aria-pressed and an accessible name, the shared .a4-quickpick,
 *         no inline CSS or script, the same hidden field as the picker
 *  QL-06  Images and Files right after Media (found by their paths, translated labels), not again in Media's row,
 *         with the same permission filtering and current marking
 *  QL-07  the dashboard's upload target: Media > Images where the user can upload, else Media
 *  QL-08  the dashboards of admin4 (admin4l) and exp_adminui open the upload page at that target
 *
 * Run: php vendor/bin/phpunit tests/tests/extension/ezmultiupload/expMultiuploadQuickLocationsTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

$expMultiuploadRoot = dirname( __DIR__, 4 ) . '/extension/ezmultiupload/classes/';
foreach ( array( 'expmultiuploadservice.php', 'expmultiuploadfunctioncollection.php' ) as $expMultiuploadFile )
{
    if ( is_file( $expMultiuploadRoot . $expMultiuploadFile ) )
        require_once $expMultiuploadRoot . $expMultiuploadFile;
}
unset( $expMultiuploadRoot, $expMultiuploadFile );

if ( class_exists( 'expMultiuploadService' ) && method_exists( 'expMultiuploadService', 'quickLocations' ) )
{
    /** A node held in memory */
    class expMuQuickTestNode extends eZContentObjectTreeNode
    {
        public $values = array();

        public function __construct( array $values )
        {
            $this->values = $values + array( 'readable' => true, 'creatable' => true, 'is_container' => true, 'is_hidden' => 0,
                                             'is_invisible' => 0, 'class_identifier' => 'folder', 'contentclass_id' => 1 );
        }

        function attribute( $name, $noFunction = false )
        {
            return isset( $this->values[$name] ) ? $this->values[$name] : null;
        }

        function canRead()
        {
            return (bool)$this->values['readable'];
        }

        function canCreate()
        {
            return (bool)$this->values['creatable'];
        }
    }

    /** A content class held in memory */
    class expMuQuickTestClass extends eZContentClass
    {
        public $values = array();

        public function __construct( array $values )
        {
            $this->values = $values;
        }

        function attribute( $name, $noFunction = false )
        {
            return isset( $this->values[$name] ) ? $this->values[$name] : null;
        }
    }

    /** The service with the tree and the permissions in memory */
    class expMuQuickTestService extends expMultiuploadService
    {
        /** @var expMuQuickTestNode[] node id => node */
        public static $nodes = array();
        /** @var array key => node id */
        public static $keys = array();
        /** @var array node id => class identifiers the user may create there (missing: every class) */
        public static $classes = array();

        protected static function fetchNode( $nodeID )
        {
            return isset( self::$nodes[(int)$nodeID] ) ? self::$nodes[(int)$nodeID] : null;
        }

        protected static function fetchClassByIdentifier( $identifier )
        {
            return new expMuQuickTestClass( array( 'id' => strlen( $identifier ), 'identifier' => $identifier ) );
        }

        protected static function canCreateClass( $node, $class )
        {
            $id = (int)$node->attribute( 'node_id' );
            return !isset( self::$classes[$id] ) || in_array( $class->attribute( 'identifier' ), self::$classes[$id], true );
        }

        protected static function isContainer( $node )
        {
            return !empty( $node->values['is_container'] );
        }

        protected static function pathNames( $node )
        {
            $names = array();
            foreach ( static::pathIds( $node ) as $id )
                $names[] = (string)self::$nodes[$id]->attribute( 'name' );
            return $names;
        }

        protected static function url( $path )
        {
            return '/admin/' . $path;
        }

        protected static function quickNodeID( $key )
        {
            return isset( self::$keys[$key] ) ? self::$keys[$key] : 0;
        }

        protected static function quickChildRows( $node )
        {
            $rows = array();
            foreach ( self::$nodes as $id => $child )
            {
                if ( (int)$child->attribute( 'parent_node_id' ) === (int)$node->attribute( 'node_id' ) )
                    $rows[] = array( 'node_id' => $id, 'name' => (string)$child->attribute( 'name' ), 'is_hidden' => $child->attribute( 'is_hidden' ),
                                     'is_invisible' => $child->attribute( 'is_invisible' ), 'node' => $child );
            }
            return $rows;
        }
    }

    /** The fetch function on the in-memory service */
    class expMuQuickTestFunctions extends expMultiuploadFunctionCollection
    {
    }
}

class expMultiuploadQuickLocationsTest extends PHPUnit\Framework\TestCase
{
    /** @var string|null */
    private $cwd = null;

    protected function setUp(): void
    {
        if ( !class_exists( 'expMuQuickTestService', false ) || !class_exists( 'expNodeQuickPick' ) )
            self::markTestSkipped( 'ezmultiupload 6.1.8 or later and the kernel\'s expNodeQuickPick are needed.' );
        $this->cwd = getcwd();
        chdir( dirname( __DIR__, 4 ) );
        $ini = eZINI::instance( 'ezmultiupload.ini' );
        $ini->setReadOnlySettingsCheck( false );
        $ini->setVariable( 'MultiUploadSettings', 'AvailableClasses', array( 'folder', 'gallery' ) );
        $ini->setVariable( 'MultiUploadSettings', 'AvailableSubtreeNode', array() );
        $ini->setVariable( 'MultiUploadSettings', 'AnyContainer', 'enabled' );
        $ini->setVariable( 'QuickLocationSettings', 'QuickLocations', 'enabled' );
        $ini->setVariable( 'QuickLocationSettings', 'PresetList', array( 'content', 'media' ) );
        $ini->setVariable( 'QuickLocationSettings', 'ChildRowList', array( 'content', 'media' ) );
        $ini->setVariable( 'QuickLocationSettings', 'ChildRowLimit', '12' );
        $upload = eZINI::instance( 'upload.ini' );
        $upload->setReadOnlySettingsCheck( false );
        $upload->setVariable( 'CreateSettings', 'MimeClassMap', array( 'image' => 'image', 'video' => 'file' ) );
        $upload->setVariable( 'CreateSettings', 'DefaultClass', 'file' );

        $n = function ( $id, $parent, $name, array $more = array() )
        {
            $path = $parent ? expMuQuickTestService::$nodes[$parent]->attribute( 'path_string' ) . $id . '/' : '/1/' . $id . '/';
            return new expMuQuickTestNode( array( 'node_id' => $id, 'parent_node_id' => $parent ?: 1, 'name' => $name, 'path_string' => $path ) + $more );
        };
        expMuQuickTestService::$nodes = array();
        foreach ( array( array( 2, 0, 'Home', array( 'class_identifier' => 'ng_frontpage' ) ), array( 43, 0, 'Media' ),
                         array( 73, 2, 'Bold Agency' ), array( 89, 2, 'Fit & Healthy' ), array( 90, 2, 'Hidden site', array( 'is_hidden' => 1 ) ),
                         array( 91, 2, 'Secret', array( 'readable' => false ) ), array( 92, 2, 'Read only', array( 'creatable' => false ) ),
                         array( 51, 43, 'Images' ), array( 52, 43, 'Files' ), array( 53, 43, 'Multimedia' ),
                         array( 54, 43, 'Articles only' ), array( 55, 43, 'A page', array( 'is_container' => false, 'class_identifier' => 'article' ) ),
                         array( 56, 43, 'Banners' ), array( 510, 51, 'Logos' ) ) as $row )
            expMuQuickTestService::$nodes[$row[0]] = $n( $row[0], $row[1], $row[2], isset( $row[3] ) ? $row[3] : array() );
        expMuQuickTestService::$keys = array( 'content' => 2, 'media' => 43, 'media/images' => 51, 'media/files' => 52 );
        eZINI::instance( 'content.ini' )->setVariable( 'NodeSettings', 'MediaRootNode', '43' );
        expMuQuickTestService::$classes = array( 54 => array( 'article' ) );
    }

    protected function tearDown(): void
    {
        if ( $this->cwd !== null )
        {
            eZINI::resetInstance( 'ezmultiupload.ini' );
            eZINI::resetInstance( 'upload.ini' );
            eZINI::resetInstance( 'content.ini' );
            chdir( $this->cwd );
        }
    }

    private function target( $id )
    {
        return expMuQuickTestService::$nodes[$id];
    }

    private static function ids( array $buttons )
    {
        return array_column( $buttons, 'node_id' );
    }

    /** QL-01 */
    public function testTheButtons()
    {
        $quick = expMuQuickTestService::quickLocations( $this->target( 43 ) );
        $this->assertSame( array( 2, 43 ), self::ids( $quick['presets'] ) );
        $this->assertSame( array( 'Websites', 'Media' ), array_column( $quick['presets'], 'label' ) );
        $this->assertSame( array( 'content', 'media' ), array_column( $quick['rows'], 'key' ) );
        $this->assertSame( array( 'Websites', 'Media' ), array_column( $quick['rows'], 'label' ) );
        $this->assertSame( array( 2, 43 ), array_column( $quick['rows'], 'parent_id' ) );
        $this->assertSame( array( 73, 89 ), self::ids( $quick['rows'][0]['items'] ) );
        $this->assertSame( array( 51, 52, 53, 56 ), self::ids( $quick['rows'][1]['items'] ), 'Images, Files, Multimedia and the others, in order' );
        $this->assertSame( array( 'Images', 'Files', 'Multimedia', 'Banners' ), array_column( $quick['rows'][1]['items'], 'label' ) );
        $this->assertFalse( $quick['rows'][1]['more'] );

        $images = $quick['rows'][1]['items'][0];
        $this->assertSame( 'Media / Images', $images['path'] );
        $location = json_decode( $images['location'], true );
        $this->assertSame( 51, $location['id'] );
        $this->assertSame( 'Media / Images', $location['pathText'] );
        $this->assertSame( array( 43, 51 ), $location['pathIds'] );
        $this->assertTrue( $location['allowed'] );
        $this->assertSame( '/admin/content/view/full/51', $location['url'] );
        // safe in an HTML attribute as it is
        $fit = expMuQuickTestService::quickLocations( $this->target( 89 ) )['rows'][0]['items'][1];
        $this->assertSame( 'Fit & Healthy', $fit['label'] );
        $this->assertDoesNotMatchRegularExpression( '/[<>&\']/', $fit['location'] );
        $this->assertSame( 'Home / Fit & Healthy', json_decode( $fit['location'], true )['pathText'] );

        // the row length, then "More"
        eZINI::instance( 'ezmultiupload.ini' )->setVariable( 'QuickLocationSettings', 'ChildRowLimit', '2' );
        $quick = expMuQuickTestService::quickLocations( $this->target( 43 ) );
        $this->assertSame( array( 51, 52 ), self::ids( $quick['rows'][1]['items'] ) );
        $this->assertTrue( $quick['rows'][1]['more'] );
        $this->assertFalse( $quick['rows'][0]['more'] );
    }

    /** QL-02 */
    public function testPermissionFiltering()
    {
        $quick = expMuQuickTestService::quickLocations( $this->target( 43 ) );
        $all = array_merge( self::ids( $quick['presets'] ), self::ids( $quick['rows'][0]['items'] ), self::ids( $quick['rows'][1]['items'] ) );
        foreach ( array( 90 => 'hidden', 91 => 'not readable', 92 => 'no create access', 54 => 'no uploaded class may be created',
                         55 => 'not a container' ) as $id => $why )
            $this->assertNotContains( $id, $all, $why );
        $this->assertStringNotContainsString( 'Secret', json_encode( $quick ), 'nothing of a location the user cannot read' );

        // the uploaded classes: one of them is enough (Images may take images only)
        expMuQuickTestService::$classes[51] = array( 'image' );
        $this->assertContains( 51, self::ids( expMuQuickTestService::quickLocations( $this->target( 43 ) )['rows'][1]['items'] ) );
        $this->assertSame( array( 'image', 'file' ), expMuQuickTestService::uploadClasses() );
        $this->assertTrue( expMuQuickTestService::quickLocationAllowed( $this->target( 51 ) ) );
        $this->assertFalse( expMuQuickTestService::quickLocationAllowed( $this->target( 54 ) ) );
        $this->assertFalse( expMuQuickTestService::quickLocationAllowed( null ) );

        // Media itself closed: its button goes, its row stays
        expMuQuickTestService::$nodes[43]->values['creatable'] = false;
        $quick = expMuQuickTestService::quickLocations( $this->target( 51 ) );
        $this->assertSame( array( 2 ), self::ids( $quick['presets'] ) );
        $this->assertSame( array( 51, 52, 53, 56 ), self::ids( $quick['rows'][1]['items'] ) );

        // Media not readable: no button and no row, nothing of its name
        expMuQuickTestService::$nodes[43]->values['readable'] = false;
        $quick = expMuQuickTestService::quickLocations( $this->target( 73 ) );
        $this->assertSame( array( 2 ), self::ids( $quick['presets'] ) );
        $this->assertSame( array( 'content' ), array_column( $quick['rows'], 'key' ) );

        // a place that is not there
        expMuQuickTestService::$keys = array( 'content' => 0, 'media' => 999 );
        $this->assertSame( array( 'presets' => array(), 'rows' => array() ), expMuQuickTestService::quickLocations( $this->target( 73 ) ) );
    }

    /** QL-03 */
    public function testTheCurrentMarking()
    {
        $current = function ( array $quick )
        {
            $list = array();
            foreach ( array_merge( $quick['presets'], $quick['rows'][0]['items'], $quick['rows'][1]['items'] ) as $b )
                $list[$b['node_id']] = ( $b['current'] ? 'current' : '' ) . ( $b['within'] ? 'within' : '' );
            return array_filter( $list );
        };
        $this->assertSame( array( 43 => 'current' ), $current( expMuQuickTestService::quickLocations( $this->target( 43 ) ) ) );
        $this->assertSame( array( 43 => 'within', 51 => 'current' ), $current( expMuQuickTestService::quickLocations( $this->target( 51 ) ) ) );
        $this->assertSame( array( 43 => 'within', 51 => 'within' ), $current( expMuQuickTestService::quickLocations( $this->target( 510 ) ) ), 'below Images' );
        $this->assertSame( array( 2 => 'within', 89 => 'current' ), $current( expMuQuickTestService::quickLocations( $this->target( 89 ) ) ) );
        $this->assertSame( array( 2 => 'current' ), $current( expMuQuickTestService::quickLocations( $this->target( 2 ) ) ) );
        // a target outside every place marks nothing; /1/5/ is no prefix of 51 or 52
        expMuQuickTestService::$nodes[5] = new expMuQuickTestNode( array( 'node_id' => 5, 'parent_node_id' => 1, 'name' => 'Users', 'path_string' => '/1/5/' ) );
        $this->assertSame( array(), $current( expMuQuickTestService::quickLocations( $this->target( 5 ) ) ) );
    }

    /** QL-04 */
    public function testSettingsAndFetchFunction()
    {
        $settings = expMuQuickTestService::quickLocationSettings();
        $this->assertSame( array( 'enabled' => true, 'presets' => array( 'content', 'media' ), 'rows' => array( 'content', 'media' ), 'limit' => 12,
                                 'labels' => array( 'media/images' => 'Images', 'media/files' => 'Files' ) ), $settings );
        $ini = eZINI::instance( 'ezmultiupload.ini' );
        $ini->setVariable( 'QuickLocationSettings', 'ChildRowLimit', '500' );
        $ini->setVariable( 'QuickLocationSettings', 'PresetList', array( 'media', 'media', '', array( 'x' ), 'remote:abc' ) );
        $settings = expMuQuickTestService::quickLocationSettings();
        $this->assertSame( 12, $settings['limit'] );
        $this->assertSame( array( 'media', 'remote:abc' ), $settings['presets'] );
        $ini->setVariable( 'QuickLocationSettings', 'QuickLocations', 'disabled' );
        $this->assertSame( array( 'presets' => array(), 'rows' => array() ), expMuQuickTestService::quickLocations( $this->target( 43 ) ) );
        $this->assertSame( array( 'presets' => array(), 'rows' => array() ), expMuQuickTestService::quickLocations( null ) );

        expMuQuickTestFunctions::$service = 'expMuQuickTestService';
        try
        {
            foreach ( array( 0, -1, '1 OR 1=1', 'abc', null, array( 43 ), '' ) as $bad )
                $this->assertSame( array( 'result' => array( 'presets' => array(), 'rows' => array() ) ), expMuQuickTestFunctions::fetchQuickLocations( $bad ), var_export( $bad, true ) );
            expMuQuickTestFunctions::$service = 'expNoSuchService';
            $this->assertSame( array(), expMuQuickTestFunctions::fetchQuickLocations( 43 )['result']['presets'] );
        }
        finally
        {
            expMuQuickTestFunctions::$service = 'expMultiuploadService';
        }
    }

    /** QL-05 */
    public function testTemplateAndScripts()
    {
        $root = dirname( __DIR__, 4 ) . '/extension/ezmultiupload';
        $tpl = (string)file_get_contents( $root . '/design/admin/templates/ezmultiupload/quick_locations.tpl' );
        $code = preg_replace( '/\{\*.*?\*\}/s', '', $tpl );
        foreach ( array( 'a4-toolbar a4-quickpick', 'a4-quickpick-label', 'role="group"', 'aria-labelledby=', 'type="button"', 'aria-pressed=',
                         'data-exp-mu-quick-location="{$mu_button.location|wash}"', '{$mu_button.label|wash}', 'a4-sr-only', 'data-exp-mu-quick-more' ) as $needle )
            $this->assertStringContainsString( $needle, $code );
        $this->assertDoesNotMatchRegularExpression( '/<style\b|\sstyle\s*=|<script\b|\son[a-z]+\s*=/i', $code );
        $this->assertSame( '', trim( preg_replace( '/\{\*.*?\*\}/s', '', (string)file_get_contents( $root . '/design/standard/templates/ezmultiupload/quick_locations.tpl' ) ) ), 'the site designs show none' );

        $body = (string)file_get_contents( $root . '/design/admin4/templates/ezmultiupload/upload_body.tpl' );
        $this->assertStringContainsString( "{include uri='design:ezmultiupload/quick_locations.tpl' parent_node=\$parent_node}", $body );
        $this->assertStringContainsString( 'id="exp-mu-location-id"', $body );
        $this->assertStringContainsString( 'id="exp-mu-change-location"', $body, 'Change location... stays' );
        $classic = (string)file_get_contents( $root . '/design/standard/templates/ezmultiupload/upload.tpl' );
        $this->assertStringContainsString( "{include uri='design:ezmultiupload/quick_locations.tpl' parent_node=\$parent_node more=false()}", $classic );

        $page = (string)file_get_contents( $root . '/design/standard/javascript/expmultiupload.js' );
        foreach ( array( "setAttribute('aria-pressed'", "setQueueLocation(loc)", "field.value = String(queueLocation.id)", "data-exp-mu-quick-more" ) as $needle )
            $this->assertStringContainsString( $needle, $page );
        $old = (string)file_get_contents( $root . '/design/standard/javascript/ezmultiupload_upload.js' );
        foreach ( array( "setAttribute('aria-pressed'", 'up.options.url = uploadBase', "\$('#ezmultiupload-location')" ) as $needle )
            $this->assertStringContainsString( $needle, $old );
        foreach ( array( $page, $old ) as $script )
            $this->assertDoesNotMatchRegularExpression( '/\.style\.|cssText|innerHTML\s*=\s*loc/', $script );
    }

    /** QL-06 */
    public function testImagesAndFilesAfterMedia()
    {
        $ini = eZINI::instance( 'ezmultiupload.ini' );
        $ini->setVariable( 'QuickLocationSettings', 'PresetList', array( 'content', 'media', 'media/images', 'media/files' ) );
        $ini->setVariable( 'QuickLocationSettings', 'PresetLabels', array( 'media/images' => 'Images', 'media/files' => 'Files' ) );
        expMuQuickTestService::$nodes[51]->values['name'] = 'Pictures folder';
        $quick = expMuQuickTestService::quickLocations( $this->target( 51 ) );
        $this->assertSame( array( 2, 43, 51, 52 ), self::ids( $quick['presets'] ), 'Websites | Media | Images | Files' );
        $this->assertSame( array( 'Websites', 'Media', 'Images', 'Files' ), array_column( $quick['presets'], 'label' ), 'the label, not the node name' );
        $this->assertSame( array( 53, 56 ), self::ids( $quick['rows'][1]['items'] ), 'not again in the row under Media' );
        $this->assertTrue( $quick['presets'][2]['current'] );
        $this->assertTrue( $quick['presets'][1]['within'] );
        $this->assertSame( 'Images', expMuQuickTestService::quickLabel( 'media/images', 'x', array( 'media/images' => 'Images' ) ) );
        $this->assertSame( 'Pictures', expMuQuickTestService::quickLabel( 'media/x', 'Pictures', array() ) );

        // Images closed to the user: no button, and nothing else moves
        expMuQuickTestService::$classes[51] = array( 'article' );
        $quick = expMuQuickTestService::quickLocations( $this->target( 43 ) );
        $this->assertSame( array( 2, 43, 52 ), self::ids( $quick['presets'] ) );
        $this->assertSame( array( 53, 56 ), self::ids( $quick['rows'][1]['items'] ) );
        // Files not there on this site
        expMuQuickTestService::$keys['media/files'] = 0;
        $this->assertSame( array( 2, 43 ), self::ids( expMuQuickTestService::quickLocations( $this->target( 43 ) )['presets'] ) );
        // the shipped settings
        $shipped = (string)file_get_contents( dirname( __DIR__, 4 ) . '/extension/ezmultiupload/settings/ezmultiupload.ini' );
        $this->assertMatchesRegularExpression( '/PresetList\[\]=media\nPresetList\[\]=media\/images\nPresetList\[\]=media\/files\n/', $shipped );
        $this->assertStringContainsString( 'PresetLabels[media/images]=Images', $shipped );
    }

    /** QL-07 */
    public function testDashboardTarget()
    {
        $this->assertSame( 51, expMuQuickTestService::dashboardTarget(), 'Media > Images' );
        expMuQuickTestService::$classes[51] = array( 'article' );
        $this->assertSame( 43, expMuQuickTestService::dashboardTarget(), 'Media when the user may not upload to Images' );
        unset( expMuQuickTestService::$classes[51] );
        expMuQuickTestService::$keys['media/images'] = 0;
        $this->assertSame( 43, expMuQuickTestService::dashboardTarget(), 'Media when there is no Images folder' );
        expMuQuickTestService::$nodes[43]->values['creatable'] = false;
        $this->assertSame( 43, expMuQuickTestService::dashboardTarget(), 'the media root as before when nothing takes files' );
        eZINI::instance( 'ezmultiupload.ini' )->setVariable( 'DashboardSettings', 'UploadTargetList', array( 'media/files', 'media' ) );
        $this->assertSame( 52, expMuQuickTestService::dashboardTarget() );

        expMuQuickTestFunctions::$service = 'expMuQuickTestService';
        try
        {
            $this->assertSame( array( 'result' => 52 ), expMuQuickTestFunctions::fetchDashboardTarget() );
            expMuQuickTestFunctions::$service = 'expNoSuchService';
            $this->assertSame( array( 'result' => 43 ), expMuQuickTestFunctions::fetchDashboardTarget(), 'an older service: the media root' );
        }
        finally
        {
            expMuQuickTestFunctions::$service = 'expMultiuploadService';
        }
    }

    /** QL-08 */
    public function testDashboardsUseTheTarget()
    {
        $root = dirname( __DIR__, 4 );
        $files = array( $root . '/design/admin4/templates/content/dashboard.tpl' );
        if ( is_file( $root . '/extension/exp_adminui/design/adminui/templates/content/dashboard.tpl' ) )
            $files[] = $root . '/extension/exp_adminui/design/adminui/templates/content/dashboard.tpl';
        foreach ( $files as $file )
        {
            $tpl = (string)file_get_contents( $file );
            if ( strpos( $tpl, 'dashboard_target' ) === false && strpos( $file, 'exp_adminui' ) !== false )
                continue; // an exp_adminui release before 1.0.0.43
            $this->assertStringContainsString( "fetch( 'ezmultiupload', 'dashboard_target', hash() )", $tpl, $file );
            $this->assertStringContainsString( "ezmodule( 'ezmultiupload/upload' )", $tpl, $file );
            $this->assertStringContainsString( "href={concat( 'ezmultiupload/upload/', \$upload_node )|ezurl}", $tpl, $file );
            $this->assertStringContainsString( "concat( 'ezmultiupload/upload/', \$upload_node ) ) ) )", $tpl, $file );
            $this->assertStringNotContainsString( "ezmultiupload/upload/', \$media_node", $tpl, $file );
        }
    }
}
