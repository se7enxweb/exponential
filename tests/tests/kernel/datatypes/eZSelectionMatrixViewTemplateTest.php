<?php
/**
 * The view templates of the selection and matrix datatypes write their values without stray line breaks
 * (design/standard/templates/content/datatype/view/ezselection.tpl and ezmatrix.tpl), interpreted and compiled.
 *
 *  SM-01 - several selected options: in the order of the class, one <br /> between two of them, none before or after
 *  SM-02 - one selected option: its name and nothing around it; none selected: nothing
 *  SM-03 - option names are escaped (wash xhtml); an id that is not an option of the class is not written
 *  SM-04 - the matrix is the table and nothing else: a header row with the column names, one row per matrix row
 *          with alternating bglight/bgdark, a cell per column, no <br /> anywhere, cells escaped
 *  SM-05 - a matrix without rows is the header row only
 *
 * The templates are rendered by a bare eZTemplate (as the other template tests do) with the attribute as an array,
 * as the template reads it through .content and .class_content. No database.
 *
 * Portions derived from LoveStack (mugoweb/ezpublish-legacy, commits eee7f69fc3, 67eec8f972 and 492a946963),
 * Copyright (C) Mugo Web, GPL v2
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/datatypes/eZSelectionMatrixViewTemplateTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group kernel
 * @group eztemplate
 */

class eZSelectionMatrixViewTemplateTest extends PHPUnit\Framework\TestCase
{
    const SELECTION = 'design/standard/templates/content/datatype/view/ezselection.tpl';

    const MATRIX = 'design/standard/templates/content/datatype/view/ezmatrix.tpl';

    /** @var string private directory of the compiled templates */
    private static $dir;

    private $savedGlobals = array();

    public static function setUpBeforeClass(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        self::$dir = 'var/tmp/phpunit-selection-matrix-view-' . getmypid() . '-' . substr( md5( uniqid( '', true ) ), 0, 8 );
        mkdir( self::$dir . '/compiled', 0777, true );
    }

    public static function tearDownAfterClass(): void
    {
        self::removeTree( self::$dir );
    }

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        foreach ( array( 'eZTemplateCompilerSettings', 'eZSiteBasics' ) as $name )
            $this->savedGlobals[$name] = array_key_exists( $name, $GLOBALS ) ? array( $GLOBALS[$name] ) : null;
    }

    protected function tearDown(): void
    {
        foreach ( $this->savedGlobals as $name => $saved )
        {
            if ( $saved === null )
                unset( $GLOBALS[$name] );
            else
                $GLOBALS[$name] = $saved[0];
        }
    }

    private static function removeTree( $path )
    {
        if ( !$path || !file_exists( $path ) )
            return;
        if ( is_dir( $path ) && !is_link( $path ) )
        {
            foreach ( scandir( $path ) as $entry )
            {
                if ( $entry !== '.' && $entry !== '..' )
                    self::removeTree( $path . '/' . $entry );
            }
            rmdir( $path );
        }
        else
            unlink( $path );
    }

    /** The template rendered with $attribute, interpreted and compiled: array( mode => output ); fails on a notice */
    private function render( $file, array $attribute )
    {
        $outputs = array();
        foreach ( array( 'interpreted' => false, 'compiled' => true ) as $mode => $compiled )
        {
            if ( $compiled )
            {
                unset( $GLOBALS['eZSiteBasics'] );
                $GLOBALS['eZTemplateCompilerSettings']['compile'] = true;
                $GLOBALS['eZTemplateCompilerSettings']['compilation-directory'] = self::$dir . '/compiled';
            }
            else
            {
                $GLOBALS['eZTemplateCompilerSettings']['compile'] = false;
                $GLOBALS['eZSiteBasics']['no-cache-adviced'] = true;
            }
            $notices = array();
            set_error_handler( function ( $no, $str, $f, $l ) use ( &$notices )
            {
                $notices[] = $str . ' (' . basename( $f ) . ':' . $l . ')';
                return true;
            } );
            try
            {
                $tpl = new eZTemplate();
                $tpl->autoload();
                $tpl->setVariable( 'attribute', $attribute );
                $outputs[$mode] = $tpl->fetch( $file );
            }
            finally
            {
                restore_error_handler();
            }
            $this->assertSame( array(), $notices, "$mode: PHP notices" );
        }
        $this->assertSame( $outputs['interpreted'], $outputs['compiled'], 'the same output interpreted and compiled' );
        return $outputs['compiled'];
    }

    private function selection( array $selected )
    {
        $options = array( array( 'id' => 0, 'name' => 'First' ), array( 'id' => 1, 'name' => 'Second' ),
                          array( 'id' => 2, 'name' => 'Third & <last>' ) );
        return trim( $this->render( self::SELECTION, array( 'content' => $selected, 'class_content' => array( 'options' => $options ) ) ) );
    }

    /** SM-01 */
    public function testSeveralOptionsHaveOneLineBreakBetweenTwo()
    {
        $this->assertSame( 'First<br />Third &amp; &lt;last&gt;', $this->selection( array( 2, 0 ) ), 'in the order of the class' );
        $this->assertSame( 'First<br />Second<br />Third &amp; &lt;last&gt;', $this->selection( array( 0, 1, 2 ) ) );
    }

    /** SM-02 */
    public function testOneOrNoOption()
    {
        $this->assertSame( 'Second', $this->selection( array( 1 ) ) );
        $this->assertSame( '', $this->selection( array() ) );
        $raw = $this->render( self::SELECTION, array( 'content' => array( 1 ), 'class_content' => array( 'options' => array( array( 'id' => 1, 'name' => 'Second' ) ) ) ) );
        $this->assertStringNotContainsString( '<br', $raw );
        $this->assertSame( 1, substr_count( $raw, 'Second' ) );
    }

    /** SM-03 */
    public function testUnknownIdsAreNotWritten()
    {
        $this->assertSame( 'Second', $this->selection( array( 1, 7 ) ) );
        $this->assertSame( '', $this->selection( array( 9 ) ) );
    }

    private function matrix( array $columns, array $rows )
    {
        $columnList = array();
        foreach ( $columns as $name )
            $columnList[] = array( 'name' => $name );
        $rowList = array();
        foreach ( $rows as $cells )
            $rowList[] = array( 'columns' => $cells );
        return $this->render( self::MATRIX, array( 'content' => array( 'columns' => array( 'sequential' => $columnList ),
                                                                       'rows' => array( 'sequential' => $rowList ) ) ) );
    }

    /** The table without white space between the tags */
    private static function compact( $html )
    {
        return preg_replace( '/>\s+</', '><', trim( $html ) );
    }

    /** SM-04 */
    public function testTheMatrixIsTheTableAndNothingElse()
    {
        $html = $this->matrix( array( 'Name', 'Value' ), array( array( 'a', 'b' ), array( 'c', '<d>' ), array( 'e', 'f' ) ) );
        $this->assertStringNotContainsString( '<br', $html );
        $this->assertSame( '<table class="list" cellspacing="0"><tr><th>Name</th><th>Value</th></tr>'
                         . '<tr class="bglight"><td>a</td><td>b</td></tr>'
                         . '<tr class="bgdark"><td>c</td><td>&lt;d&gt;</td></tr>'
                         . '<tr class="bglight"><td>e</td><td>f</td></tr></table>', self::compact( $html ) );
    }

    /** SM-05 */
    public function testAMatrixWithoutRows()
    {
        $html = $this->matrix( array( 'Only' ), array() );
        $this->assertSame( '<table class="list" cellspacing="0"><tr><th>Only</th></tr></table>', self::compact( $html ) );
    }
}
