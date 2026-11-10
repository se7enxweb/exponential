<?php
/**
 * The example URL maps of the administration's tab Formats and examples (expUrlMapExamples), without a database.
 *
 *  UE-01 every format that can be read has an example, every example file exists in English and German and is read
 *        by its format's reader with the rows its README says, the German one with as many rows as the English one
 *  UE-02 the paths: an unknown id or a path outside the list is no example, a German locale takes the German file and
 *        falls back to the English one when there is none
 *  UE-03 what the page gets: text formats with their text, spreadsheets without, Open in the import only for text,
 *        with the example's format
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expUrlMapExamplesTest extends PHPUnit\Framework\TestCase
{
    /** @return string the installation */
    private static function root()
    {
        return dirname( __DIR__, 5 );
    }

    /** UE-01 */
    public function testEveryFormatHasAReadableExample()
    {
        $root = self::root();
        $formats = array();
        foreach ( expUrlMapExamples::definitions() as $id => $definition )
            $formats[$definition['format']] = true;
        foreach ( expUrlMapFormats::readable() as $format )
            $this->assertArrayHasKey( $format, $formats, "no example of the format $format" );

        // the rows of the README's table (an Apache Redirect gives two rows); the spreadsheets are old-site.csv
        $rows = array( 'csv' => 8, 'csv-wildcards' => 4, 'csv-query' => 5, 'json' => 7, 'xlsx' => 8, 'ods' => 8, 'apache' => 7, 'nginx' => 7 );
        $this->assertSame( array_keys( $rows ), array_keys( expUrlMapExamples::definitions() ) );
        foreach ( expUrlMapExamples::all( 'eng-US', $root ) as $example )
        {
            $this->assertSame( $rows[$example['id']], $example['rows'], $example['file'] );
            $this->assertGreaterThan( 0, $example['size'] );
        }
        $this->assertCount( 8, expUrlMapExamples::all( 'eng-US', $root ) );

        foreach ( array_keys( $rows ) as $id )
        {
            $english = expUrlMapExamples::path( $id, 'eng-US', $root );
            $german = expUrlMapExamples::path( $id, 'ger-DE', $root );
            $this->assertNotSame( '', $english, $id );
            $this->assertStringContainsString( expUrlMapExamples::DIRECTORY_GERMAN, $german, "$id has no German file" );
            $format = expUrlMapExamples::definitions()[$id]['format'];
            $this->assertCount( count( expUrlMapFormats::readFile( $english, $format ) ), expUrlMapFormats::readFile( $german, $format ), $id );
        }
    }

    /** UE-02 */
    public function testPaths()
    {
        $root = self::root();
        foreach ( array( '', 'nothing', '../../settings/site.ini', 'csv/../json', null, 3 ) as $id )
            $this->assertSame( '', expUrlMapExamples::path( $id, 'eng-US', $root ) );
        $this->assertStringEndsWith( '/' . expUrlMapExamples::DIRECTORY . '/old-site.csv', expUrlMapExamples::path( 'csv', 'eng-US', $root ) );
        $this->assertStringEndsWith( '/alte-site.csv', expUrlMapExamples::path( 'csv', 'ger-DE', $root ) );
        $this->assertTrue( expUrlMapExamples::useGerman( 'ger-CH' ) );
        $this->assertFalse( expUrlMapExamples::useGerman( 'eng-GB' ) );

        // a German locale without a German file takes the English one
        $dir = $root . '/var/tmp/urlmap-examples-' . bin2hex( random_bytes( 6 ) );
        mkdir( $dir . '/' . expUrlMapExamples::DIRECTORY, 0755, true );
        copy( $root . '/' . expUrlMapExamples::DIRECTORY . '/wildcards.csv', $dir . '/' . expUrlMapExamples::DIRECTORY . '/wildcards.csv' );
        $this->assertSame( $dir . '/' . expUrlMapExamples::DIRECTORY . '/wildcards.csv', expUrlMapExamples::path( 'csv-wildcards', 'ger-DE', $dir ) );
        $this->assertSame( '', expUrlMapExamples::path( 'csv', 'ger-DE', $dir ) );
        $this->assertCount( 1, expUrlMapExamples::all( 'ger-DE', $dir ) );
        unlink( $dir . '/' . expUrlMapExamples::DIRECTORY . '/wildcards.csv' );
        foreach ( array( 'doc/eng-us/examples/url-maps', 'doc/eng-us/examples', 'doc/eng-us', 'doc', '' ) as $sub )
            rmdir( rtrim( $dir . '/' . $sub, '/' ) );
    }

    /** UE-03 */
    public function testWhatThePageGets()
    {
        $root = self::root();
        foreach ( expUrlMapExamples::all( 'eng-US', $root ) as $example )
        {
            $text = in_array( $example['format'], array( expUrlMapFormats::XLSX, expUrlMapFormats::ODS ), true ) === false;
            $this->assertSame( $text, $example['is_text'], $example['id'] );
            $this->assertSame( $text, $example['text'] !== '', $example['id'] );
            $import = expUrlMapExamples::forImport( $example['id'], 'eng-US', $root );
            if ( $text )
            {
                $this->assertSame( $example['format'], $import['format'] );
                $this->assertSame( $example['text'], $import['text'] );
            }
            else
                $this->assertNull( $import );
        }
        $this->assertNull( expUrlMapExamples::forImport( 'nothing', 'eng-US', $root ) );
        $this->assertStringContainsString( '/old-recipes/*', expUrlMapExamples::forImport( 'csv-wildcards', 'eng-US', $root )['text'] );
    }
}
