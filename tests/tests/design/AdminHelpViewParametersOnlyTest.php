<?php
/**
 * The help center takes its options as view parameters (/(lang)/<key>, /(format)/json), never as GET query
 * parameters (doc/eng-us/features/6.0/help-system.md). DB-less: reads the templates, the script and the views.
 *
 *  HV-01  no help template of any admin design, and no help header template, builds ?lang=, ?format=, ?q=, ?p= or ?tracker=
 *  HV-02  the help script builds no query string for the search (no "?q=", "format=json", "&limit=")
 *  HV-03  the help views read no $_GET and module.php declares lang, format and tracker as view parameters
 *  HV-04  every help search form is a POST (the view redirects it to the canonical address), never a GET
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class AdminHelpViewParametersOnlyTest extends PHPUnit\Framework\TestCase
{
    const OPTIONS = '/\?(?:lang|format|q|p|tracker)=|[?&](?:lang|format)=/';

    protected static function root()
    {
        return dirname( __DIR__, 3 );
    }

    /** @return string[] the help templates of every design, and the header templates that hold a help search */
    protected static function templates()
    {
        $root = self::root();
        $files = array();
        foreach ( glob( $root . '/design/*/templates/exphelp', GLOB_ONLYDIR ) as $dir )
        {
            $it = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $dir, FilesystemIterator::SKIP_DOTS ) );
            foreach ( $it as $file )
                if ( substr( $file->getFilename(), -4 ) === '.tpl' )
                    $files[] = $file->getPathname();
        }
        foreach ( array( 'design/admin/templates/page_header.tpl', 'design/admin4/templates/parts/a4_header_help.tpl' ) as $path )
            if ( is_file( $root . '/' . $path ) )
                $files[] = $root . '/' . $path;
        sort( $files );
        return $files;
    }

    /** HV-01 */
    public function testHelpTemplatesBuildNoQueryOptions()
    {
        $files = self::templates();
        $this->assertGreaterThan( 20, count( $files ), 'the help templates are found' );
        $hits = array();
        foreach ( $files as $file )
        {
            foreach ( file( $file ) as $n => $line )
                if ( preg_match( self::OPTIONS, $line ) )
                    $hits[] = substr( $file, strlen( self::root() ) + 1 ) . ':' . ( $n + 1 ) . ': ' . trim( $line );
        }
        $this->assertSame( array(), $hits, 'use view parameters such as /(lang)/<key> and /(format)/json' );
    }

    /** HV-02 */
    public function testHelpScriptBuildsNoQueryString()
    {
        foreach ( glob( self::root() . '/design/*/javascript/exphelp.js' ) as $file )
        {
            $source = file_get_contents( $file );
            foreach ( array( "'?'", "'q='", "'lang='", "'format=json'", "'&limit='", "'limit='", '?lang=', '?format=' ) as $needle )
                $this->assertStringNotContainsString( $needle, $source, basename( dirname( $file, 2 ) ) . ': ' . $needle );
            $this->assertStringContainsString( "'/(format)/json'", $source );
        }
    }

    /** HV-03 */
    public function testHelpViewsReadNoGetParameters()
    {
        $root = self::root();
        foreach ( array_merge( glob( $root . '/kernel/private/classes/views/exphelp/*.php' ), array( $root . '/kernel/classes/help/exphelp.php' ) ) as $file )
            $this->assertStringNotContainsString( '$_GET', file_get_contents( $file ), basename( $file ) );

        $Module = null;
        $ViewList = array();
        $FunctionList = array();
        include $root . '/kernel/exphelp/module.php';
        $this->assertSame( array( 'lang' => 'Language' ), $ViewList['viewer']['unordered_params'] );
        $this->assertSame( array( 'format' => 'Format' ), $ViewList['manual']['unordered_params'] );
        $this->assertSame( array( 'tracker' => 'Tracker' ), $ViewList['report']['unordered_params'] );
        $this->assertSame( 'Format', $ViewList['search']['unordered_params']['format'] );
    }

    /** HV-04 */
    public function testHelpSearchFormsArePosted()
    {
        $hits = array();
        foreach ( self::templates() as $file )
        {
            if ( preg_match_all( '/<form\b[^>]*>/', file_get_contents( $file ), $m ) )
                foreach ( $m[0] as $form )
                    if ( stripos( $form, 'method="get"' ) !== false )
                        $hits[] = substr( $file, strlen( self::root() ) + 1 ) . ': ' . $form;
        }
        $this->assertSame( array(), $hits );
    }
}
