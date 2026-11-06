<?php
/**
 * @description List the URL map imports: who imported which file when, the counts and whether it was undone
 * @alias exp:url:batches
 *
 * File containing the exp:url:batches command.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Expurlbatches extends ExpurlBase
{
    protected function name()
    {
        return 'exp:url:batches';
    }

    protected function begin()
    {
        $this->script( array(
            'description' => "List the URL map imports\n" .
                             "The batches of exp:url:import (and the administration's import), newest first: the file, who and when,\n" .
                             "the counts, and whether it was undone (exp:url:undo).\n" .
                             "\n" .
                             "./console exp:url:batches\n" .
                             "./console exp:url:batches --limit=100 --format=json",
        ) + self::SCRIPT );
        return (array)$this->startup( '[limit:][format:]', '', array(
            'limit' => 'How many of the last imports to list (default 20)',
            'format' => 'import: the format of the file (csv, json, xlsx, ods, apache, nginx; default from its name or content); export: the format to write (default from --output, else csv); batches, undo: text (default) or json (the machine output exp-url/1)',
        ) );
    }

    public function run()
    {
        $options = $this->begin();
        $format = $this->outputFormat( $options );
        $limit = isset( $options['limit'] ) && $options['limit'] !== false ? (string)$options['limit'] : '20';
        if ( !ctype_digit( $limit ) || (int)$limit < 1 )
            $this->usage( "--limit must be a number, not $limit" );
        $db = $this->database( $format );
        if ( !\expUrlMapBatch::tablesExist( $db ) )
            $this->finish( array( 'mode' => 'batches', 'batches' => array(),
                                  'warnings' => array( 'the tables of URL map imports do not exist yet: ./console exp:upgrade --apply (step 6.0.15-url-map)' ) ), $format );
        $batches = array_map( function ( \expUrlMapBatch $batch ) { return $batch->asArray(); }, \expUrlMapBatch::fetchList( 0, (int)$limit ) );
        $this->finish( array( 'mode' => 'batches', 'total' => \expUrlMapBatch::fetchListCount(), 'batches' => $batches ), $format );
    }

    protected function lines( array $report )
    {
        if ( empty( $report['batches'] ) )
            return array( 'No URL map imports' );
        $lines = array( '  Batch  When              By    File                           Created Updated Skipped  State' );
        foreach ( $report['batches'] as $b )
            $lines[] = sprintf( '  %-6d %-17s %-5d %-30s %7d %7d %7d  %s', $b['id'], date( 'Y-m-d H:i', $b['created'] ), $b['user_id'],
                                mb_substr( $b['source_name'], 0, 30 ), $b['created_count'], $b['updated_count'], $b['skipped_count'], $b['state'] );
        return $lines;
    }
}

}
