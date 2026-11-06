<?php
/**
 * @description Import a URL map (CSV, JSON, .xlsx, .ods, Apache or nginx rules) into the URL translator, the wildcards and the redirects, as one batch that can be undone
 * @alias exp:url:import
 *
 * File containing the exp:url:import command.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Expurlimport extends ExpurlBase
{
    protected function name()
    {
        return 'exp:url:import';
    }

    protected function begin()
    {
        $this->script( array(
            'description' => "Import a URL map into the URL translator, the wildcards and the redirects\n" .
                             "Reads the file (CSV, JSON, .xlsx, .ods, Apache or nginx rules; the format from its name or content,\n" .
                             "or --format), plans every row (new, changed, unchanged, duplicate, conflict, broken target, unknown\n" .
                             "host, not importable) and imports the new ones, and the changed ones with --on-conflict=overwrite, as\n" .
                             "one batch: exp:url:undo <batch> takes it back. Bare paths belong to the siteaccess of -s (default the\n" .
                             "default siteaccess); a full address's host picks the siteaccess, or --host maps an old host to one.\n" .
                             "\n" .
                             "./console exp:url:import old-site.csv --dry-run --report=var/tmp/plan.csv\n" .
                             "./console exp:url:import old-site.htaccess -s site --on-conflict=overwrite\n" .
                             "./console exp:url:import map.xlsx --host=www.old-site.com=site --output-format=json",
        ) + self::SCRIPT );
        return (array)$this->startup( '[format:][language:][on-conflict:][dry-run][report:][host:*][login:][output-format:]', '[file]', array(
            'format' => 'import: the format of the file (csv, json, xlsx, ods, apache, nginx; default from its name or content); export: the format to write (default from --output, else csv); batches, undo, from-sitemap, from-404, suggest, test: text (default) or json (the machine output exp-url/1)',
            'language' => 'import: the language of rows without one (default the siteaccess\'s); export: only this language',
            'on-conflict' => 'What happens to an old address that has another entry already: skip (default) or overwrite',
            'dry-run' => 'Plan and check only: list what would be imported, write nothing',
            'report' => 'import: also write the plan, row by row, to this file (.json, or .csv); suggest, test: also write every row with its suggestions or its result to this file (.json, or .csv)',
            'host' => 'An old host and the siteaccess it stands for, old-site.com=site (a comma list or repeated): for full addresses of a host that is not this installation\'s',
            'login' => 'Run as this user (recorded with the import and in the audit)',
            'output-format' => 'import, export: what the command prints, text (default) or json (the machine output exp-url/1); --format is the file\'s format there; from-sitemap, from-404, suggest: the format of the file written (csv, json, xlsx, ods, apache, nginx; default from --output, else csv)',
        ) );
    }

    public function run()
    {
        $options = $this->begin();
        $format = $this->outputFormat( $options, 'output-format' );
        $file = isset( $options['arguments'][0] ) ? (string)$options['arguments'][0] : '';
        if ( $file === '' )
            $this->usage( 'name the file: exp:url:import <file>' );
        $fileFormat = isset( $options['format'] ) && $options['format'] ? strtolower( (string)$options['format'] ) : null;
        if ( $fileFormat !== null && !in_array( $fileFormat, \expUrlMapFormats::readable(), true ) )
            $this->usage( '--format must be one of ' . implode( ', ', \expUrlMapFormats::readable() ) . ", not $fileFormat" );
        $onConflict = isset( $options['on-conflict'] ) && $options['on-conflict'] ? (string)$options['on-conflict'] : \expUrlMapPlan::ON_CONFLICT_SKIP;
        if ( !in_array( $onConflict, array( \expUrlMapPlan::ON_CONFLICT_SKIP, \expUrlMapPlan::ON_CONFLICT_OVERWRITE ), true ) )
            $this->usage( "--on-conflict must be skip or overwrite, not $onConflict" );
        $hosts = array();
        foreach ( self::listOption( isset( $options['host'] ) ? $options['host'] : array() ) as $pair )
        {
            if ( !preg_match( '/^([^=\s]+)=([A-Za-z0-9_\-]+)$/', $pair, $m ) )
                $this->usage( "--host must be <host>=<siteaccess>, not $pair" );
            $hosts[strtolower( $m[1] )] = $m[2];
        }
        $dryRun = !empty( $options['dry-run'] );
        $userId = $this->login( $options );
        $this->database( $format );
        if ( !is_file( $file ) || !is_readable( $file ) )
            $this->finish( array( 'mode' => 'import', 'file' => $file, 'errors' => array( "the file $file cannot be read" ) ), $format );
        try
        {
            $detected = $fileFormat !== null ? $fileFormat : \expUrlMapFormats::detect( $file, (string)file_get_contents( $file, false, null, 0, 65536 ) );
            $rows = \expUrlMapFormats::readFile( $file, $detected );
        }
        catch ( \expUrlMapFormatException $e )
        {
            $this->finish( array( 'mode' => 'import', 'file' => $file, 'errors' => array( $e->getMessage() ) ), $format );
            return;
        }
        $planner = new \expUrlMapPlanner();
        $siteaccess = $this->givenSiteaccess();
        if ( $siteaccess !== '' && !in_array( $siteaccess, $planner->environment()->siteaccesses(), true ) )
            $this->usage( "-s names no siteaccess of this installation: $siteaccess" );
        $plan = $planner->plan( $rows, array( 'siteaccess' => $siteaccess, 'language' => isset( $options['language'] ) && $options['language'] ? (string)$options['language'] : '',
                                              'on_conflict' => $onConflict, 'hosts' => $hosts, 'source_name' => basename( $file ), 'format' => (string)$detected ) );
        $report = array( 'mode' => $dryRun ? 'dry-run' : 'import', 'file' => $file, 'format' => (string)$detected, 'dry_run' => $dryRun,
                         'options' => $plan->options(), 'counts' => $plan->counts(), 'result' => null,
                         'rows' => array_values( array_filter( $plan->items(), function ( $item ) {
                             return !in_array( $item['state'], array( \expUrlMapPlan::STATE_NEW, \expUrlMapPlan::STATE_UNCHANGED ), true );
                         } ) ), 'errors' => array(), 'warnings' => array() );
        if ( isset( $options['report'] ) && $options['report'] )
        {
            try
            {
                $plan->writeReport( (string)$options['report'] );
                $report['report'] = (string)$options['report'];
            }
            catch ( \expUrlMapFormatException $e )
            {
                $report['warnings'][] = $e->getMessage();
            }
        }
        try
        {
            $importer = new \expUrlMapImporter( $userId ? array( 'user_id' => $userId ) : array() );
            $report['result'] = $importer->apply( $plan, array( 'dry_run' => $dryRun ) );
            if ( $report['result']['counts']['failed'] )
                $report['errors'][] = $report['result']['counts']['failed'] . ' row(s) could not be written';
        }
        catch ( \RuntimeException $e )
        {
            $report['errors'][] = $e->getMessage();
        }
        $this->finish( $report, $format );
    }

    protected function lines( array $report )
    {
        return self::planLines( $report );
    }
}

}
