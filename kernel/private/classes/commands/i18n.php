<?php
/**
 * @description The translation fallbacks: per locale the chain of translations it falls back to, and how much of the kernel and of each extension it shows translated; --check for CI
 * @alias exp:i18n
 *
 * File containing the exp:i18n command (doc/en/us/languages.md, "Translation fallbacks"). It uses the classes of
 * Setup > Interface languages: expI18nFallbackResolver (i18n.ini [TranslationSettings]) and expI18nFallbackReport.
 * Nothing is changed.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class I18n extends \Exponential\Runnable\Command
{
    /** exit codes */
    const EXIT_OK = 0;
    const EXIT_PROBLEMS = 1;
    const EXIT_USAGE = 2;

    public function run()
    {
        $cli = $this->cli();
        $this->script(
            array(
                'description' => "The translation fallbacks (i18n.ini [TranslationSettings], doc/en/us/languages.md)\n" .
                                 "\n" .
                                 "./console exp:i18n fallbacks                         every locale: its chain, kernel and extension coverage\n" .
                                 "./console exp:i18n fallbacks --only=fallback         only the locales translated through a fallback\n" .
                                 "./console exp:i18n fallbacks --locale=esl-AR,ger-AT  each step with its rule, and every extension\n" .
                                 "./console exp:i18n fallbacks --format=json           one JSON object\n" .
                                 "./console exp:i18n check [--strict]                  cycles, chains too long, missing targets, rules\n" .
                                 "                                                     that are none: exit 1 when there is one (CI)\n" .
                                 "\n" .
                                 "Exit code: 0 fine, 1 problems found (check, or --check), 2 usage error.",
                'use-session'    => false,
                'use-modules'    => false,
                'use-extensions' => true
            )
        );
        $options = $this->startup( '[locale:][format:][only:][check][strict][no-extensions]', '[ACTION]', array(
            'locale'        => 'Only these locales, comma separated (esl-AR,ger-AT): each step of the chain and every extension',
            'format'        => 'table (default) or json',
            'only'          => 'With the list: own (a translation of its own), fallback (translated through a fallback) or untranslated',
            'check'         => 'Check the fallbacks as the check action does, after the list',
            'strict'        => 'With check: warnings (a configured locale without a locale file, a chain without a translation) fail too',
            'no-extensions' => 'Leave out the coverage of the extensions (reading their files the first time takes seconds)'
        ) );

        $action = isset( $options['arguments'][0] ) ? strtolower( trim( $options['arguments'][0] ) ) : 'fallbacks';
        $format = isset( $options['format'] ) && is_string( $options['format'] ) ? strtolower( $options['format'] ) : 'table';
        $only = isset( $options['only'] ) && is_string( $options['only'] ) ? strtolower( $options['only'] ) : '';
        if ( !in_array( $action, array( 'fallbacks', 'check' ), true ) || !in_array( $format, array( 'table', 'json' ), true )
             || !in_array( $only, array( '', 'own', 'fallback', 'untranslated' ), true ) )
        {
            $cli->error( 'Usage: ./console exp:i18n fallbacks|check [--locale=...] [--format=table|json] [--only=own|fallback|untranslated] [--strict] (see --help)' );
            $this->shutdown( self::EXIT_USAGE );
            return;
        }

        $report = new \expI18nFallbackReport();
        $check = $action === 'check' || !empty( $options['check'] );
        $strict = !empty( $options['strict'] );
        $problems = $check ? $report->problems() : array();
        $locales = null;
        if ( isset( $options['locale'] ) && is_string( $options['locale'] ) && $options['locale'] !== '' )
        {
            $locales = array_values( array_filter( array_map( 'trim', explode( ',', $options['locale'] ) ), 'strlen' ) );
            foreach ( $locales as $locale )
            {
                if ( !preg_match( \expI18nFallbackResolver::LOCALE_PATTERN, $locale ) )
                {
                    $cli->error( "Not a locale code: $locale (like esl-AR)" );
                    $this->shutdown( self::EXIT_USAGE );
                    return;
                }
            }
        }

        $rows = array();
        if ( $action === 'fallbacks' )
            $rows = self::filter( $report->rows( $locales, empty( $options['no-extensions'] ) ), $only );

        if ( $format === 'json' )
            $cli->output( json_encode( self::toArray( $report->resolver(), $rows, $problems, $check ), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
        else
        {
            if ( $action === 'fallbacks' )
                $cli->output( rtrim( $locales !== null ? self::renderLocales( $rows ) : self::renderTable( $report->resolver(), $rows ) ) );
            if ( $check )
                $cli->output( rtrim( self::renderProblems( $problems, $strict ) ) );
        }
        $this->shutdown( $check ? self::exitCode( $problems, $strict ) : self::EXIT_OK );
    }

    /**
     * The rows of one kind: own, fallback, untranslated, or all for ''.
     *
     * @param array $rows expI18nFallbackReport::rows()
     * @param string $only
     * @return array
     */
    public static function filter( array $rows, $only )
    {
        return \expI18nFallbackReport::filterRows( $rows, (string)$only );
    }

    /**
     * 0 without problems, 1 with an error (or, with $strict, a warning).
     *
     * @param array $problems expI18nFallbackResolver::problems()
     * @param bool $strict
     * @return int
     */
    public static function exitCode( array $problems, $strict )
    {
        foreach ( $problems as $problem )
        {
            if ( $problem['level'] === 'error' || $strict )
                return self::EXIT_PROBLEMS;
        }
        return self::EXIT_OK;
    }

    /**
     * A rule's name for people: FallbackLanguages[], automatic, or the class of an extension's rule.
     *
     * @param string $rule
     * @return string
     */
    public static function ruleLabel( $rule )
    {
        if ( $rule === \expI18nFallbackResolver::RULE_CONFIGURED )
            return 'FallbackLanguages[]';
        return $rule;
    }

    /**
     * A chain as text: esl-MX > esl-ES, 'untranslated' as "source texts", with the end of a cycle or of the depth.
     *
     * @param array $row
     * @param bool $rules with the rule of each step
     * @return string
     */
    public static function chainText( array $row, $rules = false )
    {
        $parts = array();
        foreach ( $row['chain'] as $step )
        {
            $text = $step['target'] === \expI18nFallbackResolver::SOURCE ? 'source texts' : $step['target'];
            if ( $rules )
                $text .= ' (' . self::ruleLabel( $step['rule'] ) . ')';
            $parts[] = $text;
        }
        if ( $row['problem'] !== '' )
            $parts[] = '!' . $row['problem'] . ' ' . $row['problem_target'];
        return $parts ? implode( ' > ', $parts ) : '-';
    }

    /**
     * The list: one line per locale.
     *
     * @param \expI18nFallbackResolver $resolver
     * @param array $rows
     * @return string
     */
    public static function renderTable( \expI18nFallbackResolver $resolver, array $rows )
    {
        $extensions = $rows && isset( $rows[0]['extension_summary'] );
        $lines = array();
        $lines[] = 'Mode ' . $resolver->mode() . ', rules ' . implode( ', ', array_map( array( __CLASS__, 'ruleLabel' ), $resolver->ruleNames() ) )
                   . ', max depth ' . $resolver->option( 'max_depth' );
        $header = sprintf( '%-12s %-34s %-7s %-6s %s', 'Locale', 'Language', 'Kernel', 'From', 'Chain' );
        if ( $extensions )
            $header = sprintf( '%-12s %-34s %-7s %-6s %-14s %s', 'Locale', 'Language', 'Kernel', 'From', 'Extensions', 'Chain' );
        $lines[] = $header;
        foreach ( $rows as $row )
        {
            $kernel = $row['coverage'] === null ? '-' : $row['coverage'] . '%';
            $from = $row['from'] !== '' ? $row['from'] : ( $row['own'] ? 'own' : '' );
            $name = self::cut( $row['english'] !== '' ? $row['english'] : $row['native'], 34 );
            if ( $extensions )
            {
                $summary = $row['extension_summary'];
                $ext = $summary['translated'] . '/' . $summary['total'] . ( $summary['average'] !== null ? ' ' . $summary['average'] . '%' : '' );
                $lines[] = rtrim( sprintf( '%-12s %-34s %-7s %-6s %-14s %s', $row['locale'], $name, $kernel, $from, $ext, self::chainText( $row ) ) );
            }
            else
                $lines[] = rtrim( sprintf( '%-12s %-34s %-7s %-6s %s', $row['locale'], $name, $kernel, $from, self::chainText( $row ) ) );
        }
        $counts = \expI18nFallbackReport::counts( $rows );
        $lines[] = sprintf( '%d locales: %d with a translation of their own, %d through a fallback, %d in the source texts (English)',
                            count( $rows ), $counts['own'], $counts['fallback'], $counts['untranslated'] );
        return implode( "\n", $lines ) . "\n";
    }

    /**
     * The details of a few locales: each step with its rule, the kernel and every extension.
     *
     * @param array $rows
     * @return string
     */
    public static function renderLocales( array $rows )
    {
        $lines = array();
        foreach ( $rows as $row )
        {
            $lines[] = $row['locale'] . ( $row['english'] !== '' ? ' ' . $row['english'] : ' (no locale file)' );
            $lines[] = '  own translation: ' . ( $row['own'] ? 'yes' : 'no' );
            $lines[] = '  chain: ' . self::chainText( $row, true );
            $lines[] = '  kernel: ' . ( $row['coverage'] === null ? 'source texts (English)' : $row['coverage'] . '%' )
                       . ( $row['from'] !== '' ? ' through ' . $row['from'] : '' );
            if ( isset( $row['extensions'] ) )
            {
                $summary = $row['extension_summary'];
                $lines[] = sprintf( '  extensions: %d of %d translated, %d complete%s', $summary['translated'], $summary['total'], $summary['complete'],
                                    $summary['average'] !== null ? ', ' . $summary['average'] . '% on average' : '' );
                foreach ( $row['extensions'] as $name => $coverage )
                    $lines[] = sprintf( '    %-36s %s', $name, $coverage === null ? '-' : $coverage . '%' );
            }
            $lines[] = '';
        }
        return implode( "\n", $lines );
    }

    /**
     * The problems, and a PASS or FAIL line.
     *
     * @param array $problems
     * @param bool $strict
     * @return string
     */
    public static function renderProblems( array $problems, $strict )
    {
        $texts = array( 'cycle' => 'its chain comes back to %s',
                        'depth' => 'its chain is longer than FallbackMaxDepth, at %s',
                        'invalid-target' => 'falls back to %s, which is no locale code',
                        'missing-target' => 'falls back to %s, which has neither a locale file nor a translation',
                        'invalid-rule' => 'FallbackRules[] names %s, which is no class implementing expI18nFallbackRuleInterface',
                        'unknown-locale' => 'is configured but has no locale file (share/locale/%s.ini)',
                        'no-translation' => 'falls back to %s, which has no translation yet: it shows the source texts' );
        $lines = array();
        foreach ( $problems as $problem )
        {
            $text = isset( $texts[$problem['code']] ) ? sprintf( $texts[$problem['code']], $problem['detail'] ) : $problem['code'] . ' ' . $problem['detail'];
            $lines[] = strtoupper( $problem['level'] ) . ': ' . ( $problem['locale'] !== '' ? $problem['locale'] . ' ' : '' ) . $text;
        }
        $failed = self::exitCode( $problems, $strict ) !== self::EXIT_OK;
        $errors = count( array_filter( $problems, function ( $problem ) { return $problem['level'] === 'error'; } ) );
        $lines[] = ( $failed ? 'FAIL' : 'PASS' ) . sprintf( ': %d errors, %d warnings', $errors, count( $problems ) - $errors );
        return implode( "\n", $lines ) . "\n";
    }

    /**
     * The JSON of the command.
     *
     * @return array
     */
    public static function toArray( \expI18nFallbackResolver $resolver, array $rows, array $problems, $check )
    {
        $data = array( 'mode' => $resolver->mode(), 'rules' => $resolver->ruleNames(), 'max_depth' => $resolver->option( 'max_depth' ),
                       'automatic' => (bool)$resolver->option( 'automatic' ), 'locales' => $rows );
        if ( $check )
            $data['problems'] = $problems;
        return $data;
    }

    protected static function cut( $text, $length )
    {
        if ( function_exists( 'mb_strlen' ) && mb_strlen( $text, 'UTF-8' ) > $length )
            return mb_substr( $text, 0, $length - 1, 'UTF-8' ) . '…';
        return $text;
    }
}

}
