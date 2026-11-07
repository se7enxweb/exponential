<?php
/**
 * @description Suggest new targets for the rows of a URL map that have none, by how alike their old address is to the addresses and names of this site's pages
 * @alias exp:url:suggest
 *
 * File containing the exp:url:suggest command.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Expurlsuggest extends ExpurlBase
{
    protected function name()
    {
        return 'exp:url:suggest';
    }

    protected function begin()
    {
        $this->script( array(
            'description' => "Suggest new targets for a URL map\n" .
                             "For every row without a new_url, compares the old address (its last element most, without endings\n" .
                             "such as .html, transliterated) with the URL aliases and names of the visible pages of the siteaccess\n" .
                             "of -s and scores the best ones from 0 to 100. With --fill-above the best one is written into new_url\n" .
                             "when it scores that much; the others are listed in the row's note. The map is written again, with\n" .
                             "every row; nothing is imported.\n" .
                             "\n" .
                             "./console exp:url:suggest var/tmp/old-site.csv --output=var/tmp/old-site-suggested.csv\n" .
                             "./console exp:url:suggest var/tmp/old-site.csv -s site --fill-above=85 --output=var/tmp/map.xlsx\n" .
                             "./console exp:url:suggest var/tmp/old-site.csv --top=5 --report=var/tmp/suggestions.json --output=var/tmp/map.csv",
        ) + self::SCRIPT );
        return (array)$this->startup( '[output:][output-format:][format:][top:][fill-above:][min-score:][report:]', '[file]', array(
            'output' => 'The file to write (default the standard output; needed for xlsx and ods)',
            'output-format' => 'import, export: what the command prints, text (default) or json (the machine output exp-url/1); --format is the file\'s format there; from-sitemap, from-404, suggest: the format of the file written (csv, json, xlsx, ods, apache, nginx; default from --output, else csv)',
            'format' => 'import: the format of the file (csv, json, xlsx, ods, apache, nginx; default from its name or content); export: the format to write (default from --output, else csv); batches, undo, from-sitemap, from-404, suggest, test: text (default) or json (the machine output exp-url/1)',
            'top' => 'suggest: the suggestions per row (default 3)',
            'fill-above' => 'suggest: write the best suggestion into new_url when it scores this much or more (0 to 100; never a tie; default: never)',
            'min-score' => 'suggest: the lowest score suggested (0 to 100, default 30)',
            'report' => 'import: also write the plan, row by row, to this file (.json, or .csv); suggest, test: also write every row with its suggestions or its result to this file (.json, or .csv)',
        ) );
    }

    public function run()
    {
        $options = $this->begin();
        $printFormat = $this->outputFormat( $options );
        $mapFormat = $this->mapFormat( $options );
        $top = $this->intOption( $options, 'top', 3, 1, 20 );
        $fill = $this->intOption( $options, 'fill-above', null, 0, 100 );
        $min = $this->intOption( $options, 'min-score', 30, 0, 100 );
        $rows = $this->readMap( isset( $options['arguments'][0] ) ? (string)$options['arguments'][0] : '', $printFormat );
        $this->database( $printFormat );
        $siteaccess = $this->givenSiteaccess();
        $started = microtime( true );
        $candidates = \expUrlMapSuggester::candidatesFromDatabase( array( 'siteaccess' => $siteaccess ) );
        $suggester = new \expUrlMapSuggester( $candidates, array( 'top' => $top, 'min_score' => $min ) );
        $result = $suggester->apply( $rows, array( 'fill_above' => $fill ) );
        $report = array( 'mode' => 'suggest', 'file' => (string)$options['arguments'][0], 'siteaccess' => $siteaccess, 'candidates' => $suggester->count(),
                         'fill_above' => $fill, 'counts' => $result['counts'], 'seconds' => round( microtime( true ) - $started, 2 ),
                         'errors' => array(), 'warnings' => array() );
        if ( !$candidates )
            $report['warnings'][] = 'no visible page with a URL alias below the content root of the siteaccess';
        if ( isset( $options['report'] ) && $options['report'] )
        {
            try
            {
                self::writeReport( (string)$options['report'], $result['report'], array( 'line', 'old_url', 'new_url', 'filled', 'suggestions' ) );
                $report['report'] = (string)$options['report'];
            }
            catch ( \RuntimeException $e )
            {
                $report['warnings'][] = $e->getMessage();
            }
        }
        if ( $printFormat === 'json' )
            $report['suggestions'] = $result['report'];
        $this->finishRows( $result['rows'], $options, $report, $printFormat, $mapFormat );
    }

    protected function lines( array $report )
    {
        if ( !isset( $report['counts'] ) )
            return array();
        $c = $report['counts'];
        return array( sprintf( 'Suggest %s: %d rows, %d without a target: %d filled (score %s or more), %d with suggestions in the note, %d without any; %d pages compared in %.1f s%s',
                               $report['file'], $c['rows'], $c['open'], $c['filled'], $report['fill_above'] === null ? '-' : $report['fill_above'], $c['suggested'], $c['none'],
                               $report['candidates'], $report['seconds'], $report['output'] !== '' ? ' -> ' . $report['output'] : '' ) );
    }
}

}
