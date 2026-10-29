<?php
/**
 * File containing the expHelpExamplesTranslation class.
 *
 * The translation of the generated reference examples (expHelpExamplesGenerator) into one language of the help, for
 * example German in doc/ger-de/ (doc/eng-us/examples/README.md, "How the generated pages are made"). It reads the
 * language's own files, which live next to its pages and its path map:
 *
 *   doc/<language>/.examples/strings.json          the generator's fixed text: "strings" (English => translated, with
 *                                                  {placeholders}), "keywords" (English keyword => translated) and
 *                                                  "files" (a fixed English file name => translated, triggers.md)
 *   doc/<language>/.examples/descriptions/*.json   the text the pages quote from the sources (INI comments, command
 *                                                  and cronjob descriptions, docblocks, datatype names), keyed by the
 *                                                  item: "ini:settings/site.ini [Block] Setting" => {"en": source
 *                                                  text, "de": translation}. All files of the directory are read as
 *                                                  one catalogue.
 *   doc/<language>/.dirnames.json                  English directory name => translated (examples => beispiele)
 *   doc/<language>/.paths.json                     translated path => English path (where the hand-written pages are)
 *
 * A description is used only while its "en" is the text the source has now: when a comment changes, the page shows
 * the new English text until the catalogue is updated (exp:help:examples --language=<code> --worklist=<file> lists
 * them). A description that has no entry of its own takes the translation of an entry with the same English text.
 * Code, INI keys and values, commands and identifiers are never translated: they are not in the tables.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expHelpExamplesTranslation
{
    /** The directory of the language's tables, below doc/<language>/ */
    const TABLE_DIR = '.examples';

    /** @var string the language directory (ger-de) */
    protected $directory;

    /** @var string the key of the translation in the description entries (de for ger-de) */
    protected $field;

    /** @var array English => translated */
    protected $strings = array();

    /** @var array English keyword => translated */
    protected $keywords = array();

    /** @var array English file name => translated */
    protected $files = array();

    /** @var array key => array( 'en' => ..., <field> => ... ) */
    protected $descriptions = array();

    /** @var array English text => translation, from every description entry */
    protected $byText = array();

    /** @var array English directory name => translated */
    protected $dirnames = array();

    /** @var array translated path => English path */
    protected $paths = array();

    /** @var array English path => translated path */
    protected $reverse = array();

    /** @var array English strings asked for that the table does not have => true */
    protected $missingStrings = array();

    /** @var array key => English text of descriptions without a current translation */
    protected $missingDescriptions = array();

    /**
     * @param string $directory the language directory (ger-de)
     * @param array $tables 'strings', 'keywords', 'files', 'descriptions', 'dirnames', 'paths'
     */
    public function __construct( $directory, array $tables = array() )
    {
        $this->directory = $directory;
        $this->field = self::fieldOf( $directory );
        foreach ( array( 'strings', 'keywords', 'files', 'descriptions', 'dirnames', 'paths' ) as $name )
            if ( isset( $tables[$name] ) && is_array( $tables[$name] ) )
                $this->$name = $tables[$name];
        foreach ( $this->paths as $translated => $original )
            if ( is_string( $translated ) && is_string( $original ) )
                $this->reverse[$original] = $translated;
        $texts = $this->descriptions;
        ksort( $texts, SORT_STRING );
        foreach ( $texts as $entry )
            if ( is_array( $entry ) && isset( $entry['en'], $entry[$this->field] ) && is_string( $entry[$this->field] )
                 && $entry[$this->field] !== '' && !isset( $this->byText[$entry['en']] ) )
                $this->byText[$entry['en']] = $entry[$this->field];
    }

    /**
     * Reads the tables of a language from its directory below doc/.
     *
     * @param string $root the installation root
     * @param string $directory the language directory (ger-de)
     * @return expHelpExamplesTranslation
     */
    public static function load( $root, $directory )
    {
        $base = rtrim( $root, '/' ) . '/doc/' . $directory;
        $strings = self::readJson( $base . '/' . self::TABLE_DIR . '/strings.json' );
        $descriptions = array();
        $dir = $base . '/' . self::TABLE_DIR . '/descriptions';
        if ( is_dir( $dir ) )
        {
            $list = glob( $dir . '/*.json' ) ?: array();
            sort( $list, SORT_STRING );
            foreach ( $list as $file )
                foreach ( self::readJson( $file ) as $key => $entry )
                    if ( is_string( $key ) && is_array( $entry ) && !isset( $descriptions[$key] ) )
                        $descriptions[$key] = $entry;
        }
        return new self( $directory, array(
            'strings'      => isset( $strings['strings'] ) ? $strings['strings'] : array(),
            'keywords'     => isset( $strings['keywords'] ) ? $strings['keywords'] : array(),
            'files'        => isset( $strings['files'] ) ? $strings['files'] : array(),
            'descriptions' => $descriptions,
            'dirnames'     => self::readJson( $base . '/.dirnames.json' ),
            'paths'        => self::readJson( $base . '/.paths.json' ),
        ) );
    }

    /**
     * @param string $file
     * @return array
     */
    protected static function readJson( $file )
    {
        if ( !is_file( $file ) || is_link( $file ) )
            return array();
        $data = json_decode( (string)file_get_contents( $file ), true );
        return is_array( $data ) ? $data : array();
    }

    /**
     * The key of a translation in the description entries: the language of the directory (ger-de => de).
     *
     * @param string $directory
     * @return string
     */
    public static function fieldOf( $directory )
    {
        $map = array( 'ger' => 'de', 'fre' => 'fr', 'nor' => 'no', 'spa' => 'es', 'ita' => 'it', 'por' => 'pt', 'dut' => 'nl', 'pol' => 'pl', 'rus' => 'ru' );
        $language = strtolower( (string)strtok( (string)$directory, '-' ) );
        return isset( $map[$language] ) ? $map[$language] : $language;
    }

    /** @return string */
    public function directory()
    {
        return $this->directory;
    }

    /** @return string */
    public function field()
    {
        return $this->field;
    }

    /**
     * A fixed text of the generator in this language; the English text when the table does not have it (noted).
     *
     * @param string $english
     * @return string
     */
    public function string( $english )
    {
        if ( isset( $this->strings[$english] ) && is_string( $this->strings[$english] ) && $this->strings[$english] !== '' )
            return $this->strings[$english];
        $this->missingStrings[$english] = true;
        return $english;
    }

    /**
     * A keyword in this language, or null when it has no translation (identifiers have none).
     *
     * @param string $english
     * @return string|null
     */
    public function keyword( $english )
    {
        return isset( $this->keywords[$english] ) && is_string( $this->keywords[$english] ) ? $this->keywords[$english] : null;
    }

    /**
     * The text a page quotes from a source, in this language when the catalogue has a translation of the source's
     * current text; else the English text (noted).
     *
     * @param string $key the item (ini:settings/site.ini [Block] Setting)
     * @param string $english the text of the source
     * @return string
     */
    public function description( $key, $english )
    {
        if ( $english === '' )
            return '';
        if ( isset( $this->descriptions[$key] ) && is_array( $this->descriptions[$key] ) )
        {
            $entry = $this->descriptions[$key];
            if ( isset( $entry['en'], $entry[$this->field] ) && $entry['en'] === $english && is_string( $entry[$this->field] ) && $entry[$this->field] !== '' )
                return $entry[$this->field];
        }
        if ( isset( $this->byText[$english] ) )
            return $this->byText[$english];
        $this->missingDescriptions[$key] = $english;
        return $english;
    }

    /**
     * A directory name in this language (.dirnames.json), or the English one.
     *
     * @param string $segment
     * @return string
     */
    public function dirname( $segment )
    {
        return isset( $this->dirnames[$segment] ) && is_string( $this->dirnames[$segment] ) && $this->dirnames[$segment] !== '' ? $this->dirnames[$segment] : $segment;
    }

    /**
     * A fixed file name of the generator in this language (triggers.md), or the English one.
     *
     * @param string $name
     * @return string
     */
    public function file( $name )
    {
        return isset( $this->files[$name] ) && is_string( $this->files[$name] ) && $this->files[$name] !== '' ? $this->files[$name] : $name;
    }

    /**
     * The translated path of an English page that the path map lists (a hand-written page), or null.
     *
     * @param string $englishPath below doc/eng-us/
     * @return string|null below doc/<language>/
     */
    public function translatedPath( $englishPath )
    {
        return isset( $this->reverse[$englishPath] ) ? $this->reverse[$englishPath] : null;
    }

    /** @return array translated path => English path */
    public function paths()
    {
        return $this->paths;
    }

    /** @return string[] the fixed texts asked for that the table does not have, sorted */
    public function missingStrings()
    {
        $list = array_keys( $this->missingStrings );
        sort( $list, SORT_STRING );
        return $list;
    }

    /** @return array key => English text of the descriptions without a current translation, sorted by key */
    public function missingDescriptions()
    {
        $list = $this->missingDescriptions;
        ksort( $list, SORT_STRING );
        return $list;
    }

    /** Forgets what was noted as missing (a new build) */
    public function resetMissing()
    {
        $this->missingStrings = array();
        $this->missingDescriptions = array();
    }

    /**
     * A catalogue file for the descriptions that have no current translation: key => array( 'en' => text,
     * <field> => '' ), sorted, in the format of the description files.
     *
     * @return string
     */
    public function worklist()
    {
        $out = array();
        foreach ( $this->missingDescriptions() as $key => $english )
            $out[$key] = array( 'en' => $english, $this->field => '' );
        return self::encode( $out );
    }

    /**
     * A table as the files store it: keys sorted, four spaces of indent, a final newline.
     *
     * @param array $data
     * @return string
     */
    public static function encode( array $data )
    {
        ksort( $data, SORT_STRING );
        if ( !$data )
            return "{}\n";
        return json_encode( $data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . "\n";
    }
}
