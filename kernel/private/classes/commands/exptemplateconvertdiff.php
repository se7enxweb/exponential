<?php
/**
 * @description Print the conversion of one template to another engine on stdout, without writing anything (--file, --to-engine, --from-engine)
 * @alias exp:template:convert:diff
 *
 * File containing the exp:template:convert:diff command (bin/php/exptemplateconvertdiff.php): one template through the
 * converter of exp:template:convert (expTemplateDesignConverter), printed instead of written, to review a hand edit
 * against what the converter writes. Guide: doc/eng-us/guides/template-conversion.md
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Exptemplateconvertdiff extends \Exponential\Runnable\Command
{
    public function run()
    {
        $this->script( array( 'description' => "Print the conversion of one template to another engine, without writing anything\n" .
                                               "The engine of --file follows from its ending unless --from-engine is given. The converted text goes\n" .
                                               "to stdout, the NOTE lines of what could not be converted exactly to stderr; with --against=<file> the\n" .
                                               "converted text is compared with that file (a target written before and perhaps edited by hand) and\n" .
                                               "the differences are printed as a unified diff.\n" .
                                               "\n" .
                                               "./console exp:template:convert:diff --file=design/<design>/templates/<name>.tpl --to-engine=twig\n" .
                                               "./console exp:template:convert:diff --file=<name>.tpl --to-engine=twig --against=<name>.twig\n" .
                                               "\n" .
                                               "Exit code: 0 converted (no differences with --against); 1 notes, a check failed, or differences;\n" .
                                               "2 wrong options; 3 the conversion is not available.",
                              'use-session' => false,
                              'use-modules' => true,
                              'use-extensions' => true ) );
        $options = $this->startup( '[file:][from-engine:][to-engine:][against:]', '', array(
            'file' => 'the template to convert',
            'from-engine' => 'its engine: tpl, stpl or twig (default: from the file ending)',
            'to-engine' => 'the engine to convert it to: tpl, stpl or twig',
            'against' => 'compare the conversion with this file and print a unified diff',
        ) );
        $cli = $this->cli();
        $file = isset( $options['file'] ) ? (string)$options['file'] : '';
        if ( $file === '' || !is_file( $file ) )
        {
            $cli->error( 'FAIL --file must name an existing template' . ( $file !== '' ? ": $file" : '' ) );
            $this->shutdown( 2 );
        }
        $from = isset( $options['from-engine'] ) && $options['from-engine'] ? \expTemplateDesignConverter::engine( $options['from-engine'] ) : null;
        if ( $from === null )
            $from = substr( $file, -4 ) === '.tpl' ? 'tpl' : ( \expTemplateEngines::engineFor( $file ) ?: null );
        $to = isset( $options['to-engine'] ) && $options['to-engine'] ? \expTemplateDesignConverter::engine( $options['to-engine'] ) : null;
        if ( $from === null || $to === null || $from === $to )
        {
            $cli->error( 'FAIL --to-engine (and --from-engine for a file of no known ending) must be two different engines: tpl, stpl, twig' );
            $this->shutdown( 2 );
        }
        $converter = new \expTemplateDesignConverter( $from, $to );
        if ( !$converter->available() )
        {
            $cli->error( 'FAIL ' . $converter->direction() . ' is not available' );
            $this->shutdown( 3 );
        }
        list( $text, $notes ) = $converter->convertText( (string)file_get_contents( $file ), basename( $file ) );
        $problem = $converter->verify( $text, basename( $file ) );
        $exit = $notes || $problem !== null ? 1 : 0;
        if ( isset( $options['against'] ) && $options['against'] )
        {
            $against = (string)$options['against'];
            if ( !is_file( $against ) )
            {
                $cli->error( "FAIL --against must name an existing file: $against" );
                $this->shutdown( 2 );
            }
            list( $existing ) = \expTemplateDesignConverter::splitMarker( (string)file_get_contents( $against ) );
            $diff = self::unifiedDiff( $existing, $text, $against, 'converted ' . $file );
            if ( $diff !== '' )
            {
                $cli->output( rtrim( $diff, "\n" ) );
                $exit = 1;
            }
        }
        else
            $cli->output( $text, false );
        foreach ( $notes as $note )
            $cli->error( "NOTE $file: $note" );
        if ( $problem !== null )
            $cli->error( "ERROR $file: $problem" );
        $this->shutdown( $exit );
    }

    /**
     * A unified diff of two texts, line by line (longest common subsequence), with three lines of context.
     */
    public static function unifiedDiff( $old, $new, $oldName, $newName )
    {
        $a = explode( "\n", $old );
        $b = explode( "\n", $new );
        $n = count( $a );
        $m = count( $b );
        $lcs = array_fill( 0, $n + 1, array_fill( 0, $m + 1, 0 ) );
        for ( $i = $n - 1; $i >= 0; --$i )
            for ( $j = $m - 1; $j >= 0; --$j )
                $lcs[$i][$j] = $a[$i] === $b[$j] ? $lcs[$i + 1][$j + 1] + 1 : max( $lcs[$i + 1][$j], $lcs[$i][$j + 1] );
        $ops = array();
        for ( $i = 0, $j = 0; $i < $n || $j < $m; )
        {
            if ( $i < $n && $j < $m && $a[$i] === $b[$j] )
                $ops[] = array( ' ', $a[$i++], $j++ );
            // removed lines before added ones, as diff tools show them
            else if ( $i < $n && ( $j >= $m || $lcs[$i + 1][$j] >= $lcs[$i][$j + 1] ) )
                $ops[] = array( '-', $a[$i++], null );
            else
                $ops[] = array( '+', $b[$j++], null );
        }
        $changed = array();
        foreach ( $ops as $k => $op )
            if ( $op[0] !== ' ' )
                $changed[] = $k;
        if ( !$changed )
            return '';
        $out = "--- $oldName\n+++ $newName\n";
        $shown = array();
        foreach ( $changed as $k )
            for ( $c = max( 0, $k - 3 ); $c <= min( count( $ops ) - 1, $k + 3 ); ++$c )
                $shown[$c] = true;
        $last = -2;
        foreach ( array_keys( $shown ) as $k )
        {
            if ( $k !== $last + 1 )
                $out .= "@@\n";
            $out .= $ops[$k][0] . $ops[$k][1] . "\n";
            $last = $k;
        }
        return $out;
    }
}

}
