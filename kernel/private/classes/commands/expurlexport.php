<?php
/**
 * @description Export the URL translator, the wildcards and the redirects as a URL map (CSV, JSON, .xlsx, .ods, Apache or nginx rules)
 * @alias exp:url:export
 *
 * File containing the exp:url:export command.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Expurlexport extends ExpurlBase
{
    protected function name()
    {
        return 'exp:url:export';
    }

    protected function begin()
    {
        $this->script( array(
            'description' => "Export the URL translator, the wildcards and the redirects as a URL map\n" .
                             "Writes the global URL aliases (with --include-node-aliases also the custom aliases of nodes), the URL\n" .
                             "wildcards and the redirects of exp_url_redirect in any format exp:url:import reads (and .ods); a\n" .
                             "redirect's -s filter is the siteaccess it belongs to. Importing the file again gives the same entries.\n" .
                             "\n" .
                             "./console exp:url:export --output=var/tmp/url-map.xlsx\n" .
                             "./console exp:url:export --kind=wildcard,redirect --format=nginx\n" .
                             "./console exp:url:export --kind=redirect -s site --language=ger-DE --format=json",
        ) + self::SCRIPT );
        return (array)$this->startup( '[format:][kind:*][language:][output:][include-node-aliases][output-format:]', '', array(
            'format' => 'import: the format of the file (csv, json, xlsx, ods, apache, nginx; default from its name or content); export: the format to write (default from --output, else csv); batches, undo: text (default) or json (the machine output exp-url/1)',
            'kind' => 'Only these kinds: alias (the URL translator), wildcard, redirect (a comma list or repeated; default all)',
            'language' => 'import: the language of rows without one (default the siteaccess\'s); export: only this language',
            'output' => 'The file to write (default the standard output; needed for xlsx and ods)',
            'include-node-aliases' => 'Also the custom URL aliases of nodes (target node:<id>), not only the URL translator\'s',
            'output-format' => 'import, export: what the command prints, text (default) or json (the machine output exp-url/1); --format is the file\'s format there',
        ) );
    }

    public function run()
    {
        $options = $this->begin();
        $printFormat = $this->outputFormat( $options, 'output-format' );
        $output = isset( $options['output'] ) && $options['output'] ? (string)$options['output'] : '';
        $format = isset( $options['format'] ) && $options['format'] ? strtolower( (string)$options['format'] ) : ( $output !== '' ? (string)\expUrlMapFormats::detect( $output ) : 'csv' );
        if ( $format === '' )
            $format = 'csv';
        if ( !in_array( $format, \expUrlMapFormats::writable(), true ) )
            $this->usage( '--format must be one of ' . implode( ', ', \expUrlMapFormats::writable() ) . ", not $format" );
        if ( $output === '' && in_array( $format, array( 'xlsx', 'ods' ), true ) )
            $this->usage( "--format=$format needs --output=<file>" );
        $kinds = self::listOption( isset( $options['kind'] ) ? $options['kind'] : array() ) ?: \expUrlMapExporter::KINDS;
        foreach ( $kinds as $kind )
            if ( !in_array( $kind, \expUrlMapExporter::KINDS, true ) )
                $this->usage( '--kind must be alias, wildcard or redirect, not ' . $kind );
        $this->database( $printFormat );
        $rows = ( new \expUrlMapExporter() )->export( array( 'kinds' => $kinds, 'siteaccess' => $this->givenSiteaccess(),
                                                             'language' => isset( $options['language'] ) && $options['language'] ? (string)$options['language'] : '',
                                                             'include_node_aliases' => !empty( $options['include-node-aliases'] ) ) );
        $report = array( 'mode' => 'export', 'format' => $format, 'kinds' => $kinds, 'rows' => count( $rows ), 'output' => $output, 'errors' => array(), 'warnings' => array() );
        try
        {
            if ( $output !== '' )
                \expUrlMapFormats::writeFile( $output, $rows, $format );
            else
            {
                // the map itself is the output: no report around it
                $this->cli()->output( \expUrlMapFormats::writer( $format )->writeString( $rows ), false );
                $this->shutdown( \expUpgrade::EXIT_OK );
                return;
            }
        }
        catch ( \expUrlMapFormatException $e )
        {
            $report['errors'][] = $e->getMessage();
        }
        $this->finish( $report, $printFormat );
    }

    protected function lines( array $report )
    {
        return array( sprintf( 'URL map export: %d rows (%s) as %s to %s', $report['rows'], implode( ', ', $report['kinds'] ), $report['format'], $report['output'] ) );
    }
}

}
