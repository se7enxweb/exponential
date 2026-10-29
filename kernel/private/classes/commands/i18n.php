<?php
/**
 * @description The translation fallbacks: per locale the chain of translations it falls back to, and how much of the kernel and of each extension it shows translated; --check for CI
 * @alias exp:i18n
 *
 * File containing the exp:i18n command (doc/eng-us/languages.md, "Translation fallbacks"). It uses the classes of
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
                'description' => "The translation fallbacks (i18n.ini [TranslationSettings], doc/eng-us/languages.md)\n" .
                                 "\n" .
                                 "./console exp:i18n fallbacks                         every locale: its chain, kernel and extension coverage\n" .
                                 "./console exp:i18n fallbacks --only=fallback         only the locales translated through a fallback\n" .
                                 "./console exp:i18n fallbacks --locale=esl-AR,ger-AT  each step with its rule, and every extension\n" .
                                 "./console exp:i18n fallbacks --format=json           one JSON object\n" .
                                 "./console exp:i18n check [--strict]                  cycles, chains too long, missing targets, rules\n" .
                                 "                                                     that are none: exit 1 when there is one (CI)\n" .
                                 "./console exp:i18n coverage                          the totals and the world map at the top of\n" .
                                 "                                                     doc/eng-us/languages.md and doc/ger-de/sprachen.md:\n" .
                                 "                                                     write what changed (--raster: every WebP copy)\n" .
                                 "./console exp:i18n coverage --check                  compare only: exit 1 when they are out of date\n" .
                                 "./console exp:i18n waves                             the translation waves at the top of\n" .
                                 "                                                     doc/eng-us/translation-waves.md and\n" .
                                 "                                                     doc/ger-de/uebersetzungswellen.md: write what changed\n" .
                                 "./console exp:i18n waves --check                     compare only: exit 1 when a percentage changed or a\n" .
                                 "                                                     size is more than 3 % off\n" .
                                 "./console exp:i18n waves --list                      the whole queue, one wave a line\n" .
                                 "./console exp:i18n waves --wave=ara-SA/H-help        the pages (contexts, files) of a wave, which are done\n" .
                                 "\n" .
                                 "Exit code: 0 fine, 1 problems found (check, or --check), 2 usage error.",
                'use-session'    => false,
                'use-modules'    => false,
                'use-extensions' => true
            )
        );
        $options = $this->startup( '[locale:][format:][only:][check][strict][no-extensions][raster][date:][list][wave:]', '[ACTION]', array(
            'list'          => 'With waves: print the whole queue, one wave a line',
            'wave'          => 'With waves: the pages, contexts or files of one wave (ara-SA/H-help) or work set (A03), and which are done',
            'raster'        => 'With coverage: write the WebP copy of every map again, not only of the maps that changed',
            'date'          => 'With coverage: the date of the figures when they changed (YYYY-MM-DD, default today)',
            'locale'        => 'Only these locales, comma separated (esl-AR,ger-AT): each step of the chain and every extension',
            'format'        => 'table (default) or json',
            'only'          => 'With the list: own (a translation of its own), fallback (translated through a fallback) or untranslated',
            'check'         => 'Check the fallbacks as the check action does, after the list; with coverage: compare only, change nothing',
            'strict'        => 'With check: warnings (a configured locale without a locale file, a chain without a translation) fail too',
            'no-extensions' => 'Leave out the coverage of the extensions (reading their files the first time takes seconds)'
        ) );

        $action = isset( $options['arguments'][0] ) ? strtolower( trim( $options['arguments'][0] ) ) : 'fallbacks';
        $format = isset( $options['format'] ) && is_string( $options['format'] ) ? strtolower( $options['format'] ) : 'table';
        $only = isset( $options['only'] ) && is_string( $options['only'] ) ? strtolower( $options['only'] ) : '';
        if ( !in_array( $action, array( 'fallbacks', 'check', 'coverage', 'waves' ), true ) || !in_array( $format, array( 'table', 'json' ), true )
             || !in_array( $only, array( '', 'own', 'fallback', 'untranslated' ), true ) )
        {
            $cli->error( 'Usage: ./console exp:i18n fallbacks|check|coverage [--locale=...] [--format=table|json] [--only=own|fallback|untranslated] [--strict] [--raster] (see --help)' );
            $this->shutdown( self::EXIT_USAGE );
            return;
        }
        if ( $action === 'coverage' )
        {
            $this->shutdown( $this->coverage( $options ) );
            return;
        }
        if ( $action === 'waves' )
        {
            $this->shutdown( $this->waves( $options ) );
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
     * The coverage action: the totals and maps of the language pages (expI18nCoverageMap), written or, with
     * --check, compared.
     *
     * @param array $options
     * @return int exit code
     */
    protected function coverage( array $options )
    {
        $cli = $this->cli();
        $date = isset( $options['date'] ) && is_string( $options['date'] ) && $options['date'] !== '' ? $options['date'] : date( 'Y-m-d' );
        if ( !preg_match( '/^\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12]\d|3[01])$/', $date ) )
        {
            $cli->error( '--date must be YYYY-MM-DD' );
            return self::EXIT_USAGE;
        }
        $root = defined( 'EXP_ROOT_DIR' ) ? EXP_ROOT_DIR : getcwd();
        $map = new \expI18nCoverageMap( $root );
        try
        {
            $data = $map->data();
            $t = $data['translations'];
            $l = $data['locales'];
            $cli->output( sprintf( 'Administration translations: %d (%d complete)', $t['total'], $t['complete'] ) );
            $cli->output( sprintf( 'Locales: %d files (%d variants, %d test), %d language codes, %d right to left',
                                   $l['files'], $l['variants'], $l['test'], $l['languages'], $l['rtl'] ) );
            $cli->output( sprintf( 'Countries and territories: %d, %d of them with the administration in their language',
                                   count( $data['countries'] ), $data['covered'] ) );
            $cli->output( sprintf( 'ISO 3166-1 codes without a locale: %d by design (%s), %d not yet%s',
                                   count( $data['without']['design'] ), implode( ', ', array_keys( $data['without']['design'] ) ) ?: '-',
                                   count( $data['without']['missing'] ), $data['without']['missing'] ? ' (' . implode( ', ', $data['without']['missing'] ) . ')' : '' ) );
            $cli->output( sprintf( 'Languages: %d known, %d shown translated, %d not translated',
                                   count( $data['languages'] ), $data['supported'], count( $data['languages'] ) - $data['supported'] ) );
            if ( !empty( $options['check'] ) )
            {
                $problems = $map->check( $date );
                foreach ( $problems as $problem )
                    $cli->error( $problem );
                $cli->output( $problems ? 'FAIL: ' . count( $problems ) . ' out of date' : 'PASS: the totals and maps are current' );
                return $problems ? self::EXIT_PROBLEMS : self::EXIT_OK;
            }
            $result = $map->write( $date, !empty( $options['raster'] ) );
        }
        catch ( \RuntimeException $e )
        {
            $cli->error( $e->getMessage() );
            return self::EXIT_USAGE;
        }
        foreach ( $result['written'] as $path )
            $cli->output( 'written: ' . $path );
        foreach ( $result['errors'] as $error )
            $cli->error( $error );
        if ( !$result['written'] && !$result['errors'] )
            $cli->output( 'Nothing changed.' );
        return $result['errors'] ? self::EXIT_PROBLEMS : self::EXIT_OK;
    }

    /**
     * The waves action: the translation waves of the help pages (expI18nTranslationWaves), written, compared with
     * --check, the whole queue with --list, or one wave with --wave.
     *
     * @param array $options
     * @return int exit code
     */
    protected function waves( array $options )
    {
        $cli = $this->cli();
        $date = isset( $options['date'] ) && is_string( $options['date'] ) && $options['date'] !== '' ? $options['date'] : date( 'Y-m-d' );
        if ( !preg_match( '/^\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12]\d|3[01])$/', $date ) )
        {
            $cli->error( '--date must be YYYY-MM-DD' );
            return self::EXIT_USAGE;
        }
        $root = defined( 'EXP_ROOT_DIR' ) ? EXP_ROOT_DIR : getcwd();
        $waves = new \expI18nTranslationWaves( $root );
        if ( isset( $options['wave'] ) && is_string( $options['wave'] ) && $options['wave'] !== '' )
        {
            $wave = $waves->wave( $options['wave'] );
            if ( $wave === null )
            {
                $cli->error( 'No such wave or work set: ' . $options['wave'] . ' (A01, E, H-help, X01 ..., or <locale>/<set>)' );
                return self::EXIT_USAGE;
            }
            $open = 0;
            foreach ( $wave['items'] as $item )
            {
                $mark = $item['done'] === null ? '  ' : ( $item['done'] ? '+ ' : '- ' );
                if ( $item['done'] === false )
                    $open++;
                $cli->output( $mark . $item['item'] );
            }
            $cli->output( count( $wave['items'] ) . ' items' . ( $wave['locale'] !== '' ? ", $open not translated in " . $wave['locale'] . ' (- open, + done)' : '' ) );
            return self::EXIT_OK;
        }
        $data = $waves->data();
        if ( !empty( $options['list'] ) )
        {
            foreach ( $data['queue'] as $wave )
            {
                $s = $wave['size'];
                $cli->output( sprintf( '%6d  %d  %-34s %-10s messages %d, pages %d, entries %d, words %d, left %d', $wave['number'], $wave['phase'], $wave['id'],
                                       $wave['kind'], $s['messages'], $s['pages'], $s['entries'], $s['words'], $s['missing'] ) );
            }
            return self::EXIT_OK;
        }
        $tiers = $data['help']['tiers'];
        $cli->output( sprintf( 'Administration: %d strings in %d parts; shipped extensions: %d strings', $data['admin']['messages'], count( $data['admin']['parts'] ), array_sum( $data['extensions'] ) ) );
        foreach ( $tiers as $tier => $figures )
            $cli->output( sprintf( 'Help, %-9s %5d pages, %5d entries, %8d words, %3d parts', $tier . ':', $figures['pages'], $figures['entries'], $figures['words'], $figures['parts'] ) );
        $cli->output( sprintf( 'Translations: %d; languages without one: %d; waves: %d', count( $data['status'] ), count( $data['untranslated'] ), count( $data['queue'] ) ) );
        if ( !empty( $options['check'] ) )
        {
            $problems = $waves->check( $date );
            foreach ( $problems as $problem )
                $cli->error( $problem );
            $cli->output( $problems ? 'FAIL: ' . count( $problems ) . ' out of date' : 'PASS: the translation waves are current' );
            return $problems ? self::EXIT_PROBLEMS : self::EXIT_OK;
        }
        $written = $waves->write( $date );
        foreach ( $written as $path )
            $cli->output( 'written: ' . $path );
        if ( !$written )
            $cli->output( 'Nothing changed.' );
        return self::EXIT_OK;
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
