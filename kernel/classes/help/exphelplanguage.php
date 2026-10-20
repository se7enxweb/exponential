<?php
/**
 * File containing the expHelpLanguage class.
 *
 * The languages of the documentation (doc/en/us/features/6.0/help-system.md, "The pages in the
 * reader's language"; doc/en/us/bc/6.0/translated-documentation.md).
 * Each language has a directory below doc/: doc/en/us/ (the default, the original) and, for a translation, doc/de/,
 * doc/fr/ and so on. A page has one id in every language: its path below the default directory ("guides/caches.md").
 * A translation may keep the English file names (doc/de/guides/caches.md) or translate them; translated names are
 * listed in the language's path map, doc/<language>/.paths.json, a JSON object of "translated path" => "path below
 * doc/en/us/" ({"anleitungen/caches.md": "guides/caches.md"}). Files directly below doc/ that are in no language
 * directory (doc/changelogs/) are not translated: their id is their path below doc/, and every reader sees them.
 *
 * The reader's language is the locale the interface is shown in (site.ini [RegionalSettings] Locale, which the
 * administration's language switch sets), mapped to a directory by exphelp.ini [DocLanguages] Map[] (eng-US and
 * eng-GB -> en/us, ger-DE -> de; the locale's language alone, Map[ger], answers for every variant without an entry).
 * A language that is not in Directories[] is read in the default language.
 *
 *   $languages = expHelpLanguage::fromIni();
 *   $languages->forLocale( 'ger-DE' );                  // 'de'
 *   $languages->current();                              // the reader's directory
 *   $languages->split( 'doc/de/anleitungen/caches.md' ); // array( 'de', 'anleitungen/caches.md' )
 *   $languages->canonical( 'de', 'anleitungen/caches.md' ); // 'guides/caches.md'
 *   $languages->translated( 'de', 'guides/caches.md' );     // 'anleitungen/caches.md'
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expHelpLanguage
{
    /** The defaults of exphelp.ini [DocLanguages] */
    const DEFAULTS = array(
        'default'     => 'en/us',
        'directories' => array( 'en/us', 'de' ),
        'map'         => array( 'eng-US' => 'en/us', 'eng-GB' => 'en/us', 'eng' => 'en/us', 'ger-DE' => 'de', 'ger' => 'de' ),
        'names'       => array( 'en/us' => 'English', 'de' => 'German' ),
    );

    /** The documentation root the language directories are in, relative to the installation root */
    const DOC_ROOT = 'doc';

    /** The path map of a language, in its directory */
    const PATH_MAP_FILE = '.paths.json';

    /** The largest path map that is read, in bytes */
    const PATH_MAP_MAX_SIZE = 1048576;

    /** What a language directory may look like: one or two lower case segments (de, en/us, pt/br) */
    const DIRECTORY_PATTERN = '#^[a-z]{2,3}(/[a-z]{2,4})?$#';

    /** @var string */
    protected $default;

    /** @var string[] the directories, the default first */
    protected $directories;

    /** @var array locale => directory */
    protected $map;

    /** @var array directory => English name */
    protected $names;

    /** @var string the installation root, without a trailing slash ('' for the working directory) */
    protected $rootDir;

    /** @var array directory => translated path => canonical path, read on demand */
    protected $pathMaps = array();

    /** @var array directory => canonical path => translated path */
    protected $reverseMaps = array();

    /**
     * @param array $config DEFAULTS keys; missing ones take the default
     * @param string|null $rootDir the installation root (default expHelp::installRoot(), else the working directory)
     */
    public function __construct( array $config = array(), $rootDir = null )
    {
        $config = array_merge( self::DEFAULTS, array_intersect_key( $config, self::DEFAULTS ) );
        $default = self::cleanDirectory( $config['default'] );
        $this->default = $default !== null ? $default : self::DEFAULTS['default'];
        $directories = array( $this->default );
        foreach ( (array)$config['directories'] as $directory )
            if ( ( $directory = self::cleanDirectory( $directory ) ) !== null && !in_array( $directory, $directories, true ) )
                $directories[] = $directory;
        $this->directories = $directories;
        $this->map = array();
        foreach ( (array)$config['map'] as $locale => $directory )
            if ( is_string( $locale ) && ( $directory = self::cleanDirectory( $directory ) ) !== null )
                $this->map[$locale] = $directory;
        $this->names = array();
        foreach ( (array)$config['names'] as $directory => $name )
            if ( ( $directory = self::cleanDirectory( $directory ) ) !== null && is_string( $name ) && trim( $name ) !== '' )
                $this->names[$directory] = trim( $name );
        if ( $rootDir === null )
            $rootDir = class_exists( 'expHelp' ) && class_exists( 'eZSys' ) ? expHelp::installRoot() : getcwd();
        $this->rootDir = rtrim( str_replace( '\\', '/', (string)$rootDir ), '/' );
    }

    /**
     * The languages configured by exphelp.ini [DocLanguages].
     *
     * @param string|null $rootDir the installation root
     * @return expHelpLanguage
     */
    public static function fromIni( $rootDir = null )
    {
        return new self( self::iniConfig(), $rootDir );
    }

    /**
     * exphelp.ini [DocLanguages] as DEFAULTS keys; the defaults where it is missing.
     *
     * @return array
     */
    public static function iniConfig()
    {
        $config = array();
        if ( !class_exists( 'eZINI' ) || !eZINI::exists( 'exphelp.ini' ) )
            return $config;
        $ini = eZINI::instance( 'exphelp.ini' );
        if ( !$ini->hasGroup( 'DocLanguages' ) )
            return $config;
        if ( $ini->hasVariable( 'DocLanguages', 'Default' ) )
            $config['default'] = (string)$ini->variable( 'DocLanguages', 'Default' );
        foreach ( array( 'Directories' => 'directories', 'Map' => 'map', 'Names' => 'names' ) as $name => $key )
            if ( $ini->hasVariable( 'DocLanguages', $name ) && is_array( $ini->variable( 'DocLanguages', $name ) ) )
                $config[$key] = $ini->variable( 'DocLanguages', $name );
        return $config;
    }

    /**
     * A language directory in canonical form, or null when it is not one ("en/us", "de"; no slashes around it).
     *
     * @param mixed $directory
     * @return string|null
     */
    public static function cleanDirectory( $directory )
    {
        if ( !is_string( $directory ) )
            return null;
        $directory = strtolower( trim( $directory, " \t/" ) );
        return preg_match( self::DIRECTORY_PATTERN, $directory ) ? $directory : null;
    }

    /**
     * @return string the default language's directory (en/us)
     */
    public function defaultDirectory()
    {
        return $this->default;
    }

    /**
     * @return string[] every configured directory, the default first
     */
    public function directories()
    {
        return $this->directories;
    }

    /**
     * @param string $directory
     * @return bool whether it is a configured language directory
     */
    public function isDirectory( $directory )
    {
        return in_array( $directory, $this->directories, true );
    }

    /**
     * The English name of a language (translated by the caller), else its directory.
     *
     * @param string $directory
     * @return string
     */
    public function name( $directory )
    {
        return isset( $this->names[$directory] ) ? $this->names[$directory] : (string)$directory;
    }

    /**
     * @return array locale => directory, as configured
     */
    public function map()
    {
        return $this->map;
    }

    /**
     * The directory of a locale (eng-US, ger-DE, ger-AT, nor-NO@intl): Map[<locale>], else Map[<locale without
     * variant>], else Map[<language>] (the three letters before "-"); the default when none names a configured
     * directory.
     *
     * @param string $locale
     * @return string
     */
    public function forLocale( $locale )
    {
        $locale = trim( (string)$locale );
        $candidates = array( $locale );
        $plain = preg_replace( '/@.*$/', '', $locale );
        $candidates[] = $plain;
        $candidates[] = strtok( $plain, '-' );
        foreach ( $candidates as $key )
            if ( $key !== '' && $key !== false && isset( $this->map[$key] ) && $this->isDirectory( $this->map[$key] ) )
                return $this->map[$key];
        return $this->default;
    }

    /**
     * The locale the reader sees the interface in: site.ini [RegionalSettings] Locale of the request (the language
     * switch of the administration sets it for the signed-in user). '' when there are no settings (a test, a tool).
     *
     * @return string
     */
    public static function currentLocale()
    {
        if ( !class_exists( 'eZINI' ) )
            return '';
        $ini = eZINI::instance();
        return $ini->hasVariable( 'RegionalSettings', 'Locale' ) ? (string)$ini->variable( 'RegionalSettings', 'Locale' ) : '';
    }

    /**
     * The reader's language directory.
     *
     * @return string
     */
    public function current()
    {
        return $this->forLocale( self::currentLocale() );
    }

    /**
     * The language directory a path (relative to the installation root) is in, and the path below it; null when the
     * path is not in a language directory (doc/changelogs/..., or outside doc/). The longest directory wins, so en/us
     * is found before a language "en".
     *
     * @param string $path
     * @return array|null array( directory, rest )
     */
    public function split( $path )
    {
        $path = (string)$path;
        $prefix = self::DOC_ROOT . '/';
        if ( strncmp( $path, $prefix, strlen( $prefix ) ) !== 0 )
            return null;
        $rest = substr( $path, strlen( $prefix ) );
        $best = null;
        foreach ( $this->directories as $directory )
            if ( strncmp( $rest, $directory . '/', strlen( $directory ) + 1 ) === 0
                 && ( $best === null || strlen( $directory ) > strlen( $best ) ) )
                $best = $directory;
        return $best === null ? null : array( $best, substr( $rest, strlen( $best ) + 1 ) );
    }

    /**
     * Whether a path is in a language directory that is not configured: below doc/ a directory named by a two
     * letter language code (fr, or en/gb next to en/us) that split() does not know. Such a translation is not read
     * until Directories[] lists it.
     *
     * @param string $path relative to the installation root
     * @return bool
     */
    public function isUnconfiguredLanguagePath( $path )
    {
        if ( $this->split( $path ) !== null )
            return false;
        return (bool)preg_match( '#^' . preg_quote( self::DOC_ROOT, '#' ) . '/[a-z]{2}/#', (string)$path );
    }

    /**
     * The page id of a path below a language directory: the path map's entry for a translated name, else the path.
     *
     * @param string $directory
     * @param string $rest the path below the directory
     * @return string
     */
    public function canonical( $directory, $rest )
    {
        if ( $directory === $this->default )
            return $rest;
        $map = $this->pathMap( $directory );
        return isset( $map[$rest] ) ? $map[$rest] : $rest;
    }

    /**
     * The path below a language directory of a page id: the reverse of canonical().
     *
     * @param string $directory
     * @param string $id
     * @return string
     */
    public function translated( $directory, $id )
    {
        if ( $directory === $this->default )
            return $id;
        $this->pathMap( $directory );
        return isset( $this->reverseMaps[$directory][$id] ) ? $this->reverseMaps[$directory][$id] : $id;
    }

    /**
     * The page id of a value that may name a page by its file: "doc/en/us/guides/x.md", "en/us/guides/x.md",
     * "doc/de/anleitungen/x.md", "doc/changelogs/..." or already an id. Values that name no language directory come
     * back without a leading "doc/".
     *
     * @param string $value
     * @return string
     */
    public function idOfValue( $value )
    {
        $value = ltrim( (string)$value, '/' );
        $path = strncmp( $value, self::DOC_ROOT . '/', strlen( self::DOC_ROOT ) + 1 ) === 0 ? $value : self::DOC_ROOT . '/' . $value;
        $split = $this->split( $path );
        if ( $split !== null )
            return $this->canonical( $split[0], $split[1] );
        return substr( $path, strlen( self::DOC_ROOT ) + 1 );
    }

    /**
     * The path map of a language: translated path => path below the default directory. Read from
     * doc/<directory>/.paths.json (a regular file, not a symbolic link, at most PATH_MAP_MAX_SIZE bytes); an entry
     * whose paths are not clean relative paths is left out. Empty for the default language and when there is none.
     *
     * @param string $directory
     * @return array
     */
    public function pathMap( $directory )
    {
        if ( isset( $this->pathMaps[$directory] ) )
            return $this->pathMaps[$directory];
        $map = array();
        $file = $this->pathMapFile( $directory );
        if ( $directory !== $this->default && $file !== null && is_file( $file ) && !is_link( $file )
             && (int)@filesize( $file ) <= self::PATH_MAP_MAX_SIZE )
        {
            $data = json_decode( (string)@file_get_contents( $file ), true );
            if ( is_array( $data ) )
                foreach ( $data as $translated => $canonical )
                {
                    if ( !is_string( $translated ) || !is_string( $canonical ) )
                        continue;
                    $translated = class_exists( 'expHelpAllowlist' ) ? expHelpAllowlist::normalize( $translated ) : trim( $translated, '/' );
                    $canonical = class_exists( 'expHelpAllowlist' ) ? expHelpAllowlist::normalize( $canonical ) : trim( $canonical, '/' );
                    if ( $translated !== null && $canonical !== null && $translated !== '' && $canonical !== '' )
                        $map[$translated] = $canonical;
                }
        }
        $this->pathMaps[$directory] = $map;
        $this->reverseMaps[$directory] = array_flip( $map );
        return $map;
    }

    /**
     * Sets the path map of a language (tests, tools); null forgets it, so it is read from its file again.
     *
     * @param string $directory
     * @param array|null $map translated path => path below the default directory
     */
    public function setPathMap( $directory, ?array $map = null )
    {
        if ( $map === null )
        {
            unset( $this->pathMaps[$directory], $this->reverseMaps[$directory] );
            return;
        }
        $this->pathMaps[$directory] = $map;
        $this->reverseMaps[$directory] = array_flip( $map );
    }

    /** The directory name map of a translation, next to its path map: English directory name => translated one */
    const DIRNAMES_FILE = '.dirnames.json';

    /**
     * The union of several path maps (or directory name maps), for translators who add entries to the same
     * .paths.json in parallel: every entry of every map, keys sorted. Two maps that disagree never lose an entry
     * silently: a key with two different values keeps the first map's value and is reported as a conflict, and
     * so is a value that two keys give (a page translated under two names: its id would name two files). A key or
     * value that is not a clean relative path (or not a string) is left out and reported as invalid.
     *
     * @param array[] $maps decoded JSON objects, the first one wins a conflict (the side being merged into)
     * @return array array( 'map' => key => value, sorted by key; 'conflicts' => list of messages; 'invalid' => list of messages )
     */
    public static function mergePathMaps( array $maps )
    {
        $merged = array();
        $owner = array();
        $conflicts = array();
        $invalid = array();
        foreach ( array_values( $maps ) as $n => $map )
        {
            if ( !is_array( $map ) )
            {
                $invalid[] = sprintf( 'map %d is not a JSON object', $n + 1 );
                continue;
            }
            foreach ( $map as $key => $value )
            {
                $cleanKey = is_string( $key ) ? self::cleanPath( $key ) : null;
                $cleanValue = is_string( $value ) ? self::cleanPath( $value ) : null;
                if ( $cleanKey === null || $cleanValue === null )
                {
                    $invalid[] = sprintf( 'map %d: "%s" => %s is not a pair of relative paths', $n + 1, (string)$key, json_encode( $value ) );
                    continue;
                }
                if ( isset( $merged[$cleanKey] ) )
                {
                    if ( $merged[$cleanKey] !== $cleanValue )
                        $conflicts[] = sprintf( '"%s" is "%s" in one map and "%s" in another; kept "%s"', $cleanKey, $merged[$cleanKey], $cleanValue, $merged[$cleanKey] );
                    continue;
                }
                if ( isset( $owner[$cleanValue] ) )
                {
                    $conflicts[] = sprintf( '"%s" is named both "%s" and "%s"; kept "%s"', $cleanValue, $owner[$cleanValue], $cleanKey, $owner[$cleanValue] );
                    continue;
                }
                $merged[$cleanKey] = $cleanValue;
                $owner[$cleanValue] = $cleanKey;
            }
        }
        ksort( $merged, SORT_STRING );
        return array( 'map' => $merged, 'conflicts' => $conflicts, 'invalid' => $invalid );
    }

    /**
     * A path map as the file stores it: a JSON object, keys sorted, four spaces of indent, one entry per line and a
     * final newline, so that two translators' additions merge line by line and a merge of the same entries is
     * byte for byte the same file.
     *
     * @param array $map
     * @return string
     */
    public static function encodePathMap( array $map )
    {
        ksort( $map, SORT_STRING );
        if ( !$map )
            return "{}\n";
        return json_encode( $map, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . "\n";
    }

    /**
     * The entries of a path map whose directories are not named as the directory name map says: each directory of
     * the original path (help, project) has to be the translated name the map gives it (hilfe, projekt), or its own
     * name when the map has none. So translators who work in parallel put the pages of one directory in one place.
     *
     * @param array $pathMap translated path => original path
     * @param array $dirnames original directory name => translated one
     * @return string[] one message per entry that does not follow the map
     */
    public static function pathsAgainstDirnames( array $pathMap, array $dirnames )
    {
        $problems = array();
        foreach ( $pathMap as $translated => $original )
        {
            $from = explode( '/', (string)$original );
            $to = explode( '/', (string)$translated );
            array_pop( $from );
            array_pop( $to );
            $expected = array();
            foreach ( $from as $segment )
                $expected[] = isset( $dirnames[$segment] ) && is_string( $dirnames[$segment] ) ? $dirnames[$segment] : $segment;
            if ( $expected !== $to )
                $problems[] = sprintf( '"%s" (for "%s") should be below "%s/"', $translated, $original, implode( '/', $expected ) );
        }
        return $problems;
    }

    /**
     * The versions a file holds: the file itself, or, when git left conflict markers in it, the two sides of the
     * conflict ("ours" first, then "theirs"; the lines outside the conflicts belong to both, a diff3 base section to
     * neither). So a .paths.json that two translators changed can be merged from the conflicted file alone.
     *
     * @param string $text
     * @return string[] one text, or two
     */
    public static function conflictSides( $text )
    {
        $text = (string)$text;
        if ( !preg_match( '/^<{7}( |$)/m', $text ) )
            return array( $text );
        $ours = $theirs = '';
        $state = 'both';
        foreach ( preg_split( '/(?<=\n)/', $text ) as $line )
        {
            if ( preg_match( '/^<{7}( |\r?$)/', $line ) )
                $state = 'ours';
            elseif ( $state === 'ours' && preg_match( '/^\|{7}( |\r?$)/', $line ) )
                $state = 'base';
            elseif ( ( $state === 'ours' || $state === 'base' ) && preg_match( '/^={7}\r?$/', $line ) )
                $state = 'theirs';
            elseif ( $state === 'theirs' && preg_match( '/^>{7}( |\r?$)/', $line ) )
                $state = 'both';
            elseif ( $state === 'both' )
            {
                $ours .= $line;
                $theirs .= $line;
            }
            elseif ( $state === 'ours' )
                $ours .= $line;
            elseif ( $state === 'theirs' )
                $theirs .= $line;
        }
        return array( $ours, $theirs );
    }

    /**
     * A map file's text as maps: each side of a conflict decoded (conflictSides()); a side that is not a JSON object
     * is null. A JSON object whose last entry lost or gained a comma in the merge is read all the same.
     *
     * @param string $text
     * @return array list of array|null
     */
    public static function decodeMapSides( $text )
    {
        $maps = array();
        foreach ( self::conflictSides( $text ) as $side )
        {
            $data = json_decode( $side, true );
            if ( !is_array( $data ) )
            {
                // the commas of the lines next to a conflict: one entry per line, so add the missing and drop the extra
                $repaired = preg_replace( '/("\s*)\n(\s*")/', "\$1,\n\$2", $side );
                $repaired = preg_replace( '/,(\s*)([}\]])/', '$1$2', preg_replace( '/,\s*,/', ',', (string)$repaired ) );
                $data = json_decode( (string)$repaired, true );
            }
            $maps[] = is_array( $data ) ? $data : null;
        }
        return $maps;
    }

    /**
     * A clean relative path ("a/b.md": no leading or trailing slash, no "." or ".." segment, no empty segment, no
     * backslash or control character), or null.
     *
     * @param string $path
     * @return string|null
     */
    protected static function cleanPath( $path )
    {
        if ( class_exists( 'expHelpAllowlist' ) )
            $path = expHelpAllowlist::normalize( $path );
        else
            $path = trim( (string)$path, '/' );
        if ( $path === null || $path === '' || preg_match( '/[\x00-\x1F\x7F\\\\]/', $path ) )
            return null;
        foreach ( explode( '/', $path ) as $segment )
            if ( $segment === '' || $segment === '.' || $segment === '..' )
                return null;
        return $path;
    }

    /**
     * The absolute file name of a language's path map, or null for a directory that is not configured.
     *
     * @param string $directory
     * @return string|null
     */
    public function pathMapFile( $directory )
    {
        if ( !$this->isDirectory( $directory ) )
            return null;
        return ( $this->rootDir === '' ? '' : $this->rootDir . '/' ) . self::DOC_ROOT . '/' . $directory . '/' . self::PATH_MAP_FILE;
    }

    /**
     * Changes when the configuration or a path map changes (part of the help index's signature).
     *
     * @return string
     */
    public function stamp()
    {
        $parts = array( $this->default, implode( ',', $this->directories ), json_encode( $this->map ) );
        foreach ( $this->directories as $directory )
        {
            $file = $this->pathMapFile( $directory );
            if ( $file !== null && is_file( $file ) )
                $parts[] = $directory . ':' . (int)@filesize( $file ) . ':' . (int)@filemtime( $file );
        }
        return sha1( implode( "\n", $parts ) );
    }

    /**
     * The HTML language code of a directory: en/us -> en-US, de -> de, pt/br -> pt-BR.
     *
     * @param string $directory
     * @return string
     */
    public static function htmlLang( $directory )
    {
        $parts = explode( '/', (string)$directory );
        if ( !preg_match( '/^[a-z]{2,3}$/', $parts[0] ) )
            return 'en';
        return isset( $parts[1] ) && preg_match( '/^[a-z]{2,4}$/', $parts[1] ) ? $parts[0] . '-' . strtoupper( $parts[1] ) : $parts[0];
    }

    /** The languages written right to left (the first part of a directory) */
    const RTL_LANGUAGES = array( 'ar', 'fa', 'he', 'ur', 'yi', 'ps', 'sd', 'ug', 'dv', 'ckb' );

    /**
     * The writing direction of a directory's language: rtl or ltr.
     *
     * @param string $directory
     * @return string
     */
    public static function htmlDir( $directory )
    {
        return in_array( strtok( (string)$directory, '/' ), self::RTL_LANGUAGES, true ) ? 'rtl' : 'ltr';
    }

    /**
     * A directory as a file name part: en/us -> en-us.
     *
     * @param string $directory
     * @return string
     */
    public static function key( $directory )
    {
        return str_replace( '/', '-', (string)$directory );
    }
}

?>
