<?php
/**
 * File containing the expUISwitchLanguageCatalogue class
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * What the interface language choice knows about every locale of the installation: its names (share/locale/<code>.ini
 * LanguageName and InternationalLanguageName), its HTTP code ([HTTP] ContentLanguage) and how complete the kernel's
 * translation of the interface is (share/translations/<code>/translation.ts against the source catalogue
 * share/translations/untranslated/translation.ts). A locale without a translation of its own counts with the first
 * translation of its fallback chain (expI18nFallbackResolver: FallbackLanguages[], the rules of FallbackRules[], the
 * automatic same-language fallback; ger-AT to ger-DE), because the translator loads that one in its place. With
 * FallbackMode=merge it counts with every translation of the chain together.
 *
 * Each translation file is read on its own and kept only as a mask of the source messages it translates, so the
 * memory stays flat however many translations there are. A well-formed file is read by string search
 * (scanMessageKeys(), about a third of the time of a DOM node per message), any other streamed with XMLReader. The masks are
 * kept per file in <cache directory>/uiswitch/translation-masks.json (cachedMask()), so a changed translation reads
 * only itself again. The result is cached: in memory for the process and in
 * <cache directory>/uiswitch/language-catalogue.json, both keyed by a signature of the names, sizes and modification
 * times of the files read. A changed or added file builds the catalogue again on its next use; nothing needs clearing.
 * The cache files get the installation's file mode (writeCacheFile()), so root (Velocity) and the site user (PHP-FPM)
 * can both replace them.
 * Guide: doc/eng-us/features/6.0/ui-switch-languages.md
 */
class expUISwitchLanguageCatalogue
{
    const LOCALE_PATTERN = '/^[a-z]{3}-[A-Z]{2}(@[a-z0-9]{1,16})?$/';

    /** the format of the cache file; a new format number builds the catalogue again */
    const FORMAT = 5;

    /** @var array signature => catalogue, for the life of the process (Velocity workers) */
    private static $memory = array();

    /** @var array signature => reader language => code => name */
    private static $readerMemory = array();

    /** @var int the request of the process (expUISwitch::beginRequest() counts it up): see signature() */
    private static $generation = 0;

    /** @var string the installation root the share/ paths are relative to; '' is the working directory */
    protected $root;

    /** @var string|null where the cache file goes; null: the kernel's cache directory, '' : no cache file */
    protected $cacheDirectory;

    /** @var string|null the signature of the files, see signature() */
    protected $signature = null;

    /** @var string|null the times of the directories the signature was computed with */
    protected $directoryStamp = null;

    /** @var int|null the request generation the directory times were last looked at in */
    protected $checkedGeneration = null;

    /** @var array|null locale => translation it falls back to; null: read from i18n.ini when first needed */
    protected $fallbacks;

    /** @var expI18nFallbackResolver|null */
    protected $resolver;

    /**
     * @param string $root
     * @param string|null $cacheDirectory
     * @param array|null $fallbacks locale => translation, as i18n.ini [TranslationSettings] FallbackLanguages[], with
     *        no other rule; null reads the settings (expI18nFallbackResolver::settings())
     * @param expI18nFallbackResolver|null $resolver the resolver to use instead (it then decides alone)
     */
    public function __construct( $root = '', $cacheDirectory = null, $fallbacks = null, $resolver = null )
    {
        $this->root = (string)$root;
        $this->cacheDirectory = $cacheDirectory;
        $this->fallbacks = is_array( $fallbacks ) ? $fallbacks : null;
        $this->resolver = $resolver instanceof expI18nFallbackResolver ? $resolver : null;
    }

    /**
     * The fallback resolver the catalogue counts with: the one given, one of the map given (without other rules),
     * or the installation's (its settings, with the translations and locales below the catalogue's root).
     *
     * @return expI18nFallbackResolver
     */
    public function resolver()
    {
        if ( $this->resolver === null )
        {
            $paths = array( 'repository' => $this->path( 'share/translations' ), 'locale_directory' => $this->path( 'share/locale' ) );
            if ( $this->fallbacks !== null )
                $this->resolver = new expI18nFallbackResolver( $this->fallbacks, array(), array_merge( $paths, array( 'automatic' => false ) ) );
            elseif ( $this->root === '' && class_exists( 'eZINI' ) )
                $this->resolver = expI18nFallbackResolver::instance();
            else
            {
                $settings = expI18nFallbackResolver::settings();
                $this->resolver = new expI18nFallbackResolver( $settings['configured'], $settings['rules'], array_merge( $settings['options'], $paths ) );
            }
        }
        return $this->resolver;
    }

    /**
     * The translation fallbacks that point to another translation, locale => translation: the ones to
     * "untranslated" or eng-GB (the source texts) are left out, as the translator treats them as none.
     *
     * @return array
     */
    public function fallbacks()
    {
        $map = array();
        foreach ( $this->resolver()->configured() as $code => $target )
        {
            $isTranslation = is_string( $target ) && preg_match( self::LOCALE_PATTERN, $target ) && $target !== 'eng-GB';
            if ( is_string( $code ) && $isTranslation && $target !== $code )
                $map[$code] = $target;
        }
        ksort( $map, SORT_STRING );
        return $map;
    }

    /**
     * Forgets the in-memory catalogues (tests).
     */
    public static function resetMemory()
    {
        self::$memory = array();
        self::$readerMemory = array();
    }

    /**
     * Start of a request (expUISwitch::beginRequest()): every catalogue of the process looks at the times of its
     * directories again at its next use, so a locale or translation added or replaced while a persistent worker runs
     * (Velocity) is seen at the next request, without a restart.
     */
    public static function beginRequest()
    {
        self::$generation++;
    }

    protected function path( $relative )
    {
        return ( $this->root !== '' ? rtrim( $this->root, '/' ) . '/' : '' ) . $relative;
    }

    /**
     * Every locale code with a share/locale/<code>.ini, sorted by code. Variants (eng-GB@euro, nor-NO@intl) are only
     * included when $variants is true: they change formats, not the language.
     *
     * @param bool $variants
     * @return string[]
     */
    public function localeCodes( $variants = false )
    {
        $codes = array();
        foreach ( (array)glob( $this->path( 'share/locale/*.ini' ) ) as $file )
        {
            $code = basename( $file, '.ini' );
            if ( !preg_match( self::LOCALE_PATTERN, $code ) )
                continue;
            if ( !$variants && strpos( $code, '@' ) !== false )
                continue;
            $codes[] = $code;
        }
        sort( $codes, SORT_STRING );
        return $codes;
    }

    /**
     * The files the catalogue is built from, with their sizes and times: a change in any of them changes the
     * signature.
     *
     * @return string
     */
    public function signature()
    {
        // Once per request: the modification times of share/locale, share/translations and each translation
        // directory (a file added, removed or replaced by a rename changes its directory's time). Only when they
        // changed are the names, sizes and times of every file read again.
        if ( $this->signature !== null && $this->checkedGeneration === self::$generation )
            return $this->signature;
        $this->checkedGeneration = self::$generation;
        $stamp = $this->directoryStamp();
        if ( $this->signature !== null && $stamp === $this->directoryStamp )
            return $this->signature;
        $this->directoryStamp = $stamp;
        clearstatcache();
        $parts = array( self::FORMAT, $this->resolver()->signature() );
        $files = array_merge( (array)glob( $this->path( 'share/locale/*.ini' ) ),
                              (array)glob( $this->path( 'share/translations/*/translation.ts' ) ) );
        sort( $files, SORT_STRING );
        foreach ( $files as $file )
        {
            $stat = @stat( $file );
            if ( $stat )
                $parts[] = $file . ':' . $stat['size'] . ':' . $stat['mtime'];
        }
        return $this->signature = md5( implode( "\n", $parts ) );
    }

    /**
     * The modification times of the directories the catalogue reads, fresh from the file system (not PHP's stat
     * cache, which a persistent worker keeps between requests).
     *
     * @return string
     */
    protected function directoryStamp()
    {
        $dirs = array_merge( array( $this->path( 'share/locale' ), $this->path( 'share/translations' ) ),
                             (array)glob( $this->path( 'share/translations/*' ), GLOB_ONLYDIR ) );
        $parts = array();
        foreach ( $dirs as $dir )
        {
            clearstatcache( true, $dir );
            $parts[] = $dir . ':' . (int)@filemtime( $dir );
        }
        return implode( "\n", $parts );
    }

    /**
     * The catalogue: code => hash( native, english, http, coverage, fallback ), coverage an integer percentage or null
     * when the locale has no translation (English, the language of the source texts, is always 100), fallback the
     * translation it was counted with when it has none of its own (its fallback chain), else '', chain the
     * locale's fallback chain (expI18nFallbackResolver::chain(), 'untranslated' left out).
     *
     * @return array
     */
    public function entries()
    {
        $signature = $this->signature();
        if ( isset( self::$memory[$signature] ) )
            return self::$memory[$signature];

        $file = $this->cacheFile();
        if ( $file !== '' && is_file( $file ) )
        {
            $data = json_decode( (string)@file_get_contents( $file ), true );
            if ( is_array( $data ) && isset( $data['signature'], $data['entries'] ) && $data['signature'] === $signature && is_array( $data['entries'] ) )
                return self::$memory[$signature] = $data['entries'];
        }

        $entries = $this->build();
        if ( $file !== '' )
            $this->store( $file, json_encode( array( 'signature' => $signature, 'entries' => $entries ) ) );
        return self::$memory[$signature] = $entries;
    }

    /**
     * The entry of one locale, or null for a code the installation does not know.
     *
     * @param string $code
     * @return array|null
     */
    public function entry( $code )
    {
        $entries = $this->entries();
        return is_string( $code ) && isset( $entries[$code] ) ? $entries[$code] : null;
    }

    /**
     * Builds the catalogue from the files.
     *
     * @return array
     */
    public function build()
    {
        // Every translation is read on its own and kept only as a mask of the source messages it translates (a byte
        // each, some 19 KB), never as its messages: with 125 complete translations of 19,000 messages their key
        // arrays took more than a gigabyte together.
        // The masks are kept per file (masks()), so a changed translation reads only that file again; the source
        // catalogue is only read when a mask has to be built.
        $sourceFile = $this->path( 'share/translations/untranslated/translation.ts' );
        $sourceIndex = null;
        $indexOf = function () use ( &$sourceIndex, $sourceFile ) {
            if ( $sourceIndex === null )
                $sourceIndex = array_flip( array_keys( $this->messageKeys( $sourceFile, false ) ) );
            return $sourceIndex;
        };
        $sourceStamp = self::fileStamp( $sourceFile );
        $entries = array();
        $maskOf = array();
        $resolver = $this->resolver();
        $merge = $resolver->mode() === 'merge';
        foreach ( $this->localeCodes( true ) as $code )
        {
            $names = $this->readLocaleNames( $this->path( 'share/locale/' . $code . '.ini' ) );
            $coverage = null;
            $fallback = '';
            $chain = array_values( array_diff( $resolver->chain( $code ), array( expI18nFallbackResolver::SOURCE ) ) );
            if ( strpos( $code, 'eng-' ) === 0 )
                $coverage = 100;
            else
            {
                // As eZTSTranslator: the locale's own file, then its chain; a variant (ger-DE@euro) also loads the
                // translation of its locale (ger-DE) and that one's chain.
                $base = strpos( $code, '@' ) !== false ? substr( $code, 0, strpos( $code, '@' ) ) : $code;
                $candidates = array_merge( array( $code ), $chain );
                if ( $base !== $code )
                    $candidates = array_merge( $candidates, array( $base ), $resolver->chain( $base ) );
                $files = array();
                foreach ( array_unique( $candidates ) as $candidate )
                {
                    if ( $candidate === expI18nFallbackResolver::SOURCE )
                        continue;
                    $ts = $this->path( 'share/translations/' . $candidate . '/translation.ts' );
                    if ( !is_file( $ts ) )
                        continue;
                    if ( !$files && $candidate !== $code )
                        $fallback = $candidate;
                    $files[] = $ts;
                    if ( !$merge )
                        break;
                }
                if ( $files )
                {
                    // the translated source messages of the files together: the masks OR-ed, a byte per message
                    $mask = '';
                    $count = 0;
                    $any = false;
                    foreach ( $files as $ts )
                    {
                        if ( !isset( $maskOf[$ts] ) )
                            $maskOf[$ts] = $this->cachedMask( $ts, $sourceStamp, $indexOf );
                        $mask = $mask === '' ? $maskOf[$ts]['mask'] : ( $mask | $maskOf[$ts]['mask'] );
                        $any = $any || $maskOf[$ts]['any'];
                        $count = $maskOf[$ts]['count'];
                    }
                    $coverage = self::maskCoveragePercent( $mask, $count, $any );
                }
            }
            $entries[$code] = array( 'native' => $names['LanguageName'], 'english' => $names['InternationalLanguageName'],
                                     'http' => $names['ContentLanguage'], 'coverage' => $coverage, 'fallback' => $fallback, 'chain' => $chain,
                                     'intl_native' => strpos( $code, '@' ) === false ? self::intlDisplayName( $names['ContentLanguage'], $names['ContentLanguage'] ) : '',
                                     'test' => strtolower( $names['TestLanguage'] ) === 'true' );
        }
        $this->saveMasks();
        return $entries;
    }

    /** @var array|null the mask store: file => hash( stamp, source, count, any, mask ), see cachedMask() */
    protected $maskStore = null;

    /** @var bool whether the mask store changed since it was read or last saved */
    protected $maskStoreChanged = false;

    /** @var float when the mask store was last saved (or read) */
    protected $maskStoreSaved = 0.0;

    /**
     * The size, modification time and inode of a file ('' when it is missing): a file replaced or changed has
     * another stamp.
     *
     * @param string $file
     * @return string
     */
    public static function fileStamp( $file )
    {
        clearstatcache( true, $file );
        $stat = @stat( $file );
        return $stat ? $stat['size'] . ':' . $stat['mtime'] . ':' . $stat['ino'] : '';
    }

    /**
     * translatedMask() of a translation file against a source catalogue, with the number of source messages (count),
     * kept per file in <cache directory>/uiswitch/translation-masks.json: read again only when the file's stamp
     * (fileStamp()) or that of its source catalogue changed. One changed translation (a translation landing) builds
     * only its own mask again. Used for the kernel's translations here and for the extensions' by
     * expI18nFallbackReport.
     *
     * @param string $file
     * @param string $sourceStamp what the source catalogue is: fileStamp() of it, or of every file it is made of
     * @param callable $sourceIndex function(): key => position of the source messages, called only when needed
     * @return array hash( mask, any, count )
     */
    public function cachedMask( $file, $sourceStamp, callable $sourceIndex )
    {
        $this->loadMasks();
        $stamp = self::fileStamp( $file );
        if ( isset( $this->maskStore[$file] ) )
        {
            $kept = $this->maskStore[$file];
            if ( is_array( $kept ) && isset( $kept['stamp'], $kept['source'], $kept['count'], $kept['mask'] )
                 && $kept['stamp'] === $stamp && $kept['source'] === (string)$sourceStamp )
            {
                $mask = self::decodeMask( (string)$kept['mask'] );
                if ( $mask !== null && strlen( $mask ) === (int)$kept['count'] )
                    return array( 'mask' => $mask, 'any' => !empty( $kept['any'] ), 'count' => (int)$kept['count'] );
            }
        }
        $index = $sourceIndex();
        $result = $this->translatedMask( $file, $index );
        $result['count'] = count( $index );
        if ( $stamp !== '' )
        {
            $this->maskStore[$file] = array( 'stamp' => $stamp, 'source' => (string)$sourceStamp, 'count' => $result['count'],
                                             'any' => $result['any'], 'mask' => self::encodeMask( $result['mask'] ) );
            $this->maskStoreChanged = true;
            // a long build keeps what it has read so far: a request stopped by a time limit leaves the masks it built
            if ( microtime( true ) - $this->maskStoreSaved > 5 )
                $this->saveMasks();
        }
        return $result;
    }

    /**
     * Writes the mask store when it changed, without the files that are gone.
     */
    public function saveMasks()
    {
        if ( !$this->maskStoreChanged || !is_array( $this->maskStore ) )
            return;
        $this->maskStoreChanged = false;
        $this->maskStoreSaved = microtime( true );
        $file = $this->maskFile();
        if ( $file === '' )
            return;
        foreach ( array_keys( $this->maskStore ) as $path )
        {
            if ( !is_file( $path ) )
                unset( $this->maskStore[$path] );
        }
        $this->store( $file, json_encode( array( 'format' => self::FORMAT, 'masks' => $this->maskStore ) ) );
    }

    protected function loadMasks()
    {
        if ( $this->maskStore !== null )
            return;
        $this->maskStore = array();
        $this->maskStoreSaved = microtime( true );
        $file = $this->maskFile();
        if ( $file === '' || !is_file( $file ) )
            return;
        $data = json_decode( (string)@file_get_contents( $file ), true );
        if ( is_array( $data ) && isset( $data['format'], $data['masks'] ) && $data['format'] === self::FORMAT && is_array( $data['masks'] ) )
            $this->maskStore = $data['masks'];
    }

    /**
     * The mask store's file, or '' when there is none.
     *
     * @return string
     */
    protected function maskFile()
    {
        $file = $this->cacheFile();
        return $file === '' ? '' : dirname( $file ) . '/translation-masks.json';
    }

    /**
     * A mask as text for the store: compressed (a mask is long runs of the same byte) and in base64.
     *
     * @param string $mask
     * @return string
     */
    protected static function encodeMask( $mask )
    {
        if ( function_exists( 'gzdeflate' ) )
            return 'z' . base64_encode( (string)gzdeflate( $mask, 6 ) );
        return 'b' . base64_encode( $mask );
    }

    /**
     * @param string $text encodeMask()
     * @return string|null
     */
    protected static function decodeMask( $text )
    {
        $data = base64_decode( (string)substr( $text, 1 ), true );
        if ( !is_string( $data ) )
            return null;
        if ( $text[0] === 'b' )
            return $data;
        if ( $text[0] === 'z' && function_exists( 'gzinflate' ) )
        {
            $mask = @gzinflate( $data );
            return is_string( $mask ) ? $mask : null;
        }
        return null;
    }

    /**
     * Whether a locale is a test language ([LanguageSettings] TestLanguage=true in its file, as Lorem Ipsum, qlo-ZZ):
     * the choice leaves it out of the ranked groups and of "All languages"; only AllowedValues[] offers it.
     *
     * @param string $code
     * @return bool
     */
    public function isTestLocale( $code )
    {
        $entry = $this->entry( $code );
        return is_array( $entry ) && !empty( $entry['test'] );
    }

    /**
     * Every locale's name in a reader's language (Intl), code => name, '' where Intl does not know it. Looked up once
     * per catalogue and reader language: in memory, and in <cache directory>/uiswitch/language-names-<reader>.json.
     *
     * @param string $readerLanguage an HTTP code from a locale file (de-DE)
     * @return array
     */
    public function readerNames( $readerLanguage )
    {
        $reader = preg_replace( '/[^A-Za-z0-9-]/', '', (string)$readerLanguage );
        $entries = $this->entries();
        $signature = $this->signature();
        if ( isset( self::$readerMemory[$signature][$reader] ) )
            return self::$readerMemory[$signature][$reader];
        $file = $this->cacheFile();
        if ( $file !== '' && $reader !== '' )
            $file = dirname( $file ) . '/language-names-' . $reader . '.json';
        else
            $file = '';
        if ( $file !== '' && is_file( $file ) )
        {
            $data = json_decode( (string)@file_get_contents( $file ), true );
            if ( is_array( $data ) && isset( $data['signature'], $data['names'] ) && $data['signature'] === $signature && is_array( $data['names'] ) )
                return self::$readerMemory[$signature][$reader] = $data['names'];
        }
        $names = array();
        foreach ( $entries as $code => $entry )
            $names[$code] = strpos( $code, '@' ) === false && $reader !== '' ? self::intlDisplayName( $entry['http'], $reader ) : '';
        if ( $file !== '' )
            $this->store( $file, json_encode( array( 'signature' => $signature, 'names' => $names ) ) );
        return self::$readerMemory[$signature][$reader] = $names;
    }

    /**
     * Intl's name of a language in another (Locale::getDisplayName()), or '' without the extension or when it only
     * gives the code back.
     *
     * @param string $httpCode the language (de-DE)
     * @param string $displayHttpCode the language to name it in
     * @return string
     */
    public static function intlDisplayName( $httpCode, $displayHttpCode )
    {
        if ( !class_exists( 'Locale' ) || $httpCode === '' || $displayHttpCode === '' )
            return '';
        $name = Locale::getDisplayName( (string)$httpCode, (string)$displayHttpCode );
        if ( !is_string( $name ) || $name === '' || strcasecmp( str_replace( '_', '-', $name ), (string)$httpCode ) === 0 )
            return '';
        return $name;
    }

    /**
     * The share of the source messages a translation translates, rounded down so that only a complete translation
     * says 100. A translation file without a source catalogue to compare with counts as complete when it translates
     * anything.
     *
     * @param array $sourceKeys key => true
     * @param array $translatedKeys key => true
     * @return int 0 to 100
     */
    public static function coveragePercent( array $sourceKeys, array $translatedKeys )
    {
        if ( !$sourceKeys )
            return $translatedKeys ? 100 : 0;
        $done = count( array_intersect_key( $translatedKeys, $sourceKeys ) );
        return (int)min( 100, floor( $done * 100 / count( $sourceKeys ) ) );
    }

    /**
     * coveragePercent() of a mask of translatedMask(): the share of the source messages whose byte is set.
     *
     * @param string $mask
     * @param int $sourceCount
     * @param bool $any whether the translation translates any message (for a missing source catalogue)
     * @return int 0 to 100
     */
    public static function maskCoveragePercent( $mask, $sourceCount, $any )
    {
        if ( $sourceCount < 1 )
            return $any ? 100 : 0;
        $done = substr_count( (string)$mask, "\x01" );
        return (int)min( 100, floor( $done * 100 / $sourceCount ) );
    }

    /**
     * Which source messages a translation file translates, read message by message without keeping them: mask has
     * a byte per source message ($sourceIndex key => position), "\x01" where translated, else "\x00"; any whether
     * the file translates any message at all.
     *
     * @param string $file
     * @param array $sourceIndex key => position
     * @return array hash( mask, any )
     */
    public function translatedMask( $file, array $sourceIndex )
    {
        $mask = str_repeat( "\x00", count( $sourceIndex ) );
        $any = false;
        $keys = $this->scannedKeys( $file, true );
        if ( $keys !== null )
        {
            foreach ( $keys as $key )
            {
                if ( isset( $sourceIndex[$key] ) )
                    $mask[$sourceIndex[$key]] = "\x01";
            }
            return array( 'mask' => $mask, 'any' => $keys !== array() );
        }
        $this->readMessageKeys( $file, true, function ( $key ) use ( &$mask, &$any, $sourceIndex ) {
            $any = true;
            if ( isset( $sourceIndex[$key] ) )
                $mask[$sourceIndex[$key]] = "\x01";
        } );
        return array( 'mask' => $mask, 'any' => $any );
    }

    /**
     * The messages of a Qt Linguist file, as context, source and comment joined, the key the translator uses. With
     * $translatedOnly only those with a translation that is neither empty, unfinished nor obsolete.
     *
     * @param string $file
     * @param bool $translatedOnly
     * @return array key => true
     */
    public function messageKeys( $file, $translatedOnly )
    {
        $list = $this->scannedKeys( $file, $translatedOnly );
        if ( $list !== null )
            return array_fill_keys( $list, true );
        $keys = array();
        $this->readMessageKeys( $file, $translatedOnly, function ( $key ) use ( &$keys ) {
            $keys[$key] = true;
        } );
        return $keys;
    }

    /**
     * The keys of a translation file's messages by string search (scannableText(), scanMessageKeys()), in file
     * order, or null when the file has to be streamed with XMLReader (readMessageKeys()) or is missing.
     *
     * @param string $file
     * @param bool $translatedOnly
     * @return string[]|null
     */
    protected function scannedKeys( $file, $translatedOnly )
    {
        if ( !is_file( $file ) )
            return null;
        $text = $this->scannableText( $file );
        return $text === null ? null : $this->scanMessageKeys( $text, $translatedOnly );
    }

    /**
     * Calls $each with the key of every message of a Qt Linguist file (see messageKeys()), streamed with XMLReader.
     *
     * @param string $file
     * @param bool $translatedOnly
     * @param callable $each function( string $key )
     */
    protected function eachMessageKey( $file, $translatedOnly, callable $each )
    {
        if ( !is_file( $file ) )
            return;
        $keys = $this->scannedKeys( $file, $translatedOnly );
        if ( $keys === null )
        {
            $this->readMessageKeys( $file, $translatedOnly, $each );
            return;
        }
        foreach ( $keys as $key )
            $each( $key );
    }

    /**
     * The text of a translation file when scanMessageKeys() reads it as the XML parser would, else null
     * (readMessageKeys() then streams it): a complete UTF-8 file without CDATA sections, entity declarations or
     * processing instructions after the declaration, with XML comments taken out and its line ends as the parser
     * gives them (\n). A broken file (a merge in progress) is left to the streaming reader, which counts what it read
     * before the error.
     *
     * @param string $file
     * @return string|null
     */
    protected function scannableText( $file )
    {
        $text = @file_get_contents( $file );
        if ( !is_string( $text ) || $text === '' )
            return null;
        if ( strpos( $text, '<![CDATA[' ) !== false || strpos( $text, '<!ENTITY' ) !== false || strpos( $text, '<?', 5 ) !== false )
            return null;
        if ( strpos( $text, '<!--' ) !== false )
            $text = (string)preg_replace( '/<!--.*?-->/s', '', $text );
        // only UTF-8 (the parser would convert another encoding; the scan reads the bytes as they are)
        $end = strncmp( $text, '<?xml', 5 ) === 0 ? strpos( $text, '?>' ) : false;
        if ( $end !== false && preg_match( '/encoding\s*=\s*["\']([^"\']*)/i', substr( $text, 0, $end ), $match ) && strcasecmp( $match[1], 'utf-8' ) !== 0 )
            return null;
        // Complete: it ends with </TS>, every message and context is closed (a <message/> is left to the reader too),
        // and no merge is in progress. The translator does not load a broken file at all (eZTSTranslator).
        // (scanMessageKeys() counts the messages)
        if ( substr( rtrim( substr( $text, -256 ) ), -5 ) !== '</TS>' ||strpos( $text, "\n<<<<<<< " ) !== false || strpos( $text, "\n>>>>>>> " ) !== false )
            return null;
        if ( strpos( $text, "\r" ) !== false )
            $text = str_replace( array( "\r\n", "\r" ), "\n", $text );
        return $text;
    }

    /**
     * Calls $each with the key of every message of the text of a well-formed Qt Linguist file (scannableText()),
     * found by string search: the same keys readMessageKeys() streams, in a fraction of the time (a DOM node per
     * message took a quarter of a second for each 3 MB translation; the administration has more than a hundred).
     * The text of an element is its character data without the elements inside it (Qt's <byte/>, <numerusform>),
     * with the entities resolved, as DOM's textContent.
     *
     * @param string $text
     * @param bool $translatedOnly
     * @return string[]|null the keys in file order, null when the text is not as expected (then streamed instead)
     */
    protected function scanMessageKeys( $text, $translatedOnly )
    {
        $keys = array();
        $context = '';
        // each piece ends where a message ends: what is before its <message is the end of the previous context and
        // the start of a new one, if any
        $pieces = explode( '</message>', $text );
        array_pop( $pieces );
        // every message closed (a <message/> or an unclosed one is left to the stream)
        if ( count( $pieces ) !== substr_count( $text, '<message' ) )
            return null;
        foreach ( $pieces as $piece )
        {
            $start = strrpos( $piece, '<message' );
            if ( $start === false )
                return null;
            if ( strpos( $piece, '<context' ) !== false )
            {
                $opened = strrpos( substr( $piece, 0, $start ), '<context' );
                if ( $opened !== false )
                {
                    $context = '';
                    $name = self::tagPosition( $piece, 'name', $opened );
                    if ( $name !== false && $name < $start )
                        $context = trim( (string)self::elementText( $piece, 'name', $name ) );
                }
            }
            $body = substr( $piece, $start );

            $type = '';
            $translation = '';
            $at = strpos( $body, '<translation' );
            if ( $at !== false )
            {
                $tagEnd = strpos( $body, '>', $at );
                if ( $tagEnd === false )
                    return null;
                if ( $tagEnd > $at + 12 )
                {
                    $attributes = substr( $body, $at + 12, $tagEnd - $at - 12 );
                    if ( strpos( $attributes, 'type' ) !== false && preg_match( '/\btype\s*=\s*(["\'])(.*?)\1/', $attributes, $match ) )
                        $type = $match[2];
                }
                if ( $body[$tagEnd - 1] !== '/' )
                {
                    $close = strpos( $body, '</translation>', $tagEnd );
                    if ( $close === false )
                        return null;
                    $translation = substr( $body, $tagEnd + 1, $close - $tagEnd - 1 );
                }
            }
            if ( $type === 'obsolete' )
                continue;
            if ( $translatedOnly )
            {
                if ( $type === 'unfinished' )
                    continue;
                if ( strpos( $translation, '<' ) !== false || strpos( $translation, '&' ) !== false )
                    $translation = self::decodeText( $translation );
                if ( trim( $translation ) === '' )
                    continue;
            }

            $open = strpos( $body, '<source>' );
            if ( $open !== false )
            {
                $close = strpos( $body, '</source>', $open );
                if ( $close === false )
                    return null;
                $source = substr( $body, $open + 8, $close - $open - 8 );
                if ( strpos( $source, '<' ) !== false || strpos( $source, '&' ) !== false )
                    $source = self::decodeText( $source );
            }
            else
                $source = self::childText( $body, 'source' );
            $comment = strpos( $body, '<comment' ) !== false ? self::childText( $body, 'comment' ) : '';
            $keys[] = $context . "\x1F" . $source . "\x1F" . $comment;
        }
        return $keys;
    }

    /**
     * Character data as DOM's textContent gives it: the elements inside taken out, the entities resolved.
     *
     * @param string $inner
     * @return string
     */
    protected static function decodeText( $inner )
    {
        if ( strpos( $inner, '<' ) !== false )
            $inner = strip_tags( $inner );
        if ( strpos( $inner, '&' ) !== false )
            $inner = html_entity_decode( $inner, ENT_QUOTES | ENT_XML1, 'UTF-8' );
        return $inner;
    }

    /**
     * The position of the next start tag <$name> or <$name ...> in $text from $offset (not <$nameother>), or false.
     *
     * @param string $text
     * @param string $name
     * @param int $offset
     * @return int|false
     */
    protected static function tagPosition( $text, $name, $offset )
    {
        $length = strlen( $name ) + 1;
        while ( ( $at = strpos( $text, '<' . $name, $offset ) ) !== false )
        {
            $next = isset( $text[$at + $length] ) ? $text[$at + $length] : '';
            if ( $next === '>' || $next === '/' || $next === ' ' || $next === "\n" || $next === "\t" || $next === "\r" )
                return $at;
            $offset = $at + $length;
        }
        return false;
    }

    /**
     * The text of the first <$name> element directly in a message's body, '' without one.
     *
     * @param string $body
     * @param string $name
     * @return string
     */
    protected static function childText( $body, $name )
    {
        $at = self::tagPosition( $body, $name, 0 );
        return $at === false ? '' : (string)self::elementText( $body, $name, $at );
    }

    /**
     * The text of the element whose start tag is at $at: its character data with the elements inside it taken out
     * and the entities resolved; '' for an empty element, null when it is not closed.
     *
     * @param string $text
     * @param string $name
     * @param int $at
     * @return string|null
     */
    protected static function elementText( $text, $name, $at )
    {
        $open = strpos( $text, '>', $at );
        if ( $open === false )
            return null;
        if ( $text[$open - 1] === '/' )
            return '';
        $close = strpos( $text, '</' . $name . '>', $open );
        if ( $close === false )
            return null;
        return self::decodeText( substr( $text, $open + 1, $close - $open - 1 ) );
    }

    /**
     * Calls $each with the key of every message of a Qt Linguist file, streamed with XMLReader: for the files
     * scanMessageKeys() does not read (a broken file counts what was read before the error).
     *
     * @param string $file
     * @param bool $translatedOnly
     * @param callable $each function( string $key )
     */
    protected function readMessageKeys( $file, $translatedOnly, callable $each )
    {
        if ( !is_file( $file ) || !class_exists( 'XMLReader' ) )
            return;
        $reader = new XMLReader();
        if ( !@$reader->open( $file, null, LIBXML_NONET ) )
            return;
        // A broken file (a merge in progress) counts what was read before the error, without a PHP warning for
        // each parser complaint.
        $useInternal = libxml_use_internal_errors( true );
        $context = '';
        $inName = false;
        try
        {
            // after a message the reader stands on what follows it (next()), which may be the next message itself
            // (</message><message> without a line between them): that node is looked at before reading on
            $advance = true;
            while ( !$advance || @$reader->read() )
            {
                $advance = true;
                if ( $reader->nodeType !== XMLReader::ELEMENT )
                    continue;
                if ( $reader->name === 'context' )
                    $context = '';
                elseif ( $reader->name === 'name' && $reader->depth === 2 )
                    $context = trim( $reader->readString() );
                elseif ( $reader->name === 'message' )
                {
                    $node = @$reader->expand();
                    if ( !$node instanceof DOMElement )
                        continue;
                    $source = $comment = $translation = '';
                    $type = '';
                    foreach ( $node->childNodes as $child )
                    {
                        if ( !$child instanceof DOMElement )
                            continue;
                        if ( $child->tagName === 'source' )
                            $source = $child->textContent;
                        elseif ( $child->tagName === 'comment' )
                            $comment = $child->textContent;
                        elseif ( $child->tagName === 'translation' )
                        {
                            $translation = $child->textContent;
                            $type = $child->getAttribute( 'type' );
                        }
                    }
                    // past the message and its children (at the end of the file next() fails and read() ends the loop)
                    $advance = !@$reader->next();
                    if ( $translatedOnly && ( trim( $translation ) === '' || $type === 'unfinished' || $type === 'obsolete' ) )
                        continue;
                    if ( !$translatedOnly && $type === 'obsolete' )
                        continue;
                    $each( $context . "\x1F" . $source . "\x1F" . $comment );
                }
            }
        }
        catch ( Throwable $e )
        {
            // a broken file counts what was read before the error
        }
        $reader->close();
        libxml_clear_errors();
        libxml_use_internal_errors( $useInternal );
    }

    /**
     * LanguageName, InternationalLanguageName, ContentLanguage and TestLanguage of a locale file, '' where missing. Read line by
     * line: eZINI would keep 140 instances in memory for three values each.
     *
     * @param string $file
     * @return array
     */
    public function readLocaleNames( $file )
    {
        $names = array( 'LanguageName' => '', 'InternationalLanguageName' => '', 'ContentLanguage' => '', 'TestLanguage' => '' );
        $lines = is_file( $file ) ? @file( $file, FILE_IGNORE_NEW_LINES ) : false;
        foreach ( (array)$lines as $line )
        {
            if ( preg_match( '/^(LanguageName|InternationalLanguageName|ContentLanguage|TestLanguage)=(.*)$/', trim( $line ), $match )
                 && $names[$match[1]] === '' )
                $names[$match[1]] = trim( $match[2] );
        }
        return $names;
    }

    /**
     * The cache file, or '' when there is none.
     *
     * @return string
     */
    protected function cacheFile()
    {
        if ( $this->cacheDirectory === '' )
            return '';
        $directory = $this->cacheDirectory;
        if ( $directory === null )
        {
            if ( !class_exists( 'eZSys' ) )
                return '';
            try
            {
                $directory = eZSys::cacheDirectory();
            }
            catch ( Throwable $e )
            {
                return '';
            }
        }
        return rtrim( $directory, '/' ) . '/uiswitch/language-catalogue.json';
    }

    /**
     * Writes the cache file atomically, with the installation's permissions. A cache that cannot be written is no
     * error: the catalogue is then kept in memory only.
     *
     * @param string $file
     * @param string|false $data
     */
    protected function store( $file, $data )
    {
        self::writeCacheFile( $file, $data );
    }

    /**
     * Writes a cache file atomically, its directory with the installation's directory mode (site.ini [FileSettings]
     * StorageDirPermissions) and the file with its file mode (StorageFilePermissions, within the installation's
     * limits): the other user that runs the installation (root for Velocity and the command line, the site user for
     * PHP-FPM) can then replace it. A cache that cannot be written is no error: the result is kept in memory only.
     *
     * @param string $file
     * @param string|false $data
     * @return bool whether the file was written
     */
    public static function writeCacheFile( $file, $data )
    {
        if ( !is_string( $data ) || !class_exists( 'eZFile' ) || !class_exists( 'eZDir' ) )
            return false;
        try
        {
            if ( !@eZFile::create( basename( $file ), dirname( $file ), $data, true ) )
                return false;
            if ( class_exists( 'eZINI' ) )
            {
                $mode = eZFile::fileMode( octdec( (string)eZINI::instance()->variable( 'FileSettings', 'StorageFilePermissions' ) ) );
                if ( $mode > 0 && ( @fileperms( $file ) & 0777 ) !== $mode )
                    @chmod( $file, $mode );
            }
            return true;
        }
        catch ( Throwable $e )
        {
            return false;
        }
    }

    /**
     * Sorts labels for people: by the collation of a locale (Intl's Collator) when the extension is there and
     * $useIntl, else by the labels with their accents and case folded, then byte order.
     *
     * @param array $labels value => label
     * @param string $httpLocale the locale of the reader, as an HTTP code (de-DE)
     * @param bool $useIntl
     * @return string[] the values in order
     */
    public static function sortValuesByLabel( array $labels, $httpLocale, $useIntl = true )
    {
        if ( $useIntl && class_exists( 'Collator' ) )
        {
            $name = str_replace( '-', '_', (string)$httpLocale );
            try
            {
                $collator = new Collator( $name !== '' ? $name : 'root' );
            }
            catch ( Throwable $e )
            {
                $collator = null;
            }
            if ( $collator instanceof Collator )
            {
                $collator->setStrength( Collator::SECONDARY );
                uksort( $labels, function ( $a, $b ) use ( $labels, $collator ) {
                    $order = $collator->compare( (string)$labels[$a], (string)$labels[$b] );
                    return is_int( $order ) && $order !== 0 ? $order : strcmp( (string)$a, (string)$b );
                } );
                return array_map( 'strval', array_keys( $labels ) );
            }
        }
        $folded = array();
        foreach ( $labels as $value => $label )
            $folded[$value] = self::foldForSorting( (string)$label );
        uksort( $labels, function ( $a, $b ) use ( $folded, $labels ) {
            $order = strcmp( $folded[$a], $folded[$b] );
            if ( $order === 0 )
                $order = strcmp( (string)$labels[$a], (string)$labels[$b] );
            return $order !== 0 ? $order : strcmp( (string)$a, (string)$b );
        } );
        return array_map( 'strval', array_keys( $labels ) );
    }

    /**
     * A label with Latin accents removed and the case folded, the sort key of the fallback order: Č sorts with C,
     * É with E, and scripts other than Latin after it, in byte order.
     *
     * @param string $label
     * @return string
     */
    public static function foldForSorting( $label )
    {
        $text = $label;
        if ( class_exists( 'Normalizer' ) )
        {
            $decomposed = Normalizer::normalize( $text, Normalizer::FORM_D );
            if ( is_string( $decomposed ) )
                $text = preg_replace( '/\p{Mn}+/u', '', $decomposed );
        }
        else
        {
            $map = array( 'ä' => 'a', 'á' => 'a', 'à' => 'a', 'â' => 'a', 'ã' => 'a', 'å' => 'a', 'č' => 'c', 'ç' => 'c',
                          'é' => 'e', 'è' => 'e', 'ê' => 'e', 'ë' => 'e', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 'ñ' => 'n',
                          'ó' => 'o', 'ò' => 'o', 'ô' => 'o', 'ö' => 'o', 'õ' => 'o', 'ø' => 'o', 'š' => 's', 'ú' => 'u',
                          'ù' => 'u', 'û' => 'u', 'ü' => 'u', 'ý' => 'y', 'ž' => 'z', 'ß' => 'ss', 'ə' => 'e',
                          'ł' => 'l', 'ő' => 'o', 'ű' => 'u', 'ğ' => 'g', 'ş' => 's', 'ı' => 'i' );
            $text = strtr( function_exists( 'mb_strtolower' ) ? mb_strtolower( $text, 'UTF-8' ) : strtolower( $text ), $map );
        }
        $text = function_exists( 'mb_strtolower' ) ? mb_strtolower( (string)$text, 'UTF-8' ) : strtolower( (string)$text );
        return (string)$text;
    }
}
