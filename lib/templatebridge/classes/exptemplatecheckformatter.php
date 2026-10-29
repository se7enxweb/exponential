<?php
/**
 * The output of exp:template:check: the human text and the machine formats json, jsonl, junit, checkstyle, github and
 * sarif. The JSON schema exp-template-check/1 is versioned: its fields are never removed or changed in type, new ones
 * may be added (doc/eng-us/reference/template-check-schema.json).
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */
class expTemplateCheckFormatter
{
    private $findings;
    private $results;
    private $summary;

    public function __construct( array $findings, array $results, array $summary )
    {
        $this->findings = $findings;
        $this->results = $results;
        $this->summary = $summary;
    }

    public function format( $format )
    {
        switch ( $format )
        {
            case 'json':
                return json_encode( $this->document(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . "\n";
            case 'jsonl':
                $out = '';
                foreach ( $this->findings as $finding )
                    $out .= json_encode( array( 'schema' => expTemplateChecker::SCHEMA, 'type' => 'finding' ) + $this->finding( $finding ), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . "\n";
                return $out . json_encode( array( 'schema' => expTemplateChecker::SCHEMA, 'type' => 'summary' ) + $this->summary, JSON_UNESCAPED_SLASHES ) . "\n";
            case 'junit':
                return $this->junit();
            case 'checkstyle':
                return $this->checkstyle();
            case 'github':
                return $this->github();
            case 'sarif':
                return json_encode( $this->sarif(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . "\n";
        }
        return $this->text( 0, 'engine' );
    }

    private function finding( array $f )
    {
        return array( 'rule' => $f['rule'], 'severity' => $f['severity'], 'engine' => $f['engine'], 'file' => $f['file'],
                      'line' => $f['line'], 'column' => $f['column'], 'message' => $f['message'], 'hint' => $f['hint'],
                      'source' => isset( $f['source'] ) ? $f['source'] : null, 'belongs' => isset( $f['belongs'] ) ? $f['belongs'] : array() );
    }

    public function document()
    {
        $files = array();
        foreach ( $this->results as $path => $result )
            $files[] = array( 'file' => $path, 'engine' => $result['engine'], 'status' => $result['status'], 'ms' => $result['ms'], 'belongs' => $result['belongs'] );
        return array( 'schema' => expTemplateChecker::SCHEMA, 'summary' => $this->summary,
                      'findings' => array_map( array( $this, 'finding' ), $this->findings ), 'files' => $files );
    }

    /**
     * The human output: one CHECK/OK line per file with -v, every finding with its place, message, source line and
     * hint, then the summary and PASS or FAIL.
     */
    public function text( $verbosity, $summaryBy )
    {
        $out = '';
        $byFile = array();
        foreach ( $this->findings as $finding )
            $byFile[$finding['file']][] = $finding;
        foreach ( $this->results as $path => $result )
        {
            if ( $verbosity > 0 && $result['status'] === 'OK' )
                $out .= sprintf( "OK     %-5s %s (%s, %d ms)\n", $result['engine'], $path, $this->summary['level'], $result['ms'] );
            foreach ( isset( $byFile[$path] ) ? $byFile[$path] : array() as $finding )
            {
                if ( $verbosity < 0 && $finding['severity'] !== 'ERROR' )
                    continue;
                $out .= $this->textFinding( $finding, $verbosity );
            }
            unset( $byFile[$path] );
        }
        foreach ( $byFile as $findings )
        {
            foreach ( $findings as $finding )
            {
                if ( $verbosity >= 0 || $finding['severity'] === 'ERROR' )
                    $out .= $this->textFinding( $finding, $verbosity );
            }
        }
        // -q: the findings at ERROR only; the exit code says the rest
        if ( $verbosity < 0 )
            return $out;
        $s = $this->summary;
        $engines = array();
        foreach ( $s['engines'] as $engine => $count )
            $engines[] = "$engine $count";
        $out .= sprintf( "SUMMARY %d files: %d OK, %d failed, %d SKIP; findings: %d ERROR, %d WARN, %d NOTE  (%s)  %.1f s\n", $s['files'], $s['ok'],
                         isset( $s['failed'] ) ? $s['failed'] : max( 0, $s['files'] - $s['ok'] - $s['skipped'] ), $s['skipped'], $s['errors'], $s['warnings'],
                         $s['notes'], implode( ', ', $engines ), $s['seconds'] );
        if ( $summaryBy !== 'engine' && $summaryBy !== '' )
            $out .= $this->groupedSummary( $summaryBy );
        foreach ( $s['unavailable'] as $engine )
            $out .= "UNAVAILABLE $engine\n";
        return $out . $s['result'] . "\n";
    }

    private function textFinding( array $f, $verbosity )
    {
        $place = $f['file'] . ( $f['line'] ? ':' . $f['line'] . ( $f['column'] ? ':' . $f['column'] : '' ) : '' );
        $out = sprintf( "%-6s %-5s %s\n       %s [%s]\n", $f['severity'], $f['engine'], $place, $f['message'], $f['rule'] );
        if ( $f['source'] !== null && $f['source'] !== '' )
            $out .= sprintf( "       > %d | %s\n", $f['line'], $f['source'] );
        if ( $f['hint'] )
            $out .= "       hint: " . $f['hint'] . "\n";
        if ( $verbosity > 0 && !empty( $f['belongs'] ) )
            $out .= "       in: " . implode( '; ', $f['belongs'] ) . "\n";
        return $out;
    }

    private function groupedSummary( $by )
    {
        $groups = array();
        foreach ( $this->results as $path => $result )
        {
            switch ( $by )
            {
                case 'directory':
                    $keys = array( dirname( $path ) );
                    break;
                case 'design':
                case 'extension':
                case 'siteaccess':
                    $keys = array();
                    foreach ( $result['belongs'] as $belongs )
                    {
                        if ( preg_match( '/' . $by . ' ([^ :()]+)/', $belongs, $m ) )
                            $keys[] = $m[1];
                    }
                    $keys = $keys ?: array( '-' );
                    break;
                default:
                    $keys = array( $result['engine'] );
            }
            foreach ( array_unique( $keys ) as $key )
            {
                if ( !isset( $groups[$key] ) )
                    $groups[$key] = array( 'files' => 0, 'bad' => 0 );
                ++$groups[$key]['files'];
                if ( $result['status'] === 'ERROR' )
                    ++$groups[$key]['bad'];
            }
        }
        ksort( $groups );
        $out = '';
        foreach ( $groups as $key => $group )
            $out .= sprintf( "  %-10s %-40s %5d files, %d with errors\n", $by, $key, $group['files'], $group['bad'] );
        return $out;
    }

    private function junit()
    {
        $suites = array();
        foreach ( $this->results as $path => $result )
            $suites[$result['engine']][$path] = array();
        foreach ( $this->findings as $f )
            $suites[$f['engine']][$f['file']][] = $f;
        $x = new XMLWriter();
        $x->openMemory();
        $x->setIndent( true );
        $x->startDocument( '1.0', 'UTF-8' );
        $x->startElement( 'testsuites' );
        $x->writeAttribute( 'name', 'exp:template:check' );
        foreach ( $suites as $engine => $files )
        {
            $failures = 0;
            foreach ( $files as $findings )
                $failures += count( array_filter( $findings, function ( $f ) { return $f['severity'] === 'ERROR'; } ) ) ? 1 : 0;
            $x->startElement( 'testsuite' );
            $x->writeAttribute( 'name', $engine );
            $x->writeAttribute( 'tests', (string)count( $files ) );
            $x->writeAttribute( 'failures', (string)$failures );
            foreach ( $files as $path => $findings )
            {
                $x->startElement( 'testcase' );
                $x->writeAttribute( 'name', $path );
                $x->writeAttribute( 'classname', 'template.' . $engine );
                foreach ( $findings as $f )
                {
                    $x->startElement( $f['severity'] === 'ERROR' ? 'failure' : 'system-out' );
                    if ( $f['severity'] === 'ERROR' )
                    {
                        $x->writeAttribute( 'type', $f['rule'] );
                        $x->writeAttribute( 'message', $f['message'] );
                    }
                    $x->text( $f['severity'] . ' ' . $f['rule'] . ' ' . $path . ':' . $f['line'] . ':' . $f['column'] . ' ' . $f['message'] );
                    $x->endElement();
                }
                $x->endElement();
            }
            $x->endElement();
        }
        $x->endElement();
        $x->endDocument();
        return $x->outputMemory();
    }

    private function checkstyle()
    {
        $byFile = array();
        foreach ( $this->results as $path => $result )
            $byFile[$path] = array();
        foreach ( $this->findings as $f )
            $byFile[$f['file']][] = $f;
        $x = new XMLWriter();
        $x->openMemory();
        $x->setIndent( true );
        $x->startDocument( '1.0', 'UTF-8' );
        $x->startElement( 'checkstyle' );
        $x->writeAttribute( 'version', '4.3' );
        foreach ( $byFile as $path => $findings )
        {
            $x->startElement( 'file' );
            $x->writeAttribute( 'name', $path );
            foreach ( $findings as $f )
            {
                $x->startElement( 'error' );
                $x->writeAttribute( 'line', (string)$f['line'] );
                if ( $f['column'] )
                    $x->writeAttribute( 'column', (string)$f['column'] );
                $x->writeAttribute( 'severity', array( 'ERROR' => 'error', 'WARN' => 'warning', 'NOTE' => 'info' )[$f['severity']] );
                $x->writeAttribute( 'message', $f['message'] );
                $x->writeAttribute( 'source', 'exp.template.check.' . $f['rule'] );
                $x->endElement();
            }
            $x->endElement();
        }
        $x->endElement();
        $x->endDocument();
        return $x->outputMemory();
    }

    private function github()
    {
        $out = '';
        $escape = function ( $value ) { return str_replace( array( '%', "\r", "\n", ',', ':' ), array( '%25', '%0D', '%0A', '%2C', '%3A' ), (string)$value ); };
        foreach ( $this->findings as $f )
        {
            $level = array( 'ERROR' => 'error', 'WARN' => 'warning', 'NOTE' => 'notice' )[$f['severity']];
            $out .= '::' . $level . ' file=' . $escape( $f['file'] ) . ( $f['line'] ? ',line=' . $f['line'] : '' ) . ( $f['column'] ? ',col=' . $f['column'] : '' ) .
                    ',title=' . $escape( $f['rule'] ) . '::' . str_replace( array( '%', "\r", "\n" ), array( '%25', '%0D', '%0A' ), $f['message'] ) . "\n";
        }
        return $out;
    }

    private function sarif()
    {
        $rules = array();
        $index = array();
        foreach ( expTemplateChecker::RULES as $id => $rule )
        {
            $index[$id] = count( $rules );
            $rules[] = array( 'id' => $id, 'shortDescription' => array( 'text' => $rule[1] ),
                              'defaultConfiguration' => array( 'level' => array( 'ERROR' => 'error', 'WARN' => 'warning', 'NOTE' => 'note' )[$rule[0]] ),
                              'helpUri' => 'doc/eng-us/reference/template-check-rules.md#' . strtolower( $id ) );
        }
        $results = array();
        foreach ( $this->findings as $f )
        {
            $region = array();
            if ( $f['line'] )
                $region['startLine'] = $f['line'];
            if ( $f['column'] )
                $region['startColumn'] = $f['column'];
            $location = array( 'physicalLocation' => array( 'artifactLocation' => array( 'uri' => $f['file'] ) ) );
            if ( $region )
                $location['physicalLocation']['region'] = $region;
            $results[] = array( 'ruleId' => $f['rule'], 'ruleIndex' => isset( $index[$f['rule']] ) ? $index[$f['rule']] : 0,
                                'level' => array( 'ERROR' => 'error', 'WARN' => 'warning', 'NOTE' => 'note' )[$f['severity']],
                                'message' => array( 'text' => $f['message'] . ( $f['hint'] ? ' (' . $f['hint'] . ')' : '' ) ), 'locations' => array( $location ) );
        }
        return array( '$schema' => 'https://json.schemastore.org/sarif-2.1.0.json', 'version' => '2.1.0',
                      'runs' => array( array( 'tool' => array( 'driver' => array( 'name' => 'exp:template:check',
                                                                                  'rules' => $rules ) ), 'results' => $results ) ) );
    }
}
