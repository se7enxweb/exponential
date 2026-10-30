<?php
/**
 * exp:template:check for Exponential templates (.tpl): the template system's own parser (as eztemplatecheck uses it,
 * eZTemplate::validateTemplateFile()), at compile level the template compiler into a private directory with the
 * compiled PHP linted, at strict level every design: include resolved in the design chain.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */
class expTplTemplateChecker implements expTemplateEngineChecker
{
    public function available()
    {
        return true;
    }

    public function check( $file, $text, $level, array $context )
    {
        $findings = array();
        $tpl = eZTemplate::factory();
        $path = $file;
        $temporary = null;
        if ( $file === '-' || !is_file( $file ) || file_get_contents( $file ) !== $text )
        {
            $temporary = $context['compiledDir'] . '/stdin-' . md5( $text ) . '.tpl';
            file_put_contents( $temporary, $text );
            $path = $temporary;
        }
        $resourceData = $tpl->validateTemplateFile( $path, true );
        if ( !is_array( $resourceData ) )
            return array( self::finding( 'CHECK-READ', 'ERROR', 0, 0, 'the template cannot be read' ) );
        foreach ( array( 'errors' => 'ERROR', 'warnings' => 'WARN' ) as $kind => $severity )
        {
            foreach ( isset( $resourceData[$kind] ) ? $resourceData[$kind] : array() as $error )
            {
                $line = 0;
                $column = 0;
                if ( !empty( $error['placement'] ) && isset( $error['placement']['start'] ) )
                {
                    $line = (int)$error['placement']['start']['line'];
                    $column = (int)$error['placement']['start']['column'];
                }
                list( $line, $column ) = self::position( $error['text'], $line, $column );
                $findings[] = self::finding( $severity === 'ERROR' ? 'TPL-SYNTAX-001' : 'TPL-SYNTAX-002', $severity, $line, $column,
                                             self::message( (string)$error['text'] ) );
            }
        }
        if ( $level !== 'syntax' && !self::hasErrors( $findings ) && eZTemplateCompiler::isCompilationEnabled() )
        {
            $saved = isset( $GLOBALS['eZTemplateCompilerSettings']['compilation-directory'] ) ? $GLOBALS['eZTemplateCompilerSettings']['compilation-directory'] : null;
            $GLOBALS['eZTemplateCompilerSettings']['compilation-directory'] = $context['compiledDir'] . '/tpl';
            try
            {
                $key = md5( 'check:' . $path . ':' . md5( $text ) );
                $resourceData['key-data'] = 'file:' . $path;
                if ( eZTemplateCompiler::compileTemplate( $tpl, $key, $resourceData ) )
                {
                    // compilationFilename() is the file's name below the compilation directory
                    $compiled = eZTemplateCompiler::compilationDirectory() . '/' . eZTemplateCompiler::compilationFilename( $key, $resourceData );
                    $error = is_file( $compiled ) ? expTemplateChecker::lintPhp( file_get_contents( $compiled ) ) : 'no compiled file was written';
                    if ( $error !== null )
                        $findings[] = self::finding( 'TPL-COMPILE-001', 'ERROR', 0, 0, 'the compiled template is not valid PHP: ' . $error );
                }
            }
            catch ( Throwable $e )
            {
                $findings[] = self::finding( 'TPL-COMPILE-001', 'ERROR', 0, 0, 'the template does not compile: ' . $e->getMessage() );
            }
            finally
            {
                $GLOBALS['eZTemplateCompilerSettings']['compilation-directory'] = $saved;
            }
        }
        if ( $level === 'strict' && isset( $resourceData['root-node'] ) )
            $findings = array_merge( $findings, self::unknownOperators( $tpl, $resourceData['root-node'], $text ) );
        if ( !empty( $context['includes'] ) && isset( $context['resolve'] ) && preg_match_all( "/\\{include\\s+uri=(['\"])(design:[A-Za-z0-9_.\\/-]+)\\1/", $text, $matches, PREG_OFFSET_CAPTURE ) )
        {
            foreach ( $matches[2] as $match )
            {
                if ( !call_user_func( $context['resolve'], $match[0] ) )
                {
                    $line = substr_count( $text, "\n", 0, $match[1] ) + 1;
                    $findings[] = self::finding( 'TPL-STRICT-INCLUDE-MISSING', 'ERROR', $line, 0, "the included template {$match[0]} is not found in the design chain" );
                }
            }
        }
        if ( $temporary !== null )
            @unlink( $temporary );
        $tpl->resetErrorLog();
        return $findings;
    }

    /**
     * A parser message without its head ("parser error @ <file>:<line>[<column>]", which can be a bare word), on one
     * line: "Extra characters found in expression, they will be ignored: |wash(' ($a)".
     */
    public static function message( $text )
    {
        $lines = explode( "\n", str_replace( "\r", '', (string)$text ) );
        if ( preg_match( '/^parser (error|warning) @/', $lines[0] ) )
            array_shift( $lines );
        $lines = array_values( array_filter( array_map( 'trim', $lines ), 'strlen' ) );
        if ( !$lines )
            return trim( (string)$text );
        $first = array_shift( $lines );
        return $lines ? rtrim( $first, '.' ) . ': ' . implode( ' ', $lines ) : $first;
    }

    /**
     * strict: the operators of the parsed tree that the template system has not registered (they pass their input
     * through and write a warning when the template runs).
     */
    private static function unknownOperators( eZTemplate $tpl, $root, $text )
    {
        $names = array();
        $walk = function ( $node ) use ( &$walk, &$names )
        {
            if ( !is_array( $node ) )
                return;
            if ( isset( $node[0], $node[1] ) && $node[0] === eZTemplate::TYPE_OPERATOR && is_array( $node[1] ) && isset( $node[1][0] ) && is_string( $node[1][0] ) )
                $names[$node[1][0]] = true;
            foreach ( $node as $child )
                $walk( $child );
        };
        $walk( $root );
        $findings = array();
        foreach ( array_keys( $names ) as $name )
        {
            if ( isset( $tpl->Operators[$name] ) )
                continue;
            $line = 0;
            if ( preg_match( '/(\|\s*' . preg_quote( $name, '/' ) . '\b|\b' . preg_quote( $name, '/' ) . '\s*\()/', $text, $m, PREG_OFFSET_CAPTURE ) )
                $line = substr_count( $text, "\n", 0, $m[0][1] ) + 1;
            $findings[] = self::finding( 'TPL-STRICT-UNKNOWN-OPERATOR', 'WARN', $line, 0, "the operator $name is not registered: it passes its input through",
                                         expTemplateChecker::operatorSuggestion( $name ) );
        }
        return $findings;
    }

    /** The line and column a parser message names ("@ file:12[5]") where the placement has none. */
    private static function position( $text, $line, $column )
    {
        if ( $line === 0 && preg_match( '/:(\d+)(?:\[(\d+)\])?/', (string)$text, $m ) )
            return array( (int)$m[1], isset( $m[2] ) ? (int)$m[2] : 0 );
        return array( $line, $column );
    }

    private static function hasErrors( array $findings )
    {
        foreach ( $findings as $finding )
        {
            if ( $finding['severity'] === 'ERROR' )
                return true;
        }
        return false;
    }

    public static function finding( $rule, $severity, $line, $column, $message, $hint = null )
    {
        return array( 'rule' => $rule, 'severity' => $severity, 'line' => (int)$line, 'column' => (int)$column, 'message' => (string)$message, 'hint' => $hint );
    }
}
