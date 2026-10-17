<?php
/**
 * Template defects of the admin4 review of 15.10.2026 that showed as debug warnings or broken output:
 *
 *  RW-01 - the admin4 left-menu, translations and relations templates do not {def} names the including
 *          page already defines (an include shares the page's variables: "Variable ... is already defined")
 *  RW-02 - admininterface.ini declares every template list popup_export_items.tpl reads (no "No such variable")
 *  RW-04 - every {default} of the admin4 templates is closed
 *  RW-05 - the object relation list editor passes content/object only parameters it knows
 *  RW-07 - the toolbar editor draws a preview image only for tools that have one
 *  RW-08 - the dashboard says so when nothing was published in 14 days
 *  RW-09 - the ger-DE strings admin4 shows are not left unfinished
 *  RW-10 - the class view of admin and admin4 links to the background process monitor only where its module exists
 *  RW-12 - ezoe's ger-DE file table headings (Name, Size, Type) are not left unfinished
 *
 * Run: php vendor/bin/phpunit tests/tests/design/AdminReviewTemplateWarningsTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class AdminReviewTemplateWarningsTest extends PHPUnit\Framework\TestCase
{
    private static function file( $path )
    {
        $file = dirname( __DIR__, 3 ) . '/' . $path;
        self::assertFileExists( $file );
        return (string)file_get_contents( $file );
    }

    /** The names a template defines with {def ...} (every "$name =" inside a def tag). */
    private static function defined( $tpl )
    {
        $names = array();
        if ( preg_match_all( '/\{def\b(.*?)\}/s', $tpl, $tags ) )
            foreach ( $tags[1] as $body )
                if ( preg_match_all( '/\$([a-z_0-9]+)\s*=/i', $body, $m ) )
                    $names = array_merge( $names, $m[1] );
        return $names;
    }

    /** RW-01 */
    public function testIncludedTemplatesDoNotDefineNamesOfTheirIncluders()
    {
        $forbidden = array(
            'parts/ini_menu.tpl'  => array( 'section', 'groups', 'check' ),
            'translations.tpl'    => array( 'a4_main_language_name' ), // node/view/a4_full_body.tpl defines it
            'relations.tpl'       => array( 'offset' ),                // the node view defines it
        );
        foreach ( $forbidden as $path => $names )
        {
            $defined = self::defined( self::file( "design/admin4/templates/$path" ) );
            foreach ( $names as $name )
                $this->assertNotContains( $name, $defined, "$path must not {def} \$$name" );
        }
    }

    /** RW-02 */
    public function testExportMenuTemplateListsAreDeclaredInAdminInterfaceIni()
    {
        $ini = self::file( 'settings/admininterface.ini' );
        foreach ( array( 'design/admin', 'design/admin4' ) as $design )
        {
            $tpl = self::file( "$design/templates/popupmenu/popup_export_items.tpl" );
            $this->assertGreaterThan( 0, preg_match_all( "/ezini\( 'AdditionalMenuSettings', '([A-Za-z]+)', 'admininterface.ini' \)/", $tpl, $m ), $design );
            foreach ( $m[1] as $variable )
                $this->assertMatchesRegularExpression( '/^' . preg_quote( $variable, '/' ) . '\[\]\s*$/m', $ini, "$design reads $variable" );
        }
    }

    /** RW-04 */
    public function testEveryDefaultBlockIsClosed()
    {
        $root = dirname( __DIR__, 3 ) . '/design/admin4/templates';
        $unclosed = array();
        foreach ( new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $root, FilesystemIterator::SKIP_DOTS ) ) as $file )
        {
            if ( $file->getExtension() !== 'tpl' )
                continue;
            $tpl = (string)file_get_contents( $file->getPathname() );
            $tpl = preg_replace( '/\{literal\}.*?\{\/literal\}/s', '', $tpl );
            $tpl = preg_replace( '/\{\*.*?\*\}/s', '', $tpl );
            if ( preg_match_all( '/\{default\b/', $tpl ) !== preg_match_all( '/\{\/default\}/', $tpl ) )
                $unclosed[] = substr( $file->getPathname(), strlen( $root ) + 1 );
        }
        $this->assertSame( array(), $unclosed, '{default} without {/default}' );
    }

    /** RW-05 */
    public function testRelationListEditorPassesOnlyKnownParametersToContentObject()
    {
        $tpl = self::file( 'design/admin4/templates/content/datatype/edit/ezobjectrelationlist.tpl' );
        $this->assertDoesNotMatchRegularExpression( '/fetch\(\s*content,\s*object,\s*hash\([^)]*object_version/', $tpl );
    }

    /** RW-07 */
    public function testToolbarEditorDrawsPreviewImagesOnlyForToolsThatHaveOne()
    {
        $tpl = self::file( 'design/admin4/templates/visual/toolbar.tpl' );
        $this->assertSame( 2, preg_match_all( '/\{if \$toolbar_preview_(plain|line)\|contains\( \$Tool\.name \)\}<img /', $tpl ) );
        foreach ( array( 'plain' => '.png', 'line' => '_line.png' ) as $kind => $suffix )
        {
            preg_match( '/\$toolbar_preview_' . $kind . '\s*=\s*array\(([^)]*)\)/', $tpl, $m );
            preg_match_all( "/'([a-z_]+)'/", $m[1], $names );
            $this->assertNotEmpty( $names[1], $kind );
            foreach ( $names[1] as $name )
                $this->assertFileExists( dirname( __DIR__, 3 ) . '/design/standard/images/toolbar/' . $name . $suffix );
        }
    }

    /** RW-08 */
    public function testDashboardChartSaysWhenNothingWasPublished()
    {
        $tpl = self::file( 'design/admin4/templates/content/dashboard.tpl' );
        $this->assertStringContainsString( "{set \$day_total = sum( \$day_total, \$day_count )}", $tpl );
        $this->assertMatchesRegularExpression( '/\{if \$day_total\|eq\( 0 \)\}\s*\{\*[^*]*\*\}\s*<p class="a4-empty">/', $tpl );
        $this->assertStringContainsString( 'Nothing was published in the last 14 days.', self::file( 'share/translations/ger-DE/translation.ts' ) );
    }

    /** RW-09 */
    public function testTheGermanStringsAdmin4ShowAreFinished()
    {
        $ts = self::file( 'share/translations/ger-DE/translation.ts' );
        foreach ( array( 'Add', 'Required', 'Name', 'Type', 'Section', 'prev', 'next', 'Path String', 'Comment', 'E-mail',
                         'Add existing objects', 'Html5 Video', 'Html5 Audio', 'Global (override)' ) as $source )
        {
            $this->assertDoesNotMatchRegularExpression(
                '#<source>' . preg_quote( $source, '#' ) . '</source>\s*<translation type="unfinished">#',
                $ts, "'$source' is unfinished" );
        }
    }

    /** The translation of $source in $context, null where the catalogue lacks it, '' where it is unfinished */
    private static function translation( $path, $context, $source )
    {
        $xml = simplexml_load_string( self::file( $path ) );
        self::assertNotFalse( $xml, "$path is valid XML" );
        foreach ( $xml->context as $ctx )
        {
            if ( (string)$ctx->name !== $context )
                continue;
            foreach ( $ctx->message as $message )
                if ( (string)$message->source === $source )
                    return (string)$message->translation['type'] === 'unfinished' ? '' : (string)$message->translation;
        }
        return null;
    }

    /** RW-10 */
    public function testClassViewLinksToTheScriptMonitorOnlyWhereItsModuleExists()
    {
        foreach ( array( 'design/admin', 'design/admin4' ) as $design )
        {
            $tpl = self::file( "$design/templates/class/view.tpl" );
            $this->assertSame( 1, preg_match_all( '#scriptmonitor/view/#', $tpl ), $design );
            $this->assertMatchesRegularExpression( "#\{if ezmodule\( 'scriptmonitor/view' \)\}<a href=\{concat\( 'scriptmonitor/view/'#", $tpl, $design );
        }
    }

    /** RW-12 */
    public function testEzoeGermanFileTableHeadingsAreFinished()
    {
        $ts = 'extension/ezoe/translations/ger-DE/translation.ts';
        foreach ( array( 'Name' => 'Name', 'Size' => 'Größe', 'Type' => 'Art' ) as $source => $german )
            $this->assertSame( $german, self::translation( $ts, 'design/admin/content/edit', $source ), $source );
    }
}
