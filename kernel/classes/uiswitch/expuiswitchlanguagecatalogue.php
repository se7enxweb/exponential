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
 * share/translations/untranslated/translation.ts).
 *
 * Reading 25 translation files of 1 to 3 MB takes seconds, so the result is cached: in memory for the process and in
 * <cache directory>/uiswitch/language-catalogue.json, both keyed by a signature of the names, sizes and modification
 * times of the files read. A changed or added file builds the catalogue again on its next use; nothing needs clearing.
 * Guide: doc/features/6.0/ui-switch-languages.md
 */
class expUISwitchLanguageCatalogue
{
    const LOCALE_PATTERN = '/^[a-z]{3}-[A-Z]{2}(@[a-z0-9]{1,16})?$/';

    /** the format of the cache file; a new format number builds the catalogue again */
    const FORMAT = 3;

    /** @var array signature => catalogue, for the life of the process (Velocity workers) */
    private static $memory = array();

    /** @var array signature => reader language => code => name */
    private static $readerMemory = array();

    /** @var string the installation root the share/ paths are relative to; '' is the working directory */
    protected $root;

    /** @var string|null where the cache file goes; null: the kernel's cache directory, '' : no cache file */
    protected $cacheDirectory;

    /** @var string|null the signature, computed once per instance (one request: files changed later are seen at the next) */
    protected $signature = null;

    /**
     * @param string $root
     * @param string|null $cacheDirectory
     */
    public function __construct( $root = '', $cacheDirectory = null )
    {
        $this->root = (string)$root;
        $this->cacheDirectory = $cacheDirectory;
    }

    /**
     * Forgets the in-memory catalogues (tests).
     */
    public static function resetMemory()
    {
        self::$memory = array();
        self::$readerMemory = array();
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
        if ( $this->signature !== null )
            return $this->signature;
        $parts = array( self::FORMAT );
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
     * The catalogue: code => hash( native, english, http, coverage ), coverage an integer percentage or null when
     * the locale has no translation (English, the language of the source texts, is always 100).
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
        $sourceKeys = $this->messageKeys( $this->path( 'share/translations/untranslated/translation.ts' ), false );
        $entries = array();
        foreach ( $this->localeCodes( true ) as $code )
        {
            $names = $this->readLocaleNames( $this->path( 'share/locale/' . $code . '.ini' ) );
            $coverage = null;
            if ( strpos( $code, 'eng-' ) === 0 )
                $coverage = 100;
            else
            {
                $ts = $this->path( 'share/translations/' . $code . '/translation.ts' );
                if ( is_file( $ts ) )
                    $coverage = self::coveragePercent( $sourceKeys, $this->messageKeys( $ts, true ) );
            }
            $entries[$code] = array( 'native' => $names['LanguageName'], 'english' => $names['InternationalLanguageName'],
                                     'http' => $names['ContentLanguage'], 'coverage' => $coverage,
                                     'intl_native' => strpos( $code, '@' ) === false ? self::intlDisplayName( $names['ContentLanguage'], $names['ContentLanguage'] ) : '',
                                     'test' => strtolower( $names['TestLanguage'] ) === 'true' );
        }
        return $entries;
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
     * The messages of a Qt Linguist file, as context, source and comment joined, the key the translator uses. With
     * $translatedOnly only those with a translation that is neither empty, unfinished nor obsolete.
     *
     * @param string $file
     * @param bool $translatedOnly
     * @return array key => true
     */
    public function messageKeys( $file, $translatedOnly )
    {
        $keys = array();
        if ( !is_file( $file ) || !class_exists( 'XMLReader' ) )
            return $keys;
        $reader = new XMLReader();
        if ( !@$reader->open( $file, null, LIBXML_NONET ) )
            return $keys;
        $context = '';
        $inName = false;
        try
        {
            while ( @$reader->read() )
            {
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
                    if ( $translatedOnly && ( trim( $translation ) === '' || $type === 'unfinished' || $type === 'obsolete' ) )
                        continue;
                    if ( !$translatedOnly && $type === 'obsolete' )
                        continue;
                    $keys[$context . "\x1F" . $source . "\x1F" . $comment] = true;
                    $reader->next();
                }
            }
        }
        catch ( Throwable $e )
        {
            // a broken file counts what was read before the error
        }
        $reader->close();
        return $keys;
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
        if ( !is_string( $data ) )
            return;
        try
        {
            // eZFile and eZDir use the installation's file and directory modes; without them (no kernel) the
            // catalogue is kept in memory only
            if ( class_exists( 'eZFile' ) && class_exists( 'eZDir' ) )
                @eZFile::create( basename( $file ), dirname( $file ), $data, true );
        }
        catch ( Throwable $e )
        {
            // kept in memory only
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
