<?php
/**
 * File containing the expI18nFallbackReport class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The translation fallbacks of every locale, for ./console exp:i18n and Setup > Interface languages: the chain each
 * locale falls back through (expI18nFallbackResolver), the rule of each step, how much of the kernel's interface it
 * then shows translated (expUISwitchLanguageCatalogue) and, when asked, how much of each extension's.
 *
 * An extension's coverage is that of the files the translator loads for the locale from the extension's translations
 * directory (its own, else the first of the chain, or all of the chain with FallbackMode=merge) against the
 * extension's source messages: its untranslated/translation.ts, or every message of its files when it has none.
 * Reading the 500 extension files takes seconds, so that part is cached in <cache directory>/i18n/fallback-report.json,
 * keyed by a signature of the fallbacks and the names, sizes and times of the files. Each file is counted as a mask of
 * the source messages it translates, kept per file by the catalogue (expUISwitchLanguageCatalogue::cachedMask()), so a
 * changed file is the only one read again when the report is built anew.
 *
 * @package kernel
 */
class expI18nFallbackReport
{
    /** the format of the cache file */
    const FORMAT = 1;

    /** @var expI18nFallbackResolver */
    protected $resolver;

    /** @var expUISwitchLanguageCatalogue */
    protected $catalogue;

    /** @var array|null name => translations directory of each extension */
    protected $extensionRoots;

    /** @var string|null */
    protected $cacheDirectory;

    /** @var array|null */
    protected $extensionCoverage;

    /**
     * @param expI18nFallbackResolver|null $resolver default: the installation's
     * @param expUISwitchLanguageCatalogue|null $catalogue default: the installation's, with $resolver
     * @param array|null $extensionRoots name => translations directory; null: those of site.ini
     *        [RegionalSettings] TranslationExtensions[]
     * @param string|null $cacheDirectory '' for no cache file, null for the installation's cache directory
     */
    public function __construct( $resolver = null, $catalogue = null, ?array $extensionRoots = null, $cacheDirectory = null )
    {
        $this->resolver = $resolver instanceof expI18nFallbackResolver ? $resolver : expI18nFallbackResolver::instance();
        $this->catalogue = $catalogue instanceof expUISwitchLanguageCatalogue ? $catalogue
                         : new expUISwitchLanguageCatalogue( '', $cacheDirectory, null, $this->resolver );
        $this->extensionRoots = $extensionRoots;
        $this->cacheDirectory = $cacheDirectory;
    }

    /** @return expI18nFallbackResolver */
    public function resolver()
    {
        return $this->resolver;
    }

    /**
     * The translations directory of each extension the translator reads (site.ini [RegionalSettings]
     * TranslationExtensions[]), name => path, sorted by name.
     *
     * @return array
     */
    public function extensionRoots()
    {
        if ( $this->extensionRoots === null )
        {
            $this->extensionRoots = array();
            if ( class_exists( 'eZINI' ) && class_exists( 'eZExtension' ) )
            {
                $extensions = (array)eZINI::instance()->variable( 'RegionalSettings', 'TranslationExtensions' );
                foreach ( eZExtension::expandedPathList( $extensions, 'translations' ) as $path )
                {
                    if ( is_dir( $path ) )
                        $this->extensionRoots[basename( dirname( $path ) )] = $path;
                }
            }
            ksort( $this->extensionRoots, SORT_STRING );
        }
        return $this->extensionRoots;
    }

    /**
     * One row per locale: locale, native, english, http (its HTTP language code), own (a kernel translation of its own), coverage (kernel, integer
     * percentage or null), from (the translation counted when not its own, else ''), chain (list of hash( target,
     * rule, translated )), problem ('' or cycle or depth), and with $extensions: extensions (name => percentage or
     * null) and extension_summary (hash( total, translated, complete, average )).
     *
     * @param string[]|null $locales default: every locale of share/locale (variants left out)
     * @param bool $extensions
     * @return array
     */
    public function rows( ?array $locales = null, $extensions = false )
    {
        $entries = $this->catalogue->entries();
        if ( $locales === null )
        {
            $locales = array();
            foreach ( array_keys( $entries ) as $code )
            {
                if ( strpos( $code, '@' ) === false )
                    $locales[] = $code;
            }
        }
        $coverage = $extensions ? $this->extensionCoverage() : array();
        $rows = array();
        foreach ( $locales as $locale )
        {
            $locale = (string)$locale;
            $entry = isset( $entries[$locale] ) ? $entries[$locale] : null;
            $explained = $this->resolver->explain( $locale );
            $chain = array();
            foreach ( $explained['steps'] as $step )
                $chain[] = array( 'target' => $step['target'], 'rule' => $step['rule'],
                                  'translated' => $step['target'] !== expI18nFallbackResolver::SOURCE && $this->resolver->hasTranslation( $step['target'] ) );
            $row = array( 'locale' => $locale,
                          'native' => $entry ? (string)$entry['native'] : '',
                          'english' => $entry ? (string)$entry['english'] : '',
                          'http' => $entry ? (string)$entry['http'] : '',
                          'own' => $this->resolver->hasTranslation( $locale ),
                          'coverage' => $entry ? $entry['coverage'] : null,
                          'from' => $entry && isset( $entry['fallback'] ) ? (string)$entry['fallback'] : '',
                          'chain' => $chain,
                          'problem' => $explained['problem'],
                          'problem_target' => $explained['problem_target'] );
            if ( $extensions )
            {
                $row['extensions'] = isset( $coverage[$locale] ) ? $coverage[$locale] : $this->extensionCoverageOf( $locale );
                $row['extension_summary'] = self::summary( $row['extensions'] );
            }
            $rows[] = $row;
        }
        return $rows;
    }

    /**
     * What a row's locale shows: own (a kernel translation of its own, or English, the language of the source
     * texts), fallback (the translation of its chain) or untranslated (the source texts).
     *
     * @param array $row
     * @return string
     */
    public static function kind( array $row )
    {
        if ( $row['own'] || strpos( $row['locale'], 'eng-' ) === 0 )
            return 'own';
        return $row['from'] !== '' ? 'fallback' : 'untranslated';
    }

    /**
     * The rows of one kind (kind()), or all of them for ''.
     *
     * @param array $rows
     * @param string $kind
     * @return array
     */
    public static function filterRows( array $rows, $kind )
    {
        if ( $kind === '' )
            return $rows;
        return array_values( array_filter( $rows, function ( $row ) use ( $kind ) {
            return self::kind( $row ) === $kind;
        } ) );
    }

    /**
     * How many rows of each kind: own, fallback, untranslated.
     *
     * @param array $rows
     * @return array
     */
    public static function counts( array $rows )
    {
        $counts = array( 'own' => 0, 'fallback' => 0, 'untranslated' => 0 );
        foreach ( $rows as $row )
            $counts[self::kind( $row )]++;
        return $counts;
    }

    /**
     * total, translated (with a file through the chain), complete (100) and average (of the translated, rounded
     * down; null with none) of an extension coverage map.
     *
     * @param array $coverage name => percentage or null
     * @return array
     */
    public static function summary( array $coverage )
    {
        $translated = array_filter( $coverage, function ( $value ) { return $value !== null; } );
        return array( 'total' => count( $coverage ), 'translated' => count( $translated ),
                      'complete' => count( array_filter( $translated, function ( $value ) { return $value >= 100; } ) ),
                      'average' => $translated ? (int)floor( array_sum( $translated ) / count( $translated ) ) : null );
    }

    /**
     * The problems of the fallbacks (expI18nFallbackResolver::problems()) of every configured locale and every
     * locale of share/locale.
     *
     * @return array
     */
    public function problems()
    {
        $locales = array_keys( $this->resolver->configured() );
        foreach ( array_keys( $this->catalogue->entries() ) as $code )
            $locales[] = $code;
        $problems = $this->resolver->problems( array_values( array_unique( $locales ) ) );
        // one line per problem, not one per locale whose chain passes through the cycle
        $unique = array();
        foreach ( $problems as $problem )
            $unique[md5( json_encode( $problem ) )] = $problem;
        return array_values( $unique );
    }

    /**
     * Every locale's coverage of every extension, locale => name => percentage or null, cached.
     *
     * @return array
     */
    public function extensionCoverage()
    {
        if ( $this->extensionCoverage !== null )
            return $this->extensionCoverage;
        $signature = $this->extensionSignature();
        $file = $this->cacheFile();
        if ( $file !== '' && is_file( $file ) )
        {
            $data = json_decode( (string)@file_get_contents( $file ), true );
            if ( is_array( $data ) && isset( $data['signature'], $data['coverage'] ) && $data['signature'] === $signature && is_array( $data['coverage'] ) )
                return $this->extensionCoverage = $data['coverage'];
        }
        $coverage = array();
        foreach ( array_keys( $this->catalogue->entries() ) as $locale )
        {
            if ( strpos( $locale, '@' ) === false )
                $coverage[$locale] = $this->extensionCoverageOf( $locale );
        }
        $this->catalogue->saveMasks();
        if ( $file !== '' )
            expUISwitchLanguageCatalogue::writeCacheFile( $file, json_encode( array( 'signature' => $signature, 'coverage' => $coverage ) ) );
        return $this->extensionCoverage = $coverage;
    }

    /** @var array root => source keys */
    private $sourceKeys = array();

    /** @var array root => source key => position */
    private $sourceIndexes = array();

    /** @var array root => stamp of its source messages, see sourceStampOf() */
    private $sourceStamps = array();

    /** @var array file => hash( mask, any, count ), see expUISwitchLanguageCatalogue::cachedMask() */
    private $masks = array();

    /**
     * One locale's coverage of every extension, name => percentage or null (no file through the chain: the source
     * texts). English locales are the source texts: 100.
     *
     * @param string $locale
     * @return array
     */
    public function extensionCoverageOf( $locale )
    {
        $result = array();
        $chain = $this->resolver->chain( $locale );
        $merge = $this->resolver->mode() === 'merge';
        foreach ( $this->extensionRoots() as $name => $root )
        {
            if ( strpos( $locale, 'eng-' ) === 0 )
            {
                $result[$name] = 100;
                continue;
            }
            $files = array();
            foreach ( array_merge( array( $locale ), $chain ) as $candidate )
            {
                if ( $candidate === expI18nFallbackResolver::SOURCE )
                    continue;
                $path = $root . '/' . $candidate . '/translation.ts';
                if ( is_file( $path ) )
                {
                    $files[] = $path;
                    if ( !$merge )
                        break;
                }
            }
            if ( !$files )
            {
                $result[$name] = null;
                continue;
            }
            // the translated source messages of the files together, as masks (a byte per source message), each
            // kept per file by the catalogue: a changed file is the only one read again
            $sourceStamp = $this->sourceStampOf( $root );
            $indexOf = function () use ( $root ) {
                return $this->sourceIndexOf( $root );
            };
            $mask = '';
            $any = false;
            $count = 0;
            foreach ( $files as $path )
            {
                if ( !isset( $this->masks[$path] ) )
                    $this->masks[$path] = $this->catalogue->cachedMask( $path, $sourceStamp, $indexOf );
                $mask = $mask === '' ? $this->masks[$path]['mask'] : ( $mask | $this->masks[$path]['mask'] );
                $any = $any || $this->masks[$path]['any'];
                $count = $this->masks[$path]['count'];
            }
            $result[$name] = expUISwitchLanguageCatalogue::maskCoveragePercent( $mask, $count, $any );
        }
        return $result;
    }

    /**
     * What an extension's source messages are made of, for the masks (expUISwitchLanguageCatalogue::cachedMask()):
     * the stamp of its untranslated/translation.ts, or of every file of it when it has none.
     *
     * @param string $root
     * @return string
     */
    protected function sourceStampOf( $root )
    {
        if ( isset( $this->sourceStamps[$root] ) )
            return $this->sourceStamps[$root];
        $untranslated = $root . '/untranslated/translation.ts';
        if ( is_file( $untranslated ) )
            return $this->sourceStamps[$root] = 'u:' . expUISwitchLanguageCatalogue::fileStamp( $untranslated );
        $parts = array();
        $files = (array)glob( $root . '/*/translation.ts' );
        sort( $files, SORT_STRING );
        foreach ( $files as $file )
            $parts[] = $file . ':' . expUISwitchLanguageCatalogue::fileStamp( $file );
        return $this->sourceStamps[$root] = 'a:' . md5( implode( "\n", $parts ) );
    }

    /**
     * The source messages of an extension as key => position.
     *
     * @param string $root
     * @return array
     */
    protected function sourceIndexOf( $root )
    {
        if ( !isset( $this->sourceIndexes[$root] ) )
            $this->sourceIndexes[$root] = array_flip( array_keys( $this->sourceKeysOf( $root ) ) );
        return $this->sourceIndexes[$root];
    }

    /**
     * The source messages of an extension: its untranslated/translation.ts, or every message of its files.
     *
     * @param string $root
     * @return array
     */
    protected function sourceKeysOf( $root )
    {
        if ( isset( $this->sourceKeys[$root] ) )
            return $this->sourceKeys[$root];
        $untranslated = $root . '/untranslated/translation.ts';
        if ( is_file( $untranslated ) )
            return $this->sourceKeys[$root] = $this->catalogue->messageKeys( $untranslated, false );
        $keys = array();
        foreach ( (array)glob( $root . '/*/translation.ts' ) as $file )
            $keys += $this->catalogue->messageKeys( $file, false );
        return $this->sourceKeys[$root] = $keys;
    }

    /**
     * The fallbacks, and the names, sizes and times of every extension translation file.
     *
     * @return string
     */
    public function extensionSignature()
    {
        clearstatcache();
        $parts = array( self::FORMAT, $this->resolver->signature(), $this->catalogue->signature() );
        foreach ( $this->extensionRoots() as $name => $root )
        {
            $files = (array)glob( $root . '/*/translation.ts' );
            sort( $files, SORT_STRING );
            foreach ( $files as $file )
            {
                $stat = @stat( $file );
                if ( $stat )
                    $parts[] = $file . ':' . $stat['size'] . ':' . $stat['mtime'];
            }
        }
        return md5( implode( "\n", $parts ) );
    }

    protected function cacheFile()
    {
        if ( $this->cacheDirectory === '' )
            return '';
        $directory = $this->cacheDirectory;
        if ( $directory === null )
        {
            if ( !class_exists( 'eZSys' ) )
                return '';
            $directory = eZSys::cacheDirectory();
        }
        return rtrim( $directory, '/' ) . '/i18n/fallback-report.json';
    }
}
