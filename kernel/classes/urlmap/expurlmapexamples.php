<?php
/**
 * File containing the expUrlMapExamples class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The example URL maps the administration shows on content/urlimport/(source)/examples: one or more per format, to
 * look at, copy, download or open in the import. The files are the ones of the documentation (doc/eng-us/examples/
 * url-maps, the German ones in doc/ger-de/beispiele/url-zuordnungen), so the help pages and the administration show
 * the same files; each is read with its format's reader, so an example that stops being readable fails its test.
 *
 * @since 6.0.15
 */
class expUrlMapExamples
{
    /** The directory of the English files, below the installation */
    const DIRECTORY = 'doc/eng-us/examples/url-maps';

    /** The directory of the German files, and the German name of each English one */
    const DIRECTORY_GERMAN = 'doc/ger-de/beispiele/url-zuordnungen';
    const GERMAN_FILES = array( 'old-site.csv' => 'alte-site.csv', 'old-site.json' => 'alte-site.json', 'old-site.htaccess' => 'alte-site.htaccess',
                                'old-site.nginx' => 'alte-site.nginx', 'query-and-temporary.csv' => 'query-und-voruebergehend.csv',
                                'wildcards.csv' => 'wildcards.csv', 'old-site.xlsx' => 'alte-site.xlsx', 'old-site.ods' => 'alte-site.ods' );

    /** The largest example shown as text on the page */
    const MAX_SHOWN = 8192;

    /**
     * The examples in the order of the page: id => file, format and what it shows (English; the template translates
     * it in the context design/admin/content/urlimport).
     *
     * @return array[]
     */
    static function definitions()
    {
        return array(
            'csv' => array( 'file' => 'old-site.csv', 'format' => expUrlMapFormats::CSV,
                            'title' => 'Single pages',
                            'description' => 'Permanent redirects of single pages, a target by node ID (node:163) that follows the page when it moves, and an alias that shows the page at the old address. The first line names the columns; only old_url and new_url are needed. A .tsv file is read the same way, with tabs.' ),
            'csv-wildcards' => array( 'file' => 'wildcards.csv', 'format' => expUrlMapFormats::CSV,
                                      'title' => 'Wildcards',
                                      'description' => 'A * matches any part of an address and {1} puts it into the new one: a section that moved, a * in the middle of an address, a temporary wildcard and a direct one (no redirect).' ),
            'csv-query' => array( 'file' => 'query-and-temporary.csv', 'format' => expUrlMapFormats::CSV,
                                  'title' => 'Queries and temporary redirects',
                                  'description' => 'Old addresses with a query (/index.php?id=12), a 308, and temporary redirects by kind (redirect-temporary) and by status (307).' ),
            'json' => array( 'file' => 'old-site.json', 'format' => expUrlMapFormats::JSON,
                             'title' => 'JSON',
                             'description' => 'A list of objects with the same keys as the CSV columns; the first object may name the schema exp-url-map/1. What the export writes and other programs make most easily.' ),
            'xlsx' => array( 'file' => 'old-site.xlsx', 'format' => expUrlMapFormats::XLSX,
                             'title' => 'Excel',
                             'description' => 'The single pages as an Excel workbook: the first sheet, the column names in the first row. Open it, change the addresses and save it as .xlsx.' ),
            'ods' => array( 'file' => 'old-site.ods', 'format' => expUrlMapFormats::ODS,
                            'title' => 'LibreOffice',
                            'description' => 'The same as an OpenDocument spreadsheet, for LibreOffice and OpenOffice.' ),
            'apache' => array( 'file' => 'old-site.htaccess', 'format' => expUrlMapFormats::APACHE,
                               'title' => 'Apache rewrite rules',
                               'description' => 'The .htaccess of an old site as it is: RewriteRule, RewriteCond for a query, Redirect of a whole section (the page and everything below it) and RedirectMatch as a wildcard.' ),
            'nginx' => array( 'file' => 'old-site.nginx', 'format' => expUrlMapFormats::NGINX,
                              'title' => 'nginx rewrite rules',
                              'description' => 'The server block of an old site: location = with return, if ($args) for a query, rewrite as a wildcard and a map of single pages.' ),
        );
    }

    /**
     * Whether the German files are the ones to show: the locale of the administration is German and the file exists.
     *
     * @param string $locale e.g. ger-DE
     * @return bool
     */
    static function useGerman( $locale )
    {
        return strtolower( substr( (string)$locale, 0, 3 ) ) === 'ger';
    }

    /**
     * The path of an example, or '' for an id that is not one or a file that is missing.
     *
     * @param string $id
     * @param string $locale the German file when there is one and the locale is German
     * @param string|null $root the installation (the current directory)
     * @return string
     */
    static function path( $id, $locale = '', $root = null )
    {
        $definitions = self::definitions();
        if ( !is_string( $id ) || !isset( $definitions[$id] ) )
            return '';
        $root = rtrim( $root === null ? eZSys::rootDir() : (string)$root, '/' );
        $file = $definitions[$id]['file'];
        if ( self::useGerman( $locale ) && isset( self::GERMAN_FILES[$file] ) )
        {
            $german = $root . '/' . self::DIRECTORY_GERMAN . '/' . self::GERMAN_FILES[$file];
            if ( is_file( $german ) )
                return $german;
        }
        $path = $root . '/' . self::DIRECTORY . '/' . $file;
        return is_file( $path ) ? $path : '';
    }

    /**
     * Whether a format is text, so its example can be shown on the page and pasted into the import.
     *
     * @param string $format
     * @return bool
     */
    static function isText( $format )
    {
        return !in_array( $format, array( expUrlMapFormats::XLSX, expUrlMapFormats::ODS ), true );
    }

    /**
     * The examples for the page: id, title, description, format and its label, the extensions the format is read
     * from, the file name and size, the rows the import makes of it, and the text (text formats up to MAX_SHOWN bytes,
     * else ''). Missing files are left out.
     *
     * @param string $locale
     * @param string|null $root
     * @return array[]
     */
    static function all( $locale = '', $root = null )
    {
        $formats = expUrlMapFormats::definitions();
        $examples = array();
        foreach ( self::definitions() as $id => $definition )
        {
            $path = self::path( $id, $locale, $root );
            if ( $path === '' )
                continue;
            $format = $definition['format'];
            try
            {
                $rows = count( expUrlMapFormats::readFile( $path, $format ) );
            }
            catch ( expUrlMapFormatException $e )
            {
                eZDebug::writeWarning( "The example URL map $path cannot be read: " . $e->getMessage(), __METHOD__ );
                continue;
            }
            $size = (int)filesize( $path );
            $examples[] = array( 'id' => $id, 'title' => $definition['title'], 'description' => $definition['description'],
                                 'format' => $format, 'format_label' => $formats[$format]['label'],
                                 'extensions' => $formats[$format]['extensions'], 'file' => basename( $path ), 'size' => $size,
                                 'rows' => $rows, 'is_text' => self::isText( $format ),
                                 'text' => self::isText( $format ) && $size <= self::MAX_SHOWN ? (string)file_get_contents( $path ) : '' );
        }
        return $examples;
    }

    /**
     * The text of a text example, to prefill the import's form with, or null.
     *
     * @param string $id
     * @param string $locale
     * @param string|null $root
     * @return array|null array( 'format' => ..., 'text' => ... )
     */
    static function forImport( $id, $locale = '', $root = null )
    {
        $path = self::path( $id, $locale, $root );
        if ( $path === '' )
            return null;
        $definitions = self::definitions();
        $format = $definitions[$id]['format'];
        if ( !self::isText( $format ) )
            return null;
        return array( 'format' => $format, 'text' => (string)file_get_contents( $path ) );
    }
}
